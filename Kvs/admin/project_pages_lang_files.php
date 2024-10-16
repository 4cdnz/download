<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions_admin.php');
require_once('include/functions.php');
require_once('include/check_access.php');

// =====================================================================================================================
// initialization
// =====================================================================================================================

$langs_dir = "$config[project_path]/langs";

$languages = [];

$default_lang = '';
$alternate_languages = [];
if (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	if (strpos(file_get_contents("$config[project_path]/admin/data/config/theme.xml"), 'field id="theme.lang"') !== false)
	{
		if (is_file("$langs_dir/default.lang"))
		{
			$file = fopen("$langs_dir/default.lang", 'r');
			while (($row = fgets($file)) !== false)
			{
				$row = trim($row);
				$pair = explode('=', $row, 2);
				if (array_cnt($pair) == 2)
				{
					if (trim($pair[0]) == 'theme.lang')
					{
						$default_lang = trim($pair[1]);
						continue;
					}
					if (trim($pair[0]) == 'theme.langs')
					{
						$value = trim($pair[1]);
						if (strpos($value, 'array(') === 0)
						{
							if (trim(substr($value, 6, -1)) == '')
							{
								$value = [];
							} else
							{
								$value = array_map('trim', explode(',', substr($value, 6, -1)));
							}
						}
						$alternate_languages = $value;
						continue;
					}
				}
			}
			fclose($file);
		}
	}

	if (function_exists('simplexml_load_string') && $default_lang != '')
	{
		/* @var $xml SimpleXMLElement */
		$xml = simplexml_load_string(file_get_contents("$config[project_path]/admin/data/config/theme.xml"));

		if ($xml)
		{
			$json = json_decode(json_encode($xml), true);

			$xml_languages = $xml->xpath('/theme/languages/language');
			foreach ($xml_languages as $xml_language)
			{
				$json_language = json_decode(json_encode($xml_language), true);
				$language = ['code' => $json_language['@attributes']['id'], 'title' => $json_language['title'][$_SESSION['userdata']['lang']]];
				if ($language['code'] == $default_lang)
				{
					$language['default_text'] = '(' . $lang['website_ui']['lang_file_field_language_default'] . ')';
				} elseif (!in_array($language['code'], $alternate_languages))
				{
					$language['is_inactive'] = 1;
				}
				$languages[] = $language;
			}
		}
	}
}

$db_languages = mr2array(sql("select code, title from $config[tables_prefix]languages order by language_id asc"));
foreach ($db_languages as $db_language)
{
	foreach ($languages as $language)
	{
		if ($language['code'] == $db_language['code'])
		{
			continue 2;
		}
	}
	if ($db_language['code'] == $default_lang)
	{
		$db_language['default_text'] = '(' . $lang['website_ui']['lang_file_field_language_default'] . ')';
	} elseif (!in_array($db_language['code'], $alternate_languages))
	{
		$db_language['is_inactive'] = 1;
	}
	$languages[] = $db_language;
}

grid_presets_start($grid_presets, $page_name, 'theme_language_files');

