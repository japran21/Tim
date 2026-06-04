<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'koneksi.php';
$result = [];
if (!empty($_GET['rasa'])) {
    $rasa = mysqli_real_escape_string($koneksi, trim($_GET['rasa']));
    $sql = "
        SELECT
            p.id_produk,
            p.nama_produk,
            p.harga,
            p.kategori_produk,
            u.nama_umkm
        FROM produk p
        JOIN umkm u ON p.id_umkm = u.id_umkm
        WHERE p.kategori_produk LIKE '%$rasa%'
        ORDER BY p.nama_produk ASC
    ";

} elseif (!empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, trim($_GET['keyword']));
    $sql = "
        SELECT
            p.id_produk,
            p.nama_produk,
            p.harga,
            p.kategori_produk,
            u.nama_umkm
        FROM produk p
        JOIN umkm u ON p.id_umkm = u.id_umkm
        WHERE p.nama_produk LIKE '%$keyword%'
           OR u.nama_umkm   LIKE '%$keyword%'
        ORDER BY p.nama_produk ASC
        LIMIT 50
    ";
} else {
    echo json_encode([]);
    exit;
}
$query = mysqli_query($koneksi, $sql);
if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $result[] = $row;
    }
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
