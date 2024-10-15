<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function neuroscoreInit()
{
	global $config;

	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.neuroscore.debug', 'plugins|neuroscore', 'plugins.php?plugin_id=neuroscore', KvsAdminNotificationEnum::SEVERITY_INFO));
	KvsAdminNotificationEnum::register_value(new KvsAdminNotificationEnum('plugins.neuroscore.balance', 'plugins|neuroscore', 'plugins.php?plugin_id=neuroscore', KvsAdminNotificationEnum::SEVERITY_WARNING, 1));

	$plugin_path = "$config[project_path]/admin/data/plugins/neuroscore";
	mkdir_recursive($plugin_path);

	if (is_file("$plugin_path/data.dat"))
	{
		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
		if (intval($data['score_is_enabled']) == 1 || intval($data['title_is_enabled']) == 1 || intval($data['categories_is_enabled']) == 1 || intval($data['models_is_enabled']) == 1)
		{
			sql_insert("insert into $config[tables_prefix_multi]admin_processes set pid='cron_plugins.neuroscore', exec_interval=300, status_data='a:0:{}'");
		}
	} else
	{
		$data = ['on_empty_balance' => 1];
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	}
}

function neuroscoreIsEnabled()
{
	global $config;

	neuroscoreInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/neuroscore";
	if (is_file("$plugin_path/data.dat"))
	{
		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
		if (intval($data['score_is_enabled']) == 1 || intval($data['title_is_enabled']) == 1 || intval($data['categories_is_enabled']) == 1 || intval($data['models_is_enabled']) == 1)
		{
			return true;
		}
	}
	return false;
}

