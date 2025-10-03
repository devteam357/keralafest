<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner - Kerala Fest 2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #30303b 0%, #1a1a2e 100%);
            --warning-gradient: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f0f2f5;
        }

        .navbar {
            background: var(--dark-gradient) !important;
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .scanner-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 20px;
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            font-weight: 600;
            color: white;
        }

        #reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .scan-result {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideIn 0.5s;
        }

        .scan-result.success {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }

        .scan-result.error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
        }

        .scan-result.warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
        }

        .scan-result.info {
            background: #d1ecf1;
            border: 2px solid #17a2b8;
            color: #0c5460;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stat-box {
            text-align: center;
            padding: 15px;
        }

        .stat-box h3 {
            font-size: 2.5rem;
            margin: 0;
            color: #667eea;
        }

        .history-item {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 3px solid #667eea;
        }

        .btn-primary { background: var(--primary-gradient); border: none; }
        .btn-success { background: var(--success-gradient); border: none; }
        .btn-danger { background: var(--danger-gradient); border: none; }
        .bg-primary { background: var(--primary-gradient) !important; }
        .bg-info { background: var(--info-gradient) !important; }
        .bg-dark { background: var(--dark-gradient) !important; }
        .bg-secondary { background: var(--warning-gradient) !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand">📷 QR Check-in Scanner</span>
            <div>
                <a href="index.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="attendees.php" class="btn btn-outline-light me-2">Attendees</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="scanner-container">
        <!-- Scanner Result Display -->
        <div id="scan-result"></div>

        <div class="row">
            <!-- Left Column: Scanner -->
            <div class="col-lg-6">
                <!-- QR Scanner Card -->
                <div class="card">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0">📷 QR Code Scanner</h5>
                    </div>
                    <div class="card-body">
                        <div id="reader"></div>
                        <div class="text-center mt-3">
                            <button id="start-scan" class="btn btn-success" onclick="startScanner()">
                                Start Scanner
                            </button>
                            <button id="stop-scan" class="btn btn-danger" onclick="stopScanner()" style="display:none;">
                                Stop Scanner
                            </button>
                        </div>
                        
                        <!-- File Upload Option -->
                        <div class="text-center mt-3">
                            <input type="file" id="qr-file" accept="image/*" style="display:none;" onchange="handleFileUpload(this)">
                            <button class="btn btn-secondary btn-sm" onclick="document.getElementById('qr-file').click()">
                                📁 Upload QR Image
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manual Check-in Card -->
                <div class="card">
                    <div class="card-header bg-dark">
                        <h5 class="mb-0">⌨️ Manual Check-in</h5>
                    </div>
                    <div class="card-body">
                        <form onsubmit="manualCheckIn(event)">
                            <div class="mb-3">
                                <label class="form-label">Enter Unique ID:</label>
                                <input type="text" 
                                       id="manual-id" 
                                       class="form-control" 
                                       placeholder="e.g. KERF00022 or EVT2025XXXX" 
                                       style="text-transform: uppercase; font-family: monospace;"
                                       required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                Check In
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Statistics -->
            <div class="col-lg-6">
                <!-- Today's Stats -->
                <div class="card">
                    <div class="card-header bg-info">
                        <h5 class="mb-0">📊 Today's Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 stat-box">
                                <h3 id="checked-count">0</h3>
                                <small>Checked In</small>
                            </div>
                            <div class="col-6 stat-box">
                                <h3 id="total-count">0</h3>
                                <small>Total Registered</small>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 25px;">
                            <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%">0%</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Check-ins -->
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h5 class="mb-0">🕒 Recent Check-ins</h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <div id="recent-checkins">
                            <p class="text-muted text-center">No check-ins yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let html5QrCode = null;
    let recentCheckins = [];

    // Initialize on page load
    window.onload = function() {
        loadStatistics();
        setInterval(loadStatistics, 30000); // Refresh every 30 seconds
    };

    // Start QR Scanner
    function startScanner() {
        document.getElementById('start-scan').style.display = 'none';
        document.getElementById('stop-scan').style.display = 'inline-block';
        
        html5QrCode = new Html5Qrcode("reader");
        
        html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            (decodedText) => {
                console.log("QR Code detected:", decodedText);
                processQRCode(decodedText);
            },
            (errorMessage) => {
                // Ignore errors silently
            }
        ).catch((err) => {
            console.error("Error starting scanner:", err);
            alert("Unable to access camera. Please check permissions or use manual check-in.");
            stopScanner();
        });
    }

    // Stop QR Scanner
    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                document.getElementById('start-scan').style.display = 'inline-block';
                document.getElementById('stop-scan').style.display = 'none';
            }).catch((err) => {
                console.error("Error stopping scanner:", err);
            });
        }
    }

    // Handle File Upload
    function handleFileUpload(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }
            
            html5QrCode.scanFile(file, true)
                .then(decodedText => {
                    console.log("QR from file:", decodedText);
                    processQRCode(decodedText);
                })
                .catch(err => {
                    alert("No QR code found in the image");
                });
            
            input.value = '';
        }
    }

    // Process QR Code
    function processQRCode(qrData) {
        // Extract ID from URL format: https://keralafest.in/admin/scan.php?id=KERF00022
        let ticketId = '';
        
        if (qrData.includes('?id=')) {
            const urlParams = new URLSearchParams(qrData.split('?')[1]);
            ticketId = urlParams.get('id');
        } else {
            ticketId = qrData;
        }
        
        console.log("Extracted ID:", ticketId);
        
        if (ticketId) {
            checkInAttendee(ticketId);
        } else {
            showResult('error', 'Invalid QR Code', 'Could not extract ID from QR code');
        }
    }

    // Manual Check-in
    function manualCheckIn(event) {
        event.preventDefault();
        const ticketId = document.getElementById('manual-id').value.trim().toUpperCase();
        
        if (ticketId) {
            checkInAttendee(ticketId);
            document.getElementById('manual-id').value = '';
        }
    }

    // Check-in Attendee
    function checkInAttendee(ticketId) {
        showResult('info', 'Processing...', 'Checking ID: ' + ticketId);
        
        // IMPORTANT: scan_qr.php is in the same directory (admin/)
        fetch('scan_qr.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                unique_id: ticketId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log("Response:", data);
            
            if (data.success) {
                showResult('success', '✅ Check-in Successful!', 
                    `<strong>Name:</strong> ${data.name}<br>
                     <strong>ID:</strong> ${data.unique_id}<br>
                     <strong>Time:</strong> ${data.check_in_time}`
                );
                
                addToRecentCheckins(data);
                loadStatistics();
            } else if (data.already_checked) {
                showResult('warning', '⚠️ Already Checked In', 
                    `${data.name} was already checked in at ${data.check_in_time}`
                );
            } else {
                showResult('error', '❌ Check-in Failed', data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showResult('error', '❌ Error', 'Network error or scan_qr.php not found. Check console for details.');
        });
    }

    // Show Result
    function showResult(type, title, message) {
        const resultDiv = document.getElementById('scan-result');
        
        resultDiv.innerHTML = `
            <div class="scan-result ${type}">
                <h4>${title}</h4>
                <p class="mb-0">${message}</p>
            </div>
        `;
        
        if (type !== 'info') {
            setTimeout(() => {
                resultDiv.innerHTML = '';
            }, 5000);
        }
    }

    // Add to Recent Check-ins
    function addToRecentCheckins(data) {
        recentCheckins.unshift({
            name: data.name,
            unique_id: data.unique_id,
            time: new Date().toLocaleTimeString()
        });
        
        if (recentCheckins.length > 10) {
            recentCheckins.pop();
        }
        
        updateRecentCheckinsDisplay();
    }

    // Update Recent Check-ins Display
    function updateRecentCheckinsDisplay() {
        const container = document.getElementById('recent-checkins');
        
        if (recentCheckins.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No check-ins yet</p>';
        } else {
            let html = '';
            recentCheckins.forEach(checkin => {
                html += `
                    <div class="history-item">
                        <strong>${checkin.name}</strong><br>
                        <small class="text-muted">${checkin.unique_id} - ${checkin.time}</small>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
    }

    // Load Statistics  
    function loadStatistics() {
        // IMPORTANT: get_stats.php is in the same directory (admin/)
        fetch('get_stats.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log("Stats:", data);
                
                document.getElementById('checked-count').textContent = data.checked_in || 0;
                document.getElementById('total-count').textContent = data.total || 0;
                
                // Update progress bar
                const percentage = data.percentage || 0;
                const progressBar = document.getElementById('progress-bar');
                progressBar.style.width = percentage + '%';
                progressBar.textContent = percentage + '%';
            })
            .catch(error => {
                console.error('Error loading statistics:', error);
            });
    }
    </script>
</body>
</html>