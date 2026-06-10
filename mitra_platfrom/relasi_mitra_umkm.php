<<<<<<< HEAD
<?php
session_start();
require_once 'koneksi.php';

$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Proses tambah/update relasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id_umkm = (int)$_POST['id_umkm'];
    $mitra_ids = isset($_POST['mitra_ids']) ? $_POST['mitra_ids'] : [];
    $links = isset($_POST['links']) ? $_POST['links'] : [];
    
    // Hapus relasi lama
    mysqli_query($koneksi, "DELETE FROM umkm_mitra WHERE id_umkm = $id_umkm");
    
    // Insert relasi baru
    if (!empty($mitra_ids)) {
        foreach ($mitra_ids as $id_mitra) {
            $id_mitra = (int)$id_mitra;
            $link = isset($links[$id_mitra]) ? mysqli_real_escape_string($koneksi, trim($links[$id_mitra])) : '';
            
            $sql = "INSERT INTO umkm_mitra (id_umkm, id_mitra, link_mitra) 
                    VALUES ($id_umkm, $id_mitra, " . ($link ? "'$link'" : "NULL") . ")";
            mysqli_query($koneksi, $sql);
        }
    }
    
    $_SESSION['message'] = 'Relasi UMKM dengan mitra platform berhasil diupdate!';
    $_SESSION['message_type'] = 'success';
    header('Location: relasi_mitra_umkm.php');
    exit;
}

// Ambil semua UMKM
$query_umkm = "SELECT * FROM umkm ORDER BY id_umkm ASC";
$result_umkm = mysqli_query($koneksi, $query_umkm);

// Ambil semua mitra
$query_mitra = "SELECT * FROM mitra_platform ORDER BY id_mitra ASC";
$result_mitra = mysqli_query($koneksi, $query_mitra);
$mitra_list = [];
while ($m = mysqli_fetch_assoc($result_mitra)) {
    $mitra_list[] = $m;
}
mysqli_data_seek($result_mitra, 0);

// Ambil relasi yang sudah ada
$query_relasi = "SELECT * FROM umkm_mitra";
$result_relasi = mysqli_query($koneksi, $query_relasi);
$relasi_existing = [];
while ($r = mysqli_fetch_assoc($result_relasi)) {
    $relasi_existing[$r['id_umkm']][$r['id_mitra']] = $r['link_mitra'];
}

// Jika ada parameter id_umkm, tampilkan form edit untuk UMKM tersebut
$selected_umkm = isset($_GET['id_umkm']) ? (int)$_GET['id_umkm'] : 0;
$umkm_data = null;
if ($selected_umkm > 0) {
    $query_detail = "SELECT * FROM umkm WHERE id_umkm = $selected_umkm";
    $result_detail = mysqli_query($koneksi, $query_detail);
    $umkm_data = mysqli_fetch_assoc($result_detail);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
=======
<?php
session_start();
require_once 'koneksi.php';

$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Proses tambah/update relasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id_umkm = (int)$_POST['id_umkm'];
    $mitra_ids = isset($_POST['mitra_ids']) ? $_POST['mitra_ids'] : [];
    $links = isset($_POST['links']) ? $_POST['links'] : [];
    
    // Hapus relasi lama
    mysqli_query($koneksi, "DELETE FROM umkm_mitra WHERE id_umkm = $id_umkm");
    
    // Insert relasi baru
    if (!empty($mitra_ids)) {
        foreach ($mitra_ids as $id_mitra) {
            $id_mitra = (int)$id_mitra;
            $link = isset($links[$id_mitra]) ? mysqli_real_escape_string($koneksi, trim($links[$id_mitra])) : '';
            
            $sql = "INSERT INTO umkm_mitra (id_umkm, id_mitra, link_mitra) 
                    VALUES ($id_umkm, $id_mitra, " . ($link ? "'$link'" : "NULL") . ")";
            mysqli_query($koneksi, $sql);
        }
    }
    
    $_SESSION['message'] = 'Relasi UMKM dengan mitra platform berhasil diupdate!';
    $_SESSION['message_type'] = 'success';
    header('Location: relasi_mitra_umkm.php');
    exit;
}

// Ambil semua UMKM
$query_umkm = "SELECT * FROM umkm ORDER BY id_umkm ASC";
$result_umkm = mysqli_query($koneksi, $query_umkm);

// Ambil semua mitra
$query_mitra = "SELECT * FROM mitra_platform ORDER BY id_mitra ASC";
$result_mitra = mysqli_query($koneksi, $query_mitra);
$mitra_list = [];
while ($m = mysqli_fetch_assoc($result_mitra)) {
    $mitra_list[] = $m;
}
mysqli_data_seek($result_mitra, 0);

// Ambil relasi yang sudah ada
$query_relasi = "SELECT * FROM umkm_mitra";
$result_relasi = mysqli_query($koneksi, $query_relasi);
$relasi_existing = [];
while ($r = mysqli_fetch_assoc($result_relasi)) {
    $relasi_existing[$r['id_umkm']][$r['id_mitra']] = $r['link_mitra'];
}

// Jika ada parameter id_umkm, tampilkan form edit untuk UMKM tersebut
$selected_umkm = isset($_GET['id_umkm']) ? (int)$_GET['id_umkm'] : 0;
$umkm_data = null;
if ($selected_umkm > 0) {
    $query_detail = "SELECT * FROM umkm WHERE id_umkm = $selected_umkm";
    $result_detail = mysqli_query($koneksi, $query_detail);
    $umkm_data = mysqli_fetch_assoc($result_detail);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
>>>>>>> fcfb940 (update)
  <meta name="viewport" content="width=device-width, initial-scale=