<?php
// Quick script to check admin table
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mobile_shop_pos';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, first_name, last_name, email, password, account_status, deleted FROM admin LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. "<br>";
        echo "Name: " . $row["first_name"]. " " . $row["last_name"]. "<br>";
        echo "Email: " . $row["email"]. "<br>";
        echo "Password Hash: " . $row["password"]. "<br>";
        echo "Account Status: " . $row["account_status"]. "<br>";
        echo "Deleted: " . $row["deleted"]. "<br>";
        echo "<br>";
        echo "Password hash length: " . strlen($row["password"]) . "<br>";
        echo "Is bcrypt? " . (substr($row["password"], 0, 4) === '$2y$' ? 'YES' : 'NO') . "<br>";
        echo "<br>";
        echo "Testing password 'admin123':<br>";
        echo "Verify result: " . (password_verify('admin123', $row["password"]) ? 'SUCCESS' : 'FAILED') . "<br>";
    }
} else {
    echo "No admin users found!";
}

$conn->close();
?>
