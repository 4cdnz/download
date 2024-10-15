<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
$_REQUEST = array_merge($_GET, $_POST);

if (isset($_SERVER['KVS_DEV_PATH']) && is_dir($_SERVER['KVS_DEV_PATH']))
{
	require_once "$_SERVER[KVS_DEV_PATH]/admin/include/setup.php";
} else
{
	$include_path = '';
	$included_pages = get_included_files();
	foreach ($included_pages as $page)
	{
		if (strpos($page, 'process_page.php') !== false)
		{
			$include_path = str_replace(['include/process_page.php', 'include\process_page.php'], '', $page);
			break;
		}
	}
	require_once "$include_path/include/setup.php";
}

if (!is_file("$config[project_path]/admin/include/setup.php"))
{
	die('[FATAL]: project_path directory is not specified correctly in /admin/include/setup.php');
}
if ($config['www_redirect'] == 'true')
{
	$project_url_domain = parse_url($config['project_url'], PHP_URL_HOST);
	if (is_string($project_url_domain))
	{
		if ((strpos($project_url_domain, 'www.') !== false && $_SERVER['HTTP_HOST'] == str_replace('www.', '', $project_url_domain)) ||
				(strpos($project_url_domain, 'www.') === false && strpos($_SERVER['HTTP_HOST'], 'www.') !== false))
		{
			if (strpos($config['project_url'], '/', 10) === false)
			{
				header("Location: $config[project_url]$_SERVER[REQUEST_URI]", true, 301);
			} else
			{
				$project_url = substr($config['project_url'], 0, strpos($config['project_url'], '/', 10));
				header("Location: $project_url$_SERVER[REQUEST_URI]", true, 301);
			}
			die;
		}
	}
}

require_once "$config[project_path]/admin/include/functions_base.php";

$website_ui_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));

if (intval($website_ui_data['ALLOW_IFRAMES']) == 0)
{
	header('X-Frame-Options: SAMEORIGIN');
}

if (function_exists('sys_getloadavg'))
{
	$la = sys_getloadavg();
} else
{
	$la = [0];
}
$la = floatval($la[0]);

$overload_max_la_pages = intval($website_ui_data['OVERLOAD_MAX_LA_PAGES']);
if ($overload_max_la_pages == 0)
{
	$overload_max_la_pages = intval($config['overload_max_la_pages']) ?: 50;
}
if ($la > $overload_max_la_pages)
{
	write_overload_stats(1);

	http_response_code(503);
	if (is_file("$config[project_path]/overload.html"))
	{
		die(file_get_contents("$config[project_path]/overload.html"));
	}

	die('Sorry, the website is temporary unavailable. Please come back later!');
}

if ($_REQUEST['action'] == 'js_stats')
{
	$stats_params = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
	if (intval($stats_params['collect_traffic_stats']) == 1)
	{
		write_stats(1);

		$stats_referer_host = '';
		if ($_SERVER['HTTP_REFERER'] != '')
		{
			$stats_referer_host = trim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST));
		}
		if ($stats_referer_host == '' || KvsUtilities::str_ends_with($stats_referer_host, $config['project_licence_domain']))
		{
			$device_type = 0;
			if (intval($stats_params['collect_traffic_stats_devices']) == 1)
			{
				$device_type = get_device_type();
			}

			if (intval($_REQUEST['video_id']) > 0)
			{
				file_put_contents("$config[project_path]/admin/data/stats/videos_id.dat", intval($_REQUEST['video_id']) . "||0||1||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||" . date("Y-m-d H:i:s") . "||$_SERVER[REMOTE_ADDR]||$device_type\r\n", LOCK_EX | FILE_APPEND);
			}
			if (intval($_REQUEST['album_id']) > 0)
			{
				file_put_contents("$config[project_path]/admin/data/stats/albums_id.dat", intval($_REQUEST['album_id']) . "||0||1||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||" . date("Y-m-d H:i:s") . "||$_SERVER[REMOTE_ADDR]||$device_type\r\n", LOCK_EX | FILE_APPEND);
			}
		}
	}

	header("Content-type: image/gif");
	die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
}

$plugin_extensions = [];
$plugin_extensions_files = get_contents_from_dir("$config[project_path]/admin/data/plugins/engine", 1);
foreach ($plugin_extensions_files as $plugin_extension_file)
{
	if (substr($plugin_extension_file, -4) == '.dat')
	{
		$plugin_extension = substr($plugin_extension_file, 0, -4);
		if (is_file("$config[project_path]/admin/plugins/$plugin_extension/$plugin_extension.php"))
		{
			require_once "$config[project_path]/admin/plugins/$plugin_extension/$plugin_extension.php";
			$plugin_extensions[] = $plugin_extension;
		}
	}
}

start_session();
if (!$page_id)
{
	$page_id = preg_replace("|\..{1,4}$|is", "", end(explode("/", $_SERVER['SCRIPT_FILENAME'])));
}
if (intval($_SESSION['user_id'] ?? 0) > 0)
{
	try
	{
		KvsContext::init(KvsContext::CONTEXT_TYPE_PUBLIC, intval($_SESSION['user_id']));
	} catch (Throwable $e)
	{
		// such user doesn't exist
	}
}



