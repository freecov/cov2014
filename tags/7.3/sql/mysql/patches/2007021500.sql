
--
-- Table structure for table `funambol_address_sync`
--

CREATE TABLE `funambol_address_sync` (
  `id` int(11) NOT NULL auto_increment,
  `guid` int(11) NOT NULL,
  `address_table` varchar(255) NOT NULL,
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `funambol_calendar_sync`
--

CREATE TABLE `funambol_calendar_sync` (
  `id` int(11) NOT NULL auto_increment,
  `guid` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `funambol_stats`
--

CREATE TABLE `funambol_stats` (
  `source` varchar(255) NOT NULL,
  `lasthash` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `funambol_todo_sync`
--

CREATE TABLE `funambol_todo_sync` (
  `id` int(11) NOT NULL auto_increment,
  `guid` int(11) NOT NULL,
  `todo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
