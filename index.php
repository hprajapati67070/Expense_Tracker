<?php
include("inc/header.php");
include("inc/db_connect.php");

// If user is logged in, get their stats
$total_spent = 0;
$recent_expenses = [];
$chart_data = [];
$spending_limit = 0;
$status_message = "";

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user_query = mysqli_query($conn, "SELECT id, spending_limit FROM users WHERE username='$username'");
    $user_row = mysqli_fetch_assoc($user_query);
    $user_id = $user_row['id'];
    $spending_limit = $user_row['spending_limit'] ?? 0;

    // Handle form submission for spending limit
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['limit'])) {
        $new_limit = floatval($_POST['limit']);
        mysqli_query($conn, "UPDATE users SET spending_limit='$new_limit' WHERE id='$user_id'");
        $spending_limit = $new_limit;
    }

    // Total spent
    $total_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE user_id='$user_id'");
    $total_row = mysqli_fetch_assoc($total_query);
    $total_spent = $total_row['total'] ?? 0;

    // Spending status
    if ($spending_limit > 0) {
        if ($total_spent < $spending_limit * 0.8) {
            $status_message = "<span style='color:#4caf50;'>✅ Within limit</span>";
        } elseif ($total_spent < $spending_limit) {
            $status_message = "<span style='color:#ffc107;'>⚠️ Nearing limit</span>";
        } else {
            $status_message = "<span style='color:#f44336;'>❌ Exceeded limit</span>";
        }
    }

    // Last 4 transactions
    $recent_query = mysqli_query($conn, "SELECT * FROM expenses WHERE user_id='$user_id' ORDER BY expense_date DESC LIMIT 4");
    while ($row = mysqli_fetch_assoc($recent_query)) {
        $recent_expenses[] = $row;
    }

    // Category-wise totals for pie chart
    $chart_query = mysqli_query($conn, "SELECT category, SUM(amount) AS total FROM expenses WHERE user_id='$user_id' GROUP BY category");
    while ($row = mysqli_fetch_assoc($chart_query)) {
        $chart_data[] = $row;
    }
}
?>

<section class="home">
    <div class="container" style="text-align:center; padding:50px 20px;">
        <h1>Welcome to Expense Tracker 💰</h1>
        <p style="margin-top:15px; font-size:18px;">
            Keep track of your expenses, manage your budget, and visualize your spending habits easily.
        </p>

        <div style="margin-top:30px;">
            <?php if (!isset($_SESSION['username'])): ?>
                <a href="register.php" class="btn">Register</a>
                <a href="login.php" class="btn">Login</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn">Go to Dashboard</a>
                <a href="add_expense.php" class="btn">Add Expense</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (isset($_SESSION['username'])): ?>
<section class="home-stats container">
    <h2>Your Spending Limit</h2>
    <form method="post" style="max-width:400px; margin:auto; text-align:center;">
        <label for="limit">Set Your Limit (₹)</label>
        <input type="number" step="0.01" name="limit" id="limit" value="<?php echo $spending_limit; ?>" required>
        <button type="submit">Save Limit</button>
    </form>
    <p style="margin-top:10px; text-align:center;">
        <?php if ($spending_limit > 0): ?>
            Limit: ₹<?php echo number_format($spending_limit, 2); ?> |
            Spent: ₹<?php echo number_format($total_spent, 2); ?> <br>
            Status: <?php echo $status_message; ?>
        <?php else: ?>
            <em>No limit set yet.</em>
        <?php endif; ?>
    </p>
</section>

<section class="home-stats container">
    <h2>Your Stats</h2>
    <div class="stats-cards">
        <div class="card">
            <h3>Total Spent</h3>
            <p>₹<?php echo number_format($total_spent,2); ?></p>
        </div>
        <div class="card">
            <h3>Last 4 Transactions</h3>
            <?php if (count($recent_expenses) > 0): ?>
                <ul>
                    <?php foreach ($recent_expenses as $exp): ?>
                        <li><?php echo htmlspecialchars($exp['category']).": ₹".number_format($exp['amount'],2); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No transactions yet.</p>
            <?php endif; ?>
        </div>
        <div class="card">
            <h3>Spending Overview</h3>
            <div class="chart-container" style="width:250px; height:250px; margin:auto;">
                <canvas id="homeChart"></canvas>
            </div>
        </div>
    </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxHome = document.getElementById('homeChart').getContext('2d');
const dataHome = {
    labels: [
        <?php
        $labels = [];
        $totals = [];
        foreach ($chart_data as $row) {
            $labels[] = "'".$row['category']."'";
            $totals[] = $row['total'];
        }
        echo implode(',', $labels);
        ?>
    ],
    datasets: [{
        label: 'Expenses by Category',
        data: [<?php echo implode(',', $totals); ?>],
        backgroundColor: ['#00bcd4','#ff5722','#4caf50','#ffc107','#9c27b0','#f44336']
    }]
};
new Chart(ctxHome, {
    type: 'pie',
    data: dataHome,
    options: {
        plugins: {
            legend: { labels: { color: '#e0e0e0' } }
        }
    }
});
</script>
<?php endif; ?>

<?php include("inc/footer.php"); ?>
