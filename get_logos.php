<?php
// api/get_logos.php - Fetch sponsor logos organized by category

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';
require_once '../config/settings.php';

try {
    // Get all active sponsor logos
    $query = "SELECT 
                id,
                category,
                sponsor_name,
                logo_file,
                website_url,
                display_order
              FROM sponsor_logos 
              WHERE is_active = 1 
              ORDER BY category, display_order ASC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($conn));
    }
    
    // Organize logos by category
    $logos = [
        'title' => [],
        'powered_by' => [],
        'official_partner' => [],
        'supported_by' => [],
        'association' => [],
        'radio_partner' => [],
        'digital_partner' => []
    ];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Add full URL path for logo
        $row['logo_url'] = SITE_URL . '/' . $row['logo_file'];
        
        $category = $row['category'];
        if (isset($logos[$category])) {
            $logos[$category][] = $row;
        }
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'logos' => $logos,
        'counts' => [
            'title' => count($logos['title']),
            'powered_by' => count($logos['powered_by']),
            'official_partner' => count($logos['official_partner']),
            'supported_by' => count($logos['supported_by']),
            'association' => count($logos['association']),
            'radio_partner' => count($logos['radio_partner']),
            'digital_partner' => count($logos['digital_partner'])
        ]
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