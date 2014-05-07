ALTER TABLE users ADD addressmode INT NULL DEFAULT 0;
ALTER TABLE sales ADD project_id INT NULL DEFAULT 0;
CREATE TABLE twinfield_settings (
	id int(11) NOT NULL auto_increment,
	username varchar(255),
	password varchar(255),
	offices varchar(255),
	company varchar(255),
	PRIMARY KEY (id)
);
CREATE TABLE `address_titles` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 255 ) NULL
) ENGINE = MYISAM ;
ALTER TABLE twinfield_settings ADD COLUMN default_office int(11) not null;
ALTER TABLE twinfield_settings DROP COLUMN offices;
alter table cms_gallery_photos add column rating int(11) default 0;
alter table cms_gallery_photos add column url text;
alter table cms_gallery_photos add column internal_stat int(11);
alter table cms_gallery_photos add index ( internal_stat );
alter table cms_gallery_photos add index ( rating );
alter table cms_gallery_photos add index ( pageid );
