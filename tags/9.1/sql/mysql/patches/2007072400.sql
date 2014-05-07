ALTER TABLE `cms_license` ADD `cms_shop` TINYINT( 3 ) NOT NULL ;
ALTER TABLE `cms_data` ADD `isShop` TINYINT( 3 ) NOT NULL ;
ALTER TABLE `cms_data` ADD `shopPrice` FLOAT( 16, 2 ) NOT NULL ;