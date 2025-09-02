<?php
session_start();

require_once "config/db_connect.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    die("Please fill all fields.");
}

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($password === $user['password_hash']) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        header("Location: dashboard.php");
        exit;
    } else {
        echo "❌ Invalid password.";
    }
} else {
    echo "❌ Email not found.";
}

$stmt->close();
$conn->close();
?>
