CREATE TABLE `poll_items` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`polls_id` INT( 11 ) NOT NULL ,
	`position` INT NOT NULL ,
	`value` VARCHAR( 255 ) NOT NULL
); 
ALTER TABLE poll_answers ADD COLUMN item_id int(11);
