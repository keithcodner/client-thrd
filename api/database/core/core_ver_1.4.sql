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
CREATE DATABASE IF NOT EXISTS `thrd` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `thrd`;

-- Dumping structure for table thrd.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.cache: ~4 rows (approximately)
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('thrd-cache-b39efed6f405d9a00d43b2e6f311148aa7403250', 'i:1;', 1772001061),
	('thrd-cache-b39efed6f405d9a00d43b2e6f311148aa7403250:timer', 'i:1772001061;', 1772001061),
	('thrd-cache-codnerkj@gmail.com|10.0.0.12', 'i:2;', 1771997591),
	('thrd-cache-codnerkj@gmail.com|10.0.0.12:timer', 'i:1771997591;', 1771997591);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;

-- Dumping structure for table thrd.cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.cache_locks: ~0 rows (approximately)
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;

-- Dumping structure for table thrd.credit_transactions
DROP TABLE IF EXISTS `credit_transactions`;
CREATE TABLE IF NOT EXISTS `credit_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `stripe_payment_intent_id` varchar(5000) DEFAULT NULL,
  `credits_amount` int DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `currency` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thrd.credit_transactions: ~0 rows (approximately)
/*!40000 ALTER TABLE `credit_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_transactions` ENABLE KEYS */;

-- Dumping structure for table thrd.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.failed_jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

-- Dumping structure for table thrd.image_test
DROP TABLE IF EXISTS `image_test`;
CREATE TABLE IF NOT EXISTS `image_test` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `original_image_public_id` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `original_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `generated_image_public_id` varchar(50) DEFAULT NULL,
  `generated_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `operation_type` varchar(255) DEFAULT NULL,
  `operation_metadata` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thrd.image_test: ~8 rows (approximately)
/*!40000 ALTER TABLE `image_test` DISABLE KEYS */;
INSERT INTO `image_test` (`id`, `user_id`, `original_image_public_id`, `original_image`, `generated_image_public_id`, `generated_image`, `operation_type`, `operation_metadata`, `created_at`, `updated_at`) VALUES
	(2, 3, 'uploads/2jOK0vqnqAKacg5Re43nBS4lKSBzmsHWKKlMP2kP.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772250070/uploads/2jOK0vqnqAKacg5Re43nBS4lKSBzmsHWKKlMP2kP.jpg', 'uploads/transformed/gen_fill/yzdfsz2x0cigqfdwhkqg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772250085/transformed/gen_fill/yzdfsz2x0cigqfdwhkqg.jpg', 'generative-fill', '{"aspect_ratio":"4:3"}', '2026-02-28 03:41:25', '2026-02-28 03:41:25'),
	(3, 3, 'uploads/qtermaDPpk77PjPMeyOPm2ZPMmOAWHiNdN3GpRfE.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772256204/uploads/qtermaDPpk77PjPMeyOPm2ZPMmOAWHiNdN3GpRfE.jpg', 'uploads/transformed/gen_fill/prnou9ieohl7bhy7cjmi', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772256225/transformed/gen_fill/prnou9ieohl7bhy7cjmi.jpg', 'generative-fill', '{"aspect_ratio":"1:1"}', '2026-02-28 05:23:45', '2026-02-28 05:23:45'),
	(5, 3, 'uploads/FxVYsQspnSlhSInpFIfjjWTVEHQ7ruZaSRHAj2MY.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772310802/uploads/FxVYsQspnSlhSInpFIfjjWTVEHQ7ruZaSRHAj2MY.jpg', 'uploads/transformed/gen_fill/cuhrfs5sfu7odbyl6fa1', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772310810/transformed/gen_fill/cuhrfs5sfu7odbyl6fa1.jpg', 'generative-fill', '{"aspect_ratio":"4:3"}', '2026-02-28 20:33:32', '2026-02-28 20:33:32'),
	(6, 3, 'uploads/vUab7bxX5iqAKyINL3faQMx0PNDzYQ8flCVeobcs.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337144/uploads/vUab7bxX5iqAKyINL3faQMx0PNDzYQ8flCVeobcs.jpg', 'uploads/transformed/gen_fill/r0hirtkazkmm7wurx8wm', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337154/transformed/gen_fill/r0hirtkazkmm7wurx8wm.jpg', 'generative-fill', '{"aspect_ratio":"4:3"}', '2026-03-01 03:52:36', '2026-03-01 03:52:36'),
	(7, 3, 'uploads/c22Dvu51mV2AHcIXSQdEVQbIE2JCM4xFCRkK3RnO.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337248/uploads/c22Dvu51mV2AHcIXSQdEVQbIE2JCM4xFCRkK3RnO.jpg', 'uploads/transformed/gen_fill/rbuq453zp2wd8lvvekmk', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337255/transformed/gen_fill/rbuq453zp2wd8lvvekmk.jpg', 'generative-fill', '{"aspect_ratio":"16:9"}', '2026-03-01 03:54:17', '2026-03-01 03:54:17'),
	(8, 3, 'uploads/meTlp77IpU6sTZCB0UGEUlzfO6OyaOWueqh4CgtK.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337337/uploads/meTlp77IpU6sTZCB0UGEUlzfO6OyaOWueqh4CgtK.jpg', 'uploads/transformed/gen_fill/we89i3nii6k3gdnlb1ad', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337344/transformed/gen_fill/we89i3nii6k3gdnlb1ad.jpg', 'generative-fill', '{"aspect_ratio":"16:9"}', '2026-03-01 03:55:46', '2026-03-01 03:55:46'),
	(9, 3, 'uploads/BiSwWsjNXC4jawcveOeJeABXTCEKxkV6xCxKOksw.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337433/uploads/BiSwWsjNXC4jawcveOeJeABXTCEKxkV6xCxKOksw.jpg', 'uploads/transformed/gen_fill/vw8khginccsdbiwsgfas', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337441/transformed/gen_fill/vw8khginccsdbiwsgfas.jpg', 'generative-fill', '{"aspect_ratio":"16:9"}', '2026-03-01 03:57:22', '2026-03-01 03:57:22'),
	(10, 3, 'uploads/gPqPF5DicgyB3XOGEnFaZalXqWjvdifmOTAsJcWW.jpg', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337472/uploads/gPqPF5DicgyB3XOGEnFaZalXqWjvdifmOTAsJcWW.jpg', 'uploads/transformed/gen_fill/obyczt7cccmtpb08c5l2', 'https://res.cloudinary.com/dr41anqet/image/upload/v1772337477/transformed/gen_fill/obyczt7cccmtpb08c5l2.jpg', 'generative-fill', '{"aspect_ratio":"16:9"}', '2026-03-01 03:57:59', '2026-03-01 03:57:59');
/*!40000 ALTER TABLE `image_test` ENABLE KEYS */;

-- Dumping structure for table thrd.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.job_batches: ~0 rows (approximately)
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;

-- Dumping structure for table thrd.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.migrations: ~4 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_02_21_061537_create_personal_access_tokens_table', 2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table thrd.password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.password_reset_tokens: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;

-- Dumping structure for table thrd.personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.personal_access_tokens: ~3 rows (approximately)
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 2, 'auth_token', '7bd84e46bd76285a62ab00423714a681e1ad576590a2bfd8e97df5bd287e63a1', '["*"]', NULL, NULL, '2026-02-21 06:48:35', '2026-02-21 06:48:35'),
	(3, 'App\\Models\\User', 3, 'auth_token', '9988074f45412577bc8f31478cc580ddb628971258f5d3858c2f0b598b4476c0', '["*"]', NULL, NULL, '2026-02-25 05:32:45', '2026-02-25 05:32:45'),
	(4, 'App\\Models\\User', 3, 'auth_token', '0b4b65465edec69e036cae5977fb680196318bf418ee3b8ef4db5a97ec793045', '["*"]', '2026-03-01 05:49:46', NULL, '2026-02-26 04:43:02', '2026-03-01 05:49:46');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;

-- Dumping structure for table thrd.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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

-- Dumping structure for table thrd.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credits` int DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table thrd.users: ~5 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `credits`, `created_at`, `updated_at`) VALUES
	(1, 'John Doe', 'john@example.com', NULL, '$2y$12$GYImQF/48li/1g8e2Z32Zu5M/.89/BxDEN8ZJFZ/8MBq6YfRS3sge', NULL, 100, '2026-02-21 06:30:49', '2026-02-21 06:30:49'),
	(2, 'John Doe', 'john1@example.com', NULL, '$2y$12$a3Bi0u6MlmoEdtKmivUKIu1rsMbcF0yWUkQ3l.PPrDSaCbQ60CdWC', NULL, 100, '2026-02-21 06:44:58', '2026-02-21 06:44:58'),
	(3, 'test', 'codnerk@gmail.com', NULL, '$2y$12$pYeBnFm1dOZcb8.CeT19z.vvo9Nn2P0ZJeSnTovSVjcb0//4WYsdO', NULL, 100, '2026-02-23 20:28:08', '2026-03-01 03:57:59'),
	(4, 'Laravel', 'test@test.com', NULL, '$2y$12$IJ8RzbKMvWIWr21RvC6Nt.wg7H/p3OTV1u7KtoDCEClxMcldgmBrq', NULL, 100, '2026-02-25 05:54:09', '2026-02-25 05:54:09'),
	(5, 'James', 'test@test.coms', '2026-02-25 06:24:29', '$2y$12$cpu619LB6Em5O3pgWc3RFOO1231NKCQvRJkp3wXxsrgubbA2Wb8Ze', NULL, 100, '2026-02-25 06:24:20', '2026-02-25 06:24:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
