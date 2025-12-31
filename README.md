# 📱 Mobile Shop POS System

**Version 1.0.0** - Production Ready  
A complete Point of Sale system designed specifically for Pakistani mobile phone shops with IMEI tracking, credit/khata management, and comprehensive inventory control.

---

## 🌟 Key Features

### 📦 Inventory Management
- ✅ **Dual Item Types:**
  - **Standard Items** - Accessories (cables, cases, chargers) with quantity tracking
  - **Serialized Items** - Mobile phones with individual IMEI tracking
- ✅ **Real-time IMEI Validation** - Instant duplicate detection while typing
- ✅ **Cost & Profit Tracking** - Automatic profit calculation per item
- ✅ **Stock Management** - Add stock, deficit tracking, low stock alerts
- ✅ **Multi-color Support** - Track same model in different colors
- ✅ **Warranty Management** - Track warranty periods per item
- ✅ **Advanced Filters** - Filter by category, type, search by name/code/IMEI

### 💰 Sales & Transactions
- ✅ **POS Interface** - Fast and intuitive sales processing
- ✅ **Multiple Payment Methods** - Cash, POS, Credit/Khata, Mixed payments
- ✅ **IMEI Selection** - Choose specific IMEI for serialized items
- ✅ **Receipt Generation** - Thermal printer support
- ✅ **Transaction History** - Complete sales records with filters

### 👥 Customer Management (Khata System)
- ✅ **Credit Control** - Enable/disable credit per customer
- ✅ **Credit Limits** - Set maximum credit amount
- ✅ **Customer Ledger** - Complete transaction history
- ✅ **Payment Recording** - Track payments and outstanding balances
- ✅ **Status Management** - Active, Inactive, Blocked customers
- ✅ **CNIC Tracking** - Customer identification
- ✅ **Smart Filtering** - Only active customers show in POS

### 📊 Dashboard & Reports
- ✅ **Real-time Dashboard** - Today's earnings, sales summary
- ✅ **Payment Method Analytics** - Visual breakdown of payment types
- ✅ **Profit Reports** - Daily, monthly, and custom date ranges
- ✅ **Inventory Reports** - Stock value, low stock alerts
- ✅ **Customer Reports** - Outstanding balances, credit usage

### 🎨 User Experience
- ✅ **Pakistani Context** - Designed for local business practices
- ✅ **Urdu-friendly** - Works with Urdu names and addresses
- ✅ **No Email Required** - Phone-based customer management
- ✅ **Custom Notifications** - No browser alerts, clean UI notifications
- ✅ **Responsive Design** - Works on desktop and tablets
- ✅ **Fast Performance** - Optimized for quick operations

---

## 🚀 Quick Start

### Prerequisites
- **XAMPP** (Apache + MySQL + PHP 7.4+)
- **Web Browser** (Chrome, Firefox, Edge)
- **Windows/Linux/Mac**

### Installation Steps

1. **Download & Extract**
   ```bash
   # Extract to XAMPP htdocs folder
   C:\xampp\htdocs\mobile-shop-pos\
   ```

