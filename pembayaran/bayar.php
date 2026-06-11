<?php
session_start();
require_once '../koneksi.php';

// Pesan notifikasi
$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Ambil semua data metode pembayaran
$query = "SELECT * FROM metode_pembayaran ORDER BY id_metode ASC";
$result = mysqli_query($koneksi, $query);

// Ambil data transaksi pembayaran UMKM (untuk fitur undo)
$query_transaksi = "
    SELECT up.id_umkm_bayar,
           u.nama_umkm,
           mp.nama_metode
    FROM   umkm_pembayaran   up
    JOIN   umkm              u  ON u.id_umkm   = up.id_umkm
    JOIN   metode_pembayaran mp ON mp.id_metode = up.id_metode
    ORDER  BY up.id_umkm_bayar DESC
";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Metode Pembayaran - UMKM Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .admin-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 24px;
  }

  .admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 16px;
  }

  .admin-header h1 {
    font-family: 'Playfair Display', serif;
    color: #1a1a2e;
    font-size: 1.8rem;
  }

  .btn-tambah {
    background: #2e6b4f;
    color: white;
    padding: 10px 24px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-tambah:hover {
    background: #1a2e1e;
  }

  .message {
    padding: 12px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-weight: 500;
  }

  .message.success {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #10b981;
  }

  .message.error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #ef4444;
  }

  .payment-table {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th {
    background: #f8fafc;
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    color: #1a1a2e;
    border-bottom: 2px solid #e2e8f0;
  }

  td {
    padding: 14px 20px;
    border-bottom: 1px solid #e2e8f0;
    color: #374151;
  }

  tr:hover {
    background: #faf9f6;
  }

  .action-buttons {
    display: flex;
    gap: 8px;
  }

  .btn-edit,
  .btn-delete {
    padding: 6px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .btn-edit {
    background: #dbeafe;
    color: #1e40af;
  }

  .btn-edit:hover {
    background: #bfdbfe;
  }

  .btn-delete {
    background: #fee2e2;
    color: #991b1b;
  }

  .btn-delete:hover {
    background: #fecaca;
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 24px;
    color: #6b7280;
    text-decoration: none;
    font-weight: 500;
  }

  .back-link:hover {
    color: #2e6b4f;
  }

  .empty-state {
    text-align: center;
    padding: 48px;
    color: #9ca3af;
  }

  .method-icon {
    font-size: 1.5rem;
    margin-right: 8px;
    vertical-align: middle;
  }

  .btn-undo {
    background: #fff7ed;
    color: #9a3412;
    border: 1.5px solid #fed7aa;
    padding: 6px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .btn-undo:hover {
    background: #ffedd5;
    border-color: #fb923c;
  }
  </style>
</head>

<body>
  <header class="navbar">
    <div class="nav-inner">
      <div class="brand">
        <span class="brand-icon">🏪</span>
        <div class="brand-text">
          <span class="brand-name">STREET FOOD</span>
          <span class="brand-sub">Ciwaruga</span>
        </div>
      </div>
      <nav class="nav-links">
        <a href="index.php">Beranda</a>
        <a href="kategori_rasa.php">Kelola Rasa</a>
        <a href="bayar.php">Kelola Pembayaran</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="admin-container">
    <div class="admin-header">
      <h1> Manajemen Metode Pembayaran</h1>
      <a href="tambah_bayar.php" class="btn-tambah">+ Tambah Metode Baru</a>
    </div>

    <?php if ($message): ?>
    <div class="message <?= $messageType ?>">
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <div class="payment-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Metode Pembayaran</th>
            <th>Icon</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
          <?php 
                        $iconMap = [
                            'Cash' => '💵',
                            'QRIS' => '📱',
                            'Dana' => '💜',
                            'OVO' => '🟣',
                            'GoPay' => '🟢',
                            'LinkAja' => '🔵'
                        ];
                        while ($row = mysqli_fetch_assoc($result)): 
                            $icon = $iconMap[$row['nama_metode']] ?? '';
                        ?>
          <tr>
            <td><?= $row['id_metode'] ?></td>
            <td>
              <strong><?= htmlspecialchars($row['nama_metode']) ?></strong>
            </td>
            <td style="font-size: 1.5rem;"><?= $icon ?></td>
            <td>
              <div class="action-buttons">
                <a href="edit_bayar.php?id=<?= $row['id_metode'] ?>" class="btn-edit">✏️ Edit</a>
                <button
                  onclick="confirmDelete(<?= $row['id_metode'] ?>, '<?= htmlspecialchars($row['nama_metode']) ?>')"
                  class="btn-delete">🗑️ Hapus</button>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php else: ?>
          <tr>
            <td colspan="4">
              <div class="empty-state">
                📭 Belum ada data metode pembayaran
              </div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- ══ SECTION: Undo / Rollback Transaksi Pembayaran ══ -->
    <div class="admin-header" style="margin-top: 48px;">
      <h2 style="font-family:'Playfair Display',serif;color:#1a1a2e;font-size:1.5rem;">
        🔄 Undo Transaksi Pembayaran UMKM
      </h2>
    </div>
    <p style="color:#6b7280;margin-bottom:20px;">
      Tabel ini menampilkan semua data pembayaran yang sudah tercatat.
      Klik <strong>Undo</strong> untuk membatalkan (rollback) transaksi — data akan dihapus dan dicatat di log audit.
    </p>

    <div class="payment-table">
      <table>
        <thead>
          <tr>
            <th>ID Transaksi</th>
            <th>Nama UMKM</th>
            <th>Metode Pembayaran</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result_transaksi && mysqli_num_rows($result_transaksi) > 0): ?>
          <?php while ($trx = mysqli_fetch_assoc($result_transaksi)): ?>
          <tr>
            <td>#<?= $trx['id_umkm_bayar'] ?></td>
            <td><?= htmlspecialchars($trx['nama_umkm']) ?></td>
            <td><?= htmlspecialchars($trx['nama_metode']) ?></td>
            <td>
              <button
                onclick="confirmUndo(<?= $trx['id_umkm_bayar'] ?>, '<?= htmlspecialchars($trx['nama_umkm'], ENT_QUOTES) ?>', '<?= htmlspecialchars($trx['nama_metode'], ENT_QUOTES) ?>')"
                class="btn-undo">
                ↩️ Undo
              </button>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php else: ?>
          <tr>
            <td colspan="4">
              <div class="empty-state">📭 Tidak ada data transaksi pembayaran</div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <a href="index.php" class="back-link">← Kembali ke Beranda</a>
  </div>

  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <span class="brand-icon">🏪</span>
        <span class="brand-name"> Street Food</span> Ciwaruga
      </div>
      <p class="footer-copy">© 2026 Street Food Ciwaruga · Mendukung Usaha Lokal</p>
    </div>
  </footer>

  <script>
  function confirmUndo(id, namaUmkm, namaMetode) {
    if (confirm(
        `⚠️ KONFIRMASI ROLLBACK TRANSAKSI\n\nUMKM     : ${namaUmkm}\nMetode   : ${namaMetode}\nID Trx   : #${id}\n\nData pembayaran ini akan DIHAPUS dan dicatat di log audit.\nLanjutkan?`
      )) {
      window.location.href = `undo_bayar.php?id=${id}`;
    }
  }

  function confirmDelete(id, nama) {
    if (confirm(
        `Apakah Anda yakin ingin menghapus metode pembayaran "${nama}"?\n\nData UMKM yang menggunakan metode ini akan terpengaruh.`
      )) {
      window.location.href = `hapus_bayar.php?id=${id}`;
    }
  }
  </script>
</body>

</html>