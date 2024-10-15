<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['template_cache_cleanup']['title']         = "Template Cache Cleanup";
$lang['plugins']['template_cache_cleanup']['description']   = "Removes obsolete file cache entries.";
$lang['plugins']['template_cache_cleanup']['long_desc']     = "
		The plugin is designed to automatically remove junk cache items that are not needed anymore by your site. It 
		will automatically detect which items are junk and can be removed without affecting your site operation. The 
		recommended execution interval is 24-48 hours and it is better to configure execution during time of the day 
		when your traffic typically goes down. The plugin will keep its best to not produce high load in your disks, 
		thus for projects with huge number of content it can be running 8+ hours, which is absolutely normal.
		[kt|br][kt|br]
		[kt|b]IMPORTANT[/kt|b]: you should not try using this plugin to reset file cache, as it won't do that. It will 
		delete only cache that KVS thinks is obsolete and won't reset all cache. To completely reset cache please use
		sidebar options in Website UI section.
";
$lang['permissions']['plugins|template_cache_cleanup']      = $lang['plugins']['template_cache_cleanup']['title'];

$lang['plugins']['template_cache_cleanup']['message_calculating']                           = "Calculating size of %1%";

$lang['plugins']['template_cache_cleanup']['field_cache_folder']            = "Template cache folder";
$lang['plugins']['template_cache_cleanup']['field_cache_size']              = "Template cache size";
$lang['plugins']['template_cache_cleanup']['field_storage_folder']          = "\$storage cache folder";
$lang['plugins']['template_cache_cleanup']['field_storage_size']            = "\$storage cache size";
$lang['plugins']['template_cache_cleanup']['field_size_check']              = "N/A";
$lang['plugins']['template_cache_cleanup']['field_size_files']              = "file(s)";
$lang['plugins']['template_cache_cleanup']['field_speed']                   = "Cleanup speed";
$lang['plugins']['template_cache_cleanup']['field_speed_veryslow']          = "Very slow";
$lang['plugins']['template_cache_cleanup']['field_speed_slow']              = "Slow";
$lang['plugins']['template_cache_cleanup']['field_speed_normal']            = "Normal";
$lang['plugins']['template_cache_cleanup']['field_speed_fast']              = "Fast";
$lang['plugins']['template_cache_cleanup']['field_speed_ultrafast']         = "Ultra fast";
$lang['plugins']['template_cache_cleanup']['field_speed_hint']              = "slower speed means lower disk usage and produces lower server load";
$lang['plugins']['template_cache_cleanup']['field_enable']                  = "Enable schedule";
$lang['plugins']['template_cache_cleanup']['field_enable_enabled']          = "enabled";
$lang['plugins']['template_cache_cleanup']['field_schedule']                = "Schedule";
$lang['plugins']['template_cache_cleanup']['field_schedule_interval']       = "min interval (h)";
$lang['plugins']['template_cache_cleanup']['field_schedule_tod']            = "time of day";
$lang['plugins']['template_cache_cleanup']['field_schedule_tod_any']        = "any, as soon as possible";
$lang['plugins']['template_cache_cleanup']['field_schedule_hint']           = "specify minimum interval for this plugin execution and specific time of day if needed";
$lang['plugins']['template_cache_cleanup']['field_last_exec']               = "Last executed";
$lang['plugins']['template_cache_cleanup']['field_last_exec_none']          = "none";
$lang['plugins']['template_cache_cleanup']['field_last_exec_data']          = "%1% seconds, %2% files removed";
$lang['plugins']['template_cache_cleanup']['field_next_exec']               = "Next execution";
$lang['plugins']['template_cache_cleanup']['field_next_exec_none']          = "none";
$lang['plugins']['template_cache_cleanup']['field_next_exec_running']       = "running now...";
$lang['plugins']['template_cache_cleanup']['btn_save']                      = "Save";
$lang['plugins']['template_cache_cleanup']['btn_calculate_stats']           = "Check cache size";
$lang['plugins']['template_cache_cleanup']['btn_start_now']                 = "Clean-up now";
