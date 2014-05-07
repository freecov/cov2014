--
-- Table structure for table `projects_ext_activities`
--

-- DROP TABLE IF EXISTS `projects_ext_activities`;
CREATE TABLE IF NOT EXISTS `projects_ext_activities` (
  `id` int(11) NOT NULL auto_increment,
  `department_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects_ext_departments`
--

-- DROP TABLE IF EXISTS `projects_ext_departments`;
CREATE TABLE IF NOT EXISTS `projects_ext_departments` (
  `id` int(11) NOT NULL auto_increment,
  `department` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `address_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects_ext_extrainfo`
--

-- DROP TABLE IF EXISTS `projects_ext_extrainfo`;
CREATE TABLE IF NOT EXISTS `projects_ext_extrainfo` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects_ext_metafields`
--

-- DROP TABLE IF EXISTS `projects_ext_metafields`;
CREATE TABLE IF NOT EXISTS `projects_ext_metafields` (
  `id` int(11) NOT NULL auto_increment,
  `field_name` varchar(255) NOT NULL,
  `field_type` smallint(3) NOT NULL,
  `field_order` int(11) NOT NULL,
  `activity` int(11) NOT NULL,
  `show_list` tinyint(3) NOT NULL,
  `default_value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects_ext_metavalues`
--

-- DROP TABLE IF EXISTS `projects_ext_metavalues`;
CREATE TABLE IF NOT EXISTS `projects_ext_metavalues` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `meta_value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
