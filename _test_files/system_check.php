<?php
/**
 * System Check Script
 * Run this to identify issues in the POS system
 */

// Load CodeIgniter
require_once('../index.php');

echo "<h1>Mobile Shop POS - System Check</h1>";
echo "<style>
    body { font-family: Arial; padding: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    h2 { background: #f0f0f0; padding: 10px; }
</style>";

// 1. Database Connection Check
echo "<div class='section'>";
echo "<h2>1. Database Connection</h2>";
try {
    $CI =& get_instance();
    $CI->load->database();
    
    if ($CI->db->conn_id) {
        echo "<p class='success'>✓ Database connected successfully</p>";
        echo "<p>Database: " . $CI->db->database . "</p>";
        
        // Check tables
        $tables = $CI->db->list_tables();
        echo "<p>Total Tables: " . count($tables) . "</p>";
        echo "<details><summary>View Tables</summary><ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul></details>";
    } else {
        echo "<p class='error'>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 2. Check Admin Users
echo "<div class='section'>";
echo "<h2>2. Admin Users</h2>";
try {
    $query = $CI->db->get('admin');
    if ($query->num_rows() > 0) {
        echo "<p class='success'>✓ Admin users found: " . $query->num_rows() . "</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
        foreach ($query->result() as $admin) {
            echo "<tr>";
            echo "<td>{$admin->id}</td>";
            echo "<td>{$admin->first_name} {$admin->last_name}</td>";
            echo "<td>{$admin->email}</td>";
            echo "<td>{$admin->role}</td>";
            echo "<td>" . ($admin->status ? 'Active' : 'Inactive') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ No admin users found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 3. Check Items
echo "<div class='section'>";
echo "<h2>3. Inventory Items</h2>";
try {
    $query = $CI->db->get('items');
    echo "<p class='success'>✓ Total Items: " . $query->num_rows() . "</p>";
    
    // Count by type
    $CI->db->where('item_type', 'standard');
    $standard = $CI->db->count_all_results('items');
    
    $CI->db->where('item_type', 'serialized');
    $serialized = $CI->db->count_all_results('items');
    
    echo "<p>Standard Items: $standard</p>";
    echo "<p>Serialized Items: $serialized</p>";
    
    // Check IMEI count
    $imei_count = $CI->db->count_all('item_serials');
    echo "<p>Total IMEIs: $imei_count</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 4. Check Customers
echo "<div class='section'>";
echo "<h2>4. Customers</h2>";
try {
    $query = $CI->db->get('customers');
    echo "<p class='success'>✓ Total Customers: " . $query->num_rows() . "</p>";
    
    $CI->db->where('status', 'active');
    $active = $CI->db->count_all_results('customers');
    echo "<p>Active Customers: $active</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 5. Check Transactions
echo "<div class='section'>";
echo "<h2>5. Transactions</h2>";
try {
    $query = $CI->db->get('transactions');
    echo "<p class='success'>✓ Total Transactions: " . $query->num_rows() . "</p>";
    
    // Get unique refs
    $CI->db->distinct();
    $CI->db->select('ref');
    $refs = $CI->db->get('transactions');
    echo "<p>Unique Sales: " . $refs->num_rows() . "</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 6. Check File Permissions
echo "<div class='section'>";
echo "<h2>6. File Permissions</h2>";
$dirs_to_check = [
    'application/cache',
    'application/logs',
    'system/sessions'
];

foreach ($dirs_to_check as $dir) {
    $path = FCPATH . $dir;
    if (is_dir($path)) {
        if (is_writable($path)) {
            echo "<p class='success'>✓ $dir is writable</p>";
        } else {
            echo "<p class='error'>✗ $dir is NOT writable</p>";
        }
    } else {
        echo "<p class='warning'>⚠ $dir does not exist</p>";
    }
}
echo "</div>";

// 7. Check PHP Configuration
echo "<div class='section'>";
echo "<h2>7. PHP Configuration</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Memory Limit: " . ini_get('memory_limit') . "</p>";
echo "<p>Max Upload Size: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>Max POST Size: " . ini_get('post_max_size') . "</p>";
echo "<p>Session Save Path: " . session_save_path() . "</p>";
echo "</div>";

// 8. Check Routes
echo "<div class='section'>";
echo "<h2>8. Important URLs</h2>";
echo "<ul>";
echo "<li><a href='" . base_url() . "'>Home/Login</a></li>";
echo "<li><a href='" . base_url('dashboard') . "'>Dashboard</a></li>";
echo "<li><a href='" . base_url('transactions') . "'>Transactions (POS)</a></li>";
echo "<li><a href='" . base_url('items') . "'>Items</a></li>";
echo "<li><a href='" . base_url('customers') . "'>Customers</a></li>";
echo "<li><a href='" . base_url('reports') . "'>Reports</a></li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><strong>System Check Complete!</strong></p>";
echo "<p>If you see any errors above, those need to be fixed.</p>";
?>
