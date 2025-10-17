<?php
// Database connection settings
$servername = "localhost";
$username   = "root";        // default in XAMPP
$password   = "";            // default in XAMPP (empty)
$dbname     = "expense_db";  // must be created in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
