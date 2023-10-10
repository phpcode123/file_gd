-- MySQL dump 10.13  Distrib 5.7.37, for Linux (x86_64)
--
-- Host: localhost    Database: file_gd
-- ------------------------------------------------------
-- Server version	5.7.37-log

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
-- Table structure for table `tp_adsense`
--

DROP TABLE IF EXISTS `tp_adsense`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_adsense` (
  `itemid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adsense_domain` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `adsense_txt` varchar(1000) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `adsense_code` text CHARACTER SET utf8 NOT NULL,
  `adsense_switch` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `note` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`itemid`),
  KEY `adsense_domain` (`adsense_domain`),
  KEY `adsense_switch` (`adsense_switch`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_adsense`
--
-- Table structure for table `tp_click`
--

DROP TABLE IF EXISTS `tp_click`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_click` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_time` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `all_click` int(10) unsigned NOT NULL DEFAULT '0',
  `is_pc` int(10) unsigned NOT NULL DEFAULT '0',
  `is_m` int(10) unsigned NOT NULL DEFAULT '0',
  `file_click` int(10) unsigned NOT NULL DEFAULT '0',
  `archive_click` int(10) unsigned NOT NULL DEFAULT '0',
  `start_click` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_click` int(10) unsigned NOT NULL DEFAULT '0',
  `index_click` int(10) unsigned NOT NULL DEFAULT '0',
  `file_num` int(10) unsigned NOT NULL DEFAULT '0',
  `file_byte` bigint(20) unsigned NOT NULL DEFAULT '0',
  `file_size` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ad_income` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `ad_views` int(10) unsigned NOT NULL DEFAULT '0',
  `ad_display` int(10) unsigned NOT NULL DEFAULT '0',
  `ad_hits` int(10) unsigned NOT NULL DEFAULT '0',
  `ad_cpc` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `ad_rpm` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `ad_ctr` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `date_time` (`date_time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_click`
--


DROP TABLE IF EXISTS `tp_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_contact` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `remote_ip` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `message` varchar(10000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_contact`
--

--
-- Table structure for table `tp_domain`
--

DROP TABLE IF EXISTS `tp_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_domain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `http_prefix` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `site_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `index_title` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `index_keyword` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `index_description` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `display_ad` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sitemap` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `domain_url` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_domain`
--

LOCK TABLES `tp_domain` WRITE;
/*!40000 ALTER TABLE `tp_domain` DISABLE KEYS */;
INSERT INTO `tp_domain` VALUES (1,'http://','192.168.0.5:8083','localhost','localhost','localhost','localhost',1,0,'');
/*!40000 ALTER TABLE `tp_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tp_file`
--

DROP TABLE IF EXISTS `tp_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_file` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `site_id` int(10) unsigned NOT NULL DEFAULT '0',
  `server_id` int(10) unsigned NOT NULL DEFAULT '0',
  `archive_str` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `short_str` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_hash` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_name` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_byte` bigint(20) unsigned NOT NULL DEFAULT '0',
  `file_size` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `downloads` int(10) unsigned NOT NULL DEFAULT '0',
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `last_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `display_ad` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `redis_index` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `delete_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_404` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `short_str` (`short_str`),
  KEY `file_hash` (`file_hash`),
  KEY `hits` (`hits`),
  KEY `downloads` (`downloads`),
  KEY `redis_index` (`redis_index`),
  KEY `server_id` (`server_id`),
  KEY `delete_status` (`delete_status`),
  KEY `last_timestamp` (`last_timestamp`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_file`
--


--
-- Table structure for table `tp_file_archive`
--

DROP TABLE IF EXISTS `tp_file_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_file_archive` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `site_id` int(10) unsigned NOT NULL DEFAULT '0',
  `server_id` int(10) unsigned NOT NULL DEFAULT '0',
  `file_id` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `short_str` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_byte` bigint(20) unsigned NOT NULL DEFAULT '0',
  `file_size` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `last_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `display_ad` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `redis_index` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_404` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `server_id` (`server_id`),
  KEY `short_str` (`short_str`),
  KEY `hits` (`hits`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `tp_file_creater_info`
--

DROP TABLE IF EXISTS `tp_file_creater_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_file_creater_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_ip` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_pc` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;



DROP TABLE IF EXISTS `tp_http_referer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_http_referer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `short_str` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `http_referer` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_agent` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `is_pc` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_spider` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_language` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `page_from` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timestamp` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `short_str` (`short_str`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;



DROP TABLE IF EXISTS `tp_index_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_index_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `http_referer` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_agent` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `is_pc` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_spider` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_language` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timestamp` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `tp_malicious_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_malicious_file` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file_hash` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_name` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_size` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timestamp` int(10) NOT NULL DEFAULT '0',
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `file_hash` (`file_hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `tp_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_report` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `reason` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `remote_ip` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment` varchar(10000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `tp_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_server` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `server_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `disk_size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `disk_avail` bigint(20) unsigned NOT NULL DEFAULT '0',
  `disk_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `times` int(8) unsigned NOT NULL DEFAULT '0',
  `error_times` int(8) unsigned NOT NULL DEFAULT '0',
  `server_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `use_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `server_order_date` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `server_expiration_date` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_server`
--

LOCK TABLES `tp_server` WRITE;
/*!40000 ALTER TABLE `tp_server` DISABLE KEYS */;
INSERT INTO `tp_server` VALUES (100,'https://f100.cdn_server.com',257024,57422,1,899260,0,0,1,1695516596,'20230609','20230808','//250Gssd');
/*!40000 ALTER TABLE `tp_server` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-09-24  8:49:57
