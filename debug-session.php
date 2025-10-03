<?php
// debug-session.php - Debug session issues

session_start();

// If setting test registration
if (isset($_GET['set_registration'])) {
    $_SESSION['registration'] = array(
        'id' => 1,
        'unique_id' => 'TEST_' . time(),
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '9876543210',
        'amount' => 200,
        'attendees' => 1
    );
    echo "<div style='background:#d4edda; padding:10px; margin:10px; border-radius:5px;'>✅ Registration session set!</div>";
}

// Get last registration from database
require_once 'config/database.php';
$last_reg = null;
$result = mysqli_query($conn, "SELECT * FROM registrations ORDER BY id DESC LIMIT 1");
if ($result && mysqli_num_rows($result) > 0) {
    $last_reg = mysqli_fetch_assoc($result);
}

// If setting from database
if (isset($_GET['set_from_db']) && $last_reg) {
    $_SESSION['registration'] = array(
        'id' => $last_reg['id'],
        'unique_id' => $last_reg['unique_id'],
        'name' => $last_reg['name'],
        'email' => $last_reg['email'],
        'phone' => $last_reg['phone'],
        'amount' => $last_reg['total_amount'],
        'attendees' => 1 + $last_reg['additional_attendees']
    );
    echo "<div style='background:#d4edda; padding:10px; margin:10px; border-radius:5px;'>✅ Registration session set from database!</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Session</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-warning {
            background: #ffc107;
            color: black;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        .status-pending { color: orange; }
        .status-success { color: green; }
        .status-failed { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Session Debug Panel</h1>
        
        <div class="section">
            <h2>Current Session Status</h2>
            <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
            <p><strong>Session Name:</strong> <?php echo session_name(); ?></p>
            <p><strong>Session Save Path:</strong> <?php echo session_save_path(); ?></p>
            <p><strong>Session Cookie Params:</strong></p>
            <pre><?php print_r(session_get_cookie_params()); ?></pre>
        </div>
        
        <div class="section">
            <h2>Session Variables</h2>
            <pre><?php print_r($_SESSION); ?></pre>
            
            <?php if (isset($_SESSION['registration'])): ?>
                <div style="background:#d4edda; padding:10px; margin:10px 0; border-radius:5px;">
                    ✅ Registration session exists!
                </div>
            <?php else: ?>
                <div style="background:#f8d7da; padding:10px; margin:10px 0; border-radius:5px;">
                    ❌ No registration session found!
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>Quick Actions</h2>
            <a href="?set_registration=1" class="btn btn-success">Set Test Registration Session</a>
            <?php if ($last_reg): ?>
                <a href="?set_from_db=1" class="btn btn-warning">Set Session from Last DB Registration</a>
            <?php endif; ?>
            <a href="payment.php" class="btn">Go to Payment (with session)</a>
            <?php if ($last_reg): ?>
                <a href="payment.php?registration_id=<?php echo $last_reg['id']; ?>" class="btn">Go to Payment (with ID)</a>
            <?php endif; ?>
            <a href="index.php" class="btn">Go to Index</a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger">Refresh</a>
        </div>
        
        <div class="section">
            <h2>Last Registration in Database</h2>
            <?php if ($last_reg): ?>
                <table>
                    <tr><th>Field</th><th>Value</th></tr>
                    <tr><td>ID</td><td><?php echo $last_reg['id']; ?></td></tr>
                    <tr><td>Unique ID</td><td><?php echo $last_reg['unique_id']; ?></td></tr>
                    <tr><td>Name</td><td><?php echo $last_reg['name']; ?></td></tr>
                    <tr><td>Email</td><td><?php echo $last_reg['email']; ?></td></tr>
                    <tr><td>Phone</td><td><?php echo $last_reg['phone']; ?></td></tr>
                    <tr><td>Amount</td><td>₹<?php echo $last_reg['total_amount']; ?></td></tr>
                    <tr><td>Status</td><td class="status-<?php echo $last_reg['payment_status']; ?>"><?php echo $last_reg['payment_status']; ?></td></tr>
                </table>
                
                <div style="margin-top: 10px;">
                    <a href="payment.php?registration_id=<?php echo $last_reg['id']; ?>" class="btn btn-success">
                        Process Payment for this Registration
                    </a>
                </div>
            <?php else: ?>
                <p>No registrations found in database.</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>Test Registration Form Submit</h2>
            <p>This will submit directly to api/register.php</p>
            <form action="api/register.php" method="POST">
                <input type="text" name="name" value="Test User <?php echo time(); ?>" required>
                <input type="email" name="email" value="test<?php echo time(); ?>@example.com" required>
                <input type="tel" name="phone" value="9876543210" required>
                <input type="hidden" name="additional_count" value="0">
                <input type="hidden" name="amount" value="200">
                <button type="submit" class="btn btn-success">Submit Test Registration</button>
            </form>
        </div>
        
        <div class="section">
            <h2>Session Files Check</h2>
            <?php
            $session_dir = session_save_path();
            if (empty($session_dir)) {
                $session_dir = sys_get_temp_dir();
            }
            echo "<p>Session Directory: $session_dir</p>";
            
            if (is_writable($session_dir)) {
                echo "<p style='color:green;'>✅ Session directory is writable</p>";
            } else {
                echo "<p style='color:red;'>❌ Session directory is NOT writable</p>";
            }
            
            // Check if session file exists
            $session_file = $session_dir . '/sess_' . session_id();
            if (file_exists($session_file)) {
                echo "<p style='color:green;'>✅ Session file exists</p>";
                echo "<p>File size: " . filesize($session_file) . " bytes</p>";
            } else {
                echo "<p style='color:orange;'>⚠️ Session file not found at expected location</p>";
            }
            ?>
        </div>
        
        <div class="section">
            <h2>PHP Configuration</h2>
            <pre>
PHP Version: <?php echo PHP_VERSION; ?>

session.save_handler: <?php echo ini_get('session.save_handler'); ?>

session.save_path: <?php echo ini_get('session.save_path'); ?>

session.gc_maxlifetime: <?php echo ini_get('session.gc_maxlifetime'); ?> seconds
session.cookie_lifetime: <?php echo ini_get('session.cookie_lifetime'); ?> seconds
session.cookie_domain: <?php echo ini_get('session.cookie_domain'); ?>

session.cookie_path: <?php echo ini_get('session.cookie_path'); ?>

session.use_cookies: <?php echo ini_get('session.use_cookies'); ?>
            </pre>
        </div>
    </div>
</body>
</html>