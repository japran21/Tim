<?php
require_once 'koneksi.php';

// Hari dalam seminggu
$hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

// Hari & jam sekarang (WIB)
date_default_timezone_set('Asia/Jakarta');
$hariSekarang = date('l'); // English
$hariMap = [
    'Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
    'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'
];
$hariSekarangID = $hariMap[$hariSekarang] ?? 'Senin';
$jamSekarang    = date('H:i');

// Filter dari form
$hariFilter = isset($_GET['hari']) && in_array($_GET['hari'], $hariList)
    ? $_GET['hari'] : $hariSekarangID;
$jamFilter  = isset($_GET['jam'])  && preg_match('/^\d{2}:\d{2}$/', $_GET['jam'])
    ? $_GET['jam'] : $jamSekarang;

// Query UMKM yang buka
$jamSQL = mysqli_real_escape_string($koneksi, $jamFilter . ':00');
$hariSQL = mysqli_real_escape_string($koneksi, $hariFilter);

$query = "
    SELECT u.id_umkm, u.nama_umkm, u.lokasi, u.foto, u.nomor_kontak, u.status_halal,
           w.jam_buka, w.jam_tutup, w.keterangan
    FROM umkm u
    JOIN waktu_operasional w ON u.id_umkm = w.id_umkm
    WHERE w.hari = '$hariSQL'
      AND w.keterangan = 'Buka'
      AND w.jam_buka  <= '$jamSQL'
      AND w.jam_tutup >= '$jamSQL'
    ORDER BY u.nama_umkm ASC
";
$result  = mysqli_query($koneksi, $query);
$umkmBuka = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) $umkmBuka[] = $row;
}

// Hitung total UMKM terdaftar
$totalUMKM = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM umkm"))['total'];


