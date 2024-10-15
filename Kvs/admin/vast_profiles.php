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

grid_presets_start($grid_presets, $page_name, 'player_advertising_profiles');

$list_categories = [];
$temp_categories = mr2array(sql_pr("select category_id, title from $config[tables_prefix]categories"));
foreach ($temp_categories as $k => $category)
{
	$list_categories[$category['category_id']] = $category['title'];
}

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc", $lang['system']['language_code']));
foreach ($list_countries as $k => $country)
{
	$list_countries[$country['country_code']] = $country['title'];
	unset($list_countries[$k]);
}

$list_devices_values = array(
		'pc' => $lang['settings']['vast_profile_field_devices_pc'],
		'phone' => $lang['settings']['vast_profile_field_devices_phone'],
		'tablet' => $lang['settings']['vast_profile_field_devices_tablet'],
);

$list_browsers_values = array(
	'chrome' => $lang['settings']['vast_profile_browsers_chrome'],
	'firefox' => $lang['settings']['vast_profile_browsers_firefox'],
	'safari' => $lang['settings']['vast_profile_browsers_safari'],
	'msie' => $lang['settings']['vast_profile_browsers_msie'],
	'opera' => $lang['settings']['vast_profile_browsers_opera'],
	'yandex' => $lang['settings']['vast_profile_browsers_yandex'],
	'uc' => $lang['settings']['vast_profile_browsers_uc'],
	'samsung' => $lang['settings']['vast_profile_browsers_samsung'],
	'bot' => $lang['settings']['vast_profile_browsers_bot'],
	'other' => $lang['settings']['vast_profile_browsers_other'],
);

