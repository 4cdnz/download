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

$list_embed_profiles=array();
$embed_folders=get_contents_from_dir("$config[project_path]/admin/data/player/embed",2);
foreach ($embed_folders as $embed_folder)
{
	if (is_file("$config[project_path]/admin/data/player/embed/$embed_folder/config.dat"))
	{
		$embed_profile=@unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/$embed_folder/config.dat"));
		if ($embed_profile['embed_profile_id'])
		{
			$list_embed_profiles[$embed_profile['embed_profile_id']]=array(
				'embed_profile_name'=>$embed_profile['embed_profile_name'],
				'embed_profile_domains'=>array_map('trim',explode(',',$embed_profile['embed_profile_domains']))
			);
		}
	}
}

$list_skins=array();
$skin_files=get_contents_from_dir("$config[project_path]/player/skin",1);
foreach ($skin_files as $skin_file)
{
	if (substr($skin_file, -4) == '.css')
	{
		$list_skins[]=$skin_file;
	}
}

$list_formats_timeline_screenshots=mr2array(sql("select *, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=2"));

$list_formats_videos_groups = mr2array(sql("select * from $config[tables_prefix]formats_videos_groups where format_video_group_id in (select format_video_group_id from $config[tables_prefix]formats_videos)"));

$ad_spots = get_site_spots();
$vast_profiles = get_vast_profiles();
$vast_key_data = @unserialize(file_get_contents("$config[project_path]/admin/data/player/vast/key.dat"), ['allowed_classes' => false]) ?: [];

$errors = null;

if ($_POST['action'] == 'quick_save' && $_POST['is_embed'] == 1)
{
	$player_path = "$config[project_path]/admin/data/player/embed";
	if ($_POST['embed_profile_id'] != '')
	{
		$player_path = "$config[project_path]/admin/data/player/embed/" . md5($_POST['embed_profile_id']);
	}

	if (isset($_POST['embed_template']))
	{
		validate_field('empty', $_POST['embed_template'], $lang['settings']['player_field_embed_template']);
		if (!is_writable("$player_path/config.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$player_path/config.dat");
		}

		if (!is_array($errors))
		{
			file_put_contents("$player_path/config.tpl", $_POST['embed_template'], LOCK_EX);

			$last_audit_record = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_audit_log where user_id=? order by added_date desc limit 1", $_SESSION['userdata']['user_id']));
			if (empty($last_audit_record) || $last_audit_record['action_details'] != 'embed_template' || $last_audit_record['action_id'] != 226 || $last_audit_record['object_type_id'] != 30)
			{
				sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=226, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], 'embed_template', date("Y-m-d H:i:s"));
			}
			return_ajax_success($page_name);
		} else
		{
			return_ajax_errors($errors);
		}
	}
	if (isset($_POST['error_template']))
	{
		validate_field('empty', $_POST['error_template'], $lang['settings']['player_field_error_template']);
		if (!is_writable("$player_path/config.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$player_path/config.dat");
		}

		if (!is_array($errors))
		{
			file_put_contents("$player_path/error.tpl", $_POST['error_template'], LOCK_EX);

			$last_audit_record = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_audit_log where user_id=? order by added_date desc limit 1", $_SESSION['userdata']['user_id']));
			if (empty($last_audit_record) || $last_audit_record['action_details'] != 'error_template' || $last_audit_record['action_id'] != 226 || $last_audit_record['object_type_id'] != 30)
			{
				sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=226, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], 'error_template', date("Y-m-d H:i:s"));
			}
			return_ajax_success($page_name);
		} else
		{
			return_ajax_errors($errors);
		}
	}
}

