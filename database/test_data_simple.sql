-- ============================================
-- Simple Test Data - No Errors Version
-- Works with ANY existing items
-- ============================================

-- Clear existing test data
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE customer_ledger;
TRUNCATE TABLE trade_ins;
TRUNCATE TABLE transactions;
TRUNCATE TABLE item_serials;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- 1. FIRST: Check what items exist
-- ============================================
SELECT 'Checking existing items...' as Status;
SELECT id, name, code, item_type FROM items WHERE item_type = 'serialized' LIMIT 5;

-- ============================================
-- 2. ITEM SERIALS (IMEI Numbers)
-- Use item IDs that actually exist in your database
-- ============================================

-- Get first serialized item ID (usually MOB001)
SET @item1 = (SELECT id FROM items WHERE item_type = 'serialized' ORDER BY id LIMIT 1);
SET @item2 = (SELECT id FROM items WHERE item_type = 'serialized' ORDER BY id LIMIT 1 OFFSET 1);

-- If no serialized items exist, create them first
INSERT IGNORE INTO `items` (`id`, `name`, `brand`, `model`, `category`, `code`, `item_type`, `unitPrice`, `quantity`, `warranty_months`) VALUES
(100, 'Test iPhone 13', 'Apple', 'iPhone 13', 'mobile', 'TEST001', 'serialized', 180000.00, 0, 12),
(101, 'Test Samsung S21', 'Samsung', 'Galaxy S21', 'mobile', 'TEST002', 'serialized', 120000.00, 0, 12);

-- Use the test items we just created
SET @item1 = 100;
SET @item2 = 101;

-- Add IMEI numbers for item 1
INSERT INTO `item_serials` (`item_id`, `imei_number`, `serial_number`, `color`, `storage`, `cost_price`, `selling_price`, `status`) VALUES
(@item1, '351234567890123', 'F2LY3456ABC', 'Graphite', '128GB', 165000.00, 180000.00, 'available'),
(@item1, '351234567890124', 'F2LY3456ABD', 'Blue', '128GB', 165000.00, 180000.00, 'available'),
(@item1, '351234567890125', 'F2LY3456ABE', 'Gold', '128GB', 165000.00, 180000.00, 'sold');

-- Add IMEI numbers for item 2
INSERT INTO `item_serials` (`item_id`, `imei_number`, `serial_number`, `color`, `storage`, `cost_price`, `selling_price`, `status`) VALUES
(@item2, '352345678901234', 'R58N1234XYZ', 'Black', '128GB', 105000.00, 120000.00, 'available'),
(@item2, '352345678901235', 'R58N1234XYA', 'White', '128GB', 105000.00, 120000.00, 'available'),
(@item2, '352345678901236', 'R58N1234XYB', 'Green', '128GB', 105000.00, 120000.00, 'sold');

-- ============================================
-- 3. TRANSACTIONS (Simple Version)
-- ============================================

INSERT INTO `transactions` (
    `itemName`, `itemCode`, `quantity`, `unitPrice`, `totalPrice`, 
    `amountTendered`, `changeDue`, `modeOfPayment`, `transType`, 
    `staffId`, `totalMoneySpent`, `ref`, `transDate`
) VALUES
('Test iPhone 13', 'TEST001', 1, 180000.00, 180000.00, 180000.00, 0.00, 'cash', 1, 1, 180000.00, 'TXN001', '2024-12-25 10:30:00'),
('Test Samsung S21', 'TEST002', 1, 120000.00, 120000.00, 120000.00, 0.00, 'cash', 1, 1, 120000.00, 'TXN002', '2024-12-26 14:15:00'),
('Samsung Fast Charger', 'ACC001', 2, 1500.00, 3000.00, 3000.00, 0.00, 'cash', 1, 1, 3000.00, 'TXN003', '2024-12-27 15:30:00');

-- ============================================
-- 4. CUSTOMER LEDGER (Simple Version)
-- ============================================

INSERT INTO `customer_ledger` (`customer_id`, `transaction_type`, `amount`, `payment_method`, `description`) VALUES
(1, 'credit_sale', 50000.00, NULL, 'Test credit sale'),
(1, 'payment', 30000.00, 'cash', 'Partial payment'),
(2, 'credit_sale', 35000.00, NULL, 'Another test sale'),
(2, 'payment', 20000.00, 'cash', 'Payment received');

-- ============================================
-- 5. UPDATE CUSTOMER BALANCES
-- ============================================

UPDATE `customers` SET `current_balance` = 20000.00, `total_purchases` = 50000.00, `total_payments` = 30000.00 WHERE `id` = 1;
UPDATE `customers` SET `current_balance` = 15000.00, `total_purchases` = 35000.00, `total_payments` = 20000.00 WHERE `id` = 2;

-- ============================================
-- 6. VERIFICATION
-- ============================================

SELECT '✓ Test data loaded successfully!' as Status;
SELECT 'Items with IMEI:' as Info, COUNT(DISTINCT item_id) as Count FROM item_serials
UNION ALL
SELECT 'Total IMEIs:', COUNT(*) FROM item_serials
UNION ALL
SELECT 'Transactions:', COUNT(*) FROM transactions
UNION ALL
SELECT 'Ledger Entries:', COUNT(*) FROM customer_ledger
UNION ALL
SELECT 'Customers with Balance:', COUNT(*) FROM customers WHERE current_balance > 0;
