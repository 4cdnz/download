<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function digiregsInit()
{
	global $config;

	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.digiregs.debug', 'plugins|digiregs', 'plugins.php?plugin_id=digiregs', KvsAdminNotificationEnum::SEVERITY_INFO));
	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.digiregs.balance', 'plugins|digiregs', 'plugins.php?plugin_id=digiregs', KvsAdminNotificationEnum::SEVERITY_WARNING, 1));

	$plugin_path = "$config[project_path]/admin/data/plugins/digiregs";
	mkdir_recursive($plugin_path);

	if (!is_file("$plugin_path/data.dat"))
	{
		$data = [];
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	}
}

function digiregsIsEnabled()
{
	global $config;

	digiregsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/digiregs";
	if (is_file("$plugin_path/data.dat"))
	{
		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
		if (intval($data['copyright_is_enabled']) == 1)
		{
			return true;
		}
	}
	return false;
}

function digiregsShow()
{
	global $config, $lang, $errors, $page_name, $list_messages;

	digiregsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/digiregs";

	$errors = null;

	if ($_GET['action'] == 'get_log')
	{
		download_log_file("$config[project_path]/admin/logs/plugins/digiregs.txt");
		die;
	} elseif ($_GET['action'] == 'get_debug_log')
	{
		download_log_file("$config[project_path]/admin/logs/plugins/digiregs_debug.txt");
		die;
	}

	if ($_POST['action'] == 'save')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (intval($_POST['copyright_apply_to_feeds_type']) == 0)
		{
			$_POST['copyright_apply_to_feeds'] = [];
		}
		if (intval($_POST['copyright_known_set_admin_flag_type']) == 0)
		{
			$_POST['copyright_known_set_admin_flag'] = 0;
		}
		if (intval($_POST['copyright_known_action']) == 2)
		{
			$_POST['copyright_known_create_content_sources'] = 0;
			$_POST['copyright_known_create_dvds'] = 0;
		}
		if (intval($_POST['copyright_unknown_set_admin_flag_type']) == 0)
		{
			$_POST['copyright_unknown_set_admin_flag'] = 0;
		}
		if (intval($_POST['copyright_unknown_action']) == 2)
		{
			$_POST['copyright_unknown_create_content_sources'] = 0;
			$_POST['copyright_unknown_create_dvds'] = 0;
		}

		if ($_POST['api_key'] !== '')
		{
			$balance_response = digiregsQueryAPI([], $_POST);
			if ($balance_response['code'] != 200)
			{
				$errors[] = str_replace('%1%', $balance_response['message'], $lang['plugins']['digiregs']['error_invalid_api_response_code']);
			} elseif (!isset($balance_response['data']['remaining_submissions']))
			{
				$errors[] = $lang['plugins']['digiregs']['error_invalid_api_response_format'];
			} else
			{
				$_POST['balance_submissions'] = $balance_response['data']['remaining_submissions'];
			}
		} elseif (intval($_POST['copyright_is_enabled']) == 1)
		{
			validate_field('empty', $_POST['api_key'], $lang['plugins']['digiregs']['field_api_key']);
		}

		if (intval($_POST['copyright_is_enabled']) == 1)
		{
			validate_field('empty', $_POST['copyright_apply_to'], $lang['plugins']['digiregs']['field_apply_to']);
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
		}

		if (in_array('manual', $_POST['copyright_apply_to'] ?? []))
		{
			if (intval($_POST['copyright_known_action']) != 3)
			{
				$errors[] = str_replace('%1%', $lang['plugins']['digiregs']['field_copyright_known_action'], $lang['plugins']['digiregs']['error_delete_not_possible_with_manual_execution']);
			}
			if (intval($_POST['copyright_unknown_action']) != 3)
			{
				$errors[] = str_replace('%1%', $lang['plugins']['digiregs']['field_copyright_unknown_action'], $lang['plugins']['digiregs']['error_delete_not_possible_with_manual_execution']);
			}
			if (intval($_POST['copyright_is_delete_with_empty']) == 1)
			{
				$errors[] = str_replace('%1%', $lang['plugins']['digiregs']['field_copyright_empty_action'], $lang['plugins']['digiregs']['error_delete_not_possible_with_manual_execution']);
			}
		}

		if (!is_array($errors))
		{
			$save_data = @unserialize(file_get_contents("$plugin_path/data.dat"));
			$save_data['api_key'] = $_POST['api_key'];
			$save_data['balance_submissions'] = $_POST['balance_submissions'];
			$save_data['on_empty_balance'] = intval($_POST['on_empty_balance']);
			$save_data['is_debug_enabled'] = intval($_POST['is_debug_enabled']);

			$save_data['copyright_is_enabled'] = intval($_POST['copyright_is_enabled']);
			$save_data['copyright_apply_to'] = $_POST['copyright_apply_to'] ?? [];
			$save_data['copyright_apply_to_feeds'] = $_POST['copyright_apply_to_feeds'] ? array_map('intval', $_POST['copyright_apply_to_feeds']) : [];
			$save_data['copyright_apply_only_with_empty_content_source'] = intval($_POST['copyright_apply_only_with_empty_content_source']);
			$save_data['copyright_known_action'] = intval($_POST['copyright_known_action']);
			$save_data['copyright_known_deactivate'] = intval($_POST['copyright_known_deactivate']);
			$save_data['copyright_known_set_admin_flag'] = intval($_POST['copyright_known_set_admin_flag']);
			$save_data['copyright_known_truncate_duration'] = intval($_POST['copyright_known_truncate_duration']);
			$save_data['copyright_known_create_content_sources'] = intval($_POST['copyright_known_create_content_sources']);
			$save_data['copyright_known_create_content_sources_disabled'] = intval($_POST['copyright_known_create_content_sources_disabled']);
			$save_data['copyright_known_create_dvds'] = intval($_POST['copyright_known_create_dvds']);
			$save_data['copyright_known_create_dvds_disabled'] = intval($_POST['copyright_known_create_dvds_disabled']);
			$save_data['copyright_unknown_action'] = intval($_POST['copyright_unknown_action']);
			$save_data['copyright_unknown_deactivate'] = intval($_POST['copyright_unknown_deactivate']);
			$save_data['copyright_unknown_set_admin_flag'] = intval($_POST['copyright_unknown_set_admin_flag']);
			$save_data['copyright_unknown_truncate_duration'] = intval($_POST['copyright_unknown_truncate_duration']);
			$save_data['copyright_unknown_create_content_sources'] = intval($_POST['copyright_unknown_create_content_sources']);
			$save_data['copyright_unknown_create_content_sources_disabled'] = intval($_POST['copyright_unknown_create_content_sources_disabled']);
			$save_data['copyright_unknown_create_dvds'] = intval($_POST['copyright_unknown_create_dvds']);
			$save_data['copyright_unknown_create_dvds_disabled'] = intval($_POST['copyright_unknown_create_dvds_disabled']);
			$save_data['copyright_is_delete_with_empty'] = intval($_POST['copyright_is_delete_with_empty']);
			$save_data['copyright_blacklist'] = trim($_POST['copyright_blacklist']);

			file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

			if (intval($_POST['is_debug_enabled']) == 0)
			{
				@unlink("$config[project_path]/admin/logs/plugins/digiregs_debug.txt");
			}
			add_admin_notification('plugins.digiregs.debug', intval($_POST['is_debug_enabled']));
			add_admin_notification('plugins.digiregs.balance', intval($_POST['copyright_is_enabled']) == 1 && intval($_POST['balance_submissions']) < 400 ? 1 : 0, 400);

			if (intval($_POST['copyright_is_enabled']) == 1)
			{
				get_page('', "https://www.kernel-scripts.com/track_feature.php?feature=digiregs&url=$config[project_url]&api_key=" . urlencode($_POST['api_key']), '', '', 1, 0, 5, '');
			}

			return_ajax_success("$page_name?plugin_id=digiregs");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	$_POST = @unserialize(file_get_contents("$plugin_path/data.dat"));
	$_POST['feeds'] = mr2array(sql_pr("select * from $config[tables_prefix]videos_feeds_import order by title asc"));
	$_POST['admin_flags'] = mr2array(sql_pr("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 order by title asc"));

	if (is_file("$config[project_path]/admin/logs/plugins/digiregs_debug.txt"))
	{
		$_POST['is_debug_enabled'] = 1;
	}

	if ($_POST['api_key'] !== '')
	{
		$balance_response = digiregsQueryAPI([], $_POST);
		if (isset($balance_response['data']['remaining_submissions']))
		{
			$_POST['balance_submissions'] = $balance_response['data']['remaining_submissions'];
			add_admin_notification('plugins.digiregs.balance', intval($_POST['copyright_is_enabled']) == 1 && intval($balance_response['data']['remaining_submissions']) < 400 ? 1 : 0, 400);
		}
	}

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
	}
	if (isset($_SESSION['admin_notifications']['list']['plugins.digiregs.debug']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['plugins.digiregs.debug']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['plugins.digiregs.balance']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['plugins.digiregs.balance']['title'];
	}
}

function digiregsGetCopyrightInfo($video_id, $is_new, $logger = null)
{
	global $config;

	$data = [];

	$plugin_path = "$config[project_path]/admin/data/plugins/digiregs";
	if (is_file("$plugin_path/data.dat"))
	{
		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	}

	if (intval($data['copyright_is_enabled']) == 0)
	{
		return null;
	}

	$video_data = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!$video_data)
	{
		digiregsLog("ERROR Copyright detection: video $video_id is not found", $logger);
		return null;
	}
	if ($video_data['load_type_id'] != 1)
	{
		if (!$is_new)
		{
			digiregsLog("ERROR Copyright detection: video $video_id is not a file upload type", $logger);
		}
		return null;
	}

	$execution_mode = 'manual';
	if ($is_new)
	{
		if ($video_data['feed_id'] > 0)
		{
			$execution_mode = 'feeds';
		} elseif ($video_data['gallery_url'] != '')
		{
			$execution_mode = 'grabbers';
		} else
		{
			switch (mr2number(sql_pr("select action_id from $config[tables_prefix]admin_audit_log where object_id=? and object_type_id=1 order by record_id asc limit 1", $video_id)))
			{
				case 100:
					$execution_mode = 'admins';
					break;
				case 110:
					$execution_mode = 'import';
					break;
				case 120:
					$execution_mode = 'feeds';
					break;
				case 130:
					$execution_mode = 'ftp';
					break;
				case 140:
					$execution_mode = 'site';
					break;
				default:
					$execution_mode = 'unknown';
			}
		}
	}

	$run_copyright_detection = true;
	if (!in_array($execution_mode, $data['copyright_apply_to']))
	{
		digiregsLog("INFO  Copyright detection: execution mode is not enabled in plugin settings ($execution_mode)", $logger);
		$run_copyright_detection = false;
	} elseif ($execution_mode == 'feeds' && array_cnt($data['copyright_apply_to_feeds']) > 0)
	{
		if (!in_array($video_data['feed_id'], $data['copyright_apply_to_feeds']))
		{
			digiregsLog("INFO  Copyright detection: new video added by feed not enabled in plugin settings ($video_data[feed_id])", $logger);
			$run_copyright_detection = false;
		}
	}
	if ($run_copyright_detection && $data['copyright_apply_only_with_empty_content_source'] == 1 && $video_data['content_source_id'] > 0)
	{
		digiregsLog("INFO  Copyright detection: video with known content source is skipped", $logger);
		$run_copyright_detection = false;
	}

	if (!$run_copyright_detection)
	{
		return null;
	}

	$previous_result = mr2array_single(sql_pr("select * from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=2", $video_id));
	if (!$previous_result)
	{
		$dir_path = get_dir_by_id($video_id);
		$video_sources_dir = "$config[content_path_videos_sources]/$dir_path/$video_id";
		$video_screenshots_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id";
		$video_screenshots_url = "$config[content_url_videos_screenshots]/$dir_path/$video_id";
		if (strpos($video_screenshots_url, '//') === 0)
		{
			$video_screenshots_url = "http:$video_screenshots_url";
		}
		if (!mkdir_recursive($video_screenshots_dir))
		{
			digiregsLog("ERROR Copyright detection: failed to create video screenshot directory: $video_screenshots_dir", $logger);
			return null;
		}

		$screenshot_temp_path = "$video_screenshots_dir/digiregs.jpg";
		$screenshot_temp_url = "$video_screenshots_url/digiregs.jpg";

		$source_video_file = '';
		if (is_file("$video_sources_dir/$video_id.tmp"))
		{
			$source_video_file = "$video_sources_dir/$video_id.tmp";
		} else
		{
			$temp_size = 0;
			$temp_video_files = get_contents_from_dir($video_sources_dir, 1);
			foreach ($temp_video_files as $temp_video_file)
			{
				if (filesize("$video_sources_dir/$temp_video_file") > $temp_size)
				{
					$temp_size = filesize("$video_sources_dir/$temp_video_file");
					$source_video_file = "$video_sources_dir/$temp_video_file";
				}
			}
		}

		if ($source_video_file)
		{
			$video_duration = get_video_duration($source_video_file);
			if ($video_duration > 2)
			{
				$screenshot_duration = round($video_duration / 2);
				$exec_str = "$config[ffmpeg_path] -ss $screenshot_duration -i $source_video_file -vframes 1 -y -f mjpeg -qscale 1 $screenshot_temp_path 2>&1";
				exec($exec_str, $res);
				if (!is_file($screenshot_temp_path) || filesize($screenshot_temp_path) == 0)
				{
					digiregsLog("ERROR Copyright detection: failed to create video screenshot from $source_video_file: " . implode("\n...... ", $res), $logger);
					return null;
				}
				digiregsLog("INFO  Copyright detection: created test screenshot at {$screenshot_duration}s", $logger);
			}
		} elseif ($video_data['screen_amount'] > 0)
		{
			$screenshot_index = round($video_data['screen_amount'] / 2);
			if ($screenshot_index == 0)
			{
				$screenshot_index = 1;
			}
			if (!copy("$video_sources_dir/screenshots/$screenshot_index.jpg", $screenshot_temp_path))
			{
				digiregsLog("ERROR Copyright detection: failed to copy video screenshot source from $video_sources_dir/screenshots/$screenshot_index.jpg", $logger);
				return null;
			}
			digiregsLog("INFO  Copyright detection: took existing screenshot #$screenshot_index", $logger);
		}

		if (is_file($screenshot_temp_path) && filesize($screenshot_temp_path) > 0)
		{
			$api_response = digiregsQueryAPI(['action' => $screenshot_temp_url]);
			if ($api_response['code'] != 200)
			{
				digiregsLog("ERROR Copyright detection: API error $api_response[code] ($api_response[message])", $logger);
				if ($api_response['code'] == 402 && intval($data['on_empty_balance']) == 0)
				{
					return ['action' => 'wait'];
				}
				digiregsLog('ERROR Copyright detection: ignoring this video', $logger);
				return null;
			}

			$copyright_data = $api_response['data'];
			if (trim($copyright_data['uid']) == '')
			{
				digiregsLog('ERROR Copyright detection: empty response from API', $logger);
				return ['action' => 'wait'];
			}
			digiregsLog("INFO  Copyright detection: uid=$copyright_data[uid], watermark=$copyright_data[watermark], owner=$copyright_data[owner], type=$copyright_data[type], allowed=$copyright_data[allowed]", $logger);

			$operation_status_id = 0;
			if (trim($copyright_data['watermark']) !== '')
			{
				$operation_status_id = 1;
				if ($copyright_data['type'] == 'unknown')
				{
					$operation_status_id = 2;
				}
			}
			sql_insert("insert into $config[tables_prefix]videos_advanced_operations set video_id=?, operation_type_id=2, operation_status_id=?, operation_task_id=?, operation_data=?, added_date=?, finished_date=?", $video_id, $operation_status_id, trim($copyright_data['uid']), serialize($copyright_data), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
		} else
		{
			digiregsLog("ERROR Copyright detection: no ability to create screenshot", $logger);
			return null;
		}
	} else
	{
		$copyright_data = @unserialize($previous_result['operation_data']) ?: [];
		digiregsLog("INFO  Copyright detection: using cached copyright data", $logger);
	}

	$copyright_data['action'] = 'allow';
	if (trim($copyright_data['watermark']) !== '')
	{
		if (trim($copyright_data['owner']) === '')
		{
			$copyright_data['owner'] = $copyright_data['watermark'];
		}
		$copyright_data['watermark'] = trim(mb_lowercase($copyright_data['watermark']));
		$copyright_data['owner'] = trim(mb_lowercase($copyright_data['owner']));

		$search_content_sources = mr2array(sql_pr("select content_source_id, title, synonyms from $config[tables_prefix]content_sources where title=? or title=? or synonyms like ? or synonyms like ?", $copyright_data['watermark'], $copyright_data['owner'], "%$copyright_data[watermark]%", "%$copyright_data[owner]%"));
		foreach ($search_content_sources as $search_content_source)
		{
			if (mb_lowercase($search_content_source['title']) == $copyright_data['watermark'] || mb_lowercase($search_content_source['title']) == $copyright_data['owner'])
			{
				$copyright_data['content_source_id'] = $search_content_source['content_source_id'];
				break;
			}

			$search_content_source['synonyms'] = explode(',', $search_content_source['synonyms']);
			foreach ($search_content_source['synonyms'] as $synonym)
			{
				if (mb_lowercase(trim($synonym)) == $copyright_data['watermark'] || mb_lowercase(trim($synonym)) == $copyright_data['owner'])
				{
					$copyright_data['content_source_id'] = $search_content_source['content_source_id'];
					break 2;
				}
			}
		}

		$search_dvds = mr2array(sql_pr("select dvd_id, title, synonyms from $config[tables_prefix]dvds where title=? or title=? or synonyms like ? or synonyms like ?", $copyright_data['watermark'], $copyright_data['owner'], "%$copyright_data[watermark]%", "%$copyright_data[owner]%"));
		foreach ($search_dvds as $search_dvd)
		{
			if (mb_lowercase($search_dvd['title']) == $copyright_data['watermark'] || mb_lowercase($search_dvd['title']) == $copyright_data['owner'])
			{
				$copyright_data['dvd_id'] = $search_dvd['dvd_id'];
				break;
			}

			$search_dvd['synonyms'] = explode(',', $search_dvd['synonyms']);
			foreach ($search_dvd['synonyms'] as $synonym)
			{
				if (mb_lowercase(trim($synonym)) == $copyright_data['watermark'] || mb_lowercase(trim($synonym)) == $copyright_data['owner'])
				{
					$copyright_data['dvd_id'] = $search_dvd['dvd_id'];
					break 2;
				}
			}
		}

		$options_group = 'copyright_known';
		$options_group_title = 'copyrighted videos';
		if ($copyright_data['type'] == 'unknown')
		{
			$options_group = 'copyright_unknown';
			$options_group_title = 'videos with unknown watermarks';
		}
		switch (intval($data["{$options_group}_action"]))
		{
			case 0:
				$copyright_data['action'] = 'delete';
				digiregsLog("INFO  Copyright detection: any $options_group_title are configured to be deleted", $logger);
				break;
			case 1:
				if (intval($copyright_data['allowed']) > 0)
				{
					$copyright_data['action'] = 'allow';
					$copyright_data['truncate_to'] = intval($copyright_data['allowed']);
				} else
				{
					$copyright_data['action'] = 'delete';
				}
				digiregsLog("INFO  Copyright detection: only copyrighted videos with known duration should be allowed", $logger);
				break;
			case 2:
				$copyright_data['action'] = intval($copyright_data['content_source_id']) + intval($copyright_data['dvd_id']) > 0 ? 'allow' : 'delete';
				digiregsLog("INFO  Copyright detection: only $options_group_title from known content sources / channels should be allowed", $logger);
				break;
			case 3:
				$copyright_data['action'] = 'allow';
				digiregsLog("INFO  Copyright detection: any $options_group_title should be allowed", $logger);
				break;
		}
		if ($copyright_data['action'] == 'allow' && intval($data["{$options_group}_truncate_duration"]) > 0 && intval($copyright_data['allowed']) > 0)
		{
			$copyright_data['truncate_to'] = intval($copyright_data['allowed']);
		}

		if (trim($data['copyright_blacklist']) !== '')
		{
			$copyright_blacklist = str_replace(["\n", "\r"], ',', $data['copyright_blacklist']);
			$copyright_blacklist_list = array_map('trim', explode(',', $copyright_blacklist));
			foreach ($copyright_blacklist_list as $copyright_blacklist_item)
			{
				$copyright_blacklist_item = mb_lowercase($copyright_blacklist_item);
				if ($copyright_blacklist_item !== '' && ($copyright_blacklist_item == $copyright_data['watermark'] || $copyright_blacklist_item == $copyright_data['owner']))
				{
					digiregsLog("INFO  Copyright detection: copyright owner / watermark is in black list: $copyright_blacklist_item", $logger);
					$copyright_data['action'] = 'delete';
					break;
				}
			}
		}
	} else
	{
		if ($data['copyright_is_delete_with_empty'] == 1)
		{
			$copyright_data['action'] = 'delete';
			digiregsLog("INFO  Copyright detection: no copyright detected, such videos are configured to be deleted", $logger);
		} else
		{
			digiregsLog("INFO  Copyright detection: no copyright detected, any such videos should be allowed", $logger);
		}
	}

	return $copyright_data;
}

function digiregsQueryAPI($params = [], $test_mode = null): array
{
	global $config;

	digiregsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/digiregs";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if (is_array($test_mode))
	{
		$data = $test_mode;
	}
	if (!is_array($data) || !$data['api_key'])
	{
		return [];
	}

	$is_debug_enabled = false;
	if (intval($data['is_debug_enabled']) == 1)
	{
		$is_debug_enabled = true;
	}

	$api_url = "https://digiregs.net/api/kvs/query.php?api_key=$data[api_key]";
	foreach ($params as $param_name => $param_value)
	{
		$api_url .= "&$param_name=" . urlencode($param_value);
	}
	$headers = [
			"Accept: application/json",
			"Content-Type: application/json",
	];

	$curl = curl_init();
	curl_setopt_array($curl, array(
					CURLOPT_URL => $api_url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_FOLLOWLOCATION => 1,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_TIMEOUT => 20,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => $headers,
			)
	);

	$verbose = null;
	if ($is_debug_enabled)
	{
		$verbose = fopen('php://temp', 'w+');
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_STDERR, $verbose);
	}

	$result = [];

	$response = curl_exec($curl);
	if ($response)
	{
		$response = @json_decode($response, true) ?: [];
	} else
	{
		$response = [];
	}
	$result['data'] = $response;
	if (curl_errno($curl))
	{
		file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt", "[" . date("Y-m-d H:i:s") . "] [" . curl_errno($curl) . "] " . curl_error($curl) . "\n", FILE_APPEND | LOCK_EX);
	}

	if ($verbose)
	{
		rewind($verbose);
		file_put_contents("$config[project_path]/admin/logs/plugins/digiregs_debug.txt", "[" . date("Y-m-d H:i:s") . "] CURL request -------------- \n\n" . stream_get_contents($verbose) . "\n", FILE_APPEND | LOCK_EX);
	}

	if ($response && $is_debug_enabled)
	{
		file_put_contents("$config[project_path]/admin/logs/plugins/digiregs_debug.txt", "[" . date("Y-m-d H:i:s") . "] RESPONSE data -------------- \n\n" . json_encode($response) . "\n\n", FILE_APPEND | LOCK_EX);
	}

	$result['code'] = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	$result['message'] = "HTTP $result[code] response";
	if (intval($result['data']['errcode']) > 0)
	{
		$result['code'] = intval($result['data']['errcode']);
		$result['message'] = trim($result['data']['description']);
	}
	curl_close($curl);

	return $result;
}

