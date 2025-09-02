<?php
session_start();
require_once "config/db_connect.php";

$medicine_id = $_GET['id'] ?? 0;

// First delete from prescriptions table
$delete_prescriptions_sql = "DELETE FROM prescriptions WHERE medicine_id = ?";
$stmt = $conn->prepare($delete_prescriptions_sql);
$stmt->bind_param("i", $medicine_id);
$stmt->execute();

// Then delete from order_details table
$delete_order_details_sql = "DELETE FROM order_details WHERE medicine_id = ?";
$stmt = $conn->prepare($delete_order_details_sql);
$stmt->bind_param("i", $medicine_id);
$stmt->execute();

// Finally delete the medicine itself
$delete_medicine_sql = "DELETE FROM medicines WHERE medicine_id = ?";
$stmt = $conn->prepare($delete_medicine_sql);
$stmt->bind_param("i", $medicine_id);
$stmt->execute();

// Redirect back to medicines list
header("Location: medicines_list.php?msg=deleted");
exit;
