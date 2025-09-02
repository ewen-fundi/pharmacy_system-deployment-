<?php
session_start();
require_once "config/db_connect.php";

$bulk_order_id = $_GET['id'] ?? 0;

if ($bulk_order_id) {
    // Delete order details first (to avoid foreign key error)
    $stmt = $conn->prepare("DELETE FROM bulk_order_details WHERE bulk_order_id = ?");
    $stmt->bind_param("i", $bulk_order_id);
    $stmt->execute();

    // Delete bulk order
    $stmt = $conn->prepare("DELETE FROM bulk_orders WHERE bulk_order_id = ?");
    $stmt->bind_param("i", $bulk_order_id);
    $stmt->execute();
}

header("Location: bulk_orders_list.php?msg=deleted");
exit;
?>
