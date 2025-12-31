-- Check Test 21 item details
SELECT id, code, name, unitPrice, cost_price, item_type, quantity 
FROM items 
WHERE code = 'MOB20251230001';

-- Check IMEIs for Test 21
SELECT item_id, imei_number, color, cost_price, status 
FROM item_serials 
WHERE item_id = (SELECT id FROM items WHERE code = 'MOB20251230001');

-- Check available quantity from view
SELECT id, code, name, available_qty, cost_price 
FROM inventory_available 
WHERE code = 'MOB20251230001';
