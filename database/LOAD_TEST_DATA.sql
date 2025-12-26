-- ============================================
-- Mobile Shop POS - Complete Test Data Loader
-- Run this file to load all test data
-- ============================================

-- Usage:
-- mysql -u root -p mobile_shop_pos < database/LOAD_TEST_DATA.sql
-- OR import via phpMyAdmin

SOURCE database/test_data_part1.sql;
SOURCE database/test_data_part2.sql;
SOURCE database/test_data_part3.sql;
SOURCE database/test_data_part4.sql;
SOURCE database/test_data_part5.sql;

-- Verify data loaded
SELECT 'Admin Users:' as Info, COUNT(*) as Count FROM admin
UNION ALL
SELECT 'Customers:', COUNT(*) FROM customers
UNION ALL
SELECT 'Items:', COUNT(*) FROM items
UNION ALL
SELECT 'IMEI Numbers:', COUNT(*) FROM item_serials
UNION ALL
SELECT 'Transactions:', COUNT(*) FROM transactions
UNION ALL
SELECT 'Ledger Entries:', COUNT(*) FROM customer_ledger
UNION ALL
SELECT 'Trade-ins:', COUNT(*) FROM trade_ins;

SELECT '✓ Test data loaded successfully!' as Status;
