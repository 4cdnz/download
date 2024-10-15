<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'setup.php';
require_once 'functions_base.php';
require_once 'functions_admin.php';
require_once 'functions_servers.php';
require_once 'functions.php';
require_once 'database_tables.php';

if ($_SERVER['DOCUMENT_ROOT'] != '')
{
	// under web
	start_session();
	if ($_SESSION['userdata']['user_id'] < 1)
	{
		http_response_code(403);
		die('Access denied');
	}
	header('Content-Type: text/plain; charset=utf-8');
}

KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
if (!KvsUtilities::try_exclusive_lock('admin/data/system/cron_check_db'))
{
	die('Already locked');
}

$start_time = time();

ini_set('display_errors', 1);

$options = get_options();

$memory_limit = $options['LIMIT_MEMORY'];
if ($memory_limit == 0)
{
	$memory_limit = 512;
}
ini_set('memory_limit', "{$memory_limit}M");

log_output('INFO  System check started');
log_output('INFO  Memory limit: ' . ini_get('memory_limit'));
log_output('');

$config['sql_safe_mode'] = 1;
foreach ($database_tables as $table)
{
	check_table_status($table);
}
unset($config['sql_safe_mode']);

@unlink("$config[project_path]/admin/data/engine/checks/mysql_corrupted.dat");
log_output('MySQL check done');

$has_versions = mr2number(sql_pr("select count(*) from $config[tables_prefix_multi]file_history")) > 0;

$path = "$config[project_path]/.htaccess";
if (is_file($path))
{
	KvsDataTypeFileHistory::check_version($path, $has_versions);
}

$pages = get_site_pages();
$pages[] = ['external_id' => '$global'];
foreach ($pages as $page)
{
	if ($page['external_id'] != '$global')
	{
		if (is_file("$config[project_path]/$page[external_id].php"))
		{
			KvsDataTypeFileHistory::check_version("$config[project_path]/$page[external_id].php", $has_versions);
		}
	}
	try
	{
		if (is_file("$config[project_path]/template/$page[external_id].tpl"))
		{
			KvsDataTypeFileHistory::check_version("$config[project_path]/template/$page[external_id].tpl", $has_versions);
		}
		$template_files = KvsFilesystem::scan_dir("$config[project_path]/template/blocks/$page[external_id]", KvsFilesystem::SCAN_DIR_FILES);
		foreach ($template_files as $template_file)
		{
			if (KvsUtilities::str_ends_with($template_file, '.tpl'))
			{
				KvsDataTypeFileHistory::check_version($template_file, $has_versions);
			}
		}
		$config_files = KvsFilesystem::scan_dir("$config[project_path]/admin/data/config/$page[external_id]", KvsFilesystem::SCAN_DIR_FILES);
		foreach ($config_files as $config_file)
		{
			if (KvsUtilities::str_ends_with($config_file, '.dat') && !KvsUtilities::str_ends_with($config_file, '/name.dat') && !KvsUtilities::str_ends_with($config_file, '/config.dat'))
			{
				KvsDataTypeFileHistory::check_version($config_file, $has_versions);
			}
		}
	} catch (KvsException $ignored)
	{
	}
}

try
{
	$templates = KvsFilesystem::scan_dir("$config[project_path]/template", KvsFilesystem::SCAN_DIR_FILES, true);
	foreach ($templates as $template)
	{
		if (KvsUtilities::str_ends_with($template, '.tpl'))
		{
			KvsDataTypeFileHistory::check_version($template, $has_versions);
		}
	}
} catch (KvsException $ignored)
{
}

$ad_spots = get_site_spots();
foreach ($ad_spots as $ad_spot)
{
	KvsDataTypeFileHistory::check_version("$config[project_path]/admin/data/advertisements/spot_$ad_spot[external_id].dat#template", $has_versions);
	foreach ($ad_spot['ads'] as $ad)
	{
		KvsDataTypeFileHistory::check_version("$config[project_path]/admin/data/advertisements/spot_$ad_spot[external_id].dat#ads:$ad[advertisement_id]:code", $has_versions);
	}
}

if (is_dir("$config[project_path]/js"))
{
	try
	{
		$static_files = KvsFilesystem::scan_dir("$config[project_path]/js", KvsFilesystem::SCAN_DIR_FILES, true);
		foreach ($static_files as $static_file)
		{
			if (KvsUtilities::str_ends_with($static_file, '.js'))
			{
				KvsDataTypeFileHistory::check_version($static_file, $has_versions);
			}
		}
	} catch (KvsException $ignored)
	{
	}
}

