<?php
// Direct login test - run this in browser: http://localhost/mobile-shop-pos/test_login.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mobile_shop_pos';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Database Connection: OK</h2>";

// Check admin table structure
$result = $conn->query("DESCRIBE admin");
echo "<h3>Admin Table Structure:</h3><pre>";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}
echo "</pre>";

// Check admin data
$result = $conn->query("SELECT * FROM admin WHERE email = 'admin@shop.com'");
echo "<h3>Admin Data:</h3><pre>";
if ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "Email: " . $row['email'] . "\n";
    echo "Password Hash: " . $row['password'] . "\n";
    echo "Password Length: " . strlen($row['password']) . "\n";
    
    $storedPassword = $row['password'];
    $testPassword = 'admin123';
    
    echo "\n<h3>Password Tests:</h3>";
    echo "Testing password: " . $testPassword . "\n\n";
    
    // Test MD5
    $md5Hash = md5($testPassword);
    echo "MD5 of 'admin123': " . $md5Hash . "\n";
    echo "MD5 Match: " . ($md5Hash === $storedPassword ? "YES ✓" : "NO ✗") . "\n\n";
    
    // Test bcrypt
    echo "Bcrypt verify: " . (password_verify($testPassword, $storedPassword) ? "YES ✓" : "NO ✗") . "\n\n";
    
    // Check if it's MD5 format (32 chars hex)
    if (strlen($storedPassword) == 32 && ctype_xdigit($storedPassword)) {
        echo "Password appears to be MD5 hash\n";
        
        // Try common passwords
        $commonPasswords = ['admin123', 'admin', 'password', '123456', 'admin@123', '12345678'];
        echo "\nTrying common passwords:\n";
        foreach ($commonPasswords as $pwd) {
            if (md5($pwd) === $storedPassword) {
                echo "FOUND! Password is: " . $pwd . " ✓✓✓\n";
                break;
            }
        }
    }
    
    // Generate new bcrypt hash
    echo "\n<h3>New Bcrypt Hash for 'admin123':</h3>";
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    echo $newHash . "\n";
    
    echo "\n<h3>SQL to fix password:</h3>";
    echo "<code>UPDATE admin SET password = '" . $newHash . "' WHERE email = 'admin@shop.com';</code>\n";
    
} else {
    echo "No admin found with email admin@shop.com\n";
    
    // Show all admins
    $result2 = $conn->query("SELECT id, email, first_name FROM admin");
    echo "\nAll admins in database:\n";
    while ($row2 = $result2->fetch_assoc()) {
        echo "- " . $row2['email'] . " (" . $row2['first_name'] . ")\n";
    }
}
echo "</pre>";

$conn->close();
?>
