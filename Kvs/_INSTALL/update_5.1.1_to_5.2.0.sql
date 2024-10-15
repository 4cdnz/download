INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('FILE_UPLOAD_SIZE_LIMIT','0');

ALTER TABLE `ktvs_stats_in` ADD COLUMN `device` TINYINT(3) NOT NULL AFTER `country_code`;
ALTER TABLE `ktvs_stats_in` ADD KEY `device` (`device`);

ALTER TABLE `ktvs_stats_cs_out` ADD COLUMN `device` TINYINT(3) NOT NULL AFTER `country_code`;
ALTER TABLE `ktvs_stats_cs_out` ADD KEY `device` (`device`);

ALTER TABLE `ktvs_stats_adv_out` ADD COLUMN `device` TINYINT(3) NOT NULL AFTER `country_code`;
ALTER TABLE `ktvs_stats_adv_out` ADD KEY `device` (`device`);

ALTER TABLE `ktvs_stats_player`
    ADD COLUMN `device` TINYINT(3) NOT NULL AFTER `country_code`,
    ADD COLUMN `embed_profile_id` varchar(32) NOT NULL default '' AFTER `is_embed`;
ALTER TABLE `ktvs_stats_player` ADD KEY `device` (`device`);
ALTER TABLE `ktvs_stats_player` ADD KEY `embed_profile_id` (`embed_profile_id`);

UPDATE `ktvs_options` SET `value`='5.2.0' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';
UPDATE `ktvs_options` SET `value`='5.2.0' WHERE `variable`='SYSTEM_STORAGE_API_VERSION';

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('system|antispam_settings',         '4', '2200');

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_BLACKLIST_WORDS','');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_BLACKLIST_ACTION','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_VIDEOS_ANALYZE_HISTORY','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_VIDEOS_FORCE_CAPTCHA','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_VIDEOS_FORCE_DISABLED','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_VIDEOS_AUTODELETE','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_VIDEOS_ERROR','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_ALBUMS_ANALYZE_HISTORY','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_ALBUMS_FORCE_CAPTCHA','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_ALBUMS_FORCE_DISABLED','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_ALBUMS_AUTODELETE','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_ALBUMS_ERROR','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_POSTS_ANALYZE_HISTORY','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_POSTS_FORCE_CAPTCHA','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_POSTS_FORCE_DISABLED','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_POSTS_AUTODELETE','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_POSTS_ERROR','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_PLAYLISTS_ANALYZE_HISTORY','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_PLAYLISTS_FORCE_CAPTCHA','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_PLAYLISTS_FORCE_DISABLED','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_PLAYLISTS_AUTODELETE','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_PLAYLISTS_ERROR','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_DVDS_ANALYZE_HISTORY','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_DVDS_FORCE_CAPTCHA','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_DVDS_FORCE_DISABLED','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_DVDS_AUTODELETE','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_DVDS_ERROR','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_COMMENTS_ANALYZE_HISTORY','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_COMMENTS_FORCE_CAPTCHA','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_COMMENTS_FORCE_DISABLED','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_COMMENTS_AUTODELETE','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_COMMENTS_ERROR','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_COMMENTS_DUPLICATES','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_MESSAGES_ANALYZE_HISTORY','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_MESSAGES_AUTODELETE','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_MESSAGES_ERROR','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_MESSAGES_DUPLICATES','0');

ALTER TABLE `ktvs_comments` ADD COLUMN `comment_md5` varchar(32) NOT NULL default '' AFTER `comment`;
ALTER TABLE `ktvs_comments` ADD KEY `comment_md5` (`comment_md5`);
ALTER TABLE `ktvs_comments` ADD KEY `ip` (`ip`);

ALTER TABLE `ktvs_messages` ADD COLUMN `message_md5` varchar(32) NOT NULL default '' AFTER `message`;
ALTER TABLE `ktvs_messages` ADD KEY `message_md5` (`message_md5`);

ALTER TABLE `ktvs_messages` ADD COLUMN `ip` bigint(20) unsigned NOT NULL AFTER `message_md5`;
ALTER TABLE `ktvs_messages` ADD KEY `ip` (`ip`);

ALTER TABLE `ktvs_deleted_content` ADD COLUMN `dir` varchar(255) NOT NULL default '' AFTER `object_type_id`;

