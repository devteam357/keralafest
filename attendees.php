<?php
require_once '../config/settings.php';
require_once '../includes/functions.php';
check_admin();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Search and filters
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? clean_input($_GET['status']) : 'all';
$filter_attended = isset($_GET['attended']) ? clean_input($_GET['attended']) : 'all';

// Build WHERE clause
$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (r.unique_id LIKE '%$search%' OR r.primary_name LIKE '%$search%' 
                OR r.primary_email LIKE '%$search%' OR r.primary_phone LIKE '%$search%')";
}
if ($filter_status != 'all') {
    $where .= " AND r.payment_status = '$filter_status'";
}
if ($filter_attended != 'all') {
    $attended_value = ($filter_attended == 'yes') ? 1 : 0;
    $where .= " AND r.attended = $attended_value";
}

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM registrations r $where";
$count_result = mysqli_query($conn, $count_sql);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $per_page);

// Get registrations
$sql = "SELECT r.*, 
        (SELECT COUNT(*) FROM attendees WHERE registration_id = r.id) as guest_count,
        (SELECT COUNT(*) FROM attendees WHERE registration_id = r.id AND attended = 1) as guests_attended
        FROM registrations r 
        $where 
        ORDER BY r.id DESC 
        LIMIT $offset, $per_page";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .attendee-row:hover { background-color: #f8f9fa; }
        .badge-attended { background-color: #198754; }
        .badge-pending { background-color: #ffc107; color: #000; }
        .search-box { max-width: 400px; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Attendee Management</span>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="scan.php" class="btn btn-outline-light me-2">QR Scanner</a>
                <a href="export.php" class="btn btn-outline-success me-2">Export CSV</a>
                <a href="print_list.php" class="btn btn-outline-info me-2">Print List</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Search and Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by ID, Name, Email, Phone..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="all">All Status</option>
                            <option value="success" <?php echo $filter_status == 'success' ? 'selected' : ''; ?>>Paid</option>
                            <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="attended" class="form-select">
                            <option value="all">All Attendance</option>
                            <option value="yes" <?php echo $filter_attended == 'yes' ? 'selected' : ''; ?>>Attended</option>
                            <option value="no" <?php echo $filter_attended == 'no' ? 'selected' : ''; ?>>Not Attended</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="attendees.php" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="alert alert-info mb-0">
                    <strong>Total Found:</strong> <?php echo $total_records; ?> registrations
                </div>
            </div>
        </div>

        <!-- Attendees Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Unique ID</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Tickets</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Check-in Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="attendee-row">
                                <td>
                                    <small class="font-monospace"><?php echo $row['unique_id']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo $row['primary_name']; ?></strong>
                                </td>
                                <td>
                                    <small>
                                        <?php echo $row['primary_email']; ?><br>
                                        <?php echo $row['primary_phone']; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo 1 + $row['additional_attendees']; ?> tickets
                                    </span>
                                    <?php if($row['guest_count'] > 0): ?>
                                        <br><small><?php echo $row['guest_count']; ?> guests</small>
                                    <?php endif; ?>
                                </td>
                                <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td>
                                    <?php if($row['payment_status'] == 'success'): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['attended'] == 1): ?>
                                        <span class="badge badge-attended">
                                            <i class="bi bi-check-circle"></i> Attended
                                        </span>
                                        <?php if($row['attended_at']): ?>
                                            <br><small><?php echo date('h:i A', strtotime($row['attended_at'])); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-pending">Not Attended</span>
                                    <?php endif; ?>
                                    
                                    <?php if($row['guest_count'] > 0 && $row['guests_attended'] > 0): ?>
                                        <br><small><?php echo $row['guests_attended']; ?>/<?php echo $row['guest_count']; ?> guests checked in</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo date('d M Y', strtotime($row['registration_date'])); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewDetails(<?php echo $row['id']; ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" onclick="markAttended(<?php echo $row['id']; ?>)">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <a href="edit_registration.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $filter_status; ?>&attended=<?php echo $filter_attended; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $filter_status; ?>&attended=<?php echo $filter_attended; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $filter_status; ?>&attended=<?php echo $filter_attended; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registration Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-content">
                    Loading...
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function viewDetails(id) {
        // Fetch and show details in modal
        const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
        document.getElementById('modal-content').innerHTML = 'Loading...';
        
        fetch('../api/get_registration_details.php?id=' + id)
            .then(response => response.text())
            .then(data => {
                document.getElementById('modal-content').innerHTML = data;
            });
        
        modal.show();
    }

    function markAttended(id) {
        if (confirm('Mark this registration as attended?')) {
            fetch('../api/mark_attended.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({registration_id: id})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }
    </script>
</body>
</html>