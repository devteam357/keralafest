<?php
// includes/qr_generator.php - Complete QR Code Generation Functions

/**
 * Generate QR Code URL using QR Server API (primary method - works on your server)
 */
function generateQRCode($unique_id) {
    // Make sure SITE_URL is defined
    if (!defined('SITE_URL')) {
        $site_url = 'https://keralafest.in';
    } else {
        $site_url = SITE_URL;
    }
    
    // QR data points to admin scan page with unique ID
    $qr_data = $site_url . "/admin/scan.php?id=" . $unique_id;
    
    // Generate QR URL using QR Server API (confirmed working on your server)
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qr_data);
    
    return $qr_url;
}

/**
 * Alternative: Generate QR using QuickChart.io API (backup method - also works)
 */
function generateQRCodeAlt($unique_id) {
    if (!defined('SITE_URL')) {
        $site_url = 'https://keralafest.in';
    } else {
        $site_url = SITE_URL;
    }
    
    $qr_data = $site_url . "/admin/scan.php?id=" . $unique_id;
    $qr_url = "https://quickchart.io/qr?text=" . urlencode($qr_data) . "&size=300";
    
    return $qr_url;
}

/**
 * Generate QR Code with custom parameters
 */
function generateQRCodeCustom($unique_id, $size = 300, $margin = 0) {
    if (!defined('SITE_URL')) {
        $site_url = 'https://keralafest.in';
    } else {
        $site_url = SITE_URL;
    }
    
    $qr_data = $site_url . "/admin/scan.php?id=" . $unique_id;
    
    // Using QR Server API with custom size
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?";
    $qr_url .= "size=" . $size . "x" . $size;
    $qr_url .= "&data=" . urlencode($qr_data);
    if ($margin > 0) {
        $qr_url .= "&margin=" . $margin;
    }
    
    return $qr_url;
}

/**
 * Generate QR Code for simple text
 */
function generateSimpleQR($text, $size = 300) {
    // Just encode the text directly
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=" . $size . "x" . $size . "&data=" . urlencode($text);
    return $qr_url;
}

/**
 * Generate attendee ID (KERF format)
 */
if (!function_exists('generateAttendeeId')) {
    function generateAttendeeId($conn) {
        $query = "SELECT COUNT(*) as count FROM attendees";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'] + 1;
        return 'KERF' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}

/**
 * Generate registration ID
 */
if (!function_exists('generateRegistrationId')) {
    function generateRegistrationId($conn) {
        $date = date('Ymd');
        $query = "SELECT COUNT(*) as count FROM registrations WHERE DATE(registration_date) = CURDATE()";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'] + 1;
        return 'REG' . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

/**
 * Verify QR code data (for scanning)
 */
function verifyQRCode($qr_data, $conn) {
    // Extract ID from QR data
    if (strpos($qr_data, 'scan.php?id=') !== false) {
        // Full URL format
        $parts = parse_url($qr_data);
        parse_str($parts['query'], $query);
        $attendee_id = $query['id'] ?? '';
    } else {
        // Simple ID format
        $attendee_id = $qr_data;
    }
    
    if (empty($attendee_id)) {
        return ['valid' => false, 'message' => 'Invalid QR code format'];
    }
    
    // Check if attendee exists
    $attendee_id = mysqli_real_escape_string($conn, $attendee_id);
    
    $query = "SELECT a.*, r.* 
              FROM attendees a 
              JOIN registrations r ON a.registration_id = r.id 
              WHERE a.attendee_unique_id = '$attendee_id'";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return ['valid' => false, 'message' => 'Ticket not found'];
    }
    
    $attendee = mysqli_fetch_assoc($result);
    
    // Check if already checked in
    if ($attendee['attended'] == 1) {
        return [
            'valid' => false,
            'message' => 'Already checked in at ' . $attendee['attended_at'],
            'data' => $attendee
        ];
    }
    
    return [
        'valid' => true,
        'message' => 'Valid ticket',
        'data' => $attendee
    ];
}

/**
 * Mark attendee as checked in
 */
function checkInAttendee($attendee_id, $conn) {
    $attendee_id = mysqli_real_escape_string($conn, $attendee_id);
    $current_time = date('Y-m-d H:i:s');
    
    $query = "UPDATE attendees 
              SET attended = 1, 
                  attended_at = '$current_time' 
              WHERE attendee_unique_id = '$attendee_id' 
              AND attended = 0";
    
    if (mysqli_query($conn, $query)) {
        if (mysqli_affected_rows($conn) > 0) {
            return ['success' => true, 'message' => 'Check-in successful at ' . $current_time];
        } else {
            return ['success' => false, 'message' => 'Already checked in or ticket not found'];
        }
    } else {
        return ['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)];
    }
}
?>