<?php
/**
 * Test Dashboard API Endpoints
 */

echo "<h1>Dashboard API Test</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:10px;}</style>";

$base_url = "http://localhost/mobile-shop-pos/";

// Test URLs
$tests = [
    'Earnings Graph' => $base_url . 'index.php/dashboard/earningsGraph',
    'Payment Method Chart' => $base_url . 'index.php/dashboard/paymentMethodChart',
    'Total Earned Today' => $base_url . 'index.php/misc/totalearnedtoday',
];

echo "<h2>Testing API Endpoints:</h2>";

foreach ($tests as $name => $url) {
    echo "<h3>$name</h3>";
    echo "<p>URL: <code>$url</code></p>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    
    curl_close($ch);
    
    echo "<p><strong>HTTP Code:</strong> ";
    if ($http_code == 200) {
        echo "<span class='success'>$http_code OK</span>";
    } else {
        echo "<span class='error'>$http_code ERROR</span>";
    }
    echo "</p>";
    
    echo "<details><summary>Response Headers</summary><pre>" . htmlspecialchars($headers) . "</pre></details>";
    
    echo "<details><summary>Response Body (first 500 chars)</summary><pre>";
    echo htmlspecialchars(substr($body, 0, 500));
    echo "</pre></details>";
    
    // Check if it's JSON
    $json = json_decode($body);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p class='success'>✓ Valid JSON Response</p>";
        echo "<details><summary>Parsed JSON</summary><pre>" . print_r($json, true) . "</pre></details>";
    } else {
        echo "<p class='error'>✗ Not JSON - " . json_last_error_msg() . "</p>";
        if (strpos($body, 'XAMPP') !== false) {
            echo "<p class='error'><strong>⚠ XAMPP Welcome Page Detected!</strong></p>";
            echo "<p>This means the URL is not reaching the controller.</p>";
        }
    }
    
    echo "<hr>";
}

echo "<h2>Debugging Info:</h2>";
echo "<p><strong>Base URL:</strong> $base_url</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// Check mod_rewrite
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p class='success'>✓ mod_rewrite is ENABLED</p>";
    } else {
        echo "<p class='error'>✗ mod_rewrite is DISABLED</p>";
        echo "<p>Enable it in httpd.conf: LoadModule rewrite_module modules/mod_rewrite.so</p>";
    }
} else {
    echo "<p>⚠ Cannot check mod_rewrite status (not Apache or function disabled)</p>";
}

// Check .htaccess
if (file_exists('../.htaccess')) {
    echo "<p class='success'>✓ .htaccess file exists</p>";
    echo "<details><summary>View .htaccess</summary><pre>" . htmlspecialchars(file_get_contents('../.htaccess')) . "</pre></details>";
} else {
    echo "<p class='error'>✗ .htaccess file NOT found</p>";
}

echo "<h2>Quick Fixes:</h2>";
echo "<ol>";
echo "<li>If mod_rewrite is disabled, enable it in XAMPP Control Panel → Apache Config → httpd.conf</li>";
echo "<li>Uncomment: <code>LoadModule rewrite_module modules/mod_rewrite.so</code></li>";
echo "<li>Restart Apache</li>";
echo "<li>If still not working, set <code>\$config['index_page'] = 'index.php';</code> in config.php</li>";
echo "</ol>";
?>
