<?php
// Konfigurasi Database
$host     = 'localhost';
$username = 'root';
$password = 'ameng';
$database = 'u117465023_sapu'; 

// Membuat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// Mengatur charset UTF-8 untuk mendukung karakter Indonesia
$koneksi->set_charset("utf8");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
} else {
    // Uncomment baris di bawah untuk testing (hapus saat production)
    // echo "Koneksi berhasil!";
}
?>