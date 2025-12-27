<?php
/**
 * Transaction Processing Test & Debug Script
 * Test this file by accessing: http://localhost/mobile-shop-pos/test_transaction.php
 */

// Load CodeIgniter
require_once('index.php');

// Get CI instance
$CI =& get_instance();

echo "<h2>Transaction Processing Debug Test</h2>";
echo "<hr>";

// Test 1: Check if session is active
echo "<h3>Test 1: Session Check</h3>";
if ($CI->session->userdata('admin_id')) {
    echo "✅ Session Active - Admin ID: " . $CI->session->userdata('admin_id') . "<br>";
    echo "✅ Admin Name: " . $CI->session->userdata('admin_name') . "<br>";
} else {
    echo "❌ No active session. Please login first.<br>";
    echo "<a href='index.php/home'>Go to Login</a><br>";
    exit;
}

echo "<hr>";

// Test 2: Check cart items
echo "<h3>Test 2: Cart Items Check</h3>";
$CI->load->library('genlib');
$cartItems = $CI->genlib->getCartItems();

if (empty($cartItems)) {
    echo "⚠️ Cart is empty<br>";
    echo "<p>Please add items to cart first from Transactions page</p>";
} else {
    echo "✅ Cart has " . count($cartItems) . " item(s)<br>";
    echo "<pre>";
    print_r($cartItems);
    echo "</pre>";
}

echo "<hr>";

// Test 3: Check cart totals calculation
echo "<h3>Test 3: Cart Totals Calculation</h3>";
if (!empty($cartItems)) {
    try {
        $totals = $CI->genlib->calculateCartTotals(0, 0);
        echo "✅ Totals calculated successfully<br>";
        echo "<pre>";
        print_r($totals);
        echo "</pre>";
    } catch (Exception $e) {
        echo "❌ Error calculating totals: " . $e->getMessage() . "<br>";
    }
} else {
    echo "⚠️ Skipped (cart is empty)<br>";
}

echo "<hr>";

// Test 4: Check models
echo "<h3>Test 4: Models Check</h3>";
$CI->load->model(['transaction', 'item', 'customer']);

if (method_exists($CI->transaction, 'addMobileShopTransaction')) {
    echo "✅ Transaction model loaded<br>";
} else {
    echo "❌ Transaction model method missing<br>";
}

if (method_exists($CI->item, 'getItemInfo')) {
    echo "✅ Item model loaded<br>";
} else {
    echo "❌ Item model method missing<br>";
}

if (method_exists($CI->customer, 'getById')) {
    echo "✅ Customer model loaded<br>";
} else {
    echo "❌ Customer model method missing<br>";
}

echo "<hr>";

// Test 5: Simulate transaction data
echo "<h3>Test 5: Simulate Transaction Processing</h3>";
if (!empty($cartItems)) {
    echo "<h4>Transaction Data:</h4>";
    
    $testData = [
        'payment_method' => 'cash',
        'discount_percent' => 0,
        'vat_percent' => 0,
        'amount_tendered' => 10000,
        'customer_id' => null,
        'cart_items' => count($cartItems)
    ];
    
    echo "<pre>";
    print_r($testData);
    echo "</pre>";
    
    echo "<p><strong>To test actual transaction:</strong></p>";
    echo "<ol>";
    echo "<li>Go to Transactions page</li>";
    echo "<li>Add items to cart</li>";
    echo "<li>Select payment method: Cash</li>";
    echo "<li>Enter amount tendered</li>";
    echo "<li>Click 'Complete Transaction'</li>";
    echo "</ol>";
} else {
    echo "⚠️ Cannot simulate (cart is empty)<br>";
}

echo "<hr>";

// Test 6: Check database tables
echo "<h3>Test 6: Database Tables Check</h3>";
$tables = ['items', 'item_serials', 'transactions', 'customers'];

foreach ($tables as $table) {
    $query = $CI->db->query("SHOW TABLES LIKE '$table'");
    if ($query->num_rows() > 0) {
        $count = $CI->db->count_all($table);
        echo "✅ Table '$table' exists ($count records)<br>";
    } else {
        echo "❌ Table '$table' not found<br>";
    }
}

echo "<hr>";

// Test 7: Check for PHP errors
echo "<h3>Test 7: PHP Configuration</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Error Reporting: " . ini_get('display_errors') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "s<br>";

echo "<hr>";

// Test 8: Manual transaction test
echo "<h3>Test 8: Manual Transaction Test</h3>";
if (!empty($cartItems)) {
    echo "<form method='POST' action='index.php/transactions/processTransaction' style='border: 1px solid #ccc; padding: 20px; background: #f9f9f9;'>";
    echo "<h4>Test Transaction Form</h4>";
    echo "<input type='hidden' name='cart_items' value='" . htmlspecialchars(json_encode($cartItems)) . "'>";
    echo "<label>Payment Method:</label><br>";
    echo "<select name='payment_method' required style='width: 100%; padding: 5px; margin-bottom: 10px;'>";
    echo "<option value='cash'>Cash</option>";
    echo "<option value='pos'>POS/Card</option>";
    echo "</select><br>";
    echo "<label>Amount Tendered:</label><br>";
    echo "<input type='number' name='amount_tendered' value='10000' required style='width: 100%; padding: 5px; margin-bottom: 10px;'><br>";
    echo "<label>Discount %:</label><br>";
    echo "<input type='number' name='discount_percent' value='0' style='width: 100%; padding: 5px; margin-bottom: 10px;'><br>";
    echo "<label>VAT %:</label><br>";
    echo "<input type='number' name='vat_percent' value='0' style='width: 100%; padding: 5px; margin-bottom: 10px;'><br>";
    echo "<button type='submit' style='background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer;'>Process Test Transaction</button>";
    echo "</form>";
} else {
    echo "<p>⚠️ Add items to cart first from <a href='index.php/transactions'>Transactions page</a></p>";
}

echo "<hr>";
echo "<p><a href='index.php/transactions'>← Back to Transactions</a></p>";
?>
