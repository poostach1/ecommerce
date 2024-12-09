<?php
session_start();

// Prevent browser cache (aggressively prevent caching)
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header("Cache-Control: private, no-store, max-age=0, must-revalidate");

// Database connection (replace with your credentials)
$dsn = 'mysql:host=localhost;dbname=foodzie';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Ensure cart is unique per user by checking session user ID
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize user cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Clean up the cart by removing invalid products
function cleanCart($pdo)
{
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return;
    }

    $cart = $_SESSION['cart'];
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id IN (" . implode(",", array_keys($cart)) . ")");
    $stmt->execute();
    $validProductIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $_SESSION['cart'] = array_filter($cart, function ($productId) use ($validProductIds) {
        return in_array($productId, $validProductIds);
    }, ARRAY_FILTER_USE_KEY);
}

// Clean the cart
cleanCart($pdo);

// Handle "Remove from Cart" request
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);

    // Return updated cart count
    echo json_encode(['cart_count' => array_sum($_SESSION['cart'])]);
    exit();
}

// Handle "Update Cart" request via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_cart') {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        $_SESSION['cart'][$product_id] = $quantity;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }

    // Recalculate the total price
    $totalPrice = 0;
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product) {
            $totalPrice += $product['price'] * $qty;
        }
    }

    // Return updated cart count and total price
    echo json_encode([
        'cart_count' => array_sum($_SESSION['cart']),
        'total_price' => number_format($totalPrice, 2)
    ]);
    exit();
}

// Fetch products based on cart items
function getProducts($pdo, $cart)
{
    if (empty($cart)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart));
    return $stmt->fetchAll();
}

// Get products in the cart
$cart = $_SESSION['cart'] ?? [];
$products = getProducts($pdo, $cart);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FOODZIE Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta http-equiv="cache-control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
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
                        <li class="nav-item"><a class="nav-link" href="#">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="checkout.php">Checkout</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">About Us</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <!-- Cart Section -->
    <section id="cart" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Your Cart</h2>
            <?php if (empty($products)): ?>
                <p class="text-center">Your cart is empty.</p>
            <?php else: ?>
                <div class="row">
                    <?php
                    $totalPrice = 0;
                    foreach ($products as $product):
                        $product_id = $product['id'];
                        $quantity = $cart[$product_id];
                        $subtotal = $product['price'] * $quantity;
                        $totalPrice += $subtotal;
                    ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="price">₱<?= number_format($product['price'], 2) ?></p>
                                    <form method="POST" class="d-flex align-items-center update-cart-form">
                                        <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                        <input type="number" name="quantity" value="<?= $quantity ?>" min="1" class="form-control me-2" style="width: 80px;">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                    <a href="?remove=<?= $product_id ?>" class="btn btn-danger mt-2 remove-from-cart" data-product-id="<?= $product_id ?>">Remove</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <h4>Total: ₱<span id="total-price"><?= number_format($totalPrice, 2) ?></span></h4>
                    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2024 FOODZIE. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Handle updating cart quantity via AJAX
        $(document).on('submit', '.update-cart-form', function (e) {
            e.preventDefault();
            const form = $(this);
            const productId = form.find('input[name="product_id"]').val();
            const quantity = form.find('input[name="quantity"]').val();

            $.post("cart.php", {
                action: 'update_cart',
                product_id: productId,
                quantity: quantity
            }, function (response) {
                const data = JSON.parse(response);
                if (data.cart_count !== undefined) {
                    $('#cart-count').text(data.cart_count); // Update cart count in navbar
                }
                if (data.total_price !== undefined) {
                    $('#total-price').text(data.total_price); // Update total price dynamically
                }
            });
        });

        // Handle removing item from cart without page refresh
        $(document).on('click', '.remove-from-cart', function (e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            $.get("cart.php?remove=" + productId, function (response) {
                const data = JSON.parse(response);
                $('#cart-count').text(data.cart_count); // Update cart count in navbar
                location.reload(); // Refresh the page to update cart
            });
        });
    </script>
</body>
</html>
