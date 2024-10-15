<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions_admin.php');
require_once('include/functions_servers.php');
require_once('include/functions.php');
require_once('include/check_access.php');

// =====================================================================================================================
// initialization
// =====================================================================================================================

grid_presets_start($grid_presets, $page_name, 'log_background_tasks');

$list_status_values = array(
	2 => $lang['settings']['background_task_log_field_status_error'],
	3 => $lang['settings']['background_task_log_field_status_completed'],
	4 => $lang['settings']['background_task_log_field_status_cancelled'],
);

$list_type_values = array(
	1  => $lang['settings']['common_background_task_type_new_video'],
	2  => $lang['settings']['common_background_task_type_delete_video'],
	3  => $lang['settings']['common_background_task_type_upload_video_format_file'],
	4  => $lang['settings']['common_background_task_type_create_video_format_file'],
	5  => $lang['settings']['common_background_task_type_delete_video_format_file'],
	6  => $lang['settings']['common_background_task_type_delete_video_format'],
	24 => $lang['settings']['common_background_task_type_create_overview_screenshots'],
	28 => $lang['settings']['common_background_task_type_delete_overview_screenshots'],
	8  => $lang['settings']['common_background_task_type_create_timeline_screenshots'],
	20 => $lang['settings']['common_background_task_type_delete_timeline_screenshots'],
	7  => $lang['settings']['common_background_task_type_create_screenshot_format'],
	9  => $lang['settings']['common_background_task_type_delete_screenshot_format'],
	16 => $lang['settings']['common_background_task_type_create_screenshots_zip'],
	17 => $lang['settings']['common_background_task_type_delete_screenshots_zip'],
	29 => $lang['settings']['common_background_task_type_recreate_screenshot_formats'],
	10 => $lang['settings']['common_background_task_type_new_album'],
	11 => $lang['settings']['common_background_task_type_delete_album'],
	12 => $lang['settings']['common_background_task_type_create_album_format'],
	13 => $lang['settings']['common_background_task_type_delete_album_format'],
	14 => $lang['settings']['common_background_task_type_upload_album_images'],
	18 => $lang['settings']['common_background_task_type_create_images_zip'],
	19 => $lang['settings']['common_background_task_type_delete_images_zip'],
	22 => $lang['settings']['common_background_task_type_album_images_manipulation'],
	30 => $lang['settings']['common_background_task_type_recreate_album_formats'],
	15 => $lang['settings']['common_background_task_type_change_storage_group_video'],
	23 => $lang['settings']['common_background_task_type_change_storage_group_album'],
	27 => $lang['settings']['common_background_task_type_sync_storage_server'],
	31 => $lang['settings']['common_background_task_type_recreate_player_preview'],
	26 => $lang['settings']['common_background_task_type_update_resolution_type'],
	50 => $lang['settings']['common_background_task_type_videos_import'],
	51 => $lang['settings']['common_background_task_type_albums_import'],
	52 => $lang['settings']['common_background_task_type_videos_mass_edit'],
	53 => $lang['settings']['common_background_task_type_albums_mass_edit'],
);

$list_error_code_values = array(
	1 => $lang['settings']['common_background_task_error_codes']['1'],
	2 => $lang['settings']['common_background_task_error_codes']['2'],
	3 => $lang['settings']['common_background_task_error_codes']['3'],
	4 => $lang['settings']['common_background_task_error_codes']['4'],
	5 => $lang['settings']['common_background_task_error_codes']['5'],
	6 => $lang['settings']['common_background_task_error_codes']['6'],
	7 => $lang['settings']['common_background_task_error_codes']['7'],
	8 => $lang['settings']['common_background_task_error_codes']['8'],
	9 => $lang['settings']['common_background_task_error_codes']['9'],
);

