<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['digiregs']['title']          = "DigiRegs";
$lang['plugins']['digiregs']['description']    = "Automatically detects copyrighted videos and reduces the number of DMCA claims.";
$lang['plugins']['digiregs']['long_desc']      = "
		DigiRegs provides a paid access to their video copyright API that allows detecting video copyright holder 
		for every uploaded video. Use this plugin to automate handling of copyrighted videos uploaded to your site and
		reduce the number of DMCA claims for your content.
";
$lang['permissions']['plugins|digiregs']   = $lang['plugins']['digiregs']['title'];

$lang['plugins']['digiregs']['divider_general']                                                     = "DigiRegs API settings";
$lang['plugins']['digiregs']['divider_copyright']                                                   = "Copyright detection";
$lang['plugins']['digiregs']['divider_copyright_hint']                                              = "KVS will send screenshot samples of new videos to DigiRegs for analysis before processing them; this will allow deleting copyrighted videos, or truncating them to the allowed duration.";
$lang['plugins']['digiregs']['field_api_key']                                                       = "API key";
$lang['plugins']['digiregs']['field_api_key_hint']                                                  = "Please <a href=\"https://digiregs.net/kvs/register.php\">Register</a> or <a href=\"https://digiregs.net/kvs/\">Login</a> to your DigiRegs account to get your API key";
$lang['plugins']['digiregs']['field_balance']                                                       = "Your balance";
$lang['plugins']['digiregs']['field_balance_value']                                                 = "%1% credits";
$lang['plugins']['digiregs']['field_on_empty_balance']                                              = "On insufficient balance";
$lang['plugins']['digiregs']['field_on_empty_balance_wait']                                         = "Wait for balance refill";
$lang['plugins']['digiregs']['field_on_empty_balance_ignore']                                       = "Process videos without checking";
$lang['plugins']['digiregs']['field_on_empty_balance_hint']                                         = "set to wait, if you want KVS to check 100% videos before they are processed, but this may clog the processing queue";
$lang['plugins']['digiregs']['field_enable_debug']                                                  = "Enable debug";
$lang['plugins']['digiregs']['field_enable_debug_enabled']                                          = "enabled";
$lang['plugins']['digiregs']['field_enable_debug_log']                                              = "debug log";
$lang['plugins']['digiregs']['field_enable_debug_hint']                                             = "enable logging of all API requests";
$lang['plugins']['digiregs']['field_copyright_enable']                                              = "Enable copyright detection";
$lang['plugins']['digiregs']['field_copyright_enable_enabled']                                      = "enabled";
$lang['plugins']['digiregs']['field_copyright_known_action']                                        = "Known copyrighted content";
$lang['plugins']['digiregs']['field_copyright_known_action_delete']                                 = "Delete all copyrighted content";
$lang['plugins']['digiregs']['field_copyright_known_action_delete_hint']                            = "will deny any copyrighted content[kt|br] [kt|b]NOTE: [/kt|b] this action is only possible for new content";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_only_with_duration_limit']         = "Process only content with allowed duration limit";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_only_with_duration_limit_hint']    = "will allow only content from copyright holders that allow publishing their content with limited duration";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_only_from_known_sources']          = "Process only content from existing content sources / channels";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_only_from_known_sources_hint']     = "will allow only content from copyright holders that exist as content sources / channels in your database (either title or synonyms match)";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_all']                              = "Process all copyrighted content";
$lang['plugins']['digiregs']['field_copyright_known_action_allow_all_hint']                         = "will allow any copyrighted content, but you can also use additional actions to further process such content manually";
$lang['plugins']['digiregs']['field_copyright_known_deactivate']                                    = "Deactivate content";
$lang['plugins']['digiregs']['field_copyright_known_deactivate_hint']                               = "all copyrighted content will be deactivated after processing";
$lang['plugins']['digiregs']['field_copyright_known_set_admin_flag']                                = "Set admin flag";
$lang['plugins']['digiregs']['field_copyright_known_set_admin_flag_hint']                           = "set the selected admin flag for all copyrighted content";
$lang['plugins']['digiregs']['field_copyright_known_truncate_duration']                             = "Truncate duration where required by copyright holder";
$lang['plugins']['digiregs']['field_copyright_known_truncate_duration_hint']                        = "content from copyright holders that allow publishing their content with limited duration will be automatically truncated to the needed duration";
$lang['plugins']['digiregs']['field_copyright_known_create_content_sources']                        = "Create missing content sources";
$lang['plugins']['digiregs']['field_copyright_known_create_content_sources_hint']                   = "automatically create content sources for known copyright holders; could be used for publishing links to copyright holder websites along with their content";
$lang['plugins']['digiregs']['field_copyright_known_create_content_sources_disabled']               = "in inactive status";
$lang['plugins']['digiregs']['field_copyright_known_create_dvds']                                   = "Create missing channels";
$lang['plugins']['digiregs']['field_copyright_known_create_dvds_hint']                              = "automatically create channels for known copyright holders; could be used for publishing links to copyright holder websites along with their content";
$lang['plugins']['digiregs']['field_copyright_known_create_dvds_disabled']                          = "in inactive status";
$lang['plugins']['digiregs']['field_copyright_unknown_action']                                      = "Content with unknown watermarks";
$lang['plugins']['digiregs']['field_copyright_unknown_action_delete']                               = "Delete all content with unknown watermarks";
$lang['plugins']['digiregs']['field_copyright_unknown_action_delete_hint']                          = "will deny any content with unknown watermarks[kt|br] [kt|b]NOTE: [/kt|b] this action is only possible for new content";
$lang['plugins']['digiregs']['field_copyright_unknown_action_allow_only_from_known_sources']        = "Process only content from existing content sources / channels";
$lang['plugins']['digiregs']['field_copyright_unknown_action_allow_only_from_known_sources_hint']   = "will allow only content with unknown watermarks that exist as content sources / channels in your database (either title or synonyms match)";
$lang['plugins']['digiregs']['field_copyright_unknown_action_allow_all']                            = "Process all content with unknown watermarks";
$lang['plugins']['digiregs']['field_copyright_unknown_action_allow_all_hint']                       = "will allow any copyrighted content with unknown watermarks, but you can also use additional actions to further process such content manually";
$lang['plugins']['digiregs']['field_copyright_unknown_deactivate']                                  = "Deactivate content";
$lang['plugins']['digiregs']['field_copyright_unknown_deactivate_hint']                             = "all content with unknown watermarks will be deactivated after processing";
$lang['plugins']['digiregs']['field_copyright_unknown_set_admin_flag']                              = "Set admin flag";
$lang['plugins']['digiregs']['field_copyright_unknown_set_admin_flag_hint']                         = "set the selected admin flag for all content with unknown watermarks";
$lang['plugins']['digiregs']['field_copyright_unknown_create_content_sources']                      = "Create missing content sources";
$lang['plugins']['digiregs']['field_copyright_unknown_create_content_sources_hint']                 = "automatically create content sources for unknown watermarks";
$lang['plugins']['digiregs']['field_copyright_unknown_create_content_sources_disabled']             = "in inactive status";
$lang['plugins']['digiregs']['field_copyright_unknown_create_dvds']                                 = "Create missing channels";
$lang['plugins']['digiregs']['field_copyright_unknown_create_dvds_hint']                            = "automatically create channels for unknown watermarks";
$lang['plugins']['digiregs']['field_copyright_unknown_create_dvds_disabled']                        = "in inactive status";
$lang['plugins']['digiregs']['field_copyright_empty_action']                                        = "Undetected content";
$lang['plugins']['digiregs']['field_copyright_empty_action_delete']                                 = "Delete all undetected content";
$lang['plugins']['digiregs']['field_copyright_empty_action_delete_hint']                            = "will deny any content that has no copyright or watermark info found by Digiregs[kt|br] [kt|b]NOTE: [/kt|b] this action is only possible for new content";
$lang['plugins']['digiregs']['field_copyright_empty_action_allow_all']                              = "Process all undetected content";
$lang['plugins']['digiregs']['field_copyright_empty_action_allow_all_hint']                         = "will allow all content that has no copyright or watermark info found by Digiregs";
$lang['plugins']['digiregs']['field_copyright_blacklisted_holders']                                 = "Blacklisted copyright holders and watermarks";
$lang['plugins']['digiregs']['field_copyright_blacklisted_holders_hint']                            = "comma or new line separated list of copyright holders and watermarks that should never be added";
$lang['plugins']['digiregs']['field_apply_to']                                                      = "Use for";
$lang['plugins']['digiregs']['field_apply_to_list']['admins']                                       = "New videos added manually by admins";
$lang['plugins']['digiregs']['field_apply_to_list']['import']                                       = "New videos imported by admins";
$lang['plugins']['digiregs']['field_apply_to_list']['feeds']                                        = "New videos imported by feeds";
$lang['plugins']['digiregs']['field_apply_to_list']['grabbers']                                     = "New videos imported by grabbers";
$lang['plugins']['digiregs']['field_apply_to_list']['ftp']                                          = "New videos imported via FTP content upload plugin";
$lang['plugins']['digiregs']['field_apply_to_list']['site']                                         = "New videos added by site users";
$lang['plugins']['digiregs']['field_apply_to_list']['manual']                                       = "Manual execution for existing videos";
$lang['plugins']['digiregs']['field_apply_to_list']['manual_hint']                                  = "manual execution can be run from mass edit GUI and can be used for testing purposes";
$lang['plugins']['digiregs']['field_apply_to_feeds_all']                                            = "All importing feeds";
$lang['plugins']['digiregs']['field_apply_to_feeds_selected']                                       = "Only selected feeds";
$lang['plugins']['digiregs']['field_apply_to_feeds_feed']                                           = "Feed [kt|b]%1%[/kt|b]";
$lang['plugins']['digiregs']['field_apply_only_with_empty_content_source']                          = "Only for videos with no content source set";
$lang['plugins']['digiregs']['field_apply_only_with_empty_content_source_hint']                     = "enable, if you want this plugin to skip videos with specified content source";
$lang['plugins']['digiregs']['btn_save']                                                            = "Save";
$lang['plugins']['digiregs']['error_invalid_api_response_code']                                     = "Wrong API key or failed to access DigiRegs API (%1%)";
$lang['plugins']['digiregs']['error_invalid_api_response_format']                                   = "Wrong API response format";
$lang['plugins']['digiregs']['error_delete_not_possible_with_manual_execution']                     = "[kt|b][%1%][/kt|b]: using this option is not possible with manual execution";