<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['neuroscore']['title']          = "Neuroscore";
$lang['plugins']['neuroscore']['description']    = "Integrates AI technology into KVS.";
$lang['plugins']['neuroscore']['long_desc']      = "
		Neuroscore provides API for using neural networks for different classification tasks. Create an account at 
		https://neuroscore.ai and specify your API key to get benefits from using modern AI technologies in your 
		project.
		[kt|br][kt|br]
		This plugin works asyncronously. It submits tasks to Neuroscore and checks if Neuroscore has processed them 
		every 5 minutes. Video data is updated only after Neuroscore has fully completed task execution.
";
$lang['permissions']['plugins|neuroscore']   = $lang['plugins']['neuroscore']['title'];

$lang['plugins']['neuroscore']['divider_general']                           = "Neuroscore API settings";
$lang['plugins']['neuroscore']['divider_score']                             = "Screenshot scoring";
$lang['plugins']['neuroscore']['divider_score_hint']                        = "Uses neural network to identify best screenshots. Typical usage scenario is to auto-create 10-20 screenshots in KVS and send them for analysis to Neuroscore to identify the best one, or several best screenshots that will further be rotated by KVS screenshot rotator to identify the most clickable screenshot that will be set as main.";
$lang['plugins']['neuroscore']['divider_title']                             = "Title rewrite";
$lang['plugins']['neuroscore']['divider_title_hint']                        = "Uses neural network to create new titles from existing titles, categories and models.";
$lang['plugins']['neuroscore']['divider_categories']                        = "Category (tag) auto-selection";
$lang['plugins']['neuroscore']['divider_categories_hint']                   = "Analyzes screenshots to auto-select categories or tags.";
$lang['plugins']['neuroscore']['divider_models']                            = "Model auto-selection";
$lang['plugins']['neuroscore']['divider_models_hint']                       = "Analyzes screenshots to auto-select known models using face detection techniques.";
$lang['plugins']['neuroscore']['field_api_key']                             = "API key";
$lang['plugins']['neuroscore']['field_api_key_hint']                        = "please <a href=\"https://neuroscore.ai/app/#/signup\">Register</a> or <a href=\"https://neuroscore.ai/app/#/login\">Login</a> to your NeuroScore.ai account to get your API key (find it in Profile > Api Key tab)";
$lang['plugins']['neuroscore']['field_balance']                             = "Your balance";
$lang['plugins']['neuroscore']['field_on_empty_balance']                    = "On insufficient balance";
$lang['plugins']['neuroscore']['field_on_empty_balance_schedule']           = "Schedule all tasks to wait for balance refill";
$lang['plugins']['neuroscore']['field_on_empty_balance_ignore']             = "Ignore all tasks that happen during zero balance";
$lang['plugins']['neuroscore']['field_on_empty_balance_hint']               = "choose to schedule tasks, if you want KVS to run them all after your balance is refilled";
$lang['plugins']['neuroscore']['field_enable_debug']                        = "Enable debug";
$lang['plugins']['neuroscore']['field_enable_debug_enabled']                = "enabled";
$lang['plugins']['neuroscore']['field_enable_debug_log']                    = "debug log";
$lang['plugins']['neuroscore']['field_enable_debug_hint']                   = "enable logging of all API requests";
$lang['plugins']['neuroscore']['field_score_enable']                        = "Enable screenshot scoring";
$lang['plugins']['neuroscore']['field_score_enable_enabled']                = "enabled";
$lang['plugins']['neuroscore']['field_score_enable_hint']                   = "you can see scoring task information in the log of every video (Videos -> View video log)";
$lang['plugins']['neuroscore']['field_score_screenshot_type']               = "Apply to screenshots";
$lang['plugins']['neuroscore']['field_score_screenshot_type_all']           = "Videos with both auto-created and manually uploaded screenshots";
$lang['plugins']['neuroscore']['field_score_screenshot_type_auto']          = "Ignore videos with manually uploaded screenshots";
$lang['plugins']['neuroscore']['field_score_screenshot_type_hint']          = "indicates whether videos with manually uploaded screenshots should also be sent to Neuroscore";
$lang['plugins']['neuroscore']['field_score_screenshot_max_count']          = "Max screenshots to score";
$lang['plugins']['neuroscore']['field_score_screenshot_max_count_hint']     = "specify the maximum limit of screenshots per video to send for scoring, so that if video contains more screenshots than that, KVS will send only the first N screenshots [kt|br] leave empty if you want ALL screenshots to be scored in all cases";
$lang['plugins']['neuroscore']['field_score_screenshot_retain']             = "After scoring";
$lang['plugins']['neuroscore']['field_score_screenshot_retain_all']         = "Change main screenshot only, do not delete anything";
$lang['plugins']['neuroscore']['field_score_screenshot_retain_all_hint']    = "KVS admin panel will provide information about screenshot scores for your review";
$lang['plugins']['neuroscore']['field_score_screenshot_retain_count']       = "Retain N best screenshots, change main and delete the rest";
$lang['plugins']['neuroscore']['field_score_screenshot_retain_count_hint']  = "specify the number of best screenshots you want to retain after their scoring; the rest of bad screenshots will be automatically deleted";
$lang['plugins']['neuroscore']['field_title_enable']                        = "Enable title rewrite";
$lang['plugins']['neuroscore']['field_title_enable_enabled']                = "enabled";
$lang['plugins']['neuroscore']['field_title_rewrite_directories']           = "Sync URLs with new titles";
$lang['plugins']['neuroscore']['field_title_rewrite_directories_enabled']   = "enabled";
$lang['plugins']['neuroscore']['field_title_rewrite_directories_hint']      = "if enabled, directories and URLs will be adjusted accordingly [kt|br] [kt|b]WARNING![/kt|b] Do not use this option if your video URLs do not have numeric IDs, otherwise your old video URLs will become 404 errors.";
$lang['plugins']['neuroscore']['field_categories_enable']                   = "Enable category (tag) auto-selection";
$lang['plugins']['neuroscore']['field_categories_enable_enabled']           = "enabled";
$lang['plugins']['neuroscore']['field_categories_type']                     = "Add as";
$lang['plugins']['neuroscore']['field_categories_type_categories']          = "Categories";
$lang['plugins']['neuroscore']['field_categories_type_tags']                = "Tags";
$lang['plugins']['neuroscore']['field_models_enable']                       = "Enable model auto-selection";
$lang['plugins']['neuroscore']['field_models_enable_enabled']               = "enabled";
$lang['plugins']['neuroscore']['field_apply_to']                            = "Use for";
$lang['plugins']['neuroscore']['field_apply_to_list']['admins']             = "New videos added manually by admins";
$lang['plugins']['neuroscore']['field_apply_to_list']['import']             = "New videos imported by admins";
$lang['plugins']['neuroscore']['field_apply_to_list']['feeds']              = "New videos imported by feeds";
$lang['plugins']['neuroscore']['field_apply_to_list']['grabbers']           = "New videos imported by grabbers";
$lang['plugins']['neuroscore']['field_apply_to_list']['ftp']                = "New videos imported via FTP content upload plugin";
$lang['plugins']['neuroscore']['field_apply_to_list']['site']               = "New videos added by site users";
$lang['plugins']['neuroscore']['field_apply_to_list']['manual']             = "Manual execution for existing videos";
$lang['plugins']['neuroscore']['field_apply_to_list']['manual_hint']        = "manual execution can be run from mass edit GUI and can be used for testing purposes";
$lang['plugins']['neuroscore']['field_apply_to_list']['manual_repeat']      = "Allow repeated processing";
$lang['plugins']['neuroscore']['field_apply_to_list']['manual_repeat_hint'] = "by default KVS will skip videos that were already processed by Neuroscore; activate this option if you want to force repeated execution for any such videos";
$lang['plugins']['neuroscore']['field_apply_to_feeds_all']                  = "All importing feeds";
$lang['plugins']['neuroscore']['field_apply_to_feeds_selected']             = "Only selected feeds";
$lang['plugins']['neuroscore']['field_apply_to_feeds_feed']                 = "Feed [kt|b]%1%[/kt|b]";
$lang['plugins']['neuroscore']['field_apply_to_empty_categories']           = "Only for videos with no categories (tags)";
$lang['plugins']['neuroscore']['field_apply_to_empty_models']               = "Only for videos with no models";
$lang['plugins']['neuroscore']['field_stats']                               = "Stats";
$lang['plugins']['neuroscore']['field_stats_none']                          = "N/A";
$lang['plugins']['neuroscore']['field_stats_postponed']                     = "%1% videos postponed";
$lang['plugins']['neuroscore']['field_stats_processing']                    = "%1% videos in progress";
$lang['plugins']['neuroscore']['field_stats_finished']                      = "%1% processed videos";
$lang['plugins']['neuroscore']['field_stats_deleted']                       = "%1% processed and deleted videos";
$lang['plugins']['neuroscore']['field_tasks']                               = "Completed tasks";
$lang['plugins']['neuroscore']['field_status_missing']                      = "Not applied";
$lang['plugins']['neuroscore']['field_status_postponed']                    = "Postponed";
$lang['plugins']['neuroscore']['field_status_processing']                   = "In process";
$lang['plugins']['neuroscore']['field_status_finished']                     = "Finished";
$lang['plugins']['neuroscore']['btn_save']                                  = "Save";
$lang['plugins']['neuroscore']['error_invalid_api_response_code']           = "Wrong API key or failed to access Neuroscore API";
$lang['plugins']['neuroscore']['error_invalid_api_response_format']         = "Wrong API response format";