<?php
session_start();

// Include database connection
include('db.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if email and password are empty
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Both email and password are required.';
        header("Location: login.php");
        exit;
    }

    // Prepare SQL query to fetch user data from the database
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user is found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password using password_verify
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;  // Mark user as logged in

            // Redirect to the home page or dashboard
            header("Location: index.php");
            exit;
        } else {
            // Invalid password
            $_SESSION['error'] = 'Invalid email or password.';
            header("Location: login.php");
            exit;
        }
    } else {
        // No user found with that email
        $_SESSION['error'] = 'No user found with that email.';
        header("Location: login.php");
        exit;
    }
} else {
    // If not a POST request, redirect to login page
    header("Location: login.php");
    exit;
}
?>
