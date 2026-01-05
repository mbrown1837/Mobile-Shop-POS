# 🚀 Automated Installer Guide

## ✨ What is This?

The automated installer (`install.php`) is a **web-based installation wizard** that automatically sets up your Mobile Shop POS in just a few clicks!

No manual configuration needed - just follow the on-screen steps.

---

## 🎯 Benefits

### Traditional Installation:
- ❌ Manual database creation
- ❌ Manual SQL import
- ❌ Manual config file editing
- ❌ Multiple steps
- ❌ 10-15 minutes

### Automated Installation:
- ✅ Automatic database creation
- ✅ Automatic SQL import
- ✅ Automatic configuration
- ✅ Visual wizard
- ✅ 2-3 minutes!

---

## 📋 Prerequisites

Before using the installer:

1. **XAMPP/WAMP Running**:
   - Apache started
   - MySQL started

2. **Files Extracted**:
   - Extract ZIP to web directory
   - Example: `C:\xampp\htdocs\mobile-shop-pos\`

3. **Database File Present**:
   - File: `database/mobile_shop_pos_v1.1.0_final.sql`
   - Should be included in package

---

## 🚀 Installation Steps

### Step 1: Access Installer

Open your browser and go to:
```
http://localhost/mobile-shop-pos/install.php
```

You'll see a beautiful installation wizard!

### Step 2: System Requirements Check

The installer automatically checks:
- ✅ PHP version (7.4+)
- ✅ Required extensions (mysqli, json, mbstring)
- ✅ File permissions (config, cache, logs)
- ✅ Database file exists

If all checks pass, click **"Next: Database Setup"**

### Step 3: Database Configuration

Enter your database details:

**Field** | **Default Value** | **Description**
----------|-------------------|----------------
Database Host | `localhost` | Usually localhost
Database Username | `root` | Your MySQL username
Database Password | _(empty)_ | Leave empty if no password
Database Name | `mobile_shop_pos` | Will be created automatically

Click **"Test Connection"** to verify.

If successful, click **"Next: Import Database"**

### Step 4: Import Database

The installer shows:
- Database file path
- File size
- Ready to import

Click **"Import Database"**

The installer will:
- Read SQL file
- Execute all queries
- Create tables
- Insert initial data
- Create admin user

Click **"Next: Configure"** when done.

### Step 5: Application Configuration

Configure your app:

**Base URL**:
```
http://localhost/mobile-shop-pos/
```
⚠️ Must end with `/`

**Admin Password** (Optional):
- Leave empty to use default: `admin123`
- Or set your own secure password

Click **"Configure & Complete Installation"**

### Step 6: Installation Complete! 🎉

You'll see success message with:
- ✅ Installation successful
- 🔐 Login credentials
- 🔗 Link to application

Click **"Go to Application"** to start using!

---

## 🔐 Default Login

After installation:

**URL**: `http://localhost/mobile-shop-pos/`

**Credentials**:
- Username: `admin`
- Password: `admin123` (or your custom password)

⚠️ **Change password after first login!**

---

## 🛡️ Security

### After Installation:

1. **Delete installer**:
   ```
   Delete: install.php
   ```
   Or rename it to prevent unauthorized access

2. **Change admin password**:
   - Login to application
   - Go to Settings
   - Change password

3. **Secure config files**:
   - Files are already protected by `.htaccess`
   - But verify permissions

---

## 🔧 What Gets Configured

The installer automatically configures:

### 1. Database Connection
File: `application/config/database.php`
```php
'hostname' => 'localhost',
'username' => 'root',
'password' => '',
'database' => 'mobile_shop_pos',
```

### 2. Base URL
File: `application/config/config.php`
```php
$config['base_url'] = 'http://localhost/mobile-shop-pos/';
```

### 3. Database Schema
- Creates all tables
- Sets up relationships
- Creates views
- Inserts admin user

### 4. Installation Flag
File: `application/config/installed.txt`
- Prevents re-installation
- Contains installation timestamp

---

## ❓ Troubleshooting

### "Application is already installed"

**Solution**:
- Delete `application/config/installed.txt`
- Or add `?reinstall` to URL:
  ```
  http://localhost/mobile-shop-pos/install.php?reinstall
  ```

### "Database connection failed"

