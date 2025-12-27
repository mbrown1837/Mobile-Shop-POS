# Quick Implementation Guide - Missing Features

## 1. Barcode Scanner Integration (2-3 hours)

### Step 1: Add JavaScript Listener
```javascript
// Add to public/js/items.js or pos.js
$(document).ready(function() {
    var barcodeBuffer = '';
    var barcodeTimeout;
    
    $(document).on('keypress', function(e) {
        // Scanner sends Enter after barcode
        if (e.which === 13 && barcodeBuffer.length > 0) {
            handleBarcodeScanned(barcodeBuffer);
            barcodeBuffer = '';
            return;
        }
        
        // Build barcode string
        clearTimeout(barcodeTimeout);
        barcodeBuffer += String.fromCharCode(e.which);
        
        // Reset after 100ms (scanner is faster than typing)
        barcodeTimeout = setTimeout(function() {
            barcodeBuffer = '';
        }, 100);
    });
});

function handleBarcodeScanned(barcode) {
    // Play beep sound
    new Audio(appRoot + 'public/sounds/beep.mp3').play();
    
    // Search for item
    $('#itemSearch').val(barcode).trigger('input');
}
```

### Step 2: Add Beep Sound
- Download beep.mp3
- Place in `public/sounds/beep.mp3`

**Done!** Scanner will work automatically.

---

## 2. Professional Print System (4-6 hours)

### Step 1: Create Receipt Template
Create `application/views/transactions/receipt_print.php`

### Step 2: Add Print CSS
Create `public/css/print.css` with thermal printer styles

### Step 3: Add Print Function
```javascript
function printReceipt(transactionRef) {
    $.ajax({
        url: appRoot + 'transactions/getReceipt',
        data: {ref: transactionRef},
        success: function(html) {
            var printWindow = window.open('', '', 'width=300,height=600');
            printWindow.document.write(html);
            printWindow.print();
        }
    });
}
```

---

## 3. Customer Debit Tracking (2-3 hours)

### Step 1: Allow Negative Balance
Update `customers` table - already supports negative values!

### Step 2: Add Debit Recording
```php
// In Customers controller
public function recordDebit() {
    // When shop owes customer (refund, advance payment)
    $this->customer->recordDebit($customerId, $amount, $notes);
}
```

### Step 3: Update Ledger View
Show positive balance = customer owes shop (green)
Show negative balance = shop owes customer (red)

**Done!** Debit tracking complete.

---

## Files to Create/Modify

### For Barcode Scanner:
- `public/js/barcode.js` (new)
- `public/sounds/beep.mp3` (new)

### For Print:
- `application/views/transactions/receipt_print.php` (new)
- `public/css/print.css` (new)
- `application/controllers/Transactions.php` (modify)

### For Debit:
- `application/models/Customer.php` (modify)
- `application/controllers/Customers.php` (modify)
- `application/views/customers/ledger.php` (modify)

---

## Testing

1. **Barcode Scanner:** Scan any barcode, should search automatically
2. **Print:** Click print button, should open print dialog
3. **Debit:** Record refund, balance should go negative

Total time: 8-12 hours for all three features!
