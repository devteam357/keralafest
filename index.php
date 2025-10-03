<?php
// admin/index.php - Safe version that works without content management tables
require_once '../config/settings.php';
require_once '../includes/functions.php';
check_admin();

$stats = get_dashboard_stats($conn);

// Get recent registrations with correct column names
$recent_query = "SELECT *, 
                 additional_attendees as additional_count 
                 FROM registrations 
                 ORDER BY id DESC 
                 LIMIT 10";
$recent_result = mysqli_query($conn, $recent_query);

// Get today's stats
$today = date('Y-m-d');
$today_stats_query = "SELECT 
    COUNT(*) as today_registrations,
    SUM(total_amount) as today_revenue,
    SUM(1 + additional_attendees) as today_tickets
    FROM registrations 
    WHERE DATE(registration_date) = '$today' 
    AND payment_status = 'success'";
$today_stats_result = mysqli_query($conn, $today_stats_query);
$today_stats = mysqli_fetch_assoc($today_stats_result);

// Get checked-in count
$checked_query = "SELECT 
    (SELECT COUNT(*) FROM registrations WHERE attended = 1) + 
    (SELECT COUNT(*) FROM attendees WHERE attended = 1) as total";
$checked_result = mysqli_query($conn, $checked_query);
$checked_count = mysqli_fetch_assoc($checked_result)['total'] ?: 0;

// Get content management stats (with error handling for missing tables)
$slider_count = 0;
$logo_count = 0;
$update_count = 0;