if (is_dir("$config[project_path]/styles"))
{
	try
	{
		$static_files = KvsFilesystem::scan_dir("$config[project_path]/styles", KvsFilesystem::SCAN_DIR_FILES, true);
		foreach ($static_files as $static_file)
		{
			if (KvsUtilities::str_ends_with($static_file, '.css'))
			{
				KvsDataTypeFileHistory::check_version($static_file, $has_versions);
			}
		}
	} catch (KvsException $ignored)
	{
	}
}

if (is_dir("$config[project_path]/css"))
{
	try
	{
		$static_files = KvsFilesystem::scan_dir("$config[project_path]/css", KvsFilesystem::SCAN_DIR_FILES, true);
		foreach ($static_files as $static_file)
		{
			if (KvsUtilities::str_ends_with($static_file, '.css'))
			{
				KvsDataTypeFileHistory::check_version($static_file, $has_versions);
			}
		}
	} catch (KvsException $ignored)
	{
	}
}

if (is_dir("$config[project_path]/static"))
{
	try
	{
		$static_files = KvsFilesystem::scan_dir("$config[project_path]/static", KvsFilesystem::SCAN_DIR_FILES, true);
		foreach ($static_files as $static_file)
		{
			if (KvsUtilities::str_ends_with($static_file, '.css') || KvsUtilities::str_ends_with($static_file, '.js'))
			{
				KvsDataTypeFileHistory::check_version($static_file, $has_versions);
			}
		}
	} catch (KvsException $ignored)
	{
	}
}

$other_paths = [
		"$config[project_path]/admin/include/pre_initialize_page_code.php",
		"$config[project_path]/admin/include/pre_display_page_code.php",
		"$config[project_path]/admin/include/pre_process_page_code.php",
		"$config[project_path]/admin/include/pre_async_action_code.php",
		"$config[project_path]/admin/include/post_process_page_code.php",
		"$config[project_path]/admin/data/.htaccess",
		"$config[project_path]/admin/logs/.htaccess",
		"$config[project_path]/admin/plugins/.htaccess",
		"$config[project_path]/admin/smarty/.htaccess",
		"$config[project_path]/admin/stamp/.htaccess",
		"$config[project_path]/admin/template/.htaccess",
		"$config[project_path]/admin/tools/.htaccess",
		"$config[project_path]/blocks/.htaccess",
		"$config[project_path]/langs/default.php",
		"$config[project_path]/langs/.htaccess",
		"$config[project_path]/template/.htaccess",
		"$config[project_path]/tmp/.htaccess",
];
foreach ($other_paths as $other_path)
{
	if (is_file($other_path))
	{
		KvsDataTypeFileHistory::check_version($other_path, $has_versions);
	}
}

$history_files = mr2array_list(sql_pr("select distinct path from $config[tables_prefix_multi]file_history"));
foreach ($history_files as $history_file)
{
	if (strpos($history_file, '#') === false && !is_file("$config[project_path]$history_file"))
	{
		KvsDataTypeFileHistory::increment_version("$config[project_path]$history_file");
	}
}

log_output('Theme files check done');

$vast_key_data = @unserialize(file_get_contents("$config[project_path]/admin/data/player/vast/key.dat"), ['allowed_classes' => false]) ?: [];
$new_vast_key_data = @json_decode(get_page('', "https://www.kernel-scripts.com/get_vast.php?domain=$config[project_licence_domain]&license_code=$config[player_license_code]", '', '', 1, 0, 20, ''), true);
if (is_array($new_vast_key_data) && $new_vast_key_data['domain'] == $config['project_licence_domain'])
{
	if (!$vast_key_data['primary_vast_key'] || $vast_key_data['domain'] != $new_vast_key_data['domain'])
	{
		mkdir_recursive("$config[project_path]/admin/data/player/vast");
		file_put_contents("$config[project_path]/admin/data/player/vast/key.dat", serialize($new_vast_key_data), LOCK_EX);
		file_put_contents("$config[project_path]/admin/data/player/version.dat", md5(serialize($new_vast_key_data)), LOCK_EX);
		file_put_contents("$config[project_path]/admin/data/player/embed/version.dat", md5(serialize($new_vast_key_data)), LOCK_EX);
		$vast_key_data = $new_vast_key_data;
		log_output("Player VAST key updated to: $new_vast_key_data[primary_vast_key]");
	} else
	{
		$vast_key_valid = intval(substr($vast_key_data['primary_vast_key'], 0, 10));
		$new_vast_key_valid = intval(substr($new_vast_key_data['primary_vast_key'], 0, 10));
		if ($new_vast_key_valid > $vast_key_valid || $new_vast_key_data['aliases_hash'] != $vast_key_data['aliases_hash'])
		{
			mkdir_recursive("$config[project_path]/admin/data/player/vast");
			file_put_contents("$config[project_path]/admin/data/player/vast/key.dat", serialize($new_vast_key_data), LOCK_EX);
			file_put_contents("$config[project_path]/admin/data/player/version.dat", md5(serialize($new_vast_key_data)), LOCK_EX);
			file_put_contents("$config[project_path]/admin/data/player/embed/version.dat", md5(serialize($new_vast_key_data)), LOCK_EX);
			$vast_key_data = $new_vast_key_data;
			log_output("Player VAST key updated to: $new_vast_key_data[primary_vast_key]");
		}
	}
}

