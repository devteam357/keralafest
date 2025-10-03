<?php
// admin/slider_management.php - UPDATED with proper Video Support
require_once '../config/settings.php';
require_once '../includes/functions.php';
check_admin();

$message = '';
$error = '';

// Create upload directories if they don't exist
$upload_dirs = [
    '../uploads/sliders/desktop',
    '../uploads/sliders/mobile'
];

foreach ($upload_dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['add_slider'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $media_type = mysqli_real_escape_string($conn, $_POST['media_type']); // image or video
        $display_order = (int)$_POST['display_order'];
        
        // File size limits based on type
        $max_size = ($media_type == 'video') ? 50 * 1024 * 1024 : 5 * 1024 * 1024; // 50MB for video, 5MB for image
        
        // Allowed file types
        $allowed_image = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowed_video = ['mp4', 'webm', 'ogg'];
        $allowed_types = ($media_type == 'video') ? $allowed_video : $allowed_image;
        
        $desktop_file = '';
        $mobile_file = '';
        $upload_success = true;
        
        // Upload Desktop File
        if (isset($_FILES['desktop_file']) && $_FILES['desktop_file']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['desktop_file']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_types)) {
                $error = "Invalid desktop file type. Allowed: " . implode(', ', $allowed_types);
                $upload_success = false;
            } elseif ($_FILES['desktop_file']['size'] > $max_size) {
                $error = "Desktop file too large. Max " . ($media_type == 'video' ? '50MB' : '5MB');
                $upload_success = false;
            } else {
                $desktop_file = 'desktop_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                $desktop_path = '../uploads/sliders/desktop/' . $desktop_file;
                
                if (!move_uploaded_file($_FILES['desktop_file']['tmp_name'], $desktop_path)) {
                    $error = "Failed to upload desktop file";
                    $upload_success = false;
                }
            }
        } else {
            $error = "Desktop file is required";
            $upload_success = false;
        }
        
        // Upload Mobile File
        if ($upload_success && isset($_FILES['mobile_file']) && $_FILES['mobile_file']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['mobile_file']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_types)) {
                $error = "Invalid mobile file type. Allowed: " . implode(', ', $allowed_types);
                $upload_success = false;
            } elseif ($_FILES['mobile_file']['size'] > $max_size) {
                $error = "Mobile file too large. Max " . ($media_type == 'video' ? '50MB' : '5MB');
                $upload_success = false;
            } else {
                $mobile_file = 'mobile_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                $mobile_path = '../uploads/sliders/mobile/' . $mobile_file;
                
                if (!move_uploaded_file($_FILES['mobile_file']['tmp_name'], $mobile_path)) {
                    $error = "Failed to upload mobile file";
                    $upload_success = false;
                    // Delete desktop file if mobile upload fails
                    if (file_exists($desktop_path)) {
                        unlink($desktop_path);
                    }
                }
            }
        } else {
            $error = "Mobile file is required";
            $upload_success = false;
        }
        
        // Insert into database if uploads successful
        if ($upload_success) {
            $sql = "INSERT INTO sliders (title, description, media_type, desktop_file, mobile_file, display_order, status) 
                    VALUES ('$title', '$description', '$media_type', '$desktop_file', '$mobile_file', $display_order, 'active')";
            
            if (mysqli_query($conn, $sql)) {
                $message = "Slider added successfully!";
            } else {
                $error = "Database error: " . mysqli_error($conn);
            }
        }
    }
    
    // Toggle status
    if (isset($_POST['toggle_status'])) {
        $id = (int)$_POST['slider_id'];
        $sql = "UPDATE sliders SET status = IF(status = 'active', 'inactive', 'active') WHERE id = $id";
        mysqli_query($conn, $sql);
        $message = "Status updated!";
    }
    
    // Delete slider
    if (isset($_POST['delete_slider'])) {
        $id = (int)$_POST['slider_id'];
        
        // Get file names before deleting
        $sql = "SELECT desktop_file, mobile_file FROM sliders WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        
        // Delete files
        if ($row['desktop_file'] && file_exists('../uploads/sliders/desktop/' . $row['desktop_file'])) {
            unlink('../uploads/sliders/desktop/' . $row['desktop_file']);
        }
        if ($row['mobile_file'] && file_exists('../uploads/sliders/mobile/' . $row['mobile_file'])) {
            unlink('../uploads/sliders/mobile/' . $row['mobile_file']);
        }
        
        // Delete from database
        $sql = "DELETE FROM sliders WHERE id = $id";
        mysqli_query($conn, $sql);
        $message = "Slider deleted!";
    }
    
    // Update display order
    if (isset($_POST['update_order'])) {
        $id = (int)$_POST['slider_id'];
        $new_order = (int)$_POST['new_order'];
        $sql = "UPDATE sliders SET display_order = $new_order WHERE id = $id";
        mysqli_query($conn, $sql);
        $message = "Order updated!";
    }
}

