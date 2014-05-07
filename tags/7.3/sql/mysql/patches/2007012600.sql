CREATE TABLE `cms_alias_history` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;