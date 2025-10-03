<?php
require_once '../config/settings.php';
require_once '../includes/functions.php';
check_admin();

// Date filters
$from_date = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to_date = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

// Get transactions
$sql = "SELECT * FROM registrations 
        WHERE DATE(registration_date) BETWEEN '$from_date' AND '$to_date'
        ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

// Calculate totals
$stats_sql = "SELECT 
              COUNT(CASE WHEN payment_status = 'success' THEN 1 END) as paid_count,
              COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_count,
              SUM(CASE WHEN payment_status = 'success' THEN total_amount ELSE 0 END) as total_revenue
              FROM registrations 
              WHERE DATE(registration_date) BETWEEN '$from_date' AND '$to_date'";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Payment Transactions</span>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="attendees.php" class="btn btn-outline-light me-2">Attendees</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Date Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="from" class="form-control" value="<?php echo $from_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="to" class="form-control" value="<?php echo $to_date; ?>">
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Successful Payments</h5>
                        <h3><?php echo $stats['paid_count']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5>Pending Payments</h5>
                        <h3><?php echo $stats['pending_count']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Revenue</h5>
                        <h3>₹<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header">
                <h5>Transaction Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Amount</th>
                                <th>Payment ID</th>
                                <th>Status</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><small><?php echo $row['unique_id']; ?></small></td>
                                <td><?php echo $row['primary_name']; ?></td>
                                <td><?php echo $row['primary_email']; ?></td>
                                <td><strong>₹<?php echo number_format($row['total_amount'], 2); ?></strong></td>
                                <td>
                                    <?php if($row['payment_id']): ?>
                                        <small><?php echo $row['payment_id']; ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['payment_status'] == 'success'): ?>
                                        <span class="badge bg-success">Success</span>
                                    <?php elseif($row['payment_status'] == 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Failed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d M Y h:i A', strtotime($row['registration_date'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>