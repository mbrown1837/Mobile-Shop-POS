-- ============================================
-- MOBILE SHOP POS - PHASE 1 COMPLETE MIGRATION
-- ============================================
-- Version: 1.0
-- Date: 2024-12-26
-- Description: Complete database transformation for Mobile Shop POS
-- 
-- IMPORTANT: Backup your database before running this!
-- Usage: mysql -u root -p mobile_shop_pos < phase1_mobile_shop_complete.sql
-- ============================================

-- Use the database
USE mobile_shop_pos;

-- ============================================
-- STEP 1: Modify items table for hybrid inventory
-- ============================================
ALTER TABLE items 
ADD COLUMN item_type ENUM('standard', 'serialized') DEFAULT 'standard' COMMENT 'standard=quantity based, serialized=IMEI based' AFTER quantity,
ADD COLUMN warranty_months INT DEFAULT 0 COMMENT 'Warranty period in months' AFTER item_type,
ADD COLUMN warranty_terms VARCHAR(200) NULL COMMENT 'Warranty terms and conditions' AFTER warranty_months,
ADD COLUMN brand VARCHAR(50) NULL COMMENT 'Product brand' AFTER name,
ADD COLUMN model VARCHAR(50) NULL COMMENT 'Product model' AFTER brand,
ADD COLUMN category ENUM('mobile', 'accessory', 'other') DEFAULT 'other' COMMENT 'Product category' AFTER model;

-- Add indexes for better performance
CREATE INDEX idx_item_type ON items(item_type);
CREATE INDEX idx_category ON items(category);
CREATE INDEX idx_brand ON items(brand);

