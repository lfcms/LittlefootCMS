-- MySQL dump 10.11
--
-- Host: localhost    Database: bios_dump
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
-- Table structure for table `lf_acl_global`
--

DROP TABLE IF EXISTS `lf_acl_global`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lf_acl_global` (
  `id` int(11) NOT NULL auto_increment,
  `action` varchar(128) NOT NULL,
  `perm` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lf_acl_global`
--


--
-- Table structure for table `lf_acl_inherit`
--

CREATE TABLE `lf_acl_inherit` (
  `id` int(11) NOT NULL auto_increment,
  `group` varchar(128) NOT NULL,
  `inherits` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lf_acl_inherit`
--

--
-- Table structure for table `lf_acl_user`
--

CREATE TABLE `lf_acl_user` (
  `id` int(11) NOT NULL auto_increment,
  `action` varchar(256) NOT NULL,
  `perm` int(11) NOT NULL,
  `affects` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lf_acl_user`
--


--
-- Table structure for table `lf_actions`
--

DROP TABLE IF EXISTS `lf_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lf_actions` (
  `id` int(5) NOT NULL auto_increment,
  `parent` int(5) NOT NULL,
  `position` varchar(19) NOT NULL,
  `alias` varchar(32) NOT NULL,
  `title` varchar(128) NOT NULL,
  `label` varchar(16) NOT NULL,
  `app` int(1) NOT NULL,
  `template` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=198 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lf_actions`
--

LOCK TABLES `lf_actions` WRITE;
/*!40000 ALTER TABLE `lf_actions` DISABLE KEYS */;
INSERT INTO `lf_actions` VALUES (109,-1,'1','','Home','Home',0,'default'),(181,-1,'0','hidden','Hidden','Hidden',1,'default'),(196,-1,'2','blog','Blog','Blog',1,'default');
/*!40000 ALTER TABLE `lf_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lf_links`
--

DROP TABLE IF EXISTS `lf_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lf_links` (
  `id` int(6) NOT NULL auto_increment,
  `include` varchar(19) NOT NULL,
  `app` varchar(25) NOT NULL,
  `ini` varchar(45) NOT NULL,
  `section` varchar(10) NOT NULL,
  `recursive` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=173 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lf_links`
--

LOCK TABLES `lf_links` WRITE;
/*!40000 ALTER TABLE `lf_links` DISABLE KEYS */;
INSERT INTO `lf_links` VALUES (171,'196','blog','','content',0),(169,'181','pages','5','content',0),(168,'109','pages','4','content',0);
/*!40000 ALTER TABLE `lf_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lf_pages`
--

DROP TABLE IF EXISTS `lf_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lf_pages` (
  `id` int(5) NOT NULL auto_increment,
  `author` int(5) NOT NULL,
  `title` varchar(30) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lf_pages`
--

LOCK TABLES `lf_pages` WRITE;
/*!40000 ALTER TABLE `lf_pages` DISABLE KEYS */;
INSERT INTO `lf_pages` VALUES (4,0,'Sample Page','<p>This is an example page. Log in to the admin to mess with it.</p>'),(5,0,'Hidden Page','<p>This page is linked to a \"hidden\" nav item.</p>');
/*!40000 ALTER TABLE `lf_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lf_settings`
--

DROP TABLE IF EXISTS `lf_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lf_settings` (
  `id` int(5) NOT NULL auto_increment,
  `var` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
  `val` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lf_settings`
--

LOCK TABLES `lf_settings` WRITE;
/*!40000 ALTER TABLE `lf_settings` DISABLE KEYS */;
INSERT INTO `lf_settings` VALUES (1,'default_skin','fresh'),(5,'rewrite','off'),(6,'nav_class',''),(7,'force_url',''),(9,'debug','off'),(10,'signup','off'),(11,'simple_cms','_lfcms');
/*!40000 ALTER TABLE `lf_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lf_users`
--

DROP TABLE IF EXISTS `lf_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lf_users` (
  `id` int(5) NOT NULL auto_increment,
  `user` varchar(128) NOT NULL,
  `pass` varchar(40) NOT NULL,
  `email` varchar(256) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `last_request` varchar(20) NOT NULL,
  `status` varchar(128) NOT NULL,
  `access` varchar(64) NOT NULL,
  `hash` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lf_users`
--

LOCK TABLES `lf_users` WRITE;
/*!40000 ALTER TABLE `lf_users` DISABLE KEYS */;
INSERT INTO `lf_users` VALUES (1,'admin','9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684','me@mydomain.com','Admin','','2012-08-10 17:41:15','valid','admin','');
/*!40000 ALTER TABLE `lf_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-11-25 17:27:45
