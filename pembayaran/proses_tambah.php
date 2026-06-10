<<<<<<< HEAD
<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah_bayar.php');
    exit;
}

$nama_metode = trim($_POST['nama_metode']);

if (empty($nama_metode)) {
    $_SESSION['message'] = 'Nama metode pembayaran tidak boleh kosong!';
    $_SESSION['message_type'] = 'error';
    header('Location: tambah_bayar.php');
    exit;
}

// Cek apakah metode sudah ada
$check = mysqli_query($koneksi, "SELECT id_metode FROM metode_pembayaran WHERE nama_metode = '$nama_metode'");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['message'] = 'Metode pembayaran "' . htmlspecialchars($nama_metode) . '" sudah terdaftar!';
    $_SESSION['message_type'] = 'error';
    header('Location: tambah_bayar.php');
    exit;
}

// Insert data
$query = "INSERT INTO metode_pembayaran (nama_metode) VALUES ('$nama_metode')";

if (mysqli_query($koneksi, $query)) {
    $_SESSION['message'] = 'Metode pembayaran "' . htmlspecialchars($nama_metode) . '" berhasil ditambahkan!';
    $_SESSION['message_type'] = 'success';
    header('Location: bayar.php');
} else {
    $_SESSION['message'] = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
    header('Location: tambah_bayar.php');
}
exit;
=======
<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah_bayar.php');
    exit;
}

$nama_metode = trim($_POST['nama_metode']);

if (empty($nama_metode)) {
    $_SESSION['message'] = 'Nama metode pembayaran tidak boleh kosong!';
    $_SESSION['message_type'] = 'error';
    header('Location: tambah_bayar.php');
    exit;
}

// Cek apakah metode sudah ada
$check = mysqli_query($koneksi, "SELECT id_metode FROM metode_pembayaran WHERE nama_metode = '$nama_metode'");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['message'] = 'Metode pembayaran "' . htmlspecialchars($nama_metode) . '" sudah terdaftar!';
    $_SESSION['message_type'] = 'error';
    header('Location: tambah_bayar.php');
    exit;
}

// Insert data
$query = "INSERT INTO metode_pembayaran (nama_metode) VALUES ('$nama_metode')";

if (mysqli_query($koneksi, $query)) {
    $_SESSION['message'] = 'Metode pembayaran "' . htmlspecialchars($nama_metode) . '" berhasil ditambahkan!';
    $_SESSION['message_type'] = 'success';
    header('Location: bayar.php');
} else {
    $_SESSION['message'] = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
    header('Location: tambah_bayar.php');
}
exit;
>>>>>>> fcfb940 (update)
?>