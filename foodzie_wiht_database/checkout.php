<?php
session_start();
require_once 'db.php';  // Assuming you have a separate file for database connection

// Prevent browser cache
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Ensure cart is not empty
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Initialize variables for checkout form
$name = $address = $phone = '';
$totalPrice = 0;
$products = $_SESSION['cart'];

// Calculate total price
foreach ($products as $product) {
    $totalPrice += $product['price'] * $product['quantity'];
}

// Handle form submission (when order is confirmed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $user_id = 1;  // Replace with actual user ID from session or login

    // Collect order details
    $product_ids = array_map(function($product) {
        return $product['id'];  // Collecting product IDs for the order
    }, $products);

    // Insert order into database
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_ids, total_price, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("isd", $user_id, implode(',', $product_ids), $totalPrice);
    $stmt->execute();

    // Clear the cart after order is placed
    unset($_SESSION['cart']);

    // Redirect to order confirmation page
    header("Location: order_confirmation.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FOODZIE Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .checkout-container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }
        .checkout-summary {
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
            padding: 20px;
        }
        .checkout-summary h4 {
            margin-bottom: 20px;
        }
        .checkout-summary ul {
            list-style: none;
            padding: 0;
        }
        .checkout-summary ul li {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .checkout-summary ul li:last-child {
            border-bottom: none;
        }
        .checkout-form {
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
            padding: 20px;
        }
        .checkout-form h4 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">FOODZIE</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Checkout Section -->
    <section class="py-5">
        <div class="checkout-container">
            <h2 class="text-center mb-4">Checkout</h2>
            <div class="row">
                <!-- Order Summary -->
                <div class="col-md-6 checkout-summary">
                    <h4>Order Summary</h4>
                    <ul>
                        <?php foreach ($products as $product): ?>
                            <li class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold"><?= htmlspecialchars($product['name']) ?></span> 
                                    <small class="text-muted">(x<?= $product['quantity'] ?>)</small>
                                </div>
                                <span>₱<?= number_format($product['price'] * $product['quantity'], 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <h5 class="mt-3 text-end">Total: ₱<?= number_format($totalPrice, 2) ?></h5>
                </div>

                <!-- Shipping Form -->
                <div class="col-md-6 checkout-form">
                    <h4>Shipping Information</h4>
                    <form id="checkout-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#confirmationModal">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to place the order for the selected items?</p>
                    <p><strong>Total: ₱<?= number_format($totalPrice, 2) ?></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="checkout.php" class="d-inline">
                        <input type="hidden" name="confirm_order" value="1">
                        <button type="submit" class="btn btn-primary">Yes, Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 FOODZIE. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