function neuroscoreShow()
{
	global $config, $lang, $errors, $page_name, $list_messages;

	neuroscoreInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/neuroscore";

	$errors = null;

	if ($_GET['action'] == 'get_log')
	{
		download_log_file("$config[project_path]/admin/logs/plugins/neuroscore.txt");
		die;
	} elseif ($_GET['action'] == 'get_debug_log')
	{
		download_log_file("$config[project_path]/admin/logs/plugins/neuroscore_debug.txt");
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

		$is_any_task_enabled = (intval($_POST['score_is_enabled']) == 1 || intval($_POST['title_is_enabled']) == 1 || intval($_POST['categories_is_enabled']) == 1 || intval($_POST['models_is_enabled']) == 1);

		if (intval($_POST['score_apply_to_feeds_type']) == 0)
		{
			$_POST['score_apply_to_feeds'] = [];
		}
		if (intval($_POST['score_screenshot_retain_option']) == 0)
		{
			$_POST['score_screenshot_retain_count'] = '0';
		}
		if (intval($_POST['title_apply_to_feeds_type']) == 0)
		{
			$_POST['title_apply_to_feeds'] = [];
		}
		if (intval($_POST['categories_apply_to_feeds_type']) == 0)
		{
			$_POST['categories_apply_to_feeds'] = [];
		}
		if (intval($_POST['models_apply_to_feeds_type']) == 0)
		{
			$_POST['models_apply_to_feeds'] = [];
		}

		if ($_POST['api_key'] !== '')
		{
			$balance_response = neuroscoreQueryAPI('GET', 'balance', [], $_POST);
			if ($balance_response['code'] != 200)
			{
				$errors[] = $lang['plugins']['neuroscore']['error_invalid_api_response_code'];
			} elseif (!isset($balance_response['data']['balance_usd']))
			{
				$errors[] = $lang['plugins']['neuroscore']['error_invalid_api_response_format'];
			} else
			{
				$_POST['balance_usd'] = $balance_response['data']['balance_usd'];
			}
		} elseif ($is_any_task_enabled)
		{
			validate_field('empty', $_POST['api_key'], $lang['plugins']['neuroscore']['field_api_key']);
		}

		if ($_POST['score_screenshot_max_count'] != '')
		{
			validate_field('empty_int', $_POST['score_screenshot_max_count'], $lang['plugins']['neuroscore']['divider_score'] . ' - ' . $lang['plugins']['neuroscore']['field_score_screenshot_max_count']);
		}

		if (intval($_POST['score_screenshot_retain_option']) == 1)
		{
			validate_field('empty_int', $_POST['score_screenshot_retain_count'], $lang['plugins']['neuroscore']['divider_score'] . ' - ' . $lang['plugins']['neuroscore']['field_score_screenshot_retain']);
		}

		if (intval($_POST['score_is_enabled']) == 1)
		{
			validate_field('empty', $_POST['score_apply_to'], $lang['plugins']['neuroscore']['divider_score'] . ' - ' . $lang['plugins']['neuroscore']['field_apply_to']);
		}

		if (intval($_POST['title_is_enabled']) == 1)
		{
			validate_field('empty', $_POST['title_apply_to'], $lang['plugins']['neuroscore']['divider_title'] . ' - ' . $lang['plugins']['neuroscore']['field_apply_to']);
		}

		if (intval($_POST['categories_is_enabled']) == 1)
		{
			validate_field('empty', $_POST['categories_apply_to'], $lang['plugins']['neuroscore']['divider_categories'] . ' - ' . $lang['plugins']['neuroscore']['field_apply_to']);
		}

		if (intval($_POST['models_is_enabled']) == 1)
		{
			validate_field('empty', $_POST['models_apply_to'], $lang['plugins']['neuroscore']['divider_models'] . ' - ' . $lang['plugins']['neuroscore']['field_apply_to']);
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
		}

		if (!is_array($errors))
		{
			$save_data = @unserialize(file_get_contents("$plugin_path/data.dat"));
			$save_data['api_key'] = $_POST['api_key'];
			$save_data['balance_usd'] = $_POST['balance_usd'];
			$save_data['on_empty_balance'] = intval($_POST['on_empty_balance']);
			$save_data['is_debug_enabled'] = intval($_POST['is_debug_enabled']);

			$save_data['score_is_enabled'] = intval($_POST['score_is_enabled']);
			$save_data['score_screenshot_type'] = intval($_POST['score_screenshot_type']);
			$save_data['score_screenshot_max_count'] = intval($_POST['score_screenshot_max_count']);
			$save_data['score_screenshot_retain_count'] = intval($_POST['score_screenshot_retain_count']);
			$save_data['score_apply_to'] = $_POST['score_apply_to'] ?? [];
			$save_data['score_apply_to_feeds'] = $_POST['score_apply_to_feeds'] ? array_map('intval', $_POST['score_apply_to_feeds']) : [];
			$save_data['score_apply_to_manual_repeat'] = intval($_POST['score_apply_to_manual_repeat']);

			$save_data['title_is_enabled'] = intval($_POST['title_is_enabled']);
			$save_data['title_is_rewrite_directories'] = intval($_POST['title_is_rewrite_directories']);
			$save_data['title_apply_to'] = $_POST['title_apply_to'] ?? [];
			$save_data['title_apply_to_feeds'] = $_POST['title_apply_to_feeds'] ? array_map('intval', $_POST['title_apply_to_feeds']) : [];
			$save_data['title_apply_to_manual_repeat'] = intval($_POST['title_apply_to_manual_repeat']);

			$save_data['categories_is_enabled'] = intval($_POST['categories_is_enabled']);
			$save_data['categories_apply_to'] = $_POST['categories_apply_to'] ?? [];
			$save_data['categories_apply_to_feeds'] = $_POST['categories_apply_to_feeds'] ? array_map('intval', $_POST['categories_apply_to_feeds']) : [];
			$save_data['categories_apply_to_manual_repeat'] = intval($_POST['categories_apply_to_manual_repeat']);
			$save_data['categories_apply_to_empty'] = intval($_POST['categories_apply_to_empty']);
			$save_data['categories_type'] = trim($_POST['categories_type']);

			$save_data['models_is_enabled'] = intval($_POST['models_is_enabled']);
			$save_data['models_apply_to'] = $_POST['models_apply_to'] ?? [];
			$save_data['models_apply_to_feeds'] = $_POST['models_apply_to_feeds'] ? array_map('intval', $_POST['models_apply_to_feeds']) : [];
			$save_data['models_apply_to_manual_repeat'] = intval($_POST['models_apply_to_manual_repeat']);
			$save_data['models_apply_to_empty'] = intval($_POST['models_apply_to_empty']);

			file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

			if (intval($_POST['is_debug_enabled']) == 0)
			{
				@unlink("$config[project_path]/admin/logs/plugins/neuroscore_debug.txt");
			}
			add_admin_notification('plugins.neuroscore.debug', intval($_POST['is_debug_enabled']));
			add_admin_notification('plugins.neuroscore.balance', $is_any_task_enabled && floatval($_POST['balance_usd']) < 2 ? 1 : 0, 2);

			if (intval($save_data['on_empty_balance']) == 1)
			{
				sql_delete("delete from $config[tables_prefix]videos_advanced_operations where operation_type_id in (1,3,4,5) and operation_status_id=0");
			}

			if ($is_any_task_enabled)
			{
				sql_insert("insert into $config[tables_prefix_multi]admin_processes set pid='cron_plugins.neuroscore', exec_interval=300, status_data='a:0:{}'");
				get_page('', "https://www.kernel-scripts.com/track_feature.php?feature=neuroscore&url=$config[project_url]&api_key=" . urlencode($_POST['api_key']), '', '', 1, 0, 5, '');
			} else
			{
				sql_delete("delete from $config[tables_prefix_multi]admin_processes where pid='cron_plugins.neuroscore'");
			}

			return_ajax_success("$page_name?plugin_id=neuroscore");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	$_POST = @unserialize(file_get_contents("$plugin_path/data.dat"));
	$_POST['feeds'] = mr2array(sql_pr("select * from $config[tables_prefix]videos_feeds_import"));

	if (is_file("$config[project_path]/admin/logs/plugins/neuroscore_debug.txt"))
	{
		$_POST['is_debug_enabled'] = 1;
	}

	if ($_POST['api_key'] !== '')
	{
		$balance_response = neuroscoreQueryAPI('GET', 'balance', [], $_POST);
		if (isset($balance_response['data']['balance_usd']))
		{
			$_POST['balance_usd'] = $balance_response['data']['balance_usd'];
		}
	}

	$stats_types = [
			'score_stats' => 1,
			'title_stats' => 3,
			'categories_stats' => 4,
			'models_stats' => 5,
	];

	$total_postponed = 0;
	$total_processing = 0;
	$total_finished = 0;
	$total_deleted = 0;
	foreach ($stats_types as $key => $operation_type_id)
	{
		$_POST[$key] = mr2array(sql_pr("select ao.operation_status_id, count(*) as tasks from $config[tables_prefix]videos_advanced_operations ao inner join $config[tables_prefix]videos v using (video_id) where ao.operation_type_id=? group by ao.operation_status_id order by ao.operation_status_id asc", $operation_type_id));
		$deleted_tasks = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos_advanced_operations where operation_type_id=? and video_id not in (select video_id from $config[tables_prefix]videos)", $operation_type_id));
		if ($deleted_tasks > 0)
		{
			$_POST[$key][] = [
					'operation_status_id' => 3,
					'tasks' => $deleted_tasks,
			];
		}
		foreach ($_POST[$key] as $stats_item)
		{
			switch ($stats_item['operation_status_id'])
			{
				case 0:
					$total_postponed += $stats_item['tasks'];
					break;
				case 1:
					$total_processing += $stats_item['tasks'];
					break;
				case 2:
					$total_finished += $stats_item['tasks'];
					break;
				case 3:
					$total_deleted += $stats_item['tasks'];
					break;
			}
		}
	}
	$_POST['total_stats_postponed'] = $total_postponed;
	$_POST['total_stats'] = [
			[
					'operation_status_id' => 0,
					'tasks' => $total_postponed,
			], [
					'operation_status_id' => 1,
					'tasks' => $total_processing,
			], [
					'operation_status_id' => 2,
					'tasks' => $total_finished,
			], [
					'operation_status_id' => 3,
					'tasks' => $total_deleted,
			],
	];

	if (!is_writable($plugin_path))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', "$plugin_path/data.dat");
	}
	if (isset($_SESSION['admin_notifications']['list']['plugins.neuroscore.debug']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['plugins.neuroscore.debug']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['plugins.neuroscore.balance']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['plugins.neuroscore.balance']['title'];
	}
}

function neuroscoreCron()
{
	global $config;

	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions_screenshots.php';
	require_once 'functions_admin.php';
	require_once 'pclzip.lib.php';

	ini_set('display_errors', 1);

	$start_time = time();

	neuroscoreInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/neuroscore";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));

	$memory_limit = mr2number(sql("select value from $config[tables_prefix]options where variable='LIMIT_MEMORY'"));
	if ($memory_limit == 0)
	{
		$memory_limit = 512;
	}
	ini_set('memory_limit', "{$memory_limit}M");

	neuroscoreLog('INFO  Starting neuroscore plugin', true);
	neuroscoreLog('INFO  Memory limit: ' . ini_get('memory_limit'), true);

	$balance_response = neuroscoreQueryAPI('GET', 'balance', []);
	neuroscoreLog("INFO  Balance: {$balance_response['data']['balance_usd']}", true);

	if (time() % 86400 <= 600)
	{
		$stats = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos_advanced_operations where operation_type_id in (1,3) and operation_status_id=2"));
		get_page('', "https://www.kernel-scripts.com/track_feature.php?feature=neuroscore&url=$config[project_url]&stats=$stats&api_key=" . urlencode($data['api_key']), '', '', 1, 0, 5, '');
	}

	$options = get_options();

	$categories_all = [];
	$categories_regexp = [];
	if (mr2number(sql_pr("select video_id from $config[tables_prefix]videos_advanced_operations where operation_type_id=4 and operation_status_id=1 limit 1")) > 0)
	{
		$temp = mr2array(sql_pr("select category_id, title, synonyms from $config[tables_prefix]categories"));
		foreach ($temp as $category)
		{
			$categories_all[KvsUtilities::str_lowercase($category['title'])] = $category['category_id'];
			$temp_syn = explode(',', $category['synonyms']);
			if (is_array($temp_syn))
			{
				foreach ($temp_syn as $syn)
				{
					$syn = trim($syn);
					if ($syn !== '')
					{
						if (strpos($syn, '*') !== false)
						{
							$categories_regexp[$syn] = $category['category_id'];
						} else
						{
							$categories_all[KvsUtilities::str_lowercase($syn)] = $category['category_id'];
						}
					}
				}
			}
		}
	}

	$models_all = [];
	if (mr2number(sql_pr("select video_id from $config[tables_prefix]videos_advanced_operations where operation_type_id=5 and operation_status_id=1 limit 1")) > 0)
	{
		$temp = mr2array(sql_pr("select model_id, title, alias from $config[tables_prefix]models"));
		foreach ($temp as $model)
		{
			$models_all[KvsUtilities::str_lowercase($model['title'])] = $model['model_id'];
			$temp_syn = explode(',', $model['alias']);
			if (is_array($temp_syn))
			{
				foreach ($temp_syn as $syn)
				{
					$syn = trim($syn);
					if (strlen($syn) > 0)
					{
						$models_all[KvsUtilities::str_lowercase($syn)] = $model['model_id'];
					}
				}
			}
		}
	}

	add_admin_notification('plugins.neuroscore.balance', (intval($data['score_is_enabled']) == 1 || intval($data['title_is_enabled']) == 1 || intval($data['categories_is_enabled']) == 1 || intval($data['models_is_enabled']) == 1) && floatval($balance_response['data']['balance_usd']) < 2 ? 1 : 0, 2);

	$tasks_finalized = 0;
	$list_formats_overview = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=1"));

	$running_tasks = mr2array(sql_pr("select * from $config[tables_prefix]videos_advanced_operations where operation_type_id in (1,3,4,5) and operation_status_id=1"));
	if (array_cnt($running_tasks) > 0)
	{
		foreach ($running_tasks as $running_task)
		{
			$video_id = $running_task['video_id'];
			$dir_path = get_dir_by_id($video_id);
			$screen_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots";
			$screen_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id";

			$response = neuroscoreQueryAPI('GET', "task/$running_task[operation_task_id]", []);
			if ($response['code'] == 200 && is_array($response['data']['response']))
			{
				$video_data = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
				if (empty($video_data))
				{
					neuroscoreLog("INFO  Task $running_task[operation_task_id] completed for video $video_id, but video is deleted", true);

					log_video('', $video_id);
					log_video("INFO  Task $running_task[operation_task_id] completed, but video deleted", $video_id);
					sql_update("update $config[tables_prefix]videos_advanced_operations set operation_status_id=2, finished_date=? where video_id=? and operation_type_id=?", date('Y-m-d H:i:s'), $video_id, $running_task['operation_type_id']);
					$tasks_finalized++;

					continue;
				}

				if ($running_task['operation_type_id'] == 1)
				{
					if (!isset($response['data']['response']['images']))
					{
						neuroscoreLog("ERROR Task $running_task[operation_task_id] completed for video $video_id, but doesn't provide the expected scoring data", true);
						continue;
					}

					$scoring_data = [];
					$screenshots_data = @unserialize(file_get_contents("$screen_source_dir/info.dat")) ?: [];
					$rotator_data = null;
					if (is_file("$screen_source_dir/rotator.dat"))
					{
						$rotator_data = @unserialize(file_get_contents("$screen_source_dir/rotator.dat"));
					}
					for ($i = 1; $i <= $video_data['screen_amount']; $i++)
					{
						$screen_url = get_video_source_url($video_id, "screenshots/$i.jpg");
						if (strpos($screen_url, '//') === 0)
						{
							$screen_url = "http:$screen_url";
						}
						foreach ($response['data']['response']['images'] as $item)
						{
							if ($item['image_url'] == $screen_url)
							{
								$scoring_data[$i] = floatval($item['detection']['score']);
								if (isset($screenshots_data[$i]))
								{
									$screenshots_data[$i]['score'] = floatval($item['detection']['score']);
								} else
								{
									$screenshots_data[$i] = ['score' => floatval($item['detection']['score'])];
								}
								break;
							}
						}
						if (!isset($scoring_data[$i]))
						{
							$scoring_data[$i] = 0;
							if (isset($screenshots_data[$i]))
							{
								$screenshots_data[$i]['score'] = 0;
							} else
							{
								$screenshots_data[$i] = ['score' => 0];
							}
						}
					}
					arsort($scoring_data, SORT_NUMERIC);

					$scoring_data_str = '';
					foreach ($scoring_data as $k => $v)
					{
						$scoring_data_str .= "#$k: $v, ";
					}
					$scoring_data_str = trim($scoring_data_str, ' ,');

					log_video('', $video_id);
					log_video("INFO  Screenshot scoring: task $running_task[operation_task_id] completed, scores are: $scoring_data_str", $video_id);

					$removed_screenshots = [];
					$main = $video_data['screen_main'];
					$screen_amount = $video_data['screen_amount'];
					if ($data['score_screenshot_retain_count'] > 0 && $screen_amount > $data['score_screenshot_retain_count'] && array_cnt($scoring_data) > $data['score_screenshot_retain_count'])
					{
						$i = 1;
						foreach ($scoring_data as $screen => $weight)
						{
							if ($i == 1)
							{
								$main = $screen;
							}
							if ($i > $data['score_screenshot_retain_count'])
							{
								$removed_screenshots[] = "#$screen";
								log_video("INFO  Screenshot scoring: removing overview screenshot #{$screen} with weight $weight by neuroscore", $video_id);

								@unlink("$screen_source_dir/$screen.jpg");
								foreach ($list_formats_overview as $format)
								{
									@unlink("$screen_target_dir/$format[size]/$screen.jpg");
								}
								if (isset($screenshots_data[$screen]))
								{
									unset($screenshots_data[$screen]);
								}
								if (isset($rotator_data[$screen]))
								{
									unset($rotator_data[$screen]);
								}
							}
							$i++;
						}

						$last_index = 0;
						for ($i = 1; $i <= $screen_amount; $i++)
						{
							if (is_file("$screen_source_dir/$i.jpg"))
							{
								if ($last_index == $i - 1)
								{
									$last_index++;
								} else
								{
									$last_index++;
									if ($i == $main)
									{
										$main = $last_index;
									}
									if (!rename("$screen_source_dir/$i.jpg", "$screen_source_dir/$last_index.jpg"))
									{
										log_video("ERROR Failed to replace file $screen_source_dir/$last_index.jpg", $video_id);
									}
									foreach ($list_formats_overview as $format)
									{
										if (!rename("$screen_target_dir/$format[size]/$i.jpg", "$screen_target_dir/$format[size]/$last_index.jpg"))
										{
											log_video("ERROR Failed to replace file $screen_target_dir/$format[size]/$last_index.jpg", $video_id);
										}
									}
									if (isset($screenshots_data[$i]))
									{
										$screenshots_data[$last_index] = $screenshots_data[$i];
										unset($screenshots_data[$i]);
									}
									if (isset($rotator_data[$i]))
									{
										$rotator_data[$last_index] = $rotator_data[$i];
										unset($rotator_data[$i]);
									}
								}
							}
						}

						for ($i = 1; $i <= $screen_amount; $i++)
						{
							if (!is_file("$screen_source_dir/$i.jpg"))
							{
								copy("$screen_source_dir/$main.jpg", "$screen_source_dir/$i.jpg");
								foreach ($list_formats_overview as $format)
								{
									copy("$screen_target_dir/$format[size]/$main.jpg", "$screen_target_dir/$format[size]/$i.jpg");
								}
							}
						}
						sql_insert("insert into $config[tables_prefix]background_tasks_postponed set type_id=8, video_id=?, data=?, added_date=?, due_date=DATE_ADD(added_date, INTERVAL 48 HOUR)", $video_id, serialize(['old_screen_amount' => $screen_amount]), date('Y-m-d H:i:s'));

						if (isset($rotator_data))
						{
							@file_put_contents("$screen_source_dir/rotator.dat", serialize($rotator_data), LOCK_EX);
						}

						$screen_amount = intval($data['score_screenshot_retain_count']);
						foreach ($list_formats_overview as $format)
						{
							if ($format['is_create_zip'] == 1)
							{
								log_video("INFO  Screenshot scoring: replacing screenshots ZIP for \"$format[title]\" format", $video_id);
								@unlink("$screen_target_dir/$format[size]/$video_id-$format[size].zip");

								$zip_files_to_add = array();
								for ($i = 1; $i <= $screen_amount; $i++)
								{
									$zip_files_to_add[] = "$screen_target_dir/$format[size]/$i.jpg";
								}
								$zip = new PclZip("$screen_target_dir/$format[size]/$video_id-$format[size].zip");
								$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = "$screen_target_dir/$format[size]");
							}
						}
					} else
					{
						$i = 1;
						foreach ($scoring_data as $screen => $weight)
						{
							if ($i == 1)
							{
								$main = $screen;
								break;
							}
						}
					}

					if ($video_data['screen_main'] != $main)
					{
						log_video("INFO  Screenshot scoring: changing main screenshot from #{$video_data['screen_main']} to #$main", $video_id);
					}

					$video_formats = get_video_formats($video_id, $video_data['file_formats']);
					if (copy("$screen_source_dir/$main.jpg", "$screen_target_dir/preview.jpg"))
					{
						foreach ($video_formats as $format)
						{
							try
							{
								KvsImagemagick::resize_image(KvsImagemagick::RESIZE_TYPE_MAX_SIZE, "$screen_target_dir/preview.jpg", "$screen_target_dir/preview{$format['postfix']}.jpg", $format['dimensions'][0] . 'x' . $format['dimensions'][1], true);
							} catch (KvsException $e)
							{
								log_video("WARN  Failed to create player preview preview{$format['postfix']}.jpg: " . KvsContext::get_last_error_message(), $video_id);
							}
						}
					}

					@file_put_contents("$screen_source_dir/info.dat", serialize($screenshots_data), LOCK_EX);
					sql_update("update $config[tables_prefix]videos set screen_amount=?, screen_main=? where video_id=?", $screen_amount, $main, $video_id);
					neuroscoreLog("INFO  Screenshot scoring: task $running_task[operation_task_id] completed for video $video_id, scores are: $scoring_data_str" . (array_cnt($removed_screenshots) > 0 ? ', removed ' . implode(', ', $removed_screenshots) . ' screenshots' : ''), true);
				} elseif ($running_task['operation_type_id'] == 3)
				{
					if (!isset($response['data']['response']['summary']) || !is_array($response['data']['response']['summary']['new_titles']) || !is_array($response['data']['response']['summary']['new_titles'][0]['titles']))
					{
						neuroscoreLog("ERROR Task $running_task[operation_task_id] completed for video $video_id, but doesn't provide the expected new_titles data", true);
						continue;
					}

					$new_title = trim($response['data']['response']['summary']['new_titles'][0]['titles'][0]);
					if ($new_title === '')
					{
						neuroscoreLog("ERROR Task $running_task[operation_task_id] completed for video $video_id, but provides empty title", true);
						continue;
					}

					$update_array = [
						'title' => $new_title
					];
					if (intval($data['title_is_rewrite_directories']) == 1)
					{
						$dir = get_correct_dir_name($update_array['title']);
						$temp_dir = $dir;
						for ($i = 2; $i < 999999; $i++)
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where dir=? and video_id!=?", $temp_dir, $video_id)) == 0)
							{
								$dir = $temp_dir;
								break;
							}
							$temp_dir = $dir . $i;
						}
						$update_array['dir'] = $dir;
					}

					$update_details = '';
					foreach ($update_array as $field => $value)
					{
						if ($video_data[$field] == $value)
						{
							unset($update_array[$field]);
						} else
						{
							$update_details .= "$field, ";
						}
					}
					if (array_cnt($update_array) > 0)
					{
						$update_details = substr($update_details, 0, -2);
						sql_update("update $config[tables_prefix]videos set ?% where video_id=?", $update_array, $video_id);
						sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='neuroscore', action_id=168, object_id=?, object_type_id=1, action_details=?, added_date=?", $video_id, $update_details, date('Y-m-d H:i:s'));
					}

					log_video('', $video_id);
					log_video("INFO  Title rewrite: task $running_task[operation_task_id] completed, new title is \"$new_title\"", $video_id);

					neuroscoreLog("INFO  Title rewrite: task $running_task[operation_task_id] completed for video $video_id, new title is \"$new_title\"", true);
				} elseif ($running_task['operation_type_id'] == 4)
				{
					if (!isset($response['data']['response']['summary']) || !is_array($response['data']['response']['summary']['categories']))
					{
						neuroscoreLog("ERROR Task $running_task[operation_task_id] completed for video $video_id, but doesn't provide the expected categories data", true);
						continue;
					}

					$detected_categories = [];
					$added_categories = [];
					foreach ($response['data']['response']['summary']['categories'] as $category_data)
					{
						$category_title = trim($category_data['label']);
						if ($category_title === '')
						{
							neuroscoreLog("ERROR Task $running_task[operation_task_id] completed for video $video_id, but provided empty category", true);
							continue 2;
						}

						$detected_categories[] = $category_title;

						if ($data['categories_type'] == 'tags')
						{
							$tag_id = find_or_create_tag($category_title, $options);
							if ($tag_id > 0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]tags_videos where tag_id=? and video_id=?", $tag_id, $video_id)) == 0)
							{
								sql_pr("insert into $config[tables_prefix]tags_videos set tag_id=?, video_id=?", $tag_id, $video_id);
								$added_categories[] = $tag_id;
							}
						} else
						{
							$category_title = KvsUtilities::str_uppercase_first($category_title);
							if ($categories_all[KvsUtilities::str_lowercase($category_title)] > 0)
							{
								$category_id = $categories_all[KvsUtilities::str_lowercase($category_title)];
							} else
							{
								$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $category_title));
								if ($category_id == 0)
								{
									foreach ($categories_regexp as $regexp => $temp_id)
									{
										$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
										if (preg_match("/^$regexp$/iu", $category_title))
										{
											$category_id = $temp_id;
											break;
										}
									}
								}
								if ($category_id == 0)
								{
									$cat_dir = get_correct_dir_name($category_title);
									$temp_dir = $cat_dir;
									for ($it = 2; $it < 999999; $it++)
									{
										if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?", $temp_dir)) == 0)
										{
											$cat_dir = $temp_dir;
											break;
										}
										$temp_dir = $cat_dir . $it;
									}
									$category_id = sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?", $category_title, $cat_dir, date('Y-m-d H:i:s'));
									sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='neuroscore', action_id=130, object_id=?, object_type_id=6, added_date=?", $category_id, date('Y-m-d H:i:s'));
								}
								if ($category_id > 0)
								{
									$categories_all[KvsUtilities::str_lowercase($category_title)] = $category_id;
								}
							}
							if ($category_id > 0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]categories_videos where category_id=? and video_id=?", $category_id, $video_id)) == 0)
							{
								sql_pr("insert into $config[tables_prefix]categories_videos set category_id=?, video_id=?", $category_id, $video_id);
								$added_categories[] = $category_id;
							}
						}
					}

					$categories_type = 'categories';
					if ($data['categories_type'] == 'tags')
					{
						$categories_type = 'tags';
					}

					log_video('', $video_id);
					log_video("INFO  Category auto-selection: task $running_task[operation_task_id] completed, detected $categories_type \"" . implode(', ', $detected_categories) . "\", added $categories_type \"" . implode(', ', $added_categories) . "\"", $video_id);

					neuroscoreLog("INFO  Category auto-selection: task $running_task[operation_task_id] completed for video $video_id, detected $categories_type \"" . implode(', ', $detected_categories) . "\", added $categories_type \"" . implode(', ', $added_categories) . "\"", true);
				} elseif ($running_task['operation_type_id'] == 5)
				{
					if (!isset($response['data']['response']['summary']) || !is_array($response['data']['response']['summary']['faces']))
					{
						neuroscoreLog("ERROR Task $running_task[operation_task_id] completed for video $video_id, but doesn't provide the expected faces data", true);
						continue;
					}

					$detected_models = [];
					$added_models = [];
					foreach ($response['data']['response']['summary']['faces'] as $model_data)
					{
						$model_title = trim($model_data['label']);
						if ($model_title === '')
						{
							neuroscoreLog("ERROR Task $running_task[operation_task_id] completed for video $video_id, but provided empty model", true);
							continue 2;
						}

						$detected_models[] = $model_title;

						if ($models_all[KvsUtilities::str_lowercase($model_title)] > 0)
						{
							$model_id = $models_all[KvsUtilities::str_lowercase($model_title)];
						} else
						{
							$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $model_title));
							if ($model_id == 0)
							{
								$cat_dir = get_correct_dir_name($model_title);
								$temp_dir = $cat_dir;
								for ($it = 2; $it < 999999; $it++)
								{
									if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where dir=?", $temp_dir)) == 0)
									{
										$cat_dir = $temp_dir;
										break;
									}
									$temp_dir = $cat_dir . $it;
								}
								$model_id = sql_insert("insert into $config[tables_prefix]models set title=?, dir=?, rating_amount=1, added_date=?", $model_title, $cat_dir, date('Y-m-d H:i:s'));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='neuroscore', action_id=130, object_id=?, object_type_id=4, added_date=?", $model_id, date('Y-m-d H:i:s'));
							}
							if ($model_id > 0)
							{
								$models_all[KvsUtilities::str_lowercase($model_title)] = $model_id;
							}
						}
						if ($model_id > 0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]models_videos where model_id=? and video_id=?", $model_id, $video_id)) == 0)
						{
							sql_pr("insert into $config[tables_prefix]models_videos set model_id=?, video_id=?", $model_id, $video_id);
							$added_models[] = $model_id;
						}
					}

					log_video('', $video_id);
					log_video("INFO  Model auto-selection: task $running_task[operation_task_id] completed, detected models \"" . implode(', ', $detected_models) . "\", added models \"" . implode(', ', $added_models) . "\"", $video_id);

					neuroscoreLog("INFO  Model auto-selection: task $running_task[operation_task_id] completed for video $video_id, detected models \"" . implode(', ', $detected_models) . "\", added models \"" . implode(', ', $added_models) . "\"", true);
				}

				sql_update("update $config[tables_prefix]videos_advanced_operations set operation_status_id=2, finished_date=? where video_id=? and operation_type_id=?", date('Y-m-d H:i:s'), $video_id, $running_task['operation_type_id']);
				$tasks_finalized++;
			} else
			{
				neuroscoreLog("INFO  Task $running_task[operation_task_id] not yet finished for video $video_id", true);

				log_video('', $video_id);
				log_video("INFO  Task $running_task[operation_task_id] not yet finished", $video_id);
			}
		}
	}

	if ($balance_response['data']['balance_points'] > 10000)
	{
		$postponed_task_types = [0];
		if ($data['score_is_enabled'] == 1)
		{
			$postponed_task_types[] = 1;
		}
		if ($data['title_is_enabled'] == 1)
		{
			$postponed_task_types[] = 3;
		}
		if ($data['categories_is_enabled'] == 1)
		{
			$postponed_task_types[] = 4;
		}
		if ($data['models_is_enabled'] == 1)
		{
			$postponed_task_types[] = 5;
		}
		$postponed_task_types_str = implode(',', $postponed_task_types);

		$postponed_tasks = mr2array(sql_pr("select * from $config[tables_prefix]videos_advanced_operations where operation_type_id in ($postponed_task_types_str) and operation_status_id=0"));
		if (array_cnt($postponed_tasks) > 0)
		{
			neuroscoreLog('INFO  Starting postponed tasks', true);
			foreach ($postponed_tasks as $postponed_task)
			{
				$video_id = $postponed_task['video_id'];
				$video_data = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));

				if (empty($video_data))
				{
					sql_delete("delete from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=?", $video_id, $postponed_task['operation_type_id']);
					neuroscoreLog("INFO  Video $video_id deleted", true);
					continue;
				}

				if ($postponed_task['operation_type_id'] == 1)
				{
					$task_data = ['tasks' => ['score' => true], 'image_urls' => [], 'referrer' => '8ecf2c2c-093c-52fd-8997-cbc9e9779a6c'];

					$screenshots_count = $video_data['screen_amount'];
					if ($data['score_screenshot_max_count'] > 0)
					{
						$screenshots_count = min($screenshots_count, $data['score_screenshot_max_count']);
					}
					for ($i = 1; $i <= $screenshots_count; $i++)
					{
						$url = get_video_source_url($video_id, "screenshots/$i.jpg");
						if (strpos($url, '//') === 0)
						{
							$url = "http:$url";
						}
						$task_data['image_urls'][] = $url;
					}

					$response = neuroscoreQueryAPI('POST', 'task/ns/imagetoolbox', $task_data);
					if ($response['code'] == 202 && $response['data']['task_id'] != '')
					{
						sql_update("update $config[tables_prefix]videos_advanced_operations set operation_status_id=1, operation_task_id=? where video_id=? and operation_type_id=?", $response['data']['task_id'], $video_id, $postponed_task['operation_type_id']);
						neuroscoreLog("INFO  Screenshot scoring: task submitted for video $video_id => checking progress on background (task #{$response['data']['task_id']})", true);

						log_video('', $video_id);
						log_video("INFO  Screenshot scoring: task submitted => checking progress on background (task #{$response['data']['task_id']})", $video_id);
					} else
					{
						neuroscoreLog("INFO  Screenshot scoring: postponed task not submitted for video $video_id", true);
					}
				} elseif ($postponed_task['operation_type_id'] == 3)
				{
					$video_categories = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_videos.category_id) from $config[tables_prefix]categories_videos where $config[tables_prefix]categories_videos.video_id=? order by id asc", $video_id));
					$video_tags = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=? order by id asc", $video_id));
					$video_models = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_videos.model_id) from $config[tables_prefix]models_videos where $config[tables_prefix]models_videos.video_id=? order by id asc", $video_id));
					$task_data = [
							'tasks' => ['rewrite' => true],
							'referrer' => '8ecf2c2c-093c-52fd-8997-cbc9e9779a6c',
							'title' => [
								'id' => $video_id,
								'title' => $video_data['title'],
								'tags' => array_merge($video_categories, $video_tags),
								'models' => $video_models,
							]
					];

					$response = neuroscoreQueryAPI('POST', 'task/ns/imagetoolbox', $task_data);
					if ($response['code'] == 202 && $response['data']['task_id'] != '')
					{
						sql_update("update $config[tables_prefix]videos_advanced_operations set operation_status_id=1, operation_task_id=? where video_id=? and operation_type_id=?", $response['data']['task_id'], $video_id, $postponed_task['operation_type_id']);
						neuroscoreLog("INFO  Title rewrite: task submitted for video $video_id => checking progress on background (task #{$response['data']['task_id']})", true);

						log_video('', $video_id);
						log_video("INFO  Title rewrite: task submitted => checking progress on background (task #{$response['data']['task_id']})", $video_id);
					} else
					{
						neuroscoreLog("INFO  Title rewrite: postponed task not submitted for video $video_id", true);
					}
				} elseif ($postponed_task['operation_type_id'] == 4)
				{
					$task_data = ['tasks' => ['category' => true], 'image_urls' => [], 'referrer' => '8ecf2c2c-093c-52fd-8997-cbc9e9779a6c'];

					for ($i = 1; $i <= $video_data['screen_amount']; $i++)
					{
						$url = get_video_source_url($video_id, "screenshots/$i.jpg");
						if (strpos($url, '//') === 0)
						{
							$url = "http:$url";
						}
						$task_data['image_urls'][] = $url;
					}

					$video_categories = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_videos.category_id) from $config[tables_prefix]categories_videos where $config[tables_prefix]categories_videos.video_id=? order by id asc", $video_id));
					$video_tags = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=? order by id asc", $video_id));
					$video_models = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_videos.model_id) from $config[tables_prefix]models_videos where $config[tables_prefix]models_videos.video_id=? order by id asc", $video_id));
					$task_data['title'] = [
						'id' => $video_id,
						'title' => $video_data['title'],
						'tags' => array_merge($video_categories, $video_tags),
						'models' => $video_models,
					];

					$response = neuroscoreQueryAPI('POST', 'task/ns/imagetoolbox', $task_data);
					if ($response['code'] == 202 && $response['data']['task_id'] != '')
					{
						sql_update("update $config[tables_prefix]videos_advanced_operations set operation_status_id=1, operation_task_id=? where video_id=? and operation_type_id=?", $response['data']['task_id'], $video_id, $postponed_task['operation_type_id']);
						neuroscoreLog("INFO  Category auto-selection: task submitted for video $video_id => checking progress on background (task #{$response['data']['task_id']})", true);

						log_video('', $video_id);
						log_video("INFO  Category auto-selection: task submitted => checking progress on background (task #{$response['data']['task_id']})", $video_id);
					} else
					{
						neuroscoreLog("INFO  Category auto-selection: postponed task not submitted for video $video_id", true);
					}
				} elseif ($postponed_task['operation_type_id'] == 5)
				{
					$task_data = ['tasks' => ['faces' => true], 'image_urls' => [], 'referrer' => '8ecf2c2c-093c-52fd-8997-cbc9e9779a6c'];

					for ($i = 1; $i <= $video_data['screen_amount']; $i++)
					{
						$url = get_video_source_url($video_id, "screenshots/$i.jpg");
						if (strpos($url, '//') === 0)
						{
							$url = "http:$url";
						}
						$task_data['image_urls'][] = $url;
					}

					$video_categories = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_videos.category_id) from $config[tables_prefix]categories_videos where $config[tables_prefix]categories_videos.video_id=? order by id asc", $video_id));
					$video_tags = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=? order by id asc", $video_id));
					$video_models = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_videos.model_id) from $config[tables_prefix]models_videos where $config[tables_prefix]models_videos.video_id=? order by id asc", $video_id));
					$task_data['title'] = [
						'id' => $video_id,
						'title' => $video_data['title'],
						'tags' => array_merge($video_categories, $video_tags),
						'models' => $video_models,
					];

					$response = neuroscoreQueryAPI('POST', 'task/ns/imagetoolbox', $task_data);
					if ($response['code'] == 202 && $response['data']['task_id'] != '')
					{
						sql_update("update $config[tables_prefix]videos_advanced_operations set operation_status_id=1, operation_task_id=? where video_id=? and operation_type_id=?", $response['data']['task_id'], $video_id, $postponed_task['operation_type_id']);
						neuroscoreLog("INFO  Model auto-selection: task submitted for video $video_id => checking progress on background (task #{$response['data']['task_id']})", true);

						log_video('', $video_id);
						log_video("INFO  Model auto-selection: task submitted => checking progress on background (task #{$response['data']['task_id']})", $video_id);
					} else
					{
						neuroscoreLog("INFO  Model auto-selection: postponed task not submitted for video $video_id", true);
					}
				}
			}
		} else
		{
			neuroscoreLog('INFO  No postponed tasks', true);
		}
	}

	sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_plugins.neuroscore'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));
	neuroscoreLog("INFO  Total finalized $tasks_finalized tasks", true);
}

