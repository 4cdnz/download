<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$engine_customization_files = ['admin/include/setup.php', 'admin/include/setup_smarty.php', 'admin/include/setup_smarty_site.php', 'admin/include/pre_initialize_page_code.php', 'admin/include/pre_process_page_code.php', 'admin/include/pre_display_page_code.php', 'admin/include/pre_async_action_code.php', 'admin/include/post_process_page_code.php', 'admin/include/cron_custom.php', 'admin/.htaccess', '.htaccess', '.htaccess_mobile'];

$log_files = [];
$temp = get_contents_from_dir("$config[project_path]/admin/logs", 1);
foreach ($temp as $log_file)
{
	if (substr($log_file, -4) == '.txt')
	{
		$log_files[] = $log_file;
	}
}

$plugins_list = get_contents_from_dir("$config[project_path]/admin/plugins", 2);
foreach ($plugins_list as $plugin_id)
{
	if (is_file("$config[project_path]/admin/logs/plugins/$plugin_id.txt"))
	{
		$log_files[] = "plugins/$plugin_id.txt";
	}
}

if ($_REQUEST['action'] == 'get_info')
{
	/** @noinspection ForgottenDebugOutputInspection */
	phpinfo();
	die;
}

if ($_REQUEST['action'] == 'get_log')
{
	if (in_array($_REQUEST['log_file'], $log_files))
	{
		download_log_file("$config[project_path]/admin/logs/$_REQUEST[log_file]");
	}
	die;
}
if ($_REQUEST['action'] == 'get_customization_file')
{
	$customization_file = str_replace('htaccess', '.htaccess', $_REQUEST['customization_file']);
	if (in_array($customization_file, $engine_customization_files))
	{
		if (is_file("$config[project_path]/$customization_file"))
		{
			header('Content-Type: text/plain; charset=utf-8');
			if (isset($_REQUEST['download']))
			{
				header("Content-Disposition: attachment; filename=\"$customization_file\"");
			} else
			{
				header("Content-Disposition: inline; filename=\"$customization_file\"");
			}
			readfile("$config[project_path]/$customization_file");
		}
	}
	die;
}


$smarty = new mysmarty();

$data = [];
foreach ($config as $k => $v)
{
	$item = [];
	$item['key'] = $k;
	$item['value'] = $v;
	$data[] = $item;
}

$logs = [];
$i = 1;
foreach ($log_files as $log_file)
{
	if (is_file("$config[project_path]/admin/logs/$log_file"))
	{
		$item = [];
		$item['file_name'] = $log_file;
		$item['file_time'] = filemtime("$config[project_path]/admin/logs/$log_file");
		$item['file_size'] = sizeToHumanString(filesize("$config[project_path]/admin/logs/$log_file"), 2);
		$logs[] = $item;
	}
	$i++;
}
usort($logs, static function($a, $b) {
	return $b['file_time'] - $a['file_time'];
});

$engine_customizations = [];
$i = 1;
foreach ($engine_customization_files as $customization_file)
{
	if (is_file("$config[project_path]/$customization_file"))
	{
		$item = [];
		$item['file_name'] = $customization_file;
		$item['file_time'] = filemtime("$config[project_path]/$customization_file");
		$item['file_size'] = sizeToHumanString(filesize("$config[project_path]/$customization_file"), 2);
		$engine_customizations[] = $item;
	}
	$i++;
}

$monitored_vars = ['date.timezone', 'allow_url_fopen', 'file_uploads', 'max_execution_time', 'max_input_time', 'max_input_vars', 'memory_limit', 'post_max_size', 'open_basedir', 'sendmail_path', 'session.cookie_domain', 'session.save_handler', 'session.save_path', 'session.gc_maxlifetime', 'upload_max_filesize', 'upload_tmp_dir', 'max_file_uploads', 'disable_functions'];
$ini_vars = ini_get_all();
foreach ($ini_vars as $k => $v)
{
	if (!in_array($k, $monitored_vars))
	{
		unset($ini_vars[$k]);
	}
}
$ini_vars['date.timezone']['local_value'] = date_default_timezone_get();

