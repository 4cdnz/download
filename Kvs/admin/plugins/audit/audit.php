<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function auditInit()
{
	global $config;

	mkdir_recursive("$config[project_path]/admin/data/plugins/audit");
}

function auditCompareMessages($a, $b)
{
	if (!is_array($a) || !is_array($b) || !$a['message_type'] || !$b['message_type'])
	{
		return 0;
	}
	if ($a['message_type'] == $b['message_type'])
	{
		return (trim($a['resource']) < trim($b['resource'])) ? -1 : 1;
	}

	return ($a['message_type'] < $b['message_type']) ? -1 : 1;
}

function auditShow()
{
	global $config,$errors,$page_name,$lang;

	$infos=array(7,36,37,42,73,302,702,710,715,724);
	$warnings=array(5,23,25,31,32,41,61,71,300,301,303,304,305,306,600,601,602,603,604,605,606,607,701,703,713,714,717,718,722,723,725,731);

	auditInit();
	$plugin_path="$config[project_path]/admin/data/plugins/audit";

	$errors = null;

	if ($_GET['action'] == 'progress')
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
					$json_response['url'] = "plugins.php?plugin_id=audit&action=display_result&task_id=$task_id";
					$json_response['redirect'] = true;
					@unlink("$plugin_path/task-progress-$task_id.dat");
				}
			}
			if (isset($json['message']))
			{
				$json_response['message'] = $json['message'];
			} elseif (isset($json['message_id']))
			{
				$json_response['message'] = $lang['plugins']['audit'][$json['message_id']];
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
	} elseif ($_GET['action']=='log')
	{
		$task_id = intval($_GET['task_id']);
		download_log_file("$plugin_path/task-log-$task_id.dat");
		die;
	} elseif ($_GET['action']=='file')
	{
		header('Content-Type: text/plain; charset=utf-8');
		$task_id=intval($_GET['task_id']);
		$file_path=trim($_GET['file_path']);
		if (strpos($file_path, '..'))
		{
			die;
		}

		if ($_SESSION['userdata']['is_superadmin']==1 || $_SESSION['userdata']['is_superadmin']==2)
		{
			if (is_file("$plugin_path/task-$task_id.dat"))
			{
				$data=@unserialize(@file_get_contents("$plugin_path/task-$task_id.dat"));
				foreach ($data['audit_messages'] as $message)
				{
					if ($message['resource_path']==$file_path)
					{
						$filename = basename($file_path);
						if (isset($_REQUEST['download']))
						{
							header("Content-Disposition: attachment; filename=\"$filename\"");
						} else
						{
							header("Content-Disposition: inline; filename=\"$filename\"");
						}
						echo @file_get_contents("$config[project_path]$message[resource_path]");
						break;
					}
				}
			}
		}
		die;
	} elseif ($_GET['action'] == 'obsolete_clean')
	{
		@unlink("$config[project_path]/admin/data/engine/checks/audit1017.dat");
	} elseif ($_POST['action']=='start_audit')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		$rnd=mt_rand(10000000,99999999);

		$data=array();
		$data['check_installation']=intval($_POST['check_installation']);
		$data['check_database']=intval($_POST['check_database']);
		$data['check_formats']=intval($_POST['check_formats']);
		$data['check_servers']=intval($_POST['check_servers']);
		$data['check_website_ui']=intval($_POST['check_website_ui']);
		$data['check_video_content']=intval($_POST['check_video_content']);
		$data['check_album_content']=intval($_POST['check_album_content']);
		$data['check_auxiliary_content']=intval($_POST['check_auxiliary_content']);
		$data['check_content_protection']=intval($_POST['check_content_protection']);
		$data['check_security']=intval($_POST['check_security']);
		$data['check_video_stream']=intval($_POST['check_video_stream']);
		$data['check_video_embed']=intval($_POST['check_video_embed']);
		$data['video_id_range_from']=$_POST['video_id_range_from'];
		$data['video_id_range_to']=$_POST['video_id_range_to'];
		$data['album_id_range_from']=$_POST['album_id_range_from'];
		$data['album_id_range_to']=$_POST['album_id_range_to'];
		$data['php_file_uploads']=trim(ini_get('file_uploads'));
		$data['php_allow_url_fopen']=trim(ini_get('allow_url_fopen'));
		$data['php_open_basedir']=trim(ini_get('open_basedir'));
		$data['http_host']=str_replace('www.', '', $_SERVER['HTTP_HOST']);
		file_put_contents("$plugin_path/task-$rnd.dat",serialize($data),LOCK_EX);

		if (!is_file("$plugin_path/task-$rnd.dat"))
		{
			$errors[]=get_aa_error('filesystem_permission_write',"$plugin_path/task-$rnd.dat");
		}

		if (!is_array($errors))
		{
			if (is_writable("$plugin_path"))
			{
				exec("find $plugin_path -name '*.tmp' -mtime +30 -delete");
				exec("find $plugin_path -name '*.dat' -mtime +30 -delete");
				exec("find $plugin_path -name '*.jpg' -mtime +30 -delete");
			}
			exec("$config[php_path] $config[project_path]/admin/plugins/audit/audit.php $rnd {$_SESSION['userdata']['user_id']} > $plugin_path/task-log-$rnd.dat 2>&1 &");
			return_ajax_success("$page_name?plugin_id=audit&action=progress&task_id=$rnd",2);
		} else {
			return_ajax_errors($errors);
		}
	}

	$results=scandir($plugin_path);
	$results_time=array();
	$results_values=array();
	foreach ($results as $file)
	{
		if (is_file("$plugin_path/$file") && strpos($file,'task-')===0 && strpos($file,'task-progress')===false && strpos($file,'task-log')===false)
		{
			if (time()-filectime("$plugin_path/$file")<30*24*3600)
			{
				$results_time[]=filectime("$plugin_path/$file");
				$results_values[]=$file;
			}
		}
	}
	array_multisort($results_time,SORT_NUMERIC,SORT_DESC,$results_values);

	$task_id = 0;
	if ($_GET['action'] == 'display_result')
	{
		$task_id = intval($_GET['task_id']);
	} elseif (array_cnt($results_values) > 0)
	{
		$task_id = intval(substr($results_values[0], 5, strpos($results_values[0], '.') - 5));
	}

	if ($task_id > 0)
	{
		$_POST = @unserialize(file_get_contents("$plugin_path/task-$task_id.dat"));
		$_POST['audit_time'] = @filectime("$plugin_path/task-$task_id.dat");
		$_POST['task_id'] = $task_id;
		$audit_errors = [];
		$audit_warnings = [];
		$audit_infos = [];
		if (isset($_POST['audit_messages']))
		{
			foreach ($_POST['audit_messages'] as $message)
			{
				if (in_array($message['message_type'], $warnings))
				{
					$message['is_warning'] = 1;
					$audit_warnings[] = $message;
				} elseif (in_array($message['message_type'], $infos))
				{
					$message['is_info'] = 1;
					$audit_infos[] = $message;
				} else
				{
					$message['is_error'] = 1;
					$audit_errors[] = $message;
				}
			}
			$_POST['has_finished'] = 1;
		}

		usort($audit_errors, 'auditCompareMessages');
		usort($audit_warnings, 'auditCompareMessages');
		usort($audit_infos, 'auditCompareMessages');

		$_POST['is_displayed'] = 1;
		$_POST['errors_count'] = array_cnt($audit_errors);
		$_POST['warnings_count'] = array_cnt($audit_warnings);
		$_POST['infos_count'] = array_cnt($audit_infos);
		$_POST['audit_messages'] = array_merge($audit_errors, $audit_warnings, $audit_infos);
	}

	unset($processes);
	exec("ps -ax", $processes);

	$results = [];
	foreach ($results_values as $file)
	{
		$check_result = @unserialize(@file_get_contents("$plugin_path/$file"));
		$key = substr($file, 5, strpos($file, '.') - 5);

		$results[$file] = [];
		$results[$file]['time'] = filectime("$plugin_path/$file");
		$results[$file]['errors_count'] = 0;
		$results[$file]['warnings_count'] = 0;
		$results[$file]['infos_count'] = 0;
		if (isset($check_result['audit_messages']))
		{
			foreach ($check_result['audit_messages'] as $message)
			{
				if (in_array($message['message_type'], $warnings))
				{
					$results[$file]['warnings_count']++;
				} elseif (in_array($message['message_type'], $infos))
				{
					$results[$file]['infos_count']++;
				} else
				{
					$results[$file]['errors_count']++;
				}
			}
			$results[$file]['has_finished'] = 1;
		} else
		{
			foreach ($processes as $process)
			{
				if (strpos($process, "audit.php $key") !== false)
				{
					$results[$file]['has_process'] = 1;
					break;
				}
			}
		}
		$results[$file]['key'] = $key;
		if (is_file("$plugin_path/task-progress-$key.dat"))
		{
			$json = @json_decode(file_get_contents("$plugin_path/task-progress-$key.dat"), true);
			if (isset($json['percent']))
			{
				$results[$file]['process'] = min($json['percent'] + 1, 100);
			}
		}
		if ($task_id > 0 && $file == "task-$task_id.dat")
		{
			$results[$file]['is_displayed'] = 1;
		}
	}
	$_POST['recent_audits'] = $results;

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	}
}

function auditCheckWritable($file,$check_file_existence=false)
{
	global $config,$audit_messages;

	if ($check_file_existence)
	{
		if (!is_file($file) && !is_dir($file))
		{
			return;
		}
	}
	if (!is_writable($file))
	{
		$audit_messages[]=array('message_type'=>10,'resource'=>str_replace($config['project_path'],'[kt|b]/%ROOT%[/kt|b]',$file));
	} elseif (is_dir($file) && !is_executable($file))
	{
		$audit_messages[]=array('message_type'=>10,'resource'=>str_replace($config['project_path'],'[kt|b]/%ROOT%[/kt|b]',$file));
	} elseif (is_dir($file))
	{
		$test_file=rtrim($file,'/')."/kvs_audit_test.dat";
		if (@file_put_contents($test_file,'test')!=4)
		{
			$audit_messages[]=array('message_type'=>34,'resource'=>str_replace($config['project_path'],'[kt|b]/%ROOT%[/kt|b]',$file));
		}
		@unlink($test_file);
	}
}

function auditCheckChildrenWritable($file)
{
	global $config,$audit_messages;

	$children=get_contents_from_dir($file,2);
	foreach($children as $child)
	{
		if (!is_writable($file.'/'.$child))
		{
			$audit_messages[]=array('message_type'=>10,'resource'=>str_replace($config['project_path'],'[kt|b]/%ROOT%[/kt|b]',$file.'/'.$child));
		}
	}
}

function auditCheckFileHash($file, $required_hash)
{
	global $config, $audit_messages;

	if ($required_hash == 'obsolete' || $required_hash == 'custom')
	{
		return;
	}
	if (!is_file("{$config['project_path']}$file") && !is_link("{$config['project_path']}$file"))
	{
		$audit_messages[] = array('message_type' => 2, 'resource' => "[kt|b]/%ROOT%[/kt|b]$file");
		return;
	}
	if ($required_hash == 'ignore')
	{
		return;
	}
	if (substr($file, -4) == '.ttf' || substr($file, -5) == '.woff')
	{
		if (strtoupper(md5_file("{$config['project_path']}$file")) != $required_hash)
		{
			$audit_messages[] = array('message_type' => 7, 'resource' => "[kt|b]/%ROOT%[/kt|b]$file", 'resource_path' => $file);
		}
		return;
	}

	$contents = trim(@file_get_contents("{$config['project_path']}$file"));
	$hash = strtoupper(md5(preg_replace('/[\r\n]+/', '', $contents)));
	if ($hash != $required_hash)
	{
		$audit_messages[] = array('message_type' => 7, 'resource' => "[kt|b]/%ROOT%[/kt|b]$file", 'resource_path' => $file);
	}
}

function auditCheckPathSlashes($file)
{
	global $audit_messages;

	if (substr($file,strlen($file)-1)=='/')
	{
		$audit_messages[]=array('message_type'=>17,'resource'=>"[kt|b]/%ROOT%[/kt|b]/admin/include/setup.php",'detail'=>"$file");
	} elseif (strpos($file,"//")!==false) {
		$audit_messages[]=array('message_type'=>17,'resource'=>"[kt|b]/%ROOT%[/kt|b]/admin/include/setup.php",'detail'=>"$file");
	}
}

function auditCheckSerializedFile($file)
{
	global $config,$audit_messages;

	if (is_file($file) && !is_array(unserialize(file_get_contents($file))))
	{
		$audit_messages[]=array('message_type'=>19,'resource'=>str_replace($config['project_path'],'[kt|b]/%ROOT%[/kt|b]',$file));
	}
}

