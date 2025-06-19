<?php
// Start session
session_start();

// Include database connection file
require_once 'koneksi.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    // Get the product_id from the URL
    $product_id = $_GET['id'];

    // Create DELETE SQL query
    $sql = "DELETE FROM produk WHERE product_id = ?";

    // Prepare and bind the statement
    if ($stmt = $koneksi->prepare($sql)) {
        $stmt->bind_param("i", $product_id); // 'i' means integer (product_id is an integer)
        
        // Execute the query
        if ($stmt->execute()) {
            // If deletion is successful, redirect to the product list page
            header("Location: produk_list.php");
            exit();
        } else {
            echo "Error deleting product: " . $koneksi->error;
        }

        // Close the statement
        $stmt->close();
    }
} else {
    echo "No product ID provided!";
}
?>
