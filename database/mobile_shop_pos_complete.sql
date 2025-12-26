-- ============================================
-- Mobile Shop POS - Complete Database Setup
-- Drop existing database and create fresh
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================
-- Drop existing database and recreate
-- ============================================
DROP DATABASE IF EXISTS `mobile_shop_pos`;
CREATE DATABASE `mobile_shop_pos` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mobile_shop_pos`;

-- ============================================
-- Table: admin
-- ============================================
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Super','Manager','Staff') DEFAULT 'Staff',
  `mobile1` varchar(20) DEFAULT NULL,
  `mobile2` varchar(20) DEFAULT NULL,
  `created_on` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `account_status` tinyint(1) DEFAULT 1,
  `deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin user: admin@shop.com / admin123
INSERT INTO `admin` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `mobile1`, `mobile2`, `created_on`, `last_login`, `account_status`, `deleted`) VALUES
(1, 'Admin', 'User', 'admin@shop.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'Super', NULL, NULL, NOW(), NULL, 1, 0);


-- ============================================
-- Table: customers
-- ============================================
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `idx_phone` (`phone`),
  KEY `idx_status` (`status`),
  KEY `idx_balance` (`current_balance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `cnic`, `address`, `current_balance`, `credit_limit`, `total_purchases`, `total_payments`, `status`, `notes`) VALUES
(1, 'Ahmed Khan', '0300-1234567', 'ahmed@example.com', '42101-1234567-1', 'Shop #12, Main Market, Karachi', 0.00, 50000.00, 0.00, 0.00, 'active', 'Regular customer'),
(2, 'Sara Ali', '0321-9876543', 'sara@example.com', '42201-9876543-2', 'House #45, Gulshan-e-Iqbal, Karachi', 0.00, 30000.00, 0.00, 0.00, 'active', NULL),
(3, 'Hassan Raza', '0333-5555555', 'hassan@example.com', NULL, 'Flat #3, Clifton, Karachi', 0.00, 100000.00, 0.00, 0.00, 'active', 'VIP customer'),
(4, 'Fatima Malik', '0345-7777777', 'fatima@example.com', '42301-7777777-3', 'Block A, North Nazimabad, Karachi', 0.00, 25000.00, 0.00, 0.00, 'active', NULL),
(5, 'Bilal Ahmed', '0312-8888888', 'bilal@example.com', NULL, 'Saddar, Karachi', 0.00, 20000.00, 0.00, 0.00, 'active', NULL);


-- ============================================
-- Table: customer_ledger
-- ============================================
DROP TABLE IF EXISTS `customer_ledger`;
CREATE TABLE `customer_ledger` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_type` enum('credit_sale','payment','adjustment','return') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_transaction_id` (`transaction_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: eventlog
-- ============================================
DROP TABLE IF EXISTS `eventlog`;
CREATE TABLE `eventlog` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event` varchar(100) NOT NULL,
  `eventRowIdOrRef` varchar(50) NOT NULL,
  `eventDesc` text NOT NULL,
  `eventTable` varchar(50) NOT NULL,
  `staffId` int(11) NOT NULL,
  `eventDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_staffId` (`staffId`),
  KEY `idx_eventDate` (`eventDate`),
  KEY `idx_eventTable` (`eventTable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- Table: items
-- ============================================
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_item_type` (`item_type`),
  KEY `idx_category` (`category`),
  KEY `idx_brand` (`brand`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `items` (`id`, `name`, `brand`, `model`, `color`, `category`, `code`, `description`, `quantity`, `item_type`, `warranty_months`, `warranty_terms`, `unitPrice`, `reorderLevel`, `status`) VALUES
(1, 'Samsung Fast Charger 25W', 'Samsung', 'EP-TA800', NULL, 'accessory', 'ACC001', 'Original Samsung 25W fast charger with USB-C cable', 50, 'standard', 6, 'Manufacturer warranty', 1500.00, 10, 'active'),
(2, 'iPhone Lightning Charger', 'Apple', 'A2305', NULL, 'accessory', 'ACC002', 'Original Apple 20W USB-C power adapter', 30, 'standard', 12, 'Apple warranty', 2500.00, 10, 'active'),
(3, 'Universal Fast Charger', 'Anker', 'PowerPort', NULL, 'accessory', 'ACC003', 'Anker 18W fast charger, universal compatibility', 75, 'standard', 6, NULL, 1200.00, 15, 'active'),
(4, 'Phone Case Universal', 'Generic', 'Universal', NULL, 'accessory', 'ACC004', 'Universal silicone phone case, multiple colors', 100, 'standard', 3, NULL, 500.00, 20, 'active'),
(5, 'Tempered Glass Screen Protector', 'Generic', 'Universal', NULL, 'accessory', 'ACC005', '9H hardness tempered glass', 200, 'standard', 0, NULL, 300.00, 50, 'active'),
(6, 'Samsung Galaxy Buds', 'Samsung', 'Buds 2', NULL, 'accessory', 'ACC006', 'True wireless earbuds with ANC', 25, 'standard', 12, 'Samsung warranty', 15000.00, 5, 'active'),
(7, 'Power Bank 10000mAh', 'Anker', 'PowerCore', NULL, 'accessory', 'ACC007', 'Compact 10000mAh power bank with fast charging', 30, 'standard', 12, NULL, 4500.00, 10, 'active'),
(8, 'USB-C Cable', 'Anker', 'PowerLine', NULL, 'accessory', 'ACC008', 'Durable braided USB-C cable, 1.8m', 80, 'standard', 12, NULL, 800.00, 20, 'active'),
(9, 'iPhone 13', 'Apple', 'iPhone 13', NULL, 'mobile', 'MOB001', '128GB, Multiple colors available', 0, 'serialized', 12, 'Apple warranty', 180000.00, 2, 'active'),
(10, 'iPhone 13 Pro', 'Apple', 'iPhone 13 Pro', NULL, 'mobile', 'MOB002', '256GB, Pro camera system', 0, 'serialized', 12, 'Apple warranty', 250000.00, 2, 'active'),
(11, 'Samsung Galaxy S21', 'Samsung', 'Galaxy S21', NULL, 'mobile', 'MOB003', '128GB, 5G enabled', 0, 'serialized', 12, 'Samsung warranty', 120000.00, 2, 'active'),
(12, 'Samsung Galaxy S21 Ultra', 'Samsung', 'Galaxy S21 Ultra', NULL, 'mobile', 'MOB004', '256GB, S Pen support', 0, 'serialized', 12, 'Samsung warranty', 180000.00, 2, 'active'),
(13, 'Xiaomi Redmi Note 11', 'Xiaomi', 'Redmi Note 11', NULL, 'mobile', 'MOB005', '128GB, 90Hz display', 0, 'serialized', 12, 'Xiaomi warranty', 45000.00, 3, 'active'),
(14, 'OnePlus 9', 'OnePlus', 'OnePlus 9', NULL, 'mobile', 'MOB006', '128GB, Hasselblad camera', 0, 'serialized', 12, 'OnePlus warranty', 95000.00, 2, 'active');


-- ============================================
-- Table: item_serials
-- ============================================
DROP TABLE IF EXISTS `item_serials`;
CREATE TABLE `item_serials` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `purchase_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `imei_number` (`imei_number`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_imei` (`imei_number`),
  KEY `idx_status` (`status`),
  KEY `idx_sold_date` (`sold_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `item_serials` (`id`, `item_id`, `imei_number`, `serial_number`, `color`, `storage`, `cost_price`, `selling_price`, `status`, `sold_transaction_id`, `sold_date`, `purchase_date`) VALUES
(1, 9, '123456789012345', 'SN-IP13-001', 'Blue', '128GB', 170000.00, NULL, 'available', NULL, NULL, NOW()),
(2, 9, '123456789012346', 'SN-IP13-002', 'Pink', '128GB', 170000.00, NULL, 'available', NULL, NULL, NOW()),
(3, 9, '123456789012347', 'SN-IP13-003', 'Midnight', '128GB', 170000.00, NULL, 'available', NULL, NULL, NOW()),
(4, 10, '234567890123456', 'SN-IP13P-001', 'Graphite', '256GB', 240000.00, NULL, 'available', NULL, NULL, NOW()),
(5, 10, '234567890123457', 'SN-IP13P-002', 'Gold', '256GB', 240000.00, NULL, 'available', NULL, NULL, NOW()),
(6, 11, '345678901234567', 'SN-S21-001', 'Phantom Gray', '128GB', 110000.00, NULL, 'available', NULL, NULL, NOW()),
(7, 11, '345678901234568', 'SN-S21-002', 'Phantom White', '128GB', 110000.00, NULL, 'available', NULL, NULL, NOW()),
(8, 11, '345678901234569', 'SN-S21-003', 'Phantom Violet', '128GB', 110000.00, NULL, 'available', NULL, NULL, NOW()),
(9, 12, '456789012345678', 'SN-S21U-001', 'Phantom Black', '256GB', 170000.00, NULL, 'available', NULL, NULL, NOW()),
(10, 12, '456789012345679', 'SN-S21U-002', 'Phantom Silver', '256GB', 170000.00, NULL, 'available', NULL, NULL, NOW()),
(11, 13, '567890123456789', 'SN-RN11-001', 'Graphite Gray', '128GB', 42000.00, NULL, 'available', NULL, NULL, NOW()),
(12, 13, '567890123456790', 'SN-RN11-002', 'Twilight Blue', '128GB', 42000.00, NULL, 'available', NULL, NULL, NOW()),
(13, 13, '567890123456791', 'SN-RN11-003', 'Star Blue', '128GB', 42000.00, NULL, 'available', NULL, NULL, NOW()),
(14, 14, '678901234567890', 'SN-OP9-001', 'Winter Mist', '128GB', 90000.00, NULL, 'available', NULL, NULL, NOW()),
(15, 14, '678901234567891', 'SN-OP9-002', 'Arctic Sky', '128GB', 90000.00, NULL, 'available', NULL, NULL, NOW());


-- ============================================
-- Table: trade_ins
-- ============================================
DROP TABLE IF EXISTS `trade_ins`;
CREATE TABLE `trade_ins` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `imei_number` varchar(20) DEFAULT NULL,
  `device_condition` enum('excellent','good','fair','poor','faulty') NOT NULL,
  `trade_in_value` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `idx_transaction_id` (`transaction_id`),
  KEY `idx_imei` (`imei_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: transactions
-- ============================================
DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `transId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `transDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `lastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transId`),
  KEY `idx_ref` (`ref`),
  KEY `idx_transDate` (`transDate`),
  KEY `idx_staffId` (`staffId`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- Database Views
-- ============================================

-- View: inventory_available
DROP VIEW IF EXISTS `inventory_available`;
CREATE VIEW `inventory_available` AS 
SELECT 
  i.id,
  i.name,
  i.code,
  i.brand,
  i.model,
  i.category,
  i.item_type,
  i.unitPrice,
  i.warranty_months,
  i.quantity,
  CASE 
    WHEN i.item_type = 'serialized' THEN COALESCE(COUNT(s.id), 0) 
    ELSE i.quantity 
  END AS available_qty,
  CASE 
    WHEN i.item_type = 'serialized' THEN COALESCE(SUM(s.cost_price), 0) 
    ELSE i.quantity * i.unitPrice 
  END AS total_value
FROM items i
LEFT JOIN item_serials s ON i.id = s.item_id AND s.status = 'available'
GROUP BY i.id;

-- View: profit_report
DROP VIEW IF EXISTS `profit_report`;
CREATE VIEW `profit_report` AS 
SELECT 
  t.ref,
  t.transDate AS date,
  t.customer_id,
  c.name AS customer_name,
  t.totalPrice AS total_amount,
  t.profit_amount,
  t.modeOfPayment AS payment_method,
  t.staffId AS admin_id
FROM transactions t
LEFT JOIN customers c ON t.customer_id = c.id
WHERE t.profit_amount IS NOT NULL;


-- ============================================
-- Foreign Key Constraints
-- ============================================

ALTER TABLE `customer_ledger`
  ADD CONSTRAINT `customer_ledger_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

ALTER TABLE `item_serials`
  ADD CONSTRAINT `item_serials_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

ALTER TABLE `trade_ins`
  ADD CONSTRAINT `trade_ins_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transId`) ON DELETE CASCADE,
  ADD CONSTRAINT `trade_ins_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================
-- Database Setup Complete!
-- ============================================
-- Login Credentials:
--   Email: admin@shop.com
--   Password: admin123
-- 
-- Test Data Included:
--   - 1 Admin user (Super role)
--   - 5 Customers
--   - 14 Items (8 accessories, 6 mobiles)
--   - 15 Serialized phones with IMEI numbers
-- ============================================
