<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$kvs_blocks = array('list_content_sources', 'global_stats', 'invite_friend', 'tags_cloud_v2', 'list_tags', 'pagination', 'top_referers', 'list_content_sources_groups', 'playlist_edit', 'list_albums_images', 'content_source_group_view', 'list_members_events', 'album_edit', 'list_dvds', 'album_comments', 'random_video', 'album_images', 'list_models', 'list_models_groups', 'post_view', 'post_edit', 'list_dvds_groups', 'list_playlists', 'list_posts', 'content_source_view', 'list_content', 'member_profile_view', 'dvd_view', 'logon', 'search_results', 'dvd_edit', 'playlist_view', 'content_source_comments', 'list_members', 'list_comments', 'list_members_blog', 'list_videos', 'member_profile_delete', 'video_comments', 'post_comments', 'list_categories_groups', 'signup', 'dvd_comments', 'model_comments', 'video_edit', 'feedback', 'list_members_tokens', 'list_albums', 'list_categories', 'video_view', 'album_view', 'tags_cloud', 'playlist_comments', 'dvd_group_view', 'message_details', 'model_view', 'upgrade', 'list_messages', 'list_members_subscriptions', 'member_profile_edit');

if (isset($_GET['se_group_by']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = $_GET['se_group_by'];
}
if ($_SESSION['save'][$page_name]['se_group_by'] != 'type')
{
	$_SESSION['save'][$page_name]['se_group_by'] = 'functionality';
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

$awe_api_enabled = false;
if (is_file("$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php"))
{
	require_once "$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php";
	$awe_api_enabled = awe_black_labelIsEnabled();
	$awe_api_blocks = awe_black_labelListBlocks();
	if (is_array($awe_api_blocks))
	{
		$kvs_blocks = array_merge($kvs_blocks, $awe_api_blocks);
	}
}

$data = [];

$blocks_list = get_contents_from_dir("$config[project_path]/blocks", 2);
sort($blocks_list);
foreach ($blocks_list as $k => $v)
{
	if ($_GET['action'] == 'show_long_desc' && $_GET['block_id'] <> $v)
	{
		continue;
	}

	if (in_array($v, $awe_api_blocks) && !$awe_api_enabled)
	{
		continue;
	}

	$temp = array();
	$temp['external_id'] = $v;
	if (is_file("$config[project_path]/blocks/$v/$v.php") && is_file("$config[project_path]/blocks/$v/$v.dat"))
	{
		unset($res);
		exec("$config[php_path] -l $config[project_path]/blocks/$v/$v.php", $res, $response_code);
		if ($response_code == 255)
		{
			$temp['is_invalid'] = 1;
		} else
		{
			require_once "$config[project_path]/blocks/$v/$v.php";
			if (function_exists("{$v}Show") === false || function_exists("{$v}GetHash") === false || function_exists("{$v}MetaData") === false)
			{
				$temp['is_invalid'] = 1;
			}
		}

		$block_documentator = null;
		if (function_exists("{$v}Instance"))
		{
			$function = "{$v}Instance";
			$block_instance = $function();
			if ($block_instance instanceof KvsAbstractSiteBlock)
			{
				$block_documentator = new KvsSiteBlockDocumentator($block_instance);
			}
		}

		if (is_file("$config[project_path]/blocks/$v/langs/english.php"))
		{
			require_once "$config[project_path]/blocks/$v/langs/english.php";
		}
		if (($_SESSION['userdata']['lang'] != 'english') && is_file("$config[project_path]/blocks/$v/langs/" . $_SESSION['userdata']['lang'] . ".php"))
		{
			require_once "$config[project_path]/blocks/$v/langs/" . $_SESSION['userdata']['lang'] . ".php";
		}
		if (is_file("$config[project_path]/blocks/$v/$v.dat"))
		{
			$file_data = file_get_contents("$config[project_path]/blocks/$v/$v.dat");

			unset($temp_find);
			preg_match("|<block_name>(.*?)</block_name>|is", $file_data, $temp_find);
			$temp['block_name'] = trim($temp_find[1]);

			unset($temp_find);
			preg_match("|<author>(.*?)</author>|is", $file_data, $temp_find);
			$temp['author'] = trim($temp_find[1]);

			unset($temp_find);
			preg_match("|<version>(.*?)</version>|is", $file_data, $temp_find);
			$temp['version'] = trim($temp_find[1]);

			unset($temp_find);
			preg_match("|<deprecated>(.*?)</deprecated>|is", $file_data, $temp_find);
			if (trim($temp_find[1]) == 'yes')
			{
				continue;
			}

			unset($temp_find);
			preg_match("|<package>(.*?)</package>|is", $file_data, $temp_find);
			$temp['package'] = trim($temp_find[1]);
			switch ($temp['package'])
			{
				case 'ultimate':
					$temp['package'] = 4;
					break;
				case 'premium':
					$temp['package'] = 3;
					break;
				case 'advanced':
					$temp['package'] = 2;
					break;
				default:
					$temp['package'] = 1;
			}

			unset($temp_find);
			preg_match("|<types>(.*?)</types>|is", $file_data, $temp_find);
			$temp['types'] = explode(',', trim($temp_find[1]));

			unset($temp_find);
			preg_match("|<functionalities>(.*?)</functionalities>|is", $file_data, $temp_find);
			$temp['functionalities'] = explode(',', trim($temp_find[1]));
		}

		if ($block_documentator)
		{
			$block_title = $block_documentator->get_block_title();
			if ($block_title)
			{
				$temp['short_desc'] = $block_title;
			}
			$block_desc = $block_documentator->get_block_description();
			if ($block_desc)
			{
				$temp['desc'] = $block_desc;
			}
			$block_examples = $block_documentator->get_block_examples();
			if ($block_examples)
			{
				$temp['examples'] = $block_examples;
			}
		}

		if (!isset($temp['short_desc']))
		{
			$temp['short_desc'] = $lang[$v]['block_short_desc'];
		}
		if (!isset($temp['desc']))
		{
			$temp['desc'] = $lang[$v]['block_desc'];
		}
		if (!isset($temp['examples']))
		{
			$temp['examples'] = $lang[$v]['block_examples'];
		}

		if (!in_array($v, $kvs_blocks))
		{
			$temp['types'][] = 'custom';
		}
	} else
	{
		$temp['is_invalid'] = 1;
	}
	if ($temp['is_invalid'] == 1)
	{
		$_POST['errors'][] = get_aa_error('website_ui_block_implementation_broken', $v);
	}
	if ($_SESSION['save'][$page_name]['se_text'] == '' || strpos($v, $_SESSION['save'][$page_name]['se_text']) !== false)
	{
		$data[] = $temp;
	}
}

if ($_GET['action'] == 'show_long_desc' && $_GET['block_id'] <> '')
{
	foreach ($data as $res)
	{
		if ($res['external_id'] == $_GET['block_id'])
		{
			$_POST['external_id'] = $res['external_id'];
			$_POST['desc'] = $res['desc'];
			$_POST['examples'] = $res['examples'];

			$block_id = $res['external_id'];

			require_once "$config[project_path]/blocks/$block_id/$block_id.php";

			$block_documentator = null;
			if (function_exists("{$block_id}Instance"))
			{
				$function = "{$block_id}Instance";
				$block_instance = $function();
				if ($block_instance instanceof KvsAbstractSiteBlock)
				{
					$block_documentator = new KvsSiteBlockDocumentator($block_instance);
				}
			}

			$meta_function = "{$block_id}MetaData";
			$block_params = $meta_function();

			foreach ($block_params as $k => $param)
			{
				$param_type = $param['type'];
				if (strpos($param['type'], "SORTING") === 0)
				{
					$param_type = "SORTING";
				}
				if (strpos($param['type'], "CHOICE") === 0)
				{
					$param_type = "CHOICE";
				}

				if ($param_type == 'INT')
				{
					$block_params[$k]['type'] = $lang['website_ui']['common_type_int'];
				} elseif ($param_type == 'INT_LIST')
				{
					$block_params[$k]['type'] = $lang['website_ui']['common_type_int_list'];
				} elseif ($param_type == 'INT_PAIR')
				{
					$block_params[$k]['type'] = $lang['website_ui']['common_type_int_pair'];
				} elseif ($param_type == 'STRING')
				{
					$block_params[$k]['type'] = $lang['website_ui']['common_type_string'];
				} elseif ($param_type == '')
				{
					$block_params[$k]['type'] = $lang['website_ui']['common_type_boolean'];
				} elseif ($param_type == 'SORTING')
				{
					$block_params[$k]['type'] = $lang['website_ui']['common_type_sorting'];
				} elseif ($param_type == 'CHOICE')
				{
					$block_params[$k]['type'] = $lang['website_ui']['common_type_choice'];
				} elseif ($param_type == 'LIST_BLOCK')
				{
					$block_params[$k]['type'] = $lang['website_ui']['common_type_list_block'];
				}

				unset($temp_param_number);
				if ($block_documentator)
				{
					$param_desc = $block_documentator->get_block_parameter_description($param['name']);
					if ($param_desc)
					{
						$block_params[$k]['desc'] = $param_desc;
					}
				}
				if (!isset($block_params[$k]['desc']))
				{
					$block_params[$k]['desc'] = $lang[$block_id]['params'][$param['name']] ?: $lang['website_ui']["parameter_default_{$param['name']}"];
					if ($block_params[$k]['desc'] == '' && preg_match('|^([^0-9]+)([0-9]+)$|', $param['name'], $temp_param_number))
					{
						$block_params[$k]['desc'] = str_replace('%1%', $temp_param_number[2], $lang['website_ui']["parameter_default_{$temp_param_number[1]}"]);
					}
				}

				if ($param['group'] != '')
				{
					if ($block_documentator)
					{
						$group_desc = $block_documentator->get_block_parameter_group_description($param['group']);
						if ($group_desc)
						{
							$block_params[$k]['group_desc'] = $group_desc;
						}
					}
					if (!isset($block_params[$k]['group_desc']))
					{
						$block_params[$k]['group_desc'] = $lang[$block_id]['groups'][$param['group']] ?: $lang['website_ui']["block_group_default_{$param['group']}"];
					}
				}
			}
			$_POST['params'] = $block_params;

			$_POST['template'] = '';
			if ($block_documentator)
			{
				$_POST['template'] = $block_documentator->get_block_template_example();
			}
			if ($_POST['template'] == '' && is_file("$config[project_path]/blocks/$block_id/$block_id.tpl"))
			{
				$_POST['template'] = file_get_contents("$config[project_path]/blocks/$block_id/$block_id.tpl");
			}
			break;
		}
	}
}

$total_num = array_cnt($data);

$data_temp = array();
if ($_SESSION['save'][$page_name]['se_group_by'] == 'type')
{
	foreach ($data as $block)
	{
		if (array_cnt($block['types']) == 0)
		{
			$block['types'][] = 'misc';
		}
		foreach ($block['types'] as $type)
		{
			if (!$type)
			{
				$type = 'misc';
			}
			$data_temp["type:$type"][] = $block;
		}
	}
	$data = $data_temp;
} elseif ($_SESSION['save'][$page_name]['se_group_by'] == 'functionality')
{
	foreach ($data as $block)
	{
		if (array_cnt($block['functionalities']) == 0)
		{
			$block['functionalities'][] = 'misc';
		}
		foreach ($block['functionalities'] as $functionality)
		{
			if (!$functionality)
			{
				$functionality = 'misc';
			}
			$data_temp["functionality:$functionality"][] = $block;
		}
	}
	$data = $data_temp;
}

$smarty = new mysmarty();

if ($_REQUEST['action'] == 'show_long_desc')
{
	$smarty->assign('supports_popups', 1);
}

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

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'show_long_desc')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['external_id'], $lang['website_ui']['block_view']));
} else
{
	$smarty->assign('page_title', $lang['website_ui']['submenu_option_blocks_list']);
}

$smarty->display("layout.tpl");
