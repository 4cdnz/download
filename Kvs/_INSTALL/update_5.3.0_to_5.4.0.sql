ALTER TABLE `ktvs_admin_conversion_servers` ADD COLUMN `ftp_force_ssl` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `ftp_timeout`;
ALTER TABLE `ktvs_admin_servers` ADD COLUMN `ftp_force_ssl` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `ftp_timeout`;

ALTER TABLE `ktvs_card_bill_packages` ADD COLUMN `satellite_prefix` varchar(255) NOT NULL AFTER `exclude_countries`;

CREATE TABLE `ktvs_admin_processes` (
    `pid` VARCHAR(100) NOT NULL,
    `last_exec_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `last_exec_duration` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `exec_interval` INT(10) UNSIGNED NOT NULL,
    `exec_tod` TINYINT(2) UNSIGNED NOT NULL,
    `status_data` TEXT NOT NULL,
    PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

CREATE TABLE `ktvs_videos_advanced_operations` (
    `video_id` INT(10) UNSIGNED NOT NULL,
    `operation_type_id` TINYINT(2) UNSIGNED NOT NULL,
    `operation_status_id` TINYINT(2) UNSIGNED NOT NULL,
    `operation_task_id` VARCHAR(100) NOT NULL DEFAULT '',
    `added_date` DATETIME NOT NULL,
    `finished_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    KEY `video_id` (`video_id`),
    PRIMARY KEY (`video_id`, `operation_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('plugins|neuroscore',               '28','2100');

ALTER TABLE `ktvs_admin_conversion_servers` ADD COLUMN `task_types` TEXT NOT NULL AFTER `max_tasks`;

ALTER TABLE `ktvs_videos_feeds_import` ADD COLUMN `videos_admin_flag_id` INT(10) UNSIGNED NOT NULL AFTER `videos_dvd_id`;

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_FEEDBACKS_ANALYZE_HISTORY','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_FEEDBACKS_AUTODELETE','0/0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_FEEDBACKS_ERROR','0/0');

ALTER TABLE `ktvs_messages` ADD COLUMN `is_spam` TINYINT(1) UNSIGNED NOT NULL AFTER `is_read`;
ALTER TABLE `ktvs_messages` ADD KEY `is_spam` (`is_spam`);

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('users|manage_awards',              '6', '1400');

UPDATE `ktvs_admin_permissions` SET `sort_id`=7 WHERE `title`='users|manage_blogs';
UPDATE `ktvs_admin_permissions` SET `sort_id`=8 WHERE `title`='users|emailings';

DELETE FROM `ktvs_admin_permissions` WHERE `title`='plugins|awe_black_label';

UPDATE ktvs_stats_search SET query_length=char_length(`query`);

UPDATE `ktvs_options` SET `value`='5.4.0' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';

UPDATE `ktvs_options` SET `value`='5.4.0' WHERE `value`='5.3.0' AND `variable`='SYSTEM_VERSION';