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

grid_presets_start($grid_presets, $page_name, 'theme_history');

$table_fields = array();
$table_fields[] = array('id' => 'change_id',  'title' => $lang['website_ui']['page_history_field_change_id'],  'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'object',     'title' => $lang['website_ui']['page_history_field_object'],     'is_default' => 1, 'type' => 'longtext', 'link' => 'custom', 'link_id' => 'object_id', 'permission' => 'website_ui|view');
$table_fields[] = array('id' => 'username',   'title' => $lang['website_ui']['page_history_field_author'],     'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'version',    'title' => $lang['website_ui']['page_history_field_version'],    'is_default' => 1, 'type' => 'number');
$table_fields[] = array('id' => 'added_date', 'title' => $lang['website_ui']['page_history_field_added_date'], 'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "change_id";
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
$search_fields[] = array('id' => 'filenames', 'title' => $lang['website_ui']['page_history_filter_search_in_names']);
$search_fields[] = array('id' => 'contents',  'title' => $lang['website_ui']['page_history_filter_search_in_content']);

$table_key_name = "change_id";
$table_name = "$config[tables_prefix_multi]file_history";
$table_selector = "change_id, path, version, username, added_date";

$objects_map = [];

$blocks_list = get_contents_from_dir("$config[project_path]/blocks", 2);

$pages = get_site_pages();
$page_components = [];
$page_ids = [];
foreach ($pages as $page)
{
	$page_ids[$page['external_id']] = $page;
}

$templates_data = get_site_parsed_templates();

$templates = get_contents_from_dir("$config[project_path]/template", 1);
foreach ($templates as $template)
{
	if (substr(strtolower($template), -4) != '.tpl')
	{
		continue;
	}

	$temp = explode(".", $template);
	$page_external_id = $temp[0];
	if (isset($page_ids[$page_external_id]))
	{
		$page = $page_ids[$page_external_id];
		$objects_map["$config[project_path]/template/$page_external_id.tpl"] = ['type' => 'page', 'page_id' => $page_external_id, 'page_name' => $page['title']];
		if (isset($templates_data["$page_external_id.tpl"]))
		{
			foreach ($templates_data["$page_external_id.tpl"]['block_inserts'] as $block_insert)
			{
				$block_id = trim($block_insert['block_id']);
				$block_name = trim($block_insert['block_name']);
				if (!preg_match($regexp_valid_external_id, $block_id) || !preg_match($regexp_valid_block_name, $block_name))
				{
					continue;
				}
				$block_internal_name = strtolower(str_replace(" ", "_", $block_name));
				$objects_map["$config[project_path]/template/blocks/$page_external_id/{$block_id}_{$block_internal_name}.tpl"] = ['type' => 'block_template', 'page_id' => $page_external_id, 'page_name' => $page['title'], 'block_id' => $block_id, 'block_name' => $block_name, 'block_internal_name' => $block_internal_name];
				$objects_map["$config[project_path]/admin/data/config/$page_external_id/{$block_id}_{$block_internal_name}.dat"] = ['type' => 'block_params', 'page_id' => $page_external_id, 'page_name' => $page['title'], 'block_id' => $block_id, 'block_name' => $block_name, 'block_internal_name' => $block_internal_name];
			}
		}
	} else
	{
		$page_components[] = $page_external_id;
	}
}

if (is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
{
	$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
	$global_blocks = explode("|AND|", trim($temp[2]));
	foreach ($global_blocks as $global_block)
	{
		if ($global_block == '')
		{
			continue;
		}
		$block_id = substr($global_block, 0, strpos($global_block, "[SEP]"));
		$block_internal_name = substr($global_block, strpos($global_block, "[SEP]") + 5);
		$block_name = ucwords(str_replace('_', ' ', $block_internal_name));

		$objects_map["$config[project_path]/template/blocks/\$global/{$block_id}_$block_internal_name.tpl"] = ['type' => 'global_template', 'block_id' => $block_id, 'block_name' => $block_name, 'block_internal_name' => $block_internal_name];
		$objects_map["$config[project_path]/admin/data/config/\$global/{$block_id}_$block_internal_name.dat"] = ['type' => 'global_params', 'block_id' => $block_id, 'block_name' => $block_name, 'block_internal_name' => $block_internal_name];
	}
}

foreach ($page_components as $page_component_id)
{
	$objects_map["$config[project_path]/template/$page_component_id.tpl"] = array('type' => 'component', 'component_id' => $page_component_id, 'component_name' => "$page_component_id.tpl");
}

$ad_spots = get_site_spots();
foreach ($ad_spots as $ad_spot)
{
	$objects_map["$config[project_path]/admin/data/advertisements/spot_$ad_spot[external_id].dat#template"] = array('type' => 'spot', 'spot_id' => $ad_spot['external_id'], 'spot_name' => $ad_spot['title']);
	foreach ($ad_spot['ads'] as $ad)
	{
		$objects_map["$config[project_path]/admin/data/advertisements/spot_$ad_spot[external_id].dat#ads:$ad[advertisement_id]:code"] = array('type' => 'ad', 'ad_id' => $ad['advertisement_id'], 'ad_name' => $ad['title'], 'spot_id' => $ad_spot['external_id'], 'spot_name' => $ad_spot['title']);
	}
}

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
	$_SESSION['save'][$page_name]['se_object'] = '';
	$_SESSION['save'][$page_name]['se_type'] = '';
	$_SESSION['save'][$page_name]['se_username'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_username']))
	{
		$_SESSION['save'][$page_name]['se_username'] = trim($_GET['se_username']);
	}
	if (isset($_GET['se_object']))
	{
		$_SESSION['save'][$page_name]['se_object'] = trim($_GET['se_object']);
	}
	if (isset($_GET['se_type']))
	{
		$_SESSION['save'][$page_name]['se_type'] = trim($_GET['se_type']);
	}
	if (isset($_GET['se_date_from']))
	{
		$_SESSION['save'][$page_name]['se_date_from'] = strtotime($_GET['se_date_from']) !== false ? date('Y-m-d', strtotime($_GET['se_date_from'])) : '';
	}
	if (isset($_GET['se_date_to']))
	{
		$_SESSION['save'][$page_name]['se_date_to'] = strtotime($_GET['se_date_to']) !== false ? date('Y-m-d', strtotime($_GET['se_date_to'])) : '';
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
			if ($search_field['id'] == 'filenames')
			{
				$where_search .= " or $table_name.path like '%$q%'";
			} elseif ($search_field['id'] == 'contents')
			{
				$where_search .= " or case when substring($table_name.file_content, 1, 4)='B64=' then from_base64(substring($table_name.file_content, 5)) else $table_name.file_content end like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

if ($_SESSION['save'][$page_name]['se_type'] != '')
{
	switch ($_SESSION['save'][$page_name]['se_type'])
	{
		case 'pages':
			$page_template_list = [];
			foreach ($pages as $page)
			{
				$page_template_list[] = "'/template/$page[external_id].tpl'";
			}
			if (array_cnt($page_template_list) > 0)
			{
				$where .= " and $table_name.path in (" . implode(', ', $page_template_list) . ')';
			} else
			{
				$where .= " and 1=0";
			}
			break;
		case 'blocks':
			$where .= " and ($table_name.path like '/template/blocks/%' or $table_name.path like '/admin/data/config/%')";
			break;
		case 'global':
			$where .= " and ($table_name.path like '/template/blocks/\$global/%' or $table_name.path like '/admin/data/config/\$global/%')";
			break;
		case 'components':
			$page_template_list = [];
			foreach ($pages as $page)
			{
				$page_template_list[] = "$page[external_id].tpl";
			}
			$component_template_list = [];
			foreach ($templates as $template)
			{
				if (substr(strtolower($template), -4) != '.tpl')
				{
					continue;
				}
				if (!in_array($template, $page_template_list))
				{
					$component_template_list[] = "'/template/$template'";
				}
			}
			if (array_cnt($component_template_list) > 0)
			{
				$where .= " and $table_name.path in (" . implode(', ', $component_template_list) . ')';
			} else
			{
				$where .= " and 1=0";
			}
			break;
		case 'advertising':
			$where .= " and $table_name.path like '/admin/data/advertisements/%'";
			break;
		case 'files':
			$where .= " and $table_name.path not like '/template/%' and $table_name.path not like '/admin/data/%'";
			break;
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_object'] != '')
{
	$object_filter = explode('/', $_SESSION['save'][$page_name]['se_object']);
	switch ($object_filter[0])
	{
		case 'page':
			$q_page = sql_escape($object_filter[1]);
			$where .= " and $table_name.path='/template/$q_page.tpl'";
			break;
		case 'block':
			$q_page = sql_escape($object_filter[1]);
			$q_block = sql_escape($object_filter[2]);
			$where .= " and ($table_name.path='/template/blocks/$q_page/$q_block.tpl' or $table_name.path='/admin/data/config/$q_page/$q_block.dat')";
			break;
		case 'global':
			$q_block = sql_escape($object_filter[1]);
			$where .= " and ($table_name.path='/template/blocks/\$global/$q_block.tpl' or $table_name.path='/admin/data/config/\$global/$q_block.dat')";
			break;
		case 'component':
			$q_component = sql_escape($object_filter[1]);
			$where .= " and $table_name.path='/template/$q_component.tpl'";
			break;
		case 'spot':
			$q_spot = sql_escape($object_filter[1]);
			$where .= " and $table_name.path='/admin/data/advertisements/spot_$q_spot.dat#template'";
			break;
		case 'ad':
			$q_spot = sql_escape($object_filter[1]);
			$q_ad = sql_escape($object_filter[2]);
			$where .= " and $table_name.path='/admin/data/advertisements/spot_$q_spot.dat#ads:$q_ad:code'";
			break;
		default:
			$where .= " and 1=0";
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_username'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_username']);
	$where .= " and username='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_from'] <> "")
{
	$where .= " and added_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and added_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

grid_presets_end($grid_presets, $page_name, 'theme_history');

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change')
{
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_GET['item_id'])));
	if (empty($_POST))
	{
		header("Location: $page_name");
		die;
	}
	$_POST['prev_version'] = mr2array_single(sql_pr("select * from $table_name where path=? and version<? order by version desc limit 1", $_POST['path'], $_POST['version']));
	$_POST['next_version'] = mr2array_single(sql_pr("select * from $table_name where path=? and version>? order by version asc limit 1", $_POST['path'], $_POST['version']));

	if (substr($_POST['file_content'], 0, 4) == 'B64=')
	{
		$_POST['file_content'] = base64_decode(substr($_POST['file_content'], 4));
	}
	if (substr($_POST['prev_version']['file_content'], 0, 4) == 'B64=')
	{
		$_POST['prev_version']['file_content'] = base64_decode(substr($_POST['prev_version']['file_content'], 4));
	}
	if (substr($_POST['next_version']['file_content'], 0, 4) == 'B64=')
	{
		$_POST['next_version']['file_content'] = base64_decode(substr($_POST['prev_version']['file_content'], 4));
	}
	if (strpos($_POST['path'], "/admin/data/config") !== false)
	{
		if ($_POST['prev_version']['file_content'] != '')
		{
			$version_data = explode('||', $_POST['prev_version']['file_content']);
			$_POST['prev_version']['file_content'] = 'cache_time=' . intval($version_data[0]) . "\nis_not_cached_for_members=" . intval($version_data[2]) . "\n";
			$_POST['prev_version']['file_content'] .= implode("\n", explode('&', trim($version_data[1])));
		}
		if ($_POST['file_content'] != '')
		{
			$version_data = explode('||', $_POST['file_content']);
			$_POST['file_content'] = 'cache_time=' . intval($version_data[0]) . "\nis_not_cached_for_members=" . intval($version_data[2]) . "\n";
			$_POST['file_content'] .= implode("\n", explode('&', trim($version_data[1])));
		}
	}

	$object = $objects_map["$config[project_path]/" . trim($_POST['path'], '/')];
	if (isset($object))
	{
		switch ($object['type'])
		{
			case 'page':
				$_POST['object'] = str_replace('%1%', $object['page_name'], $lang['website_ui']['page_history_field_object_page']);
				$_POST['object_link'] = "project_pages.php?action=change&item_id=$object[page_id]";
				break;
			case 'block_template':
				$_POST['object'] = str_replace(['%2%', '%1%'], [$object['page_name'], $object['block_name']], $lang['website_ui']['page_history_field_object_block_template']);
				$_POST['object_link'] = "project_pages.php?action=change_block&item_id=$object[page_id]||$object[block_id]||$object[block_internal_name]&item_name=$object[block_name]";
				break;
			case 'block_params':
				$_POST['object'] = str_replace(['%2%', '%1%'], [$object['page_name'], $object['block_name']], $lang['website_ui']['page_history_field_object_block_params']);
				$_POST['object_link'] = "project_pages.php?action=change_block&item_id=$object[page_id]||$object[block_id]||$object[block_internal_name]&item_name=$object[block_name]";
				break;
			case 'component':
				$_POST['object'] = str_replace('%1%', $object['component_name'], $lang['website_ui']['page_history_field_object_component']);
				$_POST['object_link'] = "project_pages_components.php?action=change&item_id=$object[component_name]";
				break;
			case 'global_template':
				$_POST['object'] = str_replace('%1%', $object['block_name'], $lang['website_ui']['page_history_field_object_global_template']);
				$_POST['object_link'] = "project_pages.php?action=change_block&item_id=\$global||$object[block_id]||$object[block_internal_name]&item_name=$object[block_name]";
				break;
			case 'global_params':
				$_POST['object'] = str_replace('%1%', $object['block_name'], $lang['website_ui']['page_history_field_object_global_params']);
				$_POST['object_link'] = "project_pages.php?action=change_block&item_id=\$global||$object[block_id]||$object[block_internal_name]&item_name=$object[block_name]";
				break;
			case 'spot':
				$_POST['object'] = str_replace('%1%', $object['spot_name'], $lang['website_ui']['page_history_field_object_ad_spot']);
				$_POST['object_link'] = "project_spots.php?action=change_spot&item_id=$object[spot_id]";
				break;
			case 'ad':
				$_POST['object'] = str_replace(['%2%', '%1%'], [$object['spot_name'], $object['ad_name']], $lang['website_ui']['page_history_field_object_ad']);
				$_POST['object_link'] = "project_spots.php?action=change&item_id=$object[ad_id]";
				break;
			default:
				$_POST['object'] = str_replace('%1%', $_POST['path'], $lang['website_ui']['page_history_field_object_file']);
				if (is_file("$config[project_path]$_POST[path]"))
				{
					if (strpos($_POST['path'], 'admin/include/') === false && strpos($_POST['path'], '.htaccess') === false)
					{
						$_POST['object_link'] = "$config[project_url]$_POST[path]";
					}
				} else
				{
					$_POST['object'] = str_replace('%1%', $_POST['path'], $lang['website_ui']['page_history_field_object_file']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				}
				break;
		}
	} else
	{
		if (strpos($_POST['path'], '/template/') === 0 && substr($_POST['path'], -4) == '.tpl')
		{
			$path_exploded = explode('/', trim($_POST['path'], '/'), 4);
			if ($path_exploded[0] == 'template' && $path_exploded[1] == 'blocks')
			{
				$block_title = $path_exploded[3];
				$temp = explode('||', @file_get_contents("$config[project_path]/admin/data/config/$path_exploded[2]/" . str_replace('.tpl', '.dat', $block_title)));
				if ($temp[3] != '' && in_array($temp[3], $blocks_list))
				{
					$block_title = ucwords(str_replace('_', ' ', substr(substr($block_title, strlen($temp[3]) + 1),  0, -4)));
				} else
				{
					foreach ($blocks_list as $block_type_id)
					{
						if (strpos($block_title, $block_type_id) === 0)
						{
							$block_title = ucwords(str_replace('_', ' ', substr(substr($block_title, strlen($block_type_id) + 1),  0, -4)));
						}
					}
				}
				if ($path_exploded[2] == '$global')
				{
					$_POST['object'] = str_replace('%1%', $block_title, $lang['website_ui']['page_history_field_object_global_template']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				} else
				{
					$page_title = $path_exploded[2];
					if (isset($page_ids[$page_title]))
					{
						$page_title = $page_ids[$page_title]['title'];
					}
					$_POST['object'] = str_replace(['%2%', '%1%'], [$page_title, $block_title], $lang['website_ui']['page_history_field_object_block_template']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				}
			} else
			{
				$component_title = $path_exploded[1];
				if (is_file("$config[project_path]/admin/data/config/" . substr($component_title, 0, -4) . '/name.dat'))
				{
					$component_title = file_get_contents("$config[project_path]/admin/data/config/" . substr($component_title, 0, -4) . '/name.dat');
					$_POST['object'] = str_replace('%1%', $component_title, $lang['website_ui']['page_history_field_object_page']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				} else
				{
					$_POST['object'] = str_replace('%1%', $component_title, $lang['website_ui']['page_history_field_object_component']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				}
			}
		} elseif (strpos($_POST['path'], '/admin/data/config/') === 0 && substr($_POST['path'], -4) == '.dat')
		{
			$path_exploded = explode('/', trim($_POST['path'], '/'), 5);
			$block_title = $path_exploded[4];
			$temp = explode('||', @file_get_contents("$config[project_path]/admin/data/config/$path_exploded[3]/$block_title"));
			if ($temp[3] != '' && in_array($temp[3], $blocks_list))
			{
				$block_title = ucwords(str_replace('_', ' ', substr(substr($block_title, strlen($temp[3]) + 1),  0, -4)));
			} else
			{
				foreach ($blocks_list as $block_type_id)
				{
					if (strpos($block_title, $block_type_id) === 0)
					{
						$block_title = ucwords(str_replace('_', ' ', substr(substr($block_title, strlen($block_type_id) + 1),  0, -4)));
					}
				}
			}
			if ($path_exploded[3] == '$global')
			{
				$_POST['object'] = str_replace('%1%', $block_title, $lang['website_ui']['page_history_field_object_global_params']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			} else
			{
				$page_title = $path_exploded[3];
				if (isset($page_ids[$page_title]))
				{
					$page_title = $page_ids[$page_title]['title'];
				}
				$_POST['object'] = str_replace(['%2%', '%1%'], [$page_title, $block_title], $lang['website_ui']['page_history_field_object_block_params']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			}
		} elseif (strpos($_POST['path'], '/admin/data/advertisements/') === 0)
		{
			$path_exploded = explode('/', trim($_POST['path'], '/'), 4);
			if (substr($_POST['path'], -13) == '.dat#template')
			{
				$spot_title = substr($path_exploded[3], 0, -13);
				$_POST['object'] = str_replace('%1%', $spot_title, $lang['website_ui']['page_history_field_object_ad_spot']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			} else
			{
				$spot_title = substr($path_exploded[3], 0, strpos($path_exploded[3], '#') - 4);
				$ad_title = substr($path_exploded[3], strpos($path_exploded[3], '#') + 5, -5);
				$_POST['object'] = str_replace(['%2%', '%1%'], [$spot_title, $ad_title], $lang['website_ui']['page_history_field_object_ad']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			}
		} else
		{
			$_POST['object'] = str_replace('%1%', $_POST['path'], $lang['website_ui']['page_history_field_object_file']);
			if (is_file("$config[project_path]$_POST[path]"))
			{
				if (strpos($_POST['path'], 'admin/include/') === false && strpos($_POST['path'], '.htaccess') === false)
				{
					$_POST['object_link'] = "$config[project_url]$_POST[path]";
				}
			} else
			{
				$_POST['object'] = str_replace('%1%', $_POST['path'], $lang['website_ui']['page_history_field_object_file']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			}
		}
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name where version>0 $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}

$data = mr2array(sql("select $table_selector from $table_name where version>0 $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));
foreach ($data as $k => $v)
{
	$object = $objects_map["$config[project_path]/" . trim($v['path'], '/')];
	if (isset($object))
	{
		switch ($object['type'])
		{
			case 'page':
				$data[$k]['object'] = str_replace('%1%', $object['page_name'], $lang['website_ui']['page_history_field_object_page']);
				$data[$k]['object_id'] = $object['page_id'];
				$data[$k]['object_link'] = "project_pages.php?action=change&item_id=%id%";
				break;
			case 'block_template':
				$data[$k]['object'] = str_replace(['%2%', '%1%'], [$object['page_name'], $object['block_name']], $lang['website_ui']['page_history_field_object_block_template']);
				$data[$k]['object_id'] = "$object[page_id]||$object[block_id]||$object[block_internal_name]";
				$data[$k]['object_link'] = "project_pages.php?action=change_block&item_id=%id%&item_name=$object[block_name]";
				break;
			case 'block_params':
				$data[$k]['object'] = str_replace(['%2%', '%1%'], [$object['page_name'], $object['block_name']], $lang['website_ui']['page_history_field_object_block_params']);
				$data[$k]['object_id'] = "$object[page_id]||$object[block_id]||$object[block_internal_name]";
				$data[$k]['object_link'] = "project_pages.php?action=change_block&item_id=%id%&item_name=$object[block_name]";
				break;
			case 'component':
				$data[$k]['object'] = str_replace('%1%', $object['component_name'], $lang['website_ui']['page_history_field_object_component']);
				$data[$k]['object_id'] = $object['component_name'];
				$data[$k]['object_link'] = "project_pages_components.php?action=change&item_id=%id%";
				break;
			case 'global_template':
				$data[$k]['object'] = str_replace('%1%', $object['block_name'], $lang['website_ui']['page_history_field_object_global_template']);
				$data[$k]['object_id'] = "\$global||$object[block_id]||$object[block_internal_name]";
				$data[$k]['object_link'] = "project_pages.php?action=change_block&item_id=%id%&item_name=$object[block_name]";
				break;
			case 'global_params':
				$data[$k]['object'] = str_replace('%1%', $object['block_name'], $lang['website_ui']['page_history_field_object_global_params']);
				$data[$k]['object_id'] = "\$global||$object[block_id]||$object[block_internal_name]";
				$data[$k]['object_link'] = "project_pages.php?action=change_block&item_id=%id%&item_name=$object[block_name]";
				break;
			case 'spot':
				$data[$k]['object'] = str_replace('%1%', $object['spot_name'], $lang['website_ui']['page_history_field_object_ad_spot']);
				$data[$k]['object_id'] = "$object[spot_id]";
				$data[$k]['object_link'] = "project_spots.php?action=change_spot&item_id=%id%";
				break;
			case 'ad':
				$data[$k]['object'] = str_replace(['%2%', '%1%'], [$object['spot_name'], $object['ad_name']], $lang['website_ui']['page_history_field_object_ad']);
				$data[$k]['object_id'] = "$object[ad_id]";
				$data[$k]['object_link'] = "project_spots.php?action=change&item_id=%id%";
				break;
			default:
				$data[$k]['object'] = str_replace('%1%', $v['path'], $lang['website_ui']['page_history_field_object_file']);
				if (is_file("$config[project_path]$v[path]"))
				{
					if (strpos($v['path'], 'admin/include/') === false && strpos($v['path'], '.htaccess') === false)
					{
						$data[$k]['object_id'] = 'test';
						$data[$k]['object_link'] = "$config[project_url]$v[path]";
					}
				} else
				{
					$data[$k]['object'] = str_replace('%1%', $v['path'], $lang['website_ui']['page_history_field_object_file']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				}
				break;
		}
	} else
	{
		if (strpos($v['path'], '/template/') === 0 && substr($v['path'], -4) == '.tpl')
		{
			$path_exploded = explode('/', trim($v['path'], '/'), 4);
			if ($path_exploded[0] == 'template' && $path_exploded[1] == 'blocks')
			{
				$block_title = $path_exploded[3];
				$temp = explode('||', @file_get_contents("$config[project_path]/admin/data/config/$path_exploded[2]/" . str_replace('.tpl', '.dat', $block_title)));
				if ($temp[3] != '' && in_array($temp[3], $blocks_list))
				{
					$block_title = ucwords(str_replace('_', ' ', substr(substr($block_title, strlen($temp[3]) + 1),  0, -4)));
				} else
				{
					foreach ($blocks_list as $block_type_id)
					{
						if (strpos($block_title, $block_type_id) === 0)
						{
							$block_title = ucwords(str_replace('_', ' ', substr(substr($block_title, strlen($block_type_id) + 1),  0, -4)));
						}
					}
				}
				if ($path_exploded[2] == '$global')
				{
					$data[$k]['object'] = str_replace('%1%', $block_title, $lang['website_ui']['page_history_field_object_global_template']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				} else
				{
					$page_title = $path_exploded[2];
					if (isset($page_ids[$page_title]))
					{
						$page_title = $page_ids[$page_title]['title'];
					}
					$data[$k]['object'] = str_replace(['%2%', '%1%'], [$page_title, $block_title], $lang['website_ui']['page_history_field_object_block_template']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				}
			} else
			{
				$component_title = $path_exploded[1];
				if (is_file("$config[project_path]/admin/data/config/" . substr($component_title, 0, -4) . '/name.dat'))
				{
					$component_title = file_get_contents("$config[project_path]/admin/data/config/" . substr($component_title, 0, -4) . '/name.dat');
					$data[$k]['object'] = str_replace('%1%', $component_title, $lang['website_ui']['page_history_field_object_page']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				} else
				{
					$data[$k]['object'] = str_replace('%1%', $component_title, $lang['website_ui']['page_history_field_object_component']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
				}
			}
		} elseif (strpos($v['path'], '/admin/data/config/') === 0 && substr($v['path'], -4) == '.dat')
		{
			$path_exploded = explode('/', trim($v['path'], '/'), 5);
			$block_title = $path_exploded[4];
			$temp = explode('||', @file_get_contents("$config[project_path]/admin/data/config/$path_exploded[3]/$block_title"));
			if ($temp[3] != '' && in_array($temp[3], $blocks_list))
			{
				$block_title = ucwords(str_replace('_', ' ', substr(substr($block_title, strlen($temp[3]) + 1),  0, -4)));
			} else
			{
				foreach ($blocks_list as $block_type_id)
				{
					if (strpos($block_title, $block_type_id) === 0)
					{
						$block_title = ucwords(str_replace('_', ' ', substr(substr($block_title, strlen($block_type_id) + 1),  0, -4)));
					}
				}
			}
			if ($path_exploded[3] == '$global')
			{
				$data[$k]['object'] = str_replace('%1%', $block_title, $lang['website_ui']['page_history_field_object_global_params']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			} else
			{
				$page_title = $path_exploded[3];
				if (isset($page_ids[$page_title]))
				{
					$page_title = $page_ids[$page_title]['title'];
				}
				$data[$k]['object'] = str_replace(['%2%', '%1%'], [$page_title, $block_title], $lang['website_ui']['page_history_field_object_block_params']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			}
		} elseif (strpos($v['path'], '/admin/data/advertisements/') === 0)
		{
			$path_exploded = explode('/', trim($v['path'], '/'), 4);
			if (substr($v['path'], -13) == '.dat#template')
			{
				$spot_title = substr($path_exploded[3], 0, -13);
				$data[$k]['object'] = str_replace('%1%', $spot_title, $lang['website_ui']['page_history_field_object_ad_spot']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			} else
			{
				$spot_title = substr($path_exploded[3], 0, strpos($path_exploded[3], '#') - 4);
				$ad_title = substr($path_exploded[3], strpos($path_exploded[3], '#') + 5, -5);
				$data[$k]['object'] = str_replace(['%2%', '%1%'], [$spot_title, $ad_title], $lang['website_ui']['page_history_field_object_ad']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			}
		} else
		{
			$data[$k]['object'] = str_replace('%1%', $v['path'], $lang['website_ui']['page_history_field_object_file']);
			if (is_file("$config[project_path]$v[path]"))
			{
				if (strpos($v['path'], 'admin/include/') === false && strpos($v['path'], '.htaccess') === false)
				{
					$data[$k]['object_id'] = 'test';
					$data[$k]['object_link'] = "$config[project_url]$v[path]";
				}
			} else
			{
				$data[$k]['object'] = str_replace('%1%', $v['path'], $lang['website_ui']['page_history_field_object_file']) . ' ' . $lang['website_ui']['page_history_field_object_deleted'];
			}
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('list_usernames', mr2array_list(sql("select distinct username from $table_name")));
$smarty->assign('list_objects', $objects_map);

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
$smarty->assign('num_on_page', $_SESSION['save'][$page_name]['num_on_page']);
$smarty->assign('grid_presets', $grid_presets);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

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

$smarty->assign('page_title', $lang['website_ui']['submenu_option_theme_history']);

$smarty->display("layout.tpl");