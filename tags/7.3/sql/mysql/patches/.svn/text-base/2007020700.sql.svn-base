ALTER TABLE `license` ADD `address_migrated` TINYINT( 3 ) NOT NULL;
CREATE TABLE `address_businesscards_info` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`address_id` INT( 11 ) NOT NULL ,
`bcard_id` INT( 11 ) NOT NULL
) ENGINE = MYISAM;
ALTER TABLE `address_businesscards_info` ADD INDEX ( `address_id` );
ALTER TABLE `address_businesscards_info` ADD INDEX ( `bcard_id` );