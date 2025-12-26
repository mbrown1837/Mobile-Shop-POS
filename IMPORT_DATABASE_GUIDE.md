# Database Import Guide

## Quick Setup Steps

### 1. Drop Old Database (if exists)
In phpMyAdmin:
```sql
DROP DATABASE IF EXISTS `mobile_shop_pos`;
CREATE DATABASE `mobile_shop_pos` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Import the Fixed SQL File
1. Open phpMyAdmin
2. Select the `mobile_shop_pos` database
3. Click "Import" tab
4. Choose file: `mobile_shop_pos_fixed.sql`
5. Click "Go"

### 3. Login Credentials
- **Email**: admin@shop.com
- **Password**: admin123

### 4. Access the Application
- **URL**: http://localhost/mobile-shop-pos/

## What Was Fixed

### Admin Table
✅ Added `mobile1` and `mobile2` columns for phone numbers
✅ Added `created_on` column (datetime)
✅ Added `last_login` column (datetime)
✅ Added `account_status` column (replaces `status`)
✅ Added `deleted` column for soft deletes
✅ Fixed password to use bcrypt hash (was MD5)

### Login Page
✅ Removed "1410Inventory" branding
✅ Changed to "Mobile Shop POS" title
✅ Removed Amir Sanni footer
✅ Fixed base_url to include trailing slash

### File Paths
✅ All CSS/JS files are in correct locations
✅ Bootstrap, Font Awesome, jQuery all present
✅ Base URL configured for localhost

## Test Data Included
- 1 Admin user (Super role)
- 3 Customers
- 6 Items (4 accessories, 2 mobiles)
- 3 Serialized phones with IMEI numbers

## Troubleshooting

### If CSS/JS still not loading:
Check your folder name matches the base_url:
- Folder should be: `C:\xampp\htdocs\mobile-shop-pos\`
- URL should be: `http://localhost/mobile-shop-pos/`

If your folder name is different, update `application/config/config.php` line 36:
```php
$config['base_url'] =  $protocol . $host . "/YOUR-FOLDER-NAME/";
```

### If login fails:
Make sure you imported `mobile_shop_pos_fixed.sql` (not the old one)
The password hash must be bcrypt format, not MD5.

## Next Steps
After successful login:
1. Change your password immediately
2. Add more admin users if needed
3. Configure your shop settings
4. Start adding inventory
