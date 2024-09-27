<?php
include '../Backend/connect.php';

$error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Username and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT ID, user_name, password FROM user_tb WHERE user_name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['username'] = $user['user_name'];
                header("Location: ../Frontend/dashboard.php");
                exit();
            } else {
                $_SESSION['login_error'] = "Invalid password.";
            }
        } else {
            $_SESSION['login_error'] = "No user found with that username.";
        }

        $stmt->close();
    }

    // Close connection
    $conn->close();
    header("Location: ../Frontend/index.php");
    exit();
}