if ($_REQUEST['mode']=='async')
{
	foreach ($plugin_extensions as $plugin_extension)
	{
		$plugin_function = "{$plugin_extension}PreAsyncRequest";
		if (function_exists($plugin_function))
		{
			$plugin_function();
		}
	}

	if ($_REQUEST['action']=='show_security_code' || $_REQUEST['function']=='show_security_code')
	{
		$t1=mt_rand(1,9);
		$t2=mt_rand(0,9);
		$t3=mt_rand(0,9);
		$t4=mt_rand(0,9);
		$t5=mt_rand(0,9);
		$text=$t1.$t2.$t3.$t4.$t5;
		$_SESSION['security_code']=$text;
		if ($_REQUEST['captcha_id']!='')
		{
			$_SESSION['security_code_'.$_REQUEST['captcha_id']]=$text;
		}

		$a1=mt_rand(-15,15);$b1=mt_rand(0,255);
		$a2=mt_rand(-15,15);$b2=mt_rand(0,255);
		$a3=mt_rand(-15,15);$b3=mt_rand(0,255);
		$a4=mt_rand(-15,15);$b4=mt_rand(0,255);
		$a5=mt_rand(-15,15);$b5=mt_rand(0,255);

		$font  = "$config[project_path]/admin/data/system/verdanaz.ttf";
		$fname = "$config[project_path]/admin/data/system/security_code.jpg";
		$im = imagecreatefromjpeg($fname);

		$white = imagecolorallocate($im, $b1, $b1, $b1);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a1, 33, 51, $black, $font, $t1);
		imagettftext($im, 33, $a1, 32, 50, $white, $font, $t1);

		$white = imagecolorallocate($im, $b2, $b2, $b2);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a2, 53, 51, $black, $font, $t2);
		imagettftext($im, 33, $a2, 52, 50, $white, $font, $t2);

		$white = imagecolorallocate($im, $b3, $b3, $b3);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a3, 73, 51, $black, $font, $t3);
		imagettftext($im, 33, $a3, 72, 50, $white, $font, $t3);

		$white = imagecolorallocate($im, $b4, $b4, $b4);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a4, 93, 51, $black, $font, $t4);
		imagettftext($im, 33, $a4, 92, 50, $white, $font, $t4);

		$white = imagecolorallocate($im, $b5, $b5, $b5);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a5, 113, 51, $black, $font, $t5);
		imagettftext($im, 33, $a5, 112, 50, $white, $font, $t5);

		$gif_file=mt_rand(0,9999999999);
		header("Content-Type: image/gif");
		imagegif($im,"$config[temporary_path]/$gif_file.gif");
		$gif_size=filesize("$config[temporary_path]/$gif_file.gif");
		if ($gif_size>0)
		{
			header("Content-Length: $gif_size");
			unlink("$config[temporary_path]/$gif_file.gif");
		}
		imagegif($im);
		imagedestroy($im);
		die;
	} elseif ($_REQUEST['action']=='check_security_code' || $_REQUEST['function']=='check_security_code')
	{
		async_set_request_content_type();
		if (strlen($_REQUEST['code'])>0 && $_REQUEST['code']==$_SESSION['security_code'])
		{
			if ($_REQUEST['format']=='json') {echo 'true';} else {echo "<success/>";}
		} else {
			if ($_REQUEST['format']=='json') {echo 'false';} else {echo "<error type=\"invalid_code\"/>";}
		}
		die;
	} elseif ($_REQUEST['action']=='js_online_status' || $_REQUEST['function']=='js_online_status')
	{
		if ($_SESSION['user_id']>0)
		{
			if ($website_ui_data['ENABLE_USER_ONLINE_STATUS_REFRESH']==1)
			{
				if (time()-$_SESSION['last_time_user_online_status_refreshed']>($website_ui_data['USER_ONLINE_STATUS_REFRESH_INTERVAL']-1)*60)
				{
					sql_pr("update $config[tables_prefix]users set last_online_date=? where user_id=?",date("Y-m-d H:i:s"),$_SESSION['user_id']);
					$_SESSION['last_time_user_online_status_refreshed']=time();
				}
			}
		}
		header("Content-type: image/gif");
		die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
	} elseif ($_REQUEST['action']=='js_user_status' || $_REQUEST['function']=='js_user_status')
	{
		async_set_request_content_type();
		if ($_SESSION['user_id']>0)
		{
			if ($_REQUEST['format']=='json')
			{
				echo json_encode(array('id'=>$_SESSION['user_id'],'status'=>$_SESSION['status_id'],'display_name'=>$_SESSION['display_name']));
			} else {
				$display_name=$_SESSION['display_name'];
				$display_name=str_replace("&","&amp;",$display_name);
				$display_name=str_replace(">","&gt;",$display_name);
				$display_name=str_replace("<","&lt;",$display_name);
				echo "<member id=\"$_SESSION[user_id]\" status=\"$_SESSION[status_id]\" display_name=\"$display_name\"/>";
			}
		} else {
			if ($_REQUEST['format']=='json')
			{
				echo json_encode(array('id'=>0));
			} else {
				echo "<guest/>";
			}
		}
		die;
	} elseif (($_REQUEST['action']=='get_block' || $_REQUEST['function']=='get_block') && isset($_REQUEST['block_id']))
	{
		include_once("$config[project_path]/admin/include/pre_initialize_page_code.php");

		$stats_params=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
		$runtime_params=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
		$request_uri=clean_request_uri();

		if (is_file("$config[project_path]/admin/data/plugins/recaptcha/enabled.dat") && is_file("$config[project_path]/admin/data/plugins/recaptcha/data.dat"))
		{
			$recaptcha_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/recaptcha/data.dat"));
		}

		if ($_SESSION['user_id']>0)
		{
			// sync user status
			$result=mr2array_single(sql_pr("select status_id, tokens_available, is_trusted from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
			$_SESSION['status_id']=intval($result['status_id']);
			if (intval($_SESSION['status_id'])==0)
			{
				require_once("$config[project_path]/logout.php");
				die;
			}

			$_SESSION['tokens_available']=intval($result['tokens_available']);
			$_SESSION['is_trusted']=intval($result['is_trusted']);
			$_SESSION['content_purchased']=mr2array(sql_pr("select distinct video_id, album_id, profile_id, dvd_id from $config[tables_prefix]users_purchases where user_id=? and expiry_date>?",$_SESSION['user_id'],date("Y-m-d H:i:s")));
			$_SESSION['content_purchased_amount']=array_cnt($_SESSION['content_purchased']);
		}

		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		$show_admin_debug = false;
		$show_admin_debug_block_data = [];
		$force_admin_no_cache_blocks = false;
		if ($_SESSION['userdata']['user_id'] > 0)
		{
			$force_admin_no_cache_blocks = true;
			if (@intval($_SESSION['save']['options']['enable_site_caching']) == 1 && @intval($_SESSION['save']['options']['disable_toolbar']) == 0)
			{
				$force_admin_no_cache_blocks = false;
			}

			if ($_REQUEST['debug'] == 'true')
			{
				$show_admin_debug = true;
				$force_admin_no_cache_blocks = true;
			}
		}

		if ($_REQUEST['global']=='true')
		{
			if (is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
			{
				$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
				$block_list=explode("|AND|",trim($temp[2]));

				foreach ($block_list as $block)
				{
					$block_id=substr($block,0,strpos($block,"[SEP]"));
					$block_name=substr($block,strpos($block,"[SEP]")+5);
					$block=str_replace("[SEP]","_",$block);

					if ($_REQUEST['block_id']!=$block) {continue;}
					if (!is_file("$config[project_path]/blocks/$block_id/$block_id.php")) {die;}
					if (!is_file("$config[project_path]/admin/data/config/\$global/$block.dat")) {die;}

					require_once("$config[project_path]/admin/include/setup_smarty_site.php");
					include_once("$config[project_path]/admin/include/list_countries.php");

					$args=array('global_id'=>$block);
					$block_content=insert_getGlobal($args);

					if ($show_admin_debug)
					{
						require_once "$config[project_path]/admin/website_ui_debug.php";
						die;
					}

					echo replace_runtime_params($block_content);

					foreach ($plugin_extensions as $plugin_extension)
					{
						$plugin_function = "{$plugin_extension}PostAsyncRequest";
						if (function_exists($plugin_function))
						{
							$plugin_function();
						}
					}
					die;
				}
			}
		} else {
			if (is_file("$config[project_path]/admin/data/config/$page_id/config.dat"))
			{
				$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/config.dat"));
				check_page_access(intval($temp[4]), intval($temp[5]), trim($temp[6]));
				$block_list=explode("|AND|",trim($temp[2]));

				$storage = [];
				foreach ($block_list as $block)
				{
					$block_id=substr($block,0,strpos($block,"[SEP]"));
					$block_name=substr($block,strpos($block,"[SEP]")+5);
					$block=str_replace("[SEP]","_",$block);

					include_once("$config[project_path]/blocks/$block_id/$block_id.php");
					$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/$block.dat"));
					$config_params=array();
					if (trim($temp[1])<>'')
					{
						$temp_params=explode("&",$temp[1]);
						foreach ($temp_params as $temp_param)
						{
							$temp_param=explode("=",$temp_param,2);
							$config_params[trim($temp_param[0])]=trim($temp_param[1]);
						}
					}
					$hash_function="{$block_id}GetHash";
					$block_hash=$hash_function($config_params);

					if (trim($temp[4])!='')
					{
						$block_dynamic_params=explode(',',$temp[4]);
						foreach ($block_dynamic_params as $block_dynamic_param)
						{
							if (trim($block_dynamic_param)!='' && trim($_REQUEST[trim($block_dynamic_param)])!='')
							{
								$block_hash='dyn:'.trim($_REQUEST[trim($block_dynamic_param)]).'|'.$block_hash;
							}
						}
					}

					$block_hash="$config[project_url]|$page_id|$block|$block_hash";
					if ($config['cache_control_user_status_in_cache']=='true')
					{
						$block_hash=intval($_SESSION['status_id'])."|$block_hash";
					}
					if ($config['project_url_scheme']=="https")
					{
						$block_hash="https|$block_hash";
					}
					if ($config['device']<>"")
					{
						$block_hash="$config[device]|$block_hash";
					}
					if ($config['locale']<>'')
					{
						$block_hash="$config[locale]|$block_hash";
					} elseif ($config['theme_locale']<>'')
					{
						$block_hash="$config[theme_locale]|$block_hash";
					}
					if ($config['relative_post_dates']=="true")
					{
						$relative_post_date=0;
						if ($_SESSION['user_id']>0 && $_SESSION['added_date']<>'')
						{
							$registration_date=strtotime($_SESSION['added_date']);
							$relative_post_date=floor((time()-$registration_date)/86400)+1;
						}
						$block_hash="$relative_post_date|$block_hash";
					}
					$block_hash=md5($block_hash);

					if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$block}_$block_hash.dat"))
					{
						$storage[$block]=unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$block}_$block_hash.dat"));
					}

					if ($_REQUEST['block_id']!=$block) {continue;}
					if (!is_file("$config[project_path]/blocks/$block_id/$block_id.php")) {die;}
					if (!is_file("$config[project_path]/admin/data/config/$page_id/$block.dat")) {die;}

					$storage[$block]=array();
					require_once("$config[project_path]/admin/include/setup_smarty_site.php");
					include_once("$config[project_path]/admin/include/list_countries.php");

					$args=array('block_id'=>$block_id,'block_name'=>$block_name);
					$block_content=insert_getBlock($args);

					if ($show_admin_debug)
					{
						require_once "$config[project_path]/admin/website_ui_debug.php";
						die;
					}

					echo replace_runtime_params($block_content);

					foreach ($plugin_extensions as $plugin_extension)
					{
						$plugin_function = "{$plugin_extension}PostAsyncRequest";
						if (function_exists($plugin_function))
						{
							$plugin_function();
						}
					}
					die;
				}
			}
		}
		die;
	} elseif ($_REQUEST['action']=='add_to_friends' || $_REQUEST['function']=='add_to_friends')
	{
		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_REQUEST['user_id']);
			if ($user_id>0 && $user_id<>$_SESSION['user_id'])
			{
				require_once("$config[project_path]/admin/include/functions.php");

				$is_friend=mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where (user_id=? and friend_id=?) or (friend_id=? and user_id=?)",$_SESSION['user_id'],$user_id,$_SESSION['user_id'],$user_id));
				if ($is_friend==0)
				{
					$message=strip_tags($_REQUEST['message']);

					$tokens_required = 0;
					$memberzone_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
					if ($_SESSION['status_id'] == 6)
					{
						if ($memberzone_data['ENABLE_TOKENS_MESSAGES_WEBMASTERS'] == 1)
						{
							$tokens_required = intval($memberzone_data['TOKENS_MESSAGES_WEBMASTERS']);
						}
					} elseif ($_SESSION['status_id'] == 3)
					{
						if ($memberzone_data['ENABLE_TOKENS_MESSAGES_PREMIUM'] == 1)
						{
							$tokens_required = intval($memberzone_data['TOKENS_MESSAGES_PREMIUM']);
						}
					} else
					{
						if ($memberzone_data['ENABLE_TOKENS_MESSAGES_ACTIVE'] == 1)
						{
							$tokens_required = intval($memberzone_data['TOKENS_MESSAGES_ACTIVE']);
						}
					}

					if ($tokens_required > 0 && $tokens_required > mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?", $_SESSION['user_id'])))
					{
						async_return_request_status(array(array('error_field_name' => 'message', 'error_code' => 'not_enough_tokens')));
					}

					$antispam_action = process_antispam_rules(21, $_REQUEST['message']);
					if (strpos($antispam_action, 'error') !== false)
					{
						sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
						async_return_request_status(array(array('error_code' => 'spam', 'block' => 'member_profile_view')));
					}

					sql_pr("insert into $config[tables_prefix]friends set user_id=?, friend_id=?, added_date=?", $_SESSION['user_id'], $user_id, date("Y-m-d H:i:s"));
					sql_pr("delete from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?", $_SESSION['user_id'], $user_id);
					$message_id = sql_insert("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=1, message=?, message_md5=md5(message), ip=?, added_date=?", $user_id, $_SESSION['user_id'], $message, ip2int($_SERVER['REMOTE_ADDR']), date("Y-m-d H:i:s"));

					if ($tokens_required > 0)
					{
						message_tokens_payment($tokens_required, $user_id, intval($memberzone_data['ENABLE_TOKENS_MESSAGES_REVENUE']) == 1, intval($memberzone_data['TOKENS_MESSAGES_REVENUE_INTEREST']), trim($memberzone_data['TOKENS_SALE_EXCLUDES']));
					}

					if (strpos($antispam_action, 'delete') !== false)
					{
						sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_id=1, is_spam=1 where message_id=?", $message_id);
						sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
					}
					async_return_request_status(null, null, array('message_id' => $message_id));
				}
			}
			async_return_request_status(array(array('error_code'=>'invalid_params')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in')));
		}
	} elseif ($_REQUEST['action']=='donate' || $_REQUEST['function']=='donate')
	{
		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		if ($_SESSION['user_id']>0)
		{
			$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
			if ($memberzone_data['ENABLE_TOKENS_DONATIONS']==1)
			{
				$user_id=intval($_REQUEST['user_id']);
				if ($user_id>0 && $user_id<>$_SESSION['user_id'])
				{
					require_once("$config[project_path]/admin/include/functions.php");

					$tokens_donated=intval($_REQUEST['tokens']);
					$tokens_required=intval($memberzone_data['TOKENS_DONATION_MIN']);

					if ($tokens_donated==0)
					{
						async_return_request_status(array(array('error_field_name'=>'tokens','error_code'=>'required')));
					} elseif ($tokens_donated<$tokens_required)
					{
						async_return_request_status(array(array('error_field_name'=>'tokens','error_code'=>'tokens_minimum','error_details'=>array($tokens_required))));
					} elseif ($tokens_donated>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id'])))
					{
						async_return_request_status(array(array('error_field_name'=>'tokens','error_code'=>'not_enough_tokens')));
					}

					$assign_tokens=$tokens_donated-ceil($tokens_donated*min(100,intval($memberzone_data['TOKENS_DONATION_INTEREST']))/100);
					$tokens_revenue=$tokens_donated-$assign_tokens;

					$donation_id=sql_insert("insert into $config[tables_prefix]log_donations_users set donator_id=?, user_id=?, tokens=?, tokens_revenue=?, comment=?, added_date=?",$_SESSION['user_id'],$user_id,$tokens_donated,$tokens_revenue,trim($_REQUEST['comment']),date("Y-m-d H:i:s"));

					if ($assign_tokens>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",$assign_tokens,$user_id);
						sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=10, user_id=?, ref_id=?, donation_id=?, tokens_granted=?, added_date=?",$user_id,$_SESSION['user_id'],$donation_id,$assign_tokens,date("Y-m-d H:i:s"));
					}

					sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$tokens_donated, 0) where user_id=?",$_SESSION['user_id']);
					$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));

					async_return_request_status();
				}
			}
			async_return_request_status(array(array('error_code'=>'invalid_params')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in')));
		}
	} elseif ($_REQUEST['action']=='subscribe' || $_REQUEST['function']=='subscribe')
	{
		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions.php");
			require_once("$config[project_path]/admin/include/database_selectors.php");

			$s_object_id=$s_type_id=0;
			$s_table_name=$s_table_key=$s_cache_info=$s_cache_key='';
			if (intval($_REQUEST['subscribe_user_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_user_id']); $s_type_id=1;
				$s_table_name="$config[tables_prefix]users"; $s_table_key="user_id";
				if ($s_object_id==$_SESSION['user_id'])
				{
					async_return_request_status(array(array('error_code'=>'invalid_params')));
				}
			} elseif (intval($_REQUEST['subscribe_cs_id']) > 0 || intval($_REQUEST['subscribe_content_source_id']) > 0)
			{
				$s_object_id = intval($_REQUEST['subscribe_cs_id']);
				if ($s_object_id == 0)
				{
					$s_object_id = intval($_REQUEST['subscribe_content_source_id']);
				}
				$s_type_id = 3;
				$s_table_name = "$config[tables_prefix]content_sources";
				$s_table_key = "content_source_id";
				$s_cache_info = 'content_sources_info';
				$s_cache_key = 'cs';
			} elseif (intval($_REQUEST['subscribe_model_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_model_id']); $s_type_id=4;
				$s_table_name="$config[tables_prefix]models"; $s_table_key="model_id";
				$s_cache_info='models_info'; $s_cache_key='model';
			} elseif (intval($_REQUEST['subscribe_dvd_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_dvd_id']); $s_type_id=5;
				$s_table_name="$config[tables_prefix]dvds"; $s_table_key="dvd_id";
				$s_cache_info='dvds_info'; $s_cache_key='dvd';
			} elseif (intval($_REQUEST['subscribe_category_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_category_id']); $s_type_id=6;
				$s_table_name="$config[tables_prefix]categories"; $s_table_key="category_id";
			} elseif (intval($_REQUEST['subscribe_playlist_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_playlist_id']); $s_type_id=13;
				$s_table_name="$config[tables_prefix]playlists"; $s_table_key="playlist_id";
				$s_cache_info='playlists_info'; $s_cache_key='playlist';
				$database_selectors['generic_selector_dir']='dir';
			}

			if ($s_object_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_object_type_id=?",$_SESSION['user_id'],$s_object_id,$s_type_id))==0)
				{
					$s_object=mr2array_single(sql_pr("select * from $s_table_name where $s_table_key=?",$s_object_id));
					if (intval($s_object[$s_table_key])==0)
					{
						async_return_request_status(array(array('error_code'=>'invalid_params')));
					}

					$s_purchase_id=0;
					if ($s_type_id==1 || $s_type_id==5)
					{
						$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
						$s_tokens=0;
						$s_expiry_period=0;
						$s_token_sale=0;
						if ($s_type_id==1)
						{
							$s_purchase_table_key='profile_id';
							$s_award_type_id=13;
							$s_expiry_period=intval($memberzone_data['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD']);
							$s_token_sale=intval($memberzone_data['ENABLE_TOKENS_SALE_MEMBERS']);
							if (intval($memberzone_data['ENABLE_TOKENS_SUBSCRIBE_MEMBERS'])==1)
							{
								$s_tokens=intval($s_object['tokens_required']);
								if ($s_tokens==0)
								{
									$s_tokens=intval($memberzone_data['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']);
								}
							}
						} elseif ($s_type_id==5)
						{
							$s_purchase_table_key='dvd_id';
							$s_award_type_id=14;
							$s_expiry_period=intval($memberzone_data['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD']);
							$s_token_sale=intval($memberzone_data['ENABLE_TOKENS_SALE_DVDS']);
							if (intval($memberzone_data['ENABLE_TOKENS_SUBSCRIBE_DVDS'])==1)
							{
								$s_tokens=intval($s_object['tokens_required']);
								if ($s_tokens==0)
								{
									$s_tokens=intval($memberzone_data['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']);
								}
							}
						}

						if ($s_tokens>0)
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_purchases where user_id=? and $s_purchase_table_key=? and expiry_date>?",$_SESSION['user_id'],$s_object_id,date("Y-m-d H:i:s")))==0)
							{
								if ($s_tokens>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id'])))
								{
									async_return_request_status(array(array('error_code'=>'subscription_not_enough_tokens')));
								}

								$s_added_date=date("Y-m-d H:i:s");
								$s_expiry_date="2070-01-01 00:00:00";
								if ($s_expiry_period>0)
								{
									$s_expiry_date=date("Y-m-d H:i:s",time()+$s_expiry_period*86400);
								}

								$s_assign_tokens=0;
								if ($s_token_sale==1 && $s_object['user_id']>0)
								{
									$s_assign_tokens=$s_tokens-ceil($s_tokens*min(100,intval($memberzone_data['TOKENS_SALE_INTEREST']))/100);

									$s_exclude_users=array_map('trim',explode(",",$memberzone_data['TOKENS_SALE_EXCLUDES']));
									$s_username=mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?",$s_object['user_id']));
									if ($s_username && in_array($s_username,$s_exclude_users))
									{
										$s_assign_tokens=0;
									}

									if ($s_assign_tokens>0)
									{
										sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",$s_assign_tokens,$s_object['user_id']);
										sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=?, user_id=?, $s_purchase_table_key=?, tokens_granted=?, added_date=?",$s_award_type_id,$s_object['user_id'],$s_object_id,$s_assign_tokens,date("Y-m-d H:i:s"));
									}
								}
								$s_tokens_revenue=$s_tokens-$s_assign_tokens;

								$s_purchase_id=sql_insert("insert into $config[tables_prefix]users_purchases set is_recurring=1, $s_purchase_table_key=?, user_id=?, owner_user_id=?, tokens=?, tokens_revenue=?, added_date=?, expiry_date=?",$s_object_id,$_SESSION['user_id'],$s_object['user_id'],$s_tokens,$s_tokens_revenue,$s_added_date,$s_expiry_date);

								sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$s_tokens, 0) where user_id=?",$_SESSION['user_id']);

								$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
								$_SESSION['content_purchased'][]=array($s_purchase_table_key=>$s_object_id);
								$_SESSION['content_purchased_amount']=array_cnt($_SESSION['content_purchased']);
							}
						}
					}

					$s_subscription_id=sql_insert("insert into $config[tables_prefix]users_subscriptions set user_id=?, subscribed_object_id=?, subscribed_object_type_id=?, added_date=?",$_SESSION['user_id'],$s_object_id,$s_type_id,date("Y-m-d H:i:s"));
					if ($s_purchase_id>0)
					{
						sql_pr("update $config[tables_prefix]users_purchases set subscription_id=? where purchase_id=?",$s_subscription_id,$s_purchase_id);
					}
					sql_pr("update $s_table_name set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$s_table_name.$s_table_key and subscribed_object_type_id=$s_type_id) where $s_table_key=?",$s_object_id);

					if ($s_cache_info!='')
					{
						$obj_info=mr2array_single(sql_pr("select $s_table_key, $database_selectors[generic_selector_dir] as dir from $s_table_name where $s_table_key=?",$s_object_id));
						if ($obj_info[$s_table_key]>0)
						{
							inc_block_version($s_cache_info,$s_cache_key,$obj_info[$s_table_key],$obj_info['dir'],$_SESSION['user_id']);
						}
					}
					$_SESSION['subscriptions_amount']=mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=?",$_SESSION['user_id']));
				}
				$new_subscribers = mr2number(sql_pr("select subscribers_count from $s_table_name where $s_table_key=?", $s_object_id, $s_type_id));
				async_return_request_status(null, null, ['subscribers' => $new_subscribers, 'user_subscriptions' => intval($_SESSION['subscriptions_amount'])]);
			}
			async_return_request_status(array(array('error_code'=>'invalid_params')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in')));
		}
	} elseif ($_REQUEST['action']=='unsubscribe' || $_REQUEST['function']=='unsubscribe')
	{
		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions.php");
			require_once("$config[project_path]/admin/include/database_selectors.php");

			$s_object_id=$s_type_id=0;
			$s_table_name=$s_table_key=$s_cache_info=$s_cache_key='';
			if (intval($_REQUEST['unsubscribe_user_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_user_id']); $s_type_id=1;
				$s_table_name="$config[tables_prefix]users"; $s_table_key="user_id";
			} elseif (intval($_REQUEST['unsubscribe_cs_id']) > 0 || intval($_REQUEST['unsubscribe_content_source_id']) > 0)
			{
				$s_object_id = intval($_REQUEST['unsubscribe_cs_id']);
				if ($s_object_id == 0)
				{
					$s_object_id = intval($_REQUEST['unsubscribe_content_source_id']);
				}
				$s_type_id = 3;
				$s_table_name = "$config[tables_prefix]content_sources";
				$s_table_key = "content_source_id";
				$s_cache_info = 'content_sources_info';
				$s_cache_key = 'cs';
			} elseif (intval($_REQUEST['unsubscribe_model_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_model_id']); $s_type_id=4;
				$s_table_name="$config[tables_prefix]models"; $s_table_key="model_id";
				$s_cache_info='models_info'; $s_cache_key='model';
			} elseif (intval($_REQUEST['unsubscribe_dvd_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_dvd_id']); $s_type_id=5;
				$s_table_name="$config[tables_prefix]dvds"; $s_table_key="dvd_id";
				$s_cache_info='dvds_info'; $s_cache_key='dvd';
			} elseif (intval($_REQUEST['unsubscribe_category_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_category_id']); $s_type_id=6;
				$s_table_name="$config[tables_prefix]categories"; $s_table_key="category_id";
			} elseif (intval($_REQUEST['unsubscribe_playlist_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_playlist_id']); $s_type_id=13;
				$s_table_name="$config[tables_prefix]playlists"; $s_table_key="playlist_id";
				$s_cache_info='playlists_info'; $s_cache_key='playlist';
				$database_selectors['generic_selector_dir']='dir';
			}

			if ($s_object_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_object_type_id=?",$_SESSION['user_id'],$s_object_id,$s_type_id))>0)
				{
					sql_pr("delete from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_object_type_id=?",$_SESSION['user_id'],$s_object_id,$s_type_id);
					sql_pr("update $s_table_name set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$s_table_name.$s_table_key and subscribed_object_type_id=$s_type_id) where $s_table_key=?",$s_object_id);

					if ($s_cache_info!='')
					{
						$obj_info=mr2array_single(sql_pr("select $s_table_key, $database_selectors[generic_selector_dir] as dir from $s_table_name where $s_table_key=?",$s_object_id));
						if ($obj_info[$s_table_key]>0)
						{
							inc_block_version($s_cache_info,$s_cache_key,$obj_info[$s_table_key],$obj_info['dir'],$_SESSION['user_id']);
						}
					}
					$_SESSION['subscriptions_amount']=mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=?",$_SESSION['user_id']));
				}
				$new_subscribers = mr2number(sql_pr("select subscribers_count from $s_table_name where $s_table_key=?", $s_object_id, $s_type_id));
				async_return_request_status(null, null, ['subscribers' => $new_subscribers, 'user_subscriptions' => intval($_SESSION['subscriptions_amount'])]);
			}
			async_return_request_status(array(array('error_code'=>'invalid_params')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in')));
		}
	} elseif ($_REQUEST['action']=='rotator_videos' || $_REQUEST['function']=='rotator_videos')
	{
		$pqr=trim($_REQUEST['pqr']);
		if ($_SESSION['userdata']['user_id']>0)
		{
			header("Content-type: image/gif");
			die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
		}

		if (!is_dir("$config[project_path]/admin/data/engine/rotator")) {mkdir("$config[project_path]/admin/data/engine/rotator",0777);chmod("$config[project_path]/admin/data/engine/rotator",0777);}
		if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos")) {mkdir("$config[project_path]/admin/data/engine/rotator/videos",0777);chmod("$config[project_path]/admin/data/engine/rotator/videos",0777);}
		file_put_contents("$config[project_path]/admin/data/engine/rotator/videos/clicks.dat", "x:$pqr\r\n", LOCK_EX | FILE_APPEND);

		header("Content-type: image/gif");
		die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
	}

	configure_locale();
	if (is_file("$config[project_path]/langs/default.php"))
	{
		include_once("$config[project_path]/langs/default.php");
	}
	if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
	{
		include_once("$config[project_path]/langs/$config[locale].php");
	}
	include_once("$config[project_path]/admin/include/pre_async_action_code.php");

	if (is_file("$config[project_path]/admin/data/config/$page_id/config.dat"))
	{
		require_once("$config[project_path]/admin/include/database_selectors.php");
		$stats_params=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));

		$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/config.dat"));
		check_page_access(intval($temp[4]), intval($temp[5]), trim($temp[6]));
		$block_list=explode("|AND|",trim($temp[2]));

		foreach ($block_list as $block)
		{
			$block_id=substr($block,0,strpos($block,"[SEP]"));
			$block=str_replace("[SEP]","_",$block);

			if (!is_file("$config[project_path]/blocks/$block_id/$block_id.php")) {continue;}
			if (!is_file("$config[project_path]/admin/data/config/$page_id/$block.dat")) {continue;}

			include_once("$config[project_path]/blocks/$block_id/$block_id.php");
			$async_function="{$block_id}Async";
			if (!function_exists($async_function)) {continue;}

			$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/$block.dat"));
			$config_params=array();
			if (trim($temp[1])<>'')
			{
				$temp_params=explode("&",$temp[1]);
				foreach ($temp_params as $temp_param)
				{
					$temp_param=explode("=",$temp_param,2);
					$config_params[trim($temp_param[0])]=trim($temp_param[1]);
				}
			}
			try
			{
				$async_function($config_params);
			} catch (Exception $e)
			{
				KvsContext::log_exception($e);
				async_return_request_status(array(array('error_code'=>'invalid_params')));
			}
		}
	}
	async_return_request_status(array(array('error_code'=>'invalid_params')));
} elseif (($_REQUEST['action']=='redirect_adv' || $_REQUEST['action']=='trace') && $_REQUEST['id']>0)
{
	require_once "$config[project_path]/admin/include/functions_admin.php";

	$id=intval($_REQUEST['id']);

	$ad_info = null;
	foreach (get_site_spots() as $spot)
	{
		if (isset($spot['ads'][$id]))
		{
			$ad_info=$spot['ads'][$id];
			break;
		}
	}

	if (isset($ad_info))
	{
		if ($_COOKIE['kt_tcookie']=='1')
		{
			$stats_params = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
			if (intval($stats_params['collect_traffic_stats']) == 1)
			{
				$device_type = 0;
				if (intval($stats_params['collect_traffic_stats_devices']) == 1)
				{
					$device_type = get_device_type();
				}

				file_put_contents("$config[project_path]/admin/data/stats/adv_out.dat", date("Y-m-d") . "|$id|$_SERVER[GEOIP_COUNTRY_CODE]|$_COOKIE[kt_referer]|$_COOKIE[kt_qparams]|$device_type\r\n", LOCK_EX | FILE_APPEND);
			}
		}

		$url=$ad_info['url'];

		if (is_file("$config[project_path]/admin/data/system/runtime_params.dat"))
		{
			$runtime_params = @unserialize(file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
			if ($runtime_params)
			{
				foreach ($runtime_params as $param)
				{
					$var = trim($param['name']);
					if ($var != '')
					{
						$val = trim($_SESSION['runtime_params'][$var]);
						if ($val == '')
						{
							$val = trim($param['default_value']);
						}
						$url = str_replace("%$var%", $val, $url);
					}
				}
			}
		}

		header("Location: $url");
	}
	die;
}

$start_time = microtime(true);
$start_memory = 0;
$performance_log_summary = '';

if ($_SESSION['user_id'] < 1 && strlen($_COOKIE['kt_member']) == 32)
{
	require_once "$config[project_path]/admin/include/functions.php";
	require_once "$config[project_path]/admin/include/database_selectors.php";

	$result = sql_pr("select * from $config[tables_prefix]users where status_id not in (0,1,4,5) and remember_me_key=? and remember_me_valid_for>=?", $_COOKIE['kt_member'], date("Y-m-d H:i:s"));
	if (mr2rows($result) == 0)
	{
		set_cookie('kt_member', '', time() - 86400);
	} else
	{
		$user_data = mr2array_single($result);
		login_user($user_data, 0);
	}
}

$page_config = [];
if (is_file("$config[project_path]/admin/data/config/$page_id/config.dat"))
{
	$temp = explode('||', file_get_contents("$config[project_path]/admin/data/config/$page_id/config.dat"));
	check_page_access(intval($temp[4]), intval($temp[5]), trim($temp[6]));

	$page_config['cache_time'] = intval($temp[0]);
	$page_config['is_compressed'] = intval($temp[1]);
	$page_config['content_type'] = intval($temp[3]);
	$page_config['dynamic_http_params'] = trim($temp[7]);
	$page_config['memory_limit'] = intval($temp[8]);

	$page_config['blocks_list'] = explode('|AND|', trim($temp[2]));
	foreach ($page_config['blocks_list'] as $key => $block)
	{
		$block_id = substr($block, 0, strpos($block, '[SEP]'));
		$block_uid = str_replace('[SEP]', '_', $block);
		if (!is_file("$config[project_path]/blocks/$block_id/$block_id.php"))
		{
			unset($page_config['blocks_list'][$key]);
			continue;
		}
		if (!is_file("$config[project_path]/admin/data/config/$page_id/$block_uid.dat"))
		{
			unset($page_config['blocks_list'][$key]);
			continue;
		}

		$temp = explode('||', file_get_contents("$config[project_path]/admin/data/config/$page_id/$block_uid.dat"));
		$config_params = [];
		if (trim($temp[1]) != '')
		{
			$temp_params = explode('&', $temp[1]);
			foreach ($temp_params as $temp_param)
			{
				$temp_param = explode('=', $temp_param, 2);
				$config_params[trim($temp_param[0])] = trim($temp_param[1]);
			}
		}
		$dynamic_params = [];
		if (trim($temp[4]) != '')
		{
			$dynamic_params = explode(',', $temp[4]);
		}
		$page_config['blocks_list'][$key] = ['block_id' => $block_id, 'block_uid' => $block_uid, 'params' => $config_params, 'dynamic_params' => $dynamic_params];
	}
} else
{
	die("Page is not defined within engine: $page_id");
}

$storage = [];

if ($website_ui_data['DISABLE_WEBSITE'] == 1 && $_SESSION['userdata']['user_id'] < 1)
{
	http_response_code(404);
	if (is_file("$config[project_path]/website_disabled.html"))
	{
		die(file_get_contents("$config[project_path]/website_disabled.html"));
	}

	die('The requested URL was not found on this server.');
}

if ($page_config['memory_limit'] > 0)
{
	ini_set('memory_limit', "$page_config[memory_limit]M");
}

$stats_params = @unserialize(file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
if (intval($stats_params['collect_traffic_stats']) == 0)
{
	$config['disable_stats'] = 'true';
}

include_once "$config[project_path]/admin/include/pre_initialize_page_code.php";

configure_locale();
if (is_file("$config[project_path]/langs/default.php"))
{
	include_once("$config[project_path]/langs/default.php");
}
if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
{
	include_once("$config[project_path]/langs/$config[locale].php");
}

$show_admin_toolbar = false;
$show_admin_debug = false;
$show_admin_debug_block_data = [];
$force_admin_no_cache_page = false;
$force_admin_no_cache_blocks = false;
if ($_SESSION['userdata']['user_id'] > 0)
{
	if ($page_config['content_type'] == 0 && @intval($_SESSION['save']['options']['disable_toolbar']) == 0)
	{
		$show_admin_toolbar = true;
	}

	if (isset($_COOKIE['kt_admin_action']))
	{
		switch ($_COOKIE['kt_admin_action'])
		{
			case 'enable_caching':
				$_SESSION['save']['options']['enable_site_caching'] = 1;
				break;
			case 'disable_caching':
				unset($_SESSION['save']['options']['enable_site_caching']);
				break;
			case 'disable_toolbar':
				$_SESSION['save']['options']['disable_toolbar'] = 1;
				break;
		}
		set_cookie('kt_admin_action', '', time());
	}

	$force_admin_no_cache_page = true;
	$force_admin_no_cache_blocks = true;
	if (@intval($_SESSION['save']['options']['enable_site_caching']) == 1 && @intval($_SESSION['save']['options']['disable_toolbar']) == 0)
	{
		$force_admin_no_cache_blocks = false;
	}

	if ($_REQUEST['debug'] == 'true')
	{
		$show_admin_toolbar = false;
		$show_admin_debug = true;
		$force_admin_no_cache_page = true;
		$force_admin_no_cache_blocks = true;
	}
}

foreach ($plugin_extensions as $plugin_extension)
{
	$plugin_function = "{$plugin_extension}PreSiteRequest";
	if (function_exists($plugin_function))
	{
		$plugin_function();
	}
}

if ($page_config['content_type'] == 1)
{
	header('Content-Type: text/xml; charset=utf-8');
} elseif ($page_config['content_type'] == 2)
{
	header('Content-Type: application/json; charset=utf-8');
} elseif ($page_config['content_type'] == 3)
{
	header('Content-Type: text/plain; charset=utf-8');
} else
{
	header('Content-Type: text/html; charset=utf-8');
}

if ($_SESSION['user_id'] > 0)
{
	if ($website_ui_data['ENABLE_USER_MESSAGES_REFRESH'] == 1)
	{
		// sync new user messages
		if (time() - $_SESSION['last_time_get_new_message_amount'] > ($website_ui_data['USER_MESSAGES_REFRESH_INTERVAL'] - 1) * 60)
		{
			require_once("$config[project_path]/admin/include/functions.php");
			messages_changed();

			$_SESSION['last_time_get_new_message_amount'] = time();
		}
	}

	// sync user status
	$user_data = mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?", $_SESSION['user_id']));
	unset($user_data['pass'], $user_data['pass_bill'], $user_data['temp_pass']);

	if ($user_data['avatar'] != '')
	{
		$user_data['avatar_url'] = $config['content_url_avatars'] . "/" . $user_data['avatar'];
	}
	if ($user_data['cover'] != '')
	{
		$user_data['cover_url'] = $config['content_url_avatars'] . "/" . $user_data['cover'];
	}
	if ($user_data['birth_date'] != '0000-00-00')
	{
		require_once "$config[project_path]/admin/include/functions.php";
		$age = get_time_passed($user_data['birth_date']);
		$user_data['age'] = $age['value'];
	}

	$expose_to_session = ['user_id', 'display_name', 'last_login_date', 'added_date', 'avatar', 'avatar_url', 'cover',
			'cover_url', 'status_id', 'username', 'content_source_group_id', 'is_trusted', 'tokens_available', 'birth_date', 'age', 'gender_id'];
	foreach ($expose_to_session as $key)
	{
		$_SESSION[$key] = $user_data[$key];
	}
	$_SESSION['user_info'] = $user_data;

	if (intval($_SESSION['status_id']) == 0 || (intval($website_ui_data['ALLOW_MULTISESSION']) != 1 && $user_data['last_session_id_hash'] !== '' && $user_data['last_session_id_hash'] != md5(session_id())))
	{
		include_once "$config[project_path]/logout.php";
		die;
	} elseif ($_SESSION['status_id'] == '3')
	{
		$transaction_data = mr2array_single(sql_pr("select (UNIX_TIMESTAMP(access_end_date) - UNIX_TIMESTAMP(?)) / 3600 as hours_left, is_unlimited_access, external_guid, external_package_id, internal_provider_id from $config[tables_prefix]bill_transactions where status_id=1 and user_id=? order by access_end_date desc limit 1", date("Y-m-d H:i:s"), $_SESSION['user_id']));
		$_SESSION['paid_access_hours_left'] = intval($transaction_data['hours_left']);
		$_SESSION['paid_access_is_unlimited'] = intval($transaction_data['is_unlimited_access']);
		$_SESSION['paid_access_internal_provider_id'] = trim($transaction_data['internal_provider_id']);
		$_SESSION['external_guid'] = trim($transaction_data['external_guid']);
		$_SESSION['external_package_id'] = trim($transaction_data['external_package_id']);
	} else
	{
		unset($_SESSION['paid_access_hours_left'], $_SESSION['paid_access_is_unlimited'], $_SESSION['external_package_id'], $_SESSION['external_guid']);
	}

	$_SESSION['content_purchased'] = mr2array(sql_pr("select distinct video_id, album_id, profile_id, dvd_id from $config[tables_prefix]users_purchases where user_id=? and expiry_date>?", $_SESSION['user_id'], date("Y-m-d H:i:s")));
	$_SESSION['content_purchased_amount'] = array_cnt($_SESSION['content_purchased']);
}

if (is_file("$config[project_path]/admin/data/system/runtime_params.dat"))
{
	$runtime_params = @unserialize(file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
	if ($runtime_params)
	{
		foreach ($runtime_params as $param)
		{
			$var = trim($param['name']);
			if (isset($_GET[$var]) || isset($_POST[$var]) || isset($_COOKIE["kt_rt_$var"]))
			{
				$val = $_GET[$var];
				if ($val == '')
				{
					$val = $_POST[$var];
				}
				if ($val == '')
				{
					$val = $_COOKIE["kt_rt_$var"];
				}
				if ($var != '' && $val != '')
				{
					$_SESSION['runtime_params'][$var] = $val;
					if (isset($_GET[$var]) || isset($_POST[$var]))
					{
						set_cookie("kt_rt_$var", $val, time() + (intval($param['lifetime']) ?: 360) * 86400);
					}
				}
			}
		}
	}
}

$use_memcache = 1;
if ($config['memcache_server'] == '' || $_SESSION['user_id'] > 0 || array_cnt($_POST) > 0 || $force_admin_no_cache_page || $website_ui_data['WEBSITE_CACHING'] >= 1 || !class_exists('Memcached'))
{
	$use_memcache = 0;
} else
{
	$memcache = new Memcached();
	$memcache->addServer($config['memcache_server'], $config['memcache_port']) or $use_memcache = 0;
}

if ($use_memcache == 1)
{
	$page_hash = '';
	if ($page_config['cache_time'] > 0)
	{
		$is_no_cache = 0;
		foreach ($page_config['blocks_list'] as $block)
		{
			require_once "$config[project_path]/blocks/$block[block_id]/$block[block_id].php";
			$hash_function = "{$block['block_id']}GetHash";
			if (!function_exists($hash_function))
			{
				continue;
			}

			$block_hash = $hash_function($block['params']);
			if (in_array($block_hash, ['nocache', 'runtime_nocache']))
			{
				$is_no_cache = 1;
				break;
			}

			foreach ($block['dynamic_params'] as $dynamic_param)
			{
				if (trim($dynamic_param) != '' && trim($_REQUEST[trim($dynamic_param)]) != '')
				{
					$block_hash = 'dyn:' . trim($_REQUEST[trim($dynamic_param)]) . '|' . $block_hash;
				}
			}

			$page_hash .= "$block_hash|";

			$block_hash = "$config[project_url]|$page_id|$block[block_uid]|$block_hash";
			if ($config['cache_control_user_status_in_cache'] == 'true')
			{
				$block_hash = intval($_SESSION['status_id']) . "|$block_hash";
			}
			if ($config['project_url_scheme'] == "https")
			{
				$block_hash = "https|$block_hash";
			}
			if ($config['device'] <> "")
			{
				$block_hash = "$config[device]|$block_hash";
			}
			if ($config['locale'] <> '')
			{
				$block_hash = "$config[locale]|$block_hash";
			} elseif ($config['theme_locale'] <> '')
			{
				$block_hash = "$config[theme_locale]|$block_hash";
			}
			if ($config['relative_post_dates'] == "true")
			{
				$relative_post_date = 0;
				if ($_SESSION['user_id'] > 0 && $_SESSION['added_date'] <> '')
				{
					$registration_date = strtotime($_SESSION['added_date']);
					$relative_post_date = floor((time() - $registration_date) / 86400) + 1;
				}
				$block_hash = "$relative_post_date|$block_hash";
			}
			$block_hash = md5($block_hash);

			if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$block['block_uid']}_$block_hash.dat"))
			{
				$storage[$block['block_uid']] = unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$block['block_uid']}_$block_hash.dat"));
			}
		}

		if ($page_config['dynamic_http_params'] != '')
		{
			$page_dynamic_params = explode(',', $page_config['dynamic_http_params']);
			foreach ($page_dynamic_params as $dynamic_param)
			{
				if (trim($dynamic_param) != '' && trim($_REQUEST[trim($dynamic_param)]) != '')
				{
					$page_hash .= 'dyn:' . trim($_REQUEST[trim($dynamic_param)]) . '|';
				}
			}
		}

		if ($is_no_cache != 1)
		{
			$page_hash = "$config[project_url]|$page_id|" . md5($page_hash);
			if ($config['project_url_scheme'] == 'https')
			{
				$page_hash = "https|$page_hash";
			}
			if ($config['device'] != '')
			{
				$page_hash = "$config[device]|$page_hash";
			}
			if ($config['locale'] != '')
			{
				$page_hash = "$config[locale]|$page_hash";
			} elseif ($config['theme_locale'] != '')
			{
				$page_hash = "$config[theme_locale]|$page_hash";
			}

			$page_content = $memcache->get($page_hash);
			if ($page_content !== false)
			{
				write_stats(0);
				if ($page_config['content_type'] == 0)
				{
					include_once "$config[project_path]/admin/include/pre_process_page_code.php";
				}
				foreach ($page_config['blocks_list'] as $block)
				{
					$pre_process_function = "{$block['block_id']}PreProcess";
					if (function_exists($pre_process_function))
					{
						$pre_process_function($block['params'], $block['block_uid']);
					}
				}
				if ($page_config['content_type'] == 0)
				{
					include_once "$config[project_path]/admin/include/pre_display_page_code.php";
				}
				echo replace_runtime_params($page_content);

				foreach ($plugin_extensions as $plugin_extension)
				{
					$plugin_function = "{$plugin_extension}PostSiteRequest";
					if (function_exists($plugin_function))
					{
						$plugin_function();
					}
				}

				if ($page_config['content_type'] == 0)
				{
					include_once "$config[project_path]/admin/include/post_process_page_code.php";
				}

				log_performance(microtime(true) - $start_time, memory_get_peak_usage() - $start_memory, 1, null);
				die;
			}
		}
	}
}

$overload_max_la_blocks = intval($website_ui_data['OVERLOAD_MAX_LA_BLOCKS']);
if ($overload_max_la_blocks == 0)
{
	$overload_max_la_blocks = intval($config['overload_max_la_blocks']) ?: 30;
}

if ($la > $overload_max_la_blocks)
{
	write_overload_stats(3);
	http_response_code(503);
	if (is_file("$config[project_path]/overload.html"))
	{
		die(file_get_contents("$config[project_path]/overload.html"));
	}
	die('Sorry, the website is temporary unavailable. Please come back later!');
}

$random_version = md5($config['project_version'] . $config['installation_id']);
$config['project_version'] = hexdec($random_version[0]) . '.' . hexdec($random_version[1]) . '.' . hexdec($random_version[2]);

$js_files = [];
$js_files[] = "KernelTeamVideoSharingSystem.js?v={$config['project_version']}";
if ($_SESSION['user_id'] > 0)
{
	$js_files[] = "KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
}

foreach ($page_config['blocks_list'] as $block)
{
	require_once "$config[project_path]/blocks/$block[block_id]/$block[block_id].php";
	$js_function = "{$block['block_id']}Javascript";
	if (!function_exists($js_function))
	{
		continue;
	}

	$block_js = $js_function($block['params']);
	if (strlen($block_js) > 0)
	{
		$js_files[] = $block_js;
	}
}
$js_files = array_unique($js_files);

$js_includes = '';
foreach ($js_files as $js_file)
{
	$js_includes .= "<script type=\"text/javascript\" src=\"$config[statics_url]/js/$js_file\"></script>\n    ";
}

$request_uri = clean_request_uri();

require_once "$config[project_path]/admin/include/setup_smarty_site.php";
include_once "$config[project_path]/admin/include/list_countries.php";

$smarty = new mysmarty_site();
$smarty->assign_by_ref('config', $config);
$smarty->assign_by_ref('storage', $storage);
$smarty->assign_by_ref('global_storage', $global_storage);
$smarty->assign_by_ref('list_countries', $list_countries['name']);
$smarty->assign_by_ref('list_countries_codes', $list_countries['code']);
$smarty->assign('js_includes', $js_includes);
$smarty->assign('request_uri', $request_uri);
$smarty->assign('page_id', $page_id);

if (is_file("$config[project_path]/admin/data/plugins/recaptcha/enabled.dat") && is_file("$config[project_path]/admin/data/plugins/recaptcha/data.dat"))
{
	$recaptcha_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/recaptcha/data.dat"));
	if (is_array($recaptcha_data) && $recaptcha_data['site_key'] && intval($recaptcha_data['is_enabled']) > 0)
	{
		$recaptcha_site_key = $recaptcha_data['site_key'];
		if (is_array($recaptcha_data['aliases']))
		{
			foreach ($recaptcha_data['aliases'] as $recaptcha_alias)
			{
				if (str_replace('www.', '', $_SERVER['HTTP_HOST']) == $recaptcha_alias['domain'])
				{
					$recaptcha_site_key = $recaptcha_alias['site_key'];
					break;
				}
			}
		}
		$smarty->assign('recaptcha_site_key', $recaptcha_site_key);
		if (intval($recaptcha_data['is_enabled']) == 1)
		{
			$smarty->assign('recaptcha_type', 'google');
		} elseif (intval($recaptcha_data['is_enabled']) == 2)
		{
			$smarty->assign('recaptcha_type', 'cloudflare');
		}
	}
}

$is_post = false;
if (array_cnt($_POST) > 0)
{
	$is_post = true;
}

if (!$is_post)
{
	write_stats(0);
}
if ($page_config['content_type'] == 0)
{
	include_once "$config[project_path]/admin/include/pre_process_page_code.php";
}
if (is_array($lang))
{
	$smarty->assign_by_ref('lang', $lang);
}

$admin_toolbar = '';
$template = $smarty->fetch("$page_id.tpl");
if ($show_admin_debug)
{
	require_once "$config[project_path]/admin/website_ui_debug.php";
	die;
}

if (!$is_post && $page_config['content_type'] == 0)
{
	include_once "$config[project_path]/admin/include/pre_display_page_code.php";
}
if ($show_admin_toolbar && strpos($template, '<html') !== false)
{
	// admin toolbar
	$admin_toolbar = include "$config[project_path]/admin/website_ui_toolbar.php";
}
echo replace_runtime_params($template);

foreach ($plugin_extensions as $plugin_extension)
{
	$plugin_function = "{$plugin_extension}PostSiteRequest";
	if (function_exists($plugin_function))
	{
		$plugin_function();
	}
}

if ($memcache && $use_memcache == 1 && $page_hash != '' && $page_config['cache_time'] > 0 && $is_no_cache != 1)
{
	$memcache->set($page_hash, $template, $page_config['cache_time']);
}

if ($page_config['content_type'] == 0)
{
	include_once "$config[project_path]/admin/include/post_process_page_code.php";
}

if ($show_admin_toolbar)
{
	echo $admin_toolbar;
}

log_performance(microtime(true) - $start_time, memory_get_peak_usage() - $start_memory, 0, null);


function insert_getGlobal($args)
{
	global $config,$page_id,$smarty,$storage,$request_uri,$recaptcha_data,$global_storage,$regexp_check_email,$regexp_check_alpha_numeric,$la,$use_memcache,$list_countries,$lang,$database_selectors;

	if (!is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
	{
		return '';
	}
	$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
	$global_blocks_list=explode("|AND|",trim($temp[2]));
	foreach ($global_blocks_list as $block)
	{
		$block_id=substr($block,0,strpos($block,"[SEP]"));
		$block_name=substr($block,strpos($block,"[SEP]")+5);
		$block=str_replace("[SEP]","_",$block);

		if ($args['global_id']!=$block) {continue;}

		$old_page_id=$page_id;
		$old_storage=$storage;
		$page_id='$global';
		$storage=array();

		$args=array('block_id'=>$block_id,'block_name'=>$block_name);
		$block_content=insert_getBlock($args);

		$global_storage[$block]=$storage[$block];
		$smarty->assign_by_ref("global_storage",$global_storage);

		$page_id=$old_page_id;
		$storage=$old_storage;
		return $block_content;
	}
	return '';
}

function insert_getBlock($args)
{
	global $config,$page_id,$smarty,$storage,$request_uri,$recaptcha_data,$regexp_check_email,$regexp_check_alpha_numeric,$la,$use_memcache,$list_countries,$lang,
			$database_selectors,$website_ui_data,$show_admin_toolbar,$force_admin_no_cache_blocks,$show_admin_debug,$show_admin_debug_block_data;

	umask(0);
	$start_time_block=microtime(true);
	$start_memory_block=memory_get_usage();

	$block_id=$args['block_id'];
	$block_name=$args['block_name'];
	$block_name_dir=strtolower(str_replace(" ","_",$block_name));
	$object_id="{$block_id}_$block_name_dir";

	$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/$object_id.dat"));
	$cache_time=intval($temp[0]);
	if (intval($temp[2])==1 && $_SESSION['user_id']>0)
	{
		$cache_time=0;
	}
	$config_params=array();
	if (trim($temp[1])<>'')
	{
		$temp_params=explode("&",$temp[1]);
		foreach ($temp_params as $temp_param)
		{
			$temp_param=explode("=",$temp_param,2);
			$config_params[trim($temp_param[0])]=trim($temp_param[1]);
		}
	}

	include_once("$config[project_path]/blocks/$block_id/$block_id.php");
	$smarty=new mysmarty_site();
	$smarty->assign_by_ref("config",$config);
	$smarty->assign_by_ref("storage",$storage);


	$pre_process_function="{$block_id}PreProcess";
	if (function_exists($pre_process_function))
	{
		$pre_process_function($config_params,$object_id);
	}

	$hash_function="{$block_id}GetHash";
	$block_hash=$hash_function($config_params);
	if (in_array($block_hash,array('nocache','runtime_nocache'))) {$is_no_cache=1;} else {$is_no_cache=0;}
	if ($website_ui_data['WEBSITE_CACHING']==2)
	{
		$is_no_cache=1;
	}

	if (trim($temp[4])!='')
	{
		$block_dynamic_params=explode(',',$temp[4]);
		foreach ($block_dynamic_params as $block_dynamic_param)
		{
			if (trim($block_dynamic_param)!='' && trim($_REQUEST[trim($block_dynamic_param)])!='')
			{
				$block_hash='dyn:'.trim($_REQUEST[trim($block_dynamic_param)]).'|'.$block_hash;
			}
		}
	}

	$block_hash="$config[project_url]|$page_id|$object_id|$block_hash";
	if ($config['cache_control_user_status_in_cache']=='true')
	{
		$block_hash=intval($_SESSION['status_id'])."|$block_hash";
	}
	if ($config['project_url_scheme']=="https")
	{
		$block_hash="https|$block_hash";
	}
	if ($config['device']<>"")
	{
		$block_hash="$config[device]|$block_hash";
	}
	if ($config['locale']<>'')
	{
		$block_hash="$config[locale]|$block_hash";
	} elseif ($config['theme_locale']<>'')
	{
		$block_hash="$config[theme_locale]|$block_hash";
	}
	if ($config['relative_post_dates']=="true")
	{
		$relative_post_date=0;
		if ($_SESSION['user_id']>0 && $_SESSION['added_date']<>'')
		{
			$registration_date=strtotime($_SESSION['added_date']);
			$relative_post_date=floor((time()-$registration_date)/86400)+1;
		}
		$block_hash="$relative_post_date|$block_hash";
	}
	$block_hash=md5($block_hash);

	if ($cache_time>0 && !$force_admin_no_cache_blocks && $is_no_cache<>1)
	{
		$smarty->caching=1;
		$smarty->cache_lifetime=$cache_time;

		$has_storage_file = true;
		if ($smarty->is_cached("blocks/$page_id/$object_id.tpl",$block_hash))
		{
			if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"))
			{
				$storage[$object_id]=unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"));
			} else
			{
				$has_storage_file = false;
			}
			if ($has_storage_file)
			{
				if (is_file("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat"))
				{
					@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");
				}
				$res = $smarty->fetch("blocks/$page_id/$object_id.tpl", $block_hash);

				log_performance(microtime(true) - $start_time_block, memory_get_usage() - $start_memory_block, 1, $object_id);
				if ($res == '')
				{
					$use_memcache = 0;
				}

				if ($show_admin_toolbar)
				{
					$res = "<!--start/$page_id/$object_id-->$res<!--end/$page_id/$object_id-->";
				}
				return $res;
			}
		}

		if ($has_storage_file && is_file("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat") &&
				time()-filectime("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat")<100)
		{
			$smarty->cache_lifetime=$cache_time+100;
			if ($smarty->is_cached("blocks/$page_id/$object_id.tpl",$block_hash))
			{
				$block_storage_temp=array();
				if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"))
				{
					$block_storage_temp=@unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"));
				}
				if (is_array($block_storage_temp))
				{
					$storage[$object_id]=$block_storage_temp;
					$res=$smarty->fetch("blocks/$page_id/$object_id.tpl",$block_hash);

					log_performance(microtime(true)-$start_time_block,memory_get_usage()-$start_memory_block,1,$object_id);
					$use_memcache=0;

					if ($show_admin_toolbar)
					{
						$res = "<!--start/$page_id/$object_id-->$res<!--end/$page_id/$object_id-->";
					}
					return $res;
				}
			}
			$smarty->cache_lifetime=$cache_time;

			$iterations=intval($config['overload_block_wait_iterations']);
			if ($iterations==0) {$iterations=5;}
			$wait_time=intval($config['overload_block_wait_time']);
			if ($wait_time==0) {$wait_time=1;}
			if (time()-filectime("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat")<$iterations*5)
			{
				for ($i=0;$i<$iterations;$i++)
				{
					sleep($wait_time);
					if (!is_file("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat"))
					{
						if ($smarty->is_cached("blocks/$page_id/$object_id.tpl",$block_hash))
						{
							if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"))
							{
								$storage[$object_id]=unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"));
							}
							$res=$smarty->fetch("blocks/$page_id/$object_id.tpl",$block_hash);
							log_performance(microtime(true)-$start_time_block,memory_get_usage()-$start_memory_block,1,$object_id);
							$use_memcache=0;

							if ($show_admin_toolbar)
							{
								$res = "<!--start/$page_id/$object_id-->$res<!--end/$page_id/$object_id-->";
							}
							return $res;
						}
						break;
					}
					clearstatcache();
				}
				if ($i==$iterations)
				{
					write_overload_stats(6, "$page_id/$object_id");
					$use_memcache=0; return "";
				}
			}
		}

		if (!is_dir("$config[project_path]/admin/data/engine/blocks_state")) {mkdir("$config[project_path]/admin/data/engine/blocks_state",0777);chmod("$config[project_path]/admin/data/engine/blocks_state",0777);}
		if (!is_dir("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]")) {mkdir("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]",0777);chmod("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]",0777);}
		file_put_contents("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat", '1');
	}

	require_once("$config[project_path]/admin/include/functions.php");
	require_once("$config[project_path]/admin/include/database_selectors.php");

	//overload protection
	if (substr($block_id, 0, 5) == 'list_')
	{
		$overload_min_mysql_processes = intval($website_ui_data['OVERLOAD_MIN_MYSQL_PROCESSES']);
		if ($overload_min_mysql_processes == 0)
		{
			$overload_min_mysql_processes = $config['overload_min_mysql_processes'] ?: 20;
		}
		if (!isset($config['mysql_processes']))
		{
			$result = sql_pr("show processlist");
			$config['mysql_processes'] = mr2rows($result);
			if ($config['mysql_processes'] > $overload_min_mysql_processes)
			{
				$temp = mr2array($result);
				$config['mysql_processes'] = 0;
				foreach ($temp as $res)
				{
					if ($res['Command'] != 'Sleep')
					{
						$config['mysql_processes']++;
					}
				}
			}
		}
		if ($config['mysql_processes'] > $overload_min_mysql_processes)
		{
			if ($cache_time > 0 && !$force_admin_no_cache_blocks && $is_no_cache <> 1)
			{
				$smarty->cache_lifetime = $cache_time * 2;
				if ($smarty->is_cached("blocks/$page_id/$object_id.tpl", $block_hash))
				{
					if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"))
					{
						$storage[$object_id] = unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"));
					} else
					{
						$has_storage_file = false;
					}
					if ($has_storage_file)
					{
						if (is_file("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat"))
						{
							@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");
						}
						$res = $smarty->fetch("blocks/$page_id/$object_id.tpl", $block_hash);

						log_performance(microtime(true) - $start_time_block, memory_get_usage() - $start_memory_block, 1, $object_id);
						$use_memcache = 0;

						if ($show_admin_toolbar)
						{
							$res = "<!--start/$page_id/$object_id-->$res<!--end/$page_id/$object_id-->";
						}
						return $res;
					}
				}
				$smarty->cache_lifetime = $cache_time;
			}
		}

		$overload_max_mysql_processes = intval($website_ui_data['OVERLOAD_MAX_MYSQL_PROCESSES']);
		if ($overload_max_mysql_processes == 0)
		{
			$overload_max_mysql_processes = $config['overload_max_mysql_processes'] ?: 40;
		}
		if ($config['mysql_processes'] > $overload_max_mysql_processes)
		{
			write_overload_stats(4);
			$use_memcache = 0;
			return "";
		}
	}

	include_once("$config[project_path]/admin/include/list_countries.php");

	$smarty->assign("list_countries",$list_countries['name']);
	$smarty->assign("list_countries_codes",$list_countries['code']);
	if (is_array($lang))
	{
		$smarty->assign("lang",$lang);
	}
	$smarty->assign("request_uri",$request_uri);

	if (is_array($recaptcha_data) && $recaptcha_data['site_key'])
	{
		$recaptcha_site_key = $recaptcha_data['site_key'];
		if (is_array($recaptcha_data['aliases']))
		{
			foreach ($recaptcha_data['aliases'] as $recaptcha_alias)
			{
				if (str_replace('www.', '', $_SERVER['HTTP_HOST']) == $recaptcha_alias['domain'])
				{
					$recaptcha_site_key = $recaptcha_alias['site_key'];
					break;
				}
			}
		}
		$smarty->assign("recaptcha_site_key", $recaptcha_site_key);
	}

	foreach ($args as $k=>$v)
	{
		if (strpos($k, 'var_')===0)
		{
			$smarty->assign($k,$v);
		}
	}

	$show_block_function="{$block_id}Show";
	$show_result=$show_block_function($config_params,$object_id);
	if ($show_admin_debug)
	{
		$block_debug = [];
		if ($page_id == '$global')
		{
			$block_debug['is_global'] = 1;
		}
		$block_debug['global_id'] = "blocks/$page_id/$object_id";
		$block_debug['block_id'] = $block_id;
		$block_debug['block_name'] = ucwords(str_replace('_', ' ', $block_name));
		$block_debug['block_name_mod'] = $block_name_dir;
		$block_debug['block_uid'] = $object_id;
		$block_debug['params'] = $config_params;

		if ($show_result == 'status_404')
		{
			$block_debug['status_code'] = '404';
		} elseif (strpos($show_result, 'status_302:') === 0)
		{
			$block_debug['status_code'] = '302 (' . substr($show_result, 11) . ')';
		} elseif (strpos($show_result, 'status_301:') === 0)
		{
			$block_debug['status_code'] = '301 (' . substr($show_result, 11) . ')';
		}
		$block_debug['memory_usage'] = memory_get_usage() - $start_memory_block;
		$block_debug['time_usage'] = microtime(true) - $start_time_block;
		if ($block_debug['status_code'] != '')
		{
			$show_admin_debug_block_data[] = $block_debug;
			require_once "$config[project_path]/admin/website_ui_debug.php";
			die;
		}

		$smarty->assign('block_uid', $object_id);
		if ($page_id == '$global')
		{
			$smarty->assign('is_global', 1);
		}
		$smarty->assign('page_id', $page_id);
		$smarty->fetch("blocks/$page_id/$object_id.tpl");

		$smarty->clear_assign('list_countries');
		$smarty->clear_assign('list_countries_codes');
		$smarty->clear_assign('lang');
		$smarty->clear_assign('storage');
		$smarty->clear_assign('config');
		$smarty->clear_assign('page_id');
		$smarty->clear_assign('request_uri');

		$block_debug['storage'] = $storage[$object_id];
		$block_debug['template_vars'] = $smarty->get_template_vars();
		$block_debug['memory_usage'] = memory_get_usage() - $start_memory_block;
		$block_debug['time_usage'] = microtime(true) - $start_time_block;

		$show_admin_debug_block_data[] = $block_debug;
		return '';
	}
	if ($show_result == 'nocache')
	{
		$use_memcache = 0;
		$smarty->caching = 0;
	} elseif ($show_result == 'status_404')
	{
		ob_end_clean();
		@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");
		http_response_code(404);
		if ($_REQUEST['mode'] == 'async')
		{
			die;
		} elseif (is_file("$config[project_path]/404.html"))
		{
			die(file_get_contents("$config[project_path]/404.html"));
		} else
		{
			die('The requested URL was not found on this server.');
		}
	} elseif (strpos($show_result, 'status_302:') === 0)
	{
		ob_end_clean();
		@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");

		$redirect_url = substr($show_result, 11);
		header("Location: $redirect_url");
		die;
	} elseif (strpos($show_result, 'status_301:') === 0)
	{
		ob_end_clean();
		@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");

		$redirect_url = substr($show_result, 11);
		if (intval($website_ui_data['APPEND_PARAMETERS_FOR_301']) == 1)
		{
			if (strpos($_SERVER['REQUEST_URI'], '?') !== false)
			{
				$params = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1);
				if ($params)
				{
					$redirect_url .= (strpos($redirect_url, '?') !== false ? '&' : '?') . $params;
				}
			}
		}
		header("Location: $redirect_url", true, 301);
		die;
	} elseif ($show_result == 'status_410')
	{
		$use_memcache = 0;
		$smarty->caching = 0;
		http_response_code(410);
	}

	if (!is_dir("$config[project_path]/admin/data/engine/storage")) {mkdir("$config[project_path]/admin/data/engine/storage",0777);chmod("$config[project_path]/admin/data/engine/storage",0777);}
	if (!is_dir("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]")) {mkdir("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]",0777);chmod("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]",0777);}
	if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat")){@chmod("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat",0666);}
	file_put_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat", serialize($storage[$object_id]), LOCK_EX);

	$smarty->assign('block_uid',$object_id);
	if ($page_id=='$global')
	{
		$smarty->assign('is_global',1);
	}
	$smarty->assign('page_id',$page_id);
	$res=$smarty->fetch("blocks/$page_id/$object_id.tpl",$block_hash);
	$smarty->clear_all_assign();
	@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");

	log_performance(microtime(true)-$start_time_block,memory_get_usage()-$start_memory_block,0,$object_id);
	if ($res=='')
	{
		$use_memcache=0;
	}

	if ($show_admin_toolbar)
	{
		$res = "<!--start/$page_id/$object_id-->$res<!--end/$page_id/$object_id-->";
	}
	return $res;
}

function insert_getAdv($args)
{
	$spot_id = trim($args['place_id']);
	return "%KTA:$spot_id:0%";
}

function clean_request_uri()
{
	global $runtime_params;

	if (strpos($_SERVER['REQUEST_URI'], '?') !== false)
	{
		$request_uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
		$request_uri_params = explode("&", substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1));
		foreach ($request_uri_params as $param)
		{
			$param = explode("=", $param, 2);
			[$var, $val] = $param;
			if ($var == 'pqr')
			{
				continue;
			}
			if (is_array($runtime_params))
			{
				foreach ($runtime_params as $param2)
				{
					if ($var == trim($param2['name']))
					{
						continue 2;
					}
				}
			}
			if ($var != '' && $val != '')
			{
				if (strpos($request_uri, '?') !== false)
				{
					$request_uri .= "&$var=$val";
				} else
				{
					$request_uri .= "?$var=$val";
				}
			}
		}
	} else
	{
		$request_uri = $_SERVER['REQUEST_URI'];
	}
	return $request_uri;
}

