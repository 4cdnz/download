<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['audit']['title']          = "System audit";
$lang['plugins']['audit']['description']    = "Verifies different aspects of KVS installation and configuration, such as required permissions, database integrity and some other.";
$lang['plugins']['audit']['long_desc']      = "
		The audit plugin runs a full check on many critical aspects of KVS, from checking file and directory privileges
		to full content availability check. If you launched the audit plugin with all options enabled and no errors
		were detected, most likely, your site is working without any issues (this is not a 100% guarantee though). If
		you have a lot of videos and albums, it is likely that full content scan will take several hours to complete. 
		You don't need to wait for it to finish, as this is a background task. You can close the page and return to it 
		in a while. The results of the check will be shown in the list of recent checks.
";
$lang['permissions']['plugins|audit']       = $lang['plugins']['audit']['title'];

$lang['plugins']['audit']['message_checking_installation']          = "Checking installation";
$lang['plugins']['audit']['message_checking_table']                 = "Checking table %1%";
$lang['plugins']['audit']['message_checking_format']                = "Checking format %1%";
$lang['plugins']['audit']['message_checking_server']                = "Checking server %1%";
$lang['plugins']['audit']['message_checking_blocks']                = "Checking site blocks";
$lang['plugins']['audit']['message_checking_templates']             = "Checking site templates";
$lang['plugins']['audit']['message_checking_advertising']           = "Checking site advertising";
$lang['plugins']['audit']['message_checking_video']                 = "Checking video %1%";
$lang['plugins']['audit']['message_checking_album']                 = "Checking album %1%";
$lang['plugins']['audit']['message_checking_categories']            = "Checking categories";
$lang['plugins']['audit']['message_checking_models']                = "Checking models";
$lang['plugins']['audit']['message_checking_content_sources']       = "Checking content sources";
$lang['plugins']['audit']['message_checking_dvds']                  = "Checking DVDs / channels / TV series";
$lang['plugins']['audit']['message_checking_posts']                 = "Checking posts";
$lang['plugins']['audit']['message_checking_users']                 = "Checking users";
$lang['plugins']['audit']['message_checking_content_protection']    = "Checking content protection";
$lang['plugins']['audit']['message_checking_security']              = "Checking security";

