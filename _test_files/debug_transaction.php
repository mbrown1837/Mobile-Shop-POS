<?php
/**
 * Direct Transaction Debug Script (Standalone)
 * Access: http://localhost/mobile-shop-pos/debug_transaction.php
 */

// Start session
session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mobile_shop_pos';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaction Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .test { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        h3 { color: #555; margin-top: 0; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table td, table th { padding: 8px; border: 1px solid #ddd; text-align: left; }
        table th { background: #007bff; color: white; }
    </style>
</head>
<body>

<h2>🔍 Transaction Processing Debug</h2>

<?php
// Test 1: Session Check
echo '<div class="test">';
echo '<h3>Test 1: Session Check</h3>';
if (isset($_SESSION['admin_id'])) {
    echo '<p class="success">✅ Session Active</p>';
    echo '<p>Admin ID: ' . $_SESSION['admin_id'] . '</p>';
    echo '<p>Admin Name: ' . ($_SESSION['admin_name'] ?? 'N/A') . '</p>';
} else {
    echo '<p class="error">❌ No Session Found</p>';
    echo '<p>Please <a href="index.php/home">login first</a></p>';
}
echo '</div>';

// Test 2: Database Tables
echo '<div class="test">';
echo '<h3>Test 2: Database Tables</h3>';
$tables = ['items', 'item_serials', 'transactions', 'customers', 'admin'];
echo '<table>';
echo '<tr><th>Table</th><th>Status</th><th>Records</th></tr>';
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        $count = $conn->query("SELECT COUNT(*) as cnt FROM $table")->fetch_assoc()['cnt'];
        echo "<tr><td>$table</td><td class='success'>✅ Exists</td><td>$count</td></tr>";
    } else {
        echo "<tr><td>$table</td><td class='error'>❌ Missing</td><td>-</td></tr>";
    }
}
echo '</table>';
echo '</div>';

// Test 3: Check Items
echo '<div class="test">';
echo '<h3>Test 3: Available Items</h3>';
$items = $conn->query("SELECT id, name, code, unitPrice, item_type, quantity FROM items LIMIT 5");
if ($items->num_rows > 0) {
    echo '<p class="success">✅ Found ' . $items->num_rows . ' items</p>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Name</th><th>Code</th><th>Price</th><th>Type</th><th>Qty</th></tr>';
    while ($item = $items->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $item['id'] . '</td>';
        echo '<td>' . $item['name'] . '</td>';
        echo '<td>' . $item['code'] . '</td>';
        echo '<td>Rs. ' . number_format($item['unitPrice'], 2) . '</td>';
        echo '<td>' . ($item['item_type'] ?? 'standard') . '</td>';
        echo '<td>' . ($item['quantity'] ?? 0) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p class="warning">⚠️ No items found</p>';
}
echo '</div>';

// Test 4: Check Customers
echo '<div class="test">';
echo '<h3>Test 4: Customers</h3>';
$customers = $conn->query("SELECT COUNT(*) as cnt FROM customers")->fetch_assoc()['cnt'];
echo '<p>Total Customers: ' . $customers . '</p>';
if ($customers > 0) {
    $recent = $conn->query("SELECT id, name, phone, credit_limit, current_balance FROM customers LIMIT 3");
    echo '<table>';
    echo '<tr><th>ID</th><th>Name</th><th>Phone</th><th>Credit Limit</th><th>Balance</th></tr>';
    while ($cust = $recent->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $cust['id'] . '</td>';
        echo '<td>' . $cust['name'] . '</td>';
        echo '<td>' . $cust['phone'] . '</td>';
        echo '<td>Rs. ' . number_format($cust['credit_limit'], 2) . '</td>';
        echo '<td>Rs. ' . number_format($cust['current_balance'], 2) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
echo '</div>';

// Test 5: Check Recent Transactions
echo '<div class="test">';
echo '<h3>Test 5: Recent Transactions</h3>';
$trans = $conn->query("SELECT COUNT(*) as cnt FROM transactions")->fetch_assoc()['cnt'];
echo '<p>Total Transactions: ' . $trans . '</p>';
if ($trans > 0) {
    $recent = $conn->query("SELECT ref, itemName, quantity, totalMoneySpent, modeOfPayment, transDate FROM transactions ORDER BY transDate DESC LIMIT 5");
    echo '<table>';
    echo '<tr><th>Ref</th><th>Item</th><th>Qty</th><th>Amount</th><th>Payment</th><th>Date</th></tr>';
    while ($t = $recent->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $t['ref'] . '</td>';
        echo '<td>' . $t['itemName'] . '</td>';
        echo '<td>' . $t['quantity'] . '</td>';
        echo '<td>Rs. ' . number_format($t['totalMoneySpent'], 2) . '</td>';
        echo '<td>' . $t['modeOfPayment'] . '</td>';
        echo '<td>' . date('Y-m-d H:i', strtotime($t['transDate'])) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
echo '</div>';

// Test 6: PHP Configuration
echo '<div class="test">';
echo '<h3>Test 6: PHP Configuration</h3>';
echo '<table>';
echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
echo '<tr><td>Display Errors</td><td>' . ini_get('display_errors') . '</td></tr>';
echo '<tr><td>Error Reporting</td><td>' . error_reporting() . '</td></tr>';
echo '<tr><td>Max Execution Time</td><td>' . ini_get('max_execution_time') . 's</td></tr>';
echo '<tr><td>Memory Limit</td><td>' . ini_get('memory_limit') . '</td></tr>';
echo '</table>';
echo '</div>';

// Test 7: Session Data
echo '<div class="test">';
echo '<h3>Test 7: Session Data</h3>';
if (!empty($_SESSION)) {
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
} else {
    echo '<p class="warning">⚠️ No session data</p>';
}
echo '</div>';

$conn->close();
?>

<div class="test">
    <h3>Next Steps</h3>
    <ol>
        <li>If session is not active, <a href="index.php/home">login first</a></li>
        <li>Go to <a href="index.php/transactions">Transactions page</a></li>
        <li>Add items to cart</li>
        <li>Try to complete transaction</li>
        <li>Check browser console for errors</li>
    </ol>
</div>

<div class="test">
    <p><a href="index.php/dashboard">← Back to Dashboard</a> | <a href="index.php/transactions">Go to Transactions →</a></p>
</div>

</body>
</html>
