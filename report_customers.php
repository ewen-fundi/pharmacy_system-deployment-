<?php
session_start();
require_once "config/db_connect.php";

// Only Admins can access reports
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login_form.php");
    exit;
}

// Fetch customer report data
$sql = "SELECT c.customer_id, c.name, c.contact, c.address,
               COUNT(o.order_id) AS total_orders,
               COALESCE(SUM(p.amount), 0) AS total_spent
        FROM customers c
        LEFT JOIN orders o ON c.customer_id = o.customer_id
        LEFT JOIN payments p ON o.order_id = p.order_id
        GROUP BY c.customer_id, c.name, c.contact, c.address
        ORDER BY total_spent DESC";

$result = $conn->query($sql);
$customers = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #2c3e50;
        }
        .back-link {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }
        .summary-cards {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .card {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 {
            margin: 5px 0;
            color: #34495e;
        }
        .search-box {
            margin: 15px 0;
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background: #3498db;
            color: white;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        tr:hover {
            background: #eaf2f8;
        }
        .download-btn {
            display: inline-block;
            margin: 15px 0;
            padding: 10px 15px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .download-btn:hover {
            background: #219150;
        }
    </style>
    <script>
        function searchCustomer() {
            let input = document.getElementById("search").value.toLowerCase();
            let rows = document.querySelectorAll("table tbody tr");
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
            });
        }
    </script>
</head>
<body>
    <h2>👥 Customer Report</h2>
    <p><a href="reports.php" class="back-link">⬅ Back to Reports</a></p>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card">
            <h3>Total Customers</h3>
            <p><?= count($customers) ?></p>
        </div>
        <div class="card">
            <h3>Top Spender</h3>
            <p><?= $customers ? htmlspecialchars($customers[0]['name']) . " (Ksh " . number_format($customers[0]['total_spent'], 2) . ")" : "N/A" ?></p>
        </div>
        <div class="card">
            <h3>Total Revenue</h3>
            <p>Ksh <?= number_format(array_sum(array_column($customers, 'total_spent')), 2) ?></p>
        </div>
    </div>

    <!-- Search & Download -->
    <input type="text" id="search" class="search-box" onkeyup="searchCustomer()" placeholder="🔍 Search customer...">
    <a href="export_customers.php" class="download-btn">⬇ Download CSV</a>

    <?php if (count($customers) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Total Orders</th>
                    <th>Total Spent (Ksh)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= $row['total_orders'] ?></td>
                        <td><?= number_format($row['total_spent'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>ℹ No customer data available.</p>
    <?php endif; ?>
</body>
</html>
