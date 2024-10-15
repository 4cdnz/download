ALTER TABLE `ktvs_videos_feeds_import_history` ADD KEY `feed_id` (`feed_id`);

ALTER TABLE `ktvs_videos_feeds_import`
    ADD COLUMN `autopaginate_param` VARCHAR(50) NOT NULL AFTER `autodelete_last_exec_videos`,
    ADD COLUMN `is_autopaginate` TINYINT(1) UNSIGNED NOT NULL AFTER `autodelete_last_exec_videos`;

ALTER TABLE `ktvs_dvds` ADD COLUMN `synonyms` text NOT NULL AFTER `description`;

ALTER TABLE `ktvs_models` ADD INDEX `model_group_id` (`model_group_id`);

ALTER TABLE `ktvs_users_blocked_ips` CHANGE COLUMN `ip` `ip` varchar(40) NOT NULL;

UPDATE `ktvs_options` SET `value`='5.5.1' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';

UPDATE `ktvs_options` SET `value`='5.5.1' WHERE `value`='5.5.0' AND `variable`='SYSTEM_VERSION';