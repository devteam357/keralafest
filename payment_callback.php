<?php
// api/payment_callback.php - Handle PhonePe payment callback

session_start();
require_once '../config/database.php';
require_once '../config/settings.php';
require_once '../includes/phonepe.php';
require_once '../includes/functions.php';
require_once '../includes/qr_generator.php';

// Log callback for debugging
if (TEST_MODE) {
    error_log("PhonePe Callback received: " . print_r($_REQUEST, true));
    error_log("Headers: " . print_r(getallheaders(), true));
}

// Get order ID from query parameter
$order_id = $_GET['order_id'] ?? '';

if (empty($order_id)) {
    $_SESSION['error'] = "Invalid payment response";
    header('Location: ../index.php');
    exit();
}

// Initialize PhonePe
$phonePe = new PhonePePayment();

// Get transaction ID from the order_id (which is our unique_id)
$transaction_id = $order_id;

// Check payment status from PhonePe
$statusResult = $phonePe->checkPaymentStatus($transaction_id);

if ($statusResult['success']) {
    $payment_status = $statusResult['status'];
    $payment_data = $statusResult['data'];
    
    // Get registration details
    $query = "SELECT * FROM registrations WHERE unique_id = '$transaction_id'";
    $result = mysqli_query($conn, $query);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        $_SESSION['error'] = "Registration not found";
        header('Location: ../index.php');
        exit();
    }
    
    $registration = mysqli_fetch_assoc($result);
    
    // PhonePe payment states
    switch ($payment_status) {
        case 'COMPLETED':
            // Payment successful
            handleSuccessfulPayment($conn, $registration, $payment_data);
            break;
            
        case 'FAILED':
        case 'DECLINED':
            // Payment failed
            handleFailedPayment($conn, $registration, $payment_data);
            break;
            
        case 'PENDING':
            // Payment pending
            $_SESSION['warning'] = "Payment is still being processed. Please wait.";
            header('Location: ../payment.php');
            exit();
            break;
            
        default:
            $_SESSION['error'] = "Unknown payment status";
            header('Location: ../index.php');
            exit();
    }
} else {
    $_SESSION['error'] = "Unable to verify payment status";
    header('Location: ../index.php');
    exit();
}

/**
 * Handle successful payment
 */
function handleSuccessfulPayment($conn, $registration, $payment_data) {
    global $transaction_id;
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Update registration with payment success
        $phonepe_transaction_id = $payment_data['transactionId'] ?? '';
        $payment_method = $payment_data['paymentInstrument']['type'] ?? 'UNKNOWN';
        
        $update_reg = "UPDATE registrations SET 
                       payment_status = 'success',
                       payment_id = '$phonepe_transaction_id'
                       WHERE id = " . $registration['id'];
        
        if (!mysqli_query($conn, $update_reg)) {
            throw new Exception("Failed to update registration");
        }
        
        // Generate attendee unique ID (KERF format)
        $attendee_unique_id = generateAttendeeId($conn);
        
        // Generate QR code content
        $qr_content = json_encode([
            'attendee_id' => $attendee_unique_id,
            'registration_id' => $registration['id'],
            'name' => $registration['name'],
            'email' => $registration['email'],
            'phone' => $registration['phone'],
            'event' => EVENT_NAME,
            'valid_date' => EVENT_DATE
        ]);
        
        // Generate QR code (you'll need to implement this based on your QR library)
        $qr_code_path = generateQRCode($qr_content, $attendee_unique_id);
        
        // Create attendee record
        $insert_attendee = "INSERT INTO attendees (
            registration_id,
            attendee_unique_id,
            attendee_name,
            attendee_email,
            qr_code,
            attended,
            attended_at
        ) VALUES (
            " . $registration['id'] . ",
            '$attendee_unique_id',
            '" . mysqli_real_escape_string($conn, $registration['name']) . "',
            '" . mysqli_real_escape_string($conn, $registration['email']) . "',
            '$qr_code_path',
            0,
            NULL
        )";
        
        if (!mysqli_query($conn, $insert_attendee)) {
            throw new Exception("Failed to create attendee record");
        }
        
        // Add email to queue for confirmation
        $email_content = getConfirmationEmailContent($registration, $attendee_unique_id);
        $insert_email = "INSERT INTO email_queue (
            recipient_email,
            email_type,
            unique_id,
            sent,
            sent_at,
            created_at
        ) VALUES (
            '" . $registration['email'] . "',
            'confirmation',
            '$attendee_unique_id',
            0,
            NULL,
            NOW()
        )";
        
        mysqli_query($conn, $insert_email);
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Store success data in session
        $_SESSION['payment_success'] = [
            'attendee_id' => $attendee_unique_id,
            'name' => $registration['name'],
            'email' => $registration['email'],
            'amount' => $registration['total_amount'],
            'transaction_id' => $phonepe_transaction_id,
            'qr_code' => $qr_code_path
        ];
        
        // Clear registration session
        unset($_SESSION['registration']);
        
        // Redirect to thank you page
        header('Location: ../thank-you.php');
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        
        // Log error
        error_log("Payment processing error: " . $e->getMessage());
        
        $_SESSION['error'] = "Payment received but ticket generation failed. Please contact support.";
        header('Location: ../index.php');
        exit();
    }
}

/**
 * Handle failed payment
 */
function handleFailedPayment($conn, $registration, $payment_data) {
    // Update registration with payment failure
    $update_reg = "UPDATE registrations SET 
                   payment_status = 'failed'
                   WHERE id = " . $registration['id'];
    
    mysqli_query($conn, $update_reg);
    
    $_SESSION['error'] = "Payment failed. Please try again.";
    $_SESSION['form_data'] = [
        'name' => $registration['name'],
        'email' => $registration['email'],
        'phone' => $registration['phone'],
        'additional_attendees' => $registration['additional_attendees']
    ];
    
    // Clear registration session
    unset($_SESSION['registration']);
    
    header('Location: ../index.php');
    exit();
}

/**
 * Generate confirmation email content
 */
function getConfirmationEmailContent($registration, $attendee_id) {
    $content = "
    <h2>Registration Confirmed - Kerala Food Fest 2025</h2>
    <p>Dear {$registration['name']},</p>
    <p>Your registration for Kerala Food Fest 2025 has been confirmed!</p>
    
    <h3>Registration Details:</h3>
    <ul>
        <li><strong>Attendee ID:</strong> {$attendee_id}</li>
        <li><strong>Name:</strong> {$registration['name']}</li>
        <li><strong>Email:</strong> {$registration['email']}</li>
        <li><strong>Phone:</strong> {$registration['phone']}</li>
        <li><strong>Number of Attendees:</strong> " . (1 + $registration['additional_attendees']) . "</li>
        <li><strong>Amount Paid:</strong> ₹{$registration['total_amount']}</li>
    </ul>
    
    <h3>Event Details:</h3>
    <ul>
        <li><strong>Event:</strong> " . EVENT_NAME . "</li>
        <li><strong>Date:</strong> " . EVENT_DATE . "</li>
        <li><strong>Time:</strong> " . EVENT_TIME . "</li>
        <li><strong>Venue:</strong> " . EVENT_VENUE . "</li>
    </ul>
    
    <p>Please show your QR code at the venue for entry.</p>
    <p>We look forward to seeing you at the event!</p>
    
    <p>Best regards,<br>Kerala Food Fest Team</p>
    ";
    
    return $content;
}
?>