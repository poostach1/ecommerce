<?php
session_start();
include('db.php');

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get the order ID from the URL
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']); // Validate the order_id to prevent SQL injection
} else {
    header("Location: order-history.php");
    exit();
}

// Fetch user ID based on the logged-in user
$user = $_SESSION['user'];
$userQuery = $conn->query("SELECT * FROM users WHERE username = '$user'");
$userRow = $userQuery->fetch_assoc();
$user_id = $userRow['id']; // Get the logged-in user's ID

// Fetch the order details for the given order ID
$orderQuery = $conn->query("SELECT * FROM orders WHERE id = '$order_id' AND user_id = '$user_id'");
$order = $orderQuery->fetch_assoc();

if (!$order) {
    // If no order found, redirect to order history
    header("Location: order-history.php");
    exit();
}

// Fetch the products in the order
$orderItemsQuery = $conn->query("SELECT oi.*, p.name, p.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = '$order_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | FOODZIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style>
        .order-details-table th, .order-details-table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">FOODZIE</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">About Us</a></li>
                    </ul>
                    <!-- Profile Dropdown Menu -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Profile (<?= htmlspecialchars($_SESSION['user']); ?>)
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile.php">View Profile</a></li>
                                <li><a class="dropdown-item" href="order-history.php">Order History</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <!-- Order Details Section -->
    <section class="order-details-table py-5">
        <div class="container">
            <h2 class="text-center mb-4">Order Details: #<?= htmlspecialchars($order['id']); ?></h2>
            
            <p><strong>Order Date:</strong> <?= date('F j, Y', strtotime($order['order_date'])); ?></p>
            <p><strong>Status:</strong> 
                <?php
                    switch ($order['status']) {
                        case 'pending':
                            echo '<span class="badge bg-warning">Pending</span>';
                            break;
                        case 'completed':
                            echo '<span class="badge bg-success">Completed</span>';
                            break;
                        case 'cancelled':
                            echo '<span class="badge bg-danger">Cancelled</span>';
                            break;
                        default:
                            echo '<span class="badge bg-secondary">Unknown</span>';
                            break;
                    }
                ?>
            </p>
            <p><strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2); ?></p>

            <h3 class="text-center">Products in Order</h3>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Product Name</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $orderItemsQuery->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']); ?></td>
                            <td>₱<?= number_format($item['price'], 2); ?></td>
                            <td><?= htmlspecialchars($item['quantity']); ?></td>
                            <td>₱<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <a href="order-history.php" class="btn btn-primary">Back to Order History</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2024 FOODZIE. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
