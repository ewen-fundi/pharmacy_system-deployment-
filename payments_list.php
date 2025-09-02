<?php
session_start();
require_once "config/db_connect.php";

// Protect route
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

// Fetch all payments with order + customer info
$sql = "
    SELECT p.payment_id, p.method, p.amount, p.date, 
           o.order_id, o.status, 
           c.name AS customer_name
    FROM payments p
    JOIN orders o ON p.order_id = o.order_id
    JOIN customers c ON o.customer_id = c.customer_id
    ORDER BY p.payment_id DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payments List</title>
</head>
<body>
    <h2>Payments List</h2>
    <a href="dashboard.php">⬅ Back to Dashboard</a> | 
    <a href="add_payment.php">➕ Add Payment</a>
    <hr>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Order</th>
            <th>Customer</th>
            <th>Method</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['payment_id'] ?></td>
            <td>#<?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= $row['method'] ?></td>
            <td><?= $row['amount'] ?></td>
            <td><?= $row['date'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <a href="edit_payment.php?id=<?= $row['payment_id'] ?>">✏ Edit</a> | 
                <a href="delete_payment.php?id=<?= $row['payment_id'] ?>" onclick="return confirm('Are you sure?')">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
