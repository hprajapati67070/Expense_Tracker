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

$error = "";

// Process POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = trim($_POST['login_input'] ?? ''); // username or email
    $password = $_POST['password'] ?? '';

    if (empty($login_input) || empty($password)) {
        $error = "Please fill all fields.";
    } else {
        // Check user by username or email
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username=? OR email=? LIMIT 1");
        $stmt->bind_param("ss", $login_input, $login_input);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $error = "No account found with this username/email.";
        } else {
            $stmt->bind_result($user_id, $username, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Set session
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user_id;

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        }
        $stmt->close();
    }
}
?>

<section class="login">
    <div class="container">
        <form method="POST" action="">
            <h2>Login</h2>

            <?php if(!empty($error)): ?>
                <p style="color:#f44336; text-align:center;"><?php echo $error; ?></p>
            <?php endif; ?>

            <label for="login_input">Username or Email:</label>
            <input type="text" name="login_input" id="login_input" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>

            <p style="margin-top:10px; text-align:center;">
                Forgot password? <a href="reset_password.php" style="color:#00bcd4;">Reset here</a>
            </p>
            <p style="margin-top:5px; text-align:center;">
                Don't have an account? <a href="register.php" style="color:#00bcd4;">Register</a>
            </p>
        </form>
    </div>
</section>

<?php
include("inc/footer.php");
?>
