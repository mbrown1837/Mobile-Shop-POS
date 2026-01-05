<?php
/**
 * Mobile Shop POS - Automated Installer
 * This script automates the installation process
 */

// Start session
session_start();

// Configuration
define('INSTALLER_VERSION', '1.0.0');
define('APP_VERSION', '1.1.0');
define('MIN_PHP_VERSION', '7.4.0');
define('DB_FILE', 'database/mobile_shop_pos_v1.1.0_final.sql');

// Check if already installed
if (file_exists('application/config/installed.txt') && !isset($_GET['reinstall'])) {
    die('Application is already installed. Delete application/config/installed.txt to reinstall.');
}

// Initialize session variables
if (!isset($_SESSION['install_step'])) {
    $_SESSION['install_step'] = 1;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step'])) {
        $_SESSION['install_step'] = (int)$_POST['step'];
        
        // Save form data to session
        if (isset($_POST['db_host'])) $_SESSION['db_host'] = $_POST['db_host'];
        if (isset($_POST['db_user'])) $_SESSION['db_user'] = $_POST['db_user'];
        if (isset($_POST['db_pass'])) $_SESSION['db_pass'] = $_POST['db_pass'];
        if (isset($_POST['db_name'])) $_SESSION['db_name'] = $_POST['db_name'];
        if (isset($_POST['base_url'])) $_SESSION['base_url'] = $_POST['base_url'];
        if (isset($_POST['admin_password'])) $_SESSION['admin_password'] = $_POST['admin_password'];
    }
}

$step = $_SESSION['install_step'];
$errors = [];
$success = [];

// Step 1: System Requirements Check
if ($step == 1) {
    $requirements = [
        'PHP Version >= ' . MIN_PHP_VERSION => version_compare(PHP_VERSION, MIN_PHP_VERSION, '>='),
        'MySQL Extension' => extension_loaded('mysqli'),
        'JSON Extension' => extension_loaded('json'),
        'MBString Extension' => extension_loaded('mbstring'),
        'Config Writable' => is_writable('application/config'),
        'Cache Writable' => is_writable('application/cache'),
        'Logs Writable' => is_writable('application/logs'),
        'Database File Exists' => file_exists(DB_FILE)
    ];
}

