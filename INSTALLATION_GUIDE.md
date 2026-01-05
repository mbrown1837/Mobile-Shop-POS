# Mobile Shop POS - Installation Guide

## 📋 System Requirements

- **Web Server**: Apache 2.4+ (with mod_rewrite enabled)
- **PHP**: 7.4 or higher (PHP 8.0+ recommended)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Browser**: Modern browser (Chrome, Firefox, Edge, Safari)

## 🚀 Installation Steps

### Step 1: Download & Extract

1. Download the project files
2. Extract to your web server directory:
   - **XAMPP**: `C:\xampp\htdocs\mobile-shop-pos`
   - **WAMP**: `C:\wamp\www\mobile-shop-pos`
   - **Linux**: `/var/www/html/mobile-shop-pos`

### Step 2: Database Setup

1. **Create Database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Click "New" to create a database
   - Database name: `mobile_shop_pos`
   - Collation: `utf8mb4_general_ci`

2. **Import Database**:
   - Select the `mobile_shop_pos` database
   - Click "Import" tab
   - Choose file: `database/mobile_shop_pos_v1.1.0_final.sql`
   - Click "Go" to import

3. **Verify Tables**:
   - Check that these tables exist:
     - `items`
     - `item_serials`
     - `customers`
     - `customer_ledger`
     - `transactions`
     - `admin`
     - `inventory_available` (view)

### Step 3: Configuration

1. **Database Configuration**:
   - Open file: `application/config/database.php`
   - Update these settings:
   ```php
   'hostname' => 'localhost',
   'username' => 'root',        // Your MySQL username
   'password' => '',            // Your MySQL password
   'database' => 'mobile_shop_pos',
   ```

2. **Base URL Configuration**:
   - Open file: `application/config/config.php`
   - Update base URL:
   ```php
   $config['base_url'] = 'http://localhost/mobile-shop-pos/';
   ```
   - If using different port or domain, update accordingly

3. **Environment File** (Optional):
   - Copy `.env.example` to `.env` (if exists)
   - Update shop details:
   ```
   SHOP_NAME=Your Shop Name
   SHOP_ADDRESS=Your Shop Address
   SHOP_PHONE=Your Phone Number
   CURRENCY_SYMBOL=Rs.
   ```

### Step 4: File Permissions

**For Linux/Mac**:
```bash
chmod -R 755 application/cache
chmod -R 755 application/logs
chmod -R 755 public/uploads
```

**For Windows**: No special permissions needed

### Step 5: Access the System

1. **Open in Browser**:
   ```
   http://localhost/mobile-shop-pos/
   ```

2. **Default Login Credentials**:
   - **Username**: `admin`
   - **Password**: `admin123`
   
   ⚠️ **IMPORTANT**: Change password immediately after first login!

### Step 6: Initial Setup

1. **Change Admin Password**:
   - Go to Settings → Change Password
   - Set a strong password

2. **Update Shop Settings**:
   - Go to Settings
   - Update:
     - Shop Name
     - Shop Address
     - Contact Number
     - Currency Symbol (if needed)

3. **Add First Items**:
   - Go to Manage Items
   - Click "Add New Item"
   - Add your inventory

4. **Add Customers** (Optional):
   - Go to Customers
   - Click "Add Customer"
   - Add customer details for credit sales

## 🔧 Troubleshooting

### Issue: "404 Page Not Found"

**Solution**: Enable mod_rewrite in Apache
- **XAMPP**: Edit `httpd.conf`, uncomment:
  ```
  LoadModule rewrite_module modules/mod_rewrite.so
  ```
- Restart Apache

### Issue: "Database Connection Error"

**Solution**: Check database credentials
1. Verify MySQL is running
2. Check username/password in `application/config/database.php`
3. Ensure database `mobile_shop_pos` exists

### Issue: "Blank Page" or "PHP Errors"

**Solution**: Check PHP version and extensions
1. Ensure PHP 7.4+ is installed
2. Required extensions:
   - mysqli
   - mbstring
   - json
   - session

### Issue: "Permission Denied"

**Solution**: Set proper folder permissions (Linux/Mac)
```bash
chmod -R 755 application/cache
chmod -R 755 application/logs
```

## 📱 Features Overview

### 1. Point of Sale (POS)
- Search items by name, code, IMEI, brand, or model
- Support for mobile phones (with IMEI tracking)
- Support for accessories (with quantity)
- Cash and Credit/Khata payment methods
- Automatic IMEI locking and tracking
- Receipt generation

### 2. Inventory Management
- Add/Edit/Delete items
- Two item types:
  - **Standard**: Accessories with quantity tracking
  - **Serialized**: Mobiles with IMEI tracking
- Stock status filters (In Stock, Low Stock, Sold Out)
- Bulk stock updates

### 3. Customer Management
- Add/Edit/Delete customers
- Credit limit management
- Current balance tracking (Khata)
- Customer ledger (transaction history)
- Payment recording

### 4. Reports
- **Sales Summary**: Daily, Monthly, Item-wise
- **Khata Report**: Outstanding customer balances
- Profit tracking
- Payment method breakdown

### 5. Dashboard
- Today's sales and profit
- Outstanding khata summary
- Items in stock
- Monthly sales graph
- Quick action buttons

## 🔐 Security Recommendations

1. **Change Default Password**: Immediately after installation
2. **Database Backup**: Regular backups of your database
3. **File Permissions**: Restrict write access to necessary folders only
4. **HTTPS**: Use SSL certificate for production (recommended)
5. **Update Regularly**: Keep PHP and MySQL updated

## 💾 Backup & Restore

### Backup Database
1. Open phpMyAdmin
2. Select `mobile_shop_pos` database
3. Click "Export"
4. Choose "Quick" method
5. Click "Go" to download SQL file

### Restore Database
1. Open phpMyAdmin
2. Select `mobile_shop_pos` database
3. Click "Import"
4. Choose your backup SQL file
5. Click "Go"

## 📞 Support

For issues or questions:
1. Check this installation guide
2. Review troubleshooting section
3. Check system requirements
4. Verify all configuration files

## 📝 Quick Start Checklist

- [ ] Database created and imported
- [ ] Database credentials configured
- [ ] Base URL configured
- [ ] System accessible in browser
- [ ] Logged in with default credentials
- [ ] Admin password changed
- [ ] Shop settings updated
- [ ] First items added
- [ ] Test transaction completed
- [ ] Receipt generated successfully

## 🎯 Next Steps

After installation:
1. Add your inventory items
2. Add regular customers
3. Configure shop settings
4. Train staff on POS usage
5. Set up regular database backups

---

**Version**: 1.1.0  
**Last Updated**: January 2026

For the latest updates and documentation, check the README.md file.
