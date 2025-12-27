-- ============================================
-- Mobile Shop POS - Test Data (Part 5)
-- Customer Ledger and Trade-ins
-- ============================================

-- ============================================
-- 7. CUSTOMER LEDGER (Payment History)
-- ============================================

-- Customer 1 (Ahmed Hassan) - Credit sale and payments
INSERT INTO `customer_ledger` (`customer_id`, `transaction_type`, `amount`, `payment_method`, `description`, `created_at`) VALUES
(1, 'credit_sale', 275000.00, NULL, 'Samsung Galaxy S23 Ultra - TXN20241227002', '2024-12-26 14:15:00'),
(1, 'payment', 100000.00, 'cash', 'Partial payment received', '2024-12-26 18:00:00'),
(1, 'payment', 50000.00, 'bank_transfer', 'Bank transfer payment', '2024-12-27 10:00:00'),
(1, 'payment', 110000.00, 'cash', 'Final payment received', '2024-12-27 14:00:00');

-- Customer 3 (Hassan Raza) - Partial payment
INSERT INTO `customer_ledger` (`customer_id`, `transaction_type`, `amount`, `payment_method`, `description`, `created_at`) VALUES
(3, 'credit_sale', 195000.00, NULL, 'iPhone 14 - TXN20241227003', '2024-12-26 16:45:00'),
(3, 'payment', 170000.00, 'cash', 'Partial payment at time of sale', '2024-12-26 16:45:00');

-- Customer 5 (Bilal Ahmed) - Old balance
INSERT INTO `customer_ledger` (`customer_id`, `transaction_type`, `amount`, `payment_method`, `description`, `created_at`) VALUES
(5, 'credit_sale', 35000.00, NULL, 'Previous purchase - accessories', '2024-12-20 12:00:00'),
(5, 'payment', 30000.00, 'cash', 'Partial payment', '2024-12-22 15:00:00');

-- ============================================
-- 8. TRADE-INS (Old Phone Exchange)
-- ============================================

INSERT INTO `trade_ins` (`transaction_id`, `customer_id`, `brand`, `model`, `imei_number`, `device_condition`, `trade_in_value`, `notes`, `created_at`) VALUES
(2, 1, 'Samsung', 'Galaxy S21', '351111111111111', 'good', 85000.00, 'Screen minor scratches, battery 85%', '2024-12-26 14:15:00'),
(3, 3, 'iPhone', 'iPhone 11', '352222222222222', 'fair', 65000.00, 'Back glass cracked, working fine', '2024-12-26 16:45:00');

-- ============================================
-- 9. UPDATE ITEM SERIALS STATUS
-- ============================================

-- Mark sold IMEIs
UPDATE `item_serials` SET `status` = 'sold', `sold_date` = '2024-12-25 10:30:00' WHERE `imei_number` = '351234567890125';
UPDATE `item_serials` SET `status` = 'sold', `sold_date` = '2024-12-26 14:15:00' WHERE `imei_number` = '352345678901236';
UPDATE `item_serials` SET `status` = 'sold', `sold_date` = '2024-12-26 16:45:00' WHERE `imei_number` = '353456789012348';
UPDATE `item_serials` SET `status` = 'sold', `sold_date` = '2024-12-27 11:20:00' WHERE `imei_number` = '354567890123459';
UPDATE `item_serials` SET `status` = 'sold', `sold_date` = '2024-12-27 13:00:00' WHERE `imei_number` = '355678901234569';

-- ============================================
-- 10. UPDATE CUSTOMER BALANCES
-- ============================================

-- Customer 1: 275000 - 260000 = 15000 remaining
UPDATE `customers` SET `current_balance` = 15000.00, `total_purchases` = 275000.00, `total_payments` = 260000.00 WHERE `id` = 1;

-- Customer 3: 195000 - 170000 = 25000 remaining
UPDATE `customers` SET `current_balance` = 25000.00, `total_purchases` = 195000.00, `total_payments` = 170000.00 WHERE `id` = 3;

-- Customer 5: 35000 - 30000 = 5000 remaining
UPDATE `customers` SET `current_balance` = 5000.00, `total_purchases` = 35000.00, `total_payments` = 30000.00 WHERE `id` = 5;

-- ============================================
-- COMPLETE! Test data loaded successfully
-- ============================================
