<?php
// admin/minute_updates.php - Manage minute-to-minute updates

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
require_once '../config/settings.php';

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Add new update
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $update_date = mysqli_real_escape_string($conn, $_POST['update_date']);
        $update_time = mysqli_real_escape_string($conn, $_POST['update_time']);
        $update_text = mysqli_real_escape_string($conn, $_POST['update_text']);
        
        $query = "INSERT INTO minute_updates (update_date, update_time, update_text) 
                  VALUES ('$update_date', '$update_time', '$update_text')";
        
        if (mysqli_query($conn, $query)) {
            $message = "Update added successfully!";
            $message_type = 'success';
        } else {
            $message = "Error: " . mysqli_error($conn);
            $message_type = 'danger';
        }
    }
    
    // Toggle active status
    if (isset($_POST['action']) && $_POST['action'] === 'toggle') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "UPDATE minute_updates SET is_active = NOT is_active WHERE id = $id");
        $message = "Status updated!";
        $message_type = 'success';
    }
    
    // Delete update
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "DELETE FROM minute_updates WHERE id = $id");
        $message = "Update deleted!";
        $message_type = 'success';
    }
    
    // Edit update
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $update_date = mysqli_real_escape_string($conn, $_POST['update_date']);
        $update_time = mysqli_real_escape_string($conn, $_POST['update_time']);
        $update_text = mysqli_real_escape_string($conn, $_POST['update_text']);
        
        $query = "UPDATE minute_updates 
                  SET update_date = '$update_date', 
                      update_time = '$update_time', 
                      update_text = '$update_text' 
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $message = "Update modified successfully!";
            $message_type = 'success';
        }
    }
}

// Get selected date or use first event date
$selected_date = $_GET['date'] ?? EVENT_DATES[0] ?? date('Y-m-d');

// Get updates for selected date
$updates_query = "SELECT * FROM minute_updates 
                  WHERE update_date = '$selected_date' 
                  ORDER BY update_time DESC";
$updates_result = mysqli_query($conn, $updates_query);

// Get count per date
$date_counts = [];
foreach (EVENT_DATES as $date) {
    $count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM minute_updates WHERE update_date = '$date'");
    $date_counts[$date] = mysqli_fetch_assoc($count_result)['count'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Minute Updates - <?php echo EVENT_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Minute-to-Minute Updates</span>
            <div>
                <a href="index.php" class="btn btn-outline-light btn-sm">Dashboard</a>
                <a href="slider_management.php" class="btn btn-outline-light btn-sm">Sliders</a>
                <a href="logo_management.php" class="btn btn-outline-light btn-sm">Logos</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Date Selection -->
        <div class="card mb-4">
            <div class="card-body">
                <h6>Select Event Date:</h6>
                <div class="btn-group" role="group">
                    <?php foreach (EVENT_DATES as $date): ?>
                    <a href="?date=<?php echo $date; ?>" 
                       class="btn btn-<?php echo $date === $selected_date ? 'primary' : 'outline-primary'; ?>">
                        <?php echo date('d M', strtotime($date)); ?>
                        <span class="badge bg-light text-dark"><?php echo $date_counts[$date] ?? 0; ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Add Update Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Add New Update</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add">
                            
                            <div class="mb-3">
                                <label class="form-label">Date *</label>
                                <input type="date" name="update_date" class="form-control" 
                                       value="<?php echo $selected_date; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Time *</label>
                                <input type="time" name="update_time" class="form-control" 
                                       value="<?php echo date('H:i'); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Update Text *</label>
                                <textarea name="update_text" class="form-control" rows="4" 
                                          placeholder="e.g., Cultural performance starting at main stage" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add Update
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" onsubmit="return confirm('Delete all updates for this date?')">
                            <input type="hidden" name="action" value="delete_all_for_date">
                            <input type="hidden" name="delete_date" value="<?php echo $selected_date; ?>">
                            <button type="submit" class="btn btn-sm btn-warning w-100 mb-2">
                                <i class="bi bi-trash"></i> Clear This Date
                            </button>
                        </form>
                        
                        <a href="../api/get_minute_updates.php?date=<?php echo $selected_date; ?>" 
                           target="_blank" class="btn btn-sm btn-info w-100">
                            <i class="bi bi-eye"></i> Preview API
                        </a>
                    </div>
                </div>
            </div>

            <!-- Updates Timeline -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            Updates for <?php echo date('d F Y', strtotime($selected_date)); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($updates_result) > 0): ?>
                        <div class="list-group">
                            <?php while ($update = mysqli_fetch_assoc($updates_result)): ?>
                            <div class="list-group-item <?php echo !$update['is_active'] ? 'bg-light' : ''; ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <i class="bi bi-clock"></i> 
                                            <?php echo date('h:i A', strtotime($update['update_time'])); ?>
                                            <?php if (!$update['is_active']): ?>
                                            <span class="badge bg-secondary">Hidden</span>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="mb-2"><?php echo nl2br(htmlspecialchars($update['update_text'])); ?></p>
                                        <small class="text-muted">
                                            Added: <?php echo date('d M Y, h:i A', strtotime($update['created_at'])); ?>
                                        </small>
                                    </div>
                                    
                                    <div class="btn-group btn-group-sm ms-2">
                                        <!-- Edit Button (trigger modal) -->
                                        <button type="button" class="btn btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal<?php echo $update['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <!-- Toggle Status -->
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?php echo $update['id']; ?>">
                                            <button type="submit" class="btn btn-outline-<?php echo $update['is_active'] ? 'success' : 'secondary'; ?>">
                                                <i class="bi bi-<?php echo $update['is_active'] ? 'eye' : 'eye-slash'; ?>"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Delete -->
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Delete this update?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $update['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $update['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?php echo $update['id']; ?>">
                                            
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Update</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Date</label>
                                                    <input type="date" name="update_date" class="form-control" 
                                                           value="<?php echo $update['update_date']; ?>" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Time</label>
                                                    <input type="time" name="update_time" class="form-control" 
                                                           value="<?php echo $update['update_time']; ?>" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Update Text</label>
                                                    <textarea name="update_text" class="form-control" rows="4" required><?php echo htmlspecialchars($update['update_text']); ?></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No updates for this date yet. Add your first update using the form.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>