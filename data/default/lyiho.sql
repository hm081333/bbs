/*
 Navicat Premium Data Transfer

 Source Server         : MariaDB
 Source Server Type    : MariaDB
 Source Server Version : 100310
 Source Host           : 127.0.0.1:3306
 Source Schema         : lyiho

 Target Server Type    : MariaDB
 Target Server Version : 100310
 File Encoding         : 65001

 Date: 21/01/2019 10:44:47
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ly_admin
-- ----------------------------
DROP TABLE IF EXISTS `ly_admin`;
CREATE TABLE `ly_admin`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '账号',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态',
  `auth` int(1) NOT NULL DEFAULT 0,
  `add_time` int(10) NULL DEFAULT 0 COMMENT '添加时间',
  `edit_time` int(10) NULL DEFAULT 0 COMMENT '添加时间',
  `a_pwd` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `token` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员用户列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_baiduid
-- ----------------------------
DROP TABLE IF EXISTS `ly_baiduid`;
CREATE TABLE `ly_baiduid`  (
  `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(30) UNSIGNED NOT NULL,
  `bduss` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `refresh_time` int(11) NULL DEFAULT 0 COMMENT '刷新列表时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`user_id`) USING BTREE,
  INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 47 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员绑定的BDUSS ID列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_class
-- ----------------------------
DROP TABLE IF EXISTS `ly_class`;
CREATE TABLE `ly_class`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '课程ID',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '课程名称',
  `tips` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '课程说明',
  `add_time` int(11) NULL DEFAULT 0 COMMENT '添加事件',
  `edit_time` int(11) NULL DEFAULT 0 COMMENT '编辑时间',
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '课程列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_delivery
-- ----------------------------
DROP TABLE IF EXISTS `ly_delivery`;
CREATE TABLE `ly_delivery`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `memo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '快递公司代码',
  `sn` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '快递单号',
  `add_time` int(11) NULL DEFAULT NULL COMMENT '添加时间',
  `edit_time` int(11) NULL DEFAULT NULL COMMENT '编辑时间',
  `last_time` int(11) NULL DEFAULT NULL COMMENT '上次查看时间',
  `state` int(1) NULL DEFAULT NULL COMMENT '单号状态',
  `log_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '物流公司名字',
  `end_time` int(11) NULL DEFAULT NULL COMMENT '物流最后更新时间',
  `user_id` int(12) NULL DEFAULT 0 COMMENT '所属用户',
  `last_message` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '最后物流信息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '快递查询--单号' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_email_auth
-- ----------------------------
DROP TABLE IF EXISTS `ly_email_auth`;
CREATE TABLE `ly_email_auth`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `add_time` int(64) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '邮件验证码' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_ip
-- ----------------------------
DROP TABLE IF EXISTS `ly_ip`;
CREATE TABLE `ly_ip`  (
  `id` int(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'ip地址',
  `info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'ip对应信息',
  `add_time` int(12) NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 135 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'IP地址库' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_logistics_company
-- ----------------------------
DROP TABLE IF EXISTS `ly_logistics_company`;
CREATE TABLE `ly_logistics_company`  (
  `id` int(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '快递公司名称',
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '快递公司代码 ',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '快递公司网址',
  `add_time` int(10) NULL DEFAULT 0 COMMENT '添加时间',
  `state` int(1) NULL DEFAULT 0 COMMENT '状态',
  `sort` int(64) NULL DEFAULT 0 COMMENT '排序',
  `used` int(255) NULL DEFAULT 0 COMMENT '使用次数',
  `memo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '快递公司说明',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 128 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物流公司' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_message
-- ----------------------------
DROP TABLE IF EXISTS `ly_message`;
CREATE TABLE `ly_message`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板内容',
  `edit_time` int(11) NULL DEFAULT NULL COMMENT '编辑时间',
  `type` tinyint(1) NULL DEFAULT 0 COMMENT '信息类型（0：邮件，1：短信）',
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '信息代号',
  `state` tinyint(1) NULL DEFAULT 0 COMMENT '模板状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '短信、邮件模板' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_reply
-- ----------------------------
DROP TABLE IF EXISTS `ly_reply`;
CREATE TABLE `ly_reply`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '回复ID',
  `topic_id` int(10) NOT NULL DEFAULT 0 COMMENT '文章ID',
  `sort` int(10) NOT NULL DEFAULT 0 COMMENT '回复排序',
  `user_id` int(11) NULL DEFAULT 0 COMMENT '回复会员ID',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `detail` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '回复内容',
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '回复时间',
  `edit_time` int(11) NOT NULL COMMENT '编辑时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '回复帖子' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_setting
-- ----------------------------
DROP TABLE IF EXISTS `ly_setting`;
CREATE TABLE `ly_setting`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮件设置',
  `sms` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '短信设置',
  `wechat` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信设置',
  `tuling` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图灵设置',
  `baidu_map` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '百度地图设置',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统设置信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_sms_auth
-- ----------------------------
DROP TABLE IF EXISTS `ly_sms_auth`;
CREATE TABLE `ly_sms_auth`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `add_time` int(64) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '手机验证码' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_tieba
-- ----------------------------
DROP TABLE IF EXISTS `ly_tieba`;
CREATE TABLE `ly_tieba`  (
  `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '递增ID',
  `user_id` int(30) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'user表ID',
  `baidu_id` int(30) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'baiduid表ID',
  `fid` int(30) UNSIGNED NOT NULL DEFAULT 0 COMMENT '贴吧ID',
  `tieba` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '贴吧名',
  `no` tinyint(1) NOT NULL DEFAULT 0 COMMENT '忽略签到 0 否 1忽略',
  `status` mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态',
  `latest` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后签到时间',
  `last_error` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '最近一次签到错误',
  `refresh_time` int(11) NULL DEFAULT 0 COMMENT '刷新列表时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`user_id`) USING BTREE,
  INDEX `latest`(`latest`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 391 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '贴吧列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_topic
-- ----------------------------
DROP TABLE IF EXISTS `ly_topic`;
CREATE TABLE `ly_topic`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '文章ID',
  `class_id` int(10) NOT NULL COMMENT '分类ID',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `detail` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `user_id` int(10) NULL DEFAULT 0 COMMENT '会员ID',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '发表时间',
  `edit_time` int(11) NOT NULL COMMENT '修改时间',
  `view` int(10) NOT NULL DEFAULT 0 COMMENT '浏览数量',
  `reply` int(10) NOT NULL DEFAULT 0 COMMENT '回复数量',
  `sticky` tinyint(1) NOT NULL DEFAULT 0 COMMENT '顶置',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '帖子表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ly_user
-- ----------------------------
DROP TABLE IF EXISTS `ly_user`;
CREATE TABLE `ly_user`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '邮箱',
  `real_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '真实姓名',
  `auth` int(1) NOT NULL DEFAULT 0 COMMENT '权限',
  `reg_time` int(11) NOT NULL DEFAULT 0 COMMENT '注册时间',
  `edit_time` int(11) NULL DEFAULT 0 COMMENT '修改时间',
  `birth_time` int(11) NOT NULL DEFAULT 0 COMMENT '生日',
  `a_pwd` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `open_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '微信唯一ID',
  `secret` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '谷歌二步验证',
  `sign_notice` tinyint(1) NULL DEFAULT 0 COMMENT '签到通知',
  `sex` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '性别',
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '登录令牌',
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员列表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
