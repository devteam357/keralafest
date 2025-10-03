<?php
// test-session.php - Test if sessions are working properly

session_start();

echo "<h2>Session Test Page</h2>";

// Set a test session variable
if (isset($_GET['set'])) {
    $_SESSION['test'] = 'Session is working! Time: ' . date('Y-m-d H:i:s');
    $_SESSION['registration_test'] = [
        'id' => 123,
        'name' => 'Test User'
    ];
    echo "<p>✅ Session variables set!</p>";
}

// Clear session
if (isset($_GET['clear'])) {
    session_destroy();
    echo "<p>🗑️ Session cleared!</p>";
}

// Display session info
echo "<h3>Session Information:</h3>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Name: " . session_name() . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
echo "Session Status: " . session_status() . "\n\n";

echo "Session Variables:\n";
print_r($_SESSION);
echo "</pre>";

// Show links
echo "<h3>Actions:</h3>";
echo "<a href='?set=1' class='btn'>Set Session Variables</a> | ";
echo "<a href='?clear=1' class='btn'>Clear Session</a> | ";
echo "<a href='test-session.php' class='btn'>Refresh</a> | ";
echo "<a href='index.php' class='btn'>Go to Index</a> | ";
echo "<a href='payment.php' class='btn'>Go to Payment</a>";

// Check recent registrations
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
    
    echo "<h3>Recent Registrations in Database:</h3>";
    $query = "SELECT id, unique_id, name, payment_status FROM registrations ORDER BY id DESC LIMIT 5";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Unique ID</th><th>Name</th><th>Status</th><th>Action</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['unique_id']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['payment_status']}</td>";
            echo "<td><a href='payment.php?registration_id={$row['id']}'>Test Payment</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Style
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; }
h2, h3 { color: #333; }
pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }
.btn { 
    display: inline-block; 
    padding: 8px 15px; 
    background: #007bff; 
    color: white; 
    text-decoration: none; 
    border-radius: 5px; 
    margin: 5px;
}
.btn:hover { background: #0056b3; }
table { border-collapse: collapse; margin-top: 10px; }
th { background: #007bff; color: white; }
td, th { padding: 8px; }
</style>";
?>