<?php
session_start();
include('db_connect.php');

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all orders
$orders = mysqli_query($conn, "SELECT * FROM orders");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Order Management</h2>

        <h3 class="mt-5">Current Orders</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($orders)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <a href="view_order_details.php?id=<?php echo $row['id']; ?>" class="btn btn-info">View</a>
                        <a href="mark_as_completed.php?id=<?php echo $row['id']; ?>" class="btn btn-success">Mark as Completed</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
