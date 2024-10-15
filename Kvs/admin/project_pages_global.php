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

grid_presets_start($grid_presets, $page_name, 'theme_global_blocks');

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

grid_presets_end($grid_presets, $page_name, 'theme_global_blocks');

$site_templates_path = "$config[project_path]/template";

$templates_data = get_site_parsed_templates();
$spots_data = get_site_spots();

$errors = null;

$state_file_path = "$config[project_path]/admin/data/engine/blocks_state/state_check.dat";
$block_checks = @unserialize(@file_get_contents(($state_file_path)));
if (!is_array($block_checks))
{
	$block_checks = array();
}

$blocks_list = get_contents_from_dir("$config[project_path]/blocks", 2);
sort($blocks_list);
foreach ($blocks_list as $k => $v)
{
	if (!is_file("$config[project_path]/blocks/$v/$v.php") || !is_file("$config[project_path]/blocks/$v/$v.dat"))
	{
		header("Location: project_blocks.php");
		die;
	}

	if (filemtime("$config[project_path]/blocks/$v/$v.php") != $block_checks[$v])
	{
		$block_checks[$v] = filemtime("$config[project_path]/blocks/$v/$v.php");

		unset($res);
		exec("$config[php_path] -l $config[project_path]/blocks/$v/$v.php", $res, $response_code);
		if ($response_code == 255)
		{
			header("Location: project_blocks.php");
			die;
		}

		include_once("$config[project_path]/blocks/$v/$v.php");
		if (function_exists("{$v}Show") === false || function_exists("{$v}GetHash") === false || function_exists("{$v}MetaData") === false)
		{
			header("Location: project_blocks.php");
			die;
		}
	}
}
mkdir_recursive(dirname($state_file_path));
file_put_contents($state_file_path, serialize($block_checks), LOCK_EX);

