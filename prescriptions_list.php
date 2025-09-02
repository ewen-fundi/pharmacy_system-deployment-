<?php
session_start();
require_once "config/db_connect.php";

$sql = "SELECT p.prescription_id, c.name AS customer_name, m.name AS medicine_name, p.dosage, p.date
        FROM prescriptions p
        JOIN customers c ON p.customer_id = c.customer_id
        JOIN medicines m ON p.medicine_id = m.medicine_id
        ORDER BY p.date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Prescriptions</title>
<style>
/* Full screen body with background image and dark overlay */
html, body {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #f0f0f0;
    background: url("images/portrait-female-pharmacist-working-drugstore.jpg") no-repeat center center fixed;
    background-size: cover;
    position: relative;
}

/* Dark overlay for readability */
body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-color: rgba(18,18,18,0.7); /* semi-transparent dark layer */
    z-index: -1;
}

/* Heading style */
h2 {
    text-align: center;
    margin: 20px 0;
    font-size: 2em;
    color: #fff;
    text-shadow: 1px 1px 5px #000;
}

/* Container with dark semi-transparent background */
.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background-color: rgba(30, 30, 30, 0.85);
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.7);
}

/* Buttons */
a { text-decoration: none; transition: color 0.3s; }
a:hover { color: #00bfff; }

.btn {
    padding: 6px 12px;
    border-radius: 5px;
    font-weight: bold;
    transition: 0.3s;
    text-decoration: none;
}

.btn-add { background-color: #1E88E5; color: #fff; margin-bottom: 15px; display: inline-block; }
.btn-add:hover { background-color: #1565C0; }

.btn-edit { background-color: #FFC107; color: #000; }
.btn-edit:hover { background-color: #FFA000; }

.btn-delete { background-color: #E53935; color: #fff; }
.btn-delete:hover { background-color: #C62828; }

/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background-color: rgba(44,44,44,0.9);
    border-radius: 5px;
    overflow: hidden;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #444;
}

th { background-color: rgba(51,51,51,0.9); }

tr:hover { background-color: rgba(61,61,61,0.8); cursor: pointer; }

.back-link { display: inline-block; margin-top: 20px; font-size: 1em; color: #1E88E5; }
.back-link:hover { color: #00bfff; }

/* Responsive design */
@media (max-width: 768px) {
    th, td { padding: 8px 10px; }
    .btn { padding: 4px 8px; font-size: 0.9em; }
}
</style>
</head>
<body>
<div class="container">
    <h2>Prescriptions List</h2>

    <a class="btn btn-add" href="add_prescription.php">➕ Add Prescription</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Medicine</th>
            <th>Dosage</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['prescription_id'] ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['medicine_name']) ?></td>
            <td><?= htmlspecialchars($row['dosage']) ?></td>
            <td><?= $row['date'] ?></td>
            <td>
                <a class="btn btn-edit" href="edit_prescription.php?id=<?= $row['prescription_id'] ?>">✏ Edit</a>
                <a class="btn btn-delete" href="delete_prescription.php?id=<?= $row['prescription_id'] ?>" onclick="return confirm('Delete this prescription?');">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a class="back-link" href="dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
