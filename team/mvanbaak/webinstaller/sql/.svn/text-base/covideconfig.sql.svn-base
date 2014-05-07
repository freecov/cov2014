CREATE TABLE `offices` (
`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`officename` VARCHAR( 255 ) NOT NULL ,
`officeurl` VARCHAR( 255 ) NOT NULL ,
`dsn` VARCHAR( 255 ) NOT NULL ,
`created` INT( 11 ) UNSIGNED NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `offices_urls` (
`offices_id` INT( 11 ) UNSIGNED NOT NULL ,
`officeurl` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `offices_id` , `officeurl` )
) ENGINE = MYISAM ;