function auditCheckInstallation($audit_data)
{
	global $config,$plugin_path,$audit_messages,$options,$database_tables_autoincrement;

	$db_version=$options['SYSTEM_VERSION'];
	if ($db_version<>$config['project_version'])
	{
		$audit_messages[]=array('message_type'=>1,'resource'=>'MySQL','detail'=>"Database version is $db_version; the required version is $config[project_version]");
	}

	$initial_version=$options['INITIAL_VERSION'];
	if (!$initial_version)
	{
		$audit_messages[]=array('message_type'=>9,'resource'=>'MySQL');
	}

	$sql_mode=mr2string(sql("select @@SESSION.sql_mode"));
	if ($sql_mode=='')
	{
		$sql_mode=mr2string(sql("select @@GLOBAL.sql_mode"));
	}
	if (strpos($sql_mode,'STRICT_ALL_TABLES')!==false || strpos($sql_mode,'STRICT_TRANS_TABLES')!==false)
	{
		$audit_messages[]=array('message_type'=>16,'resource'=>'MySQL');
	}

	auditCheckWritable("$config[temporary_path]");
	auditCheckWritable("$config[content_path_videos_sources]");
	auditCheckWritable("$config[content_path_videos_screenshots]");
	auditCheckWritable("$config[content_path_albums_sources]");
	auditCheckWritable("$config[content_path_categories]");
	auditCheckWritable("$config[content_path_models]");
	auditCheckWritable("$config[content_path_dvds]");
	auditCheckWritable("$config[content_path_posts]");
	auditCheckWritable("$config[content_path_avatars]");
	auditCheckWritable("$config[content_path_content_sources]");
	auditCheckWritable("$config[content_path_referers]");
	auditCheckWritable("$config[content_path_other]");
	auditCheckWritable("$config[project_path]/admin/logs");
	auditCheckWritable("$config[project_path]/admin/logs/albums");
	auditCheckWritable("$config[project_path]/admin/logs/plugins");
	auditCheckWritable("$config[project_path]/admin/logs/tasks");
	auditCheckWritable("$config[project_path]/admin/logs/videos");
	auditCheckWritable("$config[project_path]/admin/smarty/template-c");
	auditCheckWritable("$config[project_path]/admin/smarty/cache");
	auditCheckWritable("$config[project_path]/admin/smarty/template-c-site");
	auditCheckWritable("$config[project_path]/admin/data/advertisements");
	auditCheckWritable("$config[project_path]/admin/data/analysis");
	auditCheckWritable("$config[project_path]/admin/data/analysis/performance", true);
	auditCheckWritable("$config[project_path]/admin/data/engine");
	$engine_dirs=get_contents_from_dir("$config[project_path]/admin/data/engine",2);
	foreach ($engine_dirs as $engine_dir)
	{
		auditCheckWritable("$config[project_path]/admin/data/engine/$engine_dir",true);
	}
	auditCheckWritable("$config[project_path]/admin/data/other");
	auditCheckWritable("$config[project_path]/admin/data/player");
	auditCheckWritable("$config[project_path]/admin/data/player/config.dat");
	auditCheckWritable("$config[project_path]/admin/data/player/embed");
	auditCheckWritable("$config[project_path]/admin/data/player/embed/config.dat");
	auditCheckWritable("$config[project_path]/admin/data/player/embed/config.tpl", true);
	auditCheckWritable("$config[project_path]/admin/data/player/embed/error.tpl", true);
	auditCheckWritable("$config[project_path]/admin/data/player/active");
	auditCheckWritable("$config[project_path]/admin/data/player/active/config.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/player/premium");
	auditCheckWritable("$config[project_path]/admin/data/player/premium/config.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/player/vast",true);
	auditCheckWritable("$config[project_path]/admin/data/plugins");
	auditCheckWritable("$config[project_path]/admin/data/stats");
	auditCheckWritable("$config[project_path]/admin/data/stats/videos_id.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/videos_dir.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/search.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/profiles_id.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/playlists_id.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/playlists_dir.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/player.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/overload.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/models_id.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/models_dir.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/ip_data.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/ip_blocked.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/in.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/embed.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/cs_out.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/content_sources_id.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/content_sources_dir.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/albums_id.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/albums_dir.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/images_id.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/stats/adv_out.dat",true);
	auditCheckWritable("$config[project_path]/admin/data/system");
	auditCheckWritable("$config[project_path]/admin/data/system/ap.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/api.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/cluster.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/file_upload_params.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/hotlink_info.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/runtime_params.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/rotator.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/formats_albums.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/formats_videos.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/website_ui_params.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/memberzone_params.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/stats_params.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/mixed_options.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/blocked_words.dat");
	auditCheckWritable("$config[project_path]/admin/data/system/languages.dat", true);

	auditCheckChildrenWritable("$config[content_path_videos_sources]");
	auditCheckChildrenWritable("$config[content_path_videos_screenshots]");
	auditCheckChildrenWritable("$config[content_path_albums_sources]");
	auditCheckChildrenWritable("$config[content_path_posts]");
	auditCheckChildrenWritable("$config[content_path_other]");

	auditCheckPathSlashes("$config[project_path]");
	auditCheckPathSlashes("$config[temporary_path]");
	auditCheckPathSlashes("$config[content_path_videos_sources]");
	auditCheckPathSlashes("$config[content_path_videos_screenshots]");
	auditCheckPathSlashes("$config[content_path_albums_sources]");
	auditCheckPathSlashes("$config[content_path_categories]");
	auditCheckPathSlashes("$config[content_path_models]");
	auditCheckPathSlashes("$config[content_path_dvds]");
	auditCheckPathSlashes("$config[content_path_posts]");
	auditCheckPathSlashes("$config[content_path_avatars]");
	auditCheckPathSlashes("$config[content_path_content_sources]");
	auditCheckPathSlashes("$config[content_path_referers]");
	auditCheckPathSlashes("$config[content_path_other]");

	auditCheckSerializedFile("$config[project_path]/admin/data/system/api.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/cluster.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/file_upload_params.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/hotlink_info.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/runtime_params.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/rotator.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/formats_albums.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/formats_videos.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/website_ui_params.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/memberzone_params.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/stats_params.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/mixed_options.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/system/blocked_words.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/player/config.dat");
	auditCheckSerializedFile("$config[project_path]/admin/data/player/embed/config.dat");
	if (is_file("$config[project_path]/admin/data/player/active/config.dat"))
	{
		auditCheckSerializedFile("$config[project_path]/admin/data/player/active/config.dat");
	}
	if (is_file("$config[project_path]/admin/data/player/premium/config.dat"))
	{
		auditCheckSerializedFile("$config[project_path]/admin/data/player/premium/config.dat");
	}

	$cron_process_info = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_processes where pid='main'"));
	$cron_process_info['status_data'] = @unserialize($cron_process_info['status_data']) ?: [];
	$cron_process_info['last_exec_date'] = strtotime($cron_process_info['last_exec_date']);
	if ($cron_process_info['status_data']['error_type'] == 1)
	{
		$audit_messages[] = array('message_type' => 3, 'resource' => 'Cron', 'detail' => $cron_process_info['status_data']['pwd']);
	} elseif ($cron_process_info['status_data']['error_type'] == 2)
	{
		$audit_messages[] = array('message_type' => 40, 'resource' => 'Cron', 'detail' => $cron_process_info['status_data']['uid']);
	} elseif (time() - $cron_process_info['last_exec_date'] > 900)
	{
		$audit_messages[] = array('message_type' => 4, 'resource' => 'Cron');
	} elseif ($cron_process_info['status_data']['is_privileged'] == 1 && !(is_writable("$config[project_path]/admin") || is_writable("$config[project_path]/admin/include") || is_writable("$config[project_path]/admin/include/setup.php")))
	{
		$audit_messages[] = array('message_type' => 41, 'resource' => 'Cron');
	}

	$open_basedir = @trim(ini_get('open_basedir'));
	if ($open_basedir !== '' || $audit_data['php_open_basedir'] !== '')
	{
		$audit_messages[] = array('message_type' => 42, 'resource' => 'PHP', 'detail' => $audit_data['php_open_basedir'] !== '' ? $audit_data['php_open_basedir'] : $open_basedir);
	}

	if ($audit_data['php_file_uploads']==='0')
	{
		$audit_messages[]=array('message_type'=>8,'resource'=>'file_uploads');
	}
	if ($audit_data['php_allow_url_fopen']==='0')
	{
		$audit_messages[]=array('message_type'=>8,'resource'=>'allow_url_fopen');
	}

	if ($audit_data['http_host']!==$config['project_licence_domain'] && $audit_data['http_host']!=='dev.' . $config['project_licence_domain'])
	{
		$audit_messages[]=array('message_type'=>27,'resource'=>'Nginx / Apache','detail'=>$audit_data['http_host']);
	}

	$memory_limit = trim(ini_get('memory_limit'));
	if ($memory_limit != '' && $memory_limit <> '-1')
	{
		$last = strtolower($memory_limit[strlen($memory_limit) - 1]);
		switch ($last)
		{
			/** @noinspection PhpMissingBreakStatementInspection */
			case 'g':
				$memory_limit = intval($memory_limit) * 1024;
			/** @noinspection PhpMissingBreakStatementInspection */
			case 'm':
				$memory_limit = intval($memory_limit) * 1024;
			case 'k':
				$memory_limit = intval($memory_limit) * 1024;
		}
		if ($memory_limit < 128 * 1000 * 1000)
		{
			$audit_messages[] = array('message_type' => 5, 'resource' => "PHP_INI", 'detail' => ini_get('memory_limit'));
		}
	}

	$build_stamp = [];
	if (is_file("$config[project_path]/admin/stamp/stamp_$config[project_version].php"))
	{
		require "$config[project_path]/admin/stamp/stamp_$config[project_version].php";
		if (is_dir("$config[project_path]/admin/stamp/patches"))
		{
			$patches = get_contents_from_dir("$config[project_path]/admin/stamp/patches", 1);
			foreach ($patches as $patch)
			{
				if (strpos($patch, "$config[project_version]_patch") !== false)
				{
					require "$config[project_path]/admin/stamp/patches/$patch";
				}
			}
		}
		if (is_array($build_stamp) && array_cnt($build_stamp) > 0)
		{
			ksort($build_stamp);
			foreach ($build_stamp as $path => $hash)
			{
				auditCheckFileHash($path, $hash);
			}
		} else
		{
			$audit_messages[] = ['message_type' => 2, 'resource' => "[kt|b]/%ROOT%[/kt|b]/admin/stamp/stamp_$config[project_version].php"];
		}
	} else
	{
		$audit_messages[] = ['message_type' => 2, 'resource' => "[kt|b]/%ROOT%[/kt|b]/admin/stamp/stamp_$config[project_version].php"];
	}

	if (!is_file("$config[project_path]/admin/data/system/security_code.jpg"))
	{
		$audit_messages[] = ['message_type' => 2, 'resource' => "[kt|b]/%ROOT%[/kt|b]/admin/data/system/security_code.jpg"];
	}
	if (!is_file("$config[project_path]/admin/data/system/verdanaz.ttf"))
	{
		$audit_messages[] = ['message_type' => 2, 'resource' => "[kt|b]/%ROOT%[/kt|b]/admin/data/system/verdanaz.ttf"];
	}

	$own_ip = file_get_contents("$config[project_url]/get_file.php?action=check_ip");
	if ($own_ip != '' && strlen($own_ip) <= 40)
	{
		$admin_ip = mr2number(sql_pr("select ip from $config[tables_prefix_multi]log_logins order by login_date desc limit 1"));
		if (ip2int($own_ip) == $admin_ip)
		{
			$audit_messages[] = ['message_type' => 6, 'resource' => 'Nginx / Apache', 'detail' => $own_ip];
		}
	}

	$curl_installed = true;
	if (!function_exists('curl_init'))
	{
		$audit_messages[] = ['message_type' => 29, 'resource_id' => 'curl', 'resource' => 'cURL'];
		$curl_installed = false;
	}

	$gd_installed = true;
	if (!function_exists('imagecreatefromjpeg'))
	{
		$gd_installed = false;
		$audit_messages[] = ['message_type' => 29, 'resource_id' => 'image', 'resource' => 'Image Processing and GD'];
	}

	if ($curl_installed)
	{
		$site_headers = get_page('', 'https://www.google.com', '', '', 0, 1, 10, '', ['return_error' => true]);
		if (strpos($site_headers, 'Error:') === 0)
		{
			$audit_messages[] = ['message_type' => 11, 'resource' => 'cURL', 'detail' => "https://www.google.com\n\n$site_headers"];
		}

		if ($config['www_redirect'] != 'true' && strpos(str_replace('http://', '', $config['project_url']), '/') === false)
		{
			$site_headers = get_page('', str_replace('www.', '', $config['project_url']), '', '', 0, 1, 20, '', ['dont_follow' => true]);
			if ($site_headers)
			{
				if (stripos($site_headers, 'location: http://www.' . str_replace('http://', '', str_replace('www.', '', $config['project_url']))) !== false)
				{
					if (strpos($config['project_url'], 'www.') === false)
					{
						$audit_messages[] = ['message_type' => 20, 'resource' => '[kt|b]/%ROOT%[/kt|b]/admin/include/setup.php', 'detail' => "$config[project_url]\n\n$site_headers"];
					}
				} else
				{
					if (strpos($config['project_url'], 'www.') !== false)
					{
						$audit_messages[] = ['message_type' => 21, 'resource' => '[kt|b]/%ROOT%[/kt|b]/admin/include/setup.php', 'detail' => "$config[project_url]\n\n$site_headers"];
					}
				}
			}
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "KVS/$config[project_version]");
		curl_setopt($ch, CURLOPT_URL, "$config[project_url]/player/kt_player.js");
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept-Encoding: gzip']);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$js_file_headers = curl_exec($ch);
		if (stripos($js_file_headers, 'content-encoding: gzip') === false)
		{
			$audit_messages[] = ['message_type' => 25, 'resource' => 'Nginx / Apache', 'detail' => "$config[project_url]/player/kt_player.js\n\n$js_file_headers"];
		}
	}

	$rnd=mt_rand(10000000,99999999);
	$target_video="$plugin_path/test_video-$rnd.tmp";
	copy("$config[project_path]/admin/plugins/audit/data/test_video.avi",$target_video);
	if (is_file($target_video))
	{
		$video_duration=get_video_duration($target_video);
		if ($video_duration<30)
		{
			unset($res);
			exec("$config[ffmpeg_path] -i $target_video  2>&1",$res);
			$audit_messages[]=array('message_type'=>11,'resource'=>'FFmpeg','detail'=>"get_video_duration():\n".implode("\n",$res));
		} else {
			$video_dimension=get_video_dimensions($target_video);
			if ($video_dimension[0]<>320 || $video_dimension[1]<>240)
			{
				unset($res);
				exec("$config[ffmpeg_path] -i $target_video  2>&1",$res);
				$audit_messages[]=array('message_type'=>11,'resource'=>'FFmpeg','detail'=>"get_video_dimensions():\n".implode("\n",$res));
			}
		}
	}
	@unlink($target_video);

	$source_image="$config[project_path]/admin/plugins/audit/data/test_watermark.png";
	$target_image="$plugin_path/test_image-$rnd.jpg";
	unset($res);
	exec("$config[image_magick_path] $source_image $target_image 2>&1",$res);
	if (!is_file($target_image) || filesize($target_image)==0)
	{
		$audit_messages[]=array('message_type'=>11,'resource'=>'ImageMagick','detail'=>"$config[image_magick_path] $source_image $target_image:\n".implode("\n",$res));
	}
	@unlink($target_image);

	if ($curl_installed && $gd_installed)
	{
		$is_captcha_ok=true;
		$captcha_page_id='';

		$page_folders=get_contents_from_dir("$config[project_path]/admin/data/config",2);
		foreach ($page_folders as $page_id)
		{
			if ($page_id!='$global' && is_file("$config[project_path]/admin/data/config/$page_id/config.dat") && !is_file("$config[project_path]/admin/data/config/$page_id/deleted.dat"))
			{
				$page_config=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/config.dat"));
				if (intval($page_config[4])==0 && intval($page_config[5])==0)
				{
					$captcha_page_id=$page_id;
					break;
				}
			}
		}

		if ($captcha_page_id)
		{
			$temp_captcha="$plugin_path/captcha-$rnd.gif";
			save_file_from_url("$config[project_url]/$captcha_page_id.php?mode=async&action=show_security_code",$temp_captcha,"",20);
			if (is_file($temp_captcha))
			{
				$img_size=getimagesize("$temp_captcha");
				if ($img_size[0]<1 || $img_size[1]<1)
				{
					$is_captcha_ok=false;
				}
			} else
			{
				$is_captcha_ok=false;
			}
			if (is_file($temp_captcha)) {unlink($temp_captcha);}

			if (!$is_captcha_ok)
			{
				$captcha_headers=get_page("","$config[project_url]/$captcha_page_id.php?mode=async&action=show_security_code","","",0,1,20,"");
				if ($captcha_headers && strpos($captcha_headers, '401 Unauthorized') === false)
				{
					$audit_messages[]=array('message_type'=>15,'resource'=>'TrueType','detail'=>"$config[project_url]/$captcha_page_id.php?mode=async&action=show_security_code\n\n$captcha_headers");
				}
			}
		}
	}

	if (is_writable($config['temporary_path']))
	{
		$rnd=mt_rand(10000000,99999999);
		mkdir("$config[temporary_path]/$rnd",0777);
		chmod("$config[temporary_path]/$rnd",0777);
		$permissions=substr(sprintf('%o',fileperms("$config[temporary_path]/$rnd")),-3);
		if (intval($permissions)<>777)
		{
			$audit_messages[]=array('message_type'=>18,'resource'=>'File System','detail'=>"$config[temporary_path]/$rnd: $permissions");
		} else {
			@unlink("$config[temporary_path]/$rnd");
		}
	}

	if ($config['is_clone_db']=='true' && $config['satellite_for']!='')
	{
		$database_system_domain=$options['SYSTEM_DOMAIN'];
		if ($config['satellite_for']!=$database_system_domain)
		{
			$audit_messages[]=array('message_type'=>22,'resource'=>"[kt|b]/%ROOT%[/kt|b]/admin/include/setup.php",'detail'=>"Database: $database_system_domain\nsetup.php: $config[satellite_for]");
		}
	}

	if (is_file("$config[project_path]/.htaccess"))
	{
		$htaccess_contents=file_get_contents("$config[project_path]/.htaccess");
		if (strpos($htaccess_contents,"iframe_embed.php?video_id=")!==false && strpos($htaccess_contents,"player/iframe_embed.php?video_id=")===false)
		{
			$audit_messages[]=array('message_type'=>23,'resource'=>"[kt|b]/%ROOT%[/kt|b]/.htaccess");
		}
	}

	if (!is_file("$config[project_path]/admin/data/system/default_translit_rules.dat"))
	{
		if ($options['DIRECTORIES_TRANSLIT_RULES']=='' && !is_file("$config[project_path]/admin/include/kvs_translit.php"))
		{
			$audit_messages[]=array('message_type'=>26, 'resource'=>"KVS");
		}
	}

	$admin_notificatons = mr2array(sql_pr("select * from $config[tables_prefix_multi]admin_notifications"));
	foreach ($admin_notificatons as $notification)
	{
		$notification_sub_ids = explode('.', $notification['notification_id']);
		if (array_cnt($notification_sub_ids) >= 2)
		{
			if ($notification_sub_ids[0] == 'plugins')
			{
				if (is_file("$config[project_path]/admin/plugins/$notification_sub_ids[1]/$notification_sub_ids[1].php"))
				{
					require_once "$config[project_path]/admin/plugins/$notification_sub_ids[1]/$notification_sub_ids[1].php";
					$init_function = "$notification_sub_ids[1]Init";
					if (function_exists($init_function))
					{
						$init_function();
					}

					$notification_type = KvsAdminNotificationEnum::get_by_uid($notification['notification_id']);
					if ($notification_type)
					{
						$audit_messages[] = [
								'message_type' => (in_array($notification_type->get_severity(), [KvsAdminNotificationEnum::SEVERITY_ERROR, KvsAdminNotificationEnum::SEVERITY_CRITICAL]) ? 72 : ($notification_type->get_severity() == KvsAdminNotificationEnum::SEVERITY_WARNING ? 71 : 73)),
								'plugin_id' => $notification_sub_ids[1],
								'plugin_message' => $notification_type->get_title(@json_decode($notification['details'], true) ?: [])
						];
					}
				}
			}
		}
	}

	if (!function_exists('mb_detect_encoding'))
	{
		$audit_messages[] = array('message_type' => 29, 'resource_id' => "mbstring", 'resource' => "Multibyte String");
	}

	if (!function_exists('gzopen'))
	{
		$audit_messages[] = array('message_type' => 29, 'resource_id' => "zlib", 'resource' => "Zlib");
	}

	if (!function_exists('json_decode'))
	{
		$audit_messages[] = array('message_type' => 29, 'resource_id' => "json", 'resource' => "JSON");
	}

	if (!function_exists('simplexml_load_string'))
	{
		$audit_messages[] = array('message_type' => 29, 'resource_id' => "simplexml", 'resource' => "SimpleXML");
	}

	if ($config['memcache_server']<>'')
	{
		if (!class_exists('Memcached'))
		{
			$audit_messages[]=array('message_type'=>31, 'resource'=>"PHP");
		} else
		{
			$memcache=new Memcached();
			if (!$memcache->addServer($config['memcache_server'],$config['memcache_port']) || !$memcache->getStats())
			{
				$audit_messages[]=array('message_type'=>32, 'resource'=>"Memcached", 'detail'=>"$config[memcache_server]:$config[memcache_port]\n" . $memcache->getLastErrorMessage());
			}
		}
	}

	if (is_dir("$config[project_path]/_INSTALL"))
	{
		$audit_messages[]=array('message_type'=>35, 'resource'=>"[kt|b]/%ROOT%[/kt|b]/_INSTALL");
	}
	if (is_file("$config[project_path]/kvs_change_domain.php"))
	{
		$audit_messages[]=array('message_type'=>35, 'resource'=>"[kt|b]/%ROOT%[/kt|b]/kvs_change_domain.php");
	}
	if (is_file("$config[project_path]/reset_admin_password.php"))
	{
		$audit_messages[]=array('message_type'=>35, 'resource'=>"[kt|b]/%ROOT%[/kt|b]/reset_admin_password.php");
	}
	if (is_file("$config[project_path]/kvs_package_upgrade.php"))
	{
		$audit_messages[]=array('message_type'=>35, 'resource'=>"[kt|b]/%ROOT%[/kt|b]/kvs_package_upgrade.php");
	}

	$kvs_blocks=array('list_content_sources','global_stats','invite_friend','tags_cloud_v2','list_tags','pagination','top_referers','list_content_sources_groups','playlist_edit','list_albums_images','content_source_group_view','list_members_events','album_edit','list_dvds','album_comments','random_video','album_images','list_models','list_models_groups','post_view','post_edit','list_dvds_groups','list_playlists','list_posts','content_source_view','list_content','member_profile_view','dvd_view','logon','search_results','dvd_edit','playlist_view','content_source_comments','list_members','list_comments','list_members_blog','list_videos','member_profile_delete','video_comments','post_comments','list_categories_groups','signup','dvd_comments','model_comments','video_edit','feedback','list_members_tokens','list_albums','list_categories','video_view','album_view','tags_cloud','playlist_comments','dvd_group_view','message_details','model_view','upgrade','list_messages','list_members_subscriptions','member_profile_edit');

	if (is_file("$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php"))
	{
		require_once "$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php";
		$awe_api_blocks = awe_black_labelListBlocks();
		if (is_array($awe_api_blocks))
		{
			$kvs_blocks = array_merge($kvs_blocks, $awe_api_blocks);
		}
	}

	$custom_blocks_list = '';
	$blocks = get_contents_from_dir("$config[project_path]/blocks", 2);
	foreach ($blocks as $block)
	{
		if (!in_array($block, $kvs_blocks))
		{
			$custom_blocks_list .= "$block, ";
		}
	}
	$custom_blocks_list = trim($custom_blocks_list, ' ,');
	if ($custom_blocks_list != '')
	{
		$audit_messages[]=array('message_type'=>36, 'resource'=>"[kt|b]/%ROOT%[/kt|b]/blocks", 'detail'=>$custom_blocks_list);
	}

	$ydl_path = '/usr/local/bin/yt-dlp';
	unset($res);
	exec("$ydl_path 2>&1", $res);
	if (!preg_match('|\[OPTIONS]|is', trim(implode(' ', $res))))
	{
		$ydl_path = '/usr/bin/yt-dlp';
		unset($res);
		exec("$ydl_path 2>&1", $res);
		if (!preg_match('|\[OPTIONS]|is', trim(implode(' ', $res))))
		{
			$ydl_path = '';
		}
	}
	if ($ydl_path === '')
	{
		$audit_messages[]=array('message_type'=>37, 'resource'=>"Yt-dlp");
	}

	$setup_db_contents = file_get_contents("$config[project_path]/admin/include/setup_db.php");
	if (strpos($setup_db_contents, '_query(') !== false)
	{
		$audit_messages[]=array('message_type'=>38, 'resource'=>"[kt|b]/%ROOT%[/kt|b]/admin/include/setup_db.php");
	}

	if ($curl_installed && $gd_installed)
	{
		$test_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where status_id in (0, 1) and load_type_id=1 order by video_id desc limit 1"));
		if (intval($test_video['video_id']) > 0)
		{
			$dir_path = get_dir_by_id($test_video['video_id']);
			if (is_file("$config[content_path_videos_sources]/$dir_path/$test_video[video_id]/screenshots/1.jpg"))
			{
				$screen_source_url = get_video_source_url($test_video['video_id'], 'screenshots/1.jpg');
				save_file_from_url($screen_source_url, "$plugin_path/test_image-$rnd.jpg");
				$img_size = getimagesize("$plugin_path/test_image-$rnd.jpg");
				if ($img_size[0] < 1 || $img_size[1] < 1)
				{
					$headers = get_page('',$screen_source_url,'','',0,1,20,'');
					if (strpos($headers, '401 Unauthorized') === false)
					{
						$audit_messages[] = array('message_type' => 39, 'resource' => "[kt|b]/%ROOT%[/kt|b]/admin/include/setup.php", 'detail' => "$screen_source_url\n\n$headers");
					}
				}
				@unlink("$plugin_path/test_image-$rnd.jpg");
			}
		}
	}

	$languages = mr2array(sql_pr("select * from $config[tables_prefix]languages"));
	foreach ($languages as $language)
	{
		auditCheckLanguage($language);
	}

	$config['sql_safe_mode'] = 1;
	foreach ($database_tables_autoincrement as $table => $key_field)
	{
		if (mr2rows(sql_pr("show columns from $table where `Field`=? and `Extra` like '%auto_increment%'", $key_field)) == 0)
		{
			$audit_messages[] = array('message_type' => 64, 'resource' => "$table.$key_field");
		}
	}
	unset($config['sql_safe_mode']);

	auditCheckPlayerSettings();

	auditCheckKnownKvsIssues();
}

