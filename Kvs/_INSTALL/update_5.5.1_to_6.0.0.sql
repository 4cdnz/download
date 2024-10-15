ALTER TABLE `ktvs_dvds`
    ADD COLUMN `is_locked` TINYINT(1) UNSIGNED NOT NULL AFTER `is_review_needed`,
    ADD COLUMN `ip` BIGINT(20) UNSIGNED NOT NULL AFTER `avg_videos_popularity`;

ALTER TABLE `ktvs_categories` ADD COLUMN `last_content_date` DATETIME NOT NULL AFTER `avg_posts_popularity`;
ALTER TABLE `ktvs_categories` ADD COLUMN `total_content_sources` INT(10) UNSIGNED NOT NULL AFTER `total_cs`;
UPDATE `ktvs_categories` SET `total_content_sources`=`total_cs`;

ALTER TABLE `ktvs_tags` ADD COLUMN `last_content_date` DATETIME NOT NULL AFTER `avg_posts_popularity`;
ALTER TABLE `ktvs_tags` ADD COLUMN `total_content_sources` INT(10) UNSIGNED NOT NULL AFTER `total_cs`;
UPDATE `ktvs_tags` SET `total_content_sources`=`total_cs`;

ALTER TABLE `ktvs_posts`
    ADD COLUMN `content_source_id` INT(10) UNSIGNED NOT NULL AFTER `status_id`,
    ADD COLUMN `delete_reason` TEXT NOT NULL AFTER `ip`,
    ADD COLUMN `admin_flag_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `admin_user_id`,
    ADD COLUMN `is_private` TINYINT(1) UNSIGNED NOT NULL AFTER `status_id`,
    ADD COLUMN `access_level_id` TINYINT(1) UNSIGNED NOT NULL AFTER `is_private`,
    ADD COLUMN `tokens_required` INT(10) UNSIGNED NOT NULL AFTER `access_level_id`,
    ADD COLUMN `purchases_count` INT(10) UNSIGNED NOT NULL AFTER `comments_count`,
    ADD COLUMN `favourites_count` INT(10) UNSIGNED NOT NULL AFTER `comments_count`;

ALTER TABLE `ktvs_posts`
    ADD KEY `is_private` (`is_private`),
    ADD KEY `content_source_id` (`content_source_id`);

ALTER TABLE `ktvs_content_sources`
    ADD COLUMN `total_posts` INT (10) UNSIGNED NOT NULL AFTER `total_photos`,
    ADD COLUMN `today_posts` INT (10) UNSIGNED NOT NULL AFTER `total_posts`;

ALTER TABLE `ktvs_content_sources`
    ADD COLUMN `avg_posts_rating` FLOAT NOT NULL AFTER `avg_albums_popularity`,
    ADD COLUMN `avg_posts_popularity` FLOAT NOT NULL AFTER `avg_posts_rating`;

ALTER TABLE `ktvs_log_logins` ADD COLUMN `country_code` varchar(3) NOT NULL DEFAULT '' AFTER `ip`;
ALTER TABLE `ktvs_admin_users` ADD COLUMN `last_country_code` varchar(3) NOT NULL DEFAULT '' AFTER `last_ip`;
UPDATE `ktvs_admin_users` SET status_id=1 WHERE is_superadmin IN (1,2);

CREATE TABLE `ktvs_admin_users_settings` (
    `user_id` int(10) unsigned NOT NULL,
    `section` VARCHAR(30) NOT NULL DEFAULT '',
    `type` VARCHAR(30) NOT NULL DEFAULT '',
    `title` VARCHAR(100) NOT NULL DEFAULT '',
    `setting` TEXT NOT NULL,
    PRIMARY KEY (`user_id`, `section`, `type`, `title`),
    KEY (`user_id`, `section`, `type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

ALTER TABLE `ktvs_users` ADD COLUMN `is_manual_purchase_approval` TINYINT(1) UNSIGNED NOT NULL AFTER `tokens_required`;
ALTER TABLE `ktvs_dvds` ADD COLUMN `is_manual_purchase_approval` TINYINT(1) UNSIGNED NOT NULL AFTER `tokens_required`;

insert into `ktvs_card_bill_providers`(`status_id`,`internal_id`,`title`,`url`,`cf_pkg_trials`,`cf_pkg_rebills`,`cf_pkg_tokens`,`cf_pkg_oneclick`,`cf_pkg_setprice`) values (0, 'coinpayments', 'CoinPayments', 'https://www.coinpayments.net', 0, 0, 1, 0, 1);

ALTER TABLE `ktvs_stats_in` ADD PRIMARY KEY (`referer_id`, `country_code`, `device`, `added_date`);
ALTER TABLE `ktvs_stats_adv_out` ADD PRIMARY KEY (`advertisement_id`,`referer_id`,`country_code`,`device`,`added_date`);
ALTER TABLE `ktvs_stats_cs_out` ADD PRIMARY KEY (`content_source_id`,`referer_id`,`country_code`,`device`,`added_date`);
ALTER TABLE `ktvs_stats_embed` ADD PRIMARY KEY (`domain`,`added_date`);
ALTER TABLE `ktvs_stats_overload_protection` DROP KEY `added_date`;
ALTER TABLE `ktvs_stats_overload_protection` ADD PRIMARY KEY (`added_date`);
ALTER TABLE `ktvs_stats_player` ADD PRIMARY KEY (`referer_id`, `country_code`, `device`, `added_date`, `is_embed`, `embed_profile_id`);
ALTER TABLE `ktvs_bill_outs` DROP KEY `added_date`;
ALTER TABLE `ktvs_bill_outs` ADD PRIMARY KEY (`added_date`);
ALTER TABLE `ktvs_admin_system_log` ADD COLUMN `record_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`record_id`);
ALTER TABLE `ktvs_users_blocked_passwords` ADD PRIMARY KEY (`user_id`, `pass`);
ALTER TABLE `ktvs_users_ignores` ADD PRIMARY KEY (`user_id`, `ignored_user_id`);

ALTER TABLE `ktvs_background_tasks_history` ADD KEY `server_id` (`server_id`);
ALTER TABLE `ktvs_background_tasks_history` ADD KEY `video_id` (`video_id`);
ALTER TABLE `ktvs_background_tasks_history` ADD KEY `album_id` (`album_id`);

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('ANTISPAM_BLACKLIST_WORDS_IGNORE_FEEDBACKS','0');

ALTER TABLE `ktvs_admin_servers` ADD COLUMN `warning_id` TINYINT(1) unsigned NOT NULL AFTER `error_iteration`;

ALTER TABLE `ktvs_stats_player` ADD COLUMN `player_total` int(10) unsigned NOT NULL AFTER `embed_profile_id`;
UPDATE `ktvs_stats_player` set player_total=player_loads;
ALTER TABLE `ktvs_stats_player` ADD COLUMN `popunder_ad_clicks` int(10) unsigned NOT NULL AFTER `pause_ad_errors`;

ALTER TABLE `ktvs_admin_users` ADD COLUMN `custom_css` text NOT NULL AFTER `preference`;

UPDATE `ktvs_options` SET `value`='6.0.0' WHERE `variable`='SYSTEM_CONVERSION_API_VERSION';

UPDATE `ktvs_options` SET `value`='6.0.0' WHERE `value` = '5.5.1' AND `variable` = 'SYSTEM_VERSION';