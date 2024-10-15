<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_admin.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc", $lang['system']['language_code']));
foreach ($list_countries as $k => $country)
{
	$list_countries[$country['country_code']] = $country['title'];
	unset($list_countries[$k]);
}

$list_satellites = mr2array(sql("select * from $config[tables_prefix]admin_satellites"));
foreach ($list_satellites as &$satellite)
{
	$satellite['host'] = str_replace('www.', '', parse_url($satellite['project_url'], PHP_URL_HOST));
}
unset($satellite);

$list_satellite_values = [
		'main' => $lang['users']['card_bill_package_field_limit_satellite_main']
];
foreach ($list_satellites as $satellite)
{
	$list_satellite_values[$satellite['multi_prefix']] = $satellite['host'];
}

$list_status_values = array(
		0 => $lang['users']['card_bill_package_field_status_disabled'],
		1 => $lang['users']['card_bill_package_field_status_active'],
);

$list_scope_values = array(
		0 => $lang['users']['card_bill_package_field_scope_all'],
		1 => $lang['users']['card_bill_package_field_scope_signup'],
		2 => $lang['users']['card_bill_package_field_scope_upgrade'],
);

$table_fields = array();
$table_fields[] = array('id' => 'package_id',           'title' => $lang['users']['card_bill_package_field_id'],               'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',                'title' => $lang['users']['card_bill_package_field_title'],            'is_default' => 1, 'type' => 'text', 'value_postfix' => 'default_text');
$table_fields[] = array('id' => 'status_id',            'title' => $lang['users']['card_bill_package_field_status'],           'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'external_id',          'title' => $lang['users']['card_bill_package_field_external_id'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'scope_id',             'title' => $lang['users']['card_bill_package_field_scope'],            'is_default' => 1, 'type' => 'choice', 'values' => $list_scope_values);
if (array_cnt($list_satellites) > 0)
{
	$table_fields[] = array('id' => 'satellite_prefix', 'title' => $lang['users']['card_bill_package_field_limit_satellite'],  'is_default' => 1, 'type' => 'choice', 'values' => $list_satellite_values);
}
$table_fields[] = array('id' => 'access_type',          'title' => $lang['users']['card_bill_package_field_access_type'],      'is_default' => 1, 'type' => 'longtext');
$table_fields[] = array('id' => 'countries',            'title' => $lang['users']['card_bill_package_field_countries'],        'is_default' => 1, 'type' => 'list');
$table_fields[] = array('id' => 'payment_page_url',     'title' => $lang['users']['card_bill_package_field_payment_page_url'], 'is_default' => 0, 'type' => 'url');
$table_fields[] = array('id' => 'sort_id',              'title' => $lang['users']['card_bill_package_field_order'],            'is_default' => 1, 'type' => 'number');

$sort_def_field = "sort_id";
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

$search_fields = array();
$search_fields[] = array('id' => 'title',            'title' => $lang['users']['card_bill_package_field_title']);
$search_fields[] = array('id' => 'external_id',      'title' => $lang['users']['card_bill_package_field_external_id']);
$search_fields[] = array('id' => 'payment_page_url', 'title' => $lang['users']['card_bill_package_field_payment_page_url']);

$table_name = "$config[tables_prefix]card_bill_packages";
$table_key_name = "package_id";

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

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
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
			$where_search .= " or $table_name.$search_field[id] like '%$q%'";
		}
	}
	$where .= " and ($where_search) ";
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'] . ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if ($_POST['action'] == 'change_provider_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$item_id = intval($_POST['item_id']);
	$status_id = intval($_POST['status_id']);
	if ($status_id != 1)
	{
		$status_id = 0;
	}

	$provider_data = mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where provider_id=?", $item_id));
	validate_field('empty', $provider_data['internal_id'], $lang['users']['card_bill_config_field_processor_type']);

	if ($provider_data['cf_pkg_setprice'] == 1 && $status_id == 1 && $provider_data['internal_id'] != 'tokens')
	{
		validate_field('empty', $_POST['signature'], $lang['users']['card_bill_config_field_signature']);
	}

	if (($provider_data['internal_id'] == 'ccbill' || $provider_data['internal_id'] == 'ccbilldyn') && $status_id == 1)
	{
		validate_field('empty', $_POST['account_id'], $lang['users']['card_bill_config_field_datalink_account']);
		validate_field('empty', $_POST['sub_account_id'], $lang['users']['card_bill_config_field_datalink_subaccount']);
		validate_field('empty', $_POST['datalink_username'], $lang['users']['card_bill_config_field_datalink_username']);
		validate_field('empty', $_POST['datalink_password'], $lang['users']['card_bill_config_field_datalink_password']);
	}
	if ($provider_data['internal_id'] == 'nats' || $provider_data['internal_id'] == 'natsum')
	{
		if ($_POST['datalink_url'] != '' || $_POST['datalink_username'] != '' || $_POST['datalink_password'] != '')
		{
			validate_field('url', $_POST['datalink_url'], $lang['users']['card_bill_config_field_datalink_url']);
			validate_field('empty', $_POST['datalink_username'], $lang['users']['card_bill_config_field_datalink_username']);
			validate_field('empty', $_POST['datalink_password'], $lang['users']['card_bill_config_field_datalink_password']);
		}
	}

	$package_ids = mr2array_list(sql_pr("select package_id from $config[tables_prefix]card_bill_packages where provider_id=?", $item_id));

	$has_title_error = 0;
	$has_order_error = 0;
	$active_packages = 0;
	foreach ($package_ids as $package_id)
	{
		if (intval($_POST["delete_$package_id"]) == 1)
		{
			continue;
		}
		if ($_POST["title_$package_id"] == '' && $has_title_error == 0)
		{
			$errors[] = get_aa_error('bill_config_package_field_required', $lang['users']['card_bill_config_divider_packages'] . " - " . $lang['users']['card_bill_package_field_title']);
			$has_title_error = 1;
		}
		if (trim(intval(trim($_POST["order_$package_id"]))) != trim($_POST["order_$package_id"]) && $has_order_error == 0)
		{
			$errors[] = get_aa_error('bill_config_package_field_integer', $lang['users']['card_bill_config_divider_packages'] . " - " . $lang['users']['card_bill_package_field_order']);
			$has_order_error = 1;
		}
		if (intval($_POST["is_active_$package_id"]) == 1)
		{
			$active_packages++;
		}
	}

	if (!is_array($errors))
	{
		if ($status_id == 1 && $active_packages == 0)
		{
			$errors[] = get_aa_error('bill_config_activation');
		}
	}

	if (!is_array($errors))
	{
		$is_default = 0;
		if ($status_id == 1)
		{
			$is_default = intval($_POST['is_default']);
			if ($is_default == 0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]card_bill_providers where status_id=1 and is_default=1 and provider_id!=?", $item_id)) == 0)
			{
				$is_default = 1;
			}
		}

		$options = '';
		if (is_array($_POST['options']))
		{
			$options = serialize($_POST['options']);
		}

		sql_update("update $config[tables_prefix]card_bill_providers set status_id=?, is_default=?, postback_reseller_param=?, postback_repost_url=?, postback_ip_protection=?, postback_username=?, postback_password=?, account_id=?, sub_account_id=?, datalink_url=?, datalink_username=?, datalink_password=?, datalink_use_ip=?, signature=?, options=? where provider_id=?",
				$status_id, $is_default, nvl($_POST['postback_reseller_param']), nvl($_POST['postback_repost_url']), nvl($_POST['postback_ip_protection']), nvl($_POST['postback_username']), nvl($_POST['postback_password']), nvl($_POST['account_id']), nvl($_POST['sub_account_id']), nvl($_POST['datalink_url']), nvl($_POST['datalink_username']), nvl($_POST['datalink_password']), nvl($_POST['datalink_use_ip']), nvl($_POST['signature']), $options, $item_id);
		if ($is_default == 1)
		{
			sql_update("update $config[tables_prefix]card_bill_providers set is_default=0 where provider_id!=?", $item_id);
		}

		foreach ($package_ids as $package_id)
		{
			if (intval($_POST["delete_$package_id"]) == 1)
			{
				sql_delete("delete from $config[tables_prefix]card_bill_packages where package_id=?", $package_id);
			} else
			{
				sql_update("update $config[tables_prefix]card_bill_packages set title=?, status_id=?, is_default=?, sort_id=? where package_id=?", $_POST["title_$package_id"], intval($_POST["is_active_$package_id"]), ($_POST['default_package_id'] == $package_id ? 1 : 0), intval($_POST["order_$package_id"]), $package_id);
			}
		}

		check_default_billing_package();

		$_SESSION['messages'][] = $lang['common']['success_message_modified'];

		if (isset($_POST['save_and_add_package']))
		{
			return_ajax_success("$page_name?action=add_new&provider_id=$item_id");
		}
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$item_id = intval($_POST['item_id']);

	if ($item_id > 0)
	{
		$provider_data = mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where provider_id=(select provider_id from $config[tables_prefix]card_bill_packages where package_id=?)", $item_id));
	} else
	{
		$provider_data = mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where provider_id=?", intval($_POST['provider_id'])));
	}

	validate_field('empty', $provider_data['provider_id'], $lang['users']['card_bill_config_field_bill_type']);

	validate_field('empty', $_POST['title'], $lang['users']['card_bill_package_field_title']);
	validate_field('uniq', $_POST['external_id'], $lang['users']['card_bill_package_field_external_id'], ['field_name_in_base' => 'external_id']);

	if (intval($_POST['access_type']) == 1)
	{
		$_POST['duration_initial'] = 0;
		$_POST['duration_rebill'] = 0;
		$_POST['tokens'] = 0;
	} elseif (intval($_POST['access_type']) == 2)
	{
		validate_field('empty_int', $_POST['duration_initial'], $lang['users']['card_bill_package_field_access_type']);
		if ($_POST['duration_rebill'] != '')
		{
			validate_field('empty_int', $_POST['duration_rebill'], $lang['users']['card_bill_package_field_access_type']);
		}
		$_POST['tokens'] = 0;
	} elseif (intval($_POST['access_type']) == 3)
	{
		validate_field('empty_int', $_POST['tokens'], $lang['users']['card_bill_package_field_access_type']);
		$_POST['duration_initial'] = 0;
		$_POST['duration_rebill'] = 0;
	}

	if ($provider_data['cf_pkg_setprice'] == 1)
	{
		if (validate_field('empty', $_POST['price_initial'], $lang['users']['card_bill_package_field_price']))
		{
			if (!preg_match("|^[0-9\.]+$|is", $_POST['price_initial']))
			{
				$errors[] = get_aa_error('bill_package_price', $lang['users']['card_bill_package_field_price']);
			} elseif (intval($_POST['duration_rebill']) != 0)
			{
				if (validate_field('empty', $_POST['price_rebill'], $lang['users']['card_bill_package_field_price']))
				{
					if (!preg_match("|^[0-9\.]+$|is", $_POST['price_rebill']))
					{
						$errors[] = get_aa_error('bill_package_price', $lang['users']['card_bill_package_field_price']);
					}
				}
			}
		}
	}

	if ($provider_data['internal_id'] != 'tokens')
	{
		validate_field('url', $_POST['payment_page_url'], $lang['users']['card_bill_package_field_payment_page_url']);
	}
	if ($_POST['oneclick_page_url'] != '')
	{
		validate_field('url', $_POST['oneclick_page_url'], $lang['users']['card_bill_package_field_oneclick_page_url']);
	}

	if (!is_array($errors))
	{
		$status_id = intval($_POST["status_id"]);
		if ($status_id != 1)
		{
			$status_id = 0;
		}

		$_POST['include_countries'] = strtolower(trim(implode(',', $_POST['include_countries'] ?? [])));
		$_POST['exclude_countries'] = strtolower(trim(implode(',', $_POST['exclude_countries'] ?? [])));

		if ($_POST['action'] == 'add_new_complete')
		{
			$sort_id = mr2number(sql_pr("select max(sort_id) from $table_name where provider_id=?", $provider_data['provider_id'])) + 1;
			$item_id = sql_insert("insert into $table_name set status_id=?, scope_id=?, duration_initial=?, duration_rebill=?, tokens=?, title=?, price_initial=?, price_initial_currency=?, price_rebill=?, price_rebill_currency=?, payment_page_url=?, oneclick_page_url=?, include_countries=?, exclude_countries=?, external_id=?, satellite_prefix=?, provider_id=?, sort_id=?",
					$status_id, intval($_POST["scope_id"]), intval($_POST["duration_initial"]), intval($_POST["duration_rebill"]), intval($_POST["tokens"]), nvl($_POST["title"]), nvl($_POST["price_initial"]), nvl($_POST["price_initial_currency"]), nvl($_POST["price_rebill"]), nvl($_POST["price_rebill_currency"]), nvl($_POST["payment_page_url"]), nvl($_POST["oneclick_page_url"]), nvl($_POST["include_countries"]), nvl($_POST["exclude_countries"]), nvl($_POST['external_id']), nvl($_POST['satellite_prefix']), $provider_data['provider_id'], $sort_id
			);
		} else
		{
			sql_update("update $table_name set status_id=?, scope_id=?, duration_initial=?, duration_rebill=?, tokens=?, title=?, price_initial=?, price_initial_currency=?, price_rebill=?, price_rebill_currency=?, payment_page_url=?, oneclick_page_url=?, include_countries=?, exclude_countries=?, external_id=?, satellite_prefix=? where $table_key_name=?",
					$status_id, intval($_POST["scope_id"]), intval($_POST["duration_initial"]), intval($_POST["duration_rebill"]), intval($_POST["tokens"]), nvl($_POST["title"]), nvl($_POST["price_initial"]), nvl($_POST["price_initial_currency"]), nvl($_POST["price_rebill"]), nvl($_POST["price_rebill_currency"]), nvl($_POST["payment_page_url"]), nvl($_POST["oneclick_page_url"]), nvl($_POST["include_countries"]), nvl($_POST["exclude_countries"]), nvl($_POST['external_id']), nvl($_POST['satellite_prefix']), $item_id
			);
		}

		check_default_billing_package();

		$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		if ($_POST['action'] == 'add_new_complete' && isset($_POST['save_default']))
		{
			return_ajax_success("$page_name?action=change_provider&item_id=$provider_data[provider_id]", 1);
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
			$package_ids = mr2array_list(sql_pr("select package_id from $config[tables_prefix]card_bill_packages where package_id in ($row_select)"));
			if (array_cnt($package_ids) > 0)
			{
				$package_ids_str = implode(",", $package_ids);
				$provider_ids = mr2array_list(sql_pr("select distinct prv.provider_id from $config[tables_prefix]card_bill_packages pkg inner join $config[tables_prefix]card_bill_providers prv using (provider_id) where pkg.package_id in ($package_ids_str) and prv.status_id=1"));
				foreach ($provider_ids as $provider_id)
				{
					if (is_array($errors))
					{
						break;
					}
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]card_bill_packages pkg inner join $config[tables_prefix]card_bill_providers prv using (provider_id) where prv.provider_id=? and pkg.status_id=1 and pkg.package_id not in ($package_ids_str)", $provider_id)) == 0)
					{
						$errors[] = get_aa_error('bill_config_package_removal');
					}
				}

				if (!is_array($errors))
				{
					sql_delete("delete from $config[tables_prefix]card_bill_packages where package_id in ($package_ids_str)");
				} else
				{
					return_ajax_errors($errors);
				}
			}
			check_default_billing_package();

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

if ($_GET['action'] == 'change_provider')
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where provider_id=?", $item_id));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	$_POST['packages'] = mr2array(sql_pr("select * from $config[tables_prefix]card_bill_packages where provider_id=? order by sort_id asc", $item_id));
	foreach ($_POST['packages'] as &$access_package)
	{
		$access_package['countries'] = [];
		$include_countries = array_map('trim', explode(',', strtolower($access_package['include_countries'])));
		$exclude_countries = array_map('trim', explode(',', strtolower($access_package['exclude_countries'])));
		foreach ($include_countries as $country_code)
		{
			if ($country_code && isset($list_countries[$country_code]))
			{
				$access_package['countries'][] = ['title' => "+$list_countries[$country_code]"];
			}
		}
		foreach ($exclude_countries as $country_code)
		{
			if ($country_code && isset($list_countries[$country_code]))
			{
				$access_package['countries'][] = ['title' => "-$list_countries[$country_code]"];
			}
		}
		if (array_cnt($access_package['countries']) == 0)
		{
			$access_package['countries'][] = ['title' => $lang['settings']['vast_profile_field_countries_all']];
		}
	}
	unset($access_package);

	if ($_POST['options'] != '')
	{
		$_POST['options'] = @unserialize($_POST['options']) ?: [];
	} else
	{
		$_POST['options'] = [];
	}

	$provider_internal_id = $_POST['internal_id'];
	if ($provider_internal_id && is_file("$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php"))
	{
		require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";
		require_once "$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php";
		$payment_processor = KvsPaymentProcessorFactory::create_instance($provider_internal_id);
		if ($payment_processor instanceof KvsPaymentProcessor)
		{
			$_POST["example_payment_url"] = $payment_processor->get_example_payment_url();
			$_POST["example_oneclick_url"] = $payment_processor->get_example_oneclick_url();
		}
	}
}

if ($_GET['action'] == 'change')
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_packages where package_id=?", $item_id));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	$_POST['provider'] = mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where provider_id=?", $_POST['provider_id']));

	$provider_internal_id = $_POST['provider']['internal_id'];
	if ($provider_internal_id && is_file("$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php"))
	{
		require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";
		require_once "$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php";
		$payment_processor = KvsPaymentProcessorFactory::create_instance($provider_internal_id);
		if ($payment_processor instanceof KvsPaymentProcessor)
		{
			$_POST['provider']["example_payment_url"] = $payment_processor->get_example_payment_url();
			$_POST['provider']["example_oneclick_url"] = $payment_processor->get_example_oneclick_url();
		}
	}

	if (strlen($_POST['include_countries']) == 0)
	{
		$_POST['include_countries'] = [];
	} else
	{
		$_POST['include_countries'] = explode(',', strtolower($_POST['include_countries']));
	}
	if (strlen($_POST['exclude_countries']) == 0)
	{
		$_POST['exclude_countries'] = [];
	} else
	{
		$_POST['exclude_countries'] = explode(',', strtolower($_POST['exclude_countries']));
	}
}