?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jadwal Operasional UMKM – Street Food Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  * {
    box-sizing: border-box;
  }

  body {
    background: var(--krem, #fefce8);
    font-family: 'Plus Jakarta Sans', sans-serif;
  }

  /* ===== PAGE LAYOUT ===== */
  .page-body {
    display: flex;
    min-height: calc(100vh - 68px);
  }

  /* ===== SIDEBAR ===== */
  .sidebar {
    width: 220px;
    flex-shrink: 0;
    background: var(--putih, #fff);
    border-right: 1px solid rgba(45, 106, 79, .10);
    padding: 32px 0;
    position: sticky;
    top: 68px;
    height: calc(100vh - 68px);
    overflow-y: auto;
  }

  .sidebar-section {
    margin-bottom: 8px;
  }

  .sidebar-label {
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--abu, #9ca3af);
    padding: 0 20px 8px;
    display: block;
  }

  .sidebar-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    text-decoration: none;
    font-size: .88rem;
    font-weight: 500;
    color: #374151;
    border-left: 3px solid transparent;
    transition: all .18s ease;
  }

  .sidebar-link:hover {
    background: rgba(46, 107, 79, .06);
    color: #2e6b4f;
    border-left-color: #6ee7b7;
  }

  .sidebar-link.active {
    background: rgba(46, 107, 79, .08);
    color: #2e6b4f;
    border-left-color: #2e6b4f;
    font-weight: 600;
  }

  .sidebar-icon {
    font-size: 1rem;
    width: 20px;
    text-align: center;
  }

  /* ===== MAIN ===== */
  .main-content {
    flex: 1;
    min-width: 0;
    overflow-x: hidden;
    padding: 40px 36px 60px;
  }

  .page-header {
    margin-bottom: 32px;
  }

  .page-title {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    color: #1a1a2e;
    margin: 0 0 6px;
  }

  .page-subtitle {
    color: #6b7280;
    font-size: .95rem;
    margin: 0;
  }

  /* ===== FILTER CARD ===== */
  .filter-card {
    background: #fff;
    border-radius: 24px;
    padding: 28px 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, .07);
    margin-bottom: 32px;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: flex-end;
  }

  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
    min-width: 160px;
  }

  .filter-label {
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: #6b7280;
  }

  .filter-select,
  .filter-input {
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    font-size: .95rem;
    font-family: inherit;
    background: #f9fafb;
    color: #1a1a2e;
    cursor: pointer;
    transition: border-color .2s;
  }

  .filter-select:focus,
  .filter-input:focus {
    outline: none;
    border-color: #2e6b4f;
    background: #fff;
  }

  .btn-cari {
    background: #2e6b4f;
    color: #fff;
    padding: 12px 28px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    font-size: .95rem;
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
    font-family: inherit;
  }

  .btn-cari:hover {
    background: #1a4a35;
    transform: scale(1.02);
  }

  .btn-sekarang {
    background: #fef3c7;
    color: #92400e;
    padding: 12px 20px;
    border: 2px solid #fde68a;
    border-radius: 40px;
    font-weight: 600;
    font-size: .88rem;
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: inherit;
  }

  .btn-sekarang:hover {
    background: #fde68a;
  }

  /* ===== STATS ROW ===== */
  .stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: #fff;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .stat-number {
    font-family: 'Playfair Display', serif;
    font-size: 2.4rem;
    font-weight: 700;
    line-height: 1;
  }

  .stat-number.green {
    color: #2e6b4f;
  }

  .stat-number.amber {
    color: #d97706;
  }

  .stat-number.gray {
    color: #9ca3af;
  }

  .stat-label {
    font-size: .82rem;
    color: #6b7280;
    font-weight: 500;
    margin-top: 4px;
  }

  /* ===== RESULT HEADER ===== */
  .result-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
  }

  .result-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    color: #1a1a2e;
  }

  .result-badge {
    background: #d1fae5;
    color: #065f46;
    padding: 6px 16px;
    border-radius: 40px;
    font-size: .82rem;
    font-weight: 600;
  }

  .result-badge.empty {
    background: #fee2e2;
    color: #991b1b;
  }

  /* ===== UMKM GRID ===== */
  .umkm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
  }

  .umkm-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
    transition: transform .2s, box-shadow .2s;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
  }

  .umkm-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 28px rgba(0, 0, 0, .12);
  }

  .umkm-foto {
    width: 100%;
    height: 160px;
    object-fit: cover;
    background: #f3f4f6;
  }

  .umkm-foto-placeholder {
    width: 100%;
    height: 160px;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
  }

  .umkm-body {
    padding: 18px 20px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .umkm-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.05rem;
    color: #1a1a2e;
    font-weight: 700;
    margin: 0;
  }

  .umkm-lokasi {
    font-size: .8rem;
    color: #6b7280;
    display: flex;
    gap: 4px;
    align-items: flex-start;
  }

  .umkm-kontak {
    font-size: .8rem;
    color: #6b7280;
  }

  .umkm-footer {
    margin-top: auto;
    padding-top: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
  }

  .jam-badge {
    background: #ecfdf5;
    color: #065f46;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .halal-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: .72rem;
    font-weight: 600;
  }

  .halal-badge.bersertifikat {
    background: #d1fae5;
    color: #065f46;
  }

  .halal-badge.belum {
    background: #fef3c7;
    color: #92400e;
  }

  .halal-badge.non {
    background: #f3f4f6;
    color: #6b7280;
  }

  /* ===== EMPTY STATE ===== */
  .empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
  }

  .empty-icon {
    font-size: 3.5rem;
    margin-bottom: 16px;
  }

  .empty-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    color: #1a1a2e;
    margin-bottom: 8px;
  }

  .empty-desc {
    color: #9ca3af;
    font-size: .9rem;
  }

  /* ===== JAM TIMELINE ===== */
  @media (max-width: 768px) {
    .main-content {
      padding: 24px 16px 40px;
    }

    .filter-card {
      flex-direction: column;
    }

    .umkm-grid {
      grid-template-columns: 1fr;
    }
  }
  </style>
</head>

