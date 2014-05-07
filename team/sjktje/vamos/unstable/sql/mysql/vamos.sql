
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE TABLE `active_calls` (
  `name` varchar(255) default NULL,
  `address_id` int(11) default NULL,
  `timestamp` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `address` (
  `id` int(11) NOT NULL auto_increment,
  `surname` varchar(255) default NULL,
  `givenname` varchar(255) default NULL,
  `companyname` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `zipcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `phone_nr` varchar(255) default NULL,
  `fax_nr` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `is_company` smallint(3) default NULL,
  `link` int(11) default NULL,
  `is_public` smallint(3) default NULL,
  `mobile_nr` varchar(255) default NULL,
  `debtor_nr` int(11) default NULL,
  `country` varchar(255) default NULL,
  `company_type` smallint(3) default NULL,
  `comment` mediumtext,
  `website` varchar(255) default NULL,
  `relation_type` smallint(3) default NULL,
  `tav` varchar(255) default NULL,
  `contact_person` varchar(255) default NULL,
  `is_customer` smallint(3) default NULL,
  `is_supplier` smallint(3) default NULL,
  `is_partner` smallint(3) default NULL,
  `is_prospect` smallint(3) default NULL,
  `is_other` smallint(3) default NULL,
  `warning` varchar(255) default NULL,
  `pobox` varchar(255) default NULL,
  `pobox_zipcode` varchar(255) default NULL,
  `pobox_city` varchar(255) default NULL,
  `classification` varchar(255) default NULL,
  `account_manager` int(11) default NULL,
  `is_active` smallint(3) default '1',
  `contact_letterhead` smallint(3) default NULL,
  `contact_commencement` smallint(3) default NULL,
  `contact_initials` varchar(255) default NULL,
  `contact_givenname` varchar(255) default NULL,
  `contact_infix` varchar(255) default NULL,
  `contact_surname` varchar(255) default NULL,
  `e4lid` varchar(255) default NULL,
  `title` int(11) default NULL,
  `relname` varchar(255) default NULL,
  `relpass` varchar(255) default NULL,
  `modified` int(11) default NULL,
  `sync_modified` int(11) default NULL,
  `address2` varchar(255) default NULL,
  `contact_birthday` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `address_classification` (`classification`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
CREATE TABLE `address_businesscards` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) NOT NULL default '0',
  `givenname` varchar(255) default NULL,
  `initials` varchar(255) default NULL,
  `infix` varchar(255) default NULL,
  `surname` varchar(255) default NULL,
  `timestamp_birthday` int(11) default NULL,
  `mobile_nr` varchar(255) default NULL,
  `phone_nr` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `memo` mediumtext,
  `commencement` smallint(3) default '3',
  `classification` varchar(255) default NULL,
  `letterhead` smallint(3) default '2',
  `business_address` varchar(255) default NULL,
  `business_zipcode` varchar(255) default NULL,
  `business_city` varchar(255) default NULL,
  `business_mobile_nr` varchar(255) default NULL,
  `business_phone_nr` varchar(255) default NULL,
  `business_email` varchar(255) default NULL,
  `business_fax_nr` varchar(255) default NULL,
  `personal_address` varchar(255) default NULL,
  `personal_zipcode` varchar(255) default NULL,
  `personal_city` varchar(255) default NULL,
  `personal_mobile_nr` varchar(255) default NULL,
  `personal_phone_nr` varchar(255) default NULL,
  `personal_email` varchar(255) default NULL,
  `personal_fax_nr` varchar(255) default NULL,
  `e4lid` varchar(255) default NULL,
  `title` int(11) default NULL,
  `alternative_name` varchar(255) default NULL,
  `modified` int(11) default NULL,
  `photo` varchar(255) default NULL,
  `sync_modified` int(11) default NULL,
  `businessunit` varchar(255) default NULL,
  `department` varchar(255) default NULL,
  `locationcode` varchar(255) default NULL,
  `multirel` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_address_businesscards_address_id` (`address_id`),
  KEY `addressbcards_classification` (`classification`),
  KEY `addressbcards_addressid` (`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `address_classifications` (
  `id` int(11) NOT NULL auto_increment,
  `description` varchar(255) default NULL,
  `is_active` smallint(3) default '0',
  `is_locked` smallint(3) default NULL,
  `subtype` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `address_info` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) NOT NULL default '0',
  `comment` mediumtext,
  `classification` varchar(255) default NULL,
  `warning` varchar(255) default NULL,
  `photo` varchar(255) default NULL,
  `provision_perc` decimal(8,2) default NULL,
  `md5` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `addressinfo_classification` (`classification`),
  KEY `addressinfo_addressid` (`address_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
CREATE TABLE `address_multivers` (
  `id` int(11) NOT NULL auto_increment,
  `surname` varchar(255) default NULL,
  `givenname` varchar(255) default NULL,
  `companyname` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `zipcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `phone_nr` varchar(255) default NULL,
  `fax_nr` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `is_company` smallint(3) default NULL,
  `link` int(11) default NULL,
  `is_public` smallint(3) default NULL,
  `mobile_nr` varchar(255) default NULL,
  `debtor_nr` int(11) default NULL,
  `country` varchar(255) default NULL,
  `company_type` smallint(3) default NULL,
  `comment` mediumtext,
  `website` varchar(255) default NULL,
  `relation_type` smallint(3) default NULL,
  `tav` varchar(255) default NULL,
  `contact_person` varchar(255) default NULL,
  `is_customer` smallint(3) default NULL,
  `is_supplier` smallint(3) default NULL,
  `is_partner` smallint(3) default NULL,
  `is_prospect` smallint(3) default NULL,
  `is_other` smallint(3) default NULL,
  `warning` varchar(255) default NULL,
  `pobox` varchar(255) default NULL,
  `pobox_zipcode` varchar(255) default NULL,
  `pobox_city` varchar(255) default NULL,
  `classification` varchar(255) default NULL,
  `account_manager` int(11) default NULL,
  `is_active` smallint(3) default '1',
  `contact_letterhead` smallint(3) default '2',
  `contact_commencement` smallint(3) default '2',
  `contact_initials` varchar(255) default NULL,
  `contact_givenname` varchar(255) default NULL,
  `contact_infix` varchar(255) default NULL,
  `contact_surname` varchar(255) default NULL,
  `e4lid` varchar(255) default NULL,
  `title` int(11) default NULL,
  `relname` varchar(255) default NULL,
  `relpass` varchar(255) default NULL,
  `modified` int(11) default NULL,
  `sync_modified` int(11) default NULL,
  `address2` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `address_other` (
  `id` int(11) NOT NULL auto_increment,
  `companyname` varchar(255) default NULL,
  `surname` varchar(255) default NULL,
  `givenname` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `zipcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `phone_nr` varchar(255) default NULL,
  `fax_nr` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `is_company` smallint(3) default NULL,
  `is_public` smallint(3) default NULL,
  `mobile_nr` varchar(255) default NULL,
  `comment` mediumtext,
  `website` varchar(255) default NULL,
  `pobox` varchar(255) default NULL,
  `pobox_zipcode` varchar(255) default NULL,
  `pobox_city` varchar(255) default NULL,
  `is_active` smallint(3) default '1',
  `is_companylocation` smallint(3) default '0',
  `arbo_kantoor` smallint(3) default '0',
  `arbo_bedrijf` int(11) default NULL,
  `arbo_code` varchar(255) default NULL,
  `arbo_team` varchar(255) default NULL,
  `sync_modified` int(11) default NULL,
  `address2` varchar(255) default NULL,
  `infix` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `address_private` (
  `id` int(11) NOT NULL auto_increment,
  `surname` varchar(255) default NULL,
  `givenname` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `zipcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `phone_nr` varchar(255) default NULL,
  `fax_nr` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `is_company` smallint(3) default NULL,
  `is_public` smallint(3) default NULL,
  `mobile_nr` varchar(255) default NULL,
  `comment` mediumtext,
  `website` varchar(255) default NULL,
  `pobox` varchar(255) default NULL,
  `pobox_zipcode` varchar(255) default NULL,
  `pobox_city` varchar(255) default NULL,
  `is_active` smallint(3) default '1',
  `country` varchar(255) default NULL,
  `e4lid` varchar(255) default NULL,
  `modified` int(11) default NULL,
  `sync_modified` int(11) default NULL,
  `address2` varchar(255) default NULL,
  `infix` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
CREATE TABLE `address_selections` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
CREATE TABLE `address_sync` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) default NULL,
  `address_table` varchar(255) default NULL,
  `is_private` int(11) default NULL,
  `account_manager` int(11) default NULL,
  `sync_modified` int(11) default NULL,
  `sync_hash` varchar(255) default NULL,
  `parent_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `address_sync_guid` (
  `id` int(11) NOT NULL auto_increment,
  `sync_id` int(11) default NULL,
  `sync_guid` int(11) default NULL,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `address_sync_records` (
  `id` int(11) NOT NULL auto_increment,
  `address_table` varchar(255) default NULL,
  `address_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `agenda_sync` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `sync_guid` int(11) default NULL,
  `action` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `amb_news` (
  `username` varchar(20) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `header` text NOT NULL,
  `content` text NOT NULL,
  `id` int(5) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
CREATE TABLE `amb_servers` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL default '',
  `region` varchar(5) NOT NULL default '0',
  `location` varchar(50) NOT NULL default '',
  `hidden_hub` tinyint(1) NOT NULL default '0',
  `service` tinyint(1) NOT NULL default '0',
  `permlink` int(1) default '0',
  `linked` int(1) default '0',
  `ip` varchar(16) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(255) default NULL,
  `body` mediumtext,
  `is_popup` smallint(3) default NULL,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `arbo_arbo` (
  `id` int(11) NOT NULL auto_increment,
  `regiokantoor` varchar(255) default NULL,
  `regiofax` varchar(255) default NULL,
  `regiotel` varchar(255) default NULL,
  `regioteam` varchar(255) default NULL,
  `werkgever` varchar(255) default NULL,
  `adres` varchar(255) default NULL,
  `postcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `aansluitcode` varchar(255) default NULL,
  `contactpers` varchar(255) default NULL,
  `tel` varchar(255) default NULL,
  `fax` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `arbo_verslag` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `manager` int(11) default NULL,
  `soort` int(11) default NULL,
  `datum` int(11) default NULL,
  `omschrijving` mediumtext,
  `acties` mediumtext,
  `betrokkenen` mediumtext,
  `datum_invoer` int(11) default NULL,
  `ziekmelding` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `arbo_ziek` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `werkgever_id` int(11) default NULL,
  `arbo_id` int(11) default NULL,
  `datum` int(11) default NULL,
  `datum_ziek` int(11) default NULL,
  `datum_melding` int(11) default NULL,
  `datum_herstel` int(11) default NULL,
  `herstel` int(11) default NULL,
  `herstel_loon` int(11) default NULL,
  `zwanger` smallint(3) default '0',
  `zwanger_ziek` smallint(3) default '0',
  `orgaandonatie` smallint(3) default '0',
  `ongeval` smallint(3) default '0',
  `ontvangt_wao` smallint(3) default '0',
  `wao_perc` int(11) default NULL,
  `herintr_wao` smallint(3) default '0',
  `herintr_perc` int(11) default NULL,
  `bijzonderheden` mediumtext,
  `ziekmelding` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `bayham_settings` (
  `companyid` varchar(255) default NULL,
  `userid` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `sender` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `bugs` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `module` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `description` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `calendar` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp_start` int(11) default NULL,
  `timestamp_end` int(11) default NULL,
  `description` mediumtext,
  `user_id` int(11) default NULL,
  `address_id` int(11) default NULL,
  `project_id` int(11) default NULL,
  `is_private` smallint(3) default NULL,
  `note_id` int(11) NOT NULL default '0',
  `is_important` smallint(3) default '0',
  `is_registered` smallint(3) default '0',
  `notifytime` int(11) default NULL,
  `notified` smallint(3) default '0',
  `location` varchar(255) default NULL,
  `human_start` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `human_end` timestamp NOT NULL default '0000-00-00 00:00:00',
  `is_group` int(11) default NULL,
  `group_id` int(11) default NULL,
  `kilometers` int(11) default NULL,
  `is_repeat` int(11) default NULL,
  `multirel` varchar(255) default NULL,
  `repeat_type` char(1) default NULL,
  `is_alert` smallint(3) default '0',
  `is_holiday` smallint(3) default '0',
  `is_specialleave` smallint(3) default '0',
  `is_ill` smallint(3) default '0',
  `e4l_id` varchar(255) default NULL,
  `is_dnd` smallint(3) default '0',
  `deckm` smallint(3) default '0',
  `modified` int(11) default NULL,
  `modified_by` int(11) default NULL,
  `sync_guid` int(11) default NULL,
  `sync_hash` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `extra_users` varchar(255) default NULL,
  `is_event` tinyint(4) default '0',
  PRIMARY KEY  (`id`),
  KEY `cvd_calendar_user_id` (`user_id`),
  KEY `cvd_calendar_user_timestamp_start` (`timestamp_start`),
  KEY `cvd_calendar_user_timestamp_end` (`timestamp_end`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
CREATE TABLE `calendar_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `user_id_visitor` int(11) default NULL,
  `permissions` varchar(255) default 'RO',
  PRIMARY KEY  (`id`),
  KEY `cvd_calendar_permissions_user_id` (`user_id`),
  KEY `cvd_calendar_permissions_user_id_visitor` (`user_id_visitor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `cdr` (
  `calldate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `clid` varchar(255) default NULL,
  `src` varchar(255) default NULL,
  `dst` varchar(255) default NULL,
  `dconmediumtext` varchar(255) default NULL,
  `channel` varchar(255) default NULL,
  `dstchannel` varchar(255) default NULL,
  `lastapp` varchar(255) default NULL,
  `lastdata` varchar(255) default NULL,
  `duration` bigint(20) default NULL,
  `billsec` bigint(20) default NULL,
  `disposition` varchar(255) default NULL,
  `amaflags` bigint(20) default NULL,
  `accountcode` varchar(255) default NULL,
  `uniqueid` varchar(255) default NULL,
  `userfield` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `chat_rooms` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `users` mediumtext,
  `topic` varchar(255) default NULL,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `chat_text` (
  `id` int(11) NOT NULL auto_increment,
  `room` int(11) default NULL,
  `user` int(11) default NULL,
  `text` varchar(255) default NULL,
  `timestamp` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `cms_abbreviations` (
  `id` int(11) NOT NULL auto_increment,
  `abbreviation` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `cms_banners` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `image` text,
  `rating` int(11) default NULL,
  `url` text,
  `internal_stat` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `cms_counters` (
  `id` int(11) NOT NULL auto_increment,
  `counter1` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `unique` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `cms_data` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `page_parent` int(11) NOT NULL default '0',
  `page_type` int(11) NOT NULL default '0',
  `page_titel` varchar(255) default NULL,
  `publicationdate` int(11) NOT NULL default '0',
  `page_data` mediumtext,
  `page_redirect` varchar(255) default '0',
  `is_public` smallint(3) NOT NULL default '0',
  `is_active` smallint(3) NOT NULL default '0',
  `is_menuitem` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `cms_files` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `type` varchar(255) default 'application/octet-stream',
  `size` varchar(255) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `cms_images` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL default '0',
  `path` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `employees_info` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `social_security_nr` varchar(255) default NULL,
  `timestamp_started` int(11) default NULL,
  `timestamp_birthday` int(11) default NULL,
  `gender` smallint(3) NOT NULL default '0',
  `contract_type` mediumtext,
  `function` varchar(255) default NULL,
  `functionlevel` varchar(255) default NULL,
  `contract_hours` int(11) default NULL,
  `contract_holidayhours` int(11) default NULL,
  `timestamp_stop` int(11) default NULL,
  `evaluation` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL auto_increment,
  `category_id` int(11) default NULL,
  `question` mediumtext,
  `answer` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
CREATE TABLE `faq_cat` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `faxes` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default NULL,
  `sender` varchar(255) default NULL,
  `receiver` varchar(255) default NULL,
  `relation_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_faxes_relation_id` (`relation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `filesys_files` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `folder_id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` varchar(255) default NULL,
  `size` varchar(255) default NULL,
  `description` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `cvd_filesys_files_folder_id` (`folder_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
CREATE TABLE `filesys_folders` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `is_public` smallint(3) NOT NULL default '0',
  `is_relation` smallint(3) NOT NULL default '0',
  `address_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `parent_id` int(11) default NULL,
  `is_shared` varchar(255) default NULL,
  `description` mediumtext,
  `hrm_id` int(11) default NULL,
  `sticky` smallint(3) default '0',
  `project_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_filesys_folders_parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=162 DEFAULT CHARSET=latin1;
CREATE TABLE `filesys_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `folder_id` int(11) default NULL,
  `user_id` varchar(255) default NULL,
  `permissions` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_filesys_permissions_user_id` (`user_id`),
  KEY `cvd_filesys_permissions_folder_id` (`folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_akties` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) default NULL,
  `omschrijving` mediumtext,
  `datum` int(11) default NULL,
  `rekeningflow` decimal(16,2) default NULL,
  `factuur_nr` int(11) default NULL,
  `rekeningflow_btw` decimal(16,2) default NULL,
  `grootboeknummer_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_begin_standen_finance` (
  `id` int(11) NOT NULL auto_increment,
  `grootboek_id` int(11) NOT NULL default '0',
  `stand` decimal(16,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
CREATE TABLE `finance_boekingen` (
  `id` int(11) NOT NULL auto_increment,
  `credit` smallint(3) NOT NULL default '0',
  `factuur` int(11) NOT NULL default '0',
  `grootboek_id` int(11) default NULL,
  `status` smallint(3) default NULL,
  `datum` int(11) default NULL,
  `koppel_id` int(11) default NULL,
  `bedrag` decimal(16,2) default NULL,
  `product` int(11) default NULL,
  `inkoop` smallint(3) NOT NULL default '0',
  `deb_nr` int(11) NOT NULL default '0',
  `betaald` smallint(3) NOT NULL default '0',
  `locked` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_boekingen_20012003` (
  `id` int(11) NOT NULL auto_increment,
  `credit` smallint(3) NOT NULL default '0',
  `factuur` int(11) NOT NULL default '0',
  `grootboek_id` int(11) default NULL,
  `status` smallint(3) default NULL,
  `datum` int(11) default NULL,
  `koppel_id` int(11) default NULL,
  `bedrag` decimal(16,2) default NULL,
  `product` int(11) default NULL,
  `inkoop` smallint(3) NOT NULL default '0',
  `deb_nr` int(11) NOT NULL default '0',
  `betaald` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_grootboeknummers` (
  `id` int(11) NOT NULL auto_increment,
  `nr` int(11) default NULL,
  `titel` varchar(255) default NULL,
  `debiteur` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_inkopen` (
  `id` int(11) NOT NULL auto_increment,
  `datum` int(11) NOT NULL default '0',
  `balanspost` int(11) NOT NULL default '0',
  `boekstuknr` int(11) NOT NULL default '1',
  `descr` varchar(255) default NULL,
  `leverancier_nr` int(11) NOT NULL default '0',
  `bedrag_ex` decimal(16,2) default NULL,
  `bedrag_inc` decimal(16,2) default NULL,
  `bedrag_btw` decimal(16,2) default NULL,
  `betaald` decimal(16,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_jaar_afsluitingen` (
  `jaar` int(11) NOT NULL default '0',
  `datum_afgesloten` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_klanten` (
  `id` int(11) NOT NULL auto_increment,
  `naam` varchar(255) default NULL,
  `adres` varchar(255) default NULL,
  `postcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `land` varchar(255) default NULL,
  `telefoonnummer` varchar(255) default NULL,
  `faxnummer` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `soortbedrijf_id` int(11) default NULL,
  `aantalwerknemers` int(11) default NULL,
  `address_id` int(11) default NULL,
  `contactpersoon` varchar(255) default NULL,
  `contactpersoon_voorletters` varchar(255) default NULL,
  `totaal_flow` int(11) default NULL,
  `totaal_flow_12` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_offertes` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) default NULL,
  `titel` varchar(255) default NULL,
  `status` int(11) default NULL,
  `uitvoerder` varchar(255) default NULL,
  `producten_id_0` varchar(255) default NULL,
  `producten_id_1` varchar(255) default NULL,
  `producten_id_2` varchar(255) default NULL,
  `producten_id_3` varchar(255) default NULL,
  `html_0` mediumtext,
  `html_1` mediumtext,
  `html_2` mediumtext,
  `html_3` mediumtext,
  `datum_0` varchar(255) default NULL,
  `datum_1` varchar(255) default NULL,
  `datum_2` varchar(255) default NULL,
  `datum_3` varchar(255) default NULL,
  `bedrijfsnaam` varchar(255) default NULL,
  `prec_betaald_0` int(11) default NULL,
  `prec_betaald_1` int(11) default NULL,
  `prec_betaald_2` int(11) default NULL,
  `prec_betaald_3` int(11) default NULL,
  `factuur_nr_0` int(11) default NULL,
  `factuur_nr_1` int(11) default NULL,
  `factuur_nr_2` int(11) default NULL,
  `factuur_nr_3` int(11) default NULL,
  `btw_tonen` smallint(3) default NULL,
  `btw_prec` decimal(10,0) default NULL,
  `factuur_nr` int(11) default NULL,
  `datum` varchar(255) default NULL,
  `definitief_2` smallint(3) NOT NULL default '0',
  `definitief_3` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_omzet_akties` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) default NULL,
  `omschrijving` mediumtext,
  `datum` int(11) default NULL,
  `datum_betaald` int(11) default NULL,
  `rekeningflow` decimal(16,2) default NULL,
  `rekeningflow_btw` decimal(16,2) default NULL,
  `rekeningflow_ex` decimal(16,2) default NULL,
  `factuur_nr` int(11) default NULL,
  `grootboeknummer_id` int(11) default NULL,
  `bedrag_betaald` decimal(16,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_omzet_totaal` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) NOT NULL default '0',
  `totaal_flow` int(11) default NULL,
  `totaal_flow_btw` decimal(16,2) default NULL,
  `totaal_flow_ex` decimal(16,2) default NULL,
  `totaal_flow_12` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_overige_posten` (
  `id` int(11) NOT NULL auto_increment,
  `grootboek_id` int(11) NOT NULL default '0',
  `omschrijving` mediumtext NOT NULL,
  `debiteur` int(11) NOT NULL default '0',
  `datum` int(11) NOT NULL default '0',
  `bedrag` decimal(16,2) NOT NULL default '0.00',
  `tegenrekening` int(11) NOT NULL default '59',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_producten` (
  `id` int(11) NOT NULL auto_increment,
  `titel` varchar(255) default NULL,
  `html` mediumtext,
  `prijsperjaar` smallint(3) default NULL,
  `categorie` varchar(255) default NULL,
  `grootboeknummer_id` int(11) default NULL,
  `address_id` int(11) default NULL,
  `prijs` decimal(10,2) default NULL,
  `btw_prec` decimal(10,2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_producten_in_offertes` (
  `id` int(11) NOT NULL auto_increment,
  `producten_id` int(11) default NULL,
  `omschrijving` mediumtext,
  `link_id` int(11) default NULL,
  `aantal` int(11) default NULL,
  `btw` decimal(10,0) default NULL,
  `prijs` decimal(16,2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_relatie_type` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_soortbedrijf` (
  `id` int(11) NOT NULL auto_increment,
  `omschrijving` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `finance_teksten` (
  `id` int(11) NOT NULL auto_increment,
  `html` mediumtext,
  `description` varchar(255) default NULL,
  `type` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `forum` (
  `id` int(11) NOT NULL auto_increment,
  `ref` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `timestamp` int(11) default NULL,
  `subject` varchar(255) default NULL,
  `body` mediumtext,
  `read` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `functions` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `hours_activities` (
  `id` int(11) NOT NULL auto_increment,
  `activity` varchar(255) default NULL,
  `tarif` decimal(16,2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `hours_registration` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `timestamp_start` int(11) default NULL,
  `timestamp_end` int(11) default NULL,
  `activity_id` varchar(255) default NULL,
  `description` mediumtext,
  `is_billable` smallint(3) default '0',
  `type` smallint(3) default '0',
  PRIMARY KEY  (`id`),
  KEY `cvd_hours_registration_user_id` (`user_id`),
  KEY `cvd_hours_registration_project_id` (`project_id`),
  KEY `cvd_hours_registration_activity_id` (`activity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `issues` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) default NULL,
  `description` mediumtext,
  `solution` mediumtext,
  `project_id` int(11) default NULL,
  `registering_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `priority` smallint(3) default '0',
  `is_solved` smallint(3) default NULL,
  `address_id` int(11) default NULL,
  `email` mediumtext,
  `reference_nr` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_issues_user_id` (`user_id`),
  KEY `cvd_issues_address_id` (`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `license` (
  `name` varchar(255) default NULL,
  `code` varchar(255) default NULL,
  `timestamp` int(11) NOT NULL default '0',
  `has_project` smallint(3) default NULL,
  `has_faq` smallint(3) default NULL,
  `has_forum` smallint(3) default NULL,
  `has_issues` smallint(3) default NULL,
  `has_chat` smallint(3) default NULL,
  `has_announcements` smallint(3) default NULL,
  `has_enquete` smallint(3) default NULL,
  `has_emagazine` smallint(3) default NULL,
  `has_finance` smallint(3) default NULL,
  `has_external` smallint(3) default NULL,
  `has_snack` smallint(3) default NULL,
  `email` varchar(255) default NULL,
  `has_snelstart` smallint(3) default NULL,
  `plain` smallint(3) default NULL,
  `latest_version` varchar(255) default '0',
  `has_multivers` smallint(3) default NULL,
  `multivers_path` varchar(255) default NULL,
  `mail_interval` int(11) default NULL,
  `has_salariscom` smallint(3) default '1',
  `multivers_update` int(11) default NULL,
  `has_hrm` smallint(3) default NULL,
  `has_exact` smallint(3) default '0',
  `finance_start_date` int(11) default NULL,
  `max_upload_size` varchar(255) default '24M',
  `has_e4l` smallint(3) default '0',
  `dayquote` int(11) default NULL,
  `dayquote_nr` text,
  `mail_shell` smallint(3) default '0',
  `has_voip` smallint(3) default '0',
  `has_sales` smallint(3) default NULL,
  `filesyspath` varchar(255) default NULL,
  `has_arbo` int(11) default NULL,
  `disable_basics` int(11) default NULL,
  `has_privoxy_config` int(11) default NULL,
  `has_hypo` smallint(3) default NULL,
  `has_sync4j` int(11) default NULL,
  `mail_migrated` tinyint(3) NOT NULL default '0',
  `has_cms` tinyint(3) default NULL,
  `has_project_ext_samba` tinyint(3) default NULL,
  `mail_force_server` varchar(255) default NULL,
  `mail_lock_settings` tinyint(3) default NULL,
  `has_project_ext` tinyint(3) default NULL,
  `lang` varchar(255) default NULL,
  `force_ssl` smallint(3) default NULL,
  `has_funambol` tinyint(3) NOT NULL,
  `default_lang` char(3) default 'EN',
  `has_project_declaration` tinyint(3) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `logbook` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `message` text NOT NULL,
  `record_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
CREATE TABLE `login_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `ip` varchar(255) default NULL,
  `time` int(11) NOT NULL default '0',
  `day` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=latin1;
CREATE TABLE `mail_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `message_id` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `temp_id` int(11) NOT NULL default '0',
  `type` varchar(255) default 'application/octet-stream',
  `size` varchar(255) default '0',
  `cid` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_attachments_message_id` (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `mail_filters` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `sender` varchar(255) default NULL,
  `receipient` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `to_mapid` int(11) NOT NULL default '0',
  `priority` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_filters_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `mail_folders` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `parent_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_folders_user_id` (`user_id`),
  KEY `cvd_mail_folders_parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=latin1;
CREATE TABLE `mail_messages` (
  `id` int(11) NOT NULL auto_increment,
  `message_id` varchar(255) default NULL,
  `folder_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `project_id` int(11) default NULL,
  `sender` varchar(255) default NULL,
  `subject` mediumtext,
  `header` mediumtext,
  `body` mediumtext,
  `date` varchar(255) default NULL,
  `is_text` smallint(3) default NULL,
  `is_public` smallint(3) default NULL,
  `sender_emailaddress` varchar(255) default NULL,
  `to` mediumtext,
  `cc` mediumtext,
  `description` mediumtext,
  `is_new` smallint(3) NOT NULL default '0',
  `replyto` varchar(255) default NULL,
  `status_pop` smallint(3) NOT NULL default '0',
  `bcc` mediumtext,
  `date_received` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `askwichrel` smallint(3) default '0',
  `indexed` int(11) default NULL,
  `options` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `flag_indexed` (`indexed`),
  KEY `cvd_mail_messages_folder_id` (`folder_id`),
  KEY `cvd_mail_messages_user_id` (`user_id`),
  KEY `cvd_mail_messages_address_id` (`address_id`),
  KEY `cvd_mail_messages_project_id` (`project_id`),
  KEY `cvd_mail_messages_message_id` (`message_id`),
  KEY `cvd_mail_messages_date` (`date`),
  KEY `cvd_mail_messages_date_received` (`date_received`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
CREATE TABLE `mail_messages_data` (
  `mail_id` int(11) NOT NULL default '0',
  `body` longtext NOT NULL,
  `header` mediumtext NOT NULL,
  `mail_decoding` varchar(255) default NULL,
  PRIMARY KEY  (`mail_id`),
  KEY `cvd_mail_messages_data_mail_id` (`mail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `mail_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `users` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `mail_signatures` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `email` varchar(255) default NULL,
  `signature` mediumtext,
  `subject` varchar(255) default NULL,
  `realname` varchar(255) default NULL,
  `companyname` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_signatures_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `mail_templates` (
  `id` int(11) NOT NULL auto_increment,
  `header` mediumtext NOT NULL,
  `description` varchar(255) default NULL,
  `width` varchar(255) NOT NULL default '800',
  `repeat` smallint(3) NOT NULL default '1',
  `footer` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `mail_templates_files` (
  `id` int(11) NOT NULL auto_increment,
  `template_id` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `temp_id` int(11) NOT NULL default '0',
  `type` varchar(255) default 'application/octet-stream',
  `size` varchar(255) default '0',
  `position` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_templates_files_template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `mail_tracking` (
  `id` int(11) NOT NULL auto_increment,
  `mail_id` int(11) default NULL,
  `email` varchar(255) default NULL,
  `timestamp_first` int(11) default NULL,
  `timestamp_last` int(11) default NULL,
  `count` int(11) default NULL,
  `mail_id_2` int(11) default NULL,
  `clients` mediumtext,
  `agents` mediumtext,
  `mailcode` varchar(255) default NULL,
  `hyperlinks` mediumtext,
  `is_sent` smallint(6) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_tracking_mail_id` (`mail_id`),
  KEY `cvd_mail_tracking_mail_id_2` (`mail_id_2`),
  KEY `cvd_mail_tracking_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `meta_table` (
  `id` int(11) NOT NULL auto_increment,
  `tablename` varchar(255) default NULL,
  `fieldname` varchar(255) default NULL,
  `fieldtype` int(11) default NULL,
  `fieldorder` int(11) default NULL,
  `record_id` int(11) default NULL,
  `value` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `cvd_meta_table_record_id` (`record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `morgage` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) default NULL,
  `total_sum` decimal(16,2) default NULL,
  `investor` int(11) default NULL,
  `insurancer` int(11) default NULL,
  `year_payement` int(11) default NULL,
  `user_id` int(11) default NULL,
  `user_src` int(11) default NULL,
  `subject` varchar(255) default NULL,
  `description` mediumtext,
  `type` int(11) default NULL,
  `address_id` int(11) default NULL,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_morgage_address_id` (`address_id`),
  KEY `cvd_morgage_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `notes` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) default NULL,
  `subject` varchar(255) default NULL,
  `body` mediumtext,
  `sender` int(11) default NULL,
  `is_read` smallint(3) default NULL,
  `user_id` int(11) default NULL,
  `is_done` smallint(3) default NULL,
  `delstatus` smallint(3) NOT NULL default '0',
  `project_id` smallint(3) default NULL,
  `address_id` int(11) default NULL,
  `is_support` smallint(3) default '0',
  `extra_receipients` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_notes_user_id` (`user_id`),
  KEY `cvd_notes_sender` (`sender`),
  KEY `cvd_notes_timestamp` (`timestamp`),
  KEY `cvd_notes_project_id` (`project_id`),
  KEY `cvd_notes_address_id` (`address_id`),
  KEY `cvd_notes_delstatus` (`delstatus`),
  KEY `cvd_notes_is_done` (`is_done`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_area` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant` (
  `id` int(11) NOT NULL auto_increment,
  `title` int(11) default NULL,
  `surname` varchar(255) default NULL,
  `firstname` varchar(255) default NULL,
  `ssn` varchar(20) default NULL,
  `employee_nr` varchar(25) NOT NULL default '',
  `prescription_code` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `address2` varchar(255) default NULL,
  `zipcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `country` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `phone_nr` varchar(255) default NULL,
  `mobile_nr` varchar(255) default NULL,
  `pincode` varchar(10) default NULL,
  `clearing_nr` varchar(20) default NULL,
  `account_nr` varchar(200) default NULL,
  `iban_nr` varchar(200) default NULL,
  `tax` int(11) NOT NULL default '30',
  `is_photo` smallint(3) default NULL,
  `is_interested_in_oncall_duty` tinyint(4) NOT NULL default '0',
  `is_beingchecked` tinyint(4) NOT NULL,
  `is_blacklisted` smallint(3) default NULL,
  `warning` varchar(255) default NULL,
  `other` mediumtext,
  `modified` int(11) default NULL,
  `fax_nr` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3896 DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant_area` (
  `consultant_id` int(11) NOT NULL default '0',
  `area_id` int(11) NOT NULL default '0',
  `is_interested` tinyint(4) NOT NULL default '1',
  UNIQUE KEY `consultant_id` (`consultant_id`,`area_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant_category` (
  `consultant_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  UNIQUE KEY `consultant_id` (`consultant_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant_category_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant_company` (
  `id` int(11) NOT NULL auto_increment,
  `consultant_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant_competence` (
  `consultant_id` int(11) NOT NULL default '0',
  `competence_id` int(11) NOT NULL default '0',
  UNIQUE KEY `consultant_id` (`consultant_id`,`competence_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant_competence_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `code` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant_log` (
  `id` int(11) NOT NULL auto_increment,
  `consultant_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `logdate` date NOT NULL default '0000-00-00',
  `text` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant_timeperiod_not_interested` (
  `id` int(11) NOT NULL auto_increment,
  `consultant_id` int(11) NOT NULL default '0',
  `date_start` date NOT NULL default '0000-00-00',
  `date_end` date NOT NULL default '0000-00-00',
  `timeout` date NOT NULL default '0000-00-00',
  `modified` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`consultant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_consultant_unwantedcustomer` (
  `consultant_id` int(11) NOT NULL default '0',
  `customer_id` int(11) NOT NULL default '0',
  `modified` int(11) NOT NULL default '0',
  PRIMARY KEY  (`consultant_id`,`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_contact` (
  `id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) default '0',
  `firstname` varchar(255) default NULL,
  `surname` varchar(255) NOT NULL,
  `title` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `address2` varchar(255) default NULL,
  `zipcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `country` varchar(255) default NULL,
  `phone_nr` varchar(255) default NULL,
  `mobile_nr` varchar(255) default NULL,
  `other` mediumtext,
  `pobox` varchar(255) default NULL,
  `pobox_zipcode` varchar(255) default NULL,
  `pobox_city` varchar(255) default NULL,
  `type` int(11) default NULL,
  `modified` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_customer` (
  `id` int(11) NOT NULL auto_increment,
  `company_name` varchar(150) default NULL,
  `expense_nr` varchar(100) default NULL,
  `organisation_nr` varchar(20) default NULL,
  `customer_nr` varchar(25) NOT NULL default '',
  `address` varchar(255) default NULL,
  `address2` varchar(255) default NULL,
  `zipcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `country` varchar(255) default NULL,
  `is_active` smallint(3) default NULL,
  `quickfacts` text,
  `consultant_directory_id` int(11) default NULL,
  `other_documents_directory_id` int(11) NOT NULL default '0',
  `other` mediumtext,
  `modified` int(11) default NULL,
  `website` varchar(255) default NULL,
  `fax_nr` varchar(255) default NULL,
  `telephone_nr` varchar(255) default NULL,
  `billing_address` varchar(255) default NULL,
  `type` int(11) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_customer_agreement` (
  `id` int(11) NOT NULL auto_increment,
  `customer_group_id` int(11) NOT NULL default '0',
  `name` varchar(200) NOT NULL default '',
  `number` varchar(100) NOT NULL default '',
  `fine` tinyint(4) NOT NULL default '0',
  `paid_holidays` tinyint(4) NOT NULL default '0',
  `on_call_duty_agreement` text NOT NULL,
  `special_agreement` text NOT NULL,
  `contact_id` int(11) NOT NULL default '0',
  `document_id` int(11) NOT NULL default '0',
  `other` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_customer_group` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `other` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_customer_log` (
  `id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `logdate` date NOT NULL default '0000-00-00',
  `text` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_customer_mark` (
  `residence_id` int(11) NOT NULL default '0',
  `consultant_id` int(11) NOT NULL default '0',
  `date_set` int(11) NOT NULL default '0',
  PRIMARY KEY  (`residence_id`),
  KEY `consultant_id` (`consultant_id`,`date_set`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_customer_unwantedconsultant` (
  `consultant_id` int(11) NOT NULL default '0',
  `customer_id` int(11) NOT NULL default '0',
  `modified` int(11) NOT NULL default '0',
  PRIMARY KEY  (`consultant_id`,`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_holidays` (
  `holiday_date` date NOT NULL default '0000-00-00',
  `title` varchar(100) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_residence` (
  `id` int(11) NOT NULL auto_increment,
  `contact_id` int(11) default NULL,
  `address` varchar(255) default NULL,
  `address2` varchar(255) default NULL,
  `zipcode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `country` varchar(255) default NULL,
  `other` mediumtext,
  `keyretrieving` text,
  `description` text NOT NULL,
  `cost_night` int(11) default NULL,
  `cost_week` int(11) default NULL,
  `documents_directory_id` int(11) default NULL,
  `modified` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_residence_mark` (
  `residence_id` int(11) NOT NULL default '0',
  `consultant_id` int(11) NOT NULL default '0',
  `date_set` int(11) NOT NULL default '0',
  PRIMARY KEY  (`residence_id`),
  KEY `consultant_id` (`consultant_id`,`date_set`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_vacancy` (
  `id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL default '0',
  `vacancy_type_id` int(11) NOT NULL default '0',
  `customer_agreement_id` int(11) NOT NULL default '0',
  `consultant_category_id` int(11) NOT NULL default '0',
  `real_hours` int(11) NOT NULL default '0',
  `recalculated_hours` int(11) NOT NULL default '0',
  `total_invoicesum_week` int(11) NOT NULL default '0',
  `agreement_changes` text NOT NULL,
  `is_fine` tinyint(4) NOT NULL default '0',
  `is_wished` tinyint(4) NOT NULL default '0',
  `other` text NOT NULL,
  `modified` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_vacancy_booking` (
  `id` int(11) NOT NULL auto_increment,
  `consultant_id` int(11) NOT NULL default '0',
  `salary` int(11) NOT NULL default '0',
  `traktamente_dygn` int(11) NOT NULL default '0',
  `is_train` tinyint(4) NOT NULL default '0',
  `is_flight` tinyint(4) NOT NULL default '0',
  `is_car` tinyint(4) NOT NULL default '0',
  `is_taxi` tinyint(4) NOT NULL default '0',
  `is_fakturerar` tinyint(4) NOT NULL default '0',
  `other` text NOT NULL,
  `modified` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_vacancy_not_interested` (
  `vacancy_id` int(11) NOT NULL default '0',
  `consultant_id` int(11) NOT NULL default '0',
  `timeout` date NOT NULL default '0000-00-00',
  `modified` int(11) NOT NULL default '0',
  PRIMARY KEY  (`vacancy_id`,`consultant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_vacancy_timeperiod` (
  `id` int(11) NOT NULL auto_increment,
  `year` int(11) NOT NULL default '0',
  `week` tinyint(4) default NULL,
  `day` tinyint(4) NOT NULL default '0',
  `vacancy_date` date NOT NULL default '0000-00-00',
  `vacancy_id` int(11) default NULL,
  `vacancy_booking_id` int(11) default NULL,
  `time_start` time NOT NULL default '00:00:00',
  `time_end` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `nrd_vacancy_type` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
CREATE TABLE `poll_answers` (
  `id` int(11) NOT NULL auto_increment,
  `poll_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `answer` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `polls` (
  `id` int(11) NOT NULL auto_increment,
  `question` mediumtext,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `project` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` mediumtext,
  `manager` int(11) default NULL,
  `group_id` smallint(3) NOT NULL default '0',
  `is_active` smallint(3) default NULL,
  `status` smallint(3) default '0',
  `address_id` int(11) default NULL,
  `lfact` int(11) default NULL,
  `budget` int(11) default NULL,
  `hours` int(11) default NULL,
  `address_businesscard_id` int(11) default NULL,
  `multirel` varchar(255) default NULL,
  `users` varchar(255) default NULL,
  `executor` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `cvd_project_address_id` (`address_id`),
  KEY `cvd_project_group_id` (`group_id`),
  KEY `cvd_project_is_active` (`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `projects_declaration_extrainfo` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `task_date` int(11) NOT NULL,
  `damage_date` int(11) NOT NULL,
  `accident_type` int(11) NOT NULL,
  `perc_liabilities_wished` float(16,2) NOT NULL,
  `perc_liabilities_recognised` float(16,2) NOT NULL,
  `constituent` int(11) NOT NULL,
  `tarif` int(11) NOT NULL,
  `is_NCNP` int(11) NOT NULL,
  `perc_NCNP` float(16,2) NOT NULL,
  `client` int(11) NOT NULL,
  `adversary` varchar(255) NOT NULL,
  `expertise` varchar(255) NOT NULL,
  `lesion` int(11) NOT NULL,
  `lesion_description` text NOT NULL,
  `hospitalisation` int(11) NOT NULL,
  `incapacity_for_work` int(11) NOT NULL,
  `profession` int(11) NOT NULL,
  `employment` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `projects_ext_activities` (
  `id` int(11) NOT NULL auto_increment,
  `department_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `users` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `projects_ext_departments` (
  `id` int(11) NOT NULL auto_increment,
  `department` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `address_id` int(11) NOT NULL,
  `users` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `projects_ext_extrainfo` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `project_year` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `projects_ext_metafields` (
  `id` int(11) NOT NULL auto_increment,
  `field_name` varchar(255) NOT NULL,
  `field_type` smallint(3) NOT NULL,
  `field_order` int(11) NOT NULL,
  `activity` int(11) NOT NULL,
  `show_list` tinyint(3) NOT NULL,
  `default_value` varchar(255) NOT NULL,
  `large_data` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `projects_ext_metavalues` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `meta_value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `projects_master` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` mediumtext,
  `manager` int(11) default NULL,
  `is_active` smallint(3) default NULL,
  `status` smallint(3) default '0',
  `address_id` int(11) default NULL,
  `address_businesscard_id` int(11) default NULL,
  `users` varchar(255) default NULL,
  `ext_department` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `rssfeeds` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `homepage` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `count` int(11) default '5',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `rssitems` (
  `id` int(11) NOT NULL auto_increment,
  `feed` int(11) default NULL,
  `subject` varchar(255) default NULL,
  `body` mediumtext,
  `link` varchar(255) default NULL,
  `date` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `sales` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(255) default NULL,
  `expected_score` int(11) default NULL,
  `total_sum` decimal(16,2) default NULL,
  `timestamp_proposal` int(11) default NULL,
  `timestamp_order` int(11) default NULL,
  `timestamp_invoice` int(11) default NULL,
  `address_id` int(11) default NULL,
  `description` mediumtext,
  `is_active` int(11) default NULL,
  `timestamp_prospect` int(11) default NULL,
  `user_id_modified` int(11) default NULL,
  `user_sales_id` int(11) default NULL,
  `user_id_create` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `snack_items` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `snack_order` (
  `id` int(11) NOT NULL auto_increment,
  `ammount` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `statistics` (
  `table` varchar(255) default NULL,
  `updates` int(11) default NULL,
  `vacuum` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `status_conn` (
  `user_id` int(11) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `status_list` (
  `id` int(11) NOT NULL auto_increment,
  `msg_id` varchar(255) NOT NULL default '0',
  `mail_id` int(11) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `mark_delete` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `support` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) default NULL,
  `body` mediumtext,
  `type` int(11) default NULL,
  `relation_name` mediumtext,
  `email` mediumtext,
  `reference_nr` int(11) default NULL,
  `customer_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `templates` (
  `id` int(11) NOT NULL auto_increment,
  `address_businesscard_id` smallint(3) NOT NULL default '0',
  `font` varchar(255) default NULL,
  `fontsize` int(11) NOT NULL default '0',
  `body` mediumtext,
  `footer` mediumtext,
  `sender` mediumtext,
  `address_id` int(11) NOT NULL default '0',
  `description` varchar(255) default NULL,
  `classification` varchar(255) default NULL,
  `ids` mediumtext,
  `header` varchar(255) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `settings_id` int(11) NOT NULL default '0',
  `date` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `negative_classification` varchar(255) default NULL,
  `multirel` varchar(255) default NULL,
  `save_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `and_or` varchar(255) default 'AND',
  `fax_nr` smallint(3) default NULL,
  `signature` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `templates_files` (
  `id` int(11) NOT NULL auto_increment,
  `template_id` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `temp_id` int(11) NOT NULL default '0',
  `type` varchar(255) default 'application/octet-stream',
  `size` varchar(255) default '0',
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_templates_files_template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `templates_settings` (
  `id` int(11) NOT NULL auto_increment,
  `page_left` decimal(16,2) NOT NULL default '0.00',
  `page_top` decimal(16,2) NOT NULL default '0.00',
  `page_right` decimal(16,2) NOT NULL default '0.00',
  `address_left` decimal(16,2) NOT NULL default '0.00',
  `address_width` decimal(16,2) NOT NULL default '0.00',
  `address_top` decimal(16,2) NOT NULL default '0.00',
  `address_position` smallint(3) NOT NULL default '0',
  `description` varchar(255) default NULL,
  `footer_position` varchar(10) NOT NULL,
  `footer_text` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `todo` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `is_done` smallint(3) default '0',
  `subject` varchar(255) default NULL,
  `body` mediumtext,
  `address_id` int(11) default NULL,
  `timestamp_end` int(11) default NULL,
  `project_id` int(11) default NULL,
  `is_alert` smallint(3) default '0',
  `is_customercontact` int(11) default NULL,
  `sync_guid` int(11) default NULL,
  `sync_hash` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `todo_sync` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `sync_guid` int(11) default NULL,
  `action` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `members` varchar(255) default NULL,
  `manager` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `userchangelog` (
  `id` int(11) NOT NULL auto_increment,
  `manager` int(11) default NULL,
  `user_id` int(11) default NULL,
  `timestamp` int(11) default NULL,
  `change` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `pers_nr` int(11) default NULL,
  `xs_usermanage` smallint(3) default '0',
  `xs_addressmanage` smallint(3) default NULL,
  `xs_projectmanage` smallint(3) default NULL,
  `xs_forummanage` smallint(3) default NULL,
  `address_id` int(11) default NULL,
  `xs_pollmanage` smallint(3) default NULL,
  `xs_faqmanage` smallint(3) default NULL,
  `xs_issuemanage` smallint(3) default NULL,
  `xs_chatmanage` smallint(3) default NULL,
  `xs_turnovermanage` smallint(3) default NULL,
  `xs_notemanage` smallint(3) default NULL,
  `xs_todomanage` smallint(3) default '0',
  `comment` mediumtext,
  `is_active` smallint(3) NOT NULL default '1',
  `style` smallint(3) default NULL,
  `mail_server` varchar(255) default NULL,
  `mail_user_id` varchar(255) default NULL,
  `mail_password` varchar(255) default NULL,
  `mail_email` varchar(255) default NULL,
  `mail_email1` varchar(255) default NULL,
  `mail_html` smallint(3) default NULL,
  `mail_signature` mediumtext,
  `mail_showcount` smallint(3) default '0',
  `mail_deltime` int(11) default NULL,
  `days` smallint(3) default '0',
  `htmleditor` smallint(3) NOT NULL default '2',
  `addressaccountmanage` varchar(255) default NULL,
  `calendarselection` varchar(255) default NULL,
  `showhelp` int(11) NOT NULL default '0',
  `showpopup` smallint(3) default '1',
  `xs_salariscommanage` smallint(3) default '0',
  `mail_server_deltime` int(11) default '1',
  `xs_companyinfomanage` smallint(3) default '0',
  `xs_hrmmanage` smallint(3) default '0',
  `language` char(2) NOT NULL default 'NL',
  `employer_id` int(11) default NULL,
  `automatic_logout` smallint(3) default '0',
  `mail_view_textmail_only` smallint(3) default NULL,
  `e4l_update` int(11) default NULL,
  `dayquote` int(11) default NULL,
  `infowin_altmethod` int(11) default NULL,
  `xs_e4l` smallint(3) default '0',
  `xs_filemanage` int(11) default NULL,
  `xs_limitusermanage` int(11) default NULL,
  `change_theme` int(11) default NULL,
  `xs_relationmanage` int(11) default NULL,
  `xs_newslettermanage` int(11) default NULL,
  `renderstatus` mediumtext,
  `mail_forward` int(11) default NULL,
  `showvoip` smallint(3) default '0',
  `signature` varchar(255) default NULL,
  `voip_device` varchar(255) default NULL,
  `xs_salesmanage` smallint(3) default NULL,
  `e4l_username` varchar(255) default NULL,
  `e4l_password` varchar(255) default NULL,
  `xs_arbo` int(11) default NULL,
  `xs_arbo_validated` int(11) default NULL,
  `alternative_note_view_desktop` int(11) default NULL,
  `calendarmode` int(11) default '1',
  `rssnews` int(11) default NULL,
  `mail_showbcc` int(11) default NULL,
  `mail_imap` int(11) default NULL,
  `xs_hypo` smallint(3) default NULL,
  `mail_num_items` int(11) default NULL,
  `sync4j_source` varchar(255) default NULL,
  `sync4j_path` varchar(255) default NULL,
  `sync4j_source_adres` varchar(255) default NULL,
  `sync4j_source_todo` varchar(255) default NULL,
  `xs_funambol` tinyint(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
CREATE TABLE `various` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `content` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `voting` (
  `id` int(10) NOT NULL auto_increment,
  `voteDesc` text NOT NULL,
  `voted_servers` varchar(100) NOT NULL default '',
  `yes` tinyint(2) NOT NULL default '0',
  `no` tinyint(2) NOT NULL default '0',
  `abstain` tinyint(2) NOT NULL default '0',
  `novote` tinyint(2) NOT NULL default '0',
  `voteStarter` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

