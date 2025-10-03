<?php
// api/get_minute_updates.php - Fetch minute-to-minute updates

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';
require_once '../config/settings.php';

try {
    // Get date from query parameter or use today
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    $date = mysqli_real_escape_string($conn, $date);
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        throw new Exception('Invalid date format. Use YYYY-MM-DD');
    }
    
    // Get active updates for the specified date
    $query = "SELECT 
                id,
                update_date,
                update_time,
                update_text,
                DATE_FORMAT(update_time, '%h:%i %p') as formatted_time,
                DATE_FORMAT(update_date, '%d %b %Y') as formatted_date
              FROM minute_updates 
              WHERE update_date = '$date' AND is_active = 1 
              ORDER BY update_time DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($conn));
    }
    
    $updates = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $updates[] = $row;
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'date' => $date,
        'formatted_date' => date('d F Y', strtotime($date)),
        'count' => count($updates),
        'updates' => $updates
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>