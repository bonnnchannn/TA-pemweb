<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE v4 | Product" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard" />
    <meta name="keywords" content="bootstrap 5, admin dashboard" />

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />

    <!-- Third Party Plugin(OverlayScrollbars) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" crossorigin="anonymous" />

    <!-- Third Party Plugin(Bootstrap Icons) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous" />

    <!-- Required Plugin(AdminLTE) -->
    <link rel="stylesheet" href="css/adminlte.css" />

    <!-- ApexCharts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" crossorigin="anonymous" />
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="index.php" class="nav-link">Home</a></li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <!-- Navbar Search -->
           
           
            <!-- User Menu Dropdown -->
            <li class="nav-item dropdown user-menu">
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <li class="user-footer">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                  <a href="#" class="btn btn-default btn-flat float-end">Sign out</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <a href="../user/index.html" class="brand-link">
      <img src="../assets_login/img/favicon.png" alt="" class="brand-image opacity-75 shadow" />
      <span class="brand-text fw-light">Sapu jagaD</span>
    </a>
  </div>
  <!--end::Sidebar Brand-->
  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        <li class="nav-item menu-open">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>
              Dashboard
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon bi bi-person"></i>
                <p>
                  User Management
                  <i class="nav-arrow bi bi-chevron-right"></i>
                </p>
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
            <p>
              Produk
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="./produk_list.php" class="nav-link active">
                <i class="nav-icon bi bi-circle"></i>
                <p>Product List</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./add_product.php" class="nav-link">
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
  <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->

      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Product Details</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Product</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <div class="app-content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Product Image</h3>
                  </div>
                  <div class="card-body">
                    <img src="https://via.placeholder.com/500" alt="Product Image" class="img-fluid rounded" />
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Product Information</h3>
                  </div>
                  <div class="card-body">
                    <h3>Product Name</h3>
                    <p class="text-muted">Category: Electronics</p>
                    <h4 class="text-success">$199.99</h4>
                    <p class="mt-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    <div class="mt-4">
                      <button class="btn btn-primary me-2">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                      </button>
                      <button class="btn btn-outline-secondary">
                        <i class="bi bi-heart"></i> Add to Wishlist
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12 connectedSortable">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Product Description</h3>
                  </div>
                  <div class="card-body">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    <p>Excepteur sint occaecat cupidatat non proident.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <strong>&copy; 2014-2024 <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.</strong> All rights reserved.
      </footer>
    </div>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/adminlte.js"></script>
  </body>
</html>
