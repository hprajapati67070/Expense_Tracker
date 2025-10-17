<?php
include("inc/db_connect.php");
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];

    $expense_date = $_POST['expense_date'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validate required fields
    if (empty($expense_date) || empty($amount) || empty($category) || $amount <= 0) {
        echo "Please fill in all required fields correctly.";
        exit();
    }

    // Validate date format
    $d = DateTime::createFromFormat('Y-m-d', $expense_date);
    if (!$d || $d->format('Y-m-d') !== $expense_date) {
        echo "Invalid date format.";
        exit();
    }

    // Get user ID
    $user_query = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    $user_row = mysqli_fetch_assoc($user_query);
    $user_id = $user_row['id'];

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO expenses (user_id, expense_date, amount, category, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $user_id, $expense_date, $amount, $category, $description);

    if (!$stmt->execute()) {
        die("Insert failed: " . $stmt->error);
    }

    $stmt->close();
    header("Location: add_expense.php?msg=success");
    exit();
} else {
    header("Location: add_expense.php");
    exit();
}
?>
