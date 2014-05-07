-- MySQL dump 10.10
--
-- Host: localhost    Database: covide-devel
-- ------------------------------------------------------
-- Server version	5.0.20-Debian_1-log
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `address_finance_bank`
--

CREATE TABLE `address_finance_bank` (
  `id` int(11) NOT NULL auto_increment,
  `address_id` int(11) NOT NULL,
  `desc` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `address` varchar(250) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `place` varchar(250) NOT NULL,
  `province` varchar(40) NOT NULL,
  `country` varchar(40) NOT NULL,
  `iban` varchar(20) NOT NULL,
  `bic` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `address_finance`
--

CREATE TABLE `address_finance` (
  `address_id` int(11) NOT NULL auto_increment,
  `modify` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `tax_nr` varchar(50) NOT NULL,
  `acc_nr` varchar(50) NOT NULL,
  `ecotax` varchar(50) NOT NULL,
  `pay_remark` tinyblob NOT NULL,
  `kingid` int(11) NOT NULL,
  PRIMARY KEY  (`address_id`)
);
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