$lang['plugins']['audit']['divider_parameters']                     = "Parameters";
$lang['plugins']['audit']['divider_result']                         = "Audit result - %1%";
$lang['plugins']['audit']['divider_result_log_file']                = "Log file - %1%";
$lang['plugins']['audit']['divider_result_not_finished']            = "This audit task is not yet finished.";
$lang['plugins']['audit']['divider_result_none']                    = "There are no problems detected.";
$lang['plugins']['audit']['divider_recent_audits']                  = "Recent audits";
$lang['plugins']['audit']['divider_recent_audits_none']             = "There are no audits executed recently.";
$lang['plugins']['audit']['field_check_installation']               = "Check installation";
$lang['plugins']['audit']['field_check_installation_hint']          = "verifies basic system health";
$lang['plugins']['audit']['field_check_database']                   = "Check database";
$lang['plugins']['audit']['field_check_database_hint']              = "verifies database tables and indexes state; may take much time for big database";
$lang['plugins']['audit']['field_check_formats']                    = "Check formats";
$lang['plugins']['audit']['field_check_formats_hint']               = "verifies ability to create all formats (videos, screenshots and albums)";
$lang['plugins']['audit']['field_check_servers']                    = "Check servers";
$lang['plugins']['audit']['field_check_servers_hint']               = "tests storage and conversion servers availability and operability";
$lang['plugins']['audit']['field_check_website_ui']                 = "Check website pages, blocks and templates";
$lang['plugins']['audit']['field_check_website_ui_hint']            = "looks for potential errors and caching problems in website pages and templates";
$lang['plugins']['audit']['field_check_video_content']              = "Check video content (may take much time)";
$lang['plugins']['audit']['field_check_video_content_hint']         = "provides a complete round of testing for all video files and video screenshots (reports broken and missing files); may take much time for big amount of videos";
$lang['plugins']['audit']['field_check_video_content_stream']       = "also check video playing (may greatly increase site load)";
$lang['plugins']['audit']['field_check_video_content_stream_hint']  = "choose this option if you want to check if all video files stored on your server(s) can be successfully played; this check may increase load on your main server";
$lang['plugins']['audit']['field_check_video_content_embed']        = "also check embed codes, galleries and hotlinked files";
$lang['plugins']['audit']['field_check_video_content_embed_hint']   = "choose this option if you want to check if all embed codes and hotlinked files can be successfully played";
$lang['plugins']['audit']['field_check_video_content_range_from']   = "video ID range from";
$lang['plugins']['audit']['field_check_video_content_range_to']     = "to";
$lang['plugins']['audit']['field_check_video_content_range_hint']   = "leave empty if you don't want to use ID limit";
$lang['plugins']['audit']['field_check_album_content']              = "Check album content (may take much time)";
$lang['plugins']['audit']['field_check_album_content_hint']         = "provides a complete round of testing for all album files (reports broken and missing files); may take much time for big amount of albums";
$lang['plugins']['audit']['field_check_album_content_range_from']   = "album ID range from";
$lang['plugins']['audit']['field_check_album_content_range_hint']   = "leave empty if you don't want to use ID limit";
$lang['plugins']['audit']['field_check_album_content_range_to']     = "to";
$lang['plugins']['audit']['field_check_auxiliary_content']          = "Check posts, categorization and members (may take much time)";
$lang['plugins']['audit']['field_check_auxiliary_content_hint']     = "provides a complete round of testing for all files of posts, categorization and members (reports missing files); may take much time for big amount of data";
$lang['plugins']['audit']['field_check_content_protection']         = "Check content protection (requires some content)";
$lang['plugins']['audit']['field_check_content_protection_hint']    = "verifies that sensitive content is protected against unauthorized access (e.g. no ability to access video sources, protected video files, protected album images and etc.)";
$lang['plugins']['audit']['field_check_security']                   = "Check project security criteria";
$lang['plugins']['audit']['field_check_security_hint']              = "checks for possible security issues";
$lang['plugins']['audit']['btn_start']                              = "Start checking";

$lang['plugins']['audit']['dg_recent_audits_col_time']                  = "Executed";
$lang['plugins']['audit']['dg_recent_audits_col_results']               = "Results";
$lang['plugins']['audit']['dg_recent_audits_col_results_messages']      = "%1% messages";
$lang['plugins']['audit']['dg_recent_audits_col_results_errors']        = "%1% errors";
$lang['plugins']['audit']['dg_recent_audits_col_results_warnings']      = "%1% warnings";
$lang['plugins']['audit']['dg_recent_audits_col_results_infos']         = "%1% infos";
$lang['plugins']['audit']['dg_recent_audits_col_results_in_process']    = "In process...";
$lang['plugins']['audit']['dg_recent_audits_col_results_in_process_pc'] = "In process: %1%% done";
$lang['plugins']['audit']['dg_recent_audits_col_results_error']         = "Process error";
$lang['plugins']['audit']['dg_recent_audits_col_log']                   = "Log";

