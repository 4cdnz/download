<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['external_search']['title']        = "External search";
$lang['plugins']['external_search']['description']  = "Provides ability to integrate external search engines into your website videos and albums search or related.";
$lang['plugins']['external_search']['long_desc']    = "
		This plugin lets you use third party API to enhance search and related features on your site. You can set up 
		the conditions that trigger the replacement of inner search results with third party search; third party results 
		can also be added to on-site search results. This plugin is typically used for integrating Sphinx search and 
		related videos / albums into KVS.
";
$lang['permissions']['plugins|external_search']     = $lang['plugins']['external_search']['title'];

$lang['plugins']['external_search']['validation_error_api_no_result']           = "[kt|b][%1%][/kt|b]: the specified API does not return anything";
$lang['plugins']['external_search']['validation_error_api_invalid']             = "[kt|b][%1%][/kt|b]: the specified API does not return results in the required format";
$lang['plugins']['external_search']['divider_videos']                           = "External search for videos";
$lang['plugins']['external_search']['divider_albums']                           = "External search for albums";
$lang['plugins']['external_search']['divider_searches']                         = "External search for search queries";
$lang['plugins']['external_search']['field_enable_external_search_videos']      = "Use external search for videos";
$lang['plugins']['external_search']['field_enable_external_search_albums']      = "Use external search for albums";
$lang['plugins']['external_search']['field_enable_external_search_searches']    = "Use external search for search queries";
$lang['plugins']['external_search']['field_enable_external_search_never']       = "Never";
$lang['plugins']['external_search']['field_enable_external_search_always']      = "Always";
$lang['plugins']['external_search']['field_enable_external_search_condition']   = "When internal search results are less than";
$lang['plugins']['external_search']['field_enable_external_search_hint']        = "for connecting Sphinx API choose [kt|b]Always[/kt|b]";
$lang['plugins']['external_search']['field_display_results']                    = "Display external search results";
$lang['plugins']['external_search']['field_display_results_replace']            = "Completely replace internal search";
$lang['plugins']['external_search']['field_display_results_beginning']          = "In the beginning";
$lang['plugins']['external_search']['field_display_results_end']                = "In the end";
$lang['plugins']['external_search']['field_display_results_hint']               = "for connecting Sphinx API choose [kt|b]Completely replace internal search[/kt|b]";
$lang['plugins']['external_search']['field_api_call']                           = "API call";
$lang['plugins']['external_search']['field_api_call_hint_videos']               = "API call string to external search engine; must contain [kt|b]%QUERY%[/kt|b] token which will be replaced with the actual query string entered by user [kt|br] for connecting Sphinx API specify [kt|b]%1%/admin/sphinx_search/kvs_sphinx_search_videos.php?query=%QUERY%&limit=%LIMIT%&from=%FROM%[/kt|b]";
$lang['plugins']['external_search']['field_api_call_hint_albums']               = "API call string to external search engine; must contain [kt|b]%QUERY%[/kt|b] token which will be replaced with the actual query string entered by user [kt|br] for connecting Sphinx API specify [kt|b]%1%/admin/sphinx_search/kvs_sphinx_search_albums.php?query=%QUERY%&limit=%LIMIT%&from=%FROM%[/kt|b]";
$lang['plugins']['external_search']['field_api_call_hint_searches']             = "API call string to external search engine; must contain [kt|b]%QUERY%[/kt|b] token which will be replaced with the actual query string entered by user [kt|br] for connecting Sphinx API specify [kt|b]%1%/admin/sphinx_search/kvs_sphinx_search_searches.php?query=%QUERY%&limit=%LIMIT%&from=%FROM%[/kt|b]";
$lang['plugins']['external_search']['field_outgoing_url']                       = "Outgoing URL";
$lang['plugins']['external_search']['field_outgoing_url_hint']                  = "outgoing URL to redirect users from external search results; can contain [kt|b]%QUERY%[/kt|b] token which will be replaced with the actual query string entered by user; [kt|br] for connecting Sphinx API specify URL of the current domain";
$lang['plugins']['external_search']['field_avg_query_time']                     = "Query average time";
$lang['plugins']['external_search']['field_avg_parse_time']                     = "Parsing average time";
$lang['plugins']['external_search']['btn_save']                                 = "Save";
