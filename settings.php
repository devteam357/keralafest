<?php
// config/settings.php - Updated for Kerala Fest Pratibimb with new features

require_once 'database.php';

// Set timezone to Indian
date_default_timezone_set('Asia/Kolkata');

// TESTING MODE - Set to true for testing, false for production
define('TEST_MODE', true);

// Get settings from database
$settings_array = array();
$settings_query = "SELECT setting_key, setting_value FROM settings";
$settings_result = mysqli_query($conn, $settings_query);

if ($settings_result && mysqli_num_rows($settings_result) > 0) {
    while($row = mysqli_fetch_assoc($settings_result)) {
        $settings_array[$row['setting_key']] = $row['setting_value'];
    }
}

// Core settings
define('SITE_URL', rtrim($settings_array['site_url'] ?? 'https://keralafest.in', '/'));
define('EVENT_NAME', $settings_array['event_name'] ?? 'Kerala Fest Pratibimb');

// Event configuration
define('EVENT_DATE', $settings_array['event_date'] ?? '13-16 November 2025');
define('EVENT_TIME', $settings_array['event_time'] ?? '9:00 AM - 10:00 PM');
define('EVENT_VENUE', $settings_array['event_venue'] ?? 'Bittan Market Ground, Bhopal');
define('EVENT_PREFIX', $settings_array['event_prefix'] ?? 'KERF');

// Event dates array (NEW - from JSON in database)
$event_dates_json = $settings_array['event_dates'] ?? '["2025-11-13","2025-11-14","2025-11-15","2025-11-16"]';
define('EVENT_DATES_JSON', $event_dates_json);
define('EVENT_DATES', json_decode($event_dates_json, true) ?: []);

// Pricing (NEW - from database settings)
$price_per_day = $settings_array['price_per_day'] ?? $settings_array['ticket_price'] ?? '20';
define('PRICE_PER_DAY', (float)$price_per_day);
define('TICKET_PRICE', PRICE_PER_DAY); // For backward compatibility

// Payment Gateway
if (TEST_MODE) {
    define('PHONEPE_MODE', 'UAT');
    define('PHONEPE_MERCHANT_ID', 'PGTESTPAYUAT86');
    define('PHONEPE_SALT_KEY', '96434309-7796-489d-8924-ab56988a6076');
    define('PHONEPE_SALT_INDEX', 1);
    define('PHONEPE_HOST_URL', 'https://api-preprod.phonepe.com/apis/pg-sandbox');
} else {
    define('PHONEPE_MODE', 'PRODUCTION');
    define('PHONEPE_MERCHANT_ID', $settings_array['phonepe_merchant_id'] ?? '');
    define('PHONEPE_SALT_KEY', $settings_array['phonepe_salt_key'] ?? '');
    define('PHONEPE_SALT_INDEX', (int)($settings_array['phonepe_salt_index'] ?? 1));
    define('PHONEPE_HOST_URL', 'https://api.phonepe.com/apis/hermes');
}

// Payment URLs
define('PAYMENT_CALLBACK_URL', SITE_URL . '/api/payment_callback.php');
define('PAYMENT_WEBHOOK_URL', SITE_URL . '/api/payment_webhook.php');
define('PAYMENT_REDIRECT_URL', SITE_URL . '/thank-you.php');

// Email Configuration
define('SMTP_HOST', $settings_array['smtp_host'] ?? 'smtp.gmail.com');
define('SMTP_PORT', (int)($settings_array['smtp_port'] ?? 587));
define('SMTP_USERNAME', $settings_array['smtp_username'] ?? '');
define('SMTP_PASSWORD', $settings_array['smtp_password'] ?? '');
define('SMTP_FROM_EMAIL', $settings_array['smtp_from_email'] ?? 'noreply@keralafest.in');
define('SMTP_FROM_NAME', $settings_array['smtp_from_name'] ?? EVENT_NAME);
define('SMTP_ENCRYPTION', $settings_array['smtp_encryption'] ?? 'tls');

// QR Code Settings
define('QR_CODE_SIZE', (int)($settings_array['qr_code_size'] ?? 300));

// Upload Configuration
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('SLIDER_UPLOAD_DIR', UPLOAD_DIR . 'sliders/');
define('LOGO_UPLOAD_DIR', UPLOAD_DIR . 'logos/');

// File size limits (bytes)
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_VIDEO_SIZE', 50 * 1024 * 1024); // 50MB

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'webm', 'ogg']);

// Test configuration
if (TEST_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    define('SKIP_PAYMENT', false);
    define('SKIP_EMAIL', true);
    define('AUTO_FILL_FORMS', true);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    define('SKIP_PAYMENT', false);
    define('SKIP_EMAIL', false);
    define('AUTO_FILL_FORMS', false);
}

// ============================================
// Helper Functions
// ============================================

/**
 * Calculate total amount based on dates and attendees
 * Formula: (Number of People) × (Number of Days) × (Price Per Day)
 */
function calculateTotalAmount($selected_dates, $additional_attendees = 0) {
    $total_people = 1 + (int)$additional_attendees;
    $total_days = is_array($selected_dates) ? count($selected_dates) : (int)$selected_dates;
    
    if ($total_days < 1) $total_days = 1;
    
    return $total_people * $total_days * PRICE_PER_DAY;
}

/**
 * Generate unique registration ID
 */