$memcache_stats = [];
if ($config['memcache_server'] <> '' && class_exists('Memcached'))
{
	$memcache = new Memcached();
	if ($memcache->addServer($config['memcache_server'], $config['memcache_port']))
	{
		$memcache_total_bytes = 0;
		$memcache_used_bytes = 0;
		$memcache_get_hits = 0;
		$memcache_get_misses = 0;
		$stats = $memcache->getStats();
		if (is_array($stats))
		{
			foreach ($stats as $server)
			{
				$memcache_total_bytes += $server['limit_maxbytes'];
				$memcache_used_bytes += $server['bytes'];
				$memcache_get_hits += $server['get_hits'];
				$memcache_get_misses += $server['get_misses'];
			}
			if ($memcache_total_bytes > 0)
			{
				$memcache_stats['memcache_usage_percent'] = floor($memcache_used_bytes / $memcache_total_bytes * 100);
				$memcache_stats['memcache_total_memory'] = sizeToHumanString($memcache_total_bytes);
				$memcache_stats['memcache_used_memory'] = sizeToHumanString($memcache_used_bytes);
				$memcache_stats['memcache_total_hits'] = $memcache_get_hits + $memcache_get_misses;
				$memcache_stats['memcache_success_hits'] = $memcache_get_hits;
				if ($memcache_get_hits + $memcache_get_misses > 0)
				{
					$memcache_stats['memcache_success_percent'] = floor($memcache_get_hits / ($memcache_get_hits + $memcache_get_misses) * 100);
				}
			}
		}
	}
}

$system = [];

$exec_res = [];
$exec_res2 = [];
exec("$config[php_path] -v 2>&1", $exec_res);
exec("$config[php_path] -i 2>&1", $exec_res2);
if (array_cnt($exec_res2) > 2)
{
	array_shift($exec_res2);
	array_shift($exec_res2);
}
$system[] = ['name' => 'PHP CLI', 'type' => 'multiline', 'value' => implode("\n", $exec_res) . "\n" . implode("\n", $exec_res2)];

$exec_res = [];
exec("$config[ffmpeg_path] -version 2>&1", $exec_res);
$system[] = ['name' => 'FFmpeg', 'type' => 'multiline', 'value' => implode("\n", $exec_res)];

$exec_res = [];
exec("$config[image_magick_path] 2>&1", $exec_res);
$system[] = ['name' => 'ImageMagick', 'type' => 'multiline', 'value' => implode("\n", $exec_res)];

$exec_res = [];
$wget_path = $config['wget_path'];
if ($wget_path == '' || $wget_path == 'disabled')
{
	$wget_path = 'wget';
}
exec("$wget_path -V 2>&1", $exec_res);
$system[] = ['name' => 'WGet', 'type' => 'multiline', 'value' => implode("\n", $exec_res)];

$curl_test_url = "http://www.google.com";
if ($_REQUEST['curl_test_url'] != '')
{
	$curl_test_url = trim($_REQUEST['curl_test_url']);
}

$exec_res = [];
exec("curl -I -L " . escapeshellarg($curl_test_url), $exec_res);
if (trim(implode("\n", $exec_res)) == '')
{
	$exec_res = [];
	exec("curl --connect-timeout 5 -I -L " . escapeshellarg($curl_test_url) . " 2>&1", $exec_res);
}
$system[] = ['name' => 'cURL (console)', 'type' => 'multiline', 'value' => "curl -I $curl_test_url\n\n" . implode("\n", $exec_res)];

if (function_exists('curl_init'))
{
	$exec_res = get_page("", $curl_test_url, "", "", 0, 1, 5, "", ['return_error' => true]);
	$system[] = ['name' => 'cURL (PHP)', 'type' => 'multiline', 'value' => "$curl_test_url\n\n" . $exec_res];
}

$process_stats = [];
exec("ps -ax", $process_stats);
foreach ($process_stats as $k => $v)
{
	if (strpos($v, 'php') === false && strpos($v, 'convert') === false && strpos($v, 'ffmpeg') === false)
	{
		unset($process_stats[$k]);
	}
}
$system[] = ['name' => 'ProcessStats', 'type' => 'multiline', 'value' => implode("\n", $process_stats)];

