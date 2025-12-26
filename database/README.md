# Database Setup Instructions

## Quick Import (Recommended)

### Step 1: Open phpMyAdmin
Go to: http://localhost/phpmyadmin

### Step 2: Import the SQL File
1. Click on "Import" tab (top menu)
2. Click "Choose File"
3. Select: `database/mobile_shop_pos_complete.sql`
4. Scroll down and click "Go"

**That's it!** The SQL file will:
- Drop the old database (if exists)
- Create a fresh `mobile_shop_pos` database
- Create all tables with correct structure
- Insert test data

### Step 3: Login
- **URL**: http://localhost/Mini-Inventory-and-Sales-Management-System/
- **Email**: admin@shop.com
- **Password**: admin123

## What's Included

### Admin Users
- 1 Super Admin (full access)

### Test Customers
- Ahmed Khan (0300-1234567)
- Sara Ali (0321-9876543)
- Hassan Raza (0333-5555555)
- Fatima Malik (0345-7777777)
- Bilal Ahmed (0312-8888888)

### Test Items
**Accessories (8 items):**
- Samsung Fast Charger 25W
- iPhone Lightning Charger
- Universal Fast Charger
- Phone Case Universal
- Tempered Glass Screen Protector
- Samsung Galaxy Buds
- Power Bank 10000mAh
- USB-C Cable

**Mobile Phones (6 items):**
- iPhone 13 (3 units with IMEI)
- iPhone 13 Pro (2 units with IMEI)
- Samsung Galaxy S21 (3 units with IMEI)
- Samsung Galaxy S21 Ultra (2 units with IMEI)
- Xiaomi Redmi Note 11 (3 units with IMEI)
- OnePlus 9 (2 units with IMEI)

**Total: 15 serialized phones ready for sale**

## Database Structure

### Core Tables
- `admin` - User accounts with proper bcrypt passwords
- `customers` - Customer information and credit limits
- `items` - Product catalog (standard and serialized)
- `item_serials` - IMEI tracking for phones
- `transactions` - Sales records
- `customer_ledger` - Customer payment history
- `trade_ins` - Trade-in device records
- `eventlog` - System activity log

### Views
- `inventory_available` - Real-time inventory status
- `profit_report` - Sales profit analysis

## Troubleshooting

### Import Failed?
- Make sure MySQL is running in XAMPP
- Check if you have permission to drop/create databases
- Try running as root user in phpMyAdmin

### Can't Login?
- Verify database name is `mobile_shop_pos`
- Check `.env` file has correct DB credentials
- Clear browser cache and try again

### Still Having Issues?
Check the main `SETUP_COMPLETE.md` file in the root directory for more help.
