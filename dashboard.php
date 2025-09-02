<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

$role = $_SESSION['role'];
$name = $_SESSION['name'];

// Example data – Replace with real DB queries
$total_customers = 120;
$total_orders = 85;
$total_medicines = 230;
$total_sales = 45200;
$low_stock = 5;
$expiring_soon = 3;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pharmacy Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Segoe UI', Tahoma, sans-serif; margin:0; background: #f4f6f9; }

    /* Sidebar */
    .sidebar { 
      background: url('images/sidebar-bg.jpg') no-repeat center center; 
      background-size: cover; 
      min-height: 100vh; 
      color: white; 
      position: fixed; 
      width: 260px; 
      top: 0; 
      left: 0; 
    }
    .sidebar-overlay { 
      background: rgba(0,0,0,0.7); 
      position: absolute; 
      top: 0; left: 0; 
      width: 260px; 
      min-height: 100vh; 
      padding: 20px; 
    }
    .sidebar a { 
      color: #ddd; 
      display:block; 
      padding:10px; 
      margin:6px 0; 
      border-radius:6px; 
      text-decoration:none; 
      transition:0.3s; 
    }
    .sidebar a:hover { 
      background: rgba(255,255,255,0.2); 
      color:#fff; 
    }

    /* Main content with background */
    .main-content { 
      margin-left: 280px; 
      min-height: 100vh; 
      position: relative; 
      background: url('images/portrait-female-pharmacist-working-drugstore.jpg') no-repeat center center fixed; 
      background-size: cover; 
    }
    .main-overlay { 
      background: rgba(255,255,255,0.9); 
      padding: 25px; 
      border-radius: 12px; 
      min-height: 100vh; 
    }

    /* Hero Section */
    .hero-section {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 30px;
    }
    .hero-bg {
      background: url('images/pharmacy-hero.jpg') no-repeat center center/cover;
      min-height: 250px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }
    .hero-bg .overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.55);
    }
    .hero-bg .content {
      position: relative;
      color: white;
      text-align: center;
      padding: 20px;
    }
    .hero-bg h1 {
      font-weight: bold;
    }

    .card h5 { font-weight: bold; }

    /* Chatbot styles */
    #chatbot-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 300px;
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
      z-index: 9999;
    }
    #chatbot-header {
      background: #0d6efd;
      color: #fff;
      padding: 10px;
      font-weight: bold;
      text-align: center;
      border-radius: 10px 10px 0 0;
    }
    #chatbot-messages {
      height: 250px;
      padding: 10px;
      overflow-y: auto;
      font-size: 14px;
    }
    #chatbot-input {
      display: flex;
      border-top: 1px solid #ccc;
    }
    #chatbot-input input {
      flex: 1;
      padding: 8px;
      border: none;
      outline: none;
    }
    #chatbot-input button {
      background: #0d6efd;
      color: white;
      border: none;
      padding: 8px 12px;
      cursor: pointer;
    }
    #chatbot-input button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-overlay">
    <h4 class="mb-3">💊 Pharmacy</h4>
    <p>Welcome, <strong><?= htmlspecialchars($name) ?></strong><br>
      <span class="badge bg-info"><?= htmlspecialchars($role) ?></span></p>
    <hr class="text-light">

    <?php if ($role === 'Admin'): ?>
      <a href="dashboard.php">🏠 Dashboard</a>
      <a href="suppliers_list.php">📦 Suppliers</a>
      <a href="medicines_list.php">💊 Medicines</a>
      <a href="customers_list.php">👥 Customers</a>
      <a href="orders_list.php">🛒 Orders</a>
      <a href="prescriptions_list.php">📄 Prescriptions</a>
      <a href="users_list.php">🔑 Users</a>
      <a href="bulk_orders_list.php">📑 Bulk Orders</a>
      <a href="pos.php">💳 POS</a>
      <a href="reports.php">📊 Reports</a>
    <?php elseif ($role === 'Pharmacist'): ?>
      <a href="prescriptions_list.php">📄 Prescriptions</a>
      <a href="medicines_list.php">💊 Medicines</a>
      <a href="orders_list.php">🛒 Orders</a>
    <?php elseif ($role === 'Cashier'): ?>
      <a href="pos.php">💳 POS</a>
      <a href="orders_list.php">🛒 Orders</a>
    <?php elseif ($role === 'Procurement'): ?>
      <a href="suppliers_list.php">📦 Suppliers</a>
      <a href="bulk_orders_list.php">📑 Bulk Orders</a>
    <?php elseif ($role === 'Customer'): ?>
      <a href="orders_list.php">🛍 My Orders</a>
      <a href="prescriptions_list.php">📄 My Prescriptions</a>
    <?php endif; ?>
  </div>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="main-overlay">
    
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-bg">
        <div class="overlay"></div>
        <div class="content">
          <h1>Welcome to the Ultracare Pharmacy Management System</h1>
          <p class="lead">Manage medicines, sales & reports with ease.</p>
        </div>
      </div>
    </div>

    <!-- Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>📊 Dashboard Overview</h2>
      <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
    </div>

    <!-- Quick Stats -->
    <div class="row text-center">
      <div class="col-md-3">
        <div class="card shadow p-3">
          <h5>Customers</h5>
          <p class="display-6 text-primary"><?= $total_customers ?></p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card shadow p-3">
          <h5>Orders</h5>
          <p class="display-6 text-success"><?= $total_orders ?></p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card shadow p-3">
          <h5>Medicines</h5>
          <p class="display-6 text-warning"><?= $total_medicines ?></p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card shadow p-3">
          <h5>Sales (KES)</h5>
          <p class="display-6 text-danger"><?= number_format($total_sales) ?></p>
        </div>
      </div>
    </div>

    <!-- Alerts -->
    <div class="row mt-4">
      <div class="col-md-6">
        <div class="alert alert-warning shadow">
          ⚠️ <?= $low_stock ?> medicines are low in stock!
        </div>
      </div>
      <div class="col-md-6">
        <div class="alert alert-danger shadow">
          ⏳ <?= $expiring_soon ?> medicines expiring soon!
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row mt-4">
      <div class="col-md-6">
        <div class="card p-3 shadow">
          <h5>Sales Trend</h5>
          <canvas id="salesChart"></canvas>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-3 shadow">
          <h5>Top Medicines</h5>
          <canvas id="medChart"></canvas>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Chatbot -->