function auditCheckFormat($rnd, $format)
{
	global $config, $audit_messages;

	if ($config['is_clone_db'] == "true")
	{
		return;
	}

	$priority_prefix = 'nice -n 4 ';
	if (strtolower(substr(PHP_OS, 0, 3)) == 'win')
	{
		$priority_prefix = '';
	}
	if ($format['format_video_id'] > 0)
	{
		auditLogMessage("Video format \"$format[title]\" check started");

		$source_file = "$config[project_path]/admin/data/plugins/audit/test_video-$rnd.tmp";
		$target_file = "$config[project_path]/admin/data/plugins/audit/test_video-$rnd-" . intval($format['format_video_id']) . '.tmp';

		$watermark_string = '';
		if (is_file("$config[project_path]/admin/data/other/watermark_video_{$format['format_video_id']}.png") || is_file("$config[project_path]/admin/data/other/watermark2_video_{$format['format_video_id']}.png"))
		{
			$watermark_string = "-vf \"movie=$config[project_path]/admin/plugins/audit/data/test_watermark.png [wm];[in][wm] overlay=0:0 [out]\"";
		}
		if (strpos($format['ffmpeg_options'], ';') !== false || strpos($format['ffmpeg_options'], '..') !== false)
		{
			$audit_messages[] = array('message_type' => 12, 'resource' => $format['title'], 'resource_id' => $format['format_video_id'], 'detail' => $format['ffmpeg_options']);
			return;
		}
		$exec_str = "{$priority_prefix}$config[ffmpeg_path] -y -i $source_file $format[ffmpeg_options] $watermark_string $target_file 2>&1";
		unset($res);
		exec("$exec_str 2>&1", $res);
		if (!is_file($target_file) || sprintf("%.0f", @filesize($target_file)) == 0)
		{
			$res = (array_cnt($res) > 0 ? implode("\n", $res) : "no response");
			$audit_messages[] = array('message_type' => 12, 'resource' => $format['title'], 'resource_id' => $format['format_video_id'], 'detail' => "$exec_str: $res");
			@unlink($target_file);
			return;
		}
		@unlink($target_file);
	} elseif ($format['format_screenshot_id'] > 0)
	{
		auditLogMessage("Screenshot format \"$format[title]\" check started");

		$source_file = "$config[project_path]/admin/data/plugins/audit/test_video-$rnd.jpg";
		$target_file = "$config[project_path]/admin/data/plugins/audit/test_video-" . intval($format['format_screenshot_id']) . '.jpg';

		$image_target_size = KvsUtilities::parse_size($format['size']);
		if ($image_target_size == 'source' && function_exists('getimagesize'))
		{
			$image_target_size = getimagesize($source_file);
		}
		if (strpos($format['im_options'], ';') !== false || strpos($format['im_options'], '..') !== false)
		{
			$audit_messages[] = array('message_type' => 13, 'resource' => $format['title'], 'resource_id' => $format['format_screenshot_id'], 'detail' => $format['im_options']);
			return;
		}
		if (strpos($format['im_options_manual'], ';') !== false || strpos($format['im_options_manual'], '..') !== false)
		{
			$audit_messages[] = array('message_type' => 13, 'resource' => $format['title'], 'resource_id' => $format['format_screenshot_id'], 'detail' => $format['im_options_manual']);
			return;
		}
		if ($format['image_type'] == 1)
		{
			$exec_str = "{$priority_prefix}$config[image_magick_path] " . str_replace("%SIZE%", "$image_target_size[0]x$image_target_size[1]", str_replace("%INPUT_FILE%", $source_file, str_replace("%OUTPUT_FILE%", "webp:$target_file", $format['im_options'])));
		} else
		{
			$exec_str = "{$priority_prefix}$config[image_magick_path] " . str_replace("%SIZE%", "$image_target_size[0]x$image_target_size[1]", str_replace("%INPUT_FILE%", $source_file, str_replace("%OUTPUT_FILE%", $target_file, $format['im_options'])));
		}
		unset($res);
		exec("$exec_str 2>&1", $res);
		if (sprintf("%.0f", @filesize($target_file)) == 0)
		{
			$res = (array_cnt($res) > 0 ? implode("\n", $res) : "no response");
			$audit_messages[] = array('message_type' => 13, 'resource' => $format['title'], 'resource_id' => $format['format_screenshot_id'], 'detail' => "$exec_str: $res");
			@unlink($target_file);
			return;
		}
		@unlink($target_file);

		if ($format['image_type'] == 1)
		{
			$exec_str = "{$priority_prefix}$config[image_magick_path] " . str_replace("%SIZE%", "$image_target_size[0]x$image_target_size[1]", str_replace("%INPUT_FILE%", $source_file, str_replace("%OUTPUT_FILE%", "webp:$target_file", $format['im_options_manual'])));
		} else
		{
			$exec_str = "{$priority_prefix}$config[image_magick_path] " . str_replace("%SIZE%", "$image_target_size[0]x$image_target_size[1]", str_replace("%INPUT_FILE%", $source_file, str_replace("%OUTPUT_FILE%", $target_file, $format['im_options_manual'])));
		}
		unset($res);
		exec("$exec_str 2>&1", $res);
		if (sprintf("%.0f", @filesize($target_file)) == 0)
		{
			$res = (array_cnt($res) > 0 ? implode("\n", $res) : "no response");
			$audit_messages[] = array('message_type' => 13, 'resource' => $format['title'], 'resource_id' => $format['format_screenshot_id'], 'detail' => "$exec_str: $res");
			@unlink($target_file);
			return;
		}
		@unlink($target_file);

		$watermark_path = "$config[project_path]/admin/data/other/watermark_screen_" . intval($format['format_screenshot_id']) . '.png';
		if (is_file($watermark_path))
		{
			$exec_str = "{$priority_prefix}$config[image_magick_path] $source_file $watermark_path -gravity NorthWest -composite $target_file";
			unset($res);
			exec("$exec_str 2>&1", $res);
			if (sprintf("%.0f", @filesize($target_file)) == 0)
			{
				$res = (array_cnt($res) > 0 ? implode("\n", $res) : "no response");
				$audit_messages[] = array('message_type' => 13, 'resource' => $format['title'], 'resource_id' => $format['format_screenshot_id'], 'detail' => "$exec_str: $res");
				@unlink($target_file);
				return;
			}
		}
		@unlink($target_file);
	} elseif ($format['format_album_id'] > 0)
	{
		auditLogMessage("Album format \"$format[title]\" check started");

		$source_file = "$config[project_path]/admin/data/plugins/audit/test_video-$rnd.jpg";
		$target_file = "$config[project_path]/admin/data/plugins/audit/test_video-" . intval($format['format_album_id']) . '.jpg';

		if (strpos($format['im_options'], ';') !== false || strpos($format['im_options'], '..') !== false)
		{
			$audit_messages[] = array('message_type' => 14, 'resource' => $format['title'], 'resource_id' => $format['format_album_id'], 'detail' => $format['im_options']);
			return;
		}

		$image_target_size = KvsUtilities::parse_size($format['size']);
		$exec_str = "{$priority_prefix}$config[image_magick_path] " . str_replace("%SIZE%", "$image_target_size[0]x$image_target_size[1]", str_replace("%INPUT_FILE%", $source_file, str_replace("%OUTPUT_FILE%", $target_file, $format['im_options'])));
		unset($res);
		exec("$exec_str 2>&1", $res);
		if (sprintf("%.0f", @filesize($target_file)) == 0)
		{
			$res = (array_cnt($res) > 0 ? implode("\n", $res) : "no response");
			$audit_messages[] = array('message_type' => 14, 'resource' => $format['title'], 'resource_id' => $format['format_album_id'], 'detail' => "$exec_str: $res");
			@unlink($target_file);
			return;
		}
		@unlink($target_file);

		$watermark_path = "$config[project_path]/admin/data/other/watermark_album_" . intval($format['format_album_id']) . '.png';
		if (is_file($watermark_path))
		{
			$exec_str = "{$priority_prefix}$config[image_magick_path] $source_file $watermark_path -gravity NorthWest -composite $target_file";
			unset($res);
			exec("$exec_str 2>&1", $res);
			if (sprintf("%.0f", @filesize($target_file)) == 0)
			{
				$res = (array_cnt($res) > 0 ? implode("\n", $res) : "no response");
				$audit_messages[] = array('message_type' => 14, 'resource' => $format['title'], 'resource_id' => $format['format_album_id'], 'detail' => "$exec_str: $res");
				@unlink($target_file);
				return;
			}
		}
		@unlink($target_file);
	}
}

function auditCheckTableStatus($table)
{
	global $audit_messages;

	$result=mr2array(sql("check table $table medium"));
	foreach ($result as $row)
	{
		if (strtolower($row['Msg_type'])=='warning')
		{
			$audit_messages[]=array('message_type'=>61,'resource'=>$table,'detail'=>$row['Msg_text']);
		} elseif (strtolower($row['Msg_type'])=='error')
		{
			$audit_messages[]=array('message_type'=>62,'resource'=>$table,'detail'=>$row['Msg_text']);
		}
	}
}

function auditCheckLanguage($language)
{
	global $config, $audit_messages;

	$tables = [
			"$config[tables_prefix]tags",
			"$config[tables_prefix]categories",
			"$config[tables_prefix]categories_groups",
			"$config[tables_prefix]models",
			"$config[tables_prefix]models_groups",
			"$config[tables_prefix]content_sources",
			"$config[tables_prefix]content_sources_groups",
			"$config[tables_prefix]dvds",
			"$config[tables_prefix]dvds_groups",
			"$config[tables_prefix]videos",
			"$config[tables_prefix]albums",
	];

	$error_details = [];
	foreach ($tables as $table)
	{
		if ($table == "$config[tables_prefix]tags")
		{
			if (mr2rows(sql("show columns from `$table` like 'tag_$language[code]'")) == 0)
			{
				$error_details[] = "$table.tag_$language[code]";
			}
			if (mr2rows(sql("show columns from `$table` like 'tag_dir_$language[code]'")) == 0)
			{
				$error_details[] = "$table.tag_dir_$language[code]";
			}
		} else
		{
			if (mr2rows(sql("show columns from `$table` like 'title_$language[code]'")) == 0)
			{
				$error_details[] = "$table.title_$language[code]";
			}
			if (mr2rows(sql("show columns from `$table` like 'description_$language[code]'")) == 0)
			{
				$error_details[] = "$table.description_$language[code]";
			}
			if (mr2rows(sql("show columns from `$table` like 'dir_$language[code]'")) == 0)
			{
				$error_details[] = "$table.dir_$language[code]";
			}
		}
	}
	if (array_cnt($error_details) > 0)
	{
		$audit_messages[] = array('message_type' => 63, 'resource' => $language['title'], 'detail' => implode("\n", $error_details));
	}
}

