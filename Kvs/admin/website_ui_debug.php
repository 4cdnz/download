<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
if (!isset($config))
{
	http_response_code(403);
	die('Access denied');
}

$variable_flat = [];
variable_flat_recursive('localization', ['$lang' => $lang], '', 0);
$localization = $variable_flat;

$old_request = $_REQUEST;
require_once 'include/setup_smarty.php';
require_once 'include/setup_smarty_site.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/database_selectors.php';
require_once 'include/list_countries.php';
$_REQUEST = $old_request;

$website_ui_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"), ['allowed_classes' => false]);

$result = get_site_pages(array($page_id));
if (array_cnt($result) > 0)
{
	$page_info = $result[0];
} else
{
	die;
}

$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = str_replace(array('debug=true', '&&', '?&'), array('', '&', '?'), $request_uri);
$request_uri = trim($request_uri, '?&');

$status_code = '200';

$http_params = array();
foreach ($_REQUEST as $k => $v)
{
	if ($k == 'debug')
	{
		continue;
	}
	$param = array('name' => $k, 'value' => $v);
	$http_params[] = $param;
}

$htaccess_rules = array();
$htaccess_rows = explode("\n", @file_get_contents("$config[project_path]/.htaccess"));
foreach ($htaccess_rows as $row)
{
	$row = trim($row);
	if (strpos($row, 'RewriteRule') === 0 && (strpos($row, "/$page_id.php") !== false || strpos($row, " $page_id.php") !== false))
	{
		$row_pattern = explode(' ', $row);
		$test_request_uri = $request_uri;
		if (strpos($test_request_uri, '/') === 0)
		{
			$test_request_uri = substr($test_request_uri, 1);
		}

		$row_is_current = 0;
		if ($row_pattern[1] != '' && preg_match("($row_pattern[1])u", $test_request_uri))
		{
			$row_is_current = 1;
		}
		$htaccess_rules[] = array('rule' => $row, 'is_current' => $row_is_current);
	}
}