function configure_locale()
{
	global $config;

	$kt_lang = $_COOKIE['kt_lang'];
	if ($_REQUEST['kt_lang'])
	{
		$kt_lang = $_REQUEST['kt_lang'];
	}
	if ($kt_lang == '' && array_cnt($config['locales']) > 0)
	{
		if (in_array($_SESSION['user_info']['language_code'], $config['locales']))
		{
			$kt_lang = $_SESSION['user_info']['language_code'];
		} else
		{
			$user_locales = array_map('trim', explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
			foreach ($config['locales'] as $supported_locale)
			{
				if (strpos($user_locales[0], $supported_locale) === 0)
				{
					$kt_lang = $supported_locale;
					break;
				}
			}
		}
	}
	if ($kt_lang)
	{
		if (is_array($config['locales']) && in_array($kt_lang, $config['locales']))
		{
			$config['locale'] = $kt_lang;
		}
		if ($_REQUEST['kt_lang'] && $config['locale_set_cookie'] == 'true')
		{
			set_cookie('kt_lang', $kt_lang, time() + 31104000);
		}
	}
}

function validate_recaptcha($code, $recaptcha_data)
{
	global $config;

	if (!$code)
	{
		return false;
	}
	if (is_array($recaptcha_data) && $recaptcha_data['secret_key'])
	{
		$recaptcha_secret_key = $recaptcha_data['secret_key'];
		if (is_array($recaptcha_data['aliases']))
		{
			foreach ($recaptcha_data['aliases'] as $recaptcha_alias)
			{
				if (str_replace('www.', '', $_SERVER['HTTP_HOST']) == $recaptcha_alias['domain'])
				{
					$recaptcha_secret_key = $recaptcha_alias['secret_key'];
					break;
				}
			}
		}
		$ch = curl_init();
		if (intval($recaptcha_data['is_enabled']) == 1)
		{
			$url = 'https://www.google.com/recaptcha/api/siteverify';
		} elseif (intval($recaptcha_data['is_enabled']) == 2)
		{
			$url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
			'secret' => $recaptcha_secret_key,
			'response' => $code,
			'remoteip' => $_SERVER['REMOTE_ADDR']
		));
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$recaptcha_response = curl_exec($ch);
		if (curl_errno($ch) > 0)
		{
			file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt", "[" . date("Y-m-d H:i:s") . "] $url [" . curl_errno($ch) . "] " . curl_error($ch) . "\n", FILE_APPEND | LOCK_EX);
		}
		curl_close($ch);

		if ($recaptcha_response)
		{
			$recaptcha_response = @json_decode($recaptcha_response, true);
			if (is_array($recaptcha_response) && intval($recaptcha_response['success']) == 1)
			{
				return true;
			}
		}
		return false;
	}

	return false;
}

