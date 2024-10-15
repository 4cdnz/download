ALTER TABLE `ktvs_formats_videos`
  ADD COLUMN `postroll_video_uploaded` tinyint(1) NOT NULL AFTER `customize_offset_end_id`,
  ADD COLUMN `preroll_video_uploaded` tinyint(1) NOT NULL AFTER `customize_offset_end_id`;

ALTER TABLE `ktvs_background_tasks` ADD COLUMN `last_server_id` int(10) unsigned NOT NULL DEFAULT '0' AFTER `server_id`;

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('FAILED_TASKS_AUTO_RESTART','0');

ALTER TABLE `ktvs_background_tasks` ADD COLUMN `times_restarted` tinyint(2) unsigned NOT NULL DEFAULT '0' AFTER `error_code`;

ALTER TABLE `ktvs_file_history` ADD COLUMN `is_modified` TINYINT(1) UNSIGNED NOT NULL AFTER `file_content`;
UPDATE `ktvs_file_history` INNER JOIN (SELECT `ktvs_file_history`.`path`, max(`ktvs_file_history`.`version`) AS `version` FROM `ktvs_file_history` INNER JOIN `ktvs_file_changes` USING (`path`) WHERE `ktvs_file_changes`.`is_modified`=1 GROUP BY `path`) t2 USING (`path`, `version`) SET `is_modified`=1;

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ENABLE_VIDEO_FLAG_1','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ENABLE_VIDEO_FLAG_2','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ENABLE_VIDEO_FLAG_3','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ENABLE_ALBUM_FLAG_1','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ENABLE_ALBUM_FLAG_2','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ENABLE_ALBUM_FLAG_3','0');

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('VIDEO_FLAG_1_NAME','');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('VIDEO_FLAG_2_NAME','');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('VIDEO_FLAG_3_NAME','');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ALBUM_FLAG_1_NAME','');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ALBUM_FLAG_2_NAME','');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ALBUM_FLAG_3_NAME','');

UPDATE `ktvs_admin_permissions` SET `sort_id`='30' WHERE `title`='posts|delete';
UPDATE `ktvs_admin_permissions` SET `sort_id`='40' WHERE `title`='albums|delete';
UPDATE `ktvs_admin_permissions` SET `sort_id`='50' WHERE `title`='albums|manage_images';
UPDATE `ktvs_admin_permissions` SET `sort_id`='60' WHERE `title`='albums|import';
UPDATE `ktvs_admin_permissions` SET `sort_id`='70' WHERE `title`='albums|export';
UPDATE `ktvs_admin_permissions` SET `sort_id`='7' WHERE `title`='videos|edit_post_date';
UPDATE `ktvs_admin_permissions` SET `sort_id`='8' WHERE `title`='videos|edit_user';
UPDATE `ktvs_admin_permissions` SET `sort_id`='9' WHERE `title`='videos|edit_status';
UPDATE `ktvs_admin_permissions` SET `sort_id`='10' WHERE `title`='videos|edit_type';
UPDATE `ktvs_admin_permissions` SET `sort_id`='11' WHERE `title`='videos|edit_access_level';
UPDATE `ktvs_admin_permissions` SET `sort_id`='12' WHERE `title`='videos|edit_tokens';
UPDATE `ktvs_admin_permissions` SET `sort_id`='13' WHERE `title`='videos|edit_release_year';
UPDATE `ktvs_admin_permissions` SET `sort_id`='18' WHERE `title`='videos|edit_dvd';
UPDATE `ktvs_admin_permissions` SET `sort_id`='19' WHERE `title`='videos|edit_content_source';
UPDATE `ktvs_admin_permissions` SET `sort_id`='20' WHERE `title`='videos|edit_categories';
UPDATE `ktvs_admin_permissions` SET `sort_id`='21' WHERE `title`='videos|edit_tags';
UPDATE `ktvs_admin_permissions` SET `sort_id`='22' WHERE `title`='videos|edit_models';
UPDATE `ktvs_admin_permissions` SET `sort_id`='23' WHERE `title`='videos|edit_flags';
UPDATE `ktvs_admin_permissions` SET `sort_id`='24' WHERE `title`='videos|edit_custom';
UPDATE `ktvs_admin_permissions` SET `sort_id`='25' WHERE `title`='videos|edit_admin_flag';
UPDATE `ktvs_admin_permissions` SET `sort_id`='40' WHERE `title`='videos|manage_screenshots';
UPDATE `ktvs_admin_permissions` SET `sort_id`='50' WHERE `title`='videos|import';
UPDATE `ktvs_admin_permissions` SET `sort_id`='60' WHERE `title`='videos|export';
UPDATE `ktvs_admin_permissions` SET `sort_id`='70' WHERE `title`='videos|delete';
UPDATE `ktvs_admin_permissions` SET `sort_id`='80' WHERE `title`='videos|feeds_import';
UPDATE `ktvs_admin_permissions` SET `sort_id`='90' WHERE `title`='videos|feeds_export';

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('videos|edit_is_locked',            '26','200');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('videos|edit_storage',              '27','200');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('videos|edit_connected_data',       '28','200');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('videos|edit_video_files',          '29','200');

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('posts|edit_is_locked',             '17','500');
UPDATE `ktvs_admin_permissions` SET `sort_id`='8' WHERE `title`='posts|edit_post_date';
UPDATE `ktvs_admin_permissions` SET `sort_id`='9' WHERE `title`='posts|edit_user';
UPDATE `ktvs_admin_permissions` SET `sort_id`='10' WHERE `title`='posts|edit_status';
UPDATE `ktvs_admin_permissions` SET `sort_id`='11' WHERE `title`='posts|edit_type';
UPDATE `ktvs_admin_permissions` SET `sort_id`='12' WHERE `title`='posts|edit_categories';
UPDATE `ktvs_admin_permissions` SET `sort_id`='13' WHERE `title`='posts|edit_tags';

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_title',                '4', '300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_dir',                  '5', '300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_description',          '6', '300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_post_date',            '7', '300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_user',                 '8', '300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_status',               '9', '300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_type',                 '10','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_access_level',         '11','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_tokens',               '12','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_content_source',       '13','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_categories',           '14','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_tags',                 '15','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_models',               '16','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_flags',                '17','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_custom',               '18','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_admin_flag',           '19','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_is_locked',            '20','300');
INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('albums|edit_storage',              '21','300');

