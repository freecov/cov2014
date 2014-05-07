ALTER TABLE `cms_license` ADD `ideal_type` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE `cms_license` ADD `ideal_merchant_id` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE `cms_license` ADD `ideal_last_order` INT( 11 ) NOT NULL ;
ALTER TABLE `cms_license` ADD `ideal_currency` VARCHAR( 10 ) NOT NULL ;
ALTER TABLE `cms_license` ADD `ideal_secret_key` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `cms_license` ADD `ideal_test_mode` TINYINT( 3 ) NOT NULL ;
