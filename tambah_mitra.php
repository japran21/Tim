<<<<<<< HEAD
<?php
session_start();
require_once 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_mitra = trim($_POST['nama_mitra']);
    
    if (empty($nama_mitra)) {
        $error = 'Nama mitra platform tidak boleh kosong!';
    } else {
        // Cek apakah mitra sudah ada
        $check = mysqli_query($koneksi, "SELECT id_mitra FROM mitra_platform WHERE nama_mitra = '$nama_mitra'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Mitra platform "' . htmlspecialchars($nama_mitra) . '" sudah terdaftar!';
        } else {
            $query = "INSERT INTO mitra_platform (nama_mitra) VALUES ('$nama_mitra')";
            if (mysqli_query($koneksi, $query)) {
                $_SESSION['message'] = 'Mitra platform "' . htmlspecialchars($nama_mitra) . '" berhasil ditambahkan!';
                $_SESSION['message_type'] = 'success';
                header('Location: mitra.php');
                exit;
            } else {
                $error = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Mitra Platform - UMKM Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .form-container {
    max-width: 600px;
    margin: 40px auto;
    padding: 0 24px;
  }

  .form-card {
    background: white;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }

  .form-title {
    font-family: 'Playfair Display', serif;
    color: #1a1a2e;
    font-size: 1.8rem;
    margin-bottom: 8px;
  }

  .form-subtitle {
    color: #6b7280;
    margin-bottom: 32px;
  }

  .form-group {
    margin-bottom: 24px;
  }

  label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
  }

  input[type="text"],
  select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: border-color 0.2s;
    font-family: inherit;
  }

  input[type="text"]:focus,
  select:focus {
    outline: none;
    border-color: #2e6b4f;
  }

  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
  }

  .btn-submit {
    background: #2e6b4f;
    color: white;
    padding: 12px 28px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .btn-submit:hover {
    background: #1a2e1e;
  }

  .btn-cancel {
    background: #f3f4f6;
    color: #6b7280;
    padding: 12px 28px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: background 0.2s;
  }

  .btn-cancel:hover {
    background: #e5e7eb;
  }

  .error-message {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
  }

  .icon-tip {
    background: #fef3c7;
    color: #92400e;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 0.85rem;
    margin-top: 16px;
  }

  .mitra-grid {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 12px;
  }

  .mitra-option {
    padding: 10px 20px;
    background: #f3f4f6;
    border-radius: 40px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .mitra-option:hover {
    background: #e5e7eb;
    transform: scale(1.02);
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
        <a href="kategori_rasa.php">Kelola Rasa</a>
        <a href="bayar.php">Kelola Pembayaran</a>
        <a href="mitra.php">Kelola Mitra</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="form-container">
    <div class="form-card">
      <h1 class="form-title">➕ Tambah Mitra Platform</h1>
      <p class="form-subtitle">Silakan masukkan mitra platform delivery makanan baru</p>

      <?php if ($error): ?>
      <div class="error-message">
        ⚠️ <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="nama_mitra">Nama Mitra Platform *</label>
          <input type="text" id="nama_mitra" name="nama_mitra" placeholder="Contoh: GoFood, GrabFood, ShopeeFood"
            required value="<?= isset($_POST['nama_mitra']) ? htmlspecialchars($_POST['nama_mitra']) : '' ?>">
          <div class="icon-tip">
            💡 <strong>Mitra platform yang umum digunakan:</strong>
            <div class="mitra-grid">
              <div class="mitra-option" onclick="setMitra('GoFood')">🟢 GoFood</div>
              <div class="mitra-option" onclick="setMitra('GrabFood')">🟠 GrabFood</div>
              <div class="mitra-option" onclick="setMitra('ShopeeFood')">🟡 ShopeeFood</div>
              <div class="mitra-option" onclick="setMitra('Gojek')">🟢 Gojek</div>
              <div class="mitra-option" onclick="setMitra('Grab')">🟠 Grab</div>
              <div class="mitra-option" onclick="setMitra('Shopee')">🟡 Shopee</div>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Simpan</button>
          <a href="mitra.php" class="btn-cancel">Batal</a>
        </div>
      </form>
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

  <script>
  function setMitra(nama) {
    document.getElementById('nama_mitra').value = nama;
  }
  </script>
</body>

=======
<?php
session_start();
require_once 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_mitra = trim($_POST['nama_mitra']);
    
    if (empty($nama_mitra)) {
        $error = 'Nama mitra platform tidak boleh kosong!';
    } else {
        // Cek apakah mitra sudah ada
        $check = mysqli_query($koneksi, "SELECT id_mitra FROM mitra_platform WHERE nama_mitra = '$nama_mitra'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Mitra platform "' . htmlspecialchars($nama_mitra) . '" sudah terdaftar!';
        } else {
            $query = "INSERT INTO mitra_platform (nama_mitra) VALUES ('$nama_mitra')";
            if (mysqli_query($koneksi, $query)) {
                $_SESSION['message'] = 'Mitra platform "' . htmlspecialchars($nama_mitra) . '" berhasil ditambahkan!';
                $_SESSION['message_type'] = 'success';
                header('Location: mitra.php');
                exit;
            } else {
                $error = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Mitra Platform - UMKM Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .form-container {
    max-width: 600px;
    margin: 40px auto;
    padding: 0 24px;
  }

  .form-card {
    background: white;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }

  .form-title {
    font-family: 'Playfair Display', serif;
    color: #1a1a2e;
    font-size: 1.8rem;
    margin-bottom: 8px;
  }

  .form-subtitle {
    color: #6b7280;
    margin-bottom: 32px;
  }

  .form-group {
    margin-bottom: 24px;
  }

  label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
  }

  input[type="text"],
  select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: border-color 0.2s;
    font-family: inherit;
  }

  input[type="text"]:focus,
  select:focus {
    outline: none;
    border-color: #2e6b4f;
  }

  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
  }

  .btn-submit {
    background: #2e6b4f;
    color: white;
    padding: 12px 28px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .btn-submit:hover {
    background: #1a2e1e;
  }

  .btn-cancel {
    background: #f3f4f6;
    color: #6b7280;
    padding: 12px 28px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: background 0.2s;
  }

  .btn-cancel:hover {
    background: #e5e7eb;
  }

  .error-message {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
  }

  .icon-tip {
    background: #fef3c7;
    color: #92400e;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 0.85rem;
    margin-top: 16px;
  }

  .mitra-grid {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 12px;
  }

  .mitra-option {
    padding: 10px 20px;
    background: #f3f4f6;
    border-radius: 40px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .mitra-option:hover {
    background: #e5e7eb;
    transform: scale(1.02);
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
        <a href="kategori_rasa.php">Kelola Rasa</a>
        <a href="bayar.php">Kelola Pembayaran</a>
        <a href="mitra.php">Kelola Mitra</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="form-container">
    <div class="form-card">
      <h1 class="form-title">➕ Tambah Mitra Platform</h1>
      <p class="form-subtitle">Silakan masukkan mitra platform delivery makanan baru</p>

      <?php if ($error): ?>
      <div class="error-message">
        ⚠️ <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="nama_mitra">Nama Mitra Platform *</label>
          <input type="text" id="nama_mitra" name="nama_mitra" placeholder="Contoh: GoFood, GrabFood, ShopeeFood"
            required value="<?= isset($_POST['nama_mitra']) ? htmlspecialchars($_POST['nama_mitra']) : '' ?>">
          <div class="icon-tip">
            💡 <strong>Mitra platform yang umum digunakan:</strong>
            <div class="mitra-grid">
              <div class="mitra-option" onclick="setMitra('GoFood')">🟢 GoFood</div>
              <div class="mitra-option" onclick="setMitra('GrabFood')">🟠 GrabFood</div>
              <div class="mitra-option" onclick="setMitra('ShopeeFood')">🟡 ShopeeFood</div>
              <div class="mitra-option" onclick="setMitra('Gojek')">🟢 Gojek</div>
              <div class="mitra-option" onclick="setMitra('Grab')">🟠 Grab</div>
              <div class="mitra-option" onclick="setMitra('Shopee')">🟡 Shopee</div>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Simpan</button>
          <a href="mitra.php" class="btn-cancel">Batal</a>
        </div>
      </form>
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

  <script>
  function setMitra(nama) {
    document.getElementById('nama_mitra').value = nama;
  }
  </script>
</body>

>>>>>>> fcfb940 (update)
</html>