// Step 2: Database Configuration
if ($step == 2 && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_connection'])) {
    $host = $_POST['db_host'];
    $user = $_POST['db_user'];
    $pass = $_POST['db_pass'];
    $name = $_POST['db_name'];
    
    $conn = @mysqli_connect($host, $user, $pass);
    if ($conn) {
        // Try to select database
        if (@mysqli_select_db($conn, $name)) {
            $success[] = "Database connection successful!";
        } else {
            // Try to create database
            if (@mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$name`")) {
                $success[] = "Database created successfully!";
            } else {
                $errors[] = "Cannot create database. Please create it manually.";
            }
        }
        mysqli_close($conn);
    } else {
        $errors[] = "Database connection failed: " . mysqli_connect_error();
    }
}

// Step 3: Import Database
if ($step == 3 && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_database'])) {
    $host = $_SESSION['db_host'];
    $user = $_SESSION['db_user'];
    $pass = $_SESSION['db_pass'];
    $name = $_SESSION['db_name'];
    
    $conn = @mysqli_connect($host, $user, $pass, $name);
    if ($conn) {
        mysqli_set_charset($conn, 'utf8');
        
        // Read SQL file
        $sql = file_get_contents(DB_FILE);
        
        // Split into individual queries
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        $imported = 0;
        foreach ($queries as $query) {
            if (!empty($query) && substr($query, 0, 2) !== '--') {
                if (@mysqli_query($conn, $query)) {
                    $imported++;
                } else {
                    $errors[] = "Query failed: " . mysqli_error($conn);
                    break;
                }
            }
        }
        
        if (empty($errors)) {
            $success[] = "Database imported successfully! ($imported queries executed)";
            $_SESSION['db_imported'] = true;
        }
        
        mysqli_close($conn);
    } else {
        $errors[] = "Database connection failed!";
    }
}

// Step 4: Configure Application
if ($step == 4 && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['configure'])) {
    // Update database.php
    $db_config = file_get_contents('application/config/database.php');
    $db_config = preg_replace(
        "/'hostname' => '.*?'/",
        "'hostname' => '{$_SESSION['db_host']}'",
        $db_config
    );
    $db_config = preg_replace(
        "/'username' => '.*?'/",
        "'username' => '{$_SESSION['db_user']}'",
        $db_config
    );
    $db_config = preg_replace(
        "/'password' => '.*?'/",
        "'password' => '{$_SESSION['db_pass']}'",
        $db_config
    );
    $db_config = preg_replace(
        "/'database' => '.*?'/",
        "'database' => '{$_SESSION['db_name']}'",
        $db_config
    );
    
    if (file_put_contents('application/config/database.php', $db_config)) {
        $success[] = "Database configuration updated!";
    } else {
        $errors[] = "Failed to update database configuration!";
    }
    
    // Update config.php
    $config = file_get_contents('application/config/config.php');
    $config = preg_replace(
        "/\\\$config\['base_url'\] = '.*?';/",
        "\$config['base_url'] = '{$_SESSION['base_url']}';",
        $config
    );
    
    if (file_put_contents('application/config/config.php', $config)) {
        $success[] = "Base URL configuration updated!";
    } else {
        $errors[] = "Failed to update base URL!";
    }
    
    // Update admin password if provided
    if (!empty($_SESSION['admin_password'])) {
        $conn = @mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_pass'], $_SESSION['db_name']);
        if ($conn) {
            $password = password_hash($_SESSION['admin_password'], PASSWORD_DEFAULT);
            $query = "UPDATE admin SET password = '$password' WHERE username = 'admin'";
            if (mysqli_query($conn, $query)) {
                $success[] = "Admin password updated!";
            }
            mysqli_close($conn);
        }
    }
    
    // Create installed flag
    if (empty($errors)) {
        file_put_contents('application/config/installed.txt', date('Y-m-d H:i:s'));
        $_SESSION['install_complete'] = true;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Shop POS - Installer</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 40px; }
        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        .steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
        }
        .step.active .step-circle {
            background: #667eea;
            color: white;
        }
        .step.completed .step-circle {
            background: #4caf50;
            color: white;
        }
        .step-label {
            font-size: 12px;
            color: #666;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        input[type="text"],
        input[type="password"],
        input[type="url"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-success {
            background: #4caf50;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .requirements {
            list-style: none;
        }
        .requirements li {
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .requirements li.pass {
            background: #d4edda;
            color: #155724;
        }
        .requirements li.fail {
            background: #f8d7da;
            color: #721c24;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Mobile Shop POS Installer</h1>
            <p>Version <?php echo APP_VERSION; ?> - Automated Installation Wizard</p>
        </div>
        
        <div class="content">
            <!-- Progress Steps -->
            <div class="steps">
                <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                    <div class="step-circle">1</div>
                    <div class="step-label">Requirements</div>
                </div>
                <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">
                    <div class="step-circle">2</div>
                    <div class="step-label">Database</div>
                </div>
                <div class="step <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'completed' : ''; ?>">
                    <div class="step-circle">3</div>
                    <div class="step-label">Import</div>
                </div>
                <div class="step <?php echo $step >= 4 ? 'active' : ''; ?> <?php echo $step > 4 ? 'completed' : ''; ?>">
                    <div class="step-circle">4</div>
                    <div class="step-label">Configure</div>
                </div>
                <div class="step <?php echo $step >= 5 ? 'active' : ''; ?>">
                    <div class="step-circle">5</div>
                    <div class="step-label">Complete</div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <div>❌ <?php echo $error; ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php foreach ($success as $msg): ?>
                        <div>✅ <?php echo $msg; ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Step Content -->
            <?php if ($step == 1): ?>
                <h2>System Requirements Check</h2>
                <p style="margin: 20px 0; color: #666;">Checking if your server meets the requirements...</p>
                
                <ul class="requirements">
                    <?php foreach ($requirements as $name => $status): ?>
                        <li class="<?php echo $status ? 'pass' : 'fail'; ?>">
                            <span><?php echo $name; ?></span>
                            <span class="badge <?php echo $status ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $status ? 'PASS' : 'FAIL'; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if (array_product($requirements)): ?>
                    <form method="POST">
                        <input type="hidden" name="step" value="2">
                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">Next: Database Setup →</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-error" style="margin-top: 20px;">
                        Please fix the failed requirements before continuing.
                    </div>
                <?php endif; ?>

            <?php elseif ($step == 2): ?>
                <h2>Database Configuration</h2>
                <p style="margin: 20px 0; color: #666;">Enter your database connection details:</p>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Database Host</label>
                        <input type="text" name="db_host" value="<?php echo $_SESSION['db_host'] ?? 'localhost'; ?>" required>
                        <div class="help-text">Usually "localhost"</div>
                    </div>

                    <div class="form-group">
                        <label>Database Username</label>
                        <input type="text" name="db_user" value="<?php echo $_SESSION['db_user'] ?? 'root'; ?>" required>
                        <div class="help-text">Your MySQL username</div>
                    </div>

                    <div class="form-group">
                        <label>Database Password</label>
                        <input type="password" name="db_pass" value="<?php echo $_SESSION['db_pass'] ?? ''; ?>">
                        <div class="help-text">Leave empty if no password</div>
                    </div>

                    <div class="form-group">
                        <label>Database Name</label>
                        <input type="text" name="db_name" value="<?php echo $_SESSION['db_name'] ?? 'mobile_shop_pos'; ?>" required>
                        <div class="help-text">Will be created if doesn't exist</div>
                    </div>

                    <div class="button-group">
                        <button type="submit" name="test_connection" class="btn btn-primary">Test Connection</button>
                        <?php if (!empty($success)): ?>
                            <button type="submit" name="step" value="3" class="btn btn-success">Next: Import Database →</button>
                        <?php endif; ?>
                    </div>
                </form>

            <?php elseif ($step == 3): ?>
                <h2>Import Database</h2>
                <p style="margin: 20px 0; color: #666;">Ready to import the database schema and initial data.</p>
                
                <div class="alert alert-success">
                    <strong>Database File:</strong> <?php echo DB_FILE; ?><br>
                    <strong>File Size:</strong> <?php echo round(filesize(DB_FILE) / 1024, 2); ?> KB
                </div>

                <form method="POST">
                    <input type="hidden" name="step" value="3">
                    <div class="button-group">
                        <button type="submit" name="import_database" class="btn btn-primary">Import Database</button>
                        <?php if (isset($_SESSION['db_imported'])): ?>
                            <button type="submit" name="step" value="4" class="btn btn-success">Next: Configure →</button>
                        <?php endif; ?>
                    </div>
                </form>

            <?php elseif ($step == 4): ?>
                <h2>Application Configuration</h2>
                <p style="margin: 20px 0; color: #666;">Configure your application settings:</p>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Base URL</label>
                        <input type="url" name="base_url" value="<?php echo $_SESSION['base_url'] ?? 'http://localhost/mobile-shop-pos/'; ?>" required>
                        <div class="help-text">Your application URL (must end with /)</div>
                    </div>

                    <div class="form-group">
                        <label>Admin Password (Optional)</label>
                        <input type="password" name="admin_password" value="">
                        <div class="help-text">Leave empty to keep default (admin123)</div>
                    </div>

                    <div class="button-group">
                        <button type="submit" name="configure" class="btn btn-primary">Configure & Complete Installation</button>
                    </div>
                </form>

            <?php elseif ($step == 5 || isset($_SESSION['install_complete'])): ?>
                <h2>🎉 Installation Complete!</h2>
                <p style="margin: 20px 0; color: #666;">Your Mobile Shop POS is now ready to use!</p>
                
                <div class="alert alert-success">
                    <h3 style="margin-bottom: 15px;">✅ Installation Successful</h3>
                    <p><strong>Default Login Credentials:</strong></p>
                    <ul style="margin: 10px 0 10px 20px;">
                        <li>Username: <strong>admin</strong></li>
                        <li>Password: <strong><?php echo !empty($_SESSION['admin_password']) ? '(your custom password)' : 'admin123'; ?></strong></li>
                    </ul>
                    <p style="margin-top: 15px;">⚠️ <strong>Important:</strong> Delete or rename <code>install.php</code> for security!</p>
                </div>

                <div class="button-group">
                    <a href="<?php echo $_SESSION['base_url']; ?>" class="btn btn-success">Go to Application →</a>
                </div>

                <?php
                // Clear session
                session_destroy();
                ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
