# Mobile Shop POS - Design Specification

## 1. System Architecture

### 1.1 Architecture Overview
```
┌─────────────────────────────────────────────────────────┐
│                    Presentation Layer                    │
│  (Views: HTML/CSS/JS + Bootstrap + jQuery)              │
└─────────────────────────────────────────────────────────┘
                           ↕
┌─────────────────────────────────────────────────────────┐
│                   Application Layer                      │
│  (Controllers: CodeIgniter MVC)                         │
│  - Items Controller                                      │
│  - Transactions Controller                               │
│  - Customers Controller                                  │
│  - Reports Controller                                    │
└─────────────────────────────────────────────────────────┘
                           ↕
┌─────────────────────────────────────────────────────────┐
│                    Business Logic Layer                  │
│  (Models: Item, Transaction, Customer, Genmod)         │
└─────────────────────────────────────────────────────────┘
                           ↕
┌─────────────────────────────────────────────────────────┐
│                      Data Layer                          │
│  (MySQL Database via mysqli driver)                     │
└─────────────────────────────────────────────────────────┘
                           ↕
┌─────────────────────────────────────────────────────────┐
│                  External Integrations                   │
│  - Thermal Printer (ESC/POS via mike42/escpos-php)     │
│  - Barcode Scanner (USB HID)                            │
└─────────────────────────────────────────────────────────┘
```

### 1.2 Technology Stack
- **Backend Framework**: CodeIgniter 3.1.x
- **Database**: MySQL 5.7+ (mysqli driver)
- **Frontend**: Bootstrap 3.x, jQuery 3.x, Font Awesome
- **Printing**: mike42/escpos-php
- **Server**: Apache 2.4+ with mod_rewrite

---

## 2. Database Design

### 2.1 Entity Relationship Diagram
```
┌──────────────┐         ┌──────────────────┐
│    admin     │         │      items       │
│──────────────│         │──────────────────│
│ id (PK)      │         │ id (PK)          │
│ first_name   │         │ name             │
│ last_name    │         │ code (UNIQUE)    │
│ email        │         │ brand            │
│ role         │         │ model            │
└──────────────┘         │ category         │
                         │ item_type        │
                         │ unitPrice        │
                         │ quantity         │
                         │ warranty_months  │
                         └──────────────────┘
                                  │
                                  │ 1:N
                                  ↓
                         ┌──────────────────┐
                         │  item_serials    │
                         │──────────────────│
                         │ id (PK)          │
                         │ item_id (FK)     │
                         │ imei_number      │
                         │ color            │
                         │ cost_price       │
                         │ selling_price    │
                         │ status           │
                         └──────────────────┘
```



### 2.2 Table Schemas

#### items (Modified)
```sql
CREATE TABLE items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    code VARCHAR(50) NOT NULL UNIQUE,
    brand VARCHAR(50) NULL,
    model VARCHAR(50) NULL,
    category ENUM('mobile', 'accessory', 'other') DEFAULT 'other',
    item_type ENUM('standard', 'serialized') DEFAULT 'standard',
    unitPrice DECIMAL(10,2) NOT NULL,
    quantity INT(6) NOT NULL,
    warranty_months INT DEFAULT 0,
    warranty_terms VARCHAR(200) NULL,
    description TEXT,
    dateAdded DATETIME NOT NULL,
    lastUpdated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_item_type (item_type),
    INDEX idx_category (category)
);
```

#### item_serials (New)
```sql
CREATE TABLE item_serials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id BIGINT UNSIGNED NOT NULL,
    imei_number VARCHAR(20) UNIQUE NOT NULL,
    serial_number VARCHAR(50) NULL,
    color VARCHAR(30) NULL,
    storage VARCHAR(20) NULL,
    cost_price DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NULL,
    status ENUM('available', 'sold', 'returned', 'traded_in', 'defective') DEFAULT 'available',
    sold_transaction_id BIGINT UNSIGNED NULL,
    sold_date DATETIME NULL,
    purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_item_id (item_id),
    INDEX idx_imei (imei_number),
    INDEX idx_status (status)
);
```

#### customers (New)
```sql
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100) NULL,
    cnic VARCHAR(20) NULL,
    address TEXT NULL,
    current_balance DECIMAL(12,2) SIGNED DEFAULT 0.00,
    credit_limit DECIMAL(10,2) DEFAULT 0.00,
    total_purchases DECIMAL(12,2) DEFAULT 0.00,
    total_payments DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('active', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone),
    INDEX idx_status (status)
);
```

---

## 3. Component Design

### 3.1 Inventory Management Module

#### 3.1.1 Add Item Flow
```
User Action → Controller → Validation → Model → Database
     ↓
Item Type Check
     ↓
Standard? → Insert item with quantity
     ↓
Serialized? → Insert item + Loop IMEI → Insert item_serials
```

