# 📱 Mobile Shop POS System

A comprehensive Point of Sale (POS) system designed specifically for mobile phone shops. Manage inventory, track IMEI numbers, handle customer credit (Khata), and generate detailed reports.

## ✨ Key Features

### 🛒 Point of Sale
- **Smart Search**: Search by name, code, IMEI, brand, or model
- **Dual Item Types**:
  - Serialized items (mobiles) with IMEI tracking
  - Standard items (accessories) with quantity management
- **Payment Methods**: Cash and Credit/Khata
- **IMEI Management**: Automatic locking and tracking for mobile phones
- **Dual SIM Support**: Handle multiple IMEIs per device
- **Receipt Generation**: Clean, printable receipts

### 📦 Inventory Management
- Add, edit, and manage items
- IMEI tracking for mobile phones
- Stock status indicators (In Stock, Low Stock, Sold Out)
- Filter by stock status
- Automatic quantity updates on sales
- Cost price and profit tracking

### 👥 Customer Management
- Customer database with contact details
- Credit limit management
- Current balance tracking (Khata/Udhar)
- Customer ledger with transaction history
- Payment recording
- Outstanding balance reports

### 📊 Reports & Analytics
- **Sales Summary**: Daily, monthly, and item-wise reports
- **Khata Report**: Outstanding customer balances
- **Profit Tracking**: Real-time profit calculations
- **Dashboard**: Visual overview with graphs and metrics
- **Payment Breakdown**: Cash vs Credit analysis

### 📈 Dashboard
- Today's sales and profit metrics
- Outstanding khata summary
- Inventory status
- Monthly sales graph (smooth area chart)
- Quick action buttons

## 🚀 Quick Start

