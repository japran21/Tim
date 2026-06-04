<?php
// get_produk.php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

$result = [];

if (!empty($_GET['rasa'])) {
    $rasa = mysqli_real_escape_string($koneksi, trim($_GET['rasa']));
    
    // Ambil id_rasa berdasarkan nama rasa
    $queryRasa = "SELECT id_rasa FROM kategori_rasa WHERE jenis_rasa = '$rasa'";
    $resultRasa = mysqli_query($koneksi, $queryRasa);
    
    if ($rowRasa = mysqli_fetch_assoc($resultRasa)) {
        $id_rasa = $rowRasa['id_rasa'];
        
        $sql = "
            SELECT 
                p.id_produk,
                p.nama_produk,
                p.harga,
                p.kategori_produk,
                u.nama_umkm,
                GROUP_CONCAT(DISTINCT kr.jenis_rasa SEPARATOR ', ') as daftar_rasa
            FROM produk p
            JOIN umkm u ON p.id_umkm = u.id_umkm
            JOIN produk_rasa pr ON p.id_produk = pr.id_produk
            JOIN kategori_rasa kr ON pr.id_rasa = kr.id_rasa
            WHERE pr.id_rasa = $id_rasa
            GROUP BY p.id_produk
            ORDER BY p.nama_produk ASC
            LIMIT 50
        ";
        
        $query = mysqli_query($koneksi, $sql);
        
        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $result[] = $row;
            }
        } else {
            echo json_encode(['error' => mysqli_error($koneksi)]);
            exit;
        }
    } else {
        echo json_encode(['error' => "Rasa '$rasa' tidak ditemukan"]);
        exit;
    }
    
} elseif (!empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, trim($_GET['keyword']));
    
    $sql = "
        SELECT 
            p.id_produk,
            p.nama_produk,
            p.harga,
            p.kategori_produk,
            u.nama_umkm,
            GROUP_CONCAT(DISTINCT kr.jenis_rasa SEPARATOR ', ') as daftar_rasa
        FROM produk p
        JOIN umkm u ON p.id_umkm = u.id_umkm
        LEFT JOIN produk_rasa pr ON p.id_produk = pr.id_produk
        LEFT JOIN kategori_rasa kr ON pr.id_rasa = kr.id_rasa
        WHERE p.nama_produk LIKE '%$keyword%'
           OR u.nama_umkm LIKE '%$keyword%'
        GROUP BY p.id_produk
        ORDER BY p.nama_produk ASC
        LIMIT 50
    ";
    
    $query = mysqli_query($koneksi, $sql);
    
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
    } else {
        echo json_encode(['error' => mysqli_error($koneksi)]);
        exit;
    }
} else {
    echo json_encode(['error' => 'Parameter tidak ditemukan']);
    exit;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>