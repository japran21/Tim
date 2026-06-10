<?php
/**
 * undo_bayar.php
 * Rollback / batalkan data pembayaran UMKM yang sudah masuk.
 * 
 * Alur:
 *  1. Ambil data dari umkm_pembayaran berdasarkan id_umkm_bayar
 *  2. Simpan snapshot ke tabel log_undo_pembayaran (audit trail)
 *  3. Hapus baris dari umkm_pembayaran (rollback)
 *  4. Redirect ke bayar.php dengan pesan hasil
 */

session_start();
require_once '../koneksi.php';

// Validasi parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message']      = 'ID transaksi tidak valid!';
    $_SESSION['message_type'] = 'error';
    header('Location: bayar.php');
    exit;
}

$id_umkm_bayar = (int) $_GET['id'];

// ── 1. Ambil data transaksi yang akan di-rollback ────────────────────────────
$sql_get = "
    SELECT up.id_umkm_bayar,
           up.id_umkm,
           up.id_metode,
           u.nama_umkm,
           mp.nama_metode
    FROM   umkm_pembayaran   up
    JOIN   umkm              u  ON u.id_umkm   = up.id_umkm
    JOIN   metode_pembayaran mp ON mp.id_metode = up.id_metode
    WHERE  up.id_umkm_bayar = $id_umkm_bayar
    LIMIT  1
";

$result = mysqli_query($koneksi, $sql_get);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['message']      = 'Data transaksi tidak ditemukan atau sudah dihapus!';
    $_SESSION['message_type'] = 'error';
    header('Location: bayar.php');
    exit;
}

$row          = mysqli_fetch_assoc($result);
$id_umkm      = (int) $row['id_umkm'];
$id_metode    = (int) $row['id_metode'];
$nama_umkm    = $row['nama_umkm'];
$nama_metode  = $row['nama_metode'];

// ── 2. Buat tabel log jika belum ada (auto-create) ───────────────────────────
$sql_create_log = "
    CREATE TABLE IF NOT EXISTS `log_undo_pembayaran` (
        `id_log`         INT(11)      NOT NULL AUTO_INCREMENT,
        `id_umkm_bayar`  INT(11)      NOT NULL COMMENT 'ID asli sebelum dihapus',
        `id_umkm`        INT(11)      NOT NULL,
        `id_metode`      INT(11)      NOT NULL,
        `nama_umkm`      VARCHAR(255) NOT NULL,
        `nama_metode`    VARCHAR(100) NOT NULL,
        `di_undo_pada`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `keterangan`     VARCHAR(255) DEFAULT 'Rollback manual oleh admin',
        PRIMARY KEY (`id_log`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
mysqli_query($koneksi, $sql_create_log);

// ── 3. Simpan snapshot ke log (audit trail) ──────────────────────────────────
$sql_log = "
    INSERT INTO log_undo_pembayaran
        (id_umkm_bayar, id_umkm, id_metode, nama_umkm, nama_metode, keterangan)
    VALUES
        ($id_umkm_bayar, $id_umkm, $id_metode,
         '" . mysqli_real_escape_string($koneksi, $nama_umkm)   . "',
         '" . mysqli_real_escape_string($koneksi, $nama_metode) . "',
         'Undo transaksi via undo_bayar.php')
";

$log_ok = mysqli_query($koneksi, $sql_log);

if (!$log_ok) {
    // Jika log gagal, batalkan proses agar tidak ada data hilang tanpa jejak
    $_SESSION['message']      = 'Gagal menyimpan log rollback: ' . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
    header('Location: bayar.php');
    exit;
}

// ── 4. Hapus transaksi dari umkm_pembayaran (rollback) ───────────────────────
$sql_delete = "DELETE FROM umkm_pembayaran WHERE id_umkm_bayar = $id_umkm_bayar";

if (mysqli_query($koneksi, $sql_delete) && mysqli_affected_rows($koneksi) > 0) {
    $_SESSION['message']      = "✅ Transaksi berhasil di-rollback! Data pembayaran \"$nama_metode\" untuk UMKM \"$nama_umkm\" telah dibatalkan dan dicatat di log.";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message']      = 'Gagal melakukan rollback: ' . mysqli_error($koneksi);
    $_SESSION['message_type'] = 'error';
}

header('Location: bayar.php');
exit;
?>