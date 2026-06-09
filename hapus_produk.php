<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID produk tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: produk.php');
    exit;
}

$id_produk = (int)$_GET['id'];

// Ambil nama produk sebelum dihapus
$query = "SELECT nama_produk FROM produk WHERE id_produk = $id_produk";
$result = mysqli_query($koneksi, $query);
$produk = mysqli_fetch_assoc($result);

if ($produk) {
    $nama_produk = $produk['nama_produk'];
    
    // Hapus data relasi di produk_rasa terlebih dahulu untuk menghindari constraint foreign key
    mysqli_query($koneksi, "DELETE FROM produk_rasa WHERE id_produk = $id_produk");
    
    $query_delete = "DELETE FROM produk WHERE id_produk = $id_produk";
    if (mysqli_query($koneksi, $query_delete)) {
        $_SESSION['message'] = "Produk \"$nama_produk\" berhasil dihapus!";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Gagal menghapus produk: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = 'error';
    }
} else {
    $_SESSION['message'] = "Data produk tidak ditemukan!";
    $_SESSION['message_type'] = 'error';
}

header('Location: produk.php');
exit;
?>
