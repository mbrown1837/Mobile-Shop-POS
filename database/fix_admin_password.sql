-- Fix Admin Password
-- Password: admin123
-- Hash generated: $2y$12$D6JwgjQ62NbcI7Ui/RUgkeWQK3kAOUiauF4ob3hr3x4M.E38FHBuW

UPDATE `admin` SET `password` = '$2y$12$D6JwgjQ62NbcI7Ui/RUgkeWQK3kAOUiauF4ob3hr3x4M.E38FHBuW' WHERE `email` = 'admin@shop.com';

-- Verify
SELECT id, first_name, last_name, email, 'Password Updated' as status FROM admin WHERE email = 'admin@shop.com';
