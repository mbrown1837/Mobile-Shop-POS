# Login Issue - Quick Fix

## Problem
Invalid email/password combination error

## Cause
Password hash in database is incorrect or using old format

## ✅ Solution

### Step 1: Fix Password
```bash
cd C:\xampp\htdocs\mobile-shop-pos
C:\xampp\mysql\bin\mysql.exe -u root -p mobile_shop_pos < database/fix_admin_password.sql
```

### Step 2: Login
- **URL:** `http://localhost/mobile-shop-pos/`
- **Email:** `admin@shop.com`
- **Password:** `admin123`

---

## Alternative: Manual Fix

### Option 1: Via phpMyAdmin
1. Open: `http://localhost/phpmyadmin`
2. Select `mobile_shop_pos` database
3. Click on `admin` table
4. Find row with email `admin@shop.com`
5. Click "Edit"
6. Change `password` field to:
   ```
   $2y$12$D6JwgjQ62NbcI7Ui/RUgkeWQK3kAOUiauF4ob3hr3x4M.E38FHBuW
   ```
7. Click "Go"

### Option 2: Via SQL Query
```sql
UPDATE admin 
SET password = '$2y$12$D6JwgjQ62NbcI7Ui/RUgkeWQK3kAOUiauF4ob3hr3x4M.E38FHBuW' 
WHERE email = 'admin@shop.com';
```

---

## Verify Password Updated

```bash
C:\xampp\mysql\bin\mysql.exe -u root -p mobile_shop_pos -e "SELECT email, LEFT(password, 20) as password_hash FROM admin WHERE email = 'admin@shop.com';"
```

Should show:
```
email               password_hash
admin@shop.com      $2y$12$D6JwgjQ62NbcI
```

---

## Test Login

1. Open: `http://localhost/mobile-shop-pos/`
2. Enter:
   - Email: `admin@shop.com`
   - Password: `admin123`
3. Click "Log in!"
4. Should redirect to Dashboard

---

## If Still Not Working

### Check 1: Verify Email Exists
```sql
SELECT id, email, role FROM admin WHERE email = 'admin@shop.com';
```

### Check 2: Check Password Hash
```sql
SELECT email, password FROM admin WHERE email = 'admin@shop.com';
```

Should start with `$2y$12$`

### Check 3: Clear Browser Cache
- Press `Ctrl + Shift + Delete`
- Clear cookies and cache
- Try again

### Check 4: Check Session
```sql
SELECT * FROM admin WHERE email = 'admin@shop.com' AND account_status = 1 AND deleted = 0;
```

---

## Create New Admin (If Needed)

```sql
INSERT INTO admin (first_name, last_name, email, password, role, account_status, deleted) 
VALUES ('Admin', 'User', 'admin@shop.com', '$2y$12$D6JwgjQ62NbcI7Ui/RUgkeWQK3kAOUiauF4ob3hr3x4M.E38FHBuW', 'Super', 1, 0);
```

---

## Password Hash Info

**Password:** `admin123`  
**Hash:** `$2y$12$D6JwgjQ62NbcI7Ui/RUgkeWQK3kAOUiauF4ob3hr3x4M.E38FHBuW`  
**Algorithm:** bcrypt (PHP password_hash)  
**Cost:** 12

---

## Quick Fix Command

```bash
C:\xampp\mysql\bin\mysql.exe -u root -p mobile_shop_pos < database/fix_admin_password.sql
```

Then refresh browser and login!

---

**Status:** ✅ Password hash updated  
**Login:** admin@shop.com / admin123
