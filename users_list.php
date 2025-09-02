<?php
session_start();
require_once "config/db_connect.php";

// Restrict access to Admins only
if ($_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php");
    exit;
}

$result = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users</title>
<style>
/* Full screen dark background with image */
html, body {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #f0f0f0;
    background: url("images/medication-dark-environment.jpg") no-repeat center center fixed;
    background-size: cover;
    background-color: #121212; /* fallback color */
}

/* Dark overlay for readability */
body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-color: rgba(18,18,18,0.7); /* semi-transparent dark overlay */
    z-index: -1;
}

/* Page heading */
h2 {
    text-align: center;
    margin: 20px 0;
    font-size: 2em;
    color: #fff;
    text-shadow: 1px 1px 5px #000;
}

/* Main container */
.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background-color: rgba(30, 30, 30, 0.85); /* semi-transparent dark container */
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.7);
}

/* Links and buttons */
a {
    text-decoration: none;
    transition: color 0.3s;
}
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
    background-color: rgba(44,44,44,0.9); /* semi-transparent dark table */
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

/* Responsive */
@media (max-width: 768px) {
    th, td { padding: 8px 10px; }
    .btn { padding: 4px 8px; font-size: 0.9em; }
}
</style>
</head>
<body>
<div class="container">
    <h2>Users Management</h2>

    <a class="btn btn-add" href="add_user.php">➕ Add New User</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
                <a class="btn btn-edit" href="edit_user.php?id=<?= $row['user_id'] ?>">✏ Edit</a>
                <a class="btn btn-delete" href="delete_user.php?id=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure?')">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a class="back-link" href="dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
