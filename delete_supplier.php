<?php
session_start();
require_once "config/db_connect.php";

$supplier_id = $_GET['id'] ?? 0;

// Delete medicines from this supplier (first delete order details and prescriptions linked to those medicines)
$medicines_result = $conn->query("SELECT medicine_id FROM medicines WHERE supplier_id = $supplier_id");

while ($med = $medicines_result->fetch_assoc()) {
    $mid = $med['medicine_id'];

    // Delete prescriptions for this medicine
    $stmt = $conn->prepare("DELETE FROM prescriptions WHERE medicine_id = ?");
    $stmt->bind_param("i", $mid);
    $stmt->execute();

    // Delete order details for this medicine
    $stmt = $conn->prepare("DELETE FROM order_details WHERE medicine_id = ?");
    $stmt->bind_param("i", $mid);
    $stmt->execute();
}

// Delete medicines themselves
$stmt = $conn->prepare("DELETE FROM medicines WHERE supplier_id = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();

// Finally delete the supplier
$stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();

header("Location: suppliers_list.php?msg=deleted");
exit;
