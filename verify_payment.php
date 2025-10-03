<?php
session_start();
require_once '../config/settings.php';

header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

$payment_id = $input['razorpay_payment_id'] ?? '';
$registration_id = $input['registration_id'] ?? 0;

if (!$payment_id || !$registration_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

// For production, verify signature with Razorpay
// For now, we'll just update the payment status

// Update registration
$update_sql = "UPDATE registrations 
               SET payment_status = 'success', 
                   payment_id = '" . mysqli_real_escape_string($conn, $payment_id) . "' 
               WHERE id = " . (int)$registration_id;

if (mysqli_query($conn, $update_sql)) {
    // Send confirmation email (if enabled)
    if (!SKIP_EMAIL) {
        // Add to email queue
        $reg_sql = "SELECT * FROM registrations WHERE id = $registration_id";
        $reg_result = mysqli_query($conn, $reg_sql);
        $registration = mysqli_fetch_assoc($reg_result);
        
        $email_sql = "INSERT INTO email_queue (recipient_email, email_type, unique_id) 
                      VALUES ('{$registration['primary_email']}', 'confirmation', '{$registration['unique_id']}')";
        mysqli_query($conn, $email_sql);
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
}
?>