$error_player_logging_count = 0;
$error_embed_logging_count = 0;
$has_player_vast_enabled = false;
$has_embed_vast_enabled = false;
$player_files = get_player_data_files();
foreach ($player_files as $player_file)
{
	$player_data = @unserialize(file_get_contents($player_file['file']), ['allowed_classes' => false]);
	if (isset($player_data))
	{
		if ($player_data['error_logging'] == 1)
		{
			if ($player_file['is_embed'] == 1)
			{
				$error_embed_logging_count++;
			} else
			{
				$error_player_logging_count++;
			}
		}
		if ($player_data['enable_pre_roll_vast'] == 1 || $player_data['enable_post_roll_vast'] == 1)
		{
			if ($player_file['is_embed'] == 1)
			{
				$has_embed_vast_enabled = true;
			} else
			{
				$has_player_vast_enabled = true;
			}
		}
	}
}
add_admin_notification('settings.player.debug', $error_player_logging_count);
add_admin_notification('settings.embed.debug', $error_embed_logging_count);

$is_vast_key_expiring = false;
$is_vast_key_expired = false;
if ($vast_key_data['primary_vast_key'])
{
	$vast_key_valid = intval(substr($vast_key_data['primary_vast_key'], 0, 10));
	if ($vast_key_valid > 0)
	{
		$vast_key_valid = intval(($vast_key_valid - time()) / 86400);
		if ($vast_key_valid > 0)
		{
			if ($vast_key_valid <= 3)
			{
				$is_vast_key_expiring = true;
			}
		} else
		{
			$is_vast_key_expired = true;
		}
	} else
	{
		$is_vast_key_expired = true;
	}
	add_admin_notification('settings.player.vast_expiring', $has_player_vast_enabled && $is_vast_key_expiring ? 1 : 0);
	add_admin_notification('settings.embed.vast_expiring', $has_embed_vast_enabled && $is_vast_key_expiring ? 1 : 0);
	add_admin_notification('settings.player.vast_expired', $has_player_vast_enabled && $is_vast_key_expired ? 1 : 0);
	add_admin_notification('settings.embed.vast_expired', $has_embed_vast_enabled && $is_vast_key_expired ? 1 : 0);
}

$admin_notification_objects = 0;
foreach ($ad_spots as $ad_spot)
{
	if ($ad_spot['is_debug_enabled'] == 1)
	{
		$admin_notification_objects++;
	}
}
add_admin_notification('theme.advertising.debug', $admin_notification_objects);

$profiles = get_vast_profiles();

$admin_notification_objects = 0;
foreach ($profiles as $profile)
{
	if ($profile['is_debug_enabled'] == 1)
	{
		$admin_notification_objects++;
	}
}
add_admin_notification('settings.vast_profiles.debug', $admin_notification_objects);

$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
add_admin_notification('settings.website.disabled', intval($website_ui_data['DISABLE_WEBSITE']));
add_admin_notification('settings.website.caching_disabled', intval($website_ui_data['WEBSITE_CACHING']) == 2 ? 1 : 0);

$stats_params = unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
add_admin_notification('settings.stats.performance_debug', intval($stats_params['collect_performance_stats']));

sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_check_db'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));

log_output('');
log_output('INFO  Finished');

function check_table_status($table)
{
	global $config;

	if (is_file("$config[project_path]/admin/data/engine/checks/mysql_corrupted.dat"))
	{
		$result = mr2array(sql_pr("check table $table medium"));
		foreach ($result as $row)
		{
			if (strtolower($row['Msg_type']) == 'error' || strtolower($row['Msg_type']) == 'warning')
			{
				log_output("Repairing table $table");
				sql_pr("repair table $table");
				return;
			}
		}
	} else
	{
		$result = mr2string(sql_pr("select count(*) from $table"));
		if ($result === '')
		{
			log_output("Repairing table $table");
			sql_pr("repair table $table");
		}
	}
}

function log_output($message)
{
	if (!$message)
	{
		echo "\n";
	} else
	{
		echo date('[Y-m-d H:i:s] ') . $message . "\n";
	}
}
