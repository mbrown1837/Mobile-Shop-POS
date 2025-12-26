# Test Data Guide - Mobile Shop POS

## Overview

Complete test database with realistic data to understand:
- How data flows through the system
- How UI displays information
- How reports are generated
- How customer credit (Khata) works

---

## Test Data Summary

### 👥 Users (3)
- **Admin** - admin@shop.com (Super Admin)
- **Ali Ahmed** - ali@shop.com (Manager)
- **Sara Khan** - sara@shop.com (Staff)
- Password for all: `admin123`

### 👨‍💼 Customers (5)
1. **Ahmed Hassan** - Rs. 15,000 balance (owes shop)
2. **Fatima Ali** - Rs. 0 balance (paid in full)
3. **Hassan Raza** - Rs. 25,000 balance (owes shop)
4. **Ayesha Khan** - Rs. 0 balance (new customer)
5. **Bilal Ahmed** - Rs. 5,000 balance (owes shop)

### 📱 Items

#### Standard Items (Accessories) - 5 types
- Samsung Fast Charger (50 units)
- iPhone Lightning Cable (100 units)
- Wireless Earbuds (30 units)
- Phone Cases (200 units)
- Screen Protectors (150 units)

#### Serialized Items (Mobiles) - 5 models, 28 IMEI numbers
- iPhone 13 Pro Max (5 units) - 4 available, 1 sold
- Samsung S23 Ultra (4 units) - 3 available, 1 sold
- iPhone 14 (6 units) - 5 available, 1 sold
- Samsung A54 (8 units) - 7 available, 1 sold
- Xiaomi 13 Pro (5 units) - 4 available, 1 sold

### 💰 Transactions (8)
- 5 mobile sales (with IMEI tracking)
- 3 accessory sales
- Mix of: Cash, POS, Credit, Partial payments
- Total sales: Rs. 999,500
- Total profit: Rs. 76,900

### 📊 Customer Ledger
- 9 ledger entries
- Credit sales tracked
- Payments recorded
- Balance calculations

### 🔄 Trade-ins (2)
- Samsung S21 traded in
- iPhone 11 traded in

---

## How to Load Test Data

### Method 1: Command Line
```bash
# Navigate to project root
cd C:\Users\Administrator\Desktop\Mini-Inventory-and-Sales-Management-System

# Load all test data
mysql -u root -p mobile_shop_pos < database/LOAD_TEST_DATA.sql
```

### Method 2: phpMyAdmin
1. Open phpMyAdmin
2. Select `mobile_shop_pos` database
3. Go to "Import" tab
4. Choose file: `database/LOAD_TEST_DATA.sql`
5. Click "Go"

### Method 3: Individual Files
Load in order:
1. `test_data_part1.sql` - Users & Customers
2. `test_data_part2.sql` - Items
3. `test_data_part3.sql` - IMEI Numbers
4. `test_data_part4.sql` - Transactions
5. `test_data_part5.sql` - Ledger & Updates

---

## What You'll See After Loading

### 1. Dashboard
- **Total Sales Today**: 3 items
- **Total Transactions**: 8
- **Items in Stock**: 23 available IMEIs + accessories
- **Profit graphs** with real data

### 2. Items Page
**Search Examples:**
- Search "iPhone" → Shows all iPhone models
- Search "351234567890123" → Shows iPhone 13 Pro Max
- Search "Samsung" → Shows Samsung items
- Search "ACC" → Shows all accessories

**Filters:**
- Category: Mobile (5 models)
- Category: Accessory (5 types)
- Sort by price, quantity, name

### 3. Customers Page
**Customer List:**
- Ahmed Hassan - Rs. 15,000 owed (red)
- Hassan Raza - Rs. 25,000 owed (red)
- Bilal Ahmed - Rs. 5,000 owed (red)
- Others - Rs. 0 (green)

**Ledger View:**
- Click "View Ledger" on any customer
- See all transactions
- See all payments
- See current balance

### 4. Transactions Page (POS)
**Test Scenarios:**
- Search IMEI: `351234567890123` → iPhone 13 Pro Max
- Search Code: `ACC20241227001` → Samsung Charger
- Add to cart
- Select customer
- Choose payment method
- Complete sale

### 5. Reports

#### Daily Profit Report
- Date: 2024-12-27
- Total Sales: Rs. 239,500
- Total Profit: Rs. 21,900
- Transactions: 4

#### Monthly Profit Report
- Month: December 2024
- Total Sales: Rs. 999,500
- Total Profit: Rs. 76,900
- Transactions: 8
- By category, staff, items