$external_id = '$global';
if ($_POST['action'] == 'add_new_complete')
{
	if (!is_writable("$site_templates_path/blocks/$external_id"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', "$site_templates_path/blocks/$external_id");
	}
	if (!is_writable("$config[project_path]/admin/data/config/$external_id"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/config/$external_id");
	}
	if (!is_writable("$config[project_path]/admin/data/config/$external_id/config.dat"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/config/$external_id/config.dat");
	}

	$valid_ids = 1;
	if (validate_field('empty', $_POST['block_id'], $lang['website_ui']['global_blocks_field_id']))
	{
		if (!preg_match($regexp_valid_external_id, $_POST['block_id']))
		{
			$errors[] = get_aa_error('website_ui_invalid_block_id', $lang['website_ui']['global_blocks_field_id'], $_POST['block_id']);
			$valid_ids = 0;
		}
	}
	if (validate_field('empty', $_POST['block_name'], $lang['website_ui']['global_blocks_field_name']))
	{
		if (!preg_match($regexp_valid_block_name, $_POST['block_name']))
		{
			$errors[] = get_aa_error('website_ui_invalid_block_name', $lang['website_ui']['global_blocks_field_name'], $_POST['block_name']);
			$valid_ids = 0;
		}
	}

	$global_config_file = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$external_id/config.dat"));
	$list_blocks = explode("|AND|", trim($global_config_file[2]));
	if ($valid_ids)
	{
		foreach ($list_blocks as $block)
		{
			$block_name = substr($block, strpos($block, "[SEP]") + 5);
			if ($block_name == strtolower(str_replace(" ", "_", $_POST['block_name'])))
			{
				$errors[] = get_aa_error('website_ui_blocks_unique_names', $lang['website_ui']['global_blocks_field_name'], $_POST['block_name']);
				break;
			}
		}
	}

	if (!is_array($errors))
	{
		$block_id = $_POST['block_id'];
		$block_display_name = $_POST['block_name'];
		$block_name = strtolower(str_replace(" ", "_", $_POST['block_name']));
		if (!is_file("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat"))
		{
			include_once("$config[project_path]/blocks/$block_id/$block_id.php");
			$metadata_function = "{$block_id}MetaData";
			if (function_exists($metadata_function))
			{
				$params = $metadata_function();
			} else
			{
				$params = array();
			}
			$list_params = "";
			foreach ($params as $param)
			{
				if ($param['is_required'] == 1)
				{
					$list_params .= "&$param[name]=$param[default_value]";
				} elseif ($param['type'] == '' && $param['default_value'] <> '')
				{
					$list_params .= "&$param[name]";
				}
			}
			$list_params = trim($list_params, "&");

			file_put_contents("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat", "0||$list_params||0||$block_id", LOCK_EX);
			KvsDataTypeFileHistory::increment_version("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat");
		}
		if (!is_file("$site_templates_path/blocks/$external_id/{$block_id}_$block_name.tpl"))
		{
			file_put_contents("$site_templates_path/blocks/$external_id/{$block_id}_$block_name.tpl", "<div class=\"$block_id\">\n$block_display_name\n</div>", LOCK_EX);
			KvsDataTypeFileHistory::increment_version("$site_templates_path/blocks/$external_id/{$block_id}_$block_name.tpl");
		}
		$list_blocks[] = "{$block_id}[SEP]$block_name";
		$list_blocks = implode("|AND|", $list_blocks);
		file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", "0||0||" . $list_blocks . "||0", LOCK_EX);

		$_SESSION['messages'][] = $lang['common']['success_message_added'];
		return_ajax_success("project_pages.php?action=change_block&item_id=\$global||$block_id||$block_name&item_name=" . urlencode($block_display_name));
	} else
	{
		return_ajax_errors($errors);
	}
}

if ($_REQUEST['batch_action'] != '')
{
	if (is_array($_REQUEST['row_select']) && array_search('0', $_REQUEST['row_select']) !== false)
	{
		unset($_REQUEST['row_select'][array_search('0', $_REQUEST['row_select'])]);
	}

	if (is_array($_REQUEST['row_select']) && array_cnt($_REQUEST['row_select']) > 0)
	{
		$row_select = $_REQUEST['row_select'];
		if ($_REQUEST['batch_action'] == 'delete')
		{
			$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$external_id/config.dat"));
			$list_blocks = explode("|AND|", trim($temp[2]));

			foreach ($list_blocks as $k => $block)
			{
				$block = str_replace("[SEP]", "_", $block);
				if (in_array($block, $row_select))
				{
					foreach ($templates_data as $template_info)
					{
						foreach ($template_info['global_block_inserts'] as $global_block_insert)
						{
							if ($global_block_insert['global_uid'] == $block)
							{
								$errors[] = get_aa_error('global_block_cannot_be_deleted', $block);
								return_ajax_errors($errors);
							}
						}
					}
					unset($list_blocks[$k]);
				}
			}

			$list_blocks = implode("|AND|", $list_blocks);
			file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", "0||0||" . $list_blocks . "||0", LOCK_EX);

			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'restore_block')
		{
			if (!is_writable("$config[project_path]/admin/data/config/$external_id/config.dat"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/config/$external_id/config.dat");
				return_ajax_errors($errors);
			}

			$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$external_id/config.dat"));
			$list_blocks = explode("|AND|", trim($temp[2]));

			foreach ($row_select as $temp_id)
			{
				$temp = explode('||', $temp_id);
				$block_id = trim($temp[0]);
				$block_name_mod = trim($temp[1]);
				if ($block_id == '' || $block_name_mod == '')
				{
					continue;
				}
				if (!in_array($block_id, $blocks_list) || !preg_match($regexp_valid_external_id, $block_name_mod))
				{
					continue;
				}
				if (is_file("$config[project_path]/admin/data/config/$external_id/{$block_id}_{$block_name_mod}.dat"))
				{
					$list_blocks[] = "{$block_id}[SEP]$block_name_mod";
				}
			}

			$list_blocks = implode("|AND|", $list_blocks);
			file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", "0||0||" . $list_blocks . "||0", LOCK_EX);

			$_SESSION['messages'][] = $lang['website_ui']['success_message_block_restored'];
			return_ajax_success($page_name);
		} elseif ($_REQUEST['batch_action'] == 'wipeout_block')
		{
			if (is_dir("$site_templates_path/blocks/$external_id") && !is_writable("$site_templates_path/blocks/$external_id"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', "$site_templates_path/blocks/$external_id");
			}
			if (is_dir("$config[project_path]/admin/data/config/$external_id") && !is_writable("$config[project_path]/admin/data/config/$external_id"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/config/$external_id");
			}
			if (!is_array($errors))
			{
				foreach ($row_select as $temp_id)
				{
					$temp = explode('||', $temp_id);
					$block_id = trim($temp[0]);
					$block_name_mod = trim($temp[1]);
					if ($block_id == '' || $block_name_mod == '')
					{
						continue;
					}
					if (!in_array($block_id, $blocks_list) || !preg_match($regexp_valid_external_id, $block_name_mod))
					{
						continue;
					}
					@unlink("$config[project_path]/admin/data/config/$external_id/{$block_id}_{$block_name_mod}.dat");
					KvsDataTypeFileHistory::increment_version("$config[project_path]/admin/data/config/$external_id/{$block_id}_{$block_name_mod}.dat");

					@unlink("$site_templates_path/blocks/$external_id/{$block_id}_{$block_name_mod}.tpl");
					KvsDataTypeFileHistory::increment_version("$site_templates_path/blocks/$external_id/{$block_id}_{$block_name_mod}.tpl");
				}

				$_SESSION['messages'][] = $lang['website_ui']['success_message_block_wiped_out'];
				return_ajax_success($page_name);
			} else
			{
				return_ajax_errors($errors);
			}
		}
	}

	$errors[] = get_aa_error('unexpected_error');
	return_ajax_errors($errors);
}

mkdir_recursive("$site_templates_path/blocks/$external_id");
mkdir_recursive("$config[project_path]/admin/data/config/$external_id");
if (!is_file("$config[project_path]/admin/data/config/$external_id/config.dat"))
{
	file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", "0||0||||0", LOCK_EX);
}

$data = array();
$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$external_id/config.dat"));
$list_blocks = explode("|AND|", trim($temp[2]));
$valid_global_blocks = array();
$template_global_blocks = '';
foreach ($list_blocks as $block)
{
	if ($block == '')
	{
		continue;
	}
	$block_id = substr($block, 0, strpos($block, "[SEP]"));
	$block_name = substr($block, strpos($block, "[SEP]") + 5);
	$block_display_name = ucwords(str_replace('_', ' ', substr($block, strpos($block, "[SEP]") + 5)));
	$block = str_replace("[SEP]", "_", $block);

	$template_global_blocks .= "{{insert name=\"getBlock\" block_id=\"$block_id\" block_name=\"$block_display_name\"}}\n";

	include_once("$config[project_path]/blocks/$block_id/$block_id.php");
	if (!is_file("$config[project_path]/admin/data/config/$external_id/$block.dat"))
	{
		$metadata_function = "{$block_id}MetaData";
		if (function_exists($metadata_function))
		{
			$params = $metadata_function();
		} else
		{
			$params = array();
		}
		$list_params = "";
		foreach ($params as $param)
		{
			if ($param['is_required'] == 1)
			{
				$list_params .= "&$param[name]=$param[default_value]";
			} elseif ($param['type'] == '' && $param['default_value'] <> '')
			{
				$list_params .= "&$param[name]";
			}
		}
		$list_params = trim($list_params, "&");

		file_put_contents("$config[project_path]/admin/data/config/$external_id/$block.dat", "86400||$list_params||0||$block_id", LOCK_EX);
		KvsDataTypeFileHistory::increment_version("$config[project_path]/admin/data/config/$external_id/$block.dat");
	}
	if (!is_file("$site_templates_path/blocks/$external_id/$block.tpl"))
	{
		file_put_contents("$site_templates_path/blocks/$external_id/$block.tpl", "<div class=\"$block_id\">\n$block_display_name\n</div>", LOCK_EX);
		KvsDataTypeFileHistory::increment_version("$site_templates_path/blocks/$external_id/$block.tpl");
	}

	$valid_global_blocks[] = $block;

	$block_data = array();
	$block_data['id'] = $block_id;
	$block_data['name'] = $block_name;
	$block_data['display_name'] = $block_display_name;
	$data[] = $block_data;
}
$templates_data['$global.tpl'] = get_site_parsed_template($template_global_blocks);

$templates_list = get_contents_from_dir($site_templates_path, 1);
foreach ($templates_list as $template_file)
{
	$template_info = $templates_data[$template_file];
	if (isset($template_info))
	{
		foreach ($template_info['global_block_inserts'] as $global_block_insert)
		{
			$global_id = trim($global_block_insert['global_uid']);
			if ($global_id != '')
			{
				$known_global_block = false;
				foreach ($data as $k1 => $v1)
				{
					if ($global_id == "$v1[id]_$v1[name]")
					{
						$data[$k1]['is_used'] = 1;
						$known_global_block = true;
					}
				}
				if (!$known_global_block)
				{
					if (is_file("$site_templates_path/blocks/$external_id/$global_id.tpl") && is_file("$config[project_path]/admin/data/config/$external_id/$global_id.dat"))
					{
						$global_block_info = file_get_contents("$config[project_path]/admin/data/config/$external_id/$global_id.dat");
						$temp_bl = explode('||', $global_block_info);
						if ($temp_bl[3] != '')
						{
							$block_name = substr($global_id, strlen($temp_bl[3]) + 1);
							$block_display_name = ucwords(str_replace("_", " ", $block_name));
							$list_blocks[] = "{$temp_bl[3]}[SEP]$block_name";
							$valid_global_blocks[] = $global_id;

							file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", "0||0||" . implode("|AND|", $list_blocks) . "||0", LOCK_EX);

							$new_block = array();
							$new_block['id'] = $temp_bl[3];
							$new_block['name'] = $block_name;
							$new_block['display_name'] = $block_display_name;
							$new_block['is_used'] = 1;
							$data[] = $new_block;
						}
					}
				}
			}
		}
	}
}

if (is_file("$config[project_path]/.htaccess"))
{
	$htaccess_contents = file_get_contents("$config[project_path]/.htaccess");
	foreach ($data as $k1 => $v1)
	{
		if (strpos($htaccess_contents, "block_id=$v1[id]_$v1[name]") !== false)
		{
			$data[$k1]['is_used'] = 1;
		}
	}
}

$has_block_caching_errors = array();
$validation_errors = validate_page('$global', $template_global_blocks, '', false, false, true);
foreach ($validation_errors as $validation_error)
{
	if ($validation_error['block_uid'] != '')
	{
		$has_block_error = false;
		$has_block_warning = false;
		switch ($validation_error['type'])
		{
			case 'block_id_invalid':
				$_POST['errors'][] = get_aa_error('website_ui_invalid_block_id', $lang['website_ui']['global_blocks_field_id'], $validation_error['data']);
				$has_block_error = true;
				break;
			case 'block_state_invalid':
				$_POST['errors'][] = get_aa_error('website_ui_invalid_block_state', $lang['website_ui']['global_blocks_field_id'], $validation_error['data']);
				$has_block_error = true;
				break;
			case 'block_name_invalid':
				$_POST['errors'][] = get_aa_error('website_ui_invalid_block_name', $lang['website_ui']['global_blocks_field_name'], $validation_error['data']);
				$has_block_error = true;
				break;
			case 'block_name_duplicate':
				$_POST['errors'][] = get_aa_error('website_ui_blocks_unique_names', $lang['website_ui']['global_blocks_field_name'], $validation_error['data']);
				$has_block_error = true;
				break;
			case 'block_template_empty':
				$_POST['errors'][] = get_aa_error('website_ui_block_empty_template', $validation_error['block_name']);
				$has_block_error = true;
				break;
			case 'block_circular_insert_block':
				$_POST['errors'][] = get_aa_error('website_ui_invalid_block_insert_block', $validation_error['block_name']);
				$has_block_error = true;
				break;
			case 'block_circular_insert_global':
				$_POST['errors'][] = get_aa_error('website_ui_invalid_block_insert_block2', $validation_error['block_name']);
				$has_block_error = true;
				break;
			case 'page_component_external_id_invalid':
				$_POST['errors'][] = get_aa_error('website_ui_invalid_page_component_id', $validation_error['block_name'], $validation_error['data']);
				$has_block_error = true;
				break;
			case 'page_component_unknown':
				$_POST['errors'][] = get_aa_error('website_ui_invalid_page_component', $validation_error['block_name'], $validation_error['data']);
				$has_block_error = true;
				break;
			case 'advertising_spot_unknown':
				$_POST['errors'][] = get_aa_error('website_ui_invalid_advertising_spot', $validation_error['block_name'], $validation_error['data']);
				$has_block_error = true;
				break;
			case 'file_missing':
				$has_block_error = true;
				break;
			case 'block_template_smarty_session_usage':
			case 'block_template_smarty_session_status_usage':
			case 'block_template_smarty_get_usage':
			case 'block_template_smarty_request_usage':
				$has_block_caching_errors[$validation_error['block_uid']] = $validation_error['block_name'];
				if ($validation_error['include'] == '')
				{
					$has_block_error = true;
				} else
				{
					$has_block_warning = true;
				}
				break;
			case 'fs_permissions':
				$has_block_warning = true;
				break;
		}
		if ($has_block_error || $has_block_warning)
		{
			foreach ($data as $k => $v)
			{
				if ($validation_error['block_uid'] == "{$v['id']}_$v[name]")
				{
					if ($has_block_error)
					{
						$data[$k]['errors'] = 1;
					}
					if ($has_block_warning)
					{
						$data[$k]['warnings'] = 1;
					}
				}
			}
		}
	}
}
foreach ($has_block_caching_errors as $block_name)
{
	$_POST['errors'][] = get_aa_error('website_ui_block_caching_issues', $block_name);
}
foreach ($validation_errors as $validation_error)
{
	switch ($validation_error['type'])
	{
		case 'file_missing':
			$_POST['errors'][] = get_aa_error('website_ui_missing_required_file', $validation_error['data']);
			break;
		case 'dir_missing':
			$_POST['errors'][] = get_aa_error('website_ui_missing_required_dir', $validation_error['data']);
			break;
		case 'fs_permissions':
			if ($validation_error['block_uid'] == '')
			{
				$_POST['errors'][] = get_aa_error('filesystem_permission_write', $validation_error['data']);
			}
			break;
	}
}

if (is_array($_POST['errors']))
{
	$_POST['errors'] = array_unique($_POST['errors']);
}

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	foreach ($data as $k => $v)
	{
		if (!stripos($v['id'], $_SESSION['save'][$page_name]['se_text']) && !stripos($v['display_name'], $_SESSION['save'][$page_name]['se_text']))
		{
			unset($data[$k]);
		}
	}
}

$list_global_files = get_contents_from_dir("$config[project_path]/admin/data/config/$external_id", 1);
$deleted_blocks = array();
$deleted_blocks_count = 0;
foreach ($list_global_files as $global_file)
{
	if ($global_file == "config.dat")
	{
		continue;
	}

	$is_delete_block = 1;
	foreach ($valid_global_blocks as $k)
	{
		if ($global_file == "$k.dat")
		{
			$is_delete_block = 0;
			break;
		}
	}
	if ($is_delete_block == 1)
	{
		$block_uid = str_replace(".dat", "", $global_file);
		if (is_file("$site_templates_path/blocks/$external_id/$block_uid.tpl"))
		{
			$temp = array_map('trim', explode("||", file_get_contents("$config[project_path]/admin/data/config/$external_id/$global_file")));
			if ($temp[3] != '' && in_array($temp[3], $blocks_list))
			{
				$deleted_blocks[] = array('block_id' => $temp[3], 'block_name_mod' => substr($block_uid, strlen($temp[3]) + 1), 'block_name' => ucwords(str_replace("_", " ", substr($block_uid, strlen($temp[3]) + 1))));
				$deleted_blocks_count++;
			} elseif ($temp[3] == '')
			{
				foreach ($blocks_list as $block_type_id)
				{
					if (strpos($block_uid, $block_type_id) === 0)
					{
						$deleted_blocks[] = array('block_id' => $block_type_id, 'block_name_mod' => substr($block_uid, strlen($block_type_id) + 1), 'block_name' => ucwords(str_replace("_", " ", substr($block_uid, strlen($block_type_id) + 1))));
						$deleted_blocks_count++;
					}
				}
			}
		}
	}
}

$smarty = new mysmarty();
$smarty->assign('blocks_list', $blocks_list);
$smarty->assign('deleted_global_blocks', $deleted_blocks);
$smarty->assign('deleted_global_blocks_count', $deleted_blocks_count);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('data', $data);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('total_num', array_cnt($data));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if (!is_dir("$config[project_path]/admin/data/config"))
{
	header("Location: project_theme_install.php");
	die;
}
if (is_dir("$config[project_path]/langs"))
{
	$smarty->assign('supports_langs', 1);
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

if ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', str_replace("%1%", $deleted_blocks_count, $lang['website_ui']['submenu_option_add_global_block']));
} elseif ($_REQUEST['action'] == 'restore_blocks')
{
	$smarty->assign('page_title', str_replace("%1%", $deleted_blocks_count, $lang['website_ui']['submenu_option_restore_global_blocks']));
	$smarty->assign('total_num', $deleted_blocks_count);
} else
{
	$smarty->assign('page_title', $lang['website_ui']['submenu_option_global_blocks']);
}

$smarty->display("layout.tpl");