function check_page_access($is_disabled, $access_type_id, $access_type_redirect_url)
{
	global $config;

	if ($is_disabled == 1)
	{
		http_response_code(404);
		if (is_file("$config[project_path]/404.html"))
		{
			die(@file_get_contents("$config[project_path]/404.html"));
		} else
		{
			die("The requested URL was not found on this server.");
		}
	}
	if (($access_type_id == 1 && $_SESSION['user_id'] < 1) || ($access_type_id == 2 && $_SESSION['status_id'] != 3) ||
			($access_type_id == 3 && $_SESSION['status_id'] != 6) || ($access_type_id == 4 && $_SESSION['is_trusted'] != 1))
	{
		if (trim($access_type_redirect_url))
		{
			$_SESSION['private_page_referer'] = $_SERVER['REQUEST_URI'];
			header("Location: $access_type_redirect_url");
			die;
		} else
		{
			http_response_code(403);
			die("Access denied");
		}
	}
}

function replace_runtime_params($page)
{
	global $config, $storage, $runtime_params, $page_config, $show_admin_toolbar;

	$page=trim($page);

	$hotlink_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/hotlink_info.dat"));
	if (intval($hotlink_data['ENABLE_ANTI_HOTLINK'])==1 && intval($hotlink_data['ANTI_HOTLINK_TYPE']) == 1)
	{
		$lock_ips = explode(',', trim($_COOKIE['kt_ips']));
		if (!in_array($_SERVER['REMOTE_ADDR'], $lock_ips))
		{
			$lock_ips[] = $_SERVER['REMOTE_ADDR'];
		}

		set_cookie('kt_ips', trim(implode(',', $lock_ips), ', '), time() + 86400);

		$lock_ip = $_SERVER['REMOTE_ADDR'];
		if (!is_array($_SESSION['lock_ips']) || !isset($_SESSION['lock_ips'][$_SERVER['REMOTE_ADDR']]))
		{
			$_SESSION['lock_ips'][$_SERVER['REMOTE_ADDR']] = 1;
		}
		$pos = strpos($page, '/get_file/');
		if ($pos !== false)
		{
			$result = '';
			$pos2 = 0;
			while ($pos !== false)
			{
				$pos = strpos($page, '/', $pos + 10) + 1;
				$length = strpos($page, '/', $pos + 1) - $pos;
				$token = substr($page, $pos, $length);
				if ($length == 32)
				{
					$token .= substr(md5($token . $config['cv'] . $lock_ip), 0, 10);
				}

				$result .= substr($page, $pos2, $pos - $pos2) . $token;
				$pos2 = $pos + $length;
				$pos = strpos($page, '/get_file/', $pos + 1);
			}
			$result .= substr($page, $pos2);
			$page = $result;
		}
	}

	// advertising
	$pos = strpos($page, '%KTA:');
	if ($pos !== false)
	{
		$now_date = time();
		$now_time = explode(':', date("H:i"));
		$now_time = intval($now_time[0]) * 3600 + intval($now_time[1]) * 60;

		$result = '';
		$pos2 = 0;
		while ($pos !== false)
		{
			$length = strpos($page, '%', $pos + 1) + 1 - $pos;
			$token = substr($page, $pos + 5, $length - 6);
			$spot_id = substr($token, 0, strpos($token, ':'));

			$ads = [];
			$ads_empty = [];

			$spot_info = [];
			$spot_data_file = "$config[project_path]/admin/data/advertisements/spot_$spot_id.dat";
			if (is_file($spot_data_file))
			{
				$spot_info = @unserialize(file_get_contents($spot_data_file), ['allowed_classes' => false]);
			}
			if (is_array($spot_info['ads']))
			{
				foreach ($spot_info['ads'] as $ad_info)
				{
					if ($ad_info['is_active'] == 0)
					{
						continue;
					}
					if (($ad_info['show_from_date'] != '0000-00-00' && strtotime($ad_info['show_from_date']) > $now_date) || ($ad_info['show_to_date'] != '0000-00-00' && strtotime($ad_info['show_to_date']) < $now_date))
					{
						continue;
					}
					if ($ad_info['show_from_time'] > 0 || $ad_info['show_to_time'] > 0)
					{
						if ($now_time < $ad_info['show_from_time'] || $now_time > $ad_info['show_to_time'])
						{
							$skip_ad = true;
							if ($ad_info['show_from_time'] > $ad_info['show_to_time'])
							{
								if (($now_time > $ad_info['show_from_time'] && $now_time < 86400) || $now_time < $ad_info['show_to_time'])
								{
									$skip_ad = false;
								}
							}
							if ($skip_ad)
							{
								continue;
							}
						}
					}
					if (array_cnt($ad_info['devices']) > 0)
					{
						if (!class_exists('Mobile_Detect'))
						{
							include_once "$config[project_path]/admin/include/mobiledetect/Mobile_Detect.php";
						}
						if (class_exists('Mobile_Detect'))
						{
							$mobiledetect = new Mobile_Detect();
							$ad_device_show = false;
							foreach ($ad_info['devices'] as $ad_device)
							{
								if ($ad_device_show)
								{
									break;
								}
								switch ($ad_device)
								{
									case 'pc':
										$ad_device_show = !$mobiledetect->isMobile();
										break;
									case 'tablet':
										$ad_device_show = $mobiledetect->isTablet();
										break;
									case 'phone':
										$ad_device_show = $mobiledetect->isMobile() && !$mobiledetect->isTablet();
										break;
								}
							}
							if (!$ad_device_show)
							{
								continue;
							}
						}
					}
					if (array_cnt($ad_info['browsers']) > 0)
					{
						$current_browser = get_user_agent_code();
						if (!in_array($current_browser, $ad_info['browsers']))
						{
							continue;
						}
					}
					if (array_cnt($ad_info['users']) > 0)
					{
						$ad_user_show = false;
						foreach ($ad_info['users'] as $ad_user)
						{
							if ($ad_user_show)
							{
								break;
							}
							switch ($ad_user)
							{
								case 'guest':
									$ad_user_show = intval($_SESSION['user_id']) < 1;
									break;
								case 'active':
									$ad_user_show = intval($_SESSION['status_id']) == 2;
									break;
								case 'premium':
									$ad_user_show = intval($_SESSION['status_id']) == 3;
									break;
								case 'webmaster':
									$ad_user_show = intval($_SESSION['status_id']) == 6;
									break;
							}
						}
						if (!$ad_user_show)
						{
							continue;
						}
					}

					$countries = explode(',', $ad_info['countries']);
					if (array_cnt($countries) == 0 || (array_cnt($countries) == 1 && $countries[0] == ''))
					{
						$ads_empty[] = $ad_info;
					} else
					{
						foreach ($countries as $country_code)
						{
							if (strtolower(trim($country_code)) == strtolower($_SERVER['GEOIP_COUNTRY_CODE']))
							{
								$ads[] = $ad_info;
								break;
							}
						}
					}
				}
			}

			if (array_cnt($ads) == 0)
			{
				$ads = $ads_empty;
			}

			if (array_cnt($ads) > 0)
			{
				$category_ids_in_context = [];
				foreach ($storage as $storage_info)
				{
					if (is_array($storage_info['category_info']) && intval($storage_info['category_info']['category_id']) > 0)
					{
						$category_ids_in_context[intval($storage_info['category_info']['category_id'])] = intval($storage_info['category_info']['category_id']);
					}
					if (is_array($storage_info['categories']))
					{
						foreach ($storage_info['categories'] as $category_info)
						{
							if (intval($category_info['category_id']) > 0)
							{
								$category_ids_in_context[intval($category_info['category_id'])] = intval($category_info['category_id']);
							}
						}
					}
				}

				if (array_cnt($category_ids_in_context) > 0)
				{
					$has_categorized_ads = false;
					foreach ($ads as $k => $ad)
					{
						if (array_cnt($ad['category_ids']) > 0)
						{
							$should_delete_ad = true;
							foreach ($ad['category_ids'] as $ad_category_id)
							{
								if (isset($category_ids_in_context[$ad_category_id]))
								{
									$has_categorized_ads = true;
									$should_delete_ad = false;
									break;
								}
							}
							if ($should_delete_ad)
							{
								unset($ads[$k]);
							}
						}
					}

					if ($has_categorized_ads)
					{
						foreach ($ads as $k => $ad)
						{
							if (array_cnt($ad['category_ids']) == 0)
							{
								unset($ads[$k]);
							}
						}
					}

					foreach ($ads as $k => $ad)
					{
						if (array_cnt($ad['exclude_category_ids']) > 0)
						{
							$should_delete_ad = false;
							foreach ($ad['exclude_category_ids'] as $ad_category_id)
							{
								if (isset($category_ids_in_context[$ad_category_id]))
								{
									$should_delete_ad = true;
									break;
								}
							}
							if ($should_delete_ad)
							{
								unset($ads[$k]);
							}
						}
					}
				} else
				{
					foreach ($ads as $k => $ad)
					{
						if (array_cnt($ad['category_ids']) > 0)
						{
							unset($ads[$k]);
						}
					}
				}
			}

			if (array_cnt($ads) > 0)
			{
				$ads = array_values($ads);
				$ad_info = $ads[mt_rand(0, array_cnt($ads) - 1)];

				$token = $ad_info['code'];
				$token = str_replace("%URL%", "$config[project_url]/?action=trace&amp;id=$ad_info[advertisement_id]", $token);
				if ($spot_info['template'] != '')
				{
					$token = str_replace("%ADV%", $token, $spot_info['template']);
				}
				if ($show_admin_toolbar)
				{
					$token = "<!--start/spot/$spot_id-->$token<!--end/spot/$spot_id-->";
				}

				if (isset($page_config))
				{
					if (!isset($page_config['ad_spots']))
					{
						$page_config['ad_spots'] = [];
					}
					$page_config['ad_spots'][] = ['spot_id' => $spot_info['external_id'], 'ad_id' => $ad_info['advertisement_id'], 'title' => $ad_info['title']];
				}

				if (intval($spot_info['is_debug_enabled']) == 1)
				{
					$ads_str = '';
					foreach ($ads as $ad)
					{
						$ads_str .= "$ad[advertisement_id], ";
					}
					$ads_str = trim($ads_str, ' ,');
					file_put_contents("$config[project_path]/admin/logs/debug_ad_spot_$spot_id.txt", date("[Y-m-d H:i:s] ") . "Displayed advertising $ad_info[advertisement_id] / \"$ad_info[title]\" from $ads_str for URI: $_SERVER[REQUEST_URI], User: $_SESSION[username], Agent: $_SERVER[HTTP_USER_AGENT], Country: $_SERVER[GEOIP_COUNTRY_CODE]\n", FILE_APPEND | LOCK_EX);
				}
			} else
			{
				$token = '';
				if (intval($spot_info['is_debug_enabled']) == 1)
				{
					file_put_contents("$config[project_path]/admin/logs/debug_ad_spot_$spot_id.txt", date("[Y-m-d H:i:s] ") . "No advertising for URI: $_SERVER[REQUEST_URI], User: $_SESSION[username], Agent: $_SERVER[HTTP_USER_AGENT], Country: $_SERVER[GEOIP_COUNTRY_CODE]\n", FILE_APPEND | LOCK_EX);
				}
			}

			$result .= substr($page, $pos2, $pos - $pos2) . $token;
			$pos2 = $pos + $length;
			$pos = strpos($page, '%KTA:', $pos + 1);
		}
		$result .= substr($page, $pos2);
		$page = $result;
	}

	// advanced advertising
	$pos = strpos($page, '%KTV:');
	if ($pos !== false)
	{
		$result = '';
		$pos2 = 0;
		while ($pos !== false)
		{
			$length = strpos($page, '%', $pos + 1) + 1 - $pos;
			$token = substr($page, $pos + 5, $length - 6);
			$profile_id = $token;

			$ads = [];
			$ads_sorting = [];

			$profile_data_file = "$config[project_path]/admin/data/player/vast/vast_$profile_id.dat";
			$profile_info = null;
			if (is_file($profile_data_file))
			{
				$profile_info = @unserialize(file_get_contents($profile_data_file));
			}

			$seen_ads = [];
			if (is_array($profile_info) && is_array($profile_info['providers']))
			{
				if (trim($_COOKIE["kt_vast_$profile_id"]))
				{
					$seen_ads = explode(',', trim($_COOKIE["kt_vast_$profile_id"]));
				}

				$category_ids_in_context = [];
				foreach ($storage as $storage_info)
				{
					if (is_array($storage_info['categories']))
					{
						foreach ($storage_info['categories'] as $category_info)
						{
							if (intval($category_info['category_id']) > 0)
							{
								$category_ids_in_context[intval($category_info['category_id'])] = intval($category_info['category_id']);
							}
						}
					}
				}

				foreach ($profile_info['providers'] as $provider)
				{
					if (intval($provider['is_enabled']) == 0)
					{
						continue;
					}

					$show_ad_devices = false;
					$show_ad_browsers = false;
					$show_ad_categories = false;
					$show_ad_countries = false;
					$show_ad_referers = false;
					$skip_ad = false;

					if (array_cnt($provider['devices']) == 0)
					{
						$show_ad_devices = true;
					} else
					{
						if (!class_exists('Mobile_Detect'))
						{
							include_once "$config[project_path]/admin/include/mobiledetect/Mobile_Detect.php";
						}
						if (class_exists('Mobile_Detect'))
						{
							$mobiledetect = new Mobile_Detect();
							foreach ($provider['devices'] as $ad_device)
							{
								switch ($ad_device)
								{
									case 'pc':
										$show_ad_devices = !$mobiledetect->isMobile();
										break;
									case 'tablet':
										$show_ad_devices = $mobiledetect->isTablet();
										break;
									case 'phone':
										$show_ad_devices = $mobiledetect->isMobile() && !$mobiledetect->isTablet();
										break;
								}
								if ($show_ad_devices)
								{
									break;
								}
							}
						}
					}

					if (array_cnt($provider['browsers']) == 0)
					{
						$show_ad_browsers = true;
					} else
					{
						$current_browser = get_user_agent_code();
						if (in_array($current_browser, $provider['browsers']))
						{
							$show_ad_browsers = true;
						}
					}

					if (!$provider['categories'])
					{
						$show_ad_categories = true;
					} else
					{
						$categories = explode(',', $provider['categories']);
						foreach ($categories as $category_id)
						{
							if (in_array(trim($category_id), $category_ids_in_context))
							{
								$show_ad_categories = true;
								break;
							}
						}
					}

					if (!$provider['countries'])
					{
						$show_ad_countries = true;
					} else
					{
						$countries = explode(',', $provider['countries']);
						foreach ($countries as $country_code)
						{
							if (strtolower(trim($country_code)) == strtolower($_SERVER['GEOIP_COUNTRY_CODE']))
							{
								$show_ad_countries = true;
								break;
							}
						}
					}

					if (!$provider['referers'])
					{
						$show_ad_referers = true;
					} else
					{
						$referers = array_map('trim', explode("\n", $provider['referers']));
						foreach ($referers as $referer)
						{
							if ($referer)
							{
								if (is_url($referer))
								{
									$referer_host = str_replace('www.', '', trim(parse_url($referer, PHP_URL_HOST)));
									$current_referer_host = str_replace('www.', '', trim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)));
									if (strpos($current_referer_host, $referer_host) === 0)
									{
										$show_ad_referers = true;
										break;
									}
								} elseif (strpos($_SERVER['REQUEST_URI'], $referer) !== false)
								{
									$show_ad_referers = true;
									break;
								}
							}
						}
					}

					if ($provider['exclude_categories'])
					{
						$categories = explode(',', $provider['exclude_categories']);
						foreach ($categories as $category_id)
						{
							if (in_array(trim($category_id), $category_ids_in_context))
							{
								$skip_ad = true;
								break;
							}
						}
					}

					if ($provider['exclude_countries'])
					{
						$countries = explode(',', $provider['exclude_countries']);
						foreach ($countries as $country_code)
						{
							if (strtolower(trim($country_code)) == strtolower($_SERVER['GEOIP_COUNTRY_CODE']))
							{
								$skip_ad = true;
								break;
							}
						}
					}

					if ($provider['exclude_referers'])
					{
						$referers = array_map('trim', explode("\n", $provider['exclude_referers']));
						foreach ($referers as $referer)
						{
							if ($referer)
							{
								if (is_url($referer))
								{
									$referer_host = str_replace('www.', '', trim(parse_url($referer, PHP_URL_HOST)));
									$current_referer_host = str_replace('www.', '', trim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)));
									if (strpos($current_referer_host, $referer_host) === 0)
									{
										$skip_ad = true;
										break;
									}
								} elseif (strpos($_SERVER['REQUEST_URI'], $referer) !== false)
								{
									$skip_ad = true;
									break;
								}
							}
						}
					}

					if ($show_ad_devices && $show_ad_browsers && $show_ad_categories && $show_ad_countries && $show_ad_referers && !$skip_ad)
					{
						$ads[] = $provider;
						$ads_sorting[] = intval($provider['weight']);
					}
				}
			}

			array_multisort($ads_sorting, SORT_NUMERIC, SORT_DESC, $ads);

			$temp_ads = $ads;
			foreach ($temp_ads as $k => $provider)
			{
				if (in_array(md5($provider['url']), $seen_ads))
				{
					unset($temp_ads[$k]);
				}
			}
			if (array_cnt($temp_ads) == 0)
			{
				$temp_ads = $ads;
				$seen_ads = [];
			}

			if (array_cnt($temp_ads) > 0)
			{
				$provider = array_pop(array_reverse($temp_ads));
				$seen_ads[] = md5($provider['url']);

				set_cookie("kt_vast_$profile_id", trim(implode(',', $seen_ads), ', '), time() + 86400);

				$token = $provider['url'];
				if (intval($profile_info['is_debug_enabled']) == 1)
				{
					$seen_ads_count = array_cnt($seen_ads) - 1;
					file_put_contents("$config[project_path]/admin/logs/debug_vast_profile_$profile_id.txt", date("[Y-m-d H:i:s] ") . "Displayed VAST $token after $seen_ads_count displayed ads for URI: $_SERVER[REQUEST_URI], Agent: $_SERVER[HTTP_USER_AGENT], Country: $_SERVER[GEOIP_COUNTRY_CODE]\n", FILE_APPEND | LOCK_EX);
				}
				if ($provider['alt_url'])
				{
					$alternate_vasts = [];
					foreach (array_map('trim', explode("\n", $provider['alt_url'])) as $vast)
					{
						if ($vast)
						{
							$alternate_vasts[] = $vast;
						}
					}
					if (array_cnt($alternate_vasts) > 0)
					{
						$token .= '|' . implode('|', $alternate_vasts);
					}
				}
			} else
			{
				$token = '';
				if (intval($profile_info['is_debug_enabled']) == 1)
				{
					file_put_contents("$config[project_path]/admin/logs/debug_vast_profile_$profile_id.txt", date("[Y-m-d H:i:s] ") . "No VAST for URI: $_SERVER[REQUEST_URI], Agent: $_SERVER[HTTP_USER_AGENT], Country: $_SERVER[GEOIP_COUNTRY_CODE]\n", FILE_APPEND | LOCK_EX);
				}
			}

			$result .= substr($page, $pos2, $pos - $pos2) . $token;
			$pos2 = $pos + $length;
			$pos = strpos($page, '%KTV:', $pos + 1);
		}
		$result .= substr($page, $pos2);
		$page = $result;
	}

	if (is_array($runtime_params))
	{
		foreach ($runtime_params as $param)
		{
			$var=trim($param['name']);
			$val=$_SESSION['runtime_params'][$var];
			if (strlen($val)==0)
			{
				$val=trim($param['default_value']);
			}
			if ($var<>'')
			{
				$val=str_replace("\"","&#34;",$val);
				$val=str_replace(">","&gt;",$val);
				$val=str_replace("<","&lt;",$val);
				$page=str_replace("%$var%",$val,$page);
			}
		}
	}

	if ($config['minify_html'] == 'true')
	{
		$page = preg_replace('/\s+/', ' ', $page);
	}

	if ($config['disable_rotator']<>'true')
	{
		// rotator
		$result='';
		$pos=strpos($page,'%KTR:');
		if ($pos===false)
		{
			return $page;
		}
		$tokens_list=array();
		$pos2=0;
		while ($pos!==false)
		{
			$token=substr($page,$pos,12);
			if (isset($tokens_list[$token]))
			{
				$index=$tokens_list[$token];
			} else {
				$max_index=intval(substr($page,$pos+5,2));
				if ($max_index==0)
				{
					$max_index=1;
				}
				$index=mt_rand(1,$max_index);
				$tokens_list[$token]=$index;
			}
			$result.=substr($page,$pos2,$pos-$pos2).$index;
			$pos2=$pos+12;
			$pos=strpos($page,'%KTR:',$pos+1);
		}
		$result.=substr($page,$pos2);
		return $result;
	}
	return $page;
}

