/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : lyiho

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-09-24 20:07:39
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_admin
-- ----------------------------
INSERT INTO `ly_admin` VALUES ('1', 'root', '$2y$10$G0yecuK.J5OEkRdKf//0oOku6dIAD5ys/Y0lnBfLNjYACsFrMZLxm', '1');
INSERT INTO `ly_admin` VALUES ('2', 'test', '$2y$10$ycAX52.TI4svrq2gO8CuZOjPp.OvGx8g6VHWJz8imLupL38QCNiqK', '1');
INSERT INTO `ly_admin` VALUES ('3', 'empty', '$2y$10$d4uOFrCTN0LfTyIuChNEOO3a9uSS7SsjDa2LgoNwE6y4Mj6bGuNNC', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_class
-- ----------------------------
INSERT INTO `ly_class` VALUES ('1', '闲聊', '随意灌水区，随便灌水的地方。。。写个长一点儿的说明试一下css省略效果。感觉有点爆炸', '1504446603');
INSERT INTO `ly_class` VALUES ('2', 'PHP', 'PHP是世界上最好的语言', '1504446603');
INSERT INTO `ly_class` VALUES ('3', 'JavaScript', 'JS是微信最常用的语言', '1504446603');
INSERT INTO `ly_class` VALUES ('4', 'Photoshop', '', '1504446603');
INSERT INTO `ly_class` VALUES ('5', 'Flash', '', '1504446603');
INSERT INTO `ly_class` VALUES ('6', 'C程序设计', '', '1504446603');
INSERT INTO `ly_class` VALUES ('7', 'MySQL数据库', '', '1504446603');
INSERT INTO `ly_class` VALUES ('8', '网页设计', '', '1504446603');
INSERT INTO `ly_class` VALUES ('9', '网络营销', '', '1504446603');
INSERT INTO `ly_class` VALUES ('10', '计算机网络基础', '', '1504446603');
INSERT INTO `ly_class` VALUES ('11', 'Illustrator平面设计', '', '1504446603');
INSERT INTO `ly_class` VALUES ('12', 'Linux网络操作系统', '', '1504446603');
INSERT INTO `ly_class` VALUES ('13', 'ASP.NET', '', '1504446603');
INSERT INTO `ly_class` VALUES ('14', 'Android应用开发', '', '1504446603');
INSERT INTO `ly_class` VALUES ('16', 'PhalApi', 'PhalApi是一个用于快速写api接口的php框架', '1504446603');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_delivery
-- ----------------------------

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_email_auth
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_ip
-- ----------------------------
INSERT INTO `ly_ip` VALUES ('1', '113.77.81.172', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.81.172\";}', '1504111817');
INSERT INTO `ly_ip` VALUES ('2', '183.42.21.165', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"广州市\";s:7:\"city_id\";s:6:\"440100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"183.42.21.165\";}', '1504112445');
INSERT INTO `ly_ip` VALUES ('3', '223.104.63.235', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:14:\"223.104.63.235\";}', '1504112568');
INSERT INTO `ly_ip` VALUES ('4', '116.18.229.133', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"116.18.229.133\";}', '1504140861');
INSERT INTO `ly_ip` VALUES ('5', '47.90.127.34', 'a:13:{s:7:\"country\";s:6:\"香港\";s:10:\"country_id\";s:2:\"HK\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:21:\"香港特别行政区\";s:9:\"region_id\";s:5:\"HK_01\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:2:\"-1\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:9:\"阿里云\";s:6:\"isp_id\";s:7:\"1000323\";s:2:\"ip\";s:12:\"47.90.127.34\";}', '1504161561');
INSERT INTO `ly_ip` VALUES ('6', '40.83.125.125', 'a:13:{s:7:\"country\";s:6:\"香港\";s:10:\"country_id\";s:2:\"HK\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:21:\"香港特别行政区\";s:9:\"region_id\";s:5:\"HK_01\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:2:\"-1\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:9:\"microsoft\";s:6:\"isp_id\";s:2:\"-1\";s:2:\"ip\";s:13:\"40.83.125.125\";}', '1504161645');
INSERT INTO `ly_ip` VALUES ('7', '103.86.71.89', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华东\";s:7:\"area_id\";s:6:\"300000\";s:6:\"region\";s:9:\"上海市\";s:9:\"region_id\";s:6:\"310000\";s:4:\"city\";s:9:\"上海市\";s:7:\"city_id\";s:6:\"310100\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:0:\"\";s:6:\"isp_id\";s:2:\"-1\";s:2:\"ip\";s:12:\"103.86.71.89\";}', '1504161728');
INSERT INTO `ly_ip` VALUES ('8', '123.103.252.54', 'a:13:{s:7:\"country\";s:6:\"香港\";s:10:\"country_id\";s:2:\"HK\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:21:\"香港特别行政区\";s:9:\"region_id\";s:5:\"HK_01\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:2:\"-1\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:0:\"\";s:6:\"isp_id\";s:2:\"-1\";s:2:\"ip\";s:14:\"123.103.252.54\";}', '1504161763');
INSERT INTO `ly_ip` VALUES ('9', '113.78.15.31', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:12:\"113.78.15.31\";}', '1504161945');
INSERT INTO `ly_ip` VALUES ('10', '203.114.75.84', 'a:13:{s:7:\"country\";s:9:\"菲律宾\";s:10:\"country_id\";s:2:\"PH\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:0:\"\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:0:\"\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:0:\"\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:0:\"\";s:3:\"isp\";s:0:\"\";s:6:\"isp_id\";s:0:\"\";s:2:\"ip\";s:13:\"203.114.75.84\";}', '1504162208');
INSERT INTO `ly_ip` VALUES ('11', '104.194.206.171', 'a:13:{s:7:\"country\";s:6:\"美国\";s:10:\"country_id\";s:2:\"US\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:0:\"\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:0:\"\";s:4:\"city\";s:0:\"\";s:7:\"city_id\";s:0:\"\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:0:\"\";s:3:\"isp\";s:0:\"\";s:6:\"isp_id\";s:0:\"\";s:2:\"ip\";s:15:\"104.194.206.171\";}', '1504162224');
INSERT INTO `ly_ip` VALUES ('12', '223.104.63.229', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"移动\";s:6:\"isp_id\";s:6:\"100025\";s:2:\"ip\";s:14:\"223.104.63.229\";}', '1504163202');
INSERT INTO `ly_ip` VALUES ('13', '113.77.83.155', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.83.155\";}', '1504185481');
INSERT INTO `ly_ip` VALUES ('14', '127.0.0.1', 'a:13:{s:7:\"country\";s:23:\"未分配或者内网IP\";s:10:\"country_id\";s:4:\"IANA\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:2:\"-1\";s:4:\"city\";s:8:\"内网IP\";s:7:\"city_id\";s:5:\"local\";s:6:\"county\";s:8:\"内网IP\";s:9:\"county_id\";s:5:\"local\";s:3:\"isp\";s:8:\"内网IP\";s:6:\"isp_id\";s:5:\"local\";s:2:\"ip\";s:9:\"127.0.0.1\";}', '1504369694');
INSERT INTO `ly_ip` VALUES ('15', '10.0.0.10', 'a:13:{s:7:\"country\";s:23:\"未分配或者内网IP\";s:10:\"country_id\";s:4:\"IANA\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:2:\"-1\";s:4:\"city\";s:8:\"内网IP\";s:7:\"city_id\";s:5:\"local\";s:6:\"county\";s:8:\"内网IP\";s:9:\"county_id\";s:5:\"local\";s:3:\"isp\";s:8:\"内网IP\";s:6:\"isp_id\";s:5:\"local\";s:2:\"ip\";s:9:\"10.0.0.10\";}', '1504420502');
INSERT INTO `ly_ip` VALUES ('16', '10.0.0.5', 'a:13:{s:7:\"country\";s:23:\"未分配或者内网IP\";s:10:\"country_id\";s:4:\"IANA\";s:4:\"area\";s:0:\"\";s:7:\"area_id\";s:2:\"-1\";s:6:\"region\";s:0:\"\";s:9:\"region_id\";s:2:\"-1\";s:4:\"city\";s:8:\"内网IP\";s:7:\"city_id\";s:5:\"local\";s:6:\"county\";s:8:\"内网IP\";s:9:\"county_id\";s:5:\"local\";s:3:\"isp\";s:8:\"内网IP\";s:6:\"isp_id\";s:5:\"local\";s:2:\"ip\";s:8:\"10.0.0.5\";}', '1504449599');
INSERT INTO `ly_ip` VALUES ('17', '113.77.80.150', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.80.150\";}', '1504878419');
INSERT INTO `ly_ip` VALUES ('18', '113.77.81.65', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:12:\"113.77.81.65\";}', '1505001956');
INSERT INTO `ly_ip` VALUES ('19', '113.77.81.188', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.81.188\";}', '1505829370');
INSERT INTO `ly_ip` VALUES ('20', '113.77.83.132', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.83.132\";}', '1506093460');
INSERT INTO `ly_ip` VALUES ('21', '183.49.252.242', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:14:\"183.49.252.242\";}', '1506249167');

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
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_logistics_company
-- ----------------------------
INSERT INTO `ly_logistics_company` VALUES ('1', '澳大利亚邮政(英文结果）', 'auspost', '', '1502764220', '1', '0', '0', '澳大利亚邮政(英文结果）');
INSERT INTO `ly_logistics_company` VALUES ('2', 'AAE', 'aae', '', '1502764220', '1', '0', '0', 'AAE');
INSERT INTO `ly_logistics_company` VALUES ('3', '安信达', 'anxindakuaixi', '', '1502764220', '1', '0', '0', '安信达');
INSERT INTO `ly_logistics_company` VALUES ('4', '汇通快运', 'huitongkuaidi', '', '1502764220', '1', '0', '0', '汇通快运');
INSERT INTO `ly_logistics_company` VALUES ('5', '百福东方', 'baifudongfang', '', '1502764220', '1', '0', '0', '百福东方');
INSERT INTO `ly_logistics_company` VALUES ('6', 'BHT', 'bht', '', '1502764220', '1', '0', '0', 'BHT');
INSERT INTO `ly_logistics_company` VALUES ('7', '邮政小包（国内），邮政包裹（国内）、邮政国内给据（国内）', 'youzhengguonei', '', '1502764220', '1', '0', '0', '邮政小包（国内），邮政包裹（国内）、邮政国内给据（国内）');
INSERT INTO `ly_logistics_company` VALUES ('8', '邦送物流', 'bangsongwuliu', '', '1502764220', '1', '0', '0', '邦送物流');
INSERT INTO `ly_logistics_company` VALUES ('9', '希伊艾斯（CCES）', 'cces', '', '1502764220', '1', '0', '0', '希伊艾斯（CCES）');
INSERT INTO `ly_logistics_company` VALUES ('10', '中国东方（COE）', 'coe', '', '1502764220', '1', '0', '0', '中国东方（COE）');
INSERT INTO `ly_logistics_company` VALUES ('11', '传喜物流', 'chuanxiwuliu', '', '1502764220', '1', '0', '0', '传喜物流');
INSERT INTO `ly_logistics_company` VALUES ('12', '加拿大邮政Canada Post（英文结果）', 'canpost', '', '1502764220', '1', '0', '0', '加拿大邮政Canada Post（英文结果）');
INSERT INTO `ly_logistics_company` VALUES ('13', '加拿大邮政Canada Post(德文结果）', 'canpostfr', '', '1502764220', '1', '0', '0', '加拿大邮政Canada Post(德文结果）');
INSERT INTO `ly_logistics_company` VALUES ('14', '大田物流', 'datianwuliu', '', '1502764220', '1', '0', '0', '大田物流');
INSERT INTO `ly_logistics_company` VALUES ('15', '德邦物流', 'debangwuliu', '', '1502764220', '1', '0', '0', '德邦物流');
INSERT INTO `ly_logistics_company` VALUES ('16', 'DPEX', 'dpex', '', '1502764220', '1', '0', '0', 'DPEX');
INSERT INTO `ly_logistics_company` VALUES ('17', 'DHL-中国件-中文结果', 'dhl', '', '1502764220', '1', '0', '0', 'DHL-中国件-中文结果');
INSERT INTO `ly_logistics_company` VALUES ('18', 'DHL-国际件-英文结果', 'dhlen', '', '1502764220', '1', '0', '0', 'DHL-国际件-英文结果');
INSERT INTO `ly_logistics_company` VALUES ('19', 'DHL-德国件-德文结果（德国国内派、收的件）', 'dhlde', '', '1502764220', '1', '0', '0', 'DHL-德国件-德文结果（德国国内派、收的件）');
INSERT INTO `ly_logistics_company` VALUES ('20', 'D速快递', 'dsukuaidi', '', '1502764220', '1', '0', '0', 'D速快递');
INSERT INTO `ly_logistics_company` VALUES ('21', '递四方', 'disifang', '', '1502764220', '1', '0', '0', '递四方');
INSERT INTO `ly_logistics_company` VALUES ('22', 'E邮宝', 'ems', '', '1502764220', '1', '0', '0', 'E邮宝');
INSERT INTO `ly_logistics_company` VALUES ('23', 'EMS（英文结果）', 'emsen', '', '1502764220', '1', '0', '0', 'EMS（英文结果）');
INSERT INTO `ly_logistics_company` VALUES ('24', 'EMS-（中国-国际）件-中文结果/EMS-(China-International）-Chinese data', 'emsguoji', '', '1502764220', '1', '0', '0', 'EMS-（中国-国际）件-中文结果/EMS-(China-International）-Chinese data');
INSERT INTO `ly_logistics_company` VALUES ('25', 'EMS-（中国-国际）件-英文结果/EMS-(China-International）-Englilsh data', 'emsinten', '', '1502764220', '1', '0', '0', 'EMS-（中国-国际）件-英文结果/EMS-(China-International）-Englilsh data');
INSERT INTO `ly_logistics_company` VALUES ('26', 'Fedex-国际件-英文结果（说明：Fedex是国际件的英文结果，Fedex中国的请用“lianbangkuaidi”，Fedex-美国请用fedexus）', 'fedex', '', '1502764220', '1', '0', '0', 'Fedex-国际件-英文结果（说明：Fedex是国际件的英文结果，Fedex中国的请用“lianbangkuaidi”，Fedex-美国请用fedexus）');
INSERT INTO `ly_logistics_company` VALUES ('27', 'Fedex-国际件-中文结果', 'fedexcn', '', '1502764220', '1', '0', '0', 'Fedex-国际件-中文结果');
INSERT INTO `ly_logistics_company` VALUES ('28', 'Fedex-美国件-英文结果(说明：如果无效，请偿试使用fedex）', 'fedexus', '', '1502764220', '1', '0', '0', 'Fedex-美国件-英文结果(说明：如果无效，请偿试使用fedex）');
INSERT INTO `ly_logistics_company` VALUES ('29', '飞康达物流', 'feikangda', '', '1502764220', '1', '0', '0', '飞康达物流');
INSERT INTO `ly_logistics_company` VALUES ('30', '飞快达', 'feikuaida', '', '1502764220', '1', '0', '0', '飞快达');
INSERT INTO `ly_logistics_company` VALUES ('31', '如风达快递', 'rufengda', '', '1502764220', '1', '0', '0', '如风达快递');
INSERT INTO `ly_logistics_company` VALUES ('32', '风行天下', 'fengxingtianxia', '', '1502764220', '1', '0', '0', '风行天下');
INSERT INTO `ly_logistics_company` VALUES ('33', '飞豹快递', 'feibaokuaidi', '', '1502764220', '1', '0', '0', '飞豹快递');
INSERT INTO `ly_logistics_company` VALUES ('34', '港中能达', 'ganzhongnengda', '', '1502764220', '1', '0', '0', '港中能达');
INSERT INTO `ly_logistics_company` VALUES ('35', '国通快递', 'guotongkuaidi', '', '1502764220', '1', '0', '0', '国通快递');
INSERT INTO `ly_logistics_company` VALUES ('36', '广东邮政', 'guangdongyouzhengwuliu', '', '1502764220', '1', '0', '0', '广东邮政');
INSERT INTO `ly_logistics_company` VALUES ('37', '邮政小包（国际），邮政包裹（国际）、邮政国内给据（国际）', 'youzhengguoji', '', '1502764220', '1', '0', '0', '邮政小包（国际），邮政包裹（国际）、邮政国内给据（国际）');
INSERT INTO `ly_logistics_company` VALUES ('38', 'GLS', 'gls', '', '1502764220', '1', '0', '0', 'GLS');
INSERT INTO `ly_logistics_company` VALUES ('39', '共速达', 'gongsuda', '', '1502764220', '1', '0', '0', '共速达');
INSERT INTO `ly_logistics_company` VALUES ('40', '汇强快递', 'huiqiangkuaidi', '', '1502764220', '1', '0', '0', '汇强快递');
INSERT INTO `ly_logistics_company` VALUES ('41', '天地华宇', 'tiandihuayu', '', '1502764220', '1', '0', '0', '天地华宇');
INSERT INTO `ly_logistics_company` VALUES ('42', '恒路物流', 'hengluwuliu', '', '1502764220', '1', '0', '0', '恒路物流');
INSERT INTO `ly_logistics_company` VALUES ('43', '华夏龙', 'huaxialongwuliu', '', '1502764220', '1', '0', '0', '华夏龙');
INSERT INTO `ly_logistics_company` VALUES ('44', '天天快递', 'tiantian', '', '1502764220', '1', '0', '0', '天天快递');
INSERT INTO `ly_logistics_company` VALUES ('45', '海外环球', 'haiwaihuanqiu', '', '1502764220', '1', '0', '0', '海外环球');
INSERT INTO `ly_logistics_company` VALUES ('46', '河北建华', 'hebeijianhua', '', '1502764220', '1', '0', '0', '河北建华（暂只能查好乐买的单，其他商家要查，请发邮件至 wensheng_chen#kingdee.com(将#替换成@)开通权限');
INSERT INTO `ly_logistics_company` VALUES ('47', '海盟速递', 'haimengsudi', '', '1502764220', '1', '0', '0', '海盟速递');
INSERT INTO `ly_logistics_company` VALUES ('48', '华企快运', 'huaqikuaiyun', '', '1502764220', '1', '0', '0', '华企快运');
INSERT INTO `ly_logistics_company` VALUES ('49', '山东海红', 'haihongwangsong', '', '1502764220', '1', '0', '0', '山东海红');
INSERT INTO `ly_logistics_company` VALUES ('50', '佳吉物流', 'jiajiwuliu', '', '1502764220', '1', '0', '0', '佳吉物流');
INSERT INTO `ly_logistics_company` VALUES ('51', '佳怡物流', 'jiayiwuliu', '', '1502764220', '1', '0', '0', '佳怡物流');
INSERT INTO `ly_logistics_company` VALUES ('52', '加运美', 'jiayunmeiwuliu', '', '1502764220', '1', '0', '0', '加运美');
INSERT INTO `ly_logistics_company` VALUES ('53', '京广速递', 'jinguangsudikuaijian', '', '1502764220', '1', '0', '0', '京广速递');
INSERT INTO `ly_logistics_company` VALUES ('54', '急先达', 'jixianda', '', '1502764220', '1', '0', '0', '急先达');
INSERT INTO `ly_logistics_company` VALUES ('55', '晋越快递', 'jinyuekuaidi', '', '1502764220', '1', '0', '0', '晋越快递');
INSERT INTO `ly_logistics_company` VALUES ('56', '捷特快递', 'jietekuaidi', '', '1502764220', '1', '0', '0', '捷特快递');
INSERT INTO `ly_logistics_company` VALUES ('57', '金大物流', 'jindawuliu', '', '1502764220', '1', '0', '0', '金大物流');
INSERT INTO `ly_logistics_company` VALUES ('58', '嘉里大通', 'jialidatong', '', '1502764220', '1', '0', '0', '嘉里大通');
INSERT INTO `ly_logistics_company` VALUES ('59', '快捷速递', 'kuaijiesudi', '', '1502764220', '1', '0', '0', '快捷速递');
INSERT INTO `ly_logistics_company` VALUES ('60', '康力物流', 'kangliwuliu', '', '1502764220', '1', '0', '0', '康力物流');
INSERT INTO `ly_logistics_company` VALUES ('61', '跨越物流', 'kuayue', '', '1502764220', '1', '0', '0', '跨越物流');
INSERT INTO `ly_logistics_company` VALUES ('62', '联昊通', 'lianhaowuliu', '', '1502764220', '1', '0', '0', '联昊通');
INSERT INTO `ly_logistics_company` VALUES ('63', '龙邦物流', 'longbanwuliu', '', '1502764220', '1', '0', '0', '龙邦物流');
INSERT INTO `ly_logistics_company` VALUES ('64', '蓝镖快递', 'lanbiaokuaidi', '', '1502764220', '1', '0', '0', '蓝镖快递');
INSERT INTO `ly_logistics_company` VALUES ('65', '乐捷递', 'lejiedi', '', '1502764220', '1', '0', '0', '乐捷递（暂只能查好乐买的单，其他商家要查，请发邮件至 wensheng_chen#kingdee.com(将#替换成@)开通权限');
INSERT INTO `ly_logistics_company` VALUES ('66', '联邦快递（Fedex-中国-中文结果）（说明：国外的请用 fedex）', 'lianbangkuaidi', '', '1502764220', '1', '0', '0', '联邦快递（Fedex-中国-中文结果）（说明：国外的请用 fedex）');
INSERT INTO `ly_logistics_company` VALUES ('67', '联邦快递(Fedex-中国-英文结果）', 'lianbangkuaidien', '', '1502764220', '1', '0', '0', '联邦快递(Fedex-中国-英文结果）');
INSERT INTO `ly_logistics_company` VALUES ('68', '立即送', 'lijisong', '', '1502764220', '1', '0', '0', '立即送（暂只能查好乐买的单，其他商家要查，请发邮件至 wensheng_chen#kingdee.com(将#替换成@)开通权限)');
INSERT INTO `ly_logistics_company` VALUES ('69', '隆浪快递', 'longlangkuaidi', '', '1502764220', '1', '0', '0', '隆浪快递');
INSERT INTO `ly_logistics_company` VALUES ('70', '门对门', 'menduimen', '', '1502764220', '1', '0', '0', '门对门');
INSERT INTO `ly_logistics_company` VALUES ('71', '美国快递', 'meiguokuaidi', '', '1502764220', '1', '0', '0', '美国快递');
INSERT INTO `ly_logistics_company` VALUES ('72', '明亮物流', 'mingliangwuliu', '', '1502764220', '1', '0', '0', '明亮物流');
INSERT INTO `ly_logistics_company` VALUES ('73', 'OCS', 'ocs', '', '1502764220', '1', '0', '0', 'OCS');
INSERT INTO `ly_logistics_company` VALUES ('74', 'onTrac', 'ontrac', '', '1502764220', '1', '0', '0', 'onTrac');
INSERT INTO `ly_logistics_company` VALUES ('75', '全晨快递', 'quanchenkuaidi', '', '1502764220', '1', '0', '0', '全晨快递');
INSERT INTO `ly_logistics_company` VALUES ('76', '全际通', 'quanjitong', '', '1502764220', '1', '0', '0', '全际通');
INSERT INTO `ly_logistics_company` VALUES ('77', '全日通', 'quanritongkuaidi', '', '1502764220', '1', '0', '0', '全日通');
INSERT INTO `ly_logistics_company` VALUES ('78', '全一快递', 'quanyikuaidi', '', '1502764220', '1', '0', '0', '全一快递');
INSERT INTO `ly_logistics_company` VALUES ('79', '全峰快递', 'quanfengkuaidi', '', '1502764220', '1', '0', '0', '全峰快递');
INSERT INTO `ly_logistics_company` VALUES ('80', '七天连锁', 'sevendays', '', '1502764220', '1', '0', '0', '七天连锁');
INSERT INTO `ly_logistics_company` VALUES ('81', '申通', 'shentong', '', '1502764220', '1', '0', '3', '申通');
INSERT INTO `ly_logistics_company` VALUES ('82', '顺丰', 'shunfeng', '', '1502764220', '1', '0', '1', '顺丰');
INSERT INTO `ly_logistics_company` VALUES ('83', '顺丰（英文结果）', 'shunfengen', '', '1502764220', '1', '0', '0', '顺丰（英文结果）');
INSERT INTO `ly_logistics_company` VALUES ('84', '三态速递', 'santaisudi', '', '1502764220', '1', '0', '0', '三态速递');
INSERT INTO `ly_logistics_company` VALUES ('85', '盛辉物流', 'shenghuiwuliu', '', '1502764220', '1', '0', '0', '盛辉物流');
INSERT INTO `ly_logistics_company` VALUES ('86', '速尔物流', 'suer', '', '1502764220', '1', '0', '0', '速尔物流');
INSERT INTO `ly_logistics_company` VALUES ('87', '盛丰物流', 'shengfengwuliu', '', '1502764220', '1', '0', '0', '盛丰物流');
INSERT INTO `ly_logistics_company` VALUES ('88', '上大物流', 'shangda', '', '1502764220', '1', '0', '0', '上大物流');
INSERT INTO `ly_logistics_company` VALUES ('89', '赛澳递', 'saiaodi', '', '1502764220', '1', '0', '0', '赛澳递');
INSERT INTO `ly_logistics_company` VALUES ('90', '山西红马甲', 'sxhongmajia', '', '1502764220', '1', '0', '0', '山西红马甲');
INSERT INTO `ly_logistics_company` VALUES ('91', '圣安物流', 'shenganwuliu', '', '1502764220', '1', '0', '0', '圣安物流');
INSERT INTO `ly_logistics_company` VALUES ('92', '穗佳物流', 'suijiawuliu', '', '1502764220', '1', '0', '0', '穗佳物流');
INSERT INTO `ly_logistics_company` VALUES ('93', 'TNT（中文结果）', 'tnt', '', '1502764220', '1', '0', '0', 'TNT（中文结果）');
INSERT INTO `ly_logistics_company` VALUES ('94', 'TNT（英文结果）', 'tnten', '', '1502764220', '1', '0', '0', 'TNT（英文结果）');
INSERT INTO `ly_logistics_company` VALUES ('95', '通和天下', 'tonghetianxia', '', '1502764220', '1', '0', '0', '通和天下');
INSERT INTO `ly_logistics_company` VALUES ('96', 'UPS（中文结果）', 'ups', '', '1502764220', '1', '0', '0', 'UPS（中文结果）');
INSERT INTO `ly_logistics_company` VALUES ('97', 'UPS（英文结果）', 'upsen', '', '1502764220', '1', '0', '0', 'UPS（英文结果）');
INSERT INTO `ly_logistics_company` VALUES ('98', '优速物流', 'youshuwuliu', '', '1502764220', '1', '0', '0', '优速物流');
INSERT INTO `ly_logistics_company` VALUES ('99', 'USPS（中英文）', 'usps', '', '1502764220', '1', '0', '0', 'USPS（中英文）');
INSERT INTO `ly_logistics_company` VALUES ('100', '万家物流', 'wanjiawuliu', '', '1502764220', '1', '0', '0', '万家物流');
INSERT INTO `ly_logistics_company` VALUES ('101', '万象物流', 'wanxiangwuliu', '', '1502764220', '1', '0', '0', '万象物流');
INSERT INTO `ly_logistics_company` VALUES ('102', '微特派', 'weitepai', '', '1502764220', '1', '0', '0', '微特派');
INSERT INTO `ly_logistics_company` VALUES ('103', '新邦物流', 'xinbangwuliu', '', '1502764220', '1', '0', '0', '新邦物流');
INSERT INTO `ly_logistics_company` VALUES ('104', '信丰物流', 'xinfengwuliu', '', '1502764220', '1', '0', '0', '信丰物流');
INSERT INTO `ly_logistics_company` VALUES ('105', '新蛋奥硕物流', 'neweggozzo', '', '1502764220', '1', '0', '0', '新蛋奥硕物流');
INSERT INTO `ly_logistics_company` VALUES ('106', '香港邮政', 'hkpost', '', '1502764220', '1', '0', '0', '香港邮政');
INSERT INTO `ly_logistics_company` VALUES ('107', '圆通速递', 'yuantong', '', '1502764220', '1', '0', '0', '圆通速递');
INSERT INTO `ly_logistics_company` VALUES ('108', '韵达快运', 'yunda', '', '1502764220', '1', '0', '0', '韵达快运');
INSERT INTO `ly_logistics_company` VALUES ('109', '运通快递', 'yuntongkuaidi', '', '1502764220', '1', '0', '0', '运通快递');
INSERT INTO `ly_logistics_company` VALUES ('110', '远成物流', 'yuanchengwuliu', '', '1502764220', '1', '0', '0', '远成物流');
INSERT INTO `ly_logistics_company` VALUES ('111', '亚风速递', 'yafengsudi', '', '1502764220', '1', '0', '0', '亚风速递');
INSERT INTO `ly_logistics_company` VALUES ('112', '一邦速递', 'yibangwuliu', '', '1502764220', '1', '0', '0', '一邦速递');
INSERT INTO `ly_logistics_company` VALUES ('113', '源伟丰快递', 'yuanweifeng', '', '1502764220', '1', '0', '0', '源伟丰快递');
INSERT INTO `ly_logistics_company` VALUES ('114', '元智捷诚', 'yuanzhijiecheng', '', '1502764220', '1', '0', '0', '元智捷诚');
INSERT INTO `ly_logistics_company` VALUES ('115', '越丰物流', 'yuefengwuliu', '', '1502764220', '1', '0', '0', '越丰物流');
INSERT INTO `ly_logistics_company` VALUES ('116', '源安达', 'yuananda', '', '1502764220', '1', '0', '0', '源安达');
INSERT INTO `ly_logistics_company` VALUES ('117', '原飞航', 'yuanfeihangwuliu', '', '1502764220', '1', '0', '0', '原飞航');
INSERT INTO `ly_logistics_company` VALUES ('118', '忠信达', 'zhongxinda', '', '1502764220', '1', '0', '0', '忠信达');
INSERT INTO `ly_logistics_company` VALUES ('119', '芝麻开门', 'zhimakaimen', '', '1502764220', '1', '0', '0', '芝麻开门');
INSERT INTO `ly_logistics_company` VALUES ('120', '银捷速递', 'yinjiesudi', '', '1502764220', '1', '0', '0', '银捷速递');
INSERT INTO `ly_logistics_company` VALUES ('121', '一统飞鸿', 'yitongfeihong', '', '1502764220', '1', '0', '0', '一统飞鸿');
INSERT INTO `ly_logistics_company` VALUES ('122', '中通速递', 'zhongtong', '', '1502764220', '1', '0', '0', '中通速递');
INSERT INTO `ly_logistics_company` VALUES ('123', '宅急送', 'zhaijisong', '', '1502764220', '1', '0', '0', '宅急送');
INSERT INTO `ly_logistics_company` VALUES ('124', '中邮物流', 'zhongyouwuliu', '', '1502764220', '1', '0', '0', '中邮物流');
INSERT INTO `ly_logistics_company` VALUES ('125', '中速快件', 'zhongsukuaidi', '', '1502764220', '1', '0', '0', '中速快件');
INSERT INTO `ly_logistics_company` VALUES ('126', '郑州建华', 'zhengzhoujianhua', '', '1502764220', '1', '0', '0', '郑州建华');
INSERT INTO `ly_logistics_company` VALUES ('127', '中天万运', 'zhongtianwanyun', '', '1502764220', '1', '0', '0', '中天万运');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_reply
-- ----------------------------

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_sms_auth
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_topic
-- ----------------------------
INSERT INTO `ly_topic` VALUES ('1', '1', '初次发帖', '请多多指教', 'pics/1505016035.jpg', '1', 'hm081333', '522751485@qq.com', '1505016035', '2', '0', '0');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ly_user
-- ----------------------------
INSERT INTO `ly_user` VALUES ('1', 'hm081333', '$2y$10$o9MgNe1eSx4JByhhYy3rdejcW4B5ugCdhbelfg//YWIubCFYnK2yG', '522751485@qq.com', '何朗义', '0', '1505015829', '845136000');
SET FOREIGN_KEY_CHECKS=1;
