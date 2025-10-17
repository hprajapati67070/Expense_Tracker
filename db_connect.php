<?php
// inc/db_connect.php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = ''; // <--- put your MySQL root password (or user password)
$DB_NAME = 'expense_tracker';


$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
die('Database connection error: ' . mysqli_connect_error());
}
// set charset
mysqli_set_charset($conn, 'utf8mb4');