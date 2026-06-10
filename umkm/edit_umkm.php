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

// Ambil data UMKM
$query = "SELECT * FROM umkm WHERE id_umkm = $id_umkm";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['message'] = 'Data UMKM tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: umkm.php');
    exit;
}

$umkm = mysqli_fetch_assoc($result);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_umkm = trim($_POST['nama_umkm']);
    $lokasi = trim($_POST['lokasi']);
    $nomor_kontak = !empty($_POST['nomor_kontak']) ? trim($_POST['nomor_kontak']) : NULL;
    $status_halal = $_POST['status_halal'];
    $no_sertifikat = !empty($_POST['no_sertifikat']) ? trim($_POST['no_sertifikat']) : NULL;
    $lembaga_penerbit = !empty($_POST['lembaga_penerbit']) ? trim($_POST['lembaga_penerbit']) : NULL;
    $tanggal_terbit = !empty($_POST['tanggal_terbit']) ? $_POST['tanggal_terbit'] : NULL;
    
    // Upload foto baru jika ada
    $foto = $umkm['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "FOTO_UMKM/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Hapus foto lama jika ada
        if ($umkm['foto'] && file_exists($umkm['foto'])) {
            unlink($umkm['foto']);
        }
        
        $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nama_umkm) . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto = $target_file;
        }
    }
    
    $errors = [];
    if (empty($nama_umkm)) $errors[] = 'Nama UMKM tidak boleh kosong';
    if (empty($lokasi)) $errors[] = 'Lokasi tidak boleh kosong';
    
    if (empty($errors)) {
        $foto_sql = $f