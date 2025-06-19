<?php
session_start();
require_once 'koneksi.php';

$error = '';

// Redirect jika user sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan normalisasi input
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi input tidak kosong
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        // Gunakan prepared statement untuk mencari berdasarkan username
        $stmt = $koneksi->prepare("SELECT * FROM users WHERE LOWER(username) = ?");
        $username_lower = strtolower($username);
        $stmt->bind_param("s", $username_lower);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Update last_login jika ada field tersebut
                // $update_stmt = $koneksi->prepare("UPDATE users SET last_login = NOW() WHERE username = ?");
                // $update_stmt->bind_param("s", $user['username']);
                // $update_stmt->execute();
                // $update_stmt->close();
                
                // Login berhasil - simpan data ke session
                $_SESSION['user_id'] = $user['username'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['user_level'] = strtoupper($user['user_level']);
                $_SESSION['email'] = $user['email'];

                // Redirect berdasarkan user_level dengan absolute path
                $redirect_url = "index.php";
                
                // Pastikan tidak ada output sebelum header
                ob_clean();
                header("Location: " . $redirect_url);
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan atau akun tidak aktif!";
        }

        $stmt->close();
    }
}

// Handle error message from URL parameter
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Halaman login sistem Sapu Jagad" />
        <meta name="author" content="Sapu Jagad" />
        <title>Login - Sapu Jagad Admin</title>
        <link href="./css/adminlte.rtl.min.css" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="assets_login/img/favicon.png" />
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header text-center">
                                        <h3 class="font-weight-light my-4">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($error)): ?>
                                            <div class="alert alert-danger" role="alert">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <?php echo htmlspecialchars($error); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <form method="POST" action="" id="loginForm">
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputUsername">
                                                    <i class="fas fa-user me-1"></i>Username <span class="text-danger">*</span>
                                                </label>
                                                <input class="form-control py-4" id="inputUsername" name="username" 
                                                       type="text" placeholder="Masukkan username" 
                                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                                       required maxlength="50" autocomplete="username"/>
                                            </div>
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputPassword">
                                                    <i class="fas fa-lock me-1"></i>Password <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input class="form-control py-4" id="inputPassword" name="password" 
                                                           type="password" placeholder="Masukkan password" 
                                                           required autocomplete="current-password"/>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input" id="rememberPasswordCheck" type="checkbox" />
                                                    <label class="custom-control-label" for="rememberPasswordCheck">Ingat saya</label>
                                                </div>
                                            </div>
                                            <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="forgot-password.php">
                                                    <i class="fas fa-key me-1"></i>Lupa Password?
                                                </a>
                                                <button class="btn btn-primary" type="submit" id="loginBtn">
                                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="small">
                                            <a href="register.php">
                                                <i class="fas fa-user-plus me-1"></i>Belum punya akun? Daftar di sini!
                                            </a>
                                        </div>
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
                                <a href="#!">Kebijakan Privasi</a>
                                &middot;
                                <a href="#!">Syarat &amp; Ketentuan</a>
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
            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#inputPassword');
                const toggleIcon = $('#toggleIcon');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Prevent double submit
            $('#loginForm').on('submit', function() {
                $('#loginBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
            });

            // Focus on username field when page loads
            $('#inputUsername').focus();
        });
        </script>
    </body>
</html>