<?php
session_start();
require_once "config/db_connect.php";

// Protect route
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

$payment_id = $_GET['id'] ?? 0;

// Fetch payment
$sql = "SELECT * FROM payments WHERE payment_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();

if (!$payment) {
    echo "Payment not found.";
    exit;
}

// Fetch orders for dropdown
$orders = $conn->query("SELECT o.order_id, c.name AS customer_name FROM orders o JOIN customers c ON o.customer_id=c.customer_id ORDER BY o.order_id DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $method   = $_POST['method'];
    $amount   = $_POST['amount'];
    $date     = $_POST['date'];

    $update_sql = "UPDATE payments SET order_id=?, method=?, amount=?, date=? WHERE payment_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("isdsi", $order_id, $method, $amount, $date, $payment_id);

    if ($stmt->execute()) {
        header("Location: payments_list.php?msg=updated");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Payment</title>
</head>
<body>
    <h2>Edit Payment</h2>
    <a href="payments_list.php">⬅ Back to Payments</a>
    <hr>

    <form method="POST">
        <label>Order:</label>
        <select name="order_id" required>
            <?php while ($o = $orders->fetch_assoc()): ?>
                <option value="<?= $o['order_id'] ?>" <?= ($o['order_id'] == $payment['order_id']) ? 'selected' : '' ?>>
                    Order #<?= $o['order_id'] ?> - <?= htmlspecialchars($o['customer_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Payment Method:</label>
        <select name="method" required>
            <option value="Cash" <?= ($payment['method']=="Cash")?'selected':'' ?>>Cash</option>
            <option value="M-Pesa" <?= ($payment['method']=="M-Pesa")?'selected':'' ?>>M-Pesa</option>
            <option value="Bank Transfer" <?= ($payment['method']=="Bank Transfer")?'selected':'' ?>>Bank Transfer</option>
        </select>
        <br><br>

        <label>Amount:</label>
        <input type="number" step="0.01" name="amount" value="<?= $payment['amount'] ?>" required>
        <br><br>

        <label>Date:</label>
        <input type="date" name="date" value="<?= $payment['date'] ?>" required>
        <br><br>

        <button type="submit">Update Payment</button>
    </form>
</body>
</html>
