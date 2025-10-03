<?php
// api/register.php - Updated registration with date selection support

require_once '../config/database.php';
require_once '../config/settings.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

// Get form data
$name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
$email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
$additional_count = isset($_POST['additional_count']) ? (int)$_POST['additional_count'] : 0;

// NEW: Get selected dates
$selected_dates = isset($_POST['dates']) && is_array($_POST['dates']) ? $_POST['dates'] : [];

// Validate required fields
if (empty($name) || empty($email) || empty($phone)) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: ../index.php');
    exit();
}

// Validate date selection
if (empty($selected_dates)) {
    $_SESSION['error'] = 'Please select at least one date to attend.';
    header('Location: ../index.php');
    exit();
}

// Validate selected dates are actual event dates
foreach ($selected_dates as $date) {
    if (!in_array($date, EVENT_DATES)) {
        $_SESSION['error'] = 'Invalid date selected.';
        header('Location: ../index.php');
        exit();
    }
}

// Calculate pricing
$total_days = count($selected_dates);
$total_people = 1 + $additional_count;
$price_per_day = PRICE_PER_DAY;
$total_amount = calculateTotalAmount($selected_dates, $additional_count);

// Generate unique registration ID
$unique_id = generateRegistrationId($conn);

// Store selected dates as JSON
$selected_dates_json = json_encode($selected_dates);

// Insert registration
$registration_date = date('Y-m-d H:i:s');

$insert_query = "INSERT INTO registrations (
    unique_id,
    name,
    email,
    phone,
    primary_name,
    primary_email,
    primary_phone,
    selected_dates,
    total_days,
    price_per_day,
    additional_attendees,
    total_amount,
    payment_status,
    registration_date
) VALUES (
    '$unique_id',
    '$name',
    '$email',
    '$phone',
    '$name',
    '$email',
    '$phone',
    '$selected_dates_json',
    $total_days,
    $price_per_day,
    $additional_count,
    $total_amount,
    'pending',
    '$registration_date'
)";

if (mysqli_query($conn, $insert_query)) {
    $registration_id = mysqli_insert_id($conn);
    
    // Log for debugging in test mode
    if (TEST_MODE) {
        error_log("Registration created: ID=$registration_id, Unique=$unique_id, Days=$total_days, Amount=$total_amount");
    }
    
    // Redirect to payment page
    header('Location: ../payment.php?registration_id=' . $registration_id);
    exit();
    
} else {
    // Log error in test mode
    if (TEST_MODE) {
        error_log("Registration failed: " . mysqli_error($conn));
    }
    
    $_SESSION['error'] = 'Registration failed. Please try again.';
    header('Location: ../index.php');
    exit();
}
?>