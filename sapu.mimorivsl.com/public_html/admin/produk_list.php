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

// Fetch product data from the database
$sql = "SELECT * FROM produk"; // Adjust this query based on your table structure
$result = $koneksi->query($sql);
?>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Product List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE v4 | Product List" />
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

    <!-- ApexCharts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" crossorigin="anonymous" />
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
                        <div class="col-sm-6"><h3 class="mb-0">Product List</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Product List</li>
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
                                    <h3 class="card-title">Product Table</h3>
                                    <div class="card-tools">
                                        <a href="add_product.php" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add New Product</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        if ($result && $result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                // Format price with Rp and thousands separator
                $formatted_price = "Rp " . number_format($product['price'], 0, ',', '.');
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$product['product_name']}</td>
                        <td>{$product['category']}</td>
                        <td>{$formatted_price}</td>
                        <td>{$product['stock']}</td>
                        <td>
                            <a href='edit_product.php?id={$product['product_id']}' class='btn btn-warning btn-sm' title='Edit Product'>
                                <i class='bi bi-pencil'></i>
                            </a>
                            <a href='delete_product.php?id={$product['product_id']}' class='btn btn-danger btn-sm' 
                               onclick='return confirm(\"Are you sure you want to delete this product?\")' title='Delete Product'>
                                <i class='bi bi-trash'></i>
                            </a>
                        </td>
                      </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>No products found</td></tr>";
        }
        ?>
    </tbody>
</table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <strong>&copy; 2014-2024 <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>. All rights reserved.</strong>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/adminlte.js"></script>
</body>
</html>
