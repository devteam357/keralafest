<?php
// payment.php - Fixed to handle multiple attendees properly

require_once 'config/database.php';
require_once 'config/settings.php';

// Get registration_id from URL
$registration_id = isset($_GET['registration_id']) ? (int)$_GET['registration_id'] : 0;

if ($registration_id == 0) {
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error - Kerala Food Fest 2025</title>
        <style>
            body { font-family: Arial; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f0f0f0; }
            .error-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
            .btn { display: inline-block; margin-top: 20px; padding: 12px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h2>❌ No Registration ID Provided</h2>
            <p>Please complete registration first to proceed with payment.</p>
            <a href="index.php" class="btn">Go to Registration</a>
        </div>
    </body>
    </html>
    ');
}

// Get registration from database
$query = "SELECT * FROM registrations WHERE id = $registration_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die('Registration not found. <a href="index.php">Start Over</a>');
}

$registration = mysqli_fetch_assoc($result);

// Check if already paid
if ($registration['payment_status'] == 'success') {
    // Get all attendees for this registration
    $attendee_query = "SELECT attendee_unique_id FROM attendees WHERE registration_id = $registration_id ORDER BY id";
    $attendee_result = mysqli_query($conn, $attendee_query);
    
    $attendee_ids = array();
    while ($row = mysqli_fetch_assoc($attendee_result)) {
        $attendee_ids[] = $row['attendee_unique_id'];
    }
    
    if (!empty($attendee_ids)) {
        // Redirect to thank-you page with all attendee IDs
        $ids_string = implode(',', $attendee_ids);
        header('Location: thank-you.php?attendee_id=' . $ids_string);
        exit();
    }
}

// Process payment if form submitted
if (isset($_POST['process_payment'])) {
    // Calculate total number of attendees
    $total_attendees = 1 + (int)$registration['additional_attendees'];
    
    // Array to store all generated attendee IDs
    $all_attendee_ids = array();
    
    // Update registration as paid FIRST
    $payment_id = 'TEST_' . time();
    $update_query = "UPDATE registrations SET payment_status = 'success', payment_id = '$payment_id' WHERE id = $registration_id";
    
    if (mysqli_query($conn, $update_query)) {
        // Create attendee records for each person
        for ($i = 0; $i < $total_attendees; $i++) {
            // Generate unique attendee ID for each person
            $count_query = "SELECT COUNT(*) as count FROM attendees";
            $count_result = mysqli_query($conn, $count_query);
            $count_row = mysqli_fetch_assoc($count_result);
            $attendee_count = $count_row['count'] + 1;
            $attendee_unique_id = 'KERF' . str_pad($attendee_count, 5, '0', STR_PAD_LEFT);
            
            // Store just the attendee ID in database, not the full QR URL
            // QR will be generated fresh when displaying
            $qr_code_placeholder = $attendee_unique_id; // Just store the ID
            
            // Determine attendee name (primary or additional)
            if ($i == 0) {
                $attendee_name = $registration['name'];
            } else {
                // You can modify this to get actual names from form if you store them
                $attendee_name = $registration['name'] . " - Guest " . $i;
            }
            
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
                '" . mysqli_real_escape_string($conn, $attendee_name) . "',
                '" . mysqli_real_escape_string($conn, $registration['email']) . "',
                '$qr_code_placeholder',
                0
            )";
            
            if (mysqli_query($conn, $insert_query)) {
                $all_attendee_ids[] = $attendee_unique_id;
            } else {
                $error = "Failed to create attendee record: " . mysqli_error($conn);
                break;
            }
        }
        
        // If all attendees created successfully, redirect to thank you page
        if (count($all_attendee_ids) == $total_attendees) {
            $ids_string = implode(',', $all_attendee_ids);
            header('Location: thank-you.php?attendee_id=' . $ids_string);
            exit();
        }
    } else {
        $error = "Failed to update payment status: " . mysqli_error($conn);
    }
}

// Show payment page
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment - Kerala Food Fest 2025</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        h2 {
            text-align: center;
            color: #666;
            font-size: 1rem;
            font-weight: normal;
            margin-bottom: 30px;
        }
        .logo {
            text-align: center;
            font-size: 50px;
            margin-bottom: 20px;
        }
        .order-summary {
            background: linear-gradient(135deg, #f8f9fa, #fff);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
        }
        .order-summary h3 {
            margin-top: 0;
            color: #667eea;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .order-row:last-child {
            border-bottom: none;
            font-weight: bold;
            color: #667eea;
            font-size: 1.3rem;
            padding-top: 20px;
            border-top: 2px solid #667eea;
            margin-top: 10px;
        }
        .order-label {
            color: #666;
        }
        .order-value {
            color: #333;
            font-weight: 500;
        }
        .highlight-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .pay-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .pay-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #666;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .back-link:hover {
            color: #667eea;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .processing {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .processing.active {
            display: block;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="logo">🎪</div>
        <h1>Kerala Food Fest 2025</h1>
        <h2>Complete Your Payment</h2>
        
        <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php 
        $total_attendees = 1 + (int)$registration['additional_attendees'];
        if ($total_attendees > 1): 
        ?>
        <div class="highlight-box">
            <strong>🎫 <?php echo $total_attendees; ?> Tickets</strong><br>
            <small>Each person will receive an individual QR code for entry</small>
        </div>
        <?php endif; ?>
        
        <div class="order-summary">
            <h3>📋 Order Summary</h3>
            <div class="order-row">
                <span class="order-label">Registration ID</span>
                <span class="order-value"><?php echo $registration['unique_id']; ?></span>
            </div>
            <div class="order-row">
                <span class="order-label">Primary Name</span>
                <span class="order-value"><?php echo htmlspecialchars($registration['name']); ?></span>
            </div>
            <div class="order-row">
                <span class="order-label">Email</span>
                <span class="order-value"><?php echo htmlspecialchars($registration['email']); ?></span>
            </div>
            <div class="order-row">
                <span class="order-label">Phone</span>
                <span class="order-value"><?php echo $registration['phone']; ?></span>
            </div>
            <?php if ($total_attendees > 1): ?>
            <div class="order-row">
                <span class="order-label">Total Attendees</span>
                <span class="order-value"><?php echo $total_attendees; ?> Person(s)</span>
            </div>
            <div class="order-row">
                <span class="order-label">Price per Ticket</span>
                <span class="order-value">₹<?php echo number_format($registration['total_amount'] / $total_attendees, 2); ?></span>
            </div>
            <?php endif; ?>
            <div class="order-row">
                <span class="order-label">Total Amount</span>
                <span class="order-value">₹<?php echo number_format($registration['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <div class="processing" id="processing">
            <div class="spinner"></div>
            <p>Processing payment and generating <?php echo $total_attendees; ?> ticket(s)...</p>
            <p style="color: #666; font-size: 0.9rem;">Please wait, do not refresh the page</p>
        </div>
        
        <form method="POST" onsubmit="showProcessing()">
            <button type="submit" name="process_payment" value="1" class="pay-btn" id="payBtn">
                <?php echo (defined('TEST_MODE') && TEST_MODE) ? '✅ Complete Test Payment' : '💳 Proceed to Payment'; ?>
            </button>
        </form>
        
        <a href="index.php" class="back-link">← Back to Registration</a>
    </div>
    
    <script>
        function showProcessing() {
            document.getElementById('processing').classList.add('active');
            document.getElementById('payBtn').style.display = 'none';
        }
    </script>
</body>
</html>