$sql_queries = [];
$exec_res = mr2array(sql_pr("show full processlist"));
foreach ($exec_res as $v)
{
	if ($v['Command'] != 'Sleep' && $v['Info'] != 'show full processlist')
	{
		$sql_queries[] = "Query $v[Id]: $v[Info] ($v[State], $v[Time]s)";
	}
}
$system[] = ['name' => 'DatabaseStats', 'type' => 'multiline', 'value' => implode("\n", $sql_queries)];

$system[] = ['name' => 'Cron', 'value' => "cd $config[project_path]/admin/include && $config[php_path] cron.php > /dev/null 2>&1"];

if (isset($_SESSION['admin_notifications']['list']['administration.installation.php_exec']['title']))
{
	$_POST['errors'][] = $lang['notifications']['critical_prefix'] . $_SESSION['admin_notifications']['list']['administration.installation.php_exec']['title'];
}
if (isset($_SESSION['admin_notifications']['list']['administration.installation.console_php_version']['title']))
{
	$_POST['errors'][] = $lang['notifications']['critical_prefix'] . $_SESSION['admin_notifications']['list']['administration.installation.console_php_version']['title'];
}
if (isset($_SESSION['admin_notifications']['list']['administration.installation.cron_execution']['title']))
{
	$_POST['errors'][] = $lang['notifications']['critical_prefix'] . $_SESSION['admin_notifications']['list']['administration.installation.cron_execution']['title'];
}
if (isset($_SESSION['admin_notifications']['list']['administration.installation.cron_directory']['title']))
{
	$_POST['errors'][] = $lang['notifications']['critical_prefix'] . $_SESSION['admin_notifications']['list']['administration.installation.cron_directory']['title'];
}
if (isset($_SESSION['admin_notifications']['list']['administration.installation.cron_duplicate']['title']))
{
	$_POST['errors'][] = $lang['notifications']['critical_prefix'] . $_SESSION['admin_notifications']['list']['administration.installation.cron_duplicate']['title'];
}

