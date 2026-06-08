<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UMKM Ciwaruga</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <style>
    /* ===== FILTER RASA ===== */
    .filter-rasa-box {
      background: #fff;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      margin-top: 16px;
    }

    .filter-rasa-box h4 {
      font-size: 0.85rem;
      font-weight: 700;
      color: #888;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      margin: 0 0 12px 0;
    }

    .filter-rasa-buttons {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .btn-rasa {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 14px;
      border-radius: 10px;
      border: 2px solid transparent;
      background: #f7f7f7;
      cursor: pointer;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
      color: #333;
      transition: all 0.2s;
      text-align: left;
    }

    .btn-rasa:hover {
      background: #f0f4ff;
      border-color: #3b5bdb;
      color: #3b5bdb;
    }

    .btn-rasa.active {
      background: #3b5bdb;
      color: #fff;
      border-color: #3b5bdb;
    }

    .btn-rasa .rasa-emoji {
      font-size: 1.2rem;
    }

    /* ===== SEARCH RESULT STYLES ===== */
    #hasil-pencarian {
      padding: 48px 24px;
      max-width: 1100px;
      margin: 0 auto;
    }

    #hasil-pencarian h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      margin-bottom: 8px;
      color: #1a1a1a;
    }

    #hasil-pencarian .result-info {
      color: #666;
      margin-bottom: 28px;
      font-size: 0.95rem;
    }

    .umkm-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 24px;
    }

    .umkm-card {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .umkm-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    }

    .umkm-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      background: #f3f3f3;
    }

    .umkm-card .img-placeholder {
      width: 100%;
      height: 200px;
      background: linear-gradient(135deg, #f5a623, #f76b1c);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 4rem;
    }

    .umkm-card-body {
      padding: 20px;
    }

    .umkm-card-body h3 {
      font-size: 1.1rem;
      font-weight: 700;
      margin: 0 0 8px 0;
      color: #1a1a1a;
    }

    .umkm-card-body .badge-halal {
      display: inline-block;
      font-size: 0.7rem;
      padding: 3px 8px;
      border-radius: 20px;
      margin-bottom: 10px;
      font-weight: 600;
    }

    .badge-halal.bersertifikat { background: #e6f9ee; color: #1a8c42; }
    .badge-halal.belum { background: #fff3e0; color: #e65c00; }

    .umkm-card-body .harga {
      font-size: 1rem;
      color: #e65c00;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .umkm-card-body .rasa-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-bottom: 10px;
    }

    .rasa-tags .tag {
      background: #f0f4ff;
      color: #3b5bdb;
      font-size: 0.72rem;
      padding: 3px 10px;
      border-radius: 20px;
      font-weight: 500;
    }

    .umkm-card-body .lokasi {
      font-size: 0.8rem;
      color: #888;
      margin-bottom: 10px;
      display: flex;
      align-items: flex-start;
      gap: 4px;
    }

    .umkm-card-body .produk-list {
      font-size: 0.82rem;
      color: #444;
      border-top: 1px solid #f0f0f0;
      padding-top: 10px;
      margin-top: 6px;
    }

    .produk-list strong { display: block; margin-bottom: 4px; color: #1a1a1a; }
    .produk-list ul { margin: 0; padding-left: 18px; max-height: 90px; overflow-y: auto; }
    .produk-list ul li { margin-bottom: 2px; }

    .not-found { text-align: center; padding: 60px 20px; color: #888; }
    .not-found .icon { font-size: 3rem; margin-bottom: 12px; }

    .loading {
      text-align: center;
      padding: 40px;
      color: #888;
      font-size: 1rem;
    }

    .loading::after {
      content: '';
      display: inline-block;
      width: 18px;
      height: 18px;
      border: 3px solid #ddd;
      border-top-color: #f76b1c;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
      margin-left: 10px;
      vertical-align: middle;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* Beranda link aktif */
    .nav-links a { cursor: pointer; }

    /* Fix layout hero-visual */
    .hero-visual {
      display: flex !important;
      flex-direction: column !important;
      gap: 16px !important;
      align-items: stretch !important;
    }

    .visual-card.card-1 {
      width: 100% !important;
      box-sizing: border-box;
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
        <a onclick="kembaliKeBeranda()">Beranda</a>
        <a href="#">Tentang</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <!-- ===== HERO ===== -->
  <section class="hero" id="hero-section">
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
          <input type="text" id="search-input" placeholder="Cari makanan, contoh: batagor, surabi, mie ayam..." />
          <button class="search-btn" id="search-btn" onclick="cariMakanan()">Cari</button>
        </div>
        <div class="search-tags">
          <span onclick="cariDenganKeyword('Batagor')">🍢 Batagor</span>
          <span onclick="cariDenganKeyword('Mie Ayam')">🍜 Mie Ayam</span>
          <span onclick="cariDenganKeyword('Surabi')">🥞 Surabi</span>
          <span onclick="cariDenganKeyword('Es Kelapa')">🥥 Es Kelapa</span>
        </div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="visual-card card-1">
        <div class="card-emoji">🍜</div>
        <div>
          <div class="card-title">Street Food</div>
          <div class="card-sub">21 UMKM</div>
        </div>
      </div>

      <!-- FILTER RASA -->
      <div class="filter-rasa-box">
        <h4>🎯 Filter Rasa</h4>
        <div class="filter-rasa-buttons">
          <button class="btn-rasa" onclick="filterRasa('Asam', this)"><span class="rasa-emoji">🍋</span> Asam</button>
          <button class="btn-rasa" onclick="filterRasa('Asin', this)"><span class="rasa-emoji">🧂</span> Asin</button>
          <button class="btn-rasa" onclick="filterRasa('Gurih', this)"><span class="rasa-emoji">🍗</span> Gurih</button>
          <button class="btn-rasa" onclick="filterRasa('Manis', this)"><span class="rasa-emoji">🍯</span> Manis</button>
          <button class="btn-rasa" onclick="filterRasa('Pedas', this)"><span class="rasa-emoji">🌶️</span> Pedas</button>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== HASIL PENCARIAN ===== -->
  <section id="hasil-pencarian" style="display:none;">
    <h2 id="judul-hasil">Hasil Pencarian</h2>
    <p class="result-info" id="info-hasil"></p>
    <div class="umkm-grid" id="grid-hasil"></div>
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
    // Tekan Enter untuk cari
    document.getElementById('search-input').addEventListener('keydown', function(e) {
      if (e.key === 'Enter') cariMakanan();
    });

    function kembaliKeBeranda() {
      // Sembunyikan hasil pencarian
      document.getElementById('hasil-pencarian').style.display = 'none';
      // Reset input search
      document.getElementById('search-input').value = '';
      // Reset tombol filter rasa
      document.querySelectorAll('.btn-rasa').forEach(b => b.classList.remove('active'));
      // Scroll ke atas
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function cariDenganKeyword(keyword) {
      document.getElementById('search-input').value = keyword;
      cariMakanan();
    }

    function filterRasa(rasa, btn) {
      // Toggle active
      document.querySelectorAll('.btn-rasa').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const section = document.getElementById('hasil-pencarian');
      const grid = document.getElementById('grid-hasil');
      const judul = document.getElementById('judul-hasil');
      const info = document.getElementById('info-hasil');

      section.style.display = 'block';
      judul.textContent = 'Makanan Rasa ' + rasa;
      info.textContent = '';
      grid.innerHTML = '<div class="loading">Sedang memfilter</div>';

      section.scrollIntoView({ behavior: 'smooth', block: 'start' });

      fetch('filter_rasa.php?rasa=' + encodeURIComponent(rasa))
        .then(res => res.json())
        .then(data => tampilkanHasil(data, 'rasa ' + rasa))
        .catch(err => {
          grid.innerHTML = '<div class="not-found"><div class="icon">⚠️</div><p>Gagal terhubung ke server.</p></div>';
          judul.textContent = 'Koneksi Gagal';
        });
    }

    function cariMakanan() {
      const keyword = document.getElementById('search-input').value.trim();
      if (!keyword) {
        alert('Masukkan kata kunci pencarian terlebih dahulu!');
        return;
      }

      // Reset filter rasa
      document.querySelectorAll('.btn-rasa').forEach(b => b.classList.remove('active'));

      const section = document.getElementById('hasil-pencarian');
      const grid = document.getElementById('grid-hasil');
      const judul = document.getElementById('judul-hasil');
      const info = document.getElementById('info-hasil');

      section.style.display = 'block';
      judul.textContent = 'Mencari "' + keyword + '"...';
      info.textContent = '';
      grid.innerHTML = '<div class="loading">Sedang mencari</div>';

      section.scrollIntoView({ behavior: 'smooth', block: 'start' });

      fetch('search_produk.php?keyword=' + encodeURIComponent(keyword))
        .then(res => res.json())
        .then(data => tampilkanHasil(data, '"' + keyword + '"'))
        .catch(err => {
          grid.innerHTML = '<div class="not-found"><div class="icon">⚠️</div><p>Gagal terhubung ke server. Pastikan XAMPP berjalan.</p></div>';
          document.getElementById('judul-hasil').textContent = 'Koneksi Gagal';
        });
    }

    function tampilkanHasil(data, label) {
      const grid = document.getElementById('grid-hasil');
      const judul = document.getElementById('judul-hasil');
      const info = document.getElementById('info-hasil');

      if (data.error) {
        grid.innerHTML = '<div class="not-found"><div class="icon">⚠️</div><p>' + data.error + '</p></div>';
        judul.textContent = 'Terjadi Kesalahan';
        return;
      }

      if (data.length === 0) {
        judul.textContent = 'Tidak Ditemukan';
        info.textContent = 'Tidak ada makanan yang cocok dengan ' + label;
        grid.innerHTML = '<div class="not-found"><div class="icon">🔍</div><p>Coba kata kunci atau filter lain.</p></div>';
        return;
      }

      judul.textContent = 'Hasil untuk ' + label;
      info.textContent = 'Ditemukan ' + data.length + ' UMKM yang sesuai';

      grid.innerHTML = data.map(umkm => {
        const fotoUrl = umkm.foto ? umkm.foto : null;
        const gambar = fotoUrl
          ? `<img src="${fotoUrl}" alt="${umkm.nama_umkm}" onerror="this.parentElement.innerHTML='<div class=\\'img-placeholder\\'>🍽️</div>'">`
          : `<div class="img-placeholder">🍽️</div>`;

        const isHalal = umkm.status_halal === 'Halal Bersertifikat';
        const badgeClass = isHalal ? 'bersertifikat' : 'belum';

        const hargaMulai = formatRupiah(umkm.harga_mulai);
        const hargaMaks = formatRupiah(umkm.harga_maks);
        const hargaStr = umkm.harga_mulai === umkm.harga_maks ? hargaMulai : hargaMulai + ' – ' + hargaMaks;

        const rasaTags = umkm.daftar_rasa
          ? umkm.daftar_rasa.split(', ').map(r => `<span class="tag">${r}</span>`).join('')
          : '';

        const produkItems = umkm.produk_list.slice(0, 8).map(p => `<li>${p}</li>`).join('');
        const sisanya = umkm.produk_list.length > 8 ? `<li style="color:#aaa">...dan ${umkm.produk_list.length - 8} lainnya</li>` : '';

        const asalBersih = umkm.asal_daerah
          ? [...new Set(umkm.asal_daerah.split(', ').filter(a => a && a !== 'NULL'))].join(', ')
          : '';

        return `
          <div class="umkm-card">
            ${gambar}
            <div class="umkm-card-body">
              <h3>${umkm.nama_umkm}</h3>
              <span class="badge-halal ${badgeClass}">${umkm.status_halal}</span>
              <div class="harga">💰 Rp ${hargaStr}</div>
              <div class="rasa-tags">${rasaTags}</div>
              ${asalBersih ? `<div class="lokasi">📍 Asal: ${asalBersih}</div>` : ''}
              <div class="lokasi">🗺️ ${umkm.lokasi}</div>
              <div class="produk-list">
                <strong>Menu yang tersedia:</strong>
                <ul>${produkItems}${sisanya}</ul>
              </div>
            </div>
          </div>
        `;
      }).join('');
    }

    function formatRupiah(angka) {
      return new Intl.NumberFormat('id-ID').format(angka);
    }
  </script>
</body>
</html>