**Check**:
- Is MySQL running?
- Username/password correct?
- Try connecting via phpMyAdmin first

**Solution**:
- Verify credentials
- Check MySQL service status
- Try default: root / (empty password)

### "Cannot create database"

**Solution**:
- Create database manually in phpMyAdmin
- Name it: `mobile_shop_pos`
- Then run installer again

### "Permission denied" errors

**Solution**:
- Check folder permissions
- Make writable:
  ```
  application/config/
  application/cache/
  application/logs/
  ```

### "Database import failed"

**Check**:
- Database file exists: `database/mobile_shop_pos_v1.1.0_final.sql`
- File is not corrupted
- Database is empty (no conflicting tables)

**Solution**:
- Try manual import via phpMyAdmin
- Or delete existing tables and retry

### "Blank page" after installation

**Solution**:
- Check if `install.php` was deleted
- Access application directly:
  ```
  http://localhost/mobile-shop-pos/
  ```

---

## 🔄 Reinstallation

To reinstall:

1. **Delete installation flag**:
   ```
   Delete: application/config/installed.txt
   ```

2. **Drop database** (optional):
   ```sql
   DROP DATABASE mobile_shop_pos;
   ```

3. **Run installer again**:
   ```
   http://localhost/mobile-shop-pos/install.php
   ```

---

## 📊 Installation Process

```
┌─────────────────────────────────────┐
│  1. System Requirements Check       │
│     ✓ PHP version                   │
│     ✓ Extensions                    │
│     ✓ Permissions                   │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  2. Database Configuration          │
│     • Enter credentials             │
│     • Test connection               │
│     • Create database               │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  3. Import Database                 │
│     • Read SQL file                 │
│     • Execute queries               │
│     • Create tables                 │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  4. Configure Application           │
│     • Set base URL                  │
│     • Update config files           │
│     • Set admin password            │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  5. Installation Complete! 🎉       │
│     • Ready to use                  │
│     • Login and start               │
└─────────────────────────────────────┘
```

---

## 🎯 Comparison: Manual vs Automated

### Manual Installation (Old Way):

1. Open phpMyAdmin
2. Create database
3. Import SQL file
4. Open `database.php` in editor
5. Edit database credentials
6. Save file
7. Open `config.php` in editor
8. Edit base URL
9. Save file
10. Test application

**Time**: 10-15 minutes  
**Difficulty**: Medium  
**Error-prone**: Yes

### Automated Installation (New Way):

1. Open `install.php` in browser
2. Click through wizard
3. Enter database details
4. Click "Install"
5. Done!

**Time**: 2-3 minutes  
**Difficulty**: Easy  
**Error-prone**: No

---

## ✨ Features

### Visual Progress
- Step-by-step wizard
- Progress indicator
- Clear instructions

### Automatic Checks
- System requirements
- File permissions
- Database connectivity

### Error Handling
- Clear error messages
- Helpful suggestions
- Validation

### User-Friendly
- Beautiful interface
- No technical knowledge needed
- Guided process

---

## 📝 For Developers

### Customization

You can customize the installer by editing `install.php`:

**Change default values**:
```php
define('MIN_PHP_VERSION', '7.4.0');
define('DB_FILE', 'database/mobile_shop_pos_v1.1.0_final.sql');
```

**Add more checks**:
```php
$requirements = [
    'Your Check' => your_check_function(),
];
```

**Modify steps**:
- Add/remove steps
- Change validation
- Customize UI

### Session Variables

The installer uses session to store:
- `install_step` - Current step
- `db_host`, `db_user`, `db_pass`, `db_name` - Database config
- `base_url` - Application URL
- `admin_password` - Custom password
- `db_imported` - Import status
- `install_complete` - Installation status

---

## 🎉 Success!

With the automated installer:
- ✅ No manual configuration
- ✅ No file editing
- ✅ No command line
- ✅ Just click and go!

**Perfect for non-technical users!** 🚀

---

## 📞 Support

If you encounter issues:

1. Check troubleshooting section
2. Verify prerequisites
3. Try manual installation as fallback
4. Check error messages carefully

---

**Installer Version**: 1.0.0  
**Compatible With**: Mobile Shop POS v1.1.0  
**Status**: Ready to use ✅
