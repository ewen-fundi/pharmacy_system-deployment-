<?php
session_start();
require_once "config/db_connect.php";

// Only allow Admins to access reports
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login_form.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports Dashboard</title>
    <style>
        /* ===== Reset & Base ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url("images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg") no-repeat center center/cover;
            background-size: cover;
            color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* ===== Dark Overlay ===== */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 10, 20, 0.8);
            z-index: -1;
        }

        /* ===== Card Container ===== */
        .card {
            background: rgba(30, 30, 40, 0.9);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
            width: 400px;
            text-align: center;
            backdrop-filter: blur(8px);
        }

        h2 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: #00d1b2;
        }

        p {
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            color: #ccc;
        }

        hr {
            border: 0;
            height: 1px;
            background: #444;
            margin: 1rem 0;
        }

        /* ===== Report Links ===== */
        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            margin: 0.8rem 0;
        }

        ul li a {
            display: block;
            padding: 12px;
            border-radius: 12px;
            background: #1f2937;
            color: #f5f5f5;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }

        ul li a:hover {
            background: #00d1b2;
            color: #111;
            transform: translateY(-3px);
        }

        /* ===== Back Button ===== */
        .back-btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 10px 20px;
            border-radius: 8px;
            background: #444;
            color: #f5f5f5;
            text-decoration: none;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #00d1b2;
            color: #111;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>📊 Reports Dashboard</h2>
        <p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)</p>
        <hr>

        <ul>
            <li><a href="report_sales.php">💰 Sales Report</a></li>
            <li><a href="report_inventory.php">📦 Inventory Report</a></li>
            <li><a href="report_expiry.php">⏳ Expiry Alerts</a></li>
            <li><a href="report_customers.php">👥 Customer Report</a></li>
        </ul>

        <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
