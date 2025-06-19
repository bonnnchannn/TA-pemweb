<?php
session_start();
require_once 'koneksi.php';

$error = '';
$success = '';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Retrieve and sanitize inputs
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']); // Use full_name instead of nama
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $user_level = strtoupper($_POST['user_level']);

        // Validate inputs
        if (empty($username) || empty($email) || empty($full_name) || empty($password) || empty($confirm_password) || empty($user_level)) {
            $error = 'Semua field wajib diisi!';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $error = 'Username harus antara 3-50 karakter!';
        } elseif (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
            $error = 'Username hanya boleh berisi huruf, angka, dan underscore!';
        } elseif (strlen($full_name) < 2 || strlen($full_name) > 100) {
            $error = 'Nama lengkap harus antara 2-100 karakter!';
        } elseif ($password !== $confirm_password) {
            $error = 'Password dan konfirmasi password tidak cocok!';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid!';
        } elseif (strlen($email) > 100) {
            $error = 'Email terlalu panjang!';
        } elseif (!in_array($user_level, ['ADMIN', 'MANAGER', 'PEGAWAI', 'USER'])) {
            $error = 'Level pengguna tidak valid!';
        } else {
            try {
                $koneksi->begin_transaction();
                // Check if email or username exists
                $stmt = $koneksi->prepare('SELECT username FROM users WHERE LOWER(email) = ? OR LOWER(username) = ?');
                $email_lower = strtolower($email);
                $username_lower = strtolower($username);
                $stmt->bind_param('ss', $email_lower, $username_lower);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $error = 'Email atau username sudah terdaftar!';
                    $stmt->close();
                } else {
                    $stmt->close();
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert new user with full_name
                    $stmt = $koneksi->prepare('INSERT INTO users (email, password, username, full_name, user_level) VALUES (?, ?, ?, ?, ?)');
                    $stmt->bind_param('sssss', $email_lower, $hashed_password, $username, $full_name, $user_level);

                    if ($stmt->execute()) {
                        $koneksi->commit();
                        $success = 'Registrasi berhasil! Silakan login.';
                        error_log("New user registered: $email_lower");
                        $_POST = [];
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    } else {
                        throw new Exception('Execute failed: ' . $stmt->error);
                    }

                    $stmt->close();
                }
            } catch (Exception $e) {
                $koneksi->rollback();
                $error = 'Gagal melakukan registrasi. Silakan coba lagi.';
                error_log('Registration error: ' . $e->getMessage());
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Halaman registrasi pengguna baru">
    <meta name="author" content="Sapu Jagad">
    <title>Registrasi - Sapu Jagad Admin</title>
    <link href="css/styles.css" rel="stylesheet">
    <link rel="icon" href="assets_login/img/favicon.png" type="image/x-icon">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js" crossorigin="anonymous"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>
    <style>
        .form-text { font-size: .875rem; margin-top: .25rem; }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right .5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; }
        select.form-control:focus { outline: none; border-color: #80bdff; box-shadow: 0 0 0 .2rem rgba(0,123,255,.25); }
        .text-muted { color: #6c757d !important; }
    </style>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header text-center">
                                    <h3 class="font-weight-light my-4">Buat Akun Baru</h3>
                                </div>
                                <div class="card-body">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($success): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                                        </div>
                                    <?php endif; ?>
                                    <form method="POST" action="" id="registrationForm">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputUsername">Username <span class="text-danger">*</span></label>
                                                <input class="form-control py-4" id="inputUsername" name="username" type="text" placeholder="Masukkan username" required minlength="3" maxlength="50" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                                                <div class="form-text">3-50 karakter, huruf/angka/_</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputEmail">Email <span class="text-danger">*</span></label>
                                                <input class="form-control py-4" id="inputEmail" name="email" type="email" placeholder="Masukkan email" required maxlength="100" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                            </div>
                                        </div>
                                        <div class="form-row mt-3">
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputFullName">Nama Lengkap <span class="text-danger">*</span></label>
                                                <input class="form-control py-4" id="inputFullName" name="full_name" type="text" placeholder="Masukkan nama lengkap" required minlength="2" maxlength="100" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>">
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="small mb-1" for="inputUserLevel">Level Pengguna <span class="text-danger">*</span></label>
                                            <select class="form-control" id="inputUserLevel" name="user_level" required>
                                                <option value="">Pilih Level Pengguna</option>
                                                <option value="admin" <?= (isset($_POST['user_level']) && $_POST['user_level']=='admin')?'selected':'' ?>>Admin</option>
                                                <option value="manager" <?= (isset($_POST['user_level']) && $_POST['user_level']=='manager')?'selected':'' ?>>Manager</option>
                                                <option value="pegawai" <?= (isset($_POST['user_level']) && $_POST['user_level']=='pegawai')?'selected':'' ?>>Pegawai</option>
                                                <option value="user" <?= (isset($_POST['user_level']) && $_POST['user_level']=='user')?'selected':'' ?>>User</option>
                                            </select>
                                        </div>
                                        <div class="form-row mt-3">
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputPassword">Password <span class="text-danger">*</span></label>
                                                <input class="form-control py-4" id="inputPassword" name="password" type="password" placeholder="Masukkan password" required minlength="6" maxlength="255">
                                                <div class="form-text">Minimal 6 karakter</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputConfirmPassword">Konfirmasi Password <span class="text-danger">*</span></label>
                                                <input class="form-control py-4" id="inputConfirmPassword" name="confirm_password" type="password" placeholder="Konfirmasi password" required>
                                                <div class="form-text" id="passwordMatch"></div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-4 mb-0">
                                            <button class="btn btn-primary btn-block" type="submit" id="submitBtn"><i class="fas fa-user-plus me-2"></i>Buat Akun</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="small"><a href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Sudah punya akun? Login di sini</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="footer mt-auto footer-dark">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 small">Copyright &copy; Sapu Jagad <?= date('Y') ?></div>
                        <div class="col-md-6 text-md-right small">
                            <a href="#!">Kebijakan Privasi</a> &middot; <a href="#!">Syarat & Ketentuan</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
    $(function(){
        // Password match checker
        function checkPasswordMatch(){
            var pass = $('#inputPassword').val();
            var confirm = $('#inputConfirmPassword').val();
            var div = $('#passwordMatch');
            if(!confirm){ div.text(''); return; }
            if(pass === confirm){
                div.text('Password cocok').addClass('text-success').removeClass('text-danger');
            } else {
                div.text('Password tidak cocok').addClass('text-danger').removeClass('text-success');
            }
        }
        $('#inputPassword, #inputConfirmPassword').on('input', checkPasswordMatch);
        
        // Auto-fill full_name from nama
        $('#inputNama').on('input', function(){
            var namaValue = $(this).val();
            if($('#inputFullName').val() === ''){
                $('#inputFullName').val(namaValue);
            }
        });
        
        // Prevent double submit
        $('#registrationForm').on('submit', function(){
            $('#submitBtn').prop('disabled',true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
        });
    });
    </script>
</body>
</html>