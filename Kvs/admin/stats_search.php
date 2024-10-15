<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/functions_admin.php';
require_once 'include/check_access.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

grid_presets_start($grid_presets, $page_name, 'stats_search');

$list_status_values = array(
	0 => $lang['stats']['search_field_status_disabled'],
	1 => $lang['stats']['search_field_status_active'],
);

$table_fields = array();
$table_fields[] = array('id' => 'search_id',            'title' => $lang['stats']['search_field_id'],                   'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'query',                'title' => $lang['stats']['search_field_query'],                'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'status_id',            'title' => $lang['stats']['search_field_status'],               'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'filter_ids' => ['se_status_id' => 'status_id']);
$table_fields[] = array('id' => 'amount',               'title' => $lang['stats']['search_field_total'],                'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1,   'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'query_results_videos', 'title' => $lang['stats']['search_field_query_results_videos'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1,   'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'query_results_albums', 'title' => $lang['stats']['search_field_query_results_albums'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1,   'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'query_results_total',  'title' => $lang['stats']['search_field_query_results_total'],  'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1,   'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'added_date',           'title' => $lang['stats']['search_field_added_date'],           'is_default' => 1, 'type' => 'date',   'show_in_sidebar' => 1);

$sort_def_field = "search_id";
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
	if ($field['show_in_sidebar'] == 1)
	{
		$sidebar_fields[] = $field;
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

$table_name = "$config[tables_prefix_multi]stats_search";
$table_key_name = 'search_id';

$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));

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
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_added_by'] = '';
	$_SESSION['save'][$page_name]['se_data'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_added_by']))
	{
		$_SESSION['save'][$page_name]['se_added_by'] = intval($_GET['se_added_by']);
	}
	if (isset($_GET['se_data']))
	{
		$_SESSION['save'][$page_name]['se_data'] = trim($_GET['se_data']);
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and (query like '%$q%') ";
}

if ($_SESSION['save'][$page_name]['se_status_id'] == '0')
{
	$where .= " and $table_name.status_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '1')
{
	$where .= " and $table_name.status_id=1";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_added_by'] == 1)
{
	$where .= " and is_manual=1 ";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_added_by'] == 2)
{
	$where .= " and is_manual=0 ";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_data'] != '')
{
	switch ($_SESSION['save'][$page_name]['se_data'])
	{
		case 'no/videos':
		case 'no/albums':
		case 'no/total':
			$column_name = 'query_results_' . substr($_SESSION['save'][$page_name]['se_data'], 3);
			$where .= " and $column_name=0 ";
			break;
		case 'have/videos':
		case 'have/albums':
		case 'have/total':
			$column_name = 'query_results_' . substr($_SESSION['save'][$page_name]['se_data'], 5);
			$where .= " and $column_name>0 ";
			break;
	}
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'] . ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'stats_search');

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if ($_POST['action'] == 'add_new_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	validate_field('empty', $_POST['queries'], $lang['stats']['search_field_queries']);

	if (!is_array($errors))
	{
		$queries = explode("\n", $_POST['queries']);
		foreach ($queries as $query)
		{
			$query = trim($query);
			if (mr2number(sql_pr("select count(*) from $table_name where query_md5=md5(lower(?))", $query)) == 0)
			{
				sql_pr("insert into $table_name set is_manual=1, query=?, query_md5=md5(lower(query)), query_length=char_length(query), added_date=?", $query, date("Y-m-d H:i:s"));
			}
		}
		$_SESSION['messages'][] = $lang['common']['success_message_added'];
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
} elseif ($_POST['action'] == 'change_complete')
{
	$item_id = intval($_POST['item_id']);

	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	validate_field('uniq', $_POST['query'], $lang['stats']['search_field_query'], array('field_name_in_base' => 'query'));

	if (!is_array($errors))
	{
		sql_pr("update $table_name set `query`=?, query_md5=md5(lower(`query`)), query_length=char_length(`query`), status_id=? where $table_key_name=?",
			$_POST['query'], intval($_POST['status_id']), $item_id
		);

		$_SESSION['messages'][] = $lang['common']['success_message_modified'];
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
		$md5_list = [];
		foreach ($_REQUEST['row_select'] as $query_md5)
		{
			$md5_list[] = "'" . sql_escape($query_md5) . "'";
		}
		if ($_REQUEST['batch_action'] == 'delete')
		{
			sql_pr("delete from $table_name where query_md5 in (" . implode(',', $md5_list) . ')');
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'deactivate')
		{
			sql("update $table_name set status_id=0, is_manual=1 where query_md5 in (" . implode(',', $md5_list) . ')');
			$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'activate')
		{
			sql("update $table_name set status_id=1 where query_md5 in (" . implode(',', $md5_list) . ')');
			$_SESSION['messages'][] = $lang['common']['success_message_activated'];
			return_ajax_success($page_name);
		}
	}

	$errors[] = get_aa_error('unexpected_error');
	return_ajax_errors($errors);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change')
{
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_GET['item_id'])));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	if ($website_ui_data['WEBSITE_LINK_PATTERN_SEARCH'] <> '')
	{
		$query = $_POST['query'];
		$query = str_replace(array("&", "?", "/", " "), array("%26", "%3F", "%2F", "-"), $query);
		$_POST['website_link'] = "$config[project_url]/" . str_replace("%QUERY%", $query, $website_ui_data['WEBSITE_LINK_PATTERN_SEARCH']);
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name $where"));

if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select * from $table_name $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

foreach ($data as $k => $v)
{
	if ($website_ui_data['WEBSITE_LINK_PATTERN_SEARCH'] <> '')
	{
		$query = $data[$k]['query'];
		$query = str_replace(array("&", "?", "/", " "), array("%26", "%3F", "%2F", "-"), $query);
		$data[$k]['website_link'] = "$config[project_url]/" . str_replace("%QUERY%", $query, $website_ui_data['WEBSITE_LINK_PATTERN_SEARCH']);
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['query'], $lang['stats']['search_edit']));
	$smarty->assign('sidebar_fields', $sidebar_fields);
} elseif ($_GET['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['stats']['search_add']);
} else
{
	$smarty->assign('page_title', $lang['stats']['submenu_option_stats_search']);
}

$smarty->display("layout.tpl");
