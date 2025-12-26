-- ============================================
-- Mobile Shop POS - Test Data (Part 3)
-- IMEI Numbers for Serialized Items
-- ============================================

-- ============================================
-- 5. ITEM SERIALS (IMEI Numbers)
-- ============================================

-- iPhone 13 Pro Max (5 units)
INSERT INTO `item_serials` (`item_id`, `imei_number`, `serial_number`, `color`, `storage`, `cost_price`, `selling_price`, `status`) VALUES
(10, '351234567890123', 'F2LY3456ABC', 'Graphite', '256GB', 265000.00, 285000.00, 'available'),
(10, '351234567890124', 'F2LY3456ABD', 'Sierra Blue', '256GB', 265000.00, 285000.00, 'available'),
(10, '351234567890125', 'F2LY3456ABE', 'Gold', '256GB', 265000.00, 285000.00, 'sold'),
(10, '351234567890126', 'F2LY3456ABF', 'Silver', '256GB', 265000.00, 285000.00, 'available'),
(10, '351234567890127', 'F2LY3456ABG', 'Graphite', '256GB', 265000.00, 285000.00, 'available');

-- Samsung Galaxy S23 Ultra (4 units)
INSERT INTO `item_serials` (`item_id`, `imei_number`, `serial_number`, `color`, `storage`, `cost_price`, `selling_price`, `status`) VALUES
(11, '352345678901234', 'R58N1234XYZ', 'Phantom Black', '256GB', 255000.00, 275000.00, 'available'),
(11, '352345678901235', 'R58N1234XYA', 'Cream', '256GB', 255000.00, 275000.00, 'available'),
(11, '352345678901236', 'R58N1234XYB', 'Green', '256GB', 255000.00, 275000.00, 'sold'),
(11, '352345678901237', 'R58N1234XYC', 'Phantom Black', '256GB', 255000.00, 275000.00, 'available');

-- iPhone 14 (6 units)
INSERT INTO `item_serials` (`item_id`, `imei_number`, `serial_number`, `color`, `storage`, `cost_price`, `selling_price`, `status`) VALUES
(12, '353456789012345', 'F3MZ4567DEF', 'Midnight', '128GB', 180000.00, 195000.00, 'available'),
(12, '353456789012346', 'F3MZ4567DEG', 'Starlight', '128GB', 180000.00, 195000.00, 'available'),
(12, '353456789012347', 'F3MZ4567DEH', 'Blue', '128GB', 180000.00, 195000.00, 'available'),
(12, '353456789012348', 'F3MZ4567DEI', 'Purple', '128GB', 180000.00, 195000.00, 'sold'),
(12, '353456789012349', 'F3MZ4567DEJ', 'Red', '128GB', 180000.00, 195000.00, 'available'),
(12, '353456789012350', 'F3MZ4567DEK', 'Midnight', '128GB', 180000.00, 195000.00, 'available');

-- Samsung Galaxy A54 (8 units)
INSERT INTO `item_serials` (`item_id`, `imei_number`, `serial_number`, `color`, `storage`, `cost_price`, `selling_price`, `status`) VALUES
(13, '354567890123456', 'R59P2345MNO', 'Awesome Violet', '128GB', 68000.00, 75000.00, 'available'),
(13, '354567890123457', 'R59P2345MNP', 'Awesome Lime', '128GB', 68000.00, 75000.00, 'available'),
(13, '354567890123458', 'R59P2345MNQ', 'Awesome Graphite', '128GB', 68000.00, 75000.00, 'available'),
(13, '354567890123459', 'R59P2345MNR', 'Awesome White', '128GB', 68000.00, 75000.00, 'sold'),
(13, '354567890123460', 'R59P2345MNS', 'Awesome Violet', '128GB', 68000.00, 75000.00, 'available'),
(13, '354567890123461', 'R59P2345MNT', 'Awesome Lime', '128GB', 68000.00, 75000.00, 'available'),
(13, '354567890123462', 'R59P2345MNU', 'Awesome Graphite', '128GB', 68000.00, 75000.00, 'available'),
(13, '354567890123463', 'R59P2345MNV', 'Awesome White', '128GB', 68000.00, 75000.00, 'available');

-- Xiaomi 13 Pro (5 units)
INSERT INTO `item_serials` (`item_id`, `imei_number`, `serial_number`, `color`, `storage`, `cost_price`, `selling_price`, `status`) VALUES
(14, '355678901234567', 'M13P3456RST', 'Ceramic Black', '256GB', 135000.00, 145000.00, 'available'),
(14, '355678901234568', 'M13P3456RSU', 'Ceramic White', '256GB', 135000.00, 145000.00, 'available'),
(14, '355678901234569', 'M13P3456RSV', 'Flora Green', '256GB', 135000.00, 145000.00, 'sold'),
(14, '355678901234570', 'M13P3456RSW', 'Ceramic Black', '256GB', 135000.00, 145000.00, 'available'),
(14, '355678901234571', 'M13P3456RSX', 'Ceramic White', '256GB', 135000.00, 145000.00, 'available');
