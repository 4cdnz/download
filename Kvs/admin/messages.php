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

grid_presets_start($grid_presets, $page_name, 'messages');

$list_status_values = array(
	0 => $lang['users']['message_field_status_unread'],
	1 => $lang['users']['message_field_status_read'],
);

$table_fields = array();
$table_fields[] = array('id' => 'message_id', 'title' => $lang['users']['message_field_id'],         'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'message',    'title' => $lang['users']['message_field_message'],    'is_default' => 1, 'type' => 'longtext', 'ifhighlight' => 'is_spam', 'value_postfix' => 'spam_text');
$table_fields[] = array('id' => 'user_from',  'title' => $lang['users']['message_field_sender'],     'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user_from' => 'user_from']);
$table_fields[] = array('id' => 'user',       'title' => $lang['users']['message_field_recipient'],  'is_default' => 1, 'type' => 'user', 'filter_ids' => ['se_user' => 'user']);
if ($config['safe_mode'] != 'true')
{
	$table_fields[] = array('id' => 'ip',     'title' => $lang['users']['message_field_ip'],         'is_default' => 1, 'type' => 'ip', 'filter_ids' => ['se_ip' => '@value']);
}
$table_fields[] = array('id' => 'is_read',    'title' => $lang['users']['message_field_status'],     'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'filter_ids' => ['se_is_read' => 'is_read']);
$table_fields[] = array('id' => 'is_spam',    'title' => $lang['users']['message_field_spam'],       'is_default' => 1, 'type' => 'bool', 'ifhighlight' => 'is_spam', 'filter_ids' => ['se_is_spam' => 'is_spam']);
$table_fields[] = array('id' => 'added_date', 'title' => $lang['users']['message_field_added_date'], 'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'read_date',  'title' => $lang['users']['message_field_read_date'],  'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "message_id";
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
$search_fields[] = array('id' => 'message_id', 'title' => $lang['users']['message_field_id']);
$search_fields[] = array('id' => 'message',    'title' => $lang['users']['message_field_message']);

$table_name = "$config[tables_prefix]messages";
$table_key_name = "message_id";
$table_selector = "$table_name.*, u1.username as user, u1.status_id as user_status_id, u2.username as user_from, u2.status_id as user_from_status_id";
$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.user_id left join $config[tables_prefix]users u2 on u2.user_id=$table_name.user_from_id";

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
	$_SESSION['save'][$page_name]['se_is_read'] = '';
	$_SESSION['save'][$page_name]['se_type_id'] = '';
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_user_from'] = '';
	$_SESSION['save'][$page_name]['se_ip'] = '';
	$_SESSION['save'][$page_name]['se_is_spam'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_is_read']))
	{
		$_SESSION['save'][$page_name]['se_is_read'] = trim($_GET['se_is_read']);
	}
	if (isset($_GET['se_type_id']))
	{
		$_SESSION['save'][$page_name]['se_type_id'] = trim($_GET['se_type_id']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_user_from']))
	{
		$_SESSION['save'][$page_name]['se_user_from'] = trim($_GET['se_user_from']);
	}
	if (isset($_GET['se_ip']))
	{
		$_SESSION['save'][$page_name]['se_ip'] = trim($_GET['se_ip']);
	}
	if (isset($_GET['se_is_spam']))
	{
		$_SESSION['save'][$page_name]['se_is_spam'] = trim($_GET['se_is_spam']);
	}
}

$table_filtered = 0;
$where = '';

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
			if ($search_field['id'] == $table_key_name)
			{
				if (preg_match("/^([\ ]*[0-9]+[\ ]*,[\ ]*)+[0-9]+[\ ]*$/is", $q))
				{
					$search_ids_array = array_map('intval', array_map('trim', explode(',', $q)));
					$where_search .= " or $table_name.$search_field[id] in (" . implode(',', $search_ids_array) . ")";
				} else
				{
					$where_search .= " or $table_name.$search_field[id]='$q'";
				}
			} else
			{
				$where_search .= " or $table_name.$search_field[id] like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
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
		$where .= " and $table_name.user_id=$user_id";
		$_SESSION['save'][$page_name]['se_user_id'] = $user_id;
	}
	$table_filtered = 1;
}

$_SESSION['save'][$page_name]['se_user_from_id'] = 0;
if ($_SESSION['save'][$page_name]['se_user_from'] != '')
{
	$user_from_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_SESSION['save'][$page_name]['se_user_from']));
	if ($user_from_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and $table_name.user_from_id=$user_from_id";
		$_SESSION['save'][$page_name]['se_user_from_id'] = $user_from_id;
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_ip'] != '')
{
	$q = ip2int($_SESSION['save'][$page_name]['se_ip']);
	$where .= " and $table_name.ip='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_is_read'] == '1')
{
	$where .= " and $table_name.is_read=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_is_read'] == '0')
{
	$where .= " and $table_name.is_read=0";
	$table_filtered = 1;
}

if (in_array($_SESSION['save'][$page_name]['se_type_id'], array('0', '1', '2', '3', '4')))
{
	$where .= " and type_id=" . intval($_SESSION['save'][$page_name]['se_type_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_is_spam'] == '1')
{
	$where .= " and $table_name.is_spam=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_is_spam'] == '0')
{
	$where .= " and $table_name.is_spam=0";
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
} elseif ($sort_by == 'user_from')
{
	$sort_by = "u2.username";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'messages');

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if ($_POST['action'] == 'add_new_complete')
	{
		if (validate_field('empty', $_POST['user_from'], $lang['users']['message_field_sender']))
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $_POST['user_from'])) == 0)
			{
				$errors[] = get_aa_error('invalid_user', $lang['users']['message_field_sender']);
			}
		}
		if (validate_field('empty', $_POST['user'], $lang['users']['message_field_recipient']))
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $_POST['user'])) == 0)
			{
				$errors[] = get_aa_error('invalid_user', $lang['users']['message_field_recipient']);
			}
		}
		if (!is_array($errors) && $_POST['user'] == $_POST['user_from'])
		{
			$errors[] = get_aa_error('message_the_same_user');
		}
	}
	validate_field('empty', $_POST['message'], $lang['users']['message_field_message']);

	if (!is_array($errors))
	{
		if ($_POST['action'] == 'add_new_complete')
		{
			$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_POST['user']));
			$user_from_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_POST['user_from']));

			sql_pr("insert into $table_name set message=?, user_id=?, user_from_id=?, added_date=?", $_POST['message'], $user_id, $user_from_id, date("Y-m-d H:i:s"));

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set message=? where $table_key_name=?", $_POST['message'], intval($_POST['item_id']));
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}
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

	if ($_REQUEST['batch_action'] == 'delete_spam')
	{
		sql("delete from $table_name where is_spam=1");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		return_ajax_success($page_name);
	} elseif (is_array($_REQUEST['row_select']) && array_cnt($_REQUEST['row_select']) > 0)
	{
		$row_select = implode(',', array_map('intval', $_REQUEST['row_select']));
		if ($_REQUEST['batch_action'] == 'delete')
		{
			sql("delete from $table_name where $table_key_name in ($row_select)");
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'delete_from_user')
		{
			sql("delete from $table_name where user_from_id in ($row_select)");
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
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
	$_POST = mr2array_single(sql_pr("select $table_selector from $table_projector where $table_name.$table_key_name=?", intval($_GET['item_id'])));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}
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
		if ($data[$k]['type_id'] == 0)
		{
			if ($data[$k]['message'] == '')
			{
				$data[$k]['message'] = $lang['users']['message_field_message_deleted'];
				$data[$k]['is_deleted'] = 1;
			}
		} elseif ($data[$k]['type_id'] == 1)
		{
			if ($data[$k]['message'])
			{
				$data[$k]['message'] = $lang['users']['message_field_message_add_to_friends'] . ': ' . $data[$k]['message'];
			} else
			{
				$data[$k]['message'] = $lang['users']['message_field_message_add_to_friends'];
			}
		} elseif ($data[$k]['type_id'] == 2)
		{
			$data[$k]['message'] = $lang['users']['message_field_message_reject_add_to_friends'];
		} elseif ($data[$k]['type_id'] == 3)
		{
			$data[$k]['message'] = $lang['users']['message_field_message_remove_friends'];
		} elseif ($data[$k]['type_id'] == 4)
		{
			$data[$k]['message'] = $lang['users']['message_field_message_approve_add_to_friends'];
		}
		if ($data[$k]['is_spam'] == 1)
		{
			$data[$k]['spam_text'] = '[' . $lang['users']['message_field_spam'] . ']';
		}
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
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('spam_count', mr2number(sql_pr("select count(*) from $table_name where is_spam=1")));
$smarty->assign('num_on_page', $_SESSION['save'][$page_name]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['message_id'], $lang['users']['message_edit']));
	$smarty->assign('supports_popups', 1);
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['users']['message_add']);
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_messages_list']);
}

$smarty->display("layout.tpl");
