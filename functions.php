<?php
// Generate unique ID
function generateUniqueId() {
    return 'EVT' . date('Y') . strtoupper(substr(md5(uniqid()), 0, 8));
}

// Clean input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if admin is logged in
function check_admin() {
    session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        header("Location: login.php");
        exit();
    }
}

// Format Indian currency
function format_currency($amount) {
    return '₹' . number_format($amount, 2);
}

// Get total stats - UPDATED with correct column names
function get_dashboard_stats($conn) {
    $stats = array();
    
    // Total tickets sold (using correct column name: additional_attendees)
    $query = "SELECT SUM(1 + additional_attendees) as total 
              FROM registrations 
              WHERE payment_status = 'success'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['tickets'] = $row['total'] ?: 0;
    } else {
        $stats['tickets'] = 0;
    }
    
    // Total attendees (registrations count)
    $query = "SELECT COUNT(*) as total 
              FROM registrations 
              WHERE payment_status = 'success'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['attendees'] = $row['total'] ?: 0;
    } else {
        $stats['attendees'] = 0;
    }
    
    // Total revenue
    $query = "SELECT SUM(total_amount) as total 
              FROM registrations 
              WHERE payment_status = 'success'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $stats['revenue'] = $row['total'] ?: 0;
    } else {
        $stats['revenue'] = 0;
    }
    
    return $stats;
}
?>