CREATE TABLE `address_selections` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;
