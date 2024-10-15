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

grid_presets_start($grid_presets, $page_name, 'theme_search');

$search_fields = [];
$search_fields[] = array('id' => 'templates',  'title' => $lang['website_ui']['template_search_group_templates']);
$search_fields[] = array('id' => 'params',  'title' => $lang['website_ui']['template_search_group_params']);
$search_fields[] = array('id' => 'htaccess',  'title' => $lang['website_ui']['template_search_group_htaccess']);
$search_fields[] = array('id' => 'advertising',  'title' => $lang['website_ui']['template_search_group_advertising']);
$search_fields[] = array('id' => 'texts',  'title' => $lang['website_ui']['template_search_group_texts']);
$search_fields[] = array('id' => 'scripts',  'title' => $lang['website_ui']['template_search_group_scripts']);
$search_fields[] = array('id' => 'styles',  'title' => $lang['website_ui']['template_search_group_styles']);

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

if (strlen($_SESSION['save'][$page_name]['se_text']) > 0)
{
	foreach ($search_fields as $search_field)
	{
		if (isset($_GET["se_text_$search_field[id]"]))
		{
			$_SESSION['save'][$page_name]["se_text_$search_field[id]"] = $_GET["se_text_$search_field[id]"];
		}
	}
}

grid_presets_end($grid_presets, $page_name, 'theme_search');

if ($_GET['action'] == 'htaccess')
{
	$_POST['htaccess_contents'] = file_get_contents("$config[project_path]/.htaccess");
}

