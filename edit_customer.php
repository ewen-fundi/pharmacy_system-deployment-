<?php
session_start();
require_once "config/db_connect.php";

// Get customer ID from URL
$id = $_GET['id'] ?? 0;

// Fetch customer data
$result = $conn->query("SELECT * FROM customers WHERE customer_id = $id");
$customer = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    // Update customer
    $sql = "UPDATE customers SET name=?, contact=?, address=? WHERE customer_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $contact, $address, $id);

    if ($stmt->execute()) {
        header("Location: customers_list.php");
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
    <title>Edit Customer</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/medication-dark-environment.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.75);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background: rgba(25, 25, 25, 0.85);
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        h2 {
            margin-bottom: 20px;
            color: #00d4ff;
        }

        label {
            display: block;
            text-align: left;
            margin: 12px 0 6px;
            font-weight: bold;
            color: #ddd;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #222;
            color: #fff;
            font-size: 16px;
            margin-bottom: 15px;
            outline: none;
            transition: 0.3s;
        }

        input[type="text"]:focus {
            background: #333;
            border: 1px solid #00d4ff;
            box-shadow: 0 0 8px #00d4ff;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #00d4ff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            color: #000;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: #0099cc;
            transform: scale(1.05);
        }

        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #00d4ff;
            font-weight: bold;
            transition: 0.3s;
        }

        a:hover {
            color: #fff;
            text-shadow: 0 0 6px #00d4ff;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="form-container">
            <h2>✏ Edit Customer</h2>
            <form method="POST">
                <label for="name">Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>

                <label for="contact">Contact</label>
                <input type="text" name="contact" value="<?= htmlspecialchars($customer['contact']) ?>">

                <label for="address">Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($customer['address']) ?>">

                <button type="submit">💾 Update</button>
            </form>
            <a href="customers_list.php">⬅ Back to Customers List</a>
        </div>
    </div>
</body>
</html>
