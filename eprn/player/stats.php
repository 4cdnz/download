<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

$event_str = trim($_REQUEST['event']);
if ($event_str)
{
	require_once '../admin/include/setup.php';
	require_once '../admin/include/functions_base.php';

	$device_type = intval($_REQUEST['device_type']);
	if ($event_str == 'FirstPlay')
	{
		$video_id = intval($_REQUEST['video_id']);
		if ($video_id > 0)
		{
			$is_seo_bot = intval(KvsUtilities::is_seo_bot());
			file_put_contents("$config[project_path]/admin/data/stats/video_plays.dat", date('Y-m-d H:i:s') . "|$video_id|$_SERVER[GEOIP_COUNTRY_CODE]|$_COOKIE[kt_referer]|$_COOKIE[kt_qparams]|$device_type|$is_seo_bot\n", FILE_APPEND | LOCK_EX);
		}
	} elseif (!KvsUtilities::is_seo_bot())
	{
		$is_embed = 0;
		$embed_profile_id = '';
		if (trim($_REQUEST['embed']) == 'true' || trim($_REQUEST['embed']) == '1')
		{
			$is_embed = 1;
			$embed_profile_id = trim($_REQUEST['embed_profile_id']);
		}
		$event_str = str_replace(',', '|', $event_str);

		file_put_contents("$config[project_path]/admin/data/stats/player.dat", date("Y-m-d") . "|$is_embed|$event_str|$_SERVER[GEOIP_COUNTRY_CODE]|$_COOKIE[kt_referer]|$_COOKIE[kt_qparams]|$device_type|$embed_profile_id\n", FILE_APPEND | LOCK_EX);
	}
}

header("Content-type: image/gif");
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');