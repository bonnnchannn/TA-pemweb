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

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: user.php");
    exit();
}

$user_id = $_GET['id'];
$message = '';
$error = '';

// Handle form submission
if ($_POST) {
    $full_name = trim($_POST['full_name']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validation
    if (empty($full_name)) {
        $error = "Full name is required.";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Update user data
        if (!empty($password)) {
            // Update with new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET full_name = ?, password = ? WHERE user_id = ?";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("ssi", $full_name, $hashed_password, $user_id);
        } else {
            // Update without changing password
            $sql = "UPDATE users SET full_name = ? WHERE user_id = ?";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("si", $full_name, $user_id);
        }
        
        if ($stmt->execute()) {
            $message = "User updated successfully!";
        } else {
            $error = "Error updating user: " . $koneksi->error;
        }
        $stmt->close();
    }
}

// Fetch user data
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: user.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Edit User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE v4 | Edit User" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS." />
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard" />
    
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
  </head>
  
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="./index.php" class="nav-link">Home</a></li>
          </ul>
          <!--end::Start Navbar Links-->
          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::User Menu Dropdown-->
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
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
      </nav>
      <!--end::Header-->
        
      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="./index.html" class="brand-link">
            <!--begin::Brand Image-->
            <img
              src="../assets_login/img/favicon.png"
              alt=""
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">Sapu jagaD</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
       <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
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
                    <a href="./produk_list.php" class="nav-link">
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
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->

      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Edit User</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item"><a href="user.php">User List</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->

        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <!--begin::Col-->
              <div class="col-lg-8">
                <!--begin::Edit User Card-->
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Edit User: <?php echo htmlspecialchars($user['username']); ?></h3>
                    <div class="card-tools">
                      <a href="user.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to User List
                      </a>
                    </div>
                  </div>
                  <div class="card-body">
                    <?php if ($message): ?>
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                            <div class="form-text">Username cannot be changed</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            <div class="form-text">Email cannot be changed</div>
                          </div>
                        </div>
                      </div>

                      <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                      </div>

                      <div class="mb-3">
                        <label for="user_level" class="form-label">User Level</label>
                        <input type="text" class="form-control" id="user_level" name="user_level" 
                               value="<?php echo htmlspecialchars($user['user_level']); ?>" readonly>
                        <div class="form-text">User level cannot be changed</div>
                      </div>

                      <hr>
                      <h5>Change Password (Optional)</h5>
                      <p class="text-muted">Leave password fields empty if you don't want to change the password</p>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter new password (min 6 characters)">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Confirm new password">
                          </div>
                        </div>
                      </div>

                      <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="user.php" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                          <i class="bi bi-check-lg"></i> Update User
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
                <!--end::Edit User Card-->
              </div>
              <!--end::Col-->

              <!--begin::Info Col-->
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">User Information</h3>
                  </div>
                  <div class="card-body">
                    <table class="table table-sm">
                      <tr>
                        <td><strong>User ID:</strong></td>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                      </tr>
                      <tr>
                        <td><strong>Username:</strong></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                      </tr>
                      <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                      </tr>
                      <tr>
                        <td><strong>User Level:</strong></td>
                        <td>
                          <span class="badge <?php echo $user['user_level'] == 'admin' ? 'text-bg-success' : 'text-bg-primary'; ?>">
                            <?php echo htmlspecialchars($user['user_level']); ?>
                          </span>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>

                <div class="card mt-3">
                  <div class="card-header">
                    <h3 class="card-title">Tips</h3>
                  </div>
                  <div class="card-body">
                    <ul class="list-unstyled">
                      <li><i class="bi bi-info-circle text-info"></i> Only Full Name and Password can be edited</li>
                      <li><i class="bi bi-shield-lock text-warning"></i> Password must be at least 6 characters</li>
                      <li><i class="bi bi-eye-slash text-secondary"></i> Leave password empty to keep current password</li>
                    </ul>
                  </div>
                </div>
              </div>
              <!--end::Info Col-->
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->

      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2014-2024&nbsp;
          <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->
    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZfxSB1/Rf9WtqRHgG5S0="
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->
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
    <!--end::OverlayScrollbars Configure-->

    <!-- Password Confirmation Validation -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        const form = document.querySelector('form');

        function validatePasswords() {
          const password = passwordField.value;
          const confirmPassword = confirmPasswordField.value;

          if (password !== '' || confirmPassword !== '') {
            if (password !== confirmPassword) {
              confirmPasswordField.setCustomValidity('Passwords do not match');
              confirmPasswordField.classList.add('is-invalid');
            } else if (password.length < 6) {
              passwordField.setCustomValidity('Password must be at least 6 characters long');
              passwordField.classList.add('is-invalid');
            } else {
              passwordField.setCustomValidity('');
              confirmPasswordField.setCustomValidity('');
              passwordField.classList.remove('is-invalid');
              confirmPasswordField.classList.remove('is-invalid');
              passwordField.classList.add('is-valid');
              confirmPasswordField.classList.add('is-valid');
            }
          } else {
            passwordField.setCustomValidity('');
            confirmPasswordField.setCustomValidity('');
            passwordField.classList.remove('is-invalid', 'is-valid');
            confirmPasswordField.classList.remove('is-invalid', 'is-valid');
          }
        }

        passwordField.addEventListener('input', validatePasswords);
        confirmPasswordField.addEventListener('input', validatePasswords);

        form.addEventListener('submit', function(e) {
          validatePasswords();
          if (!passwordField.checkValidity() || !confirmPasswordField.checkValidity()) {
            e.preventDefault();
          }
        });
      });
    </script>
    <!--end::Script-->
  </body>
</html>