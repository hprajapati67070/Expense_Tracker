<?php
include("inc/header.php");
include("inc/db_connect.php");

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if expense ID is provided
if (!isset($_GET['id'])) {
    header("Location: view_expense.php");
    exit();
}

$expense_id = intval($_GET['id']);

// Get user ID
$username = $_SESSION['username'];
$user_query = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
$user_row = mysqli_fetch_assoc($user_query);
$user_id = $user_row['id'];

// Fetch expense data for this user
$expense_query = mysqli_query($conn, "SELECT * FROM expenses WHERE id='$expense_id' AND user_id='$user_id'");
if (mysqli_num_rows($expense_query) == 0) {
    echo "<p style='color:red; text-align:center;'>Expense not found or you don't have permission to edit it.</p>";
    include("inc/footer.php");
    exit();
}

$expense = mysqli_fetch_assoc($expense_query);
?>

<section class="edit-expense">
    <div class="container">
        <h2>Edit Expense</h2>

        <form action="update_expense.php" method="POST" onsubmit="return validateForm()">
            <input type="hidden" name="id" value="<?php echo $expense['id']; ?>">

            <label for="expense_date">Date:</label>
            <input type="date" name="expense_date" id="expense_date" 
                   value="<?php echo htmlspecialchars($expense['expense_date']); ?>" required>

            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" 
                   value="<?php echo htmlspecialchars($expense['amount']); ?>" required min="0.01" step="0.01">

            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <option value="">-- Select Category --</option>
                <?php
                $categories = ["Food","Transport","Shopping","Bills","Entertainment","Other"];
                foreach($categories as $cat){
                    $selected = ($expense['category'] == $cat) ? "selected" : "";
                    echo "<option value='$cat' $selected>$cat</option>";
                }
                ?>
            </select>

            <label for="description">Description:</label>
            <textarea name="description" id="description"><?php echo htmlspecialchars($expense['description']); ?></textarea>

            <button type="submit">Update Expense</button>
        </form>
    </div>
</section>

<script>
function validateForm() {
    const amount = document.getElementById('amount').value;
    const category = document.getElementById('category').value;
    const date = document.getElementById('expense_date').value;

    if (!date) {
        alert("Please select a date");
        return false;
    }
    if (amount <= 0) {
        alert("Please enter a valid amount");
        return false;
    }
    if (!category) {
        alert("Please select a category");
        return false;
    }
    return true;
}
</script>

<?php
include("inc/footer.php");
?>
