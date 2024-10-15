<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function rotator_resetInit()
{
	global $config;

	mkdir_recursive("$config[project_path]/admin/data/plugins/rotator_reset");
}

function rotator_resetShow()
{
	global $config, $errors, $page_name, $lang;

	rotator_resetInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/rotator_reset";

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
					$json_response['url'] = "plugins.php?plugin_id=rotator_reset&full_check=true";
					$json_response['redirect'] = true;
					@unlink("$plugin_path/task-progress-$task_id.dat");
				}
			}
			if (isset($json['message']))
			{
				$json_response['message'] = $json['message'];
			} elseif (isset($json['message_id']))
			{
				$json_response['message'] = $lang['plugins']['rotator_reset'][$json['message_id']];
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
	} elseif ($_POST['action'] == 'reset')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		$rnd = mt_rand(10000000, 99999999);

		$data = [];
		$data['reset_videos'] = intval($_POST['reset_videos']);
		$data['reset_screenshots'] = intval($_POST['reset_screenshots']);

		file_put_contents("$plugin_path/task-$rnd.dat", serialize($data), LOCK_EX);
		if (!is_file("$plugin_path/task-$rnd.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$plugin_path/task-$rnd.dat");
		}

		if (!is_array($errors))
		{
			exec("$config[php_path] $config[project_path]/admin/plugins/rotator_reset/rotator_reset.php $rnd > /dev/null 2>&1 &");
			return_ajax_success("$page_name?plugin_id=rotator_reset&action=progress&task_id=$rnd", 2);
		} else
		{
			return_ajax_errors($errors);
		}
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $plugin_path);
	}
}

$task_id = intval($_SERVER['argv'][1]);

if ($task_id > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once('include/functions_screenshots.php');
	require_once('include/functions.php');

	$plugin_path = "$config[project_path]/admin/data/plugins/rotator_reset";

	$data = @unserialize(file_get_contents("$plugin_path/task-$task_id.dat"));

	$total_videos = 0;
	$total_amount_of_work = 0;
	$done_amount_of_work = 0;
	$last_pc = 0;

	if ($data['reset_screenshots'] == 1)
	{
		$total_videos = mr2number(sql("select count(*) from $config[tables_prefix]videos where status_id in (0,1)"));
		$total_amount_of_work += $total_videos;
	}
	if ($data['reset_videos'] == 1)
	{
		if ($total_videos > 0)
		{
			$total_amount_of_work += floor($total_videos * 0.1);
		} else
		{
			$total_amount_of_work += 1;
		}
	}

	if ($data['reset_screenshots'] == 1)
	{
		$video_ids = mr2array_list(sql("select video_id from $config[tables_prefix]videos where status_id in (0,1) order by video_id asc"));

		foreach ($video_ids as $video_id)
		{
			$dir_path = get_dir_by_id($video_id);
			@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat");

			$done_amount_of_work += 1;
			$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			if ($pc > $last_pc)
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $pc, 'message_id' => 'message_reset_screenshots', 'message_params' => [$video_id]]), LOCK_EX);
				$last_pc = $pc;
			}

			log_video("", $video_id);
			log_video("INFO  Screenshots rotator stats are reset from plugin", $video_id);
			usleep(2000);
		}

		sql("update $config[tables_prefix]videos set rs_dlist=0, rs_ccount=0, rs_completed=0");
	}

	if ($data['reset_videos'] == 1)
	{
		file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => $pc, 'message_id' => 'message_reset_videos']), LOCK_EX);
		sql("update $config[tables_prefix]videos set r_dlist=0, r_ccount=0, r_cweight=0, r_ctr=0");
		sql("update $config[tables_prefix]categories_videos set cr_dlist=0, cr_ccount=0, cr_cweight=0, cr_ctr=0");
		sql("update $config[tables_prefix]tags_videos set cr_dlist=0, cr_ccount=0, cr_cweight=0, cr_ctr=0");
	}

	@unlink("$plugin_path/task-$task_id.dat");
	file_put_contents("$plugin_path/task-progress-$task_id.dat", json_encode(['percent' => 100]), LOCK_EX);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
