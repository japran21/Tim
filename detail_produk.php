<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID Produk tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: produk.php');
    exit;
}

$id_produk = (int)$_GET['id'];

// 1. Ambil detail produk beserta UMKM
$query = "
    SELECT 
        p.*,
        u.id_umkm,
        u.nama_umkm,
        u.lokasi,
        u.nomor_kontak,
        u.foto,
        u.status_halal,
        u.no_sertifikat,
        u.lembaga_penerbit,
        u.tanggal_terbit
    FROM produk p
    JOIN umkm u ON p.id_umkm = u.id_umkm
    WHERE p.id_produk = $id_produk
";
$result = mysqli_query($koneksi, $query);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    $_SESSION['message'] = 'Data Produk tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: produk.php');
    exit;
}

// 2. Ambil daftar rasa produk ini
$queryRasa = "
    SELECT kr.id_rasa, kr.jenis_rasa
    FROM produk_rasa pr
    JOIN kategori_rasa kr ON pr.id_rasa = kr.id_rasa
    WHERE pr.id_produk = $id_produk
    ORDER BY kr.jenis_rasa ASC
";
$resultRasa = mysqli_query($koneksi, $queryRasa);
$rasaList = [];
while ($row = mysqli_fetch_assoc($resultRasa)) {
    $rasaList[] = $row;
}

// 3. Ambil waktu operasional UMKM
$id_umkm = (int)$produk['id_umkm'];
$queryWaktu = "SELECT * FROM waktu_operasional WHERE id_umkm = $id_umkm ORDER BY 
               FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')";
$resultWaktu = mysqli_query($koneksi, $queryWaktu);
$waktuList = [];
while ($row = mysqli_fetch_assoc($resultWaktu)) {
    $waktuList[] = $row;
}

// 4. Ambil produk lain dari UMKM yang sama (untuk "Produk Lainnya")
$queryLainnya = "
    SELECT p.id_produk, p.nama_produk, p.harga, p.kategori_produk,
           GROUP_CONCAT(kr.jenis_rasa SEPARATOR ', ') as daftar_rasa,
           u.foto
    FROM produk p
    JOIN umkm u ON p.id_umkm = u.id_umkm
    LEFT JOIN produk_rasa pr ON p.id_produk = pr.id_produk
    LEFT JOIN kategori_rasa kr ON pr.id_rasa = kr.id_rasa
    WHERE p.id_umkm = $id_umkm AND p.id_produk != $id_produk
    GROUP BY p.id_produk, u.foto
    ORDER BY p.nama_produk ASC
    LIMIT 6
";
$resultLainnya = mysqli_query($koneksi, $queryLainnya);
$produkLainnya = [];
while ($row = mysqli_fetch_assoc($resultLainnya)) {
    $produkLainnya[] = $row;
}

// 5. Ambil metode pembayaran yang diterima UMKM
$queryBayar = "
    SELECT up.id_umkm_bayar AS id_bayar, mp.nama_metode AS metode_pembayaran
    FROM umkm_pembayaran up
    JOIN metode_pembayaran mp ON up.id_metode = mp.id_metode
    WHERE up.id_umkm = $id_umkm
    ORDER BY mp.nama_metode ASC
";
$resultBayar = mysqli_query($koneksi, $queryBayar);
$bayarList = [];
if ($resultBayar) {
    while ($row = mysqli_fetch_assoc($resultBayar)) {
        $bayarList[] = $row;
    }
}

// 6. Ambil mitra platform
$queryMitra = "SELECT um.*, mp.nama_mitra 
               FROM umkm_mitra um 
               JOIN mitra_platform mp ON um.id_mitra = mp.id_mitra 
               WHERE um.id_umkm = $id_umkm";
$resultMitra = mysqli_query($koneksi, $queryMitra);
$mitraList = [];
if ($resultMitra) {
    while ($row = mysqli_fetch_assoc($resultMitra)) {
        $mitraList[] = $row;
    }
}

