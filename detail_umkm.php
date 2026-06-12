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

// 1. Ambil detail UMKM
$query = "SELECT * FROM umkm WHERE id_umkm = $id_umkm";
$result = mysqli_query($koneksi, $query);
$umkm = mysqli_fetch_assoc($result);

if (!$umkm) {
    $_SESSION['message'] = 'Data UMKM tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: umkm.php');
    exit;
}

// 2. Ambil waktu operasional
$queryWaktu = "SELECT * FROM waktu_operasional WHERE id_umkm = $id_umkm ORDER BY 
               FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')";
$resultWaktu = mysqli_query($koneksi, $queryWaktu);
$waktuList = [];
while ($row = mysqli_fetch_assoc($resultWaktu)) {
    $waktuList[] = $row;
}

// 3. Ambil mitra platform yang terhubung
$queryMitra = "SELECT um.*, mp.nama_mitra 
               FROM umkm_mitra um 
               JOIN mitra_platform mp ON um.id_mitra = mp.id_mitra 
               WHERE um.id_umkm = $id_umkm";
$resultMitra = mysqli_query($koneksi, $queryMitra);
$mitraList = [];
while ($row = mysqli_fetch_assoc($resultMitra)) {
    $mitraList[] = $row;
}

// 4. Ambil produk milik UMKM
$queryProduk = "SELECT p.*, 
                GROUP_CONCAT(kr.jenis_rasa SEPARATOR ', ') as daftar_rasa
                FROM produk p 
                LEFT JOIN produk_rasa pr ON p.id_produk = pr.id_produk 
                LEFT JOIN kategori_rasa kr ON pr.id_rasa = kr.id_rasa 
                WHERE p.id_umkm = $id_umkm 
                GROUP BY p.id_produk 
                ORDER BY p.nama_produk ASC";
$resultProduk = mysqli_query($koneksi, $queryProduk);
$produkList = [];
while ($row = mysqli_fetch_assoc($resultProduk)) {
    $produkList[] = $row;
}

