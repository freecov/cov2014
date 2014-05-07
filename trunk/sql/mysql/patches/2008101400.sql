ALTER TABLE `calendar` ADD `external_id` INT( 11 ) NOT NULL ;

CREATE TABLE `calendar_external` (
	`id` INT( 11 ) NOT NULL auto_increment,
	`userid` INT( 11 ) NOT NULL ,
	`url` VARCHAR( 255 ) NOT NULL ,
	PRIMARY KEY ( `id` ) ,
	INDEX ( `userid` ) ,
	UNIQUE ( `url`)
) ENGINE = MYISAM ;

