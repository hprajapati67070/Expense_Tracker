<?php
include("inc/header.php");
include("inc/db_connect.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_query = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
$user_row = mysqli_fetch_assoc($user_query);
$user_id = $user_row['id'];

// Fetch all expenses
$expenses_query = mysqli_query($conn, "SELECT * FROM expenses WHERE user_id='$user_id' ORDER BY expense_date DESC");

$msg = "";
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == "deleted") $msg = "Expense deleted successfully.";
    elseif ($_GET['msg'] == "updated") $msg = "Expense updated successfully.";
}
?>

<section class="view-expense">
    <div class="container">
        <h2>Your Expenses</h2>
        <?php if($msg != ""): ?>
            <p style="color:#00bcd4;"><?php echo $msg; ?></p>
        <?php endif; ?>

        <table>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <?php if(mysqli_num_rows($expenses_query) > 0): ?>
                <?php while($expense = mysqli_fetch_assoc($expenses_query)): ?>
                    <tr>
                        <td><?php echo $expense['expense_date']; ?></td>
                        <td><?php echo htmlspecialchars($expense['category']); ?></td>
                        <td><?php echo number_format($expense['amount'],2); ?></td>
                        <td><?php echo htmlspecialchars($expense['description']); ?></td>
                        <td>
                            <a href="edit_expense.php?id=<?php echo $expense['id']; ?>" class="btn">Edit</a>
                            <a href="delete_expense.php?id=<?php echo $expense['id']; ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">No expenses found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</section>

<?php
include("inc/footer.php");
?>

