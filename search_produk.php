<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'koneksi.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if (empty($keyword)) {
    echo json_encode(['error' => 'Keyword kosong']);
    exit;
}

$keyword_safe = mysqli_real_escape_string($koneksi, $keyword);

$sql = "
    SELECT 
        u.id_umkm,
        u.nama_umkm,
        u.lokasi,
        u.foto,
        u.nomor_kontak,
        u.status_halal,
        MIN(p.harga) AS harga_mulai,
        MAX(p.harga) AS harga_maks,
        GROUP_CONCAT(DISTINCT kr.jenis_rasa ORDER BY kr.jenis_rasa SEPARATOR ', ') AS daftar_rasa,
        GROUP_CONCAT(DISTINCT p.nama_produk ORDER BY p.nama_produk SEPARATOR '||') AS daftar_produk,
        GROUP_CONCAT(DISTINCT p.asal_daerah SEPARATOR ', ') AS asal_daerah
    FROM umkm u
    JOIN produk p ON u.id_umkm = p.id_umkm
    LEFT JOIN produk_rasa pr ON p.id_produk = pr.id_produk
    LEFT JOIN kategori_rasa kr ON pr.id_rasa = kr.id_rasa
    WHERE 
        u.nama_umkm LIKE '%$keyword_safe%'
        OR p.nama_produk LIKE '%$keyword_safe%'
        OR p.kategori_produk LIKE '%$keyword_safe%'
    GROUP BY u.id_umkm
    ORDER BY u.nama_umkm ASC
    LIMIT 20
";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    echo json_encode(['error' => mysqli_error($koneksi)]);
    exit;
}

$results = [];
while ($row = mysqli_fetch_assoc($query)) {
    // Format harga
    $row['harga_mulai'] = (float) $row['harga_mulai'];
    $row['harga_maks']  = (float) $row['harga_maks'];

    $row['produk_list'] = $row['daftar_produk']
        ? explode('||', $row['daftar_produk'])
        : [];
    unset($row['daftar_produk']);

    $results[] = $row;
}

echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
