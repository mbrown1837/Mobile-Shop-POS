-- Add missing columns to transactions table
-- Run this in phpMyAdmin

ALTER TABLE `transactions` 
ADD COLUMN IF NOT EXISTS `imei_numbers` TEXT DEFAULT NULL COMMENT 'Comma-separated IMEIs' AFTER `cust_email`,
ADD COLUMN IF NOT EXISTS `profit_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `imei_numbers`,
ADD COLUMN IF NOT EXISTS `payment_status` ENUM('paid','partial','credit') DEFAULT 'paid' AFTER `modeOfPayment`,
ADD COLUMN IF NOT EXISTS `paid_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `payment_status`,
ADD COLUMN IF NOT EXISTS `credit_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `paid_amount`,
ADD COLUMN IF NOT EXISTS `customer_id` BIGINT(20) UNSIGNED DEFAULT NULL AFTER `cust_email`,
ADD COLUMN IF NOT EXISTS `trade_in_value` DECIMAL(10,2) DEFAULT 0.00 AFTER `customer_id`;

-- Add foreign key for customer_id if not exists
-- ALTER TABLE `transactions` ADD CONSTRAINT `fk_transactions_customer` 
-- FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

SELECT 'Missing columns added successfully!' as message;
