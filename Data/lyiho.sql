/*
Navicat MySQL Data Transfer

Source Server         : 黛米云
Source Server Version : 50720
Source Host           : 123.249.20.195:3306
Source Database       : lyiho

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2017-12-24 13:49:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ly_admin
-- ----------------------------
DROP TABLE IF EXISTS `ly_admin`;
CREATE TABLE `ly_admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员用户列表';

-- ----------------------------
-- Table structure for ly_baiduid
-- ----------------------------
DROP TABLE IF EXISTS `ly_baiduid`;
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

-- ----------------------------
-- Table structure for ly_class
-- ----------------------------
DROP TABLE IF EXISTS `ly_class`;
CREATE TABLE `ly_class` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tips` varchar(255) NOT NULL,
  `add_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='课程列表';

-- ----------------------------
-- Table structure for ly_delivery
-- ----------------------------
DROP TABLE IF EXISTS `ly_delivery`;
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

-- ----------------------------
-- Table structure for ly_email_auth
-- ----------------------------
DROP TABLE IF EXISTS `ly_email_auth`;
CREATE TABLE `ly_email_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `add_time` int(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件验证码';

-- ----------------------------
-- Table structure for ly_ip
-- ----------------------------
DROP TABLE IF EXISTS `ly_ip`;
CREATE TABLE `ly_ip` (
  `id` int(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(255) DEFAULT NULL COMMENT 'ip地址',
  `info` text COMMENT 'ip对应信息',
  `add_time` int(12) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 COMMENT='IP地址库';

-- ----------------------------
-- Table structure for ly_logistics_company
-- ----------------------------
DROP TABLE IF EXISTS `ly_logistics_company`;
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

-- ----------------------------
-- Table structure for ly_reply
-- ----------------------------
DROP TABLE IF EXISTS `ly_reply`;
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

-- ----------------------------
-- Table structure for ly_sms_auth
-- ----------------------------
DROP TABLE IF EXISTS `ly_sms_auth`;
CREATE TABLE `ly_sms_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `add_time` int(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机验证码';

-- ----------------------------
-- Table structure for ly_tieba
-- ----------------------------
DROP TABLE IF EXISTS `ly_tieba`;
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

-- ----------------------------
-- Table structure for ly_topic
-- ----------------------------
DROP TABLE IF EXISTS `ly_topic`;
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

-- ----------------------------
-- Table structure for ly_user
-- ----------------------------
DROP TABLE IF EXISTS `ly_user`;
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
SET FOREIGN_KEY_CHECKS=1;
