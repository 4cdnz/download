ALTER TABLE `ktvs_models` ADD COLUMN `gallery_url` VARCHAR(255) NOT NULL AFTER `access_level_id`;
ALTER TABLE `ktvs_models` ADD KEY `gallery_url` (`gallery_url`);

ALTER TABLE `ktvs_dvds` ADD COLUMN `release_year` int(10) unsigned NOT NULL AFTER `status_id`;
ALTER TABLE `ktvs_dvds` ADD KEY `release_year` (`release_year`);

ALTER TABLE `ktvs_dvds_groups` DROP COLUMN `total_dvds`;

ALTER TABLE `ktvs_content_sources` ADD COLUMN `synonyms` text NOT NULL AFTER `description`;

ALTER TABLE `ktvs_formats_videos`
  ADD COLUMN `customize_preroll_video_id` tinyint(2) NOT NULL AFTER `preroll_video_uploaded`,
  ADD COLUMN `customize_postroll_video_id` tinyint(2) NOT NULL AFTER `postroll_video_uploaded`;

ALTER TABLE `ktvs_admin_conversion_servers` ADD COLUMN `is_allow_any_tasks` tinyint(1) unsigned NOT NULL AFTER `task_types`;
UPDATE ktvs_admin_conversion_servers SET `is_allow_any_tasks`=1;

ALTER TABLE `ktvs_admin_users` ADD COLUMN `is_old_list_navigation` tinyint(1) unsigned NOT NULL AFTER `is_popups_enabled`;

INSERT INTO `ktvs_admin_permissions`(`title`,`sort_id`,`group_sort_id`) VALUES ('plugins|digiregs',                 '29','2100');

ALTER TABLE `ktvs_videos_advanced_operations` ADD COLUMN `operation_data` TEXT NOT NULL AFTER `operation_task_id`;

ALTER TABLE `ktvs_albums_images` CHANGE COLUMN `format` `format` varchar(4) NOT NULL;

ALTER TABLE `ktvs_admin_system_log` CHANGE COLUMN `process_id` `process_id` bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `ktvs_admin_system_log` ADD COLUMN `event_trace` TEXT AFTER `event_details`;

CREATE TABLE `ktvs_admin_system_extensions` (
    `file_path` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`file_path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

UPDATE `ktvs_options` SET `value`='5.5.0' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';

UPDATE `ktvs_options` SET `value`='5.5.0' WHERE `value`='5.4.0' AND `variable`='SYSTEM_VERSION';