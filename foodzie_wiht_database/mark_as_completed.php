<?php
session_start();
include('db.php');

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Update order status to 'Completed'
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Update the order status
    $update_status = mysqli_query($conn, "UPDATE orders SET status = 'Completed' WHERE id = $order_id");

    if ($update_status) {
        header("Location: view_orders.php");  // Redirect back to orders page
        exit();
    } else {
        echo "<script>alert('Failed to update order status');</script>";
    }
}
?>
