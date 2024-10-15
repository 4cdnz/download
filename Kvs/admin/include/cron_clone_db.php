<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'setup.php';
require_once 'functions_base.php';
require_once 'functions_servers.php';
require_once 'functions.php';

if ($_SERVER['DOCUMENT_ROOT'] != '')
{
	// under web
	start_session();
	if ($_SESSION['userdata']['user_id'] < 1)
	{
		http_response_code(403);
		die('Access denied');
	}
	header('Content-Type: text/plain; charset=utf-8');
}

KvsContext::init(KvsContext::CONTEXT_TYPE_CRON, 0);
if (!KvsUtilities::try_exclusive_lock('admin/data/system/cron_clone_db'))
{
	die('Already locked');
}

if ($config['is_clone_db'] != 'true')
{
	die('Only for satellite');
}

$start_time = time();

ini_set('display_errors', 1);

$options = get_options();

$memory_limit = $options['LIMIT_MEMORY'];
if ($memory_limit == 0)
{
	$memory_limit = 512;
}
ini_set('memory_limit', "{$memory_limit}M");

log_output('INFO  Satellite processor started');
log_output('INFO  Memory limit: ' . ini_get('memory_limit'));

// update info about satellites
if (sql_update("update $config[tables_prefix]admin_satellites set project_url=? where multi_prefix=?", $config['project_url'], $config['tables_prefix_multi']) == 0)
{
	sql_insert("insert into $config[tables_prefix]admin_satellites set multi_prefix=?, project_url=?, state_id=?, added_date=?", $config['tables_prefix_multi'], $config['project_url'], 0, date('Y-m-d H:i:s'));
}

$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
if (is_array($website_ui_data))
{
	if ($config['locale'] != '')
	{
		$website_ui_data['locale'] = $config['locale'];
	}
	sql_update("update $config[tables_prefix]admin_satellites set website_ui_data=?, project_url=?, last_ping_date=? where multi_prefix=?", serialize($website_ui_data), $config['project_url'], date('Y-m-d H:i:s'), $config['tables_prefix_multi']);
}

// check if file upload params are changed
$old_value = @file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat");
$file_upload_params = [];
$file_upload_params['FILE_UPLOAD_DISK_OPTION'] = $options['FILE_UPLOAD_DISK_OPTION'];
$file_upload_params['FILE_UPLOAD_URL_OPTION'] = $options['FILE_UPLOAD_URL_OPTION'];
$file_upload_params['FILE_UPLOAD_SIZE_LIMIT'] = intval($options['FILE_UPLOAD_SIZE_LIMIT']);
$file_upload_params['FILE_DOWNLOAD_SPEED_LIMIT'] = intval($options['FILE_DOWNLOAD_SPEED_LIMIT']);
$new_value = serialize($file_upload_params);

if ($old_value != $new_value)
{
	log_output('');
	log_output('File upload settings were changed');
	file_put_contents("$config[project_path]/admin/data/system/file_upload_params.dat", $new_value, LOCK_EX);
}

// check if hotlink params are changed
$old_value = @file_get_contents("$config[project_path]/admin/data/system/hotlink_info.dat");
$anti_hotlink_params = [];
$anti_hotlink_params['ENABLE_ANTI_HOTLINK'] = intval($options['ENABLE_ANTI_HOTLINK']);
$anti_hotlink_params['ANTI_HOTLINK_ENABLE_IP_LIMIT'] = intval($options['ANTI_HOTLINK_ENABLE_IP_LIMIT']);
$anti_hotlink_params['ANTI_HOTLINK_TYPE'] = intval($options['ANTI_HOTLINK_TYPE']);
$anti_hotlink_params['ANTI_HOTLINK_ENCODE_LINKS'] = intval($options['ANTI_HOTLINK_ENCODE_LINKS']);
$anti_hotlink_params['ANTI_HOTLINK_FILE'] = $options['ANTI_HOTLINK_FILE'];
$anti_hotlink_params['ANTI_HOTLINK_WHITE_DOMAINS'] = $options['ANTI_HOTLINK_WHITE_DOMAINS'];
$anti_hotlink_params['ANTI_HOTLINK_WHITE_IPS'] = $options['ANTI_HOTLINK_WHITE_IPS'];
$new_value = serialize($anti_hotlink_params);

