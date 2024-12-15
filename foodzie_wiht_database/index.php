<?php
session_start();
include('db.php');

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

// Handle "Add to Cart" request via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $product_id = intval($_POST['product_id']); // Validate product_id

    // Check if product exists in the database
    $productQuery = $conn->query("SELECT * FROM products WHERE id = $product_id");
    if ($productQuery && $productQuery->num_rows > 0) {
        // Check if the product is already in the user's cart
        $checkCartQuery = $conn->prepare("SELECT * FROM carts WHERE user_id = ? AND product_id = ?");
        $checkCartQuery->bind_param("ii", $user_id, $product_id);
        $checkCartQuery->execute();
        $cartResult = $checkCartQuery->get_result();

        if ($cartResult->num_rows > 0) {
            // Product already in cart, update quantity
            $updateQuery = $conn->prepare("UPDATE carts SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
            $updateQuery->bind_param("ii", $user_id, $product_id);
            $updateQuery->execute();
        } else {
            // Product not in cart, insert a new entry
            $insertQuery = $conn->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $insertQuery->bind_param("ii", $user_id, $product_id);
            $insertQuery->execute();
        }
    }

    // Return updated cart count
    echo json_encode(['cart_count' => get_cart_count($user_id)]);
    exit();
}

// Fetch all products from the database
$productResults = $conn->query("SELECT * FROM products");

// Function to get cart count dynamically from the database
function get_cart_count($user_id) {
    global $conn;
    $cartQuery = $conn->prepare("SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?");
    $cartQuery->bind_param("i", $user_id);
    $cartQuery->execute();
    $cartResult = $cartQuery->get_result();
    $cartData = $cartResult->fetch_assoc();
    return $cartData['total_quantity'] ?? 0; // Return total cart count or 0 if empty
}
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
    <style>
        .floating-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 1000;
        }

        .floating-cart span {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: red;
            color: white;
            font-size: 12px;
            font-weight: bold;
            border-radius: 50%;
            padding: 2px 6px;
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
                        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
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
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
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
            <div class="row g-3">
                <?php if ($productResults && $productResults->num_rows > 0): ?>
                    <?php while ($row = $productResults->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="product card h-100">
                                <img src="<?= htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']); ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($row['name']); ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($row['description']); ?></p>
                                    <p class="price">₱<?= number_format($row['price'], 2); ?></p>
                                    <button class="btn btn-primary mt-auto add-to-cart" data-product-id="<?= $row['id']; ?>">Add to Cart</button>
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

    <!-- Floating Cart Button -->
    <div class="floating-cart" onclick="window.location.href='cart.php'">
        <i class="fa fa-shopping-cart fa-lg"></i>
        <span id="floating-cart-count"><?= get_cart_count($user_id); ?></span> <!-- Initial cart count -->
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2024 FOODZIE. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to update cart count dynamically
        function updateCartCount() {
            $.post("index.php", { action: 'get_cart_count' }, function(response) {
                const data = JSON.parse(response);
                if (data.cart_count !== undefined) {
                    $('#floating-cart-count').text(data.cart_count); // Update the cart count
                }
            });
        }

        // Add product to cart
        $(document).on('click', '.add-to-cart', function () {
            const productId = $(this).data('product-id');
            $.post("index.php", { action: 'add_to_cart', product_id: productId }, function (response) {
                const data = JSON.parse(response);
                if (data.cart_count !== undefined) {
                    $('#floating-cart-count').text(data.cart_count); // Update the cart count in the floating cart
                }
            });
        });

        // Update cart count when page loads
        $(document).ready(function() {
            updateCartCount();
        });
    </script>
</body>
</html>