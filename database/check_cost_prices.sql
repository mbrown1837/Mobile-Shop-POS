-- Check if cost_price column exists and has data
SELECT id, code, name, unitPrice, cost_price, (unitPrice - cost_price) as profit 
FROM items 
LIMIT 10;
