<?php
session_start();
require_once "config/db_connect.php";

$order_id = $_GET['id'] ?? 0;

// Fetch customers for dropdown
$customers = $conn->query("SELECT customer_id, name FROM customers ORDER BY name");

// Fetch medicines for dropdown
$medicines = $conn->query("SELECT medicine_id, name, price FROM medicines ORDER BY name");

// Fetch existing order
$order_sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit;
}

// Fetch existing order details (first medicine in this order)
$details_sql = "SELECT * FROM order_details WHERE order_id = ? LIMIT 1";
$stmt = $conn->prepare($details_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$details_result = $stmt->get_result();
$order_detail = $details_result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $status = $_POST['status'];
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];

    // Update orders table
    $update_order_sql = "UPDATE orders SET customer_id=?, status=? WHERE order_id=?";
    $stmt = $conn->prepare($update_order_sql);
    $stmt->bind_param("isi", $customer_id, $status, $order_id);
    $stmt->execute();

    // Get new price for selected medicine
    $price_query = $conn->query("SELECT price FROM medicines WHERE medicine_id = $medicine_id");
    $price_row = $price_query->fetch_assoc();
    $price = $price_row['price'];

    // Prevent duplicate medicine
    $check_sql = "SELECT * FROM order_details WHERE order_id = ? AND medicine_id = ? AND medicine_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("iii", $order_id, $medicine_id, $order_detail['medicine_id']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "Error: This medicine is already in the order. Please update the quantity instead.";
        exit;
    }

    // Update order_details
    $update_details_sql = "UPDATE order_details SET medicine_id=?, quantity=?, price=? WHERE order_id=? AND medicine_id=?";
    $stmt = $conn->prepare($update_details_sql);
    $stmt->bind_param("iidii", $medicine_id, $quantity, $price, $order_id, $order_detail['medicine_id']);
    $stmt->execute();

    header("Location: orders_list.php?msg=updated");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Order #<?= $order['order_id'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif;}
        body {
            background: url("images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg") no-repeat center center/cover;

            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px;
            position: relative;
            color: #f1f1f1;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: -1;
        }
        .card {
            background: rgba(20,20,20,0.8);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 30px 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
            animation: fadeIn 0.8s ease-in-out;
        }
        h2 {margin-bottom: 20px; text-align: center; color: #fff;}
        label {display: block; margin: 12px 0 5px; font-weight: 600; color: #ccc;}
        select, input[type="number"], button {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: none;
            margin-bottom: 15px;
            font-size: 14px;
        }
        select, input[type="number"] {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        option {color: #000;}
        button {
            background: linear-gradient(135deg, #00bcd4, #008ba3);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background: linear-gradient(135deg, #008ba3, #00bcd4);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,188,212,0.3);
        }
        .back-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #444, #222);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            background: linear-gradient(135deg, #222, #444);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,255,255,0.2);
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.95);}
            to {opacity: 1; transform: scale(1);}
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>✏️ Edit Order #<?= $order['order_id'] ?></h2>
        <form method="POST">
            <label>Customer:</label>
            <select name="customer_id" required>
                <?php while ($c = $customers->fetch_assoc()): ?>
                    <option value="<?= $c['customer_id'] ?>" <?= ($c['customer_id'] == $order['customer_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Medicine:</label>
            <select name="medicine_id" required>
                <?php
                mysqli_data_seek($medicines, 0);
                while ($m = $medicines->fetch_assoc()):
                ?>
                    <option value="<?= $m['medicine_id'] ?>" <?= ($m['medicine_id'] == $order_detail['medicine_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['name']) ?> (Price: <?= $m['price'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Quantity:</label>
            <input type="number" name="quantity" min="1" value="<?= $order_detail['quantity'] ?>" required>

            <label>Status:</label>
            <select name="status" required>
                <option value="Pending" <?= ($order['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                <option value="Completed" <?= ($order['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
            </select>

            <button type="submit">Update Order</button>
        </form>
        <a href="orders_list.php" class="back-link">⬅ Back to Orders List</a>
    </div>
</body>
</html>
