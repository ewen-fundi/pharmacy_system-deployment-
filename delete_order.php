<?php
session_start();
require_once "config/db_connect.php";

$order_id = $_GET['id'] ?? 0;

// Delete payments linked to this order
$stmt = $conn->prepare("DELETE FROM payments WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

// Delete order_details linked to this order
$stmt = $conn->prepare("DELETE FROM order_details WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

// Finally delete the order
$stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

header("Location: orders_list.php?msg=deleted");
exit;