### Requirements
- PHP 7.4+ (PHP 8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Apache with mod_rewrite enabled
- Modern web browser

### Installation

#### ⚡ Quick Install (Automated - RECOMMENDED)

1. **Extract files** to web directory
2. **Open browser**: `http://localhost/mobile-shop-pos/install.php`
3. **Follow wizard** (2-3 minutes)
4. **Done!** 🎉

See [AUTOMATED_INSTALLER_GUIDE.md](AUTOMATED_INSTALLER_GUIDE.md) for details.

#### 📝 Manual Install

1. **Clone or Download**:
   ```bash
   git clone <repository-url>
   cd mobile-shop-pos
   ```

2. **Create Database**:
   - Create database: `mobile_shop_pos`
   - Import: `database/mobile_shop_pos_v1.1.0_final.sql`

3. **Configure**:
   - Update `application/config/database.php` with your credentials
   - Update `application/config/config.php` with your base URL

4. **Access**:
   - Open: `http://localhost/mobile-shop-pos/`
   - Login: `admin` / `admin123`
   - **Change password immediately!**

📖 **For detailed instructions**:
- **Automated**: [AUTOMATED_INSTALLER_GUIDE.md](AUTOMATED_INSTALLER_GUIDE.md) ⚡
- **Manual**: [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)
- **Quick Start**: [QUICK_SETUP.md](QUICK_SETUP.md)

## 📸 Screenshots

### Dashboard
Clean, responsive dashboard with real-time metrics and sales graph.

### POS Interface
Intuitive search and cart management with support for both mobile phones and accessories.

### Customer Management
Track customer balances, credit limits, and transaction history.

### Reports
Comprehensive sales and profit reports with filtering options.

## 🔧 Technology Stack

- **Backend**: PHP (CodeIgniter 3)
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **UI Framework**: Bootstrap 3
- **Charts**: Chart.js
- **Icons**: Font Awesome

## 📋 System Architecture

### Database Tables
- `items` - Product inventory
- `item_serials` - IMEI tracking for mobile phones
- `customers` - Customer information
- `customer_ledger` - Transaction history
- `transactions` - Sales records
- `admin` - User authentication
- `inventory_available` - View for accurate stock counts

### Key Features Implementation
- **IMEI Tracking**: Status flow (available → reserved → sold)
- **Credit System**: Automatic balance updates in customer ledger
- **Profit Calculation**: Real-time profit tracking per transaction
- **Dynamic Search**: Responsive table with conditional columns

## 🔐 Security Features

- Session-based authentication
- Password hashing
- SQL injection prevention (prepared statements)
- XSS protection
- CSRF protection
- Input validation and sanitization

## 📱 Mobile Responsive

The system is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

## 🛠️ Configuration

### Shop Settings
Configure your shop details in Settings:
- Shop Name
- Address
- Contact Number
- Currency Symbol

### Environment Variables
Optional `.env` file for shop configuration:
```env
SHOP_NAME=Your Shop Name
SHOP_ADDRESS=Your Address
SHOP_PHONE=Your Phone
CURRENCY_SYMBOL=Rs.
```

## 📦 Release Management

### Creating a Release
Use the provided script to create a clean release package:
```bash
.\create-release-zip.ps1
```

This creates a production-ready ZIP file excluding:
- Development files
- Git repository
- Documentation files
- Test files

See [CREATE_RELEASE_ZIP.md](CREATE_RELEASE_ZIP.md) for details.

## 🔄 Version History

### v1.1.0 (Current)
- ✅ Simplified POS (Cash and Credit only)
- ✅ Dual SIM support (multiple IMEIs)
- ✅ Dynamic search results
- ✅ Customer credit/khata system
- ✅ Improved dashboard with graphs
- ✅ Stock status filters
- ✅ Responsive design improvements
- ✅ Customer delete functionality
- ✅ Simplified receipt format

### v1.0.0
- Initial release
- Basic POS functionality
- Inventory management
- Customer management
- Basic reporting

## 📝 Usage Guide

### Adding Items

**Standard Items (Accessories)**:
1. Go to Manage Items
2. Click "Add New Item"
3. Select "Standard" type
4. Enter name, price, quantity
5. Save

**Serialized Items (Mobiles)**:
1. Go to Manage Items
2. Click "Add New Item"
3. Select "Serialized" type
4. Enter name, price, brand, model
5. Add IMEI numbers (1 or 2 for dual SIM)
6. Save

### Making a Sale

1. Search for item (name, code, IMEI, brand)
2. Click "Add" to add to cart
3. For mobiles: Select specific unit with IMEI
4. For accessories: Choose quantity
5. Apply discount (optional)
6. Select payment method:
   - **Cash**: Enter amount received
   - **Credit**: Select customer
7. Complete transaction
8. Print receipt

### Managing Customers

1. Go to Customers
2. Add customer with name, phone, credit limit
3. View ledger for transaction history
4. Record payments for outstanding balances
5. Track khata/udhar

### Viewing Reports

1. Go to Reports
2. Select report type:
   - Sales Summary (Daily/Monthly/Item-wise)
   - Khata Report (Outstanding balances)
3. Filter by date range
4. Print or export

## 🐛 Troubleshooting

### Common Issues

**404 Errors**: Enable Apache mod_rewrite  
**Database Connection**: Check credentials in `database.php`  
**Blank Page**: Check PHP version (7.4+ required)  
**Permission Errors**: Set proper folder permissions

See [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) for detailed troubleshooting.

## 💾 Backup

**Important**: Regular backups are essential!

1. **Database**: Export from phpMyAdmin regularly
2. **Files**: Backup entire project folder
3. **Frequency**: Daily backups recommended for active shops

## 🤝 Contributing

This is a production system. For modifications:
1. Test thoroughly in development environment
2. Backup database before updates
3. Document all changes

## 📄 License

See [license.txt](license.txt) for license information.

## 🙏 Credits

Built with:
- CodeIgniter Framework
- Bootstrap
- jQuery
- Chart.js
- Font Awesome
- Select2

## 📞 Support

For installation help or issues:
1. Check [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)
2. Review [SYSTEM_VERIFICATION_CHECKLIST.md](SYSTEM_VERIFICATION_CHECKLIST.md)
3. Check troubleshooting section above

---

**Version**: 1.1.0  
**Last Updated**: January 2026  
**Status**: Production Ready ✅

Made with ❤️ for mobile shop owners
