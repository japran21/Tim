<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah_kategori_rasa.php');
    exit;
}

$jenis_rasa = trim($_POST['jenis_rasa']);

if (empty($jenis_rasa)) {
    $_SESSION['message'] = 'Jenis rasa tidak boleh kosong!';
    $_SESSION['message_type'] = 'error';
    header('Location: tambah_kategori_rasa.php');
    exit;
}

// Cek apakah rasa sudah ada
$check = mysqli_query($koneksi, "SELECT id_rasa FROM kategori_rasa WHERE jenis_rasa = '$jenis_rasa'");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['message'] = 'Rasa "' . htmlspecialchars($jenis_rasa) . '" sudah terdaftar!';
    $_SESSION['message_type'] = 'error';
    header('Location: tambah_kategori_rasa.php');
    exit;
}

// Insert data
$query = "INSERT INTO kategori_rasa (jenis_rasa) VALUES ('$jenis_rasa')";

if (mysqli_query($koneksi, $query)) {
    $_SESSION['message'] = 'Kategori rasa "' . htmlspecialchars($jenis_rasa) . '" berhasil ditambahkan!';
    $_SESSION['message_type'] = 'success';
    header('Location: kategori_rasa.php');
} else {
    $_SESSION['message'] = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
    header('Location: tambah_kategori_rasa.php');
}
exit;
?>