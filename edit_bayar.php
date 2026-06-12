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

// Ambil data metode pembayaran
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
$error_type = 'normal';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_metode = trim($_POST['nama_metode']);

    if (empty($nama_metode)) {
        $error = 'Nama metode pembayaran tidak boleh kosong';
    } else {
        try {
            $nama_esc = mysqli_real_escape_string($koneksi, $nama_metode);
            $query_update = "UPDATE metode_pembayaran SET nama_metode = '$nama_esc' WHERE id_metode = $id_metode";

            if (mysqli_query($koneksi, $query_update)) {
                $_SESSION['message'] = 'Metode pembayaran "' . htmlspecialchars($nama_metode) . '" berhasil diperbarui!';
                $_SESSION['message_type'] = 'success';
                header('Location: bayar.php');
                exit;
            } else {
                $error = mysqli_error($koneksi);
                $error_type = 'rollback';
            }
        } catch (mysqli_sql_exception $e) {
            $error = $e->getMessage();
            $error_type = 'rollback';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Metode Pembayaran - Street Food Ciwaruga</title>
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
    box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
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

  input[type="text"] {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: border-color .2s;
    font-family: inherit;
    box-sizing: border-box;
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
    transition: background .2s;
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
    transition: background .2s;
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
        <a href="bayar.php" class="active">Kelola Pembayaran</a>
        <a href="mitra.php">Kelola Mitra</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="form-container">
    <div class="form-card">
      <h1 class="form-title">Edit Metode Pembayaran</h1>
      <p class="form-subtitle">Ubah nama metode pembayaran #<?= $id_metode ?></p>

      <?php if ($error && $error_type === 'rollback'): ?>
      <div
        style="background:#1a1a2e;color:#fff;border-radius:16px;padding:24px 28px;margin-bottom:24px;border-left:5px solid #ef4444;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
          <span style="font-size:1.3rem;">🔴</span>
          <strong style="font-size:.95rem;letter-spacing:.05em;text-transform:uppercase;">GAGAL — PERUBAHAN DIBATALKAN
            (ROLLBACK)</strong>
        </div>
        <div style="font-size:.88rem;color:#fca5a5;margin-bottom:10px;"><?= htmlspecialchars($error) ?></div>
        <div style="font-size:.8rem;color:#9ca3af;">Tidak ada perubahan yang tersimpan. Silakan coba lagi.</div>
      </div>
      <?php elseif ($error): ?>
      <div class="error-message">⚠️ <?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="nama_metode">Nama Metode Pembayaran *</label>
          <input type="text" id="nama_metode" name="nama_metode" value="<?= htmlspecialchars($metode['nama_metode']) ?>"
            placeholder="Contoh: Cash, QRIS, Transfer Bank" required>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-submit" onclick="showKonfirmasi()">Simpan Perubahan</button>
          <a href="bayar.php" class="btn-cancel">Batal</a>
        </div>
      </form>

      <!-- POPUP KONFIRMASI -->
      <div id="overlay-konfirmasi"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;">
        <div
          style="background:#1a1a2e;color:#fff;border-radius:16px;padding:32px 36px;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.4);">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span style="font-size:1.3rem;">⚠️</span>
            <strong style="font-size:1rem;letter-spacing:.05em;text-transform:uppercase;">KONFIRMASI PERUBAHAN
              PEMBAYARAN</strong>
          </div>
          <div style="font-size:.9rem;line-height:1.8;color:#d1d5db;margin-bottom:20px;">
            <div><span style="color:#9ca3af;width:110px;display:inline-block;">ID Metode</span>: <span
                style="color:#fff;">#<?= $id_metode ?></span></div>
            <div><span style="color:#9ca3af;width:110px;display:inline-block;">Nama Baru</span>: <span id="konfirm-nama"
                style="color:#fff;font-weight:600;"></span></div>
          </div>
          <p style="font-size:.85rem;color:#fbbf24;margin-bottom:24px;">Metode pembayaran ini akan
            <strong>DIPERBARUI</strong>. Lanjutkan?</p>
          <div style="display:flex;gap:12px;justify-content:flex-end;">
            <button onclick="submitForm()"
              style="background:#2e6b4f;color:#fff;border:none;padding:10px 28px;border-radius:40px;font-weight:600;cursor:pointer;font-size:.95rem;">Oke</button>
            <button onclick="tutupKonfirmasi()"
              style="background:#374151;color:#fff;border:none;padding:10px 28px;border-radius:40px;font-weight:600;cursor:pointer;font-size:.95rem;">Batal</button>
          </div>
        </div>
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

  <script>
  function showKonfirmasi() {
    const nama = document.getElementById('nama_metode').value.trim();
    if (!nama) {
      alert('Nama metode pembayaran wajib diisi!');
      return;
    }
    document.getElementById('konfirm-nama').textContent = nama;
    document.getElementById('overlay-konfirmasi').style.display = 'flex';
  }

  function tutupKonfirmasi() {
    document.getElementById('overlay-konfirmasi').style.display = 'none';
  }

  function submitForm() {
    document.querySelector('form').submit();
  }
  document.getElementById('overlay-konfirmasi').addEventListener('click', function(e) {
    if (e.target === this) tutupKonfirmasi();
  });
  </script>
</body>

</html>