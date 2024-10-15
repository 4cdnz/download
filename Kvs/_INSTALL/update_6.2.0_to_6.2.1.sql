ALTER TABLE `ktvs_fav_videos` ADD INDEX `video_id` (`video_id`);
ALTER TABLE `ktvs_fav_albums` ADD INDEX `album_id` (`album_id`);

DELETE FROM `ktvs_card_bill_providers` WHERE internal_id='zombaio' AND provider_id NOT IN (SELECT provider_id FROM ktvs_card_bill_packages);

ALTER TABLE `ktvs_admin_servers` ADD COLUMN `s3_is_endpoint_subdirectory` TINYINT NOT NULL DEFAULT 0 AFTER `s3_endpoint`;
ALTER TABLE `ktvs_admin_servers` ADD COLUMN `s3_timeout` INT NOT NULL DEFAULT 0 AFTER `s3_upload_chunk_size_mb`;

ALTER TABLE `ktvs_admin_satellites`
    ADD COLUMN `last_ping_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `state_id`,
    ADD COLUMN `added_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `state_id`;

UPDATE `ktvs_admin_satellites` SET added_date=now();

ALTER TABLE `ktvs_admin_servers` ADD COLUMN `streaming_skip_ssl_check` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `streaming_type_id`;

ALTER TABLE `ktvs_background_imports` ADD COLUMN `description` VARCHAR(255) NOT NULL DEFAULT '' AFTER `threads`;

UPDATE `ktvs_options` SET `value`='6.2.1' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';

UPDATE `ktvs_options` SET `value`='6.2.1' WHERE `value` = '6.2.0' AND `variable` = 'SYSTEM_VERSION';