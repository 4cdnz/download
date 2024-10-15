ALTER TABLE `ktvs_admin_servers` ADD COLUMN `s3_upload_chunk_size_mb` INT NOT NULL DEFAULT 0 AFTER `s3_api_secret`;

ALTER TABLE `ktvs_videos` DROP KEY `is_hd`;
ALTER TABLE `ktvs_videos` ADD COLUMN `resolution_type` TINYINT UNSIGNED NOT NULL AFTER `status_id`;
UPDATE `ktvs_videos` SET `resolution_type`=`is_hd`;
ALTER TABLE `ktvs_videos` ADD KEY `resolution_type` (`resolution_type`);

DELETE FROM `ktvs_background_tasks` WHERE type_id=26;
DELETE FROM `ktvs_background_tasks_history` WHERE type_id=26;

ALTER TABLE `ktvs_stats_player` ADD COLUMN `video_rate_changes` INT(10) UNSIGNED NOT NULL AFTER `video_skips`;

UPDATE `ktvs_options` SET `value`='6.1.1' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';

UPDATE `ktvs_options` SET `value`='6.1.1' WHERE `value` = '6.1.0' AND `variable` = 'SYSTEM_VERSION';