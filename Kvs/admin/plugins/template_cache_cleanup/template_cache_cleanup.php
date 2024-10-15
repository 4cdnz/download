<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function template_cache_cleanupInit()
{
	global $config;

	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.template_cache_cleanup.automatic_cleanup_not_enabled', 'plugins|template_cache_cleanup', 'plugins.php?plugin_id=template_cache_cleanup', KvsAdminNotificationEnum::SEVERITY_WARNING));

	$plugin_path = "$config[project_path]/admin/data/plugins/template_cache_cleanup";
	mkdir_recursive($plugin_path);

	if (!is_file("$plugin_path/data.dat"))
	{
		$data = [];
		$data['is_enabled'] = 1;
		$data['interval'] = 24;
		$data['tod'] = 0;

		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	} else
	{
		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	}

	if ($data['is_enabled'] > 0)
	{
		sql_insert("insert into $config[tables_prefix_multi]admin_processes set pid='cron_plugins.template_cache_cleanup', exec_interval=?, exec_tod=?, status_data='a:0:{}'", intval($data['interval']) * 3600, intval($data['tod']));
	}
}

function template_cache_cleanupIsEnabled()
{
	global $config;

	template_cache_cleanupInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/template_cache_cleanup";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	$result = ($data['is_enabled'] > 0);

	add_admin_notification('plugins.template_cache_cleanup.automatic_cleanup_not_enabled', $result ? 0 : 1);

	return $result;
}

