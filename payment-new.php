<?php
// payment-new.php - Simple payment page without sessions

require_once 'config/database.php';
require_once 'config/settings.php';

// Get registration_id from URL
$registration_id = isset($_GET['registration_id']) ? (int)$_GET['registration_id'] : 0;

if ($registration_id == 0) {
    die('<h2>ERROR: No registration_id provided in URL.</h2> <a href="index.php">Start Over</a>');
}

// Get registration from database
$query = "SELECT * FROM registrations WHERE id = $registration_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('<h2>Database Error:</h2> ' . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    die('<h2>ERROR: Registration #' . $registration_id . ' not found in database.</h2> <a href="index.php">Start Over</a>');
}

$registration = mysqli_fetch_assoc($result);

// Check if already paid
if ($registration['payment_status'] == 'success') {
    die('<h2>This registration has already been paid.</h2> <a href="index.php">Register Again</a>');
}

echo "<h1>Payment Page - Working!</h1>";
echo "<p>Registration found successfully!</p>";
echo "<pre>";
print_r($registration);
echo "</pre>";

// Test mode - create attendee and redirect to success
if (TEST_MODE) {
    echo '<h2>TEST MODE - Click below to complete test payment:</h2>';
    echo '<form method="POST">';
    echo '<input type="hidden" name="complete_payment" value="1">';
    echo '<button type="submit" style="padding:10px 20px; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer;">Complete Test Payment</button>';
    echo '</form>';
    
    if (isset($_POST['complete_payment'])) {
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
        $test_payment_id = 'TEST_' . time();
        $update_query = "UPDATE registrations SET payment_status = 'success', payment_id = '$test_payment_id' WHERE id = $registration_id";
        mysqli_query($conn, $update_query);
        
        // Create attendee record
        $insert_attendee = "INSERT INTO attendees (
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
        
        if (mysqli_query($conn, $insert_attendee)) {
            echo '<h2 style="color:green;">✅ Payment Successful! Attendee ID: ' . $attendee_unique_id . '</h2>';
            echo '<p>QR Code: <br><img src="' . $qr_code_url . '" alt="QR Code"></p>';
            echo '<a href="index.php">Back to Home</a>';
        } else {
            echo '<h2 style="color:red;">Error creating attendee: ' . mysqli_error($conn) . '</h2>';
        }
    }
}
?>