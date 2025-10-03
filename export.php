<?php
require_once '../config/settings.php';
require_once '../includes/functions.php';
check_admin();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="attendees_' . date('Y-m-d_H-i-s') . '.csv"');

// Create file pointer
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, [
    'Registration ID',
    'Unique ID',
    'Type',
    'Name',
    'Email',
    'Phone',
    'Total Tickets',
    'Additional Attendees',
    'Total Amount',
    'Payment Status',
    'Payment ID',
    'Attended',
    'Check-in Time',
    'Registration Date'
]);

// Get all primary registrations
$sql = "SELECT * FROM registrations WHERE payment_status = 'success' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    // Add primary registrant
    fputcsv($output, [
        $row['id'],
        $row['unique_id'],
        'Primary',
        $row['primary_name'],
        $row['primary_email'],
        $row['primary_phone'],
        1 + $row['additional_attendees'],
        $row['additional_attendees'],
        $row['total_amount'],
        $row['payment_status'],
        $row['payment_id'],
        $row['attended'] == 1 ? 'Yes' : 'No',
        $row['attended_at'] ?: 'Not checked in',
        $row['registration_date']
    ]);
    
    // Get additional attendees for this registration
    $sql2 = "SELECT * FROM attendees WHERE registration_id = " . $row['id'];
    $result2 = mysqli_query($conn, $sql2);
    
    while ($attendee = mysqli_fetch_assoc($result2)) {
        fputcsv($output, [
            $row['id'],
            $attendee['attendee_unique_id'],
            'Guest',
            $attendee['attendee_name'],
            $attendee['attendee_email'] ?: $row['primary_email'],
            '-',
            '-',
            '-',
            '-',
            'Included',
            '-',
            $attendee['attended'] == 1 ? 'Yes' : 'No',
            $attendee['attended_at'] ?: 'Not checked in',
            $row['registration_date']
        ]);
    }
}

fclose($output);
exit();
?>