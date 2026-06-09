<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID UMKM tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: umkm.php');
    exit;
}

$id_umkm = (int)$_GET['id'];

// Ambil nama UMKM dan foto sebelum dihapus untuk pesan dan unlink
$query = "SELECT nama_umkm, foto FROM umkm WHERE id_umkm = $id_umkm";
$result = mysqli_query($koneksi, $query);
$umkm = mysqli_fetch_assoc($result);

if ($umkm) {
    $nama_umkm = $umkm['nama_umkm'];
    
    // Hapus file foto jika ada
    if ($umkm['foto'] && file_exists($umkm['foto'])) {
        unlink($umkm['foto']);
    }
    
    // Hapus semua data yang berhubungan dengan UMKM ini secara berurutan untuk menghindari constraint error
    
    // 1. Hapus waktu operasional
    mysqli_query($koneksi, "DELETE FROM waktu_operasional WHERE id_umkm = $id_umkm");
    
    // 2. Hapus relasi dengan mitra platform
    mysqli_query($koneksi, "DELETE FROM umkm_mitra WHERE id_umkm = $id_umkm");
    
    // 3. Hapus relasi rasa produk untuk produk-produk milik UMKM ini
    mysqli_query($koneksi, "DELETE FROM produk_rasa WHERE id_produk IN (SELECT id_produk FROM produk WHERE id_umkm = $id_umkm)");
    
    // 4. Hapus produk-produk milik UMKM ini
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_umkm = $id_umkm");
    
    // 5. Hapus UMKM itu sendiri
    $query_delete = "DELETE FROM umkm WHERE id_umkm = $id_umkm";
    if (mysqli_query($koneksi, $query_delete)) {
        $_SESSION['message'] = "UMKM \"$nama_umkm\" beserta seluruh produk dan data terkait berhasil dihapus!";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Gagal menghapus UMKM: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = 'error';
    }
} else {
    $_SESSION['message'] = "Data UMKM tidak ditemukan!";
    $_SESSION['message_type'] = 'error';
}

header('Location: umkm.php');
exit;
?>
