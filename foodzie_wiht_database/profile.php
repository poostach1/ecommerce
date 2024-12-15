<?php
session_start();
include('db.php');

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's information from the database
$username = $_SESSION['user'];
$userQuery = $conn->query("SELECT * FROM users WHERE username = '$username'");
$user = $userQuery->fetch_assoc();

// Handle profile picture update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["profile_picture"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is a valid image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
            // Update profile picture in the database
            $conn->query("UPDATE users SET profile_picture = '$targetFile' WHERE username = '$username'");
            header("Location: profile.php");
            exit();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "File is not an image.";
    }
}

// Handle profile information update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $fullName = htmlspecialchars($_POST['full_name']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $email = htmlspecialchars($_POST['email']);
    
    // Update user's information
    $conn->query("UPDATE users SET full_name = '$fullName', phone = '$phone', address = '$address', email = '$email' WHERE username = '$username'");
    header("Location: profile.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - FOODZIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                    <a class="nav-link" href="cart.php">
                        <i class="fa fa-shopping-cart"></i> Cart (<span id="cart-count"><?= array_sum($_SESSION['cart']); ?></span>)
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Profile Section -->
    <section class="profile-section py-5">
        <div class="container">
            <h2 class="text-center mb-4">Profile</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <!-- Profile Picture -->
                            <img src="<?= !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'default-profile.png'; ?>" 
                                 alt="Profile Picture" class="rounded-circle mb-3" width="150" height="150">
                            <form action="profile.php" method="POST" enctype="multipart/form-data">
                                <input type="file" name="profile_picture" class="form-control mb-3" accept="image/*">
                                <button type="submit" class="btn btn-primary">Change Profile Picture</button>
                            </form>

                            <h5 class="card-title mt-3"><?= htmlspecialchars($user['username']); ?></h5>
                            <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
                            <p class="card-text"><strong>Full Name:</strong> <?= htmlspecialchars($user['full_name']); ?></p>
                            <p class="card-text"><strong>Phone:</strong> <?= htmlspecialchars($user['phone']); ?></p>
                            <p class="card-text"><strong>Address:</strong> <?= htmlspecialchars($user['address']); ?></p>

                            <!-- Update Profile Form -->
                            <form action="profile.php" method="POST">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <button type="submit" name="update" class="btn btn-success">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2024 FOODZIE. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