if ($_POST['action']=='change_complete')
{
	$new_vast_key_data = @json_decode(get_page('', "https://www.kernel-scripts.com/get_vast.php?domain=$config[project_licence_domain]&license_code=$config[player_license_code]", '', '', 1, 0, 20, ''), true);
	if (is_array($new_vast_key_data) && $new_vast_key_data['domain'] == $config['project_licence_domain'])
	{
		if (!mkdir_recursive("$config[project_path]/admin/data/player/vast"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/player/vast");
		}
		if (!$vast_key_data['primary_vast_key'] || $vast_key_data['domain'] != $new_vast_key_data['domain'])
		{
			$vast_key_data = $new_vast_key_data;
			file_put_contents("$config[project_path]/admin/data/player/vast/key.dat", serialize($new_vast_key_data), LOCK_EX);
		} else
		{
			$vast_key_valid = intval(substr($vast_key_data['primary_vast_key'], 0, 10));
			$new_vast_key_valid = intval(substr($new_vast_key_data['primary_vast_key'], 0, 10));
			if ($new_vast_key_valid > $vast_key_valid || $new_vast_key_data['aliases_hash'] != $vast_key_data['aliases_hash'])
			{
				$vast_key_data = $new_vast_key_data;
				file_put_contents("$config[project_path]/admin/data/player/vast/key.dat", serialize($new_vast_key_data), LOCK_EX);
			}
		}
	}

	if ($_POST['is_embed']==1)
	{
		if ($_POST['embed_profile_id']!='')
		{
			if ($_POST['embed_profile_id']=='new')
			{
				$_POST['embed_profile_id']=md5(time());
			}

			if (validate_field('empty',$_POST['embed_profile_name'],$lang['settings']['player_field_embed_profile_name']))
			{
				foreach ($list_embed_profiles as $embed_profile_id=>$embed_profile)
				{
					if ($_POST['embed_profile_id']!=$embed_profile_id && trim($_POST['embed_profile_name'])==$embed_profile['embed_profile_name'])
					{
						$errors[]=get_aa_error('unique_field',$lang['settings']['player_field_embed_profile_name']);
						break;
					}
				}
			}
			if (validate_field('empty',$_POST['embed_profile_domains'],$lang['settings']['player_field_embed_profile_domains']))
			{
				$temp_embed_profile_domains=array_map('trim',explode(',',$_POST['embed_profile_domains']));
				foreach ($list_embed_profiles as $embed_profile_id=>$embed_profile)
				{
					if ($_POST['embed_profile_id']!=$embed_profile_id)
					{
						foreach ($temp_embed_profile_domains as $temp_embed_profile_domain)
						{
							if (in_array($temp_embed_profile_domain,$embed_profile['embed_profile_domains']))
							{
								$errors[]=get_aa_error('player_duplicate_embed_profile_domains',$lang['settings']['player_field_embed_profile_domains'],$temp_embed_profile_domain,$embed_profile['embed_profile_name']);
							}
						}
					}
				}
			}
		}
		validate_field('empty',$_POST['embed_template'],$lang['settings']['player_field_embed_template']);
		if ($_POST['embed_cache_time']!='0')
		{
			validate_field('empty_int',$_POST['embed_cache_time'],$lang['settings']['player_field_embed_cache_time']);
		}
	}

	validate_field('empty_int',$_POST['width'],$lang['settings']['player_field_size']);
	validate_field('empty_int',$_POST['height'],$lang['settings']['player_field_size']);

	if (!in_array($_POST['skin'], $list_skins) && $_POST['skin'] != 'disable')
	{
		validate_field('empty', '', $lang['settings']['player_field_skin']);
	}

	if (intval($_POST['loop'])==2)
	{
		validate_field('empty_int',$_POST['loop_duration'],$lang['settings']['player_field_loop']);
	}

	if (intval($_POST['enable_adblock_protection'])==1)
	{
		if (validate_field('empty_int',$_POST['adblock_protection_html_after'],$lang['settings']['player_field_adblock_protection']))
		{
			validate_field('empty',$_POST['adblock_protection_html'],$lang['settings']['player_field_adblock_protection']);
		}
	}

	if ($_POST['timeline_screenshots_size'])
	{
		$valid_format=false;
		foreach ($list_formats_timeline_screenshots as $format)
		{
			if ($format['size']==$_POST['timeline_screenshots_size'])
			{
				$valid_format=true;
				break;
			}
		}
		if (!$valid_format)
		{
			validate_field('empty','',$lang['settings']['player_field_timeline_screenshots']);
		}
	}

	if ($_POST['is_embed'] == 1)
	{
		if ($_POST['affiliate_param_name'] != '')
		{
			$dynamic_params = unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));

			$affiliate_params = array_map('trim', explode(',', $_POST['affiliate_param_name']));
			foreach ($affiliate_params as $affiliate_param)
			{
				if ($affiliate_param != '' && strpos($affiliate_param, '=') === false)
				{
					$has_dynamic_param = false;
					foreach ($dynamic_params as $dynamic_param)
					{
						if ($affiliate_param == $dynamic_param['name'])
						{
							$has_dynamic_param = true;
							break;
						}
					}
					if (!$has_dynamic_param)
					{
						$errors[] = get_aa_error('website_settings_runtime_parameter', $lang['settings']['player_field_affiliate_param_name'], $affiliate_param);
					}
				}
			}
		}
	}

	validate_field('empty_int',$_POST['pre_roll_vast_timeout'],$lang['settings']['player_field_advertising_vast_timeout']);

	validate_field('file','logo',$lang['settings']['player_field_logo'],array('allowed_ext'=>$config['image_allowed_ext'],'strict_mode'=>'1'));

	if ($_POST['logo_url'] != '' && intval($_POST['logo_url_source']) != 2)
	{
		validate_field('url', $_POST['logo_url'], $lang['settings']['player_divider_branding_settings'] . " - " . $lang['settings']['player_field_logo_url'], array('is_related_allowed' => 1));
	}

	if ($_POST['logo_position_x']<>'' && $_POST['logo_position_x']<>'0') {validate_field('empty_int',$_POST['logo_position_x'],$lang['settings']['player_divider_branding_settings']." - ".$lang['settings']['player_field_logo_position']." - ".$lang['settings']['player_field_logo_position_x']);}
	if ($_POST['logo_position_y']<>'' && $_POST['logo_position_y']<>'0') {validate_field('empty_int',$_POST['logo_position_y'],$lang['settings']['player_divider_branding_settings']." - ".$lang['settings']['player_field_logo_position']." - ".$lang['settings']['player_field_logo_position_y']);}

	if ($_POST['controlbar_ad_url'] != '' && intval($_POST['controlbar_ad_url_source']) != 2)
	{
		validate_field('url', $_POST['controlbar_ad_url'], $lang['settings']['player_divider_branding_settings'] . " - " . $lang['settings']['player_field_controlbar_ad_url'], array('is_related_allowed' => 1));
	}

	if ($_POST['format_redirect_url_source']=='1')
	{
		validate_field('url',$_POST['format_redirect_url'],$lang['settings']['player_divider_formats_settings']." - ".$lang['settings']['player_field_format_redirect'],array('is_related_allowed'=>1));
	} elseif ($_POST['format_redirect_url_source']=='3')
	{
		if ($_POST['format_redirect_url']=='')
		{
			$errors[]=get_aa_error('player_no_default_url',$lang['settings']['player_divider_formats_settings']." - ".$lang['settings']['player_field_format_redirect']);
		} else {
			validate_field('url',$_POST['format_redirect_url'],$lang['settings']['player_divider_formats_settings']." - ".$lang['settings']['player_field_format_redirect'],array('is_related_allowed'=>1));
		}
	}

	if ($_POST['is_embed'] == 1)
	{
		$formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2) and access_level_id=0"));
	} else
	{
		$formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2)"));
	}

	$formats = [];
	foreach ($list_formats_videos_groups as $format_group)
	{
		$group_id = $format_group['format_video_group_id'];
		if (isset($_POST["group{$group_id}_slot1"]))
		{
			unset($group);
			for ($i = 1; $i <= 7; $i++)
			{
				if (trim($_POST["group{$group_id}_slot{$i}"]) != '')
				{
					$slot = array();
					$slot['type'] = trim($_POST["group{$group_id}_slot{$i}"]);
					$slot['title'] = trim($_POST["group{$group_id}_slot_title{$i}"]);
					if ($slot['type'] != 'redirect' && intval($_POST["group{$group_id}_default"]) == $i)
					{
						$slot['is_default'] = 1;
					}
					$group[] = $slot;
				} else
				{
					break;
				}
			}
			if (is_array($group))
			{
				$formats[$group_id] = $group;
			}
		}
	}
	foreach ($formats as $group_id => $list)
	{
		$group_title = '';
		foreach ($list_formats_videos_groups as $format_group)
		{
			if ($format_group['format_video_group_id'] == $group_id)
			{
				$group_title = ' - ' . $format_group['title'];
				break;
			}
		}

		$video_formats = array();
		$has_missing_titles = false;
		$has_missing_formats = false;
		foreach ($list as $item)
		{
			if ($item['type'] != 'redirect' && in_array($item['type'], $video_formats))
			{
				$errors[] = get_aa_error('player_duplicate_formats', $lang['settings']['player_divider_formats_settings'] . $group_title);
			} elseif ($item['type'] != 'redirect')
			{
				$video_formats[] = $item['type'];

				$has_format = false;
				foreach ($formats_videos as $format)
				{
					if ($format['format_video_group_id'] == $group_id && $format['postfix'] == $item['type'])
					{
						$has_format = true;
						break;
					}
				}
				if (!$has_format)
				{
					$has_missing_formats = true;
				}
			}
			if ($item['title'] == '')
			{
				$has_missing_titles = true;
			}
		}
		if (array_cnt($list) > 1 && $has_missing_titles)
		{
			$errors[] = get_aa_error('required_field', $lang['settings']['player_divider_formats_settings'] . $group_title . " - " . $lang['settings']['player_formats_col_player_title']);
		}
		if ($has_missing_formats)
		{
			$errors[] = get_aa_error('required_field', $lang['settings']['player_divider_formats_settings'] . $group_title . " - " . $lang['settings']['player_formats_col_format']);
		}
	}

	if ($_POST['enable_video_click'] == 1)
	{
		if ($_POST['video_click_url_source'] == '1' || $_POST['video_click_url'] != '')
		{
			validate_field('url', $_POST['video_click_url'], $lang['settings']['player_divider_click_settings'] . " - " . $lang['settings']['player_field_video_click_url'], array('is_related_allowed' => 1));
		}
	}
	if ($_POST['enable_popunder'] == 1)
	{
		if ($_POST['popunder_url_source'] == '1' || $_POST['popunder_url'] != '')
		{
			validate_field('url', $_POST['popunder_url'], $lang['settings']['player_divider_click_settings'] . " - " . $lang['settings']['player_field_popunder_url'], array('is_related_allowed' => 1));
		}
		if ($_POST['popunder_duration'] != '' && $_POST['popunder_duration'] != '0')
		{
			validate_field('empty_int', $_POST['popunder_duration'], $lang['settings']['player_field_popunder_duration']);
		}
		if ($_POST['popunder_start_option']==1)
		{
			validate_field('empty_int',$_POST['popunder_start_after'],$lang['settings']['player_divider_click_settings']." - ".$lang['settings']['player_field_popunder_start']);
		}
	}

	if ($_POST['enable_start_html']==1)
	{
		if ($_POST['start_html_source']==1)
		{
			validate_field('empty',$_POST['start_html_code'],$lang['settings']['player_divider_start_settings']." - ".$lang['settings']['player_field_start_html_code']);
		} elseif (strpos($_POST['start_html_source'],'spot_')!==false)
		{
			if (!isset($ad_spots[substr($_POST['start_html_source'],5)]))
			{
				$errors[]=get_aa_error('player_invalid_spot',$lang['settings']['player_divider_start_settings']." - ".$lang['settings']['player_field_start_html_code']);
			}
		}
		if (intval($_POST['start_html_adaptive'])==1)
		{
			if (validate_field('empty_int',$_POST['start_html_adaptive_width'],$lang['settings']['player_divider_start_settings']." - ".$lang['settings']['common_field_advertising_html_adaptive']))
			{
				validate_field('empty_int',$_POST['start_html_adaptive_height'],$lang['settings']['player_divider_start_settings']." - ".$lang['settings']['common_field_advertising_html_adaptive']);
			}
		}
	}
	if ($_POST['enable_pre_roll']==1)
	{
		if ($_POST['pre_roll_file_source']==1)
		{
			validate_field('file','pre_roll_file',$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_file'],array('is_required'=>1,'allowed_ext'=>$config['image_allowed_ext'].",mp4",'strict_mode'=>'1'));
		} else {
			validate_field('file','pre_roll_file',$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_file'],array('allowed_ext'=>$config['image_allowed_ext'].",mp4",'strict_mode'=>'1'));
		}
		validate_field('url',$_POST['pre_roll_url'],$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_url'],array('is_related_allowed'=>1));
	}
	if ($_POST['enable_pre_roll_html']==1)
	{
		if ($_POST['pre_roll_html_source']==1)
		{
			validate_field('empty',$_POST['pre_roll_html_code'],$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_html_code']);
		} elseif (strpos($_POST['pre_roll_html_source'],'spot_')!==false)
		{
			if (!isset($ad_spots[substr($_POST['pre_roll_html_source'],5)]))
			{
				$errors[]=get_aa_error('player_invalid_spot',$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_html_code']);
			}
		}
		if (intval($_POST['pre_roll_html_adaptive'])==1)
		{
			if (validate_field('empty_int',$_POST['pre_roll_html_adaptive_width'],$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['common_field_advertising_html_adaptive']))
			{
				validate_field('empty_int',$_POST['pre_roll_html_adaptive_height'],$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['common_field_advertising_html_adaptive']);
			}
		}
	}
	if ($_POST['enable_pre_roll_vast']==1)
	{
		if (strpos($_POST['pre_roll_vast_provider'],'vast_profile_')===false && strpos($_POST['pre_roll_vast_provider'],'cs_custom_')===false)
		{
			validate_field('empty',$_POST['pre_roll_vast_url'],$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['common_field_advertising_vast_url']);
		}
		if ($_POST['pre_roll_vast_provider']=='c' || strpos($_POST['pre_roll_vast_provider'],'vast_profile_')!==false || strpos($_POST['pre_roll_vast_provider'],'cs_custom_')!==false)
		{
			if (!$vast_key_data['primary_vast_key'])
			{
				$errors[] = get_aa_error('player_no_vast_subscription', $lang['settings']['player_field_advertising_vast_key']);
			}
		}
	}
	if ($_POST['enable_pre_roll']==1 || $_POST['enable_pre_roll_html']==1)
	{
		validate_field('empty_int',$_POST['pre_roll_duration'],$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_duration']);
	}
	if ($_POST['pre_roll_start_option']==1)
	{
		validate_field('empty_int',$_POST['pre_roll_start_after'],$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_start']);
	}
	if ($_POST['pre_roll_replay_option']==1)
	{
		validate_field('empty_int',$_POST['pre_roll_replay_after'],$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_frequency']);
	}
	if ($_POST['enable_pre_roll_skip']==1)
	{
		validate_field('empty',$_POST['pre_roll_skip_text2'],$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_skip_text2']);
	}
	if ($_POST['enable_post_roll']==1)
	{
		if ($_POST['post_roll_file_source']==1)
		{
			validate_field('file','post_roll_file',$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['player_field_post_roll_file'],array('is_required'=>1,'allowed_ext'=>$config['image_allowed_ext'].",mp4",'strict_mode'=>'1'));
		} else {
			validate_field('file','post_roll_file',$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['player_field_post_roll_file'],array('allowed_ext'=>$config['image_allowed_ext'].",mp4",'strict_mode'=>'1'));
		}
		validate_field('url',$_POST['post_roll_url'],$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['player_field_post_roll_url'],array('is_related_allowed'=>1));
	}
	if ($_POST['enable_post_roll_html']==1)
	{
		if ($_POST['post_roll_html_source']==1)
		{
			validate_field('empty',$_POST['post_roll_html_code'],$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['player_field_post_roll_html_code']);
		} elseif (strpos($_POST['post_roll_html_source'],'spot_')!==false)
		{
			if (!isset($ad_spots[substr($_POST['post_roll_html_source'],5)]))
			{
				$errors[]=get_aa_error('player_invalid_spot',$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['player_field_post_roll_html_code']);
			}
		}
		if (intval($_POST['post_roll_html_adaptive'])==1)
		{
			if (validate_field('empty_int',$_POST['post_roll_html_adaptive_width'],$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['common_field_advertising_html_adaptive']))
			{
				validate_field('empty_int',$_POST['post_roll_html_adaptive_height'],$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['common_field_advertising_html_adaptive']);
			}
		}
	}
	if ($_POST['enable_post_roll_vast']==1)
	{
		if (strpos($_POST['post_roll_vast_provider'],'vast_profile_')===false && strpos($_POST['post_roll_vast_provider'],'cs_custom_')===false)
		{
			validate_field('empty',$_POST['post_roll_vast_url'],$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['common_field_advertising_vast_url']);
		}
		if ($_POST['post_roll_vast_provider']=='c' || strpos($_POST['post_roll_vast_provider'],'vast_profile_')!==false || strpos($_POST['post_roll_vast_provider'],'cs_custom_')!==false)
		{
			if (!$vast_key_data['primary_vast_key'])
			{
				$errors[] = get_aa_error('player_no_vast_subscription', $lang['settings']['player_field_advertising_vast_key']);
			}
		}
	}
	if ($_POST['post_roll_duration']<>'')
	{
		validate_field('empty_int',$_POST['post_roll_duration'],$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['player_field_post_roll_duration']);
	}
	if ($_POST['enable_post_roll_skip']==1)
	{
		validate_field('empty',$_POST['post_roll_skip_text2'],$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['player_field_post_roll_skip_text2']);
	}
	if ($_POST['enable_pause']==1)
	{
		if ($_POST['pause_file_source']==1)
		{
			validate_field('file','pause_file',$lang['settings']['player_divider_pause_settings']." - ".$lang['settings']['player_field_pause_file'],array('is_required'=>1,'allowed_ext'=>$config['image_allowed_ext'],'strict_mode'=>'1'));
		} else {
			validate_field('file','pause_file',$lang['settings']['player_divider_pause_settings']." - ".$lang['settings']['player_field_pause_file'],array('allowed_ext'=>$config['image_allowed_ext'],'strict_mode'=>'1'));
		}
		validate_field('url',$_POST['pause_url'],$lang['settings']['player_divider_pause_settings']." - ".$lang['settings']['player_field_pause_url'],array('is_related_allowed'=>1));
	}
	if ($_POST['enable_pause_html']==1)
	{
		if ($_POST['pause_html_source']==1)
		{
			validate_field('empty',$_POST['pause_html_code'],$lang['settings']['player_divider_pause_settings']." - ".$lang['settings']['player_field_pause_html_code']);
		} elseif (strpos($_POST['pause_html_source'],'spot_')!==false)
		{
			if (!isset($ad_spots[substr($_POST['pause_html_source'],5)]))
			{
				$errors[]=get_aa_error('player_invalid_spot',$lang['settings']['player_divider_pause_settings']." - ".$lang['settings']['player_field_pause_html_code']);
			}
		}
		if (intval($_POST['pause_html_adaptive'])==1)
		{
			if (validate_field('empty_int',$_POST['pause_html_adaptive_width'],$lang['settings']['player_divider_pause_settings']." - ".$lang['settings']['common_field_advertising_html_adaptive']))
			{
				validate_field('empty_int',$_POST['pause_html_adaptive_height'],$lang['settings']['player_divider_pause_settings']." - ".$lang['settings']['common_field_advertising_html_adaptive']);
			}
		}
	}

	for ($i=1;$i<=4;$i++)
	{
		if ($_POST["enable_float$i"]==1)
		{
			if ($_POST["float{$i}_time"]!='0')
			{
				validate_field('empty_int',$_POST["float{$i}_time"],str_replace("%1%","$i",$lang['settings']['player_field_float_enable'])." - ".$lang['settings']['player_field_float_time']);
			}
			if ($_POST["float{$i}_duration"]!='0')
			{
				validate_field('empty_int',$_POST["float{$i}_duration"],str_replace("%1%","$i",$lang['settings']['player_field_float_enable'])." - ".$lang['settings']['player_field_float_duration']);
			}
			if ($_POST["float{$i}_file_source"]==1)
			{
				validate_field('file',"float{$i}_file",str_replace("%1%","$i",$lang['settings']['player_field_float_enable'])." - ".$lang['settings']['player_field_float_file'],array('is_required'=>1,'allowed_ext'=>$config['image_allowed_ext'],'strict_mode'=>'1'));
			} else {
				validate_field('file',"float{$i}_file",str_replace("%1%","$i",$lang['settings']['player_field_float_enable'])." - ".$lang['settings']['player_field_float_file'],array('allowed_ext'=>$config['image_allowed_ext'],'strict_mode'=>'1'));
			}
			validate_field('url',$_POST["float{$i}_url"],str_replace("%1%","$i",$lang['settings']['player_field_float_enable'])." - ".$lang['settings']['player_field_float_url'],array('is_related_allowed'=>1));
		}
	}

	$file_names=array();
	if ($_POST['logo']<>'') {$file_names[]=$_POST['logo'];}
	if ($_POST['pre_roll_file']<>'') {$file_names[]=$_POST['pre_roll_file'];}
	if ($_POST['post_roll_file']<>'') {$file_names[]=$_POST['post_roll_file'];}
	if ($_POST['pause_file']<>'') {$file_names[]=$_POST['pause_file'];}
	for ($i=1;$i<=4;$i++)
	{
		if ($_POST["float{$i}_file"]<>'') {$file_names[]=$_POST["float{$i}_file"];}
	}
	if (array_cnt($file_names)<>array_cnt(array_unique($file_names))) {$errors[]=get_aa_error('object_duplicate_files');}

	if ($_POST['is_embed']=='1')
	{
		$player_path="$config[project_path]/admin/data/player/embed";
		$player_files_path="$config[content_path_other]/player/embed";
		if ($_POST['embed_profile_id']!='')
		{
			$player_path="$config[project_path]/admin/data/player/embed/".md5($_POST['embed_profile_id']);
			$player_files_path="$config[content_path_other]/player/embed/".md5($_POST['embed_profile_id']);
		}
	} elseif (intval($_POST['access_level'])==0) {
		$player_path="$config[project_path]/admin/data/player";
		$player_files_path="$config[content_path_other]/player";
	} elseif (intval($_POST['access_level'])==2)
	{
		$player_path="$config[project_path]/admin/data/player/active";
		$player_files_path="$config[content_path_other]/player/active";
	} elseif (intval($_POST['access_level'])==3)
	{
		$player_path="$config[project_path]/admin/data/player/premium";
		$player_files_path="$config[content_path_other]/player/premium";
	}

	if (is_file("$player_path/config.dat") && !is_writable("$player_path/config.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',"$player_path/config.dat");
	}
	if ($_POST['is_embed']=='1')
	{
		if (is_file("$player_path/config.tpl") && !is_writable("$player_path/config.tpl"))
		{
			$errors[]=get_aa_error('filesystem_permission_write',"$player_path/config.tpl");
		}
		if (is_file("$player_path/error.tpl") && !is_writable("$player_path/error.tpl"))
		{
			$errors[]=get_aa_error('filesystem_permission_write',"$player_path/error.tpl");
		}
	}

	if (!is_array($errors))
	{
		if (!is_dir($player_files_path))
		{
			mkdir_recursive($player_files_path);
		}
		if (!is_writable($player_files_path))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$player_files_path");
		}

		if (!is_dir($player_path))
		{
			mkdir_recursive($player_path);
		}
		if (!is_writable($player_path))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$player_path");
		}
	}

	if (!is_array($errors))
	{
		$old_data=@unserialize(file_get_contents("$player_path/config.dat"));

		$player_data=@unserialize(file_get_contents("$player_path/config.dat"));

		if (intval($_POST['access_level'])<>0 && !isset($_POST['overwrite_settings']))
		{
			@unlink("$player_path/config.dat");
			@unlink("$player_files_path/$player_data[logo]");
			@unlink("$player_files_path/$player_data[pre_roll_file]");
			@unlink("$player_files_path/$player_data[post_roll_file]");
			@unlink("$player_files_path/$player_data[pause_file]");
			for ($i=1;$i<=4;$i++)
			{
				@unlink("$player_files_path/".$player_data["float{$i}_file"]);
			}
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=225, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], 'overwrite_settings', date("Y-m-d H:i:s"));
			return_ajax_success("$page_name?access_level=$_POST[access_level]");
		}
		if ($_POST['is_embed']=='1' && $_POST['embed_profile_id']!='' && isset($_POST['delete_profile']))
		{
			rmdir_recursive($player_path);
			rmdir_recursive($player_files_path);
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=226, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], 'delete_profile', date("Y-m-d H:i:s"));
			return_ajax_success("$page_name?page=embed");
		}

		if (is_file("$player_path/config.dat"))
		{
			if ($_POST['logo']=='' && $player_data['logo']<>'') {@unlink("$player_files_path/$player_data[logo]");}
			if ($_POST['pre_roll_file']=='' && $player_data['pre_roll_file']<>'') {@unlink("$player_files_path/$player_data[pre_roll_file]");}
			if ($_POST['post_roll_file']=='' && $player_data['post_roll_file']<>'') {@unlink("$player_files_path/$player_data[post_roll_file]");}
			if ($_POST['pause_file']=='' && $player_data['pause_file']<>'') {@unlink("$player_files_path/$player_data[pause_file]");}
			for ($i=1;$i<=4;$i++)
			{
				if ($_POST["float{$i}_file"]=='' && $player_data["float{$i}_file"]<>'') {@unlink("$player_files_path/".$player_data["float{$i}_file"]);}
			}
		}

		if ($_POST['logo']<>'') {transfer_uploaded_file('logo',"$player_files_path/$_POST[logo]");}
		if ($_POST['pre_roll_file']<>'') {transfer_uploaded_file('pre_roll_file',"$player_files_path/$_POST[pre_roll_file]");}
		if ($_POST['post_roll_file']<>'') {transfer_uploaded_file('post_roll_file',"$player_files_path/$_POST[post_roll_file]");}
		if ($_POST['pause_file']<>'') {transfer_uploaded_file('pause_file',"$player_files_path/$_POST[pause_file]");}
		for ($i=1;$i<=4;$i++)
		{
			if ($_POST["float{$i}_file"]<>'') {transfer_uploaded_file("float{$i}_file","$player_files_path/".$_POST["float{$i}_file"]);}
		}

		if ($_POST['is_embed']=='1')
		{
			if ($_POST['embed_profile_id']!='')
			{
				$player_data['embed_profile_id']=$_POST['embed_profile_id'];
				$player_data['embed_profile_name']=$_POST['embed_profile_name'];
				$player_data['embed_profile_domains']=$_POST['embed_profile_domains'];
			}
			$player_data['embed_cache_time']=intval($_POST['embed_cache_time']);
			$player_data['embed_size_option']=intval($_POST['embed_size_option']);
			$player_data['player_replacement_html']=$_POST['player_replacement_html'];
			$player_data['black_list_countries']=$_POST['black_list_countries'];
			$player_data['black_list_domains']=$_POST['black_list_domains'];
			$player_data['default_video']="";
		}
		$player_data['width']=intval($_POST['width']);
		$player_data['height']=intval($_POST['height']);
		$player_data['height_option']=intval($_POST['height_option']);
		$player_data['adjust_embed_codes']=intval($_POST['adjust_embed_codes']);
		$player_data['skin']=$_POST['skin'];
		$player_data['logo']=$_POST['logo'];
		$player_data['logo_source']=$_POST['logo_source'];
		$player_data['logo_text']=$_POST['logo_text'];
		$player_data['logo_text_source']=$_POST['logo_text_source'];
		$player_data['logo_anchor']=$_POST['logo_anchor'];
		$player_data['logo_position_x']=intval($_POST['logo_position_x']);
		$player_data['logo_position_y']=intval($_POST['logo_position_y']);
		$player_data['logo_url_source']=$_POST['logo_url_source'];
		$player_data['logo_url']=$_POST['logo_url'];
		$player_data['logo_hide']=intval($_POST['logo_hide']);
		$player_data['preload_metadata']=$_POST['preload_metadata'];
		$player_data['volume']=$_POST['volume'];
		$player_data['loop']=intval($_POST['loop']);
		$player_data['loop_duration']=intval($_POST['loop_duration']);
		$player_data['timeline_screenshots_size']=$_POST['timeline_screenshots_size'];
		$player_data['timeline_screenshots_cuepoints']=intval($_POST['timeline_screenshots_cuepoints']);
		$player_data['timeline_screenshots_preload']=intval($_POST['timeline_screenshots_preload']);
		$player_data['affiliate_param_name']=$_POST['affiliate_param_name'];
		$player_data['enable_adblock_protection']=intval($_POST['enable_adblock_protection']);
		$player_data['adblock_protection_html']=$_POST['adblock_protection_html'];
		$player_data['adblock_protection_html_after']=intval($_POST['adblock_protection_html_after']);
		$player_data['show_speed']=intval($_POST['show_speed']);
		$player_data['enable_stream']=intval($_POST['enable_stream']);
		$player_data['enable_autoplay']=intval($_POST['enable_autoplay']);
		$player_data['enable_related_videos']=intval($_POST['enable_related_videos']);
		$player_data['enable_related_videos_on_pause']=intval($_POST['enable_related_videos_on_pause']);
		$player_data['enable_float_replay']=intval($_POST['enable_float_replay']);
		$player_data['enable_urls_in_same_window']=intval($_POST['enable_urls_in_same_window']);
		$player_data['disable_embed_code']=intval($_POST['disable_embed_code']);
		$player_data['disable_preview_resize']=intval($_POST['disable_preview_resize']);
		$player_data['use_preview_source']=intval($_POST['use_preview_source']);
		$player_data['use_uploaded_poster']=intval($_POST['use_uploaded_poster']);
		$player_data['error_logging']=intval($_POST['error_logging']);

		$player_data['disable_selected_slot_restoring']=intval($_POST['disable_selected_slot_restoring']);
		$player_data['show_global_duration']=intval($_POST['show_global_duration']);
		$player_data['format_redirect_url_source']=$_POST['format_redirect_url_source'];
		$player_data['format_redirect_url']=$_POST['format_redirect_url'];
		$player_data['formats']=$formats;

		$player_data['controlbar']=$_POST['controlbar'];
		$player_data['controlbar_ad_text']=$_POST['controlbar_ad_text'];
		$player_data['controlbar_ad_text_source']=$_POST['controlbar_ad_text_source'];
		$player_data['controlbar_ad_url_source']=$_POST['controlbar_ad_url_source'];
		$player_data['controlbar_ad_url']=$_POST['controlbar_ad_url'];
		$player_data['controlbar_hide_style']=$_POST['controlbar_hide_style'];

		$player_data['enable_video_click']=$_POST['enable_video_click'];
		$player_data['video_click_url_source']=$_POST['video_click_url_source'];
		$player_data['video_click_url']=$_POST['video_click_url'];

		$player_data['enable_popunder']=$_POST['enable_popunder'];
		$player_data['popunder_url_source']=$_POST['popunder_url_source'];
		$player_data['popunder_url']=$_POST['popunder_url'];
		$player_data['popunder_duration']=$_POST['popunder_duration'];
		$player_data['popunder_autoplay_only']=intval($_POST['popunder_autoplay_only']);

		if ($_POST['popunder_start_option']==1)
		{
			$player_data['popunder_start_after']=intval($_POST['popunder_start_after']);
		} else
		{
			$player_data['popunder_start_after']='';
		}

		$player_data['enable_start_html']=$_POST['enable_start_html'];
		$player_data['start_html_source']=$_POST['start_html_source'];
		$player_data['start_html_code']=$_POST['start_html_code'];
		$player_data['start_html_bg']=$_POST['start_html_bg'];
		$player_data['start_html_adaptive']=intval($_POST['start_html_adaptive']);
		$player_data['start_html_adaptive_width']=min(100,intval($_POST['start_html_adaptive_width']));
		$player_data['start_html_adaptive_height']=min(100,intval($_POST['start_html_adaptive_height']));

		$player_data['enable_pre_roll']=$_POST['enable_pre_roll'];
		$player_data['enable_pre_roll_html']=$_POST['enable_pre_roll_html'];
		$player_data['enable_pre_roll_vast']=$_POST['enable_pre_roll_vast'];
		$player_data['pre_roll_file_source']=$_POST['pre_roll_file_source'];
		$player_data['pre_roll_file']=$_POST['pre_roll_file'];
		$player_data['pre_roll_url_source']=$_POST['pre_roll_url_source'];
		$player_data['pre_roll_url']=$_POST['pre_roll_url'];
		$player_data['pre_roll_html_source']=$_POST['pre_roll_html_source'];
		$player_data['pre_roll_html_code']=$_POST['pre_roll_html_code'];
		$player_data['pre_roll_html_bg']=$_POST['pre_roll_html_bg'];
		$player_data['pre_roll_html_adaptive']=intval($_POST['pre_roll_html_adaptive']);
		$player_data['pre_roll_html_adaptive_width']=min(100,intval($_POST['pre_roll_html_adaptive_width']));
		$player_data['pre_roll_html_adaptive_height']=min(100,intval($_POST['pre_roll_html_adaptive_height']));
		$player_data['pre_roll_duration']=$_POST['pre_roll_duration'];
		$player_data['pre_roll_duration_text']=$_POST['pre_roll_duration_text'];
		$player_data['pre_roll_vast_provider']=$_POST['pre_roll_vast_provider'];
		$player_data['pre_roll_vast_url']=$_POST['pre_roll_vast_url'];
		$player_data['pre_roll_vast_alt_url']=$_POST['pre_roll_vast_alt_url'];
		$player_data['pre_roll_vast_logo']=intval($_POST['pre_roll_vast_logo']);
		$player_data['pre_roll_vast_logo_click']=intval($_POST['pre_roll_vast_logo_click']);
		$player_data['pre_roll_vast_timeout']=$_POST['pre_roll_vast_timeout'];

		if ($_POST['pre_roll_start_option']==1)
		{
			$player_data['pre_roll_start_after']=intval($_POST['pre_roll_start_after']);
		} else
		{
			$player_data['pre_roll_start_after']='';
		}
		if ($_POST['pre_roll_replay_option']==1)
		{
			$player_data['pre_roll_replay_after']=intval($_POST['pre_roll_replay_after']);
		} else
		{
			$player_data['pre_roll_replay_after']='';
		}
		$player_data['pre_roll_replay_after_type']=intval($_POST['pre_roll_replay_after_type']);

		$player_data['enable_pre_roll_skip']=$_POST['enable_pre_roll_skip'];
		$player_data['pre_roll_skip_duration']=$_POST['pre_roll_skip_duration'];
		$player_data['pre_roll_skip_text1']=$_POST['pre_roll_skip_text1'];
		$player_data['pre_roll_skip_text2']=$_POST['pre_roll_skip_text2'];

		$player_data['post_roll_mode']=intval($_POST['post_roll_mode']);
		$player_data['enable_post_roll']=$_POST['enable_post_roll'];
		$player_data['enable_post_roll_html']=$_POST['enable_post_roll_html'];
		$player_data['enable_post_roll_vast']=$_POST['enable_post_roll_vast'];
		$player_data['post_roll_file_source']=$_POST['post_roll_file_source'];
		$player_data['post_roll_file']=$_POST['post_roll_file'];
		$player_data['post_roll_url_source']=$_POST['post_roll_url_source'];
		$player_data['post_roll_url']=$_POST['post_roll_url'];
		$player_data['post_roll_html_source']=$_POST['post_roll_html_source'];
		$player_data['post_roll_html_code']=$_POST['post_roll_html_code'];
		$player_data['post_roll_html_bg']=$_POST['post_roll_html_bg'];
		$player_data['post_roll_html_adaptive']=intval($_POST['post_roll_html_adaptive']);
		$player_data['post_roll_html_adaptive_width']=min(100,intval($_POST['post_roll_html_adaptive_width']));
		$player_data['post_roll_html_adaptive_height']=min(100,intval($_POST['post_roll_html_adaptive_height']));
		$player_data['post_roll_duration']=$_POST['post_roll_duration'];
		$player_data['post_roll_duration_text']=$_POST['post_roll_duration_text'];
		$player_data['post_roll_vast_provider']=$_POST['post_roll_vast_provider'];
		$player_data['post_roll_vast_url']=$_POST['post_roll_vast_url'];
		$player_data['post_roll_vast_alt_url']=$_POST['post_roll_vast_alt_url'];

		$player_data['enable_post_roll_skip']=$_POST['enable_post_roll_skip'];
		$player_data['post_roll_skip_duration']=$_POST['post_roll_skip_duration'];
		$player_data['post_roll_skip_text1']=$_POST['post_roll_skip_text1'];
		$player_data['post_roll_skip_text2']=$_POST['post_roll_skip_text2'];

		$player_data['enable_pause']=$_POST['enable_pause'];
		$player_data['enable_pause_html']=$_POST['enable_pause_html'];
		$player_data['pause_file_source']=$_POST['pause_file_source'];
		$player_data['pause_file']=$_POST['pause_file'];
		$player_data['pause_url_source']=$_POST['pause_url_source'];
		$player_data['pause_url']=$_POST['pause_url'];
		$player_data['pause_html_source']=$_POST['pause_html_source'];
		$player_data['pause_html_code']=$_POST['pause_html_code'];
		$player_data['pause_html_bg']=$_POST['pause_html_bg'];
		$player_data['pause_html_adaptive']=intval($_POST['pause_html_adaptive']);
		$player_data['pause_html_adaptive_width']=min(100,intval($_POST['pause_html_adaptive_width']));
		$player_data['pause_html_adaptive_height']=min(100,intval($_POST['pause_html_adaptive_height']));
		$player_data['pause_duration']=$_POST['pause_duration'];

		for ($i=1;$i<=4;$i++)
		{
			$player_data["enable_float$i"]=$_POST["enable_float$i"];
			$player_data["float{$i}_time"]=$_POST["float{$i}_time"];
			$player_data["float{$i}_location"]=$_POST["float{$i}_location"];
			$player_data["float{$i}_duration"]=$_POST["float{$i}_duration"];
			$player_data["float{$i}_size"]=$_POST["float{$i}_size"];
			$player_data["float{$i}_size_width"]=$_POST["float{$i}_size_width"];
			$player_data["float{$i}_size_height"]=$_POST["float{$i}_size_height"];
			$player_data["float{$i}_file_source"]=$_POST["float{$i}_file_source"];
			$player_data["float{$i}_file"]=$_POST["float{$i}_file"];
			$player_data["float{$i}_url_source"]=$_POST["float{$i}_url_source"];
			$player_data["float{$i}_url"]=$_POST["float{$i}_url"];
		}

		file_put_contents("$player_path/config.dat", serialize($player_data), LOCK_EX);
		file_put_contents("$player_path/version.dat", md5(serialize($player_data) . $vast_key_data['primary_vast_key']), LOCK_EX);

		$update_details='';
		foreach ($player_data as $var=>$val)
		{
			if ($old_data[$var] != $val)
			{
				$update_details .= "$var, ";
			}
		}

		if ($_POST['is_embed']=='1')
		{
			$old_data['embed_template'] = @file_get_contents("$player_path/config.tpl");
			$old_data['error_template'] = @file_get_contents("$player_path/error.tpl");

			file_put_contents("$player_path/config.tpl", $_POST['embed_template'], LOCK_EX);
			file_put_contents("$player_path/error.tpl", $_POST['error_template'], LOCK_EX);

			if ($old_data['embed_template'] != $_POST['embed_template'])
			{
				$update_details .= "embed_template, ";
			}
			if ($old_data['error_template'] != $_POST['error_template'])
			{
				$update_details .= "error_template, ";
			}
		}

		if (intval($_POST['error_logging']) == 0)
		{
			@unlink("$config[project_path]/admin/logs/log_player_errors.txt");
		}

		$error_player_logging_count = 0;
		$error_embed_logging_count = 0;
		$has_player_vast_enabled = false;
		$has_embed_vast_enabled = false;
		$player_files = get_player_data_files();
		foreach ($player_files as $player_file)
		{
			$player_data = @unserialize(file_get_contents($player_file['file']), ['allowed_classes' => false]);
			if (isset($player_data))
			{
				if ($player_data['error_logging'] == 1)
				{
					if ($player_file['is_embed'] == 1)
					{
						$error_embed_logging_count++;
					} else
					{
						$error_player_logging_count++;
					}
				}
				if ($player_data['enable_pre_roll_vast'] == 1 || $player_data['enable_post_roll_vast'] == 1)
				{
					if ($player_file['is_embed'] == 1)
					{
						$has_embed_vast_enabled = true;
					} else
					{
						$has_player_vast_enabled = true;
					}
				}
			}
		}
		add_admin_notification('settings.player.debug', $error_player_logging_count);
		add_admin_notification('settings.embed.debug', $error_embed_logging_count);

		$is_vast_key_expiring = false;
		$is_vast_key_expired = false;
		if ($vast_key_data['primary_vast_key'])
		{
			$vast_key_valid = intval(substr($vast_key_data['primary_vast_key'], 0, 10));
			if ($vast_key_valid > 0)
			{
				$vast_key_valid = intval(($vast_key_valid - time()) / 86400);
				if ($vast_key_valid > 0)
				{
					if ($vast_key_valid <= 3)
					{
						$is_vast_key_expiring = true;
					}
				} else
				{
					$is_vast_key_expired = true;
				}
			} else
			{
				$is_vast_key_expired = true;
			}
		}
		add_admin_notification('settings.player.vast_expiring', $has_player_vast_enabled && $is_vast_key_expiring ? 1 : 0);
		add_admin_notification('settings.embed.vast_expiring', $has_embed_vast_enabled && $is_vast_key_expiring ? 1 : 0);
		add_admin_notification('settings.player.vast_expired', $has_player_vast_enabled && $is_vast_key_expired ? 1 : 0);
		add_admin_notification('settings.embed.vast_expired', $has_embed_vast_enabled && $is_vast_key_expired ? 1 : 0);

		if ($_POST['enable_pre_roll_vast'] == 1 && in_array($_POST['pre_roll_vast_provider'], array(1, 2)))
		{
			$feature = '';
			if ($_POST['pre_roll_vast_provider'] == 1)
			{
				$feature = 'teasernet';
			} elseif ($_POST['pre_roll_vast_provider'] == 2)
			{
				$feature = 'adwise';
			}
			get_page("", "https://www.kernel-scripts.com/track_feature.php?feature=$feature&url=$config[project_url]&vast=" . urlencode($_POST['pre_roll_vast_url']), "", "", 1, 0, 5, "");
		}
		if ($_POST['enable_post_roll_vast'] == 1 && in_array($_POST['post_roll_vast_provider'], array(1, 2)))
		{
			$feature = '';
			if ($_POST['post_roll_vast_provider'] == 1)
			{
				$feature = 'teasernet';
			} elseif ($_POST['post_roll_vast_provider'] == 2)
			{
				$feature = 'adwise';
			}
			get_page("", "https://www.kernel-scripts.com/track_feature.php?feature=$feature&url=$config[project_url]&vast=" . urlencode($_POST['post_roll_vast_url']), "", "", 1, 0, 5, "");
		}

		if (strlen($update_details) > 0)
		{
			$update_details = substr($update_details, 0, -2);
		}
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=case when 1=? then 226 else 225 end, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], intval($_POST['is_embed']), $update_details, date("Y-m-d H:i:s"));

		$_SESSION['messages'][]=$lang['settings']['common_success_message_modified'];
		if ($_POST['is_embed']=='1')
		{
			if ($_POST['embed_profile_id']!='')
			{
				return_ajax_success("$page_name?page=embed&embed_profile_id=".$_POST['embed_profile_id']);
			} else {
				return_ajax_success("$page_name?page=embed");
			}
		} elseif (intval($_POST['access_level'])==0) {
			return_ajax_success("$page_name");
		} else {
			return_ajax_success("$page_name?access_level=$_POST[access_level]");
		}
	} else {
		return_ajax_errors($errors);
	}
}

$options = get_options();
for ($i=1;$i<=10;$i++)
{
	if ($options["CS_FIELD_{$i}_NAME"]=='') {$options["CS_FIELD_{$i}_NAME"]=$lang['settings']["custom_field_{$i}"];}
	if ($options["CS_FILE_FIELD_{$i}_NAME"]=='') {$options["CS_FILE_FIELD_{$i}_NAME"]=$lang['settings']["custom_file_field_{$i}"];}
}

$smarty=new mysmarty();
if (isset($_REQUEST['embed_profile_id']))
{
	$_REQUEST['page']='embed';
}
if ($_REQUEST['page']=='embed')
{
	$temp = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2) and access_level_id=0"));
	$formats = [];
	foreach ($temp as $k => $v)
	{
		if (in_array(end(explode(".", $v['postfix'])), explode(",", $config['player_allowed_ext'])))
		{
			$formats[$v['format_video_group_id']][] = $v;
		}
	}

	$file="$config[project_path]/admin/data/player/embed/config.dat";
	if ($_REQUEST['embed_profile_id']=='new')
	{
		$default_data=@unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/config.dat"));

		$player_data=array();
		$player_data['embed_template']=@file_get_contents("$config[project_path]/admin/data/player/embed/config.tpl");
		$player_data['error_template']=@file_get_contents("$config[project_path]/admin/data/player/embed/error.tpl");
		$player_data['embed_cache_time']=$default_data['embed_cache_time'];
		$player_data['embed_size_option']=$default_data['embed_size_option'];
		$player_data['width']=$default_data['width'];
		$player_data['height']=$default_data['height'];
		$player_data['height_option']=$default_data['height_option'];
		$player_data['skin']=$default_data['skin'];
		$player_data['preload_metadata']=$default_data['preload_metadata'];
		$player_data['volume']=$default_data['volume'];
		$player_data['loop']=$default_data['loop'];
		$player_data['loop_duration']=$default_data['loop_duration'];
		$player_data['show_speed']=$default_data['show_speed'];
		$player_data['enable_stream']=$default_data['enable_stream'];
		$player_data['enable_autoplay']=$default_data['enable_autoplay'];
		$player_data['enable_related_videos']=$default_data['enable_related_videos'];
		$player_data['enable_related_videos_on_pause']=$default_data['enable_related_videos_on_pause'];
		$player_data['enable_urls_in_same_window']=$default_data['enable_urls_in_same_window'];
		$player_data['disable_embed_code']=$default_data['disable_embed_code'];
		$player_data['disable_preview_resize']=$default_data['disable_preview_resize'];
		$player_data['use_preview_source']=$default_data['use_preview_source'];
		$player_data['use_uploaded_poster']=$default_data['use_uploaded_poster'];
		$player_data['error_logging']=$default_data['error_logging'];
		$player_data['format_redirect_url_source']=$default_data['format_redirect_url_source'];
		$player_data['format_redirect_url']=$default_data['format_redirect_url'];
		$player_data['controlbar']=$default_data['controlbar'];
		$player_data['controlbar_hide_style']=$default_data['controlbar_hide_style'];
		$player_data['timeline_screenshots_size']=$default_data['timeline_screenshots_size'];
		$player_data['timeline_screenshots_cuepoints']=intval($default_data['timeline_screenshots_cuepoints']);
		$player_data['timeline_screenshots_preload']=intval($default_data['timeline_screenshots_preload']);
		$player_data['affiliate_param_name']=$default_data['affiliate_param_name'];
		$player_data['enable_adblock_protection']=$default_data['enable_adblock_protection'];
		$player_data['adblock_protection_html']=$default_data['adblock_protection_html'];
		$player_data['adblock_protection_html_after']=$default_data['adblock_protection_html_after'];
	} elseif ($_REQUEST['embed_profile_id']!='')
	{
		$embed_dir=md5($_REQUEST['embed_profile_id']);
		if (is_dir("$config[project_path]/admin/data/player/embed/$embed_dir") && is_file("$config[project_path]/admin/data/player/embed/$embed_dir/config.dat"))
		{
			$player_data=@unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/$embed_dir/config.dat"));
			$player_data['embed_template']=@file_get_contents("$config[project_path]/admin/data/player/embed/$embed_dir/config.tpl");
			$player_data['error_template']=@file_get_contents("$config[project_path]/admin/data/player/embed/$embed_dir/error.tpl");
		} else {
			header('Location: player.php?page=embed&embed_profile_id=new');die;
		}
	} else {
		$player_data=@unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/config.dat"));
		$player_data['embed_template']=@file_get_contents("$config[project_path]/admin/data/player/embed/config.tpl");
		$player_data['error_template']=@file_get_contents("$config[project_path]/admin/data/player/embed/error.tpl");
	}

	$selected_formats = array();
	foreach ($list_formats_videos_groups as $format_group)
	{
		$selected_formats["group{$format_group['format_video_group_id']}_default"] = 1;
	}
	if (is_array($player_data['formats']))
	{
		foreach ($player_data['formats'] as $group_id => $group)
		{
			$default_format = 1;
			foreach ($group as $index => $slot)
			{
				$i = $index + 1;
				$selected_formats["group{$group_id}_slot{$i}"] = $slot['type'];
				$selected_formats["group{$group_id}_slot_title{$i}"] = $slot['title'];
				if ($slot['is_default'] == 1)
				{
					$default_format = $i;
				}
			}
			$selected_formats["group{$group_id}_default"] = $default_format;
		}
	}

	if ($player_data['skin']=='1')
	{
		$player_data['skin']='dark.css';
	} elseif ($player_data['skin']=='2')
	{
		$player_data['skin']='white.css';
	}

	$smarty->assign('player_data',$player_data);
	$smarty->assign('formats',$formats);
	$smarty->assign('formats_groups',$list_formats_videos_groups);
	$smarty->assign('selected_formats',$selected_formats);
	$smarty->assign('applied',array(1));
} else {
	$temp = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2)"));
	$formats = [];
	foreach ($temp as $k => $v)
	{
		if (in_array(end(explode(".", $v['postfix'])), explode(",", $config['player_allowed_ext'])))
		{
			$formats[$v['format_video_group_id']][] = $v;
		}
	}

	$applied=array();
	if (intval($_REQUEST['access_level'])==0)
	{
		$file="$config[project_path]/admin/data/player/config.dat";

		$applied[]=1;
		if ($config['installation_type']>=2)
		{
			if (!is_file("$config[project_path]/admin/data/player/active/config.dat"))
			{
				$applied[]=2;
			}
			if (!is_file("$config[project_path]/admin/data/player/premium/config.dat"))
			{
				$applied[]=3;
			}
		}
	} elseif (intval($_REQUEST['access_level'])==2)
	{
		$file="$config[project_path]/admin/data/player/active/config.dat";
		$applied[]=2;
	} elseif (intval($_REQUEST['access_level'])==3)
	{
		$file="$config[project_path]/admin/data/player/premium/config.dat";
		$applied[]=3;
	}
	if (is_file($file))
	{
		$player_data=@unserialize(file_get_contents($file));
	} else {
		$default_data=@unserialize(file_get_contents("$config[project_path]/admin/data/player/config.dat"));

		$player_data=array();
		$player_data['no_settings']=1;
		$player_data['width']=$default_data['width'];
		$player_data['height']=$default_data['height'];
		$player_data['height_option']=$default_data['height_option'];
		$player_data['adjust_embed_codes']=intval($default_data['adjust_embed_codes']);
		$player_data['skin']=$default_data['skin'];
		$player_data['preload_metadata']=$default_data['preload_metadata'];
		$player_data['volume']=$default_data['volume'];
		$player_data['loop']=$default_data['loop'];
		$player_data['loop_duration']=$default_data['loop_duration'];
		$player_data['timeline_screenshots_size']=$default_data['timeline_screenshots_size'];
		$player_data['timeline_screenshots_cuepoints']=intval($default_data['timeline_screenshots_cuepoints']);
		$player_data['timeline_screenshots_preload']=intval($default_data['timeline_screenshots_preload']);
		$player_data['show_speed']=$default_data['show_speed'];
		$player_data['enable_stream']=$default_data['enable_stream'];
		$player_data['enable_autoplay']=$default_data['enable_autoplay'];
		$player_data['enable_related_videos']=$default_data['enable_related_videos'];
		$player_data['enable_related_videos_on_pause']=$default_data['enable_related_videos_on_pause'];
		$player_data['enable_urls_in_same_window']=$default_data['enable_urls_in_same_window'];
		$player_data['disable_embed_code']=$default_data['disable_embed_code'];
		$player_data['disable_preview_resize']=$default_data['disable_preview_resize'];
		$player_data['use_preview_source']=$default_data['use_preview_source'];
		$player_data['use_uploaded_poster']=$default_data['use_uploaded_poster'];
		$player_data['error_logging']=$default_data['error_logging'];
		$player_data['format_redirect_url_source']=$default_data['format_redirect_url_source'];
		$player_data['format_redirect_url']=$default_data['format_redirect_url'];
		$player_data['disable_selected_slot_restoring']=$default_data['disable_selected_slot_restoring'];
		$player_data['show_global_duration']=$default_data['show_global_duration'];
		$player_data['controlbar']=$default_data['controlbar'];
		$player_data['controlbar_hide_style']=$default_data['controlbar_hide_style'];
	}
	$player_data['access_level']=intval($_REQUEST['access_level']);

	$selected_formats = array();
	foreach ($list_formats_videos_groups as $format_group)
	{
		$selected_formats["group{$format_group['format_video_group_id']}_default"] = 1;
	}
	if (is_array($player_data['formats']))
	{
		foreach ($player_data['formats'] as $group_id => $group)
		{
			$default_format = 1;
			foreach ($group as $index => $slot)
			{
				$i = $index + 1;
				$selected_formats["group{$group_id}_slot{$i}"] = $slot['type'];
				$selected_formats["group{$group_id}_slot_title{$i}"] = $slot['title'];
				if ($slot['is_default'] == 1)
				{
					$default_format = $i;
				}
			}
			$selected_formats["group{$group_id}_default"] = $default_format;
		}
	}

	if ($player_data['skin']=='1')
	{
		$player_data['skin']='dark.css';
	} elseif ($player_data['skin']=='2')
	{
		$player_data['skin']='white.css';
	}

	$smarty->assign('player_data',$player_data);
	$smarty->assign('formats',$formats);
	$smarty->assign('formats_groups',$list_formats_videos_groups);
	$smarty->assign('selected_formats',$selected_formats);
	if (array_cnt($applied))
	{
		$smarty->assign('applied',$applied);
	}
}

