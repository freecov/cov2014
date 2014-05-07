CREATE TABLE `cms_banner_views` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`banner_id` INT( 11 ) NOT NULL ,
`datetime` INT( 11 ) NOT NULL ,
`visited` INT( 11 ) NOT NULL ,
`clicked` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;
