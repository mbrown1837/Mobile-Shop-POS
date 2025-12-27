# Currency System Usage Guide

## Overview
The Mobile Shop POS system now uses a centralized currency configuration system for Pakistani Rupees (Rs.).

## Configuration

### Currency Settings
File: `application/config/currency.php`

```php
$config['currency_symbol'] = 'Rs.';           // Currency symbol
$config['currency_code'] = 'PKR';             // ISO currency code
$config['currency_decimals'] = 2;             // Decimal places
$config['currency_decimal_separator'] = '.';  // Decimal separator
$config['currency_thousands_separator'] = ','; // Thousands separator
$config['currency_symbol_position'] = 'before'; // Symbol position
```

## Helper Functions

### 1. currency_symbol()
Returns the configured currency symbol.

```php
echo currency_symbol(); 
// Output: Rs.
```

### 2. format_currency($amount, $include_symbol = TRUE)
Formats a number as currency with proper separators.

```php
echo format_currency(1500);
// Output: Rs. 1,500.00

echo format_currency(1500, FALSE);
// Output: 1,500.00

echo format_currency(999999.99);
// Output: Rs. 999,999.99
```

## Usage in Views

### Display Currency Symbol
```php
<span><?= currency_symbol() ?></span>
```

### Display Formatted Price
```php
<td><?= format_currency($item->unitPrice) ?></td>
```

### Display Amount Without Symbol
```php
<input type="text" value="<?= format_currency($amount, FALSE) ?>">
```

## Usage in Controllers

```php
// Load the helper (already auto-loaded)
$this->load->helper('currency');

// Get symbol
$symbol = currency_symbol();

// Format amount
$formatted = format_currency($total_amount);

// Pass to view
$data['total'] = format_currency($total);
$this->load->view('sales', $data);
```

## Changing Currency

To change to a different currency (e.g., US Dollar):

1. Edit `application/config/currency.php`:
```php
$config['currency_symbol'] = '$';
$config['currency_code'] = 'USD';
```

2. No code changes needed - all views will automatically use the new symbol!

## Examples

### Sales Receipt
```php
<tr>
    <td>Item Price:</td>
    <td><?= format_currency($item->unitPrice) ?></td>
</tr>
<tr>
    <td>Quantity:</td>
    <td><?= $item->quantity ?></td>
</tr>
<tr>
    <td><strong>Total:</strong></td>
    <td><strong><?= format_currency($item->unitPrice * $item->quantity) ?></strong></td>
</tr>
```

### Inventory List
```php
<?php foreach ($items as $item): ?>
<tr>
    <td><?= $item->name ?></td>
    <td><?= format_currency($item->unitPrice) ?></td>
    <td><?= $item->quantity ?></td>
    <td><?= format_currency($item->unitPrice * $item->quantity) ?></td>
</tr>
<?php endforeach; ?>
```

## Benefits

1. **Centralized**: Change currency in one place
2. **Consistent**: All prices display the same way
3. **Flexible**: Easy to switch currencies
4. **Professional**: Proper number formatting with separators
5. **Maintainable**: No hardcoded currency symbols in code