if ($old_value != $new_value)
{
	log_output('');
	log_output('Hotlink protection settings were changed');
	file_put_contents("$config[project_path]/admin/data/system/hotlink_info.dat", $new_value, LOCK_EX);
}

// rotator is disabled
$rotator_params = [];
$rotator_params['ROTATOR_VIDEOS_ENABLE'] = 0;

file_put_contents("$config[project_path]/admin/data/system/rotator.dat", serialize($rotator_params), LOCK_EX);

// check if api params are changed
$api_params = [];
$api_params['API_ENABLE'] = intval($options['API_ENABLE']);
$api_params['API_PASSWORD'] = $options['API_PASSWORD'];

$old_value = @file_get_contents("$config[project_path]/admin/data/system/api.dat");
$new_value = serialize($api_params);

if ($old_value != $new_value)
{
	log_output('');
	log_output('API settings were changed');
	file_put_contents("$config[project_path]/admin/data/system/api.dat", $new_value, LOCK_EX);
}

// check if mixed params are changed
$mixed_params = [];
$mixed_params['ALBUMS_SOURCE_FILES_ACCESS_LEVEL'] = intval($options['ALBUMS_SOURCE_FILES_ACCESS_LEVEL']);

$old_value = @file_get_contents("$config[project_path]/admin/data/system/mixed_options.dat");
$new_value = serialize($mixed_params);

if ($old_value != $new_value)
{
	log_output('');
	log_output('Mixed settings were changed');
	file_put_contents("$config[project_path]/admin/data/system/mixed_options.dat", $new_value, LOCK_EX);
}

