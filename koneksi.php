<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_streetfood";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    // Jangan tampilkan detail error ke end-user di production
    error_log("mysqli_connect gagal: " . mysqli_connect_error());
    die(json_encode([
        "status"  => "error",
        "message" => "Koneksi database gagal. Silakan coba beberapa saat lagi."
    ]));
}

// Set charset ke utf8mb4 agar mendukung karakter emoji & Unicode penuh
mysqli_set_charset($koneksi, "utf8mb4");
try {
    $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // lempar exception saat error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // hasil fetch sebagai array asosiatif
        PDO::ATTR_EMULATE_PREPARES   => false,                    // pakai prepared statement asli
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log("PDO gagal: " . $e->getMessage());
    $pdo = null; // biarkan null; file yang butuh $pdo wajib cek sendiri
}
?>
