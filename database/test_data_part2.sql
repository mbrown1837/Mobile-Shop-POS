-- ============================================
-- Mobile Shop POS - Test Data (Part 2)
-- Items and IMEI Numbers
-- ============================================

-- ============================================
-- 3. ITEMS (Standard Items - Accessories)
-- ============================================
INSERT INTO `items` (`id`, `code`, `name`, `item_type`, `category`, `brand`, `model`, `quantity`, `unitPrice`, `warranty_months`, `description`) VALUES
(1, 'ACC20241227001', 'Samsung Fast Charger 25W', 'standard', 'accessory', 'Samsung', '25W EP-TA800', 50, 2500.00, 6, 'Original Samsung 25W fast charger with Type-C cable'),
(2, 'ACC20241227002', 'iPhone Lightning Cable', 'standard', 'accessory', 'Apple', 'Lightning to USB-C', 100, 1500.00, 12, 'Original Apple Lightning cable 1m'),
(3, 'ACC20241227003', 'Wireless Earbuds', 'standard', 'accessory', 'Samsung', 'Galaxy Buds 2', 30, 15000.00, 12, 'Samsung Galaxy Buds 2 with ANC'),
(4, 'ACC20241227004', 'Phone Case', 'standard', 'accessory', 'Generic', 'Universal', 200, 500.00, 0, 'Silicone phone case - various colors'),
(5, 'ACC20241227005', 'Screen Protector', 'standard', 'accessory', 'Generic', 'Tempered Glass', 150, 300.00, 0, '9H tempered glass screen protector');

-- ============================================
-- 4. ITEMS (Serialized Items - Mobiles)
-- ============================================
INSERT INTO `items` (`id`, `code`, `name`, `item_type`, `category`, `brand`, `model`, `quantity`, `unitPrice`, `warranty_months`, `description`) VALUES
(10, 'MOB20241227001', 'iPhone 13 Pro Max', 'serialized', 'mobile', 'Apple', 'iPhone 13 Pro Max', 0, 285000.00, 12, 'iPhone 13 Pro Max 256GB - Factory Unlocked'),
(11, 'MOB20241227002', 'Samsung Galaxy S23 Ultra', 'serialized', 'mobile', 'Samsung', 'Galaxy S23 Ultra', 0, 275000.00, 12, 'Samsung S23 Ultra 256GB - Dual SIM'),
(12, 'MOB20241227003', 'iPhone 14', 'serialized', 'mobile', 'Apple', 'iPhone 14', 0, 195000.00, 12, 'iPhone 14 128GB - Factory Unlocked'),
(13, 'MOB20241227004', 'Samsung Galaxy A54', 'serialized', 'mobile', 'Samsung', 'Galaxy A54', 0, 75000.00, 12, 'Samsung A54 128GB - Dual SIM'),
(14, 'MOB20241227005', 'Xiaomi 13 Pro', 'serialized', 'mobile', 'Xiaomi', 'Mi 13 Pro', 0, 145000.00, 12, 'Xiaomi 13 Pro 256GB - Global Version');
