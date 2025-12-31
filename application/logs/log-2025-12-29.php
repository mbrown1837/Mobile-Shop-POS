<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-12-29 02:32:10 --> Severity: Compile Error --> Cannot redeclare Items::delete() C:\xampp\htdocs\mobile-shop-pos\application\controllers\Items.php 699
ERROR - 2025-12-29 02:32:11 --> Severity: Compile Error --> Cannot redeclare Items::delete() C:\xampp\htdocs\mobile-shop-pos\application\controllers\Items.php 699
ERROR - 2025-12-29 02:32:11 --> Severity: Compile Error --> Cannot redeclare Items::delete() C:\xampp\htdocs\mobile-shop-pos\application\controllers\Items.php 699
ERROR - 2025-12-29 02:32:11 --> Severity: Compile Error --> Cannot redeclare Items::delete() C:\xampp\htdocs\mobile-shop-pos\application\controllers\Items.php 699
ERROR - 2025-12-29 02:32:12 --> Severity: Compile Error --> Cannot redeclare Items::delete() C:\xampp\htdocs\mobile-shop-pos\application\controllers\Items.php 699
ERROR - 2025-12-29 02:32:12 --> Severity: Compile Error --> Cannot redeclare Items::delete() C:\xampp\htdocs\mobile-shop-pos\application\controllers\Items.php 699
ERROR - 2025-12-29 02:32:12 --> Severity: Compile Error --> Cannot redeclare Items::delete() C:\xampp\htdocs\mobile-shop-pos\application\controllers\Items.php 699
ERROR - 2025-12-29 02:49:06 --> Query error: Unknown column 'itemId' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `transactions`
WHERE `itemId` = '12'
ERROR - 2025-12-29 02:49:15 --> Query error: Unknown column 'itemId' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `transactions`
WHERE `itemId` = '12'
ERROR - 2025-12-29 02:54:02 --> Query error: Unknown column 'itemId' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `transactions`
WHERE `itemId` = '10'
ERROR - 2025-12-29 03:08:03 --> Query error: Unknown column 'costPrice' in 'field list' - Invalid query: UPDATE `items` SET `name` = 'iPhone Lightning Cable', `unitPrice` = '1500.00', `description` = 'Original Apple Lightning cable 1m', `category` = 'accessory', `brand` = 'Apple', `model` = 'Lightning to USB-C', `warranty_months` = 12, `costPrice` = '2000'
WHERE `id` = '2'
ERROR - 2025-12-29 03:27:46 --> Query error: Unknown column 'costPrice' in 'field list' - Invalid query: UPDATE `items` SET `name` = 'iPhone Lightning Cable', `unitPrice` = '1500.00', `description` = 'Original Apple Lightning cable 1m', `category` = 'accessory', `brand` = 'Apple', `model` = 'Lightning to USB-C', `warranty_months` = 12, `costPrice` = '2000'
WHERE `id` = '2'
ERROR - 2025-12-29 03:27:47 --> Query error: Unknown column 'costPrice' in 'field list' - Invalid query: UPDATE `items` SET `name` = 'iPhone Lightning Cable', `unitPrice` = '1500.00', `description` = 'Original Apple Lightning cable 1m', `category` = 'accessory', `brand` = 'Apple', `model` = 'Lightning to USB-C', `warranty_months` = 12, `costPrice` = '2000'
WHERE `id` = '2'
ERROR - 2025-12-29 03:27:47 --> Query error: Unknown column 'costPrice' in 'field list' - Invalid query: UPDATE `items` SET `name` = 'iPhone Lightning Cable', `unitPrice` = '1500.00', `description` = 'Original Apple Lightning cable 1m', `category` = 'accessory', `brand` = 'Apple', `model` = 'Lightning to USB-C', `warranty_months` = 12, `costPrice` = '2000'
WHERE `id` = '2'
ERROR - 2025-12-29 03:27:50 --> Query error: Unknown column 'costPrice' in 'field list' - Invalid query: UPDATE `items` SET `name` = 'iPhone Lightning Cable', `unitPrice` = '1500.00', `description` = 'Original Apple Lightning cable 1m', `category` = 'accessory', `brand` = 'Apple', `model` = 'Lightning to USB-C', `warranty_months` = 12, `costPrice` = '1500'
WHERE `id` = '2'
ERROR - 2025-12-29 03:32:11 --> Query error: Unknown column 'costPrice' in 'field list' - Invalid query: UPDATE `items` SET `name` = 'iPhone Lightning Cable', `unitPrice` = '1500.00', `description` = 'Original Apple Lightning cable 1m', `category` = 'accessory', `brand` = 'Apple', `model` = 'Lightning to USB-C', `warranty_months` = 12, `costPrice` = '2000'
WHERE `id` = '2'
ERROR - 2025-12-29 03:39:51 --> Severity: Warning --> Undefined array key "pos_cart" C:\xampp\htdocs\mobile-shop-pos\application\libraries\Genlib.php 219
ERROR - 2025-12-29 03:39:51 --> Severity: Warning --> Trying to access array offset on value of type null C:\xampp\htdocs\mobile-shop-pos\application\libraries\Genlib.php 219
ERROR - 2025-12-29 04:20:53 --> 404 Page Not Found: Reports/lowStock
ERROR - 2025-12-29 04:20:55 --> 404 Page Not Found: Reports/stockValue
ERROR - 2025-12-29 04:20:56 --> 404 Page Not Found: Reports/imeiStatus
ERROR - 2025-12-29 04:21:05 --> Severity: Warning --> Undefined array key "pos_cart" C:\xampp\htdocs\mobile-shop-pos\application\libraries\Genlib.php 219
ERROR - 2025-12-29 04:21:05 --> Severity: Warning --> Trying to access array offset on value of type null C:\xampp\htdocs\mobile-shop-pos\application\libraries\Genlib.php 219
ERROR - 2025-12-29 04:28:15 --> Severity: error --> Exception: count(): Argument #1 ($value) must be of type Countable|array, bool given C:\xampp\htdocs\mobile-shop-pos\application\controllers\Administrators.php 59
ERROR - 2025-12-29 10:13:11 --> Query error: Cannot add or update a child row: a foreign key constraint fails (`mobile_shop_pos`.`transactions`, CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL) - Invalid query: INSERT INTO `transactions` (transDate, `itemName`, `itemCode`, `description`, `quantity`, `unitPrice`, `totalPrice`, `totalMoneySpent`, `amountTendered`, `changeDue`, `modeOfPayment`, `payment_status`, `paid_amount`, `credit_amount`, `transType`, `staffId`, `ref`, `vatAmount`, `vatPercentage`, `discount_amount`, `discount_percentage`, `profit_amount`, `cust_name`, `cust_phone`, `cust_email`, `customer_id`, `trade_in_value`, `imei_numbers`) VALUES (NOW(), 'Wireless Earbuds', 'ACC20241227003', '', '30', '15000.00', 450000, 450000, '450000', 0, 'cash', 'paid', 450000, 0, 1, '1', '73041517', 0, 0, 0, 0, 135000, '', '', '', '', 0, NULL)
ERROR - 2025-12-29 10:16:09 --> Query error: Unknown column 'dateAdded' in 'field list' - Invalid query: INSERT INTO `items` (dateAdded, `name`, `code`, `item_type`, `unitPrice`, `quantity`, `brand`, `model`, `warranty_months`, `description`, `category`) VALUES (NOW(), 'S21 Ultra', 'MOB20251229001', 'serialized', '150000', 0, 'Samsung', 'S25', '24', '10/9', 'mobile')
ERROR - 2025-12-29 12:59:17 --> Query error: Unknown column 'dateAdded' in 'field list' - Invalid query: INSERT INTO `items` (dateAdded, `name`, `code`, `item_type`, `unitPrice`, `quantity`, `brand`, `model`, `warranty_months`, `description`, `category`) VALUES (NOW(), 'S21 Ultra', 'MOB20251229001', 'serialized', '180000', 0, 'Samsung', 'S25 Ultra', '12', 'test', 'mobile')
