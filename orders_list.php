<?php
session_start();
require_once "config/db_connect.php";

// Fetch all orders with customer and payment info
$sql = "SELECT o.order_id, c.name AS customer_name, o.date, o.status, 
               p.method AS payment_method, p.amount AS payment_amount
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        LEFT JOIN payments p ON o.order_id = p.order_id
        ORDER BY o.order_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orders List</title>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #f0f0f0;
        background: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
        background-size: cover;
        backdrop-filter: brightness(0.5);
    }

    h2 {
        text-align: center;
        margin: 20px 0;
        font-size: 2em;
        color: #fff;
        text-shadow: 1px 1px 5px #000;
    }

    a {
        text-decoration: none;
        color: #1E90FF;
        transition: color 0.3s;
    }
    a:hover {
        color: #00bfff;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background-color: rgba(20, 20, 20, 0.85);
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.7);
    }

    .alert {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-weight: bold;
        text-align: center;
    }
    .alert-success { background-color: #28a745; color: #fff; }
    .alert-error { background-color: #dc3545; color: #fff; }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background-color: rgba(0,0,0,0.7);
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #444;
    }

    th {
        background-color: rgba(50, 50, 50, 0.9);
    }

    tr:hover {
        background-color: rgba(70, 70, 70, 0.8);
        cursor: pointer;
    }

    .btn {
        padding: 6px 12px;
        border-radius: 5px;
        font-weight: bold;
        transition: 0.3s;
        text-decoration: none;
    }

    .btn-add {
        background-color: #1E90FF;
        color: #fff;
        margin-bottom: 15px;
        display: inline-block;
    }
    .btn-add:hover { background-color: #00bfff; }

    .btn-view {
        background-color: #17a2b8;
        color: #fff;
    }
    .btn-view:hover { background-color: #138496; }

    .btn-edit {
        background-color: #ffc107;
        color: #000;
    }
    .btn-edit:hover { background-color: #e0a800; }

    .btn-delete {
        background-color: #dc3545;
        color: #fff;
    }
    .btn-delete:hover { background-color: #c82333; }

    .back-link {
        display: inline-block;
        margin-top: 20px;
        font-size: 1em;
    }

    @media (max-width: 768px) {
        th, td {
            padding: 8px 10px;
        }
        .btn {
            padding: 4px 8px;
            font-size: 0.9em;
        }
    }
</style>
</head>
<body>
    <div class="container">
        <h2>Orders List</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success">✅ Order deleted successfully.</div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'linked'): ?>
            <div class="alert alert-error">❌ Cannot delete order — it has linked order details or payments.</div>
        <?php endif; ?>

        <a class="btn btn-add" href="add_order.php">➕ Add Order</a>

        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Payment Amount</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['order_id'] ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                <td><?= $row['payment_amount'] ?></td>
                <td>
                    <a class="btn btn-view" href="view_order.php?id=<?= $row['order_id'] ?>">👁 View</a>
                    <a class="btn btn-edit" href="edit_order.php?id=<?= $row['order_id'] ?>">✏ Edit</a>
                    <a class="btn btn-delete" href="delete_order.php?id=<?= $row['order_id'] ?>" onclick="return confirm('Are you sure you want to delete this order?')">🗑 Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <a class="back-link" href="dashboard.php">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
