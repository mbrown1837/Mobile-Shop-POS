<?php
$hash = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy';
$password = 'admin123';

echo "Testing password: $password\n";
echo "Against hash: $hash\n";
echo "Result: " . (password_verify($password, $hash) ? 'SUCCESS ✓' : 'FAILED ✗') . "\n";
?>
