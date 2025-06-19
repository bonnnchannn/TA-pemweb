<?php
// Start session
session_start();

// Include database connection
require_once 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah ada ID user yang diberikan melalui URL
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // pastikan integer

    // Cegah user menghapus dirinya sendiri
    if (isset($_SESSION['id']) && $_SESSION['id'] == $user_id) {
        header("Location: user.php?status=delete_failed&reason=self");
        exit();
    }

    // Query hapus user berdasarkan ID
    $sql = "DELETE FROM users WHERE id = ?";

    if ($stmt = $koneksi->prepare($sql)) {
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            // Sukses hapus
            header("Location: user.php?status=delete_success");
            exit();
        } else {
            echo "Error deleting user: " . $koneksi->error;
        }

        $stmt->close();
    } else {
        echo "Query preparation failed: " . $koneksi->error;
    }

} else {
    echo "No user ID provided!";
}
?>
