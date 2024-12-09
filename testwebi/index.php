<?php
session_start();
include('db.php');

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Initialize cart for the logged-in user, if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle "Add to Cart" request via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $product_id = intval($_POST['product_id']); // Validate product_id

    // Check if product exists in the database
    $productQuery = $conn->query("SELECT * FROM products WHERE id = $product_id");
    if ($productQuery && $productQuery->num_rows > 0) {
        // Add product to session cart if not already in it
        if (!isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = 1; // Add product with quantity 1
        } else {
            $_SESSION['cart'][$product_id]++; // Increment quantity
        }
    }

    // Return updated cart count
    echo json_encode(['cart_count' => array_sum($_SESSION['cart'])]);
    exit();
}

// Handle "Remove from Cart" request via AJAX
if (isset($_POST['action']) && $_POST['action'] === 'remove_from_cart') {
    $product_id = intval($_POST['product_id']);
    
    // Remove the product from the cart
    unset($_SESSION['cart'][$product_id]);

    // Return updated cart count
    echo json_encode(['cart_count' => array_sum($_SESSION['cart'])]);
    exit();
}

// Fetch all products from the database
$productResults = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FOODZIE WEBSITE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">FOODZIE</a> <!-- Updated link -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">About Us</a></li>
                        <!-- Profile Button with username instead of email -->
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profile (<?= htmlspecialchars($_SESSION['user']); ?>)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                    <a class="nav-link" href="cart.php">
                        <i class="fa fa-shopping-cart"></i> Cart (<span id="cart-count"><?= array_sum($_SESSION['cart']); ?></span>)
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Hero Section -->
    <section class="hero bg-primary text-white py-5" style="background: url('images/logoheader.png') no-repeat center center/cover;">
        <div class="container">
            <h1 class="display-4">WELCOME TO FOODZIE</h1>
            <p class="lead">Home Of The Authentic Filipino Dishes.</p>
            <a href="#products" class="btn btn-light btn-lg">Browse Products</a>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="products-container py-5">
        <div class="container">
            <h2 class="text-center mb-4">Our Products</h2>
            <div class="row">
                <?php if ($productResults && $productResults->num_rows > 0): ?>
                    <?php while ($row = $productResults->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="product card">
                                <img src="<?= htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['name']); ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($row['description']); ?></p>
                                    <p class="price">₱<?= number_format($row['price'], 2); ?></p>
                                    <button class="btn btn-primary add-to-cart" data-product-id="<?= $row['id']; ?>">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No products available.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2024 FOODZIE. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add product to cart
        $(document).on('click', '.add-to-cart', function () {
            const productId = $(this).data('product-id');
            $.post("index.php", { action: 'add_to_cart', product_id: productId }, function (response) {
                const data = JSON.parse(response);
                if (data.cart_count !== undefined) {
                    $('#cart-count').text(data.cart_count); // Update the cart count in the navbar
                }
            });
        });

        // Remove product from cart
        $(document).on('click', '.remove-from-cart', function () {
            const productId = $(this).data('product-id');
            $.post("index.php", { action: 'remove_from_cart', product_id: productId }, function (response) {
                const data = JSON.parse(response);
                if (data.cart_count !== undefined) {
                    $('#cart-count').text(data.cart_count); // Update the cart count in the navbar
                }
            });
        });
    </script>
</body>
</html>