ALTER TABLE `ktvs_formats_screenshots`
    ADD COLUMN `vertical_aspect_ratio_gravity` varchar(10) NOT NULL AFTER `aspect_ratio_id`,
    ADD COLUMN `vertical_aspect_ratio_id` tinyint(1) NOT NULL AFTER `aspect_ratio_id`,
    ADD COLUMN `aspect_ratio_gravity` varchar(10) NOT NULL AFTER `aspect_ratio_id`;

UPDATE `ktvs_formats_screenshots` SET `aspect_ratio_gravity`='', `vertical_aspect_ratio_id`=`aspect_ratio_id`, `vertical_aspect_ratio_gravity`=`aspect_ratio_gravity`;

ALTER TABLE `ktvs_formats_albums`
    ADD COLUMN `vertical_aspect_ratio_gravity` varchar(10) NOT NULL AFTER `aspect_ratio_id`,
    ADD COLUMN `vertical_aspect_ratio_id` tinyint(1) NOT NULL AFTER `aspect_ratio_id`,
    ADD COLUMN `aspect_ratio_gravity` varchar(10) NOT NULL AFTER `aspect_ratio_id`;

UPDATE `ktvs_formats_albums` SET `aspect_ratio_gravity`='', `vertical_aspect_ratio_id`=`aspect_ratio_id`, `vertical_aspect_ratio_gravity`=`aspect_ratio_gravity`;

ALTER TABLE `ktvs_formats_videos` ADD COLUMN `watermark_max_width_vertical` int(10) NOT NULL AFTER `watermark_max_width`;
UPDATE `ktvs_formats_videos` SET `watermark_max_width_vertical` = `watermark_max_width`;

ALTER TABLE `ktvs_formats_albums` ADD COLUMN `watermark_max_width_vertical` int(10) NOT NULL AFTER `watermark_max_width`;
UPDATE `ktvs_formats_albums` SET `watermark_max_width_vertical` = `watermark_max_width`;

ALTER TABLE `ktvs_formats_videos`
    ADD COLUMN `customize_watermark2_id` int(10) NOT NULL AFTER `customize_watermark_id`,
    ADD COLUMN `watermark2_scrolling_times` varchar(100) NOT NULL AFTER `customize_watermark_id`,
    ADD COLUMN `watermark2_scrolling_direction` tinyint(1) NOT NULL AFTER `customize_watermark_id`,
    ADD COLUMN `watermark2_scrolling_duration` int(10) NOT NULL AFTER `customize_watermark_id`,
    ADD COLUMN `watermark2_max_height_vertical` int(10) NOT NULL AFTER `customize_watermark_id`,
    ADD COLUMN `watermark2_max_height` int(10) NOT NULL AFTER `customize_watermark_id`,
    ADD COLUMN `watermark2_position_id` tinyint(1) NOT NULL AFTER `customize_watermark_id`;

ALTER TABLE `ktvs_admin_users` ADD COLUMN `status_id` tinyint(1) unsigned NOT NULL AFTER `group_id`;
UPDATE `ktvs_admin_users` SET `status_id`=1 WHERE `is_superadmin`=0;

ALTER TABLE `ktvs_flags` ADD COLUMN `is_admin_flag` tinyint(1) NOT NULL AFTER `external_id`;
UPDATE `ktvs_flags` SET `is_admin_flag`=1;

ALTER TABLE `ktvs_admin_users` ADD COLUMN `is_access_to_content_flagged_with` varchar(255) DEFAULT '' NOT NULL AFTER `is_access_to_disabled_content`;
ALTER TABLE `ktvs_admin_users_groups` ADD COLUMN `is_access_to_content_flagged_with` varchar(255) DEFAULT '' NOT NULL AFTER `is_access_to_disabled_content`;

ALTER TABLE `ktvs_stats_in` ADD COLUMN `view_player_amount` int(10) unsigned NOT NULL AFTER `view_video_amount`;

UPDATE `ktvs_videos` SET `load_type_id`=1 WHERE `load_type_id`=4;

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('SCREENSHOTS_CROP_TRIM_SIDES','1');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('SCREENSHOTS_UPLOADED_WATERMARK','0');
UPDATE `ktvs_options` SET `variable`='SCREENSHOTS_UPLOADED_CROP' WHERE `variable`='SCREENSHOTS_CROP_APPLY';

ALTER TABLE `ktvs_videos`
    CHANGE COLUMN `screen_main` `screen_main` int(10) NOT NULL,
    CHANGE COLUMN `screen_main_temp` `screen_main_temp` int(10) NOT NULL;

