<?php
session_start();
require_once "config/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $batch_number = $_POST['batch_number'];
    $expiry_date = $_POST['expiry_date'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $supplier_id = $_POST['supplier_id'];

    // Handle image upload
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "images/medicines/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // create folder if not exists
        }
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $image = $fileName;
        }
    }

    $sql = "INSERT INTO medicines (name, batch_number, expiry_date, quantity, price, supplier_id, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssidis", $name, $batch_number, $expiry_date, $quantity, $price, $supplier_id, $image);

    if ($stmt->execute()) {
        header("Location: medicines_list.php");
        exit;
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Medicine</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('images/portrait-female-pharmacist-working-drugstore.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0; 
            padding: 0;
        }
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            background: rgba(255,255,255,0.95);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #2C3E50;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
            color: #333;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            background: #27ae60;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #219150;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Medicine</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Batch Number:</label>
            <input type="text" name="batch_number">

            <label>Expiry Date:</label>
            <input type="date" name="expiry_date">

            <label>Quantity:</label>
            <input type="number" name="quantity" required>

            <label>Price:</label>
            <input type="number" step="0.01" name="price" required>

            <label>Supplier:</label>
            <select name="supplier_id" required>
                <option value="">-- Select Supplier --</option>
                <?php
                $suppliers = $conn->query("SELECT supplier_id, name FROM suppliers");
                while ($row = $suppliers->fetch_assoc()) {
                    echo "<option value='{$row['supplier_id']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <label>Upload Medicine Image:</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">💊 Save Medicine</button>
        </form>
    </div>
</body>
</html>
