-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2026 at 02:01 PM
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
(1, 'BIL-001', 'Transport', '2026-03-03', 1500.00, 'Buy', 'Paid', '2026-03-03 11:13:10', '2026-03-03 11:16:57', NULL);

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
(1, 'Allen Solly', 'ASY', 'Active', 1, NULL, NULL, '2026-02-23 09:35:41', '2026-02-23 09:35:41');

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
(1, 'FST', 'Fomal Shirt', NULL, 'Active', 1, NULL, NULL, '2026-02-23 09:35:16', '2026-02-23 09:35:16');

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
(1, 'TCS', 'Active', 1, NULL, '2026-02-23 09:30:56', '2026-02-23 09:30:56', NULL);

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
(1, 1, 'Madurai', 'MDU', 'Active', 1, NULL, '2026-02-23 09:25:16', '2026-02-23 09:25:16', NULL),
(2, 1, 'Chennai', 'CH', 'Active', 1, NULL, '2026-02-26 07:12:25', '2026-02-26 07:12:25', NULL);

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
(1, 'Cross', 'Active', 1, NULL, NULL, '2026-02-26 08:37:15', '2026-02-26 08:37:15');

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
(1, 'Red', NULL, '2026-02-26 13:39:04', '2026-02-26 13:39:04', 'Active', 1, NULL, NULL);

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

