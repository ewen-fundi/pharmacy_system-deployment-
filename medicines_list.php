<?php
session_start();
require_once "config/db_connect.php";

// Restrict access to Admin & Pharmacist only
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Pharmacist'])) {
    header("Location: login_form.php");
    exit;
}

$result = $conn->query("SELECT * FROM medicines");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medicines List (Dark Mode)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #121212 url('images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            color: #f1f1f1;
        }
        .overlay {
            background: rgba(18, 18, 18, 0.92);
            min-height: 100vh;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #00bcd4;
            margin-bottom: 20px;
        }
        .links {
            text-align: center;
            margin-bottom: 20px;
        }
        .links a {
            text-decoration: none;
            margin: 0 10px;
            padding: 8px 15px;
            background: #00bcd4;
            color: black;
            font-weight: bold;
            border-radius: 6px;
            transition: 0.3s;
        }
        .links a:hover {
            background: #008c9e;
            color: white;
        }
        .alert {
            max-width: 800px;
            margin: 10px auto;
            padding: 12px;
            border-radius: 5px;
            font-size: 14px;
        }
        .success { background: #1e4620; color: #a5d6a7; border: 1px solid #388e3c; }
        .error { background: #4a1c1c; color: #ef9a9a; border: 1px solid #e53935; }
        .warning { background: #4e4320; color: #ffe082; border: 1px solid #fbc02d; }
        
        .search-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-box input {
            width: 300px;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #555;
            background: #1e1e1e;
            color: #f1f1f1;
        }
        table {
            width: 95%;
            margin: 0 auto;
            border-collapse: collapse;
            background: #1e1e1e;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        th, td {
            border: 1px solid #333;
            padding: 12px;
            text-align: center;
            color: #ddd;
        }
        th {
            background: #00bcd4;
            color: black;
            font-weight: bold;
        }
        tr:hover {
            background: #2c2c2c;
        }
        .actions a {
            margin: 0 5px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }
        .edit-btn { background: #4caf50; color: black; }
        .edit-btn:hover { background: #388e3c; color: white; }
        .delete-btn { background: #f44336; color: black; }
        .delete-btn:hover { background: #c62828; color: white; }
    </style>
    <script>
        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("table tbody tr");
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>
</head>
<body>
<div class="overlay">
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert success">✅ Medicine deleted successfully.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'linked'): ?>
        <div class="alert error">❌ Cannot delete — medicine is linked to existing orders.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'failed'): ?>
        <div class="alert error">❌ Deletion failed. Please try again.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
        <div class="alert warning">⚠ Invalid medicine ID.</div>
    <?php endif; ?>

    <h2>Medicines Inventory</h2>
    
    <div class="links">
        <a href="dashboard.php">← Back to Dashboard</a>
        <a href="add_medicine.php">➕ Add New Medicine</a>
    </div>

    <div class="search-box">
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="🔍 Search medicine...">
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Batch</th>
            <th>Expiry Date</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Supplier ID</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['medicine_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['batch_number']) ?></td>
                <td><?= $row['expiry_date'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= $row['supplier_id'] ?></td>
                <td class="actions">
                    <a href="edit_medicine.php?id=<?= $row['medicine_id'] ?>" class="edit-btn">✏ Edit</a>
                    <a href="delete_medicine.php?id=<?= $row['medicine_id'] ?>" 
                       onclick="return confirm('Delete this medicine?')" 
                       class="delete-btn">🗑 Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
