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
require_once 'include/database_selectors.php';

$errors = null;

// =====================================================================================================================
// select action
// =====================================================================================================================

if ($_POST['action'] == 'select_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	validate_field('empty', $_POST['selector'], $lang['videos']['select_field_list_items']);
	validate_field('empty', $_POST['operation'], $lang['videos']['select_field_operation']);

	$video_ids = array();
	$selector = explode("\n", str_replace(',', "\n", $_POST['selector']));
	$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));

	foreach ($selector as $video_url)
	{
		$video_url = trim($video_url);
		if (is_url($video_url))
		{
			$search_id = 0;
			$search_dir = '';

			$pattern_check = $website_ui_data['WEBSITE_LINK_PATTERN'];
			if ($pattern_check[0] != '/')
			{
				$pattern_check = "/$pattern_check";
			}
			$pattern_regexp = str_replace(array('?', '%DIR%', '%ID%'), array('\?', '(.*)', '([0-9]+)'), $pattern_check);

			unset($temp);
			if (preg_match("|$pattern_regexp|is", parse_url($video_url, PHP_URL_PATH), $temp))
			{
				if (strpos($pattern_check, '%ID%') !== false)
				{
					if (strpos($pattern_check, '%DIR%') === false)
					{
						$search_id = intval($temp[1]);
					} elseif (strpos($pattern_check, '%ID%') > strpos($pattern_check, '%DIR%'))
					{
						$search_id = intval($temp[2]);
					} else
					{
						$search_id = intval($temp[1]);
					}
				} elseif (strpos($pattern_check, '%DIR%') !== false)
				{
					$search_dir = trim($temp[1]);
				}
			} else
			{
				$satellites = mr2array(sql_pr("select * from $config[tables_prefix]admin_satellites"));
				foreach ($satellites as $satellite)
				{
					if (strpos($video_url, str_replace('www.', '', parse_url($satellite['project_url'], PHP_URL_HOST))) !== false && $satellite['website_ui_data'])
					{
						$satellite['website_ui_data'] = @unserialize($satellite['website_ui_data']) ?: [];
						$pattern_check = $satellite['website_ui_data']['WEBSITE_LINK_PATTERN'];
						if ($pattern_check[0] != '/')
						{
							$pattern_check = "/$pattern_check";
						}
						$pattern_regexp = str_replace(array('?', '%DIR%', '%ID%'), array('\?', '(.*)', '([0-9]+)'), $pattern_check);

						unset($temp);
						if (preg_match("|$pattern_regexp|is", parse_url($video_url, PHP_URL_PATH), $temp))
						{
							if (strpos($pattern_check, '%ID%') !== false)
							{
								if (strpos($pattern_check, '%DIR%') === false)
								{
									$search_id = intval($temp[1]);
								} elseif (strpos($pattern_check, '%ID%') > strpos($pattern_check, '%DIR%'))
								{
									$search_id = intval($temp[2]);
								} else
								{
									$search_id = intval($temp[1]);
								}
							} elseif (strpos($pattern_check, '%DIR%') !== false)
							{
								$search_dir = trim($temp[1]);
							}
							break;
						}
					}
				}
			}
			if ($search_id == 0 && $search_dir !== '')
			{
				$search_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where dir=?", $search_dir));
			}

			if ($search_id > 0)
			{
				$video_ids[] = $search_id;
			} else
			{
				$errors[] = get_aa_error('invalid_video_page_url', $video_url);
			}
		} elseif (is_numeric($video_url) && intval($video_url) > 0)
		{
			$video_ids[] = intval($video_url);
		} elseif ($video_url)
		{
			$errors[] = get_aa_error('invalid_video_page_url', $video_url);
		}
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
	if ($where != '' && array_cnt($video_ids) > 0)
	{
		$video_ids_str = implode(', ', $video_ids);
		$video_ids = mr2array_list(sql("select video_id from $config[tables_prefix]videos where video_id in ($video_ids_str) $where"));
	}

	if (array_cnt($video_ids) == 0)
	{
		validate_field('empty', '', $lang['videos']['select_field_list_items']);
	}

	if ($_POST['operation'] == 'delete' || $_POST['operation'] == 'mark_deleted')
	{
		validate_field('empty', $_POST['confirm'], $lang['videos']['select_field_operation_confirm']);
	}

	if (!is_array($errors))
	{
		switch ($_POST['operation'])
		{
			case 'list':
				$rnd = mt_rand(10000000, 99999999);
				file_put_contents("$config[temporary_path]/mass-select-$rnd.dat", serialize(['ids' => $video_ids]));
				return_ajax_success("videos.php?no_filter=true&se_file_ids=$rnd");
				break;
			case 'mass_edit':
				$rnd = mt_rand(10000000, 99999999);
				file_put_contents("$config[temporary_path]/mass-edit-$rnd.dat", serialize(['all' => 0, 'ids' => $video_ids]));
				return_ajax_success("videos_mass_edit.php?edit_id=$rnd", 1);
				break;
			case 'export':
				$rnd = mt_rand(10000000, 99999999);
				file_put_contents("$config[temporary_path]/mass-export-$rnd.dat", serialize(['ids' => $video_ids]));
				return_ajax_success("videos_export.php?se_file_ids=$rnd");
				break;
			case 'mark_deleted':
				$rnd = mt_rand(10000000, 99999999);
				file_put_contents("$config[temporary_path]/delete-videos-$rnd.dat", serialize(['ids' => $video_ids]));
				return_ajax_success("videos.php?action=mark_deleted&delete_id=$rnd", 1);
				break;
			case 'delete':
				foreach ($video_ids as $video_id)
				{
					if (!delete_video($video_id))
					{
						return_ajax_errors([get_aa_error('content_delete_limit_triggered')]);
					}
				}
				$_SESSION['messages'][] = $lang['common']['success_message_removed'];
				return_ajax_success("videos.php", 1);
				break;
		}
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// view
// =====================================================================================================================

$mass_select_id = intval($_REQUEST['select_id']);
if ($mass_select_id > 0)
{
	if (!is_file("$config[temporary_path]/mass-select-$mass_select_id.dat"))
	{
		header("Location: videos.php");
		die;
	}
	$_POST = @unserialize(file_get_contents("$config[temporary_path]/mass-select-$mass_select_id.dat"));
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('options', $options);

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', $lang['videos']['submenu_option_select_videos']);

$content_scheduler_days = intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days = '';
	$sorting_content_scheduler_days = 'desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option']) == 1)
	{
		$now_date = date("Y-m-d 00:00:00");
		$where_content_scheduler_days=" and post_date>'$now_date'";
		$sorting_content_scheduler_days = 'asc';
	}
	$smarty->assign('list_updates', mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]videos where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
