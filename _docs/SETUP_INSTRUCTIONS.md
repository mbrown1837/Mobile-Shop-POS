# Mobile Shop POS System - Setup Instructions

## Critical Fixes Applied

This document outlines all the fixes that have been applied to restore your Mobile Shop POS system.

## 1. Database Views (REQUIRED)

The system requires two database views to function properly. Run the SQL script:

```bash
mysql -u your_username -p your_database < create_database_views.sql
```

Or manually execute the SQL in `create_database_views.sql` file in your database.

## 2. Currency System

### Files Created:
- `application/config/currency.php` - Currency configuration (Pakistani Rupees)
- `application/helpers/currency_helper.php` - Currency formatting functions

### Files Modified:
- `application/config/autoload.php` - Added 'currency' to autoloaded helpers

### Usage:
```php
// Get currency symbol
echo currency_symbol(); // Output: Rs.

// Format amount
echo format_currency(1500); // Output: Rs. 1,500.00
```

## 3. JavaScript Configuration

### Files Modified:
- `application/views/main.php` - Added baseUrl and appRoot JavaScript variables

This fixes AJAX requests and form submissions that were stuck on "Processing...".

## 4. Configuration Files

### Base URL Configuration:
The `application/config/config.php` is already configured to auto-detect localhost and production URLs.

### .htaccess Configuration:
The `.htaccess` file is configured for URL rewriting with base path `/mini-inventory-and-sales-management-system`.

## 5. PHP Extensions Required

Make sure these PHP extensions are enabled in your Apache PHP configuration:
- mysqli
- mbstring
- json

Check your `php.ini` file (usually in `C:\xampp\php\php.ini` or similar) and ensure these lines are uncommented:
```ini
extension=mysqli
extension=mbstring
extension=json
```

## 6. Database Connection

Update your database credentials in:
- `application/config/database.php`

## Quick Start Checklist

1. ✅ Import database views: `mysql -u root -p your_db < create_database_views.sql`
2. ✅ Verify PHP extensions are enabled (mysqli, mbstring, json)
3. ✅ Update database credentials in `application/config/database.php`
4. ✅ Ensure Apache mod_rewrite is enabled
5. ✅ Set proper file permissions on `application/cache` and `system/sessions` folders
6. ✅ Access the application at: `http://localhost/mini-inventory-and-sales-management-system/`

## Common Issues

### Items Page Not Loading
- **Cause**: Missing database views
- **Fix**: Run `create_database_views.sql`

### Currency Showing Wrong Symbol
- **Cause**: Missing currency configuration
- **Fix**: Already fixed - currency helper and config files created

### Forms Stuck on "Processing..."
- **Cause**: Missing JavaScript baseUrl variable
- **Fix**: Already fixed - added to main.php

### 500 Internal Server Error
- **Cause**: Database connection or missing views
- **Fix**: Check database credentials and run SQL script

## Support

If you encounter any issues, check:
1. Apache error logs
2. PHP error logs
3. Browser console for JavaScript errors
4. Database connection settings