function log_performance($exec_time, $exec_memory, $was_cached, $block_uid)
{
	global $config, $page_id, $page_config, $stats_params, $performance_log_summary;

	if ($exec_memory < 0)
	{
		$exec_memory = 0;
	}

	if (isset($page_config))
	{
		if ($block_uid)
		{
			if ($page_id == '$global')
			{
				if (!isset($page_config['global_blocks_list']))
				{
					$page_config['global_blocks_list'] = [];
				}
				$page_config['global_blocks_list'][] = ['global_uid' => $block_uid, 'exec_stats' => ['was_cached' => $was_cached, 'exec_time' => $exec_time, 'exec_memory' => $exec_memory]];
			} else
			{
				foreach ($page_config['blocks_list'] as &$block)
				{
					if ($block['block_uid'] == $block_uid)
					{
						$block['exec_stats'] = ['was_cached' => $was_cached, 'exec_time' => $exec_time, 'exec_memory' => $exec_memory];
						break;
					}
				}
				unset($block);
			}
		} else
		{
			$page_config['exec_stats'] = ['was_cached' => $was_cached, 'exec_time' => $exec_time, 'exec_memory' => $exec_memory];
		}
	}

	if ($_REQUEST['debug'] == 'true')
	{
		if ($block_uid)
		{
			$performance_log_summary .= "$block_uid " . ($was_cached == 1 ? 'from cache' : 'generated') . " in {$exec_time}s, ";
		} else
		{
			if ($was_cached == 1)
			{
				echo "<!--Page $page_id from cache in {$exec_time}s-->";
			} else
			{
				echo "<!--Page $page_id generated in {$exec_time}s [" . trim($performance_log_summary, ', ') . ']-->';
			}
		}
	}

	if (intval($stats_params['collect_performance_stats']) != 1)
	{
		return;
	}

	if (!is_dir("$config[project_path]/admin/data/analysis/performance"))
	{
		mkdir_recursive("$config[project_path]/admin/data/analysis/performance");
	}

	if ($block_uid)
	{
		$path = "$config[project_path]/admin/data/analysis/performance/{$page_id}_{$block_uid}.dat";
	} else
	{
		$path = "$config[project_path]/admin/data/analysis/performance/$page_id.dat";
	}
	$fp = fopen($path, 'a+');
	if (!$fp)
	{
		return;
	}
	flock($fp, LOCK_EX);
	$performance_log = @unserialize(file_get_contents($path));

	if (!is_array($performance_log))
	{
		$performance_log = [];
		$performance_log['cached_avg_time_s'] = 0;
		$performance_log['cached_requests_count'] = 1;
		$performance_log['uncached_avg_time_s'] = 0;
		$performance_log['uncached_requests_count'] = 1;
		$performance_log['max_memory'] = 0;
	}

	if ($was_cached == 1)
	{
		$performance_log['cached_avg_time_s'] = ($performance_log['cached_avg_time_s'] * $performance_log['cached_requests_count'] + $exec_time) / ($performance_log['cached_requests_count'] + 1);
		$performance_log['cached_requests_count']++;
	} else
	{
		$performance_log['uncached_avg_time_s'] = ($performance_log['uncached_avg_time_s'] * $performance_log['uncached_requests_count'] + $exec_time) / ($performance_log['uncached_requests_count'] + 1);
		$performance_log['uncached_requests_count']++;
	}
	$performance_log['max_memory'] = max($performance_log['max_memory'], $exec_memory);

	ftruncate($fp, 0);
	fwrite($fp, serialize($performance_log));
	flock($fp, LOCK_UN);
	fclose($fp);
}

