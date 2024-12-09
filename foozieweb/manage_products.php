<?php
session_start();
include('db_connect.php');  // Include your DB connection file

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = $_POST['product_name'];
    $description = $_POST['product_description'];
    $price = $_POST['product_price'];
    $image = $_FILES['product_image']['name'];

    // Validate image file type (Allow only specific image formats)
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    if (in_array($file_extension, $allowed_extensions)) {
        // Move uploaded image to the correct folder
        $target_dir = "images/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file);

        // Insert product into the database using prepared statements
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $description, $price, $image);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "<script>alert('Invalid image type. Only JPG, JPEG, PNG, and GIF files are allowed.');</script>";
    }
}

// Handle Delete Product
if (isset($_GET['delete_product_id'])) {
    $product_id = $_GET['delete_product_id'];

    // Delete product using prepared statement
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
}

// Get all products from the database
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Product Management</h2>

        <!-- Add Product Form -->
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" name="product_name" required>
            </div>
            <div class="mb-3">
                <label for="product_description" class="form-label">Product Description</label>
                <textarea class="form-control" name="product_description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="product_price" class="form-label">Price</label>
                <input type="number" class="form-control" name="product_price" required>
            </div>
            <div class="mb-3">
                <label for="product_image" class="form-label">Product Image</label>
                <input type="file" class="form-control" name="product_image" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_product">Add Product</button>
        </form>

        <!-- Display Products -->
        <h3 class="mt-5">Current Products</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $products->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>₱<?php echo htmlspecialchars($row['price']); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="?delete_product_id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