// Emoji mapping untuk mitra
$iconMitra = [
    'GoFood' => '🟢',
    'GrabFood' => '🟠',
    'ShopeeFood' => '🟡',
    'Gojek' => '🟢',
    'Grab' => '🟠',
    'Shopee' => '🟡',
];
$colorMitra = [
    'GoFood'     => '#00b14f',
    'GrabFood'   => '#00b29c',
    'ShopeeFood' => '#ee4d2d',
    'Gojek'      => '#00b14f',
    'Grab'       => '#00b29c',
    'Shopee'     => '#ee4d2d',
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail UMKM - <?= htmlspecialchars($umkm['nama_umkm']) ?></title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 24px;
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 32px;
  }

  .sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
  }

  .main-content {
    display: flex;
    flex-direction: column;
    gap: 32px;
  }

  .card {
    background: white;
    border-radius: 24px;
    padding: 28px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
  }

  .card-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    color: #1a1a2e;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .profile-header {
    text-align: center;
    position: relative;
  }

  .profile-foto {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 16px;
    margin-bottom: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .profile-no-foto {
    width: 100%;
    height: 180px;
    background: #f1f5f9;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    margin-bottom: 16px;
  }

  .profile-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem;
    color: #1a1a2e;
    margin-bottom: 8px;
  }

  .halal-badge-large {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 40px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 8px;
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

  .info-list {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .info-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
  }

  .info-icon {
    font-size: 1.2rem;
  }

  .info-label {
    font-size: 0.8rem;
    color: #9ca3af;
    font-weight: 500;
  }

  .info-val {
    font-size: 0.95rem;
    color: #374151;
    font-weight: 600;
  }

  .schedule-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #f1f5f9;
    font-size: 0.9rem;
  }

  .schedule-row:last-child {
    border-bottom: none;
  }

  .schedule-day {
    font-weight: 600;
    color: #4b5563;
  }

  .schedule-time {
    color: #1a1a2e;
    font-weight: 500;
  }

  .schedule-closed {
    color: #ef4444;
    font-weight: 600;
  }

  .product-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }

  .product-card {
    background: #f8fafc;
    border-radius: 16px;
    padding: 18px;
    border: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.10);
    border-color: #d1fae5;
  }

  .product-detail h4 {
    font-size: 1.05rem;
    color: #1a1a2e;
    margin-bottom: 4px;
  }

  .product-category {
    font-size: 0.75rem;
    color: #9ca3af;
    text-transform: uppercase;
    font-weight: 600;
  }

  .product-price {
    font-size: 1.1rem;
    color: #f59e0b;
    font-weight: 700;
    margin-top: 8px;
  }

  .product-flavor-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
  }

  .flavor-badge {
    font-size: 0.7rem;
    background: #e2e8f0;
    color: #475569;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 500;
  }

  .mitra-links-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .mitra-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-radius: 14px;
    text-decoration: none;
    font-weight: 600;
    color: white;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s;
  }

  .mitra-btn:hover {
    transform: translateY(-2px);
  }

  .back-btn-container {
    grid-column: span 2;
    margin-bottom: -16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
  }

  .detail-action-btns {
    display: flex;
    gap: 10px;
  }

  .btn-edit-detail {
    background: #2e6b4f;
    color: white;
    padding: 8px 20px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: background 0.2s;
  }

  .btn-edit-detail:hover {
    background: #1a4a35;
  }

  .btn-hapus-detail {
    background: #fee2e2;
    color: #991b1b;
    padding: 8px 20px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
  }

  .btn-hapus-detail:hover {
    background: #fecaca;
  }

  .back-link-top {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #6b7280;
    text-decoration: none;
    font-weight: 600;
  }

  .back-link-top:hover {
    color: #2e6b4f;
  }

  @media (max-width: 900px) {
    .container {
      grid-template-columns: 1fr;
    }

    .back-btn-container {
      grid-column: span 1;
    }

    .product-grid {
      grid-template-columns: 1fr;
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

  <div class="container">
    <div class="back-btn-container">
      <a href="umkm.php" class="back-link-top">← Kembali ke Kelola UMKM</a>
      <div class="detail-action-btns">
        <a href="edit_umkm.php?id=<?= $id_umkm ?>" class="btn-edit-detail">Edit UMKM</a>
        <?php $nama_konfirm = htmlspecialchars($umkm['nama_umkm'], ENT_QUOTES); ?>
        <button class="btn-hapus-detail"
          onclick="if(confirm('Yakin ingin menghapus UMKM &quot;<?= $nama_konfirm ?>&quot; beserta semua data terkaitnya?')) window.location.href='hapus_umkm.php?id=<?= $id_umkm ?>'">
          Hapus
        </button>
      </div>
    </div>

    <!-- SIDEBAR Profile -->
    <div class="sidebar">
      <div class="card" style="text-align: center;">
        <div class="profile-header">
          <?php if ($umkm['foto'] && file_exists($umkm['foto'])): ?>
          <img src="<?= htmlspecialchars($umkm['foto']) ?>" class="profile-foto" alt="Foto">
          <?php else: ?>
          <div class="profile-no-foto">🏪</div>
          <?php endif; ?>

          <h2 class="profile-name"><?= htmlspecialchars($umkm['nama_umkm']) ?></h2>

          <?php
          $status_class = '';
          if ($umkm['status_halal'] == 'Halal Bersertifikat') {
              $status_class = 'status-bersertifikat';
          } elseif ($umkm['status_halal'] == 'Halal Belum Bersertifikat') {
              $status_class = 'status-belum';
          } else {
              $status_class = 'status-non';
          }
          ?>
          <span class="halal-badge-large <?= $status_class ?>">
            <?= htmlspecialchars($umkm['status_halal']) ?>
          </span>
        </div>

        <div class="info-list">
          <div class="info-item">
            <span class="info-icon">📍</span>
            <div style="text-align: left;">
              <div class="info-label">LOKASI / ALAMAT</div>
              <div class="info-val"><?= htmlspecialchars($umkm['lokasi']) ?></div>
            </div>
          </div>

          <div class="info-item">
            <span class="info-icon">📞</span>
            <div style="text-align: left;">
              <div class="info-label">NOMOR KONTAK</div>
              <div class="info-val"><?= htmlspecialchars($umkm['nomor_kontak'] ?: '-') ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sertifikasi Halal Detail -->
      <?php if ($umkm['status_halal'] === 'Halal Bersertifikat'): ?>
      <div class="card">
        <div class="card-title">📜 Sertifikasi Halal</div>
        <div class="info-list">
          <div class="info-item">
            <div style="text-align: left;">
              <div class="info-label">NOMOR SERTIFIKAT</div>
              <div class="info-val"><?= htmlspecialchars($umkm['no_sertifikat'] ?: '-') ?></div>
            </div>
          </div>
          <div class="info-item">
            <div style="text-align: left;">
              <div class="info-label">LEMBAGA PENERBIT</div>
              <div class="info-val"><?= htmlspecialchars($umkm['lembaga_penerbit'] ?: '-') ?></div>
            </div>
          </div>
          <div class="info-item">
            <div style="text-align: left;">
              <div class="info-label">TANGGAL TERBIT</div>
              <div class="info-val">
                <?= $umkm['tanggal_terbit'] ? date('d-m-Y', strtotime($umkm['tanggal_terbit'])) : '-' ?></div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>


    <div class="main-content">

      <div class="card">
        <div class="card-title">Jam Operasional Mingguan</div>
        <div>
          <?php if (empty($waktuList)): ?>
          <p style="color: #6b7280; font-style: italic;">Jam operasional belum diatur untuk UMKM ini. <a
              href="waktu_operasional.php?id_umkm=<?= $id_umkm ?>">Atur sekarang</a>.</p>
          <?php else: ?>
          <?php foreach ($waktuList as $waktu): 
              $isTutup = $waktu['keterangan'] === 'Tutup';
          ?>
          <div class="schedule-row">
            <span class="schedule-day"><?= htmlspecialchars($waktu['hari']) ?></span>
            <?php if ($isTutup): ?>
            <span class="schedule-closed">🚫 Tutup / Libur</span>
            <?php else: ?>
            <span class="schedule-time">
              🟢 <?= date('H:i', strtotime($waktu['jam_buka'])) ?> - <?= date('H:i', strtotime($waktu['jam_tutup'])) ?>
              WIB
            </span>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-title">Menu & Produk (<?= count($produkList) ?>)</div>
        <div class="product-grid">
          <?php if (empty($produkList)): ?>
          <p style="color: #6b7280; font-style: italic; grid-column: span 2;">Belum ada produk yang didaftarkan untuk
            UMKM ini.</p>
          <?php else: ?>
          <?php foreach ($produkList as $produk): ?>
          <a href="detail_produk.php?id=<?= $produk['id_produk'] ?>" class="product-card"
            style="text-decoration:none;cursor:pointer;">
            <div class="product-detail">
              <span class="product-category">
                <?php 
                if ($produk['kategori_produk'] === 'Makanan') echo '';
                elseif ($produk['kategori_produk'] === 'Minuman') echo '';
                elseif ($produk['kategori_produk'] === 'Topping') echo '';
                else echo '🍿 ';
                echo htmlspecialchars($produk['kategori_produk']);
                ?>
              </span>
              <h4><?= htmlspecialchars($produk['nama_produk']) ?></h4>
              <?php if ($produk['asal_daerah']): ?>
              <span style="font-size: 0.75rem; color: #6b7280; display: block; margin-top: 2px;">📍 Asal:
                <?= htmlspecialchars($produk['asal_daerah']) ?></span>
              <?php endif; ?>

              <?php if ($produk['daftar_rasa']): ?>
              <div class="product-flavor-tags">
                <?php foreach (explode(', ', $produk['daftar_rasa']) as $rasa): ?>
                <span class="flavor-badge"><?= htmlspecialchars($rasa) ?></span>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>
            <div class="product-price">
              Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
            </div>
          </a>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>


      <div class="card">
        <div class="card-title">🤝 Pesan Online Melalui Mitra</div>
        <div class="mitra-links-grid">
          <?php if (empty($mitraList)): ?>
          <p style="color: #6b7280; font-style: italic;">UMKM ini belum terhubung dengan mitra pemesanan online.</p>
          <?php else: ?>
          <?php foreach ($mitraList as $mitra): 
              $icon = $iconMitra[$mitra['nama_mitra']] ?? '📱';
              $color = $colorMitra[$mitra['nama_mitra']] ?? '#4b5563';
          ?>
          <a href="<?= htmlspecialchars($mitra['link_mitra'] ?: '#') ?>" target="_blank" class="mitra-btn"
            style="background: <?= $color ?>;">
            <span><?= $icon ?> Pesan via <?= htmlspecialchars($mitra['nama_mitra']) ?></span>
            <span>Buka Aplikasi ➔</span>
          </a>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
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
</body>

</html>