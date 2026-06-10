<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_streetfood";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    error_log("mysqli_connect gagal: " . mysqli_connect_error());
    die(json_encode([
        "status"  => "error",
        "message" => "Koneksi database gagal. Silakan coba beberapa saat lagi."
    ]));
}

/* 🔥 TAMBAHAN PENTING (BIAR ROLLBACK JALAN) */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* Charset */
mysqli_set_charset($koneksi, "utf8mb4");

/* 🔥 OPTIONAL (BIAR LEBIH AMAN TRANSACTION) */
$koneksi->autocommit(true);

/* ================== PDO (TETAP ADA, GAK DIHAPUS) ================== */
try {
    $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log("PDO gagal: " . $e->getMessage());
    $pdo = null;
}
?>