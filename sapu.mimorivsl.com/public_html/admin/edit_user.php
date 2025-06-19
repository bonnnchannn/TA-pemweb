<?php
// Mulai session
session_start();

// Sertakan file koneksi database
require_once 'koneksi.php';

// Implementasi BASE_URL untuk path yang stabil
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/PEMWEB/domains/sapu.mimorivsl.com/public_html/admin/'); 
}

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

// Pastikan ID pengguna ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: " . BASE_URL . "user.php");
    exit();
}

$user_id = $_GET['id'];
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DIPERBAIKI: Menggunakan 'nama' sesuai dengan nama kolom di database
    $nama = trim($_POST['nama']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validasi
    // DIPERBAIKI: Memeriksa variabel $nama
    if (empty($nama)) {
        $error = "Nama lengkap harus diisi.";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok.";
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = "Password minimal harus 6 karakter.";
    } else {
        // Update data pengguna
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // DIPERBAIKI: Mengupdate kolom 'nama', bukan 'full_name'
            $sql = "UPDATE users SET nama = ?, password = ? WHERE id = ?";
            $stmt = $koneksi->prepare($sql);
            // DIPERBAIKI: Mengirim variabel $nama
            $stmt->bind_param("ssi", $nama, $hashed_password, $user_id);
        } else {
            // DIPERBAIKI: Mengupdate kolom 'nama', bukan 'full_name'
            $sql = "UPDATE users SET nama = ? WHERE id = ?";
            $stmt = $koneksi->prepare($sql);
            // DIPERBAIKI: Mengirim variabel $nama
            $stmt->bind_param("si", $nama, $user_id);
        }
        
        // DIPERBAIKI: Baris ini sekarang menjadi sekitar baris 61 tempat error terjadi
        if ($stmt->execute()) {
            $message = "Data pengguna berhasil diperbarui!";
        } else {
            $error = "Error saat memperbarui pengguna: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Ambil data pengguna untuk ditampilkan di form
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $koneksi->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $koneksi->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: " . BASE_URL . "user.php?error=notfound");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
?>

<!doctype html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Edit Pengguna</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/adminlte.css" />
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
                    <li class="nav-item d-none d-md-block"><a href="<?php echo BASE_URL; ?>index.php" class="nav-link">Home</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                            <i class="bi bi-person-circle"></i> <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-footer">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                                <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                </div>
        </nav>
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="<?php echo BASE_URL; ?>index.php" class="brand-link">
                    <img src="<?php echo rtrim(BASE_URL, '/admin/'); ?>/assets_login/img/favicon.png" alt="Logo" class="brand-image opacity-75 shadow" />
                    <span class="brand-text fw-light">Sapu JagaD</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    </nav>
            </div>
        </aside>
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Edit Pengguna</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>user.php">Daftar Pengguna</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Pengguna</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Edit Pengguna: <?php echo htmlspecialchars($user['username']); ?></h3>
                                    <div class="card-tools">
                                        <a href="<?php echo BASE_URL; ?>user.php" class="btn btn-secondary btn-sm">
                                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
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

                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $user_id; ?>">
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                                        </div>
                                        <hr>
                                        <h5>Ubah Password (Opsional)</h5>
                                        <p class="text-muted">Biarkan kosong jika tidak ingin mengubah password.</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Password Baru</label>
                                                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password baru (min 6 karakter)">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi password baru">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="<?php echo BASE_URL; ?>user.php" class="btn btn-secondary me-md-2">Batal</a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-lg"></i> Perbarui Pengguna
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header"><h3 class="card-title">Informasi Pengguna</h3></div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>ID Pengguna:</strong></td>
                                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Username:</strong></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        </tr>
                                        </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="app-footer">
            </footer>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"></script>
    <script src="<?php echo BASE_URL; ?>js/adminlte.js"></script>
    <script>
        // ... (script biarkan seperti aslinya) ...
    </script>
</body>
</html>