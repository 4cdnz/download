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

grid_presets_start($grid_presets, $page_name, 'stats_users');

$list_status_values = array(
	0 => $lang['stats']['users_field_status_disabled'],
	1 => $lang['stats']['users_field_status_not_confirmed'],
	2 => $lang['stats']['users_field_status_active'],
	3 => $lang['stats']['users_field_status_premium'],
	4 => $lang['stats']['users_field_status_anonymous'],
	5 => $lang['stats']['users_field_status_generated'],
	6 => $lang['stats']['users_field_status_webmaster'],
);

$table_fields = array();
$table_fields[] = array('id' => 'user',                   'title' => $lang['stats']['users_field_user'],              'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user' => 'user']);
$table_fields[] = array('id' => 'status_id',              'title' => $lang['stats']['users_field_status'],            'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'filter_ids' => ['se_status_id' => 'status_id']);
$table_fields[] = array('id' => 'reseller_code',          'title' => $lang['stats']['users_field_reseller_code'],     'is_default' => 0, 'type' => 'text', 'filter_ids' => ['se_reseller_code' => 'reseller_code']);
$table_fields[] = array('id' => 'added_date',             'title' => $lang['stats']['users_field_added_date'],        'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'video_watched',          'title' => $lang['stats']['users_field_videos_watched'],    'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'ratings_videos_count',   'title' => $lang['stats']['users_field_videos_rated'],      'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'total_videos_count',     'title' => $lang['stats']['users_field_videos_created'],    'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'favourite_videos_count', 'title' => $lang['stats']['users_field_videos_favourited'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'album_watched',          'title' => $lang['stats']['users_field_albums_watched'],    'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'ratings_albums_count',   'title' => $lang['stats']['users_field_albums_rated'],      'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'total_albums_count',     'title' => $lang['stats']['users_field_albums_created'],    'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'favourite_albums_count', 'title' => $lang['stats']['users_field_albums_favourited'], 'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'total_posts_count',      'title' => $lang['stats']['users_field_posts_created'],     'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'comments_total_count',   'title' => $lang['stats']['users_field_comments'],          'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'logins_count',           'title' => $lang['stats']['users_field_logins'],            'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'avg_sess_duration',      'title' => $lang['stats']['users_field_sess_duration'],     'is_default' => 1, 'type' => 'time',   'ifdisable_zero' => 1);

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

$table_name = "$config[tables_prefix]users";
$table_key_name = "user_id";

$table_selector = "$table_name.*, $table_name.username as user, $table_name.status_id as user_status_id";
$table_summary_selector = "sum($table_name.video_watched) as video_watched, sum($table_name.ratings_videos_count) as ratings_videos_count, sum($table_name.total_videos_count) as total_videos_count, sum($table_name.favourite_videos_count) as favourite_videos_count, sum($table_name.album_watched) as album_watched, sum($table_name.ratings_albums_count) as ratings_albums_count, sum($table_name.total_albums_count) as total_albums_count, sum($table_name.favourite_albums_count) as favourite_albums_count, sum($table_name.comments_total_count) as comments_total_count, sum($table_name.logins_count) as logins_count, floor(avg($table_name.avg_sess_duration)) as avg_sess_duration, '0000-00-00 00:00:00' as added_date";
$table_summary_field_name = "user";
$table_projector = $table_name;

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
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_reseller_code'] = '';
	$_SESSION['save'][$page_name]['se_period_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_reseller_code']))
	{
		$_SESSION['save'][$page_name]['se_reseller_code'] = trim($_GET['se_reseller_code']);
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

$_SESSION['save'][$page_name]['se_user_id'] = 0;
if ($_SESSION['save'][$page_name]['se_user'] != '')
{
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_SESSION['save'][$page_name]['se_user']));
	if ($user_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$q = sql_escape($_SESSION['save'][$page_name]['se_user']);
		$where .= " and $table_name.username='$q'";
	}
	$_SESSION['save'][$page_name]['se_user_id'] = $user_id;
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_status_id'] != '')
{
	$where .= " and $table_name.status_id=" . intval($_SESSION['save'][$page_name]['se_status_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_reseller_code'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_reseller_code']);
	$where .= " and $table_name.reseller_code='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_from'] <> "")
{
	$where .= " and $table_name.added_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and $table_name.added_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'] . ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'stats_users');

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_projector $where"));

if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

if ($table_summary_selector != '' && $table_summary_field_name != '')
{
	$total[0] = mr2array_single(sql("select $table_summary_selector from $table_projector $where limit 1"));
	$total[0][$table_summary_field_name] = $lang['common']['total'];

	if ($total_num > 1)
	{
		$summary_data = $total[0];
		$summary_count = $total_num;

		foreach ($summary_data as $k => $v)
		{
			$total[1][$k] = $v;
			foreach ($table_fields as $table_field)
			{
				if ($table_field['id'] == $k && in_array($table_field['type'], ['number', 'currency', 'duration', 'traffic']))
				{
					if ($summary_count > 0)
					{
						$total[1][$k] /= $summary_count;
					} else
					{
						$total[1][$k] = 0;
					}
				}
			}
		}
		$total[1][$table_summary_field_name] = $lang['common']['average'];
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();

$smarty->assign('data', $data);
$smarty->assign('total', $total);
$smarty->assign('average', $total[1]);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_users']);

$smarty->display("layout.tpl");
