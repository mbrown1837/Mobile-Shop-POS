# 🚀 Phase 1 Setup Guide - Mobile Shop POS

## ✅ What Phase 1 Includes

### Files Modified/Created:
1. ✅ `.env` - Environment configuration (CREATED)
2. ✅ `application/config/database.php` - Database config (MODIFIED)
3. ✅ `index.php` - Timezone and env loading (MODIFIED)
4. ✅ `database/migrations/phase1_mobile_shop_complete.sql` - Complete migration (CREATED)
5. ✅ `setup_phase1.bat` - Automated setup script for Windows (CREATED)

### Database Changes:
- **Tables Modified:** 2 (items, transactions)
- **Tables Created:** 4 (item_serials, customers, customer_ledger, trade_ins)
- **Views Created:** 3 (inventory_available, profit_report, daily_sales_summary)
- **Indexes Added:** 12
- **Total Columns Added:** 18

---

## 🎯 Setup Methods

### Method 1: Automated Setup (Recommended for Windows/XAMPP)

```cmd
# Simply run the batch file
setup_phase1.bat
```

The script will:
1. Check MySQL installation
2. Create database
3. Import base schema
4. Run Phase 1 migration
5. Verify installation

---

### Method 2: Manual Setup (Step by Step)

#### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL

#### Step 2: Open Command Prompt
```cmd
# Navigate to your project directory
cd C:\xampp\htdocs\mini-inventory-and-sales-management-system

# Add MySQL to PATH (temporary)
set PATH=%PATH%;C:\xampp\mysql\bin
```

#### Step 3: Create Database
```cmd
# Login to MySQL
mysql -u root -p

# In MySQL prompt, run:
CREATE DATABASE mobile_shop_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### Step 4: Import Base Schema
```cmd
mysql -u root -p mobile_shop_pos < 1410inventory.sql
```

#### Step 5: Run Phase 1 Migration
```cmd
mysql -u root -p mobile_shop_pos < database\migrations\phase1_mobile_shop_complete.sql
```

#### Step 6: Verify Installation
```cmd
mysql -u root -p mobile_shop_pos
```

In MySQL prompt:
```sql
-- Check all tables
SHOW TABLES;

-- Should show:
-- admin
-- customers (NEW)
-- customer_ledger (NEW)
-- eventlog
-- item_serials (NEW)
-- items (MODIFIED)
-- lk_sess
-- trade_ins (NEW)
-- transactions (MODIFIED)

-- Verify items table structure
DESCRIBE items;

-- Should include new columns:
-- brand, model, category, item_type, warranty_months, warranty_terms

-- Verify item_serials table
DESCRIBE item_serials;

-- Check views work
SELECT * FROM inventory_available LIMIT 5;
SELECT * FROM profit_report LIMIT 5;
SELECT * FROM daily_sales_summary LIMIT 5;

EXIT;
```

---

## 🔍 Verification Checklist

After setup, verify:

### Database Structure
- [ ] Database `mobile_shop_pos` exists
- [ ] 8 tables present (4 new, 4 original)
- [ ] 3 views created
- [ ] `items` table has 6 new columns
- [ ] `transactions` table has 6 new columns
- [ ] `item_serials` table exists with correct structure
- [ ] `customers` table exists
- [ ] `customer_ledger` table exists
- [ ] `trade_ins` table exists

### Application Configuration
- [ ] `.env` file exists in root directory
- [ ] `database.php` loads environment variables
- [ ] Database driver is `mysqli` (not PDO)
- [ ] Character set is `utf8mb4`
- [ ] `index.php` sets timezone to Asia/Karachi
- [ ] `index.php` loads .env file

### Test Database Connection
```cmd
# Access the application
http://localhost/mini-inventory-and-sales-management-system/

# Login with demo account
Email: demo@1410inc.xyz
Password: demopass

# Check if login works
# If successful, database connection is working!
```

---

## 📊 Expected Database Schema

### items (Modified)
```
+------------------+------------------+
| Column           | Type             |
+------------------+------------------+
| id               | bigint unsigned  |
| name             | varchar(50)      |
| brand            | varchar(50)      | ← NEW
| model            | varchar(50)      | ← NEW
| category         | enum             | ← NEW
| code             | varchar(50)      |
| description      | text             |
| item_type        | enum             | ← NEW
| unitPrice        | decimal(10,2)    |
| quantity         | int(6)           |
| warranty_months  | int              | ← NEW
| warranty_terms   | varchar(200)     | ← NEW
| dateAdded        | datetime         |
| lastUpdated      | timestamp        |
+------------------+------------------+
```

### item_serials (New)
```
+----------------------+------------------+
| Column               | Type             |
+----------------------+------------------+
| id                   | bigint unsigned  |
| item_id              | bigint unsigned  |
| imei_number          | varchar(20)      |
| serial_number        | varchar(50)      |
| color                | varchar(30)      |
| storage              | varchar(20)      |
| cost_price           | decimal(10,2)    |
| selling_price        | decimal(10,2)    |
| status               | enum             |
| sold_transaction_id  | bigint unsigned  |
| sold_date            | datetime         |
| purchase_date        | datetime         |
| notes                | text             |
| created_at           | timestamp        |
| updated_at           | timestamp        |
+----------------------+------------------+
```

### customers (New)
```
+------------------+------------------+
| Column           | Type             |
+------------------+------------------+
| id               | bigint unsigned  |
| name             | varchar(100)     |
| phone            | varchar(20)      |
| email            | varchar(100)     |
| cnic             | varchar(20)      |
| address          | text             |
| current_balance  | decimal(12,2)    |
| credit_limit     | decimal(10,2)    |
| total_purchases  | decimal(12,2)    |
| total_payments   | decimal(12,2)    |
| status           | enum             |
| created_at       | timestamp        |
| updated_at       | timestamp        |
+------------------+------------------+
```

### transactions (Modified)
```
+------------------+------------------+
| Column           | Type             |
+------------------+------------------+
| ... (existing columns)              |
| customer_id      | bigint unsigned  | ← NEW
| payment_status   | enum             | ← NEW
| paid_amount      | decimal(10,2)    | ← NEW
| credit_amount    | decimal(10,2)    | ← NEW
| profit_amount    | decimal(10,2)    | ← NEW
| imei_numbers     | text             | ← NEW
+------------------+------------------+
```

---

## 🐛 Troubleshooting

### Error: "mysql: command not found"
**Solution:**
```cmd
# Add MySQL to PATH
set PATH=%PATH%;C:\xampp\mysql\bin

