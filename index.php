<?php
require_once 'koneksi.php';

// Ambil data kategori rasa dari DB
$queryRasa = "SELECT * FROM kategori_rasa ORDER BY id_rasa ASC";
$resultRasa = mysqli_query($koneksi, $queryRasa);

// Hitung jumlah produk per rasa menggunakan tabel produk_rasa
$rasaCounts = [];
$countQuery = "
    SELECT kr.id_rasa, kr.jenis_rasa, COUNT(pr.id_produk) as total
    FROM kategori_rasa kr
    LEFT JOIN produk_rasa pr ON kr.id_rasa = pr.id_rasa
    GROUP BY kr.id_rasa
";
$countResult = mysqli_query($koneksi, $countQuery);
while ($count = mysqli_fetch_assoc($countResult)) {
    $rasaCounts[$count['jenis_rasa']] = $count['total'];
}

// Hitung jumlah produk per kategori produk
$kategoriCounts = [];
$kategoriQuery = "
    SELECT kategori_produk, COUNT(*) as total
    FROM produk
    WHERE kategori_produk IS NOT NULL AND kategori_produk != ''
    GROUP BY kategori_produk
    ORDER BY kategori_produk ASC
";
$kategoriResult = mysqli_query($koneksi, $kategoriQuery);
while ($row = mysqli_fetch_assoc($kategoriResult)) {
    $kategoriCounts[$row['kategori_produk']] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UMKM Ciwaruga</title>
  <link rel="stylesheet" href="style.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet" />
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

  <!-- ===== HERO SECTION ===== -->
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
          <input type="text" id="searchInput" placeholder="Cari Makanan anda..." />
          <button class="search-btn" onclick="doSearch()">Cari</button>
        </div>
        <div class="search-tags">
          <span>🍱 Makanan</span>
          <span>🥤 Minuman</span>
        </div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="visual-card card-1">
        <div class="card-emoji">🍜</div>
        <div>
          <div class="card-title">Street Food</div>
          <div class="card-sub"></div>
        </div>
      </div>
    </div>
  </section>

  <section class="flavor-section">
    <div class="flavor-section-header">
      <h2>Sok Cari rasa apa</h2>
      <p>YOK YOK PILIH</p>
    </div>

    <?php
        $emojiRasa = ['Asin', 'Gurih', 'Manis', 'Pedas','Asam', ];
        ?>
    <div class="flavor-grid">
      <?php 
            mysqli_data_seek($resultRasa, 0);
            while ($rasa = mysqli_fetch_assoc($resultRasa)) :
                $namaRasa  = htmlspecialchars($rasa['jenis_rasa']);
                $emoji     = $emojiRasa[$namaRasa] ?? 'MISSING';
                $total     = $rasaCounts[$namaRasa] ?? 0;
            ?>
      <div class="flavor-card" data-rasa="<?= $namaRasa ?>" data-id="<?= (int)$rasa['id_rasa'] ?>"
        onclick="filterByRasa('<?= $namaRasa ?>', this)">
        <span class="flavor-emoji"><?= $emoji ?></span>
        <span class="flavor-label"><?= $namaRasa ?></span>
        <?php if ($total > 0) : ?>
        <span class="flavor-card-badge"><?= $total ?> produk</span>
        <?php endif; ?>
      </div>
      <?php endwhile; ?>
    </div>
  </section>

  <!-- ===== KATEGORI FILTER SECTION ===== -->
  <section class="kategori-section">
    <div class="flavor-section-header">
      <h2>Kategori Produk</h2>
      <p>Pilih berdasarkan jenis makanan</p>
    </div>

    <div class="kategori-grid">
      <?php
      $kategoriList = [
          'Makanan' => ['emoji' => '🍜', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
          'Minuman' => ['emoji' => '🥤', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
          'Topping' => ['emoji' => '🍯', 'color' => '#ec4899', 'bg' => '#fdf2f8'],
          'Snack'   => ['emoji' => '🍿', 'color' => '#22c55e', 'bg' => '#f0fdf4'],
      ];
      foreach ($kategoriList as $namaKat => $info):
          $totalKat = $kategoriCounts[$namaKat] ?? 0;
      ?>
      <div class="kategori-card" data-kategori="<?= $namaKat ?>"
        style="--kat-color: <?= $info['color'] ?>; --kat-bg: <?= $info['bg'] ?>;" 
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

  <section class="results-section" id="resultsSection" style="display:none;">
    <div class="results-header">
      <div class="results-title">
        Menampilkan hasil untuk: <span id="activeFilterLabel">—</span>
      </div>
      <button class="reset-filter" id="resetBtn" onclick="resetFilter()">✕ Hapus Filter</button>
    </div>
    <div class="results-grid" id="resultsGrid">
      <!-- Diisi via AJAX -->
    </div>
  </section>

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
  const emojiMap = {
    'Asin': '🧂',
    'Gurih': '🍗',
    'Manis': '🍯',
    'Pedas': '🌶️',
    'Asam': '🍋',
  };

  const kategoriEmojiMap = {
    'Makanan': '🍜',
    'Minuman': '🥤',
    'Topping': '🍯',
    'Snack': '🍿',
  };

  let activeRasa = null;
  let activeKategori = null;

  function clearAllActive() {
    activeRasa = null;
    activeKategori = null;
    document.querySelectorAll('.flavor-card').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.kategori-card').forEach(c => c.classList.remove('active'));
  }

  function filterByRasa(namaRasa, cardEl) {
    if (activeRasa === namaRasa) {
      resetFilter();
      return;
    }
    clearAllActive();
    activeRasa = namaRasa;

    // Tandai card aktif
    cardEl.classList.add('active');

    // Tampilkan seksi hasil
    const section = document.getElementById('resultsSection');
    const grid = document.getElementById('resultsGrid');
    const label = document.getElementById('activeFilterLabel');
    const resetBtn = document.getElementById('resetBtn');

    section.style.display = 'block';
    label.textContent = (emojiMap[namaRasa] || '🍽️') + ' Rasa ' + namaRasa;
    resetBtn.classList.add('visible');

    // Loading state
    grid.innerHTML = '<div class="loading-spinner">⏳ Memuat produk...</div>';

    // Scroll ke seksi hasil
    section.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });

    // Fetch produk
    fetch('get_produk.php?rasa=' + encodeURIComponent(namaRasa))
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          showError(grid, data.error);
        } else {
          renderProducts(data, namaRasa);
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        showError(grid, 'Gagal memuat data. Periksa koneksi database.');
      });
  }

  function filterByKategori(namaKategori, cardEl) {
    if (activeKategori === namaKategori) {
      resetFilter();
      return;
    }
    clearAllActive();
    activeKategori = namaKategori;

    // Tandai card aktif
    cardEl.classList.add('active');

    const section = document.getElementById('resultsSection');
    const grid = document.getElementById('resultsGrid');
    const label = document.getElementById('activeFilterLabel');
    const resetBtn = document.getElementById('resetBtn');

    const emoji = kategoriEmojiMap[namaKategori] || '📂';
    section.style.display = 'block';
    label.textContent = emoji + ' Kategori ' + namaKategori;
    resetBtn.classList.add('visible');

    grid.innerHTML = '<div class="loading-spinner">⏳ Memuat produk...</div>';

    section.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });

    fetch('get_produk.php?kategori=' + encodeURIComponent(namaKategori))
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          showError(grid, data.error);
        } else {
          renderKategoriProducts(data, namaKategori);
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        showError(grid, 'Gagal memuat data. Periksa koneksi database.');
      });
  }

  function showError(grid, message) {
    grid.innerHTML = `
            <div class="empty-state">
                <div class="empty-emoji">⚠️</div>
                <p>${escapeHtml(message)}</p>
            </div>
        `;
  }

  function renderProducts(products, namaRasa) {
    const grid = document.getElementById('resultsGrid');

    if (!products || products.length === 0) {
      grid.innerHTML = `
                <div class="empty-state">
                    <div class="empty-emoji">😕</div>
                    <p>Tidak ada produk dengan rasa <strong>${escapeHtml(namaRasa)}</strong> saat ini.</p>
                </div>
            `;
      return;
    }

    const emoji = emojiMap[namaRasa] || '🍽️';
    grid.innerHTML = products.map(product => {
      return `
                <a href="detail_produk.php?id=${product.id_produk}" class="product-card" style="text-decoration:none;cursor:pointer;display:block;">
                    <div class="product-card-top">${emoji}</div>
                    <div class="product-card-body">
                        <div class="product-name">${escapeHtml(product.nama_produk)}</div>
                        <div class="product-umkm">🏪 ${escapeHtml(product.nama_umkm)}</div>
                        <div class="product-tags">
                            <span class="product-tag tag-rasa-${escapeHtml(namaRasa)}">
                                ${emoji} ${escapeHtml(namaRasa)}
                            </span>
                            ${product.daftar_rasa ? `
                                <span class="product-tag" style="background:#f3f4f6;color:#6b7280;">
                                     ${escapeHtml(product.daftar_rasa)}
                                </span>
                            ` : ''}
                            ${product.kategori_produk ? `
                                <span class="product-tag" style="background:#e0e7ff;color:#3730a3;">
                                    📂 ${escapeHtml(product.kategori_produk)}
                                </span>
                            ` : ''}
                        </div>
                        <div class="product-harga">Rp ${formatRupiah(product.harga)}</div>
                    </div>
                </a>
            `;
    }).join('');
  }

  function renderKategoriProducts(products, namaKategori) {
    const grid = document.getElementById('resultsGrid');
    const emoji = kategoriEmojiMap[namaKategori] || '📂';

    if (!products || products.length === 0) {
      grid.innerHTML = `
                <div class="empty-state">
                    <div class="empty-emoji">😕</div>
                    <p>Tidak ada produk dalam kategori <strong>${escapeHtml(namaKategori)}</strong> saat ini.</p>
                </div>
            `;
      return;
    }

    grid.innerHTML = products.map(product => {
      return `
                <a href="detail_produk.php?id=${product.id_produk}" class="product-card" style="text-decoration:none;cursor:pointer;display:block;">
                    <div class="product-card-top">${emoji}</div>
                    <div class="product-card-body">
                        <div class="product-name">${escapeHtml(product.nama_produk)}</div>
                        <div class="product-umkm">🏪 ${escapeHtml(product.nama_umkm)}</div>
                        <div class="product-tags">
                            <span class="product-tag" style="background:#e0e7ff;color:#3730a3;">
                                ${emoji} ${escapeHtml(namaKategori)}
                            </span>
                            ${product.daftar_rasa ? `
                                <span class="product-tag" style="background:#f3f4f6;color:#6b7280;">
                                     ${escapeHtml(product.daftar_rasa)}
                                </span>
                            ` : ''}
                        </div>
                        <div class="product-harga">Rp ${formatRupiah(product.harga)}</div>
                    </div>
                </a>
            `;
    }).join('');
  }

  function resetFilter() {
    clearAllActive();
    document.getElementById('resultsSection').style.display = 'none';
    document.getElementById('resetBtn').classList.remove('visible');
  }

  function doSearch() {
    const keyword = document.getElementById('searchInput').value.trim();

    if (!keyword) {
      alert('Masukkan kata kunci pencarian!');
      return;
    }

    const section = document.getElementById('resultsSection');
    const grid = document.getElementById('resultsGrid');
    const label = document.getElementById('activeFilterLabel');
    const resetBtn = document.getElementById('resetBtn');

    // Reset active rasa jika ada
    if (activeRasa) {
      activeRasa = null;
      document.querySelectorAll('.flavor-card').forEach(c => c.classList.remove('active'));
    }

    section.style.display = 'block';
    label.textContent = '🔍 "' + escapeHtml(keyword) + '"';
    resetBtn.classList.add('visible');
    grid.innerHTML = '<div class="loading-spinner">⏳ Mencari produk...</div>';

    section.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });

    fetch('get_produk.php?keyword=' + encodeURIComponent(keyword))
      .then(response => response.json())
      .then(data => {
        if (!data || data.length === 0) {
          grid.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-emoji">😕</div>
                            <p>Produk "<strong>${escapeHtml(keyword)}</strong>" tidak ditemukan.</p>
                        </div>
                    `;
          return;
        }
        grid.innerHTML = data.map(product => {
          return `
                        <a href="detail_produk.php?id=${product.id_produk}" class="product-card" style="text-decoration:none;cursor:pointer;display:block;">
                            <div class="product-card-top">🍽️</div>
                            <div class="product-card-body">
                                <div class="product-name">${escapeHtml(product.nama_produk)}</div>
                                <div class="product-umkm">🏪 ${escapeHtml(product.nama_umkm)}</div>
                                <div class="product-tags">
                                    ${product.kategori_produk ? `
                                        <span class="product-tag" style="background:#e0e7ff;color:#3730a3;">
                                            📂 ${escapeHtml(product.kategori_produk)}
                                        </span>
                                    ` : ''}
                                    ${product.daftar_rasa ? `
                                        <span class="product-tag" style="background:#f3f4f6;color:#6b7280;">
                                             ${escapeHtml(product.daftar_rasa)}
                                        </span>
                                    ` : ''}
                                </div>
                                <div class="product-harga">Rp ${formatRupiah(product.harga)}</div>
                            </div>
                        </a>
                    `;
        }).join('');
      })
      .catch(error => {
        console.error('Search error:', error);
        grid.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-emoji"></div>
                        <p>Gagal memuat data. Periksa koneksi database.</p>
                    </div>
                `;
      });
  }

  function escapeHtml(str) {
    if (!str) return '';
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }


  function formatRupiah(num) {
    return Number(num).toLocaleString('id-ID');
  }
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          doSearch();
        }
      });
    }
  });
  </script>
</body>

</html>
