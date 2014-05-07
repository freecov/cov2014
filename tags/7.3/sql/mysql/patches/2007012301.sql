CREATE TABLE `cms_mailings` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
