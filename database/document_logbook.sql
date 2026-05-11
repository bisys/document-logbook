-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for document_logbook
CREATE DATABASE IF NOT EXISTS `document_logbook` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `document_logbook`;

-- Dumping structure for table document_logbook.approvals
CREATE TABLE IF NOT EXISTS `approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `approvable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approvable_id` bigint unsigned NOT NULL,
  `approval_role_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `approval_status_id` bigint unsigned NOT NULL,
  `approval_at` timestamp NULL DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approvals_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`),
  KEY `approvals_approval_role_id_foreign` (`approval_role_id`),
  KEY `approvals_user_id_foreign` (`user_id`),
  KEY `approvals_approval_status_id_foreign` (`approval_status_id`),
  CONSTRAINT `approvals_approval_role_id_foreign` FOREIGN KEY (`approval_role_id`) REFERENCES `approval_roles` (`id`),
  CONSTRAINT `approvals_approval_status_id_foreign` FOREIGN KEY (`approval_status_id`) REFERENCES `approval_statuses` (`id`),
  CONSTRAINT `approvals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.approvals: ~0 rows (approximately)
INSERT INTO `approvals` (`id`, `approvable_type`, `approvable_id`, `approval_role_id`, `user_id`, `approval_status_id`, `approval_at`, `remark`, `created_at`, `updated_at`) VALUES
	(42, 'App\\Models\\SupplierPayment', 18, 1, 2, 1, '2026-04-27 09:31:39', 'Okay', '2026-04-27 09:31:39', '2026-04-27 09:31:39'),
	(43, 'App\\Models\\SupplierPayment', 18, 2, 6, 1, '2026-04-27 09:44:04', 'Oke', '2026-04-27 09:44:04', '2026-04-27 09:44:04'),
	(44, 'App\\Models\\PettyCash', 11, 1, 2, 1, '2026-04-28 06:56:46', 'Acc', '2026-04-28 06:56:46', '2026-04-28 06:56:46'),
	(45, 'App\\Models\\PettyCash', 11, 2, 6, 1, '2026-04-28 07:17:03', 'Acc juga', '2026-04-28 07:17:03', '2026-04-28 07:17:03'),
	(46, 'App\\Models\\PettyCash', 12, 1, 2, 1, '2026-04-28 08:13:37', 'Sip', '2026-04-28 08:13:37', '2026-04-28 08:13:37'),
	(47, 'App\\Models\\PettyCash', 12, 2, 6, 1, '2026-04-28 08:19:48', 'Sipp', '2026-04-28 08:19:48', '2026-04-28 08:19:48'),
	(48, 'App\\Models\\PettyCash', 13, 1, 2, 1, '2026-04-29 01:41:25', 'Okay', '2026-04-29 01:41:25', '2026-04-29 01:41:25'),
	(49, 'App\\Models\\PettyCash', 14, 1, 2, 1, '2026-04-29 01:53:28', 'Gas', '2026-04-29 01:53:28', '2026-04-29 01:53:28');

-- Dumping structure for table document_logbook.approval_roles
CREATE TABLE IF NOT EXISTS `approval_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sequence` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `approval_roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.approval_roles: ~2 rows (approximately)
INSERT INTO `approval_roles` (`id`, `name`, `slug`, `sequence`, `created_at`, `updated_at`) VALUES
	(1, 'Accounting Staff', 'accounting-staff', 1, '2026-02-25 20:32:54', '2026-04-27 03:00:02'),
	(2, 'Accounting Manager', 'accounting-manager', 2, '2026-02-25 20:33:06', '2026-04-27 03:00:02');

-- Dumping structure for table document_logbook.approval_statuses
CREATE TABLE IF NOT EXISTS `approval_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `approval_statuses_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.approval_statuses: ~2 rows (approximately)
INSERT INTO `approval_statuses` (`id`, `status`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'Approved', 'approved', '2026-02-25 20:28:19', '2026-02-25 20:28:19'),
	(2, 'Rejected', 'rejected', '2026-02-25 20:28:27', '2026-02-25 20:28:27');

-- Dumping structure for table document_logbook.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.cache: ~0 rows (approximately)

-- Dumping structure for table document_logbook.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.cache_locks: ~0 rows (approximately)

-- Dumping structure for table document_logbook.cash_advance_draw
CREATE TABLE IF NOT EXISTS `cash_advance_draw` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `cost_center_id` bigint unsigned NOT NULL,
  `car_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proposal_or_monitor_budget` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget_plan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_document` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edit_count` int NOT NULL DEFAULT '0',
  `hardfile_received_at` timestamp NULL DEFAULT NULL,
  `hardfile_received_by` bigint unsigned DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `paid_by` bigint unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_receipt_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_status_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_advance_draw_number_unique` (`number`),
  UNIQUE KEY `cash_advance_draw_document_number_unique` (`document_number`),
  KEY `cash_advance_draw_user_id_foreign` (`user_id`),
  KEY `cash_advance_draw_cost_center_id_foreign` (`cost_center_id`),
  KEY `cash_advance_draw_document_status_id_foreign` (`document_status_id`),
  KEY `cash_advance_draw_hardfile_received_by_foreign` (`hardfile_received_by`),
  KEY `cash_advance_draw_paid_by_foreign` (`paid_by`),
  CONSTRAINT `cash_advance_draw_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `cash_advance_draw_document_status_id_foreign` FOREIGN KEY (`document_status_id`) REFERENCES `document_statuses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `cash_advance_draw_hardfile_received_by_foreign` FOREIGN KEY (`hardfile_received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_advance_draw_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`),
  CONSTRAINT `cash_advance_draw_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.cash_advance_draw: ~1 rows (approximately)
INSERT INTO `cash_advance_draw` (`id`, `number`, `user_id`, `cost_center_id`, `car_form`, `document_number`, `proposal_or_monitor_budget`, `budget_plan`, `other_document`, `edit_count`, `hardfile_received_at`, `hardfile_received_by`, `is_paid`, `paid_by`, `paid_at`, `payment_receipt_path`, `document_status_id`, `created_at`, `updated_at`) VALUES
	(2, 'CARD130320260001', 3, 1, 'cash_advance_draw/car_form_CARD130320260001.pdf', '00005', 'cash_advance_draw/proposal_or_monitor_budget_CARD130320260001.pdf', 'cash_advance_draw/budget_plan_CARD130320260001_revised(1).pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 5, '2026-03-12 20:56:13', '2026-03-24 20:24:34'),
	(3, 'CARD230420260001', 3, 1, 'cash_advance_draw/car_form_CARD230420260001.pdf', '002', 'cash_advance_draw/proposal_or_monitor_budget_CARD230420260001.pdf', 'cash_advance_draw/budget_plan_CARD230420260001.pdf', 'cash_advance_draw/other_document_CARD230420260001.pdf', 0, NULL, NULL, 0, NULL, NULL, NULL, 1, '2026-04-23 06:27:24', '2026-04-23 06:27:24');

-- Dumping structure for table document_logbook.cash_advance_realization
CREATE TABLE IF NOT EXISTS `cash_advance_realization` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cash_advance_draw_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `cost_center_id` bigint unsigned DEFAULT NULL,
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `car_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `copy_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_memo_entertain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entertain_realization_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minutes_of_meeting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominative_summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cic_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_evidence` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_document` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edit_count` int NOT NULL DEFAULT '0',
  `hardfile_received_at` timestamp NULL DEFAULT NULL,
  `hardfile_received_by` bigint unsigned DEFAULT NULL,
  `document_status_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_advance_realization_number_unique` (`number`),
  UNIQUE KEY `cash_advance_draw_id` (`cash_advance_draw_id`),
  KEY `cash_advance_realization_cash_advance_draw_id_foreign` (`cash_advance_draw_id`),
  KEY `cash_advance_realization_document_status_id_foreign` (`document_status_id`),
  KEY `cash_advance_realization_user_id_foreign` (`user_id`),
  KEY `cash_advance_realization_cost_center_id_foreign` (`cost_center_id`),
  KEY `cash_advance_realization_hardfile_received_by_foreign` (`hardfile_received_by`),
  CONSTRAINT `cash_advance_realization_cash_advance_draw_id_foreign` FOREIGN KEY (`cash_advance_draw_id`) REFERENCES `cash_advance_draw` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `cash_advance_realization_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`),
  CONSTRAINT `cash_advance_realization_document_status_id_foreign` FOREIGN KEY (`document_status_id`) REFERENCES `document_statuses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `cash_advance_realization_hardfile_received_by_foreign` FOREIGN KEY (`hardfile_received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_advance_realization_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.cash_advance_realization: ~1 rows (approximately)
