<?php
// test-payment.php - Simple test to verify everything works

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head><title>Payment Test</title></head>";
echo "<body>";
echo "<h1>Payment Test Page</h1>";
echo "<p>This file is working correctly!</p>";

// Check if we can access database
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
    echo "<p style='color:green;'>✓ Database config found</p>";
    
    // Test database connection
    if ($conn) {
        echo "<p style='color:green;'>✓ Database connected</p>";
        
        // Check if registration_id is provided
        if (isset($_GET['registration_id'])) {
            $id = (int)$_GET['registration_id'];
            echo "<h2>Looking for registration ID: " . $id . "</h2>";
            
            $query = "SELECT * FROM registrations WHERE id = $id";
            $result = mysqli_query($conn, $query);
            
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    $data = mysqli_fetch_assoc($result);
                    echo "<h3 style='color:green;'>Registration Found!</h3>";
                    echo "<pre>";
                    print_r($data);
                    echo "</pre>";
                } else {
                    echo "<p style='color:red;'>No registration found with ID: $id</p>";
                }
            } else {
                echo "<p style='color:red;'>Query error: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p>No registration_id provided in URL</p>";
            
            // Show last 5 registrations
            echo "<h3>Recent Registrations:</h3>";
            $query = "SELECT id, unique_id, name, payment_status FROM registrations ORDER BY id DESC LIMIT 5";
            $result = mysqli_query($conn, $query);
            
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<p>";
                    echo "ID: " . $row['id'] . " | ";
                    echo "Unique: " . $row['unique_id'] . " | ";
                    echo "Name: " . $row['name'] . " | ";
                    echo "Status: " . $row['payment_status'] . " | ";
                    echo "<a href='?registration_id=" . $row['id'] . "'>Test This</a>";
                    echo "</p>";
                }
            }
        }
    } else {
        echo "<p style='color:red;'>✗ Database connection failed</p>";
    }
} else {
    echo "<p style='color:red;'>✗ Database config not found</p>";
}

echo "</body>";
echo "</html>";
?>