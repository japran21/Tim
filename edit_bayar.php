<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID metode pembayaran tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: bayar.php');
    exit;
}

$id_metode = (int)$_GET['id'];

// Ambil data metode
$query = "SELECT * FROM metode_pembayaran WHERE id_metode = $id_metode";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['message'] = 'Data metode pembayaran tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: bayar.php');
    exit;
}

$metode = mysqli_fetch_assoc($result);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_metode = trim($_POST['nama_metode']);
    
    if (empty($nama_metode)) {
        $error = 'Nama metode pembayaran tidak boleh kosong!';
    } else {
        // Cek apakah metode sudah ada (kecuali metode yang sedang diedit)
        $check = mysqli_query($koneksi, "SELECT id_metode FROM metode_pembayaran WHERE nama_metode = '$nama_metode' AND id_metode != $id_metode");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Metode pembayaran "' . htmlspecialchars($nama_metode) . '" sudah terdaftar!';
        } else {
            $update = "UPDATE metode_pembayaran SET nama_metode = '$nama_metode' WHERE id_metode = $id_metode";
            if (mysqli_query($koneksi, $update)) {
                $_SESSION['message'] = 'Metode pembayaran berhasil diupdate!';
                $_SESSION['message_type'] = 'success';
                header('Location: bayar.php');
                exit;
            } else {
                $error = 'Gagal mengupdate data: ' . mysqli_error($koneksi);
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
  <title>Edit Metode Pembayaran - UMKM Ciwaruga</title>
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

  input[type="text"] {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: border-color 0.2s;
    font-family: inherit;
  }

  input[type="text"]:focus {
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

  .info-card {
    background: #e0f2fe;
    padding: 12px 16px;
    border-radius: 12px;
    margin-top: 20px;
  }

  .current-icon {
    font-size: 2rem;
    text-align: center;
    margin-bottom: 16px;
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
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="form-container">
    <div class="form-card">
      <h1 class="form-title">✏️ Edit Metode Pembayaran</h1>
      <p class="form-subtitle">Ubah informasi metode pembayaran</p>

      <?php 
            $iconMap = [
                'Cash' => '💵',
                'QRIS' => '📱',
                'Dana' => '💜',
                'OVO' => '🟣',
                'GoPay' => '🟢',
                'LinkAja' => '🔵'
            ];
            $currentIcon = $iconMap[$metode['nama_metode']] ?? '💰';
            ?>

      <div class="current-icon">
        Icon saat ini: <?= $currentIcon ?>
      </div>

      <?php if ($error): ?>
      <div class="error-message">
        ⚠️ <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="nama_metode">Nama Metode Pembayaran *</label>
          <input type="text" id="nama_metode" name="nama_metode" required
            value="<?= htmlspecialchars($metode['nama_metode']) ?>">
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Update</button>
          <a href="bayar.php" class="btn-cancel">Batal</a>
        </div>
      </form>

      <div class="info-card">
        ℹ️ <strong>Informasi:</strong> Perubahan nama metode pembayaran akan mempengaruhi data UMKM yang menggunakan
        metode ini.
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