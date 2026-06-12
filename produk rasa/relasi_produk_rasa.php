<?php
session_start();
require_once 'koneksi.php';

$id_produk = isset($_GET['id_produk']) ? (int)$_GET['id_produk'] : 0;

if ($id_produk <= 0) {
    $_SESSION['message'] = 'ID produk tidak valid!';
    $_SESSION['message_type'] = 'error';
    header('Location: produk.php');
    exit;
}

// Ambil data produk
$queryProduk = "SELECT p.*, u.nama_umkm 
                FROM produk p 
                JOIN umkm u ON p.id_umkm = u.id_umkm 
                WHERE p.id_produk = $id_produk";
$resultProduk = mysqli_query($koneksi, $queryProduk);
$produk = mysqli_fetch_assoc($resultProduk);

if (!$produk) {
    $_SESSION['message'] = 'Data produk tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: produk.php');
    exit;
}

// Ambil semua rasa
$queryRasa = "SELECT * FROM kategori_rasa ORDER BY id_rasa";
$resultRasa = mysqli_query($koneksi, $queryRasa);

// Ambil rasa yang sudah dimiliki produk
$queryRasaProduk = "SELECT id_rasa FROM produk_rasa WHERE id_produk = $id_produk";
$resultRasaProduk = mysqli_query($koneksi, $queryRasaProduk);
$rasaTerpilih = [];
while ($row = mysqli_fetch_assoc($resultRasaProduk)) {
    $rasaTerpilih[] = $row['id_rasa'];
}

// Proses update relasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_rasa') {
    $rasa_ids = isset($_POST['rasa_ids']) ? $_POST['rasa_ids'] : [];
    
    // Hapus semua relasi lama
    mysqli_query($koneksi, "DELETE FROM produk_rasa WHERE id_produk = $id_produk");
    
    // Insert relasi baru
    if (!empty($rasa_ids)) {
        $values = [];
        foreach ($rasa_ids as $id_rasa) {
            $id_rasa = (int)$id_rasa;
            $values[] = "($id_produk, $id_rasa)";
        }
        $sql = "INSERT INTO produk_rasa (id_produk, id_rasa) VALUES " . implode(', ', $values);
        if (mysqli_query($koneksi, $sql)) {
            $_SESSION['message'] = 'Relasi rasa produk berhasil diupdate!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Gagal mengupdate relasi: ' . mysqli_error($koneksi);
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'Semua rasa telah dihapus dari produk ini.';
        $_SESSION['message_type'] = 'warning';
    }
    
    // Refresh daftar rasa terpilih
    $rasaTerpilih = $rasa_ids;
    header("Location: relasi_produk_rasa.php?id_produk=$id_produk");
    exit;
}

// Hapus relasi tertentu
if (isset($_GET['hapus_rasa']) && isset($_GET['id_rasa'])) {
    $id_rasa_hapus = (int)$_GET['id_rasa'];
    $queryDelete = "DELETE FROM produk_rasa WHERE id_produk = $id_produk AND id_rasa = $id_rasa_hapus";
    if (mysqli_query($koneksi, $queryDelete)) {
        $_SESSION['message'] = 'Rasa berhasil dihapus dari produk!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Gagal menghapus rasa: ' . mysqli_error($koneksi);
        $_SESSION['message_type'] = 'error';
    }
    header("Location: relasi_produk_rasa.php?id_produk=$id_produk");
    exit;
}

// Emoji mapping per rasa
$emojiRasa = [
    'Asin' => '',
    'Gurih' => '',
    'Manis' => '🍯',
    'Pedas' => '',
    'Asam' => '',
];

