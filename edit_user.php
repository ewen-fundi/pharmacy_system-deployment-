<?php
session_start();
require_once "config/db_connect.php";

// Allow only Admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "Access denied. Only Admins can access this page.";
    exit;
}

$id = $_GET['id'] ?? 0;

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $role  = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name=?, email=?, role=?, password_hash=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $role, $password_hash, $id);
    } else {
        $sql = "UPDATE users SET name=?, email=?, role=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $role, $id);
    }

    if ($stmt->execute()) {
        header("Location: users_list.php?msg=updated");
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
    <title>Edit User</title>
    <style>
        /* Background */
        body {
            font-family: Arial, sans-serif;
             background: url("images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg") no-repeat center center/cover;
            background-size: cover;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        /* Overlay */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); /* Dark overlay */
            z-index: 0;
        }

        /* Card container */
        .card {
            position: relative;
            z-index: 1;
            background: rgba(20, 20, 20, 0.9);
            padding: 30px;
            border-radius: 15px;
            width: 400px;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.6);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4CAF50;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 8px;
            background: #333;
            color: #fff;
        }

        input:focus, select:focus {
            outline: none;
            border: 1px solid #4CAF50;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #45a049;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            color: #ccc;
            text-decoration: none;
        }

        .back-link:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Edit User</h2>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="Admin" <?= ($user['role'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                <option value="Pharmacist" <?= ($user['role'] == 'Pharmacist') ? 'selected' : '' ?>>Pharmacist</option>
                <option value="Cashier" <?= ($user['role'] == 'Cashier') ? 'selected' : '' ?>>Cashier</option>
                <option value="Procurement" <?= ($user['role'] == 'Procurement') ? 'selected' : '' ?>>Procurement</option>
                <option value="Customer" <?= ($user['role'] == 'Customer') ? 'selected' : '' ?>>Customer</option>
            </select>

            <label>Password:</label>
            <input type="password" name="password" placeholder="Leave blank to keep old password">

            <button type="submit">Update</button>
        </form>
        <a href="users_list.php" class="back-link">⬅ Back to Users</a>
    </div>
</body>
</html>
