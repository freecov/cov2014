--
-- Table structure for table `projects_declaration_options`
--

CREATE TABLE IF NOT EXISTS `projects_declaration_options` (
  `id` int(11) NOT NULL auto_increment,
  `group` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects_declaration_registration`
--

CREATE TABLE IF NOT EXISTS `projects_declaration_registration` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `hour_tarif` int(11) NOT NULL,
  `declaration_type` int(11) NOT NULL,
  `perc_btw` int(11) NOT NULL,
  `price` float(16,2) NOT NULL,
  `description` text NOT NULL,
  `is_invoice` tinyint(3) NOT NULL,
  `is_paid` tinyint(3) NOT NULL,
  `batch_nr` int(11) NOT NULL,
  `user_id_input` int(11) NOT NULL,
  `time_units` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `kilometers` int(11) NOT NULL,
  `perc_NCNP` float(16,2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
