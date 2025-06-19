<?php
// Start the session
session_start();

// Include database connection file
require_once 'koneksi.php'; // Pastikan file ini ada dan membuat koneksi $koneksi

// Ensure the user is logged in
// Ini adalah pengecekan sederhana, Anda mungkin ingin menambahkan otorisasi peran di sini
// Contoh: if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'ADMIN') { ... }
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from the database
// Menggunakan prepared statement meskipun ini query SELECT sederhana tanpa input user
// untuk konsistensi dan kebiasaan baik.
$users = []; // Inisialisasi array untuk menyimpan data user
$stmt = $koneksi->prepare("SELECT id, username, nama, email, role FROM users ORDER BY username ASC");

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $result->free(); // Bebaskan hasil set
    }
    $stmt->close(); // Tutup statement
} else {
    // Handle error jika prepared statement gagal
    error_log("Error preparing statement: " . $koneksi->error);
    // Anda bisa menampilkan pesan error yang lebih user-friendly di sini
}

// Tutup koneksi database
mysqli_close($koneksi);
?>


<!doctype html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | User List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE v4 | User List" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS." />
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard" />
    
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="css/adminlte.css" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
      integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
      integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4="
      crossorigin="anonymous"
    />
  </head>
  
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
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
            <li class="nav-item dropdown user-menu">
              <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown user-menu">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                            <i class="bi bi-person-circle"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-footer">
                                <a href="../user/index.html" class="btn btn-default btn-flat">Profile</a>
                                <a href="logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            </ul>
          </div>
      </nav>
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
          <a href="../user/index.html" class="brand-link">
            <img
              src="../assets_login/img/favicon.png"
              alt=""
              class="brand-image opacity-75 shadow"
            />
            <span class="brand-text fw-light">Sapu jagaD</span>
            </a>
          </div>
       <div class="sidebar-wrapper">
          <nav class="mt-2">
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="menu"
              data-accordion="false">
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
                    <a href="./user.php" class="nav-link active">
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
                    <a href="../pages/add_user.php" class="nav-link">
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
                    <a href="../pages/" class="nav-link">
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
      <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">User List</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">User List</li>
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
                    <h3 class="card-title">User Table</h3>
                    <div class="card-tools">
                      <a href="add_user.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus"></i> Add New User
                      </a>
                    </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Username</th>
                          <th>Full Name</th>
                          <th>Email</th>
                          <th>User Level</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no = 1;
                        if (!empty($users)) { // Check if $users array is not empty
                          foreach ($users as $user) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>" . htmlspecialchars($user['username']) . "</td>
                                    <td>" . htmlspecialchars($user['nama']) . "</td>
                                    <td>" . htmlspecialchars($user['email']) . "</td>
                                    <td>
                                      <span class='badge ";
                                      // Logic to determine badge color based on role
                                      switch (strtoupper($user['role'])) {
                                          case 'ADMIN':
                                              echo 'text-bg-danger'; // Or any color you prefer for admin
                                              break;
                                          case 'MANAGER':
                                              echo 'text-bg-warning';
                                              break;
                                          case 'PEGAWAI':
                                              echo 'text-bg-info';
                                              break;
                                          case 'USER':
                                              echo 'text-bg-primary';
                                              break;
                                          default:
                                              echo 'text-bg-secondary'; // Default color for unknown roles
                                              break;
                                      }
                                      echo "'>
                                        " . htmlspecialchars($user['role']) . "
                                      </span>
                                    </td>
                                    <td>
                                      <a href='edit_user.php?id=" . htmlspecialchars($user['id']) . "' class='btn btn-warning btn-sm' title='Edit User'>
                                        <i class='bi bi-pencil'></i>
                                      </a>
                                      <a href='delete_user.php?id=" . htmlspecialchars($user['id']) . "' class='btn btn-danger btn-sm' 
                                         onclick='return confirm(\"Are you sure you want to delete this user?\")' title='Delete User'>
                                        <i class='bi bi-trash'></i>
                                      </a>
                                    </td>
                                  </tr>";
                            $no++;
                          }
                        } else {
                          echo "<tr><td colspan='6' class='text-center'>No users found</td></tr>";
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
      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <strong>
          Copyright &copy; 2014-<?= date('Y') ?>&nbsp; <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        All rights reserved.
        </footer>
      </div>
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZfxSB1/Rf9WtqRHgG5S0="
      crossorigin="anonymous"
    ></script>
    <script src="js/adminlte.js"></script>
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <script
      src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
      integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ="
      crossorigin="anonymous"
    ></script>
    <script>
      const connectedSortables = document.querySelectorAll('.connectedSortable');
      connectedSortables.forEach((connectedSortable) => {
        let sortable = new Sortable(connectedSortable, {
          group: 'shared',
          handle: '.card-header',
        });
      });

      const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
      cardHeaders.forEach((cardHeader) => {
        cardHeader.style.cursor = 'move';
      });
    </script>
    </body>
</html>