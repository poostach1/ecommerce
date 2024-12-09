<?php
session_start(); // Start the session

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    // Unset session variables to log the user out
    unset($_SESSION['user']);
    unset($_SESSION['user_id']); // If you store user ID in session
    unset($_SESSION['cart']); // Clear the cart for the user

    // Destroy the session to fully clear the user data
    session_destroy();

    // Redirect to login page after successful logout
    header("Location: login.php");
    exit();
} else {
    // If no user is logged in, redirect to the login page
    header("Location: login.php");
    exit();
}
?>
