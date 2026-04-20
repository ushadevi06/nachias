-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2026 at 01:30 PM
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
-- Table structure for table `backups`
--

CREATE TABLE `backups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `backup_no` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `backup_type` enum('Full','Database Only','Files Only') NOT NULL DEFAULT 'Database Only',
  `file_size` varchar(255) DEFAULT NULL,
  `location` varchar(255) NOT NULL DEFAULT 'Local',
  `status` enum('Pending','Running','Success','Failed') NOT NULL DEFAULT 'Pending',
  `error_message` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billings`
--

CREATE TABLE `billings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_no` varchar(100) NOT NULL,
  `billing_type` varchar(255) DEFAULT NULL,
  `bill_date` date NOT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `billings`
--

INSERT INTO `billings` (`id`, `bill_no`, `billing_type`, `bill_date`, `amount`, `reason`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'BIL-001', 'Transport', '2026-03-03', 1500.00, 'Buy', 'Paid', '2026-03-03 11:13:10', '2026-03-03 11:16:57', NULL),
(2, 'BIL-002', 'Purchase', '2026-04-08', 200.00, 'test', 'Cancelled', '2026-04-08 04:31:51', '2026-04-08 04:34:33', NULL),
(3, 'Bill-003', NULL, '2026-04-08', 8500.00, 'testet', 'Paid', '2026-04-08 04:41:45', '2026-04-09 12:07:29', NULL),
(4, 'Bill-004', 'Job Work', '2026-04-09', 50000.00, NULL, 'Partially Paid', '2026-04-09 12:09:45', '2026-04-09 12:09:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blood_groups`
--

CREATE TABLE `blood_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `blood_grp_name` varchar(5) NOT NULL,
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
-- Table structure for table `bottom_cuts`
--

CREATE TABLE `bottom_cuts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bottom_cut_name` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bottom_cuts`
--

INSERT INTO `bottom_cuts` (`id`, `bottom_cut_name`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'AERO CUT', 'Active', 1, NULL, NULL, '2026-04-16 09:58:45', '2026-04-16 09:58:45'),
(2, 'SLACK CUT', 'Active', 1, NULL, NULL, '2026-04-16 09:58:53', '2026-04-16 09:58:53');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `brand_name`, `code`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'CASINO FORMAL', 'CF', 'Active', 1, NULL, NULL, '2026-04-16 10:41:51', '2026-04-16 10:41:51'),
(2, 'CASINO DEAL', 'CD', 'Active', 1, NULL, NULL, '2026-04-16 10:42:08', '2026-04-16 10:42:08'),
(3, 'CASINO BRAVO', 'CB', 'Active', 1, NULL, NULL, '2026-04-16 10:42:24', '2026-04-16 10:42:24'),
(4, 'CASINO FORMAL CORE', 'CFC', 'Active', 1, NULL, NULL, '2026-04-16 10:42:36', '2026-04-16 10:42:36');

-- --------------------------------------------------------

--
-- Table structure for table `brand_categories`
--

CREATE TABLE `brand_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brand_categories`
--

INSERT INTO `brand_categories` (`id`, `code`, `name`, `description`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '1001', 'formal', NULL, 'Active', 2, NULL, NULL, '2026-04-20 08:28:57', '2026-04-20 08:28:57');

-- --------------------------------------------------------

--
-- Table structure for table `charges`
--

CREATE TABLE `charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `charge_name` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `charges`
--

INSERT INTO `charges` (`id`, `charge_name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'BROKERAGE', 'Active', 1, NULL, '2026-04-16 09:50:04', '2026-04-16 09:50:04', NULL),
(2, 'INSURANCE CHARGE', 'Active', 1, NULL, '2026-04-16 09:50:19', '2026-04-16 09:50:19', NULL),
(3, 'PACKING CHARGE', 'Active', 1, NULL, '2026-04-16 09:50:29', '2026-04-16 09:50:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `city_name` varchar(100) NOT NULL,
  `city_code` varchar(100) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `state_id`, `city_name`, `city_code`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'MADURAI', 'MDU', 'Active', 1, NULL, '2026-04-16 09:33:49', '2026-04-16 09:33:49', NULL),
(2, 1, 'CHENNAI', NULL, 'Active', 1, NULL, '2026-04-16 09:34:03', '2026-04-16 09:34:03', NULL),
(3, 1, 'COIMBATORE', 'CBE', 'Active', 1, NULL, '2026-04-16 09:34:19', '2026-04-16 09:34:19', NULL),
(4, 4, 'KOCHI', NULL, 'Active', 1, NULL, '2026-04-16 09:34:38', '2026-04-16 09:34:38', NULL),
(5, 4, 'THIRUVANDHAPURAM', NULL, 'Active', 1, NULL, '2026-04-16 09:34:55', '2026-04-16 09:34:55', NULL),
(6, 5, 'BANGALORE', NULL, 'Active', 1, NULL, '2026-04-16 09:35:11', '2026-04-16 09:35:11', NULL),
(7, 2, 'VIZAG', NULL, 'Active', 1, NULL, '2026-04-16 09:35:23', '2026-04-16 09:35:23', NULL),
(8, 2, 'AMARAVATI', NULL, 'Active', 1, NULL, '2026-04-16 09:35:41', '2026-04-16 09:35:41', NULL),
(9, 5, 'MYSORE', NULL, 'Active', 1, NULL, '2026-04-16 09:36:08', '2026-04-16 09:36:08', NULL),
(10, 3, 'MUMBAI', NULL, 'Active', 1, NULL, '2026-04-16 10:10:35', '2026-04-16 10:10:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `collar_types`
--

CREATE TABLE `collar_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `collar_type_name` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collar_types`
--

INSERT INTO `collar_types` (`id`, `collar_type_name`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'REGULAR COLLAR SINGLE CANVAS & FOAM CANVAS', 'Active', 1, NULL, NULL, '2026-04-16 09:57:21', '2026-04-16 09:57:21'),
(2, 'TAILOR COLLAR SINGLE CANVAS & FOAM CANVAS', 'Active', 1, NULL, NULL, '2026-04-16 09:57:30', '2026-04-16 09:57:30'),
(3, 'WASHING COLLAR SINGLE CANVAS & FOAM CANVAS', 'Active', 1, NULL, NULL, '2026-04-16 09:57:36', '2026-04-16 09:57:36'),
(4, 'CHINESE COLLAR DOUBLE CANVAS', 'Active', 1, NULL, NULL, '2026-04-16 09:57:43', '2026-04-16 09:57:43');

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `id` int(11) NOT NULL,
  `color_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`id`, `color_name`, `description`, `created_at`, `updated_at`, `status`, `created_by`, `updated_by`, `deleted_at`) VALUES
(1, 'ENGLISH COLORS', NULL, '2026-04-16 15:16:26', '2026-04-16 15:16:26', 'Active', 1, NULL, NULL),
(2, 'BRIGHT COLORS', NULL, '2026-04-16 15:16:33', '2026-04-16 15:16:33', 'Active', 1, NULL, NULL),
(3, 'NEUTRAL COLORS', NULL, '2026-04-16 15:16:48', '2026-04-16 15:16:48', 'Active', 1, NULL, NULL),
(4, 'DIRTY COLOR', NULL, '2026-04-16 15:16:55', '2026-04-16 15:16:55', 'Active', 1, NULL, NULL),
(5, 'DARK COLORS', NULL, '2026-04-16 15:17:02', '2026-04-16 15:17:02', 'Active', 1, NULL, NULL),
(6, 'IVORY', NULL, '2026-04-16 15:17:19', '2026-04-16 15:17:19', 'Active', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `credit_notes`
--

CREATE TABLE `credit_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `note_no` varchar(50) NOT NULL,
  `note_date` date NOT NULL,
  `sales_invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `other_state` tinyint(1) NOT NULL DEFAULT 0,
  `igst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `igst` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cgst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `cgst` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sgst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sgst` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sub_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `round_off` decimal(15,2) NOT NULL DEFAULT 0.00,
  `round_off_type` varchar(255) NOT NULL DEFAULT 'Add',
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `reference_doc` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `credit_notes`
--

INSERT INTO `credit_notes` (`id`, `note_no`, `note_date`, `sales_invoice_id`, `customer_id`, `reason`, `other_state`, `igst_percent`, `igst`, `cgst_percent`, `cgst`, `sgst_percent`, `sgst`, `sub_total`, `tax_amount`, `round_off`, `round_off_type`, `grand_total`, `remarks`, `reference_doc`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CN-001', '2026-06-20', 1, 1, 'Return', 1, 18.00, 32.83, 0.00, 0.00, 0.00, 0.00, 182.39, 32.83, 0.22, 'Less', 215.00, NULL, 'credit_note_1773993749.jpg', 'Approved', 1, NULL, '2026-03-19 06:38:49', '2026-03-20 08:02:29', NULL),
(2, 'CN-002', '2026-06-20', 1, 1, NULL, 1, 18.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'Add', 0.00, NULL, NULL, 'Draft', 1, NULL, '2026-03-26 05:53:38', '2026-03-26 05:54:44', '2026-03-26 05:54:44'),
(3, 'CN-003', '2026-06-20', 1, 1, NULL, 1, 18.00, 114.30, 0.00, 0.00, 0.00, 0.00, 635.00, 114.30, 0.00, 'Add', 749.30, NULL, NULL, 'Draft', 1, NULL, '2026-03-26 06:05:42', '2026-03-26 06:05:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `credit_note_items`
--

CREATE TABLE `credit_note_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credit_note_id` bigint(20) UNSIGNED NOT NULL,
  `sales_invoice_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `size` varchar(20) DEFAULT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `sleeve_type` varchar(255) DEFAULT NULL,
  `mrp` decimal(15,2) DEFAULT NULL,
  `uom_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rate` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cuff_types`
--

CREATE TABLE `cuff_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cuff_type_name` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cuff_types`
--

INSERT INTO `cuff_types` (`id`, `cuff_type_name`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ROUND', 'Active', 1, NULL, NULL, '2026-04-16 09:57:59', '2026-04-16 09:57:59'),
(2, 'CORNER CROSS', 'Active', 1, NULL, NULL, '2026-04-16 09:58:07', '2026-04-16 09:58:07');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` enum('Retailer','Wholesaler') NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `mobile_no` varchar(15) NOT NULL,
  `email` varchar(128) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `transport_name` varchar(100) DEFAULT NULL,
  `booking_office` varchar(100) DEFAULT NULL,
  `zone_id` bigint(20) UNSIGNED NOT NULL,
  `store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `place_id` bigint(20) UNSIGNED NOT NULL,
  `address_line_1` varchar(150) NOT NULL,
  `address_line_2` varchar(150) DEFAULT NULL,
  `address_line_3` varchar(150) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `contact_mobile_no` varchar(15) DEFAULT NULL,
  `contact_email` varchar(128) DEFAULT NULL,
  `tax_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gst_no` varchar(15) DEFAULT NULL,
  `pan_no` varchar(10) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sales_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `box_discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `bank_name` varchar(100) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `category`, `name`, `code`, `mobile_no`, `email`, `website_url`, `transport_name`, `booking_office`, `zone_id`, `store_id`, `status`, `created_by`, `updated_by`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `address_line_3`, `zip_code`, `contact_person_name`, `designation`, `contact_mobile_no`, `contact_email`, `tax_type_id`, `gst_no`, `pan_no`, `payment_terms`, `credit_limit`, `sales_discount`, `box_discount`, `bank_name`, `branch`, `account_number`, `ifsc_code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Wholesaler', 'AK AHAMED', '1001', '9876987650', NULL, NULL, NULL, NULL, 1, NULL, 'Active', 1, NULL, 1, 1, 3, '25, NAVBATHAKANA STREET', NULL, NULL, '625011', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-16 10:06:15', '2026-04-16 10:06:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `debit_notes`
--

CREATE TABLE `debit_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `debit_note_no` varchar(50) NOT NULL,
  `debit_note_date` date NOT NULL,
  `purchase_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `other_state` enum('Y','N') NOT NULL DEFAULT 'N',
  `reason` varchar(255) DEFAULT NULL,
  `sub_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_charges` decimal(15,2) DEFAULT NULL,
  `igst_percent` decimal(5,2) DEFAULT NULL,
  `cgst_percent` decimal(5,2) DEFAULT NULL,
  `sgst_percent` decimal(5,2) DEFAULT NULL,
  `tax_amount` decimal(15,2) DEFAULT NULL,
  `round_off_type` enum('Add','Less') NOT NULL DEFAULT 'Add',
  `round_off` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `reference_document` varchar(255) DEFAULT NULL,
  `status` enum('Draft','Approved','Cancelled') NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debit_note_charges`
--

CREATE TABLE `debit_note_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `debit_note_id` bigint(20) UNSIGNED NOT NULL,
  `charge_id` bigint(20) UNSIGNED NOT NULL,
  `charge_name` varchar(255) DEFAULT NULL,
  `charge_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `debit_note_charges`
--

INSERT INTO `debit_note_charges` (`id`, `debit_note_id`, `charge_id`, `charge_name`, `charge_amount`, `tax_type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'TCS', 10.00, 'Post-GST', '2026-04-02 09:14:48', '2026-04-02 09:14:48', NULL),
(2, 1, 2, 'Courier Charge', 5.00, 'Pre-GST', '2026-04-02 09:14:48', '2026-04-02 09:14:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `debit_note_items`
--

CREATE TABLE `debit_note_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `debit_note_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_invoice_item_id` bigint(20) UNSIGNED NOT NULL,
  `raw_material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `uom_id` bigint(20) UNSIGNED NOT NULL,
  `rate` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Cutting', 'Active', 1, NULL, '2026-04-16 09:27:39', '2026-04-16 09:27:39', NULL),
(2, 'Stitching', 'Active', 1, NULL, '2026-04-16 09:28:18', '2026-04-16 09:28:40', NULL),
(3, 'Covering', 'Inactive', 1, NULL, '2026-04-16 09:28:54', '2026-04-16 09:28:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `document_repositories`
--

CREATE TABLE `document_repositories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_name` varchar(100) NOT NULL,
  `document_type` enum('Certification','HR','Compliance','Policy') NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `validity_date` date DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `file` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_repositories`
--

INSERT INTO `document_repositories` (`id`, `document_name`, `document_type`, `department_id`, `validity_date`, `remarks`, `file`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Purchase Agreement', 'Certification', 1, '2026-04-12', NULL, '1773212853.pdf', 'Active', 1, 1, NULL, '2026-03-11 07:07:33', '2026-03-11 07:07:52'),
(3, 'sdfsf', 'Certification', 1, NULL, 'sfsf', '1775550459.docx', 'Active', 1, 1, NULL, '2026-04-07 08:27:14', '2026-04-07 08:27:39'),
(4, 'test est test', 'HR', 1, NULL, NULL, '1775550950.jpg', 'Active', 1, 1, NULL, '2026-04-07 08:35:50', '2026-04-07 08:35:59');

-- --------------------------------------------------------

--
-- Table structure for table `fabric_sizes`
--

CREATE TABLE `fabric_sizes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `width` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fabric_sizes`
--

INSERT INTO `fabric_sizes` (`id`, `width`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '58', 'Active', 1, NULL, NULL, '2026-04-16 09:49:42', '2026-04-16 09:49:42'),
(2, '36', 'Active', 1, NULL, NULL, '2026-04-16 09:49:49', '2026-04-16 09:49:49');

-- --------------------------------------------------------

--
-- Table structure for table `fabric_types`
--

CREATE TABLE `fabric_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fabric_type` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fabric_types`
--

INSERT INTO `fabric_types` (`id`, `fabric_type`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'COTTON', 'Active', '2026-04-16 09:49:04', '2026-04-16 09:49:04', NULL, 1, NULL),
(2, 'POLYESTER & BLENDS', 'Active', '2026-04-16 09:49:21', '2026-04-16 09:49:21', NULL, 1, NULL),
(3, 'lINEN', 'Active', '2026-04-16 09:49:30', '2026-04-16 09:49:30', NULL, 1, NULL);

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
-- Table structure for table `fits`
--

CREATE TABLE `fits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fit_name` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fits`
--

INSERT INTO `fits` (`id`, `fit_name`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'RUGULAR FIT', 'Active', 1, NULL, NULL, '2026-04-16 09:55:47', '2026-04-16 09:55:47'),
(2, 'TAILOR FIT', 'Active', 1, NULL, NULL, '2026-04-16 09:55:54', '2026-04-16 09:55:54'),
(3, 'SLIM FIT', 'Active', 1, NULL, NULL, '2026-04-16 09:56:01', '2026-04-16 09:56:01'),
(4, '28 MM AMERICAN PAATI', 'Active', 1, NULL, '2026-04-16 09:56:24', '2026-04-16 09:56:14', '2026-04-16 09:56:24');

-- --------------------------------------------------------

--
-- Table structure for table `grn_entries`
--

CREATE TABLE `grn_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grn_number` varchar(100) NOT NULL,
  `grn_date` date NOT NULL,
  `purchase_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` int(10) UNSIGNED NOT NULL,
  `supplier_invoice_date` date NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grn_entries`
--

INSERT INTO `grn_entries` (`id`, `grn_number`, `grn_date`, `purchase_invoice_id`, `supplier_id`, `supplier_invoice_date`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'GRN001', '2026-04-16', 3, 3, '2026-04-16', 'Received', 1, NULL, '2026-04-16 11:42:40', '2026-04-16 11:42:40', NULL),
(2, 'GRN002', '2026-04-16', 1, 3, '2026-04-16', 'Received', 1, 1, '2026-04-16 12:00:38', '2026-04-16 12:35:00', NULL),
(3, 'GRN003', '2026-04-16', 2, 2, '2026-04-16', 'Received', 1, 1, '2026-04-16 12:01:07', '2026-04-16 12:35:24', NULL),
(4, 'GRN004', '2026-04-17', 4, 2, '2026-04-17', 'Received', 1, 1, '2026-04-17 05:31:53', '2026-04-17 05:33:16', NULL),
(5, 'GRN005', '2026-04-17', 5, 3, '2026-04-17', 'Received', 1, NULL, '2026-04-17 05:33:39', '2026-04-17 05:33:39', NULL),
(6, 'GRN006', '2026-04-17', 6, 2, '2026-04-17', 'Received', 1, NULL, '2026-04-17 06:04:55', '2026-04-17 06:04:55', NULL),
(7, 'GRN007', '2026-04-17', 7, 3, '2026-04-17', 'Received', 1, NULL, '2026-04-17 06:05:17', '2026-04-17 06:05:17', NULL),
(8, 'GRN008', '2026-04-17', 8, 3, '2026-04-17', 'Received', 1, NULL, '2026-04-17 06:42:13', '2026-04-17 06:42:13', NULL),
(9, 'GRN009', '2026-04-17', 9, 2, '2026-04-17', 'Received', 1, NULL, '2026-04-17 06:42:40', '2026-04-17 06:42:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `grn_entry_items`
--

CREATE TABLE `grn_entry_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grn_entry_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_invoice_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `art_no` varchar(50) DEFAULT NULL,
  `fabric_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `color_id` bigint(20) UNSIGNED DEFAULT NULL,
  `qty_ordered` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_received` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_accepted` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_rejected` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_balanced` decimal(15,2) NOT NULL DEFAULT 0.00,
  `rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `quality_check_status` enum('Pass','Fail','Hold') DEFAULT NULL,
  `store_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grn_entry_items`
--

INSERT INTO `grn_entry_items` (`id`, `grn_entry_id`, `purchase_invoice_item_id`, `art_no`, `fabric_type_id`, `color_id`, `qty_ordered`, `qty_received`, `qty_accepted`, `qty_rejected`, `qty_balanced`, `rate`, `amount`, `quality_check_status`, `store_location_id`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 3, 'CF-34343', 1, 1, 150.00, 150.00, 150.00, 0.00, 0.00, 96.00, 14400.00, 'Pass', 3, NULL, '2026-04-16 11:42:40', '2026-04-16 11:42:40', NULL),
(2, 1, 4, 'CF-34344', 3, 2, 160.00, 160.00, 160.00, 0.00, 0.00, 99.00, 15840.00, 'Pass', 1, NULL, '2026-04-16 11:42:40', '2026-04-16 11:42:40', NULL),
(3, 2, 1, 'CF-34345', NULL, 6, 150.00, 50.00, 50.00, 0.00, 0.00, 10.00, 500.00, 'Pass', 3, NULL, '2026-04-16 12:00:38', '2026-04-16 12:35:00', NULL),
(4, 2, 1, 'CF-34346', NULL, 3, 150.00, 100.00, 100.00, 0.00, 0.00, 10.00, 1000.00, 'Pass', 2, NULL, '2026-04-16 12:00:38', '2026-04-16 12:35:00', NULL),
(5, 3, 2, 'CF-0909', 2, 2, 150.00, 50.00, 50.00, 0.00, 0.00, 99.00, 4950.00, 'Pass', 3, NULL, '2026-04-16 12:01:07', '2026-04-16 12:35:24', NULL),
(6, 3, 2, 'CF-09093', 2, 4, 150.00, 100.00, 100.00, 0.00, 0.00, 99.00, 9900.00, 'Pass', 3, NULL, '2026-04-16 12:01:07', '2026-04-16 12:35:24', NULL),
(7, 4, 5, 'CF-34934', 1, 1, 150.00, 50.00, 50.00, 0.00, 0.00, 90.00, 4500.00, 'Pass', 3, NULL, '2026-04-17 05:31:53', '2026-04-17 05:33:16', NULL),
(8, 4, 5, 'CF-34935', 1, 1, 150.00, 100.00, 100.00, 0.00, 0.00, 90.00, 9000.00, 'Pass', 3, NULL, '2026-04-17 05:33:16', '2026-04-17 05:33:16', NULL),
(9, 5, 6, 'CF-34936', NULL, NULL, 280.00, 280.00, 280.00, 0.00, 0.00, 8.00, 2240.00, 'Pass', 3, NULL, '2026-04-17 05:33:39', '2026-04-17 05:33:39', NULL),
(10, 6, 7, 'CF-03489', 1, NULL, 150.00, 50.00, 50.00, 0.00, 0.00, 96.00, 4800.00, 'Pass', 3, NULL, '2026-04-17 06:04:55', '2026-04-17 06:04:55', NULL),
(11, 6, 7, 'CF-03480', 1, NULL, 150.00, 100.00, 100.00, 0.00, 0.00, 96.00, 9600.00, 'Pass', 3, NULL, '2026-04-17 06:04:55', '2026-04-17 06:04:55', NULL),
(12, 7, 8, 'CF-34937', NULL, NULL, 250.00, 250.00, 250.00, 0.00, 0.00, 5.00, 1250.00, 'Pass', 3, NULL, '2026-04-17 06:05:17', '2026-04-17 06:05:17', NULL),
(13, 8, 9, 'CB-1001', NULL, NULL, 160.00, 160.00, 160.00, 0.00, 0.00, 5.00, 800.00, 'Pass', 3, NULL, '2026-04-17 06:42:13', '2026-04-17 06:42:13', NULL),
(14, 9, 10, 'CF-349300', 1, 2, 200.00, 100.00, 100.00, 0.00, 0.00, 100.00, 10000.00, 'Pass', 3, NULL, '2026-04-17 06:42:40', '2026-04-17 06:42:40', NULL),
(15, 9, 10, 'CF-349301', 1, 2, 200.00, 100.00, 100.00, 0.00, 0.00, 100.00, 10000.00, 'Pass', 3, NULL, '2026-04-17 06:42:40', '2026-04-17 06:42:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `grn_entry_item_variants`
--

CREATE TABLE `grn_entry_item_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grn_entry_item_id` bigint(20) UNSIGNED NOT NULL,
  `color_id` int(11) NOT NULL,
  `qty_received` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grn_entry_item_variants`
--

INSERT INTO `grn_entry_item_variants` (`id`, `grn_entry_item_id`, `color_id`, `qty_received`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 6, 50.00, '2026-04-16 12:35:00', '2026-04-16 12:35:00', NULL),
(2, 4, 3, 100.00, '2026-04-16 12:35:00', '2026-04-16 12:35:37', '2026-04-16 12:35:37'),
(3, 5, 2, 50.00, '2026-04-16 12:35:24', '2026-04-16 12:35:24', NULL),
(4, 6, 4, 50.00, '2026-04-16 12:35:24', '2026-04-16 12:35:24', NULL),
(5, 4, 3, 100.00, '2026-04-16 12:35:37', '2026-04-16 12:35:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_category_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `style_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fabric_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `design_art_no` varchar(50) DEFAULT NULL,
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
  `mrp` decimal(15,2) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `brand_category_id`, `brand_id`, `name`, `code`, `style_id`, `fabric_type_id`, `design_art_no`, `uom_id`, `size_ratio_id`, `color_id`, `product_barcode`, `standard_costing`, `store_category_id`, `related_materials`, `operation_stages`, `service_providers`, `wholesale_price`, `retail_price`, `export_price`, `mrp`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 'Men’s Formal Cotton Shirt', '1000', NULL, NULL, NULL, 3, NULL, '', NULL, NULL, NULL, '{\"1\":{\"category_id\":\"1\",\"category_name\":\"Fabric(FBC)\",\"material_id\":\"1\",\"material_name\":\"COTTON FABRIC(1001)\"}}', NULL, '{\"cutting\":null,\"stitching ready\":null,\"stitching assemble\":null,\"kaja button\":null,\"trimming & checking\":null,\"ironing & packing\":null}', NULL, NULL, NULL, NULL, 'Active', 2, NULL, '2026-04-20 08:32:05', '2026-04-20 08:32:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_card_cutting_size_ratios`
--

CREATE TABLE `job_card_cutting_size_ratios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_entry_id` bigint(20) UNSIGNED NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `ratio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `qty_fs` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_hs` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_cutting_size_ratios`
--

INSERT INTO `job_card_cutting_size_ratios` (`id`, `job_card_entry_id`, `size`, `ratio`, `qty_fs`, `qty_hs`, `total_qty`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '36', 0.00, 6.00, 8.00, 14.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(2, 1, '38', 0.00, 6.00, 8.00, 14.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(3, 1, '40', 0.00, 6.00, 8.00, 14.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(4, 1, '42', 0.00, 16.00, 8.00, 24.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(5, 1, '44', 0.00, 4.00, 4.00, 8.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(6, 2, '36', 0.00, 10.00, 0.00, 10.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(7, 2, '38', 0.00, 15.00, 5.00, 20.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(8, 2, '40', 0.00, 15.00, 5.00, 20.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(9, 2, '42', 0.00, 5.00, 5.00, 10.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(10, 2, '44', 0.00, 8.00, 3.00, 11.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(26, 3, '36', 0.00, 12.00, 0.00, 12.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(27, 3, '38', 0.00, 25.00, 15.00, 40.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(28, 3, '40', 0.00, 25.00, 15.00, 40.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(29, 3, '42', 0.00, 25.00, 0.00, 25.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(30, 3, '44', 0.00, 15.00, 0.00, 15.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(36, 4, '36', 0.00, 5.00, 5.00, 10.00, '2026-04-20 09:41:54', '2026-04-20 09:41:54', NULL),
(37, 4, '38', 0.00, 15.00, 6.00, 21.00, '2026-04-20 09:41:54', '2026-04-20 09:41:54', NULL),
(38, 4, '40', 0.00, 15.00, 6.00, 21.00, '2026-04-20 09:41:54', '2026-04-20 09:41:54', NULL),
(39, 4, '42', 0.00, 10.00, 6.00, 16.00, '2026-04-20 09:41:54', '2026-04-20 09:41:54', NULL),
(40, 4, '44', 0.00, 15.00, 3.00, 18.00, '2026-04-20 09:41:54', '2026-04-20 09:41:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_card_entries`
--

CREATE TABLE `job_card_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_no` varchar(100) NOT NULL,
  `job_card_type` varchar(255) DEFAULT 'Regular',
  `reference_no` varchar(255) DEFAULT NULL,
  `purchase_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_entry_ids` text DEFAULT NULL,
  `service_provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `issue_store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receipt_store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patti_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `collar_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cuff_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pocket_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bottom_cut_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fabric_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `season_id` bigint(20) UNSIGNED DEFAULT NULL,
  `process_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `size_ratio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_card_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `washing` enum('Yes','No') NOT NULL DEFAULT 'No',
  `width` varchar(128) DEFAULT NULL,
  `fs_qty` decimal(15,2) DEFAULT NULL,
  `hs_qty` decimal(15,2) DEFAULT NULL,
  `ex_1_label` varchar(255) DEFAULT NULL,
  `ex_2_label` varchar(255) DEFAULT NULL,
  `price_fs` decimal(10,2) DEFAULT NULL,
  `price_hs` decimal(10,2) DEFAULT NULL,
  `total_qty_fs` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_qty_hs` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `average` decimal(8,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sleeve_instances` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_entries`
--

INSERT INTO `job_card_entries` (`id`, `job_card_no`, `job_card_type`, `reference_no`, `purchase_order_id`, `stock_entry_ids`, `service_provider_id`, `issue_store_id`, `receipt_store_id`, `fit_id`, `patti_type_id`, `collar_type_id`, `cuff_type_id`, `pocket_type_id`, `bottom_cut_id`, `brand_id`, `brand_category_id`, `fabric_type_id`, `item_id`, `season_id`, `process_group_id`, `size_ratio_id`, `job_card_date`, `delivery_date`, `washing`, `width`, `fs_qty`, `hs_qty`, `ex_1_label`, `ex_2_label`, `price_fs`, `price_hs`, `total_qty_fs`, `total_qty_hs`, `grand_total_qty`, `average`, `remarks`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `sleeve_instances`) VALUES
(1, 'JC001', 'Regular', 'JC001', NULL, '\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\"', 1, 1, 1, 2, 3, 3, 2, 2, 2, 1, NULL, 2, NULL, 1, 2, NULL, '2026-04-16', '2026-05-09', 'No', NULL, NULL, NULL, NULL, NULL, 1.00, 0.00, 135.00, 120.00, 255.00, 8.66, NULL, 'Production In Progress', 1, NULL, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL, '{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}}'),
(2, 'JC002', 'Regular', 'JC002', NULL, '\"[\\\"4::art|CF-34935\\\",\\\"4::art|CF-34934\\\",\\\"3::art|CF-09093\\\",\\\"2::art|CF-34346\\\"]\"', 1, 1, 1, 2, 3, 4, 2, 2, 2, 3, NULL, 1, NULL, NULL, 2, NULL, '2026-04-17', '2026-05-09', 'No', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 135.00, 75.00, 210.00, 9.65, NULL, 'Production In Progress', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL, '{\"instances\":[{\"id\":1776405547414.9329,\"type\":\"fs\"},{\"id\":1776405547623.6736,\"type\":\"fs\"},{\"id\":1776405547991.6877,\"type\":\"hs\"}],\"values\":{\"1776405547414.9329\":{\"36\":\"0\",\"38\":\"5\",\"40\":\"5\",\"42\":\"5\",\"44\":\"5\"},\"1776405547623.6736\":{\"36\":\"10\",\"38\":\"10\",\"40\":\"10\",\"42\":\"0\",\"44\":\"3\"},\"1776405547991.6877\":{\"36\":\"0\",\"38\":\"5\",\"40\":\"5\",\"42\":\"5\",\"44\":\"3\"}}}'),
(3, 'JC003', 'Regular', 'JC003', NULL, '\"[\\\"7::art|CF-03489\\\",\\\"7::art|CF-03480\\\",\\\"4::art|CF-34934\\\",\\\"6::art|CF-34937\\\"]\"', 1, 1, 3, 2, 2, 4, 2, 1, 2, 3, NULL, 2, NULL, 1, 2, NULL, '2026-04-17', '2026-05-09', 'No', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 180.00, 150.00, 330.00, 10.86, NULL, 'Production In Progress', 1, NULL, '2026-04-17 06:33:06', '2026-04-17 08:55:30', NULL, '{\"instances\":[{\"id\":1776407422319.4292,\"type\":\"fs\"},{\"id\":1776407422492.016,\"type\":\"fs\"},{\"id\":1776407423230.0393,\"type\":\"hs\"}],\"values\":{\"1776407422319.4292\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"15\"},\"1776407422492.016\":{\"36\":\"12\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"0\"},\"1776407423230.0393\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"0\",\"44\":\"0\"}}}'),
(4, 'JC004', 'Regular', 'JC004', NULL, '\"[\\\"4::art|CF-34935\\\",\\\"8::art|CF-349301\\\"]\"', 1, 1, 3, 1, 2, 2, 1, 2, 1, 1, NULL, 1, NULL, 1, 2, NULL, '2026-04-20', '2026-05-09', 'No', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 76.00, 78.00, 154.00, 1.52, NULL, 'Production In Progress', 1, NULL, '2026-04-20 04:52:33', '2026-04-20 09:41:55', NULL, '{\"instances\":[{\"id\":1776660676650.4226,\"type\":\"fs\"},{\"id\":1776660676825.516,\"type\":\"fs\"},{\"id\":1776660677210.1726,\"type\":\"hs\"}],\"values\":{\"1776660676650.4226\":{\"36\":\"0\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"10\"},\"1776660676825.516\":{\"36\":\"5\",\"38\":\"5\",\"40\":\"5\",\"42\":\"0\",\"44\":\"5\"},\"1776660677210.1726\":{\"36\":\"5\",\"38\":\"6\",\"40\":\"6\",\"42\":\"6\",\"44\":\"3\"}}}');

-- --------------------------------------------------------

--
-- Table structure for table `job_card_fabric_consumptions`
--

CREATE TABLE `job_card_fabric_consumptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_fabric_detail_id` bigint(20) UNSIGNED NOT NULL,
  `size` varchar(255) NOT NULL,
  `fs_cons` decimal(10,3) DEFAULT NULL,
  `hs_cons` decimal(10,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_fabric_consumptions`
--

INSERT INTO `job_card_fabric_consumptions` (`id`, `job_card_fabric_detail_id`, `size`, `fs_cons`, `hs_cons`, `created_at`, `updated_at`) VALUES
(1, 4, '36', 8.000, 6.000, '2026-04-16 12:06:50', '2026-04-20 09:42:44'),
(2, 4, '38', 8.000, 6.000, '2026-04-16 12:06:50', '2026-04-20 09:42:44'),
(3, 4, '40', 8.000, 6.000, '2026-04-16 12:06:50', '2026-04-20 09:42:44'),
(4, 4, '42', 8.000, 6.000, '2026-04-16 12:06:50', '2026-04-20 09:42:44'),
(5, 4, '44', 8.000, 6.000, '2026-04-16 12:06:50', '2026-04-20 09:42:44'),
(6, 8, '36', 8.000, 6.000, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(7, 8, '38', 8.000, 6.000, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(8, 8, '40', 8.000, 6.000, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(9, 8, '42', 8.000, 6.000, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(10, 8, '44', 8.000, 6.000, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(26, 24, '36', 10.000, 8.000, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(27, 24, '38', 10.000, 8.000, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(28, 24, '40', 10.000, 8.000, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(29, 24, '42', 10.000, 8.000, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(30, 24, '44', 10.000, 8.000, '2026-04-17 08:55:30', '2026-04-17 08:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `job_card_fabric_details`
--

CREATE TABLE `job_card_fabric_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_entry_id` bigint(20) UNSIGNED NOT NULL,
  `art_no` varchar(255) DEFAULT NULL,
  `width` varchar(255) DEFAULT NULL,
  `mtr` varchar(255) DEFAULT NULL,
  `in_out` varchar(255) DEFAULT NULL,
  `n_patti` varchar(255) DEFAULT NULL,
  `fs_qty` decimal(10,2) DEFAULT NULL,
  `hs_qty` decimal(10,2) DEFAULT NULL,
  `total_qty` decimal(15,3) DEFAULT NULL,
  `used_qty` decimal(15,3) DEFAULT NULL,
  `remaining_qty` decimal(15,3) DEFAULT NULL,
  `row_total` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_fabric_details`
--

INSERT INTO `job_card_fabric_details` (`id`, `job_card_entry_id`, `art_no`, `width`, `mtr`, `in_out`, `n_patti`, `fs_qty`, `hs_qty`, `total_qty`, `used_qty`, `remaining_qty`, `row_total`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'CF-0909', '58', '99.44', 'NO', 'WHITE', 45.00, 40.00, 99.440, 99.440, 0.000, 85, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(2, 1, 'CF-34343', '58', '150.00', 'NO', 'WHITE', 45.00, 40.00, 150.000, 150.000, 0.000, 85, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(3, 1, 'CF-34344', '58', '160.00', 'NO', 'WHITE', 45.00, 40.00, 160.000, 160.000, 0.000, 85, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(4, 1, 'CF-34345', NULL, '1800.00', 'NO', 'WHITE', 1080.00, 720.00, 1800.000, 1800.000, 0.000, 1800, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(5, 2, 'CF-34935', NULL, '109.33', 'NO', 'WHITE', 45.00, 25.00, 109.330, 109.330, 4.040, 70, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(6, 2, 'CF-34934', NULL, '109.33', 'NO', 'WHITE', 45.00, 25.00, 109.330, 109.330, 4.050, 70, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(7, 2, 'CF-09093', NULL, '109.33', 'NO', 'WHITE', 45.00, 25.00, 109.330, 109.330, 4.040, 70, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(8, 2, 'CF-34346', NULL, '1698.00', 'NO', 'WHITE', 792.00, 450.00, 1698.000, 1698.000, 0.000, 1530, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(21, 3, 'CF-03489', '58', '242.56', 'NO', 'WHITE', 60.00, 50.00, 242.560, 242.560, 0.000, 110, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(22, 3, 'CF-03480', '58', '171.28', 'NO', 'WHITE', 60.00, 50.00, 171.280, 171.280, 0.000, 110, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(23, 3, 'CF-34934', '58', '171.28', 'NO', 'WHITE', 60.00, 50.00, 171.280, 171.280, 0.000, 110, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(24, 3, 'CF-34937', NULL, '3000.00', 'NO', 'WHITE', 1800.00, 1200.00, 3000.000, 3000.000, 0.000, 3000, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(27, 4, 'CF-34935', '58', '113.37', 'NO', 'WHITE', 38.00, 39.00, 113.370, 113.370, 0.000, 77, '2026-04-20 09:41:54', '2026-04-20 09:41:55', NULL),
(28, 4, 'CF-349301', '58', '120.68', 'NO', 'WHITE', 38.00, 39.00, 120.680, 120.680, 0.000, 77, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_card_images`
--

CREATE TABLE `job_card_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_entry_id` bigint(20) UNSIGNED NOT NULL,
  `art_no` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_card_issue_items`
--

CREATE TABLE `job_card_issue_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_entry_id` bigint(20) UNSIGNED NOT NULL,
  `job_card_article_matrix_id` bigint(20) UNSIGNED NOT NULL,
  `stock_entry_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `raw_material_id` bigint(20) UNSIGNED DEFAULT NULL,
  `qty_issue` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_adjusted` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_wastage` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_used` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `average` decimal(15,2) NOT NULL DEFAULT 0.00,
  `produced_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cost_per_pc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_issue_items`
--

INSERT INTO `job_card_issue_items` (`id`, `job_card_entry_id`, `job_card_article_matrix_id`, `stock_entry_item_id`, `raw_material_id`, `qty_issue`, `qty_adjusted`, `qty_wastage`, `qty_used`, `bit`, `balance`, `average`, `produced_qty`, `unit_price`, `total_cost`, `cost_per_pc`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 5, 1, 85.00, 0.00, 0.00, 85.00, 0.00, 0.00, 0.00, 85.00, 99.00, 8415.00, 99.00, 1, 1, '2026-04-16 12:09:07', '2026-04-16 12:09:07', NULL),
(2, 1, 2, 1, 1, 150.00, 0.00, 0.00, 150.00, 0.00, 0.00, 0.00, 85.00, 96.00, 14400.00, 169.41, 1, 1, '2026-04-16 12:09:14', '2026-04-16 12:09:14', NULL),
(3, 1, 3, 2, 2, 160.00, 0.00, 0.00, 160.00, 0.00, 0.00, 0.00, 85.00, 99.00, 15840.00, 186.35, 1, 1, '2026-04-16 12:09:20', '2026-04-16 12:09:20', NULL),
(4, 1, 4, 3, 3, 1800.00, 0.00, 1620.00, 180.00, 0.00, 0.00, 0.00, 1800.00, 10.00, 1800.00, 1.00, 1, 1, '2026-04-16 12:09:26', '2026-04-16 12:09:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_card_issue_stock_details`
--

CREATE TABLE `job_card_issue_stock_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_issue_item_id` bigint(20) UNSIGNED NOT NULL,
  `stock_entry_item_id` bigint(20) UNSIGNED NOT NULL,
  `qty` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_card_lay_marks`
--

CREATE TABLE `job_card_lay_marks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_fabric_detail_id` bigint(20) UNSIGNED NOT NULL,
  `mark_no` int(11) NOT NULL DEFAULT 1,
  `sizes` longtext DEFAULT NULL,
  `sleeve_type` varchar(10) DEFAULT NULL,
  `lay_mark_meter` decimal(10,2) DEFAULT NULL,
  `no_of_lay` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_lay_marks`
--

INSERT INTO `job_card_lay_marks` (`id`, `job_card_fabric_detail_id`, `mark_no`, `sizes`, `sleeve_type`, `lay_mark_meter`, `no_of_lay`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 3.68, 7.00, '2026-04-16 12:06:50', '2026-04-16 12:06:50'),
(2, 1, 2, '[\"40\",\"42\",\"44\"]', 'F/S', 4.00, 8.00, '2026-04-16 12:06:50', '2026-04-16 12:06:50'),
(3, 1, 3, '[\"36\",\"38\",\"40\",\"42\",\"44\"]', 'H/S', 5.21, 8.00, '2026-04-16 12:06:50', '2026-04-16 12:06:50'),
(4, 2, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 3.68, 7.00, '2026-04-16 12:06:50', '2026-04-16 12:06:50'),
(5, 2, 2, '[\"40\",\"42\",\"44\"]', 'F/S', 4.00, 8.00, '2026-04-16 12:06:50', '2026-04-16 12:06:50'),
(6, 2, 3, '[\"36\",\"38\",\"40\",\"42\",\"44\"]', 'H/S', 5.21, 8.00, '2026-04-16 12:06:50', '2026-04-16 12:06:50'),
(7, 3, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 3.68, 7.00, '2026-04-16 12:06:50', '2026-04-16 12:06:50'),
(8, 3, 2, '[\"40\",\"42\",\"44\"]', 'F/S', 4.00, 8.00, '2026-04-16 12:06:50', '2026-04-16 12:06:50'),
(9, 3, 3, '[\"36\",\"38\",\"40\",\"42\",\"44\"]', 'H/S', 5.21, 8.00, '2026-04-16 12:06:50', '2026-04-16 12:06:50'),
(10, 5, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 6.12, 8.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(11, 5, 2, '[\"38\",\"40\",\"42\"]', 'F/S', 5.63, 7.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(12, 5, 3, '[\"36\",\"38\",\"40\",\"42\",\"44\"]', 'H/S', 5.00, 5.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(13, 6, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 6.12, 8.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(14, 6, 2, '[\"38\",\"40\",\"42\"]', 'F/S', 5.63, 7.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(15, 6, 3, '[\"36\",\"38\",\"40\",\"42\",\"44\"]', 'H/S', 5.00, 5.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(16, 7, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 6.12, 8.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(17, 7, 2, '[\"38\",\"40\",\"42\"]', 'F/S', 5.63, 7.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(18, 7, 3, '[\"36\",\"38\",\"40\",\"42\",\"44\"]', 'H/S', 5.00, 5.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(46, 21, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 7.21, 8.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(47, 21, 2, '[\"38\",\"40\",\"42\",\"44\"]', 'F/S', 6.00, 9.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(48, 21, 3, '[\"36\",\"38\",\"40\",\"42\",\"44\"]', 'H/S', 5.96, 10.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(49, 22, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 7.21, 8.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(50, 22, 2, '[\"38\",\"40\",\"42\",\"44\"]', 'F/S', 6.00, 9.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(51, 22, 3, '[\"36\",\"38\",\"40\",\"42\",\"44\"]', 'H/S', 5.96, 10.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(52, 23, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 7.21, 8.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(53, 23, 2, '[\"38\",\"40\",\"42\",\"44\"]', 'F/S', 6.00, 9.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(54, 23, 3, '[\"36\",\"38\",\"40\",\"42\",\"44\"]', 'H/S', 5.96, 10.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(63, 27, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 6.12, 6.00, '2026-04-20 09:41:54', '2026-04-20 09:41:54'),
(64, 27, 2, '[\"38\",\"40\",\"42\",\"44\"]', 'F/S', 5.00, 5.00, '2026-04-20 09:41:54', '2026-04-20 09:41:54'),
(65, 27, 3, '[\"36\",\"38\",\"40\",\"42\"]', 'H/S', 4.96, 6.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55'),
(66, 27, 4, '[\"40\",\"42\",\"44\"]', 'H/S', 3.11, 5.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55'),
(67, 28, 1, '[\"36\",\"38\",\"40\"]', 'F/S', 6.12, 6.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55'),
(68, 28, 2, '[\"38\",\"40\",\"42\",\"44\"]', 'F/S', 5.00, 5.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55'),
(69, 28, 3, '[\"36\",\"38\",\"40\",\"42\"]', 'H/S', 4.96, 6.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55'),
(70, 28, 4, '[\"40\",\"42\",\"44\"]', 'H/S', 3.11, 5.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55');

-- --------------------------------------------------------

--
-- Table structure for table `job_card_matrix_quantities`
--

CREATE TABLE `job_card_matrix_quantities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_fabric_detail_id` bigint(20) UNSIGNED NOT NULL,
  `size` varchar(255) NOT NULL,
  `color_id` bigint(20) UNSIGNED DEFAULT NULL,
  `qty_fs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `qty_hs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_matrix_quantities`
--

INSERT INTO `job_card_matrix_quantities` (`id`, `job_card_fabric_detail_id`, `size`, `color_id`, `qty_fs`, `qty_hs`, `total_qty`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '36', 1, 7.00, 8.00, 15.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(2, 1, '38', 1, 7.00, 8.00, 15.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(3, 1, '40', 1, 15.00, 8.00, 23.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(4, 1, '42', 1, 8.00, 8.00, 16.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(5, 1, '44', 1, 8.00, 8.00, 16.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(6, 2, '36', 1, 7.00, 8.00, 15.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(7, 2, '38', 1, 7.00, 8.00, 15.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(8, 2, '40', 1, 15.00, 8.00, 23.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(9, 2, '42', 1, 8.00, 8.00, 16.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(10, 2, '44', 1, 8.00, 8.00, 16.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(11, 3, '36', 1, 7.00, 8.00, 15.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(12, 3, '38', 1, 7.00, 8.00, 15.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(13, 3, '40', 1, 15.00, 8.00, 23.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(14, 3, '42', 1, 8.00, 8.00, 16.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(15, 3, '44', 1, 8.00, 8.00, 16.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(16, 4, '36', 1, 168.00, 144.00, 312.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(17, 4, '38', 1, 168.00, 144.00, 312.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(18, 4, '40', 1, 360.00, 144.00, 504.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(19, 4, '42', 1, 192.00, 144.00, 336.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(20, 4, '44', 1, 192.00, 144.00, 336.00, '2026-04-16 12:06:50', '2026-04-20 09:42:44', NULL),
(21, 5, '36', 2, 8.00, 5.00, 13.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(22, 5, '38', 2, 15.00, 5.00, 20.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(23, 5, '40', 2, 15.00, 5.00, 20.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(24, 5, '42', 2, 7.00, 5.00, 12.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(25, 5, '44', 2, 0.00, 5.00, 5.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(26, 6, '36', 2, 8.00, 5.00, 13.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(27, 6, '38', 2, 15.00, 5.00, 20.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(28, 6, '40', 2, 15.00, 5.00, 20.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(29, 6, '42', 2, 7.00, 5.00, 12.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(30, 6, '44', 2, 0.00, 5.00, 5.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(31, 7, '36', 2, 8.00, 5.00, 13.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(32, 7, '38', 2, 15.00, 5.00, 20.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(33, 7, '40', 2, 15.00, 5.00, 20.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(34, 7, '42', 2, 7.00, 5.00, 12.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(35, 7, '44', 2, 0.00, 5.00, 5.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(36, 8, '36', 2, 192.00, 90.00, 282.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(37, 8, '38', 2, 360.00, 90.00, 450.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(38, 8, '40', 2, 360.00, 90.00, 450.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(39, 8, '42', 2, 168.00, 90.00, 258.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(40, 8, '44', 2, 0.00, 90.00, 90.00, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(96, 21, '36', 1, 8.00, 10.00, 18.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(97, 21, '38', 1, 17.00, 10.00, 27.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(98, 21, '40', 1, 17.00, 10.00, 27.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(99, 21, '42', 1, 9.00, 10.00, 19.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(100, 21, '44', 1, 9.00, 10.00, 19.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(101, 22, '36', 1, 8.00, 10.00, 18.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(102, 22, '38', 1, 17.00, 10.00, 27.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(103, 22, '40', 1, 17.00, 10.00, 27.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(104, 22, '42', 1, 9.00, 10.00, 19.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(105, 22, '44', 1, 9.00, 10.00, 19.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(106, 23, '36', 1, 8.00, 10.00, 18.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(107, 23, '38', 1, 17.00, 10.00, 27.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(108, 23, '40', 1, 17.00, 10.00, 27.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(109, 23, '42', 1, 9.00, 10.00, 19.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(110, 23, '44', 1, 9.00, 10.00, 19.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(111, 24, '36', 1, 240.00, 240.00, 480.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(112, 24, '38', 1, 510.00, 240.00, 750.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(113, 24, '40', 1, 510.00, 240.00, 750.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(114, 24, '42', 1, 270.00, 240.00, 510.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(115, 24, '44', 1, 270.00, 240.00, 510.00, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(126, 27, '36', 1, 6.00, 6.00, 12.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(127, 27, '38', 1, 11.00, 6.00, 17.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(128, 27, '40', 1, 11.00, 11.00, 22.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(129, 27, '42', 1, 5.00, 11.00, 16.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(130, 27, '44', 1, 5.00, 5.00, 10.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(131, 28, '36', 1, 6.00, 6.00, 12.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(132, 28, '38', 1, 11.00, 6.00, 17.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(133, 28, '40', 1, 11.00, 11.00, 22.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(134, 28, '42', 1, 5.00, 11.00, 16.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(135, 28, '44', 1, 5.00, 5.00, 10.00, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_card_operations`
--

CREATE TABLE `job_card_operations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_entry_id` bigint(20) UNSIGNED NOT NULL,
  `operation_stage_id` bigint(20) UNSIGNED NOT NULL,
  `service_provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_date` date DEFAULT NULL,
  `deadline_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `received_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_operations`
--

INSERT INTO `job_card_operations` (`id`, `job_card_entry_id`, `operation_stage_id`, `service_provider_id`, `employee_id`, `assigned_date`, `deadline_date`, `remarks`, `received_by`, `created_at`, `updated_at`) VALUES
(3, 2, 1, 1, NULL, '2026-04-17', '2026-04-21', NULL, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(4, 2, 2, 2, NULL, '2026-04-21', '2026-04-24', NULL, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16'),
(11, 3, 1, 1, NULL, '2026-04-17', '2026-04-21', NULL, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(12, 3, 2, 2, NULL, '2026-04-21', '2026-04-24', NULL, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(15, 4, 1, 1, NULL, '2026-04-20', '2026-04-24', NULL, NULL, '2026-04-20 09:41:55', '2026-04-20 09:41:55'),
(16, 4, 2, 2, NULL, '2026-04-24', '2026-04-27', NULL, NULL, '2026-04-20 09:41:55', '2026-04-20 09:41:55'),
(17, 1, 1, 1, NULL, '2026-04-16', '2026-04-20', NULL, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44'),
(18, 1, 2, 2, NULL, '2026-04-20', '2026-04-23', NULL, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44');

-- --------------------------------------------------------

--
-- Table structure for table `job_card_sleeve_meters`
--

CREATE TABLE `job_card_sleeve_meters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_id` bigint(20) UNSIGNED NOT NULL,
  `sleeve_type` varchar(255) NOT NULL,
  `meter` decimal(10,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 'create', 'Role', 'roles', 1, NULL, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-16T08:56:57.000000Z\",\"updated_at\":\"2026-04-16T08:56:57.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 08:56:57'),
(2, 1, 'create', 'Department', 'departments', 1, NULL, '{\"department\":\"Cutting\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:27:39'),
(3, 1, 'create', 'Department', 'departments', 2, NULL, '{\"department\":\"Stitching\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:28:18'),
(4, 1, 'update_status', 'Department Status', 'departments', 2, '{\"id\":2,\"department\":\"Stitching\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:28:18.000000Z\",\"updated_at\":\"2026-04-16T09:28:18.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"department\":\"Stitching\",\"status\":\"Inactive\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:28:18.000000Z\",\"updated_at\":\"2026-04-16T09:28:35.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:28:35'),
(5, 1, 'update_status', 'Department Status', 'departments', 2, '{\"id\":2,\"department\":\"Stitching\",\"status\":\"Inactive\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:28:18.000000Z\",\"updated_at\":\"2026-04-16T09:28:35.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"department\":\"Stitching\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:28:18.000000Z\",\"updated_at\":\"2026-04-16T09:28:40.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:28:40'),
(6, 1, 'create', 'Department', 'departments', 3, NULL, '{\"department\":\"Covering\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:28:54'),
(7, 1, 'update_status', 'Department Status', 'departments', 3, '{\"id\":3,\"department\":\"Covering\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:28:54.000000Z\",\"updated_at\":\"2026-04-16T09:28:54.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"department\":\"Covering\",\"status\":\"Inactive\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:28:54.000000Z\",\"updated_at\":\"2026-04-16T09:28:59.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:28:59'),
(8, 1, 'create', 'Operation Stage', 'operation_stages', 1, NULL, '{\"operation_stage_name\":\"CUTTING\",\"working_days\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:29:47.000000Z\",\"created_at\":\"2026-04-16T09:29:47.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:29:47'),
(9, 1, 'create', 'Operation Stage', 'operation_stages', 2, NULL, '{\"operation_stage_name\":\"STITCHING READY\",\"working_days\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:29:55.000000Z\",\"created_at\":\"2026-04-16T09:29:55.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:29:55'),
(10, 1, 'create', 'Operation Stage', 'operation_stages', 3, NULL, '{\"operation_stage_name\":\"STITCHING ASSEMBLE\",\"working_days\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:30:02.000000Z\",\"created_at\":\"2026-04-16T09:30:02.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:30:02'),
(11, 1, 'create', 'Operation Stage', 'operation_stages', 4, NULL, '{\"operation_stage_name\":\"KAJA BUTTON\",\"working_days\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:30:08.000000Z\",\"created_at\":\"2026-04-16T09:30:08.000000Z\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:30:08'),
(12, 1, 'create', 'Operation Stage', 'operation_stages', 5, NULL, '{\"operation_stage_name\":\"TRIMMING & CHECKING\",\"working_days\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:30:16.000000Z\",\"created_at\":\"2026-04-16T09:30:16.000000Z\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:30:16'),
(13, 1, 'create', 'Operation Stage', 'operation_stages', 6, NULL, '{\"operation_stage_name\":\"IRONING & PACKING\",\"working_days\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:30:24.000000Z\",\"created_at\":\"2026-04-16T09:30:24.000000Z\",\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:30:24'),
(14, 1, 'update', 'Operation Stage', 'operation_stages', 1, '{\"id\":1,\"operation_stage_name\":\"CUTTING\",\"working_days\":0,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:29:47.000000Z\",\"updated_at\":\"2026-04-16T09:29:47.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '{\"id\":1,\"operation_stage_name\":\"CUTTING\",\"working_days\":4,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:29:47.000000Z\",\"updated_at\":\"2026-04-16T09:30:33.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:30:33'),
(15, 1, 'update', 'Operation Stage', 'operation_stages', 2, '{\"id\":2,\"operation_stage_name\":\"STITCHING READY\",\"working_days\":0,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:29:55.000000Z\",\"updated_at\":\"2026-04-16T09:29:55.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '{\"id\":2,\"operation_stage_name\":\"STITCHING READY\",\"working_days\":3,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:29:55.000000Z\",\"updated_at\":\"2026-04-16T09:30:42.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:30:42'),
(16, 1, 'update', 'Operation Stage', 'operation_stages', 3, '{\"id\":3,\"operation_stage_name\":\"STITCHING ASSEMBLE\",\"working_days\":0,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:30:02.000000Z\",\"updated_at\":\"2026-04-16T09:30:02.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '{\"id\":3,\"operation_stage_name\":\"STITCHING ASSEMBLE\",\"working_days\":2,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:30:02.000000Z\",\"updated_at\":\"2026-04-16T09:30:48.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:30:48'),
(17, 1, 'update', 'Operation Stage', 'operation_stages', 4, '{\"id\":4,\"operation_stage_name\":\"KAJA BUTTON\",\"working_days\":0,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:30:08.000000Z\",\"updated_at\":\"2026-04-16T09:30:08.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '{\"id\":4,\"operation_stage_name\":\"KAJA BUTTON\",\"working_days\":2,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:30:08.000000Z\",\"updated_at\":\"2026-04-16T09:30:57.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:30:57'),
(18, 1, 'update', 'Operation Stage', 'operation_stages', 5, '{\"id\":5,\"operation_stage_name\":\"TRIMMING & CHECKING\",\"working_days\":0,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:30:16.000000Z\",\"updated_at\":\"2026-04-16T09:30:16.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '{\"id\":5,\"operation_stage_name\":\"TRIMMING & CHECKING\",\"working_days\":1,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:30:16.000000Z\",\"updated_at\":\"2026-04-16T09:31:03.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:31:03'),
(19, 1, 'update', 'Operation Stage', 'operation_stages', 6, '{\"id\":6,\"operation_stage_name\":\"IRONING & PACKING\",\"working_days\":0,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:30:24.000000Z\",\"updated_at\":\"2026-04-16T09:30:24.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '{\"id\":6,\"operation_stage_name\":\"IRONING & PACKING\",\"working_days\":2,\"status\":\"Active\",\"created_at\":\"2026-04-16T09:30:24.000000Z\",\"updated_at\":\"2026-04-16T09:31:09.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:31:09'),
(20, 1, 'create', 'State', 'states', 1, NULL, '{\"id\":1,\"state_code\":\"33\",\"state_name\":\"TAMIL NADU\",\"status\":\"Active\",\"created_at\":\"2026-04-16T09:31:31.000000Z\",\"updated_at\":\"2026-04-16T09:31:31.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:31:31'),
(21, 1, 'create', 'State', 'states', 2, NULL, '{\"id\":2,\"state_code\":\"37\",\"state_name\":\"ANDHRA PRADESH\",\"status\":\"Active\",\"created_at\":\"2026-04-16T09:31:49.000000Z\",\"updated_at\":\"2026-04-16T09:31:49.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:31:49'),
(22, 1, 'create', 'State', 'states', 3, NULL, '{\"id\":3,\"state_code\":\"27\",\"state_name\":\"MAHARASHTRA\",\"status\":\"Active\",\"created_at\":\"2026-04-16T09:32:03.000000Z\",\"updated_at\":\"2026-04-16T09:32:03.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:32:03'),
(23, 1, 'create', 'State', 'states', 4, NULL, '{\"id\":4,\"state_code\":\"32\",\"state_name\":\"KERALA\",\"status\":\"Active\",\"created_at\":\"2026-04-16T09:32:18.000000Z\",\"updated_at\":\"2026-04-16T09:32:18.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:32:18'),
(24, 1, 'create', 'State', 'states', 5, NULL, '{\"id\":5,\"state_code\":\"29\",\"state_name\":\"KARNATAKA\",\"status\":\"Active\",\"created_at\":\"2026-04-16T09:33:17.000000Z\",\"updated_at\":\"2026-04-16T09:33:17.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:33:17'),
(25, 1, 'create', 'City', 'cities', 1, NULL, '{\"id\":1,\"state_id\":1,\"city_name\":\"MADURAI\",\"city_code\":\"MDU\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:33:49.000000Z\",\"updated_at\":\"2026-04-16T09:33:49.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:33:49'),
(26, 1, 'create', 'City', 'cities', 2, NULL, '{\"id\":2,\"state_id\":1,\"city_name\":\"CHENNAI\",\"city_code\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:34:03.000000Z\",\"updated_at\":\"2026-04-16T09:34:03.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:34:03'),
(27, 1, 'create', 'City', 'cities', 3, NULL, '{\"id\":3,\"state_id\":1,\"city_name\":\"COIMBATORE\",\"city_code\":\"CBE\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:34:19.000000Z\",\"updated_at\":\"2026-04-16T09:34:19.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:34:19'),
(28, 1, 'create', 'City', 'cities', 4, NULL, '{\"id\":4,\"state_id\":4,\"city_name\":\"KOCHI\",\"city_code\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:34:38.000000Z\",\"updated_at\":\"2026-04-16T09:34:38.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:34:38'),
(29, 1, 'create', 'City', 'cities', 5, NULL, '{\"id\":5,\"state_id\":4,\"city_name\":\"THIRUVANDHAPURAM\",\"city_code\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:34:55.000000Z\",\"updated_at\":\"2026-04-16T09:34:55.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:34:55'),
(30, 1, 'create', 'City', 'cities', 6, NULL, '{\"id\":6,\"state_id\":5,\"city_name\":\"BANGALORE\",\"city_code\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:35:11.000000Z\",\"updated_at\":\"2026-04-16T09:35:11.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:35:11'),
(31, 1, 'create', 'City', 'cities', 7, NULL, '{\"id\":7,\"state_id\":2,\"city_name\":\"VIZAG\",\"city_code\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:35:23.000000Z\",\"updated_at\":\"2026-04-16T09:35:23.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:35:23'),
(32, 1, 'create', 'City', 'cities', 8, NULL, '{\"id\":8,\"state_id\":2,\"city_name\":\"AMARAVATI\",\"city_code\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:35:41.000000Z\",\"updated_at\":\"2026-04-16T09:35:41.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:35:41'),
(33, 1, 'create', 'City', 'cities', 9, NULL, '{\"id\":9,\"state_id\":5,\"city_name\":\"MYSORE\",\"city_code\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:36:08.000000Z\",\"updated_at\":\"2026-04-16T09:36:08.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:36:08'),
(34, 1, 'create', 'Place', 'places', 1, NULL, '{\"id\":1,\"state_id\":1,\"city_id\":1,\"place_name\":\"JAIHINDPURAM\",\"place_type\":\"Residential\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:36:43.000000Z\",\"updated_at\":\"2026-04-16T09:36:43.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:36:43'),
(35, 1, 'create', 'Place', 'places', 2, NULL, '{\"id\":2,\"state_id\":4,\"city_id\":5,\"place_name\":\"KAZHAKKOOTTAM\",\"place_type\":\"Residential\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:37:22.000000Z\",\"updated_at\":\"2026-04-16T09:37:22.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:37:22'),
(36, 1, 'create', 'Place', 'places', 3, NULL, '{\"id\":3,\"state_id\":1,\"city_id\":1,\"place_name\":\"ARAPALAYAM\",\"place_type\":\"Residential\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:37:37.000000Z\",\"updated_at\":\"2026-04-16T09:37:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:37:37'),
(37, 1, 'create', 'Place', 'places', 4, NULL, '{\"id\":4,\"state_id\":1,\"city_id\":1,\"place_name\":\"MATTUTHAVANI\",\"place_type\":\"Commercial\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:37:50.000000Z\",\"updated_at\":\"2026-04-16T09:37:50.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:37:50'),
(38, 1, 'create', 'Place', 'places', 5, NULL, '{\"id\":5,\"state_id\":1,\"city_id\":2,\"place_name\":\"Mylapore\",\"place_type\":\"Commercial\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:44:00.000000Z\",\"updated_at\":\"2026-04-16T09:44:00.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:44:00'),
(39, 1, 'create', 'Place', 'places', 6, NULL, '{\"id\":6,\"state_id\":1,\"city_id\":2,\"place_name\":\"Adyar\",\"place_type\":\"Commercial\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:44:19.000000Z\",\"updated_at\":\"2026-04-16T09:44:19.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:44:19'),
(40, 1, 'update', 'Place', 'places', 6, '{\"id\":6,\"state_id\":1,\"city_id\":2,\"place_name\":\"Adyar\",\"place_type\":\"Commercial\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:44:19.000000Z\",\"updated_at\":\"2026-04-16T09:44:19.000000Z\",\"deleted_at\":null}', '{\"id\":6,\"state_id\":1,\"city_id\":2,\"place_name\":\"ADYAR\",\"place_type\":\"Commercial\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T09:44:19.000000Z\",\"updated_at\":\"2026-04-16T09:44:28.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:44:28'),
(41, 1, 'update', 'Place', 'places', 5, '{\"id\":5,\"state_id\":1,\"city_id\":2,\"place_name\":\"Mylapore\",\"place_type\":\"Commercial\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:44:00.000000Z\",\"updated_at\":\"2026-04-16T09:44:00.000000Z\",\"deleted_at\":null}', '{\"id\":5,\"state_id\":1,\"city_id\":2,\"place_name\":\"MYLAPORE\",\"place_type\":\"Commercial\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T09:44:00.000000Z\",\"updated_at\":\"2026-04-16T09:44:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:44:37'),
(42, 1, 'create', 'UOM', 'uoms', 1, NULL, '{\"id\":1,\"uom_code\":\"KG\",\"uom_name\":\"KILOGRAM\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:44:58.000000Z\",\"updated_at\":\"2026-04-16T09:44:58.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:44:58'),
(43, 1, 'create', 'UOM', 'uoms', 2, NULL, '{\"id\":2,\"uom_code\":\"BL\",\"uom_name\":\"BALE\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:45:16.000000Z\",\"updated_at\":\"2026-04-16T09:45:16.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:45:16'),
(44, 1, 'create', 'UOM', 'uoms', 3, NULL, '{\"id\":3,\"uom_code\":\"NOS\",\"uom_name\":\"NUMBERS\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:45:27.000000Z\",\"updated_at\":\"2026-04-16T09:45:27.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:45:27'),
(45, 1, 'create', 'UOM', 'uoms', 4, NULL, '{\"id\":4,\"uom_code\":\"BDL\",\"uom_name\":\"BUNDLE\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:45:40.000000Z\",\"updated_at\":\"2026-04-16T09:45:40.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:45:40'),
(46, 1, 'create', 'UOM', 'uoms', 5, NULL, '{\"id\":5,\"uom_code\":\"MTR\",\"uom_name\":\"METER\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T09:45:51.000000Z\",\"updated_at\":\"2026-04-16T09:45:51.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:45:51'),
(47, 1, 'create', 'Color', 'colors', 1, NULL, '{\"id\":1,\"color_name\":\"ENGLISH COLORS\",\"description\":null,\"created_at\":\"2026-04-16T09:46:26.000000Z\",\"updated_at\":\"2026-04-16T09:46:26.000000Z\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:46:26'),
(48, 1, 'create', 'Color', 'colors', 2, NULL, '{\"id\":2,\"color_name\":\"BRIGHT COLORS\",\"description\":null,\"created_at\":\"2026-04-16T09:46:33.000000Z\",\"updated_at\":\"2026-04-16T09:46:33.000000Z\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:46:33'),
(49, 1, 'create', 'Color', 'colors', 3, NULL, '{\"id\":3,\"color_name\":\"NEUTRAL COLORS\",\"description\":null,\"created_at\":\"2026-04-16T09:46:48.000000Z\",\"updated_at\":\"2026-04-16T09:46:48.000000Z\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:46:48'),
(50, 1, 'create', 'Color', 'colors', 4, NULL, '{\"id\":4,\"color_name\":\"DIRTY COLOR\",\"description\":null,\"created_at\":\"2026-04-16T09:46:55.000000Z\",\"updated_at\":\"2026-04-16T09:46:55.000000Z\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:46:55'),
(51, 1, 'create', 'Color', 'colors', 5, NULL, '{\"id\":5,\"color_name\":\"DARK COLORS\",\"description\":null,\"created_at\":\"2026-04-16T09:47:02.000000Z\",\"updated_at\":\"2026-04-16T09:47:02.000000Z\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:47:02'),
(52, 1, 'create', 'Color', 'colors', 6, NULL, '{\"id\":6,\"color_name\":\"IVORY\",\"description\":null,\"created_at\":\"2026-04-16T09:47:19.000000Z\",\"updated_at\":\"2026-04-16T09:47:19.000000Z\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:47:19'),
(53, 1, 'create', 'Zone', 'zones', 1, NULL, '{\"zone_name\":\"I\",\"state_id\":\"1\",\"city_ids\":\"1,3\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:47:50.000000Z\",\"created_at\":\"2026-04-16T09:47:50.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:47:50'),
(54, 1, 'create', 'Zone', 'zones', 2, NULL, '{\"zone_name\":\"II\",\"state_id\":\"4\",\"city_ids\":\"4,5\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:48:05.000000Z\",\"created_at\":\"2026-04-16T09:48:05.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:48:05'),
(55, 1, 'create', 'Zone', 'zones', 3, NULL, '{\"zone_name\":\"III\",\"state_id\":\"2\",\"city_ids\":\"7,8\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:48:23.000000Z\",\"created_at\":\"2026-04-16T09:48:23.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:48:23'),
(56, 1, 'create', 'Size Ratio', 'size_ratios', 1, NULL, '{\"size\":\"38,40,42,44\",\"ratio\":\"5,6,5,6\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:48:47.000000Z\",\"created_at\":\"2026-04-16T09:48:47.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:48:47'),
(57, 1, 'create', 'Fabric Type', 'fabric_types', 1, NULL, '{\"fabric_type\":\"COTTON\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:49:04.000000Z\",\"created_at\":\"2026-04-16T09:49:04.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:49:04'),
(58, 1, 'create', 'Fabric Type', 'fabric_types', 2, NULL, '{\"fabric_type\":\"POLYESTER & BLENDS\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:49:21.000000Z\",\"created_at\":\"2026-04-16T09:49:21.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:49:21'),
(59, 1, 'create', 'Fabric Type', 'fabric_types', 3, NULL, '{\"fabric_type\":\"lINEN\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:49:30.000000Z\",\"created_at\":\"2026-04-16T09:49:30.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:49:30'),
(60, 1, 'create', 'Fabric Size', 'fabric_sizes', 1, NULL, '{\"width\":\"58\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:49:42'),
(61, 1, 'create', 'Fabric Size', 'fabric_sizes', 2, NULL, '{\"width\":\"36\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:49:49'),
(62, 1, 'create', 'Charge', 'charges', 1, NULL, '{\"charge_name\":\"BROKERAGE\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:50:04.000000Z\",\"created_at\":\"2026-04-16T09:50:04.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:50:04'),
(63, 1, 'create', 'Charge', 'charges', 2, NULL, '{\"charge_name\":\"INSURANCE CHARGE\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:50:19.000000Z\",\"created_at\":\"2026-04-16T09:50:19.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:50:19'),
(64, 1, 'create', 'Charge', 'charges', 3, NULL, '{\"charge_name\":\"PACKING CHARGE\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:50:29.000000Z\",\"created_at\":\"2026-04-16T09:50:29.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:50:29'),
(65, 1, 'create', 'Store Location', 'store_locations', 1, NULL, '{\"store_location\":\"S1\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:50:42.000000Z\",\"created_at\":\"2026-04-16T09:50:42.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:50:42'),
(66, 1, 'create', 'Store Location', 'store_locations', 2, NULL, '{\"store_location\":\"S2\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:50:50.000000Z\",\"created_at\":\"2026-04-16T09:50:50.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:50:50'),
(67, 1, 'create', 'Store Location', 'store_locations', 3, NULL, '{\"store_location\":\"A1\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T09:51:00.000000Z\",\"created_at\":\"2026-04-16T09:51:00.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:51:00'),
(68, 1, 'create', 'Style', 'styles', 1, NULL, '{\"style_name\":\"PLAIN\",\"code\":\"PLN\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:51:44'),
(69, 1, 'create', 'Style', 'styles', 2, NULL, '{\"style_name\":\"PRINT\",\"code\":\"PRNT\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:51:57'),
(70, 1, 'create', 'Style', 'styles', 3, NULL, '{\"style_name\":\"CHECKED\",\"code\":\"CHD\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:52:06'),
(71, 1, 'create', 'Style', 'styles', 4, NULL, '{\"style_name\":\"STRIPED\",\"code\":\"STD\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:52:18'),
(72, 1, 'update', 'Store Type', 'store_types', 1, NULL, '{\"store_type_name\":\"FABRIC STORE\",\"status\":\"Active\",\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:52:58'),
(73, 1, 'update', 'Store Type', 'store_types', 3, NULL, '{\"store_type_name\":\"ACCESSORIES\",\"status\":\"Active\",\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:53:07'),
(74, 1, 'update', 'Store Type', 'store_types', 2, NULL, '{\"store_type_name\":\"FINISHED GOODS\",\"status\":\"Active\",\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:53:15'),
(75, 1, 'update', 'Store Type', 'store_types', 3, NULL, '{\"store_type_name\":\"ACCESSORIES STORE\",\"status\":\"Active\",\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:53:21'),
(76, 1, 'create', 'Shipping Method', 'shipping_methods', 1, NULL, '{\"name\":\"DTDC\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:54:05'),
(77, 1, 'create', 'Shipping Method', 'shipping_methods', 2, NULL, '{\"name\":\"BLUEDART\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:54:18'),
(78, 1, 'create', 'Shipping Method', 'shipping_methods', 3, NULL, '{\"name\":\"DHL EXPRESS\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:54:30'),
(79, 1, 'create', 'Transport Mode', 'transport_modes', 1, NULL, '{\"name\":\"ROAD\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:55:07'),
(80, 1, 'create', 'Transport Mode', 'transport_modes', 2, NULL, '{\"name\":\"RAIL\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:55:15'),
(81, 1, 'create', 'Transport Mode', 'transport_modes', 3, NULL, '{\"name\":\"AIR\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:55:22'),
(82, 1, 'create', 'Transport Mode', 'transport_modes', 4, NULL, '{\"name\":\"SEA\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:55:28'),
(83, 1, 'create', 'Fit', 'fits', 1, NULL, '{\"fit_name\":\"RUGULAR FIT\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:55:47'),
(84, 1, 'create', 'Fit', 'fits', 2, NULL, '{\"fit_name\":\"TAILOR FIT\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:55:54'),
(85, 1, 'create', 'Fit', 'fits', 3, NULL, '{\"fit_name\":\"SLIM FIT\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:56:01'),
(86, 1, 'create', 'Fit', 'fits', 4, NULL, '{\"fit_name\":\"28 MM AMERICAN PAATI\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:56:14'),
(87, 1, 'delete', 'Fit', 'fits', 4, '{\"id\":4,\"fit_name\":\"28 MM AMERICAN PAATI\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null,\"created_at\":\"2026-04-16T09:56:14.000000Z\",\"updated_at\":\"2026-04-16T09:56:14.000000Z\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:56:24'),
(88, 1, 'create', 'Patti Type', 'patti_types', 1, NULL, '{\"patti_type_name\":\"28 MM AMERICAN PAATI\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:56:53'),
(89, 1, 'create', 'Patti Type', 'patti_types', 2, NULL, '{\"patti_type_name\":\"28 MM INSIDE PAATI\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:57:00'),
(90, 1, 'create', 'Patti Type', 'patti_types', 3, NULL, '{\"patti_type_name\":\"28 MM WITHOUT PAATI\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:57:07'),
(91, 1, 'create', 'Collar Type', 'collar_types', 1, NULL, '{\"collar_type_name\":\"REGULAR COLLAR SINGLE CANVAS & FOAM CANVAS\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:57:21'),
(92, 1, 'create', 'Collar Type', 'collar_types', 2, NULL, '{\"collar_type_name\":\"TAILOR COLLAR SINGLE CANVAS & FOAM CANVAS\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:57:30'),
(93, 1, 'create', 'Collar Type', 'collar_types', 3, NULL, '{\"collar_type_name\":\"WASHING COLLAR SINGLE CANVAS & FOAM CANVAS\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:57:36'),
(94, 1, 'create', 'Collar Type', 'collar_types', 4, NULL, '{\"collar_type_name\":\"CHINESE COLLAR DOUBLE CANVAS\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:57:43'),
(95, 1, 'create', 'Cuff Type', 'cuff_types', 1, NULL, '{\"cuff_type_name\":\"ROUND\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:57:59'),
(96, 1, 'create', 'Cuff Type', 'cuff_types', 2, NULL, '{\"cuff_type_name\":\"CORNER CROSS\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:58:07'),
(97, 1, 'create', 'Pocket Type', 'pocket_types', 1, NULL, '{\"pocket_type_name\":\"CORNER CROSS\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:58:26'),
(98, 1, 'create', 'Pocket Type', 'pocket_types', 2, NULL, '{\"pocket_type_name\":\"V POCKET\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:58:32'),
(99, 1, 'create', 'Bottom Cut', 'bottom_cuts', 1, NULL, '{\"bottom_cut_name\":\"AERO CUT\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:58:45'),
(100, 1, 'create', 'Bottom Cut', 'bottom_cuts', 2, NULL, '{\"bottom_cut_name\":\"SLACK CUT\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:58:53'),
(101, 1, 'create', 'Process Group', 'process_groups', 1, NULL, '{\"name\":\"CHECKED FULL SLEEVE\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 09:59:52'),
(102, 1, 'create', 'Process Group', 'process_groups', 2, NULL, '{\"name\":\"CHECKED FULL & HALF SLEEVE\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:00:01'),
(103, 1, 'create', 'Process Group', 'process_groups', 3, NULL, '{\"name\":\"CHECKED HALF SLEEVE\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:00:08'),
(104, 1, 'create', 'Process Group', 'process_groups', 4, NULL, '{\"name\":\"OTHERS FULL SLEEVE\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:00:14'),
(105, 1, 'create', 'Process Group', 'process_groups', 5, NULL, '{\"name\":\"OTHERS HALF SLEEVE\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:00:21'),
(106, 1, 'create', 'Process Group', 'process_groups', 6, NULL, '{\"name\":\"OTHERS FULL & HALF SLEEVE\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:00:27'),
(107, 1, 'create', 'Production Service', 'production_services', 1, NULL, '{\"service_name\":\"Fabric Inspection\",\"service_code\":\"CUT-FI\",\"operation_stage_id\":\"1\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:01:39'),
(108, 1, 'update', 'Production Service', 'production_services', 1, NULL, '{\"service_name\":\"FABRIC INSPECTION\",\"service_code\":\"CUT-FI\",\"operation_stage_id\":\"1\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:01:53'),
(109, 1, 'create', 'Production Service', 'production_services', 2, NULL, '{\"service_name\":\"MARKER PLANNING\",\"service_code\":\"CUT-MP\",\"operation_stage_id\":\"1\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:02:15'),
(110, 1, 'create', 'Production Service', 'production_services', 3, NULL, '{\"service_name\":\"FABRIC SPREADING\",\"service_code\":\"CUT-FS\",\"operation_stage_id\":\"1\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:02:33'),
(111, 1, 'create', 'Production Service', 'production_services', 4, NULL, '{\"service_name\":\"BUNDLING\",\"service_code\":\"CUT-BD\",\"operation_stage_id\":\"1\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:02:50'),
(112, 1, 'create', 'Production Service', 'production_services', 5, NULL, '{\"service_name\":\"TRIMMING\",\"service_code\":\"CUT-TR\",\"operation_stage_id\":\"1\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:03:12'),
(113, 1, 'create', 'Production Service', 'production_services', 6, NULL, '{\"service_name\":\"COLLAR STITCHING\",\"service_code\":\"SEW-CL\",\"operation_stage_id\":\"2\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:03:32'),
(114, 1, 'create', 'Production Service', 'production_services', 7, NULL, '{\"service_name\":\"CUFF STITCHING\",\"service_code\":\"SEW-CF\",\"operation_stage_id\":\"2\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:03:50'),
(115, 1, 'create', 'Production Service', 'production_services', 8, NULL, '{\"service_name\":\"SLEEVE ATTACHING\",\"service_code\":\"SEW-SL\",\"operation_stage_id\":\"2\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:04:08'),
(116, 1, 'create', 'Production Service', 'production_services', 9, NULL, '{\"service_name\":\"THREAD TRIMMING\",\"service_code\":\"FIN-TR\",\"operation_stage_id\":\"3\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:04:37'),
(117, 1, 'create', 'Production Service', 'production_services', 10, NULL, '{\"service_name\":\"FOLDING\",\"service_code\":\"FIN-FL\",\"operation_stage_id\":\"3\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:04:56'),
(118, 1, 'create', 'Customer', 'customers', 1, NULL, '{\"category\":\"Wholesaler\",\"name\":\"AK AHAMED\",\"code\":\"1001\",\"mobile_no\":\"9876987650\",\"email\":null,\"website_url\":null,\"transport_name\":null,\"booking_office\":null,\"zone_id\":\"1\",\"store_id\":null,\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"3\",\"address_line_1\":\"25, NAVBATHAKANA STREET\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":null,\"designation\":null,\"contact_mobile_no\":null,\"contact_email\":null,\"tax_type_id\":null,\"gst_no\":null,\"pan_no\":null,\"payment_terms\":null,\"credit_limit\":0,\"sales_discount\":0,\"box_discount\":0,\"bank_name\":null,\"branch\":null,\"account_number\":null,\"ifsc_code\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:06:15.000000Z\",\"created_at\":\"2026-04-16T10:06:15.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:06:15'),
(119, 1, 'create', 'Purchase Commission Agent', 'purchase_commission_agents', 1, NULL, '{\"name\":\"BHAGWAN TEXTILE AGENCY\",\"code\":\"1001\",\"email\":null,\"mobile_no\":\"6936953698\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"4\",\"address_line_1\":null,\"address_line_2\":null,\"zipcode\":null,\"contact_person_name\":null,\"designation\":null,\"phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"remarks\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:06:48.000000Z\",\"created_at\":\"2026-04-16T10:06:48.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:06:48');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(120, 1, 'create', 'Purchase Commission Agent', 'purchase_commission_agents', 2, NULL, '{\"name\":\"SRI MEENAKSHI TEXTILE\",\"code\":\"1002\",\"email\":null,\"mobile_no\":\"6535358745\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"4\",\"address_line_1\":null,\"address_line_2\":null,\"zipcode\":null,\"contact_person_name\":null,\"designation\":null,\"phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"remarks\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:08:18.000000Z\",\"created_at\":\"2026-04-16T10:08:18.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:08:18'),
(121, 1, 'create', 'Purchase Commission Agent', 'purchase_commission_agents', 3, NULL, '{\"name\":\"Bright agencies - Matching CenteR\",\"code\":\"1003\",\"email\":null,\"mobile_no\":\"8520147474\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"2\",\"place_id\":\"6\",\"address_line_1\":null,\"address_line_2\":null,\"zipcode\":null,\"contact_person_name\":null,\"designation\":null,\"phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"remarks\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:08:52.000000Z\",\"created_at\":\"2026-04-16T10:08:52.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:08:52'),
(122, 1, 'create', 'City', 'cities', 10, NULL, '{\"id\":10,\"state_id\":3,\"city_name\":\"MUMBAI\",\"city_code\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T10:10:35.000000Z\",\"updated_at\":\"2026-04-16T10:10:35.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:10:35'),
(123, 1, 'create', 'Place', 'places', 7, NULL, '{\"id\":7,\"state_id\":3,\"city_id\":10,\"place_name\":\"BHIWANDI\",\"place_type\":\"Commercial\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T10:13:16.000000Z\",\"updated_at\":\"2026-04-16T10:13:16.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:13:16'),
(124, 1, 'create', 'Supplier', 'suppliers', 1, NULL, '{\"name\":\"KAMAL SYNTHETICS\",\"code\":\"1000\",\"mobile_no\":\"8585742369\",\"email\":null,\"website_url\":null,\"transport_name\":\"UTTAM ROADWAYS\",\"booking_area\":null,\"stores\":null,\"store_id\":\"1\",\"status\":\"Active\",\"state_id\":\"3\",\"city_id\":\"10\",\"place_id\":\"7\",\"address_line_1\":\"A-205 AARYA MOOLCHAND COMPOUND 2ND FLOOR DAPODA ROAD\",\"address_line_2\":\"ANJUR PHATA\",\"address_line_3\":null,\"zip_code\":\"400002\",\"contact_person_name\":null,\"designation\":null,\"contact_mobile_no\":null,\"contact_email\":null,\"purchase_commission_agent_id\":null,\"commission_percentage\":0,\"tax_id\":null,\"igst_percent\":\"5.00\",\"cgst_percent\":0,\"sgst_percent\":0,\"gst_no\":\"27ADNPJ9869J1ZL\",\"pan_no\":null,\"ecc_no\":null,\"credit_limit\":0,\"payment_terms\":null,\"bank_name\":null,\"branch\":null,\"account_number\":null,\"ifsc_code\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:14:00.000000Z\",\"created_at\":\"2026-04-16T10:14:00.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:14:00'),
(125, 1, 'create', 'Supplier', 'suppliers', 2, NULL, '{\"name\":\"BALAJI SHIRTING LLP\",\"code\":\"1001\",\"mobile_no\":\"8850761244\",\"email\":null,\"website_url\":null,\"transport_name\":\"UTTAM ROADWAYS\",\"booking_area\":\"MADURAI\",\"stores\":null,\"store_id\":\"1\",\"status\":\"Active\",\"state_id\":\"3\",\"city_id\":\"10\",\"place_id\":\"7\",\"address_line_1\":\"H.NO: DHARANI ARCADE, OPP. MEGHDHARA COMPLEX\",\"address_line_2\":\"2ND FLOOR, GALA NO. 12, ANJURPHATA,\",\"address_line_3\":\"RAHNAL VILLAGE, BHIWANDI\",\"zip_code\":\"421302\",\"contact_person_name\":null,\"designation\":null,\"contact_mobile_no\":null,\"contact_email\":null,\"purchase_commission_agent_id\":null,\"commission_percentage\":0,\"tax_id\":null,\"igst_percent\":\"5.00\",\"cgst_percent\":0,\"sgst_percent\":0,\"gst_no\":\"27ABCFB8882A1ZH\",\"pan_no\":null,\"ecc_no\":null,\"credit_limit\":\"90.00\",\"payment_terms\":null,\"bank_name\":null,\"branch\":null,\"account_number\":null,\"ifsc_code\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:15:13.000000Z\",\"created_at\":\"2026-04-16T10:15:13.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:15:13'),
(126, 1, 'create', 'Supplier', 'suppliers', 3, NULL, '{\"name\":\"POOJA IMPEX\",\"code\":\"1002\",\"mobile_no\":\"9840377952\",\"email\":null,\"website_url\":null,\"transport_name\":\"MADURAI RADHA TRANSPORT\",\"booking_area\":\"MADURAI\",\"stores\":null,\"store_id\":\"3\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"2\",\"place_id\":\"5\",\"address_line_1\":\"OLD NO 11A\\/2 NEW NO 20 LAWYER CHINNAI THAMBI STREET\",\"address_line_2\":\"KONDITHOPE\",\"address_line_3\":null,\"zip_code\":null,\"contact_person_name\":null,\"designation\":null,\"contact_mobile_no\":null,\"contact_email\":null,\"purchase_commission_agent_id\":null,\"commission_percentage\":0,\"tax_id\":null,\"igst_percent\":0,\"cgst_percent\":\"9\",\"sgst_percent\":\"9\",\"gst_no\":\"33AEPPK6992J1ZT\",\"pan_no\":null,\"ecc_no\":null,\"credit_limit\":0,\"payment_terms\":null,\"bank_name\":null,\"branch\":null,\"account_number\":null,\"ifsc_code\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:16:13.000000Z\",\"created_at\":\"2026-04-16T10:16:13.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:16:13'),
(127, 1, 'create', 'Supplier', 'suppliers', 4, NULL, '{\"name\":\"SHARMAN UDYOG PVT LTD\",\"code\":\"1003\",\"mobile_no\":\"9543880334\",\"email\":null,\"website_url\":null,\"transport_name\":null,\"booking_area\":\"MADURAI\",\"stores\":null,\"store_id\":\"3\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"3\",\"address_line_1\":\"161&153 G&H HSIIDC INDL ESTATE\",\"address_line_2\":\"PHASE -II KUNDLI\",\"address_line_3\":null,\"zip_code\":\"131028\",\"contact_person_name\":\"SARAVANAN\",\"designation\":\"MARKETING MANAGER\",\"contact_mobile_no\":\"9543880334\",\"contact_email\":null,\"purchase_commission_agent_id\":null,\"commission_percentage\":0,\"tax_id\":null,\"igst_percent\":\"18.00\",\"cgst_percent\":0,\"sgst_percent\":0,\"gst_no\":\"06AACCS5208F2ZN\",\"pan_no\":null,\"ecc_no\":null,\"credit_limit\":0,\"payment_terms\":null,\"bank_name\":null,\"branch\":null,\"account_number\":null,\"ifsc_code\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:17:29.000000Z\",\"created_at\":\"2026-04-16T10:17:29.000000Z\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:17:29'),
(128, 1, 'create', 'Supplier', 'suppliers', 5, NULL, '{\"name\":\"NATHALAKSHMI PRINTERS\",\"code\":\"1004\",\"mobile_no\":\"8220012476\",\"email\":\"jlsaravanan@gmail.com\",\"website_url\":null,\"transport_name\":null,\"booking_area\":\"MADURAI\",\"stores\":null,\"store_id\":\"3\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"4\",\"address_line_1\":\"31,32,33,34 WEST CAR STREET\",\"address_line_2\":\"CUDDALORE\",\"address_line_3\":\"CHIDAMBARAM\",\"zip_code\":\"608001\",\"contact_person_name\":null,\"designation\":null,\"contact_mobile_no\":null,\"contact_email\":null,\"purchase_commission_agent_id\":null,\"commission_percentage\":0,\"tax_id\":null,\"igst_percent\":0,\"cgst_percent\":\"2.50\",\"sgst_percent\":\"2.50\",\"gst_no\":\"33BPBPS9489A2ZN\",\"pan_no\":null,\"ecc_no\":null,\"credit_limit\":0,\"payment_terms\":null,\"bank_name\":null,\"branch\":null,\"account_number\":null,\"ifsc_code\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:18:35.000000Z\",\"created_at\":\"2026-04-16T10:18:35.000000Z\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:18:35'),
(129, 1, 'create', 'Service Provider', 'service_providers', 1, NULL, '{\"operation_stage_id\":\"1\",\"name\":\"Nachias Fashion Private Limited\",\"code\":\"NFPL\",\"is_plant\":1,\"email\":\"nachias@gmail.com\",\"mobile_no\":\"8520369741\",\"zip_code\":\"625016\",\"website_url\":null,\"service_rate\":\"Job Type\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"3\",\"address_line_1\":\"272\\/2, Somu Nagar, Sringeri Nagar,\",\"address_line_2\":\"By Pass Road\",\"contact_person_name\":null,\"designation\":null,\"phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"remarks\":null,\"bank_name\":null,\"bank_acc_no\":null,\"ifsc_code\":null,\"payment_terms\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:20:36.000000Z\",\"created_at\":\"2026-04-16T10:20:36.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:20:36'),
(130, 1, 'create', 'Service Provider', 'service_providers', 2, NULL, '{\"operation_stage_id\":\"2\",\"name\":\"Samayanallur Unit\",\"code\":\"SMLR\",\"is_plant\":1,\"email\":null,\"mobile_no\":\"9666520321\",\"zip_code\":\"625011\",\"website_url\":null,\"service_rate\":\"Job Type\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"2\",\"place_id\":\"6\",\"address_line_1\":\"23, Block Side Road,\",\"address_line_2\":null,\"contact_person_name\":null,\"designation\":null,\"phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"remarks\":null,\"bank_name\":null,\"bank_acc_no\":null,\"ifsc_code\":null,\"payment_terms\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:22:57.000000Z\",\"created_at\":\"2026-04-16T10:22:57.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:22:57'),
(131, 1, 'create', 'Service Provider', 'service_providers', 3, NULL, '{\"operation_stage_id\":\"3\",\"name\":\"Kalavasal\",\"code\":\"KVSL\",\"is_plant\":1,\"email\":null,\"mobile_no\":\"6565654102\",\"zip_code\":\"625011\",\"website_url\":null,\"service_rate\":\"Per Agent\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"3\",\"address_line_1\":\"90, Bypass Road,\",\"address_line_2\":null,\"contact_person_name\":null,\"designation\":null,\"phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"remarks\":null,\"bank_name\":null,\"bank_acc_no\":null,\"ifsc_code\":null,\"payment_terms\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:23:51.000000Z\",\"created_at\":\"2026-04-16T10:23:51.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:23:51'),
(132, 1, 'create', 'Sales Agent', 'sales_agents', 1, NULL, '{\"agent_type\":\"Export Agent\",\"name\":\"JAY\",\"code\":\"1000\",\"email\":\"jay34@gmail.com\",\"mobile_no\":\"6985968596\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"1\",\"zone_id\":\"1\",\"address_line_1\":\"25, Alavai Nagar,\",\"address_line_2\":null,\"zip_code\":\"625011\",\"contact_person_name\":null,\"designation\":null,\"contact_phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"commission_value\":null,\"sales_target\":null,\"created_by\":1,\"updated_at\":\"2026-04-16T10:25:56.000000Z\",\"created_at\":\"2026-04-16T10:25:56.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:25:56'),
(133, 1, 'create', 'Store Category', 'store_categories', 1, NULL, '{\"code\":\"FBC\",\"category_name\":\"Fabric\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:27:04.000000Z\",\"created_at\":\"2026-04-16T10:27:04.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:27:04'),
(134, 1, 'create', 'Store Category', 'store_categories', 2, NULL, '{\"code\":\"ACC\",\"category_name\":\"Accessories\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:27:25.000000Z\",\"created_at\":\"2026-04-16T10:27:25.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:27:25'),
(135, 1, 'create', 'Raw Material', 'raw_materials', 1, NULL, '{\"store_category_id\":\"1\",\"code\":\"1001\",\"name\":\"COTTON FABRIC\",\"supplier_design_name\":null,\"size_width\":null,\"uom_id\":\"5\",\"material_type\":null,\"specification\":null,\"min_stock\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:28:09.000000Z\",\"created_at\":\"2026-04-16T10:28:09.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:28:09'),
(136, 1, 'create', 'Raw Material', 'raw_materials', 2, NULL, '{\"store_category_id\":\"1\",\"code\":\"1002\",\"name\":\"DENIM FABRIC\",\"supplier_design_name\":null,\"size_width\":null,\"uom_id\":\"5\",\"material_type\":null,\"specification\":null,\"min_stock\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:30:43.000000Z\",\"created_at\":\"2026-04-16T10:30:43.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:30:43'),
(137, 1, 'create', 'Raw Material', 'raw_materials', 3, NULL, '{\"store_category_id\":\"2\",\"code\":\"1003\",\"name\":\"BUTTONS\",\"supplier_design_name\":null,\"size_width\":null,\"uom_id\":\"3\",\"material_type\":null,\"specification\":null,\"min_stock\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:32:09.000000Z\",\"created_at\":\"2026-04-16T10:32:09.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:32:09'),
(138, 1, 'create', 'Raw Material', 'raw_materials', 4, NULL, '{\"store_category_id\":\"2\",\"code\":\"1004\",\"name\":\"COTTON THREAD\",\"supplier_design_name\":null,\"size_width\":null,\"uom_id\":\"5\",\"material_type\":null,\"specification\":null,\"min_stock\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:40:13.000000Z\",\"created_at\":\"2026-04-16T10:40:13.000000Z\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:40:13'),
(139, 1, 'create', 'Raw Material', 'raw_materials', 5, NULL, '{\"store_category_id\":\"2\",\"code\":\"AS-01\",\"name\":\"28 MM N.PATTI CANVAS\",\"supplier_design_name\":null,\"size_width\":null,\"uom_id\":\"3\",\"material_type\":null,\"specification\":null,\"min_stock\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:40:49.000000Z\",\"created_at\":\"2026-04-16T10:40:49.000000Z\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:40:49'),
(140, 1, 'create', 'Brand', 'brands', 1, NULL, '{\"brand_name\":\"CASINO FORMAL\",\"code\":\"CF\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:41:51.000000Z\",\"created_at\":\"2026-04-16T10:41:51.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:41:51'),
(141, 1, 'create', 'Brand', 'brands', 2, NULL, '{\"brand_name\":\"CASINO DEAL\",\"code\":\"CD\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:42:08.000000Z\",\"created_at\":\"2026-04-16T10:42:08.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:42:08'),
(142, 1, 'create', 'Brand', 'brands', 3, NULL, '{\"brand_name\":\"CASINO BRAVO\",\"code\":\"CB\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:42:24.000000Z\",\"created_at\":\"2026-04-16T10:42:24.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:42:24'),
(143, 1, 'create', 'Brand', 'brands', 4, NULL, '{\"brand_name\":\"CASINO FORMAL CORE\",\"code\":\"CFC\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-04-16T10:42:36.000000Z\",\"created_at\":\"2026-04-16T10:42:36.000000Z\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 10:42:36'),
(144, 1, 'create', 'Purchase Order', 'purchase_orders', 1, NULL, '{\"po_number\":\"PO-0001\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":\"2\",\"commission\":\"3.00\",\"supplier_id\":\"3\",\"reference_no\":\"6525\",\"reference_date\":\"2026-04-15T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":\"3\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"310.00\",\"sub_total\":\"30240.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"604.80\",\"taxable_amount\":\"28728.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"5171.04\",\"round_off_type\":\"Less\",\"round_off\":\"0.04\",\"total_amount\":\"33899.00\",\"is_self_closed\":false,\"updated_at\":\"2026-04-16T11:22:10.000000Z\",\"created_at\":\"2026-04-16T11:22:10.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:22:10'),
(145, 1, 'create', 'Store Type', 'store_types', 1, NULL, '{\"store_type_name\":\"Fabric Store\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:34:05'),
(146, 1, 'create', 'Store Type', 'store_types', 2, NULL, '{\"store_type_name\":\"Accessories Store\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:34:16'),
(147, 1, 'create', 'Store Type', 'store_types', 3, NULL, '{\"store_type_name\":\"Finished Goods\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:34:29'),
(148, 1, 'create', 'Purchase Order', 'purchase_orders', 2, NULL, '{\"po_number\":\"PO-0002\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":\"2\",\"commission\":\"2.00\",\"supplier_id\":\"2\",\"reference_no\":\"5820\",\"reference_date\":\"2026-04-15T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":\"1\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"150.00\",\"sub_total\":\"14850.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"297.00\",\"taxable_amount\":\"14256.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"712.80\",\"round_off_type\":\"Less\",\"round_off\":\"0.20\",\"total_amount\":\"14968.60\",\"is_self_closed\":false,\"updated_at\":\"2026-04-16T11:36:29.000000Z\",\"created_at\":\"2026-04-16T11:36:29.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:36:29'),
(149, 1, 'update', 'Supplier', 'suppliers', 3, '{\"id\":3,\"name\":\"POOJA IMPEX\",\"code\":\"1002\",\"mobile_no\":\"9840377952\",\"email\":null,\"website_url\":null,\"transport_name\":\"MADURAI RADHA TRANSPORT\",\"booking_area\":\"MADURAI\",\"stores\":null,\"store_id\":3,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"state_id\":1,\"city_id\":2,\"place_id\":5,\"address_line_1\":\"OLD NO 11A\\/2 NEW NO 20 LAWYER CHINNAI THAMBI STREET\",\"address_line_2\":\"KONDITHOPE\",\"address_line_3\":null,\"zip_code\":null,\"contact_person_name\":null,\"designation\":null,\"contact_mobile_no\":null,\"contact_email\":null,\"purchase_commission_agent_id\":null,\"commission_percentage\":\"0.00\",\"gst_no\":\"33AEPPK6992J1ZT\",\"tax_id\":null,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"pan_no\":null,\"ecc_no\":null,\"credit_limit\":\"0.00\",\"payment_terms\":null,\"bank_name\":null,\"branch\":null,\"account_number\":null,\"ifsc_code\":null,\"created_at\":\"2026-04-16T10:16:13.000000Z\",\"updated_at\":\"2026-04-16T10:16:13.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"name\":\"POOJA IMPEX\",\"code\":\"1002\",\"mobile_no\":\"9840377952\",\"email\":null,\"website_url\":null,\"transport_name\":\"MADURAI RADHA TRANSPORT\",\"booking_area\":\"MADURAI\",\"stores\":null,\"store_id\":2,\"status\":\"Active\",\"created_by\":1,\"updated_by\":1,\"state_id\":1,\"city_id\":2,\"place_id\":5,\"address_line_1\":\"OLD NO 11A\\/2 NEW NO 20 LAWYER CHINNAI THAMBI STREET\",\"address_line_2\":\"KONDITHOPE\",\"address_line_3\":null,\"zip_code\":null,\"contact_person_name\":null,\"designation\":null,\"contact_mobile_no\":null,\"contact_email\":null,\"purchase_commission_agent_id\":null,\"commission_percentage\":\"0.00\",\"gst_no\":\"33AEPPK6992J1ZT\",\"tax_id\":null,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"pan_no\":null,\"ecc_no\":null,\"credit_limit\":\"0.00\",\"payment_terms\":null,\"bank_name\":null,\"branch\":null,\"account_number\":null,\"ifsc_code\":null,\"created_at\":\"2026-04-16T10:16:13.000000Z\",\"updated_at\":\"2026-04-16T11:37:16.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:37:16'),
(150, 1, 'create', 'Purchase Order', 'purchase_orders', 3, NULL, '{\"po_number\":\"PO-0003\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":\"2\",\"commission\":\"2.00\",\"supplier_id\":\"3\",\"reference_no\":\"4242\",\"reference_date\":\"2026-04-15T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":\"2\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"150.00\",\"sub_total\":\"1500.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"30.00\",\"taxable_amount\":\"1440.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"259.20\",\"round_off_type\":\"Less\",\"round_off\":\"0.20\",\"total_amount\":\"1699.00\",\"is_self_closed\":false,\"updated_at\":\"2026-04-16T11:38:53.000000Z\",\"created_at\":\"2026-04-16T11:38:53.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:38:53'),
(151, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 3, '{\"id\":3,\"purchase_executive_id\":null,\"po_number\":\"PO-0003\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":3,\"reference_no\":\"4242\",\"reference_date\":\"2026-04-15T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":2,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"1500.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"30.00\",\"taxable_amount\":\"1440.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"259.20\",\"round_off_type\":\"Less\",\"round_off\":\"0.20\",\"total_amount\":\"1699.00\",\"created_at\":\"2026-04-16T11:38:53.000000Z\",\"updated_at\":\"2026-04-16T11:38:53.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"purchase_executive_id\":null,\"po_number\":\"PO-0003\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":3,\"reference_no\":\"4242\",\"reference_date\":\"2026-04-15T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":2,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"1500.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"30.00\",\"taxable_amount\":\"1440.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"259.20\",\"round_off_type\":\"Less\",\"round_off\":\"0.20\",\"total_amount\":\"1699.00\",\"created_at\":\"2026-04-16T11:38:53.000000Z\",\"updated_at\":\"2026-04-16T11:38:59.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:38:59'),
(152, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 2, '{\"id\":2,\"purchase_executive_id\":null,\"po_number\":\"PO-0002\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"5820\",\"reference_date\":\"2026-04-15T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"14850.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"297.00\",\"taxable_amount\":\"14256.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"712.80\",\"round_off_type\":\"Less\",\"round_off\":\"0.20\",\"total_amount\":\"14968.60\",\"created_at\":\"2026-04-16T11:36:29.000000Z\",\"updated_at\":\"2026-04-16T11:36:29.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"purchase_executive_id\":null,\"po_number\":\"PO-0002\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"5820\",\"reference_date\":\"2026-04-15T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"14850.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"297.00\",\"taxable_amount\":\"14256.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"712.80\",\"round_off_type\":\"Less\",\"round_off\":\"0.20\",\"total_amount\":\"14968.60\",\"created_at\":\"2026-04-16T11:36:29.000000Z\",\"updated_at\":\"2026-04-16T11:39:00.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:39:00'),
(153, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 1, '{\"id\":1,\"purchase_executive_id\":null,\"po_number\":\"PO-0001\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"3.00\",\"supplier_id\":3,\"reference_no\":\"6525\",\"reference_date\":\"2026-04-15T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":3,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"310.00\",\"sub_total\":\"30240.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"604.80\",\"taxable_amount\":\"28728.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"5171.04\",\"round_off_type\":\"Less\",\"round_off\":\"0.04\",\"total_amount\":\"33899.00\",\"created_at\":\"2026-04-16T11:22:10.000000Z\",\"updated_at\":\"2026-04-16T11:22:10.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"purchase_executive_id\":null,\"po_number\":\"PO-0001\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"3.00\",\"supplier_id\":3,\"reference_no\":\"6525\",\"reference_date\":\"2026-04-15T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":3,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"310.00\",\"sub_total\":\"30240.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"604.80\",\"taxable_amount\":\"28728.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"5171.04\",\"round_off_type\":\"Less\",\"round_off\":\"0.04\",\"total_amount\":\"33899.00\",\"created_at\":\"2026-04-16T11:22:10.000000Z\",\"updated_at\":\"2026-04-16T11:39:02.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:39:02'),
(154, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 1, NULL, '{\"invoice_no\":\"INV3452\",\"invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_order_id\":\"3\",\"supplier_id\":\"3\",\"po_reference\":\"PO-0003\",\"transport\":null,\"destination\":null,\"lr_no\":null,\"eway_billno\":null,\"lr_date\":null,\"indent_no\":null,\"indent_date\":null,\"sub_total\":\"1500.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"30.00\",\"taxable_amount\":\"1440.00\",\"other_state\":false,\"igst_percent\":\"0\",\"igst_amount\":\"0\",\"cgst_percent\":\"9.00\",\"cgst_amount\":\"129.60\",\"sgst_percent\":\"9.00\",\"sgst_amount\":\"129.60\",\"tax_amount\":\"259.20\",\"other_charges\":\"0.00\",\"round_off\":\"0.20\",\"round_off_type\":\"Less\",\"grand_total\":\"1699.00\",\"received_amount\":\"0\",\"due_amount\":\"1699.00\",\"invoice_status\":\"Draft\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"purchase_commission_agent_id\":\"2\",\"commission\":\"2.00\",\"commission_amount\":\"30.00\",\"updated_at\":\"2026-04-16T11:41:29.000000Z\",\"created_at\":\"2026-04-16T11:41:29.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:41:29'),
(155, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 2, NULL, '{\"invoice_no\":\"INV343\",\"invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_order_id\":\"2\",\"supplier_id\":\"2\",\"po_reference\":\"PO-0002\",\"transport\":null,\"destination\":null,\"lr_no\":null,\"eway_billno\":null,\"lr_date\":null,\"indent_no\":null,\"indent_date\":null,\"sub_total\":\"14850.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"297.00\",\"taxable_amount\":\"14256.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"igst_amount\":\"712.80\",\"cgst_percent\":\"0\",\"cgst_amount\":\"0\",\"sgst_percent\":\"0\",\"sgst_amount\":\"0\",\"tax_amount\":\"712.80\",\"other_charges\":\"0.00\",\"round_off\":\"0.20\",\"round_off_type\":\"Less\",\"grand_total\":\"14968.60\",\"received_amount\":\"0\",\"due_amount\":\"14968.60\",\"invoice_status\":\"Unpaid\\/Credit\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"purchase_commission_agent_id\":\"2\",\"commission\":\"2.00\",\"commission_amount\":\"297.00\",\"updated_at\":\"2026-04-16T11:41:44.000000Z\",\"created_at\":\"2026-04-16T11:41:44.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:41:44'),
(156, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 3, NULL, '{\"invoice_no\":\"INV987\",\"invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_order_id\":\"1\",\"supplier_id\":\"3\",\"po_reference\":\"PO-0001\",\"transport\":null,\"destination\":null,\"lr_no\":null,\"eway_billno\":null,\"lr_date\":null,\"indent_no\":null,\"indent_date\":null,\"sub_total\":\"30240.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"604.80\",\"taxable_amount\":\"28728.00\",\"other_state\":false,\"igst_percent\":\"0\",\"igst_amount\":\"0\",\"cgst_percent\":\"9.00\",\"cgst_amount\":\"2585.52\",\"sgst_percent\":\"9.00\",\"sgst_amount\":\"2585.52\",\"tax_amount\":\"5171.04\",\"other_charges\":\"0\",\"round_off\":\"0.04\",\"round_off_type\":\"Less\",\"grand_total\":\"33899.00\",\"received_amount\":\"0\",\"due_amount\":\"33899.00\",\"invoice_status\":\"Unpaid\\/Credit\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"purchase_commission_agent_id\":\"2\",\"commission\":\"3.00\",\"commission_amount\":\"907.20\",\"updated_at\":\"2026-04-16T11:42:06.000000Z\",\"created_at\":\"2026-04-16T11:42:06.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:42:06'),
(157, 1, 'create', 'GRN Entry', 'grn_entries', 1, NULL, '{\"grn_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_invoice_id\":\"3\",\"supplier_id\":3,\"supplier_invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN001\",\"created_by\":1,\"updated_at\":\"2026-04-16T11:42:40.000000Z\",\"created_at\":\"2026-04-16T11:42:40.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:42:40'),
(158, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 1, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:42:40'),
(159, 1, 'create', 'Purchase Order', 'purchase_orders', 4, NULL, '{\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":\"2\",\"commission\":\"2.00\",\"supplier_id\":\"2\",\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":\"1\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"is_self_closed\":false,\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:47:56'),
(160, 1, 'update', 'Purchase Order', 'purchase_orders', 4, '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:55:33'),
(161, 1, 'update', 'Purchase Order', 'purchase_orders', 4, '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:58:12'),
(162, 1, 'update', 'Purchase Order', 'purchase_orders', 4, '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:58:22'),
(163, 1, 'update', 'Purchase Order', 'purchase_orders', 4, '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 11:58:38'),
(164, 1, 'create', 'GRN Entry', 'grn_entries', 2, NULL, '{\"grn_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_invoice_id\":\"1\",\"supplier_id\":3,\"supplier_invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN002\",\"created_by\":1,\"updated_at\":\"2026-04-16T12:00:38.000000Z\",\"created_at\":\"2026-04-16T12:00:38.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:00:38'),
(165, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 3, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:00:38'),
(166, 1, 'create', 'GRN Entry', 'grn_entries', 3, NULL, '{\"grn_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_invoice_id\":\"2\",\"supplier_id\":2,\"supplier_invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN003\",\"created_by\":1,\"updated_at\":\"2026-04-16T12:01:07.000000Z\",\"created_at\":\"2026-04-16T12:01:07.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:01:07'),
(167, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 2, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:01:07'),
(168, 1, 'create', 'Stock Entry', 'stock_entries', 1, NULL, '{\"stock_date\":\"2026-04-16\",\"grn_entry_id\":\"1\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":null,\"remarks\":null,\"status\":\"Draft\",\"price\":0,\"stock_entry_no\":\"SE00001\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:01:16'),
(169, 1, 'create', 'Stock Entry', 'stock_entries', 2, NULL, '{\"stock_date\":\"2026-04-16\",\"grn_entry_id\":\"2\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":null,\"remarks\":null,\"status\":\"Draft\",\"price\":0,\"stock_entry_no\":\"SE00002\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:01:24'),
(170, 1, 'create', 'Stock Entry', 'stock_entries', 3, NULL, '{\"stock_date\":\"2026-04-16\",\"grn_entry_id\":\"3\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":null,\"remarks\":null,\"status\":\"Draft\",\"price\":0,\"stock_entry_no\":\"SE00003\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:01:31'),
(171, 1, 'create', 'Job Card Entry', 'job_card_entries', 1, NULL, '{\"job_card_no\":\"JC001\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":\"1\",\"issue_store_id\":\"1\",\"receipt_store_id\":\"1\",\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"season_id\":\"1\",\"brand_id\":\"1\",\"fs_qty\":null,\"hs_qty\":null,\"remarks\":null,\"status\":\"Production In Progress\",\"fit_id\":\"2\",\"patti_type_id\":\"3\",\"collar_type_id\":\"3\",\"cuff_type_id\":\"2\",\"pocket_type_id\":\"2\",\"bottom_cut_id\":\"2\",\"total_qty_fs\":\"135\",\"total_qty_hs\":\"120\",\"grand_total_qty\":255,\"process_group_id\":\"2\",\"size_ratio_id\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"fabric_type_id\":\"2\",\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"job_card_type\":\"Regular\",\"created_by\":1,\"updated_at\":\"2026-04-16T12:06:50.000000Z\",\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:06:50');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(172, 1, 'update', 'Job Card Issue Items', 'job_card_entries', 1, '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:06:50.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"purchase_order\":null,\"issue_items\":[]}', '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":\"99.00\",\"price_hs\":\"0.00\",\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"issue_items\":[{\"id\":1,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":1,\"stock_entry_item_id\":5,\"raw_material_id\":1,\"qty_issue\":\"85.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"85.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"8415.00\",\"cost_per_pc\":\"99.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:07.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null}]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:09:07'),
(173, 1, 'update', 'Job Card Issue Items', 'job_card_entries', 1, '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":\"99.00\",\"price_hs\":\"0.00\",\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"purchase_order\":null,\"issue_items\":[{\"id\":1,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":1,\"stock_entry_item_id\":5,\"raw_material_id\":1,\"qty_issue\":\"85.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"85.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"8415.00\",\"cost_per_pc\":\"99.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:07.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null}]}', '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":\"169.41\",\"price_hs\":\"0.00\",\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:09:14.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"issue_items\":[{\"id\":1,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":1,\"stock_entry_item_id\":5,\"raw_material_id\":1,\"qty_issue\":\"85.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"85.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"8415.00\",\"cost_per_pc\":\"99.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:07.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null},{\"id\":2,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":2,\"stock_entry_item_id\":1,\"raw_material_id\":1,\"qty_issue\":\"150.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"150.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"96.00\",\"total_cost\":\"14400.00\",\"cost_per_pc\":\"169.41\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:14.000000Z\",\"updated_at\":\"2026-04-16T12:09:14.000000Z\",\"deleted_at\":null}]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:09:14'),
(174, 1, 'update', 'Job Card Issue Items', 'job_card_entries', 1, '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":\"169.41\",\"price_hs\":\"0.00\",\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:09:14.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"purchase_order\":null,\"issue_items\":[{\"id\":1,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":1,\"stock_entry_item_id\":5,\"raw_material_id\":1,\"qty_issue\":\"85.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"85.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"8415.00\",\"cost_per_pc\":\"99.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:07.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null},{\"id\":2,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":2,\"stock_entry_item_id\":1,\"raw_material_id\":1,\"qty_issue\":\"150.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"150.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"96.00\",\"total_cost\":\"14400.00\",\"cost_per_pc\":\"169.41\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:14.000000Z\",\"updated_at\":\"2026-04-16T12:09:14.000000Z\",\"deleted_at\":null}]}', '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":\"186.35\",\"price_hs\":\"0.00\",\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:09:20.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"issue_items\":[{\"id\":1,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":1,\"stock_entry_item_id\":5,\"raw_material_id\":1,\"qty_issue\":\"85.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"85.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"8415.00\",\"cost_per_pc\":\"99.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:07.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null},{\"id\":2,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":2,\"stock_entry_item_id\":1,\"raw_material_id\":1,\"qty_issue\":\"150.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"150.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"96.00\",\"total_cost\":\"14400.00\",\"cost_per_pc\":\"169.41\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:14.000000Z\",\"updated_at\":\"2026-04-16T12:09:14.000000Z\",\"deleted_at\":null},{\"id\":3,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":3,\"stock_entry_item_id\":2,\"raw_material_id\":2,\"qty_issue\":\"160.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"160.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"15840.00\",\"cost_per_pc\":\"186.35\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:20.000000Z\",\"updated_at\":\"2026-04-16T12:09:20.000000Z\",\"deleted_at\":null}]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:09:20'),
(175, 1, 'update', 'Job Card Issue Items', 'job_card_entries', 1, '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":\"186.35\",\"price_hs\":\"0.00\",\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:09:20.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"purchase_order\":null,\"issue_items\":[{\"id\":1,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":1,\"stock_entry_item_id\":5,\"raw_material_id\":1,\"qty_issue\":\"85.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"85.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"8415.00\",\"cost_per_pc\":\"99.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:07.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null},{\"id\":2,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":2,\"stock_entry_item_id\":1,\"raw_material_id\":1,\"qty_issue\":\"150.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"150.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"96.00\",\"total_cost\":\"14400.00\",\"cost_per_pc\":\"169.41\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:14.000000Z\",\"updated_at\":\"2026-04-16T12:09:14.000000Z\",\"deleted_at\":null},{\"id\":3,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":3,\"stock_entry_item_id\":2,\"raw_material_id\":2,\"qty_issue\":\"160.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"160.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"15840.00\",\"cost_per_pc\":\"186.35\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:20.000000Z\",\"updated_at\":\"2026-04-16T12:09:20.000000Z\",\"deleted_at\":null}]}', '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":\"1.00\",\"price_hs\":\"0.00\",\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:09:26.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"issue_items\":[{\"id\":1,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":1,\"stock_entry_item_id\":5,\"raw_material_id\":1,\"qty_issue\":\"85.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"85.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"8415.00\",\"cost_per_pc\":\"99.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:07.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null},{\"id\":2,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":2,\"stock_entry_item_id\":1,\"raw_material_id\":1,\"qty_issue\":\"150.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"150.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"96.00\",\"total_cost\":\"14400.00\",\"cost_per_pc\":\"169.41\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:14.000000Z\",\"updated_at\":\"2026-04-16T12:09:14.000000Z\",\"deleted_at\":null},{\"id\":3,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":3,\"stock_entry_item_id\":2,\"raw_material_id\":2,\"qty_issue\":\"160.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"160.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"15840.00\",\"cost_per_pc\":\"186.35\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:20.000000Z\",\"updated_at\":\"2026-04-16T12:09:20.000000Z\",\"deleted_at\":null},{\"id\":4,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":4,\"stock_entry_item_id\":3,\"raw_material_id\":3,\"qty_issue\":\"1800.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"1620.00\",\"qty_used\":\"180.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"1800.00\",\"unit_price\":\"10.00\",\"total_cost\":\"1800.00\",\"cost_per_pc\":\"1.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:26.000000Z\",\"updated_at\":\"2026-04-16T12:09:26.000000Z\",\"deleted_at\":null}]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:09:26'),
(176, 1, 'create', 'User', 'users', 2, NULL, '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":null,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":1,\"blood_group_id\":4,\"name\":\"Kishore\",\"phone\":\"9658580210\",\"email\":\"kishore32@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:21:16.000000Z\",\"updated_at\":\"2026-04-16T12:21:16.000000Z\",\"date_of_joining\":\"2024-04-17\",\"father_name\":\"Arul\",\"father_phone\":\"6938956565\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Arapalayam Main Road\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:21:16'),
(177, 1, 'create', 'User', 'users', 3, NULL, '{\"id\":3,\"service_provider_id\":1,\"operation_stage_id\":null,\"emp_id\":\"1002\",\"department_id\":2,\"role_id\":1,\"blood_group_id\":4,\"name\":\"Pooja\",\"phone\":\"6956261616\",\"email\":\"pooja.saitech@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:22:25.000000Z\",\"updated_at\":\"2026-04-16T12:22:25.000000Z\",\"date_of_joining\":\"2026-04-16\",\"father_name\":\"Arya\",\"father_phone\":\"8520369874\",\"country\":null,\"state_id\":1,\"city_id\":2,\"address_line1\":\"25, T.Nagar\",\"address_line2\":null,\"zipcode\":\"620008\",\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:22:25'),
(178, 1, 'create', 'User', 'users', 4, NULL, '{\"id\":4,\"service_provider_id\":2,\"operation_stage_id\":null,\"emp_id\":\"1003\",\"department_id\":1,\"role_id\":1,\"blood_group_id\":5,\"name\":\"Arjun\",\"phone\":\"9685968596\",\"email\":\"arjun@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:23:20.000000Z\",\"updated_at\":\"2026-04-16T12:23:20.000000Z\",\"date_of_joining\":\"2026-04-16\",\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":4,\"city_id\":5,\"address_line1\":\"25, Efa Building road\",\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:23:20'),
(179, 1, 'create', 'User', 'users', 5, NULL, '{\"id\":5,\"service_provider_id\":2,\"operation_stage_id\":null,\"emp_id\":\"1004\",\"department_id\":1,\"role_id\":1,\"blood_group_id\":null,\"name\":\"Ganesh\",\"phone\":\"8639589635\",\"email\":\"ganesh@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:24:37.000000Z\",\"updated_at\":\"2026-04-16T12:24:37.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":1,\"city_id\":2,\"address_line1\":\"25, Kodambakkam Road\",\"address_line2\":null,\"zipcode\":\"325006\",\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:24:37'),
(180, 1, 'update', 'GRN Entry', 'grn_entries', 2, '{\"id\":2,\"grn_number\":\"GRN002\",\"grn_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_invoice_id\":1,\"supplier_id\":3,\"supplier_invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"status\":\"Received\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:00:38.000000Z\",\"updated_at\":\"2026-04-16T12:00:38.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"grn_number\":\"GRN002\",\"grn_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_invoice_id\":1,\"supplier_id\":3,\"supplier_invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"status\":\"Received\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:00:38.000000Z\",\"updated_at\":\"2026-04-16T12:35:00.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:35:00'),
(181, 1, 'update', 'GRN Entry', 'grn_entries', 3, '{\"id\":3,\"grn_number\":\"GRN003\",\"grn_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_invoice_id\":2,\"supplier_id\":2,\"supplier_invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"status\":\"Received\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:01:07.000000Z\",\"updated_at\":\"2026-04-16T12:01:07.000000Z\",\"deleted_at\":null}', '{\"id\":3,\"grn_number\":\"GRN003\",\"grn_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_invoice_id\":2,\"supplier_id\":2,\"supplier_invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"status\":\"Received\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:01:07.000000Z\",\"updated_at\":\"2026-04-16T12:35:24.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:35:24'),
(182, 1, 'update', 'GRN Entry', 'grn_entries', 2, '{\"id\":2,\"grn_number\":\"GRN002\",\"grn_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_invoice_id\":1,\"supplier_id\":3,\"supplier_invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"status\":\"Received\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:00:38.000000Z\",\"updated_at\":\"2026-04-16T12:35:00.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"grn_number\":\"GRN002\",\"grn_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_invoice_id\":1,\"supplier_id\":3,\"supplier_invoice_date\":\"2026-04-15T18:30:00.000000Z\",\"status\":\"Received\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:00:38.000000Z\",\"updated_at\":\"2026-04-16T12:35:00.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-16 12:35:37'),
(183, 1, 'create', 'Purchase Order', 'purchase_orders', 5, NULL, '{\"po_number\":\"PO-0005\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":\"2\",\"commission\":\"2.00\",\"supplier_id\":\"3\",\"reference_no\":\"1010\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":\"2\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"280.00\",\"sub_total\":\"2240.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"2195.20\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"395.14\",\"round_off_type\":\"Less\",\"round_off\":\"0.34\",\"total_amount\":\"2590.00\",\"is_self_closed\":false,\"updated_at\":\"2026-04-17T05:27:07.000000Z\",\"created_at\":\"2026-04-17T05:27:07.000000Z\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:27:07'),
(184, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 5, '{\"id\":5,\"purchase_executive_id\":null,\"po_number\":\"PO-0005\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":3,\"reference_no\":\"1010\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":2,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"280.00\",\"sub_total\":\"2240.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"2195.20\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"395.14\",\"round_off_type\":\"Less\",\"round_off\":\"0.34\",\"total_amount\":\"2590.00\",\"created_at\":\"2026-04-17T05:27:07.000000Z\",\"updated_at\":\"2026-04-17T05:27:07.000000Z\",\"deleted_at\":null}', '{\"id\":5,\"purchase_executive_id\":null,\"po_number\":\"PO-0005\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":3,\"reference_no\":\"1010\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":2,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"280.00\",\"sub_total\":\"2240.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"2195.20\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"395.14\",\"round_off_type\":\"Less\",\"round_off\":\"0.34\",\"total_amount\":\"2590.00\",\"created_at\":\"2026-04-17T05:27:07.000000Z\",\"updated_at\":\"2026-04-17T05:27:15.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:27:15'),
(185, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 4, '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-16T11:47:56.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"purchase_executive_id\":null,\"po_number\":\"PO-0004\",\"po_date\":\"2026-04-15T18:30:00.000000Z\",\"purchase_commission_agent_id\":2,\"commission\":\"2.00\",\"supplier_id\":2,\"reference_no\":\"233\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-07T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"661.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.50\",\"total_amount\":\"13892.00\",\"created_at\":\"2026-04-16T11:47:56.000000Z\",\"updated_at\":\"2026-04-17T05:27:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:27:17'),
(186, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 4, NULL, '{\"invoice_no\":\"INV007\",\"invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_order_id\":\"4\",\"supplier_id\":\"2\",\"po_reference\":\"PO-0004\",\"transport\":null,\"destination\":null,\"lr_no\":null,\"eway_billno\":null,\"lr_date\":null,\"indent_no\":null,\"indent_date\":null,\"sub_total\":\"13500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"13230.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"igst_amount\":\"661.50\",\"cgst_percent\":\"0\",\"cgst_amount\":\"0\",\"sgst_percent\":\"0\",\"sgst_amount\":\"0\",\"tax_amount\":\"661.50\",\"other_charges\":\"0.00\",\"round_off\":\"0.50\",\"round_off_type\":\"Add\",\"grand_total\":\"13892.00\",\"received_amount\":\"0\",\"due_amount\":\"13892.00\",\"invoice_status\":\"Draft\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"purchase_commission_agent_id\":\"2\",\"commission\":\"2.00\",\"commission_amount\":\"270.00\",\"updated_at\":\"2026-04-17T05:27:42.000000Z\",\"created_at\":\"2026-04-17T05:27:42.000000Z\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:27:42'),
(187, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 5, NULL, '{\"invoice_no\":\"INV0945\",\"invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_order_id\":\"5\",\"supplier_id\":\"3\",\"po_reference\":\"PO-0005\",\"transport\":null,\"destination\":null,\"lr_no\":null,\"eway_billno\":null,\"lr_date\":null,\"indent_no\":null,\"indent_date\":null,\"sub_total\":\"2240.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"2195.20\",\"other_state\":false,\"igst_percent\":\"0\",\"igst_amount\":\"0\",\"cgst_percent\":\"9.00\",\"cgst_amount\":\"197.57\",\"sgst_percent\":\"9.00\",\"sgst_amount\":\"197.57\",\"tax_amount\":\"395.14\",\"other_charges\":\"0.00\",\"round_off\":\"0.34\",\"round_off_type\":\"Less\",\"grand_total\":\"2590.00\",\"received_amount\":\"0\",\"due_amount\":\"2590.00\",\"invoice_status\":\"Unpaid\\/Credit\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"purchase_commission_agent_id\":\"2\",\"commission\":\"2.00\",\"commission_amount\":\"44.80\",\"updated_at\":\"2026-04-17T05:28:02.000000Z\",\"created_at\":\"2026-04-17T05:28:02.000000Z\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:28:02'),
(188, 1, 'create', 'GRN Entry', 'grn_entries', 4, NULL, '{\"grn_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_invoice_id\":\"4\",\"supplier_id\":2,\"supplier_invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN004\",\"created_by\":1,\"updated_at\":\"2026-04-17T05:31:53.000000Z\",\"created_at\":\"2026-04-17T05:31:53.000000Z\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:31:53'),
(189, 1, 'update', 'GRN Entry', 'grn_entries', 4, '{\"id\":4,\"grn_number\":\"GRN004\",\"grn_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_invoice_id\":4,\"supplier_id\":2,\"supplier_invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"status\":\"Received\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-17T05:31:53.000000Z\",\"updated_at\":\"2026-04-17T05:31:53.000000Z\",\"deleted_at\":null}', '{\"id\":4,\"grn_number\":\"GRN004\",\"grn_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_invoice_id\":4,\"supplier_id\":2,\"supplier_invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"status\":\"Received\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-17T05:31:53.000000Z\",\"updated_at\":\"2026-04-17T05:33:16.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:33:16'),
(190, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 4, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:33:16'),
(191, 1, 'create', 'GRN Entry', 'grn_entries', 5, NULL, '{\"grn_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_invoice_id\":\"5\",\"supplier_id\":3,\"supplier_invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN005\",\"created_by\":1,\"updated_at\":\"2026-04-17T05:33:39.000000Z\",\"created_at\":\"2026-04-17T05:33:39.000000Z\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:33:39'),
(192, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 5, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:33:39'),
(193, 1, 'create', 'Stock Entry', 'stock_entries', 4, NULL, '{\"stock_date\":\"2026-04-17\",\"grn_entry_id\":\"4\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":null,\"remarks\":null,\"status\":\"Draft\",\"price\":0,\"stock_entry_no\":\"SE00004\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:33:48'),
(194, 1, 'create', 'Stock Entry', 'stock_entries', 5, NULL, '{\"stock_date\":\"2026-04-17\",\"grn_entry_id\":\"5\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":null,\"remarks\":null,\"status\":\"Draft\",\"price\":0,\"stock_entry_no\":\"SE00005\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 05:33:54'),
(195, 1, 'create', 'Job Card Entry', 'job_card_entries', 2, NULL, '{\"job_card_no\":\"JC002\",\"reference_no\":\"JC002\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"4::art|CF-34935\\\",\\\"4::art|CF-34934\\\",\\\"3::art|CF-09093\\\",\\\"2::art|CF-34346\\\"]\",\"service_provider_id\":\"1\",\"issue_store_id\":\"1\",\"receipt_store_id\":\"1\",\"job_card_date\":\"2026-04-17\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"season_id\":null,\"brand_id\":\"3\",\"fs_qty\":null,\"hs_qty\":null,\"remarks\":null,\"status\":\"Production In Progress\",\"fit_id\":\"2\",\"patti_type_id\":\"3\",\"collar_type_id\":\"4\",\"cuff_type_id\":\"2\",\"pocket_type_id\":\"2\",\"bottom_cut_id\":\"2\",\"total_qty_fs\":\"135\",\"total_qty_hs\":\"75\",\"grand_total_qty\":210,\"process_group_id\":\"2\",\"size_ratio_id\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"fabric_type_id\":\"1\",\"sleeve_instances\":{\"instances\":[{\"id\":1776405547414.9329,\"type\":\"fs\"},{\"id\":1776405547623.6736,\"type\":\"fs\"},{\"id\":1776405547991.6877,\"type\":\"hs\"}],\"values\":{\"1776405547414.9329\":{\"36\":\"0\",\"38\":\"5\",\"40\":\"5\",\"42\":\"5\",\"44\":\"5\"},\"1776405547623.6736\":{\"36\":\"10\",\"38\":\"10\",\"40\":\"10\",\"42\":\"0\",\"44\":\"3\"},\"1776405547991.6877\":{\"36\":\"0\",\"38\":\"5\",\"40\":\"5\",\"42\":\"5\",\"44\":\"3\"}}},\"job_card_type\":\"Regular\",\"created_by\":1,\"updated_at\":\"2026-04-17T06:02:16.000000Z\",\"created_at\":\"2026-04-17T06:02:16.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:02:16'),
(196, 1, 'create', 'Purchase Order', 'purchase_orders', 6, NULL, '{\"po_number\":\"PO-0006\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":\"1\",\"commission\":\"3.00\",\"supplier_id\":\"2\",\"reference_no\":\"34343\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":\"1\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"150.00\",\"sub_total\":\"14400.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"288.00\",\"taxable_amount\":\"13680.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"684.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"14364.00\",\"is_self_closed\":false,\"updated_at\":\"2026-04-17T06:02:58.000000Z\",\"created_at\":\"2026-04-17T06:02:58.000000Z\",\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:02:58'),
(197, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 6, '{\"id\":6,\"purchase_executive_id\":null,\"po_number\":\"PO-0006\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":1,\"commission\":\"3.00\",\"supplier_id\":2,\"reference_no\":\"34343\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"14400.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"288.00\",\"taxable_amount\":\"13680.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"684.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"14364.00\",\"created_at\":\"2026-04-17T06:02:58.000000Z\",\"updated_at\":\"2026-04-17T06:02:58.000000Z\",\"deleted_at\":null}', '{\"id\":6,\"purchase_executive_id\":null,\"po_number\":\"PO-0006\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":1,\"commission\":\"3.00\",\"supplier_id\":2,\"reference_no\":\"34343\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"14400.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"288.00\",\"taxable_amount\":\"13680.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"684.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"14364.00\",\"created_at\":\"2026-04-17T06:02:58.000000Z\",\"updated_at\":\"2026-04-17T06:03:01.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:03:01'),
(198, 1, 'create', 'Purchase Order', 'purchase_orders', 7, NULL, '{\"po_number\":\"PO-0007\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":\"3\",\"reference_no\":\"3434\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":\"2\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"250.00\",\"sub_total\":\"1250.00\",\"discount_percent\":\"4.00\",\"discount_amount\":\"50.00\",\"taxable_amount\":\"1200.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"216.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"1416.00\",\"is_self_closed\":false,\"updated_at\":\"2026-04-17T06:03:37.000000Z\",\"created_at\":\"2026-04-17T06:03:37.000000Z\",\"id\":7}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:03:37');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(199, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 7, '{\"id\":7,\"purchase_executive_id\":null,\"po_number\":\"PO-0007\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":3,\"reference_no\":\"3434\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":2,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"250.00\",\"sub_total\":\"1250.00\",\"discount_percent\":\"4.00\",\"discount_amount\":\"50.00\",\"taxable_amount\":\"1200.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"216.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"1416.00\",\"created_at\":\"2026-04-17T06:03:37.000000Z\",\"updated_at\":\"2026-04-17T06:03:37.000000Z\",\"deleted_at\":null}', '{\"id\":7,\"purchase_executive_id\":null,\"po_number\":\"PO-0007\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":3,\"reference_no\":\"3434\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":2,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"250.00\",\"sub_total\":\"1250.00\",\"discount_percent\":\"4.00\",\"discount_amount\":\"50.00\",\"taxable_amount\":\"1200.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"216.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"1416.00\",\"created_at\":\"2026-04-17T06:03:37.000000Z\",\"updated_at\":\"2026-04-17T06:03:41.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:03:41'),
(200, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 6, NULL, '{\"invoice_no\":\"INV002\",\"invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_order_id\":\"6\",\"supplier_id\":\"2\",\"po_reference\":\"PO-0006\",\"transport\":null,\"destination\":null,\"lr_no\":null,\"eway_billno\":null,\"lr_date\":null,\"indent_no\":null,\"indent_date\":null,\"sub_total\":\"14400.00\",\"discount_percent\":\"2.00\",\"discount_amount\":\"288.00\",\"taxable_amount\":\"13680.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"igst_amount\":\"684.00\",\"cgst_percent\":\"0\",\"cgst_amount\":\"0\",\"sgst_percent\":\"0\",\"sgst_amount\":\"0\",\"tax_amount\":\"684.00\",\"other_charges\":\"0.00\",\"round_off\":\"0.00\",\"round_off_type\":\"Less\",\"grand_total\":\"14364.00\",\"received_amount\":\"0\",\"due_amount\":\"14364.00\",\"invoice_status\":\"Unpaid\\/Credit\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"purchase_commission_agent_id\":\"1\",\"commission\":\"3.00\",\"commission_amount\":\"432.00\",\"updated_at\":\"2026-04-17T06:04:00.000000Z\",\"created_at\":\"2026-04-17T06:04:00.000000Z\",\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:04:00'),
(201, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 7, NULL, '{\"invoice_no\":\"INV003\",\"invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_order_id\":\"7\",\"supplier_id\":\"3\",\"po_reference\":\"PO-0007\",\"transport\":null,\"destination\":null,\"lr_no\":null,\"eway_billno\":null,\"lr_date\":null,\"indent_no\":null,\"indent_date\":null,\"sub_total\":\"1250.00\",\"discount_percent\":\"4.00\",\"discount_amount\":\"50.00\",\"taxable_amount\":\"1200.00\",\"other_state\":false,\"igst_percent\":\"0\",\"igst_amount\":\"0\",\"cgst_percent\":\"9.00\",\"cgst_amount\":\"108.00\",\"sgst_percent\":\"9.00\",\"sgst_amount\":\"108.00\",\"tax_amount\":\"216.00\",\"other_charges\":\"0.00\",\"round_off\":\"0.00\",\"round_off_type\":\"Add\",\"grand_total\":\"1416.00\",\"received_amount\":\"0\",\"due_amount\":\"1416.00\",\"invoice_status\":\"Unpaid\\/Credit\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"commission_amount\":\"0.00\",\"updated_at\":\"2026-04-17T06:04:17.000000Z\",\"created_at\":\"2026-04-17T06:04:17.000000Z\",\"id\":7}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:04:17'),
(202, 1, 'create', 'GRN Entry', 'grn_entries', 6, NULL, '{\"grn_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_invoice_id\":\"6\",\"supplier_id\":2,\"supplier_invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN006\",\"created_by\":1,\"updated_at\":\"2026-04-17T06:04:55.000000Z\",\"created_at\":\"2026-04-17T06:04:55.000000Z\",\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:04:55'),
(203, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 6, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:04:55'),
(204, 1, 'create', 'GRN Entry', 'grn_entries', 7, NULL, '{\"grn_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_invoice_id\":\"7\",\"supplier_id\":3,\"supplier_invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN007\",\"created_by\":1,\"updated_at\":\"2026-04-17T06:05:17.000000Z\",\"created_at\":\"2026-04-17T06:05:17.000000Z\",\"id\":7}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:05:17'),
(205, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 7, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:05:17'),
(206, 1, 'create', 'Stock Entry', 'stock_entries', 6, NULL, '{\"stock_date\":\"2026-04-17\",\"grn_entry_id\":\"7\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":null,\"remarks\":null,\"status\":\"Draft\",\"price\":0,\"stock_entry_no\":\"SE00006\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:05:24'),
(207, 1, 'create', 'Stock Entry', 'stock_entries', 7, NULL, '{\"stock_date\":\"2026-04-17\",\"grn_entry_id\":\"6\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":null,\"remarks\":null,\"status\":\"Draft\",\"price\":0,\"stock_entry_no\":\"SE00007\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:05:30'),
(208, 1, 'create', 'Job Card Entry', 'job_card_entries', 3, NULL, '{\"job_card_no\":\"JC003\",\"reference_no\":\"JC003\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"7::art|CF-03489\\\",\\\"7::art|CF-03480\\\",\\\"4::art|CF-34934\\\",\\\"6::art|CF-34937\\\"]\",\"service_provider_id\":\"1\",\"issue_store_id\":\"1\",\"receipt_store_id\":\"3\",\"job_card_date\":\"2026-04-17\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"season_id\":null,\"brand_id\":\"3\",\"fs_qty\":null,\"hs_qty\":null,\"remarks\":null,\"status\":\"Production In Progress\",\"fit_id\":\"2\",\"patti_type_id\":\"2\",\"collar_type_id\":\"4\",\"cuff_type_id\":\"2\",\"pocket_type_id\":\"1\",\"bottom_cut_id\":\"2\",\"total_qty_fs\":\"180\",\"total_qty_hs\":\"150\",\"grand_total_qty\":330,\"process_group_id\":\"2\",\"size_ratio_id\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"fabric_type_id\":\"2\",\"sleeve_instances\":{\"instances\":[{\"id\":1776407422319.4292,\"type\":\"fs\"},{\"id\":1776407422492.016,\"type\":\"fs\"},{\"id\":1776407423230.0393,\"type\":\"hs\"}],\"values\":{\"1776407422319.4292\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"15\"},\"1776407422492.016\":{\"36\":\"12\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"0\"},\"1776407423230.0393\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"0\",\"44\":\"0\"}}},\"job_card_type\":\"Regular\",\"created_by\":1,\"updated_at\":\"2026-04-17T06:33:06.000000Z\",\"created_at\":\"2026-04-17T06:33:06.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:33:06'),
(209, 1, 'update', 'Job Card Entry', 'job_card_entries', 3, '{\"id\":3,\"job_card_no\":\"JC003\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC003\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"7::art|CF-03489\\\",\\\"7::art|CF-03480\\\",\\\"4::art|CF-34934\\\",\\\"6::art|CF-34937\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":3,\"fit_id\":2,\"patti_type_id\":2,\"collar_type_id\":4,\"cuff_type_id\":2,\"pocket_type_id\":1,\"bottom_cut_id\":2,\"brand_id\":3,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":null,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-17\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"180.00\",\"total_qty_hs\":\"150.00\",\"grand_total_qty\":\"330.00\",\"average\":\"3.70\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-17T06:33:06.000000Z\",\"updated_at\":\"2026-04-17T06:33:06.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776407422319.4292,\"type\":\"fs\"},{\"id\":1776407422492.016,\"type\":\"fs\"},{\"id\":1776407423230.0393,\"type\":\"hs\"}],\"values\":{\"1776407422319.4292\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"15\"},\"1776407422492.016\":{\"36\":\"12\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"0\"},\"1776407423230.0393\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"0\",\"44\":\"0\"}}},\"issue_items\":[]}', '{\"id\":3,\"job_card_no\":\"JC003\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC003\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"7::art|CF-03489\\\",\\\"7::art|CF-03480\\\",\\\"4::art|CF-34934\\\",\\\"6::art|CF-34937\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":3,\"fit_id\":2,\"patti_type_id\":2,\"collar_type_id\":4,\"cuff_type_id\":2,\"pocket_type_id\":1,\"bottom_cut_id\":2,\"brand_id\":3,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":null,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-17\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"180.00\",\"total_qty_hs\":\"150.00\",\"grand_total_qty\":\"330.00\",\"average\":\"3.70\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-17T06:33:06.000000Z\",\"updated_at\":\"2026-04-17T06:39:34.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776407422319.4292,\"type\":\"fs\"},{\"id\":1776407422492.016,\"type\":\"fs\"},{\"id\":1776407423230.0393,\"type\":\"hs\"}],\"values\":{\"1776407422319.4292\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"15\"},\"1776407422492.016\":{\"36\":\"12\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"0\"},\"1776407423230.0393\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"0\",\"44\":\"0\"}}}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:39:34'),
(210, 1, 'create', 'Purchase Order', 'purchase_orders', 8, NULL, '{\"po_number\":\"PO-0008\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":\"3\",\"commission\":\"3.00\",\"supplier_id\":\"3\",\"reference_no\":\"6767\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":\"2\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"160.00\",\"sub_total\":\"800.00\",\"discount_percent\":\"5.00\",\"discount_amount\":\"40.00\",\"taxable_amount\":\"736.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"132.48\",\"round_off_type\":\"Less\",\"round_off\":\"0.48\",\"total_amount\":\"868.00\",\"is_self_closed\":false,\"updated_at\":\"2026-04-17T06:40:33.000000Z\",\"created_at\":\"2026-04-17T06:40:33.000000Z\",\"id\":8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:40:33'),
(211, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 8, '{\"id\":8,\"purchase_executive_id\":null,\"po_number\":\"PO-0008\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":3,\"commission\":\"3.00\",\"supplier_id\":3,\"reference_no\":\"6767\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":2,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"160.00\",\"sub_total\":\"800.00\",\"discount_percent\":\"5.00\",\"discount_amount\":\"40.00\",\"taxable_amount\":\"736.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"132.48\",\"round_off_type\":\"Less\",\"round_off\":\"0.48\",\"total_amount\":\"868.00\",\"created_at\":\"2026-04-17T06:40:33.000000Z\",\"updated_at\":\"2026-04-17T06:40:33.000000Z\",\"deleted_at\":null}', '{\"id\":8,\"purchase_executive_id\":null,\"po_number\":\"PO-0008\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":3,\"commission\":\"3.00\",\"supplier_id\":3,\"reference_no\":\"6767\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":2,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"160.00\",\"sub_total\":\"800.00\",\"discount_percent\":\"5.00\",\"discount_amount\":\"40.00\",\"taxable_amount\":\"736.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"132.48\",\"round_off_type\":\"Less\",\"round_off\":\"0.48\",\"total_amount\":\"868.00\",\"created_at\":\"2026-04-17T06:40:33.000000Z\",\"updated_at\":\"2026-04-17T06:40:36.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:40:36'),
(212, 1, 'create', 'Purchase Order', 'purchase_orders', 9, NULL, '{\"po_number\":\"PO-0009\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":\"2\",\"reference_no\":\"67767\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":\"1\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"200.00\",\"sub_total\":\"20000.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"20000.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"1000.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"21000.00\",\"is_self_closed\":false,\"updated_at\":\"2026-04-17T06:41:13.000000Z\",\"created_at\":\"2026-04-17T06:41:13.000000Z\",\"id\":9}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:41:13'),
(213, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 9, '{\"id\":9,\"purchase_executive_id\":null,\"po_number\":\"PO-0009\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":2,\"reference_no\":\"67767\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"200.00\",\"sub_total\":\"20000.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"20000.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"1000.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"21000.00\",\"created_at\":\"2026-04-17T06:41:13.000000Z\",\"updated_at\":\"2026-04-17T06:41:13.000000Z\",\"deleted_at\":null}', '{\"id\":9,\"purchase_executive_id\":null,\"po_number\":\"PO-0009\",\"po_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":2,\"reference_no\":\"67767\",\"reference_date\":\"2026-04-16T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"200.00\",\"sub_total\":\"20000.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"20000.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"cgst_percent\":\"0.00\",\"sgst_percent\":\"0.00\",\"tax_amount\":\"1000.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"21000.00\",\"created_at\":\"2026-04-17T06:41:13.000000Z\",\"updated_at\":\"2026-04-17T06:41:16.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:41:16'),
(214, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 8, NULL, '{\"invoice_no\":\"INV0093\",\"invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_order_id\":\"8\",\"supplier_id\":\"3\",\"po_reference\":\"PO-0008\",\"transport\":null,\"destination\":null,\"lr_no\":null,\"eway_billno\":null,\"lr_date\":null,\"indent_no\":null,\"indent_date\":null,\"sub_total\":\"800.00\",\"discount_percent\":\"5.00\",\"discount_amount\":\"40.00\",\"taxable_amount\":\"736.00\",\"other_state\":false,\"igst_percent\":\"0\",\"igst_amount\":\"0\",\"cgst_percent\":\"9.00\",\"cgst_amount\":\"66.24\",\"sgst_percent\":\"9.00\",\"sgst_amount\":\"66.24\",\"tax_amount\":\"132.48\",\"other_charges\":\"0.00\",\"round_off\":\"0.48\",\"round_off_type\":\"Less\",\"grand_total\":\"868.00\",\"received_amount\":\"0\",\"due_amount\":\"868.00\",\"invoice_status\":\"Draft\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"purchase_commission_agent_id\":\"3\",\"commission\":\"3.00\",\"commission_amount\":\"24.00\",\"updated_at\":\"2026-04-17T06:41:35.000000Z\",\"created_at\":\"2026-04-17T06:41:35.000000Z\",\"id\":8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:41:35'),
(215, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 9, NULL, '{\"invoice_no\":\"INV0094\",\"invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_order_id\":\"9\",\"supplier_id\":\"2\",\"po_reference\":\"PO-0009\",\"transport\":null,\"destination\":null,\"lr_no\":null,\"eway_billno\":null,\"lr_date\":null,\"indent_no\":null,\"indent_date\":null,\"sub_total\":\"20000.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"20000.00\",\"other_state\":true,\"igst_percent\":\"5.00\",\"igst_amount\":\"1000.00\",\"cgst_percent\":\"0\",\"cgst_amount\":\"0\",\"sgst_percent\":\"0\",\"sgst_amount\":\"0\",\"tax_amount\":\"1000.00\",\"other_charges\":\"0.00\",\"round_off\":\"0.00\",\"round_off_type\":\"Add\",\"grand_total\":\"21000.00\",\"received_amount\":\"0\",\"due_amount\":\"21000.00\",\"invoice_status\":\"Partially Paid\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"commission_amount\":\"0.00\",\"updated_at\":\"2026-04-17T06:41:52.000000Z\",\"created_at\":\"2026-04-17T06:41:52.000000Z\",\"id\":9}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:41:52'),
(216, 1, 'create', 'GRN Entry', 'grn_entries', 8, NULL, '{\"grn_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_invoice_id\":\"8\",\"supplier_id\":3,\"supplier_invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN008\",\"created_by\":1,\"updated_at\":\"2026-04-17T06:42:13.000000Z\",\"created_at\":\"2026-04-17T06:42:13.000000Z\",\"id\":8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:42:13'),
(217, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 8, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:42:13'),
(218, 1, 'create', 'GRN Entry', 'grn_entries', 9, NULL, '{\"grn_date\":\"2026-04-16T18:30:00.000000Z\",\"purchase_invoice_id\":\"9\",\"supplier_id\":2,\"supplier_invoice_date\":\"2026-04-16T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN009\",\"created_by\":1,\"updated_at\":\"2026-04-17T06:42:40.000000Z\",\"created_at\":\"2026-04-17T06:42:40.000000Z\",\"id\":9}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:42:40'),
(219, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 9, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:42:40'),
(220, 1, 'create', 'Stock Entry', 'stock_entries', 8, NULL, '{\"stock_date\":\"2026-04-17\",\"grn_entry_id\":\"9\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":null,\"remarks\":null,\"status\":\"Draft\",\"price\":0,\"stock_entry_no\":\"SE00008\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:42:49'),
(221, 1, 'create', 'Stock Entry', 'stock_entries', 9, NULL, '{\"stock_date\":\"2026-04-17\",\"grn_entry_id\":\"8\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":null,\"remarks\":null,\"status\":\"Draft\",\"price\":0,\"stock_entry_no\":\"SE00009\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 06:42:54'),
(222, 1, 'update', 'Job Card Entry', 'job_card_entries', 3, '{\"id\":3,\"job_card_no\":\"JC003\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC003\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"7::art|CF-03489\\\",\\\"7::art|CF-03480\\\",\\\"4::art|CF-34934\\\",\\\"6::art|CF-34937\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":3,\"fit_id\":2,\"patti_type_id\":2,\"collar_type_id\":4,\"cuff_type_id\":2,\"pocket_type_id\":1,\"bottom_cut_id\":2,\"brand_id\":3,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":null,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-17\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"180.00\",\"total_qty_hs\":\"150.00\",\"grand_total_qty\":\"330.00\",\"average\":\"10.86\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-17T06:33:06.000000Z\",\"updated_at\":\"2026-04-17T06:39:34.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776407422319.4292,\"type\":\"fs\"},{\"id\":1776407422492.016,\"type\":\"fs\"},{\"id\":1776407423230.0393,\"type\":\"hs\"}],\"values\":{\"1776407422319.4292\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"15\"},\"1776407422492.016\":{\"36\":\"12\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"0\"},\"1776407423230.0393\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"0\",\"44\":\"0\"}}},\"issue_items\":[]}', '{\"id\":3,\"job_card_no\":\"JC003\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC003\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"7::art|CF-03489\\\",\\\"7::art|CF-03480\\\",\\\"4::art|CF-34934\\\",\\\"6::art|CF-34937\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":3,\"fit_id\":2,\"patti_type_id\":2,\"collar_type_id\":4,\"cuff_type_id\":2,\"pocket_type_id\":1,\"bottom_cut_id\":2,\"brand_id\":3,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":null,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-17\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"180.00\",\"total_qty_hs\":\"150.00\",\"grand_total_qty\":\"330.00\",\"average\":\"10.86\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-17T06:33:06.000000Z\",\"updated_at\":\"2026-04-17T08:14:25.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776407422319.4292,\"type\":\"fs\"},{\"id\":1776407422492.016,\"type\":\"fs\"},{\"id\":1776407423230.0393,\"type\":\"hs\"}],\"values\":{\"1776407422319.4292\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"15\"},\"1776407422492.016\":{\"36\":\"12\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"0\"},\"1776407423230.0393\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"0\",\"44\":\"0\"}}}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 08:14:25'),
(223, 1, 'update', 'Job Card Entry', 'job_card_entries', 3, '{\"id\":3,\"job_card_no\":\"JC003\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC003\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"7::art|CF-03489\\\",\\\"7::art|CF-03480\\\",\\\"4::art|CF-34934\\\",\\\"6::art|CF-34937\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":3,\"fit_id\":2,\"patti_type_id\":2,\"collar_type_id\":4,\"cuff_type_id\":2,\"pocket_type_id\":1,\"bottom_cut_id\":2,\"brand_id\":3,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":null,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-17\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"180.00\",\"total_qty_hs\":\"150.00\",\"grand_total_qty\":\"330.00\",\"average\":\"10.86\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-17T06:33:06.000000Z\",\"updated_at\":\"2026-04-17T08:14:25.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776407422319.4292,\"type\":\"fs\"},{\"id\":1776407422492.016,\"type\":\"fs\"},{\"id\":1776407423230.0393,\"type\":\"hs\"}],\"values\":{\"1776407422319.4292\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"15\"},\"1776407422492.016\":{\"36\":\"12\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"0\"},\"1776407423230.0393\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"0\",\"44\":\"0\"}}},\"issue_items\":[]}', '{\"id\":3,\"job_card_no\":\"JC003\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC003\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"7::art|CF-03489\\\",\\\"7::art|CF-03480\\\",\\\"4::art|CF-34934\\\",\\\"6::art|CF-34937\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":3,\"fit_id\":2,\"patti_type_id\":2,\"collar_type_id\":4,\"cuff_type_id\":2,\"pocket_type_id\":1,\"bottom_cut_id\":2,\"brand_id\":3,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-17\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"180.00\",\"total_qty_hs\":\"150.00\",\"grand_total_qty\":\"330.00\",\"average\":\"10.86\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-17T06:33:06.000000Z\",\"updated_at\":\"2026-04-17T08:55:30.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776407422319.4292,\"type\":\"fs\"},{\"id\":1776407422492.016,\"type\":\"fs\"},{\"id\":1776407423230.0393,\"type\":\"hs\"}],\"values\":{\"1776407422319.4292\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"15\"},\"1776407422492.016\":{\"36\":\"12\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"0\"},\"1776407423230.0393\":{\"36\":\"0\",\"38\":\"15\",\"40\":\"15\",\"42\":\"0\",\"44\":\"0\"}}}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-17 08:55:30'),
(224, 1, 'create', 'Job Card Entry', 'job_card_entries', 4, NULL, '{\"job_card_no\":\"JC004\",\"reference_no\":\"JC004\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"4::art|CF-34935\\\",\\\"8::art|CF-349301\\\"]\",\"service_provider_id\":\"1\",\"issue_store_id\":\"1\",\"receipt_store_id\":\"3\",\"job_card_date\":\"2026-04-20\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"season_id\":\"1\",\"brand_id\":\"1\",\"fs_qty\":null,\"hs_qty\":null,\"remarks\":null,\"status\":\"Production In Progress\",\"fit_id\":\"1\",\"patti_type_id\":\"2\",\"collar_type_id\":\"2\",\"cuff_type_id\":\"1\",\"pocket_type_id\":\"2\",\"bottom_cut_id\":\"1\",\"total_qty_fs\":\"76\",\"total_qty_hs\":\"78\",\"grand_total_qty\":154,\"process_group_id\":\"2\",\"size_ratio_id\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"fabric_type_id\":\"1\",\"sleeve_instances\":{\"instances\":[{\"id\":1776660676650.4226,\"type\":\"fs\"},{\"id\":1776660676825.516,\"type\":\"fs\"},{\"id\":1776660677210.1726,\"type\":\"hs\"}],\"values\":{\"1776660676650.4226\":{\"36\":\"0\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"10\"},\"1776660676825.516\":{\"36\":\"5\",\"38\":\"5\",\"40\":\"5\",\"42\":\"0\",\"44\":\"5\"},\"1776660677210.1726\":{\"36\":\"5\",\"38\":\"6\",\"40\":\"6\",\"42\":\"6\",\"44\":\"3\"}}},\"job_card_type\":\"Regular\",\"created_by\":1,\"updated_at\":\"2026-04-20T04:52:33.000000Z\",\"created_at\":\"2026-04-20T04:52:33.000000Z\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 04:52:33'),
(225, 1, 'update', 'Role', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-16T08:56:57.000000Z\",\"updated_at\":\"2026-04-16T08:56:57.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":1,\"module\":\"roles\",\"action\":\"create\",\"label\":\"Create Roles\",\"name\":\"create roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":1}},{\"id\":2,\"module\":\"roles\",\"action\":\"edit\",\"label\":\"Edit Roles\",\"name\":\"edit roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":2}},{\"id\":4,\"module\":\"roles\",\"action\":\"view\",\"label\":\"View Roles\",\"name\":\"view roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":4}},{\"id\":3,\"module\":\"roles\",\"action\":\"delete\",\"label\":\"Delete Roles\",\"name\":\"delete roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":3}},{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":7}},{\"id\":170,\"module\":\"purchase-invoice\",\"action\":\"create\",\"label\":\"Create Purchase Invoice\",\"name\":\"create purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":170}},{\"id\":171,\"module\":\"purchase-invoice\",\"action\":\"edit\",\"label\":\"Edit Purchase Invoice\",\"name\":\"edit purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":171}},{\"id\":172,\"module\":\"purchase-invoice\",\"action\":\"view\",\"label\":\"View Purchase Invoice\",\"name\":\"view purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":172}},{\"id\":173,\"module\":\"purchase-invoice\",\"action\":\"view_details\",\"label\":\"View_details Purchase Invoice\",\"name\":\"view_details purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":173}},{\"id\":174,\"module\":\"grn-entry\",\"action\":\"create\",\"label\":\"Create Grn Entry\",\"name\":\"create grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":174}}]}', '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-16T08:56:57.000000Z\",\"updated_at\":\"2026-04-16T08:56:57.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:21:44'),
(226, 1, 'update', 'Role', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-16T08:56:57.000000Z\",\"updated_at\":\"2026-04-16T08:56:57.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":1,\"module\":\"roles\",\"action\":\"create\",\"label\":\"Create Roles\",\"name\":\"create roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":1}},{\"id\":2,\"module\":\"roles\",\"action\":\"edit\",\"label\":\"Edit Roles\",\"name\":\"edit roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":2}},{\"id\":4,\"module\":\"roles\",\"action\":\"view\",\"label\":\"View Roles\",\"name\":\"view roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":4}},{\"id\":3,\"module\":\"roles\",\"action\":\"delete\",\"label\":\"Delete Roles\",\"name\":\"delete roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":3}},{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":7}},{\"id\":170,\"module\":\"purchase-invoice\",\"action\":\"create\",\"label\":\"Create Purchase Invoice\",\"name\":\"create purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":170}},{\"id\":171,\"module\":\"purchase-invoice\",\"action\":\"edit\",\"label\":\"Edit Purchase Invoice\",\"name\":\"edit purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":171}},{\"id\":172,\"module\":\"purchase-invoice\",\"action\":\"view\",\"label\":\"View Purchase Invoice\",\"name\":\"view purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":172}},{\"id\":174,\"module\":\"grn-entry\",\"action\":\"create\",\"label\":\"Create Grn Entry\",\"name\":\"create grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":174}}]}', '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-16T08:56:57.000000Z\",\"updated_at\":\"2026-04-16T08:56:57.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:22:25'),
(227, 1, 'create', 'Role', 'roles', 2, NULL, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:23:17'),
(228, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":null,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":1,\"blood_group_id\":4,\"name\":\"Kishore\",\"phone\":\"9658580210\",\"email\":\"kishore32@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:21:16.000000Z\",\"updated_at\":\"2026-04-16T12:21:16.000000Z\",\"date_of_joining\":\"2024-04-17\",\"father_name\":\"Arul\",\"father_phone\":\"6938956565\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Arapalayam Main Road\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":null,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":2,\"blood_group_id\":4,\"name\":\"Kishore\",\"phone\":\"9658580210\",\"email\":\"kishore32@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:21:16.000000Z\",\"updated_at\":\"2026-04-20T05:23:26.000000Z\",\"date_of_joining\":\"2024-04-17\",\"father_name\":\"Arul\",\"father_phone\":\"6938956565\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Arapalayam Main Road\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:23:26'),
(229, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":260,\"module\":\"dashboard\",\"action\":\"view-sales-order\",\"label\":\"Sales & Order Dashboard\",\"name\":\"view-sales-order dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":260}},{\"id\":261,\"module\":\"dashboard\",\"action\":\"view-accounts-financial\",\"label\":\"Accounts & Financial Dashboard\",\"name\":\"view-accounts-financial dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":261}},{\"id\":262,\"module\":\"dashboard\",\"action\":\"view-production\",\"label\":\"Production Dashboard\",\"name\":\"view-production dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":262}},{\"id\":263,\"module\":\"dashboard\",\"action\":\"view-maintenance\",\"label\":\"Maintenance Dashboard\",\"name\":\"view-maintenance dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":263}},{\"id\":1,\"module\":\"roles\",\"action\":\"create\",\"label\":\"Create Roles\",\"name\":\"create roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":1}},{\"id\":2,\"module\":\"roles\",\"action\":\"edit\",\"label\":\"Edit Roles\",\"name\":\"edit roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":2}},{\"id\":4,\"module\":\"roles\",\"action\":\"view\",\"label\":\"View Roles\",\"name\":\"view roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":4}},{\"id\":3,\"module\":\"roles\",\"action\":\"delete\",\"label\":\"Delete Roles\",\"name\":\"delete roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":3}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:24:13'),
(230, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":260,\"module\":\"dashboard\",\"action\":\"view-sales-order\",\"label\":\"Sales & Order Dashboard\",\"name\":\"view-sales-order dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":260}},{\"id\":261,\"module\":\"dashboard\",\"action\":\"view-accounts-financial\",\"label\":\"Accounts & Financial Dashboard\",\"name\":\"view-accounts-financial dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":261}},{\"id\":262,\"module\":\"dashboard\",\"action\":\"view-production\",\"label\":\"Production Dashboard\",\"name\":\"view-production dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":262}},{\"id\":263,\"module\":\"dashboard\",\"action\":\"view-maintenance\",\"label\":\"Maintenance Dashboard\",\"name\":\"view-maintenance dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":263}},{\"id\":1,\"module\":\"roles\",\"action\":\"create\",\"label\":\"Create Roles\",\"name\":\"create roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":1}},{\"id\":2,\"module\":\"roles\",\"action\":\"edit\",\"label\":\"Edit Roles\",\"name\":\"edit roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":2}},{\"id\":4,\"module\":\"roles\",\"action\":\"view\",\"label\":\"View Roles\",\"name\":\"view roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":4}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:24:29');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(231, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":260,\"module\":\"dashboard\",\"action\":\"view-sales-order\",\"label\":\"Sales & Order Dashboard\",\"name\":\"view-sales-order dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":260}},{\"id\":261,\"module\":\"dashboard\",\"action\":\"view-accounts-financial\",\"label\":\"Accounts & Financial Dashboard\",\"name\":\"view-accounts-financial dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":261}},{\"id\":262,\"module\":\"dashboard\",\"action\":\"view-production\",\"label\":\"Production Dashboard\",\"name\":\"view-production dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":262}},{\"id\":263,\"module\":\"dashboard\",\"action\":\"view-maintenance\",\"label\":\"Maintenance Dashboard\",\"name\":\"view-maintenance dashboard\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:15.000000Z\",\"updated_at\":\"2026-04-16T09:11:15.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":263}},{\"id\":1,\"module\":\"roles\",\"action\":\"create\",\"label\":\"Create Roles\",\"name\":\"create roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":1}},{\"id\":2,\"module\":\"roles\",\"action\":\"edit\",\"label\":\"Edit Roles\",\"name\":\"edit roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":2}},{\"id\":3,\"module\":\"roles\",\"action\":\"delete\",\"label\":\"Delete Roles\",\"name\":\"delete roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":3}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:24:49'),
(232, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":1,\"module\":\"roles\",\"action\":\"create\",\"label\":\"Create Roles\",\"name\":\"create roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":1}},{\"id\":2,\"module\":\"roles\",\"action\":\"edit\",\"label\":\"Edit Roles\",\"name\":\"edit roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":2}},{\"id\":3,\"module\":\"roles\",\"action\":\"delete\",\"label\":\"Delete Roles\",\"name\":\"delete roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":3}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:26:01'),
(233, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":2,\"module\":\"roles\",\"action\":\"edit\",\"label\":\"Edit Roles\",\"name\":\"edit roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":2}},{\"id\":4,\"module\":\"roles\",\"action\":\"view\",\"label\":\"View Roles\",\"name\":\"view roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":4}},{\"id\":3,\"module\":\"roles\",\"action\":\"delete\",\"label\":\"Delete Roles\",\"name\":\"delete roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-16T09:11:14.000000Z\",\"updated_at\":\"2026-04-16T09:11:14.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":3}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:27:04'),
(234, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":9,\"module\":\"employees\",\"action\":\"view_details\",\"label\":\"View_details Employees\",\"name\":\"view_details employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":9}},{\"id\":10,\"module\":\"states\",\"action\":\"create\",\"label\":\"Create States\",\"name\":\"create states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":10}},{\"id\":12,\"module\":\"states\",\"action\":\"delete\",\"label\":\"Delete States\",\"name\":\"delete states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":12}},{\"id\":11,\"module\":\"states\",\"action\":\"edit\",\"label\":\"Edit States\",\"name\":\"edit states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":11}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 05:45:38'),
(235, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":10,\"module\":\"states\",\"action\":\"create\",\"label\":\"Create States\",\"name\":\"create states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":10}},{\"id\":11,\"module\":\"states\",\"action\":\"edit\",\"label\":\"Edit States\",\"name\":\"edit states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":11}},{\"id\":12,\"module\":\"states\",\"action\":\"delete\",\"label\":\"Delete States\",\"name\":\"delete states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":12}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:01:42'),
(236, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":10,\"module\":\"states\",\"action\":\"create\",\"label\":\"Create States\",\"name\":\"create states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":10}},{\"id\":11,\"module\":\"states\",\"action\":\"edit\",\"label\":\"Edit States\",\"name\":\"edit states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":11}},{\"id\":13,\"module\":\"states\",\"action\":\"view\",\"label\":\"View States\",\"name\":\"view states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":13}},{\"id\":12,\"module\":\"states\",\"action\":\"delete\",\"label\":\"Delete States\",\"name\":\"delete states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":12}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:03:03'),
(237, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":10,\"module\":\"states\",\"action\":\"create\",\"label\":\"Create States\",\"name\":\"create states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":10}},{\"id\":13,\"module\":\"states\",\"action\":\"view\",\"label\":\"View States\",\"name\":\"view states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":13}},{\"id\":12,\"module\":\"states\",\"action\":\"delete\",\"label\":\"Delete States\",\"name\":\"delete states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":12}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:03:17'),
(238, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":14,\"module\":\"cities\",\"action\":\"create\",\"label\":\"Create Cities\",\"name\":\"create cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":14}},{\"id\":15,\"module\":\"cities\",\"action\":\"edit\",\"label\":\"Edit Cities\",\"name\":\"edit cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":15}},{\"id\":17,\"module\":\"cities\",\"action\":\"view\",\"label\":\"View Cities\",\"name\":\"view cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":17}},{\"id\":16,\"module\":\"cities\",\"action\":\"delete\",\"label\":\"Delete Cities\",\"name\":\"delete cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":16}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:04:08'),
(239, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":14,\"module\":\"cities\",\"action\":\"create\",\"label\":\"Create Cities\",\"name\":\"create cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":14}},{\"id\":15,\"module\":\"cities\",\"action\":\"edit\",\"label\":\"Edit Cities\",\"name\":\"edit cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":15}},{\"id\":17,\"module\":\"cities\",\"action\":\"view\",\"label\":\"View Cities\",\"name\":\"view cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":17}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:04:30'),
(240, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":15,\"module\":\"cities\",\"action\":\"edit\",\"label\":\"Edit Cities\",\"name\":\"edit cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":15}},{\"id\":17,\"module\":\"cities\",\"action\":\"view\",\"label\":\"View Cities\",\"name\":\"view cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":17}},{\"id\":16,\"module\":\"cities\",\"action\":\"delete\",\"label\":\"Delete Cities\",\"name\":\"delete cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":16}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:04:40'),
(241, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":15,\"module\":\"cities\",\"action\":\"edit\",\"label\":\"Edit Cities\",\"name\":\"edit cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":15}},{\"id\":16,\"module\":\"cities\",\"action\":\"delete\",\"label\":\"Delete Cities\",\"name\":\"delete cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":16}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:04:41'),
(242, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":15,\"module\":\"cities\",\"action\":\"edit\",\"label\":\"Edit Cities\",\"name\":\"edit cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":15}},{\"id\":16,\"module\":\"cities\",\"action\":\"delete\",\"label\":\"Delete Cities\",\"name\":\"delete cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":16}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:04:53'),
(243, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":18,\"module\":\"service-points\",\"action\":\"create\",\"label\":\"Create Service Points\",\"name\":\"create service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":18}},{\"id\":19,\"module\":\"service-points\",\"action\":\"edit\",\"label\":\"Edit Service Points\",\"name\":\"edit service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":19}},{\"id\":21,\"module\":\"service-points\",\"action\":\"view\",\"label\":\"View Service Points\",\"name\":\"view service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":21}},{\"id\":20,\"module\":\"service-points\",\"action\":\"delete\",\"label\":\"Delete Service Points\",\"name\":\"delete service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":20}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:06:05'),
(244, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":18,\"module\":\"service-points\",\"action\":\"create\",\"label\":\"Create Service Points\",\"name\":\"create service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":18}},{\"id\":19,\"module\":\"service-points\",\"action\":\"edit\",\"label\":\"Edit Service Points\",\"name\":\"edit service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":19}},{\"id\":21,\"module\":\"service-points\",\"action\":\"view\",\"label\":\"View Service Points\",\"name\":\"view service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":21}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:06:16'),
(245, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":21,\"module\":\"service-points\",\"action\":\"view\",\"label\":\"View Service Points\",\"name\":\"view service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":21}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:06:33'),
(246, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":18,\"module\":\"service-points\",\"action\":\"create\",\"label\":\"Create Service Points\",\"name\":\"create service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":18}},{\"id\":19,\"module\":\"service-points\",\"action\":\"edit\",\"label\":\"Edit Service Points\",\"name\":\"edit service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":19}},{\"id\":21,\"module\":\"service-points\",\"action\":\"view\",\"label\":\"View Service Points\",\"name\":\"view service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":21}},{\"id\":20,\"module\":\"service-points\",\"action\":\"delete\",\"label\":\"Delete Service Points\",\"name\":\"delete service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":20}},{\"id\":22,\"module\":\"uoms\",\"action\":\"create\",\"label\":\"Create Uoms\",\"name\":\"create uoms\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":22}},{\"id\":25,\"module\":\"uoms\",\"action\":\"view\",\"label\":\"View Uoms\",\"name\":\"view uoms\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":25}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:06:59'),
(247, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":18,\"module\":\"service-points\",\"action\":\"create\",\"label\":\"Create Service Points\",\"name\":\"create service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":18}},{\"id\":19,\"module\":\"service-points\",\"action\":\"edit\",\"label\":\"Edit Service Points\",\"name\":\"edit service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":19}},{\"id\":21,\"module\":\"service-points\",\"action\":\"view\",\"label\":\"View Service Points\",\"name\":\"view service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":21}},{\"id\":20,\"module\":\"service-points\",\"action\":\"delete\",\"label\":\"Delete Service Points\",\"name\":\"delete service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":20}},{\"id\":25,\"module\":\"uoms\",\"action\":\"view\",\"label\":\"View Uoms\",\"name\":\"view uoms\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":25}},{\"id\":24,\"module\":\"uoms\",\"action\":\"delete\",\"label\":\"Delete Uoms\",\"name\":\"delete uoms\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":24}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:07:18'),
(248, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":27,\"module\":\"colors\",\"action\":\"edit\",\"label\":\"Edit Colors\",\"name\":\"edit colors\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":27}},{\"id\":29,\"module\":\"colors\",\"action\":\"view\",\"label\":\"View Colors\",\"name\":\"view colors\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":29}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:08:45');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(249, 1, 'update', 'Role', 'roles', 1, '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-16T08:56:57.000000Z\",\"updated_at\":\"2026-04-16T08:56:57.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":1,\"module\":\"roles\",\"action\":\"create\",\"label\":\"Create Roles\",\"name\":\"create roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":1}},{\"id\":2,\"module\":\"roles\",\"action\":\"edit\",\"label\":\"Edit Roles\",\"name\":\"edit roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":2}},{\"id\":4,\"module\":\"roles\",\"action\":\"view\",\"label\":\"View Roles\",\"name\":\"view roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":4}},{\"id\":3,\"module\":\"roles\",\"action\":\"delete\",\"label\":\"Delete Roles\",\"name\":\"delete roles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":3}},{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":7}},{\"id\":170,\"module\":\"purchase-order\",\"action\":\"view_details\",\"label\":\"View_details Purchase Order\",\"name\":\"view_details purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":170}},{\"id\":171,\"module\":\"purchase-invoice\",\"action\":\"create\",\"label\":\"Create Purchase Invoice\",\"name\":\"create purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":171}},{\"id\":172,\"module\":\"purchase-invoice\",\"action\":\"edit\",\"label\":\"Edit Purchase Invoice\",\"name\":\"edit purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":172}},{\"id\":174,\"module\":\"purchase-invoice\",\"action\":\"view_details\",\"label\":\"View_details Purchase Invoice\",\"name\":\"view_details purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":174}},{\"id\":189,\"module\":\"stock-consumable-return\",\"action\":\"view_details\",\"label\":\"View_details Stock Consumable Return\",\"name\":\"view_details stock-consumable-return\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":189}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":191}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":194}},{\"id\":195,\"module\":\"job-card\",\"action\":\"work-order-pdf\",\"label\":\"Work-order-pdf Job Card\",\"name\":\"work-order-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":1,\"permission_id\":195}}]}', '{\"id\":1,\"name\":\"Manager\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-16T08:56:57.000000Z\",\"updated_at\":\"2026-04-16T08:56:57.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:10:01'),
(250, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":10,\"module\":\"states\",\"action\":\"create\",\"label\":\"Create States\",\"name\":\"create states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":10}},{\"id\":13,\"module\":\"states\",\"action\":\"view\",\"label\":\"View States\",\"name\":\"view states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":13}},{\"id\":27,\"module\":\"colors\",\"action\":\"edit\",\"label\":\"Edit Colors\",\"name\":\"edit colors\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":27}},{\"id\":29,\"module\":\"colors\",\"action\":\"view\",\"label\":\"View Colors\",\"name\":\"view colors\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":29}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:10:19'),
(251, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":10,\"module\":\"states\",\"action\":\"create\",\"label\":\"Create States\",\"name\":\"create states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":10}},{\"id\":13,\"module\":\"states\",\"action\":\"view\",\"label\":\"View States\",\"name\":\"view states\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":13}},{\"id\":14,\"module\":\"cities\",\"action\":\"create\",\"label\":\"Create Cities\",\"name\":\"create cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":14}},{\"id\":17,\"module\":\"cities\",\"action\":\"view\",\"label\":\"View Cities\",\"name\":\"view cities\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":17}},{\"id\":18,\"module\":\"service-points\",\"action\":\"create\",\"label\":\"Create Service Points\",\"name\":\"create service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":18}},{\"id\":21,\"module\":\"service-points\",\"action\":\"view\",\"label\":\"View Service Points\",\"name\":\"view service-points\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":21}},{\"id\":22,\"module\":\"uoms\",\"action\":\"create\",\"label\":\"Create Uoms\",\"name\":\"create uoms\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":22}},{\"id\":25,\"module\":\"uoms\",\"action\":\"view\",\"label\":\"View Uoms\",\"name\":\"view uoms\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":25}},{\"id\":27,\"module\":\"colors\",\"action\":\"edit\",\"label\":\"Edit Colors\",\"name\":\"edit colors\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":27}},{\"id\":29,\"module\":\"colors\",\"action\":\"view\",\"label\":\"View Colors\",\"name\":\"view colors\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":29}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:13:24'),
(252, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":30,\"module\":\"operation-stages\",\"action\":\"create\",\"label\":\"Create Operation Stages\",\"name\":\"create operation-stages\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":30}},{\"id\":33,\"module\":\"operation-stages\",\"action\":\"view\",\"label\":\"View Operation Stages\",\"name\":\"view operation-stages\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":33}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:17:11'),
(253, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":34,\"module\":\"zones\",\"action\":\"create\",\"label\":\"Create Zones\",\"name\":\"create zones\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":34}},{\"id\":37,\"module\":\"zones\",\"action\":\"view\",\"label\":\"View Zones\",\"name\":\"view zones\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":37}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:28:17'),
(254, 2, 'create', 'Size Ratio', 'size_ratios', 2, NULL, '{\"size\":\"40\",\"ratio\":\"2\",\"status\":\"Active\",\"created_by\":2,\"updated_at\":\"2026-04-20T06:32:15.000000Z\",\"created_at\":\"2026-04-20T06:32:15.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-04-20 06:32:15'),
(255, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":38,\"module\":\"size-ratio\",\"action\":\"create\",\"label\":\"Create Size Ratio\",\"name\":\"create size-ratio\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":38}},{\"id\":41,\"module\":\"size-ratio\",\"action\":\"view\",\"label\":\"View Size Ratio\",\"name\":\"view size-ratio\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":41}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:34:22'),
(256, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":34,\"module\":\"zones\",\"action\":\"create\",\"label\":\"Create Zones\",\"name\":\"create zones\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":34}},{\"id\":37,\"module\":\"zones\",\"action\":\"view\",\"label\":\"View Zones\",\"name\":\"view zones\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":37}},{\"id\":38,\"module\":\"size-ratio\",\"action\":\"create\",\"label\":\"Create Size Ratio\",\"name\":\"create size-ratio\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":38}},{\"id\":41,\"module\":\"size-ratio\",\"action\":\"view\",\"label\":\"View Size Ratio\",\"name\":\"view size-ratio\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":41}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:38:39'),
(257, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":42,\"module\":\"fabric-type\",\"action\":\"create\",\"label\":\"Create Fabric Type\",\"name\":\"create fabric-type\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":42}},{\"id\":43,\"module\":\"fabric-type\",\"action\":\"edit\",\"label\":\"Edit Fabric Type\",\"name\":\"edit fabric-type\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":43}},{\"id\":45,\"module\":\"fabric-type\",\"action\":\"view\",\"label\":\"View Fabric Type\",\"name\":\"view fabric-type\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":45}},{\"id\":44,\"module\":\"fabric-type\",\"action\":\"delete\",\"label\":\"Delete Fabric Type\",\"name\":\"delete fabric-type\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":44}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:39:10'),
(258, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":42,\"module\":\"fabric-type\",\"action\":\"create\",\"label\":\"Create Fabric Type\",\"name\":\"create fabric-type\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":42}},{\"id\":45,\"module\":\"fabric-type\",\"action\":\"view\",\"label\":\"View Fabric Type\",\"name\":\"view fabric-type\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":45}},{\"id\":44,\"module\":\"fabric-type\",\"action\":\"delete\",\"label\":\"Delete Fabric Type\",\"name\":\"delete fabric-type\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":44}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:40:03'),
(259, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":47,\"module\":\"fabric-sizes\",\"action\":\"edit\",\"label\":\"Edit Fabric Sizes\",\"name\":\"edit fabric-sizes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":47}},{\"id\":49,\"module\":\"fabric-sizes\",\"action\":\"view\",\"label\":\"View Fabric Sizes\",\"name\":\"view fabric-sizes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":49}},{\"id\":48,\"module\":\"fabric-sizes\",\"action\":\"delete\",\"label\":\"Delete Fabric Sizes\",\"name\":\"delete fabric-sizes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":48}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:40:52'),
(260, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":50,\"module\":\"charges\",\"action\":\"create\",\"label\":\"Create Charges\",\"name\":\"create charges\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":50}},{\"id\":53,\"module\":\"charges\",\"action\":\"view\",\"label\":\"View Charges\",\"name\":\"view charges\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":53}},{\"id\":52,\"module\":\"charges\",\"action\":\"delete\",\"label\":\"Delete Charges\",\"name\":\"delete charges\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":52}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:44:16'),
(261, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":54,\"module\":\"store-location\",\"action\":\"create\",\"label\":\"Create Store Location\",\"name\":\"create store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":54}},{\"id\":55,\"module\":\"store-location\",\"action\":\"edit\",\"label\":\"Edit Store Location\",\"name\":\"edit store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":55}},{\"id\":57,\"module\":\"store-location\",\"action\":\"view\",\"label\":\"View Store Location\",\"name\":\"view store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":57}},{\"id\":56,\"module\":\"store-location\",\"action\":\"delete\",\"label\":\"Delete Store Location\",\"name\":\"delete store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":56}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:44:49'),
(262, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":58,\"module\":\"departments\",\"action\":\"create\",\"label\":\"Create Departments\",\"name\":\"create departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":58}},{\"id\":61,\"module\":\"departments\",\"action\":\"view\",\"label\":\"View Departments\",\"name\":\"view departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":61}},{\"id\":60,\"module\":\"departments\",\"action\":\"delete\",\"label\":\"Delete Departments\",\"name\":\"delete departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":60}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:45:08'),
(263, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":54,\"module\":\"store-location\",\"action\":\"create\",\"label\":\"Create Store Location\",\"name\":\"create store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":54}},{\"id\":55,\"module\":\"store-location\",\"action\":\"edit\",\"label\":\"Edit Store Location\",\"name\":\"edit store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":55}},{\"id\":57,\"module\":\"store-location\",\"action\":\"view\",\"label\":\"View Store Location\",\"name\":\"view store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":57}},{\"id\":56,\"module\":\"store-location\",\"action\":\"delete\",\"label\":\"Delete Store Location\",\"name\":\"delete store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":56}},{\"id\":58,\"module\":\"departments\",\"action\":\"create\",\"label\":\"Create Departments\",\"name\":\"create departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":58}},{\"id\":61,\"module\":\"departments\",\"action\":\"view\",\"label\":\"View Departments\",\"name\":\"view departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":61}},{\"id\":60,\"module\":\"departments\",\"action\":\"delete\",\"label\":\"Delete Departments\",\"name\":\"delete departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":60}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:45:36'),
(264, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":54,\"module\":\"store-location\",\"action\":\"create\",\"label\":\"Create Store Location\",\"name\":\"create store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":54}},{\"id\":57,\"module\":\"store-location\",\"action\":\"view\",\"label\":\"View Store Location\",\"name\":\"view store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":57}},{\"id\":56,\"module\":\"store-location\",\"action\":\"delete\",\"label\":\"Delete Store Location\",\"name\":\"delete store-location\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":56}},{\"id\":58,\"module\":\"departments\",\"action\":\"create\",\"label\":\"Create Departments\",\"name\":\"create departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":58}},{\"id\":61,\"module\":\"departments\",\"action\":\"view\",\"label\":\"View Departments\",\"name\":\"view departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":61}},{\"id\":60,\"module\":\"departments\",\"action\":\"delete\",\"label\":\"Delete Departments\",\"name\":\"delete departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":60}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:46:22');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(265, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":58,\"module\":\"departments\",\"action\":\"create\",\"label\":\"Create Departments\",\"name\":\"create departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":58}},{\"id\":61,\"module\":\"departments\",\"action\":\"view\",\"label\":\"View Departments\",\"name\":\"view departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":61}},{\"id\":60,\"module\":\"departments\",\"action\":\"delete\",\"label\":\"Delete Departments\",\"name\":\"delete departments\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":60}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:47:08'),
(266, 2, 'create', 'Tax', 'taxes', 1, NULL, '{\"item_name\":\"test\",\"tax_rate\":\"3\",\"status\":\"Active\",\"created_by\":2,\"updated_at\":\"2026-04-20T06:47:25.000000Z\",\"created_at\":\"2026-04-20T06:47:25.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-04-20 06:47:25'),
(267, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":62,\"module\":\"taxes\",\"action\":\"create\",\"label\":\"Create Taxes\",\"name\":\"create taxes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":62}},{\"id\":65,\"module\":\"taxes\",\"action\":\"view\",\"label\":\"View Taxes\",\"name\":\"view taxes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":65}},{\"id\":64,\"module\":\"taxes\",\"action\":\"delete\",\"label\":\"Delete Taxes\",\"name\":\"delete taxes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":64}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:50:14'),
(268, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":66,\"module\":\"styles\",\"action\":\"create\",\"label\":\"Create Styles\",\"name\":\"create styles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":66}},{\"id\":67,\"module\":\"styles\",\"action\":\"edit\",\"label\":\"Edit Styles\",\"name\":\"edit styles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":67}},{\"id\":69,\"module\":\"styles\",\"action\":\"view\",\"label\":\"View Styles\",\"name\":\"view styles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":69}},{\"id\":68,\"module\":\"styles\",\"action\":\"delete\",\"label\":\"Delete Styles\",\"name\":\"delete styles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":68}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:50:29'),
(269, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":66,\"module\":\"styles\",\"action\":\"create\",\"label\":\"Create Styles\",\"name\":\"create styles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":66}},{\"id\":69,\"module\":\"styles\",\"action\":\"view\",\"label\":\"View Styles\",\"name\":\"view styles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":69}},{\"id\":68,\"module\":\"styles\",\"action\":\"delete\",\"label\":\"Delete Styles\",\"name\":\"delete styles\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":68}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:51:07'),
(270, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":70,\"module\":\"stores\",\"action\":\"create\",\"label\":\"Create Stores\",\"name\":\"create stores\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":70}},{\"id\":73,\"module\":\"stores\",\"action\":\"view\",\"label\":\"View Stores\",\"name\":\"view stores\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":73}},{\"id\":72,\"module\":\"stores\",\"action\":\"delete\",\"label\":\"Delete Stores\",\"name\":\"delete stores\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":72}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:52:04'),
(271, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":74,\"module\":\"shipping-methods\",\"action\":\"create\",\"label\":\"Create Shipping Methods\",\"name\":\"create shipping-methods\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":74}},{\"id\":77,\"module\":\"shipping-methods\",\"action\":\"view\",\"label\":\"View Shipping Methods\",\"name\":\"view shipping-methods\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":77}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:53:48'),
(272, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":79,\"module\":\"transport-mode\",\"action\":\"edit\",\"label\":\"Edit Transport Mode\",\"name\":\"edit transport-mode\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":79}},{\"id\":81,\"module\":\"transport-mode\",\"action\":\"view\",\"label\":\"View Transport Mode\",\"name\":\"view transport-mode\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":81}},{\"id\":80,\"module\":\"transport-mode\",\"action\":\"delete\",\"label\":\"Delete Transport Mode\",\"name\":\"delete transport-mode\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":80}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:57:38'),
(273, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":79,\"module\":\"transport-mode\",\"action\":\"edit\",\"label\":\"Edit Transport Mode\",\"name\":\"edit transport-mode\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":79}},{\"id\":81,\"module\":\"transport-mode\",\"action\":\"view\",\"label\":\"View Transport Mode\",\"name\":\"view transport-mode\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":81}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 06:59:14'),
(274, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":82,\"module\":\"fits\",\"action\":\"create\",\"label\":\"Create Fits\",\"name\":\"create fits\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":82}},{\"id\":85,\"module\":\"fits\",\"action\":\"view\",\"label\":\"View Fits\",\"name\":\"view fits\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":85}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:00:11'),
(275, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":86,\"module\":\"patti-types\",\"action\":\"create\",\"label\":\"Create Patti Types\",\"name\":\"create patti-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":86}},{\"id\":89,\"module\":\"patti-types\",\"action\":\"view\",\"label\":\"View Patti Types\",\"name\":\"view patti-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":89}},{\"id\":88,\"module\":\"patti-types\",\"action\":\"delete\",\"label\":\"Delete Patti Types\",\"name\":\"delete patti-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":88}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:00:59'),
(276, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":86,\"module\":\"patti-types\",\"action\":\"create\",\"label\":\"Create Patti Types\",\"name\":\"create patti-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":86}},{\"id\":89,\"module\":\"patti-types\",\"action\":\"view\",\"label\":\"View Patti Types\",\"name\":\"view patti-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":89}},{\"id\":88,\"module\":\"patti-types\",\"action\":\"delete\",\"label\":\"Delete Patti Types\",\"name\":\"delete patti-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":88}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:01:44'),
(277, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":90,\"module\":\"collar-types\",\"action\":\"create\",\"label\":\"Create Collar Types\",\"name\":\"create collar-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":90}},{\"id\":93,\"module\":\"collar-types\",\"action\":\"view\",\"label\":\"View Collar Types\",\"name\":\"view collar-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":93}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:02:42'),
(278, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":94,\"module\":\"cuff-types\",\"action\":\"create\",\"label\":\"Create Cuff Types\",\"name\":\"create cuff-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":94}},{\"id\":95,\"module\":\"cuff-types\",\"action\":\"edit\",\"label\":\"Edit Cuff Types\",\"name\":\"edit cuff-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":95}},{\"id\":97,\"module\":\"cuff-types\",\"action\":\"view\",\"label\":\"View Cuff Types\",\"name\":\"view cuff-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":97}},{\"id\":96,\"module\":\"cuff-types\",\"action\":\"delete\",\"label\":\"Delete Cuff Types\",\"name\":\"delete cuff-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":96}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:03:09'),
(279, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":94,\"module\":\"cuff-types\",\"action\":\"create\",\"label\":\"Create Cuff Types\",\"name\":\"create cuff-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":94}},{\"id\":97,\"module\":\"cuff-types\",\"action\":\"view\",\"label\":\"View Cuff Types\",\"name\":\"view cuff-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":97}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:03:51'),
(280, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":98,\"module\":\"pocket-types\",\"action\":\"create\",\"label\":\"Create Pocket Types\",\"name\":\"create pocket-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":98}},{\"id\":101,\"module\":\"pocket-types\",\"action\":\"view\",\"label\":\"View Pocket Types\",\"name\":\"view pocket-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":101}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:04:45'),
(281, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":98,\"module\":\"pocket-types\",\"action\":\"create\",\"label\":\"Create Pocket Types\",\"name\":\"create pocket-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":98}},{\"id\":99,\"module\":\"pocket-types\",\"action\":\"edit\",\"label\":\"Edit Pocket Types\",\"name\":\"edit pocket-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":99}},{\"id\":101,\"module\":\"pocket-types\",\"action\":\"view\",\"label\":\"View Pocket Types\",\"name\":\"view pocket-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":101}},{\"id\":100,\"module\":\"pocket-types\",\"action\":\"delete\",\"label\":\"Delete Pocket Types\",\"name\":\"delete pocket-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":100}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:05:58'),
(282, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":98,\"module\":\"pocket-types\",\"action\":\"create\",\"label\":\"Create Pocket Types\",\"name\":\"create pocket-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":98}},{\"id\":101,\"module\":\"pocket-types\",\"action\":\"view\",\"label\":\"View Pocket Types\",\"name\":\"view pocket-types\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":101}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:07:29'),
(283, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":102,\"module\":\"bottom-cuts\",\"action\":\"create\",\"label\":\"Create Bottom Cuts\",\"name\":\"create bottom-cuts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":102}},{\"id\":105,\"module\":\"bottom-cuts\",\"action\":\"view\",\"label\":\"View Bottom Cuts\",\"name\":\"view bottom-cuts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":105}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:08:20'),
(284, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":106,\"module\":\"process-groups\",\"action\":\"create\",\"label\":\"Create Process Groups\",\"name\":\"create process-groups\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":106}},{\"id\":109,\"module\":\"process-groups\",\"action\":\"view\",\"label\":\"View Process Groups\",\"name\":\"view process-groups\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":109}},{\"id\":108,\"module\":\"process-groups\",\"action\":\"delete\",\"label\":\"Delete Process Groups\",\"name\":\"delete process-groups\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":108}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:09:36');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(285, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":110,\"module\":\"seasons\",\"action\":\"create\",\"label\":\"Create Seasons\",\"name\":\"create seasons\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":110}},{\"id\":113,\"module\":\"seasons\",\"action\":\"view\",\"label\":\"View Seasons\",\"name\":\"view seasons\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":113}},{\"id\":112,\"module\":\"seasons\",\"action\":\"delete\",\"label\":\"Delete Seasons\",\"name\":\"delete seasons\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":112}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 07:10:31'),
(286, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":114,\"module\":\"shifts\",\"action\":\"create\",\"label\":\"Create Shifts\",\"name\":\"create shifts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":114}},{\"id\":117,\"module\":\"shifts\",\"action\":\"view\",\"label\":\"View Shifts\",\"name\":\"view shifts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":117}},{\"id\":116,\"module\":\"shifts\",\"action\":\"delete\",\"label\":\"Delete Shifts\",\"name\":\"delete shifts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":116}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:00:13'),
(287, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":114,\"module\":\"shifts\",\"action\":\"create\",\"label\":\"Create Shifts\",\"name\":\"create shifts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":114}},{\"id\":117,\"module\":\"shifts\",\"action\":\"view\",\"label\":\"View Shifts\",\"name\":\"view shifts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":117}},{\"id\":116,\"module\":\"shifts\",\"action\":\"delete\",\"label\":\"Delete Shifts\",\"name\":\"delete shifts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":116}},{\"id\":118,\"module\":\"production-services\",\"action\":\"create\",\"label\":\"Create Production Services\",\"name\":\"create production-services\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":118}},{\"id\":121,\"module\":\"production-services\",\"action\":\"view\",\"label\":\"View Production Services\",\"name\":\"view production-services\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":121}},{\"id\":120,\"module\":\"production-services\",\"action\":\"delete\",\"label\":\"Delete Production Services\",\"name\":\"delete production-services\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":120}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:01:19'),
(288, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":118,\"module\":\"production-services\",\"action\":\"create\",\"label\":\"Create Production Services\",\"name\":\"create production-services\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":118}},{\"id\":121,\"module\":\"production-services\",\"action\":\"view\",\"label\":\"View Production Services\",\"name\":\"view production-services\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":121}},{\"id\":120,\"module\":\"production-services\",\"action\":\"delete\",\"label\":\"Delete Production Services\",\"name\":\"delete production-services\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":120}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:03:40'),
(289, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":122,\"module\":\"customers\",\"action\":\"create\",\"label\":\"Create Customers\",\"name\":\"create customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":122}},{\"id\":125,\"module\":\"customers\",\"action\":\"view\",\"label\":\"View Customers\",\"name\":\"view customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":125}},{\"id\":124,\"module\":\"customers\",\"action\":\"delete\",\"label\":\"Delete Customers\",\"name\":\"delete customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":124}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:17:06'),
(290, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":127,\"module\":\"suppliers\",\"action\":\"create\",\"label\":\"Create Suppliers\",\"name\":\"create suppliers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":127}},{\"id\":130,\"module\":\"suppliers\",\"action\":\"view\",\"label\":\"View Suppliers\",\"name\":\"view suppliers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":130}},{\"id\":131,\"module\":\"suppliers\",\"action\":\"view_details\",\"label\":\"View_details Suppliers\",\"name\":\"view_details suppliers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":131}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:18:26'),
(291, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":132,\"module\":\"service-providers\",\"action\":\"create\",\"label\":\"Create Service Providers\",\"name\":\"create service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":132}},{\"id\":135,\"module\":\"service-providers\",\"action\":\"view\",\"label\":\"View Service Providers\",\"name\":\"view service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":135}},{\"id\":134,\"module\":\"service-providers\",\"action\":\"delete\",\"label\":\"Delete Service Providers\",\"name\":\"delete service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":134}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:19:33'),
(292, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":122,\"module\":\"customers\",\"action\":\"create\",\"label\":\"Create Customers\",\"name\":\"create customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":122}},{\"id\":123,\"module\":\"customers\",\"action\":\"edit\",\"label\":\"Edit Customers\",\"name\":\"edit customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":123}},{\"id\":125,\"module\":\"customers\",\"action\":\"view\",\"label\":\"View Customers\",\"name\":\"view customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":125}},{\"id\":124,\"module\":\"customers\",\"action\":\"delete\",\"label\":\"Delete Customers\",\"name\":\"delete customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":124}},{\"id\":132,\"module\":\"service-providers\",\"action\":\"create\",\"label\":\"Create Service Providers\",\"name\":\"create service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":132}},{\"id\":135,\"module\":\"service-providers\",\"action\":\"view\",\"label\":\"View Service Providers\",\"name\":\"view service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":135}},{\"id\":134,\"module\":\"service-providers\",\"action\":\"delete\",\"label\":\"Delete Service Providers\",\"name\":\"delete service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":134}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:19:53'),
(293, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":122,\"module\":\"customers\",\"action\":\"create\",\"label\":\"Create Customers\",\"name\":\"create customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":122}},{\"id\":123,\"module\":\"customers\",\"action\":\"edit\",\"label\":\"Edit Customers\",\"name\":\"edit customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":123}},{\"id\":125,\"module\":\"customers\",\"action\":\"view\",\"label\":\"View Customers\",\"name\":\"view customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":125}},{\"id\":124,\"module\":\"customers\",\"action\":\"delete\",\"label\":\"Delete Customers\",\"name\":\"delete customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":124}},{\"id\":127,\"module\":\"suppliers\",\"action\":\"create\",\"label\":\"Create Suppliers\",\"name\":\"create suppliers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":127}},{\"id\":128,\"module\":\"suppliers\",\"action\":\"edit\",\"label\":\"Edit Suppliers\",\"name\":\"edit suppliers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":128}},{\"id\":130,\"module\":\"suppliers\",\"action\":\"view\",\"label\":\"View Suppliers\",\"name\":\"view suppliers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":130}},{\"id\":132,\"module\":\"service-providers\",\"action\":\"create\",\"label\":\"Create Service Providers\",\"name\":\"create service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":132}},{\"id\":135,\"module\":\"service-providers\",\"action\":\"view\",\"label\":\"View Service Providers\",\"name\":\"view service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":135}},{\"id\":134,\"module\":\"service-providers\",\"action\":\"delete\",\"label\":\"Delete Service Providers\",\"name\":\"delete service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":134}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:20:25'),
(294, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":122,\"module\":\"customers\",\"action\":\"create\",\"label\":\"Create Customers\",\"name\":\"create customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":122}},{\"id\":123,\"module\":\"customers\",\"action\":\"edit\",\"label\":\"Edit Customers\",\"name\":\"edit customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":123}},{\"id\":125,\"module\":\"customers\",\"action\":\"view\",\"label\":\"View Customers\",\"name\":\"view customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":125}},{\"id\":124,\"module\":\"customers\",\"action\":\"delete\",\"label\":\"Delete Customers\",\"name\":\"delete customers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":124}},{\"id\":127,\"module\":\"suppliers\",\"action\":\"create\",\"label\":\"Create Suppliers\",\"name\":\"create suppliers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":127}},{\"id\":128,\"module\":\"suppliers\",\"action\":\"edit\",\"label\":\"Edit Suppliers\",\"name\":\"edit suppliers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":128}},{\"id\":130,\"module\":\"suppliers\",\"action\":\"view\",\"label\":\"View Suppliers\",\"name\":\"view suppliers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":130}},{\"id\":135,\"module\":\"service-providers\",\"action\":\"view\",\"label\":\"View Service Providers\",\"name\":\"view service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":135}},{\"id\":134,\"module\":\"service-providers\",\"action\":\"delete\",\"label\":\"Delete Service Providers\",\"name\":\"delete service-providers\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":134}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:23:02'),
(295, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":137,\"module\":\"sales-agents\",\"action\":\"create\",\"label\":\"Create Sales Agents\",\"name\":\"create sales-agents\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":137}},{\"id\":140,\"module\":\"sales-agents\",\"action\":\"view\",\"label\":\"View Sales Agents\",\"name\":\"view sales-agents\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":140}},{\"id\":139,\"module\":\"sales-agents\",\"action\":\"delete\",\"label\":\"Delete Sales Agents\",\"name\":\"delete sales-agents\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":139}},{\"id\":141,\"module\":\"sales-agents\",\"action\":\"view_details\",\"label\":\"View_details Sales Agents\",\"name\":\"view_details sales-agents\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":141}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:24:13'),
(296, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":137,\"module\":\"sales-agents\",\"action\":\"create\",\"label\":\"Create Sales Agents\",\"name\":\"create sales-agents\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":137}},{\"id\":140,\"module\":\"sales-agents\",\"action\":\"view\",\"label\":\"View Sales Agents\",\"name\":\"view sales-agents\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":140}},{\"id\":139,\"module\":\"sales-agents\",\"action\":\"delete\",\"label\":\"Delete Sales Agents\",\"name\":\"delete sales-agents\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":139}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:24:33'),
(297, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":142,\"module\":\"purchase-commission-agent\",\"action\":\"create\",\"label\":\"Create Purchase Commission Agent\",\"name\":\"create purchase-commission-agent\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":142}},{\"id\":145,\"module\":\"purchase-commission-agent\",\"action\":\"view\",\"label\":\"View Purchase Commission Agent\",\"name\":\"view purchase-commission-agent\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":145}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:25:28'),
(298, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":142,\"module\":\"purchase-commission-agent\",\"action\":\"create\",\"label\":\"Create Purchase Commission Agent\",\"name\":\"create purchase-commission-agent\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":142}},{\"id\":145,\"module\":\"purchase-commission-agent\",\"action\":\"view\",\"label\":\"View Purchase Commission Agent\",\"name\":\"view purchase-commission-agent\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":145}},{\"id\":146,\"module\":\"purchase-commission-agent\",\"action\":\"view_details\",\"label\":\"View_details Purchase Commission Agent\",\"name\":\"view_details purchase-commission-agent\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":146}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:25:51'),
(299, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":147,\"module\":\"store-categories\",\"action\":\"create\",\"label\":\"Create Store Categories\",\"name\":\"create store-categories\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":147}},{\"id\":150,\"module\":\"store-categories\",\"action\":\"view\",\"label\":\"View Store Categories\",\"name\":\"view store-categories\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":150}},{\"id\":149,\"module\":\"store-categories\",\"action\":\"delete\",\"label\":\"Delete Store Categories\",\"name\":\"delete store-categories\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":149}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:26:45'),
(300, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":151,\"module\":\"raw-materials\",\"action\":\"create\",\"label\":\"Create Raw Materials\",\"name\":\"create raw-materials\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":151}},{\"id\":154,\"module\":\"raw-materials\",\"action\":\"view\",\"label\":\"View Raw Materials\",\"name\":\"view raw-materials\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":154}},{\"id\":153,\"module\":\"raw-materials\",\"action\":\"delete\",\"label\":\"Delete Raw Materials\",\"name\":\"delete raw-materials\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":153}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:28:03'),
(301, 2, 'create', 'Brand Category', 'brands_categories', 1, NULL, '{\"code\":\"1001\",\"name\":\"formal\",\"status\":\"Active\",\"created_by\":2,\"updated_at\":\"2026-04-20T08:28:57.000000Z\",\"created_at\":\"2026-04-20T08:28:57.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-04-20 08:28:57');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(302, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":155,\"module\":\"brand-categories\",\"action\":\"create\",\"label\":\"Create Brand Categories\",\"name\":\"create brand-categories\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":155}},{\"id\":158,\"module\":\"brand-categories\",\"action\":\"view\",\"label\":\"View Brand Categories\",\"name\":\"view brand-categories\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":158}},{\"id\":157,\"module\":\"brand-categories\",\"action\":\"delete\",\"label\":\"Delete Brand Categories\",\"name\":\"delete brand-categories\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":157}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:29:19'),
(303, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":159,\"module\":\"brands\",\"action\":\"create\",\"label\":\"Create Brands\",\"name\":\"create brands\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":159}},{\"id\":162,\"module\":\"brands\",\"action\":\"view\",\"label\":\"View Brands\",\"name\":\"view brands\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":162}},{\"id\":161,\"module\":\"brands\",\"action\":\"delete\",\"label\":\"Delete Brands\",\"name\":\"delete brands\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":161}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:29:54'),
(304, 2, 'create', 'Item', 'items', 1, NULL, '{\"brand_id\":\"2\",\"brand_category_id\":\"1\",\"name\":\"Men\\u2019s Formal Cotton Shirt\",\"code\":\"1000\",\"style_id\":null,\"fabric_type_id\":null,\"design_art_no\":null,\"uom_id\":\"3\",\"size_ratio_id\":null,\"color_id\":[],\"standard_costing\":null,\"store_category_id\":null,\"related_materials\":{\"1\":{\"category_id\":\"1\",\"category_name\":\"Fabric(FBC)\",\"material_id\":\"1\",\"material_name\":\"COTTON FABRIC(1001)\"}},\"operation_stages\":null,\"service_providers\":{\"cutting\":null,\"stitching ready\":null,\"stitching assemble\":null,\"kaja button\":null,\"trimming & checking\":null,\"ironing & packing\":null},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":2,\"updated_at\":\"2026-04-20T08:32:05.000000Z\",\"created_at\":\"2026-04-20T08:32:05.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-04-20 08:32:05'),
(305, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":163,\"module\":\"items\",\"action\":\"create\",\"label\":\"Create Items\",\"name\":\"create items\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":163}},{\"id\":166,\"module\":\"items\",\"action\":\"view\",\"label\":\"View Items\",\"name\":\"view items\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":166}},{\"id\":165,\"module\":\"items\",\"action\":\"delete\",\"label\":\"Delete Items\",\"name\":\"delete items\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":165}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:52:51'),
(306, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":167,\"module\":\"purchase-order\",\"action\":\"create\",\"label\":\"Create Purchase Order\",\"name\":\"create purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":167}},{\"id\":168,\"module\":\"purchase-order\",\"action\":\"edit\",\"label\":\"Edit Purchase Order\",\"name\":\"edit purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":168}},{\"id\":169,\"module\":\"purchase-order\",\"action\":\"view\",\"label\":\"View Purchase Order\",\"name\":\"view purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":169}},{\"id\":170,\"module\":\"purchase-order\",\"action\":\"view_details\",\"label\":\"View_details Purchase Order\",\"name\":\"view_details purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":170}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 08:53:22'),
(307, 2, 'create', 'Purchase Order', 'purchase_orders', 10, NULL, '{\"po_number\":\"PO-0010\",\"po_date\":\"2026-04-19T18:30:00.000000Z\",\"purchase_commission_agent_id\":\"1\",\"commission\":\"3.00\",\"supplier_id\":\"5\",\"reference_no\":\"5200\",\"reference_date\":\"2026-04-19T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":\"3\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"150.00\",\"sub_total\":\"22500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"21825.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"2.50\",\"sgst_percent\":\"2.50\",\"tax_amount\":\"1091.25\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"22916.25\",\"is_self_closed\":false,\"updated_at\":\"2026-04-20T08:57:57.000000Z\",\"created_at\":\"2026-04-20T08:57:57.000000Z\",\"id\":10}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-04-20 08:57:57'),
(308, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":167,\"module\":\"purchase-order\",\"action\":\"create\",\"label\":\"Create Purchase Order\",\"name\":\"create purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":167}},{\"id\":169,\"module\":\"purchase-order\",\"action\":\"view\",\"label\":\"View Purchase Order\",\"name\":\"view purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":169}},{\"id\":170,\"module\":\"purchase-order\",\"action\":\"view_details\",\"label\":\"View_details Purchase Order\",\"name\":\"view_details purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":170}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:02:34'),
(309, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":169,\"module\":\"purchase-order\",\"action\":\"view\",\"label\":\"View Purchase Order\",\"name\":\"view purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":169}},{\"id\":170,\"module\":\"purchase-order\",\"action\":\"view_details\",\"label\":\"View_details Purchase Order\",\"name\":\"view_details purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":170}},{\"id\":171,\"module\":\"purchase-invoice\",\"action\":\"create\",\"label\":\"Create Purchase Invoice\",\"name\":\"create purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":171}},{\"id\":173,\"module\":\"purchase-invoice\",\"action\":\"view\",\"label\":\"View Purchase Invoice\",\"name\":\"view purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":173}},{\"id\":174,\"module\":\"purchase-invoice\",\"action\":\"view_details\",\"label\":\"View_details Purchase Invoice\",\"name\":\"view_details purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":174}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:03:38'),
(310, 2, 'update_status', 'Purchase Order Status', 'purchase_orders', 10, '{\"id\":10,\"purchase_executive_id\":null,\"po_number\":\"PO-0010\",\"po_date\":\"2026-04-19T18:30:00.000000Z\",\"purchase_commission_agent_id\":1,\"commission\":\"3.00\",\"supplier_id\":5,\"reference_no\":\"5200\",\"reference_date\":\"2026-04-19T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":3,\"payment_terms\":null,\"status\":\"Draft\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"22500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"21825.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"2.50\",\"sgst_percent\":\"2.50\",\"tax_amount\":\"1091.25\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"22916.25\",\"created_at\":\"2026-04-20T08:57:57.000000Z\",\"updated_at\":\"2026-04-20T08:57:57.000000Z\",\"deleted_at\":null}', '{\"id\":10,\"purchase_executive_id\":null,\"po_number\":\"PO-0010\",\"po_date\":\"2026-04-19T18:30:00.000000Z\",\"purchase_commission_agent_id\":1,\"commission\":\"3.00\",\"supplier_id\":5,\"reference_no\":\"5200\",\"reference_date\":\"2026-04-19T18:30:00.000000Z\",\"due_date\":\"2026-05-08T18:30:00.000000Z\",\"store_type_id\":3,\"payment_terms\":null,\"status\":\"Approved\",\"is_self_closed\":false,\"additional_attachments\":[],\"created_by\":null,\"updated_by\":null,\"total_qty\":\"150.00\",\"sub_total\":\"22500.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"21825.00\",\"other_state\":false,\"igst_percent\":\"0.00\",\"cgst_percent\":\"2.50\",\"sgst_percent\":\"2.50\",\"tax_amount\":\"1091.25\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"22916.25\",\"created_at\":\"2026-04-20T08:57:57.000000Z\",\"updated_at\":\"2026-04-20T09:03:57.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-04-20 09:03:57'),
(311, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":169,\"module\":\"purchase-order\",\"action\":\"view\",\"label\":\"View Purchase Order\",\"name\":\"view purchase-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":169}},{\"id\":171,\"module\":\"purchase-invoice\",\"action\":\"create\",\"label\":\"Create Purchase Invoice\",\"name\":\"create purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":171}},{\"id\":173,\"module\":\"purchase-invoice\",\"action\":\"view\",\"label\":\"View Purchase Invoice\",\"name\":\"view purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":173}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:06:32'),
(312, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":175,\"module\":\"grn-entry\",\"action\":\"create\",\"label\":\"Create Grn Entry\",\"name\":\"create grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":175}},{\"id\":177,\"module\":\"grn-entry\",\"action\":\"view\",\"label\":\"View Grn Entry\",\"name\":\"view grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":177}},{\"id\":178,\"module\":\"grn-entry\",\"action\":\"view_details\",\"label\":\"View_details Grn Entry\",\"name\":\"view_details grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":178}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:07:13'),
(313, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":171,\"module\":\"purchase-invoice\",\"action\":\"create\",\"label\":\"Create Purchase Invoice\",\"name\":\"create purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":171}},{\"id\":173,\"module\":\"purchase-invoice\",\"action\":\"view\",\"label\":\"View Purchase Invoice\",\"name\":\"view purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":173}},{\"id\":174,\"module\":\"purchase-invoice\",\"action\":\"view_details\",\"label\":\"View_details Purchase Invoice\",\"name\":\"view_details purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":174}},{\"id\":175,\"module\":\"grn-entry\",\"action\":\"create\",\"label\":\"Create Grn Entry\",\"name\":\"create grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":175}},{\"id\":177,\"module\":\"grn-entry\",\"action\":\"view\",\"label\":\"View Grn Entry\",\"name\":\"view grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":177}},{\"id\":178,\"module\":\"grn-entry\",\"action\":\"view_details\",\"label\":\"View_details Grn Entry\",\"name\":\"view_details grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":178}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:08:17'),
(314, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":175,\"module\":\"grn-entry\",\"action\":\"create\",\"label\":\"Create Grn Entry\",\"name\":\"create grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":175}},{\"id\":177,\"module\":\"grn-entry\",\"action\":\"view\",\"label\":\"View Grn Entry\",\"name\":\"view grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":177}},{\"id\":178,\"module\":\"grn-entry\",\"action\":\"view_details\",\"label\":\"View_details Grn Entry\",\"name\":\"view_details grn-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":178}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:08:55'),
(315, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:26.000000Z\",\"updated_at\":\"2026-04-20T05:44:26.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":171,\"module\":\"purchase-invoice\",\"action\":\"create\",\"label\":\"Create Purchase Invoice\",\"name\":\"create purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":171}},{\"id\":172,\"module\":\"purchase-invoice\",\"action\":\"edit\",\"label\":\"Edit Purchase Invoice\",\"name\":\"edit purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":172}},{\"id\":173,\"module\":\"purchase-invoice\",\"action\":\"view\",\"label\":\"View Purchase Invoice\",\"name\":\"view purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":173}},{\"id\":174,\"module\":\"purchase-invoice\",\"action\":\"view_details\",\"label\":\"View_details Purchase Invoice\",\"name\":\"view_details purchase-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":174}},{\"id\":179,\"module\":\"stock-entry\",\"action\":\"create\",\"label\":\"Create Stock Entry\",\"name\":\"create stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":179}},{\"id\":180,\"module\":\"stock-entry\",\"action\":\"edit\",\"label\":\"Edit Stock Entry\",\"name\":\"edit stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":180}},{\"id\":181,\"module\":\"stock-entry\",\"action\":\"view\",\"label\":\"View Stock Entry\",\"name\":\"view stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":181}},{\"id\":182,\"module\":\"stock-entry\",\"action\":\"stock_adjustment\",\"label\":\"Stock_adjustment Stock Entry\",\"name\":\"stock_adjustment stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":182}},{\"id\":183,\"module\":\"stock-entry\",\"action\":\"stock_adjustment_logs\",\"label\":\"Stock_adjustment_logs Stock Entry\",\"name\":\"stock_adjustment_logs stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T05:44:27.000000Z\",\"updated_at\":\"2026-04-20T05:44:27.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":183}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:09:33'),
(316, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:15:02.000000Z\",\"updated_at\":\"2026-04-20T09:15:02.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:15:02.000000Z\",\"updated_at\":\"2026-04-20T09:15:02.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:15:02.000000Z\",\"updated_at\":\"2026-04-20T09:15:02.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:15:02.000000Z\",\"updated_at\":\"2026-04-20T09:15:02.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":179,\"module\":\"stock-entry\",\"action\":\"create\",\"label\":\"Create Stock Entry\",\"name\":\"create stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:15:03.000000Z\",\"updated_at\":\"2026-04-20T09:15:03.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":179}},{\"id\":180,\"module\":\"stock-entry\",\"action\":\"view\",\"label\":\"View Stock Entry\",\"name\":\"view stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:15:03.000000Z\",\"updated_at\":\"2026-04-20T09:15:03.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":180}},{\"id\":181,\"module\":\"stock-entry\",\"action\":\"stock_adjustment\",\"label\":\"Stock_adjustment Stock Entry\",\"name\":\"stock_adjustment stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:15:03.000000Z\",\"updated_at\":\"2026-04-20T09:15:03.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":181}},{\"id\":182,\"module\":\"stock-entry\",\"action\":\"stock_adjustment_logs\",\"label\":\"Stock_adjustment_logs Stock Entry\",\"name\":\"stock_adjustment_logs stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:15:03.000000Z\",\"updated_at\":\"2026-04-20T09:15:03.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":182}},{\"id\":183,\"module\":\"debit-notes\",\"action\":\"create\",\"label\":\"Create Debit Notes\",\"name\":\"create debit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:15:03.000000Z\",\"updated_at\":\"2026-04-20T09:15:03.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":183}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:15:24'),
(317, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":179,\"module\":\"stock-entry\",\"action\":\"create\",\"label\":\"Create Stock Entry\",\"name\":\"create stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":179}},{\"id\":180,\"module\":\"stock-entry\",\"action\":\"view\",\"label\":\"View Stock Entry\",\"name\":\"view stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":180}},{\"id\":183,\"module\":\"stock-entry\",\"action\":\"stock_adjustment_logs\",\"label\":\"Stock_adjustment_logs Stock Entry\",\"name\":\"stock_adjustment_logs stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":183}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:16:46'),
(318, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":180,\"module\":\"stock-entry\",\"action\":\"view\",\"label\":\"View Stock Entry\",\"name\":\"view stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":180}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:17:30'),
(319, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":179,\"module\":\"stock-entry\",\"action\":\"create\",\"label\":\"Create Stock Entry\",\"name\":\"create stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":179}},{\"id\":180,\"module\":\"stock-entry\",\"action\":\"view\",\"label\":\"View Stock Entry\",\"name\":\"view stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":180}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:22:16');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(320, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":179,\"module\":\"stock-entry\",\"action\":\"create\",\"label\":\"Create Stock Entry\",\"name\":\"create stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":179}},{\"id\":180,\"module\":\"stock-entry\",\"action\":\"view\",\"label\":\"View Stock Entry\",\"name\":\"view stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":180}},{\"id\":183,\"module\":\"stock-entry\",\"action\":\"stock_adjustment_logs\",\"label\":\"Stock_adjustment_logs Stock Entry\",\"name\":\"stock_adjustment_logs stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":183}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:22:34'),
(321, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":179,\"module\":\"stock-entry\",\"action\":\"create\",\"label\":\"Create Stock Entry\",\"name\":\"create stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":179}},{\"id\":180,\"module\":\"stock-entry\",\"action\":\"view\",\"label\":\"View Stock Entry\",\"name\":\"view stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":180}},{\"id\":182,\"module\":\"stock-entry\",\"action\":\"stock_adjustment\",\"label\":\"Stock_adjustment Stock Entry\",\"name\":\"stock_adjustment stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":182}},{\"id\":183,\"module\":\"stock-entry\",\"action\":\"stock_adjustment_logs\",\"label\":\"Stock_adjustment_logs Stock Entry\",\"name\":\"stock_adjustment_logs stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":183}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:22:49'),
(322, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":179,\"module\":\"stock-entry\",\"action\":\"create\",\"label\":\"Create Stock Entry\",\"name\":\"create stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":179}},{\"id\":180,\"module\":\"stock-entry\",\"action\":\"view\",\"label\":\"View Stock Entry\",\"name\":\"view stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":180}},{\"id\":181,\"module\":\"stock-entry\",\"action\":\"view_details\",\"label\":\"View_details Stock Entry\",\"name\":\"view_details stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":181}},{\"id\":182,\"module\":\"stock-entry\",\"action\":\"stock_adjustment\",\"label\":\"Stock_adjustment Stock Entry\",\"name\":\"stock_adjustment stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":182}},{\"id\":183,\"module\":\"stock-entry\",\"action\":\"stock_adjustment_logs\",\"label\":\"Stock_adjustment_logs Stock Entry\",\"name\":\"stock_adjustment_logs stock-entry\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":183}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:23:05'),
(323, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":184,\"module\":\"debit-notes\",\"action\":\"create\",\"label\":\"Create Debit Notes\",\"name\":\"create debit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":184}},{\"id\":185,\"module\":\"debit-notes\",\"action\":\"edit\",\"label\":\"Edit Debit Notes\",\"name\":\"edit debit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":185}},{\"id\":186,\"module\":\"debit-notes\",\"action\":\"view\",\"label\":\"View Debit Notes\",\"name\":\"view debit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":186}},{\"id\":187,\"module\":\"debit-notes\",\"action\":\"view_details\",\"label\":\"View_details Debit Notes\",\"name\":\"view_details debit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":187}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:23:20'),
(324, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":184,\"module\":\"debit-notes\",\"action\":\"create\",\"label\":\"Create Debit Notes\",\"name\":\"create debit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":184}},{\"id\":186,\"module\":\"debit-notes\",\"action\":\"view\",\"label\":\"View Debit Notes\",\"name\":\"view debit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":186}},{\"id\":187,\"module\":\"debit-notes\",\"action\":\"view_details\",\"label\":\"View_details Debit Notes\",\"name\":\"view_details debit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":187}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:28:00'),
(325, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":188,\"module\":\"stock-consumable-return\",\"action\":\"view\",\"label\":\"View Stock Consumable Return\",\"name\":\"view stock-consumable-return\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":188}},{\"id\":189,\"module\":\"stock-consumable-return\",\"action\":\"view_details\",\"label\":\"View_details Stock Consumable Return\",\"name\":\"view_details stock-consumable-return\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":189}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:28:30'),
(326, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":188,\"module\":\"stock-consumable-return\",\"action\":\"view\",\"label\":\"View Stock Consumable Return\",\"name\":\"view stock-consumable-return\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":188}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:33:07'),
(327, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":188,\"module\":\"stock-consumable-return\",\"action\":\"view\",\"label\":\"View Stock Consumable Return\",\"name\":\"view stock-consumable-return\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":188}},{\"id\":189,\"module\":\"stock-consumable-return\",\"action\":\"view_details\",\"label\":\"View_details Stock Consumable Return\",\"name\":\"view_details stock-consumable-return\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":189}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:37:17'),
(328, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":188,\"module\":\"stock-consumable-return\",\"action\":\"view\",\"label\":\"View Stock Consumable Return\",\"name\":\"view stock-consumable-return\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":188}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:37:36'),
(329, 1, 'update', 'Job Card Entry', 'job_card_entries', 4, '{\"id\":4,\"job_card_no\":\"JC004\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC004\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"4::art|CF-34935\\\",\\\"8::art|CF-349301\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":3,\"fit_id\":1,\"patti_type_id\":2,\"collar_type_id\":2,\"cuff_type_id\":1,\"pocket_type_id\":2,\"bottom_cut_id\":1,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":1,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-20\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"76.00\",\"total_qty_hs\":\"78.00\",\"grand_total_qty\":\"154.00\",\"average\":\"1.52\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-20T04:52:33.000000Z\",\"updated_at\":\"2026-04-20T04:52:33.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776660676650.4226,\"type\":\"fs\"},{\"id\":1776660676825.516,\"type\":\"fs\"},{\"id\":1776660677210.1726,\"type\":\"hs\"}],\"values\":{\"1776660676650.4226\":{\"36\":\"0\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"10\"},\"1776660676825.516\":{\"36\":\"5\",\"38\":\"5\",\"40\":\"5\",\"42\":\"0\",\"44\":\"5\"},\"1776660677210.1726\":{\"36\":\"5\",\"38\":\"6\",\"40\":\"6\",\"42\":\"6\",\"44\":\"3\"}}},\"issue_items\":[]}', '{\"id\":4,\"job_card_no\":\"JC004\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC004\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"4::art|CF-34935\\\",\\\"8::art|CF-349301\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":3,\"fit_id\":1,\"patti_type_id\":2,\"collar_type_id\":2,\"cuff_type_id\":1,\"pocket_type_id\":2,\"bottom_cut_id\":1,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":1,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-20\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"76.00\",\"total_qty_hs\":\"78.00\",\"grand_total_qty\":\"154.00\",\"average\":\"1.52\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-20T04:52:33.000000Z\",\"updated_at\":\"2026-04-20T09:41:54.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776660676650.4226,\"type\":\"fs\"},{\"id\":1776660676825.516,\"type\":\"fs\"},{\"id\":1776660677210.1726,\"type\":\"hs\"}],\"values\":{\"1776660676650.4226\":{\"36\":\"0\",\"38\":\"10\",\"40\":\"10\",\"42\":\"10\",\"44\":\"10\"},\"1776660676825.516\":{\"36\":\"5\",\"38\":\"5\",\"40\":\"5\",\"42\":\"0\",\"44\":\"5\"},\"1776660677210.1726\":{\"36\":\"5\",\"38\":\"6\",\"40\":\"6\",\"42\":\"6\",\"44\":\"3\"}}}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:41:54'),
(330, 1, 'update', 'Job Card Entry', 'job_card_entries', 1, '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":\"1.00\",\"price_hs\":\"0.00\",\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:09:26.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}},\"issue_items\":[{\"id\":1,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":1,\"stock_entry_item_id\":5,\"raw_material_id\":1,\"qty_issue\":\"85.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"85.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"8415.00\",\"cost_per_pc\":\"99.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:07.000000Z\",\"updated_at\":\"2026-04-16T12:09:07.000000Z\",\"deleted_at\":null,\"fabric_detail\":{\"id\":1,\"job_card_entry_id\":1,\"art_no\":\"CF-0909\",\"width\":null,\"mtr\":\"85.00\",\"in_out\":\"NO\",\"n_patti\":\"WHITE\",\"fs_qty\":\"45.00\",\"hs_qty\":\"40.00\",\"total_qty\":\"99.440\",\"used_qty\":\"85.000\",\"remaining_qty\":\"14.440\",\"row_total\":85,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:06:50.000000Z\",\"deleted_at\":null}},{\"id\":2,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":2,\"stock_entry_item_id\":1,\"raw_material_id\":1,\"qty_issue\":\"150.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"150.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"96.00\",\"total_cost\":\"14400.00\",\"cost_per_pc\":\"169.41\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:14.000000Z\",\"updated_at\":\"2026-04-16T12:09:14.000000Z\",\"deleted_at\":null,\"fabric_detail\":{\"id\":2,\"job_card_entry_id\":1,\"art_no\":\"CF-34343\",\"width\":null,\"mtr\":\"150.00\",\"in_out\":\"NO\",\"n_patti\":\"WHITE\",\"fs_qty\":\"45.00\",\"hs_qty\":\"40.00\",\"total_qty\":\"150.000\",\"used_qty\":\"150.000\",\"remaining_qty\":\"0.000\",\"row_total\":85,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:06:50.000000Z\",\"deleted_at\":null}},{\"id\":3,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":3,\"stock_entry_item_id\":2,\"raw_material_id\":2,\"qty_issue\":\"160.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"0.00\",\"qty_used\":\"160.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"85.00\",\"unit_price\":\"99.00\",\"total_cost\":\"15840.00\",\"cost_per_pc\":\"186.35\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:20.000000Z\",\"updated_at\":\"2026-04-16T12:09:20.000000Z\",\"deleted_at\":null,\"fabric_detail\":{\"id\":3,\"job_card_entry_id\":1,\"art_no\":\"CF-34344\",\"width\":null,\"mtr\":\"160.00\",\"in_out\":\"NO\",\"n_patti\":\"WHITE\",\"fs_qty\":\"45.00\",\"hs_qty\":\"40.00\",\"total_qty\":\"160.000\",\"used_qty\":\"160.000\",\"remaining_qty\":\"0.000\",\"row_total\":85,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:06:50.000000Z\",\"deleted_at\":null}},{\"id\":4,\"job_card_entry_id\":1,\"job_card_article_matrix_id\":4,\"stock_entry_item_id\":3,\"raw_material_id\":3,\"qty_issue\":\"1800.00\",\"qty_adjusted\":\"0.00\",\"qty_wastage\":\"1620.00\",\"qty_used\":\"180.00\",\"bit\":\"0.00\",\"balance\":\"0.00\",\"average\":\"0.00\",\"produced_qty\":\"1800.00\",\"unit_price\":\"10.00\",\"total_cost\":\"1800.00\",\"cost_per_pc\":\"1.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-04-16T12:09:26.000000Z\",\"updated_at\":\"2026-04-16T12:09:26.000000Z\",\"deleted_at\":null,\"fabric_detail\":{\"id\":4,\"job_card_entry_id\":1,\"art_no\":\"CF-34345\",\"width\":null,\"mtr\":\"1800.00\",\"in_out\":\"NO\",\"n_patti\":\"WHITE\",\"fs_qty\":\"1080.00\",\"hs_qty\":\"720.00\",\"total_qty\":\"1800.000\",\"used_qty\":\"1800.000\",\"remaining_qty\":\"0.000\",\"row_total\":1800,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-16T12:06:50.000000Z\",\"deleted_at\":null}}]}', '{\"id\":1,\"job_card_no\":\"JC001\",\"job_card_type\":\"Regular\",\"reference_no\":\"JC001\",\"purchase_order_id\":null,\"stock_entry_ids\":\"[\\\"3::art|CF-0909\\\",\\\"1::art|CF-34343\\\",\\\"1::art|CF-34344\\\",\\\"2::art|CF-34345\\\"]\",\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":2,\"patti_type_id\":3,\"collar_type_id\":3,\"cuff_type_id\":2,\"pocket_type_id\":2,\"bottom_cut_id\":2,\"brand_id\":1,\"brand_category_id\":null,\"fabric_type_id\":2,\"item_id\":null,\"season_id\":1,\"process_group_id\":2,\"size_ratio_id\":null,\"job_card_date\":\"2026-04-16\",\"delivery_date\":\"2026-05-09\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":\"1.00\",\"price_hs\":\"0.00\",\"total_qty_fs\":\"135.00\",\"total_qty_hs\":\"120.00\",\"grand_total_qty\":\"255.00\",\"average\":\"8.61\",\"remarks\":null,\"status\":\"Production In Progress\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-04-16T12:06:50.000000Z\",\"updated_at\":\"2026-04-20T09:42:44.000000Z\",\"deleted_at\":null,\"sleeve_instances\":{\"instances\":[{\"id\":1776341019123.344,\"type\":\"hs\"},{\"id\":1776341041884.733,\"type\":\"fs\"},{\"id\":1776341042163.1191,\"type\":\"fs\"}],\"values\":{\"1776340969828.7712\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340969197.055\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776340968868.8293\":{\"36\":\"\",\"38\":\"15\",\"40\":\"15\",\"42\":\"15\",\"44\":\"5\"},\"1776341019123.344\":{\"36\":\"8\",\"38\":\"8\",\"40\":\"8\",\"42\":\"8\",\"44\":\"4\"},\"1776341036142.6514\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"\",\"44\":\"\"},\"1776341041884.733\":{\"36\":\"6\",\"38\":\"6\",\"40\":\"6\",\"42\":\"8\",\"44\":\"4\"},\"1776341042163.1191\":{\"36\":\"\",\"38\":\"\",\"40\":\"\",\"42\":\"8\",\"44\":\"\"}}}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:42:44'),
(331, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":195,\"module\":\"job-card\",\"action\":\"work-order-pdf\",\"label\":\"Work-order-pdf Job Card\",\"name\":\"work-order-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":195}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:43:24'),
(332, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":195,\"module\":\"job-card\",\"action\":\"work-order-pdf\",\"label\":\"Work-order-pdf Job Card\",\"name\":\"work-order-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":195}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:44:28'),
(333, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":195,\"module\":\"job-card\",\"action\":\"work-order-pdf\",\"label\":\"Work-order-pdf Job Card\",\"name\":\"work-order-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":195}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:45:56'),
(334, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":195,\"module\":\"job-card\",\"action\":\"work-order-pdf\",\"label\":\"Work-order-pdf Job Card\",\"name\":\"work-order-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":195}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:46:12');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(335, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":195,\"module\":\"job-card\",\"action\":\"work-order-pdf\",\"label\":\"Work-order-pdf Job Card\",\"name\":\"work-order-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":195}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:46:12'),
(336, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":195,\"module\":\"job-card\",\"action\":\"work-order-pdf\",\"label\":\"Work-order-pdf Job Card\",\"name\":\"work-order-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":195}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:46:29'),
(337, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:47:15'),
(338, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:48:17'),
(339, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 09:48:44'),
(340, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 10:15:25'),
(341, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 10:15:46'),
(342, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":199,\"module\":\"task-management\",\"action\":\"view_details\",\"label\":\"View_details Task Management\",\"name\":\"view_details task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":199}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 10:20:27'),
(343, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 10:20:51'),
(344, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":199,\"module\":\"task-management\",\"action\":\"view_details\",\"label\":\"View_details Task Management\",\"name\":\"view_details task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":199}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T09:16:08.000000Z\",\"updated_at\":\"2026-04-20T09:16:08.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 10:21:18'),
(345, 2, 'create', 'Production Receipt', 'production_receipts', 1, NULL, '{\"id\":1,\"production_id\":null,\"job_card_id\":1,\"employee_id\":null,\"order_due_date\":null,\"receipt_no\":\"RCPT-2026-0001\",\"receipt_date\":\"2026-04-20\",\"doc_no\":\"JC001\",\"doc_date\":\"2026-04-16\",\"store_type_id\":3,\"store_location_id\":3,\"status\":\"Draft\",\"remarks\":null,\"created_by\":2,\"updated_by\":null,\"created_at\":\"2026-04-20T10:26:34.000000Z\",\"updated_at\":\"2026-04-20T10:26:34.000000Z\",\"items\":[]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-04-20 10:26:34'),
(346, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 10:31:04'),
(347, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 10:31:20');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(348, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 10:34:53'),
(349, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:03:52'),
(350, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}},{\"id\":204,\"module\":\"sales-order\",\"action\":\"create\",\"label\":\"Create Sales Order\",\"name\":\"create sales-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":204}},{\"id\":205,\"module\":\"sales-order\",\"action\":\"edit\",\"label\":\"Edit Sales Order\",\"name\":\"edit sales-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":205}},{\"id\":207,\"module\":\"sales-order\",\"action\":\"view\",\"label\":\"View Sales Order\",\"name\":\"view sales-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":207}},{\"id\":206,\"module\":\"sales-order\",\"action\":\"delete\",\"label\":\"Delete Sales Order\",\"name\":\"delete sales-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":206}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:04:18'),
(351, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}},{\"id\":205,\"module\":\"sales-order\",\"action\":\"edit\",\"label\":\"Edit Sales Order\",\"name\":\"edit sales-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":205}},{\"id\":207,\"module\":\"sales-order\",\"action\":\"view\",\"label\":\"View Sales Order\",\"name\":\"view sales-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":207}},{\"id\":206,\"module\":\"sales-order\",\"action\":\"delete\",\"label\":\"Delete Sales Order\",\"name\":\"delete sales-order\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":206}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:06:40'),
(352, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}},{\"id\":208,\"module\":\"sales-invoice\",\"action\":\"create\",\"label\":\"Create Sales Invoice\",\"name\":\"create sales-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":208}},{\"id\":209,\"module\":\"sales-invoice\",\"action\":\"edit\",\"label\":\"Edit Sales Invoice\",\"name\":\"edit sales-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":209}},{\"id\":211,\"module\":\"sales-invoice\",\"action\":\"view\",\"label\":\"View Sales Invoice\",\"name\":\"view sales-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":211}},{\"id\":210,\"module\":\"sales-invoice\",\"action\":\"delete\",\"label\":\"Delete Sales Invoice\",\"name\":\"delete sales-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":210}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:07:02'),
(353, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}},{\"id\":211,\"module\":\"sales-invoice\",\"action\":\"view\",\"label\":\"View Sales Invoice\",\"name\":\"view sales-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":211}},{\"id\":210,\"module\":\"sales-invoice\",\"action\":\"delete\",\"label\":\"Delete Sales Invoice\",\"name\":\"delete sales-invoice\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":210}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:09:00'),
(354, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}},{\"id\":212,\"module\":\"credit-notes\",\"action\":\"create\",\"label\":\"Create Credit Notes\",\"name\":\"create credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":212}},{\"id\":213,\"module\":\"credit-notes\",\"action\":\"edit\",\"label\":\"Edit Credit Notes\",\"name\":\"edit credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":213}},{\"id\":215,\"module\":\"credit-notes\",\"action\":\"view\",\"label\":\"View Credit Notes\",\"name\":\"view credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":215}},{\"id\":214,\"module\":\"credit-notes\",\"action\":\"delete\",\"label\":\"Delete Credit Notes\",\"name\":\"delete credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":214}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:09:20'),
(355, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:39.000000Z\",\"updated_at\":\"2026-04-20T10:28:39.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}},{\"id\":215,\"module\":\"credit-notes\",\"action\":\"view\",\"label\":\"View Credit Notes\",\"name\":\"view credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":215}},{\"id\":214,\"module\":\"credit-notes\",\"action\":\"delete\",\"label\":\"Delete Credit Notes\",\"name\":\"delete credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T10:28:40.000000Z\",\"updated_at\":\"2026-04-20T10:28:40.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":214}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:16:19');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(356, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}},{\"id\":215,\"module\":\"credit-notes\",\"action\":\"edit\",\"label\":\"Edit Credit Notes\",\"name\":\"edit credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":215}},{\"id\":214,\"module\":\"credit-notes\",\"action\":\"create\",\"label\":\"Create Credit Notes\",\"name\":\"create credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":214}},{\"id\":216,\"module\":\"credit-notes\",\"action\":\"delete\",\"label\":\"Delete Credit Notes\",\"name\":\"delete credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":216}},{\"id\":217,\"module\":\"credit-notes\",\"action\":\"view\",\"label\":\"View Credit Notes\",\"name\":\"view credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":217}},{\"id\":218,\"module\":\"credit-notes\",\"action\":\"view_details\",\"label\":\"View_details Credit Notes\",\"name\":\"view_details credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:43.000000Z\",\"updated_at\":\"2026-04-20T11:16:43.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":218}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:17:00'),
(357, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}},{\"id\":214,\"module\":\"credit-notes\",\"action\":\"create\",\"label\":\"Create Credit Notes\",\"name\":\"create credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":214}},{\"id\":215,\"module\":\"credit-notes\",\"action\":\"edit\",\"label\":\"Edit Credit Notes\",\"name\":\"edit credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":215}},{\"id\":217,\"module\":\"credit-notes\",\"action\":\"view\",\"label\":\"View Credit Notes\",\"name\":\"view credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":217}},{\"id\":216,\"module\":\"credit-notes\",\"action\":\"delete\",\"label\":\"Delete Credit Notes\",\"name\":\"delete credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":216}},{\"id\":218,\"module\":\"credit-notes\",\"action\":\"view_details\",\"label\":\"View_details Credit Notes\",\"name\":\"view_details credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:43.000000Z\",\"updated_at\":\"2026-04-20T11:16:43.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":218}},{\"id\":219,\"module\":\"billing\",\"action\":\"create\",\"label\":\"Create Billing\",\"name\":\"create billing\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:43.000000Z\",\"updated_at\":\"2026-04-20T11:16:43.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":219}},{\"id\":220,\"module\":\"billing\",\"action\":\"edit\",\"label\":\"Edit Billing\",\"name\":\"edit billing\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:43.000000Z\",\"updated_at\":\"2026-04-20T11:16:43.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":220}},{\"id\":221,\"module\":\"billing\",\"action\":\"view\",\"label\":\"View Billing\",\"name\":\"view billing\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:43.000000Z\",\"updated_at\":\"2026-04-20T11:16:43.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":221}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:21:48'),
(358, 1, 'update', 'Role', 'roles', 2, '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null,\"permissions\":[{\"id\":5,\"module\":\"employees\",\"action\":\"create\",\"label\":\"Create Employees\",\"name\":\"create employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":5}},{\"id\":6,\"module\":\"employees\",\"action\":\"edit\",\"label\":\"Edit Employees\",\"name\":\"edit employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":6}},{\"id\":8,\"module\":\"employees\",\"action\":\"view\",\"label\":\"View Employees\",\"name\":\"view employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":8}},{\"id\":7,\"module\":\"employees\",\"action\":\"delete\",\"label\":\"Delete Employees\",\"name\":\"delete employees\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":7}},{\"id\":190,\"module\":\"job-card\",\"action\":\"create\",\"label\":\"Create Job Card\",\"name\":\"create job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":190}},{\"id\":191,\"module\":\"job-card\",\"action\":\"edit\",\"label\":\"Edit Job Card\",\"name\":\"edit job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":191}},{\"id\":192,\"module\":\"job-card\",\"action\":\"view\",\"label\":\"View Job Card\",\"name\":\"view job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":192}},{\"id\":193,\"module\":\"job-card\",\"action\":\"view_details\",\"label\":\"View_details Job Card\",\"name\":\"view_details job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":193}},{\"id\":194,\"module\":\"job-card\",\"action\":\"fabric-consumption-pdf\",\"label\":\"Fabric-consumption-pdf Job Card\",\"name\":\"fabric-consumption-pdf job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":194}},{\"id\":196,\"module\":\"job-card\",\"action\":\"issue-item\",\"label\":\"Issue-item Job Card\",\"name\":\"issue-item job-card\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":196}},{\"id\":197,\"module\":\"task-management\",\"action\":\"edit\",\"label\":\"Edit Task Management\",\"name\":\"edit task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":197}},{\"id\":198,\"module\":\"task-management\",\"action\":\"view\",\"label\":\"View Task Management\",\"name\":\"view task-management\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":198}},{\"id\":200,\"module\":\"production-receipts\",\"action\":\"create\",\"label\":\"Create Production Receipts\",\"name\":\"create production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":200}},{\"id\":201,\"module\":\"production-receipts\",\"action\":\"edit\",\"label\":\"Edit Production Receipts\",\"name\":\"edit production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":201}},{\"id\":202,\"module\":\"production-receipts\",\"action\":\"view\",\"label\":\"View Production Receipts\",\"name\":\"view production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":202}},{\"id\":203,\"module\":\"production-receipts\",\"action\":\"view_details\",\"label\":\"View_details Production Receipts\",\"name\":\"view_details production-receipts\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":203}},{\"id\":214,\"module\":\"credit-notes\",\"action\":\"create\",\"label\":\"Create Credit Notes\",\"name\":\"create credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":214}},{\"id\":215,\"module\":\"credit-notes\",\"action\":\"edit\",\"label\":\"Edit Credit Notes\",\"name\":\"edit credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":215}},{\"id\":217,\"module\":\"credit-notes\",\"action\":\"view\",\"label\":\"View Credit Notes\",\"name\":\"view credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":217}},{\"id\":216,\"module\":\"credit-notes\",\"action\":\"delete\",\"label\":\"Delete Credit Notes\",\"name\":\"delete credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:42.000000Z\",\"updated_at\":\"2026-04-20T11:16:42.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":216}},{\"id\":218,\"module\":\"credit-notes\",\"action\":\"view_details\",\"label\":\"View_details Credit Notes\",\"name\":\"view_details credit-notes\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:43.000000Z\",\"updated_at\":\"2026-04-20T11:16:43.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":218}},{\"id\":219,\"module\":\"billing\",\"action\":\"create\",\"label\":\"Create Billing\",\"name\":\"create billing\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:43.000000Z\",\"updated_at\":\"2026-04-20T11:16:43.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":219}},{\"id\":221,\"module\":\"billing\",\"action\":\"view\",\"label\":\"View Billing\",\"name\":\"view billing\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:43.000000Z\",\"updated_at\":\"2026-04-20T11:16:43.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":221}},{\"id\":222,\"module\":\"billing\",\"action\":\"view_details\",\"label\":\"View_details Billing\",\"name\":\"view_details billing\",\"guard_name\":\"web\",\"created_at\":\"2026-04-20T11:16:43.000000Z\",\"updated_at\":\"2026-04-20T11:16:43.000000Z\",\"pivot\":{\"role_id\":2,\"permission_id\":222}}]}', '{\"id\":2,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-04-20T05:23:17.000000Z\",\"updated_at\":\"2026-04-20T05:23:17.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-04-20 11:24:48');

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
(52, '2025_12_05_123743_add_deleted_at_to_customers_table', 49),
(53, '2025_12_08_043636_add_deleted_at_to_suppliers', 50),
(54, '2025_12_08_052326_add_commission_to_suppliers_table', 51),
(55, '2025_12_08_081301_add_softdeletes_and_unique_to_service_providers', 52),
(56, '2025_12_08_082645_add_deleted_at_to_sales_agents', 53),
(57, '2025_12_08_085951_add_deleted_at_to_purchase_commission_agents', 54),
(58, '2025_12_08_102557_add_deleted_at_to_raw_materials_table', 55),
(59, '2025_12_09_082151_add_created_by_to_roles_table', 56),
(60, '2025_12_09_083824_add_updated_by_to_roles_table', 57),
(61, '2025_12_09_091137_add_created_by_updated_by_to_users_table', 58),
(62, '2025_12_09_110834_add_created_by_updated_by_to_states_table', 59),
(63, '2025_12_09_112158_add_created_by_updated_by_to_cities_table', 60),
(64, '2025_12_09_171614_add_created_by_updated_by_to_places_table', 61),
(65, '2025_12_09_174219_add_created_by_updated_by_to_uoms_table', 62),
(66, '2025_12_10_152751_create_settings_table', 63),
(67, '2025_12_10_171220_create_purchase_orders_table', 64),
(68, '2025_12_10_171235_create_purchase_order_items_table', 65),
(69, '2025_12_11_123035_add_created_by_updated_by_to_charges_table', 66),
(70, '2025_12_12_120355_create_purchase_invoices_table', 67),
(71, '2025_12_12_120417_create_purchase_invoice_items_table', 68),
(72, '2025_12_12_120427_create_purchase_invoice_charges_table', 69),
(73, '2025_12_13_122457_add_qty_to_purchase_invoice_items', 70),
(74, '2025_12_13_141407_add_charge_name_to_purchase_invoices_charges_table', 71),
(75, '2025_12_13_165408_add_fields_to_purchase_invoices_table', 72),
(76, '2025_12_15_155324_add_created_by_updated_by_to_operation_stages_table', 73),
(77, '2025_12_15_160156_add_created_by_updated_by_to_zones_table', 74),
(78, '2025_12_15_161034_add_created_by_updated_by_to_size_ratios_table', 75),
(79, '2025_12_15_165107_add_created_by_updated_by_to_fabric_types', 76),
(80, '2025_12_15_165719_add_created_by_updated_by_to_store_locations_table', 77),
(81, '2025_12_15_165942_add_created_by_updated_by_to_departments_table', 78),
(82, '2025_12_15_170206_add_created_by_updated_by_to_taxes_table', 79),
(83, '2025_12_15_170601_add_created_by_updated_by_to_customers_table', 80),
(84, '2025_12_15_171544_add_created_by_updated_by_to_suppliers_table', 81),
(85, '2025_12_15_172149_add_created_by_updated_by_to_service_providers_table', 82),
(86, '2025_12_15_173220_add_created_by_updated_by_to_sales_agents_tables', 83),
(87, '2025_12_15_173516_add_created_by_updated_by_to_purchase_commission_agents_table', 84),
(88, '2025_12_15_174338_add_updated_by_raw_materials_table', 85),
(89, '2025_12_15_175115_add_updated_by_brands_table', 86),
(90, '2025_12_15_180246_add_updated_by_brand_categories', 87),
(91, '2025_12_15_180619_add_updated_by_items_table', 88),
(92, '2025_12_15_181353_add_created_by_updated_by_purchase_orders_table', 89),
(93, '2025_12_15_181822_add_created_by_updated_by_purchase_invoices_table', 90),
(94, '2025_12_18_154636_create_grn_entry_items_table', 91),
(95, '2025_12_18_create_grn_full_structure_tables', 92),
(96, '2025_12_19_104301_update_grn_status_to_workflow', 93),
(97, '2025_12_19_111351_add_image_to_grn_entry_items', 94),
(98, '2025_12_19_140000_create_stock_entries_table', 95),
(99, '2025_12_19_140001_create_stock_entry_items_table', 96),
(100, '2025_12_23_155945_create_purchase_invoice_payments_table', 97),
(101, '2025_12_23_172501_add_transaction_id_to_purchase_invoices_table', 98),
(102, '2025_12_27_115951_add_extra_fields_to_settings_table', 99),
(103, '2025_12_30_153556_add_round_off_to_purchase_invoices_table', 100),
(104, '2025_12_30_155136_add_round_off_type_to_purchase_invoices_table', 101),
(105, '2025_12_30_181653_add_prefixes_to_settings', 102),
(106, '2026_01_05_100001_add_extra_fields_to_colors_table', 103),
(107, '2026_01_05_110550_create_debit_notes_tables', 104),
(108, '2026_01_05_113321_add_tax_columns_to_debit_notes_table', 105),
(109, '2026_01_05_121256_create_styles_table', 106),
(110, '2026_01_05_123958_add_extra_fields_to_po_tables', 107),
(111, '2026_01_05_140530_add_style_id_to_purchase_order_items_table', 108),
(112, '2026_01_07_115526_create_fits_table', 109),
(113, '2026_01_07_115527_create_patti_types_table', 110),
(114, '2026_01_07_115528_create_collar_types_table', 111),
(115, '2026_01_07_115528_create_cuff_types_table', 112),
(116, '2026_01_07_115529_create_bottom_cuts_table', 113),
(117, '2026_01_07_115530_create_pocket_types_table', 114),
(120, '2026_01_07_121540_create_process_groups_table', 115),
(121, '2026_01_07_121540_create_seasons_table', 116),
(122, '2026_01_07_133507_create_job_card_entries_table', 117),
(123, '2026_01_07_133507_create_job_card_items_table', 118),
(124, '2026_01_07_133507_create_job_card_images_table', 119),
(125, '2026_01_07_133507_create_job_card_operations_table', 120),
(126, '2026_01_07_134428_add_fields_to_job_card_entries_table', 121),
(127, '2026_01_07_174111_add_reference_no_to_job_card_entries_table', 122),
(128, '2026_01_07_175011_update_job_card_entries_schema', 123),
(129, '2026_01_07_180408_add_delivery_date_to_job_card_entries_table', 124),
(130, '2026_01_07_181654_add_received_by_to_job_card_operations_table', 125),
(131, '2026_01_08_100409_add_art_no_to_job_card_images_table', 126),
(132, '2026_01_08_101014_refactor_job_card_images_table', 127),
(133, '2026_01_08_111030_create_job_card_article_matrices_table', 128),
(134, '2026_01_08_112136_add_hs_46_to_job_card_article_matrices_table', 129),
(135, '2026_01_08_115554_add_fabric_details_to_job_card_article_matrices_table', 130),
(136, '2026_01_08_123251_add_prices_to_job_card_entries_table', 131),
(137, '2026_01_08_173558_rename_sales_agent_id_in_purchase_orders_table', 132),
(138, '2026_01_08_174532_drop_order_date_from_purchase_orders_table', 133),
(139, '2026_01_09_093056_add_brand_id_to_purchase_order_items_table', 134),
(140, '2026_01_09_101753_add_fabric_width_id_to_purchase_order_items_table', 135),
(141, '2026_01_09_110719_create_job_card_issue_items_table', 136),
(142, '2026_01_09_154838_add_stock_entry_item_id_to_job_card_issue_items', 137),
(143, '2026_01_09_165049_add_hs_36_to_job_card_article_matrices_table', 138),
(144, '2026_01_10_104326_add_average_to_job_card_entries_table', 139),
(145, '2026_01_10_115538_add_produced_qty_to_job_card_issue_items_table', 140),
(146, '2026_01_10_142005_rename_job_card_tables_v2', 141),
(147, '2026_01_10_142936_add_costing_columns_to_job_card_issue_items_table', 142),
(149, '2026_01_10_181731_add_price_to_stock_entries_table', 143),
(150, '2026_01_10_152346_add_sleeve_type_to_job_card_issue_items_table', 144),
(151, '2026_01_12_104230_add_code_to_brands_table', 145),
(152, '2026_01_12_105150_add_code_to_styles_table', 146),
(153, '2026_01_12_121600_add_ex_labels_to_job_card_entries', 147),
(154, '2026_01_14_160338_add_status_to_store_types_table', 148),
(155, '2026_01_19_122332_refactor_job_card_size_storage', 149),
(156, '2026_01_19_140450_create_shifts_table', 150),
(157, '2026_01_19_140850_create_production_services_table', 151),
(158, '2026_01_19_145351_add_is_plant_to_service_providers_table', 152),
(159, '2026_01_19_145359_create_resources_table', 153),
(160, '2026_01_19_150726_create_service_provider_production_service_table', 154),
(161, '2026_01_19_152043_remove_process_and_capacity_from_resources_table', 155),
(162, '2026_01_19_165525_add_plant_and_stores_to_job_card_entries_table', 156),
(163, '2026_01_19_170131_add_audit_and_soft_delete_to_store_types_table', 157),
(164, '2026_01_19_183000_add_operation_stage_id_to_production_services_table', 158),
(165, '2026_01_20_135523_add_fields_to_production_services_table', 159),
(166, '2026_01_20_140420_remove_extra_fields_from_production_services_table', 160),
(167, '2026_01_20_154810_create_productions_table', 161),
(168, '2026_01_20_154811_create_process_schedules_table', 162),
(169, '2026_01_20_154812_create_process_schedule_services_table', 163),
(171, '2026_01_21_120108_create_job_card_issue_stock_details_table', 164),
(172, '2026_01_21_141212_create_tasks_table', 165),
(173, '2026_01_21_151758_add_services_to_tasks_table', 166),
(174, '2026_01_21_154405_add_due_date_to_tasks_table', 167),
(177, '2026_01_21_171937_create_task_statuses_table', 168),
(178, '2026_01_21_172039_change_status_to_string_in_tasks_table', 169),
(181, '2026_01_21_173922_create_task_receives_table', 170),
(182, '2026_01_21_180533_add_received_services_to_task_receives_table', 171),
(183, '2026_01_22_095357_add_shift_id_to_task_receives_table', 172),
(184, '2026_01_22_101813_create_task_adjustments_table', 173),
(185, '2026_01_22_120000_create_production_receipts_table', 174),
(186, '2026_01_23_101332_add_columns_to_production_receipts_tables', 175),
(187, '2026_01_24_095422_add_size_to_stock_entry_items', 176),
(188, '2026_01_24_100915_add_store_location_to_production_receipts', 177),
(189, '2026_01_24_101339_add_entry_type_to_stock_entries', 178),
(190, '2026_01_24_105823_create_stock_consumable_issues_table', 179),
(191, '2026_01_24_105830_create_stock_consumable_issue_items_table', 180),
(192, '2026_01_24_110339_create_stock_consumable_stock_details_table', 181),
(193, '2026_01_24_114743_create_production_stage_consumables_table', 182),
(194, '2026_01_24_175212_add_operation_stage_id_to_service_providers_table', 183),
(195, '2026_01_24_181032_add_service_provider_id_to_employees_table', 184),
(196, '2026_01_24_182551_add_service_provider_id_to_users_table_v2', 185),
(197, '2026_01_27_113703_add_raw_material_id_to_task_adjustments', 186),
(198, '2026_01_27_174537_add_adjustment_types_to_stock_consumable_issues_table', 187),
(199, '2026_01_27_181941_add_rework_return_types_to_stock_consumable_issues_table', 188),
(200, '2026_01_28_134510_create_job_card_sleeve_meters_table', 189),
(201, '2026_01_29_094305_remove_sleeve_type_from_job_card_issue_items_table', 190),
(202, '2026_01_29_123717_add_audit_fields_to_task_adjustments_table', 191),
(203, '2026_01_29_153357_create_stock_entry_adjustments_table', 192),
(204, '2026_01_29_183000_create_task_adjustment_items_table_v2', 193),
(205, '2026_01_30_101000_make_adjustment_type_nullable_in_task_adjustments_table', 194),
(206, '2026_01_31_093000_add_sleeve_wise_qty_to_job_card_fabric_details_table', 195),
(207, '2026_01_31_111923_create_job_card_fabric_consumptions_table', 196),
(208, '2026_02_03_134415_add_sleeve_type_to_production_stage_consumables_table', 197),
(209, '2026_02_03_140959_create_production_stage_actual_consumables_table', 198),
(210, '2026_02_03_143500_add_transaction_fields_to_production_stage_consumables', 199),
(211, '2026_02_04_133552_add_consumable_columns_to_production_stage_consumables', 200),
(213, '2026_02_06_163736_refactor_job_card_entry_columns_to_foreign_keys', 201),
(214, '2026_02_10_152200_add_missing_fields_to_job_card_operations_table', 202),
(215, '2026_02_10_152201_add_operation_stage_id_to_users_table', 203),
(216, '2026_02_10_154000_add_remarks_to_job_card_operations_table', 204),
(217, '2026_02_10_154941_make_cutting_fields_nullable', 205),
(218, '2026_02_10_180606_add_total_hrs_to_tasks_table', 206),
(219, '2026_02_11_093458_add_job_card_entry_id_to_process_schedules', 207),
(220, '2026_02_11_094053_fix_process_schedules_stage_columns', 208),
(221, '2026_02_11_104647_create_task_assign_employees_table', 209),
(222, '2026_02_11_105920_update_services_column_in_task_assign_employees_table', 210),
(223, '2026_02_11_182000_add_advanced_fields_to_task_management_tables', 211),
(224, '2026_02_12_093738_add_qty_cols_to_task_assign_employees', 212),
(225, '2026_02_12_104020_add_qc_fields_to_task_assign_employees_table', 213),
(226, '2026_02_12_110145_add_service_id_to_task_adjustment_items_table', 214),
(227, '2026_02_12_145619_create_task_logs_table', 215),
(228, '2026_02_13_150000_update_customers_table_change_stores_to_store_id', 216),
(229, '2026_02_14_113854_rename_style_to_style_id_in_items_table', 217),
(230, '2026_02_17_103311_add_store_id_to_suppliers_table', 218),
(231, '2026_02_17_110000_cleanup_task_adjustment_tables', 219),
(232, '2026_02_17_122300_create_payments_table', 220),
(233, '2026_02_17_144521_create_document_repositories_table', 221),
(234, '2026_02_18_110237_create_backups_table', 222),
(235, '2026_02_20_112238_add_raw_material_id_to_job_card_issue_items_v2', 223),
(236, '2026_02_20_121249_add_grn_info_to_task_adjustment_items', 224),
(237, '2026_02_20_121545_add_art_no_to_stock_entry_items', 225),
(238, '2026_02_24_114558_add_employee_id_to_task_assign_employees', 226),
(239, '2026_02_25_134809_remove_emp_id_from_task_assign_employees_table', 227),
(240, '2026_02_27_112751_add_so_prefix_to_settings_table', 228),
(241, '2026_02_27_000001_create_sale_orders_table', 229),
(242, '2026_02_27_000002_create_sale_order_items_table', 230),
(243, '2026_02_27_154902_rename_size_to_size_id_in_sale_order_items_table', 231),
(244, '2026_02_27_171348_add_size_ratio_id_to_stock_entry_items', 232),
(245, '2026_02_27_174743_change_size_id_to_varchar_in_sale_order_items', 233),
(246, '2026_02_28_101613_add_new_fields_to_sale_orders_table', 234),
(247, '2026_02_28_114523_add_commission_to_sale_orders_table', 235),
(248, '2026_02_28_122120_create_sales_invoices_table', 236),
(249, '2026_02_28_122134_create_sales_invoice_items_table', 237),
(250, '2026_02_28_151442_add_summary_fields_to_sales_invoices_table', 238),
(251, '2026_02_28_153214_rename_round_off_amount_in_sales_invoices_table', 239),
(252, '2026_02_28_171942_add_mrp_to_items_and_sales_tables', 240),
(253, '2026_02_28_183000_add_mrp_to_order_and_invoice_items_tables', 241),
(254, '2026_03_02_104415_rename_sale_orders_tables', 242),
(255, '2026_03_03_140756_add_stock_entry_item_id_to_sales_invoice_items_table', 243),
(256, '2026_03_03_101000_create_billings_table', 244),
(257, '2026_03_03_170801_create_credit_notes_table', 245),
(258, '2026_03_03_170809_create_credit_note_items_table', 246),
(259, '2026_03_03_171749_add_extra_fields_to_credit_note_items', 247),
(260, '2026_03_03_172738_add_size_to_credit_note_items', 248),
(261, '2026_03_03_173745_add_brand_category_id_to_credit_note_items', 249),
(262, '2026_03_04_141356_rename_fabric_type_id_to_material_type_in_raw_materials_table', 250),
(263, '2026_03_05_114544_rename_art_no_to_supplier_design_name_in_purchase_order_items', 251),
(264, '2026_03_06_145512_create_ticket_categories_table', 252),
(265, '2026_03_06_145519_create_tickets_table', 253),
(266, '2026_03_07_140000_update_sales_agents_and_users_for_login', 254),
(267, '2026_03_16_000000_add_zone_id_to_sales_agents_table', 255),
(268, '2026_03_16_110723_add_zone_id_to_sales_orders', 256),
(269, '2026_03_16_112105_create_shipping_methods_table', 257),
(270, '2026_03_16_112107_create_transport_modes_table', 258),
(271, '2026_03_16_112859_refactor_logistics_columns_in_sales_orders', 259),
(272, '2026_03_16_120826_add_season_code_to_seasons_table', 260),
(273, '2026_03_16_120830_refactor_dispatch_from_in_sales_orders', 261),
(274, '2026_03_16_122137_rename_eway_bill_no_to_transport_gst_no_in_sales_orders', 262),
(275, '2026_03_16_122834_drop_lr_no_from_sales_orders', 263),
(276, '2026_03_16_142103_add_box_discount_fields_to_sales_orders_table', 264),
(277, '2026_03_19_121850_add_round_off_fields_to_credit_notes_table', 265),
(278, '2026_03_23_143308_add_transport_fields_to_purchase_invoices_table', 266),
(279, '2026_03_23_144708_add_eway_billno_to_purchase_invoices_table', 267),
(280, '2026_03_23_181536_add_variations_and_color_to_items_and_production_tables', 268),
(281, '2026_03_24_104825_add_stock_entry_ids_to_job_card_entries_table', 269),
(283, '2026_03_25_111628_add_sku_and_qrcode_to_stock_entry_items_table', 270),
(284, '2026_03_25_113350_add_missing_columns_to_production_receipt_items_table', 271),
(286, '2026_03_25_114152_add_color_id_to_receipt_and_stock_items_tables', 272),
(287, '2026_03_25_123958_add_fabric_type_id_to_job_card_entries_table', 273),
(288, '2026_03_25_145649_add_item_id_to_stock_entry_items_table', 274),
(289, '2026_03_28_114653_create_production_movements_table', 275),
(290, '2026_04_01_111543_add_commission_fields_to_purchase_invoices_table', 276),
(291, '2026_04_01_112000_add_is_self_closed_to_purchase_orders_table', 277),
(292, '2026_04_02_102754_create_debit_note_charges_table', 278),
(293, '2026_04_02_103734_make_tax_and_charges_nullable_in_debit_notes_table', 279),
(294, '2026_04_03_134255_add_item_details_to_job_card_fabric_details_table', 280),
(295, '2026_04_08_061230_create_fabric_sizes_table', 281),
(296, '2026_04_08_170724_add_gst_percents_to_suppliers_table', 282),
(297, '2026_04_08_172112_add_brand_and_width_to_purchase_invoice_items_table', 283),
(298, '2026_04_09_103517_add_sleeve_instances_to_job_card_entries_table', 284),
(299, '2026_04_09_112746_create_job_card_lay_marks_table', 285),
(300, '2026_04_13_151738_add_color_id_to_grn_entry_items', 286);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 3),
(1, 'App\\Models\\User', 4),
(1, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `operation_stages`
--

CREATE TABLE `operation_stages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `operation_stage_name` varchar(100) NOT NULL,
  `working_days` int(11) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `operation_stages`
--

INSERT INTO `operation_stages` (`id`, `operation_stage_name`, `working_days`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'CUTTING', 4, 'Active', '2026-04-16 09:29:47', '2026-04-16 09:30:33', NULL, 1, 1),
(2, 'STITCHING READY', 3, 'Active', '2026-04-16 09:29:55', '2026-04-16 09:30:42', NULL, 1, 1),
(3, 'STITCHING ASSEMBLE', 2, 'Active', '2026-04-16 09:30:02', '2026-04-16 09:30:48', NULL, 1, 1),
(4, 'KAJA BUTTON', 2, 'Active', '2026-04-16 09:30:08', '2026-04-16 09:30:57', NULL, 1, 1),
(5, 'TRIMMING & CHECKING', 1, 'Active', '2026-04-16 09:30:16', '2026-04-16 09:31:03', NULL, 1, 1),
(6, 'IRONING & PACKING', 2, 'Active', '2026-04-16 09:30:24', '2026-04-16 09:31:09', NULL, 1, 1);

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
-- Table structure for table `patti_types`
--

CREATE TABLE `patti_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patti_type_name` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patti_types`
--

INSERT INTO `patti_types` (`id`, `patti_type_name`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '28 MM AMERICAN PAATI', 'Active', 1, NULL, NULL, '2026-04-16 09:56:53', '2026-04-16 09:56:53'),
(2, '28 MM INSIDE PAATI', 'Active', 1, NULL, NULL, '2026-04-16 09:57:00', '2026-04-16 09:57:00'),
(3, '28 MM WITHOUT PAATI', 'Active', 1, NULL, NULL, '2026-04-16 09:57:07', '2026-04-16 09:57:07');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_no` varchar(50) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `transaction_no` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `cheque_no` varchar(100) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `payment_no`, `payment_type`, `reference_type`, `reference_id`, `reference_no`, `payment_mode`, `amount`, `payment_date`, `transaction_no`, `bank_name`, `cheque_no`, `cheque_date`, `attachment`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PAY-20260319-69BBA000C4D39', 'Supplier Payment', 'Purchase Invoice', 1, 'INV-0001', 'Cash', 23208.80, '2026-03-19', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-03-19 07:04:32', '2026-03-19 07:04:32', NULL),
(2, 'PAY-20260320-69BD05D003F9F', 'Customer Collection', 'Sales Invoice', 1, 'SINV-0001', 'Cash', 430.44, '2026-03-20', NULL, NULL, NULL, NULL, 'uploads/payments/1773995472_buttons.jpg', NULL, 1, NULL, '2026-03-20 08:31:12', '2026-03-20 08:31:12', NULL);

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
(1, 'roles', 'create', 'Create Roles', 'create roles', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(2, 'roles', 'edit', 'Edit Roles', 'edit roles', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(3, 'roles', 'delete', 'Delete Roles', 'delete roles', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(4, 'roles', 'view', 'View Roles', 'view roles', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(5, 'employees', 'create', 'Create Employees', 'create employees', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(6, 'employees', 'edit', 'Edit Employees', 'edit employees', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(7, 'employees', 'delete', 'Delete Employees', 'delete employees', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(8, 'employees', 'view', 'View Employees', 'view employees', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(9, 'employees', 'view_details', 'View_details Employees', 'view_details employees', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(10, 'states', 'create', 'Create States', 'create states', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(11, 'states', 'edit', 'Edit States', 'edit states', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(12, 'states', 'delete', 'Delete States', 'delete states', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(13, 'states', 'view', 'View States', 'view states', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(14, 'cities', 'create', 'Create Cities', 'create cities', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(15, 'cities', 'edit', 'Edit Cities', 'edit cities', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(16, 'cities', 'delete', 'Delete Cities', 'delete cities', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(17, 'cities', 'view', 'View Cities', 'view cities', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(18, 'service-points', 'create', 'Create Service Points', 'create service-points', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(19, 'service-points', 'edit', 'Edit Service Points', 'edit service-points', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(20, 'service-points', 'delete', 'Delete Service Points', 'delete service-points', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(21, 'service-points', 'view', 'View Service Points', 'view service-points', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(22, 'uoms', 'create', 'Create Uoms', 'create uoms', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(23, 'uoms', 'edit', 'Edit Uoms', 'edit uoms', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(24, 'uoms', 'delete', 'Delete Uoms', 'delete uoms', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(25, 'uoms', 'view', 'View Uoms', 'view uoms', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(26, 'colors', 'create', 'Create Colors', 'create colors', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(27, 'colors', 'edit', 'Edit Colors', 'edit colors', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(28, 'colors', 'delete', 'Delete Colors', 'delete colors', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(29, 'colors', 'view', 'View Colors', 'view colors', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(30, 'operation-stages', 'create', 'Create Operation Stages', 'create operation-stages', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(31, 'operation-stages', 'edit', 'Edit Operation Stages', 'edit operation-stages', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(32, 'operation-stages', 'delete', 'Delete Operation Stages', 'delete operation-stages', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(33, 'operation-stages', 'view', 'View Operation Stages', 'view operation-stages', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(34, 'zones', 'create', 'Create Zones', 'create zones', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(35, 'zones', 'edit', 'Edit Zones', 'edit zones', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(36, 'zones', 'delete', 'Delete Zones', 'delete zones', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(37, 'zones', 'view', 'View Zones', 'view zones', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(38, 'size-ratio', 'create', 'Create Size Ratio', 'create size-ratio', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(39, 'size-ratio', 'edit', 'Edit Size Ratio', 'edit size-ratio', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(40, 'size-ratio', 'delete', 'Delete Size Ratio', 'delete size-ratio', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(41, 'size-ratio', 'view', 'View Size Ratio', 'view size-ratio', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(42, 'fabric-type', 'create', 'Create Fabric Type', 'create fabric-type', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(43, 'fabric-type', 'edit', 'Edit Fabric Type', 'edit fabric-type', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(44, 'fabric-type', 'delete', 'Delete Fabric Type', 'delete fabric-type', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(45, 'fabric-type', 'view', 'View Fabric Type', 'view fabric-type', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(46, 'fabric-sizes', 'create', 'Create Fabric Sizes', 'create fabric-sizes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(47, 'fabric-sizes', 'edit', 'Edit Fabric Sizes', 'edit fabric-sizes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(48, 'fabric-sizes', 'delete', 'Delete Fabric Sizes', 'delete fabric-sizes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(49, 'fabric-sizes', 'view', 'View Fabric Sizes', 'view fabric-sizes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(50, 'charges', 'create', 'Create Charges', 'create charges', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(51, 'charges', 'edit', 'Edit Charges', 'edit charges', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(52, 'charges', 'delete', 'Delete Charges', 'delete charges', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(53, 'charges', 'view', 'View Charges', 'view charges', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(54, 'store-location', 'create', 'Create Store Location', 'create store-location', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(55, 'store-location', 'edit', 'Edit Store Location', 'edit store-location', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(56, 'store-location', 'delete', 'Delete Store Location', 'delete store-location', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(57, 'store-location', 'view', 'View Store Location', 'view store-location', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(58, 'departments', 'create', 'Create Departments', 'create departments', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(59, 'departments', 'edit', 'Edit Departments', 'edit departments', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(60, 'departments', 'delete', 'Delete Departments', 'delete departments', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(61, 'departments', 'view', 'View Departments', 'view departments', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(62, 'taxes', 'create', 'Create Taxes', 'create taxes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(63, 'taxes', 'edit', 'Edit Taxes', 'edit taxes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(64, 'taxes', 'delete', 'Delete Taxes', 'delete taxes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(65, 'taxes', 'view', 'View Taxes', 'view taxes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(66, 'styles', 'create', 'Create Styles', 'create styles', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(67, 'styles', 'edit', 'Edit Styles', 'edit styles', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(68, 'styles', 'delete', 'Delete Styles', 'delete styles', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(69, 'styles', 'view', 'View Styles', 'view styles', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(70, 'stores', 'create', 'Create Stores', 'create stores', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(71, 'stores', 'edit', 'Edit Stores', 'edit stores', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(72, 'stores', 'delete', 'Delete Stores', 'delete stores', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(73, 'stores', 'view', 'View Stores', 'view stores', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(74, 'shipping-methods', 'create', 'Create Shipping Methods', 'create shipping-methods', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(75, 'shipping-methods', 'edit', 'Edit Shipping Methods', 'edit shipping-methods', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(76, 'shipping-methods', 'delete', 'Delete Shipping Methods', 'delete shipping-methods', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(77, 'shipping-methods', 'view', 'View Shipping Methods', 'view shipping-methods', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(78, 'transport-mode', 'create', 'Create Transport Mode', 'create transport-mode', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(79, 'transport-mode', 'edit', 'Edit Transport Mode', 'edit transport-mode', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(80, 'transport-mode', 'delete', 'Delete Transport Mode', 'delete transport-mode', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(81, 'transport-mode', 'view', 'View Transport Mode', 'view transport-mode', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(82, 'fits', 'create', 'Create Fits', 'create fits', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(83, 'fits', 'edit', 'Edit Fits', 'edit fits', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(84, 'fits', 'delete', 'Delete Fits', 'delete fits', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(85, 'fits', 'view', 'View Fits', 'view fits', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(86, 'patti-types', 'create', 'Create Patti Types', 'create patti-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(87, 'patti-types', 'edit', 'Edit Patti Types', 'edit patti-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(88, 'patti-types', 'delete', 'Delete Patti Types', 'delete patti-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(89, 'patti-types', 'view', 'View Patti Types', 'view patti-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(90, 'collar-types', 'create', 'Create Collar Types', 'create collar-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(91, 'collar-types', 'edit', 'Edit Collar Types', 'edit collar-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(92, 'collar-types', 'delete', 'Delete Collar Types', 'delete collar-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(93, 'collar-types', 'view', 'View Collar Types', 'view collar-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(94, 'cuff-types', 'create', 'Create Cuff Types', 'create cuff-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(95, 'cuff-types', 'edit', 'Edit Cuff Types', 'edit cuff-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(96, 'cuff-types', 'delete', 'Delete Cuff Types', 'delete cuff-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(97, 'cuff-types', 'view', 'View Cuff Types', 'view cuff-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(98, 'pocket-types', 'create', 'Create Pocket Types', 'create pocket-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(99, 'pocket-types', 'edit', 'Edit Pocket Types', 'edit pocket-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(100, 'pocket-types', 'delete', 'Delete Pocket Types', 'delete pocket-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(101, 'pocket-types', 'view', 'View Pocket Types', 'view pocket-types', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(102, 'bottom-cuts', 'create', 'Create Bottom Cuts', 'create bottom-cuts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(103, 'bottom-cuts', 'edit', 'Edit Bottom Cuts', 'edit bottom-cuts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(104, 'bottom-cuts', 'delete', 'Delete Bottom Cuts', 'delete bottom-cuts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(105, 'bottom-cuts', 'view', 'View Bottom Cuts', 'view bottom-cuts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(106, 'process-groups', 'create', 'Create Process Groups', 'create process-groups', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(107, 'process-groups', 'edit', 'Edit Process Groups', 'edit process-groups', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(108, 'process-groups', 'delete', 'Delete Process Groups', 'delete process-groups', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(109, 'process-groups', 'view', 'View Process Groups', 'view process-groups', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(110, 'seasons', 'create', 'Create Seasons', 'create seasons', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(111, 'seasons', 'edit', 'Edit Seasons', 'edit seasons', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(112, 'seasons', 'delete', 'Delete Seasons', 'delete seasons', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(113, 'seasons', 'view', 'View Seasons', 'view seasons', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(114, 'shifts', 'create', 'Create Shifts', 'create shifts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(115, 'shifts', 'edit', 'Edit Shifts', 'edit shifts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(116, 'shifts', 'delete', 'Delete Shifts', 'delete shifts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(117, 'shifts', 'view', 'View Shifts', 'view shifts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(118, 'production-services', 'create', 'Create Production Services', 'create production-services', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(119, 'production-services', 'edit', 'Edit Production Services', 'edit production-services', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(120, 'production-services', 'delete', 'Delete Production Services', 'delete production-services', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(121, 'production-services', 'view', 'View Production Services', 'view production-services', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(122, 'customers', 'create', 'Create Customers', 'create customers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(123, 'customers', 'edit', 'Edit Customers', 'edit customers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(124, 'customers', 'delete', 'Delete Customers', 'delete customers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(125, 'customers', 'view', 'View Customers', 'view customers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(126, 'customers', 'view_details', 'View_details Customers', 'view_details customers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(127, 'suppliers', 'create', 'Create Suppliers', 'create suppliers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(128, 'suppliers', 'edit', 'Edit Suppliers', 'edit suppliers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(129, 'suppliers', 'delete', 'Delete Suppliers', 'delete suppliers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(130, 'suppliers', 'view', 'View Suppliers', 'view suppliers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(131, 'suppliers', 'view_details', 'View_details Suppliers', 'view_details suppliers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(132, 'service-providers', 'create', 'Create Service Providers', 'create service-providers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(133, 'service-providers', 'edit', 'Edit Service Providers', 'edit service-providers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(134, 'service-providers', 'delete', 'Delete Service Providers', 'delete service-providers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(135, 'service-providers', 'view', 'View Service Providers', 'view service-providers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(136, 'service-providers', 'view_details', 'View_details Service Providers', 'view_details service-providers', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(137, 'sales-agents', 'create', 'Create Sales Agents', 'create sales-agents', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(138, 'sales-agents', 'edit', 'Edit Sales Agents', 'edit sales-agents', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(139, 'sales-agents', 'delete', 'Delete Sales Agents', 'delete sales-agents', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(140, 'sales-agents', 'view', 'View Sales Agents', 'view sales-agents', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(141, 'sales-agents', 'view_details', 'View_details Sales Agents', 'view_details sales-agents', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(142, 'purchase-commission-agent', 'create', 'Create Purchase Commission Agent', 'create purchase-commission-agent', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(143, 'purchase-commission-agent', 'edit', 'Edit Purchase Commission Agent', 'edit purchase-commission-agent', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(144, 'purchase-commission-agent', 'delete', 'Delete Purchase Commission Agent', 'delete purchase-commission-agent', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(145, 'purchase-commission-agent', 'view', 'View Purchase Commission Agent', 'view purchase-commission-agent', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(146, 'purchase-commission-agent', 'view_details', 'View_details Purchase Commission Agent', 'view_details purchase-commission-agent', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(147, 'store-categories', 'create', 'Create Store Categories', 'create store-categories', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(148, 'store-categories', 'edit', 'Edit Store Categories', 'edit store-categories', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(149, 'store-categories', 'delete', 'Delete Store Categories', 'delete store-categories', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(150, 'store-categories', 'view', 'View Store Categories', 'view store-categories', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(151, 'raw-materials', 'create', 'Create Raw Materials', 'create raw-materials', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(152, 'raw-materials', 'edit', 'Edit Raw Materials', 'edit raw-materials', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(153, 'raw-materials', 'delete', 'Delete Raw Materials', 'delete raw-materials', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(154, 'raw-materials', 'view', 'View Raw Materials', 'view raw-materials', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(155, 'brand-categories', 'create', 'Create Brand Categories', 'create brand-categories', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(156, 'brand-categories', 'edit', 'Edit Brand Categories', 'edit brand-categories', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(157, 'brand-categories', 'delete', 'Delete Brand Categories', 'delete brand-categories', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(158, 'brand-categories', 'view', 'View Brand Categories', 'view brand-categories', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(159, 'brands', 'create', 'Create Brands', 'create brands', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(160, 'brands', 'edit', 'Edit Brands', 'edit brands', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(161, 'brands', 'delete', 'Delete Brands', 'delete brands', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(162, 'brands', 'view', 'View Brands', 'view brands', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(163, 'items', 'create', 'Create Items', 'create items', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(164, 'items', 'edit', 'Edit Items', 'edit items', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(165, 'items', 'delete', 'Delete Items', 'delete items', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(166, 'items', 'view', 'View Items', 'view items', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(167, 'purchase-order', 'create', 'Create Purchase Order', 'create purchase-order', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(168, 'purchase-order', 'edit', 'Edit Purchase Order', 'edit purchase-order', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(169, 'purchase-order', 'view', 'View Purchase Order', 'view purchase-order', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(170, 'purchase-order', 'view_details', 'View_details Purchase Order', 'view_details purchase-order', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(171, 'purchase-invoice', 'create', 'Create Purchase Invoice', 'create purchase-invoice', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(172, 'purchase-invoice', 'edit', 'Edit Purchase Invoice', 'edit purchase-invoice', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(173, 'purchase-invoice', 'view', 'View Purchase Invoice', 'view purchase-invoice', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(174, 'purchase-invoice', 'view_details', 'View_details Purchase Invoice', 'view_details purchase-invoice', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(175, 'grn-entry', 'create', 'Create Grn Entry', 'create grn-entry', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(176, 'grn-entry', 'edit', 'Edit Grn Entry', 'edit grn-entry', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(177, 'grn-entry', 'view', 'View Grn Entry', 'view grn-entry', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(178, 'grn-entry', 'view_details', 'View_details Grn Entry', 'view_details grn-entry', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(179, 'stock-entry', 'create', 'Create Stock Entry', 'create stock-entry', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(180, 'stock-entry', 'view', 'View Stock Entry', 'view stock-entry', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(181, 'stock-entry', 'view_details', 'View_details Stock Entry', 'view_details stock-entry', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(182, 'stock-entry', 'stock_adjustment', 'Stock_adjustment Stock Entry', 'stock_adjustment stock-entry', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(183, 'stock-entry', 'stock_adjustment_logs', 'Stock_adjustment_logs Stock Entry', 'stock_adjustment_logs stock-entry', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(184, 'debit-notes', 'create', 'Create Debit Notes', 'create debit-notes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(185, 'debit-notes', 'edit', 'Edit Debit Notes', 'edit debit-notes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(186, 'debit-notes', 'view', 'View Debit Notes', 'view debit-notes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(187, 'debit-notes', 'view_details', 'View_details Debit Notes', 'view_details debit-notes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(188, 'stock-consumable-return', 'view', 'View Stock Consumable Return', 'view stock-consumable-return', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(189, 'stock-consumable-return', 'view_details', 'View_details Stock Consumable Return', 'view_details stock-consumable-return', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(190, 'job-card', 'create', 'Create Job Card', 'create job-card', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(191, 'job-card', 'edit', 'Edit Job Card', 'edit job-card', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(192, 'job-card', 'view', 'View Job Card', 'view job-card', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(193, 'job-card', 'view_details', 'View_details Job Card', 'view_details job-card', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(194, 'job-card', 'fabric-consumption-pdf', 'Fabric-consumption-pdf Job Card', 'fabric-consumption-pdf job-card', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(195, 'job-card', 'work-order-pdf', 'Work-order-pdf Job Card', 'work-order-pdf job-card', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(196, 'job-card', 'issue-item', 'Issue-item Job Card', 'issue-item job-card', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(197, 'task-management', 'edit', 'Edit Task Management', 'edit task-management', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(198, 'task-management', 'view', 'View Task Management', 'view task-management', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(199, 'task-management', 'view_details', 'View_details Task Management', 'view_details task-management', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(200, 'production-receipts', 'create', 'Create Production Receipts', 'create production-receipts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(201, 'production-receipts', 'edit', 'Edit Production Receipts', 'edit production-receipts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(202, 'production-receipts', 'view', 'View Production Receipts', 'view production-receipts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(203, 'production-receipts', 'view_details', 'View_details Production Receipts', 'view_details production-receipts', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(204, 'sales-order', 'create', 'Create Sales Order', 'create sales-order', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(205, 'sales-order', 'edit', 'Edit Sales Order', 'edit sales-order', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(206, 'sales-order', 'delete', 'Delete Sales Order', 'delete sales-order', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(207, 'sales-order', 'view', 'View Sales Order', 'view sales-order', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(208, 'sales-order', 'view_details', 'View_details Sales Order', 'view_details sales-order', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(209, 'sales-invoice', 'create', 'Create Sales Invoice', 'create sales-invoice', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(210, 'sales-invoice', 'edit', 'Edit Sales Invoice', 'edit sales-invoice', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(211, 'sales-invoice', 'delete', 'Delete Sales Invoice', 'delete sales-invoice', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(212, 'sales-invoice', 'view', 'View Sales Invoice', 'view sales-invoice', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(213, 'sales-invoice', 'view_details', 'View_details Sales Invoice', 'view_details sales-invoice', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(214, 'credit-notes', 'create', 'Create Credit Notes', 'create credit-notes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(215, 'credit-notes', 'edit', 'Edit Credit Notes', 'edit credit-notes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(216, 'credit-notes', 'delete', 'Delete Credit Notes', 'delete credit-notes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(217, 'credit-notes', 'view', 'View Credit Notes', 'view credit-notes', 'web', '2026-04-20 11:16:42', '2026-04-20 11:16:42'),
(218, 'credit-notes', 'view_details', 'View_details Credit Notes', 'view_details credit-notes', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(219, 'billing', 'create', 'Create Billing', 'create billing', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(220, 'billing', 'edit', 'Edit Billing', 'edit billing', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(221, 'billing', 'view', 'View Billing', 'view billing', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(222, 'billing', 'view_details', 'View_details Billing', 'view_details billing', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(223, 'manage-payments', 'create', 'Create Manage Payments', 'create manage-payments', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(224, 'manage-payments', 'edit', 'Edit Manage Payments', 'edit manage-payments', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(225, 'manage-payments', 'delete', 'Delete Manage Payments', 'delete manage-payments', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(226, 'manage-payments', 'view', 'View Manage Payments', 'view manage-payments', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(227, 'attendance', 'create', 'Create Attendance', 'create attendance', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(228, 'attendance', 'edit', 'Edit Attendance', 'edit attendance', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(229, 'attendance', 'delete', 'Delete Attendance', 'delete attendance', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(230, 'attendance', 'view', 'View Attendance', 'view attendance', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(231, 'manage-leaves', 'create', 'Create Manage Leaves', 'create manage-leaves', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(232, 'manage-leaves', 'edit', 'Edit Manage Leaves', 'edit manage-leaves', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(233, 'manage-leaves', 'delete', 'Delete Manage Leaves', 'delete manage-leaves', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(234, 'manage-leaves', 'view', 'View Manage Leaves', 'view manage-leaves', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(235, 'overtime-bonus', 'create', 'Create Overtime Bonus', 'create overtime-bonus', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(236, 'overtime-bonus', 'edit', 'Edit Overtime Bonus', 'edit overtime-bonus', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(237, 'overtime-bonus', 'delete', 'Delete Overtime Bonus', 'delete overtime-bonus', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(238, 'overtime-bonus', 'view', 'View Overtime Bonus', 'view overtime-bonus', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(239, 'salary-calculation', 'create', 'Create Salary Calculation', 'create salary-calculation', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(240, 'salary-calculation', 'edit', 'Edit Salary Calculation', 'edit salary-calculation', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(241, 'salary-calculation', 'delete', 'Delete Salary Calculation', 'delete salary-calculation', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(242, 'salary-calculation', 'view', 'View Salary Calculation', 'view salary-calculation', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(243, 'payslip-generation', 'create', 'Create Payslip Generation', 'create payslip-generation', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(244, 'payslip-generation', 'edit', 'Edit Payslip Generation', 'edit payslip-generation', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(245, 'payslip-generation', 'delete', 'Delete Payslip Generation', 'delete payslip-generation', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(246, 'payslip-generation', 'view', 'View Payslip Generation', 'view payslip-generation', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(247, 'payroll-reports', 'create', 'Create Payroll Reports', 'create payroll-reports', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(248, 'payroll-reports', 'edit', 'Edit Payroll Reports', 'edit payroll-reports', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(249, 'payroll-reports', 'delete', 'Delete Payroll Reports', 'delete payroll-reports', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(250, 'payroll-reports', 'view', 'View Payroll Reports', 'view payroll-reports', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(251, 'document-repository', 'create', 'Create Document Repository', 'create document-repository', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(252, 'document-repository', 'edit', 'Edit Document Repository', 'edit document-repository', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(253, 'document-repository', 'delete', 'Delete Document Repository', 'delete document-repository', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(254, 'document-repository', 'view', 'View Document Repository', 'view document-repository', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(255, 'log', 'view', 'View Log', 'view log', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(256, 'backup-restore', 'view', 'View Backup Restore', 'view backup-restore', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(257, 'sales-marketing-report', 'view', 'View Sales Marketing Report', 'view sales-marketing-report', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(258, 'warehouse-report', 'view', 'View Warehouse Report', 'view warehouse-report', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(259, 'production-report', 'view', 'View Production Report', 'view production-report', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(260, 'ticket-management', 'create', 'Create Ticket Management', 'create ticket-management', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(261, 'ticket-management', 'edit', 'Edit Ticket Management', 'edit ticket-management', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(262, 'ticket-management', 'delete', 'Delete Ticket Management', 'delete ticket-management', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(263, 'ticket-management', 'view', 'View Ticket Management', 'view ticket-management', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(264, 'settings', 'edit', 'Edit Settings', 'edit settings', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(265, 'settings', 'view', 'View Settings', 'view settings', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(266, 'dashboard', 'view-sales-order', 'Sales & Order Dashboard', 'view-sales-order dashboard', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(267, 'dashboard', 'view-accounts-financial', 'Accounts & Financial Dashboard', 'view-accounts-financial dashboard', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(268, 'dashboard', 'view-production', 'Production Dashboard', 'view-production dashboard', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43'),
(269, 'dashboard', 'view-maintenance', 'Maintenance Dashboard', 'view-maintenance dashboard', 'web', '2026-04-20 11:16:43', '2026-04-20 11:16:43');

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
  `place_name` varchar(100) NOT NULL,
  `place_type` enum('Residential','Commercial','Project Site') NOT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`id`, `state_id`, `city_id`, `place_name`, `place_type`, `latitude`, `longitude`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'JAIHINDPURAM', 'Residential', NULL, NULL, 'Active', 1, NULL, '2026-04-16 09:36:43', '2026-04-16 09:36:43', NULL),
(2, 4, 5, 'KAZHAKKOOTTAM', 'Residential', NULL, NULL, 'Active', 1, NULL, '2026-04-16 09:37:22', '2026-04-16 09:37:22', NULL),
(3, 1, 1, 'ARAPALAYAM', 'Residential', NULL, NULL, 'Active', 1, NULL, '2026-04-16 09:37:37', '2026-04-16 09:37:37', NULL),
(4, 1, 1, 'MATTUTHAVANI', 'Commercial', NULL, NULL, 'Active', 1, NULL, '2026-04-16 09:37:50', '2026-04-16 09:37:50', NULL),
(5, 1, 2, 'MYLAPORE', 'Commercial', NULL, NULL, 'Active', 1, 1, '2026-04-16 09:44:00', '2026-04-16 09:44:37', NULL),
(6, 1, 2, 'ADYAR', 'Commercial', NULL, NULL, 'Active', 1, 1, '2026-04-16 09:44:19', '2026-04-16 09:44:28', NULL),
(7, 3, 10, 'BHIWANDI', 'Commercial', NULL, NULL, 'Active', 1, NULL, '2026-04-16 10:13:16', '2026-04-16 10:13:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pocket_types`
--

CREATE TABLE `pocket_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pocket_type_name` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pocket_types`
--

INSERT INTO `pocket_types` (`id`, `pocket_type_name`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'CORNER CROSS', 'Active', 1, NULL, NULL, '2026-04-16 09:58:26', '2026-04-16 09:58:26'),
(2, 'V POCKET', 'Active', 1, NULL, NULL, '2026-04-16 09:58:32', '2026-04-16 09:58:32');

-- --------------------------------------------------------

--
-- Table structure for table `process_groups`
--

CREATE TABLE `process_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `process_groups`
--

INSERT INTO `process_groups` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CHECKED FULL SLEEVE', 'Active', 1, NULL, '2026-04-16 09:59:52', '2026-04-16 09:59:52', NULL),
(2, 'CHECKED FULL & HALF SLEEVE', 'Active', 1, NULL, '2026-04-16 10:00:01', '2026-04-16 10:00:01', NULL),
(3, 'CHECKED HALF SLEEVE', 'Active', 1, NULL, '2026-04-16 10:00:08', '2026-04-16 10:00:08', NULL),
(4, 'OTHERS FULL SLEEVE', 'Active', 1, NULL, '2026-04-16 10:00:14', '2026-04-16 10:00:14', NULL),
(5, 'OTHERS HALF SLEEVE', 'Active', 1, NULL, '2026-04-16 10:00:21', '2026-04-16 10:00:21', NULL),
(6, 'OTHERS FULL & HALF SLEEVE', 'Active', 1, NULL, '2026-04-16 10:00:27', '2026-04-16 10:00:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `process_schedules`
--

CREATE TABLE `process_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `production_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stage` varchar(100) NOT NULL,
  `operation_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `planned_qty` int(11) NOT NULL DEFAULT 0,
  `uom` varchar(25) NOT NULL DEFAULT 'PCS',
  `scheduled_to` bigint(20) DEFAULT NULL,
  `service_provider_type` enum('Internal','External') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(150) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `process_schedules`
--

INSERT INTO `process_schedules` (`id`, `job_card_entry_id`, `production_id`, `stage`, `operation_stage_id`, `planned_qty`, `uom`, `scheduled_to`, `service_provider_type`, `start_date`, `end_date`, `due_date`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, NULL, 'CUTTING', 1, 255, 'PCS', 1, NULL, '2026-04-16', NULL, '2026-04-20', 'Completed', 1, NULL, '2026-04-16 12:06:50', '2026-04-20 09:42:44', '2026-04-20 09:42:44'),
(2, 1, NULL, 'STITCHING READY', 2, 255, 'PCS', 2, NULL, '2026-04-20', NULL, '2026-04-23', 'Completed', 1, NULL, '2026-04-16 12:06:50', '2026-04-20 09:42:44', '2026-04-20 09:42:44'),
(3, 2, NULL, 'CUTTING', 1, 210, 'PCS', 1, NULL, '2026-04-17', NULL, '2026-04-21', 'Planned', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(4, 2, NULL, 'STITCHING READY', 2, 210, 'PCS', 2, NULL, '2026-04-21', NULL, '2026-04-24', 'Planned', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(5, 3, NULL, 'CUTTING', 1, 330, 'PCS', 1, NULL, '2026-04-17', NULL, '2026-04-21', 'Planned', 1, NULL, '2026-04-17 06:33:06', '2026-04-17 06:39:34', '2026-04-17 06:39:34'),
(6, 3, NULL, 'STITCHING READY', 2, 330, 'PCS', 2, NULL, '2026-04-21', NULL, '2026-04-24', 'Planned', 1, NULL, '2026-04-17 06:33:06', '2026-04-17 06:39:34', '2026-04-17 06:39:34'),
(7, 3, NULL, 'CUTTING', 1, 330, 'PCS', 1, NULL, '2026-04-17', NULL, '2026-04-21', 'Planned', 1, NULL, '2026-04-17 06:39:34', '2026-04-17 08:14:25', '2026-04-17 08:14:25'),
(8, 3, NULL, 'STITCHING READY', 2, 330, 'PCS', 2, NULL, '2026-04-21', NULL, '2026-04-24', 'Planned', 1, NULL, '2026-04-17 06:39:34', '2026-04-17 08:14:25', '2026-04-17 08:14:25'),
(9, 3, NULL, 'CUTTING', 1, 330, 'PCS', 1, NULL, '2026-04-17', NULL, '2026-04-21', 'Planned', 1, NULL, '2026-04-17 08:14:25', '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(10, 3, NULL, 'STITCHING READY', 2, 330, 'PCS', 2, NULL, '2026-04-21', NULL, '2026-04-24', 'Planned', 1, NULL, '2026-04-17 08:14:25', '2026-04-17 08:55:30', '2026-04-17 08:55:30'),
(11, 3, NULL, 'CUTTING', 1, 330, 'PCS', 1, NULL, '2026-04-17', NULL, '2026-04-21', 'Planned', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(12, 3, NULL, 'STITCHING READY', 2, 330, 'PCS', 2, NULL, '2026-04-21', NULL, '2026-04-24', 'Planned', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(13, 4, NULL, 'CUTTING', 1, 154, 'PCS', 1, NULL, '2026-04-20', NULL, '2026-04-24', 'Planned', 1, NULL, '2026-04-20 04:52:33', '2026-04-20 09:41:55', '2026-04-20 09:41:55'),
(14, 4, NULL, 'STITCHING READY', 2, 154, 'PCS', 2, NULL, '2026-04-24', NULL, '2026-04-27', 'Planned', 1, NULL, '2026-04-20 04:52:33', '2026-04-20 09:41:55', '2026-04-20 09:41:55'),
(15, 4, NULL, 'CUTTING', 1, 154, 'PCS', 1, NULL, '2026-04-20', NULL, '2026-04-24', 'Planned', 1, NULL, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(16, 4, NULL, 'STITCHING READY', 2, 154, 'PCS', 2, NULL, '2026-04-24', NULL, '2026-04-27', 'Planned', 1, NULL, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(17, 1, NULL, 'CUTTING', 1, 255, 'PCS', 1, NULL, '2026-04-16', NULL, '2026-04-20', 'Planned', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL),
(18, 1, NULL, 'STITCHING READY', 2, 255, 'PCS', 2, NULL, '2026-04-20', NULL, '2026-04-23', 'Planned', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `production_movements`
--

CREATE TABLE `production_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_id` bigint(20) UNSIGNED NOT NULL,
  `process_schedule_id` bigint(20) UNSIGNED DEFAULT NULL,
  `operation_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `production_service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `task_id` bigint(20) UNSIGNED DEFAULT NULL,
  `inward_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `outward_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wastage_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_movements`
--

INSERT INTO `production_movements` (`id`, `job_card_id`, `process_schedule_id`, `operation_stage_id`, `production_service_id`, `task_id`, `inward_qty`, `outward_qty`, `wastage_qty`, `remarks`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, 1, 0.00, 255.00, 0.00, 'Automated progress sync', 1, NULL, NULL, '2026-04-17 05:09:27', '2026-04-17 05:09:27'),
(2, 1, 2, 2, NULL, NULL, 255.00, 0.00, 0.00, 'Automated inward from Previous Stage', 1, NULL, NULL, '2026-04-17 05:09:27', '2026-04-17 05:09:27'),
(3, 1, 2, 2, NULL, 2, 0.00, 255.00, 0.00, 'Automated progress sync', 1, NULL, NULL, '2026-04-17 05:11:11', '2026-04-17 05:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `production_receipts`
--

CREATE TABLE `production_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `production_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_card_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_due_date` date DEFAULT NULL,
  `receipt_no` varchar(50) DEFAULT NULL,
  `receipt_date` date NOT NULL,
  `doc_no` varchar(255) DEFAULT NULL,
  `doc_date` date DEFAULT NULL,
  `store_type_id` bigint(20) UNSIGNED NOT NULL,
  `store_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Draft','Posted') NOT NULL DEFAULT 'Draft',
  `remarks` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_receipts`
--

INSERT INTO `production_receipts` (`id`, `production_id`, `job_card_id`, `employee_id`, `order_due_date`, `receipt_no`, `receipt_date`, `doc_no`, `doc_date`, `store_type_id`, `store_location_id`, `status`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, NULL, NULL, 'RCPT-2026-0001', '2026-04-20', 'JC001', '2026-04-16', 3, 3, 'Draft', NULL, 2, NULL, '2026-04-20 10:26:34', '2026-04-20 10:26:34');

-- --------------------------------------------------------

--
-- Table structure for table `production_receipt_items`
--

CREATE TABLE `production_receipt_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `production_receipt_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(100) NOT NULL,
  `art_no` varchar(50) DEFAULT NULL,
  `size` varchar(25) DEFAULT NULL,
  `color_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `size_variant` varchar(255) DEFAULT NULL,
  `uom_id` bigint(20) UNSIGNED DEFAULT NULL,
  `uom_code` varchar(25) DEFAULT NULL,
  `ordered_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `completed_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_already_received` decimal(15,2) NOT NULL DEFAULT 0.00,
  `scan_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `damage_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_to_receive` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_services`
--

CREATE TABLE `production_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `service_code` varchar(255) NOT NULL,
  `operation_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `applies_to` enum('ALL','Full Sleeve','Half Sleeve','Both') NOT NULL DEFAULT 'ALL',
  `base_quantity_source` enum('Total Qty','FS Qty','HS Qty') NOT NULL DEFAULT 'Total Qty',
  `multiplier` decimal(10,2) NOT NULL DEFAULT 1.00,
  `uom` varchar(20) NOT NULL DEFAULT 'PCS',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_services`
--

INSERT INTO `production_services` (`id`, `service_name`, `service_code`, `operation_stage_id`, `status`, `applies_to`, `base_quantity_source`, `multiplier`, `uom`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'FABRIC INSPECTION', 'CUT-FI', 1, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, 1, '2026-04-16 10:01:39', '2026-04-16 10:01:53', NULL),
(2, 'MARKER PLANNING', 'CUT-MP', 1, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-04-16 10:02:15', '2026-04-16 10:02:15', NULL),
(3, 'FABRIC SPREADING', 'CUT-FS', 1, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-04-16 10:02:33', '2026-04-16 10:02:33', NULL),
(4, 'BUNDLING', 'CUT-BD', 1, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-04-16 10:02:50', '2026-04-16 10:02:50', NULL),
(5, 'TRIMMING', 'CUT-TR', 1, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-04-16 10:03:12', '2026-04-16 10:03:12', NULL),
(6, 'COLLAR STITCHING', 'SEW-CL', 2, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-04-16 10:03:32', '2026-04-16 10:03:32', NULL),
(7, 'CUFF STITCHING', 'SEW-CF', 2, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-04-16 10:03:50', '2026-04-16 10:03:50', NULL),
(8, 'SLEEVE ATTACHING', 'SEW-SL', 2, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-04-16 10:04:08', '2026-04-16 10:04:08', NULL),
(9, 'THREAD TRIMMING', 'FIN-TR', 3, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-04-16 10:04:37', '2026-04-16 10:04:37', NULL),
(10, 'FOLDING', 'FIN-FL', 3, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-04-16 10:04:56', '2026-04-16 10:04:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `production_stage_consumables`
--

CREATE TABLE `production_stage_consumables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `production_id` bigint(20) UNSIGNED DEFAULT NULL,
  `production_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stage` varchar(255) NOT NULL,
  `art_no` varchar(255) DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  `fs_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `hs_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `total_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `raw_material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_per_unit` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `planned_qty` decimal(12,4) DEFAULT NULL,
  `actual_qty` decimal(12,4) DEFAULT NULL,
  `uom_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sleeve_type` enum('All','F/S','H/S') NOT NULL DEFAULT 'All',
  `status` enum('Active','Inactive','LOCKED') NOT NULL DEFAULT 'Active',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_stage_consumables`
--

INSERT INTO `production_stage_consumables` (`id`, `job_card_id`, `production_id`, `production_stage_id`, `stage`, `art_no`, `item_type`, `fs_qty`, `hs_qty`, `total_qty`, `raw_material_id`, `quantity_per_unit`, `planned_qty`, `actual_qty`, `uom_id`, `sleeve_type`, `status`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, 2, NULL, 1, 'CUTTING', 'CF-34935', 'Consumable', 6075.000, 1875.000, 7950.000, 1, 0.0000, 210.0000, 7950.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34935', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(10, 2, NULL, 1, 'CUTTING', 'CF-34934', 'Consumable', 6075.000, 1875.000, 7950.000, 1, 0.0000, 210.0000, 7950.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34934', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(11, 2, NULL, 1, 'CUTTING', 'CF-09093', 'Consumable', 6075.000, 1875.000, 7950.000, 1, 0.0000, 210.0000, 7950.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-09093', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(12, 2, NULL, 1, 'CUTTING', 'CF-34346', 'Consumable', 424.000, 108.000, 532.000, 3, 0.0000, 210.0000, 532.0000, 3, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34346', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(13, 2, NULL, 2, 'STITCHING READY', 'CF-34935', 'Consumable', 6075.000, 1875.000, 7950.000, 1, 0.0000, 210.0000, 7950.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34935', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(14, 2, NULL, 2, 'STITCHING READY', 'CF-34934', 'Consumable', 6075.000, 1875.000, 7950.000, 1, 0.0000, 210.0000, 7950.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34934', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(15, 2, NULL, 2, 'STITCHING READY', 'CF-09093', 'Consumable', 6075.000, 1875.000, 7950.000, 1, 0.0000, 210.0000, 7950.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-09093', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(16, 2, NULL, 2, 'STITCHING READY', 'CF-34346', 'Consumable', 424.000, 108.000, 532.000, 3, 0.0000, 210.0000, 532.0000, 3, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34346', 1, NULL, '2026-04-17 06:02:16', '2026-04-17 06:02:16', NULL),
(39, 3, NULL, 1, 'CUTTING', 'CF-03489', 'Consumable', 10800.000, 7500.000, 18300.000, 1, 0.0000, 330.0000, 18300.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-03489', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(40, 3, NULL, 1, 'CUTTING', 'CF-03480', 'Consumable', 10800.000, 7500.000, 18300.000, 1, 0.0000, 330.0000, 18300.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-03480', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(41, 3, NULL, 1, 'CUTTING', 'CF-34934', 'Consumable', 10800.000, 7500.000, 18300.000, 1, 0.0000, 330.0000, 18300.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34934', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(42, 3, NULL, 1, 'CUTTING', 'CF-34937', 'Consumable', 1020.000, 240.000, 1260.000, 3, 0.0000, 330.0000, 1260.0000, 3, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34937', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(43, 3, NULL, 2, 'STITCHING READY', 'CF-03489', 'Consumable', 10800.000, 7500.000, 18300.000, 1, 0.0000, 330.0000, 18300.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-03489', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(44, 3, NULL, 2, 'STITCHING READY', 'CF-03480', 'Consumable', 10800.000, 7500.000, 18300.000, 1, 0.0000, 330.0000, 18300.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-03480', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(45, 3, NULL, 2, 'STITCHING READY', 'CF-34934', 'Consumable', 10800.000, 7500.000, 18300.000, 1, 0.0000, 330.0000, 18300.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34934', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(46, 3, NULL, 2, 'STITCHING READY', 'CF-34937', 'Consumable', 1020.000, 240.000, 1260.000, 3, 0.0000, 330.0000, 1260.0000, 3, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34937', 1, NULL, '2026-04-17 08:55:30', '2026-04-17 08:55:30', NULL),
(51, 4, NULL, 1, 'CUTTING', 'CF-34935', 'Consumable', 2888.000, 3042.000, 5930.000, 1, 0.0000, 154.0000, 5930.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34935', 1, NULL, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(52, 4, NULL, 1, 'CUTTING', 'CF-349301', 'Consumable', 2888.000, 3042.000, 5930.000, 2, 0.0000, 154.0000, 5930.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-349301', 1, NULL, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(53, 4, NULL, 2, 'STITCHING READY', 'CF-34935', 'Consumable', 2888.000, 3042.000, 5930.000, 1, 0.0000, 154.0000, 5930.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34935', 1, NULL, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(54, 4, NULL, 2, 'STITCHING READY', 'CF-349301', 'Consumable', 2888.000, 3042.000, 5930.000, 2, 0.0000, 154.0000, 5930.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-349301', 1, NULL, '2026-04-20 09:41:55', '2026-04-20 09:41:55', NULL),
(55, 1, NULL, 1, 'CUTTING', 'CF-0909', 'Consumable', 6075.000, 4800.000, 10875.000, 1, 0.0000, 255.0000, 10875.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-0909', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL),
(56, 1, NULL, 1, 'CUTTING', 'CF-34343', 'Consumable', 6075.000, 4800.000, 10875.000, 1, 0.0000, 255.0000, 10875.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34343', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL),
(57, 1, NULL, 1, 'CUTTING', 'CF-34344', 'Consumable', 6075.000, 4800.000, 10875.000, 2, 0.0000, 255.0000, 10875.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34344', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL),
(58, 1, NULL, 1, 'CUTTING', 'CF-34345', 'Consumable', 304.000, 216.000, 520.000, 3, 0.0000, 255.0000, 520.0000, 3, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34345', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL),
(59, 1, NULL, 2, 'STITCHING READY', 'CF-0909', 'Consumable', 6075.000, 4800.000, 10875.000, 1, 0.0000, 255.0000, 10875.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-0909', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL),
(60, 1, NULL, 2, 'STITCHING READY', 'CF-34343', 'Consumable', 6075.000, 4800.000, 10875.000, 1, 0.0000, 255.0000, 10875.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34343', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL),
(61, 1, NULL, 2, 'STITCHING READY', 'CF-34344', 'Consumable', 6075.000, 4800.000, 10875.000, 2, 0.0000, 255.0000, 10875.0000, 5, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34344', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL),
(62, 1, NULL, 2, 'STITCHING READY', 'CF-34345', 'Consumable', 304.000, 216.000, 520.000, 3, 0.0000, 255.0000, 520.0000, 3, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-34345', 1, NULL, '2026-04-20 09:42:44', '2026-04-20 09:42:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_commission_agents`
--

CREATE TABLE `purchase_commission_agents` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `email` varchar(128) DEFAULT NULL,
  `mobile_no` varchar(20) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `place_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_line_1` varchar(150) DEFAULT NULL,
  `address_line_2` varchar(150) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `designation` varchar(50) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `contact_email` varchar(128) DEFAULT NULL,
  `pan_no` varchar(10) DEFAULT NULL,
  `gst_no` varchar(15) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_commission_agents`
--

INSERT INTO `purchase_commission_agents` (`id`, `name`, `code`, `email`, `mobile_no`, `status`, `created_by`, `updated_by`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `zipcode`, `contact_person_name`, `designation`, `phone_number`, `contact_email`, `pan_no`, `gst_no`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'BHAGWAN TEXTILE AGENCY', '1001', NULL, '6936953698', 'Active', 1, NULL, 1, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:06:48', '2026-04-16 10:06:48', NULL),
(2, 'SRI MEENAKSHI TEXTILE', '1002', NULL, '6535358745', 'Active', 1, NULL, 1, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:08:18', '2026-04-16 10:08:18', NULL),
(3, 'Bright agencies - Matching CenteR', '1003', NULL, '8520147474', 'Active', 1, NULL, 1, 2, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:08:52', '2026-04-16 10:08:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_invoices`
--

CREATE TABLE `purchase_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(100) NOT NULL,
  `invoice_date` date NOT NULL,
  `purchase_order_id` int(11) NOT NULL DEFAULT 0,
  `purchase_commission_agent_id` int(11) DEFAULT NULL,
  `commission` decimal(5,2) NOT NULL DEFAULT 0.00,
  `commission_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `supplier_id` int(11) NOT NULL,
  `po_reference` varchar(50) DEFAULT NULL,
  `sub_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `taxable_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_state` char(1) NOT NULL DEFAULT '0',
  `igst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `igst_amount` decimal(15,2) DEFAULT NULL,
  `cgst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `cgst_amount` decimal(15,2) DEFAULT NULL,
  `sgst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sgst_amount` decimal(15,2) DEFAULT NULL,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_charges` decimal(15,2) NOT NULL DEFAULT 0.00,
  `round_off_type` enum('Add','Less') NOT NULL DEFAULT 'Add',
  `round_off` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `received_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `invoice_status` enum('Draft','Unpaid/Credit','Paid','Partially Paid') NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `auth_signature` varchar(255) DEFAULT NULL,
  `attachments` varchar(255) DEFAULT NULL,
  `transport` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `lr_no` varchar(255) DEFAULT NULL,
  `lr_date` date DEFAULT NULL,
  `eway_billno` varchar(255) DEFAULT NULL,
  `indent_no` varchar(255) DEFAULT NULL,
  `indent_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_invoices`
--

INSERT INTO `purchase_invoices` (`id`, `invoice_no`, `invoice_date`, `purchase_order_id`, `purchase_commission_agent_id`, `commission`, `commission_amount`, `supplier_id`, `po_reference`, `sub_total`, `discount_percent`, `discount_amount`, `taxable_amount`, `other_state`, `igst_percent`, `igst_amount`, `cgst_percent`, `cgst_amount`, `sgst_percent`, `sgst_amount`, `tax_amount`, `other_charges`, `round_off_type`, `round_off`, `grand_total`, `received_amount`, `due_amount`, `invoice_status`, `created_by`, `updated_by`, `payment_mode`, `transaction_id`, `due_date`, `notes`, `auth_signature`, `attachments`, `transport`, `destination`, `lr_no`, `lr_date`, `eway_billno`, `indent_no`, `indent_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'INV3452', '2026-04-16', 3, 2, 2.00, 30.00, 3, 'PO-0003', 1500.00, 2.00, 30.00, 1440.00, '0', 0.00, 0.00, 9.00, 129.60, 9.00, 129.60, 259.20, 0.00, 'Less', 0.20, 1699.00, 0.00, 1699.00, 'Draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 11:41:29', '2026-04-16 11:41:29', NULL),
(2, 'INV343', '2026-04-16', 2, 2, 2.00, 297.00, 2, 'PO-0002', 14850.00, 2.00, 297.00, 14256.00, '1', 5.00, 712.80, 0.00, 0.00, 0.00, 0.00, 712.80, 0.00, 'Less', 0.20, 14968.60, 0.00, 14968.60, 'Unpaid/Credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 11:41:44', '2026-04-16 11:41:44', NULL),
(3, 'INV987', '2026-04-16', 1, 2, 3.00, 907.20, 3, 'PO-0001', 30240.00, 2.00, 604.80, 28728.00, '0', 0.00, 0.00, 9.00, 2585.52, 9.00, 2585.52, 5171.04, 0.00, 'Less', 0.04, 33899.00, 0.00, 33899.00, 'Unpaid/Credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 11:42:06', '2026-04-16 11:42:06', NULL),
(4, 'INV007', '2026-04-17', 4, 2, 2.00, 270.00, 2, 'PO-0004', 13500.00, 0.00, 0.00, 13230.00, '1', 5.00, 661.50, 0.00, 0.00, 0.00, 0.00, 661.50, 0.00, 'Add', 0.50, 13892.00, 0.00, 13892.00, 'Draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-17 05:27:42', '2026-04-17 05:27:42', NULL),
(5, 'INV0945', '2026-04-17', 5, 2, 2.00, 44.80, 3, 'PO-0005', 2240.00, 0.00, 0.00, 2195.20, '0', 0.00, 0.00, 9.00, 197.57, 9.00, 197.57, 395.14, 0.00, 'Less', 0.34, 2590.00, 0.00, 2590.00, 'Unpaid/Credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-17 05:28:02', '2026-04-17 05:28:02', NULL),
(6, 'INV002', '2026-04-17', 6, 1, 3.00, 432.00, 2, 'PO-0006', 14400.00, 2.00, 288.00, 13680.00, '1', 5.00, 684.00, 0.00, 0.00, 0.00, 0.00, 684.00, 0.00, 'Less', 0.00, 14364.00, 0.00, 14364.00, 'Unpaid/Credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-17 06:04:00', '2026-04-17 06:04:00', NULL),
(7, 'INV003', '2026-04-17', 7, NULL, 0.00, 0.00, 3, 'PO-0007', 1250.00, 4.00, 50.00, 1200.00, '0', 0.00, 0.00, 9.00, 108.00, 9.00, 108.00, 216.00, 0.00, 'Add', 0.00, 1416.00, 0.00, 1416.00, 'Unpaid/Credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-17 06:04:17', '2026-04-17 06:04:17', NULL),
(8, 'INV0093', '2026-04-17', 8, 3, 3.00, 24.00, 3, 'PO-0008', 800.00, 5.00, 40.00, 736.00, '0', 0.00, 0.00, 9.00, 66.24, 9.00, 66.24, 132.48, 0.00, 'Less', 0.48, 868.00, 0.00, 868.00, 'Draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-17 06:41:35', '2026-04-17 06:41:35', NULL),
(9, 'INV0094', '2026-04-17', 9, NULL, 0.00, 0.00, 2, 'PO-0009', 20000.00, 0.00, 0.00, 20000.00, '1', 5.00, 1000.00, 0.00, 0.00, 0.00, 0.00, 1000.00, 0.00, 'Add', 0.00, 21000.00, 0.00, 21000.00, 'Partially Paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-17 06:41:52', '2026-04-17 06:41:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_invoice_charges`
--

CREATE TABLE `purchase_invoice_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `charge_name` varchar(255) DEFAULT NULL,
  `charge_id` bigint(20) UNSIGNED NOT NULL,
  `charge_amount` decimal(15,2) NOT NULL,
  `tax_type` varchar(255) NOT NULL DEFAULT 'Post-GST',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_invoice_items`
--

CREATE TABLE `purchase_invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint(20) UNSIGNED NOT NULL,
  `raw_material_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hsn_code` varchar(10) DEFAULT NULL,
  `fabric_width_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `uom_id` bigint(20) UNSIGNED NOT NULL,
  `rate` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `qty_ordered` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_received` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_invoiced` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_invoice_items`
--

INSERT INTO `purchase_invoice_items` (`id`, `purchase_invoice_id`, `purchase_order_item_id`, `raw_material_id`, `brand_id`, `hsn_code`, `fabric_width_id`, `quantity`, `uom_id`, `rate`, `amount`, `qty_ordered`, `qty_received`, `qty_invoiced`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 4, 3, 1, NULL, NULL, 150.00, 3, 10.00, 1500.00, 150.00, 150.00, 150.00, '2026-04-16 11:41:29', '2026-04-16 11:41:29', NULL),
(2, 2, 3, 1, 2, NULL, 1, 150.00, 5, 99.00, 14850.00, 150.00, 150.00, 150.00, '2026-04-16 11:41:44', '2026-04-16 11:41:44', NULL),
(3, 3, 1, 1, 1, NULL, 1, 150.00, 5, 96.00, 14400.00, 150.00, 150.00, 150.00, '2026-04-16 11:42:06', '2026-04-16 11:42:06', NULL),
(4, 3, 2, 2, 3, NULL, 1, 160.00, 5, 99.00, 15840.00, 160.00, 160.00, 160.00, '2026-04-16 11:42:06', '2026-04-16 11:42:06', NULL),
(5, 4, 9, 1, 1, NULL, 1, 150.00, 5, 90.00, 13500.00, 150.00, 150.00, 150.00, '2026-04-17 05:27:42', '2026-04-17 05:27:42', NULL),
(6, 5, 10, 3, 1, NULL, NULL, 280.00, 3, 8.00, 2240.00, 280.00, 280.00, 280.00, '2026-04-17 05:28:02', '2026-04-17 05:28:02', NULL),
(7, 6, 11, 1, 2, NULL, 1, 150.00, 5, 96.00, 14400.00, 150.00, 150.00, 150.00, '2026-04-17 06:04:00', '2026-04-17 06:04:00', NULL),
(8, 7, 12, 3, 1, NULL, NULL, 250.00, 3, 5.00, 1250.00, 250.00, 250.00, 250.00, '2026-04-17 06:04:17', '2026-04-17 06:04:17', NULL),
(9, 8, 13, 3, 3, NULL, NULL, 160.00, 3, 5.00, 800.00, 160.00, 160.00, 160.00, '2026-04-17 06:41:35', '2026-04-17 06:41:35', NULL),
(10, 9, 14, 2, 1, NULL, 1, 200.00, 5, 100.00, 20000.00, 200.00, 200.00, 200.00, '2026-04-17 06:41:52', '2026-04-17 06:41:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_invoice_payments`
--

CREATE TABLE `purchase_invoice_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_mode` varchar(100) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_executive_id` int(11) DEFAULT NULL,
  `po_number` varchar(100) NOT NULL,
  `po_date` date NOT NULL,
  `purchase_commission_agent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `commission` decimal(5,2) NOT NULL DEFAULT 0.00,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `reference_date` date DEFAULT NULL,
  `due_date` date NOT NULL,
  `store_type_id` bigint(20) UNSIGNED NOT NULL,
  `payment_terms` text DEFAULT NULL,
  `status` enum('Draft','Approved','Dispatched','Received') NOT NULL DEFAULT 'Draft',
  `is_self_closed` tinyint(1) NOT NULL DEFAULT 0,
  `additional_attachments` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `total_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sub_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `taxable_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_state` tinyint(1) NOT NULL DEFAULT 0,
  `igst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `cgst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sgst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `round_off_type` enum('Add','Less') DEFAULT NULL,
  `round_off` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `purchase_executive_id`, `po_number`, `po_date`, `purchase_commission_agent_id`, `commission`, `supplier_id`, `reference_no`, `reference_date`, `due_date`, `store_type_id`, `payment_terms`, `status`, `is_self_closed`, `additional_attachments`, `created_by`, `updated_by`, `total_qty`, `sub_total`, `discount_percent`, `discount_amount`, `taxable_amount`, `other_state`, `igst_percent`, `cgst_percent`, `sgst_percent`, `tax_amount`, `round_off_type`, `round_off`, `total_amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'PO-0001', '2026-04-16', 2, 3.00, 3, '6525', '2026-04-16', '2026-05-09', 3, NULL, 'Received', 0, '[]', NULL, NULL, 310.00, 30240.00, 2.00, 604.80, 28728.00, 0, 0.00, 9.00, 9.00, 5171.04, 'Less', 0.04, 33899.00, '2026-04-16 11:22:10', '2026-04-16 11:42:40', NULL),
(2, NULL, 'PO-0002', '2026-04-16', 2, 2.00, 2, '5820', '2026-04-16', '2026-05-09', 1, NULL, 'Received', 0, '[]', NULL, NULL, 150.00, 14850.00, 2.00, 297.00, 14256.00, 1, 5.00, 0.00, 0.00, 712.80, 'Less', 0.20, 14968.60, '2026-04-16 11:36:29', '2026-04-16 12:01:07', NULL),
(3, NULL, 'PO-0003', '2026-04-16', 2, 2.00, 3, '4242', '2026-04-16', '2026-05-09', 2, NULL, 'Received', 0, '[]', NULL, NULL, 150.00, 1500.00, 2.00, 30.00, 1440.00, 0, 0.00, 9.00, 9.00, 259.20, 'Less', 0.20, 1699.00, '2026-04-16 11:38:53', '2026-04-16 12:00:38', NULL),
(4, NULL, 'PO-0004', '2026-04-16', 2, 2.00, 2, '233', '2026-04-17', '2026-05-08', 1, NULL, 'Received', 0, '[]', NULL, NULL, 150.00, 13500.00, 0.00, 0.00, 13230.00, 1, 5.00, 0.00, 0.00, 661.50, 'Add', 0.50, 13892.00, '2026-04-16 11:47:56', '2026-04-17 05:33:16', NULL),
(5, NULL, 'PO-0005', '2026-04-17', 2, 2.00, 3, '1010', '2026-04-17', '2026-05-09', 2, NULL, 'Received', 0, '[]', NULL, NULL, 280.00, 2240.00, 0.00, 0.00, 2195.20, 0, 0.00, 9.00, 9.00, 395.14, 'Less', 0.34, 2590.00, '2026-04-17 05:27:07', '2026-04-17 05:33:39', NULL),
(6, NULL, 'PO-0006', '2026-04-17', 1, 3.00, 2, '34343', '2026-04-17', '2026-05-09', 1, NULL, 'Received', 0, '[]', NULL, NULL, 150.00, 14400.00, 2.00, 288.00, 13680.00, 1, 5.00, 0.00, 0.00, 684.00, 'Add', 0.00, 14364.00, '2026-04-17 06:02:58', '2026-04-17 06:04:55', NULL),
(7, NULL, 'PO-0007', '2026-04-17', NULL, 0.00, 3, '3434', '2026-04-17', '2026-05-09', 2, NULL, 'Received', 0, '[]', NULL, NULL, 250.00, 1250.00, 4.00, 50.00, 1200.00, 0, 0.00, 9.00, 9.00, 216.00, 'Add', 0.00, 1416.00, '2026-04-17 06:03:37', '2026-04-17 06:05:17', NULL),
(8, NULL, 'PO-0008', '2026-04-17', 3, 3.00, 3, '6767', '2026-04-17', '2026-05-09', 2, NULL, 'Received', 0, '[]', NULL, NULL, 160.00, 800.00, 5.00, 40.00, 736.00, 0, 0.00, 9.00, 9.00, 132.48, 'Less', 0.48, 868.00, '2026-04-17 06:40:33', '2026-04-17 06:42:13', NULL),
(9, NULL, 'PO-0009', '2026-04-17', NULL, 0.00, 2, '67767', '2026-04-17', '2026-05-09', 1, NULL, 'Received', 0, '[]', NULL, NULL, 200.00, 20000.00, 0.00, 0.00, 20000.00, 1, 5.00, 0.00, 0.00, 1000.00, 'Add', 0.00, 21000.00, '2026-04-17 06:41:13', '2026-04-17 06:42:40', NULL),
(10, NULL, 'PO-0010', '2026-04-20', 1, 3.00, 5, '5200', '2026-04-20', '2026-05-09', 3, NULL, 'Approved', 0, '[]', NULL, NULL, 150.00, 22500.00, 0.00, 0.00, 21825.00, 0, 0.00, 2.50, 2.50, 1091.25, 'Add', 0.00, 22916.25, '2026-04-20 08:57:57', '2026-04-20 09:03:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `store_category_id` bigint(20) UNSIGNED NOT NULL,
  `raw_material_id` bigint(20) UNSIGNED NOT NULL,
  `uom_id` bigint(20) UNSIGNED NOT NULL,
  `color_id` bigint(20) UNSIGNED DEFAULT NULL,
  `style_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fabric_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fabric_width_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `supplier_design_name` varchar(50) DEFAULT NULL,
  `rate` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `attached_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `store_category_id`, `raw_material_id`, `uom_id`, `color_id`, `style_id`, `brand_id`, `fabric_type_id`, `fabric_width_id`, `quantity`, `supplier_design_name`, `rate`, `amount`, `remarks`, `attached_file`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 5, 1, 1, 1, 1, 1, 150.00, 'FAB_COTTON', 96.00, 14400.00, NULL, NULL, '2026-04-16 11:22:10', '2026-04-16 11:22:10', NULL),
(2, 1, 1, 2, 5, 2, 2, 3, 3, 1, 160.00, 'DENI_COTTON', 99.00, 15840.00, NULL, NULL, '2026-04-16 11:22:10', '2026-04-16 11:22:10', NULL),
(3, 2, 1, 1, 5, NULL, 3, 2, 2, 1, 150.00, 'FAB', 99.00, 14850.00, NULL, NULL, '2026-04-16 11:36:29', '2026-04-16 11:36:29', NULL),
(4, 3, 2, 3, 3, NULL, NULL, 1, NULL, NULL, 150.00, NULL, 10.00, 1500.00, NULL, NULL, '2026-04-16 11:38:53', '2026-04-16 11:38:53', NULL),
(9, 4, 1, 1, 5, 1, 1, 1, 1, 1, 150.00, 'DENIM', 90.00, 13500.00, NULL, '1776340076_0_fabric_img.jpg', '2026-04-16 11:58:38', '2026-04-16 11:58:38', NULL),
(10, 5, 2, 3, 3, NULL, NULL, 1, NULL, NULL, 280.00, NULL, 8.00, 2240.00, NULL, NULL, '2026-04-17 05:27:07', '2026-04-17 05:27:07', NULL),
(11, 6, 1, 1, 5, NULL, 1, 2, 1, 1, 150.00, NULL, 96.00, 14400.00, NULL, NULL, '2026-04-17 06:02:58', '2026-04-17 06:02:58', NULL),
(12, 7, 2, 3, 3, NULL, NULL, 1, NULL, NULL, 250.00, NULL, 5.00, 1250.00, NULL, NULL, '2026-04-17 06:03:37', '2026-04-17 06:03:37', NULL),
(13, 8, 2, 3, 3, NULL, NULL, 3, NULL, NULL, 160.00, NULL, 5.00, 800.00, NULL, NULL, '2026-04-17 06:40:33', '2026-04-17 06:40:33', NULL),
(14, 9, 1, 2, 5, 2, 1, 1, 1, 1, 200.00, 'FAB', 100.00, 20000.00, NULL, NULL, '2026-04-17 06:41:13', '2026-04-17 06:41:13', NULL),
(15, 10, 1, 2, 5, NULL, 1, 1, 2, 1, 150.00, 'POLY FABRIO', 150.00, 22500.00, NULL, NULL, '2026-04-20 08:57:57', '2026-04-20 08:57:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_category_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `supplier_design_name` varchar(150) DEFAULT NULL,
  `size_width` varchar(100) DEFAULT NULL,
  `uom_id` bigint(20) UNSIGNED NOT NULL,
  `material_type` varchar(100) DEFAULT NULL,
  `reference_image` varchar(255) DEFAULT NULL,
  `specification` varchar(255) DEFAULT NULL,
  `min_stock` int(11) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `raw_materials`
--

INSERT INTO `raw_materials` (`id`, `store_category_id`, `code`, `name`, `supplier_design_name`, `size_width`, `uom_id`, `material_type`, `reference_image`, `specification`, `min_stock`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, '1001', 'COTTON FABRIC', NULL, NULL, 5, NULL, NULL, NULL, 0, 'Active', 1, NULL, NULL, '2026-04-16 10:28:09', '2026-04-16 10:28:09'),
(2, 1, '1002', 'DENIM FABRIC', NULL, NULL, 5, NULL, NULL, NULL, 0, 'Active', 1, NULL, NULL, '2026-04-16 10:30:43', '2026-04-16 10:30:43'),
(3, 2, '1003', 'BUTTONS', NULL, NULL, 3, NULL, NULL, NULL, 0, 'Active', 1, NULL, NULL, '2026-04-16 10:32:09', '2026-04-16 10:32:09'),
(4, 2, '1004', 'COTTON THREAD', NULL, NULL, 5, NULL, NULL, NULL, 0, 'Active', 1, NULL, NULL, '2026-04-16 10:40:13', '2026-04-16 10:40:13'),
(5, 2, 'AS-01', '28 MM N.PATTI CANVAS', NULL, NULL, 3, NULL, NULL, NULL, 0, 'Active', 1, NULL, NULL, '2026-04-16 10:40:49', '2026-04-16 10:40:49');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_by`, `updated_by`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Manager', 'web', NULL, NULL, 'Active', '2026-04-16 08:56:57', '2026-04-16 08:56:57', NULL),
(2, 'Supervisior', 'web', NULL, NULL, 'Active', '2026-04-20 05:23:17', '2026-04-20 05:23:17', NULL);

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
(190, 1, 1, NULL, NULL),
(191, 2, 1, NULL, NULL),
(192, 4, 1, NULL, NULL),
(193, 3, 1, NULL, NULL),
(194, 5, 1, NULL, NULL),
(195, 6, 1, NULL, NULL),
(196, 8, 1, NULL, NULL),
(197, 7, 1, NULL, NULL),
(198, 14, 1, NULL, NULL),
(199, 17, 1, NULL, NULL),
(200, 18, 1, NULL, NULL),
(201, 21, 1, NULL, NULL),
(202, 22, 1, NULL, NULL),
(203, 25, 1, NULL, NULL),
(204, 170, 1, NULL, NULL),
(205, 171, 1, NULL, NULL),
(206, 172, 1, NULL, NULL),
(207, 174, 1, NULL, NULL),
(208, 189, 1, NULL, NULL),
(209, 190, 1, NULL, NULL),
(210, 191, 1, NULL, NULL),
(211, 193, 1, NULL, NULL),
(212, 194, 1, NULL, NULL),
(213, 195, 1, NULL, NULL),
(1148, 5, 2, NULL, NULL),
(1149, 6, 2, NULL, NULL),
(1150, 8, 2, NULL, NULL),
(1151, 7, 2, NULL, NULL),
(1152, 190, 2, NULL, NULL),
(1153, 191, 2, NULL, NULL),
(1154, 192, 2, NULL, NULL),
(1155, 193, 2, NULL, NULL),
(1156, 194, 2, NULL, NULL),
(1157, 196, 2, NULL, NULL),
(1158, 197, 2, NULL, NULL),
(1159, 198, 2, NULL, NULL),
(1160, 200, 2, NULL, NULL),
(1161, 201, 2, NULL, NULL),
(1162, 202, 2, NULL, NULL),
(1163, 203, 2, NULL, NULL),
(1164, 214, 2, NULL, NULL),
(1165, 215, 2, NULL, NULL),
(1166, 217, 2, NULL, NULL),
(1167, 216, 2, NULL, NULL),
(1168, 218, 2, NULL, NULL),
(1169, 219, 2, NULL, NULL),
(1170, 221, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_agents`
--

CREATE TABLE `sales_agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agent_type` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `mobile_no` varchar(15) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `place_id` bigint(20) UNSIGNED DEFAULT NULL,
  `zone_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_line_1` varchar(150) NOT NULL,
  `address_line_2` varchar(150) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `contact_phone_number` varchar(20) DEFAULT NULL,
  `contact_email` varchar(128) DEFAULT NULL,
  `pan_no` varchar(10) DEFAULT NULL,
  `gst_no` varchar(15) DEFAULT NULL,
  `commission_value` decimal(10,2) DEFAULT NULL,
  `sales_target` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_agents`
--

INSERT INTO `sales_agents` (`id`, `agent_type`, `name`, `code`, `email`, `password`, `remember_token`, `mobile_no`, `status`, `created_by`, `updated_by`, `state_id`, `city_id`, `place_id`, `zone_id`, `address_line_1`, `address_line_2`, `zip_code`, `contact_person_name`, `designation`, `contact_phone_number`, `contact_email`, `pan_no`, `gst_no`, `commission_value`, `sales_target`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Export Agent', 'JAY', '1000', 'jay34@gmail.com', NULL, NULL, '6985968596', 'Active', 1, NULL, 1, 1, 1, 1, '25, Alavai Nagar,', NULL, '625011', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:25:56', '2026-04-16 10:25:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoices`
--

CREATE TABLE `sales_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inv_no` varchar(100) NOT NULL,
  `inv_date` date NOT NULL,
  `so_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_address` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `invoice_status` varchar(50) DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `extra_input` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `signature_file` varchar(255) DEFAULT NULL,
  `attachment_file` varchar(255) DEFAULT NULL,
  `show_fields` text DEFAULT NULL,
  `sub_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_percent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_state` tinyint(1) NOT NULL DEFAULT 0,
  `igst` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cgst` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sgst` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `igst_percent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cgst_percent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sgst_percent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_charges` decimal(10,2) NOT NULL DEFAULT 0.00,
  `round_off_type` varchar(10) NOT NULL DEFAULT 'Add',
  `round_off` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `received_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice_items`
--

CREATE TABLE `sales_invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sales_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `uom_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `mrp` decimal(15,2) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `hsn_sac` varchar(50) DEFAULT NULL,
  `art_no` varchar(100) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `sleeve_type` varchar(50) DEFAULT NULL,
  `stock_entry_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `so_no` varchar(50) NOT NULL,
  `so_date` date NOT NULL,
  `request_date` date DEFAULT NULL,
  `order_type` varchar(50) NOT NULL DEFAULT 'Regular',
  `season_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_po_ref` varchar(50) DEFAULT NULL,
  `store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `agent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shipping_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transport_mode_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dispatch_from_id` bigint(20) UNSIGNED DEFAULT NULL,
  `zone_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `transporter_name` varchar(255) DEFAULT NULL,
  `freight_type` enum('Paid','To Pay') DEFAULT NULL,
  `freight_amount` decimal(10,2) DEFAULT 0.00,
  `transport_gst_no` varchar(50) DEFAULT NULL,
  `dispatch_through` varchar(255) DEFAULT NULL,
  `status` enum('Draft','Approved','Pending','In Production','Dispatched','Cancelled') NOT NULL DEFAULT 'Draft',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `total_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sub_total_qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `commission_percent` decimal(5,2) DEFAULT NULL,
  `commission_amount` decimal(15,2) DEFAULT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `apply_box_discount` tinyint(1) NOT NULL DEFAULT 0,
  `taxable_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_state` tinyint(1) NOT NULL DEFAULT 0,
  `igst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `cgst_percent` decimal(5,2) NOT NULL DEFAULT 18.00,
  `sgst_percent` decimal(5,2) NOT NULL DEFAULT 9.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `round_off_type` varchar(50) NOT NULL DEFAULT 'Add',
  `round_off` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `internal_remarks` varchar(255) DEFAULT NULL,
  `terms_conditions` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_order_id` bigint(20) UNSIGNED NOT NULL,
  `brand_cat_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_entry_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `color_id` bigint(20) UNSIGNED DEFAULT NULL,
  `art_no` varchar(50) DEFAULT NULL,
  `uom_id` bigint(20) UNSIGNED DEFAULT NULL,
  `size_id` varchar(50) DEFAULT NULL,
  `qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `mrp` decimal(15,2) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sleeve` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sleeve`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE `seasons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `season_code` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seasons`
--

INSERT INTO `seasons` (`id`, `name`, `season_code`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Diwali', 'DIW', 'Active', 1, 1, '2026-02-26 08:41:29', '2026-03-19 13:21:40', NULL),
(2, 'Pongal', 'PONG', 'Active', 1, NULL, '2026-03-19 13:21:53', '2026-03-19 13:21:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_providers`
--

CREATE TABLE `service_providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `operation_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `is_plant` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Plant, 0 = Regular Service Provider',
  `email` varchar(128) DEFAULT NULL,
  `mobile_no` varchar(20) NOT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `service_rate` enum('Per Agent','Job Type') NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `place_id` bigint(20) UNSIGNED NOT NULL,
  `address_line_1` varchar(150) NOT NULL,
  `address_line_2` varchar(150) DEFAULT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `contact_email` varchar(128) DEFAULT NULL,
  `pan_no` varchar(10) DEFAULT NULL,
  `gst_no` varchar(15) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_acc_no` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `payment_terms` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_providers`
--

INSERT INTO `service_providers` (`id`, `operation_stage_id`, `name`, `code`, `is_plant`, `email`, `mobile_no`, `zip_code`, `website_url`, `service_rate`, `status`, `created_by`, `updated_by`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `contact_person_name`, `designation`, `phone_number`, `contact_email`, `pan_no`, `gst_no`, `remarks`, `bank_name`, `bank_acc_no`, `ifsc_code`, `payment_terms`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Nachias Fashion Private Limited', 'NFPL', 1, 'nachias@gmail.com', '8520369741', '625016', NULL, 'Job Type', 'Active', 1, NULL, 1, 1, 3, '272/2, Somu Nagar, Sringeri Nagar,', 'By Pass Road', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:20:36', '2026-04-16 10:20:36', NULL),
(2, 2, 'Samayanallur Unit', 'SMLR', 1, NULL, '9666520321', '625011', NULL, 'Job Type', 'Active', 1, NULL, 1, 2, 6, '23, Block Side Road,', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:22:57', '2026-04-16 10:22:57', NULL),
(3, 3, 'Kalavasal', 'KVSL', 1, NULL, '6565654102', '625011', NULL, 'Per Agent', 'Active', 1, NULL, 1, 1, 3, '90, Bypass Road,', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:23:51', '2026-04-16 10:23:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `email` varchar(128) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `phone_number` varchar(15) NOT NULL,
  `toll_free_no` text DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address` text NOT NULL,
  `cgst` int(11) NOT NULL DEFAULT 0,
  `sgst` int(11) NOT NULL DEFAULT 0,
  `igst` int(11) NOT NULL DEFAULT 0,
  `pan_no` varchar(10) DEFAULT NULL,
  `gst_no` varchar(15) DEFAULT NULL,
  `cin_no` varchar(21) DEFAULT NULL,
  `working_days` varchar(100) DEFAULT NULL,
  `opening_time` varchar(100) DEFAULT NULL,
  `closing_time` varchar(100) DEFAULT NULL,
  `po_prefix` varchar(15) DEFAULT NULL,
  `purchase_invoice_prefix` varchar(15) DEFAULT NULL,
  `so_prefix` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `company_name`, `email`, `logo`, `phone_number`, `toll_free_no`, `state_id`, `city_id`, `address`, `cgst`, `sgst`, `igst`, `pan_no`, `gst_no`, `cin_no`, `working_days`, `opening_time`, `closing_time`, `po_prefix`, `purchase_invoice_prefix`, `so_prefix`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Nachias Fashion Private Limited', 'srinachias@yahoo.in,sales@nachias.com', NULL, '8489938071', '8489938071,8489938073', 1, 1, '272/2, Somu Nagar, Siringeri Nagar\r\n(Sarathambal Kovil Backside),\r\nByepass Road, Madurai - 625016', 6, 6, 12, NULL, '33AADCN9342A1ZU', NULL, 'Monday - Friday', NULL, NULL, 'PO-', 'INV-', 'SO-', '2026-02-20 08:49:21', '2026-04-01 04:56:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shift_name` varchar(50) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `shift_name`, `start_time`, `end_time`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'I', '09:00:00', '19:00:00', 'Active', 1, NULL, '2026-02-26 08:47:01', '2026-02-26 08:47:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_methods`
--

CREATE TABLE `shipping_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_methods`
--

INSERT INTO `shipping_methods` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'DTDC', 'Active', 1, NULL, '2026-04-16 09:54:05', '2026-04-16 09:54:05', NULL),
(2, 'BLUEDART', 'Active', 1, NULL, '2026-04-16 09:54:18', '2026-04-16 09:54:18', NULL),
(3, 'DHL EXPRESS', 'Active', 1, NULL, '2026-04-16 09:54:30', '2026-04-16 09:54:30', NULL);

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
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `size_ratios`
--

INSERT INTO `size_ratios` (`id`, `size`, `ratio`, `status`, `deleted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, '38,40,42,44', '5,6,5,6', 'Active', NULL, 1, NULL, '2026-04-16 09:48:47', '2026-04-16 09:48:47'),
(2, '40', '2', 'Active', NULL, 2, NULL, '2026-04-20 06:32:15', '2026-04-20 06:32:15');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_code` varchar(10) NOT NULL,
  `state_name` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `state_code`, `state_name`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, '33', 'TAMIL NADU', 'Active', '2026-04-16 09:31:31', '2026-04-16 09:31:31', NULL, 1, NULL),
(2, '37', 'ANDHRA PRADESH', 'Active', '2026-04-16 09:31:49', '2026-04-16 09:31:49', NULL, 1, NULL),
(3, '27', 'MAHARASHTRA', 'Active', '2026-04-16 09:32:03', '2026-04-16 09:32:03', NULL, 1, NULL),
(4, '32', 'KERALA', 'Active', '2026-04-16 09:32:18', '2026-04-16 09:32:18', NULL, 1, NULL),
(5, '29', 'KARNATAKA', 'Active', '2026-04-16 09:33:17', '2026-04-16 09:33:17', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_consumable_issues`
--

CREATE TABLE `stock_consumable_issues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `issue_no` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `issue_type` enum('Consumable Issue','Sales Return','Stock Adjustment','Consumable Adjustment','Rework','Material Return') DEFAULT 'Consumable Issue',
  `production_stage` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('Draft','Posted') NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_consumable_issue_items`
--

CREATE TABLE `stock_consumable_issue_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_consumable_issue_id` bigint(20) UNSIGNED NOT NULL,
  `raw_material_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_entry_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `qty_issued` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_returned` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_consumption` decimal(15,2) NOT NULL DEFAULT 0.00,
  `return_reason` varchar(255) DEFAULT NULL,
  `uom_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_entries`
--

CREATE TABLE `stock_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_entry_no` varchar(255) NOT NULL,
  `price` decimal(15,2) DEFAULT 0.00,
  `stock_date` date NOT NULL,
  `entry_type` varchar(255) DEFAULT NULL,
  `grn_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_store_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_store_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `reference_document` varchar(255) DEFAULT NULL,
  `status` enum('Draft','Posted','Cancelled') NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_entries`
--

INSERT INTO `stock_entries` (`id`, `stock_entry_no`, `price`, `stock_date`, `entry_type`, `grn_entry_id`, `from_store_location_id`, `to_store_location_id`, `remarks`, `reference_document`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SE00001', 0.00, '2026-04-16', 'Raw Material', 1, NULL, NULL, NULL, NULL, 'Draft', 1, NULL, '2026-04-16 12:01:16', '2026-04-16 12:01:16', NULL),
(2, 'SE00002', 0.00, '2026-04-16', 'Raw Material', 2, NULL, NULL, NULL, NULL, 'Draft', 1, NULL, '2026-04-16 12:01:24', '2026-04-16 12:01:24', NULL),
(3, 'SE00003', 0.00, '2026-04-16', 'Raw Material', 3, NULL, NULL, NULL, NULL, 'Draft', 1, NULL, '2026-04-16 12:01:31', '2026-04-16 12:01:31', NULL),
(4, 'SE00004', 0.00, '2026-04-17', 'Raw Material', 4, NULL, NULL, NULL, NULL, 'Draft', 1, NULL, '2026-04-17 05:33:48', '2026-04-17 05:33:48', NULL),
(5, 'SE00005', 0.00, '2026-04-17', 'Raw Material', 5, NULL, NULL, NULL, NULL, 'Draft', 1, NULL, '2026-04-17 05:33:54', '2026-04-17 05:33:54', NULL),
(6, 'SE00006', 0.00, '2026-04-17', 'Raw Material', 7, NULL, NULL, NULL, NULL, 'Draft', 1, NULL, '2026-04-17 06:05:24', '2026-04-17 06:05:24', NULL),
(7, 'SE00007', 0.00, '2026-04-17', 'Raw Material', 6, NULL, NULL, NULL, NULL, 'Draft', 1, NULL, '2026-04-17 06:05:30', '2026-04-17 06:05:30', NULL),
(8, 'SE00008', 0.00, '2026-04-17', 'Raw Material', 9, NULL, NULL, NULL, NULL, 'Draft', 1, NULL, '2026-04-17 06:42:49', '2026-04-17 06:42:49', NULL),
(9, 'SE00009', 0.00, '2026-04-17', 'Raw Material', 8, NULL, NULL, NULL, NULL, 'Draft', 1, NULL, '2026-04-17 06:42:54', '2026-04-17 06:42:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_entry_adjustments`
--

CREATE TABLE `stock_entry_adjustments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `adjustment_no` varchar(255) NOT NULL,
  `stock_entry_item_id` bigint(20) UNSIGNED NOT NULL,
  `raw_material_id` bigint(20) UNSIGNED NOT NULL,
  `qty` decimal(15,2) NOT NULL,
  `previous_stock` decimal(15,2) NOT NULL,
  `new_stock` decimal(15,2) NOT NULL,
  `approved_by` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Posted',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_entry_adjustments`
--

INSERT INTO `stock_entry_adjustments` (`id`, `adjustment_no`, `stock_entry_item_id`, `raw_material_id`, `qty`, `previous_stock`, `new_stock`, `approved_by`, `reason`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ADJ-SE-20260416-0001', 3, 3, 175.00, 50.00, 225.00, 'Admin', 'test', 'Posted', 1, NULL, NULL, '2026-04-16 12:05:36', '2026-04-16 12:05:36'),
(2, 'ADJ-SE-20260416-0002', 3, 3, 1575.00, 225.00, 1800.00, 'admin', 'test', 'Posted', 1, NULL, NULL, '2026-04-16 12:06:05', '2026-04-16 12:06:05'),
(3, 'ADJ-SE-20260416-0003', 5, 1, 49.44, 50.00, 99.44, 'test', 'test', 'Posted', 1, NULL, NULL, '2026-04-16 12:06:23', '2026-04-16 12:06:23'),
(4, 'ADJ-SE-20260417-0004', 7, 1, 59.33, 50.00, 109.33, 'Admin', 'Need', 'Posted', 1, NULL, NULL, '2026-04-17 05:37:19', '2026-04-17 05:37:19'),
(5, 'ADJ-SE-20260417-0005', 6, 1, 9.33, 100.00, 109.33, 'Admin', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 05:37:57', '2026-04-17 05:37:57'),
(6, 'ADJ-SE-20260417-0006', 8, 1, 9.33, 100.00, 109.33, 'Admin', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 05:38:21', '2026-04-17 05:38:21'),
(7, 'ADJ-SE-20260417-0007', 4, 3, 1598.00, 100.00, 1698.00, 'Admin', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 05:38:46', '2026-04-17 05:38:46'),
(8, 'ADJ-SE-20260417-0008', 7, 1, 4.05, 109.33, 113.38, 'Admin', 'Need', 'Posted', 1, NULL, NULL, '2026-04-17 06:01:49', '2026-04-17 06:01:49'),
(9, 'ADJ-SE-20260417-0009', 8, 1, 4.04, 109.33, 113.37, 'Admin', 'Need', 'Posted', 1, NULL, NULL, '2026-04-17 06:01:49', '2026-04-17 06:01:49'),
(10, 'ADJ-SE-20260417-0010', 6, 1, 4.04, 109.33, 113.37, 'Admin', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 06:02:12', '2026-04-17 06:02:12'),
(11, 'ADJ-SE-20260417-0011', 11, 1, 38.85, 50.00, 88.85, 'Admin', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 06:10:54', '2026-04-17 06:10:54'),
(12, 'ADJ-SE-20260417-0012', 10, 3, 670.00, 250.00, 920.00, 'tete', 'teste', 'Posted', 1, NULL, NULL, '2026-04-17 06:11:14', '2026-04-17 06:11:14'),
(13, 'ADJ-SE-20260417-0013', 11, 1, 82.43, 88.85, 171.28, 'Admin', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 06:31:57', '2026-04-17 06:31:57'),
(14, 'ADJ-SE-20260417-0014', 11, 1, 71.28, 171.28, 242.56, 'test', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 06:32:19', '2026-04-17 06:32:19'),
(15, 'ADJ-SE-20260417-0015', 12, 1, 71.28, 100.00, 171.28, 'test', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 06:32:42', '2026-04-17 06:32:42'),
(16, 'ADJ-SE-20260417-0016', 7, 1, 57.90, 113.38, 171.28, 'test', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 06:33:02', '2026-04-17 06:33:02'),
(17, 'ADJ-SE-20260417-0017', 10, 3, 2080.00, 920.00, 3000.00, 'tet', 'tes', 'Posted', 1, NULL, NULL, '2026-04-17 06:34:19', '2026-04-17 06:34:19'),
(18, 'ADJ-SE-20260417-0018', 13, 2, 20.68, 100.00, 120.68, 'test', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 07:06:51', '2026-04-17 07:06:51'),
(19, 'ADJ-SE-20260417-0019', 14, 2, 20.68, 100.00, 120.68, 'test', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 07:07:06', '2026-04-17 07:07:06'),
(20, 'ADJ-SE-20260417-0020', 15, 3, 1032.00, 160.00, 1192.00, 'test', 'test', 'Posted', 1, NULL, NULL, '2026-04-17 07:07:27', '2026-04-17 07:07:27');

-- --------------------------------------------------------

--
-- Table structure for table `stock_entry_items`
--

CREATE TABLE `stock_entry_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_entry_id` bigint(20) UNSIGNED NOT NULL,
  `stock_type` enum('raw_material','finished_goods') NOT NULL,
  `grn_entry_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `art_no` varchar(255) DEFAULT NULL,
  `raw_material_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `finished_item_code` varchar(255) DEFAULT NULL COMMENT 'TEMPORARY: Replace with finished_good_id FK when finished_goods table exists',
  `sku` varchar(255) DEFAULT NULL,
  `qrcode` text DEFAULT NULL,
  `fabric_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sleeve_type` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `color_id` bigint(20) UNSIGNED DEFAULT NULL,
  `size_ratio_id` bigint(20) UNSIGNED DEFAULT NULL,
  `store_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `store_location_id` bigint(20) UNSIGNED NOT NULL,
  `uom_id` bigint(20) UNSIGNED DEFAULT NULL,
  `qty_in` decimal(15,2) NOT NULL DEFAULT 0.00,
  `qty_out` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `price` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_entry_items`
--

INSERT INTO `stock_entry_items` (`id`, `stock_entry_id`, `stock_type`, `grn_entry_item_id`, `art_no`, `raw_material_id`, `item_id`, `finished_item_code`, `sku`, `qrcode`, `fabric_type_id`, `sleeve_type`, `size`, `color_id`, `size_ratio_id`, `store_category_id`, `store_location_id`, `uom_id`, `qty_in`, `qty_out`, `created_at`, `updated_at`, `deleted_at`, `price`) VALUES
(1, 1, 'raw_material', 1, 'CF-34343', 1, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 3, 5, 150.00, 150.00, '2026-04-16 12:01:16', '2026-04-16 12:09:14', NULL, 96.00),
(2, 1, 'raw_material', 2, 'CF-34344', 2, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, 1, 1, 5, 160.00, 160.00, '2026-04-16 12:01:16', '2026-04-16 12:09:20', NULL, 99.00),
(3, 2, 'raw_material', 3, 'CF-34345', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, 3, 1800.00, 180.00, '2026-04-16 12:01:24', '2026-04-16 12:09:26', NULL, 10.00),
(4, 2, 'raw_material', 4, 'CF-34346', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, 3, 1698.00, 0.00, '2026-04-16 12:01:24', '2026-04-17 05:38:46', NULL, 10.00),
(5, 3, 'raw_material', 5, 'CF-0909', 1, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, 1, 3, 5, 99.44, 85.00, '2026-04-16 12:01:31', '2026-04-16 12:09:07', NULL, 99.00),
(6, 3, 'raw_material', 6, 'CF-09093', 1, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, 1, 3, 5, 113.37, 0.00, '2026-04-16 12:01:31', '2026-04-17 06:02:12', NULL, 99.00),
(7, 4, 'raw_material', 7, 'CF-34934', 1, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 3, 5, 171.28, 0.00, '2026-04-17 05:33:48', '2026-04-17 06:33:02', NULL, 90.00),
(8, 4, 'raw_material', 8, 'CF-34935', 1, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 3, 5, 113.37, 0.00, '2026-04-17 05:33:48', '2026-04-17 06:01:49', NULL, 90.00),
(9, 5, 'raw_material', 9, 'CF-34936', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, 3, 280.00, 0.00, '2026-04-17 05:33:54', '2026-04-17 05:33:54', NULL, 8.00),
(10, 6, 'raw_material', 12, 'CF-34937', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, 3, 3000.00, 0.00, '2026-04-17 06:05:24', '2026-04-17 06:34:19', NULL, 5.00),
(11, 7, 'raw_material', 10, 'CF-03489', 1, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 3, 5, 242.56, 0.00, '2026-04-17 06:05:30', '2026-04-17 06:32:19', NULL, 96.00),
(12, 7, 'raw_material', 11, 'CF-03480', 1, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 3, 5, 171.28, 0.00, '2026-04-17 06:05:30', '2026-04-17 06:32:42', NULL, 96.00),
(13, 8, 'raw_material', 14, 'CF-349300', 2, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 3, 5, 120.68, 0.00, '2026-04-17 06:42:49', '2026-04-17 07:06:51', NULL, 100.00),
(14, 8, 'raw_material', 15, 'CF-349301', 2, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 3, 5, 120.68, 0.00, '2026-04-17 06:42:49', '2026-04-17 07:07:06', NULL, 100.00),
(15, 9, 'raw_material', 13, 'CB-1001', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, 3, 1192.00, 0.00, '2026-04-17 06:42:54', '2026-04-17 07:07:27', NULL, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `store_categories`
--

CREATE TABLE `store_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_categories`
--

INSERT INTO `store_categories` (`id`, `code`, `category_name`, `description`, `status`, `updated_by`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'FBC', 'Fabric', NULL, 'Active', NULL, 1, NULL, '2026-04-16 10:27:04', '2026-04-16 10:27:04'),
(2, 'ACC', 'Accessories', NULL, 'Active', NULL, 1, NULL, '2026-04-16 10:27:25', '2026-04-16 10:27:25');

-- --------------------------------------------------------

--
-- Table structure for table `store_locations`
--

CREATE TABLE `store_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_location` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_locations`
--

INSERT INTO `store_locations` (`id`, `store_location`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'S1', 'Active', 1, NULL, '2026-04-16 09:50:42', '2026-04-16 09:50:42', NULL),
(2, 'S2', 'Active', 1, NULL, '2026-04-16 09:50:50', '2026-04-16 09:50:50', NULL),
(3, 'A1', 'Active', 1, NULL, '2026-04-16 09:51:00', '2026-04-16 09:51:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `store_types`
--

CREATE TABLE `store_types` (
  `id` int(11) NOT NULL,
  `store_type_name` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_types`
--

INSERT INTO `store_types` (`id`, `store_type_name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Fabric Store', 'Active', 1, NULL, '2026-04-16 17:04:05', '2026-04-16 17:04:05', NULL),
(2, 'Accessories Store', 'Active', 1, NULL, '2026-04-16 17:04:16', '2026-04-16 17:04:16', NULL),
(3, 'Finished Goods', 'Active', 1, NULL, '2026-04-16 17:04:29', '2026-04-16 17:04:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `styles`
--

CREATE TABLE `styles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `style_name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `styles`
--

INSERT INTO `styles` (`id`, `style_name`, `code`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'PLAIN', 'PLN', 'Active', 1, NULL, NULL, '2026-04-16 09:51:44', '2026-04-16 09:51:44'),
(2, 'PRINT', 'PRNT', 'Active', 1, NULL, NULL, '2026-04-16 09:51:57', '2026-04-16 09:51:57'),
(3, 'CHECKED', 'CHD', 'Active', 1, NULL, NULL, '2026-04-16 09:52:06', '2026-04-16 09:52:06'),
(4, 'STRIPED', 'STD', 'Active', 1, NULL, NULL, '2026-04-16 09:52:18', '2026-04-16 09:52:18');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `mobile_no` varchar(15) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `transport_name` varchar(100) DEFAULT NULL,
  `booking_area` varchar(100) DEFAULT NULL,
  `stores` varchar(255) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `place_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_line_1` varchar(150) DEFAULT NULL,
  `address_line_2` varchar(150) DEFAULT NULL,
  `address_line_3` varchar(150) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `contact_mobile_no` varchar(15) DEFAULT NULL,
  `contact_email` varchar(128) DEFAULT NULL,
  `purchase_commission_agent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `commission_percentage` decimal(8,2) DEFAULT NULL,
  `gst_no` varchar(255) DEFAULT NULL,
  `tax_id` bigint(20) UNSIGNED DEFAULT NULL,
  `igst_percent` decimal(8,2) DEFAULT NULL,
  `cgst_percent` decimal(8,2) DEFAULT NULL,
  `sgst_percent` decimal(8,2) DEFAULT NULL,
  `pan_no` varchar(10) DEFAULT NULL,
  `ecc_no` varchar(15) DEFAULT NULL,
  `credit_limit` decimal(10,2) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `code`, `mobile_no`, `email`, `website_url`, `transport_name`, `booking_area`, `stores`, `store_id`, `status`, `created_by`, `updated_by`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `address_line_3`, `zip_code`, `contact_person_name`, `designation`, `contact_mobile_no`, `contact_email`, `purchase_commission_agent_id`, `commission_percentage`, `gst_no`, `tax_id`, `igst_percent`, `cgst_percent`, `sgst_percent`, `pan_no`, `ecc_no`, `credit_limit`, `payment_terms`, `bank_name`, `branch`, `account_number`, `ifsc_code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'KAMAL SYNTHETICS', '1000', '8585742369', NULL, NULL, 'UTTAM ROADWAYS', NULL, NULL, 1, 'Active', 1, NULL, 3, 10, 7, 'A-205 AARYA MOOLCHAND COMPOUND 2ND FLOOR DAPODA ROAD', 'ANJUR PHATA', NULL, '400002', NULL, NULL, NULL, NULL, NULL, 0.00, '27ADNPJ9869J1ZL', NULL, 5.00, 0.00, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:14:00', '2026-04-16 10:14:00', NULL),
(2, 'BALAJI SHIRTING LLP', '1001', '8850761244', NULL, NULL, 'UTTAM ROADWAYS', 'MADURAI', NULL, 1, 'Active', 1, NULL, 3, 10, 7, 'H.NO: DHARANI ARCADE, OPP. MEGHDHARA COMPLEX', '2ND FLOOR, GALA NO. 12, ANJURPHATA,', 'RAHNAL VILLAGE, BHIWANDI', '421302', NULL, NULL, NULL, NULL, NULL, 0.00, '27ABCFB8882A1ZH', NULL, 5.00, 0.00, 0.00, NULL, NULL, 90.00, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:15:13', '2026-04-16 10:15:13', NULL),
(3, 'POOJA IMPEX', '1002', '9840377952', NULL, NULL, 'MADURAI RADHA TRANSPORT', 'MADURAI', NULL, 2, 'Active', 1, 1, 1, 2, 5, 'OLD NO 11A/2 NEW NO 20 LAWYER CHINNAI THAMBI STREET', 'KONDITHOPE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '33AEPPK6992J1ZT', NULL, 0.00, 9.00, 9.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:16:13', '2026-04-16 11:37:16', NULL),
(4, 'SHARMAN UDYOG PVT LTD', '1003', '9543880334', NULL, NULL, NULL, 'MADURAI', NULL, 3, 'Active', 1, NULL, 1, 1, 3, '161&153 G&H HSIIDC INDL ESTATE', 'PHASE -II KUNDLI', NULL, '131028', 'SARAVANAN', 'MARKETING MANAGER', '9543880334', NULL, NULL, 0.00, '06AACCS5208F2ZN', NULL, 18.00, 0.00, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:17:29', '2026-04-16 10:17:29', NULL),
(5, 'NATHALAKSHMI PRINTERS', '1004', '8220012476', 'jlsaravanan@gmail.com', NULL, NULL, 'MADURAI', NULL, 3, 'Active', 1, NULL, 1, 1, 4, '31,32,33,34 WEST CAR STREET', 'CUDDALORE', 'CHIDAMBARAM', '608001', NULL, NULL, NULL, NULL, NULL, 0.00, '33BPBPS9489A2ZN', NULL, 0.00, 2.50, 2.50, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-04-16 10:18:35', '2026-04-16 10:18:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_no` varchar(100) NOT NULL,
  `job_card_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_card_no` varchar(100) DEFAULT NULL,
  `stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `services` text DEFAULT NULL,
  `issued_to` bigint(20) UNSIGNED DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `total_hrs` decimal(10,2) DEFAULT NULL,
  `issue_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `issue_store` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Planned',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `task_no`, `job_card_entry_id`, `job_card_no`, `stage_id`, `services`, `issued_to`, `issue_date`, `due_date`, `total_hrs`, `issue_qty`, `issue_store`, `remarks`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TASK-001', 1, 'JC001', 1, '[\"1\",\"2\",\"3\",\"4\",\"5\"]', 2, '2026-04-16', '2026-04-17', NULL, 255.00, '2', NULL, 'Completed', 1, 1, '2026-04-17 04:57:07', '2026-04-17 05:09:27', NULL),
(2, 'TASK-002', 1, 'JC001', 2, '[\"6\",\"7\",\"8\"]', 4, '2026-04-20', '2026-04-21', NULL, 255.00, '2', NULL, 'Completed', 1, 1, '2026-04-17 05:10:52', '2026-04-17 05:11:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `task_adjustments`
--

CREATE TABLE `task_adjustments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `adjustment_no` varchar(100) NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `job_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `affected_stage` varchar(255) DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `overall_reason` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_adjustment_items`
--

CREATE TABLE `task_adjustment_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_adjustment_id` bigint(20) UNSIGNED NOT NULL,
  `raw_material_id` bigint(20) UNSIGNED NOT NULL,
  `grn_no` varchar(255) DEFAULT NULL,
  `art_no` varchar(255) DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `adjustment_type` varchar(255) NOT NULL,
  `qty` decimal(15,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `previous_stock` decimal(15,2) NOT NULL DEFAULT 0.00,
  `new_stock` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_assign_employees`
--

CREATE TABLE `task_assign_employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `issued_to` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `issue_qty` decimal(10,2) DEFAULT NULL,
  `completed_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `inprogress_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wastage_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `qc_checked_qty` int(11) NOT NULL DEFAULT 0,
  `qc_passed_qty` int(11) NOT NULL DEFAULT 0,
  `qc_rejected_qty` int(11) NOT NULL DEFAULT 0,
  `qc_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `total_hrs` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Open',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_assign_employees`
--

INSERT INTO `task_assign_employees` (`id`, `task_id`, `issued_to`, `service_id`, `issue_date`, `due_date`, `issue_qty`, `completed_qty`, `inprogress_qty`, `wastage_qty`, `qc_checked_qty`, `qc_passed_qty`, `qc_rejected_qty`, `qc_status`, `total_hrs`, `status`, `remarks`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, '2026-04-16', '2026-04-17', 255.00, 255.00, 0.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'Completed', NULL, 1, NULL, NULL, '2026-04-17 04:57:07', '2026-04-17 05:09:27'),
(2, 1, 3, 2, '2026-04-17', '2026-04-18', 255.00, 255.00, 0.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'Completed', NULL, 1, NULL, NULL, '2026-04-17 04:57:07', '2026-04-17 05:09:27'),
(3, 1, 2, 3, '2026-04-18', '2026-04-19', 255.00, 255.00, 0.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'Completed', NULL, 1, NULL, NULL, '2026-04-17 04:57:07', '2026-04-17 05:09:27'),
(4, 1, 3, 4, '2026-04-19', '2026-04-20', 255.00, 255.00, 0.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'Completed', NULL, 1, NULL, NULL, '2026-04-17 04:57:07', '2026-04-17 05:09:27'),
(5, 1, 2, 5, '2026-04-19', '2026-04-20', 255.00, 255.00, 0.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'Completed', NULL, 1, NULL, NULL, '2026-04-17 04:57:07', '2026-04-17 05:09:27'),
(6, 2, 4, 6, '2026-04-20', '2026-04-21', 255.00, 255.00, 0.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'Completed', NULL, 1, NULL, NULL, '2026-04-17 05:10:52', '2026-04-17 05:11:11'),
(7, 2, 5, 7, '2026-04-21', '2026-04-22', 255.00, 255.00, 0.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'Completed', NULL, 1, NULL, NULL, '2026-04-17 05:10:52', '2026-04-17 05:11:11'),
(8, 2, 5, 8, '2026-04-22', '2026-04-23', 255.00, 255.00, 0.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'Completed', NULL, 1, NULL, NULL, '2026-04-17 05:10:52', '2026-04-17 05:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `task_logs`
--

CREATE TABLE `task_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_logs`
--

INSERT INTO `task_logs` (`id`, `task_id`, `user_id`, `action`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Created', 'Task created with ticket number TASK-001', '2026-04-16 12:33:06', '2026-04-16 12:33:06'),
(2, 1, 1, 'Created', 'Task created with ticket number TASK-001', '2026-04-17 04:39:55', '2026-04-17 04:39:55'),
(3, 1, 1, 'Progress Update', 'Updated progress for **Kishore**: Completed: 0 -> 255', '2026-04-17 04:40:11', '2026-04-17 04:40:11'),
(4, 1, 1, 'Progress Update', 'Updated progress for **Pooja**: Completed: 0 -> 100', '2026-04-17 04:40:11', '2026-04-17 04:40:11'),
(5, 1, 1, 'Status Change', 'Task status automatically updated to In Progress', '2026-04-17 04:40:11', '2026-04-17 04:40:11'),
(6, 1, 1, 'Created', 'Task created with ticket number TASK-001', '2026-04-17 04:57:07', '2026-04-17 04:57:07'),
(7, 1, 1, 'Progress Update', 'Updated progress for **Kishore**: Completed: 0 -> 255', '2026-04-17 04:57:36', '2026-04-17 04:57:36'),
(8, 1, 1, 'Progress Update', 'Updated progress for **Pooja**: Completed: 0 -> 255', '2026-04-17 04:57:36', '2026-04-17 04:57:36'),
(9, 1, 1, 'Status Change', 'Task status automatically updated to In Progress', '2026-04-17 04:57:36', '2026-04-17 04:57:36'),
(10, 1, 1, 'Progress Update', 'Updated progress for **Kishore**: Completed: 0 -> 255', '2026-04-17 05:09:27', '2026-04-17 05:09:27'),
(11, 1, 1, 'Progress Update', 'Updated progress for **Pooja**: Completed: 0 -> 255', '2026-04-17 05:09:27', '2026-04-17 05:09:27'),
(12, 1, 1, 'Progress Update', 'Updated progress for **Kishore**: Completed: 0 -> 255', '2026-04-17 05:09:27', '2026-04-17 05:09:27'),
(13, 1, 1, 'Status Change', 'Task status automatically updated to Completed', '2026-04-17 05:09:27', '2026-04-17 05:09:27'),
(14, 2, 1, 'Created', 'Task created with ticket number TASK-002', '2026-04-17 05:10:52', '2026-04-17 05:10:52'),
(15, 2, 1, 'Progress Update', 'Updated progress for **Arjun**: Completed: 0 -> 255', '2026-04-17 05:11:11', '2026-04-17 05:11:11'),
(16, 2, 1, 'Progress Update', 'Updated progress for **Ganesh**: Completed: 0 -> 255', '2026-04-17 05:11:11', '2026-04-17 05:11:11'),
(17, 2, 1, 'Progress Update', 'Updated progress for **Ganesh**: Completed: 0 -> 255', '2026-04-17 05:11:11', '2026-04-17 05:11:11'),
(18, 2, 1, 'Status Change', 'Task status automatically updated to Completed', '2026-04-17 05:11:11', '2026-04-17 05:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `task_status`
--

CREATE TABLE `task_status` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(50) NOT NULL DEFAULT 'secondary',
  `progress_percent` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_status`
--

INSERT INTO `task_status` (`id`, `name`, `color`, `progress_percent`, `created_at`, `updated_at`) VALUES
(1, 'Planned', 'secondary', 0, NULL, NULL),
(2, 'In Progress', 'secondary', 0, NULL, NULL),
(3, 'Completed', 'secondary', 0, NULL, NULL),
(4, 'Hold', 'secondary', 0, NULL, NULL);

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
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `item_name`, `tax_rate`, `created_at`, `updated_at`, `deleted_at`, `status`, `created_by`, `updated_by`) VALUES
(1, 'test', 3.00, '2026-04-20 06:47:25', '2026-04-20 06:47:25', NULL, 'Active', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_no` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ticket_cat_id` bigint(20) UNSIGNED NOT NULL,
  `priority` varchar(255) NOT NULL,
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `operation_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_to_id` bigint(20) UNSIGNED DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `attachment` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `resolution_details` text DEFAULT NULL,
  `resolved_date` date DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_no`, `subject`, `description`, `ticket_cat_id`, `priority`, `requester_id`, `department_id`, `operation_stage_id`, `assigned_to_id`, `due_date`, `status`, `attachment`, `remarks`, `resolution_details`, `resolved_date`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TLT-2026-001', 'Fabric Cutting Machine Not Working', 'Fabric Cutting Machine Not Working', 9, 'Medium', 1, 1, 1, 2, '2026-03-06', 'Active', 'uploads/tickets/1772790510_download (7).png', 'test', NULL, NULL, 1, NULL, '2026-03-06 09:48:30', '2026-03-06 10:02:04', '2026-03-06 10:02:04'),
(3, 'TLT-2026-002', 'Fabric Cutting Machine Not Working', 'Machine blade stuck while cutting fabric', 1, 'Medium', 1, 1, 1, 2, '2026-03-20', 'Active', 'uploads/tickets/1772791579_download (7).png', NULL, NULL, NULL, 1, NULL, '2026-03-06 10:06:19', '2026-03-06 10:06:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_categories`
--

CREATE TABLE `ticket_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_categories`
--

INSERT INTO `ticket_categories` (`id`, `category_name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Machinery Fault', 'Active', '2026-03-06 09:28:55', '2026-03-06 09:28:55', NULL),
(2, 'IT Support', 'Active', '2026-03-06 09:28:55', '2026-03-06 09:28:55', NULL),
(3, 'Electrical', 'Active', '2026-03-06 09:28:55', '2026-03-06 09:28:55', NULL),
(4, 'Facility', 'Active', '2026-03-06 09:28:55', '2026-03-06 09:28:55', NULL),
(5, 'Logistics', 'Active', '2026-03-06 09:28:55', '2026-03-06 09:28:55', NULL),
(6, 'Security', 'Active', '2026-03-06 09:28:56', '2026-03-06 09:28:56', NULL),
(7, 'Production Issue', 'Active', '2026-03-06 09:45:11', '2026-03-06 09:45:11', NULL),
(8, 'Quality Issue', 'Active', '2026-03-06 09:45:11', '2026-03-06 09:45:11', NULL),
(9, 'Maintenance', 'Active', '2026-03-06 09:45:11', '2026-03-06 09:45:11', NULL),
(10, 'Inventory / Material Issue', 'Active', '2026-03-06 09:45:11', '2026-03-06 09:45:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transport_modes`
--

CREATE TABLE `transport_modes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_modes`
--

INSERT INTO `transport_modes` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ROAD', 'Active', 1, NULL, '2026-04-16 09:55:07', '2026-04-16 09:55:07', NULL),
(2, 'RAIL', 'Active', 1, NULL, '2026-04-16 09:55:15', '2026-04-16 09:55:15', NULL),
(3, 'AIR', 'Active', 1, NULL, '2026-04-16 09:55:22', '2026-04-16 09:55:22', NULL),
(4, 'SEA', 'Active', 1, NULL, '2026-04-16 09:55:28', '2026-04-16 09:55:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `uoms`
--

CREATE TABLE `uoms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uom_code` varchar(50) NOT NULL,
  `uom_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `uoms`
--

INSERT INTO `uoms` (`id`, `uom_code`, `uom_name`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'KG', 'KILOGRAM', NULL, 'Active', 1, NULL, '2026-04-16 09:44:58', '2026-04-16 09:44:58', NULL),
(2, 'BL', 'BALE', NULL, 'Active', 1, NULL, '2026-04-16 09:45:16', '2026-04-16 09:45:16', NULL),
(3, 'NOS', 'NUMBERS', NULL, 'Active', 1, NULL, '2026-04-16 09:45:27', '2026-04-16 09:45:27', NULL),
(4, 'BDL', 'BUNDLE', NULL, 'Active', 1, NULL, '2026-04-16 09:45:40', '2026-04-16 09:45:40', NULL),
(5, 'MTR', 'METER', NULL, 'Active', 1, NULL, '2026-04-16 09:45:51', '2026-04-16 09:45:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `operation_stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `emp_id` varchar(100) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `blood_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `email` varchar(128) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `father_phone` varchar(15) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_line1` varchar(150) DEFAULT NULL,
  `address_line2` varchar(150) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `contact_person_phone` varchar(16) DEFAULT NULL,
  `contact_person_email` varchar(128) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `hra` decimal(10,2) DEFAULT NULL,
  `allowances` decimal(10,2) DEFAULT NULL,
  `deductions` decimal(10,2) DEFAULT NULL,
  `gross_salary` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `service_provider_id`, `operation_stage_id`, `emp_id`, `department_id`, `role_id`, `blood_group_id`, `name`, `phone`, `email`, `email_verified_at`, `password`, `remember_token`, `created_by`, `updated_by`, `created_at`, `updated_at`, `date_of_joining`, `father_name`, `father_phone`, `country`, `state_id`, `city_id`, `address_line1`, `address_line2`, `zipcode`, `contact_person_name`, `contact_person_phone`, `contact_person_email`, `basic_salary`, `hra`, `allowances`, `deductions`, `gross_salary`, `net_salary`, `account_number`, `bank_name`, `ifsc_code`, `profile_image`, `esi_document`, `pf_document`, `aadhaar_document`, `pan_document`, `status`, `deleted_at`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, 'Admin', '8520147963', 'admin@gmail.com', NULL, '$2y$10$4F7zEd8RmTgdr.djAWj0aOOrvYZotpY.9g9B030jiuyJ0Sj3ZsdUu', '4LiFWkOJra1sWjak1ht8O5iu3jGHEUU2YJ8qKlziFgdCtiFyKY1fckLaKTuI', NULL, NULL, NULL, '2026-02-26 06:02:14', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'profile.png', NULL, NULL, NULL, NULL, 'Active', NULL),
(2, 1, NULL, '1001', 1, 2, 4, 'Kishore', '9658580210', 'kishore32@gmail.com', NULL, '$2y$10$K03SB1mn/JU5.YHIxV.8qOgJpdwVMDI/rEZDTFcn3sfOMDdkX6JTG', 'hKLpoZ9cRxaGoUhrXt5BKRbUPN0BJZudKoRn5C5wOCxcSkAnNl34y2XfsMFY', 1, 1, '2026-04-16 12:21:16', '2026-04-20 05:23:26', '2024-04-17', 'Arul', '6938956565', NULL, 1, 1, '25, Arapalayam Main Road', NULL, '625011', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', NULL),
(3, 1, NULL, '1002', 2, 1, 4, 'Pooja', '6956261616', 'pooja.saitech@gmail.com', NULL, '$2y$10$WPq.peRc08YcrPbIrDTLWe6Pv9c9aTNOD.fORD7FogtBsBFaVEW7C', NULL, 1, NULL, '2026-04-16 12:22:25', '2026-04-16 12:22:25', '2026-04-16', 'Arya', '8520369874', NULL, 1, 2, '25, T.Nagar', NULL, '620008', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', NULL),
(4, 2, NULL, '1003', 1, 1, 5, 'Arjun', '9685968596', 'arjun@gmail.com', NULL, '$2y$10$Dik7GyeYb95NgUocguvNnOeUXbkuDcNYw3ZqOMmEG/RV8XU1VpvT2', NULL, 1, NULL, '2026-04-16 12:23:20', '2026-04-16 12:23:20', '2026-04-16', NULL, NULL, NULL, 4, 5, '25, Efa Building road', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', NULL),
(5, 2, NULL, '1004', 1, 1, NULL, 'Ganesh', '8639589635', 'ganesh@gmail.com', NULL, '$2y$10$eNaAW/U6eXXofupNCTK9OuN1WhhsUgp4eTHhpiTcBw/Xb7ty1d7uW', NULL, 1, NULL, '2026-04-16 12:24:37', '2026-04-16 12:24:37', NULL, NULL, NULL, NULL, 1, 2, '25, Kodambakkam Road', NULL, '325006', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_ids` text NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `zone_name`, `state_id`, `city_ids`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'I', 1, '1,3', 'Active', '2026-04-16 09:47:50', '2026-04-16 09:47:50', NULL, 1, NULL),
(2, 'II', 4, '4,5', 'Active', '2026-04-16 09:48:05', '2026-04-16 09:48:05', NULL, 1, NULL),
(3, 'III', 2, '7,8', 'Active', '2026-04-16 09:48:23', '2026-04-16 09:48:23', NULL, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `backups_backup_no_unique` (`backup_no`),
  ADD KEY `backups_created_by_foreign` (`created_by`);

--
-- Indexes for table `billings`
--
ALTER TABLE `billings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blood_groups`
--
ALTER TABLE `blood_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blood_groups_blood_grp_name_unique` (`blood_grp_name`);

--
-- Indexes for table `bottom_cuts`
--
ALTER TABLE `bottom_cuts`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `collar_types`
--
ALTER TABLE `collar_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_notes`
--
ALTER TABLE `credit_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `credit_notes_note_no_unique` (`note_no`);

--
-- Indexes for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `credit_note_items_credit_note_id_foreign` (`credit_note_id`);

--
-- Indexes for table `cuff_types`
--
ALTER TABLE `cuff_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_code_unique` (`code`);

--
-- Indexes for table `debit_notes`
--
ALTER TABLE `debit_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `debit_notes_debit_note_no_unique` (`debit_note_no`);

--
-- Indexes for table `debit_note_charges`
--
ALTER TABLE `debit_note_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `debit_note_charges_debit_note_id_foreign` (`debit_note_id`),
  ADD KEY `debit_note_charges_charge_id_foreign` (`charge_id`);

--
-- Indexes for table `debit_note_items`
--
ALTER TABLE `debit_note_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_repositories`
--
ALTER TABLE `document_repositories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_repositories_department_id_foreign` (`department_id`);

--
-- Indexes for table `fabric_sizes`
--
ALTER TABLE `fabric_sizes`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `fits`
--
ALTER TABLE `fits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grn_entries`
--
ALTER TABLE `grn_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_entries_created_by_foreign` (`created_by`),
  ADD KEY `grn_entries_purchase_invoice_id_foreign` (`purchase_invoice_id`),
  ADD KEY `grn_entries_supplier_id_foreign` (`supplier_id`),
  ADD KEY `grn_entries_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `grn_entry_items`
--
ALTER TABLE `grn_entry_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_entry_items_grn_entry_id_foreign` (`grn_entry_id`),
  ADD KEY `grn_entry_items_fabric_type_id_foreign` (`fabric_type_id`),
  ADD KEY `grn_entry_items_store_location_id_foreign` (`store_location_id`);

--
-- Indexes for table `grn_entry_item_variants`
--
ALTER TABLE `grn_entry_item_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_entry_item_variants_grn_entry_item_id_foreign` (`grn_entry_item_id`),
  ADD KEY `grn_entry_item_variants_color_id_foreign` (`color_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `items_name_unique` (`name`),
  ADD UNIQUE KEY `items_code_unique` (`code`),
  ADD UNIQUE KEY `items_product_barcode_unique` (`product_barcode`);

--
-- Indexes for table `job_card_cutting_size_ratios`
--
ALTER TABLE `job_card_cutting_size_ratios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_items_job_card_entry_id_foreign` (`job_card_entry_id`);

--
-- Indexes for table `job_card_entries`
--
ALTER TABLE `job_card_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_card_entries_job_card_number_unique` (`job_card_no`),
  ADD KEY `job_card_entries_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `job_card_entries_brand_id_foreign` (`brand_id`),
  ADD KEY `job_card_entries_season_id_foreign` (`season_id`),
  ADD KEY `job_card_entries_process_group_id_foreign` (`process_group_id`),
  ADD KEY `job_card_entries_service_provider_id_foreign` (`service_provider_id`),
  ADD KEY `job_card_entries_fit_id_foreign` (`fit_id`),
  ADD KEY `job_card_entries_patti_type_id_foreign` (`patti_type_id`),
  ADD KEY `job_card_entries_collar_type_id_foreign` (`collar_type_id`),
  ADD KEY `job_card_entries_cuff_type_id_foreign` (`cuff_type_id`),
  ADD KEY `job_card_entries_pocket_type_id_foreign` (`pocket_type_id`),
  ADD KEY `job_card_entries_bottom_cut_id_foreign` (`bottom_cut_id`);

--
-- Indexes for table `job_card_fabric_consumptions`
--
ALTER TABLE `job_card_fabric_consumptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_fabric_consumptions_job_card_fabric_detail_id_foreign` (`job_card_fabric_detail_id`);

--
-- Indexes for table `job_card_fabric_details`
--
ALTER TABLE `job_card_fabric_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_article_matrices_job_card_entry_id_foreign` (`job_card_entry_id`);

--
-- Indexes for table `job_card_images`
--
ALTER TABLE `job_card_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_images_job_card_entry_id_foreign` (`job_card_entry_id`);

--
-- Indexes for table `job_card_issue_items`
--
ALTER TABLE `job_card_issue_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_issue_items_job_card_entry_id_foreign` (`job_card_entry_id`),
  ADD KEY `job_card_issue_items_job_card_article_matrix_id_foreign` (`job_card_article_matrix_id`),
  ADD KEY `job_card_issue_items_created_by_foreign` (`created_by`),
  ADD KEY `job_card_issue_items_updated_by_foreign` (`updated_by`),
  ADD KEY `job_card_issue_items_raw_material_id_foreign` (`raw_material_id`);

--
-- Indexes for table `job_card_issue_stock_details`
--
ALTER TABLE `job_card_issue_stock_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_issue_stock_details_job_card_issue_item_id_foreign` (`job_card_issue_item_id`),
  ADD KEY `job_card_issue_stock_details_stock_entry_item_id_foreign` (`stock_entry_item_id`);

--
-- Indexes for table `job_card_lay_marks`
--
ALTER TABLE `job_card_lay_marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_lay_marks_job_card_fabric_detail_id_foreign` (`job_card_fabric_detail_id`);

--
-- Indexes for table `job_card_matrix_quantities`
--
ALTER TABLE `job_card_matrix_quantities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jc_matrix_quant_id` (`job_card_fabric_detail_id`),
  ADD KEY `job_card_matrix_quantities_color_id_index` (`color_id`);

--
-- Indexes for table `job_card_operations`
--
ALTER TABLE `job_card_operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_operations_job_card_entry_id_foreign` (`job_card_entry_id`),
  ADD KEY `job_card_operations_operation_stage_id_foreign` (`operation_stage_id`),
  ADD KEY `job_card_operations_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `job_card_sleeve_meters`
--
ALTER TABLE `job_card_sleeve_meters`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

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
-- Indexes for table `patti_types`
--
ALTER TABLE `patti_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_payment_no_unique` (`payment_no`);

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
-- Indexes for table `pocket_types`
--
ALTER TABLE `pocket_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `process_groups`
--
ALTER TABLE `process_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `process_schedules`
--
ALTER TABLE `process_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `process_schedules_production_id_foreign` (`production_id`),
  ADD KEY `process_schedules_created_by_foreign` (`created_by`),
  ADD KEY `process_schedules_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `production_movements`
--
ALTER TABLE `production_movements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_receipts`
--
ALTER TABLE `production_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_receipts_production_id_index` (`production_id`),
  ADD KEY `production_receipts_job_card_id_index` (`job_card_id`),
  ADD KEY `production_receipts_store_type_id_index` (`store_type_id`),
  ADD KEY `production_receipts_store_location_id_foreign` (`store_location_id`);

--
-- Indexes for table `production_receipt_items`
--
ALTER TABLE `production_receipt_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_services`
--
ALTER TABLE `production_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_stage_consumables`
--
ALTER TABLE `production_stage_consumables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_stage_consumables_raw_material_id_foreign` (`raw_material_id`),
  ADD KEY `production_stage_consumables_uom_id_foreign` (`uom_id`),
  ADD KEY `production_stage_consumables_created_by_foreign` (`created_by`),
  ADD KEY `production_stage_consumables_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `purchase_commission_agents`
--
ALTER TABLE `purchase_commission_agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_commission_agents_code_unique` (`code`);

--
-- Indexes for table `purchase_invoices`
--
ALTER TABLE `purchase_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_invoices_invoice_no_unique` (`invoice_no`);

--
-- Indexes for table `purchase_invoice_charges`
--
ALTER TABLE `purchase_invoice_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_invoice_items`
--
ALTER TABLE `purchase_invoice_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_invoice_payments`
--
ALTER TABLE `purchase_invoice_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_invoice_payments_purchase_invoice_id_foreign` (`purchase_invoice_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_fabric_width_id_foreign` (`fabric_width_id`);

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
  ADD KEY `sales_agents_place_id_foreign` (`place_id`),
  ADD KEY `sales_agents_zone_id_foreign` (`zone_id`);

--
-- Indexes for table `sales_invoices`
--
ALTER TABLE `sales_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_invoices_inv_no_unique` (`inv_no`);

--
-- Indexes for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_invoice_items_sales_invoice_id_foreign` (`sales_invoice_id`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_orders_so_no_unique` (`so_no`),
  ADD KEY `sale_orders_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_order_items_sale_order_id_foreign` (`sale_order_id`);

--
-- Indexes for table `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seasons_season_code_unique` (`season_code`);

--
-- Indexes for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_providers_operation_stage_id_foreign` (`operation_stage_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shifts_shift_name_unique` (`shift_name`),
  ADD KEY `shifts_created_by_foreign` (`created_by`),
  ADD KEY `shifts_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shipping_methods_name_unique` (`name`);

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
-- Indexes for table `stock_consumable_issues`
--
ALTER TABLE `stock_consumable_issues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_consumable_issues_issue_no_unique` (`issue_no`),
  ADD KEY `stock_consumable_issues_created_by_foreign` (`created_by`),
  ADD KEY `stock_consumable_issues_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `stock_consumable_issue_items`
--
ALTER TABLE `stock_consumable_issue_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_consumable_issue_items_stock_consumable_issue_id_foreign` (`stock_consumable_issue_id`),
  ADD KEY `stock_consumable_issue_items_raw_material_id_foreign` (`raw_material_id`),
  ADD KEY `stock_consumable_issue_items_stock_entry_item_id_foreign` (`stock_entry_item_id`),
  ADD KEY `stock_consumable_issue_items_uom_id_foreign` (`uom_id`),
  ADD KEY `stock_consumable_issue_items_created_by_foreign` (`created_by`),
  ADD KEY `stock_consumable_issue_items_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `stock_entries`
--
ALTER TABLE `stock_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_entries_created_by_foreign` (`created_by`),
  ADD KEY `stock_entries_from_store_location_id_foreign` (`from_store_location_id`),
  ADD KEY `stock_entries_grn_entry_id_foreign` (`grn_entry_id`),
  ADD KEY `stock_entries_to_store_location_id_foreign` (`to_store_location_id`),
  ADD KEY `stock_entries_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `stock_entry_adjustments`
--
ALTER TABLE `stock_entry_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_entry_adjustments_adjustment_no_unique` (`adjustment_no`),
  ADD KEY `stock_entry_adjustments_stock_entry_item_id_foreign` (`stock_entry_item_id`),
  ADD KEY `stock_entry_adjustments_raw_material_id_foreign` (`raw_material_id`),
  ADD KEY `stock_entry_adjustments_created_by_foreign` (`created_by`),
  ADD KEY `stock_entry_adjustments_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `stock_entry_items`
--
ALTER TABLE `stock_entry_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_entry_items_stock_entry_id_foreign` (`stock_entry_id`),
  ADD KEY `stock_entry_items_grn_entry_item_id_foreign` (`grn_entry_item_id`),
  ADD KEY `stock_entry_items_raw_material_id_foreign` (`raw_material_id`),
  ADD KEY `stock_entry_items_store_category_id_foreign` (`store_category_id`),
  ADD KEY `stock_entry_items_store_location_id_foreign` (`store_location_id`),
  ADD KEY `stock_entry_items_uom_id_foreign` (`uom_id`),
  ADD KEY `stock_entry_items_size_ratio_id_foreign` (`size_ratio_id`),
  ADD KEY `stock_entry_items_item_id_foreign` (`item_id`);

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
-- Indexes for table `store_types`
--
ALTER TABLE `store_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `styles`
--
ALTER TABLE `styles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_code_unique` (`code`),
  ADD KEY `suppliers_store_id_foreign` (`store_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tasks_task_no_unique` (`task_no`);

--
-- Indexes for table `task_adjustments`
--
ALTER TABLE `task_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `task_adjustments_adjustment_no_unique` (`adjustment_no`),
  ADD KEY `task_adjustments_task_id_foreign` (`task_id`),
  ADD KEY `task_adjustments_job_card_id_foreign` (`job_card_id`);

--
-- Indexes for table `task_adjustment_items`
--
ALTER TABLE `task_adjustment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_adjustment_items_raw_material_id_foreign` (`raw_material_id`),
  ADD KEY `task_adjustment_items_service_id_foreign` (`service_id`);

--
-- Indexes for table `task_assign_employees`
--
ALTER TABLE `task_assign_employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_logs`
--
ALTER TABLE `task_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_status`
--
ALTER TABLE `task_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `task_status_name_unique` (`name`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tickets_ticket_no_unique` (`ticket_no`),
  ADD KEY `tickets_ticket_cat_id_foreign` (`ticket_cat_id`),
  ADD KEY `tickets_requester_id_foreign` (`requester_id`),
  ADD KEY `tickets_department_id_foreign` (`department_id`),
  ADD KEY `tickets_operation_stage_id_foreign` (`operation_stage_id`),
  ADD KEY `tickets_assigned_to_id_foreign` (`assigned_to_id`),
  ADD KEY `tickets_created_by_foreign` (`created_by`),
  ADD KEY `tickets_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ticket_categories`
--
ALTER TABLE `ticket_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transport_modes`
--
ALTER TABLE `transport_modes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transport_modes_name_unique` (`name`);

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
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_service_provider_id_foreign` (`service_provider_id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billings`
--
ALTER TABLE `billings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `blood_groups`
--
ALTER TABLE `blood_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bottom_cuts`
--
ALTER TABLE `bottom_cuts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `collar_types`
--
ALTER TABLE `collar_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `credit_notes`
--
ALTER TABLE `credit_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cuff_types`
--
ALTER TABLE `cuff_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `debit_notes`
--
ALTER TABLE `debit_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `debit_note_charges`
--
ALTER TABLE `debit_note_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `debit_note_items`
--
ALTER TABLE `debit_note_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `document_repositories`
--
ALTER TABLE `document_repositories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `fabric_sizes`
--
ALTER TABLE `fabric_sizes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `fits`
--
ALTER TABLE `fits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grn_entries`
--
ALTER TABLE `grn_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `grn_entry_items`
--
ALTER TABLE `grn_entry_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `grn_entry_item_variants`
--
ALTER TABLE `grn_entry_item_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `job_card_cutting_size_ratios`
--
ALTER TABLE `job_card_cutting_size_ratios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `job_card_entries`
--
ALTER TABLE `job_card_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `job_card_fabric_consumptions`
--
ALTER TABLE `job_card_fabric_consumptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `job_card_fabric_details`
--
ALTER TABLE `job_card_fabric_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `job_card_images`
--
ALTER TABLE `job_card_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_card_issue_items`
--
ALTER TABLE `job_card_issue_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `job_card_issue_stock_details`
--
ALTER TABLE `job_card_issue_stock_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_card_lay_marks`
--
ALTER TABLE `job_card_lay_marks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `job_card_matrix_quantities`
--
ALTER TABLE `job_card_matrix_quantities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `job_card_operations`
--
ALTER TABLE `job_card_operations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `job_card_sleeve_meters`
--
ALTER TABLE `job_card_sleeve_meters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=359;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT for table `operation_stages`
--
ALTER TABLE `operation_stages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patti_types`
--
ALTER TABLE `patti_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=270;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pocket_types`
--
ALTER TABLE `pocket_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `process_groups`
--
ALTER TABLE `process_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `process_schedules`
--
ALTER TABLE `process_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `production_movements`
--
ALTER TABLE `production_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `production_receipts`
--
ALTER TABLE `production_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `production_receipt_items`
--
ALTER TABLE `production_receipt_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_services`
--
ALTER TABLE `production_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `production_stage_consumables`
--
ALTER TABLE `production_stage_consumables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `purchase_commission_agents`
--
ALTER TABLE `purchase_commission_agents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchase_invoices`
--
ALTER TABLE `purchase_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `purchase_invoice_charges`
--
ALTER TABLE `purchase_invoice_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_invoice_items`
--
ALTER TABLE `purchase_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_invoice_payments`
--
ALTER TABLE `purchase_invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1171;

--
-- AUTO_INCREMENT for table `sales_agents`
--
ALTER TABLE `sales_agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_invoices`
--
ALTER TABLE `sales_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_providers`
--
ALTER TABLE `service_providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `size_ratios`
--
ALTER TABLE `size_ratios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stock_consumable_issues`
--
ALTER TABLE `stock_consumable_issues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_consumable_issue_items`
--
ALTER TABLE `stock_consumable_issue_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_entries`
--
ALTER TABLE `stock_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stock_entry_adjustments`
--
ALTER TABLE `stock_entry_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `stock_entry_items`
--
ALTER TABLE `stock_entry_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `store_categories`
--
ALTER TABLE `store_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `store_locations`
--
ALTER TABLE `store_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `store_types`
--
ALTER TABLE `store_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `styles`
--
ALTER TABLE `styles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `task_adjustments`
--
ALTER TABLE `task_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_adjustment_items`
--
ALTER TABLE `task_adjustment_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_assign_employees`
--
ALTER TABLE `task_assign_employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `task_logs`
--
ALTER TABLE `task_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `task_status`
--
ALTER TABLE `task_status`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket_categories`
--
ALTER TABLE `ticket_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transport_modes`
--
ALTER TABLE `transport_modes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `uoms`
--
ALTER TABLE `uoms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `backups`
--
ALTER TABLE `backups`
  ADD CONSTRAINT `backups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  ADD CONSTRAINT `credit_note_items_credit_note_id_foreign` FOREIGN KEY (`credit_note_id`) REFERENCES `credit_notes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `debit_note_charges`
--
ALTER TABLE `debit_note_charges`
  ADD CONSTRAINT `debit_note_charges_charge_id_foreign` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`),
  ADD CONSTRAINT `debit_note_charges_debit_note_id_foreign` FOREIGN KEY (`debit_note_id`) REFERENCES `debit_notes` (`id`);

--
-- Constraints for table `document_repositories`
--
ALTER TABLE `document_repositories`
  ADD CONSTRAINT `document_repositories_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grn_entries`
--
ALTER TABLE `grn_entries`
  ADD CONSTRAINT `grn_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `grn_entries_purchase_invoice_id_foreign` FOREIGN KEY (`purchase_invoice_id`) REFERENCES `purchase_invoices` (`id`),
  ADD CONSTRAINT `grn_entries_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `grn_entries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `grn_entry_items`
--
ALTER TABLE `grn_entry_items`
  ADD CONSTRAINT `grn_entry_items_fabric_type_id_foreign` FOREIGN KEY (`fabric_type_id`) REFERENCES `fabric_types` (`id`),
  ADD CONSTRAINT `grn_entry_items_grn_entry_id_foreign` FOREIGN KEY (`grn_entry_id`) REFERENCES `grn_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grn_entry_items_store_location_id_foreign` FOREIGN KEY (`store_location_id`) REFERENCES `store_locations` (`id`);

--
-- Constraints for table `grn_entry_item_variants`
--
ALTER TABLE `grn_entry_item_variants`
  ADD CONSTRAINT `grn_entry_item_variants_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`),
  ADD CONSTRAINT `grn_entry_item_variants_grn_entry_item_id_foreign` FOREIGN KEY (`grn_entry_item_id`) REFERENCES `grn_entry_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_card_cutting_size_ratios`
--
ALTER TABLE `job_card_cutting_size_ratios`
  ADD CONSTRAINT `job_card_items_job_card_entry_id_foreign` FOREIGN KEY (`job_card_entry_id`) REFERENCES `job_card_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_card_entries`
--
ALTER TABLE `job_card_entries`
  ADD CONSTRAINT `job_card_entries_bottom_cut_id_foreign` FOREIGN KEY (`bottom_cut_id`) REFERENCES `bottom_cuts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_collar_type_id_foreign` FOREIGN KEY (`collar_type_id`) REFERENCES `collar_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_cuff_type_id_foreign` FOREIGN KEY (`cuff_type_id`) REFERENCES `cuff_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_fit_id_foreign` FOREIGN KEY (`fit_id`) REFERENCES `fits` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_patti_type_id_foreign` FOREIGN KEY (`patti_type_id`) REFERENCES `patti_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_pocket_type_id_foreign` FOREIGN KEY (`pocket_type_id`) REFERENCES `pocket_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_process_group_id_foreign` FOREIGN KEY (`process_group_id`) REFERENCES `process_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_entries_service_provider_id_foreign` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`);

--
-- Constraints for table `job_card_fabric_consumptions`
--
ALTER TABLE `job_card_fabric_consumptions`
  ADD CONSTRAINT `job_card_fabric_consumptions_job_card_fabric_detail_id_foreign` FOREIGN KEY (`job_card_fabric_detail_id`) REFERENCES `job_card_fabric_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_card_fabric_details`
--
ALTER TABLE `job_card_fabric_details`
  ADD CONSTRAINT `job_card_article_matrices_job_card_entry_id_foreign` FOREIGN KEY (`job_card_entry_id`) REFERENCES `job_card_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_card_images`
--
ALTER TABLE `job_card_images`
  ADD CONSTRAINT `job_card_images_job_card_entry_id_foreign` FOREIGN KEY (`job_card_entry_id`) REFERENCES `job_card_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_card_issue_items`
--
ALTER TABLE `job_card_issue_items`
  ADD CONSTRAINT `job_card_issue_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `job_card_issue_items_job_card_article_matrix_id_foreign` FOREIGN KEY (`job_card_article_matrix_id`) REFERENCES `job_card_fabric_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_card_issue_items_job_card_entry_id_foreign` FOREIGN KEY (`job_card_entry_id`) REFERENCES `job_card_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_card_issue_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`),
  ADD CONSTRAINT `job_card_issue_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `job_card_issue_stock_details`
--
ALTER TABLE `job_card_issue_stock_details`
  ADD CONSTRAINT `job_card_issue_stock_details_job_card_issue_item_id_foreign` FOREIGN KEY (`job_card_issue_item_id`) REFERENCES `job_card_issue_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_card_issue_stock_details_stock_entry_item_id_foreign` FOREIGN KEY (`stock_entry_item_id`) REFERENCES `stock_entry_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_card_lay_marks`
--
ALTER TABLE `job_card_lay_marks`
  ADD CONSTRAINT `job_card_lay_marks_job_card_fabric_detail_id_foreign` FOREIGN KEY (`job_card_fabric_detail_id`) REFERENCES `job_card_fabric_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_card_matrix_quantities`
--
ALTER TABLE `job_card_matrix_quantities`
  ADD CONSTRAINT `fk_jc_matrix_quant_id` FOREIGN KEY (`job_card_fabric_detail_id`) REFERENCES `job_card_fabric_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_card_operations`
--
ALTER TABLE `job_card_operations`
  ADD CONSTRAINT `job_card_operations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_operations_job_card_entry_id_foreign` FOREIGN KEY (`job_card_entry_id`) REFERENCES `job_card_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_card_operations_operation_stage_id_foreign` FOREIGN KEY (`operation_stage_id`) REFERENCES `operation_stages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `process_schedules`
--
ALTER TABLE `process_schedules`
  ADD CONSTRAINT `process_schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `process_schedules_production_id_foreign` FOREIGN KEY (`production_id`) REFERENCES `productions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `process_schedules_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `production_receipts`
--
ALTER TABLE `production_receipts`
  ADD CONSTRAINT `production_receipts_store_location_id_foreign` FOREIGN KEY (`store_location_id`) REFERENCES `store_locations` (`id`);

--
-- Constraints for table `production_services`
--
ALTER TABLE `production_services`
  ADD CONSTRAINT `production_services_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `production_services_operation_stage_id_foreign` FOREIGN KEY (`operation_stage_id`) REFERENCES `operation_stages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_services_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `production_stage_consumables`
--
ALTER TABLE `production_stage_consumables`
  ADD CONSTRAINT `production_stage_consumables_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `production_stage_consumables_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`),
  ADD CONSTRAINT `production_stage_consumables_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`),
  ADD CONSTRAINT `production_stage_consumables_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_invoice_payments`
--
ALTER TABLE `purchase_invoice_payments`
  ADD CONSTRAINT `purchase_invoice_payments_purchase_invoice_id_foreign` FOREIGN KEY (`purchase_invoice_id`) REFERENCES `purchase_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_fabric_width_id_foreign` FOREIGN KEY (`fabric_width_id`) REFERENCES `size_ratios` (`id`);

--
-- Constraints for table `sales_agents`
--
ALTER TABLE `sales_agents`
  ADD CONSTRAINT `sales_agents_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_agents_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_agents_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_agents_zone_id_foreign` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  ADD CONSTRAINT `sales_invoice_items_sales_invoice_id_foreign` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sale_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sale_order_items_sale_order_id_foreign` FOREIGN KEY (`sale_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD CONSTRAINT `service_providers_operation_stage_id_foreign` FOREIGN KEY (`operation_stage_id`) REFERENCES `operation_stages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shifts_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_consumable_issues`
--
ALTER TABLE `stock_consumable_issues`
  ADD CONSTRAINT `stock_consumable_issues_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_consumable_issues_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_consumable_issue_items`
--
ALTER TABLE `stock_consumable_issue_items`
  ADD CONSTRAINT `stock_consumable_issue_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_consumable_issue_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`),
  ADD CONSTRAINT `stock_consumable_issue_items_stock_consumable_issue_id_foreign` FOREIGN KEY (`stock_consumable_issue_id`) REFERENCES `stock_consumable_issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_consumable_issue_items_stock_entry_item_id_foreign` FOREIGN KEY (`stock_entry_item_id`) REFERENCES `stock_entry_items` (`id`),
  ADD CONSTRAINT `stock_consumable_issue_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`),
  ADD CONSTRAINT `stock_consumable_issue_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_entries`
--
ALTER TABLE `stock_entries`
  ADD CONSTRAINT `stock_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_entries_from_store_location_id_foreign` FOREIGN KEY (`from_store_location_id`) REFERENCES `store_locations` (`id`),
  ADD CONSTRAINT `stock_entries_grn_entry_id_foreign` FOREIGN KEY (`grn_entry_id`) REFERENCES `grn_entries` (`id`),
  ADD CONSTRAINT `stock_entries_to_store_location_id_foreign` FOREIGN KEY (`to_store_location_id`) REFERENCES `store_locations` (`id`),
  ADD CONSTRAINT `stock_entries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_entry_adjustments`
--
ALTER TABLE `stock_entry_adjustments`
  ADD CONSTRAINT `stock_entry_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_entry_adjustments_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`),
  ADD CONSTRAINT `stock_entry_adjustments_stock_entry_item_id_foreign` FOREIGN KEY (`stock_entry_item_id`) REFERENCES `stock_entry_items` (`id`),
  ADD CONSTRAINT `stock_entry_adjustments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_entry_items`
--
ALTER TABLE `stock_entry_items`
  ADD CONSTRAINT `stock_entry_items_grn_entry_item_id_foreign` FOREIGN KEY (`grn_entry_item_id`) REFERENCES `grn_entry_items` (`id`),
  ADD CONSTRAINT `stock_entry_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_entry_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`),
  ADD CONSTRAINT `stock_entry_items_size_ratio_id_foreign` FOREIGN KEY (`size_ratio_id`) REFERENCES `size_ratios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_entry_items_store_category_id_foreign` FOREIGN KEY (`store_category_id`) REFERENCES `store_categories` (`id`),
  ADD CONSTRAINT `stock_entry_items_store_location_id_foreign` FOREIGN KEY (`store_location_id`) REFERENCES `store_locations` (`id`),
  ADD CONSTRAINT `stock_entry_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`);

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `store_types` (`id`);

--
-- Constraints for table `task_adjustments`
--
ALTER TABLE `task_adjustments`
  ADD CONSTRAINT `task_adjustments_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_card_entries` (`id`),
  ADD CONSTRAINT `task_adjustments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_adjustment_items`
--
ALTER TABLE `task_adjustment_items`
  ADD CONSTRAINT `task_adjustment_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`),
  ADD CONSTRAINT `task_adjustment_items_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `production_services` (`id`),
  ADD CONSTRAINT `task_adjustment_items_task_adjustment_id_foreign` FOREIGN KEY (`task_adjustment_id`) REFERENCES `task_adjustments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_assigned_to_id_foreign` FOREIGN KEY (`assigned_to_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tickets_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `tickets_operation_stage_id_foreign` FOREIGN KEY (`operation_stage_id`) REFERENCES `operation_stages` (`id`),
  ADD CONSTRAINT `tickets_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tickets_ticket_cat_id_foreign` FOREIGN KEY (`ticket_cat_id`) REFERENCES `ticket_categories` (`id`),
  ADD CONSTRAINT `tickets_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_service_provider_id_foreign` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
