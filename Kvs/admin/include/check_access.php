<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
$_REQUEST=array_merge($_GET,$_POST);
foreach ($_POST as $name => $value)
{
	if (is_array($value) && array_cnt($value) > 0)
	{
		$all_sub_arrays = true;
		$max_size = 0;
		foreach ($value as $inner_name => $inner_value)
		{
			if (!is_array($inner_value)) {
				$all_sub_arrays = false;
				break;
			}
			$max_size = max($max_size, array_cnt($inner_value));
		}
		if ($all_sub_arrays)
		{
			$_POST[$name] = [];
			for ($i = 0; $i < $max_size; $i++)
			{
				$record = [];
				foreach ($value as $inner_name => $inner_value)
				{
					$record[$inner_name] = $i < array_cnt($inner_value) ? $inner_value[$i] : '';
				}
				$_POST[$name][] = $record;
			}
		}
	}
}

if (isset($_SERVER['HTTP_ORIGIN']))
{
	$origin_domain = str_replace('dev.', '', str_replace('www.', '', parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST)));
	$license_domain = str_replace('dev.', '', str_replace('www.', '', $config['project_licence_domain']));
	if ($origin_domain != $license_domain)
	{
		http_response_code(403);
		header('X-KVS-Origin-Check: failed');
		die;
	}
}

header('Cache-Control: no-store, no-cache, must-revalidate');

if (intval($config['admin_session_duration_minutes'])>0 && session_status() != PHP_SESSION_ACTIVE)
{
	ini_set("session.gc_maxlifetime",intval($config['admin_session_duration_minutes'])*60);
}

start_session();
if ($_SESSION['userdata']['login'] == '' || ($_SESSION['userdata']['is_ip_protection_disabled'] != 1 && $_SESSION['userdata']['ip'] != $_SERVER['REMOTE_ADDR']))
{
	if (substr(dirname($_SERVER['SCRIPT_FILENAME']), -6) == '/async' || strtolower($_SERVER['REQUEST_METHOD']) == 'post')
	{
		http_response_code(403);
	} else
	{
		$_SESSION['admin_panel_referer'] = $_SERVER['REQUEST_URI'];
		header("Location: index.php");
	}
	die;
}



ini_set('max_execution_time','9999');

$memory_limit = trim(ini_get('memory_limit'));
$_SESSION['original_memory_limit'] = $memory_limit;
if ($memory_limit && $memory_limit != '-1')
{
	$last = strtolower(substr($memory_limit, -1));
	$memory_limit = intval($memory_limit);
	switch ($last)
	{
		/** @noinspection PhpMissingBreakStatementInspection */
		case 'g':
			$memory_limit *= 1024;
		/** @noinspection PhpMissingBreakStatementInspection */
		case 'm':
			$memory_limit *= 1024;
		case 'k':
			$memory_limit *= 1024;
	}
	if ($memory_limit < 510 * 1000 * 1000)
	{
		ini_set('memory_limit', '512M');
	}
}

$_SESSION['admin_page_generation_time_start'] = microtime(true);
$_SESSION['admin_page_generation_memory_start'] = memory_get_peak_usage();

if ($_SESSION['userdata']['login_gate']<>$config['project_url'])
{
	$config['sql_safe_mode'] = 1;
	$result=sql_pr("select * from $config[tables_prefix_multi]admin_users where login=? and md5(pass)=?",$_SESSION['userdata']['login'],nvl($_SESSION['userdata']['pass']));
	unset($config['sql_safe_mode']);
	if (mr2rows($result)>0)
	{
		$old_session_id=$_SESSION['userdata']['session_id'];

		$admin_data=mr2array_single($result);
		$_SESSION['userdata']=$admin_data;
		$_SESSION['userdata']['ip']=$_SERVER['REMOTE_ADDR'];
		$_SESSION['userdata']['session_id']=$old_session_id;
		$_SESSION['userdata']['last_login']=mr2array_single(sql_pr("select login_date, ip, country_code, duration from $config[tables_prefix_multi]log_logins where user_id=? order by login_date desc limit 1",$_SESSION['userdata']['user_id']));
		$_SESSION['userdata']['pass']=md5($_SESSION['userdata']['pass']);
		$_SESSION['userdata']['login_gate']=$config['project_url'];
		if ($_SESSION['userdata']['last_login']['ip']<>'') {$_SESSION['userdata']['last_login']['ip']=int2ip($_SESSION['userdata']['last_login']['ip']);}

		$_SESSION['save'] = @unserialize($_SESSION['userdata']['preference']) ?: [];
		unset($_SESSION['userdata']['preference']);

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix_multi]log_logins where session_id=? and (UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(last_request_date))<86400",$_SESSION['userdata']['session_id'],date("Y-m-d H:i:s")))==0)
		{
			sql_pr("insert into $config[tables_prefix_multi]log_logins set session_id=?, user_id=?, login_date=?, last_request_date=?, duration=0, ip=?, full_ip=?, country_code=?",$_SESSION['userdata']['session_id'],$_SESSION['userdata']['user_id'],date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),ip2int($_SERVER['REMOTE_ADDR']), trim($_SERVER['REMOTE_ADDR']), trim($_SERVER['GEOIP_COUNTRY_CODE']));
		}
		sql_pr("update $config[tables_prefix_multi]admin_users set last_ip=? where user_id=?",ip2int($_SERVER['REMOTE_ADDR']),$_SESSION['userdata']['user_id']);
	} else {
		if (substr(dirname($_SERVER['SCRIPT_FILENAME']), -6) == '/async' || strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			http_response_code(403);
		} else
		{
			$_SESSION['admin_panel_referer'] = $_SERVER['REQUEST_URI'];
			header("Location: index.php?force_relogin=true");
		}
		die;
	}
}

