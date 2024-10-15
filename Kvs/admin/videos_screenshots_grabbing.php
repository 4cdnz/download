<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_admin.php';
require_once 'include/functions_base.php';
require_once 'include/functions_screenshots.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/pclzip.lib.php';

$errors = null;

if ($_GET['action'] == 'progress')
{
	header('Content-Type: application/json; charset=utf-8');

	$json_response = ['status' => 'success'];

	$grabbing_id = intval($_GET['grabbing_id']);
	$item_id = intval($_GET['video_id']);
	$json = @json_decode(file_get_contents("$config[temporary_path]/grabbing-$grabbing_id/progress.dat"), true);

	if (is_array($json))
	{
		if ($json['is_error'] == 1)
		{
			die(json_encode(['status' => 'failure', 'header' => $lang['validation']['common_header'], 'errors' => [$lang['validation'][$json['message_id']]]]));
		}

		if (isset($json['percent']))
		{
			$json_response['percent'] = intval($json['percent']);
			if (intval($json['percent']) == 100)
			{
				$json_response['url'] = "videos_screenshots_grabbing.php?action=grabbing_complete&item_id=$item_id&grabbing_id=$grabbing_id";
				$json_response['redirect'] = true;
				rmdir_recursive("$config[temporary_path]/grabbing-$grabbing_id");
			}
		}
		if (isset($json['message']))
		{
			$json_response['message'] = $json['message'];
		} elseif (isset($json['message_id']))
		{
			$json_response['message'] = $lang['videos'][$json['message_id']];
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
}

$item_id = intval($_REQUEST['item_id']);
if ($item_id < 1)
{
	header("Location: videos.php");
	die;
}

$where = '';
if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
{
	$admin_id = intval($_SESSION['userdata']['user_id']);
	$where .= " and admin_user_id=$admin_id ";
}
if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
{
	$where .= " and status_id=0 ";
}
if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
{
	$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
	$where .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
}

$result = sql_pr("select * from $config[tables_prefix]videos where video_id=? $where", $item_id);
if (mr2rows($result) > 0)
{
	$data_video = mr2array_single($result);
} else
{
	exit_to_permission_error();
	die;
}

$options = get_options();

$list_formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2)"));
$video_formats = get_video_formats($item_id, $data_video['file_formats']);
foreach ($video_formats as $k => $format_rec)
{
	foreach ($list_formats_videos as $format_video)
	{
		if ($format_video['postfix'] == $format_rec['postfix'])
		{
			$video_formats[$k]['title'] = $format_video['title'];
			$video_formats[$k]['format_video_id'] = $format_video['format_video_id'];
		}
	}
}

$dir_path = get_dir_by_id($item_id);
if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp"))
{
	$source_file = array();
	$source_file['dimensions'] = explode("x", $data_video['file_dimensions']);
	$source_file['duration_string'] = durationToHumanString($data_video['duration']);
}

if ($_POST['action'] == 'start_grabbing' || isset($_POST['start_grabbing']))
{
	if (floatval($options['LIMIT_CONVERSION_LA']) > 0)
	{
		if (get_LA() > floatval($options['LIMIT_CONVERSION_LA']))
		{
			$errors[] = get_aa_error('video_screenshot_grabbing_la', $options['LIMIT_CONVERSION_LA']);
			return_ajax_errors($errors);
		}
	}
	if ($options['LIMIT_CONVERSION_TIME_FROM'])
	{
		$temp = explode(":", $options['LIMIT_CONVERSION_TIME_FROM']);
		if (array_cnt($temp) == 2)
		{
			$current_date = getdate();
			if (intval($current_date['hours']) * 3600 + intval($current_date['minutes']) * 60 + intval($current_date['seconds']) < intval($temp[0]) * 3600 + intval($temp[1]) * 60)
			{
				$errors[] = get_aa_error('video_screenshot_grabbing_time', $options['LIMIT_CONVERSION_TIME_FROM'], $options['LIMIT_CONVERSION_TIME_TO'] ?: '23:59');
				return_ajax_errors($errors);
			}
		}
	}
	if ($options['LIMIT_CONVERSION_TIME_TO'])
	{
		$temp = explode(":", $options['LIMIT_CONVERSION_TIME_TO']);
		if (array_cnt($temp) == 2)
		{
			$current_date = getdate();
			if (intval($current_date['hours']) * 3600 + intval($current_date['minutes']) * 60 + intval($current_date['seconds']) > intval($temp[0]) * 3600 + intval($temp[1]) * 60)
			{
				$errors[] = get_aa_error('video_screenshot_grabbing_time', $options['LIMIT_CONVERSION_TIME_FROM'] ?: '00:00', $options['LIMIT_CONVERSION_TIME_TO']);
				return_ajax_errors($errors);
			}
		}
	}

	if (validate_field('empty_int', $_POST['method'], $lang['videos']['screenshots_grabbing_field_method']))
	{
		if ($_POST['method'] == 2)
		{
			validate_field('empty_int', $_POST['interval'], $lang['videos']['screenshots_grabbing_field_method']);
		} else
		{
			$selected_format_with_timelines = false;
			foreach ($video_formats as $video_format)
			{
				if ($video_format['format_video_id'] == $_POST['source_file_id'] && $video_format['timeline_screen_amount'] > 0)
				{
					$selected_format_with_timelines = true;
					break;
				}
			}
			if (!$selected_format_with_timelines)
			{
				validate_field('empty', '', $lang['videos']['screenshots_grabbing_field_method']);
			}
		}
	}
	if ($_POST['display_size'])
	{
		validate_field('size', $_POST['display_size'], $lang['videos']['screenshots_grabbing_field_display_size']);
	}

	$rnd = mt_rand(10000000, 99999999);
	if (!mkdir_recursive("$config[temporary_path]/grabbing-$rnd"))
	{
		$errors[] = get_aa_error('filesystem_permission_create', "$config[temporary_path]/grabbing-$rnd");
	}

	if (!is_array($errors))
	{
		if (isset($_POST['source_file_id']))
		{
			$_SESSION['save'][$page_name]['source_file_id'] = intval($_POST['source_file_id']);
		}
		if (isset($_POST['method']))
		{
			$_SESSION['save'][$page_name]['method'] = intval($_POST['method']);
		}
		if (isset($_POST['interval']))
		{
			$_SESSION['save'][$page_name]['interval'] = intval($_POST['interval']);
		}
		if (isset($_POST['display_size']))
		{
			$_SESSION['save'][$page_name]['display_size'] = $_POST['display_size'];
		}
		if (isset($_POST['screenshots_crop_left']))
		{
			$_SESSION['save'][$page_name]['screenshots_crop_left'] = intval($_POST['screenshots_crop_left']);
		}
		if (isset($_POST['screenshots_crop_left_unit']))
		{
			$_SESSION['save'][$page_name]['screenshots_crop_left_unit'] = intval($_POST['screenshots_crop_left_unit']);
		}
		if (isset($_POST['screenshots_crop_top']))
		{
			$_SESSION['save'][$page_name]['screenshots_crop_top'] = intval($_POST['screenshots_crop_top']);
		}
		if (isset($_POST['screenshots_crop_top_unit']))
		{
			$_SESSION['save'][$page_name]['screenshots_crop_top_unit'] = intval($_POST['screenshots_crop_top_unit']);
		}
		if (isset($_POST['screenshots_crop_right']))
		{
			$_SESSION['save'][$page_name]['screenshots_crop_right'] = intval($_POST['screenshots_crop_right']);
		}
		if (isset($_POST['screenshots_crop_right_unit']))
		{
			$_SESSION['save'][$page_name]['screenshots_crop_right_unit'] = intval($_POST['screenshots_crop_right_unit']);
		}
		if (isset($_POST['screenshots_crop_bottom']))
		{
			$_SESSION['save'][$page_name]['screenshots_crop_bottom'] = intval($_POST['screenshots_crop_bottom']);
		}
		if (isset($_POST['screenshots_crop_bottom_unit']))
		{
			$_SESSION['save'][$page_name]['screenshots_crop_bottom_unit'] = intval($_POST['screenshots_crop_bottom_unit']);
		}
		$_SESSION['save'][$page_name]['screenshots_crop_trim'] = intval($_POST['screenshots_crop_trim']);
		if (isset($_POST['screenshots_offset']))
		{
			$_SESSION['save'][$page_name]['screenshots_offset'] = intval($_POST['screenshots_offset']);
		}

		$data = [];
		$data['method'] = intval($_POST['method']);
		$data['source_file_id'] = intval($_POST['source_file_id']);
		$data['interval'] = intval($_POST['interval']);
		$data['display_size'] = $_POST['display_size'];
		$data['video_id'] = $data_video['video_id'];
		$data['screenshots_crop_left'] = intval($_POST['screenshots_crop_left']);
		$data['screenshots_crop_left_unit'] = intval($_POST['screenshots_crop_left_unit']);
		$data['screenshots_crop_top'] = intval($_POST['screenshots_crop_top']);
		$data['screenshots_crop_top_unit'] = intval($_POST['screenshots_crop_top_unit']);
		$data['screenshots_crop_right'] = intval($_POST['screenshots_crop_right']);
		$data['screenshots_crop_right_unit'] = intval($_POST['screenshots_crop_right_unit']);
		$data['screenshots_crop_bottom'] = intval($_POST['screenshots_crop_bottom']);
		$data['screenshots_crop_bottom_unit'] = intval($_POST['screenshots_crop_bottom_unit']);
		$data['screenshots_crop_trim'] = intval($_POST['screenshots_crop_trim']);
		$data['screenshots_offset'] = intval($_POST['screenshots_offset']);
		$data['slow_method'] = intval($_POST['slow_method']);
		file_put_contents("$config[temporary_path]/grabbing-$rnd/task.dat", serialize($data), LOCK_EX);

		$admin_id = intval($_SESSION['userdata']['user_id']);
		exec("$config[php_path] $config[project_path]/admin/background_screenshots_grabbing.php $rnd $admin_id > /dev/null 2>&1 &");
		return_ajax_success("$page_name?action=progress&grabbing_id=$rnd&video_id=$data_video[video_id]", 2);
	} else
	{
		return_ajax_errors($errors);
	}
} elseif ($_POST['action'] == 'save_screenshots')
{
	if (floatval($options['LIMIT_CONVERSION_LA']) > 0)
	{
		if (get_LA() > floatval($options['LIMIT_CONVERSION_LA']))
		{
			$errors[] = get_aa_error('video_screenshot_grabbing_la', $options['LIMIT_CONVERSION_LA']);
			return_ajax_errors($errors);
		}
	}
	if ($options['LIMIT_CONVERSION_TIME_FROM'])
	{
		$temp = explode(":", $options['LIMIT_CONVERSION_TIME_FROM']);
		if (array_cnt($temp) == 2)
		{
			$current_date = getdate();
			if (intval($current_date['hours']) * 3600 + intval($current_date['minutes']) * 60 + intval($current_date['seconds']) < intval($temp[0]) * 3600 + intval($temp[1]) * 60)
			{
				$errors[] = get_aa_error('video_screenshot_grabbing_time', $options['LIMIT_CONVERSION_TIME_FROM'], $options['LIMIT_CONVERSION_TIME_TO'] ?: '23:59');
				return_ajax_errors($errors);
			}
		}
	}
	if ($options['LIMIT_CONVERSION_TIME_TO'])
	{
		$temp = explode(":", $options['LIMIT_CONVERSION_TIME_TO']);
		if (array_cnt($temp) == 2)
		{
			$current_date = getdate();
			if (intval($current_date['hours']) * 3600 + intval($current_date['minutes']) * 60 + intval($current_date['seconds']) > intval($temp[0]) * 3600 + intval($temp[1]) * 60)
			{
				$errors[] = get_aa_error('video_screenshot_grabbing_time', $options['LIMIT_CONVERSION_TIME_FROM'] ?: '00:00', $options['LIMIT_CONVERSION_TIME_TO']);
				return_ajax_errors($errors);
			}
		}
	}

	$data_amount = intval($_POST['data_amount']);
	$grabbing_id = intval($_POST['grabbing_id']);
	$screen_amount = $data_video['screen_amount'];
	$video_id = $data_video['video_id'];

	try
	{
		KvsUtilities::acquire_exclusive_lock('admin/data/engine/rotator/videos/screenshots');
	} catch (KvsException $e)
	{
		$errors[] = get_aa_error('filesystem_failed_to_acquire_lock', 'admin/data/engine/rotator/videos/screenshots');
		return_ajax_errors($errors);
	}

	$rotator_data_changed = 0;
	if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat"))
	{
		$rotator_data = @unserialize(file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat"));
	}

	$screenshots_changed = 0;
	$screenshots_data = @unserialize(file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/info.dat")) ?: [];

	log_video("", $video_id);
	log_video("INFO  Replacing overview screenshots by manual grabbing", $video_id);

	if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots"))
	{
		log_video("ERROR Failed to create directory $config[content_path_videos_sources]/$dir_path/$video_id/screenshots", $video_id);
		log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
		$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
		return_ajax_errors($errors);
	}
	if (!mkdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id"))
	{
		log_video("ERROR Failed to create directory $config[content_path_videos_screenshots]/$dir_path/$video_id", $video_id);
		log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
		$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
		return_ajax_errors($errors);
	}

	$list_formats_overview = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=1"));
	for ($i = 0; $i <= $data_amount; $i++)
	{
		$is = $i + 1;
		if (intval($_POST["save_as_screenshot_$i"]) > 0 || $_POST["save_as_screenshot_$i"] == 'new')
		{
			if (intval($_POST["save_as_screenshot_$i"]) > 0)
			{
				$it = intval($_POST["save_as_screenshot_$i"]);
				log_video("INFO  Replacing screenshot #{$it} with the grabbed one", $video_id);
			} elseif ($_POST["save_as_screenshot_$i"] == 'new')
			{
				$screen_amount++;
				$it = $screen_amount;
				log_video("INFO  Adding new screenshot #$it", $video_id);
			}

			if (!@rename("$config[content_path_videos_screenshots]/temp/$grabbing_id/$is.jpg", "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$it.jpg"))
			{
				log_video("ERROR Failed to replace file $config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$it.jpg", $video_id);
				log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
				$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
				return_ajax_errors($errors);
			}

			foreach ($list_formats_overview as $format)
			{
				log_video("INFO  Creating screenshots for \"$format[title]\" format", $video_id);

				if (!mkdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]"))
				{
					log_video("ERROR Failed to create directory $config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]", $video_id);
					log_video("ERROR Error during screenshots creation for \"$format[title]\" format, stopping further processing", $video_id);
					$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
					return_ajax_errors($errors);
				}

				$exec_res = make_screen_from_source("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$it.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$it.jpg", $format, $options, false);
				if ($exec_res)
				{
					log_video("ERROR IM operation failed: $exec_res", $video_id);
					log_video("ERROR Error during screenshots creation for \"$format[title]\" format, stopping further processing", $video_id);
					$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
					return_ajax_errors($errors);
				}
			}

			if ($it == $data_video['screen_main'])
			{
				$video_formats = get_video_formats($video_id, $data_video['file_formats']);
				if (!@copy("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$it.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg"))
				{
					log_video("ERROR Failed to replace file $config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg", $video_id);
					log_video("ERROR Error during player preview image update, stopping further processing", $video_id);
					$errors[] = get_aa_error('video_screenshot_preview_image');
					return_ajax_errors($errors);
				}
				foreach ($video_formats as $format)
				{
					try
					{
						KvsImagemagick::resize_image(KvsImagemagick::RESIZE_TYPE_MAX_SIZE, "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$format['postfix']}.jpg", $format['dimensions'][0] . 'x' . $format['dimensions'][1], true);
					} catch (KvsException $e)
					{
						log_video("ERROR Failed to replace file $config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$format['postfix']}.jpg", $video_id);
						log_video("ERROR Error during player preview image update, stopping further processing", $video_id);
						$errors[] = get_aa_error('video_screenshot_preview_image');
						return_ajax_errors($errors);
					}
				}
			}
			if (isset($rotator_data[$it]))
			{
				unset($rotator_data[$it]);
				$rotator_data_changed = 1;
			}
			$screenshots_changed = 1;
			$screenshots_data[$it] = ['type' => 'auto', 'filesize' => filesize("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$it.jpg")];
		}
	}
	if (isset($rotator_data) && $rotator_data_changed == 1)
	{
		@file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat", serialize($rotator_data));
	}
	KvsUtilities::release_lock('admin/data/engine/rotator/videos/screenshots');

	if ($screenshots_changed == 1)
	{
		foreach ($list_formats_overview as $format)
		{
			if ($format['is_create_zip'] == 1)
			{
				log_video("INFO  Replacing screenshots ZIP for \"$format[title]\" format", $video_id);
				$source_folder = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]";
				@unlink("$source_folder/$video_id-$format[size].zip");

				$zip_files_to_add = array();
				for ($i = 1; $i <= $screen_amount; $i++)
				{
					$zip_files_to_add[] = "$source_folder/$i.jpg";
				}
				$zip = new PclZip("$source_folder/$video_id-$format[size].zip");
				$zip->create($zip_files_to_add, $p_add_dir = "", $p_remove_dir = "$source_folder");
			}
		}
		if (!@file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/info.dat", serialize($screenshots_data)))
		{
			log_video("ERROR Failed to replace file $config[content_path_videos_sources]/$dir_path/$video_id/screenshots/info.dat", $video_id);
			log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
			$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
			return_ajax_errors($errors);
		}
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=151, object_id=?, object_type_id=1, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $data_video['video_id'], date("Y-m-d H:i:s"));
	}
	if ($screen_amount != $data_video['screen_amount'])
	{
		$data_video['screen_amount'] = $screen_amount;
		sql_pr("update $config[tables_prefix]videos set screen_amount=? where video_id=?", $screen_amount, $video_id);
	}
	log_video("INFO  Done screenshots changes", $video_id);
	return_ajax_success("videos_screenshots.php?item_id=$data_video[video_id]&group_id=1", 1);
}

$data = [];
if ($_GET['action'] == 'grabbing_complete' && intval($_GET['item_id']) > 0 && intval($_GET['grabbing_id']) > 0)
{
	$item_id = intval($_GET['item_id']);
	$grabbing_id = intval($_GET['grabbing_id']);

	$image_size = [];
	for ($i = 1; $i < 99999; $i++)
	{
		if (is_file("$config[content_path_videos_screenshots]/temp/$grabbing_id/{$i}r.jpg"))
		{
			if (isset($config['content_url_videos_screenshots_admin_panel']))
			{
				$data[] = "$config[content_url_videos_screenshots_admin_panel]/temp/$grabbing_id/{$i}r.jpg";
			} else
			{
				$data[] = "$config[content_url_videos_screenshots]/temp/$grabbing_id/{$i}r.jpg";
			}
			if ($image_size[0] < 1)
			{
				$image_size = getimagesize("$config[content_path_videos_screenshots]/temp/$grabbing_id/{$i}r.jpg");
			}
		} elseif (is_file("$config[content_path_videos_screenshots]/temp/$grabbing_id/$i.jpg"))
		{
			if (isset($config['content_url_videos_screenshots_admin_panel']))
			{
				$data[] = "$config[content_url_videos_screenshots_admin_panel]/temp/$grabbing_id/$i.jpg";
			} else
			{
				$data[] = "$config[content_url_videos_screenshots]/temp/$grabbing_id/$i.jpg";
			}
			if ($image_size[0] < 1)
			{
				$image_size = getimagesize("$config[content_path_videos_screenshots]/temp/$grabbing_id/$i.jpg");
			}
		} else
		{
			break;
		}
	}
}

$smarty = new mysmarty();
$smarty->assign('data_video', $data_video);
$smarty->assign('formats', $video_formats);
$smarty->assign('source_file', $source_file);
$smarty->assign('data_amount', array_cnt($data));

$smarty->assign('rnd', $rnd);
$smarty->assign('supports_popups', 1);

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', str_replace("%1%", ($data_video['title'] ?: $data_video['video_id']), $lang['videos']['screenshots_header_grabbing']));

$smarty->display("layout.tpl");
