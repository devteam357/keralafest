<?php
// test-qr.php - Test QR code generation to see what works

$test_id = isset($_GET['id']) ? $_GET['id'] : 'KERF00001';
?>
<!DOCTYPE html>
<html>
<head>
    <title>QR Code Test</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .qr-test {
            border: 1px solid #ddd;
            padding: 20px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        .qr-test h3 {
            margin-top: 0;
            color: #333;
        }
        .qr-test img {
            border: 2px solid #333;
            padding: 10px;
            background: white;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .url-display {
            background: #f0f0f0;
            padding: 10px;
            margin: 10px 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>QR Code Generation Test</h1>
    <p>Testing different methods to generate QR codes for ID: <strong><?php echo htmlspecialchars($test_id); ?></strong></p>
    
    <!-- Method 1: Google Charts API -->
    <div class="qr-test">
        <h3>Method 1: Google Charts API</h3>
        <?php 
        $qr_data_1 = "https://keralafest.in/admin/scan.php?id=" . $test_id;
        $qr_url_1 = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($qr_data_1);
        ?>
        <div class="url-display">URL: <?php echo htmlspecialchars($qr_url_1); ?></div>
        <img src="<?php echo $qr_url_1; ?>" alt="QR Code Method 1" onload="this.nextElementSibling.className='success'" onerror="this.nextElementSibling.className='error'">
        <p>Loading status...</p>
        <p>Data encoded: <?php echo htmlspecialchars($qr_data_1); ?></p>
    </div>
    
    <!-- Method 2: QR Server API -->
    <div class="qr-test">
        <h3>Method 2: QR Server API</h3>
        <?php 
        $qr_data_2 = "https://keralafest.in/admin/scan.php?id=" . $test_id;
        $qr_url_2 = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qr_data_2);
        ?>
        <div class="url-display">URL: <?php echo htmlspecialchars($qr_url_2); ?></div>
        <img src="<?php echo $qr_url_2; ?>" alt="QR Code Method 2" onload="this.nextElementSibling.className='success'" onerror="this.nextElementSibling.className='error'">
        <p>Loading status...</p>
        <p>Data encoded: <?php echo htmlspecialchars($qr_data_2); ?></p>
    </div>
    
    <!-- Method 3: Simple text encoding -->
    <div class="qr-test">
        <h3>Method 3: Simple Text QR (just ID)</h3>
        <?php 
        $qr_data_3 = $test_id;
        $qr_url_3 = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($qr_data_3);
        ?>
        <div class="url-display">URL: <?php echo htmlspecialchars($qr_url_3); ?></div>
        <img src="<?php echo $qr_url_3; ?>" alt="QR Code Method 3" onload="this.nextElementSibling.className='success'" onerror="this.nextElementSibling.className='error'">
        <p>Loading status...</p>
        <p>Data encoded: <?php echo htmlspecialchars($qr_data_3); ?></p>
    </div>
    
    <!-- Method 4: QuickChart.io -->
    <div class="qr-test">
        <h3>Method 4: QuickChart.io API</h3>
        <?php 
        $qr_data_4 = "https://keralafest.in/admin/scan.php?id=" . $test_id;
        $qr_url_4 = "https://quickchart.io/qr?text=" . urlencode($qr_data_4) . "&size=300";
        ?>
        <div class="url-display">URL: <?php echo htmlspecialchars($qr_url_4); ?></div>
        <img src="<?php echo $qr_url_4; ?>" alt="QR Code Method 4" onload="this.nextElementSibling.className='success'" onerror="this.nextElementSibling.className='error'">
        <p>Loading status...</p>
        <p>Data encoded: <?php echo htmlspecialchars($qr_data_4); ?></p>
    </div>
    
    <hr>
    
    <h2>Test with different ID:</h2>
    <form method="GET">
        <input type="text" name="id" value="<?php echo htmlspecialchars($test_id); ?>" placeholder="Enter ID like KERF00011">
        <button type="submit">Test This ID</button>
    </form>
    
    <hr>
    
    <h2>Direct Image Test:</h2>
    <p>Can you see this test image from Google?</p>
    <img src="https://via.placeholder.com/300x300.png?text=Test+Image" alt="Test placeholder">
    
</body>
</html>