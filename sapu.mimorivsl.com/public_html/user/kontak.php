
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact - Sapu jagaD</title>
  <link rel="icon" type="icon" href="assets/favicon.png" />

  <!-- CSS -->
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg shadow-sm bg-white py-3 fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold fs-4 text-primary" href="index.html">Sapu jagaD</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto align-items-center gap-3">
          <li class="nav-item"><a class="nav-link fw-medium" href="index.html">Home</a></li>
          <li class="nav-item"><a class="nav-link fw-medium" href="produk.html">Product</a></li>
          <li class="nav-item"><a class="nav-link fw-medium active" href="kontak.php">Contact</a></li>
          <li class="nav-item"><a class="nav-link fw-medium" href="profil.html">About</a></li>
          <li class="nav-item">
            <a class="nav-link position-relative" href="keranjang.html">
              <span class="material-symbols-outlined fs-4">shopping_cart</span>
              <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
            </a>
          </li>
          <li class="nav-item"><a class="nav-link fw-medium" href="admin">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contact Form Section -->
  <div class="container my-5 pt-5">
    <div class="contact-container text-center">
      <h1>Contact Us</h1>
      <p>Have a question, comment, or just want to say hi? Fill out the form below!</p>
    </div>
    <?php if (!empty($errorMsg)): ?>
      <div class="alert alert-danger text-center"><?= $errorMsg ?></div>
    <?php endif; ?>
    <form id="contactForm" class="contact-form mx-auto" style="max-width: 600px;" action="kontak_post.php" method="POST">
      <div class="mb-3">
        <label for="nama" class="form-label">Your Name</label>
        <input type="text" class="form-control" id="nama" name="nama" placeholder="Enter your name" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
      </div>
      <div class="mb-3">
        <label for="pesan" class="form-label">Your Message</label>
        <textarea class="form-control" id="pesan" name="pesan" rows="5" placeholder="Write something..." required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
  </div>

  <!-- Footer -->
  <footer class="bg-light text-dark pt-4 mt-5">
    <div class="container text-center text-md-start">
      <div class="row">
        <!-- Company Info -->
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold">Sapu jagaD</h6>
          <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #000; height: 2px" />
          <p>
            Penyedia layanan dan produk otomotif terpercaya yang selalu mengutamakan kualitas dan kepuasan pelanggan.
          </p>
        </div>

        <!-- Navigation Links -->
        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold">Social Media</h6>
          <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #000; height: 2px"/>
          <p>
            <a href="https://www.instagram.com/sapujagad" class="text-dark text-decoration-none">
              <i class="bi bi-instagram me-2"></i>Instagram
            </a>
          </p>
          <p>
            <a href="https://www.facebook.com/sapujagad" class="text-dark text-decoration-none">
              <i class="bi bi-facebook me-2"></i>Facebook
            </a>
          </p>
          <p>
            <a href="https://twitter.com/sapujagad" class="text-dark text-decoration-none">
              <i class="bi bi-twitter me-2"></i>Twitter
            </a>
          </p>
          <p>
            <a href="https://youtube.com/@sapujagad" class="text-dark text-decoration-none">
              <i class="bi bi-youtube me-2"></i>YouTube
            </a>
          </p>
        </div>

        <!-- Contact Info -->
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
          <h6 class="text-uppercase fw-bold">Contact</h6>
          <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #000; height: 2px" />
          <p><i class="bi bi-house-door-fill me-2"></i> Jl. Merdeka No.123, Jakarta</p>
          <p><i class="bi bi-envelope-fill me-2"></i> info@sapujagad.com</p>
          <p><i class="bi bi-phone-fill me-2"></i> +62 812 3456 7890</p>
        </div>
      </div>
    </div>

    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0,0,0,0.05);">
      © 2025 Sapu jagaD. All Rights Reserved.
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="index.js"></script>
</body>
</html>
