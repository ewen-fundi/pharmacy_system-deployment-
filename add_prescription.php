<?php
session_start();
require_once "config/db_connect.php";

// Fetch customers & medicines for dropdowns
$customers = $conn->query("SELECT customer_id, name FROM customers ORDER BY name");
$medicines = $conn->query("SELECT medicine_id, name FROM medicines ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $medicine_id = $_POST['medicine_id'];
    $dosage = $_POST['dosage'];
    $date = $_POST['date'];

    $sql = "INSERT INTO prescriptions (customer_id, medicine_id, dosage, date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $customer_id, $medicine_id, $dosage, $date);

    if ($stmt->execute()) {
        header("Location: prescriptions_list.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Prescription</title>
    <style>
        /* ===== Reset & Fonts ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
             background: url("images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg") no-repeat center center/cover;
            background-size: cover;
            color: #f5f5f5;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Dark overlay */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            background: rgba(20,20,20,0.9);
            padding: 30px;
            border-radius: 15px;
            width: 400px;
            box-shadow: 0 0 15px rgba(0,0,0,0.8);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #00c8ff;
        }

        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        select, input[type="text"], input[type="date"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #2a2a2a;
            color: #fff;
        }

        select option {
            background: #2a2a2a;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #00c8ff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #009edc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Prescription</h2>
        <form method="POST">
            <label for="customer_id">Customer:</label>
            <select name="customer_id" required>
                <?php while ($c = $customers->fetch_assoc()): ?>
                    <option value="<?= $c['customer_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="medicine_id">Medicine:</label>
            <select name="medicine_id" required>
                <?php while ($m = $medicines->fetch_assoc()): ?>
                    <option value="<?= $m['medicine_id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="dosage">Dosage:</label>
            <input type="text" name="dosage" required>

            <label for="date">Date:</label>
            <input type="date" name="date" required>

            <button type="submit">Add Prescription</button>
        </form>
    </div>
</body>
</html>
