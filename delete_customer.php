<?php
session_start();
require_once "config/db_connect.php";

$customer_id = $_GET['id'] ?? 0;

// Delete prescriptions linked to customer
$delete_prescriptions_sql = "DELETE FROM prescriptions WHERE customer_id = ?";
$stmt = $conn->prepare($delete_prescriptions_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();

// Delete order_details via orders
$delete_order_details_sql = "
    DELETE od FROM order_details od
    JOIN orders o ON od.order_id = o.order_id
    WHERE o.customer_id = ?
";
$stmt = $conn->prepare($delete_order_details_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();

// Delete payments via orders
$delete_payments_sql = "
    DELETE p FROM payments p
    JOIN orders o ON p.order_id = o.order_id
    WHERE o.customer_id = ?
";
$stmt = $conn->prepare($delete_payments_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();

// Delete orders linked to customer
$delete_orders_sql = "DELETE FROM orders WHERE customer_id = ?";
$stmt = $conn->prepare($delete_orders_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();

// Finally delete the customer
$delete_customer_sql = "DELETE FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($delete_customer_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();

header("Location: customers_list.php?msg=deleted");
exit;
