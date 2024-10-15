<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions_admin.php');
require_once('include/functions.php');
require_once('include/check_access.php');

$table_name="$config[tables_prefix]videos";

$errors = null;

$mass_edit_id=intval($_REQUEST['edit_id']);

if ($_REQUEST['action'] == 'progress')
{
	header('Content-Type: application/json; charset=utf-8');

	$json_response = ['status' => 'success'];
	$json = @json_decode(file_get_contents("$config[temporary_path]/mass-edit-progress-$mass_edit_id.dat"), true);
	if (is_array($json))
	{
		if (isset($json['percent']))
		{
			$json_response['percent'] = intval($json['percent']);
			if (intval($json['percent']) == 100)
			{
				$_SESSION['messages'][] = $lang['videos']['success_message_objects_updated'];
				$json_response['url'] = "videos.php";
				$json_response['redirect'] = true;
				@unlink("$config[temporary_path]/mass-edit-progress-$mass_edit_id.dat");
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

if ($mass_edit_id < 1 || !is_file("$config[temporary_path]/mass-edit-$mass_edit_id.dat")) {header("Location: videos.php");die;}
$data=@unserialize(file_get_contents("$config[temporary_path]/mass-edit-$mass_edit_id.dat"));
if (!is_array($data)) {header("Location: videos.php");die;}

$ids_str=implode(",",$data['ids']);
if ($ids_str=='')
{
	$ids_str='0';
}

if ($_POST['action']=='change_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if ($_POST['tokens_required'] != '' && $_POST['tokens_required'] != '0')
	{
		validate_field('empty_int', $_POST['tokens_required'], $lang['videos']['mass_edit_videos_field_tokens_cost']);
	}
	if ($_POST['release_year'] != '')
	{
		validate_field('empty_int', $_POST['release_year'], $lang['videos']['mass_edit_videos_field_release_year']);
	}
	if ($_POST['content_source'] != '')
	{
		if (!in_array('content_sources|add', $_SESSION['permissions']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where title=?", $_POST['content_source'])) == 0)
		{
			$errors[] = get_aa_error('invalid_content_source', $lang['videos']['mass_edit_videos_field_content_source']);
		}
	}
	if ($_POST['dvd'] != '')
	{
		if (!in_array('dvds|add', $_SESSION['permissions']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where title=?", $_POST['dvd'])) == 0)
		{
			$errors[] = get_aa_error('invalid_dvd', $lang['videos']['mass_edit_videos_field_dvd']);
		}
	}

	if (intval($_POST['post_date_option']) == 1)
	{
		if ($_POST['relative_post_date_from'] != '' || $_POST['relative_post_date_to'] != '')
		{
			validate_field('int_range', $_POST, $lang['videos']['mass_edit_videos_field_post_date'], ['is_fully_required' => 1, 'same_allowed' => 1, 'range_start' => 'relative_post_date_from', 'range_end' => 'relative_post_date_to']);
		}
	} else
	{
		if ($_POST['post_date_from'] != '' || $_POST['post_date_to'] != '')
		{
			validate_field('calendar_range', $_POST, $lang['videos']['mass_edit_videos_field_post_date'], ['is_fully_required' => 1, 'same_allowed' => 1, 'range_start' => 'post_date_from', 'range_end' => 'post_date_to']);
		}
	}

	if (intval($_POST['post_time_change']) == 1)
	{
		validate_field('time_range', $_POST, $lang['videos']['mass_edit_videos_field_post_time'], ['is_required' => 1, 'range_start' => 'post_time_from', 'range_end' => 'post_time_to', 'same_allowed' => 1]);
	}

	if ($_POST['rating_min'] != '' || $_POST['rating_max'] != '')
	{
		$has_rating_error = 0;
		$rating_min = floatval($_POST['rating_min']);
		$rating_max = floatval($_POST['rating_max']);
		$votes_min = intval($_POST['rating_amount_min']);
		$votes_max = intval($_POST['rating_amount_max']);
		if ($_POST['rating_min'] != '' && $_POST['rating_min'] != '0' && $has_rating_error == 0)
		{
			if (!validate_field('empty_float', $_POST['rating_min'], $lang['videos']['mass_edit_videos_field_rating']))
			{
				$has_rating_error = 1;
			} else
			{
				if ($rating_min < 0 || $rating_min > 10)
				{
					$errors[] = get_aa_error('invalid_rating', $lang['videos']['mass_edit_videos_field_rating']);
					$has_rating_error = 1;
				}
			}
		}
		if ($_POST['rating_max'] != '' && $_POST['rating_max'] != '0' && $has_rating_error == 0)
		{
			if (!validate_field('empty_float', $_POST['rating_max'], $lang['videos']['mass_edit_videos_field_rating']))
			{
				$has_rating_error = 1;
			} else
			{
				if ($rating_max < 0 || $rating_max > 10)
				{
					$errors[] = get_aa_error('invalid_rating', $lang['videos']['mass_edit_videos_field_rating']);
					$has_rating_error = 1;
				}
			}
		}
		if ($has_rating_error == 0)
		{
			if ($rating_max < $rating_min)
			{
				$errors[] = get_aa_error('invalid_int_range', $lang['videos']['mass_edit_videos_field_rating']);
				$has_rating_error = 1;
			}
		}
		if ($has_rating_error == 0)
		{
			if (!validate_field('empty_int', $_POST['rating_amount_min'], $lang['videos']['mass_edit_videos_field_rating']))
			{
				$has_rating_error = 1;
			}
		}
		if ($has_rating_error == 0)
		{
			if (!validate_field('empty_int', $_POST['rating_amount_max'], $lang['videos']['mass_edit_videos_field_rating']))
			{
				$has_rating_error = 1;
			}
		}
		if ($has_rating_error == 0)
		{
			if ($votes_max < $votes_min)
			{
				$errors[] = get_aa_error('invalid_int_range', $lang['videos']['mass_edit_videos_field_rating']);
				$has_rating_error = 1;
			}
		}
	}

	validate_field('int_range', $_POST, $lang['videos']['mass_edit_videos_field_visits'], ['same_allowed' => 1, 'range_start' => 'visits_min', 'range_end' => 'visits_max']);

	if ($_POST['video_format_upload_id'] != '')
	{
		if (validate_field('file', 'video_format_upload_file', $lang['videos']['mass_edit_videos_field_format_video_upload'], array('is_required' => 1)))
		{
			if (get_video_duration("$config[temporary_path]/$_POST[video_format_upload_file_hash].tmp") < 1)
			{
				$errors[] = get_aa_error('invalid_video_file', $lang['videos']['mass_edit_videos_field_format_video_upload']);
			}
		}
	}
	if (intval($_POST['truncate_duration_enabled']) == 1)
	{
		validate_field('empty_int', $_POST['truncate_duration_value'], $lang['videos']['mass_edit_videos_field_duration_truncate']);
	}
	if (intval($_POST['truncate_offset_start_enabled']) == 1)
	{
		validate_field('empty_int', $_POST['truncate_duration_offset_start'], $lang['videos']['mass_edit_videos_field_duration_truncate']);
	}
	if (intval($_POST['truncate_offset_end_enabled']) == 1)
	{
		validate_field('empty_int', $_POST['truncate_duration_offset_end'], $lang['videos']['mass_edit_videos_field_duration_truncate']);
	}
	if ($_POST['new_storage_group_id'] != '')
	{
		$background_tasks = mr2array(sql("select video_id from $config[tables_prefix]background_tasks where type_id=15"));
		foreach ($background_tasks as $task)
		{
			$video_id = intval($task['video_id']);
			if (in_array($video_id, $data['ids']))
			{
				$errors[] = get_aa_error('videos_mass_edit_migration');
				break;
			}
		}
	}

	if (!is_array($errors))
	{
		$needs_editing = 0;

		if (in_array('videos|edit_admin_user', $_SESSION['permissions']))
		{
			if (array_cnt($_POST['admin_user_ids']) > 0)
			{
				$data['admin_user_ids'] = $_POST['admin_user_ids'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_dir',$_SESSION['permissions']))
		{
			if (intval($_POST['regenerate_directories']) == 1)
			{
				$lang_codes = mr2array_list(sql("select code from $config[tables_prefix]languages"));
				if ($_POST['regenerate_directories_language'] == '' || in_array($_POST['regenerate_directories_language'], $lang_codes))
				{
					$data['regenerate_directories'] = 1;
					$data['regenerate_directories_language'] = $_POST['regenerate_directories_language'];
					$needs_editing = 1;
				}
			}
		}

		if (in_array('videos|edit_status',$_SESSION['permissions']))
		{
			if ($_POST['status_id'] != '')
			{
				$data['status_id'] = $_POST['status_id'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_type',$_SESSION['permissions']))
		{
			if ($_POST['is_private'] != '')
			{
				$data['is_private'] = $_POST['is_private'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_access_level',$_SESSION['permissions']))
		{
			if ($_POST['access_level_id'] != '')
			{
				$data['access_level_id'] = $_POST['access_level_id'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_tokens',$_SESSION['permissions']))
		{
			if ($_POST['tokens_required'] != '')
			{
				$data['tokens_required'] = $_POST['tokens_required'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_release_year',$_SESSION['permissions']))
		{
			if ($_POST['release_year'] != '')
			{
				$data['release_year'] = $_POST['release_year'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_user',$_SESSION['permissions']))
		{
			if ($_POST['is_username_randomization'] && in_array('users|add', $_SESSION['permissions']))
			{
				$data['is_username_randomization'] = 1;
				$needs_editing = 1;
			} elseif (array_cnt($_POST['user_ids']) > 0)
			{
				foreach ($_POST['user_ids'] as $key => $user_id)
				{
					if (strpos($user_id, 'new_') === 0 && in_array('users|add', $_SESSION['permissions']))
					{
						$username = substr($user_id, 4);
						$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $username));
						if ($user_id == 0)
						{
							$user_id = sql_insert("insert into $config[tables_prefix]users set username=?, status_id=2, display_name=?, email=?, added_date=?", $username, $username, generate_email($username), date('Y-m-d H:i:s'));
						}
						$_POST['user_ids'][$key] = $user_id;
					}
				}
				$data['user_ids'] = $_POST['user_ids'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_content_source',$_SESSION['permissions']))
		{
			if (intval($_POST['content_source_set_empty']) == 1)
			{
				$data['content_source_id'] = -1;
				$needs_editing = 1;
			} elseif ($_POST['content_source'] != '')
			{
				$content_source_id = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $_POST['content_source']));
				if ($content_source_id == 0 && in_array('content_sources|add', $_SESSION['permissions']))
				{
					$cs_dir = get_correct_dir_name($_POST['content_source']);
					$temp_dir = $cs_dir;
					for ($it = 2; $it < 999999; $it++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where dir=?", $temp_dir)) == 0)
						{
							$cs_dir = $temp_dir;
							break;
						}
						$temp_dir = $cs_dir . $it;
					}
					$content_source_id = sql_insert("insert into $config[tables_prefix]content_sources set title=?, dir=?, rating_amount=1, added_date=?", $_POST['content_source'], $cs_dir, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=3, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $content_source_id, date("Y-m-d H:i:s"));
				}
				if ($content_source_id > 0)
				{
					$data['content_source_id'] = $content_source_id;
					$needs_editing = 1;
				}
			}
		}

		if (in_array('videos|edit_dvd',$_SESSION['permissions']))
		{
			if (intval($_POST['dvd_set_empty']) == 1)
			{
				$data['dvd_id'] = -1;
				$needs_editing = 1;
			} elseif ($_POST['dvd'] != '')
			{
				$dvd_id = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $_POST['dvd']));
				if ($dvd_id == 0 && in_array('dvds|add', $_SESSION['permissions']))
				{
					$dvd_dir = get_correct_dir_name($_POST['dvd']);
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
					$dvd_id = sql_insert("insert into $config[tables_prefix]dvds set title=?, dir=?, rating_amount=1, added_date=?", $_POST['dvd'], $dvd_dir, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=5, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $dvd_id, date("Y-m-d H:i:s"));
				}
				if ($dvd_id > 0)
				{
					$data['dvd_id'] = $dvd_id;
					$needs_editing = 1;
				}
			}
		}

		if (in_array('videos|edit_admin_flag',$_SESSION['permissions']))
		{
			if ($_POST['admin_flag_id'] != '')
			{
				$data['admin_flag_id'] = $_POST['admin_flag_id'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_is_locked',$_SESSION['permissions']))
		{
			if ($_POST['is_locked'] != '')
			{
				$data['is_locked'] = $_POST['is_locked'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_status', $_SESSION['permissions']))
		{
			if ($_POST['is_review_needed'] != '')
			{
				$data['is_review_needed'] = $_POST['is_review_needed'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_post_date', $_SESSION['permissions']))
		{
			if (intval($_POST['post_date_option']) == 1)
			{
				if ($_POST['relative_post_date_from'] != '' && $_POST['relative_post_date_to'] != '')
				{
					$data['relative_post_date_from'] = intval($_POST['relative_post_date_from']);
					$data['relative_post_date_to'] = intval($_POST['relative_post_date_to']);
					$data['change_post_date_relative'] = 1;
					$needs_editing = 1;
				}
			} else
			{
				if ($_POST['post_date_from'] != '' && $_POST['post_date_to'] != '')
				{
					$data['post_date_from'] = date('Y-m-d', strtotime($_POST['post_date_from']));
					$data['post_date_to'] = date('Y-m-d', strtotime($_POST['post_date_to']));
					$data['change_post_date_fixed'] = 1;
					$needs_editing = 1;
				}
			}
			if (intval($_POST['post_time_change']) == 1)
			{
				$data['post_time_change'] = 1;
				$temp = explode(':', $_POST['post_time_from']);
				$data['post_time_from'] = intval($temp[0]) * 3600 + intval($temp[1]) * 60;
				$temp = explode(':', $_POST['post_time_to']);
				$data['post_time_to'] = intval($temp[0]) * 3600 + intval($temp[1]) * 60;
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_all', $_SESSION['permissions']))
		{
			if (isset($rating_min, $rating_max))
			{
				$data['rating_min'] = $rating_min;
				$data['rating_max'] = $rating_max;
				$data['rating_amount_min'] = $votes_min;
				$data['rating_amount_max'] = $votes_max;
				$data['change_rating'] = 1;
				$needs_editing = 1;
			}
			if ($_POST['visits_min'] != '' || $_POST['visits_max'] != '')
			{
				$data['visits_min'] = intval($_POST['visits_min']);
				$data['visits_max'] = intval($_POST['visits_max']);
				if ($data['visits_max'] == 0)
				{
					$data['visits_max'] = $data['visits_min'];
				}
				$data['change_visits'] = 1;
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_categories', $_SESSION['permissions']))
		{
			if (array_cnt($_POST['category_ids_add']) > 0)
			{
				foreach ($_POST['category_ids_add'] as $key => $category_id)
				{
					if (strpos($category_id, 'new_') === 0 && in_array('categories|add', $_SESSION['permissions']))
					{
						$category_title = substr($category_id, 4);
						$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $category_title));
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
							$category_id = sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?", $category_title, $cat_dir, date("Y-m-d H:i:s"));
							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=6, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $category_id, date("Y-m-d H:i:s"));
						}
						$_POST['category_ids_add'][$key] = $category_id;
					}
				}
				$data['category_ids_add'] = $_POST['category_ids_add'];
				$needs_editing = 1;
			}
			if (array_cnt($_POST['category_ids_delete']) > 0)
			{
				$data['category_ids_delete'] = $_POST['category_ids_delete'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_models', $_SESSION['permissions']))
		{
			if (array_cnt($_POST['model_ids_add']) > 0)
			{
				foreach ($_POST['model_ids_add'] as $key => $model_id)
				{
					if (strpos($model_id, 'new_') === 0 && in_array('models|add', $_SESSION['permissions']))
					{
						$model_title = substr($model_id, 4);
						$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $model_title));
						if ($model_id == 0)
						{
							$model_dir = get_correct_dir_name($model_title);
							$temp_dir = $model_dir;
							for ($it = 2; $it < 999999; $it++)
							{
								if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where dir=?", $temp_dir)) == 0)
								{
									$model_dir = $temp_dir;
									break;
								}
								$temp_dir = $model_dir . $it;
							}
							$model_id = sql_insert("insert into $config[tables_prefix]models set title=?, dir=?, rating_amount=1, added_date=?", $model_title, $model_dir, date("Y-m-d H:i:s"));
							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=4, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $model_id, date("Y-m-d H:i:s"));
						}
						$_POST['model_ids_add'][$key] = $model_id;
					}
				}
				$data['model_ids_add'] = $_POST['model_ids_add'];
				$needs_editing = 1;
			}
			if (array_cnt($_POST['model_ids_delete']) > 0)
			{
				$data['model_ids_delete'] = $_POST['model_ids_delete'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_tags', $_SESSION['permissions']))
		{
			if ($_POST['tags_add'] != '')
			{
				$data['tags_add'] = explode(",", $_POST['tags_add']);
				$needs_editing = 1;
			}
			if ($_POST['tags_delete'] != '')
			{
				$data['tags_delete'] = explode(",", $_POST['tags_delete']);
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_flags', $_SESSION['permissions']))
		{
			if (array_cnt($_POST['flag_ids_delete']) > 0)
			{
				$data['flag_ids_delete'] = $_POST['flag_ids_delete'];
				$needs_editing = 1;
			}
		}

		if (in_array('playlists|edit_all', $_SESSION['permissions']))
		{
			if (array_cnt($_POST['playlist_ids_add']) > 0)
			{
				foreach ($_POST['playlist_ids_add'] as $key => $playlist_id)
				{
					if (strpos($playlist_id, 'new_') === 0 && in_array('playlists|add', $_SESSION['permissions']))
					{
						$playlist_title = substr($playlist_id, 4);
						$playlist_id = mr2number(sql_pr("select playlist_id from $config[tables_prefix]playlists where title=?", $playlist_title));
						if ($playlist_id == 0)
						{
							$playlist_dir = get_correct_dir_name($playlist_title);
							$temp_dir = $playlist_dir;
							for ($it = 2; $it < 999999; $it++)
							{
								if (mr2number(sql_pr("select count(*) from $config[tables_prefix]playlists where dir=?", $temp_dir)) == 0)
								{
									$playlist_dir = $temp_dir;
									break;
								}
								$temp_dir = $playlist_dir . $it;
							}
							$playlist_id = sql_insert("insert into $config[tables_prefix]playlists set title=?, dir=?, rating_amount=1, added_date=?, user_id=(select user_id from $config[tables_prefix]users where status_id=4 limit 1)", $playlist_title, $playlist_dir, date("Y-m-d H:i:s"));
							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=13, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $playlist_id, date("Y-m-d H:i:s"));
						}
						$_POST['playlist_ids_add'][$key] = $playlist_id;
					}
				}
				$data['playlist_ids_add'] = $_POST['playlist_ids_add'];
				$needs_editing = 1;
			}
			if (array_cnt($_POST['playlist_ids_delete']) > 0)
			{
				$data['playlist_ids_delete'] = $_POST['playlist_ids_delete'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_video_files', $_SESSION['permissions']))
		{
			if (intval($_POST['delete_source_files']) == 1)
			{
				$data['delete_source_files'] = 1;
				$needs_editing = 1;
			}
			if (array_cnt($_POST['video_format_create_ids']) > 0)
			{
				$data['video_format_create_ids'] = $_POST['video_format_create_ids'];
				$data['video_format_create_disable_wm'] = intval($_POST['video_format_create_disable_wm']);
				$needs_editing = 1;
			}
			if ($_POST['video_format_upload_id'] != '')
			{
				$data['video_format_upload_id'] = $_POST['video_format_upload_id'];
				$data['video_format_upload_file'] = "$config[temporary_path]/$_POST[video_format_upload_file_hash].tmp";
				$needs_editing = 1;
			}
			if (array_cnt($_POST['video_format_delete_ids']) > 0)
			{
				$data['video_format_delete_ids'] = $_POST['video_format_delete_ids'];
				$needs_editing = 1;
			}
			if (intval($_POST['truncate_duration_enabled']) == 1)
			{
				$data['truncate_duration_value'] = intval($_POST['truncate_duration_value']);
				$needs_editing = 1;
			}
			if (intval($_POST['truncate_offset_start_enabled']) == 1)
			{
				$data['truncate_duration_offset_start'] = intval($_POST['truncate_duration_offset_start']);
				$needs_editing = 1;
			}
			if (intval($_POST['truncate_offset_end_enabled']) == 1)
			{
				$data['truncate_duration_offset_end'] = intval($_POST['truncate_duration_offset_end']);
				$needs_editing = 1;
			}
			if ($_POST['video_format_duration_id'] != '')
			{
				$data['video_format_duration_id'] = $_POST['video_format_duration_id'];
				$needs_editing = 1;
			}
			if ($_POST['new_format_video_group_id'] != '')
			{
				$data['new_format_video_group_id'] = $_POST['new_format_video_group_id'];
				$needs_editing = 1;
			}
			if (intval($_POST['invalidate_cdn']) == 1)
			{
				$data['invalidate_cdn'] = 1;
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_storage', $_SESSION['permissions']))
		{
			if ($_POST['new_storage_group_id'] != '')
			{
				$data['new_storage_group_id'] = $_POST['new_storage_group_id'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|manage_screenshots', $_SESSION['permissions']))
		{
			if (intval($_POST['recreate_overview_screenshots']) == 1)
			{
				$data['recreate_overview_screenshots'] = 1;
				$data['recreate_overview_screenshots_main'] = intval($_POST['recreate_overview_screenshots_main']);
				$needs_editing = 1;
			}
			if (intval($_POST['delete_overview_screenshots']) == 1)
			{
				$data['delete_overview_screenshots'] = 1;
				$needs_editing = 1;
			}
			if (array_cnt($_POST['video_format_recreate_timelines_ids']) > 0)
			{
				$data['video_format_recreate_timelines_ids'] = $_POST['video_format_recreate_timelines_ids'];
				$needs_editing = 1;
			}
			if (array_cnt($_POST['screenshot_format_recreate_ids']) > 0)
			{
				$data['screenshot_format_recreate_ids'] = $_POST['screenshot_format_recreate_ids'];
				$needs_editing = 1;
			}
		}

		if (in_array('videos|edit_all', $_SESSION['permissions']))
		{
			if (intval($_POST['rotator_reset_main_stats']) == 1)
			{
				$data['rotator_reset_main_stats'] = 1;
				$needs_editing = 1;
			}
			if (intval($_POST['rotator_reset_screenshots_stats']) == 1)
			{
				$data['rotator_reset_screenshots_stats'] = 1;
				$needs_editing = 1;
			}
			if (array_cnt($_POST['post_process_plugins']) > 0)
			{
				$data['post_process_plugins'] = $_POST['post_process_plugins'];
				$needs_editing = 1;
			}
		}

		if ($needs_editing==1)
		{
			file_put_contents("$config[temporary_path]/mass-edit-$mass_edit_id.dat",serialize($data),LOCK_EX);

			$admin_id=$_SESSION['userdata']['user_id'];
			$is_access_to_own_content=intval($_SESSION['userdata']['is_access_to_own_content']);
			$is_access_to_disabled_content=intval($_SESSION['userdata']['is_access_to_disabled_content']);
			$is_access_to_content_flagged_with='0';
			if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
			{
				$is_access_to_content_flagged_with = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
			}

			$task_id=sql_insert("insert into $config[tables_prefix]background_tasks set status_id=1, type_id=52, added_date=?, start_date=?",date("Y-m-d H:i:s"),date("Y-m-d H:i:s"));

			exec("$config[php_path] $config[project_path]/admin/background_mass_edit.php $mass_edit_id $admin_id $is_access_to_own_content $is_access_to_disabled_content $is_access_to_content_flagged_with $task_id > $config[project_path]/admin/logs/tasks/$task_id.txt 2>&1 &");
			return_ajax_success("$page_name?action=progress&edit_id=$mass_edit_id",2);
		} else {
			@unlink("$config[temporary_path]/mass-edit-$mass_edit_id.dat");
			$_SESSION['messages'][]=$lang['videos']['success_message_objects_updated'];
			return_ajax_success("videos.php");
		}
	} else {
		return_ajax_errors($errors);
	}
}

$list_formats_videos_create=array();
$temp=mr2array(sql("select format_video_id, postfix, title, format_video_group_id from $config[tables_prefix]formats_videos where status_id in (1,2) order by format_video_group_id asc, title asc"));
foreach ($temp as $res)
{
	if (is_file("$config[project_path]/admin/data/other/watermark_video_{$res['format_video_id']}.png") || is_file("$config[project_path]/admin/data/other/watermark2_video_{$res['format_video_id']}.png"))
	{
		$res['has_watermark']=1;
	}
	$list_formats_videos_create[$res['format_video_group_id']][]=$res;
}

$list_formats_videos_delete=array();
$temp=mr2array(sql("select postfix, title, format_video_group_id from $config[tables_prefix]formats_videos where status_id in (0,2) order by format_video_group_id asc, title asc"));
foreach ($temp as $res)
{
	$list_formats_videos_delete[$res['format_video_group_id']][]=$res;
}

$list_formats_videos_duration=array();
$temp=mr2array(sql("select format_video_id, postfix, title, format_video_group_id from $config[tables_prefix]formats_videos where status_id in (1) order by format_video_group_id asc, title asc"));
foreach ($temp as $res)
{
	$list_formats_videos_duration[$res['format_video_group_id']][]=$res;
}

$list_formats_videos_timelines=array();
$temp=mr2array(sql("select postfix, title, format_video_group_id from $config[tables_prefix]formats_videos where status_id in (0,1,2) and is_timeline_enabled=1 order by format_video_group_id asc, title asc"));
foreach ($temp as $res)
{
	$list_formats_videos_timelines[$res['format_video_group_id']][]=$res;
}

$list_server_groups=mr2array(sql("select * from (select group_id, title, (select min(total_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as total_space, (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as free_space from $config[tables_prefix]admin_servers_groups where content_type_id=1) x where free_space>0 order by title asc"));
foreach ($list_server_groups as $k=>$v)
{
	$list_server_groups[$k]['free_space']=sizeToHumanString($v['free_space'],2);
	$list_server_groups[$k]['total_space']=sizeToHumanString($v['total_space'],2);
}

$list_formats_screenshots_overview = [];
$list_formats_screenshots_timeline = [];
$list_formats_screenshots_posters = [];
$temp = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id=1 order by title asc"));
foreach ($temp as $res)
{
	if ($res['group_id'] == 1)
	{
		$list_formats_screenshots_overview[] = $res;
	} elseif ($res['group_id'] == 2)
	{
		$list_formats_screenshots_timeline[] = $res;
	} elseif ($res['group_id'] == 3)
	{
		$list_formats_screenshots_posters[] = $res;
	}
}

$plugins_list=get_contents_from_dir("$config[project_path]/admin/plugins",2);
sort($plugins_list);
$list_post_process_plugins=array();
foreach ($plugins_list as $k=>$v)
{
	if (!is_file("$config[project_path]/admin/plugins/$v/$v.php") || !is_file("$config[project_path]/admin/plugins/$v/$v.tpl") || !is_file("$config[project_path]/admin/plugins/$v/$v.dat") || !in_array('videos|edit_all', $_SESSION['permissions']))
	{
		continue;
	}
	$file_data=file_get_contents("$config[project_path]/admin/plugins/$v/$v.dat");
	preg_match("|<plugin_types>(.*?)</plugin_types>|is",$file_data,$temp_find);
	$plugin_types=explode(',',trim($temp_find[1]));
	$is_process_plugin=0;
	foreach ($plugin_types as $type)
	{
		if ($type=='process_object')
		{
			$is_process_plugin=1;
		}
	}

	if ($is_process_plugin==1)
	{
		require_once("$config[project_path]/admin/plugins/$v/$v.php");
		$process_plugin_function="{$v}IsEnabled";
		if (function_exists($process_plugin_function))
		{
			if ($process_plugin_function())
			{
				if (is_file("$config[project_path]/admin/plugins/$v/langs/english.php"))
				{
					require_once("$config[project_path]/admin/plugins/$v/langs/english.php");
				}
				if (($_SESSION['userdata']['lang']!='english') && (is_file("$config[project_path]/admin/plugins/$v/langs/".$_SESSION['userdata']['lang'].".php")))
				{
					require_once("$config[project_path]/admin/plugins/$v/langs/".$_SESSION['userdata']['lang'].".php");
				}
				$list_post_process_plugins[]=array('plugin_id'=>$v,'title'=>$lang['plugins'][$v]['title']);
			}
		}
	}
}

$temp = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos_groups"));
foreach ($temp as $format_group)
{
	$list_formats_videos_groups[$format_group['format_video_group_id']] = $format_group['title'];
}

$smarty=new mysmarty();
$smarty->assign('list_formats_videos_groups',$list_formats_videos_groups);
$smarty->assign('list_formats_videos_create',$list_formats_videos_create);
$smarty->assign('list_formats_videos_delete',$list_formats_videos_delete);
$smarty->assign('list_formats_videos_duration',$list_formats_videos_duration);
$smarty->assign('list_formats_videos_timelines',$list_formats_videos_timelines);
$smarty->assign('list_formats_screenshots_overview',$list_formats_screenshots_overview);
$smarty->assign('list_formats_screenshots_timeline',$list_formats_screenshots_timeline);
$smarty->assign('list_formats_screenshots_posters',$list_formats_screenshots_posters);
$smarty->assign('list_flags_videos',mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=0 order by title asc")));
$smarty->assign('list_flags_admins',mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 order by title asc")));
$smarty->assign('list_server_groups',$list_server_groups);
$smarty->assign('list_post_process_plugins',$list_post_process_plugins);
$smarty->assign('list_languages',mr2array(sql("select * from $config[tables_prefix]languages")));

$website_ui_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
if (strpos($website_ui_data['WEBSITE_LINK_PATTERN'],'%ID%')===false)
{
	$smarty->assign('disallow_directory_change',1);
}

unset($where);
if ($_SESSION['userdata']['is_access_to_own_content']==1)
{
	$admin_id=intval($_SESSION['userdata']['user_id']);
	$where.=" and admin_user_id=$admin_id ";
}
if ($_SESSION['userdata']['is_access_to_disabled_content']==1)
{
	$where.=" and status_id=0 ";
}
if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
{
	$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
	$where .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
}

$videos_count=mr2number(sql("select count(*) from $table_name where status_id in (0,1) and (video_id in ($ids_str)) $where"));
$all_videos_count=mr2number(sql("select count(*) from $table_name where status_id in (0,1) $where"));
$smarty->assign('videos_count',$videos_count);
if ($videos_count==$all_videos_count)
{
	$smarty->assign('videos_count_all',1);
}

$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

$smarty->assign('page_title',$lang['videos']['mass_edit_videos_header']);

$content_scheduler_days=intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days='';
	$sorting_content_scheduler_days='desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option'])==1)
	{
		$now_date = date("Y-m-d 00:00:00");
		$where_content_scheduler_days=" and post_date>'$now_date'";
		$sorting_content_scheduler_days='asc';
	}
	$smarty->assign('list_updates',mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]videos where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
