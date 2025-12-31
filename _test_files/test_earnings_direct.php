<?php
/**
 * Direct test of earnings API
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load CodeIgniter
define('BASEPATH', true);
require_once('../index.php');

echo "<h1>Direct Earnings Test</h1>";
echo "<style>body{font-family:Arial;padding:20px;} pre{background:#f5f5f5;padding:10px;overflow:auto;}</style>";

// Get CI instance
$CI =& get_instance();

echo "<h2>Test 1: Call earningsGraph directly</h2>";

try {
    // Load required models
    $CI->load->model('genmod');
    
    // Call the method
    ob_start();
    $result = $CI->dashboard->earningsGraph('', true);
    $output = ob_get_clean();
    
    echo "<h3>Result:</h3>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    echo "<h3>JSON Encoded:</h3>";
    $json = json_encode($result);
    echo "<pre>" . htmlspecialchars($json) . "</pre>";
    
    if ($output) {
        echo "<h3>Extra Output (This is the problem!):</h3>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Test 2: Check for PHP errors/warnings</h2>";

// Capture any output
ob_start();
$CI->dashboard->earningsGraph();
$captured = ob_get_clean();

echo "<h3>Captured Output:</h3>";
echo "<p>Length: " . strlen($captured) . " bytes</p>";

if (strlen($captured) > 0) {
    echo "<h4>First 1000 characters:</h4>";
    echo "<pre>" . htmlspecialchars(substr($captured, 0, 1000)) . "</pre>";
    
    // Check if it's valid JSON
    $decoded = json_decode($captured);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p style='color:green'>✓ Valid JSON</p>";
    } else {
        echo "<p style='color:red'>✗ Invalid JSON: " . json_last_error_msg() . "</p>";
        
        // Find what's before the JSON
        $json_start = strpos($captured, '{');
        if ($json_start > 0) {
            echo "<h4>Extra content before JSON:</h4>";
            echo "<pre>" . htmlspecialchars(substr($captured, 0, $json_start)) . "</pre>";
        }
    }
}

echo "<h2>Test 3: Check getYearEarnings</h2>";

try {
    $earnings = $CI->genmod->getYearEarnings(date('Y'));
    echo "<h3>Earnings Data:</h3>";
    if ($earnings) {
        echo "<p>Found " . count($earnings) . " records</p>";
        echo "<pre>" . print_r($earnings, true) . "</pre>";
    } else {
        echo "<p>No earnings data found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
