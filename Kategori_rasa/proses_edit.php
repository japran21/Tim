<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kategori_rasa.php');
    exit;
}

if (!isset($_POST['id_rasa']) || empty($_POST['id_rasa'])) {
    $_SESSION['message'] = 'ID kategori rasa tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: kategori_rasa.php');
    exit;
}

$id_rasa = (int)$_POST['id_rasa'];
$jenis_rasa = trim($_POST['jenis_rasa']);

if (empty($jenis_rasa)) {
    $_SESSION['message'] = 'Jenis rasa tidak boleh kosong!';
    $_SESSION['message_type'] = 'error';
    header("Location: edit_kategori_rasa.php?id=$id_rasa");
    exit;
}

// Cek apakah rasa sudah ada (kecuali rasa yang sedang diedit)
$check = mysqli_query($koneksi, "SELECT id_rasa FROM kategori_rasa WHERE jenis_rasa = '$jenis_rasa' AND id_rasa != $id_rasa");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['message'] = 'Rasa "' . htmlspecialchars($jenis_rasa) . '" sudah terdaftar!';
    $_SESSION['message_type'] = 'error';
    header("Location: edit_kategori_rasa.php?id=$id_rasa");
    exit;
}

// Update data
$query = "UPDATE kategori_rasa SET jenis_rasa = '$jenis_rasa' WHERE id_rasa = $id_rasa";

if (mysqli_query($koneksi, $query)) {
    $_SESSION['message'] = 'Kategori rasa berhasil diupdate!';
    $_SESSION['message_type'] = 'success';
    header('Location: kategori_rasa.php');
} else {
    $_SESSION['message'] = 'Gagal mengupdate data: ' . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
    header("Location: edit_kategori_rasa.php?id=$id_rasa");
}
exit;
?>