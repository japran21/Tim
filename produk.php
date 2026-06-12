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
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$filter_umkm = isset($_GET['id_umkm']) ? (int)$_GET['id_umkm'] : 0;
$filter_rasa = isset($_GET['id_rasa']) ? (int)$_GET['id_rasa'] : 0;

// Build query
$where = [];
if ($search) {
    $where[] = "(p.nama_produk LIKE '%$search%' OR u.nama_umkm LIKE '%$search%')";
}
if ($filter_umkm > 0) {
    $where[] = "p.id_umkm = $filter_umkm";
}
if ($filter_rasa > 0) {
    $where[] = "EXISTS (SELECT 1 FROM produk_rasa pr WHERE pr.id_produk = p.id_produk AND pr.id_rasa = $filter_rasa)";
}

$where_clause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Query produk dengan relasi
$query = "
    SELECT 
        p.id_produk,
        p.nama_produk,
        p.harga,
        p.kategori_produk,
        p.asal_daerah,
        u.id_umkm,
        u.nama_umkm,
        GROUP_CONCAT(DISTINCT kr.jenis_rasa SEPARATOR ', ') as daftar_rasa,
        GROUP_CONCAT(DISTINCT kr.id_rasa SEPARATOR ',') as id_rasa_list,
        GROUP_CONCAT(DISTINCT kt.nama_topping SEPARATOR ', ') as daftar_topping
    FROM produk p
    JOIN umkm u ON p.id_umkm = u.id_umkm
    LEFT JOIN produk_rasa pr ON p.id_produk = pr.id_produk
    LEFT JOIN kategori_rasa kr ON pr.id_rasa = kr.id_rasa
    LEFT JOIN produk_topping pt ON p.id_produk = pt.id_produk
    LEFT JOIN kategori_topping kt ON pt.id_topping = kt.id_topping
    $where_clause
    GROUP BY p.id_produk
    ORDER BY p.id_produk ASC
    LIMIT $offset, $limit
";

$result = mysqli_query($koneksi, $query);

// Hitung total data untuk pagination
$count_query = "
    SELECT COUNT(DISTINCT p.id_produk) as total
    FROM produk p
    JOIN umkm u ON p.id_umkm = u.id_umkm
    LEFT JOIN produk_rasa pr ON p.id_produk = pr.id_produk
    $where_clause
";
$count_result = mysqli_query($koneksi, $count_query);
$total_data = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data UMKM untuk filter
$query_umkm = "SELECT id_umkm, nama_umkm FROM umkm ORDER BY nama_umkm";
$result_umkm = mysqli_query($koneksi, $query_umkm);

