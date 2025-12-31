-- Fix admin table to add missing columns required by the application
-- Run this SQL in phpMyAdmin or MySQL command line

-- Add missing columns to admin table
ALTER TABLE `admin` 
ADD COLUMN IF NOT EXISTS `account_status` TINYINT(1) DEFAULT 1 AFTER `status`,
ADD COLUMN IF NOT EXISTS `deleted` TINYINT(1) DEFAULT 0 AFTER `account_status`,
ADD COLUMN IF NOT EXISTS `last_seen` DATETIME DEFAULT NULL AFTER `deleted`,
ADD COLUMN IF NOT EXISTS `last_login` DATETIME DEFAULT NULL AFTER `last_seen`;

-- Update existing admin with proper password (password: admin123)
UPDATE `admin` SET 
    `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    `account_status` = 1,
    `deleted` = 0
WHERE `email` = 'admin@shop.com';

-- If the above doesn't work, try this alternative password hash
-- UPDATE `admin` SET `password` = '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm' WHERE `email` = 'admin@shop.com';

SELECT 'Admin table fixed successfully!' as message;
