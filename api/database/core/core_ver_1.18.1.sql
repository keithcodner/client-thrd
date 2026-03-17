-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.40 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for thrd
DROP DATABASE IF EXISTS `thrd`;
CREATE DATABASE IF NOT EXISTS `thrd` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `thrd`;

-- Dumping structure for table thrd.address
DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `addr_street` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_zip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_postal_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_state` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_phone_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_area_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_street_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_apart_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_po_box` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_floor_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_suite` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.address: ~0 rows (approximately)
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
/*!40000 ALTER TABLE `address` ENABLE KEYS */;

-- Dumping structure for table thrd.admin_activity_logs
DROP TABLE IF EXISTS `admin_activity_logs`;
CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` bigint unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_activity_logs_admin_id_created_at_index` (`admin_id`,`created_at`),
  KEY `admin_activity_logs_target_type_target_id_index` (`target_type`,`target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.admin_activity_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `admin_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_activity_logs` ENABLE KEYS */;

-- Dumping structure for table thrd.answers
DROP TABLE IF EXISTS `answers`;
CREATE TABLE IF NOT EXISTS `answers` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `ua_ui_id` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ua_description` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ua_likes` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ua_dislikes` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ua_shares` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ua_date_created` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.answers: ~0 rows (approximately)
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;

-- Dumping structure for table thrd.articles
DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `file_store_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `link` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `articles_user_id_index` (`user_id`),
  KEY `articles_status_index` (`status`),
  KEY `articles_type_index` (`type`),
  KEY `articles_created_at_index` (`created_at`),
  CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.articles: ~0 rows (approximately)
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;

-- Dumping structure for table thrd.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.cache: ~8 rows (approximately)
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('thrd-cache-b39efed6f405d9a00d43b2e6f311148aa7403250', 'i:1;', 1772001061),
	('thrd-cache-b39efed6f405d9a00d43b2e6f311148aa7403250:timer', 'i:1772001061;', 1772001061),
	('thrd-cache-codnerk@gmail.com|10.0.0.12', 'i:2;', 1773717975),
	('thrd-cache-codnerk@gmail.com|10.0.0.12:timer', 'i:1773717975;', 1773717975),
	('thrd-cache-codnerkj@gmail.com|10.0.0.12', 'i:2;', 1771997591),
	('thrd-cache-codnerkj@gmail.com|10.0.0.12:timer', 'i:1771997591;', 1771997591),
	('thrd-cache-test@test.com|10.0.0.12', 'i:1;', 1773718071),
	('thrd-cache-test@test.com|10.0.0.12:timer', 'i:1773718071;', 1773718071);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;

-- Dumping structure for table thrd.cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.cache_locks: ~0 rows (approximately)
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;

-- Dumping structure for table thrd.carts
DROP TABLE IF EXISTS `carts`;
CREATE TABLE IF NOT EXISTS `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `cart_an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.carts: ~0 rows (approximately)
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;

-- Dumping structure for table thrd.circles
DROP TABLE IF EXISTS `circles`;
CREATE TABLE IF NOT EXISTS `circles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_owner_id` int unsigned DEFAULT NULL,
  `name` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='- table the tracks who owns the circle group\r\n- types: standard\r\n- status: active, in-active';

-- Dumping data for table thrd.circles: ~0 rows (approximately)
/*!40000 ALTER TABLE `circles` DISABLE KEYS */;
/*!40000 ALTER TABLE `circles` ENABLE KEYS */;

-- Dumping structure for table thrd.circles_details
DROP TABLE IF EXISTS `circles_details`;
CREATE TABLE IF NOT EXISTS `circles_details` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `circle_id` int unsigned DEFAULT NULL,
  `circle_idea_board_id` int unsigned DEFAULT NULL,
  `file_store_circle_an_id` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_store_circle_bg_img_an_id` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tone_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transparency_percent` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blur_depth_value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privacy_state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_circles_details_circles` (`circle_id`),
  KEY `FK_circles_details_circles_idea_board` (`circle_idea_board_id`),
  CONSTRAINT `FK_circles_details_circles` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_circles_details_circles_idea_board` FOREIGN KEY (`circle_idea_board_id`) REFERENCES `circles_idea_board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='- table the tracks details of a circle\r\n- circle_id refs circle table\r\n- circle_idea_board_id refs circle_idea_board table\r\n- style_code: sage, stone, clay, sand, dusk, amber';

-- Dumping data for table thrd.circles_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `circles_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `circles_details` ENABLE KEYS */;

-- Dumping structure for table thrd.circles_idea_board
DROP TABLE IF EXISTS `circles_idea_board`;
CREATE TABLE IF NOT EXISTS `circles_idea_board` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `circle_id` int unsigned DEFAULT NULL,
  `file_store_circle_an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_circles_idea_board_circles` (`circle_id`),
  CONSTRAINT `FK_circles_idea_board_circles` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='- table the tracks what users post to a particular circle idea board';

-- Dumping data for table thrd.circles_idea_board: ~0 rows (approximately)
/*!40000 ALTER TABLE `circles_idea_board` DISABLE KEYS */;
/*!40000 ALTER TABLE `circles_idea_board` ENABLE KEYS */;

-- Dumping structure for table thrd.circles_idea_board_posts
DROP TABLE IF EXISTS `circles_idea_board_posts`;
CREATE TABLE IF NOT EXISTS `circles_idea_board_posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `circles_idea_board_id` int unsigned DEFAULT NULL,
  `user_owner_id` bigint unsigned DEFAULT NULL,
  `name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_circles_idea_board_posts_circles_idea_board` (`circles_idea_board_id`),
  KEY `FK_circles_idea_board_posts_users` (`user_owner_id`),
  CONSTRAINT `FK_circles_idea_board_posts_circles_idea_board` FOREIGN KEY (`circles_idea_board_id`) REFERENCES `circles_idea_board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_circles_idea_board_posts_users` FOREIGN KEY (`user_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='- table the tracks who owns the groups\r\n- type: note, image, link';

-- Dumping data for table thrd.circles_idea_board_posts: ~0 rows (approximately)
/*!40000 ALTER TABLE `circles_idea_board_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `circles_idea_board_posts` ENABLE KEYS */;