// Ambil data rasa untuk filter
$query_rasa = "SELECT id_rasa, jenis_rasa FROM kategori_rasa ORDER BY id_rasa";
$result_rasa = mysqli_query($koneksi, $query_rasa);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Produk - UMKM Ciwaruga</title>
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

  .btn-tambah,
  .btn-relasi {
    padding: 10px 24px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-tambah {
    background: #2e6b4f;
    color: white;
  }

  .btn-tambah:hover {
    background: #1a2e1e;
  }

  .btn-relasi {
    background: #4f46e5;
    color: white;
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

  /* Product Table */
  .product-table {
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
  .btn-rasa,
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

  .btn-detail {
    background: #d1fae5;
    color: #065f46;
  }

  .btn-detail:hover {
    background: #a7f3d0;
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

  .btn-rasa {
    background: #fef3c7;
    color: #92400e;
  }

  .btn-rasa:hover {
    background: #fde68a;
  }

  .btn-topping {
    background: #fef08a;
    color: #854d0e;
  }

  .btn-topping:hover {
    background: #fde047;
  }

  .harga {
    font-weight: 600;
    color: #f59e0b;
  }

  .rasa-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.7rem;
    margin: 2px;
    background: #f3f4f6;
    color: #6b7280;
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

  .empty-state {
    text-align: center;
    padding: 48px;
    color: #9ca3af;
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
      <h1>Manajemen Produk</h1>
      <div class="btn-group">
        <a href="tambah_produk.php" class="btn-tambah">+ Tambah Produk Baru</a>
      </div>
    </div>

    <?php if ($message): ?>
    <div class="message <?= $messageType ?>">
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <?php
        // Hitung statistik
        $query_total_produk = "SELECT COUNT(*) as total FROM produk";
        $result_total = mysqli_query($koneksi, $query_total_produk);
        $total_produk = mysqli_fetch_assoc($result_total)['total'];
        
        $query_total_umkm = "SELECT COUNT(*) as total FROM umkm";
        $result_umkm_count = mysqli_query($koneksi, $query_total_umkm);
        $total_umkm = mysqli_fetch_assoc($result_umkm_count)['total'];
        ?>

    <div class="stats">
      <div class="stat-card">
        <div class="stat-number"><?= $total_produk ?></div>
        <div class="stat-label">Total Produk</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $total_umkm ?></div>
        <div class="stat-label">Total UMKM</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= number_format($total_produk / max($total_umkm, 1), 1) ?></div>
        <div class="stat-label">Rata-rata Produk/UMKM</div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
      <form method="GET" action="" class="filter-form">
        <div class="filter-group">
          <label>Cari Produk/UMKM</label>
          <input type="text" name="search" placeholder="Nama produk atau UMKM..."
            value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="filter-group">
          <label>Filter UMKM</label>
          <select name="id_umkm">
            <option value="0">Semua UMKM</option>
            <?php mysqli_data_seek($result_umkm, 0); ?>
            <?php while ($umkm = mysqli_fetch_assoc($result_umkm)): ?>
            <option value="<?= $umkm['id_umkm'] ?>" <?= $filter_umkm == $umkm['id_umkm'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($umkm['nama_umkm']) ?>
            </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="filter-group">
          <label>Filter Rasa</label>
          <select name="id_rasa">
            <option value="0">Semua Rasa</option>
            <?php mysqli_data_seek($result_rasa, 0); ?>
            <?php while ($rasa = mysqli_fetch_assoc($result_rasa)): ?>
            <option value="<?= $rasa['id_rasa'] ?>" <?= $filter_rasa == $rasa['id_rasa'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($rasa['jenis_rasa']) ?>
            </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="filter-group">
          <button type="submit" class="btn-filter">🔍 Filter</button>
          <a href="produk.php" class="btn-reset">↺ Reset</a>
        </div>
      </form>
    </div>

    <!-- Product Table -->
    <div class="product-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Produk</th>
            <th>UMKM</th>
            <th>Harga</th>
            <th>Kategori</th>
            <th>Rasa</th>
            <th>Topping</th>
            <th>Asal Daerah</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['id_produk'] ?></td>
            <td><strong><a href="detail_produk.php?id=<?= $row['id_produk'] ?>" style="color: #1a1a2e; text-decoration: none; border-bottom: 1px dashed #d1d5db; transition: color 0.2s;" onmouseover="this.style.color='#2e6b4f'" onmouseout="this.style.color='#1a1a2e'"><?= htmlspecialchars($row['nama_produk']) ?></a></strong></td>
            <td><?= htmlspecialchars($row['nama_umkm']) ?></td>
            <td class="harga">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
            <td>
              <span class="rasa-badge"><?= htmlspecialchars($row['kategori_produk']) ?></span>
            </td>
            <td>
              <?php 
                                    $rasa_list = explode(', ', $row['daftar_rasa']);
                                    foreach ($rasa_list as $r): 
                                        if ($r): ?>
              <span class="rasa-badge"><?= htmlspecialchars($r) ?></span>
              <?php 
                                        endif;
                                    endforeach; 
                                    if (empty($row['daftar_rasa'])): ?>
              <span class="rasa-badge" style="background:#fee2e2;">Belum ada rasa</span>
              <?php endif; ?>
            </td>
            <td>
              <?php 
                                    $topping_list = explode(', ', $row['daftar_topping'] ?? '');
                                    foreach ($topping_list as $t): 
                                        if ($t): ?>
              <span class="rasa-badge" style="background:#fef3c7; color:#92400e;"><?= htmlspecialchars($t) ?></span>
              <?php 
                                        endif;
                                    endforeach; 
                                    if (empty($row['daftar_topping'])): ?>
              <span class="rasa-badge" style="background:#f3f4f6;">-</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['asal_daerah'] ?: '-') ?></td>
            <td>
              <div class="action-buttons">
                <a href="detail_produk.php?id=<?= $row['id_produk'] ?>" class="btn-detail">Detail</a>
                <a href="edit_produk.php?id=<?= $row['id_produk'] ?>" class="btn-edit">Edit</a>
                <a href="relasi_produk_rasa.php?id_produk=<?= $row['id_produk'] ?>" class="btn-rasa">Rasa</a>
                <?php if ($row['kategori_produk'] === 'Makanan' || $row['kategori_produk'] === 'Minuman'): ?>
                <a href="relasi_produk_topping.php?id_produk=<?= $row['id_produk'] ?>" class="btn-topping" style="padding: 5px 12px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">Topping</a>
                <?php endif; ?>
                <button
                  onclick="confirmDelete(<?= $row['id_produk'] ?>, '<?= htmlspecialchars($row['nama_produk']) ?>')"
                  class="btn-delete">Hapus</button>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php else: ?>
          <tr>
            <td colspan="8">
              <div class="empty-state">
                📭 Tidak ada data produk
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
      <a
        href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&id_umkm=<?= $filter_umkm ?>&id_rasa=<?= $filter_rasa ?>">«
        Prev</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <?php if ($i == $page): ?>
      <span class="active"><?= $i ?></span>
      <?php else: ?>
      <a
        href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&id_umkm=<?= $filter_umkm ?>&id_rasa=<?= $filter_rasa ?>"><?= $i ?></a>
      <?php endif; ?>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
      <a
        href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&id_umkm=<?= $filter_umkm ?>&id_rasa=<?= $filter_rasa ?>">Next
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
        `Apakah Anda yakin ingin menghapus produk "${nama}"?\n\nData relasi rasa produk ini juga akan terhapus.`)) {
      window.location.href = `hapus_produk.php?id=${id}`;
    }
  }
  </script>
</body>

</html>