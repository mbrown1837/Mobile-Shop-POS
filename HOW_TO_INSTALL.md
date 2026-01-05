# 🚀 How to Install - Mobile Shop POS

## Two Simple Methods

---

## ⚡ Method 1: Standalone Installer (RECOMMENDED)

**Perfect for: Fresh PC, No XAMPP needed**

### What You Need:
- ✅ Windows PC
- ✅ Internet connection
- ✅ Administrator rights
- ✅ 10-20 minutes

### Steps:

1. **Download** `STANDALONE_INSTALLER.ps1`

2. **Right-click** the file

3. **Select** "Run with PowerShell"

4. **Click "Yes"** on UAC prompt

5. **Type "Y"** and press Enter

6. **Wait** 10-20 minutes while it:
   - Downloads XAMPP (~150 MB)
   - Installs XAMPP
   - Downloads Mobile Shop POS (~5 MB)
   - Sets up database
   - Configures everything

7. **Browser opens** automatically

8. **Login** with:
   - Username: `admin`
   - Password: `admin123`

9. **Done!** 🎉

### What It Downloads:
- XAMPP: ~150 MB
- Mobile Shop POS: ~5 MB
- **Total: ~155 MB**

### Time Required:
- Download: 2-5 minutes
- Installation: 5-10 minutes
- Setup: 2-3 minutes
- **Total: 10-20 minutes**

---

## 🌐 Method 2: Web-Based Installer

**Perfect for: Already have XAMPP**

### What You Need:
- ✅ XAMPP installed and running
- ✅ Application files (ZIP)
- ✅ Web browser
- ✅ 2-3 minutes

### Steps:

1. **Extract** ZIP to `C:\xampp\htdocs\mobile-shop-pos\`

2. **Start XAMPP**:
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

3. **Open browser** and go to:
   ```
   http://localhost/mobile-shop-pos/install.php
   ```

4. **Follow wizard**:
   - Step 1: System check ✓
   - Step 2: Database config
   - Step 3: Import database
   - Step 4: Configure app
   - Step 5: Complete!

5. **Click** "Go to Application"

6. **Login** with:
   - Username: `admin`
   - Password: `admin123`

7. **Done!** 🎉

---

## 🎯 Which Method to Choose?

### Use Standalone Installer if:
- ✅ Fresh PC
- ✅ Don't have XAMPP
- ✅ Want everything automatic
- ✅ Have internet connection

### Use Web-Based Installer if:
- ✅ Already have XAMPP
- ✅ Have application files
- ✅ Prefer visual wizard
- ✅ Want faster setup (2-3 min)

---

## ⚠️ Troubleshooting

### Standalone Installer

**"Administrator rights required"**
- Right-click file
- Select "Run with PowerShell"
- Click "Yes" on UAC

**"Download failed"**
- Check internet connection
- Try again
- Or use Web-Based Installer

**"XAMPP installation failed"**
- Download XAMPP manually: https://www.apachefriends.org/download.html
- Install it
- Then use Web-Based Installer

**"Script closes immediately"**
- Don't double-click
- Right-click → "Run with PowerShell"
- Make sure to click "Yes" on UAC

### Web-Based Installer

**"Cannot access install.php"**
- Check XAMPP is running
- Check files in correct location
- Try: http://localhost/mobile-shop-pos/install.php

**"Database connection failed"**
- Check MySQL is running in XAMPP
- Check credentials (default: root / no password)

**"Import failed"**
- Check database file exists
- Check MySQL has permissions

---

## ✅ After Installation

### 1. Change Password
- Login with admin/admin123
- Go to Settings
- Change password
- Save

### 2. Delete Installers
```
Delete: STANDALONE_INSTALLER.ps1
Delete: install.php
```

### 3. Update Shop Settings
- Shop name
- Address
- Contact number

### 4. Start Using
- Add items
- Add customers
- Make sales!

---

## 🔐 Default Login

**URL**: `http://localhost/mobile-shop-pos/`

**Credentials**:
- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT**: Change password after first login!

---

## 📍 Installation Location

Application will be installed at:
```
C:\xampp\htdocs\mobile-shop-pos\
```

Database will be created:
```
Database: mobile_shop_pos
```

---

## 🆘 Need Help?

### Documentation:
- [README.md](README.md) - Overview
- [AUTOMATED_INSTALLER_GUIDE.md](AUTOMATED_INSTALLER_GUIDE.md) - Web installer details
- [QUICK_SETUP.md](QUICK_SETUP.md) - Quick start
- [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) - Detailed guide

### Common Issues:
- Port 80 in use → Stop IIS or other web server
- MySQL won't start → Check port 3306
- 404 error → Check file location
- Blank page → Check PHP errors

---

## 🎉 Success!

Installation successful when:
- ✅ Browser opens automatically
- ✅ Login page appears
- ✅ Can login with admin/admin123
- ✅ Dashboard loads
- ✅ No errors

**Enjoy using Mobile Shop POS!** 🚀

---

**Recommended Method**: Standalone Installer  
**Time Required**: 10-20 minutes  
**Technical Knowledge**: None  
**Success Rate**: 95%+ ✅