-- ============================================
-- STEP 2: Create item_serials table (CORE TABLE)
-- ============================================
CREATE TABLE IF NOT EXISTS item_serials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to items table',
    imei_number VARCHAR(20) UNIQUE NOT NULL COMMENT '15-digit IMEI number',
    serial_number VARCHAR(50) NULL COMMENT 'Optional serial number',
    color VARCHAR(30) NULL COMMENT 'Device color',
    storage VARCHAR(20) NULL COMMENT 'Storage capacity (e.g., 128GB)',
    cost_price DECIMAL(10,2) NOT NULL COMMENT 'Purchase/cost price',
    selling_price DECIMAL(10,2) NULL COMMENT 'Override selling price (optional)',
    status ENUM('available', 'sold', 'returned', 'traded_in', 'defective') DEFAULT 'available' COMMENT 'Current status',
    sold_transaction_id BIGINT UNSIGNED NULL COMMENT 'Transaction ID when sold',
    sold_date DATETIME NULL COMMENT 'Date when sold',
    purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Date when purchased',
    notes TEXT NULL COMMENT 'Additional notes',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_item_id (item_id),
    INDEX idx_imei (imei_number),
    INDEX idx_status (status),
    INDEX idx_sold_date (sold_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='IMEI tracking for serialized items';

-- ============================================
-- STEP 3: Create customers table (for Khata/Credit)
-- ============================================
CREATE TABLE IF NOT EXISTS customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Customer full name',
    phone VARCHAR(20) UNIQUE NOT NULL COMMENT 'Primary phone number',
    email VARCHAR(100) NULL COMMENT 'Email address',
    cnic VARCHAR(20) NULL COMMENT 'National ID card number',
    address TEXT NULL COMMENT 'Full address',
    current_balance DECIMAL(12,2) SIGNED DEFAULT 0.00 COMMENT 'Current credit balance (positive=owes us, negative=we owe)',
    credit_limit DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Maximum credit allowed',
    total_purchases DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Lifetime purchase amount',
    total_payments DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Lifetime payment amount',
    status ENUM('active', 'blocked') DEFAULT 'active' COMMENT 'Account status',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_phone (phone),
    INDEX idx_status (status),
    INDEX idx_balance (current_balance)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customer information and credit management';

-- ============================================
-- STEP 4: Create customer_ledger table
-- ============================================
CREATE TABLE IF NOT EXISTS customer_ledger (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key to customers table',
    transaction_ref VARCHAR(20) NULL COMMENT 'Links to transactions.ref if sale',
    entry_type ENUM('sale', 'payment', 'adjustment', 'return') NOT NULL COMMENT 'Type of ledger entry',
    amount DECIMAL(10,2) NOT NULL COMMENT 'Transaction amount',
    balance_before DECIMAL(12,2) NOT NULL COMMENT 'Balance before this entry',
    balance_after DECIMAL(12,2) NOT NULL COMMENT 'Balance after this entry',
    payment_method VARCHAR(20) NULL COMMENT 'Payment method if payment entry',
    notes TEXT NULL COMMENT 'Additional notes',
    staff_id INT NOT NULL COMMENT 'Staff who made the entry',
    entry_date DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Entry timestamp',
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_transaction_ref (transaction_ref),
    INDEX idx_entry_date (entry_date),
    INDEX idx_entry_type (entry_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customer credit ledger entries';

-- ============================================
-- STEP 5: Modify transactions table
-- ============================================
ALTER TABLE transactions 
ADD COLUMN customer_id BIGINT UNSIGNED NULL COMMENT 'Foreign key to customers table' AFTER cust_email,
ADD COLUMN payment_status ENUM('paid', 'partial', 'credit') DEFAULT 'paid' COMMENT 'Payment status' AFTER modeOfPayment,
ADD COLUMN paid_amount DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Amount actually paid' AFTER payment_status,
ADD COLUMN credit_amount DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Amount on credit' AFTER paid_amount,
ADD COLUMN profit_amount DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Calculated profit for this transaction' AFTER credit_amount,
ADD COLUMN imei_numbers TEXT NULL COMMENT 'Comma-separated IMEI numbers sold in this transaction' AFTER profit_amount;

-- Add indexes
CREATE INDEX idx_customer_id ON transactions(customer_id);
CREATE INDEX idx_payment_status ON transactions(payment_status);
CREATE INDEX idx_trans_date ON transactions(transDate);

-- ============================================
-- STEP 6: Create trade_ins table
-- ============================================
CREATE TABLE IF NOT EXISTS trade_ins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_ref VARCHAR(20) NOT NULL COMMENT 'Links to sale transaction',
    customer_id BIGINT UNSIGNED NULL COMMENT 'Customer who traded in',
    imei_number VARCHAR(20) NULL COMMENT 'IMEI of traded device (if available)',
    brand VARCHAR(50) NOT NULL COMMENT 'Brand of traded device',
    model VARCHAR(50) NOT NULL COMMENT 'Model of traded device',
    condition_rating ENUM('excellent', 'good', 'fair', 'poor') NOT NULL COMMENT 'Physical condition',
    trade_in_value DECIMAL(10,2) NOT NULL COMMENT 'Value given for trade-in',
    notes TEXT NULL COMMENT 'Additional notes about condition',
    staff_id INT NOT NULL COMMENT 'Staff who processed trade-in',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_transaction_ref (transaction_ref),
    INDEX idx_imei (imei_number),
    INDEX idx_customer_id (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Trade-in transaction records';

-- ============================================
-- STEP 7: Create inventory_available VIEW
-- ============================================
CREATE OR REPLACE VIEW inventory_available AS
SELECT 
    i.id,
    i.name,
    i.code,
    i.brand,
    i.model,
    i.category,
    i.item_type,
    i.unitPrice,
    i.warranty_months,
    i.warranty_terms,
    i.description,
    CASE 
        WHEN i.item_type = 'serialized' THEN COALESCE(COUNT(s.id), 0)
        ELSE i.quantity
    END as available_qty,
    CASE 
        WHEN i.item_type = 'serialized' THEN COALESCE(SUM(s.cost_price), 0)
        ELSE i.quantity * i.unitPrice
    END as total_inventory_value,
    i.dateAdded,
    i.lastUpdated
FROM items i
LEFT JOIN item_serials s ON i.id = s.item_id AND s.status = 'available'
GROUP BY i.id;

-- ============================================
-- STEP 8: Create profit_report VIEW
-- ============================================
CREATE OR REPLACE VIEW profit_report AS
SELECT 
    t.ref,
    t.transDate,
    DATE(t.transDate) as sale_date,
    t.totalMoneySpent as sale_amount,
    t.profit_amount,
    t.discount_amount,
    t.vatAmount,
    t.payment_status,
    t.modeOfPayment,
    t.staffId,
    CONCAT(a.first_name, ' ', a.last_name) as staff_name,
    t.customer_id,
    c.name as customer_name,
    COUNT(DISTINCT t.transId) as items_count
FROM transactions t
LEFT JOIN admin a ON t.staffId = a.id
LEFT JOIN customers c ON t.customer_id = c.id
WHERE t.cancelled = '0'
GROUP BY t.ref
ORDER BY t.transDate DESC;

-- ============================================
-- STEP 9: Create daily_sales_summary VIEW
-- ============================================
CREATE OR REPLACE VIEW daily_sales_summary AS
SELECT 
    DATE(transDate) as sale_date,
    COUNT(DISTINCT ref) as total_transactions,
    SUM(totalMoneySpent) as total_sales,
    SUM(profit_amount) as total_profit,
    SUM(CASE WHEN payment_status = 'paid' THEN totalMoneySpent ELSE 0 END) as cash_sales,
    SUM(CASE WHEN payment_status = 'credit' THEN totalMoneySpent ELSE 0 END) as credit_sales,
    SUM(CASE WHEN payment_status = 'partial' THEN credit_amount ELSE 0 END) as partial_credit
FROM transactions
WHERE cancelled = '0'
GROUP BY DATE(transDate)
ORDER BY sale_date DESC;

-- ============================================
-- STEP 10: Insert sample data (OPTIONAL - for testing)
-- ============================================
-- Uncomment below to add sample data

/*
-- Sample mobile phone (serialized item)
INSERT INTO items (name, code, brand, model, category, item_type, unitPrice, quantity, warranty_months, warranty_terms, description, dateAdded) 
VALUES 
('iPhone 13 Pro', 'IP13PRO', 'Apple', 'iPhone 13 Pro', 'mobile', 'serialized', 250000.00, 0, 12, 'Official Apple warranty', '128GB, Multiple Colors', NOW()),
('Samsung Galaxy S21', 'SAMS21', 'Samsung', 'Galaxy S21', 'mobile', 'serialized', 180000.00, 0, 12, 'Samsung official warranty', '256GB, Multiple Colors', NOW());

-- Sample IMEIs for iPhone
INSERT INTO item_serials (item_id, imei_number, color, storage, cost_price, status, purchase_date) 
VALUES 
(1, '123456789012345', 'Graphite', '128GB', 230000.00, 'available', NOW()),
(1, '123456789012346', 'Gold', '128GB', 230000.00, 'available', NOW()),
(1, '123456789012347', 'Silver', '256GB', 245000.00, 'available', NOW());

-- Sample accessories (standard items)
INSERT INTO items (name, code, brand, model, category, item_type, unitPrice, quantity, warranty_months, description, dateAdded) 
VALUES 
('Samsung 25W Charger', 'SAMCHG25', 'Samsung', '25W Fast Charger', 'accessory', 'standard', 1500.00, 50, 6, 'Type-C Fast Charging', NOW()),
('iPhone Case', 'IPCASE', 'Apple', 'Silicone Case', 'accessory', 'standard', 3500.00, 30, 0, 'Original Apple Silicone Case', NOW()),
('Screen Protector', 'SCRPROT', 'Generic', 'Tempered Glass', 'accessory', 'standard', 500.00, 100, 0, '9H Hardness Tempered Glass', NOW());

-- Sample customer
INSERT INTO customers (name, phone, email, cnic, address, credit_limit, status, created_at) 
VALUES 
('Ahmed Khan', '03001234567', 'ahmed@example.com', '12345-1234567-1', 'House #123, Block A, Karachi', 50000.00, 'active', NOW()),
('Fatima Ali', '03009876543', 'fatima@example.com', '12345-7654321-2', 'Flat #45, Gulshan, Karachi', 30000.00, 'active', NOW());
*/

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- Run these after migration to verify everything worked

-- Check all tables exist
-- SHOW TABLES;

-- Verify items table structure
-- DESCRIBE items;

-- Verify item_serials table
-- DESCRIBE item_serials;

-- Verify customers table
-- DESCRIBE customers;

-- Verify customer_ledger table
-- DESCRIBE customer_ledger;

-- Verify trade_ins table
-- DESCRIBE trade_ins;

-- Verify transactions modifications
-- DESCRIBE transactions;

-- Check views work
-- SELECT * FROM inventory_available LIMIT 5;
-- SELECT * FROM profit_report LIMIT 5;
-- SELECT * FROM daily_sales_summary LIMIT 5;

-- Check sample data (if inserted)
-- SELECT * FROM items;
-- SELECT * FROM item_serials;
-- SELECT * FROM customers;

-- ============================================
-- ROLLBACK SCRIPT (if needed - BE VERY CAREFUL!)
-- ============================================
/*
-- To rollback this migration, run these commands:

DROP VIEW IF EXISTS daily_sales_summary;
DROP VIEW IF EXISTS profit_report;
DROP VIEW IF EXISTS inventory_available;
DROP TABLE IF EXISTS trade_ins;
DROP TABLE IF EXISTS customer_ledger;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS item_serials;

ALTER TABLE transactions 
DROP COLUMN imei_numbers,
DROP COLUMN profit_amount,
DROP COLUMN credit_amount,
DROP COLUMN paid_amount,
DROP COLUMN payment_status,
DROP COLUMN customer_id;

ALTER TABLE items 
DROP COLUMN category,
DROP COLUMN model,
DROP COLUMN brand,
DROP COLUMN warranty_terms,
DROP COLUMN warranty_months,
DROP COLUMN item_type;

DROP INDEX idx_trans_date ON transactions;
DROP INDEX idx_payment_status ON transactions;
DROP INDEX idx_customer_id ON transactions;
DROP INDEX idx_brand ON items;
DROP INDEX idx_category ON items;
DROP INDEX idx_item_type ON items;
*/

-- ============================================
-- MIGRATION COMPLETE
-- ============================================
-- Phase 1 database transformation is complete!
-- Next: Proceed to Phase 2 - Inventory Management Module
-- ============================================
