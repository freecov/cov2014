CREATE TABLE `mail_messages_data_archive` (
  `mail_id` int(11) NOT NULL default '0',
  `body` longtext NOT NULL,
  `header` mediumtext NOT NULL,
  `mail_decoding` varchar(255) NOT NULL,
  PRIMARY KEY  (`mail_id`),
  KEY `cvd_mail_messages_archive_body` (`body`(255)),
  KEY `cvd_mail_messages_archive_header` (`header`(255))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
