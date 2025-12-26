-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2025 at 08:56 PM
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
-- Database: `mobile_shop_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Super','Manager','Staff') DEFAULT 'Staff',
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'User', 'admin@shop.com', '0192023a7bbd73250516f069df18b500', 'Super', 1, '2025-12-25 21:26:37', '2025-12-25 21:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `cnic` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `current_balance` decimal(12,2) DEFAULT 0.00 COMMENT 'Amount customer owes',
  `credit_limit` decimal(10,2) DEFAULT 0.00,
  `total_purchases` decimal(12,2) DEFAULT 0.00,
  `total_payments` decimal(12,2) DEFAULT 0.00,
  `status` enum('active','blocked','inactive') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `cnic`, `address`, `current_balance`, `credit_limit`, `total_purchases`, `total_payments`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Ahmed Khan', '0300-1234567', 'ahmed@example.com', NULL, 'Shop #12, Main Market, Karachi', 0.00, 50000.00, 0.00, 0.00, 'inactive', NULL, '2025-12-25 21:39:19', '2025-12-26 00:37:51'),
(2, 'Sara Ali', '0321-9876543', 'sara@example.com', NULL, 'House #45, Gulshan-e-Iqbal, Karachi', 0.00, 30000.00, 0.00, 0.00, 'active', NULL, '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(3, 'Hassan Raza', '0333-5555555', 'hassan@example.com', NULL, 'Flat #3, Clifton, Karachi', 0.00, 100000.00, 0.00, 0.00, 'active', NULL, '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(4, 'Fatima Malik', '0345-7777777', 'fatima@example.com', '', 'Block A, North Nazimabad, Karachi', 0.00, 2500000.00, 0.00, 0.00, 'active', '', '2025-12-25 21:39:19', '2025-12-26 00:38:14'),
(5, 'Bilal Ahmed', '0312-8888888', NULL, NULL, 'Saddar, Karachi', 0.00, 20000.00, 0.00, 0.00, 'inactive', NULL, '2025-12-25 21:39:19', '2025-12-26 00:37:57'),
(11, 'Shehrose Jamshaid', '03220716197', 'sarmadsahib786@gmail.com', '434243242342', 'Loran Gali No 1', 0.00, 3333.00, 0.00, 0.00, 'active', '', '2025-12-25 23:12:33', '2025-12-25 23:12:33'),
(12, 'Ali', '03120716197', 'ali@gmail.com', '434243242342', 'Loran Gali No 1', 0.00, 11000.00, 0.00, 0.00, 'inactive', '', '2025-12-26 00:47:59', '2025-12-26 01:12:03');

-- --------------------------------------------------------

--
-- Table structure for table `customer_ledger`
--

CREATE TABLE `customer_ledger` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_type` enum('credit_sale','payment','adjustment','return') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventlog`
--

CREATE TABLE `eventlog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event` varchar(100) NOT NULL,
  `eventRowIdOrRef` varchar(50) NOT NULL,
  `eventDesc` text NOT NULL,
  `eventTable` varchar(50) NOT NULL,
  `staffId` int(11) NOT NULL,
  `eventDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `inventory_available`
