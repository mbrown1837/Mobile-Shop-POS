-- ============================================
-- Mobile Shop POS - Test Data (Part 4)
-- Transactions and Customer Ledger
-- ============================================

-- ============================================
-- 6. TRANSACTIONS (Sample Sales)
-- ============================================

-- Transaction 1: Cash sale - iPhone 13 Pro Max
INSERT INTO `transactions` (`ref`, `itemName`, `itemCode`, `quantity`, `unitPrice`, `totalPrice`, `modeOfPayment`, `staffId`, `transDate`, `customer_id`, `payment_status`, `paid_amount`, `credit_amount`, `profit_amount`, `imei_numbers`) VALUES
('TXN20241227001', 'iPhone 13 Pro Max', 'MOB20241227001', 1, 285000.00, 285000.00, 'cash', 1, '2024-12-25 10:30:00', NULL, 'paid', 285000.00, 0.00, 20000.00, '351234567890125');

-- Transaction 2: Credit sale - Samsung S23 Ultra
INSERT INTO `transactions` (`ref`, `itemName`, `itemCode`, `quantity`, `unitPrice`, `totalPrice`, `modeOfPayment`, `staffId`, `transDate`, `customer_id`, `payment_status`, `paid_amount`, `credit_amount`, `profit_amount`, `imei_numbers`) VALUES
('TXN20241227002', 'Samsung Galaxy S23 Ultra', 'MOB20241227002', 1, 275000.00, 275000.00, 'credit', 2, '2024-12-26 14:15:00', 1, 'credit', 0.00, 275000.00, 20000.00, '352345678901236');

-- Transaction 3: Partial payment - iPhone 14
INSERT INTO `transactions` (`ref`, `itemName`, `itemCode`, `quantity`, `unitPrice`, `totalPrice`, `modeOfPayment`, `staffId`, `transDate`, `customer_id`, `payment_status`, `paid_amount`, `credit_amount`, `profit_amount`, `imei_numbers`) VALUES
('TXN20241227003', 'iPhone 14', 'MOB20241227003', 1, 195000.00, 195000.00, 'partial', 3, '2024-12-26 16:45:00', 3, 'partial', 170000.00, 25000.00, 15000.00, '353456789012348');

-- Transaction 4: Cash sale - Samsung A54
INSERT INTO `transactions` (`ref`, `itemName`, `itemCode`, `quantity`, `unitPrice`, `totalPrice`, `modeOfPayment`, `staffId`, `transDate`, `customer_id`, `payment_status`, `paid_amount`, `credit_amount`, `profit_amount`, `imei_numbers`) VALUES
('TXN20241227004', 'Samsung Galaxy A54', 'MOB20241227004', 1, 75000.00, 75000.00, 'pos', 1, '2024-12-27 11:20:00', NULL, 'paid', 75000.00, 0.00, 7000.00, '354567890123459');

-- Transaction 5: Cash sale - Xiaomi 13 Pro
INSERT INTO `transactions` (`ref`, `itemName`, `itemCode`, `quantity`, `unitPrice`, `totalPrice`, `modeOfPayment`, `staffId`, `transDate`, `customer_id`, `payment_status`, `paid_amount`, `credit_amount`, `profit_amount`, `imei_numbers`) VALUES
('TXN20241227005', 'Xiaomi 13 Pro', 'MOB20241227005', 1, 145000.00, 145000.00, 'cash', 2, '2024-12-27 13:00:00', NULL, 'paid', 145000.00, 0.00, 10000.00, '355678901234569');

-- Transaction 6: Accessory sale (multiple items)
INSERT INTO `transactions` (`ref`, `itemName`, `itemCode`, `quantity`, `unitPrice`, `totalPrice`, `modeOfPayment`, `staffId`, `transDate`, `customer_id`, `payment_status`, `paid_amount`, `credit_amount`, `profit_amount`) VALUES
('TXN20241227006', 'Samsung Fast Charger 25W', 'ACC20241227001', 2, 2500.00, 5000.00, 'cash', 3, '2024-12-27 15:30:00', NULL, 'paid', 5000.00, 0.00, 1000.00, NULL),
('TXN20241227007', 'iPhone Lightning Cable', 'ACC20241227002', 3, 1500.00, 4500.00, 'cash', 3, '2024-12-27 15:30:00', NULL, 'paid', 4500.00, 0.00, 900.00, NULL),
('TXN20241227008', 'Wireless Earbuds', 'ACC20241227003', 1, 15000.00, 15000.00, 'pos', 1, '2024-12-27 16:00:00', NULL, 'paid', 15000.00, 0.00, 3000.00, NULL);
