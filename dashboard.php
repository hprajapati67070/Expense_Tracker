<?php
include("inc/header.php");
include("inc/db_connect.php");

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Get user info
$user_query = mysqli_query($conn, "SELECT id, spending_limit FROM users WHERE username='$username'");
$user_row = mysqli_fetch_assoc($user_query);
$user_id = $user_row['id'];
$spending_limit = $user_row['spending_limit'] ?? 0;

// Total spent
$total_query = mysqli_query($conn, "SELECT SUM(amount) AS total FROM expenses WHERE user_id='$user_id'");
$total_row = mysqli_fetch_assoc($total_query);
$total_spent = $total_row['total'] ?? 0;

// Recent transactions
$recent_expenses = [];
$recent_query = mysqli_query($conn, "SELECT * FROM expenses WHERE user_id='$user_id' ORDER BY expense_date DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($recent_query)) {
    $recent_expenses[] = $row;
}

// Chart data
$chart_data = [];
$chart_query = mysqli_query($conn, "SELECT category, SUM(amount) AS total FROM expenses WHERE user_id='$user_id' GROUP BY category");
while ($row = mysqli_fetch_assoc($chart_query)) {
    $chart_data[] = $row;
}

// Spending status
$status = "No limit set";
if ($spending_limit > 0) {
    if ($total_spent > $spending_limit) {
        $status = "<span style='color:#f44336;'>Limit Exceeded 🚨</span>";
    } else {
        $status = "<span style='color:#4caf50;'>Within Limit ✅</span>";
    }
}
?>

<section class="dashboard container">
    <h2>Your Dashboard</h2>
    <div class="stats-cards">
        <div class="card">
            <h3>Total Spent</h3>
            <p>₹<?php echo number_format($total_spent, 2); ?></p>
        </div>

        <div class="card">
            <h3>Spending Limit</h3>
            <p>₹<?php echo number_format($spending_limit, 2); ?></p>
            <p>Status: <?php echo $status; ?></p>
        </div>

        <div class="card">
            <h3>Recent Transactions</h3>
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
            <div class="chart-container" style="width:350px; height:350px; margin:auto;">
                <canvas id="dashChart"></canvas>
            </div>
        </div>
    </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxDash = document.getElementById('dashChart').getContext('2d');
const dataDash = {
    labels: [
        <?php
        $labels = [];
        $totals = [];
        foreach($chart_data as $row){
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
new Chart(ctxDash, {
    type: 'pie',
    data: dataDash,
    options: {
        plugins: {
            legend: {
                labels: { color: '#e0e0e0' }
            }
        }
    }
});
</script>

<?php include("inc/footer.php"); ?>
