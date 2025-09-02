<?php
session_start();
require_once "config/db_connect.php";

// Protect route
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

$payment_id = $_GET['id'] ?? 0;

// Delete payment
$sql = "DELETE FROM payments WHERE payment_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);

if ($stmt->execute()) {
    header("Location: payments_list.php?msg=deleted");
    exit;
} else {
    echo "Error: " . $conn->error;
}
?>
