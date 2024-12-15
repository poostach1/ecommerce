<?php
session_start();
include('db.php');

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch order details
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Fetch the main order information with prepared statements
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
    $order = $order_result->fetch_assoc();
    $stmt->close();

    // Fetch the items in the order (if you have an order_items table) with prepared statements
    $items_stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    $items_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Order Details (ID: <?= htmlspecialchars($order['id']); ?>)</h2>
        
        <!-- Order Information -->
        <h4 class="mt-4">Order Information</h4>
        <p><strong>User ID:</strong> <?= htmlspecialchars($order['user_id']); ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']); ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']); ?></p>

        <!-- Order Items -->
        <h4 class="mt-5">Ordered Products</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']); ?></td>
                    <td><?= htmlspecialchars($item['quantity']); ?></td>
                    <td>₱<?= number_format($item['price'], 2); ?></td>
                    <td>₱<?= number_format($item['total_price'], 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
