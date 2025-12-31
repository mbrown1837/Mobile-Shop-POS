-- =====================================================
-- RUN THIS SQL IN PHPMYADMIN TO FIX LOGIN
-- =====================================================

-- Step 1: Add missing columns to admin table
ALTER TABLE `admin` 
ADD COLUMN `account_status` TINYINT(1) DEFAULT 1 AFTER `status`,
ADD COLUMN `deleted` TINYINT(1) DEFAULT 0 AFTER `account_status`,
ADD COLUMN `last_seen` DATETIME DEFAULT NULL AFTER `deleted`,
ADD COLUMN `last_login` DATETIME DEFAULT NULL AFTER `last_seen`;

-- Step 2: Update admin password with bcrypt hash (password: admin123)
UPDATE `admin` SET 
    `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    `account_status` = 1,
    `deleted` = 0
WHERE `email` = 'admin@shop.com';

-- Verify the update
SELECT id, email, password, account_status, deleted FROM admin;
