<?php
require_once 'config/settings.php';

if (!TEST_MODE) {
    die("Test panel only available in TEST_MODE");
}

// Handle test actions
$message = '';

if (isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'add_test_data':
            // Add test registrations
            for($i = 1; $i <= 5; $i++) {
                $unique_id = 'TEST' . date('Ymd') . sprintf('%03d', $i);
                $sql = "INSERT IGNORE INTO registrations (unique_id, name, email, phone, additional_count, total_amount, payment_status, qr_code) 
                        VALUES ('$unique_id', 'Test User $i', 'test$i@example.com', '987654321$i', 1, 1000, 'success', 
                        'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=$unique_id')";
                mysqli_query($conn, $sql);
            }
            $message = "✅ Added 5 test registrations";
            break;
            
        case 'clear_test_data':
            mysqli_query($conn, "DELETE FROM registrations WHERE unique_id LIKE 'TEST%'");
            mysqli_query($conn, "DELETE FROM attendees WHERE attendee_unique_id LIKE 'TEST%'");
            $message = "✅ Cleared all test data";
            break;
            
        case 'reset_database':
            mysqli_query($conn, "TRUNCATE TABLE registrations");
            mysqli_query($conn, "TRUNCATE TABLE attendees");
            mysqli_query($conn, "TRUNCATE TABLE email_queue");
            $message = "✅ Database reset complete";
            break;
    }
}

// Get statistics
$total_reg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM registrations"))['count'];
$test_reg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM registrations WHERE unique_id LIKE 'TEST%'"))['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>🧪 Test Control Panel</h1>
        
        <?php if($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Test Configuration</h5>
                        <table class="table">
                            <tr>
                                <td>TEST_MODE</td>
                                <td><span class="badge bg-success">ENABLED</span></td>
                            </tr>
                            <tr>
                                <td>SKIP_PAYMENT</td>
                                <td><span class="badge bg-<?php echo SKIP_PAYMENT ? 'warning' : 'success'; ?>">
                                    <?php echo SKIP_PAYMENT ? 'YES' : 'NO'; ?>
                                </span></td>
                            </tr>
                            <tr>
                                <td>SKIP_EMAIL</td>
                                <td><span class="badge bg-<?php echo SKIP_EMAIL ? 'warning' : 'success'; ?>">
                                    <?php echo SKIP_EMAIL ? 'YES' : 'NO'; ?>
                                </span></td>
                            </tr>
                            <tr>
                                <td>AUTO_FILL_FORMS</td>
                                <td><span class="badge bg-<?php echo AUTO_FILL_FORMS ? 'warning' : 'success'; ?>">
                                    <?php echo AUTO_FILL_FORMS ? 'YES' : 'NO'; ?>
                                </span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Database Statistics</h5>
                        <table class="table">
                            <tr>
                                <td>Total Registrations</td>
                                <td><strong><?php echo $total_reg; ?></strong></td>
                            </tr>
                            <tr>
                                <td>Test Registrations</td>
                                <td><strong><?php echo $test_reg; ?></strong></td>
                            </tr>
                            <tr>
                                <td>Database</td>
                                <td><span class="badge bg-success">CONNECTED</span></td>
                            </tr>
                            <tr>
                                <td>Site URL</td>
                                <td><small><?php echo SITE_URL; ?></small></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-body">
                <h5>Test Actions</h5>
                <form method="POST" class="d-inline">
                    <button type="submit" name="action" value="add_test_data" class="btn btn-success me-2">
                        ➕ Add 5 Test Registrations
                    </button>
                </form>
                
                <form method="POST" class="d-inline">
                    <button type="submit" name="action" value="clear_test_data" class="btn btn-warning me-2">
                        🧹 Clear Test Data Only
                    </button>
                </form>
                
                <form method="POST" class="d-inline" onsubmit="return confirm('This will delete ALL data. Are you sure?')">
                    <button type="submit" name="action" value="reset_database" class="btn btn-danger">
                        ⚠️ Reset Entire Database
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-body">
                <h5>Quick Links</h5>
                <a href="index.php" class="btn btn-primary me-2">Main Site</a>
                <a href="admin/login.php" class="btn btn-secondary me-2">Admin Panel</a>
                <a href="thank-you.php?test=1" class="btn btn-info me-2">Test Thank You Page</a>
                <a href="phpinfo.php" class="btn btn-warning">PHP Info</a>
            </div>
        </div>
        
        <div class="alert alert-info mt-4">
            <h6>Test Credentials:</h6>
            <ul>
                <li>Admin Username: <code>admin</code></li>
                <li>Admin Password: <code>admin123</code></li>
            </ul>
        </div>
    </div>
</body>
</html>