<?php
session_start();
header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Include existing database config
require_once '../config/database.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['unique_id'])) {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
    exit;
}

$unique_id = mysqli_real_escape_string($conn, strtoupper(trim($input['unique_id'])));

// First check in registrations table
$query = "SELECT * FROM registrations WHERE unique_id = '$unique_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    // Found in main registrations
    $row = mysqli_fetch_assoc($result);
    
    // Check if already attended
    if ($row['attended'] == 1) {
        $check_in_time = $row['attended_at'] ? date('d M Y, h:i A', strtotime($row['attended_at'])) : 'Previously';
        echo json_encode([
            'success' => false,
            'already_checked' => true,
            'message' => 'This person has already checked in',
            'check_in_time' => $check_in_time,
            'name' => $row['primary_name']
        ]);
        exit;
    }
    
    // Update attendance status
    $update_query = "UPDATE registrations SET attended = 1, attended_at = NOW() WHERE unique_id = '$unique_id'";
    
    if (mysqli_query($conn, $update_query)) {
        echo json_encode([
            'success' => true,
            'unique_id' => $unique_id,
            'name' => $row['primary_name'],
            'email' => $row['primary_email'],
            'phone' => $row['primary_phone'],
            'check_in_time' => date('d M Y, h:i A'),
            'message' => 'Check-in successful!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update attendance status'
        ]);
    }
    
} else {
    // Check in attendees table (for additional attendees)
    $query2 = "SELECT a.*, r.primary_name as main_registrant_name, r.unique_id as main_id 
               FROM attendees a 
               LEFT JOIN registrations r ON a.registration_id = r.id 
               WHERE a.attendee_unique_id = '$unique_id'";
    
    $result2 = mysqli_query($conn, $query2);
    
    if (mysqli_num_rows($result2) > 0) {
        $row = mysqli_fetch_assoc($result2);
        
        // Check if already attended
        if ($row['attended'] == 1) {
            $check_in_time = $row['attended_at'] ? date('d M Y, h:i A', strtotime($row['attended_at'])) : 'Previously';
            echo json_encode([
                'success' => false,
                'already_checked' => true,
                'message' => 'This attendee has already checked in',
                'check_in_time' => $check_in_time,
                'name' => $row['attendee_name']
            ]);
            exit;
        }
        
        // Update attendance status for attendee
        $update_query = "UPDATE attendees SET attended = 1, attended_at = NOW() WHERE attendee_unique_id = '$unique_id'";
        
        if (mysqli_query($conn, $update_query)) {
            echo json_encode([
                'success' => true,
                'unique_id' => $unique_id,
                'name' => $row['attendee_name'] . ' (Guest of ' . $row['main_registrant_name'] . ')',
                'email' => $row['attendee_email'],
                'check_in_time' => date('d M Y, h:i A'),
                'message' => 'Guest check-in successful!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update attendee status'
            ]);
        }
        
    } else {
        // ID not found in either table
        echo json_encode([
            'success' => false,
            'message' => 'Invalid ID - This ticket ID is not registered in our system',
            'unique_id' => $unique_id
        ]);
    }
}

mysqli_close($conn);
?>