function auditCheckPlayerSettings()
{
	global $config,$audit_messages;

	$player_skins=array('1', '2');
	$player_skin_files=get_contents_from_dir("$config[project_path]/player/skin",1);
	foreach ($player_skin_files as $player_skin_file)
	{
		if (KvsUtilities::str_ends_with($player_skin_file, '.css'))
		{
			$player_skins[]=$player_skin_file;
		}
	}

	$formats_videos=mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2)"));
	$formats_screenshots=mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=2"));
	$advertising_spots=get_site_spots();
	$vast_profiles=get_vast_profiles();

	$has_vast_enabled = false;
	$player_files=get_player_data_files();
	foreach ($player_files as $player_file)
	{
		if (is_file($player_file['file']))
		{
			$has_error=false;
			$error_detail='';
			$player_data=@unserialize(file_get_contents($player_file['file']));

			if (!in_array($player_data['skin'],$player_skins) && $player_data['skin']!='disable')
			{
				$has_error=true;
				$error_detail.="Skin: $player_data[skin]\n";
			}

			if ($player_data['timeline_screenshots_size'])
			{
				$valid_format=false;
				foreach ($formats_screenshots as $format)
				{
					if ($format['size']==$player_data['timeline_screenshots_size'])
					{
						$valid_format=true;
						break;
					}
				}
				if (!$valid_format)
				{
					$has_error=true;
					$error_detail.="Screenshot format: $player_data[timeline_screenshots_size]\n";
				}
			}

			foreach ($player_data['formats'] as $group_id => $group)
			{
				$has_missing_formats = false;
				foreach ($group as $item)
				{
					if ($item['type'] <> 'redirect')
					{
						$has_format = false;
						foreach ($formats_videos as $format)
						{
							if ($format['format_video_group_id'] == $group_id && $format['postfix'] == $item['type'])
							{
								$has_format = true;
								break;
							}
						}
						if (!$has_format)
						{
							$has_missing_formats = true;
							$error_detail .= "Video format: $item[type]\n";
						}
					}
				}
				if ($has_missing_formats)
				{
					$has_error = true;
				}
			}

			if (strpos($player_data['start_html_source'],'spot_')!==false)
			{
				if (!isset($advertising_spots[substr($player_data['start_html_source'],5)]))
				{
					$has_error=true;
					$error_detail.="Advertising spot: ".substr($player_data['start_html_source'],5)."\n";
				}
			}

			if (strpos($player_data['pre_roll_html_source'],'spot_')!==false)
			{
				if (!isset($advertising_spots[substr($player_data['pre_roll_html_source'],5)]))
				{
					$has_error=true;
					$error_detail.="Advertising spot: ".substr($player_data['pre_roll_html_source'],5)."\n";
				}
			}

			if (strpos($player_data['post_roll_html_source'],'spot_')!==false)
			{
				if (!isset($advertising_spots[substr($player_data['post_roll_html_source'],5)]))
				{
					$has_error=true;
					$error_detail.="Advertising spot: ".substr($player_data['post_roll_html_source'],5)."\n";
				}
			}

			if (strpos($player_data['pause_html_source'],'spot_')!==false)
			{
				if (!isset($advertising_spots[substr($player_data['pause_html_source'],5)]))
				{
					$has_error=true;
					$error_detail.="Advertising spot: ".substr($player_data['pause_html_source'],5)."\n";
				}
			}

			if (strpos($player_data['video_click_url_source'], 'vast_profile_') !== false)
			{
				if (!isset($vast_profiles[substr($player_data['video_click_url_source'], 13)]))
				{
					$has_error = true;
					$error_detail .= "Advertising profile: " . substr($player_data['video_click_url_source'], 13) . "\n";
				}
			}

			if (strpos($player_data['popunder_url_source'], 'vast_profile_') !== false)
			{
				if (!isset($vast_profiles[substr($player_data['popunder_url_source'], 13)]))
				{
					$has_error = true;
					$error_detail .= "Advertising profile: " . substr($player_data['popunder_url_source'], 13) . "\n";
				}
			}

			if (strpos($player_data['pre_roll_vast_provider'], 'vast_profile_') !== false)
			{
				if (!isset($vast_profiles[substr($player_data['pre_roll_vast_provider'], 13)]))
				{
					$has_error = true;
					$error_detail .= "Advertising profile: " . substr($player_data['pre_roll_vast_provider'], 13) . "\n";
				}
			}

			if (strpos($player_data['post_roll_vast_provider'], 'vast_profile_') !== false)
			{
				if (!isset($vast_profiles[substr($player_data['post_roll_vast_provider'], 13)]))
				{
					$has_error = true;
					$error_detail .= "Advertising profile: " . substr($player_data['post_roll_vast_provider'], 13) . "\n";
				}
			}

			if ($player_data['enable_pre_roll_vast'] == 1 || $player_data['enable_post_roll_vast'] == 1)
			{
				$has_vast_enabled = true;
			}

			if ($has_error)
			{
				$audit_messages[]=array('message_type'=>$player_file['is_embed']==1?52:51,'resource_id'=>$player_file['admin_page'],'detail'=>$error_detail);
			}
		}
	}

	if ($has_vast_enabled)
	{
		$vast_key_data = @unserialize(file_get_contents("$config[project_path]/admin/data/player/vast/key.dat"), ['allowed_classes' => false]) ?: [];
		if ($vast_key_data['primary_vast_key'])
		{
			$vast_key_valid = intval(substr($vast_key_data['primary_vast_key'], 0, 10));
			if ($vast_key_valid > 0)
			{
				$vast_key_valid = intval(($vast_key_valid - time()) / 86400);
				if ($vast_key_valid <= 0)
				{
					$audit_messages[] = ['message_type' => 51, 'resource_id' => 'player.php', 'detail' => "VAST subscription key: $vast_key_data[primary_vast_key]"];
				}
			} else
			{
				$audit_messages[] = ['message_type' => 51, 'resource_id' => 'player.php', 'detail' => "VAST subscription key: $vast_key_data[primary_vast_key]"];
			}
		}
	}
}