if (!in_array($player_data['skin'], $list_skins) && $player_data['skin'] != 'disable')
{
	$_POST['errors'][] = get_aa_error('required_field', $lang['settings']['player_field_skin']);
}
if ($player_data['timeline_screenshots_size'])
{
	$valid_format=false;
	foreach ($list_formats_timeline_screenshots as $format)
	{
		if ($format['size']==$player_data['timeline_screenshots_size'])
		{
			$valid_format=true;
			break;
		}
	}
	if (!$valid_format)
	{
		$_POST['errors'][]=get_aa_error('required_field',$lang['settings']['player_field_timeline_screenshots']);
	}
}
if (strpos($player_data['video_click_url_source'], 'vast_profile_') !== false)
{
	if (!isset($vast_profiles[substr($player_data['video_click_url_source'], 13)]))
	{
		$_POST['errors'][] = get_aa_error('player_invalid_vast_profile', $lang['settings']['player_divider_click_settings'] . " - " . $lang['settings']['player_field_video_click_url']);
	}
}
if (strpos($player_data['popunder_url_source'], 'vast_profile_') !== false)
{
	if (!isset($vast_profiles[substr($player_data['popunder_url_source'], 13)]))
	{
		$_POST['errors'][] = get_aa_error('player_invalid_vast_profile', $lang['settings']['player_divider_click_settings'] . " - " . $lang['settings']['player_field_popunder_url']);
	}
}
if (is_array($player_data['formats']))
{
	foreach ($player_data['formats'] as $group_id => $group)
	{
		$group_title = '';
		foreach ($list_formats_videos_groups as $format_group)
		{
			if ($format_group['format_video_group_id'] == $group_id)
			{
				$group_title = ' - ' . $format_group['title'];
				break;
			}
		}

		$has_missing_formats = false;
		foreach ($group as $item)
		{
			if ($item['type'] != 'redirect')
			{
				$has_format = false;
				foreach ($formats[$group_id] as $format)
				{
					if ($format['postfix'] == $item['type'])
					{
						$has_format = true;
						break;
					}
				}
				if (!$has_format)
				{
					$has_missing_formats = true;
				}
			}
		}
		if ($has_missing_formats)
		{
			$_POST['errors'][] = get_aa_error('required_field', $lang['settings']['player_divider_formats_settings'] . $group_title . ' - ' . $lang['settings']['player_formats_col_format']);
		}
	}
}
if (strpos($player_data['start_html_source'],'spot_')!==false)
{
	if (!isset($ad_spots[substr($player_data['start_html_source'],5)]))
	{
		$_POST['errors'][]=get_aa_error('player_invalid_spot',$lang['settings']['player_divider_start_settings']." - ".$lang['settings']['player_field_start_html_code']);
	}
}
if (strpos($player_data['pre_roll_html_source'],'spot_')!==false)
{
	if (!isset($ad_spots[substr($player_data['pre_roll_html_source'],5)]))
	{
		$_POST['errors'][]=get_aa_error('player_invalid_spot',$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['player_field_pre_roll_html_code']);
	}
}
if (strpos($player_data['post_roll_html_source'],'spot_')!==false)
{
	if (!isset($ad_spots[substr($player_data['post_roll_html_source'],5)]))
	{
		$_POST['errors'][]=get_aa_error('player_invalid_spot',$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['player_field_post_roll_html_code']);
	}
}
if (strpos($player_data['pause_html_source'],'spot_')!==false)
{
	if (!isset($ad_spots[substr($player_data['pause_html_source'],5)]))
	{
		$_POST['errors'][]=get_aa_error('player_invalid_spot',$lang['settings']['player_divider_pause_settings']." - ".$lang['settings']['player_field_pause_html_code']);
	}
}
if (strpos($player_data['pre_roll_vast_provider'],'vast_profile_')!==false)
{
	if (!isset($vast_profiles[substr($player_data['pre_roll_vast_provider'],13)]))
	{
		$_POST['errors'][]=get_aa_error('player_invalid_vast_profile',$lang['settings']['player_divider_pre_roll_settings']." - ".$lang['settings']['common_field_advertising_vast_provider']);
	}
}
if (strpos($player_data['post_roll_vast_provider'],'vast_profile_')!==false)
{
	if (!isset($vast_profiles[substr($player_data['post_roll_vast_provider'],13)]))
	{
		$_POST['errors'][]=get_aa_error('player_invalid_vast_profile',$lang['settings']['player_divider_post_roll_settings']." - ".$lang['settings']['common_field_advertising_vast_provider']);
	}
}