function template_cache_cleanupShow()
{
	global $config, $lang, $errors, $page_name, $list_messages;

	template_cache_cleanupInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/template_cache_cleanup";

	$errors = null;

	if ($_GET['action'] == 'get_log')
	{
		download_log_file("$config[project_path]/admin/logs/plugins/template_cache_cleanup.txt");
		die;
	} elseif ($_GET['action'] == 'example')
	{
		header('Content-Type: text/plain; charset=utf-8');
		if (intval($_GET['result_id']) > 0)
		{
			$result_id = intval($_GET['result_id']);
			$result = @unserialize(file_get_contents("$plugin_path/task-$result_id.dat"));
			if (isset($result['cache_details'][$_GET['template']]))
			{
				echo sizeToHumanString(strlen($result['cache_details'][$_GET['template']]['example'])) . "\n";
				echo $result['cache_details'][$_GET['template']]['example'];
			}
		}
		die;
	} elseif ($_GET['action'] == 'progress')
	{
		header('Content-Type: application/json; charset=utf-8');

		$json_response = ['status' => 'success'];

		$task_id = intval($_GET['task_id']);
		$json = @json_decode(file_get_contents("$plugin_path/task-progress-$task_id.dat"), true);
		if (is_array($json))
		{
			if (isset($json['percent']))
			{
				$json_response['percent'] = intval($json['percent']);
				if (intval($json['percent']) == 100)
				{
					$json_response['url'] = "plugins.php?plugin_id=template_cache_cleanup&result_id=$task_id";
					$json_response['redirect'] = true;
					@unlink("$plugin_path/task-progress-$task_id.dat");
				}
			}
			if (isset($json['message']))
			{
				$json_response['message'] = $json['message'];
			} elseif (isset($json['message_id']))
			{
				$json_response['message'] = $lang['plugins']['template_cache_cleanup'][$json['message_id']];
				if (is_array($json['message_params']))
				{
					foreach ($json['message_params'] as $name => $value)
					{
						if (is_numeric($name))
						{
							$name++;
						}
						$json_response['message'] = str_replace("%$name%", $value, $json_response['message']);
					}
				}
			}
		}
		die(json_encode($json_response));
	} elseif ($_POST['action'] == 'change_complete')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (intval($_POST['is_enabled']) == 1)
		{
			validate_field('empty_int', $_POST['interval'], $lang['plugins']['template_cache_cleanup']['field_schedule']);
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
		}

		if (isset($_POST['calculate_stats']))
		{
			$rnd = mt_rand(10000000, 99999999);

			chdir("$config[project_path]/admin/include");
			exec("$config[php_path] $config[project_path]/admin/plugins/template_cache_cleanup/template_cache_cleanup.php calculate $rnd > /dev/null 2>&1 &");
			return_ajax_success("$page_name?plugin_id=template_cache_cleanup&action=progress&task_id=$rnd", 2);
		} elseif (isset($_POST['start_now']))
		{
			chdir("$config[project_path]/admin/include");
			exec("$config[php_path] $config[project_path]/admin/plugins/template_cache_cleanup/template_cache_cleanup.php cron > $config[project_path]/admin/logs/plugins/template_cache_cleanup.txt 2>&1 &");
			sleep(1);
			return_ajax_success("$page_name?plugin_id=template_cache_cleanup");
		} else
		{
			if (!is_array($errors))
			{
				$save_data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
				$save_data['speed'] = $_POST['speed'];
				$save_data['is_enabled'] = intval($_POST['is_enabled']);
				$save_data['interval'] = intval($_POST['interval']);
				$save_data['tod'] = intval($_POST['tod']);

				file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

				if (intval($_POST['is_enabled']) > 0)
				{
					if (!sql_update("update $config[tables_prefix_multi]admin_processes set exec_interval=?, exec_tod=? where pid='cron_plugins.template_cache_cleanup'", intval($_POST['interval']) * 3600, intval($_POST['tod'])))
					{
						sql_insert("insert into $config[tables_prefix_multi]admin_processes set exec_interval=?, exec_tod=?, pid='cron_plugins.template_cache_cleanup', status_data='a:0:{}'", intval($_POST['interval']) * 3600, intval($_POST['tod']));
					}
					add_admin_notification('plugins.template_cache_cleanup.automatic_cleanup_not_enabled', 0);
				} else
				{
					sql_delete("delete from $config[tables_prefix_multi]admin_processes where pid='cron_plugins.template_cache_cleanup'");
					add_admin_notification('plugins.template_cache_cleanup.automatic_cleanup_not_enabled', 1);
				}

				return_ajax_success("$page_name?plugin_id=template_cache_cleanup");
			} else
			{
				return_ajax_errors($errors);
			}
		}
	}

	$_POST = @unserialize(file_get_contents("$plugin_path/data.dat"));

	$process = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_processes where pid='cron_plugins.template_cache_cleanup'"));
	if (empty($process))
	{
		$_POST['last_exec_date'] = 0;
		$_POST['next_exec_date'] = 0;
		$_POST['duration'] = 0;
	} else
	{
		$process['last_exec_date'] = $process['last_exec_date'] == '0000-00-00 00:00:00' ? 0 : strtotime($process['last_exec_date']);

		if ($process['last_exec_date'] > 0)
		{
			$next_exec_date = $process['last_exec_date'] + $process['exec_interval'] - 10;
		} else
		{
			$next_exec_date = time();
		}
		if ($process['exec_tod'] > 0)
		{
			$next_exec_hour = date('H', $next_exec_date);
			if ($next_exec_hour < $process['exec_tod'] - 1)
			{
				$next_exec_date = strtotime(date('Y-m-d ', $next_exec_date) . ($process['exec_tod'] - 1) . ':00:00');
			} elseif ($next_exec_hour > $process['exec_tod'] - 1)
			{
				$next_exec_date = strtotime(date('Y-m-d ', $next_exec_date) . ($process['exec_tod'] - 1) . ':00:00') + 86400;
			}
		}

		$_POST['last_exec_date'] = $process['last_exec_date'];
		$_POST['next_exec_date'] = $next_exec_date;
		$_POST['duration'] = $process['last_exec_duration'];
	}

	if (KvsUtilities::is_locked("admin/data/plugins/template_cache_cleanup/cron"))
	{
		$_POST['is_running'] = 1;
	}

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
	}

	if (isset($_SESSION['admin_notifications']['list']['plugins.template_cache_cleanup.automatic_cleanup_not_enabled']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['plugins.template_cache_cleanup.automatic_cleanup_not_enabled']['title'];
	}

	require_once 'include/setup_smarty_site.php';
	$smarty_site = new mysmarty_site();
	$_POST['cache_dir'] = $smarty_site->cache_dir;
	$_POST['storage_dir'] = "$config[project_path]/admin/data/engine/storage";

	if (is_writable($plugin_path))
	{
		exec("find $plugin_path -name '*.dat' -mtime +6 -delete");
	}

	if (intval($_GET['result_id']) > 0)
	{
		$result_id = intval($_GET['result_id']);
		$result = @unserialize(file_get_contents("$plugin_path/task-$result_id.dat"));
		$_POST['cache_size'] = $result['cache_size'];
		$_POST['cache_count'] = $result['cache_count'];
		$_POST['cache_details'] = $result['cache_details'];
		$_POST['storage_size'] = $result['storage_size'];
		$_POST['storage_count'] = $result['storage_count'];
		$_POST['storage_details'] = $result['storage_details'];
	}
}