INSERT INTO `credit_notes` (`id`, `note_no`, `note_date`, `sales_invoice_id`, `customer_id`, `reason`, `other_state`, `igst_percent`, `igst`, `cgst_percent`, `cgst`, `sgst_percent`, `sgst`, `sub_total`, `tax_amount`, `grand_total`, `remarks`, `reference_doc`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CN-001', '2026-06-20', 1, 1, 'Return', 0, 18.00, 0.00, 9.00, 16.08, 9.00, 16.08, 178.68, 32.16, 210.84, 'For reason', 'credit_note_1772542130.jpg', 'Draft', 1, NULL, '2026-03-03 12:27:04', '2026-03-03 12:48:50', NULL);

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

--
-- Dumping data for table `credit_note_items`
--

INSERT INTO `credit_note_items` (`id`, `credit_note_id`, `sales_invoice_item_id`, `brand_category_id`, `item_id`, `size`, `quantity`, `sleeve_type`, `mrp`, `uom_id`, `rate`, `amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 3, 1, 1, '40', 1.00, 'Full', 360.00, 1, 178.68, 178.68, '2026-03-03 12:27:04', '2026-03-03 12:39:58', '2026-03-03 12:39:58'),
(2, 1, 3, 1, 1, '40', 1.00, 'Full', 360.00, 1, 178.68, 178.68, '2026-03-03 12:39:58', '2026-03-03 12:41:30', '2026-03-03 12:41:30'),
(3, 1, 3, 1, 1, '40', 1.00, 'Full', 360.00, 1, 178.68, 178.68, '2026-03-03 12:41:30', '2026-03-03 12:46:38', '2026-03-03 12:46:38'),
(4, 1, 3, 1, 1, '40', 1.00, 'Full', 360.00, 1, 178.68, 178.68, '2026-03-03 12:46:38', '2026-03-03 12:48:39', '2026-03-03 12:48:39'),
(5, 1, 3, 1, 1, '40', 1.00, 'Full', 360.00, 1, 178.68, 178.68, '2026-03-03 12:48:39', '2026-03-03 12:48:50', '2026-03-03 12:48:50'),
(6, 1, 3, 1, 1, '40', 1.00, 'Full', 360.00, 1, 178.68, 178.68, '2026-03-03 12:48:50', '2026-03-03 12:48:50', NULL);

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
(1, 'Wholesaler', 'AK Ahamed & Co', '1000', '8220055143', 'aaykaymdu@yahoo.co.in', NULL, 'Thirumalammal Lorry Booking Office', NULL, 1, 1, 'Active', 1, 1, 1, 1, 1, '9, Navabathkana Street, Mahal Area, Madurai Main,', NULL, NULL, '625011', 'Sai', 'Employeer', '9698520147', 'sai89@gmail.com', NULL, '33AADFA4747M1ZD', 'AADFA4747M', 'Advance Payment', 2.00, 1.00, 10.00, 'State Bank of India', 'Anna Nagar', '123456789012', 'SBIN0001234', '2026-02-26 09:03:02', '2026-02-26 09:36:32', NULL);

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
  `igst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `cgst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sgst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
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

--
-- Dumping data for table `debit_notes`
--

INSERT INTO `debit_notes` (`id`, `debit_note_no`, `debit_note_date`, `purchase_invoice_id`, `supplier_id`, `other_state`, `reason`, `sub_total`, `igst_percent`, `cgst_percent`, `sgst_percent`, `tax_amount`, `round_off_type`, `round_off`, `grand_total`, `remarks`, `reference_document`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'DN-0001', '2026-03-03', 1, 1, 'N', NULL, 1485.00, 9.00, 18.00, 9.00, 400.95, 'Add', 0.00, 1886.00, 'ttestetsetest', 'debit_note_1772541261.jpg', 'Draft', 1, NULL, NULL, '2026-03-03 12:34:21', '2026-03-03 12:34:21');

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

--
-- Dumping data for table `debit_note_items`
--

INSERT INTO `debit_note_items` (`id`, `debit_note_id`, `purchase_invoice_item_id`, `raw_material_id`, `quantity`, `uom_id`, `rate`, `amount`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 15.00, 2, 99.00, 1485.00, NULL, '2026-03-03 12:34:21', '2026-03-03 12:34:21');

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
(1, 'Cutting Department', 'Active', 1, NULL, '2026-02-23 09:38:07', '2026-02-23 09:38:07', NULL),
(2, 'Stitching Department', 'Active', 1, NULL, '2026-02-26 08:22:13', '2026-02-26 08:22:13', NULL);

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
(1, 'Plain', 'Active', '2026-02-26 08:17:43', '2026-02-26 08:23:07', '2026-02-26 08:23:07', 1, NULL),
(2, 'Cotton', 'Active', '2026-02-26 08:17:52', '2026-02-26 08:23:16', NULL, 1, 1),
(3, 'Print', 'Active', '2026-02-26 08:17:59', '2026-02-26 08:23:10', '2026-02-26 08:23:10', 1, NULL);

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
(1, 'Cross', 'Active', 1, 1, NULL, '2026-02-26 08:34:54', '2026-02-26 08:35:02');

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
(1, 'GRN001', '2026-02-23', 1, 1, '2026-02-23', 'Received', 1, NULL, '2026-02-23 09:31:55', '2026-02-23 09:31:55', NULL);

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

INSERT INTO `grn_entry_items` (`id`, `grn_entry_id`, `purchase_invoice_item_id`, `art_no`, `fabric_type_id`, `qty_ordered`, `qty_received`, `qty_accepted`, `qty_rejected`, `qty_balanced`, `rate`, `amount`, `quality_check_status`, `store_location_id`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'CF-123451', NULL, 150.00, 150.00, 150.00, 0.00, 0.00, 99.00, 14850.00, 'Pass', 1, NULL, '2026-02-23 09:31:55', '2026-02-23 09:31:55', NULL),
(2, 1, 2, 'CF-123452', NULL, 300.00, 300.00, 300.00, 0.00, 0.00, 6.00, 1800.00, 'Pass', 1, NULL, '2026-02-23 09:31:55', '2026-02-23 09:31:55', NULL);

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
(1, 1, 1, 'Formal Cotton Shirt', '1001', NULL, NULL, NULL, 1, NULL, '', NULL, NULL, NULL, NULL, NULL, '{\"cutting\":null}', NULL, NULL, NULL, NULL, 'Active', 1, NULL, '2026-02-23 09:36:12', '2026-02-23 09:36:12', NULL),
(2, 1, 1, 'Formal Lenin Shirt', 'FLS', 1, 2, NULL, 1, 1, '1', NULL, NULL, NULL, NULL, NULL, '{\"cutting\":null,\"stitching\":null}', NULL, NULL, NULL, NULL, 'Active', 1, NULL, '2026-02-27 12:44:59', '2026-02-27 12:44:59', NULL);

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
(9, 1, '38', 0.00, 10.00, 15.00, 25.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(10, 1, '40', 0.00, 10.00, 15.00, 25.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(11, 1, '42', 0.00, 10.00, 15.00, 25.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(12, 1, '44', 0.00, 10.00, 15.00, 25.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_card_entries`
--

CREATE TABLE `job_card_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_no` varchar(100) NOT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `purchase_order_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_entries`
--

INSERT INTO `job_card_entries` (`id`, `job_card_no`, `reference_no`, `purchase_order_id`, `service_provider_id`, `issue_store_id`, `receipt_store_id`, `fit_id`, `patti_type_id`, `collar_type_id`, `cuff_type_id`, `pocket_type_id`, `bottom_cut_id`, `brand_id`, `brand_category_id`, `item_id`, `season_id`, `process_group_id`, `size_ratio_id`, `job_card_date`, `delivery_date`, `washing`, `width`, `fs_qty`, `hs_qty`, `ex_1_label`, `ex_2_label`, `price_fs`, `price_hs`, `total_qty_fs`, `total_qty_hs`, `grand_total_qty`, `average`, `remarks`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'JC001', 'JC001', 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, 1, 1, '2026-02-23', '2026-03-02', 'No', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, 60.00, 100.00, 8.30, NULL, 'Production Completed', 1, NULL, '2026-02-23 09:42:11', '2026-02-25 08:48:51', NULL);

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
(9, 5, '38', 1.320, 1.040, '2026-02-25 08:48:51', '2026-02-25 08:48:51'),
(10, 5, '40', 1.320, 1.040, '2026-02-25 08:48:51', '2026-02-25 08:48:51'),
(11, 5, '42', 1.320, 1.040, '2026-02-25 08:48:51', '2026-02-25 08:48:51'),
(12, 5, '44', 1.320, 1.040, '2026-02-25 08:48:51', '2026-02-25 08:48:51');

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
  `row_total` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_fabric_details`
--

INSERT INTO `job_card_fabric_details` (`id`, `job_card_entry_id`, `art_no`, `width`, `mtr`, `in_out`, `n_patti`, `fs_qty`, `hs_qty`, `row_total`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 1, 'CF-123451', NULL, '150', 'NO', 'WHITE', NULL, NULL, 100, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(6, 1, 'CF-123452', NULL, '680', 'NO', 'WHITE', 8.00, 6.00, 680, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL);

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
-- Table structure for table `job_card_matrix_quantities`
--

CREATE TABLE `job_card_matrix_quantities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_fabric_detail_id` bigint(20) UNSIGNED NOT NULL,
  `size` varchar(255) NOT NULL,
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

INSERT INTO `job_card_matrix_quantities` (`id`, `job_card_fabric_detail_id`, `size`, `qty_fs`, `qty_hs`, `total_qty`, `created_at`, `updated_at`, `deleted_at`) VALUES
(17, 5, '38', 10.00, 15.00, 25.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(18, 5, '40', 10.00, 15.00, 25.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(19, 5, '42', 10.00, 15.00, 25.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(20, 5, '44', 10.00, 15.00, 25.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(21, 6, '38', 80.00, 90.00, 170.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(22, 6, '40', 80.00, 90.00, 170.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(23, 6, '42', 80.00, 90.00, 170.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(24, 6, '44', 80.00, 90.00, 170.00, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL);

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
(3, 1, 1, 1, 2, '2026-02-23', '2026-03-04', NULL, NULL, '2026-02-25 08:48:51', '2026-02-25 08:48:51');

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
(1, 1, 'create', 'State', 'states', 1, NULL, '{\"id\":1,\"state_code\":\"TN\",\"state_name\":\"Tamil Nadu\",\"status\":\"Active\",\"created_at\":\"2026-02-23T09:24:55.000000Z\",\"updated_at\":\"2026-02-23T09:24:55.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:24:55'),
(2, 1, 'create', 'City', 'cities', 1, NULL, '{\"id\":1,\"state_id\":1,\"city_name\":\"Madurai\",\"city_code\":\"MDU\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:25:16.000000Z\",\"updated_at\":\"2026-02-23T09:25:16.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:25:16'),
(3, 1, 'create', 'Place', 'places', 1, NULL, '{\"id\":1,\"state_id\":1,\"city_id\":1,\"place_name\":\"Keelavasal\",\"place_type\":\"Residential\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:26:11.000000Z\",\"updated_at\":\"2026-02-23T09:26:11.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:26:11'),
(4, 1, 'create', 'UOM', 'uoms', 1, NULL, '{\"id\":1,\"uom_code\":\"PCS\",\"uom_name\":\"Pieces\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:26:27.000000Z\",\"updated_at\":\"2026-02-23T09:26:27.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:26:27'),
(5, 1, 'create', 'UOM', 'uoms', 2, NULL, '{\"id\":2,\"uom_code\":\"MTR\",\"uom_name\":\"Meter\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:26:41.000000Z\",\"updated_at\":\"2026-02-23T09:26:41.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:26:41'),
(6, 1, 'create', 'Supplier', 'suppliers', 1, NULL, '{\"name\":\"Shri\",\"code\":\"1000\",\"mobile_no\":\"8585858585\",\"email\":\"ushadevi.saitech@gmail.com\",\"website_url\":null,\"transport_name\":null,\"booking_area\":null,\"stores\":null,\"store_id\":null,\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"1\",\"address_line_1\":\"Jaihindpuram\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":null,\"designation\":null,\"contact_mobile_no\":null,\"contact_email\":null,\"purchase_commission_agent_id\":null,\"commission_percentage\":0,\"tax_id\":null,\"gst_no\":null,\"pan_no\":null,\"ecc_no\":null,\"credit_limit\":0,\"payment_terms\":null,\"bank_name\":null,\"branch\":null,\"account_number\":null,\"ifsc_code\":null,\"created_by\":1,\"updated_at\":\"2026-02-23T09:27:42.000000Z\",\"created_at\":\"2026-02-23T09:27:42.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:27:42'),
(7, 1, 'create', 'Store Category', 'store_categories', 1, NULL, '{\"code\":\"FBC\",\"category_name\":\"Fabric\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:28:02.000000Z\",\"created_at\":\"2026-02-23T09:28:02.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:28:02'),
(8, 1, 'create', 'Raw Material', 'raw_materials', 1, NULL, '{\"store_category_id\":\"1\",\"code\":\"1000\",\"name\":\"Cotton Fabric\",\"supplier_design_name\":null,\"size_width\":null,\"uom_id\":\"2\",\"fabric_type_id\":null,\"specification\":null,\"min_stock\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:28:32.000000Z\",\"created_at\":\"2026-02-23T09:28:32.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:28:32'),
(9, 1, 'create', 'Store Category', 'store_categories', 2, NULL, '{\"code\":\"ACC\",\"category_name\":\"Accessories\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:28:45.000000Z\",\"created_at\":\"2026-02-23T09:28:45.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:28:45'),
(10, 1, 'create', 'Raw Material', 'raw_materials', 2, NULL, '{\"store_category_id\":\"2\",\"code\":\"1001\",\"name\":\"Buttons\",\"supplier_design_name\":null,\"size_width\":null,\"uom_id\":\"1\",\"fabric_type_id\":null,\"specification\":null,\"min_stock\":\"0\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:29:02.000000Z\",\"created_at\":\"2026-02-23T09:29:02.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:29:02'),
(11, 1, 'create', 'Store Type', 'store_types', 1, NULL, '{\"store_type_name\":\"Fabric Store\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:29:48'),
(12, 1, 'create', 'Purchase Order', 'purchase_orders', 1, NULL, '{\"po_number\":\"PO-001\",\"po_date\":\"2026-02-22T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":\"1\",\"reference_no\":\"PO-001\",\"reference_date\":\"2026-02-22T18:30:00.000000Z\",\"due_date\":\"2026-03-12T18:30:00.000000Z\",\"store_type_id\":\"1\",\"payment_terms\":null,\"status\":\"Draft\",\"total_qty\":\"450.00\",\"sub_total\":\"16650.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"16650.00\",\"other_state\":false,\"igst_percent\":\"9.00\",\"cgst_percent\":\"18.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"4495.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"21145.50\",\"updated_at\":\"2026-02-23T09:30:28.000000Z\",\"created_at\":\"2026-02-23T09:30:28.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:30:28'),
(13, 1, 'update_status', 'Purchase Order Status', 'purchase_orders', 1, '{\"id\":1,\"po_number\":\"PO-001\",\"po_date\":\"2026-02-22T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":1,\"reference_no\":\"PO-001\",\"reference_date\":\"2026-02-22T18:30:00.000000Z\",\"due_date\":\"2026-03-12T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Draft\",\"additional_attachments\":null,\"created_by\":null,\"updated_by\":null,\"total_qty\":\"450.00\",\"sub_total\":\"16650.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"16650.00\",\"other_state\":false,\"igst_percent\":\"9.00\",\"cgst_percent\":\"18.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"4495.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"21145.50\",\"created_at\":\"2026-02-23T09:30:28.000000Z\",\"updated_at\":\"2026-02-23T09:30:28.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"po_number\":\"PO-001\",\"po_date\":\"2026-02-22T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":1,\"reference_no\":\"PO-001\",\"reference_date\":\"2026-02-22T18:30:00.000000Z\",\"due_date\":\"2026-03-12T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Approved\",\"additional_attachments\":null,\"created_by\":null,\"updated_by\":null,\"total_qty\":\"450.00\",\"sub_total\":\"16650.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"16650.00\",\"other_state\":false,\"igst_percent\":\"9.00\",\"cgst_percent\":\"18.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"4495.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"21145.50\",\"created_at\":\"2026-02-23T09:30:28.000000Z\",\"updated_at\":\"2026-02-23T09:30:31.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:30:31'),
(14, 1, 'create', 'Charge', 'charges', 1, NULL, '{\"charge_name\":\"TCS\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:30:56.000000Z\",\"created_at\":\"2026-02-23T09:30:56.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:30:56'),
(15, 1, 'create', 'Purchase Invoice', 'purchase_invoices', 1, NULL, '{\"invoice_no\":\"INV-001\",\"invoice_date\":\"2026-02-22T18:30:00.000000Z\",\"purchase_order_id\":\"1\",\"supplier_id\":\"1\",\"po_reference\":\"PO-001\",\"sub_total\":\"16650.00\",\"discount_percent\":\"0\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"16650.00\",\"other_state\":false,\"igst_percent\":\"9\",\"igst_amount\":\"0\",\"cgst_percent\":\"18\",\"cgst_amount\":\"2997.00\",\"sgst_percent\":\"9\",\"sgst_amount\":\"1498.50\",\"tax_amount\":\"4495.50\",\"other_charges\":\"0.00\",\"round_off\":\"0\",\"round_off_type\":\"Add\",\"grand_total\":\"21145.50\",\"received_amount\":\"0\",\"due_amount\":\"21145.50\",\"invoice_status\":\"Draft\",\"payment_mode\":null,\"due_date\":null,\"notes\":null,\"transaction_id\":null,\"updated_at\":\"2026-02-23T09:31:06.000000Z\",\"created_at\":\"2026-02-23T09:31:06.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:31:06'),
(16, 1, 'update_status', 'Purchase Invoice Status', 'purchase_invoices', 1, '{\"id\":1,\"invoice_no\":\"INV-001\",\"invoice_date\":\"2026-02-22T18:30:00.000000Z\",\"purchase_order_id\":1,\"supplier_id\":1,\"po_reference\":\"PO-001\",\"sub_total\":\"16650.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"16650.00\",\"other_state\":false,\"igst_percent\":\"9.00\",\"igst_amount\":\"0.00\",\"cgst_percent\":\"18.00\",\"cgst_amount\":\"2997.00\",\"sgst_percent\":\"9.00\",\"sgst_amount\":\"1498.50\",\"tax_amount\":\"4495.50\",\"other_charges\":\"0.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"grand_total\":\"21145.50\",\"received_amount\":\"0.00\",\"due_amount\":\"21145.50\",\"invoice_status\":\"Draft\",\"created_by\":null,\"updated_by\":null,\"payment_mode\":null,\"transaction_id\":null,\"due_date\":null,\"notes\":null,\"auth_signature\":null,\"attachments\":null,\"created_at\":\"2026-02-23T09:31:06.000000Z\",\"updated_at\":\"2026-02-23T09:31:06.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"invoice_no\":\"INV-001\",\"invoice_date\":\"2026-02-22T18:30:00.000000Z\",\"purchase_order_id\":1,\"supplier_id\":1,\"po_reference\":\"PO-001\",\"sub_total\":\"16650.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"16650.00\",\"other_state\":false,\"igst_percent\":\"9.00\",\"igst_amount\":\"0.00\",\"cgst_percent\":\"18.00\",\"cgst_amount\":\"2997.00\",\"sgst_percent\":\"9.00\",\"sgst_amount\":\"1498.50\",\"tax_amount\":\"4495.50\",\"other_charges\":\"0.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"grand_total\":\"21145.50\",\"received_amount\":\"0.00\",\"due_amount\":\"21145.50\",\"invoice_status\":\"Unpaid\\/Credit\",\"created_by\":null,\"updated_by\":null,\"payment_mode\":null,\"transaction_id\":null,\"due_date\":null,\"notes\":null,\"auth_signature\":null,\"attachments\":null,\"created_at\":\"2026-02-23T09:31:06.000000Z\",\"updated_at\":\"2026-02-23T09:31:09.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:31:09'),
(17, 1, 'create', 'Store Location', 'store_locations', 1, NULL, '{\"store_location\":\"S1\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:31:47.000000Z\",\"created_at\":\"2026-02-23T09:31:47.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:31:47'),
(18, 1, 'create', 'GRN Entry', 'grn_entries', 1, NULL, '{\"grn_date\":\"2026-02-22T18:30:00.000000Z\",\"purchase_invoice_id\":\"1\",\"supplier_id\":1,\"supplier_invoice_date\":\"2026-02-22T18:30:00.000000Z\",\"status\":\"Received\",\"grn_number\":\"GRN001\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:31:55.000000Z\",\"created_at\":\"2026-02-23T09:31:55.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:31:55'),
(19, 1, 'update', 'Purchase Order Status (Auto)', 'purchase_orders', 1, '{\"status\":\"Approved\"}', '{\"status\":\"Received\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:31:55'),
(20, 1, 'create', 'Stock Entry', 'stock_entries', 1, NULL, '{\"stock_date\":\"2026-02-23\",\"grn_entry_id\":\"1\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":\"1\",\"remarks\":null,\"status\":\"Draft\",\"price\":\"99.00\",\"stock_entry_no\":\"SE00001\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:32:04'),
(21, 1, 'create', 'Stock Entry', 'stock_entries', 2, NULL, '{\"stock_date\":\"2026-02-23\",\"grn_entry_id\":\"1\",\"entry_type\":\"Raw Material\",\"from_store_location_id\":null,\"to_store_location_id\":\"1\",\"remarks\":null,\"status\":\"Draft\",\"price\":\"6.00\",\"stock_entry_no\":\"SE00002\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:32:09'),
(22, 1, 'create', 'Process Group', 'process_groups', 1, NULL, '{\"name\":\"Checked Sleeve\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:32:30'),
(23, 1, 'create', 'Size Ratio', 'size_ratios', 1, NULL, '{\"size\":\"38,40,42,44\",\"ratio\":\"9,5,4,6\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:32:50.000000Z\",\"created_at\":\"2026-02-23T09:32:50.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:32:50'),
(24, 1, 'create', 'Operation Stage', 'operation_stages', 1, NULL, '{\"operation_stage_name\":\"Cutting\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:33:23.000000Z\",\"created_at\":\"2026-02-23T09:33:23.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:33:23'),
(25, 1, 'create', 'Service Provider', 'service_providers', 1, NULL, '{\"operation_stage_id\":\"1\",\"name\":\"Nachias Fashion Private Limited\",\"code\":\"NFPL\",\"is_plant\":1,\"email\":\"ushadevi.saitech@gmail.com\",\"mobile_no\":\"9685741200\",\"zip_code\":\"625011\",\"website_url\":null,\"service_rate\":\"Per Agent\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"1\",\"address_line_1\":\"Jaihindpuram\",\"address_line_2\":null,\"contact_person_name\":null,\"designation\":null,\"phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"remarks\":null,\"bank_name\":null,\"bank_acc_no\":null,\"ifsc_code\":null,\"payment_terms\":null,\"created_by\":1,\"updated_at\":\"2026-02-23T09:34:24.000000Z\",\"created_at\":\"2026-02-23T09:34:24.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:34:24'),
(26, 1, 'create', 'Brand Category', 'brands_categories', 1, NULL, '{\"code\":\"FST\",\"name\":\"Fomal Shirt\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:35:16.000000Z\",\"created_at\":\"2026-02-23T09:35:16.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:35:16'),
(27, 1, 'create', 'Brand', 'brands', 1, NULL, '{\"brand_name\":\"Allen Solly\",\"code\":\"ASY\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:35:41.000000Z\",\"created_at\":\"2026-02-23T09:35:41.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:35:41'),
(28, 1, 'create', 'Item', 'items', 1, NULL, '{\"brand_id\":\"1\",\"brand_category_id\":\"1\",\"name\":\"Formal Cotton Shirt\",\"code\":\"1001\",\"style_id\":null,\"fabric_type_id\":null,\"design_art_no\":null,\"uom_id\":\"1\",\"size_ratio_id\":null,\"color_id\":[],\"standard_costing\":null,\"store_category_id\":null,\"related_materials\":null,\"operation_stages\":null,\"service_providers\":{\"cutting\":null},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-23T09:36:12.000000Z\",\"created_at\":\"2026-02-23T09:36:12.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:36:12'),
(29, 1, 'create', 'Role', 'roles', 1, NULL, '{\"id\":1,\"name\":\"Manageer\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-02-23T09:36:53.000000Z\",\"updated_at\":\"2026-02-23T09:36:53.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:36:53'),
(30, 1, 'create', 'Role', 'roles', 2, NULL, '{\"id\":2,\"name\":\"Assistant Manager\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-02-23T09:37:02.000000Z\",\"updated_at\":\"2026-02-23T09:37:02.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:37:02'),
(31, 1, 'create', 'Role', 'roles', 3, NULL, '{\"id\":3,\"name\":\"Executive\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-02-23T09:37:11.000000Z\",\"updated_at\":\"2026-02-23T09:37:11.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:37:11'),
(32, 1, 'create', 'Role', 'roles', 4, NULL, '{\"id\":4,\"name\":\"Quality Checker\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-02-23T09:37:31.000000Z\",\"updated_at\":\"2026-02-23T09:37:31.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:37:31'),
(33, 1, 'create', 'Role', 'roles', 5, NULL, '{\"id\":5,\"name\":\"Supervisior\",\"guard_name\":\"web\",\"created_by\":null,\"updated_by\":null,\"status\":\"Active\",\"created_at\":\"2026-02-23T09:37:44.000000Z\",\"updated_at\":\"2026-02-23T09:37:44.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:37:45'),
(34, 1, 'create', 'Department', 'departments', 1, NULL, '{\"department\":\"Cutting Department\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:38:07'),
(35, 1, 'create', 'User', 'users', 2, NULL, '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":null,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":null,\"name\":\"Usha\",\"phone\":\"8585858585\",\"email\":\"usha@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:38:53.000000Z\",\"updated_at\":\"2026-02-23T09:38:53.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:38:53'),
(36, 1, 'update', 'User', 'users', 2, '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":null,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":null,\"name\":\"Usha\",\"phone\":\"8585858585\",\"email\":\"usha@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:38:53.000000Z\",\"updated_at\":\"2026-02-23T09:38:53.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":null,\"name\":\"Usha\",\"phone\":\"8585858585\",\"email\":\"usha@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-23T09:38:53.000000Z\",\"updated_at\":\"2026-02-23T09:41:53.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:41:53'),
(37, 1, 'create', 'Job Card Entry', 'job_card_entries', 1, NULL, '{\"job_card_no\":\"JC001\",\"reference_no\":\"JC001\",\"purchase_order_id\":\"1\",\"service_provider_id\":\"1\",\"issue_store_id\":\"1\",\"receipt_store_id\":\"1\",\"job_card_date\":\"2026-02-23\",\"delivery_date\":\"2026-03-02\",\"washing\":\"No\",\"width\":null,\"season_id\":null,\"brand_id\":\"1\",\"fs_qty\":null,\"hs_qty\":null,\"remarks\":null,\"status\":\"Production Hold\",\"brand_category_id\":\"1\",\"item_id\":\"1\",\"fit_id\":null,\"patti_type_id\":null,\"collar_type_id\":null,\"cuff_type_id\":null,\"pocket_type_id\":null,\"bottom_cut_id\":null,\"total_qty_fs\":\"40\",\"total_qty_hs\":\"60\",\"grand_total_qty\":100,\"process_group_id\":\"1\",\"size_ratio_id\":\"1\",\"ex_1_label\":null,\"ex_2_label\":null,\"created_by\":1,\"updated_at\":\"2026-02-23T09:42:11.000000Z\",\"created_at\":\"2026-02-23T09:42:11.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 09:42:11'),
(38, 1, 'create', 'Production Service', 'production_services', 1, NULL, '{\"service_name\":\"Fabric Cutting\",\"service_code\":\"FB-CUT\",\"operation_stage_id\":\"1\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 10:02:26'),
(39, 1, 'update', 'Job Card Entry', 'job_card_entries', 1, '{\"id\":1,\"job_card_no\":\"JC001\",\"reference_no\":\"JC001\",\"purchase_order_id\":1,\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":null,\"patti_type_id\":null,\"collar_type_id\":null,\"cuff_type_id\":null,\"pocket_type_id\":null,\"bottom_cut_id\":null,\"brand_id\":1,\"brand_category_id\":1,\"item_id\":1,\"season_id\":null,\"process_group_id\":1,\"size_ratio_id\":1,\"job_card_date\":\"2026-02-23\",\"delivery_date\":\"2026-03-02\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"40.00\",\"total_qty_hs\":\"60.00\",\"grand_total_qty\":\"100.00\",\"average\":\"8.30\",\"remarks\":null,\"status\":\"Production Hold\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:42:11.000000Z\",\"updated_at\":\"2026-02-23T09:42:11.000000Z\",\"deleted_at\":null,\"issue_items\":[]}', '{\"id\":1,\"job_card_no\":\"JC001\",\"reference_no\":\"JC001\",\"purchase_order_id\":1,\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":null,\"patti_type_id\":null,\"collar_type_id\":null,\"cuff_type_id\":null,\"pocket_type_id\":null,\"bottom_cut_id\":null,\"brand_id\":1,\"brand_category_id\":1,\"item_id\":1,\"season_id\":null,\"process_group_id\":1,\"size_ratio_id\":1,\"job_card_date\":\"2026-02-23\",\"delivery_date\":\"2026-03-02\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"40.00\",\"total_qty_hs\":\"60.00\",\"grand_total_qty\":\"100.00\",\"average\":\"8.30\",\"remarks\":null,\"status\":\"Production Completed\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:42:11.000000Z\",\"updated_at\":\"2026-02-23T12:42:14.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 12:42:14'),
(40, 1, 'create', 'Production Receipt', 'production_receipts', 1, NULL, '{\"id\":1,\"production_id\":null,\"job_card_id\":1,\"customer_name\":\"Shri\",\"order_due_date\":\"2026-03-13\",\"receipt_no\":\"1001\",\"receipt_date\":\"2026-02-23\",\"doc_no\":\"JC001\",\"doc_date\":\"2026-02-23\",\"store_type_id\":1,\"store_location_id\":1,\"status\":\"Draft\",\"remarks\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\",\"items\":[{\"id\":1,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"38\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"38 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":2,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"38\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"38 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":3,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"40\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"40 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":4,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"40\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"40 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":5,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"42\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"42 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":6,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"42\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"42 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":7,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"44\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"44 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":8,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"44\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"44 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"}]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 12:46:06'),
(41, 1, 'update', 'Production Receipt', 'production_receipts', 1, '{\"id\":1,\"production_id\":null,\"job_card_id\":1,\"customer_name\":\"Shri\",\"order_due_date\":\"2026-03-13\",\"receipt_no\":\"1001\",\"receipt_date\":\"2026-02-23\",\"doc_no\":\"JC001\",\"doc_date\":\"2026-02-23\",\"store_type_id\":1,\"store_location_id\":1,\"status\":\"Draft\",\"remarks\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\",\"items\":[{\"id\":1,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"38\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"38 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":2,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"38\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"38 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":3,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"40\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"40 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":4,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"40\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"40 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":5,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"42\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"42 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":6,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"42\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"42 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":7,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"44\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"44 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"},{\"id\":8,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"44\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"44 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:46:06.000000Z\"}]}', '{\"id\":1,\"production_id\":null,\"job_card_id\":1,\"customer_name\":\"Shri\",\"order_due_date\":\"2026-03-13\",\"receipt_no\":\"1001\",\"receipt_date\":\"2026-02-23\",\"doc_no\":\"JC001\",\"doc_date\":\"2026-02-23\",\"store_type_id\":1,\"store_location_id\":1,\"status\":\"Posted\",\"remarks\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-23T12:46:06.000000Z\",\"updated_at\":\"2026-02-23T12:50:40.000000Z\",\"items\":[{\"id\":9,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"38\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"38 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:50:40.000000Z\",\"updated_at\":\"2026-02-23T12:50:40.000000Z\"},{\"id\":10,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"38\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"38 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:50:40.000000Z\",\"updated_at\":\"2026-02-23T12:50:40.000000Z\"},{\"id\":11,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"40\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"40 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:50:40.000000Z\",\"updated_at\":\"2026-02-23T12:50:40.000000Z\"},{\"id\":12,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"40\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"40 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:50:40.000000Z\",\"updated_at\":\"2026-02-23T12:50:40.000000Z\"},{\"id\":13,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"42\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"42 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:50:40.000000Z\",\"updated_at\":\"2026-02-23T12:50:40.000000Z\"},{\"id\":14,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"42\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"42 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:50:40.000000Z\",\"updated_at\":\"2026-02-23T12:50:40.000000Z\"},{\"id\":15,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - F\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"44\",\"unit_price\":\"178.68\",\"total_value\":\"1786.80\",\"description\":\"\",\"size_variant\":\"44 - F\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"10.00\",\"completed_qty\":\"10.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"10.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"10.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:50:40.000000Z\",\"updated_at\":\"2026-02-23T12:50:40.000000Z\"},{\"id\":16,\"production_receipt_id\":1,\"item_id\":1,\"item_code\":\"1001 - H\\/S\",\"item_name\":\"Checked Sleeve\",\"art_no\":null,\"size\":\"44\",\"unit_price\":\"138.96\",\"total_value\":\"2084.40\",\"description\":\"\",\"size_variant\":\"44 - H\\/S\",\"uom_id\":1,\"uom_code\":\"PCS\",\"ordered_qty\":\"15.00\",\"completed_qty\":\"15.00\",\"qty_already_received\":\"0.00\",\"scan_qty\":\"15.00\",\"damage_qty\":\"0.00\",\"qty_to_receive\":\"15.00\",\"balance_qty\":\"0.00\",\"created_at\":\"2026-02-23T12:50:40.000000Z\",\"updated_at\":\"2026-02-23T12:50:40.000000Z\"}]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-23 12:50:40'),
(42, 1, 'update', 'Job Card Entry', 'job_card_entries', 1, '{\"id\":1,\"job_card_no\":\"JC001\",\"reference_no\":\"JC001\",\"purchase_order_id\":1,\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":null,\"patti_type_id\":null,\"collar_type_id\":null,\"cuff_type_id\":null,\"pocket_type_id\":null,\"bottom_cut_id\":null,\"brand_id\":1,\"brand_category_id\":1,\"item_id\":1,\"season_id\":null,\"process_group_id\":1,\"size_ratio_id\":1,\"job_card_date\":\"2026-02-23\",\"delivery_date\":\"2026-03-02\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"40.00\",\"total_qty_hs\":\"60.00\",\"grand_total_qty\":\"100.00\",\"average\":\"8.30\",\"remarks\":null,\"status\":\"Production Completed\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:42:11.000000Z\",\"updated_at\":\"2026-02-23T12:42:14.000000Z\",\"deleted_at\":null,\"issue_items\":[]}', '{\"id\":1,\"job_card_no\":\"JC001\",\"reference_no\":\"JC001\",\"purchase_order_id\":1,\"service_provider_id\":1,\"issue_store_id\":1,\"receipt_store_id\":1,\"fit_id\":null,\"patti_type_id\":null,\"collar_type_id\":null,\"cuff_type_id\":null,\"pocket_type_id\":null,\"bottom_cut_id\":null,\"brand_id\":1,\"brand_category_id\":1,\"item_id\":1,\"season_id\":null,\"process_group_id\":1,\"size_ratio_id\":1,\"job_card_date\":\"2026-02-23\",\"delivery_date\":\"2026-03-02\",\"washing\":\"No\",\"width\":null,\"fs_qty\":null,\"hs_qty\":null,\"ex_1_label\":null,\"ex_2_label\":null,\"price_fs\":null,\"price_hs\":null,\"total_qty_fs\":\"40.00\",\"total_qty_hs\":\"60.00\",\"grand_total_qty\":\"100.00\",\"average\":\"8.30\",\"remarks\":null,\"status\":\"Production Completed\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:42:11.000000Z\",\"updated_at\":\"2026-02-25T08:48:51.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-25 08:48:51'),
(43, 1, 'update_status', 'User', 'users', 2, '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":null,\"name\":\"Usha\",\"phone\":\"8585858585\",\"email\":\"usha@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-23T09:38:53.000000Z\",\"updated_at\":\"2026-02-23T09:41:53.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":null,\"name\":\"Usha\",\"phone\":\"8585858585\",\"email\":\"usha@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-23T09:38:53.000000Z\",\"updated_at\":\"2026-02-26T05:13:16.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Inactive\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 05:13:16'),
(44, 1, 'update_status', 'User', 'users', 2, '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":null,\"name\":\"Usha\",\"phone\":\"8585858585\",\"email\":\"usha@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-23T09:38:53.000000Z\",\"updated_at\":\"2026-02-26T05:13:16.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Inactive\",\"deleted_at\":null}', '{\"id\":2,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1001\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":null,\"name\":\"Usha\",\"phone\":\"8585858585\",\"email\":\"usha@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-23T09:38:53.000000Z\",\"updated_at\":\"2026-02-26T05:56:19.000000Z\",\"date_of_joining\":null,\"father_name\":null,\"father_phone\":null,\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"Jaihindpuram\",\"address_line2\":null,\"zipcode\":null,\"contact_person_name\":null,\"contact_person_phone\":null,\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":null,\"bank_name\":null,\"ifsc_code\":null,\"profile_image\":null,\"esi_document\":null,\"pf_document\":null,\"aadhaar_document\":null,\"pan_document\":null,\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 05:56:19');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(45, 1, 'create', 'User', 'users', 3, NULL, '{\"id\":3,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1002\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":7,\"name\":\"Krithika\",\"phone\":\"8520630369\",\"email\":\"krithika23@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-26T06:56:45.000000Z\",\"updated_at\":\"2026-02-26T06:56:46.000000Z\",\"date_of_joining\":\"2024-02-26\",\"father_name\":\"Iyyappan K\",\"father_phone\":\"9632569632\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Kamarajar Salai\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Annamalai\",\"contact_person_phone\":\"8967410025\",\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"123456789012\",\"bank_name\":\"State Bank of India\",\"ifsc_code\":\"SBIN0001234\",\"profile_image\":\"profile.jpg\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.png\",\"pan_document\":\"pan_document.png\",\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 06:56:46'),
(46, 1, 'update', 'User', 'users', 3, '{\"id\":3,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1002\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":7,\"name\":\"Krithika\",\"phone\":\"8520630369\",\"email\":\"krithika23@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-26T06:56:45.000000Z\",\"updated_at\":\"2026-02-26T06:56:46.000000Z\",\"date_of_joining\":\"2024-02-26\",\"father_name\":\"Iyyappan K\",\"father_phone\":\"9632569632\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Kamarajar Salai\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Annamalai\",\"contact_person_phone\":\"8967410025\",\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"123456789012\",\"bank_name\":\"State Bank of India\",\"ifsc_code\":\"SBIN0001234\",\"profile_image\":\"profile.jpg\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.png\",\"pan_document\":\"pan_document.png\",\"status\":\"Active\",\"deleted_at\":null}', '{\"id\":3,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1002\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":7,\"name\":\"Krithika\",\"phone\":\"8520630369\",\"email\":\"krithika23@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-26T06:56:45.000000Z\",\"updated_at\":\"2026-02-26T07:03:10.000000Z\",\"date_of_joining\":\"2024-02-26\",\"father_name\":\"Iyyappan K\",\"father_phone\":\"9632569632\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Kamarajar Salai\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Annamalai\",\"contact_person_phone\":\"8967410025\",\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"123456789012\",\"bank_name\":\"State Bank of India\",\"ifsc_code\":\"SBIN0001234\",\"profile_image\":\"profile.jpg\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.png\",\"pan_document\":\"pan_document.png\",\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 07:03:10'),
(47, 1, 'update', 'User', 'users', 3, '{\"id\":3,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1002\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":7,\"name\":\"Krithika\",\"phone\":\"8520630369\",\"email\":\"krithika23@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-26T06:56:45.000000Z\",\"updated_at\":\"2026-02-26T07:03:10.000000Z\",\"date_of_joining\":\"2024-02-26\",\"father_name\":\"Iyyappan K\",\"father_phone\":\"9632569632\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Kamarajar Salai\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Annamalai\",\"contact_person_phone\":\"8967410025\",\"contact_person_email\":null,\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"123456789012\",\"bank_name\":\"State Bank of India\",\"ifsc_code\":\"SBIN0001234\",\"profile_image\":\"profile.jpg\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.png\",\"pan_document\":\"pan_document.png\",\"status\":\"Active\",\"deleted_at\":null}', '{\"id\":3,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1002\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":7,\"name\":\"Krithika\",\"phone\":\"8520630369\",\"email\":\"krithika23@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-26T06:56:45.000000Z\",\"updated_at\":\"2026-02-26T07:03:52.000000Z\",\"date_of_joining\":\"2024-02-26\",\"father_name\":\"Iyyappan K\",\"father_phone\":\"9632569632\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Kamarajar Salai\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Annamalai\",\"contact_person_phone\":\"8967410025\",\"contact_person_email\":\"annamalai23@gmail.com\",\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"123456789012\",\"bank_name\":\"State Bank of India\",\"ifsc_code\":\"SBIN0001234\",\"profile_image\":\"profile.jpg\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.png\",\"pan_document\":\"pan_document.png\",\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 07:03:52'),
(48, 1, 'create', 'State', 'states', 2, NULL, '{\"id\":2,\"state_code\":\"AP\",\"state_name\":\"Andhra Pradesh\",\"status\":\"Active\",\"created_at\":\"2026-02-26T07:08:55.000000Z\",\"updated_at\":\"2026-02-26T07:08:55.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 07:08:55'),
(49, 1, 'create', 'City', 'cities', 2, NULL, '{\"id\":2,\"state_id\":1,\"city_name\":\"Chennai\",\"city_code\":\"CH\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-26T07:12:25.000000Z\",\"updated_at\":\"2026-02-26T07:12:25.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 07:12:25'),
(50, 1, 'create', 'Place', 'places', 2, NULL, '{\"id\":2,\"state_id\":1,\"city_id\":1,\"place_name\":\"Anna Nagar\",\"place_type\":\"Commercial\",\"latitude\":null,\"longitude\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-26T08:05:36.000000Z\",\"updated_at\":\"2026-02-26T08:05:36.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:05:36'),
(51, 1, 'create', 'UOM', 'uoms', 3, NULL, '{\"id\":3,\"uom_code\":\"G\",\"uom_name\":\"Gram\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-26T08:07:26.000000Z\",\"updated_at\":\"2026-02-26T08:07:26.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:07:26'),
(52, 1, 'create', 'Color', 'colors', 1, NULL, '{\"id\":1,\"color_name\":\"Red\",\"description\":null,\"created_at\":\"2026-02-26T08:09:04.000000Z\",\"updated_at\":\"2026-02-26T08:09:04.000000Z\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:09:04'),
(53, 1, 'create', 'Operation Stage', 'operation_stages', 2, NULL, '{\"operation_stage_name\":\"Stitching\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-26T08:11:36.000000Z\",\"created_at\":\"2026-02-26T08:11:36.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:11:36'),
(54, 1, 'create', 'Zone', 'zones', 1, NULL, '{\"zone_name\":\"South\",\"state_id\":\"1\",\"city_ids\":\"1\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-26T08:13:12.000000Z\",\"created_at\":\"2026-02-26T08:13:12.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:13:12'),
(55, 1, 'update_status', 'Size Ratio Status', 'size_ratios', 1, '{\"id\":1,\"size\":\"38,40,42,44\",\"ratio\":\"9,5,4,6\",\"status\":\"Active\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:32:50.000000Z\",\"updated_at\":\"2026-02-23T09:32:50.000000Z\"}', '{\"id\":1,\"size\":\"38,40,42,44\",\"ratio\":\"9,5,4,6\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:32:50.000000Z\",\"updated_at\":\"2026-02-26T08:16:49.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:16:49'),
(56, 1, 'update_status', 'Size Ratio Status', 'size_ratios', 1, '{\"id\":1,\"size\":\"38,40,42,44\",\"ratio\":\"9,5,4,6\",\"status\":\"Inactive\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:32:50.000000Z\",\"updated_at\":\"2026-02-26T08:16:49.000000Z\"}', '{\"id\":1,\"size\":\"38,40,42,44\",\"ratio\":\"9,5,4,6\",\"status\":\"Active\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:32:50.000000Z\",\"updated_at\":\"2026-02-26T08:16:51.000000Z\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:16:51'),
(57, 1, 'create', 'Fabric Type', 'fabric_types', 1, NULL, '{\"fabric_type\":\"Plain\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-26T08:17:43.000000Z\",\"created_at\":\"2026-02-26T08:17:43.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:17:43'),
(58, 1, 'create', 'Fabric Type', 'fabric_types', 2, NULL, '{\"fabric_type\":\"Checked\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-26T08:17:52.000000Z\",\"created_at\":\"2026-02-26T08:17:52.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:17:52'),
(59, 1, 'create', 'Fabric Type', 'fabric_types', 3, NULL, '{\"fabric_type\":\"Print\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-26T08:17:59.000000Z\",\"created_at\":\"2026-02-26T08:17:59.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:17:59'),
(60, 1, 'create', 'Store Location', 'store_locations', 2, NULL, '{\"store_location\":\"S2\",\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-26T08:21:16.000000Z\",\"created_at\":\"2026-02-26T08:21:16.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:21:16'),
(61, 1, 'create', 'Department', 'departments', 2, NULL, '{\"department\":\"Stitching Department\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:22:13'),
(62, 1, 'delete', 'Fabric Type', 'fabric_types', 1, '{\"id\":1,\"fabric_type\":\"Plain\",\"status\":\"Active\",\"created_at\":\"2026-02-26T08:17:43.000000Z\",\"updated_at\":\"2026-02-26T08:17:43.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:23:07'),
(63, 1, 'delete', 'Fabric Type', 'fabric_types', 3, '{\"id\":3,\"fabric_type\":\"Print\",\"status\":\"Active\",\"created_at\":\"2026-02-26T08:17:59.000000Z\",\"updated_at\":\"2026-02-26T08:17:59.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:23:10'),
(64, 1, 'update', 'Fabric Type', 'fabric_types', 2, '{\"id\":2,\"fabric_type\":\"Checked\",\"status\":\"Active\",\"created_at\":\"2026-02-26T08:17:52.000000Z\",\"updated_at\":\"2026-02-26T08:17:52.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":null}', '{\"id\":2,\"fabric_type\":\"Cotton\",\"status\":\"Active\",\"created_at\":\"2026-02-26T08:17:52.000000Z\",\"updated_at\":\"2026-02-26T08:23:16.000000Z\",\"deleted_at\":null,\"created_by\":1,\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:23:16'),
(65, 1, 'create', 'Style', 'styles', 1, NULL, '{\"style_name\":\"Plain\",\"code\":\"PLN\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:25:10'),
(66, 1, 'create', 'Store Type', 'store_types', 2, NULL, '{\"store_type_name\":\"Finished Goods\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:31:29'),
(67, 1, 'create', 'Fit', 'fits', 1, NULL, '{\"fit_name\":\"Cross\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:34:54'),
(68, 1, 'update', 'Fit', 'fits', 1, NULL, '{\"fit_name\":\"Cross\",\"status\":\"Active\",\"updated_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:35:02'),
(69, 1, 'create', 'Patti Type', 'patti_types', 1, NULL, '{\"patti_type_name\":\"Cross\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:36:34'),
(70, 1, 'create', 'Collar Type', 'collar_types', 1, NULL, '{\"collar_type_name\":\"Cross\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:37:15'),
(71, 1, 'create', 'Season', 'seasons', 1, NULL, '{\"name\":\"Summer\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:41:29'),
(72, 1, 'create', 'Shift', 'shifts', 1, NULL, '{\"shift_name\":\"I\",\"start_time\":\"09:00\",\"end_time\":\"19:00\",\"status\":\"Active\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:47:01'),
(73, 1, 'create', 'Production Service', 'production_services', 2, NULL, '{\"service_name\":\"Fabric Spreading\",\"service_code\":\"FAB-SPRD\",\"operation_stage_id\":\"2\",\"status\":\"Active\",\"applies_to\":\"ALL\",\"base_quantity_source\":\"Total Qty\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 08:49:24'),
(74, 1, 'update', 'User', 'users', 3, '{\"id\":3,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1002\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":7,\"name\":\"Krithika\",\"phone\":\"8520630369\",\"email\":\"krithika23@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-26T06:56:45.000000Z\",\"updated_at\":\"2026-02-26T07:03:52.000000Z\",\"date_of_joining\":\"2024-02-26\",\"father_name\":\"Iyyappan K\",\"father_phone\":\"9632569632\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Kamarajar Salai\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Annamalai\",\"contact_person_phone\":\"8967410025\",\"contact_person_email\":\"annamalai23@gmail.com\",\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"123456789012\",\"bank_name\":\"State Bank of India\",\"ifsc_code\":\"SBIN0001234\",\"profile_image\":\"profile.jpg\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.png\",\"pan_document\":\"pan_document.png\",\"status\":\"Active\",\"deleted_at\":null}', '{\"id\":3,\"service_provider_id\":1,\"operation_stage_id\":1,\"emp_id\":\"1002\",\"department_id\":1,\"role_id\":5,\"blood_group_id\":7,\"name\":\"Krithika\",\"phone\":\"8520630369\",\"email\":\"krithika23@gmail.com\",\"email_verified_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-26T06:56:45.000000Z\",\"updated_at\":\"2026-02-26T07:03:52.000000Z\",\"date_of_joining\":\"2024-02-26\",\"father_name\":\"Iyyappan K\",\"father_phone\":\"9632569632\",\"country\":null,\"state_id\":1,\"city_id\":1,\"address_line1\":\"25, Kamarajar Salai\",\"address_line2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Annamalai\",\"contact_person_phone\":\"8967410025\",\"contact_person_email\":\"annamalai23@gmail.com\",\"basic_salary\":null,\"hra\":null,\"allowances\":null,\"deductions\":null,\"gross_salary\":null,\"net_salary\":null,\"account_number\":\"123456789012\",\"bank_name\":\"State Bank of India\",\"ifsc_code\":\"SBIN0001234\",\"profile_image\":\"profile.jpg\",\"esi_document\":\"esi_document.pdf\",\"pf_document\":\"pf_document.pdf\",\"aadhaar_document\":\"aadhaar_document.png\",\"pan_document\":\"pan_document.png\",\"status\":\"Active\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 09:02:37'),
(75, 1, 'create', 'Customer', 'customers', 1, NULL, '{\"category\":\"Wholesaler\",\"name\":\"AK Ahamed & Co\",\"code\":\"1000\",\"mobile_no\":\"8220055143\",\"email\":\"aaykaymdu@yahoo.co.in\",\"website_url\":null,\"transport_name\":\"Thirumalammal Lorry Booking Office\",\"booking_office\":null,\"zone_id\":\"1\",\"store_id\":\"1\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"1\",\"address_line_1\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":\"Sai\",\"designation\":\"Employeer\",\"contact_mobile_no\":\"9698520147\",\"contact_email\":\"sai89@gmail.com\",\"tax_type_id\":null,\"gst_no\":\"33AADFA4747M1ZD\",\"pan_no\":\"AADFA4747M\",\"payment_terms\":\"Advance Payment\",\"credit_limit\":0,\"sales_discount\":0,\"box_discount\":\"10\",\"bank_name\":\"State Bank of India\",\"branch\":\"Anna Nagar\",\"account_number\":\"123456789012\",\"ifsc_code\":\"SBIN0001234\",\"created_by\":1,\"updated_at\":\"2026-02-26T09:03:02.000000Z\",\"created_at\":\"2026-02-26T09:03:02.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 09:03:02'),
(76, 1, 'update', 'Customer', 'customers', 1, '{\"id\":1,\"category\":\"Wholesaler\",\"name\":\"AK Ahamed & Co\",\"code\":\"1000\",\"mobile_no\":\"8220055143\",\"email\":\"aaykaymdu@yahoo.co.in\",\"website_url\":null,\"transport_name\":\"Thirumalammal Lorry Booking Office\",\"booking_office\":null,\"zone_id\":1,\"store_id\":1,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"state_id\":1,\"city_id\":1,\"place_id\":1,\"address_line_1\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":\"Sai\",\"designation\":\"Employeer\",\"contact_mobile_no\":\"9698520147\",\"contact_email\":\"sai89@gmail.com\",\"tax_type_id\":null,\"gst_no\":\"33AADFA4747M1ZD\",\"pan_no\":\"AADFA4747M\",\"payment_terms\":\"Advance Payment\",\"credit_limit\":\"0.00\",\"sales_discount\":\"0.00\",\"box_discount\":\"10.00\",\"bank_name\":\"State Bank of India\",\"branch\":\"Anna Nagar\",\"account_number\":\"123456789012\",\"ifsc_code\":\"SBIN0001234\",\"created_at\":\"2026-02-26T09:03:02.000000Z\",\"updated_at\":\"2026-02-26T09:03:02.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"category\":\"Wholesaler\",\"name\":\"AK Ahamed & Co\",\"code\":\"1000\",\"mobile_no\":\"8220055143\",\"email\":\"aaykaymdu@yahoo.co.in\",\"website_url\":null,\"transport_name\":\"Thirumalammal Lorry Booking Office\",\"booking_office\":null,\"zone_id\":1,\"store_id\":1,\"status\":\"Active\",\"created_by\":1,\"updated_by\":1,\"state_id\":1,\"city_id\":1,\"place_id\":1,\"address_line_1\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":\"Sai\",\"designation\":\"Employeer\",\"contact_mobile_no\":\"9698520147\",\"contact_email\":\"sai89@gmail.com\",\"tax_type_id\":null,\"gst_no\":\"33AADFA4747M1ZD\",\"pan_no\":\"AADFA4747M\",\"payment_terms\":\"Advance Payment\",\"credit_limit\":\"2.00\",\"sales_discount\":\"1.00\",\"box_discount\":\"10.00\",\"bank_name\":\"State Bank of India\",\"branch\":\"Anna Nagar\",\"account_number\":\"123456789012\",\"ifsc_code\":\"SBIN0001234\",\"created_at\":\"2026-02-26T09:03:02.000000Z\",\"updated_at\":\"2026-02-26T09:03:28.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 09:03:28'),
(77, 1, 'update_status', 'Customer Status', 'customers', 1, '{\"id\":1,\"category\":\"Wholesaler\",\"name\":\"AK Ahamed & Co\",\"code\":\"1000\",\"mobile_no\":\"8220055143\",\"email\":\"aaykaymdu@yahoo.co.in\",\"website_url\":null,\"transport_name\":\"Thirumalammal Lorry Booking Office\",\"booking_office\":null,\"zone_id\":1,\"store_id\":1,\"status\":\"Active\",\"created_by\":1,\"updated_by\":1,\"state_id\":1,\"city_id\":1,\"place_id\":1,\"address_line_1\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":\"Sai\",\"designation\":\"Employeer\",\"contact_mobile_no\":\"9698520147\",\"contact_email\":\"sai89@gmail.com\",\"tax_type_id\":null,\"gst_no\":\"33AADFA4747M1ZD\",\"pan_no\":\"AADFA4747M\",\"payment_terms\":\"Advance Payment\",\"credit_limit\":\"2.00\",\"sales_discount\":\"1.00\",\"box_discount\":\"10.00\",\"bank_name\":\"State Bank of India\",\"branch\":\"Anna Nagar\",\"account_number\":\"123456789012\",\"ifsc_code\":\"SBIN0001234\",\"created_at\":\"2026-02-26T09:03:02.000000Z\",\"updated_at\":\"2026-02-26T09:03:28.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"category\":\"Wholesaler\",\"name\":\"AK Ahamed & Co\",\"code\":\"1000\",\"mobile_no\":\"8220055143\",\"email\":\"aaykaymdu@yahoo.co.in\",\"website_url\":null,\"transport_name\":\"Thirumalammal Lorry Booking Office\",\"booking_office\":null,\"zone_id\":1,\"store_id\":1,\"status\":\"Inactive\",\"created_by\":1,\"updated_by\":1,\"state_id\":1,\"city_id\":1,\"place_id\":1,\"address_line_1\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":\"Sai\",\"designation\":\"Employeer\",\"contact_mobile_no\":\"9698520147\",\"contact_email\":\"sai89@gmail.com\",\"tax_type_id\":null,\"gst_no\":\"33AADFA4747M1ZD\",\"pan_no\":\"AADFA4747M\",\"payment_terms\":\"Advance Payment\",\"credit_limit\":\"2.00\",\"sales_discount\":\"1.00\",\"box_discount\":\"10.00\",\"bank_name\":\"State Bank of India\",\"branch\":\"Anna Nagar\",\"account_number\":\"123456789012\",\"ifsc_code\":\"SBIN0001234\",\"created_at\":\"2026-02-26T09:03:02.000000Z\",\"updated_at\":\"2026-02-26T09:36:30.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 09:36:30'),
(78, 1, 'update_status', 'Customer Status', 'customers', 1, '{\"id\":1,\"category\":\"Wholesaler\",\"name\":\"AK Ahamed & Co\",\"code\":\"1000\",\"mobile_no\":\"8220055143\",\"email\":\"aaykaymdu@yahoo.co.in\",\"website_url\":null,\"transport_name\":\"Thirumalammal Lorry Booking Office\",\"booking_office\":null,\"zone_id\":1,\"store_id\":1,\"status\":\"Inactive\",\"created_by\":1,\"updated_by\":1,\"state_id\":1,\"city_id\":1,\"place_id\":1,\"address_line_1\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":\"Sai\",\"designation\":\"Employeer\",\"contact_mobile_no\":\"9698520147\",\"contact_email\":\"sai89@gmail.com\",\"tax_type_id\":null,\"gst_no\":\"33AADFA4747M1ZD\",\"pan_no\":\"AADFA4747M\",\"payment_terms\":\"Advance Payment\",\"credit_limit\":\"2.00\",\"sales_discount\":\"1.00\",\"box_discount\":\"10.00\",\"bank_name\":\"State Bank of India\",\"branch\":\"Anna Nagar\",\"account_number\":\"123456789012\",\"ifsc_code\":\"SBIN0001234\",\"created_at\":\"2026-02-26T09:03:02.000000Z\",\"updated_at\":\"2026-02-26T09:36:30.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"category\":\"Wholesaler\",\"name\":\"AK Ahamed & Co\",\"code\":\"1000\",\"mobile_no\":\"8220055143\",\"email\":\"aaykaymdu@yahoo.co.in\",\"website_url\":null,\"transport_name\":\"Thirumalammal Lorry Booking Office\",\"booking_office\":null,\"zone_id\":1,\"store_id\":1,\"status\":\"Active\",\"created_by\":1,\"updated_by\":1,\"state_id\":1,\"city_id\":1,\"place_id\":1,\"address_line_1\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":\"Sai\",\"designation\":\"Employeer\",\"contact_mobile_no\":\"9698520147\",\"contact_email\":\"sai89@gmail.com\",\"tax_type_id\":null,\"gst_no\":\"33AADFA4747M1ZD\",\"pan_no\":\"AADFA4747M\",\"payment_terms\":\"Advance Payment\",\"credit_limit\":\"2.00\",\"sales_discount\":\"1.00\",\"box_discount\":\"10.00\",\"bank_name\":\"State Bank of India\",\"branch\":\"Anna Nagar\",\"account_number\":\"123456789012\",\"ifsc_code\":\"SBIN0001234\",\"created_at\":\"2026-02-26T09:03:02.000000Z\",\"updated_at\":\"2026-02-26T09:36:32.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 09:36:32'),
(79, 1, 'create', 'Supplier', 'suppliers', 2, NULL, '{\"name\":\"TAJ DISTRIBUTORS\",\"code\":\"1001\",\"mobile_no\":\"9965221102\",\"email\":\"taj323@gmail.com\",\"website_url\":\"https:\\/\\/www.tajinternationalexports.in\\/\",\"transport_name\":null,\"booking_area\":null,\"stores\":null,\"store_id\":\"2\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"1\",\"address_line_1\":\"No. 19, 2nd Floor, West Masi Street,\",\"address_line_2\":null,\"address_line_3\":null,\"zip_code\":\"625011\",\"contact_person_name\":\"Naaven\",\"designation\":\"Employee\",\"contact_mobile_no\":\"9696985201\",\"contact_email\":\"naveen@gmail.com\",\"purchase_commission_agent_id\":null,\"commission_percentage\":0,\"tax_id\":null,\"gst_no\":\"33ACGPF5198L1ZB\",\"pan_no\":\"ACGPF5198L\",\"ecc_no\":\"0415038723\",\"credit_limit\":\"5\",\"payment_terms\":\"Net 30 \\/ Net 45 days from invoice (subject to credit approval)\",\"bank_name\":\"State Bank of India\",\"branch\":\"Anna Nagar\",\"account_number\":\"123456789012\",\"ifsc_code\":\"SBIN0001233\",\"created_by\":1,\"updated_at\":\"2026-02-26T09:49:51.000000Z\",\"created_at\":\"2026-02-26T09:49:51.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 09:49:51'),
(80, 1, 'create', 'Service Provider', 'service_providers', 2, NULL, '{\"operation_stage_id\":\"2\",\"name\":\"Samayanallur Unit\",\"code\":\"SMLR\",\"is_plant\":1,\"email\":null,\"mobile_no\":\"6985232541\",\"zip_code\":\"625011\",\"website_url\":null,\"service_rate\":\"Per Agent\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"2\",\"address_line_1\":\"12, Samayanallur\",\"address_line_2\":null,\"contact_person_name\":null,\"designation\":null,\"phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"remarks\":null,\"bank_name\":null,\"bank_acc_no\":null,\"ifsc_code\":null,\"payment_terms\":null,\"created_by\":1,\"updated_at\":\"2026-02-26T10:06:53.000000Z\",\"created_at\":\"2026-02-26T10:06:53.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 10:06:53'),
(81, 1, 'update', 'Service Provider', 'service_providers', 2, '{\"id\":2,\"operation_stage_id\":2,\"name\":\"Samayanallur Unit\",\"code\":\"SMLR\",\"is_plant\":1,\"email\":null,\"mobile_no\":\"6985232541\",\"zip_code\":\"625011\",\"website_url\":null,\"service_rate\":\"Per Agent\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"state_id\":1,\"city_id\":1,\"place_id\":2,\"address_line_1\":\"12, Samayanallur\",\"address_line_2\":null,\"contact_person_name\":null,\"designation\":null,\"phone_number\":null,\"contact_email\":null,\"pan_no\":null,\"gst_no\":null,\"remarks\":null,\"bank_name\":null,\"bank_acc_no\":null,\"ifsc_code\":null,\"payment_terms\":null,\"created_at\":\"2026-02-26T10:06:53.000000Z\",\"updated_at\":\"2026-02-26T10:06:53.000000Z\",\"deleted_at\":null}', '{\"id\":2,\"operation_stage_id\":\"2\",\"name\":\"Samayanallur Unit\",\"code\":\"SMLR\",\"is_plant\":1,\"email\":null,\"mobile_no\":\"6985232541\",\"zip_code\":\"625011\",\"website_url\":null,\"service_rate\":\"Per Agent\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":1,\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"2\",\"address_line_1\":\"12, Samayanallur\",\"address_line_2\":null,\"contact_person_name\":\"Priya\",\"designation\":\"Employee\",\"phone_number\":\"93938383832\",\"contact_email\":\"priyaarjun@gmail.com\",\"pan_no\":\"AADFA4747M\",\"gst_no\":\"33AADFA4747M1ZD\",\"remarks\":null,\"bank_name\":\"State Bank of India\",\"bank_acc_no\":\"123456789012\",\"ifsc_code\":\"SBIN0001234\",\"payment_terms\":\"New customers may require part\\/100% upfront payment\",\"created_at\":\"2026-02-26T10:06:53.000000Z\",\"updated_at\":\"2026-02-26T10:10:06.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 10:10:06'),
(82, 1, 'create', 'Sales Agent', 'sales_agents', 1, NULL, '{\"agent_type\":\"Direct Sales Agent\",\"name\":\"Akshan\",\"code\":\"1001\",\"email\":\"akshan@gmail.com\",\"mobile_no\":\"6568932140\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"2\",\"address_line_1\":\"25, West Street\",\"address_line_2\":null,\"zip_code\":null,\"contact_person_name\":\"Vinoth\",\"designation\":\"Employee\",\"contact_phone_number\":\"9292938448\",\"contact_email\":\"vinoth34@gmail.com\",\"pan_no\":\"AADFA4747M\",\"gst_no\":\"33AADFA4747M1ZD\",\"commission_value\":\"3\",\"sales_target\":\"3\",\"created_by\":1,\"updated_at\":\"2026-02-26T10:20:10.000000Z\",\"created_at\":\"2026-02-26T10:20:10.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 10:20:10'),
(83, 1, 'update', 'Sales Agent', 'sales_agents', 1, '{\"id\":1,\"agent_type\":\"Direct Sales Agent\",\"name\":\"Akshan\",\"code\":\"1001\",\"email\":\"akshan@gmail.com\",\"mobile_no\":\"6568932140\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"state_id\":1,\"city_id\":1,\"place_id\":2,\"address_line_1\":\"25, West Street\",\"address_line_2\":null,\"zip_code\":null,\"contact_person_name\":\"Vinoth\",\"designation\":\"Employee\",\"contact_phone_number\":\"9292938448\",\"contact_email\":\"vinoth34@gmail.com\",\"pan_no\":\"AADFA4747M\",\"gst_no\":\"33AADFA4747M1ZD\",\"commission_value\":\"3.00\",\"sales_target\":\"3.00\",\"created_at\":\"2026-02-26T10:20:10.000000Z\",\"updated_at\":\"2026-02-26T10:20:10.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"agent_type\":\"Direct Sales Agent\",\"name\":\"Akshan\",\"code\":\"1001\",\"email\":\"akshan@gmail.com\",\"mobile_no\":\"6568932140\",\"status\":\"Active\",\"created_by\":1,\"updated_by\":1,\"state_id\":1,\"city_id\":1,\"place_id\":2,\"address_line_1\":\"25, West Street\",\"address_line_2\":null,\"zip_code\":\"625011\",\"contact_person_name\":\"Vinoth\",\"designation\":\"Employee\",\"contact_phone_number\":\"9292938448\",\"contact_email\":\"vinoth34@gmail.com\",\"pan_no\":\"AADFA4747M\",\"gst_no\":\"33AADFA4747M1ZD\",\"commission_value\":\"3.00\",\"sales_target\":\"3.00\",\"created_at\":\"2026-02-26T10:20:10.000000Z\",\"updated_at\":\"2026-02-26T10:20:36.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 10:20:36'),
(84, 1, 'create', 'Purchase Commission Agent', 'purchase_commission_agents', 1, NULL, '{\"name\":\"Shyam\",\"code\":\"1001\",\"email\":\"shyam@gmail.com\",\"mobile_no\":\"9192394949\",\"status\":\"Active\",\"state_id\":\"1\",\"city_id\":\"1\",\"place_id\":\"2\",\"address_line_1\":\"12, Bye pass road\",\"address_line_2\":null,\"zipcode\":\"625011\",\"contact_person_name\":\"Eswar\",\"designation\":\"Employee\",\"phone_number\":\"9632587400\",\"contact_email\":\"eswar@gmail.com\",\"pan_no\":\"AADFA4747M\",\"gst_no\":\"33AADFA4747M1ZD\",\"remarks\":null,\"created_by\":1,\"updated_at\":\"2026-02-26T11:12:38.000000Z\",\"created_at\":\"2026-02-26T11:12:38.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-26 11:12:38'),
(85, 1, 'update', 'Setting', 'settings', 1, '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"ushadevi.saitech@gmail.com\",\"logo\":null,\"phone_number\":\"9856355632\",\"toll_free_no\":null,\"state_id\":1,\"city_id\":1,\"address\":\"Jaihindpuram\",\"cgst\":18,\"sgst\":9,\"igst\":9,\"pan_no\":null,\"gst_no\":null,\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-02-20T08:49:21.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"ushadevi.saitech@gmail.com\",\"logo\":null,\"phone_number\":\"9856355632\",\"toll_free_no\":null,\"state_id\":1,\"city_id\":1,\"address\":\"Jaihindpuram\",\"cgst\":9,\"sgst\":9,\"igst\":18,\"pan_no\":null,\"gst_no\":null,\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-02-27T04:39:49.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-27 04:39:49'),
(86, 1, 'create', 'Sale Order', 'sale_orders', 1, NULL, '{\"so_no\":\"SO-001\",\"so_date\":\"2026-02-26T18:30:00.000000Z\",\"request_date\":\"2026-02-26T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"status\":\"Draft\",\"total_qty\":\"1.00\",\"sub_total_qty\":\"178.68\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"178.68\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"32.16\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"210.84\",\"internal_remarks\":null,\"created_by\":1,\"updated_at\":\"2026-02-27T09:52:31.000000Z\",\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-27 09:52:31'),
(87, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-26T18:30:00.000000Z\",\"request_date\":\"2026-02-26T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"status\":\"Draft\",\"total_qty\":\"1.00\",\"sub_total_qty\":\"178.68\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"178.68\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"32.16\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"210.00\",\"internal_remarks\":null,\"attachment\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-27T11:42:08.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-27 11:42:08'),
(88, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-26T18:30:00.000000Z\",\"request_date\":\"2026-02-26T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"status\":\"Draft\",\"total_qty\":\"2.00\",\"sub_total_qty\":\"357.36\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"357.36\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"64.32\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"420.84\",\"internal_remarks\":null,\"attachment\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-27T12:22:27.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-27 12:22:27'),
(89, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-26T18:30:00.000000Z\",\"request_date\":\"2026-02-26T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"status\":\"Draft\",\"total_qty\":\"2.00\",\"sub_total_qty\":\"357.36\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"357.36\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"64.32\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"420.84\",\"internal_remarks\":null,\"attachment\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-27T12:22:27.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-27 12:22:40'),
(90, 1, 'create', 'Item', 'items', 2, NULL, '{\"brand_id\":\"1\",\"brand_category_id\":\"1\",\"name\":\"Formal Lenin Shirt\",\"code\":\"FLS\",\"style_id\":\"1\",\"fabric_type_id\":\"2\",\"design_art_no\":null,\"uom_id\":\"1\",\"size_ratio_id\":\"1\",\"color_id\":[\"1\"],\"standard_costing\":null,\"store_category_id\":null,\"related_materials\":null,\"operation_stages\":null,\"service_providers\":{\"cutting\":null,\"stitching\":null},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"status\":\"Active\",\"created_by\":1,\"updated_at\":\"2026-02-27T12:44:59.000000Z\",\"created_at\":\"2026-02-27T12:44:59.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-27 12:44:59'),
(91, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-26T18:30:00.000000Z\",\"request_date\":\"2026-02-26T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"status\":\"Draft\",\"total_qty\":\"2.00\",\"sub_total_qty\":\"357.36\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"357.36\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"64.32\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"420.84\",\"internal_remarks\":null,\"attachment\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-27T12:22:27.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-27 13:11:31'),
(92, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-27T18:30:00.000000Z\",\"request_date\":\"2026-02-27T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Draft\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"317.64\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"57.18\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"373.98\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-28T04:58:00.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-28 04:58:00'),
(93, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-27T18:30:00.000000Z\",\"request_date\":\"2026-02-27T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Draft\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"317.64\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"57.18\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"373.98\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-28T05:01:49.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-28 05:01:49'),
(94, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-27T18:30:00.000000Z\",\"request_date\":\"2026-02-27T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Draft\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"317.64\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"57.18\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"373.98\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-28T05:01:49.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-28 06:02:10');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(95, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-27T18:30:00.000000Z\",\"request_date\":\"2026-02-27T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Draft\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"commission_percent\":\"2.00\",\"commission_amount\":\"6.35\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"317.64\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"57.18\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"373.98\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-28T06:22:59.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-28 06:22:59'),
(96, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-27T18:30:00.000000Z\",\"request_date\":\"2026-02-27T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Draft\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"commission_percent\":\"2.00\",\"commission_amount\":\"6.35\",\"discount_percent\":\"2.00\",\"discount_amount\":\"6.35\",\"taxable_amount\":\"311.29\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"56.03\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"366.48\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-28T06:23:18.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-28 06:23:18'),
(97, 1, 'update_status', 'Sale Order Status', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-27T18:30:00.000000Z\",\"request_date\":\"2026-02-27T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":1,\"customer_id\":1,\"customer_po_ref\":null,\"store_id\":1,\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":1,\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Approved\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"commission_percent\":\"2.00\",\"commission_amount\":\"6.35\",\"discount_percent\":\"2.00\",\"discount_amount\":\"6.35\",\"taxable_amount\":\"311.29\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"56.03\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"366.48\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-28T08:12:07.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-28 08:12:07'),
(98, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-27T18:30:00.000000Z\",\"request_date\":\"2026-02-27T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Approved\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"commission_percent\":\"2.00\",\"commission_amount\":\"6.35\",\"discount_percent\":\"2.00\",\"discount_amount\":\"6.35\",\"taxable_amount\":\"311.29\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"56.03\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"366.48\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-28T08:12:07.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-28 11:56:44'),
(99, 1, 'update', 'Sale Order', 'sale_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-02-27T18:30:00.000000Z\",\"request_date\":\"2026-02-27T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Approved\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"commission_percent\":\"2.00\",\"commission_amount\":\"6.35\",\"discount_percent\":\"2.00\",\"discount_amount\":\"6.35\",\"taxable_amount\":\"311.29\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"56.03\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"366.48\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-02-28T08:12:07.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-02-28 12:02:53'),
(100, 1, 'update', 'Sale Order', 'sales_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-03-01T18:30:00.000000Z\",\"request_date\":\"2026-03-01T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Approved\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"commission_percent\":\"2.00\",\"commission_amount\":\"6.35\",\"discount_percent\":\"2.00\",\"discount_amount\":\"6.35\",\"taxable_amount\":\"311.29\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"56.03\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"366.48\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-03-02T08:29:28.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-02 08:29:28'),
(101, 1, 'update', 'Sale Order', 'sales_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-03-01T18:30:00.000000Z\",\"request_date\":\"2026-03-01T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Approved\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"commission_percent\":\"2.00\",\"commission_amount\":\"6.35\",\"discount_percent\":\"2.00\",\"discount_amount\":\"6.35\",\"taxable_amount\":\"311.29\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"56.03\",\"round_off_type\":\"Less\",\"round_off\":\"0.84\",\"total_amount\":\"366.48\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-03-02T08:29:28.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-02 08:31:14'),
(102, 1, 'update_status', 'Sales Invoice Status', 'sales_invoices', 1, '{\"id\":1,\"inv_no\":\"SINV-0001\",\"inv_date\":\"2026-02-27T18:30:00.000000Z\",\"so_id\":1,\"customer_id\":1,\"delivery_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"remarks\":\"This is sales invoice\",\"invoice_status\":\"Unpaid\\/Credit\",\"payment_mode\":\"Bank (Cheque)\",\"extra_input\":\"0123456789\",\"due_date\":\"2026-02-27T18:30:00.000000Z\",\"notes\":null,\"signature_file\":\"uploads\\/sales_invoices\\/signatures\\/1772274057_sig_signature_images.jpg\",\"attachment_file\":\"uploads\\/sales_invoices\\/attachments\\/1772274057_att_pdf_image.jpg\",\"show_fields\":[\"amount\",\"discount\",\"subtotal\",\"grandtotal\"],\"sub_total\":\"317.64\",\"discount_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"317.64\",\"other_state\":false,\"igst\":\"0.00\",\"cgst\":\"28.59\",\"sgst\":\"28.59\",\"tax_amount\":\"57.18\",\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"other_charges\":\"0.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"grand_total\":\"374.82\",\"received_amount\":\"0.00\",\"due_amount\":\"374.82\",\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-02-28T10:13:30.000000Z\",\"updated_at\":\"2026-03-02T08:37:27.000000Z\",\"deleted_at\":null,\"items\":[{\"id\":17,\"sales_invoice_id\":1,\"brand_id\":1,\"item_id\":1,\"uom_id\":1,\"quantity\":\"1.00\",\"rate\":\"178.68\",\"mrp\":\"350.00\",\"amount\":\"178.68\",\"hsn_sac\":\"62053000\",\"art_no\":\"CF12345-1\",\"size\":\"40\",\"sleeve_type\":\"Full\",\"created_at\":\"2026-03-02T08:37:27.000000Z\",\"updated_at\":\"2026-03-02T08:37:27.000000Z\",\"deleted_at\":null},{\"id\":18,\"sales_invoice_id\":1,\"brand_id\":1,\"item_id\":1,\"uom_id\":1,\"quantity\":\"1.00\",\"rate\":\"138.96\",\"mrp\":\"350.00\",\"amount\":\"138.96\",\"hsn_sac\":\"62053000\",\"art_no\":\"CF12345-2\",\"size\":\"38\",\"sleeve_type\":\"Half\",\"created_at\":\"2026-03-02T08:37:27.000000Z\",\"updated_at\":\"2026-03-02T08:37:27.000000Z\",\"deleted_at\":null}]}', '{\"id\":1,\"inv_no\":\"SINV-0001\",\"inv_date\":\"2026-02-27T18:30:00.000000Z\",\"so_id\":1,\"customer_id\":1,\"delivery_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"remarks\":\"This is sales invoice\",\"invoice_status\":\"Partially Paid\",\"payment_mode\":\"Bank (Cheque)\",\"extra_input\":\"0123456789\",\"due_date\":\"2026-02-27T18:30:00.000000Z\",\"notes\":null,\"signature_file\":\"uploads\\/sales_invoices\\/signatures\\/1772274057_sig_signature_images.jpg\",\"attachment_file\":\"uploads\\/sales_invoices\\/attachments\\/1772274057_att_pdf_image.jpg\",\"show_fields\":[\"amount\",\"discount\",\"subtotal\",\"grandtotal\"],\"sub_total\":\"317.64\",\"discount_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"317.64\",\"other_state\":false,\"igst\":\"0.00\",\"cgst\":\"28.59\",\"sgst\":\"28.59\",\"tax_amount\":\"57.18\",\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"other_charges\":\"0.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"grand_total\":\"374.82\",\"received_amount\":\"0.00\",\"due_amount\":\"374.82\",\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-02-28T10:13:30.000000Z\",\"updated_at\":\"2026-03-02T08:38:02.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-02 08:38:02'),
(103, 1, 'update', 'Setting', 'settings', 1, '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"ushadevi.saitech@gmail.com\",\"logo\":null,\"phone_number\":\"9856355632\",\"toll_free_no\":null,\"state_id\":1,\"city_id\":1,\"address\":\"Jaihindpuram\",\"cgst\":9,\"sgst\":9,\"igst\":18,\"pan_no\":null,\"gst_no\":null,\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"so_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-02-27T04:39:49.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"ushadevi.saitech@gmail.com\",\"logo\":null,\"phone_number\":\"9856355632\",\"toll_free_no\":null,\"state_id\":1,\"city_id\":1,\"address\":\"272\\/2, Somu Nagar, Siringeri Nagar\\r\\n(Sarathambal Kovil Backside),\\r\\nByepass Road, Madurai - 625016\",\"cgst\":9,\"sgst\":9,\"igst\":18,\"pan_no\":null,\"gst_no\":null,\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"so_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-03-02T08:43:46.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-02 08:43:46'),
(104, 1, 'update_status', 'Sales Invoice Status', 'sales_invoices', 1, '{\"id\":1,\"inv_no\":\"SINV-0001\",\"inv_date\":\"2026-02-27T18:30:00.000000Z\",\"so_id\":1,\"customer_id\":1,\"delivery_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"remarks\":\"This is sales invoice\",\"invoice_status\":\"Partially Paid\",\"payment_mode\":\"Bank (Cheque)\",\"extra_input\":\"0123456789\",\"due_date\":\"2026-02-27T18:30:00.000000Z\",\"notes\":null,\"signature_file\":\"uploads\\/sales_invoices\\/signatures\\/1772274057_sig_signature_images.jpg\",\"attachment_file\":\"uploads\\/sales_invoices\\/attachments\\/1772274057_att_pdf_image.jpg\",\"show_fields\":[\"amount\",\"discount\",\"subtotal\",\"grandtotal\"],\"sub_total\":\"317.64\",\"discount_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"317.64\",\"other_state\":false,\"igst\":\"0.00\",\"cgst\":\"28.59\",\"sgst\":\"28.59\",\"tax_amount\":\"57.18\",\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"other_charges\":\"0.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"grand_total\":\"374.82\",\"received_amount\":\"0.00\",\"due_amount\":\"374.82\",\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-02-28T10:13:30.000000Z\",\"updated_at\":\"2026-03-02T08:44:11.000000Z\",\"deleted_at\":null,\"items\":[{\"id\":19,\"sales_invoice_id\":1,\"brand_id\":1,\"item_id\":1,\"uom_id\":1,\"quantity\":\"1.00\",\"rate\":\"178.68\",\"mrp\":\"350.00\",\"amount\":\"178.68\",\"hsn_sac\":\"62053000\",\"art_no\":\"CF12345-1\",\"size\":\"40\",\"sleeve_type\":\"Full\",\"created_at\":\"2026-03-02T08:44:11.000000Z\",\"updated_at\":\"2026-03-02T08:44:11.000000Z\",\"deleted_at\":null},{\"id\":20,\"sales_invoice_id\":1,\"brand_id\":1,\"item_id\":1,\"uom_id\":1,\"quantity\":\"1.00\",\"rate\":\"138.96\",\"mrp\":\"350.00\",\"amount\":\"138.96\",\"hsn_sac\":\"62053000\",\"art_no\":\"CF12345-2\",\"size\":\"38\",\"sleeve_type\":\"Half\",\"created_at\":\"2026-03-02T08:44:11.000000Z\",\"updated_at\":\"2026-03-02T08:44:11.000000Z\",\"deleted_at\":null}]}', '{\"id\":1,\"inv_no\":\"SINV-0001\",\"inv_date\":\"2026-02-27T18:30:00.000000Z\",\"so_id\":1,\"customer_id\":1,\"delivery_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"remarks\":\"This is sales invoice\",\"invoice_status\":\"Paid\",\"payment_mode\":\"Bank (Cheque)\",\"extra_input\":\"0123456789\",\"due_date\":\"2026-02-27T18:30:00.000000Z\",\"notes\":null,\"signature_file\":\"uploads\\/sales_invoices\\/signatures\\/1772274057_sig_signature_images.jpg\",\"attachment_file\":\"uploads\\/sales_invoices\\/attachments\\/1772274057_att_pdf_image.jpg\",\"show_fields\":[\"amount\",\"discount\",\"subtotal\",\"grandtotal\"],\"sub_total\":\"317.64\",\"discount_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"317.64\",\"other_state\":false,\"igst\":\"0.00\",\"cgst\":\"28.59\",\"sgst\":\"28.59\",\"tax_amount\":\"57.18\",\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"other_charges\":\"0.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"grand_total\":\"374.82\",\"received_amount\":\"0.00\",\"due_amount\":\"374.82\",\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-02-28T10:13:30.000000Z\",\"updated_at\":\"2026-03-02T08:44:22.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-02 08:44:22'),
(105, 1, 'update', 'Setting', 'settings', 1, '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"ushadevi.saitech@gmail.com\",\"logo\":null,\"phone_number\":\"9856355632\",\"toll_free_no\":null,\"state_id\":1,\"city_id\":1,\"address\":\"272\\/2, Somu Nagar, Siringeri Nagar\\r\\n(Sarathambal Kovil Backside),\\r\\nByepass Road, Madurai - 625016\",\"cgst\":9,\"sgst\":9,\"igst\":18,\"pan_no\":null,\"gst_no\":null,\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"so_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-03-02T08:43:46.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"srinachias@yahoo.in,sales@nachias.com\",\"logo\":null,\"phone_number\":\"9856355632\",\"toll_free_no\":null,\"state_id\":1,\"city_id\":1,\"address\":\"272\\/2, Somu Nagar, Siringeri Nagar\\r\\n(Sarathambal Kovil Backside),\\r\\nByepass Road, Madurai - 625016\",\"cgst\":9,\"sgst\":9,\"igst\":18,\"pan_no\":null,\"gst_no\":null,\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"so_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-03-02T11:12:42.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-02 11:12:42'),
(106, 1, 'update', 'Setting', 'settings', 1, '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"srinachias@yahoo.in,sales@nachias.com\",\"logo\":null,\"phone_number\":\"8489938071,8489\",\"toll_free_no\":null,\"state_id\":1,\"city_id\":1,\"address\":\"272\\/2, Somu Nagar, Siringeri Nagar\\r\\n(Sarathambal Kovil Backside),\\r\\nByepass Road, Madurai - 625016\",\"cgst\":9,\"sgst\":9,\"igst\":18,\"pan_no\":null,\"gst_no\":null,\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"so_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-03-02T11:12:42.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"srinachias@yahoo.in,sales@nachias.com\",\"logo\":null,\"phone_number\":\"9856355632\",\"toll_free_no\":\"8489938071,8489938073\",\"state_id\":1,\"city_id\":1,\"address\":\"272\\/2, Somu Nagar, Siringeri Nagar\\r\\n(Sarathambal Kovil Backside),\\r\\nByepass Road, Madurai - 625016\",\"cgst\":9,\"sgst\":9,\"igst\":18,\"pan_no\":null,\"gst_no\":null,\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"so_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-03-02T11:16:01.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-02 11:16:01'),
(107, 1, 'update', 'Setting', 'settings', 1, '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"srinachias@yahoo.in,sales@nachias.com\",\"logo\":null,\"phone_number\":\"8489938071,8489\",\"toll_free_no\":\"8489938071,8489938073\",\"state_id\":1,\"city_id\":1,\"address\":\"272\\/2, Somu Nagar, Siringeri Nagar\\r\\n(Sarathambal Kovil Backside),\\r\\nByepass Road, Madurai - 625016\",\"cgst\":9,\"sgst\":9,\"igst\":18,\"pan_no\":null,\"gst_no\":null,\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"so_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-03-02T11:16:01.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"company_name\":\"Nachias Fashion Private Limited\",\"email\":\"srinachias@yahoo.in,sales@nachias.com\",\"logo\":null,\"phone_number\":\"8489938071\",\"toll_free_no\":\"8489938071,8489938073\",\"state_id\":1,\"city_id\":1,\"address\":\"272\\/2, Somu Nagar, Siringeri Nagar\\r\\n(Sarathambal Kovil Backside),\\r\\nByepass Road, Madurai - 625016\",\"cgst\":9,\"sgst\":9,\"igst\":18,\"pan_no\":null,\"gst_no\":\"33AADCN9342A1ZU\",\"cin_no\":null,\"working_days\":null,\"opening_time\":null,\"closing_time\":null,\"po_prefix\":null,\"purchase_invoice_prefix\":null,\"so_prefix\":null,\"created_at\":\"2026-02-20T08:49:21.000000Z\",\"updated_at\":\"2026-03-02T11:24:56.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-02 11:24:56'),
(108, 1, 'update_status', 'Sales Invoice Status', 'sales_invoices', 1, '{\"id\":1,\"inv_no\":\"SINV-0001\",\"inv_date\":\"2026-02-27T18:30:00.000000Z\",\"so_id\":1,\"customer_id\":1,\"delivery_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"remarks\":\"This is sales invoice\",\"invoice_status\":\"Paid\",\"payment_mode\":\"Bank (Cheque)\",\"extra_input\":\"0123456789\",\"due_date\":\"2026-02-27T18:30:00.000000Z\",\"notes\":null,\"signature_file\":\"uploads\\/sales_invoices\\/signatures\\/1772274057_sig_signature_images.jpg\",\"attachment_file\":\"uploads\\/sales_invoices\\/attachments\\/1772274057_att_pdf_image.jpg\",\"show_fields\":[\"amount\",\"discount\",\"subtotal\",\"grandtotal\"],\"sub_total\":\"317.64\",\"discount_percent\":\"2.00\",\"discount\":\"6.35\",\"total\":\"311.29\",\"other_state\":false,\"igst\":\"0.00\",\"cgst\":\"28.02\",\"sgst\":\"28.02\",\"tax_amount\":\"56.03\",\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"other_charges\":\"0.00\",\"round_off_type\":\"Less\",\"round_off\":\"0.32\",\"grand_total\":\"367.00\",\"received_amount\":\"0.00\",\"due_amount\":\"367.00\",\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-02-28T10:13:30.000000Z\",\"updated_at\":\"2026-03-03T05:15:20.000000Z\",\"deleted_at\":null,\"items\":[{\"id\":25,\"sales_invoice_id\":1,\"brand_id\":1,\"item_id\":1,\"uom_id\":1,\"quantity\":\"1.00\",\"rate\":\"178.68\",\"mrp\":\"350.00\",\"amount\":\"178.68\",\"hsn_sac\":\"62053000\",\"art_no\":\"CF12345-1\",\"size\":\"40\",\"sleeve_type\":\"Full\",\"created_at\":\"2026-03-03T05:15:20.000000Z\",\"updated_at\":\"2026-03-03T05:15:20.000000Z\",\"deleted_at\":null},{\"id\":26,\"sales_invoice_id\":1,\"brand_id\":1,\"item_id\":1,\"uom_id\":1,\"quantity\":\"1.00\",\"rate\":\"138.96\",\"mrp\":\"350.00\",\"amount\":\"138.96\",\"hsn_sac\":\"62053000\",\"art_no\":\"CF12345-2\",\"size\":\"38\",\"sleeve_type\":\"Half\",\"created_at\":\"2026-03-03T05:15:20.000000Z\",\"updated_at\":\"2026-03-03T05:15:20.000000Z\",\"deleted_at\":null}]}', '{\"id\":1,\"inv_no\":\"SINV-0001\",\"inv_date\":\"2026-02-27T18:30:00.000000Z\",\"so_id\":1,\"customer_id\":1,\"delivery_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"remarks\":\"This is sales invoice\",\"invoice_status\":\"Partially Paid\",\"payment_mode\":\"Bank (Cheque)\",\"extra_input\":\"0123456789\",\"due_date\":\"2026-02-27T18:30:00.000000Z\",\"notes\":null,\"signature_file\":\"uploads\\/sales_invoices\\/signatures\\/1772274057_sig_signature_images.jpg\",\"attachment_file\":\"uploads\\/sales_invoices\\/attachments\\/1772274057_att_pdf_image.jpg\",\"show_fields\":[\"amount\",\"discount\",\"subtotal\",\"grandtotal\"],\"sub_total\":\"317.64\",\"discount_percent\":\"2.00\",\"discount\":\"6.35\",\"total\":\"311.29\",\"other_state\":false,\"igst\":\"0.00\",\"cgst\":\"28.02\",\"sgst\":\"28.02\",\"tax_amount\":\"56.03\",\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"other_charges\":\"0.00\",\"round_off_type\":\"Less\",\"round_off\":\"0.32\",\"grand_total\":\"367.00\",\"received_amount\":\"0.00\",\"due_amount\":\"367.00\",\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-02-28T10:13:30.000000Z\",\"updated_at\":\"2026-03-03T05:35:37.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 05:35:38'),
(109, 1, 'update_status', 'Sales Invoice Status', 'sales_invoices', 1, '{\"id\":1,\"inv_no\":\"SINV-0001\",\"inv_date\":\"2026-02-27T18:30:00.000000Z\",\"so_id\":1,\"customer_id\":1,\"delivery_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"remarks\":\"This is sales invoice\",\"invoice_status\":\"Partially Paid\",\"payment_mode\":\"Bank (Cheque)\",\"extra_input\":\"0123456789\",\"due_date\":\"2026-02-27T18:30:00.000000Z\",\"notes\":null,\"signature_file\":\"uploads\\/sales_invoices\\/signatures\\/1772274057_sig_signature_images.jpg\",\"attachment_file\":\"uploads\\/sales_invoices\\/attachments\\/1772274057_att_pdf_image.jpg\",\"show_fields\":[\"amount\",\"discount\",\"subtotal\",\"grandtotal\"],\"sub_total\":\"317.64\",\"discount_percent\":\"2.00\",\"discount\":\"6.35\",\"total\":\"311.29\",\"other_state\":false,\"igst\":\"0.00\",\"cgst\":\"28.02\",\"sgst\":\"28.02\",\"tax_amount\":\"56.03\",\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"other_charges\":\"0.00\",\"round_off_type\":\"Less\",\"round_off\":\"0.32\",\"grand_total\":\"367.00\",\"received_amount\":\"0.00\",\"due_amount\":\"367.00\",\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-02-28T10:13:30.000000Z\",\"updated_at\":\"2026-03-03T05:35:37.000000Z\",\"deleted_at\":null,\"items\":[{\"id\":25,\"sales_invoice_id\":1,\"brand_id\":1,\"item_id\":1,\"uom_id\":1,\"quantity\":\"1.00\",\"rate\":\"178.68\",\"mrp\":\"350.00\",\"amount\":\"178.68\",\"hsn_sac\":\"62053000\",\"art_no\":\"CF12345-1\",\"size\":\"40\",\"sleeve_type\":\"Full\",\"created_at\":\"2026-03-03T05:15:20.000000Z\",\"updated_at\":\"2026-03-03T05:15:20.000000Z\",\"deleted_at\":null},{\"id\":26,\"sales_invoice_id\":1,\"brand_id\":1,\"item_id\":1,\"uom_id\":1,\"quantity\":\"1.00\",\"rate\":\"138.96\",\"mrp\":\"350.00\",\"amount\":\"138.96\",\"hsn_sac\":\"62053000\",\"art_no\":\"CF12345-2\",\"size\":\"38\",\"sleeve_type\":\"Half\",\"created_at\":\"2026-03-03T05:15:20.000000Z\",\"updated_at\":\"2026-03-03T05:15:20.000000Z\",\"deleted_at\":null}]}', '{\"id\":1,\"inv_no\":\"SINV-0001\",\"inv_date\":\"2026-02-27T18:30:00.000000Z\",\"so_id\":1,\"customer_id\":1,\"delivery_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"remarks\":\"This is sales invoice\",\"invoice_status\":\"Paid\",\"payment_mode\":\"Bank (Cheque)\",\"extra_input\":\"0123456789\",\"due_date\":\"2026-02-27T18:30:00.000000Z\",\"notes\":null,\"signature_file\":\"uploads\\/sales_invoices\\/signatures\\/1772274057_sig_signature_images.jpg\",\"attachment_file\":\"uploads\\/sales_invoices\\/attachments\\/1772274057_att_pdf_image.jpg\",\"show_fields\":[\"amount\",\"discount\",\"subtotal\",\"grandtotal\"],\"sub_total\":\"317.64\",\"discount_percent\":\"2.00\",\"discount\":\"6.35\",\"total\":\"311.29\",\"other_state\":false,\"igst\":\"0.00\",\"cgst\":\"28.02\",\"sgst\":\"28.02\",\"tax_amount\":\"56.03\",\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"other_charges\":\"0.00\",\"round_off_type\":\"Less\",\"round_off\":\"0.32\",\"grand_total\":\"367.00\",\"received_amount\":\"0.00\",\"due_amount\":\"367.00\",\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-02-28T10:13:30.000000Z\",\"updated_at\":\"2026-03-03T05:35:42.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 05:35:42'),
(110, 1, 'update', 'Sale Order', 'sales_orders', 1, NULL, '{\"id\":1,\"so_no\":\"SO-001\",\"so_date\":\"2026-03-02T18:30:00.000000Z\",\"request_date\":\"2026-03-02T18:30:00.000000Z\",\"order_type\":\"Regular\",\"season_id\":\"1\",\"customer_id\":\"1\",\"customer_po_ref\":null,\"store_id\":\"1\",\"billing_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"shipping_address\":\"9, Navabathkana Street, Mahal Area, Madurai Main,\",\"payment_terms\":\"Advance Payment\",\"agent_id\":\"1\",\"delivery_date\":\"2026-03-05T18:30:00.000000Z\",\"shipping_method\":\"BlueDart\",\"transport_mode\":\"By Hand\",\"dispatch_from\":null,\"transporter_name\":\"Thirumalammal Lorry Booking Office\",\"freight_type\":\"Paid\",\"freight_amount\":\"0.00\",\"eway_bill_no\":null,\"lr_no\":null,\"dispatch_through\":null,\"status\":\"Approved\",\"approved_by\":null,\"approved_date\":null,\"total_qty\":\"2.00\",\"sub_total_qty\":\"317.64\",\"commission_percent\":\"2.00\",\"commission_amount\":\"6.35\",\"discount_percent\":\"2.00\",\"discount_amount\":\"6.35\",\"taxable_amount\":\"311.29\",\"other_state\":false,\"igst_percent\":\"18.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"56.03\",\"round_off_type\":\"Less\",\"round_off\":\"0.32\",\"total_amount\":\"367.00\",\"internal_remarks\":null,\"terms_conditions\":\"1. Goods once sold will not be taken back.\\r\\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.\",\"attachment\":\"attach_1772254909_69a276bdb13d1.png\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-02-27T09:52:31.000000Z\",\"updated_at\":\"2026-03-03T07:01:40.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 07:01:40'),
(111, 1, 'create', 'Credit Note', 'credit_notes', 1, NULL, '{\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":\"1\",\"customer_id\":\"1\",\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772540824.jpg\",\"status\":\"Draft\",\"created_by\":1,\"updated_at\":\"2026-03-03T12:27:04.000000Z\",\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 12:27:04'),
(112, 1, 'create', 'Debit Note', 'debit_notes', 1, NULL, '{\"debit_note_no\":\"DN-0001\",\"debit_note_date\":\"2026-03-03\",\"purchase_invoice_id\":\"1\",\"supplier_id\":\"1\",\"reason\":null,\"other_state\":\"N\",\"igst_percent\":\"9.00\",\"cgst_percent\":\"18.00\",\"sgst_percent\":\"9.00\",\"sub_total\":\"1485.00\",\"tax_amount\":\"400.95\",\"round_off_type\":\"Add\",\"round_off\":0,\"grand_total\":\"1886.00\",\"remarks\":\"ttestetsetest\",\"reference_document\":\"debit_note_1772541261.jpg\",\"status\":\"Draft\",\"created_by\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 12:34:21'),
(113, 1, 'update', 'Purchase Order', 'purchase_orders', 1, '{\"id\":1,\"po_number\":\"PO-001\",\"po_date\":\"2026-02-22T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":1,\"reference_no\":\"PO-001\",\"reference_date\":\"2026-02-22T18:30:00.000000Z\",\"due_date\":\"2026-03-12T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Received\",\"additional_attachments\":null,\"created_by\":null,\"updated_by\":null,\"total_qty\":\"450.00\",\"sub_total\":\"16650.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"16650.00\",\"other_state\":false,\"igst_percent\":\"9.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"4495.50\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"21145.50\",\"created_at\":\"2026-02-23T09:30:28.000000Z\",\"updated_at\":\"2026-02-23T09:31:55.000000Z\",\"deleted_at\":null}', '{\"id\":1,\"po_number\":\"PO-001\",\"po_date\":\"2026-03-02T18:30:00.000000Z\",\"purchase_commission_agent_id\":null,\"commission\":\"0.00\",\"supplier_id\":1,\"reference_no\":\"PO-001\",\"reference_date\":\"2026-02-22T18:30:00.000000Z\",\"due_date\":\"2026-03-12T18:30:00.000000Z\",\"store_type_id\":1,\"payment_terms\":null,\"status\":\"Received\",\"additional_attachments\":null,\"created_by\":null,\"updated_by\":null,\"total_qty\":\"450.00\",\"sub_total\":\"16650.00\",\"discount_percent\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"16650.00\",\"other_state\":false,\"igst_percent\":\"9.00\",\"cgst_percent\":\"9.00\",\"sgst_percent\":\"9.00\",\"tax_amount\":\"2997.00\",\"round_off_type\":\"Add\",\"round_off\":\"0.00\",\"total_amount\":\"19647.00\",\"created_at\":\"2026-02-23T09:30:28.000000Z\",\"updated_at\":\"2026-03-03T12:37:51.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 12:37:51'),
(114, 1, 'update', 'Credit Note', 'credit_notes', 1, '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772540824.jpg\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:27:04.000000Z\",\"deleted_at\":null,\"items\":[{\"id\":1,\"credit_note_id\":1,\"sales_invoice_item_id\":3,\"brand_category_id\":1,\"item_id\":1,\"size\":\"40\",\"quantity\":\"1.00\",\"sleeve_type\":\"Full\",\"mrp\":\"360.00\",\"uom_id\":1,\"rate\":\"178.68\",\"amount\":\"178.68\",\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:27:04.000000Z\",\"deleted_at\":null,\"item\":{\"id\":1,\"brand_category_id\":1,\"brand_id\":1,\"name\":\"Formal Cotton Shirt\",\"code\":\"1001\",\"style_id\":null,\"fabric_type_id\":null,\"design_art_no\":null,\"uom_id\":1,\"size_ratio_id\":null,\"color_id\":[],\"product_barcode\":null,\"standard_costing\":null,\"store_category_id\":null,\"related_materials\":null,\"operation_stages\":null,\"service_providers\":{\"cutting\":null},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"mrp\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:36:12.000000Z\",\"updated_at\":\"2026-02-23T09:36:12.000000Z\",\"deleted_at\":null},\"uom\":{\"id\":1,\"uom_code\":\"PCS\",\"uom_name\":\"Pieces\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:26:27.000000Z\",\"updated_at\":\"2026-02-23T09:26:27.000000Z\",\"deleted_at\":null},\"brand_category\":{\"id\":1,\"code\":\"FST\",\"name\":\"Fomal Shirt\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null,\"created_at\":\"2026-02-23T09:35:16.000000Z\",\"updated_at\":\"2026-02-23T09:35:16.000000Z\"}}]}', '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772541598.pdf\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:39:58.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 12:39:58'),
(115, 1, 'update', 'Credit Note', 'credit_notes', 1, '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772541598.pdf\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:39:58.000000Z\",\"deleted_at\":null,\"items\":[{\"id\":2,\"credit_note_id\":1,\"sales_invoice_item_id\":3,\"brand_category_id\":1,\"item_id\":1,\"size\":\"40\",\"quantity\":\"1.00\",\"sleeve_type\":\"Full\",\"mrp\":\"360.00\",\"uom_id\":1,\"rate\":\"178.68\",\"amount\":\"178.68\",\"created_at\":\"2026-03-03T12:39:58.000000Z\",\"updated_at\":\"2026-03-03T12:39:58.000000Z\",\"deleted_at\":null,\"item\":{\"id\":1,\"brand_category_id\":1,\"brand_id\":1,\"name\":\"Formal Cotton Shirt\",\"code\":\"1001\",\"style_id\":null,\"fabric_type_id\":null,\"design_art_no\":null,\"uom_id\":1,\"size_ratio_id\":null,\"color_id\":[],\"product_barcode\":null,\"standard_costing\":null,\"store_category_id\":null,\"related_materials\":null,\"operation_stages\":null,\"service_providers\":{\"cutting\":null},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"mrp\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:36:12.000000Z\",\"updated_at\":\"2026-02-23T09:36:12.000000Z\",\"deleted_at\":null},\"uom\":{\"id\":1,\"uom_code\":\"PCS\",\"uom_name\":\"Pieces\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:26:27.000000Z\",\"updated_at\":\"2026-02-23T09:26:27.000000Z\",\"deleted_at\":null},\"brand_category\":{\"id\":1,\"code\":\"FST\",\"name\":\"Fomal Shirt\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null,\"created_at\":\"2026-02-23T09:35:16.000000Z\",\"updated_at\":\"2026-02-23T09:35:16.000000Z\"}}]}', '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772541598.pdf\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:39:58.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 12:41:30'),
(116, 1, 'update', 'Credit Note', 'credit_notes', 1, '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772541598.pdf\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:39:58.000000Z\",\"deleted_at\":null,\"items\":[{\"id\":3,\"credit_note_id\":1,\"sales_invoice_item_id\":3,\"brand_category_id\":1,\"item_id\":1,\"size\":\"40\",\"quantity\":\"1.00\",\"sleeve_type\":\"Full\",\"mrp\":\"360.00\",\"uom_id\":1,\"rate\":\"178.68\",\"amount\":\"178.68\",\"created_at\":\"2026-03-03T12:41:30.000000Z\",\"updated_at\":\"2026-03-03T12:41:30.000000Z\",\"deleted_at\":null,\"item\":{\"id\":1,\"brand_category_id\":1,\"brand_id\":1,\"name\":\"Formal Cotton Shirt\",\"code\":\"1001\",\"style_id\":null,\"fabric_type_id\":null,\"design_art_no\":null,\"uom_id\":1,\"size_ratio_id\":null,\"color_id\":[],\"product_barcode\":null,\"standard_costing\":null,\"store_category_id\":null,\"related_materials\":null,\"operation_stages\":null,\"service_providers\":{\"cutting\":null},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"mrp\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:36:12.000000Z\",\"updated_at\":\"2026-02-23T09:36:12.000000Z\",\"deleted_at\":null},\"uom\":{\"id\":1,\"uom_code\":\"PCS\",\"uom_name\":\"Pieces\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:26:27.000000Z\",\"updated_at\":\"2026-02-23T09:26:27.000000Z\",\"deleted_at\":null},\"brand_category\":{\"id\":1,\"code\":\"FST\",\"name\":\"Fomal Shirt\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null,\"created_at\":\"2026-02-23T09:35:16.000000Z\",\"updated_at\":\"2026-02-23T09:35:16.000000Z\"}}]}', '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772541998.jpg\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:46:38.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 12:46:38');
INSERT INTO `logs` (`id`, `user_id`, `action_type`, `module`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`) VALUES
(117, 1, 'update', 'Credit Note', 'credit_notes', 1, '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772541998.jpg\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:46:38.000000Z\",\"deleted_at\":null,\"items\":[{\"id\":4,\"credit_note_id\":1,\"sales_invoice_item_id\":3,\"brand_category_id\":1,\"item_id\":1,\"size\":\"40\",\"quantity\":\"1.00\",\"sleeve_type\":\"Full\",\"mrp\":\"360.00\",\"uom_id\":1,\"rate\":\"178.68\",\"amount\":\"178.68\",\"created_at\":\"2026-03-03T12:46:38.000000Z\",\"updated_at\":\"2026-03-03T12:46:38.000000Z\",\"deleted_at\":null,\"item\":{\"id\":1,\"brand_category_id\":1,\"brand_id\":1,\"name\":\"Formal Cotton Shirt\",\"code\":\"1001\",\"style_id\":null,\"fabric_type_id\":null,\"design_art_no\":null,\"uom_id\":1,\"size_ratio_id\":null,\"color_id\":[],\"product_barcode\":null,\"standard_costing\":null,\"store_category_id\":null,\"related_materials\":null,\"operation_stages\":null,\"service_providers\":{\"cutting\":null},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"mrp\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:36:12.000000Z\",\"updated_at\":\"2026-02-23T09:36:12.000000Z\",\"deleted_at\":null},\"uom\":{\"id\":1,\"uom_code\":\"PCS\",\"uom_name\":\"Pieces\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:26:27.000000Z\",\"updated_at\":\"2026-02-23T09:26:27.000000Z\",\"deleted_at\":null},\"brand_category\":{\"id\":1,\"code\":\"FST\",\"name\":\"Fomal Shirt\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null,\"created_at\":\"2026-02-23T09:35:16.000000Z\",\"updated_at\":\"2026-02-23T09:35:16.000000Z\"}}]}', '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772542119.jpg\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:48:39.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 12:48:39'),
(118, 1, 'update', 'Credit Note', 'credit_notes', 1, '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772542119.jpg\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:48:39.000000Z\",\"deleted_at\":null,\"items\":[{\"id\":5,\"credit_note_id\":1,\"sales_invoice_item_id\":3,\"brand_category_id\":1,\"item_id\":1,\"size\":\"40\",\"quantity\":\"1.00\",\"sleeve_type\":\"Full\",\"mrp\":\"360.00\",\"uom_id\":1,\"rate\":\"178.68\",\"amount\":\"178.68\",\"created_at\":\"2026-03-03T12:48:39.000000Z\",\"updated_at\":\"2026-03-03T12:48:39.000000Z\",\"deleted_at\":null,\"item\":{\"id\":1,\"brand_category_id\":1,\"brand_id\":1,\"name\":\"Formal Cotton Shirt\",\"code\":\"1001\",\"style_id\":null,\"fabric_type_id\":null,\"design_art_no\":null,\"uom_id\":1,\"size_ratio_id\":null,\"color_id\":[],\"product_barcode\":null,\"standard_costing\":null,\"store_category_id\":null,\"related_materials\":null,\"operation_stages\":null,\"service_providers\":{\"cutting\":null},\"wholesale_price\":null,\"retail_price\":null,\"export_price\":null,\"mrp\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:36:12.000000Z\",\"updated_at\":\"2026-02-23T09:36:12.000000Z\",\"deleted_at\":null},\"uom\":{\"id\":1,\"uom_code\":\"PCS\",\"uom_name\":\"Pieces\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-02-23T09:26:27.000000Z\",\"updated_at\":\"2026-02-23T09:26:27.000000Z\",\"deleted_at\":null},\"brand_category\":{\"id\":1,\"code\":\"FST\",\"name\":\"Fomal Shirt\",\"description\":null,\"status\":\"Active\",\"created_by\":1,\"updated_by\":null,\"deleted_at\":null,\"created_at\":\"2026-02-23T09:35:16.000000Z\",\"updated_at\":\"2026-02-23T09:35:16.000000Z\"}}]}', '{\"id\":1,\"note_no\":\"CN-001\",\"note_date\":\"2026-06-19T18:30:00.000000Z\",\"sales_invoice_id\":1,\"customer_id\":1,\"reason\":\"Return\",\"other_state\":false,\"igst_percent\":\"18.00\",\"igst\":\"0.00\",\"cgst_percent\":\"9.00\",\"cgst\":\"16.08\",\"sgst_percent\":\"9.00\",\"sgst\":\"16.08\",\"sub_total\":\"178.68\",\"tax_amount\":\"32.16\",\"grand_total\":\"210.84\",\"remarks\":\"For reason\",\"reference_doc\":\"credit_note_1772542130.jpg\",\"status\":\"Draft\",\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-03-03T12:27:04.000000Z\",\"updated_at\":\"2026-03-03T12:48:50.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-03 12:48:50');

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
(261, '2026_03_03_173745_add_brand_category_id_to_credit_note_items', 249);

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
(2, 'App\\Models\\User', 5),
(4, 'App\\Models\\User', 7),
(4, 'App\\Models\\User', 10),
(5, 'App\\Models\\User', 2),
(5, 'App\\Models\\User', 3),
(5, 'App\\Models\\User', 4),
(5, 'App\\Models\\User', 6),
(5, 'App\\Models\\User', 8),
(6, 'App\\Models\\User', 9);

-- --------------------------------------------------------

--
-- Table structure for table `operation_stages`
--

CREATE TABLE `operation_stages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `operation_stage_name` varchar(100) NOT NULL,
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

INSERT INTO `operation_stages` (`id`, `operation_stage_name`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'Cutting', 'Active', '2026-02-23 09:33:23', '2026-02-23 09:33:23', NULL, 1, NULL),
(2, 'Stitching', 'Active', '2026-02-26 08:11:36', '2026-02-26 08:11:36', NULL, 1, NULL);

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
(1, 'Cross', 'Active', 1, NULL, NULL, '2026-02-26 08:36:34', '2026-02-26 08:36:34');

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
(1, 'roles', 'create', 'Create Roles', 'create roles', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(2, 'roles', 'edit', 'Edit Roles', 'edit roles', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(3, 'roles', 'delete', 'Delete Roles', 'delete roles', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(4, 'roles', 'view', 'View Roles', 'view roles', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(5, 'employee', 'create', 'Create Employee', 'create employee', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(6, 'employee', 'edit', 'Edit Employee', 'edit employee', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(7, 'employee', 'delete', 'Delete Employee', 'delete employee', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(8, 'employee', 'view', 'View Employee', 'view employee', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(9, 'states', 'create', 'Create States', 'create states', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(10, 'states', 'edit', 'Edit States', 'edit states', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(11, 'states', 'delete', 'Delete States', 'delete states', 'web', '2026-02-06 07:13:53', '2026-02-06 07:13:53'),
(12, 'states', 'view', 'View States', 'view states', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(13, 'cities', 'create', 'Create Cities', 'create cities', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(14, 'cities', 'edit', 'Edit Cities', 'edit cities', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(15, 'cities', 'delete', 'Delete Cities', 'delete cities', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(16, 'cities', 'view', 'View Cities', 'view cities', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(17, 'service-points', 'create', 'Create Service Points', 'create service-points', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(18, 'service-points', 'edit', 'Edit Service Points', 'edit service-points', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(19, 'service-points', 'delete', 'Delete Service Points', 'delete service-points', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(20, 'service-points', 'view', 'View Service Points', 'view service-points', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(21, 'uoms', 'create', 'Create Uoms', 'create uoms', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(22, 'uoms', 'edit', 'Edit Uoms', 'edit uoms', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(23, 'uoms', 'delete', 'Delete Uoms', 'delete uoms', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(24, 'uoms', 'view', 'View Uoms', 'view uoms', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(25, 'colors', 'create', 'Create Colors', 'create colors', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(26, 'colors', 'edit', 'Edit Colors', 'edit colors', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(27, 'colors', 'delete', 'Delete Colors', 'delete colors', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(28, 'colors', 'view', 'View Colors', 'view colors', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(29, 'operation-stages', 'create', 'Create Operation Stages', 'create operation-stages', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(30, 'operation-stages', 'edit', 'Edit Operation Stages', 'edit operation-stages', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(31, 'operation-stages', 'delete', 'Delete Operation Stages', 'delete operation-stages', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(32, 'operation-stages', 'view', 'View Operation Stages', 'view operation-stages', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(33, 'zones', 'create', 'Create Zones', 'create zones', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(34, 'zones', 'edit', 'Edit Zones', 'edit zones', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(35, 'zones', 'delete', 'Delete Zones', 'delete zones', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(36, 'zones', 'view', 'View Zones', 'view zones', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(37, 'size-ratio', 'create', 'Create Size Ratio', 'create size-ratio', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(38, 'size-ratio', 'edit', 'Edit Size Ratio', 'edit size-ratio', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(39, 'size-ratio', 'delete', 'Delete Size Ratio', 'delete size-ratio', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(40, 'size-ratio', 'view', 'View Size Ratio', 'view size-ratio', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(41, 'fabric-type', 'create', 'Create Fabric Type', 'create fabric-type', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(42, 'fabric-type', 'edit', 'Edit Fabric Type', 'edit fabric-type', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(43, 'fabric-type', 'delete', 'Delete Fabric Type', 'delete fabric-type', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(44, 'fabric-type', 'view', 'View Fabric Type', 'view fabric-type', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(45, 'charges', 'create', 'Create Charges', 'create charges', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(46, 'charges', 'edit', 'Edit Charges', 'edit charges', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(47, 'charges', 'delete', 'Delete Charges', 'delete charges', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(48, 'charges', 'view', 'View Charges', 'view charges', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(49, 'store-location', 'create', 'Create Store Location', 'create store-location', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(50, 'store-location', 'edit', 'Edit Store Location', 'edit store-location', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(51, 'store-location', 'delete', 'Delete Store Location', 'delete store-location', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(52, 'store-location', 'view', 'View Store Location', 'view store-location', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(53, 'departments', 'create', 'Create Departments', 'create departments', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(54, 'departments', 'edit', 'Edit Departments', 'edit departments', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(55, 'departments', 'delete', 'Delete Departments', 'delete departments', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(56, 'departments', 'view', 'View Departments', 'view departments', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(57, 'taxes', 'create', 'Create Taxes', 'create taxes', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(58, 'taxes', 'edit', 'Edit Taxes', 'edit taxes', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(59, 'taxes', 'delete', 'Delete Taxes', 'delete taxes', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(60, 'taxes', 'view', 'View Taxes', 'view taxes', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(61, 'styles', 'create', 'Create Styles', 'create styles', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(62, 'styles', 'edit', 'Edit Styles', 'edit styles', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(63, 'styles', 'delete', 'Delete Styles', 'delete styles', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(64, 'styles', 'view', 'View Styles', 'view styles', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(65, 'stores', 'create', 'Create Stores', 'create stores', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(66, 'stores', 'edit', 'Edit Stores', 'edit stores', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(67, 'stores', 'delete', 'Delete Stores', 'delete stores', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(68, 'stores', 'view', 'View Stores', 'view stores', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(69, 'fits', 'create', 'Create Fits', 'create fits', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(70, 'fits', 'edit', 'Edit Fits', 'edit fits', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(71, 'fits', 'delete', 'Delete Fits', 'delete fits', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(72, 'fits', 'view', 'View Fits', 'view fits', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(73, 'patti-types', 'create', 'Create Patti Types', 'create patti-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(74, 'patti-types', 'edit', 'Edit Patti Types', 'edit patti-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(75, 'patti-types', 'delete', 'Delete Patti Types', 'delete patti-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(76, 'patti-types', 'view', 'View Patti Types', 'view patti-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(77, 'collar-types', 'create', 'Create Collar Types', 'create collar-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(78, 'collar-types', 'edit', 'Edit Collar Types', 'edit collar-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(79, 'collar-types', 'delete', 'Delete Collar Types', 'delete collar-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(80, 'collar-types', 'view', 'View Collar Types', 'view collar-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(81, 'cuff-types', 'create', 'Create Cuff Types', 'create cuff-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(82, 'cuff-types', 'edit', 'Edit Cuff Types', 'edit cuff-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(83, 'cuff-types', 'delete', 'Delete Cuff Types', 'delete cuff-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(84, 'cuff-types', 'view', 'View Cuff Types', 'view cuff-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(85, 'pocket-types', 'create', 'Create Pocket Types', 'create pocket-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(86, 'pocket-types', 'edit', 'Edit Pocket Types', 'edit pocket-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(87, 'pocket-types', 'delete', 'Delete Pocket Types', 'delete pocket-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(88, 'pocket-types', 'view', 'View Pocket Types', 'view pocket-types', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(89, 'bottom-cuts', 'create', 'Create Bottom Cuts', 'create bottom-cuts', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(90, 'bottom-cuts', 'edit', 'Edit Bottom Cuts', 'edit bottom-cuts', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(91, 'bottom-cuts', 'delete', 'Delete Bottom Cuts', 'delete bottom-cuts', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(92, 'bottom-cuts', 'view', 'View Bottom Cuts', 'view bottom-cuts', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(93, 'process-groups', 'create', 'Create Process Groups', 'create process-groups', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(94, 'process-groups', 'edit', 'Edit Process Groups', 'edit process-groups', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(95, 'process-groups', 'delete', 'Delete Process Groups', 'delete process-groups', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(96, 'process-groups', 'view', 'View Process Groups', 'view process-groups', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(97, 'seasons', 'create', 'Create Seasons', 'create seasons', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(98, 'seasons', 'edit', 'Edit Seasons', 'edit seasons', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(99, 'seasons', 'delete', 'Delete Seasons', 'delete seasons', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(100, 'seasons', 'view', 'View Seasons', 'view seasons', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(101, 'shifts', 'create', 'Create Shifts', 'create shifts', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(102, 'shifts', 'edit', 'Edit Shifts', 'edit shifts', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(103, 'shifts', 'delete', 'Delete Shifts', 'delete shifts', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(104, 'shifts', 'view', 'View Shifts', 'view shifts', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(105, 'production-services', 'create', 'Create Production Services', 'create production-services', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(106, 'production-services', 'edit', 'Edit Production Services', 'edit production-services', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(107, 'production-services', 'delete', 'Delete Production Services', 'delete production-services', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(108, 'production-services', 'view', 'View Production Services', 'view production-services', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(109, 'customers', 'create', 'Create Customers', 'create customers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(110, 'customers', 'edit', 'Edit Customers', 'edit customers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(111, 'customers', 'delete', 'Delete Customers', 'delete customers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(112, 'customers', 'view', 'View Customers', 'view customers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(113, 'customers', 'view_details', 'View_details Customers', 'view_details customers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(114, 'suppliers', 'create', 'Create Suppliers', 'create suppliers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(115, 'suppliers', 'edit', 'Edit Suppliers', 'edit suppliers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(116, 'suppliers', 'delete', 'Delete Suppliers', 'delete suppliers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(117, 'suppliers', 'view', 'View Suppliers', 'view suppliers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(118, 'suppliers', 'view_details', 'View_details Suppliers', 'view_details suppliers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(119, 'service-providers', 'create', 'Create Service Providers', 'create service-providers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(120, 'service-providers', 'edit', 'Edit Service Providers', 'edit service-providers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(121, 'service-providers', 'delete', 'Delete Service Providers', 'delete service-providers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(122, 'service-providers', 'view', 'View Service Providers', 'view service-providers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(123, 'service-providers', 'view_details', 'View_details Service Providers', 'view_details service-providers', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(124, 'sales-agents', 'create', 'Create Sales Agents', 'create sales-agents', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(125, 'sales-agents', 'edit', 'Edit Sales Agents', 'edit sales-agents', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(126, 'sales-agents', 'delete', 'Delete Sales Agents', 'delete sales-agents', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(127, 'sales-agents', 'view', 'View Sales Agents', 'view sales-agents', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(128, 'sales-agents', 'view_details', 'View_details Sales Agents', 'view_details sales-agents', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(129, 'purchase-commission-agent', 'create', 'Create Purchase Commission Agent', 'create purchase-commission-agent', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(130, 'purchase-commission-agent', 'edit', 'Edit Purchase Commission Agent', 'edit purchase-commission-agent', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(131, 'purchase-commission-agent', 'delete', 'Delete Purchase Commission Agent', 'delete purchase-commission-agent', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(132, 'purchase-commission-agent', 'view', 'View Purchase Commission Agent', 'view purchase-commission-agent', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(133, 'purchase-commission-agent', 'view_details', 'View_details Purchase Commission Agent', 'view_details purchase-commission-agent', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(134, 'store-categories', 'create', 'Create Store Categories', 'create store-categories', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(135, 'store-categories', 'edit', 'Edit Store Categories', 'edit store-categories', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(136, 'store-categories', 'delete', 'Delete Store Categories', 'delete store-categories', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(137, 'store-categories', 'view', 'View Store Categories', 'view store-categories', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(138, 'raw-materials', 'create', 'Create Raw Materials', 'create raw-materials', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(139, 'raw-materials', 'edit', 'Edit Raw Materials', 'edit raw-materials', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(140, 'raw-materials', 'delete', 'Delete Raw Materials', 'delete raw-materials', 'web', '2026-02-06 07:13:54', '2026-02-06 07:13:54'),
(141, 'raw-materials', 'view', 'View Raw Materials', 'view raw-materials', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(142, 'brand-categories', 'create', 'Create Brand Categories', 'create brand-categories', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(143, 'brand-categories', 'edit', 'Edit Brand Categories', 'edit brand-categories', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(144, 'brand-categories', 'delete', 'Delete Brand Categories', 'delete brand-categories', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(145, 'brand-categories', 'view', 'View Brand Categories', 'view brand-categories', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(146, 'brands', 'create', 'Create Brands', 'create brands', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(147, 'brands', 'edit', 'Edit Brands', 'edit brands', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(148, 'brands', 'delete', 'Delete Brands', 'delete brands', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(149, 'brands', 'view', 'View Brands', 'view brands', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(150, 'items', 'create', 'Create Items', 'create items', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(151, 'items', 'edit', 'Edit Items', 'edit items', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(152, 'items', 'delete', 'Delete Items', 'delete items', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(153, 'items', 'view', 'View Items', 'view items', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(154, 'purchase-order', 'create', 'Create Purchase Order', 'create purchase-order', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(155, 'purchase-order', 'edit', 'Edit Purchase Order', 'edit purchase-order', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(156, 'purchase-order', 'view', 'View Purchase Order', 'view purchase-order', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(157, 'purchase-order', 'view_details', 'View_details Purchase Order', 'view_details purchase-order', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(158, 'purchase-invoice', 'create', 'Create Purchase Invoice', 'create purchase-invoice', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(159, 'purchase-invoice', 'edit', 'Edit Purchase Invoice', 'edit purchase-invoice', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(160, 'purchase-invoice', 'view', 'View Purchase Invoice', 'view purchase-invoice', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(161, 'purchase-invoice', 'view_details', 'View_details Purchase Invoice', 'view_details purchase-invoice', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(162, 'debit-notes', 'create', 'Create Debit Notes', 'create debit-notes', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(163, 'debit-notes', 'edit', 'Edit Debit Notes', 'edit debit-notes', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(164, 'debit-notes', 'view', 'View Debit Notes', 'view debit-notes', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(165, 'debit-notes', 'view_details', 'View_details Debit Notes', 'view_details debit-notes', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(166, 'grn-entry', 'create', 'Create Grn Entry', 'create grn-entry', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(167, 'grn-entry', 'edit', 'Edit Grn Entry', 'edit grn-entry', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(168, 'grn-entry', 'view', 'View Grn Entry', 'view grn-entry', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(169, 'grn-entry', 'view_details', 'View_details Grn Entry', 'view_details grn-entry', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(170, 'stock-entry', 'create', 'Create Stock Entry', 'create stock-entry', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(171, 'stock-entry', 'edit', 'Edit Stock Entry', 'edit stock-entry', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(172, 'stock-entry', 'view', 'View Stock Entry', 'view stock-entry', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(173, 'stock-entry', 'stock_adjustment', 'Stock_adjustment Stock Entry', 'stock_adjustment stock-entry', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(174, 'stock-entry', 'stock_adjustment_logs', 'Stock_adjustment_logs Stock Entry', 'stock_adjustment_logs stock-entry', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(175, 'stock-consumable-return', 'view', 'View Stock Consumable Return', 'view stock-consumable-return', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(176, 'stock-consumable-return', 'view_details', 'View_details Stock Consumable Return', 'view_details stock-consumable-return', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(177, 'sales-order', 'create', 'Create Sales Order', 'create sales-order', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(178, 'sales-order', 'edit', 'Edit Sales Order', 'edit sales-order', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(179, 'sales-order', 'delete', 'Delete Sales Order', 'delete sales-order', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(180, 'sales-order', 'view', 'View Sales Order', 'view sales-order', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(181, 'sales-invoice', 'create', 'Create Sales Invoice', 'create sales-invoice', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(182, 'sales-invoice', 'edit', 'Edit Sales Invoice', 'edit sales-invoice', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(183, 'sales-invoice', 'delete', 'Delete Sales Invoice', 'delete sales-invoice', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(184, 'sales-invoice', 'view', 'View Sales Invoice', 'view sales-invoice', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(185, 'credit-notes', 'create', 'Create Credit Notes', 'create credit-notes', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(186, 'credit-notes', 'edit', 'Edit Credit Notes', 'edit credit-notes', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(187, 'credit-notes', 'delete', 'Delete Credit Notes', 'delete credit-notes', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(188, 'credit-notes', 'view', 'View Credit Notes', 'view credit-notes', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(189, 'job-card', 'create', 'Create Job Card', 'create job-card', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(190, 'job-card', 'edit', 'Edit Job Card', 'edit job-card', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(191, 'job-card', 'view', 'View Job Card', 'view job-card', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(192, 'job-card', 'view_details', 'View_details Job Card', 'view_details job-card', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(193, 'job-card', 'fabric-consumption-pdf', 'Fabric-consumption-pdf Job Card', 'fabric-consumption-pdf job-card', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(194, 'job-card', 'work-order-pdf', 'Work-order-pdf Job Card', 'work-order-pdf job-card', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(195, 'job-card', 'issue-item', 'Issue-item Job Card', 'issue-item job-card', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(196, 'production', 'create', 'Create Production', 'create production', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(197, 'production', 'edit', 'Edit Production', 'edit production', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(198, 'production', 'view', 'View Production', 'view production', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(199, 'production', 'view_details', 'View_details Production', 'view_details production', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(200, 'production', 'assign_task', 'Assign_task Production', 'assign_task production', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(201, 'task-management', 'edit', 'Edit Task Management', 'edit task-management', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(202, 'task-management', 'view', 'View Task Management', 'view task-management', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(203, 'task-management', 'view_details', 'View_details Task Management', 'view_details task-management', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(204, 'production-receipts', 'create', 'Create Production Receipts', 'create production-receipts', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(205, 'production-receipts', 'edit', 'Edit Production Receipts', 'edit production-receipts', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(206, 'production-receipts', 'view', 'View Production Receipts', 'view production-receipts', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(207, 'billing', 'create', 'Create Billing', 'create billing', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(208, 'billing', 'edit', 'Edit Billing', 'edit billing', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(209, 'billing', 'view', 'View Billing', 'view billing', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(210, 'manage-payments', 'create', 'Create Manage Payments', 'create manage-payments', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(211, 'manage-payments', 'edit', 'Edit Manage Payments', 'edit manage-payments', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(212, 'manage-payments', 'delete', 'Delete Manage Payments', 'delete manage-payments', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(213, 'manage-payments', 'view', 'View Manage Payments', 'view manage-payments', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(214, 'attendance', 'create', 'Create Attendance', 'create attendance', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(215, 'attendance', 'edit', 'Edit Attendance', 'edit attendance', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(216, 'attendance', 'delete', 'Delete Attendance', 'delete attendance', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(217, 'attendance', 'view', 'View Attendance', 'view attendance', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(218, 'manage-leaves', 'create', 'Create Manage Leaves', 'create manage-leaves', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(219, 'manage-leaves', 'edit', 'Edit Manage Leaves', 'edit manage-leaves', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(220, 'manage-leaves', 'delete', 'Delete Manage Leaves', 'delete manage-leaves', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(221, 'manage-leaves', 'view', 'View Manage Leaves', 'view manage-leaves', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(222, 'overtime-bonus', 'create', 'Create Overtime Bonus', 'create overtime-bonus', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(223, 'overtime-bonus', 'edit', 'Edit Overtime Bonus', 'edit overtime-bonus', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(224, 'overtime-bonus', 'delete', 'Delete Overtime Bonus', 'delete overtime-bonus', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(225, 'overtime-bonus', 'view', 'View Overtime Bonus', 'view overtime-bonus', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(226, 'salary-calculation', 'create', 'Create Salary Calculation', 'create salary-calculation', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(227, 'salary-calculation', 'edit', 'Edit Salary Calculation', 'edit salary-calculation', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(228, 'salary-calculation', 'delete', 'Delete Salary Calculation', 'delete salary-calculation', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(229, 'salary-calculation', 'view', 'View Salary Calculation', 'view salary-calculation', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(230, 'payslip-generation', 'create', 'Create Payslip Generation', 'create payslip-generation', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(231, 'payslip-generation', 'edit', 'Edit Payslip Generation', 'edit payslip-generation', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(232, 'payslip-generation', 'delete', 'Delete Payslip Generation', 'delete payslip-generation', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(233, 'payslip-generation', 'view', 'View Payslip Generation', 'view payslip-generation', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(234, 'payroll-reports', 'create', 'Create Payroll Reports', 'create payroll-reports', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(235, 'payroll-reports', 'edit', 'Edit Payroll Reports', 'edit payroll-reports', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(236, 'payroll-reports', 'delete', 'Delete Payroll Reports', 'delete payroll-reports', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(237, 'payroll-reports', 'view', 'View Payroll Reports', 'view payroll-reports', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(238, 'document-repository', 'create', 'Create Document Repository', 'create document-repository', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(239, 'document-repository', 'edit', 'Edit Document Repository', 'edit document-repository', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(240, 'document-repository', 'delete', 'Delete Document Repository', 'delete document-repository', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(241, 'document-repository', 'view', 'View Document Repository', 'view document-repository', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(242, 'log', 'view', 'View Log', 'view log', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(243, 'backup-restore', 'view', 'View Backup Restore', 'view backup-restore', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(244, 'customer-report', 'view', 'View Customer Report', 'view customer-report', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(245, 'sale-report', 'view', 'View Sale Report', 'view sale-report', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(246, 'stock-report', 'view', 'View Stock Report', 'view stock-report', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(247, 'daily-production-report', 'view', 'View Daily Production Report', 'view daily-production-report', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(248, 'order-report', 'view', 'View Order Report', 'view order-report', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(249, 'employee-report', 'view', 'View Employee Report', 'view employee-report', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(250, 'settings', 'edit', 'Edit Settings', 'edit settings', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55'),
(251, 'settings', 'view', 'View Settings', 'view settings', 'web', '2026-02-06 07:13:55', '2026-02-06 07:13:55');

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
(1, 1, 1, 'Keelavasal', 'Residential', NULL, NULL, 'Active', 1, NULL, '2026-02-23 09:26:11', '2026-02-23 09:26:11', NULL),
(2, 1, 1, 'Anna Nagar', 'Commercial', NULL, NULL, 'Active', 1, NULL, '2026-02-26 08:05:36', '2026-02-26 08:05:36', NULL);

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
(1, 'Checked Sleeve', 'Active', 1, NULL, '2026-02-23 09:32:30', '2026-02-23 09:32:30', NULL);

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
(1, 1, NULL, 'Cutting', 1, 100, 'PCS', 1, NULL, '2026-02-23', NULL, '2026-03-04', 'Planned', 1, NULL, '2026-02-23 09:42:11', '2026-02-23 12:42:14', '2026-02-23 12:42:14'),
(2, 1, NULL, 'Cutting', 1, 100, 'PCS', 1, NULL, '2026-02-23', NULL, '2026-03-04', 'Planned', 1, NULL, '2026-02-23 12:42:14', '2026-02-25 08:48:51', '2026-02-25 08:48:51'),
(3, 1, NULL, 'Cutting', 1, 100, 'PCS', 1, NULL, '2026-02-23', NULL, '2026-03-04', 'Planned', 1, NULL, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `production_receipts`
--

CREATE TABLE `production_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `production_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_card_id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
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

INSERT INTO `production_receipts` (`id`, `production_id`, `job_card_id`, `customer_name`, `order_due_date`, `receipt_no`, `receipt_date`, `doc_no`, `doc_date`, `store_type_id`, `store_location_id`, `status`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 'Shri', '2026-03-13', '1001', '2026-02-23', 'JC001', '2026-02-23', 1, 1, 'Posted', NULL, 1, 1, '2026-02-23 12:46:06', '2026-02-23 12:50:40');

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

--
-- Dumping data for table `production_receipt_items`
--

INSERT INTO `production_receipt_items` (`id`, `production_receipt_id`, `item_id`, `item_code`, `item_name`, `art_no`, `size`, `unit_price`, `total_value`, `description`, `size_variant`, `uom_id`, `uom_code`, `ordered_qty`, `completed_qty`, `qty_already_received`, `scan_qty`, `damage_qty`, `qty_to_receive`, `balance_qty`, `created_at`, `updated_at`) VALUES
(9, 1, 1, '1001 - F/S', 'Checked Sleeve', NULL, '38', 178.68, 1786.80, '', '38 - F/S', 1, 'PCS', 10.00, 10.00, 0.00, 10.00, 0.00, 10.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40'),
(10, 1, 1, '1001 - H/S', 'Checked Sleeve', NULL, '38', 138.96, 2084.40, '', '38 - H/S', 1, 'PCS', 15.00, 15.00, 0.00, 15.00, 0.00, 15.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40'),
(11, 1, 1, '1001 - F/S', 'Checked Sleeve', NULL, '40', 178.68, 1786.80, '', '40 - F/S', 1, 'PCS', 10.00, 10.00, 0.00, 10.00, 0.00, 10.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40'),
(12, 1, 1, '1001 - H/S', 'Checked Sleeve', NULL, '40', 138.96, 2084.40, '', '40 - H/S', 1, 'PCS', 15.00, 15.00, 0.00, 15.00, 0.00, 15.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40'),
(13, 1, 1, '1001 - F/S', 'Checked Sleeve', NULL, '42', 178.68, 1786.80, '', '42 - F/S', 1, 'PCS', 10.00, 10.00, 0.00, 10.00, 0.00, 10.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40'),
(14, 1, 1, '1001 - H/S', 'Checked Sleeve', NULL, '42', 138.96, 2084.40, '', '42 - H/S', 1, 'PCS', 15.00, 15.00, 0.00, 15.00, 0.00, 15.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40'),
(15, 1, 1, '1001 - F/S', 'Checked Sleeve', NULL, '44', 178.68, 1786.80, '', '44 - F/S', 1, 'PCS', 10.00, 10.00, 0.00, 10.00, 0.00, 10.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40'),
(16, 1, 1, '1001 - H/S', 'Checked Sleeve', NULL, '44', 138.96, 2084.40, '', '44 - H/S', 1, 'PCS', 15.00, 15.00, 0.00, 15.00, 0.00, 15.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40');

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
(1, 'Fabric Cutting', 'FB-CUT', 1, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-02-23 10:02:25', '2026-02-23 10:02:25', NULL),
(2, 'Fabric Spreading', 'FAB-SPRD', 2, 'Active', 'ALL', 'Total Qty', 1.00, 'PCS', 1, NULL, '2026-02-26 08:49:24', '2026-02-26 08:49:24', NULL);

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
(5, 1, NULL, 1, 'Cutting', 'CF-123451', 'Consumable', 52.800, 62.400, 115.200, 1, 0.0000, 100.0000, 115.2000, 2, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-123451', 1, NULL, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL),
(6, 1, NULL, 1, 'Cutting', 'CF-123452', 'Consumable', 320.000, 360.000, 680.000, 2, 0.0000, 100.0000, 680.0000, 1, 'All', 'Active', 'Derived from Job Card Fabric Details. Article: CF-123452', 1, NULL, '2026-02-25 08:48:51', '2026-02-25 08:48:51', NULL);

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
(1, 'Shyam', '1001', 'shyam@gmail.com', '9192394949', 'Active', 1, NULL, 1, 1, 2, '12, Bye pass road', NULL, '625011', 'Eswar', 'Employee', '9632587400', 'eswar@gmail.com', 'AADFA4747M', '33AADFA4747M1ZD', NULL, '2026-02-26 11:12:38', '2026-02-26 11:12:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_invoices`
--

CREATE TABLE `purchase_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(100) NOT NULL,
  `invoice_date` date NOT NULL,
  `purchase_order_id` int(11) NOT NULL DEFAULT 0,
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_invoices`
--

INSERT INTO `purchase_invoices` (`id`, `invoice_no`, `invoice_date`, `purchase_order_id`, `supplier_id`, `po_reference`, `sub_total`, `discount_percent`, `discount_amount`, `taxable_amount`, `other_state`, `igst_percent`, `igst_amount`, `cgst_percent`, `cgst_amount`, `sgst_percent`, `sgst_amount`, `tax_amount`, `other_charges`, `round_off_type`, `round_off`, `grand_total`, `received_amount`, `due_amount`, `invoice_status`, `created_by`, `updated_by`, `payment_mode`, `transaction_id`, `due_date`, `notes`, `auth_signature`, `attachments`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'INV-001', '2026-02-23', 1, 1, 'PO-001', 16650.00, 0.00, 0.00, 16650.00, '0', 9.00, 0.00, 18.00, 2997.00, 9.00, 1498.50, 4495.50, 0.00, 'Add', 0.00, 21145.50, 0.00, 21145.50, 'Unpaid/Credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 09:31:06', '2026-02-23 09:31:09', NULL);

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
  `hsn_code` varchar(10) DEFAULT NULL,
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

INSERT INTO `purchase_invoice_items` (`id`, `purchase_invoice_id`, `purchase_order_item_id`, `raw_material_id`, `hsn_code`, `quantity`, `uom_id`, `rate`, `amount`, `qty_ordered`, `qty_received`, `qty_invoiced`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, NULL, 150.00, 2, 99.00, 14850.00, 150.00, 150.00, 150.00, '2026-02-23 09:31:06', '2026-02-23 09:31:06', NULL),
(2, 1, 2, 2, NULL, 300.00, 1, 6.00, 1800.00, 300.00, 300.00, 300.00, '2026-02-23 09:31:06', '2026-02-23 09:31:06', NULL);

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

INSERT INTO `purchase_orders` (`id`, `po_number`, `po_date`, `purchase_commission_agent_id`, `commission`, `supplier_id`, `reference_no`, `reference_date`, `due_date`, `store_type_id`, `payment_terms`, `status`, `additional_attachments`, `created_by`, `updated_by`, `total_qty`, `sub_total`, `discount_percent`, `discount_amount`, `taxable_amount`, `other_state`, `igst_percent`, `cgst_percent`, `sgst_percent`, `tax_amount`, `round_off_type`, `round_off`, `total_amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PO-001', '2026-03-03', NULL, 0.00, 1, 'PO-001', '2026-02-23', '2026-03-13', 1, NULL, 'Received', 'additional_1772541471.jpg', NULL, NULL, 450.00, 16650.00, 0.00, 0.00, 16650.00, 0, 9.00, 9.00, 9.00, 2997.00, 'Add', 0.00, 19647.00, '2026-02-23 09:30:28', '2026-03-03 12:37:51', NULL);

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
  `fabric_width_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `art_no` varchar(50) DEFAULT NULL,
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

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `store_category_id`, `raw_material_id`, `uom_id`, `color_id`, `style_id`, `brand_id`, `fabric_width_id`, `quantity`, `art_no`, `rate`, `amount`, `remarks`, `attached_file`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 1, 1, 1, 2, NULL, NULL, NULL, NULL, 150.00, NULL, 99.00, 14850.00, NULL, NULL, '2026-03-03 12:37:51', '2026-03-03 12:37:51', NULL),
(4, 1, 2, 2, 1, NULL, NULL, NULL, NULL, 300.00, NULL, 6.00, 1800.00, NULL, NULL, '2026-03-03 12:37:51', '2026-03-03 12:37:51', NULL);

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
  `fabric_type_id` bigint(20) UNSIGNED DEFAULT NULL,
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

INSERT INTO `raw_materials` (`id`, `store_category_id`, `code`, `name`, `supplier_design_name`, `size_width`, `uom_id`, `fabric_type_id`, `reference_image`, `specification`, `min_stock`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, '1000', 'Cotton Fabric', NULL, NULL, 2, NULL, NULL, NULL, 0, 'Active', 1, NULL, NULL, '2026-02-23 09:28:32', '2026-02-23 09:28:32'),
(2, 2, '1001', 'Buttons', NULL, NULL, 1, NULL, NULL, NULL, 0, 'Active', 1, NULL, NULL, '2026-02-23 09:29:02', '2026-02-23 09:29:02');

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
(1, 'Manageer', 'web', NULL, NULL, 'Active', '2026-02-23 09:36:53', '2026-02-23 09:36:53', NULL),
(2, 'Assistant Manager', 'web', NULL, NULL, 'Active', '2026-02-23 09:37:02', '2026-02-23 09:37:02', NULL),
(3, 'Executive', 'web', NULL, NULL, 'Active', '2026-02-23 09:37:11', '2026-02-23 09:37:11', NULL),
(4, 'Quality Checker', 'web', NULL, NULL, 'Active', '2026-02-23 09:37:31', '2026-02-23 09:37:31', NULL),
(5, 'Supervisior', 'web', NULL, NULL, 'Active', '2026-02-23 09:37:44', '2026-02-23 09:37:44', NULL);

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
(1, 1, 5, NULL, NULL),
(2, 2, 5, NULL, NULL),
(3, 4, 5, NULL, NULL),
(4, 3, 5, NULL, NULL);

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
  `mobile_no` varchar(15) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `place_id` bigint(20) UNSIGNED DEFAULT NULL,
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

INSERT INTO `sales_agents` (`id`, `agent_type`, `name`, `code`, `email`, `mobile_no`, `status`, `created_by`, `updated_by`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `zip_code`, `contact_person_name`, `designation`, `contact_phone_number`, `contact_email`, `pan_no`, `gst_no`, `commission_value`, `sales_target`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Direct Sales Agent', 'Akshan', '1001', 'akshan@gmail.com', '6568932140', 'Active', 1, 1, 1, 1, 2, '25, West Street', NULL, '625011', 'Vinoth', 'Employee', '9292938448', 'vinoth34@gmail.com', 'AADFA4747M', '33AADFA4747M1ZD', 3.00, 3.00, '2026-02-26 10:20:10', '2026-02-26 10:20:36', NULL);

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

--
-- Dumping data for table `sales_invoices`
--

INSERT INTO `sales_invoices` (`id`, `inv_no`, `inv_date`, `so_id`, `customer_id`, `delivery_address`, `remarks`, `invoice_status`, `payment_mode`, `extra_input`, `due_date`, `notes`, `signature_file`, `attachment_file`, `show_fields`, `sub_total`, `discount_percent`, `discount`, `total`, `other_state`, `igst`, `cgst`, `sgst`, `tax_amount`, `igst_percent`, `cgst_percent`, `sgst_percent`, `other_charges`, `round_off_type`, `round_off`, `grand_total`, `received_amount`, `due_amount`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SINV-0001', '2026-03-03', 1, 1, '9, Navabathkana Street, Mahal Area, Madurai Main,', NULL, 'Unpaid/Credit', 'Cash', NULL, '2026-03-03', NULL, NULL, NULL, '[\"amount\",\"discount\",\"tax\",\"subtotal\",\"grandtotal\"]', 317.64, 2.00, 6.35, 311.29, 0, 0.00, 28.02, 28.02, 56.03, 18.00, 9.00, 9.00, 0.00, 'Add', 0.00, 367.32, 0.00, 367.32, NULL, NULL, '2026-03-03 08:43:59', '2026-03-03 08:51:45', NULL);

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

--
-- Dumping data for table `sales_invoice_items`
--

INSERT INTO `sales_invoice_items` (`id`, `sales_invoice_id`, `brand_id`, `item_id`, `uom_id`, `quantity`, `rate`, `mrp`, `amount`, `hsn_sac`, `art_no`, `size`, `sleeve_type`, `stock_entry_item_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 1, 1, 1, 1, 1.00, 178.68, 360.00, 178.68, '62053000', 'CF12345-1', '40', 'Full', 5, '2026-03-03 08:51:45', '2026-03-03 08:51:45', NULL),
(4, 1, 1, 1, 1, 1.00, 138.96, 360.00, 138.96, '62053000', 'CF12345-2', '38', 'Half', 4, '2026-03-03 08:51:45', '2026-03-03 08:51:45', NULL);

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
  `delivery_date` date DEFAULT NULL,
  `shipping_method` varchar(50) DEFAULT NULL,
  `transport_mode` varchar(50) DEFAULT NULL,
  `dispatch_from` varchar(255) DEFAULT NULL,
  `transporter_name` varchar(255) DEFAULT NULL,
  `freight_type` enum('Paid','To Pay') DEFAULT NULL,
  `freight_amount` decimal(10,2) DEFAULT 0.00,
  `eway_bill_no` varchar(50) DEFAULT NULL,
  `lr_no` varchar(50) DEFAULT NULL,
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

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `so_no`, `so_date`, `request_date`, `order_type`, `season_id`, `customer_id`, `customer_po_ref`, `store_id`, `billing_address`, `shipping_address`, `payment_terms`, `agent_id`, `delivery_date`, `shipping_method`, `transport_mode`, `dispatch_from`, `transporter_name`, `freight_type`, `freight_amount`, `eway_bill_no`, `lr_no`, `dispatch_through`, `status`, `approved_by`, `approved_date`, `total_qty`, `sub_total_qty`, `commission_percent`, `commission_amount`, `discount_percent`, `discount_amount`, `taxable_amount`, `other_state`, `igst_percent`, `cgst_percent`, `sgst_percent`, `tax_amount`, `round_off_type`, `round_off`, `total_amount`, `internal_remarks`, `terms_conditions`, `attachment`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SO-001', '2026-03-03', '2026-03-03', 'Regular', 1, 1, NULL, 1, '9, Navabathkana Street, Mahal Area, Madurai Main,', '9, Navabathkana Street, Mahal Area, Madurai Main,', 'Advance Payment', 1, '2026-03-06', 'BlueDart', 'By Hand', NULL, 'Thirumalammal Lorry Booking Office', 'Paid', 0.00, NULL, NULL, NULL, 'Approved', NULL, NULL, 2.00, 317.64, 2.00, 6.35, 2.00, 6.35, 311.29, 0, 18.00, 9.00, 9.00, 56.03, 'Less', 0.32, 367.00, NULL, '1. Goods once sold will not be taken back.\r\n2. Interest @ 18% p.a. will be charged if payment is not made within the stipulated time.', 'attach_1772254909_69a276bdb13d1.png', 1, 1, '2026-02-27 09:52:31', '2026-03-03 07:01:40', NULL);

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

--
-- Dumping data for table `sales_order_items`
--

INSERT INTO `sales_order_items` (`id`, `sale_order_id`, `brand_cat_id`, `item_id`, `stock_entry_item_id`, `color_id`, `art_no`, `uom_id`, `size_id`, `qty`, `rate`, `mrp`, `amount`, `sleeve`, `created_at`, `updated_at`, `deleted_at`) VALUES
(27, 1, 1, 1, 5, 1, 'CF12345-1', 1, '40', 1.00, 178.68, 360.00, 178.68, '[\"Full\"]', '2026-03-03 07:01:40', '2026-03-03 07:01:40', NULL),
(28, 1, 1, 1, 4, 1, 'CF12345-2', 1, '38', 1.00, 138.96, 360.00, 138.96, '[\"Half\"]', '2026-03-03 07:01:40', '2026-03-03 07:01:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE `seasons` (
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
-- Dumping data for table `seasons`
--

INSERT INTO `seasons` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Summer', 'Active', 1, NULL, '2026-02-26 08:41:29', '2026-02-26 08:41:29', NULL);

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
(1, 1, 'Nachias Fashion Private Limited', 'NFPL', 1, 'ushadevi.saitech@gmail.com', '9685741200', '625011', NULL, 'Per Agent', 'Active', 1, NULL, 1, 1, 1, 'Jaihindpuram', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 09:34:24', '2026-02-23 09:34:24', NULL),
(2, 2, 'Samayanallur Unit', 'SMLR', 1, NULL, '6985232541', '625011', NULL, 'Per Agent', 'Active', 1, 1, 1, 1, 2, '12, Samayanallur', NULL, 'Priya', 'Employee', '93938383832', 'priyaarjun@gmail.com', 'AADFA4747M', '33AADFA4747M1ZD', NULL, 'State Bank of India', '123456789012', 'SBIN0001234', 'New customers may require part/100% upfront payment', '2026-02-26 10:06:53', '2026-02-26 10:10:06', NULL);

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
(1, 'Nachias Fashion Private Limited', 'srinachias@yahoo.in,sales@nachias.com', NULL, '8489938071', '8489938071,8489938073', 1, 1, '272/2, Somu Nagar, Siringeri Nagar\r\n(Sarathambal Kovil Backside),\r\nByepass Road, Madurai - 625016', 9, 9, 18, NULL, '33AADCN9342A1ZU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-20 08:49:21', '2026-03-02 11:24:56', NULL);

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
(1, '38,40,42,44', '9,5,4,6', 'Active', NULL, 1, NULL, '2026-02-23 09:32:50', '2026-02-26 08:16:51');

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
(1, 'TN', 'Tamil Nadu', 'Active', '2026-02-23 09:24:55', '2026-02-23 09:24:55', NULL, 1, NULL),
(2, 'AP', 'Andhra Pradesh', 'Active', '2026-02-26 07:08:55', '2026-02-26 07:08:55', NULL, 1, NULL);

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
(1, 'SE00001', 99.00, '2026-02-23', 'Raw Material', 1, NULL, 1, NULL, NULL, 'Draft', 1, NULL, '2026-02-23 09:32:04', '2026-02-23 09:32:04', NULL),
(2, 'SE00002', 6.00, '2026-02-23', 'Raw Material', 1, NULL, 1, NULL, NULL, 'Draft', 1, NULL, '2026-02-23 09:32:09', '2026-02-23 09:32:09', NULL),
(3, 'SE00003', 15484.80, '2026-02-23', 'Finished Goods', NULL, NULL, 1, NULL, '1001', 'Posted', 1, NULL, '2026-02-23 12:50:40', '2026-02-23 12:50:40', NULL);

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
(1, 'ADJ-SE-20260223-0001', 2, 2, 380.00, 300.00, 680.00, 'test', 'test', 'Posted', 1, NULL, NULL, '2026-02-23 09:40:59', '2026-02-23 09:40:59');

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
  `finished_item_code` varchar(255) DEFAULT NULL COMMENT 'TEMPORARY: Replace with finished_good_id FK when finished_goods table exists',
  `size` varchar(255) DEFAULT NULL,
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

INSERT INTO `stock_entry_items` (`id`, `stock_entry_id`, `stock_type`, `grn_entry_item_id`, `art_no`, `raw_material_id`, `finished_item_code`, `size`, `size_ratio_id`, `store_category_id`, `store_location_id`, `uom_id`, `qty_in`, `qty_out`, `created_at`, `updated_at`, `deleted_at`, `price`) VALUES
(1, 1, 'raw_material', 1, 'CF-123451', 1, NULL, NULL, NULL, 1, 1, 2, 150.00, 0.00, '2026-02-23 09:32:04', '2026-02-23 09:32:04', NULL, 99.00),
(2, 2, 'raw_material', 2, 'CF-123452', 2, NULL, NULL, NULL, 2, 1, 1, 680.00, 0.00, '2026-02-23 09:32:09', '2026-02-23 09:40:59', NULL, 6.00),
(3, 3, 'finished_goods', NULL, NULL, NULL, '1001 - F/S', '38', NULL, NULL, 1, 1, 10.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40', NULL, 178.68),
(4, 3, 'finished_goods', NULL, NULL, NULL, '1001 - H/S', '38', NULL, NULL, 1, 1, 15.00, 1.00, '2026-02-23 12:50:40', '2026-03-03 08:51:45', NULL, 138.96),
(5, 3, 'finished_goods', NULL, NULL, NULL, '1001 - F/S', '40', NULL, NULL, 1, 1, 10.00, 1.00, '2026-02-23 12:50:40', '2026-03-03 08:51:45', NULL, 178.68),
(6, 3, 'finished_goods', NULL, NULL, NULL, '1001 - H/S', '40', NULL, NULL, 1, 1, 15.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40', NULL, 138.96),
(7, 3, 'finished_goods', NULL, NULL, NULL, '1001 - F/S', '42', NULL, NULL, 1, 1, 10.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40', NULL, 178.68),
(8, 3, 'finished_goods', NULL, NULL, NULL, '1001 - H/S', '42', NULL, NULL, 1, 1, 15.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40', NULL, 138.96),
(9, 3, 'finished_goods', NULL, NULL, NULL, '1001 - F/S', '44', NULL, NULL, 1, 1, 10.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40', NULL, 178.68),
(10, 3, 'finished_goods', NULL, NULL, NULL, '1001 - H/S', '44', NULL, NULL, 1, 1, 15.00, 0.00, '2026-02-23 12:50:40', '2026-02-23 12:50:40', NULL, 138.96);

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
(1, 'FBC', 'Fabric', NULL, 'Active', NULL, 1, NULL, '2026-02-23 09:28:02', '2026-02-23 09:28:02'),
(2, 'ACC', 'Accessories', NULL, 'Active', NULL, 1, NULL, '2026-02-23 09:28:45', '2026-02-23 09:28:45');

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
(1, 'S1', 'Active', 1, NULL, '2026-02-23 09:31:47', '2026-02-23 09:31:47', NULL),
(2, 'S2', 'Active', 1, NULL, '2026-02-26 08:21:16', '2026-02-26 08:21:16', NULL);

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
(1, 'Fabric Store', 'Active', 1, NULL, '2026-02-23 14:59:48', '2026-02-23 14:59:48', NULL),
(2, 'Finished Goods', 'Active', 1, NULL, '2026-02-26 14:01:29', '2026-02-26 14:01:29', NULL);

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
(1, 'Plain', 'PLN', 'Active', 1, NULL, NULL, '2026-02-26 08:25:10', '2026-02-26 08:25:10');

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

INSERT INTO `suppliers` (`id`, `name`, `code`, `mobile_no`, `email`, `website_url`, `transport_name`, `booking_area`, `stores`, `store_id`, `status`, `created_by`, `updated_by`, `state_id`, `city_id`, `place_id`, `address_line_1`, `address_line_2`, `address_line_3`, `zip_code`, `contact_person_name`, `designation`, `contact_mobile_no`, `contact_email`, `purchase_commission_agent_id`, `commission_percentage`, `gst_no`, `tax_id`, `pan_no`, `ecc_no`, `credit_limit`, `payment_terms`, `bank_name`, `branch`, `account_number`, `ifsc_code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Shri', '1000', '8585858585', 'ushadevi.saitech@gmail.com', NULL, NULL, NULL, NULL, NULL, 'Active', 1, NULL, 1, 1, 1, 'Jaihindpuram', NULL, NULL, '625011', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-02-23 09:27:42', '2026-02-23 09:27:42', NULL),
(2, 'TAJ DISTRIBUTORS', '1001', '9965221102', 'taj323@gmail.com', 'https://www.tajinternationalexports.in/', NULL, NULL, NULL, 2, 'Active', 1, NULL, 1, 1, 1, 'No. 19, 2nd Floor, West Masi Street,', NULL, NULL, '625011', 'Naaven', 'Employee', '9696985201', 'naveen@gmail.com', NULL, 0.00, '33ACGPF5198L1ZB', NULL, 'ACGPF5198L', '0415038723', 5.00, 'Net 30 / Net 45 days from invoice (subject to credit approval)', 'State Bank of India', 'Anna Nagar', '123456789012', 'SBIN0001233', '2026-02-26 09:49:51', '2026-02-26 09:49:51', NULL);

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
(1, 'TASK-001', 1, 'JC001', 1, '[\"1\"]', 2, '2026-02-24', '2026-02-25', NULL, 100.00, '1', NULL, 'In Progress', 1, 1, '2026-02-24 06:49:47', '2026-02-25 08:34:58', NULL);

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

--
-- Dumping data for table `task_adjustments`
--

INSERT INTO `task_adjustments` (`id`, `adjustment_no`, `reference_no`, `task_id`, `job_card_id`, `affected_stage`, `approved_by`, `overall_reason`, `attachment`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ADJ-2026-001', NULL, 1, 1, NULL, 'test', 'tsdsd', NULL, 'Posted', 1, NULL, '2026-02-24 07:07:27', '2026-02-24 07:07:27', NULL);

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

--
-- Dumping data for table `task_adjustment_items`
--

INSERT INTO `task_adjustment_items` (`id`, `task_adjustment_id`, `raw_material_id`, `grn_no`, `art_no`, `service_id`, `adjustment_type`, `qty`, `remarks`, `previous_stock`, `new_stock`, `created_at`, `updated_at`) VALUES
(2, 1, 1, 'GRN001', 'CF-123451', 1, 'Loss', 5.00, '', 150.00, 145.00, '2026-02-24 07:07:28', '2026-02-24 07:07:28');

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
(1, 1, 2, 1, '2026-02-24', '2026-02-25', 100.00, 52.00, 48.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'In Progress', NULL, 1, NULL, '2026-02-25 08:34:58', '2026-02-24 06:49:47', '2026-02-25 08:34:58'),
(2, 1, 2, 1, '2026-02-25', '2026-02-28', 100.00, 0.00, 100.00, 0.00, 0, 0, 0, 'Pending', 96.00, 'Open', NULL, 1, NULL, '2026-02-25 08:34:58', '2026-02-24 06:49:47', '2026-02-25 08:34:58'),
(3, 1, 2, 1, '2026-02-24', '2026-02-25', 100.00, 100.00, 0.00, 0.00, 0, 0, 0, 'Pending', 48.00, 'Completed', NULL, 1, NULL, NULL, '2026-02-25 08:34:58', '2026-02-25 12:15:38'),
(4, 1, 2, 1, '2026-02-25', '2026-02-28', 100.00, 0.00, 100.00, 0.00, 0, 0, 0, 'Pending', 96.00, 'Open', NULL, 1, NULL, NULL, '2026-02-25 08:34:58', '2026-02-25 12:15:38'),
(5, 1, 2, 1, '2026-02-25', '2026-02-28', 100.00, 0.00, 100.00, 0.00, 0, 0, 0, 'Pending', 96.00, 'Open', NULL, 1, NULL, NULL, '2026-02-25 08:34:58', '2026-02-25 12:15:38');

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
(1, 1, 1, 'Created', 'Task created with ticket number TASK-001', '2026-02-24 06:49:47', '2026-02-24 06:49:47'),
(2, 1, 1, 'Progress Update', 'Updated progress for **Usha**: Completed: 0 -> 52', '2026-02-24 06:59:39', '2026-02-24 06:59:39'),
(3, 1, 1, 'Status Change', 'Task status automatically updated to In Progress', '2026-02-24 06:59:39', '2026-02-24 06:59:39'),
(4, 1, 1, 'Adjustment', 'Adjustment **ADJ-2026-001** posted for reason: tsdsd. Items: Cotton Fabric (Loss: 5)', '2026-02-24 07:07:28', '2026-02-24 07:07:28'),
(5, 1, 1, 'Updated', 'Task updated: Stage changed', '2026-02-25 08:34:58', '2026-02-25 08:34:58'),
(6, 1, 1, 'Progress Update', 'Updated progress for **Usha**: Completed: 0 -> 100', '2026-02-25 12:15:38', '2026-02-25 12:15:38');

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
(1, 'PCS', 'Pieces', NULL, 'Active', 1, NULL, '2026-02-23 09:26:27', '2026-02-23 09:26:27', NULL),
(2, 'MTR', 'Meter', NULL, 'Active', 1, NULL, '2026-02-23 09:26:41', '2026-02-23 09:26:41', NULL),
(3, 'G', 'Gram', NULL, 'Active', 1, NULL, '2026-02-26 08:07:26', '2026-02-26 08:07:26', NULL);

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
(1, NULL, NULL, NULL, NULL, NULL, NULL, 'Admin', '8520147963', 'admin@gmail.com', NULL, '$2y$10$4F7zEd8RmTgdr.djAWj0aOOrvYZotpY.9g9B030jiuyJ0Sj3ZsdUu', 'g68hChNSwMPIqVjzT3ASHFCRFR75Vp6FnuMj9DSTyYotzY0acXbgx07JKyD5', NULL, NULL, NULL, '2026-02-26 06:02:14', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'profile.png', NULL, NULL, NULL, NULL, 'Active', NULL),
(2, 1, 1, '1001', 1, 5, NULL, 'Usha', '8585858585', 'usha@gmail.com', NULL, '$2y$10$RfuOS/3hW1fSOPB5XP9hq.m/Xe6BUYIlvtDhCv1VGAyUe1EHbWzX6', 'hNbjHH7r1EydbzoPq4WEZ3HyuUHAG5szRP8XosAxVuu3nen8zPahqnXTAAOP', 1, 1, '2026-02-23 09:38:53', '2026-02-26 05:56:19', NULL, NULL, NULL, NULL, 1, 1, 'Jaihindpuram', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', NULL),
(3, 1, 1, '1002', 1, 5, 7, 'Krithika', '8520630369', 'krithika23@gmail.com', NULL, '$2y$10$orVHcNBHfaHMZTEkQYRcq.Go.pgVIGQUXCUqGiZoROrps69ByLy1K', NULL, 1, 1, '2026-02-26 06:56:45', '2026-02-26 07:03:52', '2024-02-26', 'Iyyappan K', '9632569632', NULL, 1, 1, '25, Kamarajar Salai', NULL, '625011', 'Annamalai', '8967410025', 'annamalai23@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '123456789012', 'State Bank of India', 'SBIN0001234', 'profile.jpg', 'esi_document.pdf', 'pf_document.pdf', 'aadhaar_document.png', 'pan_document.png', 'Active', NULL);

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
(1, 'South', 1, '1', 'Active', '2026-02-26 08:13:12', '2026-02-26 08:13:12', NULL, 1, NULL);

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
-- Indexes for table `job_card_matrix_quantities`
--
ALTER TABLE `job_card_matrix_quantities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jc_matrix_quant_id` (`job_card_fabric_detail_id`);

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
  ADD KEY `sales_agents_place_id_foreign` (`place_id`);

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
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `stock_entry_items_size_ratio_id_foreign` (`size_ratio_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blood_groups`
--
ALTER TABLE `blood_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bottom_cuts`
--
ALTER TABLE `bottom_cuts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brand_categories`
--
ALTER TABLE `brand_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `charges`
--
ALTER TABLE `charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `collar_types`
--
ALTER TABLE `collar_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `credit_notes`
--
ALTER TABLE `credit_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cuff_types`
--
ALTER TABLE `cuff_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `debit_notes`
--
ALTER TABLE `debit_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `debit_note_items`
--
ALTER TABLE `debit_note_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `document_repositories`
--
ALTER TABLE `document_repositories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grn_entries`
--
ALTER TABLE `grn_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grn_entry_items`
--
ALTER TABLE `grn_entry_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grn_entry_item_variants`
--
ALTER TABLE `grn_entry_item_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_card_cutting_size_ratios`
--
ALTER TABLE `job_card_cutting_size_ratios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `job_card_entries`
--
ALTER TABLE `job_card_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `job_card_fabric_consumptions`
--
ALTER TABLE `job_card_fabric_consumptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `job_card_fabric_details`
--
ALTER TABLE `job_card_fabric_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `job_card_images`
--
ALTER TABLE `job_card_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_card_issue_items`
--
ALTER TABLE `job_card_issue_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_card_issue_stock_details`
--
ALTER TABLE `job_card_issue_stock_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_card_matrix_quantities`
--
ALTER TABLE `job_card_matrix_quantities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `job_card_operations`
--
ALTER TABLE `job_card_operations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_card_sleeve_meters`
--
ALTER TABLE `job_card_sleeve_meters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT for table `operation_stages`
--
ALTER TABLE `operation_stages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patti_types`
--
ALTER TABLE `patti_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;

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
-- AUTO_INCREMENT for table `pocket_types`
--
ALTER TABLE `pocket_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `process_groups`
--
ALTER TABLE `process_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `process_schedules`
--
ALTER TABLE `process_schedules`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `production_services`
--
ALTER TABLE `production_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `production_stage_consumables`
--
ALTER TABLE `production_stage_consumables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchase_commission_agents`
--
ALTER TABLE `purchase_commission_agents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_invoices`
--
ALTER TABLE `purchase_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_invoice_charges`
--
ALTER TABLE `purchase_invoice_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_invoice_items`
--
ALTER TABLE `purchase_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_invoice_payments`
--
ALTER TABLE `purchase_invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales_agents`
--
ALTER TABLE `sales_agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_invoices`
--
ALTER TABLE `sales_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `service_providers`
--
ALTER TABLE `service_providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `size_ratios`
--
ALTER TABLE `size_ratios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock_entry_adjustments`
--
ALTER TABLE `stock_entry_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_entry_items`
--
ALTER TABLE `stock_entry_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `store_categories`
--
ALTER TABLE `store_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `store_locations`
--
ALTER TABLE `store_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `store_types`
--
ALTER TABLE `store_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `styles`
--
ALTER TABLE `styles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `task_adjustments`
--
ALTER TABLE `task_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `task_adjustment_items`
--
ALTER TABLE `task_adjustment_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `task_assign_employees`
--
ALTER TABLE `task_assign_employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `task_logs`
--
ALTER TABLE `task_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `task_status`
--
ALTER TABLE `task_status`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uoms`
--
ALTER TABLE `uoms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `sales_agents_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_service_provider_id_foreign` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
