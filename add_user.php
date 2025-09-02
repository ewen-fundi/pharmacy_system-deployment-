<?php
session_start();
require_once "config/db_connect.php"; // ✅ fixed parse error

// Allow only Admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "Access denied. Only Admins can access this page.";
    exit;
}

if ($_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $role  = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, role, password_hash) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $role, $password);

    if ($stmt->execute()) {
        header("Location: users_list.php");
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
    <title>Add User</title>
    <style>
        /* Background & Dark Mode */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
             background: url("images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg") no-repeat center center/cover;
            background-size: cover;
            color: #f0f0f0;
        }

        /* Overlay for semi-dark look */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.65);
            z-index: -1;
        }

        /* Center container */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Card */
        .card {
            background: rgba(30,30,30,0.9);
            padding: 25px 35px;
            border-radius: 12px;
            width: 380px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.5);
        }

        .card h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #4cafef;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: none;
            outline: none;
            background: #222;
            color: #f0f0f0;
        }

        input:focus, select:focus {
            border: 1px solid #4cafef;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #4cafef;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #3b8bcc;
        }

        a {
            display: block;
            margin-top: 15px;
            text-align: center;
            color: #bbb;
            text-decoration: none;
        }

        a:hover {
            color: #4cafef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Add New User</h2>
            <form method="POST">
                <label>Name:</label>
                <input type="text" name="name" required>

                <label>Email:</label>
                <input type="email" name="email" required>

                <label>Role:</label>
                <select name="role" required>
                    <option value="Admin">Admin</option>
                    <option value="Pharmacist">Pharmacist</option>
                    <option value="Cashier">Cashier</option>
                    <option value="Procurement">Procurement</option>
                    <option value="Customer">Customer</option>
                </select>

                <label>Password:</label>
                <input type="password" name="password" required>

                <button type="submit">Save</button>
            </form>
            <a href="users_list.php">⬅ Back to Users</a>
        </div>
    </div>
</body>
</html>
