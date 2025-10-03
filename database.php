<?php
// Simple database connection
$db_host = "localhost";
$db_user = "u361874700_newkerala_admi";
$db_pass = "Xy9TUOlV!2*";
$db_name = "u361874700_newkerala";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");
?>