function write_stats($stats_mode)
{
	global $config, $stats_params, $page_id;

	if (intval($stats_params['collect_traffic_stats']) == 0 || $page_id == 'related_videos_html')
	{
		return;
	}

	$is_uniq = 1;
	if ($stats_mode == 1)
	{
		if ($_COOKIE['kt_is_visited'] == 1)
		{
			$is_uniq = 0;
		}
		set_cookie('kt_is_visited', 1, time() + 86400);
	}

	$incoming_page_params = '';
	if (strpos($_SERVER['HTTP_REFERER'], str_replace("www.", "", $_SERVER['HTTP_HOST'])) === false)
	{
		$referer = $_SERVER['HTTP_REFERER'];
		if (strlen($referer) > 255)
		{
			$referer = substr($referer, 0, 255);
		}
		$incoming_page_params = $_SERVER['QUERY_STRING'];
	} else
	{
		$referer = "";
	}

	if ($referer <> '')
	{
		set_cookie('kt_referer', $referer, time() + 86400);
	} else
	{
		$referer = $_COOKIE['kt_referer'];
	}
	if ($incoming_page_params <> '')
	{
		set_cookie('kt_qparams', $incoming_page_params, time() + 86400);
	} else
	{
		$incoming_page_params = $_COOKIE['kt_qparams'];
	}

	$device_type = 0;
	if (intval($stats_params['collect_traffic_stats_devices']) == 1)
	{
		$device_type = get_device_type();
	}

	file_put_contents("$config[project_path]/admin/data/stats/in.dat", date("Y-m-d") . "|$is_uniq|$_SERVER[GEOIP_COUNTRY_CODE]|$referer|$incoming_page_params|$stats_mode|$device_type\r\n", LOCK_EX | FILE_APPEND);
}

