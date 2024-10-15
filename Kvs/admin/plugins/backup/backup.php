<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function backupInit()
{
	global $config;

	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.backup.automatic_backup_not_enabled', 'plugins|backup', 'plugins.php?plugin_id=backup', KvsAdminNotificationEnum::SEVERITY_WARNING));
	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.backup.backup_directory_not_writable', 'plugins|backup', 'plugins.php?plugin_id=backup', KvsAdminNotificationEnum::SEVERITY_ERROR, true));
	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.backup.automatic_backup_failed', 'plugins|backup', 'plugins.php?plugin_id=backup', KvsAdminNotificationEnum::SEVERITY_ERROR, true));

	$plugin_path = "$config[project_path]/admin/data/plugins/backup";
	mkdir_recursive($plugin_path);

	if (!is_file("$plugin_path/data.dat"))
	{
		$data = [];
		$data['backup_folder'] = "$config[project_path]/admin/data/backup";
		$data['auto_backup_daily'] = 1;
		$data['auto_backup_weekly'] = 1;
		$data['auto_backup_monthly'] = 1;
		$data['auto_skip_database'] = 0;
		$data['auto_skip_content_auxiliary'] = 0;
		$data['tod'] = 0;

		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	} else
	{
		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	}

	if (intval($data['auto_backup_daily']) + intval($data['auto_backup_weekly']) + intval($data['auto_backup_monthly']) > 0)
	{
		sql_insert("insert into $config[tables_prefix_multi]admin_processes set pid='cron_plugins.backup', exec_interval=?, exec_tod=?, status_data='a:0:{}'", 86400, intval($data['tod']));
	}
}

function backupIsEnabled()
{
	global $config;

	backupInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/backup";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	$result = (intval($data['auto_backup_daily']) + intval($data['auto_backup_weekly']) + intval($data['auto_backup_monthly']) > 0);

	add_admin_notification('plugins.backup.automatic_backup_not_enabled', $result ? 0 : 1);

	return $result;
}

