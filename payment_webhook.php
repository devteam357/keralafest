<?php
require_once '../config/settings.php';

// Get webhook data
$webhook_body = file_get_contents('php://input');
$webhook_data = json_decode($webhook_body, true);

// Log webhook (for debugging)
$log_file = '../logs/razorpay_webhook_' . date('Y-m-d') . '.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . ' - ' . $webhook_body . "\n", FILE_APPEND);

// Verify webhook signature (for production)
$webhook_signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';
$webhook_secret = RAZORPAY_SECRET; // Add webhook secret to settings

// For now, we'll process without signature verification
// In production, you should verify the signature

if ($webhook_data['event'] == 'payment.captured') {
    $payment = $webhook_data['payload']['payment']['entity'];
    $payment_id = $payment['id'];
    $notes = $payment['notes'];
    
    if (isset($notes['registration_id'])) {
        $registration_id = $notes['registration_id'];
        
        // Update payment status
        $update_sql = "UPDATE registrations 
                       SET payment_status = 'success', 
                           payment_id = '$payment_id' 
                       WHERE id = $registration_id";
        
        mysqli_query($conn, $update_sql);
    }
}

// Return 200 OK
http_response_code(200);
echo json_encode(['status' => 'ok']);
?>