if ($_GET['action'] == 'add_new')
{
	$provider_data = mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where provider_id=?", intval($_GET['provider_id'])));
	if (empty($provider_data))
	{
		header("Location: $page_name");
		die;
	}

	$_POST['status_id'] = 1;

	$_POST['provider'] = $provider_data;

	$provider_internal_id = $_POST['provider']['internal_id'];
	if ($provider_internal_id && is_file("$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php"))
	{
		require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";
		require_once "$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php";
		$payment_processor = KvsPaymentProcessorFactory::create_instance($provider_internal_id);
		if ($payment_processor instanceof KvsPaymentProcessor)
		{
			$_POST['provider']["example_payment_url"] = $payment_processor->get_example_payment_url();
			$_POST['provider']["example_oneclick_url"] = $payment_processor->get_example_oneclick_url();
		}
	}
	if ($_POST['provider']['cf_pkg_setprice'] == 1)
	{
		$_POST['external_id'] = md5(time() . mt_rand(0, 10000000));
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

if ($_GET['action'] == '')
{
	$data = mr2array(sql("select * from $config[tables_prefix]card_bill_providers order by status_id desc, is_default desc, title asc"));
	foreach ($data as $k => $v)
	{
		$internal_id = $data[$k]['internal_id'];
		$data[$k]['packages'] = mr2array(sql_pr("select * from $config[tables_prefix]card_bill_packages where provider_id=? $where order by $sort_by", $v['provider_id']));

		foreach ($data[$k]['packages'] as &$access_package)
		{
			$access_package['countries'] = [];
			$include_countries = array_map('trim', explode(',', strtolower($access_package['include_countries'])));
			$exclude_countries = array_map('trim', explode(',', strtolower($access_package['exclude_countries'])));
			foreach ($include_countries as $country_code)
			{
				if ($country_code && isset($list_countries[$country_code]))
				{
					$access_package['countries'][] = ['title' => "+$list_countries[$country_code]"];
				}
			}
			foreach ($exclude_countries as $country_code)
			{
				if ($country_code && isset($list_countries[$country_code]))
				{
					$access_package['countries'][] = ['title' => "-$list_countries[$country_code]"];
				}
			}
			if (array_cnt($access_package['countries']) == 0)
			{
				$access_package['countries'][] = ['title' => $lang['settings']['vast_profile_field_countries_all']];
			}
			if ($access_package['is_default'] == 1)
			{
				$access_package['default_text'] = '(' . $lang['users']['card_bill_package_field_default'] . ')';
			}
			if ($access_package['tokens'] > 0)
			{
				$access_package['access_type'] = str_replace(['%1%', '%2%', '%3%'], [$access_package['tokens'], $access_package['price_initial'], $access_package['price_initial_currency']], $v['cf_pkg_setprice'] == 1 ? $lang['users']['card_bill_package_field_access_type_tokens_short2'] : $lang['users']['card_bill_package_field_access_type_tokens_short']);
			} elseif ($access_package['duration_initial'] == 0)
			{
				$access_package['access_type'] = str_replace(['%2%', '%3%'], [$access_package['price_initial'], $access_package['price_initial_currency']], $v['cf_pkg_setprice'] == 1 ? $lang['users']['card_bill_package_field_access_type_unlimited2'] : $lang['users']['card_bill_package_field_access_type_unlimited']);
			} elseif ($access_package['duration_rebill'] > 0)
			{
				$access_package['access_type'] = str_replace(['%1%', '%2%', '%3%', '%4%', '%5%', '%6%'], [$access_package['duration_initial'], $access_package['price_initial'], $access_package['price_initial_currency'], $access_package['duration_rebill'], $access_package['price_rebill'], $access_package['price_rebill_currency']], $v['cf_pkg_setprice'] == 1 ? $lang['users']['card_bill_package_field_access_type_duration_recurring_short2'] : $lang['users']['card_bill_package_field_access_type_duration_recurring_short']);
			} else
			{
				$access_package['access_type'] = str_replace(['%1%', '%2%', '%3%'], [$access_package['duration_initial'], $access_package['price_initial'], $access_package['price_initial_currency']], $v['cf_pkg_setprice'] == 1 ? $lang['users']['card_bill_package_field_access_type_duration_short2'] : $lang['users']['card_bill_package_field_access_type_duration_short']);
			}
		}
		unset($access_package);
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();

$smarty->assign('list_satellites', $list_satellites);
$smarty->assign('list_countries', $list_countries);

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', array_cnt($data));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change_provider')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['users']['card_bill_config_edit']));
	$smarty->assign('supports_popups', 1);
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['users']['card_bill_package_add']);
	$smarty->assign('supports_popups', 1);
} elseif ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%2%", $_POST['provider']['title'], str_replace("%1%", $_POST['title'], $lang['users']['card_bill_package_edit'])));
	$smarty->assign('supports_popups', 1);
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_card_billing']);
}

$smarty->display("layout.tpl");
