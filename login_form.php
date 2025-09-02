<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy System - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url('images/man-wheelchair-social-worker-choosing-prescription-treatment.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .card {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.85);
        }
    </style>
</head>
<body>

<div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-3">
            <h3 class="fw-bold">Pharmacy System</h3>
            <p class="text-muted">Login to continue</p>
        </div>

        <!-- Login Form -->
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">👁</button>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">© <?= date("Y") ?> Pharmacy Management System</small>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Show Password Script -->
<script>
    const togglePassword = document.querySelector("#togglePassword");
    const passwordField = document.querySelector("#password");

    togglePassword.addEventListener("click", function () {
        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);
        this.textContent = type === "password" ? "👁" : "🙈";
    });
</script>
</body>
</html>