// check if memberzone params are changed
$awards = [
		'AWARDS_SIGNUP' => $lang['settings']['memberzone_awards_col_action_signup'],
		'AWARDS_AVATAR' => $lang['settings']['memberzone_awards_col_action_avatar'],
		'AWARDS_COVER' => $lang['settings']['memberzone_awards_col_action_cover'],
		'AWARDS_LOGIN' => $lang['settings']['memberzone_awards_col_action_login'],
		'AWARDS_COMMENT_VIDEO' => $lang['settings']['memberzone_awards_col_action_comment_video'],
		'AWARDS_COMMENT_ALBUM' => $lang['settings']['memberzone_awards_col_action_comment_album'],
		'AWARDS_COMMENT_CS' => $lang['settings']['memberzone_awards_col_action_comment_content_source'],
		'AWARDS_COMMENT_MODEL' => $lang['settings']['memberzone_awards_col_action_comment_model'],
		'AWARDS_COMMENT_DVD' => $lang['settings']['memberzone_awards_col_action_comment_dvd'],
		'AWARDS_COMMENT_POST' => $lang['settings']['memberzone_awards_col_action_comment_post'],
		'AWARDS_COMMENT_PLAYLIST' => $lang['settings']['memberzone_awards_col_action_comment_playlist'],
		'AWARDS_VIDEO_UPLOAD' => $lang['settings']['memberzone_awards_col_action_video_upload'],
		'AWARDS_ALBUM_UPLOAD' => $lang['settings']['memberzone_awards_col_action_album_upload'],
		'AWARDS_POST_UPLOAD' => $lang['settings']['memberzone_awards_col_action_post_upload'],
		'AWARDS_REFERRAL_SIGNUP' => $lang['settings']['memberzone_awards_col_action_referral_signup'],
];
$memberzone_params = [];
$memberzone_params['STATUS_AFTER_PREMIUM'] = $options['STATUS_AFTER_PREMIUM'];
$memberzone_params['PUBLIC_VIDEOS_ACCESS'] = intval($options['PUBLIC_VIDEOS_ACCESS']);
$memberzone_params['PRIVATE_VIDEOS_ACCESS'] = intval($options['PRIVATE_VIDEOS_ACCESS']);
$memberzone_params['PREMIUM_VIDEOS_ACCESS'] = intval($options['PREMIUM_VIDEOS_ACCESS']);
$memberzone_params['PUBLIC_ALBUMS_ACCESS'] = intval($options['PUBLIC_ALBUMS_ACCESS']);
$memberzone_params['PRIVATE_ALBUMS_ACCESS'] = intval($options['PRIVATE_ALBUMS_ACCESS']);
$memberzone_params['PREMIUM_ALBUMS_ACCESS'] = intval($options['PREMIUM_ALBUMS_ACCESS']);
$memberzone_params['AFFILIATE_PARAM_NAME'] = trim($options['AFFILIATE_PARAM_NAME']);
$memberzone_params['GENERATED_USERS_REUSE_PROBABILITY'] = intval($options['GENERATED_USERS_REUSE_PROBABILITY']);
$memberzone_params['ENABLE_TOKENS_PUBLIC_VIDEO'] = intval($options['ENABLE_TOKENS_PUBLIC_VIDEO']);
$memberzone_params['ENABLE_TOKENS_PRIVATE_VIDEO'] = intval($options['ENABLE_TOKENS_PRIVATE_VIDEO']);
$memberzone_params['ENABLE_TOKENS_PREMIUM_VIDEO'] = intval($options['ENABLE_TOKENS_PREMIUM_VIDEO']);
$memberzone_params['ENABLE_TOKENS_PUBLIC_ALBUM'] = intval($options['ENABLE_TOKENS_PUBLIC_ALBUM']);
$memberzone_params['ENABLE_TOKENS_PRIVATE_ALBUM'] = intval($options['ENABLE_TOKENS_PRIVATE_ALBUM']);
$memberzone_params['ENABLE_TOKENS_PREMIUM_ALBUM'] = intval($options['ENABLE_TOKENS_PREMIUM_ALBUM']);
$memberzone_params['DEFAULT_TOKENS_PUBLIC_VIDEO'] = intval($options['DEFAULT_TOKENS_PUBLIC_VIDEO']);
$memberzone_params['DEFAULT_TOKENS_PRIVATE_VIDEO'] = intval($options['DEFAULT_TOKENS_PRIVATE_VIDEO']);
$memberzone_params['DEFAULT_TOKENS_PREMIUM_VIDEO'] = intval($options['DEFAULT_TOKENS_PREMIUM_VIDEO']);
$memberzone_params['DEFAULT_TOKENS_PUBLIC_ALBUM'] = intval($options['DEFAULT_TOKENS_PUBLIC_ALBUM']);
$memberzone_params['DEFAULT_TOKENS_PRIVATE_ALBUM'] = intval($options['DEFAULT_TOKENS_PRIVATE_ALBUM']);
$memberzone_params['DEFAULT_TOKENS_PREMIUM_ALBUM'] = intval($options['DEFAULT_TOKENS_PREMIUM_ALBUM']);
$memberzone_params['TOKENS_PURCHASE_EXPIRY'] = $options['TOKENS_PURCHASE_EXPIRY'];
$memberzone_params['ENABLE_TOKENS_SUBSCRIBE_MEMBERS'] = intval($options['ENABLE_TOKENS_SUBSCRIBE_MEMBERS']);
$memberzone_params['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE'] = intval($options['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']);
$memberzone_params['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD'] = $options['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD'];
$memberzone_params['ENABLE_TOKENS_SUBSCRIBE_DVDS'] = intval($options['ENABLE_TOKENS_SUBSCRIBE_DVDS']);
$memberzone_params['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE'] = intval($options['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']);
$memberzone_params['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD'] = $options['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD'];
$memberzone_params['ENABLE_TOKENS_SALE_VIDEOS'] = intval($options['ENABLE_TOKENS_SALE_VIDEOS']);
$memberzone_params['ENABLE_TOKENS_SALE_ALBUMS'] = intval($options['ENABLE_TOKENS_SALE_ALBUMS']);
$memberzone_params['ENABLE_TOKENS_SALE_MEMBERS'] = intval($options['ENABLE_TOKENS_SALE_MEMBERS']);
$memberzone_params['ENABLE_TOKENS_SALE_DVDS'] = intval($options['ENABLE_TOKENS_SALE_DVDS']);
$memberzone_params['TOKENS_SALE_INTEREST'] = min(100, intval($options['TOKENS_SALE_INTEREST']));
$memberzone_params['TOKENS_SALE_EXCLUDES'] = $options['TOKENS_SALE_EXCLUDES'];
$memberzone_params['ENABLE_TOKENS_DONATIONS'] = intval($options['ENABLE_TOKENS_DONATIONS']);
$memberzone_params['TOKENS_DONATION_MIN'] = intval($options['TOKENS_DONATION_MIN']);
$memberzone_params['TOKENS_DONATION_INTEREST'] = min(100, intval($options['TOKENS_DONATION_INTEREST']));
$memberzone_params['ENABLE_TOKENS_MESSAGES_ACTIVE']=intval($options['ENABLE_TOKENS_MESSAGES_ACTIVE']);
$memberzone_params['TOKENS_MESSAGES_ACTIVE']=intval($options['TOKENS_MESSAGES_ACTIVE']);
$memberzone_params['ENABLE_TOKENS_MESSAGES_PREMIUM']=intval($options['ENABLE_TOKENS_MESSAGES_PREMIUM']);
$memberzone_params['TOKENS_MESSAGES_PREMIUM']=intval($options['TOKENS_MESSAGES_PREMIUM']);
$memberzone_params['ENABLE_TOKENS_MESSAGES_WEBMASTERS']=intval($options['ENABLE_TOKENS_MESSAGES_WEBMASTERS']);
$memberzone_params['TOKENS_MESSAGES_WEBMASTERS']=intval($options['TOKENS_MESSAGES_WEBMASTERS']);
$memberzone_params['ENABLE_TOKENS_MESSAGES_REVENUE']=intval($options['ENABLE_TOKENS_MESSAGES_REVENUE']);
$memberzone_params['TOKENS_MESSAGES_REVENUE_INTEREST']=intval($options['TOKENS_MESSAGES_REVENUE_INTEREST']);
foreach ($awards as $award_id => $field_name)
{
	$memberzone_params["{$award_id}_CONDITION"] = $options["{$award_id}_CONDITION"];
	$memberzone_params["{$award_id}"] = $options["{$award_id}"];
}