ALTER TABLE `ktvs_videos`
    ADD COLUMN `poster_main` int(10) NOT NULL AFTER `screen_main_temp`,
    ADD COLUMN `poster_amount` int(10) unsigned NOT NULL AFTER `screen_main_temp`;

ALTER TABLE `ktvs_background_tasks_postponed`
    ADD COLUMN `album_id` int (10) unsigned NOT NULL DEFAULT '0' AFTER `type_id`,
    ADD COLUMN `video_id` int (10) unsigned NOT NULL DEFAULT '0' AFTER `type_id`;

ALTER TABLE `ktvs_videos_feeds_export`
    ADD COLUMN `last_exec_duration` float NOT NULL AFTER `cache`,
    ADD COLUMN `last_exec_date` datetime NOT NULL AFTER `cache`;

ALTER TABLE `ktvs_videos_feeds_import`
    ADD COLUMN `data_configuration` text NOT NULL AFTER `feed_charset`;

UPDATE `ktvs_videos_feeds_import` SET `data_configuration` = `csv_configuration`;
UPDATE `ktvs_videos_feeds_import` SET `data_configuration` = 'a:1:{s:6:"fields";a:1:{i:0;s:3:"all";}}' WHERE feed_type_id='kvs';
UPDATE `ktvs_videos_feeds_import` SET `videos_adding_mode_id`=4 WHERE `videos_adding_mode_id`=5;

ALTER TABLE `ktvs_videos_feeds_import`
    ADD COLUMN `limit_terminology` text NOT NULL AFTER `limit_duration_to`,
    ADD COLUMN `limit_views_to` int(10) unsigned NOT NULL AFTER `limit_duration_to`,
    ADD COLUMN `limit_views_from` int(10) unsigned NOT NULL AFTER `limit_duration_to`,
    ADD COLUMN `limit_rating_to` int(10) unsigned NOT NULL AFTER `limit_duration_to`,
    ADD COLUMN `limit_rating_from` int(10) unsigned NOT NULL AFTER `limit_duration_to`,
    ADD COLUMN `keep_log_days` int(10) unsigned NOT NULL AFTER `is_debug_enabled`;

ALTER TABLE `ktvs_videos_feeds_import`
    ADD COLUMN `last_exec_videos_errored` int(10) unsigned NOT NULL AFTER `last_exec_date`,
    ADD COLUMN `last_exec_videos_skipped` int(10) unsigned NOT NULL AFTER `last_exec_date`,
    ADD COLUMN `last_exec_videos_added` int(10) unsigned NOT NULL AFTER `last_exec_date`,
    ADD COLUMN `last_exec_duration` int(10) unsigned NOT NULL AFTER `last_exec_date`;

UPDATE `ktvs_videos_feeds_import` SET `keep_log_days` = 90;

ALTER TABLE `ktvs_videos_feeds_import`
    ADD COLUMN `autodelete_last_exec_videos` int(10) NOT NULL AFTER `last_exec_videos_errored`,
    ADD COLUMN `autodelete_last_exec_duration` int(10) NOT NULL AFTER `last_exec_videos_errored`,
    ADD COLUMN `autodelete_last_exec_date` datetime NOT NULL AFTER `last_exec_videos_errored`,
    ADD COLUMN `autodelete_exec_interval` int(10) NOT NULL AFTER `last_exec_videos_errored`,
    ADD COLUMN `autodelete_url` text NOT NULL AFTER `last_exec_videos_errored`,
    ADD COLUMN `autodelete_reason` text NOT NULL AFTER `last_exec_videos_errored`,
    ADD COLUMN `autodelete_mode` tinyint(1) unsigned NOT NULL AFTER `last_exec_videos_errored`,
    ADD COLUMN `is_autodelete` tinyint(1) unsigned NOT NULL AFTER `last_exec_videos_errored`;

ALTER TABLE `ktvs_stats_search` ADD COLUMN `is_manual` tinyint(1) unsigned NOT NULL AFTER `query_results_total`;
ALTER TABLE `ktvs_stats_search` ADD KEY `is_manual` (`is_manual`);

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('plugins|awe_black_label',          '27','2100');

DELETE FROM `ktvs_stats_in` WHERE added_date>now();

UPDATE `ktvs_options` SET `value`='5.2.0' WHERE `value`='5.1.1' AND `variable`='SYSTEM_VERSION';