if (intval($config['admin_session_duration_minutes'])>0)
{
	if (intval($_SESSION['last_request_date'])>0)
	{
		if (time()-intval($_SESSION['last_request_date'])>intval($config['admin_session_duration_minutes'])*60)
		{
			destroy_session();
			start_session();
			if (substr(dirname($_SERVER['SCRIPT_FILENAME']), -6) == '/async' || strtolower($_SERVER['REQUEST_METHOD']) == 'post')
			{
				http_response_code(403);
			} else
			{
				$_SESSION['admin_panel_referer'] = $_SERVER['REQUEST_URI'];
				header("Location: index.php");
			}
			die;
		}
	}
	$_SESSION['last_request_date']=time();
}

KvsContext::init(KvsContext::CONTEXT_TYPE_ADMIN, $_SESSION['userdata']['user_id']);

$last_version_check=@file_get_contents("$config[project_path]/admin/data/engine/checks/last_version.dat");
if ($last_version_check<>$config['project_version'])
{
	$compiled_templates=get_contents_from_dir("$config[project_path]/admin/smarty/template-c",1);
	foreach ($compiled_templates as $compiled_template)
	{
		@unlink("$config[project_path]/admin/smarty/template-c/$compiled_template");
	}
	if (!is_dir("$config[project_path]/admin/data/engine/checks")){mkdir("$config[project_path]/admin/data/engine/checks",0777);chmod("$config[project_path]/admin/data/engine/checks",0777);}
	file_put_contents("$config[project_path]/admin/data/engine/checks/last_version.dat",$config['project_version'],LOCK_EX);
}

$duration=mr2number(sql_pr("select UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(last_request_date) from $config[tables_prefix_multi]log_logins where session_id=?",date("Y-m-d H:i:s"),$_SESSION['userdata']['session_id']));
if ($duration>600) {$duration=600;}
sql_pr("update $config[tables_prefix_multi]log_logins set last_request_date=?, duration=duration+? where session_id=?",date("Y-m-d H:i:s"),$duration,$_SESSION['userdata']['session_id']);

if (is_array($_SESSION['save']) && array_cnt($_SESSION['save']) > 0)
{
	if ($_SESSION['saved_serialized'] != serialize($_SESSION['save']))
	{
		$_SESSION['saved_serialized'] = serialize($_SESSION['save']);
		sql_pr("update $config[tables_prefix_multi]admin_users set preference=? where user_id=?", $_SESSION['saved_serialized'], $_SESSION['userdata']['user_id']);
	}
}

if (!is_file($config['project_path']."/admin/styles/".$_SESSION['userdata']['skin'].".css"))
{
	$_SESSION['userdata']['skin']="default";
}
if (!is_file($config['project_path']."/admin/langs/".$_SESSION['userdata']['lang'].".php"))
{
	$_SESSION['userdata']['lang']="english";
}
if (!is_file("$config[project_path]/admin/langs/english.php"))  {echo "Project has run into inconsistent state, as one of the project resources is missing (language pack)";die;}
if (!is_file("$config[project_path]/admin/styles/default.css")) {echo "Project has run into inconsistent state, as one of the project resources is missing (styles pack)";die;}

if ($_SESSION['save']['options']['skin_enable_night_mode'] == 1 && KvsUtilities::is_time_in_interval(time(), trim($_SESSION['save']['options']['skin_night_mode_from']), trim($_SESSION['save']['options']['skin_night_mode_to'])))
{
	$_SESSION['userdata']['night_mode'] = 1;
} else
{
	$_SESSION['userdata']['night_mode'] = 0;
}

unset($lang);
require_once("$config[project_path]/admin/langs/english.php");
if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php"))
{
	require_once("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php");
}
if ($config['dvds_mode']=='dvds')
{
	require_once("$config[project_path]/admin/langs/english/lang_dvds_replace.php");
	if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/lang_dvds_replace.php"))
	{
		require_once("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/lang_dvds_replace.php");
	}
} elseif ($config['dvds_mode']=='series')
{
	require_once("$config[project_path]/admin/langs/english/lang_series_replace.php");
	if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/lang_series_replace.php"))
	{
		require_once("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/lang_series_replace.php");
	}
}
if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php"))
{
	require_once("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php");
}

if (isset($lang['system']['set_locale']))
{
	setlocale(LC_TIME,$lang['system']['set_locale']);
}

require_once "$config[project_path]/admin/include/functions_admin.php";

$options = get_options(['MAIN_SERVER_MIN_FREE_SPACE_MB']);