<body>

  <header class="navbar">
    <div class="nav-inner">
      <a href="index.php" class="brand" style="text-decoration:none;">
        <span class="brand-icon">🏪</span>
        <div class="brand-text">
          <span class="brand-name">STREET FOOD</span>
          <span class="brand-sub">Ciwaruga</span>
        </div>
      </a>
      <nav class="nav-links">
        <a href="index.php">Beranda</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="page-body">

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="sidebar-section">
        <span class="sidebar-label">Menu Kelola</span>
        <a href="umkm.php" class="sidebar-link"><span class="sidebar-icon">🏪</span> Kelola UMKM</a>
        <a href="produk.php" class="sidebar-link"><span class="sidebar-icon">🍽️</span> Kelola Produk</a>
        <a href="kategori_rasa.php" class="sidebar-link"><span class="sidebar-icon">🌶️</span> Kelola Rasa</a>
        <a href="bayar.php" class="sidebar-link"><span class="sidebar-icon">💳</span> Kelola Pembayaran</a>
        <a href="mitra.php" class="sidebar-link"><span class="sidebar-icon">🤝</span> Kelola Mitra</a>
        <a href="jadwal_umkm.php" class="sidebar-link active"><span class="sidebar-icon">🕐</span> Jadwal Buka</a>
      </div>
    </aside>

    <!-- MAIN -->
    <div class="main-content">

      <div class="page-header">
        <h1 class="page-title">🕐 Jadwal Operasional UMKM</h1>
        <p class="page-subtitle">Lihat UMKM mana yang sedang buka pada hari dan jam tertentu</p>
      </div>

      <!-- FILTER -->
      <form method="GET" action="">
        <div class="filter-card">
          <div class="filter-group">
            <label class="filter-label">Hari</label>
            <select name="hari" class="filter-select">
              <?php foreach ($hariList as $h): ?>
              <option value="<?= $h ?>" <?= $hariFilter === $h ? 'selected' : '' ?>><?= $h ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label class="filter-label">Jam</label>
            <input type="time" name="jam" class="filter-input" value="<?= htmlspecialchars($jamFilter) ?>">
          </div>
          <button type="submit" class="btn-cari">🔍 Cari</button>
          <a href="jadwal_umkm.php?hari=<?= $hariSekarangID ?>&jam=<?= $jamSekarang ?>" class="btn-sekarang">⏰
            Sekarang</a>
        </div>
      </form>

      <!-- STATS -->
      <?php $jumlahTutup = $totalUMKM - count($umkmBuka); ?>
      <div class="stats-row">
        <div class="stat-card">
          <span class="stat-number green"><?= count($umkmBuka) ?></span>
          <span class="stat-label">UMKM Buka sekarang</span>
        </div>
        <div class="stat-card">
          <span class="stat-number amber"><?= $jumlahTutup ?></span>
          <span class="stat-label">UMKM Tutup / Libur</span>
        </div>
        <div class="stat-card">
          <span class="stat-number gray"><?= $totalUMKM ?></span>
          <span class="stat-label">Total UMKM Terdaftar</span>
        </div>
        <div class="stat-card">
          <span class="stat-number green" style="font-size:1.6rem;"><?= $hariFilter ?></span>
          <span class="stat-label">Pukul <?= $jamFilter ?> WIB</span>
        </div>
      </div>

      <!-- HASIL -->
      <div class="result-header">
        <h2 class="result-title">UMKM yang Buka</h2>
        <span class="result-badge <?= empty($umkmBuka) ? 'empty' : '' ?>">
          <?= count($umkmBuka) ?> UMKM <?= empty($umkmBuka) ? 'tidak ada' : 'ditemukan' ?>
        </span>
      </div>

      <?php if (empty($umkmBuka)): ?>
      <div class="empty-state">
        <div class="empty-icon">😴</div>
        <div class="empty-title">Tidak Ada UMKM yang Buka</div>
        <p class="empty-desc">Pada hari <?= $hariFilter ?> pukul <?= $jamFilter ?> WIB,<br>belum ada UMKM yang tercatat
          buka. Coba jam lain!</p>
      </div>

      <?php else: ?>
      <div class="umkm-grid">
        <?php foreach ($umkmBuka as $u):
        $halalClass = match($u['status_halal']) {
            'Halal Bersertifikat'       => 'bersertifikat',
            'Halal Belum Bersertifikat' => 'belum',
            default                     => 'non'
        };
        $halalLabel = match($u['status_halal']) {
            'Halal Bersertifikat'       => ' Halal',
            'Halal Belum Bersertifikat' => '🟡 Halal',
            default                     => '⚪ Non-Halal'
        };
        $jamBuka  = date('H:i', strtotime($u['jam_buka']));
        $jamTutup = date('H:i', strtotime($u['jam_tutup']));
        $fotoPath = 'uploads/' . $u['foto'];
        $adaFoto  = $u['foto'] && file_exists(__DIR__ . '/' . $fotoPath);
      ?>
        <a href="detail_produk.php?id=<?= $u['id_umkm'] ?>" class="umkm-card">
          <?php if ($adaFoto): ?>
          <img src="<?= htmlspecialchars($fotoPath) ?>" alt="<?= htmlspecialchars($u['nama_umkm']) ?>"
            class="umkm-foto">
          <?php else: ?>
          <div class="umkm-foto-placeholder">🏪</div>
          <?php endif; ?>

          <div class="umkm-body">
            <h3 class="umkm-name"><?= htmlspecialchars($u['nama_umkm']) ?></h3>
            <div class="umkm-lokasi">📍 <span><?= htmlspecialchars($u['lokasi']) ?></span></div>
            <?php if ($u['nomor_kontak']): ?>
            <div class="umkm-kontak">📞 <?= htmlspecialchars($u['nomor_kontak']) ?></div>
            <?php endif; ?>

            <div class="umkm-footer">
              <span class="jam-badge">🕐 <?= $jamBuka ?> – <?= $jamTutup ?></span>
              <span class="halal-badge <?= $halalClass ?>"><?= $halalLabel ?></span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div><!-- /main-content -->
  </div><!-- /page-body -->

  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <span class="brand-icon">🏪</span>
        <span class="brand-name">Street Food Ciwaruga</span>
      </div>
      <p class="footer-copy">© 2026 Street Food Ciwaruga · Mendukung Usaha Lokal</p>
    </div>
  </footer>

</body>

</html>