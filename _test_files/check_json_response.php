<?php
/**
 * Check what's actually being returned by the API
 */

$url = "http://localhost/mobile-shop-pos/index.php/dashboard/earningsGraph";

echo "<h1>JSON Response Checker</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .error{color:red;background:#ffe0e0;padding:10px;} .success{color:green;background:#e0ffe0;padding:10px;} pre{background:#f5f5f5;padding:10px;overflow:auto;max-height:400px;}</style>";

echo "<h2>Testing: $url</h2>";

// Get response
$response = file_get_contents($url);

echo "<h3>Response Length: " . strlen($response) . " bytes</h3>";

// Show first 500 characters
echo "<h3>First 500 characters:</h3>";
echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";

// Try to decode JSON
$decoded = json_decode($response);
$json_error = json_last_error();

if ($json_error === JSON_ERROR_NONE) {
    echo "<div class='success'><h3>✓ Valid JSON!</h3></div>";
    echo "<h4>Decoded Data:</h4>";
    echo "<pre>" . print_r($decoded, true) . "</pre>";
} else {
    echo "<div class='error'><h3>✗ Invalid JSON!</h3>";
    echo "<p><strong>Error:</strong> " . json_last_error_msg() . "</p></div>";
    
    // Find where JSON starts
    $json_start = strpos($response, '{');
    $json_start_array = strpos($response, '[');
    
    if ($json_start !== false && $json_start > 0) {
        echo "<h4>⚠ Extra content BEFORE JSON (first " . $json_start . " bytes):</h4>";
        $before = substr($response, 0, $json_start);
        echo "<pre>" . htmlspecialchars($before) . "</pre>";
        
        echo "<h4>Hex dump of extra content:</h4>";
        echo "<pre>";
        for ($i = 0; $i < min(strlen($before), 100); $i++) {
            echo sprintf("%02X ", ord($before[$i]));
            if (($i + 1) % 16 == 0) echo "\n";
        }
        echo "</pre>";
        
        // Try to decode from JSON start
        echo "<h4>Trying to decode from position $json_start:</h4>";
        $clean_json = substr($response, $json_start);
        $decoded_clean = json_decode($clean_json);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "<div class='success'><p>✓ JSON is valid after removing extra content!</p></div>";
            echo "<pre>" . print_r($decoded_clean, true) . "</pre>";
        } else {
            echo "<p class='error'>Still invalid: " . json_last_error_msg() . "</p>";
        }
    }
    
    // Check for common issues
    echo "<h4>Common Issues Check:</h4>";
    echo "<ul>";
    
    // BOM check
    if (substr($response, 0, 3) === "\xEF\xBB\xBF") {
        echo "<li class='error'>⚠ UTF-8 BOM detected at start!</li>";
    } else {
        echo "<li>✓ No BOM</li>";
    }
    
    // Whitespace check
    if (preg_match('/^\s+/', $response)) {
        echo "<li class='error'>⚠ Whitespace at start!</li>";
    } else {
        echo "<li>✓ No leading whitespace</li>";
    }
    
    // PHP errors/warnings check
    if (strpos($response, '<br') !== false || strpos($response, 'Warning:') !== false || strpos($response, 'Notice:') !== false) {
        echo "<li class='error'>⚠ PHP errors/warnings in output!</li>";
    } else {
        echo "<li>✓ No PHP errors visible</li>";
    }
    
    echo "</ul>";
}

echo "<h3>Full Response:</h3>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

echo "<hr>";
echo "<h2>Quick Fixes:</h2>";
echo "<ol>";
echo "<li>Check for BOM in PHP files (use editor to save as UTF-8 without BOM)</li>";
echo "<li>Check for whitespace/newlines before &lt;?php tags</li>";
echo "<li>Check for echo/print statements in models/controllers</li>";
echo "<li>Enable error logging instead of display: ini_set('display_errors', 0)</li>";
echo "</ol>";
?>
