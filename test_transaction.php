<?php
// Test transaction processing - run this: http://localhost/mobile-shop-pos/test_transaction.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Set fake admin session for testing
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'admin@shop.com';
$_SESSION['admin_role'] = 'Super';

// Load CodeIgniter
define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('ENVIRONMENT', 'development');

require_once BASEPATH . '../index.php';

echo "<h2>Testing Transaction Processing</h2>";

// Get CI instance
$CI =& get_instance();

echo "<h3>1. Check if processTransaction method exists</h3>";
if (method_exists($CI->transactions, 'processTransaction')) {
    echo "✓ Method exists<br>";
} else {
    echo "✗ Method does NOT exist<br>";
}

echo "<h3>2. Check required models</h3>";
$CI->load->model(['transaction', 'item', 'customer']);

echo "Transaction model: ";
echo class_exists('Transaction') ? "✓ Loaded<br>" : "✗ Not loaded<br>";

echo "Item model: ";
echo class_exists('Item') ? "✓ Loaded<br>" : "✗ Not loaded<br>";

echo "Customer model: ";
echo class_exists('Customer') ? "✓ Loaded<br>" : "✗ Not loaded<br>";

echo "<h3>3. Check database tables</h3>";
$tables = ['transactions', 'items', 'item_serials', 'customers', 'customer_ledger'];
foreach ($tables as $table) {
    $exists = $CI->db->table_exists($table);
    echo "$table: " . ($exists ? "✓ Exists<br>" : "✗ Missing<br>");
}

echo "<h3>4. Try to call processTransaction with empty cart</h3>";
try {
    // Simulate AJAX request
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    $_POST = [];
    
    ob_start();
    $CI->transactions->processTransaction();
    $output = ob_get_clean();
    
    echo "<pre>Response: " . htmlspecialchars($output) . "</pre>";
    
    $response = json_decode($output, true);
    if ($response && isset($response['status'])) {
        echo "Status: " . $response['status'] . "<br>";
        echo "Message: " . $response['msg'] . "<br>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>ERROR: " . $e->getMessage() . "</span><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h3>5. Check PHP errors in log</h3>";
$logPath = APPPATH . 'logs/';
if (is_dir($logPath)) {
    $files = glob($logPath . 'log-*.php');
    if (!empty($files)) {
        $latestLog = end($files);
        echo "Latest log: " . basename($latestLog) . "<br>";
        echo "<textarea style='width:100%;height:200px'>" . file_get_contents($latestLog) . "</textarea>";
    } else {
        echo "No log files found<br>";
    }
} else {
    echo "Logs directory not found<br>";
}
?>
