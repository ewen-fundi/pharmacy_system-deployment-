<?php
session_start();
require_once "config/db_connect.php";

// Only Admins can access reports
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login_form.php");
    exit;
}

// Fetch sales data
$sql = "SELECT o.order_id, c.name AS customer_name, o.date, o.status, 
               p.method AS payment_method, p.amount AS payment_amount
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        LEFT JOIN payments p ON o.order_id = p.order_id
        ORDER BY o.date DESC";
$result = $conn->query($sql);

// Calculate total sales
$total_sales = 0;
if ($result && $result->num_rows > 0) {
    $sales_data = $result->fetch_all(MYSQLI_ASSOC);
    foreach ($sales_data as $row) {
        $total_sales += $row['payment_amount'] ?? 0;
    }
} else {
    $sales_data = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <style>
        /* ====== Dark Mode Theme ====== */
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #f5f5f5;
            text-align: center;
            margin-bottom: 20px;
        }

        a {
            color: #00bcd4;
            text-decoration: none;
            font-size: 14px;
        }
        a:hover {
            text-decoration: underline;
        }

        /* ====== Table Styling ====== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 12px rgba(0,0,0,0.6);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #1f1f1f;
            color: #00e676;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        tr {
            transition: background 0.2s ease;
        }
        tr:nth-child(even) {
            background-color: #1a1a1a;
        }
        tr:nth-child(odd) {
            background-color: #161616;
        }
        tr:hover {
            background-color: #2c2c2c;
        }

        td {
            font-size: 14px;
        }

        /* Highlight status */
        .status-Pending { color: #ff9800; font-weight: bold; }
        .status-Processing { color: #03a9f4; font-weight: bold; }
        .status-Delivered { color: #4caf50; font-weight: bold; }
        .status-Paid { color: #8bc34a; font-weight: bold; }

        /* Total Row */
        .total {
            font-weight: bold;
            background-color: #212121;
        }
        .total td {
            font-size: 15px;
            color: #00e676;
        }

        /* Card container */
        .card {
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>💰 Sales Report</h2>
        <p><a href="reports.php" class="back-link">⬅ Back to Reports</a></p>

        <?php if (count($sales_data) > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Payment Amount</th>
                </tr>
                <?php foreach ($sales_data as $row): ?>
                    <tr>
                        <td><?= $row['order_id'] ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= $row['date'] ?></td>
                        <td class="status-<?= $row['status'] ?>"><?= $row['status'] ?></td>
                        <td><?= $row['payment_method'] ?? 'N/A' ?></td>
                        <td>$<?= number_format($row['payment_amount'] ?? 0, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total">
                    <td colspan="5" align="right">TOTAL SALES:</td>
                    <td>$<?= number_format($total_sales, 2) ?></td>
                </tr>
            </table>
        <?php else: ?>
            <p>No sales records found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
