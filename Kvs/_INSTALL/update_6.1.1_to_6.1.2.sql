ALTER TABLE `ktvs_admin_servers` ADD COLUMN `s3_prefix` VARCHAR(150) NOT NULL DEFAULT '' AFTER `s3_bucket`;

UPDATE `ktvs_options` SET `value`='6.1.2' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';

UPDATE `ktvs_options` SET `value`='6.1.2' WHERE `value` = '6.1.1' AND `variable` = 'SYSTEM_VERSION';