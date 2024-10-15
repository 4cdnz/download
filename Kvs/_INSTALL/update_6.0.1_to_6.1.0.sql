DELETE FROM `ktvs_admin_users_settings` WHERE user_id NOT IN (SELECT user_id FROM ktvs_admin_users);

ALTER TABLE `ktvs_languages` CHANGE COLUMN `code` `code` varchar(5) NOT NULL;
ALTER TABLE `ktvs_languages` ADD COLUMN `url` varchar(2) NOT NULL AFTER `code`;

UPDATE `ktvs_admin_processes` SET exec_interval=300 WHERE pid='cron_plugins.grabbers';

ALTER TABLE `ktvs_albums` ADD KEY `admin_user_id` (`admin_user_id`);
ALTER TABLE `ktvs_posts` ADD KEY `admin_user_id` (`admin_user_id`);
ALTER TABLE `ktvs_videos` ADD KEY `admin_user_id` (`admin_user_id`);

ALTER TABLE `ktvs_formats_albums` ADD UNIQUE `size` (`group_id`, `size`);
ALTER TABLE `ktvs_formats_screenshots` ADD UNIQUE `size` (`group_id`, `size`);
ALTER TABLE `ktvs_formats_videos` ADD UNIQUE `postfix` (`postfix`);

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('videos|edit_admin_user', '30','200');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_admin_user', '22','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('posts|edit_admin_user', '18','500');

ALTER TABLE `ktvs_admin_audit_log` ADD KEY `action_id` (`action_id`);

ALTER TABLE `ktvs_admin_system_log` DROP COLUMN `record_id`, DROP PRIMARY KEY;
ALTER TABLE `ktvs_admin_system_log` ADD COLUMN `record_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`record_id`);
ALTER TABLE `ktvs_admin_system_log` ADD COLUMN `satellite_prefix` varchar(255) NOT NULL DEFAULT '' AFTER `record_id`;
ALTER TABLE `ktvs_admin_system_log` ADD KEY `satellite_prefix` (`satellite_prefix`);

ALTER TABLE `ktvs_stats_search` ADD COLUMN `status_id` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `query_md5`;
ALTER TABLE `ktvs_stats_search` ADD KEY `status_id` (`status_id`);

ALTER TABLE `ktvs_admin_users` ADD COLUMN `email` varchar(100) NOT NULL default '' AFTER `status_id`;

ALTER TABLE `ktvs_admin_notifications` ADD COLUMN `details` TEXT NOT NULL AFTER `objects`;

ALTER TABLE `ktvs_bill_log` ADD COLUMN `satellite_prefix` varchar(255) NOT NULL DEFAULT '' AFTER `record_id`;
ALTER TABLE `ktvs_bill_log` ADD KEY `satellite_prefix` (`satellite_prefix`);

ALTER TABLE `ktvs_users` ADD COLUMN `last_session_id_hash` varchar(32) NOT NULL DEFAULT '' AFTER `last_online_date`;

ALTER TABLE `ktvs_formats_videos`
  ADD COLUMN `watermark_offset_random` VARCHAR(10) NOT NULL AFTER `watermark_position_id`,
  ADD COLUMN `watermark2_offset_random` VARCHAR(10) NOT NULL AFTER `watermark2_position_id`;

ALTER TABLE `ktvs_formats_videos`
  ADD COLUMN `watermark_dynamic_switches` INT NOT NULL AFTER `watermark_scrolling_times`,
  ADD COLUMN `watermark2_dynamic_switches` INT NOT NULL AFTER `watermark2_scrolling_times`;

UPDATE `ktvs_admin_processes` SET exec_interval=1800 WHERE pid='cron_optimize';

ALTER TABLE `ktvs_admin_servers`
  ADD COLUMN `s3_api_secret` VARCHAR(150) NOT NULL DEFAULT '' AFTER `ftp_force_ssl`,
  ADD COLUMN `s3_api_key` VARCHAR(150) NOT NULL DEFAULT '' AFTER `ftp_force_ssl`,
  ADD COLUMN `s3_bucket` VARCHAR(150) NOT NULL DEFAULT '' AFTER `ftp_force_ssl`,
  ADD COLUMN `s3_endpoint` VARCHAR(150) NOT NULL DEFAULT '' AFTER `ftp_force_ssl`,
  ADD COLUMN `s3_region` VARCHAR(150) NOT NULL DEFAULT '' AFTER `ftp_force_ssl`;

UPDATE `ktvs_admin_permissions` SET sort_id=40 where title='albums|manage_images';
UPDATE `ktvs_admin_permissions` SET sort_id=50 where title='albums|import';
UPDATE `ktvs_admin_permissions` SET sort_id=60 where title='albums|export';
UPDATE `ktvs_admin_permissions` SET sort_id=70 where title='albums|delete';

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('videos|mass_edit', '35','200');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|mass_edit', '35','300');

ALTER TABLE `ktvs_admin_users` ADD COLUMN `content_delete_daily_limit` INT UNSIGNED NOT NULL DEFAULT 30 AFTER `is_access_to_content_flagged_with`;

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('GENERATED_USERS_REUSE_PROBABILITY','30');

UPDATE `ktvs_options` SET `value`='6.1.0' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';

UPDATE `ktvs_options` SET `value`='6.1.0' WHERE `value` = '6.0.1' AND `variable` = 'SYSTEM_VERSION';