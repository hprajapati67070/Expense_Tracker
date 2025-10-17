<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("inc/header.php");
include("inc/db_connect.php");


// Redirect if already logged in
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

$msg = "";
$error = "";

// Process POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $error = "Please fill all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $error = "No account found with this email.";
        } else {
            $stmt->bind_result($user_id);
            $stmt->fetch();

            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                $msg = "Password updated successfully. <a href='login.php' style='color:#00bcd4;'>Login here</a>.";
            } else {
                $error = "Failed to update password: " . $update_stmt->error;
            }
            $update_stmt->close();
        }
        $stmt->close();
    }
}
?>

<section class="reset-password">
    <div class="container">
        <form method="POST" action="">
            <h2>Reset Password</h2>

            <?php if(!empty($msg)): ?>
                <p style="color:#00bcd4; text-align:center;"><?php echo $msg; ?></p>
            <?php endif; ?>

            <?php if(!empty($error)): ?>
                <p style="color:#f44336; text-align:center;"><?php echo $error; ?></p>
            <?php endif; ?>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit">Reset Password</button>
            <p style="margin-top:10px; text-align:center;">Remembered? <a href="login.php" style="color:#00bcd4;">Login here</a></p>
        </form>
    </div>
</section>

<?php
include("inc/footer.php");
?>
