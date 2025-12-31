-- Check if IMEI 351234567890123 already exists
SELECT item_id, imei_number, color, cost_price, status 
FROM item_serials 
WHERE imei_number = '351234567890123';

-- Check all IMEIs for S25 Ultra (MOB20251230002)
SELECT item_id, imei_number, color, cost_price, status 
FROM item_serials 
WHERE item_id = (SELECT id FROM items WHERE code = 'MOB20251230002');