function neuroscoreQueryAPI($method, $endpoint, $params = [], $test_mode = null)
{
	global $config;

	neuroscoreInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/neuroscore";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if (is_array($test_mode))
	{
		$data = $test_mode;
	}
	if (!is_array($data) || !$data['api_key'])
	{
		return [];
	}

	if (!in_array(strtoupper($method), ['GET', 'POST']))
	{
		return [];
	}

	$is_debug_enabled = false;
	if (intval($data['is_debug_enabled']) == 1)
	{
		$is_debug_enabled = true;
	}

	$api_params = '';
	if (array_cnt($params) > 0)
	{
		$api_params = json_encode(['payload' => $params]);
	}

	$api_url = "https://api.neuroscore.ai/api/v1/$endpoint";
	$headers = [
			"Accept: application/json",
			"Content-Type: application/json",
			"X-Api-Key: $data[api_key]",
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
					CURLOPT_CUSTOMREQUEST => strtoupper($method),
					CURLOPT_HTTPHEADER => $headers,
			)
	);
	if (strtoupper($method) != 'GET' && $api_params)
	{
		curl_setopt($curl, CURLOPT_POSTFIELDS, $api_params);
		if ($is_debug_enabled)
		{
			file_put_contents("$config[project_path]/admin/logs/plugins/neuroscore_debug.txt", "[" . date("Y-m-d H:i:s") . "] POST data -------------- \n\n$api_params\n\n", FILE_APPEND | LOCK_EX);
		}
	}

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
		$response = @json_decode($response, true);
		if ($response && $response['data'])
		{
			$result['data'] = $response['data'];
		}
	} else
	{
		$response = [];
	}
	if (curl_errno($curl))
	{
		file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt", "[" . date("Y-m-d H:i:s") . "] [" . curl_errno($curl) . "] " . curl_error($curl) . "\n", FILE_APPEND | LOCK_EX);
	}

	if ($verbose)
	{
		rewind($verbose);
		file_put_contents("$config[project_path]/admin/logs/plugins/neuroscore_debug.txt", "[" . date("Y-m-d H:i:s") . "] CURL request -------------- \n\n" . stream_get_contents($verbose) . "\n", FILE_APPEND | LOCK_EX);
	}

	if ($response && $is_debug_enabled)
	{
		file_put_contents("$config[project_path]/admin/logs/plugins/neuroscore_debug.txt", "[" . date("Y-m-d H:i:s") . "] RESPONSE data -------------- \n\n" . json_encode($response) . "\n\n", FILE_APPEND | LOCK_EX);
	}

	$result['code'] = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	if (intval($response['code']) > 0)
	{
		$result['code'] = $response['code'];
		$result['message'] = trim($response['message']);
	}
	curl_close($curl);

	return $result;
}

