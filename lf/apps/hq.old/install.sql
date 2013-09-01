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
-- Table structure for table `hq_events`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hq_events` (
  `id` int(11) NOT NULL auto_increment,
  `project` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `note` text NOT NULL,
  `date` datetime NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=138 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hq_events`
--

LOCK TABLES `hq_events` WRITE;
/*!40000 ALTER TABLE `hq_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `hq_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hq_notes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hq_notes` (
  `id` int(11) NOT NULL auto_increment,
  `project` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `note` text NOT NULL,
  `date` datetime NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=142 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hq_notes`
--

LOCK TABLES `hq_notes` WRITE;
/*!40000 ALTER TABLE `hq_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `hq_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hq_projects`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hq_projects` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(256) NOT NULL,
  `wiki` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hq_projects`
--

LOCK TABLES `hq_projects` WRITE;
/*!40000 ALTER TABLE `hq_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `hq_projects` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-01-02 11:37:30
