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

// =====================================================================================================================
// initialization
// =====================================================================================================================

$options = get_options();
for ($i = 1; $i <= 10; $i++)
{
	if ($options["CS_FIELD_{$i}_NAME"] == '')
	{
		$options["CS_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
	if ($options["CS_FILE_FIELD_{$i}_NAME"] == '')
	{
		$options["CS_FILE_FIELD_{$i}_NAME"] = $lang['settings']["custom_file_field_{$i}"];
	}
}

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc", $lang['system']['language_code']));
foreach ($list_countries as $k => $country)
{
	$list_countries[$country['country_code']] = $country['title'];
	unset($list_countries[$k]);
}

$list_status_values = array(
	0 => $lang['settings']['format_video_field_status_disabled'],
	1 => $lang['settings']['format_video_field_status_active_required_short'],
	2 => $lang['settings']['format_video_field_status_active_optional_short'],
	3 => $lang['settings']['format_video_field_status_deleting'],
	4 => $lang['settings']['format_video_field_status_error'],
	9 => $lang['settings']['format_video_field_status_active_optional_conditional_short'],
);

$list_watermark_positions = array(
	0 => $lang['settings']['format_video_field_watermark_position_random'],
	1 => $lang['settings']['format_video_field_watermark_position_top_left'],
	2 => $lang['settings']['format_video_field_watermark_position_top_right'],
	3 => $lang['settings']['format_video_field_watermark_position_bottom_right'],
	4 => $lang['settings']['format_video_field_watermark_position_bottom_left'],
	5 => $lang['settings']['format_video_field_watermark_position_scrolling_top'],
	6 => $lang['settings']['format_video_field_watermark_position_scrolling_bottom'],
	7 => $lang['settings']['format_video_field_watermark_position_scrolling_top_bottom'],
	8 => $lang['settings']['format_video_field_watermark_position_dynamic'],
);

$list_watermark2_positions = array(
		0 => $lang['settings']['format_video_field_watermark2_position_random'],
		1 => $lang['settings']['format_video_field_watermark2_position_top_left'],
		2 => $lang['settings']['format_video_field_watermark2_position_top_right'],
		3 => $lang['settings']['format_video_field_watermark2_position_bottom_right'],
		4 => $lang['settings']['format_video_field_watermark2_position_bottom_left'],
		5 => $lang['settings']['format_video_field_watermark2_position_scrolling_top'],
		6 => $lang['settings']['format_video_field_watermark2_position_scrolling_bottom'],
		7 => $lang['settings']['format_video_field_watermark2_position_scrolling_top_bottom'],
		8 => $lang['settings']['format_video_field_watermark2_position_dynamic'],
);

$list_access_level_values = array(
	0 => $lang['settings']['format_video_field_access_level_any_short'],
	1 => $lang['settings']['format_video_field_access_level_member_short'],
	2 => $lang['settings']['format_video_field_access_level_premium_short'],
);

$table_fields = array();
$table_fields[] = array('id' => 'format_video_id',               'title' => $lang['settings']['format_video_field_id'],                    'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',                         'title' => $lang['settings']['format_video_field_title'],                 'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'postfix',                       'title' => $lang['settings']['format_video_field_postfix'],               'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'status_id',                     'title' => $lang['settings']['format_video_field_status'],                'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'append' => array(3 => 'pc_complete'), 'is_nowrap' => 1, 'ifhighlight' => 'is_error', 'value_postfix' => 'source_text');
$table_fields[] = array('id' => 'size',                          'title' => $lang['settings']['format_video_field_size'],                  'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'limit_total_duration',          'title' => $lang['settings']['format_video_field_duration'],              'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'limit_offset_start',            'title' => $lang['settings']['format_video_field_offset_start'],          'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'limit_offset_end',              'title' => $lang['settings']['format_video_field_offset_end'],            'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'watermark_image',               'title' => $lang['settings']['format_video_field_watermark_image'],       'is_default' => 0, 'type' => 'image');
$table_fields[] = array('id' => 'watermark_position_id',         'title' => $lang['settings']['format_video_field_watermark_position'],    'is_default' => 0, 'type' => 'choice', 'values' => $list_watermark_positions, 'append' => array(0 => 'watermark_position_offset', 1 => 'watermark_position_offset', 2 => 'watermark_position_offset', 3 => 'watermark_position_offset', 4 => 'watermark_position_offset', 8 => 'watermark_position_offset', 5 => 'watermark_position_scrolling', 6 => 'watermark_position_scrolling', 7 => 'watermark_position_scrolling'));
$table_fields[] = array('id' => 'watermark_max_width',           'title' => $lang['settings']['format_video_field_watermark_max_width'],   'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'watermark2_image',              'title' => $lang['settings']['format_video_field_watermark2_image'],      'is_default' => 0, 'type' => 'image');
$table_fields[] = array('id' => 'watermark2_position_id',        'title' => $lang['settings']['format_video_field_watermark2_position'],   'is_default' => 0, 'type' => 'choice', 'values' => $list_watermark2_positions, 'append' => array(0 => 'watermark2_position_offset', 1 => 'watermark2_position_offset', 2 => 'watermark2_position_offset', 3 => 'watermark2_position_offset', 4 => 'watermark2_position_offset', 8 => 'watermark2_position_offset', 5 => 'watermark2_position_scrolling', 6 => 'watermark2_position_scrolling', 7 => 'watermark2_position_scrolling'));
$table_fields[] = array('id' => 'watermark2_max_height',         'title' => $lang['settings']['format_video_field_watermark2_max_height'], 'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'access_level_id',               'title' => $lang['settings']['format_video_field_access_level'],          'is_default' => 1, 'type' => 'choice', 'values' => $list_access_level_values);
$table_fields[] = array('id' => 'is_download_enabled',           'title' => $lang['settings']['format_video_field_enable_download'],       'is_default' => 1, 'type' => 'bool');
$table_fields[] = array('id' => 'download_order',                'title' => $lang['settings']['format_video_field_enable_download_order'], 'is_default' => 0, 'type' => 'int');
$table_fields[] = array('id' => 'is_hotlink_protection_enabled', 'title' => $lang['settings']['format_video_field_hotlink_protection'],    'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'preroll_video',                 'title' => $lang['settings']['format_video_field_preroll_video'],         'is_default' => 0, 'type' => 'video');
$table_fields[] = array('id' => 'postroll_video',                'title' => $lang['settings']['format_video_field_postroll_video'],        'is_default' => 0, 'type' => 'video');
$table_fields[] = array('id' => 'limit_speed_value',             'title' => $lang['settings']['format_video_field_limit_speed'],           'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'is_timeline_enabled',           'title' => $lang['settings']['format_video_field_timeline_screenshots'],  'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'videos_count',                  'title' => $lang['settings']['format_video_field_videos_count'],          'is_default' => 1, 'type' => 'number', 'link' => 'videos.php?no_filter=true&se_show_id=wf/%id%', 'link_id' => 'postfix', 'permission' => 'videos|view', 'ifdisable_zero' => 1);

$sort_def_field = "format_video_id";
$sort_def_direction = "desc";
$sort_array = array();
$sidebar_fields = array();
foreach ($table_fields as $k => $field)
{
	if ($field['type'] != 'list' && $field['type'] != 'rename' && $field['type'] != 'thumb')
	{
		$sort_array[] = $field['id'];
		$table_fields[$k]['is_sortable'] = 1;
	}
	if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
	{
		if (in_array($field['id'], $_GET['grid_columns']))
		{
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name]['grid_columns'][$field['id']]);
	} else
	{
		$table_fields[$k]['is_enabled'] = intval($field['is_default']);
	}
	if ($field['type'] == 'id')
	{
		$table_fields[$k]['is_enabled'] = 1;
	}
}
if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
{
	$_SESSION['save'][$page_name]['grid_columns_order'] = $_GET['grid_columns'];
}
if (is_array($_SESSION['save'][$page_name]['grid_columns_order']))
{
	$temp_table_fields = array();
	foreach ($table_fields as $table_field)
	{
		if ($table_field['type'] == 'id')
		{
			$temp_table_fields[] = $table_field;
			break;
		}
	}
	foreach ($_SESSION['save'][$page_name]['grid_columns_order'] as $table_field_id)
	{
		foreach ($table_fields as $table_field)
		{
			if ($table_field['id'] == $table_field_id)
			{
				$temp_table_fields[] = $table_field;
				break;
			}
		}
	}
	foreach ($table_fields as $table_field)
	{
		if (!in_array($table_field['id'], $_SESSION['save'][$page_name]['grid_columns_order']) && $table_field['type'] != 'id')
		{
			$temp_table_fields[] = $table_field;
		}
	}
	$table_fields = $temp_table_fields;
}

$table_name = "$config[tables_prefix]formats_videos";
$table_key_name = "format_video_id";

$table_selector = "*, case when is_hotlink_protection_disabled=1 then 0 else 1 end as is_hotlink_protection_enabled";

$errors = null;

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (in_array($_GET['sort_by'], $sort_array))
{
	$_SESSION['save'][$page_name]['sort_by'] = $_GET['sort_by'];
}
if ($_SESSION['save'][$page_name]['sort_by'] == '')
{
	$_SESSION['save'][$page_name]['sort_by'] = $sort_def_field;
	$_SESSION['save'][$page_name]['sort_direction'] = $sort_def_direction;
} else
{
	if (in_array($_GET['sort_direction'], array('desc', 'asc')))
	{
		$_SESSION['save'][$page_name]['sort_direction'] = $_GET['sort_direction'];
	}
	if ($_SESSION['save'][$page_name]['sort_direction'] == '')
	{
		$_SESSION['save'][$page_name]['sort_direction'] = 'desc';
	}
}

if (isset($_GET['num_on_page']))
{
	$_SESSION['save'][$page_name]['num_on_page'] = intval($_GET['num_on_page']);
}
if ($_SESSION['save'][$page_name]['num_on_page'] < 1)
{
	$_SESSION['save'][$page_name]['num_on_page'] = 20;
}

if (isset($_GET['from']))
{
	$_SESSION['save'][$page_name]['from'] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name]['from'], "integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and (title like '%$q%' or postfix like '%$q%' or ffmpeg_options like '%$q%') ";
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'watermark_image' || $sort_by == 'watermark2_image' || $sort_by == 'preroll_video' || $sort_by == 'postroll_video')
{
	$sort_by = $table_key_name;
} elseif ($sort_by == 'limit_total_duration')
{
	$sort_by = 'limit_total_duration_unit_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', limit_total_duration';
} elseif ($sort_by == 'limit_offset_start')
{
	$sort_by = 'limit_offset_start_unit_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', limit_offset_start';
} elseif ($sort_by == 'limit_offset_end')
{
	$sort_by = 'limit_offset_end_unit_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', limit_offset_end';
} elseif ($sort_by == 'limit_speed_value')
{
	$sort_by = 'limit_speed_option ' . $_SESSION['save'][$page_name]['sort_direction'] . ', limit_speed_value';
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// watermark
// =====================================================================================================================

if ($_REQUEST['action'] == 'download_watermark')
{
	$format_id = intval($_REQUEST['id']);
	header('Content-Type: image/png');
	header("Content-Disposition: inline; filename=\"watermark_video_{$format_id}.png\"");
	$watermark_file = "$config[project_path]/admin/data/other/watermark_video_{$format_id}.png";
	if (is_file($watermark_file))
	{
		header('Content-Length: ' . filesize($watermark_file));
		readfile($watermark_file);
	}
	die;
}

if ($_REQUEST['action'] == 'download_watermark2')
{
	$format_id = intval($_REQUEST['id']);
	header('Content-Type: image/png');
	header("Content-Disposition: inline; filename=\"watermark2_video_{$format_id}.png\"");
	$watermark2_file = "$config[project_path]/admin/data/other/watermark2_video_{$format_id}.png";
	if (is_file($watermark2_file))
	{
		header('Content-Length: ' . filesize($watermark2_file));
		readfile($watermark2_file);
	}
	die;
}

if ($_REQUEST['action'] == 'download_preroll')
{
	header('Content-Type: video/mp4');
	$format_id = intval($_REQUEST['id']);
	$preroll_file = "$config[project_path]/admin/data/other/preroll_video_{$format_id}.mp4";
	if (is_file($preroll_file))
	{
		header('Content-Length: ' . filesize($preroll_file));
		readfile($preroll_file);
	}
	die;
}

if ($_REQUEST['action'] == 'download_postroll')
{
	header('Content-Type: video/mp4');
	$format_id = intval($_REQUEST['id']);
	$postroll_file = "$config[project_path]/admin/data/other/postroll_video_{$format_id}.mp4";
	if (is_file($postroll_file))
	{
		header('Content-Length: ' . filesize($postroll_file));
		readfile($postroll_file);
	}
	die;
}

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if (in_array($_POST['action'], array('add_new_group_complete', 'change_group_complete')))
{
	if ($_POST['action'] == 'add_new_group_complete')
	{
		$_POST['action'] = 'add_new_complete';
	} else
	{
		$_POST['action'] = 'change_complete';
	}
	$table_name = "$config[tables_prefix]formats_videos_groups";
	$table_key_name = "format_video_group_id";

	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$item_id = intval($_POST['item_id']);

	validate_field('uniq', $_POST['title'], $lang['settings']['format_video_field_title'], array('field_name_in_base' => 'title'));

	if (!is_array($errors))
	{
		if ($_POST['action'] == 'add_new_complete')
		{
			$item_id = sql_insert("insert into $table_name set title=?, added_date=?",
				$_POST['title'], date('Y-m-d H:i:s')
			);

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, is_default=?, is_premium=?, set_duration_from=? where $table_key_name=?",
				$_POST['title'], intval($_POST['is_default']), intval($_POST['is_premium']), $_POST['set_duration_from'], $item_id
			);
			if (intval($_POST['is_default']) == 1)
			{
				sql_pr("update $table_name set is_default=0 where $table_key_name!=?", $item_id);
			}
			if (intval($_POST['is_premium']) == 1)
			{
				sql_pr("update $table_name set is_premium=0 where $table_key_name!=?", $item_id);
			}

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id<>2 and type_id not in (50,51,52,53)")) > 0)
	{
		if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
		{
			$errors[] = get_aa_error('format_video_changes_not_allowed');
			return_ajax_errors($errors);
		}
	}

	$item_id = intval($_POST['item_id']);

	validate_field('uniq', $_POST['title'], $lang['settings']['format_video_field_title'], array('field_name_in_base' => 'title'));
	if (validate_field('uniq', $_POST['postfix'], $lang['settings']['format_video_field_postfix'], array('field_name_in_base' => 'postfix')))
	{
		if (!preg_match("|^[a-z0-9_.]+$|s", $_POST['postfix']))
		{
			$errors[] = get_aa_error('format_video_postfix_symbols', $lang['settings']['format_video_field_postfix']);
		} else
		{
			if (is_numeric($_POST['postfix'][0]))
			{
				$errors[] = get_aa_error('format_video_postfix_start_with_number', $lang['settings']['format_video_field_postfix']);
			} else
			{
				$temp = explode('.', $_POST['postfix']);
				if (array_cnt($temp) != 2 || strlen($temp[1]) == 0)
				{
					$errors[] = get_aa_error('format_video_postfix_dot', $lang['settings']['format_video_field_postfix']);
				} else
				{
					$allowed_formats = explode(',', "$config[video_allowed_ext],gif");
					$is_allowed = 0;
					foreach ($allowed_formats as $format)
					{
						if (strlen($format) > 0 && strpos($_POST['postfix'], ".$format") === strlen($_POST['postfix']) - strlen(".$format"))
						{
							$is_allowed = 1;
							break;
						}
					}
					if ($is_allowed == 0)
					{
						$errors[] = get_aa_error('format_video_postfix_not_allowed', $lang['settings']['format_video_field_postfix']);
					}
				}
			}
		}
	}

	if (intval($_POST['status_id']) != 1 && mr2number(sql_pr("select count(*) from $table_name where status_id=1 and format_video_group_id=? and $table_key_name<>?", intval($_POST['format_video_group_id']), $item_id)) == 0)
	{
		$errors[] = get_aa_error('format_video_status_required', $lang['settings']['format_video_field_status']);
	}

	if (intval($_POST['resize_option']) == 1)
	{
		validate_field('size', $_POST['size'], $lang['settings']['format_video_field_size']);
	}
	validate_field('empty', $_POST['ffmpeg_options'], $lang['settings']['format_video_field_ffmpeg_options']);

	validate_field('file', 'watermark_image', $lang['settings']['format_video_field_watermark_image'], array('is_image' => '1', 'allowed_ext' => 'png'));
	if (in_array(intval($_POST['watermark_position_id']), array(5, 6, 7)))
	{
		if (validate_field('empty_int', $_POST['watermark_scrolling_duration'], $lang['settings']['format_video_field_watermark_position']))
		{
			validate_field('empty', $_POST['watermark_scrolling_times'], $lang['settings']['format_video_field_watermark_position']);
		}
	} elseif (intval($_POST['watermark_position_id']) == 8)
	{
		validate_field('empty_int', $_POST['watermark_dynamic_switches'], $lang['settings']['format_video_field_watermark_position']);
	}
	if ($_POST['watermark_max_width'] <> '')
	{
		validate_field('empty_int', $_POST['watermark_max_width'], $lang['settings']['format_video_field_watermark_max_width']);
	}
	if ($_POST['watermark_max_width_vertical'] <> '')
	{
		validate_field('empty_int', $_POST['watermark_max_width_vertical'], $lang['settings']['format_video_field_watermark_max_width']);
	}

	validate_field('file', 'watermark2_image', $lang['settings']['format_video_field_watermark2_image'], array('is_image' => '1', 'allowed_ext' => 'png'));
	if (in_array(intval($_POST['watermark2_position_id']), array(5, 6, 7)))
	{
		if (validate_field('empty_int', $_POST['watermark2_scrolling_duration'], $lang['settings']['format_video_field_watermark2_position']))
		{
			validate_field('empty', $_POST['watermark2_scrolling_times'], $lang['settings']['format_video_field_watermark2_position']);
		}
	} elseif (intval($_POST['watermark2_position_id']) == 8)
	{
		validate_field('empty_int', $_POST['watermark2_dynamic_switches'], $lang['settings']['format_video_field_watermark2_position']);
	}
	if ($_POST['watermark2_max_height'] <> '')
	{
		validate_field('empty_int', $_POST['watermark2_max_height'], $lang['settings']['format_video_field_watermark2_max_height']);
	}
	if ($_POST['watermark2_max_height_vertical'] <> '')
	{
		validate_field('empty_int', $_POST['watermark2_max_height_vertical'], $lang['settings']['format_video_field_watermark2_max_height']);
	}

	if (intval($_POST['access_level_id']) > 0 && mr2number(sql_pr("select access_level_id from $table_name where $table_key_name=?", $item_id)) == 0)
	{
		if (check_format_usage_in_player($_POST['postfix'], "$config[project_path]/admin/data/player/embed/config.dat"))
		{
			$errors[] = get_aa_error('format_video_embed_player_access_level', $lang['settings']['format_video_field_access_level']);
		}
	}

	if (intval($_POST['is_download_enabled']) == 1)
	{
		if ($_POST['download_order'] <> '' && $_POST['download_order'] <> '0')
		{
			validate_field('empty_int', $_POST['download_order'], $lang['settings']['format_video_field_enable_download_order']);
		}
	}

	$limit_duration_ok = 1;
	if ($_POST['limit_total_duration'] <> '' && $limit_duration_ok == 1)
	{
		$limit_duration_ok = validate_field('empty_int', $_POST['limit_total_duration'], $lang['settings']['format_video_field_limit_duration']);
	}
	if ($_POST['limit_total_min_duration_sec'] <> '' && $limit_duration_ok == 1)
	{
		$limit_duration_ok = validate_field('empty_int', $_POST['limit_total_min_duration_sec'], $lang['settings']['format_video_field_limit_duration']);
	}
	if ($_POST['limit_total_max_duration_sec'] <> '' && $limit_duration_ok == 1)
	{
		$limit_duration_ok = validate_field('empty_int', $_POST['limit_total_max_duration_sec'], $lang['settings']['format_video_field_limit_duration']);
	}
	if ($_POST['limit_total_min_duration_sec'] <> '' && $_POST['limit_total_max_duration_sec'] <> '' && $limit_duration_ok == 1 && intval($_POST['limit_total_min_duration_sec']) >= intval($_POST['limit_total_max_duration_sec']))
	{
		$errors[] = get_aa_error('invalid_int_range', $lang['settings']['format_video_field_limit_duration']);
	}

	if ($_POST['limit_offset_start'] <> '')
	{
		validate_field('empty_int', $_POST['limit_offset_start'], $lang['settings']['format_video_field_offset_start']);
	}
	if ($_POST['limit_offset_end'] <> '')
	{
		validate_field('empty_int', $_POST['limit_offset_end'], $lang['settings']['format_video_field_offset_end']);
	}

	if (validate_field('empty_int', $_POST['limit_number_parts'], $lang['settings']['format_video_field_number_of_parts']))
	{
		if ($limit_duration_ok == 1 && intval($_POST['limit_number_parts']) > 1)
		{
			if (intval($_POST['limit_total_duration']) > 0 && intval($_POST['limit_total_duration_unit_id']) == 0 && intval($_POST['limit_total_duration']) / intval($_POST['limit_number_parts']) < 1)
			{
				$errors[] = get_aa_error('format_video_parts_too_small', $lang['settings']['format_video_field_limit_duration']);
			} elseif (intval($_POST['limit_number_parts_crossfade']) > 0)
			{
				$part_projected_length = floor(intval($_POST['limit_total_duration']) / intval($_POST['limit_number_parts']));
				if ($part_projected_length == 1 || intval($_POST['limit_number_parts_crossfade']) > $part_projected_length / 2)
				{
					$errors[] = get_aa_error('format_video_parts_crossfade_too_big', $lang['settings']['format_video_field_number_of_parts'], $part_projected_length);
				}
			}
		}
	}

	validate_field('file', 'preroll_video', $lang['settings']['format_video_field_preroll_video'], array('allowed_ext' => 'mp4'));
	validate_field('file', 'postroll_video', $lang['settings']['format_video_field_postroll_video'], array('allowed_ext' => 'mp4'));

	if (strpos($_POST['postfix'], ".mp4") === false)
	{
		if ($_POST['preroll_video_hash'])
		{
			$errors[] = get_aa_error('format_video_rolls_not_allowed', $lang['settings']['format_video_field_preroll_video']);
		}
		if ($_POST['postroll_video_hash'])
		{
			$errors[] = get_aa_error('format_video_rolls_not_allowed', $lang['settings']['format_video_field_postroll_video']);
		}
	}

	if (intval($_POST['limit_speed_option']) > 0)
	{
		if (intval($_POST['limit_speed_option']) == 1)
		{
			validate_field('empty_int', $_POST['limit_speed_value'], $lang['settings']['format_video_field_limit_speed_global']);
		} elseif (intval($_POST['limit_speed_option']) == 2)
		{
			validate_field('empty_float', $_POST['limit_speed_value'], $lang['settings']['format_video_field_limit_speed_global']);
		}
	}
	if (intval($_POST['limit_speed_guests_option_override']) == 1 && intval($_POST['limit_speed_guests_option']) > 0)
	{
		if (intval($_POST['limit_speed_guests_option']) == 1)
		{
			validate_field('empty_int', $_POST['limit_speed_guests_value'], $lang['settings']['format_video_field_limit_speed_guests']);
		} elseif (intval($_POST['limit_speed_guests_option']) == 2)
		{
			validate_field('empty_float', $_POST['limit_speed_guests_value'], $lang['settings']['format_video_field_limit_speed_guests']);
		}
	}
	if (intval($_POST['limit_speed_standard_option_override']) == 1 && intval($_POST['limit_speed_standard_option']) > 0)
	{
		if (intval($_POST['limit_speed_standard_option']) == 1)
		{
			validate_field('empty_int', $_POST['limit_speed_standard_value'], $lang['settings']['format_video_field_limit_speed_standard']);
		} elseif (intval($_POST['limit_speed_standard_option']) == 2)
		{
			validate_field('empty_float', $_POST['limit_speed_standard_value'], $lang['settings']['format_video_field_limit_speed_standard']);
		}
	}
	if (intval($_POST['limit_speed_premium_option_override']) == 1 && intval($_POST['limit_speed_premium_option']) > 0)
	{
		if (intval($_POST['limit_speed_premium_option']) == 1)
		{
			validate_field('empty_int', $_POST['limit_speed_premium_value'], $lang['settings']['format_video_field_limit_speed_premium']);
		} elseif (intval($_POST['limit_speed_premium_option']) == 2)
		{
			validate_field('empty_float', $_POST['limit_speed_premium_value'], $lang['settings']['format_video_field_limit_speed_premium']);
		}
	}
	if (intval($_POST['limit_speed_embed_option_override']) == 1 && intval($_POST['limit_speed_embed_option']) > 0)
	{
		if (intval($_POST['limit_speed_embed_option']) == 1)
		{
			validate_field('empty_int', $_POST['limit_speed_embed_value'], $lang['settings']['format_video_field_limit_speed_embed']);
		} elseif (intval($_POST['limit_speed_embed_option']) == 2)
		{
			validate_field('empty_float', $_POST['limit_speed_embed_value'], $lang['settings']['format_video_field_limit_speed_embed']);
		}
	}

	if (intval($_POST['is_timeline_enabled']) == 1)
	{
		if (intval($_POST['timeline_option']) == 1)
		{
			validate_field('empty_int', $_POST['timeline_amount'], $lang['settings']['format_video_field_timeline_screenshots_option']);
		} else
		{
			validate_field('empty_int', $_POST['timeline_interval'], $lang['settings']['format_video_field_timeline_screenshots_option']);
		}
		if (validate_field('empty', $_POST['timeline_directory'], $lang['settings']['format_video_field_timeline_screenshots_directory']))
		{
			if (!preg_match("|^[a-z0-9_]+$|s", $_POST['timeline_directory']))
			{
				$errors[] = get_aa_error('format_video_timeline_folder_symbols', $lang['settings']['format_video_field_timeline_screenshots_directory']);
			} else
			{
				validate_field('uniq', $_POST['timeline_directory'], $lang['settings']['format_video_field_timeline_screenshots_directory'], array('field_name_in_base' => 'timeline_directory'));
			}
		}
		if ($_POST['action'] == 'add_new_complete')
		{
			$old_timeline_enabled = 0;
		} else
		{
			$old_timeline_enabled = mr2number(sql_pr("select is_timeline_enabled from $table_name where $table_key_name=?", $item_id));
		}
	}

	if (!is_array($errors))
	{
		if (intval($_POST['limit_speed_guests_option_override']) == 0)
		{
			$_POST['limit_speed_guests_option'] = $_POST['limit_speed_option'];
			$_POST['limit_speed_guests_value'] = $_POST['limit_speed_value'];
		}
		if (intval($_POST['limit_speed_standard_option_override']) == 0)
		{
			$_POST['limit_speed_standard_option'] = $_POST['limit_speed_option'];
			$_POST['limit_speed_standard_value'] = $_POST['limit_speed_value'];
		}
		if (intval($_POST['limit_speed_premium_option_override']) == 0)
		{
			$_POST['limit_speed_premium_option'] = $_POST['limit_speed_option'];
			$_POST['limit_speed_premium_value'] = $_POST['limit_speed_value'];
		}
		if (intval($_POST['limit_speed_embed_option_override']) == 0)
		{
			$_POST['limit_speed_embed_option'] = $_POST['limit_speed_option'];
			$_POST['limit_speed_embed_value'] = $_POST['limit_speed_value'];
		}
		if (intval($_POST['limit_speed_option']) == 0)
		{
			$_POST['limit_speed_value'] = 0;
		}
		if (intval($_POST['limit_speed_guests_option']) == 0)
		{
			$_POST['limit_speed_guests_value'] = 0;
		}
		if (intval($_POST['limit_speed_standard_option']) == 0)
		{
			$_POST['limit_speed_standard_value'] = 0;
		}
		if (intval($_POST['limit_speed_premium_option']) == 0)
		{
			$_POST['limit_speed_premium_value'] = 0;
		}
		if (intval($_POST['limit_speed_embed_option']) == 0)
		{
			$_POST['limit_speed_embed_value'] = 0;
		}

		if (!is_array($_POST['limit_speed_countries']))
		{
			$_POST['limit_speed_countries'] = array();
		}

		if ($_POST['action'] == 'add_new_complete')
		{
			$item_id = sql_insert("insert into $table_name set title=?, postfix=?, status_id=?, is_conditional=?, is_use_as_source=?, format_video_group_id=?, size=?, resize_option=?, resize_option2=?, ffmpeg_options=?, watermark_position_id=?, watermark_offset_random=?, watermark_scrolling_direction=?, watermark_scrolling_duration=?, watermark_scrolling_times=?, watermark_dynamic_switches=?, watermark_max_width=?, watermark_max_width_vertical=?, customize_watermark_id=?, watermark2_position_id=?, watermark2_offset_random=?, watermark2_scrolling_direction=?, watermark2_scrolling_duration=?, watermark2_scrolling_times=?, watermark2_dynamic_switches=?, watermark2_max_height=?, watermark2_max_height_vertical=?, customize_watermark2_id=?, access_level_id=?, is_hotlink_protection_disabled=?, is_download_enabled=?, download_order=?,
					limit_total_duration=?, limit_total_duration_unit_id=?, limit_total_min_duration_sec=?, limit_total_max_duration_sec=?, limit_number_parts=?, limit_number_parts_crossfade=?, limit_offset_start=?, limit_offset_start_unit_id=?, limit_offset_end=?, limit_offset_end_unit_id=?, limit_is_last_part_from_end=?, customize_duration_id=?, customize_offset_start_id=?, customize_offset_end_id=?,
					preroll_video_uploaded=?, customize_preroll_video_id=?, postroll_video_uploaded=?, customize_postroll_video_id=?, limit_speed_option=?, limit_speed_value=?, limit_speed_guests_option=?, limit_speed_guests_value=?, limit_speed_standard_option=?, limit_speed_standard_value=?, limit_speed_premium_option=?, limit_speed_premium_value=?, limit_speed_embed_option=?, limit_speed_embed_value=?, limit_speed_countries=?, is_timeline_enabled=?, timeline_option=?, timeline_amount=?, timeline_interval=?, timeline_directory=?, added_date=?",
				$_POST['title'], $_POST['postfix'], intval($_POST['status_id']), intval($_POST['is_conditional']), intval($_POST['is_use_as_source']), intval($_POST['format_video_group_id']), $_POST['size'], intval($_POST['resize_option']), intval($_POST['resize_option2']), $_POST['ffmpeg_options'], intval($_POST['watermark_position_id']), trim($_POST['watermark_offset_random']), intval($_POST['watermark_scrolling_direction']), intval($_POST['watermark_scrolling_duration']), trim($_POST['watermark_scrolling_times']), intval($_POST['watermark_dynamic_switches']), intval($_POST['watermark_max_width']), intval($_POST['watermark_max_width_vertical']), intval($_POST['customize_watermark_id']), intval($_POST['watermark2_position_id']), trim($_POST['watermark2_offset_random']), intval($_POST['watermark2_scrolling_direction']), intval($_POST['watermark2_scrolling_duration']), trim($_POST['watermark2_scrolling_times']), intval($_POST['watermark2_dynamic_switches']), intval($_POST['watermark2_max_height']), intval($_POST['watermark2_max_height_vertical']), intval($_POST['customize_watermark2_id']), intval($_POST['access_level_id']), intval($_POST['is_hotlink_protection_disabled']), intval($_POST['is_download_enabled']), intval($_POST['download_order']),
				intval($_POST['limit_total_duration']), intval($_POST['limit_total_duration_unit_id']), intval($_POST['limit_total_min_duration_sec']), intval($_POST['limit_total_max_duration_sec']), intval($_POST['limit_number_parts']), intval($_POST['limit_number_parts_crossfade']), intval($_POST['limit_offset_start']), intval($_POST['limit_offset_start_unit_id']), intval($_POST['limit_offset_end']), intval($_POST['limit_offset_end_unit_id']), intval($_POST['limit_is_last_part_from_end']), intval($_POST['customize_duration_id']), intval($_POST['customize_offset_start_id']), intval($_POST['customize_offset_end_id']),
				intval($_POST['preroll_video_uploaded']), intval($_POST['customize_preroll_video_id']), intval($_POST['postroll_video_uploaded']), intval($_POST['customize_postroll_video_id']), intval($_POST['limit_speed_option']), floatval($_POST['limit_speed_value']), intval($_POST['limit_speed_guests_option']), floatval($_POST['limit_speed_guests_value']), intval($_POST['limit_speed_standard_option']), floatval($_POST['limit_speed_standard_value']), intval($_POST['limit_speed_premium_option']), floatval($_POST['limit_speed_premium_value']), intval($_POST['limit_speed_embed_option']), floatval($_POST['limit_speed_embed_value']), implode(',', array_map('trim', $_POST['limit_speed_countries'])), intval($_POST['is_timeline_enabled']), intval($_POST['timeline_option']), intval($_POST['timeline_amount']), intval($_POST['timeline_interval']), $_POST['timeline_directory'], date("Y-m-d H:i:s")
			);

			transfer_uploaded_file('watermark_image', "$config[project_path]/admin/data/other/watermark_video_{$item_id}.png");
			transfer_uploaded_file('watermark2_image', "$config[project_path]/admin/data/other/watermark2_video_{$item_id}.png");
			transfer_uploaded_file('preroll_video', "$config[project_path]/admin/data/other/preroll_video_{$item_id}.mp4");
			transfer_uploaded_file('postroll_video', "$config[project_path]/admin/data/other/postroll_video_{$item_id}.mp4");

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));

			sql_pr("update $table_name set title=?, postfix=?, status_id=?, is_conditional=?, is_use_as_source=?, size=?, resize_option=?, resize_option2=?, ffmpeg_options=?, watermark_position_id=?, watermark_offset_random=?, watermark_scrolling_direction=?, watermark_scrolling_duration=?, watermark_scrolling_times=?, watermark_dynamic_switches=?, watermark_max_width=?, watermark_max_width_vertical=?, customize_watermark_id=?, watermark2_position_id=?, watermark2_offset_random=?, watermark2_scrolling_direction=?, watermark2_scrolling_duration=?, watermark2_scrolling_times=?, watermark2_dynamic_switches=?, watermark2_max_height=?, watermark2_max_height_vertical=?, customize_watermark2_id=?, access_level_id=?, is_hotlink_protection_disabled=?, is_download_enabled=?, download_order=?,
					limit_total_duration=?, limit_total_duration_unit_id=?, limit_total_min_duration_sec=?, limit_total_max_duration_sec=?, limit_number_parts=?, limit_number_parts_crossfade=?, limit_offset_start=?, limit_offset_start_unit_id=?, limit_offset_end=?, limit_offset_end_unit_id=?, limit_is_last_part_from_end=?, customize_duration_id=?, customize_offset_start_id=?, customize_offset_end_id=?,
					preroll_video_uploaded=?, customize_preroll_video_id=?, postroll_video_uploaded=?, customize_postroll_video_id=?, limit_speed_option=?, limit_speed_value=?, limit_speed_guests_option=?, limit_speed_guests_value=?, limit_speed_standard_option=?, limit_speed_standard_value=?, limit_speed_premium_option=?, limit_speed_premium_value=?, limit_speed_embed_option=?, limit_speed_embed_value=?, limit_speed_countries=?, is_timeline_enabled=?, timeline_option=?, timeline_amount=?, timeline_interval=?, timeline_directory=? where $table_key_name=?",
				$_POST['title'], $_POST['postfix'], intval($_POST['status_id']), intval($_POST['is_conditional']), intval($_POST['is_use_as_source']), $_POST['size'], intval($_POST['resize_option']), intval($_POST['resize_option2']), $_POST['ffmpeg_options'], intval($_POST['watermark_position_id']), trim($_POST['watermark_offset_random']), intval($_POST['watermark_scrolling_direction']), intval($_POST['watermark_scrolling_duration']), trim($_POST['watermark_scrolling_times']), intval($_POST['watermark_dynamic_switches']), intval($_POST['watermark_max_width']), intval($_POST['watermark_max_width_vertical']), intval($_POST['customize_watermark_id']), intval($_POST['watermark2_position_id']), trim($_POST['watermark2_offset_random']), intval($_POST['watermark2_scrolling_direction']), intval($_POST['watermark2_scrolling_duration']), trim($_POST['watermark2_scrolling_times']), intval($_POST['watermark2_dynamic_switches']), intval($_POST['watermark2_max_height']), intval($_POST['watermark2_max_height_vertical']), intval($_POST['customize_watermark2_id']), intval($_POST['access_level_id']), intval($_POST['is_hotlink_protection_disabled']), intval($_POST['is_download_enabled']), intval($_POST['download_order']),
				intval($_POST['limit_total_duration']), intval($_POST['limit_total_duration_unit_id']), intval($_POST['limit_total_min_duration_sec']), intval($_POST['limit_total_max_duration_sec']), intval($_POST['limit_number_parts']), intval($_POST['limit_number_parts_crossfade']), intval($_POST['limit_offset_start']), intval($_POST['limit_offset_start_unit_id']), intval($_POST['limit_offset_end']), intval($_POST['limit_offset_end_unit_id']), intval($_POST['limit_is_last_part_from_end']), intval($_POST['customize_duration_id']), intval($_POST['customize_offset_start_id']), intval($_POST['customize_offset_end_id']),
				intval($_POST['preroll_video_uploaded']), intval($_POST['customize_preroll_video_id']), intval($_POST['postroll_video_uploaded']), intval($_POST['customize_postroll_video_id']), intval($_POST['limit_speed_option']), floatval($_POST['limit_speed_value']), intval($_POST['limit_speed_guests_option']), floatval($_POST['limit_speed_guests_value']), intval($_POST['limit_speed_standard_option']), floatval($_POST['limit_speed_standard_value']), intval($_POST['limit_speed_premium_option']), floatval($_POST['limit_speed_premium_value']), intval($_POST['limit_speed_embed_option']), floatval($_POST['limit_speed_embed_value']), implode(',', array_map('trim', $_POST['limit_speed_countries'])), intval($_POST['is_timeline_enabled']), intval($_POST['timeline_option']), intval($_POST['timeline_amount']), intval($_POST['timeline_interval']), $_POST['timeline_directory'], $item_id
			);

			if (intval($_POST['status_id']) != 1)
			{
				sql_update("update $config[tables_prefix]formats_videos_groups set set_duration_from='' where set_duration_from=? and format_video_group_id=?", $old_data['postfix'], $old_data['format_video_group_id']);
			}
			if ($_POST['watermark_image_hash'])
			{
				transfer_uploaded_file('watermark_image', "$config[project_path]/admin/data/other/watermark_video_{$item_id}.png");
			} elseif (!$_POST['watermark_image'])
			{
				@unlink("$config[project_path]/admin/data/other/watermark_video_{$item_id}.png");
			}
			if ($_POST['watermark2_image_hash'])
			{
				transfer_uploaded_file('watermark2_image', "$config[project_path]/admin/data/other/watermark2_video_{$item_id}.png");
			} elseif (!$_POST['watermark2_image'])
			{
				@unlink("$config[project_path]/admin/data/other/watermark2_video_{$item_id}.png");
			}
			if ($_POST['preroll_video_hash'])
			{
				transfer_uploaded_file('preroll_video', "$config[project_path]/admin/data/other/preroll_video_{$item_id}.mp4");
			} elseif (!$_POST['preroll_video'])
			{
				@unlink("$config[project_path]/admin/data/other/preroll_video_{$item_id}.mp4");
			}
			if ($_POST['postroll_video_hash'])
			{
				transfer_uploaded_file('postroll_video', "$config[project_path]/admin/data/other/postroll_video_{$item_id}.mp4");
			} elseif (!$_POST['postroll_video'])
			{
				@unlink("$config[project_path]/admin/data/other/postroll_video_{$item_id}.mp4");
			}

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		if (intval($_POST['is_use_as_source']) == 1)
		{
			sql_pr("update $table_name set is_use_as_source=0 where $table_key_name<>? and format_video_group_id=?", $item_id, intval($_POST['format_video_group_id']));
		}

		$started_creation_tasks = 0;
		$started_timelines_creation_tasks = 0;

		$where_type = " and format_video_group_id=" . intval($_POST['format_video_group_id']);
		if (intval($_POST['status_id']) == 1)
		{
			$video_ids = mr2array_list(sql("select video_id from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats not like '%||$_POST[postfix]|%' $where_type"));
			foreach ($video_ids as $video_id)
			{
				$background_task = array();
				$background_task['format_postfix'] = $_POST['postfix'];
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where type_id=4 and video_id=?", $video_id)) == 0)
				{
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=4, video_id=?, data=?, added_date=?", $video_id, serialize($background_task), date("Y-m-d H:i:s"));
					$started_creation_tasks++;
				}
			}
		}
		if (intval($_POST['is_timeline_enabled']) == 1 && $old_timeline_enabled == 0)
		{
			$videos = mr2array(sql("select video_id, file_formats from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats like '%||$_POST[postfix]|%' $where_type"));
			foreach ($videos as $video)
			{
				$video_formats = get_video_formats($video['video_id'], $video['file_formats']);
				foreach ($video_formats as $video_format)
				{
					if ($video_format['postfix'] == $_POST['postfix'] && intval($video_format['timeline_screen_amount']) == 0)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where type_id=8 and video_id=?", $video['video_id'])) == 0)
						{
							sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=8, video_id=?, data=?, added_date=?", $video['video_id'], serialize(['format_postfix' => $_POST['postfix']]), date("Y-m-d H:i:s"));
							$started_timelines_creation_tasks++;
						}
					}
				}
			}
		}

		if ($started_creation_tasks > 0)
		{
			$_SESSION['messages'][] = str_replace('%1%', $started_creation_tasks, $lang['settings']['format_video_success_message_created']);
		} elseif ($started_timelines_creation_tasks > 0)
		{
			$_SESSION['messages'][] = str_replace('%1%', $started_timelines_creation_tasks, $lang['settings']['format_video_success_message_timelines_created']);
		}

		if (intval($_POST['is_timeline_enabled']) == 0 && $_POST['timeline_directory']!='' && isset($_POST['save_and_delete_timelines']))
		{
			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=20, data=?, added_date=?", serialize(['format_postfix' => $_POST['postfix']]), date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['settings']['format_video_success_message_timelines_deleted'];
		}

		update_format_data();
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// table actions
// =====================================================================================================================

if ($_REQUEST['batch_action'] != '')
{
	if (is_array($_REQUEST['row_select']) && array_search('0', $_REQUEST['row_select']) !== false)
	{
		unset($_REQUEST['row_select'][array_search('0', $_REQUEST['row_select'])]);
	}

	if (is_array($_REQUEST['row_select']) && array_cnt($_REQUEST['row_select']) > 0)
	{
		$format_id = intval($_REQUEST['row_select'][0]);
		if ($_REQUEST['batch_action'] == 'delete')
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id<>2 and type_id not in (50,51,52,53)")) > 0)
			{
				if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
				{
					$errors[] = get_aa_error('format_video_changes_not_allowed');
					return_ajax_errors($errors);
				}
			}

			$format_data = mr2array_single(sql_pr("select *, (select count(*) from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats like concat('%||', postfix, '|%')) as videos_count from $table_name where $table_key_name=?", $format_id));
			if (!empty($format_data))
			{
				if ($format_data['videos_count'] > 0 || $format_data['format_video_group_id'] == 0)
				{
					if (mr2number(sql_pr("select count(*) from $table_name where $table_key_name!=? and status_id=1 and format_video_group_id=?", $format_id, $format_data['format_video_group_id'])) == 0)
					{
						$errors[] = get_aa_error('format_video_delete_not_allowed');
						return_ajax_errors($errors);
					}
				}
				$used_in_player = false;
				$player_data_files = get_player_data_files();
				foreach ($player_data_files as $player_data_file)
				{
					if (check_format_usage_in_player($format_data['postfix'], $player_data_file['file']))
					{
						$used_in_player = true;
					}
				}
				if ($used_in_player)
				{
					$errors[] = get_aa_error('format_video_delete_not_allowed2');
					return_ajax_errors($errors);
				}

				sql_update("update $config[tables_prefix]formats_videos_groups set set_duration_from='' where set_duration_from=? and format_video_group_id=?", $format_data['postfix'], $format_data['format_video_group_id']);
				if ($format_data['videos_count'] > 0)
				{
					sql_pr("update $table_name set status_id=3 where $table_key_name=?", $format_id);

					$background_task = array();
					$background_task['format_postfix'] = $format_data['postfix'];
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=6, data=?, added_date=?", serialize($background_task), date("Y-m-d H:i:s"));

					$_SESSION['messages'][] = $lang['settings']['format_video_success_message_deleted'];
				} else
				{
					sql_pr("delete from $table_name where $table_key_name=?", $format_id);

					$_SESSION['messages'][] = $lang['common']['success_message_removed'];
				}

				@unlink("$config[project_path]/admin/data/other/watermark_video_{$format_id}.png");
				@unlink("$config[project_path]/admin/data/other/watermark2_video_{$format_id}.png");
				@unlink("$config[project_path]/admin/data/other/preroll_video_{$format_id}.mp4");
				@unlink("$config[project_path]/admin/data/other/postroll_video_{$format_id}.mp4");

				sql_pr("update $config[tables_prefix]videos_feeds_import set $table_key_name=0 where $table_key_name=?", $format_id);
				update_format_data();

				return_ajax_success($page_name);
			}
		} elseif ($_REQUEST['batch_action'] == 'delete_timelines')
		{
			$format_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $format_id));
			if (!empty($format_data))
			{
				$background_task = array();
				$background_task['format_postfix'] = $format_data['postfix'];
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=20, data=?, added_date=?", serialize($background_task), date("Y-m-d H:i:s"));

				$_SESSION['messages'][] = $lang['settings']['format_video_success_message_timelines_deleted'];
				return_ajax_success($page_name);
			}
		} elseif ($_REQUEST['batch_action'] == 'delete_group')
		{
			if (mr2number(sql_pr("select count(*) from $table_name where format_video_group_id=?", $format_id)) == 0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where format_video_group_id=?", $format_id)) == 0)
			{
				sql_delete("delete from $config[tables_prefix]formats_videos_groups where format_video_group_id=?", $format_id);
				$_SESSION['messages'][] = $lang['common']['success_message_removed'];
				return_ajax_success($page_name);
			}
		}
	}

	$errors[] = get_aa_error('unexpected_error');
	return_ajax_errors($errors);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change_group')
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_videos_groups where format_video_group_id=?", $item_id));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}
	$_POST['required_formats'] = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id=1 and format_video_group_id=?", $item_id));
}

if ($_GET['action'] == 'change')
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=? and status_id in (0,1,2)", $item_id));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}
	if ($_POST['watermark_scrolling_duration'] == '0')
	{
		$_POST['watermark_scrolling_duration'] = '';
	}
	if ($_POST['watermark_dynamic_switches'] == '0')
	{
		$_POST['watermark_dynamic_switches'] = '';
	}
	if ($_POST['watermark_max_width'] == '0')
	{
		$_POST['watermark_max_width'] = '';
	}
	if ($_POST['watermark_max_width_vertical'] == '0')
	{
		$_POST['watermark_max_width_vertical'] = '';
	}
	if ($_POST['watermark2_scrolling_duration'] == '0')
	{
		$_POST['watermark2_scrolling_duration'] = '';
	}
	if ($_POST['watermark2_dynamic_switches'] == '0')
	{
		$_POST['watermark2_dynamic_switches'] = '';
	}
	if ($_POST['watermark2_max_height'] == '0')
	{
		$_POST['watermark2_max_height'] = '';
	}
	if ($_POST['watermark2_max_height_vertical'] == '0')
	{
		$_POST['watermark2_max_height_vertical'] = '';
	}
	if ($_POST['limit_total_duration'] == '0')
	{
		$_POST['limit_total_duration'] = '';
	}
	if ($_POST['limit_total_min_duration_sec'] == '0')
	{
		$_POST['limit_total_min_duration_sec'] = '';
	}
	if ($_POST['limit_total_max_duration_sec'] == '0')
	{
		$_POST['limit_total_max_duration_sec'] = '';
	}
	if ($_POST['limit_offset_start'] == '0')
	{
		$_POST['limit_offset_start'] = '';
	}
	if ($_POST['limit_offset_end'] == '0')
	{
		$_POST['limit_offset_end'] = '';
	}
	if ($_POST['limit_speed_value'] == '0')
	{
		$_POST['limit_speed_value'] = '';
	}
	if ($_POST['limit_speed_guests_value'] == '0')
	{
		$_POST['limit_speed_guests_value'] = '';
	}
	if ($_POST['limit_speed_standard_value'] == '0')
	{
		$_POST['limit_speed_standard_value'] = '';
	}
	if ($_POST['limit_speed_premium_value'] == '0')
	{
		$_POST['limit_speed_premium_value'] = '';
	}
	if ($_POST['limit_speed_embed_value'] == '0')
	{
		$_POST['limit_speed_embed_value'] = '';
	}
	if ($_POST['timeline_interval'] == '0')
	{
		$_POST['timeline_interval'] = '';
	}
	if ($_POST['timeline_amount'] == '0')
	{
		$_POST['timeline_amount'] = '';
	}

	if (is_file("$config[project_path]/admin/data/other/watermark_video_{$item_id}.png"))
	{
		$_POST['watermark_image'] = "watermark_video_{$item_id}.png";
		$_POST['watermark_image_url'] = "$page_name?action=download_watermark&id={$item_id}";
	}
	if (is_file("$config[project_path]/admin/data/other/watermark2_video_{$item_id}.png"))
	{
		$_POST['watermark2_image'] = "watermark2_video_{$item_id}.png";
		$_POST['watermark2_image_url'] = "$page_name?action=download_watermark2&id={$item_id}";
	}
	if (is_file("$config[project_path]/admin/data/other/preroll_video_{$item_id}.mp4"))
	{
		$_POST['preroll_video'] = "preroll_video_{$item_id}.mp4";
		$_POST['preroll_video_url'] = "$page_name?action=download_preroll&id={$item_id}";
	}
	if (is_file("$config[project_path]/admin/data/other/postroll_video_{$item_id}.mp4"))
	{
		$_POST['postroll_video'] = "postroll_video_{$item_id}.mp4";
		$_POST['postroll_video_url'] = "$page_name?action=download_postroll&id={$item_id}";
	}

	if (strlen($_POST['limit_speed_countries']) == 0)
	{
		$_POST['limit_speed_countries'] = array();
	} else
	{
		$_POST['limit_speed_countries'] = explode(',', $_POST['limit_speed_countries']);
	}

	$_POST['videos_count'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats like concat('%||',?,'|%')", $_POST['postfix']));

	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id<>2 and type_id not in (50,51,52,53)")) > 0)
	{
		if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
		{
			$_POST['errors'][] = get_aa_error('format_video_changes_not_allowed');
		}
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['status_id'] = '2';
	$_POST['resize_option'] = '1';
	$_POST['resize_option2'] = '2';
	$_POST['ffmpeg_options'] = '-vcodec libx264 -movflags +faststart -threads 0 -r 25 -g 50 -crf 25 -me_method hex -trellis 0 -bf 8 -acodec aac -strict -2 -ar 44100 -ab 128k -f mp4';
	$_POST['limit_number_parts'] = '1';

	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id<>2 and type_id not in (50,51,52,53)")) > 0)
	{
		if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
		{
			$_POST['errors'][] = get_aa_error('format_video_changes_not_allowed');
		}
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$formats_deleting_tasks = mr2array(sql("select * from $config[tables_prefix]background_tasks where type_id=6"));

$data = [];
$total_count = mr2number(sql("select count(*) from $table_name where 1=1 $where"));
$temp = mr2array(sql("select $table_selector from $table_name where 1=1 $where order by $sort_by"));
foreach ($temp as $format)
{
	$data[$format['format_video_group_id']][] = $format;
}

$videos_count_summary = [];
foreach ($table_fields as $k => $field)
{
	if ($field['id'] == 'videos_count' && $field['is_enabled'] == 1)
	{
		$videos_count_summary_query = '';
		$videos_count_summary_query_inner = '';
		foreach ($data as $index => $data_item)
		{
			foreach ($data_item as $k => $v)
			{
				$videos_count_summary_query .= ", sum(format$v[format_video_id]) as format$v[format_video_id]";
				$videos_count_summary_query_inner .= ", case when INSTR(file_formats, '||$v[postfix]|') > 0 then 1 else 0 end as format$v[format_video_id]";
			}
		}
		if ($videos_count_summary_query !== '')
		{
			$videos_count_summary = mr2array_single(sql_pr("select xx{$videos_count_summary_query} from (select 1 as xx {$videos_count_summary_query_inner} from $config[tables_prefix]videos where load_type_id=1 and status_id in (0, 1)) x"));
		}
		break;
	}
}

foreach ($data as $index => $data_item)
{
	foreach ($data_item as $k => $v)
	{
		$data[$index][$k]['videos_count'] = intval($videos_count_summary["format{$v['format_video_id']}"]);
		if (in_array($v['watermark_position_id'], array(5, 6, 7)))
		{
			$data[$index][$k]['watermark_position_scrolling'] = $v['watermark_scrolling_times'] . ' x ' . $v['watermark_scrolling_duration'] . $lang['common']['second_truncated'];
			if ($v['watermark_offset_random'] !== '')
			{
				$data[$index][$k]['watermark_position_scrolling'] .= " ±$v[watermark_offset_random]";
			}
		} else
		{
			if ($v['watermark_offset_random'] !== '')
			{
				$data[$index][$k]['watermark_position_offset'] .= "±$v[watermark_offset_random]";
			}
		}
		if ($v['watermark_max_width'] > 0 || $v['watermark_max_width_vertical'] > 0)
		{
			$data[$index][$k]['watermark_max_width'] = "$v[watermark_max_width]% / $v[watermark_max_width_vertical]%";
		} else
		{
			$data[$index][$k]['watermark_max_width'] = '';
		}

		if (in_array($v['watermark2_position_id'], array(5, 6, 7)))
		{
			$data[$index][$k]['watermark2_position_scrolling'] = $v['watermark2_scrolling_times'] . ' x ' . $v['watermark2_scrolling_duration'] . $lang['common']['second_truncated'];
			if ($v['watermark2_offset_random'] !== '')
			{
				$data[$index][$k]['watermark2_position_scrolling'] .= " ±$v[watermark2_offset_random]";
			}
		} else
		{
			if ($v['watermark2_offset_random'] !== '')
			{
				$data[$index][$k]['watermark2_position_offset'] .= "±$v[watermark2_offset_random]";
			}
		}
		if ($v['watermark2_max_height'] > 0 || $v['watermark2_max_height_vertical'] > 0)
		{
			$data[$index][$k]['watermark2_max_height'] = "$v[watermark2_max_height]% / $v[watermark2_max_height_vertical]%";
		} else
		{
			$data[$index][$k]['watermark2_max_height'] = '';
		}

		if (is_file("$config[project_path]/admin/data/other/watermark_video_{$v['format_video_id']}.png"))
		{
			$data[$index][$k]['watermark_image'] = "{$v['format_video_id']}.png";
			$data[$index][$k]['watermark_image_url'] = "$page_name?action=download_watermark&id=$v[format_video_id]";
		} else
		{
			$data[$index][$k]['watermark_position_id'] = '';
			$data[$index][$k]['watermark_max_width'] = '';
		}
		if (is_file("$config[project_path]/admin/data/other/watermark2_video_{$v['format_video_id']}.png"))
		{
			$data[$index][$k]['watermark2_image'] = "{$v['format_video_id']}.png";
			$data[$index][$k]['watermark2_image_url'] = "$page_name?action=download_watermark2&id=$v[format_video_id]";
		} else
		{
			$data[$index][$k]['watermark2_position_id'] = '';
			$data[$index][$k]['watermark2_max_height'] = '';
		}
		if (is_file("$config[project_path]/admin/data/other/preroll_video_{$v['format_video_id']}.mp4"))
		{
			$data[$index][$k]['preroll_video'] = "{$v['format_video_id']}.mp4";
			$data[$index][$k]['preroll_video_url'] = "$page_name?action=download_preroll&id=$v[format_video_id]";
		}
		if (is_file("$config[project_path]/admin/data/other/postroll_video_{$v['format_video_id']}.mp4"))
		{
			$data[$index][$k]['postroll_video'] = "{$v['format_video_id']}.mp4";
			$data[$index][$k]['postroll_video_url'] = "$page_name?action=download_postroll&id=$v[format_video_id]";
		}

		if ($v['resize_option'] == 2)
		{
			$data[$index][$k]['size'] = $lang['settings']['format_video_field_size_source'];
		} elseif ($v['resize_option2'] == 0)
		{
			$data[$index][$k]['size'] = $v['size'] . " " . $lang['settings']['format_video_field_size_dynamic_height'];
		} elseif ($v['resize_option2'] == 1)
		{
			$data[$index][$k]['size'] = $v['size'] . " " . $lang['settings']['format_video_field_size_fixed'];
		} elseif ($v['resize_option2'] == 2)
		{
			$data[$index][$k]['size'] = $v['size'] . " " . $lang['settings']['format_video_field_size_dynamic_width'];
		}

		if (intval($options['ENABLE_ANTI_HOTLINK']) == 0)
		{
			$data[$index][$k]['is_hotlink_protection_enabled'] = 0;
		}

		if ($v['customize_duration_id'] > 0)
		{
			$data[$index][$k]['limit_total_duration'] = $lang['settings']['format_video_field_duration_custom'];
		} elseif ($v['limit_total_duration'] == 0)
		{
			$data[$index][$k]['limit_total_duration'] = $lang['settings']['format_video_field_duration_source'];
		} else
		{
			$duration_string = '';
			if ($v['limit_total_duration_unit_id'] == 0)
			{
				$duration_string = $v['limit_total_duration'] . $lang['common']['second_truncated'];
			} elseif ($v['limit_total_duration_unit_id'] == 1)
			{
				$duration_string = '';
				if ($v['limit_total_min_duration_sec']>0)
				{
					$duration_string .= $v['limit_total_min_duration_sec'] . $lang['common']['second_truncated'] . ' ≤ ';
				}
				$duration_string .= $v['limit_total_duration'] . '%';
				if ($v['limit_total_max_duration_sec']>0)
				{
					$duration_string .= ' ≤ ' . $v['limit_total_max_duration_sec'] . $lang['common']['second_truncated'];
				}
			}

			if ($v['limit_number_parts'] > 1)
			{
				$duration_string .= " / $v[limit_number_parts]";
			}

			$data[$index][$k]['limit_total_duration'] = $duration_string;
		}

		if ($v['customize_offset_start_id'] > 0)
		{
			$data[$index][$k]['limit_offset_start'] = $lang['settings']['format_video_field_offset_start_custom'];
		} elseif ($v['limit_offset_start'] == 0)
		{
			$data[$index][$k]['limit_offset_start'] = '';
		} elseif ($v['limit_offset_start_unit_id'] == 1)
		{
			$data[$index][$k]['limit_offset_start'] = $v['limit_offset_start'] . '%';
		} else
		{
			$data[$index][$k]['limit_offset_start'] = $v['limit_offset_start'] . $lang['common']['second_truncated'];
		}

		if ($v['customize_offset_end_id'] > 0)
		{
			$data[$index][$k]['limit_offset_end'] = $lang['settings']['format_video_field_offset_end_custom'];
		} elseif ($v['limit_offset_end'] == 0)
		{
			$data[$index][$k]['limit_offset_end'] = '';
		} elseif ($v['limit_offset_end_unit_id'] == 1)
		{
			$data[$index][$k]['limit_offset_end'] = $v['limit_offset_end'] . '%';
		} else
		{
			$data[$index][$k]['limit_offset_end'] = $v['limit_offset_end'] . $lang['common']['second_truncated'];
		}

		$speed_limit_string = $lang['common']['undefined'];
		if ($v['limit_speed_option'] == 1)
		{
			$speed_limit_string = $v['limit_speed_value'] . ' ' . $lang['settings']['format_video_field_limit_speed_option_fixed_kbps'];
		} elseif ($v['limit_speed_option'] == 2)
		{
			$speed_limit_string = 'x' . $v['limit_speed_value'];
		}
		if ($v['limit_speed_guests_option'] != $v['limit_speed_option'] || $v['limit_speed_guests_value'] != $v['limit_speed_value'] ||
			$v['limit_speed_standard_option'] != $v['limit_speed_option'] || $v['limit_speed_standard_value'] != $v['limit_speed_value'] ||
			$v['limit_speed_premium_option'] != $v['limit_speed_option'] || $v['limit_speed_premium_value'] != $v['limit_speed_value'] ||
			$v['limit_speed_embed_option'] != $v['limit_speed_option'] || $v['limit_speed_embed_value'] != $v['limit_speed_value'])
		{
			$speed_limit_string .= ' (';
			if ($v['limit_speed_guests_option'] != $v['limit_speed_option'] || $v['limit_speed_guests_value'] != $v['limit_speed_value'])
			{
				if ($v['limit_speed_guests_option'] == 0)
				{
					$speed_limit_string .= $lang['common']['undefined'];
				} elseif ($v['limit_speed_guests_option'] == 1)
				{
					$speed_limit_string .= $v['limit_speed_guests_value'] . ' ' . $lang['settings']['format_video_field_limit_speed_option_fixed_kbps'];
				} elseif ($v['limit_speed_guests_option'] == 2)
				{
					$speed_limit_string .= 'x' . $v['limit_speed_guests_value'];
				}
				$speed_limit_string .= ', ';
			}
			if ($v['limit_speed_standard_option'] != $v['limit_speed_option'] || $v['limit_speed_standard_value'] != $v['limit_speed_value'])
			{
				if ($v['limit_speed_standard_option'] == 0)
				{
					$speed_limit_string .= $lang['common']['undefined'];
				} elseif ($v['limit_speed_standard_option'] == 1)
				{
					$speed_limit_string .= $v['limit_speed_standard_value'] . ' ' . $lang['settings']['format_video_field_limit_speed_option_fixed_kbps'];
				} elseif ($v['limit_speed_standard_option'] == 2)
				{
					$speed_limit_string .= 'x' . $v['limit_speed_standard_value'];
				}
				$speed_limit_string .= ', ';
			}
			if ($v['limit_speed_premium_option'] != $v['limit_speed_option'] || $v['limit_speed_premium_value'] != $v['limit_speed_value'])
			{
				if ($v['limit_speed_premium_option'] == 0)
				{
					$speed_limit_string .= $lang['common']['undefined'];
				} elseif ($v['limit_speed_premium_option'] == 1)
				{
					$speed_limit_string .= $v['limit_speed_premium_value'] . ' ' . $lang['settings']['format_video_field_limit_speed_option_fixed_kbps'];
				} elseif ($v['limit_speed_premium_option'] == 2)
				{
					$speed_limit_string .= 'x' . $v['limit_speed_premium_value'];
				}
				$speed_limit_string .= ', ';
			}
			if ($v['limit_speed_embed_option'] != $v['limit_speed_option'] || $v['limit_speed_embed_value'] != $v['limit_speed_value'])
			{
				if ($v['limit_speed_embed_option'] == 0)
				{
					$speed_limit_string .= $lang['common']['undefined'];
				} elseif ($v['limit_speed_embed_option'] == 1)
				{
					$speed_limit_string .= $v['limit_speed_embed_value'] . ' ' . $lang['settings']['format_video_field_limit_speed_option_fixed_kbps'];
				} elseif ($v['limit_speed_embed_option'] == 2)
				{
					$speed_limit_string .= 'x' . $v['limit_speed_embed_value'];
				}
				$speed_limit_string .= ', ';
			}
			$speed_limit_string = trim($speed_limit_string, ' ,') . ')';
		}
		$data[$index][$k]['limit_speed_value'] = $speed_limit_string;

		if ($v['is_timeline_enabled'] == 1)
		{
			if ($v['timeline_option'] == 1)
			{
				$data[$index][$k]['is_timeline_enabled'] = 'x' . $v['timeline_amount'];
			} else
			{
				$data[$index][$k]['is_timeline_enabled'] = $v['timeline_interval'] . $lang['common']['second_truncated'];
			}
		} else
		{
			$data[$index][$k]['is_timeline_enabled'] = '';
		}

		if ($v['status_id'] == 2)
		{
			if ($v['is_conditional'] == 1)
			{
				$data[$index][$k]['status_id'] = 9;
			}
		} elseif ($v['status_id'] == 3)
		{
			$data[$index][$k]['is_editing_forbidden'] = 1;

			$has_task = false;
			foreach ($formats_deleting_tasks as $task)
			{
				$task_data = @unserialize($task['data']);
				if ($task_data['format_postfix'] == $v['postfix'])
				{
					$data[$index][$k]['pc_complete'] = intval(@file_get_contents("$config[project_path]/admin/data/engine/tasks/$task[task_id].dat")) . '%';
					$has_task = true;
					break;
				}
			}
			if (!$has_task)
			{
				$data[$index][$k]['status_id'] = 4;
				$data[$index][$k]['is_error'] = 1;
			}
		} elseif ($v['status_id'] == 4)
		{
			$data[$index][$k]['is_editing_forbidden'] = 1;
			$data[$index][$k]['is_error'] = 1;
		}

		if ($v['is_use_as_source'] == 1)
		{
			$data[$index][$k]['source_text'] = '(' . $lang['settings']['format_video_field_use_as_source'] . ')';
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$list_format_groups = mr2array(sql("select *, (select count(*) from $config[tables_prefix]videos where format_video_group_id=$config[tables_prefix]formats_videos_groups.format_video_group_id and load_type_id=1 and status_id in (0, 1)) as videos_count from $config[tables_prefix]formats_videos_groups"));

$smarty = new mysmarty();
$smarty->assign('allowed_formats', str_replace(',', ', ', "$config[video_allowed_ext],gif"));
$smarty->assign('list_countries', $list_countries);
$smarty->assign('list_format_groups', $list_format_groups);

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('options', $options);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_count);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['format_video_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['format_video_add']);
} elseif ($_REQUEST['action'] == 'change_group')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['format_video_group_edit']));
} elseif ($_REQUEST['action'] == 'add_new_group')
{
	$smarty->assign('page_title', $lang['settings']['format_video_group_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_formats_videos_list']);
}

$smarty->display("layout.tpl");

function check_format_usage_in_player($postfix, $data_file): bool
{
	if (is_file($data_file))
	{
		$player_data = @unserialize(file_get_contents($data_file));
		if (is_array($player_data))
		{
			foreach ($player_data['formats'] as $group)
			{
				foreach ($group as $format)
				{
					if ($format['type'] == $postfix)
					{
						return true;
					}
				}
			}
		}
	}

	return false;
}

function update_format_data()
{
	global $config;

	$data = mr2array(sql("select title,postfix,format_video_group_id,access_level_id,is_hotlink_protection_disabled,is_download_enabled,download_order,limit_speed_option,limit_speed_value,limit_speed_guests_option,limit_speed_guests_value,limit_speed_standard_option,limit_speed_standard_value,limit_speed_premium_option,limit_speed_premium_value,limit_speed_embed_option,limit_speed_embed_value,limit_speed_countries,timeline_directory,limit_total_duration,limit_number_parts from $config[tables_prefix]formats_videos order by format_video_id asc"));
	if (array_cnt($data) == 0)
	{
		return;
	}

	file_put_contents("$config[project_path]/admin/data/system/formats_videos.dat", serialize($data), LOCK_EX);
}
