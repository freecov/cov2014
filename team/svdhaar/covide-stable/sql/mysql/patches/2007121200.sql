ALTER TABLE `license` ADD `has_campaign` TINYINT( 3 ) NOT NULL ;
ALTER TABLE `users` ADD `xs_campaignmanage` TINYINT( 3 ) NOT NULL ;
CREATE TABLE `campaign` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`description` TEXT NOT NULL ,
`classifications` TEXT NOT NULL ,
`datetime` INT( 11 ) NOT NULL ,
`type` INT( 11 ) NOT NULL
) ;
CREATE TABLE `campaign_records` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`campaign_id` INT( 11 ) NOT NULL ,
`address_id` INT( 11 ) NOT NULL ,
`businesscard_id` INT( 11 ) NOT NULL ,
`is_called` INT( 11 ) NOT NULL ,
`answer` INT( 11 ) NOT NULL ,
`user_id` INT( 11 ) NOT NULL
) ;
ALTER TABLE `mail_tracking` ADD `campaign_id` INT( 11 ) NOT NULL ;
