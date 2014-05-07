-- MySQL dump 10.9
--
-- Host: localhost    Database: covide_6
-- ------------------------------------------------------
-- Server version	4.1.15-Debian_1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `active_calls`
--

DROP TABLE IF EXISTS `active_calls`;
CREATE TABLE `active_calls` (
  `name` varchar(255) default NULL,
  `address_id` int(11) default NULL,
  `timestamp` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `active_calls`
--


/*!40000 ALTER TABLE `active_calls` DISABLE KEYS */;
LOCK TABLES `active_calls` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `active_calls` ENABLE KEYS */;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
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
  PRIMARY KEY  (`id`),
  KEY `address_classification` (`classification`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `address`
--


/*!40000 ALTER TABLE `address` DISABLE KEYS */;
LOCK TABLES `address` WRITE;
INSERT INTO `address` VALUES (8,'','','Terrazur','A. Fokkerstraat 27-1','3772 MP','Barneveld','0342-490364','0342-423577','info@terrazur.nl',3,1,NULL,1,'',14144,NULL,NULL,NULL,'http://www.terrazur.nl',0,'Dhr. W.  Massier','Beste Willem',1,1,NULL,NULL,NULL,NULL,'','','',NULL,0,1,1,1,'W.','Willem','','Massier','',0,NULL,NULL,0,NULL,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `address` ENABLE KEYS */;

--
-- Table structure for table `address_businesscards`
--

DROP TABLE IF EXISTS `address_businesscards`;
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
  PRIMARY KEY  (`id`),
  KEY `cvd_address_businesscards_address_id` (`address_id`),
  KEY `addressbcards_classification` (`classification`),
  KEY `addressbcards_addressid` (`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `address_businesscards`
--


/*!40000 ALTER TABLE `address_businesscards` DISABLE KEYS */;
LOCK TABLES `address_businesscards` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `address_businesscards` ENABLE KEYS */;

--
-- Table structure for table `address_classifications`
--

DROP TABLE IF EXISTS `address_classifications`;
CREATE TABLE `address_classifications` (
  `id` int(11) NOT NULL auto_increment,
  `description` varchar(255) default NULL,
  `is_active` smallint(3) default '0',
  `is_locked` smallint(3) default NULL,
  `subtype` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `address_classifications`
--


/*!40000 ALTER TABLE `address_classifications` DISABLE KEYS */;
LOCK TABLES `address_classifications` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `address_classifications` ENABLE KEYS */;

--
-- Table structure for table `address_info`
--

DROP TABLE IF EXISTS `address_info`;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `address_info`
--


/*!40000 ALTER TABLE `address_info` DISABLE KEYS */;
LOCK TABLES `address_info` WRITE;
INSERT INTO `address_info` VALUES (8,8,'','','',NULL,NULL,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `address_info` ENABLE KEYS */;

--
-- Table structure for table `address_multivers`
--

DROP TABLE IF EXISTS `address_multivers`;
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

--
-- Dumping data for table `address_multivers`
--


/*!40000 ALTER TABLE `address_multivers` DISABLE KEYS */;
LOCK TABLES `address_multivers` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `address_multivers` ENABLE KEYS */;

--
-- Table structure for table `address_other`
--

DROP TABLE IF EXISTS `address_other`;
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

--
-- Dumping data for table `address_other`
--


/*!40000 ALTER TABLE `address_other` DISABLE KEYS */;
LOCK TABLES `address_other` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `address_other` ENABLE KEYS */;

--
-- Table structure for table `address_private`
--

DROP TABLE IF EXISTS `address_private`;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `address_private`
--


/*!40000 ALTER TABLE `address_private` DISABLE KEYS */;
LOCK TABLES `address_private` WRITE;
INSERT INTO `address_private` VALUES (3,'BV','Covide','Covideweg 1','1111 CC','covide','0111-111111','','test@covide.nl',2,0,1,'','','','','','',1,'','Nederland',0,NULL,NULL,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `address_private` ENABLE KEYS */;

--
-- Table structure for table `address_sync`
--

DROP TABLE IF EXISTS `address_sync`;
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

--
-- Dumping data for table `address_sync`
--


/*!40000 ALTER TABLE `address_sync` DISABLE KEYS */;
LOCK TABLES `address_sync` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `address_sync` ENABLE KEYS */;

--
-- Table structure for table `address_sync_guid`
--

DROP TABLE IF EXISTS `address_sync_guid`;
CREATE TABLE `address_sync_guid` (
  `id` int(11) NOT NULL auto_increment,
  `sync_id` int(11) default NULL,
  `sync_guid` int(11) default NULL,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `address_sync_guid`
--


/*!40000 ALTER TABLE `address_sync_guid` DISABLE KEYS */;
LOCK TABLES `address_sync_guid` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `address_sync_guid` ENABLE KEYS */;

--
-- Table structure for table `address_sync_records`
--

DROP TABLE IF EXISTS `address_sync_records`;
CREATE TABLE `address_sync_records` (
  `id` int(11) NOT NULL auto_increment,
  `address_table` varchar(255) default NULL,
  `address_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `address_sync_records`
--


/*!40000 ALTER TABLE `address_sync_records` DISABLE KEYS */;
LOCK TABLES `address_sync_records` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `address_sync_records` ENABLE KEYS */;

--
-- Table structure for table `agenda_sync`
--

DROP TABLE IF EXISTS `agenda_sync`;
CREATE TABLE `agenda_sync` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `sync_guid` int(11) default NULL,
  `action` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `agenda_sync`
--


/*!40000 ALTER TABLE `agenda_sync` DISABLE KEYS */;
LOCK TABLES `agenda_sync` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `agenda_sync` ENABLE KEYS */;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(255) default NULL,
  `body` mediumtext,
  `is_popup` smallint(3) default NULL,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `announcements`
--


/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
LOCK TABLES `announcements` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;

--
-- Table structure for table `arbo_arbo`
--

DROP TABLE IF EXISTS `arbo_arbo`;
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

--
-- Dumping data for table `arbo_arbo`
--


/*!40000 ALTER TABLE `arbo_arbo` DISABLE KEYS */;
LOCK TABLES `arbo_arbo` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `arbo_arbo` ENABLE KEYS */;

--
-- Table structure for table `arbo_verslag`
--

DROP TABLE IF EXISTS `arbo_verslag`;
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

--
-- Dumping data for table `arbo_verslag`
--


/*!40000 ALTER TABLE `arbo_verslag` DISABLE KEYS */;
LOCK TABLES `arbo_verslag` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `arbo_verslag` ENABLE KEYS */;

--
-- Table structure for table `arbo_ziek`
--

DROP TABLE IF EXISTS `arbo_ziek`;
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

--
-- Dumping data for table `arbo_ziek`
--


/*!40000 ALTER TABLE `arbo_ziek` DISABLE KEYS */;
LOCK TABLES `arbo_ziek` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `arbo_ziek` ENABLE KEYS */;

--
-- Table structure for table `bayham_settings`
--

DROP TABLE IF EXISTS `bayham_settings`;
CREATE TABLE `bayham_settings` (
  `companyid` varchar(255) default NULL,
  `userid` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `sender` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bayham_settings`
--


/*!40000 ALTER TABLE `bayham_settings` DISABLE KEYS */;
LOCK TABLES `bayham_settings` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `bayham_settings` ENABLE KEYS */;

--
-- Table structure for table `bugs`
--

DROP TABLE IF EXISTS `bugs`;
CREATE TABLE `bugs` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `module` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `description` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bugs`
--


/*!40000 ALTER TABLE `bugs` DISABLE KEYS */;
LOCK TABLES `bugs` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `bugs` ENABLE KEYS */;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
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
  PRIMARY KEY  (`id`),
  KEY `cvd_calendar_user_id` (`user_id`),
  KEY `cvd_calendar_user_timestamp_start` (`timestamp_start`),
  KEY `cvd_calendar_user_timestamp_end` (`timestamp_end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `calendar`
--


/*!40000 ALTER TABLE `calendar` DISABLE KEYS */;
LOCK TABLES `calendar` WRITE;
INSERT INTO `calendar` VALUES (1,1080824400,1080826200,'aanmaken covide',1,8,3,0,0,0,1,0,0,NULL,'2004-04-01 15:00:00','2004-04-01 15:30:00',NULL,NULL,0,0,'0','D',0,0,0,0,'',0,0,0,NULL,NULL,NULL,NULL),(2,1143021600,1143025200,'uoeauoeauoae',2,0,0,0,0,0,0,900,0,'','2006-03-22 11:07:04','0000-00-00 00:00:00',0,0,0,0,'0','0',0,0,0,0,NULL,0,0,1143022024,2,NULL,NULL,'ueaouaoe');
UNLOCK TABLES;
/*!40000 ALTER TABLE `calendar` ENABLE KEYS */;

--
-- Table structure for table `calendar_permissions`
--

DROP TABLE IF EXISTS `calendar_permissions`;
CREATE TABLE `calendar_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `user_id_visitor` int(11) default NULL,
  `permissions` varchar(255) default 'RO',
  PRIMARY KEY  (`id`),
  KEY `cvd_calendar_permissions_user_id` (`user_id`),
  KEY `cvd_calendar_permissions_user_id_visitor` (`user_id_visitor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `calendar_permissions`
--


/*!40000 ALTER TABLE `calendar_permissions` DISABLE KEYS */;
LOCK TABLES `calendar_permissions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `calendar_permissions` ENABLE KEYS */;

--
-- Table structure for table `cdr`
--

DROP TABLE IF EXISTS `cdr`;
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

--
-- Dumping data for table `cdr`
--


/*!40000 ALTER TABLE `cdr` DISABLE KEYS */;
LOCK TABLES `cdr` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `cdr` ENABLE KEYS */;

--
-- Table structure for table `chat_rooms`
--

DROP TABLE IF EXISTS `chat_rooms`;
CREATE TABLE `chat_rooms` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `users` mediumtext,
  `topic` varchar(255) default NULL,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chat_rooms`
--


/*!40000 ALTER TABLE `chat_rooms` DISABLE KEYS */;
LOCK TABLES `chat_rooms` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `chat_rooms` ENABLE KEYS */;

--
-- Table structure for table `chat_text`
--

DROP TABLE IF EXISTS `chat_text`;
CREATE TABLE `chat_text` (
  `id` int(11) NOT NULL auto_increment,
  `room` int(11) default NULL,
  `user` int(11) default NULL,
  `text` varchar(255) default NULL,
  `timestamp` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chat_text`
--


/*!40000 ALTER TABLE `chat_text` DISABLE KEYS */;
LOCK TABLES `chat_text` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `chat_text` ENABLE KEYS */;

--
-- Table structure for table `cms_data`
--

DROP TABLE IF EXISTS `cms_data`;
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

--
-- Dumping data for table `cms_data`
--


/*!40000 ALTER TABLE `cms_data` DISABLE KEYS */;
LOCK TABLES `cms_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `cms_data` ENABLE KEYS */;

--
-- Table structure for table `cms_files`
--

DROP TABLE IF EXISTS `cms_files`;
CREATE TABLE `cms_files` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `type` varchar(255) default 'application/octet-stream',
  `size` varchar(255) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_files`
--


/*!40000 ALTER TABLE `cms_files` DISABLE KEYS */;
LOCK TABLES `cms_files` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `cms_files` ENABLE KEYS */;

--
-- Table structure for table `cms_images`
--

DROP TABLE IF EXISTS `cms_images`;
CREATE TABLE `cms_images` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL default '0',
  `path` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_images`
--


/*!40000 ALTER TABLE `cms_images` DISABLE KEYS */;
LOCK TABLES `cms_images` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `cms_images` ENABLE KEYS */;

--
-- Table structure for table `employees_info`
--

DROP TABLE IF EXISTS `employees_info`;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employees_info`
--


/*!40000 ALTER TABLE `employees_info` DISABLE KEYS */;
LOCK TABLES `employees_info` WRITE;
INSERT INTO `employees_info` VALUES (1,2,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `employees_info` ENABLE KEYS */;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL auto_increment,
  `category_id` int(11) default NULL,
  `question` mediumtext,
  `answer` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faq`
--


/*!40000 ALTER TABLE `faq` DISABLE KEYS */;
LOCK TABLES `faq` WRITE;
INSERT INTO `faq` VALUES (1,1,'Waar kan ik de handleiding van Covide vinden?','In de bovenste rij iconen is het tweede icoontje van links (met het vergrootglas) de handleiding.\r\nAls u daar op klikt verschijnt er een popup venster met de handleiding.\r\n'),(2,1,'Waar staat Covide voor?','Covide is een afkorting van Coöperative virtual desktop.\r\n\r\nWat zoveel wil zeggen als:\r\nSamenwerken is het uitgangspunt van Covide. Zowel met klanten, collega\'s als met andere software pakketten, dat is het Coöperatieve karakter.\r\n\r\nDe virtuele desktop maakt de optimale mobiele werkplek mogelijk.\r\n\r\nCovide staat samengevat dus voor:\r\nSamenwerken via een volledig mobiele werkplek voor elke medewerker en leidinggevende met oog voor hun klant.\r\n\r\n');
UNLOCK TABLES;
/*!40000 ALTER TABLE `faq` ENABLE KEYS */;

--
-- Table structure for table `faq_cat`
--

DROP TABLE IF EXISTS `faq_cat`;
CREATE TABLE `faq_cat` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faq_cat`
--


/*!40000 ALTER TABLE `faq_cat` DISABLE KEYS */;
LOCK TABLES `faq_cat` WRITE;
INSERT INTO `faq_cat` VALUES (1,'Covide');
UNLOCK TABLES;
/*!40000 ALTER TABLE `faq_cat` ENABLE KEYS */;

--
-- Table structure for table `faxes`
--

DROP TABLE IF EXISTS `faxes`;
CREATE TABLE `faxes` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default NULL,
  `sender` varchar(255) default NULL,
  `receiver` varchar(255) default NULL,
  `relation_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_faxes_relation_id` (`relation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faxes`
--


/*!40000 ALTER TABLE `faxes` DISABLE KEYS */;
LOCK TABLES `faxes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `faxes` ENABLE KEYS */;

--
-- Table structure for table `filesys_files`
--

DROP TABLE IF EXISTS `filesys_files`;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `filesys_files`
--


/*!40000 ALTER TABLE `filesys_files` DISABLE KEYS */;
LOCK TABLES `filesys_files` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `filesys_files` ENABLE KEYS */;

--
-- Table structure for table `filesys_folders`
--

DROP TABLE IF EXISTS `filesys_folders`;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `filesys_folders`
--


/*!40000 ALTER TABLE `filesys_folders` DISABLE KEYS */;
LOCK TABLES `filesys_folders` WRITE;
INSERT INTO `filesys_folders` VALUES (3,'openbare mappen',1,0,NULL,NULL,0,NULL,NULL,NULL,0,0),(12,'Terrazur',1,1,8,NULL,4,NULL,NULL,NULL,0,0),(23,'mijn documenten',0,0,NULL,1,0,NULL,NULL,NULL,0,0),(20,'medewerkers',1,0,NULL,NULL,19,NULL,NULL,NULL,1,0),(21,'oud-medewerkers',1,0,NULL,NULL,19,NULL,NULL,NULL,1,0),(24,'projecten',1,0,NULL,NULL,0,NULL,NULL,NULL,1,0),(19,'hrm',1,0,NULL,NULL,0,NULL,NULL,NULL,1,0),(4,'relaties',1,0,NULL,NULL,0,NULL,NULL,NULL,1,0),(22,'covide',1,0,NULL,NULL,20,NULL,NULL,2,0,0),(108,'mijn documenten',0,0,NULL,2,0,NULL,NULL,NULL,0,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `filesys_folders` ENABLE KEYS */;

--
-- Table structure for table `filesys_permissions`
--

DROP TABLE IF EXISTS `filesys_permissions`;
CREATE TABLE `filesys_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `folder_id` int(11) default NULL,
  `user_id` varchar(255) default NULL,
  `permissions` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_filesys_permissions_user_id` (`user_id`),
  KEY `cvd_filesys_permissions_folder_id` (`folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `filesys_permissions`
--


/*!40000 ALTER TABLE `filesys_permissions` DISABLE KEYS */;
LOCK TABLES `filesys_permissions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `filesys_permissions` ENABLE KEYS */;

--
-- Table structure for table `finance_akties`
--

DROP TABLE IF EXISTS `finance_akties`;
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

--
-- Dumping data for table `finance_akties`
--


/*!40000 ALTER TABLE `finance_akties` DISABLE KEYS */;
LOCK TABLES `finance_akties` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_akties` ENABLE KEYS */;

--
-- Table structure for table `finance_begin_standen_finance`
--

DROP TABLE IF EXISTS `finance_begin_standen_finance`;
CREATE TABLE `finance_begin_standen_finance` (
  `id` int(11) NOT NULL auto_increment,
  `grootboek_id` int(11) NOT NULL default '0',
  `stand` decimal(16,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_begin_standen_finance`
--


/*!40000 ALTER TABLE `finance_begin_standen_finance` DISABLE KEYS */;
LOCK TABLES `finance_begin_standen_finance` WRITE;
INSERT INTO `finance_begin_standen_finance` VALUES (1,1000,'0.00'),(2,1100,'0.00');
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_begin_standen_finance` ENABLE KEYS */;

--
-- Table structure for table `finance_boekingen`
--

DROP TABLE IF EXISTS `finance_boekingen`;
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

--
-- Dumping data for table `finance_boekingen`
--


/*!40000 ALTER TABLE `finance_boekingen` DISABLE KEYS */;
LOCK TABLES `finance_boekingen` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_boekingen` ENABLE KEYS */;

--
-- Table structure for table `finance_boekingen_20012003`
--

DROP TABLE IF EXISTS `finance_boekingen_20012003`;
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

--
-- Dumping data for table `finance_boekingen_20012003`
--


/*!40000 ALTER TABLE `finance_boekingen_20012003` DISABLE KEYS */;
LOCK TABLES `finance_boekingen_20012003` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_boekingen_20012003` ENABLE KEYS */;

--
-- Table structure for table `finance_grootboeknummers`
--

DROP TABLE IF EXISTS `finance_grootboeknummers`;
CREATE TABLE `finance_grootboeknummers` (
  `id` int(11) NOT NULL auto_increment,
  `nr` int(11) default NULL,
  `titel` varchar(255) default NULL,
  `debiteur` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_grootboeknummers`
--


/*!40000 ALTER TABLE `finance_grootboeknummers` DISABLE KEYS */;
LOCK TABLES `finance_grootboeknummers` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_grootboeknummers` ENABLE KEYS */;

--
-- Table structure for table `finance_inkopen`
--

DROP TABLE IF EXISTS `finance_inkopen`;
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

--
-- Dumping data for table `finance_inkopen`
--


/*!40000 ALTER TABLE `finance_inkopen` DISABLE KEYS */;
LOCK TABLES `finance_inkopen` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_inkopen` ENABLE KEYS */;

--
-- Table structure for table `finance_jaar_afsluitingen`
--

DROP TABLE IF EXISTS `finance_jaar_afsluitingen`;
CREATE TABLE `finance_jaar_afsluitingen` (
  `jaar` int(11) NOT NULL default '0',
  `datum_afgesloten` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_jaar_afsluitingen`
--


/*!40000 ALTER TABLE `finance_jaar_afsluitingen` DISABLE KEYS */;
LOCK TABLES `finance_jaar_afsluitingen` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_jaar_afsluitingen` ENABLE KEYS */;

--
-- Table structure for table `finance_klanten`
--

DROP TABLE IF EXISTS `finance_klanten`;
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

--
-- Dumping data for table `finance_klanten`
--


/*!40000 ALTER TABLE `finance_klanten` DISABLE KEYS */;
LOCK TABLES `finance_klanten` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_klanten` ENABLE KEYS */;

--
-- Table structure for table `finance_offertes`
--

DROP TABLE IF EXISTS `finance_offertes`;
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

--
-- Dumping data for table `finance_offertes`
--


/*!40000 ALTER TABLE `finance_offertes` DISABLE KEYS */;
LOCK TABLES `finance_offertes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_offertes` ENABLE KEYS */;

--
-- Table structure for table `finance_omzet_akties`
--

DROP TABLE IF EXISTS `finance_omzet_akties`;
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

--
-- Dumping data for table `finance_omzet_akties`
--


/*!40000 ALTER TABLE `finance_omzet_akties` DISABLE KEYS */;
LOCK TABLES `finance_omzet_akties` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_omzet_akties` ENABLE KEYS */;

--
-- Table structure for table `finance_omzet_totaal`
--

DROP TABLE IF EXISTS `finance_omzet_totaal`;
CREATE TABLE `finance_omzet_totaal` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) NOT NULL default '0',
  `totaal_flow` int(11) default NULL,
  `totaal_flow_btw` decimal(16,2) default NULL,
  `totaal_flow_ex` decimal(16,2) default NULL,
  `totaal_flow_12` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_omzet_totaal`
--


/*!40000 ALTER TABLE `finance_omzet_totaal` DISABLE KEYS */;
LOCK TABLES `finance_omzet_totaal` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_omzet_totaal` ENABLE KEYS */;

--
-- Table structure for table `finance_overige_posten`
--

DROP TABLE IF EXISTS `finance_overige_posten`;
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

--
-- Dumping data for table `finance_overige_posten`
--


/*!40000 ALTER TABLE `finance_overige_posten` DISABLE KEYS */;
LOCK TABLES `finance_overige_posten` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_overige_posten` ENABLE KEYS */;

--
-- Table structure for table `finance_producten`
--

DROP TABLE IF EXISTS `finance_producten`;
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

--
-- Dumping data for table `finance_producten`
--


/*!40000 ALTER TABLE `finance_producten` DISABLE KEYS */;
LOCK TABLES `finance_producten` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_producten` ENABLE KEYS */;

--
-- Table structure for table `finance_producten_in_offertes`
--

DROP TABLE IF EXISTS `finance_producten_in_offertes`;
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

--
-- Dumping data for table `finance_producten_in_offertes`
--


/*!40000 ALTER TABLE `finance_producten_in_offertes` DISABLE KEYS */;
LOCK TABLES `finance_producten_in_offertes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_producten_in_offertes` ENABLE KEYS */;

--
-- Table structure for table `finance_relatie_type`
--

DROP TABLE IF EXISTS `finance_relatie_type`;
CREATE TABLE `finance_relatie_type` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_relatie_type`
--


/*!40000 ALTER TABLE `finance_relatie_type` DISABLE KEYS */;
LOCK TABLES `finance_relatie_type` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_relatie_type` ENABLE KEYS */;

--
-- Table structure for table `finance_soortbedrijf`
--

DROP TABLE IF EXISTS `finance_soortbedrijf`;
CREATE TABLE `finance_soortbedrijf` (
  `id` int(11) NOT NULL auto_increment,
  `omschrijving` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_soortbedrijf`
--


/*!40000 ALTER TABLE `finance_soortbedrijf` DISABLE KEYS */;
LOCK TABLES `finance_soortbedrijf` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_soortbedrijf` ENABLE KEYS */;

--
-- Table structure for table `finance_teksten`
--

DROP TABLE IF EXISTS `finance_teksten`;
CREATE TABLE `finance_teksten` (
  `id` int(11) NOT NULL auto_increment,
  `html` mediumtext,
  `description` varchar(255) default NULL,
  `type` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_teksten`
--


/*!40000 ALTER TABLE `finance_teksten` DISABLE KEYS */;
LOCK TABLES `finance_teksten` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `finance_teksten` ENABLE KEYS */;

--
-- Table structure for table `forum`
--

DROP TABLE IF EXISTS `forum`;
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

--
-- Dumping data for table `forum`
--


/*!40000 ALTER TABLE `forum` DISABLE KEYS */;
LOCK TABLES `forum` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `forum` ENABLE KEYS */;

--
-- Table structure for table `functions`
--

DROP TABLE IF EXISTS `functions`;
CREATE TABLE `functions` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `functions`
--


/*!40000 ALTER TABLE `functions` DISABLE KEYS */;
LOCK TABLES `functions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `functions` ENABLE KEYS */;

--
-- Table structure for table `hours_activities`
--

DROP TABLE IF EXISTS `hours_activities`;
CREATE TABLE `hours_activities` (
  `id` int(11) NOT NULL auto_increment,
  `activity` varchar(255) default NULL,
  `tarif` decimal(16,2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hours_activities`
--


/*!40000 ALTER TABLE `hours_activities` DISABLE KEYS */;
LOCK TABLES `hours_activities` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `hours_activities` ENABLE KEYS */;

--
-- Table structure for table `hours_registration`
--

DROP TABLE IF EXISTS `hours_registration`;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hours_registration`
--


/*!40000 ALTER TABLE `hours_registration` DISABLE KEYS */;
LOCK TABLES `hours_registration` WRITE;
INSERT INTO `hours_registration` VALUES (1,1,3,1080824400,1080826200,'1','aanmaken covide',0,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `hours_registration` ENABLE KEYS */;

--
-- Table structure for table `issues`
--

DROP TABLE IF EXISTS `issues`;
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

--
-- Dumping data for table `issues`
--


/*!40000 ALTER TABLE `issues` DISABLE KEYS */;
LOCK TABLES `issues` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `issues` ENABLE KEYS */;

--
-- Table structure for table `license`
--

DROP TABLE IF EXISTS `license`;
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
  `dayquote_nr` int(11) default NULL,
  `mail_shell` smallint(3) default '0',
  `has_voip` smallint(3) default '0',
  `has_sales` smallint(3) default NULL,
  `filesyspath` varchar(255) default NULL,
  `has_arbo` int(11) default NULL,
  `disable_basics` int(11) default NULL,
  `has_privoxy_config` int(11) default NULL,
  `has_hypo` smallint(3) default NULL,
  `has_sync4j` int(11) default NULL,
  `mail_migrated` tinyint(3) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `license`
--


/*!40000 ALTER TABLE `license` DISABLE KEYS */;
LOCK TABLES `license` WRITE;
INSERT INTO `license` VALUES ('covide','covide',1045522860,1,1,1,1,0,1,NULL,1,0,NULL,NULL,NULL,NULL,NULL,'2.4.1',NULL,NULL,0,1,NULL,1,0,NULL,'24M',0,255,3066,0,NULL,1,'',NULL,NULL,NULL,0,NULL,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `license` ENABLE KEYS */;

--
-- Table structure for table `login_log`
--

DROP TABLE IF EXISTS `login_log`;
CREATE TABLE `login_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `ip` varchar(255) default NULL,
  `time` int(11) NOT NULL default '0',
  `day` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login_log`
--


/*!40000 ALTER TABLE `login_log` DISABLE KEYS */;
LOCK TABLES `login_log` WRITE;
INSERT INTO `login_log` VALUES (72,1,'127.0.0.1',1106131628,1106089201),(73,2,'127.0.0.1',1106131744,1106089201),(74,2,'127.0.0.1',1106131961,1106089201);
UNLOCK TABLES;
/*!40000 ALTER TABLE `login_log` ENABLE KEYS */;

--
-- Table structure for table `mail_attachments`
--

DROP TABLE IF EXISTS `mail_attachments`;
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

--
-- Dumping data for table `mail_attachments`
--


/*!40000 ALTER TABLE `mail_attachments` DISABLE KEYS */;
LOCK TABLES `mail_attachments` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `mail_attachments` ENABLE KEYS */;

--
-- Table structure for table `mail_filters`
--

DROP TABLE IF EXISTS `mail_filters`;
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

--
-- Dumping data for table `mail_filters`
--


/*!40000 ALTER TABLE `mail_filters` DISABLE KEYS */;
LOCK TABLES `mail_filters` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `mail_filters` ENABLE KEYS */;

--
-- Table structure for table `mail_folders`
--

DROP TABLE IF EXISTS `mail_folders`;
CREATE TABLE `mail_folders` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `parent_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_folders_user_id` (`user_id`),
  KEY `cvd_mail_folders_parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mail_folders`
--


/*!40000 ALTER TABLE `mail_folders` DISABLE KEYS */;
LOCK TABLES `mail_folders` WRITE;
INSERT INTO `mail_folders` VALUES (4,'Archief',NULL,NULL),(5,'Postvak-IN',1,NULL),(6,'Verzonden-Items',1,NULL),(7,'Verwijderde-Items',1,NULL),(121,'Bounced berichten',1,NULL),(122,'Concepten',1,NULL),(123,'Concepten',2,NULL),(124,'Bounced berichten',2,NULL),(125,'Postvak-IN',2,NULL),(126,'Verzonden-Items',2,NULL),(127,'Verwijderde-Items',2,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `mail_folders` ENABLE KEYS */;

--
-- Table structure for table `mail_messages`
--

DROP TABLE IF EXISTS `mail_messages`;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mail_messages`
--


/*!40000 ALTER TABLE `mail_messages` DISABLE KEYS */;
LOCK TABLES `mail_messages` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `mail_messages` ENABLE KEYS */;

--
-- Table structure for table `mail_messages_data`
--

DROP TABLE IF EXISTS `mail_messages_data`;
CREATE TABLE `mail_messages_data` (
  `mail_id` int(11) NOT NULL default '0',
  `body` longtext NOT NULL,
  `header` mediumtext NOT NULL,
  PRIMARY KEY  (`mail_id`),
  KEY `cvd_mail_messages_data_mail_id` (`mail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mail_messages_data`
--


/*!40000 ALTER TABLE `mail_messages_data` DISABLE KEYS */;
LOCK TABLES `mail_messages_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `mail_messages_data` ENABLE KEYS */;

--
-- Table structure for table `mail_signatures`
--

DROP TABLE IF EXISTS `mail_signatures`;
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

--
-- Dumping data for table `mail_signatures`
--


/*!40000 ALTER TABLE `mail_signatures` DISABLE KEYS */;
LOCK TABLES `mail_signatures` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `mail_signatures` ENABLE KEYS */;

--
-- Table structure for table `mail_templates`
--

DROP TABLE IF EXISTS `mail_templates`;
CREATE TABLE `mail_templates` (
  `id` int(11) NOT NULL auto_increment,
  `header` mediumtext NOT NULL,
  `description` varchar(255) default NULL,
  `width` varchar(255) NOT NULL default '800',
  `repeat` smallint(3) NOT NULL default '1',
  `footer` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mail_templates`
--


/*!40000 ALTER TABLE `mail_templates` DISABLE KEYS */;
LOCK TABLES `mail_templates` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `mail_templates` ENABLE KEYS */;

--
-- Table structure for table `mail_templates_files`
--

DROP TABLE IF EXISTS `mail_templates_files`;
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

--
-- Dumping data for table `mail_templates_files`
--


/*!40000 ALTER TABLE `mail_templates_files` DISABLE KEYS */;
LOCK TABLES `mail_templates_files` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `mail_templates_files` ENABLE KEYS */;

--
-- Table structure for table `mail_tracking`
--

DROP TABLE IF EXISTS `mail_tracking`;
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

--
-- Dumping data for table `mail_tracking`
--


/*!40000 ALTER TABLE `mail_tracking` DISABLE KEYS */;
LOCK TABLES `mail_tracking` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `mail_tracking` ENABLE KEYS */;

--
-- Table structure for table `meta_table`
--

DROP TABLE IF EXISTS `meta_table`;
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

--
-- Dumping data for table `meta_table`
--


/*!40000 ALTER TABLE `meta_table` DISABLE KEYS */;
LOCK TABLES `meta_table` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meta_table` ENABLE KEYS */;

--
-- Table structure for table `morgage`
--

DROP TABLE IF EXISTS `morgage`;
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

--
-- Dumping data for table `morgage`
--


/*!40000 ALTER TABLE `morgage` DISABLE KEYS */;
LOCK TABLES `morgage` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `morgage` ENABLE KEYS */;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
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

--
-- Dumping data for table `notes`
--


/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
LOCK TABLES `notes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;

--
-- Table structure for table `poll_answers`
--

DROP TABLE IF EXISTS `poll_answers`;
CREATE TABLE `poll_answers` (
  `id` int(11) NOT NULL auto_increment,
  `poll_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `answer` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `poll_answers`
--


/*!40000 ALTER TABLE `poll_answers` DISABLE KEYS */;
LOCK TABLES `poll_answers` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `poll_answers` ENABLE KEYS */;

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
CREATE TABLE `polls` (
  `id` int(11) NOT NULL auto_increment,
  `question` mediumtext,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `polls`
--


/*!40000 ALTER TABLE `polls` DISABLE KEYS */;
LOCK TABLES `polls` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `polls` ENABLE KEYS */;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
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
  PRIMARY KEY  (`id`),
  KEY `cvd_project_address_id` (`address_id`),
  KEY `cvd_project_group_id` (`group_id`),
  KEY `cvd_project_is_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `project`
--


/*!40000 ALTER TABLE `project` DISABLE KEYS */;
LOCK TABLES `project` WRITE;
INSERT INTO `project` VALUES (3,'covide',NULL,2,1,1,0,NULL,NULL,NULL,NULL,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `project` ENABLE KEYS */;

--
-- Table structure for table `projects_master`
--

DROP TABLE IF EXISTS `projects_master`;
CREATE TABLE `projects_master` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` mediumtext,
  `manager` int(11) default NULL,
  `is_active` smallint(3) default NULL,
  `status` smallint(3) default '0',
  `address_id` int(11) default NULL,
  `address_businesscard_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `projects_master`
--


/*!40000 ALTER TABLE `projects_master` DISABLE KEYS */;
LOCK TABLES `projects_master` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `projects_master` ENABLE KEYS */;

--
-- Table structure for table `rssfeeds`
--

DROP TABLE IF EXISTS `rssfeeds`;
CREATE TABLE `rssfeeds` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `homepage` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `count` int(11) default '5',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rssfeeds`
--


/*!40000 ALTER TABLE `rssfeeds` DISABLE KEYS */;
LOCK TABLES `rssfeeds` WRITE;
INSERT INTO `rssfeeds` VALUES (1,'Covide','http://www.covide.nl','http://www.covide.nl/rss.php',0,5);
UNLOCK TABLES;
/*!40000 ALTER TABLE `rssfeeds` ENABLE KEYS */;

--
-- Table structure for table `rssitems`
--

DROP TABLE IF EXISTS `rssitems`;
CREATE TABLE `rssitems` (
  `id` int(11) NOT NULL auto_increment,
  `feed` int(11) default NULL,
  `subject` varchar(255) default NULL,
  `body` mediumtext,
  `link` varchar(255) default NULL,
  `date` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rssitems`
--


/*!40000 ALTER TABLE `rssitems` DISABLE KEYS */;
LOCK TABLES `rssitems` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `rssitems` ENABLE KEYS */;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
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

--
-- Dumping data for table `sales`
--


/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
LOCK TABLES `sales` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;

--
-- Table structure for table `snack_items`
--

DROP TABLE IF EXISTS `snack_items`;
CREATE TABLE `snack_items` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `snack_items`
--


/*!40000 ALTER TABLE `snack_items` DISABLE KEYS */;
LOCK TABLES `snack_items` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `snack_items` ENABLE KEYS */;

--
-- Table structure for table `snack_order`
--

DROP TABLE IF EXISTS `snack_order`;
CREATE TABLE `snack_order` (
  `id` int(11) NOT NULL auto_increment,
  `ammount` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `snack_order`
--


/*!40000 ALTER TABLE `snack_order` DISABLE KEYS */;
LOCK TABLES `snack_order` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `snack_order` ENABLE KEYS */;

--
-- Table structure for table `statistics`
--

DROP TABLE IF EXISTS `statistics`;
CREATE TABLE `statistics` (
  `table` varchar(255) default NULL,
  `updates` int(11) default NULL,
  `vacuum` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `statistics`
--


/*!40000 ALTER TABLE `statistics` DISABLE KEYS */;
LOCK TABLES `statistics` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `statistics` ENABLE KEYS */;

--
-- Table structure for table `status_conn`
--

DROP TABLE IF EXISTS `status_conn`;
CREATE TABLE `status_conn` (
  `user_id` int(11) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status_conn`
--


/*!40000 ALTER TABLE `status_conn` DISABLE KEYS */;
LOCK TABLES `status_conn` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `status_conn` ENABLE KEYS */;

--
-- Table structure for table `status_list`
--

DROP TABLE IF EXISTS `status_list`;
CREATE TABLE `status_list` (
  `id` int(11) NOT NULL auto_increment,
  `msg_id` varchar(255) NOT NULL default '0',
  `mail_id` int(11) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `mark_delete` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status_list`
--


/*!40000 ALTER TABLE `status_list` DISABLE KEYS */;
LOCK TABLES `status_list` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `status_list` ENABLE KEYS */;

--
-- Table structure for table `support`
--

DROP TABLE IF EXISTS `support`;
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

--
-- Dumping data for table `support`
--


/*!40000 ALTER TABLE `support` DISABLE KEYS */;
LOCK TABLES `support` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `support` ENABLE KEYS */;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
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

--
-- Dumping data for table `templates`
--


/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
LOCK TABLES `templates` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;

--
-- Table structure for table `templates_settings`
--

DROP TABLE IF EXISTS `templates_settings`;
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
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `templates_settings`
--


/*!40000 ALTER TABLE `templates_settings` DISABLE KEYS */;
LOCK TABLES `templates_settings` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `templates_settings` ENABLE KEYS */;

--
-- Table structure for table `todo`
--

DROP TABLE IF EXISTS `todo`;
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

--
-- Dumping data for table `todo`
--


/*!40000 ALTER TABLE `todo` DISABLE KEYS */;
LOCK TABLES `todo` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `todo` ENABLE KEYS */;

--
-- Table structure for table `todo_sync`
--

DROP TABLE IF EXISTS `todo_sync`;
CREATE TABLE `todo_sync` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `sync_guid` int(11) default NULL,
  `action` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `todo_sync`
--


/*!40000 ALTER TABLE `todo_sync` DISABLE KEYS */;
LOCK TABLES `todo_sync` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `todo_sync` ENABLE KEYS */;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `members` varchar(255) default NULL,
  `manager` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_groups`
--


/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
LOCK TABLES `user_groups` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;

--
-- Table structure for table `userchangelog`
--

DROP TABLE IF EXISTS `userchangelog`;
CREATE TABLE `userchangelog` (
  `id` int(11) NOT NULL auto_increment,
  `manager` int(11) default NULL,
  `user_id` int(11) default NULL,
  `timestamp` int(11) default NULL,
  `change` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userchangelog`
--


/*!40000 ALTER TABLE `userchangelog` DISABLE KEYS */;
LOCK TABLES `userchangelog` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `userchangelog` ENABLE KEYS */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
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
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--


/*!40000 ALTER TABLE `users` DISABLE KEYS */;
LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES (3,'archiefgebruiker','3c604b2e43ca806536d70f781a13a65e',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,2,NULL,NULL,0,1,0,1,0,0,'NL',0,0,NULL,0,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL),(1,'administrator','3c604b2e43ca806536d70f781a13a65e',NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,21,2,NULL,NULL,0,1,0,1,0,0,'NL',0,0,NULL,0,NULL,1,0,1,NULL,NULL,1,1,'',NULL,0,NULL,'',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL),(2,'covide','1a529f44b9575060c370478b01e6674c',1,1,1,1,1,3,1,1,1,NULL,0,1,1,NULL,1,0,'','test@covide.nl','','test@covide.nl','',0,'',1,2674801,19,2,'','',1,1,0,601201,1,1,'NL',0,0,0,0,1,1,0,1,1,0,1,1,'',0,0,NULL,'',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

