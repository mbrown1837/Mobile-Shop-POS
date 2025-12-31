-- Add credit_enabled column to customers table
-- This allows selective credit/khata for trusted customers only

ALTER TABLE `customers` 
ADD COLUMN `credit_enabled` TINYINT(1) DEFAULT 0 COMMENT 'Is credit/khata allowed for this customer' AFTER `credit_limit`;

-- Update existing customers with credit_limit > 0 to have credit enabled
UPDATE `customers` 
SET `credit_enabled` = 1 
WHERE `credit_limit` > 0;

-- Verify the changes
SELECT id, name, phone, status, credit_enabled, credit_limit, balance 
FROM customers 
LIMIT 10;
