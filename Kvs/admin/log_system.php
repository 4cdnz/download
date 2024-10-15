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

// =====================================================================================================================
// initialization
// =====================================================================================================================

grid_presets_start($grid_presets, $page_name, 'log_system');

$list_level_values = [
		1 => $lang['settings']['system_log_field_level_debug'],
		2 => $lang['settings']['system_log_field_level_info'],
		3 => $lang['settings']['system_log_field_level_warning'],
		4 => $lang['settings']['system_log_field_level_error']
];

$list_satellite_values = [];
if ($config['is_clone_db'] != 'true')
{
	foreach (sql_pr("select multi_prefix, project_url from $config[tables_prefix]admin_satellites order by multi_prefix") as $satellite)
	{
		$list_satellite_values[$satellite['multi_prefix']] = str_replace('https://', '', str_replace('http://', '', str_replace('www.', '', $satellite['project_url'])));
	}
}

$table_fields[] = array('id' => 'event_level',   'title' => $lang['settings']['system_log_field_level'],      'is_default' => 1, 'type' => 'choice', 'values' => $list_level_values, 'ifhighlight' => 'is_error', 'ifwarn' => 'is_warning', 'ifdisable' => 'is_debug');
$table_fields[] = array('id' => 'event_code',    'title' => $lang['settings']['system_log_field_code'],       'is_default' => 1, 'type' => 'number');
$table_fields[] = array('id' => 'event_message', 'title' => $lang['settings']['system_log_field_message'],    'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'event_details', 'title' => $lang['settings']['system_log_field_details'],    'is_default' => 1, 'type' => 'longtext', 'no_truncate' => 1);
$table_fields[] = array('id' => 'event_trace',   'title' => $lang['settings']['system_log_field_trace'],      'is_default' => 0, 'type' => 'multitext');
$table_fields[] = array('id' => 'process_id',    'title' => $lang['settings']['system_log_field_pid'],        'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'process_name',  'title' => $lang['settings']['system_log_field_name'],       'is_default' => 0, 'type' => 'text');
if (array_cnt($list_satellite_values) > 0)
{
	$table_fields[] = array('id' => 'satellite_prefix', 'title' => $lang['settings']['system_log_field_satellite'],  'is_default' => 0, 'type' => 'choice', 'values' => $list_satellite_values, 'filter_ids' => ['se_satellite_prefix' => 'satellite_prefix']);
}
$table_fields[] = array('id' => 'added_date',    'title' => $lang['settings']['system_log_field_added_date'], 'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "added_date";
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

$search_fields = array();
$search_fields[] = array('id' => 'event_message', 'title' => $lang['settings']['system_log_field_message']);
$search_fields[] = array('id' => 'event_details', 'title' => $lang['settings']['system_log_field_details']);
$search_fields[] = array('id' => 'process_id',    'title' => $lang['settings']['system_log_field_pid']);
$search_fields[] = array('id' => 'process_name',  'title' => $lang['settings']['system_log_field_name']);

$table_name = "$config[tables_prefix]admin_system_log";

$table_selector = '*, case when event_level=4 then 1 else 0 end as is_error, case when event_level=3 then 1 else 0 end as is_warning, case when event_level<2 then 1 else 0 end as is_debug';

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
	$_SESSION['save'][$page_name]['se_satellite_prefix'] = '';
	$_SESSION['save'][$page_name]['se_period_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_satellite_prefix']))
	{
		$_SESSION['save'][$page_name]['se_satellite_prefix'] = trim($_GET['se_satellite_prefix']);
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
if ($config['is_clone_db'] == 'true')
{
	$where .= "and satellite_prefix='" . sql_escape($config['tables_prefix_multi']) . "'";
}

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where_search = '1=0';
	foreach ($search_fields as $search_field)
	{
		if (isset($_GET["se_text_$search_field[id]"]))
		{
			$_SESSION['save'][$page_name]["se_text_$search_field[id]"] = $_GET["se_text_$search_field[id]"];
		}
		if (intval($_SESSION['save'][$page_name]["se_text_$search_field[id]"]) == 1)
		{
			if ($search_field['id'] == 'process_id')
			{
				$where_search .= " or $table_name.process_id=" . intval($q);
			} else
			{
				$where_search .= " or $table_name.$search_field[id] like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

if ($_SESSION['save'][$page_name]['se_satellite_prefix'] != '')
{
	$satellite_prefix = $_SESSION['save'][$page_name]['se_satellite_prefix'];
	if ($_SESSION['save'][$page_name]['se_satellite_prefix'] == '_')
	{
		$satellite_prefix = '';
	}
	$where .= " and satellite_prefix='" . sql_escape($satellite_prefix) . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_from'] != '')
{
	$where .= " and added_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_date_to'] != '')
{
	$where .= " and added_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'added_date')
{
	$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'] . ', added_microtime';
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'log_system');

// =====================================================================================================================
// table actions
// =====================================================================================================================

// =====================================================================================================================
// view item
// =====================================================================================================================

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select $table_selector from $table_name $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

foreach ($data as &$item)
{
	$item['event_trace'] = str_replace("\n", '[kt|br]', $item['event_trace']);
}


// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('list_satellite_values', $list_satellite_values);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

$smarty->assign('page_title', $lang['settings']['submenu_option_system_log']);

$smarty->display("layout.tpl");
