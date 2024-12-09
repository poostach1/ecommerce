<?php
// Start a session to store user data later
session_start();

// Include database connection
include('db.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate input (ensure no fields are empty)
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'All fields are required.';
        header("Location: signup.php");
        exit;
    }

    // Check if email already exists in the database
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists
        $_SESSION['error'] = 'Email is already registered.';
        header("Location: signup.php");
        exit;
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL query to insert new user into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    // Execute the query
    if ($stmt->execute()) {
        // Successful signup, log the user in by setting session variables
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['logged_in'] = true;

        // Redirect to home page or dashboard
        header("Location: index.php");
        exit;
    } else {
        // Something went wrong with database insertion
        $_SESSION['error'] = 'Something went wrong, please try again.';
        header("Location: signup.php");
        exit;
    }
} else {
    // If not a POST request, redirect to signup page
    header("Location: signup.php");
    exit;
}
?>
