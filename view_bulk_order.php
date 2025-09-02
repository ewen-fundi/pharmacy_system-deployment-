<?php
session_start();
require_once "config/db_connect.php";

$order_id = $_GET['id'] ?? 0;

// Fetch order main info
$order_sql = "SELECT o.order_id, o.date, o.status,
                     c.name AS customer_name, c.contact, c.address,
                     p.method AS payment_method, p.amount AS payment_amount
              FROM orders o
              JOIN customers c ON o.customer_id = c.customer_id
              LEFT JOIN payments p ON o.order_id = p.order_id
              WHERE o.order_id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit;
}

// Fetch order details (medicines)
$details_sql = "SELECT m.name AS medicine_name, od.quantity, od.price
                FROM order_details od
                JOIN medicines m ON od.medicine_id = m.medicine_id
                WHERE od.order_id = ?";
$stmt = $conn->prepare($details_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$details_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Order #<?= $order['order_id'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background: url("images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg") no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px;
            position: relative;
            color: #f1f1f1;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7); /* dark overlay */
            z-index: -1;
        }
        .card {
            background: rgba(20, 20, 20, 0.75);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 30px 40px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
            animation: fadeIn 0.8s ease-in-out;
        }
        h2, h3 {
            color: #fff;
            margin-bottom: 15px;
        }
        p {
            margin: 5px 0;
            color: #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: rgba(30, 30, 30, 0.8);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background: #00bcd4;
            color: #fff;
        }
        tr:nth-child(even) {
            background: rgba(255,255,255,0.05);
        }
        tr:hover {
            background: rgba(0, 188, 212, 0.15);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #00bcd4, #008ba3);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            background: linear-gradient(135deg, #008ba3, #00bcd4);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,188,212,0.3);
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.95);}
            to {opacity: 1; transform: scale(1);}
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>📦 Order #<?= $order['order_id'] ?> Details</h2>
        <p><strong>Date:</strong> <?= $order['date'] ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?><br>
           <strong>Contact:</strong> <?= htmlspecialchars($order['contact']) ?><br>
           <strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>

        <h3>💊 Medicines</h3>
        <table>
            <tr>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Price (Each)</th>
                <th>Total</th>
            </tr>
            <?php 
            $grand_total = 0;
            while ($row = $details_result->fetch_assoc()): 
                $total = $row['quantity'] * $row['price'];
                $grand_total += $total;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['medicine_name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= number_format($row['price'], 2) ?></td>
                <td><?= number_format($total, 2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <th colspan="3" style="text-align:right;">Grand Total:</th>
                <th><?= number_format($grand_total, 2) ?></th>
            </tr>
        </table>

        <h3>💳 Payment</h3>
        <p><strong>Method:</strong> <?= $order['payment_method'] ?: "Not Paid" ?></p>
        <p><strong>Amount:</strong> <?= $order['payment_amount'] ?: "0.00" ?></p>

        <a href="orders_list.php" class="back-link">⬅ Back to Orders List</a>
    </div>
</body>
</html>
