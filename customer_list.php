<?php
session_start();
require_once "config/db_connect.php";

// Fetch all customers
$result = $conn->query("SELECT * FROM customers");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customers List</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Customers List</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <p style="color:green;">✅ Customer deleted successfully.</p>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'linked'): ?>
        <p style="color:red;">❌ Cannot delete customer — they have linked orders or prescriptions.</p>
    <?php endif; ?>

    <a href="add_customer.php">➕ Add Customer</a>
    <br><br>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['customer_id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td>
                <a href="edit_customer.php?id=<?= $row['customer_id'] ?>">✏ Edit</a> | 
                <a href="delete_customer.php?id=<?= $row['customer_id'] ?>" onclick="return confirm('Are you sure you want to delete this customer?')">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
