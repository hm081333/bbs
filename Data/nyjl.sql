/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : nyjl

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-08-31 00:51:10
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
-- Table structure for forum_delivery
-- ----------------------------
DROP TABLE IF EXISTS `forum_delivery`;
CREATE TABLE `forum_delivery` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `code` varchar(255) DEFAULT NULL COMMENT '快递公司代码',
  `sn` varchar(255) DEFAULT NULL COMMENT '快递单号',
  `add_time` int(10) DEFAULT NULL COMMENT '添加时间',
  `last_time` int(10) DEFAULT NULL COMMENT '上次查看时间',
  `state` int(1) DEFAULT NULL COMMENT '单号状态',
  `log_name` varchar(255) DEFAULT NULL COMMENT '物流公司名字',
  `end_time` int(10) DEFAULT NULL COMMENT '物流最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_delivery
-- ----------------------------
INSERT INTO `forum_delivery` VALUES ('1', '米air', 'shunfeng', '236738349760', '1502894475', '1503067495', '3', '顺丰', null);
INSERT INTO `forum_delivery` VALUES ('2', '米air贴纸', 'shentong', '3336346596198', '1503109012', '1503110776', '0', '申通', null);

-- ----------------------------
-- Table structure for forum_email_auth
-- ----------------------------
DROP TABLE IF EXISTS `forum_email_auth`;
CREATE TABLE `forum_email_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `add_time` int(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_email_auth
-- ----------------------------

-- ----------------------------
-- Table structure for forum_ip
-- ----------------------------
DROP TABLE IF EXISTS `forum_ip`;
CREATE TABLE `forum_ip` (
  `id` int(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(255) DEFAULT NULL COMMENT 'ip地址',
  `info` text COMMENT 'ip对应信息',
  `add_time` int(12) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_ip
-- ----------------------------
INSERT INTO `forum_ip` VALUES ('1', '113.77.81.172', 'a:13:{s:7:\"country\";s:6:\"中国\";s:10:\"country_id\";s:2:\"CN\";s:4:\"area\";s:6:\"华南\";s:7:\"area_id\";s:6:\"800000\";s:6:\"region\";s:9:\"广东省\";s:9:\"region_id\";s:6:\"440000\";s:4:\"city\";s:9:\"东莞市\";s:7:\"city_id\";s:6:\"441900\";s:6:\"county\";s:0:\"\";s:9:\"county_id\";s:2:\"-1\";s:3:\"isp\";s:6:\"电信\";s:6:\"isp_id\";s:6:\"100017\";s:2:\"ip\";s:13:\"113.77.81.172\";}', '1504111817');

-- ----------------------------
-- Table structure for forum_logistics_company
-- ----------------------------
DROP TABLE IF EXISTS `forum_logistics_company`;
CREATE TABLE `forum_logistics_company` (
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
-- Records of forum_logistics_company
-- ----------------------------
INSERT INTO `forum_logistics_company` VALUES ('1', '澳大利亚邮政(英文结果）', 'auspost', '', '1502764220', '1', '0', '0', '澳大利亚邮政(英文结果）');
INSERT INTO `forum_logistics_company` VALUES ('2', 'AAE', 'aae', '', '1502764220', '1', '0', '0', 'AAE');
INSERT INTO `forum_logistics_company` VALUES ('3', '安信达', 'anxindakuaixi', '', '1502764220', '1', '0', '0', '安信达');
INSERT INTO `forum_logistics_company` VALUES ('4', '汇通快运', 'huitongkuaidi', '', '1502764220', '1', '0', '0', '汇通快运');
INSERT INTO `forum_logistics_company` VALUES ('5', '百福东方', 'baifudongfang', '', '1502764220', '1', '0', '0', '百福东方');
INSERT INTO `forum_logistics_company` VALUES ('6', 'BHT', 'bht', '', '1502764220', '1', '0', '0', 'BHT');
INSERT INTO `forum_logistics_company` VALUES ('7', '邮政小包（国内），邮政包裹（国内）、邮政国内给据（国内）', 'youzhengguonei', '', '1502764220', '1', '0', '0', '邮政小包（国内），邮政包裹（国内）、邮政国内给据（国内）');
INSERT INTO `forum_logistics_company` VALUES ('8', '邦送物流', 'bangsongwuliu', '', '1502764220', '1', '0', '0', '邦送物流');
INSERT INTO `forum_logistics_company` VALUES ('9', '希伊艾斯（CCES）', 'cces', '', '1502764220', '1', '0', '0', '希伊艾斯（CCES）');
INSERT INTO `forum_logistics_company` VALUES ('10', '中国东方（COE）', 'coe', '', '1502764220', '1', '0', '0', '中国东方（COE）');
INSERT INTO `forum_logistics_company` VALUES ('11', '传喜物流', 'chuanxiwuliu', '', '1502764220', '1', '0', '0', '传喜物流');
INSERT INTO `forum_logistics_company` VALUES ('12', '加拿大邮政Canada Post（英文结果）', 'canpost', '', '1502764220', '1', '0', '0', '加拿大邮政Canada Post（英文结果）');
INSERT INTO `forum_logistics_company` VALUES ('13', '加拿大邮政Canada Post(德文结果）', 'canpostfr', '', '1502764220', '1', '0', '0', '加拿大邮政Canada Post(德文结果）');
INSERT INTO `forum_logistics_company` VALUES ('14', '大田物流', 'datianwuliu', '', '1502764220', '1', '0', '0', '大田物流');
INSERT INTO `forum_logistics_company` VALUES ('15', '德邦物流', 'debangwuliu', '', '1502764220', '1', '0', '0', '德邦物流');
INSERT INTO `forum_logistics_company` VALUES ('16', 'DPEX', 'dpex', '', '1502764220', '1', '0', '0', 'DPEX');
INSERT INTO `forum_logistics_company` VALUES ('17', 'DHL-中国件-中文结果', 'dhl', '', '1502764220', '1', '0', '0', 'DHL-中国件-中文结果');
INSERT INTO `forum_logistics_company` VALUES ('18', 'DHL-国际件-英文结果', 'dhlen', '', '1502764220', '1', '0', '0', 'DHL-国际件-英文结果');
INSERT INTO `forum_logistics_company` VALUES ('19', 'DHL-德国件-德文结果（德国国内派、收的件）', 'dhlde', '', '1502764220', '1', '0', '0', 'DHL-德国件-德文结果（德国国内派、收的件）');
INSERT INTO `forum_logistics_company` VALUES ('20', 'D速快递', 'dsukuaidi', '', '1502764220', '1', '0', '0', 'D速快递');
INSERT INTO `forum_logistics_company` VALUES ('21', '递四方', 'disifang', '', '1502764220', '1', '0', '0', '递四方');
INSERT INTO `forum_logistics_company` VALUES ('22', 'E邮宝', 'ems', '', '1502764220', '1', '0', '0', 'E邮宝');
INSERT INTO `forum_logistics_company` VALUES ('23', 'EMS（英文结果）', 'emsen', '', '1502764220', '1', '0', '0', 'EMS（英文结果）');
INSERT INTO `forum_logistics_company` VALUES ('24', 'EMS-（中国-国际）件-中文结果/EMS-(China-International）-Chinese data', 'emsguoji', '', '1502764220', '1', '0', '0', 'EMS-（中国-国际）件-中文结果/EMS-(China-International）-Chinese data');
INSERT INTO `forum_logistics_company` VALUES ('25', 'EMS-（中国-国际）件-英文结果/EMS-(China-International）-Englilsh data', 'emsinten', '', '1502764220', '1', '0', '0', 'EMS-（中国-国际）件-英文结果/EMS-(China-International）-Englilsh data');
INSERT INTO `forum_logistics_company` VALUES ('26', 'Fedex-国际件-英文结果（说明：Fedex是国际件的英文结果，Fedex中国的请用“lianbangkuaidi”，Fedex-美国请用fedexus）', 'fedex', '', '1502764220', '1', '0', '0', 'Fedex-国际件-英文结果（说明：Fedex是国际件的英文结果，Fedex中国的请用“lianbangkuaidi”，Fedex-美国请用fedexus）');
INSERT INTO `forum_logistics_company` VALUES ('27', 'Fedex-国际件-中文结果', 'fedexcn', '', '1502764220', '1', '0', '0', 'Fedex-国际件-中文结果');
INSERT INTO `forum_logistics_company` VALUES ('28', 'Fedex-美国件-英文结果(说明：如果无效，请偿试使用fedex）', 'fedexus', '', '1502764220', '1', '0', '0', 'Fedex-美国件-英文结果(说明：如果无效，请偿试使用fedex）');
INSERT INTO `forum_logistics_company` VALUES ('29', '飞康达物流', 'feikangda', '', '1502764220', '1', '0', '0', '飞康达物流');
INSERT INTO `forum_logistics_company` VALUES ('30', '飞快达', 'feikuaida', '', '1502764220', '1', '0', '0', '飞快达');
INSERT INTO `forum_logistics_company` VALUES ('31', '如风达快递', 'rufengda', '', '1502764220', '1', '0', '0', '如风达快递');
INSERT INTO `forum_logistics_company` VALUES ('32', '风行天下', 'fengxingtianxia', '', '1502764220', '1', '0', '0', '风行天下');
INSERT INTO `forum_logistics_company` VALUES ('33', '飞豹快递', 'feibaokuaidi', '', '1502764220', '1', '0', '0', '飞豹快递');
INSERT INTO `forum_logistics_company` VALUES ('34', '港中能达', 'ganzhongnengda', '', '1502764220', '1', '0', '0', '港中能达');
INSERT INTO `forum_logistics_company` VALUES ('35', '国通快递', 'guotongkuaidi', '', '1502764220', '1', '0', '0', '国通快递');
INSERT INTO `forum_logistics_company` VALUES ('36', '广东邮政', 'guangdongyouzhengwuliu', '', '1502764220', '1', '0', '0', '广东邮政');
INSERT INTO `forum_logistics_company` VALUES ('37', '邮政小包（国际），邮政包裹（国际）、邮政国内给据（国际）', 'youzhengguoji', '', '1502764220', '1', '0', '0', '邮政小包（国际），邮政包裹（国际）、邮政国内给据（国际）');
INSERT INTO `forum_logistics_company` VALUES ('38', 'GLS', 'gls', '', '1502764220', '1', '0', '0', 'GLS');
INSERT INTO `forum_logistics_company` VALUES ('39', '共速达', 'gongsuda', '', '1502764220', '1', '0', '0', '共速达');
INSERT INTO `forum_logistics_company` VALUES ('40', '汇强快递', 'huiqiangkuaidi', '', '1502764220', '1', '0', '0', '汇强快递');
INSERT INTO `forum_logistics_company` VALUES ('41', '天地华宇', 'tiandihuayu', '', '1502764220', '1', '0', '0', '天地华宇');
INSERT INTO `forum_logistics_company` VALUES ('42', '恒路物流', 'hengluwuliu', '', '1502764220', '1', '0', '0', '恒路物流');
INSERT INTO `forum_logistics_company` VALUES ('43', '华夏龙', 'huaxialongwuliu', '', '1502764220', '1', '0', '0', '华夏龙');
INSERT INTO `forum_logistics_company` VALUES ('44', '天天快递', 'tiantian', '', '1502764220', '1', '0', '0', '天天快递');
INSERT INTO `forum_logistics_company` VALUES ('45', '海外环球', 'haiwaihuanqiu', '', '1502764220', '1', '0', '0', '海外环球');
INSERT INTO `forum_logistics_company` VALUES ('46', '河北建华', 'hebeijianhua', '', '1502764220', '1', '0', '0', '河北建华（暂只能查好乐买的单，其他商家要查，请发邮件至 wensheng_chen#kingdee.com(将#替换成@)开通权限');
INSERT INTO `forum_logistics_company` VALUES ('47', '海盟速递', 'haimengsudi', '', '1502764220', '1', '0', '0', '海盟速递');
INSERT INTO `forum_logistics_company` VALUES ('48', '华企快运', 'huaqikuaiyun', '', '1502764220', '1', '0', '0', '华企快运');
INSERT INTO `forum_logistics_company` VALUES ('49', '山东海红', 'haihongwangsong', '', '1502764220', '1', '0', '0', '山东海红');
INSERT INTO `forum_logistics_company` VALUES ('50', '佳吉物流', 'jiajiwuliu', '', '1502764220', '1', '0', '0', '佳吉物流');
INSERT INTO `forum_logistics_company` VALUES ('51', '佳怡物流', 'jiayiwuliu', '', '1502764220', '1', '0', '0', '佳怡物流');
INSERT INTO `forum_logistics_company` VALUES ('52', '加运美', 'jiayunmeiwuliu', '', '1502764220', '1', '0', '0', '加运美');
INSERT INTO `forum_logistics_company` VALUES ('53', '京广速递', 'jinguangsudikuaijian', '', '1502764220', '1', '0', '0', '京广速递');
INSERT INTO `forum_logistics_company` VALUES ('54', '急先达', 'jixianda', '', '1502764220', '1', '0', '0', '急先达');
INSERT INTO `forum_logistics_company` VALUES ('55', '晋越快递', 'jinyuekuaidi', '', '1502764220', '1', '0', '0', '晋越快递');
INSERT INTO `forum_logistics_company` VALUES ('56', '捷特快递', 'jietekuaidi', '', '1502764220', '1', '0', '0', '捷特快递');
INSERT INTO `forum_logistics_company` VALUES ('57', '金大物流', 'jindawuliu', '', '1502764220', '1', '0', '0', '金大物流');
INSERT INTO `forum_logistics_company` VALUES ('58', '嘉里大通', 'jialidatong', '', '1502764220', '1', '0', '0', '嘉里大通');
INSERT INTO `forum_logistics_company` VALUES ('59', '快捷速递', 'kuaijiesudi', '', '1502764220', '1', '0', '0', '快捷速递');
INSERT INTO `forum_logistics_company` VALUES ('60', '康力物流', 'kangliwuliu', '', '1502764220', '1', '0', '0', '康力物流');
INSERT INTO `forum_logistics_company` VALUES ('61', '跨越物流', 'kuayue', '', '1502764220', '1', '0', '0', '跨越物流');
INSERT INTO `forum_logistics_company` VALUES ('62', '联昊通', 'lianhaowuliu', '', '1502764220', '1', '0', '0', '联昊通');
INSERT INTO `forum_logistics_company` VALUES ('63', '龙邦物流', 'longbanwuliu', '', '1502764220', '1', '0', '0', '龙邦物流');
INSERT INTO `forum_logistics_company` VALUES ('64', '蓝镖快递', 'lanbiaokuaidi', '', '1502764220', '1', '0', '0', '蓝镖快递');
INSERT INTO `forum_logistics_company` VALUES ('65', '乐捷递', 'lejiedi', '', '1502764220', '1', '0', '0', '乐捷递（暂只能查好乐买的单，其他商家要查，请发邮件至 wensheng_chen#kingdee.com(将#替换成@)开通权限');
INSERT INTO `forum_logistics_company` VALUES ('66', '联邦快递（Fedex-中国-中文结果）（说明：国外的请用 fedex）', 'lianbangkuaidi', '', '1502764220', '1', '0', '0', '联邦快递（Fedex-中国-中文结果）（说明：国外的请用 fedex）');
INSERT INTO `forum_logistics_company` VALUES ('67', '联邦快递(Fedex-中国-英文结果）', 'lianbangkuaidien', '', '1502764220', '1', '0', '0', '联邦快递(Fedex-中国-英文结果）');
INSERT INTO `forum_logistics_company` VALUES ('68', '立即送', 'lijisong', '', '1502764220', '1', '0', '0', '立即送（暂只能查好乐买的单，其他商家要查，请发邮件至 wensheng_chen#kingdee.com(将#替换成@)开通权限)');
INSERT INTO `forum_logistics_company` VALUES ('69', '隆浪快递', 'longlangkuaidi', '', '1502764220', '1', '0', '0', '隆浪快递');
INSERT INTO `forum_logistics_company` VALUES ('70', '门对门', 'menduimen', '', '1502764220', '1', '0', '0', '门对门');
INSERT INTO `forum_logistics_company` VALUES ('71', '美国快递', 'meiguokuaidi', '', '1502764220', '1', '0', '0', '美国快递');
INSERT INTO `forum_logistics_company` VALUES ('72', '明亮物流', 'mingliangwuliu', '', '1502764220', '1', '0', '0', '明亮物流');
INSERT INTO `forum_logistics_company` VALUES ('73', 'OCS', 'ocs', '', '1502764220', '1', '0', '0', 'OCS');
INSERT INTO `forum_logistics_company` VALUES ('74', 'onTrac', 'ontrac', '', '1502764220', '1', '0', '0', 'onTrac');
INSERT INTO `forum_logistics_company` VALUES ('75', '全晨快递', 'quanchenkuaidi', '', '1502764220', '1', '0', '0', '全晨快递');
INSERT INTO `forum_logistics_company` VALUES ('76', '全际通', 'quanjitong', '', '1502764220', '1', '0', '0', '全际通');
INSERT INTO `forum_logistics_company` VALUES ('77', '全日通', 'quanritongkuaidi', '', '1502764220', '1', '0', '0', '全日通');
INSERT INTO `forum_logistics_company` VALUES ('78', '全一快递', 'quanyikuaidi', '', '1502764220', '1', '0', '0', '全一快递');
INSERT INTO `forum_logistics_company` VALUES ('79', '全峰快递', 'quanfengkuaidi', '', '1502764220', '1', '0', '0', '全峰快递');
INSERT INTO `forum_logistics_company` VALUES ('80', '七天连锁', 'sevendays', '', '1502764220', '1', '0', '0', '七天连锁');
INSERT INTO `forum_logistics_company` VALUES ('81', '申通', 'shentong', '', '1502764220', '1', '0', '3', '申通');
INSERT INTO `forum_logistics_company` VALUES ('82', '顺丰', 'shunfeng', '', '1502764220', '1', '0', '1', '顺丰');
INSERT INTO `forum_logistics_company` VALUES ('83', '顺丰（英文结果）', 'shunfengen', '', '1502764220', '1', '0', '0', '顺丰（英文结果）');
INSERT INTO `forum_logistics_company` VALUES ('84', '三态速递', 'santaisudi', '', '1502764220', '1', '0', '0', '三态速递');
INSERT INTO `forum_logistics_company` VALUES ('85', '盛辉物流', 'shenghuiwuliu', '', '1502764220', '1', '0', '0', '盛辉物流');
INSERT INTO `forum_logistics_company` VALUES ('86', '速尔物流', 'suer', '', '1502764220', '1', '0', '0', '速尔物流');
INSERT INTO `forum_logistics_company` VALUES ('87', '盛丰物流', 'shengfengwuliu', '', '1502764220', '1', '0', '0', '盛丰物流');
INSERT INTO `forum_logistics_company` VALUES ('88', '上大物流', 'shangda', '', '1502764220', '1', '0', '0', '上大物流');
INSERT INTO `forum_logistics_company` VALUES ('89', '赛澳递', 'saiaodi', '', '1502764220', '1', '0', '0', '赛澳递');
INSERT INTO `forum_logistics_company` VALUES ('90', '山西红马甲', 'sxhongmajia', '', '1502764220', '1', '0', '0', '山西红马甲');
INSERT INTO `forum_logistics_company` VALUES ('91', '圣安物流', 'shenganwuliu', '', '1502764220', '1', '0', '0', '圣安物流');
INSERT INTO `forum_logistics_company` VALUES ('92', '穗佳物流', 'suijiawuliu', '', '1502764220', '1', '0', '0', '穗佳物流');
INSERT INTO `forum_logistics_company` VALUES ('93', 'TNT（中文结果）', 'tnt', '', '1502764220', '1', '0', '0', 'TNT（中文结果）');
INSERT INTO `forum_logistics_company` VALUES ('94', 'TNT（英文结果）', 'tnten', '', '1502764220', '1', '0', '0', 'TNT（英文结果）');
INSERT INTO `forum_logistics_company` VALUES ('95', '通和天下', 'tonghetianxia', '', '1502764220', '1', '0', '0', '通和天下');
INSERT INTO `forum_logistics_company` VALUES ('96', 'UPS（中文结果）', 'ups', '', '1502764220', '1', '0', '0', 'UPS（中文结果）');
INSERT INTO `forum_logistics_company` VALUES ('97', 'UPS（英文结果）', 'upsen', '', '1502764220', '1', '0', '0', 'UPS（英文结果）');
INSERT INTO `forum_logistics_company` VALUES ('98', '优速物流', 'youshuwuliu', '', '1502764220', '1', '0', '0', '优速物流');
INSERT INTO `forum_logistics_company` VALUES ('99', 'USPS（中英文）', 'usps', '', '1502764220', '1', '0', '0', 'USPS（中英文）');
INSERT INTO `forum_logistics_company` VALUES ('100', '万家物流', 'wanjiawuliu', '', '1502764220', '1', '0', '0', '万家物流');
INSERT INTO `forum_logistics_company` VALUES ('101', '万象物流', 'wanxiangwuliu', '', '1502764220', '1', '0', '0', '万象物流');
INSERT INTO `forum_logistics_company` VALUES ('102', '微特派', 'weitepai', '', '1502764220', '1', '0', '0', '微特派');
INSERT INTO `forum_logistics_company` VALUES ('103', '新邦物流', 'xinbangwuliu', '', '1502764220', '1', '0', '0', '新邦物流');
INSERT INTO `forum_logistics_company` VALUES ('104', '信丰物流', 'xinfengwuliu', '', '1502764220', '1', '0', '0', '信丰物流');
INSERT INTO `forum_logistics_company` VALUES ('105', '新蛋奥硕物流', 'neweggozzo', '', '1502764220', '1', '0', '0', '新蛋奥硕物流');
INSERT INTO `forum_logistics_company` VALUES ('106', '香港邮政', 'hkpost', '', '1502764220', '1', '0', '0', '香港邮政');
INSERT INTO `forum_logistics_company` VALUES ('107', '圆通速递', 'yuantong', '', '1502764220', '1', '0', '0', '圆通速递');
INSERT INTO `forum_logistics_company` VALUES ('108', '韵达快运', 'yunda', '', '1502764220', '1', '0', '0', '韵达快运');
INSERT INTO `forum_logistics_company` VALUES ('109', '运通快递', 'yuntongkuaidi', '', '1502764220', '1', '0', '0', '运通快递');
INSERT INTO `forum_logistics_company` VALUES ('110', '远成物流', 'yuanchengwuliu', '', '1502764220', '1', '0', '0', '远成物流');
INSERT INTO `forum_logistics_company` VALUES ('111', '亚风速递', 'yafengsudi', '', '1502764220', '1', '0', '0', '亚风速递');
INSERT INTO `forum_logistics_company` VALUES ('112', '一邦速递', 'yibangwuliu', '', '1502764220', '1', '0', '0', '一邦速递');
INSERT INTO `forum_logistics_company` VALUES ('113', '源伟丰快递', 'yuanweifeng', '', '1502764220', '1', '0', '0', '源伟丰快递');
INSERT INTO `forum_logistics_company` VALUES ('114', '元智捷诚', 'yuanzhijiecheng', '', '1502764220', '1', '0', '0', '元智捷诚');
INSERT INTO `forum_logistics_company` VALUES ('115', '越丰物流', 'yuefengwuliu', '', '1502764220', '1', '0', '0', '越丰物流');
INSERT INTO `forum_logistics_company` VALUES ('116', '源安达', 'yuananda', '', '1502764220', '1', '0', '0', '源安达');
INSERT INTO `forum_logistics_company` VALUES ('117', '原飞航', 'yuanfeihangwuliu', '', '1502764220', '1', '0', '0', '原飞航');
INSERT INTO `forum_logistics_company` VALUES ('118', '忠信达', 'zhongxinda', '', '1502764220', '1', '0', '0', '忠信达');
INSERT INTO `forum_logistics_company` VALUES ('119', '芝麻开门', 'zhimakaimen', '', '1502764220', '1', '0', '0', '芝麻开门');
INSERT INTO `forum_logistics_company` VALUES ('120', '银捷速递', 'yinjiesudi', '', '1502764220', '1', '0', '0', '银捷速递');
INSERT INTO `forum_logistics_company` VALUES ('121', '一统飞鸿', 'yitongfeihong', '', '1502764220', '1', '0', '0', '一统飞鸿');
INSERT INTO `forum_logistics_company` VALUES ('122', '中通速递', 'zhongtong', '', '1502764220', '1', '0', '0', '中通速递');
INSERT INTO `forum_logistics_company` VALUES ('123', '宅急送', 'zhaijisong', '', '1502764220', '1', '0', '0', '宅急送');
INSERT INTO `forum_logistics_company` VALUES ('124', '中邮物流', 'zhongyouwuliu', '', '1502764220', '1', '0', '0', '中邮物流');
INSERT INTO `forum_logistics_company` VALUES ('125', '中速快件', 'zhongsukuaidi', '', '1502764220', '1', '0', '0', '中速快件');
INSERT INTO `forum_logistics_company` VALUES ('126', '郑州建华', 'zhengzhoujianhua', '', '1502764220', '1', '0', '0', '郑州建华');
INSERT INTO `forum_logistics_company` VALUES ('127', '中天万运', 'zhongtianwanyun', '', '1502764220', '1', '0', '0', '中天万运');

-- ----------------------------
-- Table structure for forum_reply
-- ----------------------------
DROP TABLE IF EXISTS `forum_reply`;
CREATE TABLE `forum_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `topic_id` int(10) NOT NULL DEFAULT '0',
  `reply_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `reply_name` varchar(32) NOT NULL,
  `reply_email` varchar(100) NOT NULL,
  `reply_detail` text NOT NULL,
  `reply_pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `reply_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of forum_reply
-- ----------------------------
INSERT INTO `forum_reply` VALUES ('68', '61', '1', null, 'admin', '', 'first test OK', '', '2016-10-12 12:57:58');
INSERT INTO `forum_reply` VALUES ('71', '61', '2', null, 'admin', '', '第二条回复', '', '2016-10-12 13:24:40');
INSERT INTO `forum_reply` VALUES ('72', '61', '3', null, 'admin', '', '第三条回复', '', '2016-10-12 13:24:45');
INSERT INTO `forum_reply` VALUES ('73', '61', '4', null, 'admin', '', '第四条回复', '', '2016-10-12 13:25:00');
INSERT INTO `forum_reply` VALUES ('74', '61', '5', null, 'admin', '', '第五条回复', '', '2016-10-12 13:25:07');
INSERT INTO `forum_reply` VALUES ('75', '61', '6', null, 'admin', '', '6', '', '2016-10-12 13:35:20');
INSERT INTO `forum_reply` VALUES ('76', '61', '7', null, 'admin', '', '7', '', '2016-10-12 13:35:24');
INSERT INTO `forum_reply` VALUES ('77', '61', '8', null, 'admin', '', '8', '', '2016-10-12 13:35:27');
INSERT INTO `forum_reply` VALUES ('78', '61', '9', null, 'admin', '', '9', '', '2016-10-12 13:35:29');
INSERT INTO `forum_reply` VALUES ('79', '61', '10', null, 'admin', '', '10', '', '2016-10-12 13:35:32');
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
INSERT INTO `forum_reply` VALUES ('124', '112', '2', '4', 'hm081333', '522751485@qq.com', '123', '', '2017-08-09 22:11:02');
INSERT INTO `forum_reply` VALUES ('125', '112', '3', '4', 'hm081333', '522751485@qq.com', '123', '', '2017-08-09 22:11:16');
INSERT INTO `forum_reply` VALUES ('126', '112', '4', '4', 'hm081333', '522751485@qq.com', '123', 'pics/1502288504.jpg', '2017-08-09 22:21:45');

-- ----------------------------
-- Table structure for forum_sms_auth
-- ----------------------------
DROP TABLE IF EXISTS `forum_sms_auth`;
CREATE TABLE `forum_sms_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `add_time` int(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_sms_auth
-- ----------------------------

-- ----------------------------
-- Table structure for forum_topic
-- ----------------------------
DROP TABLE IF EXISTS `forum_topic`;
CREATE TABLE `forum_topic` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `class_id` int(10) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  `pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(10) DEFAULT '0',
  `name` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `view` int(10) NOT NULL DEFAULT '0',
  `reply` int(10) NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_topic
-- ----------------------------
INSERT INTO `forum_topic` VALUES ('61', '1', 'first test', 'first test', '', '0', 'admin', '', '2016-10-12 12:27:22', '98', '16', '0');
INSERT INTO `forum_topic` VALUES ('68', '1', '新用户尝试发帖', '新用户尝试发帖图片内容', 'pics/1476281421.png', '34', '123', '123@123.123', '2016-10-12 22:10:21', '23', '0', '0');
INSERT INTO `forum_topic` VALUES ('89', '1', '手机尝试发图贴', '第一次尝试', 'pics/1476369877.jpeg', '34', '123', '123@123.123', '2016-10-13 22:44:37', '29', '2', '0');
INSERT INTO `forum_topic` VALUES ('95', '2', '123', '123', '', '0', 'admin', '', '2016-10-24 19:04:22', '11', '0', '0');
INSERT INTO `forum_topic` VALUES ('98', '1', '管理员第一次尝试发帖', '管理员第一次尝试发帖', 'pics/1477319193.jpg', '1', '管理员', '', '2016-10-24 22:26:33', '31', '0', '1');
INSERT INTO `forum_topic` VALUES ('99', '2', '管理员第二次尝试发帖', '管理员第二次尝试发帖', 'pics/1477319322.jpg', '1', '管理员', '', '2016-10-24 22:28:42', '3', '0', '1');
INSERT INTO `forum_topic` VALUES ('101', '2', '修改用户权限验证方式', '修改用户权限验证方式', 'pics/1477540278.jpg', '4', 'hm081333', '522751485@qq.com', '2016-10-27 11:51:18', '15', '0', '1');
INSERT INTO `forum_topic` VALUES ('104', '1', '更新支持php7', '2017年5月12号 凌晨1点03分写的代码', '', '4', 'hm081333', '522751485@qq.com', '2017-05-12 01:04:09', '0', '3', '1');
INSERT INTO `forum_topic` VALUES ('105', '1', '替换，使用phalapi框架作为核心', 'test123', 'pics/1496039683.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-29 14:34:43', '0', '0', '1');
INSERT INTO `forum_topic` VALUES ('106', '1', '把前台转移去phalapi ing', '工程有点大。。。', 'pics/1496039732.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-29 14:35:32', '0', '0', '1');
INSERT INTO `forum_topic` VALUES ('107', '1', '把前台转移去phalapi ing', '工程有点大。。。', 'pics/1496039733.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-29 14:35:33', '19', '0', '1');
INSERT INTO `forum_topic` VALUES ('108', '1', 'phalapi尝试添加新帖', 'phalapi尝试添加新帖', 'pics/1496081698.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-30 02:16:59', '2', '0', '1');
INSERT INTO `forum_topic` VALUES ('110', '1', 'phalapi尝试添加新帖', 'phalapi尝试添加新帖', 'pics/1496081920.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-30 02:18:41', '64', '0', '1');
INSERT INTO `forum_topic` VALUES ('111', '1', 'PhalApi前台基本完工', 'PhalApi前台基本完工', 'pics/1496138646.jpg', '4', 'hm081333', '522751485@qq.com', '2017-05-30 18:04:07', '3', '0', '1');
INSERT INTO `forum_topic` VALUES ('112', '1', 'phalapi.版尝试手机上传图片', '传图片', 'pics/1496139940.png', '4', 'hm081333', '522751485@qq.com', '2017-05-30 18:25:41', '52', '0', '1');
INSERT INTO `forum_topic` VALUES ('113', '16', 'PhalApi框架进度', '后台创建新帖成功', 'pics/1496412596.jpg', '0', '管理员', '', '2017-06-02 22:09:57', '8', '0', '1');

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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forum_user
-- ----------------------------
INSERT INTO `forum_user` VALUES ('4', 'hm081333', '$2y$10$ww7CvAzywm63TrgxAc5LjO5LbCj5Qk/NrEId5QdWkEerXHcZVidhq', '522751485@qq.com', '何朗义', '1', '2016-10-27 11:32:20');
INSERT INTO `forum_user` VALUES ('34', '123', '$2y$10$3WwirmQkC76hGz7ICMA8jOk3LS7lNtZ/QoTxZEEDKUj6e/stXMzMS', '123', '', '1', '2016-10-31 22:12:46');
INSERT INTO `forum_user` VALUES ('36', '1231', '$2y$10$Js2EeIpCJb2ulZULvJPpQuwhpCW2R.U6lK3R/CWFZYbf0n4E.X1PS', '123@123.1231', '123', '1', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('37', 'LYi-Ho', '$2y$10$XamG9q35OPsGYNg7s2m5EesZbcE3fs6k5qvqfK1rBP0oAm9X141YG', '503214851@qq.com', '何朗义', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('38', '1234', '$2y$10$WxpRJRo8SDwyTusqFOXqUuNm.HMOU4LFZG/0wvM6Frg71jxSJJtZO', '1234@1234.1234', '', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('39', '12345', '$2y$10$cOTwR/BJeEmGC0JdimzPxukMnuKni/7knA6qoqsb/4zxtToW7aaF.', '1234@1234.123', '', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('40', '123456', '$2y$10$XGx55oG4i4OqpiVGwPQVJupgkLhvb9cXLnL3XUewcOzHMtH2GlG3C', '1234@1234.12345', '', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('41', 'test', '$2y$10$k1U76hKD7SaNevJSz2bw2OeKbbaxZCDTfXrnmk9DO2UdZTVtb75UO', 'test@test.test', '', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('42', 'qwe', '$2y$10$1wFHILZnaOfLXlM/k4YnquNRVri56TDmsMPScARA9Esenly10mU6a', 'qwe@qwe.qwe', 'qwe', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('43', 'qqq', '$2y$10$q.g8ZKcIrxcCFaUFvzQctOsmV3mPYQMSmHd5A35oDoJsXJ0ClADTu', 'qqq@qqq.qqq', 'qqq', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('44', 'asd', '$2y$10$XFML6DA3Bd0g.WzsRMXjReHihyFLOsLtTT.f0URVWBWs6.QgS4Uvi', 'asd@asd.asd', 'asd', '0', '0000-00-00 00:00:00');
INSERT INTO `forum_user` VALUES ('45', 'test', '$2y$10$cDj6AUspm5kmPDd6d6sGRua.ArmlTne8pIeIpq16Rt8LIZ.jvC/AG', '', '', '1', '0000-00-00 00:00:00');
SET FOREIGN_KEY_CHECKS=1;
