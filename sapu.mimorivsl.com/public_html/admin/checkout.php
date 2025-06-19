<?php
// checkout.php

// Koneksi ke database (ganti dengan kredensial Anda)
require_once 'koneksi.php';

// Mendapatkan data dari frontend (POST request)
$data = json_decode(file_get_contents('php://input'), true);

// Memeriksa apakah data valid
if (!$data || !isset($data['items']) || !isset($data['customer_name']) || !isset($data['customer_email'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    exit;
}

// Mengambil data dari request
$items = $data['items'];
$customer_name = $data['customer_name'];
$customer_email = $data['customer_email'];
$customer_phone = $data['customer_phone'] ?? null;
$total_amount = $data['total_amount'];


// Mulai transaksi untuk menyimpan pesanan
try {
    $koneksi->begin_transaction();

    // Menyimpan informasi pesanan ke dalam tabel orders
    $stmt = $koneksi->prepare('INSERT INTO orders (customer_name, customer_email, customer_phone, total_amount, status) VALUES (?, ?, ?, ?, ?)');
    $status = 'pending'; // Status pesanan, default 'pending'
    $stmt->bind_param('sssss', $customer_name, $customer_email, $customer_phone, $total_amount, $status);  // Ganti tipe data 'd' dengan 's' untuk status
    $stmt->execute();
    $order_id = $stmt->insert_id;  // Mendapatkan ID pesanan yang baru dimasukkan

    // Menyimpan rincian produk ke dalam tabel order_items
    foreach ($items as $item) {
        $stmt = $koneksi->prepare('INSERT INTO order_items (order_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)');
        $total_price = $item['harga'] * $item['jumlah'];
        $stmt->bind_param('iiii', $order_id, $item['productId'], $item['jumlah'], $total_price);
        $stmt->execute();
    }

    // Commit transaksi
    $koneksi->commit();

    // Mengembalikan ID pesanan yang baru dibuat
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    // Jika terjadi error, rollback transaksi
    $koneksi->rollback();
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memproses pesanan.']);
}
?>
