<?php
session_start(); // Start the session
include "../Backend/connect.php"; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $post_vars);
    $name = $post_vars['name']; // Get the name from the parsed variables

    $sql = "DELETE FROM brgy_bangkal_record_census_final WHERE NAME = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        // Log the error if preparation fails
        error_log("Prepare failed: " . $conn->error);
        $_SESSION['error_message'] = "Failed to prepare statement.";
        echo json_encode(["success" => false, "message" => "Failed to prepare statement."]);
        exit();
    }

    $stmt->bind_param("s", $name); // Bind the name parameter

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Data deleted successfully!";
        echo json_encode(["success" => true, "message" => "Data deleted successfully!"]);
    } else {
        // Log the error if execution fails
        error_log("Execute failed: " . $stmt->error);
        $_SESSION['error_message'] = "Failed to delete data.";
        echo json_encode(["success" => false, "message" => "Failed to delete data: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]); // Handle if not DELETE
}

$conn->close();
