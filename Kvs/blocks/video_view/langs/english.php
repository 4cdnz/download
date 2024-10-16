<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// video_view messages
// =====================================================================================================================

$lang['video_view']['groups']['object_context']     = $lang['website_ui']['block_group_default_context_object'];
$lang['video_view']['groups']['additional_data']    = $lang['website_ui']['block_group_default_additional_data'];
$lang['video_view']['groups']['limit_views']        = "Limit the number of watched videos from a single IP";

$lang['video_view']['params']['var_video_dir']                  = "URL parameter, which provides video directory.";
$lang['video_view']['params']['var_video_id']                   = "URL parameter, which provides video ID. Will be used instead of video directory if specified and is not empty.";
$lang['video_view']['params']['show_next_and_previous_info']    = "Enables block to load data of both next and previous videos based on the given criteria.";
$lang['video_view']['params']['show_stats']                     = "Enables block to load traffic stats for the displayed video (daily views).";
$lang['video_view']['params']['limit_unknown_user']             = "Enables limit for unregistered users. Configures the maximum number of videos (the first number) that can be watched during time period in seconds (the second number, max 86400 e.g. 1 day).";
$lang['video_view']['params']['limit_member']                   = "Enables limit for standard members. Configures the maximum number of videos (the first number) that can be watched during time period in seconds (the second number, max 86400 e.g. 1 day).";
$lang['video_view']['params']['limit_premium_member']           = "Enables limit for premium members. Configures the maximum number of videos (the first number) that can be watched during time period in seconds (the second number, max 86400 e.g. 1 day).";
$lang['video_view']['params']['limit_ignore_seo_bots']          = "Do not limit SE bots when limiting other users.";

$lang['video_view']['values']['show_next_and_previous_info']['0']   = "By publishing date";
$lang['video_view']['values']['show_next_and_previous_info']['1']   = "By DVD";
$lang['video_view']['values']['show_next_and_previous_info']['2']   = "By content source";
$lang['video_view']['values']['show_next_and_previous_info']['3']   = "By user";

$lang['video_view']['block_short_desc'] = "Displays data of a single video";

$lang['video_view']['block_desc'] = "
	Block displays data of the given video (context object) and provides the following functionality:
	[kt|br][kt|br]

	- Access video player configured via player settings.[kt|br]
	- Rate video once from a single IP.[kt|br]
	- Flag video once from a single IP.[kt|br]
	- Add video to memberzone favourites or playlist (only for members).[kt|br]
	- Delete video from memberzone favourites or playlist (only for members).[kt|br]
	- Purchase premium access to the video using KVS tokens (only for members).[kt|br]
	- Create playlist on-the-fly (only for members).[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['video_view']['groups']['limit_views']}[/kt|b]
	[kt|br][kt|br]

	You can use these options to limit the number of videos that can be watched by a single IP for any given period
	(e.g. for 1 hour, for 4 hours, up to 24 hours). Enabling this functionality will increase database load.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['video_view']['block_examples'] = "
	[kt|b]Display video with directory value 'my_video'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_video_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_video
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display video with ID '198'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_video_dir = dir[kt|br]
	- var_video_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=198
	[/kt|code]
";
