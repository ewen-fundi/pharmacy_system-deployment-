<?php
session_start();
require_once "config/db_connect.php";

// Fetch customers for dropdown
$customers = $conn->query("SELECT customer_id, name FROM customers ORDER BY name");

// Fetch medicines for dropdown
$medicines = $conn->query("SELECT medicine_id, name, price FROM medicines ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $status = $_POST['status'];
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];

    // Insert into orders table
    $order_sql = "INSERT INTO orders (customer_id, date, status) VALUES (?, NOW(), ?)";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("is", $customer_id, $status);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get new order ID

    // Insert into order_details
    $price_query = $conn->query("SELECT price FROM medicines WHERE medicine_id = $medicine_id");
    $price_row = $price_query->fetch_assoc();
    $price = $price_row['price'];

    $details_sql = "INSERT INTO order_details (order_id, medicine_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($details_sql);
    $stmt->bind_param("iiid", $order_id, $medicine_id, $quantity, $price);
    $stmt->execute();

    header("Location: orders_list.php?msg=added");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* ===== Reset & Base ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url("images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg") no-repeat center center/cover;
            position: relative;
            color: #f1f1f1;
        }
        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.6); /* dark overlay */
        }

        /* ===== Card ===== */
        .card {
            position: relative;
            background: rgba(20, 20, 20, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 30px 40px;
            width: 420px;
            z-index: 2;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
            animation: fadeIn 0.8s ease-in-out;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: #fff;
        }

        /* ===== Form ===== */
        label {
            display: block;
            margin: 12px 0 5px;
            font-size: 14px;
            color: #ccc;
        }
        select, input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #444;
            background: #1e1e1e;
            color: #fff;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        select:focus, input:focus {
            border-color: #00bcd4;
            outline: none;
            background: #2b2b2b;
        }

        /* ===== Button ===== */
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #00bcd4, #008ba3);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background: linear-gradient(135deg, #008ba3, #00bcd4);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,188,212,0.3);
        }

        /* ===== Back link ===== */
        .back-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            color: #bbb;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #00bcd4;
        }

        /* ===== Animation ===== */
        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.95);}
            to {opacity: 1; transform: scale(1);}
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>➕ Add Order</h2>
        <form method="POST">
            <label>Customer:</label>
            <select name="customer_id" required>
                <option value="">-- Select Customer --</option>
                <?php while ($c = $customers->fetch_assoc()): ?>
                    <option value="<?= $c['customer_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Medicine:</label>
            <select name="medicine_id" required>
                <option value="">-- Select Medicine --</option>
                <?php while ($m = $medicines->fetch_assoc()): ?>
                    <option value="<?= $m['medicine_id'] ?>">
                        <?= htmlspecialchars($m['name']) ?> (<?= $m['price'] ?> Ksh)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Quantity:</label>
            <input type="number" name="quantity" min="1" required>

            <label>Status:</label>
            <select name="status" required>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
            </select>

            <button type="submit">💾 Save Order</button>
        </form>
        <a href="orders_list.php" class="back-link">⬅ Back to Orders List</a>
    </div>
</body>
</html>
