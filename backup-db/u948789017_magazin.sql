-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 16, 2026 at 11:01 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u948789017_magazin`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `kicker` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `subtitle`, `kicker`, `image`, `link`, `active`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(3, NULL, NULL, NULL, 'banners/o98PVJJVPjQINtprILpLMe5ii0BFspB8tX0HuDSV.png', NULL, 1, 0, 1, '2026-02-01 03:05:39', '2026-02-01 03:05:39');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-maib_access_token', 's:1365:\"eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICI0cTg3R2dKSU1PZllBMEZWNHZvZDJSQ1F4UlREQWoyZ1pEcEtvMGM4c3RBIn0.eyJleHAiOjE3NzAzMjg1MDQsImlhdCI6MTc3MDMyODIwNCwianRpIjoiMjZkZWJlYTUtNDliMS00Mjc3LThkN2MtYmY5NTRkMjEzYjc4IiwiaXNzIjoiaHR0cHM6Ly9rYy5tYWliLm1kL3JlYWxtcy9Vc2Vyc1BKQXV0aCIsImF1ZCI6ImFjY291bnQiLCJzdWIiOiI0MDI2ZWRlMy00YjA1LTRjMmUtOGNjZS1kNGQ0ZmFlMzYyYzAiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJtZXJjaGFudHByb3h5LWF1dGgiLCJzaWQiOiJmY2FkNTRlZS0zOWQ4LTQ3MWItOTFiMi1iNmFmNjgzZTM1ZDkiLCJhY3IiOiIxIiwiYWxsb3dlZC1vcmlnaW5zIjpbIioiXSwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIm9mZmxpbmVfYWNjZXNzIiwidW1hX2F1dGhvcml6YXRpb24iLCJkZWZhdWx0LXJvbGVzLXVzZXJzcGphdXRoIl19LCJyZXNvdXJjZV9hY2Nlc3MiOnsiYWNjb3VudCI6eyJyb2xlcyI6WyJtYW5hZ2UtYWNjb3VudCIsIm1hbmFnZS1hY2NvdW50LWxpbmtzIiwidmlldy1wcm9maWxlIl19fSwic2NvcGUiOiJwcm9maWxlIGVtYWlsIiwiZW1haWxfdmVyaWZpZWQiOmZhbHNlLCJ0ZXN0Ijp0cnVlLCJpcCI6IiouKi4qLioiLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiI5YjljMTlhZS1kYzMyLTQxMjgtOTI0OS0xNjQxMmNjZDdlNmIiLCJwcm9qZWN0SWQiOiI5QjlDMTlBRS1EQzMyLTQxMjgtOTI0OS0xNjQxMkNDRDdFNkIifQ.tU2cgbYAI0cJ4REZ60fOWCs20KZMc9aAPRok35xYRaVRqk-RvpBNJKys0B-4NzsO7m57M77OgXoeMVfnhR2iFpxH5eHD9qe-eIhEXDv8MQGbsXsMx1sDOAElWug47a46PQyBKY4h2Da6ysYSYhnrEdnMEU-y7nFy-jxLjircq2RZwyvFhUfaeuJNbh3s95NG94vGY30FiVS65CLqsNC195Re0f5qHh-HByrd8dC0CchaVFRKn9_76o5PkcEQblyNGGSHu7Gh-9cbiLrGKagfmDJAVhfZaZtmo43TTWcn-vgPTxZbZv8NMkDh8erwc_PV6kBKVKA_8wdEdSe133cs5w\";', 1770328444);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `created_at`, `updated_at`) VALUES
(6, 'Laptopuri', 'laptopuri', NULL, '2026-02-01 00:14:32', '2026-02-01 00:14:32'),
(7, 'Office', 'office', 6, '2026-02-01 00:14:44', '2026-02-01 00:14:44'),
(9, 'Gamming', 'gamming', 6, '2026-02-01 00:15:22', '2026-02-01 00:15:22'),
(10, 'MiniPC', 'minipc', NULL, '2026-02-01 02:43:24', '2026-02-01 02:43:24'),
(11, 'Calculatoare', 'calculatoare', NULL, '2026-02-01 02:43:51', '2026-02-01 02:43:51'),
(12, 'Gamming', 'gamming-1', 11, '2026-02-05 16:10:29', '2026-02-05 16:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `localities`
--

CREATE TABLE `localities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `district_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_29_213600_create_products_table', 1),
(5, '2026_01_30_071318_add_is_admin_to_users_table', 1),
(6, '2026_01_30_090439_add_role_to_users_table', 1),
(7, '2026_01_31_170234_add_image_to_products_table', 2),
(8, '2026_01_31_173237_create_banners_table', 3),
(9, '2026_01_31_175047_create_categories_table', 3),
(10, '2026_01_31_233956_add_subcategory_id_to_products_table', 4),
(11, '2026_02_01_004918_fix_missing_fields_in_products_table', 5),
(12, '2026_02_01_010515_create_categories_table', 6),
(13, '2026_02_01_011433_add_category_fields_to_products_table', 7),
(14, '2026_02_01_041931_add_fields_to_banners_table', 8),
(15, '2026_02_01_134251_create_orders_table', 9),
(16, '2026_02_01_134356_create_order_items_table', 10),
(17, '2026_02_01_135904_add_address_fields_to_orders_table', 11),
(18, '2026_02_01_152001_create_districts_table', 12),
(19, '2026_02_01_152037_create_localities_table', 12),
(20, '2026_02_03_071907_add_order_number_to_orders_table', 13),
(21, '2026_02_03_153817_add_maib_fields_to_orders_table', 14),
(22, '2026_02_04_200911_add_street_to_orders_table', 15),
(23, '2026_02_04_103431_add_payment_fields_to_orders_table', 16),
(24, '2026_02_04_202923_add_maib_tx_fields_to_orders_table', 16),
(25, '2026_02_04_224715_fix_payment_status_column_on_orders_table', 17),
(26, '2026_02_04_234055_fix_payment_status_defaults_on_orders_table', 18);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `locality` varchar(255) NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `customer_note` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) DEFAULT NULL,
  `payment_provider` varchar(255) DEFAULT NULL,
  `pay_id` varchar(191) DEFAULT NULL,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `payment_status` varchar(20) NOT NULL DEFAULT 'unpaid',
  `refund_status` varchar(30) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `paid_email_sent_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `maib_status` varchar(255) DEFAULT NULL,
  `maib_status_code` varchar(255) DEFAULT NULL,
  `maib_status_message` varchar(255) DEFAULT NULL,
  `maib_rrn` varchar(255) DEFAULT NULL,
  `maib_approval` varchar(255) DEFAULT NULL,
  `maib_card` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `first_name`, `last_name`, `phone`, `district`, `locality`, `street`, `postal_code`, `customer_name`, `customer_phone`, `customer_address`, `customer_note`, `subtotal`, `status`, `payment_provider`, `pay_id`, `payment_details`, `payment_status`, `refund_status`, `refunded_at`, `paid_at`, `paid_email_sent_at`, `created_at`, `updated_at`, `user_id`, `maib_status`, `maib_status_code`, `maib_status_message`, `maib_rrn`, `maib_approval`, `maib_card`) VALUES
