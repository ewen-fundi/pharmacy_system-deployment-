<?php
session_start();
require_once "config/db_connect.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    $sql = "INSERT INTO customers (name, contact, address) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $contact, $address);

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
    <title>Add Customer</title>
    <style>
        /* ===== Base Styles ===== */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background 0.5s ease, color 0.5s ease;
            background: url('https://picsum.photos/1600/900?blur=5') no-repeat center center/cover;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 30px;
            width: 350px;
            text-align: center;
            color: #fff;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: none;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            transition: all 0.3s ease-in-out;
        }

        input:focus {
            box-shadow: 0 0 10px #4cafef;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 10px;
            background: #4cafef;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        button:hover {
            transform: scale(1.05);
            background: #3a9ad9;
        }

        a {
            display: block;
            margin-top: 15px;
            color: #eee;
            text-decoration: none;
            transition: color 0.3s;
        }

        a:hover {
            color: #4cafef;
        }

        /* ===== Toggle Button ===== */
        .toggle-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #4cafef;
            border: none;
            padding: 10px 15px;
            border-radius: 50px;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
        }

        .toggle-btn:hover {
            background: #3a9ad9;
        }

        /* ===== Dark Mode ===== */
        body.dark {
            background: #121212;
            color: #fff;
        }

        body.dark .container {
            background: rgba(40, 40, 40, 0.9);
        }

        body.dark input {
            background: #222;
            color: #fff;
        }

        body.dark a {
            color: #bbb;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>
    <button class="toggle-btn" onclick="toggleTheme()">🌙</button>

    <div class="container">
        <h2>Add Customer</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Enter Customer Name" required>
            <input type="text" name="contact" placeholder="Enter Contact">
            <input type="text" name="address" placeholder="Enter Address">
            <button type="submit">Save</button>
        </form>
        <a href="customers_list.php">⬅ Back to Customers List</a>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle("dark");
            const btn = document.querySelector(".toggle-btn");
            btn.textContent = document.body.classList.contains("dark") ? "☀️" : "🌙";
        }
    </script>
</body>
</html>
