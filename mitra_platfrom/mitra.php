<?php
session_start();
require_once 'koneksi.php';

// Pesan notifikasi
$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Ambil semua data mitra platform
$query = "SELECT * FROM mitra_platform ORDER BY id_mitra ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Mitra Platform - UMKM Ciwaruga</title>
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

  .btn-relasi {
    background: #4f46e5;
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

  .btn-relasi:hover {
    background: #4338ca;
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

  .mitra-table {
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
  .btn-delete,
  .btn-link {
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

  .btn-link {
    background: #e0e7ff;
    color: #3730a3;
  }

  .btn-link:hover {
    background: #c7d2fe;
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

  .mitra-icon {
    font-size: 1.5rem;
    margin-right: 8px;
    vertical-align: middle;
  }

  .stats {
    display: flex;
    gap: 20px;
    margin-bottom: 24px;
    flex-wrap: wrap;
  }

  .stat-card {
    background: white;
    padding: 16px 24px;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    flex: 1;
    min-width: 150px;
  }

  .stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #2e6b4f;
  }

  .stat-label {
    color: #6b7280;
    font-size: 0.85rem;
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
        <a href="mitra.php">Kelola Mitra</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="admin-container">
    <div class="admin-header">
      <h1>🤝 Manajemen Mitra Platform</h1>
      <div style="display: flex; gap: 12px;">
        <a href="relasi_mitra_umkm.php" class="btn-relasi">🔗 Kelola Relasi UMKM-Mitra</a>
        <a href="tambah_mitra.php" class="btn-tambah">+ Tambah Mitra Baru</a>
      </div>
    </div>

    <?php if ($message): ?>
    <div class="message <?= $messageType ?>">
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <?php
        // Hitung statistik
        $total_mitra = mysqli_num_rows($result);
        $query_relasi = "SELECT COUNT(*) as total FROM umkm_mitra";
        $result_relasi = mysqli_query($koneksi, $query_relasi);
        $total_relasi = mysqli_fetch_assoc($result_relasi)['total'];
        mysqli_data_seek($result, 0);
        ?>

    <div class="stats">
      <div class="stat-card">
        <div class="stat-number"><?= $total_mitra ?></div>
        <div class="stat-label">Total Mitra Platform</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $total_relasi ?></div>
        <div class="stat-label">Total Relasi UMKM-Mitra</div>
      </div>
    </div>

    <div class="mitra-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Mitra</th>
            <th>Icon</th>
            <th>Warna</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($total_mitra > 0): ?>
          <?php 
                        $iconMap = [
                            'GoFood' => '🟢',
                            'GrabFood' => '🟠',
                            'ShopeeFood' => '🟡',
                            'Gojek' => '🟢',
                            'Grab' => '🟠',
                            'Shopee' => '🟡'
                        ];
                        $colorMap = [
                            'GoFood' => '#00b14f',
                            'GrabFood' => '#00b14f',
                            'ShopeeFood' => '#ee4d2d'
                        ];
                        while ($row = mysqli_fetch_assoc($result)): 
                            $icon = $iconMap[$row['nama_mitra']] ?? '📱';
                            $color = $colorMap[$row['nama_mitra']] ?? '#6b7280';
                        ?>
          <tr>
            <td><?= $row['id_mitra'] ?></td>
            <td>
              <strong><?= htmlspecialchars($row['nama_mitra']) ?></strong>
            </td>
            <td style="font-size: 1.5rem;"><?= $icon ?></td>
            <td>
              <span
                style="display: inline-block; width: 20px; height: 20px; background: <?= $color ?>; border-radius: 50%;"></span>
              <?= $color ?>
            </td>
            <td>
              <div class="action-buttons">
                <a href="edit_mitra.php?id=<?= $row['id_mitra'] ?>" class="btn-edit">✏️ Edit</a>
                <a href="detail_mitra.php?id=<?= $row['id_mitra'] ?>" class="btn-link">🔍 Detail</a>
                <button onclick="confirmDelete(<?= $row['id_mitra'] ?>, '<?= htmlspecialchars($row['nama_mitra']) ?>')"
                  class="btn-delete">🗑️ Hapus</button>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php else: ?>
          <tr>
            <td colspan="5">
              <div class="empty-state">
                📭 Belum ada data mitra platform
              </div>
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
  function confirmDelete(id, nama) {
    if (confirm(
        `Apakah Anda yakin ingin menghapus mitra platform "${nama}"?\n\nData UMKM yang terhubung dengan mitra ini akan terpengaruh.`
        )) {
      window.location.href = `hapus_mitra.php?id=${id}`;
    }
  }
  </script>
</body>

</html>