// Warna untuk setiap rasa
$colorRasa = [
    'Asin' => '#3b82f6',
    'Gurih' => '#f59e0b',
    'Manis' => '#ec4899',
    'Pedas' => '#ef4444',
    'Asam' => '#22c55e',
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Rasa Produk - UMKM Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 24px;
  }

  .card {
    background: white;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }

  .product-info {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 24px;
    border-radius: 20px;
    margin-bottom: 28px;
    border: 1px solid #e2e8f0;
  }

  .product-name {
    font-size: 1.6rem;
    font-weight: bold;
    color: #1a1a2e;
    font-family: 'Playfair Display', serif;
  }

  .product-umkm {
    color: #6b7280;
    margin-top: 6px;
    font-size: 0.95rem;
  }

  .product-price {
    color: #f59e0b;
    margin-top: 8px;
    font-weight: 600;
    font-size: 1.1rem;
  }

  .section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a2e;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .rasa-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 28px;
  }

  .rasa-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: #f9fafb;
    border-radius: 60px;
    cursor: pointer;
    transition: all 0.25s ease;
    border: 2px solid transparent;
    font-weight: 500;
  }

  .rasa-checkbox:hover {
    background: #f3f4f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .rasa-checkbox.selected {
    background: #ecfdf5;
    border-color: #10b981;
  }

  .rasa-checkbox input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #2e6b4f;
  }

  .rasa-emoji {
    font-size: 1.3rem;
  }

  .rasa-label {
    font-size: 0.95rem;
  }

  .selected-rasa-list {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 16px;
    margin-bottom: 24px;
  }

  .rasa-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 500;
    color: white;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  .rasa-badge .remove {
    cursor: pointer;
    font-weight: bold;
    margin-left: 6px;
    padding: 2px 6px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.2);
    transition: background 0.2s;
  }

  .rasa-badge .remove:hover {
    background: rgba(0, 0, 0, 0.4);
  }

  .btn-save {
    background: #2e6b4f;
    color: white;
    padding: 12px 32px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-save:hover {
    background: #1a2e1e;
    transform: scale(1.02);
  }

  .btn-back {
    background: #f3f4f6;
    color: #6b7280;
    padding: 12px 32px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
    text-align: center;
  }

  .btn-back:hover {
    background: #e5e7eb;
    color: #374151;
  }

  .form-actions {
    display: flex;
    gap: 16px;
    margin-top: 32px;
    flex-wrap: wrap;
  }

  .message {
    padding: 14px 20px;
    border-radius: 14px;
    margin-bottom: 24px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .message.success {
    background: #d1fae5;
    color: #065f46;
    border-left: 5px solid #10b981;
  }

  .message.error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 5px solid #ef4444;
  }

  .message.warning {
    background: #fef3c7;
    color: #92400e;
    border-left: 5px solid #f59e0b;
  }

  .empty-rasa {
    text-align: center;
    padding: 32px;
    background: #f9fafb;
    border-radius: 16px;
    color: #9ca3af;
    font-style: italic;
  }

  .stats-info {
    background: #e0f2fe;
    padding: 12px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-size: 0.85rem;
    color: #0369a1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
  }

  @media (max-width: 640px) {
    .rasa-checkbox {
      padding: 8px 16px;
    }

    .form-actions {
      flex-direction: column;
    }

    .btn-save,
    .btn-back {
      text-align: center;
    }
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
        <a href="kategori_rasa.php">Kelola Rasa</a>
        <a href="bayar.php">Kelola Pembayaran</a>
        <a href="mitra.php">Kelola Mitra</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="container">
    <div class="card">
      <!-- Informasi Produk -->
      <div class="product-info">
        <div class="product-name"> <?= htmlspecialchars($produk['nama_produk']) ?></div>
        <div class="product-umkm"> <?= htmlspecialchars($produk['nama_umkm']) ?></div>
        <div class="product-price"> Rp <?= number_format($produk['harga'], 0, ',', '.') ?></div>
        <div class="product-umkm"> Kategori: <?= htmlspecialchars($produk['kategori_produk']) ?></div>
        <?php if ($produk['asal_daerah']): ?>
        <div class="product-umkm">📍 Asal: <?= htmlspecialchars($produk['asal_daerah']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Pesan Notifikasi -->
      <?php if (isset($_SESSION['message'])): ?>
      <div class="message <?= $_SESSION['message_type'] ?>">
        <?php if ($_SESSION['message_type'] == 'success'): ?>
        ✅
        <?php elseif ($_SESSION['message_type'] == 'error'): ?>
        ❌
        <?php elseif ($_SESSION['message_type'] == 'warning'): ?>
        ⚠️
        <?php endif; ?>
        <?= $_SESSION['message'] ?>
      </div>
      <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
      <?php endif; ?>

      <!-- Statistik -->
      <div class="stats-info">
        <span>📊 Total rasa yang dipilih: <strong><?= count($rasaTerpilih) ?></strong></span>
        <span>Setiap produk bisa memiliki multiple rasa</span>
      </div>

      <!-- Rasa yang Sudah Dipilih -->
      <div>
        <div class="section-title">
          <span></span> Rasa yang Sudah Dipilih
        </div>
        <div class="selected-rasa-list">
          <?php if (empty($rasaTerpilih)): ?>
          <div class="empty-rasa">
            Belum ada rasa yang dipilih untuk produk ini.<br>
            Silakan pilih rasa di bawah.
          </div>
          <?php else: ?>
          <?php 
                        mysqli_data_seek($resultRasa, 0);
                        while ($rasa = mysqli_fetch_assoc($resultRasa)):
                            if (in_array($rasa['id_rasa'], $rasaTerpilih)):
                                $emoji = $emojiRasa[$rasa['jenis_rasa']] ?? '';
                                $color = $colorRasa[$rasa['jenis_rasa']] ?? '#6b7280';
                        ?>
          <div class="rasa-badge" style="background: <?= $color ?>;">
            <?= $emoji ?> <?= htmlspecialchars($rasa['jenis_rasa']) ?>
            <a href="?id_produk=<?= $id_produk ?>&hapus_rasa=1&id_rasa=<?= $rasa['id_rasa'] ?>" class="remove"
              onclick="return confirm('Hapus rasa <?= htmlspecialchars($rasa['jenis_rasa']) ?> dari produk ini?')"
              style="color: white; text-decoration: none;">✕</a>
          </div>
          <?php 
                            endif;
                        endwhile; 
                        ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Form Tambah Rasa -->
      <form method="POST" action="" style="margin-top: 32px;">
        <input type="hidden" name="action" value="update_rasa">

        <div class="section-title">
          <span>➕</span> Tambah / Ubah Rasa Produk
        </div>

        <div class="rasa-grid">
          <?php 
                    mysqli_data_seek($resultRasa, 0);
                    foreach ($allRasa as $rasa): 
                        $isChecked = in_array($rasa['id_rasa'], $rasaTerpilih);
                        $emoji = $emojiRasa[$rasa['jenis_rasa']] ?? '';
                        $color = $colorRasa[$rasa['jenis_rasa']] ?? '#6b7280';
                    ?>
          <label class="rasa-checkbox <?= $isChecked ? 'selected' : '' ?>"
            style="border-color: <?= $isChecked ? $color : 'transparent' ?>;">
            <input type="checkbox" name="rasa_ids[]" value="<?= $rasa['id_rasa'] ?>" <?= $isChecked ? 'checked' : '' ?>>
            <span class="rasa-emoji"><?= $emoji ?></span>
            <span class="rasa-label"><?= htmlspecialchars($rasa['jenis_rasa']) ?></span>
          </label>
          <?php endforeach; ?>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">
            💾 Simpan Perubahan Rasa
          </button>
          <a href="produk.php" class="btn-back">
            ← Kembali ke Daftar Produk
          </a>
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
  // Update styling saat checkbox berubah
  document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.rasa-checkbox input');

    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        if (this.checked) {
          this.parentElement.classList.add('selected');
          // Tambah efek animasi
          this.parentElement.style.transform = 'scale(1.02)';
          setTimeout(() => {
            this.parentElement.style.transform = '';
          }, 200);
        } else {
          this.parentElement.classList.remove('selected');
        }
      });
    });
  });
  </script>
</body>

</html>