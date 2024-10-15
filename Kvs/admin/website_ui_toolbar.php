<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
if (!isset($config))
{
	http_response_code(403);
	die('Access denied');
}

require_once "$config[project_path]/admin/include/setup_smarty.php";
require_once "$config[project_path]/admin/include/functions_admin.php";
require_once "$config[project_path]/admin/include/check_access.php";

function discover_template_structure($id, $type, $level, $template_file)
{
	global $config, $regexp_include_tpl, $regexp_insert_block, $regexp_insert_global, $regexp_insert_adv;

	$template_code = file_get_contents($template_file);

	$structure = [];

	$temp = [];
	$duplicates = [];
	preg_match_all($regexp_include_tpl, $template_code, $temp, PREG_OFFSET_CAPTURE);
	for ($i = 0; $i < array_cnt($temp[0]); $i++)
	{
		if (!isset($duplicates[$temp[1][$i][0]]))
		{
			$structure[$temp[0][$i][1]] = ['page_component_id' => $temp[1][$i][0]];
			$duplicates[$temp[1][$i][0]] = 1;
		}
	}

	$temp = [];
	preg_match_all($regexp_insert_adv, $template_code, $temp, PREG_OFFSET_CAPTURE);
	for ($i = 0; $i < array_cnt($temp[0]); $i++)
	{
		$spot_info = unserialize(file_get_contents("$config[project_path]/admin/data/advertisements/spot_{$temp[1][$i][0]}.dat"));
		$structure[$temp[0][$i][1]] = ['spot_id' => $temp[1][$i][0], 'title' => $spot_info['title']];
	}

	if ($type == 'page')
	{
		$temp = [];
		$blocks_passed_to_other = [];
		preg_match_all($regexp_insert_block, $template_code, $temp, PREG_OFFSET_CAPTURE);
		for ($i = 0; $i < array_cnt($temp[0]); $i++)
		{
			$passed_to_other_block = '';
			$block_start_pos = $temp[0][$i][1];
			$block_end_pos = strpos($template_code, '}}', $block_start_pos);
			if ($block_end_pos > $block_start_pos)
			{
				$block_insert_code = substr($template_code, $block_start_pos, $block_end_pos - $block_start_pos);
				$temp2 = [];
				if (preg_match('|assign\ *=[\'"\ ]*([^}]*?)[\'"]|is', $block_insert_code, $temp2) && $temp2[1])
				{
					$block_start_pos = strpos($template_code, "{{\$$temp2[1]");
					if ($block_start_pos === false)
					{
						$block_start_pos = strpos($template_code, "=\$$temp2[1]");
						if ($block_start_pos === false)
						{
							$block_start_pos = strlen($template_code) - 1;
						} else
						{
							$other_insert_start_pos = strrpos(strrev(substr($template_code, 0, $block_start_pos)), '}}');
							$other_insert_end_pos = strpos($template_code, '}}', $block_start_pos);
							if ($other_insert_start_pos !== false && $other_insert_end_pos !== false)
							{
								$other_insert = substr($template_code, $block_start_pos - $other_insert_start_pos + 1, $other_insert_start_pos + $other_insert_end_pos - $block_start_pos + 1);
								if (strpos($other_insert, '{{insert') === 0)
								{
									$temp2 = [];
									if (preg_match($regexp_insert_block, $other_insert, $temp2))
									{
										$passed_to_other_block = $temp2[1] . '_' . strtolower(str_replace(' ', '_', $temp2[2]));
									}
								}
							}
						}
					}
				}
			}

			$block_info = ['block_id' => $temp[1][$i][0], 'block_name' => strtolower(str_replace(' ', '_', $temp[2][$i][0])), 'block_uid' => $temp[1][$i][0] . '_' . strtolower(str_replace(' ', '_', $temp[2][$i][0])), 'title' => $temp[2][$i][0]];
			if ($passed_to_other_block)
			{
				$blocks_passed_to_other[$passed_to_other_block] = $block_info;
			} else
			{
				$structure[$block_start_pos] = $block_info;
			}
		}
		foreach ($blocks_passed_to_other as $block_uid => $block_passed_to_other)
		{
			$insert_as_key = 0;
			foreach ($structure as $key => $item)
			{
				if ($block_uid == $item['block_uid'])
				{
					$insert_as_key = $key + 1;
					break;
				}
			}
			if ($insert_as_key > 0)
			{
				$structure[$insert_as_key] = $block_passed_to_other;
			}
		}
	}

	if ($type == 'page' || $type == 'component')
	{
		$global_blocks_list = [];
		if (is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
		{
			$temp = explode('||', @file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
			$global_blocks = explode('|AND|', trim($temp[2]));
			foreach ($global_blocks as $global_block)
			{
				if ($global_block == '')
				{
					continue;
				}
				$block_id = substr($global_block, 0, strpos($global_block, '[SEP]'));
				$block_name = substr($global_block, strpos($global_block, '[SEP]') + 5);
				$global_blocks_list["{$block_id}_{$block_name}"] = ['title' => ucwords(str_replace('_', ' ', $block_name)), 'block_id' => $block_id, 'block_name' => $block_name];
			}
		}

		$temp = [];
		preg_match_all($regexp_insert_global, $template_code, $temp, PREG_OFFSET_CAPTURE);
		for ($i = 0; $i < array_cnt($temp[0]); $i++)
		{
			$structure[$temp[0][$i][1]] = ['global_uid' => $temp[1][$i][0], 'block_id' => $global_blocks_list[$temp[1][$i][0]]['block_id'], 'block_name' => $global_blocks_list[$temp[1][$i][0]]['block_name'], 'title' => $global_blocks_list[$temp[1][$i][0]]['title']];
		}
	}

	$result = [];

	ksort($structure);
	foreach ($structure as $item)
	{
		$inner_items = [];
		$parent_id = '';
		if ($item['page_component_id'])
		{
			$parent_id = "$id/component:$item[page_component_id]";
			$inner_items = discover_template_structure($parent_id, 'component', $level + 1, "$config[project_path]/template/$item[page_component_id]");
		} elseif ($item['block_uid'])
		{
			$parent_id = "$id/block:$item[block_uid]";
			$inner_items = discover_template_structure($parent_id, 'block', $level + 1, "$config[project_path]/template/blocks/$id/$item[block_uid].tpl");
		} elseif ($item['global_uid'])
		{
			$parent_id = "$id/global:$item[global_uid]";
			$inner_items = discover_template_structure($parent_id, 'block', $level + 1, "$config[project_path]/template/blocks/\$global/$item[global_uid].tpl");
		} elseif ($item['spot_id'])
		{
			$parent_id = "$id/spot:$item[spot_id]";
		}

		$item['id'] = $parent_id;
		$item['level'] = $level + 1;
		if (array_cnt($inner_items) > 0)
		{
			$item['has_structure'] = 1;
		}
		$result[] = $item;
		foreach ($inner_items as $inner_item)
		{
			if (!$inner_item['parent_id'])
			{
				$inner_item['parent_id'] = $parent_id;
			}
			$result[] = $inner_item;
		}
	}
	return $result;
}

$context_objects = [];
$page_definition = ['title' => $lang['common']['undefined']];
$pages = get_site_pages([$page_id]);

if (array_cnt($pages) == 1)
{
	$page_definition = $pages[0];
	$page_definition['exec_time'] = number_format($page_config['exec_stats']['exec_time'], 2);
	$page_definition['display_mode'] = $lang['website_ui']['toolbar_mode_generated'];
	if ($page_config['exec_stats']['was_cached'] == 1)
	{
		$page_definition['display_mode'] = $lang['website_ui']['toolbar_mode_cached'];
	}

	$page_structure = discover_template_structure($page_definition['external_id'], 'page', 1, "$config[project_path]/template/$page_definition[external_id].tpl");

	$page_definition['structure'] = [];
	foreach ($page_structure as $page_structure_item)
	{
		if ($page_structure_item['page_component_id'] || $page_structure_item['spot_id'])
		{
			$ad_structure_item = null;
			if ($page_structure_item['spot_id'])
			{
				if (array_cnt($page_config['ad_spots']) > 0)
				{
					foreach ($page_config['ad_spots'] as $ad_spot)
					{
						if ($page_structure_item['spot_id'] == $ad_spot['spot_id'])
						{
							$page_structure_item['has_structure'] = 1;
							$ad_structure_item = ['id' => "$page_structure_item[id]/$ad_spot[ad_id]", 'parent_id' => $page_structure_item['id'], 'level' => $page_structure_item['level'] + 1, 'ad_id' => $ad_spot['ad_id'], 'title' => $ad_spot['title']];
							break;
						}
					}
				}
			}
			$page_definition['structure'][] = $page_structure_item;
			if ($ad_structure_item)
			{
				$page_definition['structure'][] = $ad_structure_item;
			}
		} elseif ($page_structure_item['block_uid'])
		{
			if (array_cnt($page_config['blocks_list']) > 0)
			{
				foreach ($page_config['blocks_list'] as $page_block)
				{
					if ($page_structure_item['block_uid'] == $page_block['block_uid'] && isset($page_block['exec_stats']))
					{
						$page_structure_item['exec_time'] = number_format($page_block['exec_stats']['exec_time'], 2);
						$page_structure_item['display_mode'] = $lang['website_ui']['toolbar_mode_generated'];
						if ($page_block['exec_stats']['was_cached'] == 1)
						{
							$page_structure_item['display_mode'] = $lang['website_ui']['toolbar_mode_cached'];
						}
						$page_definition['structure'][] = $page_structure_item;
					}
				}
			}
		} elseif ($page_structure_item['global_uid'])
		{
			if (array_cnt($page_config['global_blocks_list']) > 0)
			{
				foreach ($page_config['global_blocks_list'] as $global_block)
				{
					if ($page_structure_item['global_uid'] == $global_block['global_uid'] && isset($global_block['exec_stats']))
					{
						$page_structure_item['exec_time'] = number_format($global_block['exec_stats']['exec_time'], 2);
						$page_structure_item['display_mode'] = $lang['website_ui']['toolbar_mode_generated'];
						if ($global_block['exec_stats']['was_cached'] == 1)
						{
							$page_structure_item['display_mode'] = $lang['website_ui']['toolbar_mode_cached'];
						}
						$page_definition['structure'][] = $page_structure_item;
					}
				}
			}
		}
	}

	$var_variables = [
			'category' =>             ['table' => 'categories',             'id' => 'category_id',               'name' => 'title',         'dir' => 'dir',     'supports_user' => false, 'editor' => 'categories.php',               'permission' => 'categories|view'],
			'category_group' =>       ['table' => 'categories_groups',      'id' => 'category_group_id',         'name' => 'title',         'dir' => 'dir',     'supports_user' => false, 'editor' => 'categories_groups.php',        'permission' => 'category_groups|view'],
			'tag' =>                  ['table' => 'tags',                   'id' => 'tag_id',                    'name' => 'tag',           'dir' => 'tag_dir', 'supports_user' => false, 'editor' => 'tags.php',                     'permission' => 'tags|view'],
			'model' =>                ['table' => 'models',                 'id' => 'model_id',                  'name' => 'title',         'dir' => 'dir',     'supports_user' => false, 'editor' => 'models.php',                   'permission' => 'models|view'],
			'model_group' =>          ['table' => 'models_groups',          'id' => 'model_group_id',            'name' => 'title',         'dir' => 'dir',     'supports_user' => false, 'editor' => 'models_groups.php',            'permission' => 'models_groups|view'],
			'content_source' =>       ['table' => 'content_sources',        'id' => 'content_source_id',         'name' => 'title',         'dir' => 'dir',     'supports_user' => false, 'editor' => 'content_sources.php',          'permission' => 'content_sources|view'],
			'content_source_group' => ['table' => 'content_sources_groups', 'id' => 'content_source_group_id',   'name' => 'title',         'dir' => 'dir',     'supports_user' => false, 'editor' => 'content_sources_groups.php',   'permission' => 'content_sources_groups|view'],
			'dvd' =>                  ['table' => 'dvds',                   'id' => 'dvd_id',                    'name' => 'title',         'dir' => 'dir',     'supports_user' => false, 'editor' => 'dvds.php',                     'permission' => 'dvds|view'],
			'dvd_group' =>            ['table' => 'dvds_groups',            'id' => 'dvd_group_id',              'name' => 'title',         'dir' => 'dir',     'supports_user' => false, 'editor' => 'dvds_groups.php',              'permission' => 'dvds_groups|view'],
			'video' =>                ['table' => 'videos',                 'id' => 'video_id',                  'name' => 'title',         'dir' => 'dir',     'supports_user' => true,  'editor' => 'videos.php',                   'permission' => 'videos|view'],
			'album' =>                ['table' => 'albums',                 'id' => 'album_id',                  'name' => 'title',         'dir' => 'dir',     'supports_user' => true,  'editor' => 'albums.php',                   'permission' => 'albums|view'],
			'post' =>                 ['table' => 'posts',                  'id' => 'post_id',                   'name' => 'title',         'dir' => 'dir',     'supports_user' => true,  'editor' => 'posts.php',                    'permission' => 'posts|view'],
			'playlist' =>             ['table' => 'playlists',              'id' => 'playlist_id',               'name' => 'title',         'dir' => 'dir',     'supports_user' => true,  'editor' => 'playlists.php',                'permission' => 'playlists|view'],
			'user' =>                 ['table' => 'users',                  'id' => 'user_id',                   'name' => 'display_name',  'dir' => '',        'supports_user' => false, 'editor' => 'users.php',                    'permission' => 'users|view'],
	];
	$found_objects = [];
	if (array_cnt($page_config['blocks_list']) > 0)
	{
		foreach ($page_config['blocks_list'] as $page_block)
		{
			foreach ($page_block['params'] as $block_param_name => $block_param_value)
			{
				foreach ($var_variables as $var_variable => $variable_data)
				{
					$object = null;
					if ($block_param_name == "var_{$var_variable}_id" && $block_param_value && $_REQUEST[$block_param_value])
					{
						$object = mr2array_single(sql_pr("select * from $config[tables_prefix]$variable_data[table] where $variable_data[id]=?", $_REQUEST[$block_param_value]));
					} elseif ($block_param_name == "var_{$var_variable}_dir" && $block_param_value && $_REQUEST[$block_param_value] && $variable_data['dir'])
					{
						$object = mr2array_single(sql_pr("select * from $config[tables_prefix]$variable_data[table] where $variable_data[dir]=?", $_REQUEST[$block_param_value]));
					}

					if ($object && !isset($found_objects["{$var_variable}_{$object[$variable_data['id']]}"]))
					{
						$found_objects["{$var_variable}_{$object[$variable_data['id']]}"] = 1;

						$variable_data['id'] = $object[$variable_data['id']];
						$variable_data['name'] = $object[$variable_data['name']] ?: $variable_data['id'];
						$variable_data['type'] = $var_variable;
						$context_objects[] = $variable_data;

						if ($variable_data['supports_user'] && $object['user_id'] > 0)
						{
							$object = mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?", $object['user_id']));
							if ($object)
							{
								$context_objects[] = ['table' => 'users', 'id' => $object['user_id'], 'name' => $object['display_name'], 'editor' => 'users.php', 'permission' => 'users|view', 'type' => 'user'];
							}
						}
					}
				}
			}
		}
	}
}

$smarty = new mysmarty();
$smarty->assign_by_ref('config', $config);
$smarty->assign_by_ref('lang', $lang);
$smarty->assign_by_ref('page_definition', $page_definition);
$smarty->assign_by_ref('context_objects', $context_objects);
$smarty->assign("admin_url", str_replace('www.', '', ($config['admin_url'] ?? $config['project_url'] . '/admin')));

return $smarty->fetch('website_ui_toolbar.tpl');