$kvs_processes = [];
$admin_processes = mr2array(sql_pr("select * from $config[tables_prefix_multi]admin_processes where pid not like 'cron_plugins.%' order by case when pid='main' then 1 else 0 end desc, last_exec_date desc"));
$importing_feeds = mr2array(sql_pr("select * from $config[tables_prefix]videos_feeds_import where status_id=1 order by last_exec_date desc"));
$importing_tasks = mr2array(sql_pr("select * from $config[tables_prefix]background_imports where status_id in (0, 1) order by added_date desc"));
foreach ($admin_processes as $admin_process)
{
	$log_filename = "$admin_process[pid].txt";
	$lock_id = "admin/data/system/$admin_process[pid]";
	if ($admin_process['pid'] == 'main')
	{
		$log_filename = 'cron.txt';
		$lock_id = '';
	}
	if ($log_filename)
	{
		$admin_process['log_filename'] = $log_filename;
	}
	if ($lock_id)
	{
		$admin_process['lock_id'] = $lock_id;
		$admin_process['script_file'] = "$config[project_path]/admin/include/$admin_process[pid].php";
	}

	$kvs_processes[] = $admin_process;
	if ($admin_process['pid'] == 'cron_feeds')
	{
		foreach ($importing_feeds as $importing_feed)
		{
			$kvs_processes[] = [
					'level' => 2,
					'pid' => "cron_feeds.$importing_feed[feed_id]",
					'last_exec_date' => $importing_feed['last_exec_date'],
					'last_exec_duration' => $importing_feed['last_exec_duration'],
					'exec_interval' => $importing_feed['exec_interval_hours'] * 3600 + $importing_feed['exec_interval_minutes'] * 60,
					'log_filename' => "feeds_videos_$importing_feed[feed_id].txt",
					'lock_id' => "admin/data/system/feeds_videos_$importing_feed[feed_id]",
					'script_file' => "$config[project_path]/admin/background_feed_videos.php $importing_feed[feed_id]",
					'link' => "videos_feeds_import.php?action=change&item_id=$importing_feed[feed_id]",
					'permission' => 'videos|feeds_import',
			];
		}
	} elseif ($admin_process['pid'] == 'cron_import')
	{
		foreach ($importing_tasks as $importing_task)
		{
			$kvs_processes[] = [
					'level' => 2,
					'pid' => "cron_import.$importing_task[import_id]",
					'last_exec_date' => '0000-00-00 00:00:00',
					'last_exec_duration' => 0,
					'exec_interval' => 0,
					'is_running' => $importing_task['status_id'],
					'link' => "log_imports.php?action=change&item_id=$importing_task[import_id]",
					'log_link' => "log_imports.php?action=import_log&item_id=$importing_task[import_id]",
					'permission' => 'system|administration',
					'log_filename' => "tasks/$importing_task[task_id].txt",
					'script_file' => in_array($importing_task['type_id'], [1,3]) ? "$config[project_path]/admin/background_import.php $importing_task[import_id]" : "$config[project_path]/admin/background_import_albums.php $importing_task[import_id]",
					'import_details' => $importing_task
			];
		}
	} elseif ($admin_process['pid'] == 'cron_plugins')
	{
		$plugin_processes = mr2array(sql_pr("select * from $config[tables_prefix_multi]admin_processes where pid like 'cron_plugins.%' order by last_exec_date desc"));
		foreach ($plugin_processes as $plugin_process)
		{
			$plugin_id = str_replace('cron_plugins.', '', $plugin_process['pid']);

			$plugin_process['level'] = 2;
			$plugin_process['link'] = "plugins.php?plugin_id=$plugin_id";
			$plugin_process['permission'] = "plugins|$plugin_id";
			$plugin_process['log_filename'] = "plugins/$plugin_id.txt";
			$plugin_process['lock_id'] = "admin/data/plugins/$plugin_id/cron";
			$plugin_process['script_file'] = "$config[project_path]/admin/plugins/$plugin_id/$plugin_id.php";
			$kvs_processes[] = $plugin_process;
		}
	}
}
foreach ($kvs_processes as &$kvs_process)
{
	if ($kvs_process['log_filename'])
	{
		$kvs_process['last_message'] = last_line_from_log_file("$config[project_path]/admin/logs/$kvs_process[log_filename]");
	}
	if ($kvs_process['lock_id'])
	{
		if (KvsUtilities::is_locked($kvs_process['lock_id']))
		{
			$kvs_process['is_running'] = 1;
		}
	}
}
unset($kvs_process);
foreach ($kvs_processes as &$kvs_process)
{
	if ($kvs_process['script_file'] && $kvs_process['is_running'] == 1)
	{
		foreach ($process_stats as $line => $process_stat)
		{
			if (strpos($process_stat, $kvs_process['script_file']) !== false)
			{
				$kvs_process['osid'] = intval($process_stat);
				if ($_REQUEST['action'] == 'kill' && intval($process_stat) == intval($_REQUEST['pid']))
				{
					$result_array = [];
					$result_code = 0;
					exec('kill -9 ' . intval($process_stat), $result_array, $result_code);
					if ($result_code == 0)
					{
						$kvs_process['is_running'] = 0;
						unset($process_stats[$line]);
						foreach ($system as &$system_item)
						{
							if ($system_item['name'] == 'ProcessStats')
							{
								$system_item['value'] = implode("\n", $process_stats);
							}
						}
						if ($kvs_process['import_details']['task_id'] > 0)
						{
							file_put_contents("$config[project_path]/admin/logs/tasks/{$kvs_process['import_details']['task_id']}.txt", date("[Y-m-d H:i:s] ") . "ERROR Interrupted by user\n", FILE_APPEND | LOCK_EX);
							sql_delete("delete from $config[tables_prefix]background_tasks where task_id=?", $kvs_process['import_details']['task_id']);
							sql_update("update $config[tables_prefix]background_imports set status_id=3 where import_id=?", $kvs_process['import_details']['import_id']);
						}
					}
				}
			}
		}
	}
}
unset($kvs_process);

$smarty->assign('data', $data);
$smarty->assign('phpversion', PHP_VERSION);
$smarty->assign('system', $system);
$smarty->assign('processes', $kvs_processes);
$smarty->assign('logs', $logs);
$smarty->assign('engine_customizations', $engine_customizations);
$smarty->assign('ini_vars', $ini_vars);
$smarty->assign('memcache_stats', $memcache_stats);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', $lang['settings']['installation_header']);

$smarty->display("layout.tpl");
