# Test & Debug Files

This folder contains test files and debugging scripts used during development.

## 🧪 Test Files

### HTML Test Pages
- `test_ajax.html` - AJAX functionality testing
- `test_approot.html` - Base URL testing
- `test_cache.html` - Cache testing
- `test_customers_ui.html` - Customer UI testing
- `test_login.html` - Login page testing
- `test_login_debug.html` - Login debugging
- `test_search.html` - Search functionality testing

### PHP Test Scripts
- `test_ajax.php` - AJAX endpoint testing
- `test_password.php` - Password hashing testing
- `test_transaction.php` - Transaction testing

### Debug Scripts
- `check_admin.php` - Admin account verification
- `debug_transaction.php` - Transaction debugging
- `diagnostic_check.php` - System diagnostics
- `generate_password.php` - Password hash generator

## ⚠️ Important Notes

1. **These files are NOT needed for production**
2. **Do NOT delete** - useful for debugging
3. **Security**: These files should not be accessible in production
4. **Usage**: Only for development and troubleshooting

## 🔒 Security Recommendation

In production, either:
- Delete this folder entirely, OR
- Add `.htaccess` to block access:

```apache
Order Deny,Allow
Deny from all
```

## 🛠️ How to Use

### Generate Password Hash
```
http://localhost/mobile-shop-pos/_test_files/generate_password.php
```

### Check Admin Account
```
http://localhost/mobile-shop-pos/_test_files/check_admin.php
```

### Run Diagnostics
```
http://localhost/mobile-shop-pos/_test_files/diagnostic_check.php
```

### Test AJAX
```
http://localhost/mobile-shop-pos/_test_files/test_ajax.html
```

## 📝 Notes

These files were created during development to test various features and debug issues. They are kept for reference and future debugging needs.
