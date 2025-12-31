-- Check Samsung Galaxy S24 Ultra item
SELECT id, code, name, unitPrice, cost_price, item_type, quantity 
FROM items 
WHERE code = 'MOB20251230003';

-- Check IMEIs for S24 Ultra
SELECT item_id, imei_number, color, cost_price, status 
FROM item_serials 
WHERE item_id = (SELECT id FROM items WHERE code = 'MOB20251230003');

-- Check available quantity from view
SELECT id, code, name, available_qty, cost_price 
FROM inventory_available 
WHERE code = 'MOB20251230003';
