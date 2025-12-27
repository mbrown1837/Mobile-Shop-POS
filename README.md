# Mobile Shop POS System

A comprehensive Point of Sale system designed specifically for mobile phone retail shops in Pakistan.

## 🚀 Quick Start

### Prerequisites
- XAMPP (PHP 8.2+, MySQL/MariaDB)
- Web browser (Chrome, Firefox, Edge)

### Installation

1. **Extract files** to `C:\xampp\htdocs\mobile-shop-pos\`

2. **Import Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click "Import" tab
   - Select file: `database/mobile_shop_pos_complete.sql`
   - Click "Go"

3. **Configure Environment**
   - Edit `.env` file if needed (default settings work for XAMPP)

4. **Access System**
   - URL: http://localhost/mobile-shop-pos/
   - Email: `admin@shop.com`
   - Password: `admin123`

## 📁 Project Structure

```
mobile-shop-pos/
├── application/          # CodeIgniter application (MVC)
│   ├── controllers/     # Business logic
│   ├── models/          # Database operations
│   ├── views/           # UI templates
│   └── config/          # Configuration files
├── database/            # SQL files and migrations
├── public/              # Assets (CSS, JS, images)
├── system/              # CodeIgniter core
├── _docs/               # Documentation and old files
├── _test_files/         # Test and debug files
├── .env                 # Environment configuration
└── index.php            # Application entry point
```

## ✨ Key Features

### Inventory Management
- **Dual Item Types**: Standard (accessories) & Serialized (mobiles with IMEI)
- **IMEI Tracking**: Individual tracking for each mobile phone
- **Stock Management**: Real-time quantity updates
- **Warranty Tracking**: Months and terms

### Point of Sale (POS)
- **Unified Search**: Search by name, code, IMEI, brand, or model
- **Smart Cart**: Automatic IMEI reservation
- **Multiple Payment Methods**: Cash, Card, Credit, Partial
- **Discount & VAT**: Flexible pricing
- **Trade-in Support**: Accept old devices

### Customer Management (Khata)
- **Credit System**: Track customer credit purchases
- **Credit Limits**: Set maximum credit per customer
- **Payment Recording**: Track all payments
- **Customer Ledger**: Complete transaction history

### Profit Tracking
- **Individual Cost Tracking**: Per-IMEI cost price
- **Profit Reports**: Daily, monthly, yearly
- **Margin Analysis**: Track profit margins
- **Top Performers**: Best selling items

### Reports & Analytics
- Daily sales and profit
- Inventory status and value
- Customer outstanding balances
- Payment method distribution
- Sales trends and charts

## 🔐 User Roles

- **Super Admin**: Full system access
- **Manager**: Limited admin access
- **Staff**: POS and basic operations

## 💾 Database

- **Tables**: 9 core tables
- **Views**: 2 optimized views for reporting
- **Encoding**: UTF-8 (supports Urdu)
- **Engine**: InnoDB with foreign keys

## 🛠️ Technology Stack

- **Backend**: PHP 8.2+ with CodeIgniter 3
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 3, jQuery, Font Awesome
- **Components**: Select2, DateTimePicker

## 📖 Documentation

All documentation files are in the `_docs/` folder:
- `FEATURES_CHECKLIST.md` - Complete feature list
- `SETUP_COMPLETE.md` - Detailed setup guide
- `CURRENCY_USAGE_GUIDE.md` - Currency configuration
- And more...

## 🧪 Testing

Test files are in the `_test_files/` folder for debugging purposes.

## 🔧 Configuration

### Database (.env)
```
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=mobile_shop_pos
```

### Currency (application/config/currency.php)
```php
$config['currency_symbol'] = 'Rs.';
$config['currency_code'] = 'PKR';
```

### Base URL (application/config/config.php)
Automatically detects localhost or production environment.

## 📊 System Status

- **Feature Completion**: 90%
- **Production Ready**: Yes
- **PHP 8.2 Compatible**: Yes
- **Security**: Bcrypt passwords, XSS protection, role-based access

## 🆘 Support

For issues or questions:
1. Check documentation in `_docs/` folder
2. Review test files in `_test_files/` folder
3. Check database structure in `database/` folder

## 📝 License

See `license.txt` for details.

## 🎯 Next Steps After Installation

1. **Change Admin Password** (Security!)
2. **Add Your Items** (Inventory → Add Item)
3. **Add Customers** (Customers → Add Customer)
4. **Configure Settings** (Currency, Tax, etc.)
5. **Start Selling!** (Transactions → POS)

---

**Made for Mobile Phone Shops in Pakistan** 🇵🇰