$old_value = @file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat");
$new_value = serialize($memberzone_params);

if ($old_value != $new_value)
{
	log_output('');
	log_output('Memberzone settings were changed');
	file_put_contents("$config[project_path]/admin/data/system/memberzone_params.dat", $new_value, LOCK_EX);
}

// check if formats videos are changed
$data = mr2array(sql_pr("select title,postfix,format_video_group_id,access_level_id,is_hotlink_protection_disabled,is_download_enabled,download_order,limit_speed_option,limit_speed_value,limit_speed_guests_option,limit_speed_guests_value,limit_speed_standard_option,limit_speed_standard_value,limit_speed_premium_option,limit_speed_premium_value,limit_speed_embed_option,limit_speed_embed_value,limit_speed_countries,timeline_directory,limit_total_duration,limit_number_parts from $config[tables_prefix]formats_videos order by format_video_id asc"));
if (array_cnt($data) > 0)
{
	$old_value = @file_get_contents("$config[project_path]/admin/data/system/formats_videos.dat");
	$new_value = serialize($data);

	if ($old_value != $new_value)
	{
		log_output('');
		log_output('Video format settings were changed');
		file_put_contents("$config[project_path]/admin/data/system/formats_videos.dat", $new_value, LOCK_EX);
	}
}

// check if formats albums are changed
$data = mr2array(sql_pr("select format_album_id, group_id, size, access_level_id, is_create_zip from $config[tables_prefix]formats_albums order by format_album_id asc"));
if (array_cnt($data) > 0)
{
	$old_value = @file_get_contents("$config[project_path]/admin/data/system/formats_albums.dat");
	$new_value = serialize($data);

	if ($old_value != $new_value)
	{
		log_output('');
		log_output('Album format settings were changed');
		file_put_contents("$config[project_path]/admin/data/system/formats_albums.dat", $new_value, LOCK_EX);
	}
}

// update servers data
update_cluster_data();

// update languages data
$data = mr2array(sql_pr("select `code`, url from $config[tables_prefix]languages"));
if (sql_error_code() == 0)
{
	$old_value = @file_get_contents("$config[project_path]/admin/data/system/languages.dat");
	$new_value = json_encode($data);

	if ($old_value != $new_value)
	{
		log_output('');
		log_output('Language settings were changed');
		file_put_contents("$config[project_path]/admin/data/system/languages.dat", $new_value, LOCK_EX);
	}
}

sql_update("update $config[tables_prefix_multi]admin_processes set last_exec_date=?, last_exec_duration=?, status_data=? where pid='cron_clone_db'", date('Y-m-d H:i:s', $start_time), time() - $start_time, serialize([]));

log_output('');
log_output('INFO  Finished');

function log_output($message)
{
	if ($message == '')
	{
		echo "\n";
	} else
	{
		echo date("[Y-m-d H:i:s] ") . $message . "\n";
	}
}
