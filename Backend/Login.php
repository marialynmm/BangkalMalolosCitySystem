<?php
// Include database connection
include "connect.php";

// Start session
session_start();

// Initialize error message variable
$error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from POST request
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } else {
        // Prepare SQL query to fetch user details
        $stmt = $conn->prepare("SELECT user_id, username, password FROM user_tb WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verify plain text password
            if ($password === $user['password']) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];

                // Redirect to a protected page (e.g., dashboard)
                header("Location: ../Frontend/dashboard.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "No user found with that username.";
        }

        // Close statement
        $stmt->close();
    }

    // Store error message in session
    $_SESSION['login_error'] = $error_message;

    // Close connection
    $conn->close();

    // Redirect back to the login page
    header("Location: ../Frontend/index.php");
    exit();
}
?>
