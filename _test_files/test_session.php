<?php
/**
 * Test Session Configuration
 */

echo "<h1>Session Test</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;}</style>";

// Start session
session_start();

echo "<h2>Session Info:</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Save Path:</strong> " . session_save_path() . "</p>";
echo "<p><strong>Session Name:</strong> " . session_name() . "</p>";

// Check if writable
$save_path = session_save_path();
if (is_writable($save_path)) {
    echo "<p class='success'>✓ Session path is writable</p>";
} else {
    echo "<p class='error'>✗ Session path is NOT writable</p>";
}

// Test session write
$_SESSION['test'] = 'Hello World';
$_SESSION['timestamp'] = time();

echo "<h2>Session Data:</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

// Load CodeIgniter and check
echo "<h2>CodeIgniter Session Test:</h2>";

require_once('../index.php');

$CI =& get_instance();

echo "<p><strong>CI Session Library:</strong> ";
if (isset($CI->session)) {
    echo "<span class='success'>✓ Loaded</span></p>";
    
    // Check admin session
    echo "<p><strong>Admin ID in Session:</strong> ";
    if (!empty($_SESSION['admin_id'])) {
        echo "<span class='success'>✓ " . $_SESSION['admin_id'] . "</span></p>";
        echo "<p><strong>Admin Email:</strong> " . ($_SESSION['admin_email'] ?? 'Not set') . "</p>";
        echo "<p><strong>Admin Role:</strong> " . ($_SESSION['admin_role'] ?? 'Not set') . "</p>";
    } else {
        echo "<span class='error'>✗ NOT SET (You are not logged in)</span></p>";
    }
} else {
    echo "<span class='error'>✗ Not loaded</span></p>";
}

echo "<h2>All Session Variables:</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h2>Session Files:</h2>";
$files = glob($save_path . '/ci_session*');
if ($files) {
    echo "<p>Found " . count($files) . " session files</p>";
    echo "<ul>";
    foreach (array_slice($files, 0, 5) as $file) {
        $age = time() - filemtime($file);
        echo "<li>" . basename($file) . " (Age: " . $age . " seconds)</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='error'>No session files found</p>";
}
?>
