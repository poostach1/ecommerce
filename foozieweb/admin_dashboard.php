<?php
session_start();
include('db_connect.php'); // Include database connection

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Admin name for display (optional)
$admin_name = $_SESSION['admin']; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Welcome, <?= htmlspecialchars($admin_name); ?>! Admin Dashboard</h2>
        
        <!-- Admin Dashboard Content -->
        <div class="row">
            <!-- Manage Products -->
            <div class="col-md-6 mb-3">
                <h4>Manage Products</h4>
                <a href="manage_products.php" class="btn btn-primary w-100">View / Add / Edit Products</a>
            </div>
            <!-- Manage Orders -->
            <div class="col-md-6 mb-3">
                <h4>View Orders</h4>
                <a href="view_orders.php" class="btn btn-primary w-100">View All Orders</a>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="mt-5 text-center">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
