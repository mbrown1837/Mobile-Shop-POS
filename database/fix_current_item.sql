-- Fix the current item's cost_price
UPDATE items 
SET cost_price = 120000.00 
WHERE code = 'MOB20251229001';

-- Verify
SELECT id, code, name, unitPrice, cost_price, item_type 
FROM items 
WHERE code = 'MOB20251229001';

-- Check available quantity from view
SELECT id, code, name, available_qty, cost_price 
FROM inventory_available 
WHERE code = 'MOB20251229001';
