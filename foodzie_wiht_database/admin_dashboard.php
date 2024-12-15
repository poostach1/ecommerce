<?php
session_start();
include('db.php'); // Include database connection

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Admin name for display
$admin_name = htmlspecialchars($_SESSION['admin']); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            padding-top: 70px;
            background-color: #f8f9fa;
        }
        .dashboard-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        footer {
            margin-top: 30px;
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text me-3">Welcome, <?= $admin_name; ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container">
        <div class="row">
            <!-- Manage Products -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manage Products</h5>
                        <p class="card-text">Add, edit, or view all products.</p>
                        <a href="manage_products.php" class="btn btn-primary">Go to Products</a>
                    </div>
                </div>
            </div>
            <!-- View Orders -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">View Orders</h5>
                        <p class="card-text">Check all customer orders.</p>
                        <a href="view_orders.php" class="btn btn-primary">View Orders</a>
                    </div>
                </div>
            </div>
            <!-- Manage Order Items -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manage Order Items</h5>
                        <p class="card-text">View detailed order items.</p>
                        <a href="view_order_details.php" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Admin Activities -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Admin Activities</h5>
                        <p class="card-text">Track admin activity logs.</p>
                        <a href="view_admin_activities.php" class="btn btn-secondary">View Activities</a>
                    </div>
                </div>
            </div>
            <!-- Manage Users -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manage Users</h5>
                        <p class="card-text">Edit or manage user details.</p>
                        <a href="manage_users.php" class="btn btn-secondary">Manage Users</a>
                    </div>
                </div>
            </div>
            <!-- Site Settings -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Site Settings</h5>
                        <p class="card-text">Update website settings.</p>
                        <a href="site_settings.php" class="btn btn-secondary">Settings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; <?= date('Y'); ?> Admin Dashboard. All Rights Reserved.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
