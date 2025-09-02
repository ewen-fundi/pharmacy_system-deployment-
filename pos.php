<?php
session_start();
require_once "config/db_connect.php";

// Restrict to Cashier only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Cashier') {
    header("Location: dashboard.php");
    exit;
}

// Fetch customers
$customers = $conn->query("SELECT customer_id, name FROM customers ORDER BY name");

// Fetch medicines
$medicines = $conn->query("SELECT medicine_id, name, price, quantity FROM medicines WHERE quantity > 0 ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Point of Sale</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('images/portrait-female-pharmacist-working-drugstore.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6); /* dark overlay */
            z-index: -1;
        }
        .overlay {
            min-height: 100vh;
            padding: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pos-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.4);
            animation: fadeIn 0.5s ease-in-out;
            width: 100%;
            max-width: 850px;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }
        .total-box {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            text-align: right;
            padding: 10px;
        }

        /* ===== Dark Mode ===== */
        body.dark {
            color: #f1f1f1;
        }
        body.dark .pos-card {
            background: rgba(30,30,30,0.9);
            color: #f1f1f1;
        }
        body.dark h2 {
            color: #ffcc00;
        }
        body.dark .table {
            color: #fff;
            background: rgba(50,50,50,0.9);
        }
        body.dark .table thead {
            background: #444;
        }
        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 30px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        .theme-toggle:hover {
            background: #0056b3;
        }
    </style>
    <script>
        function addToCart() {
            let medSelect = document.getElementById("medicine_id");
            let quantity = document.getElementById("quantity").value;
            let cartTable = document.getElementById("cart-body");

            if (medSelect.value === "" || quantity <= 0) {
                alert("⚠️ Please select medicine and enter valid quantity.");
                return;
            }

            let selectedText = medSelect.options[medSelect.selectedIndex].text;
            let medId = medSelect.value;
            let price = medSelect.options[medSelect.selectedIndex].getAttribute("data-price");

            let row = document.createElement("tr");

            row.innerHTML = `
                <td>
                    <input type="hidden" name="medicine_id[]" value="${medId}">
                    <input type="hidden" name="price[]" value="${price}">
                    ${selectedText}
                </td>
                <td><input type="number" class="form-control" name="quantity[]" value="${quantity}" min="1" onchange="updateTotal()"></td>
                <td class="item-total">Ksh ${(price * quantity).toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); updateTotal();">Remove</button></td>
            `;

            cartTable.appendChild(row);
            updateTotal();
        }

        function updateTotal() {
            let rows = document.querySelectorAll("#cart-body tr");
            let total = 0;

            rows.forEach(row => {
                let qty = row.querySelector("input[name='quantity[]']").value;
                let price = row.querySelector("input[name='price[]']").value;
                let itemTotal = qty * price;
                row.querySelector(".item-total").innerText = "Ksh " + itemTotal.toFixed(2);
                total += itemTotal;
            });

            document.getElementById("grand-total").innerText = "Grand Total: Ksh " + total.toFixed(2);
        }

        function toggleTheme() {
            document.body.classList.toggle("dark");
        }
    </script>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()">🌙 Toggle Theme</button>
    <div class="overlay">
        <div class="pos-card">
            <h2>💊 Point of Sale (POS)</h2>

            <form method="POST" action="pos_process.php">
                <!-- Select Customer -->
                <div class="mb-3">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">-- Select Customer --</option>
                        <?php while ($c = $customers->fetch_assoc()): ?>
                            <option value="<?= $c['customer_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Select Medicine -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Medicine</label>
                        <select id="medicine_id" class="form-select">
                            <option value="">-- Select Medicine --</option>
                            <?php
                            $medicines = $conn->query("SELECT medicine_id, name, price, quantity FROM medicines WHERE quantity > 0 ORDER BY name");
                            while ($m = $medicines->fetch_assoc()):
                            ?>
                                <option value="<?= $m['medicine_id'] ?>" data-price="<?= $m['price'] ?>">
                                    <?= htmlspecialchars($m['name']) ?> (Stock: <?= $m['quantity'] ?> | Price: <?= $m['price'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" id="quantity" class="form-control" min="1">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="addToCart()">➕ Add to Cart</button>
                    </div>
                </div>

                <!-- Cart Table -->
                <h5>🛒 Cart</h5>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="cart-body"></tbody>
                </table>

                <div id="grand-total" class="total-box">Grand Total: Ksh 0.00</div>

                <!-- Payment -->
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="">-- Select Payment Method --</option>
                        <option value="Cash">Cash</option>
                        <option value="M-Pesa">M-Pesa</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100">✅ Complete Sale</button>
            </form>
        </div>
    </div>
</body>
</html>
