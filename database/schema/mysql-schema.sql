/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `ly_baidu_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_baidu_ids` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='百度ID表';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ly_failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ly_failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ly_fund_net_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_fund_net_values` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fund_id` bigint(20) unsigned NOT NULL COMMENT '基金ID',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金代码',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金名称',
  `unit_net_value` decimal(10,4) NOT NULL COMMENT '单位净值',
  `cumulative_net_value` decimal(10,4) NOT NULL COMMENT '累计净值',
  `net_value_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '基金净值时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ly_fund_net_values_fund_id_net_value_time_index` (`fund_id`,`net_value_time`),
  KEY `ly_fund_net_values_code_net_value_time_index` (`code`,`net_value_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='基金净值表';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ly_fund_valuations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_fund_valuations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fund_id` bigint(20) unsigned NOT NULL COMMENT '基金ID',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金代码',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金名称',
  `unit_net_value` decimal(10,4) NOT NULL COMMENT '单位净值',
  `estimated_net_value` decimal(10,4) NOT NULL COMMENT '预估净值',
  `estimated_growth` decimal(10,4) NOT NULL COMMENT '预估增长值',
  `estimated_growth_rate` decimal(10,4) NOT NULL COMMENT '预估增长率',
  `valuation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '基金估值时间',
  `valuation_source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金估值来源',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ly_fund_valuations_fund_id_valuation_time_valuation_source_index` (`fund_id`,`valuation_time`,`valuation_source`),
  KEY `ly_fund_valuations_code_valuation_time_valuation_source_index` (`code`,`valuation_time`,`valuation_source`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='基金估值表';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ly_funds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_funds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金代码',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金名称',
  `pinyin_initial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金名称拼音首字母',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金类型',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ly_funds_code_index` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='基金表';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ly_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ly_personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ly_personal_access_tokens_token_unique` (`token`),
  KEY `ly_personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ly_websockets_statistics_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ly_websockets_statistics_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peak_connection_count` int(11) NOT NULL,
  `websocket_message_count` int(11) NOT NULL,
  `api_message_count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `ly_migrations` VALUES (22,'0000_00_00_000000_create_websockets_statistics_entries_table',1);
INSERT INTO `ly_migrations` VALUES (23,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `ly_migrations` VALUES (24,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `ly_migrations` VALUES (25,'2023_04_12_160621_create_baidu_ids_table',1);
INSERT INTO `ly_migrations` VALUES (26,'2023_08_16_043842_create_fund_valuations_table',1);
INSERT INTO `ly_migrations` VALUES (27,'2023_08_16_132102_create_funds_table',1);
INSERT INTO `ly_migrations` VALUES (28,'2023_08_16_135855_create_fund_net_values_table',1);
