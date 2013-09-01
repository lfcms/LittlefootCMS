-- MySQL dump 10.11
--
-- Host: localhost    Database: jcstillc_dev
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
-- Table structure for table `todo_notes`
--

DROP TABLE IF EXISTS `todo_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo_notes` (
  `id` int(5) NOT NULL auto_increment,
  `note` text NOT NULL,
  `date` datetime NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `todo_notes`
--

LOCK TABLES `todo_notes` WRITE;
/*!40000 ALTER TABLE `todo_notes` DISABLE KEYS */;
INSERT INTO `todo_notes` VALUES (40,'No-grain cat food\r\n\r\n19004 E Colonial Dr\r\nOrlando, FL 32833','2012-08-17 00:00:00','Cat'),(29,'FTP Client - http://cyberduck.ch/','2012-01-20 14:58:00','Try'),(44,'C+J4E.com - cpj4e.com, template in email: \\\"wedding template\\\"','2012-08-17 00:00:00','Wedding'),(41,'Memory - http://www.ted.com/talks/joshua_foer_feats_of_memory_anyone_can_do.html','2012-08-17 00:00:00','Watch'),(39,'http://php.net/manual/en/function.readfile.php','2012-04-24 14:30:00','Try'),(47,'Passports as soon as Connie\\\'s Birth Certificate comes in.','2012-08-22 00:00:00','Wedding'),(46,'Unstoppable - http://www.imdb.com/title/tt0477080/','2012-08-21 00:00:00','Watch'),(61,'Gary Johnson AMA - http://www.reddit.com/r/IAmA/comments/zq0ow/i_am_gov_gary_johnson_the_libertarian_candidate/','2012-09-12 00:00:00','Read'),(48,'$1000 left to transfer to Connie from College','2012-08-30 00:00:00','Money'),(49,'Solution Hosting. Offer software to do this, IONCUBE IT\r\n\r\nhttp://www.softaculous.com/docs/Auto_Install_API','2012-09-01 00:00:00','Business'),(50,'Win2012 - http://www.amazon.com/Introducing-Windows-Server%C2%AE-2012-ebook/dp/B0084HJB06/ref=tmm_kin_title_0?ie=UTF8&qid=1346760288&sr=8-1\r\nSQL2012 - http://www.amazon.com/Introducing-Microsoft%C2%AE-Server%C2%AE-2012-ebook/dp/B007PJ6DSW/ref=tmm_kin_title_0?ie=UTF8&qid=1346760404&sr=1-1','2012-09-04 00:00:00','Watch'),(51,'Metta World Peace - http://www.youtube.com/watch?v=rZrVRU5G2dg','2012-09-04 00:00:00','Watch'),(53,'Talking Heads technique - http://www.reddit.com/r/LucidDreaming/comments/zdymj/here_is_a_lucid_dreaming_exercise_that_you_can_do/','2012-09-05 00:00:00','Dream'),(54,'Clinton Speech - https://www.youtube.com/watch?v=i5knEXDsrL4&hd=1','2012-09-06 00:00:00','Watch'),(55,'Get loan from credit union','2012-09-06 00:00:00','Wedding'),(56,'Credit Union 10am - Get pay stub','2012-09-07 00:00:00','Money'),(58,'Nothing Matters - http://www.youtube.com/watch?v=ootnLu8jaek','2012-09-07 00:00:00','Listen'),(59,'Tutes - http://www.wakeupinyourdreams.com/dream-tutorials/','2012-09-10 00:00:00','Dream'),(60,'From Charles - http://grooveshark.com/#!/s/Essential+Chip+Mix+2011/43v6J2?src=5','2012-09-12 00:00:00','Listen'),(62,'Jill Stein AMA - http://www.reddit.com/r/IAmA/comments/zs2n3/i_am_jill_stein_green_party_presidential/','2012-09-12 00:00:00','Read'),(63,'Loan - bbhimsen@mycfe.com','2012-09-13 00:00:00','Money');
/*!40000 ALTER TABLE `todo_notes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-09-15 23:14:54
