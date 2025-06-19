<?php
// Start the session
session_start();

// Include database connection file
require_once 'koneksi.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if 'id' parameter is passed in the URL
if (isset($_GET['id'])) {
    // Get the user id
    $user_id = $_GET['id'];

    // Create SQL query to delete the user based on the user_id
    $sql = "DELETE FROM users WHERE user_id = ?";

    // Prepare and bind the statement
    if ($stmt = $koneksi->prepare($sql)) {
        $stmt->bind_param("i", $user_id); // 'i' for integer

        // Execute the query
        if ($stmt->execute()) {
            // If the query is successful, redirect to the user list page
            header("Location: user.php");
            exit();
        } else {
            // If there's an error, display the error message
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: " . $koneksi->error;
    }
} else {
    // If no 'id' is passed in the URL, redirect to the user list page
    header("Location: user.php");
    exit();
}
?>
