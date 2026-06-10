<<<<<<< HEAD
<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID mitra platform tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: mitra.php');
    exit;
}

$id_mitra = (int)$_GET['id'];

// Ambil nama mitra sebelum dihapus untuk pesan
$query_nama = "SELECT nama_mitra FROM mitra_platform WHERE id_mitra = $id_mitra";
$result_nama = mysqli_query($koneksi, $query_nama);
$mitra = mysqli_fetch_assoc($result_nama);
$nama_mitra = $mitra ? $mitra['nama_mitra'] : '';

// Cek apakah ada UMKM yang menggunakan mitra ini
$check_umkm = "SELECT COUNT(*) as total FROM umkm_mitra WHERE id_mitra = $id_mitra";
$result_check = mysqli_query($koneksi, $check_umkm);
$umkm_count = mysqli_fetch_assoc($result_check)['total'];

if ($umkm_count > 0) {
    $_SESSION['message'] = "Tidak dapat menghapus mitra platform \"$nama_mitra\" karena masih ada $umkm_count UMKM yang terhubung dengan mitra ini. Silahkan hapus relasi terlebih dahulu.";
    $_SESSION['message_type'] = 'error';
    header('Location: mitra.php');
    exit;
}

// Hapus data
$query_delete = "DELETE FROM mitra_platform WHERE id_mitra = $id_mitra";

if (mysqli_query($koneksi, $query_delete)) {
    $_SESSION['message'] = "Mitra platform \"$nama_mitra\" berhasil dihapus!";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
}

header('Location: mitra.php');
exit;
=======
<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID mitra platform tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: mitra.php');
    exit;
}

$id_mitra = (int)$_GET['id'];

// Ambil nama mitra sebelum dihapus untuk pesan
$query_nama = "SELECT nama_mitra FROM mitra_platform WHERE id_mitra = $id_mitra";
$result_nama = mysqli_query($koneksi, $query_nama);
$mitra = mysqli_fetch_assoc($result_nama);
$nama_mitra = $mitra ? $mitra['nama_mitra'] : '';

// Cek apakah ada UMKM yang menggunakan mitra ini
$check_umkm = "SELECT COUNT(*) as total FROM umkm_mitra WHERE id_mitra = $id_mitra";
$result_check = mysqli_query($koneksi, $check_umkm);
$umkm_count = mysqli_fetch_assoc($result_check)['total'];

if ($umkm_count > 0) {
    $_SESSION['message'] = "Tidak dapat menghapus mitra platform \"$nama_mitra\" karena masih ada $umkm_count UMKM yang terhubung dengan mitra ini. Silahkan hapus relasi terlebih dahulu.";
    $_SESSION['message_type'] = 'error';
    header('Location: mitra.php');
    exit;
}

// Hapus data
$query_delete = "DELETE FROM mitra_platform WHERE id_mitra = $id_mitra";

if (mysqli_query($koneksi, $query_delete)) {
    $_SESSION['message'] = "Mitra platform \"$nama_mitra\" berhasil dihapus!";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
}

header('Location: mitra.php');
exit;
>>>>>>> fcfb940 (update)
?>