function backupShow()
{
	global $config, $lang, $errors, $page_name, $list_messages;

	backupInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/backup";

	$errors = null;

	if ($_GET['action'] == 'get_log')
	{
		download_log_file("$config[project_path]/admin/logs/plugins/backup.txt");
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
					$json_response['url'] = "plugins.php?plugin_id=backup";
					$json_response['redirect'] = true;
					@unlink("$plugin_path/task-progress-$task_id.dat");
				}
			}
			if (isset($json['message']))
			{
				$json_response['message'] = $json['message'];
			} elseif (isset($json['message_id']))
			{
				$json_response['message'] = $lang['plugins']['backup'][$json['message_id']];
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
			if (intval($json['error_code']) > 0)
			{
				$json_response['url'] = "plugins.php?plugin_id=backup&error=$json[error_code]";
				$json_response['redirect'] = true;
				@unlink("$plugin_path/task-progress-$task_id.dat");
			}
		}
		die(json_encode($json_response));
	} elseif ($_GET['action'] == 'download')
	{
		if ($_SESSION['userdata']['is_superadmin'] > 0)
		{
			$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
			if ($data['backup_folder'] != '' && is_dir($data['backup_folder']))
			{
				$results = scandir($data['backup_folder']);
				if (is_array($results))
				{
					foreach ($results as $file)
					{
						if ($file == $_GET['file'])
						{
							if (is_file("$data[backup_folder]/$file") && (strpos($file, '.zip') !== false || strpos($file, '.gz') !== false))
							{
								header('Content-Type: application/gzip');
								header('Content-Disposition: attachment; filename="' . $file . '"');
								header('Content-Length: ' . filesize("$data[backup_folder]/$file"));
								readfile("$data[backup_folder]/$file");
								die;
							}
						}
					}
				}
			}
		}

		http_response_code(403);
		die;
	} elseif ($_POST['action'] == 'save_backup')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		$data = @unserialize(file_get_contents("$plugin_path/data.dat")) ?: [];
		if ($_POST['ftp_pass'] == '')
		{
			$_POST['ftp_pass'] = $data['ftp_pass'];
		}
		if ($_POST['s3_api_secret'] == '')
		{
			$_POST['s3_api_secret'] = $data['s3_api_secret'];
		}

		validate_field('empty', $_POST['backup_folder'], $lang['plugins']['backup']['field_backup_folder']);
		if (!is_writable("$_POST[backup_folder]"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$_POST[backup_folder]");
		}
		if ($_POST['remote_storage_type'] != '')
		{
			if ($_POST['remote_storage_type'] == 'ftp')
			{
				validate_field('empty', $_POST['ftp_host'], $lang['plugins']['backup']['field_backup_ftp_host']);
				validate_field('empty_int', $_POST['ftp_port'], $lang['plugins']['backup']['field_backup_ftp_port']);
				validate_field('empty', $_POST['ftp_user'], $lang['plugins']['backup']['field_backup_ftp_user']);
				validate_field('empty', $_POST['ftp_pass'], $lang['plugins']['backup']['field_backup_ftp_pass']);
				validate_field('empty_int', $_POST['ftp_timeout'], $lang['plugins']['backup']['field_backup_ftp_timeout']);
			} elseif ($_POST['remote_storage_type'] == 's3')
			{
				validate_field('empty', $_POST['s3_region'], $lang['plugins']['backup']['field_backup_s3_region']);
				validate_field('empty', $_POST['s3_bucket'], $lang['plugins']['backup']['field_backup_s3_bucket']);
				validate_field('empty', $_POST['s3_api_key'], $lang['plugins']['backup']['field_backup_s3_api_key']);
				validate_field('empty', $_POST['s3_api_secret'], $lang['plugins']['backup']['field_backup_s3_api_secret']);
			}
			if (!is_array($errors))
			{
				$server_data = backup_EmulateRemoteServerData($_POST);
				$test_result = test_connection_detailed($server_data);
				if ($test_result == 1)
				{
					$errors[] = get_aa_error('server_invalid_connection1', $_POST['ftp_host'], $_POST['ftp_port']);
				} elseif ($test_result == 2)
				{
					$errors[] = get_aa_error('server_invalid_connection2');
				} elseif ($test_result == 3)
				{
					$errors[] = get_aa_error('server_invalid_connection3');
				} elseif ($test_result == 4)
				{
					$errors[] = get_aa_error('server_no_ftp_extension', $lang['plugins']['backup']['remote_storage_type']);
				} elseif ($test_result == 5)
				{
					$errors[] = get_aa_error('server_no_aws_extension', $lang['plugins']['backup']['remote_storage_type']);
				}
			}
		}

		if (!is_writable($plugin_path))
		{
			$errors[] = get_aa_error('filesystem_permission_write', $plugin_path);
		}

		$rnd = 0;
		if (!is_array($errors))
		{
			$data['backup_folder'] = $_POST['backup_folder'];
			$data['remote_storage_type'] = $_POST['remote_storage_type'];
			$data['ftp_host'] = $_POST['ftp_host'];
			$data['ftp_port'] = $_POST['ftp_port'];
			$data['ftp_timeout'] = $_POST['ftp_timeout'];
			$data['ftp_user'] = $_POST['ftp_user'];
			$data['ftp_pass'] = $_POST['ftp_pass'];
			$data['ftp_folder'] = $_POST['ftp_folder'];
			$data['s3_region'] = $_POST['s3_region'];
			$data['s3_endpoint'] = $_POST['s3_endpoint'];
			$data['s3_bucket'] = $_POST['s3_bucket'];
			$data['s3_prefix'] = $_POST['s3_prefix'];
			$data['s3_api_key'] = $_POST['s3_api_key'];
			$data['s3_api_secret'] = $_POST['s3_api_secret'];
			$data['s3_upload_chunk_size_mb'] = intval($_POST['s3_upload_chunk_size_mb']);
			$data['s3_is_endpoint_subdirectory'] = intval($_POST['s3_is_endpoint_subdirectory']);
			$data['auto_backup_daily'] = intval($_POST['auto_backup_daily']);
			$data['auto_backup_weekly'] = intval($_POST['auto_backup_weekly']);
			$data['auto_backup_monthly'] = intval($_POST['auto_backup_monthly']);
			$data['auto_skip_database'] = intval($_POST['auto_skip_database']);
			$data['auto_skip_content_auxiliary'] = intval($_POST['auto_skip_content_auxiliary']);
			$data['tod'] = intval($_POST['tod']);

			if (file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX) === false)
			{
				@unlink("$plugin_path/data.dat");
				if (file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX) === false)
				{
					$errors[] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
				}
			}

			if (intval($_POST['backup_mysql']) + intval($_POST['backup_website']) + intval($_POST['backup_player']) + intval($_POST['backup_kvs']) + intval($_POST['backup_content_auxiliary']) > 0)
			{
				$rnd = mt_rand(10000000, 99999999);
				$data['backup_mysql'] = intval($_POST['backup_mysql']);
				$data['backup_website'] = intval($_POST['backup_website']);
				$data['backup_player'] = intval($_POST['backup_player']);
				$data['backup_kvs'] = intval($_POST['backup_kvs']);
				$data['backup_content_auxiliary'] = intval($_POST['backup_content_auxiliary']);

				if (file_put_contents("$plugin_path/task-$rnd.dat", serialize($data), LOCK_EX) === false)
				{
					$errors[] = get_aa_error('filesystem_permission_write', "$plugin_path/task-$rnd.dat");
				}
			}

			if (intval($_POST['auto_backup_daily']) + intval($_POST['auto_backup_weekly']) + intval($_POST['auto_backup_monthly']) > 0)
			{
				if (!sql_update("update $config[tables_prefix_multi]admin_processes set exec_interval=?, exec_tod=? where pid='cron_plugins.backup'", 86400, intval($_POST['tod'])))
				{
					sql_insert("insert into $config[tables_prefix_multi]admin_processes set exec_interval=?, exec_tod=?, pid='cron_plugins.backup', status_data='a:0:{}'", 86400, intval($_POST['tod']));
				}
				add_admin_notification('plugins.backup.automatic_backup_not_enabled', 0);
			} else
			{
				sql_delete("delete from $config[tables_prefix_multi]admin_processes where pid='cron_plugins.backup'");
				add_admin_notification('plugins.backup.automatic_backup_not_enabled', 1);
			}
			add_admin_notification('plugins.backup.automatic_backup_failed', 0);
			add_admin_notification('plugins.backup.backup_directory_not_writable', 0);

			if (!is_array($errors))
			{
				if (is_array($_POST['delete']))
				{
					$results = scandir($_POST['backup_folder']);
					if (is_array($results))
					{
						foreach ($results as $file)
						{
							if ((is_file("$_POST[backup_folder]/$file") && (strpos($file, '.zip') !== false || strpos($file, '.gz') !== false)) || (intval($file) > 0 && is_dir("$_POST[backup_folder]/$file")))
							{
								if (in_array($file, $_POST['delete']))
								{
									if (is_dir("$_POST[backup_folder]/$file"))
									{
										backup_RecurseDelete("$_POST[backup_folder]/$file");
									} elseif (time() - filemtime("$_POST[backup_folder]/$file") > 90 * 86400)
									{
										@unlink("$_POST[backup_folder]/$file");
										if ($data['remote_storage_type'] != '')
										{
											delete_file($file, '', backup_EmulateRemoteServerData($data));
										}
									}
								}
							}
						}
					}
				}
				if ($rnd > 0)
				{
					chdir("$config[project_path]/admin/include");
					exec("$config[php_path] $config[project_path]/admin/plugins/backup/backup.php manual $rnd > /dev/null 2>&1 &");
					return_ajax_success("$page_name?plugin_id=backup&action=progress&task_id=$rnd", 2);
				} else
				{
					return_ajax_success("$page_name?plugin_id=backup");
				}
			} else
			{
				@unlink("$plugin_path/task-$rnd.dat");
				return_ajax_errors($errors);
			}
		} else
		{
			@unlink("$plugin_path/task-$rnd.dat");
			return_ajax_errors($errors);
		}
	}

	$_POST = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if (intval($_POST['enable_ftp']) == 1 && !isset($_POST['remote_storage_type']))
	{
		$_POST['remote_storage_type'] = 'ftp';
	}

	$process = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_processes where pid='cron_plugins.backup'"));
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

	if (KvsUtilities::is_locked("admin/data/plugins/backup/cron"))
	{
		$_POST['is_running'] = 1;
	}

	unset($res);
	$mysqldump_path = "/usr/local/bin/mysqldump";
	if ($config['mysqldump_path'] <> '')
	{
		$mysqldump_path = $config['mysqldump_path'];
	}
	exec("$mysqldump_path 2>&1", $res);
	if (stripos(implode("\n", $res), '--help') === false)
	{
		unset($res);
		$mysqldump_path = "/usr/local/bin/mysqldump";
		exec("$mysqldump_path 2>&1", $res);
		if (stripos(implode("\n", $res), '--help') === false)
		{
			unset($res);
			$mysqldump_path = "/usr/bin/mysqldump";
			exec("$mysqldump_path 2>&1", $res);
			if (stripos(implode("\n", $res), '--help') === false)
			{
				$_POST['errors'][] = str_replace("%1%", $config['mysqldump_path'], $lang['plugins']['backup']['error_mysqldump_command']);
				$_POST['has_mysqldump_error'] = 1;
			}
		}
	}

	if ($_POST['backup_folder'] != '' && is_dir($_POST['backup_folder']))
	{
		$results = scandir($_POST['backup_folder']);
		$results_time = [];
		$results_values = [];
		if (is_array($results))
		{
			foreach ($results as $file)
			{
				if (is_file("$_POST[backup_folder]/$file") && (strpos($file, '.zip') !== false || strpos($file, '.gz') !== false))
				{
					$results_time[] = filemtime("$_POST[backup_folder]/$file");
					$results_values[] = $file;
				}
				if (intval($file) > 0 && is_dir("$_POST[backup_folder]/$file"))
				{
					$results_time[] = filemtime("$_POST[backup_folder]/$file");
					$results_values[] = $file;
				}
			}
		}
		array_multisort($results_time, SORT_NUMERIC, SORT_DESC, $results_values);
		$results = [];
		$summary_size = 0;
		foreach ($results_values as $file)
		{
			$item = [];
			$item['filename'] = $file;
			$item['filedate'] = filemtime("$_POST[backup_folder]/$file");
			if (time() - $item['filedate'] > 90 * 86400)
			{
				$item['is_deletable'] = 1;
			}
			if (is_dir("$_POST[backup_folder]/$file"))
			{
				$item['filesize'] = 'DIR';
				$item['is_deletable'] = 1;
			} else
			{
				$filesize = @sprintf("%.0f", filesize("$_POST[backup_folder]/$file"));
				$summary_size += floatval($filesize);
				$item['filesize'] = sizeToHumanString($filesize, 2);
			}
			if ($_SESSION['userdata']['is_superadmin'] > 0)
			{
				$item['url'] = "$page_name?plugin_id=backup&action=download&file=" .urlencode($file);
			}
			$item['contents'] = [];

			preg_match("|backup-([dwepsc]+)-.*|is", $file, $temp);
			if (is_dir("$_POST[backup_folder]/$file"))
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_junk'];
			}
			if (strpos($temp[1], 'd') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_mysql'];
			}
			if (strpos($temp[1], 'w') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_website'];
			}
			if (strpos($temp[1], 'p') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_player'];
			}
			if (strpos($temp[1], 's') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_kvs'];
			}
			if (strpos($temp[1], 'c') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_content_auxiliary'];
			}
			preg_match("|backup-auto-([dwepsc]+)-.*|is", $file, $temp);
			if (strpos($temp[1], 'd') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_mysql'];
			}
			if (strpos($temp[1], 'w') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_website'];
			}
			if (strpos($temp[1], 'p') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_player'];
			}
			if (strpos($temp[1], 's') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_kvs'];
			}
			if (strpos($temp[1], 'c') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_content_auxiliary'];
			}

			$results[] = $item;
		}
		$_POST['backups'] = $results;
		$_POST['backups_summary_size'] = sizeToHumanString($summary_size, 2);

		if (!is_writable("$_POST[backup_folder]"))
		{
			$_POST['errors'][] = get_aa_error('filesystem_permission_write', $_POST['backup_folder']);
		}
	}

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	} else
	{
		exec("find $plugin_path \( -iname \"*.dat\" ! -iname \"data.dat\" \) -mtime +6 -delete");
	}
	if ($_GET['error'] == 1)
	{
		$_POST['errors'][] = $lang['plugins']['backup']['error_folder_permissions'];
	}
	if ($_GET['error'] == 2)
	{
		$_POST['errors'][] = $lang['plugins']['backup']['error_failed'];
	}

	$_POST['open_basedir'] = trim(@ini_get('open_basedir'));

	if (isset($_SESSION['admin_notifications']['list']['plugins.backup.automatic_backup_not_enabled']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['plugins.backup.automatic_backup_not_enabled']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['plugins.backup.backup_directory_not_writable']['title']))
	{
		$_POST['errors'][] = $_SESSION['admin_notifications']['list']['plugins.backup.backup_directory_not_writable']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['plugins.backup.automatic_backup_failed']['title']))
	{
		$_POST['errors'][] = $_SESSION['admin_notifications']['list']['plugins.backup.automatic_backup_failed']['title'];
	}
}

