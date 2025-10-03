<?php
// thank-you.php - Fixed version with multiple attendees support and working QR codes

require_once 'config/database.php';
require_once 'config/settings.php';

// Get attendee_id from URL (can be comma-separated for multiple attendees)
$attendee_ids = isset($_GET['attendee_id']) ? $_GET['attendee_id'] : '';

if (empty($attendee_ids)) {
    header('Location: index.php');
    exit();
}

// Handle multiple attendee IDs
$ids_array = explode(',', $attendee_ids);
$attendees_data = array();

// Fetch data for each attendee
foreach ($ids_array as $attendee_id) {
    $attendee_id = trim($attendee_id);
    
    $query = "SELECT a.*, r.* 
              FROM attendees a 
              JOIN registrations r ON a.registration_id = r.id 
              WHERE a.attendee_unique_id = '" . mysqli_real_escape_string($conn, $attendee_id) . "'";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        // Generate QR code URL using QR Server API (which works on your server)
        $qr_data = "https://keralafest.in/admin/scan.php?id=" . $attendee_id;
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qr_data);
        
        $data['qr_url'] = $qr_url;
        $data['attendee_id'] = $attendee_id;
        $attendees_data[] = $data;
    }
}

if (empty($attendees_data)) {
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error - Kerala Food Fest 2025</title>
        <style>
            body { font-family: Arial; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f0f0f0; }
            .error-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
            .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h2>Invalid Ticket ID</h2>
            <p>The ticket ID provided is not valid.</p>
            <a href="index.php" class="btn">Go to Home</a>
        </div>
    </body>
    </html>
    ');
}

// Use first attendee's data for common fields
$main_data = $attendees_data[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - Kerala Food Fest 2025</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideDown 0.5s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .success-icon {
            font-size: 80px;
            color: #4CAF50;
            animation: bounce 1s ease-out;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        h1 {
            color: #333;
            margin: 20px 0 10px;
            font-size: 2.5rem;
        }
        
        .subtitle {
            color: #666;
            font-size: 1.1rem;
        }
        
        .transaction-info {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 10px;
        }
        
        .transaction-id {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        /* Multiple ticket cards for multiple attendees */
        .tickets-container {
            margin: 30px 0;
        }
        
        .ticket-card {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .ticket-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .ticket-number {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .attendee-id {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 3px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .qr-section {
            text-align: center;
            margin: 20px 0;
        }
        
        .qr-section h4 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .qr-code {
            display: inline-block;
            padding: 15px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .qr-code img {
            display: block;
            width: 250px;
            height: 250px;
        }
        
        .attendee-name {
            text-align: center;
            font-size: 1.1rem;
            color: #333;
            margin-top: 15px;
            font-weight: 600;
        }
        
        .event-details {
            background: linear-gradient(135deg, #fff3cd, #fff9e6);
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 15px;
            margin: 30px 0;
        }
        
        .event-details h3 {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .event-details p {
            color: #856404;
            margin: 8px 0;
        }
        
        .instructions {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 20px;
            border-radius: 10px;
            margin: 30px 0;
        }
        
        .instructions h3 {
            color: #1565C0;
            margin-bottom: 15px;
        }
        
        .instructions ul {
            list-style: none;
            padding-left: 0;
        }
        
        .instructions li {
            color: #333;
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        
        .instructions li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #2196F3;
            font-weight: bold;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 15px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            display: inline-block;
            text-align: center;
            min-width: 180px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
        }
        
        .footer-note {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #999;
            font-size: 0.9rem;
        }
        
        @media print {
            body {
                background: white;
            }
            .container {
                box-shadow: none;
                max-width: 100%;
            }
            .btn {
                display: none;
            }
            .action-buttons {
                display: none;
            }
            .ticket-card {
                page-break-inside: avoid;
                border: 1px solid #000;
            }
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            h1 {
                font-size: 1.8rem;
            }
            .attendee-id {
                font-size: 20px;
                padding: 15px;
            }
            .qr-code img {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-header">
            <div class="success-icon">✅</div>
            <h1>Payment Successful!</h1>
            <p class="subtitle">Your registration for Kerala Food Fest 2025 is confirmed!</p>
        </div>
        
        <div class="transaction-info">
            <strong>Transaction ID:</strong>
            <div class="transaction-id"><?php echo htmlspecialchars($main_data['payment_id']); ?></div>
        </div>
        
        <div class="tickets-container">
            <?php 
            $ticket_number = 1;
            foreach ($attendees_data as $attendee): 
            ?>
            <div class="ticket-card">
                <div class="ticket-header">
                    <?php if (count($attendees_data) > 1): ?>
                    <span class="ticket-number">Ticket <?php echo $ticket_number; ?> of <?php echo count($attendees_data); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="attendee-id">
                    <?php echo htmlspecialchars($attendee['attendee_id']); ?>
                    <div style="font-size: 16px; font-weight: normal; margin-top: 10px; letter-spacing: normal;">
                        <?php echo htmlspecialchars($attendee['attendee_name']); ?>
                    </div>
                </div>
                
                <div class="qr-section">
                    <h4>Entry QR Code</h4>
                    <div class="qr-code">
                        <img src="<?php echo $attendee['qr_url']; ?>" alt="QR Code for <?php echo $attendee['attendee_id']; ?>">
                    </div>
                    <p style="color: #666; margin-top: 10px; font-size: 0.9rem;">
                        Show this QR code at the venue for entry
                    </p>
                </div>
            </div>
            <?php 
            $ticket_number++;
            endforeach; 
            ?>
        </div>
        
        <div class="event-details">
            <h3>📅 Event Details</h3>
            <p><strong>Event:</strong> Kerala Food Fest 2025 - 2nd Edition</p>
            <p><strong>Dates:</strong> November 13-16, 2025 (4 Days)</p>
            <p><strong>Time:</strong> 9:00 AM - 10:00 PM Daily</p>
            <p><strong>Venue:</strong> Bittan Market Ground, Bhopal</p>
            <p><strong>Organized by:</strong> Ulledam Malayalee Association, Bhopal</p>
        </div>
        
        <div class="instructions">
            <h3>📌 Important Instructions</h3>
            <ul>
                <li>Save this page or take a screenshot for your records</li>
                <li>Each person must show their individual QR code at entry</li>
                <li>Gates open at 9:00 AM sharp on all event days</li>
                <li>Free parking available at the venue</li>
                <li>Children under 5 years enter free (no ticket required)</li>
                <li>Food coupons available at the venue</li>
            </ul>
        </div>
        
        <div class="action-buttons">
            <a href="index.php" class="btn btn-primary">🏠 Back to Home</a>
            <a href="javascript:window.print()" class="btn btn-secondary">🖨️ Print All Tickets</a>
        </div>
        
        <div class="footer-note">
            <p>Total Amount Paid: ₹<?php echo number_format($main_data['total_amount'], 2); ?></p>
            <p>For support, contact: support@keralafest.in | +91 98765 43210</p>
            <p>Keep this ticket safe. It will be required for entry.</p>
        </div>
    </div>
</body>
</html>