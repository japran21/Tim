<<<<<<< HEAD
<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID metode pembayaran tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: bayar.php');
    exit;
}

$id_metode = (int)$_GET['id'];

// Ambil nama metode sebelum dihapus untuk pesan
$query_nama = "SELECT nama_metode FROM metode_pembayaran WHERE id_metode = $id_metode";
$result_nama = mysqli_query($koneksi, $query_nama);
$metode = mysqli_fetch_assoc($result_nama);
$nama_metode = $metode ? $metode['nama_metode'] : '';

// Cek apakah ada UMKM yang menggunakan metode ini
$check_umkm = "SELECT COUNT(*) as total FROM umkm_pembayaran WHERE id_metode = $id_metode";
$result_check = mysqli_query($koneksi, $check_umkm);
$umkm_count = mysqli_fetch_assoc($result_check)['total'];

if ($umkm_count > 0) {
    $_SESSION['message'] = "Tidak dapat menghapus metode pembayaran \"$nama_metode\" karena masih ada $umkm_count UMKM yang menggunakan metode ini. Silahkan update UMKM terlebih dahulu.";
    $_SESSION['message_type'] = 'error';
    header('Location: bayar.php');
    exit;
}

// Hapus data
$query_delete = "DELETE FROM metode_pembayaran WHERE id_metode = $id_metode";

if (mysqli_query($koneksi, $query_delete)) {
    $_SESSION['message'] = "Metode pembayaran \"$nama_metode\" berhasil dihapus!";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
}

header('Location: bayar.php');
exit;
=======
<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID metode pembayaran tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: bayar.php');
    exit;
}

$id_metode = (int)$_GET['id'];

// Ambil nama metode sebelum dihapus untuk pesan
$query_nama = "SELECT nama_metode FROM metode_pembayaran WHERE id_metode = $id_metode";
$result_nama = mysqli_query($koneksi, $query_nama);
$metode = mysqli_fetch_assoc($result_nama);
$nama_metode = $metode ? $metode['nama_metode'] : '';

// Cek apakah ada UMKM yang menggunakan metode ini
$check_umkm = "SELECT COUNT(*) as total FROM umkm_pembayaran WHERE id_metode = $id_metode";
$result_check = mysqli_query($koneksi, $check_umkm);
$umkm_count = mysqli_fetch_assoc($result_check)['total'];

if ($umkm_count > 0) {
    $_SESSION['message'] = "Tidak dapat menghapus metode pembayaran \"$nama_metode\" karena masih ada $umkm_count UMKM yang menggunakan metode ini. Silahkan update UMKM terlebih dahulu.";
    $_SESSION['message_type'] = 'error';
    header('Location: bayar.php');
    exit;
}

// Hapus data
$query_delete = "DELETE FROM metode_pembayaran WHERE id_metode = $id_metode";

if (mysqli_query($koneksi, $query_delete)) {
    $_SESSION['message'] = "Metode pembayaran \"$nama_metode\" berhasil dihapus!";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
}

header('Location: bayar.php');
exit;
>>>>>>> fcfb940 (update)
?>