#### 3.1.2 Item Model Methods
```php
class Item extends CI_Model {
    // Existing methods (keep)
    public function getAll($orderBy, $orderFormat, $start, $limit)
    public function add($itemName, $itemQuantity, $itemPrice, $itemDescription, $itemCode)
    
    // New methods (add)
    public function addWithType($data) // Handles both types
    public function addSerialNumber($itemId, $imeiData) // Bulk insert IMEIs
    public function getAvailableSerials($itemId) // Get available IMEIs
    public function lockSerial($imeiNumber) // Reserve IMEI for cart
    public function releaseSerial($imeiNumber) // Release IMEI from cart
    public function markSerialSold($imeiNumber, $transactionId) // Mark as sold
    public function getSerialInfo($imeiNumber) // Get IMEI details
}
```

---

### 3.2 POS Transaction Module

#### 3.2.1 Transaction Flow
```
1. Search Product (IMEI or Name)
   ↓
2. Add to Cart (Lock IMEI if serialized)
   ↓
3. Apply Discount/VAT
   ↓
4. Select Payment Method
   ↓
5. Process Trade-In (if applicable)
   ↓
6. Calculate Profit
   ↓
7. Save Transaction
   ↓
8. Update Inventory
   ↓
9. Update Customer Balance (if credit)
   ↓
10. Generate Receipt
   ↓
11. Print Receipt
```

#### 3.2.2 Transaction Controller Methods
```php
class Transactions extends CI_Controller {
    // Existing methods (modify)
    public function nso_() // Modify to handle IMEI and profit
    
    // New methods (add)
    public function searchByImei() // AJAX: Search IMEI
    public function addToCart() // AJAX: Add item to session cart
    public function removeFromCart() // AJAX: Remove item from cart
    public function processTradeIn() // Handle trade-in logic
    public function calculateProfit($cartItems) // Calculate total profit
    public function lockCartItems($cartItems) // Lock all IMEIs in cart
    public function releaseCartItems($cartItems) // Release IMEIs if cancelled
}
```

---

### 3.3 Customer Credit (Khata) Module

#### 3.3.1 Credit Sale Flow
```
1. Select Customer (or create new)
   ↓
2. Display Current Balance
   ↓
3. Check Credit Limit
   ↓
4. Enter Amount Paid
   ↓
5. Calculate Credit Amount = Total - Paid
   ↓
6. Update Customer Balance
   ↓
7. Create Ledger Entry
   ↓
8. Save Transaction with payment_status
```

#### 3.3.2 Customer Model Methods
```php
class Customer extends CI_Model {
    public function add($name, $phone, $email, $cnic, $address, $creditLimit)
    public function getAll($orderBy, $orderFormat, $start, $limit)
    public function getByPhone($phone)
    public function updateBalance($customerId, $amount, $type) // type: add/subtract
    public function getLedger($customerId, $limit)
    public function recordPayment($customerId, $amount, $paymentMethod, $staffId)
    public function checkCreditLimit($customerId, $amount)
}
```

---

## 4. User Interface Design

### 4.1 Inventory Screen Wireframe
```
┌─────────────────────────────────────────────────────────┐
│ [+ Add Item] [Search: ________] [Filter: All ▼]        │
├─────────────────────────────────────────────────────────┤
│ Code  │ Name      │ Type   │ Qty │ Price  │ Actions   │
├─────────────────────────────────────────────────────────┤
│ IP13  │ iPhone 13 │ Serial │ 5   │ 250000 │ [Edit][Del]│
│ SAMCH │ Charger   │ Std    │ 50  │ 1500   │ [Edit][Del]│
└─────────────────────────────────────────────────────────┘
```

### 4.2 Add Item Modal (Serialized)
```
┌─────────────────────────────────────────────────────────┐
│                    Add New Item                          │
├─────────────────────────────────────────────────────────┤
│ Item Name: [________________]                           │
│ Code:      [________________]                           │
│ Brand:     [________________]                           │
│ Model:     [________________]                           │
│ Category:  [Mobile ▼]                                   │
│ Type:      [● Serialized  ○ Standard]                   │
│ Price:     [________________]                           │
│ Warranty:  [12] months                                  │
│                                                          │
│ IMEI Numbers:                                           │
│ ┌────────────────────────────────────────────────────┐ │
│ │ IMEI: [_______________] Color: [_____] Cost: [___]│ │
│ │ IMEI: [_______________] Color: [_____] Cost: [___]│ │
│ │ [+ Add More]                                       │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ [Cancel] [Save Item]                                    │
└─────────────────────────────────────────────────────────┘
```

