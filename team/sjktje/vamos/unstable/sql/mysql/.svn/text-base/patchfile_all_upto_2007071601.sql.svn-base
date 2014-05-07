ALTER TABLE mail_signatures ADD realname varchar(255);
ALTER TABLE mail_signatures ADD companyname varchar(255);
ALTER TABLE mail_messages ADD `options` varchar(255);
ALTER TABLE mail_tracking ADD is_sent smallint;
ALTER TABLE mail_filters ADD `priority` INT(11);ALTER TABLE address_private ADD address2 varchar(255);
ALTER TABLE address_private ADD infix varchar(255);
ALTER TABLE address_other ADD address2 varchar(255);
ALTER TABLE address_other ADD infix varchar(255);

ALTER TABLE mail_messages MODIFY description mediumtext;
ALTER TABLE address_businesscards ADD businessunit varchar(255);
ALTER TABLE address_businesscards ADD department varchar(255);
ALTER TABLE address_businesscards ADD locationcode varchar(255);
UPDATE address_sync SET address_table = 'address_private' WHERE address_table = 'adres_personen';
UPDATE address_sync SET address_table = 'address' WHERE address_table = 'adres';
UPDATE address_sync SET address_table = 'address_businesscards' WHERE address_table = 'bcards';
UPDATE address_sync SET address_table = 'address_other' WHERE address_table = 'adres_overig';
UPDATE address_sync_records SET address_table = 'address_private' WHERE address_table = 'adres_personen';
UPDATE address_sync_records SET address_table = 'address' WHERE address_table = 'adres';
UPDATE address_sync_records SET address_table = 'address_businesscards' WHERE address_table = 'bcards';
UPDATE address_sync_records SET address_table = 'address_other' WHERE address_table = 'adres_overig';