if ($vast_key_data['primary_vast_key'])
{
	$smarty->assign('primary_vast_key', $vast_key_data['primary_vast_key']);
	if (array_cnt($vast_key_data['aliases']) > 0)
	{
		$smarty->assign('vast_aliases', $vast_key_data['aliases']);
	}
	$vast_key_valid = intval(substr($vast_key_data['primary_vast_key'], 0, 10));
	if ($vast_key_valid > 0)
	{
		$vast_key_valid = intval(($vast_key_valid - time()) / 86400);
		if ($vast_key_valid > 0)
		{
			$smarty->assign('primary_vast_key_valid', $vast_key_valid);
		} else
		{
			$smarty->assign('primary_vast_key_invalid', 1);
		}
	} else
	{
		$smarty->assign('primary_vast_key_invalid', 1);
	}
}

if ($_REQUEST['page']=='embed')
{
	if (isset($_SESSION['admin_notifications']['list']['settings.embed.debug']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.embed.debug']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['settings.embed.vast_expiring']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.embed.vast_expiring']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['settings.embed.vast_expired']['title']))
	{
		$_POST['errors'][] = $_SESSION['admin_notifications']['list']['settings.embed.vast_expired']['title'];
	}
} else
{
	if (isset($_SESSION['admin_notifications']['list']['settings.player.debug']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.player.debug']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['settings.player.vast_expiring']['title']))
	{
		$list_messages[] = $lang['notifications']['warning_prefix'] . $_SESSION['admin_notifications']['list']['settings.player.vast_expiring']['title'];
	}
	if (isset($_SESSION['admin_notifications']['list']['settings.player.vast_expired']['title']))
	{
		$_POST['errors'][] = $_SESSION['admin_notifications']['list']['settings.player.vast_expired']['title'];
	}
}

$smarty->assign('data',$data);
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('options',$options);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('list_skins',$list_skins);
$smarty->assign('list_spots',$ad_spots);
$smarty->assign('vast_profiles',$vast_profiles);
$smarty->assign('list_embed_profiles',$list_embed_profiles);
$smarty->assign('list_formats_timeline_screenshots',$list_formats_timeline_screenshots);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

if ($_REQUEST['page']=='embed')
{
	$smarty->assign('page_title',$lang['settings']['player_embed_header']);
} else {
	$smarty->assign('page_title',$lang['settings']['player_header']);
}

$smarty->display("layout.tpl");
