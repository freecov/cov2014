-- MySQL dump 10.11
--
-- Host: localhost    Database: covide_cleaninstall
-- ------------------------------------------------------
-- Server version	5.0.67-0ubuntu6

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

--
-- Table structure for table `active_calls`
--

DROP TABLE IF EXISTS `active_calls`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `active_calls` (
  `name` varchar(255) default NULL,
  `address_id` int(11) default NULL,
  `timestamp` int(11) default NULL,
  `user_id` int(11) NOT NULL,
  `invite` tinyint(3) NOT NULL,
  `user_id_src` int(11) default NULL,
  `alert_done` tinyint(3) default NULL,
  `ident` varchar(255) default NULL,
  KEY `ident_index` (`ident`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `active_calls`
--

LOCK TABLES `active_calls` WRITE;
/*!40000 ALTER TABLE `active_calls` DISABLE KEYS */;
/*!40000 ALTER TABLE `active_calls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `debtor_nr` varchar(50) default NULL,
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
  `address2` varchar(255) default NULL,
  `contact_birthday` int(11) default NULL,
  `state` varchar(255) default NULL,
  `suffix` int(11) NOT NULL,
  `pobox_state` varchar(255) NOT NULL,
  `pobox_country` varchar(255) NOT NULL,
  `is_person` tinyint(3) default NULL,
  `jobtitle` varchar(255) NOT NULL,
  `modified_by` int(11) default NULL,
  `bankaccount` varchar(255) NOT NULL,
  `giro` varchar(255) default NULL,
  `bsn` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `address_classification` (`classification`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
INSERT INTO `address` VALUES (8,'','','Terrazur','Bouwheerstraat 1b','3772 AL','Barneveld','0342-490364','0342-423577','info@terrazur.nl',3,1,NULL,1,'','14144',NULL,NULL,NULL,'http://www.terrazur.nl',0,'Dhr. W.  Massier','Beste Willem',1,1,NULL,NULL,NULL,NULL,'','','',NULL,0,1,1,1,'W.','Willem','','Massier','',0,NULL,NULL,0,NULL,NULL,NULL,0,'','',NULL,'',NULL,'',NULL,NULL);
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_birthdays`
--

DROP TABLE IF EXISTS `address_birthdays`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `address_birthdays` (
  `id` int(11) NOT NULL auto_increment,
  `bcard_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `address_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `bcard_id` (`bcard_id`),
  KEY `address_birthdays_address_id` (`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_birthdays`
--

LOCK TABLES `address_birthdays` WRITE;
/*!40000 ALTER TABLE `address_birthdays` DISABLE KEYS */;
/*!40000 ALTER TABLE `address_birthdays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_businesscards`
--

DROP TABLE IF EXISTS `address_businesscards`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `businessunit` varchar(255) default NULL,
  `department` varchar(255) default NULL,
  `locationcode` varchar(255) default NULL,
  `multirel` varchar(255) default NULL,
  `business_state` varchar(255) default NULL,
  `personal_state` varchar(255) default NULL,
  `suffix` int(11) NOT NULL,
  `jobtitle` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `business_phone_nr_2` varchar(255) NOT NULL,
  `business_country` varchar(255) NOT NULL,
  `business_car_phone` varchar(255) NOT NULL,
  `personal_phone_nr_2` varchar(255) NOT NULL,
  `personal_country` varchar(255) NOT NULL,
  `other_address` varchar(255) NOT NULL,
  `other_zipcode` varchar(255) NOT NULL,
  `other_city` varchar(255) NOT NULL,
  `other_state` varchar(255) NOT NULL,
  `other_phone_nr` varchar(255) NOT NULL,
  `other_phone_nr_2` varchar(255) NOT NULL,
  `other_fax_nr` varchar(255) NOT NULL,
  `other_mobile_nr` varchar(255) NOT NULL,
  `other_email` varchar(255) NOT NULL,
  `pobox` varchar(255) NOT NULL,
  `pobox_country` varchar(255) NOT NULL,
  `pobox_state` varchar(255) NOT NULL,
  `pobox_zipcode` varchar(255) NOT NULL,
  `pobox_city` varchar(255) NOT NULL,
  `other_country` varchar(255) NOT NULL,
  `opt_assistant_name` varchar(255) NOT NULL,
  `opt_assistant_phone_nr` varchar(255) NOT NULL,
  `opt_callback_phone_nr` varchar(255) NOT NULL,
  `opt_company_phone_nr` varchar(255) NOT NULL,
  `opt_company_name` varchar(255) NOT NULL,
  `opt_manager_name` varchar(255) NOT NULL,
  `opt_pager_number` varchar(255) NOT NULL,
  `opt_profession` varchar(255) NOT NULL,
  `opt_radio_phone_nr` varchar(255) NOT NULL,
  `opt_telex_number` varchar(255) NOT NULL,
  `rcbc` tinyint(2) default '0',
  `ssn` varchar(255) default NULL,
  `modified_by` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_address_businesscards_address_id` (`address_id`),
  KEY `addressbcards_classification` (`classification`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_businesscards`
--

LOCK TABLES `address_businesscards` WRITE;
/*!40000 ALTER TABLE `address_businesscards` DISABLE KEYS */;
INSERT INTO `address_businesscards` VALUES (1,8,'Willem','W.','','Massier',0,NULL,NULL,NULL,'',1,'',1,'Bouwheerstraat 1b','3772 AL','Barneveld','','0342-490364','info@terrazur.nl','0342-423577','','','','','','','',NULL,0,'',1226575591,NULL,'','','','','','',0,'','http://www.terrazur.nl','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,'',2);
/*!40000 ALTER TABLE `address_businesscards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_businesscards_info`
--

DROP TABLE IF EXISTS `address_businesscards_info`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `address_businesscards_info` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) NOT NULL,
  `bcard_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `address_id` (`address_id`),
  KEY `bcard_id` (`bcard_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_businesscards_info`
--

LOCK TABLES `address_businesscards_info` WRITE;
/*!40000 ALTER TABLE `address_businesscards_info` DISABLE KEYS */;
INSERT INTO `address_businesscards_info` VALUES (1,8,1);
/*!40000 ALTER TABLE `address_businesscards_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_classifications`
--

DROP TABLE IF EXISTS `address_classifications`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `address_classifications` (
  `id` int(11) NOT NULL auto_increment,
  `description` varchar(255) default NULL,
  `is_active` smallint(3) default '0',
  `is_locked` smallint(3) default NULL,
  `subtype` smallint(3) default NULL,
  `access` varchar(255) NOT NULL,
  `access_read` varchar(255) NOT NULL,
  `description_full` varchar(255) default NULL,
  `is_cms` smallint(2) default NULL,
  `group_id` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_classifications`
--

LOCK TABLES `address_classifications` WRITE;
/*!40000 ALTER TABLE `address_classifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `address_classifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_commencement`
--

DROP TABLE IF EXISTS `address_commencement`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `address_commencement` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_commencement`
--

LOCK TABLES `address_commencement` WRITE;
/*!40000 ALTER TABLE `address_commencement` DISABLE KEYS */;
INSERT INTO `address_commencement` VALUES (1,'Dhr.'),(2,'Mevr.'),(3,'---'),(4,'Mr.'),(5,'Mrs.'),(6,'Ms.');
/*!40000 ALTER TABLE `address_commencement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_info`
--

DROP TABLE IF EXISTS `address_info`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_info`
--

LOCK TABLES `address_info` WRITE;
/*!40000 ALTER TABLE `address_info` DISABLE KEYS */;
INSERT INTO `address_info` VALUES (8,8,'','','',NULL,NULL,NULL);
/*!40000 ALTER TABLE `address_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_letterhead`
--

DROP TABLE IF EXISTS `address_letterhead`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `address_letterhead` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_letterhead`
--

LOCK TABLES `address_letterhead` WRITE;
/*!40000 ALTER TABLE `address_letterhead` DISABLE KEYS */;
INSERT INTO `address_letterhead` VALUES (1,'Beste'),(2,'Geachte'),(3,'Dear');
/*!40000 ALTER TABLE `address_letterhead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_multivers`
--

DROP TABLE IF EXISTS `address_multivers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_multivers`
--

LOCK TABLES `address_multivers` WRITE;
/*!40000 ALTER TABLE `address_multivers` DISABLE KEYS */;
/*!40000 ALTER TABLE `address_multivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_other`
--

DROP TABLE IF EXISTS `address_other`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `address2` varchar(255) default NULL,
  `infix` varchar(255) default NULL,
  `state` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_other`
--

LOCK TABLES `address_other` WRITE;
/*!40000 ALTER TABLE `address_other` DISABLE KEYS */;
/*!40000 ALTER TABLE `address_other` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_private`
--

DROP TABLE IF EXISTS `address_private`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `address2` varchar(255) default NULL,
  `infix` varchar(255) default NULL,
  `tav` varchar(255) default NULL,
  `contact_person` varchar(255) default NULL,
  `contact_letterhead` smallint(3) default NULL,
  `contact_commencement` smallint(3) default NULL,
  `contact_initials` varchar(255) default NULL,
  `title` int(11) default NULL,
  `sync_added` int(11) NOT NULL,
  `state` varchar(255) default NULL,
  `jobtitle` varchar(255) NOT NULL,
  `locationcode` varchar(255) NOT NULL,
  `businessunit` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `business_address` varchar(255) NOT NULL,
  `business_phone_nr` varchar(255) NOT NULL,
  `business_city` varchar(255) NOT NULL,
  `business_phone_nr_2` varchar(255) NOT NULL,
  `business_state` varchar(255) NOT NULL,
  `business_fax_nr` varchar(255) NOT NULL,
  `business_zipcode` varchar(255) NOT NULL,
  `business_mobile_nr` varchar(255) NOT NULL,
  `business_country` varchar(255) NOT NULL,
  `business_car_phone` varchar(255) NOT NULL,
  `business_email` varchar(255) NOT NULL,
  `phone_nr_2` varchar(255) NOT NULL,
  `other_address` varchar(255) NOT NULL,
  `other_phone_nr` varchar(255) NOT NULL,
  `other_city` varchar(255) NOT NULL,
  `other_phone_nr_2` varchar(255) NOT NULL,
  `other_state` varchar(255) NOT NULL,
  `other_fax_nr` varchar(255) NOT NULL,
  `other_zipcode` varchar(255) NOT NULL,
  `other_mobile_nr` varchar(255) NOT NULL,
  `other_country` varchar(255) NOT NULL,
  `alternative_name` varchar(255) NOT NULL,
  `timestamp_birthday` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `pobox_state` varchar(255) NOT NULL,
  `pobox_country` varchar(255) NOT NULL,
  `other_email` varchar(255) NOT NULL,
  `opt_assistant_name` varchar(255) NOT NULL,
  `opt_assistant_phone_nr` varchar(255) NOT NULL,
  `opt_callback_phone_nr` varchar(255) NOT NULL,
  `opt_company_phone_nr` varchar(255) NOT NULL,
  `opt_company_name` varchar(255) NOT NULL,
  `opt_manager_name` varchar(255) NOT NULL,
  `opt_pager_number` varchar(255) NOT NULL,
  `opt_profession` varchar(255) NOT NULL,
  `opt_radio_phone_nr` varchar(255) NOT NULL,
  `opt_telex_number` varchar(255) NOT NULL,
  `modified_by` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_private`
--

LOCK TABLES `address_private` WRITE;
/*!40000 ALTER TABLE `address_private` DISABLE KEYS */;
INSERT INTO `address_private` VALUES (3,'BV','Covide','Covideweg 1','1111 CC','covide','0111-111111','','test@covide.nl',2,0,1,'','','','','','',1,'','Nederland',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',NULL);
/*!40000 ALTER TABLE `address_private` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_selections`
--

DROP TABLE IF EXISTS `address_selections`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `address_selections` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_selections`
--

LOCK TABLES `address_selections` WRITE;
/*!40000 ALTER TABLE `address_selections` DISABLE KEYS */;
INSERT INTO `address_selections` VALUES (1,2,1226575591,'a:15:{s:11:\"addresstype\";s:9:\"relations\";s:12:\"bcard_export\";b:1;s:3:\"top\";i:0;s:6:\"action\";N;s:1:\"l\";N;s:3:\"sub\";N;s:6:\"and_or\";N;s:6:\"search\";N;s:9:\"specified\";N;s:10:\"landSelect\";N;s:15:\"classifications\";N;s:13:\"selectiontype\";N;s:4:\"sort\";N;s:13:\"funambol_user\";N;s:8:\"cmsforms\";N;}'),(2,2,1226575596,'a:15:{s:11:\"addresstype\";s:9:\"relations\";s:12:\"bcard_export\";b:1;s:3:\"top\";i:0;s:6:\"action\";N;s:1:\"l\";N;s:3:\"sub\";N;s:6:\"and_or\";N;s:6:\"search\";N;s:9:\"specified\";N;s:10:\"landSelect\";N;s:15:\"classifications\";N;s:13:\"selectiontype\";N;s:4:\"sort\";N;s:13:\"funambol_user\";N;s:8:\"cmsforms\";N;}');
/*!40000 ALTER TABLE `address_selections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_suffix`
--

DROP TABLE IF EXISTS `address_suffix`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `address_suffix` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_suffix`
--

LOCK TABLES `address_suffix` WRITE;
/*!40000 ALTER TABLE `address_suffix` DISABLE KEYS */;
INSERT INTO `address_suffix` VALUES (1,'I'),(2,'II'),(3,'III'),(4,'Jr.'),(5,'Sr.');
/*!40000 ALTER TABLE `address_suffix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_titles`
--

DROP TABLE IF EXISTS `address_titles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `address_titles` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `address_titles`
--

LOCK TABLES `address_titles` WRITE;
/*!40000 ALTER TABLE `address_titles` DISABLE KEYS */;
INSERT INTO `address_titles` VALUES (1,'Dr.'),(2,'Drs.'),(3,'Ing.'),(4,'Ir.'),(5,'Mr.'),(6,'Prof.'),(7,'Prof. Dr.'),(8,'BSc.'),(9,'MSc.'),(10,'Drs. Ing.');
/*!40000 ALTER TABLE `address_titles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(255) default NULL,
  `body` mediumtext,
  `is_popup` smallint(3) default NULL,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arbo_arbo`
--

DROP TABLE IF EXISTS `arbo_arbo`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `arbo_arbo`
--

LOCK TABLES `arbo_arbo` WRITE;
/*!40000 ALTER TABLE `arbo_arbo` DISABLE KEYS */;
/*!40000 ALTER TABLE `arbo_arbo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arbo_verslag`
--

DROP TABLE IF EXISTS `arbo_verslag`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `arbo_verslag`
--

LOCK TABLES `arbo_verslag` WRITE;
/*!40000 ALTER TABLE `arbo_verslag` DISABLE KEYS */;
/*!40000 ALTER TABLE `arbo_verslag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arbo_ziek`
--

DROP TABLE IF EXISTS `arbo_ziek`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `arbo_ziek`
--

LOCK TABLES `arbo_ziek` WRITE;
/*!40000 ALTER TABLE `arbo_ziek` DISABLE KEYS */;
/*!40000 ALTER TABLE `arbo_ziek` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bugs`
--

DROP TABLE IF EXISTS `bugs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `bugs` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `module` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `description` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `bugs`
--

LOCK TABLES `bugs` WRITE;
/*!40000 ALTER TABLE `bugs` DISABLE KEYS */;
/*!40000 ALTER TABLE `bugs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calendar` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT 'unique id for an appointment.',
  `timestamp_start` int(11) unsigned NOT NULL COMMENT 'UNIX timestamp of start date and time for the appointment',
  `timestamp_end` int(11) unsigned NOT NULL COMMENT 'UNIX timestamp of end date and time for the appointment',
  `alldayevent` tinyint(2) NOT NULL COMMENT '1 if this is an all-day event',
  `subject` varchar(255) NOT NULL COMMENT 'Short description for the appointment',
  `body` text NOT NULL COMMENT 'Extended description for the appointment. Be sure to remove HTML tags when sending this to funambol.',
  `location` varchar(255) NOT NULL COMMENT 'The location for the appointment',
  `kilometers` int(11) unsigned default NULL COMMENT 'Distance to the location of the appointment',
  `reminderset` tinyint(2) NOT NULL COMMENT '1 if a reminder should be sent to the user',
  `reminderminutesbeforestart` int(11) unsigned NOT NULL COMMENT 'Number of minutes before the start of the appointment a reminder should be sent',
  `busystatus` tinyint(4) NOT NULL COMMENT '0 for free, 1 for tentative, 2 for busy, 3 for outofoffice',
  `importance` tinyint(4) NOT NULL COMMENT '0 for low, 1 for normal, 2 for high',
  `address_id` int(11) unsigned NOT NULL COMMENT 'main contact id from address table for this appointment',
  `multirel` varchar(255) NOT NULL COMMENT 'pipe seperated list of additional contacts for this appointment',
  `project_id` int(11) unsigned default NULL COMMENT 'main project id from project table for this appointment',
  `private_id` int(11) NOT NULL default '0',
  `is_private` tinyint(2) NOT NULL COMMENT '1 if this is a private appointment that other users are not allowed to view/alter',
  `isrecurring` tinyint(2) NOT NULL default '0' COMMENT 'true if the appointment is a recurring appointment',
  `modified_by` int(11) unsigned NOT NULL COMMENT 'user id of the user that last modified this appointment',
  `modified` int(11) unsigned NOT NULL COMMENT 'UNIX timestamp when this appointment was last modified',
  `is_ill` tinyint(2) NOT NULL,
  `is_specialleave` tinyint(2) NOT NULL,
  `is_holiday` tinyint(2) NOT NULL,
  `is_dnd` tinyint(2) NOT NULL COMMENT 'With the voip module this will mean the phone wont ring',
  `multiprivate` varchar(255) NOT NULL,
  `deckm` smallint(3) default NULL,
  `note_id` int(11) default NULL,
  `external_id` int(11) default NULL,
  `dimdim_meeting` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `address_id` (`address_id`),
  KEY `timestamp_start` (`timestamp_start`),
  KEY `timestamp_end` (`timestamp_end`),
  KEY `project_id` (`project_id`),
  KEY `isrecurring` (`isrecurring`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `calendar`
--

LOCK TABLES `calendar` WRITE;
/*!40000 ALTER TABLE `calendar` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_exceptions`
--

DROP TABLE IF EXISTS `calendar_exceptions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calendar_exceptions` (
  `calendar_id` int(11) unsigned NOT NULL COMMENT 'id from calendar table',
  `user_id` int(11) unsigned NOT NULL COMMENT 'id from userstable',
  `timestamp_exception` int(11) unsigned NOT NULL COMMENT 'UNIX TIMESTAMP of exception date'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `calendar_exceptions`
--

LOCK TABLES `calendar_exceptions` WRITE;
/*!40000 ALTER TABLE `calendar_exceptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_exceptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_external`
--

DROP TABLE IF EXISTS `calendar_external`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calendar_external` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `calendar_external`
--

LOCK TABLES `calendar_external` WRITE;
/*!40000 ALTER TABLE `calendar_external` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_external` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_notifications`
--

DROP TABLE IF EXISTS `calendar_notifications`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calendar_notifications` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `template` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `calendar_notifications`
--

LOCK TABLES `calendar_notifications` WRITE;
/*!40000 ALTER TABLE `calendar_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_old`
--

DROP TABLE IF EXISTS `calendar_old`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calendar_old` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp_start` int(11) default NULL,
  `timestamp_end` int(11) default NULL,
  `description` mediumtext,
  `user_id` int(11) default NULL,
  `address_id` int(11) default NULL,
  `project_id` int(11) default NULL,
  `private_id` int(11) NOT NULL default '0',
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
  `subject` varchar(255) default NULL,
  `extra_users` varchar(255) default NULL,
  `is_event` tinyint(4) default '0',
  `multiprivate` varchar(255) NOT NULL,
  `external_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_calendar_user_id` (`user_id`),
  KEY `cvd_calendar_user_timestamp_start` (`timestamp_start`),
  KEY `cvd_calendar_user_timestamp_end` (`timestamp_end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `calendar_old`
--

LOCK TABLES `calendar_old` WRITE;
/*!40000 ALTER TABLE `calendar_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_permissions`
--

DROP TABLE IF EXISTS `calendar_permissions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calendar_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `user_id_visitor` int(11) default NULL,
  `permissions` varchar(255) default 'RO',
  PRIMARY KEY  (`id`),
  KEY `cvd_calendar_permissions_user_id` (`user_id`),
  KEY `cvd_calendar_permissions_user_id_visitor` (`user_id_visitor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `calendar_permissions`
--

LOCK TABLES `calendar_permissions` WRITE;
/*!40000 ALTER TABLE `calendar_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_repeats`
--

DROP TABLE IF EXISTS `calendar_repeats`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calendar_repeats` (
  `calendar_id` int(11) unsigned NOT NULL,
  `repeat_type` int(3) unsigned default NULL,
  `timestamp_end` int(11) default NULL,
  `repeat_frequency` int(11) unsigned default NULL,
  `repeat_days` char(7) default NULL,
  KEY `calendar_id` (`calendar_id`),
  KEY `repeat_type` (`repeat_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `calendar_repeats`
--

LOCK TABLES `calendar_repeats` WRITE;
/*!40000 ALTER TABLE `calendar_repeats` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_repeats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_user`
--

DROP TABLE IF EXISTS `calendar_user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calendar_user` (
  `calendar_id` int(11) unsigned NOT NULL COMMENT 'appointment id',
  `user_id` int(11) unsigned NOT NULL COMMENT 'user id as found in the users table',
  `status` int(11) NOT NULL COMMENT 'status of evenvt for this user: 1 for accepted, 2 for rejected, 3 for waiting',
  KEY `user_id` (`user_id`),
  KEY `calendar_id` (`calendar_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `calendar_user`
--

LOCK TABLES `calendar_user` WRITE;
/*!40000 ALTER TABLE `calendar_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign`
--

DROP TABLE IF EXISTS `campaign`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campaign` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `classifications` text NOT NULL,
  `datetime` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `tracker_id` int(11) NOT NULL,
  `is_active` tinyint(2) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `campaign`
--

LOCK TABLES `campaign` WRITE;
/*!40000 ALTER TABLE `campaign` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_records`
--

DROP TABLE IF EXISTS `campaign_records`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campaign_records` (
  `id` int(11) NOT NULL auto_increment,
  `campaign_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `businesscard_id` int(11) NOT NULL,
  `is_called` int(11) NOT NULL,
  `answer` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `note_id` int(11) NOT NULL,
  `email_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `call_again` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `campaign_records`
--

LOCK TABLES `campaign_records` WRITE;
/*!40000 ALTER TABLE `campaign_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cdr`
--

DROP TABLE IF EXISTS `cdr`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cdr`
--

LOCK TABLES `cdr` WRITE;
/*!40000 ALTER TABLE `cdr` DISABLE KEYS */;
/*!40000 ALTER TABLE `cdr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_rooms`
--

DROP TABLE IF EXISTS `chat_rooms`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `chat_rooms` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `users` mediumtext,
  `topic` varchar(255) default NULL,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `chat_rooms`
--

LOCK TABLES `chat_rooms` WRITE;
/*!40000 ALTER TABLE `chat_rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_text`
--

DROP TABLE IF EXISTS `chat_text`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `chat_text` (
  `id` int(11) NOT NULL auto_increment,
  `room` int(11) default NULL,
  `user` int(11) default NULL,
  `text` varchar(255) default NULL,
  `timestamp` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `chat_text`
--

LOCK TABLES `chat_text` WRITE;
/*!40000 ALTER TABLE `chat_text` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_text` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classification_groups`
--

DROP TABLE IF EXISTS `classification_groups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `classification_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `classification_groups`
--

LOCK TABLES `classification_groups` WRITE;
/*!40000 ALTER TABLE `classification_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `classification_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_abbreviations`
--

DROP TABLE IF EXISTS `cms_abbreviations`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_abbreviations` (
  `id` int(11) NOT NULL auto_increment,
  `abbreviation` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `lang` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_abbreviations`
--

LOCK TABLES `cms_abbreviations` WRITE;
/*!40000 ALTER TABLE `cms_abbreviations` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_abbreviations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_alias_history`
--

DROP TABLE IF EXISTS `cms_alias_history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_alias_history` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_alias_history`
--

LOCK TABLES `cms_alias_history` WRITE;
/*!40000 ALTER TABLE `cms_alias_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_alias_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_banner_views`
--

DROP TABLE IF EXISTS `cms_banner_views`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_banner_views` (
  `id` int(11) NOT NULL auto_increment,
  `banner_id` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `visited` int(11) NOT NULL,
  `clicked` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_banner_views`
--

LOCK TABLES `cms_banner_views` WRITE;
/*!40000 ALTER TABLE `cms_banner_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_banner_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_banners`
--

DROP TABLE IF EXISTS `cms_banners`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_banners` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `image` text,
  `rating` int(11) default NULL,
  `url` text,
  `internal_stat` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_banners`
--

LOCK TABLES `cms_banners` WRITE;
/*!40000 ALTER TABLE `cms_banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_banners_log`
--

DROP TABLE IF EXISTS `cms_banners_log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_banners_log`
--

LOCK TABLES `cms_banners_log` WRITE;
/*!40000 ALTER TABLE `cms_banners_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_banners_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_banners_summary`
--

DROP TABLE IF EXISTS `cms_banners_summary`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_banners_summary` (
  `id` int(11) NOT NULL auto_increment,
  `bannerid` int(11) NOT NULL default '0',
  `datum` int(11) NOT NULL default '0',
  `bezoekers` int(11) NOT NULL default '0',
  `uniek` int(11) NOT NULL default '0',
  `kliks` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `bannerid` (`bannerid`),
  KEY `datum` (`datum`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_banners_summary`
--

LOCK TABLES `cms_banners_summary` WRITE;
/*!40000 ALTER TABLE `cms_banners_summary` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_banners_summary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_cache`
--

DROP TABLE IF EXISTS `cms_cache`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_cache` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) NOT NULL,
  `ident` varchar(255) NOT NULL,
  `data` mediumblob NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `ident` (`ident`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_cache`
--

LOCK TABLES `cms_cache` WRITE;
/*!40000 ALTER TABLE `cms_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_counters`
--

DROP TABLE IF EXISTS `cms_counters`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_counters` (
  `id` int(11) NOT NULL auto_increment,
  `counter1` int(11) default NULL,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_counters`
--

LOCK TABLES `cms_counters` WRITE;
/*!40000 ALTER TABLE `cms_counters` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_counters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_data`
--

DROP TABLE IF EXISTS `cms_data`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `apEnabled` int(11) default '0',
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
  `address_ids` varchar(255) default NULL,
  `address_level` tinyint(3) default NULL,
  `isProtected` int(11) NOT NULL,
  `useSSL` tinyint(3) NOT NULL,
  `useInternal` tinyint(3) NOT NULL,
  `isFeedback` tinyint(3) default NULL,
  `pageHeader` text NOT NULL,
  `autosave_header` text NOT NULL,
  `isShop` tinyint(3) NOT NULL,
  `shopPrice` float(16,2) NOT NULL,
  `isSource` tinyint(3) default NULL,
  PRIMARY KEY  (`id`),
  KEY `parentPage` (`parentPage`),
  KEY `pageTitle` (`pageTitle`),
  KEY `pageLabel` (`pageLabel`),
  KEY `pageData` (`pageData`(255)),
  KEY `search_fields` (`search_fields`),
  KEY `search_descr` (`search_descr`),
  KEY `search_title` (`search_title`),
  KEY `pageAlias` (`pageAlias`),
  FULLTEXT KEY `ftdata` (`pageData`,`pageTitle`,`pageLabel`,`pageAlias`,`search_title`,`search_fields`,`search_descr`,`keywords`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_data`
--

LOCK TABLES `cms_data` WRITE;
/*!40000 ALTER TABLE `cms_data` DISABLE KEYS */;
INSERT INTO `cms_data` VALUES (1,0,'site root','',0,'','',0,0,0,'',1,0,0,0,0,NULL,NULL,0,0,0,0,'',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'R',NULL,'monthly','0.5','','',NULL,NULL,0,0,0,NULL,'','',0,0.00,NULL),(2,0,'deleted items','',0,NULL,'',1,0,1,'',0,0,0,0,0,NULL,NULL,0,0,0,0,'0',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'D',NULL,'monthly','0.5','','',NULL,NULL,0,0,0,NULL,'','',0,0.00,NULL),(3,0,'protected items','',0,NULL,'',1,1,1,'',0,0,0,0,0,NULL,NULL,0,0,0,0,'0',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'X',NULL,'monthly','0.5','','',NULL,NULL,0,0,0,NULL,'','',0,0.00,NULL),(4,1,'Home','0001',1204664400,'<p>Welcome to your new home in Cyberspace.</p><p>If you can see this, your CMS is correctly installed.<br />Login to covide and select the most left button in the top menubar to access the CMS backend.</p>',NULL,1,1,0,NULL,1,0,0,0,0,NULL,NULL,0,0,0,1204664732,'0',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'home',NULL,NULL,'monthly','0.5','','',NULL,NULL,0,0,0,NULL,'','',0,0.00,NULL);
/*!40000 ALTER TABLE `cms_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_date`
--

DROP TABLE IF EXISTS `cms_date`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_date` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `date_begin` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `date_end` int(11) default NULL,
  `repeating` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `unique` (`pageid`,`date_begin`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_date`
--

LOCK TABLES `cms_date` WRITE;
/*!40000 ALTER TABLE `cms_date` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_date_index`
--

DROP TABLE IF EXISTS `cms_date_index`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_date_index` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `dateid` int(11) NOT NULL default '0',
  `datetime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `unique` (`pageid`,`datetime`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_date_index`
--

LOCK TABLES `cms_date_index` WRITE;
/*!40000 ALTER TABLE `cms_date_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_date_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_feedback`
--

DROP TABLE IF EXISTS `cms_feedback`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_feedback` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `is_visitor` tinyint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_feedback`
--

LOCK TABLES `cms_feedback` WRITE;
/*!40000 ALTER TABLE `cms_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_files`
--

DROP TABLE IF EXISTS `cms_files`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_files` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `type` varchar(255) default 'application/octet-stream',
  `size` varchar(255) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_files`
--

LOCK TABLES `cms_files` WRITE;
/*!40000 ALTER TABLE `cms_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_form_results`
--

DROP TABLE IF EXISTS `cms_form_results`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_form_results` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `user_value` varchar(255) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_form_results`
--

LOCK TABLES `cms_form_results` WRITE;
/*!40000 ALTER TABLE `cms_form_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_form_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_form_results_visitors`
--

DROP TABLE IF EXISTS `cms_form_results_visitors`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_form_results_visitors` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `visitor_hash` varchar(255) NOT NULL,
  `datetime_start` int(11) NOT NULL,
  `datetime_end` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_form_results_visitors`
--

LOCK TABLES `cms_form_results_visitors` WRITE;
/*!40000 ALTER TABLE `cms_form_results_visitors` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_form_results_visitors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_form_settings`
--

DROP TABLE IF EXISTS `cms_form_settings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_form_settings` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `mode` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_form_settings`
--

LOCK TABLES `cms_form_settings` WRITE;
/*!40000 ALTER TABLE `cms_form_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_form_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_formsettings`
--

DROP TABLE IF EXISTS `cms_formsettings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_formsettings` (
  `form_id` int(11) default NULL,
  `settingsname` varchar(255) default NULL,
  `settingsvalue` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_formsettings`
--

LOCK TABLES `cms_formsettings` WRITE;
/*!40000 ALTER TABLE `cms_formsettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_formsettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_formulieren`
--

DROP TABLE IF EXISTS `cms_formulieren`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_formulieren`
--

LOCK TABLES `cms_formulieren` WRITE;
/*!40000 ALTER TABLE `cms_formulieren` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_formulieren` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_gallery`
--

DROP TABLE IF EXISTS `cms_gallery`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_gallery` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `gallerytype` smallint(3) NOT NULL default '0',
  `cols` int(11) NOT NULL default '0',
  `rows` int(11) NOT NULL default '0',
  `thumbsize` int(11) NOT NULL default '0',
  `bigsize` int(11) NOT NULL default '0',
  `fullsize` tinyint(3) NOT NULL,
  `font` varchar(50) default NULL,
  `font_size` int(11) default NULL,
  `last_update` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `pageid` (`pageid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_gallery`
--

LOCK TABLES `cms_gallery` WRITE;
/*!40000 ALTER TABLE `cms_gallery` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_gallery_photos`
--

DROP TABLE IF EXISTS `cms_gallery_photos`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_gallery_photos` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `file` text NOT NULL,
  `description` text NOT NULL,
  `order` int(11) NOT NULL default '0',
  `cachefile` varchar(255) NOT NULL,
  `count` int(11) NOT NULL default '0',
  `rating` int(11) default '0',
  `url` text,
  `internal_stat` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `internal_stat` (`internal_stat`),
  KEY `rating` (`rating`),
  KEY `pageid` (`pageid`)
) ENGINE=MyISAM AUTO_INCREMENT=152 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_gallery_photos`
--

LOCK TABLES `cms_gallery_photos` WRITE;
/*!40000 ALTER TABLE `cms_gallery_photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_gallery_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_image_cache`
--

DROP TABLE IF EXISTS `cms_image_cache`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_image_cache` (
  `id` int(11) NOT NULL auto_increment,
  `img_id` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `use_original` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_image_cache`
--

LOCK TABLES `cms_image_cache` WRITE;
/*!40000 ALTER TABLE `cms_image_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_image_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_images`
--

DROP TABLE IF EXISTS `cms_images`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_images` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL default '0',
  `path` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_images`
--

LOCK TABLES `cms_images` WRITE;
/*!40000 ALTER TABLE `cms_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_keys`
--

DROP TABLE IF EXISTS `cms_keys`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_keys` (
  `id` int(11) NOT NULL auto_increment,
  `crypt_key` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_keys`
--

LOCK TABLES `cms_keys` WRITE;
/*!40000 ALTER TABLE `cms_keys` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_languages`
--

DROP TABLE IF EXISTS `cms_languages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_languages` (
  `id` int(11) NOT NULL auto_increment,
  `filename` varchar(255) default NULL,
  `text_nl` varchar(255) default NULL,
  `text_uk` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_languages`
--

LOCK TABLES `cms_languages` WRITE;
/*!40000 ALTER TABLE `cms_languages` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_license`
--

DROP TABLE IF EXISTS `cms_license`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `cms_favicon` varchar(255) default NULL,
  `cms_logo` varchar(255) default NULL,
  `cms_mailings` tinyint(3) default NULL,
  `cms_protected` tinyint(3) default NULL,
  `cms_address` tinyint(3) default NULL,
  `cms_feedback` tinyint(3) default NULL,
  `cms_user_register` tinyint(3) default NULL,
  `cms_manage_hostname` varchar(255) default NULL,
  `google_analytics` varchar(255) NOT NULL,
  `letsstat_analytics` varchar(255) NOT NULL,
  `cms_shop` tinyint(3) NOT NULL,
  `cms_shop_page` int(11) NOT NULL,
  `cms_shop_results` int(11) NOT NULL,
  `cms_use_strict_mode` tinyint(3) NOT NULL,
  `ideal_type` varchar(50) NOT NULL,
  `ideal_merchant_id` varchar(50) NOT NULL,
  `ideal_last_order` int(11) NOT NULL,
  `ideal_currency` varchar(10) NOT NULL,
  `ideal_secret_key` varchar(255) NOT NULL,
  `ideal_test_mode` tinyint(3) NOT NULL,
  `yahoo_key` varchar(255) default NULL,
  `custom_401` int(11) NOT NULL,
  `custom_feedback` int(11) NOT NULL,
  `custom_403` int(11) NOT NULL,
  `custom_404` int(11) NOT NULL,
  `custom_602` int(11) NOT NULL,
  `custom_shop_cancel` int(11) NOT NULL,
  `custom_shop_error` int(11) NOT NULL,
  `custom_loginprofile` int(11) NOT NULL,
  PRIMARY KEY  (`cms_license`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_license`
--

LOCK TABLES `cms_license` WRITE;
/*!40000 ALTER TABLE `cms_license` DISABLE KEYS */;
INSERT INTO `cms_license` VALUES ('Welcome to your new home in cyberspace',0,'covide',0,NULL,'Covide, CRM, CMS, Groupware, VoIP','Covide combines great Groupware (shared email, calendars, files) and CRM (sales and support) in CRM-groupware. The most efficient way to work together. Integrate it with VoIP PBX Asterisk and OpenOffice and you can create a complete Virtual Office.',0,0,'','Covide','Covide','',0,0,0,0,0,'','',0,0,'on',0,0,0,'',0,'localhost',4,'','',NULL,NULL,NULL,NULL,NULL,'','','',0,0,0,0,'','',0,'','',0,'',0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `cms_license` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_license_siteroots`
--

DROP TABLE IF EXISTS `cms_license_siteroots`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `google_analytics` varchar(255) NOT NULL,
  `letsstat_analytics` varchar(255) NOT NULL,
  `yahoo_key` varchar(255) default NULL,
  `custom_401` int(11) NOT NULL,
  `custom_feedback` int(11) NOT NULL,
  `custom_403` int(11) NOT NULL,
  `custom_404` int(11) NOT NULL,
  `custom_602` int(11) NOT NULL,
  `custom_loginprofile` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_license_siteroots`
--

LOCK TABLES `cms_license_siteroots` WRITE;
/*!40000 ALTER TABLE `cms_license_siteroots` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_license_siteroots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_list`
--

DROP TABLE IF EXISTS `cms_list`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_list`
--

LOCK TABLES `cms_list` WRITE;
/*!40000 ALTER TABLE `cms_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_logins_log`
--

DROP TABLE IF EXISTS `cms_logins_log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_logins_log` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `datetime` int(11) NOT NULL default '0',
  `user_agent` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=248 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_logins_log`
--

LOCK TABLES `cms_logins_log` WRITE;
/*!40000 ALTER TABLE `cms_logins_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_logins_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_mailings`
--

DROP TABLE IF EXISTS `cms_mailings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_mailings` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_mailings`
--

LOCK TABLES `cms_mailings` WRITE;
/*!40000 ALTER TABLE `cms_mailings` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_mailings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_metadata`
--

DROP TABLE IF EXISTS `cms_metadata`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_metadata` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `fieldid` int(11) NOT NULL default '0',
  `value` text NOT NULL,
  `isDefault` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pageid` (`pageid`),
  KEY `fieldid` (`fieldid`),
  KEY `value` (`value`(255))
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_metadata`
--

LOCK TABLES `cms_metadata` WRITE;
/*!40000 ALTER TABLE `cms_metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_metadef`
--

DROP TABLE IF EXISTS `cms_metadef`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_metadef` (
  `id` int(11) NOT NULL auto_increment,
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(20) NOT NULL,
  `field_value` text NOT NULL,
  `order` int(11) NOT NULL default '0',
  `group` varchar(255) default NULL,
  `fphide` tinyint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_metadef`
--

LOCK TABLES `cms_metadef` WRITE;
/*!40000 ALTER TABLE `cms_metadef` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_metadef` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_permissions`
--

DROP TABLE IF EXISTS `cms_permissions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=330 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_permissions`
--

LOCK TABLES `cms_permissions` WRITE;
/*!40000 ALTER TABLE `cms_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_siteviews`
--

DROP TABLE IF EXISTS `cms_siteviews`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_siteviews` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `view` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_siteviews`
--

LOCK TABLES `cms_siteviews` WRITE;
/*!40000 ALTER TABLE `cms_siteviews` DISABLE KEYS */;
INSERT INTO `cms_siteviews` VALUES (2,2,'a:4:{s:3:\"cmd\";s:0:\"\";s:8:\"siteroot\";s:1:\"R\";s:6:\"buffer\";a:0:{}s:9:\"toonpages\";a:0:{}}');
/*!40000 ALTER TABLE `cms_siteviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_temp`
--

DROP TABLE IF EXISTS `cms_temp`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_temp` (
  `id` int(11) NOT NULL auto_increment,
  `userkey` varchar(255) NOT NULL,
  `ids` text NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userkey` (`userkey`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_temp`
--

LOCK TABLES `cms_temp` WRITE;
/*!40000 ALTER TABLE `cms_temp` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_temp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_templates`
--

DROP TABLE IF EXISTS `cms_templates`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_templates` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `data` longtext,
  `category` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_templates`
--

LOCK TABLES `cms_templates` WRITE;
/*!40000 ALTER TABLE `cms_templates` DISABLE KEYS */;
INSERT INTO `cms_templates` VALUES (10,'main template','<?php\r\n// load default stylesheet\r\n$template->load_css(11);\r\n// start html etc including full head information\r\n$template->start_html(1);\r\n// echo pagetitle in <h1> tag\r\n$template->getPageTitle($template->pageid);\r\n// echo page content\r\n$template->getPageData();\r\n// close page\r\n$template->getPageFooter();\r\n// close html and flush to client\r\n$template->end_html();\r\n?>','main'),(11,'Main CSS','html, body {\r\n	background-color: #FFFFFF;\r\n	color: #000000;\r\n}','css');
/*!40000 ALTER TABLE `cms_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_users`
--

DROP TABLE IF EXISTS `cms_users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_enabled` tinyint(3) NOT NULL,
  `email` varchar(255) NOT NULL,
  `registration_date` int(11) NOT NULL,
  `is_active` tinyint(3) NOT NULL,
  `confirm_hash` varchar(255) default NULL,
  `address_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_users`
--

LOCK TABLES `cms_users` WRITE;
/*!40000 ALTER TABLE `cms_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dimdim`
--

DROP TABLE IF EXISTS `dimdim`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dimdim` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `room` varchar(255) NOT NULL,
  `attendees` varchar(255) NOT NULL,
  `external_attendees` varchar(255) NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `dimdim`
--

LOCK TABLES `dimdim` WRITE;
/*!40000 ALTER TABLE `dimdim` DISABLE KEYS */;
/*!40000 ALTER TABLE `dimdim` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees_info`
--

DROP TABLE IF EXISTS `employees_info`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `employees_info`
--

LOCK TABLES `employees_info` WRITE;
/*!40000 ALTER TABLE `employees_info` DISABLE KEYS */;
INSERT INTO `employees_info` VALUES (1,2,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `employees_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL auto_increment,
  `category_id` int(11) default NULL,
  `question` mediumtext,
  `answer` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `faq`
--

LOCK TABLES `faq` WRITE;
/*!40000 ALTER TABLE `faq` DISABLE KEYS */;
/*!40000 ALTER TABLE `faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faq_cat`
--

DROP TABLE IF EXISTS `faq_cat`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `faq_cat` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `faq_cat`
--

LOCK TABLES `faq_cat` WRITE;
/*!40000 ALTER TABLE `faq_cat` DISABLE KEYS */;
/*!40000 ALTER TABLE `faq_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faxes`
--

DROP TABLE IF EXISTS `faxes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `faxes` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default NULL,
  `sender` varchar(255) default NULL,
  `receiver` varchar(255) default NULL,
  `relation_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_faxes_relation_id` (`relation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `faxes`
--

LOCK TABLES `faxes` WRITE;
/*!40000 ALTER TABLE `faxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `faxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filesys_files`
--

DROP TABLE IF EXISTS `filesys_files`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `filesys_files`
--

LOCK TABLES `filesys_files` WRITE;
/*!40000 ALTER TABLE `filesys_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `filesys_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filesys_folders`
--

DROP TABLE IF EXISTS `filesys_folders`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `filesys_folders`
--

LOCK TABLES `filesys_folders` WRITE;
/*!40000 ALTER TABLE `filesys_folders` DISABLE KEYS */;
INSERT INTO `filesys_folders` VALUES (3,'openbare mappen',1,0,NULL,NULL,0,NULL,NULL,NULL,0,0),(12,'Terrazur',1,1,8,NULL,4,NULL,NULL,NULL,0,0),(23,'mijn documenten',0,0,NULL,1,0,NULL,NULL,NULL,0,0),(20,'medewerkers',1,0,NULL,NULL,19,NULL,NULL,NULL,1,0),(21,'oud-medewerkers',1,0,NULL,NULL,19,NULL,NULL,NULL,1,0),(24,'projecten',1,0,NULL,NULL,0,NULL,NULL,NULL,1,0),(19,'hrm',1,0,NULL,NULL,0,NULL,NULL,NULL,1,0),(4,'relaties',1,0,NULL,NULL,0,NULL,NULL,NULL,1,0),(22,'covide',1,0,NULL,NULL,20,NULL,NULL,2,0,0),(108,'mijn documenten',0,0,NULL,2,0,NULL,NULL,NULL,0,0),(109,'mijn mappen',0,0,NULL,NULL,0,NULL,NULL,NULL,0,NULL),(110,'google mappen',0,0,NULL,NULL,0,NULL,NULL,NULL,0,NULL);
/*!40000 ALTER TABLE `filesys_folders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filesys_permissions`
--

DROP TABLE IF EXISTS `filesys_permissions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `filesys_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `folder_id` int(11) default NULL,
  `user_id` varchar(255) default NULL,
  `permissions` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_filesys_permissions_user_id` (`user_id`),
  KEY `cvd_filesys_permissions_folder_id` (`folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `filesys_permissions`
--

LOCK TABLES `filesys_permissions` WRITE;
/*!40000 ALTER TABLE `filesys_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `filesys_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_akties`
--

DROP TABLE IF EXISTS `finance_akties`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_akties`
--

LOCK TABLES `finance_akties` WRITE;
/*!40000 ALTER TABLE `finance_akties` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_akties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_begin_standen_finance`
--

DROP TABLE IF EXISTS `finance_begin_standen_finance`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `finance_begin_standen_finance` (
  `id` int(11) NOT NULL auto_increment,
  `grootboek_id` int(11) NOT NULL default '0',
  `stand` decimal(16,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_begin_standen_finance`
--

LOCK TABLES `finance_begin_standen_finance` WRITE;
/*!40000 ALTER TABLE `finance_begin_standen_finance` DISABLE KEYS */;
INSERT INTO `finance_begin_standen_finance` VALUES (1,1000,'0.00'),(2,1100,'0.00');
/*!40000 ALTER TABLE `finance_begin_standen_finance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_boekingen`
--

DROP TABLE IF EXISTS `finance_boekingen`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_boekingen`
--

LOCK TABLES `finance_boekingen` WRITE;
/*!40000 ALTER TABLE `finance_boekingen` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_boekingen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_boekingen_20012003`
--

DROP TABLE IF EXISTS `finance_boekingen_20012003`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_boekingen_20012003`
--

LOCK TABLES `finance_boekingen_20012003` WRITE;
/*!40000 ALTER TABLE `finance_boekingen_20012003` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_boekingen_20012003` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_grootboeknummers`
--

DROP TABLE IF EXISTS `finance_grootboeknummers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `finance_grootboeknummers` (
  `id` int(11) NOT NULL auto_increment,
  `nr` int(11) default NULL,
  `titel` varchar(255) default NULL,
  `debiteur` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_grootboeknummers`
--

LOCK TABLES `finance_grootboeknummers` WRITE;
/*!40000 ALTER TABLE `finance_grootboeknummers` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_grootboeknummers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_inkopen`
--

DROP TABLE IF EXISTS `finance_inkopen`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_inkopen`
--

LOCK TABLES `finance_inkopen` WRITE;
/*!40000 ALTER TABLE `finance_inkopen` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_inkopen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_jaar_afsluitingen`
--

DROP TABLE IF EXISTS `finance_jaar_afsluitingen`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `finance_jaar_afsluitingen` (
  `jaar` int(11) NOT NULL default '0',
  `datum_afgesloten` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_jaar_afsluitingen`
--

LOCK TABLES `finance_jaar_afsluitingen` WRITE;
/*!40000 ALTER TABLE `finance_jaar_afsluitingen` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_jaar_afsluitingen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_klanten`
--

DROP TABLE IF EXISTS `finance_klanten`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_klanten`
--

LOCK TABLES `finance_klanten` WRITE;
/*!40000 ALTER TABLE `finance_klanten` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_klanten` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_offertes`
--

DROP TABLE IF EXISTS `finance_offertes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `template_id` int(11) NOT NULL,
  `font` varchar(255) NOT NULL,
  `fontsize` int(11) NOT NULL,
  `template_setting` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `bcard_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_offertes`
--

LOCK TABLES `finance_offertes` WRITE;
/*!40000 ALTER TABLE `finance_offertes` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_offertes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_omzet_akties`
--

DROP TABLE IF EXISTS `finance_omzet_akties`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_omzet_akties`
--

LOCK TABLES `finance_omzet_akties` WRITE;
/*!40000 ALTER TABLE `finance_omzet_akties` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_omzet_akties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_omzet_totaal`
--

DROP TABLE IF EXISTS `finance_omzet_totaal`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `finance_omzet_totaal` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) NOT NULL default '0',
  `totaal_flow` int(11) default NULL,
  `totaal_flow_btw` decimal(16,2) default NULL,
  `totaal_flow_ex` decimal(16,2) default NULL,
  `totaal_flow_12` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_omzet_totaal`
--

LOCK TABLES `finance_omzet_totaal` WRITE;
/*!40000 ALTER TABLE `finance_omzet_totaal` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_omzet_totaal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_overige_posten`
--

DROP TABLE IF EXISTS `finance_overige_posten`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_overige_posten`
--

LOCK TABLES `finance_overige_posten` WRITE;
/*!40000 ALTER TABLE `finance_overige_posten` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_overige_posten` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_producten`
--

DROP TABLE IF EXISTS `finance_producten`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_producten`
--

LOCK TABLES `finance_producten` WRITE;
/*!40000 ALTER TABLE `finance_producten` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_producten` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_producten_in_offertes`
--

DROP TABLE IF EXISTS `finance_producten_in_offertes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_producten_in_offertes`
--

LOCK TABLES `finance_producten_in_offertes` WRITE;
/*!40000 ALTER TABLE `finance_producten_in_offertes` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_producten_in_offertes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_relatie_type`
--

DROP TABLE IF EXISTS `finance_relatie_type`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `finance_relatie_type` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_relatie_type`
--

LOCK TABLES `finance_relatie_type` WRITE;
/*!40000 ALTER TABLE `finance_relatie_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_relatie_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_soortbedrijf`
--

DROP TABLE IF EXISTS `finance_soortbedrijf`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `finance_soortbedrijf` (
  `id` int(11) NOT NULL auto_increment,
  `omschrijving` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_soortbedrijf`
--

LOCK TABLES `finance_soortbedrijf` WRITE;
/*!40000 ALTER TABLE `finance_soortbedrijf` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_soortbedrijf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_teksten`
--

DROP TABLE IF EXISTS `finance_teksten`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `finance_teksten` (
  `id` int(11) NOT NULL auto_increment,
  `html` mediumtext,
  `description` varchar(255) default NULL,
  `type` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `finance_teksten`
--

LOCK TABLES `finance_teksten` WRITE;
/*!40000 ALTER TABLE `finance_teksten` DISABLE KEYS */;
INSERT INTO `finance_teksten` VALUES (1,'14','betaling binnen',1),(8,'root@localhost','email',1),(6,'Betaling altijd strikt binnen&nbsp;<STRONG>14</STRONG> dagen.<BR>Bij niet tijdige betaling kan u rente en kosten in rekening worden gebracht.<BR>Op al onze leveringen zijn onze Algemene Voorwaarden van toepassing.','betaling',0),(7,'1111.22.333.a.44 ','btw nummer',1),(2,'1','laatste factuur nr',1);
/*!40000 ALTER TABLE `finance_teksten` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum`
--

DROP TABLE IF EXISTS `forum`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `forum`
--

LOCK TABLES `forum` WRITE;
/*!40000 ALTER TABLE `forum` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funambol_address_sync`
--

DROP TABLE IF EXISTS `funambol_address_sync`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `funambol_address_sync` (
  `id` int(11) NOT NULL auto_increment,
  `guid` varchar(255) NOT NULL,
  `address_table` varchar(255) NOT NULL,
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `funambol_address_sync`
--

LOCK TABLES `funambol_address_sync` WRITE;
/*!40000 ALTER TABLE `funambol_address_sync` DISABLE KEYS */;
/*!40000 ALTER TABLE `funambol_address_sync` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funambol_calendar_sync`
--

DROP TABLE IF EXISTS `funambol_calendar_sync`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `funambol_calendar_sync` (
  `id` int(11) NOT NULL auto_increment,
  `guid` varchar(255) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `calendar_id` (`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `funambol_calendar_sync`
--

LOCK TABLES `funambol_calendar_sync` WRITE;
/*!40000 ALTER TABLE `funambol_calendar_sync` DISABLE KEYS */;
/*!40000 ALTER TABLE `funambol_calendar_sync` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funambol_file_sync`
--

DROP TABLE IF EXISTS `funambol_file_sync`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `funambol_file_sync` (
  `id` int(11) NOT NULL auto_increment,
  `guid` varchar(255) NOT NULL,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `funambol_file_sync`
--

LOCK TABLES `funambol_file_sync` WRITE;
/*!40000 ALTER TABLE `funambol_file_sync` DISABLE KEYS */;
/*!40000 ALTER TABLE `funambol_file_sync` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funambol_stats`
--

DROP TABLE IF EXISTS `funambol_stats`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `funambol_stats` (
  `source` varchar(255) NOT NULL,
  `lasthash` varchar(255) NOT NULL,
  `synchash` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `funambol_stats`
--

LOCK TABLES `funambol_stats` WRITE;
/*!40000 ALTER TABLE `funambol_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `funambol_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funambol_todo_sync`
--

DROP TABLE IF EXISTS `funambol_todo_sync`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `funambol_todo_sync` (
  `id` int(11) NOT NULL auto_increment,
  `guid` varchar(255) NOT NULL,
  `todo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `funambol_todo_sync`
--

LOCK TABLES `funambol_todo_sync` WRITE;
/*!40000 ALTER TABLE `funambol_todo_sync` DISABLE KEYS */;
/*!40000 ALTER TABLE `funambol_todo_sync` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `functions`
--

DROP TABLE IF EXISTS `functions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `functions` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `functions`
--

LOCK TABLES `functions` WRITE;
/*!40000 ALTER TABLE `functions` DISABLE KEYS */;
/*!40000 ALTER TABLE `functions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hours_activities`
--

DROP TABLE IF EXISTS `hours_activities`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `hours_activities` (
  `id` int(11) NOT NULL auto_increment,
  `activity` varchar(255) default NULL,
  `tarif` decimal(16,2) default NULL,
  `department_id` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `hours_activities`
--

LOCK TABLES `hours_activities` WRITE;
/*!40000 ALTER TABLE `hours_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `hours_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hours_registration`
--

DROP TABLE IF EXISTS `hours_registration`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `hours` int(11) default NULL,
  `price` float(16,2) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_hours_registration_user_id` (`user_id`),
  KEY `cvd_hours_registration_project_id` (`project_id`),
  KEY `cvd_hours_registration_activity_id` (`activity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `hours_registration`
--

LOCK TABLES `hours_registration` WRITE;
/*!40000 ALTER TABLE `hours_registration` DISABLE KEYS */;
/*!40000 ALTER TABLE `hours_registration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issues`
--

DROP TABLE IF EXISTS `issues`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `execution_time` int(11) NOT NULL,
  `remarks` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_issues_user_id` (`user_id`),
  KEY `cvd_issues_address_id` (`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `issues`
--

LOCK TABLES `issues` WRITE;
/*!40000 ALTER TABLE `issues` DISABLE KEYS */;
/*!40000 ALTER TABLE `issues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `license`
--

DROP TABLE IF EXISTS `license`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `mail_migrated` tinyint(3) NOT NULL default '0',
  `has_cms` tinyint(3) default NULL,
  `has_project_ext_samba` tinyint(3) default NULL,
  `mail_force_server` varchar(255) default NULL,
  `mail_lock_settings` tinyint(3) default NULL,
  `has_project_ext` tinyint(3) default NULL,
  `force_ssl` smallint(3) default NULL,
  `default_lang` char(3) default 'EN',
  `autopatcher_enable` tinyint(3) NOT NULL,
  `autopatcher_lastpatch` varchar(255) NOT NULL,
  `has_project_declaration` tinyint(3) default NULL,
  `has_funambol` tinyint(3) NOT NULL,
  `address_strict_permissions` tinyint(3) default NULL,
  `cms_lock_settings` tinyint(3) default NULL,
  `address_migrated` tinyint(3) NOT NULL,
  `project_ext_share` varchar(255) default NULL,
  `filesystem_checked` varchar(10) default NULL,
  `disable_local_gzip` tinyint(3) NOT NULL,
  `has_radius` tinyint(2) default '0',
  `funambol_server_version` int(11) NOT NULL default '300',
  `enable_filestore_gzip` tinyint(3) NOT NULL,
  `use_project_global_reghour` tinyint(3) NOT NULL,
  `has_factuur` tinyint(3) NOT NULL,
  `postfixdsn` varchar(255) default NULL,
  `has_postfixadmin` tinyint(2) default NULL,
  `filesys_quota` varchar(255) NOT NULL,
  `has_campaign` tinyint(3) NOT NULL,
  `google_map_key` varchar(255) NOT NULL,
  `single_login` tinyint(2) default '0',
  `calendar_migrated` int(11) default '0',
  `has_onlineusers` tinyint(3) default '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `license`
--

LOCK TABLES `license` WRITE;
/*!40000 ALTER TABLE `license` DISABLE KEYS */;
INSERT INTO `license` VALUES ('covide','covide',1045522860,1,1,1,1,1,1,0,1,0,0,5,'',0,0,'2.4.1',0,'',0,0,0,1,0,0,'24M',0,1226530800,'My dear People.	My dear Bagginses and Boffins, and my dear Tooks and Brandybucks,and Grubbs, and Chubbs, and Burrowses, and Hornblowers, and Bolgers,Bracegirdles, Goodbodies, Brockhouses and Proudfoots.  Also my goodSackville Bagginses that I welcome back at last to Bag End.  Today is myone hundred and eleventh birthday: I am eleventy-one today!\"		-- J. R. R. Tolkien',1,0,1,'',0,0,0,0,0,0,0,'',0,1,0,'EN',1,'2008111905',0,0,0,0,2,'','',0,0,600,0,0,1,'',0,'',1,'',0,1,1);
/*!40000 ALTER TABLE `license` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_current`
--

DROP TABLE IF EXISTS `login_current`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `login_current` (
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `session_id` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `login_current`
--

LOCK TABLES `login_current` WRITE;
/*!40000 ALTER TABLE `login_current` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_current` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_log`
--

DROP TABLE IF EXISTS `login_log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `login_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `ip` varchar(255) default NULL,
  `time` int(11) NOT NULL default '0',
  `day` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `login_log`
--

LOCK TABLES `login_log` WRITE;
/*!40000 ALTER TABLE `login_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_attachments`
--

DROP TABLE IF EXISTS `mail_attachments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_attachments`
--

LOCK TABLES `mail_attachments` WRITE;
/*!40000 ALTER TABLE `mail_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_filters`
--

DROP TABLE IF EXISTS `mail_filters`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail_filters` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `sender` varchar(255) default NULL,
  `recipient` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `to_mapid` int(11) NOT NULL default '0',
  `priority` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_filters_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_filters`
--

LOCK TABLES `mail_filters` WRITE;
/*!40000 ALTER TABLE `mail_filters` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_filters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_folders`
--

DROP TABLE IF EXISTS `mail_folders`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail_folders` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `parent_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_folders_user_id` (`user_id`),
  KEY `cvd_mail_folders_parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_folders`
--

LOCK TABLES `mail_folders` WRITE;
/*!40000 ALTER TABLE `mail_folders` DISABLE KEYS */;
INSERT INTO `mail_folders` VALUES (4,'Archief',NULL,NULL),(5,'Postvak-IN',1,NULL),(6,'Verzonden-Items',1,NULL),(7,'Verwijderde-Items',1,NULL),(121,'Bounced berichten',1,NULL),(122,'Concepten',1,NULL),(123,'Concepten',2,NULL),(124,'Bounced berichten',2,NULL),(125,'Postvak-IN',2,NULL),(126,'Verzonden-Items',2,NULL),(127,'Verwijderde-Items',2,NULL),(128,'Sent-Items',3,NULL),(129,'Concepten',3,NULL);
/*!40000 ALTER TABLE `mail_folders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_messages`
--

DROP TABLE IF EXISTS `mail_messages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail_messages` (
  `id` int(11) NOT NULL auto_increment,
  `message_id` varchar(255) default NULL,
  `folder_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `project_id` int(11) default NULL,
  `private_id` int(11) NOT NULL default '0',
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
  `options` text,
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_messages`
--

LOCK TABLES `mail_messages` WRITE;
/*!40000 ALTER TABLE `mail_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_messages_data`
--

DROP TABLE IF EXISTS `mail_messages_data`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail_messages_data` (
  `mail_id` int(11) NOT NULL default '0',
  `body` longtext NOT NULL,
  `header` mediumtext NOT NULL,
  `mail_decoding` varchar(255) default NULL,
  PRIMARY KEY  (`mail_id`),
  KEY `cvd_mail_messages_data_mail_id` (`mail_id`),
  KEY `body` (`body`(255)),
  KEY `header` (`header`(255))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_messages_data`
--

LOCK TABLES `mail_messages_data` WRITE;
/*!40000 ALTER TABLE `mail_messages_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_messages_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_messages_data_archive`
--

DROP TABLE IF EXISTS `mail_messages_data_archive`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail_messages_data_archive` (
  `mail_id` int(11) NOT NULL default '0',
  `body` longtext NOT NULL,
  `header` mediumtext NOT NULL,
  `mail_decoding` varchar(255) NOT NULL,
  PRIMARY KEY  (`mail_id`),
  KEY `cvd_mail_messages_archive_body` (`body`(255)),
  KEY `cvd_mail_messages_archive_header` (`header`(255))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_messages_data_archive`
--

LOCK TABLES `mail_messages_data_archive` WRITE;
/*!40000 ALTER TABLE `mail_messages_data_archive` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_messages_data_archive` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_permissions`
--

DROP TABLE IF EXISTS `mail_permissions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `users` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_permissions`
--

LOCK TABLES `mail_permissions` WRITE;
/*!40000 ALTER TABLE `mail_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_signatures`
--

DROP TABLE IF EXISTS `mail_signatures`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail_signatures` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `email` varchar(255) default NULL,
  `signature` mediumtext,
  `subject` varchar(255) default NULL,
  `realname` varchar(255) default NULL,
  `companyname` varchar(255) default NULL,
  `signature_html` text NOT NULL,
  `default` tinyint(3) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_signatures_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_signatures`
--

LOCK TABLES `mail_signatures` WRITE;
/*!40000 ALTER TABLE `mail_signatures` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_signatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_templates`
--

DROP TABLE IF EXISTS `mail_templates`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail_templates` (
  `id` int(11) NOT NULL auto_increment,
  `header` mediumtext NOT NULL,
  `description` varchar(255) default NULL,
  `width` varchar(255) NOT NULL default '800',
  `repeat` smallint(3) NOT NULL default '1',
  `footer` mediumtext,
  `use_complex_mode` tinyint(3) NOT NULL,
  `html_data` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_templates`
--

LOCK TABLES `mail_templates` WRITE;
/*!40000 ALTER TABLE `mail_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_templates_files`
--

DROP TABLE IF EXISTS `mail_templates_files`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_templates_files`
--

LOCK TABLES `mail_templates_files` WRITE;
/*!40000 ALTER TABLE `mail_templates_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_templates_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_tracking`
--

DROP TABLE IF EXISTS `mail_tracking`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `campaign_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cvd_mail_tracking_mail_id` (`mail_id`),
  KEY `cvd_mail_tracking_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mail_tracking`
--

LOCK TABLES `mail_tracking` WRITE;
/*!40000 ALTER TABLE `mail_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meta_global`
--

DROP TABLE IF EXISTS `meta_global`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `meta_global` (
  `id` int(11) NOT NULL auto_increment,
  `meta_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `meta_global`
--

LOCK TABLES `meta_global` WRITE;
/*!40000 ALTER TABLE `meta_global` DISABLE KEYS */;
/*!40000 ALTER TABLE `meta_global` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meta_table`
--

DROP TABLE IF EXISTS `meta_table`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `meta_table`
--

LOCK TABLES `meta_table` WRITE;
/*!40000 ALTER TABLE `meta_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `meta_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `morgage`
--

DROP TABLE IF EXISTS `morgage`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `morgage`
--

LOCK TABLES `morgage` WRITE;
/*!40000 ALTER TABLE `morgage` DISABLE KEYS */;
/*!40000 ALTER TABLE `morgage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `campaign_id` int(11) NOT NULL,
  `is_support` smallint(3) default '0',
  `extra_recipients` varchar(255) default NULL,
  `is_draft` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `cvd_notes_user_id` (`user_id`),
  KEY `cvd_notes_sender` (`sender`),
  KEY `cvd_notes_timestamp` (`timestamp`),
  KEY `cvd_notes_project_id` (`project_id`),
  KEY `cvd_notes_address_id` (`address_id`),
  KEY `cvd_notes_delstatus` (`delstatus`),
  KEY `cvd_notes_is_done` (`is_done`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poll_answers`
--

DROP TABLE IF EXISTS `poll_answers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `poll_answers` (
  `id` int(11) NOT NULL auto_increment,
  `poll_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `answer` smallint(3) default NULL,
  `item_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `poll_answers`
--

LOCK TABLES `poll_answers` WRITE;
/*!40000 ALTER TABLE `poll_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `poll_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poll_items`
--

DROP TABLE IF EXISTS `poll_items`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `poll_items` (
  `id` int(11) NOT NULL auto_increment,
  `polls_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `poll_items`
--

LOCK TABLES `poll_items` WRITE;
/*!40000 ALTER TABLE `poll_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `poll_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `polls` (
  `id` int(11) NOT NULL auto_increment,
  `question` mediumtext,
  `is_active` smallint(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `polls`
--

LOCK TABLES `polls` WRITE;
/*!40000 ALTER TABLE `polls` DISABLE KEYS */;
/*!40000 ALTER TABLE `polls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES (3,'covide',NULL,2,1,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_declaration_batchcounter`
--

DROP TABLE IF EXISTS `projects_declaration_batchcounter`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projects_declaration_batchcounter` (
  `year` int(11) default NULL,
  `batchcounter` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_declaration_batchcounter`
--

LOCK TABLES `projects_declaration_batchcounter` WRITE;
/*!40000 ALTER TABLE `projects_declaration_batchcounter` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_declaration_batchcounter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_declaration_extrainfo`
--

DROP TABLE IF EXISTS `projects_declaration_extrainfo`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `identifier_adversary` varchar(255) NOT NULL,
  `identifier_expertise` varchar(255) NOT NULL,
  `agreements` text NOT NULL,
  `bcard_constituent` int(11) NOT NULL,
  `bcard_client` int(11) NOT NULL,
  `bcard_adversary` int(11) NOT NULL,
  `bcard_expertise` int(11) NOT NULL,
  `default_tarif` float(16,2) default NULL,
  `identifier` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_declaration_extrainfo`
--

LOCK TABLES `projects_declaration_extrainfo` WRITE;
/*!40000 ALTER TABLE `projects_declaration_extrainfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_declaration_extrainfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_declaration_options`
--

DROP TABLE IF EXISTS `projects_declaration_options`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projects_declaration_options` (
  `id` int(11) NOT NULL auto_increment,
  `group` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_declaration_options`
--

LOCK TABLES `projects_declaration_options` WRITE;
/*!40000 ALTER TABLE `projects_declaration_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_declaration_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_declaration_registration`
--

DROP TABLE IF EXISTS `projects_declaration_registration`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projects_declaration_registration` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `hour_tarif` float(16,2) NOT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_declaration_registration`
--

LOCK TABLES `projects_declaration_registration` WRITE;
/*!40000 ALTER TABLE `projects_declaration_registration` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_declaration_registration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_ext_activities`
--

DROP TABLE IF EXISTS `projects_ext_activities`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projects_ext_activities` (
  `id` int(11) NOT NULL auto_increment,
  `department_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `users` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_ext_activities`
--

LOCK TABLES `projects_ext_activities` WRITE;
/*!40000 ALTER TABLE `projects_ext_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_ext_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_ext_departments`
--

DROP TABLE IF EXISTS `projects_ext_departments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projects_ext_departments` (
  `id` int(11) NOT NULL auto_increment,
  `department` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `address_id` int(11) NOT NULL,
  `users` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_ext_departments`
--

LOCK TABLES `projects_ext_departments` WRITE;
/*!40000 ALTER TABLE `projects_ext_departments` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_ext_departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_ext_extrainfo`
--

DROP TABLE IF EXISTS `projects_ext_extrainfo`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projects_ext_extrainfo` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `project_year` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_ext_extrainfo`
--

LOCK TABLES `projects_ext_extrainfo` WRITE;
/*!40000 ALTER TABLE `projects_ext_extrainfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_ext_extrainfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_ext_metafields`
--

DROP TABLE IF EXISTS `projects_ext_metafields`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_ext_metafields`
--

LOCK TABLES `projects_ext_metafields` WRITE;
/*!40000 ALTER TABLE `projects_ext_metafields` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_ext_metafields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_ext_metavalues`
--

DROP TABLE IF EXISTS `projects_ext_metavalues`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projects_ext_metavalues` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `meta_value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_ext_metavalues`
--

LOCK TABLES `projects_ext_metavalues` WRITE;
/*!40000 ALTER TABLE `projects_ext_metavalues` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_ext_metavalues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects_master`
--

DROP TABLE IF EXISTS `projects_master`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `executor` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `projects_master`
--

LOCK TABLES `projects_master` WRITE;
/*!40000 ALTER TABLE `projects_master` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_master` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radius_settings`
--

DROP TABLE IF EXISTS `radius_settings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `radius_settings` (
  `radius_server` varchar(255) default NULL,
  `radius_port` int(11) default NULL,
  `shared_secret` varchar(255) default NULL,
  `nas_ip` varchar(255) default NULL,
  `auth_type` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `radius_settings`
--

LOCK TABLES `radius_settings` WRITE;
/*!40000 ALTER TABLE `radius_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `radius_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rssfeeds`
--

DROP TABLE IF EXISTS `rssfeeds`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `rssfeeds` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `category` varchar(255) default NULL,
  `homepage` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `user_id` int(11) default NULL,
  `count` int(11) default '5',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `rssfeeds`
--

LOCK TABLES `rssfeeds` WRITE;
/*!40000 ALTER TABLE `rssfeeds` DISABLE KEYS */;
INSERT INTO `rssfeeds` VALUES (1,'Covide',NULL,'http://www.covide.nl','http://www.covide.nl/rss/feed/204',0,5);
/*!40000 ALTER TABLE `rssfeeds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rssitems`
--

DROP TABLE IF EXISTS `rssitems`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `rssitems` (
  `id` int(11) NOT NULL auto_increment,
  `feed` int(11) default NULL,
  `subject` varchar(255) default NULL,
  `body` mediumtext,
  `link` varchar(255) default NULL,
  `date` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `rssitems`
--

LOCK TABLES `rssitems` WRITE;
/*!40000 ALTER TABLE `rssitems` DISABLE KEYS */;
/*!40000 ALTER TABLE `rssitems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `project_id` int(11) default '0',
  `classification` varchar(255) default NULL,
  `users` varchar(255) default NULL,
  `multirel` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_settings`
--

DROP TABLE IF EXISTS `sms_settings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sms_settings` (
  `companyid` varchar(255) default NULL,
  `userid` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `sender` varchar(255) default NULL,
  `request_uri` varchar(255) NOT NULL,
  `default_prefix` varchar(10) NOT NULL,
  `trans` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sms_settings`
--

LOCK TABLES `sms_settings` WRITE;
/*!40000 ALTER TABLE `sms_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snack_items`
--

DROP TABLE IF EXISTS `snack_items`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `snack_items` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `snack_items`
--

LOCK TABLES `snack_items` WRITE;
/*!40000 ALTER TABLE `snack_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `snack_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snack_order`
--

DROP TABLE IF EXISTS `snack_order`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `snack_order` (
  `snack_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `snack_order`
--

LOCK TABLES `snack_order` WRITE;
/*!40000 ALTER TABLE `snack_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `snack_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statistics`
--

DROP TABLE IF EXISTS `statistics`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `statistics` (
  `table` varchar(255) default NULL,
  `updates` int(11) default NULL,
  `vacuum` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `statistics`
--

LOCK TABLES `statistics` WRITE;
/*!40000 ALTER TABLE `statistics` DISABLE KEYS */;
/*!40000 ALTER TABLE `statistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_conn`
--

DROP TABLE IF EXISTS `status_conn`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `status_conn` (
  `user_id` int(11) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `status_conn`
--

LOCK TABLES `status_conn` WRITE;
/*!40000 ALTER TABLE `status_conn` DISABLE KEYS */;
/*!40000 ALTER TABLE `status_conn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_list`
--

DROP TABLE IF EXISTS `status_list`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `status_list` (
  `id` int(11) NOT NULL auto_increment,
  `msg_id` varchar(255) NOT NULL default '0',
  `mail_id` int(11) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `mark_delete` smallint(3) NOT NULL default '0',
  `mark_expunge` tinyint(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `status_list`
--

LOCK TABLES `status_list` WRITE;
/*!40000 ALTER TABLE `status_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `status_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support`
--

DROP TABLE IF EXISTS `support`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `support`
--

LOCK TABLES `support` WRITE;
/*!40000 ALTER TABLE `support` DISABLE KEYS */;
/*!40000 ALTER TABLE `support` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `finance` int(11) NOT NULL,
  `businesscard_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates_files`
--

DROP TABLE IF EXISTS `templates_files`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `templates_files`
--

LOCK TABLES `templates_files` WRITE;
/*!40000 ALTER TABLE `templates_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `templates_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates_settings`
--

DROP TABLE IF EXISTS `templates_settings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `footer_text` text,
  `logo_position` tinyint(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `templates_settings`
--

LOCK TABLES `templates_settings` WRITE;
/*!40000 ALTER TABLE `templates_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `templates_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `todo`
--

DROP TABLE IF EXISTS `todo`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `status` int(11) default '0',
  `priority` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `todo`
--

LOCK TABLES `todo` WRITE;
/*!40000 ALTER TABLE `todo` DISABLE KEYS */;
/*!40000 ALTER TABLE `todo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `twinfield_settings`
--

DROP TABLE IF EXISTS `twinfield_settings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `twinfield_settings` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `company` varchar(255) default NULL,
  `default_office` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `twinfield_settings`
--

LOCK TABLES `twinfield_settings` WRITE;
/*!40000 ALTER TABLE `twinfield_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `twinfield_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `members` text,
  `manager` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user_groups`
--

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userchangelog`
--

DROP TABLE IF EXISTS `userchangelog`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `userchangelog` (
  `id` int(11) NOT NULL auto_increment,
  `manager` int(11) default NULL,
  `user_id` int(11) default NULL,
  `timestamp` int(11) default NULL,
  `change` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `userchangelog`
--

LOCK TABLES `userchangelog` WRITE;
/*!40000 ALTER TABLE `userchangelog` DISABLE KEYS */;
/*!40000 ALTER TABLE `userchangelog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `xs_cms_level` int(11) default NULL,
  `xs_funambol` tinyint(3) NOT NULL,
  `mail_signature_html` text NOT NULL,
  `xs_funambol_expunge` tinyint(3) NOT NULL,
  `addresssyncmanage` varchar(255) NOT NULL,
  `mail_shortview` tinyint(3) NOT NULL,
  `mail_default_private` tinyint(3) NOT NULL,
  `authmethod` varchar(255) default NULL,
  `xs_funambol_version` tinyint(3) NOT NULL,
  `addressmode` int(11) default '0',
  `xs_classmanage` varchar(255) default NULL,
  `calendarinterval` int(11) NOT NULL default '15',
  `hour_format` int(11) default NULL,
  `mail_default_bcc` varchar(255) default NULL,
  `google_username` varchar(255) default NULL,
  `google_password` varchar(255) default NULL,
  `voip_number` varchar(50) default NULL,
  `xs_campaignmanage` tinyint(3) NOT NULL,
  `font` varchar(255) default NULL,
  `fontsize` int(11) default NULL,
  `mail_default_template` int(11) default NULL,
  `google_startingpoint` varchar(255) default NULL,
  `mail_hide_cmsforms` tinyint(3) default '0',
  `showbdays` tinyint(2) default '1',
  `default_address_fields` mediumtext,
  `default_address_fields_bcard` mediumtext,
  `dimdim_username` varchar(255) default NULL,
  `dimdim_password` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'archiefgebruiker','',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,2,NULL,NULL,0,1,0,1,0,0,'NL',0,0,NULL,0,NULL,NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,'',0,'',0,0,NULL,0,0,NULL,15,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,1,NULL,NULL,NULL,NULL),(1,'administrator','1a529f44b9575060c370478b01e6674c',2,1,1,1,0,0,0,0,1,NULL,1,1,1,NULL,1,7,'','','','','',0,NULL,0,1,22,0,'','',0,1,0,1,0,1,'EN',0,1,0,0,0,0,0,1,1,0,1,0,'',NULL,0,NULL,'',1,NULL,NULL,0,NULL,0,1,0,NULL,0,0,20,NULL,0,'',0,'',0,0,'database',0,0,'1',15,0,'','','','',1,'0',0,0,'',0,1,NULL,NULL,NULL,NULL),(2,'covide','1a529f44b9575060c370478b01e6674c',1,1,1,1,0,3,0,0,1,NULL,1,1,1,NULL,1,7,'','test@covide.nl','','test@covide.nl','',0,'',0,2674801,21,0,'','',0,1,0,601201,0,1,'EN',0,1,0,0,1,0,0,1,1,0,1,0,'',0,0,NULL,'',1,NULL,NULL,0,NULL,0,1,0,NULL,0,0,20,3,0,'',0,'',0,0,'database',0,0,'1',15,0,'','','','',1,'0',0,0,'',0,1,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-11-19 13:46:33
