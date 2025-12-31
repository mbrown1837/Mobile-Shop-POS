-- Verify cost_price column exists in items table
SHOW COLUMNS FROM items LIKE 'cost_price';

-- If not exists, add it
-- ALTER TABLE items ADD COLUMN cost_price DECIMAL(10,2) DEFAULT 0.00 AFTER unitPrice;

-- Check the newly added item
SELECT id, code, name, unitPrice, cost_price, item_type, quantity 
FROM items 
WHERE code = 'MOB20251229001';

-- Check IMEIs for that item
SELECT item_id, imei_number, color, cost_price, status 
FROM item_serials 
WHERE item_id = (SELECT id FROM items WHERE code = 'MOB20251229001');