$data = [];
if (strlen($_SESSION['save'][$page_name]['se_text']) > 0)
{
	$site_templates_path = "$config[project_path]/template";
	$pages = get_site_pages();
	$page_ids = [];
	foreach ($pages as $page)
	{
		$page_ids[$page['external_id']] = $page;
	}

	$templates = get_contents_from_dir($site_templates_path, 1);
	foreach ($templates as $template)
	{
		if (substr(strtolower($template), -4) != '.tpl')
		{
			continue;
		}

		$contents = @file_get_contents("$site_templates_path/$template");

		$item = [];
		$item['filename'] = $template;

		$temp = explode(".", $template);
		$page_external_id = $temp[0];
		if (isset($page_ids[$page_external_id]))
		{
			$item['type'] = 'page';
			$item['page_name'] = $page_ids[$page_external_id]['title'];
			$item['editor_url'] = "project_pages.php?action=change&item_id=$page_external_id";
		} else
		{
			$item['type'] = 'component';
			$item['editor_url'] = "project_pages_components.php?action=change&item_id=$template";
		}
		if (intval($_SESSION['save'][$page_name]['se_text_templates']) == 1 && mb_contains($contents, $_SESSION['save'][$page_name]['se_text']))
		{
			$data['templates'][] = $item;
		}

		if ($item['type'] == 'page')
		{
			preg_match_all($regexp_insert_block, $contents, $temp);
			if (array_cnt($temp[1]) > 0)
			{
				foreach ($temp[1] as $k1 => $v1)
				{
					$block_id = trim($temp[1][$k1]);
					$block_name = trim($temp[2][$k1]);
					if (!preg_match($regexp_valid_external_id, $block_id) || !preg_match($regexp_valid_block_name, $block_name))
					{
						continue;
					}
					$block_internal_name = strtolower(str_replace(" ", "_", $block_name));
					$block_template = "{$block_id}_$block_internal_name.tpl";

					$block_contents = @file_get_contents("$site_templates_path/blocks/$page_external_id/$block_template");
					if (intval($_SESSION['save'][$page_name]['se_text_templates']) == 1 && mb_contains($block_contents, $_SESSION['save'][$page_name]['se_text']))
					{
						$item_block = [];
						$item_block['filename'] = $block_template;
						$item_block['type'] = 'block_template';
						$item_block['page_name'] = $page_ids[$page_external_id]['title'];
						$item_block['block_name'] = $block_name;
						$item_block['editor_url'] = "project_pages.php?action=change_block&item_id=$page_external_id||$block_id||$block_internal_name&item_name=$block_name";
						$data['templates'][] = $item_block;
					}

					$block_params = @file_get_contents("$config[project_path]/admin/data/config/$page_external_id/{$block_id}_$block_internal_name.dat");
					if (intval($_SESSION['save'][$page_name]['se_text_params']) == 1 && mb_contains($block_params, $_SESSION['save'][$page_name]['se_text']))
					{
						$item_block = [];
						$item_block['filename'] = $block_template;
						$item_block['type'] = 'block_params';
						$item_block['page_name'] = $page_ids[$page_external_id]['title'];
						$item_block['block_name'] = $block_name;
						$item_block['editor_url'] = "project_pages.php?action=change_block&item_id=$page_external_id||$block_id||$block_internal_name&item_name=$block_name";
						$data['params'][] = $item_block;
					}
				}
			}
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
			$block_name_mod = substr($global_block, strpos($global_block, "[SEP]") + 5);
			$block_name = ucwords(str_replace('_', ' ', $block_name_mod));

			$file_name = "$site_templates_path/blocks/\$global/{$block_id}_$block_name_mod.tpl";
			$template = "{$block_id}_$block_name_mod.tpl";
			if (is_file($file_name))
			{
				$contents = @file_get_contents($file_name);
				if (intval($_SESSION['save'][$page_name]['se_text_templates']) == 1 && mb_contains($contents, $_SESSION['save'][$page_name]['se_text']))
				{
					$item = [];
					$item['filename'] = $template;
					$item['type'] = 'global_block_template';
					$item['block_name'] = $block_name;
					$item['editor_url'] = "project_pages.php?action=change_block&item_id=\$global||$block_id||$block_name_mod&item_name=$block_name";
					$data['templates'][] = $item;
				}

				$params = @file_get_contents("$config[project_path]/admin/data/config/\$global/{$block_id}_$block_name_mod.dat");
				if (intval($_SESSION['save'][$page_name]['se_text_params']) == 1 && mb_contains($params, $_SESSION['save'][$page_name]['se_text']))
				{
					$item = [];
					$item['filename'] = $template;
					$item['type'] = 'global_block_params';
					$item['block_name'] = $block_name;
					$item['editor_url'] = "project_pages.php?action=change_block&item_id=\$global||$block_id||$block_name_mod&item_name=$block_name";
					$data['params'][] = $item;
				}
			}
		}
	}

	if (intval($_SESSION['save'][$page_name]['se_text_htaccess']) == 1 && mb_contains(file_get_contents("$config[project_path]/.htaccess"), $_SESSION['save'][$page_name]['se_text']))
	{
		$item = [];
		$item['type'] = 'htaccess';
		$item['filename'] = '.htaccess';
		$item['editor_url'] = "templates_search.php?action=htaccess";
		$data['htaccess'][] = $item;
	}

	if (intval($_SESSION['save'][$page_name]['se_text_advertising']) == 1)
	{
		$spots = get_site_spots();
		foreach ($spots as $external_id => $spot)
		{
			if (mb_contains($external_id, $_SESSION['save'][$page_name]['se_text']) || mb_contains($spot['template'], $_SESSION['save'][$page_name]['se_text']))
			{
				$item = [];
				$item['type'] = 'ad_spot';
				$item['external_id'] = $external_id;
				$item['filename'] = "spot_{$external_id}.dat";
				$item['spot_name'] = $spot['title'];
				$item['editor_url'] = "project_spots.php?action=change_spot&item_id=$external_id";
				$data['advertising'][] = $item;
			}
			foreach ($spot['ads'] as $advertisement_id => $ad)
			{
				if (mb_contains($ad['title'], $_SESSION['save'][$page_name]['se_text']) || mb_contains($ad['code'], $_SESSION['save'][$page_name]['se_text']))
				{
					$item = [];
					$item['type'] = 'ad';
					$item['advertisement_id'] = $advertisement_id;
					$item['filename'] = "spot_{$external_id}.dat";
					$item['advertisement_name'] = $ad['title'];
					$item['editor_url'] = "project_spots.php?action=change&item_id=$advertisement_id";
					$data['advertising'][] = $item;
				}
			}
		}
	}

	if (intval($_SESSION['save'][$page_name]['se_text_texts']) == 1)
	{
		$langs_dir = "$config[project_path]/langs";
		if (is_dir($langs_dir))
		{
			$languages = mr2array(sql("select code, title from $config[tables_prefix]languages order by language_id asc"));
			$texts = [];
			if (is_file("$langs_dir/default.lang"))
			{
				$file = fopen("$langs_dir/default.lang", 'r');
				while (($row = fgets($file)) !== false)
				{
					$row = trim($row);
					if ($row == '' || substr($row, 0, 1) == '#')
					{
						continue;
					}

					$pair = explode('=', $row, 2);
					if (array_cnt($pair) == 2)
					{
						$texts[trim($pair[0])] = array('external_id' => trim($pair[0]), 'text_default' => trim($pair[1]));
					}
				}
				fclose($file);
			}
			$default_lang = '';
			if (is_file("$config[project_path]/admin/data/config/theme.xml"))
			{
				if (strpos(file_get_contents("$config[project_path]/admin/data/config/theme.xml"), 'field id="theme.lang"') !== false && $texts['theme.lang'] != '' && is_file("$langs_dir/{$texts['theme.lang']['text_default']}.lang"))
				{
					$default_lang = $texts['theme.lang']['text_default'];
				}
			}
			if ($default_lang != '')
			{
				$file = fopen("$langs_dir/$default_lang.lang", 'r');
				while (($row = fgets($file)) !== false)
				{
					$row = trim($row);
					if ($row == '' || substr($row, 0, 1) == '#')
					{
						continue;
					}

					$pair = explode('=', $row, 2);
					if (array_cnt($pair) == 2)
					{
						$texts[trim($pair[0])] = array('external_id' => trim($pair[0]), 'text_default' => trim($pair[1]));
					}
				}
				fclose($file);
			}

			foreach ($languages as $language)
			{
				if (is_file("$langs_dir/$language[code].lang"))
				{
					$file = fopen("$langs_dir/$language[code].lang", 'r');
					while (($row = fgets($file)) !== false)
					{
						$row = trim($row);
						if ($row == '' || substr($row, 0, 1) == '#')
						{
							continue;
						}

						$pair = explode('=', $row, 2);
						if (array_cnt($pair) == 2)
						{
							if (isset($texts[trim($pair[0])]))
							{
								$texts[trim($pair[0])]["text_$language[code]"] = trim($pair[1]);
							}
						}
					}
					fclose($file);
				}
			}

			foreach ($texts as $text)
			{
				if (mb_contains($text['external_id'], $_SESSION['save'][$page_name]['se_text']))
				{
					$item = [];
					$item['type'] = 'lang_text';
					$item['external_id'] = $text['external_id'];
					$item['editor_url'] = "project_pages_lang_texts.php?action=change&item_id=$text[external_id]";
					$data['texts'][] = $item;
					continue;
				}
				if (mb_contains($text['text_default'], $_SESSION['save'][$page_name]['se_text']))
				{
					$item = [];
					$item['type'] = 'lang_text';
					$item['filename'] = 'default.lang';
					$item['language_code'] = 'default';
					$item['external_id'] = $text['external_id'];
					$item['editor_url'] = "project_pages_lang_texts.php?action=change&item_id=$text[external_id]";
					$data['texts'][] = $item;
					continue;
				}
				foreach ($languages as $language)
				{
					if (mb_contains($text["text_$language[code]"], $_SESSION['save'][$page_name]['se_text']))
					{
						$item = [];
						$item['type'] = 'lang_text';
						$item['filename'] = "$language[code].lang";
						$item['language_code'] = $language['code'];
						$item['language_title'] = $language['title'];
						$item['external_id'] = $text['external_id'];
						$item['editor_url'] = "project_pages_lang_texts.php?action=change&item_id=$text[external_id]";
						$data['texts'][] = $item;
						break;
					}
				}
			}
		}
	}

	if (intval($_SESSION['save'][$page_name]['se_text_scripts']) == 1)
	{
		$files = [];
		if (is_dir("$config[project_path]/static"))
		{
			$files = array_merge($files, list_files_recursive("$config[project_path]/static"));
		}
		if (is_dir("$config[project_path]/js"))
		{
			$files = array_merge($files, list_files_recursive("$config[project_path]/js"));
		}
		foreach ($files as $file)
		{
			if (substr(strtolower($file), -3) == '.js')
			{
				if (mb_contains(file_get_contents($file), $_SESSION['save'][$page_name]['se_text']))
				{
					$item = [];
					$item['type'] = 'script';
					$item['filename'] = basename($file);
					$item['file_url'] = str_replace($config['project_path'], $config['project_url'], $file);
					$data['scripts'][] = $item;
					continue;
				}
			}
		}
	}

	if (intval($_SESSION['save'][$page_name]['se_text_styles']) == 1)
	{
		$files = [];
		if (is_dir("$config[project_path]/static"))
		{
			$files = array_merge($files, list_files_recursive("$config[project_path]/static"));
		}
		if (is_dir("$config[project_path]/css"))
		{
			$files = array_merge($files, list_files_recursive("$config[project_path]/css"));
		}
		if (is_dir("$config[project_path]/styles"))
		{
			$files = array_merge($files, list_files_recursive("$config[project_path]/styles"));
		}
		foreach ($files as $file)
		{
			if (substr(strtolower($file), -4) == '.css')
			{
				if (mb_contains(file_get_contents($file), $_SESSION['save'][$page_name]['se_text']))
				{
					$item = [];
					$item['type'] = 'style';
					$item['filename'] = basename($file);
					$item['file_url'] = str_replace($config['project_path'], $config['project_url'], $file);
					$data['styles'][] = $item;
					continue;
				}
			}
		}
	}
}

$total_num = 0;
foreach ($data as $list)
{
	$total_num += array_cnt($list);
}

$smarty = new mysmarty();

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('grid_presets', $grid_presets);
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

$smarty->assign('page_title', $lang['website_ui']['submenu_option_template_search']);

$smarty->display("layout.tpl");
