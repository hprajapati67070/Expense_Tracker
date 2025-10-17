<?php
include("inc/db_connect.php");
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Process POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $expense_date = $_POST['expense_date'] ?? ''; // updated key
    $amount = $_POST['amount'] ?? '';
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validate required fields
    if (empty($expense_date) || empty($amount) || empty($category) || $amount <= 0) {
        echo "Please fill in all required fields correctly.";
        exit();
    }

    // Convert date to MySQL format
    $expense_date = date('Y-m-d', strtotime($expense_date));

    // Get user ID
    $username = $_SESSION['username'];
    $user_query = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    $user_row = mysqli_fetch_assoc($user_query);
    $user_id = $user_row['id'];

    // Update expense in database
    $stmt = $conn->prepare("UPDATE expenses SET expense_date=?, amount=?, category=?, description=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sdssii", $expense_date, $amount, $category, $description, $id, $user_id);

    if ($stmt->execute()) {
        header("Location: view_expense.php?msg=updated");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    header("Location: view_expense.php");
    exit();
}
?>