$admin_data = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_users where user_id=?", $_SESSION['userdata']['user_id']));
if (intval($admin_data['user_id']) != intval($_SESSION['userdata']['user_id']))
{
	exit_to_permission_error();
	die;
} else
{
	$_SESSION['userdata']['group_id'] = $admin_data['group_id'];
	$_SESSION['userdata']['is_debug_enabled'] = $admin_data['is_debug_enabled'];
	$_SESSION['userdata']['is_access_to_own_content'] = $admin_data['is_access_to_own_content'];
	$_SESSION['userdata']['is_access_to_disabled_content'] = $admin_data['is_access_to_disabled_content'];
	$_SESSION['userdata']['is_access_to_content_flagged_with'] = $admin_data['is_access_to_content_flagged_with'];
	$_SESSION['userdata']['content_delete_daily_limit'] = $admin_data['content_delete_daily_limit'];
}

if ($_SESSION['userdata']['is_debug_enabled'] == 1)
{
	require_once("$config[project_path]/admin/include/functions_base.php");
	if ($_SERVER['REQUEST_METHOD'] == 'GET')
	{
		debug_admin("GET $_SERVER[REQUEST_URI]", $_SESSION['userdata']['user_id']);
	} elseif ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$post_data = '';
		foreach ($_POST as $k => $v)
		{
			if (is_array($v))
			{
				$v = '[' . implode(', ', $v) . ']';
			}
			$post_data .= "$k = $v\n";
		}
		$post_data = trim($post_data);
		debug_admin("POST $_SERVER[REQUEST_URI]\n$post_data", $_SESSION['userdata']['user_id']);
	}
}

