# Mobile Shop POS - Requirements Specification

## 1. Project Overview

### 1.1 Project Name
Mobile Shop POS - Specialized Point of Sale System for Mobile Phone Retail

### 1.2 Base System
Refactoring of [Mini-Inventory-and-Sales-Management-System](https://github.com/amirsanni/Mini-Inventory-and-Sales-Management-System)

### 1.3 Business Context
A specialized POS system for mobile phone shops in Pakistan that handles:
- Mobile phones with IMEI tracking
- Accessories with standard inventory
- Trade-in transactions
- Customer credit (Khata) management
- Warranty tracking per device
- Profit calculation per sale

### 1.4 Target Users
- **Shop Owners**: View reports, manage staff, track profits
- **Sales Staff**: Process sales, manage inventory, handle trade-ins
- **Customers**: Receive receipts with warranty information

---

## 2. Functional Requirements

### 2.1 Hybrid Inventory Management

#### AC-INV-001: Support Two Inventory Types
**As a** shop owner  
**I want** to manage both standard and serialized inventory  
**So that** I can track accessories by quantity and phones by IMEI

**Acceptance Criteria:**
- System shall support "Standard" items (accessories, chargers, cases)
- System shall support "Serialized" items (mobile phones with IMEI)
- Each item must have a type field: `standard` or `serialized`
- Standard items track quantity as integer
- Serialized items track individual units by IMEI number
- Available quantity for serialized items = count of available IMEI records

**Priority:** P0 (Critical)  
**Dependencies:** Database schema Phase 1

---

#### AC-INV-002: Add Product with IMEI Numbers
**As a** sales staff  
**I want** to add mobile phones with multiple IMEI numbers  
**So that** each device is tracked individually

**Acceptance Criteria:**
- When adding a serialized item, system shall show IMEI input fields
- User can scan or manually enter IMEI numbers
- Each IMEI must be unique across the system
- Each IMEI record must store: IMEI number, color, cost price, status
- System shall validate IMEI format (15 digits)
- After scanning IMEI, cursor auto-moves to next input field
- User can add unlimited IMEI entries dynamically
- System prevents duplicate IMEI numbers

**Priority:** P0 (Critical)  
**Dependencies:** AC-INV-001

---

#### AC-INV-003: Add Standard Product with Quantity
**As a** sales staff  
**I want** to add accessories with quantity  
**So that** I can track stock levels

**Acceptance Criteria:**
- When adding a standard item, system shall show quantity input field
- Quantity must be a positive integer
- System shall hide IMEI fields for standard items
- Stock updates increment/decrement quantity field
- System prevents negative quantities

**Priority:** P0 (Critical)  
**Dependencies:** AC-INV-001

---

#### AC-INV-004: Product Categorization
**As a** shop owner  
**I want** to categorize products  
**So that** I can organize inventory and generate category reports

**Acceptance Criteria:**
- System shall support categories: Mobile, Accessory, Other
- Each item must have brand and model fields (optional)
- System shall support filtering by category
- Reports can be generated per category

**Priority:** P1 (High)  
**Dependencies:** None

---

#### AC-INV-005: Warranty Management
**As a** shop owner  
**I want** to track warranty terms per product  
**So that** customers know their warranty coverage

**Acceptance Criteria:**
- Each item shall have warranty_months field (default: 0)
- Each item can have warranty_terms text field (optional)
- Warranty information shall appear on receipts
- System calculates warranty expiry date from sale date

**Priority:** P1 (High)  
**Dependencies:** None

---

### 2.2 Smart POS with IMEI Search

#### AC-POS-001: Search Product by IMEI
**As a** sales staff  
**I want** to search products by scanning IMEI  
**So that** I can quickly add phones to cart

**Acceptance Criteria:**
- POS screen shall have IMEI search field
- User can scan or type IMEI number
- System searches item_serials table for exact match
- If found and available, system adds item to cart
- If found but sold, system shows "Already sold" error
- If not found, system shows "IMEI not found" message
- System displays: item name, color, price, warranty
- IMEI is locked (status changed to 'reserved') when added to cart

**Priority:** P0 (Critical)  
**Dependencies:** AC-INV-002

---

#### AC-POS-002: Fallback to Standard Search
**As a** sales staff  
**I want** to search by product name or barcode if IMEI not found  
**So that** I can add standard items to cart

**Acceptance Criteria:**
- If IMEI search fails, system shall search items.name and items.code
- System displays matching products with available quantity
- User can select product and enter quantity to add
- System validates quantity against available stock

**Priority:** P0 (Critical)  
**Dependencies:** AC-POS-001

---

#### AC-POS-003: Cart Management
**As a** sales staff  
**I want** to manage items in cart  
**So that** I can modify order before checkout

**Acceptance Criteria:**
- Cart shall display: item name, IMEI (if serialized), quantity, unit price, total
- User can remove items from cart
- For standard items, user can change quantity
- For serialized items, quantity is always 1
- Cart shows subtotal, discount, VAT, grand total
- Removing serialized item releases IMEI lock

**Priority:** P0 (Critical)  
**Dependencies:** AC-POS-001, AC-POS-002

---

#### AC-POS-004: Apply Discount and VAT
**As a** sales staff  
**I want** to apply discount and VAT  
**So that** I can offer promotions and comply with tax regulations

**Acceptance Criteria:**
- System shall support percentage-based discount
- Discount applies to subtotal before VAT
- System shall support percentage-based VAT
- VAT applies after discount
- Calculation: (Subtotal - Discount) + VAT = Grand Total
- Discount and VAT percentages are configurable

**Priority:** P1 (High)  
**Dependencies:** AC-POS-003

---

### 2.3 Trade-In System

#### AC-TRD-001: Enable Trade-In Mode
**As a** sales staff  
**I want** to enable trade-in mode during checkout  
**So that** I can accept old phones as payment

**Acceptance Criteria:**
- POS screen shall have "Trade-In" toggle button
- When enabled, system shows trade-in form
- Trade-in form captures: brand, model, IMEI (optional), condition, value
- Condition options: Excellent, Good, Fair, Poor
- Trade-in value can be negative (reduces total)
- Trade-in value cannot exceed sale total

**Priority:** P1 (High)  
**Dependencies:** AC-POS-003

---

#### AC-TRD-002: Record Trade-In Transaction
**As a** shop owner  
**I want** trade-in details recorded  
**So that** I can track trade-in inventory and value

**Acceptance Criteria:**
- System shall create record in trade_ins table
- Record includes: transaction_ref, customer_id, brand, model, IMEI, condition, value
- Trade-in value deducted from payment amount
- If trade-in has IMEI, system adds to item_serials with status 'traded_in'
- Trade-in appears on receipt

**Priority:** P1 (High)  
**Dependencies:** AC-TRD-001

---

### 2.4 Customer Credit (Khata) Management

#### AC-KHT-001: Customer Registration
**As a** sales staff  
**I want** to register customers  
**So that** I can track their credit purchases

**Acceptance Criteria:**
- System shall have customer management screen
- Required fields: name, phone (unique)
- Optional fields: email, CNIC, address
- Each customer has current_balance field (default: 0)
- Each customer has credit_limit field (default: 0)
- System prevents duplicate phone numbers

**Priority:** P1 (High)  
**Dependencies:** None

---

#### AC-KHT-002: Credit Sale
**As a** sales staff  
**I want** to process credit sales  
**So that** customers can pay later

**Acceptance Criteria:**
- During checkout, payment method includes "Credit" option
- If credit selected, system shows customer search
- System displays customer's current balance
- User enters amount paid (can be 0 for full credit)
- Remaining amount added to customer's current_balance
- System prevents credit if balance exceeds credit_limit
- Transaction marked with payment_status: 'credit' or 'partial'

**Priority:** P1 (High)  
**Dependencies:** AC-KHT-001

---

#### AC-KHT-003: Customer Ledger
**As a** sales staff  
**I want** to view customer transaction history  
**So that** I can track payments and balance

**Acceptance Criteria:**
- System shall show customer ledger with all transactions
- Ledger displays: date, type (sale/payment/adjustment), amount, balance
- Each entry shows balance_before and balance_after
- User can record payments against customer balance
- Payment reduces current_balance
- System prevents payments exceeding balance

**Priority:** P1 (High)  
**Dependencies:** AC-KHT-002

---

#### AC-KHT-004: Payment Recording
**As a** sales staff  
**I want** to record customer payments  
**So that** I can update their balance

**Acceptance Criteria:**
- System shall have "Record Payment" function
- User selects customer and enters payment amount
- Payment method captured: Cash, Bank Transfer, etc.
- Payment creates entry in customer_ledger
- current_balance decremented by payment amount
- Receipt generated for payment

**Priority:** P1 (High)  
**Dependencies:** AC-KHT-003

---

### 2.5 Profit Tracking

#### AC-PRF-001: Calculate Profit per Sale
**As a** shop owner  
**I want** automatic profit calculation  
**So that** I know margins on each sale

**Acceptance Criteria:**
- For serialized items: profit = selling_price - cost_price (from item_serials)
- For standard items: profit = selling_price - (unitPrice * quantity)
- Total profit = sum of all item profits in transaction
- Profit stored in transactions.profit_amount
- Profit calculation excludes VAT and discount

**Priority:** P1 (High)  
**Dependencies:** AC-POS-003

---

#### AC-PRF-002: Profit Reports
**As a** shop owner  
**I want** to view profit reports  
**So that** I can analyze business performance

**Acceptance Criteria:**
- System shall show daily profit summary
- System shall show monthly profit summary
- System shall show profit by category
- System shall show profit by staff member
- Reports include: total sales, total cost, total profit, profit margin %

**Priority:** P2 (Medium)  
**Dependencies:** AC-PRF-001

---

### 2.6 Thermal Printing

#### AC-PRT-001: Print Receipt with IMEI
**As a** sales staff  
**I want** to print receipts on thermal printer  
**So that** customers receive professional receipts

**Acceptance Criteria:**
- System shall integrate with thermal printer via network
- Receipt includes: shop name, address, phone, date, time
- Receipt lists items with IMEI numbers (if serialized)
- Receipt shows warranty information per item
- Receipt shows payment details and change
- Receipt includes transaction reference number
- Printer IP configurable in .env file

**Priority:** P1 (High)  
**Dependencies:** AC-POS-003

---

#### AC-PRT-002: Print Customer Ledger Statement
**As a** sales staff  
**I want** to print customer statements  
**So that** customers can see their balance

**Acceptance Criteria:**
- System shall print customer ledger on thermal printer
- Statement includes: customer name, phone, current balance
- Statement lists recent transactions (last 10)
- Statement shows total purchases and total payments

**Priority:** P2 (Medium)  
**Dependencies:** AC-KHT-003, AC-PRT-001

---

## 3. Non-Functional Requirements

### 3.1 Performance
- **NFR-001**: System shall load POS screen within 2 seconds
- **NFR-002**: IMEI search shall return results within 500ms
- **NFR-003**: Receipt printing shall complete within 3 seconds
- **NFR-004**: System shall support 1000+ items in inventory
- **NFR-005**: System shall support 10,000+ transactions per year

### 3.2 Security
- **NFR-006**: All passwords shall be hashed using bcrypt
- **NFR-007**: Session timeout shall be 2 hours
- **NFR-008**: Only Super Admin can delete transactions
- **NFR-009**: All database queries shall use prepared statements
- **NFR-010**: IMEI numbers shall be validated before storage

### 3.3 Usability
- **NFR-011**: System shall support barcode scanner input
- **NFR-012**: System shall support keyboard shortcuts for common actions
- **NFR-013**: Error messages shall be clear and actionable
- **NFR-014**: System shall work on 1024x768 minimum resolution
- **NFR-015**: Forms shall validate input in real-time

### 3.4 Reliability
- **NFR-016**: System shall backup database daily
- **NFR-017**: System shall log all critical operations
- **NFR-018**: System shall handle printer offline gracefully
- **NFR-019**: System shall prevent duplicate IMEI sales
- **NFR-020**: System shall maintain data integrity with transactions

### 3.5 Localization
- **NFR-021**: System shall use Asia/Karachi timezone
- **NFR-022**: Currency shall be Pakistani Rupees (Rs.)
- **NFR-023**: Date format shall be DD-MM-YYYY
- **NFR-024**: System shall support Urdu characters (utf8mb4)

---

## 4. Technical Constraints

### 4.1 Technology Stack
- **PHP**: 7.4+ (CodeIgniter 3.x)
- **Database**: MySQL 5.7+ (mysqli driver only, no SQLite)
- **Frontend**: HTML5, CSS3, JavaScript (jQuery), Bootstrap 3
- **Printing**: mike42/escpos-php library
- **Server**: Apache with mod_rewrite enabled

### 4.2 Browser Support
- Chrome 90+
- Firefox 88+
- Edge 90+
- Safari 14+ (limited testing)

### 4.3 Hardware Requirements
- **Server**: 2GB RAM minimum, 4GB recommended
- **Storage**: 10GB minimum
- **Network**: 100Mbps LAN for thermal printer
- **Thermal Printer**: ESC/POS compatible, network-enabled

---

## 5. Data Requirements

### 5.1 Data Migration
- Existing items table shall be modified (not replaced)
- Existing transactions table shall be modified (not replaced)
- Existing admin table remains unchanged
- New tables: item_serials, customers, customer_ledger, trade_ins
- Migration shall be reversible (rollback script provided)

### 5.2 Data Retention
- Transactions: Retain indefinitely
- Event logs: Retain for 2 years
- Customer data: Retain while account active
- IMEI records: Retain indefinitely (even after sale)

### 5.3 Data Backup
- Daily automated backup at 2:00 AM
- Backup retention: 30 days
- Manual backup option available to Super Admin

---

## 6. Integration Requirements

### 6.1 Thermal Printer Integration
- Network-based ESC/POS printer
- IP address configurable via .env
- Fallback to PDF if printer unavailable

### 6.2 Barcode Scanner Integration
- USB HID barcode scanner
- Scanner input treated as keyboard input
- Support for Code 128, EAN-13, UPC-A formats

---

## 7. User Roles and Permissions

### 7.1 Super Admin
- Full system access
- Manage users
- View all reports
- Delete transactions
- Manage system settings

### 7.2 Sales Staff
- Process sales
- Manage inventory (add/edit items)
- View own sales reports
- Record customer payments
- Cannot delete transactions
- Cannot manage users

---

## 8. Success Criteria

### 8.1 Phase 1 Success (Database)
- ✅ All tables created successfully
- ✅ Views functional
- ✅ Sample data inserted
- ✅ Rollback tested

### 8.2 Phase 2 Success (Inventory)
- Can add standard items with quantity
- Can add serialized items with IMEI
- IMEI validation works
- Inventory view shows correct available quantities

### 8.3 Phase 3 Success (POS)
- IMEI search works
- Cart management functional
- Trade-in processing works
- Transactions save correctly

### 8.4 Phase 4 Success (Khata)
- Customer registration works
- Credit sales process correctly
- Ledger displays accurately
- Payments update balance

### 8.5 Phase 5 Success (Reports & Printing)
- Thermal printing works
- Profit reports accurate
- All reports generate correctly

---

## 9. Out of Scope (Future Phases)

- SMS notifications for customers
- Online customer portal
- Multi-branch support
- Supplier management
- Purchase order system
- Barcode label printing
- Mobile app for sales staff
- Integration with accounting software

---

## 10. Assumptions and Dependencies

### 10.1 Assumptions
- Shop has stable internet connection
- Staff are trained on basic computer usage
- Thermal printer is network-enabled
- MySQL database is properly configured
- PHP and Apache are properly installed

### 10.2 Dependencies
- CodeIgniter 3.x framework
- MySQL 5.7+ database
- Apache web server with mod_rewrite
- Composer for dependency management
- mike42/escpos-php library

---

## 11. Risks and Mitigations

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| IMEI duplicate entry | High | Medium | Unique constraint + validation |
| Printer offline during sale | Medium | High | Fallback to PDF receipt |
| Database corruption | High | Low | Daily backups + transaction logs |
| Staff training gap | Medium | Medium | User manual + training sessions |
| Performance with large data | Medium | Medium | Database indexing + optimization |

---

## 12. Glossary

- **IMEI**: International Mobile Equipment Identity (15-digit unique identifier)
- **Khata**: Urdu term for credit ledger/account book
- **Serialized Item**: Product tracked by unique identifier (IMEI)
- **Standard Item**: Product tracked by quantity
- **Trade-In**: Accepting used phone as partial payment
- **ESC/POS**: Standard protocol for thermal printers

---

**Document Version**: 1.0  
**Last Updated**: 2024-12-25  
**Status**: Approved for Design Phase