$lang['plugins']['audit']['dg_errors_col_error_type']                                   = "Problem type";
$lang['plugins']['audit']['dg_errors_col_error_type_error']                             = "Error";
$lang['plugins']['audit']['dg_errors_col_error_type_warning']                           = "Warning";
$lang['plugins']['audit']['dg_errors_col_error_type_info']                              = "Notification";
$lang['plugins']['audit']['dg_errors_col_resource']                                     = "Resource";
$lang['plugins']['audit']['dg_errors_col_resource_settings']                            = "Content settings";
$lang['plugins']['audit']['dg_errors_col_resource_player_settings']                     = "Player settings";
$lang['plugins']['audit']['dg_errors_col_resource_embed_player_settings']               = "Embed player settings";
$lang['plugins']['audit']['dg_errors_col_resource_storage_server']                      = "Storage server \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_conversion_server']                   = "Conversion server \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_block']                               = "Block \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_page']                                = "Page \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_global_blocks']                       = "Global blocks";
$lang['plugins']['audit']['dg_errors_col_resource_page_component']                      = "Page component \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_page_block']                          = "Block \"%1%\" on page \"%2%\"";
$lang['plugins']['audit']['dg_errors_col_resource_advertising_spot']                    = "Spot \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_table']                               = "Database table \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_language']                            = "Language \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_video']                               = "Video \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_album']                               = "Album \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_format_video']                        = "Video format \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_format_screenshot']                   = "Screenshot format \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_format_album']                        = "Album format \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_category']                            = "Category \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_category_group']                      = "Category group \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_model']                               = "Model \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_model_group']                         = "Model group \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_content_source']                      = "Content source \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_dvd']                                 = "Channel / DVD / TV season \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_dvd_group']                           = "Channel group / DVD group / TV series \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_post']                                = "Post \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_resource_user']                                = "Member \"%1%\"";
$lang['plugins']['audit']['dg_errors_col_message']                                      = "Message";
$lang['plugins']['audit']['dg_errors_col_message_db_version_mismatch']                  = "Database version mismatch detected";
$lang['plugins']['audit']['dg_errors_col_message_db_installation']                      = "Database installation script was not fully executed";
$lang['plugins']['audit']['dg_errors_col_message_php_config_parameter_value']           = "PHP configuration parameter has value that is not supported";
$lang['plugins']['audit']['dg_errors_col_message_system_file_removed']                  = "The system file is missing";
$lang['plugins']['audit']['dg_errors_col_message_system_file_changed']                  = "The system file has custom changes";
$lang['plugins']['audit']['dg_errors_col_message_system_file_invalid']                  = "The system file has invalid format";
$lang['plugins']['audit']['dg_errors_col_message_www_missing']                          = "\$config['project_url'] should define URL with www";
$lang['plugins']['audit']['dg_errors_col_message_www_redundant']                        = "\$config['project_url'] should define URL without www";
$lang['plugins']['audit']['dg_errors_col_message_satellite_for']                        = "\$config['satellite_for'] does not match database value";
$lang['plugins']['audit']['dg_errors_col_message_iframe_embed']                         = "Old iframe embed approach is used";
$lang['plugins']['audit']['dg_errors_col_message_gzip']                                 = "GZIP compression is not enabled at server level";
$lang['plugins']['audit']['dg_errors_col_message_transliteration_rules']                = "Missing built-in transliteration rules, please contact support";
$lang['plugins']['audit']['dg_errors_col_message_http_host']                            = "HTTP_HOST environment variable is not set correctly";
$lang['plugins']['audit']['dg_errors_col_message_php_module']                           = "Required PHP module is not installed";
$lang['plugins']['audit']['dg_errors_col_message_cron_folder']                          = "Cron executed from invalid directory";
$lang['plugins']['audit']['dg_errors_col_message_cron_last_exec']                       = "Cron executed more than 15 minutes ago";
$lang['plugins']['audit']['dg_errors_col_message_memory_limit']                         = "PHP memory limit is less than 128mb";
$lang['plugins']['audit']['dg_errors_col_message_ip_detection']                         = "The client IP is not being correctly detected";
$lang['plugins']['audit']['dg_errors_col_message_permissions']                          = "Write permissions required";
$lang['plugins']['audit']['dg_errors_col_message_file_creation_failed']                 = "Failed to create a file in directory";
$lang['plugins']['audit']['dg_errors_col_message_library_problem']                      = "Library is not working properly";
$lang['plugins']['audit']['dg_errors_col_message_video_format']                         = "Video format failed to be converted";
$lang['plugins']['audit']['dg_errors_col_message_screenshot_format']                    = "Screenshot format failed to be created";
$lang['plugins']['audit']['dg_errors_col_message_album_format']                         = "Album format failed to be created";
$lang['plugins']['audit']['dg_errors_col_message_server_connection1']                   = "Failed to connect to the specified host and 21 port";
$lang['plugins']['audit']['dg_errors_col_message_server_connection2']                   = "Failed to log in using the specified data";
$lang['plugins']['audit']['dg_errors_col_message_server_connection3']                   = "Failed to execute put / get / delete operation, maybe not enough permissions";
$lang['plugins']['audit']['dg_errors_col_message_server_no_ftp_extension']              = "PHP extension for FTP is not installed";
$lang['plugins']['audit']['dg_errors_col_message_server_no_aws_extension']              = "Failed to download AWS module from https://docs.aws.amazon.com";
$lang['plugins']['audit']['dg_errors_col_message_server_control_script']                = "Control script is not accessible";
$lang['plugins']['audit']['dg_errors_col_message_server_heartbeat']                     = "Conversion script is not working";
$lang['plugins']['audit']['dg_errors_col_message_server_heartbeat2']                    = "Conversion script executed more than 15 minutes ago";
$lang['plugins']['audit']['dg_errors_col_message_server_availability']                  = "Storage server is either unavailable, or not configured correctly";
$lang['plugins']['audit']['dg_errors_col_message_server_https']                         = "Storage server is not using HTTPS";
$lang['plugins']['audit']['dg_errors_col_message_api_script']                           = "CDN control script is missing";
$lang['plugins']['audit']['dg_errors_col_message_server_key']                           = "Control script has wrong secret key";
$lang['plugins']['audit']['dg_errors_col_message_satellite_key']                        = "Secret key of this satellite is not matching main project";
$lang['plugins']['audit']['dg_errors_col_message_server_time']                          = "Storage server time is not synchronized with main server time";
$lang['plugins']['audit']['dg_errors_col_message_block_implementation']                 = "The implementation of block is broken";
$lang['plugins']['audit']['dg_errors_col_message_memcache_module']                      = "PHP Memcached module is not installed";
$lang['plugins']['audit']['dg_errors_col_message_memcache_connection']                  = "Can't connect to Memcached";
$lang['plugins']['audit']['dg_errors_col_message_temp_file']                            = "Temporary file or directory should be deleted";
$lang['plugins']['audit']['dg_errors_col_message_custom_blocks']                        = "Project is using custom blocks";
$lang['plugins']['audit']['dg_errors_col_message_ydl_not_installed']                    = "Yt-dlp library is not found";
$lang['plugins']['audit']['dg_errors_col_message_php_5_api_used']                       = "PHP5 obsolete functions are used";
$lang['plugins']['audit']['dg_errors_col_message_video_screenshot_sources']             = "Video screenshot sources are not available";
$lang['plugins']['audit']['dg_errors_col_message_cron_duplicate']                       = "Cron already being executed from another location";
$lang['plugins']['audit']['dg_errors_col_message_cron_security']                        = "Cron is executed under privileged user";
$lang['plugins']['audit']['dg_errors_col_message_open_basedir']                         = "PHP open_basedir is set";
$lang['plugins']['audit']['dg_errors_col_message_player_settings_errors']               = "Player settings have errors";
$lang['plugins']['audit']['dg_errors_col_message_embed_player_settings_errors']         = "Embed player settings have errors";
$lang['plugins']['audit']['dg_errors_col_message_table_status_check_warning']           = "MySQL returned warning for table status check";
$lang['plugins']['audit']['dg_errors_col_message_table_status_check_error']             = "MySQL returned error for table status check";
$lang['plugins']['audit']['dg_errors_col_message_language_column_error']                = "Language is missing required columns in some tables, should be re-saved";
$lang['plugins']['audit']['dg_errors_col_message_autoincrement_error']                  = "Auto-increment is missing on the primary key column in database table";
$lang['plugins']['audit']['dg_errors_col_message_video_file_missing']                   = "One of video files is missing or has invalid size";
$lang['plugins']['audit']['dg_errors_col_message_video_source_directory']               = "Video source directory is missing or not writable";
$lang['plugins']['audit']['dg_errors_col_message_video_screenshot_missing']             = "One of video screenshots is missing";
$lang['plugins']['audit']['dg_errors_col_message_video_screenshot_size_invalid']        = "Size for one of video screenshots is not valid";
$lang['plugins']['audit']['dg_errors_col_message_video_screenshot_zip_missing']         = "One of video screenshot ZIP file is missing";
$lang['plugins']['audit']['dg_errors_col_message_video_cuepoints_file_missing']         = "Cuepoints file is missing";
$lang['plugins']['audit']['dg_errors_col_message_video_preview_file_missing']           = "Player preview file is missing";
$lang['plugins']['audit']['dg_errors_col_message_video_file_cannot_be_streamed']        = "Video file cannot be played";
$lang['plugins']['audit']['dg_errors_col_message_video_format_required']                = "The required video format is missing for some videos";
$lang['plugins']['audit']['dg_errors_col_message_video_hotlink_required']               = "Video hotlink URL is missing";
$lang['plugins']['audit']['dg_errors_col_message_video_hotlink_invalid']                = "Video hotlink URL is not valid or blocked for your server IP / your domain";
$lang['plugins']['audit']['dg_errors_col_message_video_hotlink_ssl']                    = "Video hotlink URL should use HTTPS instead of HTTP";
$lang['plugins']['audit']['dg_errors_col_message_video_embed_required']                 = "Video embed code is missing";
$lang['plugins']['audit']['dg_errors_col_message_video_embed_invalid']                  = "Video embed code is not valid or blocked for your server IP / your domain";
$lang['plugins']['audit']['dg_errors_col_message_video_embed_ssl']                      = "Video embed code should use HTTPS instead of HTTP";
$lang['plugins']['audit']['dg_errors_col_message_video_pseudo_required']                = "Video outgoing URL is missing";
$lang['plugins']['audit']['dg_errors_col_message_video_pseudo_invalid']                 = "Video outgoing URL is not valid or blocked for your server IP / your domain";
$lang['plugins']['audit']['dg_errors_col_message_album_file_missing']                   = "One of album files is missing or has invalid size";
$lang['plugins']['audit']['dg_errors_col_message_album_source_directory']               = "Album source directory is not writable";
$lang['plugins']['audit']['dg_errors_col_message_true_type_fonts']                      = "True Type fonts support is not enabled";
$lang['plugins']['audit']['dg_errors_col_message_mysql_strict_mode']                    = "MySQL strict mode is enabled, you should remove STRICT_ALL_TABLES or STRICT_TRANS_TABLES from MySQL configuration";
$lang['plugins']['audit']['dg_errors_col_message_path_slashes']                         = "The configured path has redundant slash symbols";
$lang['plugins']['audit']['dg_errors_col_message_folder_permissions']                   = "Unable to set 777 permissions to the created directory";
$lang['plugins']['audit']['dg_errors_col_message_umask_permissions']                    = "Umask(0) is not allowing to auto-create files with 666 permissions";
$lang['plugins']['audit']['dg_errors_col_message_hotlink_protection1']                  = "The first level of antihotlink protection is not enabled for this server; videos can be hotlinked";
$lang['plugins']['audit']['dg_errors_col_message_hotlink_protection2']                  = "The second level of antihotlink protection is not enabled; videos can be hotlinked";
$lang['plugins']['audit']['dg_errors_col_message_format_hotlink_possible']              = "Video format can be hotlinked as per its settings";
$lang['plugins']['audit']['dg_errors_col_message_video_sources_accessible']             = "Video source directory is publicly accessible";
$lang['plugins']['audit']['dg_errors_col_message_video_files_accessible']               = "Server video directory is publicly accessible";
$lang['plugins']['audit']['dg_errors_col_message_album_sources_accessible']             = "Server album source directory is publicly accessible";
$lang['plugins']['audit']['dg_errors_col_message_album_images_accessible']              = "Server album images directory for non-public album format is publicly accessible";
$lang['plugins']['audit']['dg_errors_col_message_mysql_select_into_outfile']            = "SELECT INTO OUTFILE queries should not be allowed";
$lang['plugins']['audit']['dg_errors_col_message_suspicious_code_found']                = "Suspicious code found in non-KVS file";
$lang['plugins']['audit']['dg_errors_col_message_suspicious_file_found']                = "Suspicious file found";
$lang['plugins']['audit']['dg_errors_col_message_suspicious_folder_found']              = "Suspicious directory found";
$lang['plugins']['audit']['dg_errors_col_message_page_file_changes_found']              = "Suspicious changes found in page file";
$lang['plugins']['audit']['dg_errors_col_message_folder_allows_php']                    = "Directory allows PHP execution";
$lang['plugins']['audit']['dg_errors_col_message_folder_allows_public_access']          = "Directory allows public access";
$lang['plugins']['audit']['dg_errors_col_message_project_security_setup']               = "KVS is installed under the same user as web server runs";
$lang['plugins']['audit']['dg_errors_col_message_project_root_writable']                = "Project root directory should not be writable by web server";
$lang['plugins']['audit']['dg_errors_col_message_htaccess_writable']                    = "Directory access protection file should not be writable by web server";
$lang['plugins']['audit']['dg_errors_col_message_page_component_error']                 = "Page component has errors and is not working correctly";
$lang['plugins']['audit']['dg_errors_col_message_page_component_permissions']           = "Editing this page component requires write permissions on some files / directories";
$lang['plugins']['audit']['dg_errors_col_message_page_component_php']                   = "Page component template uses potential unsafe PHP constructs";
$lang['plugins']['audit']['dg_errors_col_message_page_component_empty_template']        = "Page component template is empty";
$lang['plugins']['audit']['dg_errors_col_message_page_disabled']                        = "Page is disabled and is not available from site";
$lang['plugins']['audit']['dg_errors_col_message_page_error']                           = "Page has errors and is not working correctly";
$lang['plugins']['audit']['dg_errors_col_message_page_caching_error']                   = "Page has caching issues and is not working correctly";
$lang['plugins']['audit']['dg_errors_col_message_page_caching_warning']                 = "Page has potential caching issues and may not work correctly";
$lang['plugins']['audit']['dg_errors_col_message_page_blocks_cache']                    = "Page has blocks that are not cached";
$lang['plugins']['audit']['dg_errors_col_message_page_permissions']                     = "Editing this page requires write permissions on some files / directories";
$lang['plugins']['audit']['dg_errors_col_message_page_php']                             = "Page template or some of its blocks use potential unsafe PHP constructs";
$lang['plugins']['audit']['dg_errors_col_message_page_var_from_equal_names']            = "Page has different blocks with the same \"var_from\" parameter names";
$lang['plugins']['audit']['dg_errors_col_message_global_blocks_error']                  = "Global blocks have errors and are not working correctly";
$lang['plugins']['audit']['dg_errors_col_message_global_blocks_caching_error']          = "Global blocks have caching issues and are not working correctly";
$lang['plugins']['audit']['dg_errors_col_message_global_blocks_caching_warning']        = "Global blocks have potential caching issues and may not work correctly";
$lang['plugins']['audit']['dg_errors_col_message_global_blocks_cache']                  = "Some of global blocks are not cached";
$lang['plugins']['audit']['dg_errors_col_message_global_blocks_permissions']            = "Editing global blocks requires write permissions on some files / directories";
$lang['plugins']['audit']['dg_errors_col_message_global_blocks_php']                    = "Templates of some global blocks use potential unsafe PHP constructs";
$lang['plugins']['audit']['dg_errors_col_message_advertising_spot_file_invalid']        = "Spot data file has invalid format";
$lang['plugins']['audit']['dg_errors_col_message_advertising_spot_permissions']         = "Editing this spot requires write permissions on some files";
$lang['plugins']['audit']['dg_errors_col_message_data_file_missing']                    = "Some object files are missing";
$lang['plugins']['audit']['dg_errors_col_message_data_file_size_invalid']               = "Some object images are broken";
$lang['plugins']['audit']['dg_errors_col_message_data_required_field_missing']          = "Some object required fields are empty";
$lang['plugins']['audit']['dg_errors_col_message_known_issue']                          = "Known KVS issue #%1%, please contact support";