$session_values = array();
if (isset($_SESSION['user_id']))
{
	$session_values['user_id'] = $_SESSION['user_id'];

	if (isset($_SESSION['display_name']))
	{
		$session_values['display_name'] = $_SESSION['display_name'];
	}
	if (isset($_SESSION['last_login_date']))
	{
		$session_values['last_login_date'] = $_SESSION['last_login_date'];
	}
	if (isset($_SESSION['added_date']))
	{
		$session_values['added_date'] = $_SESSION['added_date'];
	}
	if (isset($_SESSION['avatar']))
	{
		$session_values['avatar'] = $_SESSION['avatar'];
	}
	if (isset($_SESSION['avatar_url']))
	{
		$session_values['avatar_url'] = $_SESSION['avatar_url'];
	}
	if (isset($_SESSION['cover']))
	{
		$session_values['cover'] = $_SESSION['cover'];
	}
	if (isset($_SESSION['cover_url']))
	{
		$session_values['cover_url'] = $_SESSION['cover_url'];
	}
	if (isset($_SESSION['status_id']))
	{
		$session_values['status_id'] = $_SESSION['status_id'];
	}
	if (isset($_SESSION['username']))
	{
		$session_values['username'] = $_SESSION['username'];
	}
	if (isset($_SESSION['birth_date']))
	{
		$session_values['birth_date'] = $_SESSION['birth_date'];
	}
	if (isset($_SESSION['age']))
	{
		$session_values['age'] = $_SESSION['age'];
	}
	if (isset($_SESSION['gender_id']))
	{
		$session_values['gender_id'] = $_SESSION['gender_id'];
	}
	if (isset($_SESSION['content_source_group_id']))
	{
		$session_values['content_source_group_id'] = $_SESSION['content_source_group_id'];
	}
	if (isset($_SESSION['is_trusted']))
	{
		$session_values['is_trusted'] = $_SESSION['is_trusted'];
	}
	if (isset($_SESSION['tokens_available']))
	{
		$session_values['tokens_available'] = $_SESSION['tokens_available'];
	}
	if (isset($_SESSION['unread_messages']))
	{
		$session_values['unread_messages'] = $_SESSION['unread_messages'];
	}
	if (isset($_SESSION['unread_invites']))
	{
		$session_values['unread_invites'] = $_SESSION['unread_invites'];
	}
	if (isset($_SESSION['unread_non_invites']))
	{
		$session_values['unread_non_invites'] = $_SESSION['unread_non_invites'];
	}
	if (isset($_SESSION['paid_access_hours_left']))
	{
		$session_values['paid_access_hours_left'] = $_SESSION['paid_access_hours_left'];
	}
	if (isset($_SESSION['paid_access_is_unlimited']))
	{
		$session_values['paid_access_is_unlimited'] = $_SESSION['paid_access_is_unlimited'];
	}
	if (isset($_SESSION['paid_access_internal_provider_id']))
	{
		$session_values['paid_access_internal_provider_id'] = $_SESSION['paid_access_internal_provider_id'];
	}
	if (isset($_SESSION['external_guid']))
	{
		$session_values['external_guid'] = $_SESSION['external_guid'];
	}
	if (isset($_SESSION['external_package_id']))
	{
		$session_values['external_package_id'] = $_SESSION['external_package_id'];
	}
	if (isset($_SESSION['playlists']) && array_cnt($_SESSION['playlists']) > 0)
	{
		$session_values['playlists'] = $_SESSION['playlists'];
	}
	if (isset($_SESSION['playlists_amount']) > 0)
	{
		$session_values['playlists_amount'] = $_SESSION['playlists_amount'];
	}
	if (isset($_SESSION['content_purchased']) && array_cnt($_SESSION['content_purchased']) > 0)
	{
		$session_values['content_purchased'] = $_SESSION['content_purchased'];
	}
	if (isset($_SESSION['content_purchased_amount']) > 0)
	{
		$session_values['content_purchased_amount'] = $_SESSION['content_purchased_amount'];
	}
	if (isset($_SESSION['favourite_videos_summary']) && array_cnt($_SESSION['favourite_videos_summary']) > 0)
	{
		$session_values['favourite_videos_summary'] = $_SESSION['favourite_videos_summary'];
	}
	if (isset($_SESSION['favourite_videos_amount']) > 0)
	{
		$session_values['favourite_videos_amount'] = $_SESSION['favourite_videos_amount'];
	}
	if (isset($_SESSION['favourite_albums_summary']) && array_cnt($_SESSION['favourite_albums_summary']) > 0)
	{
		$session_values['favourite_albums_summary'] = $_SESSION['favourite_albums_summary'];
	}
	if (isset($_SESSION['favourite_albums_amount']) > 0)
	{
		$session_values['favourite_albums_amount'] = $_SESSION['favourite_albums_amount'];
	}
	if (isset($_SESSION['subscriptions_amount']) > 0)
	{
		$session_values['subscriptions_amount'] = $_SESSION['subscriptions_amount'];
	}
	if (isset($_SESSION['user_info']))
	{
		$session_values['user_info'] = $_SESSION['user_info'];
	}
}

$variable_flat = array();
variable_flat_recursive("session", ['$smarty.session' => $session_values], '', 0);
$session_values = $variable_flat;

$runtime_params = array();
$temp = unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
foreach ($temp as $param)
{
	$var = trim($param['name']);
	if (isset($_SESSION['runtime_params'][$var]))
	{
		$runtime_params[$var] = $_SESSION['runtime_params'][$var];
	}
}

$templates_data = get_site_parsed_templates();
$template_info = $templates_data["$page_id.tpl"];

