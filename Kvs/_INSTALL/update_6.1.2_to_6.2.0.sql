CREATE TABLE `ktvs_formats_videos_groups` (
    `format_video_group_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `is_default` TINYINT NOT NULL DEFAULT 0,
    `is_premium` TINYINT NOT NULL DEFAULT 0,
    `set_duration_from` VARCHAR(32) NOT NULL DEFAULT '',
    `added_date` DATETIME NOT NULL,
    PRIMARY KEY (`format_video_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO `ktvs_formats_videos_groups` SET format_video_group_id=1, title='Standard', added_date=NOW(), is_default=1, set_duration_from=(SELECT value FROM ktvs_options WHERE variable='TAKE_VIDEO_DURATION_FROM_FORMAT_STD');
INSERT INTO `ktvs_formats_videos_groups` SET format_video_group_id=2, title='Premium', added_date=NOW(), is_premium=1, set_duration_from=(SELECT value FROM ktvs_options WHERE variable='TAKE_VIDEO_DURATION_FROM_FORMAT_PREMIUM');

ALTER TABLE `ktvs_videos` ADD COLUMN `format_video_group_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `load_type_id`;
UPDATE `ktvs_videos` SET format_video_group_id=1 WHERE format_video_group_id=0 AND is_private IN (0, 1) AND load_type_id=1;
UPDATE `ktvs_videos` SET format_video_group_id=2 WHERE format_video_group_id=0 AND is_private=2 AND load_type_id=1;
ALTER TABLE `ktvs_videos` ADD KEY `format_video_group_id` (`format_video_group_id`);

ALTER TABLE `ktvs_formats_videos` ADD COLUMN `format_video_group_id` INT NOT NULL DEFAULT 0 AFTER `is_use_as_source`;
UPDATE `ktvs_formats_videos` SET format_video_group_id=video_type_id+1 WHERE format_video_group_id=0;

DELETE FROM `ktvs_formats_videos_groups` WHERE format_video_group_id NOT IN (SELECT `format_video_group_id` FROM `ktvs_formats_videos`);

ALTER TABLE `ktvs_videos_feeds_import` ADD COLUMN `format_video_group_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `format_video_id`;

ALTER TABLE `ktvs_background_tasks_history` ADD KEY `start_date` (`start_date`);

INSERT INTO `ktvs_options`(`variable`,`value`) SELECT 'ENABLE_TOKENS_PUBLIC_VIDEO', `value` FROM `ktvs_options` WHERE `variable`='ENABLE_TOKENS_STANDARD_VIDEO';
INSERT INTO `ktvs_options`(`variable`,`value`) SELECT 'ENABLE_TOKENS_PRIVATE_VIDEO', `value` FROM `ktvs_options` WHERE `variable`='ENABLE_TOKENS_STANDARD_VIDEO';
INSERT INTO `ktvs_options`(`variable`,`value`) SELECT 'ENABLE_TOKENS_PUBLIC_ALBUM', `value` FROM `ktvs_options` WHERE `variable`='ENABLE_TOKENS_STANDARD_ALBUM';
INSERT INTO `ktvs_options`(`variable`,`value`) SELECT 'ENABLE_TOKENS_PRIVATE_ALBUM', `value` FROM `ktvs_options` WHERE `variable`='ENABLE_TOKENS_STANDARD_ALBUM';
INSERT INTO `ktvs_options`(`variable`,`value`) SELECT 'DEFAULT_TOKENS_PUBLIC_VIDEO', `value` FROM `ktvs_options` WHERE `variable`='DEFAULT_TOKENS_STANDARD_VIDEO';
INSERT INTO `ktvs_options`(`variable`,`value`) SELECT 'DEFAULT_TOKENS_PRIVATE_VIDEO', `value` FROM `ktvs_options` WHERE `variable`='DEFAULT_TOKENS_STANDARD_VIDEO';
INSERT INTO `ktvs_options`(`variable`,`value`) SELECT 'DEFAULT_TOKENS_PUBLIC_ALBUM', `value` FROM `ktvs_options` WHERE `variable`='DEFAULT_TOKENS_STANDARD_ALBUM';
INSERT INTO `ktvs_options`(`variable`,`value`) SELECT 'DEFAULT_TOKENS_PRIVATE_ALBUM', `value` FROM `ktvs_options` WHERE `variable`='DEFAULT_TOKENS_STANDARD_ALBUM';

ALTER TABLE `ktvs_log_logins_users` ADD COLUMN `full_ip` VARCHAR(45) NOT NULL DEFAULT '' AFTER `ip`;
ALTER TABLE `ktvs_log_logins_users` ADD KEY `full_ip` (`full_ip`);
ALTER TABLE `ktvs_log_logins_users` ADD KEY `user_id2` (`is_failed`, `user_id`, `login_date`);

ALTER TABLE `ktvs_log_logins` ADD COLUMN `full_ip` VARCHAR(45) NOT NULL DEFAULT '' AFTER `ip`;
ALTER TABLE `ktvs_log_logins` ADD KEY `full_ip` (`full_ip`);

CREATE TABLE `ktvs_lookups` (
    `lookup_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `object_type_id` INT UNSIGNED NOT NULL,
    `field_name` VARCHAR(100) NOT NULL,
    `value` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `titles` TEXT NOT NULL,
    `added_date` DATETIME NOT NULL,
    PRIMARY KEY (`lookup_id`),
    UNIQUE KEY `value` (`object_type_id`, `field_name`, `value`),
    KEY `object_type_id` (`object_type_id`),
    KEY `field_name` (`object_type_id`, `field_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('system|lookups', '13','2200');

ALTER TABLE `ktvs_models` CHANGE COLUMN `alias` `alias` text NOT NULL;

ALTER TABLE `ktvs_models` ADD COLUMN `version_control` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `added_date`;
ALTER TABLE `ktvs_models` ADD COLUMN `country` CHAR(3) NOT NULL DEFAULT '' AFTER `country_id`;
UPDATE ktvs_models SET country=(SELECT country_code FROM ktvs_list_countries WHERE country_id=ktvs_models.country_id AND language_code='en') WHERE country_id>0;

ALTER TABLE `ktvs_stats_search` ADD UNIQUE KEY `query_md5` (`query_md5`);
ALTER TABLE `ktvs_stats_search` ADD COLUMN `search_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, DROP PRIMARY KEY, ADD PRIMARY KEY (`search_id`);

ALTER TABLE `ktvs_admin_conversion_servers` ADD COLUMN `max_tasks_priority` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `max_tasks`;

ALTER TABLE `ktvs_videos` ADD COLUMN `is_vertical` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `resolution_type`;
UPDATE `ktvs_videos` SET `is_vertical` = (CASE WHEN file_dimensions = '' THEN 0 ELSE (CASE WHEN CAST(SUBSTR(file_dimensions, 1, LOCATE('x', file_dimensions) - 1) AS UNSIGNED) < CAST(SUBSTR(file_dimensions, LOCATE('x', file_dimensions) + 1) as UNSIGNED) THEN 1 ELSE 0 END) END);
ALTER TABLE `ktvs_videos` ADD KEY `is_vertical` (`is_vertical`);

ALTER TABLE `ktvs_users` ADD COLUMN `ratings_content_sources_count` INT UNSIGNED NOT NULL AFTER `ratings_cs_count`;
UPDATE `ktvs_users` SET `ratings_content_sources_count`=`ratings_cs_count`;

ALTER TABLE `ktvs_content_sources` ADD COLUMN `version_control` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `added_date`;

ALTER TABLE `ktvs_users_subscriptions` ADD COLUMN `subscribed_object_type_id` TINYINT UNSIGNED NOT NULL AFTER `subscribed_object_id`;
UPDATE `ktvs_users_subscriptions` SET `subscribed_object_type_id`=`subscribed_type_id` WHERE `subscribed_type_id`!=0;
ALTER TABLE `ktvs_users_subscriptions` DROP KEY `subscribed_object_id`;
ALTER TABLE `ktvs_users_subscriptions` ADD KEY `subscribed_object_id` (`subscribed_object_id`,`subscribed_object_type_id`);
ALTER TABLE `ktvs_users_subscriptions` ADD UNIQUE KEY `subscription` (`user_id`, `subscribed_object_id`,`subscribed_object_type_id`);

insert into `ktvs_options`(`variable`,`value`) values ('SCREENSHOTS_MERGE_VERTICAL','0');

ALTER TABLE `ktvs_log_content_users` ADD COLUMN `format_info` VARCHAR(32) NOT NULL AFTER `stream_to`;

UPDATE `ktvs_options` SET `value`='6.2.0' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';

UPDATE `ktvs_options` SET `value`='6.2.0' WHERE `value` = '6.1.2' AND `variable` = 'SYSTEM_VERSION';