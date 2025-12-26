<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: $password\n";
echo "New Hash: $hash\n";
echo "Verify: " . (password_verify($password, $hash) ? 'SUCCESS ✓' : 'FAILED ✗') . "\n";
echo "\nSQL UPDATE command:\n";
echo "UPDATE admin SET password = '$hash' WHERE email = 'admin@shop.com';\n";
?>