function template_cache_cleanupCleanup($folder, $max_cache_time_by_template = ['total' => 86400], $speed = 'veryslow', $is_smarty = false, &$template_info = null): int
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/template_cache_cleanup";

	$sleep = 5000;
	switch ($speed)
	{
		case 'slow':
			$sleep = 1000;
			break;
		case 'normal':
			$sleep = 500;
			break;
		case 'fast':
			$sleep = 100;
			break;
		case 'ultrafast':
			$sleep = 50;
			break;
	}
	$result = 0;
	$number_scanned = 0;

	if (is_dir($folder))
	{
		$handle = opendir($folder);
		if ($handle)
		{
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry <> '.' && $entry <> '..')
				{
					$number_scanned++;
					if ($number_scanned % 10000 == 0)
					{
						clearstatcache();
						$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
						if (is_array($data) && $data['speed'] != $speed)
						{
							$speed = $data['speed'];
							$sleep = 5000;
							switch ($speed)
							{
								case 'slow':
									$sleep = 1000;
									break;
								case 'normal':
									$sleep = 500;
									break;
								case 'fast':
									$sleep = 100;
									break;
								case 'ultrafast':
									$sleep = 50;
									break;
							}
							template_cache_cleanupLog("INFO  Changed speed to $speed");
						}
					}

					$max_cache_time = intval($max_cache_time_by_template['total']);
					if ($max_cache_time == 0)
					{
						$max_cache_time = 86400;
					}
					if (is_file("$folder/$entry"))
					{
						$template_name = '';
						if ($is_smarty)
						{
							if (substr($entry, -4) == '.dat')
							{
								$template_name = substr($entry, 0, -37) . '.tpl';
								if (isset($max_cache_time_by_template[$template_name]))
								{
									$max_cache_time = intval($max_cache_time_by_template[$template_name]);
								}
							} else
							{
								$template_name_separator = strrpos($entry, '%%');
								if ($template_name_separator !== false)
								{
									$template_name = substr($entry, $template_name_separator + 2);
									if (isset($max_cache_time_by_template[$template_name]))
									{
										$max_cache_time = intval($max_cache_time_by_template[$template_name]);
									}
								}
							}
						}
						if (time() - filectime("$folder/$entry") > $max_cache_time)
						{
							if (@unlink("$folder/$entry"))
							{
								$result++;
							}
						} elseif ($template_name)
						{
							$template_info[$template_name]['cache_time'] = $max_cache_time;
							$template_info[$template_name]['count']++;
							$template_info[$template_name]['size'] += filesize("$folder/$entry");
						}
					} elseif (is_dir("$folder/$entry"))
					{
						$result += template_cache_cleanupCleanup("$folder/$entry", $max_cache_time_by_template, $speed, $is_smarty, $template_info);
						@rmdir("$folder/$entry");
					}
					usleep($sleep);
				}
			}
			closedir($handle);
		}
	}
	return $result;
}

