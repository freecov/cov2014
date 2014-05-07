ALTER TABLE `cms_license_siteroots` ADD `recaptcha_private` VARCHAR( 255 ) NOT NULL ,
ADD `recaptcha_public` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `cms_license` ADD `recaptcha_private` VARCHAR( 255 ) NOT NULL ,
ADD `recaptcha_public` VARCHAR( 255 ) NOT NULL ;
