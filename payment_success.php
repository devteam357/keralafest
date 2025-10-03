<?php
session_start();
require_once '../config/settings.php';

$registration_id = $_GET['registration_id'] ?? 0;
$payment_id = $_GET['payment_id'] ?? '';

if ($registration_id && $payment_id) {
    // Update payment status
    $update_sql = "UPDATE registrations 
                   SET payment_status = 'success', 
                       payment_id = '" . mysqli_real_escape_string($conn, $payment_id) . "' 
                   WHERE id = " . (int)$registration_id;
    
    mysqli_query($conn, $update_sql);
}

header("Location: ../thank-you.php?registration_id=$registration_id");
exit();
?>