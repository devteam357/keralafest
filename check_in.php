// ============================================
// FILE 2: api/check_in.php
// ============================================
<?php
header('Content-Type: application/json');
require_once '../config/settings.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['ticket_id'])) {
    echo json_encode(['success' => false, 'message' => 'No ticket ID provided']);
    exit;
}

$ticket_id = strtoupper(trim($input['ticket_id']));
$action = $input['action'] ?? 'check_in';

// Check if ticket exists in database
$check_query = "SELECT * FROM registrations WHERE unique_id = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "s", $ticket_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ticket ID']);
    exit;
}

$attendee = mysqli_fetch_assoc($result);

// Check if already checked in
if ($attendee['attended'] == 1) {
    echo json_encode([
        'success' => false,
        'already_checked' => true,
        'message' => 'Already checked in',
        'check_in_time' => $attendee['check_in_time'] ? date('d M Y, h:i A', strtotime($attendee['check_in_time'])) : 'Unknown time'
    ]);
    exit;
}

// Update check-in status
$update_query = "UPDATE registrations SET 
                 attended = 1, 
                 check_in_time = NOW() 
                 WHERE unique_id = ?";
                 
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "s", $ticket_id);

if (mysqli_stmt_execute($stmt)) {
    // Log the check-in
    $log_query = "INSERT INTO check_in_logs (ticket_id, attendee_name, check_in_time, checked_by) 
                  VALUES (?, ?, NOW(), ?)";
    $log_stmt = mysqli_prepare($conn, $log_query);
    $admin_id = $_SESSION['admin_id'] ?? 'admin';
    mysqli_stmt_bind_param($log_stmt, "sss", $ticket_id, $attendee['primary_name'], $admin_id);
    mysqli_stmt_execute($log_stmt);
    
    echo json_encode([
        'success' => true,
        'ticket_id' => $ticket_id,
        'name' => $attendee['primary_name'],
        'check_in_time' => date('d M Y, h:i A')
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
}

mysqli_close($conn);
?>