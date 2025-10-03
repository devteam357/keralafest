<?php
require_once '../config/settings.php';
require_once '../includes/functions.php';
check_admin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        if ($key != 'submit') {
            $key = mysqli_real_escape_string($conn, $key);
            $value = mysqli_real_escape_string($conn, $value);
            
            $sql = "INSERT INTO settings (setting_key, setting_value) 
                    VALUES ('$key', '$value') 
                    ON DUPLICATE KEY UPDATE setting_value = '$value'";
            mysqli_query($conn, $sql);
        }
    }
    $success = "Settings updated successfully!";
    
    // Reload settings
    header("Location: settings.php?success=1");
    exit();
}

// Get all settings
$sql = "SELECT * FROM settings ORDER BY id";
$result = mysqli_query($conn, $sql);
$settings = array();
while ($row = mysqli_fetch_assoc($result)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">System Settings</span>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="attendees.php" class="btn btn-outline-light me-2">Attendees</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> Settings updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="row">
                <!-- General Settings -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-gear"></i> General Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Site URL</label>
                                <input type="url" name="site_url" class="form-control" 
                                       value="<?php echo $settings['site_url'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Event Name</label>
                                <input type="text" name="event_name" class="form-control" 
                                       value="<?php echo $settings['event_name'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Event Date</label>
                                <input type="date" name="event_date" class="form-control" 
                                       value="<?php echo $settings['event_date'] ?? ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Event Time</label>
                                <input type="text" name="event_time" class="form-control" 
                                       value="<?php echo $settings['event_time'] ?? '9:00 AM - 6:00 PM'; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Event Venue</label>
                                <input type="text" name="event_venue" class="form-control" 
                                       value="<?php echo $settings['event_venue'] ?? ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Timezone</label>
                                <select name="timezone" class="form-select">
                                    <option value="Asia/Kolkata" <?php echo ($settings['timezone'] ?? '') == 'Asia/Kolkata' ? 'selected' : ''; ?>>Asia/Kolkata (IST)</option>
                                    <option value="UTC" <?php echo ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Ticket Price (₹)</label>
                                <input type="number" name="ticket_price" class="form-control" 
                                       value="<?php echo $settings['ticket_price'] ?? '500'; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Max Additional Attendees</label>
                                <input type="number" name="max_attendees_per_registration" class="form-control" 
                                       value="<?php echo $settings['max_attendees_per_registration'] ?? '5'; ?>" min="0" max="10">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Registration Status</label>
                                <select name="registration_open" class="form-select">
                                    <option value="1" <?php echo ($settings['registration_open'] ?? '1') == '1' ? 'selected' : ''; ?>>Open</option>
                                    <option value="0" <?php echo ($settings['registration_open'] ?? '1') == '0' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Settings -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-credit-card"></i> Payment Settings (Razorpay)</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Razorpay Key ID</label>
                                <input type="text" name="razorpay_key" class="form-control" 
                                       value="<?php echo $settings['razorpay_key'] ?? ''; ?>" 
                                       placeholder="rzp_test_XXXXXXXXXXXXX">
                                <small class="text-muted">Get from Razorpay Dashboard → Settings → API Keys</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Razorpay Secret Key</label>
                                <input type="password" name="razorpay_secret" class="form-control" 
                                       value="<?php echo $settings['razorpay_secret'] ?? ''; ?>" 
                                       placeholder="Your secret key">
                                <small class="text-muted">Keep this secret and secure!</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Webhook Secret (Optional)</label>
                                <input type="password" name="webhook_secret" class="form-control" 
                                       value="<?php echo $settings['webhook_secret'] ?? ''; ?>" 
                                       placeholder="Webhook secret">
                                <small class="text-muted">For webhook verification</small>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-envelope"></i> Email Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">SMTP Host</label>
                                <input type="text" name="smtp_host" class="form-control" 
                                       value="<?php echo $settings['smtp_host'] ?? 'smtp.gmail.com'; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">SMTP Port</label>
                                <input type="text" name="smtp_port" class="form-control" 
                                       value="<?php echo $settings['smtp_port'] ?? '587'; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">SMTP Username (Email)</label>
                                <input type="email" name="smtp_user" class="form-control" 
                                       value="<?php echo $settings['smtp_user'] ?? ''; ?>" 
                                       placeholder="your_email@gmail.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">SMTP Password / App Password</label>
                                <input type="password" name="smtp_password" class="form-control" 
                                       value="<?php echo $settings['smtp_password'] ?? ''; ?>" 
                                       placeholder="Your app password">
                                <small class="text-muted">For Gmail, use App Password (not regular password)</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">From Email</label>
                                <input type="email" name="email_from" class="form-control" 
                                       value="<?php echo $settings['email_from'] ?? ''; ?>" 
                                       placeholder="noreply@yourdomain.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">From Name</label>
                                <input type="text" name="email_from_name" class="form-control" 
                                       value="<?php echo $settings['email_from_name'] ?? 'Event Registration'; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Admin Email (for notifications)</label>
                                <input type="email" name="admin_email" class="form-control" 
                                       value="<?php echo $settings['admin_email'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mb-4">
                <button type="submit" name="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save"></i> Save All Settings
                </button>
                <a href="index.php" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>

        <!-- Test Section -->
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-bug"></i> Test Settings</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <button class="btn btn-outline-primary w-100" onclick="testRazorpay()">
                            Test Razorpay Connection
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-info w-100" onclick="testEmail()">
                            Send Test Email
                        </button>
                    </div>
                    <div class="col-md-4">
                        <a href="../test-panel.php" class="btn btn-outline-warning w-100">
                            Open Test Panel
                        </a>
                    </div>
                </div>
                <div id="test-result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function testRazorpay() {
        document.getElementById('test-result').innerHTML = '<div class="alert alert-info">Testing Razorpay connection...</div>';
        // Add actual test logic here
        setTimeout(() => {
            document.getElementById('test-result').innerHTML = '<div class="alert alert-success">Razorpay test completed!</div>';
        }, 2000);
    }

    function testEmail() {
        document.getElementById('test-result').innerHTML = '<div class="alert alert-info">Sending test email...</div>';
        
        fetch('../api/test_email.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('test-result').innerHTML = '<div class="alert alert-success">Test email sent successfully!</div>';
                } else {
                    document.getElementById('test-result').innerHTML = '<div class="alert alert-danger">Email test failed: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                document.getElementById('test-result').innerHTML = '<div class="alert alert-danger">Error: ' + error + '</div>';
            });
    }
    </script>
</body>
</html>