function generateRegistrationId($conn) {
    $date = date('Y-m-d');
    $query = "SELECT COUNT(*) as count FROM registrations WHERE DATE(registration_date) = '$date'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $count = $row['count'] + 1;
    
    return 'REG' . date('Ymd') . str_pad($count, 4, '0', STR_PAD_LEFT);
}

/**
 * Generate unique attendee ID
 */
function generateAttendeeId($conn) {
    $query = "SELECT COUNT(*) as count FROM attendees";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $count = $row['count'] + 1;
    
    return EVENT_PREFIX . str_pad($count, 5, '0', STR_PAD_LEFT);
}

/**
 * Format date for display
 */
function formatEventDate($date) {
    return date('d M Y', strtotime($date));
}

/**
 * Get event dates with labels for display
 */
function getEventDatesWithLabels() {
    $dates = EVENT_DATES;
    $result = [];
    
    foreach ($dates as $date) {
        $result[] = [
            'value' => $date,
            'day' => date('d', strtotime($date)),
            'month' => date('F', strtotime($date)),
            'year' => date('Y', strtotime($date)),
            'label' => date('d F Y', strtotime($date)),
            'short_label' => date('d M', strtotime($date))
        ];
    }
    
    return $result;
}

/**
 * Check if date is an event date
 */
function isEventDate($date) {
    return in_array($date, EVENT_DATES);
}

/**
 * Get or update setting
 */
function getSetting($conn, $key, $default = null) {
    $key = mysqli_real_escape_string($conn, $key);
    $query = "SELECT setting_value FROM settings WHERE setting_key = '$key'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['setting_value'];
    }
    
    return $default;
}

function updateSetting($conn, $key, $value) {
    $key = mysqli_real_escape_string($conn, $key);
    $value = mysqli_real_escape_string($conn, $value);
    
    $query = "INSERT INTO settings (setting_key, setting_value, updated_at) 
              VALUES ('$key', '$value', NOW()) 
              ON DUPLICATE KEY UPDATE 
              setting_value = '$value', updated_at = NOW()";
    
    return mysqli_query($conn, $query);
}

/**
 * Validate uploaded file
 */
function validateUploadedFile($file, $type = 'image') {
    $allowed_types = ($type === 'image') ? ALLOWED_IMAGE_TYPES : ALLOWED_VIDEO_TYPES;
    $max_size = ($type === 'image') ? MAX_IMAGE_SIZE : MAX_VIDEO_SIZE;
    
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    if ($file['size'] > $max_size) {
        $max_mb = round($max_size / (1024 * 1024), 1);
        return ['success' => false, 'error' => "File too large. Maximum {$max_mb}MB allowed"];
    }
    
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_types)];
    }
    
    return ['success' => true, 'extension' => $file_ext];
}

/**
 * Save uploaded file
 */
function saveUploadedFile($file, $destination_dir, $new_filename = null) {
    $validation = validateUploadedFile($file, strpos($file['type'], 'video') !== false ? 'video' : 'image');
    
    if (!$validation['success']) {
        return $validation;
    }
    
    if (!file_exists($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }
    
    $filename = $new_filename ?? (uniqid() . '.' . $validation['extension']);
    $destination = $destination_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename, 'path' => $destination];
    }
    
    return ['success' => false, 'error' => 'Failed to save file'];
}

/**
 * Ensure upload directories exist
 */
function ensureUploadDirectories() {
    $directories = [
        UPLOAD_DIR,
        SLIDER_UPLOAD_DIR . 'desktop/',
        SLIDER_UPLOAD_DIR . 'mobile/',
        LOGO_UPLOAD_DIR . 'title/',
        LOGO_UPLOAD_DIR . 'powered_by/',
        LOGO_UPLOAD_DIR . 'official_partner/',
        LOGO_UPLOAD_DIR . 'supported_by/',
        LOGO_UPLOAD_DIR . 'association/',
        LOGO_UPLOAD_DIR . 'partners/'
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

/**
 * Get active sliders
 */
function getActiveSliders($conn) {
    $query = "SELECT * FROM sliders WHERE is_active = 1 ORDER BY display_order ASC";
    $result = mysqli_query($conn, $query);
    $sliders = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sliders[] = $row;
        }
    }
    
    return $sliders;
}

/**
 * Get sponsor logos by category
 */
function getSponsorLogos($conn, $category = null) {
    if ($category) {
        $category = mysqli_real_escape_string($conn, $category);
        $query = "SELECT * FROM sponsor_logos WHERE category = '$category' AND is_active = 1 ORDER BY display_order ASC";
    } else {
        $query = "SELECT * FROM sponsor_logos WHERE is_active = 1 ORDER BY category, display_order ASC";
    }
    
    $result = mysqli_query($conn, $query);
    $logos = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($category) {
                $logos[] = $row;
            } else {
                $logos[$row['category']][] = $row;
            }
        }
    }
    
    return $logos;
}

/**
 * Get minute updates for a specific date
 */
function getMinuteUpdates($conn, $date = null) {
    if (!$date) {
        $date = date('Y-m-d');
    }
    
    $date = mysqli_real_escape_string($conn, $date);
    $query = "SELECT * FROM minute_updates 
              WHERE update_date = '$date' AND is_active = 1 
              ORDER BY update_time DESC";
    
    $result = mysqli_query($conn, $query);
    $updates = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $updates[] = $row;
        }
    }
    
    return $updates;
}

// Initialize upload directories
ensureUploadDirectories();
?>