<?php
session_start();
require_once "config/db_connect.php";

$id = $_GET['id'] ?? 0;

// Fetch customers & medicines
$customers = $conn->query("SELECT customer_id, name FROM customers ORDER BY name");
$medicines = $conn->query("SELECT medicine_id, name FROM medicines ORDER BY name");

// Fetch existing prescription
$result = $conn->query("SELECT * FROM prescriptions WHERE prescription_id = $id");
$prescription = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $medicine_id = $_POST['medicine_id'];
    $dosage = $_POST['dosage'];
    $date = $_POST['date'];

    $sql = "UPDATE prescriptions SET customer_id=?, medicine_id=?, dosage=?, date=? WHERE prescription_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $customer_id, $medicine_id, $dosage, $date, $id);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Prescription</title>
    <style>
        /* ====== Background ====== */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
             background: url("images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg") no-repeat center center/cover;

            background-size: cover;
            position: relative;
            color: #f1f1f1;
        }

        /* Dark overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0,0,0,0.7);
            z-index: -1;
        }

        /* ====== Card Container ====== */
        .container {
            max-width: 500px;
            margin: 80px auto;
            background: rgba(20, 20, 20, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.6);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #00c6ff;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        select, input[type="text"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: none;
            background: #2b2b2b;
            color: #fff;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(90deg, #00c6ff, #0072ff);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: linear-gradient(90deg, #0072ff, #00c6ff);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Prescription</h2>
        <form method="POST">
            <label>Customer:</label>
            <select name="customer_id" required>
                <?php mysqli_data_seek($customers, 0);
                while ($c = $customers->fetch_assoc()): ?>
                    <option value="<?= $c['customer_id'] ?>" <?= ($c['customer_id'] == $prescription['customer_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Medicine:</label>
            <select name="medicine_id" required>
                <?php mysqli_data_seek($medicines, 0);
                while ($m = $medicines->fetch_assoc()): ?>
                    <option value="<?= $m['medicine_id'] ?>" <?= ($m['medicine_id'] == $prescription['medicine_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Dosage:</label>
            <input type="text" name="dosage" value="<?= htmlspecialchars($prescription['dosage']) ?>" required>

            <label>Date:</label>
            <input type="date" name="date" value="<?= $prescription['date'] ?>" required>

            <button type="submit">Update Prescription</button>
        </form>
    </div>
</body>
</html>
