-- Cleanup test data and reorganize items by color
-- Run this SQL in your database

-- Step 1: Delete test transactions (if any)
DELETE FROM transactions WHERE itemCode IN ('TEST001', 'TEST002');

-- Step 2: Delete test item serials (IMEIs)
DELETE FROM item_serials WHERE item_id IN (100, 101);

-- Step 3: Delete test items
DELETE FROM items WHERE code IN ('TEST001', 'TEST002');

-- Step 4: Verify deletion
SELECT id, code, name FROM items WHERE code LIKE 'TEST%';

-- Note: After cleanup, you can add new items with proper color-wise separation:
-- Example:
-- Samsung S21 Black (Code: SAM-S21-BLK)
-- Samsung S21 White (Code: SAM-S21-WHT)
-- Samsung S21 Green (Code: SAM-S21-GRN)
