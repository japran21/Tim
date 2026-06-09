<?php
session_start();
require_once 'koneksi.php';

$id_umkm = isset($_GET['id_umkm']) ? (int)$_GET['id_umkm'] : 0;

if ($id_umkm <= 0) {
    $_SESSION['message'] = 'ID UMKM tidak valid!';
    $_SESSION['message_type'] = 'error';
    header('Location: umkm.php');
    exit;
}

// Ambil data UMKM
$queryUmkm = "SELECT * FROM umkm WHERE id_umkm = $id_umkm";
$resultUmkm = mysqli_query($koneksi, $queryUmkm);
$umkm = mysqli_fetch_assoc($resultUmkm);

if (!$umkm) {
    $_SESSION['message'] = 'Data UMKM tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: umkm.php');
    exit;
}

// Ambil semua mitra platform
$queryMitra = "SELECT * FROM mitra_platform ORDER BY id_mitra";
$resultMitra = mysqli_query($koneksi, $queryMitra);
$allMitra = [];
while ($mitra = mysqli_fetch_assoc($resultMitra)) {
    $allMitra[] = $mitra;
}

// Ambil mitra yang sudah dimiliki UMKM beserta linknya
$queryMitraUmkm = "SELECT id_mitra, link_mitra FROM umkm_mitra WHERE id_umkm = $id_umkm";
$resultMitraUmkm = mysqli_query($koneksi, $queryMitraUmkm);
$mitraTerpilih = [];
$linkMitra = [];
while ($row = mysqli_fetch_assoc($resultMitraUmkm)) {
    $mitraTerpilih[] = $row['id_mitra'];
    $linkMitra[$row['id_mitra']] = $row['link_mitra'];
}

// Proses update relasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_mitra') {
    $mitra_ids = isset($_POST['mitra_ids']) ? $_POST['mitra_ids'] : [];
    $links = isset($_POST['links']) ? $_POST['links'] : [];
    
    // Hapus semua relasi lama
    mysqli_query($koneksi, "DELETE FROM umkm_mitra WHERE id_umkm = $id_umkm");
    
    // Insert relasi baru
    if (!empty($mitra_ids)) {
        $values = [];
        foreach ($mitra_ids as $id_mitra) {
            $id_mitra = (int)$id_mitra;
            $link = isset($links[$id_mitra]) ? mysqli_real_escape_string($koneksi, trim($links[$id_mitra])) : '';
            $link_sql = $link ? "'$link'" : "NULL";
            $values[] = "($id_umkm, $id_mitra, $link_sql)";
        }
        $sql = "INSERT INTO umkm_mitra (id_umkm, id_mitra, link_mitra) VALUES " . implode(', ', $values);
        if (mysqli_query($koneksi, $sql)) {
            $_SESSION['message'] = 'Relasi UMKM dengan mitra platform berhasil diupdate!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Gagal mengupdate relasi: ' . mysqli_error($koneksi);
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'Semua mitra telah dihapus dari UMKM ini.';
        $_SESSION['message_type'] = 'warning';
    }
    
    // Refresh data
    $mitraTerpilih = $mitra_ids;
    header("Location: relasi_umkm_mitra.php?id_umkm=$id_umkm");
    exit;
}

// Hapus relasi tertentu
if (isset($_GET['hapus_mitra']) && isset($_GET['id_mitra'])) {
    $id_mitra_hapus = (int)$_GET['id_mitra'];
    $queryDelete = "DELETE FROM umkm_mitra WHERE id_umkm = $id_umkm AND id_mitra = $id_mitra_hapus";
    if (mysqli_query($koneksi, $queryDelete)) {
        $_SESSION['message'] = 'Mitra platform berhasil dihapus dari UMKM!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Gagal menghapus mitra: ' . mysqli_error($koneksi);
        $_SESSION['message_type'] = 'error';
    }
    header("Location: relasi_umkm_mitra.php?id_umkm=$id_umkm");
    exit;
}

// Icon dan warna untuk setiap mitra
$iconMitra = [
    'GoFood' => '🟢',
    'GrabFood' => '🟠',
    'ShopeeFood' => '🟡',
    'Gojek' => '🟢',
    'Grab' => '🟠',
    'Shopee' => '🟡',
];

