-- Add missing columns to customer_ledger table
-- These columns are needed for proper ledger display

ALTER TABLE `customer_ledger` 
ADD COLUMN `transaction_ref` VARCHAR(50) DEFAULT NULL COMMENT 'Reference to transaction' AFTER `transaction_id`,
ADD COLUMN `balance_after` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Customer balance after this entry' AFTER `amount`;

-- Add index for transaction_ref
ALTER TABLE `customer_ledger` 
ADD INDEX `idx_transaction_ref` (`transaction_ref`);

-- Verify the changes
DESCRIBE customer_ledger;

-- Update existing records to calculate balance_after
-- This will need to be done in order by created_at
SET @balance = 0;
UPDATE customer_ledger 
SET balance_after = (@balance := @balance + 
    CASE 
        WHEN transaction_type IN ('credit_sale', 'adjustment') THEN amount 
        WHEN transaction_type IN ('payment', 'return') THEN -amount 
        ELSE 0 
    END)
ORDER BY customer_id, created_at;
