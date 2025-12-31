-- Fix inventory_available view to include cost_price and all necessary fields
-- Run this SQL in your database

DROP VIEW IF EXISTS inventory_available;

CREATE VIEW inventory_available AS
SELECT 
    items.id,
    items.name,
    items.code,
    items.brand,
    items.model,
    items.category,
    items.item_type,
    items.quantity,
    items.unitPrice,
    items.cost_price,
    items.warranty_months,
    items.description,
    items.created_at,
    items.updated_at,
    -- Calculate available quantity for serialized items
    CASE 
        WHEN items.item_type = 'serialized' THEN (
            SELECT COUNT(*) 
            FROM item_serials 
            WHERE item_serials.item_id = items.id 
            AND item_serials.status = 'available'
        )
        ELSE items.quantity
    END AS available_qty
FROM items;

-- Verify the view
SELECT id, code, name, unitPrice, cost_price, available_qty 
FROM inventory_available 
LIMIT 5;