function backupCron()
{
	global $config;

	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions_admin.php';
	require_once 'functions.php';

	ini_set('display_errors', 1);

	$start_time = time();

	$plugin_path = "$config[project_path]/admin/data/plugins/backup";
	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));

	$memory_limit = mr2number(sql("select value from $config[tables_prefix]options where variable='LIMIT_MEMORY'"));
	if ($memory_limit == 0)
	{
		$memory_limit = 512;
	}
	ini_set('memory_limit', "{$memory_limit}M");

	backupLog('INFO  Starting backup plugin');
	backupLog('INFO  Memory limit: ' . ini_get('memory_limit'));

	if ($data['backup_folder'] == '')
	{
		backupLog("ERROR No backup folder is configured");
		add_admin_notification('plugins.backup.automatic_backup_failed', 1);
		return;
	}
	if (!is_writable($data['backup_folder']))
	{
		backupLog("ERROR Backup folder is not writable: $data[backup_folder]");
		add_admin_notification('plugins.backup.backup_directory_not_writable', 1);
		return;
	}

	$rnd = mt_rand(10000000, 99999999);

	$current_date = time();

	$results = scandir($data['backup_folder']);
	foreach ($results as $file)
	{
		if (strpos($file, 'backup-auto-') !== false)
		{
			$created_time = filemtime("$data[backup_folder]/$file");
			if ($data['auto_backup_monthly'] == 1)
			{
				$date_info = getdate($created_time);
				if ($date_info['mday'] == 1 && time() - $created_time < 86400 * 365)
				{
					backupLog("INFO  Backup is kept as monthly backup: $file");
					continue;
				}
			}
			if ($data['auto_backup_weekly'] == 1)
			{
				$date_info = getdate($created_time);
				if ($date_info['wday'] == 1 && time() - $created_time < 86400 * 30)
				{
					backupLog("INFO  Backup is kept as weekly backup: $file");
					continue;
				}
			}
			if ($data['auto_backup_daily'] == 1)
			{
				if (time() - $created_time < 86400 * 7)
				{
					backupLog("INFO  Backup is kept as daily backup: $file");
					continue;
				}
			}

			if (unlink("$data[backup_folder]/$file"))
			{
				backupLog("INFO  Backup is deleted: $file");
			} else
			{
				backupLog("WARN  Backup failed to be deleted: $file");
			}

			if ($data['remote_storage_type'] != '')
			{
				delete_file($file, '', backup_EmulateRemoteServerData($data));
			}
		}
	}

	$create_backup = 0;
	if ($data['auto_backup_daily'] == 1)
	{
		$create_backup = 1;
	} elseif ($data['auto_backup_weekly'] == 1)
	{
		$date_info = getdate($current_date);
		if ($date_info['wday'] == 1)
		{
			$create_backup = 1;
		}
	} elseif ($data['auto_backup_monthly'] == 1)
	{
		$date_info = getdate($current_date);
		if ($date_info['mday'] == 1)
		{
			$create_backup = 1;
		}
	}

	if ($create_backup == 1)
	{
		$remote_server_data = null;
		if ($data['remote_storage_type'] != '')
		{
			$remote_server_data = backup_EmulateRemoteServerData($data);
		}
		$result = backup_DoBackup($data['backup_folder'], $rnd, intval($data['auto_skip_database']) == 1 ? 0 : ($config['is_clone_db'] == 'true' ? 0 : 1), 1, 1, 1, intval($data['auto_skip_content_auxiliary']) == 1 ? 0 : ($config['is_clone_db'] == 'true' ? 0 : 1), 1, $remote_server_data);
		add_admin_notification('plugins.backup.automatic_backup_failed', $result != '' ? 1 : 0);
	}

	@unlink("$plugin_path/task-progress-$rnd.dat");

	sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_plugins.backup'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));
}