INSERT INTO `cash_advance_realization` (`id`, `cash_advance_draw_id`, `user_id`, `cost_center_id`, `number`, `car_form`, `original_invoice`, `copy_invoice`, `internal_memo_entertain`, `entertain_realization_form`, `minutes_of_meeting`, `nominative_summary`, `cic_form`, `transfer_evidence`, `other_document`, `edit_count`, `hardfile_received_at`, `hardfile_received_by`, `document_status_id`, `created_at`, `updated_at`) VALUES
	(1, 2, 3, 1, 'CARR250320260001', 'cash_advance_realization/car_form_CARR250320260001.pdf', 'cash_advance_realization/original_invoice_CARR250320260001.pdf', 'cash_advance_realization/copy_invoice_CARR250320260001.pdf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 5, '2026-03-24 21:11:09', '2026-03-24 22:02:44');

-- Dumping structure for table document_logbook.cost_centers
CREATE TABLE IF NOT EXISTS `cost_centers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cost_centers_number_unique` (`number`),
  UNIQUE KEY `cost_centers_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.cost_centers: ~0 rows (approximately)
INSERT INTO `cost_centers` (`id`, `number`, `name`, `slug`, `created_at`, `updated_at`) VALUES
	(1, '60641800', 'System Development Sec', 'system-development-sec', '2026-02-04 00:28:47', '2026-02-04 00:29:23');

-- Dumping structure for table document_logbook.departments
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.departments: ~4 rows (approximately)
INSERT INTO `departments` (`id`, `department`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'SYD-IT', 'syd-it', '2026-01-21 05:02:07', '2026-01-21 05:02:10'),
	(2, 'Accounting', 'accounting', '2026-01-21 05:02:12', '2026-01-22 01:49:35'),
	(3, 'HRGA', 'hrga', '2026-01-21 05:02:14', '2026-01-22 00:24:00'),
	(5, 'Production', 'production', '2026-01-27 00:04:45', '2026-01-27 00:04:45');

-- Dumping structure for table document_logbook.document_statuses
CREATE TABLE IF NOT EXISTS `document_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_statuses_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.document_statuses: ~6 rows (approximately)
INSERT INTO `document_statuses` (`id`, `status`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'Waiting Approval Staff', 'waiting-approval-staff', '2026-03-08 22:00:35', '2026-03-08 22:00:35'),
	(2, 'Waiting Approval Manager', 'waiting-approval-manager', '2026-03-08 22:00:47', '2026-03-08 22:00:47'),
	(3, 'Waiting Approval GM', 'waiting-approval-gm', '2026-03-08 22:01:00', '2026-03-08 22:01:00'),
	(4, 'Waiting Revision', 'waiting-revision', '2026-03-08 22:01:18', '2026-03-08 22:01:18'),
	(5, 'Fully Approved', 'fully-approved', '2026-03-08 22:01:35', '2026-03-08 22:01:35'),
	(6, 'Rejected', 'rejected', '2026-03-08 22:01:44', '2026-03-08 22:01:44');

-- Dumping structure for table document_logbook.document_types
CREATE TABLE IF NOT EXISTS `document_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_types_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.document_types: ~0 rows (approximately)

-- Dumping structure for table document_logbook.failed_jobs
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

-- Dumping data for table document_logbook.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table document_logbook.international_trip
CREATE TABLE IF NOT EXISTS `international_trip` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `cost_center_id` bigint unsigned NOT NULL,
  `itar_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_memo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary_bussiness_trip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `overseas_allowance_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bussiness_trip_allowance` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget_plan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_document` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edit_count` int NOT NULL DEFAULT '0',
  `hardfile_received_at` timestamp NULL DEFAULT NULL,
  `hardfile_received_by` bigint unsigned DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `paid_by` bigint unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_receipt_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_status_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `international_trip_number_unique` (`number`),
  UNIQUE KEY `international_trip_document_number_unique` (`document_number`),
  KEY `international_trip_user_id_foreign` (`user_id`),
  KEY `international_trip_cost_center_id_foreign` (`cost_center_id`),
  KEY `international_trip_document_status_id_foreign` (`document_status_id`),
  KEY `international_trip_hardfile_received_by_foreign` (`hardfile_received_by`),
  KEY `international_trip_paid_by_foreign` (`paid_by`),
  CONSTRAINT `international_trip_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `international_trip_document_status_id_foreign` FOREIGN KEY (`document_status_id`) REFERENCES `document_statuses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `international_trip_hardfile_received_by_foreign` FOREIGN KEY (`hardfile_received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `international_trip_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`),
  CONSTRAINT `international_trip_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.international_trip: ~1 rows (approximately)
INSERT INTO `international_trip` (`id`, `number`, `user_id`, `cost_center_id`, `itar_form`, `document_number`, `internal_memo`, `summary_bussiness_trip`, `overseas_allowance_form`, `bussiness_trip_allowance`, `rate`, `budget_plan`, `other_document`, `edit_count`, `hardfile_received_at`, `hardfile_received_by`, `is_paid`, `paid_by`, `paid_at`, `payment_receipt_path`, `document_status_id`, `created_at`, `updated_at`) VALUES
	(2, 'ITAR120320260001', 3, 1, 'international_trip/itar_form_ITAR120320260001.pdf', '00005', 'international_trip/internal_memo_ITAR120320260001.pdf', 'international_trip/summary_bussiness_trip_ITAR120320260001.pdf', 'international_trip/overseas_allowance_form_ITAR120320260001.pdf', 'international_trip/bussiness_trip_allowance_ITAR120320260001.pdf', 'international_trip/rate_ITAR120320260001.pdf', 'international_trip/budget_plan_ITAR120320260001.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 1, '2026-03-12 01:08:05', '2026-03-12 01:08:05');

-- Dumping structure for table document_logbook.jobs
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
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.jobs: ~21 rows (approximately)
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
	(153, 'default', '{"uuid":"b22d278f-8b38-42b7-b376-19bfde324f78","displayName":"App\\\\Mail\\\\DocumentSubmittedMail","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":17:{s:8:\\"mailable\\";O:30:\\"App\\\\Mail\\\\DocumentSubmittedMail\\":6:{s:8:\\"document\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:20:\\"App\\\\Models\\\\PettyCash\\";s:2:\\"id\\";i:13;s:9:\\"relations\\";a:2:{i:0;s:4:\\"user\\";i:1;s:15:\\"user.department\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"documentType\\";s:10:\\"Petty Cash\\";s:9:\\"submitter\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";i:3;s:9:\\"relations\\";a:1:{i:0;s:10:\\"department\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:3:\\"url\\";s:52:\\"http:\\/\\/127.0.0.1:8000\\/accounting-staff\\/petty-cash\\/13\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:34:\\"accounting02.itsp@thaisummit.co.id\\";}}s:6:\\"mailer\\";s:4:\\"smtp\\";}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:3:\\"job\\";N;}"},"createdAt":1777426495,"delay":null}', 0, NULL, 1777426495, 1777426495),
	(154, 'default', '{"uuid":"0b79a6b1-0d50-49a0-a26e-cae7fb3c3569","displayName":"App\\\\Notifications\\\\DocumentNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:2;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\DocumentNotification\\":2:{s:4:\\"data\\";a:5:{s:5:\\"title\\";s:22:\\"New Document Submitted\\";s:7:\\"message\\";s:73:\\"A new Petty Cash has been submitted for your review by Bisri Farhanullah.\\";s:3:\\"url\\";s:52:\\"http:\\/\\/127.0.0.1:8000\\/accounting-staff\\/petty-cash\\/13\\";s:4:\\"icon\\";s:15:\\"fas fa-file-alt\\";s:7:\\"icon_bg\\";s:10:\\"bg-primary\\";}s:2:\\"id\\";s:36:\\"aaaf9491-e49c-40d5-9287-2ed8b5c4ff8f\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1777426495,"delay":null}', 0, NULL, 1777426495, 1777426495),
	(155, 'default', '{"uuid":"4214bd86-5e28-4312-9994-7dab9bf34a0a","displayName":"App\\\\Mail\\\\DocumentApprovedMail","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":17:{s:8:\\"mailable\\";O:29:\\"App\\\\Mail\\\\DocumentApprovedMail\\":9:{s:8:\\"document\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:20:\\"App\\\\Models\\\\PettyCash\\";s:2:\\"id\\";i:13;s:9:\\"relations\\";a:1:{i:0;s:4:\\"user\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"documentType\\";s:10:\\"Petty Cash\\";s:8:\\"approver\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";i:2;s:9:\\"relations\\";a:1:{i:0;s:4:\\"role\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:16:\\"approverRoleName\\";s:16:\\"Accounting Staff\\";s:6:\\"remark\\";s:4:\\"Okay\\";s:3:\\"url\\";s:40:\\"http:\\/\\/127.0.0.1:8000\\/user\\/petty-cash\\/13\\";s:13:\\"recipientRole\\";s:4:\\"user\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:22:\\"it-04@thaisummit.co.id\\";}}s:6:\\"mailer\\";s:4:\\"smtp\\";}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:3:\\"job\\";N;}"},"createdAt":1777426885,"delay":null}', 0, NULL, 1777426885, 1777426885),
	(156, 'default', '{"uuid":"807a49b9-a419-4ad9-8d29-1f5263178117","displayName":"App\\\\Notifications\\\\DocumentNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:3;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\DocumentNotification\\":2:{s:4:\\"data\\";a:5:{s:5:\\"title\\";s:17:\\"Document Approved\\";s:7:\\"message\\";s:53:\\"The Petty Cash has been approved by Accounting Staff.\\";s:3:\\"url\\";s:40:\\"http:\\/\\/127.0.0.1:8000\\/user\\/petty-cash\\/13\\";s:4:\\"icon\\";s:19:\\"fas fa-check-circle\\";s:7:\\"icon_bg\\";s:10:\\"bg-success\\";}s:2:\\"id\\";s:36:\\"1b68c7b4-5d35-4768-ad0f-0578f8b50855\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1777426885,"delay":null}', 0, NULL, 1777426885, 1777426885),
	(157, 'default', '{"uuid":"b01f7c8b-9098-49ab-9aae-931ba56c22b0","displayName":"App\\\\Mail\\\\DocumentApprovedMail","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":17:{s:8:\\"mailable\\";O:29:\\"App\\\\Mail\\\\DocumentApprovedMail\\":9:{s:8:\\"document\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:20:\\"App\\\\Models\\\\PettyCash\\";s:2:\\"id\\";i:13;s:9:\\"relations\\";a:1:{i:0;s:4:\\"user\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"documentType\\";s:10:\\"Petty Cash\\";s:8:\\"approver\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";i:2;s:9:\\"relations\\";a:1:{i:0;s:4:\\"role\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:16:\\"approverRoleName\\";s:16:\\"Accounting Staff\\";s:6:\\"remark\\";s:4:\\"Okay\\";s:3:\\"url\\";s:54:\\"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/13\\";s:13:\\"recipientRole\\";s:18:\\"accounting-manager\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:35:\\"accounting.manager@thaisummit.co.id\\";}}s:6:\\"mailer\\";s:4:\\"smtp\\";}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:3:\\"job\\";N;}"},"createdAt":1777426885,"delay":null}', 0, NULL, 1777426885, 1777426885),
	(158, 'default', '{"uuid":"567c48de-3e75-49f4-a360-1a54b77e0846","displayName":"App\\\\Notifications\\\\DocumentNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:6;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\DocumentNotification\\":2:{s:4:\\"data\\";a:5:{s:5:\\"title\\";s:17:\\"Document Approved\\";s:7:\\"message\\";s:53:\\"The Petty Cash has been approved by Accounting Staff.\\";s:3:\\"url\\";s:54:\\"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/13\\";s:4:\\"icon\\";s:19:\\"fas fa-check-circle\\";s:7:\\"icon_bg\\";s:10:\\"bg-success\\";}s:2:\\"id\\";s:36:\\"6049b60e-4d71-4a04-838f-63fdcc46686b\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1777426885,"delay":null}', 0, NULL, 1777426885, 1777426885),
	(159, 'default', '{"uuid":"7db310c2-1e9e-4065-a5ac-42bcf058d7b3","displayName":"App\\\\Mail\\\\DocumentSubmittedMail","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":17:{s:8:\\"mailable\\";O:30:\\"App\\\\Mail\\\\DocumentSubmittedMail\\":6:{s:8:\\"document\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:20:\\"App\\\\Models\\\\PettyCash\\";s:2:\\"id\\";i:14;s:9:\\"relations\\";a:2:{i:0;s:4:\\"user\\";i:1;s:15:\\"user.department\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"documentType\\";s:10:\\"Petty Cash\\";s:9:\\"submitter\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";i:3;s:9:\\"relations\\";a:1:{i:0;s:10:\\"department\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:3:\\"url\\";s:52:\\"http:\\/\\/127.0.0.1:8000\\/accounting-staff\\/petty-cash\\/14\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:34:\\"accounting02.itsp@thaisummit.co.id\\";}}s:6:\\"mailer\\";s:4:\\"smtp\\";}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:3:\\"job\\";N;}"},"createdAt":1777427437,"delay":null}', 0, NULL, 1777427437, 1777427437),
	(160, 'default', '{"uuid":"4572eaeb-5e62-4a2f-962d-0373cc25580a","displayName":"App\\\\Notifications\\\\DocumentNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:2;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\DocumentNotification\\":2:{s:4:\\"data\\";a:5:{s:5:\\"title\\";s:22:\\"New Document Submitted\\";s:7:\\"message\\";s:73:\\"A new Petty Cash has been submitted for your review by Bisri Farhanullah.\\";s:3:\\"url\\";s:52:\\"http:\\/\\/127.0.0.1:8000\\/accounting-staff\\/petty-cash\\/14\\";s:4:\\"icon\\";s:15:\\"fas fa-file-alt\\";s:7:\\"icon_bg\\";s:10:\\"bg-primary\\";}s:2:\\"id\\";s:36:\\"7477ad03-8099-4f1a-be81-eac24b94c096\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1777427437,"delay":null}', 0, NULL, 1777427437, 1777427437),
	(161, 'default', '{"uuid":"e8c5ae52-1c46-4098-ac3b-e27e065c40c1","displayName":"App\\\\Mail\\\\DocumentApprovedMail","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":17:{s:8:\\"mailable\\";O:29:\\"App\\\\Mail\\\\DocumentApprovedMail\\":9:{s:8:\\"document\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:20:\\"App\\\\Models\\\\PettyCash\\";s:2:\\"id\\";i:14;s:9:\\"relations\\";a:1:{i:0;s:4:\\"user\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"documentType\\";s:10:\\"Petty Cash\\";s:8:\\"approver\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";i:2;s:9:\\"relations\\";a:1:{i:0;s:4:\\"role\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:16:\\"approverRoleName\\";s:16:\\"Accounting Staff\\";s:6:\\"remark\\";s:3:\\"Gas\\";s:3:\\"url\\";s:40:\\"http:\\/\\/127.0.0.1:8000\\/user\\/petty-cash\\/14\\";s:13:\\"recipientRole\\";s:4:\\"user\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:22:\\"it-04@thaisummit.co.id\\";}}s:6:\\"mailer\\";s:4:\\"smtp\\";}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:3:\\"job\\";N;}"},"createdAt":1777427608,"delay":null}', 0, NULL, 1777427608, 1777427608),
	(162, 'default', '{"uuid":"c30095df-0546-4957-bfac-7b74b5c00f8e","displayName":"App\\\\Notifications\\\\DocumentNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:3;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\DocumentNotification\\":2:{s:4:\\"data\\";a:5:{s:5:\\"title\\";s:17:\\"Document Approved\\";s:7:\\"message\\";s:53:\\"The Petty Cash has been approved by Accounting Staff.\\";s:3:\\"url\\";s:40:\\"http:\\/\\/127.0.0.1:8000\\/user\\/petty-cash\\/14\\";s:4:\\"icon\\";s:19:\\"fas fa-check-circle\\";s:7:\\"icon_bg\\";s:10:\\"bg-success\\";}s:2:\\"id\\";s:36:\\"4b06f670-a4b7-487b-9fae-001177f48db0\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1777427608,"delay":null}', 0, NULL, 1777427608, 1777427608),
	(163, 'default', '{"uuid":"78dc38a8-4486-4fbf-a2c5-34cabf7ee8b9","displayName":"App\\\\Mail\\\\DocumentApprovedMail","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":17:{s:8:\\"mailable\\";O:29:\\"App\\\\Mail\\\\DocumentApprovedMail\\":9:{s:8:\\"document\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:20:\\"App\\\\Models\\\\PettyCash\\";s:2:\\"id\\";i:14;s:9:\\"relations\\";a:1:{i:0;s:4:\\"user\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"documentType\\";s:10:\\"Petty Cash\\";s:8:\\"approver\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";i:2;s:9:\\"relations\\";a:1:{i:0;s:4:\\"role\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:16:\\"approverRoleName\\";s:16:\\"Accounting Staff\\";s:6:\\"remark\\";s:3:\\"Gas\\";s:3:\\"url\\";s:54:\\"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/14\\";s:13:\\"recipientRole\\";s:18:\\"accounting-manager\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:35:\\"accounting.manager@thaisummit.co.id\\";}}s:6:\\"mailer\\";s:4:\\"smtp\\";}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:3:\\"job\\";N;}"},"createdAt":1777427608,"delay":null}', 0, NULL, 1777427608, 1777427608),
	(164, 'default', '{"uuid":"943f1280-d4e6-44c1-87ca-2939db57bfb6","displayName":"App\\\\Notifications\\\\DocumentNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:6;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\DocumentNotification\\":2:{s:4:\\"data\\";a:5:{s:5:\\"title\\";s:17:\\"Document Approved\\";s:7:\\"message\\";s:53:\\"The Petty Cash has been approved by Accounting Staff.\\";s:3:\\"url\\";s:54:\\"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/14\\";s:4:\\"icon\\";s:19:\\"fas fa-check-circle\\";s:7:\\"icon_bg\\";s:10:\\"bg-success\\";}s:2:\\"id\\";s:36:\\"e1ae75d1-26e3-4fa8-a1d3-fab238b7a31d\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1777427608,"delay":null}', 0, NULL, 1777427608, 1777427608),
	(165, 'default', '{"uuid":"7a5f3380-53ca-4f95-876a-a59f0b0448fb","displayName":"App\\\\Mail\\\\HardfileReceivedMail","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":17:{s:8:\\"mailable\\";O:29:\\"App\\\\Mail\\\\HardfileReceivedMail\\":6:{s:8:\\"document\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:20:\\"App\\\\Models\\\\PettyCash\\";s:2:\\"id\\";i:14;s:9:\\"relations\\";a:1:{i:0;s:4:\\"user\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"documentType\\";s:10:\\"Petty Cash\\";s:8:\\"receiver\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";i:2;s:9:\\"relations\\";a:1:{i:0;s:4:\\"role\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:3:\\"url\\";s:40:\\"http:\\/\\/127.0.0.1:8000\\/user\\/petty-cash\\/14\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:22:\\"it-04@thaisummit.co.id\\";}}s:6:\\"mailer\\";s:4:\\"smtp\\";}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:3:\\"job\\";N;}"},"createdAt":1777427702,"delay":null}', 0, NULL, 1777427702, 1777427702),
	(166, 'default', '{"uuid":"021e1b3a-4103-4d62-84ed-505a22bf32ad","displayName":"App\\\\Notifications\\\\DocumentNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:3;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\DocumentNotification\\":2:{s:4:\\"data\\";a:5:{s:5:\\"title\\";s:17:\\"Hardfile Received\\";s:7:\\"message\\";s:65:\\"Your Petty Cash hardfile has been received by Evita Permata Sari.\\";s:3:\\"url\\";s:40:\\"http:\\/\\/127.0.0.1:8000\\/user\\/petty-cash\\/14\\";s:4:\\"icon\\";s:10:\\"fas fa-box\\";s:7:\\"icon_bg\\";s:7:\\"bg-info\\";}s:2:\\"id\\";s:36:\\"e681b1b9-721d-4488-a25d-34570c2a8128\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1777427702,"delay":null}', 0, NULL, 1777427702, 1777427702),
	(167, 'default', '{"uuid":"f21a6589-652f-4a84-8491-a9a61697d754","displayName":"App\\\\Mail\\\\HardfileReceivedMail","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Mail\\\\SendQueuedMailable","command":"O:34:\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\":17:{s:8:\\"mailable\\";O:29:\\"App\\\\Mail\\\\HardfileReceivedMail\\":6:{s:8:\\"document\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:20:\\"App\\\\Models\\\\PettyCash\\";s:2:\\"id\\";i:13;s:9:\\"relations\\";a:1:{i:0;s:4:\\"user\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"documentType\\";s:10:\\"Petty Cash\\";s:8:\\"receiver\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";i:2;s:9:\\"relations\\";a:1:{i:0;s:4:\\"role\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:3:\\"url\\";s:40:\\"http:\\/\\/127.0.0.1:8000\\/user\\/petty-cash\\/13\\";s:2:\\"to\\";a:1:{i:0;a:2:{s:4:\\"name\\";N;s:7:\\"address\\";s:22:\\"it-04@thaisummit.co.id\\";}}s:6:\\"mailer\\";s:4:\\"smtp\\";}s:5:\\"tries\\";N;s:7:\\"timeout\\";N;s:13:\\"maxExceptions\\";N;s:17:\\"shouldBeEncrypted\\";b:0;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:12:\\"messageGroup\\";N;s:12:\\"deduplicator\\";N;s:5:\\"delay\\";N;s:11:\\"afterCommit\\";N;s:10:\\"middleware\\";a:0:{}s:7:\\"chained\\";a:0:{}s:15:\\"chainConnection\\";N;s:10:\\"chainQueue\\";N;s:19:\\"chainCatchCallbacks\\";N;s:3:\\"job\\";N;}"},"createdAt":1777427702,"delay":null}', 0, NULL, 1777427702, 1777427702),
	(168, 'default', '{"uuid":"c6236837-881d-4621-9d36-6b603077c7ee","displayName":"App\\\\Notifications\\\\DocumentNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:3;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:38:\\"App\\\\Notifications\\\\DocumentNotification\\":2:{s:4:\\"data\\";a:5:{s:5:\\"title\\";s:17:\\"Hardfile Received\\";s:7:\\"message\\";s:65:\\"Your Petty Cash hardfile has been received by Evita Permata Sari.\\";s:3:\\"url\\";s:40:\\"http:\\/\\/127.0.0.1:8000\\/user\\/petty-cash\\/13\\";s:4:\\"icon\\";s:10:\\"fas fa-box\\";s:7:\\"icon_bg\\";s:7:\\"bg-info\\";}s:2:\\"id\\";s:36:\\"d8217f19-8f8c-4e98-adf8-6eb5022cca10\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1777427702,"delay":null}', 0, NULL, 1777427702, 1777427702);

-- Dumping structure for table document_logbook.job_batches
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

-- Dumping data for table document_logbook.job_batches: ~0 rows (approximately)

-- Dumping structure for table document_logbook.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.migrations: ~33 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_01_15_014103_create_departments_table', 2),
	(5, '2026_01_15_014916_create_positions_table', 2),
	(6, '2026_01_15_015234_create_roles_table', 2),
	(7, '2026_01_15_015457_create_approvals_table', 2),
	(8, '2026_01_15_024332_edit_users_table', 3),
	(9, '2026_01_15_070843_edit_relation_columns_in_users_table', 4),
	(10, '2026_01_15_083248_add_slug_column_to_users_table', 5),
	(11, '2026_01_28_033724_create_permissions_table', 6),
	(12, '2026_01_28_040416_create_role_permission_table', 6),
	(13, '2026_02_02_041722_create_document_types_table', 7),
	(14, '2026_02_04_071956_create_cost_centers_table', 8),
	(15, '2026_02_04_074844_create_document_statuses_table', 9),
	(16, '2026_02_04_082426_create_supplier_payment_request_table', 10),
	(17, '2026_02_04_083318_create_petty_cash_request_table', 10),
	(18, '2026_02_04_092106_create_cash_advance_request_draw_table', 10),
	(19, '2026_02_04_095604_create_cash_advance_request_realization_table', 10),
	(20, '2026_02_06_030607_create_international_trip_advance_request_table', 10),
	(21, '2026_02_10_094325_create_approval_statuses_table', 11),
	(22, '2026_02_10_094817_create_approval_roles_table', 11),
	(23, '2026_02_11_071053_edit_approvals_table', 11),
	(24, '2026_02_11_091344_create_revision_statuses_table', 11),
	(25, '2026_02_11_091507_create_revisions_table', 11),
	(26, '2026_01_21_000000_add_edit_count_to_supplier_payment_table', 12),
	(27, '2026_03_04_031222_add_edit_count_to_document_tables', 13),
	(28, '2026_03_06_000000_add_role_specific_document_statuses', 14),
	(29, '2026_03_31_012300_create_notifications_table', 15),
	(30, '2026_04_13_000000_add_hardfile_received_columns', 16),
	(31, '2026_04_14_151730_add_payment_columns_to_documents_table', 17),
	(32, '2026_04_23_164906_create_signed_cash_advance_draws_table', 18),
	(33, '2026_04_24_135944_rename_downloaded_columns_in_signed_cash_advance_draws', 19),
	(34, '2026_04_27_030000_add_dynamic_fields_to_approval_roles', 20),
	(35, '2026_04_27_031000_add_is_active_to_approval_roles', 21);

-- Dumping structure for table document_logbook.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.notifications: ~13 rows (approximately)
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
	('0180499a-05d7-4042-acc5-da2159a71518', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Supplier Payment has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/supplier-payment\\/12","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-13 08:39:08', '2026-04-13 08:39:08'),
	('0d6df6b3-f36e-47c9-ac5c-6ca8a2e09011', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Petty Cash has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/4","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-17 06:17:47', '2026-04-17 06:17:47'),
	('0e0e8bf7-3744-4634-b22f-797fa21a6bf4', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 7, '{"title":"Document Approved","message":"The Petty Cash has been approved by Accounting Manager.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-gm\\/petty-cash\\/8","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-15 02:45:38', '2026-04-15 02:45:38'),
	('1849f095-da72-4b59-a185-bcf7dc22a1d5', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Petty Cash has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/8","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-13 09:00:56', '2026-04-13 09:00:56'),
	('55b22309-f181-4cab-aa06-9a8e522ba7c4', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 7, '{"title":"Document Approved","message":"The Petty Cash has been approved by Accounting Manager.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-gm\\/petty-cash\\/9","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-14 08:45:39', '2026-04-14 08:45:39'),
	('5f1a299f-5a3c-44ed-8945-b51ea34db6b5', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Supplier Payment has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/supplier-payment\\/15","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-13 03:52:29', '2026-04-13 03:52:29'),
	('60b1b628-88ef-4655-a512-69fb1c253c64', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Supplier Payment has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/supplier-payment\\/16","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-28 06:48:42', '2026-04-28 06:48:42'),
	('7df42b5a-d253-408d-a2c5-f56c9cae93ef', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 7, '{"title":"Document Approved","message":"The Supplier Payment has been approved by Accounting Manager.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-gm\\/supplier-payment\\/18","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-28 06:48:43', '2026-04-28 06:48:43'),
	('8c92902e-4581-491d-a13c-75116383f02c', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Petty Cash has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/3","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-17 06:17:47', '2026-04-17 06:17:47'),
	('a368dd94-9704-4a7a-908b-63de895ce47b', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Petty Cash has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/11","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-28 06:56:49', '2026-04-28 06:56:49'),
	('a65a4960-9aa9-4406-8ea5-1987d7f1159d', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Petty Cash has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/12","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-28 08:13:39', '2026-04-28 08:13:39'),
	('a6e64251-767a-44f3-a672-831fd38bfcc1', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Supplier Payment has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/supplier-payment\\/13","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-13 08:07:39', '2026-04-13 08:07:39'),
	('bf2a44d1-f590-4bac-977d-16dc4956b00a', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Supplier Payment has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/supplier-payment\\/14","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-13 07:48:06', '2026-04-13 07:48:06'),
	('c8586907-5c0f-49cf-aca7-f695e4a2992f', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Petty Cash has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/7","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-14 08:41:31', '2026-04-14 08:41:31'),
	('d2d973b6-a4dc-4e67-8a43-4eb3b2571032', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Petty Cash has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/petty-cash\\/9","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-13 08:42:15', '2026-04-13 08:42:15'),
	('ecca106d-103f-43ce-bb69-c013e13b0f3b', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 7, '{"title":"Document Approved","message":"The Supplier Payment has been approved by Accounting Manager.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-gm\\/supplier-payment\\/15","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-17 06:55:15', '2026-04-17 06:55:15'),
	('fc5c8587-d7c1-42cf-b27a-a282e1af337e', 'App\\Notifications\\DocumentNotification', 'App\\Models\\User', 6, '{"title":"Document Approved","message":"The Supplier Payment has been approved by Accounting Staff.","url":"http:\\/\\/127.0.0.1:8000\\/accounting-manager\\/supplier-payment\\/18","icon":"fas fa-check-circle","icon_bg":"bg-success"}', NULL, '2026-04-28 06:48:43', '2026-04-28 06:48:43');

-- Dumping structure for table document_logbook.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table document_logbook.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `permission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.permissions: ~6 rows (approximately)
INSERT INTO `permissions` (`id`, `permission`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'view-user', 'view-user', '2026-01-28 20:44:16', '2026-01-28 20:44:16'),
	(2, 'create-user', 'create-user', '2026-01-28 20:44:17', '2026-01-28 20:44:17'),
	(3, 'edit-user', 'edit-user', '2026-01-28 20:44:17', '2026-01-28 20:44:17'),
	(4, 'delete-user', 'delete-user', '2026-01-28 20:44:17', '2026-01-28 20:44:17'),
	(5, 'view-report', 'view-report', '2026-01-28 20:44:17', '2026-01-28 20:44:17'),
	(6, 'create-report', 'create-report', '2026-01-28 20:44:17', '2026-01-28 20:44:17');

-- Dumping structure for table document_logbook.petty_cash
CREATE TABLE IF NOT EXISTS `petty_cash` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `cost_center_id` bigint unsigned NOT NULL,
  `pcr_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `copy_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `internal_memo_entertain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entertain_realization_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minutes_of_meeting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominative_summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cic_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget_plan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_document` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edit_count` int NOT NULL DEFAULT '0',
  `hardfile_received_at` timestamp NULL DEFAULT NULL,
  `hardfile_received_by` bigint unsigned DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `paid_by` bigint unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_receipt_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_status_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `petty_cash_number_unique` (`number`),
  UNIQUE KEY `petty_cash_document_number_unique` (`document_number`),
  KEY `petty_cash_user_id_foreign` (`user_id`),
  KEY `petty_cash_cost_center_id_foreign` (`cost_center_id`),
  KEY `petty_cash_document_status_id_foreign` (`document_status_id`),
  KEY `petty_cash_hardfile_received_by_foreign` (`hardfile_received_by`),
  KEY `petty_cash_paid_by_foreign` (`paid_by`),
  CONSTRAINT `petty_cash_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `petty_cash_document_status_id_foreign` FOREIGN KEY (`document_status_id`) REFERENCES `document_statuses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `petty_cash_hardfile_received_by_foreign` FOREIGN KEY (`hardfile_received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `petty_cash_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`),
  CONSTRAINT `petty_cash_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.petty_cash: ~10 rows (approximately)
INSERT INTO `petty_cash` (`id`, `number`, `user_id`, `cost_center_id`, `pcr_form`, `document_number`, `original_invoice`, `copy_invoice`, `tax_invoice`, `internal_memo_entertain`, `entertain_realization_form`, `minutes_of_meeting`, `nominative_summary`, `cic_form`, `budget_plan`, `other_document`, `edit_count`, `hardfile_received_at`, `hardfile_received_by`, `is_paid`, `paid_by`, `paid_at`, `payment_receipt_path`, `document_status_id`, `created_at`, `updated_at`) VALUES
	(2, 'PCR120320260001', 3, 1, 'petty_cash/pcr_form_PCR120320260001.pdf', '00002', 'petty_cash/original_invoice_PCR120320260001_revised(1).pdf', 'petty_cash/copy_invoice_PCR120320260001_revised(2).pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR120320260001.pdf', NULL, 1, NULL, NULL, 0, NULL, NULL, NULL, 5, '2026-03-11 20:41:40', '2026-03-11 21:02:55'),
	(3, 'PCR120320260002', 3, 1, 'petty_cash/pcr_form_PCR120320260002.pdf', '00003', 'petty_cash/original_invoice_PCR120320260002.pdf', 'petty_cash/copy_invoice_PCR120320260002.pdf', NULL, 'petty_cash/internal_memo_entertain_PCR120320260002_edited(1).pdf', NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR120320260002.pdf', NULL, 1, NULL, NULL, 0, NULL, NULL, NULL, 2, '2026-03-11 23:50:41', '2026-04-17 06:17:44'),
	(4, 'PCR300320260001', 3, 1, 'petty_cash/pcr_form_PCR300320260001.pdf', '0105', 'petty_cash/original_invoice_PCR300320260001.pdf', 'petty_cash/copy_invoice_PCR300320260001.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR300320260001.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 2, '2026-03-30 01:34:35', '2026-04-17 06:17:44'),
	(5, 'PCR300320260002', 3, 1, 'petty_cash/pcr_form_PCR300320260002.pdf', '0106', 'petty_cash/original_invoice_PCR300320260002.pdf', 'petty_cash/copy_invoice_PCR300320260002.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR300320260002.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 1, '2026-03-30 01:40:25', '2026-03-30 01:40:25'),
	(6, 'PCR300320260003', 3, 1, 'petty_cash/pcr_form_PCR300320260003.pdf', '0107', 'petty_cash/original_invoice_PCR300320260003.pdf', 'petty_cash/copy_invoice_PCR300320260003.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR300320260003.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 1, '2026-03-30 01:55:21', '2026-03-30 01:55:21'),
	(7, 'PCR300320260004', 3, 1, 'petty_cash/pcr_form_PCR300320260004.pdf', '0108', 'petty_cash/original_invoice_PCR300320260004.pdf', 'petty_cash/copy_invoice_PCR300320260004.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR300320260004.pdf', NULL, 0, '2026-04-16 09:53:56', 2, 0, NULL, NULL, NULL, 2, '2026-03-30 02:36:02', '2026-04-16 09:53:56'),
	(8, 'PCR300320260005', 3, 1, 'petty_cash/pcr_form_PCR300320260005.pdf', '0109', 'petty_cash/original_invoice_PCR300320260005.pdf', 'petty_cash/copy_invoice_PCR300320260005.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR300320260005.pdf', NULL, 0, '2026-04-15 02:44:40', 2, 1, 2, '2026-04-15 02:50:17', 'payments/petty_cash/B9g7vfk9P902g2B2rtuReiJ3Y2ycTNjBErlspnU0.png', 5, '2026-03-30 02:42:13', '2026-04-15 02:50:17'),
	(9, 'PCR310320260001', 3, 1, 'petty_cash/pcr_form_PCR310320260001.pdf', '0110', 'petty_cash/original_invoice_PCR310320260001.pdf', 'petty_cash/copy_invoice_PCR310320260001.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR310320260001.pdf', NULL, 0, '2026-04-14 08:44:57', 2, 1, 2, '2026-04-14 08:46:58', 'payments/petty_cash/XRZMV8KNrsKkz8mgYFXx9mVTPtsUyUVdbbgcytS1.jpg', 5, '2026-03-30 18:30:44', '2026-04-14 08:46:58'),
	(10, 'PCR020420260001', 3, 1, 'petty_cash/pcr_form_PCR020420260001.pdf', '01', 'petty_cash/original_invoice_PCR020420260001.pdf', 'petty_cash/copy_invoice_PCR020420260001.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR020420260001.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 1, '2026-04-01 23:48:10', '2026-04-01 23:48:10'),
	(11, 'PCR220420260001', 3, 1, 'petty_cash/pcr_form_PCR220420260001.pdf', '002', 'petty_cash/original_invoice_PCR220420260001.jpg', 'petty_cash/copy_invoice_PCR220420260001.pdf', 'petty_cash/tax_invoice_PCR220420260001.pdf', NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR220420260001.xlsx', NULL, 0, '2026-04-28 07:14:28', 2, 0, NULL, NULL, NULL, 3, '2026-04-22 03:06:43', '2026-04-28 07:17:03'),
	(12, 'PCR280420260001', 3, 1, 'petty_cash/pcr_form_PCR280420260001.pdf', '005', 'petty_cash/original_invoice_PCR280420260001.pdf', 'petty_cash/copy_invoice_PCR280420260001.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR280420260001.pdf', NULL, 0, '2026-04-28 08:16:41', 2, 1, 2, '2026-04-28 08:22:23', 'payments/petty_cash/Jr1YjpZDZCashxcg9sC9GSrfOEPMaEjMYd70qafh.png', 5, '2026-04-28 08:11:33', '2026-04-28 08:22:23'),
	(13, 'PCR290420260001', 3, 1, 'petty_cash/pcr_form_PCR290420260001.pdf', '003', 'petty_cash/original_invoice_PCR290420260001.pdf', 'petty_cash/copy_invoice_PCR290420260001.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR290420260001.pdf', NULL, 0, '2026-04-29 01:55:02', 2, 0, NULL, NULL, NULL, 2, '2026-04-29 01:34:55', '2026-04-29 01:55:02'),
	(14, 'PCR290420260002', 3, 1, 'petty_cash/pcr_form_PCR290420260002.pdf', '004', 'petty_cash/original_invoice_PCR290420260002.pdf', 'petty_cash/copy_invoice_PCR290420260002.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'petty_cash/budget_plan_PCR290420260002.pdf', NULL, 0, '2026-04-29 01:55:02', 2, 0, NULL, NULL, NULL, 2, '2026-04-29 01:50:37', '2026-04-29 01:55:02');

-- Dumping structure for table document_logbook.positions
CREATE TABLE IF NOT EXISTS `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `positions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.positions: ~4 rows (approximately)
INSERT INTO `positions` (`id`, `position`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'Staff', 'staff', '2026-01-23 09:53:48', '2026-01-23 03:02:00'),
	(2, 'Admin', 'admin', '2026-01-23 02:55:30', '2026-01-23 03:01:25'),
	(3, 'Manager', 'manager', '2026-02-26 00:16:51', '2026-02-26 00:16:51'),
	(4, 'General Manager', 'general-manager', '2026-02-26 00:17:00', '2026-02-26 00:17:00');

-- Dumping structure for table document_logbook.revisions
CREATE TABLE IF NOT EXISTS `revisions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `revisable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revisable_id` bigint unsigned NOT NULL,
  `revision_times` int unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `revision_status_id` bigint unsigned NOT NULL,
  `revision_at` timestamp NULL DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `revisions_revisable_type_revisable_id_index` (`revisable_type`,`revisable_id`),
  KEY `revisions_user_id_foreign` (`user_id`),
  KEY `revisions_revision_status_id_foreign` (`revision_status_id`),
  CONSTRAINT `revisions_revision_status_id_foreign` FOREIGN KEY (`revision_status_id`) REFERENCES `revision_statuses` (`id`),
  CONSTRAINT `revisions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.revisions: ~13 rows (approximately)
INSERT INTO `revisions` (`id`, `revisable_type`, `revisable_id`, `revision_times`, `user_id`, `revision_status_id`, `revision_at`, `remark`, `created_at`, `updated_at`) VALUES
	(6, 'App\\Models\\SupplierPayment', 4, 1, 2, 1, '2026-03-02 21:34:12', 'www', '2026-03-02 20:42:00', '2026-03-02 21:34:12'),
	(7, 'App\\Models\\SupplierPayment', 5, 1, 2, 2, '2026-03-05 20:13:08', 'test revisi', '2026-03-05 20:09:18', '2026-03-05 20:13:08'),
	(8, 'App\\Models\\SupplierPayment', 7, 1, 2, 2, '2026-03-10 01:12:50', 'Test 1', '2026-03-10 01:07:55', '2026-03-10 01:12:50'),
	(9, 'App\\Models\\PettyCash', 2, 1, 2, 2, '2026-03-11 20:48:43', 'test 1', '2026-03-11 20:46:28', '2026-03-11 20:48:43'),
	(10, 'App\\Models\\PettyCash', 2, 2, 2, 2, '2026-03-11 20:55:50', 'test 2', '2026-03-11 20:52:12', '2026-03-11 20:55:50'),
	(11, 'App\\Models\\CashAdvanceDraw', 2, 1, 2, 2, '2026-03-24 19:54:47', 'Revisi 1', '2026-03-24 19:51:27', '2026-03-24 19:54:47'),
	(12, 'App\\Models\\SupplierPayment', 10, 1, 2, 1, '2026-03-29 23:57:39', 'Revisi 1', '2026-03-29 23:57:39', '2026-03-29 23:57:39'),
	(13, 'App\\Models\\SupplierPayment', 9, 1, 2, 2, '2026-04-23 06:29:25', 'Revisi 1', '2026-03-30 00:09:35', '2026-04-23 06:29:25'),
	(14, 'App\\Models\\SupplierPayment', 8, 1, 2, 1, '2026-03-30 00:28:12', 'Revisi1', '2026-03-30 00:28:12', '2026-03-30 00:28:12'),
	(15, 'App\\Models\\SupplierPayment', 11, 1, 2, 1, '2026-03-30 02:37:14', 'rev', '2026-03-30 02:37:14', '2026-03-30 02:37:14'),
	(16, 'App\\Models\\PettyCash', 9, 1, 2, 2, '2026-03-30 18:50:01', 'www', '2026-03-30 18:41:03', '2026-03-30 18:50:01'),
	(17, 'App\\Models\\SupplierPayment', 17, 1, 2, 1, '2026-04-23 07:29:17', 'www', '2026-04-23 07:29:17', '2026-04-23 07:29:17'),
	(18, 'App\\Models\\SupplierPayment', 17, 2, 2, 1, '2026-04-23 07:29:17', 'www', '2026-04-23 07:29:17', '2026-04-23 07:29:17'),
	(19, 'App\\Models\\SupplierPayment', 17, 3, 2, 1, '2026-04-23 07:36:37', 'aaa', '2026-04-23 07:36:37', '2026-04-23 07:36:37');

-- Dumping structure for table document_logbook.revision_statuses
CREATE TABLE IF NOT EXISTS `revision_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.revision_statuses: ~2 rows (approximately)
INSERT INTO `revision_statuses` (`id`, `status`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'Revision Requested', 'revision-requested', '2026-02-25 20:34:25', '2026-02-25 20:34:25'),
	(2, 'Revised', 'revised', '2026-02-25 20:34:33', '2026-02-25 20:34:33');

-- Dumping structure for table document_logbook.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.roles: ~5 rows (approximately)
INSERT INTO `roles` (`id`, `role`, `slug`, `created_at`, `updated_at`) VALUES
	(1, 'Admin', 'admin', '2026-01-29 08:33:14', '2026-01-29 08:33:15'),
	(2, 'Accounting Staff', 'accounting-staff', '2026-01-29 08:33:16', '2026-02-17 20:18:36'),
	(3, 'Accounting Manager', 'accounting-manager', '2026-01-29 08:33:18', '2026-02-17 20:19:01'),
	(4, 'Accounting GM', 'accounting-gm', '2026-02-17 20:19:17', '2026-02-17 20:19:17'),
	(5, 'User', 'user', '2026-02-17 20:19:24', '2026-02-17 20:19:24');

-- Dumping structure for table document_logbook.role_permission
CREATE TABLE IF NOT EXISTS `role_permission` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_permission_role_id_foreign` (`role_id`),
  KEY `role_permission_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.role_permission: ~12 rows (approximately)
INSERT INTO `role_permission` (`id`, `role_id`, `permission_id`) VALUES
	(1, 2, 1),
	(2, 2, 2),
	(7, 1, 1),
	(8, 1, 2),
	(9, 1, 3),
	(10, 1, 4),
	(19, 3, 5),
	(20, 3, 6),
	(27, 1, 5),
	(28, 1, 6),
	(29, 4, 5),
	(30, 4, 6);

-- Dumping structure for table document_logbook.sessions
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

-- Dumping data for table document_logbook.sessions: ~3 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('GgQffzE5fV4v6bJ4ioLuwK47ZpMgZFo6kEMcI90o', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTE9lbkU2OHFzM2V1Nk8zRk5BdDFaalJ3MTJQTDdKTG9BWlQ2TEdYaCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hY2NvdW50aW5nLXN0YWZmL2Rhc2hib2FyZCI7czo1OiJyb3V0ZSI7czoyNjoiYWNjb3VudGluZy1zdGFmZi5kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1777431185);

-- Dumping structure for table document_logbook.signed_cash_advance_draws
CREATE TABLE IF NOT EXISTS `signed_cash_advance_draws` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_opened` tinyint(1) NOT NULL DEFAULT '0',
  `opened_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.signed_cash_advance_draws: ~8 rows (approximately)
INSERT INTO `signed_cash_advance_draws` (`id`, `file_name`, `file_path`, `is_opened`, `opened_at`, `created_at`, `updated_at`) VALUES
	(1, '20260424_093531_1_cash_advance_draw_signed_acct.pdf', 'signed_cash_advance_draws/20260424_093531_1_cash_advance_draw_signed_acct.pdf', 1, '2026-04-24 07:44:15', '2026-04-24 02:35:31', '2026-04-24 07:44:15'),
	(2, '20260424_100813_1_cash_advance_draw_signed_acct.pdf', 'signed_cash_advance_draws/20260424_100813_1_cash_advance_draw_signed_acct.pdf', 1, '2026-04-24 07:44:25', '2026-04-24 03:08:13', '2026-04-24 07:44:25'),
	(3, '20260424_100813_2_cash_advance_draw_signed_acct.pdf', 'signed_cash_advance_draws/20260424_100813_2_cash_advance_draw_signed_acct.pdf', 1, '2026-04-24 07:44:30', '2026-04-24 03:08:13', '2026-04-24 07:44:30'),
	(4, '20260424_102915_1_cash_advance_draw_signed_acct.pdf', 'signed_cash_advance_draws/20260424_102915_1_cash_advance_draw_signed_acct.pdf', 1, '2026-04-24 07:07:04', '2026-04-24 03:29:15', '2026-04-24 07:07:04'),
	(5, '20260424_102915_2_cash_advance_draw_signed_acct.pdf', 'signed_cash_advance_draws/20260424_102915_2_cash_advance_draw_signed_acct.pdf', 0, NULL, '2026-04-24 03:29:15', '2026-04-24 03:29:15'),
	(6, '20260424_103604_1_cash_advance_draw_signed_acct.pdf', 'signed_cash_advance_draws/20260424_103604_1_cash_advance_draw_signed_acct.pdf', 0, NULL, '2026-04-24 03:36:04', '2026-04-24 03:36:04'),
	(7, '20260424_103604_2_cash_advance_draw_signed_acct.pdf', 'signed_cash_advance_draws/20260424_103604_2_cash_advance_draw_signed_acct.pdf', 0, NULL, '2026-04-24 03:36:04', '2026-04-24 03:36:04'),
	(8, '20260424_145906_1_cash_advance_draw_signed_acct.pdf', 'signed_cash_advance_draws/20260424_145906_1_cash_advance_draw_signed_acct.pdf', 0, NULL, '2026-04-24 07:59:06', '2026-04-24 07:59:06');

-- Dumping structure for table document_logbook.supplier_payment
CREATE TABLE IF NOT EXISTS `supplier_payment` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `cost_center_id` bigint unsigned NOT NULL,
  `spr_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `copy_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agreement` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `internal_memo_entertain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entertain_realization_form` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minutes_of_meeting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominative_summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calculation_summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget_plan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_document` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edit_count` int unsigned NOT NULL DEFAULT '0',
  `hardfile_received_at` timestamp NULL DEFAULT NULL,
  `hardfile_received_by` bigint unsigned DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `paid_by` bigint unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_receipt_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_status_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_payment_number_unique` (`number`),
  UNIQUE KEY `supplier_payment_document_number_unique` (`document_number`),
  KEY `supplier_payment_user_id_foreign` (`user_id`),
  KEY `supplier_payment_cost_center_id_foreign` (`cost_center_id`),
  KEY `supplier_payment_document_status_id_foreign` (`document_status_id`),
  KEY `supplier_payment_hardfile_received_by_foreign` (`hardfile_received_by`),
  KEY `supplier_payment_paid_by_foreign` (`paid_by`),
  CONSTRAINT `supplier_payment_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `supplier_payment_document_status_id_foreign` FOREIGN KEY (`document_status_id`) REFERENCES `document_statuses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `supplier_payment_hardfile_received_by_foreign` FOREIGN KEY (`hardfile_received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_payment_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`),
  CONSTRAINT `supplier_payment_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.supplier_payment: ~11 rows (approximately)
INSERT INTO `supplier_payment` (`id`, `number`, `user_id`, `cost_center_id`, `spr_form`, `document_number`, `original_invoice`, `copy_invoice`, `tax_invoice`, `agreement`, `internal_memo_entertain`, `entertain_realization_form`, `minutes_of_meeting`, `nominative_summary`, `calculation_summary`, `budget_plan`, `other_document`, `edit_count`, `hardfile_received_at`, `hardfile_received_by`, `is_paid`, `paid_by`, `paid_at`, `payment_receipt_path`, `document_status_id`, `created_at`, `updated_at`) VALUES
	(7, 'SPR100320260001', 3, 1, 'supplier_payments/spr_form_SPR100320260001.pdf', '00001', 'supplier_payments/original_invoice_SPR100320260001_edited(1).pdf', 'supplier_payments/copy_invoice_SPR100320260001_revised(1).pdf', 'supplier_payments/tax_invoice_SPR100320260001.pdf', 'supplier_payments/agreement_SPR100320260001.pdf', NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR100320260001.pdf', NULL, 1, '2026-04-13 03:30:40', 2, 1, 2, '2026-04-16 08:17:41', 'payments/supplier_payment/XJGNi1OfZQTrKnJ066WDBFSc6F33clAeKyMdpK09.png', 5, '2026-03-09 23:58:16', '2026-04-16 08:17:41'),
	(8, 'SPR120320260001', 3, 1, 'supplier_payments/spr_form_SPR120320260001.pdf', '00004', 'supplier_payments/original_invoice_SPR120320260001.pdf', 'supplier_payments/copy_invoice_SPR120320260001.pdf', 'supplier_payments/tax_invoice_SPR120320260001.pdf', 'supplier_payments/agreement_SPR120320260001.pdf', 'supplier_payments/internal_memo_entertain_SPR120320260001.pdf', NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR120320260001.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 6, '2026-03-12 00:20:35', '2026-03-30 19:26:56'),
	(9, 'SPR300320260001', 3, 1, 'supplier_payments/spr_form_SPR300320260001.pdf', '0101', 'supplier_payments/original_invoice_SPR300320260001.pdf', 'supplier_payments/copy_invoice_SPR300320260001.pdf', 'supplier_payments/tax_invoice_SPR300320260001.pdf', 'supplier_payments/agreement_SPR300320260001.pdf', NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR300320260001.pdf', 'supplier_payments/other_document_SPR300320260001_revised(1).pdf', 0, NULL, NULL, 0, NULL, NULL, NULL, 1, '2026-03-29 23:46:19', '2026-04-23 06:29:25'),
	(10, 'SPR300320260002', 3, 1, 'supplier_payments/spr_form_SPR300320260002.pdf', '0102', 'supplier_payments/original_invoice_SPR300320260002.pdf', 'supplier_payments/copy_invoice_SPR300320260002.pdf', 'supplier_payments/tax_invoice_SPR300320260002.pdf', 'supplier_payments/agreement_SPR300320260002.pdf', NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR300320260002.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 4, '2026-03-29 23:52:14', '2026-03-29 23:57:39'),
	(11, 'SPR300320260003', 3, 1, 'supplier_payments/spr_form_SPR300320260003.pdf', '0103', 'supplier_payments/original_invoice_SPR300320260003.pdf', 'supplier_payments/copy_invoice_SPR300320260003.pdf', 'supplier_payments/tax_invoice_SPR300320260003.pdf', 'supplier_payments/agreement_SPR300320260003.pdf', NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR300320260003.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 4, '2026-03-30 01:58:17', '2026-03-30 02:37:14'),
	(12, 'SPR300320260004', 3, 1, 'supplier_payments/spr_form_SPR300320260004.pdf', '0104', 'supplier_payments/original_invoice_SPR300320260004.pdf', 'supplier_payments/copy_invoice_SPR300320260004.pdf', 'supplier_payments/tax_invoice_SPR300320260004.pdf', 'supplier_payments/agreement_SPR300320260004.pdf', NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR300320260004.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 2, '2026-03-30 02:04:01', '2026-04-13 08:39:07'),
	(13, 'SPR300320260005', 3, 1, 'supplier_payments/spr_form_SPR300320260005.pdf', '0106', 'supplier_payments/original_invoice_SPR300320260005.pdf', 'supplier_payments/copy_invoice_SPR300320260005.pdf', 'supplier_payments/tax_invoice_SPR300320260005.pdf', 'supplier_payments/agreement_SPR300320260005.pdf', NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR300320260005.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 2, '2026-03-30 02:13:48', '2026-04-13 08:07:36'),
	(14, 'SPR300320260006', 3, 1, 'supplier_payments/spr_form_SPR300320260006.pdf', '0107', 'supplier_payments/original_invoice_SPR300320260006.pdf', 'supplier_payments/copy_invoice_SPR300320260006.pdf', 'supplier_payments/tax_invoice_SPR300320260006.pdf', 'supplier_payments/agreement_SPR300320260006.pdf', NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR300320260006.pdf', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 2, '2026-03-30 02:20:36', '2026-04-13 07:48:03'),
	(15, 'SPR300320260007', 3, 1, 'supplier_payments/spr_form_SPR300320260007.pdf', '0108', 'supplier_payments/original_invoice_SPR300320260007.pdf', 'supplier_payments/copy_invoice_SPR300320260007.pdf', 'supplier_payments/tax_invoice_SPR300320260007.pdf', 'supplier_payments/agreement_SPR300320260007.pdf', NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR300320260007.pdf', NULL, 0, '2026-04-13 03:51:06', 2, 1, 2, '2026-04-21 07:46:52', 'payments/supplier_payment/IOu16fvNBxh4PNfzyi3A6bmIxIt8j1pWLPWCoWSX.jpg', 5, '2026-03-30 02:26:46', '2026-04-21 07:46:52'),
	(16, 'SPR220420260001', 3, 1, 'supplier_payments/spr_form_SPR220420260001.pdf', '002', 'supplier_payments/original_invoice_SPR220420260001.pdf', 'supplier_payments/copy_invoice_SPR220420260001.pdf', 'supplier_payments/tax_invoice_SPR220420260001.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR220420260001.xlsx', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 2, '2026-04-22 02:59:01', '2026-04-23 07:30:39'),
	(17, 'SPR230420260001', 3, 1, 'supplier_payments/spr_form_SPR230420260001.pdf', '003', 'supplier_payments/original_invoice_SPR230420260001.pdf', 'supplier_payments/copy_invoice_SPR230420260001.pdf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR230420260001.pdf', 'supplier_payments/other_document_SPR230420260001.jpg', 2, NULL, NULL, 0, NULL, NULL, NULL, 4, '2026-04-23 06:23:20', '2026-04-23 07:29:17'),
	(18, 'SPR270420260001', 3, 1, 'supplier_payments/spr_form_SPR270420260001.pdf', '004', 'supplier_payments/original_invoice_SPR270420260001.pdf', 'supplier_payments/copy_invoice_SPR270420260001.pdf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'supplier_payments/budget_plan_SPR270420260001.pdf', NULL, 0, '2026-04-27 09:37:13', 2, 1, 2, '2026-04-27 09:45:44', 'payments/supplier_payment/s6CTqafrNfowUK42TU4lLyHzji19I0Cd8KR9CKeb.jpg', 5, '2026-04-27 09:29:51', '2026-04-27 09:45:44');

-- Dumping structure for table document_logbook.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` bigint unsigned NOT NULL,
  `position_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  UNIQUE KEY `users_slug_unique` (`slug`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_department_id_foreign` (`department_id`),
  KEY `users_position_id_foreign` (`position_id`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table document_logbook.users: ~7 rows (approximately)
INSERT INTO `users` (`id`, `employee_id`, `name`, `slug`, `email`, `email_verified_at`, `password`, `department_id`, `position_id`, `role_id`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, '111.11.11', 'Admin', 'admin', 'admin@thaisummit.co.id', NULL, '$2y$12$wPp5MaHmjDuZAKUy1RcaC.eHNVCwR3zXQb/qpQbcDEUU5EKUnDFV.', 1, 1, 1, 'CtlSIjxo0fI8bqpcYMCSQivwvdBMwkRYXbVrQ26ORJB0vGdAzmGVc7Mudxk8', '2026-01-27 04:43:03', '2026-01-20 01:10:46'),
	(2, '222.22.22', 'Evita Permata Sari', 'evita-permata-sari', 'accounting02.itsp@thaisummit.co.id', NULL, '$2y$12$Wv1lL2pn6EgDkoctwljdVebhmoZdzoCp4XRPuumxO08fWui0ij5c.', 2, 1, 2, NULL, '2026-01-27 04:43:04', '2026-04-01 20:51:51'),
	(3, '333.33.33', 'Bisri Farhanullah', 'bisri-farhanullah', 'it-04@thaisummit.co.id', NULL, '$2y$12$rNFdVbltIxNgSYNL3yh0bOUACGRl96Fg6D2UUAQ7Ripu/iw9Q6EVK', 1, 1, 5, NULL, '2026-01-27 04:43:05', '2026-04-01 00:55:48'),
	(5, '444.44.44', 'User Production', 'user-production', 'production@thaisummit.co.id', NULL, '$2y$12$8yzUkb9VLgnjXjNoBOFKp.P7729IeIovXq8fe7YQ7zwPOkjakDUpu', 5, 2, 5, NULL, '2026-01-27 00:22:31', '2026-02-17 20:27:53'),
	(6, '555.55.55', 'Accounting Manager', 'accounting-manager', 'accounting.manager@thaisummit.co.id', NULL, '$2y$12$umY.mFoOLhp9.K5/cgFrEe3uZsiWNd3EOkXNThgF.O0D9UMZZAfta', 2, 3, 3, NULL, '2026-02-26 00:15:51', '2026-04-13 10:00:51'),
	(7, '666.66.66', 'Accounting GM', 'accounting-gm', 'accounting.gm@thaisummit.co.id', NULL, '$2y$12$PX48LIVtL3n7zxN7Lqk2pO8io2ciT65qJpI5KO/pDy6X5V5AS35jC', 2, 4, 4, NULL, '2026-02-26 00:16:35', '2026-02-26 00:17:25'),
	(8, '777.77.77', 'User 2', 'user-2', 'dcc-01.itsp@thaisummit.co.id', NULL, '$2y$12$gVjwANF/24MThbKFCzsK/OLR88kwayXz2CBIeN9hCmhAjFOADe9P2', 1, 1, 5, NULL, '2026-03-31 02:39:03', '2026-03-31 02:39:03');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
