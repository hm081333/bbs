/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : nyjl

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-06-02 22:16:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for forum_admin
-- ----------------------------
DROP TABLE IF EXISTS `forum_admin`;
CREATE TABLE `forum_admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_admin
-- ----------------------------
INSERT INTO `forum_admin` VALUES ('1', 'root', '$2y$10$G0yecuK.J5OEkRdKf//0oOku6dIAD5ys/Y0lnBfLNjYACsFrMZLxm', '1');
INSERT INTO `forum_admin` VALUES ('2', 'test', '$2y$10$ycAX52.TI4svrq2gO8CuZOjPp.OvGx8g6VHWJz8imLupL38QCNiqK', '1');
INSERT INTO `forum_admin` VALUES ('3', 'empty', '$2y$10$d4uOFrCTN0LfTyIuChNEOO3a9uSS7SsjDa2LgoNwE6y4Mj6bGuNNC', '0');

-- ----------------------------
-- Table structure for forum_class
-- ----------------------------
DROP TABLE IF EXISTS `forum_class`;
CREATE TABLE `forum_class` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tips` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_class
-- ----------------------------
INSERT INTO `forum_class` VALUES ('1', '闲聊', '随意灌水区');
INSERT INTO `forum_class` VALUES ('2', 'PHP', 'PHP是世界上最好的语言');
INSERT INTO `forum_class` VALUES ('3', 'JavaScript', 'JS是微信最常用的语言');
INSERT INTO `forum_class` VALUES ('4', 'Photoshop', '');
INSERT INTO `forum_class` VALUES ('5', 'Flash', '');
INSERT INTO `forum_class` VALUES ('6', 'C程序设计', '');
INSERT INTO `forum_class` VALUES ('7', 'MySQL数据库', '');
INSERT INTO `forum_class` VALUES ('8', '网页设计', '');
INSERT INTO `forum_class` VALUES ('9', '网络营销', '');
INSERT INTO `forum_class` VALUES ('10', '计算机网络基础', '');
INSERT INTO `forum_class` VALUES ('11', 'Illustrator平面设计', '');
INSERT INTO `forum_class` VALUES ('12', 'Linux网络操作系统', '');
INSERT INTO `forum_class` VALUES ('13', 'ASP.NET', '');
INSERT INTO `forum_class` VALUES ('14', 'Android应用开发', '');
INSERT INTO `forum_class` VALUES ('16', 'PhalApi', 'PhalApi是一个用于快速写api接口的php框架');

