<?php
session_start();
require_once "config/db_connect.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $institution = $_POST['institution'];
    $contact = $_POST['contact'];
    $order_date = $_POST['order_date'];
    $status = $_POST['status'];

    // Insert into DB with correct column name "order_date"
    $stmt = $conn->prepare("INSERT INTO bulk_orders (customer_name, institution, contact, order_date, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $customer_name, $institution, $contact, $order_date, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Bulk order added successfully!'); window.location='view_bulk_orders.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bulk Order</title>
    <style>
        /* ===== Dark Theme with Background Image ===== */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
             background: url("images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg") no-repeat center center/cover;
            background-size: cover;
            color: #f1f1f1;
        }

        /* Dark overlay to make image semi-transparent */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6); /* semi-transparent black */
            z-index: -1;
        }

        .container {
            width: 400px;
            margin: 80px auto;
            background: rgba(30,30,30,0.9);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.6);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffcc00;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 6px;
        }

        input[type="submit"] {
            background: #ffcc00;
            color: #000;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        input[type="submit"]:hover {
            background: #ffaa00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Bulk Order</h2>
        <form method="POST">
            <label>Customer Name:</label>
            <input type="text" name="customer_name" required>

            <label>Institution:</label>
            <input type="text" name="institution">

            <label>Contact:</label>
            <input type="text" name="contact">

            <label>Order Date:</label>
            <input type="date" name="order_date" required>

            <label>Status:</label>
            <select name="status">
                <option value="Pending">Pending</option>
                <option value="Processing">Processing</option>
                <option value="Delivered">Delivered</option>
                <option value="Paid">Paid</option>
            </select>

            <input type="submit" value="Add Order">
        </form>
    </div>
</body>
</html>
