<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['backup']['title']         = "Project backup";
$lang['plugins']['backup']['description']   = "Provides ability to backup all aspects of your project.";
$lang['plugins']['backup']['long_desc']     = "
		Use this plugin to make backup copies of the database, KVS system files, site theme and other settings
		as well. It is recommended to have at least weekly backups automated. [kt|b]ATTENTION![/kt|b] This is NOT a 
		replacement for server backups, this plugin has limited usage and will only backup specific KVS aspects to 
		allow you restore them if you did something wrong. You can find instruction to restore in backup archive.
";
$lang['permissions']['plugins|backup']  = $lang['plugins']['backup']['title'];

$lang['plugins']['backup']['message_creating_database_backup']              = "Creating database copy";
$lang['plugins']['backup']['message_creating_website_backup']               = "Creating theme copy";
$lang['plugins']['backup']['message_creating_player_backup']                = "Creating player settings copy";
$lang['plugins']['backup']['message_creating_kvs_backup']                   = "Creating KVS files copy";
$lang['plugins']['backup']['message_creating_content_backup']               = "Creating data files copy";
$lang['plugins']['backup']['message_creating_zip']                          = "Creating ZIP archive";
$lang['plugins']['backup']['message_uploading_remote']                      = "Copying backup archive to remote storage";
$lang['plugins']['backup']['error_mysqldump_command']                       = "Mysqldump command is not available: %1%, you can change it in [kt|b]/admin/include/setup.php[/kt|b]";
$lang['plugins']['backup']['error_folder_permissions']                      = "Not enough permissions for creating backup files in the specified backup directory";
$lang['plugins']['backup']['error_failed']                                  = "Failed to create backup";

