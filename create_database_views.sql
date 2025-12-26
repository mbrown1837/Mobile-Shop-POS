-- Database Views for Mobile Shop POS System
-- Run this SQL script to create the required database views

-- Drop views if they exist
DROP VIEW IF EXISTS inventory_available;
DROP VIEW IF EXISTS profit_report;

-- Create inventory_available view
-- This view shows items with their available quantity
CREATE VIEW inventory_available AS
SELECT 
    items.id,
    items.name,
    items.code,
    items.quantity,
    items.unitPrice,
    items.description,
    items.dateAdded
FROM items
WHERE items.quantity > 0;

-- Create profit_report view
-- This view calculates profit from sales
CREATE VIEW profit_report AS
SELECT 
    sales.id AS sale_id,
    sales.dateAdded AS sale_date,
    sales.itemCode,
    items.name AS item_name,
    sales.quantity AS quantity_sold,
    sales.unitPrice AS selling_price,
    items.unitPrice AS cost_price,
    (sales.unitPrice - items.unitPrice) AS profit_per_unit,
    ((sales.unitPrice - items.unitPrice) * sales.quantity) AS total_profit
FROM sales
INNER JOIN items ON sales.itemCode = items.code;