// Emoji maps
$emojiKategori = [
    'Makanan' => '🍜',
    'Minuman' => '🥤',
    'Topping' => '🍯',
];
$emojiRasa = [
    'Asin'  => '🧂',
    'Gurih' => '🍗',
    'Manis' => '🍯',
    'Pedas' => '🌶️',
    'Asam'  => '🍋',
];
$iconMitra = [
    'GoFood' => '🟢', 'GrabFood' => '🟠', 'ShopeeFood' => '🟡',
    'Gojek' => '🟢', 'Grab' => '🟠', 'Shopee' => '🟡',
];
$colorMitra = [
    'GoFood' => '#00b14f', 'GrabFood' => '#00b14f', 'ShopeeFood' => '#ee4d2d',
    'Gojek' => '#00b14f', 'Grab' => '#00b14f', 'Shopee' => '#ee4d2d',
];

// Tentukan status buka/tutup hari ini
$hariMap = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
$hariIni = $hariMap[date('l')] ?? '';
$statusBuka = null;
foreach ($waktuList as $w) {
    if ($w['hari'] === $hariIni) {
        if ($w['keterangan'] === 'Tutup') {
            $statusBuka = 'tutup';
        } else {
            $jamBuka  = $w['jam_buka'];
            $jamTutup = $w['jam_tutup'];
            $now = date('H:i:s');
            if ($now >= $jamBuka && $now <= $jamTutup) {
                $statusBuka = 'buka';
            } else {
                $statusBuka = 'tutup';
            }
        }
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($produk['nama_produk']) ?> - Street Food Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">
  <style>
  /* ===== Detail Produk Layout ===== */
  .detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 24px 60px;
  }

  .breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #9ca3af;
    margin-bottom: 28px;
  }

  .breadcrumb a {
    color: #6b7280;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
  }

  .breadcrumb a:hover {
    color: #2e6b4f;
  }

  .breadcrumb .sep {
    color: #d1d5db;
  }

  .breadcrumb .current {
    color: #1a1a2e;
    font-weight: 600;
  }

  /* Main product grid */
  .product-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1.4fr;
    gap: 36px;
    margin-bottom: 48px;
  }

  /* Left: Product visual card */
  .product-visual {
    position: sticky;
    top: 100px;
    align-self: start;
  }

  .product-hero-card {
    background: white;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
  }

  .product-hero-top {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7, #bbf7d0);
    height: 240px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6rem;
    position: relative;
    overflow: hidden;
  }

  .product-hero-top .hero-foto {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .4s ease;
  }

  .product-hero-card:hover .product-hero-top .hero-foto {
    transform: scale(1.04);
  }

  .product-hero-top::before {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    top: -40px;
    right: -40px;
  }

  .product-hero-top::after {
    content: '';
    position: absolute;
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    bottom: -30px;
    left: -20px;
  }

  .product-hero-body {
    padding: 28px;
  }

  .product-kategori-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f0fdf4;
    color: #166534;
    padding: 6px 14px;
    border-radius: 40px;
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
  }

  .product-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 900;
    color: #1a1a2e;
    margin-bottom: 8px;
    line-height: 1.2;
  }

  .product-asal {
    font-size: 0.85rem;
    color: #6b7280;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .product-price-big {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.6rem;
    font-weight: 700;
    color: #f59e0b;
    margin-bottom: 20px;
  }

  .rasa-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .rasa-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 14px;
    border-radius: 40px;
    font-size: 0.82rem;
    font-weight: 600;
    transition: transform 0.2s;
  }

  .rasa-tag:hover {
    transform: scale(1.05);
  }

  .rasa-Asin {
    background: #dbeafe;
    color: #1d4ed8;
  }

  .rasa-Gurih {
    background: #fef3c7;
    color: #b45309;
  }

  .rasa-Manis {
    background: #fce7f3;
    color: #be185d;
  }

  .rasa-Pedas {
    background: #fee2e2;
    color: #b91c1c;
  }

  .rasa-Asam {
    background: #dcfce7;
    color: #15803d;
  }

  /* Right: Detail info */
  .product-info-col {
    display: flex;
    flex-direction: column;
    gap: 24px;
  }

  .info-card {
    background: white;
    border-radius: 24px;
    padding: 28px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
    transition: transform 0.2s;
  }

  .info-card:hover {
    transform: translateY(-2px);
  }

  .info-card-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.15rem;
    color: #1a1a2e;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  /* UMKM Profile card */
  .umkm-profile {
    display: flex;
    gap: 18px;
    align-items: center;
    margin-bottom: 20px;
  }

  .umkm-foto {
    width: 72px;
    height: 72px;
    border-radius: 16px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    flex-shrink: 0;
  }

  .umkm-no-foto {
    width: 72px;
    height: 72px;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
  }

  .umkm-info h3 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 4px;
  }

  .umkm-info h3 a {
    color: #1a1a2e;
    text-decoration: none;
    transition: color 0.2s;
  }

  .umkm-info h3 a:hover {
    color: #2e6b4f;
  }

  .umkm-lokasi {
    font-size: 0.82rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.72rem;
    font-weight: 600;
    margin-top: 6px;
  }

  .status-buka {
    background: #d1fae5;
    color: #065f46;
  }

  .status-tutup {
    background: #fee2e2;
    color: #991b1b;
  }

  .umkm-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  .meta-item {
    background: #f8fafc;
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px solid #f1f5f9;
  }

  .meta-label {
    font-size: 0.7rem;
    color: #9ca3af;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }

  .meta-value {
    font-size: 0.9rem;
    color: #374151;
    font-weight: 600;
  }

  /* Schedule */
  .schedule-grid {
    display: flex;
    flex-direction: column;
    gap: 0;
  }

  .schedule-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px dashed #f1f5f9;
    font-size: 0.88rem;
  }

  .schedule-item:last-child {
    border-bottom: none;
  }

  .schedule-item.today {
    background: #f0fdf4;
    margin: 0 -16px;
    padding: 12px 16px;
    border-radius: 10px;
    border-bottom: none;
  }

  .schedule-day {
    font-weight: 600;
    color: #4b5563;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .today-label {
    font-size: 0.65rem;
    background: #2e6b4f;
    color: white;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 600;
  }

  .schedule-time {
    color: #1a1a2e;
    font-weight: 500;
  }

  .schedule-closed {
    color: #ef4444;
    font-weight: 600;
  }

  /* Payment methods */
  .payment-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .payment-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 0.82rem;
    color: #374151;
    font-weight: 500;
  }

  /* Mitra list */
  .mitra-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .mitra-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-radius: 14px;
    text-decoration: none;
    font-weight: 600;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .mitra-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  }

  /* Other products section */
  .other-products-section {
    margin-top: 16px;
  }

  .section-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .other-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
  }

  .other-product-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f5f9;
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none;
    display: block;
  }

  .other-product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.1);
  }

  .other-card-top {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.4rem;
    overflow: hidden;
  }

  .other-card-top .other-foto {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
  }

  .other-product-card:hover .other-card-top .other-foto {
    transform: scale(1.06);
  }

  .other-card-body {
    padding: 16px;
  }

  .other-card-name {
    font-weight: 600;
    color: #1a1a2e;
    font-size: 0.9rem;
    margin-bottom: 4px;
  }

  .other-card-category {
    font-size: 0.72rem;
    color: #9ca3af;
    text-transform: uppercase;
    font-weight: 600;
  }

  .other-card-price {
    font-weight: 700;
    color: #f59e0b;
    font-size: 0.95rem;
    margin-top: 8px;
  }

  .other-card-rasa {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 6px;
  }

  .other-card-rasa span {
    font-size: 0.65rem;
    background: #f3f4f6;
    color: #6b7280;
    padding: 2px 6px;
    border-radius: 20px;
    font-weight: 500;
  }

  /* Halal badge */
  .halal-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 0.72rem;
    font-weight: 600;
    margin-top: 6px;
  }

  .halal-bersertifikat {
    background: #d1fae5;
    color: #065f46;
  }

  .halal-belum {
    background: #fef3c7;
    color: #92400e;
  }

  .halal-non {
    background: #fee2e2;
    color: #991b1b;
  }

  /* Responsive */
  @media (max-width: 900px) {
    .product-detail-grid {
      grid-template-columns: 1fr;
    }

    .product-visual {
      position: static;
    }

    .umkm-meta-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 500px) {
    .product-hero-top {
      height: 180px;
      font-size: 4rem;
    }

    .product-title {
      font-size: 1.4rem;
    }

    .other-products-grid {
      grid-template-columns: 1fr 1fr;
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

  <div class="detail-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="index.php">🏠 Beranda</a>
      <span class="sep">›</span>
      <a href="produk.php">Produk</a>
      <span class="sep">›</span>
      <span class="current"><?= htmlspecialchars($produk['nama_produk']) ?></span>
    </div>

    <!-- Main Grid -->
    <div class="product-detail-grid">
      <!-- LEFT: Product Visual -->
      <div class="product-visual">
        <div class="product-hero-card">
          <div class="product-hero-top">
            <?php
              $fotoPath = $produk['foto'] ?? '';
              // strip prefix jika masih ada
              $fotoPath = ltrim(str_replace('FOTO_UMKM/', '', $fotoPath), '/');
            ?>
            <?php if ($fotoPath && file_exists('FOTO_UMKM/' . $fotoPath)): ?>
            <img src="FOTO_UMKM/<?= htmlspecialchars($fotoPath) ?>" class="hero-foto"
              alt="<?= htmlspecialchars($produk['nama_umkm']) ?>">
            <?php else: ?>
            <?= $emojiKategori[$produk['kategori_produk']] ?? '🍿' ?>
            <?php endif; ?>
          </div>
          <div class="product-hero-body">
            <span class="product-kategori-badge">
              <?= $emojiKategori[$produk['kategori_produk']] ?? '🍿' ?>
              <?= htmlspecialchars($produk['kategori_produk']) ?>
            </span>

            <h1 class="product-title"><?= htmlspecialchars($produk['nama_produk']) ?></h1>

            <?php if ($produk['asal_daerah']): ?>
            <div class="product-asal">
              📍 Asal Daerah: <?= htmlspecialchars($produk['asal_daerah']) ?>
            </div>
            <?php endif; ?>

            <div class="product-price-big">
              Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
            </div>

            <?php if (!empty($rasaList)): ?>
            <div class="rasa-tags">
              <?php foreach ($rasaList as $rasa): 
                $namaRasa = htmlspecialchars($rasa['jenis_rasa']);
                $emoji = $emojiRasa[$rasa['jenis_rasa']] ?? '🍽️';
              ?>
              <span class="rasa-tag rasa-<?= $namaRasa ?>">
                <?= $emoji ?> <?= $namaRasa ?>
              </span>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="color: #9ca3af; font-size: 0.85rem; font-style: italic;">Belum ada kategori rasa yang
              ditentukan.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- RIGHT: Info Cards -->
      <div class="product-info-col">
        <!-- UMKM Profile Card -->
        <div class="info-card">
          <div class="info-card-title">🏪 Informasi UMKM</div>
          <div class="umkm-profile">
            <?php if ($produk['foto'] && file_exists($produk['foto'])): ?>
            <img src="<?= htmlspecialchars($produk['foto']) ?>" class="umkm-foto" alt="Foto UMKM">
            <?php else: ?>
            <div class="umkm-no-foto">🏪</div>
            <?php endif; ?>

            <div class="umkm-info">
              <h3><a href="detail_umkm.php?id=<?= $id_umkm ?>"><?= htmlspecialchars($produk['nama_umkm']) ?></a></h3>
              <div class="umkm-lokasi">📍 <?= htmlspecialchars($produk['lokasi']) ?></div>

              <?php if ($statusBuka !== null): ?>
              <span class="status-badge <?= $statusBuka === 'buka' ? 'status-buka' : 'status-tutup' ?>">
                <?= $statusBuka === 'buka' ? '🟢 Sedang Buka' : '🔴 Tutup' ?>
              </span>
              <?php endif; ?>

              <?php
              $halalClass = '';
              if ($produk['status_halal'] == 'Halal Bersertifikat') $halalClass = 'halal-bersertifikat';
              elseif ($produk['status_halal'] == 'Halal Belum Bersertifikat') $halalClass = 'halal-belum';
              else $halalClass = 'halal-non';
              ?>
              <span class="halal-badge <?= $halalClass ?>">
                <?= htmlspecialchars($produk['status_halal']) ?>
              </span>
            </div>
          </div>

          <div class="umkm-meta-grid">
            <div class="meta-item">
              <div class="meta-label">📞 Kontak</div>
              <div class="meta-value"><?= htmlspecialchars($produk['nomor_kontak'] ?: 'Belum diisi') ?></div>
            </div>
            <div class="meta-item">
              <div class="meta-label">📍 Lokasi</div>
              <div class="meta-value"><?= htmlspecialchars($produk['lokasi']) ?></div>
            </div>
            <?php if ($produk['status_halal'] === 'Halal Bersertifikat' && $produk['no_sertifikat']): ?>
            <div class="meta-item" style="grid-column: span 2;">
              <div class="meta-label">📜 No. Sertifikat Halal</div>
              <div class="meta-value"><?= htmlspecialchars($produk['no_sertifikat']) ?></div>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Waktu Operasional -->
        <div class="info-card">
          <div class="info-card-title">🕒 Jam Operasional</div>
          <?php if (empty($waktuList)): ?>
          <p style="color: #6b7280; font-style: italic; font-size: 0.88rem;">
            Jam operasional belum diatur untuk UMKM ini.
          </p>
          <?php else: ?>
          <div class="schedule-grid">
            <?php foreach ($waktuList as $waktu):
              $isTutup = $waktu['keterangan'] === 'Tutup';
              $isToday = ($waktu['hari'] === $hariIni);
            ?>
            <div class="schedule-item <?= $isToday ? 'today' : '' ?>">
              <span class="schedule-day">
                <?= htmlspecialchars($waktu['hari']) ?>
                <?php if ($isToday): ?>
                <span class="today-label">Hari Ini</span>
                <?php endif; ?>
              </span>
              <?php if ($isTutup): ?>
              <span class="schedule-closed">🚫 Tutup / Libur</span>
              <?php else: ?>
              <span class="schedule-time">
                🟢 <?= date('H:i', strtotime($waktu['jam_buka'])) ?> -
                <?= date('H:i', strtotime($waktu['jam_tutup'])) ?> WIB
              </span>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Metode Pembayaran -->
        <?php if (!empty($bayarList)): ?>
        <div class="info-card">
          <div class="info-card-title">💳 Metode Pembayaran</div>
          <div class="payment-list">
            <?php foreach ($bayarList as $bayar): ?>
            <span class="payment-badge">
              💵 <?= htmlspecialchars($bayar['metode_pembayaran']) ?>
            </span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Mitra Platform -->
        <?php if (!empty($mitraList)): ?>
        <div class="info-card">
          <div class="info-card-title">🤝 Pesan Online</div>
          <div class="mitra-list">
            <?php foreach ($mitraList as $mitra):
              $icon = $iconMitra[$mitra['nama_mitra']] ?? '📱';
              $color = $colorMitra[$mitra['nama_mitra']] ?? '#4b5563';
            ?>
            <a href="<?= htmlspecialchars($mitra['link_mitra'] ?: '#') ?>" target="_blank" class="mitra-link"
              style="background: <?= $color ?>;">
              <span><?= $icon ?> Pesan via <?= htmlspecialchars($mitra['nama_mitra']) ?></span>
              <span>Buka Aplikasi ➔</span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Produk Lainnya dari UMKM yang sama -->
    <?php if (!empty($produkLainnya)): ?>
    <div class="other-products-section">
      <h2 class="section-title">🍽️ Produk Lainnya dari <?= htmlspecialchars($produk['nama_umkm']) ?></h2>
      <div class="other-products-grid">
        <?php foreach ($produkLainnya as $lain):
          $emojiLain = $emojiKategori[$lain['kategori_produk']] ?? '🍿';
          $lainFoto  = ltrim(str_replace('FOTO_UMKM/', '', $lain['foto'] ?? ''), '/');
        ?>
        <a href="detail_produk.php?id=<?= $lain['id_produk'] ?>" class="other-product-card">
          <div class="other-card-top">
            <?php if ($lainFoto && file_exists('FOTO_UMKM/' . $lainFoto)): ?>
            <img src="FOTO_UMKM/<?= htmlspecialchars($lainFoto) ?>" class="other-foto"
              alt="<?= htmlspecialchars($lain['nama_produk']) ?>">
            <?php else: ?>
            <?= $emojiLain ?>
            <?php endif; ?>
          </div>
          <div class="other-card-body">
            <div class="other-card-category"><?= $emojiLain ?> <?= htmlspecialchars($lain['kategori_produk']) ?></div>
            <div class="other-card-name"><?= htmlspecialchars($lain['nama_produk']) ?></div>
            <div class="other-card-price">Rp <?= number_format($lain['harga'], 0, ',', '.') ?></div>
            <?php if ($lain['daftar_rasa']): ?>
            <div class="other-card-rasa">
              <?php foreach (explode(', ', $lain['daftar_rasa']) as $r): ?>
              <span><?= htmlspecialchars($r) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
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
