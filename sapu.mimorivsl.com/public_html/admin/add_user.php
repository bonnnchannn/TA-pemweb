<?php
session_start();

// Include database connection
require_once 'koneksi.php'; // Ensure this file correctly establishes $koneksi

// Ensure the user is logged in
// You might want to add role-based authorization here, e.g., only admins can add users
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$error = '';

// Fetch roles and offices for dropdowns
$roles = [];
$offices = [];

try {
    // Fetch roles
    $result_roles = $koneksi->query("SELECT role_code, role_name FROM role ORDER BY role_name ASC");
    if ($result_roles) {
        while ($row = $result_roles->fetch_assoc()) {
            $roles[] = $row;
        }
        $result_roles->free();
    }

    // Fetch offices
    $result_offices = $koneksi->query("SELECT office_code, office_name FROM office ORDER BY office_name ASC");
    if ($result_offices) {
        while ($row = $result_offices->fetch_assoc()) {
            $offices[] = $row;
        }
        $result_offices->free();
    }

} catch (Exception $e) {
    error_log('Error fetching dropdown data in add_user.php: ' . $e->getMessage());
    $error = 'Failed to load role and office data. Please try again later.';
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely retrieve POST data
    $username     = trim($_POST['username'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $nama         = trim($_POST['nama'] ?? ''); // Corrected from full_name
    $role         = trim($_POST['role'] ?? ''); // Corrected from user_level
    $office       = trim($_POST['office'] ?? ''); // Corrected from department, can be empty string

    // Validate required fields
    if (empty($username) || empty($password) || empty($email) || empty($nama) || empty($role)) {
        $error = 'All mandatory fields are required (Username, Password, Email, Full Name, Role)!';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'Username must be between 3 and 50 characters.';
    } elseif (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
        $error = 'Username can only contain letters, numbers, and underscores.';
    } elseif (strlen($nama) < 2 || strlen($nama) > 100) {
        $error = 'Full Name must be between 2 and 100 characters.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($email) > 100) {
        $error = 'Email is too long.';
    }

    // Validate selected role against database roles or fallback list
    $role_valid = false;
    if (!empty($roles)) {
        foreach ($roles as $r) {
            if (strtoupper($r['role_code']) === strtoupper($role)) {
                $role_valid = true;
                break;
            }
        }
    } else {
        // Fallback for role validation if roles couldn't be fetched from DB
        $valid_roles_fallback = ['ADMIN', 'MANAGER', 'PEGAWAI', 'USER'];
        if (in_array(strtoupper($role), $valid_roles_fallback)) {
            $role_valid = true;
        }
    }
    if (!$role_valid) {
        $error = 'Invalid User Role selected!';
    }

    // Validate selected office against database offices if provided
    $office_code_for_db = null;
    if (empty($error) && !empty($office)) {
        $office_valid = false;
        foreach ($offices as $o) {
            if ($o['office_code'] === $office) {
                $office_valid = true;
                break;
            }
        }
        if (!$office_valid) {
            $error = 'Invalid Office selected!';
        } else {
            $office_code_for_db = $office; // Set if valid
        }
    }

    // Check for duplicate email or username
    if (!$error) {
        $stmt_check_duplicate = $koneksi->prepare('SELECT email FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(username) = LOWER(?)');
        $email_lower = strtolower($email);
        $username_lower = strtolower($username);
        $stmt_check_duplicate->bind_param('ss', $email_lower, $username_lower);
        $stmt_check_duplicate->execute();
        $stmt_check_duplicate->store_result();
        if ($stmt_check_duplicate->num_rows > 0) {
            $error = 'Email or Username already exists!';
        }
        $stmt_check_duplicate->close();
    }

    // Insert into database if no errors
    if (!$error) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare insert statement - Corrected column names
        // Note: 'gaji' column is removed based on your last confirmation
        $insertStmt = $koneksi->prepare(
            'INSERT INTO users (username, nama, email, password, role, office) VALUES (?, ?, ?, ?, ?, ?)'
        );
        // Bind parameters: 6 's' for 6 string columns
        $insertStmt->bind_param('ssssss', $username, $nama, $email, $hashedPassword, $role, $office_code_for_db);

        // Execute and redirect or capture error
        if ($insertStmt->execute()) {
            header('Location: user.php?status=success'); // Add a success status for feedback
            exit();
        } else {
            $error = 'Database error: ' . $insertStmt->error;
            error_log('Add user database error: ' . $insertStmt->error); // Log the actual DB error
        }
        $insertStmt->close();
    }
}

// Close database connection at the end of the script, after all operations
mysqli_close($koneksi);
?>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Add User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE v4 | Add User" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous" />

    <link rel="stylesheet" href="css/adminlte.css" />
    <style>
        /* Custom styles for select dropdowns similar to register.php */
        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right .5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }
        select.form-control:focus {
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
        }
    </style>
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
            </div>
        </nav>

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
                                            <a href="add_user.php" class="nav-link active">
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
                </nav>
            </div>
        </aside>

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
                                            <label for="nama" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="nama" name="nama" 
                                                value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>" required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="role" class="form-label">User Role</label>
                                            <select class="form-control" id="role" name="role" required>
                                                <option value="">Select User Role</option>
                                                <?php if (!empty($roles)): ?>
                                                    <?php foreach ($roles as $r_item): ?>
                                                        <option value="<?= htmlspecialchars($r_item['role_code']) ?>"
                                                            <?= (isset($_POST['role']) && $_POST['role'] === $r_item['role_code']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($r_item['role_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="ADMIN" <?= (isset($_POST['role']) && $_POST['role'] === 'ADMIN') ? 'selected' : '' ?>>Admin</option>
                                                    <option value="MANAGER" <?= (isset($_POST['role']) && $_POST['role'] === 'MANAGER') ? 'selected' : '' ?>>Manager</option>
                                                    <option value="PEGAWAI" <?= (isset($_POST['role']) && $_POST['role'] === 'PEGAWAI') ? 'selected' : '' ?>>Pegawai</option>
                                                    <option value="USER" <?= (isset($_POST['role']) && $_POST['role'] === 'USER') ? 'selected' : '' ?>>User</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="office" class="form-label">Office (Optional)</label>
                                            <select class="form-control" id="office" name="office">
                                                <option value="">Select Office</option>
                                                <?php foreach ($offices as $o_item): ?>
                                                    <option value="<?= htmlspecialchars($o_item['office_code']) ?>"
                                                        <?= (isset($_POST['office']) && $_POST['office'] === $o_item['office_code']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($o_item['office_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/adminlte.js"></script>
</body>
</html>