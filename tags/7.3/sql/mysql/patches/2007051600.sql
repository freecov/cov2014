ALTER TABLE `cms_data` ADD `isFeedback` TINYINT(3);
ALTER TABLE `cms_license` ADD `cms_feedback` TINYINT(3);
ALTER TABLE `cms_license` ADD `cms_user_register` TINYINT(3);
ALTER TABLE `cms_users` ADD `email` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `cms_users` ADD `registration_date` INT( 11 ) NOT NULL ;
ALTER TABLE `cms_users` ADD `is_active` TINYINT( 3 ) NOT NULL ;

CREATE TABLE `cms_feedback` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`page_id` INT( 11 ) NOT NULL ,
`datetime` INT( 11 ) NOT NULL ,
`user_id` INT( 11 ) NOT NULL ,
`subject` VARCHAR( 255 ) NOT NULL ,
`body` TEXT NOT NULL
) ENGINE = MYISAM ;
