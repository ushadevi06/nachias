-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2025 at 01:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nachias`
--

-- --------------------------------------------------------

--
-- Table structure for table `blood_groups`
--

CREATE TABLE `blood_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `blood_grp_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blood_groups`
--

INSERT INTO `blood_groups` (`id`, `blood_grp_name`, `created_at`, `updated_at`) VALUES
(1, 'A+', '2025-12-04 04:00:35', '2025-12-04 04:00:35'),
(2, 'A-', '2025-12-04 04:00:35', '2025-12-04 04:00:35'),
(3, 'B+', '2025-12-04 04:00:35', '2025-12-04 04:00:35'),
(4, 'B-', '2025-12-04 04:00:35', '2025-12-04 04:00:35'),
(5, 'O+', '2025-12-04 04:00:35', '2025-12-04 04:00:35'),
(6, 'O-', '2025-12-04 04:00:35', '2025-12-04 04:00:35'),
(7, 'AB+', '2025-12-04 04:00:35', '2025-12-04 04:00:35'),
(8, 'AB-', '2025-12-04 04:00:35', '2025-12-04 04:00:35');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `brand_name`, `status`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Park Avenue', 'Active', 1, NULL, '2025-12-01 06:10:09', '2025-12-01 06:10:09'),
(2, 'Peter England', 'Active', 1, NULL, '2025-12-01 06:10:17', '2025-12-02 04:34:21'),
(3, 'Pepe Jeanss', 'Active', 1, NULL, '2025-12-02 05:05:05', '2025-12-02 05:06:08'),
(4, 'Calvin Kleins', 'Active', 1, NULL, '2025-12-02 05:59:46', '2025-12-02 06:16:00');

-- --------------------------------------------------------

--
-- Table structure for table `brand_categories`
--

CREATE TABLE `brand_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brand_categories`
--

INSERT INTO `brand_categories` (`id`, `code`, `name`, `description`, `status`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '1022', 'Fashiono', NULL, 'Active', 1, NULL, '2025-12-01 05:56:25', '2025-12-01 05:56:47');

-- --------------------------------------------------------

--
-- Table structure for table `charges`
--

CREATE TABLE `charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `charge_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `charges`
--

INSERT INTO `charges` (`id`, `charge_name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Courier', 'Active', '2025-11-29 06:09:51', '2025-12-05 06:35:52', '2025-12-05 06:35:52'),
(2, 'Insurance', 'Active', '2025-11-29 06:10:11', '2025-11-29 06:10:11', NULL),
(3, 'Slitting', 'Active', '2025-11-29 06:10:23', '2025-12-05 06:35:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `city_name` varchar(255) NOT NULL,
  `city_code` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `state_id`, `city_name`, `city_code`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 'Madurai', 'MDU', 'Active', '2025-11-29 02:50:28', '2025-12-04 22:41:03', NULL),
(2, 5, 'Nagarcoil', 'BA', 'Active', '2025-12-04 07:42:02', '2025-12-04 22:42:12', NULL),
(3, 8, 'Alapuzha', 'AP', 'Active', '2025-12-04 23:31:13', '2025-12-04 23:31:13', NULL),
(4, 6, 'Hyderabad', NULL, 'Active', '2025-12-04 23:33:04', '2025-12-04 23:33:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `id` int(11) NOT NULL,
  `color_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`id`, `color_name`, `created_at`, `updated_at`) VALUES
(1, 'Black', '2025-12-01 11:29:38', '2025-12-01 11:29:38'),
(2, 'White', '2025-12-01 11:29:47', '2025-12-01 11:29:47'),
(3, 'Yellow', '2025-12-01 11:30:06', '2025-12-01 11:30:06'),
(4, 'Orange', '2025-12-01 11:30:17', '2025-12-01 11:30:17');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` enum('Retailer','Wholesaler') NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `mobile_no` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `transport_name` varchar(255) DEFAULT NULL,
  `booking_office` varchar(255) DEFAULT NULL,
  `zone_id` bigint(20) UNSIGNED NOT NULL,
  `stores` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `place_id` bigint(20) UNSIGNED NOT NULL,
  `address_line_1` text NOT NULL,
  `address_line_2` text DEFAULT NULL,
  `address_line_3` text DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `contact_mobile_no` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `tax_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gst_no` varchar(255) DEFAULT NULL,
  `pan_no` varchar(255) DEFAULT NULL,
  `payment_terms` text DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sales_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `box_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `bank_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `category`, `name`, `code`, `mobile_no`, `email`, `website_url`, `transport_name`, `booking_office`, `zone_id`, `stores`, `status`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `address_line_3`, `zip_code`, `contact_person_name`, `designation`, `contact_mobile_no`, `contact_email`, `tax_type_id`, `gst_no`, `pan_no`, `payment_terms`, `credit_limit`, `sales_discount`, `box_discount`, `bank_name`, `branch`, `account_number`, `ifsc_code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Wholesaler', 'Usha Devi', '1001', '7410852369', 'ushadevi.saitech@gmail.com', NULL, NULL, NULL, 1, 'Fabric', 'Active', 5, 1, 1, 'Jaihindpuram', NULL, NULL, '625011', 'Muthamil', 'Manager', '7410258963', 'muthamil.saitech@gmail.com', 1, '27AAEPM1234C1U6', 'AAAAA1234A', '12% of payment reduce', 12.00, 4.00, 3.98, 'IndusInd Bank', 'Villapuram', '90343434282', 'INDU0000018', '2025-11-29 06:53:52', '2025-11-29 06:53:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Cutting department', 'Active', '2025-11-29 06:19:41', '2025-11-29 06:19:45', NULL),
(3, 'Stitching Department', 'Active', '2025-11-29 06:19:57', '2025-12-05 06:56:40', NULL),
(5, 'ware', 'Active', '2025-12-05 06:57:58', '2025-12-05 06:58:08', '2025-12-05 06:58:08');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `emp_id` varchar(255) NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `blood_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `date_of_joining` date DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_phone` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `country` varchar(255) DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `contact_person_phone` varchar(255) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `hra` decimal(10,2) DEFAULT NULL,
  `allowances` decimal(10,2) DEFAULT NULL,
  `deductions` decimal(10,2) DEFAULT NULL,
  `gross_salary` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `esi_document` varchar(255) DEFAULT NULL,
  `pf_document` varchar(255) DEFAULT NULL,
  `aadhaar_document` varchar(255) DEFAULT NULL,
  `pan_document` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `emp_id`, `department_id`, `role_id`, `blood_group_id`, `name`, `email`, `phone`, `date_of_joining`, `father_name`, `father_phone`, `profile_image`, `password`, `status`, `country`, `state_id`, `city_id`, `address_line1`, `address_line2`, `zipcode`, `contact_person_name`, `contact_person_phone`, `basic_salary`, `hra`, `allowances`, `deductions`, `gross_salary`, `net_salary`, `esi_document`, `pf_document`, `aadhaar_document`, `pan_document`, `account_number`, `bank_name`, `ifsc_code`, `created_at`, `updated_at`) VALUES
(1, 'EMP001', 2, 1, 1, 'Usha Devi', 'admin@gmail.com', '7894561230', NULL, NULL, NULL, NULL, '$2y$10$4xvMbPRkdfd/4fvtD7e/O.LGXWYahkiCyqaKh.WEoFhSzrF8prmuO', 'Active', NULL, 5, 1, 'Jaihindpuram', NULL, '625011', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-04 05:52:36', '2025-12-04 06:20:10');

-- --------------------------------------------------------

--
-- Table structure for table `fabric_types`
--

CREATE TABLE `fabric_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fabric_type` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fabric_types`
--

INSERT INTO `fabric_types` (`id`, `fabric_type`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Polyester', 'Active', '2025-11-29 06:04:13', '2025-12-05 06:16:29', NULL),
(3, 'Polycotton', 'Active', '2025-12-05 06:02:10', '2025-12-05 06:03:31', '2025-12-05 06:03:31');

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
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_category_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `entry_type` varchar(255) DEFAULT NULL,
  `style` varchar(255) DEFAULT NULL,
  `fabric_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `design_art_no` varchar(255) DEFAULT NULL,
  `uom_id` bigint(20) UNSIGNED NOT NULL,
  `size_ratio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `color_id` longtext DEFAULT NULL,
  `product_barcode` varchar(255) DEFAULT NULL,
  `standard_costing` decimal(10,2) DEFAULT NULL,
  `store_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `related_materials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_materials`)),
  `operation_stages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`operation_stages`)),
  `service_providers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`service_providers`)),
  `wholesale_price` decimal(10,2) DEFAULT NULL,
  `retail_price` decimal(10,2) DEFAULT NULL,
  `export_price` decimal(10,2) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `brand_category_id`, `brand_id`, `name`, `code`, `entry_type`, `style`, `fabric_type_id`, `design_art_no`, `uom_id`, `size_ratio_id`, `color_id`, `product_barcode`, `standard_costing`, `store_category_id`, `related_materials`, `operation_stages`, `service_providers`, `wholesale_price`, `retail_price`, `export_price`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(19, 1, 2, '10X10X10', '1000', NULL, 'Print', 2, '10X10', 2, 1, '1,2', NULL, 250.00, NULL, '{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}}', '[\"Cutting\",\"Ironing\"]', '{\"cutting\":\"1\",\"ironing\":\"1\"}', 150.00, 100.00, 50.00, 'Active', 1, '2025-12-03 07:11:54', '2025-12-04 00:29:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action_type` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(1, 1, 'update', 'Brand', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-02 06:09:55'),
