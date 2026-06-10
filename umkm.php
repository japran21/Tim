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

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$filter_halal = isset($_GET['status_halal']) ? mysqli_real_escape_string($koneksi, $_GET['status_halal']) : '';

// Build query
$where = [];
if ($search) {
    $where[] = "(nama_umkm LIKE '%$search%' OR lokasi LIKE '%$search%' OR nomor_kontak LIKE '%$search%')";
}
if ($filter_halal && $filter_halal != 'all') {
    $where[] = "status_halal = '$filter_halal'";
}

$where_clause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Query UMKM
$query = "SELECT * FROM umkm $where_clause ORDER BY id_umkm ASC LIMIT $offset, $limit";
$result = mysqli_query($koneksi, $query);

// Hitung total data untuk pagination
$count_query = "SELECT COUNT(*) as total FROM umkm $where_clause";
$count_result = mysqli_query($koneksi, $count_query);
$total_data = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_data / $limit);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola UMKM - Street Food Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .admin-container {
    max-width: 1400px;
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

  .btn-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
  }

  .btn-tambah {
    background: #2e6b4f;
    color: white;
    padding: 10px 24px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-tambah:hover {
    background: #1a2e1e;
    transform: translateY(-2px);
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

  /* Filter Section */
  .filter-section {
    background: white;
    border-radius: 20px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: flex-end;
  }

  .filter-group {
    flex: 1;
    min-width: 180px;
  }

  .filter-group label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 4px;
  }

  .filter-group input,
  .filter-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-family: inherit;
  }

  .btn-filter,
  .btn-reset {
    padding: 10px 20px;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    cursor: pointer;
  }

  .btn-filter {
    background: #2e6b4f;
    color: white;
  }

  .btn-reset {
    background: #f3f4f6;
    color: #6b7280;
    text-decoration: none;
    display: inline-block;
    text-align: center;
  }

  /* Stats */
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

  /* UMKM Table */
  .umkm-table {
    background: white;
    border-radius: 20px;
    overflow-x: auto;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
  }

  th {
    background: #f8fafc;
    padding: 16px 16px;
    text-align: left;
    font-weight: 600;
    color: #1a1a2e;
    border-bottom: 2px solid #e2e8f0;
  }

  td {
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
    color: #374151;
  }

  tr:hover {
    background: #faf9f6;
  }

  .action-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }

  .btn-edit,
  .btn-delete,
  .btn-detail {
    padding: 5px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.75rem;
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

  .btn-detail {
    background: #e0e7ff;
    color: #3730a3;
  }

  .btn-detail:hover {
    background: #c7d2fe;
  }

  .status-halal {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
  }

  .status-bersertifikat {
    background: #d1fae5;
    color: #065f46;
  }

  .status-belum {
    background: #fef3c7;
    color: #92400e;
  }

  .status-non {
    background: #fee2e2;
    color: #991b1b;
  }

  /* Pagination */
  .pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
    flex-wrap: wrap;
  }

  .pagination a,
  .pagination span {
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    color: #2e6b4f;
    background: white;
    border: 1px solid #e2e8f0;
  }

  .pagination a:hover {
    background: #2e6b4f;
    color: white;
  }

  .pagination .active {
    background: #2e6b4f;
    color: white;
    border-color: #2e6b4f;
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

  .foto-preview {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 10px;
  }

  .no-foto {
    width: 50px;
    height: 50px;
    background: #f3f4f6;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
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
      <h1>🏪 Manajemen UMKM</h1>
      <div class="btn-group">
        <a href="tambah_umkm.php" class="btn-tambah">+ Tambah UMKM Baru</a>
      </div>
    </div>

    <?php if ($message): ?>
    <div class="message <?= $messageType ?>">
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <?php
        // Hitung statistik
        $query_total = "SELECT COUNT(*) as total FROM umkm";
        $result_total = mysqli_query($koneksi, $query_total);
        $total_umkm = mysqli_fetch_assoc($result_total)['total'];
        
        $query_bersertifikat = "SELECT COUNT(*) as total FROM umkm WHERE status_halal = 'Halal Bersertifikat'";
        $result_bersertifikat = mysqli_query($koneksi, $query_bersertifikat);
        $total_bersertifikat = mysqli_fetch_assoc($result_bersertifikat)['total'];
        ?>

    <div class="stats">
      <div class="stat-card">
        <div class="stat-number"><?= $total_umkm ?></div>
        <div class="stat-label">Total UMKM</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $total_bersertifikat ?></div>
        <div class="stat-label">Halal Bersertifikat</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $total_umkm - $total_bersertifikat ?></div>
        <div class="stat-label">Belum Bersertifikat</div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
      <form method="GET" action="" class="filter-form">
        <div class="filter-group">
          <label>Cari UMKM</label>
          <input type="text" name="search" placeholder="Nama, lokasi, atau kontak..."
            value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="filter-group">
          <label>Status Halal</label>
          <select name="status_halal">
            <option value="all">Semua</option>
            <option value="Halal Bersertifikat" <?= $filter_halal == 'Halal Bersertifikat' ? 'selected' : '' ?>>Halal
              Bersertifikat</option>
            <option value="Halal Belum Bersertifikat"
              <?= $filter_halal == 'Halal Belum Bersertifikat' ? 'selected' : '' ?>>Halal Belum Bersertifikat</option>
            <option value="Non-Halal" <?= $filter_halal == 'Non-Halal' ? 'selected' : '' ?>>Non-Halal</option>
          </select>
        </div>
        <div class="filter-group">
          <button type="submit" class="btn-filter">🔍 Filter</button>
          <a href="umkm.php" class="btn-reset">↺ Reset</a>
        </div>
      </form>
    </div>

    <!-- UMKM Table -->
    <div class="umkm-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Foto</th>
            <th>Nama UMKM</th>
            <th>Lokasi</th>
            <th>Kontak</th>
            <th>Status Halal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['id_umkm'] ?></td>
            <td>
              <?php if ($row['foto'] && file_exists($row['foto'])): ?>
              <img src="<?= htmlspecialchars($row['foto']) ?>" class="foto-preview" alt="Foto">
              <?php else: ?>
              <div class="no-foto">🏪</div>
              <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($row['nama_umkm']) ?></strong></td>
            <td style="max-width: 300px;"><?= htmlspecialchars(substr($row['lokasi'], 0, 50)) ?>...</td>
            <td><?= htmlspecialchars($row['nomor_kontak'] ?: '-') ?></td>
            <td>
              <?php
                                    $status_class = '';
                                    if ($row['status_halal'] == 'Halal Bersertifikat') {
                                        $status_class = 'status-bersertifikat';
                                    } elseif ($row['status_halal'] == 'Halal Belum Bersertifikat') {
                                        $status_class = 'status-belum';
                                    } else {
                                        $status_class = 'status-non';
                                    }
                                    ?>
              <span class="status-halal <?= $status_class ?>">
                <?= htmlspecialchars($row['status_halal']) ?>
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <a href="detail_umkm.php?id=<?= $row['id_umkm'] ?>" class="btn-detail">🔍 Detail</a>
                <a href="edit_umkm.php?id=<?= $row['id_umkm'] ?>" class="btn-edit">✏️ Edit</a>
                <button onclick="confirmDelete(<?= $row['id_umkm'] ?>, '<?= htmlspecialchars($row['nama_umkm']) ?>')"
                  class="btn-delete">🗑️ Hapus</button>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php else: ?>
          <tr>
            <td colspan="7">
              <div class="empty-state">
                📭 Tidak ada data UMKM
              </div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
      <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status_halal=<?= urlencode($filter_halal) ?>">«
        Prev</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <?php if ($i == $page): ?>
      <span class="active"><?= $i ?></span>
      <?php else: ?>
      <a
        href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status_halal=<?= urlencode($filter_halal) ?>"><?= $i ?></a>
      <?php endif; ?>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
      <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status_halal=<?= urlencode($filter_halal) ?>">Next
        »</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

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
        `Apakah Anda yakin ingin menghapus UMKM "${nama}"?\n\nSemua produk dan data terkait UMKM ini juga akan terhapus.`
        )) {
      window.location.href = `hapus_umkm.php?id=${id}`;
    }
  }
  </script>
</body>

</html>