<?php
// api/get_sliders.php - Fetch active sliders for display

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';
require_once '../config/settings.php';

try {
    // Get active sliders ordered by display_order
    $query = "SELECT 
                id,
                title,
                description,
                media_type,
                desktop_file,
                mobile_file,
                display_order
              FROM sliders 
              WHERE is_active = 1 
              ORDER BY display_order ASC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($conn));
    }
    
    $sliders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Add full URL paths for files
        $row['desktop_url'] = SITE_URL . '/' . $row['desktop_file'];
        $row['mobile_url'] = SITE_URL . '/' . $row['mobile_file'];
        
        $sliders[] = $row;
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'count' => count($sliders),
        'sliders' => $sliders
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