ALTER TABLE `ktvs_users`
  ADD COLUMN `total_posts_count_by_type` varchar(255) NOT NULL AFTER `favourite_albums_count`,
  ADD COLUMN `total_posts_count` int(10) unsigned NOT NULL AFTER `favourite_albums_count`;

ALTER TABLE `ktvs_posts` DROP INDEX `title`, ADD FULLTEXT KEY `title` (`title`,`description`,`content`);

ALTER TABLE `ktvs_albums_images` ADD COLUMN `comments_count` INT(10) UNSIGNED NOT NULL AFTER `rating_amount`;
UPDATE `ktvs_albums_images` SET `comments_count`=(SELECT count(*) FROM ktvs_comments WHERE `object_sub_id`=`ktvs_albums_images`.`image_id` AND `object_type_id`=2);

ALTER TABLE `ktvs_admin_servers` CHANGE COLUMN `is_logging_enabled` `is_debug_enabled` TINYINT(1) UNSIGNED NOT NULL;
ALTER TABLE `ktvs_admin_conversion_servers` CHANGE COLUMN `is_logging_enabled` `is_debug_enabled` TINYINT(1) UNSIGNED NOT NULL;

UPDATE `ktvs_options` SET `variable`='USER_TASKS_VIDEOS_PRIORITY_STANDARD' WHERE `variable`='USER_TASKS_PRIORITY_STANDARD';
UPDATE `ktvs_options` SET `variable`='USER_TASKS_VIDEOS_PRIORITY_TRUSTED' WHERE `variable`='USER_TASKS_PRIORITY_TRUSTED';
UPDATE `ktvs_options` SET `variable`='USER_TASKS_VIDEOS_PRIORITY_WEBMASTER' WHERE `variable`='USER_TASKS_PRIORITY_WEBMASTER';
UPDATE `ktvs_options` SET `variable`='USER_TASKS_VIDEOS_PRIORITY_PREMIUM' WHERE `variable`='USER_TASKS_PRIORITY_PREMIUM';
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('USER_TASKS_ALBUMS_PRIORITY_STANDARD','10');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('USER_TASKS_ALBUMS_PRIORITY_TRUSTED','10');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('USER_TASKS_ALBUMS_PRIORITY_WEBMASTER','10');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('USER_TASKS_ALBUMS_PRIORITY_PREMIUM','10');

UPDATE `ktvs_options` SET `value`='5.3.0' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';
UPDATE `ktvs_options` SET `value`='5.3.0' WHERE `variable`='SYSTEM_STORAGE_API_VERSION';

ALTER TABLE `ktvs_videos` CHANGE COLUMN `has_errors` `has_errors` BIT(8) NOT NULL DEFAULT 0;
ALTER TABLE `ktvs_albums` CHANGE COLUMN `has_errors` `has_errors` BIT(8) NOT NULL DEFAULT 0;

CREATE TABLE `ktvs_admin_notifications` (
    `notification_id` VARCHAR(100) NOT NULL DEFAULT '',
    `objects` int(10) NOT NULL DEFAULT 0,
    PRIMARY KEY (`notification_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

UPDATE `ktvs_options` SET `value`='5.3.0' WHERE `value`='5.2.0' AND `variable`='SYSTEM_VERSION';