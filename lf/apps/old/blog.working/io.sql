-- MySQL dump 10.11
--
-- Host: localhost    Database: jcstillc_io
-- ------------------------------------------------------
-- Server version	5.0.95-community

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
-- Table structure for table `io_like`
--

DROP TABLE IF EXISTS `io_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `io_like` (
  `id` int(11) NOT NULL auto_increment,
  `link` varchar(50) NOT NULL,
  `user_id` int(5) NOT NULL,
  `scope` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=152 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `io_like`
--

LOCK TABLES `io_like` WRITE;
/*!40000 ALTER TABLE `io_like` DISABLE KEYS */;
INSERT INTO `io_like` VALUES (111,'m_like390',1,'int'),(64,'t_like163',1,'int'),(57,'t_like161',1,'int'),(77,'m_like489',1,'int'),(76,'m_like488',1,'int'),(73,'m_like489',9,'int'),(74,'m_like490',9,'int'),(75,'m_like491',9,'int'),(110,'t_like202',1,'int'),(70,'m_like472',9,'int'),(72,'m_like488',9,'int'),(17,'t_like157',1,'int'),(18,'m_like463',1,'int'),(19,'m_like464',1,'int'),(20,'m_like465',1,'int'),(21,'m_like466',1,'int'),(22,'m_like467',1,'int'),(23,'m_like468',1,'int'),(24,'m_like469',1,'int'),(25,'m_like470',1,'int'),(26,'m_like471',1,'int'),(27,'m_like477',1,'int'),(28,'m_like478',1,'int'),(29,'m_like479',1,'int'),(30,'t_like155',1,'int'),(31,'t_like154',1,'int'),(32,'t_like153',1,'int'),(33,'m_like453',1,'int'),(34,'t_like151',1,'int'),(35,'m_like442',1,'int'),(36,'m_like481',1,'int'),(69,'m_like473',9,'int'),(68,'m_like480',1,'int'),(71,'m_like480',9,'int'),(65,'m_like460',1,'int'),(62,'m_like484',1,'int'),(61,'t_like162',1,'int'),(44,'m_like383',5,'int'),(45,'m_like454',5,'int'),(46,'m_like385',5,'int'),(47,'m_like456',5,'int'),(48,'m_like457',5,'int'),(49,'m_like384',5,'int'),(50,'t_like161',5,'int'),(51,'m_like483',5,'int'),(52,'t_like160',5,'int'),(53,'m_like464',5,'int'),(54,'m_like468',5,'int'),(78,'m_like490',1,'int'),(79,'m_like491',1,'int'),(80,'m_like493',1,'int'),(81,'m_like498',1,'int'),(82,'m_like499',1,'int'),(83,'t_like164',1,'int'),(84,'t_like165',1,'int'),(85,'m_like504',1,'int'),(86,'m_like509',1,'int'),(87,'t_like183',1,'int'),(89,'m_like500',5,'int'),(90,'m_like501',5,'int'),(91,'m_like502',5,'int'),(92,'m_like503',5,'int'),(93,'m_like385',1,'int'),(94,'m_like383',1,'int'),(98,'t_like177',7,'int'),(97,'m_like500',7,'int'),(99,'m_like526',1,'int'),(100,'m_like517',1,'int'),(101,'m_like518',1,'int'),(102,'t_like188',5,'int'),(106,'m_like529',1,'int'),(105,'t_like188',1,'int'),(107,'t_like191',1,'int'),(108,'m_like457',1,'int'),(109,'m_like547',1,'int'),(112,'m_like500',1,'int'),(113,'m_like501',1,'int'),(118,'t_like221',1,'int'),(117,'m_like587',1,'int'),(119,'m_like593',1,'int'),(124,'m_like755',1,'int'),(125,'m_like754',1,'int'),(126,'m_like756',1,'int'),(127,'m_like757',1,'int'),(134,'m_like550',1,'int'),(133,'t_like244',1,'int'),(130,'m_like766',1,'int'),(131,'t_like242',1,'int'),(135,'m_like772',1,'int'),(136,'t_like245',5,'int'),(137,'t_like247',1,'int'),(138,'m_like7',1,'int'),(139,'t_like1',1,'int'),(140,'t_like2',22,'int'),(141,'t_like4',1,'int'),(142,'m_like11',1,'int'),(143,'m_like9',1,'int'),(145,'m_like14',1,'int'),(146,'m_like16',1,'int'),(147,'m_like19',1,'int'),(148,'m_like20',1,'int'),(150,'t_like9',1,'int');
/*!40000 ALTER TABLE `io_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `io_messages`
--

DROP TABLE IF EXISTS `io_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `io_messages` (
  `msg_id` int(5) NOT NULL auto_increment,
  `date` varchar(20) NOT NULL,
  `parent_id` int(5) NOT NULL,
  `sender_id` int(5) NOT NULL,
  `device` varchar(25) NOT NULL,
  `link` varchar(50) NOT NULL,
  `body` text NOT NULL,
  `likes` int(5) NOT NULL,
  PRIMARY KEY  (`msg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `io_messages`
--