#### Inventory Reports
- Low Stock: None (all well stocked)
- Stock Value: Rs. 8,000,000+
- IMEI Status: 23 available, 5 sold

---

## Test Scenarios

### Scenario 1: Cash Sale
1. Login as Admin
2. Go to Transactions (POS)
3. Search IMEI: `351234567890123`
4. Add to cart
5. Payment method: Cash
6. Amount: Rs. 285,000
7. Complete transaction
8. View receipt

### Scenario 2: Credit Sale
1. Search IMEI: `352345678901234`
2. Add to cart
3. Select customer: Ahmed Hassan
4. Payment method: Credit
5. Check credit limit
6. Complete transaction
7. View customer ledger

### Scenario 3: Partial Payment
1. Search IMEI: `353456789012345`
2. Add to cart
3. Select customer: Hassan Raza
4. Payment method: Partial
5. Paid now: Rs. 150,000
6. Credit: Rs. 45,000
7. Complete transaction

### Scenario 4: Accessory Sale
1. Search code: `ACC20241227001`
2. Quantity: 5
3. Add to cart
4. Payment: Cash
5. Complete transaction

### Scenario 5: Trade-in
1. Add phone to cart
2. Click "Add Trade-in"
3. Enter old phone details
4. Trade-in value deducted
5. Complete transaction

### Scenario 6: Payment Recording
1. Go to Customers
2. Select Ahmed Hassan
3. Click "Record Payment"
4. Amount: Rs. 10,000
5. Method: Cash
6. Submit
7. Balance updated

---

## Data Relationships

### How Data Connects:

```
CUSTOMERS
    ↓
CUSTOMER_LEDGER (tracks all credit/payments)
    ↓
TRANSACTIONS (sales records)
    ↓
ITEM_SERIALS (IMEI tracking)
    ↓
ITEMS (product catalog)
```

### Example Flow:
1. Customer "Ahmed Hassan" buys Samsung S23
2. Transaction created with IMEI
3. IMEI marked as "sold"
4. Customer ledger entry created
5. Customer balance updated
6. Profit calculated and stored

---

## Understanding the UI

### Items Page
- **Standard items**: Show quantity
- **Serialized items**: Show "View IMEIs" button
- **Search**: Works across name, code, IMEI, brand, model
- **Filters**: Category, sort options

### Customer Ledger
- **Green balance**: Customer paid in full
- **Red balance**: Customer owes money
- **Ledger entries**: All transactions and payments
- **Payment button**: Record new payment

### Reports
- **Daily**: Today's profit breakdown
- **Monthly**: Full month analysis
- **Category**: Profit by mobile/accessory
- **Staff**: Performance by salesperson

---

## Verification Queries

### Check Data Loaded:
```sql
-- Count records
SELECT 'Customers' as Table_Name, COUNT(*) as Count FROM customers
UNION ALL SELECT 'Items', COUNT(*) FROM items
UNION ALL SELECT 'IMEIs', COUNT(*) FROM item_serials
UNION ALL SELECT 'Transactions', COUNT(*) FROM transactions;

-- Check balances
SELECT name, current_balance, credit_limit 
FROM customers 
WHERE current_balance > 0;

-- Check available IMEIs
SELECT i.name, COUNT(*) as available_units
FROM items i
JOIN item_serials s ON i.id = s.item_id
WHERE s.status = 'available'
GROUP BY i.id;

-- Check profit
SELECT SUM(profit_amount) as total_profit
FROM transactions;
```

---

## Next Steps

1. **Load test data** using one of the methods above
2. **Login** with admin@shop.com / admin123
3. **Explore** each module:
   - Dashboard
   - Items
   - Customers
   - Transactions
   - Reports
4. **Test scenarios** listed above
5. **Understand** data flow and relationships

---

## Troubleshooting

### If data doesn't load:
1. Check database exists: `mobile_shop_pos`
2. Check tables exist: Run `SHOW TABLES;`
3. Clear old data first: Run part1.sql (has TRUNCATE)
4. Load files in order (part1 → part5)

### If IMEIs don't show:
1. Check `item_serials` table has data
2. Check `item_type` = 'serialized' in items table
3. Check status = 'available' for unsold items

### If balances wrong:
1. Check `customer_ledger` entries
2. Verify `current_balance` in customers table
3. Run balance calculation query

---

**Test Data Version**: 1.0  
**Created**: 2024-12-27  
**Status**: Ready to use