$_SESSION['server_time']=time();
$_SESSION['server_la']=get_LA();
$_SESSION['server_processes']=mr2number(sql("select count(*) from $config[tables_prefix]background_tasks where status_id<>2"));
$_SESSION['server_processes_error']=mr2number(sql("select count(*) from $config[tables_prefix]background_tasks where status_id=2"));
$_SESSION['server_free_space']=sizeToHumanString(@disk_free_space($config['project_path']),1);
$_SESSION['server_free_space_pc']=@disk_free_space($config['project_path'])/@disk_total_space($config['project_path'])*100;
$_SESSION['server_free_space_alert'] = (@disk_free_space($config['project_path']) < $options['MAIN_SERVER_MIN_FREE_SPACE_MB'] * 1024 * 1024) ? 1 : 0;
if (is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
{
	$_SESSION['server_processes_paused']=1;
} else {
	$_SESSION['server_processes_paused']=0;
}
if (strpos($config['project_url'], $_SERVER['HTTP_HOST']) === false)
{
	$_SESSION['admin_panel_project_url'] = "$config[project_url]?" . session_name() . '=' . session_id();
} else
{
	unset($_SESSION['admin_panel_project_url']);
}

$page_name=end(explode("/",$_SERVER['SCRIPT_FILENAME']));

$list_messages = null;
if (is_array($_SESSION['messages']))
{
	$list_messages = $_SESSION['messages'];
	unset($_SESSION['messages']);
}

$config['image_allowed_ext'].=",".strtoupper($config['image_allowed_ext']);
$config['other_allowed_ext'].=",".strtoupper($config['other_allowed_ext']);
$config['player_allowed_ext'].=",".strtoupper($config['player_allowed_ext']);

if ($_SESSION['userdata']['is_superadmin']==0)
{
	if ($_SESSION['userdata']['group_id'] > 0)
	{
		$group_data = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_users_groups where group_id=?", $_SESSION['userdata']['group_id']));
		if (intval($group_data['group_id']) == 0)
		{
			exit_to_permission_error();
			die;
		}
		if ($group_data['is_access_to_own_content'] > 0)
		{
			$_SESSION['userdata']['is_access_to_own_content'] = $group_data['is_access_to_own_content'];
		}
		if ($group_data['is_access_to_disabled_content'] > 0)
		{
			$_SESSION['userdata']['is_access_to_disabled_content'] = $group_data['is_access_to_disabled_content'];
		}
		if ($group_data['is_access_to_content_flagged_with'] > 0 && $_SESSION['userdata']['is_access_to_content_flagged_with'] == '')
		{
			$_SESSION['userdata']['is_access_to_content_flagged_with'] = $group_data['is_access_to_content_flagged_with'];
		}

		$_SESSION['permissions'] = mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions where permission_id in (select permission_id from $config[tables_prefix_multi]admin_users_groups_permissions where group_id=?)", $_SESSION['userdata']['group_id']));
	} else
	{
		$_SESSION['permissions'] = [];
	}
	$_SESSION['permissions']=array_merge($_SESSION['permissions'],mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions where permission_id in (select permission_id from $config[tables_prefix_multi]admin_users_permissions where user_id=?)",$_SESSION['userdata']['user_id'])));
	$_SESSION['permissions']=array_unique($_SESSION['permissions']);

	//check permissions
	settype($_SESSION['permissions'],"array");

	if (in_array('videos|edit_all',$_SESSION['permissions']))
	{
		$_SESSION['permissions'][] = 'videos|edit_title';
		$_SESSION['permissions'][] = 'videos|edit_dir';
		$_SESSION['permissions'][] = 'videos|edit_description';
		$_SESSION['permissions'][] = 'videos|edit_post_date';
		$_SESSION['permissions'][] = 'videos|edit_user';
		$_SESSION['permissions'][] = 'videos|edit_status';
		$_SESSION['permissions'][] = 'videos|edit_type';
		$_SESSION['permissions'][] = 'videos|edit_access_level';
		$_SESSION['permissions'][] = 'videos|edit_tokens';
		$_SESSION['permissions'][] = 'videos|edit_release_year';
		$_SESSION['permissions'][] = 'videos|edit_embed';
		$_SESSION['permissions'][] = 'videos|edit_url';
		$_SESSION['permissions'][] = 'videos|edit_pseudo_url';
		$_SESSION['permissions'][] = 'videos|edit_duration';
		$_SESSION['permissions'][] = 'videos|edit_dvd';
		$_SESSION['permissions'][] = 'videos|edit_content_source';
		$_SESSION['permissions'][] = 'videos|edit_categories';
		$_SESSION['permissions'][] = 'videos|edit_tags';
		$_SESSION['permissions'][] = 'videos|edit_models';
		$_SESSION['permissions'][] = 'videos|edit_flags';
		$_SESSION['permissions'][] = 'videos|edit_custom';
		$_SESSION['permissions'][] = 'videos|edit_admin_flag';
		$_SESSION['permissions'][] = 'videos|edit_is_locked';
		$_SESSION['permissions'][] = 'videos|edit_storage';
		$_SESSION['permissions'][] = 'videos|edit_connected_data';
		$_SESSION['permissions'][] = 'videos|edit_video_files';
	}

	if (in_array('albums|edit_all',$_SESSION['permissions']))
	{
		$_SESSION['permissions'][] = 'albums|edit_title';
		$_SESSION['permissions'][] = 'albums|edit_dir';
		$_SESSION['permissions'][] = 'albums|edit_description';
		$_SESSION['permissions'][] = 'albums|edit_post_date';
		$_SESSION['permissions'][] = 'albums|edit_user';
		$_SESSION['permissions'][] = 'albums|edit_status';
		$_SESSION['permissions'][] = 'albums|edit_type';
		$_SESSION['permissions'][] = 'albums|edit_access_level';
		$_SESSION['permissions'][] = 'albums|edit_tokens';
		$_SESSION['permissions'][] = 'albums|edit_content_source';
		$_SESSION['permissions'][] = 'albums|edit_categories';
		$_SESSION['permissions'][] = 'albums|edit_tags';
		$_SESSION['permissions'][] = 'albums|edit_models';
		$_SESSION['permissions'][] = 'albums|edit_flags';
		$_SESSION['permissions'][] = 'albums|edit_custom';
		$_SESSION['permissions'][] = 'albums|edit_admin_flag';
		$_SESSION['permissions'][] = 'albums|edit_is_locked';
		$_SESSION['permissions'][] = 'albums|edit_storage';
	}

	if (in_array('posts|edit_all',$_SESSION['permissions']))
	{
		$_SESSION['permissions'][] = 'posts|edit_title';
		$_SESSION['permissions'][] = 'posts|edit_dir';
		$_SESSION['permissions'][] = 'posts|edit_description';
		$_SESSION['permissions'][] = 'posts|edit_content';
		$_SESSION['permissions'][] = 'posts|edit_post_date';
		$_SESSION['permissions'][] = 'posts|edit_user';
		$_SESSION['permissions'][] = 'posts|edit_status';
		$_SESSION['permissions'][] = 'posts|edit_type';
		$_SESSION['permissions'][] = 'posts|edit_categories';
		$_SESSION['permissions'][] = 'posts|edit_tags';
		$_SESSION['permissions'][] = 'posts|edit_models';
		$_SESSION['permissions'][] = 'posts|edit_flags';
		$_SESSION['permissions'][] = 'posts|edit_custom';
		$_SESSION['permissions'][] = 'posts|edit_is_locked';
	}

	if ($page_name=='admin_users.php' && $_SESSION['userdata']['is_superadmin']==0) {exit_to_permission_error();die;}
	if ($page_name=='admin_users_groups.php' && $_SESSION['userdata']['is_superadmin']==0) {exit_to_permission_error();die;}

	if ($page_name=='options.php')
	{
		if ($_REQUEST['page']=='general_settings' && !in_array('system|system_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['action']=='change_complete' && !in_array('system|system_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['page']=='website_settings' && !in_array('system|website_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['action']=='change_website_settings_complete' && !in_array('system|website_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['page']=='antispam_settings' && !in_array('system|antispam_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['action']=='change_antispam_settings_complete' && !in_array('system|antispam_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['page']=='memberzone_settings' && !in_array('system|memberzone_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['action']=='change_memberzone_settings_complete' && !in_array('system|memberzone_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['page']=='stats_settings' && !in_array('system|stats_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['action']=='change_stats_settings_complete' && !in_array('system|stats_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['page']=='customization' && !in_array('system|customization',$_SESSION['permissions'])) {exit_to_permission_error();die;}
		if ($_REQUEST['action']=='change_customization_complete' && !in_array('system|customization',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	}
	if ($page_name=='log_system.php' && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='log_logins.php' && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='log_audit.php' && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='log_bill.php' && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='log_feeds.php' && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='log_imports.php' && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='installation.php' && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='file_changes.php' && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='background_tasks.php' && !in_array('system|background_tasks',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='log_background_tasks.php' && !in_array('system|background_tasks',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='translations.php' && !in_array('localization|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='translations_summary.php' && !in_array('localization|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='formats_videos_basic.php' && !in_array('system|formats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='formats_videos.php' && !in_array('system|formats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='formats_screenshots.php' && !in_array('system|formats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='formats_albums.php' && !in_array('system|formats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='servers.php' && !in_array('system|servers',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='servers_test.php' && !in_array('system|servers',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='servers_conversion.php' && !in_array('system|servers',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='servers_conversion_basic.php' && !in_array('system|servers',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='player.php' && !in_array('system|player_settings',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='vast_profiles.php' && !in_array('system|vast_profiles',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='languages.php' && !in_array('system|localization',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='videos.php' && !in_array('videos|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('videos|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && in_array($_REQUEST['action'],array("mark_deleted","mark_deleted_complete","change_deleted","change_deleted_complete")) && !in_array('videos|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="restart" && !in_array('system|background_tasks',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="delete" && !in_array('videos|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="soft_delete" && !in_array('videos|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="activate" && !in_array('videos|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('videos|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="mark_reviewed" && !in_array('videos|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="delete_and_activate" && !in_array('videos|edit_status',$_SESSION['permissions']) && !in_array('videos|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="activate_and_delete" && !in_array('videos|edit_status',$_SESSION['permissions']) && !in_array('videos|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="restart" && !in_array('system|background_tasks',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="inc_priority" && !in_array('system|background_tasks',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='videos_select.php' && !in_array('videos|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos_select.php' && in_array($_REQUEST['operation'],array("mark_deleted","delete")) && !in_array('videos|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos_select.php' && in_array($_REQUEST['operation'],array("mass_edit")) && !in_array('videos|mass_edit',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='videos_mass_edit.php' && !in_array('videos|mass_edit',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos_feeds_import.php' && !in_array('videos|feeds_import',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos_feeds_export.php' && !in_array('videos|feeds_export',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='videos_screenshots.php' && !in_array('videos|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos_screenshots.php' && in_array($_REQUEST['action'],array("upload_screenshots","change_screenshots")) && !in_array('videos|manage_screenshots',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos_screenshots_grabbing.php' && !in_array('videos|manage_screenshots',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='videos_export.php' && !in_array('videos|export',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='videos_import.php' && !in_array('videos|import',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='albums.php' && !in_array('albums|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('albums|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && in_array($_REQUEST['action'],array("upload_images","process_images")) && !in_array('albums|manage_images',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && in_array($_REQUEST['action'],array("mark_deleted","mark_deleted_complete","change_deleted","change_deleted_complete")) && !in_array('albums|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="restart" && !in_array('system|background_tasks',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="delete" && !in_array('albums|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="soft_delete" && !in_array('albums|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="activate" && !in_array('albums|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('albums|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="mark_reviewed" && !in_array('albums|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="delete_and_activate" && !in_array('albums|edit_status',$_SESSION['permissions']) && !in_array('albums|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="activate_and_delete" && !in_array('albums|edit_status',$_SESSION['permissions']) && !in_array('albums|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="restart" && !in_array('system|background_tasks',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="inc_priority" && !in_array('system|background_tasks',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='albums_select.php' && !in_array('albums|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums_select.php' && in_array($_REQUEST['operation'],array("mark_deleted","delete")) && !in_array('albums|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums_select.php' && in_array($_REQUEST['operation'],array("mass_edit")) && !in_array('albums|mass_edit',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='albums_mass_edit.php' && !in_array('albums|mass_edit',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums_export.php' && !in_array('albums|export',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='albums_import.php' && !in_array('albums|import',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='posts_types.php' && !in_array('posts_types|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts_types.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('posts_types|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts_types.php' && $_REQUEST['action']=="change_complete" && !in_array('posts_types|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts_types.php' && $_REQUEST['batch_action']=="delete" && !in_array('posts_types|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts_types.php' && $_REQUEST['batch_action']=="delete_with_content" && !(in_array('posts_types|delete',$_SESSION['permissions']) && in_array('posts|delete',$_SESSION['permissions']))) {exit_to_permission_error();die;}

	if ($page_name=='posts.php' && !in_array('posts|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('posts|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts.php' && $_REQUEST['batch_action']=="delete" && !in_array('posts|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts.php' && $_REQUEST['batch_action']=="activate" && !in_array('posts|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('posts|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts.php' && $_REQUEST['batch_action']=="mark_reviewed" && !in_array('posts|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='posts_for_types.php' && !in_array('posts|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts_for_types.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('posts|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts_for_types.php' && $_REQUEST['batch_action']=="delete" && !in_array('posts|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts_for_types.php' && $_REQUEST['batch_action']=="activate" && !in_array('posts|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='posts_for_types.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('posts|edit_status',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='categories.php' && !in_array('categories|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='categories.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('categories|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='categories.php' && $_REQUEST['action']=="change_complete" && !in_array('categories|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='categories.php' && $_REQUEST['batch_action']=="delete" && !in_array('categories|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='categories_groups.php' && !in_array('category_groups|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='categories_groups.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('category_groups|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='categories_groups.php' && $_REQUEST['action']=="change_complete" && !in_array('category_groups|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='categories_groups.php' && $_REQUEST['batch_action']=="delete" && !in_array('category_groups|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='models.php' && !in_array('models|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='models.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('models|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='models.php' && $_REQUEST['action']=="change_complete" && !in_array('models|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='models.php' && $_REQUEST['batch_action']=="delete" && !in_array('models|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='models_groups.php' && !in_array('models_groups|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='models_groups.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('models_groups|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='models_groups.php' && $_REQUEST['action']=="change_complete" && !in_array('models_groups|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='models_groups.php' && $_REQUEST['batch_action']=="delete" && !in_array('models_groups|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='tags.php' && !in_array('tags|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='tags.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('tags|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='tags.php' && $_REQUEST['action']=="change_complete" && !in_array('tags|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='tags.php' && $_REQUEST['batch_action']=="delete" && !in_array('tags|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='tags.php' && isset($_REQUEST['save_rename']) && !in_array('tags|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='flags.php' && !in_array('flags|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='flags.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('flags|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='flags.php' && $_REQUEST['action']=="change_complete" && !in_array('flags|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='flags.php' && $_REQUEST['batch_action']=="delete" && !in_array('flags|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='dvds.php' && !in_array('dvds|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='dvds.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('dvds|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='dvds.php' && $_REQUEST['action']=="change_complete" && !in_array('dvds|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='dvds.php' && in_array($_REQUEST['batch_action'],array("delete","delete_with_videos")) && !in_array('dvds|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='dvds.php' && in_array($_REQUEST['batch_action'],array("delete_with_videos")) && !in_array('videos|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='dvds.php' && $_REQUEST['batch_action']=="mark_reviewed" && !in_array('dvds|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='dvds_groups.php' && !in_array('dvds_groups|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='dvds_groups.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('dvds_groups|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='dvds_groups.php' && $_REQUEST['action']=="change_complete" && !in_array('dvds_groups|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='dvds_groups.php' && $_REQUEST['batch_action']=="delete" && !in_array('dvds_groups|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='content_sources.php' && !in_array('content_sources|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='content_sources.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('content_sources|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='content_sources.php' && $_REQUEST['action']=="change_complete" && !in_array('content_sources|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='content_sources.php' && $_REQUEST['batch_action']=="delete" && !in_array('content_sources|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='content_sources_groups.php' && !in_array('content_sources_groups|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='content_sources_groups.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('content_sources_groups|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='content_sources_groups.php' && $_REQUEST['action']=="change_complete" && !in_array('content_sources_groups|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='content_sources_groups.php' && $_REQUEST['batch_action']=="delete" && !in_array('content_sources_groups|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='users.php' && !in_array('users|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('users|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users.php' && $_REQUEST['action']=="change_complete" && !in_array('users|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users.php' && in_array($_REQUEST['batch_action'],array("delete","delete_with_content")) && !in_array('users|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users.php' && $_REQUEST['batch_action']=="unban" && !in_array('users|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users.php' && $_REQUEST['batch_action']=="confirm" && !in_array('users|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users.php' && $_REQUEST['batch_action']=="activate" && !in_array('users|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('users|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='emailing.php' && !in_array('users|emailings',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='feedbacks.php' && !in_array('feedbacks|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='feedbacks.php' && $_REQUEST['action']=="change_complete" && !in_array('feedbacks|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='feedbacks.php' && $_REQUEST['batch_action']=="close" && !in_array('feedbacks|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='feedbacks.php' && $_REQUEST['batch_action']=="delete" && !in_array('feedbacks|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='flags_messages.php' && !in_array('feedbacks|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='flags_messages.php' && $_REQUEST['batch_action']=="delete" && !in_array('feedbacks|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='card_bill_configurations.php' && !in_array('billing|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='card_bill_configurations.php' && in_array($_REQUEST['action'],array("change_complete","change_provider_complete")) && !in_array('billing|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='card_bill_configurations.php' && $_REQUEST['batch_action']=="delete" && !in_array('billing|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='bill_transactions.php' && !in_array('billing|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='bill_transactions.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('billing|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='bill_transactions.php' && $_REQUEST['action']=="change_complete" && !in_array('billing|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='bill_transactions.php' && $_REQUEST['batch_action']=="cancel" && !in_array('billing|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='payouts.php' && !in_array('payouts|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='payouts.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('payouts|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='payouts.php' && $_REQUEST['action']=="change_complete" && !in_array('payouts|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='users_blogs.php' && !in_array('users|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['action']=="change_complete" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['batch_action']=="approve" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['batch_action']=="delete" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['batch_action']=="approve_and_delete" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['batch_action']=="delete_and_approve" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='comments.php' && !in_array('users|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='comments.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete","change_complete")) && !in_array('users|manage_comments',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='comments.php' && $_REQUEST['batch_action']=="approve" && !in_array('users|manage_comments',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='comments.php' && $_REQUEST['batch_action']=="delete" && !in_array('users|manage_comments',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='comments.php' && $_REQUEST['batch_action']=="delete_new" && !in_array('users|manage_comments',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='comments.php' && $_REQUEST['batch_action']=="approve_and_delete" && !in_array('users|manage_comments',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='comments.php' && $_REQUEST['batch_action']=="delete_and_approve" && !in_array('users|manage_comments',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='messages.php' && !in_array('messages|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='messages.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('messages|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='messages.php' && $_REQUEST['action']=="change_complete" && !in_array('messages|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='messages.php' && $_REQUEST['batch_action']=="delete" && !in_array('messages|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='playlists.php' && !in_array('playlists|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='playlists.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('playlists|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='playlists.php' && $_REQUEST['action']=="change_complete" && !in_array('playlists|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='playlists.php' && $_REQUEST['batch_action']=="delete" && !in_array('playlists|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='playlists.php' && $_REQUEST['batch_action']=="activate" && !in_array('playlists|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='playlists.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('playlists|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='stats_country.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_out.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_player.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_in.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_referer.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_embed.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_videos.php' && !in_array('stats|view_content_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_albums.php' && !in_array('stats|view_content_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_transactions.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_users.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_users_initial_transactions.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_users_logins.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_users_content.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_users_purchases.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_users_sellings.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_users_donations.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_users_awards.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_referers_list.php' && !in_array('stats|manage_referers',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_cleanup.php' && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='stats_search.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_search.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('stats|manage_search_queries',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_search.php' && $_REQUEST['batch_action']=="delete" && !in_array('stats|manage_search_queries',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_search.php' && $_REQUEST['batch_action']=="activate" && !in_array('stats|manage_search_queries',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='stats_search.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('stats|manage_search_queries',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_theme.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_theme.php' && $_REQUEST['action']=="change_complete" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_theme_install.php' && !in_array('website_ui|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_pages_history.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_pages.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('website_ui|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("change_complete","change_block_complete")) && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && $_REQUEST['action']=="duplicate" && !in_array('website_ui|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && $_REQUEST['action']=="save_caching" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && $_REQUEST['batch_action']=="delete" && !in_array('website_ui|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("restore_pages")) && !(in_array('website_ui|add',$_SESSION['permissions']) || in_array('website_ui|delete',$_SESSION['permissions']))) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("restore_blocks")) && !(in_array('website_ui|edit_all',$_SESSION['permissions']) || in_array('website_ui|delete',$_SESSION['permissions']))) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("reset_mem_cache","reset_file_cache","reset_perf_stats")) && !in_array('system|administration',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && $_REQUEST['batch_action']=="wipeout_page" && !in_array('website_ui|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && $_REQUEST['batch_action']=="wipeout_block" && !in_array('website_ui|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages.php' && $_REQUEST['batch_action']=="restore_page" && !in_array('website_ui|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_pages_lang_files.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_lang_files.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('website_ui|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_lang_files.php' && $_REQUEST['action']=="change_complete" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_lang_files.php' && $_REQUEST['batch_action']=="delete" && !in_array('website_ui|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_pages_lang_texts.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_lang_texts.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('website_ui|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_lang_texts.php' && $_REQUEST['action']=="change_complete" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_lang_texts.php' && $_REQUEST['batch_action']=="delete" && !in_array('website_ui|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_pages_components.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_components.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('website_ui|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_components.php' && in_array($_REQUEST['action'],array("change_complete","quick_save")) && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_components.php' && $_REQUEST['action']=="duplicate" && !in_array('website_ui|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_components.php' && $_REQUEST['batch_action']=="delete" && !in_array('website_ui|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_pages_global.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_global.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('website_ui|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_global.php' && $_REQUEST['batch_action']=="delete" && !in_array('website_ui|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_global.php' && $_REQUEST['batch_action']=="restore_block" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_pages_global.php' && $_REQUEST['batch_action']=="wipeout_block" && !in_array('website_ui|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_spots.php' && !in_array('advertising|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_spots.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete","add_new_spot","add_new_spot_complete")) && !in_array('advertising|add',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_spots.php' && in_array($_REQUEST['action'],array("change_complete","change_spot_complete")) && !in_array('advertising|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_spots.php' && $_REQUEST['batch_action']=="delete" && !in_array('advertising|delete',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_spots.php' && $_REQUEST['batch_action']=="activate" && !in_array('advertising|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='project_spots.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('advertising|edit_all',$_SESSION['permissions'])) {exit_to_permission_error();die;}

	if ($page_name=='project_blocks.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
	if ($page_name=='templates_search.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {exit_to_permission_error();die;}
} else {
	$_SESSION['permissions']=mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions"));
}

$notificatons = [];

// php basic features critical errors
if (!function_exists('exec'))
{
	$notificatons[] = ['notification_id' => 'administration.installation.php_exec', 'objects' => 1];
} elseif (intval(KvsUtilities::exec_command("$config[php_path]", ['-r' => "echo (int)version_compare(PHP_VERSION, '7.1.0', '>');"], true, false)) == 0)
{
	$notificatons[] = ['notification_id' => 'administration.installation.console_php_version', 'objects' => 1];
}

// cron processing critical errors
$cron_process_info = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_processes where pid='main'"));
$cron_process_info['status_data'] = @unserialize($cron_process_info['status_data']) ?: [];
$cron_process_info['last_exec_date'] = strtotime($cron_process_info['last_exec_date']);
if ($cron_process_info['status_data']['error_type'] == 1)
{
	$notificatons[] = ['notification_id' => 'administration.installation.cron_directory', 'objects' => 1];
} elseif ($cron_process_info['status_data']['error_type'] == 2)
{
	$notificatons[] = ['notification_id' => 'administration.installation.cron_duplicate', 'objects' => 1];
} elseif (time() - $cron_process_info['last_exec_date'] > 900)
{
	$notificatons[] = ['notification_id' => 'administration.installation.cron_execution', 'objects' => 1];
}

// admin default password and password reset script
if ($_SESSION['userdata']['pass'] == md5(md5('123')) || $_SESSION['userdata']['pass'] == md5(md5('pass:' . md5('123'))) || $_SESSION['userdata']['pass'] == md5(generate_password_hash(md5('123'))))
{
	$notificatons[] = ['notification_id' => 'settings.personal.default_password', 'objects' => 1];
}
if ($_SESSION['userdata']['is_superadmin'] > 0 && is_file("$config[project_path]/reset_admin_password.php"))
{
	$notificatons[] = ['notification_id' => 'settings.personal.password_reset_script', 'objects' => 1];
}

// background tasks pause
if (is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
{
	$notificatons[] = ['notification_id' => 'settings.general.background_tasks_paused', 'objects' => 1];
}

// theme not installed
if (!is_dir("$config[project_path]/admin/data/config"))
{
	$notificatons[] = ['notification_id' => 'theme.install.needed', 'objects' => 1];
}

// preparing notifications for GUI
$notificatons = array_merge($notificatons, mr2array(sql_pr("select * from $config[tables_prefix_multi]admin_notifications")));
foreach ($notificatons as $k => $notification)
{
	$notification_sub_ids = KvsUtilities::str_to_array($notification['notification_id'], '.');
	if (array_cnt($notification_sub_ids) >= 2 && $notification_sub_ids[0] == 'plugins')
	{
		if (is_file("$config[project_path]/admin/plugins/$notification_sub_ids[1]/$notification_sub_ids[1].php"))
		{
			require_once "$config[project_path]/admin/plugins/$notification_sub_ids[1]/$notification_sub_ids[1].php";
			$init_function = "$notification_sub_ids[1]Init";
			if (function_exists($init_function))
			{
				$init_function();
			}
		}
	}

	$notification_type = KvsAdminNotificationEnum::get_by_uid($notification['notification_id']);
	if (!$notification_type)
	{
		unset($notificatons[$k]);
		continue;
	}
	$permission = $notification_type->get_permission();
	if ($permission && !in_array($permission, $_SESSION['permissions']) && $_SESSION['userdata']['is_superadmin'] == 0)
	{
		unset($notificatons[$k]);
		continue;
	}

	$notificatons[$k]['admin_url'] = $notification_type->get_admin_url();
	$notificatons[$k]['severity'] = $notification_type->get_severity();
	$notificatons[$k]['title'] = $notification_type->get_title(@json_decode($notification['details'], true) ?: []);
}

$_SESSION['admin_notifications'] = [];
$_SESSION['admin_notifications']['badges'] = [];
$_SESSION['admin_notifications']['list'] = [];
foreach ($notificatons as $notification)
{
	$_SESSION['admin_notifications']['list'][$notification['notification_id']] = $notification;
	if (in_array($notification['notification_id'], $_SESSION['save']['options']['mute_notifications'] ?? []))
	{
		continue;
	}
	$notification_sub_ids = KvsUtilities::str_to_array($notification['notification_id'], '.');
	if (array_cnt($notification_sub_ids) >= 2 && $notification['severity'] != 'hidden')
	{
		if ($notification['title'])
		{
			$_SESSION['admin_notifications']['badges'][$notification_sub_ids[0]]['title'] .= $notification['title'] . " ($notification[objects])\n";
		}
		$_SESSION['admin_notifications']['badges'][$notification_sub_ids[0]]['count'] = min($_SESSION['admin_notifications']['badges'][$notification_sub_ids[0]]['count'] + $notification['objects'], 99);
		if (array_cnt($notification_sub_ids) >= 3)
		{
			if ($notification['title'])
			{
				$_SESSION['admin_notifications']['badges'][$notification_sub_ids[0] . '_' . $notification_sub_ids[1]]['title'] .= $notification['title'] . " ($notification[objects])\n";
			}
			$_SESSION['admin_notifications']['badges'][$notification_sub_ids[0] . '_' . $notification_sub_ids[1]]['count'] = min($_SESSION['admin_notifications']['badges'][$notification_sub_ids[0] . '_' . $notification_sub_ids[1]]['count'] + $notification['objects'], 99);
		}
	}
}