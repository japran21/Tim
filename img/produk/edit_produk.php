<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID produk tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: produk.php');
    exit;
}

$id_produk = (int)$_GET['id'];

// Ambil data produk
$query = "SELECT p.*, u.nama_umkm 
          FROM produk p 
          JOIN umkm u ON p.id_umkm = u.id_umkm 
          WHERE p.id_produk = $id_produk";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['message'] = 'Data produk tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: produk.php');
    exit;
}

$produk = mysqli_fetch_assoc($result);

// Ambil data UMKM untuk dropdown
$query_umkm = "SELECT id_umkm, nama_umkm FROM umkm ORDER BY nama_umkm";
$result_umkm = mysqli_query($koneksi, $query_umkm);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_umkm = (int)$_POST['id_umkm'];
    $nama_produk = trim($_POST['nama_produk']);
    $harga = (float)$_POST['harga'];
    $kategori_produk = trim($_POST['kategori_produk']);
    $asal_daerah = !empty($_POST['asal_daerah']) ? trim($_POST['asal_daerah']) : NULL;
    
    $errors = [];
    if ($id_umkm <= 0) $errors[] = 'Pilih UMKM terlebih dahulu';
    if (empty($nama_produk)) $errors[] = 'Nama produk tidak boleh kosong';
    if ($harga <= 0) $errors[] = 'Harga harus lebih dari 0';
    if (empty($kategori_produk)) $errors[] = 'Kategori produk tidak boleh kosong';
    
    if (empty($errors)) {
        $asal = $asal_daerah ? "'" . mysqli_real_escape_string($koneksi, $asal_daerah) . "'" : "NULL";
        $update = "UPDATE produk 
                   SET id_umkm = $id_umkm, 
                       nama_produk = '$nama_produk', 
                       harga = $harga, 
                       kategori_produk = '$kategori_produk', 
                       asal_daerah = $asal 
                   WHERE id_produk = $id_produk";
        
        if (mysqli_query($koneksi, $update)) {
            $_SESSION['message'] = 'Produk "' . htmlspecialchars($nama_produk) . '" berhasil diupdate!';
            $_SESSION['message_type'] = 'success';
            header('Location: produk.php');
            exit;
        } else {
            $error = 'Gagal mengupdate data: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Produk - UMKM Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .form-container {
    max-width: 700px;
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
    margin-bottom: 20px;
  }

  label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
  }

  input[type="text"],
  input[type="number"],
  select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: border-color 0.2s;
    font-family: inherit;
  }

  input:focus,
  select:focus {
    outline: none;
    border-color: #2e6b4f;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
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

  .btn-rasa {
    background: #fef3c7;
    color: #92400e;
    padding: 12px 28px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .error-message {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
  }

  .info-card {
    background: #e0f2fe;
    padding: 12px 16px;
    border-radius: 12px;
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
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
        <a href="produk.php">Kelola Produk</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="form-container">
    <div class="form-card">
      <h1 class="form-title">Edit Produk</h1>
      <p class="form-subtitle">Ubah informasi produk: <?= htmlspecialchars($produk['nama_produk']) ?></p>

      <?php if ($error): ?>
      <div class="error-message">
        ⚠️ <?= $error ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="id_umkm">Nama UMKM *</label>
          <select id="id_umkm" name="id_umkm" required>
            <option value="">-- Pilih UMKM --</option>
            <?php mysqli_data_seek($result_umkm, 0); ?>
            <?php while ($umkm = mysqli_fetch_assoc($result_umkm)): ?>
            <option value="<?= $umkm['id_umkm'] ?>" <?= $produk['id_umkm'] == $umkm['id_umkm'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($umkm['nama_umkm']) ?>
            </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="nama_produk">Nama Produk *</label>
          <input type="text" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>"
            required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="harga">Harga (Rp) *</label>
            <input type="number" id="harga" name="harga" value="<?= $produk['harga'] ?>" step="500" required>
          </div>

          <div class="form-group">
            <label for="kategori_produk">Kategori *</label>
            <select id="kategori_produk" name="kategori_produk" required>
              <option value="">-- Pilih Kategori --</option>
              <option value="Makanan" <?= $produk['kategori_produk'] == 'Makanan' ? 'selected' : '' ?>>🍜 Makanan
              </option>
              <option value="Minuman" <?= $produk['kategori_produk'] == 'Minuman' ? 'selected' : '' ?>>🥤 Minuman
              </option>
              <option value="Topping" <?= $produk['kategori_produk'] == 'Topping' ? 'selected' : '' ?>>🍯 Topping
              </option>
              <option value="Snack" <?= $produk['kategori_produk'] == 'Snack' ? 'selected' : '' ?>>🍿 Snack</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="asal_daerah">Asal Daerah</label>
          <input type="text" id="asal_daerah" name="asal_daerah"
            value="<?= htmlspecialchars($produk['asal_daerah'] ?? '') ?>" placeholder="Contoh: Bandung, Jawa Barat">
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Update Produk</button>
          <a href="produk.php" class="btn-cancel">Batal</a>
        </div>
      </form>

      <div class="info-card">
        <span>Atur rasa untuk produk ini:</span>
        <a href="relasi_produk_rasa.php?id_produk=<?= $id_produk ?>" class="btn-rasa">Kelola Rasa Produk</a>
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