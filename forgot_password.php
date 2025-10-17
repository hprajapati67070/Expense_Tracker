<?php
include("inc/header.php");
include("inc/db_connect.php");
session_start();

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);

    // Check if user exists
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($query) > 0) {
        $_SESSION['reset_user'] = $username;
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "User not found!";
    }
}
?>

<section class="forgot-password">
    <div class="container" style="max-width:400px; margin:auto; padding:50px 20px;">
        <h2>Forgot Password</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form action="" method="POST">
            <label for="username">Enter your Username:</label>
            <input type="text" name="username" id="username" required>
            <button type="submit">Verify</button>
        </form>
    </div>
</section>

<?php include("inc/footer.php"); ?>
