<?php
session_start();
require_once "config/db_connect.php";

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM prescriptions WHERE prescription_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: prescriptions_list.php");
    exit;
} else {
    echo "Error deleting: " . $conn->error;
}