function write_overload_stats($stats_mode, $details = '')
{
	global $config, $stats_params;

	file_put_contents("$config[project_path]/admin/data/stats/overload.dat", date('Y-m-d') . "|$stats_mode\r\n", LOCK_EX | FILE_APPEND);
	if ($details && intval($stats_params['collect_performance_stats']) == 1)
	{
		file_put_contents("$config[project_path]/admin/logs/overload.txt", date("[Y-m-d H:i:s] ") . "[$stats_mode] $details\n", LOCK_EX | FILE_APPEND);
		return;
	}
}

function login_user($user_data,$remember_for_days)
{
	global $config,$database_selectors;

	if (intval($user_data["user_id"]) == 0)
	{
		return;
	}

	require_once("$config[project_path]/admin/include/database_selectors.php");

	if ($user_data["avatar"]!='')
	{
		$user_data["avatar_url"]=$config['content_url_avatars']."/".$user_data['avatar'];
	}
	if ($user_data["cover"]!='')
	{
		$user_data["cover_url"]=$config['content_url_avatars']."/".$user_data['cover'];
	}

	$_SESSION['user_id']=$user_data["user_id"];
	$_SESSION['display_name']=$user_data["display_name"];
	$_SESSION['last_login_date']=$user_data["last_login_date"];
	$_SESSION['added_date']=$user_data["added_date"];
	$_SESSION['avatar']=$user_data["avatar"];
	$_SESSION['avatar_url']=$user_data["avatar_url"];
	$_SESSION['cover']=$user_data["cover"];
	$_SESSION['cover_url']=$user_data["cover_url"];
	$_SESSION['status_id']=$user_data["status_id"];
	$_SESSION['username']=$user_data["username"];
	$_SESSION['content_source_group_id']=$user_data["content_source_group_id"];
	$_SESSION['is_trusted']=$user_data["is_trusted"];
	$_SESSION['tokens_available']=$user_data["tokens_available"];
	$_SESSION['birth_date']=$user_data["birth_date"];
	$_SESSION['gender_id']=$user_data["gender_id"];
	if ($_SESSION['birth_date']!='0000-00-00')
	{
		$age=get_time_passed($_SESSION['birth_date']);
		$_SESSION['age']=$age['value'];
	}

	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	$login_award_tokens=intval($memberzone_data['AWARDS_LOGIN']);
	if ($login_award_tokens>0)
	{
		$login_award_interval=intval($memberzone_data['AWARDS_LOGIN_CONDITION']);
		if ($login_award_interval==0 || mr2number(sql_pr("select count(*) from $config[tables_prefix]log_awards_users where user_id=? and award_type=15 and added_date>DATE_SUB(?, INTERVAL $login_award_interval HOUR)",$_SESSION['user_id'],date("Y-m-d H:i:s")))==0)
		{
			sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=15, user_id=?, tokens_granted=?, added_date=?",$_SESSION['user_id'],$login_award_tokens,date("Y-m-d H:i:s"));
			$_SESSION['tokens_available']=intval($_SESSION['tokens_available'])+$login_award_tokens;
		} else
		{
			$login_award_tokens=0;
		}
	}

	sql_pr("insert into $config[tables_prefix]log_logins_users set is_failed=0, ip=?, full_ip=?, country_code=lower(?), login_date=?, username=?, user_id=?, user_agent=?",ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),date("Y-m-d H:i:s"),$_SESSION['username'],$_SESSION['user_id'],get_user_agent());

	$_SESSION['unread_messages'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]messages where user_id=? and is_hidden_from_user_id=0 and is_read=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=?)", $_SESSION['user_id'], $_SESSION['user_id']));
	$_SESSION['unread_invites'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]messages where user_id=? and is_hidden_from_user_id=0 and type_id=1 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=?)", $_SESSION['user_id'], $_SESSION['user_id']));
	$_SESSION['unread_non_invites'] = $_SESSION['unread_messages'] - $_SESSION['unread_invites'];
	$_SESSION['last_time_get_new_message_amount']=time();

	$_SESSION['content_purchased']=mr2array(sql_pr("select distinct video_id, album_id, profile_id, dvd_id from $config[tables_prefix]users_purchases where user_id=? and expiry_date>?",$_SESSION['user_id'],date("Y-m-d H:i:s")));
	$_SESSION['content_purchased_amount']=array_cnt($_SESSION['content_purchased']);
	$_SESSION['playlists']=mr2array(sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where user_id=? order by title asc",$_SESSION['user_id']));
	$_SESSION['playlists_amount']=array_cnt($_SESSION['playlists']);

	$temp_summary=array();
	$_SESSION['favourite_videos_amount']=0;
	$_SESSION['favourite_videos_summary']=mr2array(sql_pr("select $config[tables_prefix]fav_videos.fav_type, count(*) as amount from $config[tables_prefix]fav_videos inner join $config[tables_prefix]videos on $config[tables_prefix]fav_videos.video_id=$config[tables_prefix]videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.user_id=? group by $config[tables_prefix]fav_videos.fav_type order by $config[tables_prefix]fav_videos.fav_type desc",$_SESSION['user_id']));
	foreach ($_SESSION['favourite_videos_summary'] as $summary_item)
	{
		$temp_summary[$summary_item['fav_type']]=$summary_item;
		$_SESSION['favourite_videos_amount']+=$summary_item['amount'];
	}
	$_SESSION['favourite_videos_summary']=$temp_summary;

	$temp_summary=array();
	$_SESSION['favourite_albums_amount']=0;
	$_SESSION['favourite_albums_summary']=mr2array(sql_pr("select $config[tables_prefix]fav_albums.fav_type, count(*) as amount from $config[tables_prefix]fav_albums inner join $config[tables_prefix]albums on $config[tables_prefix]fav_albums.album_id=$config[tables_prefix]albums.album_id where $database_selectors[where_albums] and $config[tables_prefix]fav_albums.user_id=? group by $config[tables_prefix]fav_albums.fav_type order by $config[tables_prefix]fav_albums.fav_type desc",$_SESSION['user_id']));
	foreach ($_SESSION['favourite_albums_summary'] as $summary_item)
	{
		$temp_summary[$summary_item['fav_type']]=$summary_item;
		$_SESSION['favourite_albums_amount']+=$summary_item['amount'];
	}
	$_SESSION['favourite_albums_summary']=$temp_summary;

	$_SESSION['subscriptions_amount']=mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=?",$_SESSION['user_id']));

	if ($_SESSION['status_id']=='3')
	{
		sql_pr("update $config[tables_prefix]bill_transactions set status_id=1, access_start_date=?, access_end_date=(case when is_unlimited_access=1 then '2070-01-01 00:00:00' else date_add(?, interval duration_rebill day) end), duration_rebill=0, ip=?, country_code=lower(?) where status_id=4 and user_id=?", date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), intval($_SESSION['user_id']));

		$transaction_data=mr2array_single(sql_pr("select (UNIX_TIMESTAMP(access_end_date) - UNIX_TIMESTAMP(?)) / 3600 as hours_left, is_unlimited_access, external_guid, external_package_id, internal_provider_id from $config[tables_prefix]bill_transactions where status_id=1 and user_id=? order by access_end_date desc limit 1",date("Y-m-d H:i:s"),$_SESSION['user_id']));
		$_SESSION['paid_access_hours_left']=intval($transaction_data['hours_left']);
		$_SESSION['paid_access_is_unlimited']=intval($transaction_data['is_unlimited_access']);
		$_SESSION['paid_access_internal_provider_id']=trim($transaction_data['internal_provider_id']);
		$_SESSION['external_guid']=trim($transaction_data['external_guid']);
		$_SESSION['external_package_id']=trim($transaction_data['external_package_id']);
	}

	unset($user_data['pass'], $user_data['pass_bill'], $user_data['temp_pass']);
	$_SESSION['user_info']=$user_data;

	if (strtotime($user_data['last_online_date'])>0 && strtotime($user_data['last_online_date'])>strtotime($user_data['last_login_date']))
	{
		$sess_duration=strtotime($user_data['last_online_date'])-strtotime($user_data['last_login_date']);
		$sess_duration_cnt=intval($user_data['avg_sess_duration_count'])+1;
		$sess_duration=floor((intval($sess_duration)+intval($user_data['avg_sess_duration'])*intval($user_data['avg_sess_duration_count']))/$sess_duration_cnt);
	} else {
		$sess_duration=$user_data['avg_sess_duration'];
		$sess_duration_cnt=$user_data['avg_sess_duration_count'];
	}

	$remember_me_inc='';
	if ($remember_for_days>0)
	{
		$rnd=mt_rand(10000000,99999999);
		$key=md5($config['installation_id'].$_SESSION['user_id'].$rnd);
		$days=intval($remember_for_days);
		$remember_me_inc=", remember_me_key='$key', remember_me_valid_for=DATE_ADD('".date("Y-m-d H:i:s")."', INTERVAL $days DAY) ";

		set_cookie('kt_member', $key, time() + 86400 * $days);
	} elseif (trim($_COOKIE['kt_member']) === '')
	{
		set_cookie('kt_member', 1, time() + 86400);
	}

	sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+?, logins_count=logins_count+1, last_login_date=?, last_online_date=?, last_session_id_hash=?, avg_sess_duration=?, avg_sess_duration_count=? $remember_me_inc where user_id=?",$login_award_tokens,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),md5(session_id()),intval($sess_duration),intval($sess_duration_cnt),$_SESSION['user_id']);
}

