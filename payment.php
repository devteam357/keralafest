<?php
// payment.php - Final working version (based on test-payment.php which works)

require_once 'config/database.php';
require_once 'config/settings.php';

// Get registration_id from URL
$registration_id = isset($_GET['registration_id']) ? (int)$_GET['registration_id'] : 0;

if ($registration_id == 0) {
    die('<h2>No registration ID provided</h2><p><a href="index.php">Start Registration</a></p>');
}

// Get registration from database
$query = "SELECT * FROM registrations WHERE id = $registration_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die('<h2>Registration not found</h2><p><a href="index.php">Start Registration</a></p>');
}

$registration = mysqli_fetch_assoc($result);

// Check if already paid
if ($registration['payment_status'] == 'success') {
    $attendee_query = "SELECT * FROM attendees WHERE registration_id = $registration_id";
    $attendee_result = mysqli_query($conn, $attendee_query);
    if ($attendee_result && mysqli_num_rows($attendee_result) > 0) {
        $attendee = mysqli_fetch_assoc($attendee_result);
        echo '<div style="text-align:center; padding:50px;">';
        echo '<h2 style="color:green;">✅ Payment Already Completed</h2>';
        echo '<p>Attendee ID: <strong>' . $attendee['attendee_unique_id'] . '</strong></p>';
        if ($attendee['qr_code']) {
            echo '<img src="' . $attendee['qr_code'] . '" alt="QR Code">';
        }
        echo '<p><a href="index.php">Back to Home</a></p>';
        echo '</div>';
        exit();
    }
}

// Process test payment if submitted
if (isset($_POST['process_payment'])) {
    // Generate attendee ID
    $count_query = "SELECT COUNT(*) as count FROM attendees";
    $count_result = mysqli_query($conn, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $attendee_count = $count_row['count'] + 1;
    $attendee_unique_id = 'KERF' . str_pad($attendee_count, 5, '0', STR_PAD_LEFT);
    
    // Generate QR code URL
    $qr_data = "https://keralafest.in/admin/scan.php?id=" . $attendee_unique_id;
    $qr_code_url = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($qr_data);
    
    // Update registration as paid
    $payment_id = 'TEST_' . time();
    $update_query = "UPDATE registrations SET payment_status = 'success', payment_id = '$payment_id' WHERE id = $registration_id";
    
    if (mysqli_query($conn, $update_query)) {
        // Create attendee record
        $insert_query = "INSERT INTO attendees (
            registration_id,
            attendee_unique_id,
            attendee_name,
            attendee_email,
            qr_code,
            attended
        ) VALUES (
            $registration_id,
            '$attendee_unique_id',
            '" . mysqli_real_escape_string($conn, $registration['name']) . "',
            '" . mysqli_real_escape_string($conn, $registration['email']) . "',
            '$qr_code_url',
            0
        )";
        
        if (mysqli_query($conn, $insert_query)) {
            // Success! Show the ticket
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Payment Successful - Kerala Food Fest 2025</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background: linear-gradient(135deg, #4CAF50, #45a049);
                        margin: 0;
                        padding: 20px;
                    }
                    .success-container {
                        max-width: 600px;
                        margin: 0 auto;
                        background: white;
                        padding: 30px;
                        border-radius: 15px;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                        text-align: center;
                    }
                    .success-icon {
                        font-size: 72px;
                        color: #4CAF50;
                        margin-bottom: 20px;
                    }
                    .attendee-id {
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        color: white;
                        padding: 20px;
                        border-radius: 10px;
                        font-size: 28px;
                        font-weight: bold;
                        letter-spacing: 3px;
                        margin: 20px 0;
                    }
                    .qr-section {
                        margin: 30px 0;
                        padding: 20px;
                        background: #f8f9fa;
                        border-radius: 10px;
                    }
                    .details {
                        text-align: left;
                        background: #f0f0f0;
                        padding: 15px;
                        border-radius: 8px;
                        margin: 20px 0;
                    }
                    .details p {
                        margin: 8px 0;
                    }
                    .btn {
                        display: inline-block;
                        padding: 12px 30px;
                        margin: 10px;
                        background: #007bff;
                        color: white;
                        text-decoration: none;
                        border-radius: 25px;
                        transition: all 0.3s;
                    }
                    .btn:hover {
                        background: #0056b3;
                        transform: translateY(-2px);
                    }
                    @media print {
                        body { background: white; }
                        .btn { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="success-container">
                    <div class="success-icon">✅</div>
                    <h1>Payment Successful!</h1>
                    <p>Your registration for Kerala Food Fest 2025 is confirmed!</p>
                    
                    <div class="attendee-id">
                        <?php echo $attendee_unique_id; ?>
                    </div>
                    
                    <div class="qr-section">
                        <h3>Your Entry QR Code</h3>
                        <img src="<?php echo $qr_code_url; ?>" alt="Entry QR Code">
                        <p>Show this QR code at the venue for entry</p>
                    </div>
                    
                    <div class="details">
                        <h4>Registration Details:</h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($registration['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($registration['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($registration['phone']); ?></p>
                        <p><strong>Amount Paid:</strong> ₹<?php echo number_format($registration['total_amount'], 2); ?></p>
                        <p><strong>Transaction ID:</strong> <?php echo $payment_id; ?></p>
                    </div>
                    
                    <div style="background:#fff3cd; border:1px solid #ffc107; color:#856404; padding:15px; border-radius:8px; margin:20px 0;">
                        <h4>Important Instructions:</h4>
                        <ul style="text-align:left;">
                            <li>Save this QR code on your phone</li>
                            <li>A confirmation email will be sent to your registered email</li>
                            <li>Gates open at 9:00 AM on all event days</li>
                            <li>Event Dates: November 13-16, 2025</li>
                        </ul>
                    </div>
                    
                    <a href="index.php" class="btn">Back to Home</a>
                    <a href="javascript:window.print()" class="btn">Print Ticket</a>
                </div>
            </body>
            </html>
            <?php
            exit();
        } else {
            $error = "Failed to create attendee record: " . mysqli_error($conn);
        }
    } else {
        $error = "Failed to update payment status: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment - Kerala Food Fest 2025</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .payment-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        h2 {
            text-align: center;
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .order-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .order-row:last-child {
            border-bottom: none;
            font-weight: bold;
            color: #667eea;
            font-size: 18px;
            padding-top: 15px;
        }
        .pay-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        .pay-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.4);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }
        .back-link:hover {
            color: #667eea;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>🎪 Kerala Food Fest 2025</h1>
        <h2>Complete Your Payment</h2>
        
        <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="order-summary">
            <h3 style="margin-top:0;">Order Summary</h3>
            <div class="order-row">
                <span>Registration ID</span>
                <span><?php echo $registration['unique_id']; ?></span>
            </div>
            <div class="order-row">
                <span>Name</span>
                <span><?php echo htmlspecialchars($registration['name']); ?></span>
            </div>
            <div class="order-row">
                <span>Email</span>
                <span><?php echo htmlspecialchars($registration['email']); ?></span>
            </div>
            <div class="order-row">
                <span>Phone</span>
                <span><?php echo $registration['phone']; ?></span>
            </div>
            <div class="order-row">
                <span>Total Amount</span>
                <span>₹<?php echo number_format($registration['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <form method="POST">
            <button type="submit" name="process_payment" value="1" class="pay-btn">
                <?php echo TEST_MODE ? 'Complete Test Payment' : 'Proceed to Payment'; ?>
            </button>
        </form>
        
        <a href="index.php" class="back-link">← Back to Registration</a>
    </div>
</body>
</html>