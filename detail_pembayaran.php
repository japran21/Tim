<?php
require_once 'koneksi.php';

// Ambil semua metode pembayaran
$queryMetode = "SELECT * FROM metode_pembayaran ORDER BY id_metode ASC";
$resultMetode = mysqli_query($koneksi, $queryMetode);
$semuaMetode = [];
while ($row = mysqli_fetch_assoc($resultMetode)) $semuaMetode[] = $row;

// Filter metode yang dipilih
$metodeFilter = isset($_GET['metode']) ? (int)$_GET['metode'] : 0;
$searchNama   = isset($_GET['cari']) ? trim($_GET['cari']) : '';

// Ambil UMKM berdasarkan metode pembayaran
$umkmList = [];
$metodeNama = '';

if ($metodeFilter > 0) {
    $cariSQL = $searchNama ? "AND u.nama_umkm LIKE '%" . mysqli_real_escape_string($koneksi, $searchNama) . "%'" : '';
    $query = "
        SELECT u.id_umkm, u.nama_umkm, u.lokasi, u.foto, u.nomor_kontak, u.status_halal,
               GROUP_CONCAT(mp2.nama_metode ORDER BY mp2.id_metode SEPARATOR '|') as semua_metode
        FROM umkm u
        JOIN umkm_pembayaran up ON u.id_umkm = up.id_umkm
        JOIN metode_pembayaran mp ON up.id_metode = mp.id_metode
        LEFT JOIN umkm_pembayaran up2 ON u.id_umkm = up2.id_umkm
        LEFT JOIN metode_pembayaran mp2 ON up2.id_metode = mp2.id_metode
        WHERE mp.id_metode = $metodeFilter $cariSQL
        GROUP BY u.id_umkm
        ORDER BY u.nama_umkm ASC
    ";
    $result = mysqli_query($koneksi, $query);
    if ($result) while ($row = mysqli_fetch_assoc($result)) $umkmList[] = $row;

    foreach ($semuaMetode as $m) {
        if ($m['id_metode'] == $metodeFilter) { $metodeNama = $m['nama_metode']; break; }
    }
} elseif ($searchNama) {
    // Cari berdasarkan nama saja tanpa filter metode
    $cariSQL = mysqli_real_escape_string($koneksi, $searchNama);
    $query = "
        SELECT u.id_umkm, u.nama_umkm, u.lokasi, u.foto, u.nomor_kontak, u.status_halal,
               GROUP_CONCAT(mp.nama_metode ORDER BY mp.id_metode SEPARATOR '|') as semua_metode
        FROM umkm u
        LEFT JOIN umkm_pembayaran up ON u.id_umkm = up.id_umkm
        LEFT JOIN metode_pembayaran mp ON up.id_metode = mp.id_metode
        WHERE u.nama_umkm LIKE '%$cariSQL%'
        GROUP BY u.id_umkm
        ORDER BY u.nama_umkm ASC
    ";
    $result = mysqli_query($koneksi, $query);
    if ($result) while ($row = mysqli_fetch_assoc($result)) $umkmList[] = $row;
}

// Statistik per metode
$statsQuery = "
    SELECT mp.id_metode, mp.nama_metode, COUNT(up.id_umkm) as jumlah
    FROM metode_pembayaran mp
    LEFT JOIN umkm_pembayaran up ON mp.id_metode = up.id_metode
    GROUP BY mp.id_metode
    ORDER BY jumlah DESC
";
$statsResult = mysqli_query($koneksi, $statsQuery);
$statsMetode = [];
while ($row = mysqli_fetch_assoc($statsResult)) $statsMetode[] = $row;

// Icon & warna per metode
$metodeConfig = [
    'Cash'  => ['icon' => '', 'color' => '#16a34a', 'bg' => '#dcfce7', 'desc' => 'Pembayaran tunai langsung'],
    'QRIS'  => ['icon' => '', 'color' => '#7c3aed', 'bg' => '#ede9fe', 'desc' => 'Scan QR Code universal'],
    'Dana'  => ['icon' => '', 'color' => '#2563eb', 'bg' => '#dbeafe', 'desc' => 'Dompet digital Dana'],
    'OVO'   => ['icon' => '', 'color' => '#7c3aed', 'bg' => '#f3e8ff', 'desc' => 'Dompet digital OVO'],
];