ALTER TABLE `address` ADD INDEX address_classification ( `classification` ) ;
ALTER TABLE `address_businesscards` ADD INDEX addressbcards_classification ( `classification` ) ;
ALTER TABLE `address_businesscards` ADD INDEX addressbcards_addressid ( `address_id` ) ;
ALTER TABLE `address_info` ADD INDEX addressinfo_classification ( `classification` ) ;
ALTER TABLE `address_info` ADD INDEX addressinfo_addressid ( `address_id` ) ;CREATE TABLE bayham_settings (
	companyid varchar(255),
	userid varchar(255),
	password varchar(255),
	sender varchar(255)
);
CREATE TABLE `templates_files` (
	`id` int(11) NOT NULL auto_increment,
	`template_id` int(11) NOT NULL default '0',
	`name` varchar(255) default NULL,
	`temp_id` int(11) NOT NULL default '0',
	`type` varchar(255) default 'application/octet-stream',
	`size` varchar(255) default '0',
	PRIMARY KEY  (`id`),
	KEY `cvd_mail_templates_files_template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		
ALTER TABLE `templates_settings` ADD `footer_position` VARCHAR( 10 ) NOT NULL ,
ADD `footer_text` VARCHAR( 255 ) NOT NULL;
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
ALTER TABLE address_businesscards ADD COLUMN multirel varchar(255);
ALTER TABLE project ADD multirel varchar(255);
ALTER TABLE `projects_ext_metafields` ADD `large_data` MEDIUMTEXT;
ALTER TABLE `project` ADD `users` VARCHAR(255);
ALTER TABLE `projects_master` ADD `users` VARCHAR(255);
CREATE TABLE `mail_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `users` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `projects_ext_activities` ADD `users` VARCHAR(255);
ALTER TABLE `projects_ext_departments` ADD `users` VARCHAR(255);ALTER TABLE `license` ADD `has_cms` TINYINT(3);
ALTER TABLE `license` ADD `has_project_ext_samba` TINYINT(3);
ALTER TABLE `address` ADD `contact_birthday` INT(11);ALTER TABLE calendar ADD extra_users varchar(255);
ALTER TABLE `license` ADD `mail_force_server` VARCHAR(255) DEFAULT NULL;
ALTER TABLE mail_messages_data ADD mail_decoding varchar(255);
ALTER TABLE license ADD mail_lock_settings tinyint(3);
ALTER TABLE address ADD contact_birthday INT(11);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_abbreviations'
--

/* uncommented because superseded by patch 2006112000.sql */
/* it is not nessesary to apply this patch, it is only needed for new covide database */

/*
CREATE TABLE cms_abbreviations (
  id int(11) NOT NULL auto_increment,
  abbreviation varchar(255) NOT NULL,
  description varchar(255) NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_banners'
--

CREATE TABLE cms_banners (
  id int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  image text,
  rating int(11) default NULL,
  url text,
  internal_stat int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_banners_log'
--

CREATE TABLE cms_banners_log (
  id int(11) NOT NULL auto_increment,
  bannerid int(11) default NULL,
  `datetime` int(11) default NULL,
  visitor varchar(255) default NULL,
  clicked tinyint(3) default NULL,
  PRIMARY KEY  (id),
  KEY bannerid (bannerid),
  KEY datum (`datetime`),
  KEY bezoeker (visitor)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_banners_summary'
--

CREATE TABLE cms_banners_summary (
  id int(11) NOT NULL auto_increment,
  bannerid int(11) NOT NULL default '0',
  datum int(11) NOT NULL default '0',
  bezoekers int(11) NOT NULL default '0',
  uniek int(11) NOT NULL default '0',
  kliks int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY `unique` (id),
  KEY bannerid (bannerid),
  KEY datum (datum)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_counters'
--

CREATE TABLE cms_counters (
  id int(11) NOT NULL auto_increment,
  counter1 int(11) default NULL,
  PRIMARY KEY  (id),
  KEY `unique` (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_data'
--

CREATE TABLE cms_data (
  id int(11) NOT NULL auto_increment,
  parentPage int(11) NOT NULL default '0',
  pageTitle varchar(255) default NULL,
  pageLabel varchar(255) default NULL,
  datePublication int(11) default '0',
  pageData mediumtext,
  pageRedirect varchar(255) default NULL,
  isPublic tinyint(3) default '1',
  isActive tinyint(3) default '1',
  isMenuItem tinyint(3) default '1',
  keywords varchar(255) default NULL,
  apEnabled tinyint(3) default '0',
  isTemplate tinyint(3) default '0',
  isList tinyint(3) default '0',
  useMetaData tinyint(3) default '0',
  isSticky tinyint(3) default '0',
  search_fields varchar(255) default NULL,
  search_descr varchar(255) default NULL,
  isForm tinyint(3) default '0',
  date_start int(11) default '0',
  date_end int(11) default '0',
  date_changed int(11) default '0',
  notifyManager varchar(50) default '0',
  isGallery tinyint(3) default '0',
  pageRedirectPopup tinyint(3) default NULL,
  popup_data varchar(255) default NULL,
  new_code mediumtext,
  new_state char(2) default NULL,
  search_title varchar(255) default NULL,
  search_language varchar(255) default NULL,
  search_override tinyint(3) default NULL,
  pageAlias varchar(255) default NULL,
  isSpecial varchar(1) default NULL,
  date_last_action int(11) default NULL,
  google_changefreq varchar(255) default 'monthly',
  google_priority varchar(255) default '0.5',
  autosave_info varchar(255) NOT NULL,
  autosave_data mediumtext NOT NULL,
  PRIMARY KEY  (id),
  FULLTEXT KEY ftdata (pageData,pageTitle,pageLabel,pageAlias,search_title,search_fields,search_descr,keywords)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_date'
--

CREATE TABLE cms_date (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  date_begin int(11) NOT NULL default '0',
  description text NOT NULL,
  date_end int(11) default NULL,
  repeating varchar(255) default NULL,
  PRIMARY KEY  (id),
  KEY `unique` (pageid,date_begin)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_date_index'
--

CREATE TABLE cms_date_index (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  dateid int(11) NOT NULL default '0',
  `datetime` int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY `unique` (pageid,`datetime`)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_files'
--

CREATE TABLE cms_files (
  id int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `type` varchar(255) default 'application/octet-stream',
  size varchar(255) default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_formulieren'
--

CREATE TABLE cms_formulieren (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  field_name varchar(255) NOT NULL,
  field_type varchar(255) NOT NULL,
  field_value text NOT NULL,
  is_required tinyint(3) NOT NULL default '0',
  is_mailto tinyint(3) NOT NULL default '0',
  is_mailfrom tinyint(3) NOT NULL default '0',
  is_mailsubject tinyint(3) NOT NULL default '0',
  is_redirect tinyint(3) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_gallery'
--

CREATE TABLE cms_gallery (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  gallerytype smallint(3) NOT NULL default '0',
  cols int(11) NOT NULL default '0',
  `rows` int(11) NOT NULL default '0',
  thumbsize int(11) NOT NULL default '0',
  bigsize int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_gallery_photos'
--

CREATE TABLE cms_gallery_photos (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  `file` text NOT NULL,
  description text NOT NULL,
  `order` int(11) NOT NULL default '0',
  cachefile varchar(255) NOT NULL,
  count int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_images'
--

CREATE TABLE cms_images (
  id int(11) NOT NULL auto_increment,
  page_id int(11) NOT NULL default '0',
  path varchar(255) default NULL,
  description varchar(255) default NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_languages'
--

CREATE TABLE cms_languages (
  id int(11) NOT NULL auto_increment,
  filename varchar(255) default NULL,
  text_nl varchar(255) default NULL,
  text_uk varchar(255) default NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_license'
--

CREATE TABLE cms_license (
  cms_name varchar(255) NOT NULL,
  cms_meta tinyint(3) NOT NULL default '0',
  cms_license varchar(255) NOT NULL,
  cms_date int(11) NOT NULL default '0',
  cms_external text,
  search_fields text,
  search_descr varchar(255) default NULL,
  cms_forms tinyint(3) NOT NULL default '0',
  cms_list tinyint(3) NOT NULL default '0',
  cms_linkchecker_url varchar(255) default NULL,
  search_author varchar(255) default NULL,
  search_copyright varchar(255) default NULL,
  search_email varchar(255) default NULL,
  cms_changelist tinyint(3) NOT NULL default '0',
  cms_banners tinyint(3) NOT NULL default '0',
  cms_searchengine tinyint(3) NOT NULL default '0',
  db_version int(11) NOT NULL default '0',
  cms_gallery tinyint(3) NOT NULL default '0',
  website_url text,
  site_stylesheet text,
  cms_versioncontrol tinyint(3) default NULL,
  search_use_pagetitle tinyint(3) default NULL,
  search_language varchar(255) default NULL,
  cms_page_elements tinyint(3) NOT NULL default '1',
  cms_permissions tinyint(3) NOT NULL default '1',
  multiple_sitemaps tinyint(3) default NULL,
  google_verify varchar(255) default NULL,
  cms_linkchecker tinyint(3) NOT NULL,
  cms_hostnames text NOT NULL,
  cms_defaultpage int(11) NOT NULL,
  PRIMARY KEY  (cms_license)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_list'
--

CREATE TABLE cms_list (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  `query` text NOT NULL,
  `fields` text NOT NULL,
  `order` varchar(255) NOT NULL,
  count int(11) NOT NULL default '0',
  listposition varchar(50) NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_logins_log'
--

CREATE TABLE cms_logins_log (
  id int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL default '0',
  `datetime` int(11) NOT NULL default '0',
  user_agent varchar(255) default NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_metadata'
--

CREATE TABLE cms_metadata (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  fieldid int(11) NOT NULL default '0',
  `value` text NOT NULL,
  isDefault tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_metadef'
--

CREATE TABLE cms_metadef (
  id int(11) NOT NULL auto_increment,
  field_name varchar(255) NOT NULL,
  field_type varchar(20) NOT NULL,
  field_value text NOT NULL,
  `order` int(11) NOT NULL default '0',
  `group` varchar(255) default NULL,
  fphide tinyint(3) default NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_permissions'
--

CREATE TABLE cms_permissions (
  id int(11) NOT NULL auto_increment,
  pid int(11) NOT NULL default '0',
  uid varchar(50) NOT NULL default '0',
  editRight int(11) NOT NULL default '0',
  viewRight int(11) NOT NULL default '0',
  deleteRight int(11) NOT NULL default '0',
  manageRight int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY pid (pid),
  KEY uid (uid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_siteviews'
--

CREATE TABLE cms_siteviews (
  id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL,
  `view` mediumtext NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_temp'
--

CREATE TABLE cms_temp (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `fields` text NOT NULL,
  order1 varchar(255) NOT NULL,
  order2 varchar(255) NOT NULL,
  order3 varchar(255) NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_templates'
--

CREATE TABLE cms_templates (
  id int(11) NOT NULL auto_increment,
  title varchar(255) NOT NULL default '',
  `data` longtext,
  category varchar(10) NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'cms_users'
--

CREATE TABLE cms_users (
  id int(11) NOT NULL auto_increment,
  username varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  is_enabled tinyint(3) NOT NULL,
  PRIMARY KEY  (id)
);

ALTER TABLE license ADD has_cms INT(11);
ALTER TABLE users ADD xs_cms_level INT(11);
*/
ALTER TABLE license ADD has_project_ext TINYINT(3);
CREATE TABLE `address_selections` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;
ALTER TABLE `projects_ext_extrainfo` ADD COLUMN `project_year` INT(11);
ALTER TABLE `projects_master` ADD COLUMN `ext_department` INT(11);
ALTER TABLE `license` MODIFY `dayquote_nr` TEXT;alter table address alter contact_letterhead set default null;
alter table address alter contact_commencement set default null;
ALTER TABLE license ADD force_ssl smallint(3) default null;
ALTER TABLE license ADD default_lang char(3) default 'EN';
ALTER TABLE `calendar` ADD `is_event` TINYINT NULL DEFAULT '0';
ALTER TABLE `license` ADD `has_project_declaration` TINYINT(3);
CREATE TABLE `projects_declaration_extrainfo` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`project_id` INT( 11 ) NOT NULL ,
`task_date` INT( 11 ) NOT NULL ,
`damage_date` INT( 11 ) NOT NULL ,
`accident_type` INT( 11 ) NOT NULL ,
`perc_liabilities_wished` FLOAT( 16, 2 ) NOT NULL ,
`perc_liabilities_recognised` FLOAT( 16, 2 ) NOT NULL ,
`constituent` INT( 11 ) NOT NULL ,
`tarif` INT( 11 ) NOT NULL ,
`is_NCNP` INT( 11 ) NOT NULL ,
`perc_NCNP` FLOAT( 16, 2 ) NOT NULL ,
`client` INT( 11 ) NOT NULL ,
`adversary` VARCHAR( 255 ) NOT NULL ,
`expertise` VARCHAR( 255 ) NOT NULL ,
`lesion` INT( 11 ) NOT NULL ,
`lesion_description` TEXT NOT NULL ,
`hospitalisation` INT( 11 ) NOT NULL ,
`incapacity_for_work` INT NOT NULL ,
`profession` INT( 11 ) NOT NULL ,
`employment` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `mail_messages_data` DROP INDEX `mail_id`;
ALTER TABLE `mail_messages_data` ADD PRIMARY KEY ( `mail_id` );
ALTER TABLE `mail_messages_data` ADD INDEX ( `body` ( 255 ) );
ALTER TABLE `mail_messages_data` ADD INDEX ( `header` ( 255 ) );
 ALTER TABLE `users` ADD `xs_funambol` TINYINT( 3 ) NOT NULL ;
 ALTER TABLE `license` ADD `has_funambol` TINYINT( 3 ) NOT NULL ;ALTER TABLE `project` ADD `executor` INT( 11 ) NULL DEFAULT '0';-- --------------------------------------------------------
-- warning: drop statements below here
-- --------------------------------------------------------
--
-- Table structure for table `cms_abbreviations`
--

-- DROP TABLE IF EXISTS `cms_abbreviations`;
CREATE TABLE `cms_abbreviations` (
  `id` int(11) NOT NULL auto_increment,
  `abbreviation` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_banners`
--

-- DROP TABLE IF EXISTS `cms_banners`;
CREATE TABLE `cms_banners` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `image` text,
  `rating` int(11) default NULL,
  `url` text,
  `internal_stat` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_banners_log`
--

-- DROP TABLE IF EXISTS `cms_banners_log`;
CREATE TABLE `cms_banners_log` (
  `id` int(11) NOT NULL auto_increment,
  `bannerid` int(11) default NULL,
  `datetime` int(11) default NULL,
  `visitor` varchar(255) default NULL,
  `clicked` tinyint(3) default NULL,
  PRIMARY KEY  (`id`),
  KEY `bannerid` (`bannerid`),
  KEY `datum` (`datetime`),
  KEY `bezoeker` (`visitor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_banners_summary`
--

-- DROP TABLE IF EXISTS `cms_banners_summary`;
CREATE TABLE `cms_banners_summary` (
  `id` int(11) NOT NULL auto_increment,
  `bannerid` int(11) NOT NULL default '0',
  `datum` int(11) NOT NULL default '0',
  `bezoekers` int(11) NOT NULL default '0',
  `uniek` int(11) NOT NULL default '0',
  `kliks` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `unique` (`id`),
  KEY `bannerid` (`bannerid`),
  KEY `datum` (`datum`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_counters`
--

-- DROP TABLE IF EXISTS `cms_counters`;
CREATE TABLE `cms_counters` (
  `id` int(11) NOT NULL auto_increment,
  `counter1` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `unique` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_data`
--

-- DROP TABLE IF EXISTS `cms_data`;
CREATE TABLE `cms_data` (
  `id` int(11) NOT NULL auto_increment,
  `parentPage` int(11) NOT NULL default '0',
  `pageTitle` varchar(255) default NULL,
  `pageLabel` varchar(255) default NULL,
  `datePublication` int(11) default '0',
  `pageData` mediumtext,
  `pageRedirect` varchar(255) default NULL,
  `isPublic` tinyint(3) default '1',
  `isActive` tinyint(3) default '1',
  `isMenuItem` tinyint(3) default '1',
  `keywords` varchar(255) default NULL,
  `apEnabled` tinyint(3) default '0',
  `isTemplate` tinyint(3) default '0',
  `isList` tinyint(3) default '0',
  `useMetaData` tinyint(3) default '0',
  `isSticky` tinyint(3) default '0',
  `search_fields` varchar(255) default NULL,
  `search_descr` varchar(255) default NULL,
  `isForm` tinyint(3) default '0',
  `date_start` int(11) default '0',
  `date_end` int(11) default '0',
  `date_changed` int(11) default '0',
  `notifyManager` varchar(50) default '0',
  `isGallery` tinyint(3) default '0',
  `pageRedirectPopup` tinyint(3) default NULL,
  `popup_data` varchar(255) default NULL,
  `new_code` mediumtext,
  `new_state` char(2) default NULL,
  `search_title` varchar(255) default NULL,
  `search_language` varchar(255) default NULL,
  `search_override` tinyint(3) default NULL,
  `pageAlias` varchar(255) default NULL,
  `isSpecial` varchar(1) default NULL,
  `date_last_action` int(11) default NULL,
  `google_changefreq` varchar(255) default 'monthly',
  `google_priority` varchar(255) default '0.5',
  `autosave_info` varchar(255) NOT NULL,
  `autosave_data` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parentPage` (`parentPage`),
  FULLTEXT KEY `ftdata` (`pageData`,`pageTitle`,`pageLabel`,`pageAlias`,`search_title`,`search_fields`,`search_descr`,`keywords`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=691 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_date`
--

-- DROP TABLE IF EXISTS `cms_date`;
CREATE TABLE `cms_date` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `date_begin` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `date_end` int(11) default NULL,
  `repeating` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `unique` (`pageid`,`date_begin`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_date_index`
--

-- DROP TABLE IF EXISTS `cms_date_index`;
CREATE TABLE `cms_date_index` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `dateid` int(11) NOT NULL default '0',
  `datetime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `unique` (`pageid`,`datetime`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_files`
--

-- DROP TABLE IF EXISTS `cms_files`;
CREATE TABLE `cms_files` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `type` varchar(255) default 'application/octet-stream',
  `size` varchar(255) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_form_results`
--

-- DROP TABLE IF EXISTS `cms_form_results`;
CREATE TABLE `cms_form_results` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `user_value` varchar(255) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_form_results_visitors`
--

-- DROP TABLE IF EXISTS `cms_form_results_visitors`;
CREATE TABLE `cms_form_results_visitors` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `visitor_hash` varchar(255) NOT NULL,
  `datetime_start` int(11) NOT NULL,
  `datetime_end` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_form_settings`
--

-- DROP TABLE IF EXISTS `cms_form_settings`;
CREATE TABLE `cms_form_settings` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `mode` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_formulieren`
--

-- DROP TABLE IF EXISTS `cms_formulieren`;
CREATE TABLE `cms_formulieren` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(255) NOT NULL,
  `field_value` text NOT NULL,
  `is_required` tinyint(3) NOT NULL default '0',
  `is_mailto` tinyint(3) NOT NULL default '0',
  `is_mailfrom` tinyint(3) NOT NULL default '0',
  `is_mailsubject` tinyint(3) NOT NULL default '0',
  `is_redirect` tinyint(3) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_gallery`
--

-- DROP TABLE IF EXISTS `cms_gallery`;
CREATE TABLE `cms_gallery` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `gallerytype` smallint(3) NOT NULL default '0',
  `cols` int(11) NOT NULL default '0',
  `rows` int(11) NOT NULL default '0',
  `thumbsize` int(11) NOT NULL default '0',
  `bigsize` int(11) NOT NULL default '0',
  `fullsize` tinyint(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_gallery_photos`
--

-- DROP TABLE IF EXISTS `cms_gallery_photos`;
CREATE TABLE `cms_gallery_photos` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `file` text NOT NULL,
  `description` text NOT NULL,
  `order` int(11) NOT NULL default '0',
  `cachefile` varchar(255) NOT NULL,
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=152 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_images`
--

-- DROP TABLE IF EXISTS `cms_images`;
CREATE TABLE `cms_images` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL default '0',
  `path` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_languages`
--

-- DROP TABLE IF EXISTS `cms_languages`;
CREATE TABLE `cms_languages` (
  `id` int(11) NOT NULL auto_increment,
  `filename` varchar(255) default NULL,
  `text_nl` varchar(255) default NULL,
  `text_uk` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_license`
--

-- DROP TABLE IF EXISTS `cms_license`;
CREATE TABLE `cms_license` (
  `cms_name` varchar(255) NOT NULL,
  `cms_meta` tinyint(3) NOT NULL default '0',
  `cms_license` varchar(255) NOT NULL,
  `cms_date` int(11) NOT NULL default '0',
  `cms_external` text,
  `search_fields` text,
  `search_descr` varchar(255) default NULL,
  `cms_forms` tinyint(3) NOT NULL default '0',
  `cms_list` tinyint(3) NOT NULL default '0',
  `cms_linkchecker_url` varchar(255) default NULL,
  `search_author` varchar(255) default NULL,
  `search_copyright` varchar(255) default NULL,
  `search_email` varchar(255) default NULL,
  `cms_changelist` tinyint(3) NOT NULL default '0',
  `cms_banners` tinyint(3) NOT NULL default '0',
  `cms_searchengine` tinyint(3) NOT NULL default '0',
  `db_version` int(11) NOT NULL default '0',
  `cms_gallery` tinyint(3) NOT NULL default '0',
  `website_url` text,
  `site_stylesheet` text,
  `cms_versioncontrol` tinyint(3) default NULL,
  `search_use_pagetitle` tinyint(3) default NULL,
  `search_language` varchar(255) default NULL,
  `cms_page_elements` tinyint(3) NOT NULL default '1',
  `cms_permissions` tinyint(3) NOT NULL default '1',
  `multiple_sitemaps` tinyint(3) default NULL,
  `google_verify` varchar(255) default NULL,
  `cms_linkchecker` tinyint(3) NOT NULL,
  `cms_hostnames` text NOT NULL,
  `cms_defaultpage` int(11) NOT NULL,
  PRIMARY KEY  (`cms_license`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cms_list`
--

-- DROP TABLE IF EXISTS `cms_list`;
CREATE TABLE `cms_list` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `query` text NOT NULL,
  `fields` text NOT NULL,
  `order` varchar(255) NOT NULL,
  `count` int(11) NOT NULL default '0',
  `listposition` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `pageid` (`pageid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_logins_log`
--

-- DROP TABLE IF EXISTS `cms_logins_log`;
CREATE TABLE `cms_logins_log` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `datetime` int(11) NOT NULL default '0',
  `user_agent` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=248 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_metadata`
--

-- DROP TABLE IF EXISTS `cms_metadata`;
CREATE TABLE `cms_metadata` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `fieldid` int(11) NOT NULL default '0',
  `value` text NOT NULL,
  `isDefault` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pageid` (`pageid`),
  KEY `fieldid` (`fieldid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_metadef`
--

-- DROP TABLE IF EXISTS `cms_metadef`;
CREATE TABLE `cms_metadef` (
  `id` int(11) NOT NULL auto_increment,
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(20) NOT NULL,
  `field_value` text NOT NULL,
  `order` int(11) NOT NULL default '0',
  `group` varchar(255) default NULL,
  `fphide` tinyint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_permissions`
--

-- DROP TABLE IF EXISTS `cms_permissions`;
CREATE TABLE `cms_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `uid` varchar(50) NOT NULL default '0',
  `editRight` int(11) NOT NULL default '0',
  `viewRight` int(11) NOT NULL default '0',
  `deleteRight` int(11) NOT NULL default '0',
  `manageRight` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=330 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_siteviews`
--

-- DROP TABLE IF EXISTS `cms_siteviews`;
CREATE TABLE `cms_siteviews` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `view` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_temp`
--

-- DROP TABLE IF EXISTS `cms_temp`;
CREATE TABLE `cms_temp` (
  `id` int(11) NOT NULL auto_increment,
  `userkey` varchar(255) NOT NULL,
  `ids` text NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userkey` (`userkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=113 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_templates`
--

-- DROP TABLE IF EXISTS `cms_templates`;
CREATE TABLE `cms_templates` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `data` longtext,
  `category` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_users`
--

-- DROP TABLE IF EXISTS `cms_users`;
CREATE TABLE `cms_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_enabled` tinyint(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;


-- insert three basic site roots
INSERT INTO `cms_data` (`id`, `parentPage`, `pageTitle`, `pageLabel`, `datePublication`, `pageData`, `pageRedirect`, `isPublic`, `isActive`, `isMenuItem`, `keywords`, `apEnabled`, `isTemplate`, `isList`, `useMetaData`, `isSticky`, `search_fields`, `search_descr`, `isForm`, `date_start`, `date_end`, `date_changed`, `notifyManager`, `isGallery`, `pageRedirectPopup`, `popup_data`, `new_code`, `new_state`, `search_title`, `search_language`, `search_override`, `pageAlias`, `isSpecial`, `date_last_action`, `google_changefreq`, `google_priority`, `autosave_info`, `autosave_data`) VALUES
(1, 0, 'site root', '', 0, '', '', 0, 0, 0, '', 0, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'R', NULL, 'monthly', '0.5', '', ''),
(2, 0, 'deleted items', '', 0, NULL, '', 1, 0, 1, '', 0, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, '0', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'D', NULL, 'monthly', '0.5', '', ''),
(3, 0, 'protected items', '', 0, NULL, '', 1, 1, 1, '', 0, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, '0', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'X', NULL, 'monthly', '0.5', '', '');


-- add license information
INSERT INTO `cms_license` (`cms_name`, `cms_meta`, `cms_license`, `cms_date`, `cms_external`, `search_fields`, `search_descr`, `cms_forms`, `cms_list`, `cms_linkchecker_url`, `search_author`, `search_copyright`, `search_email`, `cms_changelist`, `cms_banners`, `cms_searchengine`, `db_version`, `cms_gallery`, `website_url`, `site_stylesheet`, `cms_versioncontrol`, `search_use_pagetitle`, `search_language`, `cms_page_elements`, `cms_permissions`, `multiple_sitemaps`, `google_verify`, `cms_linkchecker`, `cms_hostnames`, `cms_defaultpage`) VALUES
('Welcome to your new home in cyberspace', 0, 'covide', 0, NULL, 'Covide, CRM, CMS, Groupware, VoIP', 'Covide combines great Groupware (shared email, calendars, files) and CRM (sales and support) in CRM-groupware. The most efficient way to work together. Integrate it with VoIP PBX Asterisk and OpenOffice and you can create a complete Virtual Office.', 0, 0, '', 'Covide', 'Covide', '', 0, 0, 0, 0, 0, '', '', 0, 0, 'nl,en', 0, 0, 0, '', 0, 'localhost', 0);

ALTER TABLE `users` ADD `xs_cms_level` int(11);
ALTER TABLE `license` ADD `has_cms` tinyint(3);
ALTER TABLE cms_data MODIFY apEnabled INT(11) DEFAULT 0;
ALTER TABLE cms_data ADD address_ids varchar(255);
ALTER TABLE cms_data ADD address_level TINYINT(3);ALTER TABLE cms_license ADD cms_favicon varchar(255);
ALTER TABLE cms_license ADD cms_logo varchar(255);
ALTER TABLE cms_license_siteroots ADD cms_favicon varchar(255);
ALTER TABLE cms_license_siteroots ADD cms_logo varchar(255);
CREATE TABLE `cms_license_siteroots` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `search_fields` text,
  `search_descr` varchar(255) default NULL,
  `search_author` varchar(255) default NULL,
  `search_copyright` varchar(255) default NULL,
  `search_email` varchar(255) default NULL,
  `search_use_pagetitle` tinyint(3) default NULL,
  `search_language` varchar(255) default NULL,
  `google_verify` varchar(255) default NULL,
  `cms_hostnames` text NOT NULL,
  `cms_defaultpage` int(11) NOT NULL,
  `cms_name` varchar(255) NOT NULL,
  `cms_favicon` varchar(255) NOT NULL,
  `cms_logo` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAMALTER TABLE address_private add tav varchar(255);
ALTER TABLE address_private add contact_person varchar(255);
ALTER TABLE address_private add contact_letterhead smallint(3);
ALTER TABLE address_private add contact_commencement smallint(3);
ALTER TABLE address_private add contact_initials varchar(255);
ALTER TABLE address_private add title int(11);
ALTER TABLE license ADD address_strict_permissions TINYINT(3);
ALTER TABLE address_classifications ADD access VARCHAR( 255 ) NOT NULL ;ALTER TABLE cms_license ADD cms_mailings TINYINT(3);
ALTER TABLE cms_license ADD cms_protected TINYINT(3);
ALTER TABLE cms_license ADD cms_address TINYINT(3);
ALTER TABLE cms_abbreviations ADD lang varchar(255);
ALTER TABLE license ADD cms_lock_settings TINYINT(3);ALTER TABLE `address` MODIFY `debtor_nr` VARCHAR(50);CREATE TABLE `cms_image_cache` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`img_id` INT( 11 ) NOT NULL ,
`datetime` INT( 11 ) NOT NULL ,
`width` INT( 11 ) NOT NULL ,
`height` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;ALTER TABLE `cms_data` ADD `isProtected` INT( 11 ) NOT NULL ;ALTER TABLE `cms_gallery` ADD `font` VARCHAR(50);
ALTER TABLE `cms_gallery` ADD `font_size` INT(11);CREATE TABLE `cms_mailings` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
CREATE TABLE `cms_alias_history` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;ALTER TABLE `projects_master` ADD `executor` INT(11);ALTER TABLE `license` ADD `address_migrated` TINYINT( 3 ) NOT NULL;
CREATE TABLE `address_businesscards_info` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`address_id` INT( 11 ) NOT NULL ,
`bcard_id` INT( 11 ) NOT NULL
) ENGINE = MYISAM;
ALTER TABLE `address_businesscards_info` ADD INDEX ( `address_id` );
ALTER TABLE `address_businesscards_info` ADD INDEX ( `bcard_id` );ALTER TABLE `cms_data` ADD `useSSL` TINYINT( 3 ) NOT NULL ;
ALTER TABLE `cms_data` ADD `useInternal` TINYINT( 3 ) NOT NULL ;ALTER TABLE `users` ADD `mail_signature_html` TEXT NOT NULL;
ALTER TABLE `mail_signatures` ADD `signature_html` TEXT NOT NULL;
ALTER TABLE `mail_signatures` ADD `default` TINYINT( 3 ) NOT NULL;CREATE TABLE `address_birthdays` (
  `id` int(11) NOT NULL auto_increment,
  `bcard_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1
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
ALTER TABLE `license` ADD `project_ext_share` VARCHAR(255);ALTER TABLE `address_classifications` ADD `access_read` VARCHAR( 255 ) NOT NULL;ALTER TABLE license ADD filesystem_checked varchar(10);
ALTER TABLE `license` ADD `autopatcher_enable` TINYINT( 3 ) NOT NULL ,
ADD `autopatcher_lastpatch` VARCHAR( 255 ) NOT NULL ;ALTER TABLE `funambol_calendar_sync` ADD INDEX ( `user_id` );
ALTER TABLE `funambol_address_sync` ADD INDEX ( `user_id` );
ALTER TABLE `funambol_todo_sync` ADD INDEX ( `user_id` );

CREATE TABLE `funambol_file_sync` (
  `id` int(11) NOT NULL auto_increment,
  `guid` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1ALTER TABLE `users` ADD `xs_funambol_expunge` TINYINT( 3 ) NOT NULL ;ALTER TABLE `status_list` ADD `mark_expunge` TINYINT( 3 ) NOT NULL ;--
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
ALTER TABLE `address_private` ADD `sync_added` INT( 11 ) NOT NULL ;ALTER TABLE `users` ADD `addresssyncmanage` VARCHAR( 255 ) NOT NULL ;ALTER TABLE address ADD state varchar(255);
ALTER TABLE address_private ADD state varchar (255);
ALTER TABLE address_other ADD state varchar(255);
ALTER TABLE address_businesscards ADD business_state varchar(255);
ALTER TABLE address_businesscards ADD personal_state varchar(255);
ALTER TABLE `address_private` ADD `jobtitle` VARCHAR(255) NOT NULL ,
ADD `locationcode` VARCHAR(255) NOT NULL ,
ADD `businessunit` VARCHAR(255) NOT NULL ,
ADD `department` VARCHAR(255) NOT NULL ,
ADD `business_address` VARCHAR(255) NOT NULL ,
ADD `business_phone_nr` VARCHAR(255) NOT NULL ,
ADD `business_city` VARCHAR(255) NOT NULL ,
ADD `business_phone_nr_2` VARCHAR(255) NOT NULL ,
ADD `business_state` VARCHAR(255) NOT NULL ,
ADD `business_fax_nr` VARCHAR(255) NOT NULL ,
ADD `business_zipcode` VARCHAR(255) NOT NULL ,
ADD `business_mobile_nr` VARCHAR(255) NOT NULL ,
ADD `business_country` VARCHAR(255) NOT NULL ,
ADD `business_car_phone` VARCHAR(255) NOT NULL ,
ADD `business_email` VARCHAR(255) NOT NULL ,
ADD `phone_nr_2` VARCHAR(255) NOT NULL ,
ADD `other_address` VARCHAR(255) NOT NULL ,
ADD `other_phone_nr` VARCHAR(255) NOT NULL ,
ADD `other_city` VARCHAR(255) NOT NULL ,
ADD `other_phone_nr_2` VARCHAR(255) NOT NULL ,
ADD `other_state` VARCHAR(255) NOT NULL ,
ADD `other_fax_nr` VARCHAR(255) NOT NULL ,
ADD `other_zipcode` VARCHAR(255) NOT NULL ,
ADD `other_mobile_nr` VARCHAR(255) NOT NULL ,
ADD `other_country` VARCHAR(255) NOT NULL ,
ADD `alternative_name`  VARCHAR(255) NOT NULL ,
ADD `timestamp_birthday` VARCHAR(255) NOT NULL ,
ADD `suffix` VARCHAR(255) NOT NULL ,
ADD `pobox_state` VARCHAR(255) NOT NULL ,
ADD `pobox_country` VARCHAR(255) NOT NULL ,
ADD `other_email` VARCHAR(255) NOT NULL,
ADD `opt_assistant_name` VARCHAR(255) NOT NULL,
ADD `opt_assistant_phone_nr` VARCHAR(255) NOT NULL;

ALTER TABLE `address_businesscards` ADD `suffix` INT( 11 ) NOT NULL ,
ADD `jobtitle` VARCHAR( 255 ) NOT NULL ,
ADD `website` VARCHAR( 255 ) NOT NULL ,
ADD `business_phone_nr_2` VARCHAR( 255 ) NOT NULL ,
ADD `business_country` VARCHAR( 255 ) NOT NULL ,
ADD `business_car_phone` VARCHAR( 255 ) NOT NULL ,
ADD `personal_phone_nr_2` VARCHAR( 255 ) NOT NULL ,
ADD `personal_country` VARCHAR( 255 ) NOT NULL ,
ADD `other_address` VARCHAR( 255 ) NOT NULL ,
ADD `other_zipcode` VARCHAR( 255 ) NOT NULL ,
ADD `other_city` VARCHAR( 255 ) NOT NULL ,
ADD `other_state` VARCHAR( 255 ) NOT NULL ,
ADD `other_phone_nr` VARCHAR( 255 ) NOT NULL ,
ADD `other_phone_nr_2` VARCHAR( 255 ) NOT NULL ,
ADD `other_fax_nr` VARCHAR( 255 ) NOT NULL ,
ADD `other_mobile_nr` VARCHAR( 255 ) NOT NULL ,
ADD `other_email` VARCHAR( 255 ) NOT NULL ,
ADD `pobox` VARCHAR(255) NOT NULL ,
ADD `pobox_country` VARCHAR(255) NOT NULL ,
ADD `pobox_state` VARCHAR(255) NOT NULL ,
ADD `pobox_zipcode` VARCHAR(255) NOT NULL ,
ADD `pobox_city` VARCHAR(255) NOT NULL ,
ADD `other_country` VARCHAR( 255 ) NOT NULL,
ADD `opt_assistant_name` VARCHAR(255) NOT NULL,
ADD `opt_assistant_phone_nr` VARCHAR(255) NOT NULL;

ALTER TABLE `address_private` ADD `opt_callback_phone_nr` VARCHAR( 255 ) NOT NULL ,
ADD `opt_company_phone_nr` VARCHAR( 255 ) NOT NULL ,
ADD `opt_company_name` VARCHAR( 255 ) NOT NULL ,
ADD `opt_manager_name` VARCHAR( 255 ) NOT NULL ,
ADD `opt_pager_number` VARCHAR( 255 ) NOT NULL ,
ADD `opt_profession` VARCHAR( 255 ) NOT NULL ,
ADD `opt_radio_phone_nr` VARCHAR( 255 ) NOT NULL ,
ADD `opt_telex_number` VARCHAR( 255 ) NOT NULL ;

ALTER TABLE `address_businesscards` ADD `opt_callback_phone_nr` VARCHAR( 255 ) NOT NULL ,
ADD `opt_company_phone_nr` VARCHAR( 255 ) NOT NULL ,
ADD `opt_company_name` VARCHAR( 255 ) NOT NULL ,
ADD `opt_manager_name` VARCHAR( 255 ) NOT NULL ,
ADD `opt_pager_number` VARCHAR( 255 ) NOT NULL ,
ADD `opt_profession` VARCHAR( 255 ) NOT NULL ,
ADD `opt_radio_phone_nr` VARCHAR( 255 ) NOT NULL ,
ADD `opt_telex_number` VARCHAR( 255 ) NOT NULL ;

ALTER TABLE `address` ADD `suffix` INT( 11 ) NOT NULL ,
ADD `pobox_state` VARCHAR( 255 ) NOT NULL ,
ADD `pobox_country` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `projects_declaration_extrainfo` ADD `identifier_adversary` VARCHAR( 255 ) NOT NULL ,
ADD `identifier_expertise` VARCHAR( 255 ) NOT NULL ,
ADD `agreements` TEXT NOT NULL ,
ADD `bcard_constituent` INT( 11 ) NOT NULL ,
ADD `bcard_client` INT( 11 ) NOT NULL ,
ADD `bcard_adversary` INT( 11 ) NOT NULL ,
ADD `bcard_expertise` INT( 11 ) NOT NULL ;
ALTER TABLE `users` ADD `mail_shortview` TINYINT( 3 ) NOT NULL ;ALTER TABLE `license` ADD `disable_local_gzip` TINYINT( 3 ) NOT NULL ;ALTER TABLE projects_declaration_extrainfo ADD default_tarif FLOAT(16,2);
ALTER TABLE projects_declaration_extrainfo ADD `identifier` varchar(255);
ALTER TABLE `mail_templates` ADD `use_complex_mode` TINYINT( 3 ) NOT NULL ,
ADD `html_data` MEDIUMTEXT NOT NULL ;
ALTER TABLE `users` ADD `mail_default_private` TINYINT( 3 ) NOT NULL ;ALTER TABLE `user_groups` CHANGE `members` `members` TEXT;CREATE TABLE `cms_cache` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) NOT NULL,
  `ident` varchar(255) NOT NULL,
  `data` mediumblob NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `ident` (`ident`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;alter table license add has_radius tinyint(2) default 0;
CREATE TABLE radius_settings (
	radius_server varchar(255),
	radius_port int(11),
	shared_secret varchar(255),
	nas_ip varchar(255),
	auth_type varchar(255)
);
alter table users add authmethod varchar(255);
ALTER TABLE `license` ADD `funambol_server_version` INT( 11 ) NOT NULL DEFAULT 300;ALTER TABLE `funambol_stats` ADD `synchash` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `funambol_address_sync` CHANGE `guid` `guid` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `funambol_calendar_sync` CHANGE `guid` `guid` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `funambol_file_sync` CHANGE `guid` `guid` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `funambol_todo_sync` CHANGE `guid` `guid` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `users` ADD `xs_funambol_version` TINYINT( 3 ) NOT NULL ;alter table address add is_person tinyint(3);
ALTER TABLE `cms_data` ADD `isFeedback` TINYINT(3);
ALTER TABLE `cms_license` ADD `cms_feedback` TINYINT(3);
ALTER TABLE `cms_license` ADD `cms_user_register` TINYINT(3);
ALTER TABLE `cms_users` ADD `email` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `cms_users` ADD `registration_date` INT( 11 ) NOT NULL ;
ALTER TABLE `cms_users` ADD `is_active` TINYINT( 3 ) NOT NULL ;

CREATE TABLE `cms_feedback` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`page_id` INT( 11 ) NOT NULL ,
`datetime` INT( 11 ) NOT NULL ,
`user_id` INT( 11 ) NOT NULL ,
`subject` VARCHAR( 255 ) NOT NULL ,
`body` TEXT NOT NULL
) ENGINE = MYISAM ;
ALTER TABLE cms_counters ADD name VARCHAR(255);ALTER TABLE cms_feedback ADD is_visitor TINYINT(3);
ALTER TABLE cms_users ADD confirm_hash VARCHAR(255);
ALTER TABLE `license` ADD `enable_filestore_gzip` TINYINT( 3 ) NOT NULL ;
 ALTER TABLE `cms_users` ADD `confirm_hash` VARCHAR( 255 ) NOT NULL ;ALTER TABLE `cms_feedback` ADD `is_visitor` TINYINT( 3 ) NOT NULL ;ALTER TABLE cms_license ADD cms_manage_hostname VARCHAR(255);CREATE TABLE `cms_keys` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`crypt_key` VARCHAR( 255 ) NOT NULL ,
`datetime` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;
ALTER TABLE `cms_keys` ADD `user_id` INT( 11 ) NOT NULL ;ALTER TABLE cms_license ADD cms_manage_hostname VARCHAR(255);ALTER TABLE `license` ADD `use_project_global_reghour` TINYINT( 3 ) NOT NULL ;ALTER TABLE address_classifications ADD description_full VARCHAR(255);
CREATE TABLE `meta_global` (
	`id` int(11) NOT NULL auto_increment,
	`meta_id` int(11) NOT NULL,
	`relation_id` int(11) NOT NULL,
	`value` mediumtext NOT NULL,
	PRIMARY KEY  (`id`)
);
alter table todo add column status int(11) default 0;
alter table todo add column priority int(11);
ALTER TABLE users ADD addressmode INT NULL DEFAULT 0;
ALTER TABLE sales ADD project_id INT NULL DEFAULT 0;
CREATE TABLE twinfield_settings (
	id int(11) NOT NULL auto_increment,
	username varchar(255),
	password varchar(255),
	offices varchar(255),
	company varchar(255),
	PRIMARY KEY (id)
);
CREATE TABLE `address_titles` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 255 ) NULL
) ENGINE = MYISAM ;ALTER TABLE twinfield_settings ADD COLUMN default_office int(11) not null;
ALTER TABLE twinfield_settings DROP COLUMN offices;
alter table cms_gallery_photos add column rating int(11) default 0;
alter table cms_gallery_photos add column url text;
alter table cms_gallery_photos add column internal_stat int(11);
alter table cms_gallery_photos add index ( internal_stat );
alter table cms_gallery_photos add index ( rating );
alter table cms_gallery_photos add index ( pageid );
