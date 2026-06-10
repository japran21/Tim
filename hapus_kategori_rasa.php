<<<<<<< HEAD
<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID kategori rasa tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: kategori_rasa.php');
    exit;
}

$id_rasa = (int)$_GET['id'];

// Ambil nama rasa sebelum dihapus untuk pesan
$query_nama = "SELECT jenis_rasa FROM kategori_rasa WHERE id_rasa = $id_rasa";
$result_nama = mysqli_query($koneksi, $query_nama);
$rasa = mysqli_fetch_assoc($result_nama);
$nama_rasa = $rasa ? $rasa['jenis_rasa'] : '';

// Cek apakah ada produk yang menggunakan rasa ini
$check_produk = "SELECT COUNT(*) as total FROM produk WHERE kategori_produk LIKE '%$nama_rasa%'";
$result_check = mysqli_query($koneksi, $check_produk);
$produk_count = mysqli_fetch_assoc($result_check)['total'];

if ($produk_count > 0) {
    // Ada produk yang menggunakan rasa ini
    $_SESSION['message'] = "Tidak dapat menghapus rasa \"$nama_rasa\" karena masih ada $produk_count produk yang menggunakan rasa ini. Silahkan update produk terlebih dahulu.";
    $_SESSION['message_type'] = 'error';
    header('Location: kategori_rasa.php');
    exit;
}

// Hapus data
$query_delete = "DELETE FROM kategori_rasa WHERE id_rasa = $id_rasa";

if (mysqli_query($koneksi, $query_delete)) {
    $_SESSION['message'] = "Kategori rasa \"$nama_rasa\" berhasil dihapus!";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
}

header('Location: kategori_rasa.php');
exit;
=======
<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID kategori rasa tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: kategori_rasa.php');
    exit;
}

$id_rasa = (int)$_GET['id'];

// Ambil nama rasa sebelum dihapus untuk pesan
$query_nama = "SELECT jenis_rasa FROM kategori_rasa WHERE id_rasa = $id_rasa";
$result_nama = mysqli_query($koneksi, $query_nama);
$rasa = mysqli_fetch_assoc($result_nama);
$nama_rasa = $rasa ? $rasa['jenis_rasa'] : '';

// Cek apakah ada produk yang menggunakan rasa ini
$check_produk = "SELECT COUNT(*) as total FROM produk WHERE kategori_produk LIKE '%$nama_rasa%'";
$result_check = mysqli_query($koneksi, $check_produk);
$produk_count = mysqli_fetch_assoc($result_check)['total'];

if ($produk_count > 0) {
    // Ada produk yang menggunakan rasa ini
    $_SESSION['message'] = "Tidak dapat menghapus rasa \"$nama_rasa\" karena masih ada $produk_count produk yang menggunakan rasa ini. Silahkan update produk terlebih dahulu.";
    $_SESSION['message_type'] = 'error';
    header('Location: kategori_rasa.php');
    exit;
}

// Hapus data
$query_delete = "DELETE FROM kategori_rasa WHERE id_rasa = $id_rasa";

if (mysqli_query($koneksi, $query_delete)) {
    $_SESSION['message'] = "Kategori rasa \"$nama_rasa\" berhasil dihapus!";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
}

header('Location: kategori_rasa.php');
exit;
>>>>>>> fcfb940 (update)
?>