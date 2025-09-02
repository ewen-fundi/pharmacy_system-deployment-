<?php
session_start();
require_once "config/db_connect.php";

// Get supplier ID from URL
$id = $_GET['id'] ?? 0;

// Fetch supplier data
$result = $conn->query("SELECT * FROM suppliers WHERE supplier_id = $id");
$supplier = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    // Update supplier
    $sql = "UPDATE suppliers SET name=?, contact=?, address=? WHERE supplier_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $contact, $address, $id);

    if ($stmt->execute()) {
        header("Location: suppliers_list.php");
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
    <title>Edit Supplier</title>
    <style>
        /* Background Image with Dark Overlay */
        body {
            background: url("images/portrait-female-pharmacist-working-drugstore.jpg") no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            font-family: Arial, sans-serif;
            color: #f1f1f1;
        }

        /* Dark overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: -1;
        }

        .container {
            max-width: 450px;
            margin: 70px auto;
            background: rgba(20, 20, 20, 0.9);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.6);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #00d4ff;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 6px;
            font-weight: bold;
            color: #ddd;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: none;
            border-radius: 8px;
            background: #2a2a2a;
            color: #fff;
            outline: none;
        }

        input[type="text"]:focus {
            border: 1px solid #00d4ff;
            background: #333;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #00d4ff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            color: #000;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #00aacc;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #00d4ff;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Supplier</h2>
        <form method="POST">
            <label for="name">Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($supplier['name']) ?>" required>

            <label for="contact">Contact</label>
            <input type="text" name="contact" value="<?= htmlspecialchars($supplier['contact']) ?>">

            <label for="address">Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($supplier['address']) ?>">

            <button type="submit">Update</button>
        </form>
        <a href="suppliers_list.php">⬅ Back to Suppliers List</a>
    </div>
</body>
</html>
