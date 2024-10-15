insert into `ktvs_options`(`variable`,`value`) select 'ENABLE_TOKENS_MESSAGES_ACTIVE', `value` from `ktvs_options` where variable='ENABLE_TOKENS_INTERNAL_MESSAGES';
insert into `ktvs_options`(`variable`,`value`) select 'TOKENS_MESSAGES_ACTIVE', `value` from `ktvs_options` where variable='TOKENS_INTERNAL_MESSAGES';
insert into `ktvs_options`(`variable`,`value`) select 'ENABLE_TOKENS_MESSAGES_PREMIUM', `value` from `ktvs_options` where variable='ENABLE_TOKENS_INTERNAL_MESSAGES';
insert into `ktvs_options`(`variable`,`value`) select 'TOKENS_MESSAGES_PREMIUM', `value` from `ktvs_options` where variable='TOKENS_INTERNAL_MESSAGES';
insert into `ktvs_options`(`variable`,`value`) select 'ENABLE_TOKENS_MESSAGES_WEBMASTERS', `value` from `ktvs_options` where variable='ENABLE_TOKENS_INTERNAL_MESSAGES';
insert into `ktvs_options`(`variable`,`value`) select 'TOKENS_MESSAGES_WEBMASTERS', `value` from `ktvs_options` where variable='TOKENS_INTERNAL_MESSAGES';
insert into `ktvs_options`(`variable`,`value`) values ('ENABLE_TOKENS_MESSAGES_REVENUE','0');
insert into `ktvs_options`(`variable`,`value`) values ('TOKENS_MESSAGES_REVENUE_INTEREST','0');

ALTER TABLE `ktvs_users_purchases` ADD COLUMN `messages` int(10) unsigned NOT NULL AFTER `subscription_id`;

UPDATE `ktvs_options` SET `value`='6.0.1' WHERE `value` = '6.0.0' AND `variable` = 'SYSTEM_VERSION';