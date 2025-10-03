<?php
// Database configuration
$db_host = "localhost";
$db_user = "u361874700_keralaanna";
$db_pass = "0ksM:p?7Q:";
$db_name = "u361874700_kerala";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h2>Database Structure Check</h2>";

// Check registrations table
echo "<h3>Registrations Table:</h3>";
$result = mysqli_query($conn, "SHOW COLUMNS FROM registrations");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red'>Table doesn't exist!</p>";
    
    // Create table
    $create_sql = "CREATE TABLE registrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        unique_id VARCHAR(50),
        name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(15),
        additional_count INT DEFAULT 0,
        total_amount DECIMAL(10,2),
        payment_id VARCHAR(100),
        payment_status VARCHAR(20) DEFAULT 'pending',
        qr_code TEXT,
        attended VARCHAR(10) DEFAULT 'No',
        attended_at TIMESTAMP NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $create_sql)) {
        echo "<p style='color:green'>Table created successfully!</p>";
    } else {
        echo "<p style='color:red'>Error creating table: " . mysqli_error($conn) . "</p>";
    }
}

// Check attendees table
echo "<h3>Attendees Table:</h3>";
$result2 = mysqli_query($conn, "SHOW COLUMNS FROM attendees");
if ($result2) {
    echo "<table border='1'>";
    echo "<tr><th>Column</th><th>Type</th></tr>";
    while ($row = mysqli_fetch_assoc($result2)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red'>Attendees table doesn't exist!</p>";
    
    // Create attendees table
    $create_attendees = "CREATE TABLE attendees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        registration_id INT,
        attendee_unique_id VARCHAR(50),
        attendee_name VARCHAR(100),
        qr_code TEXT,
        attended VARCHAR(10) DEFAULT 'No',
        attended_at TIMESTAMP NULL,
        FOREIGN KEY (registration_id) REFERENCES registrations(id)
    )";
    
    if (mysqli_query($conn, $create_attendees)) {
        echo "<p style='color:green'>Attendees table created successfully!</p>";
    }
}

// Check settings table
echo "<h3>Settings Table:</h3>";
$result3 = mysqli_query($conn, "SELECT * FROM settings WHERE id = 1");
if ($result3 && mysqli_num_rows($result3) > 0) {
    $settings = mysqli_fetch_assoc($result3);
    echo "<table border='1'>";
    foreach ($settings as $key => $value) {
        echo "<tr><td>$key</td><td>$value</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red'>Settings not found!</p>";
    
    // Insert default settings
    $insert_settings = "INSERT INTO settings (id, site_url, event_name, razorpay_key, razorpay_secret) 
                       VALUES (1, 'https://keralafest.theconversions.com', 'Kerala Fest 2025', 'rzp_test_xxxxx', 'secret_xxxxx')";
    
    if (mysqli_query($conn, $insert_settings)) {
        echo "<p style='color:green'>Settings added successfully!</p>";
    }
}

mysqli_close($conn);
?>