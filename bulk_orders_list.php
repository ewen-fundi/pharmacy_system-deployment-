<?php
session_start();
require_once "config/db_connect.php";

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

// Fetch bulk orders
$sql = "SELECT * FROM bulk_orders ORDER BY bulk_order_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Orders</title>
    <style>
        /* ===== Global Dark Mode Styling ===== */
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f0f0f0;
            background: #121212 url("../images/medication-dark-environment.jpg") no-repeat center center fixed;
            background-size: cover;
        }

        /* Overlay for better readability */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.65);
            z-index: -1;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
            color: #00bfff;
        }

        a {
            text-decoration: none;
            color: #00bfff;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #1e90ff;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
            background: rgba(25, 25, 25, 0.9);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.7);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: #1e1e1e;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background: #00bfff;
            color: black;
        }

        tr:nth-child(even) {
            background: #2a2a2a;
        }

        tr:hover {
            background: #333333;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 2px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-view { background: #00bfff; color: black; }
        .btn-edit { background: #ffa500; color: black; }
        .btn-delete { background: #ff4444; color: white; }
        .btn:hover { opacity: 0.8; }

        .top-link {
            display: inline-block;
            margin: 10px 0;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📦 Bulk Orders</h2>
        <a href="add_bulk_order.php" class="top-link">➕ Add New Bulk Order</a>
        <table>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Institution</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['bulk_order_id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['institution']) ?></td>
                    <td><?= $row['order_date'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a href="view_bulk_order.php?id=<?= $row['bulk_order_id'] ?>" class="btn btn-view">👁 View</a>
                        <a href="edit_bulk_order.php?id=<?= $row['bulk_order_id'] ?>" class="btn btn-edit">✏ Edit</a>
                        <a href="delete_bulk_order.php?id=<?= $row['bulk_order_id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this bulk order?')">🗑 Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