LOCK TABLES `io_messages` WRITE;
/*!40000 ALTER TABLE `io_messages` DISABLE KEYS */;
INSERT INTO `io_messages` VALUES (6,'2011-07-20 18:13:20',4,1,'desktop','','pretty cool mang',0),(7,'2011-07-20 18:45:46',4,0,'desktop','','http://itsjtam.com/readyweb.png',0),(8,'2011-07-25 06:15:04',5,1,'desktop','','Stick to cPanel method of being brought to a single page with nothing but what you need to edit.\\',0),(9,'2011-07-25 06:15:26',5,1,'desktop','','This will cut down on trying to implement the entire system in a big ass switch',1),(10,'2011-07-25 12:41:49',5,1,'desktop','','Find a way to edit / view / edit all at the same time (dev mode)',0),(16,'2011-08-10 05:40:03',5,1,'desktop','','Make a site like this: http://www.enetdesign.co.uk/',1),(17,'2011-08-16 23:46:15',4,0,'desktop','','&#039;&gt;&quot;&gt;&lt;script&gt;alert(0);&lt;/script&gt;',0),(14,'2011-08-04 05:33:06',6,1,'desktop','','Fixed :P',1),(18,'2011-08-16 23:47:15',4,0,'desktop','6','test',0),(19,'2011-08-16 23:51:50',6,0,'desktop','','',1),(20,'2011-08-16 23:51:58',6,0,'desktop','','&#039;',1),(21,'2011-08-17 02:44:01',5,1,'desktop','','Amnesia - Skepta',0),(22,'2011-09-04 02:10:22',6,1,'desktop','','You should setup h2fs with regex linked to solutions',0),(24,'2011-09-17 12:22:55',6,0,'desktop','','test',0),(25,'2011-09-18 06:49:20',6,1,'desktop','','http://sleepyti.me/',0),(26,'2011-09-21 00:33:41',6,0,'desktop','','http://forum.xbmc.org/showthread.php?t=87703',0),(27,'2011-10-05 05:39:35',6,0,'desktop','','http://php.net/manual/en/function.memory-get-peak-usage.php',0),(28,'2011-10-09 05:22:16',6,1,'desktop','','http://www.youtube.com/watch?v=aFQFB5YpDZE',0),(29,'2011-10-11 03:39:26',6,0,'desktop','','http://soundcloud.com/kingkrow/the-grip-sip-vol-3-mixed-by',0),(30,'2011-10-17 02:12:23',6,0,'desktop','','DJ Hazard &amp; D-Minds - Mr. Happy',0),(31,'2011-10-19 04:43:20',4,0,'desktop','','http://i.imgur.com/Wa0NT.gif',0),(32,'2011-10-19 04:43:26',6,0,'desktop','','http://i.imgur.com/Wa0NT.gif',0),(36,'2011-11-01 05:44:06',8,0,'desktop','','http://youtu.be/50rq4z0enfk',0),(34,'2011-10-26 04:27:26',8,1,'desktop','','http://www.mattweidnerlaw.com/Civil-Case.wav',0),(35,'2011-10-31 00:30:46',8,0,'desktop','','http://kolibrios.org/en/',0),(37,'2011-11-08 05:26:24',8,0,'desktop','','http://www.mechlivinglegends.net/',0),(38,'2011-11-12 23:37:52',8,0,'desktop','','https://docs.google.com/viewer?a=v&amp;pid=explorer&amp;chrome=true&amp;srcid=0Bx_wEpbf68sZNjY1OWI2ZTItNjAxMS00MTEwLWI5ZTgtYjk2MzdmNTU5NzJh&amp;hl=en_US',0),(39,'2011-11-19 00:59:24',8,0,'desktop','','http://www.slapthebass.com/',0),(40,'2012-06-05 17:04:03',4,0,'desktop','','booooobehs',0),(41,'2012-06-05 17:04:25',8,0,'desktop','','tittehs',0),(42,'2012-06-05 17:04:49',6,0,'desktop','','assholes',0),(43,'2012-06-23 17:53:03',8,0,'desktop','','http://forum.xda-developers.com/showthread.php?t=1052813',0),(44,'2012-07-30 02:23:36',6,0,'desktop','','Bios can you accept my friend request? on Steam',0),(45,'2012-08-28 22:59:23',8,1,'desktop','','test',0),(46,'2012-09-13 15:29:17',8,1,'desktop','35','test',0);
/*!40000 ALTER TABLE `io_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `io_threads`
--

DROP TABLE IF EXISTS `io_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `io_threads` (
  `id` int(5) NOT NULL auto_increment,
  `date` varchar(20) NOT NULL,
  `owner_id` int(5) NOT NULL,
  `content` text NOT NULL,
  `acl` varchar(15) NOT NULL,
  `likes` int(5) NOT NULL,
  `to` int(5) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `io_threads`
--

LOCK TABLES `io_threads` WRITE;
/*!40000 ALTER TABLE `io_threads` DISABLE KEYS */;
INSERT INTO `io_threads` VALUES (8,'2011-10-26 04:27:13',1,'New thead: links I want to read later','public',0,0),(4,'2011-07-20 18:12:58',22,'I don&#039;t even know what to say... I&#039;m all broken up.','public',1,0),(5,'2011-07-25 06:14:31',1,'XMS updates','private',0,0),(6,'2011-08-03 13:32:09',1,'I am well aware of my stupid // thing in the url... everywhere in my code references the subdirectory variable in the config. well its just &quot;&quot; so I need to go through and fix this so it checks first -.-','public',0,0),(9,'2012-08-28 22:59:28',1,'test','public',1,0),(10,'2012-09-13 15:28:32',1,'Going to try to move this blog system into a proper app. Wish me luck.','public',0,0);
/*!40000 ALTER TABLE `io_threads` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-09-13 16:47:05
