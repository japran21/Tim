<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID kategori topping tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: kategori_topping.php');
    exit;
}

$id_topping = (int)$_GET['id'];

// Ambil nama topping sebelum dihapus untuk pesan
$query_nama = "SELECT nama_topping FROM kategori_topping WHERE id_topping = $id_topping";
$result_nama = mysqli_query($koneksi, $query_nama);
$topping = mysqli_fetch_assoc($result_nama);
$nama_topping = $topping ? $topping['nama_topping'] : '';

// Hapus data
$query_delete = "DELETE FROM kategori_topping WHERE id_topping = $id_topping";

if (mysqli_query($koneksi, $query_delete)) {
    $_SESSION['message'] = "Kategori topping \"$nama_topping\" berhasil dihapus!";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
}

header('Location: kategori_topping.php');
exit;
?>
