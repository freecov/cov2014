CREATE TABLE `letterheads` (
  `id` int(11) NOT NULL auto_increment,
  `letterhead` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

INSERT INTO `letterheads` VALUES (0, '');
INSERT INTO `letterheads` VALUES (1, 'Beste');
INSERT INTO `letterheads` VALUES (2, 'Geachte');
INSERT INTO `letterheads` VALUES (3, 'Dear');

CREATE TABLE `titles` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

INSERT INTO `titles` (`id`, `title`) VALUES (1, 'Dr.'),
(2, 'Drs.'),
(0, ''),
(4, 'Ing.'),
(5, 'Mr.'),
(6, 'Prof.'),
(7, 'Prof. Dr.'),
(8, 'BSc.'),
(9, 'MSc.'),
(10, 'Drs. Ing.');

