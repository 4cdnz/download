ALTER TABLE `ktvs_formats_screenshots` ADD COLUMN `image_type` tinyint(1) not null default '0' AFTER `size`;

DELETE FROM `ktvs_card_bill_providers` WHERE `internal_id` IN ('vendo', 'yandex', 'pay2pay') AND `provider_id` NOT IN (SELECT `provider_id` FROM `ktvs_card_bill_packages`);

CREATE TABLE `ktvs_admin_system_log` (
    `event_level` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `event_code` int(10) unsigned NOT NULL DEFAULT '0',
    `event_message` varchar(255) NOT NULL,
    `event_details` text,
    `process_id` int(10) unsigned NOT NULL DEFAULT '0',
    `process_name` varchar(255) NOT NULL DEFAULT '',
    `added_date` datetime NOT NULL,
    `added_microtime` int(10) NOT NULL,
    KEY `event_level` (`event_level`),
    KEY `process_id` (`process_id`),
    KEY `added_date` (`added_date`, `added_microtime`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

ALTER TABLE `ktvs_background_tasks`
    ADD COLUMN `progress` int(10) unsigned NOT NULL DEFAULT '0' AFTER `start_date`,
    ADD COLUMN `effective_duration` int(10) unsigned NOT NULL DEFAULT '0' AFTER `progress`;

ALTER TABLE `ktvs_background_tasks`
    CHANGE COLUMN `server_id` `server_id` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `video_id` `video_id` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `album_id` `album_id` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `last_processed_id` `last_processed_id` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `last_error_id` `last_error_id` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `message` `message` varchar(255) NOT NULL DEFAULT '',
    CHANGE COLUMN `error_code` `error_code` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `start_date` `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE COLUMN `data` `data` TEXT;

ALTER TABLE `ktvs_background_tasks_history`
    CHANGE COLUMN `server_id` `server_id` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `video_id` `video_id` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `album_id` `album_id` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `message` `message` varchar(255) NOT NULL DEFAULT '',
    CHANGE COLUMN `error_code` `error_code` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `start_date` `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE COLUMN `end_date` `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    CHANGE COLUMN `effective_duration` `effective_duration` int(10) NOT NULL DEFAULT '0',
    CHANGE COLUMN `data` `data` TEXT;

ALTER TABLE `ktvs_background_imports`
    CHANGE COLUMN `task_id` `task_id` int(10) unsigned NOT NULL DEFAULT '0',
    CHANGE COLUMN `threads` `threads` int(10) unsigned NOT NULL DEFAULT '1';

ALTER TABLE `ktvs_background_imports_data`
    CHANGE COLUMN `object_id` `object_id` int(10) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `ktvs_stats_player`
    ADD COLUMN `video_errors` int(10) unsigned NOT NULL AFTER `is_embed`,
    ADD COLUMN `video_ends` int(10) unsigned NOT NULL AFTER `is_embed`,
    ADD COLUMN `video_skips` int(10) unsigned NOT NULL AFTER `is_embed`,
    ADD COLUMN `video_pauses` int(10) unsigned NOT NULL AFTER `is_embed`,
    ADD COLUMN `video_starts` int(10) unsigned NOT NULL AFTER `is_embed`,
    ADD COLUMN `player_unmutes` int(10) unsigned NOT NULL AFTER `is_embed`,
    ADD COLUMN `player_mutes` int(10) unsigned NOT NULL AFTER `is_embed`,
    ADD COLUMN `player_fullscreens` int(10) unsigned NOT NULL AFTER `is_embed`,
    ADD COLUMN `player_loads` int(10) unsigned NOT NULL AFTER `is_embed`,
    ADD COLUMN `start_ad_errors` int(10) unsigned NOT NULL AFTER `start_ad_clicks`,
    ADD COLUMN `pre_ad_errors` int(10) unsigned NOT NULL AFTER `pre_ad_clicks`,
    ADD COLUMN `pre_ad_skips` int(10) unsigned NOT NULL AFTER `pre_ad_clicks`,
    ADD COLUMN `post_ad_errors` int(10) unsigned NOT NULL AFTER `post_ad_clicks`,
    ADD COLUMN `post_ad_skips` int(10) unsigned NOT NULL AFTER `post_ad_clicks`,
    ADD COLUMN `pause_ad_errors` int(10) unsigned NOT NULL AFTER `pause_ad_clicks`;

ALTER TABLE `ktvs_videos` ADD COLUMN `video_viewed_player` int(10) unsigned NOT NULL AFTER `video_viewed`;
ALTER TABLE `ktvs_stats_videos` ADD COLUMN `player_viewed` int(10) unsigned NOT NULL AFTER `viewed`;

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('TAGS_FORCE_DISABLED','0');

DELETE FROM `ktvs_admin_permissions` WHERE `title`='stats|view_referer_stats';

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ROTATOR_SCHEDULE_INTERVAL','15');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ROTATOR_SCHEDULE_PAUSE_FROM','');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ROTATOR_SCHEDULE_PAUSE_TO','');

ALTER TABLE `ktvs_bill_log` ADD COLUMN `is_postback` tinyint(1) unsigned NOT NULL AFTER `message_details`;

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('CRON_TIME','0');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('CRON_UID','');

UPDATE `ktvs_options` SET `value`='5.1.0' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';
UPDATE `ktvs_options` SET `value`='5.1.0' WHERE `variable`='SYSTEM_STORAGE_API_VERSION';

UPDATE ktvs_options SET `value`='5.1.0' WHERE `value`='5.0.1' AND `variable`='SYSTEM_VERSION';