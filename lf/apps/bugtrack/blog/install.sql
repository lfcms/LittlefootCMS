-- MySQL dump 10.13  Distrib 5.1.68, for unknown-linux-gnu (x86_64)
--
-- Host: localhost    Database: thecamp_db
-- ------------------------------------------------------
-- Server version	5.1.68-cll

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
-- Table structure for table `io_threads`
--

DROP TABLE IF EXISTS `io_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `io_threads` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `owner_id` int(5) NOT NULL,
  `instance` varchar(128) NOT NULL,
  `category` varchar(128) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `likes` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `io_threads`
--

LOCK TABLES `io_threads` WRITE;
/*!40000 ALTER TABLE `io_threads` DISABLE KEYS */;
/*!40000 ALTER TABLE `io_threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `io_messages`
--

DROP TABLE IF EXISTS `io_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `io_messages` (
  `msg_id` int(5) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL,
  `parent_id` int(5) NOT NULL,
  `sender_id` int(5) NOT NULL,
  `device` varchar(25) NOT NULL,
  `link` varchar(50) NOT NULL,
  `body` text NOT NULL,
  `likes` int(5) NOT NULL,
  `reply` int(11) NOT NULL,
  PRIMARY KEY (`msg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `io_messages`
--

LOCK TABLES `io_messages` WRITE;
/*!40000 ALTER TABLE `io_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `io_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `io_like`
--

DROP TABLE IF EXISTS `io_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `io_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(50) NOT NULL,
  `user_id` int(5) NOT NULL,
  `scope` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=184 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `io_like`
--

LOCK TABLES `io_like` WRITE;
/*!40000 ALTER TABLE `io_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `io_like` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-04-07  8:59:54
