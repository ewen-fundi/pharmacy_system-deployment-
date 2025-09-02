<?php
session_start();
require_once "config/db_connect.php";

// Only Admins can access reports
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login_form.php");
    exit;
}

// Fetch inventory data
$sql = "SELECT m.medicine_id, m.name, m.batch_number, m.expiry_date, m.quantity, m.price, 
               s.name AS supplier_name
        FROM medicines m
        LEFT JOIN suppliers s ON m.supplier_id = s.supplier_id
        ORDER BY m.name ASC";
$result = $conn->query($sql);

$inventory = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Report</title>
    <style>
        /* ===== Reset & Base ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }
        body {
            background: url('assets/bg-pharmacy.jpg') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px;
            transition: background-color 0.4s, color 0.4s;
        }

        /* ===== Container ===== */
        .container {
            width: 90%;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            transition: background 0.4s, color 0.4s;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        /* ===== Table ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #eaf3ff;
        }
        .low-stock {
            background: #fff3cd !important;
        }
        .expired {
            background: #f8d7da !important;
        }

        /* ===== Dark Theme ===== */
        body.dark {
            background: #121212;
        }
        body.dark .container {
            background: rgba(30,30,30,0.95);
            color: #eee;
        }
        body.dark th {
            background: #444;
        }
        body.dark tr:nth-child(even) {
            background: #222;
        }
        body.dark tr:hover {
            background: #333;
        }

        /* ===== Toggle Button ===== */
        .toggle-btn {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border-radius: 20px;
            cursor: pointer;
            float: right;
            margin-bottom: 15px;
            transition: background 0.3s;
        }
        .toggle-btn:hover {
            background: #0056b3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            table, th, td {
                font-size: 14px;
            }
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="toggle-btn" onclick="toggleDarkMode()">🌙 Toggle Dark Mode</span>
        <h2>📦 Inventory Report</h2>
        <p><a href="reports.php">⬅ Back to Reports</a></p>

        <?php if (count($inventory) > 0): ?>
            <table>
                <tr>
                    <th>Medicine</th>
                    <th>Batch No.</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Supplier</th>
                </tr>
                <?php 
                $today = date("Y-m-d");
                foreach ($inventory as $row): 
                    $classes = "";
                    if ($row['quantity'] < 10) $classes = "low-stock";
                    if ($row['expiry_date'] < $today) $classes = "expired";
                ?>
                    <tr class="<?= $classes ?>">
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['batch_number']) ?></td>
                        <td><?= $row['expiry_date'] ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= number_format($row['price'], 2) ?></td>
                        <td><?= htmlspecialchars($row['supplier_name'] ?? 'N/A') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p><small><span style="background:#fff3cd;">Low stock &lt; 10</span> | 
            <span style="background:#f8d7da;">Expired</span></small></p>
        <?php else: ?>
            <p>No medicines found.</p>
        <?php endif; ?>
    </div>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle("dark");
        }
    </script>
</body>
</html>
