# 🚀 Quick Setup Guide

Get your Mobile Shop POS running in minutes!

## 🎯 Choose Your Installation Method

We offer **THREE** installation methods - from fully automated to manual:

### ⚡ Method 1: One-Click Installer (EASIEST - 2 Minutes!)

**Perfect if you have XAMPP installed**

1. Right-click `ONE_CLICK_INSTALLER.bat` → Run as Administrator
2. Wait 2-3 minutes
3. Browser opens automatically
4. Done! 🎉

**See**: [ONE_CLICK_INSTALLATION_GUIDE.md](ONE_CLICK_INSTALLATION_GUIDE.md)

---

### 🚀 Method 2: Full Auto Installer (COMPLETE - 10-20 Minutes)

**Perfect for fresh PC - installs XAMPP too!**

1. Right-click `FULL_AUTO_INSTALLER.ps1` → Run with PowerShell
2. Type `Y` to download XAMPP
3. Wait for automatic installation
4. Browser opens automatically
5. Done! 🎉

**See**: [ONE_CLICK_INSTALLATION_GUIDE.md](ONE_CLICK_INSTALLATION_GUIDE.md)

---

### 🌐 Method 3: Web-Based Installer (VISUAL - 2 Minutes)

**Perfect if you prefer GUI wizard**

1. Extract files to htdocs
2. Open browser: `http://localhost/mobile-shop-pos/install.php`
3. Follow the wizard (5 simple steps)
4. Done! 🎉

**See**: [AUTOMATED_INSTALLER_GUIDE.md](AUTOMATED_INSTALLER_GUIDE.md)

---

### 📝 Method 4: Manual Setup (ADVANCED - 5 Minutes)

**For advanced users who prefer manual configuration**

## ✅ Pre-Installation Checklist

Before starting, make sure you have:
- [ ] XAMPP/WAMP installed and running
- [ ] MySQL/Apache services started
- [ ] Project files extracted to web directory

## 📝 Step-by-Step Setup

### Step 1: Create Database (2 minutes)

1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click **"New"** in left sidebar
3. Database name: `mobile_shop_pos`
4. Click **"Create"**
5. Click on `mobile_shop_pos` database
6. Click **"Import"** tab
7. Click **"Choose File"** → Select `database/mobile_shop_pos_v1.1.0_final.sql`
8. Click **"Go"** at bottom
9. Wait for "Import has been successfully finished" message

✅ **Database Created!**

### Step 2: Configure Database Connection (1 minute)

1. Open file: `application/config/database.php`
2. Find these lines (around line 76):
   ```php
   'hostname' => 'localhost',
   'username' => 'root',
   'password' => '',
   'database' => 'mobile_shop_pos',
   ```
3. Update if your MySQL credentials are different
4. Save file

✅ **Database Connected!**

### Step 3: Set Base URL (1 minute)

1. Open file: `application/config/config.php`
2. Find line (around line 26):
   ```php
   $config['base_url'] = '';
   ```
3. Change to:
   ```php
   $config['base_url'] = 'http://localhost/mobile-shop-pos/';
   ```
   ⚠️ **Note**: If your folder name is different, update accordingly
4. Save file

✅ **Base URL Configured!**

### Step 4: Access System (1 minute)

1. Open browser
2. Go to: `http://localhost/mobile-shop-pos/`
3. You should see the login page
4. Login with:
   - **Username**: `admin`
   - **Password**: `admin123`

✅ **System Running!**

### Step 5: Change Password (30 seconds)

1. After login, click **Settings** in sidebar
2. Scroll to "Change Password" section
3. Enter:
   - Current Password: `admin123`
   - New Password: (your secure password)
   - Confirm Password: (same password)
4. Click **"Update Password"**

✅ **Password Secured!**

## 🎉 Setup Complete!

Your Mobile Shop POS is now ready to use!

## 🏁 Next Steps

### 1. Update Shop Settings
- Go to **Settings**
- Update:
  - Shop Name
  - Shop Address
  - Contact Number

