-- MySQL dump 10.10
--
-- Host: localhost    Database: covide-devel
-- ------------------------------------------------------
-- Server version	5.0.20-Debian_1-log

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
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL auto_increment,
  `supplierid` int(11) NOT NULL default '0',
  `quality` varchar(50) default NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) default NULL,
  `prod_year` varchar(5) default NULL,
  `content` double(32,3) default NULL,
  `box` int(11) default NULL,
  `pallet` int(11) default NULL,
  `price` double(32,2) default NULL,
  `remark` longtext,
  `replacement` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `EAN_prod` varchar(50) default '0',
  `EAN_box` varchar(50) default NULL,
  `pricelist` tinyint(4) default '0',
  `boxlayer` int(50) default NULL,
  `prod_type` varchar(50) default NULL,
  `alcohol` float,
  `private` tinyint(4) NOT NULL default '0',
  `region` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `leverancierid` (`supplierid`),
  KEY `naam` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

