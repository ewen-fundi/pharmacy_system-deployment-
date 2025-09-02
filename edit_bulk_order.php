<?php
session_start();
require_once "config/db_connect.php";

$bulk_order_id = $_GET['id'] ?? 0;

// Fetch order
$order_sql = "SELECT * FROM bulk_orders WHERE bulk_order_id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $bulk_order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "Bulk Order not found.";
    exit;
}

// Fetch order details
$details_sql = "SELECT * FROM bulk_order_details WHERE bulk_order_id = ? LIMIT 1";
$stmt = $conn->prepare($details_sql);
$stmt->bind_param("i", $bulk_order_id);
$stmt->execute();
$details_result = $stmt->get_result();
$order_detail = $details_result->fetch_assoc();

// Fetch medicines for dropdown
$medicines = $conn->query("SELECT medicine_id, name, price FROM medicines");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $institution = $_POST['institution'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];

    // Update bulk order
    $update_sql = "UPDATE bulk_orders SET customer_name=?, institution=?, date=?, status=? WHERE bulk_order_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssi", $customer_name, $institution, $date, $status, $bulk_order_id);
    $stmt->execute();

    // Get updated price
    $price_row = $conn->query("SELECT price FROM medicines WHERE medicine_id = $medicine_id")->fetch_assoc();
    $price = $price_row['price'];

    // Update bulk_order_details
    $update_details_sql = "UPDATE bulk_order_details SET medicine_id=?, quantity=?, price=? WHERE bulk_order_id=? AND detail_id=?";
    $stmt = $conn->prepare($update_details_sql);
    $stmt->bind_param("iidii", $medicine_id, $quantity, $price, $bulk_order_id, $order_detail['detail_id']);
    $stmt->execute();

    header("Location: bulk_orders_list.php?msg=updated");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Bulk Order #<?= $order['bulk_order_id'] ?></title>
</head>
<body>
    <h2>Edit Bulk Order #<?= $order['bulk_order_id'] ?></h2>
    <form method="POST">
        Customer Name: <input type="text" name="customer_name" value="<?= htmlspecialchars($order['customer_name']) ?>" required><br><br>
        Institution: <input type="text" name="institution" value="<?= htmlspecialchars($order['institution']) ?>" required><br><br>
        Date: <input type="date" name="date" value="<?= $order['date'] ?>" required><br><br>
        Status: 
        <select name="status" required>
            <option value="Pending" <?= ($order['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
            <option value="Completed" <?= ($order['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
        </select>
        <br><br>
        Medicine: 
        <select name="medicine_id" required>
            <?php while ($m = $medicines->fetch_assoc()): ?>
                <option value="<?= $m['medicine_id'] ?>" <?= ($m['medicine_id'] == $order_detail['medicine_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m['name']) ?> (<?= $m['price'] ?>)
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>
        Quantity: <input type="number" name="quantity" min="1" value="<?= $order_detail['quantity'] ?>" required><br><br>
        <button type="submit">Update</button>
    </form>
    <br>
    <a href="bulk_orders_list.php">⬅ Back</a>
</body>
</html>