function auditCheckKnownKvsIssues()
{
	global $config,$audit_messages,$options;

	// 1) search htaccess issue

	$data=explode("\n",@file_get_contents("$config[project_path]/.htaccess"));
	for ($i=0;$i<array_cnt($data);$i++)
	{
		if (strpos(trim($data[$i]),"RewriteRule ^search/(.*)/([0-9]+)/$")===0)
		{
			if (strpos(trim($data[$i+1]),"RewriteRule ^search/(.*)/$")===0)
			{
				if (strpos(trim($data[$i+2]),"RewriteRule ^search/([0-9]+)/$")===0)
				{
					$audit_messages[]=array('message_type'=>1001,'resource'=>"KVS",'detail'=>"$config[project_path]/.htaccess");
				}
			}
		}
	}

	// 2) video_view player JSX
	// 3) model_view age.value
	// 4) list_members_subscriptions item.channel
	// 9) video_edit list_channels
	// 10) list_categories is_avatar_available
	// 11) content_source_view / video_view / album_view .screenshot}}
	// 12) album_edit list_categories_advanced
	// 16) content_source_group_view content_sources_count
	// 16) dvd_group_view dvds_count
	// 16) $data.category

	$block_folders = get_contents_from_dir("$config[project_path]/template/blocks", 2);
	foreach ($block_folders as $block_folder)
	{
		$block_templates = get_contents_from_dir("$config[project_path]/template/blocks/$block_folder", 1);
		$has_model_view_block = false;
		foreach ($block_templates as $block_template)
		{
			$template_path = "$config[project_path]/template/blocks/$block_folder/$block_template";
			$template_code = @file_get_contents($template_path);
			if (strpos($block_template, 'model_view_') === 0 || strpos($block_template, 'list_models_') === 0)
			{
				if (strpos($template_code, '.age.value') !== false)
				{
					$audit_messages[] = array('message_type' => 1003, 'resource' => "KVS", 'detail' => $template_path);
				}
				$has_model_view_block = true;
			}
			if (strpos($block_template, 'model_view_') === 0)
			{
				if (strpos($template_code, '$group_as_string') !== false || strpos($template_code, '.videos_count') !== false || strpos($template_code, '.albums_count') !== false || strpos($template_code, '.photos_count') !== false)
				{
					$audit_messages[] = array('message_type' => 1021, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'list_members_subscriptions_') === 0)
			{
				if (strpos($template_code, 'item.channel') !== false)
				{
					$audit_messages[] = array('message_type' => 1004, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'video_edit_') === 0)
			{
				if (strpos($template_code, '$list_channels') !== false)
				{
					$audit_messages[] = array('message_type' => 1009, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'list_categories_') === 0)
			{
				if (strpos($template_code, 'is_avatar_available') !== false || strpos($template_code, 'base_files_url}}.jpg') !== false || strpos($template_code, 'content_url_categories}}') !== false)
				{
					$audit_messages[] = array('message_type' => 1010, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'video_view_') === 0)
			{
				if (strpos($template_code, 'kt_player_{{$config.project_version}}.jsx') !== false)
				{
					$audit_messages[] = array('message_type' => 1002, 'resource' => "KVS", 'detail' => $template_path);
				}
				if (strpos($template_code, '.screenshot}') !== false)
				{
					$audit_messages[] = array('message_type' => 1011, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'album_view_') === 0)
			{
				if (strpos($template_code, '.screenshot}') !== false)
				{
					$audit_messages[] = array('message_type' => 1011, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'content_source_view_') === 0)
			{
				if (strpos($template_code, '.screenshot}') !== false)
				{
					$audit_messages[] = array('message_type' => 1011, 'resource' => "KVS", 'detail' => $template_path);
				}
				if (strpos($template_code, '$group_as_string') !== false || strpos($template_code, '.videos_count') !== false || strpos($template_code, '.albums_count') !== false || strpos($template_code, '.photos_count') !== false)
				{
					$audit_messages[] = array('message_type' => 1021, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'list_content_sources_') === 0)
			{
				if (strpos($template_code, '.screenshot}') !== false)
				{
					$audit_messages[] = array('message_type' => 1011, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'album_edit_') === 0)
			{
				if (strpos($template_code, 'list_categories_advanced') !== false)
				{
					$audit_messages[] = array('message_type' => 1012, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'content_source_group_view_') === 0)
			{
				if (strpos($template_code, 'content_sources_count') !== false)
				{
					$audit_messages[] = array('message_type' => 1016, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, 'dvd_group_view_') === 0)
			{
				if (strpos($template_code, 'dvds_count') !== false)
				{
					$audit_messages[] = array('message_type' => 1016, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
			if (strpos($block_template, '_view_') !== false)
			{
				if (strpos($template_code, '$data.category') !== false)
				{
					$audit_messages[] = array('message_type' => 1016, 'resource' => "KVS", 'detail' => $template_path);
				}
			}
		}

		if ($has_model_view_block)
		{
			$page_template = @file_get_contents("$config[project_path]/template/$block_folder.tpl");
			if (strpos($page_template, '.age.value') !== false)
			{
				$audit_messages[] = array('message_type' => 1003, 'resource' => "KVS", 'detail' => "$config[project_path]/template/$block_folder.tpl");
			}
		}
	}

	// 5) truncated transliteraion rules
	if (intval($options['DIRECTORIES_TRANSLIT'])==1)
	{
		if (preg_match('/.+z$/is',trim($options['DIRECTORIES_TRANSLIT_RULES'],', ')))
		{
			$audit_messages[]=array('message_type'=>1005,'resource'=>"KVS");
		}
	}

	// 7) allowed ahvs
	if (isset($config['allowed_ahvs']) && array_cnt($config['allowed_ahvs'])>0)
	{
		$audit_messages[]=array('message_type'=>1007,'resource'=>"KVS");
	}

	// 8) lrc
	if (isset($config['player_lrc']) && strlen($config['player_lrc'])==32)
	{
		$audit_messages[]=array('message_type'=>1008,'resource'=>"KVS");
	}

	// 13) list_categories category_group_ids + list_content_sources content_source_group_ids
	// 18) list_categories_groups show_only_with_avatar show_only_without_avatar
	$block_folders = get_contents_from_dir("$config[project_path]/admin/data/config", 2);
	foreach ($block_folders as $block_folder)
	{
		$block_config_files = get_contents_from_dir("$config[project_path]/admin/data/config/$block_folder", 1);
		foreach ($block_config_files as $block_config_file)
		{
			if (strpos($block_config_file, 'list_categories_') === 0 || strpos($block_config_file, 'list_content_sources_') === 0)
			{
				$block_params = @file_get_contents("$config[project_path]/admin/data/config/$block_folder/$block_config_file");
				$block_params = explode('||', $block_params);
				if ($block_params[1])
				{
					$block_params = explode('&', trim($block_params[1]));
					foreach ($block_params as $block_param)
					{
						if (strpos($block_param, 'category_group_ids=') === 0 || strpos($block_param, 'content_source_group_ids=') === 0)
						{
							$audit_messages[] = array('message_type' => 1013, 'resource' => 'KVS', 'detail' => "$config[project_path]/admin/data/config/$block_folder/$block_config_file ($block_param)");
						}
					}
				}
			}
			if (strpos($block_config_file, 'list_categories_groups_') === 0)
			{
				$block_params = @file_get_contents("$config[project_path]/admin/data/config/$block_folder/$block_config_file");
				$block_params = explode('||', $block_params);
				if ($block_params[1])
				{
					$block_params = explode('&', trim($block_params[1]));
					foreach ($block_params as $block_param)
					{
						if (strpos($block_param, 'show_only_with_avatar') === 0 || strpos($block_param, 'show_only_without_avatar') === 0)
						{
							$audit_messages[] = array('message_type' => 1018, 'resource' => 'KVS', 'detail' => "$config[project_path]/admin/data/config/$block_folder/$block_config_file ($block_param)");
						}
					}
				}
			}
			if (strpos($block_config_file, 'list_tags_') === 0)
			{
				$block_params = @file_get_contents("$config[project_path]/admin/data/config/$block_folder/$block_config_file");
				$block_params = explode('||', $block_params);
				if ($block_params[1])
				{
					$block_params = explode('&', trim($block_params[1]));
					foreach ($block_params as $block_param)
					{
						if (strpos($block_param, 'show_only_with_cs') === 0 || strpos($block_param, 'show_only_with_playlists') === 0)
						{
							$audit_messages[] = array('message_type' => 1022, 'resource' => 'KVS', 'detail' => "$config[project_path]/admin/data/config/$block_folder/$block_config_file ($block_param)");
						}
					}
				}
			}
		}
	}

	// 14) Memcached VS Memcache
	$default_lang = @file_get_contents("$config[project_path]/langs/default.php");
	if (class_exists('Memcached') && class_exists('Memcache') && strpos($default_lang, "&& class_exists('Memcache'))") !== false)
	{
		$audit_messages[] = array('message_type' => 1014, 'resource' => "KVS");
	}

	// 15) '_preview.mp4' with no duration limit
	if ($config['is_clone_db'] != 'true')
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]formats_videos where postfix='_preview.mp4' and limit_total_duration=0")) > 0)
		{
			$audit_messages[] = array('message_type' => 1015, 'resource' => "KVS");
		}
	}

	// 17) backward compatibility checks
	if (is_file("$config[project_path]/admin/data/engine/checks/audit1017.dat"))
	{
		$audit_messages[] = array('message_type' => 1017, 'resource' => "KVS");
	}

	$global_blocks_list = explode("||", file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
	$global_blocks_list = explode("|AND|", trim($global_blocks_list[2]));
	foreach ($global_blocks_list as $block)
	{
		$block_name = substr($block, strpos($block, "[SEP]") + 5);
		if ($block_name != strtolower($block_name))
		{
			$audit_messages[] = array('message_type' => 1019, 'resource' => "KVS", 'detail' => $block_name);
			break;
		}
	}

	// 20) default.php didn't load default lang
	if (strpos($default_lang, "\$theme_locale") !== false && strpos($default_lang, "\$config[project_path]/langs/{\$lang['theme']['lang']}.lang") === false)
	{
		$audit_messages[] = array('message_type' => 1020, 'resource' => "KVS");
	}
}

function auditCheckStorageServer($server)
{
	global $config, $audit_messages;

	$has_error = 0;
	$has_satellite_general_error = 0;
	foreach ($audit_messages as $audit_message)
	{
		if ($audit_message['message_type'] == 410)
		{
			$has_satellite_general_error = 1;
			break;
		}
	}
	auditLogMessage("Storage server \"$server[title]\" check started");

	$test_result = test_connection_detailed($server);
	if ($test_result == 1)
	{
		$audit_messages[] = ['message_type' => 400, 'resource' => $server['title'], 'resource_id' => $server['server_id']];
		$has_error = 1;
	} elseif ($test_result == 2)
	{
		$audit_messages[] = ['message_type' => 401, 'resource' => $server['title'], 'resource_id' => $server['server_id']];
		$has_error = 1;
	} elseif ($test_result == 3)
	{
		$audit_messages[] = ['message_type' => 402, 'resource' => $server['title'], 'resource_id' => $server['server_id']];
		$has_error = 1;
	} elseif ($test_result == 4)
	{
		$audit_messages[] = ['message_type' => 403, 'resource' => $server['title'], 'resource_id' => $server['server_id']];
		$has_error = 1;
	} elseif ($test_result == 5)
	{
		$audit_messages[] = ['message_type' => 411, 'resource' => $server['title'], 'resource_id' => $server['server_id']];
		$has_error = 1;
	} elseif ($server['is_remote'] == 1)
	{
		if (get_page('', $server['control_script_url'], '', '', 1, 0, 60, '') != 'connected.')
		{
			$audit_messages[] = ['message_type' => 404, 'resource' => $server['title'], 'resource_id' => $server['server_id']];
			$has_error = 1;
		} else
		{
			if (strpos($config['project_url'], 'https://') === 0 && strpos($server['control_script_url'], 'https://') === false)
			{
				$audit_messages[] = ['message_type' => 408, 'resource' => $server['title'], 'resource_id' => $server['server_id']];
				$has_error = 1;
			} elseif ($has_satellite_general_error == 0)
			{
				$secret_remote_key = $config['cv'];
				if ($config['cvr'])
				{
					$secret_remote_key = $config['cvr'];
				}
				$remote_path = get_page('', "$server[control_script_url]?action=path&cv=$secret_remote_key", '', '', 1, 0, 60, '');
				if (strpos($remote_path, 'Access denied') !== false)
				{
					if ($config['is_clone_db'] == 'true')
					{
						$audit_messages[] = ['message_type' => 410, 'resource' => '[kt|b]/%ROOT%[/kt|b]/admin/include/setup.php'];
						$has_satellite_general_error = 1;
						$has_error = 1;
					} else
					{
						$audit_messages[] = ['message_type' => 409, 'resource' => $server['title'], 'resource_id' => $server['server_id']];
						$has_error = 1;
					}
				} else
				{
					$remote_time = intval(get_page('', "$server[control_script_url]?action=time", '', '', 1, 0, 60, ''));
					if ($remote_time > 0)
					{
						if ($remote_time < time() + floatval($server['time_offset']) * 3600 - 300 || $remote_time > time() + floatval($server['time_offset']) * 3600 + 300)
						{
							$audit_messages[] = ['message_type' => 405, 'resource' => $server['title'], 'resource_id' => $server['server_id'], 'detail' => "Local time: " . date('Y-m-d H:i:s') . " (+" . (floatval($server['time_offset']) * 3600) . " offset)\nRemote time: " . date('Y-m-d H:i:s', $remote_time)];
							$has_error = 1;
						}
					}
				}
			}
		}
	}
	if (intval($server['streaming_type_id']) == 4)
	{
		if (!is_file("$config[project_path]/admin/cdn/$server[streaming_script]"))
		{
			$audit_messages[] = ['message_type' => 407, 'resource' => $server['title'], 'resource_id' => $server['server_id']];
			$has_error = 1;
		}
	}
	if ($has_error == 0 && $has_satellite_general_error == 0)
	{
		if ($server['content_type_id'] == 1)
		{
			$validation_result = validate_server_operation_videos($server);
		} elseif ($server['content_type_id'] == 2)
		{
			$validation_result = validate_server_operation_albums($server);
		}
		if (isset($validation_result))
		{
			foreach ($validation_result as $validation_item)
			{
				if (array_cnt($validation_item['checks']) > 0)
				{
					foreach ($validation_item['checks'] as $check)
					{
						if ($check['is_error'] == 1 && $check['type'] <> 'direct_link')
						{
							$audit_messages[] = ['message_type' => 406, 'resource' => $server['title'], 'resource_id' => $server['server_id'], 'detail' => $check['details']];
							break 2;
						}
					}
				}
			}
		}
	}
}

function auditCheckConversionServer($server)
{
	global $config,$audit_messages;

	auditLogMessage("Conversion server \"$server[title]\" check started");

	$test_result=test_connection_detailed($server);
	if ($test_result==1)
	{
		$audit_messages[]=array('message_type'=>500,'resource'=>$server['title'],'resource_id'=>$server['server_id']);
	} elseif ($test_result==2)
	{
		$audit_messages[]=array('message_type'=>501,'resource'=>$server['title'],'resource_id'=>$server['server_id']);
	} elseif ($test_result==3)
	{
		$audit_messages[]=array('message_type'=>502,'resource'=>$server['title'],'resource_id'=>$server['server_id']);
	} elseif ($test_result==4)
	{
		$audit_messages[]=array('message_type'=>503,'resource'=>$server['title'],'resource_id'=>$server['server_id']);
	} else {
		$rnd=mt_rand(10000000,99999999);
		mkdir("$config[temporary_path]/$rnd",0777);
		chmod("$config[temporary_path]/$rnd",0777);
		get_file('heartbeat.dat','/',"$config[temporary_path]/$rnd",$server);
		$heartbeat=@unserialize(@file_get_contents("$config[temporary_path]/$rnd/heartbeat.dat"));
		if (!is_array($heartbeat))
		{
			$audit_messages[]=array('message_type'=>504,'resource'=>$server['title'],'resource_id'=>$server['server_id']);
		} else {
			$heartbeat_date=date("Y-m-d H:i",$heartbeat['time']);
			if (time()-strtotime($heartbeat_date)>900)
			{
				$audit_messages[]=array('message_type'=>505,'resource'=>$server['title'],'resource_id'=>$server['server_id']);
			}
		}
		@unlink("$config[temporary_path]/$rnd/heartbeat.dat");
		@unlink("$config[temporary_path]/$rnd");
	}
}

function auditCheckBlock($block)
{
	global $config,$audit_messages;

	$error=0;
	$detail='';
	if (is_file("$config[project_path]/blocks/$block/$block.php") && is_file("$config[project_path]/blocks/$block/$block.dat"))
	{
		exec("$config[php_path] -l $config[project_path]/blocks/$block/$block.php", $res, $response_code);
		if ($response_code == 255)
		{
			$error=1;
			$detail=trim(implode("\n",$res));
		} else {
			require_once("$config[project_path]/blocks/$block/$block.php");
			if (function_exists("{$block}Show")===false || function_exists("{$block}GetHash")===false || function_exists("{$block}MetaData")===false)
			{
				$error=1;
			}
		}
	} else {
		$error=1;
	}
	if ($error==1)
	{
		$audit_messages[]=array('message_type'=>30,'resource'=>$block,'detail'=>$detail);
	}
	auditSleep();
}

function auditCheckPage($page)
{
	global $config,$audit_messages;

	$page_error=0;
	$page_error_cache=0;
	$page_warning_permission=0;
	$page_warning_cache=0;
	$page_warning_no_cache=0;
	$page_warning_php=0;
	$page_warning_var_from=0;
	$page_info_disabled=0;

	$validation_errors=validate_page($page['external_id'],'','',false,false,true);
	foreach ($validation_errors as $validation_error)
	{
		switch ($validation_error['type'])
		{
			case 'page_external_id_empty':
			case 'page_external_id_invalid':
			case 'page_state_invalid':
			case 'page_template_empty':
			case 'page_component_external_id_invalid':
			case 'page_component_insert_block':
			case 'page_component_unknown':
			case 'advertising_spot_unknown':
			case 'block_id_invalid':
			case 'block_state_invalid':
			case 'block_name_invalid':
			case 'block_name_duplicate':
			case 'block_circular_insert_block':
			case 'block_circular_insert_global':
			case 'global_block_uid_invalid':
			case 'file_missing':
			case 'dir_missing':
				$page_error=1;
				break;
			case 'fs_permissions':
				if ($validation_error['global_uid']=='')
				{
					$page_warning_permission=1;
				}
				break;
			case 'page_template_smarty_get_usage':
			case 'page_template_smarty_request_usage':
				$page_error_cache=1;
				break;
			case 'block_template_smarty_session_usage':
			case 'block_template_smarty_session_status_usage':
			case 'block_template_smarty_get_usage':
			case 'block_template_smarty_request_usage':
				if ($validation_error['include']=='')
				{
					$page_error_cache=1;
				} else {
					$page_warning_cache=1;
				}
				break;
			case 'var_from_duplicate':
				$page_warning_var_from=1;
				break;
			case 'page_template_php':
			case 'page_component_template_php':
			case 'block_template_php':
				$page_warning_php=1;
				break;
			case 'block_cache_time_zero':
				$page_warning_no_cache=1;
				break;
			case 'page_disabled':
				$page_info_disabled=1;
				break;
		}
	}

	if ($page_info_disabled)
	{
		$audit_messages[]=array('message_type'=>710,'resource'=>$page['title'],'resource_id'=>$page['external_id']);
	}
	if ($page_error==1)
	{
		$audit_messages[]=array('message_type'=>711,'resource'=>$page['title'],'resource_id'=>$page['external_id']);
	} else {
		if ($page_error_cache==1)
		{
			$audit_messages[]=array('message_type'=>712,'resource'=>$page['title'],'resource_id'=>$page['external_id']);
		} elseif ($page_warning_cache==1)
		{
			$audit_messages[]=array('message_type'=>718,'resource'=>$page['title'],'resource_id'=>$page['external_id']);
		}

		if ($page_warning_no_cache==1)
		{
			$audit_messages[]=array('message_type'=>713,'resource'=>$page['title'],'resource_id'=>$page['external_id']);
		}
		if ($page_warning_permission==1)
		{
			$audit_messages[]=array('message_type'=>714,'resource'=>$page['title'],'resource_id'=>$page['external_id']);
		}
		if ($page_warning_php==1)
		{
			$audit_messages[]=array('message_type'=>715,'resource'=>$page['title'],'resource_id'=>$page['external_id']);
		}
		if ($page_warning_var_from==1)
		{
			if ($config['is_pagination_3.0']=='true')
			{
				$audit_messages[]=array('message_type'=>716,'resource'=>$page['title'],'resource_id'=>$page['external_id']);
			} else {
				$audit_messages[]=array('message_type'=>717,'resource'=>$page['title'],'resource_id'=>$page['external_id']);
			}
		}
	}
	auditSleep();
}

function auditCheckGlobalBlocks()
{
	global $config,$audit_messages,$templates_data;

	$template_global_blocks='';
	if (is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
	{
		$temp=explode("||",@file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
		$global_blocks=explode("|AND|",trim($temp[2]));
		foreach ($global_blocks as $global_block)
		{
			if ($global_block=='')
			{
				continue;
			}
			$block_id=substr($global_block,0,strpos($global_block,"[SEP]"));
			$block_display_name=ucwords(str_replace('_',' ',substr($global_block,strpos($global_block,"[SEP]")+5)));
			$template_global_blocks.="{{insert name=\"getBlock\" block_id=\"$block_id\" block_name=\"$block_display_name\"}}\n";
		}
	}
	$templates_data['$global.tpl']=get_site_parsed_template($template_global_blocks);

	$page_error=0;
	$page_error_cache=0;
	$page_warning_cache=0;
	$page_warning_permission=0;
	$page_warning_php=0;
	$page_warning_no_cache=0;

	$validation_errors=validate_page('$global',$template_global_blocks,'',false,false,true);
	foreach ($validation_errors as $validation_error)
	{
		switch ($validation_error['type'])
		{
			case 'block_id_invalid':
			case 'block_state_invalid':
			case 'block_name_invalid':
			case 'block_name_duplicate':
			case 'block_template_empty':
			case 'block_circular_insert_block':
			case 'block_circular_insert_global':
			case 'page_component_external_id_invalid':
			case 'page_component_unknown':
			case 'advertising_spot_unknown':
			case 'file_missing':
			case 'dir_missing':
				$page_error=1;
				break;
			case 'fs_permissions':
				$page_warning_permission=1;
				break;
			case 'block_template_smarty_session_usage':
			case 'block_template_smarty_session_status_usage':
			case 'block_template_smarty_get_usage':
			case 'block_template_smarty_request_usage':
				if ($validation_error['include']=='')
				{
					$page_error_cache=1;
				} else {
					$page_warning_cache=1;
				}
				break;
			case 'block_template_php':
				$page_warning_php=1;
				break;
			case 'block_cache_time_zero':
				$page_warning_no_cache=1;
				break;
		}
	}

	if ($page_error==1)
	{
		$audit_messages[]=array('message_type'=>720);
	} else {
		if ($page_error_cache==1)
		{
			$audit_messages[]=array('message_type'=>721);
		} elseif ($page_warning_cache==1)
		{
			$audit_messages[]=array('message_type'=>725);
		}

		if ($page_warning_no_cache==1)
		{
			$audit_messages[]=array('message_type'=>722);
		}
		if ($page_warning_permission==1)
		{
			$audit_messages[]=array('message_type'=>723);
		}
		if ($page_warning_php==1)
		{
			$audit_messages[]=array('message_type'=>724);
		}
	}
	auditSleep();
}

function auditCheckPageComponent($page_component)
{
	global $audit_messages;

	$component_error=0;
	$component_warning_permission=0;
	$component_warning_php=0;
	$component_warning_empty_template=0;

	$validation_errors=validate_page_component(str_replace(".tpl","",$page_component),'',false,false);
	foreach ($validation_errors as $validation_error)
	{
		switch ($validation_error['type'])
		{
			case 'page_component_external_id_empty':
			case 'page_component_external_id_invalid':
			case 'page_component_insert_block':
			case 'page_component_insert_global':
			case 'global_block_uid_invalid':
			case 'page_component_unknown':
			case 'advertising_spot_unknown':
			case 'file_missing':
				$component_error=1;
				break;
			case 'fs_permissions':
				$component_warning_permission=1;
				break;
			case 'page_component_template_php':
				$component_warning_php=1;
				break;
			case 'page_component_template_empty':
				$component_warning_empty_template=1;
				break;
		}
	}

	if ($component_error==1)
	{
		$audit_messages[]=array('message_type'=>700,'resource'=>$page_component,'resource_id'=>$page_component);
	} else
	{
		if ($component_warning_permission==1)
		{
			$audit_messages[]=array('message_type'=>701,'resource'=>$page_component,'resource_id'=>$page_component);
		}
		if ($component_warning_php==1)
		{
			$audit_messages[]=array('message_type'=>702,'resource'=>$page_component,'resource_id'=>$page_component);
		}
		if ($component_warning_empty_template==1)
		{
			$audit_messages[]=array('message_type'=>703,'resource'=>$page_component,'resource_id'=>$page_component);
		}
	}
	auditSleep();
}

function auditCheckAdvertisingSpot($external_id)
{
	global $config,$audit_messages;

	$spot_data_file="$config[project_path]/admin/data/advertisements/spot_$external_id.dat";
	if (!unserialize(file_get_contents($spot_data_file)))
	{
		$audit_messages[]=array('message_type'=>730,'resource'=>$external_id,'resource_id'=>$external_id);
	} elseif (!is_writable($spot_data_file))
	{
		$audit_messages[]=array('message_type'=>731,'resource'=>$external_id,'resource_id'=>$external_id);
	}
	auditSleep();
}

function auditCheckVideoContent($video, $check_streaming, $check_embedded)
{
	global $config, $audit_messages, $formats_screenshots, $formats_videos, $storage_servers;

	$video_id = $video['video_id'];
	$dir_path = get_dir_by_id($video_id);
	$formats = get_video_formats($video_id, $video['file_formats'], $video['server_group_id']);

	$error_id_general = 0;
	$error_id_video_files = 0;
	$error_id_screenshots_overview = 0;
	$error_id_screenshots_timelines = 0;
	$error_id_screenshots_posters = 0;
	if ($video['status_id'] == 1)
	{
		if (trim($video['title']) == '' || trim($video['dir']) == '')
		{
			$audit_messages[] = array('message_type' => 802, 'resource' => 'video', 'resource_id' => $video_id);
			$error_id_general = 0b1;
		}
	}
	if ($video['server_group_id'] > 0)
	{
		foreach ($storage_servers as $server)
		{
			if ($video['server_group_id'] == $server['group_id'])
			{
				$validation_result = validate_server_videos($server, array($video));
				if ($validation_result <> 1)
				{
					$audit_messages[] = array('message_type' => 100, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => $server['title'] . ': ' . $validation_result);
					$error_id_video_files = 0b10;
					break;
				} elseif ($check_streaming == 1)
				{
					$time = time();
					foreach ($formats_videos as $format_video)
					{
						if ($format_video['access_level_id'] == 0 && isset($formats[$format_video['postfix']]))
						{
							$format_rec = $formats[$format_video['postfix']];
							$url = "$format_rec[file_url]?admin_rq_server_id=$server[server_id]&ttl=$time&dsc=" . md5("$config[cv]/$format_rec[file_path]/$time");

							unset($headers);
							if (!is_binary_file_url($url, true, $config['project_url'], $headers))
							{
								$audit_messages[] = array('message_type' => 108, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$url\n\n$headers");
								$error_id_video_files = 0b10;
								break 2;
							}
						}
					}
				}
			}
		}
	} elseif ($video['load_type_id'] == 2)
	{
		if ($video['file_url'] == '')
		{
			$audit_messages[] = array('message_type' => 109, 'resource' => $video_id, 'resource_id' => $video_id);
			$error_id_general = 0b1;
		} elseif (strpos($config['project_url'], 'https://') === 0 && strpos($video['file_url'], 'http://') === 0)
		{
			$audit_messages[] = array('message_type' => 115, 'resource' => $video_id, 'resource_id' => $video_id);
			$error_id_general = 0b1;
		} elseif ($check_embedded == 1)
		{
			unset($headers);
			if (!is_binary_file_url($video['file_url'], false, $config['project_url'], $headers))
			{
				$audit_messages[] = array('message_type' => 110, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$video[file_url]\n\n$headers");
				$error_id_general = 0b1;
			}
		}
	} elseif ($video['load_type_id'] == 3)
	{
		if ($video['embed'] == '')
		{
			$audit_messages[] = array('message_type' => 111, 'resource' => $video_id, 'resource_id' => $video_id);
			$error_id_general = 0b1;
		} elseif (strpos($config['project_url'], 'https://') === 0 && strpos($video['embed'], 'src="http://') !== false)
		{
			$audit_messages[] = array('message_type' => 116, 'resource' => $video_id, 'resource_id' => $video_id);
			$error_id_general = 0b1;
		} elseif ($check_embedded == 1)
		{
			if (strpos($video['embed'], '<iframe') !== false)
			{
				unset($temp);
				preg_match("|src\ *=\ *['\"]([^'\"]+)['\"]|is", $video['embed'], $temp);
				$embed_url = trim($temp[1]);
				unset($headers);
				if (!is_working_url($embed_url, $config['project_url'], $headers))
				{
					$audit_messages[] = array('message_type' => 112, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$embed_url\n\n$headers");
					$error_id_general = 0b1;
				} elseif ($video['gallery_url'] != '')
				{
					unset($headers);
					if (!is_working_url($video['gallery_url'], '', $headers))
					{
						$audit_messages[] = array('message_type' => 112, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$video[gallery_url]\n\n$headers");
						$error_id_general = 0b1;
					}
				}
			}
		}
	} elseif ($video['load_type_id'] == 5)
	{
		if ($video['pseudo_url'] == '')
		{
			$audit_messages[] = array('message_type' => 113, 'resource' => $video_id, 'resource_id' => $video_id);
			$error_id_general = 0b1;
		} elseif ($check_embedded == 1)
		{
			unset($headers);
			if (!is_working_url($video['pseudo_url'], '', $headers))
			{
				$audit_messages[] = array('message_type' => 114, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$video[pseudo_url]\n\n$headers");
				$error_id_general = 0b1;
			}
		}
	}

	mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id");
	if (!is_dir("$config[content_path_videos_sources]/$dir_path/$video_id") || !is_writable("$config[content_path_videos_sources]/$dir_path/$video_id"))
	{
		$audit_messages[] = array('message_type' => 101, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_sources]/$dir_path/$video_id");
		$error_id_general = 0b1;
	}

	for ($i = 1; $i <= $video['screen_amount']; $i++)
	{
		if (sprintf("%.0f", @filesize("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg")) < 1)
		{
			$audit_messages[] = array('message_type' => 102, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg");
			$error_id_screenshots_overview = 0b100;
			break;
		} elseif (function_exists('getimagesize'))
		{
			$size = @getimagesize("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg");
			if (!is_array($size) || $size[0] < 1 || $size[1] < 1)
			{
				$audit_messages[] = array('message_type' => 106, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg");
				$error_id_screenshots_overview = 0b100;
				break;
			}
		}
		foreach ($formats_screenshots as $format)
		{
			if ($format['group_id'] == 1)
			{
				if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg")) < 1)
				{
					$audit_messages[] = array('message_type' => 102, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg");
					$error_id_screenshots_overview = 0b100;
					break 2;
				} elseif (function_exists('getimagesize'))
				{
					$size = @getimagesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg");
					if ($format['size'] <> 'source')
					{
						$format_size = explode("x", trim($format['size']));
						if (!is_array($size) || ($format['aspect_ratio_id'] <> 3 && ($size[0] <> $format_size[0] || $size[1] <> $format_size[1])) || ($format['aspect_ratio_id'] == 3 && ($size[0] == 0 || $size[1] == 0)))
						{
							$audit_messages[] = array('message_type' => 106, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg");
							$error_id_screenshots_overview = 0b100;
							break 2;
						}
					}
				}
			}
		}
	}
	foreach ($formats_screenshots as $format)
	{
		if ($format['group_id'] == 1)
		{
			if ($format['is_create_zip'] == 1)
			{
				if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$video_id-$format[size].zip")) < 1)
				{
					$audit_messages[] = array('message_type' => 107, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$video_id-$format[size].zip");
					$error_id_screenshots_overview = 0b100;
					break;
				}
			}
		}
	}

	if ($video['poster_amount'] > 0)
	{
		for ($i = 1; $i <= $video['poster_amount']; $i++)
		{
			if (sprintf("%.0f", @filesize("$config[content_path_videos_sources]/$dir_path/$video_id/posters/$i.jpg")) < 1)
			{
				$audit_messages[] = array('message_type' => 102, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_sources]/$dir_path/$video_id/posters/$i.jpg");
				$error_id_screenshots_posters = 0b10000;
				break;
			} elseif (function_exists('getimagesize'))
			{
				$size = @getimagesize("$config[content_path_videos_sources]/$dir_path/$video_id/posters/$i.jpg");
				if (!is_array($size) || $size[0] < 1 || $size[1] < 1)
				{
					$audit_messages[] = array('message_type' => 106, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_sources]/$dir_path/$video_id/posters/$i.jpg");
					$error_id_screenshots_posters = 0b10000;
					break;
				}
			}
			foreach ($formats_screenshots as $format)
			{
				if ($format['group_id'] == 3)
				{
					if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$i.jpg")) < 1)
					{
						$audit_messages[] = array('message_type' => 102, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$i.jpg");
						$error_id_screenshots_posters = 0b10000;
						break 2;
					} elseif (function_exists('getimagesize'))
					{
						$size = @getimagesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$i.jpg");
						if ($format['size'] <> 'source')
						{
							$format_size = explode("x", trim($format['size']));
							if (!is_array($size) || ($format['aspect_ratio_id'] <> 3 && ($size[0] <> $format_size[0] || $size[1] <> $format_size[1])) || ($format['aspect_ratio_id'] == 3 && ($size[0] == 0 || $size[1] == 0)))
							{
								$audit_messages[] = array('message_type' => 106, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$i.jpg");
								$error_id_screenshots_posters = 0b10000;
								break 2;
							}
						}
					}
				}
			}
		}
		foreach ($formats_screenshots as $format)
		{
			if ($format['group_id'] == 3)
			{
				if ($format['is_create_zip'] == 1)
				{
					if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$video_id-$format[size].zip")) < 1)
					{
						$audit_messages[] = array('message_type' => 107, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]/$video_id-$format[size].zip");
						$error_id_screenshots_posters = 0b10000;
						break;
					}
				}
			}
		}
	}

	foreach ($formats as $format_rec)
	{
		if ($format_rec['timeline_screen_amount'] > 0)
		{
			foreach ($formats_videos as $format_video)
			{
				if ($format_video['postfix'] == $format_rec['postfix'])
				{
					$timeline_dir = $format_video['timeline_directory'];
					for ($i = 1; $i <= $format_rec['timeline_screen_amount']; $i++)
					{
						if (sprintf("%.0f", @filesize("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir/$i.jpg")) < 1)
						{
							$audit_messages[] = array('message_type' => 102, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir/$i.jpg");
							$error_id_screenshots_timelines = 0b1000;
							break 3;
						}
						foreach ($formats_screenshots as $format)
						{
							if ($format['group_id'] == 2)
							{
								if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$i.jpg")) < 1)
								{
									$audit_messages[] = array('message_type' => 102, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$i.jpg");
									$error_id_screenshots_timelines = 0b1000;
									break 4;
								} elseif (function_exists('getimagesize'))
								{
									$size = @getimagesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$i.jpg");
									if ($format['size'] <> 'source')
									{
										$format_size = explode("x", trim($format['size']));
										if (!is_array($size) || ($format['aspect_ratio_id'] <> 3 && ($size[0] <> $format_size[0] || $size[1] <> $format_size[1])) || ($format['aspect_ratio_id'] == 3 && ($size[0] == 0 || $size[1] == 0)))
										{
											$audit_messages[] = array('message_type' => 106, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$i.jpg");
											$error_id_screenshots_timelines = 0b1000;
											break 4;
										}
									}
								}
							}
						}
					}
					foreach ($formats_screenshots as $format)
					{
						if ($format['group_id'] == 2)
						{
							if ($format['is_create_zip'] == 1)
							{
								if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$video_id-$format[size].zip")) < 1)
								{
									$audit_messages[] = array('message_type' => 107, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format[size]/$video_id-$format[size].zip");
									$error_id_screenshots_timelines = 0b1000;
									break;
								}
							}
						}
					}
					if ($format_rec['timeline_cuepoints'] > 0)
					{
						if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/cuepoints.json")) < 1)
						{
							$audit_messages[] = array('message_type' => 104, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/cuepoints.json");
							$error_id_screenshots_timelines = 0b1000;
						}
					}
				}
			}
		}
	}
	if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg")) < 1)
	{
		$audit_messages[] = array('message_type' => 105, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg");
		$error_id_screenshots_overview = 0b100;
	} else
	{
		foreach ($formats as $format_rec)
		{
			if (sprintf("%.0f", @filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$format_rec['postfix']}.jpg")) < 1)
			{
				$audit_messages[] = array('message_type' => 105, 'resource' => $video_id, 'resource_id' => $video_id, 'detail' => "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$format_rec['postfix']}.jpg");
				$error_id_screenshots_overview = 0b100;
				break;
			}
		}
	}

	$error_id = $error_id_general + $error_id_video_files + $error_id_screenshots_overview + $error_id_screenshots_timelines + $error_id_screenshots_posters;
	if ($error_id > 0)
	{
		sql_pr("update $config[tables_prefix]videos set has_errors=? where video_id=?", $error_id, $video_id);
	} else
	{
		sql_pr("update $config[tables_prefix]videos set has_errors=0 where video_id=?", $video_id);
	}
	auditSleep();
}

function auditCheckVideoFormat($format)
{
	global $config,$audit_messages;

	if ($format['status_id'] == 1)
	{
		$where_type = " and format_video_group_id=$format[format_video_group_id]";
		$videos_with_error = mr2array_list(sql("select video_id from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats not like '%||$format[postfix]|%' $where_type"));
		if (array_cnt($videos_with_error) > 0)
		{
			$video_ids = implode(", ", array_map("intval", $videos_with_error));
			$audit_messages[] = array('message_type' => 120, 'resource' => $format['title'], 'resource_id' => $format['format_video_id'], 'detail' => "$video_ids");
		}
	}
	auditSleep();
}

function auditCheckAlbumContent($album)
{
	global $config, $audit_messages, $formats_albums, $storage_servers;

	$album_id = $album['album_id'];

	$error_id_general = 0;
	$error_id_album_files = 0;
	if ($album['status_id'] == 1)
	{
		if (trim($album['title']) == '' || trim($album['dir']) == '')
		{
			$audit_messages[] = array('message_type' => 802, 'resource' => 'album', 'resource_id' => $album_id);
			$error_id_general = 0b1;
		}
	}

	$dir_path = get_dir_by_id($album_id);
	if (is_dir("$config[content_path_albums_sources]/$dir_path/$album_id"))
	{
		if (!is_writable("$config[content_path_albums_sources]/$dir_path/$album_id"))
		{
			$audit_messages[] = array('message_type' => 201, 'resource' => $album_id, 'resource_id' => $album_id, 'detail' => "$config[content_path_albums_sources]/$dir_path/$album_id");
			$error_id_general = 0b1;
		}
	} elseif (is_dir("$config[content_path_albums_sources]/$dir_path"))
	{
		if (!is_writable("$config[content_path_albums_sources]/$dir_path"))
		{
			$audit_messages[] = array('message_type' => 201, 'resource' => $album_id, 'resource_id' => $album_id, 'detail' => "$config[content_path_albums_sources]/$dir_path");
			$error_id_general = 0b1;
		}
	}

	$images = mr2array(sql("select album_id, image_id, image_formats from $config[tables_prefix]albums_images where album_id=$album_id"));
	foreach ($storage_servers as $server)
	{
		if ($album['server_group_id'] == $server['group_id'])
		{
			$validation_result = validate_server_albums($server, array($album), $formats_albums);
			if ($validation_result <> 1)
			{
				$audit_messages[] = array('message_type' => 200, 'resource' => $album_id, 'resource_id' => $album_id, 'detail' => $server['title'] . ': ' . $validation_result);
				$error_id_album_files = 0b10;
			}
			$validation_result = validate_server_images($server, $images);
			if ($validation_result <> 1)
			{
				$audit_messages[] = array('message_type' => 200, 'resource' => $album_id, 'resource_id' => $album_id, 'detail' => $server['title'] . ': ' . $validation_result);
				$error_id_album_files = 0b10;
				break;
			}
		}
	}

	$error_id = $error_id_general + $error_id_album_files;
	if ($error_id > 0)
	{
		sql_pr("update $config[tables_prefix]albums set has_errors=? where album_id=?", $error_id, $album_id);
	} else
	{
		sql_pr("update $config[tables_prefix]albums set has_errors=0 where album_id=?", $album_id);
	}
	auditSleep();
}

function auditCheckObjectFiles($object_id,$object_type,$images,$files)
{
	global $audit_messages;

	$missing_files=array();
	$invalid_images=array();
	foreach ($images as $image)
	{
		if (sprintf("%.0f",filesize($image))<1)
		{
			$missing_files[]=$image;
		} elseif (function_exists('getimagesize')) {
			$size=getimagesize($image);
			if (!is_array($size) || $size[0]<1 || $size[1]<1)
			{
				$invalid_images[]=$image;
			}
		}
	}
	foreach ($files as $file)
	{
		if (sprintf("%.0f",filesize($file))<1)
		{
			$missing_files[]=$file;
		}
	}
	if (array_cnt($missing_files)>0)
	{
		$audit_messages[]=array('message_type'=>800,'resource'=>$object_type,'resource_id'=>$object_id,'detail'=>implode("\n",$missing_files));
	}
	if (array_cnt($invalid_images)>0)
	{
		$audit_messages[]=array('message_type'=>801,'resource'=>$object_type,'resource_id'=>$object_id,'detail'=>implode("\n",$invalid_images));
	}
}

function auditCheckCategory($obj)
{
	global $config,$audit_messages;

	$images=array();
	$files=array();

	$obj_id=$obj['category_id'];

	if (trim($obj['title'])=='' || trim($obj['dir'])=='')
	{
		$audit_messages[]=array('message_type'=>802,'resource'=>'category','resource_id'=>$obj_id);
	}

	if ($obj['screenshot1']!='')
	{
		$images[]="$config[content_path_categories]/$obj_id/$obj[screenshot1]";
	}
	if ($obj['screenshot2']!='')
	{
		$images[]="$config[content_path_categories]/$obj_id/$obj[screenshot2]";
	}
	for ($i=1;$i<=10;$i++)
	{
		if ($obj["custom_file$i"]!='')
		{
			$files[]="$config[content_path_categories]/$obj_id/{$obj["custom_file$i"]}";
		}
	}
	auditCheckObjectFiles($obj_id,'category',$images,$files);
	auditSleep();
}

function auditCheckCategoryGroup($obj)
{
	global $config,$audit_messages;

	$images=array();
	$files=array();

	$obj_id=$obj['category_group_id'];

	if (trim($obj['title'])=='' || trim($obj['dir'])=='')
	{
		$audit_messages[]=array('message_type'=>802,'resource'=>'category_group','resource_id'=>$obj_id);
	}

	if ($obj['screenshot1']!='')
	{
		$images[]="$config[content_path_categories]/groups/{$obj_id}/$obj[screenshot1]";
	}
	if ($obj['screenshot2']!='')
	{
		$images[]="$config[content_path_categories]/groups/{$obj_id}/$obj[screenshot2]";
	}
	auditCheckObjectFiles($obj_id,'category_group',$images,$files);
	auditSleep();
}

function auditCheckModel($obj)
{
	global $config,$audit_messages;

	$images=array();
	$files=array();

	$obj_id=$obj['model_id'];

	if (trim($obj['title'])=='' || trim($obj['dir'])=='')
	{
		$audit_messages[]=array('message_type'=>802,'resource'=>'model','resource_id'=>$obj_id);
	}

	if ($obj['screenshot1']!='')
	{
		$images[]="$config[content_path_models]/$obj_id/$obj[screenshot1]";
	}
	if ($obj['screenshot2']!='')
	{
		$images[]="$config[content_path_models]/$obj_id/$obj[screenshot2]";
	}
	for ($i=1;$i<=10;$i++)
	{
		if ($obj["custom_file$i"]!='')
		{
			$files[]="$config[content_path_models]/$obj_id/{$obj["custom_file$i"]}";
		}
	}
	auditCheckObjectFiles($obj_id,'model',$images,$files);
	auditSleep();
}

function auditCheckModelGroup($obj)
{
	global $config,$audit_messages;

	$images=array();
	$files=array();

	$obj_id=$obj['model_group_id'];

	if (trim($obj['title'])=='' || trim($obj['dir'])=='')
	{
		$audit_messages[]=array('message_type'=>802,'resource'=>'model_group','resource_id'=>$obj_id);
	}

	if ($obj['screenshot1']!='')
	{
		$images[]="$config[content_path_models]/groups/{$obj_id}/$obj[screenshot1]";
	}
	if ($obj['screenshot2']!='')
	{
		$images[]="$config[content_path_models]/groups/{$obj_id}/$obj[screenshot2]";
	}
	auditCheckObjectFiles($obj_id,'model_group',$images,$files);
	auditSleep();
}

function auditCheckContentSource($obj)
{
	global $config,$audit_messages;

	$images=array();
	$files=array();

	$obj_id=$obj['content_source_id'];

	if (trim($obj['title'])=='' || trim($obj['dir'])=='' || trim($obj['url'])=='')
	{
		$audit_messages[]=array('message_type'=>802,'resource'=>'content_source','resource_id'=>$obj_id);
	}

	if ($obj['screenshot1']!='')
	{
		$images[]="$config[content_path_content_sources]/$obj_id/$obj[screenshot1]";
	}
	if ($obj['screenshot2']!='')
	{
		$images[]="$config[content_path_content_sources]/$obj_id/$obj[screenshot2]";
	}
	for ($i=1;$i<=10;$i++)
	{
		if ($obj["custom_file$i"]!='')
		{
			$files[]="$config[content_path_content_sources]/$obj_id/{$obj["custom_file$i"]}";
		}
	}
	auditCheckObjectFiles($obj_id,'content_source',$images,$files);
	auditSleep();
}

function auditCheckDvd($obj)
{
	global $config,$audit_messages;

	$images=array();
	$files=array();

	$obj_id=$obj['dvd_id'];

	if (trim($obj['title'])=='' || trim($obj['dir'])=='')
	{
		$audit_messages[]=array('message_type'=>802,'resource'=>'dvd','resource_id'=>$obj_id);
	}

	if ($obj['cover1_front']!='')
	{
		$images[]="$config[content_path_dvds]/$obj_id/$obj[cover1_front]";
	}
	if ($obj['cover1_back']!='')
	{
		$images[]="$config[content_path_dvds]/$obj_id/$obj[cover1_back]";
	}
	if ($obj['cover2_front']!='')
	{
		$images[]="$config[content_path_dvds]/$obj_id/$obj[cover2_front]";
	}
	if ($obj['cover2_back']!='')
	{
		$images[]="$config[content_path_dvds]/$obj_id/$obj[cover2_back]";
	}
	for ($i=1;$i<=10;$i++)
	{
		if ($obj["custom_file$i"]!='')
		{
			$files[]="$config[content_path_dvds]/$obj_id/{$obj["custom_file$i"]}";
		}
	}
	auditCheckObjectFiles($obj_id,'dvd',$images,$files);
	auditSleep();
}

function auditCheckDvdGroup($obj)
{
	global $config,$audit_messages;

	$images=array();
	$files=array();

	$obj_id=$obj['dvd_group_id'];

	if (trim($obj['title'])=='' || trim($obj['dir'])=='')
	{
		$audit_messages[]=array('message_type'=>802,'resource'=>'dvd_group','resource_id'=>$obj_id);
	}

	if ($obj['cover1']!='')
	{
		$images[]="$config[content_path_dvds]/groups/$obj_id/$obj[cover1]";
	}
	if ($obj['cover2']!='')
	{
		$images[]="$config[content_path_dvds]/groups/$obj_id/$obj[cover2]";
	}
	for ($i=1;$i<=10;$i++)
	{
		if ($obj["custom_file$i"]!='')
		{
			$files[]="$config[content_path_dvds]/groups/$obj_id/{$obj["custom_file$i"]}";
		}
	}
	auditCheckObjectFiles($obj_id,'dvd_group',$images,$files);
	auditSleep();
}

function auditCheckPost($obj)
{
	global $config,$audit_messages;

	$images=array();
	$files=array();

	$obj_id=$obj['post_id'];
	$dir_path=get_dir_by_id($obj_id);

	if ($obj['status_id']==1)
	{
		if (trim($obj['title'])=='' || trim($obj['dir'])=='')
		{
			$audit_messages[]=array('message_type'=>802,'resource'=>'post','resource_id'=>$obj_id);
		}
	}
	if (trim($obj['content'])=='')
	{
		$audit_messages[]=array('message_type'=>802,'resource'=>'post','resource_id'=>$obj_id);
	}

	for ($i=1;$i<=10;$i++)
	{
		if ($obj["custom_file$i"]!='')
		{
			$files[]="$config[content_path_posts]/$dir_path/$obj_id/{$obj["custom_file$i"]}";
		}
	}
	auditCheckObjectFiles($obj_id,'post',$images,$files);
	auditSleep();
}

function auditCheckUser($obj)
{
	global $config,$audit_messages;

	$images=array();
	$files=array();

	$obj_id=$obj['user_id'];

	if (trim($obj['username'])=='' || trim($obj['email'])=='' || trim($obj['display_name'])=='')
	{
		$audit_messages[]=array('message_type'=>802,'resource'=>'user','resource_id'=>$obj_id);
	}

	if ($obj['avatar']!='')
	{
		$images[]="$config[content_path_avatars]/$obj[avatar]";
	}
	if ($obj['cover']!='')
	{
		$images[]="$config[content_path_avatars]/$obj[cover]";
	}
	auditCheckObjectFiles($obj_id,'user',$images,$files);
	auditSleep();
}

function auditCheckContentProtection()
{
	global $config,$audit_messages,$formats_videos,$options;

	$hotlink_info=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/hotlink_info.dat"));
	$has_hotlink_error=false;
	if ($hotlink_info['ENABLE_ANTI_HOTLINK']<>1)
	{
		$audit_messages[]=array('message_type'=>301);
		$has_hotlink_error=true;
	}
	foreach ($formats_videos as $format_video)
	{
		if ($format_video['is_hotlink_protection_disabled']==1)
		{
			$audit_messages[]=array('message_type'=>302,'resource'=>$format_video['title'],'resource_id'=>$format_video['format_video_id']);
		}
	}
	if ($options['KEEP_VIDEO_SOURCE_FILES']==1)
	{
		$video_id=mr2number(sql("select video_id from $config[tables_prefix]videos where status_id in (0,1) limit 1"));
		if ($video_id>0)
		{
			$dir_path=get_dir_by_id($video_id);

			$url="$config[content_url_videos_sources]/$dir_path/$video_id/screenshots/1.jpg";
			unset($headers);
			if (is_binary_file_url($url, false, '', $headers))
			{
				$audit_messages[]=array('message_type'=>303,'resource'=>str_replace($config['project_path'],'[kt|b]/%ROOT%[/kt|b]',$config['content_path_videos_sources']),'detail'=>"$url\n\n$headers");
			}
		}
	}
	if (!$has_hotlink_error)
	{
		$servers=mr2array(sql("select *, (select content_type_id from $config[tables_prefix]admin_servers_groups where group_id=$config[tables_prefix]admin_servers.group_id) as content_type_id from $config[tables_prefix]admin_servers order by rand()"));
		foreach ($servers as $server)
		{
			if ($server['streaming_type_id']==1 && $server['content_type_id']==1)
			{
				$audit_messages[]=array('message_type'=>300,'resource'=>$server['title'],'resource_id'=>$server['server_id']);
			} else {
				if ($server['content_type_id']==1) {
					$validation_result=validate_server_operation_videos($server);
					if (array_cnt($validation_result)>0)
					{
						foreach ($validation_result as $validation_item)
						{
							if (array_cnt($validation_item['checks'])>0)
							{
								foreach ($validation_item['checks'] as $check)
								{
									if ($check['type']=='direct_link' && $check['is_error']==1)
									{
										$audit_messages[]=array('message_type'=>304,'resource'=>$server['title'],'resource_id'=>$server['server_id'],'detail'=>$check['details']);
										break 2;
									}
								}
							}
						}
					}
				} elseif ($server['content_type_id']==2) {
					$validation_result=validate_server_operation_albums($server);
					if (array_cnt($validation_result)>0)
					{
						foreach ($validation_result as $validation_item)
						{
							if (array_cnt($validation_item['checks'])>0)
							{
								foreach ($validation_item['checks'] as $check)
								{
									if ($check['type']=='direct_link' && $check['is_error']==1)
									{
										if ($validation_item['is_sources']==1)
										{
											$audit_messages[]=array('message_type'=>305,'resource'=>$server['title'],'resource_id'=>$server['server_id'],'detail'=>$check['details']);
										} else {
											$audit_messages[]=array('message_type'=>306,'resource'=>$server['title'],'resource_id'=>$server['server_id'],'detail'=>$check['details']);
										}
										break 2;
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

function auditCheckSecuritySuspiciousFilesIn($file, $build_stamp, $subfolders = false, $exclude_subfolders = [])
{
	global $config, $audit_messages;

	$children = get_contents_from_dir($file, 1);
	foreach ($children as $child)
	{
		if (strpos($child, '.php') !== false || strpos($child, '.php3') !== false || strpos($child, '.phtml') !== false)
		{
			if (!isset($build_stamp[str_replace($config['project_path'], "", $file . '/' . $child)]))
			{
				if (str_replace($config['project_path'], "", $file . '/' . $child) == '/admin/smarty/plugins/modifier.date_format.php')
				{
					continue;
				}
				$file_contents = file_get_contents("$file/$child");
				if (strpos($child, '.php') === false || trim($file_contents) != '<?php require_once("admin/include/process_page.php");?>')
				{
					$audit_messages[] = array('message_type' => 602, 'resource' => str_replace($config['project_path'], '[kt|b]/%ROOT%[/kt|b]', $file . '/' . $child), 'resource_path' => str_replace($config['project_path'], '', $file . '/' . $child));

					unset($temp);
					preg_match_all('/[\'"][^\'"\n\r\s]+[\'"]/', $file_contents, $temp);
					foreach ($temp[0] as $line)
					{
						if (strlen($line) > 300)
						{
							$audit_messages[] = array('message_type' => 601, 'resource' => str_replace($config['project_path'], '[kt|b]/%ROOT%[/kt|b]', $file . '/' . $child), 'resource_path' => str_replace($config['project_path'], '', $file . '/' . $child));
							break;
						}
					}

					if (strpos($file_contents, 'eval') !== false || strpos($file_contents, 'base64_decode') !== false || strpos($file_contents, 'shell_exec') !== false || strpos($file_contents, 'call_user_func') !== false || strpos($file_contents, 'chr') !== false || strpos($file_contents, 'create_function') !== false)
					{
						$audit_messages[] = array('message_type' => 601, 'resource' => str_replace($config['project_path'], '[kt|b]/%ROOT%[/kt|b]', $file . '/' . $child), 'resource_path' => str_replace($config['project_path'], '', $file . '/' . $child));
					} elseif (preg_match('|[\'"][A-Za-z0-9+/]+==?[\'"]|', $file_contents))
					{
						$audit_messages[] = array('message_type' => 601, 'resource' => str_replace($config['project_path'], '[kt|b]/%ROOT%[/kt|b]', $file . '/' . $child), 'resource_path' => str_replace($config['project_path'], '', $file . '/' . $child));
					}
				}
			}
		}
	}

	if ($subfolders && is_dir($file))
	{
		$children = get_contents_from_dir($file, 2);
		foreach ($children as $child)
		{
			if (!in_array($child, $exclude_subfolders))
			{
				auditCheckSecuritySuspiciousFilesIn($file . '/' . $child, $build_stamp, true);
			}
		}
	}
}

function auditCheckSecuritySuspiciousFoldersIn($file, $allowed_folders = [])
{
	global $config, $audit_messages;

	$children = get_contents_from_dir($file, 2);
	foreach ($children as $child)
	{
		if (!in_array($child, $allowed_folders))
		{
			$contents = get_contents_from_dir("$file/$child", 0);
			foreach ($contents as $key => $value)
			{
				if (is_dir("$file/$child/$value"))
				{
					$contents[$key] = "DIR $value/";
				} elseif (stripos($value, '.php') !== false)
				{
					$contents[$key] = "PHP $value";
				} else
				{
					$contents[$key] = "--- $value";
				}
			}
			$audit_messages[] = array('message_type' => 606, 'resource' => str_replace($config['project_path'], '[kt|b]/%ROOT%[/kt|b]', $file . '/' . $child), 'detail' => implode("\n", $contents));
		}
	}
}

function auditCheckSecurityNoPublicAccess($folder, $url, $check_additional_exts = [])
{
	global $config, $audit_messages;

	if (is_file("$folder/.htaccess") && is_writable("$folder/.htaccess") && !is_writable("$config[project_path]/admin/include/setup.php"))
	{
		$audit_messages[] = array('message_type' => 609, 'resource' => str_replace($config['project_path'], '[kt|b]/%ROOT%[/kt|b]', "$folder/.htaccess"));
	}

	if (!is_writable($folder))
	{
		return;
	}

	$has_parent_error = false;
	if (!is_file("$folder/kvs_test_audit.php"))
	{
		file_put_contents("$folder/kvs_test_audit.php", "<?php echo 'kvs_test_audit';", LOCK_EX);
	}
	if (get_page("", "$url/kvs_test_audit.php", "", "", 1, 0, 10, "") == 'kvs_test_audit')
	{
		$headers = get_page("", "$url/kvs_test_audit.php", "", "", 0, 1, 10, "");
		$audit_messages[] = array('message_type' => 604, 'resource' => str_replace($config['project_path'], '[kt|b]/%ROOT%[/kt|b]', $folder), 'detail' => "$url/kvs_test_audit.php\n\n$headers");
		$has_parent_error = true;
	}
	@unlink("$folder/kvs_test_audit.php");

	if (!$has_parent_error)
	{
		foreach ($check_additional_exts as $check_additional_ext)
		{
			if (!is_file("$folder/kvs_test_audit.$check_additional_ext"))
			{
				file_put_contents("$folder/kvs_test_audit.$check_additional_ext", "kvs_test_audit", LOCK_EX);
			}
			if (get_page("", "$url/kvs_test_audit.$check_additional_ext", "", "", 1, 0, 10, "") == 'kvs_test_audit')
			{
				$headers = get_page("", "$url/kvs_test_audit.$check_additional_ext", "", "", 0, 1, 10, "");
				$audit_messages[] = array('message_type' => 605, 'resource' => str_replace($config['project_path'], '[kt|b]/%ROOT%[/kt|b]', $folder), 'detail' => "$url/kvs_test_audit.$check_additional_ext\n\n$headers");
				$has_parent_error = true;
			}
			@unlink("$folder/kvs_test_audit.$check_additional_ext");
		}
	}

	if (is_writable($folder) && !$has_parent_error)
	{
		if (mkdir("$folder/kvs_test_audit") || is_dir("$folder/kvs_test_audit"))
		{
			if (!is_file("$folder/kvs_test_audit/kvs_test_audit.php"))
			{
				file_put_contents("$folder/kvs_test_audit/kvs_test_audit.php", "<?php echo 'kvs_test_audit';", LOCK_EX);
				file_put_contents("$folder/kvs_test_audit/.htaccess", "Allow from all", LOCK_EX);
				if (get_page("", "$url/kvs_test_audit/kvs_test_audit.php", "", "", 1, 0, 10, "") == 'kvs_test_audit')
				{
					$headers = get_page("", "$url/kvs_test_audit/kvs_test_audit.php", "", "", 0, 1, 10, "");
					$audit_messages[] = array('message_type' => 604, 'resource' => str_replace($config['project_path'], '[kt|b]/%ROOT%[/kt|b]', "$folder/kvs_test_audit"), 'detail' => "$url/kvs_test_audit/kvs_test_audit.php\n\n$headers");
				}
			}
		}
		@unlink("$folder/kvs_test_audit/kvs_test_audit.php");
		@unlink("$folder/kvs_test_audit/.htaccess");
		@rmdir("$folder/kvs_test_audit");
	}
}

function auditCheckSecurity()
{
	global $config, $audit_messages;

	$build_stamp = [];
	if (is_file("$config[project_path]/admin/stamp/stamp_$config[project_version].php"))
	{
		require "$config[project_path]/admin/stamp/stamp_$config[project_version].php";
		if (is_dir("$config[project_path]/admin/stamp/patches"))
		{
			$patches = get_contents_from_dir("$config[project_path]/admin/stamp/patches", 1);
			foreach ($patches as $patch)
			{
				if (strpos($patch, "$config[project_version]_patch") !== false)
				{
					require "$config[project_path]/admin/stamp/patches/$patch";
				} elseif (in_array($patch, ['5.1.1_patch1.php', '5.1.1_patch2.php', '5.2.0_patch1.php', '5.2.0_patch2.php', '5.4.0_patch1.php', '5.5.0_patch1.php', '5.5.0_patch2.php', '5.5.0_patch3.php', '5.5.0_patch4.php', '5.5.0_patch5.php']) && filesize("$config[project_path]/admin/stamp/patches/$patch") < 1200)
				{
					$build_stamp["/admin/stamp/patches/$patch"] = 'ignore';
				}
			}
		}
	}

	$pages = get_site_pages();
	foreach ($pages as $page)
	{
		$build_stamp["/$page[external_id].php"] = 'ignore';
	}
	$build_stamp['/langs/default.php'] = 'ignore';

	if (is_writable("$config[project_path]/admin/include/setup.php"))
	{
		$audit_messages[] = ['message_type' => 607, 'resource' => 'Files'];
	} elseif (is_writable("$config[project_path]"))
	{
		$audit_messages[] = ['message_type' => 608, 'resource' => "$config[project_path]"];
	}

	if (!is_writable("$config[project_path]/admin/include/setup.php"))
	{
		auditCheckSecurityNoPublicAccess("$config[project_path]/tmp", "$config[project_url]/tmp", ['tmp']);
		auditCheckSecurityNoPublicAccess("$config[project_path]/langs", "$config[project_url]/langs", ['lang']);
		auditCheckSecurityNoPublicAccess("$config[project_path]/template", "$config[project_url]/template", ['tpl']);
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/data", "$config[project_url]/admin/data", ['dat', 'gz', 'tpl']);
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/logs", "$config[project_url]/admin/logs", ['txt']);
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/smarty", "$config[project_url]/admin/smarty");
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/smarty/cache", "$config[project_url]/admin/smarty/cache");
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/smarty/template-c-site", "$config[project_url]/admin/smarty/template-c-site");
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/smarty/template-c", "$config[project_url]/admin/smarty/template-c");
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/stamp", "$config[project_url]/admin/stamp");
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/template", "$config[project_url]/admin/template");
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/tools", "$config[project_url]/admin/tools");
		auditCheckSecurityNoPublicAccess("$config[project_path]/admin/plugins", "$config[project_url]/admin/plugins");
		auditCheckSecurityNoPublicAccess("$config[project_path]/blocks", "$config[project_url]/blocks");

		auditCheckSecurityNoPublicAccess("$config[project_path]/contents", "$config[project_url]/contents");
		$list_content_folders = get_contents_from_dir("$config[project_path]/contents", 2);
		foreach ($list_content_folders as $content_folder)
		{
			auditCheckSecurityNoPublicAccess("$config[project_path]/contents/$content_folder", "$config[project_url]/contents/$content_folder");
		}
	}

	$local_conversion_paths = [];
	$local_conversion_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_conversion_servers where connection_type_id=0"));
	foreach ($local_conversion_servers as $local_conversion_server)
	{
		if (strpos($local_conversion_server['path'], "$config[project_path]/admin/data/") === 0)
		{
			$local_conversion_paths[] = substr($local_conversion_server['path'], strlen("$config[project_path]/admin/data/"));
		}
	}

	$contents_folder_keys = [
			'content_path_videos_sources',
			'content_path_videos_screenshots',
			'content_path_albums_sources',
			'content_path_categories',
			'content_path_models',
			'content_path_dvds',
			'content_path_posts',
			'content_path_avatars',
			'content_path_content_sources',
			'content_path_referers',
			'content_path_other',
	];
	$content_folder_names = [];

	auditCheckSecuritySuspiciousFilesIn("$config[temporary_path]", $build_stamp, true);
	foreach ($contents_folder_keys as $contents_folder_key)
	{
		auditCheckSecuritySuspiciousFilesIn($config[$contents_folder_key], $build_stamp);
		if (strpos($config[$contents_folder_key], $config['project_path']) !== false)
		{
			$relative_path = explode('/', trim(str_replace($config['project_path'], '', $config[$contents_folder_key]), ' /'));
			$content_folder_names[] = $relative_path[0];
		}
	}
	$content_folder_names = array_unique($content_folder_names);

	auditCheckSecuritySuspiciousFilesIn("$config[project_path]", $build_stamp);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/blocks", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/contents", $build_stamp);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/contents2", $build_stamp);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/css", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/langs", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/images", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/js", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/player", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/styles", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/static", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/template", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/tmp", $build_stamp, true);
	auditCheckSecuritySuspiciousFoldersIn("$config[project_path]", array_merge($content_folder_names, ['admin', 'blocks', 'css', 'langs', 'images', 'js', 'player', 'static', 'styles', 'template', 'tmp']));
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin", $build_stamp);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/api", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/async", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/billings", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/cdn", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/data", $build_stamp, true, array_merge(['engine'], $local_conversion_paths));
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/docs", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/feeds", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/images", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/include", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/js", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/langs", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/logs", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/plugins", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/smarty", $build_stamp, true, ['cache', 'template-c', 'template-c-site']);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/stamp", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/styles", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/template", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/tinymce", $build_stamp, true);
	auditCheckSecuritySuspiciousFilesIn("$config[project_path]/admin/tools", $build_stamp, true);
	auditCheckSecuritySuspiciousFoldersIn("$config[project_path]/admin", ['api', 'async', 'billings', 'cdn', 'data', 'docs', 'feeds', 'images', 'include', 'js', 'langs', 'logs', 'plugins', 'smarty', 'stamp', 'styles', 'template', 'tinymce', 'tools']);

	$page_template_md5 = md5(@file_get_contents("$config[project_path]/admin/tools/page_template.php"));
	foreach ($pages as $page)
	{
		$page_contents_md5 = md5(@file_get_contents("$config[project_path]/$page[external_id].php"));
		if ($page_contents_md5 != $page_template_md5)
		{
			$audit_messages[] = ['message_type' => 603, 'resource' => "[kt|b]/%ROOT%[/kt|b]/$page[external_id].php", 'resource_path' => "/$page[external_id].php"];
		}
	}
}

function auditSleep()
{
	$la = get_LA();
	if ($la > 5)
	{
		usleep(20000);
	} elseif ($la > 1)
	{
		usleep(2000);
	}
}

function auditLogMessage($message)
{
	if ($message)
	{
		echo date("[Y-m-d H:i:s]: ")."$message\n";
	} else {
		echo "\n";
	}
}

$task_id = intval($_SERVER['argv'][1]);
$admin_id = intval($_SERVER['argv'][2]);

if ($task_id > 0 && $admin_id > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'include/setup.php';
	require_once 'include/functions_base.php';
	require_once 'include/functions_admin.php';
	require_once 'include/functions_servers.php';
	require_once 'include/functions.php';
	require_once 'include/database_tables.php';

	KvsContext::init(KvsContext::CONTEXT_TYPE_ADMIN, $admin_id);

	ini_set('display_errors', 1);

	$options = get_options();

	$memory_limit = intval($options['LIMIT_MEMORY']);
	if ($memory_limit == 0)
	{
		$memory_limit = 512;
	}
	ini_set('memory_limit', "{$memory_limit}M");

	$plugin_path = "$config[project_path]/admin/data/plugins/audit";

	$data = @unserialize(@file_get_contents("$plugin_path/task-$task_id.dat"));
	if (!is_array($data))
	{
		auditLogMessage('No task data file available');
		error_reporting(E_ALL);
		unserialize(file_get_contents("$plugin_path/task-$task_id.dat"));
		die;
	}

	$audit_start_time = time();
	$audit_start_memory = memory_get_peak_usage();
	auditLogMessage('Audit started');
	auditLogMessage('Memory limit: ' . ini_get('memory_limit'));

	$total_amount_of_work = 0;
	$done_amount_of_work = 0;
	$last_pc = 0;
	$audit_messages = [];

	if ($data['check_installation'] == 1)
	{
		$total_amount_of_work++;
	}
	if ($data['check_database'] == 1)
	{
		foreach ($database_tables as $table)
		{
			if ($config['is_clone_db'] != 'true' || strpos($table, "$config[tables_prefix_multi]") === 0)
			{
				$total_amount_of_work++;
			}
		}
	}
	if ($data['check_formats'] == 1)
	{
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]formats_videos where status_id in (1,2)"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]formats_screenshots where status_id=1"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]formats_albums where status_id=1"));
	}
	if ($data['check_servers'] == 1)
	{
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]admin_servers"));
		if ($config['is_clone_db'] != 'true')
		{
			$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]admin_conversion_servers where status_id=1"));
		}
	}
	if ($data['check_security'] == 1)
	{
		$total_amount_of_work++;
	}
	if ($data['check_website_ui'] == 1 && is_dir("$config[project_path]/admin/data/config"))
	{
		$blocks = get_contents_from_dir("$config[project_path]/blocks", 2);
		$total_amount_of_work += array_cnt($blocks);

		$templates = get_contents_from_dir("$config[project_path]/template", 1);
		$total_amount_of_work += array_cnt($templates);

		$spot_files = get_contents_from_dir("$config[project_path]/admin/data/advertisements", 1);
		$total_amount_of_work += array_cnt($spot_files);
	}
	if ($data['check_video_content'] == 1)
	{
		$where_videos = '';
		if (intval($data['video_id_range_from']) > 0)
		{
			$where_videos .= ' and video_id>=' . intval($data['video_id_range_from']);
		}
		if (intval($data['video_id_range_to']) > 0)
		{
			$where_videos .= ' and video_id<=' . intval($data['video_id_range_to']);
		}
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]videos where status_id in (0,1) $where_videos"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]formats_videos where status_id=1"));
	}
	if ($data['check_album_content'] == 1)
	{
		$where_albums = '';
		if (intval($data['album_id_range_from']) > 0)
		{
			$where_albums .= ' and album_id>=' . intval($data['album_id_range_from']);
		}
		if (intval($data['album_id_range_to']) > 0)
		{
			$where_albums .= ' and album_id<=' . intval($data['album_id_range_to']);
		}
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]albums where status_id in (0,1) $where_albums"));
	}
	if ($data['check_auxiliary_content'] == 1)
	{
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]categories"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]categories_groups"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]models"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]models_groups"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]content_sources"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]dvds"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]dvds_groups"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]posts"));
		$total_amount_of_work += mr2number(sql("select count(*) from $config[tables_prefix]users"));
	}
	if ($data['check_content_protection'] == 1)
	{
		$total_amount_of_work++;
	}

	$formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (1,2)"));
	$formats_albums = mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id=1"));

	if ($data['check_installation'] == 1)
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Installation check started');

		if (substr(sprintf('%o', fileperms("$plugin_path/task-$task_id.dat")), -3) !== '666')
		{
			$audit_messages[] = array('message_type' => 43, 'resource' => 'File System');
		}

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 0, 'message_id' => 'message_checking_installation']), LOCK_EX);

		auditCheckInstallation($data);

		$done_amount_of_work++;
		$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Installation check finished in {$end_time}s using $end_memory of memory ($last_pc%)");
	}

	if ($data['check_database'] == 1)
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Database check started');

		$config['sql_safe_mode'] = 1;
		foreach ($database_tables as $table)
		{
			file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_table', 'message_params' => [$table]]), LOCK_EX);

			if ($config['is_clone_db'] != 'true' || strpos($table, "$config[tables_prefix_multi]") === 0)
			{
				auditCheckTableStatus($table);

				$done_amount_of_work++;
				$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			}

			auditSleep();
		}
		unset($config['sql_safe_mode']);

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Database check finished in {$end_time}s using $end_memory of memory ($last_pc%)");
	}

	if ($data['check_formats'] == 1)
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Formats check started');

		$rnd = mt_rand(10000000, 99999999);
		$source_file = "$plugin_path/test_video-$rnd.tmp";
		copy("$config[project_path]/admin/plugins/audit/data/test_video.avi", $source_file);

		if (is_file($source_file))
		{
			foreach ($formats_videos as $format)
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_format', 'message_params' => [$format['title']]]), LOCK_EX);

				auditCheckFormat($rnd, $format);

				$done_amount_of_work++;
				$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			}

			auditLogMessage('Video screenshot grabbing check started');
			$screen_source_file = "$plugin_path/test_video-$rnd.jpg";
			$exec_str = "$config[ffmpeg_path] -ss 5 -i $source_file -vframes 1 -y -f mjpeg $screen_source_file 2>&1";
			unset($res);
			exec("$exec_str 2>&1", $res);
			if (sprintf("%.0f", filesize($screen_source_file)) == 0)
			{
				$res = (array_cnt($res) > 0 ? implode("\n", $res) : "no response");
				$audit_messages[] = array('message_type' => 11, 'resource' => 'FFmpeg', 'detail' => "$exec_str: $res");
				if (is_file($screen_source_file))
				{
					unlink($screen_source_file);
				}
			}

			if (is_file($screen_source_file))
			{
				$formats_screenshots = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id=1"));
				foreach ($formats_screenshots as $format)
				{
					file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_format', 'message_params' => [$format['title']]]), LOCK_EX);

					auditCheckFormat($rnd, $format);

					$done_amount_of_work++;
					$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
				}
				if ($config['installation_type'] == 4)
				{
					foreach ($formats_albums as $format)
					{
						file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_format', 'message_params' => [$format['title']]]), LOCK_EX);

						auditCheckFormat($rnd, $format);

						$done_amount_of_work++;
						$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
					}
				}
				unlink($screen_source_file);
			}
			unlink($source_file);
		}

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Formats check finished in {$end_time}s using $end_memory of memory ($last_pc%)");
	}

	if ($data['check_servers'] == 1)
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Servers check started');

		$servers = mr2array(sql("select * from $config[tables_prefix]admin_servers order by server_id asc"));
		foreach ($servers as $server)
		{
			file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_server', 'message_params' => [$server['title']]]), LOCK_EX);

			auditCheckStorageServer($server);

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		}

		if ($config['is_clone_db'] != 'true')
		{
			$servers = mr2array(sql("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where status_id=1 order by server_id asc"));
			foreach ($servers as $server)
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_server', 'message_params' => [$server['title']]]), LOCK_EX);

				auditCheckConversionServer($server);

				$done_amount_of_work++;
				$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			}
		}

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Servers check finished in {$end_time}s using $end_memory of memory ($last_pc%)");
	}

	if ($data['check_website_ui'] == 1 && is_dir("$config[project_path]/admin/data/config"))
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Website UI check started');

		$templates_data = get_site_parsed_templates();
		$spots_data = get_site_spots();

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_blocks']), LOCK_EX);

		auditLogMessage('Blocks check started');
		$blocks = get_contents_from_dir("$config[project_path]/blocks", 2);
		foreach ($blocks as $k => $v)
		{
			auditCheckBlock($v);

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		}

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_templates']), LOCK_EX);

		auditLogMessage('Pages check started');
		$page_templates = [];
		$pages = get_site_pages();
		foreach ($pages as $page)
		{
			auditCheckPage($page);
			$page_templates[] = "$page[external_id].tpl";

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		}

		auditLogMessage('Global blocks check started');
		auditCheckGlobalBlocks();

		auditLogMessage('Page components check started');
		$templates = get_contents_from_dir("$config[project_path]/template", 1);
		foreach ($templates as $k => $v)
		{
			if (strtolower(substr($v, -4)) !== '.tpl')
			{
				$done_amount_of_work++;
				continue;
			}
			if (in_array($v, $page_templates))
			{
				continue;
			}
			auditCheckPageComponent($v);

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		}

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_advertising']), LOCK_EX);

		auditLogMessage('Advertising check started');
		$spots_files = get_contents_from_dir("$config[project_path]/admin/data/advertisements", 1);
		foreach ($spots_files as $spots_file)
		{
			if (strpos($spots_file, 'spot_') !== 0 || strtolower(substr($v, -4)) !== '.dat')
			{
				continue;
			}
			$external_id = substr($spots_file, 5, strlen($spots_file) - 9);
			auditCheckAdvertisingSpot($external_id);

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		}

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Website UI check finished in {$end_time}s using $end_memory of memory ($last_pc%)");
	}

	if ($data['check_video_content'] == 1)
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Video content check started');

		$videos = mr2array(sql("select video_id, title, dir, status_id, server_group_id, file_formats, file_url, embed, pseudo_url, gallery_url, load_type_id, screen_amount, poster_amount from $config[tables_prefix]videos where status_id in (0,1) $where_videos order by video_id asc"));
		$formats_screenshots = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id=1"));
		$formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos"));
		$storage_servers = mr2array(sql("select * from $config[tables_prefix]admin_servers"));

		$processed_items = 0;
		foreach ($videos as $video)
		{
			if ($processed_items % 100 == 0)
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_video', 'message_params' => [$video['video_id']]]), LOCK_EX);
			}

			auditCheckVideoContent($video, $data['check_video_stream'], $data['check_video_embed']);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}

		$formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id=1"));
		foreach ($formats_videos as $format)
		{
			file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_format', 'message_params' => [$format['title']]]), LOCK_EX);

			auditCheckVideoFormat($format);

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		}
		unset($videos);

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Video content check finished in {$end_time}s using $end_memory of memory ($last_pc%)");
	}

	if ($data['check_album_content'] == 1)
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Album content check started');

		$albums = mr2array(sql("select album_id, title, dir, status_id, server_group_id, zip_files, has_preview from $config[tables_prefix]albums where status_id in (0,1) $where_albums order by album_id asc"));
		$storage_servers = mr2array(sql("select * from $config[tables_prefix]admin_servers"));

		$processed_items = 0;
		foreach ($albums as $album)
		{
			if ($processed_items % 100 == 0)
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_album', 'message_params' => [$album['album_id']]]), LOCK_EX);
			}

			auditCheckAlbumContent($album);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($albums);

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Album content check finished in {$end_time}s using $end_memory of memory ($last_pc%)");
	}

	if ($data['check_auxiliary_content'] == 1)
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Auxiliary content check started');

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_categories']), LOCK_EX);

		auditLogMessage('Categories check started');
		$processed_items = 0;
		$categories = mr2array(sql("select category_id, title, dir, screenshot1, screenshot2, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5 from $config[tables_prefix]categories order by category_id asc"));
		foreach ($categories as $category)
		{
			auditCheckCategory($category);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($categories);

		auditLogMessage('Category groups check started');
		$processed_items = 0;
		$category_groups = mr2array(sql("select category_group_id, title, dir, screenshot1, screenshot2 from $config[tables_prefix]categories_groups order by category_group_id asc"));
		foreach ($category_groups as $category_group)
		{
			auditCheckCategoryGroup($category_group);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($category_groups);

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_models']), LOCK_EX);

		auditLogMessage('Models check started');
		$processed_items = 0;
		$models = mr2array(sql("select model_id, title, dir, screenshot1, screenshot2, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5 from $config[tables_prefix]models order by model_id asc"));
		foreach ($models as $model)
		{
			auditCheckModel($model);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($models);

		auditLogMessage('Model groups check started');
		$processed_items = 0;
		$model_groups = mr2array(sql("select model_group_id, title, dir, screenshot1, screenshot2 from $config[tables_prefix]models_groups order by model_group_id asc"));
		foreach ($model_groups as $model_group)
		{
			auditCheckModelGroup($model_group);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($model_groups);

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_content_sources']), LOCK_EX);

		auditLogMessage('Content sources check started');
		$processed_items = 0;
		$content_sources = mr2array(sql("select content_source_id, title, dir, url, screenshot1, screenshot2, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5, custom_file6, custom_file7, custom_file8, custom_file9, custom_file10 from $config[tables_prefix]content_sources order by content_source_id asc"));
		foreach ($content_sources as $content_source)
		{
			auditCheckContentSource($content_source);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($content_sources);

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_dvds']), LOCK_EX);

		auditLogMessage('DVDs check started');
		$processed_items = 0;
		$dvds = mr2array(sql("select dvd_id, title, dir, cover1_front, cover1_back, cover2_front, cover2_back, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5 from $config[tables_prefix]dvds order by dvd_id asc"));
		foreach ($dvds as $dvd)
		{
			auditCheckDvd($dvd);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($dvds);

		auditLogMessage('DVD groups check started');
		$processed_items = 0;
		$dvd_groups = mr2array(sql("select dvd_group_id, title, dir, cover1, cover2 from $config[tables_prefix]dvds_groups"));
		foreach ($dvd_groups as $dvd_group)
		{
			auditCheckDvdGroup($dvd_group);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($dvd_groups);

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_posts']), LOCK_EX);

		auditLogMessage('Posts check started');
		$processed_items = 0;
		$posts = mr2array(sql("select post_id, title, dir, content, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5, custom_file6, custom_file7, custom_file8, custom_file9, custom_file10 from $config[tables_prefix]posts order by post_id asc"));
		foreach ($posts as $post)
		{
			auditCheckPost($post);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($posts);

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_users']), LOCK_EX);

		auditLogMessage('Users check started');
		$processed_items = 0;
		$users = mr2array(sql("select user_id, username, email, display_name, avatar, cover from $config[tables_prefix]users order by user_id asc"));
		foreach ($users as $user)
		{
			auditCheckUser($user);
			$processed_items++;

			$done_amount_of_work++;
			$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

			if ($processed_items % 1000 == 0)
			{
				auditLogMessage("$processed_items ($last_pc%)...");
			}
		}
		unset($users);

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Auxiliary content check finished in {$end_time}s using $end_memory of memory ($last_pc%)");
	}

	if ($data['check_content_protection'] == 1)
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Content protection check started');

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_content_protection']), LOCK_EX);

		auditCheckContentProtection();

		$done_amount_of_work++;
		$last_pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Content protection check finished in {$end_time}s using $end_memory of memory ($last_pc%)");
	}

	if ($data['check_security'] == 1 && function_exists('curl_init'))
	{
		$start_time = time();
		$start_memory = memory_get_peak_usage();
		auditLogMessage('');
		auditLogMessage('Security check started');

		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $last_pc, 'message_id' => 'message_checking_security']), LOCK_EX);

		auditCheckSecurity();

		$end_time = time() - $start_time;
		$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
		auditLogMessage("Security check finished in {$end_time}s using $end_memory of memory (100%)");
	}

	$audit_end_time = time() - $audit_start_time;
	$audit_end_memory = sizeToHumanString(memory_get_peak_usage() - $audit_start_memory);
	auditLogMessage('');
	auditLogMessage("Audit finished in {$audit_end_time}s using $audit_end_memory of memory (100%)");

	file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 100]), LOCK_EX);

	$data['audit_messages'] = $audit_messages;

	file_put_contents("$plugin_path/task-$task_id.dat", serialize($data), LOCK_EX);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}