$table_fields = array();
$table_fields[] = array('id' => 'external_id', 'title' => $lang['website_ui']['lang_file_field_id'],       'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'language',    'title' => $lang['website_ui']['lang_file_field_language'], 'is_default' => 1, 'type' => 'text', 'value_postfix' => 'default_text');

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

grid_presets_end($grid_presets, $page_name, 'theme_language_files');

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

	if (validate_field('empty', $_POST['external_id'], $lang['website_ui']['lang_file_field_language']))
	{
		$allowed_ids = array('default');
		foreach ($languages as $language)
		{
			$allowed_ids[] = $language['code'];
		}
		if (!in_array($_POST['external_id'], $allowed_ids))
		{
			$errors[] = get_aa_error('website_ui_invalid_lang_file_id', $lang['website_ui']['lang_file_field_language']);
		} elseif ($_POST['action'] == 'add_new_complete' && is_file("$langs_dir/$_POST[external_id].lang"))
		{
			$errors[] = get_aa_error('website_ui_invalid_lang_file_id', $lang['website_ui']['lang_file_field_language']);
		}
	}
	validate_field('empty', $_POST['code'], $lang['website_ui']['lang_file_field_code']);

	if (!is_writable($langs_dir))
	{
		$errors[] = get_aa_error('filesystem_permission_write', $langs_dir);
	}

	if (!is_array($errors))
	{
		$rnd = mt_rand(10000000,99999999);
		$temp_file = "$langs_dir/$_POST[external_id]-$rnd.lang";
		file_put_contents($temp_file, "$_POST[code]");
		rename($temp_file, "$langs_dir/$_POST[external_id].lang");

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
			if (!is_writable($langs_dir))
			{
				$errors[] = get_aa_error('filesystem_permission_write', $langs_dir);
				return_ajax_errors($errors);
			}

			foreach ($_REQUEST['row_select'] as $item_id)
			{
				foreach ($languages as $language)
				{
					if ($item_id == $language['code'])
					{
						@unlink("$langs_dir/$item_id.lang");
					}
				}
			}
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
	$_POST = null;
	if (trim($_GET['item_id']) == 'default')
	{
		$_POST = array('external_id' => 'default', 'title' => $default_lang != '' ? $lang['website_ui']['lang_file_field_language_options'] : $lang['website_ui']['lang_file_field_language_default']);
	} else
	{
		foreach ($languages as $language)
		{
			if (trim($_GET['item_id']) == $language['code'])
			{
				$_POST = array('external_id' => $language['code'], 'title' => $language['title']);
				break;
			}
		}
	}
	if (empty($_POST) || !is_file("$langs_dir/$_POST[external_id].lang"))
	{
		header("Location: $page_name");
		die;
	}

	$_POST['code'] = file_get_contents("$langs_dir/$_POST[external_id].lang");

	if (!is_writable($langs_dir))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $langs_dir);
	}

	if (is_array($_POST['errors']))
	{
		$_POST['errors'] = array_unique($_POST['errors']);
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['allowed_languages'] = array();
	foreach ($languages as $language)
	{
		if (!is_file("$langs_dir/$language[code].lang"))
		{
			$_POST['allowed_languages'][] = $language;
		}
	}

	if (!is_writable($langs_dir))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $langs_dir);
	}

	if (is_array($_POST['errors']))
	{
		$_POST['errors'] = array_unique($_POST['errors']);
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$data = array();
if (is_file("$langs_dir/default.lang"))
{
	if ($_SESSION['save'][$page_name]['se_text'] == '' || strpos("default", $_SESSION['save'][$page_name]['se_text']) !== false || mb_contains(file_get_contents("$langs_dir/default.lang"), $_SESSION['save'][$page_name]['se_text']))
	{
		$data[] = array('external_id' => 'default', 'language' => $default_lang != '' ? $lang['website_ui']['lang_file_field_language_options'] : $lang['website_ui']['lang_file_field_language_default']);
	}
}

foreach ($languages as $language)
{
	if (is_file("$langs_dir/$language[code].lang"))
	{
		if ($_SESSION['save'][$page_name]['se_text'] == '' || strpos($language['code'], $_SESSION['save'][$page_name]['se_text']) !== false || mb_contains(file_get_contents("$langs_dir/$language[code].lang"), $_SESSION['save'][$page_name]['se_text']))
		{
			$data[] = array('external_id' => $language['code'], 'language' => $language['title'], 'default_text' => $language['default_text'], 'is_inactive' => $language['is_inactive']);
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();

if (in_array($_REQUEST['action'], array('change')))
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('languages', $languages);
$smarty->assign('table_key_name', 'external_id');
$smarty->assign('table_fields', $table_fields);
$smarty->assign('total_num', array_cnt($data));
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if (!is_dir("$config[project_path]/admin/data/config"))
{
	header("Location: project_theme_install.php");
	die;
}
if (is_dir("$config[project_path]/langs"))
{
	$smarty->assign('supports_langs',1);
}
if (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	$smarty->assign('supports_theme', 1);
}

$stats_params = @unserialize(file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
if (intval($stats_params['collect_performance_stats']) == 1)
{
	$smarty->assign('collect_performance_stats', 1);
}

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['external_id'], $lang['website_ui']['lang_file_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['website_ui']['lang_file_add']);
} else
{
	$smarty->assign('page_title', $lang['website_ui']['submenu_option_lang_files']);
}

$smarty->display("layout.tpl");
