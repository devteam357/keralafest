<?php
// admin/logo_management.php - Manage sponsor logos

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
    
    // Add new logo
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $sponsor_name = mysqli_real_escape_string($conn, $_POST['sponsor_name']);
        $website_url = mysqli_real_escape_string($conn, $_POST['website_url']);
        $display_order = (int)$_POST['display_order'];
        
        $logo_file = '';
        
        // Handle logo upload
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === 0) {
            $upload_dir = LOGO_UPLOAD_DIR . $category . '/';
            $logo_result = saveUploadedFile($_FILES['logo_file'], $upload_dir);
            
            if ($logo_result['success']) {
                $logo_file = 'uploads/logos/' . $category . '/' . $logo_result['filename'];
                
                $query = "INSERT INTO sponsor_logos (category, sponsor_name, logo_file, website_url, display_order) 
                          VALUES ('$category', '$sponsor_name', '$logo_file', '$website_url', $display_order)";
                
                if (mysqli_query($conn, $query)) {
                    $message = "Logo added successfully!";
                    $message_type = 'success';
                } else {
                    $message = "Error: " . mysqli_error($conn);
                    $message_type = 'danger';
                }
            } else {
                $message = $logo_result['error'];
                $message_type = 'danger';
            }
        } else {
            $message = "Please select a logo file";
            $message_type = 'danger';
        }
    }
    
    // Toggle active status
    if (isset($_POST['action']) && $_POST['action'] === 'toggle') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "UPDATE sponsor_logos SET is_active = NOT is_active WHERE id = $id");
        $message = "Status updated!";
        $message_type = 'success';
    }
    
    // Delete logo
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        $result = mysqli_query($conn, "SELECT logo_file FROM sponsor_logos WHERE id = $id");
        $row = mysqli_fetch_assoc($result);
        
        if (mysqli_query($conn, "DELETE FROM sponsor_logos WHERE id = $id")) {
            if (file_exists('../' . $row['logo_file'])) {
                unlink('../' . $row['logo_file']);
            }
            $message = "Logo deleted!";
            $message_type = 'success';
        }
    }
    
    // Update order
    if (isset($_POST['action']) && $_POST['action'] === 'update_order') {
        $id = (int)$_POST['id'];
        $new_order = (int)$_POST['new_order'];
        mysqli_query($conn, "UPDATE sponsor_logos SET display_order = $new_order WHERE id = $id");
        $message = "Order updated!";
        $message_type = 'success';
    }
}

// Get active tab
$active_tab = $_GET['tab'] ?? 'title';

// Get logos for current category
$logos_query = "SELECT * FROM sponsor_logos WHERE category = '$active_tab' ORDER BY display_order ASC, id DESC";
$logos_result = mysqli_query($conn, $logos_query);

// Get count per category
$counts = [];
foreach (['title', 'powered_by', 'official_partner', 'supported_by', 'association', 'radio_partner', 'digital_partner'] as $cat) {
    $count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM sponsor_logos WHERE category = '$cat' AND is_active = 1");
    $counts[$cat] = mysqli_fetch_assoc($count_result)['count'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logo Management - <?php echo EVENT_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .logo-preview { max-width: 150px; max-height: 100px; object-fit: contain; background: #f8f9fa; padding: 10px; }
        .tab-badge { margin-left: 5px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Logo Management</span>
            <div>
                <a href="index.php" class="btn btn-outline-light btn-sm">Dashboard</a>
                <a href="slider_management.php" class="btn btn-outline-light btn-sm">Sliders</a>
                <a href="minute_updates.php" class="btn btn-outline-light btn-sm">Updates</a>
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

        <!-- Category Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?php echo $active_tab === 'title' ? 'active' : ''; ?>" href="?tab=title">
                    Title Sponsor <span class="badge bg-primary tab-badge"><?php echo $counts['title']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active_tab === 'powered_by' ? 'active' : ''; ?>" href="?tab=powered_by">
                    Powered By <span class="badge bg-primary tab-badge"><?php echo $counts['powered_by']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active_tab === 'official_partner' ? 'active' : ''; ?>" href="?tab=official_partner">
                    Official Partners <span class="badge bg-primary tab-badge"><?php echo $counts['official_partner']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active_tab === 'supported_by' ? 'active' : ''; ?>" href="?tab=supported_by">
                    Supported By <span class="badge bg-primary tab-badge"><?php echo $counts['supported_by']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active_tab === 'association' ? 'active' : ''; ?>" href="?tab=association">
                    Association <span class="badge bg-primary tab-badge"><?php echo $counts['association']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active_tab === 'radio_partner' ? 'active' : ''; ?>" href="?tab=radio_partner">
                    Radio Partner <span class="badge bg-primary tab-badge"><?php echo $counts['radio_partner']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active_tab === 'digital_partner' ? 'active' : ''; ?>" href="?tab=digital_partner">
                    Digital Partner <span class="badge bg-primary tab-badge"><?php echo $counts['digital_partner']; ?></span>
                </a>
            </li>
        </ul>

        <div class="row">
            <!-- Add Logo Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Add New Logo</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="category" value="<?php echo $active_tab; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control" value="<?php echo ucwords(str_replace('_', ' ', $active_tab)); ?>" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Sponsor Name *</label>
                                <input type="text" name="sponsor_name" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Logo File *</label>
                                <input type="file" name="logo_file" class="form-control" required accept="image/*">
                                <small class="text-muted">Max 5MB. PNG/JPG/WEBP recommended</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Website URL</label>
                                <input type="url" name="website_url" class="form-control" placeholder="https://example.com">
                                <small class="text-muted">Logo will be clickable if URL provided</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="1" min="1">
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add Logo
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Logos -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <?php echo ucwords(str_replace('_', ' ', $active_tab)); ?> Logos
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($logos_result) > 0): ?>
                        <div class="row">
                            <?php while ($logo = mysqli_fetch_assoc($logos_result)): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <img src="../<?php echo $logo['logo_file']; ?>" 
                                             class="logo-preview mb-2" 
                                             alt="<?php echo htmlspecialchars($logo['sponsor_name']); ?>">
                                        
                                        <h6><?php echo htmlspecialchars($logo['sponsor_name']); ?></h6>
                                        
                                        <?php if ($logo['website_url']): ?>
                                        <a href="<?php echo htmlspecialchars($logo['website_url']); ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-link-45deg"></i> Visit
                                        </a>
                                        <?php endif; ?>
                                        
                                        <div class="mt-2">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update_order">
                                                <input type="hidden" name="id" value="<?php echo $logo['id']; ?>">
                                                <input type="number" name="new_order" 
                                                       value="<?php echo $logo['display_order']; ?>" 
                                                       class="form-control form-control-sm d-inline" 
                                                       style="width: 60px;" 
                                                       onchange="this.form.submit()" 
                                                       title="Display order">
                                            </form>
                                            
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?php echo $logo['id']; ?>">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-<?php echo $logo['is_active'] ? 'success' : 'secondary'; ?>"
                                                        title="Toggle status">
                                                    <i class="bi bi-<?php echo $logo['is_active'] ? 'eye' : 'eye-slash'; ?>"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Delete this logo?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $logo['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No logos in this category yet. Add your first logo using the form.
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