-- ----------------------------
-- Table structure for forum_reply
-- ----------------------------
DROP TABLE IF EXISTS `forum_reply`;
CREATE TABLE `forum_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `topic_id` int(10) NOT NULL DEFAULT '0',
  `reply_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `reply_name` varchar(32) CHARACTER SET gbk NOT NULL,
  `reply_email` varchar(100) CHARACTER SET gbk NOT NULL,
  `reply_detail` text CHARACTER SET gbk NOT NULL,
  `reply_pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `reply_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `a_id` (`reply_id`),
  FULLTEXT KEY `reply_pics` (`reply_pics`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_reply
-- ----------------------------
INSERT INTO `forum_reply` VALUES ('68', '61', '1', null, 'admin', '', 'first test OK', '', '2016-10-12 12:57:58');
INSERT INTO `forum_reply` VALUES ('75', '61', '6', null, 'admin', '', '6', '', '2016-10-12 13:35:20');
INSERT INTO `forum_reply` VALUES ('76', '61', '7', null, 'admin', '', '7', '', '2016-10-12 13:35:24');
INSERT INTO `forum_reply` VALUES ('77', '61', '8', null, 'admin', '', '8', '', '2016-10-12 13:35:27');
INSERT INTO `forum_reply` VALUES ('78', '61', '9', null, 'admin', '', '9', '', '2016-10-12 13:35:29');
INSERT INTO `forum_reply` VALUES ('79', '61', '10', null, 'admin', '', '10', '', '2016-10-12 13:35:32');
INSERT INTO `forum_reply` VALUES ('73', '61', '4', null, 'admin', '', '第四条回复', '', '2016-10-12 13:25:00');
INSERT INTO `forum_reply` VALUES ('74', '61', '5', null, 'admin', '', '第五条回复', '', '2016-10-12 13:25:07');
INSERT INTO `forum_reply` VALUES ('72', '61', '3', null, 'admin', '', '第三条回复', '', '2016-10-12 13:24:45');
INSERT INTO `forum_reply` VALUES ('71', '61', '2', null, 'admin', '', '第二条回复', '', '2016-10-12 13:24:40');
INSERT INTO `forum_reply` VALUES ('86', '61', '11', null, 'admin', '', '123123123', '', '2016-10-13 11:50:12');
INSERT INTO `forum_reply` VALUES ('87', '61', '12', null, 'admin', '', '123123123', '', '2016-10-13 11:50:14');
INSERT INTO `forum_reply` VALUES ('88', '61', '13', null, 'admin', '', '123123123', '', '2016-10-13 11:50:17');
INSERT INTO `forum_reply` VALUES ('89', '61', '14', null, 'admin', '', '123123123', '', '2016-10-13 11:50:20');
INSERT INTO `forum_reply` VALUES ('90', '61', '15', null, 'admin', '', '123123123', '', '2016-10-13 11:50:22');
INSERT INTO `forum_reply` VALUES ('91', '61', '16', null, 'admin', '', '123123123', '', '2016-10-13 11:50:25');
INSERT INTO `forum_reply` VALUES ('94', '89', '1', null, 'admin', '', '测试成功 OK！', '', '2016-10-13 22:45:32');
INSERT INTO `forum_reply` VALUES ('95', '89', '2', null, 'admin', '', '123', '', '2016-10-13 22:51:25');
INSERT INTO `forum_reply` VALUES ('111', '104', '1', '4', 'hm081333', '522751485@qq.com', '还在改', '', '2017-05-12 01:12:15');
INSERT INTO `forum_reply` VALUES ('112', '104', '2', '4', 'hm081333', '522751485@qq.com', '插入图片试试', 'pics/1494522770.jpg', '2017-05-12 01:12:50');
INSERT INTO `forum_reply` VALUES ('113', '104', '3', '4', 'hm081333', '522751485@qq.com', '再插图', 'pics/1494523325.jpg', '2017-05-12 01:22:05');
INSERT INTO `forum_reply` VALUES ('114', '110', '1', '4', 'hm081333', '522751485@qq.com', '123', '', '2017-05-30 17:54:16');
INSERT INTO `forum_reply` VALUES ('115', '110', '2', '4', 'hm081333', '522751485@qq.com', '123', '', '2017-05-30 17:55:03');
INSERT INTO `forum_reply` VALUES ('116', '110', '3', '4', 'hm081333', '522751485@qq.com', '123', '', '2017-05-30 17:56:46');
INSERT INTO `forum_reply` VALUES ('117', '110', '4', '4', 'hm081333', '522751485@qq.com', '123', '', '2017-05-30 17:57:16');
INSERT INTO `forum_reply` VALUES ('118', '110', '5', '4', 'hm081333', '522751485@qq.com', '123', '', '2017-05-30 17:57:29');
INSERT INTO `forum_reply` VALUES ('119', '110', '6', '4', 'hm081333', '522751485@qq.com', '123', '', '2017-05-30 17:57:38');
INSERT INTO `forum_reply` VALUES ('120', '110', '7', '4', 'hm081333', '522751485@qq.com', '123', 'pics/1496138278.jpg', '2017-05-30 17:57:59');
INSERT INTO `forum_reply` VALUES ('121', '110', '8', '4', 'hm081333', '522751485@qq.com', '123', 'pics/1496138309.jpg', '2017-05-30 17:58:30');
INSERT INTO `forum_reply` VALUES ('122', '110', '9', '4', 'hm081333', '522751485@qq.com', '123', 'pics/1496138363.jpg', '2017-05-30 17:59:24');
INSERT INTO `forum_reply` VALUES ('123', '112', '1', '4', 'hm081333', '522751485@qq.com', '自己回复自己', 'pics/1496140010.png', '2017-05-30 18:26:51');

-- ----------------------------
-- Table structure for forum_topic
-- ----------------------------
DROP TABLE IF EXISTS `forum_topic`;
CREATE TABLE `forum_topic` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `class_id` int(10) NOT NULL,
  `topic` varchar(255) CHARACTER SET gbk NOT NULL,
  `detail` text CHARACTER SET gbk NOT NULL,
  `pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(10) DEFAULT '0',
  `name` varchar(32) CHARACTER SET gbk NOT NULL,
  `email` varchar(100) CHARACTER SET gbk NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `view` int(10) NOT NULL DEFAULT '0',
  `reply` int(10) NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_topic
-- ----------------------------
INSERT INTO `forum_topic` VALUES ('105', '1', '替换，使用phalapi框架作为核心', 'test123', 'pics/1496039683.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-29 14:34:43', '0', '0', '1');
INSERT INTO `forum_topic` VALUES ('68', '1', '新用户尝试发帖', '新用户尝试发帖图片内容', 'pics/1476281421.png', '34', '123', '123@123.123', '2016-10-12 22:10:21', '23', '0', '0');
INSERT INTO `forum_topic` VALUES ('89', '1', '手机尝试发图贴', '第一次尝试', 'pics/1476369877.jpeg', '34', '123', '123@123.123', '2016-10-13 22:44:37', '29', '2', '0');
INSERT INTO `forum_topic` VALUES ('113', '16', 'PhalApi框架进度', '后台创建新帖成功', 'pics/1496412596.jpg', '0', '管理员', '', '2017-06-02 22:09:57', '8', '0', '1');
INSERT INTO `forum_topic` VALUES ('61', '1', 'first test', 'first test', '', '0', 'admin', '', '2016-10-12 12:27:22', '96', '16', '0');
INSERT INTO `forum_topic` VALUES ('99', '2', '管理员第二次尝试发帖', '管理员第二次尝试发帖', 'pics/1477319322.jpg', '1', '管理员', '', '2016-10-24 22:28:42', '3', '0', '1');
INSERT INTO `forum_topic` VALUES ('95', '2', '123', '123', '', '0', 'admin', '', '2016-10-24 19:04:22', '11', '0', '0');
INSERT INTO `forum_topic` VALUES ('98', '1', '管理员第一次尝试发帖', '管理员第一次尝试发帖', 'pics/1477319193.jpg', '1', '管理员', '', '2016-10-24 22:26:33', '31', '0', '1');
INSERT INTO `forum_topic` VALUES ('101', '2', '修改用户权限验证方式', '修改用户权限验证方式', 'pics/1477540278.jpg', '4', 'hm081333', '522751485@qq.com', '2016-10-27 11:51:18', '15', '0', '1');
INSERT INTO `forum_topic` VALUES ('104', '1', '更新支持php7', '2017年5月12号 凌晨1点03分写的代码', '', '4', 'hm081333', '522751485@qq.com', '2017-05-12 01:04:09', '0', '3', '1');
INSERT INTO `forum_topic` VALUES ('106', '1', '把前台转移去phalapi ing', '工程有点大。。。', 'pics/1496039732.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-29 14:35:32', '0', '0', '1');
INSERT INTO `forum_topic` VALUES ('107', '1', '把前台转移去phalapi ing', '工程有点大。。。', 'pics/1496039733.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-29 14:35:33', '18', '0', '1');
INSERT INTO `forum_topic` VALUES ('108', '1', 'phalapi尝试添加新帖', 'phalapi尝试添加新帖', 'pics/1496081698.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-30 02:16:59', '2', '0', '1');
INSERT INTO `forum_topic` VALUES ('110', '1', 'phalapi尝试添加新帖', 'phalapi尝试添加新帖', 'pics/1496081920.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-30 02:18:41', '64', '0', '1');
INSERT INTO `forum_topic` VALUES ('111', '1', 'PhalApi前台基本完工', 'PhalApi前台基本完工', 'pics/1496138646.jpg', '0', 'hm081333', '522751485@qq.com', '2017-05-30 18:04:07', '2', '0', '1');
INSERT INTO `forum_topic` VALUES ('112', '1', 'phalapi.版尝试手机上传图片', '传图片', 'pics/1496139940.png', '0', 'hm081333', '522751485@qq.com', '2017-05-30 18:25:41', '2', '0', '1');

-- ----------------------------
-- Table structure for forum_user
-- ----------------------------
DROP TABLE IF EXISTS `forum_user`;
CREATE TABLE `forum_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `real_name` varchar(50) NOT NULL,
  `auth` int(1) NOT NULL DEFAULT '0',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of forum_user
-- ----------------------------
INSERT INTO `forum_user` VALUES ('43', 'qqq', '$2y$10$q.g8ZKcIrxcCFaUFvzQctOsmV3mPYQMSmHd5A35oDoJsXJ0ClADTu', 'qqq@qqq.qqq', 'qqq', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('44', 'asd', '$2y$10$XFML6DA3Bd0g.WzsRMXjReHihyFLOsLtTT.f0URVWBWs6.QgS4Uvi', 'asd@asd.asd', 'asd', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('4', 'hm081333', '$2y$10$ww7CvAzywm63TrgxAc5LjO5LbCj5Qk/NrEId5QdWkEerXHcZVidhq', '522751485@qq.com', '何朗义', '0', '2016-10-27 11:32:20');
INSERT INTO `forum_user` VALUES ('34', '123', '$2y$10$3WwirmQkC76hGz7ICMA8jOk3LS7lNtZ/QoTxZEEDKUj6e/stXMzMS', '123', '', '1', '2016-10-31 22:12:46');
INSERT INTO `forum_user` VALUES ('42', 'qwe', '$2y$10$1wFHILZnaOfLXlM/k4YnquNRVri56TDmsMPScARA9Esenly10mU6a', 'qwe@qwe.qwe', 'qwe', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('36', '1231', '$2y$10$Js2EeIpCJb2ulZULvJPpQuwhpCW2R.U6lK3R/CWFZYbf0n4E.X1PS', '123@123.1231', '123', '1', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('37', 'LYi-Ho', '$2y$10$XamG9q35OPsGYNg7s2m5EesZbcE3fs6k5qvqfK1rBP0oAm9X141YG', '503214851@qq.com', '何朗义', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('38', '1234', '$2y$10$WxpRJRo8SDwyTusqFOXqUuNm.HMOU4LFZG/0wvM6Frg71jxSJJtZO', '1234@1234.1234', '', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('39', '12345', '$2y$10$cOTwR/BJeEmGC0JdimzPxukMnuKni/7knA6qoqsb/4zxtToW7aaF.', '1234@1234.123', '', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('40', '123456', '$2y$10$XGx55oG4i4OqpiVGwPQVJupgkLhvb9cXLnL3XUewcOzHMtH2GlG3C', '1234@1234.12345', '', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('41', 'test', '$2y$10$k1U76hKD7SaNevJSz2bw2OeKbbaxZCDTfXrnmk9DO2UdZTVtb75UO', 'test@test.test', '', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('45', 'test', '$2y$10$cDj6AUspm5kmPDd6d6sGRua.ArmlTne8pIeIpq16Rt8LIZ.jvC/AG', '', '', '1', '0000-00-00 00:00:00');
