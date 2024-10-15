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

grid_presets_start($grid_presets, $page_name, 'stats_users_donations');

if (isset($_GET['se_group_by']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = $_GET['se_group_by'];
}
if (!in_array($_SESSION['save'][$page_name]['se_group_by'], ['donator', 'user']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = "log";
}
$list_grouping = $_SESSION['save'][$page_name]['se_group_by'];

$list_status_values = array(
	0 => $lang['stats']['users_donations_field_status_disabled'],
	1 => $lang['stats']['users_donations_field_status_not_confirmed'],
	2 => $lang['stats']['users_donations_field_status_active'],
	3 => $lang['stats']['users_donations_field_status_premium'],
	4 => $lang['stats']['users_donations_field_status_anonymous'],
	5 => $lang['stats']['users_donations_field_status_generated'],
	6 => $lang['stats']['users_donations_field_status_webmaster'],
);

$table_fields = array();

if ($list_grouping == 'donator')
{
	$table_fields[] = array('id' => 'donator',        'title' => $lang['stats']['users_donations_field_donator'],        'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_donator' => 'donator']);
	$table_fields[] = array('id' => 'status_id',      'title' => $lang['stats']['users_donations_field_status'],         'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'filter_ids' => ['se_status_id' => 'status_id']);
	$table_fields[] = array('id' => 'reseller_code',  'title' => $lang['stats']['users_donations_field_reseller_code'],  'is_default' => 0, 'type' => 'text', 'filter_ids' => ['se_reseller_code' => 'reseller_code']);
	$table_fields[] = array('id' => 'donations',      'title' => $lang['stats']['users_donations_field_donations'],      'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'tokens_average', 'title' => $lang['stats']['users_donations_field_tokens_average'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'tokens_spent',   'title' => $lang['stats']['users_donations_field_tokens_spent'],   'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'tokens_revenue', 'title' => $lang['stats']['users_donations_field_tokens_revenue'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
} elseif ($list_grouping == 'user')
{
	$table_fields[] = array('id' => 'user',            'title' => $lang['stats']['users_donations_field_user'],            'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user' => 'user']);
	$table_fields[] = array('id' => 'status_id',       'title' => $lang['stats']['users_donations_field_status'],          'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'filter_ids' => ['se_status_id' => 'status_id']);
	$table_fields[] = array('id' => 'reseller_code',   'title' => $lang['stats']['users_donations_field_reseller_code'],   'is_default' => 0, 'type' => 'text', 'filter_ids' => ['se_reseller_code' => 'reseller_code']);
	$table_fields[] = array('id' => 'donations',       'title' => $lang['stats']['users_donations_field_donations'],       'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'tokens_average',  'title' => $lang['stats']['users_donations_field_tokens_average'],  'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'tokens_received', 'title' => $lang['stats']['users_donations_field_tokens_received'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'tokens_revenue',  'title' => $lang['stats']['users_donations_field_tokens_revenue'],  'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
} else
{
	$table_fields[] = array('id' => 'donator',         'title' => $lang['stats']['users_donations_field_donator'],         'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_donator' => 'donator']);
	$table_fields[] = array('id' => 'user',            'title' => $lang['stats']['users_donations_field_user'],            'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user' => 'user']);
	$table_fields[] = array('id' => 'tokens_spent',    'title' => $lang['stats']['users_donations_field_tokens_spent'],    'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'tokens_received', 'title' => $lang['stats']['users_donations_field_tokens_received'], 'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'tokens_revenue',  'title' => $lang['stats']['users_donations_field_tokens_revenue'],  'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'added_date',      'title' => $lang['stats']['users_donations_field_added_date'],      'is_default' => 1, 'type' => 'datetime');
}

if ($list_grouping == 'donator')
{
	$sort_def_field = 'donator';
	$sort_def_direction = 'desc';
} elseif ($list_grouping == 'user')
{
	$sort_def_field = 'user';
	$sort_def_direction = 'desc';
} else
{
	$sort_def_field = 'added_date';
	$sort_def_direction = 'desc';
}

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
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name][$list_grouping]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']]);
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
	$_SESSION['save'][$page_name][$list_grouping]['grid_columns_order'] = $_GET['grid_columns'];
}
if (is_array($_SESSION['save'][$page_name][$list_grouping]['grid_columns_order']))
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
	foreach ($_SESSION['save'][$page_name][$list_grouping]['grid_columns_order'] as $table_field_id)
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
		if (!in_array($table_field['id'], $_SESSION['save'][$page_name][$list_grouping]['grid_columns_order']) && $table_field['type'] != 'id')
		{
			$temp_table_fields[] = $table_field;
		}
	}
	$table_fields = $temp_table_fields;
}

$table_name = "$config[tables_prefix]log_donations_users";

if ($list_grouping == 'donator')
{
	$table_selector = "$table_name.donator_id, u1.username as donator, u1.status_id as donator_status_id, u1.status_id, u1.reseller_code, count(*) as donations, avg(tokens) as tokens_average, sum(tokens) as tokens_spent, sum(tokens_revenue) as tokens_revenue";
	$table_summary_selector = "count(*) as donations, avg(tokens) as tokens_average, sum(tokens) as tokens_spent, sum(tokens_revenue) as tokens_revenue";
	$table_summary_field_name = "donator";
	$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.donator_id";
	$table_group_by = "$table_name.donator_id";
} elseif ($list_grouping == 'user')
{
	$table_selector = "$table_name.user_id, u2.username as user, u2.status_id as user_status_id, u2.status_id, u2.reseller_code, count(*) as donations, avg(tokens) as tokens_average, sum(tokens-tokens_revenue) as tokens_received, sum(tokens_revenue) as tokens_revenue";
	$table_summary_selector = "count(*) as donations, avg(tokens) as tokens_average, sum(tokens-tokens_revenue) as tokens_received, sum(tokens_revenue) as tokens_revenue";
	$table_summary_field_name = "user";
	$table_projector = "$table_name left join $config[tables_prefix]users u2 on u2.user_id=$table_name.user_id";
	$table_group_by = "$table_name.user_id";
} else
{
	$table_selector = "$table_name.donator_id, u1.username as donator, u1.status_id as donator_status_id, $table_name.user_id, u2.username as user, u2.status_id as user_status_id, $table_name.tokens as tokens_spent, $table_name.tokens-$table_name.tokens_revenue as tokens_received, $table_name.tokens_revenue, $table_name.added_date";
	$table_summary_selector = "sum(tokens) as tokens_spent, sum(tokens-tokens_revenue) as tokens_received, sum(tokens_revenue) as tokens_revenue, '0000-00-00 00:00:00' as added_date";
	$table_summary_field_name = "donator";
	$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.donator_id left join $config[tables_prefix]users u2 on u2.user_id=$table_name.user_id";
	$table_group_by = "";
}

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (in_array($_GET['sort_by'], $sort_array))
{
	$_SESSION['save'][$page_name][$list_grouping]['sort_by'] = $_GET['sort_by'];
}
if ($_SESSION['save'][$page_name][$list_grouping]['sort_by'] == '')
{
	$_SESSION['save'][$page_name][$list_grouping]['sort_by'] = $sort_def_field;
	$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = $sort_def_direction;
} else
{
	if (in_array($_GET['sort_direction'], array('desc', 'asc')))
	{
		$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = $_GET['sort_direction'];
	}
	if ($_SESSION['save'][$page_name][$list_grouping]['sort_direction'] == '')
	{
		$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = 'desc';
	}
}
$_SESSION['save'][$page_name]['sort_by'] = $_SESSION['save'][$page_name][$list_grouping]['sort_by'];
$_SESSION['save'][$page_name]['sort_direction'] = $_SESSION['save'][$page_name][$list_grouping]['sort_direction'];

if (isset($_GET['num_on_page']))
{
	$_SESSION['save'][$page_name][$list_grouping]['num_on_page'] = intval($_GET['num_on_page']);
}
if ($_SESSION['save'][$page_name][$list_grouping]['num_on_page'] < 1)
{
	$_SESSION['save'][$page_name][$list_grouping]['num_on_page'] = 20;
}

if (isset($_GET['from']))
{
	$_SESSION['save'][$page_name]['from'][$list_grouping] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name]['from'][$list_grouping], "integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_donator'] = '';
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
	if (isset($_GET['se_donator']))
	{
		$_SESSION['save'][$page_name]['se_donator'] = trim($_GET['se_donator']);
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

$_SESSION['save'][$page_name]['se_donator_id'] = 0;
if ($_SESSION['save'][$page_name]['se_donator'] != '')
{
	$donator_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_SESSION['save'][$page_name]['se_donator']));
	if ($donator_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$q = sql_escape($_SESSION['save'][$page_name]['se_donator']);
		$where .= " and u1.username='$q'";
	}
	$_SESSION['save'][$page_name]['se_donator_id'] = $donator_id;
	$table_filtered = 1;
}

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
		$where .= " and u2.username='$q'";
	}
	$_SESSION['save'][$page_name]['se_user_id'] = $user_id;
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_status_id'] != '')
{
	if ($list_grouping == 'donator' || $list_grouping == 'log')
	{
		$where .= " and u1.status_id=" . intval($_SESSION['save'][$page_name]['se_status_id']);
	}
	if ($list_grouping == 'user' || $list_grouping == 'log')
	{
		$where .= " and u2.status_id=" . intval($_SESSION['save'][$page_name]['se_status_id']);
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_reseller_code'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_reseller_code']);
	if ($list_grouping == 'donator' || $list_grouping == 'log')
	{
		$where .= " and u1.reseller_code='$q'";
	}
	if ($list_grouping == 'user' || $list_grouping == 'log')
	{
		$where .= " and u2.reseller_code='$q'";
	}
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

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'donator')
{
	$sort_by = "u1.username";
} elseif ($sort_by == 'user')
{
	$sort_by = "u2.username";
} elseif ($sort_by == 'status_id')
{
	if ($list_grouping == 'donator')
	{
		$sort_by = "u1.status_id";
	} elseif ($list_grouping == 'user')
	{
		$sort_by = "u2.status_id";
	}
} elseif ($sort_by == 'reseller_code')
{
	if ($list_grouping == 'donator')
	{
		$sort_by = "u1.reseller_code";
	} elseif ($list_grouping == 'user')
	{
		$sort_by = "u2.reseller_code";
	}
} elseif ($sort_by == 'donations')
{
	$sort_by = "donations";
} elseif ($sort_by == 'tokens_average')
{
	$sort_by = "tokens_average";
} elseif ($sort_by == 'tokens_spent')
{
	$sort_by = "$table_name.tokens";
} elseif ($sort_by == 'tokens_received')
{
	$sort_by = "($table_name.tokens-$table_name.tokens_revenue)";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'stats_users_donations');

