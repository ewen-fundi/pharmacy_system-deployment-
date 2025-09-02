<?php
session_start();
require_once "config/db_connect.php";

// Ensure only Cashier/Admin can access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Cashier','Admin'])) {
    header("Location: dashboard.php");
    exit;
}

$order_id = $_GET['order_id'] ?? 0;

// Fetch order details
$sql = "SELECT o.order_id, o.date, c.name AS customer_name, c.contact,
               p.method AS payment_method, p.amount AS payment_amount
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        LEFT JOIN payments p ON o.order_id = p.order_id
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "Receipt not found.";
    exit;
}

// Fetch order items
$items_sql = "SELECT m.name AS medicine_name, od.quantity, od.price
              FROM order_details od
              JOIN medicines m ON od.medicine_id = m.medicine_id
              WHERE od.order_id = ?";
$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?= $order['order_id'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; text-align: right; }
        .print-btn { margin-top: 20px; display: block; text-align: center; }
    </style>
</head>
<body>
    <h2>Pharmacy Receipt</h2>
    <p><strong>Receipt No:</strong> <?= $order['order_id'] ?></p>
    <p><strong>Date:</strong> <?= $order['date'] ?></p>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['contact']) ?>)</p>
    <hr>

    <table>
        <tr>
            <th>Medicine</th>
            <th>Quantity</th>
            <th>Price (per unit)</th>
            <th>Total</th>
        </tr>
        <?php 
        $grand_total = 0;
        while ($item = $items->fetch_assoc()): 
            $line_total = $item['quantity'] * $item['price'];
            $grand_total += $line_total;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['medicine_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['price'], 2) ?></td>
            <td><?= number_format($line_total, 2) ?></td>
        </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="3" class="total">Grand Total:</td>
            <td><strong><?= number_format($grand_total, 2) ?></strong></td>
        </tr>
    </table>

    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
    <p><strong>Amount Paid:</strong> <?= number_format($order['payment_amount'], 2) ?></p>

    <div class="print-btn">
        <button onclick="window.print()">🖨 Print Receipt</button>
    </div>
</body>
</html>
