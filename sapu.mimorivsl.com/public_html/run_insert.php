<?php
// Script untuk menjalankan insert sample users

try {
    $host = 'localhost';
    $dbname = 'u117465023_sapu';
    $username = 'root';
    $password = 'ameng';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Menjalankan Insert Sample Users...</h2>";
    
    // Cek apakah data sudah ada
    $check = $pdo->query("SELECT COUNT(*) as count FROM users");
    $existing_count = $check->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<p>Jumlah user saat ini: $existing_count</p>";
    
    if ($existing_count > 0) {
        echo "<p style='color: orange;'>Data user sudah ada. Menghapus data lama...</p>";
        $pdo->exec("DELETE FROM users");
    }
    
    // Insert sample users
    $sql = "INSERT INTO users (username, nama, email, password) VALUES 
            ('admin', 'Administrator', 'admin@sapu.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
            ('user1', 'John Doe', 'john@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
            ('user2', 'Jane Smith', 'jane@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
            ('user3', 'Bob Wilson', 'bob@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
            ('user4', 'Alice Brown', 'alice@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')";
    
    $pdo->exec($sql);
    
    echo "<p style='color: green;'>✅ Berhasil menambahkan 5 sample users!</p>";
    
    // Verifikasi hasil
    $verify = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total = $verify->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p><strong>Total users sekarang: $total</strong></p>";
    
    // Tampilkan data users
    echo "<h3>Data Users:</h3>";
    $users = $pdo->query("SELECT username, nama, email FROM users");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Username</th><th>Nama</th><th>Email</th></tr>";
    
    while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td>" . htmlspecialchars($user['nama']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='color: blue;'>🔄 Silakan refresh halaman admin dashboard untuk melihat perubahan.</p>";
    echo "<p><a href='admin/index.php'>Kembali ke Dashboard Admin</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>