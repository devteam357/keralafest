<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');

// Get checked-in count from registrations
$sql1 = "SELECT COUNT(*) as count FROM registrations WHERE attended = 1 OR attended = 'Yes'";
$result1 = mysqli_query($conn, $sql1);
$primary_checked = mysqli_fetch_assoc($result1)['count'];

// Get checked-in count from attendees
$sql2 = "SELECT COUNT(*) as count FROM attendees WHERE attended = 1 OR attended = 'Yes'";
$result2 = mysqli_query($conn, $sql2);
$additional_checked = mysqli_fetch_assoc($result2)['count'];

// Get total registered
$sql3 = "SELECT 
         (SELECT COUNT(*) FROM registrations WHERE payment_status = 'success') as primary_total,
         (SELECT COUNT(*) FROM attendees a 
          JOIN registrations r ON a.registration_id = r.id 
          WHERE r.payment_status = 'success') as additional_total";
$result3 = mysqli_query($conn, $sql3);
$totals = mysqli_fetch_assoc($result3);

$total_checked = $primary_checked + $additional_checked;
$total_registered = $totals['primary_total'] + $totals['additional_total'];
$remaining = $total_registered - $total_checked;

echo json_encode([
    'checked_in' => $total_checked,
    'remaining' => $remaining,
    'total' => $total_registered,
    'percentage' => $total_registered > 0 ? round(($total_checked / $total_registered) * 100) : 0
]);
?>