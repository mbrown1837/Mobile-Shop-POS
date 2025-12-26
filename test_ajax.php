<?php
// Simple test to check if PHP and AJAX are working
header('Content-Type: application/json');

echo json_encode([
    'status' => 1,
    'message' => 'PHP and AJAX are working!',
    'timestamp' => date('Y-m-d H:i:s')
]);