(58, 100058, 'Tulea', 'Artur', '+37360546000', 'Chişinău', 'Sectorul Botanica', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chişinău, Sectorul Botanica, Stradela 2 Lvov 83, MD-2062', NULL, 12000.00, 'canceled', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 20:36:05', '2026-02-05 16:11:09', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 100059, 'Tulea', 'Artur', '+37360546000', 'Glodeni', 'Danu', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Glodeni, Danu, Stradela 2 Lvov 83, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 20:45:58', '2026-02-04 20:45:58', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 100060, 'Tulea', 'Artur', '+37360546000', 'Criuleni', 'Dubăsarii Vechi', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Criuleni, Dubăsarii Vechi, Stradela 2 Lvov 83, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 20:51:00', '2026-02-04 20:51:00', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 100061, 'Tulea', 'Artur', '+37360546000', 'Făleşti', 'Doltu', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Făleşti, Doltu, Stradela 2 Lvov 83, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 21:08:41', '2026-02-04 21:20:03', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 100062, 'Tulea', 'Artur', '+37360546000', 'Glodeni', 'Iabloana', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Glodeni, Iabloana, Stradela 2, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 21:09:38', '2026-02-04 21:09:38', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 100063, 'Tulea', 'Artur', '+37360546000', 'Edineţ', 'Constantinovca', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Edineţ, Constantinovca, Stradela 2, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 21:16:10', '2026-02-04 21:16:10', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 100064, 'Tulea', 'Artur', '+37360546000', 'Dubăsari', 'Roghi', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Dubăsari, Roghi, Stradela 2 Lvov 83, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'unpaid', NULL, NULL, NULL, NULL, '2026-02-04 21:20:37', '2026-02-04 21:20:37', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 100065, 'Tulea', 'Artur', '+37360546000', 'Floreşti', 'Cunicea', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Floreşti, Cunicea, Stradela 2 Lvov 83, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 21:43:34', '2026-02-04 21:43:35', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 100066, 'Tulea', 'Artur', '+37360546000', 'Făleşti', 'Doltu', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Făleşti, Doltu, Stradela 2 Lvov 83, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 22:06:05', '2026-02-04 22:06:06', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 100067, 'Tulea', 'Artur', '+37360546000', 'Hînceşti', 'Cotul Morii', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Hînceşti, Cotul Morii, Stradela 2 Lvov 83, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 22:06:28', '2026-02-04 22:06:28', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 100068, 'Tulea', 'Artur', '+37360546000', 'Bălţi', 'Elizaveta', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Bălţi, Elizaveta, Stradela 2 Lvov 83, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 22:08:44', '2026-02-04 22:08:45', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 100069, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Botanica', 'str 2 Lvov 83', '2060', 'Artur Tulea', '60546000', 'Chisinau, Botanica, str 2 Lvov 83, 2060', NULL, 6500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 22:36:25', '2026-02-04 22:36:26', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 100070, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Botanica', 'str 2 Lvov 83', '2060', 'Artur Tulea', '60546000', 'Chisinau, Botanica, str 2 Lvov 83, 2060', NULL, 6500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 22:36:29', '2026-02-04 22:36:29', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 100071, 'aaaaa', 'aaa', '789878978', 'Chisinau', 'Botanica', 'sdasdasd', '2070', 'aaaaa aaa', '789878978', 'Chisinau, Botanica, sdasdasd, 2070', NULL, 6500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 22:43:24', '2026-02-04 22:43:24', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 100072, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Chisisnau', 'sjdjkasjd', 'jwjkqehjkqw', 'Artur Tulea', '60546000', 'Chisinau, Chisisnau, sjdjkasjd, jwjkqehjkqw', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:14:41', '2026-02-04 23:14:41', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 100073, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Chisisnau', 'sjdjkasjd', 'jwjkqehjkqw', 'Artur Tulea', '60546000', 'Chisinau, Chisisnau, sjdjkasjd, jwjkqehjkqw', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:17:37', '2026-02-04 23:17:37', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 100074, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Chisisnau', 'sjdjkasjd', 'jwjkqehjkqw', 'Artur Tulea', '60546000', 'Chisinau, Chisisnau, sjdjkasjd, jwjkqehjkqw', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:18:55', '2026-02-04 23:18:55', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 100075, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Chisinau', 'sjdjkasjd', 'jwjkqehjkqw', 'Artur Tulea', '60546000', 'Chisinau, Chisinau, sjdjkasjd, jwjkqehjkqw', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:19:20', '2026-02-04 23:19:20', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 100076, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Chisinau', 'sjdjkasjd', 'jwjkqehjkqw', 'Artur Tulea', '60546000', 'Chisinau, Chisinau, sjdjkasjd, jwjkqehjkqw', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:24:55', '2026-02-04 23:24:55', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 100077, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Chisinau', 'sjdjkasjd', 'jwjkqehjkqw', 'Artur Tulea', '60546000', 'Chisinau, Chisinau, sjdjkasjd, jwjkqehjkqw', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:28:09', '2026-02-04 23:28:09', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 100078, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Chisinau', 'sjdjkasjd', 'jwjkqehjkqw', 'Artur Tulea', '60546000', 'Chisinau, Chisinau, sjdjkasjd, jwjkqehjkqw', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:28:56', '2026-02-04 23:28:56', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 100079, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Chisinau', 'sjdjkasjd', 'jwjkqehjkqw', 'Artur Tulea', '60546000', 'Chisinau, Chisinau, sjdjkasjd, jwjkqehjkqw', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:29:58', '2026-02-04 23:29:58', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 100080, 'Artur', 'Tulea', '60546000', 'Chisinau', 'Chisinau', 'sjdjkasjd', 'jwjkqehjkqw', 'Artur Tulea', '60546000', 'Chisinau, Chisinau, sjdjkasjd, jwjkqehjkqw', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:30:48', '2026-02-04 23:30:48', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 100081, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:34:49', '2026-02-04 23:34:49', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 100082, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 18500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-04 23:35:04', '2026-02-04 23:35:04', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 100083, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 18500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-04 23:40:41', '2026-02-04 23:40:43', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 100084, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 18500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:05:04', '2026-02-05 00:05:05', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 100085, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Botanica', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Botanica, Stradela 2, MD-2062', NULL, 30500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:15:45', '2026-02-05 00:15:46', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 100086, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 30500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-05 00:19:48', '2026-02-05 00:19:48', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 100087, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 30500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-05 00:20:15', '2026-02-05 00:20:15', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 100088, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 30500.00, 'new', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-05 00:20:23', '2026-02-05 00:20:23', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 100089, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 30500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:20:35', '2026-02-05 00:20:36', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 100090, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Botanica', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Botanica, Stradela 2 Lvov 83, MD-2062', NULL, 30500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:28:02', '2026-02-05 00:28:05', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 100091, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:31:54', '2026-02-05 00:31:55', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 100092, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:33:58', '2026-02-05 00:33:59', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 100093, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:38:22', '2026-02-05 00:38:23', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 100094, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:43:18', '2026-02-05 00:43:19', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 100095, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:45:15', '2026-02-05 00:45:16', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 100096, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:49:10', '2026-02-05 00:49:11', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 100097, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:50:03', '2026-02-05 00:50:03', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 100098, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 12000.00, 'new', NULL, NULL, NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-05 00:55:05', '2026-02-05 00:55:05', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 100099, 'Tulea', 'Artur', '+37360546000', 'Chisinau', 'Chisinau', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chisinau, Chisinau, Stradela 2, MD-2062', NULL, 12000.00, 'canceled', NULL, NULL, NULL, 'paid', NULL, NULL, NULL, NULL, '2026-02-05 00:58:46', '2026-02-05 16:10:57', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 100107, 'Tulea', 'Artur', '+37360546000', 'Glodeni', 'Iabloana', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Glodeni, Iabloana, Stradela 2, MD-2062', NULL, 12000.00, 'confirmed', NULL, 'a98185da-939a-4104-a7af-2af97ebc4b31', NULL, 'paid', NULL, NULL, NULL, NULL, '2026-02-05 17:36:15', '2026-02-06 16:12:44', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 100108, 'Tulea', 'Artur', '+37360546000', 'Floreşti', 'Cerniţa', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Floreşti, Cerniţa, Stradela 2, MD-2062', NULL, 13000.00, 'canceled', NULL, '0a534894-ff02-43c7-9160-806d9cc0dcfa', NULL, 'paid', NULL, NULL, NULL, NULL, '2026-02-05 21:50:04', '2026-02-06 16:01:38', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 100109, 'Tulea', 'Artur', '+37360546000', 'Chişinău', 'Sectorul Botanica', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chişinău, Sectorul Botanica, Stradela 2, MD-2062', NULL, 24000.00, 'new', NULL, '8d3886de-f494-40df-b94c-ccde8aa643c4', '{\"result\":{\"payId\":\"8d3886de-f494-40df-b94c-ccde8aa643c4\",\"orderId\":\"100109-1770396355\",\"currency\":\"MDL\",\"statusCode\":\"400\",\"status\":\"OK\",\"statusMessage\":\"Accepted (for reversal)\",\"clientName\":\"Tulea Artur\",\"rrn\":\"603716100722\",\"approval\":\"554693\",\"amount\":24000,\"refundAmount\":24000,\"description\":\"Order #100109\",\"clientIp\":\"92.115.157.129\",\"email\":\"tuleaartur@gmail.com\",\"phone\":\"+37360546000\",\"items\":[{\"id\":\"8\",\"name\":\"Asrock\",\"price\":12000,\"quantity\":2,\"total\":24000}],\"cardNumber\":\"510218******1124\"},\"ok\":true}', 'refunded', 'refunded', '2026-02-09 15:37:38', NULL, NULL, '2026-02-06 16:45:55', '2026-02-09 15:37:38', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 100110, 'Artur', 'Tulea', '060546000', 'Chişinău', 'Sectorul Botanica', 'Str-la 2 Lvov 83', 'MD-2060', 'Artur Tulea', '060546000', 'Chişinău, Sectorul Botanica, Str-la 2 Lvov 83, MD-2060', NULL, 24000.00, 'new', NULL, 'fa1be9f4-ab62-4b18-9b11-bf6a6064790f', NULL, 'refunded', NULL, NULL, NULL, NULL, '2026-02-06 17:10:40', '2026-02-09 11:16:19', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 100111, 'Tulea', 'Artur', '+37360546000', 'Străşeni', 'Cojuşna', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Străşeni, Cojuşna, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '0fcd6bfd-110f-46f9-9ff9-56850f7ec97d', NULL, 'failed', NULL, NULL, NULL, NULL, '2026-02-09 12:52:32', '2026-02-09 13:07:10', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 100112, 'Tulea', 'Artur', '+37360546000', 'Chişinău', 'Sectorul Botanica', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Chişinău, Sectorul Botanica, Stradela 2 Lvov 83, MD-2062', NULL, 13000.00, 'new', NULL, '0e92bb74-dbbb-48ef-acaa-4dace74d2be1', '{\"result\":{\"payId\":\"0e92bb74-dbbb-48ef-acaa-4dace74d2be1\",\"orderId\":\"100112-1770651579\",\"currency\":\"MDL\",\"statusCode\":\"400\",\"status\":\"OK\",\"statusMessage\":\"Accepted (for reversal)\",\"clientName\":\"Tulea Artur\",\"rrn\":\"604015104964\",\"approval\":\"225268\",\"amount\":13000,\"refundAmount\":13000,\"description\":\"Order #100112\",\"clientIp\":\"92.115.157.129\",\"email\":\"tuleaartur@gmail.com\",\"phone\":\"+37360546000\",\"items\":[{\"id\":\"7\",\"name\":\"Firebat AT15\",\"price\":6500,\"quantity\":2,\"total\":13000}],\"cardNumber\":\"510218******1124\"},\"ok\":true}', 'refunded', 'refunded', '2026-02-09 15:55:07', NULL, NULL, '2026-02-09 15:39:39', '2026-02-09 15:55:07', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 100113, 'Tulea', 'Artur', '+37360546000', 'Făleşti', 'Călineşti', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Făleşti, Călineşti, Stradela 2 Lvov 83, MD-2062', NULL, 13000.00, 'new', NULL, 'f4a39cc9-4c7b-4adc-8664-b701576b68fd', '{\"result\":{\"payId\":\"f4a39cc9-4c7b-4adc-8664-b701576b68fd\",\"orderId\":\"100113-1770653233\",\"currency\":\"MDL\",\"statusCode\":\"400\",\"status\":\"OK\",\"statusMessage\":\"Accepted (for reversal)\",\"clientName\":\"Tulea Artur\",\"rrn\":\"604016105124\",\"approval\":\"164129\",\"amount\":13000,\"refundAmount\":13000,\"description\":\"Order #100113\",\"clientIp\":\"92.115.157.129\",\"email\":\"tuleaartur@gmail.com\",\"phone\":\"+37360546000\",\"items\":[{\"id\":\"7\",\"name\":\"Firebat AT15\",\"price\":6500,\"quantity\":2,\"total\":13000}],\"cardNumber\":\"510218******1124\"},\"ok\":true}', 'refunded', 'refunded', '2026-02-10 07:15:47', '2026-02-09 16:07:41', NULL, '2026-02-09 16:07:13', '2026-02-10 07:15:47', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 100118, 'Tulea', 'Artur', '+37360546000', 'Dubăsari', 'Pohrebea', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Dubăsari, Pohrebea, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '189d6d36-8430-47df-9192-6302d8066c8c', '{\"result\":{\"payId\":\"189d6d36-8430-47df-9192-6302d8066c8c\",\"orderId\":\"100118-1770709668\",\"status\":\"FAILED\",\"currency\":\"MDL\",\"amount\":6500,\"cardNumber\":\"510218******1124\"},\"signature\":\"9YsGPY28w\\/bGf\\/7k25c24tvldSdCBv3V\\/g6aLJZqZBs=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-10 07:47:48', '2026-02-10 07:49:33', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 100119, 'Tulea', 'Artur', '+37360546000', 'Glodeni', 'Fundurii Noi', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Glodeni, Fundurii Noi, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '284ccb15-3dfe-40c5-b9d4-9396e07e5d82', '{\"result\":{\"payId\":\"284ccb15-3dfe-40c5-b9d4-9396e07e5d82\",\"orderId\":\"100119-1770709825\",\"status\":\"FAILED\",\"currency\":\"MDL\",\"amount\":6500,\"cardNumber\":\"510218******1124\"},\"signature\":\"Mo70rkgdWyY77cm2ELf+AsACwI6xACSC1YMG9BL4PoY=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-10 07:50:25', '2026-02-10 07:51:19', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 100120, 'Tulea', 'Artur', '+37360546000', 'Cahul', 'Chioselia Mare', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Cahul, Chioselia Mare, Stradela 2 Lvov 83, MD-2062', NULL, 18500.00, 'new', NULL, 'aae05188-b44f-43d5-8387-959239214a90', '{\"result\":{\"payId\":\"aae05188-b44f-43d5-8387-959239214a90\",\"orderId\":\"100120-1770709987\",\"status\":\"FAILED\",\"currency\":\"MDL\",\"amount\":18500,\"cardNumber\":\"510218******1124\"},\"signature\":\"rtec3FpTQNgE0Kz0pTeACsrhFlqP8Ua\\/TL82\\/Bf4ELI=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-10 07:53:07', '2026-02-10 07:54:23', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 100121, 'Tulea', 'Artur', '+37360546000', 'Edineţ', 'Cuconeştii Noi', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Edineţ, Cuconeştii Noi, Stradela 2, MD-2062', NULL, 6500.00, 'confirmed', NULL, '391fe796-fbd3-4925-a78e-c87386dd74a8', '{\"result\":{\"payId\":\"391fe796-fbd3-4925-a78e-c87386dd74a8\",\"orderId\":\"100121-1770719342\",\"currency\":\"MDL\",\"statusCode\":\"400\",\"status\":\"OK\",\"statusMessage\":\"Accepted (for reversal)\",\"clientName\":\"Tulea Artur\",\"rrn\":\"604110106734\",\"approval\":\"958224\",\"amount\":6500,\"refundAmount\":6500,\"description\":\"Order #100121\",\"clientIp\":\"37.233.13.45\",\"email\":\"admin@site.md\",\"phone\":\"+37360546000\",\"items\":[{\"id\":\"9\",\"name\":\"Laptop Firebat AT15 16RAM 512SSD\",\"price\":6500,\"quantity\":1,\"total\":6500}],\"cardNumber\":\"510218******1124\"},\"ok\":true}', 'refunded', 'refunded', '2026-02-10 10:30:49', '2026-02-10 10:29:54', NULL, '2026-02-10 10:29:02', '2026-02-10 10:30:49', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 100122, 'Tulea', 'Artur', '+37360546000', 'Glodeni', 'Brînzeni', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Glodeni, Brînzeni, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, 'b344178a-dbea-4da9-b5cc-150b7edb8e57', '{\"result\":{\"payId\":\"b344178a-dbea-4da9-b5cc-150b7edb8e57\",\"orderId\":\"100122-1770796772\",\"status\":\"TIMEOUT\",\"currency\":\"MDL\",\"amount\":6500},\"signature\":\"h8xXOcNhh00COZBXlvvRw+RopsPHcRinVM9AZ84Sf+U=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-11 07:59:32', '2026-02-11 08:10:05', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 100123, 'Tulea', 'Artur', '+37360546000', 'Drochia', 'Baroncea', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Drochia, Baroncea, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, 'b8409d43-2f87-4f72-905e-ae0076e56c6b', '{\"result\":{\"payId\":\"b8409d43-2f87-4f72-905e-ae0076e56c6b\",\"orderId\":\"100123-1770796835\",\"status\":\"TIMEOUT\",\"currency\":\"MDL\",\"amount\":6500},\"signature\":\"LI7GHiOmsLdnjs\\/cmCg7ntgsChb7F2LZtTVLoCcq3Ts=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-11 08:00:35', '2026-02-11 08:15:09', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 100124, 'Tulea', 'Artur', '+37360546000', 'Făleşti', 'Egorovca', 'Stradela 2 Lvov 83', 'MD-2062', 'Tulea Artur', '+37360546000', 'Făleşti, Egorovca, Stradela 2 Lvov 83, MD-2062', NULL, 6500.00, 'new', NULL, 'ba7c346d-9a35-42ae-a95a-a4be5d72d8ad', '{\"result\":{\"payId\":\"ba7c346d-9a35-42ae-a95a-a4be5d72d8ad\",\"orderId\":\"100124-1770796938\",\"status\":\"TIMEOUT\",\"currency\":\"MDL\",\"amount\":6500},\"signature\":\"SO76LpxryBiA09dtV+6ZXXKrOtNMK\\/V6EDX\\/ubiphQ0=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-11 08:02:18', '2026-02-11 08:15:08', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 100125, 'Artur', 'Tulea', '060546000', 'Chişinău', 'Ciorescu', 'Str-la 2 Lvov 83', '2060', 'Artur Tulea', '060546000', 'Chişinău, Ciorescu, Str-la 2 Lvov 83, 2060', NULL, 6500.00, 'new', NULL, 'c5b472e3-b977-45d7-9d27-748d68fb3e3a', '{\"result\":{\"payId\":\"c5b472e3-b977-45d7-9d27-748d68fb3e3a\",\"orderId\":\"100125-1770797047\",\"status\":\"TIMEOUT\",\"currency\":\"MDL\",\"amount\":6500},\"signature\":\"yu+\\/iri1vIFjSWAZw4VIPF521z7UMNNqDS5khKD\\/UwQ=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-11 08:04:07', '2026-02-11 08:15:09', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 100126, 'Tulea', 'Artur', '+37360546000', 'Floreşti', 'Cuhureştii de Jos', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Floreşti, Cuhureştii de Jos, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '455ebb8d-fc27-4348-8f3c-e0b16394b324', '{\"result\":{\"payId\":\"455ebb8d-fc27-4348-8f3c-e0b16394b324\",\"orderId\":\"100126-1770797097\",\"status\":\"TIMEOUT\",\"currency\":\"MDL\",\"amount\":6500},\"signature\":\"kBjLRrfMjT0I87Mu6aXrqgjQH3cdwXXnJ0LFQr1MAaE=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-11 08:04:57', '2026-02-11 08:15:09', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 100127, 'Tulea', 'Artur', '+37360546000', 'Basarabeasca', 'Abaclia', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Basarabeasca, Abaclia, Stradela 2, MD-2062', NULL, 12000.00, 'new', NULL, '3d1286b1-bc3a-4228-81d3-ee07a2ec9923', '{\"result\":{\"payId\":\"3d1286b1-bc3a-4228-81d3-ee07a2ec9923\",\"orderId\":\"100127-1770799340\",\"rrn\":\"604208109567\",\"status\":\"OK\",\"statusCode\":\"000\",\"statusMessage\":\"Approved\",\"approval\":\"149956\",\"currency\":\"MDL\",\"amount\":12000,\"cardNumber\":\"510218******1124\"},\"signature\":\"xqn5IWRpHKG8picaOjwAAtqtoug1ELnBjYJU\\/X7K2e4=\"}', 'paid', NULL, NULL, '2026-02-11 08:44:28', NULL, '2026-02-11 08:42:20', '2026-02-11 08:44:28', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 100128, 'Tulea', 'Artur', '+37360546000', 'Făleşti', 'Doltu', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Făleşti, Doltu, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '56420dcd-5210-4541-a151-f7cab6669806', '{\"result\":{\"payId\":\"56420dcd-5210-4541-a151-f7cab6669806\",\"orderId\":\"100128-1770799855\",\"status\":\"TIMEOUT\",\"currency\":\"MDL\",\"amount\":6500},\"signature\":\"oIjkVSuTEwiWNyQjL83pNAtf7et\\/hgqUGJcXSlSfFHY=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-11 08:50:55', '2026-02-11 09:05:47', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 100129, 'Tulea', 'Artur', '+37360546000', 'Călăraşi', 'Hîrjauca', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Călăraşi, Hîrjauca, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, 'eb2ea6f2-9ecf-4b0b-80b5-a28ccc0ad7bf', '{\"result\":{\"payId\":\"eb2ea6f2-9ecf-4b0b-80b5-a28ccc0ad7bf\",\"orderId\":\"100129-1770824047\",\"status\":\"TIMEOUT\",\"currency\":\"MDL\",\"amount\":6500},\"signature\":\"6tTUCCis+3xOc7i1FU3NfnuXiYJdb1p1TWB9OF1qi3Y=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-11 15:34:07', '2026-02-11 15:46:21', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 100130, 'Tulea', 'Artur', '+37360546000', 'Cimişlia', 'Bogdanovca Veche', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Cimişlia, Bogdanovca Veche, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '1e388a8c-663c-4a35-b1cf-b85220ab59b1', '{\"result\":{\"payId\":\"1e388a8c-663c-4a35-b1cf-b85220ab59b1\",\"orderId\":\"100130-1770825219\",\"status\":\"TIMEOUT\",\"currency\":\"MDL\",\"amount\":6500},\"signature\":\"RTth5ZnnCBAEU9GNl\\/n9JbfFsUSGdyAq98QMX9gluSo=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-11 15:53:39', '2026-02-11 16:06:39', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 100131, 'Tulea', 'Artur', '+37360546000', 'Floreşti', 'Cerniţa', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Floreşti, Cerniţa, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, 'd4ee19da-e487-40f9-8f77-d32fe89bbf3a', '{\"result\":{\"payId\":\"d4ee19da-e487-40f9-8f77-d32fe89bbf3a\",\"orderId\":\"100131-1770998946\",\"status\":\"TIMEOUT\",\"currency\":\"MDL\",\"amount\":6500},\"signature\":\"SMz9z5lvzsf2lQNc\\/PWXn7mMd6zm+KnmcUqLrz8VDa8=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-13 16:09:06', '2026-02-13 16:22:14', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 100132, 'Tulea', 'Artur', '+37360546000', 'Făleşti', 'Ciuluc', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Făleşti, Ciuluc, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '8ad2e78b-7cca-4240-9699-4d95e9d0e3b3', '{\"result\":{\"payId\":\"8ad2e78b-7cca-4240-9699-4d95e9d0e3b3\",\"orderId\":\"100132-1771000164\",\"rrn\":\"604416118411\",\"status\":\"OK\",\"statusCode\":\"000\",\"statusMessage\":\"Approved\",\"approval\":\"582497\",\"currency\":\"MDL\",\"amount\":6500,\"cardNumber\":\"510218******1124\"},\"signature\":\"uODcgc5dwobZtsqdX+\\/wdnprHxH8hDtWMt7gWl1dD5I=\"}', 'paid', NULL, NULL, '2026-02-13 16:29:53', NULL, '2026-02-13 16:29:24', '2026-02-13 16:29:58', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(133, 100133, 'Tulea', 'Artur', '+37360546000', 'Criuleni', 'Chetroasa', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Criuleni, Chetroasa, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '3391c7a4-6a68-4f95-9f58-d86f7ff2a73d', '{\"result\":{\"payId\":\"3391c7a4-6a68-4f95-9f58-d86f7ff2a73d\",\"orderId\":\"100133-1771000813\",\"rrn\":\"604416118428\",\"status\":\"OK\",\"statusCode\":\"000\",\"statusMessage\":\"Approved\",\"approval\":\"289915\",\"currency\":\"MDL\",\"amount\":6500,\"cardNumber\":\"510218******1124\"},\"signature\":\"fgiLxqNx991JKnmhe6hyHuk1R3nxYRZ1lOlp\\/Zk\\/50M=\"}', 'paid', NULL, NULL, '2026-02-13 16:40:43', NULL, '2026-02-13 16:40:13', '2026-02-13 16:40:46', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(134, 100134, 'Tulea', 'Artur', '+37360546000', 'Edineţ', 'Chiurt', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Edineţ, Chiurt, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, 'a6de9598-07e1-4fa4-bbba-0ccef15f1904', '{\"result\":{\"payId\":\"a6de9598-07e1-4fa4-bbba-0ccef15f1904\",\"orderId\":\"100134-1771001256\",\"rrn\":\"604416118429\",\"status\":\"FAILED\",\"statusCode\":\"129\",\"statusMessage\":\"Decline, suspected counterfeit card\",\"approval\":\"201820\",\"currency\":\"MDL\",\"amount\":6500,\"cardNumber\":\"510218******1124\"},\"signature\":\"pj6E+fPsJEqwornRspo7MXRRe+N7QqAKDuVBv2R8Hug=\"}', 'failed', NULL, NULL, NULL, NULL, '2026-02-13 16:47:36', '2026-02-13 16:48:05', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(135, 100135, 'Tulea', 'Artur', '+37360546000', 'Făleşti', 'Ciolacu Vechi', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Făleşti, Ciolacu Vechi, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '5f2d660e-dc79-4b5c-99f3-e1aaeb543d6f', '{\"result\":{\"payId\":\"5f2d660e-dc79-4b5c-99f3-e1aaeb543d6f\",\"orderId\":\"100135-1771001325\",\"rrn\":\"604416118430\",\"status\":\"OK\",\"statusCode\":\"000\",\"statusMessage\":\"Approved\",\"approval\":\"719363\",\"currency\":\"MDL\",\"amount\":6500,\"cardNumber\":\"510218******1124\"},\"signature\":\"2nGq5Pszbx4RejaQUvFDiSgZ3BMxTNO\\/dRCZAIk7Y\\/8=\"}', 'paid', NULL, NULL, '2026-02-13 16:49:07', NULL, '2026-02-13 16:48:45', '2026-02-13 16:49:08', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 100136, 'Tulea', 'Artur', '+37360546000', 'Floreşti', 'Cuhureştii de Jos', 'Stradela 2', 'MD-2062', 'Tulea Artur', '+37360546000', 'Floreşti, Cuhureştii de Jos, Stradela 2, MD-2062', NULL, 6500.00, 'new', NULL, '4947e730-89d0-43a3-8d4a-f26ae77d7cbc', '{\"result\":{\"payId\":\"4947e730-89d0-43a3-8d4a-f26ae77d7cbc\",\"orderId\":\"100136-1771001977\",\"rrn\":\"604417118432\",\"status\":\"OK\",\"statusCode\":\"000\",\"statusMessage\":\"Approved\",\"approval\":\"287174\",\"currency\":\"MDL\",\"amount\":6500,\"cardNumber\":\"510218******1124\"},\"signature\":\"9\\/wXy5\\/cQioUw3eTTJeT6n8GoM+V\\/HjKfGTiXeBQTps=\"}', 'paid', NULL, NULL, '2026-02-13 17:00:06', NULL, '2026-02-13 16:59:37', '2026-02-13 17:00:14', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 100137, 'Tulea', 'Artur', '060546000', 'Chişinău', 'Sectorul Botanica', 'Stradela 2 Lvov 83', '2060', 'Tulea Artur', '060546000', 'Chişinău, Sectorul Botanica, Stradela 2 Lvov 83, 2060', NULL, 12000.00, 'new', NULL, '7305030d-18aa-4c87-ba1c-98af95f63802', '{\"result\":{\"payId\":\"7305030d-18aa-4c87-ba1c-98af95f63802\",\"orderId\":\"100137-1771012866\",\"rrn\":\"604420118498\",\"status\":\"OK\",\"statusCode\":\"000\",\"statusMessage\":\"Approved\",\"approval\":\"291646\",\"currency\":\"MDL\",\"amount\":12000,\"cardNumber\":\"510218******1124\"},\"signature\":\"SQfe3lQ9dQs0UdjVNO0CL0FOSUmsqcAgR9EfMo5OumM=\"}', 'paid', NULL, NULL, '2026-02-13 20:01:40', NULL, '2026-02-13 20:01:06', '2026-02-13 20:01:46', 2, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `qty`, `created_at`, `updated_at`) VALUES
(57, 58, 6, 'Asrock', 12000.00, 1, '2026-02-04 20:36:05', '2026-02-04 20:36:05'),
(58, 59, 6, 'Asrock', 12000.00, 1, '2026-02-04 20:45:58', '2026-02-04 20:45:58'),
(59, 60, 6, 'Asrock', 12000.00, 1, '2026-02-04 20:51:00', '2026-02-04 20:51:00'),
(60, 61, 6, 'Asrock', 12000.00, 1, '2026-02-04 21:08:41', '2026-02-04 21:08:41'),
(61, 62, 6, 'Asrock', 12000.00, 1, '2026-02-04 21:09:38', '2026-02-04 21:09:38'),
(62, 63, 6, 'Asrock', 12000.00, 1, '2026-02-04 21:16:10', '2026-02-04 21:16:10'),
(63, 64, 6, 'Asrock', 12000.00, 1, '2026-02-04 21:20:37', '2026-02-04 21:20:37'),
(64, 65, 6, 'Asrock', 12000.00, 1, '2026-02-04 21:43:34', '2026-02-04 21:43:34'),
(65, 66, 6, 'Asrock', 12000.00, 1, '2026-02-04 22:06:05', '2026-02-04 22:06:05'),
(66, 67, 6, 'Asrock', 12000.00, 1, '2026-02-04 22:06:28', '2026-02-04 22:06:28'),
(67, 68, 6, 'Asrock', 12000.00, 1, '2026-02-04 22:08:44', '2026-02-04 22:08:44'),
(68, 69, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 22:36:25', '2026-02-04 22:36:25'),
(69, 70, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 22:36:29', '2026-02-04 22:36:29'),
(70, 71, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 22:43:24', '2026-02-04 22:43:24'),
(71, 72, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:14:41', '2026-02-04 23:14:41'),
(72, 72, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:14:41', '2026-02-04 23:14:41'),
(73, 73, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:17:37', '2026-02-04 23:17:37'),
(74, 73, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:17:37', '2026-02-04 23:17:37'),
(75, 74, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:18:55', '2026-02-04 23:18:55'),
(76, 74, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:18:55', '2026-02-04 23:18:55'),
(77, 75, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:19:20', '2026-02-04 23:19:20'),
(78, 75, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:19:20', '2026-02-04 23:19:20'),
(79, 76, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:24:55', '2026-02-04 23:24:55'),
(80, 76, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:24:55', '2026-02-04 23:24:55'),
(81, 77, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:28:09', '2026-02-04 23:28:09'),
(82, 77, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:28:09', '2026-02-04 23:28:09'),
(83, 78, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:28:56', '2026-02-04 23:28:56'),
(84, 78, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:28:56', '2026-02-04 23:28:56'),
(85, 79, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:29:58', '2026-02-04 23:29:58'),
(86, 79, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:29:58', '2026-02-04 23:29:58'),
(87, 80, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:30:48', '2026-02-04 23:30:48'),
(88, 80, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:30:48', '2026-02-04 23:30:48'),
(89, 81, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:34:49', '2026-02-04 23:34:49'),
(90, 81, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:34:49', '2026-02-04 23:34:49'),
(91, 82, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:35:04', '2026-02-04 23:35:04'),
(92, 82, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:35:04', '2026-02-04 23:35:04'),
(93, 83, 7, 'Firebat AT15', 6500.00, 1, '2026-02-04 23:40:41', '2026-02-04 23:40:41'),
(94, 83, 6, 'Asrock', 12000.00, 1, '2026-02-04 23:40:41', '2026-02-04 23:40:41'),
(95, 84, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:05:04', '2026-02-05 00:05:04'),
(96, 84, 6, 'Asrock', 12000.00, 1, '2026-02-05 00:05:04', '2026-02-05 00:05:04'),
(97, 85, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:15:45', '2026-02-05 00:15:45'),
(98, 85, 6, 'Asrock', 12000.00, 2, '2026-02-05 00:15:45', '2026-02-05 00:15:45'),
(99, 86, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:19:48', '2026-02-05 00:19:48'),
(100, 86, 6, 'Asrock', 12000.00, 2, '2026-02-05 00:19:48', '2026-02-05 00:19:48'),
(101, 87, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:20:15', '2026-02-05 00:20:15'),
(102, 87, 6, 'Asrock', 12000.00, 2, '2026-02-05 00:20:15', '2026-02-05 00:20:15'),
(103, 88, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:20:23', '2026-02-05 00:20:23'),
(104, 88, 6, 'Asrock', 12000.00, 2, '2026-02-05 00:20:23', '2026-02-05 00:20:23'),
(105, 89, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:20:35', '2026-02-05 00:20:35'),
(106, 89, 6, 'Asrock', 12000.00, 2, '2026-02-05 00:20:35', '2026-02-05 00:20:35'),
(107, 90, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:28:02', '2026-02-05 00:28:02'),
(108, 90, 6, 'Asrock', 12000.00, 2, '2026-02-05 00:28:02', '2026-02-05 00:28:02'),
(109, 91, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:31:54', '2026-02-05 00:31:54'),
(110, 92, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:33:58', '2026-02-05 00:33:58'),
(111, 93, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:38:22', '2026-02-05 00:38:22'),
(112, 94, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:43:18', '2026-02-05 00:43:18'),
(113, 95, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:45:15', '2026-02-05 00:45:15'),
(114, 96, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 00:49:10', '2026-02-05 00:49:10'),
(115, 97, 6, 'Asrock', 12000.00, 1, '2026-02-05 00:50:03', '2026-02-05 00:50:03'),
(116, 98, 6, 'Asrock', 12000.00, 1, '2026-02-05 00:55:05', '2026-02-05 00:55:05'),
(117, 99, 6, 'Asrock', 12000.00, 1, '2026-02-05 00:58:46', '2026-02-05 00:58:46'),
(125, 107, 6, 'Asrock', 12000.00, 1, '2026-02-05 17:36:16', '2026-02-05 17:36:16'),
(126, 108, 9, 'Laptop Firebat AT15 16RAM 512SSD', 6500.00, 1, '2026-02-05 21:50:04', '2026-02-05 21:50:04'),
(127, 108, 7, 'Firebat AT15', 6500.00, 1, '2026-02-05 21:50:04', '2026-02-05 21:50:04'),
(128, 109, 8, 'Asrock', 12000.00, 2, '2026-02-06 16:45:55', '2026-02-06 16:45:55'),
(129, 110, 8, 'Asrock', 12000.00, 2, '2026-02-06 17:10:40', '2026-02-06 17:10:40'),
(130, 111, 7, 'Firebat AT15', 6500.00, 1, '2026-02-09 12:52:32', '2026-02-09 12:52:32'),
(131, 112, 7, 'Firebat AT15', 6500.00, 2, '2026-02-09 15:39:39', '2026-02-09 15:39:39'),
(132, 113, 7, 'Firebat AT15', 6500.00, 2, '2026-02-09 16:07:13', '2026-02-09 16:07:13'),
(137, 118, 7, 'Firebat AT15', 6500.00, 1, '2026-02-10 07:47:48', '2026-02-10 07:47:48'),
(138, 119, 7, 'Firebat AT15', 6500.00, 1, '2026-02-10 07:50:25', '2026-02-10 07:50:25'),
(139, 120, 8, 'Asrock', 12000.00, 1, '2026-02-10 07:53:07', '2026-02-10 07:53:07'),
(140, 120, 7, 'Firebat AT15', 6500.00, 1, '2026-02-10 07:53:07', '2026-02-10 07:53:07'),
(141, 121, 9, 'Laptop Firebat AT15 16RAM 512SSD', 6500.00, 1, '2026-02-10 10:29:02', '2026-02-10 10:29:02'),
(142, 122, 9, 'Laptop Firebat AT15 16RAM 512SSD', 6500.00, 1, '2026-02-11 07:59:32', '2026-02-11 07:59:32'),
(143, 123, 7, 'Firebat AT15', 6500.00, 1, '2026-02-11 08:00:35', '2026-02-11 08:00:35'),
(144, 124, 7, 'Firebat AT15', 6500.00, 1, '2026-02-11 08:02:18', '2026-02-11 08:02:18'),
(145, 125, 7, 'Firebat AT15', 6500.00, 1, '2026-02-11 08:04:07', '2026-02-11 08:04:07'),
(146, 126, 7, 'Firebat AT15', 6500.00, 1, '2026-02-11 08:04:57', '2026-02-11 08:04:57'),
(147, 127, 8, 'Asrock', 12000.00, 1, '2026-02-11 08:42:20', '2026-02-11 08:42:20'),
(148, 128, 7, 'Firebat AT15', 6500.00, 1, '2026-02-11 08:50:55', '2026-02-11 08:50:55'),
(149, 129, 7, 'Firebat AT15', 6500.00, 1, '2026-02-11 15:34:07', '2026-02-11 15:34:07'),
(150, 130, 7, 'Firebat AT15', 6500.00, 1, '2026-02-11 15:53:39', '2026-02-11 15:53:39'),
(151, 131, 7, 'Firebat AT15', 6500.00, 1, '2026-02-13 16:09:06', '2026-02-13 16:09:06'),
(152, 132, 7, 'Firebat AT15', 6500.00, 1, '2026-02-13 16:29:24', '2026-02-13 16:29:24'),
(153, 133, 7, 'Firebat AT15', 6500.00, 1, '2026-02-13 16:40:13', '2026-02-13 16:40:13'),
(154, 134, 5, 'Firebat AT15', 6500.00, 1, '2026-02-13 16:47:36', '2026-02-13 16:47:36'),
(155, 135, 7, 'Firebat AT15', 6500.00, 1, '2026-02-13 16:48:45', '2026-02-13 16:48:45'),
(156, 136, 7, 'Firebat AT15', 6500.00, 1, '2026-02-13 16:59:37', '2026-02-13 16:59:37'),
(157, 137, 6, 'Asrock', 12000.00, 1, '2026-02-13 20:01:06', '2026-02-13 20:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('tuleaartur@gmail.com', '$2y$12$GijJPTHF5upPmI51w5RVJOnyMeuSvEKr5fsY7RXRh/E9SCurRWG7e', '2026-02-12 06:12:24');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `final_price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_promo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `final_price`, `stock`, `image`, `status`, `created_at`, `updated_at`, `category_id`, `subcategory_id`, `is_promo`) VALUES
(5, 'Firebat AT15', 'Intel N150\r\n16GB RAM\r\n512GB SSD', 6500.00, 1, 'products/DwQojlvKk8jwRbZiohctGoIVqbqsFroiFZWghCsq.jpg', 1, '2026-02-01 00:17:53', '2026-02-04 19:49:32', 6, 7, 1),
(6, 'Asrock', 'Ryzen 9\r\n16GB RAM\r\n1TB SSD', 12000.00, 71, 'products/CtGigr3Y0uHekGIUUPYnMypFzPhJVkPTPKvvEYVl.png', 1, '2026-02-01 01:22:55', '2026-02-04 23:35:04', 6, 9, 1),
(7, 'Firebat AT15', 'aaa', 6500.00, 70, 'products/o7XGJCYKpJxAD9vuGUdpr5R2a4sO75qU9Xy4q9xm.jpg', 1, '2026-02-01 01:43:41', '2026-02-04 23:35:04', 6, 7, 1),
(8, 'Asrock', 'qqqqq', 12000.00, 94, 'products/XPTyfYC6I9jqFfXpkPxple5WoVsGXTWpW3mIM6d1.png', 1, '2026-02-01 01:44:21', '2026-02-04 17:25:00', 6, 9, 0),
(9, 'Laptop Firebat AT15 16RAM 512SSD', 'asdasdsa', 6500.00, 122, 'products/dhZi8xgS1IjiN0TQPRk6Uw3FeqqxNfktNpZ0Gk0c.webp', 1, '2026-02-05 16:08:00', '2026-02-05 16:08:00', 6, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('21eKPMCqKwMu43OW7MIXAGKii1Lo16QFFx8GgWMs', NULL, '188.244.26.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/22H31 Instagram 414.0.0.23.79 (iPhone17,3; iOS 18_7_1; en_GB; en-GB; scale=3.00; 1179x2556; IABMV/1; 868652560) Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZnZLMkxab3dwOTJMU0FVTVp5cmN3TDd6dzFaWTZ4N0ZvMlVuajRzUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8vaWF6b3MuY29tIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770327739),
('53vNiDrKSh8wWTN5djwS7JJKsIJ4W5qClbyLtQM2', 1, '92.115.157.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU2x2V2RaaGtnWXBmN29vbXltR3JaZUVDc0cxbDdKMWN4aU5lblFnTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8vaWF6b3MuY29tIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1770313080),
('6jkhJeX3n2JCPdoyex5hABaw54VNWmYLFv9yQiG8', NULL, '35.198.247.154', 'Mozilla/5.0 (Linux; Android 12) Chrome/111.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNTZKMDh6ZFZIcVpEQVlhT0szV25XT3YyMG4zclJvNFNDTVA1NEdzNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8vaWF6b3MuY29tIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770323290),
('Ai0EN6NK2PX9tpogj7AHTNw4krTpDvpFiPvJP5uD', NULL, '188.244.26.49', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRlg4UmVRcWdIbWF4QzZrTDA0RTJPRlloNjc0aEVXOUZiQnB2WDJwWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vaWF6b3MuY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770327384),
('aMnxdF1pfsKlzFQnxVUxHEX3GW3PwzgDWQ72Ux6F', NULL, '3.236.141.135', 'okhttp/5.3.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid0laVlAwcEZ6cDVSOWt2UUNqYjFnNzBUV3JDTG43WEw4U041RGpUSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8vaWF6b3MuY29tIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770322746),
('aqNJNhUO4USYWRFQQBzLELXLvUFsD5UyTplnymZx', NULL, '27.115.124.70', 'Mozilla/5.0 (Linux; U; Android 8.1.0; zh-cn; MI 8 Build/OPM1.171019.011) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaDE5SmI5a1VDS3VtUjU1Q1UxR0Q5eVM5RG5jMENjOWRHeTVvWmoxSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8vaWF6b3MuY29tIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770319281),
('ChwA4W6UKJZGTSK9sumgqCH4ptausNKBGeNnhWLp', NULL, '188.244.26.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.7 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVG9xZTFsTVMyVzdNTWY1bnFBNEREVjhVWFA3NlpZazJUM25HOWg1WCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8vaWF6b3MuY29tIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770318247),
('DVeiWNjNnUJ3SsLfmKM1v2BikXcBojg2RZHrOzNt', NULL, '188.244.26.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23C71 Instagram 414.0.0.23.79 (iPhone17,3; iOS 26_2_1; ro_RO; ro; scale=3.00; 1179x2556; IABMV/1; 868652560) Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMXRrQUQ4TkRpUVBOeklvcHpaZHBiVGtheFpZRVExM25mUzlEWmJrdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vaWF6b3MuY29tL3N1YmNhdGVnb3J5L29mZmljZSI7czo1OiJyb3V0ZSI7czoxNjoic3ViY2F0ZWdvcnkuc2hvdyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770325515),
('GQsDI9DH8L8WgoLC5izuIvJP6PwI39p9oArfSvDG', NULL, '188.244.26.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.7 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYWU5U2I4dmxMbGpSRnlEb3QzOFJqZFNDcmlYN0I5VjRmeVB2ZUx1aSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vaWF6b3MuY29tL3JlZ2lzdGVyIjtzOjU6InJvdXRlIjtzOjg6InJlZ2lzdGVyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo0OiJjYXJ0IjthOjE6e2k6NTthOjU6e3M6MjoiaWQiO2k6NTtzOjQ6Im5hbWUiO3M6MTI6IkZpcmViYXQgQVQxNSI7czo1OiJwcmljZSI7ZDo2NTAwO3M6MzoicXR5IjtpOjE7czo1OiJzdG9jayI7aToxO319fQ==', 1770318339),
('NrSnBQeMTjG3kN5HLZx9huEBniwuHUkrnM65TxuK', NULL, '27.115.124.118', 'Mozilla/5.0 (Linux; Android 10; HUAWEI P30 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.105 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibG1GOUt0bllDQjJzMzNHZ3pnT2R5ZjRPU0x4cXhreDhQNURPc0o3UiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vaWF6b3MuY29tL2NhdGVnb3J5L2xhcHRvcHVyaSI7czo1OiJyb3V0ZSI7czoxMzoiY2F0ZWdvcnkuc2hvdyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770319261),
('SIj3qmOlGQm9i05U5tEWhe1Mnwnjy8B2JlAR1tM1', NULL, '27.115.124.6', 'Mozilla/5.0 (Linux; U; Android 8.1.0; zh-cn; MI 8 Build/OPM1.171019.011) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaG9vYmRuTEtBTHIwc2FFN08yU3B1RmlCeDBaeUQ1dDBpUno5dDEwSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vaWF6b3MuY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770319326),
('v58JfYXhJvJ7B8mIXhOrHVzSJLkca87Pd2mkrT4g', NULL, '46.166.61.251', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23C55 Instagram 414.0.0.23.79 (iPhone12,1; iOS 26_2; en_GB; en-GB; scale=2.00; 828x1792; IABMV/1; 868652560) Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZHY3anl4Uk1wUk5lZXUxVkhncU93VnZOcjJkQjhtRjRSM3NpV1RZTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8vaWF6b3MuY29tIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjQ6ImNhcnQiO2E6MTp7aTo5O2E6NTp7czoyOiJpZCI7aTo5O3M6NDoibmFtZSI7czozMjoiTGFwdG9wIEZpcmViYXQgQVQxNSAxNlJBTSA1MTJTU0QiO3M6NToicHJpY2UiO2Q6NjUwMDtzOjM6InF0eSI7aToxO3M6NToic3RvY2siO2k6MTIyO319fQ==', 1770327829),
('V6t5jrnVSbLMvmoeIQcvMWLwjNrDAYdnEl7Ai0rb', NULL, '3.236.141.135', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/138.0.7204.23 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialQzUnlBdXY3c0I0VlpQcVhQaU9ybDVmWHprS0k0cFNYMEtrWDg3diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8vaWF6b3MuY29tIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770322803),
('VbqAxqfWu7ExAgGnsP9qkfkWKyhPVreJae0zXi6g', 1, '188.244.26.49', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUGVTNXVEcGFtTGFRbHJweHNUMzVCYmNaV1BaTzM3TjVRaUtKTHZGNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8vaWF6b3MuY29tIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1770328702),
('wEXnhZO7UwclGyrKR6iZojKEt6QgYHCeRSQMuNV9', NULL, '91.250.245.142', '', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiaXRZSnJkTFdRWEdpd005dFZsemhvcTh2aUdkM1pWNEhUQ2FoOTRUdiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770313064);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(255) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `is_admin`, `role`) VALUES
(1, 'Admin', 'admin@site.md', NULL, '$2y$12$waArByKxKgs1ryRDaMS9aO2ecahyzfEROYILJXwPEQIrE0zv/1Qky', 'IkDpadHeHkf5GovYUeBsWq21dPOpE6dV0uwlGTruffTA9Y5IlUCFzDehGBT7', '2026-01-31 16:47:04', '2026-01-31 16:47:04', 1, 'admin'),
(2, 'Tulea Artur', 'tuleaartur@gmail.com', NULL, '$2y$12$2.TX54/VzM/8aQHxk2W9w.Nuf3NW5d3N0w4ZmYTSeC6bUJf9Ot2ai', 'dwv91FgHJ9m6THuvLWElEZD2ig3M4nFKIHpEm022xHdwBmd4LkkgsI0x9uLK', '2026-02-01 00:19:16', '2026-02-01 00:19:16', 0, 'user'),
(3, 'test', 'test@gmail.com', NULL, '$2y$12$rgWjTwqa7l3jTqaqV5xja.zyi2ftAF/a0hD8efg2jdNymeCJgal3u', NULL, '2026-02-10 15:43:01', '2026-02-10 15:43:01', 0, 'user'),
(4, 'Mihaela', 'mihaelacebanu08@gmail.com', NULL, '$2y$12$bxqI9WM6pdo8Z4wZ/xjVBuFxDni3tfX1Mo43ALOmy4FAP2JrjQg96', '3dt2rhGVZNH9BqWL9jCESluDEkVjTup8E8rUeJ4kvJ6OenucMaLfB9dtis2p', '2026-02-11 16:55:09', '2026-02-11 16:55:09', 0, 'user'),
(5, 'Tulea Artur', 'asdhgasdgah@gmail.com', NULL, '$2y$12$9cKqVgYmKGBcd.OniRPZ9uphBl1O1pl6CX5xbIqM/LWU4ebWtlDFe', NULL, '2026-02-12 06:13:48', '2026-02-12 06:13:48', 0, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `districts_name_unique` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `localities`
--
ALTER TABLE `localities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `localities_district_id_name_index` (`district_id`,`name`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_payment_status_index` (`payment_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `localities`
--
ALTER TABLE `localities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `localities`
--
ALTER TABLE `localities`
  ADD CONSTRAINT `localities_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
