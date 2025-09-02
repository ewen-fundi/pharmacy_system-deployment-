<?php
session_start();
require_once "config/db_connect.php";
// Allow only Admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "Access denied. Only Admins can access this page.";
    exit;
}


if ($_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// Prevent admin from deleting their own account
if ($id == $_SESSION['user_id']) {
    echo "❌ You cannot delete your own account!";
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: users_list.php?msg=deleted");
    exit;
} else {
    echo "Error: " . $conn->error;
}
?>
