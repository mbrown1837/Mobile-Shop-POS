-- Add cost price column to items table for profit tracking
-- Run this SQL in your database

ALTER TABLE `items` 
ADD COLUMN `cost_price` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Purchase/Cost price' AFTER `unitPrice`;

-- Update existing items to have cost_price = 0 if not set
UPDATE `items` SET `cost_price` = 0.00 WHERE `cost_price` IS NULL;