function get_block_version($path,$prefix,$id,$dir,$user_id=0)
{
	global $config;

	$version_file=md5("{$prefix}_{$id}_{$user_id}");
	if (!is_file("$config[project_path]/admin/data/engine/$path/$version_file[0]$version_file[1]/$version_file.dat"))
	{
		$version_file=md5("{$prefix}_{$dir}_{$user_id}");
	}
	return intval(@file_get_contents("$config[project_path]/admin/data/engine/$path/$version_file[0]$version_file[1]/$version_file.dat"));
}

function inc_block_version($path,$prefix,$id,$dir,$user_id=0)
{
	global $config;

	$version=get_block_version($path,$prefix,$id,$dir,$user_id)+1;

	if (!is_dir("$config[project_path]/admin/data/engine/$path")) {mkdir("$config[project_path]/admin/data/engine/$path",0777);chmod("$config[project_path]/admin/data/engine/$path",0777);}
	if (intval($id)>0)
	{
		$version_file1=md5("{$prefix}_{$id}_{$user_id}");
		if (!is_dir("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]")) {mkdir("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]",0777);chmod("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]",0777);}
		file_put_contents("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]/$version_file1.dat","$version",LOCK_EX);
	}
	if ($dir!='')
	{
		$version_file2=md5("{$prefix}_{$dir}_{$user_id}");
		if (!is_dir("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]")) {mkdir("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]",0777);chmod("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]",0777);}
		file_put_contents("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]/$version_file2.dat","$version",LOCK_EX);
	}
}

function async_set_request_content_type()
{
	if ($_REQUEST['format']=='json')
	{
		header('Content-type: application/json; charset=utf-8');
	} else {
		header('Content-type: text/xml; charset=utf-8');
	}
}

function async_return_request_status($errors = null, $redirect = null, $success_data = null)
{
	global $lang, $plugin_extensions;

	async_set_request_content_type();

	if (!isset($errors) || array_cnt($errors)==0)
	{
		foreach ($plugin_extensions as $plugin_extension)
		{
			$plugin_function = "{$plugin_extension}PostAsyncRequest";
			if (function_exists($plugin_function))
			{
				$plugin_function();
			}
		}

		if ($_REQUEST['format']=='json')
		{
			$json=array('status'=>'success');
			if ($redirect)
			{
				$json['redirect']=$redirect;
			}
			if (is_array($success_data))
			{
				$json['data']=$success_data;
			}
			echo json_encode($json);
		} else {
			if (is_array($success_data))
			{
				echo "<success>";
				foreach ($success_data as $k=>$v)
				{
					echo "<$k>$v</$k>";
				}
				echo "</success>";
			} else {
				echo '<success/>';
			}
		}
	} else {
		if ($redirect)
		{
			$xml="<failure redirect=\"$redirect\">";
		} else {
			$xml='<failure>';
		}
		$json=array('status'=>'failure');
		foreach ($errors as $error)
		{
			$json_error=array('code'=>$error['error_code']);
			$xml.="<error type=\"$error[error_code]\"";
			if ($error['error_field_name']!='')
			{
				$json_error['field']=$error['error_field_name'];
				$xml.=" field=\"$error[error_field_name]\"";
			}
			if ($error['block']!='')
			{
				$json_error['block']=$error['block'];
				$xml.=" block=\"$error[block]\"";
			}
			if (is_array($error['error_details']) && array_cnt($error['error_details'])>0)
			{
				$json_error['details']=$error['error_details'];
			}
			if ($error['error_field_code']!='')
			{
				$xml.=">$error[error_field_code]</error>";
			} else {
				$xml.="/>";
			}

			if (isset($lang))
			{
				$error_code=$error['error_code'];
				if ($error['error_field_name']!='')
				{
					$error_code=$error['error_field_name']."_".$error['error_code'];
				}
				$error_text='';
				if ($error['message']!='')
				{
					$error_text=$error['message'];
				}
				if ($error_text=='' && $error['block']!='')
				{
					$error_text=$lang['validation'][$error['block']][$error_code];
				}
				if ($error_text=='')
				{
					$error_text=$lang['validation']['common'][$error_code];
				}
				if ($error_text=='')
				{
					$error_text=$lang['validation']['common'][$error['error_code']];
				}
				if ($error_text=='')
				{
					$error_text=str_replace("%1%",$error['error_code'],$lang["validation"]["common"]["unknown_error"]);
				}
				if ($error_text!='')
				{
					if (is_array($error['error_details']) && array_cnt($error['error_details'])>0)
					{
						for ($i=1;$i<=array_cnt($error['error_details']);$i++)
						{
							$error_text=str_replace("%$i%",$error['error_details'][$i-1],$error_text);
						}
					}
					$json_error['message']=$error_text;
				}
			}

			$json['errors'][]=$json_error;
		}
		$xml.='</failure>';

		if ($_REQUEST['format']=='json')
		{
			echo json_encode($json);
		} else {
			echo $xml;
		}
	}
	die;
}
