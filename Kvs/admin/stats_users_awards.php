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

grid_presets_start($grid_presets, $page_name, 'stats_users_awards');

if (isset($_REQUEST['se_group_by']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = $_REQUEST['se_group_by'];
}
if (!in_array($_SESSION['save'][$page_name]['se_group_by'], ['user', 'type']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = 'log';
}
$list_grouping = $_SESSION['save'][$page_name]['se_group_by'];

$list_award_type_values = array(
	1 => $lang['stats']['users_awards_field_award_type_signup'],
	15 => $lang['stats']['users_awards_field_award_type_login'],
	2 => $lang['stats']['users_awards_field_award_type_avatar'],
	16 => $lang['stats']['users_awards_field_award_type_cover'],
	3 => $lang['stats']['users_awards_field_award_type_comment'],
	4 => $lang['stats']['users_awards_field_award_type_video_upload'],
	5 => $lang['stats']['users_awards_field_award_type_album_upload'],
	6 => $lang['stats']['users_awards_field_award_type_video_sale'],
	7 => $lang['stats']['users_awards_field_award_type_album_sale'],
	13 => $lang['stats']['users_awards_field_award_type_profile_sale'],
	14 => $lang['stats']['users_awards_field_award_type_dvd_sale'],
	8 => $lang['stats']['users_awards_field_award_type_referral'],
	9 => $lang['stats']['users_awards_field_award_type_post_upload'],
	10 => $lang['stats']['users_awards_field_award_type_donate'],
	18 => $lang['stats']['users_awards_field_award_type_messages'],
	11 => $lang['stats']['users_awards_field_award_type_video_views'],
	12 => $lang['stats']['users_awards_field_award_type_album_views'],
	17 => $lang['stats']['users_awards_field_award_type_embed_views'],
);

$table_fields = array();

if ($list_grouping == 'user')
{
	$table_fields[] = array('id' => 'user',              'title' => $lang['stats']['users_awards_field_user'],           'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user' => 'user']);
	$table_fields[] = array('id' => 'total_awards',      'title' => $lang['stats']['users_awards_field_awards'],         'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'total_tokens',      'title' => $lang['stats']['users_awards_field_tokens_granted'], 'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'total_paid_awards', 'title' => $lang['stats']['users_awards_field_awards_paid'],    'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'total_paid_tokens', 'title' => $lang['stats']['users_awards_field_tokens_paid'],    'is_default' => 1, 'type' => 'number');
} elseif ($list_grouping == 'type')
{
	$table_fields[] = array('id' => 'award_type',        'title' => $lang['stats']['users_awards_field_award_type'],     'is_default' => 1, 'type' => 'choice', 'values' => $list_award_type_values, 'filter_ids' => ['se_award_type' => 'award_type']);
	$table_fields[] = array('id' => 'total_users',       'title' => $lang['stats']['users_awards_field_users'],          'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'total_awards',      'title' => $lang['stats']['users_awards_field_awards'],         'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'total_tokens',      'title' => $lang['stats']['users_awards_field_tokens_granted'], 'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'total_paid_awards', 'title' => $lang['stats']['users_awards_field_awards_paid'],    'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'total_paid_tokens', 'title' => $lang['stats']['users_awards_field_tokens_paid'],    'is_default' => 1, 'type' => 'number');
} else
{
	$table_fields[] = array('id' => 'award_id',       'title' => $lang['stats']['users_awards_field_id'],             'is_default' => 1, 'type' => 'text');
	$table_fields[] = array('id' => 'user',           'title' => $lang['stats']['users_awards_field_user'],           'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user' => 'user']);
	$table_fields[] = array('id' => 'award_type',     'title' => $lang['stats']['users_awards_field_award_type'],     'is_default' => 1, 'type' => 'choice', 'values' => $list_award_type_values, 'append' => array(11 => 'amount', 12 => 'amount', 17 => 'amount'), 'filter_ids' => ['se_award_type' => 'award_type']);
	$table_fields[] = array('id' => 'object',         'title' => $lang['stats']['users_awards_field_object'],         'is_default' => 1, 'type' => 'object', 'filter_ids' => ['se_object_type_id' => 'object_type_id', 'se_object_id' => 'object_id']);
	$table_fields[] = array('id' => 'tokens_granted', 'title' => $lang['stats']['users_awards_field_tokens_granted'], 'is_default' => 1, 'type' => 'number', 'value_postfix' => 'zero_text');
	$table_fields[] = array('id' => 'payout',         'title' => $lang['stats']['users_awards_field_payout'],         'is_default' => 1, 'type' => 'refid', 'link' => 'payouts.php?action=change&item_id=%id%', 'link_id' => 'payout_id', 'permission' => 'payouts|view');
	$table_fields[] = array('id' => 'added_date',     'title' => $lang['stats']['users_awards_field_added_date'],     'is_default' => 1, 'type' => 'datetime');
}

if ($list_grouping == 'user')
{
	$sort_def_field = 'user';
	$sort_def_direction = 'desc';
} elseif ($list_grouping == 'type')
{
	$sort_def_field = 'award_type';
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

$table_name = "$config[tables_prefix]log_awards_users";
$table_key_name = "award_id";

if ($list_grouping == 'user')
{
	$table_selector = "$table_name.user_id, u1.username as user, u1.status_id as user_status_id, count(*) as total_awards, sum(tokens_granted) as total_tokens, sum(case when $table_name.payout_id>0 then 1 else 0 end) as total_paid_awards, sum(case when $table_name.payout_id>0 then $table_name.tokens_granted else 0 end) as total_paid_tokens";
	$table_summary_selector = "count(*) as total_awards, sum(tokens_granted) as total_tokens, sum(case when $table_name.payout_id>0 then 1 else 0 end) as total_paid_awards, sum(case when $table_name.payout_id>0 then $table_name.tokens_granted else 0 end) as total_paid_tokens";
	$table_summary_field_name = "user";
	$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.user_id";
	$table_group_by = "$table_name.user_id";
} elseif ($list_grouping == 'type')
{
	$table_selector = "award_type, count(distinct user_id) as total_users, count(*) as total_awards, sum(tokens_granted) as total_tokens, sum(case when $table_name.payout_id>0 then 1 else 0 end) as total_paid_awards, sum(case when $table_name.payout_id>0 then $table_name.tokens_granted else 0 end) as total_paid_tokens";
	$table_summary_selector = "count(distinct user_id) as total_users, count(*) as total_awards, sum(tokens_granted) as total_tokens, sum(case when $table_name.payout_id>0 then 1 else 0 end) as total_paid_awards, sum(case when $table_name.payout_id>0 then $table_name.tokens_granted else 0 end) as total_paid_tokens";
	$table_summary_field_name = "award_type";
	$table_projector = "$table_name";
	$table_group_by = "award_type";
} else
{
	$table_selector = "$table_name.*, $table_name.user_id, u1.username as user, u1.status_id as user_status_id, case when $table_name.video_id>0 then $table_name.video_id when $table_name.album_id>0 then $table_name.album_id when $table_name.post_id>0 then $table_name.post_id when $table_name.comment_id>0 then $table_name.comment_id when $table_name.ref_id>0 then $table_name.ref_id when $table_name.profile_id>0 then $table_name.profile_id when $table_name.dvd_id>0 then $table_name.dvd_id end as object_id, case when $table_name.video_id>0 then 1 when $table_name.album_id>0 then 2 when $table_name.post_id>0 then 12 when $table_name.comment_id>0 then 15 when $table_name.ref_id>0 then 20 when $table_name.profile_id>0 then 20 when $table_name.dvd_id>0 then 5 when $table_name.award_type=18 then 21 else 0 end as object_type_id, case when $table_name.award_type=18 then $table_name.amount else 0 end as object_amount, case when $table_name.video_id>0 then coalesce($config[tables_prefix]videos.title, $config[tables_prefix]videos.video_id) when $table_name.album_id>0 then coalesce($config[tables_prefix]albums.title, $config[tables_prefix]albums.album_id) when $table_name.post_id>0 then coalesce($config[tables_prefix]posts.title, $config[tables_prefix]posts.post_id) when $table_name.comment_id>0 then $config[tables_prefix]comments.comment_id when $table_name.ref_id>0 then u2.username when $table_name.profile_id>0 then u1.username when $table_name.dvd_id>0 then $config[tables_prefix]dvds.title else '' end as object, $config[tables_prefix]users_payouts.status_id as payout_status_id, $config[tables_prefix]users_payouts.added_date as payout_added_date";
	$table_summary_selector = "sum(tokens_granted) as tokens_granted, '0000-00-00 00:00:00' as added_date";
	$table_summary_field_name = "user";
	$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.user_id
							left join $config[tables_prefix]videos on $config[tables_prefix]videos.video_id=$table_name.video_id
							left join $config[tables_prefix]albums on $config[tables_prefix]albums.album_id=$table_name.album_id
							left join $config[tables_prefix]posts on $config[tables_prefix]posts.post_id=$table_name.post_id
							left join $config[tables_prefix]comments on $config[tables_prefix]comments.comment_id=$table_name.comment_id
							left join $config[tables_prefix]dvds on $config[tables_prefix]dvds.dvd_id=$table_name.dvd_id
							left join $config[tables_prefix]users u2 on u2.user_id=$table_name.ref_id
							left join $config[tables_prefix]users_payouts on $config[tables_prefix]users_payouts.payout_id=$table_name.payout_id
	";
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
	$_SESSION['save'][$page_name]['se_object_type_id'] = '';
	$_SESSION['save'][$page_name]['se_object_id'] = '';
	$_SESSION['save'][$page_name]['se_award_type'] = '';
	$_SESSION['save'][$page_name]['se_payout'] = '';
	$_SESSION['save'][$page_name]['se_period_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = "";
	$_SESSION['save'][$page_name]['se_date_to'] = "";
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_object_type_id']))
	{
		$_SESSION['save'][$page_name]['se_object_type_id'] = trim($_GET['se_object_type_id']);
	}
	if (isset($_GET['se_object_id']))
	{
		$_SESSION['save'][$page_name]['se_object_id'] = trim($_GET['se_object_id']);
	}
	if (isset($_GET['se_award_type']))
	{
		$_SESSION['save'][$page_name]['se_award_type'] = intval($_GET['se_award_type']);
	}
	if (isset($_GET['se_payout']))
	{
		$_SESSION['save'][$page_name]['se_payout'] = intval($_GET['se_payout']);
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
		$where .= " and u1.username='$q'";
	}
	$_SESSION['save'][$page_name]['se_user_id'] = $user_id;
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
		case 5:
			$where .= " and $table_name.dvd_id>0";
			break;
		case 12:
			$where .= " and $table_name.post_id>0";
			break;
		case 15:
			$where .= " and $table_name.comment_id>0";
			break;
		case 20:
			$where .= " and ($table_name.profile_id>0 or $table_name.ref_id>0)";
			break;
		case 21:
			$where .= " and $table_name.award_type=18";
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
		case 5:
			$where .= " and $table_name.dvd_id=" . intval($_SESSION['save'][$page_name]['se_object_id']);
			break;
		case 12:
			$where .= " and $table_name.post_id=" . intval($_SESSION['save'][$page_name]['se_object_id']);
			break;
		case 15:
			$where .= " and $table_name.comment_id=" . intval($_SESSION['save'][$page_name]['se_object_id']);
			break;
		case 20:
			$where .= " and ($table_name.profile_id=" . intval($_SESSION['save'][$page_name]['se_object_id']) . " or $table_name.ref_id=" . intval($_SESSION['save'][$page_name]['se_object_id']) . ')';
			break;
		case 21:
			break;
		default:
			$where .= " and 0=1";
			break;
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_award_type'] > 0)
{
	$where .= " and $table_name.award_type=" . intval($_SESSION['save'][$page_name]['se_award_type']);
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_payout'])
{
	case 1:
		$where .= " and $table_name.payout_id>0";
		$table_filtered = 1;
		break;
	case 2:
		$where .= " and $table_name.payout_id=0";
		$table_filtered = 1;
		break;
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
if ($sort_by == 'user')
{
	$sort_by = "u1.username";
} elseif ($sort_by == 'object')
{
	$sort_by = "$table_name.video_id " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.album_id " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.post_id " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.comment_id " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.profile_id " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.dvd_id";
} elseif ($sort_by == 'payout')
{
	$sort_by = "$table_name.payout_id";
} elseif ($sort_by == 'total_awards' || $sort_by == 'total_tokens' || $sort_by == 'total_paid_awards' || $sort_by == 'total_paid_tokens')
{
	$sort_by = "$sort_by";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'stats_users_awards');

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
		$row_select = implode(',', array_map('intval', $_REQUEST['row_select']));
		if ($_REQUEST['batch_action'] == 'revert')
		{
			$awards = mr2array(sql_pr("select * from $table_name where $table_key_name in ($row_select) and payout_id=0 and tokens_granted>0"));
			foreach ($awards as $award)
			{
				revoke_tokens_from_user($award['user_id'], $award['tokens_granted']);
				sql_pr("update $table_name set tokens_granted=0 where $table_key_name=?", $award[$table_key_name]);
			}
			$_SESSION['messages'][] = $lang['stats']['users_awards_success_message_reverted'];
			return_ajax_success($page_name);
		}
	}

	$errors[] = get_aa_error('unexpected_error');
	return_ajax_errors($errors);
}

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
	if ($data[$k]['payout_status_id'] > 0)
	{
		if ($data[$k]['payout_status_id'] == 2)
		{
			$data[$k]['payout'] = $data[$k]['payout_added_date'];
		} else
		{
			$data[$k]['payout'] = $lang['users']['payout_field_status_in_progress'];
		}
	}
	if ($data[$k]['tokens_granted'] == 0)
	{
		$data[$k]['zero_text'] = $lang['stats']['users_awards_field_tokens_granted_reverted'];
	}
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
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_summary_field_name', $table_summary_field_name);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name][$list_grouping]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name][$list_grouping]['num_on_page'], $_SESSION['save'][$page_name]['from'][$list_grouping], "$page_name?", 14));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_users_awards']);

$smarty->display("layout.tpl");