function backup_RecurseCopy($src, $dst, $public_permissions, $excludes = [])
{
	if (!is_dir($src))
	{
		return;
	}
	$dir = opendir($src);
	if (!is_dir($dst))
	{
		mkdir_recursive($dst, 0755);
		chmod($dst, $public_permissions ? 0777 : 0755);
	}
	while ($dir && false !== ($file = readdir($dir)))
	{
		if ($file <> '.' && $file <> '..')
		{
			if (is_dir("$src/$file"))
			{
				if (!in_array($file, $excludes))
				{
					backup_RecurseCopy("$src/$file", "$dst/$file", $public_permissions, $excludes);
				}
			} else
			{
				if (!in_array($file, $excludes))
				{
					copy("$src/$file", "$dst/$file");
					if ($file == '.htaccess')
					{
						chmod("$dst/$file", 0644);
					} else
					{
						chmod("$dst/$file", $public_permissions ? 0666 : 0644);
					}
				}
			}
		}
	}
	closedir($dir);
}

function backup_CopyFilesOnly($src, $dst, $public_permissions, $limit_size = 0, $excludes = [])
{
	if (!is_dir($src))
	{
		return;
	}
	$dir = opendir($src);
	if (!is_dir($dst))
	{
		mkdir_recursive($dst, 0755);
		chmod($dst, $public_permissions ? 0777 : 0755);
	}
	while ($dir && false !== ($file = readdir($dir)))
	{
		if ($file <> '.' && $file <> '..')
		{
			if (is_file("$src/$file") && !in_array($file, $excludes))
			{
				if (intval($limit_size) == 0 || intval($limit_size) > sprintf("%.0f", filesize("$src/$file")))
				{
					copy("$src/$file", "$dst/$file");
					chmod("$dst/$file", $public_permissions ? 0666 : 0644);
				}
			}
		}
	}
	closedir($dir);
}