2. **Create Database**
   ```sql
   CREATE DATABASE mobile_shop_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Select `mobile_shop_pos` database
   - Import: `database/mobile_shop_pos_complete.sql`

4. **Run Required SQL Updates**
   Execute these SQL files in order:
   ```sql
   -- 1. Add cost price column
   database/add_cost_price_column.sql
   
   -- 2. Fix inventory view
   database/fix_inventory_view.sql
   
   -- 3. Add credit enabled column
   database/add_credit_enabled_column.sql
   
   -- 4. Fix customer ledger
   database/fix_customer_ledger_columns.sql
   ```

5. **Configure Environment**
   Edit `.env` file:
   ```env
   # Database
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=mobile_shop_pos
   
   # Shop Details
   SHOP_NAME=Your Shop Name
   SHOP_ADDRESS=Your Address
   SHOP_PHONE=+92-XXX-XXXXXXX
   SHOP_NTN=XXXXXXX-X
   ```

6. **Access Application**
   ```
   URL: http://localhost/mobile-shop-pos/
   Default Login:
   Email: admin@mobileshop.com
   Password: admin123
   ```

7. **Change Default Password**
   ⚠️ **Important:** Change admin password immediately after first login!

---

## 📖 User Guide

### Adding Items

**Standard Items (Accessories):**
1. Go to **Inventory Items**
2. Click **Add New Item**
3. Select **Standard** type
4. Fill: Name, Category, Brand, Price, Quantity
5. Optional: Cost Price (for profit tracking)
6. Click **Add Item**

**Serialized Items (Mobiles):**
1. Go to **Inventory Items**
2. Click **Add New Item**
3. Select **Serialized** type
4. Fill: Name, Category, Brand, Selling Price
5. Add IMEI numbers (system validates duplicates)
6. Optional: Color, Cost Price
7. Click **Add Item**

### Managing Customers

**Add Customer:**
1. Go to **Customers**
2. Click **Add Customer**
3. Fill: Name, Phone (required)
4. Optional: Address, CNIC
5. **Enable Credit** checkbox if trusted customer
6. Set Credit Limit (e.g., 50,000)
7. Click **Save**

**Customer Types:**
- **Cash Customer** - Credit disabled, cash only
- **Credit Customer** - Credit enabled with limit
- **Inactive** - Hidden from POS
- **Blocked** - Cannot transact

### Processing Sales

1. Go to **Transactions**
2. Search customer (or walk-in)
3. Add items to cart
4. For mobiles: Select specific IMEI
5. Choose payment method:
   - Cash
   - POS/Card
   - Credit (if customer has credit enabled)
   - Mixed payment
6. Complete sale
7. Print receipt

### Recording Payments

1. Go to **Customers**
2. Find customer with balance
3. Click **💰 Payment** button
4. Enter amount
5. Add notes (optional)
6. Click **Record Payment**

---

## 🗄️ Database Structure

### Key Tables
- **items** - Inventory items (standard & serialized)
- **item_serials** - IMEI tracking for mobiles
- **customers** - Customer information
- **customer_ledger** - Credit/payment history
- **transactions** - Sales records
- **admin** - User accounts

### Important Views
- **inventory_available** - Real-time stock with IMEI counts
- **profit_report** - Profit calculations
- **daily_sales_summary** - Sales analytics

---

## 🔧 Configuration

### Shop Settings (.env)
```env
SHOP_NAME=Mobile World
SHOP_ADDRESS=Shop #123, Main Market, Karachi
SHOP_PHONE=+92-300-1234567
SHOP_NTN=1234567-8
CURRENCY_SYMBOL=Rs.
CURRENCY_CODE=PKR
```

### Thermal Printer
```env
PRINTER_TYPE=network
PRINTER_ADDRESS=192.168.1.100
PRINTER_PORT=9100
```

---

## 📊 Reports Available

1. **Dashboard**
   - Today's earnings
   - Payment method breakdown
   - Quick stats

2. **Profit Reports**
   - Daily profit
   - Monthly profit
   - Custom date range

3. **Inventory Reports**
   - Stock value
   - Low stock items
   - IMEI status

4. **Customer Reports**
   - Outstanding balances
   - Credit usage
   - Payment history

---

## 🛡️ Security Features

- ✅ Password hashing (bcrypt)
- ✅ SQL injection protection
- ✅ XSS prevention
- ✅ CSRF protection
- ✅ Session management
- ✅ Role-based access control
- ✅ Input validation

---

## 🐛 Troubleshooting

### Common Issues

**1. Database Connection Error**
```
Solution: Check .env file, verify MySQL is running
```

**2. Items Not Showing**
```
Solution: Run database/fix_inventory_view.sql
```

**3. Cost Price Not Showing**
```
Solution: Run database/add_cost_price_column.sql
```

**4. Customer Ledger Errors**
```
Solution: Run database/fix_customer_ledger_columns.sql
```

**5. IMEI Validation Not Working**
```
Solution: Clear browser cache, check console for errors
```

---

## 📝 Changelog

### Version 1.0.0 (December 2024)
- ✅ Complete inventory management system
- ✅ IMEI tracking with real-time validation
- ✅ Customer credit/khata system
- ✅ Cost price & profit tracking
- ✅ Multiple payment methods
- ✅ Dashboard with analytics
- ✅ Thermal printer support
- ✅ Pakistani business context optimization
- ✅ Custom notifications (no browser alerts)
- ✅ Responsive design
- ✅ Email field removed (Pakistani context)
- ✅ Credit enable/disable per customer
- ✅ Active/Inactive customer filtering

---

## 🤝 Support

For issues, questions, or feature requests:
- Create an issue on GitHub
- Email: support@mobileshoppos.com

---

## 📄 License

This project is licensed under the MIT License.

---

## 👨‍💻 Credits

**Developed for Pakistani Mobile Phone Shops**  
Optimized for local business practices and requirements.

**Technology Stack:**
- CodeIgniter 3.x
- MySQL 5.7+
- jQuery 3.x
- Bootstrap 3.x
- Font Awesome 4.x

---

## 🎯 Roadmap

### Planned Features
- [ ] SMS notifications for customers
- [ ] WhatsApp integration
- [ ] Barcode scanning
- [ ] Multi-branch support
- [ ] Mobile app
- [ ] Online payment integration (JazzCash, EasyPaisa)
- [ ] Backup automation
- [ ] Advanced analytics

---

## ⚠️ Important Notes

1. **Backup Regularly** - Always backup your database
2. **Change Default Password** - Security first!
3. **Test Before Production** - Use test data initially
4. **Keep Updated** - Check for updates regularly
5. **Secure Your Server** - Use HTTPS in production

---

## 🚀 Production Deployment

### Recommended Setup
- **VPS/Dedicated Server** (not shared hosting)
- **SSL Certificate** (Let's Encrypt free)
- **Regular Backups** (daily automated)
- **Firewall** (UFW/iptables)
- **PHP 7.4+** with required extensions
- **MySQL 5.7+** or MariaDB 10.3+

---

**Made with ❤️ for Pakistani Mobile Shop Owners**

*Simplifying business, one sale at a time.* 🇵🇰📱
