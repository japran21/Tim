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
  <style>
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

    .badge-halal.bersertifikat {
      background: #e6f9ee;
      color: #1a8c42;
    }

    .badge-halal.belum {
      background: #fff3e0;
      color: #e65c00;
    }

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

    .produk-list strong {
      display: block;
      margin-bottom: 4px;
      color: #1a1a1a;
    }

    .produk-list ul {
      margin: 0;
      padding-left: 18px;
      max-height: 90px;
      overflow-y: auto;
    }

    .produk-list ul li {
      margin-bottom: 2px;
    }

    /* Tidak ditemukan */
    .not-found {
      text-align: center;
      padding: 60px 20px;
      color: #888;
    }

    .not-found .icon {
      font-size: 3rem;
      margin-bottom: 12px;
    }

    /* Loading */
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

    @keyframes spin {
      to { transform: rotate(360deg); }
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
        <a href="#">Beranda</a>
        <a href="#">Tentang</a>
      </nav>
      <div class="nav-actions">
      </div>
    </div>
  </header>

  <!-- ===== HERO ===== -->
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
        <span class="brand-name"> Street Food</span> Ciwaruga</span>
      </div>
      <p class="footer-copy">© 2026 Street Food Ciwaruga · Mendukung Usaha Lokal</p>
    </div>
  </footer>

  <script>
    // Tekan Enter untuk cari
    document.getElementById('search-input').addEventListener('keydown', function(e) {
      if (e.key === 'Enter') cariMakanan();
    });

    function cariDenganKeyword(keyword) {
      document.getElementById('search-input').value = keyword;
      cariMakanan();
    }

    function cariMakanan() {
      const keyword = document.getElementById('search-input').value.trim();
      if (!keyword) {
        alert('Masukkan kata kunci pencarian terlebih dahulu!');
        return;
      }

      const section = document.getElementById('hasil-pencarian');
      const grid = document.getElementById('grid-hasil');
      const judul = document.getElementById('judul-hasil');
      const info = document.getElementById('info-hasil');

      // Tampilkan loading
      section.style.display = 'block';
      judul.textContent = 'Mencari "' + keyword + '"...';
      info.textContent = '';
      grid.innerHTML = '<div class="loading">Sedang mencari</div>';

      // Scroll ke hasil
      section.scrollIntoView({ behavior: 'smooth', block: 'start' });

      // Fetch ke backend
      fetch('search_produk.php?keyword=' + encodeURIComponent(keyword))
        .then(res => res.json())
        .then(data => {
          if (data.error) {
            grid.innerHTML = '<div class="not-found"><div class="icon">⚠️</div><p>' + data.error + '</p></div>';
            judul.textContent = 'Terjadi Kesalahan';
            return;
          }

          if (data.length === 0) {
            judul.textContent = 'Tidak Ditemukan';
            info.textContent = 'Tidak ada makanan yang cocok dengan "' + keyword + '"';
            grid.innerHTML = '<div class="not-found"><div class="icon">🔍</div><p>Coba kata kunci lain seperti "batagor", "mie ayam", atau nama UMKM.</p></div>';
            return;
          }

          judul.textContent = 'Hasil untuk "' + keyword + '"';
          info.textContent = 'Ditemukan ' + data.length + ' UMKM yang sesuai';

          grid.innerHTML = data.map(umkm => {
            // Gambar
            const fotoUrl = umkm.foto ? umkm.foto : null;
            const gambar = fotoUrl
              ? `<img src="${fotoUrl}" alt="${umkm.nama_umkm}" onerror="this.parentElement.innerHTML='<div class=\\'img-placeholder\\'>🍽️</div>'">`
              : `<div class="img-placeholder">🍽️</div>`;

            // Badge halal
            const isHalal = umkm.status_halal === 'Halal Bersertifikat';
            const badgeClass = isHalal ? 'bersertifikat' : 'belum';
            const badgeText = umkm.status_halal;

            // Harga
            const hargaMulai = formatRupiah(umkm.harga_mulai);
            const hargaMaks = formatRupiah(umkm.harga_maks);
            const hargaStr = umkm.harga_mulai === umkm.harga_maks
              ? hargaMulai
              : hargaMulai + ' – ' + hargaMaks;

            // Rasa
            const rasaTags = umkm.daftar_rasa
              ? umkm.daftar_rasa.split(', ').map(r => `<span class="tag">${r}</span>`).join('')
              : '';

            // Produk
            const produkItems = umkm.produk_list.slice(0, 8).map(p => `<li>${p}</li>`).join('');
            const sisanya = umkm.produk_list.length > 8 ? `<li style="color:#aaa">...dan ${umkm.produk_list.length - 8} lainnya</li>` : '';

            // Asal daerah
            const asalBersih = umkm.asal_daerah
              ? [...new Set(umkm.asal_daerah.split(', ').filter(a => a && a !== 'NULL'))].join(', ')
              : '';

            return `
              <div class="umkm-card">
                ${gambar}
                <div class="umkm-card-body">
                  <h3>${umkm.nama_umkm}</h3>
                  <span class="badge-halal ${badgeClass}">${badgeText}</span>
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
        })
        .catch(err => {
          grid.innerHTML = '<div class="not-found"><div class="icon">⚠️</div><p>Gagal terhubung ke server. Pastikan XAMPP berjalan.</p></div>';
          judul.textContent = 'Koneksi Gagal';
          console.error(err);
        });
    }

    function formatRupiah(angka) {
      return new Intl.NumberFormat('id-ID').format(angka);
    }
  </script>
</body>

</html>