function backup_RecurseDelete($src)
{
	if (!is_dir($src))
	{
		return;
	}
	$dir = opendir($src);
	while ($dir && false !== ($file = readdir($dir)))
	{
		if ($file <> '.' && $file <> '..')
		{
			if (is_dir("$src/$file"))
			{
				backup_RecurseDelete("$src/$file");
			} else
			{
				@unlink("$src/$file");
			}
		}
	}
	closedir($dir);
	@rmdir("$src");
}

function backup_EmulateRemoteServerData($data)
{
	if ($data['remote_storage_type'] == 's3')
	{
		return [
				'server_id' => 100000000,
				'is_debug_enabled' => 0,
				'connection_type_id' => 3,
				's3_region' => $data['s3_region'],
				's3_endpoint' => $data['s3_endpoint'],
				's3_bucket' => $data['s3_bucket'],
				's3_prefix' => $data['s3_prefix'],
				's3_api_key' => $data['s3_api_key'],
				's3_api_secret' => $data['s3_api_secret'],
				's3_upload_chunk_size_mb' => $data['s3_upload_chunk_size_mb'],
				's3_is_endpoint_subdirectory' => $data['s3_is_endpoint_subdirectory'],
		];
	} else
	{
		return [
				'server_id' => 100000000,
				'is_debug_enabled' => 0,
				'connection_type_id' => 2,
				'ftp_host' => $data['ftp_host'],
				'ftp_port' => $data['ftp_port'],
				'ftp_timeout' => $data['ftp_timeout'],
				'ftp_user' => $data['ftp_user'],
				'ftp_pass' => $data['ftp_pass'],
				'ftp_folder' => $data['ftp_folder'],
		];
	}
}