foreach ($show_admin_debug_block_data as &$block_info)
{
	$variable_flat = [];
	variable_flat_recursive("{$block_info['global_id']}/storage", [$block_info['block_uid'] => $block_info['storage']], '', 0);
	$block_info['storage'] = $variable_flat;

	$variable_flat = [];
	variable_flat_recursive("{$block_info['global_id']}/vars", $block_info['template_vars'], '', 0);
	$block_info['template_vars'] = $variable_flat;

	if (array_cnt($_POST) > 0 && $_POST['block_uid'] == $block_info['block_uid'])
	{
		$variable_flat = [];
		variable_flat_recursive("{$block_info['global_id']}/post", $_POST, 'smarty.post', 0);
		$block_info['template_vars'] = array_merge($block_info['template_vars'], $variable_flat);
	}

	foreach ($block_info['params'] as $name => $value)
	{
		if (strpos($name, 'var_') === 0 && isset($_REQUEST[$value]))
		{
			$block_info['params'][$name] = "$value ($_REQUEST[$value])";
		}
	}

	$block_template_info = $templates_data["$block_info[global_id].tpl"];
	if (isset($block_template_info))
	{
		$block_info['block_includes'] = get_site_includes_recursively($block_template_info);
	}

	if ($block_info['status_code'] != '' && $status_code == '200')
	{
		$status_code = $block_info['status_code'];
	}
}

$smarty = new mysmarty();
$smarty->assign_by_ref("config", $config);
$smarty->assign("page_status", $status_code);
$smarty->assign("page_id", $page_info['page_id']);
$smarty->assign("page_name", $page_info['title']);
$smarty->assign("page_external_id", $page_id);
$smarty->assign("page_request_uri", $request_uri);
$smarty->assign("page_http_params", $http_params);
$smarty->assign("session_values", $session_values);
$smarty->assign("runtime_params", $runtime_params);
$smarty->assign("htaccess_rules", $htaccess_rules);
$smarty->assign("localization", $localization);
$smarty->assign("blocks", $show_admin_debug_block_data);
$smarty->assign("lang", $lang);
$smarty->assign("admin_url", $config['admin_url'] ?? $config['project_url'] . '/admin');
$smarty->assign("page_includes", get_site_includes_recursively($template_info));
$smarty->display("website_ui_debug.tpl");

function variable_flat_recursive($block_uid, $item, $parent_key, $level)
{
	global $variable_flat;

	if (is_array($item))
	{
		ksort($item);
		$index = 0;
		$is_numeric_array = false;
		if (isset($item[0]))
		{
			$is_numeric_array = true;
		}
		foreach ($item as $k => $v)
		{
			$new_id = "$block_uid-$index";
			$key = $k;
			if ($parent_key <> '')
			{
				$key = "$parent_key.$k";
			}
			if ($key == 'config' || $key == 'storage')
			{
				continue;
			}
			if ($is_numeric_array && $index > 100)
			{
				$variable_flat[] = array('row_id' => $new_id, 'level' => $level, 'key' => "$key", 'value' => '...');
				break;
			}
			if (is_array($v))
			{
				$is_expandable = 1;
				if (array_cnt($v) == 0)
				{
					$value_replace = "Array (0)";
					$is_expandable = 0;
				} elseif (isset($v[0]))
				{
					$value_replace = "Array (" . array_cnt($v) . ")";
				} elseif (isset($v['title']))
				{
					$value_replace = "$v[title] (Object)";
				} else
				{
					$value_replace = "Object";
				}
				$variable_flat[] = array('row_id' => $new_id, 'level' => $level, 'key' => "$key", 'value' => $value_replace, 'is_expandable' => $is_expandable);
				variable_flat_recursive($new_id, $v, "$key", $level + 1);
				$index++;
			} else
			{
				$variable_flat[] = array('row_id' => $new_id, 'level' => $level, 'key' => "$key", 'value' => $v);
				variable_flat_recursive($new_id, $v, "$key", $level + 1);
				$index++;
			}

		}
	}
}