-- Dumping structure for table thrd.circles_member_tracker
DROP TABLE IF EXISTS `circles_member_tracker`;
CREATE TABLE IF NOT EXISTS `circles_member_tracker` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `circle_id` int unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_circles_member_tracker_circles` (`circle_id`),
  KEY `FK_circles_member_tracker_users` (`user_id`),
  CONSTRAINT `FK_circles_member_tracker_circles` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_circles_member_tracker_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='- table the tracks who joined which circles\r\n- circle_id is the circle being referenced';

-- Dumping data for table thrd.circles_member_tracker: ~0 rows (approximately)
/*!40000 ALTER TABLE `circles_member_tracker` DISABLE KEYS */;
/*!40000 ALTER TABLE `circles_member_tracker` ENABLE KEYS */;

-- Dumping structure for table thrd.circles_requests
DROP TABLE IF EXISTS `circles_requests`;
CREATE TABLE IF NOT EXISTS `circles_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `circle_id` int unsigned DEFAULT NULL,
  `requesting_to_join_user_id` bigint unsigned DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_circles_requests_circles` (`circle_id`),
  KEY `FK_circles_requests_users` (`requesting_to_join_user_id`),
  CONSTRAINT `FK_circles_requests_circles` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_circles_requests_users` FOREIGN KEY (`requesting_to_join_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='- table the tracks requests coming to the circle to join\r\n- circle id, is the circle n question\r\n- requesting_to_join_user_id is the user id requesting to join\r\n- status: incoming, accepted, declined, auto-joined\r\n- type: join_request\r\n';

-- Dumping data for table thrd.circles_requests: ~0 rows (approximately)
/*!40000 ALTER TABLE `circles_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `circles_requests` ENABLE KEYS */;

-- Dumping structure for table thrd.cities_canada
DROP TABLE IF EXISTS `cities_canada`;
CREATE TABLE IF NOT EXISTS `cities_canada` (
  `id` int DEFAULT NULL,
  `city` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `city_ascii` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `province_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lng` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `population` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `density` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ranking` int DEFAULT NULL,
  `postal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.cities_canada: ~0 rows (approximately)
/*!40000 ALTER TABLE `cities_canada` DISABLE KEYS */;
/*!40000 ALTER TABLE `cities_canada` ENABLE KEYS */;

-- Dumping structure for table thrd.cities_us
DROP TABLE IF EXISTS `cities_us`;
CREATE TABLE IF NOT EXISTS `cities_us` (
  `id` int NOT NULL AUTO_INCREMENT,
  `city` varchar(44) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_ascii` varchar(44) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_id` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `county_fips` int DEFAULT NULL,
  `county_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(7,4) DEFAULT NULL,
  `lng` decimal(9,4) DEFAULT NULL,
  `population` int DEFAULT NULL,
  `density` decimal(7,1) DEFAULT NULL,
  `source` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `military` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incorporated` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ranking` int DEFAULT NULL,
  `zips` varchar(1847) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.cities_us: ~0 rows (approximately)
/*!40000 ALTER TABLE `cities_us` DISABLE KEYS */;
/*!40000 ALTER TABLE `cities_us` ENABLE KEYS */;

-- Dumping structure for table thrd.comments
DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `post_id` bigint DEFAULT NULL,
  `circle_item_id` bigint DEFAULT NULL,
  `video_id` bigint DEFAULT NULL,
  `incident_id` bigint DEFAULT NULL,
  `pronetwork_group_profile_id` bigint DEFAULT NULL,
  `comm_an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_usr_an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_comment_unique_an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_reply_an_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_is_reply` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `comm_reply_parent_an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_dislike` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `comm_s_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'unread',
  `comm_like` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_ui_is_public` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_type_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_ui_is_read` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comm_email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.comments: ~0 rows (approximately)
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;

-- Dumping structure for table thrd.company_info
DROP TABLE IF EXISTS `company_info`;
CREATE TABLE IF NOT EXISTS `company_info` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `company_website` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `company_social_1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `company_social_2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `company_social_3` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `social_clicks_1` int DEFAULT NULL,
  `social_clicks_2` int DEFAULT NULL,
  `social_clicks_3` int DEFAULT NULL,
  `search_click` int DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.company_info: ~0 rows (approximately)
/*!40000 ALTER TABLE `company_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_info` ENABLE KEYS */;

-- Dumping structure for table thrd.content_approvals
DROP TABLE IF EXISTS `content_approvals`;
CREATE TABLE IF NOT EXISTS `content_approvals` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `content_reason_id` bigint DEFAULT NULL,
  `content_reason_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `approval_an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_human_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_second_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_third_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_op_1` varchar(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_op_2` varchar(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_op_3` varchar(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_op_4` varchar(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.content_approvals: ~0 rows (approximately)
/*!40000 ALTER TABLE `content_approvals` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_approvals` ENABLE KEYS */;

-- Dumping structure for table thrd.content_reporting
DROP TABLE IF EXISTS `content_reporting`;
CREATE TABLE IF NOT EXISTS `content_reporting` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `reason_id` bigint DEFAULT NULL,
  `incident_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `reporting_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporting_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporting_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporting_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `reporting_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporting_threshold` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporting_threshold_count` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporting_isAppealed` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.content_reporting: ~0 rows (approximately)
/*!40000 ALTER TABLE `content_reporting` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_reporting` ENABLE KEYS */;

-- Dumping structure for table thrd.content_reporting_appeals
DROP TABLE IF EXISTS `content_reporting_appeals`;
CREATE TABLE IF NOT EXISTS `content_reporting_appeals` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `content_report_id` bigint DEFAULT NULL,
  `report_appeals_reason_desc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `report_appeals_response_desc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `report_appeals_decision` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_appeals_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_appeals_second_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_1` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_2` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_3` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.content_reporting_appeals: ~0 rows (approximately)
/*!40000 ALTER TABLE `content_reporting_appeals` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_reporting_appeals` ENABLE KEYS */;

-- Dumping structure for table thrd.content_reporting_transaction_history
DROP TABLE IF EXISTS `content_reporting_transaction_history`;
CREATE TABLE IF NOT EXISTS `content_reporting_transaction_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_reporting_id` bigint NOT NULL DEFAULT '0',
  `reason_id` bigint DEFAULT NULL,
  `reason_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `report_trans_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_trans_reason_desc` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_trans_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_trans_data` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.content_reporting_transaction_history: ~0 rows (approximately)
/*!40000 ALTER TABLE `content_reporting_transaction_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_reporting_transaction_history` ENABLE KEYS */;

-- Dumping structure for table thrd.conversations
DROP TABLE IF EXISTS `conversations`;
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `owner_user_id` bigint unsigned DEFAULT NULL,
  `to_id` bigint unsigned DEFAULT NULL,
  `circle_id` bigint unsigned DEFAULT NULL,
  `conv_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `deleted_by_user_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_by_from_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_by_group_ids` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `status_second` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'couple',
  `type_second` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'couple',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='- owner_user_id is the person who initiatiated the conversation\r\n--> if the user conversation is 1 to 1, `to_id` is used\r\n-->if the user conversation is a group (part of a circle), `to_id` is NOT used\r\n\r\n- type: couple, circle\r\n- status: active, in-active\r\n\r\n';

-- Dumping data for table thrd.conversations: ~0 rows (approximately)
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;

-- Dumping structure for table thrd.conversation_chats
DROP TABLE IF EXISTS `conversation_chats`;
CREATE TABLE IF NOT EXISTS `conversation_chats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `init_user_id` bigint unsigned DEFAULT NULL,
  `end_user_id` bigint unsigned DEFAULT NULL,
  `conversation_id` bigint unsigned DEFAULT NULL,
  `chat_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op1` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op2` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seen_by_other_user` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `seen_by_received_user` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='- when the conversation is 1 to 1, end_user_id is used\r\n- when the conversation is a circle, then messages are read by first come first serve\r\n- conversation will have a type:\r\n--> type: chat, announcement, system\r\n---> chat is when a user makes a chat\r\n---> announcement,  is when a user joines, leaves, or a change is made to the chat. When a user joins, a record will be inserted with announcement\r\n---> system is when the system enters a record, either for tutorial or admin purposes';

-- Dumping data for table thrd.conversation_chats: ~0 rows (approximately)
/*!40000 ALTER TABLE `conversation_chats` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversation_chats` ENABLE KEYS */;

-- Dumping structure for table thrd.core_site_config
DROP TABLE IF EXISTS `core_site_config`;
CREATE TABLE IF NOT EXISTS `core_site_config` (
  `cfg_id` int NOT NULL AUTO_INCREMENT,
  `cfg_option_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfg_status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cfg_value_1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cfg_value_2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cfg_value_3` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cfg_value_4` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cfg_value_5` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cfg_value_6` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cfg_value_7` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`cfg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.core_site_config: ~0 rows (approximately)
/*!40000 ALTER TABLE `core_site_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `core_site_config` ENABLE KEYS */;

-- Dumping structure for table thrd.credit_transactions
DROP TABLE IF EXISTS `credit_transactions`;
CREATE TABLE IF NOT EXISTS `credit_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `stripe_payment_intent_id` varchar(5000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credits_amount` int DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `currency` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'usd',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_credit_transactions_users` (`user_id`),
  CONSTRAINT `FK_credit_transactions_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.credit_transactions: ~0 rows (approximately)
/*!40000 ALTER TABLE `credit_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_transactions` ENABLE KEYS */;

-- Dumping structure for table thrd.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.failed_jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

-- Dumping structure for table thrd.files_articles
DROP TABLE IF EXISTS `files_articles`;
CREATE TABLE IF NOT EXISTS `files_articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` bigint unsigned NOT NULL,
  `table_reference_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_store_an_id` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foldername` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verify_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_order` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `files_articles_reference_id_index` (`reference_id`),
  KEY `files_articles_table_reference_name_index` (`table_reference_name`),
  KEY `files_articles_status_index` (`status`),
  KEY `files_articles_type_index` (`type`),
  KEY `files_articles_file_order_index` (`file_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.files_articles: ~0 rows (approximately)
/*!40000 ALTER TABLE `files_articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `files_articles` ENABLE KEYS */;

-- Dumping structure for table thrd.files_circles
DROP TABLE IF EXISTS `files_circles`;
CREATE TABLE IF NOT EXISTS `files_circles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reference_id` int NOT NULL,
  `table_reference_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_store_an_id` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `foldername` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `verify_status` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_order` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Dumping data for table thrd.files_circles: ~0 rows (approximately)
/*!40000 ALTER TABLE `files_circles` DISABLE KEYS */;
/*!40000 ALTER TABLE `files_circles` ENABLE KEYS */;

-- Dumping structure for table thrd.files_post_stored
DROP TABLE IF EXISTS `files_post_stored`;
CREATE TABLE IF NOT EXISTS `files_post_stored` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `post_id` bigint DEFAULT NULL,
  `file_store_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foldername` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `verify_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'inactive',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'image',
  `order` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.files_post_stored: ~0 rows (approximately)
/*!40000 ALTER TABLE `files_post_stored` DISABLE KEYS */;
/*!40000 ALTER TABLE `files_post_stored` ENABLE KEYS */;

-- Dumping structure for table thrd.files_product
DROP TABLE IF EXISTS `files_product`;
CREATE TABLE IF NOT EXISTS `files_product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` int unsigned NOT NULL,
  `table_reference_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_store_an_id` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foldername` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verify_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_order` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.files_product: ~0 rows (approximately)
/*!40000 ALTER TABLE `files_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `files_product` ENABLE KEYS */;

-- Dumping structure for table thrd.files_temporary
DROP TABLE IF EXISTS `files_temporary`;
CREATE TABLE IF NOT EXISTS `files_temporary` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `file_temp_an_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foldername` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.files_temporary: ~0 rows (approximately)
/*!40000 ALTER TABLE `files_temporary` DISABLE KEYS */;
/*!40000 ALTER TABLE `files_temporary` ENABLE KEYS */;

-- Dumping structure for table thrd.files_user_stored
DROP TABLE IF EXISTS `files_user_stored`;
CREATE TABLE IF NOT EXISTS `files_user_stored` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `file_store_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foldername` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `verify_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'inactive',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'image',
  `order` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.files_user_stored: ~0 rows (approximately)
/*!40000 ALTER TABLE `files_user_stored` DISABLE KEYS */;
/*!40000 ALTER TABLE `files_user_stored` ENABLE KEYS */;

-- Dumping structure for table thrd.image_test
DROP TABLE IF EXISTS `image_test`;
CREATE TABLE IF NOT EXISTS `image_test` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `original_image_public_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `original_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `generated_image_public_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `generated_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `operation_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `operation_metadata` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.image_test: ~16 rows (approximately)
/*!40000 ALTER TABLE `image_test` DISABLE KEYS */;
INSERT INTO `image_test` (`id`, `user_id`, `original_image_public_id`, `original_image`, `generated_image_public_id`, `generated_image`, `operation_type`, `operation_metadata`, `created_at`, `updated_at`) VALUES
	(2, 3, 'uploads/2jOK0vqnqAKacg5Re43nBS4lKSBzmsHWKKlMP2kP.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772250070/uploads/2jOK0vqnqAKacg5Re43nBS4lKSBzmsHWKKlMP2kP.jpg', 'uploads/transformed/gen_fill/yzdfsz2x0cigqfdwhkqg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772250085/transformed/gen_fill/yzdfsz2x0cigqfdwhkqg.jpg', 'generative-fill', '{"aspect_ratio":"4:3"}', '2026-02-28 03:41:25', '2026-02-28 03:41:25'),
	(3, 3, 'uploads/qtermaDPpk77PjPMeyOPm2ZPMmOAWHiNdN3GpRfE.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772256204/uploads/qtermaDPpk77PjPMeyOPm2ZPMmOAWHiNdN3GpRfE.jpg', 'uploads/transformed/gen_fill/prnou9ieohl7bhy7cjmi', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772256225/transformed/gen_fill/prnou9ieohl7bhy7cjmi.jpg', 'generative-fill', '{"aspect_ratio":"1:1"}', '2026-02-28 05:23:45', '2026-02-28 05:23:45'),
	(5, 3, 'uploads/FxVYsQspnSlhSInpFIfjjWTVEHQ7ruZaSRHAj2MY.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772310802/uploads/FxVYsQspnSlhSInpFIfjjWTVEHQ7ruZaSRHAj2MY.jpg', 'uploads/transformed/gen_fill/cuhrfs5sfu7odbyl6fa1', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772310810/transformed/gen_fill/cuhrfs5sfu7odbyl6fa1.jpg', 'generative-fill', '{"aspect_ratio":"4:3"}', '2026-02-28 20:33:32', '2026-02-28 20:33:32'),
	(6, 3, 'uploads/vUab7bxX5iqAKyINL3faQMx0PNDzYQ8flCVeobcs.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337144/uploads/vUab7bxX5iqAKyINL3faQMx0PNDzYQ8flCVeobcs.jpg', 'uploads/transformed/gen_fill/r0hirtkazkmm7wurx8wm', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337154/transformed/gen_fill/r0hirtkazkmm7wurx8wm.jpg', 'generative-fill', '{"aspect_ratio":"4:3"}', '2026-03-01 03:52:36', '2026-03-01 03:52:36'),
	(7, 3, 'uploads/c22Dvu51mV2AHcIXSQdEVQbIE2JCM4xFCRkK3RnO.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337248/uploads/c22Dvu51mV2AHcIXSQdEVQbIE2JCM4xFCRkK3RnO.jpg', 'uploads/transformed/gen_fill/rbuq453zp2wd8lvvekmk', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337255/transformed/gen_fill/rbuq453zp2wd8lvvekmk.jpg', 'generative-fill', '{"aspect_ratio":"16:9"}', '2026-03-01 03:54:17', '2026-03-01 03:54:17'),
	(8, 3, 'uploads/meTlp77IpU6sTZCB0UGEUlzfO6OyaOWueqh4CgtK.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337337/uploads/meTlp77IpU6sTZCB0UGEUlzfO6OyaOWueqh4CgtK.jpg', 'uploads/transformed/gen_fill/we89i3nii6k3gdnlb1ad', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337344/transformed/gen_fill/we89i3nii6k3gdnlb1ad.jpg', 'generative-fill', '{"aspect_ratio":"16:9"}', '2026-03-01 03:55:46', '2026-03-01 03:55:46'),
	(9, 3, 'uploads/BiSwWsjNXC4jawcveOeJeABXTCEKxkV6xCxKOksw.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337433/uploads/BiSwWsjNXC4jawcveOeJeABXTCEKxkV6xCxKOksw.jpg', 'uploads/transformed/gen_fill/vw8khginccsdbiwsgfas', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337441/transformed/gen_fill/vw8khginccsdbiwsgfas.jpg', 'generative-fill', '{"aspect_ratio":"16:9"}', '2026-03-01 03:57:22', '2026-03-01 03:57:22'),
	(10, 3, 'uploads/gPqPF5DicgyB3XOGEnFaZalXqWjvdifmOTAsJcWW.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337472/uploads/gPqPF5DicgyB3XOGEnFaZalXqWjvdifmOTAsJcWW.jpg', 'uploads/transformed/gen_fill/obyczt7cccmtpb08c5l2', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337477/transformed/gen_fill/obyczt7cccmtpb08c5l2.jpg', 'generative-fill', '{"aspect_ratio":"16:9"}', '2026-03-01 03:57:59', '2026-03-01 03:57:59'),
	(11, 3, 'uploads/dJexqw7vgka8G3joOku6WEcnMNmJhu7XzaQpubYP.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772509901/uploads/dJexqw7vgka8G3joOku6WEcnMNmJhu7XzaQpubYP.jpg', 'uploads/transformed/restore/lp3oxwp4xgijf84t4f8j', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772509907/transformed/restore/lp3oxwp4xgijf84t4f8j.jpg', 'restore', '[]', '2026-03-03 03:51:50', '2026-03-03 03:51:50'),
	(12, 3, 'uploads/xWevW1TxRfrbcHAXuffpxJBnQC9VIqIHzh1QRLwn.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772511992/uploads/xWevW1TxRfrbcHAXuffpxJBnQC9VIqIHzh1QRLwn.jpg', 'uploads/transformed/recolour/m2wf97ibjfvglfmtwbw0', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772511995/transformed/recolour/m2wf97ibjfvglfmtwbw0.jpg', 'recolour', '{"colour":"C4B454","target_part":"Dark mode"}', '2026-03-03 04:26:37', '2026-03-03 04:26:37'),
	(13, 3, 'uploads/t1Tlz6nCnqutMAprgD8HG3wpckqIdNVy0b9FV4Mx.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772512035/uploads/t1Tlz6nCnqutMAprgD8HG3wpckqIdNVy0b9FV4Mx.jpg', 'uploads/transformed/recolour/klva1bjjmdxzkt9cbhle', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772512040/transformed/recolour/klva1bjjmdxzkt9cbhle.jpg', 'recolour', '{"colour":"C4B454","target_part":"Blue 3ds"}', '2026-03-03 04:27:23', '2026-03-03 04:27:23'),
	(14, 3, 'uploads/Lyke6IGe0R7nSm17Xy2Mvxb2kG1KlzMNcgdGCEZH.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772512573/uploads/Lyke6IGe0R7nSm17Xy2Mvxb2kG1KlzMNcgdGCEZH.jpg', 'uploads/transformed/remove_object/mdp5ur3z2qcphmtdblc7', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772512575/transformed/remove_object/mdp5ur3z2qcphmtdblc7.jpg', 'remove_objects', '{"prompt":"Starbucks"}', '2026-03-03 04:36:16', '2026-03-03 04:36:16'),
	(15, 3, 'uploads/BPy59FNUlj5HIY4bNclxMdh1qKSMx4vMKpBPWY5A.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772512980/uploads/BPy59FNUlj5HIY4bNclxMdh1qKSMx4vMKpBPWY5A.jpg', 'uploads/transformed/remove_object/lqdy0ckfe8rg8zhjq9on', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772513008/transformed/remove_object/lqdy0ckfe8rg8zhjq9on.jpg', 'remove_objects', '{"prompt":"Card"}', '2026-03-03 04:43:31', '2026-03-03 04:43:31'),
	(16, 3, 'uploads/pZoHbySJImbzgncS97PPvvxV6MXhBrfchmrp6QqC.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772513130/uploads/pZoHbySJImbzgncS97PPvvxV6MXhBrfchmrp6QqC.jpg', 'uploads/transformed/remove_object/jkgobw0d2pswrw3zc73v', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772513150/transformed/remove_object/jkgobw0d2pswrw3zc73v.jpg', 'remove_objects', '{"prompt":"remove lock"}', '2026-03-03 04:45:55', '2026-03-03 04:45:55'),
	(17, 3, 'uploads/iqpXVGh6x7jNWOmjhMvCtL1ZoZH9Gqg6M3l3pLzF.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772513277/uploads/iqpXVGh6x7jNWOmjhMvCtL1ZoZH9Gqg6M3l3pLzF.jpg', 'uploads/transformed/remove_object/hhr68butbhbfmmqcnoia', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772513297/transformed/remove_object/hhr68butbhbfmmqcnoia.jpg', 'remove_objects', '{"prompt":"Remove battery booster"}', '2026-03-03 04:48:19', '2026-03-03 04:48:19'),
	(18, 3, 'uploads/oQgJTxfnfgLvQcNSDZmZoJ8XfVbx8NcHxph15Rlc.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772513361/uploads/oQgJTxfnfgLvQcNSDZmZoJ8XfVbx8NcHxph15Rlc.jpg', 'uploads/transformed/recolour/ch0pammlkaap1guszfmx', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772513370/transformed/recolour/ch0pammlkaap1guszfmx.jpg', 'recolour', '{"colour":"5A9E9E","target_part":"Card"}', '2026-03-03 04:49:32', '2026-03-03 04:49:32');
/*!40000 ALTER TABLE `image_test` ENABLE KEYS */;

-- Dumping structure for table thrd.incidents
DROP TABLE IF EXISTS `incidents`;
CREATE TABLE IF NOT EXISTS `incidents` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `incident_an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_id` int DEFAULT NULL,
  `incident_target_reason_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_summary` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_data1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `incident_data2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `incident_data3` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `incident_data4` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `incident_data5` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `incident_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.incidents: ~0 rows (approximately)
/*!40000 ALTER TABLE `incidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `incidents` ENABLE KEYS */;

-- Dumping structure for table thrd.incidents_catalog
DROP TABLE IF EXISTS `incidents_catalog`;
CREATE TABLE IF NOT EXISTS `incidents_catalog` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `incident_name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_rate` double DEFAULT NULL,
  `incident_priority` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_status` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_threshold` int DEFAULT NULL,
  `incident_op_1` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_op_2` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.incidents_catalog: ~0 rows (approximately)
/*!40000 ALTER TABLE `incidents_catalog` DISABLE KEYS */;
/*!40000 ALTER TABLE `incidents_catalog` ENABLE KEYS */;

-- Dumping structure for table thrd.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;

-- Dumping structure for table thrd.job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.job_batches: ~0 rows (approximately)
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;

-- Dumping structure for table thrd.job_posts
DROP TABLE IF EXISTS `job_posts`;
CREATE TABLE IF NOT EXISTS `job_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excerpt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug_trans` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('COMMITTED','UNPAID','UNPAID_TO_BE_PAID_DRAFT','UNPAID_TO_BE_PAID_DRAFT_REMOVED','DRAFT','ARCHIVED','REMOVED') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'DRAFT',
  `free_tier` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_info_id` bigint unsigned DEFAULT NULL,
  `job_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `employer_type` enum('full-time','part-time','contractor','temporary','internship','per diem','volunteer','onsite') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salary_min` decimal(10,2) DEFAULT NULL,
  `salary_max` decimal(10,2) DEFAULT NULL,
  `payment_frequency` enum('milestone','hourly','one-time') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_tag` enum('software development','customer service','sales','marketing','design','frontend','backend','legal','Quality assurance','testing','non-tech','other','JavaScript') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_tags` json DEFAULT NULL,
  `skills` json DEFAULT NULL,
  `location_type` enum('remote','on-site','hybrid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_restriction` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_state_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_zip_postal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_long` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locaiton_lat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_company_logo` tinyint(1) NOT NULL DEFAULT '0',
  `highlight_company_with_color` tinyint(1) NOT NULL DEFAULT '0',
  `highlight_company` tinyint(1) NOT NULL DEFAULT '0',
  `brand_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_post` tinyint(1) NOT NULL DEFAULT '1',
  `email_blast_job` tinyint(1) NOT NULL DEFAULT '0',
  `auto_match_applicant` tinyint(1) NOT NULL DEFAULT '0',
  `create_qr_code` tinyint(1) NOT NULL DEFAULT '0',
  `highlight_post` tinyint(1) NOT NULL DEFAULT '0',
  `sticky_note_24_hour` tinyint(1) NOT NULL DEFAULT '0',
  `sticky_note_week` tinyint(1) NOT NULL DEFAULT '0',
  `sticky_note_month` tinyint(1) NOT NULL DEFAULT '0',
  `geo_lock_post` tinyint(1) NOT NULL DEFAULT '0',
  `how_to_apply` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `apply_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apply_email_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_twitter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `invoice_notes_po_box_number` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `feedback_box` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pay_later` tinyint(1) NOT NULL DEFAULT '0',
  `benefits` json DEFAULT NULL,
  `views` bigint unsigned DEFAULT NULL,
  `clicks` bigint unsigned DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_posts_company_info_id_index` (`company_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.job_posts: ~0 rows (approximately)
/*!40000 ALTER TABLE `job_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_posts` ENABLE KEYS */;

-- Dumping structure for table thrd.knowledge_base
DROP TABLE IF EXISTS `knowledge_base`;
CREATE TABLE IF NOT EXISTS `knowledge_base` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_added_id` int DEFAULT NULL,
  `kb_parent_id` int DEFAULT NULL,
  `kb_an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kb_type1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kb_type2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kb_summary` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kb_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kb_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kb_views` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kb_data` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kb_order` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.knowledge_base: ~0 rows (approximately)
/*!40000 ALTER TABLE `knowledge_base` DISABLE KEYS */;
/*!40000 ALTER TABLE `knowledge_base` ENABLE KEYS */;

-- Dumping structure for table thrd.knowledge_base_pages
DROP TABLE IF EXISTS `knowledge_base_pages`;
CREATE TABLE IF NOT EXISTS `knowledge_base_pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_creator_user_id` int DEFAULT NULL,
  `description` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.knowledge_base_pages: ~0 rows (approximately)
/*!40000 ALTER TABLE `knowledge_base_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `knowledge_base_pages` ENABLE KEYS */;

-- Dumping structure for table thrd.likes
DROP TABLE IF EXISTS `likes`;
CREATE TABLE IF NOT EXISTS `likes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `post_id` bigint DEFAULT NULL,
  `comment_id` bigint DEFAULT NULL,
  `item_id` bigint DEFAULT NULL,
  `pronetwork_group_profile_id` bigint DEFAULT NULL,
  `lk_status` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `lk_type` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'like',
  `lk_value` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.likes: ~0 rows (approximately)
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;

-- Dumping structure for table thrd.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.migrations: ~103 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_02_21_061537_create_personal_access_tokens_table', 2),
	(5, '2024_03_01_000000_create_address_table', 3),
	(6, '2024_03_01_000000_create_answers_table', 4),
	(7, '2024_03_01_000000_create_carts_table', 5),
	(8, '2024_03_01_000000_create_cities_canada_table', 6),
	(9, '2024_03_01_000000_create_cities_us_table', 7),
	(10, '2024_03_01_000000_create_comments_table', 8),
	(11, '2024_03_01_000000_create_content_approvals_table', 9),
	(12, '2024_03_01_000000_create_content_reporting_appeals_table', 10),
	(13, '2024_03_01_000000_create_content_reporting_table', 11),
	(14, '2024_03_01_000000_create_content_reporting_transaction_history_table', 12),
	(15, '2024_03_01_000000_create_conversation_chats_table', 13),
	(16, '2024_03_01_000000_create_conversation_group_tracker_table', 14),
	(17, '2024_03_01_000000_create_conversation_table', 15),
	(18, '2024_03_01_000000_create_core_site_config_table', 16),
	(19, '2024_03_01_000000_create_files_credentials_stored_table', 17),
	(20, '2024_03_01_000000_create_files_mynetwork_table', 18),
	(21, '2024_03_01_000000_create_files_post_stored_table', 19),
	(22, '2024_03_01_000000_create_files_product_table', 20),
	(23, '2024_03_01_000000_create_files_stored_table', 21),
	(24, '2024_03_01_000000_create_files_temporary_table', 22),
	(25, '2024_03_01_000000_create_files_user_stored_table', 23),
	(26, '2024_03_01_000000_create_incidents_catalog_table', 24),
	(27, '2024_03_01_000000_create_incidents_table', 25),
	(28, '2024_03_01_000000_create_job_posts_table', 26),
	(29, '2024_03_01_000000_create_knowledge_base_pages_table', 27),
	(30, '2024_03_01_000000_create_knowledge_base_table', 28),
	(31, '2024_03_01_000000_create_likes_table', 29),
	(32, '2024_03_01_000000_create_newsletter_email_table', 30),
	(33, '2024_03_01_000000_create_notifications_table', 31),
	(34, '2024_03_01_000000_create_order_details_table', 32),
	(35, '2024_03_01_000000_create_orders_table', 33),
	(36, '2024_03_01_000000_create_password_resets_table', 34),
	(37, '2024_03_01_000000_create_posts_table', 35),
	(38, '2024_03_01_000000_create_pronetwork_connections_table', 36),
	(39, '2024_03_01_000000_create_pronetwork_group_profile_table', 37),
	(40, '2024_03_01_000000_create_pronetwork_group_table', 38),
	(41, '2024_03_01_000000_create_pronetwork_requests_table', 39),
	(42, '2024_03_01_000000_create_pronetwork_user_profile_analytics_table', 40),
	(43, '2024_03_01_000000_create_pronetwork_user_profile_education_table', 41),
	(44, '2024_03_01_000000_create_pronetwork_user_profile_experience_table', 42),
	(45, '2024_03_01_000000_create_pronetwork_user_profile_honours_table', 43),
	(46, '2024_03_01_000000_create_pronetwork_user_profile_interests_table', 44),
	(47, '2024_03_01_000000_create_pronetwork_user_profile_skills_table', 45),
	(48, '2024_03_01_000000_create_pronetwork_user_profile_table', 46),
	(49, '2024_03_01_000000_create_pronetwork_user_profile_volunteering_table', 47),
	(50, '2024_03_01_000000_create_ranking_groups_table', 48),
	(51, '2024_03_01_000000_create_ranking_interaction_catalog_table', 49),
	(52, '2024_03_01_000000_create_ranking_permissions_table', 50),
	(53, '2024_03_01_000000_create_ranking_transaction_history_table', 51),
	(54, '2024_03_01_000000_create_ranking_weight_table', 52),
	(55, '2024_03_01_000000_create_rankings_table', 53),
	(56, '2024_03_01_000000_create_search_table', 54),
	(57, '2024_03_01_000000_create_settings_site_table', 55),
	(58, '2024_03_01_000000_create_subscriptions_table', 56),
	(59, '2024_03_01_000000_create_tracker_comment_user_video_table', 57),
	(60, '2024_03_01_000000_create_tracker_playlist_video_table', 58),
	(61, '2024_03_01_000000_create_tracker_series_video_table', 59),
	(62, '2024_03_01_000000_create_tracker_thumb_vote_video_table', 60),
	(63, '2024_03_01_000000_create_tracker_user_content_history_table', 61),
	(64, '2024_03_01_000000_create_trxn_address_billing_table', 62),
	(65, '2024_03_01_000000_create_trxn_address_shipping_table', 63),
	(66, '2024_03_01_000000_create_trxn_country_table', 64),
	(67, '2024_03_01_000000_create_trxn_coupon_table', 65),
	(68, '2024_03_01_000000_create_trxn_currency_table', 66),
	(69, '2024_03_01_000000_create_trxn_invoice_item_table', 67),
	(70, '2024_03_01_000000_create_trxn_invoice_table', 68),
	(71, '2024_03_01_000000_create_trxn_membership_table', 69),
	(72, '2024_03_01_000000_create_trxn_order_history_table', 70),
	(73, '2024_03_01_000000_create_trxn_order_item_table', 71),
	(74, '2024_03_01_000000_create_trxn_order_table', 72),
	(75, '2024_03_01_000000_create_trxn_payment_processor_table', 73),
	(76, '2024_03_01_000000_create_trxn_payment_transaction_history_table', 74),
	(77, '2024_03_01_000000_create_trxn_product_table', 75),
	(78, '2024_03_01_000000_create_trxn_setting_payment_table', 76),
	(79, '2024_03_01_000000_create_trxn_setting_processor_braintree_table', 77),
	(80, '2024_03_01_000000_create_trxn_setting_processor_moneris_table', 78),
	(81, '2024_03_01_000000_create_trxn_setting_processor_paypal_table', 79),
	(82, '2024_03_01_000000_create_trxn_setting_processor_stripe_table', 80),
	(83, '2024_03_01_000000_create_trxn_shopper_table', 81),
	(84, '2024_03_01_000000_create_trxn_shopping_cart_table', 82),
	(85, '2024_03_01_000000_create_trxn_state_province_table', 83),
	(86, '2024_03_01_000000_create_trxn_tracker_product_stat_table', 84),
	(87, '2024_03_01_000000_create_trxn_vat_table', 85),
	(88, '2024_03_01_000000_create_user_roles_table', 86),
	(89, '2024_03_01_000000_create_verify_images_table', 87),
	(90, '2024_03_01_000000_create_videos_table', 88),
	(91, '2024_03_01_000000_create_wishlist_table', 89),
	(92, '2025_10_27_211804_create_subscribers_table', 90),
	(93, '2025_11_19_000000_create_web_activity_logs_table', 91),
	(94, '2025_11_19_000001_add_email_verification_token_to_users_table', 92),
	(95, '2025_11_20_100000_add_removed_status_to_job_posts', 93),
	(96, '2025_11_20_100001_create_admin_activity_logs_table', 94),
	(97, '2025_12_01_000000_add_tracking_fields_to_search_table', 95),
	(98, '2025_12_17_000001_create_articles_table', 96),
	(99, '2025_12_17_000002_create_files_articles_table', 97),
	(100, '2025_12_18_000001_create_company_info_table', 98),
	(101, '2025_12_18_000002_add_company_info_id_to_job_posts', 99),
	(102, '2026_01_22_000000_add_free_tier_to_job_posts', 100),
	(103, '2026_01_29_000000_create_promo_click_tracking_table', 101);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table thrd.newsletter_email
DROP TABLE IF EXISTS `newsletter_email`;
CREATE TABLE IF NOT EXISTS `newsletter_email` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.newsletter_email: ~0 rows (approximately)
/*!40000 ALTER TABLE `newsletter_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter_email` ENABLE KEYS */;

-- Dumping structure for table thrd.notifications
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `from_id` bigint DEFAULT NULL,
  `fk_circle_item_post_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_conversation_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_rankings_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_ranking_transaction_history_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_pronetwork_requests_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_comments_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_trxn_payment_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_circle_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_verify_images_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notif_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `note_table_name_target` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `note_table_related_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `note_relation_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `op_1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `op_2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `op_3` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `color_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'black',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.notifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;

-- Dumping structure for table thrd.orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `order_details_id` bigint DEFAULT NULL,
  `orderNumber` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orderDate` date NOT NULL,
  `requiredDate` date NOT NULL,
  `shippedDate` date DEFAULT NULL,
  `status` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `customerNumber` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.orders: ~0 rows (approximately)
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;

-- Dumping structure for table thrd.order_details
DROP TABLE IF EXISTS `order_details`;
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint DEFAULT NULL,
  `orderNumber` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `productCode` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantityOrdered` int NOT NULL,
  `priceEach` decimal(10,2) NOT NULL,
  `orderLineNumber` smallint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.order_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;

-- Dumping structure for table thrd.password_resets
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.password_resets: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Dumping structure for table thrd.password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.password_reset_tokens: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;

-- Dumping structure for table thrd.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.permissions: ~8 rows (approximately)
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`id`, `permission_name`, `description`, `type`, `created_at`, `updated_at`) VALUES
	(1, 'invite.user', 'Invites a user', 'action', '2026-03-11 00:55:51', '2026-03-11 00:55:53'),
	(2, 'post.flyer', 'Able to post flyer', 'action', '2026-03-11 00:59:05', '2026-03-11 00:59:08'),
	(3, 'receive.review', 'Able to recieve review', 'action', '2026-03-11 00:59:06', '2026-03-11 00:59:09'),
	(4, 'create.hub', 'Able to create hubs', 'action', '2026-03-11 00:59:06', '2026-03-11 00:59:10'),
	(5, 'view.analytics', 'Able to view analytics', 'action', '2026-03-11 00:59:07', '2026-03-11 00:59:09'),
	(6, 'run.promotion', 'Able to run promotions', 'action', '2026-03-11 00:59:07', '2026-03-11 00:59:11'),
	(7, 'run.advertising', 'Able to run advertising', 'action', '2026-03-11 00:59:07', '2026-03-11 00:59:11'),
	(8, 'allow.follow', 'Individual users can follow hosts, collectives, and businesses', 'action', '2026-03-11 01:01:37', '2026-03-11 01:01:36');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;

-- Dumping structure for table thrd.personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.personal_access_tokens: ~5 rows (approximately)
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 2, 'auth_token', '7bd84e46bd76285a62ab00423714a681e1ad576590a2bfd8e97df5bd287e63a1', '["*"]', NULL, NULL, '2026-02-21 06:48:35', '2026-02-21 06:48:35'),
	(3, 'App\\Models\\User', 3, 'auth_token', '9988074f45412577bc8f31478cc580ddb628971258f5d3858c2f0b598b4476c0', '["*"]', '2026-03-11 04:30:14', NULL, '2026-02-25 05:32:45', '2026-03-11 04:30:14'),
	(8, 'App\\Models\\User', 85, 'auth_token', 'ae54d9406d1a39028012c5edb8b06b59ed58c2f3e1766187fa98b8163e40538a', '["*"]', NULL, NULL, '2026-03-06 06:02:39', '2026-03-06 06:02:39'),
	(14, 'App\\Models\\User', 86, 'auth_token', 'a802f3c72bc3322752226ee6b9024c542ee264805135aea4c8318f0d1a77a451', '["*"]', NULL, NULL, '2026-03-07 16:46:51', '2026-03-07 16:46:51'),
	(15, 'App\\Models\\User', 86, 'auth_token', 'a31a55cb11f72efceb833aaa55324a1bad875b88ca2a8ac620ca3361d9b9f23a', '["*"]', '2026-03-08 06:48:39', NULL, '2026-03-07 16:47:14', '2026-03-08 06:48:39'),
	(16, 'App\\Models\\User', 86, 'auth_token', '7ac3b5144453a1b251aba3cb3af76e5679ee5f25c96477125bc21ba95256f5e8', '["*"]', NULL, NULL, '2026-03-17 03:27:33', '2026-03-17 03:27:33');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;

-- Dumping structure for table thrd.posts
DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `comments_id` bigint DEFAULT NULL,
  `likes_id` bigint DEFAULT NULL,
  `post_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_store_an_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `type` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `isVisible` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `shareLink` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `views` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.posts: ~0 rows (approximately)
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;

-- Dumping structure for table thrd.promo_click_tracking
DROP TABLE IF EXISTS `promo_click_tracking`;
CREATE TABLE IF NOT EXISTS `promo_click_tracking` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `promo_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promo_click_tracking_created_at_index` (`created_at`),
  KEY `promo_click_tracking_promo_type_index` (`promo_type`),
  KEY `promo_click_tracking_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.promo_click_tracking: ~0 rows (approximately)
/*!40000 ALTER TABLE `promo_click_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `promo_click_tracking` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork
DROP TABLE IF EXISTS `pronetwork`;
CREATE TABLE IF NOT EXISTS `pronetwork` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `profile_views_count` int NOT NULL DEFAULT '0',
  `interactive_count` int NOT NULL DEFAULT '0',
  `connections_count` int NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_connections
DROP TABLE IF EXISTS `pronetwork_connections`;
CREATE TABLE IF NOT EXISTS `pronetwork_connections` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `net_group_id` bigint DEFAULT NULL,
  `net_request_id` bigint DEFAULT NULL,
  `an_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `initiator_user_id` bigint DEFAULT NULL,
  `accepter_user_id` bigint DEFAULT NULL,
  `isConnected` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_connections: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_connections` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_connections` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_group
DROP TABLE IF EXISTS `pronetwork_group`;
CREATE TABLE IF NOT EXISTS `pronetwork_group` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `target_id` bigint DEFAULT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_group: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_group` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_group_profile
DROP TABLE IF EXISTS `pronetwork_group_profile`;
CREATE TABLE IF NOT EXISTS `pronetwork_group_profile` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `group_owner_user_id` bigint unsigned NOT NULL,
  `conversation_id` bigint unsigned NOT NULL,
  `header_image_id` bigint unsigned DEFAULT NULL,
  `profile_image_id` bigint unsigned DEFAULT NULL,
  `general_headline` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detailed_about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `general_location_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_location_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_location_state_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_circle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link1` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link2` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link3` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link4` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link5` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link6` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `following_count` int NOT NULL DEFAULT '0',
  `contact_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_skills` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_group_profile: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_group_profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_group_profile` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_requests
DROP TABLE IF EXISTS `pronetwork_requests`;
CREATE TABLE IF NOT EXISTS `pronetwork_requests` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `initiator_user_id` bigint DEFAULT NULL,
  `accepter_user_id` bigint DEFAULT NULL,
  `isAccepted` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'false',
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_requests: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_requests` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_user_profile
DROP TABLE IF EXISTS `pronetwork_user_profile`;
CREATE TABLE IF NOT EXISTS `pronetwork_user_profile` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `header_image_id` bigint unsigned DEFAULT NULL,
  `profile_image_id` bigint unsigned DEFAULT NULL,
  `general_headline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detailed_about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `general_location_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_location_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_location_state_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_circle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link1` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link2` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link3` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link4` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link5` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link6` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `views_count` int NOT NULL DEFAULT '0',
  `connections_count` int NOT NULL DEFAULT '0',
  `contact_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_skills` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_user_profile: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_user_profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_user_profile` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_user_profile_education
DROP TABLE IF EXISTS `pronetwork_user_profile_education`;
CREATE TABLE IF NOT EXISTS `pronetwork_user_profile_education` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `school` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `degree` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_of_study` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `location_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_state_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_state_province_abbrv` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_user_profile_education: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_user_profile_education` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_user_profile_education` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_user_profile_experience
DROP TABLE IF EXISTS `pronetwork_user_profile_experience`;
CREATE TABLE IF NOT EXISTS `pronetwork_user_profile_experience` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_state_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_user_profile_experience: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_user_profile_experience` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_user_profile_experience` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_user_profile_honours
DROP TABLE IF EXISTS `pronetwork_user_profile_honours`;
CREATE TABLE IF NOT EXISTS `pronetwork_user_profile_honours` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `education_association_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `issuer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issuer_start_date` date DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_user_profile_honours: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_user_profile_honours` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_user_profile_honours` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_user_profile_interests
DROP TABLE IF EXISTS `pronetwork_user_profile_interests`;
CREATE TABLE IF NOT EXISTS `pronetwork_user_profile_interests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `group_id` bigint unsigned NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_user_profile_interests: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_user_profile_interests` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_user_profile_interests` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_user_profile_skills
DROP TABLE IF EXISTS `pronetwork_user_profile_skills`;
CREATE TABLE IF NOT EXISTS `pronetwork_user_profile_skills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `skill` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `votes` int NOT NULL DEFAULT '0',
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_user_profile_skills: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_user_profile_skills` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_user_profile_skills` ENABLE KEYS */;

-- Dumping structure for table thrd.pronetwork_user_profile_volunteering
DROP TABLE IF EXISTS `pronetwork_user_profile_volunteering`;
CREATE TABLE IF NOT EXISTS `pronetwork_user_profile_volunteering` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `volunteer_company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_state_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.pronetwork_user_profile_volunteering: ~0 rows (approximately)
/*!40000 ALTER TABLE `pronetwork_user_profile_volunteering` DISABLE KEYS */;
/*!40000 ALTER TABLE `pronetwork_user_profile_volunteering` ENABLE KEYS */;

-- Dumping structure for table thrd.rankings
DROP TABLE IF EXISTS `rankings`;
CREATE TABLE IF NOT EXISTS `rankings` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `rank_group_id` bigint DEFAULT NULL,
  `rank_weight_id` bigint DEFAULT NULL,
  `rank_status` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_score` double DEFAULT NULL,
  `rank_weighed_score` double DEFAULT NULL,
  `rank_data` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rankings_user_id_index` (`user_id`),
  KEY `rankings_rank_group_id_index` (`rank_group_id`),
  KEY `rankings_rank_weight_id_index` (`rank_weight_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.rankings: ~0 rows (approximately)
/*!40000 ALTER TABLE `rankings` DISABLE KEYS */;
/*!40000 ALTER TABLE `rankings` ENABLE KEYS */;

-- Dumping structure for table thrd.ranking_groups
DROP TABLE IF EXISTS `ranking_groups`;
CREATE TABLE IF NOT EXISTS `ranking_groups` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `rank_group_type` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_group_tier` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_group_order` int DEFAULT NULL,
  `rank_group_weighted_score_threshold` double DEFAULT NULL,
  `rank_group_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_group_data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.ranking_groups: ~0 rows (approximately)
/*!40000 ALTER TABLE `ranking_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `ranking_groups` ENABLE KEYS */;

-- Dumping structure for table thrd.ranking_interaction_catalog
DROP TABLE IF EXISTS `ranking_interaction_catalog`;
CREATE TABLE IF NOT EXISTS `ranking_interaction_catalog` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `rank_interact_name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_interact_rate` double DEFAULT NULL,
  `rank_interact_type` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_interact_status` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_interact_passive_threshold` int DEFAULT NULL,
  `rank_interact_passive_reward` int DEFAULT NULL,
  `rank_interact_op_1` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_interact_op_2` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.ranking_interaction_catalog: ~0 rows (approximately)
/*!40000 ALTER TABLE `ranking_interaction_catalog` DISABLE KEYS */;
/*!40000 ALTER TABLE `ranking_interaction_catalog` ENABLE KEYS */;

-- Dumping structure for table thrd.ranking_permissions
DROP TABLE IF EXISTS `ranking_permissions`;
CREATE TABLE IF NOT EXISTS `ranking_permissions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `rank_perm_name` varchar(355) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_perm_threshold` int DEFAULT NULL,
  `rank_perm_value` int DEFAULT NULL,
  `rank_perm_order` int DEFAULT NULL,
  `rank_perm_type1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_perm_type2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_perm_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_perm_limit_duration` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_perm_op2` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_perm_op3` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.ranking_permissions: ~0 rows (approximately)
/*!40000 ALTER TABLE `ranking_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ranking_permissions` ENABLE KEYS */;

-- Dumping structure for table thrd.ranking_transaction_history
DROP TABLE IF EXISTS `ranking_transaction_history`;
CREATE TABLE IF NOT EXISTS `ranking_transaction_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `rank_trans_an_id` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_interact_id` bigint DEFAULT NULL,
  `rank_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `reason_id` bigint DEFAULT NULL,
  `reason_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_trans_reason_desc` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_trans_initiator` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_trans_name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_trans_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_trans_group` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_trans_start_rank` double DEFAULT NULL,
  `rank_trans_direction` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_trans_amount` double DEFAULT NULL,
  `rank_trans_end_rank` double DEFAULT NULL,
  `rank_trans_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_trans_threshold` int DEFAULT NULL,
  `rank_trans_threshold_count` int DEFAULT NULL,
  `rank_trans_trigger` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_trans_data` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ranking_transaction_history_rank_trans_an_id_index` (`rank_trans_an_id`),
  KEY `ranking_transaction_history_rank_interact_id_index` (`rank_interact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.ranking_transaction_history: ~0 rows (approximately)
/*!40000 ALTER TABLE `ranking_transaction_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `ranking_transaction_history` ENABLE KEYS */;

-- Dumping structure for table thrd.ranking_weight
DROP TABLE IF EXISTS `ranking_weight`;
CREATE TABLE IF NOT EXISTS `ranking_weight` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `rank_weight_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_weight` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.ranking_weight: ~0 rows (approximately)
/*!40000 ALTER TABLE `ranking_weight` DISABLE KEYS */;
/*!40000 ALTER TABLE `ranking_weight` ENABLE KEYS */;

-- Dumping structure for table thrd.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `roles_name_unique` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.roles: ~4 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `display_name`, `created_at`, `updated_at`) VALUES
	(3, 'admin', 'Administrator', '2026-03-05 00:00:26', '2026-03-05 00:00:28'),
	(4, 'personal', 'Personal', '2026-03-05 00:00:50', '2026-03-05 00:00:51'),
	(5, 'host', 'Community Host', '2026-03-05 00:01:50', '2026-03-05 00:01:52'),
	(6, 'business', 'Business Venue', '2026-03-05 00:01:51', '2026-03-05 00:01:52');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table thrd.role_permissions
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int unsigned DEFAULT NULL,
  `permission_id` int unsigned DEFAULT NULL,
  `notes` varchar(5000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_role_permissions_permissions` (`permission_id`),
  KEY `FK_role_permissions_roles` (`role_id`),
  CONSTRAINT `FK_role_permissions_permissions` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_role_permissions_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.role_permissions: ~0 rows (approximately)
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;

-- Dumping structure for table thrd.search
DROP TABLE IF EXISTS `search`;
CREATE TABLE IF NOT EXISTS `search` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `search_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ttl` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result_num` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filters` json DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `referrer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.search: ~0 rows (approximately)
/*!40000 ALTER TABLE `search` DISABLE KEYS */;
/*!40000 ALTER TABLE `search` ENABLE KEYS */;

-- Dumping structure for table thrd.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.sessions: ~5 rows (approximately)
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('E4G8siigvF4lKHunvjA4iVqGE9Cfau2x6J0jbwEw', NULL, '10.0.0.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUDQ4RjVVNEdJWmdtRks4YmNjY2tpdGJwY2tzenJ0NzRlS3lCQzVMTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTcxOiJodHRwOi8vMTAuMC4wLjEyOjgwMDAvdmVyaWZ5LWVtYWlsLzUvNDA2ZTYyZjk5MmFiYjNjMDA2MGUzZTdkM2ZjYzVjODFmOGFlNTE4MD9leHBpcmVzPTE3NzIwMDQyNjAmc2lnbmF0dXJlPTMxYTM3NzNiNjU2MmU3MWE5ZTJhNTk3NTlmNjQ3ZDA3OGNiNzdiMmJkZWNkYmQwYzdmZmUwOGE1OWUzZWM5NDkiO3M6NToicm91dGUiO3M6MTk6InZlcmlmaWNhdGlvbi52ZXJpZnkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1772001001),
	('erWhNJ7bHCQlxBvgh5SM7L8PNSog83OVFPwW7Vte', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY3ljV2hPWlJLMUh3czh6c1M0b2VUM0VBSkVvc1VMZVJibUVJbXE3ViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771829161),
	('L6HMETOr82KhfNaEekp5FYdYnLyPwxZyx6MIk48b', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTlpJRjZNNjM1WG1wNTRHWFJyRG45YkNKNTczYjJGN2hITDRSTVZUTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772042980),
	('OTB8Il4opOYlImUR4xmFhKeEKTqrJSASOyjU2fTd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2Z3NTVEUG9YTUs1SGF2V0VyaFJOZjN0WEhlaXVhejduVnluUWd4RSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771856103),
	('t2g0VMa9q57gfdCVMKUQS1QyZdusclpxBSzcNWyk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiajl2TDhIa25wQmpjakdWREVvZGxwUGZFU1VSUmhNa245TmlnbUQxTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771654762);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;

-- Dumping structure for table thrd.settings_site
DROP TABLE IF EXISTS `settings_site`;
CREATE TABLE IF NOT EXISTS `settings_site` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `op4` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `op5` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `op6` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.settings_site: ~0 rows (approximately)
/*!40000 ALTER TABLE `settings_site` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings_site` ENABLE KEYS */;

-- Dumping structure for table thrd.subscribers
DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.subscribers: ~0 rows (approximately)
/*!40000 ALTER TABLE `subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscribers` ENABLE KEYS */;

-- Dumping structure for table thrd.subscriptions
DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `subbee` bigint DEFAULT NULL,
  `subber` bigint DEFAULT NULL,
  `isSubberSubbed` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op1` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op2` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.subscriptions: ~0 rows (approximately)
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;

-- Dumping structure for table thrd.tracker_comment_user_video
DROP TABLE IF EXISTS `tracker_comment_user_video`;
CREATE TABLE IF NOT EXISTS `tracker_comment_user_video` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `video_id` bigint DEFAULT NULL,
  `circle_item_id` bigint DEFAULT NULL,
  `tcuvt_comment_an_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tcuvt_video_an_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tcuvt_user_an_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tcuvt_up_vote` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `tcuvt_down_vote` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `tcuvt_last_vote_date_change` datetime DEFAULT NULL,
  `tcuvt_record_insert_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.tracker_comment_user_video: ~0 rows (approximately)
/*!40000 ALTER TABLE `tracker_comment_user_video` DISABLE KEYS */;
/*!40000 ALTER TABLE `tracker_comment_user_video` ENABLE KEYS */;

-- Dumping structure for table thrd.tracker_playlist_video
DROP TABLE IF EXISTS `tracker_playlist_video`;
CREATE TABLE IF NOT EXISTS `tracker_playlist_video` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `plylst_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plylst_an_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plylst_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plylst_video_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plylst_date_last_mod` datetime NOT NULL,
  `plylst_date_created` datetime NOT NULL,
  `plylst_status` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `plylst_access` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Public',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.tracker_playlist_video: ~0 rows (approximately)
/*!40000 ALTER TABLE `tracker_playlist_video` DISABLE KEYS */;
/*!40000 ALTER TABLE `tracker_playlist_video` ENABLE KEYS */;

-- Dumping structure for table thrd.tracker_series_video
DROP TABLE IF EXISTS `tracker_series_video`;
CREATE TABLE IF NOT EXISTS `tracker_series_video` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `series_user_an_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `series_an_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `series_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `series_video_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `series_date_last_mod` datetime NOT NULL,
  `series_date_created` datetime NOT NULL,
  `series_status` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `series_access` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Public',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.tracker_series_video: ~0 rows (approximately)
/*!40000 ALTER TABLE `tracker_series_video` DISABLE KEYS */;
/*!40000 ALTER TABLE `tracker_series_video` ENABLE KEYS */;

-- Dumping structure for table thrd.tracker_thumb_vote_video
DROP TABLE IF EXISTS `tracker_thumb_vote_video`;
CREATE TABLE IF NOT EXISTS `tracker_thumb_vote_video` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `video_id` bigint DEFAULT NULL,
  `tlut_up_vote` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `tlut_down_vote` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `tlut_last_vote_date_change` datetime DEFAULT NULL,
  `tlut_record_insert_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.tracker_thumb_vote_video: ~0 rows (approximately)
/*!40000 ALTER TABLE `tracker_thumb_vote_video` DISABLE KEYS */;
/*!40000 ALTER TABLE `tracker_thumb_vote_video` ENABLE KEYS */;

-- Dumping structure for table thrd.tracker_user_content_history
DROP TABLE IF EXISTS `tracker_user_content_history`;
CREATE TABLE IF NOT EXISTS `tracker_user_content_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT '0',
  `his_json_history_record` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `his_date_created` datetime NOT NULL,
  `his_date_last_mod` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.tracker_user_content_history: ~0 rows (approximately)
/*!40000 ALTER TABLE `tracker_user_content_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `tracker_user_content_history` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_address_billing
DROP TABLE IF EXISTS `trxn_address_billing`;
CREATE TABLE IF NOT EXISTS `trxn_address_billing` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `addr_street` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_zip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_postal_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_state` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_phone_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_area_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_street_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_apart_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_po_box` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_floor_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_suite` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_address_billing: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_address_billing` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_address_billing` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_address_shipping
DROP TABLE IF EXISTS `trxn_address_shipping`;
CREATE TABLE IF NOT EXISTS `trxn_address_shipping` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `addr_street` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_zip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_postal_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_state` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_phone_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_area_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_street_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_apart_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_po_box` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_floor_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_suite` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr_department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_address_shipping: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_address_shipping` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_address_shipping` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_country
DROP TABLE IF EXISTS `trxn_country`;
CREATE TABLE IF NOT EXISTS `trxn_country` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_country: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_country` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_country` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_coupon
DROP TABLE IF EXISTS `trxn_coupon`;
CREATE TABLE IF NOT EXISTS `trxn_coupon` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` tinyint NOT NULL,
  `price` double NOT NULL,
  `times` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `used` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `coupon_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` int DEFAULT NULL,
  `sub_category` int DEFAULT NULL,
  `child_category` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_coupon: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_coupon` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_currency
DROP TABLE IF EXISTS `trxn_currency`;
CREATE TABLE IF NOT EXISTS `trxn_currency` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sign` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` double NOT NULL,
  `is_default` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_currency: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_currency` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_currency` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_invoice
DROP TABLE IF EXISTS `trxn_invoice`;
CREATE TABLE IF NOT EXISTS `trxn_invoice` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_invoice: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_invoice` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_invoice_item
DROP TABLE IF EXISTS `trxn_invoice_item`;
CREATE TABLE IF NOT EXISTS `trxn_invoice_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_invoice_item: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_invoice_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_invoice_item` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_membership
DROP TABLE IF EXISTS `trxn_membership`;
CREATE TABLE IF NOT EXISTS `trxn_membership` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_membership: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_membership` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_membership` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_order
DROP TABLE IF EXISTS `trxn_order`;
CREATE TABLE IF NOT EXISTS `trxn_order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `cart` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totalQty` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pay_amount` double NOT NULL,
  `txnid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `coupon_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon_discount` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `affilate_user` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affilate_charge` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_sign` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_value` double NOT NULL,
  `shipping_cost` double NOT NULL,
  `packing_cost` double NOT NULL DEFAULT '0',
  `tax` double NOT NULL,
  `tax_location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dp` tinyint NOT NULL DEFAULT '0',
  `pay_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `wallet_price` double NOT NULL DEFAULT '0',
  `shipping_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `packing_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `customer_state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount` int NOT NULL DEFAULT '0',
  `affilate_users` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `commission` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_order: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_order` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_order_history
DROP TABLE IF EXISTS `trxn_order_history`;
CREATE TABLE IF NOT EXISTS `trxn_order_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `cart` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totalQty` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pay_amount` double NOT NULL,
  `txnid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `coupon_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon_discount` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `affilate_user` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affilate_charge` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_sign` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_value` double NOT NULL,
  `shipping_cost` double NOT NULL,
  `packing_cost` double NOT NULL DEFAULT '0',
  `tax` double NOT NULL,
  `tax_location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dp` tinyint NOT NULL DEFAULT '0',
  `pay_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `wallet_price` double NOT NULL DEFAULT '0',
  `shipping_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `packing_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `customer_state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount` int NOT NULL DEFAULT '0',
  `affilate_users` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `commission` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_order_history: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_order_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_order_history` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_order_item
DROP TABLE IF EXISTS `trxn_order_item`;
CREATE TABLE IF NOT EXISTS `trxn_order_item` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT '0',
  `order_list_order` int DEFAULT '0',
  `product_id` int DEFAULT '0',
  `unit_price` double DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `column_6` int unsigned DEFAULT NULL,
  `cart_id` int unsigned DEFAULT '0',
  `transaction_id` int unsigned DEFAULT NULL,
  `trxn_ship_id` int unsigned DEFAULT NULL,
  `trxn_bill_id` int unsigned DEFAULT NULL,
  `shopper_id` int unsigned DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totalQty` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_amount` double DEFAULT NULL,
  `charge_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `coupon_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon_discount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','processing','completed','declined','on delivery') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `affilate_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affilate_charge` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_sign` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_value` double DEFAULT NULL,
  `shipping_cost` double DEFAULT NULL,
  `packing_cost` double NOT NULL DEFAULT '0',
  `tax` double DEFAULT NULL,
  `tax_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dp` tinyint(1) NOT NULL DEFAULT '0',
  `pay_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `wallet_price` double NOT NULL DEFAULT '0',
  `shipping_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `packing_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `customer_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount` int DEFAULT '0',
  `affilate_users` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `commission` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_order_item: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_order_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_order_item` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_payment_processor
DROP TABLE IF EXISTS `trxn_payment_processor`;
CREATE TABLE IF NOT EXISTS `trxn_payment_processor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subtitle` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('manual','automatic') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'manual',
  `information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `keyword` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `checkout` int NOT NULL DEFAULT '1',
  `deposit` int NOT NULL DEFAULT '1',
  `subscription` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_payment_processor: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_payment_processor` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_payment_processor` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_payment_transaction_history
DROP TABLE IF EXISTS `trxn_payment_transaction_history`;
CREATE TABLE IF NOT EXISTS `trxn_payment_transaction_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_payment_transaction_history: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_payment_transaction_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_payment_transaction_history` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_product
DROP TABLE IF EXISTS `trxn_product`;
CREATE TABLE IF NOT EXISTS `trxn_product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `affiliate_link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` int NOT NULL DEFAULT '0',
  `category_id` int NOT NULL,
  `subcategory_id` int DEFAULT NULL,
  `childcategory_id` int DEFAULT NULL,
  `attributes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `photo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_qty` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_price` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` double NOT NULL,
  `previous_price` double DEFAULT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `stock` int DEFAULT NULL,
  `color_all` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `size_all` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `stock_check` int DEFAULT '1',
  `policy` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `views` int NOT NULL DEFAULT '0',
  `tags` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `features` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `colors` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `product_condition` tinyint NOT NULL DEFAULT '0',
  `ship` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_meta` tinyint NOT NULL DEFAULT '0',
  `meta_tag` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `youtube` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('Physical','Digital','License') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `license` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `license_qty` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `platform` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `licence_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `measure` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured` tinyint NOT NULL DEFAULT '0',
  `best` tinyint NOT NULL DEFAULT '0',
  `top` tinyint NOT NULL DEFAULT '0',
  `hot` tinyint NOT NULL DEFAULT '0',
  `latest` tinyint NOT NULL DEFAULT '0',
  `big` tinyint NOT NULL DEFAULT '0',
  `trending` tinyint NOT NULL DEFAULT '0',
  `sale` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_discount` tinyint NOT NULL DEFAULT '0',
  `discount_date` date DEFAULT NULL,
  `whole_sell_qty` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `whole_sell_discount` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_catalog` tinyint NOT NULL DEFAULT '0',
  `catalog_id` int NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `language_id` int DEFAULT NULL,
  `preordered` tinyint NOT NULL DEFAULT '0',
  `minimum_qty` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_product: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_product` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_setting_payment
DROP TABLE IF EXISTS `trxn_setting_payment`;
CREATE TABLE IF NOT EXISTS `trxn_setting_payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_setting_payment: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_setting_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_setting_payment` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_setting_processor_braintree
DROP TABLE IF EXISTS `trxn_setting_processor_braintree`;
CREATE TABLE IF NOT EXISTS `trxn_setting_processor_braintree` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_setting_processor_braintree: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_setting_processor_braintree` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_setting_processor_braintree` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_setting_processor_moneris
DROP TABLE IF EXISTS `trxn_setting_processor_moneris`;
CREATE TABLE IF NOT EXISTS `trxn_setting_processor_moneris` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_setting_processor_moneris: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_setting_processor_moneris` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_setting_processor_moneris` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_setting_processor_paypal
DROP TABLE IF EXISTS `trxn_setting_processor_paypal`;
CREATE TABLE IF NOT EXISTS `trxn_setting_processor_paypal` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_setting_processor_paypal: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_setting_processor_paypal` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_setting_processor_paypal` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_setting_processor_stripe
DROP TABLE IF EXISTS `trxn_setting_processor_stripe`;
CREATE TABLE IF NOT EXISTS `trxn_setting_processor_stripe` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_setting_processor_stripe: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_setting_processor_stripe` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_setting_processor_stripe` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_shopper
DROP TABLE IF EXISTS `trxn_shopper`;
CREATE TABLE IF NOT EXISTS `trxn_shopper` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_shopper: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_shopper` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_shopper` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_shopping_cart
DROP TABLE IF EXISTS `trxn_shopping_cart`;
CREATE TABLE IF NOT EXISTS `trxn_shopping_cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `cart_id` bigint DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `cart_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expire_threshold` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '30',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_shopping_cart: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_shopping_cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_shopping_cart` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_state_province
DROP TABLE IF EXISTS `trxn_state_province`;
CREATE TABLE IF NOT EXISTS `trxn_state_province` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `country_id` int NOT NULL DEFAULT '0',
  `state` varchar(111) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax` double NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `owner_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_state_province: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_state_province` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_state_province` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_tracker_product_stat
DROP TABLE IF EXISTS `trxn_tracker_product_stat`;
CREATE TABLE IF NOT EXISTS `trxn_tracker_product_stat` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `product_views` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_tracker_product_stat: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_tracker_product_stat` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_tracker_product_stat` ENABLE KEYS */;

-- Dumping structure for table thrd.trxn_vat
DROP TABLE IF EXISTS `trxn_vat`;
CREATE TABLE IF NOT EXISTS `trxn_vat` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.trxn_vat: ~0 rows (approximately)
/*!40000 ALTER TABLE `trxn_vat` DISABLE KEYS */;
/*!40000 ALTER TABLE `trxn_vat` ENABLE KEYS */;

-- Dumping structure for table thrd.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL DEFAULT '20',
  `alpha_num_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'users/avatar.png',
  `email_verification_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `user_IsVerified` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `email_IsVerified` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `email_VerifiedToken` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `change_PasswordToken` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_try` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `credits` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settings` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `contact` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `links` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `friend_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `vid_fav` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `circle_fav` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phone_num` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isStoreOpen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'anonymous',
  `intrests` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `yourLocation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `who_i_sub_to` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `who_sub_to_me` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `who_i_sub_to_count` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `who_sub_to_me_count` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `registerIP` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastLoginIP` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suspend_reactive` datetime DEFAULT '1993-02-14 00:00:00',
  `email_verified_at` datetime DEFAULT NULL,
  `birthdate` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `user_lat` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_long` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_city` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_km_range` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `searchable` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'false',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_email_unique` (`email`) USING BTREE,
  UNIQUE KEY `Index 4` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.users: ~4 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `role_id`, `alpha_num_id`, `firstname`, `lastname`, `username`, `name`, `email`, `google_id`, `profile_photo_path`, `avatar`, `email_verification_token`, `user_IsVerified`, `email_IsVerified`, `email_VerifiedToken`, `change_PasswordToken`, `password`, `password_try`, `status`, `credits`, `remember_token`, `settings`, `user_settings`, `type`, `telephone`, `about`, `contact`, `links`, `history`, `friend_list`, `vid_fav`, `circle_fav`, `phone_num`, `isStoreOpen`, `identity`, `intrests`, `yourLocation`, `who_i_sub_to`, `who_sub_to_me`, `who_i_sub_to_count`, `who_sub_to_me_count`, `registerIP`, `lastLoginIP`, `suspend_reactive`, `email_verified_at`, `birthdate`, `last_login`, `user_lat`, `user_long`, `user_city`, `default_km_range`, `language`, `searchable`, `updated_at`, `created_at`) VALUES
	(1, 20, NULL, NULL, NULL, NULL, 'John Doe', 'john@example.com', NULL, NULL, 'users/avatar.png', 'no', 'no', 'no', NULL, NULL, '$2y$12$GYImQF/48li/1g8e2Z32Zu5M/.89/BxDEN8ZJFZ/8MBq6YfRS3sge', NULL, 'active', '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'anonymous', NULL, NULL, NULL, NULL, '0', '0', NULL, NULL, '1993-02-14 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'false', '2026-02-21 06:30:49', '2026-02-21 06:30:49'),
	(2, 20, NULL, NULL, NULL, NULL, 'John Doe', 'john1@example.com', NULL, NULL, 'users/avatar.png', 'no', 'no', 'no', NULL, NULL, '$2y$12$a3Bi0u6MlmoEdtKmivUKIu1rsMbcF0yWUkQ3l.PPrDSaCbQ60CdWC', NULL, 'active', '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'anonymous', NULL, NULL, NULL, NULL, '0', '0', NULL, NULL, '1993-02-14 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'false', '2026-02-21 06:44:58', '2026-02-21 06:44:58'),
	(85, 20, NULL, NULL, NULL, NULL, 'Test', 'test@test.com', NULL, NULL, 'users/avatar.png', 'no', 'no', 'no', NULL, NULL, '$2y$12$zLGyDkI/Uw62llRoodOaCOs9n1GMcvDbhergH6eDmkVX7Sjra.9j6', NULL, 'active', 'active', NULL, NULL, NULL, 'personal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '28994613136', NULL, 'anonymous', NULL, NULL, NULL, NULL, '0', '0', NULL, NULL, '1993-02-14 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'false', '2026-03-06 06:02:37', '2026-03-06 06:02:37'),
	(86, 20, NULL, NULL, NULL, NULL, 'Paul', 'test1@test.com', NULL, NULL, 'users/avatar.png', 'no', 'no', 'no', NULL, NULL, '$2y$12$vKDgr/ozQ34jf1CE5tOtr.u/RkZm5YFi4xtpoN5L/o43ordode.zy', NULL, 'active', 'active', NULL, NULL, NULL, 'personal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1234567891', NULL, 'anonymous', NULL, NULL, NULL, NULL, '0', '0', NULL, NULL, '1993-02-14 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'false', '2026-03-07 16:46:36', '2026-03-07 16:46:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for table thrd.users_activity
DROP TABLE IF EXISTS `users_activity`;
CREATE TABLE IF NOT EXISTS `users_activity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT '0',
  `page` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op1` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op2` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op3` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4051 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='- tracks the users activity';

-- Dumping data for table thrd.users_activity: ~0 rows (approximately)
/*!40000 ALTER TABLE `users_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_activity` ENABLE KEYS */;

-- Dumping structure for table thrd.user_roles
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `role_id` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_user_roles_users` (`user_id`),
  KEY `FK_user_roles_roles` (`role_id`),
  CONSTRAINT `FK_user_roles_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_user_roles_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.user_roles: ~4 rows (approximately)
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, '2026-03-11 01:21:33', '2026-03-11 01:21:35'),
	(2, 2, 6, '2026-03-11 01:22:12', '2026-03-11 01:22:13'),
	(3, 85, 5, '2026-03-11 01:22:43', '2026-03-11 01:22:44'),
	(4, 86, 4, '2026-03-11 01:25:16', '2026-03-11 01:25:16');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;

-- Dumping structure for table thrd.verify_images
DROP TABLE IF EXISTS `verify_images`;
CREATE TABLE IF NOT EXISTS `verify_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_id` int DEFAULT NULL,
  `reviewer_user_id` int DEFAULT NULL,
  `reason_id` int DEFAULT NULL,
  `reason_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_status_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_status_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validation_count` int DEFAULT NULL,
  `validation_threshold` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.verify_images: ~0 rows (approximately)
/*!40000 ALTER TABLE `verify_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `verify_images` ENABLE KEYS */;

-- Dumping structure for table thrd.videos
DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `circle_item_id` bigint DEFAULT NULL,
  `video_reject_reason` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_vid_comment_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_usr_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_external_id` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_title` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `video_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_web_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_size` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_hits` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `video_likes` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `video_dislikes` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `video_shares` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `video_tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `video_access` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Public',
  `video_privacy` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'None',
  `video_inStore` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `video_price` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '$0.00',
  `video_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'codr license',
  `video_isSeries` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_series_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_series_num` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_series_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_datecreated` timestamp NULL DEFAULT NULL,
  `video_thumbnail_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_duration` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '00:00',
  `video_resolution_x` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `video_resolution_y` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `video_bitrate` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `video_allow_comments` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Yes',
  `video_approval` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Not Approved',
  `video_is_this_refuted` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_rejection_date` datetime NOT NULL,
  `video_claim_dispute_reason` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_successful_or_failure_refute_date` datetime NOT NULL,
  `video_final_dispute_statement` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.videos: ~0 rows (approximately)
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;

-- Dumping structure for table thrd.websockets_statistics_entries
DROP TABLE IF EXISTS `websockets_statistics_entries`;
CREATE TABLE IF NOT EXISTS `websockets_statistics_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `app_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `peak_connection_count` int NOT NULL,
  `websocket_message_count` int NOT NULL,
  `api_message_count` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.websockets_statistics_entries: ~0 rows (approximately)
/*!40000 ALTER TABLE `websockets_statistics_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `websockets_statistics_entries` ENABLE KEYS */;

-- Dumping structure for table thrd.web_activity_logs
DROP TABLE IF EXISTS `web_activity_logs`;
CREATE TABLE IF NOT EXISTS `web_activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referrer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GET',
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `is_banned` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `web_activity_logs_created_at_index` (`created_at`),
  KEY `web_activity_logs_ip_address_is_banned_index` (`ip_address`,`is_banned`),
  KEY `web_activity_logs_ip_address_index` (`ip_address`),
  KEY `web_activity_logs_session_id_index` (`session_id`),
  KEY `web_activity_logs_user_id_index` (`user_id`),
  KEY `web_activity_logs_is_banned_index` (`is_banned`),
  CONSTRAINT `web_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.web_activity_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `web_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `web_activity_logs` ENABLE KEYS */;

-- Dumping structure for table thrd.wishlist
DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` bigint NOT NULL DEFAULT '0',
  `user_id` bigint DEFAULT NULL,
  `circle_item_type_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_type` bigint DEFAULT NULL,
  `file_stored_an_id` bigint DEFAULT NULL,
  `name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `image_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image_filename` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.wishlist: ~0 rows (approximately)
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