function digiregsLog($message, $logger = null)
{
	$message = date("...... [H:i:s] ") . $message;
	if (is_callable($logger))
	{
		$logger($message);
	} else
	{
		echo "$message\n";
	}
}

if ($_SERVER['argv'][1] == 'exec' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';
	require_once 'functions_admin.php';

	$object_type = $_SERVER['argv'][2];
	$object_id = intval($_SERVER['argv'][3]);
	$object_state = trim($_SERVER['argv'][4]);

	if ($object_type != 'video')
	{
		return;
	}
	$video_id = $object_id;
	$video_data = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!$video_data)
	{
		digiregsLog("ERROR Copyright detection: video $video_id is not found");
	}

	$data = [];

	$plugin_path = "$config[project_path]/admin/data/plugins/digiregs";
	if (is_file("$plugin_path/data.dat"))
	{
		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
		if (!is_array($data))
		{
			digiregsLog("ERROR Failed to open plugin configuration");
			return;
		}

		$data['last_exec_date'] = time();
		$data['last_exec_count']++;
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	}

	$copyright_info = digiregsGetCopyrightInfo($video_id, $object_state == 'new');
	if (is_array($copyright_info) && $copyright_info['watermark'])
	{
		$options_group = 'copyright_known';
		if ($copyright_info['type'] == 'unknown')
		{
			$options_group = 'copyright_unknown';
		}

		$video_update_info = [];

		if (intval($data["{$options_group}_deactivate"]) > 0 && intval($video_data['status_id']) !== 0)
		{
			digiregsLog("INFO  Copyright detection: deactivating video");
			$video_update_info['status_id'] = 0;
		}

		if (intval($data["{$options_group}_set_admin_flag"]) > 0 && intval($video_data['admin_flag_id']) != intval($data["{$options_group}_set_admin_flag"]))
		{
			$admin_flag = mr2array_single(sql_pr("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 and flag_id=?", intval($data["{$options_group}_set_admin_flag"])));
			if (!empty($admin_flag))
			{
				digiregsLog("INFO  Copyright detection: setting admin flag \"$admin_flag[title]\"");
				$video_update_info['admin_flag_id'] = $admin_flag['flag_id'];
			}
		}

		if (intval($copyright_info['content_source_id']) > 0)
		{
			if (intval($video_data['content_source_id']) != intval($copyright_info['content_source_id']))
			{
				$video_update_info['content_source_id'] = $copyright_info['content_source_id'];
				digiregsLog("INFO  Copyright detection: assigning content source #$video_update_info[content_source_id]");
			}
		} elseif (intval($data["{$options_group}_create_content_sources"]) > 0)
		{
			$content_source_title = $copyright_info['watermark'];
			$content_source_dir = get_correct_dir_name($content_source_title);
			$temp_dir = $content_source_dir;
			for ($it = 2; $it < 999999; $it++)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where dir=?", $temp_dir)) == 0)
				{
					$content_source_dir = $temp_dir;
					break;
				}
				$temp_dir = $content_source_dir . $it;
			}
			$video_update_info['content_source_id'] = sql_insert("insert into $config[tables_prefix]content_sources set title=?, dir=?, rating_amount=1, status_id=?, added_date=?", $content_source_title, $content_source_dir, intval($data["{$options_group}_create_content_sources_disabled"]) > 0 ? 0 : 1, date('Y-m-d H:i:s'));
			if ($video_update_info['content_source_id'] > 0)
			{
				sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='digiregs', action_id=130, object_id=?, object_type_id=3, added_date=?", $video_update_info['content_source_id'], date('Y-m-d H:i:s'));
			}
			digiregsLog("INFO  Copyright detection: content source not found, created content source #$video_update_info[content_source_id]");
		}

		if (intval($copyright_info['dvd_id']) > 0)
		{
			if (intval($video_data['dvd_id']) != intval($copyright_info['dvd_id']))
			{
				$video_update_info['dvd_id'] = $copyright_info['dvd_id'];
				digiregsLog("INFO  Copyright detection: assigning channel #$video_update_info[dvd_id]");
			}
		} elseif (intval($data["{$options_group}_create_dvds"]) > 0 && $config['installation_type'] >= 4)
		{
			$dvd_title = $copyright_info['watermark'];
			$dvd_dir = get_correct_dir_name($dvd_title);
			$temp_dir = $dvd_dir;
			for ($it = 2; $it < 999999; $it++)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where dir=?", $temp_dir)) == 0)
				{
					$dvd_dir = $temp_dir;
					break;
				}
				$temp_dir = $dvd_dir . $it;
			}
			$video_update_info['dvd_id'] = sql_insert("insert into $config[tables_prefix]dvds set title=?, dir=?, rating_amount=1, status_id=?, added_date=?", $dvd_title, $dvd_dir, intval($data["{$options_group}_create_dvds_disabled"]) > 0 ? 0 : 1, date('Y-m-d H:i:s'));
			if ($video_update_info['dvd_id'] > 0)
			{
				sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='digiregs', action_id=130, object_id=?, object_type_id=5, added_date=?", $video_update_info['dvd_id'], date('Y-m-d H:i:s'));
			}
			digiregsLog("INFO  Copyright detection: channel not found, created channel #$video_update_info[dvd_id]");
		}

		if (array_cnt($video_update_info) > 0)
		{
			sql_update("update $config[tables_prefix]videos set ?% where video_id=?", $video_update_info, $video_id);

			$update_details = '';
			foreach ($video_update_info as $k => $v)
			{
				$update_details .= "$k, ";
			}
			if (strlen($update_details) > 0)
			{
				$update_details = substr($update_details, 0, -2);
			}

			sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='digiregs', action_id=168, object_id=?, object_type_id=1, action_details=?, added_date=?", $video_id, $update_details, date('Y-m-d H:i:s'));
		}

		if ($object_state != 'new' && intval($data["{$options_group}_truncate_duration"]) > 0 && intval($copyright_info['allowed']) > 0)
		{
			$postfixes_to_recreate = [];

			$available_formats = get_video_formats($video_data['video_id'], $video_data['file_formats']);
			foreach ($available_formats as $format_rec)
			{
				if ($format_rec['duration'] > intval($copyright_info['allowed']))
				{
					$postfixes_to_recreate[] = $format_rec['postfix'];
				}
			}

			if (array_cnt($postfixes_to_recreate) > 0)
			{
				$background_task = [];
				$background_task['format_postfix'] = implode(', ', $postfixes_to_recreate);
				$background_task['force_duration_limit'] = intval($copyright_info['allowed']);
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=4, video_id=?, data=?, added_date=?", $video_id, serialize($background_task), date('Y-m-d H:i:s'));
				digiregsLog("INFO  Copyright detection: forcing duration $copyright_info[allowed] for video files");
			}
		}
	}

	if ($data['last_exec_count'] % 100 == 0)
	{
		$balance_response = digiregsQueryAPI([]);
		if (isset($balance_response['data']['remaining_submissions']))
		{
			add_admin_notification('plugins.digiregs.balance', intval($data['copyright_is_enabled']) == 1 && intval($balance_response['data']['remaining_submissions']) < 400 ? 1 : 0, 400);
		}
	}
}