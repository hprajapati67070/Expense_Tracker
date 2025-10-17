<?php
// Start session on every page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Expense Tracker</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header>
    <nav class="navbar">
      <h2 class="logo">💰 Expense Tracker</h2>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="add_expense.php">Add Expense</a></li>
        <li><a href="view_expense.php">View Expenses</a></li>
        <li><a href="about.php">About</a></li>
        <?php if (isset($_SESSION['username'])): ?>
          <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  <main>