(2, 1, 'update', 'Brand', 'brands', 4, '{\"id\":4,\"brand_name\":\"Calvin Kleins\",\"status\":\"Active\",\"created_by\":1,\"deleted_at\":null,\"created_at\":\"2025-12-02T11:29:46.000000Z\",\"updated_at\":\"2025-12-02T11:39:55.000000Z\"}', '{\"id\":4,\"brand_name\":\"Calvin Kleins f\",\"status\":\"Active\",\"created_by\":1,\"deleted_at\":null,\"created_at\":\"2025-12-02T11:29:46.000000Z\",\"updated_at\":\"2025-12-02T11:44:04.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 06:14:04'),
(3, 1, 'update', 'Brand', 'brands', 4, '{\"id\":4,\"brand_name\":\"Calvin Kleins\",\"status\":\"Active\",\"created_by\":1,\"deleted_at\":null,\"created_at\":\"2025-12-02T11:29:46.000000Z\",\"updated_at\":\"2025-12-02T11:46:00.000000Z\"}', '{\"id\":4,\"brand_name\":\"Calvin Kleins\",\"status\":\"Active\",\"created_by\":1,\"deleted_at\":null,\"created_at\":\"2025-12-02T11:29:46.000000Z\",\"updated_at\":\"2025-12-02T11:46:00.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 06:18:19'),
(4, 1, 'update', 'Brand', 'brands', 4, '{\"id\":4,\"brand_name\":\"Calvin Kleins\",\"status\":\"Active\",\"created_by\":1,\"deleted_at\":null,\"created_at\":\"2025-12-02T11:29:46.000000Z\",\"updated_at\":\"2025-12-02T11:46:00.000000Z\"}', '{\"id\":4,\"brand_name\":\"Calvin Kleins\",\"status\":\"Active\",\"created_by\":1,\"deleted_at\":null,\"created_at\":\"2025-12-02T11:29:46.000000Z\",\"updated_at\":\"2025-12-02T11:46:00.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-02 06:18:40'),
(5, 1, 'update', 'Brand', 'brands', 4, '{\"id\":4,\"brand_name\":\"Calvin Kleins\",\"status\":\"Active\",\"created_by\":1,\"deleted_at\":null,\"created_at\":\"2025-12-02T11:29:46.000000Z\",\"updated_at\":\"2025-12-02T11:46:00.000000Z\"}', '{\"id\":4,\"brand_name\":\"Calvin Kleins\",\"status\":\"Active\",\"created_by\":1,\"deleted_at\":null,\"created_at\":\"2025-12-02T11:29:46.000000Z\",\"updated_at\":\"2025-12-02T11:46:00.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-03 07:11:01'),
(6, 1, 'create', 'Item', 'items', 19, NULL, '{\"brand_id\":\"2\",\"brand_category_id\":\"1\",\"entry_type\":\"items\",\"name\":\"10X10X10\",\"code\":\"1000\",\"style\":\"Print\",\"fabric_type_id\":\"2\",\"design_art_no\":null,\"uom_id\":\"2\",\"size_ratio_id\":\"1\",\"color_id\":[\"[\\\"2\\\"]\"],\"standard_costing\":\"250\",\"store_category_id\":null,\"related_materials\":\"{\\\"1\\\":{\\\"category_id\\\":\\\"2\\\",\\\"category_name\\\":\\\"Cotton(001)\\\",\\\"material_id\\\":\\\"1\\\",\\\"material_name\\\":\\\"Cotton Poplin 60 GSM()\\\"}}\",\"operation_stages\":\"[\\\"Cutting\\\"]\",\"service_providers\":\"{\\\"cutting\\\":\\\"1\\\",\\\"ironing\\\":null}\",\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2025-12-03T12:41:54.000000Z\",\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"id\":19}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-03 07:11:54'),
(7, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":\"items\",\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":null,\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"[\\\"2\\\"]\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":\"{\\\"1\\\":{\\\"category_id\\\":\\\"2\\\",\\\"category_name\\\":\\\"Cotton(001)\\\",\\\"material_id\\\":\\\"1\\\",\\\"material_name\\\":\\\"Cotton Poplin 60 GSM()\\\"}}\",\"operation_stages\":\"[\\\"Cutting\\\"]\",\"service_providers\":\"{\\\"cutting\\\":\\\"1\\\",\\\"ironing\\\":null}\",\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-03T12:41:54.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"[\\\"1\\\"]\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":null,\"operation_stages\":null,\"service_providers\":\"{\\\"cutting\\\":null,\\\"ironing\\\":null}\",\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T04:28:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', NULL, '2025-12-03 22:58:37'),
(8, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"[\\\"1\\\"]\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":null,\"operation_stages\":null,\"service_providers\":\"{\\\"cutting\\\":null,\\\"ironing\\\":null}\",\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T04:28:37.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T04:53:32.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', NULL, '2025-12-03 23:23:32'),
(9, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T04:53:32.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:52:57.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:22:57'),
(10, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:52:57.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:53:25.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:23:25'),
(11, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:53:25.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"3\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:53:34.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:23:34'),
(12, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"3\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:53:34.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"[\\\"1\\\"]\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:54:54.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:24:54'),
(13, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"[\\\"1\\\"]\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:54:54.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"[\\\"1\\\"\",\"\\\"3\\\"]\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:55:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:25:17'),
(14, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"[\\\"1\\\"\",\"\\\"3\\\"]\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:55:17.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"[\\\"2\\\"\",\"\\\"3\\\"\",\"\\\"4\\\"]\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:55:55.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:25:55'),
(15, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"[\\\"2\\\"\",\"\\\"3\\\"\",\"\\\"4\\\"]\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:55:55.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:29:37'),
(16, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:32:00'),
(17, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:32:08'),
(18, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:33:27'),
(19, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:34:34'),
(21, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:36:47'),
(22, 1, 'update', 'Item', 'items', 19, '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '{\"id\":19,\"brand_category_id\":1,\"brand_id\":2,\"name\":\"10X10X10\",\"code\":\"1000\",\"entry_type\":null,\"style\":\"Print\",\"fabric_type_id\":2,\"design_art_no\":\"10X10\",\"uom_id\":2,\"size_ratio_id\":1,\"color_id\":[\"1\",\"2\"],\"product_barcode\":null,\"standard_costing\":\"250.00\",\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"2\",\"category_name\":\"Cotton(001)\",\"material_id\":\"1\",\"material_name\":\"Cotton Poplin 60 GSM()\"}},\"operation_stages\":[\"Cutting\",\"Ironing\"],\"service_providers\":{\"cutting\":\"1\",\"ironing\":\"1\"},\"wholesale_price\":\"150.00\",\"retail_price\":\"100.00\",\"export_price\":\"50.00\",\"status\":\"Active\",\"created_by\":1,\"created_at\":\"2025-12-03T12:41:54.000000Z\",\"updated_at\":\"2025-12-04T05:59:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 00:36:54'),
(23, 1, 'create', 'Employee', 'employees', 1, NULL, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":null,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:22:36.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 05:52:36'),
(24, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":null,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:22:36.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:32:43.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:02:43'),
(25, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:32:43.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:33:17.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:03:17'),
(26, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:33:17.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:33:21.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:03:21'),
(27, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:33:21.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:33:25.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:03:25'),
(28, 1, 'create', 'Role', 'roles', 2, NULL, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":null,\"created_at\":\"2025-12-04T11:39:06.000000Z\",\"updated_at\":\"2025-12-04T11:39:06.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:09:06'),
(29, 1, 'update', 'Role Status', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":null,\"created_at\":\"2025-12-04T11:39:06.000000Z\",\"updated_at\":\"2025-12-04T11:39:06.000000Z\"}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T11:39:06.000000Z\",\"updated_at\":\"2025-12-04T11:39:17.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:09:17'),
(30, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:33:25.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:40:07.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:10:07'),
(31, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:40:07.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:40:09.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:10:09'),
(32, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:40:09.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:40:31.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:10:31'),
(33, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:40:31.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:42:34.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:12:34'),
(34, 1, 'update', 'Role Status', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:21:34.000000Z\"}', '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Inactive\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:42:40.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:12:40'),
(35, 1, 'update', 'Role Status', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Inactive\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:42:40.000000Z\"}', '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:42:42.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:12:42');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(36, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:42:34.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:17.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:13:17'),
(37, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:17.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:19.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:13:19'),
(38, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:19.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:23.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:13:23'),
(39, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:23.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:27.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:13:27'),
(40, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:27.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:30.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:13:30'),
(41, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:30.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:58.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:13:58'),
(42, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:43:58.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:44:02.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:14:02'),
(43, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:44:02.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:44:32.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:14:32'),
(44, 1, 'update', 'Role Status', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:42:42.000000Z\"}', '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Inactive\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:44:56.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:14:56'),
(45, 1, 'update', 'Role Status', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Inactive\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:44:56.000000Z\"}', '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:45:38.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:15:38'),
(46, 1, 'update', 'Role Status', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:45:38.000000Z\"}', '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Inactive\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:45:40.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:15:40'),
(47, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:44:32.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:45:49.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:15:49'),
(48, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:45:49.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:46:15.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:16:15'),
(49, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:46:15.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:47:43.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:17:43'),
(50, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:47:43.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:47:44.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:17:44'),
(51, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:47:44.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:50:07.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:20:07'),
(52, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:50:07.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:50:08.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:20:08'),
(53, 1, 'update', 'Employee', 'employees', 1, '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Inactive\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:50:08.000000Z\"}', '{\"id\":1,\"emp_id\":\"EMP001\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":1,\"name\":\"Usha Devi\",\"email\":\"admin@gmail.com\",\"phone\":\"7894561230\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"profile_image\":null,\"status\":\"Active\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"created_at\":\"2025-12-04T11:22:36.000000Z\",\"updated_at\":\"2025-12-04T11:50:10.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:20:10'),
(54, 1, 'create', 'User', 'users', 2, NULL, '{\"id\":2,\"emp_id\":null,\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:11:36.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:41:36'),
(55, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"emp_id\":null,\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:11:36.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:12:34.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 06:42:34'),
(56, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:12:34.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:12:34.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 07:15:54'),
(57, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:12:34.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:12:34.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 07:27:21'),
(58, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:12:34.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:57:32.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"78954643203\",\"bank_name\":\"indian\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 07:27:32'),
(59, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:57:32.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"78954643203\",\"bank_name\":\"indian\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:57:32.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"78954643203\",\"bank_name\":\"indian\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 07:27:57'),
(60, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:57:32.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"78954643203\",\"bank_name\":\"indian\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:58:01.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"78954643203\",\"bank_name\":\"indian\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Inactive\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 07:28:01'),
(61, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:58:01.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"78954643203\",\"bank_name\":\"indian\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Inactive\"}', '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:58:03.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"78954643203\",\"bank_name\":\"indian\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 07:28:03'),
(62, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:58:03.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"78954643203\",\"bank_name\":\"indian\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '{\"id\":2,\"emp_id\":\"102\",\"department_id\":2,\"role_id\":2,\"blood_group_id\":2,\"name\":\"Usha Devi\",\"phone\":\"7894561230\",\"email\":\"ushadevi.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T12:11:36.000000Z\",\"updated_at\":\"2025-12-04T12:58:03.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":null,\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"78954643203\",\"bank_name\":\"indian\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 07:28:41'),
(63, 1, 'create', 'User', 'users', 4, NULL, '{\"id\":4,\"emp_id\":\"1002\",\"department_id\":3,\"role_id\":1,\"blood_group_id\":5,\"name\":\"Shri\",\"phone\":\"7539864101\",\"email\":\"shri.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T13:31:41.000000Z\",\"updated_at\":\"2025-12-04T13:31:41.000000Z\",\"date_of_joining\":\"2019-06-12\",\"father_name\":\"Moorthy\",\"father_phone\":\"7568894025\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Usha Devi\",\"contact_person_phone\":\"6985471230\",\"basic_salary\":\"10000.00\",\"hra\":\"1500.00\",\"allowances\":\"250.00\",\"deductions\":\"150.00\",\"gross_salary\":\"9600.00\",\"net_salary\":\"9600.00\",\"account_number\":\"78954643203\",\"bank_name\":\"SBI\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":\"profile.png\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.pdf\",\"pan_document\":\"pan_document.pdf\",\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 08:01:41'),
(64, 1, 'update', 'User', 'users', 4, '{\"id\":4,\"emp_id\":\"1002\",\"department_id\":3,\"role_id\":1,\"blood_group_id\":5,\"name\":\"Shri\",\"phone\":\"7539864101\",\"email\":\"shri.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T13:31:41.000000Z\",\"updated_at\":\"2025-12-04T13:31:41.000000Z\",\"date_of_joining\":\"2019-06-12\",\"father_name\":\"Moorthy\",\"father_phone\":\"7568894025\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Usha Devi\",\"contact_person_phone\":\"6985471230\",\"basic_salary\":\"10000.00\",\"hra\":\"1500.00\",\"allowances\":\"250.00\",\"deductions\":\"150.00\",\"gross_salary\":\"9600.00\",\"net_salary\":\"9600.00\",\"account_number\":\"78954643203\",\"bank_name\":\"SBI\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":\"profile.png\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.pdf\",\"pan_document\":\"pan_document.pdf\",\"status\":\"Active\"}', '{\"id\":4,\"emp_id\":\"1002\",\"department_id\":3,\"role_id\":1,\"blood_group_id\":5,\"name\":\"Shri\",\"phone\":\"7539864101\",\"email\":\"shri.saitech@gmail.com\",\"email_verified_at\":null,\"created_at\":\"2025-12-04T13:31:41.000000Z\",\"updated_at\":\"2025-12-04T13:38:32.000000Z\",\"date_of_joining\":\"2019-06-12\",\"father_name\":\"Moorthy\",\"father_phone\":\"7568894025\",\"country\":null,\"state_id\":5,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Usha Devi\",\"contact_person_phone\":\"6985471230\",\"basic_salary\":\"10000.00\",\"hra\":\"1500.00\",\"allowances\":\"250.00\",\"deductions\":\"150.00\",\"gross_salary\":\"9600.00\",\"net_salary\":\"9600.00\",\"account_number\":\"78954643203\",\"bank_name\":\"SBI\",\"ifsc_code\":\"ICIC0000004\",\"profile_image\":\"profile.png\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.pdf\",\"pan_document\":\"pan_document.jpg\",\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 08:08:32');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(65, 1, 'update', 'Role Status', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Inactive\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-04T11:45:40.000000Z\"}', '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-05T03:48:26.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:18:26'),
(66, 1, 'update', 'Role Status', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T11:39:06.000000Z\",\"updated_at\":\"2025-12-04T11:39:17.000000Z\"}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":\"Inactive\",\"created_at\":\"2025-12-04T11:39:06.000000Z\",\"updated_at\":\"2025-12-05T03:48:40.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:18:40'),
(67, 1, 'update', 'Role Status', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":\"Inactive\",\"created_at\":\"2025-12-04T11:39:06.000000Z\",\"updated_at\":\"2025-12-05T03:48:40.000000Z\"}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T11:39:06.000000Z\",\"updated_at\":\"2025-12-05T03:48:53.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:18:53'),
(68, 1, 'update', 'State Status', 'states', 6, '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Active\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T03:56:32.000000Z\"}', '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:02.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:32:02'),
(69, 1, 'update', 'State Status', 'states', 6, '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:02.000000Z\"}', '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Active\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:21.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:32:21'),
(70, 1, 'update', 'State Status', 'states', 6, '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Active\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:21.000000Z\"}', '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:23.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:32:23'),
(71, 1, 'update', 'State Status', 'states', 6, '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:23.000000Z\"}', '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Active\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:25.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:32:25'),
(72, 1, 'update', 'State Status', 'states', 6, '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Active\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:25.000000Z\"}', '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:26.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:32:26'),
(73, 1, 'update', 'State Status', 'states', 6, '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:26.000000Z\"}', '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Active\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:33.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:32:33'),
(74, 1, 'update', 'State Status', 'states', 6, '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Active\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:33.000000Z\"}', '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:37.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:32:37'),
(75, 1, 'update', 'State Status', 'states', 6, '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:37.000000Z\"}', '{\"id\":6,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Active\",\"created_at\":\"2025-12-05T03:56:32.000000Z\",\"updated_at\":\"2025-12-05T04:02:39.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 22:32:39'),
(76, 1, 'delete', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T11:39:06.000000Z\",\"updated_at\":\"2025-12-05T03:48:53.000000Z\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:01:34'),
(77, 1, 'create', 'Role', 'roles', 3, NULL, '{\"id\":3,\"name\":\"supervisior\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-05T04:32:28.000000Z\",\"updated_at\":\"2025-12-05T04:32:28.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:02:28'),
(78, 1, 'delete', 'Role', 'roles', 3, '{\"id\":3,\"name\":\"supervisior\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-05T04:32:28.000000Z\",\"updated_at\":\"2025-12-05T04:32:28.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:03:55'),
(79, 1, 'create', 'Role', 'roles', 4, NULL, '{\"id\":4,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-05T04:35:08.000000Z\",\"updated_at\":\"2025-12-05T04:35:08.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:05:08'),
(80, 1, 'delete', 'Role', 'roles', 4, '{\"id\":4,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-05T04:35:08.000000Z\",\"updated_at\":\"2025-12-05T04:35:08.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:05:12'),
(81, 1, 'delete', 'Role', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-04T10:19:27.000000Z\",\"updated_at\":\"2025-12-05T03:48:26.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:07:04'),
(82, 1, 'create', 'Role', 'roles', 7, NULL, '{\"id\":7,\"name\":\"accountant\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-05T04:42:14.000000Z\",\"updated_at\":\"2025-12-05T04:42:14.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:12:14'),
(83, 1, 'delete', 'Role', 'roles', 6, '{\"id\":6,\"name\":\"Manager\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-05T04:39:14.000000Z\",\"updated_at\":\"2025-12-05T04:39:14.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:13:57'),
(84, 1, 'delete', 'Role', 'roles', 7, '{\"id\":7,\"name\":\"accountant\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-05T04:42:14.000000Z\",\"updated_at\":\"2025-12-05T04:42:14.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:14:09'),
(85, 1, 'create', 'Role', 'roles', 8, NULL, '{\"id\":8,\"name\":\"Accountant\",\"guard_name\":\"web\",\"status\":\"Active\",\"created_at\":\"2025-12-05T04:44:21.000000Z\",\"updated_at\":\"2025-12-05T04:44:21.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-04 23:14:21'),
(86, 1, 'update', 'UOM Status', 'uoms', 3, '{\"id\":3,\"uom_code\":\"KGS\",\"uom_name\":\"Kilo grams\",\"description\":null,\"status\":\"Active\",\"created_at\":\"2025-11-29T11:17:13.000000Z\",\"updated_at\":\"2025-12-05T08:04:39.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"uom_code\":\"KGS\",\"uom_name\":\"Kilo grams\",\"description\":null,\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:17:13.000000Z\",\"updated_at\":\"2025-12-05T08:30:23.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 03:00:23'),
(87, 1, 'update', 'UOM Status', 'uoms', 3, '{\"id\":3,\"uom_code\":\"KGS\",\"uom_name\":\"Kilo grams\",\"description\":null,\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:17:13.000000Z\",\"updated_at\":\"2025-12-05T08:30:23.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"uom_code\":\"KGS\",\"uom_name\":\"Kilo grams\",\"description\":null,\"status\":\"Active\",\"created_at\":\"2025-11-29T11:17:13.000000Z\",\"updated_at\":\"2025-12-05T08:30:24.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 03:00:24'),
(88, 1, 'update', 'UOM Status', 'uoms', 3, '{\"id\":3,\"uom_code\":\"KGS\",\"uom_name\":\"Kilo grams\",\"description\":null,\"status\":\"Active\",\"created_at\":\"2025-11-29T11:17:13.000000Z\",\"updated_at\":\"2025-12-05T08:30:24.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"uom_code\":\"KGS\",\"uom_name\":\"Kilo grams\",\"description\":null,\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:17:13.000000Z\",\"updated_at\":\"2025-12-05T09:37:08.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:07:08'),
(89, 1, 'update', 'UOM Status', 'uoms', 3, '{\"id\":3,\"uom_code\":\"KGS\",\"uom_name\":\"Kilo grams\",\"description\":null,\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:17:13.000000Z\",\"updated_at\":\"2025-12-05T09:37:08.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"uom_code\":\"KGS\",\"uom_name\":\"Kilo grams\",\"description\":null,\"status\":\"Active\",\"created_at\":\"2025-11-29T11:17:13.000000Z\",\"updated_at\":\"2025-12-05T09:37:10.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:07:10'),
(90, 1, 'update', 'Operation Stage Status', 'operation_stages', 4, '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Active\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-11-29T10:36:19.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T09:48:39.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:18:39'),
(91, 1, 'update', 'Operation Stage Status', 'operation_stages', 4, '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T09:48:39.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Active\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T10:03:18.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:33:18'),
(92, 1, 'update', 'Operation Stage Status', 'operation_stages', 4, '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Active\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T10:03:18.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T10:03:19.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:33:19'),
(93, 1, 'update', 'Operation Stage Status', 'operation_stages', 4, '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T10:03:19.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Active\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T10:03:31.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:33:31'),
(94, 1, 'update', 'Operation Stage Status', 'operation_stages', 4, '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Active\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T10:03:31.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T10:03:32.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:33:32'),
(95, 1, 'create', 'Operation Stage', 'operation_stages', 5, NULL, '{\"operation_stage_name\":\"Washing\",\"status\":\"Active\",\"updated_at\":\"2025-12-05T10:04:52.000000Z\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:34:52'),
(96, 1, 'update', 'Operation Stage Status', 'operation_stages', 4, '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T10:03:32.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"operation_stage_name\":\"Ironing\",\"status\":\"Active\",\"created_at\":\"2025-11-29T10:36:19.000000Z\",\"updated_at\":\"2025-12-05T10:04:55.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:34:55'),
(97, 1, 'update', 'Operation Stage Status', 'operation_stages', 5, '{\"id\":5,\"operation_stage_name\":\"Washing\",\"status\":\"Active\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"updated_at\":\"2025-12-05T10:04:52.000000Z\",\"deleted_at\":null}', '{\"id\":5,\"operation_stage_name\":\"Washing\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"updated_at\":\"2025-12-05T10:04:58.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:34:58'),
(98, 1, 'update', 'Operation Stage', 'operation_stages', 5, '{\"id\":5,\"operation_stage_name\":\"Washing\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"updated_at\":\"2025-12-05T10:04:58.000000Z\",\"deleted_at\":null}', '{\"id\":5,\"operation_stage_name\":\"Washing\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"updated_at\":\"2025-12-05T10:05:57.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:35:57'),
(99, 1, 'update', 'Operation Stage Status', 'operation_stages', 5, '{\"id\":5,\"operation_stage_name\":\"Washing\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"updated_at\":\"2025-12-05T10:05:57.000000Z\",\"deleted_at\":null}', '{\"id\":5,\"operation_stage_name\":\"Washing\",\"status\":\"Active\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"updated_at\":\"2025-12-05T10:06:00.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:36:00'),
(100, 1, 'update', 'Operation Stage Status', 'operation_stages', 5, '{\"id\":5,\"operation_stage_name\":\"Washing\",\"status\":\"Active\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"updated_at\":\"2025-12-05T10:06:00.000000Z\",\"deleted_at\":null}', '{\"id\":5,\"operation_stage_name\":\"Washing\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"updated_at\":\"2025-12-05T10:06:01.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:36:01'),
(101, 1, 'delete', 'Operation Stage', 'operation_stages', 5, '{\"id\":5,\"operation_stage_name\":\"Washing\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T10:04:52.000000Z\",\"updated_at\":\"2025-12-05T10:06:01.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:36:04'),
(102, 1, 'update', 'Zone', 'zones', 1, '{\"id\":1,\"zone_name\":\"South Zone\",\"state_id\":5,\"city_ids\":\"1\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:19:55.000000Z\",\"updated_at\":\"2025-11-29T11:22:54.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"zone_name\":\"South Zone\",\"state_id\":5,\"city_ids\":\"1,2\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:19:55.000000Z\",\"updated_at\":\"2025-12-05T10:14:57.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:44:57'),
(103, 1, 'update', 'Zone Status', 'zones', 1, '{\"id\":1,\"zone_name\":\"South Zone\",\"state_id\":5,\"city_ids\":\"1,2\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:19:55.000000Z\",\"updated_at\":\"2025-12-05T10:14:57.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"zone_name\":\"South Zone\",\"state_id\":5,\"city_ids\":\"1,2\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:19:55.000000Z\",\"updated_at\":\"2025-12-05T10:15:20.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:45:20'),
(104, 1, 'update', 'Zone Status', 'zones', 1, '{\"id\":1,\"zone_name\":\"South Zone\",\"state_id\":5,\"city_ids\":\"1,2\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:19:55.000000Z\",\"updated_at\":\"2025-12-05T10:15:20.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"zone_name\":\"South Zone\",\"state_id\":5,\"city_ids\":\"1,2\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:19:55.000000Z\",\"updated_at\":\"2025-12-05T10:15:34.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:45:34'),
(105, 1, 'create', 'Zone', 'zones', 2, NULL, '{\"zone_name\":\"test\",\"state_id\":\"6\",\"city_ids\":\"4\",\"status\":\"Active\",\"updated_at\":\"2025-12-05T10:16:55.000000Z\",\"created_at\":\"2025-12-05T10:16:55.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:46:55'),
(106, 1, 'update', 'Zone', 'zones', 2, '{\"id\":2,\"zone_name\":\"test\",\"state_id\":6,\"city_ids\":\"4\",\"status\":\"Active\",\"created_at\":\"2025-12-05T10:16:55.000000Z\",\"updated_at\":\"2025-12-05T10:16:55.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"zone_name\":\"test\",\"state_id\":6,\"city_ids\":\"4\",\"status\":\"Active\",\"created_at\":\"2025-12-05T10:16:55.000000Z\",\"updated_at\":\"2025-12-05T10:17:21.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 04:47:21'),
(107, 1, 'create', 'Zone', 'zones', 3, NULL, '{\"zone_name\":\"Guruvayur\",\"state_id\":\"8\",\"city_ids\":\"3\",\"status\":\"Active\",\"updated_at\":\"2025-12-05T10:34:44.000000Z\",\"created_at\":\"2025-12-05T10:34:44.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:04:44'),
(108, 1, 'update', 'Zone Status', 'zones', 3, '{\"id\":3,\"zone_name\":\"Guruvayur\",\"state_id\":8,\"city_ids\":\"3\",\"status\":\"Active\",\"created_at\":\"2025-12-05T10:34:44.000000Z\",\"updated_at\":\"2025-12-05T10:34:44.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"zone_name\":\"Guruvayur\",\"state_id\":8,\"city_ids\":\"3\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T10:34:44.000000Z\",\"updated_at\":\"2025-12-05T10:34:46.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:04:46'),
(109, 1, 'update', 'Zone Status', 'zones', 3, '{\"id\":3,\"zone_name\":\"Guruvayur\",\"state_id\":8,\"city_ids\":\"3\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T10:34:44.000000Z\",\"updated_at\":\"2025-12-05T10:34:46.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"zone_name\":\"Guruvayur\",\"state_id\":8,\"city_ids\":\"3\",\"status\":\"Active\",\"created_at\":\"2025-12-05T10:34:44.000000Z\",\"updated_at\":\"2025-12-05T10:34:47.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:04:47'),
(110, 1, 'delete', 'Zone', 'zones', 2, '{\"id\":2,\"zone_name\":\"test\",\"state_id\":6,\"city_ids\":\"4\",\"status\":\"Active\",\"created_at\":\"2025-12-05T10:16:55.000000Z\",\"updated_at\":\"2025-12-05T10:17:21.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:05:10'),
(111, 1, 'update', 'Size Ratio Status', 'size_ratios', 1, '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-11-29T11:29:59.000000Z\"}', '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:20:32.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:50:32'),
(112, 1, 'update', 'Size Ratio Status', 'size_ratios', 1, '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:20:32.000000Z\"}', '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:20:34.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:50:34'),
(113, 1, 'update', 'Size Ratio Status', 'size_ratios', 1, '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:20:34.000000Z\"}', '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:21:44.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:51:44'),
(114, 1, 'update', 'Size Ratio Status', 'size_ratios', 1, '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:21:44.000000Z\"}', '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:21:45.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:51:45'),
(115, 1, 'update', 'Size Ratio Status', 'size_ratios', 1, '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:21:45.000000Z\"}', '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:21:47.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:51:47'),
(116, 1, 'update', 'Size Ratio Status', 'size_ratios', 1, '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:21:47.000000Z\"}', '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:21:48.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:51:48'),
(117, 1, 'update', 'Size Ratio', 'size_ratios', 1, '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:21:48.000000Z\"}', '{\"id\":1,\"size\":\"38,40,42\",\"ratio\":\"1,2,9,8\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-11-29T11:29:31.000000Z\",\"updated_at\":\"2025-12-05T11:21:53.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:51:54'),
(118, 1, 'create', 'Size Ratio', 'size_ratios', 2, NULL, '{\"size\":\"38,40,42\",\"ratio\":\"2,2,2\",\"status\":\"Active\",\"updated_at\":\"2025-12-05T11:27:12.000000Z\",\"created_at\":\"2025-12-05T11:27:12.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:57:12'),
(119, 1, 'update', 'Size Ratio Status', 'size_ratios', 2, '{\"id\":2,\"size\":\"38,40,42\",\"ratio\":\"2,2,2\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-12-05T11:27:12.000000Z\",\"updated_at\":\"2025-12-05T11:27:12.000000Z\"}', '{\"id\":2,\"size\":\"38,40,42\",\"ratio\":\"2,2,2\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_at\":\"2025-12-05T11:27:12.000000Z\",\"updated_at\":\"2025-12-05T11:27:15.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:57:15'),
(120, 1, 'update', 'Size Ratio Status', 'size_ratios', 2, '{\"id\":2,\"size\":\"38,40,42\",\"ratio\":\"2,2,2\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_at\":\"2025-12-05T11:27:12.000000Z\",\"updated_at\":\"2025-12-05T11:27:15.000000Z\"}', '{\"id\":2,\"size\":\"38,40,42\",\"ratio\":\"2,2,2\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-12-05T11:27:12.000000Z\",\"updated_at\":\"2025-12-05T11:27:16.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:57:16'),
(121, 1, 'delete', 'Size Ratio', 'size_ratios', 2, '{\"id\":2,\"size\":\"38,40,42\",\"ratio\":\"2,2,2\",\"status\":\"Active\",\"deleted_at\":null,\"created_at\":\"2025-12-05T11:27:12.000000Z\",\"updated_at\":\"2025-12-05T11:27:16.000000Z\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 05:57:20'),
(122, 1, 'update', 'Fabric Type Status', 'fabric_types', 2, '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-04T10:33:05.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-05T11:31:36.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:01:36'),
(123, 1, 'update', 'Fabric Type Status', 'fabric_types', 2, '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-05T11:31:36.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-05T11:31:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:01:37'),
(124, 1, 'update', 'Fabric Type Status', 'fabric_types', 2, '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-05T11:31:37.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-05T11:31:39.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:01:39'),
(125, 1, 'create', 'Fabric Type', 'fabric_types', 3, NULL, '{\"fabric_type\":\"Polycotton\",\"status\":\"Active\",\"updated_at\":\"2025-12-05T11:32:10.000000Z\",\"created_at\":\"2025-12-05T11:32:10.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:02:10'),
(126, 1, 'delete', 'Fabric Type', 'fabric_types', 3, '{\"id\":3,\"fabric_type\":\"Polycotton\",\"status\":\"Active\",\"created_at\":\"2025-12-05T11:32:10.000000Z\",\"updated_at\":\"2025-12-05T11:32:10.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:03:31'),
(127, 1, 'update', 'Fabric Type Status', 'fabric_types', 2, '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-05T11:31:39.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-05T11:46:27.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:16:27'),
(128, 1, 'update', 'Fabric Type Status', 'fabric_types', 2, '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-05T11:46:27.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"fabric_type\":\"Polyester\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:34:13.000000Z\",\"updated_at\":\"2025-12-05T11:46:29.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:16:29'),
(129, 1, 'update', 'Department Status', 'departments', 3, '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-11-29T11:49:57.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:22:07.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:52:07'),
(130, 1, 'update', 'Department Status', 'departments', 3, '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:22:07.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:22:08.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:52:08'),
(131, 1, 'update', 'Department Status', 'departments', 3, '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:22:08.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:24:07.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:54:07'),
(132, 1, 'update', 'Department Status', 'departments', 3, '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:24:07.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:24:08.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:54:08'),
(133, 1, 'update', 'Department Status', 'departments', 3, '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:24:08.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:25:43.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:55:43'),
(134, 1, 'update', 'Department Status', 'departments', 3, '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Inactive\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:25:43.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"department\":\"Stitching Department\",\"status\":\"Active\",\"created_at\":\"2025-11-29T11:49:57.000000Z\",\"updated_at\":\"2025-12-05T12:25:44.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:55:44'),
(135, 1, 'update', 'Department', 'departments', 3, NULL, '{\"department\":\"Stitching Department\",\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:56:40'),
(136, 1, 'create', 'Department', 'departments', 4, NULL, '{\"department\":\"Packing Department\",\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:56:56'),
(137, 1, 'update', 'Department Status', 'departments', 4, '{\"id\":4,\"department\":\"Packing Department\",\"status\":\"Active\",\"created_at\":\"2025-12-05T12:26:56.000000Z\",\"updated_at\":\"2025-12-05T12:26:56.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"department\":\"Packing Department\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T12:26:56.000000Z\",\"updated_at\":\"2025-12-05T12:26:59.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:56:59'),
(138, 1, 'delete', 'Department', 'departments', 4, '{\"id\":4,\"department\":\"Packing Department\",\"status\":\"Inactive\",\"created_at\":\"2025-12-05T12:26:56.000000Z\",\"updated_at\":\"2025-12-05T12:26:59.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:57:01'),
(139, 1, 'create', 'Department', 'departments', 5, NULL, '{\"department\":\"ware\",\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:57:58'),
(140, 1, 'delete', 'Department', 'departments', 5, '{\"id\":5,\"department\":\"ware\",\"status\":\"Active\",\"created_at\":\"2025-12-05T12:27:58.000000Z\",\"updated_at\":\"2025-12-05T12:27:58.000000Z\",\"deleted_at\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 06:58:08'),
(141, 1, 'create', 'Tax', 'taxes', 1, NULL, '{\"item_name\":\"Mirror with Sashes\",\"tax_rate\":\"4\",\"status\":\"Active\",\"updated_at\":\"2025-12-05T12:35:03.000000Z\",\"created_at\":\"2025-12-05T12:35:03.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 07:05:03'),
(142, 1, 'update', 'Tax Status', 'taxes', 1, '{\"id\":1,\"item_name\":\"Mirror with Sashes\",\"tax_rate\":\"4.00\",\"created_at\":\"2025-12-05T12:35:03.000000Z\",\"updated_at\":\"2025-12-05T12:35:03.000000Z\",\"deleted_at\":null,\"status\":\"Active\"}', '{\"id\":1,\"item_name\":\"Mirror with Sashes\",\"tax_rate\":\"4.00\",\"created_at\":\"2025-12-05T12:35:03.000000Z\",\"updated_at\":\"2025-12-05T12:35:05.000000Z\",\"deleted_at\":null,\"status\":\"Inactive\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 07:05:05'),
(143, 1, 'update', 'Tax Status', 'taxes', 1, '{\"id\":1,\"item_name\":\"Mirror with Sashes\",\"tax_rate\":\"4.00\",\"created_at\":\"2025-12-05T12:35:03.000000Z\",\"updated_at\":\"2025-12-05T12:35:05.000000Z\",\"deleted_at\":null,\"status\":\"Inactive\"}', '{\"id\":1,\"item_name\":\"Mirror with Sashes\",\"tax_rate\":\"4.00\",\"created_at\":\"2025-12-05T12:35:03.000000Z\",\"updated_at\":\"2025-12-05T12:35:06.000000Z\",\"deleted_at\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 07:05:06'),
(144, 1, 'update', 'Tax', 'taxes', 1, '{\"id\":1,\"item_name\":\"Mirror with Sashes\",\"tax_rate\":\"4.00\",\"created_at\":\"2025-12-05T12:35:03.000000Z\",\"updated_at\":\"2025-12-05T12:35:06.000000Z\",\"deleted_at\":null,\"status\":\"Active\"}', '{\"id\":1,\"item_name\":\"Mirror with Sashes\",\"tax_rate\":\"4.00\",\"created_at\":\"2025-12-05T12:35:03.000000Z\",\"updated_at\":\"2025-12-05T12:35:44.000000Z\",\"deleted_at\":null,\"status\":\"Active\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 07:05:44'),
(145, 1, 'delete', 'Tax', 'taxes', 1, '{\"id\":1,\"item_name\":\"Mirror with Sashes\",\"tax_rate\":\"4.00\",\"created_at\":\"2025-12-05T12:35:03.000000Z\",\"updated_at\":\"2025-12-05T12:35:44.000000Z\",\"deleted_at\":null,\"status\":\"Active\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-05 07:05:47');

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_09_17_090842_add_columns_to_users_table', 2),
(6, '2025_11_25_123556_create_departments_table', 3),
(7, '2025_11_29_053421_create_permission_tables', 4),
(8, '2025_11_29_060000_create_states_table', 5),
(9, '2025_11_29_070324_create_cities_table', 6),
(10, '2025_11_29_084554_create_places_table', 7),
(11, '2025_11_29_100048_create_uoms_table', 8),
(12, '2025_11_29_102729_create_operation_stages_table', 9),
(13, '2025_11_29_104248_create_zones_table', 10),
(14, '2025_11_29_112506_create_size_ratios_table', 11),
(15, '2025_11_29_113132_create_fabric_types_table', 12),
(16, '2025_11_29_113538_create_charges_table', 13),
(17, '2025_11_29_114134_create_store_locations_table', 14),
(18, '2025_11_29_115050_create_taxes_table', 15),
(19, '2025_11_29_115956_create_customers_table', 16),
(20, '2025_11_29_124414_create_suppliers_table', 17),
(21, '2025_12_01_045358_create_purchase_commission_agents_table', 18),
(22, '2025_12_01_064005_create_suppliers_table', 19),
(23, '2025_12_01_080800_create_service_providers', 20),
(24, '2025_12_01_082706_create_sales_agents_table', 21),
(25, '2025_12_01_090225_create_store_categories_table', 22),
(26, '2025_12_01_100306_create_raw_materials_table', 23),
(27, '2025_12_01_112249_create_brand_categories_table', 24),
(28, '2025_12_01_113153_create_brands_table', 25),
(29, '2025_12_02_102928_create_logs_table', 26),
(30, '2025_12_02_103943_create_items_table', 27),
(31, '2025_12_02_113549_update_logs_table_add_more_fields', 28),
(32, '2025_12_04_061149_create_roles_table', 29),
(33, '2025_12_04_063657_create_role_has_permissions_table', 30),
(34, '2025_12_04_082127_update_permissions_table', 31),
(35, '2025_12_04_092815_create_blood_groups_table', 32),
(36, '2025_12_04_094202_create_employees_table', 33),
(37, '2025_12_05_043033_create_model_has_roles_table', 34),
(38, '2025_12_05_043304_add_deleted_at_to_roles_table', 35),
(39, '2025_12_05_044713_add_soft_deletes_to_states_table', 36),
(40, '2025_12_05_045515_add_soft_deletes_to_cities_table', 37),
(41, '2025_12_05_060046_add_soft_deletes_to_places_table', 38),
(42, '2025_12_05_062813_add_soft_deletes_to_users_table', 39),
(43, '2025_12_05_080621_add_deleted_at_to_uoms_table', 40),
(44, '2025_12_05_094645_add_deleted_at_to_operation_stages_table', 41),
(45, '2025_12_05_100841_add_deleted_at_to_zones_table', 42),
(46, '2025_12_05_111433_add_deleted_at_to_size_ratios_table', 43),
(47, '2025_12_05_112852_add_deleted_at_to_fabric_types_table', 44),
(48, '2025_12_05_120504_add_deleted_at_to_charges_table', 45),
(49, '2025_12_05_120856_add_deleted_at_to_store_location', 46),
(50, '2025_12_05_121720_add_deleted_at_to_departments', 47),
(51, '2025_12_05_123004_add_deleted_at_to_taxes_table', 48),
(52, '2025_12_05_123743_add_deleted_at_to_customers_table', 49);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `operation_stages`
--

CREATE TABLE `operation_stages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `operation_stage_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `operation_stages`
--

INSERT INTO `operation_stages` (`id`, `operation_stage_name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'Cutting', 'Active', '2025-11-29 05:03:08', '2025-11-29 05:03:08', NULL),
(4, 'Ironing', 'Active', '2025-11-29 05:06:19', '2025-12-05 04:34:55', NULL),
(5, 'Washing', 'Inactive', '2025-12-05 04:34:52', '2025-12-05 04:36:04', '2025-12-05 04:36:04');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `module`, `action`, `label`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard', 'view', 'View Dashboard', 'view dashboard', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(2, 'roles', 'create', 'Create Roles', 'create roles', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(3, 'roles', 'edit', 'Edit Roles', 'edit roles', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(4, 'roles', 'delete', 'Delete Roles', 'delete roles', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(5, 'roles', 'view', 'View Roles', 'view roles', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(6, 'employee', 'create', 'Create Employee', 'create employee', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(7, 'employee', 'edit', 'Edit Employee', 'edit employee', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(8, 'employee', 'delete', 'Delete Employee', 'delete employee', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(9, 'employee', 'view', 'View Employee', 'view employee', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(10, 'states', 'create', 'Create States', 'create states', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(11, 'states', 'edit', 'Edit States', 'edit states', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(12, 'states', 'delete', 'Delete States', 'delete states', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(13, 'states', 'view', 'View States', 'view states', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(14, 'cities', 'create', 'Create Cities', 'create cities', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(15, 'cities', 'edit', 'Edit Cities', 'edit cities', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(16, 'cities', 'delete', 'Delete Cities', 'delete cities', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(17, 'cities', 'view', 'View Cities', 'view cities', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(18, 'service-points', 'create', 'Create Service Points', 'create service-points', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(19, 'service-points', 'edit', 'Edit Service Points', 'edit service-points', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(20, 'service-points', 'delete', 'Delete Service Points', 'delete service-points', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(21, 'service-points', 'view', 'View Service Points', 'view service-points', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(22, 'uoms', 'create', 'Create Uoms', 'create uoms', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(23, 'uoms', 'edit', 'Edit Uoms', 'edit uoms', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(24, 'uoms', 'delete', 'Delete Uoms', 'delete uoms', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(25, 'uoms', 'view', 'View Uoms', 'view uoms', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(26, 'operation-stages', 'create', 'Create Operation Stages', 'create operation-stages', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(27, 'operation-stages', 'edit', 'Edit Operation Stages', 'edit operation-stages', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(28, 'operation-stages', 'delete', 'Delete Operation Stages', 'delete operation-stages', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(29, 'operation-stages', 'view', 'View Operation Stages', 'view operation-stages', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(30, 'zones', 'create', 'Create Zones', 'create zones', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(31, 'zones', 'edit', 'Edit Zones', 'edit zones', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(32, 'zones', 'delete', 'Delete Zones', 'delete zones', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(33, 'zones', 'view', 'View Zones', 'view zones', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(34, 'size-ratio', 'create', 'Create Size Ratio', 'create size-ratio', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(35, 'size-ratio', 'edit', 'Edit Size Ratio', 'edit size-ratio', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(36, 'size-ratio', 'delete', 'Delete Size Ratio', 'delete size-ratio', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(37, 'size-ratio', 'view', 'View Size Ratio', 'view size-ratio', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(38, 'fabric-type', 'create', 'Create Fabric Type', 'create fabric-type', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(39, 'fabric-type', 'edit', 'Edit Fabric Type', 'edit fabric-type', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(40, 'fabric-type', 'delete', 'Delete Fabric Type', 'delete fabric-type', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(41, 'fabric-type', 'view', 'View Fabric Type', 'view fabric-type', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(42, 'charges', 'create', 'Create Charges', 'create charges', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(43, 'charges', 'edit', 'Edit Charges', 'edit charges', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(44, 'charges', 'delete', 'Delete Charges', 'delete charges', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(45, 'charges', 'view', 'View Charges', 'view charges', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(46, 'store-location', 'create', 'Create Store Location', 'create store-location', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(47, 'store-location', 'edit', 'Edit Store Location', 'edit store-location', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(48, 'store-location', 'delete', 'Delete Store Location', 'delete store-location', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(49, 'store-location', 'view', 'View Store Location', 'view store-location', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(50, 'departments', 'create', 'Create Departments', 'create departments', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(51, 'departments', 'edit', 'Edit Departments', 'edit departments', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(52, 'departments', 'delete', 'Delete Departments', 'delete departments', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(53, 'departments', 'view', 'View Departments', 'view departments', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(54, 'taxes', 'create', 'Create Taxes', 'create taxes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(55, 'taxes', 'edit', 'Edit Taxes', 'edit taxes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(56, 'taxes', 'delete', 'Delete Taxes', 'delete taxes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(57, 'taxes', 'view', 'View Taxes', 'view taxes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(58, 'customers', 'create', 'Create Customers', 'create customers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(59, 'customers', 'edit', 'Edit Customers', 'edit customers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(60, 'customers', 'delete', 'Delete Customers', 'delete customers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(61, 'customers', 'view', 'View Customers', 'view customers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(62, 'suppliers', 'create', 'Create Suppliers', 'create suppliers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(63, 'suppliers', 'edit', 'Edit Suppliers', 'edit suppliers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(64, 'suppliers', 'delete', 'Delete Suppliers', 'delete suppliers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(65, 'suppliers', 'view', 'View Suppliers', 'view suppliers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(66, 'service-providers', 'create', 'Create Service Providers', 'create service-providers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(67, 'service-providers', 'edit', 'Edit Service Providers', 'edit service-providers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(68, 'service-providers', 'delete', 'Delete Service Providers', 'delete service-providers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(69, 'service-providers', 'view', 'View Service Providers', 'view service-providers', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(70, 'sales-agents', 'create', 'Create Sales Agents', 'create sales-agents', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(71, 'sales-agents', 'edit', 'Edit Sales Agents', 'edit sales-agents', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(72, 'sales-agents', 'delete', 'Delete Sales Agents', 'delete sales-agents', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(73, 'sales-agents', 'view', 'View Sales Agents', 'view sales-agents', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(74, 'purchase-commission-agent', 'create', 'Create Purchase Commission Agent', 'create purchase-commission-agent', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(75, 'purchase-commission-agent', 'edit', 'Edit Purchase Commission Agent', 'edit purchase-commission-agent', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(76, 'purchase-commission-agent', 'delete', 'Delete Purchase Commission Agent', 'delete purchase-commission-agent', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(77, 'purchase-commission-agent', 'view', 'View Purchase Commission Agent', 'view purchase-commission-agent', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(78, 'store-categories', 'create', 'Create Store Categories', 'create store-categories', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(79, 'store-categories', 'edit', 'Edit Store Categories', 'edit store-categories', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(80, 'store-categories', 'delete', 'Delete Store Categories', 'delete store-categories', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(81, 'store-categories', 'view', 'View Store Categories', 'view store-categories', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(82, 'raw_materials', 'create', 'Create Raw_materials', 'create raw_materials', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(83, 'raw_materials', 'edit', 'Edit Raw_materials', 'edit raw_materials', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(84, 'raw_materials', 'delete', 'Delete Raw_materials', 'delete raw_materials', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(85, 'raw_materials', 'view', 'View Raw_materials', 'view raw_materials', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(86, 'brand-category', 'create', 'Create Brand Category', 'create brand-category', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(87, 'brand-category', 'edit', 'Edit Brand Category', 'edit brand-category', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(88, 'brand-category', 'delete', 'Delete Brand Category', 'delete brand-category', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(89, 'brand-category', 'view', 'View Brand Category', 'view brand-category', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(90, 'brands', 'create', 'Create Brands', 'create brands', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(91, 'brands', 'edit', 'Edit Brands', 'edit brands', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(92, 'brands', 'delete', 'Delete Brands', 'delete brands', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(93, 'brands', 'view', 'View Brands', 'view brands', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(94, 'items', 'create', 'Create Items', 'create items', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(95, 'items', 'edit', 'Edit Items', 'edit items', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(96, 'items', 'delete', 'Delete Items', 'delete items', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(97, 'items', 'view', 'View Items', 'view items', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(98, 'purchase-order', 'create', 'Create Purchase Order', 'create purchase-order', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(99, 'purchase-order', 'edit', 'Edit Purchase Order', 'edit purchase-order', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(100, 'purchase-order', 'delete', 'Delete Purchase Order', 'delete purchase-order', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(101, 'purchase-order', 'view', 'View Purchase Order', 'view purchase-order', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(102, 'purchase-invoice', 'create', 'Create Purchase Invoice', 'create purchase-invoice', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(103, 'purchase-invoice', 'edit', 'Edit Purchase Invoice', 'edit purchase-invoice', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(104, 'purchase-invoice', 'delete', 'Delete Purchase Invoice', 'delete purchase-invoice', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(105, 'purchase-invoice', 'view', 'View Purchase Invoice', 'view purchase-invoice', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(106, 'debit-notes', 'create', 'Create Debit Notes', 'create debit-notes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(107, 'debit-notes', 'edit', 'Edit Debit Notes', 'edit debit-notes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(108, 'debit-notes', 'delete', 'Delete Debit Notes', 'delete debit-notes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(109, 'debit-notes', 'view', 'View Debit Notes', 'view debit-notes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(110, 'grn-entry', 'create', 'Create Grn Entry', 'create grn-entry', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(111, 'grn-entry', 'edit', 'Edit Grn Entry', 'edit grn-entry', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(112, 'grn-entry', 'delete', 'Delete Grn Entry', 'delete grn-entry', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(113, 'grn-entry', 'view', 'View Grn Entry', 'view grn-entry', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(114, 'stock-entry', 'create', 'Create Stock Entry', 'create stock-entry', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(115, 'stock-entry', 'edit', 'Edit Stock Entry', 'edit stock-entry', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(116, 'stock-entry', 'delete', 'Delete Stock Entry', 'delete stock-entry', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(117, 'stock-entry', 'view', 'View Stock Entry', 'view stock-entry', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(118, 'stock-consumable-return', 'create', 'Create Stock Consumable Return', 'create stock-consumable-return', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(119, 'stock-consumable-return', 'edit', 'Edit Stock Consumable Return', 'edit stock-consumable-return', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(120, 'stock-consumable-return', 'delete', 'Delete Stock Consumable Return', 'delete stock-consumable-return', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(121, 'stock-consumable-return', 'view', 'View Stock Consumable Return', 'view stock-consumable-return', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(122, 'sales-order', 'create', 'Create Sales Order', 'create sales-order', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(123, 'sales-order', 'edit', 'Edit Sales Order', 'edit sales-order', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(124, 'sales-order', 'delete', 'Delete Sales Order', 'delete sales-order', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(125, 'sales-order', 'view', 'View Sales Order', 'view sales-order', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(126, 'sales-invoice', 'create', 'Create Sales Invoice', 'create sales-invoice', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(127, 'sales-invoice', 'edit', 'Edit Sales Invoice', 'edit sales-invoice', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(128, 'sales-invoice', 'delete', 'Delete Sales Invoice', 'delete sales-invoice', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(129, 'sales-invoice', 'view', 'View Sales Invoice', 'view sales-invoice', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(130, 'credit-notes', 'create', 'Create Credit Notes', 'create credit-notes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(131, 'credit-notes', 'edit', 'Edit Credit Notes', 'edit credit-notes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(132, 'credit-notes', 'delete', 'Delete Credit Notes', 'delete credit-notes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(133, 'credit-notes', 'view', 'View Credit Notes', 'view credit-notes', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(134, 'job-card', 'create', 'Create Job Card', 'create job-card', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(135, 'job-card', 'edit', 'Edit Job Card', 'edit job-card', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(136, 'job-card', 'delete', 'Delete Job Card', 'delete job-card', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(137, 'job-card', 'view', 'View Job Card', 'view job-card', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(138, 'production', 'create', 'Create Production', 'create production', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(139, 'production', 'edit', 'Edit Production', 'edit production', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(140, 'production', 'delete', 'Delete Production', 'delete production', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(141, 'production', 'view', 'View Production', 'view production', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(142, 'task-creation', 'create', 'Create Task Creation', 'create task-creation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(143, 'task-creation', 'edit', 'Edit Task Creation', 'edit task-creation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(144, 'task-creation', 'delete', 'Delete Task Creation', 'delete task-creation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(145, 'task-creation', 'view', 'View Task Creation', 'view task-creation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(146, 'task-assignment', 'create', 'Create Task Assignment', 'create task-assignment', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(147, 'task-assignment', 'edit', 'Edit Task Assignment', 'edit task-assignment', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(148, 'task-assignment', 'delete', 'Delete Task Assignment', 'delete task-assignment', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(149, 'task-assignment', 'view', 'View Task Assignment', 'view task-assignment', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(150, 'task-tracking-monitoring', 'create', 'Create Task Tracking Monitoring', 'create task-tracking-monitoring', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(151, 'task-tracking-monitoring', 'edit', 'Edit Task Tracking Monitoring', 'edit task-tracking-monitoring', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(152, 'task-tracking-monitoring', 'delete', 'Delete Task Tracking Monitoring', 'delete task-tracking-monitoring', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(153, 'task-tracking-monitoring', 'view', 'View Task Tracking Monitoring', 'view task-tracking-monitoring', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(154, 'task-status-updates', 'create', 'Create Task Status Updates', 'create task-status-updates', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(155, 'task-status-updates', 'edit', 'Edit Task Status Updates', 'edit task-status-updates', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(156, 'task-status-updates', 'delete', 'Delete Task Status Updates', 'delete task-status-updates', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(157, 'task-status-updates', 'view', 'View Task Status Updates', 'view task-status-updates', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(158, 'billing', 'create', 'Create Billing', 'create billing', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(159, 'billing', 'edit', 'Edit Billing', 'edit billing', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(160, 'billing', 'delete', 'Delete Billing', 'delete billing', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(161, 'billing', 'view', 'View Billing', 'view billing', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(162, 'manage-payments', 'create', 'Create Manage Payments', 'create manage-payments', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(163, 'manage-payments', 'edit', 'Edit Manage Payments', 'edit manage-payments', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(164, 'manage-payments', 'delete', 'Delete Manage Payments', 'delete manage-payments', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(165, 'manage-payments', 'view', 'View Manage Payments', 'view manage-payments', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(166, 'attendance', 'create', 'Create Attendance', 'create attendance', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(167, 'attendance', 'edit', 'Edit Attendance', 'edit attendance', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(168, 'attendance', 'delete', 'Delete Attendance', 'delete attendance', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(169, 'attendance', 'view', 'View Attendance', 'view attendance', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(170, 'manage-leaves', 'create', 'Create Manage Leaves', 'create manage-leaves', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(171, 'manage-leaves', 'edit', 'Edit Manage Leaves', 'edit manage-leaves', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(172, 'manage-leaves', 'delete', 'Delete Manage Leaves', 'delete manage-leaves', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(173, 'manage-leaves', 'view', 'View Manage Leaves', 'view manage-leaves', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(174, 'overtime-bonus', 'create', 'Create Overtime Bonus', 'create overtime-bonus', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(175, 'overtime-bonus', 'edit', 'Edit Overtime Bonus', 'edit overtime-bonus', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(176, 'overtime-bonus', 'delete', 'Delete Overtime Bonus', 'delete overtime-bonus', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(177, 'overtime-bonus', 'view', 'View Overtime Bonus', 'view overtime-bonus', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(178, 'salary-calculation', 'create', 'Create Salary Calculation', 'create salary-calculation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(179, 'salary-calculation', 'edit', 'Edit Salary Calculation', 'edit salary-calculation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(180, 'salary-calculation', 'delete', 'Delete Salary Calculation', 'delete salary-calculation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(181, 'salary-calculation', 'view', 'View Salary Calculation', 'view salary-calculation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(182, 'payslip-generation', 'create', 'Create Payslip Generation', 'create payslip-generation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(183, 'payslip-generation', 'edit', 'Edit Payslip Generation', 'edit payslip-generation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(184, 'payslip-generation', 'delete', 'Delete Payslip Generation', 'delete payslip-generation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(185, 'payslip-generation', 'view', 'View Payslip Generation', 'view payslip-generation', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(186, 'payroll-reports', 'create', 'Create Payroll Reports', 'create payroll-reports', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(187, 'payroll-reports', 'edit', 'Edit Payroll Reports', 'edit payroll-reports', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(188, 'payroll-reports', 'delete', 'Delete Payroll Reports', 'delete payroll-reports', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(189, 'payroll-reports', 'view', 'View Payroll Reports', 'view payroll-reports', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(190, 'document-repository', 'create', 'Create Document Repository', 'create document-repository', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(191, 'document-repository', 'edit', 'Edit Document Repository', 'edit document-repository', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(192, 'document-repository', 'delete', 'Delete Document Repository', 'delete document-repository', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(193, 'document-repository', 'view', 'View Document Repository', 'view document-repository', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(194, 'log', 'view', 'View Log', 'view log', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(195, 'backup-restore', 'view', 'View Backup Restore', 'view backup-restore', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(196, 'customer-report', 'view', 'View Customer Report', 'view customer-report', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(197, 'sale-report', 'view', 'View Sale Report', 'view sale-report', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(198, 'stock-report', 'view', 'View Stock Report', 'view stock-report', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(199, 'daily-production-report', 'view', 'View Daily Production Report', 'view daily-production-report', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(200, 'order-report', 'view', 'View Order Report', 'view order-report', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(201, 'employee-report', 'view', 'View Employee Report', 'view employee-report', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(202, 'settings', 'create', 'Create Settings', 'create settings', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(203, 'settings', 'edit', 'Edit Settings', 'edit settings', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(204, 'settings', 'delete', 'Delete Settings', 'delete settings', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51'),
(205, 'settings', 'view', 'View Settings', 'view settings', 'web', '2025-12-04 03:36:51', '2025-12-04 03:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE `places` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `place_name` varchar(255) NOT NULL,
  `place_type` enum('Residential','Commercial','Project Site') NOT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`id`, `state_id`, `city_id`, `place_name`, `place_type`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 1, 'Vilangudi', 'Residential', '9.9252', '78.1198', 'Active', '2025-11-29 04:16:47', '2025-12-05 00:15:04', NULL),
(2, 6, 4, 'RK Nagar', 'Residential', NULL, NULL, 'Active', '2025-12-05 00:33:31', '2025-12-05 00:33:55', '2025-12-05 00:33:55');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_commission_agents`
--

CREATE TABLE `purchase_commission_agents` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile_no` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `place_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_line_1` varchar(255) DEFAULT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `pan_no` varchar(255) DEFAULT NULL,
  `gst_no` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_commission_agents`
--

INSERT INTO `purchase_commission_agents` (`id`, `name`, `code`, `email`, `mobile_no`, `status`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `zipcode`, `contact_person_name`, `designation`, `phone_number`, `contact_email`, `pan_no`, `gst_no`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'Usha Devi', 'MAT -0009', 'ushadevi.saitech@gmail.com', '7410852369', 'Active', 5, 1, 1, 'Jaihindpuram', NULL, '625011', 'sai', 'Full Stack', '9856355632', 'admin@gympro.com', 'ABCDE1234U', '33AAPPS1234M1Z5', 'test', '2025-12-01 00:18:17', '2025-12-01 01:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `supplier_design_name` varchar(150) DEFAULT NULL,
  `color_id` bigint(20) UNSIGNED NOT NULL,
  `size_width` varchar(100) DEFAULT NULL,
  `uom_id` bigint(20) UNSIGNED NOT NULL,
  `fabric_type_id` bigint(20) UNSIGNED NOT NULL,
  `reference_image` varchar(255) DEFAULT NULL,
  `specification` varchar(255) DEFAULT NULL,
  `min_stock` int(11) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `raw_materials`
--

INSERT INTO `raw_materials` (`id`, `store_category_id`, `name`, `supplier_design_name`, `color_id`, `size_width`, `uom_id`, `fabric_type_id`, `reference_image`, `specification`, `min_stock`, `status`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'Cotton Poplin 60 GSM', 'Cotton Poplin', 3, '45', 2, 2, 'uploads/raw_materials/1764585589_hills.jpg', 'Test', 23, 'Active', 1, NULL, '2025-12-01 05:08:31', '2025-12-01 05:10:09');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Manager', 'web', 'Active', '2025-12-04 04:49:27', '2025-12-04 23:07:04', '2025-12-04 23:07:04'),
(8, 'Accountant', 'web', 'Active', '2025-12-04 23:14:21', '2025-12-04 23:14:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`id`, `permission_id`, `role_id`, `created_at`, `updated_at`) VALUES
(242, 1, 1, NULL, NULL),
(252, 1, 8, NULL, NULL),
(253, 2, 8, NULL, NULL),
(254, 3, 8, NULL, NULL),
(255, 5, 8, NULL, NULL),
(256, 4, 8, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_agents`
--

CREATE TABLE `sales_agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agent_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile_no` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `place_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `contact_phone_number` int(16) DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `pan_no` varchar(255) DEFAULT NULL,
  `gst_no` varchar(255) DEFAULT NULL,
  `commission_value` decimal(10,2) DEFAULT NULL,
  `sales_target` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_agents`
--

INSERT INTO `sales_agents` (`id`, `agent_type`, `name`, `code`, `email`, `mobile_no`, `status`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `zip_code`, `contact_person_name`, `designation`, `contact_phone_number`, `contact_email`, `pan_no`, `gst_no`, `commission_value`, `sales_target`, `created_at`, `updated_at`) VALUES
(2, 'Commission Agent', 'Usha Devi', 'INS012', 'ushadevi.saitech@gmail.com', '7410852369', 'Active', 5, 1, 1, 'Jaihindpuram', NULL, '625011', 'Muthamil', 'Php Developer', NULL, NULL, 'ABCDE1234U', '33AAPPS1234M1Z5', 4.00, 4.00, '2025-12-01 03:23:19', '2025-12-01 03:23:25');

-- --------------------------------------------------------

--
-- Table structure for table `service_providers`
--

CREATE TABLE `service_providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_type_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile_no` varchar(255) NOT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `service_rate` enum('Per Agent','Job Type') NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `place_id` bigint(20) UNSIGNED NOT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `pan_no` varchar(255) DEFAULT NULL,
  `gst_no` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_acc_no` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `payment_terms` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_providers`
--

INSERT INTO `service_providers` (`id`, `service_type_id`, `name`, `code`, `email`, `mobile_no`, `zip_code`, `website_url`, `service_rate`, `status`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `contact_person_name`, `designation`, `phone_number`, `contact_email`, `pan_no`, `gst_no`, `remarks`, `bank_name`, `bank_acc_no`, `ifsc_code`, `payment_terms`, `created_at`, `updated_at`) VALUES
(1, 1, 'Usha Devi', 'INS012', NULL, '7410852369', '625011', NULL, 'Per Agent', 'Active', 5, 1, 1, 'Jaihindpuram', NULL, 'sai', 'Php Developer', '9856355632', 'admin@gympro.com', 'ABCDE1234U', '33AAPPS1234M1Z5', 'tes', 'Indian Bank', '12323232424234', 'INDU0000018', 'test', '2025-12-01 02:51:10', '2025-12-01 02:53:01');

-- --------------------------------------------------------

--
-- Table structure for table `service_types`
--

CREATE TABLE `service_types` (
  `id` int(11) NOT NULL,
  `service_type_name` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_types`
--

INSERT INTO `service_types` (`id`, `service_type_name`, `created_at`, `updated_at`) VALUES
(1, 'Dyeing', '2025-12-01 08:10:45', '2025-12-01 08:10:45'),
(2, 'Printing', '2025-12-01 08:11:02', '2025-12-01 08:11:02'),
(3, 'Embroidery', '2025-12-01 08:11:12', '2025-12-01 08:11:12'),
(4, 'Washing', '2025-12-01 08:11:19', '2025-12-01 08:11:19'),
(5, 'Transport', '2025-12-01 08:11:27', '2025-12-01 08:11:27'),
(6, 'Logistics', '2025-12-01 08:11:35', '2025-12-01 08:11:35');

-- --------------------------------------------------------

--
-- Table structure for table `size_ratios`
--

CREATE TABLE `size_ratios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `size` text NOT NULL,
  `ratio` text NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `size_ratios`
--

INSERT INTO `size_ratios` (`id`, `size`, `ratio`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '38,40,42', '1,2,9,8', 'Active', NULL, '2025-11-29 05:59:31', '2025-12-05 05:51:53'),
(2, '38,40,42', '2,2,2', 'Active', '2025-12-05 05:57:20', '2025-12-05 05:57:12', '2025-12-05 05:57:20');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_code` varchar(10) NOT NULL,
  `state_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `state_code`, `state_name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 'TN', 'Tamilnadu', 'Active', '2025-11-29 01:28:53', '2025-11-29 01:29:01', NULL),
(6, 'AP', 'Andhra Pradesh', 'Active', '2025-12-04 22:26:32', '2025-12-04 22:32:39', NULL),
(7, 'sa', 'Kerala', 'Active', '2025-12-04 23:19:39', '2025-12-04 23:22:17', '2025-12-04 23:22:17'),
(8, 'KA', 'Kerala', 'Active', '2025-12-04 23:22:54', '2025-12-04 23:22:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `store_categories`
--

CREATE TABLE `store_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_categories`
--

INSERT INTO `store_categories` (`id`, `code`, `category_name`, `description`, `status`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '0010', 'TVS motors', 'test', 'Active', 1, '2025-12-01 04:20:06', '2025-12-01 03:43:28', '2025-12-01 04:20:06'),
(2, '001', 'Cotton', NULL, 'Active', 1, NULL, '2025-12-01 04:20:28', '2025-12-01 04:20:28'),
(3, '002', 'Fabric', NULL, 'Active', 1, NULL, '2025-12-01 04:20:39', '2025-12-01 04:20:39');

-- --------------------------------------------------------

--
-- Table structure for table `store_locations`
--

CREATE TABLE `store_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_location` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_locations`
--

INSERT INTO `store_locations` (`id`, `store_location`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'S3', 'Active', '2025-11-29 06:13:47', '2025-12-05 06:42:04', NULL),
(2, 'S5', 'Active', '2025-11-29 06:13:57', '2025-12-05 06:45:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `mobile_no` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `transport_name` varchar(255) DEFAULT NULL,
  `booking_area` varchar(255) DEFAULT NULL,
  `stores` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `place_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_line_1` varchar(255) DEFAULT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `address_line_3` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `contact_mobile_no` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `purchase_commission_agent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `commission_percentage` decimal(8,2) DEFAULT NULL,
  `gst_no` varchar(255) DEFAULT NULL,
  `tax_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pan_no` varchar(255) DEFAULT NULL,
  `ecc_no` varchar(255) DEFAULT NULL,
  `credit_limit` decimal(10,2) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `code`, `mobile_no`, `email`, `website_url`, `transport_name`, `booking_area`, `stores`, `status`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `address_line_3`, `zip_code`, `contact_person_name`, `designation`, `contact_mobile_no`, `contact_email`, `purchase_commission_agent_id`, `commission_percentage`, `gst_no`, `tax_id`, `pan_no`, `ecc_no`, `credit_limit`, `payment_terms`, `bank_name`, `branch`, `account_number`, `ifsc_code`, `created_at`, `updated_at`) VALUES
(1, 'Usha Devi', 'MAT', '7410852369', 'ushadevi.saitech@gmail.com', NULL, NULL, NULL, 'Fabric', 'Active', 5, 1, 1, 'Jaihindpuram', NULL, NULL, '625011', 'sdd', 'Employeer', '70012369852', 'admin@gympro.com', NULL, NULL, '27ABCDE1234F1Z5', 1, 'AAAAA1234A', 'ECC/DL/2023/089234', 12.00, NULL, 'State bank of india', 'Villapuram', '6598632153', 'CANA0000123', '2025-12-01 01:17:00', '2025-12-01 01:26:12');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `item_name`, `tax_rate`, `created_at`, `updated_at`, `deleted_at`, `status`) VALUES
(1, 'Mirror with Sashes', 4.00, '2025-12-05 07:05:03', '2025-12-05 07:05:47', '2025-12-05 07:05:47', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `uoms`
--

CREATE TABLE `uoms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uom_code` varchar(255) NOT NULL,
  `uom_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `uoms`
--

INSERT INTO `uoms` (`id`, `uom_code`, `uom_name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PCS', 'Pieces', NULL, 'Active', '2025-11-29 04:45:53', '2025-11-29 04:45:53', NULL),
(2, 'MTR', 'Meter', NULL, 'Active', '2025-11-29 04:51:35', '2025-11-29 04:52:26', NULL),
(3, 'KGS', 'Kilo grams', NULL, 'Active', '2025-11-29 05:47:13', '2025-12-05 04:07:10', NULL),
(4, 'sdf', 'sd', NULL, 'Active', '2025-12-05 02:49:16', '2025-12-05 02:49:19', '2025-12-05 02:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `emp_id` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `blood_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_phone` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `contact_person_phone` varchar(255) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `hra` decimal(10,2) DEFAULT NULL,
  `allowances` decimal(10,2) DEFAULT NULL,
  `deductions` decimal(10,2) DEFAULT NULL,
  `gross_salary` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `esi_document` varchar(255) DEFAULT NULL,
  `pf_document` varchar(255) DEFAULT NULL,
  `aadhaar_document` varchar(255) DEFAULT NULL,
  `pan_document` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `emp_id`, `department_id`, `role_id`, `blood_group_id`, `name`, `phone`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `date_of_joining`, `father_name`, `father_phone`, `country`, `state_id`, `city_id`, `address_line1`, `address_line2`, `zipcode`, `contact_person_name`, `contact_person_phone`, `basic_salary`, `hra`, `allowances`, `deductions`, `gross_salary`, `net_salary`, `account_number`, `bank_name`, `ifsc_code`, `profile_image`, `esi_document`, `pf_document`, `aadhaar_document`, `pan_document`, `status`, `deleted_at`) VALUES
(1, NULL, NULL, NULL, NULL, 'Admin', '', 'admin@gmail.com', NULL, '$2y$10$UbEh9rN0/yT7DA7cvaiKUusyD4tKg3VPArzNvzsLppJ.MQvJ/yLva', '43ppKHPnUdQG3AYEzPPeeq9BDqffu78uhDHX92cYydvmPDf2uVkZuk7ijc54', NULL, '2025-09-17 04:11:29', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', NULL),
(2, '102', 2, 2, 2, 'Usha Devi', '7894561230', 'ushadevi.saitech@gmail.com', NULL, '$2y$10$G28zLPp0mWL4l17BRU.aLu/VHD4Ht1VGw8UHBIbe5nBIynTHpfck6', NULL, '2025-12-04 06:41:36', '2025-12-04 07:28:03', NULL, NULL, NULL, NULL, 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '78954643203', 'indian', 'ICIC0000004', NULL, NULL, NULL, NULL, NULL, 'Active', NULL),
(4, '1002', 3, 1, 5, 'Shri', '7539864101', 'shri.saitech@gmail.com', NULL, '$2y$10$XtI4MKFzH92onDkXYmo4U.YRKIl3oEAu5mF4UKyO9OoI4.ovYK8UC', NULL, '2025-12-04 08:01:41', '2025-12-04 08:08:32', '2019-06-12', 'Moorthy', '7568894025', NULL, 5, 1, 'Jaihindpuram', NULL, '625011', 'Usha Devi', '6985471230', 10000.00, 1500.00, 250.00, 150.00, 9600.00, 9600.00, '78954643203', 'SBI', 'ICIC0000004', 'profile.png', 'esi_document.pdf', 'pf_document.pdf', 'aadhaar_document.pdf', 'pan_document.jpg', 'Active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `zone_name` varchar(255) NOT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_ids` text NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `zone_name`, `state_id`, `city_ids`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'South Zone', 5, '1,2', 'Active', '2025-11-29 05:49:55', '2025-12-05 04:45:34', NULL),
(2, 'test', 6, '4', 'Active', '2025-12-05 04:46:55', '2025-12-05 05:05:10', '2025-12-05 05:05:10'),
(3, 'Guruvayur', 8, '3', 'Active', '2025-12-05 05:04:44', '2025-12-05 05:04:47', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blood_groups`
--
ALTER TABLE `blood_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blood_groups_blood_grp_name_unique` (`blood_grp_name`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand_categories`
--
ALTER TABLE `brand_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brand_categories_code_unique` (`code`);

--
-- Indexes for table `charges`
--
ALTER TABLE `charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_code_unique` (`code`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_emp_id_unique` (`emp_id`);

--
-- Indexes for table `fabric_types`
--
ALTER TABLE `fabric_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `items_name_unique` (`name`),
  ADD UNIQUE KEY `items_code_unique` (`code`),
  ADD UNIQUE KEY `items_product_barcode_unique` (`product_barcode`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`);

--
-- Indexes for table `operation_stages`
--
ALTER TABLE `operation_stages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_commission_agents`
--
ALTER TABLE `purchase_commission_agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_commission_agents_code_unique` (`code`);

--
-- Indexes for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_agents`
--
ALTER TABLE `sales_agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_agents_code_unique` (`code`),
  ADD KEY `sales_agents_state_id_foreign` (`state_id`),
  ADD KEY `sales_agents_city_id_foreign` (`city_id`),
  ADD KEY `sales_agents_place_id_foreign` (`place_id`);

--
-- Indexes for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_types`
--
ALTER TABLE `service_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `size_ratios`
--
ALTER TABLE `size_ratios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `states_state_code_unique` (`state_code`);

--
-- Indexes for table `store_categories`
--
ALTER TABLE `store_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_categories_code_unique` (`code`);

--
-- Indexes for table `store_locations`
--
ALTER TABLE `store_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_code_unique` (`code`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uoms`
--
ALTER TABLE `uoms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uoms_uom_code_unique` (`uom_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blood_groups`
--
ALTER TABLE `blood_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `brand_categories`
--
ALTER TABLE `brand_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `charges`
--
ALTER TABLE `charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fabric_types`
--
ALTER TABLE `fabric_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `operation_stages`
--
ALTER TABLE `operation_stages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_commission_agents`
--
ALTER TABLE `purchase_commission_agents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT for table `sales_agents`
--
ALTER TABLE `sales_agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_providers`
--
ALTER TABLE `service_providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `service_types`
--
ALTER TABLE `service_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `size_ratios`
--
ALTER TABLE `size_ratios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `store_categories`
--
ALTER TABLE `store_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `store_locations`
--
ALTER TABLE `store_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uoms`
--
ALTER TABLE `uoms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sales_agents`
--
ALTER TABLE `sales_agents`
  ADD CONSTRAINT `sales_agents_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_agents_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_agents_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
