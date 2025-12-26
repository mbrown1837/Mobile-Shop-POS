-- ============================================
-- Mobile Shop POS - Test Data (Part 1)
-- Complete sample data for testing
-- ============================================

-- Clear existing test data
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE customer_ledger;
TRUNCATE TABLE trade_ins;
TRUNCATE TABLE transactions;
TRUNCATE TABLE item_serials;
TRUNCATE TABLE items;
TRUNCATE TABLE customers;
TRUNCATE TABLE admin;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- 1. ADMIN USERS (Staff)
-- ============================================
INSERT INTO `admin` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `account_status`, `deleted`) VALUES
(1, 'Admin', 'User', 'admin@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super', 1, 0),
(2, 'Ali', 'Ahmed', 'ali@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manager', 1, 0),
(3, 'Sara', 'Khan', 'sara@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff', 1, 0);

-- ============================================
-- 2. CUSTOMERS (with credit limits)
-- ============================================
INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `cnic`, `address`, `current_balance`, `credit_limit`, `total_purchases`, `total_payments`, `status`) VALUES
(1, 'Ahmed Hassan', '0300-1234567', 'ahmed@example.com', '42101-1234567-1', 'Shop #12, Main Market, Karachi', 15000.00, 50000.00, 85000.00, 70000.00, 'active'),
(2, 'Fatima Ali', '0321-9876543', 'fatima@example.com', '42201-9876543-2', 'House #45, Gulshan-e-Iqbal, Karachi', 0.00, 30000.00, 45000.00, 45000.00, 'active'),
(3, 'Hassan Raza', '0333-5555555', 'hassan@example.com', '42301-5555555-3', 'Flat #3, Clifton, Karachi', 25000.00, 100000.00, 125000.00, 100000.00, 'active'),
(4, 'Ayesha Khan', '0345-7777777', 'ayesha@example.com', '42401-7777777-4', 'Block A, North Nazimabad', 0.00, 25000.00, 0.00, 0.00, 'active'),
(5, 'Bilal Ahmed', '0312-8888888', 'bilal@example.com', '42501-8888888-5', 'Saddar, Karachi', 5000.00, 20000.00, 35000.00, 30000.00, 'active');

-- Password for all users: admin123
