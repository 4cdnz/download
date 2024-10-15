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

grid_presets_start($grid_presets, $page_name, 'admins');

$options = get_options();

$list_status_values = array(
		0 => $lang['settings']['admin_user_field_status_disabled'],
		1 => $lang['settings']['admin_user_field_status_active'],
);

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? order by title asc", $lang['system']['language_code']));

$list_country_values = array();
$list_country_values[''] = ' ';
foreach ($list_countries as $country)
{
	$list_country_values[$country['country_code']] = $country['title'];
}

$table_fields = array();

$table_fields[] = array('id' => 'user_id',                    'title' => $lang['settings']['admin_user_field_id'],                          'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'login',                      'title' => $lang['settings']['admin_user_field_login'],                       'is_default' => 1, 'type' => 'text', 'ifwarn' => 'is_debug_enabled', 'value_postfix' => 'warning_text');
$table_fields[] = array('id' => 'admin_group',                'title' => $lang['settings']['admin_user_field_group'],                       'is_default' => 1, 'type' => 'refid',  'link' => 'admin_users_groups.php?action=change&item_id=%id%', 'link_id' => 'group_id', 'filter_ids' => ['se_group_id' => 'group_id']);
$table_fields[] = array('id' => 'status_id',                  'title' => $lang['settings']['admin_user_field_status'],                      'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'lang',                       'title' => $lang['settings']['admin_user_field_language'],                    'is_default' => 0, 'type' => 'text', 'ucfirst' => 1);
$table_fields[] = array('id' => 'skin',                       'title' => $lang['settings']['admin_user_field_skin'],                        'is_default' => 0, 'type' => 'text', 'ucfirst' => 1);
if ($config['is_clone_db'] != 'true')
{
	$table_fields[] = array('id' => 'content_delete_daily_limit', 'title' => $lang['settings']['admin_user_field_content_delete_daily_limit'],  'is_default' => 1, 'type' => 'text');
	$table_fields[] = array('id' => 'videos_amount',              'title' => $lang['settings']['admin_user_field_videos_count'],                'is_default' => 1, 'type' => 'number', 'link' => 'videos.php?no_filter=true&se_admin_user_id=%id%', 'link_id' => 'user_id', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'albums_amount',              'title' => $lang['settings']['admin_user_field_albums_count'],                'is_default' => 1, 'type' => 'number', 'link' => 'albums.php?no_filter=true&se_admin_user_id=%id%', 'link_id' => 'user_id', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'posts_amount',               'title' => $lang['settings']['admin_user_field_posts_count'],                 'is_default' => 1, 'type' => 'number', 'link' => 'posts.php?no_filter=true&se_admin_user_id=%id%', 'link_id' => 'user_id', 'ifdisable_zero' => 1);
}
$table_fields[] = array('id' => 'logins_amount',              'title' => $lang['settings']['admin_user_field_logins_count'],                'is_default' => 1, 'type' => 'number', 'link' => 'log_logins.php?no_filter=true&se_user=%id%', 'link_id' => 'login', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'last_login',                 'title' => $lang['settings']['admin_user_field_last_login'],                  'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'last_activity',              'title' => $lang['settings']['admin_user_field_last_activity'],               'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'description',                'title' => $lang['settings']['admin_user_field_description'],                 'is_default' => 1, 'type' => 'longtext');
$table_fields[] = array('id' => 'is_debug_enabled',           'title' => $lang['settings']['admin_user_field_debug_mode'],                  'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'added_date',                 'title' => $lang['settings']['admin_user_field_added_date'],                  'is_default' => 0, 'type' => 'datetime');

$sort_def_field = "user_id";
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

$table_name = "$config[tables_prefix_multi]admin_users";
$table_key_name = "user_id";

$table_selector_videos_count = "(select count(*) from $config[tables_prefix]videos where admin_user_id=$table_name.$table_key_name)";
$table_selector_albums_count = "(select count(*) from $config[tables_prefix]albums where admin_user_id=$table_name.$table_key_name)";
$table_selector_posts_count = "(select count(*) from $config[tables_prefix]posts where admin_user_id=$table_name.$table_key_name)";
$table_selector = "$table_name.*, $config[tables_prefix_multi]admin_users_groups.title as admin_group, (select count(*) from $config[tables_prefix_multi]log_logins where user_id=$table_name.$table_key_name and is_failed=0) as logins_amount, (select max(last_request_date) from $config[tables_prefix_multi]log_logins where user_id=$table_name.$table_key_name and is_failed=0) as last_activity";

$table_projector = "$table_name left join $config[tables_prefix_multi]admin_users_groups on $table_name.group_id=$config[tables_prefix_multi]admin_users_groups.group_id";

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
	$_SESSION['save'][$page_name]['se_group_id'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_group_id']))
	{
		$_SESSION['save'][$page_name]['se_group_id'] = intval($_GET['se_group_id']);
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and ($table_name.login like '%$q%') ";
}

if ($_SESSION['save'][$page_name]['se_group_id'] != 0)
{
	if ($_SESSION['save'][$page_name]['se_group_id'] == -1)
	{
		$where .= " and $table_name.is_superadmin>0";
	} else
	{
		$where .= " and $table_name.group_id=" . intval($_SESSION['save'][$page_name]['se_group_id']);
	}
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'admin_group')
{
	$sort_by = "$config[tables_prefix_multi]admin_users_groups.title";
} elseif ($sort_by == 'videos_amount')
{
	$sort_by = "$table_selector_videos_count";
} elseif ($sort_by == 'albums_amount')
{
	$sort_by = "$table_selector_albums_count";
} elseif ($sort_by == 'posts_amount')
{
	$sort_by = "$table_selector_posts_count";
} elseif ($sort_by == 'logins_amount')
{
	$sort_by = "logins_amount";
} elseif ($sort_by == 'last_activity')
{
	$sort_by = "last_activity";
} elseif ($sort_by == 'last_login')
{
	$sort_by = "last_ip";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

foreach ($table_fields as $k => $field)
{
	if ($field['is_enabled'] == 1)
	{
		if ($field['id'] == 'videos_amount')
		{
			$table_selector .= ", $table_selector_videos_count as videos_amount";
		}
		if ($field['id'] == 'albums_amount')
		{
			$table_selector .= ", $table_selector_albums_count as albums_amount";
		}
		if ($field['id'] == 'posts_amount')
		{
			$table_selector .= ", $table_selector_posts_count as posts_amount";
		}
	}
}

grid_presets_end($grid_presets, $page_name, 'admins');

// =====================================================================================================================
// table actions
// =====================================================================================================================

if ($_REQUEST['action'] == 'reset_kvs_support_password' && $_REQUEST['password'] != '')
{
	$new_support_password = generate_password_hash(md5($_REQUEST['password']));
	if (mr2number(sql_pr("select user_id from $table_name where login='kvs_support'")) > 0)
	{
		sql_pr("update $table_name set pass=? where login='kvs_support'", $new_support_password);
	} else
	{
		sql_pr("insert into $table_name set login='kvs_support', status_id=1, is_superadmin=2, lang='english', skin='default', short_date_format='%d %b, %y', full_date_format='%d %b, %y %H:%M', pass=?, added_date=?", $new_support_password, date('Y-m-d H:i:s'));
	}

	$admin_data = mr2array_single(sql_pr("select * from $table_name where login='kvs_support'"));
	$known_admins_content = @json_decode(file_get_contents("$config[project_path]/admin/data/system/ap.dat"), true);
	if (is_array($known_admins_content['admins']))
	{
		$found_admin = false;
		foreach ($known_admins_content['admins'] as &$temp)
		{
			if ($temp['id'] == $admin_data['user_id'])
			{
				$temp['hash'] = substr(md5("kvs_support{$new_support_password}"), 0, 20);
				$found_admin = true;
				break;
			}
		}
		unset($temp);
		if (!$found_admin)
		{
			$known_admins_content['admins'][] = ['id' => $admin_data['user_id'], 'hash' => substr(md5("kvs_support{$new_support_password}"), 0, 20)];
		}
		file_put_contents("$config[project_path]/admin/data/system/ap.dat", json_encode($known_admins_content), LOCK_EX);
	}

	echo "Updated kvs_support password";
	die;
}

if ($_REQUEST['action'] == 'reset_admin_cache')
{
	$smarty = new mysmarty();
	$clear_dir = $smarty->compile_dir;
	if ($clear_dir <> '' && $clear_dir <> $config['project_path'])
	{
		exec("find $clear_dir -type f -delete > /dev/null");
	}
	if (function_exists('opcache_reset'))
	{
		opcache_reset();
	}
	header("Location: $page_name");
	die;
}

if ($_REQUEST['action'] == 'reset_lock_files')
{
	$system_files = get_contents_from_dir("$config[project_path]/admin/data/system", 1);
	foreach ($system_files as $system_file)
	{
		if (substr($system_file, -5) == '.lock')
		{
			@unlink("$config[project_path]/admin/data/system/$system_file");
		}
	}
	@unlink("$config[project_path]/admin/data/advertisements/advertisements.lock");
	@unlink("$config[project_path]/admin/data/engine/license/license.lock");

	$plugins = get_contents_from_dir("$config[project_path]/admin/data/plugins", 2);
	foreach ($plugins as $plugin)
	{
		if (is_file("$config[project_path]/admin/data/plugins/$plugin/cron.lock"))
		{
			@unlink("$config[project_path]/admin/data/plugins/$plugin/cron.lock");
		}
	}
}

if ($_REQUEST['action'] == 'login')
{
	$admin_id = intval($_REQUEST['admin_id']);
	$admin_data = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_users where user_id=?", $admin_id));
	if ($admin_data['user_id'] > 0 && $admin_data['is_superadmin'] == 0)
	{
		$_SESSION['userdata'] = $admin_data;
		$_SESSION['userdata']['ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['userdata']['session_id'] = md5(mt_rand(0, 999999999));
		$_SESSION['userdata']['last_login'] = @mr2array_single(sql_pr("select login_date, ip, country_code, duration from $config[tables_prefix_multi]log_logins where user_id=? order by login_date desc limit 1", $_SESSION['userdata']['user_id'], trim($_SERVER['GEOIP_COUNTRY_CODE'])));
		$_SESSION['userdata']['pass'] = md5($_SESSION['userdata']['pass']);
		$_SESSION['userdata']['login_gate'] = $config['project_url'];
		if ($_SESSION['userdata']['last_login']['ip'] <> '')
		{
			$_SESSION['userdata']['last_login']['ip'] = int2ip($_SESSION['userdata']['last_login']['ip']);
		}

		$_SESSION['save'] = @unserialize($_SESSION['userdata']['preference']) ?: [];
		unset($_SESSION['userdata']['preference']);

		sql_pr("insert into $config[tables_prefix_multi]log_logins set session_id=?, user_id=?, login_date=?, last_request_date=?, duration=0, ip=?, full_ip=?, country_code=?", $_SESSION['userdata']['session_id'], $_SESSION['userdata']['user_id'], date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']), trim($_SERVER['REMOTE_ADDR']), trim($_SERVER['GEOIP_COUNTRY_CODE']));
		sql_pr("update $config[tables_prefix_multi]admin_users set last_ip=?, last_country_code=? where user_id=?", ip2int($_SERVER['REMOTE_ADDR']), trim($_SERVER['GEOIP_COUNTRY_CODE']), $_SESSION['userdata']['user_id']);
		return_ajax_success("start.php");
	}
	die;
}

if ($_REQUEST['action'] == 'view_debug_log')
{
	$id = intval($_REQUEST['id']);
	download_log_file("$config[project_path]/admin/logs/debug_admin_$id.txt");
	die;
}

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
	settype($_POST['permissions_ids'], "array");

	validate_field('uniq', $_POST['login'], $lang['settings']['admin_user_field_login'], array('field_name_in_base' => 'login'));
	if ($_POST['action'] == "add_new_complete")
	{
		validate_field('empty', $_POST['pass'], $lang['settings']['admin_user_field_password']);
	}
	validate_field('empty', $_POST['short_date_format'], $lang['settings']['admin_user_field_short_date_format']);
	validate_field('empty', $_POST['full_date_format'], $lang['settings']['admin_user_field_full_date_format']);
	validate_field('empty', $_POST['lang'], $lang['settings']['admin_user_field_language']);
	validate_field('empty', $_POST['skin'], $lang['settings']['admin_user_field_skin']);
	validate_field('empty_int', $_POST['content_delete_daily_limit'], $lang['settings']['admin_user_field_content_delete_daily_limit']);

	$list_groups = array();
	$list_temp = mr2array(sql("select permission_id, title from $config[tables_prefix_multi]admin_permissions order by group_sort_id asc, sort_id asc"));
	foreach ($list_temp as $k => $v)
	{
		$temp = substr($v['title'], 0, strpos($v['title'], "|"));
		if (!in_array($temp, $list_groups))
		{
			$list_groups[] = $temp;
		}
	}
	$is_permissions_selected = 0;
	if (intval($_POST['group_id']) > 0)
	{
		$is_permissions_selected = 1;
	} else
	{
		foreach ($list_groups as $group_prefix)
		{
			$list_group_permissions = mr2array(sql_pr("select permission_id,title from $config[tables_prefix_multi]admin_permissions where title like ? order by group_sort_id asc, sort_id asc", "$group_prefix|%"));

			if ($_POST["access_level_$group_prefix"] == "read")
			{
				foreach ($list_group_permissions as $k => $v)
				{
					if ($v['title'] == "$group_prefix|view")
					{
						$is_permissions_selected = 1;
						break 2;
					}
				}
			} elseif ($_POST["access_level_$group_prefix"] == "full")
			{
				if (array_cnt($list_group_permissions) > 0)
				{
					$is_permissions_selected = 1;
					break;
				}
			} elseif ($_POST["access_level_$group_prefix"] <> "no")
			{
				foreach ($list_group_permissions as $k => $v)
				{
					if (in_array($v['permission_id'], $_POST['permissions_ids']))
					{
						$is_permissions_selected = 1;
						break 2;
					}
				}
			}
		}
	}
	if ($is_permissions_selected == 0)
	{
		$errors[] = get_aa_error('permissions_required', $lang['settings']['admin_user_field_permissions']);
	}
	if (!is_array($errors) && intval($_POST['group_id']) > 0)
	{
		validate_field('empty', $_POST['group_id'], $lang['settings']['admin_user_field_group']);
	}

	if ($_POST['action'] == 'add_new_complete' || $_POST['pass'] != '')
	{
		if (!is_writable("$config[project_path]/admin/data/system/ap.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/system/ap.dat");
		}
	}

	$_POST['is_access_to_content_flagged_with'] = (intval($_POST['is_access_to_content_flagged_with']) == 1 ? implode(',', $_POST['is_access_to_content_flagged_with_flags'] ?? []) : '');

	if (!is_array($errors))
	{
		$item_id = intval($_POST['item_id']);
		if ($_POST['action'] == 'add_new_complete')
		{
			$_POST['pass'] = generate_password_hash(md5($_POST['pass']));
			$item_id = sql_insert("insert into $table_name set group_id=?, login=?, status_id=?, pass=?, lang=?, skin=?, short_date_format=?, full_date_format=?, description=?, is_access_to_own_content=?, is_access_to_disabled_content=?, is_access_to_content_flagged_with=?, content_delete_daily_limit=?, preference=?, added_date=?",
				$_POST['group_id'], $_POST['login'], intval($_POST['status_id']), $_POST['pass'], $_POST['lang'], $_POST['skin'], $_POST['short_date_format'], $_POST['full_date_format'], $_POST['description'], intval($_POST['is_access_to_own_content']), intval($_POST['is_access_to_disabled_content']), trim($_POST['is_access_to_content_flagged_with']), intval($_POST['content_delete_daily_limit']), '', date("Y-m-d H:i:s"));

			$_SESSION['messages'][] = $lang['common']['success_message_added'];

			$known_admins_content = @json_decode(file_get_contents("$config[project_path]/admin/data/system/ap.dat"), true);
			if (is_array($known_admins_content['admins']))
			{
				$found_admin = false;
				foreach ($known_admins_content['admins'] as &$temp)
				{
					if ($temp['id'] == $item_id)
					{
						$temp['hash'] = substr(md5($_POST['login'] . $_POST['pass']), 0, 20);
						$found_admin = true;
						break;
					}
				}
				unset($temp);
				if (!$found_admin)
				{
					$known_admins_content['admins'][] = ['id' => $item_id, 'hash' => substr(md5($_POST['login'] . $_POST['pass']), 0, 20)];
				}
				file_put_contents("$config[project_path]/admin/data/system/ap.dat", json_encode($known_admins_content), LOCK_EX);
			}
		} else
		{
			sql_pr("update $table_name set group_id=?, login=?, status_id=?, lang=?, skin=?, short_date_format=?, full_date_format=?, description=?, custom_css=?, is_access_to_own_content=?, is_access_to_disabled_content=?, is_access_to_content_flagged_with=?, content_delete_daily_limit=? where $table_key_name=? and is_superadmin=0",
				$_POST['group_id'], $_POST['login'], intval($_POST['status_id']), $_POST['lang'], $_POST['skin'], $_POST['short_date_format'], $_POST['full_date_format'], $_POST['description'], $_POST['custom_css'], intval($_POST['is_access_to_own_content']), intval($_POST['is_access_to_disabled_content']), trim($_POST['is_access_to_content_flagged_with']), intval($_POST['content_delete_daily_limit']), $item_id);
			if ($_POST['pass'] <> '')
			{
				$_POST['pass'] = generate_password_hash(md5($_POST['pass']));
				sql_pr("update $table_name set pass=? where $table_key_name=?", $_POST['pass'], $item_id);
			}
			sql_pr("delete from $config[tables_prefix_multi]admin_users_permissions where user_id=?", $item_id);

			$admin_data = mr2array_single(sql_pr("select * from $table_name where user_id=?", $item_id));
			$known_admins_content = @json_decode(file_get_contents("$config[project_path]/admin/data/system/ap.dat"), true);
			if (is_array($known_admins_content['admins']))
			{
				$found_admin = false;
				foreach ($known_admins_content['admins'] as &$temp)
				{
					if ($temp['id'] == $item_id)
					{
						$temp['hash'] = substr(md5($admin_data['login'] . $admin_data['pass']), 0, 20);
						$found_admin = true;
						break;
					}
				}
				unset($temp);
				if (!$found_admin)
				{
					$known_admins_content['admins'][] = ['id' => $item_id, 'hash' => substr(md5($_POST['login'] . $_POST['pass']), 0, 20)];
				}
				file_put_contents("$config[project_path]/admin/data/system/ap.dat", json_encode($known_admins_content), LOCK_EX);
			}

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		foreach ($list_groups as $group_prefix)
		{
			$list_group_permissions = mr2array(sql_pr("select permission_id,title from $config[tables_prefix_multi]admin_permissions where title like ? order by group_sort_id asc, sort_id asc", "$group_prefix|%"));

			if ($_POST["access_level_$group_prefix"] == "read")
			{
				foreach ($list_group_permissions as $k => $v)
				{
					if ($v['title'] == "$group_prefix|view")
					{
						sql_pr("insert into $config[tables_prefix_multi]admin_users_permissions set user_id=?, permission_id=?", $item_id, $v['permission_id']);
					}
				}
			} elseif ($_POST["access_level_$group_prefix"] == "full")
			{
				foreach ($list_group_permissions as $k => $v)
				{
					sql_pr("insert into $config[tables_prefix_multi]admin_users_permissions set user_id=?, permission_id=?", $item_id, $v['permission_id']);
				}
			} elseif ($_POST["access_level_$group_prefix"] <> "no")
			{
				foreach ($list_group_permissions as $k => $v)
				{
					if (in_array($v['permission_id'], $_POST['permissions_ids']))
					{
						sql_pr("insert into $config[tables_prefix_multi]admin_users_permissions set user_id=?, permission_id=?", $item_id, $v['permission_id']);
					}
					if ($v['title'] == "$group_prefix|view")
					{
						sql_pr("insert into $config[tables_prefix_multi]admin_users_permissions set user_id=?, permission_id=?", $item_id, $v['permission_id']);
					}
				}
			}
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

	if (is_array($_REQUEST['row_select']) && array_cnt($_REQUEST['row_select']) > 0)
	{
		$row_select = implode(',', array_map('intval', $_REQUEST['row_select']));
		if ($_REQUEST['batch_action'] == 'delete')
		{
			sql("delete from $table_name where $table_key_name in ($row_select) and is_superadmin=0");
			sql("delete from $config[tables_prefix_multi]admin_users_permissions where $table_key_name in ($row_select)");
			sql("delete from $config[tables_prefix_multi]admin_users_settings where $table_key_name in ($row_select)");
			foreach ($_REQUEST['row_select'] as $item_id)
			{
				$item_id = intval($item_id);
				@unlink("$config[project_path]/admin/logs/debug_admin_$item_id.txt");

				$known_admins_content = @json_decode(file_get_contents("$config[project_path]/admin/data/system/ap.dat"), true);
				if (is_array($known_admins_content['admins']))
				{
					$found_admin = false;
					foreach ($known_admins_content['admins'] as $key => $temp)
					{
						if ($temp['id'] == $item_id)
						{
							unset($known_admins_content['admins'][$key]);
							$found_admin = true;
							break;
						}
					}
					unset($temp);
					if ($found_admin)
					{
						file_put_contents("$config[project_path]/admin/data/system/ap.dat", json_encode($known_admins_content), LOCK_EX);
					}
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'enable_debug')
		{
			sql("update $table_name set is_debug_enabled=1 where $table_key_name in ($row_select)");
			$_SESSION['messages'][] = $lang['common']['success_message_debug_enabled'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'activate')
		{
			sql("update $table_name set status_id=1 where $table_key_name in ($row_select) and is_superadmin=0");
			$_SESSION['messages'][] = $lang['common']['success_message_activated'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'deactivate')
		{
			sql("update $table_name set status_id=0 where $table_key_name in ($row_select) and is_superadmin=0");
			$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'disable_debug')
		{
			sql("update $table_name set is_debug_enabled=0 where $table_key_name in ($row_select)");
			foreach ($_REQUEST['row_select'] as $item_id)
			{
				$item_id = intval($item_id);
				@unlink("$config[project_path]/admin/logs/debug_admin_$item_id.txt");
			}
			$_SESSION['messages'][] = $lang['common']['success_message_debug_disabled'];
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
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=? and is_superadmin=0", intval($_GET['item_id'])));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	$_POST['permissions_ids'] = mr2array_list(sql_pr("select permission_id from $config[tables_prefix_multi]admin_users_permissions where user_id=?", $_POST['user_id']));
	$_POST['is_access_to_content_flagged_with'] = ($_POST['is_access_to_content_flagged_with'] ? array_map('trim', explode(',', $_POST['is_access_to_content_flagged_with'])) : []);
	$_POST['pass'] = "";
}

if ($_GET['action'] == 'add_new')
{
	$_POST['skin'] = 'default';
	$_POST['lang'] = 'english';
	$_POST['status_id'] = '1';
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
		if ($data[$k]['last_country_code'])
		{
			$data[$k]['last_login'] = int2ip($data[$k]['last_ip']) . ' (' . nvl($list_country_values[strtolower($data[$k]['last_country_code'])], $data[$k]['last_country_code']) . ')';
		} else
		{
			$data[$k]['last_login'] = int2ip($data[$k]['last_ip']);
		}
		if (is_file("$config[project_path]/admin/logs/debug_admin_{$v[$table_key_name]}.txt"))
		{
			$data[$k]['has_debug_log'] = 1;
		}
		if ($v['is_superadmin'] > 0)
		{
			$data[$k]['group_id'] = '-1';
			$data[$k]['admin_group'] = $lang['settings']['admin_user_field_group_superadmins'];
			$data[$k]['is_editing_forbidden'] = 1;
			$data[$k]['content_delete_daily_limit'] = '';
			if ($v['is_superadmin'] == 1 || $options['ENABLE_KVS_SUPPORT_ACCESS'] == 1)
			{
				$data[$k]['status_id'] = 1;
			}
		}
		if ($v['is_debug_enabled'] == 1)
		{
			$data[$k]['warning_text'] = '(' . $lang['settings']['admin_user_warning_debug_enabled'] . ')';
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$list_langs_temp = str_replace(".php", "", get_contents_from_dir($config['project_path'] . "/admin/langs/", 1));
$list_langs = array();
foreach ($list_langs_temp as $v)
{
	$list_langs[] = $v;
}

$list_skins_temp = str_replace(".css", "", get_contents_from_dir($config['project_path'] . "/admin/styles/", 1));
$list_skins = array();
foreach ($list_skins_temp as $v)
{
	if ($v != '.htaccess')
	{
		$list_skins[] = $v;
	}
}

$list_permissions = array();
$list_temp = mr2array(sql("select permission_id, title from $config[tables_prefix_multi]admin_permissions order by group_sort_id asc, sort_id asc"));
foreach ($list_temp as $k => $v)
{
	$temp = substr($v['title'], 0, strpos($v['title'], "|"));
	$list_permissions[$temp][$v['permission_id']] = $v['title'];
}

$plugins_list = get_contents_from_dir("$config[project_path]/admin/plugins", 2);
foreach ($plugins_list as $k => $v)
{
	if (is_file("$config[project_path]/admin/plugins/$v/langs/english.php"))
	{
		require_once "$config[project_path]/admin/plugins/$v/langs/english.php";
	}
	if ($_SESSION['userdata']['lang'] != 'english' && is_file("$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php"))
	{
		require_once "$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php";
	}
}

$languages_list = mr2array(sql("select * from $config[tables_prefix]languages"));
foreach ($languages_list as $language)
{
	$lang['permissions']["localization|$language[code]"] = $language['title'];
}

$smarty = new mysmarty();
$smarty->assign('list_groups', mr2array(sql("select group_id, title from $config[tables_prefix_multi]admin_users_groups order by title asc")));
$smarty->assign('list_permissions', $list_permissions);
$smarty->assign('list_langs', $list_langs);
$smarty->assign('list_skins', $list_skins);

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('num_on_page', $_SESSION['save'][$page_name]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('list_flags_admins', mr2array(sql("select * from $config[tables_prefix]flags where is_admin_flag=1 order by group_id, title asc")));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['login'], $lang['settings']['admin_user_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['admin_user_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_admins_list']);
}

$smarty->display("layout.tpl");
