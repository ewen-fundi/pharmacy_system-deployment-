<?php
session_start();
require_once "config/db_connect.php"; // make sure this file has your DB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $contact = trim($_POST["contact"]);
    $address = trim($_POST["address"]);

    if (!empty($name)) {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO suppliers (name, contact, address) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $contact, $address);

        if ($stmt->execute()) {
            // Redirect back to suppliers list after saving
            header("Location: suppliers_list.php");
            exit;
        } else {
            echo "<p style='color:red;text-align:center;'>Error: Could not save supplier. " . $conn->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;text-align:center;'>Supplier name is required!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: 
                linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url("images/portrait-female-pharmacist-working-drugstore.jpg")
                no-repeat center center / cover;
            color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: rgba(20, 20, 20, 0.9);
            padding: 30px;
            border-radius: 15px;
            width: 350px;
            box-shadow: 0px 8px 20px rgba(0,0,0,0.7);
            text-align: center;
            backdrop-filter: blur(6px);
        }
        h2 {
            margin-bottom: 20px;
            color: #00d4ff;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            background: #2a2a2a;
            color: #f4f4f4;
        }
        input:focus {
            outline: none;
            border: 2px solid #00d4ff;
            background: #333;
        }
        button {
            background: #00d4ff;
            color: #111;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            width: 100%;
            margin-top: 15px;
            transition: 0.3s;
        }
        button:hover {
            background: #00aacc;
            transform: scale(1.05);
        }
        a {
            display: inline-block;
            margin-top: 15px;
            color: #bbb;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }
        a:hover {
            color: #00d4ff;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add Supplier</h2>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Supplier Name" required>
            <input type="text" name="contact" placeholder="Contact">
            <input type="text" name="address" placeholder="Address">
            <button type="submit">Save</button>
        </form>
        <a href="suppliers_list.php">⬅ Back to Suppliers List</a>
    </div>
</body>
</html>

