<?php
/**
 * Diagnostic Check Script
 * Run this file to verify your Mobile Shop POS system configuration
 */

echo "<h1>Mobile Shop POS - System Diagnostic</h1>";
echo "<hr>";

// Check PHP Version
echo "<h2>1. PHP Version</h2>";
echo "Current PHP Version: " . phpversion();
echo (version_compare(phpversion(), '5.6.0', '>=')) ? " ✅ OK" : " ❌ Too old (need 5.6+)";
echo "<br><br>";

// Check Required Extensions
echo "<h2>2. Required PHP Extensions</h2>";
$required_extensions = ['mysqli', 'mbstring', 'json', 'session'];
foreach ($required_extensions as $ext) {
    echo "$ext: ";
    echo extension_loaded($ext) ? "✅ Loaded" : "❌ NOT LOADED";
    echo "<br>";
}
echo "<br>";

// Check File Permissions
echo "<h2>3. File Permissions</h2>";
$writable_dirs = [
    'application/cache',
    'system/sessions'
];

foreach ($writable_dirs as $dir) {
    echo "$dir: ";
    if (file_exists($dir)) {
        echo is_writable($dir) ? "✅ Writable" : "❌ NOT Writable";
    } else {
        echo "❌ Directory does not exist";
    }
    echo "<br>";
}
echo "<br>";

// Check Critical Files
echo "<h2>4. Critical Files</h2>";
$critical_files = [
    'application/config/currency.php',
    'application/helpers/currency_helper.php',
    'application/config/database.php',
    'application/config/config.php',
    '.htaccess'
];

foreach ($critical_files as $file) {
    echo "$file: ";
    echo file_exists($file) ? "✅ Exists" : "❌ Missing";
    echo "<br>";
}
echo "<br>";

// Check Database Connection
echo "<h2>5. Database Connection</h2>";
if (file_exists('application/config/database.php')) {
    require_once('application/config/database.php');
    
    $hostname = isset($db['default']['hostname']) ? $db['default']['hostname'] : 'Not set';
    $database = isset($db['default']['database']) ? $db['default']['database'] : 'Not set';
    $username = isset($db['default']['username']) ? $db['default']['username'] : 'Not set';
    
    echo "Hostname: $hostname<br>";
    echo "Database: $database<br>";
    echo "Username: $username<br>";
    
    // Try to connect
    if (extension_loaded('mysqli')) {
        $password = isset($db['default']['password']) ? $db['default']['password'] : '';
        $conn = @mysqli_connect($hostname, $username, $password, $database);
        
        if ($conn) {
            echo "Connection: ✅ Successful<br>";
            
            // Check for required views
            echo "<br><h3>Database Views:</h3>";
            $views = ['inventory_available', 'profit_report'];
            foreach ($views as $view) {
                $result = mysqli_query($conn, "SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_$database = '$view'");
                echo "$view: ";
                echo ($result && mysqli_num_rows($result) > 0) ? "✅ Exists" : "❌ Missing (Run create_database_views.sql)";
                echo "<br>";
            }
            
            mysqli_close($conn);
        } else {
            echo "Connection: ❌ Failed - " . mysqli_connect_error() . "<br>";
        }
    } else {
        echo "Cannot test connection: mysqli extension not loaded<br>";
    }
} else {
    echo "❌ Database config file not found<br>";
}
echo "<br>";

// Check mod_rewrite
echo "<h2>6. Apache Configuration</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite: ";
    echo in_array('mod_rewrite', $modules) ? "✅ Enabled" : "❌ Disabled (Enable in Apache)";
} else {
    echo "Cannot detect Apache modules (might be running under different SAPI)";
}
echo "<br><br>";

// Summary
echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>If all checks show ✅, your system should be ready to use.</p>";
echo "<p>If you see any ❌, please fix those issues before proceeding.</p>";
echo "<p><strong>Next Step:</strong> If database views are missing, run: <code>mysql -u root -p your_database < create_database_views.sql</code></p>";
?>