### 2. Add Your First Item

**For Mobile Phone**:
1. Go to **Manage Items**
2. Click **"Add New Item"**
3. Select **"Serialized"** type
4. Fill in:
   - Name: e.g., "iPhone 13"
   - Category: Mobile
   - Brand: Apple
   - Model: iPhone 13
   - Price: 120000
5. Add IMEI numbers (click "Add Another IMEI" for dual SIM)
6. Click **"Add Item"**

**For Accessory**:
1. Go to **Manage Items**
2. Click **"Add New Item"**
3. Select **"Standard"** type
4. Fill in:
   - Name: e.g., "Phone Case"
   - Category: Accessory
   - Brand: Generic
   - Quantity: 50
   - Price: 500
5. Click **"Add Item"**

### 3. Add Your First Customer

1. Go to **Customers**
2. Click **"Add Customer"**
3. Fill in:
   - Name: Customer name
   - Phone: Phone number
   - Credit Limit: 50000 (default)
4. Click **"Save"**

### 4. Make Your First Sale

1. Go to **POS** (or click "New Sale" on dashboard)
2. Search for item (type name, code, or IMEI)
3. Click **"Add"** to add to cart
4. Select payment method:
   - **Cash**: Enter amount received
   - **Credit**: Select customer first
5. Click **"Complete Transaction"**
6. View/Print receipt

## ❓ Troubleshooting

### "404 Page Not Found"
**Fix**: Enable mod_rewrite in Apache
- XAMPP: Edit `C:\xampp\apache\conf\httpd.conf`
- Find and uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
- Restart Apache

### "Database Connection Error"
**Fix**: Check these:
1. Is MySQL running? (Check XAMPP Control Panel)
2. Database name correct? Should be `mobile_shop_pos`
3. Username/password correct in `database.php`?

### "Blank White Page"
**Fix**: Check PHP version
- Open XAMPP Control Panel
- Click "Shell"
- Type: `php -v`
- Should be 7.4 or higher

### "Permission Denied" (Linux/Mac)
**Fix**: Set permissions
```bash
chmod -R 755 application/cache
chmod -R 755 application/logs
```

## 📚 Learn More

- **One-Click Installer**: See [ONE_CLICK_INSTALLATION_GUIDE.md](ONE_CLICK_INSTALLATION_GUIDE.md) ⚡ EASIEST
- **Automated Installer**: See [AUTOMATED_INSTALLER_GUIDE.md](AUTOMATED_INSTALLER_GUIDE.md) 🌐 VISUAL
- **Full Installation Guide**: See [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) 📖 DETAILED
- **Features Overview**: See [README.md](README.md)
- **System Verification**: See [SYSTEM_VERIFICATION_CHECKLIST.md](SYSTEM_VERIFICATION_CHECKLIST.md)

## 🎯 Quick Reference

### Default Login
- URL: `http://localhost/mobile-shop-pos/`
- Username: `admin`
- Password: `admin123` (change immediately!)

### Important Files
- Database Config: `application/config/database.php`
- Base URL Config: `application/config/config.php`
- Database File: `database/mobile_shop_pos_v1.1.0_final.sql`

### Key Features
- **POS**: Search items, manage cart, complete sales
- **Inventory**: Add/edit items, track IMEI, manage stock
- **Customers**: Manage customers, track khata, record payments
- **Reports**: Sales summary, khata report, profit tracking
- **Dashboard**: Overview, graphs, quick actions

## ✨ Tips for Success

1. **Backup Regularly**: Export database from phpMyAdmin weekly
2. **Train Staff**: Show them POS and basic features
3. **Add Items First**: Build your inventory before opening
4. **Set Credit Limits**: Configure customer credit limits appropriately
5. **Check Reports**: Review daily sales and profit reports

---

**Need Help?** Check the full [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) for detailed instructions.

**Ready to Go!** 🚀 Your mobile shop POS is set up and ready for business!
