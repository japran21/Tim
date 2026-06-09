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

// Ambil semua data kategori rasa
$query = "SELECT * FROM kategori_rasa ORDER BY id_rasa ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Kategori Rasa - UMKM Ciwaruga</title>
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

  .rasa-table {
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

  @media (max-width: 640px) {

    th,
    td {
      padding: 10px 12px;
    }

    .action-buttons {
      flex-direction: column;
    }

    .btn-edit,
    .btn-delete {
      text-align: center;
    }
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
        <a href="umkm.php">Kelola UMKM</a>
        <a href="produk.php">Kelola Produk</a>
        <a href="kategori_rasa.php">Kelola Rasa</a>
        <a href="bayar.php">Kelola Pembayaran</a>
        <a href="mitra.php">Kelola Mitra</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="admin-container">
    <div class="admin-header">
      <h1>📊 Manajemen Kategori Rasa</h1>
      <a href="tambah_kategori_rasa.php" class="btn-tambah">+ Tambah Rasa Baru</a>
    </div>

    <?php if ($message): ?>
    <div class="message <?= $messageType ?>">
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <div class="rasa-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Jenis Rasa</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['id_rasa'] ?></td>
            <td><?= htmlspecialchars($row['jenis_rasa']) ?></td>
            <td>
              <div class="action-buttons">
                <a href="edit_kategori_rasa.php?id=<?= $row['id_rasa'] ?>" class="btn-edit">✏️ Edit</a>
                <button onclick="confirmDelete(<?= $row['id_rasa'] ?>, '<?= htmlspecialchars($row['jenis_rasa']) ?>')"
                  class="btn-delete">🗑️ Hapus</button>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php else: ?>
          <tr>
            <td colspan="3">
              <div class="empty-state">
                📭 Belum ada data kategori rasa
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
        `Apakah Anda yakin ingin menghapus kategori rasa "${nama}"?\n\nData yang terkait dengan rasa ini mungkin akan terpengaruh.`
        )) {
      window.location.href = `hapus_kategori_rasa.php?id=${id}`;
    }
  }
  </script>
</body>

</html>