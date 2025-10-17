<?php
include("inc/db_connect.php");
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if 'id' is set
if (isset($_GET['id'])) {
    $expense_id = intval($_GET['id']);

    // Get user ID
    $username = $_SESSION['username'];
    $user_query = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    $user_row = mysqli_fetch_assoc($user_query);
    $user_id = $user_row['id'];

    // Delete expense only if it belongs to this user
    $delete_query = mysqli_query($conn, "DELETE FROM expenses WHERE id='$expense_id' AND user_id='$user_id'");

    if ($delete_query) {
        header("Location: view_expense.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: view_expense.php");
    exit();
}
?>
