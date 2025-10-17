<?php
include("inc/header.php");
include("inc/db_connect.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

// Process POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $spending_limit = $_POST['spending_limit'] ?? 0;

    // Server-side validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email already taken.";
        } else {
            // Insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, spending_limit) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("sssd", $username, $email, $hashed_password, $spending_limit);
            if ($insert_stmt->execute()) {
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Registration failed: " . $insert_stmt->error;
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>

<section class="register">
    <div class="container">
        <form method="POST" action="">
            <h2>Register</h2>

            <?php if(!empty($error)): ?>
                <p style="color:#f44336; text-align:center;"><?php echo $error; ?></p>
            <?php endif; ?>

            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <label for="spending_limit">Spending Limit (Optional):</label>
            <input type="number" name="spending_limit" id="spending_limit" placeholder="0.00" min="0" step="0.01">

            <button type="submit">Register</button>
            <p style="margin-top:10px; text-align:center;">Already have an account? <a href="login.php" style="color:#00bcd4;">Login</a></p>
        </form>
    </div>
</section>

<?php
include("inc/footer.php");
?>