-- (See below for the actual view)
--
CREATE TABLE `inventory_available` (
`id` bigint(20) unsigned
,`name` varchar(100)
,`code` varchar(50)
,`brand` varchar(50)
,`model` varchar(50)
,`category` enum('mobile','accessory','other')
,`item_type` enum('standard','serialized')
,`unitPrice` decimal(10,2)
,`warranty_months` int(11)
,`quantity` int(11)
,`available_qty` bigint(21)
,`total_value` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `category` enum('mobile','accessory','other') DEFAULT 'other',
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `item_type` enum('standard','serialized') DEFAULT 'standard',
  `warranty_months` int(11) DEFAULT 0,
  `warranty_terms` varchar(200) DEFAULT NULL,
  `unitPrice` decimal(10,2) NOT NULL,
  `reorderLevel` int(11) DEFAULT 10,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `brand`, `model`, `color`, `category`, `code`, `description`, `quantity`, `item_type`, `warranty_months`, `warranty_terms`, `unitPrice`, `reorderLevel`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Samsung Fast Charger', 'Samsung', 'EP-TA800', NULL, 'accessory', 'ACC20241225001', NULL, 50, 'standard', 6, NULL, 1500.00, 10, 'active', '2025-12-25 21:26:37', '2025-12-25 21:26:37'),
(2, 'Phone Case Universal', 'Generic', 'Universal', NULL, 'accessory', 'ACC20241225002', NULL, 100, 'standard', 3, NULL, 500.00, 10, 'active', '2025-12-25 21:26:37', '2025-12-25 21:26:37'),
(3, 'Screen Protector', 'Generic', 'Universal', NULL, 'accessory', 'ACC20241225003', NULL, 200, 'standard', 0, NULL, 300.00, 10, 'active', '2025-12-25 21:26:37', '2025-12-25 21:26:37'),
(7, 'Samsung Fast Charger 25W', 'Samsung', 'EP-TA800', NULL, 'accessory', 'ACC001', 'Original Samsung 25W fast charger with USB-C cable', 50, 'standard', 6, NULL, 1500.00, 10, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(8, 'iPhone Lightning Charger', 'Apple', 'A2305', NULL, 'accessory', 'ACC002', 'Original Apple 20W USB-C power adapter', 30, 'standard', 12, NULL, 2500.00, 10, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(9, 'Universal Fast Charger', 'Anker', 'PowerPort', NULL, 'accessory', 'ACC003', 'Anker 18W fast charger, universal compatibility', 75, 'standard', 6, NULL, 1200.00, 15, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(10, 'iPhone 13 Silicone Case', 'Apple', 'MagSafe', NULL, 'accessory', 'ACC004', 'Original Apple MagSafe silicone case', 40, 'standard', 3, NULL, 3500.00, 10, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(11, 'Samsung S21 Clear Case', 'Samsung', 'Clear View', NULL, 'accessory', 'ACC005', 'Original Samsung clear view standing cover', 35, 'standard', 3, NULL, 2000.00, 10, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(12, 'Universal Phone Case', 'Generic', 'Universal', NULL, 'accessory', 'ACC006', 'Universal silicone phone case, multiple colors', 100, 'standard', 0, NULL, 500.00, 20, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(13, 'Tempered Glass Screen Protector', 'Generic', 'Universal', NULL, 'accessory', 'ACC007', '9H hardness tempered glass', 200, 'standard', 0, NULL, 300.00, 50, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(14, 'iPhone 13 Screen Protector', 'Belkin', 'UltraGlass', NULL, 'accessory', 'ACC008', 'Premium tempered glass with easy installation', 60, 'standard', 6, NULL, 1500.00, 15, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(15, 'Samsung Galaxy Buds', 'Samsung', 'Buds 2', NULL, 'accessory', 'ACC009', 'True wireless earbuds with ANC', 25, 'standard', 12, NULL, 15000.00, 5, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(16, 'Wired Earphones', 'Generic', 'Universal', NULL, 'accessory', 'ACC010', 'Universal 3.5mm wired earphones', 150, 'standard', 0, NULL, 400.00, 30, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(17, 'USB-C to USB-C Cable', 'Anker', 'PowerLine', NULL, 'accessory', 'ACC011', 'Durable braided USB-C cable, 1.8m', 80, 'standard', 12, NULL, 800.00, 20, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(18, 'Lightning to USB Cable', 'Apple', 'Original', NULL, 'accessory', 'ACC012', 'Original Apple Lightning cable, 1m', 45, 'standard', 12, NULL, 1800.00, 10, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(19, 'Power Bank 10000mAh', 'Anker', 'PowerCore', NULL, 'accessory', 'ACC013', 'Compact 10000mAh power bank with fast charging', 30, 'standard', 12, NULL, 4500.00, 10, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(20, 'Power Bank 20000mAh', 'Xiaomi', 'Mi Power Bank', NULL, 'accessory', 'ACC014', 'High capacity 20000mAh with dual USB ports', 20, 'standard', 12, NULL, 6500.00, 5, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(21, 'MicroSD Card 64GB', 'SanDisk', 'Ultra', NULL, 'accessory', 'ACC015', 'Class 10 microSD card with adapter', 50, 'standard', 12, NULL, 1200.00, 15, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(22, 'MicroSD Card 128GB', 'Samsung', 'EVO Plus', NULL, 'accessory', 'ACC016', 'High speed 128GB microSD card', 35, 'standard', 12, NULL, 2200.00, 10, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(23, 'iPhone 13', 'Apple', 'iPhone 13', NULL, 'mobile', 'MOB001', '128GB, Multiple colors available', 0, 'serialized', 12, NULL, 180000.00, 2, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(24, 'iPhone 13 Pro', 'Apple', 'iPhone 13 Pro', NULL, 'mobile', 'MOB002', '256GB, Pro camera system', 0, 'serialized', 12, NULL, 250000.00, 2, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(25, 'Samsung Galaxy S21', 'Samsung', 'Galaxy S21', NULL, 'mobile', 'MOB003', '128GB, 5G enabled', 0, 'serialized', 12, NULL, 120000.00, 2, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(26, 'Samsung Galaxy S21 Ultra', 'Samsung', 'Galaxy S21 Ultra', NULL, 'mobile', 'MOB004', '256GB, S Pen support', 0, 'serialized', 12, NULL, 180000.00, 2, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(27, 'Xiaomi Redmi Note 11', 'Xiaomi', 'Redmi Note 11', NULL, 'mobile', 'MOB005', '128GB, 90Hz display', 0, 'serialized', 12, NULL, 45000.00, 3, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(28, 'OnePlus 9', 'OnePlus', 'OnePlus 9', NULL, 'mobile', 'MOB006', '128GB, Hasselblad camera', 0, 'serialized', 12, NULL, 95000.00, 2, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(29, 'Oppo Reno 7', 'Oppo', 'Reno 7', NULL, 'mobile', 'MOB007', '128GB, Fast charging', 0, 'serialized', 12, NULL, 65000.00, 2, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19'),
(30, 'Vivo V23', 'Vivo', 'V23', NULL, 'mobile', 'MOB008', '128GB, Color changing back', 0, 'serialized', 12, NULL, 70000.00, 2, 'active', '2025-12-25 21:39:19', '2025-12-25 21:39:19');

-- --------------------------------------------------------

--
-- Table structure for table `item_serials`
--

CREATE TABLE `item_serials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `imei_number` varchar(20) NOT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `storage` varchar(20) DEFAULT NULL COMMENT 'e.g., 128GB, 256GB',
  `cost_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL COMMENT 'Can override item base price',
  `status` enum('available','reserved','sold','returned','traded_in','defective') DEFAULT 'available',
  `sold_transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sold_date` datetime DEFAULT NULL,
  `purchase_date` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_serials`
--

INSERT INTO `item_serials` (`id`, `item_id`, `imei_number`, `serial_number`, `color`, `storage`, `cost_price`, `selling_price`, `status`, `sold_transaction_id`, `sold_date`, `purchase_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 23, '123456789012345', NULL, 'Blue', NULL, 170000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(2, 23, '123456789012346', NULL, 'Pink', NULL, 170000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(3, 23, '123456789012347', NULL, 'Midnight', NULL, 170000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(4, 24, '234567890123456', NULL, 'Graphite', NULL, 240000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(5, 24, '234567890123457', NULL, 'Gold', NULL, 240000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(6, 25, '345678901234567', NULL, 'Phantom Gray', NULL, 110000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(7, 25, '345678901234568', NULL, 'Phantom White', NULL, 110000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(8, 25, '345678901234569', NULL, 'Phantom Violet', NULL, 110000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(9, 26, '456789012345678', NULL, 'Phantom Black', NULL, 170000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(10, 26, '456789012345679', NULL, 'Phantom Silver', NULL, 170000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(11, 27, '567890123456789', NULL, 'Graphite Gray', NULL, 42000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(12, 27, '567890123456790', NULL, 'Twilight Blue', NULL, 42000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(13, 27, '567890123456791', NULL, 'Star Blue', NULL, 42000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(14, 27, '567890123456792', NULL, 'Graphite Gray', NULL, 42000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(15, 28, '678901234567890', NULL, 'Winter Mist', NULL, 90000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(16, 28, '678901234567891', NULL, 'Arctic Sky', NULL, 90000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(17, 29, '789012345678901', NULL, 'Startrails Blue', NULL, 62000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(18, 29, '789012345678902', NULL, 'Starlight Black', NULL, 62000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(19, 30, '890123456789012', NULL, 'Sunshine Gold', NULL, 67000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22'),
(20, 30, '890123456789013', NULL, 'Stardust Black', NULL, 67000.00, NULL, 'available', NULL, NULL, '2025-12-26 02:43:22', NULL, '2025-12-25 21:43:22', '2025-12-25 21:43:22');

-- --------------------------------------------------------

--
-- Stand-in structure for view `profit_report`
-- (See below for the actual view)
--
CREATE TABLE `profit_report` (
`ref` varchar(20)
,`date` datetime
,`customer_id` bigint(20) unsigned
,`customer_name` varchar(100)
,`total_amount` decimal(10,2)
,`profit_amount` decimal(10,2)
,`payment_method` varchar(50)
,`admin_id` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `trade_ins`
--

CREATE TABLE `trade_ins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `imei_number` varchar(20) DEFAULT NULL,
  `device_condition` enum('excellent','good','fair','poor','faulty') NOT NULL,
  `trade_in_value` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transId` bigint(20) UNSIGNED NOT NULL,
  `itemName` varchar(100) NOT NULL,
  `itemCode` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unitPrice` decimal(10,2) NOT NULL,
  `totalPrice` decimal(10,2) NOT NULL,
  `amountTendered` decimal(10,2) NOT NULL,
  `changeDue` decimal(10,2) NOT NULL,
  `modeOfPayment` varchar(50) NOT NULL,
  `payment_status` enum('paid','partial','credit') DEFAULT 'paid',
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `credit_amount` decimal(10,2) DEFAULT 0.00,
  `transType` tinyint(4) NOT NULL COMMENT '1=sale, 2=return',
  `staffId` int(11) NOT NULL,
  `totalMoneySpent` decimal(10,2) NOT NULL,
  `ref` varchar(20) NOT NULL,
  `vatAmount` decimal(10,2) DEFAULT 0.00,
  `vatPercentage` decimal(5,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `profit_amount` decimal(10,2) DEFAULT 0.00,
  `cust_name` varchar(100) DEFAULT NULL,
  `cust_phone` varchar(20) DEFAULT NULL,
  `cust_email` varchar(100) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `imei_numbers` text DEFAULT NULL COMMENT 'Comma-separated IMEIs',
  `cancelled` tinyint(1) DEFAULT 0,
  `transDate` datetime DEFAULT current_timestamp(),
  `lastUpdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `inventory_available`
--
DROP TABLE IF EXISTS `inventory_available`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `inventory_available`  AS SELECT `i`.`id` AS `id`, `i`.`name` AS `name`, `i`.`code` AS `code`, `i`.`brand` AS `brand`, `i`.`model` AS `model`, `i`.`category` AS `category`, `i`.`item_type` AS `item_type`, `i`.`unitPrice` AS `unitPrice`, `i`.`warranty_months` AS `warranty_months`, `i`.`quantity` AS `quantity`, CASE WHEN `i`.`item_type` = 'serialized' THEN coalesce(count(`s`.`id`),0) ELSE `i`.`quantity` END AS `available_qty`, CASE WHEN `i`.`item_type` = 'serialized' THEN coalesce(sum(`s`.`cost_price`),0) ELSE `i`.`quantity`* `i`.`unitPrice` END AS `total_value` FROM (`items` `i` left join `item_serials` `s` on(`i`.`id` = `s`.`item_id` and `s`.`status` = 'available')) GROUP BY `i`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `profit_report`
--
DROP TABLE IF EXISTS `profit_report`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `profit_report`  AS SELECT `t`.`ref` AS `ref`, `t`.`transDate` AS `date`, `t`.`customer_id` AS `customer_id`, `c`.`name` AS `customer_name`, `t`.`totalPrice` AS `total_amount`, `t`.`profit_amount` AS `profit_amount`, `t`.`modeOfPayment` AS `payment_method`, `t`.`staffId` AS `admin_id` FROM (`transactions` `t` left join `customers` `c` on(`t`.`customer_id` = `c`.`id`)) WHERE `t`.`profit_amount` is not null ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_balance` (`current_balance`);

--
-- Indexes for table `customer_ledger`
--
ALTER TABLE `customer_ledger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `eventlog`
--
ALTER TABLE `eventlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_staffId` (`staffId`),
  ADD KEY `idx_eventDate` (`eventDate`),
  ADD KEY `idx_eventTable` (`eventTable`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_item_type` (`item_type`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_brand` (`brand`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `item_serials`
--
ALTER TABLE `item_serials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `imei_number` (`imei_number`),
  ADD KEY `idx_item_id` (`item_id`),
  ADD KEY `idx_imei` (`imei_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sold_date` (`sold_date`);

--
-- Indexes for table `trade_ins`
--
ALTER TABLE `trade_ins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_imei` (`imei_number`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transId`),
  ADD KEY `idx_ref` (`ref`),
  ADD KEY `idx_transDate` (`transDate`),
  ADD KEY `idx_staffId` (`staffId`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_payment_status` (`payment_status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `customer_ledger`
--
ALTER TABLE `customer_ledger`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventlog`
--
ALTER TABLE `eventlog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `item_serials`
--
ALTER TABLE `item_serials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `trade_ins`
--
ALTER TABLE `trade_ins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_ledger`
--
ALTER TABLE `customer_ledger`
  ADD CONSTRAINT `customer_ledger_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_serials`
--
ALTER TABLE `item_serials`
  ADD CONSTRAINT `item_serials_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trade_ins`
--
ALTER TABLE `trade_ins`
  ADD CONSTRAINT `trade_ins_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transId`) ON DELETE CASCADE,
  ADD CONSTRAINT `trade_ins_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