$lang['plugins']['backup']['divider_parameters']                            = "Scheduled backups";
$lang['plugins']['backup']['divider_parameters_hint']                       = "[kt|b]ATTENTION![/kt|b] Backup of video / photo files is not supported. Consult your host support in terms of using backup disks or external servers for overall system backups. KVS backup plugin is not a replacement for global server backup strategy and just a convenience method for novice users.";
$lang['plugins']['backup']['divider_manual_backup']                         = "Manual backup";
$lang['plugins']['backup']['divider_backups']                               = "Existing backups";
$lang['plugins']['backup']['divider_backups_none']                          = "There are no backups available.";
$lang['plugins']['backup']['field_backup_folder']                           = "Backup directory";
$lang['plugins']['backup']['field_backup_folder_hint']                      = "We recommend you to specify external directory in order to protect yourself from accidentally deleting backup files. [kt|br] If using external directory you should make sure that your PHP.ini [kt|b]open_basedir[/kt|b] setting allows accessing this directory (if restriction is enabled).";
$lang['plugins']['backup']['field_backup_folder_hint2']                     = "Your current [kt|b]open_basedir[/kt|b] is set to: [kt|b]%1%[/kt|b]";
$lang['plugins']['backup']['remote_storage_type']                           = "Remote backup storage";
$lang['plugins']['backup']['remote_storage_type_na']                        = "Switched off";
$lang['plugins']['backup']['remote_storage_type_ftp']                       = "FTP connection";
$lang['plugins']['backup']['remote_storage_type_s3']                        = "S3 connection";
$lang['plugins']['backup']['remote_storage_type_hint']                      = "duplicate backup archives to remote server of your choice [kt|br] you can configure multiple projects into the same directory, as KVS will prepend backup archive filename with domain name";
$lang['plugins']['backup']['field_backup_ftp_host']                         = "FTP host";
$lang['plugins']['backup']['field_backup_ftp_port']                         = "FTP port";
$lang['plugins']['backup']['field_backup_ftp_user']                         = "FTP username";
$lang['plugins']['backup']['field_backup_ftp_pass']                         = "FTP password";
$lang['plugins']['backup']['field_backup_ftp_folder']                       = "FTP directory";
$lang['plugins']['backup']['field_backup_ftp_folder_hint']                  = "relative to FTP root; leave empty if FTP access is configured directly to the required directory";
$lang['plugins']['backup']['field_backup_ftp_timeout']                      = "FTP timeout";
$lang['plugins']['backup']['field_backup_s3_region']                        = "S3 region name";
$lang['plugins']['backup']['field_backup_s3_region_hint']                   = "text ID of the region where your data bucket is created";
$lang['plugins']['backup']['field_backup_s3_endpoint']                      = "S3 endpoint URL";
$lang['plugins']['backup']['field_backup_s3_endpoint_hint']                 = "this is not required for AWS, but is required for other S3-compatible service providers";
$lang['plugins']['backup']['field_backup_s3_bucket']                        = "S3 bucket name";
$lang['plugins']['backup']['field_backup_s3_prefix']                        = "S3 subfolder";
$lang['plugins']['backup']['field_backup_s3_prefix_hint']                   = "specify subfolder if files of this server should not be stored in bucket root";
$lang['plugins']['backup']['field_backup_s3_api_key']                       = "S3 access key";
$lang['plugins']['backup']['field_backup_s3_api_key_hint']                  = "in AWS you can create your access key in [kt|b]<username> -> Security credentials[/kt|b] menu";
$lang['plugins']['backup']['field_backup_s3_api_secret']                    = "S3 access key secret";
$lang['plugins']['backup']['field_backup_s3_api_secret_hint']               = "in AWS you can create your access key in [kt|b]<username> -> Security credentials[/kt|b] menu";
$lang['plugins']['backup']['field_backup_s3_upload_chunk_size_mb']          = "S3 upload chunk size (mb)";
$lang['plugins']['backup']['field_backup_s3_upload_chunk_size_mb_hint']     = "if your S3 provider doesn't allow uploading large files, specify upload limit in megabytes to switch to using chunked uploads; [kt|br] AWS S3 allows files up to 5Gb to be uploaded at once, so keep it empty if you are using Amazon";
$lang['plugins']['backup']['field_backup_s3_is_endpoint_subdirectory']      = "Bucket name as subdirectory";
$lang['plugins']['backup']['field_backup_s3_is_endpoint_subdirectory_yes']  = "enabled";
$lang['plugins']['backup']['field_backup_s3_is_endpoint_subdirectory_hint'] = "for S3-compatible service providers that operate via subdirectories and not subdomains";
$lang['plugins']['backup']['field_backup_auto']                             = "Automatic backups";
$lang['plugins']['backup']['field_backup_auto_daily']                       = "create daily backups and keep them for the last 7 days";
$lang['plugins']['backup']['field_backup_auto_weekly']                      = "create weekly backups and keep them for the last month";
$lang['plugins']['backup']['field_backup_auto_monthly']                     = "create monthly backups and keep them for the last year";
$lang['plugins']['backup']['field_backup_auto_skip_database']               = "do not backup database";
$lang['plugins']['backup']['field_backup_auto_skip_content_auxiliary']      = "do not backup files of posts, categorization objects and members";
$lang['plugins']['backup']['field_schedule']                                = "Schedule";
$lang['plugins']['backup']['field_schedule_interval']                       = "min interval (h)";
$lang['plugins']['backup']['field_schedule_tod']                            = "time of day";
$lang['plugins']['backup']['field_schedule_tod_any']                        = "any, as soon as possible";
$lang['plugins']['backup']['field_schedule_hint']                           = "specify minimum interval for this plugin execution and specific time of day if needed";
$lang['plugins']['backup']['field_last_exec']                               = "Last executed";
$lang['plugins']['backup']['field_last_exec_none']                          = "none";
$lang['plugins']['backup']['field_last_exec_data']                          = "%1% seconds";
$lang['plugins']['backup']['field_next_exec']                               = "Next execution";
$lang['plugins']['backup']['field_next_exec_none']                          = "none";
$lang['plugins']['backup']['field_next_exec_running']                       = "running now...";
$lang['plugins']['backup']['field_backup_options']                          = "Backup options";
$lang['plugins']['backup']['field_backup_options_mysql']                    = "backup MySQL database";
$lang['plugins']['backup']['field_backup_options_mysql_hint']               = "this option will fully backup KVS database into a single file";
$lang['plugins']['backup']['field_backup_options_website']                  = "backup website";
$lang['plugins']['backup']['field_backup_options_website_hint']             = "this option will backup website settings, templates, config files, advertising (except in-player advertising), images, styles, javascripts and all files in domain root directory";
$lang['plugins']['backup']['field_backup_options_player']                   = "backup player settings";
$lang['plugins']['backup']['field_backup_options_player_hint']              = "this option will backup both player settings and embed player settings";
$lang['plugins']['backup']['field_backup_options_kvs']                      = "backup KVS system files";
$lang['plugins']['backup']['field_backup_options_kvs_hint']                 = "this option will backup all KVS system files";
$lang['plugins']['backup']['field_backup_options_content_auxiliary']        = "backup post, categorization and member files";
$lang['plugins']['backup']['field_backup_options_content_auxiliary_hint']   = "this option will backup all images and custom files of posts, categorization objects and members";
$lang['plugins']['backup']['field_backup_options_content_main']             = "backup video and album files";
$lang['plugins']['backup']['field_backup_options_content_main_hint']        = "[kt|b]NOT SUPPORTED:[/kt|b] videos and albums can take terabytes of space and their backup is not supported";
$lang['plugins']['backup']['dg_backups_col_total']                          = "Total";
$lang['plugins']['backup']['dg_backups_col_delete']                         = "Delete";
$lang['plugins']['backup']['dg_backups_col_filename']                       = "Filename";
$lang['plugins']['backup']['dg_backups_col_filedate']                       = "Date created";
$lang['plugins']['backup']['dg_backups_col_filesize']                       = "Filesize";
$lang['plugins']['backup']['dg_backups_col_backup_type']                    = "Contains";
$lang['plugins']['backup']['dg_backups_col_backup_type_junk']               = "junk files";
$lang['plugins']['backup']['dg_backups_col_backup_type_mysql']              = "database dump";
$lang['plugins']['backup']['dg_backups_col_backup_type_website']            = "website";
$lang['plugins']['backup']['dg_backups_col_backup_type_player']             = "player settings";
$lang['plugins']['backup']['dg_backups_col_backup_type_kvs']                = "KVS system files";
$lang['plugins']['backup']['dg_backups_col_backup_type_content_auxiliary']  = "content files";
$lang['plugins']['backup']['btn_save']                                      = "Save";
$lang['plugins']['backup']['btn_backup']                                    = "Backup now";
