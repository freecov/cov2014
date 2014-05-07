-- --------------------------------------------------------
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
  KEY `parentPage` (`parentPage`)
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