function backup_DoBackup($backup_root, $task_id, $is_mysql, $is_website, $is_player, $is_kvs, $is_content, $is_auto, $remote_server_data = null)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/backup";

	$backup_folder = "$backup_root/$task_id";
	if (!mkdir($backup_folder, 0755))
	{
		backupLog("ERROR Failed to create backup temp folder");
		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 0, 'error_code' => 1]), LOCK_EX);
		return 'error1';
	}

	$total_amount_of_work = $is_mysql + $is_website + $is_player + $is_kvs + $is_content + 1;
	if ($remote_server_data)
	{
		$total_amount_of_work++;
	}
	$done_amount_of_work = 0;
	$pc = 0;

	$backup_type_part = '';
	if ($is_mysql == 1)
	{
		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $pc, 'message_id' => 'message_creating_database_backup']), LOCK_EX);

		mkdir("$backup_folder/mysql", 0755);

		unset($res);
		$mysqldump_path = "/usr/local/bin/mysqldump";
		if ($config['mysqldump_path'] <> '')
		{
			$mysqldump_path = $config['mysqldump_path'];
		}
		exec("$mysqldump_path 2>&1", $res);
		if (stripos(implode("\n", $res), '--help') === false)
		{
			unset($res);
			$mysqldump_path = "/usr/local/bin/mysqldump";
			exec("$mysqldump_path 2>&1", $res);
			if (stripos(implode("\n", $res), '--help') === false)
			{
				unset($res);
				$mysqldump_path = "/usr/bin/mysqldump";
				exec("$mysqldump_path 2>&1", $res);
				if (stripos(implode("\n", $res), '--help') === false)
				{
					$mysqldump_path = '';
				}
			}
		}

		backupLog("INFO  Using mysqldump command: $mysqldump_path");
		if ($mysqldump_path != '')
		{
			require_once "$config[project_path]/admin/include/setup_db.php";
			exec("$mysqldump_path --default-character-set=utf8 --user=" . DB_LOGIN . " --password='" . DB_PASS . "' --host=" . DB_HOST . " " . DB_DEVICE . " > $backup_folder/mysql/backup.sql");
			if (filesize("$backup_folder/mysql/backup.sql") < 10)
			{
				backupLog("ERROR MySQL backup failed");
			} else
			{
				backupLog("INFO  MySQL backup done");
				$backup_type_part .= 'd';
			}

			$done_amount_of_work++;
		}
	}

	$system_root_files = [
		"get_file.php",
		"get_image.php",
		"logout.php",
		"redirect_cs.php",
		"redirect_random_album.php",
		"redirect_random_video.php",
		"kvs_out.php"
	];

	$engine_customization_files = [
		"pre_process_page_code.php",
		"pre_display_page_code.php",
		"pre_initialize_page_code.php",
		"post_process_page_code.php",
		"pre_async_action_code.php"
	];

	$content_relative_paths = [];
	foreach (['avatars', 'categories', 'content_sources', 'models', 'dvds', 'posts', 'referers', 'other'] as $path_group)
	{
		if (strpos($config["content_path_{$path_group}"], $config['project_path']) === 0)
		{
			$content_relative_paths[$config["content_path_{$path_group}"]] = trim(str_replace($config['project_path'], '', $config["content_path_{$path_group}"]), ' /');
		} else
		{
			$content_relative_paths[$config["content_path_{$path_group}"]] = "contents/$path_group";
		}
	}

	if ($is_website == 1)
	{
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $pc, 'message_id' => 'message_creating_website_backup']), LOCK_EX);

		mkdir("$backup_folder/website", 0755);

		mkdir_recursive("$backup_folder/website/admin/include", 0755);
		foreach ($engine_customization_files as $engine_customization_file)
		{
			copy("$config[project_path]/admin/include/$engine_customization_file", "$backup_folder/website/admin/include/$engine_customization_file");
			chmod("$backup_folder/website/admin/include/$engine_customization_file", 0644);
		}

		backup_RecurseCopy("$config[project_path]/template", "$backup_folder/website/template", true);
		backup_RecurseCopy("$config[project_path]/admin/data/advertisements", "$backup_folder/website/admin/data/advertisements", true);
		backup_RecurseCopy("$config[project_path]/admin/data/config", "$backup_folder/website/admin/data/config", true);
		backup_CopyFilesOnly("$config[project_path]", "$backup_folder/website", false, 1 * 1024 * 1024, $system_root_files);

		mkdir_recursive("$backup_folder/website/admin/data/system");
		copy("$config[project_path]/admin/data/system/website_ui_params.dat", "$backup_folder/website/admin/data/system/website_ui_params.dat");
		chmod("$backup_folder/website/admin/data/system/website_ui_params.dat", 0666);
		copy("$config[project_path]/admin/data/system/runtime_params.dat", "$backup_folder/website/admin/data/system/runtime_params.dat");
		chmod("$backup_folder/website/admin/data/system/runtime_params.dat", 0666);
		copy("$config[project_path]/admin/data/system/blocked_words.dat", "$backup_folder/website/admin/data/system/blocked_words.dat");
		chmod("$backup_folder/website/admin/data/system/blocked_words.dat", 0666);

		if (is_dir("$config[project_path]/langs"))
		{
			backup_RecurseCopy("$config[project_path]/langs", "$backup_folder/website/langs", false);
		}
		if (is_dir("$config[project_path]/js"))
		{
			backup_RecurseCopy("$config[project_path]/js", "$backup_folder/website/js", false);
		}
		if (is_dir("$config[project_path]/styles"))
		{
			backup_RecurseCopy("$config[project_path]/styles", "$backup_folder/website/styles", false);
		}
		if (is_dir("$config[project_path]/images"))
		{
			backup_RecurseCopy("$config[project_path]/images", "$backup_folder/website/images", false);
		}
		if (is_dir("$config[project_path]/img"))
		{
			backup_RecurseCopy("$config[project_path]/img", "$backup_folder/website/img", false);
		}
		if (is_dir("$config[project_path]/css"))
		{
			backup_RecurseCopy("$config[project_path]/css", "$backup_folder/website/css", false);
		}
		if (is_dir("$config[project_path]/fonts"))
		{
			backup_RecurseCopy("$config[project_path]/fonts", "$backup_folder/website/fonts", false);
		}
		if (is_dir("$config[project_path]/static"))
		{
			backup_RecurseCopy("$config[project_path]/static", "$backup_folder/website/static", false);
		}
		if (is_dir("$config[content_path_other]/theme"))
		{
			backup_RecurseCopy("$config[content_path_other]/theme", "$backup_folder/website/{$content_relative_paths[$config['content_path_other']]}/theme", true);
		}

		$options = get_options([
				'CATEGORY_AVATAR_SIZE',
				'CATEGORY_AVATAR_TYPE',
				'CATEGORY_AVATAR_2_SIZE',
				'CATEGORY_AVATAR_2_TYPE',
				'CATEGORY_AVATAR_OPTION',
				'USER_AVATAR_SIZE',
				'USER_AVATAR_TYPE',
				'USER_COVER_SIZE',
				'USER_COVER_TYPE',
				'USER_COVER_OPTION',
				'MODELS_SCREENSHOT_1_SIZE',
				'MODELS_SCREENSHOT_1_TYPE',
				'MODELS_SCREENSHOT_2_SIZE',
				'MODELS_SCREENSHOT_2_TYPE',
				'MODELS_SCREENSHOT_OPTION',
				'CS_SCREENSHOT_1_SIZE',
				'CS_SCREENSHOT_1_TYPE',
				'CS_SCREENSHOT_2_SIZE',
				'CS_SCREENSHOT_2_TYPE',
				'CS_SCREENSHOT_OPTION',
				'DVD_COVER_1_SIZE',
				'DVD_COVER_1_TYPE',
				'DVD_COVER_2_SIZE',
				'DVD_COVER_2_TYPE',
				'DVD_COVER_OPTION',
				'DVD_GROUP_COVER_1_SIZE',
				'DVD_GROUP_COVER_1_TYPE',
				'DVD_GROUP_COVER_2_SIZE',
				'DVD_GROUP_COVER_2_TYPE',
				'DVD_GROUP_COVER_OPTION',
		]);
		file_put_contents("$backup_folder/website/admin/data/config/options.json", json_encode($options));

		backupLog("INFO  Website backup done");

		$done_amount_of_work++;
		$backup_type_part .= 'w';
	}

	if ($is_player == 1)
	{
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $pc, 'message_id' => 'message_creating_player_backup']), LOCK_EX);

		mkdir("$backup_folder/player", 0755);

		backup_RecurseCopy("$config[project_path]/admin/data/player", "$backup_folder/player/admin/data/player", true);
		backup_RecurseCopy("$config[content_path_other]/player", "$backup_folder/player/{$content_relative_paths[$config['content_path_other']]}/player", true);

		backupLog("INFO  Player backup done");

		$done_amount_of_work++;
		$backup_type_part .= 'p';
	}

	if ($is_kvs == 1)
	{
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $pc, 'message_id' => 'message_creating_kvs_backup']), LOCK_EX);

		mkdir("$backup_folder/kvs", 0755);

		backup_CopyFilesOnly("$config[project_path]/admin", "$backup_folder/kvs/admin", false);
		backup_RecurseCopy("$config[project_path]/admin/api", "$backup_folder/kvs/admin/api", false);
		backup_RecurseCopy("$config[project_path]/admin/async", "$backup_folder/kvs/admin/async", false);
		backup_RecurseCopy("$config[project_path]/admin/billings", "$backup_folder/kvs/admin/billings", false);
		if (is_dir("$config[project_path]/admin/cdn"))
		{
			backup_RecurseCopy("$config[project_path]/admin/cdn", "$backup_folder/kvs/admin/cdn", false);
		}
		backup_RecurseCopy("$config[project_path]/admin/docs", "$backup_folder/kvs/admin/docs", false);
		backup_RecurseCopy("$config[project_path]/admin/feeds", "$backup_folder/kvs/admin/feeds", false);
		backup_RecurseCopy("$config[project_path]/admin/include", "$backup_folder/kvs/admin/include", false, $engine_customization_files);
		backup_RecurseCopy("$config[project_path]/admin/js", "$backup_folder/kvs/admin/js", false);
		backup_RecurseCopy("$config[project_path]/admin/langs", "$backup_folder/kvs/admin/langs", false);
		backup_RecurseCopy("$config[project_path]/admin/plugins", "$backup_folder/kvs/admin/plugins", false);
		backup_CopyFilesOnly("$config[project_path]/admin/smarty", "$backup_folder/kvs/admin/smarty", false);
		backup_RecurseCopy("$config[project_path]/admin/smarty/internals", "$backup_folder/kvs/admin/smarty/internals", false);
		backup_RecurseCopy("$config[project_path]/admin/smarty/plugins", "$backup_folder/kvs/admin/smarty/plugins", false);
		backup_RecurseCopy("$config[project_path]/admin/stamp", "$backup_folder/kvs/admin/stamp", false);
		backup_RecurseCopy("$config[project_path]/admin/styles", "$backup_folder/kvs/admin/styles", false);
		backup_RecurseCopy("$config[project_path]/admin/template", "$backup_folder/kvs/admin/template", false);
		backup_RecurseCopy("$config[project_path]/admin/tinymce", "$backup_folder/kvs/admin/tinymce", false);
		backup_RecurseCopy("$config[project_path]/admin/tools", "$backup_folder/kvs/admin/tools", false);
		backup_RecurseCopy("$config[project_path]/blocks", "$backup_folder/kvs/blocks", false);
		backup_RecurseCopy("$config[project_path]/player", "$backup_folder/kvs/player", false);

		foreach ($system_root_files as $system_root_file)
		{
			if (is_file("$config[project_path]/$system_root_file"))
			{
				copy("$config[project_path]/$system_root_file", "$backup_folder/kvs/$system_root_file");
				chmod("$backup_folder/kvs/$system_root_file", 0644);
			}
		}

		backupLog("INFO  System files backup done");

		$done_amount_of_work++;
		$backup_type_part .= 's';
	}

	if ($is_content == 1)
	{
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $pc, 'message_id' => 'message_creating_content_backup']), LOCK_EX);

		mkdir("$backup_folder/content", 0755);

		backup_RecurseCopy("$config[content_path_avatars]", "$backup_folder/content/{$content_relative_paths[$config['content_path_avatars']]}", true);
		backup_RecurseCopy("$config[content_path_categories]", "$backup_folder/content/{$content_relative_paths[$config['content_path_categories']]}", true);
		backup_RecurseCopy("$config[content_path_content_sources]", "$backup_folder/content/{$content_relative_paths[$config['content_path_content_sources']]}", true);
		backup_RecurseCopy("$config[content_path_models]", "$backup_folder/content/{$content_relative_paths[$config['content_path_models']]}", true);
		backup_RecurseCopy("$config[content_path_dvds]", "$backup_folder/content/{$content_relative_paths[$config['content_path_dvds']]}", true);
		backup_RecurseCopy("$config[content_path_posts]", "$backup_folder/content/{$content_relative_paths[$config['content_path_posts']]}", true);
		backup_RecurseCopy("$config[content_path_referers]", "$backup_folder/content/{$content_relative_paths[$config['content_path_referers']]}", true);
		backup_RecurseCopy("$config[content_path_other]", "$backup_folder/content/{$content_relative_paths[$config['content_path_other']]}", true, ['player', 'theme']);

		backupLog("INFO  Content backup done");

		$done_amount_of_work++;
		$backup_type_part .= 'c';
	}

	if ($backup_type_part <> '')
	{
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $pc, 'message_id' => 'message_creating_zip']), LOCK_EX);

		copy("$config[project_path]/admin/plugins/backup/langs/readme.txt", "$backup_folder/readme.txt");
		$random_part = md5(strtolower(generate_password()) . $config['installation_id'] . md5_file("$config[project_path]/admin/include/setup.php"));
		$now_date = date("Y-m-d-His");

		$domain_in_filename = str_replace(['.'], '-', $config['project_licence_domain']);
		$backup_filename = "$domain_in_filename-backup-$backup_type_part-$now_date-$random_part.tar.gz";
		if ($is_auto == 1)
		{
			$backup_filename = "$domain_in_filename-backup-auto-$backup_type_part-$now_date-$random_part.tar.gz";
		}
		$backup_filepath = "$backup_root/$backup_filename";
		exec("tar -c -z -f $backup_filepath -C $backup_folder kvs mysql website player content readme.txt");

		backup_RecurseDelete("$backup_folder/kvs");
		backup_RecurseDelete("$backup_folder/mysql");
		backup_RecurseDelete("$backup_folder/website");
		backup_RecurseDelete("$backup_folder/player");
		backup_RecurseDelete("$backup_folder/content");
		rmdir_recursive($backup_folder);
		$done_amount_of_work++;

		if (!is_file($backup_filepath))
		{
			backupLog("ERROR Failed to create backup archive");
			file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 0, 'error_code' => 2]), LOCK_EX);
			return 'error2';
		}

		if (is_array($remote_server_data))
		{
			$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $pc, 'message_id' => 'message_uploading_remote']), LOCK_EX);
			require_once "$config[project_path]/admin/include/functions_servers.php";
			if (!put_file($backup_filename, $backup_root, '', $remote_server_data))
			{
				backupLog("ERROR Failed to push backup archive to the provided FTP");
				file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 0, 'error_code' => 2]), LOCK_EX);
				return 'error2';
			}
		}
	}

	backupLog("INFO  Backup finished");

	@unlink("$plugin_path/task-$task_id.dat");
	file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 100]), LOCK_EX);

	return '';
}

function backupLog($message)
{
	echo date("[Y-m-d H:i:s] ") . $message . "\n";
}

if ($_SERVER['argv'][1] == 'manual' && intval($_SERVER['argv'][2]) > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';

	$task_id = intval($_SERVER['argv'][2]);
	$plugin_path = "$config[project_path]/admin/data/plugins/backup";

	$data = @unserialize(file_get_contents("$plugin_path/task-$task_id.dat"));

	$remote_server_data = null;
	if ($data['remote_storage_type'] != '')
	{
		$remote_server_data = backup_EmulateRemoteServerData($data);
	}

	backup_DoBackup($data['backup_folder'], $task_id, $data['backup_mysql'], $data['backup_website'], $data['backup_player'], $data['backup_kvs'], $data['backup_content_auxiliary'], 0, $remote_server_data);
	die;
} elseif ($_SERVER['argv'][1] == 'cron' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';

	KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
	if (!KvsUtilities::try_exclusive_lock('admin/data/plugins/backup/cron'))
	{
		die('Already locked');
	}

	backupCron();
	die;
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
