<?php
session_start();
require_once 'koneksi.php'; // Pastikan file ini ada dan membuat koneksi $koneksi

$error = '';
$success = '';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Ambil data office dan role untuk dropdown
$offices = [];
$roles = [];

try {
    // Ambil data office
    $result_offices = $koneksi->query("SELECT office_code, office_name FROM office ORDER BY office_name");
    if ($result_offices) {
        while ($row = $result_offices->fetch_assoc()) {
            $offices[] = $row;
        }
        $result_offices->free(); // Bebaskan hasil
    }

    // Ambil data role
    $result_roles = $koneksi->query("SELECT role_code, role_name FROM role ORDER BY role_name");
    if ($result_roles) {
        while ($row = $result_roles->fetch_assoc()) {
            $roles[] = $row;
        }
        $result_roles->free(); // Bebaskan hasil
    }

    // Debug: cek apakah data role ada
    if (empty($roles)) {
        error_log('Warning: Tidak ada data role ditemukan di database. Menggunakan role fallback.');
    }

} catch (Exception $e) {
    error_log('Error fetching dropdown data: ' . $e->getMessage());
    // Opsional: tampilkan error ke user jika pengambilan data dropdown gagal
    // $error = 'Gagal memuat data pilihan role dan office. Silakan coba refresh halaman.';
}


// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Retrieve and sanitize inputs
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = strtoupper(trim($_POST['role'] ?? ''));
        $office = trim($_POST['office'] ?? '');

        // Validate inputs - Ini adalah validasi awal yang tidak melibatkan database
        if (empty($username) || empty($email) || empty($nama) || empty($password) || empty($confirm_password) || empty($role)) {
            $error = 'Semua field wajib diisi!';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $error = 'Username harus antara 3-50 karakter!';
        } elseif (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
            $error = 'Username hanya boleh berisi huruf, angka, dan underscore!';
        } elseif (strlen($nama) < 2 || strlen($nama) > 100) {
            $error = 'Nama lengkap harus antara 2-100 karakter!';
        } elseif ($password !== $confirm_password) {
            $error = 'Password dan konfirmasi password tidak cocok!';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid!';
        } elseif (strlen($email) > 100) {
            $error = 'Email terlalu panjang!';
        }

        // Jika tidak ada error validasi input dasar, lanjutkan dengan validasi database dan insert
        if (empty($error)) {
            try {
                $koneksi->begin_transaction();

                // Validasi role exists di database
                $role_valid = false;
                if (!empty($roles)) {
                    $stmt = $koneksi->prepare('SELECT role_code FROM role WHERE role_code = ?');
                    $stmt->bind_param('s', $role);
                    $stmt->execute();
                    $role_result = $stmt->get_result();

                    if ($role_result->num_rows > 0) {
                        $role_valid = true;
                    }
                    $stmt->close();
                } else {
                    $valid_roles_fallback = ['ADMIN', 'MANAGER', 'PEGAWAI', 'USER'];
                    if (in_array($role, $valid_roles_fallback)) {
                        $role_valid = true;
                    }
                }

                if (!$role_valid) {
                    $error = 'Role tidak valid!';
                }

                // Validasi office exists jika office dipilih (dan role valid)
                $office_code_for_db = null;
                if (empty($error) && !empty($office)) {
                    $stmt = $koneksi->prepare('SELECT office_code FROM office WHERE office_code = ?');
                    $stmt->bind_param('s', $office);
                    $stmt->execute();
                    $office_result = $stmt->get_result();

                    if ($office_result->num_rows == 0) {
                        $error = 'Office tidak valid!';
                    } else {
                        $office_code_for_db = $office;
                    }
                    $stmt->close();
                }

                // Jika masih tidak ada error, lakukan pengecekan duplikasi dan insert user
                if (empty($error)) {
                    // Check if email or username exists (case-insensitive)
                    $stmt = $koneksi->prepare('SELECT username FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(username) = LOWER(?)');
                    $email_lower = strtolower($email);
                    $username_lower = strtolower($username);
                    $stmt->bind_param('ss', $email_lower, $username_lower);
                    $stmt->execute();
                    $result_check_duplicate = $stmt->get_result();

                    if ($result_check_duplicate->num_rows > 0) {
                        $error = 'Email atau username sudah terdaftar!';
                        $stmt->close();
                    } else {
                        $stmt->close();
                        // Hash password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // --- BAGIAN INI YANG HARUS DIUBAH ---
                        // Ubah query INSERT untuk TIDAK MENYERTakan kolom `gaji`
                        $stmt = $koneksi->prepare('INSERT INTO users (email, password, username, nama, role, office) VALUES (?, ?, ?, ?, ?, ?)');
                        // Ubah bind_param agar TIDAK MENYERTakan parameter `$default_gaji` dan tipe `d`
                        $stmt->bind_param('ssssss', $email_lower, $hashed_password, $username, $nama, $role, $office_code_for_db);

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
    <link href="./css/adminlte.css" rel="stylesheet">
    <link rel="icon" href="assets_login/img/favicon.png" type="image/x-icon">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js" crossorigin="anonymous"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>
    <style>
        .form-text { font-size: .875rem; margin-top: .25rem; }
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
                                            <div class="col-md-6 mb-3">
                                                <label class="small mb-1" for="inputUsername">Username <span class="text-danger">*</span></label>
                                                <input class="form-control py-4" id="inputUsername" name="username" type="text" placeholder="Masukkan username" required minlength="3" maxlength="50" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                                                <div class="form-text text-muted">3-50 karakter, hanya huruf, angka, dan underscore</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="small mb-1" for="inputEmail">Email <span class="text-danger">*</span></label>
                                                <input class="form-control py-4" id="inputEmail" name="email" type="email" placeholder="Masukkan email" required maxlength="100" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                                <div class="form-text text-muted">Format email yang valid</div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputNama">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input class="form-control py-4" id="inputNama" name="nama" type="text" placeholder="Masukkan nama lengkap" required minlength="2" maxlength="100" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">
                                            <div class="form-text text-muted">Nama lengkap Anda</div>
                                        </div>

                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label class="small mb-1" for="inputRole">Role <span class="text-danger">*</span></label>
                                                <select class="form-control" id="inputRole" name="role" required>
                                                    <option value="">Pilih Role</option>
                                                    <?php if (!empty($roles)): ?>
                                                        <?php foreach ($roles as $role_item): ?>
                                                            <option value="<?= htmlspecialchars($role_item['role_code']) ?>"
                                                                <?= (isset($_POST['role']) && $_POST['role'] == $role_item['role_code']) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($role_item['role_name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="ADMIN" <?= (isset($_POST['role']) && $_POST['role'] == 'ADMIN') ? 'selected' : '' ?>>Administrator</option>
                                                        <option value="MANAGER" <?= (isset($_POST['role']) && $_POST['role'] == 'MANAGER') ? 'selected' : '' ?>>Manager</option>
                                                        <option value="PEGAWAI" <?= (isset($_POST['role']) && $_POST['role'] == 'PEGAWAI') ? 'selected' : '' ?>>Pegawai</option>
                                                        <option value="USER" <?= (isset($_POST['role']) && $_POST['role'] == 'USER') ? 'selected' : '' ?>>User</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="small mb-1" for="inputOffice">Office</label>
                                                <select class="form-control" id="inputOffice" name="office">
                                                    <option value="">Pilih Office (Opsional)</option>
                                                    <?php foreach ($offices as $office_item): ?>
                                                        <option value="<?= htmlspecialchars($office_item['office_code']) ?>"
                                                            <?= (isset($_POST['office']) && $_POST['office'] == $office_item['office_code']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($office_item['office_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label class="small mb-1" for="inputPassword">Password <span class="text-danger">*</span></label>
                                                <input class="form-control py-4" id="inputPassword" name="password" type="password" placeholder="Masukkan password" required minlength="6" maxlength="255">
                                                <div class="form-text text-muted">Minimal 6 karakter</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="small mb-1" for="inputConfirmPassword">Konfirmasi Password <span class="text-danger">*</span></label>
                                                <input class="form-control py-4" id="inputConfirmPassword" name="confirm_password" type="password" placeholder="Konfirmasi password" required>
                                                <div class="form-text" id="passwordMatch"></div>
                                            </div>
                                        </div>

                                        <div class="form-group mt-4 mb-0">
                                            <button class="btn btn-primary btn-block" type="submit" id="submitBtn">
                                                <i class="fas fa-user-plus me-2"></i>Buat Akun
                                            </button>
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

            // Prevent double submit
            $('#registrationForm').on('submit', function(){
                $('#submitBtn').prop('disabled',true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
            });
        });
    </script>
</body>
</html>