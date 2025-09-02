<?php
session_start();
require_once "config/db_connect.php";

// Only Admins can access reports
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login_form.php");
    exit;
}

// Fetch expiry data (next 60 days or already expired)
$today = date("Y-m-d");
$future = date("Y-m-d", strtotime("+60 days"));

$sql = "SELECT m.medicine_id, m.name, m.batch_number, m.expiry_date, m.quantity, s.name AS supplier_name
        FROM medicines m
        LEFT JOIN suppliers s ON m.supplier_id = s.supplier_id
        WHERE m.expiry_date <= ?
        ORDER BY m.expiry_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $future);
$stmt->execute();
$result = $stmt->get_result();
$medicines = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expiry Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 20px;
            transition: all 0.3s ease;
        }

        :root {
            --bg: #f4f6f9;
            --text: #222;
            --card: #fff;
            --border: #ccc;
            --expired: #f8d7da;
            --expiring: #fff3cd;
        }

        body.dark {
            --bg: #121212;
            --text: #f1f1f1;
            --card: #1e1e1e;
            --border: #444;
            --expired: #8b2f39;
            --expiring: #7a5c19;
        }

        .container {
            background: var(--card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover { text-decoration: underline; }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid var(--border);
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: var(--card);
            position: sticky;
            top: 0;
            z-index: 1;
        }
        tr:hover { background-color: rgba(0,0,0,0.05); }

        .expired { background-color: var(--expired); }
        .expiring { background-color: var(--expiring); }

        .badge {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .badge-expired { background: #dc3545; color: #fff; }
        .badge-expiring { background: #ffc107; color: #222; }
        .badge-ok { background: #28a745; color: #fff; }

        .toggle-btn {
            float: right;
            padding: 6px 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: #007bff;
            color: #fff;
            font-weight: bold;
            transition: background 0.3s;
        }
        .toggle-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <button class="toggle-btn" onclick="toggleTheme()">🌙 Toggle Dark Mode</button>
        <h2>⏳ Expiry Report (Next 60 Days)</h2>
        <p><a href="reports.php">⬅ Back to Reports</a></p>

        <?php if (count($medicines) > 0): ?>
            <table>
                <tr>
                    <th>Medicine</th>
                    <th>Batch No.</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th>Supplier</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($medicines as $row): 
                    $status = "<span class='badge badge-ok'>OK</span>";
                    $rowClass = "";
                    if ($row['expiry_date'] < $today) {
                        $rowClass = "expired";
                        $status = "<span class='badge badge-expired'>Expired</span>";
                    } elseif ($row['expiry_date'] <= $future) {
                        $rowClass = "expiring";
                        $status = "<span class='badge badge-expiring'>Expiring Soon</span>";
                    }
                ?>
                    <tr class="<?= $rowClass ?>">
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['batch_number']) ?></td>
                        <td><?= $row['expiry_date'] ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= htmlspecialchars($row['supplier_name'] ?? 'N/A') ?></td>
                        <td><?= $status ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>✅ No expired or soon-to-expire medicines found.</p>
        <?php endif; ?>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle("dark");
        }
    </script>
</body>
</html>