function getConfig($nama, $config) {
    return $config[$nama] ?? ['icon' => '', 'color' => '#6b7280', 'bg' => '#f3f4f6', 'desc' => 'Metode pembayaran'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Pembayaran – Street Food Ciwaruga</title>
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

  .page-body {
    display: flex;
    min-height: calc(100vh - 68px);
  }

  /* SIDEBAR */
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
    color: #9ca3af;
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
    transition: all .18s;
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

  .search-card {
    background: #fff;
    border-radius: 20px;
    padding: 20px 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
    margin-bottom: 28px;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
  }

  .search-input {
    flex: 1;
    min-width: 200px;
    padding: 11px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: .95rem;
    font-family: inherit;
    background: #f9fafb;
  }

  .search-input:focus {
    outline: none;
    border-color: #2e6b4f;
    background: #fff;
  }

  .btn-search {
    background: #2e6b4f;
    color: #fff;
    padding: 11px 24px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    font-size: .9rem;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s;
  }

  .btn-search:hover {
    background: #1a4a35;
  }

  .btn-reset {
    background: #f3f4f6;
    color: #6b7280;
    padding: 11px 20px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    font-size: .9rem;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    display: inline-block;
    transition: all .2s;
  }

  .btn-reset:hover {
    background: #e5e7eb;
    color: #374151;
  }

  .metode-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
  }

  .metode-card {
    background: #fff;
    border-radius: 20px;
    padding: 22px 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
    cursor: pointer;
    border: 3px solid transparent;
    transition: all .22s;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .metode-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
  }

  .metode-card.active {
    border-color: currentColor;
  }

  .metode-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
  }

  .metode-name {
    font-weight: 700;
    color: #1a1a2e;
    font-size: 1rem;
  }

  .metode-desc {
    font-size: .78rem;
    color: #9ca3af;
  }

  .metode-count {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 4px;
  }

  .metode-count-label {
    font-size: .75rem;
    color: #9ca3af;
    margin-top: -4px;
  }

  .section-divider {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
  }

  .section-divider h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem;
    color: #1a1a2e;
    margin: 0;
    white-space: nowrap;
  }

  .divider-line {
    flex: 1;
    height: 1px;
    background: #e2e8f0;
  }

  .result-count {
    font-size: .82rem;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 20px;
    white-space: nowrap;
  }

  /* UMKM GRID */
  .umkm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
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
    height: 150px;
    object-fit: cover;
  }

  .umkm-foto-placeholder {
    width: 100%;
    height: 150px;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.8rem;
  }

  .umkm-body {
    padding: 16px 18px 18px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .umkm-name {
    font-family: 'Playfair Display', serif;
    font-size: 1rem;
    color: #1a1a2e;
    font-weight: 700;
    margin: 0;
  }

  .umkm-lokasi {
    font-size: .78rem;
    color: #6b7280;
    display: flex;
    gap: 4px;
    align-items: flex-start;
  }

  .umkm-kontak {
    font-size: .78rem;
    color: #6b7280;
  }

  .bayar-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 4px;
  }

  .bayar-tag {
    font-size: .72rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .bayar-tag.highlight {
    box-shadow: 0 0 0 2px currentColor;
  }

  /* Halal badge */
  .halal-badge {
    padding: 3px 9px;
    border-radius: 20px;
    font-size: .7rem;
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

  .umkm-footer {
    margin-top: auto;
    padding-top: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  /* EMPTY STATE */
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

  /* PLACEHOLDER awal */
  .placeholder-state {
    text-align: center;
    padding: 48px 20px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
  }

  @media(max-width:768px) {
    .main-content {
      padding: 24px 16px 40px;
    }

    .metode-grid {
      grid-template-columns: repeat(2, 1fr);
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
      <nav class="nav-links"><a href="index.php">Beranda</a></nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="page-body">

    <aside class="sidebar">
      <div class="sidebar-section">
        <span class="sidebar-label">Menu Kelola</span>
        <a href="umkm.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola UMKM</a>
        <a href="produk.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola Produk</a>
        <a href="kategori_rasa.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola Rasa</a>
        <a href="bayar.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola Pembayaran</a>
        <a href="mitra.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola Mitra</a>
        <a href="jadwal_umkm.php" class="sidebar-link"><span class="sidebar-icon">-</span> Jadwal Buka</a>
        <a href="detail_pembayaran.php" class="sidebar-link active"><span class="sidebar-icon">-</span> Detail
          Pembayaran</a>
      </div>
    </aside>

    <div class="main-content">

      <div class="page-header">
        <h1 class="page-title"> Detail Metode Pembayaran</h1>
        <p class="page-subtitle">Cari UMKM berdasarkan metode pembayaran yang diterima</p>
      </div>

      <form method="GET" action="">
        <?php if ($metodeFilter): ?>
        <input type="hidden" name="metode" value="<?= $metodeFilter ?>">
        <?php endif; ?>
        <div class="search-card">
          <input type="text" name="cari" class="search-input" placeholder="🔍 Cari nama UMKM..."
            value="<?= htmlspecialchars($searchNama) ?>">
          <button type="submit" class="btn-search">Cari</button>
          <a href="detail_pembayaran.php" class="btn-reset">Reset</a>
        </div>
      </form>


      <div class="metode-grid">
        <?php foreach ($statsMetode as $stat):
        $cfg = getConfig($stat['nama_metode'], $metodeConfig);
        $isActive = ($metodeFilter == $stat['id_metode']);
        $url = "detail_pembayaran.php?metode={$stat['id_metode']}" . ($searchNama ? "&cari=" . urlencode($searchNama) : "");
      ?>
        <a href="<?= $isActive ? 'detail_pembayaran.php' . ($searchNama ? '?cari='.urlencode($searchNama) : '') : $url ?>"
          class="metode-card <?= $isActive ? 'active' : '' ?>" style="color:<?= $cfg['color'] ?>;">
          <div class="metode-icon-wrap" style="background:<?= $cfg['bg'] ?>;">
            <?= $cfg['icon'] ?>
          </div>
          <div>
            <div class="metode-name"><?= htmlspecialchars($stat['nama_metode']) ?></div>
            <div class="metode-desc"><?= $cfg['desc'] ?></div>
          </div>
          <div class="metode-count" style="color:<?= $cfg['color'] ?>;"><?= $stat['jumlah'] ?></div>
          <div class="metode-count-label">UMKM menerima</div>
        </a>
        <?php endforeach; ?>
      </div>


      <?php if ($metodeFilter > 0 || $searchNama): ?>

      <?php
        $labelHasil = $metodeFilter
          ? "UMKM yang Menerima " . htmlspecialchars($metodeNama)
          : "Hasil Pencarian \"" . htmlspecialchars($searchNama) . "\"";
        $metodeFiltered = array_filter($semuaMetode, fn($m) => $m['id_metode'] == $metodeFilter);
        $metodeObj = reset($metodeFiltered);
        $cfgAktif = $metodeObj ? getConfig($metodeObj['nama_metode'], $metodeConfig) : ['color'=>'#2e6b4f','bg'=>'#dcfce7'];
      ?>

      <div class="section-divider">
        <h2><?= $labelHasil ?></h2>
        <div class="divider-line"></div>
        <span class="result-count" style="background:<?= $cfgAktif['bg'] ?>;color:<?= $cfgAktif['color'] ?>;">
          <?= count($umkmList) ?> UMKM
        </span>
      </div>

      <?php if (empty($umkmList)): ?>
      <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <div class="empty-title">Tidak Ditemukan</div>
        <p class="empty-desc">Tidak ada UMKM yang cocok dengan pencarian kamu.<br>Coba kata kunci atau filter metode
          yang lain.</p>
      </div>

      <?php else: ?>
      <div class="umkm-grid">
        <?php foreach ($umkmList as $u):
          $halalClass = match($u['status_halal']) {
            'Halal Bersertifikat'       => 'bersertifikat',
            'Halal Belum Bersertifikat' => 'belum',
            default                     => 'non'
          };
          $halalLabel = match($u['status_halal']) {
            'Halal Bersertifikat'       => '✅ Halal',
            'Halal Belum Bersertifikat' => '🟡 Halal*',
            default                     => '⚪ Non-Halal'
          };
          $metodeMilik = $u['semua_metode'] ? explode('|', $u['semua_metode']) : [];
          $fotoPath = $u['foto'];
          $adaFoto  = $u['foto'] && file_exists($u['foto']);
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

            <div class="bayar-tags">
              <?php foreach ($metodeMilik as $mNama):
                $mCfg = getConfig(trim($mNama), $metodeConfig);
                $isHighlight = (trim($mNama) === $metodeNama);
              ?>
              <span class="bayar-tag <?= $isHighlight ? 'highlight' : '' ?>"
                style="background:<?= $mCfg['bg'] ?>;color:<?= $mCfg['color'] ?>;">
                <?= $mCfg['icon'] ?> <?= htmlspecialchars(trim($mNama)) ?>
              </span>
              <?php endforeach; ?>
            </div>

            <div class="umkm-footer">
              <span class="halal-badge <?= $halalClass ?>"><?= $halalLabel ?></span>
              <span style="font-size:.75rem;color:#9ca3af;">Lihat Produk →</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php else: ?>

      <div class="placeholder-state">
        <div style="font-size:3rem;margin-bottom:16px;"></div>
        <div style="font-family:'Playfair Display',serif;font-size:1.2rem;color:#1a1a2e;margin-bottom:8px;">
          Pilih Metode Pembayaran
        </div>
        <p style="color:#9ca3af;font-size:.9rem;">
          Klik salah satu kartu di atas untuk melihat UMKM yang menerima metode pembayaran tersebut,<br>
          atau cari nama UMKM langsung di kolom pencarian.
        </p>
      </div>
      <?php endif; ?>

    </div>
  </div>

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