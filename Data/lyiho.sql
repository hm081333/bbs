-- MySQL dump 10.13  Distrib 5.7.20, for Linux (x86_64)
--
-- Host: 123.249.20.195    Database: lyiho
-- ------------------------------------------------------
-- Server version	5.7.20-0ubuntu0.16.04.1

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
-- Table structure for table `ly_admin`
--

DROP TABLE IF EXISTS `ly_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员用户列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_admin`
--

LOCK TABLES `ly_admin` WRITE;
/*!40000 ALTER TABLE `ly_admin` DISABLE KEYS */;
INSERT INTO `ly_admin` VALUES (1,'root','$2y$10$G0yecuK.J5OEkRdKf//0oOku6dIAD5ys/Y0lnBfLNjYACsFrMZLxm',1);
/*!40000 ALTER TABLE `ly_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_baiduid`
--

DROP TABLE IF EXISTS `ly_baiduid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_baiduid` (
  `id` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(30) unsigned NOT NULL,
  `bduss` text NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `refresh_time` int(11) DEFAULT '0' COMMENT '刷新列表时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`) USING BTREE,
  KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='会员绑定的BDUSS ID列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_baiduid`
--

LOCK TABLES `ly_baiduid` WRITE;
/*!40000 ALTER TABLE `ly_baiduid` DISABLE KEYS */;
INSERT INTO `ly_baiduid` VALUES (1,1,'dkaEFROFZ1eWM1WXZyTGRYeGNxQkwtcElXNkZ0NFNNQ3h-RjBTd0ZMMDF4YnRZSVFBQUFBJCQAAAAAAAAAAAEAAAAbw9MHaG0wODEzMzMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADU4lFg1OJRYZV','hm081333',1506836208),(3,2,'GN-R1V6V1NTM3VnRjV-TTNDWFpzY1luTjBiTklkU09LYzZlNmtRbmlLR2RQZHBaSVFBQUFBJCQAAAAAAAAAAAEAAAC2oRkk0rnYvMbgwejDwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJ2wslmdsLJZW','夜丶凄凌美',1508931186);
/*!40000 ALTER TABLE `ly_baiduid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_class`
--

DROP TABLE IF EXISTS `ly_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_class` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tips` varchar(255) NOT NULL,
  `add_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='课程列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_class`
--

LOCK TABLES `ly_class` WRITE;
/*!40000 ALTER TABLE `ly_class` DISABLE KEYS */;
INSERT INTO `ly_class` VALUES (1,'闲聊','随意灌水区，随便灌水的地方。。。写个长一点儿的说明试一下css省略效果。感觉有点爆炸',1504446603),(2,'PHP','PHP是世界上最好的语言',1504446603),(3,'JavaScript','JS是微信最常用的语言',1504446603),(4,'Photoshop','',1504446603),(5,'Flash','',1504446603),(6,'C程序设计','',1504446603),(7,'MySQL数据库','',1504446603),(8,'网页设计','',1504446603),(9,'网络营销','',1504446603),(10,'计算机网络基础','',1504446603),(11,'Illustrator平面设计','',1504446603),(12,'Linux网络操作系统','',1504446603),(13,'ASP.NET','',1504446603),(14,'Android应用开发','',1504446603),(16,'PhalApi','PhalApi是一个用于快速写api接口的php框架',1504446603);
/*!40000 ALTER TABLE `ly_class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_delivery`
--

DROP TABLE IF EXISTS `ly_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_delivery` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `code` varchar(255) DEFAULT NULL COMMENT '快递公司代码',
  `sn` varchar(255) DEFAULT NULL COMMENT '快递单号',
  `add_time` int(10) DEFAULT NULL COMMENT '添加时间',
  `last_time` int(10) DEFAULT NULL COMMENT '上次查看时间',
  `state` int(1) DEFAULT NULL COMMENT '单号状态',
  `log_name` varchar(255) DEFAULT NULL COMMENT '物流公司名字',
  `end_time` int(10) DEFAULT NULL COMMENT '物流最后更新时间',
  `user_id` int(12) DEFAULT '0' COMMENT '所属用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='快递查询--单号';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_delivery`
--

LOCK TABLES `ly_delivery` WRITE;
/*!40000 ALTER TABLE `ly_delivery` DISABLE KEYS */;
INSERT INTO `ly_delivery` VALUES (1,'斐讯K3C','debangwuliu','5716709880',1509851398,1511507658,3,'德邦物流',1509854141,1),(4,'adidas x_plr 45码','shunfeng','601174491021',1510938252,1511946865,3,'顺丰',1511052360,1);
/*!40000 ALTER TABLE `ly_delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_email_auth`
--

DROP TABLE IF EXISTS `ly_email_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_email_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `add_time` int(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件验证码';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_email_auth`
--

LOCK TABLES `ly_email_auth` WRITE;
/*!40000 ALTER TABLE `ly_email_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `ly_email_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_ip`
--

DROP TABLE IF EXISTS `ly_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_ip` (
  `id` int(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(255) DEFAULT NULL COMMENT 'ip地址',
  `info` text COMMENT 'ip对应信息',
  `add_time` int(12) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='IP地址库';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_ip`
--

LOCK TABLES `ly_ip` WRITE;
/*!40000 ALTER TABLE `ly_ip` DISABLE KEYS */;
INSERT INTO `ly_ip` VALUES (1,'113.77.81.172','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.81.172\";}',1504111817),(2,'183.42.21.165','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"广州市\";s:7:\"city_id\";s:6:\"440100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"183.42.21.165\";}',1504112445),(3,'223.104.63.235','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:14:\"223.104.63.235\";}',1504112568),(4,'116.18.229.133','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"116.18.229.133\";}',1504140861),(5,'47.90.127.34','a:13:{s:7:\"country\";s:6:\"香港\";s:10:\"country_id\";s:2:\"HK\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:21:\"香港特别行政区\";s:9:\"region_id\";s:5:\"HK_01\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:2:\"-1\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:9:\"阿里云\";s:6:\"isp_id\";s:7:\"1000323\";s:2:\"ip\";s:12:\"47.90.127.34\";}',1504161561),(6,'40.83.125.125','a:13:{s:7:\"country\";s:6:\"香港\";s:10:\"country_id\";s:2:\"HK\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:21:\"香港特别行政区\";s:9:\"region_id\";s:5:\"HK_01\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:2:\"-1\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:9:\"microsoft\";s:6:\"isp_id\";s:2:\"-1\";s:2:\"ip\";s:13:\"40.83.125.125\";}',1504161645),(7,'103.86.71.89','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华东\";s:7:\"area_id\";s:6:\"300000\";s:6:\"region\";s:9:\"上海市\";s:9:\"region_id\";s:6:\"310000\";s:4:\"city\";s:9:\"上海市\";s:7:\"city_id\";s:6:\"310100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:0:\"\";s:6:\"isp_id\";s:2:\"-1\";s:2:\"ip\";s:12:\"103.86.71.89\";}',1504161728),(8,'123.103.252.54','a:13:{s:7:\"country\";s:6:\"香港\";s:10:\"country_id\";s:2:\"HK\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:21:\"香港特别行政区\";s:9:\"region_id\";s:5:\"HK_01\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:2:\"-1\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:0:\"\";s:6:\"isp_id\";s:2:\"-1\";s:2:\"ip\";s:14:\"123.103.252.54\";}',1504161763),(9,'113.78.15.31','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:12:\"113.78.15.31\";}',1504161945),(10,'203.114.75.84','a:13:{s:7:\"country\";s:9:\"菲律宾\";s:10:\"country_id\";s:2:\"PH\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:0:\"\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:0:\"\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:0:\"\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:0:\"\";s:3:\"isp\";s:0:\"\";s:6:\"isp_id\";s:0:\"\";s:2:\"ip\";s:13:\"203.114.75.84\";}',1504162208),(11,'104.194.206.171','a:13:{s:7:\"country\";s:6:\"美国\";s:10:\"country_id\";s:2:\"US\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:0:\"\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:0:\"\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:0:\"\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:0:\"\";s:3:\"isp\";s:0:\"\";s:6:\"isp_id\";s:0:\"\";s:2:\"ip\";s:15:\"104.194.206.171\";}',1504162224),(12,'223.104.63.229','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:14:\"223.104.63.229\";}',1504163202),(13,'113.77.83.155','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.83.155\";}',1504185481),(14,'127.0.0.1','a:13:{s:7:\"country\";s:23:\"未分配或者内网IP\";s:10:\"country_id\";s:4:\"IANA\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:2:\"-1\";s:4:\"city\";s:8:\"内网IP\";s:7:\"city_id\";s:5:\"local\";s:6:\"county\";s:8:\"内网IP\";s:9:\"county_id\";s:5:\"local\";s:3:\"isp\";s:8:\"内网IP\";s:6:\"isp_id\";s:5:\"local\";s:2:\"ip\";s:9:\"127.0.0.1\";}',1504369694),(15,'10.0.0.10','a:13:{s:7:\"country\";s:23:\"未分配或者内网IP\";s:10:\"country_id\";s:4:\"IANA\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:2:\"-1\";s:4:\"city\";s:8:\"内网IP\";s:7:\"city_id\";s:5:\"local\";s:6:\"county\";s:8:\"内网IP\";s:9:\"county_id\";s:5:\"local\";s:3:\"isp\";s:8:\"内网IP\";s:6:\"isp_id\";s:5:\"local\";s:2:\"ip\";s:9:\"10.0.0.10\";}',1504420502),(16,'10.0.0.5','a:13:{s:7:\"country\";s:23:\"未分配或者内网IP\";s:10:\"country_id\";s:4:\"IANA\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:2:\"-1\";s:4:\"city\";s:8:\"内网IP\";s:7:\"city_id\";s:5:\"local\";s:6:\"county\";s:8:\"内网IP\";s:9:\"county_id\";s:5:\"local\";s:3:\"isp\";s:8:\"内网IP\";s:6:\"isp_id\";s:5:\"local\";s:2:\"ip\";s:8:\"10.0.0.5\";}',1504449599),(17,'113.77.80.150','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.80.150\";}',1504878419),(18,'113.77.81.65','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:12:\"113.77.81.65\";}',1505001956),(19,'113.77.81.188','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.81.188\";}',1505829370),(20,'113.77.83.132','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.83.132\";}',1506093460),(21,'183.49.252.242','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"183.49.252.242\";}',1506249167),(22,'116.18.228.48','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"116.18.228.48\";}',1506310285),(23,'183.53.107.223','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"183.53.107.223\";}',1506435407),(24,'183.53.106.67','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"183.53.106.67\";}',1506607638),(25,'113.77.81.13','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:12:\"113.77.81.13\";}',1506780790),(26,'14.16.136.120','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"广州市\";s:7:\"city_id\";s:6:\"440100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"14.16.136.120\";}',1506781841),(27,'120.239.123.122','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"云浮市\";s:7:\"city_id\";s:6:\"445300\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:15:\"120.239.123.122\";}',1506854869),(28,'183.53.106.41','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"183.53.106.41\";}',1506924377),(29,'183.53.107.214','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"183.53.107.214\";}',1507027601),(30,'223.104.63.254','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:14:\"223.104.63.254\";}',1507027954),(31,'14.24.93.188','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"广州市\";s:7:\"city_id\";s:6:\"440100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:12:\"14.24.93.188\";}',1507028323),(32,'113.109.54.104','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"广州市\";s:7:\"city_id\";s:6:\"440100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"113.109.54.104\";}',1507088395),(33,'120.239.123.20','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"云浮市\";s:7:\"city_id\";s:6:\"445300\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:14:\"120.239.123.20\";}',1507091892),(34,'113.77.81.222','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.81.222\";}',1507648271),(35,'116.18.229.130','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"116.18.229.130\";}',1507695190),(36,'113.77.81.221','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.81.221\";}',1508078223),(37,'113.78.65.168','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.78.65.168\";}',1508133709),(38,'113.78.66.229','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.78.66.229\";}',1508134249),(39,'183.49.253.156','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"183.49.253.156\";}',1508161826),(40,'14.25.253.172','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"广州市\";s:7:\"city_id\";s:6:\"440100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"14.25.253.172\";}',1508238565),(41,'183.49.252.113','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"183.49.252.113\";}',1508334373),(42,'14.25.40.16','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"广州市\";s:7:\"city_id\";s:6:\"440100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:11:\"14.25.40.16\";}',1508370092),(43,'113.78.66.136','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.78.66.136\";}',1508458862),(44,'14.30.116.93','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"广州市\";s:7:\"city_id\";s:6:\"440100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:12:\"14.30.116.93\";}',1508464279),(45,'14.217.200.243','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"14.217.200.243\";}',1508464304),(46,'113.78.67.215','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.78.67.215\";}',1508481691),(47,'183.53.106.70','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"183.53.106.70\";}',1508505246),(48,'183.53.106.225','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"183.53.106.225\";}',1508678790),(49,'116.18.229.199','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"116.18.229.199\";}',1508738127),(50,'113.78.14.138','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.78.14.138\";}',1508808199),(51,'117.136.31.220','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:2:\"-1\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:14:\"117.136.31.220\";}',1508808966),(52,'116.18.229.215','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"116.18.229.215\";}',1508817320),(53,'120.239.123.100','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"云浮市\";s:7:\"city_id\";s:6:\"445300\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:15:\"120.239.123.100\";}',1508931054),(54,'14.25.53.22','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"广州市\";s:7:\"city_id\";s:6:\"440100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:11:\"14.25.53.22\";}',1508983700),(55,'14.217.202.238','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"14.217.202.238\";}',1508983833),(56,'183.49.252.221','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"183.49.252.221\";}',1509248020),(57,'183.53.106.73','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"183.53.106.73\";}',1509851348),(58,'113.77.80.45','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:12:\"113.77.80.45\";}',1510938078),(59,'113.77.82.29','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:12:\"113.77.82.29\";}',1511061014),(60,'223.104.63.233','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:14:\"223.104.63.233\";}',1511101957),(61,'116.18.228.139','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"116.18.228.139\";}',1511235424),(62,'116.18.229.20','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"116.18.229.20\";}',1511249105),(63,'223.104.64.16','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:13:\"223.104.64.16\";}',1511445988),(64,'175.11.90.197','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华中\";s:7:\"area_id\";s:6:\"400000\";s:6:\"region\";s:9:\"湖南省\";s:9:\"region_id\";s:6:\"430000\";s:4:\"city\";s:9:\"长沙市\";s:7:\"city_id\";s:6:\"430100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"175.11.90.197\";}',1511488861),(65,'101.226.66.181','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华东\";s:7:\"area_id\";s:6:\"300000\";s:6:\"region\";s:9:\"上海市\";s:9:\"region_id\";s:6:\"310000\";s:4:\"city\";s:9:\"上海市\";s:7:\"city_id\";s:6:\"310100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"101.226.66.181\";}',1511489530),(66,'120.239.123.53','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"云浮市\";s:7:\"city_id\";s:6:\"445300\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:14:\"120.239.123.53\";}',1511515535),(67,'116.18.229.108','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"116.18.229.108\";}',1511917078),(68,'183.53.106.51','a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"183.53.106.51\";}',1511964824);
/*!40000 ALTER TABLE `ly_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_logistics_company`
--

DROP TABLE IF EXISTS `ly_logistics_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_logistics_company` (
  `id` int(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) DEFAULT '' COMMENT '快递公司名称',
  `code` varchar(255) DEFAULT '' COMMENT '快递公司代码 ',
  `url` varchar(255) DEFAULT '' COMMENT '快递公司网址',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `state` int(1) DEFAULT '0' COMMENT '状态',
  `sort` int(64) DEFAULT '0' COMMENT '排序',
  `used` int(255) DEFAULT '0' COMMENT '使用次数',
  `memo` varchar(255) DEFAULT NULL COMMENT '快递公司说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8 COMMENT='物流公司';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_logistics_company`
--

LOCK TABLES `ly_logistics_company` WRITE;
/*!40000 ALTER TABLE `ly_logistics_company` DISABLE KEYS */;
INSERT INTO `ly_logistics_company` VALUES (1,'澳大利亚邮政(英文结果）','auspost','',1502764220,1,0,0,'澳大利亚邮政(英文结果）'),(2,'AAE','aae','',1502764220,1,0,0,'AAE'),(3,'安信达','anxindakuaixi','',1502764220,1,0,0,'安信达'),(4,'汇通快运','huitongkuaidi','',1502764220,1,0,0,'汇通快运'),(5,'百福东方','baifudongfang','',1502764220,1,0,0,'百福东方'),(6,'BHT','bht','',1502764220,1,0,0,'BHT'),(7,'邮政小包（国内），邮政包裹（国内）、邮政国内给据（国内）','youzhengguonei','',1502764220,1,0,0,'邮政小包（国内），邮政包裹（国内）、邮政国内给据（国内）'),(8,'邦送物流','bangsongwuliu','',1502764220,1,0,0,'邦送物流'),(9,'希伊艾斯（CCES）','cces','',1502764220,1,0,0,'希伊艾斯（CCES）'),(10,'中国东方（COE）','coe','',1502764220,1,0,0,'中国东方（COE）'),(11,'传喜物流','chuanxiwuliu','',1502764220,1,0,0,'传喜物流'),(12,'加拿大邮政Canada Post（英文结果）','canpost','',1502764220,1,0,0,'加拿大邮政Canada Post（英文结果）'),(13,'加拿大邮政Canada Post(德文结果）','canpostfr','',1502764220,1,0,0,'加拿大邮政Canada Post(德文结果）'),(14,'大田物流','datianwuliu','',1502764220,1,0,0,'大田物流'),(15,'德邦物流','debangwuliu','',1502764220,1,0,1,'德邦物流'),(16,'DPEX','dpex','',1502764220,1,0,0,'DPEX'),(17,'DHL-中国件-中文结果','dhl','',1502764220,1,0,0,'DHL-中国件-中文结果'),(18,'DHL-国际件-英文结果','dhlen','',1502764220,1,0,0,'DHL-国际件-英文结果'),(19,'DHL-德国件-德文结果（德国国内派、收的件）','dhlde','',1502764220,1,0,0,'DHL-德国件-德文结果（德国国内派、收的件）'),(20,'D速快递','dsukuaidi','',1502764220,1,0,0,'D速快递'),(21,'递四方','disifang','',1502764220,1,0,0,'递四方'),(22,'E邮宝','ems','',1502764220,1,0,0,'E邮宝'),(23,'EMS（英文结果）','emsen','',1502764220,1,0,0,'EMS（英文结果）'),(24,'EMS-（中国-国际）件-中文结果/EMS-(China-International）-Chinese data','emsguoji','',1502764220,1,0,0,'EMS-（中国-国际）件-中文结果/EMS-(China-International）-Chinese data'),(25,'EMS-（中国-国际）件-英文结果/EMS-(China-International）-Englilsh data','emsinten','',1502764220,1,0,0,'EMS-（中国-国际）件-英文结果/EMS-(China-International）-Englilsh data'),(26,'Fedex-国际件-英文结果（说明：Fedex是国际件的英文结果，Fedex中国的请用“lianbangkuaidi”，Fedex-美国请用fedexus）','fedex','',1502764220,1,0,0,'Fedex-国际件-英文结果（说明：Fedex是国际件的英文结果，Fedex中国的请用“lianbangkuaidi”，Fedex-美国请用fedexus）'),(27,'Fedex-国际件-中文结果','fedexcn','',1502764220,1,0,0,'Fedex-国际件-中文结果'),(28,'Fedex-美国件-英文结果(说明：如果无效，请偿试使用fedex）','fedexus','',1502764220,1,0,0,'Fedex-美国件-英文结果(说明：如果无效，请偿试使用fedex）'),(29,'飞康达物流','feikangda','',1502764220,1,0,0,'飞康达物流'),(30,'飞快达','feikuaida','',1502764220,1,0,0,'飞快达'),(31,'如风达快递','rufengda','',1502764220,1,0,0,'如风达快递'),(32,'风行天下','fengxingtianxia','',1502764220,1,0,0,'风行天下'),(33,'飞豹快递','feibaokuaidi','',1502764220,1,0,0,'飞豹快递'),(34,'港中能达','ganzhongnengda','',1502764220,1,0,0,'港中能达'),(35,'国通快递','guotongkuaidi','',1502764220,1,0,0,'国通快递'),(36,'广东邮政','guangdongyouzhengwuliu','',1502764220,1,0,0,'广东邮政'),(37,'邮政小包（国际），邮政包裹（国际）、邮政国内给据（国际）','youzhengguoji','',1502764220,1,0,0,'邮政小包（国际），邮政包裹（国际）、邮政国内给据（国际）'),(38,'GLS','gls','',1502764220,1,0,0,'GLS'),(39,'共速达','gongsuda','',1502764220,1,0,0,'共速达'),(40,'汇强快递','huiqiangkuaidi','',1502764220,1,0,0,'汇强快递'),(41,'天地华宇','tiandihuayu','',1502764220,1,0,0,'天地华宇'),(42,'恒路物流','hengluwuliu','',1502764220,1,0,0,'恒路物流'),(43,'华夏龙','huaxialongwuliu','',1502764220,1,0,0,'华夏龙'),(44,'天天快递','tiantian','',1502764220,1,0,0,'天天快递'),(45,'海外环球','haiwaihuanqiu','',1502764220,1,0,0,'海外环球'),(46,'河北建华','hebeijianhua','',1502764220,1,0,0,'河北建华（暂只能查好乐买的单，其他商家要查，请发邮件至 wensheng_chen#kingdee.com(将#替换成@)开通权限'),(47,'海盟速递','haimengsudi','',1502764220,1,0,0,'海盟速递'),(48,'华企快运','huaqikuaiyun','',1502764220,1,0,0,'华企快运'),(49,'山东海红','haihongwangsong','',1502764220,1,0,0,'山东海红'),(50,'佳吉物流','jiajiwuliu','',1502764220,1,0,0,'佳吉物流'),(51,'佳怡物流','jiayiwuliu','',1502764220,1,0,0,'佳怡物流'),(52,'加运美','jiayunmeiwuliu','',1502764220,1,0,0,'加运美'),(53,'京广速递','jinguangsudikuaijian','',1502764220,1,0,0,'京广速递'),(54,'急先达','jixianda','',1502764220,1,0,0,'急先达'),(55,'晋越快递','jinyuekuaidi','',1502764220,1,0,0,'晋越快递'),(56,'捷特快递','jietekuaidi','',1502764220,1,0,0,'捷特快递'),(57,'金大物流','jindawuliu','',1502764220,1,0,0,'金大物流'),(58,'嘉里大通','jialidatong','',1502764220,1,0,0,'嘉里大通'),(59,'快捷速递','kuaijiesudi','',1502764220,1,0,0,'快捷速递'),(60,'康力物流','kangliwuliu','',1502764220,1,0,0,'康力物流'),(61,'跨越物流','kuayue','',1502764220,1,0,0,'跨越物流'),(62,'联昊通','lianhaowuliu','',1502764220,1,0,0,'联昊通'),(63,'龙邦物流','longbanwuliu','',1502764220,1,0,0,'龙邦物流'),(64,'蓝镖快递','lanbiaokuaidi','',1502764220,1,0,0,'蓝镖快递'),(65,'乐捷递','lejiedi','',1502764220,1,0,0,'乐捷递（暂只能查好乐买的单，其他商家要查，请发邮件至 wensheng_chen#kingdee.com(将#替换成@)开通权限'),(66,'联邦快递（Fedex-中国-中文结果）（说明：国外的请用 fedex）','lianbangkuaidi','',1502764220,1,0,0,'联邦快递（Fedex-中国-中文结果）（说明：国外的请用 fedex）'),(67,'联邦快递(Fedex-中国-英文结果）','lianbangkuaidien','',1502764220,1,0,0,'联邦快递(Fedex-中国-英文结果）'),(68,'立即送','lijisong','',1502764220,1,0,0,'立即送（暂只能查好乐买的单，其他商家要查，请发邮件至 wensheng_chen#kingdee.com(将#替换成@)开通权限)'),(69,'隆浪快递','longlangkuaidi','',1502764220,1,0,0,'隆浪快递'),(70,'门对门','menduimen','',1502764220,1,0,0,'门对门'),(71,'美国快递','meiguokuaidi','',1502764220,1,0,0,'美国快递'),(72,'明亮物流','mingliangwuliu','',1502764220,1,0,0,'明亮物流'),(73,'OCS','ocs','',1502764220,1,0,0,'OCS'),(74,'onTrac','ontrac','',1502764220,1,0,0,'onTrac'),(75,'全晨快递','quanchenkuaidi','',1502764220,1,0,0,'全晨快递'),(76,'全际通','quanjitong','',1502764220,1,0,0,'全际通'),(77,'全日通','quanritongkuaidi','',1502764220,1,0,0,'全日通'),(78,'全一快递','quanyikuaidi','',1502764220,1,0,0,'全一快递'),(79,'全峰快递','quanfengkuaidi','',1502764220,1,0,0,'全峰快递'),(80,'七天连锁','sevendays','',1502764220,1,0,0,'七天连锁'),(81,'申通','shentong','',1502764220,1,0,3,'申通'),(82,'顺丰','shunfeng','',1502764220,1,0,4,'顺丰'),(83,'顺丰（英文结果）','shunfengen','',1502764220,1,0,0,'顺丰（英文结果）'),(84,'三态速递','santaisudi','',1502764220,1,0,0,'三态速递'),(85,'盛辉物流','shenghuiwuliu','',1502764220,1,0,0,'盛辉物流'),(86,'速尔物流','suer','',1502764220,1,0,0,'速尔物流'),(87,'盛丰物流','shengfengwuliu','',1502764220,1,0,0,'盛丰物流'),(88,'上大物流','shangda','',1502764220,1,0,0,'上大物流'),(89,'赛澳递','saiaodi','',1502764220,1,0,0,'赛澳递'),(90,'山西红马甲','sxhongmajia','',1502764220,1,0,0,'山西红马甲'),(91,'圣安物流','shenganwuliu','',1502764220,1,0,0,'圣安物流'),(92,'穗佳物流','suijiawuliu','',1502764220,1,0,0,'穗佳物流'),(93,'TNT（中文结果）','tnt','',1502764220,1,0,0,'TNT（中文结果）'),(94,'TNT（英文结果）','tnten','',1502764220,1,0,0,'TNT（英文结果）'),(95,'通和天下','tonghetianxia','',1502764220,1,0,0,'通和天下'),(96,'UPS（中文结果）','ups','',1502764220,1,0,0,'UPS（中文结果）'),(97,'UPS（英文结果）','upsen','',1502764220,1,0,0,'UPS（英文结果）'),(98,'优速物流','youshuwuliu','',1502764220,1,0,0,'优速物流'),(99,'USPS（中英文）','usps','',1502764220,1,0,0,'USPS（中英文）'),(100,'万家物流','wanjiawuliu','',1502764220,1,0,0,'万家物流'),(101,'万象物流','wanxiangwuliu','',1502764220,1,0,0,'万象物流'),(102,'微特派','weitepai','',1502764220,1,0,0,'微特派'),(103,'新邦物流','xinbangwuliu','',1502764220,1,0,0,'新邦物流'),(104,'信丰物流','xinfengwuliu','',1502764220,1,0,0,'信丰物流'),(105,'新蛋奥硕物流','neweggozzo','',1502764220,1,0,0,'新蛋奥硕物流'),(106,'香港邮政','hkpost','',1502764220,1,0,0,'香港邮政'),(107,'圆通速递','yuantong','',1502764220,1,0,0,'圆通速递'),(108,'韵达快运','yunda','',1502764220,1,0,0,'韵达快运'),(109,'运通快递','yuntongkuaidi','',1502764220,1,0,0,'运通快递'),(110,'远成物流','yuanchengwuliu','',1502764220,1,0,0,'远成物流'),(111,'亚风速递','yafengsudi','',1502764220,1,0,0,'亚风速递'),(112,'一邦速递','yibangwuliu','',1502764220,1,0,0,'一邦速递'),(113,'源伟丰快递','yuanweifeng','',1502764220,1,0,0,'源伟丰快递'),(114,'元智捷诚','yuanzhijiecheng','',1502764220,1,0,0,'元智捷诚'),(115,'越丰物流','yuefengwuliu','',1502764220,1,0,0,'越丰物流'),(116,'源安达','yuananda','',1502764220,1,0,0,'源安达'),(117,'原飞航','yuanfeihangwuliu','',1502764220,1,0,0,'原飞航'),(118,'忠信达','zhongxinda','',1502764220,1,0,0,'忠信达'),(119,'芝麻开门','zhimakaimen','',1502764220,1,0,0,'芝麻开门'),(120,'银捷速递','yinjiesudi','',1502764220,1,0,0,'银捷速递'),(121,'一统飞鸿','yitongfeihong','',1502764220,1,0,0,'一统飞鸿'),(122,'中通速递','zhongtong','',1502764220,1,0,0,'中通速递'),(123,'宅急送','zhaijisong','',1502764220,1,0,0,'宅急送'),(124,'中邮物流','zhongyouwuliu','',1502764220,1,0,0,'中邮物流'),(125,'中速快件','zhongsukuaidi','',1502764220,1,0,0,'中速快件'),(126,'郑州建华','zhengzhoujianhua','',1502764220,1,0,0,'郑州建华'),(127,'中天万运','zhongtianwanyun','',1502764220,1,0,0,'中天万运');
/*!40000 ALTER TABLE `ly_logistics_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_reply`
--

DROP TABLE IF EXISTS `ly_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `topic_id` int(10) NOT NULL DEFAULT '0',
  `reply_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `reply_name` varchar(32) NOT NULL,
  `reply_email` varchar(100) NOT NULL,
  `reply_detail` text NOT NULL,
  `reply_pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回复帖子';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_reply`
--

LOCK TABLES `ly_reply` WRITE;
/*!40000 ALTER TABLE `ly_reply` DISABLE KEYS */;
/*!40000 ALTER TABLE `ly_reply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_sms_auth`
--

DROP TABLE IF EXISTS `ly_sms_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_sms_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `add_time` int(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机验证码';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_sms_auth`
--

LOCK TABLES `ly_sms_auth` WRITE;
/*!40000 ALTER TABLE `ly_sms_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `ly_sms_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_tieba`
--

DROP TABLE IF EXISTS `ly_tieba`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_tieba` (
  `id` int(30) unsigned NOT NULL AUTO_INCREMENT COMMENT '递增ID',
  `user_id` int(30) unsigned NOT NULL DEFAULT '0' COMMENT 'user表ID',
  `baidu_id` int(30) unsigned NOT NULL DEFAULT '0' COMMENT 'baiduid表ID',
  `fid` int(30) unsigned NOT NULL DEFAULT '0' COMMENT '贴吧ID',
  `tieba` text NOT NULL COMMENT '贴吧名',
  `no` tinyint(1) NOT NULL DEFAULT '0' COMMENT '忽略签到 0 否 1忽略',
  `status` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `latest` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后签到时间',
  `last_error` text COMMENT '最近一次签到错误',
  `refresh_time` int(11) DEFAULT '0' COMMENT '刷新列表时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`) USING BTREE,
  KEY `latest` (`latest`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8 COMMENT='贴吧列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_tieba`
--

LOCK TABLES `ly_tieba` WRITE;
/*!40000 ALTER TABLE `ly_tieba` DISABLE KEYS */;
INSERT INTO `ly_tieba` VALUES (1,1,1,797510,'广州南洋理工职业学院',0,0,1511971205,'',1506836208),(2,1,1,113893,'显卡',0,0,1511971245,'',1506836208),(3,1,1,59099,'李毅',0,0,1511971208,'',1506836208),(4,1,1,531162,'hosts',0,0,1511971255,'',1506836208),(5,1,1,52,'笔记本',0,0,1511971210,'',1506836208),(6,1,1,1483669,'英伟达',0,0,1511971230,'',1506836208),(7,1,1,1364185,'小尾巴',0,0,1511971244,'',1506836208),(8,1,1,693735,'java',0,0,1511971225,'',1506836208),(9,1,1,22545,'c语言',0,0,1511971253,'',1506836208),(10,1,1,254,'编程',0,0,1511971230,'',1506836208),(11,1,1,20975,'c++',0,0,1511971236,'',1506836208),(12,1,1,74075,'图拉丁',0,0,1511971254,'',1506836208),(13,1,1,301,'电脑',0,0,1511971241,'',1506836208),(14,1,1,1665281,'chh',0,0,1511971256,'',1506836208),(15,1,1,672678,'gta5',0,0,1511971219,'',1506836208),(16,1,1,93837,'qq表情',0,0,1511971236,'',1506836208),(17,1,1,294436,'origin',0,0,1511971226,'',1506836208),(18,1,1,707597,'steam',0,0,1511971249,'',1506836208),(19,1,1,2171907,'origin跳蚤市场',0,0,1511971239,'',1506836208),(20,1,1,11380043,'京东白条',0,0,1511971250,'',1506836208),(21,1,1,5407104,'废铁战士',0,0,1511971220,'',1506836208),(22,1,1,1075526,'wp7吧福利区',0,0,1511971218,'',1506836208),(23,1,1,20912461,'titech科技',0,0,1511971222,'',1506836208),(24,1,1,21320127,'福利手机',0,0,1511971238,'',1506836208),(25,1,1,2479109,'wp7',0,0,1511971204,'',1506836208),(26,1,1,4420,'amd',0,0,1511971215,'',1506836208),(27,1,1,12334,'二手手机',0,0,1511971217,'',1506836208),(28,1,1,14539,'二手电脑',0,0,1511971211,'',1506836208),(29,1,1,2874560,'steam交易',0,0,1511971259,'',1506836208),(30,1,1,6920358,'wp7吧生活区',0,0,1511971257,'',1506836208),(31,1,1,3332958,'树莓派',0,0,1511971252,'',1506836208),(32,1,1,1996261,'固态硬盘',0,0,1511971231,'',1506836208),(33,1,1,87190,'鼠标',0,0,1511971253,'',1506836208),(34,1,1,1937070,'机械键盘',0,0,1511971207,'',1506836208),(35,1,1,69406,'显示器',0,0,1511971217,'',1506836208),(36,1,1,21683,'电源',0,0,1511971232,'',1506836208),(37,1,1,11775,'耳机',0,0,1511971228,'',1506836208),(38,1,1,143906,'手表',0,0,1511971260,'',1506836208),(39,1,1,294965,'粤语歌',0,0,1511971239,'',1506836208),(40,1,1,24,'美女',0,0,1511971203,'',1506836208),(107,2,3,487842,'起义时刻',0,0,1511971247,'',1506855065),(108,2,3,22393,'红警4',0,0,1511971206,'',1506855065),(109,2,3,1390031,'黑道圣徒',0,0,1511971241,'',1506855065),(110,2,3,2299583,'一只凹凸曼',0,0,1511971246,'',1506855065),(111,2,3,221386,'p970',0,0,1511971238,'',1506855065),(112,2,3,1445119,'wii模拟器',0,0,1511971233,'',1506855065),(113,2,3,315054,'蔡朝焜纪念中学',0,0,1511971229,'',1506855065),(114,2,3,2589571,'手机三国杀',0,0,1511971256,'',1506855065),(115,2,3,1627732,'dota2',0,0,1511971243,'',1506855065),(116,2,3,572738,'剑灵',0,0,1511971234,'',1506855065),(117,2,3,591984,'上古卷轴',0,0,1511971211,'',1506855065),(118,2,3,113893,'显卡',0,0,1511971242,'',1506855065),(119,2,3,149407,'真三国无双',0,0,1511971242,'',1506855065),(120,2,3,275430,'战国basara',0,0,1511971213,'',1506855065),(121,2,3,707597,'steam',0,0,1511971249,'',1506855065),(122,2,3,1435086,'战地3',0,0,1511971226,'',1506855065),(123,2,3,654982,'开罗游戏',0,0,1511971248,'',1506855065),(124,2,3,4797599,'lmsslkm',0,0,1511971220,'',1506855065),(125,2,3,192813,'战国无双',0,0,1511971215,'',1506855065),(126,2,3,16591,'侠盗猎车',0,0,1511971214,'',1506855065),(127,2,3,3652002,'华为荣耀6',0,0,1511971225,'',1506855065),(128,2,3,447233,'郑州一中',0,0,1511971252,'',1506855065),(129,2,3,3735514,'看门狗',0,0,1511971257,'',1506855065),(130,2,3,797510,'广州南洋理工职业学院',0,0,1511971209,'',1506855065),(131,2,3,3186007,'300英雄',0,0,1511971212,'',1506855065),(132,2,3,153,'手机',0,0,1511971210,'',1506855065),(133,2,3,294436,'origin',0,0,1511971235,'',1506855065),(134,2,3,6796540,'ppsspp',0,0,1511971224,'',1506855065),(135,2,3,195166,'planetside',0,0,1511971245,'',1506855065),(136,2,3,3056531,'csgo',0,0,1511971224,'',1506855065),(137,2,3,4420,'amd',0,0,1511971258,'',1506855065),(138,2,3,2432903,'minecraft',0,0,1511971240,'',1506855065),(139,2,3,130154,'神舟笔记本',0,0,1511971261,'',1506855065),(140,2,3,1280437,'刺客教条',0,0,1511971234,'',1506855065),(141,2,3,672678,'gta5',0,0,1511971218,'',1506855065),(142,2,3,6500558,'花园战争',0,0,1511971209,'',1506855065),(143,2,3,290139,'孤岛惊魂',0,0,1511971248,'',1506855065),(144,2,3,12342493,'h1z1',0,0,1511971235,'',1506855065),(145,2,3,719397,'战地4',1,340006,1507089612,'贴吧目录出问题啦，请到贴吧签到吧反馈',1506855065),(146,2,3,4311039,'泰坦陨落',0,0,1511971260,'',1506855065),(147,2,3,6108188,'steam跳蚤市场',0,0,1511971219,'',1506855065),(148,2,3,11391925,'讨鬼传极',0,0,1511971237,'',1506855065),(149,2,3,310030,'彩虹六号',0,0,1511971206,'',1506855065),(150,2,3,15206017,'g2a',0,0,1511971204,'',1506855065),(151,2,3,137559,'arma3',0,0,1511971255,'',1506855065),(152,2,3,1935265,'辐射4',0,0,1511971202,'',1506855065),(153,2,3,1601022,'求生之路',0,0,1511971223,'',1506855065),(154,2,3,18954774,'荣耀战魂',0,0,1511971223,'',1506855065),(155,2,3,4563989,'全境封锁',0,0,1511971214,'',1506855065),(156,2,3,4367294,'uplay',0,0,1511971212,'',1506855065),(157,2,3,2874560,'steam交易',0,0,1511971246,'',1506855065),(158,2,3,11715943,'花园战争2',0,0,1511971244,'',1506855065),(159,2,3,14359,'研究生',0,0,1511971228,'',1506855065),(160,2,3,841138,'lol日服',0,0,1511971216,'',1506855065),(161,2,3,392359,'进化',0,0,1511971227,'',1506855065),(162,2,3,3958782,'coh2',0,0,1511971259,'',1506855065),(163,2,3,19444911,'lspdfr',0,0,1511971231,'',1506855065),(164,2,3,216660,'dnf美服',0,0,1511971233,'',1506855065),(165,2,3,14216453,'fgo',0,0,1511971250,'',1506855065),(166,2,3,20598711,'fgo国服',0,0,1511971251,'',1506855065),(167,2,3,4494816,'黑暗之魂3',0,0,1511971243,'',1506855065),(168,2,3,21786560,'fgo日服',0,0,1511971262,'',1506855065),(169,2,3,22353267,'碧蓝航线',0,0,1511971265,'',1506855065),(170,2,3,2208085,'paragon',0,0,1511971263,'',1506855065),(171,2,3,18559089,'命运冠位指定',0,0,1511971264,'',1506855065),(172,2,3,2265748,'bilibili',0,0,1511971264,'',1506855065),(173,2,3,12091193,'darkestdungeon',0,0,1511971262,'',1508931186),(174,2,3,862664,'巫师3',0,0,1511971265,'',1508931186);
/*!40000 ALTER TABLE `ly_tieba` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_topic`
--

DROP TABLE IF EXISTS `ly_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_topic` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `class_id` int(10) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  `pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(10) DEFAULT '0',
  `name` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  `add_time` int(11) NOT NULL DEFAULT '0',
  `view` int(10) NOT NULL DEFAULT '0',
  `reply` int(10) NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='帖子表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_topic`
--

LOCK TABLES `ly_topic` WRITE;
/*!40000 ALTER TABLE `ly_topic` DISABLE KEYS */;
INSERT INTO `ly_topic` VALUES (1,1,'贴吧签到站已完成','登陆后…右上角有进入贴吧签到站的入口','',1,'hm081333','522751485@qq.com',1506865098,10,0,0),(2,1,'尝试新上传图片代码','上传testing...','/upload/pics/201710/1506865551.PNG',1,'hm081333','522751485@qq.com',1506865551,6,0,0),(3,1,'上传 图片','PC上传图片','/upload/pics/201710/1506865557.JPG',1,'hm081333','522751485@qq.com',1506865557,13,0,0);
/*!40000 ALTER TABLE `ly_topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ly_user`
--

DROP TABLE IF EXISTS `ly_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ly_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_name` varchar(32) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `real_name` varchar(50) NOT NULL COMMENT '真实姓名',
  `auth` int(1) NOT NULL DEFAULT '0' COMMENT '权限',
  `reg_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `birth_time` int(11) NOT NULL DEFAULT '0' COMMENT '生日',
  `a_pwd` varchar(255) NOT NULL,
  `open_id` varchar(255) NOT NULL COMMENT '微信唯一ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='会员列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ly_user`
--

LOCK TABLES `ly_user` WRITE;
/*!40000 ALTER TABLE `ly_user` DISABLE KEYS */;
INSERT INTO `ly_user` VALUES (1,'hm081333','$2y$10$Yv7v1KVBj0eJPrtFvkxbQuKtbCcyXrEXHp.sHh/4NIdh8WgJNsLk2','522751485@qq.com','何朗义',0,1506835779,845136000,'YVJZTjh1aDQ=','oYtVv1CoGhTWLk9jlTzj7rS4-CpY'),(2,'jj7596820','$2y$10$Pk6h9Bp/4Ak8F3qeAF/wHOYaydW9dVA/bDL1rQbLHXPvizav8Iw4a','582466241@qq.com','LC',0,1506854948,1483272600,'WWc5YzhlbDVBdmNv','');
/*!40000 ALTER TABLE `ly_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-30 23:07:18
