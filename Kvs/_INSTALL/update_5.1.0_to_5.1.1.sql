INSERT INTO `ktvs_card_bill_providers`(`status_id`,`internal_id`,`title`,`url`,`cf_pkg_trials`,`cf_pkg_rebills`,`cf_pkg_tokens`,`cf_pkg_oneclick`,`cf_pkg_setprice`) VALUES (0, 'natsum', 'NATS User Management', 'http://toomuchmedia.com', 1, 1, 0, 0, 0);
UPDATE `ktvs_card_bill_providers` SET `title`='NATS Transactions' WHERE `internal_id`='nats';

INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('TAGS_ADD_SYNONYMS_ON_RENAME','1');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('FILE_DOWNLOAD_SPEED_LIMIT','');
INSERT INTO `ktvs_options`(`variable`,`value`) VALUES ('AWARDS_REFERRAL_SIGNUP_CONDITION','');

ALTER TABLE `ktvs_card_bill_providers` ADD COLUMN `datalink_url` VARCHAR (255) NOT NULL AFTER `postback_repost_url`;

UPDATE ktvs_options SET `value`='5.1.1' WHERE `value`='5.1.0' AND `variable`='SYSTEM_VERSION';