-- MySQL dump 10.13  Distrib 5.7.18, for Linux (x86_64)
--
-- Host: localhost    Database: nyjl
-- ------------------------------------------------------
-- Server version	5.7.18-0ubuntu0.16.04.1

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
-- Table structure for table `forum_admin`
--

DROP TABLE IF EXISTS `forum_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_admin`
--

LOCK TABLES `forum_admin` WRITE;
/*!40000 ALTER TABLE `forum_admin` DISABLE KEYS */;
INSERT INTO `forum_admin` VALUES (1,'root','$2y$10$G0yecuK.J5OEkRdKf//0oOku6dIAD5ys/Y0lnBfLNjYACsFrMZLxm',1);
/*!40000 ALTER TABLE `forum_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_class`
--

DROP TABLE IF EXISTS `forum_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_class` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tips` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_class`
--

LOCK TABLES `forum_class` WRITE;
/*!40000 ALTER TABLE `forum_class` DISABLE KEYS */;
INSERT INTO `forum_class` VALUES (1,'闲聊','随意灌水区'),(2,'PHP',''),(3,'JavaScript',''),(4,'Photoshop',''),(5,'Flash',''),(6,'C程序设计',''),(7,'MySQL数据库',''),(8,'网页设计',''),(9,'网络营销',''),(10,'计算机网络基础',''),(11,'Illustrator平面设计',''),(12,'Linux网络操作系统',''),(13,'ASP.NET',''),(14,'Android应用开发',''),(15,'体育与健康','体育与健康');
/*!40000 ALTER TABLE `forum_class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_reply`
--

DROP TABLE IF EXISTS `forum_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `topic_id` int(10) NOT NULL DEFAULT '0',
  `reply_id` int(10) NOT NULL DEFAULT '0',
  `reply_name` varchar(32) CHARACTER SET gbk NOT NULL,
  `reply_email` varchar(100) CHARACTER SET gbk NOT NULL,
  `reply_detail` text CHARACTER SET gbk NOT NULL,
  `reply_pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `reply_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `a_id` (`reply_id`),
  FULLTEXT KEY `reply_pics` (`reply_pics`)
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_reply`
--

LOCK TABLES `forum_reply` WRITE;
/*!40000 ALTER TABLE `forum_reply` DISABLE KEYS */;
INSERT INTO `forum_reply` VALUES (68,61,1,'admin','','first test OK','','2016-10-12 12:57:58'),(75,61,6,'admin','','6','','2016-10-12 13:35:20'),(76,61,7,'admin','','7','','2016-10-12 13:35:24'),(77,61,8,'admin','','8','','2016-10-12 13:35:27'),(78,61,9,'admin','','9','','2016-10-12 13:35:29'),(79,61,10,'admin','','10','','2016-10-12 13:35:32'),(73,61,4,'admin','','第四条回复','','2016-10-12 13:25:00'),(74,61,5,'admin','','第五条回复','','2016-10-12 13:25:07'),(72,61,3,'admin','','第三条回复','','2016-10-12 13:24:45'),(71,61,2,'admin','','第二条回复','','2016-10-12 13:24:40'),(86,61,11,'admin','','123123123','','2016-10-13 11:50:12'),(87,61,12,'admin','','123123123','','2016-10-13 11:50:14'),(88,61,13,'admin','','123123123','','2016-10-13 11:50:17'),(89,61,14,'admin','','123123123','','2016-10-13 11:50:20'),(90,61,15,'admin','','123123123','','2016-10-13 11:50:22'),(91,61,16,'admin','','123123123','','2016-10-13 11:50:25'),(94,89,1,'admin','','测试成功 OK！','','2016-10-13 22:45:32'),(95,89,2,'admin','','123','','2016-10-13 22:51:25');
/*!40000 ALTER TABLE `forum_reply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_topic`
--

DROP TABLE IF EXISTS `forum_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_topic` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `class_id` int(10) NOT NULL,
  `topic` varchar(255) CHARACTER SET gbk NOT NULL,
  `detail` text CHARACTER SET gbk NOT NULL,
  `pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(32) CHARACTER SET gbk NOT NULL,
  `email` varchar(100) CHARACTER SET gbk NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `view` int(10) NOT NULL DEFAULT '0',
  `reply` int(10) NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_topic`
--

LOCK TABLES `forum_topic` WRITE;
/*!40000 ALTER TABLE `forum_topic` DISABLE KEYS */;
INSERT INTO `forum_topic` VALUES (67,1,'新用户尝试发帖','新用户尝试发帖内容','','123','123@123.123','2016-10-12 22:09:57',14,0,0),(68,1,'新用户尝试发帖','新用户尝试发帖图片内容','pics/1476281421.png','123','123@123.123','2016-10-12 22:10:21',23,0,0),(89,1,'手机尝试发图贴','第一次尝试','pics/1476369877.jpeg','123','123@123.123','2016-10-13 22:44:37',28,2,0),(76,1,'上传图片预览','尝试上传图片本地预览','pics/1476345220.png','admin','','2016-10-13 15:53:40',2,0,0),(61,1,'first test','first test','','admin','','2016-10-12 12:27:22',96,16,0),(97,1,'qwe','qwe','pics/1477319059.jpg','admin','','2016-10-24 22:24:19',1,0,0),(99,2,'管理员第二次尝试发帖','管理员第二次尝试发帖','pics/1477319322.jpg','管理员','','2016-10-24 22:28:42',3,0,1),(95,2,'123','123','','admin','','2016-10-24 19:04:22',11,0,0),(98,1,'管理员第一次尝试发帖','管理员第一次尝试发帖','pics/1477319193.jpg','管理员','','2016-10-24 22:26:33',32,0,1),(101,2,'修改用户权限验证方式','修改用户权限验证方式','pics/1477540278.jpg','hm081333','522751485@qq.com','2016-10-27 11:51:18',15,0,1);
/*!40000 ALTER TABLE `forum_topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_user`
--

DROP TABLE IF EXISTS `forum_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `realname` varchar(50) NOT NULL,
  `auth` int(1) NOT NULL DEFAULT '0',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=gbk;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_user`
--

LOCK TABLES `forum_user` WRITE;
/*!40000 ALTER TABLE `forum_user` DISABLE KEYS */;
INSERT INTO `forum_user` VALUES (3,'root','$2y$10$2jH.x35xlp4c7MJskYnVeO8VuTpvREUjfwPAdKp.78h2GY.S9hMXu','root@root.root','test',0,'2016-10-27 12:08:58'),(1,'管理员','管理员','522751485@qq.com','LYi-Ho',0,'2016-10-24 18:00:00'),(4,'hm081333','$2y$10$ww7CvAzywm63TrgxAc5LjO5LbCj5Qk/NrEId5QdWkEerXHcZVidhq','522751485@qq.com','LYi-Ho',1,'2016-10-27 11:32:20'),(33,'123123','$2y$10$ZoUxjzUlQnfVWxFts3A2HOpTJknT2ahSZHPabqfOCqhF.0tc9LmSC','123@123.123','123',0,'2016-10-31 22:03:47'),(34,'123','$2y$10$iZsEASmPA5ikW1hJa/l47.FED279nCjB0TFMEj4bE44EdX.4InKW.','','',0,'2016-10-31 22:12:46');
/*!40000 ALTER TABLE `forum_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-05-17 21:24:16