$colorMitra = [
    'GoFood' => '#00b14f',
    'GrabFood' => '#00b14f',
    'ShopeeFood' => '#ee4d2d',
    'Gojek' => '#00b14f',
    'Grab' => '#00b14f',
    'Shopee' => '#ee4d2d',
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Mitra Platform UMKM - Street Food Ciwaruga</title>
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

  .umkm-info {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 24px;
    border-radius: 20px;
    margin-bottom: 28px;
    border: 1px solid #e2e8f0;
  }

  .umkm-name {
    font-size: 1.6rem;
    font-weight: bold;
    color: #1a1a2e;
    font-family: 'Playfair Display', serif;
  }

  .umkm-location {
    color: #6b7280;
    margin-top: 6px;
    font-size: 0.9rem;
  }

  .umkm-contact {
    color: #f59e0b;
    margin-top: 8px;
    font-weight: 500;
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

  .mitra-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 28px;
  }

  .mitra-item {
    background: #f9fafb;
    border-radius: 16px;
    padding: 16px 20px;
    transition: all 0.25s ease;
    border: 2px solid transparent;
  }

  .mitra-item.selected {
    background: #ecfdf5;
    border-color: #10b981;
  }

  .mitra-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
  }

  .mitra-header input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #2e6b4f;
  }

  .mitra-icon {
    font-size: 1.8rem;
  }

  .mitra-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a2e;
  }

  .link-field {
    margin-left: 44px;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
  }

  .link-field label {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
  }

  .link-field input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.9rem;
    font-family: inherit;
    min-width: 250px;
  }

  .link-field input:focus {
    outline: none;
    border-color: #2e6b4f;
  }

  .selected-mitra-list {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 16px;
    margin-bottom: 24px;
  }

  .mitra-badge {
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

  .mitra-badge .remove {
    cursor: pointer;
    font-weight: bold;
    margin-left: 6px;
    padding: 2px 6px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.2);
    transition: background 0.2s;
    text-decoration: none;
    color: white;
  }

  .mitra-badge .remove:hover {
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

  .empty-mitra {
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

  .link-example {
    font-size: 0.7rem;
    color: #6b7280;
    margin-top: 4px;
    margin-left: 44px;
  }

  @media (max-width: 640px) {
    .link-field {
      margin-left: 0;
      flex-direction: column;
      align-items: flex-start;
    }

    .link-field input {
      width: 100%;
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
        <a href="umkm.php">Kelola UMKM</a>
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
      <!-- Informasi UMKM -->
      <div class="umkm-info">
        <div class="umkm-name">🏪 <?= htmlspecialchars($umkm['nama_umkm']) ?></div>
        <div class="umkm-location">📍 <?= htmlspecialchars($umkm['lokasi']) ?></div>
        <?php if ($umkm['nomor_kontak']): ?>
        <div class="umkm-contact">📞 <?= htmlspecialchars($umkm['nomor_kontak']) ?></div>
        <?php endif; ?>
        <div class="umkm-location" style="margin-top: 8px;">
          <?php
                    $status_class = '';
                    if ($umkm['status_halal'] == 'Halal Bersertifikat') {
                        $status_class = '✅ Halal Bersertifikat';
                    } elseif ($umkm['status_halal'] == 'Halal Belum Bersertifikat') {
                        $status_class = '⏳ Halal Belum Bersertifikat';
                    } else {
                        $status_class = '❌ Non-Halal';
                    }
                    ?>
          <?= $status_class ?>
        </div>
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
        <span>📊 Total mitra terpilih: <strong><?= count($mitraTerpilih) ?></strong></span>
        <span>🤝 UMKM bisa terdaftar di multiple mitra platform</span>
      </div>

      <!-- Mitra yang Sudah Dipilih -->
      <div>
        <div class="section-title">
          <span>🤝</span> Mitra Platform yang Sudah Dipilih
        </div>
        <div class="selected-mitra-list">
          <?php if (empty($mitraTerpilih)): ?>
          <div class="empty-mitra">
            Belum ada mitra platform yang dipilih untuk UMKM ini.<br>
            Silakan pilih mitra di bawah.
          </div>
          <?php else: ?>
          <?php 
                        mysqli_data_seek($resultMitra, 0);
                        while ($mitra = mysqli_fetch_assoc($resultMitra)):
                            if (in_array($mitra['id_mitra'], $mitraTerpilih)):
                                $icon = $iconMitra[$mitra['nama_mitra']] ?? '📱';
                                $color = $colorMitra[$mitra['nama_mitra']] ?? '#6b7280';
                                $link = $linkMitra[$mitra['id_mitra']] ?? '';
                        ?>
          <div class="mitra-badge" style="background: <?= $color ?>;">
            <?= $icon ?> <?= htmlspecialchars($mitra['nama_mitra']) ?>
            <?php if ($link): ?>
            <a href="<?= htmlspecialchars($link) ?>" target="_blank"
              style="color: white; text-decoration: none; margin-left: 4px;">🔗</a>
            <?php endif; ?>
            <a href="?id_umkm=<?= $id_umkm ?>&hapus_mitra=1&id_mitra=<?= $mitra['id_mitra'] ?>" class="remove"
              onclick="return confirm('Hapus mitra <?= htmlspecialchars($mitra['nama_mitra']) ?> dari UMKM ini?')">✕</a>
          </div>
          <?php 
                            endif;
                        endwhile; 
                        ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Form Tambah/Edit Mitra -->
      <form method="POST" action="" style="margin-top: 32px;">
        <input type="hidden" name="action" value="update_mitra">

        <div class="section-title">
          <span>➕</span> Tambah / Ubah Mitra Platform
        </div>

        <div class="mitra-grid">
          <?php 
                    mysqli_data_seek($resultMitra, 0);
                    foreach ($allMitra as $mitra): 
                        $isChecked = in_array($mitra['id_mitra'], $mitraTerpilih);
                        $icon = $iconMitra[$mitra['nama_mitra']] ?? '📱';
                        $color = $colorMitra[$mitra['nama_mitra']] ?? '#6b7280';
                        $currentLink = $linkMitra[$mitra['id_mitra']] ?? '';
                    ?>
          <div class="mitra-item <?= $isChecked ? 'selected' : '' ?>"
            style="border-color: <?= $isChecked ? $color : 'transparent' ?>;">
            <div class="mitra-header">
              <input type="checkbox" name="mitra_ids[]" value="<?= $mitra['id_mitra'] ?>"
                id="mitra_<?= $mitra['id_mitra'] ?>" <?= $isChecked ? 'checked' : '' ?>
                onchange="toggleMitra(this, <?= $mitra['id_mitra'] ?>)">
              <span class="mitra-icon"><?= $icon ?></span>
              <span class="mitra-name"><?= htmlspecialchars($mitra['nama_mitra']) ?></span>
            </div>
            <div class="link-field" id="link_field_<?= $mitra['id_mitra'] ?>"
              style="display: <?= $isChecked ? 'flex' : 'none' ?>;">
              <label>🔗 Link Mitra:</label>
              <input type="text" name="links[<?= $mitra['id_mitra'] ?>]" placeholder="https://gofood.link/a/xxxx"
                value="<?= htmlspecialchars($currentLink) ?>">
            </div>
            <div class="link-example" id="example_<?= $mitra['id_mitra'] ?>"
              style="display: <?= $isChecked ? 'block' : 'none' ?>;">
              💡 Contoh: https://gofood.link/a/xxxx atau https://r.grab.com/g/xxxx
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">
            💾 Simpan Perubahan Mitra
          </button>
          <a href="umkm.php" class="btn-back">
            ← Kembali ke Daftar UMKM
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
  function toggleMitra(checkbox, mitraId) {
    const linkField = document.getElementById('link_field_' + mitraId);
    const exampleField = document.getElementById('example_' + mitraId);
    const parentItem = checkbox.closest('.mitra-item');

    if (checkbox.checked) {
      linkField.style.display = 'flex';
      exampleField.style.display = 'block';
      parentItem.classList.add('selected');
      parentItem.style.borderColor = parentItem.classList.contains('selected') ? getComputedStyle(parentItem)
        .getPropertyValue('--mitra-color') || '#10b981' : 'transparent';
    } else {
      linkField.style.display = 'none';
      exampleField.style.display = 'none';
      parentItem.classList.remove('selected');
      parentItem.style.borderColor = 'transparent';
    }
  }

  // Inisialisasi styling untuk checkbox yang sudah tercentang
  document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.mitra-item input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
      if (checkbox.checked) {
        const mitraId = checkbox.value;
        const linkField = document.getElementById('link_field_' + mitraId);
        const exampleField = document.getElementById('example_' + mitraId);
        if (linkField) linkField.style.display = 'flex';
        if (exampleField) exampleField.style.display = 'block';
      }
    });
  });
  </script>
</body>

</html>