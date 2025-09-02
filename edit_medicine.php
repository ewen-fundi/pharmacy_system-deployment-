<?php
session_start();
require_once "config/db_connect.php";

$id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT * FROM medicines WHERE medicine_id = $id");
$medicine = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $batch_number = $_POST['batch_number'];
    $expiry_date = $_POST['expiry_date'];
    $quantity = (int) $_POST['quantity'];
    $price = (float) $_POST['price'];
    $supplier_id = (int) $_POST['supplier_id'];

    $sql = "UPDATE medicines 
            SET name=?, batch_number=?, expiry_date=?, quantity=?, price=?, supplier_id=? 
            WHERE medicine_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiddi", $name, $batch_number, $expiry_date, $quantity, $price, $supplier_id, $id);

    if ($stmt->execute()) {
        header("Location: medicines_list.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medicine</title>
    <style>
        :root {
            --bg-light: #f4f6f9;
            --text-light: #222;
            --card-light: #fff;
            --bg-dark: #181a1b;
            --text-dark: #f4f6f9;
            --card-dark: #242627;
            --primary: #4CAF50;
            --primary-hover: #45a049;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-light);
            margin: 0;
            padding: 0;
            transition: all 0.3s ease;
        }

        body.dark {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            padding: 20px;
            border-radius: 12px;
            background-color: var(--card-light);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        body.dark .container {
            background-color: var(--card-dark);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 12px 0 6px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: 0.2s;
        }

        body.dark input {
            background-color: #333;
            border: 1px solid #555;
            color: var(--text-dark);
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 18px;
            border: none;
            border-radius: 10px;
            background-color: var(--primary);
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: var(--primary-hover);
        }

        .toggle-btn {
            position: fixed;
            top: 15px;
            right: 15px;
            padding: 10px 15px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            background: #444;
            color: #fff;
            font-size: 14px;
            transition: background 0.3s;
        }

        .toggle-btn:hover {
            background: #222;
        }
    </style>
</head>
<body>
    <button class="toggle-btn" onclick="toggleTheme()">🌙 Dark Mode</button>
    <div class="container">
        <h2>Edit Medicine</h2>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($medicine['name']) ?>" required>

            <label>Batch Number:</label>
            <input type="text" name="batch_number" value="<?= htmlspecialchars($medicine['batch_number']) ?>">

            <label>Expiry Date:</label>
            <input type="date" name="expiry_date" value="<?= $medicine['expiry_date'] ?>">

            <label>Quantity:</label>
            <input type="number" name="quantity" value="<?= $medicine['quantity'] ?>" required>

            <label>Price:</label>
            <input type="number" step="0.01" name="price" value="<?= $medicine['price'] ?>" required>

            <label>Supplier ID:</label>
            <input type="number" name="supplier_id" value="<?= $medicine['supplier_id'] ?>">

            <button type="submit">Update Medicine</button>
        </form>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark');
            let btn = document.querySelector(".toggle-btn");
            if (document.body.classList.contains("dark")) {
                btn.textContent = "☀️ Light Mode";
            } else {
                btn.textContent = "🌙 Dark Mode";
            }
        }
    </script>
</body>
</html>
