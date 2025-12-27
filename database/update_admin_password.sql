-- Update admin password to: admin123
-- Run this in phpMyAdmin if you already imported the old database

USE mobile_shop_pos;

UPDATE admin 
SET password = '$2y$12$200Hzfz8E0pcLGPAQEDW5OjAAcTA60kBBsIu4ChUIeNtU/er0J0uu' 
WHERE email = 'admin@shop.com';

-- Verify the update
SELECT id, email, first_name, last_name, account_status, deleted 
FROM admin 
WHERE email = 'admin@shop.com';
