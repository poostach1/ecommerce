<?php
session_start();

// Prevent browser cache
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Ensure cart is not empty
if (empty($_SESSION['cart'])) {
    header("Location: index.php");  // Redirect to products page if cart is empty
    exit;
}

// Initialize variables for checkout form
$name = $address = $phone = $totalPrice = 0;

// Calculate total price
$totalPrice = 0;
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    // Example product data (replace with actual product data from database)
    $productPrice = 100; // Example price
    $totalPrice += $productPrice * $quantity;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // Store order in the database (code for database interaction will be added later)
    // For now, we'll just simulate it.

    // Clear the cart after order is placed
    unset($_SESSION['cart']);

    // Redirect to a confirmation page
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
</head>
<body>

    <!-- Navbar -->
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">FOODZIE</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <!-- Checkout Section -->
    <section id="checkout" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Checkout</h2>

            <!-- Checkout Form -->
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
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
                <h4>Total Price: ₱<?= number_format($totalPrice, 2) ?></h4>
                <button type="submit" class="btn btn-primary">Place Order</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2024 FOODZIE. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
