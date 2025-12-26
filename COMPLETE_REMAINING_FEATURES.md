# Complete Remaining Features - Implementation Guide

## Overview

Yeh features incomplete hain aur inhe complete karna hai:
1. Thermal Printer (testing & implementation)
2. PDF Export for reports
3. Barcode Scanner integration
4. Customer Debit tracking

---

## Priority 1: Thermal Printer (4-6 hours)

### Current Status
- ✅ Library installed (`mike42/escpos-php`)
- ✅ File exists (`application/libraries/Thermal_printer.php`)
- ❌ Methods NOT implemented
- ❌ NOT tested with hardware

### Implementation Steps

#### Step 1: Complete Thermal_printer.php
File: `application/libraries/Thermal_printer.php`

Add these methods:
- `printReceipt($transactionData)` - Main print function
- `formatHeader()` - Shop info header
- `formatItems($items)` - Item list
- `formatFooter()` - Thank you message
- `handleError()` - Printer offline handling

#### Step 2: Configure Printer
File: `.env`

Add:
```
PRINTER_TYPE=network
PRINTER_IP=192.168.1.100
PRINTER_PORT=9100
```

#### Step 3: Test with Hardware
1. Connect thermal printer (USB or Network)
2. Test print from Transactions controller
3. Verify receipt format
4. Test error handling

---

## Priority 2: PDF Export (2-3 hours)

### Current Status
- ❌ NO PDF library installed
- ❌ Export buttons exist but don't work

### Implementation Steps

#### Step 1: Install PDF Library
```bash
composer require tecnickcom/tcpdf
```

#### Step 2: Create PDF Library
File: `application/libraries/Pdf_generator.php`

Methods needed:
- `generateReceipt($transactionData)`
- `generateProfitReport($reportData)`
- `generateInventoryReport($inventoryData)`

#### Step 3: Add Export Buttons
Update these files:
- `application/views/reports/profit_daily.php`
- `application/views/reports/profit_monthly.php`
- `application/views/reports/stock_value.php`

Add button:
```html
<button onclick="exportToPDF()">Export to PDF</button>
```

---

## Priority 3: Barcode Scanner (2-3 hours)

### Current Status
- ❌ NO scanner integration
- ❌ NO JavaScript listener

### Implementation Steps

#### Step 1: Create Barcode JS
File: `public/js/barcode_scanner.js`

```javascript
var barcodeBuffer = '';
var barcodeTimeout;

$(document).on('keypress', function(e) {
    if (e.which === 13 && barcodeBuffer.length > 0) {
        handleBarcodeScanned(barcodeBuffer);
        barcodeBuffer = '';
        return;
    }
    
    clearTimeout(barcodeTimeout);
    barcodeBuffer += String.fromCharCode(e.which);
    
    barcodeTimeout = setTimeout(function() {
        barcodeBuffer = '';
    }, 100);
});

function handleBarcodeScanned(barcode) {
    // Play beep
    new Audio(appRoot + 'public/sounds/beep.mp3').play();
    
    // Search
    $('#itemSearch').val(barcode).trigger('input');
}
```

#### Step 2: Add Beep Sound
1. Download beep.mp3
2. Place in `public/sounds/beep.mp3`

#### Step 3: Include in Views
Add to `application/views/items/items.php`:
```html
<script src="<?=base_url()?>public/js/barcode_scanner.js"></script>
```

---

## Priority 4: Customer Debit (2-3 hours)

### Current Status
- ✅ Database supports negative balance
- ❌ UI doesn't show "shop owes customer"
- ❌ Refund functionality missing

### Implementation Steps

#### Step 1: Update Customer Model
File: `application/models/Customer.php`

Add method:
```php
public function recordDebit($customerId, $amount, $notes) {
    // When shop owes customer
    $this->db->trans_start();
    
    // Update balance (negative)
    $this->db->set('current_balance', 'current_balance - ' . $amount, FALSE);
    $this->db->where('id', $customerId);
    $this->db->update('customers');
    
    // Add ledger entry
    $ledgerData = [
        'customer_id' => $customerId,
        'transaction_type' => 'debit',
        'amount' => $amount,
        'description' => $notes
    ];
    $this->db->insert('customer_ledger', $ledgerData);
    
    $this->db->trans_complete();
    return $this->db->trans_status();
}
```

#### Step 2: Update Customers Controller
File: `application/controllers/Customers.php`

Add method:
```php
public function recordDebit() {
    $this->genlib->ajaxOnly();
    
    $customerId = $this->input->post('customerId', TRUE);
    $amount = $this->input->post('amount', TRUE);
    $notes = $this->input->post('notes', TRUE);
    
    if ($this->customer->recordDebit($customerId, $amount, $notes)) {
        $json['status'] = 1;
        $json['msg'] = "Debit recorded successfully";
    } else {
        $json['status'] = 0;
        $json['msg'] = "Failed to record debit";
    }
    
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
}
```

#### Step 3: Update Ledger View
File: `application/views/customers/ledger.php`

Add button:
```html
<button class="btn btn-warning" onclick="recordDebit()">
    Record Debit (Shop Owes)
</button>
```

Update balance display:
```php
<?php if ($customer->current_balance > 0): ?>
    <span class="text-danger">Customer Owes: Rs. <?=number_format($customer->current_balance, 2)?></span>
<?php elseif ($customer->current_balance < 0): ?>
    <span class="text-success">Shop Owes: Rs. <?=number_format(abs($customer->current_balance), 2)?></span>
<?php else: ?>
    <span class="text-muted">Settled</span>
<?php endif; ?>
```

---

## Implementation Timeline

### Week 1 (Day 1-2)
- [ ] Complete thermal printer implementation
- [ ] Test with hardware
- [ ] Fix any issues

### Week 1 (Day 3-4)
- [ ] Install PDF library
- [ ] Implement PDF generation
- [ ] Add export buttons
- [ ] Test PDF output

### Week 1 (Day 5)
- [ ] Implement barcode scanner
- [ ] Add beep sound
- [ ] Test with scanner hardware

### Week 2 (Day 1-2)
- [ ] Implement customer debit
- [ ] Update UI
- [ ] Test refund scenarios

---

## Testing Checklist

### Thermal Printer
- [ ] Prints receipt correctly
- [ ] Shows IMEI numbers
- [ ] Shows warranty info
- [ ] Handles printer offline
- [ ] Format is readable

### PDF Export
- [ ] Generates PDF correctly
- [ ] All data included
- [ ] Format is professional
- [ ] Download works
- [ ] Print from PDF works

### Barcode Scanner
- [ ] Detects scanner input
- [ ] Plays beep sound
- [ ] Searches correctly
- [ ] Adds to cart
- [ ] Works with different scanners

### Customer Debit
- [ ] Records debit correctly
- [ ] Updates balance (negative)
- [ ] Shows in ledger
- [ ] UI displays correctly
- [ ] Refunds work

---

## Files to Create/Modify

### New Files
- `public/js/barcode_scanner.js`
- `public/sounds/beep.mp3`
- `application/libraries/Pdf_generator.php`

### Modify Files
- `application/libraries/Thermal_printer.php`
- `application/models/Customer.php`
- `application/controllers/Customers.php`
- `application/views/customers/ledger.php`
- `application/views/reports/*.php` (add export buttons)

---

## Next Steps

1. **Load test data** first (see TEST_DATA_GUIDE.md)
2. **Test current features** to understand flow
3. **Implement missing features** one by one
4. **Test each feature** thoroughly
5. **Document** any issues

---

**Total Time**: 10-15 hours  
**Priority**: High  
**Status**: Ready to implement
