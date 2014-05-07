ALTER TABLE `mail_messages_data` DROP INDEX `mail_id`;
ALTER TABLE `mail_messages_data` ADD PRIMARY KEY ( `mail_id` );
ALTER TABLE `mail_messages_data` ADD INDEX ( `body` ( 255 ) );
ALTER TABLE `mail_messages_data` ADD INDEX ( `header` ( 255 ) );
