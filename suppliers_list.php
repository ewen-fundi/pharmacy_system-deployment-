<?php
session_start();
require_once "config/db_connect.php";

// Fetch all suppliers
$result = $conn->query("SELECT * FROM suppliers ORDER BY supplier_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Suppliers List</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body {
        background: url('images/portrait-female-pharmacist-working-drugstore.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #eee;
        min-height: 100vh;
        padding: 20px;
        position: relative;
        transition: color 0.3s;
    }

    /* Dark overlay */
    #overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(18,18,18,0.7); /* dark semi-transparent */
        z-index: -1;
        transition: background-color 0.3s;
    }

    h1 { margin-bottom: 20px; text-align: center; }

    a { color: #bb86fc; text-decoration: none; transition: color 0.3s; }
    a:hover { color: #fff; }

    .toggle-btn {
        background: #bb86fc;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: bold;
        color: #121212;
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10;
    }

    .message {
        padding: 12px 20px;
        margin-bottom: 15px;
        border-radius: 12px;
        font-weight: 500;
        background-color: rgba(0,0,0,0.6);
    }
    .success { border-left: 6px solid #4CAF50; }
    .error { border-left: 6px solid #f44336; }

    .table-container { overflow-x: auto; }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        border-radius: 8px;
        overflow: hidden;
        animation: fadeIn 0.8s ease;
        background-color: rgba(0,0,0,0.6);
    }
    th, td { padding: 15px; text-align: left; transition: all 0.2s ease; }
    th {
        background-color: rgba(31,31,31,0.9);
        color: #bb86fc;
        font-size: 16px;
        position: sticky;
        top: 0;
    }
    tr:nth-child(even) { background-color: rgba(26,26,26,0.7); }
    tr:hover { background-color: rgba(42,42,42,0.7); }

    .btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: transform 0.2s, background 0.3s;
    }
    .btn-add { background-color: #bb86fc; color: #121212; margin-bottom: 20px; }
    .btn-add:hover { background-color: #9a55ff; transform: scale(1.05); }
    .btn-edit { background-color: #03dac6; color: #121212; }
    .btn-edit:hover { background-color: #00bfa5; transform: scale(1.05); }
    .btn-delete { background-color: #cf6679; color: #fff; }
    .btn-delete:hover { background-color: #b00020; transform: scale(1.05); }

    @keyframes fadeIn { 0% {opacity:0} 100% {opacity:1} }

    @media (max-width: 600px) {
        th, td { padding: 10px; font-size: 14px; }
        .btn { font-size: 12px; padding: 5px 8px; }
    }
</style>
</head>
<body data-mode="dark">
<div id="overlay"></div>

<button class="toggle-btn" onclick="toggleMode()">🌙 Toggle Dark/Light Mode</button>

<h1>Suppliers List</h1>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <div class="message success">✅ Supplier deleted successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] == 'linked'): ?>
    <div class="message error">❌ Cannot delete — supplier is linked to existing medicines.</div>
<?php endif; ?>

<a class="btn btn-add" href="add_supplier.php">➕ Add New Supplier</a>

<div class="table-container">
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
            <td><?= $row['supplier_id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td>
                <a class="btn btn-edit" href="edit_supplier.php?id=<?= $row['supplier_id'] ?>">✏ Edit</a>
                <a class="btn btn-delete" href="delete_supplier.php?id=<?= $row['supplier_id'] ?>" onclick="return confirm('Are you sure you want to delete this supplier?')">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
function toggleMode() {
    const body = document.body;
    const overlay = document.getElementById('overlay');

    if (body.dataset.mode === 'light') {
        overlay.style.backgroundColor = 'rgba(18,18,18,0.7)'; // dark mode overlay
        body.style.color = '#eee';
        body.dataset.mode = 'dark';
    } else {
        overlay.style.backgroundColor = 'rgba(255,255,255,0.3)'; // light mode overlay
        body.style.color = '#121212';
        body.dataset.mode = 'light';
    }
}
</script>

</body>
</html>
