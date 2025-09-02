<?php
session_start();
require_once "config/db_connect.php";

// Ensure only Cashier can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Cashier') {
    header("Location: dashboard.php");
    exit;
}

// Debugging (uncomment if needed)
// echo "<pre>"; print_r($_POST); echo "</pre>"; exit;

// Validate POST data
if (
    empty($_POST['customer_id']) || 
    empty($_POST['medicine_id']) || 
    empty($_POST['quantity']) || 
    empty($_POST['payment_method'])
) {
    die("Error: Missing form data.");
}

$customer_id = intval($_POST['customer_id']);
$medicine_ids = $_POST['medicine_id'];   // array
$quantities  = $_POST['quantity'];       // array
$payment_method = $_POST['payment_method'];

// Ensure medicine_ids and quantities are arrays
if (!is_array($medicine_ids) || !is_array($quantities)) {
    die("Error: Invalid cart data format.");
}

// Start transaction
$conn->begin_transaction();

try {
    // 1. Create new order
    $order_sql = "INSERT INTO orders (customer_id, date, status) VALUES (?, NOW(), 'Completed')";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $total_amount = 0;

    // 2. Process each medicine in cart
    foreach ($medicine_ids as $index => $med_id) {
        $med_id = intval($med_id);
        $qty = intval($quantities[$index]);

        // Fetch medicine details
        $med_query = $conn->prepare("SELECT price, quantity FROM medicines WHERE medicine_id = ?");
        $med_query->bind_param("i", $med_id);
        $med_query->execute();
        $med_result = $med_query->get_result();
        $medicine = $med_result->fetch_assoc();

        if (!$medicine) {
            throw new Exception("Medicine ID $med_id not found.");
        }

        if ($medicine['quantity'] < $qty) {
            throw new Exception("Not enough stock for Medicine ID $med_id.");
        }

        $price = $medicine['price'];
        $line_total = $price * $qty;
        $total_amount += $line_total;

        // Insert into order_details
        $detail_sql = "INSERT INTO order_details (order_id, medicine_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($detail_sql);
        $stmt->bind_param("iiid", $order_id, $med_id, $qty, $price);
        $stmt->execute();

        // Update stock
        $update_sql = "UPDATE medicines SET quantity = quantity - ? WHERE medicine_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $qty, $med_id);
        $stmt->execute();
    }

    // 3. Insert payment
    $pay_sql = "INSERT INTO payments (order_id, method, amount, date) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($pay_sql);
    $stmt->bind_param("isd", $order_id, $payment_method, $total_amount);
    $stmt->execute();

    // Commit
    $conn->commit();

    // Redirect to receipt
    header("Location: receipt.php?order_id=$order_id");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Error processing sale: " . $e->getMessage());
}
?>
s