<div id="chatbot-container">
  <div id="chatbot-header">💬 AI Assistant</div>
  <div id="chatbot-messages"></div>
  <div id="chatbot-input">
    <input type="text" id="chatbot-text" placeholder="Type a message...">
    <button onclick="sendMessage()">Send</button>
  </div>
</div>

<script>
  // Sales Trend Chart
  const ctx1 = document.getElementById('salesChart');
  new Chart(ctx1, {
    type: 'line',
    data: {
      labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
      datasets: [{
        label: 'Sales (KES)',
        data: [5000,7000,4000,9000,6000,10000,8000],
        borderColor: 'green',
        backgroundColor: 'rgba(0,128,0,0.2)',
        fill: true,
        tension: 0.4
      }]
    }
  });

  // Top Medicines Chart
  const ctx2 = document.getElementById('medChart');
  new Chart(ctx2, {
    type: 'bar',
    data: {
      labels: ['Paracetamol','Ibuprofen','Amoxicillin','Metformin','Losartan'],
      datasets: [{
        label: 'Units Sold',
        data: [120,95,80,60,45],
        backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#6f42c1']
      }]
    }
  });

  // Chatbot logic
  function sendMessage() {
    let input = document.getElementById("chatbot-text");
    let message = input.value.trim();
    if (message === "") return;

    let messages = document.getElementById("chatbot-messages");
    messages.innerHTML += "<div><b>You:</b> " + message + "</div>";

    fetch("chatbot.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "message=" + encodeURIComponent(message)
    })
    .then(response => response.text())
    .then(reply => {
        messages.innerHTML += "<div><b>Bot:</b> " + reply + "</div>";
        messages.scrollTop = messages.scrollHeight;
    });

    input.value = "";
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
