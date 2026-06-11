<?php
require_once 'koneksi.php';

$queryRasa   = "SELECT * FROM kategori_rasa ORDER BY id_rasa ASC";
$resultRasa  = mysqli_query($koneksi, $queryRasa);

$rasaCounts  = [];
$countResult = mysqli_query($koneksi, "
    SELECT kr.jenis_rasa, COUNT(pr.id_produk) as total
    FROM kategori_rasa kr
    LEFT JOIN produk_rasa pr ON kr.id_rasa = pr.id_rasa
    GROUP BY kr.id_rasa
");
while ($row = mysqli_fetch_assoc($countResult)) {
    $rasaCounts[$row['jenis_rasa']] = $row['total'];
}

$kategoriCounts = [];
$katResult = mysqli_query($koneksi, "
    SELECT kategori_produk, COUNT(*) as total
    FROM produk
    WHERE kategori_produk IN ('Makanan','Minuman')
    GROUP BY kategori_produk
");
while ($row = mysqli_fetch_assoc($katResult)) {
    $kategoriCounts[$row['kategori_produk']] = $row['total'];
}

$asalList   = [];
$asalResult = mysqli_query($koneksi, "
    SELECT asal_daerah, COUNT(*) as total
    FROM produk
    WHERE asal_daerah IS NOT NULL
      AND asal_daerah != ''
      AND asal_daerah != 'NULL'
    GROUP BY asal_daerah
    HAVING total > 0
    ORDER BY total DESC
");
while ($row = mysqli_fetch_assoc($asalResult)) {
    $asalList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Street Food Ciwaruga</title>
  <link rel="stylesheet" href="style.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet" />
  <style>
  /* ===== PAGE LAYOUT ===== */
  .page-body {
    display: flex;
    min-height: calc(100vh - 68px);
  }

  /* ===== SIDEBAR ===== */
  .sidebar {
    width: 220px;
    flex-shrink: 0;
    background: var(--putih);
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
    color: var(--abu);
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
    color: var(--hijau);
    border-left-color: var(--hijau-muda);
  }

  .sidebar-link.active {
    background: rgba(46, 107, 79, .08);
    color: var(--hijau);
    border-left-color: var(--hijau);
    font-weight: 600;
  }

  .sidebar-icon {
    font-size: 1rem;
    width: 20px;
    text-align: center;
  }

  /* ===== MAIN CONTENT ===== */
  .main-content {
    flex: 1;
    min-width: 0;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .main-content>* {
    width: 100%;
  }

  /* override section max-width when sidebar is present */
  .main-content .hero,
  .main-content .flavor-section,
  .main-content .kategori-section,
  .main-content .asal-section,
  .main-content .results-section {
    max-width: 100%;
  }

  /* hero jadi 1 kolom center */
  .main-content .hero {
    grid-template-columns: 1fr;
    text-align: center;
    justify-items: center;
    padding: 48px 32px 40px;
  }

  .main-content .hero-visual {
    display: none;
  }

  .main-content .hero-badge {
    margin-left: auto;
    margin-right: auto;
  }

  .main-content .hero-desc {
    margin-left: auto;
    margin-right: auto;
    text-align: center;
  }

  .main-content .search-wrap {
    width: 100%;
    max-width: 560px;
  }

  .main-content .search-suggestions {
    justify-content: center;
  }

  /* ===== CENTERING ALL SECTIONS ===== */
  .flavor-section,
  .kategori-section,
  .asal-section,
  .results-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
  }

  .flavor-section-header {
    text-align: center;
    width: 100%;
    max-width: 900px;
  }

  .flavor-grid {
    justify-content: center;
    max-width: 900px;
    width: 100%;
  }

  .asal-section {
    padding: 0 32px 56px;
  }

  .asal-section-header,
  .asal-grid {
    width: 100%;
    max-width: 900px;
  }

  .asal-section-header {
    text-align: center;
    margin-bottom: 24px;
  }

  .asal-grid {
    justify-content: center;
  }

  .results-section {
    padding: 0 32px 60px;
    align-items: center;
  }

  .results-header {
    width: 100%;
    max-width: 100%;
  }

  .results-grid {
    width: 100%;
    max-width: 100%;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    justify-items: center;
  }

  .results-grid .product-card {
    width: 100%;
    max-width: 280px;
    min-width: 0;
  }

  .product-card-top--foto {
    padding: 0 !important;
    overflow: hidden;
  }

  .product-card-top--foto .product-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
  }

  .product-card:hover .product-card-top--foto .product-card-img {
    transform: scale(1.05);
  }

  .product-card-top--foto .product-card-emoji {
    font-size: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
  }

  /* ===== KATEGORI SECTION ===== */
  .kategori-section {
    padding: 0 32px 56px;
  }

  .kategori-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    max-width: 560px;
    margin: 0 auto;
    width: 100%;
  }

  .kategori-card {
    display: flex;
    align-items: center;
    gap: 16px;
    background: var(--kat-bg, #f9fafb);
    border: 2px solid transparent;
    border-radius: 20px;
    padding: 20px 24px;
    cursor: pointer;
    transition: all .22s ease;
    position: relative;
    overflow: hidden;
  }

  .kategori-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--kat-color, #6b7280);
    opacity: 0;
    transition: opacity .22s;
  }

  .kategori-card:hover {
    border-color: var(--kat-color, #6b7280);
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
  }

  .kategori-card:hover::before {
    opacity: .06;
  }

  .kategori-card.active {
    border-color: var(--kat-color, #6b7280);
    box-shadow: 0 4px 20px rgba(0, 0, 0, .12);
  }

  .kategori-card.active::before {
    opacity: .1;
  }

  .kategori-emoji {
    font-size: 2rem;
    position: relative;
    z-index: 1;
    transition: transform .22s;
  }

  .kategori-card:hover .kategori-emoji {
    transform: scale(1.15);
  }

  .kategori-info {
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 1;
  }

  .kategori-label {
    font-weight: 700;
    font-size: 1rem;
    color: var(--gelap);
  }

  .kategori-count {
    font-size: .78rem;
    color: var(--abu);
    margin-top: 2px;
  }

  /* ===== ASAL DAERAH SECTION ===== */
  .asal-section {
    padding: 0 32px 64px;
  }

  .asal-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }

  .asal-card {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    border: 2px solid transparent;
    border-radius: 50px;
    padding: 10px 20px;
    cursor: pointer;
    transition: all .2s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
  }

  .asal-card:hover {
    border-color: var(--kuning);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, .10);
  }

  .asal-card.active {
    background: var(--kuning);
    border-color: var(--kuning);
  }

  .asal-card.active .asal-nama,
  .asal-card.active .asal-count {
    color: #fff;
  }

  .asal-card.active .asal-count {
    background: rgba(255, 255, 255, .25);
  }

  .asal-flag {
    font-size: 1.2rem;
  }

  .asal-nama {
    font-weight: 600;
    font-size: .88rem;
    color: var(--gelap);
    white-space: nowrap;
    transition: color .2s;
  }

  .asal-count {
    font-size: .72rem;
    background: #f3f4f6;
    color: var(--abu);
    padding: 2px 8px;
    border-radius: 20px;
    white-space: nowrap;
    transition: all .2s;
  }

  .tag-asal {
    background: #fef3c7;
    color: #92400e;
  }

  /* ===== HARGA SECTION ===== */
  .harga-section {
    padding: 0 32px 56px;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
  }

  .harga-presets {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    max-width: 900px;
    width: 100%;
    justify-content: center;
  }

  .harga-preset-btn {
    background: #f3f4f6;
    border: 1.5px solid #e5e7eb;
    border-radius: 50px;
    padding: 9px 20px;
    font-size: .875rem;
    font-weight: 600;
    color: #374151;
    cursor: pointer;
    transition: all .18s ease;
    white-space: nowrap;
  }

  .harga-preset-btn:hover {
    border-color: var(--hijau);
    color: var(--hijau);
    background: rgba(46, 107, 79, .06);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 107, 79, .15);
  }

  .harga-preset-btn.active {
    background: var(--hijau);
    color: #fff;
    border-color: var(--hijau);
    box-shadow: 0 4px 12px rgba(46, 107, 79, .30);
  }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 900px) {
    .sidebar {
      display: none;
    }

    .page-body {
      display: block;
    }

    .kategori-section,
    .asal-section {
      padding: 0 20px 40px;
    }

    .kategori-grid {
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .kategori-card {
      padding: 16px;
      gap: 12px;
    }

    .kategori-emoji {
      font-size: 1.6rem;
    }

    .asal-grid {
      gap: 8px;
    }

    .asal-card {
      padding: 8px 14px;
    }
  }
  </style>
</head>

<body>

  <!-- ===== NAVBAR: hanya Beranda ===== -->
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
        <a href="index.php" class="active">Beranda</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="page-body">

    <!-- ===== SIDEBAR KIRI ===== -->
    <aside class="sidebar">
      <div class="sidebar-section">
        <span class="sidebar-label">Menu Kelola</span>
        <a href="umkm.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola UMKM</a>
        <a href="produk.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola Produk</a>
        <a href="kategori_rasa.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola Rasa</a>
        <a href="bayar.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola Pembayaran</a>
        <a href="mitra.php" class="sidebar-link"><span class="sidebar-icon">-</span> Kelola Mitra</a>
      </div>

      <!-- SIDEBAR: Filter Harga -->
      <div class="sidebar-section"
        style="padding: 12px 16px 16px; border-top: 1px solid rgba(45,106,79,.08); margin-top: 8px;">
        <span class="sidebar-label"> Range Harga</span>
        <div style="display:flex; flex-direction:column; gap:6px;">
          <button class="harga-preset-btn" style="width:100%;border-radius:10px;text-align:left;padding:7px 12px;"
            onclick="filterByHarga(0, 10000, this)">Rp 0 – 10K</button>
          <button class="harga-preset-btn" style="width:100%;border-radius:10px;text-align:left;padding:7px 12px;"
            onclick="filterByHarga(0, 20000, this)">Rp 0 – 20K</button>
          <button class="harga-preset-btn" style="width:100%;border-radius:10px;text-align:left;padding:7px 12px;"
            onclick="filterByHarga(10000, 25000, this)">Rp 10K – 25K</button>
          <button class="harga-preset-btn" style="width:100%;border-radius:10px;text-align:left;padding:7px 12px;"
            onclick="filterByHarga(20000, 50000, this)">Rp 20K – 50K</button>
        </div>
      </div>
    </aside>

    <!-- ===== KONTEN UTAMA ===== -->
    <div class="main-content">

      <!-- HERO -->
      <section class="hero">
        <div class="hero-bg-pattern"></div>
        <div class="hero-content">
          <div class="hero-badge">📍 Ciwaruga, Kabupaten Bandung Barat</div>
          <h1 class="hero-title">
            Temukan Makanan<br />
            <span class="highlight">Ciwaruga</span><br />
            Terbaik
          </h1>
          <p class="hero-desc">
            Platform digital untuk menampilkan dan mendukung UMKM Street Food yang berada di Ciwaruga.
          </p>
          <div class="search-wrap">
            <div class="search-bar">
              <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
              <input type="text" id="searchInput" placeholder="Cari makanan, contoh: batagor, surabi..." />
              <button class="search-btn" onclick="doSearch()">Cari</button>
            </div>
            <div class="search-tags">
              <span onclick="quickSearch('Batagor')">Batagor</span>
              <span onclick="quickSearch('Mie Ayam')"> Mie Ayam</span>
              <span onclick="quickSearch('Surabi')"> Surabi</span>
              <span onclick="quickSearch('Es Kelapa')"> Es Kelapa</span>
            </div>
          </div>
        </div>
        <div class="hero-visual">
          <div class="visual-card card-1">
            <div class="card-emoji">🍜</div>
            <div>
              <div class="card-title">Street Food</div>
              <div class="card-sub">21 UMKM Terdaftar</div>
            </div>
          </div>
        </div>
      </section>

      <!-- FILTER RASA (rata kiri) -->
      <section class="flavor-section">
        <div class="flavor-section-header">
          <h2>Cari Berdasarkan Rasa</h2>
          <p>Pilih rasa yang kamu inginkan</p>
        </div>
        <div class="flavor-grid">
          <?php
          $emojiRasa = ['Asin'=>'','Gurih'=>'','Manis'=>'','Pedas'=>'','Asam'=>''];
          mysqli_data_seek($resultRasa, 0);
          while ($rasa = mysqli_fetch_assoc($resultRasa)):
              $namaRasa = htmlspecialchars($rasa['jenis_rasa']);
              $emoji    = $emojiRasa[$namaRasa] ?? '🍽️';
              $total    = $rasaCounts[$namaRasa] ?? 0;
          ?>
          <div class="flavor-card" data-rasa="<?= $namaRasa ?>" onclick="filterByRasa('<?= $namaRasa ?>', this)">
            <span class="flavor-emoji"><?= $emoji ?></span>
            <span class="flavor-label"><?= $namaRasa ?></span>
            <?php if ($total > 0): ?>
            <span class="flavor-card-badge"><?= $total ?> produk</span>
            <?php endif; ?>
          </div>
          <?php endwhile; ?>
        </div>
      </section>

      <!-- FILTER KATEGORI (tengah) -->
      <section class="kategori-section">
        <div class="flavor-section-header" style="text-align:center;">
          <h2>Kategori Produk</h2>
          <p>Pilih berdasarkan jenis hidangan</p>
        </div>
        <div class="kategori-grid">
          <?php
          $kategoriList = [
            'Makanan' => ['emoji'=>'🍜','color'=>'#f59e0b','bg'=>'#fffbeb'],
            'Minuman' => ['emoji'=>'🥤','color'=>'#3b82f6','bg'=>'#eff6ff'],
          ];
          foreach ($kategoriList as $namaKat => $info):
              $totalKat = $kategoriCounts[$namaKat] ?? 0;
          ?>
          <div class="kategori-card" data-kategori="<?= $namaKat ?>"
            style="--kat-color:<?= $info['color'] ?>;--kat-bg:<?= $info['bg'] ?>;"
            onclick="filterByKategori('<?= $namaKat ?>', this)">
            <span class="kategori-emoji"><?= $info['emoji'] ?></span>
            <div class="kategori-info">
              <span class="kategori-label"><?= $namaKat ?></span>
              <span class="kategori-count"><?= $totalKat ?> produk</span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- FILTER ASAL DAERAH (rata kiri) -->
      <section class="asal-section">
        <div class="flavor-section-header">
          <h2>Asal Daerah</h2>
          <p>Temukan makanan berdasarkan daerah asalnya</p>
        </div>
        <div class="asal-grid">
          <?php
          $flagMap = [
            'Bandung'=>'','Garut'=>'','Cirebon'=>'','Madura'=>'',
            'Padang'=>'','Yogyakarta'=>'','Solo'=>'','Wonogiri'=>'',
            'Makassar'=>'','Bangka'=>'',
          ];
          foreach ($asalList as $asal):
              $nama  = htmlspecialchars($asal['asal_daerah']);
              $total = $asal['total'];
              $kota  = explode(',', $asal['asal_daerah'])[0];
              $icon  = '';
              foreach ($flagMap as $key => $em) {
                  if (stripos($kota, $key) !== false) { $icon = $em; break; }
              }
          ?>
          <div class="asal-card" data-asal="<?= $nama ?>" onclick="filterByAsal('<?= addslashes($nama) ?>', this)">
            <span class="asal-flag"><?= $icon ?></span>
            <span class="asal-nama"><?= $nama ?></span>
            <span class="asal-count"><?= $total ?> produk</span>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- HASIL FILTER / SEARCH -->
      <section class="results-section" id="resultsSection" style="display:none;">
        <div class="results-header">
          <div class="results-title">
            Menampilkan: <span id="activeFilterLabel">—</span>
          </div>
          <button class="reset-filter visible" id="resetBtn" onclick="resetFilter()">✕ Hapus Filter</button>
        </div>
        <div class="results-grid" id="resultsGrid"></div>
      </section>

    </div><!-- /.main-content -->
  </div><!-- /.page-body -->

  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <span class="brand-icon">🏪</span>
        <span class="brand-name">Street Food Ciwaruga</span>
      </div>
      <p class="footer-copy">© 2026 Street Food Ciwaruga · Mendukung Usaha Lokal</p>
    </div>
  </footer>

  <script>
  const emojiMap = {
    'Asin': '',
    'Gurih': '',
    'Manis': '',
    'Pedas': '',
    'Asam': ''
  };
  const kategoriEmojiMap = {
    'Makanan': '🍜',
    'Minuman': '🥤'
  };

  let activeRasa = null,
    activeKategori = null,
    activeAsal = null,
    activeHarga = null;

  // ===== FILTER HARGA =====
  function filterByHarga(min, max, el) {
    const label = el.textContent.trim();
    if (activeHarga === label) {
      resetFilter();
      return;
    }
    clearAllActive();
    activeHarga = label;
    document.querySelectorAll('.harga-preset-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    showResults('💰 Harga <strong>' + escHtml(label) + '</strong>');
    const maxParam = max >= 50000 ? 9999999 : max;
    fetchAndRender('get_produk.php?harga_min=' + min + '&harga_max=' + maxParam, label, 'harga');
  }
  // ===== END FILTER HARGA =====

  function clearAllActive() {
    activeRasa = activeKategori = activeAsal = activeHarga = null;
    document.querySelectorAll('.flavor-card,.kategori-card,.asal-card,.harga-preset-btn').forEach(c => c.classList
      .remove('active'));
  }

  function showResults(labelHtml) {
    const section = document.getElementById('resultsSection');
    document.getElementById('activeFilterLabel').innerHTML = labelHtml;
    document.getElementById('resultsGrid').innerHTML = '<div class="loading-spinner">⏳ Memuat produk...</div>';
    section.style.display = 'block';
    setTimeout(() => section.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    }), 50);
  }

  function filterByRasa(namaRasa, el) {
    if (activeRasa === namaRasa) {
      resetFilter();
      return;
    }
    clearAllActive();
    activeRasa = namaRasa;
    el.classList.add('active');
    showResults((emojiMap[namaRasa] || '🍽️') + ' Rasa <strong>' + escHtml(namaRasa) + '</strong>');
    fetchAndRender('get_produk.php?rasa=' + encodeURIComponent(namaRasa), namaRasa, 'rasa');
  }

  function filterByKategori(namaKat, el) {
    if (activeKategori === namaKat) {
      resetFilter();
      return;
    }
    clearAllActive();
    activeKategori = namaKat;
    el.classList.add('active');
    showResults((kategoriEmojiMap[namaKat] || '📂') + ' Kategori <strong>' + escHtml(namaKat) + '</strong>');
    fetchAndRender('get_produk.php?kategori=' + encodeURIComponent(namaKat), namaKat, 'kategori');
  }

  function filterByAsal(namaAsal, el) {
    if (activeAsal === namaAsal) {
      resetFilter();
      return;
    }
    clearAllActive();
    activeAsal = namaAsal;
    el.classList.add('active');
    showResults('📍 Asal Daerah: <strong>' + escHtml(namaAsal) + '</strong>');
    fetchAndRender('get_produk.php?asal_daerah=' + encodeURIComponent(namaAsal), namaAsal, 'asal');
  }

  function quickSearch(kw) {
    document.getElementById('searchInput').value = kw;
    doSearch();
  }

  function doSearch() {
    const kw = document.getElementById('searchInput').value.trim();
    if (!kw) {
      document.getElementById('searchInput').focus();
      return;
    }
    clearAllActive();
    showResults('🔍 "<strong>' + escHtml(kw) + '</strong>"');
    fetchAndRender('get_produk.php?keyword=' + encodeURIComponent(kw), kw, 'search');
  }

  function fetchAndRender(url, label, type) {
    const grid = document.getElementById('resultsGrid');
    fetch(url)
      .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(data => {
        if (data.error) {
          showError(grid, data.error);
          return;
        }
        if (!data.length) {
          grid.innerHTML =
            `<div class="empty-state"><div class="empty-emoji">😕</div><p>Tidak ada produk untuk <strong>${escHtml(label)}</strong>.</p></div>`;
          return;
        }
        const emoji = type === 'rasa' ? (emojiMap[label] || '🍽️') : type === 'kategori' ? (kategoriEmojiMap[label] ||
          '📂') : type === 'asal' ? '📍' : '🍽️';
        grid.innerHTML = data.map(p => {
          const fotoHtml = p.foto ?
            `<img src="FOTO_UMKM/${escHtml(p.foto)}" alt="${escHtml(p.nama_umkm)}" class="product-card-img" onerror="this.parentElement.innerHTML='<span class=\\'product-card-emoji\\'>${emoji}</span>'">` :
            `<span class="product-card-emoji">${emoji}</span>`;
          return `
          <a href="detail_produk.php?id=${p.id_produk}" class="product-card">
            <div class="product-card-top product-card-top--foto">${fotoHtml}</div>
            <div class="product-card-body">
              <div class="product-name">${escHtml(p.nama_produk)}</div>
              <div class="product-umkm">🏪 ${escHtml(p.nama_umkm)}</div>
              <div class="product-tags">
                ${p.kategori_produk?`<span class="product-tag" style="background:#e0e7ff;color:#3730a3;">📂 ${escHtml(p.kategori_produk)}</span>`:''}
                ${p.daftar_rasa?`<span class="product-tag" style="background:#f3f4f6;color:#6b7280;">${escHtml(p.daftar_rasa)}</span>`:''}
                ${p.asal_daerah&&p.asal_daerah!=='NULL'?`<span class="product-tag tag-asal">📍 ${escHtml(p.asal_daerah)}</span>`:''}
              </div>
              <div class="product-harga">Rp ${fmtRp(p.harga)}</div>
            </div>
          </a>`;
        }).join('');
      })
      .catch(err => {
        console.error(err);
        showError(grid, 'Gagal memuat data. Pastikan XAMPP berjalan.');
      });
  }

  function showError(g, m) {
    g.innerHTML = `<div class="empty-state"><div class="empty-emoji">⚠️</div><p>${escHtml(m)}</p></div>`;
  }

  function resetFilter() {
    clearAllActive();
    document.getElementById('resultsSection').style.display = 'none';
  }

  function escHtml(s) {
    return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function fmtRp(n) {
    return Number(n).toLocaleString('id-ID');
  }

  document.getElementById('searchInput').addEventListener('keypress', e => {
    if (e.key === 'Enter') {
      e.preventDefault();
      doSearch();
    }
  });
  </script>
</body>

</html>