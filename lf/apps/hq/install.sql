-- MySQL dump 10.11
--
-- Host: localhost    Database: bios_lf
-- ------------------------------------------------------
-- Server version	5.0.95

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
-- Table structure for table `hq_tickets`
--

DROP TABLE IF EXISTS `hq_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hq_tickets` (
  `id` int(5) NOT NULL auto_increment,
  `date` varchar(20) NOT NULL,
  `owner_id` int(5) NOT NULL,
  `project` varchar(128) NOT NULL,
  `category` varchar(128) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hq_tickets`
--

LOCK TABLES `hq_tickets` WRITE;
/*!40000 ALTER TABLE `hq_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `hq_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hq_events`
--

DROP TABLE IF EXISTS `hq_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hq_events` (
  `id` int(11) NOT NULL auto_increment,
  `project` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `note` text NOT NULL,
  `date` datetime NOT NULL,
  `ticket_id` int(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=141 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hq_events`
--

LOCK TABLES `hq_events` WRITE;
/*!40000 ALTER TABLE `hq_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `hq_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hq_projects`
--

DROP TABLE IF EXISTS `hq_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hq_projects` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(256) NOT NULL,
  `wiki` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hq_projects`
--

LOCK TABLES `hq_projects` WRITE;
/*!40000 ALTER TABLE `hq_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `hq_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hq_reference`
--

DROP TABLE IF EXISTS `hq_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hq_reference` (
  `id` int(5) NOT NULL auto_increment,
  `date` varchar(20) NOT NULL,
  `owner_id` int(5) NOT NULL,
  `project` varchar(128) NOT NULL,
  `category` varchar(128) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hq_reference`
--

LOCK TABLES `hq_reference` WRITE;
/*!40000 ALTER TABLE `hq_reference` DISABLE KEYS */;
/*!40000 ALTER TABLE `hq_reference` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-06-02 18:23:44
