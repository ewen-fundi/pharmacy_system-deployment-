<?php
session_start();
require_once "config/db_connect.php";

// Protect route
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

// Fetch orders that don’t yet have a payment (to avoid duplicates)
$orders = $conn->query("
    SELECT o.order_id, c.name AS customer_name, o.status
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    LEFT JOIN payments p ON o.order_id = p.order_id
    WHERE p.order_id IS NULL
    ORDER BY o.order_id DESC
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $method   = $_POST['method'];
    $amount   = $_POST['amount'];
    $date     = $_POST['date'];

    $sql = "INSERT INTO payments (order_id, method, amount, date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isds", $order_id, $method, $amount, $date);

    if ($stmt->execute()) {
        // Update order status to Completed when payment is made
        $conn->query("UPDATE orders SET status='Completed' WHERE order_id=$order_id");

        header("Location: payments_list.php?msg=added");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Payment</title>
</head>
<body>
    <h2>Add Payment</h2>
    <a href="payments_list.php">⬅ Back to Payments</a>
    <hr>

    <form method="POST">
        <label>Order:</label>
        <select name="order_id" required>
            <?php while ($o = $orders->fetch_assoc()): ?>
                <option value="<?= $o['order_id'] ?>">
                    Order #<?= $o['order_id'] ?> - <?= htmlspecialchars($o['customer_name']) ?> (<?= $o['status'] ?>)
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Payment Method:</label>
        <select name="method" required>
            <option value="Cash">Cash</option>
            <option value="M-Pesa">M-Pesa</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>
        <br><br>

        <label>Amount:</label>
        <input type="number" step="0.01" name="amount" required>
        <br><br>

        <label>Date:</label>
        <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
        <br><br>

        <button type="submit">Save Payment</button>
    </form>
</body>
</html>
