<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID mitra platform tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: mitra.php');
    exit;
}

$id_mitra = (int)$_GET['id'];

// 1. Ambil detail Mitra
$query = "SELECT * FROM mitra_platform WHERE id_mitra = $id_mitra";
$result = mysqli_query($koneksi, $query);
$mitra = mysqli_fetch_assoc($result);

if (!$mitra) {
    $_SESSION['message'] = 'Data mitra platform tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: mitra.php');
    exit;
}

// 2. Ambil list UMKM yang terhubung dengan mitra ini
$queryUmkm = "SELECT um.*, u.nama_umkm, u.lokasi, u.foto, u.status_halal 
              FROM umkm_mitra um 
              JOIN umkm u ON um.id_umkm = u.id_umkm 
              WHERE um.id_mitra = $id_mitra 
              ORDER BY u.nama_umkm ASC";
$resultUmkm = mysqli_query($koneksi, $queryUmkm);
$umkmList = [];
while ($row = mysqli_fetch_assoc($resultUmkm)) {
    $umkmList[] = $row;
}

// Icon dan warna
$iconMitra = [
    'GoFood' => '',
    'GrabFood' => '',
    'ShopeeFood' => '',
    'Gojek' => '',
    'Grab' => '',
    'Shopee' => '',
];
$colorMitra = [
    'GoFood' => '#00b14f',
    'GrabFood' => '#00b14f',
    'ShopeeFood' => '#ee4d2d',
    'Gojek' => '#00b14f',
    'Grab' => '#00b14f',
    'Shopee' => '#ee4d2d',
];

$icon = $iconMitra[$mitra['nama_mitra']] ?? '📱';
$color = $colorMitra[$mitra['nama_mitra']] ?? '#6b7280';
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Mitra - <?= htmlspecialchars($mitra['nama_mitra']) ?></title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 24px;
    display: flex;
    flex-direction: column;
    gap: 32px;
  }

  .card {
    background: white;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
  }

  .header-card {
    display: flex;
    align-items: center;
    gap: 24px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
  }

  .mitra-badge-icon {
    font-size: 3.5rem;
    padding: 16px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100px;
    height: 100px;
  }

  .mitra-title {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    color: #1a1a2e;
    margin-bottom: 6px;
  }

  .mitra-subtitle {
    color: #6b7280;
    font-weight: 500;
  }

  .section-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
    color: #1a1a2e;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .umkm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
  }

  .umkm-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .umkm-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  }

  .umkm-foto {
    height: 140px;
    width: 100%;
    object-fit: cover;
  }

  .umkm-no-foto {
    height: 140px;
    width: 100%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
  }

  .umkm-info {
    padding: 16px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .umkm-name {
    font-weight: 700;
    color: #1a1a2e;
    font-size: 1.1rem;
    margin-bottom: 4px;
  }

  .umkm-location {
    font-size: 0.8rem;
    color: #6b7280;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .halal-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 600;
    width: fit-content;
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

  .card-footer {
    display: flex;
    gap: 8px;
    margin-top: 16px;
  }

  .btn {
    flex: 1;
    padding: 10px;
    border-radius: 10px;
    text-align: center;
    font-weight: 600;
    font-size: 0.8rem;
    text-decoration: none;
  }

  .btn-view {
    background: #f1f5f9;
    color: #475569;
  }

  .btn-view:hover {
    background: #e2e8f0;
  }

  .btn-order {
    color: white;
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #6b7280;
    text-decoration: none;
    font-weight: 600;
  }

  .back-link:hover {
    color: #2e6b4f;
  }

  .empty-state {
    text-align: center;
    padding: 48px 0;
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
    <div>
      <a href="mitra.php" class="back-link">← Kembali ke Kelola Mitra</a>
    </div>
    <div class="card header-card">
      <div class="mitra-badge-icon">
        <?= $icon ?>
      </div>
      <div>
        <h1 class="mitra-title"><?= htmlspecialchars($mitra['nama_mitra']) ?></h1>
        <div class="mitra-subtitle">
          Platform Pemesanan Online dengan <strong><?= count($umkmList) ?></strong> UMKM yang Terdaftar
        </div>
      </div>
    </div>


    <div class="card">
      <div class="section-title">
        <span>🏪</span> UMKM Rekanan Terdaftar
      </div>

      <?php if (empty($umkmList)): ?>
      <div class="empty-state">
        <div style="font-size: 3rem; margin-bottom: 12px;">📭</div>
        <p>Belum ada UMKM yang terhubung dengan platform <strong><?= htmlspecialchars($mitra['nama_mitra']) ?></strong>.
        </p>
      </div>
      <?php else: ?>
      <div class="umkm-grid">
        <?php foreach ($umkmList as $row): 
            $status_class = '';
            if ($row['status_halal'] == 'Halal Bersertifikat') {
                $status_class = 'status-bersertifikat';
            } elseif ($row['status_halal'] == 'Halal Belum Bersertifikat') {
                $status_class = 'status-belum';
            } else {
                $status_class = 'status-non';
            }
        ?>
        <div class="umkm-card">
          <?php if ($row['foto'] && file_exists($row['foto'])): ?>
          <img src="<?= htmlspecialchars($row['foto']) ?>" class="umkm-foto" alt="Foto">
          <?php else: ?>
          <div class="umkm-no-foto">🏪</div>
          <?php endif; ?>

          <div class="umkm-info">
            <div>
              <div class="umkm-name"><?= htmlspecialchars($row['nama_umkm']) ?></div>
              <div class="umkm-location">📍 <?= htmlspecialchars($row['lokasi']) ?></div>
              <span class="halal-badge <?= $status_class ?>">
                <?= htmlspecialchars($row['status_halal']) ?>
              </span>
            </div>

            <div class="card-footer">
              <a href="detail_umkm.php?id=<?= $row['id_umkm'] ?>" class="btn btn-view">Profil</a>
              <?php if ($row['link_mitra']): ?>
              <a href="<?= htmlspecialchars($row['link_mitra']) ?>" target="_blank" class="btn btn-order"
                style="background: <?= $color ?>;">Order Link</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
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