// =====================================================================================================================
// list items
// =====================================================================================================================

if ($table_group_by != '')
{
	$total_num = mr2number(sql("select count(distinct $table_group_by) from $table_projector $where"));
} else
{
	$total_num = mr2number(sql("select count(*) from $table_projector $where"));
}

if (($_SESSION['save'][$page_name]['from'][$list_grouping] >= $total_num || $_SESSION['save'][$page_name]['from'][$list_grouping] < 0) || ($_SESSION['save'][$page_name]['from'][$list_grouping] > 0 && $total_num <= $_SESSION['save'][$page_name][$list_grouping]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'][$list_grouping] = 0;
}
if ($table_group_by != '')
{
	$data = mr2array(sql("select $table_selector from $table_projector $where group by $table_group_by order by $sort_by limit " . $_SESSION['save'][$page_name]['from'][$list_grouping] . ", " . $_SESSION['save'][$page_name][$list_grouping]['num_on_page']));
} else
{
	$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'][$list_grouping] . ", " . $_SESSION['save'][$page_name][$list_grouping]['num_on_page']));
}

if ($table_summary_selector != '' && $table_summary_field_name != '')
{
	$total[0] = mr2array_single(sql("select $table_summary_selector from $table_projector $where limit 1"));
	$total[0][$table_summary_field_name] = $lang['common']['total'];
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();

$smarty->assign('data', $data);
$smarty->assign('total', $total);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_summary_field_name', $table_summary_field_name);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name][$list_grouping]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name][$list_grouping]['num_on_page'], $_SESSION['save'][$page_name]['from'][$list_grouping], "$page_name?", 14));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_users_donations']);

$smarty->display("layout.tpl");