$table_fields = array();
$table_fields[] = array('id' => 'task_id',            'title' => $lang['settings']['background_task_log_field_id'],         'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'status_id',          'title' => $lang['settings']['background_task_log_field_status'],     'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'append' => array(2 => 'error_code'), 'is_nowrap' => 1, 'ifhighlight' => 'is_error', 'filter_ids' => ['se_status_id' => 'status_id']);
$table_fields[] = array('id' => 'error_code',         'title' => $lang['settings']['background_task_log_field_error_code'], 'is_default' => 1, 'type' => 'choice', 'values' => $list_error_code_values, 'ifhighlight' => 'is_error', 'filter_ids' => ['se_error_code' => 'error_code']);
$table_fields[] = array('id' => 'message',            'title' => $lang['settings']['background_task_log_field_message'],    'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'type_id',            'title' => $lang['settings']['background_task_log_field_type'],       'is_default' => 1, 'type' => 'choice', 'values' => $list_type_values, 'append' => array(3 => 'format_postfix', 4 => 'format_postfix', 5 => 'format_postfix', 6 => 'format_postfix', 7 => 'format_size', 8 => 'format_postfix', 9 => 'format_size', 12 => 'format_size', 13 => 'format_size', 16 => 'format_size', 17 => 'format_size', 18 => 'format_size', 19 => 'format_size', 20 => 'format_postfix'), 'filter_ids' => ['se_type_id' => 'type_id']);
$table_fields[] = array('id' => 'server',             'title' => $lang['settings']['background_task_log_field_server'],     'is_default' => 1, 'type' => 'refid', 'link' => $config['installation_type'] >= 3 ? 'servers_conversion.php?action=change&item_id=%id%' : 'servers_conversion_basic.php', 'link_id' => 'server_id', 'permission' => 'system|servers', 'filter_ids' => ['se_server_id' => 'server_id']);
$table_fields[] = array('id' => 'object',             'title' => $lang['settings']['background_task_log_field_object'],     'is_default' => 1, 'type' => 'object', 'filter_ids' => ['se_object_type_id' => 'object_type_id', 'se_object_id' => 'object_id']);
$table_fields[] = array('id' => 'start_date',         'title' => $lang['settings']['background_task_log_field_start_date'], 'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'end_date',           'title' => $lang['settings']['background_task_log_field_end_date'],   'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'effective_duration', 'title' => $lang['settings']['background_task_log_field_duration'],   'is_default' => 1, 'type' => 'duration');

$sort_def_field = "task_id";
$sort_def_direction = "desc";
$sort_array = array();
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

$table_name = "$config[tables_prefix]background_tasks_history";
$table_key_name = "task_id";

$table_selector = "$table_name.*, $config[tables_prefix]admin_conversion_servers.title as server, case when video_id>0 then video_id when album_id>0 then album_id end as object_id, case when video_id>0 then video_id when album_id>0 then album_id end as object, case when video_id>0 then 1 when album_id>0 then 2 end as object_type_id";
$table_projector = "$table_name left join $config[tables_prefix]admin_conversion_servers on $table_name.server_id=$config[tables_prefix]admin_conversion_servers.server_id";

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
	$_SESSION['save'][$page_name]['se_status_id'] = "";
	$_SESSION['save'][$page_name]['se_type_id'] = "";
	$_SESSION['save'][$page_name]['se_error_code'] = "";
	$_SESSION['save'][$page_name]['se_server_id'] = "";
	$_SESSION['save'][$page_name]['se_object_type_id'] = "";
	$_SESSION['save'][$page_name]['se_object_id'] = "";
	$_SESSION['save'][$page_name]['se_period_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_type_id']))
	{
		$_SESSION['save'][$page_name]['se_type_id'] = intval($_GET['se_type_id']);
	}
	if (isset($_GET['se_error_code']))
	{
		$_SESSION['save'][$page_name]['se_error_code'] = intval($_GET['se_error_code']);
	}
	if (isset($_GET['se_server_id']))
	{
		$_SESSION['save'][$page_name]['se_server_id'] = intval($_GET['se_server_id']);
	}
	if (isset($_GET['se_object_type_id']))
	{
		$_SESSION['save'][$page_name]['se_object_type_id'] = trim($_GET['se_object_type_id']);
	}
	if (isset($_GET['se_object_id']))
	{
		$_SESSION['save'][$page_name]['se_object_id'] = trim($_GET['se_object_id']);
	}
	if (isset($_GET['se_period_id']))
	{
		$_SESSION['save'][$page_name]['se_period_id'] = trim($_GET['se_period_id']);
		switch ($_SESSION['save'][$page_name]['se_period_id'])
		{
			case 'today':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d');
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 'yesterday':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d', time() - 86400);
				break;
			case 'days7':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 6);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 'days30':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 30);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 'current_month':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1');
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 'prev_month':
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1', strtotime(date('Y-m-1 00:00:00')) - 86400);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d', strtotime(date('Y-m-1 00:00:00')) - 86400);
				break;
			case 'custom':
				if (isset($_GET['se_date_from']))
				{
					$_SESSION['save'][$page_name]['se_date_from'] = strtotime($_GET['se_date_from']) !== false ? date('Y-m-d', strtotime($_GET['se_date_from'])) : '';
				}
				if (isset($_GET['se_date_to']))
				{
					$_SESSION['save'][$page_name]['se_date_to'] = strtotime($_GET['se_date_to']) !== false ? date('Y-m-d', strtotime($_GET['se_date_to'])) : '';
				}
				break;
			default:
				$_SESSION['save'][$page_name]['se_date_from'] = '';
				$_SESSION['save'][$page_name]['se_date_to'] = '';
				break;
		}
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_status_id'] == '2')
{
	$where .= " and $table_name.status_id=2";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '3')
{
	$where .= " and $table_name.status_id=3";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '4')
{
	$where .= " and $table_name.status_id=4";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_type_id'] > 0)
{
	$where .= " and $table_name.type_id=" . intval($_SESSION['save'][$page_name]['se_type_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_error_code'] > 0)
{
	$where .= " and $table_name.error_code=" . intval($_SESSION['save'][$page_name]['se_error_code']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_server_id'] > 0)
{
	$where .= " and $table_name.server_id=" . intval($_SESSION['save'][$page_name]['se_server_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_object_type_id'] > 0)
{
	switch ($_SESSION['save'][$page_name]['se_object_type_id'])
	{
		case 1:
			$where .= " and $table_name.video_id>0";
			break;
		case 2:
			$where .= " and $table_name.album_id>0";
			break;
		default:
			$where .= " and 1=0";
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_object_id'] != '')
{
	switch ($_SESSION['save'][$page_name]['se_object_type_id'])
	{
		case 1:
			$where .= " and $table_name.video_id=" . intval($_SESSION['save'][$page_name]['se_object_id']);
			break;
		case 2:
			$where .= " and $table_name.album_id=" . intval($_SESSION['save'][$page_name]['se_object_id']);
			break;
		default:
			$where .= " and 0=1";
			break;
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_from'] <> "")
{
	$where .= " and $table_name.start_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and $table_name.start_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'object')
{
	$sort_by = 'video_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', album_id';
} elseif ($sort_by == 'server')
{
	$sort_by = "$config[tables_prefix]admin_conversion_servers.title";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'log_background_tasks');

// =====================================================================================================================
// additional actions
// =====================================================================================================================

if ($_REQUEST['action'] == 'task_log')
{
	header('Content-Type: text/plain; charset=utf-8');

	$item_id = intval($_REQUEST['item_id']);
	if ($item_id > 0)
	{
		if (isset($_REQUEST['download']))
		{
			header("Content-Disposition: attachment; filename=\"$item_id.txt\"");
		} else
		{
			header("Content-Disposition: inline; filename=\"$item_id.txt\"");
		}

		$dir_path = get_dir_by_id($item_id);
		if (is_file("$config[project_path]/admin/logs/tasks/$dir_path.tar.gz"))
		{
			unset($list);
			exec("tar --list --file=$config[project_path]/admin/logs/tasks/$dir_path.tar.gz", $list);
			$list = array_flip($list);
			if (isset($list["$item_id.txt"]))
			{
				unset($temp);
				exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/tasks/$dir_path.tar.gz $item_id.txt", $temp);
				echo "-------------------------------------- {$item_id}.txt\n\n" . trim(implode("\n", $temp)) . "\n\n";

				for ($k = 1; $k < 10000; $k++)
				{
					if (isset($list["{$item_id}_$k.txt"]))
					{
						unset($temp);
						exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/tasks/$dir_path.tar.gz {$item_id}_$k.txt", $temp);
						echo "-------------------------------------- {$item_id}_$k.txt\n\n" . trim(implode("\n", $temp)) . "\n\n";
					} else
					{
						break;
					}
				}
			}
		}

		if (is_file("$config[project_path]/admin/logs/tasks/$item_id.txt"))
		{
			echo "-------------------------------------- {$item_id}.txt\n\n" . trim(file_get_contents("$config[project_path]/admin/logs/tasks/$item_id.txt")) . "\n\n";

			for ($k = 1; $k < 10000; $k++)
			{
				if (is_file("$config[project_path]/admin/logs/tasks/{$item_id}_$k.txt"))
				{
					echo "-------------------------------------- {$item_id}_$k.txt\n\n" . trim(file_get_contents("$config[project_path]/admin/logs/tasks/{$item_id}_$k.txt")) . "\n\n";
				} else
				{
					break;
				}
			}
		}
	}
	die;
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change')
{
	$_POST = mr2array_single(sql_pr("select $table_selector from $table_projector where $table_key_name=?", intval($_GET['item_id'])));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	$item_id = intval($_POST['task_id']);

	$task_data = unserialize($_POST['data']);
	if (is_array($task_data) && $task_data['format_postfix'] <> '')
	{
		$_POST['format_postfix'] = $task_data['format_postfix'];
	}
	if (is_array($task_data) && $task_data['format_size'] <> '')
	{
		$_POST['format_size'] = $task_data['format_size'];
	}
	$_POST['duration'] = durationToHumanString($_POST['effective_duration']);

	$log = '';
	$dir_path = get_dir_by_id($item_id);
	if (is_file("$config[project_path]/admin/logs/tasks/$dir_path.tar.gz"))
	{
		unset($list);
		exec("tar --list --file=$config[project_path]/admin/logs/tasks/$dir_path.tar.gz", $list);
		$list = array_flip($list);
		if (isset($list["$item_id.txt"]))
		{
			unset($temp);
			exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/tasks/$dir_path.tar.gz $item_id.txt", $temp);
			$log .= trim(implode("\n", $temp)) . "\n\n";

			for ($k = 1; $k < 10000; $k++)
			{
				if (isset($list["{$item_id}_$k.txt"]))
				{
					unset($temp);
					exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/tasks/$dir_path.tar.gz {$item_id}_$k.txt", $temp);
					$log .= trim(implode("\n", $temp)) . "\n\n";
				} else
				{
					break;
				}
			}
		}
	}

	if (is_file("$config[project_path]/admin/logs/tasks/$item_id.txt"))
	{
		$log .= trim(file_get_contents("$config[project_path]/admin/logs/tasks/$item_id.txt")) . "\n\n";
	}

	unset($temp);
	preg_match_all("/^\[([^]]+)].*\[(PH-[^]]+)]/im", $log, $temp);
	$phases = array();
	if (array_cnt($temp[0]) > 0)
	{
		$prev_phase_id = '';
		for ($i = 0; $i < array_cnt($temp[0]); $i++)
		{
			$current_phase_id = $temp[2][$i];
			$current_phase_info = '';
			if (strpos($current_phase_id, ':') !== false)
			{
				[$current_phase_id, $current_phase_info] = explode(':', $current_phase_id, 2);
			}
			$current_phase_id = str_replace('PH-', '', $current_phase_id);
			if ($current_phase_id == 'P')
			{
				$phases = array();
			}
			if ($prev_phase_id == 'E')
			{
				$phases = array();
			}
			$current_phase_start_time = $temp[1][$i];
			$current_phase_id_components = explode('-', $current_phase_id);
			$current_phase = array('id' => $current_phase_id, 'info' => $current_phase_info, 'start_time' => $current_phase_start_time, 'duration' => '0:00', 'level' => array_cnt($current_phase_id_components) - 1);
			$phases[] = $current_phase;
			$prev_phase_id = $current_phase_id;
		}
	}
	for ($i = 0; $i < array_cnt($phases); $i++)
	{
		for ($j = $i + 1; $j < array_cnt($phases); $j++)
		{
			if ($phases[$j]['level'] <= $phases[$i]['level'])
			{
				$time1 = strtotime($phases[$i]['start_time']);
				$time2 = strtotime($phases[$j]['start_time']);
				$phases[$i]['duration'] = durationToHumanString($time2 - $time1);
				break;
			}
		}
		$phase_description = $lang['settings']['background_task_log_field_details_type'][$phases[$i]['id']];
		if (strpos($phase_description, '%1%') !== false)
		{
			$phase_description = str_replace('%1%', $phases[$i]['info'], $phase_description);
		}
		$phases[$i]['description'] = $phase_description;
	}
	$_POST['phases'] = $phases;
}

// =====================================================================================================================
// list items
// =====================================================================================================================

if ($_GET['action'] == '')
{
	$total_num = mr2number(sql("select count(*) from $table_projector $where"));
	if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
	{
		$_SESSION['save'][$page_name]['from'] = 0;
	}
	$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

	foreach ($data as $k => $v)
	{
		if ($v['error_code'] == 0)
		{
			$data[$k]['error_code'] = '';
		}
		if ($v['status_id'] == 2)
		{
			$data[$k]['is_error'] = 1;
		}

		$task_data = unserialize($v['data']);
		if (is_array($task_data) && $task_data['format_postfix'] <> '')
		{
			$data[$k]['format_postfix'] = $task_data['format_postfix'];
		}
		if (is_array($task_data) && $task_data['format_size'] <> '')
		{
			$data[$k]['format_size'] = $task_data['format_size'];
		}

		if ($v['video_id'])
		{
			$data[$k]['object_type_id'] = 1;
			$data[$k]['object_id'] = $v['video_id'];
		} elseif ($v['album_id'])
		{
			$data[$k]['object_type_id'] = 2;
			$data[$k]['object_id'] = $v['album_id'];
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();

$smarty->assign('list_status_values', $list_status_values);
$smarty->assign('list_type_values', $list_type_values);
$smarty->assign('list_error_code_values', $list_error_code_values);
$smarty->assign('list_conversion_servers', mr2array(sql_pr("select * from $config[tables_prefix]admin_conversion_servers")));

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

$smarty->assign('page_title', $lang['settings']['submenu_option_background_tasks_log']);

if ($_GET['action'] == 'change')
{
	$smarty->assign('page_title', $lang['settings']['background_task_log_view']);
	$smarty->assign('supports_popups', 1);
}

$smarty->display("layout.tpl");