function template_cache_cleanupCron()
{
	global $config;

	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'setup_smarty_site.php';

	ini_set('display_errors', 1);

	$start_time = time();

	template_cache_cleanupInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/template_cache_cleanup";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data) || $data['is_enabled'] == 0)
	{
		return;
	}

	$memory_limit = mr2number(sql("select value from $config[tables_prefix]options where variable='LIMIT_MEMORY'"));
	if ($memory_limit == 0)
	{
		$memory_limit = 512;
	}
	ini_set('memory_limit', "{$memory_limit}M");

	template_cache_cleanupLog('INFO  Starting template_cache_cleanup plugin');
	template_cache_cleanupLog('INFO  Memory limit: ' . ini_get('memory_limit'));

	$max_cache_time_by_template = template_cache_cleanupDetectMaxCacheTime();
	template_cache_cleanupLog("INFO  Max cache time detected: $max_cache_time_by_template[total]");

	$smarty_site = new mysmarty_site();
	$total_cnt = 0;

	$template_info = [];
	$cnt = template_cache_cleanupCleanup($smarty_site->cache_dir, $max_cache_time_by_template, $data['speed'], true, $template_info);
	$total_cnt += $cnt;

	uasort($template_info, function($a, $b) {
		return $b['count'] - $a['count'];
	});
	$smarty_log = '';
	foreach ($template_info as $template => $info)
	{
		$smarty_log .= "\n    $template: $info[count] (" . sizeToHumanString($info['size']) . ", $info[cache_time]s)";
	}
	template_cache_cleanupLog("INFO  Cache stats: $smarty_log");
	template_cache_cleanupLog("INFO  Removed $cnt files in cache folder");

	$template_info = [];
	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/storage", $max_cache_time_by_template, $data['speed'], true, $template_info);
	$total_cnt += $cnt;

	uasort($template_info, function($a, $b) {
		return $b['count'] - $a['count'];
	});
	$smarty_log = '';
	foreach ($template_info as $template => $info)
	{
		$smarty_log .= "\n    $template: $info[count] (" . sizeToHumanString($info['size']) . ", $info[cache_time]s)";
	}
	template_cache_cleanupLog("INFO  Storage stats: $smarty_log");
	template_cache_cleanupLog("INFO  Removed $cnt files in storage folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/videos_info", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in videos_info folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/albums_info", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in albums_info folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/comments_info", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in comments_info folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/content_sources_info", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in content_sources_info folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/dvds_info", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in dvds_info folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/feeds_info", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in feeds_info folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/models_info", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in models_info folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/posts_info", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in posts_info folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/playlists_info", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in playlists_info folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/random_video", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in random_video folder");

	$cnt = template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/random_album", $max_cache_time_by_template, $data['speed']);
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in random_album folder");

	$sleep = 5000;
	$cnt = 0;
	$rotator_dirs = get_contents_from_dir("$config[project_path]/admin/data/engine/rotator/videos/list", 2);
	foreach ($rotator_dirs as $rotator_dir)
	{
		$rotator_files = get_contents_from_dir("$config[project_path]/admin/data/engine/rotator/videos/list/$rotator_dir", 1);
		foreach ($rotator_files as $rotator_file)
		{
			if (time() - filectime("$config[project_path]/admin/data/engine/rotator/videos/list/$rotator_dir/$rotator_file") > $max_cache_time_by_template['total'])
			{
				if (@unlink("$config[project_path]/admin/data/engine/rotator/videos/list/$rotator_dir/$rotator_file"))
				{
					$cnt++;
				}
			}
			usleep($sleep);
		}
	}
	$total_cnt += $cnt;
	template_cache_cleanupLog("INFO  Removed $cnt files in rotator folder");

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if (is_array($data))
	{
		$data['deleted_files'] = $total_cnt;
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	}

	sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_plugins.template_cache_cleanup'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));

	template_cache_cleanupLog("INFO  Total deleted $total_cnt files");
}

function template_cache_cleanupDetectMaxCacheTime(): array
{
	global $config, $regexp_valid_external_id, $regexp_valid_block_name;

	require_once "$config[project_path]/admin/include/functions_admin.php";

	$max_cache_time = 0;
	$max_cache_time_by_template = [];
	$pages = get_site_pages();
	$templates_data = get_site_parsed_templates();
	foreach ($pages as $v)
	{
		if (!preg_match($regexp_valid_external_id, $v['external_id']))
		{
			continue;
		}
		$template_info = $templates_data["$v[external_id].tpl"];
		if (isset($template_info))
		{
			foreach ($template_info['block_inserts'] as $block_insert)
			{
				$block_id = trim($block_insert['block_id']);
				$block_name = trim($block_insert['block_name']);

				if (preg_match($regexp_valid_external_id, $block_id) && preg_match($regexp_valid_block_name, $block_name))
				{
					$block_name = strtolower(str_replace(" ", "_", $block_name));
					$file_data = @file_get_contents("$config[project_path]/admin/data/config/$v[external_id]/{$block_id}_$block_name.dat");
					$temp_bl = explode("||", $file_data);
					$cache_time = intval($temp_bl[0]);
					if ($cache_time > $max_cache_time)
					{
						$max_cache_time = $cache_time;
					}
					$max_cache_time_by_template["{$block_id}_$block_name.tpl"] = max(intval($max_cache_time_by_template["{$block_id}_$block_name"]), $cache_time);
				}
			}
		}
	}

	$max_cache_time_by_template['config.tpl'] = 0;

	$player_files = get_player_data_files();
	foreach ($player_files as $player_file)
	{
		$player_data = @unserialize(file_get_contents($player_file['file']));
		if (intval($player_data['embed_cache_time']) > 0)
		{
			$max_cache_time_by_template['config.tpl'] = max($max_cache_time_by_template['config.tpl'], intval($player_data['embed_cache_time']));
		}
	}

	$max_cache_time_by_template['total'] = $max_cache_time;
	return $max_cache_time_by_template;
}