function neuroscoreLog($message, $full_date = false)
{
	if ($full_date)
	{
		echo date('[Y-m-d H:i:s] ') . $message . "\n";
	} else
	{
		echo date("...... [H:i:s] ") . $message . "\n";
	}
}

if ($_SERVER['argv'][1] == 'exec' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';

	$object_type = $_SERVER['argv'][2];
	$object_id = intval($_SERVER['argv'][3]);
	$object_state = trim($_SERVER['argv'][4]);

	if ($object_type != 'video')
	{
		return;
	}

	$data = [];

	$plugin_path = "$config[project_path]/admin/data/plugins/neuroscore";
	if (is_file("$plugin_path/data.dat"))
	{
		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	}

	if (intval($data['score_is_enabled']) == 0 && intval($data['title_is_enabled']) == 0 && intval($data['categories_is_enabled']) == 0 && intval($data['models_is_enabled']) == 0)
	{
		return;
	}

	$video_id = $object_id;
	$dir_path = get_dir_by_id($video_id);
	$video_data = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));

	$execution_mode = 'manual';
	if ($object_state == 'new')
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

	if (intval($data['score_is_enabled']) == 1)
	{
		$submit_screenshot_scoring = true;
		if (!in_array($execution_mode, $data['score_apply_to']))
		{
			neuroscoreLog("INFO  Screenshot scoring: execution mode is not enabled in plugin settings ($execution_mode)");
			$submit_screenshot_scoring = false;
		} elseif ($execution_mode == 'feeds' && array_cnt($data['score_apply_to_feeds']) > 0)
		{
			if (!in_array($video_data['feed_id'], $data['score_apply_to_feeds']))
			{
				neuroscoreLog("INFO  Screenshot scoring: new video added by feed not enabled in plugin settings ($video_data[feed_id])");
				$submit_screenshot_scoring = false;
			}
		}

		if ($submit_screenshot_scoring)
		{
			$existing_task = mr2array_single(sql_pr("select * from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=1 order by added_date desc", $video_id));
			if (!empty($existing_task) && ($existing_task['operation_status_id'] == 0 || $existing_task['operation_status_id'] == 1 || ($existing_task['operation_status_id'] == 2 && intval($data['score_apply_to_manual_repeat']) == 0)))
			{
				neuroscoreLog('INFO  Screenshot scoring: skipping this video as it already has screenshot scoring task scheduled or submitted');
			} else
			{
				$task_data = ['tasks' => ['score' => true], 'image_urls' => [], 'referrer' => '8ecf2c2c-093c-52fd-8997-cbc9e9779a6c'];
				$screenshots_data = @unserialize(file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/info.dat")) ?: [];

				$screenshots_count = $video_data['screen_amount'];
				if ($data['score_screenshot_max_count'] > 0)
				{
					$screenshots_count = min($screenshots_count, $data['score_screenshot_max_count']);
				}
				for ($i = 1; $i <= $screenshots_count; $i++)
				{
					$screenshot_type = 'auto';
					if (isset($screenshots_data[$i]))
					{
						$screenshot_type = $screenshots_data[$i]['type'];
					}
					if ($screenshot_type == 'uploaded' && $data['score_screenshot_type'] == 1)
					{
						neuroscoreLog('INFO  Screenshot scoring: skipping this video as it has manually uploaded screenshots');
						$submit_screenshot_scoring = false;
						break;
					}
					$url = get_video_source_url($video_id, "screenshots/$i.jpg");
					if (strpos($url, '//') === 0)
					{
						$url = "http:$url";
					}
					$task_data['image_urls'][] = $url;
				}

				if ($submit_screenshot_scoring)
				{
					$response = neuroscoreQueryAPI('POST', 'task/ns/imagetoolbox', $task_data);
					if ($response['code'] == 202 && $response['data']['task_id'] != '')
					{
						sql_delete("delete from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=1", $video_id);
						sql_insert("insert into $config[tables_prefix]videos_advanced_operations set video_id=?, operation_type_id=1, operation_status_id=1, operation_task_id=?, added_date=?", $video_id, $response['data']['task_id'], date('Y-m-d H:i:s'));
						neuroscoreLog("INFO  Screenshot scoring: task submitted => checking progress on background (task #{$response['data']['task_id']})");
					} elseif ($response['code'] == 402 && $data['on_empty_balance'] == 0)
					{
						sql_delete("delete from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=1", $video_id);
						sql_insert("insert into $config[tables_prefix]videos_advanced_operations set video_id=?, operation_type_id=1, operation_status_id=0, added_date=?", $video_id, date('Y-m-d H:i:s'));
						neuroscoreLog('INFO  Screenshot scoring: task rejected => postponed for the future');
					} else
					{
						neuroscoreLog('ERROR Screenshot scoring: task rejected => ignored');
					}
				}
			}
		}
	}

	if (intval($data['title_is_enabled']) == 1)
	{
		$submit_title = true;
		if (!in_array($execution_mode, $data['title_apply_to']))
		{
			neuroscoreLog("INFO  Title rewrite: execution mode is not enabled in plugin settings ($execution_mode)");
			$submit_title = false;
		} elseif ($execution_mode == 'feeds' && array_cnt($data['title_apply_to_feeds']) > 0)
		{
			if (!in_array($video_data['feed_id'], $data['title_apply_to_feeds']))
			{
				neuroscoreLog("INFO  Title rewrite: new video added by feed not enabled in plugin settings ($video_data[feed_id])");
				$submit_title = false;
			}
		}

		if ($submit_title)
		{
			$existing_task = mr2array_single(sql_pr("select * from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=3 order by added_date desc", $video_id));
			if (!empty($existing_task) && ($existing_task['operation_status_id'] == 0 || $existing_task['operation_status_id'] == 1 || ($existing_task['operation_status_id'] == 2 && intval($data['title_apply_to_manual_repeat']) == 0)))
			{
				neuroscoreLog('INFO  Title rewrite: skipping this video as it already has title rewrite task scheduled or submitted');
			} else
			{
				$video_categories = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_videos.category_id) from $config[tables_prefix]categories_videos where $config[tables_prefix]categories_videos.video_id=? order by id asc", $video_id));
				$video_tags = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=? order by id asc", $video_id));
				$video_models = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_videos.model_id) from $config[tables_prefix]models_videos where $config[tables_prefix]models_videos.video_id=? order by id asc", $video_id));
				$task_data = [
						'tasks' => ['rewrite' => true],
						'referrer' => '8ecf2c2c-093c-52fd-8997-cbc9e9779a6c',
						'title' => [
							'id' => $video_id,
							'title' => $video_data['title'],
							'tags' => array_merge($video_categories, $video_tags),
							'models' => $video_models,
						]
				];

				$response = neuroscoreQueryAPI('POST', 'task/ns/imagetoolbox', $task_data);
				if ($response['code'] == 202 && $response['data']['task_id'] != '')
				{
					sql_delete("delete from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=3", $video_id);
					sql_insert("insert into $config[tables_prefix]videos_advanced_operations set video_id=?, operation_type_id=3, operation_status_id=1, operation_task_id=?, added_date=?", $video_id, $response['data']['task_id'], date('Y-m-d H:i:s'));
					neuroscoreLog("INFO  Title rewrite: task submitted => checking progress on background (task #{$response['data']['task_id']})");
				} elseif ($response['code'] == 402 && $data['on_empty_balance'] == 0)
				{
					sql_delete("delete from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=3", $video_id);
					sql_insert("insert into $config[tables_prefix]videos_advanced_operations set video_id=?, operation_type_id=3, operation_status_id=0, added_date=?", $video_id, date('Y-m-d H:i:s'));
					neuroscoreLog('INFO  Title rewrite: task rejected => postponed for the future');
				} else
				{
					neuroscoreLog('ERROR Title rewrite: task rejected => ignored');
				}
			}
		}
	}

	if (intval($data['categories_is_enabled']) == 1)
	{
		$submit_categories = true;
		if (!in_array($execution_mode, $data['categories_apply_to']))
		{
			neuroscoreLog("INFO  Category auto-selection: execution mode is not enabled in plugin settings ($execution_mode)");
			$submit_categories = false;
		} elseif ($execution_mode == 'feeds' && array_cnt($data['categories_apply_to_feeds']) > 0)
		{
			if (!in_array($video_data['feed_id'], $data['categories_apply_to_feeds']))
			{
				neuroscoreLog("INFO  Category auto-selection: new video added by feed not enabled in plugin settings ($video_data[feed_id])");
				$submit_categories = false;
			}
		}

		if ($submit_categories)
		{
			$existing_task = mr2array_single(sql_pr("select * from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=4 order by added_date desc", $video_id));
			if (!empty($existing_task) && ($existing_task['operation_status_id'] == 0 || $existing_task['operation_status_id'] == 1 || ($existing_task['operation_status_id'] == 2 && intval($data['categories_apply_to_manual_repeat']) == 0)))
			{
				neuroscoreLog('INFO  Category auto-selection: skipping this video as it already has category auto-selection task scheduled or submitted');
			} else
			{
				$task_data = ['tasks' => ['category' => true], 'image_urls' => [], 'referrer' => '8ecf2c2c-093c-52fd-8997-cbc9e9779a6c'];

				for ($i = 1; $i <= $video_data['screen_amount']; $i++)
				{
					$url = get_video_source_url($video_id, "screenshots/$i.jpg");
					if (strpos($url, '//') === 0)
					{
						$url = "http:$url";
					}
					$task_data['image_urls'][] = $url;
				}

				$video_categories = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_videos.category_id) from $config[tables_prefix]categories_videos where $config[tables_prefix]categories_videos.video_id=? order by id asc", $video_id));
				$video_tags = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=? order by id asc", $video_id));
				$video_models = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_videos.model_id) from $config[tables_prefix]models_videos where $config[tables_prefix]models_videos.video_id=? order by id asc", $video_id));
				$task_data['title'] = [
					'id' => $video_id,
					'title' => $video_data['title'],
					'tags' => array_merge($video_categories, $video_tags),
					'models' => $video_models,
				];

				if ($data['categories_type'] == 'tags')
				{
					if ($data['categories_apply_to_empty'] == 1 && array_cnt($video_tags) > 0)
					{
						neuroscoreLog('INFO  Category auto-selection: skipping this video as it has tags');
						$submit_categories = false;
					}
				} else
				{
					if ($data['categories_apply_to_empty'] == 1 && array_cnt($video_categories) > 0)
					{
						neuroscoreLog('INFO  Category auto-selection: skipping this video as it has categories');
						$submit_categories = false;
					}
				}

				if ($submit_categories)
				{
					$response = neuroscoreQueryAPI('POST', 'task/ns/imagetoolbox', $task_data);
					if ($response['code'] == 202 && $response['data']['task_id'] != '')
					{
						sql_delete("delete from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=4", $video_id);
						sql_insert("insert into $config[tables_prefix]videos_advanced_operations set video_id=?, operation_type_id=4, operation_status_id=1, operation_task_id=?, added_date=?", $video_id, $response['data']['task_id'], date('Y-m-d H:i:s'));
						neuroscoreLog("INFO  Category auto-selection: task submitted => checking progress on background (task #{$response['data']['task_id']})");
					} elseif ($response['code'] == 402 && $data['on_empty_balance'] == 0)
					{
						sql_delete("delete from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=4", $video_id);
						sql_insert("insert into $config[tables_prefix]videos_advanced_operations set video_id=?, operation_type_id=4, operation_status_id=0, added_date=?", $video_id, date('Y-m-d H:i:s'));
						neuroscoreLog('INFO  Category auto-selection: task rejected => postponed for the future');
					} else
					{
						neuroscoreLog('ERROR Category auto-selection: task rejected => ignored');
					}
				}
			}
		}
	}

	if (intval($data['models_is_enabled']) == 1)
	{
		$submit_models = true;
		if (!in_array($execution_mode, $data['models_apply_to']))
		{
			neuroscoreLog("INFO  Model auto-selection: execution mode is not enabled in plugin settings ($execution_mode)");
			$submit_models = false;
		} elseif ($execution_mode == 'feeds' && array_cnt($data['models_apply_to_feeds']) > 0)
		{
			if (!in_array($video_data['feed_id'], $data['models_apply_to_feeds']))
			{
				neuroscoreLog("INFO  Model auto-selection: new video added by feed not enabled in plugin settings ($video_data[feed_id])");
				$submit_models = false;
			}
		}

		if ($submit_models)
		{
			$existing_task = mr2array_single(sql_pr("select * from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=5 order by added_date desc", $video_id));
			if (!empty($existing_task) && ($existing_task['operation_status_id'] == 0 || $existing_task['operation_status_id'] == 1 || ($existing_task['operation_status_id'] == 2 && intval($data['models_apply_to_manual_repeat']) == 0)))
			{
				neuroscoreLog('INFO  Model auto-selection: skipping this video as it already has model auto-selection task scheduled or submitted');
			} else
			{
				$task_data = ['tasks' => ['faces' => true], 'image_urls' => [], 'referrer' => '8ecf2c2c-093c-52fd-8997-cbc9e9779a6c'];

				for ($i = 1; $i <= $video_data['screen_amount']; $i++)
				{
					$url = get_video_source_url($video_id, "screenshots/$i.jpg");
					if (strpos($url, '//') === 0)
					{
						$url = "http:$url";
					}
					$task_data['image_urls'][] = $url;
				}

				$video_categories = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_videos.category_id) from $config[tables_prefix]categories_videos where $config[tables_prefix]categories_videos.video_id=? order by id asc", $video_id));
				$video_tags = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=? order by id asc", $video_id));
				$video_models = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_videos.model_id) from $config[tables_prefix]models_videos where $config[tables_prefix]models_videos.video_id=? order by id asc", $video_id));
				$task_data['title'] = [
					'id' => $video_id,
					'title' => $video_data['title'],
					'tags' => array_merge($video_categories, $video_tags),
					'models' => $video_models,
				];

				if ($data['models_apply_to_empty'] == 1 && array_cnt($video_models) > 0)
				{
					neuroscoreLog('INFO  Model auto-selection: skipping this video as it has models');
					$submit_models = false;
				}

				if ($submit_models)
				{
					$response = neuroscoreQueryAPI('POST', 'task/ns/imagetoolbox', $task_data);
					if ($response['code'] == 202 && $response['data']['task_id'] != '')
					{
						sql_delete("delete from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=5", $video_id);
						sql_insert("insert into $config[tables_prefix]videos_advanced_operations set video_id=?, operation_type_id=5, operation_status_id=1, operation_task_id=?, added_date=?", $video_id, $response['data']['task_id'], date('Y-m-d H:i:s'));
						neuroscoreLog("INFO  Model auto-selection: task submitted => checking progress on background (task #{$response['data']['task_id']})");
					} elseif ($response['code'] == 402 && $data['on_empty_balance'] == 0)
					{
						sql_delete("delete from $config[tables_prefix]videos_advanced_operations where video_id=? and operation_type_id=5", $video_id);
						sql_insert("insert into $config[tables_prefix]videos_advanced_operations set video_id=?, operation_type_id=5, operation_status_id=0, added_date=?", $video_id, date('Y-m-d H:i:s'));
						neuroscoreLog('INFO  Model auto-selection: task rejected => postponed for the future');
					} else
					{
						neuroscoreLog('ERROR Model auto-selection: task rejected => ignored');
					}
				}
			}
		}
	}
}

if ($_SERVER['argv'][1] == 'cron' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';

	KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
	if (!KvsUtilities::try_exclusive_lock('admin/data/plugins/neuroscore/cron'))
	{
		die('Already locked');
	}

	neuroscoreCron();
	die;
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
