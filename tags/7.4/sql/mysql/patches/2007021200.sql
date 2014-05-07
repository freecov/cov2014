ALTER TABLE `users` ADD `mail_signature_html` TEXT NOT NULL;
ALTER TABLE `mail_signatures` ADD `signature_html` TEXT NOT NULL;
ALTER TABLE `mail_signatures` ADD `default` TINYINT( 3 ) NOT NULL;