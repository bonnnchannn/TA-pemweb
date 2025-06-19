<?php
session_start();

// Include database connection
require_once 'koneksi.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely retrieve POST data
    $username    = trim($_POST['username'] ?? '');
    $password    = trim($_POST['password'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $full_name   = trim($_POST['full_name'] ?? '');
    $user_level  = trim($_POST['user_level'] ?? '');
    $department  = trim($_POST['department'] ?? '');

    // Validate required fields
    if ($username === '' || $password === '' || $email === '' || $full_name === '' || $user_level === '') {
        $error = 'All fields are required!';
    }

    // Check for duplicate email or username
    if (!$error) {
        $dupStmt = $koneksi->prepare('SELECT email FROM users WHERE email = ? OR username = ?');
        $dupStmt->bind_param('ss', $email, $username);
        $dupStmt->execute();
        $dupStmt->store_result();
        if ($dupStmt->num_rows > 0) {
            $error = 'Email or Username already exists!';
        }
        $dupStmt->close();
    }

    // Insert into database if no errors
    if (!$error) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare insert statement - Including user_level (department optional for now)
        $insertStmt = $koneksi->prepare(
            'INSERT INTO users (username, full_name, email, password, user_level) VALUES (?, ?, ?, ?, ?)'
        );
        $insertStmt->bind_param('sssss', $username, $full_name, $email, $hashedPassword, $user_level);

        // Execute and redirect or capture error
        if ($insertStmt->execute()) {
            header('Location: user.php');
            exit();
        } else {
            $error = 'Database error: ' . $insertStmt->error;
        }
        $insertStmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Add User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE v4 | Add User" />

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
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-footer">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                                <a href="logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="./index.html" class="brand-link">
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
                                            <a href="add_user.php" class="nav-link active">
                                                <i class="nav-icon bi bi-circle"></i>
                                                <p>Add User</p>
                                            </a>
                                        </li>
                                    </ul>
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
                        <div class="col-sm-6"><h3 class="mb-0">Add User</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Add User</li>
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
                                    <h3 class="card-title">User Information</h3>
                                </div>
                                <div class="card-body">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo htmlspecialchars($error); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="add_user.php">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" 
                                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="full_name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="user_level" class="form-label">User Level</label>
                                            <select class="form-control" id="user_level" name="user_level" required>
                                                <option value="">Select User Level</option>
                                                <option value="admin" <?php echo (isset($_POST['user_level']) && $_POST['user_level'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                <option value="manager" <?php echo (isset($_POST['user_level']) && $_POST['user_level'] === 'manager') ? 'selected' : ''; ?>>Manager</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Add User</button>
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