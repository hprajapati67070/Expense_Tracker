<?php
include("inc/header.php");
include("inc/db_connect.php");


// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if success message should be shown
$success = false;
if (isset($_GET['msg']) && $_GET['msg'] === 'success') {
    $success = true;
}
?>

<section class="add-expense">
    <div class="container">
        <h2>Add New Expense</h2>

        <!-- Success Message -->
        <?php if($success): ?>
            <p style="color:#00bcd4; text-align:center; margin-bottom:20px;">
                Expense added successfully!
            </p>
        <?php endif; ?>

        <form action="insert_expense.php" method="POST" onsubmit="return validateForm()">
            <label for="expense_date">Date:</label>
            <input type="date" name="expense_date" id="expense_date" required>

            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" placeholder="Enter amount" required min="0.01" step="0.01">

            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <option value="">-- Select Category --</option>
                <option value="Food">Food</option>
                <option value="Transport">Transport</option>
                <option value="Shopping">Shopping</option>
                <option value="Bills">Bills</option>
                <option value="Entertainment">Entertainment</option>
                <option value="Other">Other</option>
            </select>

            <label for="description">Description:</label>
            <textarea name="description" id="description" placeholder="Optional description"></textarea>

            <!-- Buttons Container -->
            <div style="display:flex; gap:15px; justify-content:center; margin-top:15px;">
                <button type="submit">Add Expense</button>

                <!-- Show View Expenses button only after successful add -->
                <?php if($success): ?>
                    <a href="view_expense.php" class="btn">View Expenses</a>
                <?php endif; ?>
            </div>
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