// Get all sliders
$sliders_query = "SELECT * FROM sliders ORDER BY display_order ASC, id DESC";
$sliders = mysqli_query($conn, $sliders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider Management - Kerala Fest 2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .preview-container {
            position: relative;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            background: #f8f9fa;
        }
        .preview-container img,
        .preview-container video {
            width: 100%;
            height: auto;
            display: block;
        }
        .media-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .video-badge {
            background: rgba(220,53,69,0.9);
        }
        .image-badge {
            background: rgba(40,167,69,0.9);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Slider Management</span>
            <div>
                <a href="index.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="logo_management.php" class="btn btn-outline-light me-2">Logos</a>
                <a href="minute_updates.php" class="btn btn-outline-light me-2">Updates</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Add New Slider Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Add New Slider</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="sliderForm">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Media Type</label>
                                <select name="media_type" id="mediaType" class="form-select" required>
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Desktop File</label>
                                <input type="file" name="desktop_file" id="desktopFile" class="form-control" accept="image/*,video/*" required>
                                <small class="text-muted" id="desktopHelp">Max 5MB for image, 50MB for video</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mobile File</label>
                                <input type="file" name="mobile_file" id="mobileFile" class="form-control" accept="image/*,video/*" required>
                                <small class="text-muted" id="mobileHelp">Optimized for mobile screens</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="1" min="1" required>
                            </div>
                            
                            <button type="submit" name="add_slider" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add Slider
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Sliders -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Existing Sliders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Preview</th>
                                        <th>Details</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($slider = mysqli_fetch_assoc($sliders)): ?>
                                    <tr>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="slider_id" value="<?php echo $slider['id']; ?>">
                                                <input type="number" name="new_order" value="<?php echo $slider['display_order']; ?>" 
                                                       style="width: 60px;" class="form-control form-control-sm d-inline">
                                                <button type="submit" name="update_order" class="btn btn-sm btn-link">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="preview-container" style="width: 150px; height: 100px;">
                                                <?php if ($slider['media_type'] == 'video'): ?>
                                                    <video style="width: 100%; height: 100%; object-fit: cover;" muted>
                                                        <source src="../uploads/sliders/desktop/<?php echo $slider['desktop_file']; ?>">
                                                    </video>
                                                    <span class="media-badge video-badge">
                                                        <i class="bi bi-play-circle"></i> VIDEO
                                                    </span>
                                                <?php else: ?>
                                                    <img src="../uploads/sliders/desktop/<?php echo $slider['desktop_file']; ?>" 
                                                         alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                                    <span class="media-badge image-badge">
                                                        <i class="bi bi-image"></i> IMAGE
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($slider['title']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars(substr($slider['description'], 0, 50)); ?><?php echo strlen($slider['description']) > 50 ? '...' : ''; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $slider['media_type'] == 'video' ? 'danger' : 'success'; ?>">
                                                <?php echo strtoupper($slider['media_type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="slider_id" value="<?php echo $slider['id']; ?>">
                                                <button type="submit" name="toggle_status" class="btn btn-sm btn-<?php echo $slider['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($slider['status']); ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this slider?');">
                                                <input type="hidden" name="slider_id" value="<?php echo $slider['id']; ?>">
                                                <button type="submit" name="delete_slider" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update file input accept types based on media type selection
        document.getElementById('mediaType').addEventListener('change', function() {
            const desktopFile = document.getElementById('desktopFile');
            const mobileFile = document.getElementById('mobileFile');
            const desktopHelp = document.getElementById('desktopHelp');
            const mobileHelp = document.getElementById('mobileHelp');
            
            if (this.value === 'video') {
                desktopFile.accept = 'video/mp4,video/webm,video/ogg';
                mobileFile.accept = 'video/mp4,video/webm,video/ogg';
                desktopHelp.textContent = 'Max 50MB for video (MP4, WebM, OGG)';
                mobileHelp.textContent = 'Optimized video for mobile (MP4 recommended)';
            } else {
                desktopFile.accept = 'image/jpeg,image/png,image/gif,image/webp';
                mobileFile.accept = 'image/jpeg,image/png,image/gif,image/webp';
                desktopHelp.textContent = 'Max 5MB for image (JPG, PNG, GIF, WebP)';
                mobileHelp.textContent = 'Optimized for mobile screens';
            }
        });
        
        // File size validation
        document.getElementById('sliderForm').addEventListener('submit', function(e) {
            const mediaType = document.getElementById('mediaType').value;
            const desktopFile = document.getElementById('desktopFile').files[0];
            const mobileFile = document.getElementById('mobileFile').files[0];
            const maxSize = mediaType === 'video' ? 50 * 1024 * 1024 : 5 * 1024 * 1024;
            
            if (desktopFile && desktopFile.size > maxSize) {
                e.preventDefault();
                alert('Desktop file is too large! Max size: ' + (mediaType === 'video' ? '50MB' : '5MB'));
                return false;
            }
            
            if (mobileFile && mobileFile.size > maxSize) {
                e.preventDefault();
                alert('Mobile file is too large! Max size: ' + (mediaType === 'video' ? '50MB' : '5MB'));
                return false;
            }
        });
    </script>
</body>
</html>