function template_cache_cleanupLog($message)
{
	if ($message)
	{
		echo date('[Y-m-d H:i:s] ') . $message . "\n";
	} else
	{
		echo "\n";
	}
}

if ($_SERVER['argv'][1] == 'calculate' && intval($_SERVER['argv'][2]) > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'setup_smarty_site.php';

	$task_id = intval($_SERVER['argv'][2]);
	$plugin_path = "$config[project_path]/admin/data/plugins/template_cache_cleanup";

	$result = [];
	$smarty_site = new mysmarty_site();
	$cache_dir = $smarty_site->cache_dir;
	$storage_dir = "$config[project_path]/admin/data/engine/storage";

	file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 0, 'message_id' => 'message_calculating', 'message_params' => ['/admin/smarty/cache']]), LOCK_EX);

	$size = 0;
	$count = 0;
	$template_info = [];
	if (is_dir($cache_dir))
	{
		if ($handle = opendir($cache_dir))
		{
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry <> '.' && $entry <> '..')
				{
					if (is_file("$cache_dir/$entry"))
					{
						$count++;
						$filesize = filesize("$cache_dir/$entry");
						$size += $filesize;
						$template_name_separator = strrpos($entry, '%%');
						if ($template_name_separator !== false)
						{
							$template_name = substr($entry, $template_name_separator + 2);
							$template_info[$template_name]['count']++;
							$template_info[$template_name]['size'] += $filesize;
							if (!isset($template_info[$template_name]['example']))
							{
								$template_info[$template_name]['example'] = file_get_contents("$cache_dir/$entry");
							}
						}
					}
				}
			}
			closedir($handle);
		}
	}

	uasort($template_info, function($a, $b) {
		return $b['count'] - $a['count'];
	});

	$result['cache_count'] = $count;
	$result['cache_size'] = $size;
	$result['cache_details'] = $template_info;

	file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 50, 'message_id' => 'message_calculating', 'message_params' => ['/admin/data/engine/storage']]), LOCK_EX);

	$size = 0;
	$count = 0;
	$template_info = [];
	if (is_dir($storage_dir))
	{
		if ($handle_dir = opendir($storage_dir))
		{
			while (false !== ($entry_dir = readdir($handle_dir)))
			{
				if ($entry_dir <> '.' && $entry_dir <> '..')
				{
					if (is_dir("$storage_dir/$entry_dir"))
					{
						if ($handle = opendir("$storage_dir/$entry_dir"))
						{
							while (false !== ($entry = readdir($handle)))
							{
								if ($entry <> '.' && $entry <> '..')
								{
									if (is_file("$storage_dir/$entry_dir/$entry"))
									{
										$count++;
										$filesize = filesize("$storage_dir/$entry_dir/$entry");
										$size += $filesize;
										$template_name = substr($entry, 0, -37) . '.tpl';
										$template_info[$template_name]['count']++;
										$template_info[$template_name]['size'] += $filesize;
										if (!isset($template_info[$template_name]['example']))
										{
											$template_info[$template_name]['example'] = file_get_contents("$storage_dir/$entry_dir/$entry");
										}
									}
								}
							}
						}
					}
				}
			}
			closedir($handle);
		}
	}

	uasort($template_info, function($a, $b) {
		return $b['count'] - $a['count'];
	});

	$result['storage_count'] = $count;
	$result['storage_size'] = $size;
	$result['storage_details'] = $template_info;

	file_put_contents("$plugin_path/task-$task_id.dat", serialize($result), LOCK_EX);
	file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 100]), LOCK_EX);
	die;
} elseif ($_SERVER['argv'][1] == 'cron' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';

	KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
	if (!KvsUtilities::try_exclusive_lock('admin/data/plugins/template_cache_cleanup/cron'))
	{
		die('Already locked');
	}

	template_cache_cleanupCron();
	die;
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo 'OK';
}
