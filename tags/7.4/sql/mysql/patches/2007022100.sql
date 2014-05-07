CREATE TABLE `funambol_file_sync` (
  `id` int(11) NOT NULL auto_increment,
  `guid` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1