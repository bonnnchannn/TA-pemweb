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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // Validate the inputs (basic validation)
    if (empty($product_name) || empty($category) || empty($price) || empty($stock) || empty($description)) {
        echo "All fields are required!";
    } else {
        // Create the SQL query to insert the new product into the database
        $sql = "INSERT INTO produk (product_name, category, price, stock, description) VALUES (?, ?, ?, ?, ?)";

        // Prepare and bind the statement
        if ($stmt = $koneksi->prepare($sql)) {
            $stmt->bind_param("ssdis", $product_name, $category, $price, $stock, $description); // 's' for string, 'i' for integer, 'd' for double

            // Execute the query
            if ($stmt->execute()) {
                // If insertion is successful, redirect to the product list page
                header("Location: produk_list.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error: " . $koneksi->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Add Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE v4 | Add Product" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard" />
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard" />

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />

    <!-- Third Party Plugin (OverlayScrollbars) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" crossorigin="anonymous" />

    <!-- Third Party Plugin (Bootstrap Icons) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous" />

    <!-- Required Plugin (AdminLTE) -->
    <link rel="stylesheet" href="css/adminlte.css" />
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Header -->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block"><a href="./index.php" class="nav-link">Home</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <!-- User Menu Dropdown -->
                    <li class="nav-item dropdown user-menu">
                <ul class="navbar-nav ms-auto">
                    <!-- Fullscreen Toggle -->
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                        </a>
                    </li>
                    <!-- User Menu -->
                    <li class="nav-item dropdown user-menu">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                            <i class="bi bi-person-circle"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-footer">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                                <a href="logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="../user/index.html" class="brand-link">
                    <img src="../assets_login/img/favicon.png" alt="" class="brand-image opacity-75 shadow" />
                    <span class="brand-text fw-light">Sapu jagaD</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                        <li class="nav-item menu-open">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-person"></i>
                                        <p>User Management</p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="user.php" class="nav-link">
                                                <i class="nav-icon bi bi-circle"></i>
                                                <p>User List</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="user_roles.php" class="nav-link">
                                                <i class="nav-icon bi bi-circle"></i>
                                                <p>User Roles</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="add_user.php" class="nav-link">
                                                <i class="nav-icon bi bi-circle"></i>
                                                <p>Add User</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-box-seam-fill"></i>
                                <p>Produk</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="produk_list.php" class="nav-link active">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Product List</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="add_product.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Add Product</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="product_categories.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Categories</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="product_stock.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Stock Management</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Add Product</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Add Product</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 connectedSortable">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Product Information</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="add_product.php">
                                        <div class="mb-3">
                                            <label for="product_name" class="form-label">Product Name</label>
                                            <input type="text" class="form-control" id="product_name" name="product_name" required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <input type="text" class="form-control" id="category" name="category" required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control" id="price" name="price" required />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Stock</label>
                                            <input type="number" class="form-control" id="stock" name="stock" required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Add Product</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/adminlte.js"></script>
</body>
</html>
