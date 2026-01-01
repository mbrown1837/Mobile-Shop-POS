-- Simplify Customer System for v1.1.0
-- Remove credit limits - Allow unlimited khata for all customers

-- Step 1: Remove credit_enabled column (all customers can have khata)
ALTER TABLE `customers` 
DROP COLUMN IF EXISTS `credit_enabled`;

-- Step 2: Remove credit_limit column (unlimited khata)
ALTER TABLE `customers` 
DROP COLUMN IF EXISTS `credit_limit`;

-- Step 3: Add simple notes column for customer info
ALTER TABLE `customers` 
ADD COLUMN IF NOT EXISTS `notes` TEXT DEFAULT NULL COMMENT 'Additional customer notes' AFTER `cnic`;

-- Step 4: Verify changes
DESCRIBE customers;

-- Step 5: Show sample data
SELECT id, name, phone, cnic, balance, status 
FROM customers 
LIMIT 5;
