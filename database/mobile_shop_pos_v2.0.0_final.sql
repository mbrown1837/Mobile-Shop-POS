-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: mobile_shop_pos
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Super','Manager','Staff') DEFAULT 'Staff',
  `mobile1` varchar(20) DEFAULT NULL,
  `mobile2` varchar(20) DEFAULT NULL,
  `created_on` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `account_status` tinyint(1) DEFAULT 1,
  `deleted` tinyint(1) DEFAULT 0,
  `last_seen` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'Admin','User','admin@shop.com','$2y$10$PhwtHdJQ72yYb/3HXYcRh.i9OgHf.lmc1TzGu9UjCDrQcjC8ZAvL6','Super',NULL,NULL,'2025-12-27 04:38:52','2026-01-01 23:41:43',1,0,'2026-01-02 00:08:24'),(2,'Ali','Ahmed','ali@shop.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Manager',NULL,NULL,'2025-12-27 04:38:52',NULL,1,0,NULL),(3,'Sara','Khan','sara@shop.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Staff',NULL,NULL,'2025-12-27 04:38:52',NULL,1,0,NULL);
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_ledger`
--

DROP TABLE IF EXISTS `customer_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_ledger` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `transaction_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_ref` varchar(50) DEFAULT NULL COMMENT 'Reference to transaction',
  `transaction_type` enum('credit_sale','payment','adjustment','return') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) DEFAULT 0.00 COMMENT 'Customer balance after this entry',
  `payment_method` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_transaction_id` (`transaction_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_transaction_ref` (`transaction_ref`),
  CONSTRAINT `customer_ledger_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_ledger`
--

LOCK TABLES `customer_ledger` WRITE;
/*!40000 ALTER TABLE `customer_ledger` DISABLE KEYS */;
INSERT INTO `customer_ledger` VALUES (1,1,NULL,NULL,'credit_sale',50000.00,50000.00,NULL,'Test credit sale',NULL,'2025-12-26 23:45:10'),(2,1,NULL,NULL,'payment',30000.00,20000.00,'cash','Partial payment',NULL,'2025-12-26 23:45:10'),(3,2,NULL,NULL,'credit_sale',35000.00,53800.00,NULL,'Another test sale',NULL,'2025-12-26 23:45:10'),(4,2,NULL,NULL,'payment',20000.00,33800.00,'cash','Payment received',NULL,'2025-12-26 23:45:10'),(5,5,NULL,NULL,'credit_sale',2500.00,36300.00,NULL,'Credit sale - Ref: 8774592','Credit sale - Ref: 8774592','2025-12-30 18:43:36'),(6,1,NULL,NULL,'payment',1200.00,18800.00,'cash','','','2025-12-30 21:17:23'),(7,5,NULL,NULL,'payment',500.00,35800.00,'cash','','','2025-12-30 21:17:41'),(8,2,NULL,NULL,'payment',15000.00,0.00,'cash','','','2025-12-30 21:54:16'),(9,5,NULL,NULL,'payment',5000.00,0.00,'cash','','','2025-12-30 21:54:37'),(10,4,NULL,NULL,'credit_sale',15000.00,0.00,NULL,'Credit sale - Ref: 290587246','Credit sale - Ref: 290587246','2026-01-01 02:06:57'),(11,3,NULL,NULL,'payment',25000.00,0.00,'cash','','','2026-01-01 19:09:23');
/*!40000 ALTER TABLE `customer_ledger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `idx_phone` (`phone`),
  KEY `idx_status` (`status`),
  KEY `idx_balance` (`current_balance`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Ahmed Hassan','0300-1234567','ahmed@example.com','42101-1234567-1','Shop #12, Main Market, Karachi',18800.00,50000.00,50000.00,30000.00,'active',NULL,'2025-12-26 23:38:52','2025-12-30 21:17:23'),(2,'Fatima Ali','0321-9876543','fatima@example.com','42201-9876543-2','House #45, Gulshan-e-Iqbal, Karachi',0.00,30000.00,35000.00,20000.00,'inactive',NULL,'2025-12-26 23:38:52','2026-01-01 19:12:58'),(3,'Hassan Raza','0333-5555555','hassan@example.com','42301-5555555-3','Flat #3, Clifton, Karachi',0.00,100000.00,195000.00,170000.00,'inactive',NULL,'2025-12-26 23:38:52','2026-01-01 19:09:26'),(4,'Ayesha Khan','0345-7777777','ayesha@example.com','42401-7777777-4','Block A, North Nazimabad',15000.00,25000.00,0.00,0.00,'active',NULL,'2025-12-26 23:38:52','2026-01-01 02:06:57'),(5,'Bilal Ahmed','0312-8888888','bilal@example.com','42501-8888888-5','Saddar, Karachi',2000.00,20000.00,35000.00,30000.00,'active',NULL,'2025-12-26 23:38:52','2025-12-30 21:54:37'),(6,'Hamid SHah','03220716197','','','Loran Gali No 1',0.00,50000.00,0.00,0.00,'inactive',NULL,'2025-12-27 02:27:45','2026-01-01 19:08:52'),(7,'Shehrose Jamshaid','03220711197','','4230155555554','TEST',0.00,2000.00,0.00,0.00,'inactive',NULL,'2025-12-30 21:18:47','2026-01-01 19:09:02');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventlog`
--

DROP TABLE IF EXISTS `eventlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventlog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(100) NOT NULL,
  `eventRowIdOrRef` varchar(50) NOT NULL,
  `eventDesc` text NOT NULL,
  `eventTable` varchar(50) NOT NULL,
  `staffId` int(11) NOT NULL,
  `eventDate` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_staffId` (`staffId`),
  KEY `idx_eventDate` (`eventDate`),
  KEY `idx_eventTable` (`eventTable`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventlog`
--

LOCK TABLES `eventlog` WRITE;
/*!40000 ALTER TABLE `eventlog` DISABLE KEYS */;
INSERT INTO `eventlog` VALUES (1,'New Transaction','492803974','1 items totalling ₨1,500.00 with reference 492803974','transactions',1,'2025-12-28 08:13:53'),(2,'New Transaction','029782','1 items totalling ₨5,000.00 with reference 029782','transactions',1,'2025-12-28 08:31:30'),(3,'New Transaction','54717452','1 items totalling ₨500.00 with reference 54717452','transactions',1,'2025-12-28 17:05:26'),(4,'New Transaction','548325','1 items totalling ₨10,000.00 with reference 548325','transactions',1,'2025-12-28 18:58:54'),(5,'Item Deleted','10','Item deleted: MOB20241227001','items',1,'2025-12-28 21:55:00'),(6,'Item Deleted','12','Item deleted: MOB20241227003','items',1,'2025-12-28 21:55:06'),(7,'Item Deleted','13','Item deleted: MOB20241227004','items',1,'2025-12-28 21:58:08'),(8,'Item Deleted','11','Item deleted: MOB20241227002','items',1,'2025-12-28 22:00:38'),(9,'Stock Update (New Stock)','2','<p>1 quantities of iPhone Lightning Cable was added to stock</p>\r\n                Reason: <p>Stock update - newStock</p>','items',1,'2025-12-28 22:07:30'),(10,'Stock Update (Deficit)','2','<p>50 quantities of iPhone Lightning Cable was removed from stock</p>\r\n                Reason: <p>faulty</p>','items',1,'2025-12-28 22:07:47'),(11,'Item Updated','2','Item details updated','items',1,'2025-12-28 22:39:45'),(12,'Item Updated','2','Item details updated','items',1,'2025-12-28 22:51:36'),(13,'Item Updated','2','Item details updated','items',1,'2025-12-28 22:51:59'),(14,'Item Updated','2','Item details updated','items',1,'2025-12-28 22:52:10'),(15,'Item Updated','2','Item details updated','items',1,'2025-12-28 22:52:20'),(16,'Item Deleted','14','Item deleted: MOB20241227005','items',1,'2025-12-28 23:19:33'),(17,'Item Deleted','5','Item deleted: ACC20241227005','items',1,'2025-12-28 23:19:40'),(18,'Item Deleted','1','Item deleted: ACC20241227001','items',1,'2025-12-28 23:19:44'),(19,'Customer Updated','3','Customer \'Hassan Raza\' details updated','customers',1,'2025-12-28 23:20:02'),(20,'Customer Updated','6','Customer \'Hamid SHah\' details updated','customers',1,'2025-12-28 23:20:09'),(21,'Customer Updated','2','Customer \'Fatima Ali\' details updated','customers',1,'2025-12-28 23:20:15'),(22,'New Transaction','46893121','1 items totalling ₨450,000.00 with reference 46893121','transactions',1,'2025-12-29 05:14:01'),(23,'Creation of new item','102','Addition of serialized item \'S21 Ultra\' (Code: MOB20251229001) with 2 IMEIs at Rs. 150,000.00','items',1,'2025-12-29 18:14:02'),(24,'Creation of new item','103','Addition of 20 quantities of \'S21 Ultra-Cover\' (Code: ACC20251230001) at Rs. 1,500.00','items',1,'2025-12-29 19:56:27'),(25,'Item Updated','103','Item details updated','items',1,'2025-12-29 19:56:43'),(26,'Creation of new item','104','Addition of serialized item \'TEst 21\' (Code: MOB20251230001) with 2 IMEIs at Rs. 150,000.00','items',1,'2025-12-29 20:26:42'),(27,'Creation of new item','105','Addition of serialized item \'S25 Ultra\' (Code: MOB20251230002) with 2 IMEIs at Rs. 150,000.00','items',1,'2025-12-30 07:35:33'),(28,'Item Deleted','104','Item deleted: MOB20251230001','items',1,'2025-12-30 07:36:24'),(29,'Creation of new item','106','Addition of serialized item \'Samsung Galaxy S24 Ultra Black 256GB\' (Code: MOB20251230003) with 2 IMEIs at Rs. 285,000.00','items',1,'2025-12-30 07:56:47'),(30,'Creation of new item','107','Addition of serialized item \'S11 Ultra\' (Code: MOB20251230004) with 2 IMEIs at Rs. 150.00','items',1,'2025-12-30 08:15:02'),(31,'New Transaction','8774592','1 items totalling ₨2,500.00 with reference 8774592','transactions',1,'2025-12-30 18:43:36'),(32,'Payment Received','1','Payment of ₨1,200.00 received from \'Ahmed Hassan\'','customer_ledger',1,'2025-12-30 21:17:23'),(33,'Payment Received','5','Payment of ₨500.00 received from \'Bilal Ahmed\'','customer_ledger',1,'2025-12-30 21:17:41'),(34,'New Customer','7','Customer \'Shehrose Jamshaid\' added with credit limit ₨0.00','customers',1,'2025-12-30 21:18:47'),(35,'Customer Updated','7','Customer \'Shehrose Jamshaid\' details updated','customers',1,'2025-12-30 21:18:59'),(36,'Customer Updated','7','Customer \'Shehrose Jamshaid\' details updated','customers',1,'2025-12-30 21:31:30'),(37,'Customer Updated','7','Customer \'Shehrose Jamshaid\' details updated','customers',1,'2025-12-30 21:31:43'),(38,'Customer Updated','7','Customer \'Shehrose Jamshaid\' details updated','customers',1,'2025-12-30 21:34:34'),(39,'Payment Received','2','Payment of ₨15,000.00 received from \'Fatima Ali\'','customer_ledger',1,'2025-12-30 21:54:16'),(40,'Payment Received','5','Payment of ₨5,000.00 received from \'Bilal Ahmed\'','customer_ledger',1,'2025-12-30 21:54:37'),(41,'Stock Update (New Stock)','3','<p>1 quantities of Wireless Earbuds was added to stock</p>\r\n                Reason: <p>Stock update - newStock</p>','items',1,'2025-12-31 20:43:38'),(42,'New Transaction','943765','1 items totalling ₨440.00 with reference 943765','transactions',1,'2025-12-31 21:11:46'),(43,'New Transaction','3206450','1 items totalling ₨2,500.00 with reference 3206450','transactions',1,'2025-12-31 21:26:07'),(44,'Item Deleted','102','Item deleted: MOB20251229001','items',1,'2025-12-31 22:18:35'),(45,'New Transaction','3904938','1 items totalling ₨145,000.00 with reference 3904938','transactions',1,'2025-12-31 22:19:41'),(46,'Stock Update (Deficit)','3','<p>1 quantities of Wireless Earbuds was removed from stock</p>\r\n                Reason: <p>Stock update - deficit</p>','items',1,'2025-12-31 22:36:04'),(47,'Stock Update (New Stock)','3','<p>01 quantities of Wireless Earbuds was added to stock</p>\r\n                Reason: <p>77vvvv</p>','items',1,'2025-12-31 22:58:29'),(48,'Creation of new item','108','Addition of serialized item \'S11 Ultra 1\' (Code: MOB20260101001) with 2 IMEIs at Rs. 120,000.00','items',1,'2026-01-01 00:43:10'),(49,'New Transaction','290587246','1 items totalling ₨15,000.00 with reference 290587246','transactions',1,'2026-01-01 02:06:57'),(50,'Customer Deleted','2','Customer \'Fatima Ali\' deactivated','customers',1,'2026-01-01 19:08:34'),(51,'Customer Deleted','6','Customer \'Hamid SHah\' deactivated','customers',1,'2026-01-01 19:08:38'),(52,'Customer Deleted','6','Customer \'Hamid SHah\' deactivated','customers',1,'2026-01-01 19:08:52'),(53,'Customer Deleted','7','Customer \'Shehrose Jamshaid\' deactivated','customers',1,'2026-01-01 19:09:02'),(54,'Payment Received','3','Payment of ₨25,000.00 received from \'Hassan Raza\'','customer_ledger',1,'2026-01-01 19:09:23'),(55,'Customer Deleted','3','Customer \'Hassan Raza\' deactivated','customers',1,'2026-01-01 19:09:26'),(56,'Customer Deleted','2','Customer \'Fatima Ali\' deactivated','customers',1,'2026-01-01 19:12:58');
/*!40000 ALTER TABLE `eventlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `inventory_available`
--

DROP TABLE IF EXISTS `inventory_available`;
/*!50001 DROP VIEW IF EXISTS `inventory_available`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `inventory_available` AS SELECT
 1 AS `id`,
  1 AS `name`,
  1 AS `code`,
  1 AS `brand`,
  1 AS `model`,
  1 AS `category`,
  1 AS `item_type`,
  1 AS `quantity`,
  1 AS `unitPrice`,
  1 AS `cost_price`,
  1 AS `warranty_months`,
  1 AS `description`,
  1 AS `created_at`,
  1 AS `updated_at`,
  1 AS `available_qty` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `item_serials`
--

DROP TABLE IF EXISTS `item_serials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_serials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `imei_number` varchar(20) NOT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `storage` varchar(20) DEFAULT NULL COMMENT 'e.g., 128GB, 256GB',
  `cost_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL COMMENT 'Can override item base price',
  `status` enum('available','reserved','sold','returned','traded_in','defective') DEFAULT 'available',
  `sold_transaction_id` bigint(20) unsigned DEFAULT NULL,
  `sold_date` datetime DEFAULT NULL,
  `purchase_date` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `imei_number` (`imei_number`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_imei` (`imei_number`),
  KEY `idx_status` (`status`),
  KEY `idx_sold_date` (`sold_date`),
  CONSTRAINT `item_serials_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_serials`
--

LOCK TABLES `item_serials` WRITE;
/*!40000 ALTER TABLE `item_serials` DISABLE KEYS */;
INSERT INTO `item_serials` VALUES (1,100,'351234567890123','F2LY3456ABC','Graphite','128GB',165000.00,180000.00,'reserved',NULL,NULL,'2025-12-27 04:45:10',NULL,'2025-12-26 23:45:10','2025-12-27 00:38:11'),(2,100,'351234567890124','F2LY3456ABD','Blue','128GB',165000.00,180000.00,'available',NULL,NULL,'2025-12-27 04:45:10',NULL,'2025-12-26 23:45:10','2025-12-26 23:45:10'),(3,100,'351234567890125','F2LY3456ABE','Gold','128GB',165000.00,180000.00,'sold',NULL,NULL,'2025-12-27 04:45:10',NULL,'2025-12-26 23:45:10','2025-12-26 23:45:10'),(4,101,'352345678901234','R58N1234XYZ','Black','128GB',105000.00,120000.00,'reserved',NULL,NULL,'2025-12-27 04:45:10',NULL,'2025-12-26 23:45:10','2025-12-27 01:37:41'),(5,101,'352345678901235','R58N1234XYA','White','128GB',105000.00,120000.00,'available',NULL,NULL,'2025-12-27 04:45:10',NULL,'2025-12-26 23:45:10','2025-12-26 23:45:10'),(6,101,'352345678901236','R58N1234XYB','Green','128GB',105000.00,120000.00,'sold',NULL,NULL,'2025-12-27 04:45:10',NULL,'2025-12-26 23:45:10','2025-12-26 23:45:10'),(8,105,'351234567890223',NULL,'Black','',120000.00,NULL,'sold',NULL,'2026-01-01 03:19:41','2025-12-30 12:35:33',NULL,'2025-12-30 07:35:33','2025-12-31 22:19:41'),(9,107,'352345678901777',NULL,'White','',120.00,NULL,'reserved',NULL,NULL,'2025-12-30 13:15:02',NULL,'2025-12-30 08:15:02','2026-01-01 01:28:28'),(10,107,'352345678901747',NULL,'White','',120.00,NULL,'reserved',NULL,NULL,'2025-12-30 13:15:02',NULL,'2025-12-30 08:15:02','2026-01-01 01:28:28'),(11,108,'223344556612345',NULL,'Black','',120000.00,NULL,'reserved',NULL,NULL,'2026-01-01 05:43:10',NULL,'2026-01-01 00:43:10','2026-01-01 00:54:45'),(12,108,'223344556612352',NULL,'Black','',120000.00,NULL,'reserved',NULL,NULL,'2026-01-01 05:43:10',NULL,'2026-01-01 00:43:10','2026-01-01 00:54:45');
/*!40000 ALTER TABLE `item_serials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `cost_price` decimal(10,2) DEFAULT 0.00 COMMENT 'Purchase/Cost price',
  `reorderLevel` int(11) DEFAULT 10,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_item_type` (`item_type`),
  KEY `idx_category` (`category`),
  KEY `idx_brand` (`brand`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (2,'iPhone Lightning Cable','Apple','Lightning to USB-C',NULL,'accessory','ACC20241227002','Original Apple Lightning cable 1m',50,'standard',12,NULL,1000.00,500.00,10,'active','2025-12-26 23:38:52','2025-12-28 22:52:20'),(3,'Wireless Earbuds','Samsung','Galaxy Buds 2',NULL,'accessory','ACC20241227003','Samsung Galaxy Buds 2 with ANC',0,'standard',12,NULL,15000.00,10500.00,10,'active','2025-12-26 23:38:52','2026-01-01 02:06:57'),(4,'Phone Case','Generic','Universal',NULL,'accessory','ACC20241227004','Silicone phone case - various colors',150,'standard',0,NULL,500.00,350.00,10,'active','2025-12-26 23:38:52','2025-12-31 21:26:07'),(100,'Test iPhone 13','Apple','iPhone 13',NULL,'mobile','TEST001',NULL,0,'serialized',12,NULL,180000.00,126000.00,10,'active','2025-12-26 23:45:10','2025-12-28 23:01:05'),(101,'Test Samsung S21','Samsung','Galaxy S21',NULL,'mobile','TEST002',NULL,0,'serialized',12,NULL,120000.00,84000.00,10,'active','2025-12-26 23:45:10','2025-12-28 23:01:05'),(103,'S21 Ultra-Cover','Samsung','S21',NULL,'accessory','ACC20251230001','',20,'standard',0,NULL,1500.00,1200.00,10,'active','2025-12-29 19:56:27','2025-12-29 19:56:43'),(105,'S25 Ultra','Samsung','S25',NULL,'mobile','MOB20251230002','',0,'serialized',12,NULL,150000.00,120000.00,10,'active','2025-12-30 07:35:33','2025-12-30 07:35:33'),(106,'Samsung Galaxy S24 Ultra Black 256GB','Samsung','Galaxy S24 Ultra',NULL,'mobile','MOB20251230003','',0,'serialized',12,NULL,285000.00,265000.00,10,'active','2025-12-30 07:56:47','2025-12-30 07:56:47'),(107,'S11 Ultra','Samsung','S11',NULL,'mobile','MOB20251230004','',0,'serialized',23,NULL,150.00,120.00,10,'active','2025-12-30 08:15:02','2025-12-30 08:15:02'),(108,'S11 Ultra 1','Samsung','S11',NULL,'mobile','MOB20260101001','S11',0,'serialized',12,NULL,120000.00,120000.00,10,'active','2026-01-01 00:43:10','2026-01-01 00:43:10');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `profit_report`
--

DROP TABLE IF EXISTS `profit_report`;
/*!50001 DROP VIEW IF EXISTS `profit_report`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `profit_report` AS SELECT
 1 AS `ref`,
  1 AS `date`,
  1 AS `customer_id`,
  1 AS `customer_name`,
  1 AS `total_amount`,
  1 AS `profit_amount`,
  1 AS `payment_method`,
  1 AS `admin_id` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `trade_ins`
--

DROP TABLE IF EXISTS `trade_ins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trade_ins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `imei_number` varchar(20) DEFAULT NULL,
  `device_condition` enum('excellent','good','fair','poor','faulty') NOT NULL,
  `trade_in_value` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `idx_transaction_id` (`transaction_id`),
  KEY `idx_imei` (`imei_number`),
  CONSTRAINT `trade_ins_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transId`) ON DELETE CASCADE,
  CONSTRAINT `trade_ins_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trade_ins`
--

LOCK TABLES `trade_ins` WRITE;
/*!40000 ALTER TABLE `trade_ins` DISABLE KEYS */;
/*!40000 ALTER TABLE `trade_ins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `transId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `trade_in_value` decimal(10,2) DEFAULT 0.00,
  `imei_numbers` text DEFAULT NULL COMMENT 'Comma-separated IMEIs',
  `cancelled` tinyint(1) DEFAULT 0,
  `transDate` datetime DEFAULT current_timestamp(),
  `lastUpdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`transId`),
  KEY `idx_ref` (`ref`),
  KEY `idx_transDate` (`transDate`),
  KEY `idx_staffId` (`staffId`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_payment_status` (`payment_status`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,'Test iPhone 13','TEST001',NULL,1,180000.00,180000.00,180000.00,0.00,'cash','paid',0.00,0.00,1,1,180000.00,'TXN001',0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,0.00,NULL,0,'2024-12-25 10:30:00','2025-12-26 23:45:10'),(2,'Test Samsung S21','TEST002',NULL,1,120000.00,120000.00,120000.00,0.00,'cash','paid',0.00,0.00,1,1,120000.00,'TXN002',0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,0.00,NULL,0,'2024-12-26 14:15:00','2025-12-26 23:45:10'),(3,'Samsung Fast Charger','ACC001',NULL,2,1500.00,3000.00,3000.00,0.00,'cash','paid',0.00,0.00,1,1,3000.00,'TXN003',0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,0.00,NULL,0,'2024-12-27 15:30:00','2025-12-26 23:45:10'),(4,'Phone Case','ACC20241227004','',1,500.00,500.00,500.00,0.00,'cash','paid',500.00,0.00,1,1,500.00,'61592409',0.00,0.00,0.00,0.00,150.00,'Hamid SHah','03220716197','',6,0.00,NULL,0,'2025-12-28 07:54:51','2025-12-28 02:54:51'),(5,'Phone Case','ACC20241227004','',5,500.00,2500.00,2500.00,0.00,'cash','paid',2500.00,0.00,1,1,2500.00,'581985',0.00,0.00,0.00,0.00,750.00,'Ahmed Hassan','0300-1234567','ahmed@example.com',1,0.00,NULL,0,'2025-12-28 12:42:09','2025-12-28 07:42:09'),(6,'Phone Case','ACC20241227004','',1,500.00,500.00,500.00,0.00,'cash','paid',500.00,0.00,1,1,500.00,'93577289',0.00,0.00,0.00,0.00,150.00,'Hassan Raza','0333-5555555','hassan@example.com',3,0.00,NULL,0,'2025-12-28 12:42:51','2025-12-28 07:42:51'),(7,'iPhone Lightning Cable','ACC20241227002','',1,1500.00,1500.00,1500.00,0.00,'cash','paid',1500.00,0.00,1,1,1500.00,'492803974',0.00,0.00,0.00,0.00,450.00,'Ahmed Hassan','0300-1234567','ahmed@example.com',1,0.00,NULL,0,'2025-12-28 13:13:53','2025-12-28 08:13:53'),(9,'Phone Case','ACC20241227004','',10,500.00,5000.00,5000.01,0.01,'cash','paid',5000.00,0.00,1,1,5000.00,'029782',0.00,0.00,0.00,0.00,1500.00,'Hamid SHah','03220716197','',6,0.00,NULL,0,'2025-12-28 13:31:30','2025-12-28 08:31:30'),(10,'Phone Case','ACC20241227004','',1,500.00,500.00,500.00,0.00,'cash','paid',500.00,0.00,1,1,500.00,'54717452',0.00,0.00,0.00,0.00,150.00,'Hamid SHah','03220716197','',6,0.00,NULL,0,'2025-12-28 22:05:26','2025-12-28 17:05:26'),(11,'Phone Case','ACC20241227004','',20,500.00,10000.00,10000.00,0.00,'pos','paid',10000.00,0.00,1,1,10000.00,'548325',0.00,0.00,0.00,0.00,3000.00,'Fatima Ali','0321-9876543','fatima@example.com',2,0.00,NULL,0,'2025-12-28 23:58:54','2025-12-28 18:58:54'),(13,'Wireless Earbuds','ACC20241227003','',30,15000.00,450000.00,450000.00,0.00,'cash','paid',450000.00,0.00,1,1,450000.00,'46893121',0.00,0.00,0.00,0.00,135000.00,'Fatima Ali','0321-9876543','fatima@example.com',2,0.00,NULL,0,'2025-12-29 10:14:01','2025-12-29 05:14:01'),(14,'Phone Case','ACC20241227004','',5,500.00,2500.00,0.00,0.00,'credit','credit',0.00,2500.00,1,1,2500.00,'8774592',0.00,0.00,0.00,0.00,750.00,'Bilal Ahmed','0312-8888888','bilal@example.com',5,0.00,NULL,0,'2025-12-30 23:43:36','2025-12-30 18:43:36'),(24,'Phone Case','ACC20241227004','',1,500.00,500.00,440.00,0.00,'cash','paid',440.00,0.00,1,1,440.00,'943765',0.00,0.00,60.00,0.00,90.00,'','','',NULL,0.00,NULL,0,'2026-01-01 02:11:46','2025-12-31 21:11:46'),(25,'Phone Case','ACC20241227004','',6,500.00,3000.00,2500.00,0.00,'cash','paid',2500.00,0.00,1,1,2500.00,'3206450',0.00,0.00,500.00,0.00,400.00,'','','',NULL,0.00,NULL,0,'2026-01-01 02:26:07','2025-12-31 21:26:07'),(26,'S25 Ultra','MOB20251230002','',1,150000.00,150000.00,145000.00,0.00,'cash','paid',145000.00,0.00,1,1,145000.00,'3904938',0.00,0.00,5000.00,0.00,25000.00,'','','',NULL,0.00,'351234567890223',0,'2026-01-01 03:19:41','2025-12-31 22:19:41'),(27,'Wireless Earbuds','ACC20241227003','',1,15000.00,15000.00,15000.00,0.00,'credit','credit',0.00,15000.00,1,1,15000.00,'290587246',0.00,0.00,0.00,0.00,4500.00,'Ayesha Khan','0345-7777777','ayesha@example.com',4,0.00,NULL,0,'2026-01-01 07:06:57','2026-01-01 02:06:57');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `inventory_available`
--

/*!50001 DROP VIEW IF EXISTS `inventory_available`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `inventory_available` AS select `items`.`id` AS `id`,`items`.`name` AS `name`,`items`.`code` AS `code`,`items`.`brand` AS `brand`,`items`.`model` AS `model`,`items`.`category` AS `category`,`items`.`item_type` AS `item_type`,`items`.`quantity` AS `quantity`,`items`.`unitPrice` AS `unitPrice`,`items`.`cost_price` AS `cost_price`,`items`.`warranty_months` AS `warranty_months`,`items`.`description` AS `description`,`items`.`created_at` AS `created_at`,`items`.`updated_at` AS `updated_at`,case when `items`.`item_type` = 'serialized' then (select count(0) from `item_serials` where `item_serials`.`item_id` = `items`.`id` and `item_serials`.`status` = 'available') else `items`.`quantity` end AS `available_qty` from `items` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `profit_report`
--

/*!50001 DROP VIEW IF EXISTS `profit_report`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `profit_report` AS select `t`.`ref` AS `ref`,`t`.`transDate` AS `date`,`t`.`customer_id` AS `customer_id`,`c`.`name` AS `customer_name`,`t`.`totalPrice` AS `total_amount`,`t`.`profit_amount` AS `profit_amount`,`t`.`modeOfPayment` AS `payment_method`,`t`.`staffId` AS `admin_id` from (`transactions` `t` left join `customers` `c` on(`t`.`customer_id` = `c`.`id`)) where `t`.`profit_amount` is not null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-02  2:38:35