### 4.3 POS Screen Wireframe
```
┌─────────────────────────────────────────────────────────┐
│ Search: [Scan IMEI or Product Name_______] [🔍]        │
├──────────────────────────────┬──────────────────────────┤
│ CART                         │ CUSTOMER INFO            │
│ ┌──────────────────────────┐ │ Name: [_______________] │
│ │Item      │Qty│Price│Total│ │ Phone:[_______________] │
│ ├──────────────────────────┤ │ Balance: Rs. 0          │
│ │iPhone 13 │ 1 │250000│250k│ │ [Select Customer]       │
│ │IMEI:12345│   │     │    │ │                          │
│ │[Remove]  │   │     │    │ │ PAYMENT                  │
│ └──────────────────────────┘ │ Subtotal: Rs. 250,000   │
│                              │ Discount: [0]%           │
│ [Trade-In Mode]              │ VAT:      [0]%           │
│                              │ Total:    Rs. 250,000    │
│                              │                          │
│                              │ Method: [Cash ▼]         │
│                              │ Paid:   [___________]    │
│                              │ Change: Rs. 0            │
│                              │                          │
│                              │ [Process Sale]           │
└──────────────────────────────┴──────────────────────────┘
```

---

## 5. API Design

### 5.1 AJAX Endpoints

#### Search by IMEI
```
GET /transactions/searchByImei?imei=123456789012345
Response: {
    "status": 1,
    "item": {
        "name": "iPhone 13 Pro",
        "imei": "123456789012345",
        "color": "Graphite",
        "price": 250000,
        "cost_price": 230000,
        "warranty": 12
    }
}
```

#### Add to Cart
```
POST /transactions/addToCart
Body: {
    "item_id": 1,
    "imei": "123456789012345",
    "quantity": 1,
    "price": 250000
}
Response: {
    "status": 1,
    "cart": [...],
    "cart_total": 250000
}
```

#### Process Sale
```
POST /transactions/nso_
Body: {
    "_aoi": [...items],
    "_mop": "Cash",
    "_at": 250000,
    "customer_id": 5,
    "paid_amount": 200000,
    "trade_in": {...}
}
Response: {
    "status": 1,
    "transReceipt": "...",
    "ref": "ABC123"
}
```

---

## 6. Security Design

### 6.1 Authentication Flow
```
Login → Validate Credentials → Create Session → Set Role
                                      ↓
                              Check on Each Request
                                      ↓
                              Valid? → Allow Access
                              Invalid? → Redirect to Login
```

### 6.2 Authorization Matrix
| Feature | Super Admin | Sales Staff |
|---------|-------------|-------------|
| Add Item | ✓ | ✓ |
| Edit Item | ✓ | ✓ |
| Delete Item | ✓ | ✗ |
| Process Sale | ✓ | ✓ |
| View Reports | ✓ | Own only |
| Manage Users | ✓ | ✗ |
| Delete Transaction | ✓ | ✗ |

---

## 7. Performance Optimization

### 7.1 Database Indexing Strategy
```sql
-- Critical indexes for performance
CREATE INDEX idx_imei ON item_serials(imei_number);
CREATE INDEX idx_status ON item_serials(status);
CREATE INDEX idx_item_type ON items(item_type);
CREATE INDEX idx_trans_date ON transactions(transDate);
CREATE INDEX idx_customer_balance ON customers(current_balance);
```

### 7.2 Caching Strategy
- Session-based cart storage (no database writes until checkout)
- View caching for reports (5-minute TTL)
- Database query result caching for inventory list

---

## 8. Error Handling

### 8.1 Error Categories
1. **Validation Errors**: User input errors (display inline)
2. **Business Logic Errors**: IMEI already sold, credit limit exceeded
3. **System Errors**: Database connection, printer offline
4. **Security Errors**: Unauthorized access, session expired

### 8.2 Error Response Format
```json
{
    "status": 0,
    "msg": "User-friendly error message",
    "errors": {
        "field_name": "Specific error"
    },
    "error_code": "ERR_IMEI_DUPLICATE"
}
```

---

## 9. Printing Design

### 9.1 Receipt Layout
```
================================
      MOBILE WORLD
   Shop #123, Main Market
   Karachi, Pakistan
   Ph: +92-300-1234567
================================
Date: 25-12-2024  Time: 14:30
Ref: ABC123456
--------------------------------
iPhone 13 Pro
IMEI: 123456789012345
Color: Graphite
Warranty: 12 months
Price: Rs. 250,000
--------------------------------
Subtotal:    Rs. 250,000
Discount:    Rs. 0
VAT:         Rs. 0
--------------------------------
TOTAL:       Rs. 250,000
Paid:        Rs. 250,000
Change:      Rs. 0
--------------------------------
Payment: Cash
Staff: John Doe
================================
   Thank you for shopping!
   Warranty valid till:
   25-12-2025
================================
```

---

## 10. Testing Strategy

### 10.1 Unit Tests
- Item model methods
- Transaction calculations
- Customer balance updates
- Profit calculations

### 10.2 Integration Tests
- Complete sale flow
- Trade-in processing
- Credit sale with ledger update
- IMEI locking/unlocking

### 10.3 User Acceptance Tests
- Add serialized item with IMEIs
- Process sale with IMEI search
- Handle trade-in transaction
- Record customer payment
- Print receipt

---

**Document Version**: 1.0  
**Last Updated**: 2024-12-25  
**Status**: Ready for Implementation