// Check if content tables exist
$tables_exist = false;
$check_tables = @mysqli_query($conn, "SHOW TABLES LIKE 'sliders'");
if ($check_tables && mysqli_num_rows($check_tables) > 0) {
    $tables_exist = true;
    
    // Get slider count
    $result = @mysqli_query($conn, "SELECT COUNT(*) as count FROM sliders WHERE status='active'");
    if ($result) {
        $slider_count = mysqli_fetch_assoc($result)['count'] ?? 0;
    }
    
    // Get logo count
    $result = @mysqli_query($conn, "SELECT COUNT(*) as count FROM sponsor_logos WHERE status='active'");
    if ($result) {
        $logo_count = mysqli_fetch_assoc($result)['count'] ?? 0;
    }
    
    // Get update count
    $result = @mysqli_query($conn, "SELECT COUNT(*) as count FROM minute_updates WHERE status='active'");
    if ($result) {
        $update_count = mysqli_fetch_assoc($result)['count'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kerala Fest 2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #30303b 0%, #1a1a2e 100%);
            --orange-gradient: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }

        /* Modern Navigation Bar */
        .navbar {
            background: var(--dark-gradient) !important;
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            border: none;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff !important;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            font-size: 2rem;
            margin-right: 10px;
        }

        /* Navigation Menu */
        .nav-menu {
            display: flex;
            gap: 0;
            flex-wrap: wrap;
            align-items: center;
        }

        .nav-btn {
            padding: 10px 20px;
            border: none;
            background: rgba(255,255,255,0.05);
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-right: 1px solid rgba(255,255,255,0.1);
        }

        .nav-btn:first-child {
            border-radius: 8px 0 0 8px;
        }

        .nav-btn:last-child {
            border-radius: 0 8px 8px 0;
            border-right: none;
        }

        .nav-btn:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
            transform: translateY(-2px);
        }

        .nav-btn i {
            font-size: 16px;
        }

        .nav-btn.logout {
            background: rgba(220,53,69,0.2);
            border-color: #dc3545;
            color: #ff6b7a;
            margin-left: 10px;
            border-radius: 8px;
        }

        .nav-btn.logout:hover {
            background: rgba(220,53,69,0.4);
        }

        .nav-btn.content {
            background: rgba(255,193,7,0.2);
            color: #ffc107;
        }

        .nav-btn.content:hover {
            background: rgba(255,193,7,0.3);
        }

        /* Statistics Cards */
        .stat-card {
            border: none;
            border-radius: 20px;
            margin-bottom: 25px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-card .card-body {
            padding: 1.8rem;
            position: relative;
            z-index: 1;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.95;
            z-index: 0;
        }

        .stat-card.primary::before { background: var(--primary-gradient); }
        .stat-card.success::before { background: var(--success-gradient); }
        .stat-card.warning::before { background: var(--warning-gradient); }
        .stat-card.info::before { background: var(--info-gradient); }
        .stat-card.orange::before { background: var(--orange-gradient); }

        .stat-card h5 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 12px;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            line-height: 1;
        }

        .stat-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.2;
        }

        /* Quick Actions Grid */
        .quick-actions {
            margin-top: 30px;
        }

        .action-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-decoration: none;
            display: block;
            margin-bottom: 20px;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .action-card i {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .action-card h5 {
            color: #333;
            margin-bottom: 5px;
        }

        .action-card p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .action-card.primary i { color: #667eea; }
        .action-card.success i { color: #11998e; }
        .action-card.warning i { color: #f093fb; }
        .action-card.info i { color: #4facfe; }
        .action-card.orange i { color: #f7971e; }
        .action-card.danger i { color: #dc3545; }

        /* Table Card */
        .table-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-top: 30px;
        }

        .table-card .card-header {
            background: var(--dark-gradient);
            color: white;
            padding: 20px 25px;
            border: none;
        }

        .table-card .card-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Modern Table */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modern-table thead {
            background: #f8f9fa;
        }

        .modern-table thead th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f1f3f5;
        }

        .modern-table tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }

        .modern-table tbody td {
            padding: 16px 20px;
            font-size: 14px;
            color: #495057;
            vertical-align: middle;
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .badge.bg-primary { background: var(--primary-gradient) !important; }
        .badge.bg-success { background: var(--success-gradient) !important; }
        .badge.bg-warning { background: var(--warning-gradient) !important; }

        .unique-id {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            display: inline-block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-menu {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                width: 100%;
                margin-top: 15px;
            }
            .nav-btn {
                border-radius: 8px !important;
                border-right: none !important;
                justify-content: center;
            }
            .navbar-brand {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="bi bi-speedometer2"></i>
                Admin Panel - <?php echo EVENT_NAME; ?>
            </span>
            <div class="nav-menu">
                <a href="index.php" class="nav-btn">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
                <a href="attendees.php" class="nav-btn">
                    <i class="bi bi-people"></i> Attendees
                </a>
                <a href="scan.php" class="nav-btn">
                    <i class="bi bi-qr-code-scan"></i> QR Scan
                </a>
                <a href="transactions.php" class="nav-btn">
                    <i class="bi bi-credit-card"></i> Payments
                </a>
                
                <?php if ($tables_exist): ?>
                <a href="slider_management.php" class="nav-btn content">
                    <i class="bi bi-images"></i> Sliders
                </a>
                <a href="logo_management.php" class="nav-btn content">
                    <i class="bi bi-award"></i> Logos
                </a>
                <a href="minute_updates.php" class="nav-btn content">
                    <i class="bi bi-clock-history"></i> Updates
                </a>
                <?php endif; ?>
                
                <a href="print_list.php" class="nav-btn">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="export.php" class="nav-btn">
                    <i class="bi bi-download"></i> Export
                </a>
                <a href="settings.php" class="nav-btn">
                    <i class="bi bi-gear"></i> Settings
                </a>
                <a href="logout.php" class="nav-btn logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-4">
        <!-- Today's Statistics Banner -->
        <div class="alert alert-info mb-4" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; color: white;">
            <div class="row text-center">
                <div class="col-md-4">
                    <h4 class="mb-0"><?php echo $today_stats['today_registrations'] ?? 0; ?></h4>
                    <small>Today's Registrations</small>
                </div>
                <div class="col-md-4">
                    <h4 class="mb-0"><?php echo $today_stats['today_tickets'] ?? 0; ?></h4>
                    <small>Today's Tickets</small>
                </div>
                <div class="col-md-4">
                    <h4 class="mb-0">₹<?php echo number_format($today_stats['today_revenue'] ?? 0, 0); ?></h4>
                    <small>Today's Revenue</small>
                </div>
            </div>
        </div>

        <!-- Overall Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card primary text-white">
                    <div class="card-body">
                        <h5>Total Tickets</h5>
                        <h2><?php echo $stats['tickets']; ?></h2>
                        <div class="stat-icon">🎟️</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card success text-white">
                    <div class="card-body">
                        <h5>Total Attendees</h5>
                        <h2><?php echo $stats['attendees']; ?></h2>
                        <div class="stat-icon">👥</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card warning text-white">
                    <div class="card-body">
                        <h5>Total Revenue</h5>
                        <h2><?php echo format_currency($stats['revenue']); ?></h2>
                        <div class="stat-icon">💰</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card info text-white">
                    <div class="card-body">
                        <h5>Checked In</h5>
                        <h2><?php echo $checked_count; ?></h2>
                        <div class="stat-icon">✅</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <?php if ($tables_exist): ?>
        <div class="quick-actions">
            <h3 class="section-title">Quick Actions</h3>
            <div class="row">
                <div class="col-md-2">
                    <a href="slider_management.php" class="action-card primary">
                        <i class="bi bi-images"></i>
                        <h5>Sliders</h5>
                        <p><?php echo $slider_count; ?> active</p>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="logo_management.php" class="action-card success">
                        <i class="bi bi-award"></i>
                        <h5>Sponsors</h5>
                        <p><?php echo $logo_count; ?> logos</p>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="minute_updates.php" class="action-card warning">
                        <i class="bi bi-clock-history"></i>
                        <h5>Updates</h5>
                        <p><?php echo $update_count; ?> active</p>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="scan.php" class="action-card info">
                        <i class="bi bi-qr-code-scan"></i>
                        <h5>QR Scanner</h5>
                        <p>Check-in</p>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="export.php" class="action-card orange">
                        <i class="bi bi-download"></i>
                        <h5>Export Data</h5>
                        <p>Download CSV</p>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="settings.php" class="action-card danger">
                        <i class="bi bi-gear"></i>
                        <h5>Settings</h5>
                        <p>Configure</p>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Registrations -->
        <div class="table-card">
            <div class="card-header">
                <h5>📋 Recent Registrations</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Unique ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Tickets</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($recent_result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                                <tr>
                                    <td><span class="unique-id"><?php echo $row['unique_id']; ?></span></td>
                                    <td><strong><?php echo $row['primary_name'] ?? $row['name'] ?? '-'; ?></strong></td>
                                    <td><?php echo $row['primary_email'] ?? $row['email'] ?? '-'; ?></td>
                                    <td><?php echo $row['primary_phone'] ?? $row['phone'] ?? '-'; ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php 
                                            $ticket_count = 1 + (int)($row['additional_count'] ?? 0);
                                            echo $ticket_count . ' ' . ($ticket_count > 1 ? 'tickets' : 'ticket');
                                            ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo format_currency($row['total_amount'] ?? 0); ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($row['payment_status'] ?? 'pending') == 'success' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($row['payment_status'] ?? 'pending'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($row['registration_date'] ?? 'now')); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <h5 class="mt-3">No registrations yet</h5>
                                        <p class="text-muted">New registrations will appear here</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>