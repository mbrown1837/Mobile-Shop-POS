-- Update cost_price for all items (70% of selling price)
UPDATE `items` 
SET `cost_price` = ROUND(unitPrice * 0.7, 2) 
WHERE `cost_price` = 0 OR `cost_price` IS NULL;

-- Verify the update
SELECT id, code, name, unitPrice, cost_price, (unitPrice - cost_price) as profit 
FROM items 
LIMIT 10;
