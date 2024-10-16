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

grid_presets_start($grid_presets, $page_name, 'stats_users_logins');

if (isset($_GET['se_group_by']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = $_GET['se_group_by'];
}
if ($_SESSION['save'][$page_name]['se_group_by'] != 'user' && $_SESSION['save'][$page_name]['se_group_by'] != 'ip')
{
	$_SESSION['save'][$page_name]['se_group_by'] = 'log';
}
$list_grouping = $_SESSION['save'][$page_name]['se_group_by'];

$list_status_values = array(
	0 => $lang['stats']['users_logins_field_status_disabled'],
	1 => $lang['stats']['users_logins_field_status_not_confirmed'],
	2 => $lang['stats']['users_logins_field_status_active'],
	3 => $lang['stats']['users_logins_field_status_premium'],
	4 => $lang['stats']['users_logins_field_status_anonymous'],
	5 => $lang['stats']['users_logins_field_status_generated'],
	6 => $lang['stats']['users_logins_field_status_webmaster'],
);

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? order by title asc", $lang['system']['language_code']));

$list_country_values = array();
$list_country_values[0] = ' ';
foreach ($list_countries as $country)
{
	$list_country_values[$country['country_code']] = $country['title'];
}

$table_fields = array();

if ($list_grouping == 'user')
{
	$table_fields[] = array('id' => 'user',             'title' => $lang['stats']['users_logins_field_user'],               'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user' => 'user']);
	$table_fields[] = array('id' => 'status_id',        'title' => $lang['stats']['users_logins_field_status'],             'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'filter_ids' => ['se_status_id' => 'status_id']);
	$table_fields[] = array('id' => 'reseller_code',    'title' => $lang['stats']['users_logins_field_reseller_code'],      'is_default' => 0, 'type' => 'text', 'filter_ids' => ['se_reseller_code' => 'reseller_code']);
	$table_fields[] = array('id' => 'unique_logins',    'title' => $lang['stats']['users_logins_field_unique_logins'],      'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'unique_ips',       'title' => $lang['stats']['users_logins_field_unique_ips'],         'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'unique_countries', 'title' => $lang['stats']['users_logins_field_unique_countries'],   'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'unique_agents',    'title' => $lang['stats']['users_logins_field_unique_agents'],      'is_default' => 1, 'type' => 'number');
} elseif ($list_grouping == 'ip')
{
	$table_fields[] = array('id' => 'ip',            'title' => $lang['stats']['users_logins_field_ip'],            'is_default' => 1, 'type' => 'ip', 'filter_ids' => ['se_ip' => '@value']);
	$table_fields[] = array('id' => 'unique_users',  'title' => $lang['stats']['users_logins_field_unique_users'],  'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'unique_users2', 'title' => $lang['stats']['users_logins_field_unique_users'],  'is_default' => 1, 'type' => 'list', 'link' => 'users.php?action=change&item_id=%id%', 'permission' => 'users|view', 'filter_ids' => ['se_user' => 'title']);
} else
{
	$table_fields[] = array('id' => 'user',          'title' => $lang['stats']['users_logins_field_user'],          'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user' => 'user']);
	$table_fields[] = array('id' => 'status_id',     'title' => $lang['stats']['users_logins_field_status'],        'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'filter_ids' => ['se_status_id' => 'status_id']);
	$table_fields[] = array('id' => 'reseller_code', 'title' => $lang['stats']['users_logins_field_reseller_code'], 'is_default' => 0, 'type' => 'text', 'filter_ids' => ['se_reseller_code' => 'reseller_code']);
	$table_fields[] = array('id' => 'login_date',    'title' => $lang['stats']['users_logins_field_login_date'],    'is_default' => 1, 'type' => 'datetime');
	if ($config['safe_mode'] == 'false')
	{
		$table_fields[] = array('id' => 'ip',        'title' => $lang['stats']['users_logins_field_ip'],            'is_default' => 1, 'type' => 'ip', 'filter_ids' => ['se_ip' => '@value']);
	}
	$table_fields[] = array('id' => 'country_code',  'title' => $lang['stats']['users_logins_field_country'],       'is_default' => 1, 'type' => 'choice', 'values' => $list_country_values);
	$table_fields[] = array('id' => 'user_agent',    'title' => $lang['stats']['users_logins_field_user_agent'],    'is_default' => 1, 'type' => 'text');
}

if ($list_grouping == 'user')
{
	$sort_def_field = 'user';
	$sort_def_direction = 'desc';
} elseif ($list_grouping == 'ip')
{
	$sort_def_field = 'ip';
	$sort_def_direction = 'desc';
} else
{
	$sort_def_field = 'login_date';
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

$table_name = "$config[tables_prefix]log_logins_users";

if ($list_grouping == 'user')
{
	$table_selector = "u1.user_id, $table_name.username as user, u1.status_id as user_status_id, u1.status_id, u1.reseller_code, count($table_name.ip) as unique_logins, count(distinct $table_name.ip) as unique_ips, count(distinct $table_name.country_code) as unique_countries, count(distinct $table_name.user_agent) as unique_agents";
	$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.user_id";
	$table_group_by = "$table_name.user_id";
} elseif ($list_grouping == 'ip')
{
	$table_selector = "$table_name.ip, $table_name.full_ip, count(distinct $table_name.user_id) as unique_users, group_concat(distinct concat($table_name.user_id, ':', $table_name.username)) as unique_user_ids";
	$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.user_id";
	$table_group_by = "$table_name.ip, $table_name.full_ip";
} else
{
	$table_selector = "$table_name.*, u1.user_id as user_id, $table_name.username as user, u1.status_id as user_status_id, u1.status_id, u1.reseller_code";
	$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.user_id";
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
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_ip'] = '';
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
	if (isset($_GET['se_ip']))
	{
		$_SESSION['save'][$page_name]['se_ip'] = trim($_GET['se_ip']);
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
$where = "and $table_name.is_failed=0";

$_SESSION['save'][$page_name]['se_user_id'] = 0;
if ($_SESSION['save'][$page_name]['se_user'] != '')
{
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_SESSION['save'][$page_name]['se_user']));
	if ($user_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and $table_name.user_id=$user_id";
	}
	$_SESSION['save'][$page_name]['se_user_id'] = $user_id;
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_status_id'] != '')
{
	$where .= " and u1.status_id=" . intval($_SESSION['save'][$page_name]['se_status_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_ip'] != '')
{
	$q = ip2int($_SESSION['save'][$page_name]['se_ip']);
	$escaped_ip = sql_escape($_SESSION['save'][$page_name]['se_ip']);
	$where .= " and ($table_name.ip='$q' or $table_name.full_ip='$escaped_ip')";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_reseller_code'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_reseller_code']);
	$where .= " and u1.reseller_code='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_from'] <> "")
{
	$where .= " and $table_name.login_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and $table_name.login_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'user')
{
	$sort_by = "$table_name.username";
} elseif ($sort_by == 'status_id')
{
	$sort_by = "u1.status_id";
} elseif ($sort_by == 'reseller_code')
{
	$sort_by = "u1.reseller_code";
} elseif ($sort_by == 'unique_logins' || $sort_by == 'unique_ips' || $sort_by == 'unique_countries' || $sort_by == 'unique_agents' || $sort_by == 'unique_users')
{
	$sort_by = "$sort_by";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'stats_users_logins');

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

foreach ($data as $k => $v)
{
	if ($list_grouping == 'ip')
	{
		$data[$k]['unique_users2'] = [];
		if ($v['unique_user_ids'])
		{
			$unique_user_ids = explode(',', $v['unique_user_ids']);
			foreach ($unique_user_ids as $unique_user_id)
			{
				$unique_user_id = trim($unique_user_id);
				if ($unique_user_id != '')
				{
					$unique_user_id = explode(':', $unique_user_id, 2);
					if (intval($unique_user_id[0]) > 0 && trim($unique_user_id[1]) != '')
					{
						$data[$k]['unique_users2'][] = ['id' => intval($unique_user_id[0]), 'title' => trim($unique_user_id[1])];
					}
				}
			}
		}
	}
	$ip = $v['full_ip'];
	if ($ip === '')
	{
		$ip = $v['ip'];
	}
	$data[$k]['ip'] = $ip;
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
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name][$list_grouping]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name][$list_grouping]['num_on_page'], $_SESSION['save'][$page_name]['from'][$list_grouping], "$page_name?", 14));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_users_logins']);

$smarty->display("layout.tpl");