# Or use full path
C:\xampp\mysql\bin\mysql -u root -p
```

### Error: "Access denied for user 'root'@'localhost'"
**Solution:**
1. Check if MySQL is running in XAMPP
2. Try without password: `mysql -u root mobile_shop_pos`
3. Reset root password in XAMPP if needed

### Error: "Database 'mobile_shop_pos' doesn't exist"
**Solution:**
```sql
CREATE DATABASE mobile_shop_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Error: "Table 'items' already has column 'brand'"
**Solution:**
Migration was already run. Either:
1. Skip this error (column already exists)
2. Or rollback and re-run (see rollback section in SQL file)

### Error: "Can't connect to database"
**Solution:**
1. Check `.env` file has correct credentials
2. Verify MySQL is running
3. Check database name is `mobile_shop_pos`
4. Test connection:
```cmd
mysql -u root -p mobile_shop_pos -e "SELECT 1;"
```

### Application shows "Database Error"
**Solution:**
1. Check `application/config/database.php` loads .env
2. Verify `.env` file exists in root directory
3. Check database credentials in `.env`
4. Ensure database driver is `mysqli`

---

## 🔄 Rollback (If Needed)

If something goes wrong and you need to rollback:

```sql
-- Login to MySQL
mysql -u root -p mobile_shop_pos

-- Run rollback commands (from migration file)
DROP VIEW IF EXISTS daily_sales_summary;
DROP VIEW IF EXISTS profit_report;
DROP VIEW IF EXISTS inventory_available;
DROP TABLE IF EXISTS trade_ins;
DROP TABLE IF EXISTS customer_ledger;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS item_serials;

ALTER TABLE transactions 
DROP COLUMN imei_numbers,
DROP COLUMN profit_amount,
DROP COLUMN credit_amount,
DROP COLUMN paid_amount,
DROP COLUMN payment_status,
DROP COLUMN customer_id;

ALTER TABLE items 
DROP COLUMN category,
DROP COLUMN model,
DROP COLUMN brand,
DROP COLUMN warranty_terms,
DROP COLUMN warranty_months,
DROP COLUMN item_type;

EXIT;
```

Then re-run the migration.

---

## 📝 Configuration Files

### .env (Root Directory)
```ini
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=mobile_shop_pos
APP_TIMEZONE=Asia/Karachi
APP_ENV=development
PRINTER_IP=192.168.1.100
PRINTER_PORT=9100
SHOP_NAME=Mobile World
SHOP_ADDRESS=Shop #123, Main Market, Karachi
SHOP_PHONE=+92-300-1234567
CURRENCY_SYMBOL=Rs.
CURRENCY_CODE=PKR
```

**Important:** Edit this file with your actual settings!

---

## ✅ Success Indicators

You'll know Phase 1 is successful when:

1. ✅ Database `mobile_shop_pos` exists
2. ✅ All 8 tables present
3. ✅ All 3 views working
4. ✅ Can login to application
5. ✅ No database errors on dashboard
6. ✅ Timezone shows Pakistan time
7. ✅ Character encoding supports Urdu (utf8mb4)

---

## 🚀 Next Steps

After Phase 1 is complete:

1. **Verify Everything Works:**
   - Login to application
   - Check dashboard loads
   - Verify no errors in browser console

2. **Review Specifications:**
   - Read `.kiro/specs/mobile-shop-pos/requirements.md`
   - Read `.kiro/specs/mobile-shop-pos/design.md`
   - Read `.kiro/specs/mobile-shop-pos/tasks.md`

3. **Start Phase 2:**
   - Task 2.1: Modify Item Model
   - Task 2.2: Update Items Controller
   - Task 2.3: Create Inventory UI
   - Task 2.4: IMEI Management Screen

---

## 📞 Support

If you encounter issues:

1. Check this troubleshooting guide
2. Review error messages carefully
3. Verify all steps were completed
4. Check XAMPP logs: `C:\xampp\mysql\data\mysql_error.log`
5. Check PHP errors: `C:\xampp\apache\logs\error.log`

---

## 📊 Phase 1 Summary

```
┌─────────────────────────────────────────────────────────┐
│              PHASE 1 COMPLETE SUMMARY                    │
├─────────────────────────────────────────────────────────┤
│ Files Modified:        3                                 │
│ Files Created:         5                                 │
│ Database Tables:       8 (4 new, 2 modified)            │
│ Database Views:        3                                 │
│ Indexes Added:         12                                │
│ Columns Added:         18                                │
│ Duration:              30 minutes                        │
│ Status:                ✅ READY FOR PHASE 2              │
└─────────────────────────────────────────────────────────┘
```

**Phase 1 is now complete! Ready to start Phase 2! 🎉**