$table_fields = array();
$table_fields[] = array('id' => 'url',        'title' => $lang['settings']['vast_profile_field_config_url'], 'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'devices',    'title' => $lang['settings']['vast_profile_field_devices'],    'is_default' => 1, 'type' => 'multi_choice', 'values' => $list_devices_values, 'value_all' => $lang['settings']['vast_profile_field_devices_all']);
$table_fields[] = array('id' => 'browsers',   'title' => $lang['settings']['vast_profile_field_browsers'],   'is_default' => 1, 'type' => 'multi_choice', 'values' => $list_browsers_values, 'value_all' => $lang['settings']['vast_profile_field_browsers_all']);
$table_fields[] = array('id' => 'categories', 'title' => $lang['settings']['vast_profile_field_categories'], 'is_default' => 1, 'type' => 'list');
$table_fields[] = array('id' => 'countries',  'title' => $lang['settings']['vast_profile_field_countries'],  'is_default' => 1, 'type' => 'list');
$table_fields[] = array('id' => 'referers',   'title' => $lang['settings']['vast_profile_field_referers'],   'is_default' => 1, 'type' => 'list');
$table_fields[] = array('id' => 'weight',     'title' => $lang['settings']['vast_profile_field_weight'],     'is_default' => 1, 'type' => 'number');

$sort_def_field = "title";
$sort_def_direction = "desc";
$sort_array = array();
foreach ($table_fields as $k => $field)
{
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

$table_key_name = 'profile_id';
$limit_providers = 10;
$profiles = get_vast_profiles();

$errors = null;

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

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

grid_presets_end($grid_presets, $page_name, 'player_advertising_profiles');

// =====================================================================================================================
// view log
// =====================================================================================================================

if ($_REQUEST['action'] == 'view_debug_log')
{
	foreach ($profiles as $profile)
	{
		if ($_REQUEST['id'] == $profile[$table_key_name])
		{
			$log_file = "debug_vast_profile_$profile[$table_key_name].txt";
			download_log_file("$config[project_path]/admin/logs/$log_file");
			die;
		}
	}
	die;
}

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if (in_array($_POST['action'], ['add_new_complete', 'change_complete']))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (validate_field('empty', $_POST['title'], $lang['settings']['vast_profile_field_title']))
	{
		foreach ($profiles as $profile)
		{
			if (mb_lowercase($_POST['title']) == mb_lowercase($profile['title']) && $_POST['item_id'] != $profile[$table_key_name])
			{
				$errors[] = get_aa_error('unique_field', $lang['settings']['vast_profile_field_title']);
				break;
			}
		}
	}
	for ($i = 0; $i < $limit_providers; $i++)
	{
		if (isset($_POST["is_provider_{$i}"]))
		{
			validate_field('empty', $_POST["provider_{$i}_url"], str_replace("%1%", $i + 1, $lang['settings']['vast_profile_divider_advertiser']) . " - " . $lang['settings']['vast_profile_field_config_url']);
			if (array_cnt($_POST["provider_{$i}_devices"]) == 0)
			{
				$errors[] = get_aa_error('required_field', str_replace("%1%", $i + 1, $lang['settings']['vast_profile_divider_advertiser']) . " - " . $lang['settings']['vast_profile_field_devices']);
			}
			if (array_cnt($_POST["provider_{$i}_browsers"]) == 0)
			{
				$errors[] = get_aa_error('required_field', str_replace("%1%", $i + 1, $lang['settings']['vast_profile_divider_advertiser']) . " - " . $lang['settings']['vast_profile_field_browsers']);
			}
			if ($_POST["provider_{$i}_weight"] != '' && $_POST["provider_{$i}_weight"] != '0')
			{
				validate_field('empty_int', $_POST["provider_{$i}_weight"], str_replace("%1%", $i + 1, $lang['settings']['vast_profile_divider_advertiser']) . " - " . $lang['settings']['vast_profile_field_weight']);
			}
		}
	}

	mkdir_recursive("$config[project_path]/admin/data/player/vast");

	$item_id = intval($_POST['item_id']);
	if ($_POST['action'] == 'add_new_complete')
	{
		$item_id = mt_rand(1, 1000000);
		for ($i = 0; $i < 99999; $i++)
		{
			if (isset($profiles[$item_id]))
			{
				$item_id = mt_rand(1, 1000000);
			}
		}
	}

	$profile_data_file = "$config[project_path]/admin/data/player/vast/vast_$item_id.dat";
	if ($_POST['action'] == 'add_new_complete')
	{
		if (!is_writable(dirname($profile_data_file)))
		{
			$errors[] = get_aa_error('filesystem_permission_write', dirname($profile_data_file));
		}
	} else
	{
		if (!is_writable($profile_data_file))
		{
			$errors[] = get_aa_error('filesystem_permission_write', $profile_data_file);
		}
	}

	if (!is_array($errors))
	{
		$profile_info = [$table_key_name => $item_id, 'title' => $_POST['title'], 'is_debug_enabled' => intval($_POST['is_debug_enabled']), 'providers' => []];
		for ($i = 0; $i < $limit_providers; $i++)
		{
			if (array_cnt($_POST["provider_{$i}_devices"]) == array_cnt($list_devices_values))
			{
				$_POST["provider_{$i}_devices"] = [];
			}
			if (array_cnt($_POST["provider_{$i}_browsers"]) == array_cnt($list_browsers_values))
			{
				$_POST["provider_{$i}_browsers"] = [];
			}

			$profile_info['providers'][] = [
				'is_enabled' => intval($_POST["is_provider_{$i}"]),
				'url' => trim($_POST["provider_{$i}_url"]),
				'alt_url' => trim($_POST["provider_{$i}_alt_url"]),
				'devices' => $_POST["provider_{$i}_devices"],
				'browsers' => $_POST["provider_{$i}_browsers"],
				'categories' => trim(implode(',', $_POST["provider_{$i}_categories"] ?? [])),
				'exclude_categories' => trim(implode(',', $_POST["provider_{$i}_exclude_categories"] ?? [])),
				'countries' => trim(implode(',', $_POST["provider_{$i}_countries"] ?? [])),
				'exclude_countries' => trim(implode(',', $_POST["provider_{$i}_exclude_countries"] ?? [])),
				'referers' => trim($_POST["provider_{$i}_referers"]),
				'exclude_referers' => trim($_POST["provider_{$i}_exclude_referers"]),
				'weight' => intval($_POST["provider_{$i}_weight"])
			];
		}
		if (intval($_POST['is_debug_enabled']) == 0)
		{
			@unlink("$config[project_path]/admin/logs/debug_vast_profile_$item_id.txt");
		}
		file_put_contents($profile_data_file, serialize($profile_info), LOCK_EX);

		$admin_notification_objects = 0;
		foreach ($profiles as $profile)
		{
			if ($profile[$table_key_name] == $item_id)
			{
				if (intval($_POST['is_debug_enabled']) == 1)
				{
					$admin_notification_objects++;
				}
			} else
			{
				if ($profile['is_debug_enabled'] == 1)
				{
					$admin_notification_objects++;
				}
			}
		}
		add_admin_notification('settings.vast_profiles.debug', $admin_notification_objects);

		if ($_POST['action'] == 'add_new_complete')
		{
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// usages
// =====================================================================================================================

$profiles_usages = [];

$player_files = get_player_data_files();
foreach ($player_files as $player_file)
{
	$player_data = @unserialize(file_get_contents($player_file['file']), ['allowed_classes' => false]);
	foreach ($profiles as $profile)
	{
		if ($player_data['pre_roll_vast_provider'] === "vast_profile_$profile[$table_key_name]")
		{
			$profiles_usages[$profile[$table_key_name]][] = ['type' => 'pre', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed'])];
		}
		if ($player_data['post_roll_vast_provider'] === "vast_profile_$profile[$table_key_name]")
		{
			$profiles_usages[$profile[$table_key_name]][] = ['type' => 'post', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed'])];
		}
		if ($player_data['video_click_url_source'] === "vast_profile_$profile[$table_key_name]")
		{
			$profiles_usages[$profile[$table_key_name]][] = ['type' => 'video_click', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed'])];
		}
		if ($player_data['popunder_url_source'] === "vast_profile_$profile[$table_key_name]")
		{
			$profiles_usages[$profile[$table_key_name]][] = ['type' => 'popunder', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed'])];
		}
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
		if ($_REQUEST['batch_action'] == 'delete')
		{
			foreach ($_REQUEST['row_select'] as $profile_id)
			{
				if (isset($profiles[$profile_id]) && !isset($profiles_usages[$profile_id]))
				{
					unlink("$config[project_path]/admin/data/player/vast/vast_$profile_id.dat");
					@unlink("$config[project_path]/admin/logs/debug_vast_profile_$profile_id.txt");
					unset($profiles[$profile_id]);
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'enable_debug')
		{
			foreach ($_REQUEST['row_select'] as $profile_id)
			{
				if (isset($profiles[$profile_id]))
				{
					$profiles[$profile_id]['is_debug_enabled'] = 1;
					file_put_contents("$config[project_path]/admin/data/player/vast/vast_$profile_id.dat", serialize($profiles[$profile_id]), LOCK_EX);
				}
			}

			$admin_notification_objects = 0;
			foreach ($profiles as $profile)
			{
				if ($profile['is_debug_enabled'] == 1)
				{
					$admin_notification_objects++;
				}
			}
			add_admin_notification('settings.vast_profiles.debug', $admin_notification_objects);

			$_SESSION['messages'][] = $lang['common']['success_message_debug_enabled'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'disable_debug')
		{
			foreach ($_REQUEST['row_select'] as $profile_id)
			{
				if (isset($profiles[$profile_id]))
				{
					unset($profiles[$profile_id]['is_debug_enabled']);
					file_put_contents("$config[project_path]/admin/data/player/vast/vast_$profile_id.dat", serialize($profiles[$profile_id]), LOCK_EX);
					@unlink("$config[project_path]/admin/logs/debug_vast_profile_$profile_id.txt");
				}
			}

			$admin_notification_objects = 0;
			foreach ($profiles as $profile)
			{
				if ($profile['is_debug_enabled'] == 1)
				{
					$admin_notification_objects++;
				}
			}
			add_admin_notification('settings.vast_profiles.debug', $admin_notification_objects);

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
	$item_id = intval($_GET['item_id']);

	$_POST = $profiles[$item_id];
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}

	$_POST['usages'] = $profiles_usages[$item_id];

	foreach ($_POST['providers'] as $provider_id => $provider)
	{
		if (strlen($provider['categories']) == 0)
		{
			$_POST['providers'][$provider_id]['categories'] = [];
		} else
		{
			$_POST['providers'][$provider_id]['categories'] = explode(',', $provider['categories']);
		}
		if (strlen($provider['exclude_categories']) == 0)
		{
			$_POST['providers'][$provider_id]['exclude_categories'] = [];
		} else
		{
			$_POST['providers'][$provider_id]['exclude_categories'] = explode(',', $provider['exclude_categories']);
		}
		if (strlen($provider['countries']) == 0)
		{
			$_POST['providers'][$provider_id]['countries'] = [];
		} else
		{
			$_POST['providers'][$provider_id]['countries'] = explode(',', $provider['countries']);
		}
		if (strlen($provider['exclude_countries']) == 0)
		{
			$_POST['providers'][$provider_id]['exclude_countries'] = [];
		} else
		{
			$_POST['providers'][$provider_id]['exclude_countries'] = explode(',', $provider['exclude_countries']);
		}
	}

	$profile_data_file = "$config[project_path]/admin/data/player/vast/vast_$item_id.dat";
	if (!unserialize(file_get_contents($profile_data_file)))
	{
		$_POST['errors'][] = get_aa_error('player_vast_profile_format', $profile_data_file);
	}
	if (!is_writable($profile_data_file))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $profile_data_file);
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$data = $profiles;
if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	foreach ($data as $profile_id => $profile)
	{
		if (!mb_contains($profile['title'], $_SESSION['save'][$page_name]['se_text']))
		{
			foreach ($profile['providers'] as $provider_id => $provider)
			{
				if (!mb_contains($provider['url'], $_SESSION['save'][$page_name]['se_text']) && !mb_contains($provider['alt_url'], $_SESSION['save'][$page_name]['se_text'])
						&& !mb_contains($provider['referers'], $_SESSION['save'][$page_name]['se_text']) && !mb_contains($provider['exclude_referers'], $_SESSION['save'][$page_name]['se_text']))
				{
					unset($data[$profile_id]['providers'][$provider_id]);
				}
			}
			if (array_cnt($data[$profile_id]['providers']) == 0)
			{
				unset($data[$profile_id]);
			}
		}
	}
}

foreach ($data as $profile_id => $profile)
{
	foreach ($profile['providers'] as $provider_id => $provider)
	{
		if (intval($provider['is_enabled']) == 1)
		{
			$provider_categories = [];
			$provider['categories'] = array_map('trim', explode(',', $provider['categories']));
			$provider['exclude_categories'] = array_map('trim', explode(',', $provider['exclude_categories']));
			foreach ($provider['categories'] as $category_id)
			{
				if ($category_id && isset($list_categories[$category_id]))
				{
					$provider_categories[] = ['title' => "+$list_categories[$category_id]"];
				}
			}
			foreach ($provider['exclude_categories'] as $category_id)
			{
				if ($category_id && isset($list_categories[$category_id]))
				{
					$provider_categories[] = ['title' => "-$list_categories[$category_id]"];
				}
			}
			if (array_cnt($provider_categories) == 0)
			{
				$provider_categories[] = ['title' => $lang['settings']['vast_profile_field_categories_all']];
			}
			$data[$profile_id]['providers'][$provider_id]['categories'] = $provider_categories;

			$provider_countries = [];
			$provider['countries'] = array_map('trim', explode(',', $provider['countries']));
			$provider['exclude_countries'] = array_map('trim', explode(',', $provider['exclude_countries']));
			foreach ($provider['countries'] as $country_code)
			{
				if ($country_code && isset($list_countries[$country_code]))
				{
					$provider_countries[] = ['title' => "+$list_countries[$country_code]"];
				}
			}
			foreach ($provider['exclude_countries'] as $country_code)
			{
				if ($country_code && isset($list_countries[$country_code]))
				{
					$provider_countries[] = ['title' => "-$list_countries[$country_code]"];
				}
			}
			if (array_cnt($provider_countries) == 0)
			{
				$provider_countries[] = ['title' => $lang['settings']['vast_profile_field_countries_all']];
			}
			$data[$profile_id]['providers'][$provider_id]['countries'] = $provider_countries;

			$provider_referers = [];
			$provider['referers'] = array_map('trim', explode("\n", $provider['referers']));
			$provider['exclude_referers'] = array_map('trim', explode("\n", $provider['exclude_referers']));
			foreach ($provider['referers'] as $referer)
			{
				if ($referer)
				{
					$provider_referers[] = ['title' => "+$referer"];
				}
			}
			foreach ($provider['exclude_referers'] as $referer)
			{
				if ($referer)
				{
					$provider_referers[] = ['title' => "-$referer"];
				}
			}
			if (array_cnt($provider_referers) == 0)
			{
				$provider_referers[] = ['title' => $lang['settings']['vast_profile_field_referers_all']];
			}
			$data[$profile_id]['providers'][$provider_id]['referers'] = $provider_referers;
		} else
		{
			unset($data[$profile_id]['providers'][$provider_id]);
		}
	}

	$data[$profile_id]['usages'] = $profiles_usages[$profile_id];

	$profile_data_file = "$config[project_path]/admin/data/player/vast/vast_$profile_id.dat";
	if (!unserialize(file_get_contents($profile_data_file)))
	{
		$data[$profile_id]['has_errors'] = 1;
	} elseif (!is_writable($profile_data_file))
	{
		$data[$profile_id]['has_warnings'] = 1;
	}
}

if ($_GET['action'] == '')
{
	if (isset($_SESSION['admin_notifications']['list']['settings.vast_profiles.debug']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.vast_profiles.debug']['title'];
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('limit_providers', $limit_providers);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_countries', $list_countries);

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
$smarty->assign('total_num', array_cnt($data));
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['vast_profile_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['vast_profile_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_vast_profiles_list']);
}

$smarty->display("layout.tpl");
