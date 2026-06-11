<?php
session_start();
require_once 'koneksi.php';

$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message'], $_SESSION['message_type']);
}

// Proses update relasi via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_mitra') {
    $id_umkm  = (int)$_POST['id_umkm'];
    $mitra_ids = isset($_POST['mitra_ids']) ? $_POST['mitra_ids'] : [];
    $links     = isset($_POST['links'])     ? $_POST['links']     : [];

    mysqli_query($koneksi, "DELETE FROM umkm_mitra WHERE id_umkm = $id_umkm");

    if (!empty($mitra_ids)) {
        $values = [];
        foreach ($mitra_ids as $id_mitra) {
            $id_mitra = (int)$id_mitra;
            $link     = isset($links[$id_mitra]) ? mysqli_real_escape_string($koneksi, trim($links[$id_mitra])) : '';
            $link_sql = $link ? "'$link'" : "NULL";
            $values[] = "($id_umkm, $id_mitra, $link_sql)";
        }
        mysqli_query($koneksi, "INSERT INTO umkm_mitra (id_umkm, id_mitra, link_mitra) VALUES " . implode(', ', $values));
    }

    $_SESSION['message']      = 'Relasi mitra berhasil diupdate!';
    $_SESSION['message_type'] = 'success';
    header("Location: relasi_umkm_mitra.php?id_umkm=$id_umkm");
    exit;
}

// Hapus satu relasi via GET
if (isset($_GET['hapus_mitra'], $_GET['id_mitra'], $_GET['id_umkm'])) {
    $id_umkm  = (int)$_GET['id_umkm'];
    $id_mitra = (int)$_GET['id_mitra'];
    mysqli_query($koneksi, "DELETE FROM umkm_mitra WHERE id_umkm=$id_umkm AND id_mitra=$id_mitra");
    $_SESSION['message']      = 'Mitra berhasil dihapus dari UMKM.';
    $_SESSION['message_type'] = 'success';
    header("Location: relasi_umkm_mitra.php?id_umkm=$id_umkm");
    exit;
}

// Ambil daftar semua UMKM
$result_umkm = mysqli_query($koneksi, "SELECT id_umkm, nama_umkm, lokasi FROM umkm ORDER BY nama_umkm ASC");
$all_umkm    = [];
while ($u = mysqli_fetch_assoc($result_umkm)) $all_umkm[] = $u;

// Ambil daftar semua mitra
$result_mitra = mysqli_query($koneksi, "SELECT * FROM mitra_platform ORDER BY id_mitra ASC");
$all_mitra    = [];
while ($m = mysqli_fetch_assoc($result_mitra)) $all_mitra[] = $m;

// UMKM yang dipilih
$selected_id  = isset($_GET['id_umkm']) ? (int)$_GET['id_umkm'] : 0;
$selected_umkm = null;
$mitraTerpilih = [];
$linkMitra     = [];

if ($selected_id > 0) {
    $r = mysqli_query($koneksi, "SELECT * FROM umkm WHERE id_umkm=$selected_id");
    $selected_umkm = mysqli_fetch_assoc($r);

    $r2 = mysqli_query($koneksi, "SELECT id_mitra, link_mitra FROM umkm_mitra WHERE id_umkm=$selected_id");
    while ($row = mysqli_fetch_assoc($r2)) {
        $mitraTerpilih[]             = $row['id_mitra'];
        $linkMitra[$row['id_mitra']] = $row['link_mitra'];
    }
}

$iconMitra  = ['GoFood'=>'','GrabFood'=>'','ShopeeFood'=>'','Gojek'=>'','Grab'=>'','Shopee'=>''];
$colorMitra = ['GoFood'=>'#00b14f','GrabFood'=>'#00b14f','ShopeeFood'=>'#ee4d2d','Gojek'=>'#00b14f','Grab'=>'#00b14f','Shopee'=>'#ee4d2d'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Relasi UMKM-Mitra - Street Food Ciwaruga</title>
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

  .page-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    color: #1a1a2e;
    margin-bottom: 24px;
  }

  .card {
    background: #fff;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
    margin-bottom: 24px;
  }

  /* Pilih UMKM */
  .umkm-select-label {
    font-size: .85rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 8px;
    display: block;
  }

  .umkm-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    font-size: 1rem;
    font-family: inherit;
    background: #f9fafb;
    cursor: pointer;
  }

  .umkm-select:focus {
    outline: none;
    border-color: #2e6b4f;
    background: #fff;
  }

  /* Info UMKM terpilih */
  .umkm-info {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 20px 24px;
    border-radius: 16px;
    margin-top: 16px;
    border: 1px solid #e2e8f0;
  }

  .umkm-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a1a2e;
    font-family: 'Playfair Display', serif;
  }

  .umkm-location {
    color: #6b7280;
    font-size: .88rem;
    margin-top: 4px;
  }

  /* Stats */
  .stats-info {
    background: #e0f2fe;
    padding: 10px 18px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-size: .85rem;
    color: #0369a1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
  }

  /* Badge mitra terpilih */
  .selected-mitra-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 24px;
    min-height: 40px;
  }

  .mitra-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 40px;
    font-size: .85rem;
    font-weight: 500;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
  }

  .mitra-badge .remove {
    cursor: pointer;
    font-weight: 700;
    margin-left: 4px;
    padding: 2px 6px;
    border-radius: 50%;
    background: rgba(0, 0, 0, .2);
    text-decoration: none;
    color: #fff;
    transition: background .2s;
  }

  .mitra-badge .remove:hover {
    background: rgba(0, 0, 0, .4);
  }

  /* Grid mitra */
  .section-title {
    font-size: 1.1rem;
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
    gap: 14px;
    margin-bottom: 24px;
  }

  .mitra-item {
    background: #f9fafb;
    border-radius: 14px;
    padding: 16px 20px;
    border: 2px solid transparent;
    transition: all .2s;
  }

  .mitra-item.selected {
    background: #ecfdf5;
    border-color: #10b981;
  }

  .mitra-header {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .mitra-header input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #2e6b4f;
  }

  .mitra-icon {
    font-size: 1.6rem;
  }

  .mitra-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a2e;
  }

  .link-field {
    margin: 12px 0 0 44px;
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
  }

  .link-field label {
    font-size: .82rem;
    color: #6b7280;
    font-weight: 500;
  }

  .link-field input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: .88rem;
    font-family: inherit;
    min-width: 240px;
  }

  .link-field input:focus {
    outline: none;
    border-color: #2e6b4f;
  }

  .link-hint {
    font-size: .7rem;
    color: #9ca3af;
    margin: 4px 0 0 44px;
  }

  /* Actions */
  .form-actions {
    display: flex;
    gap: 14px;
    margin-top: 28px;
    flex-wrap: wrap;
  }

  .btn-save {
    background: #2e6b4f;
    color: #fff;
    padding: 12px 32px;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    font-size: .95rem;
    cursor: pointer;
    transition: all .2s;
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
    font-size: .95rem;
    text-decoration: none;
    display: inline-block;
    transition: all .2s;
  }

  .btn-back:hover {
    background: #e5e7eb;
    color: #374151;
  }

  /* Message */
  .message {
    padding: 14px 20px;
    border-radius: 14px;
    margin-bottom: 20px;
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
    padding: 24px;
    background: #f9fafb;
    border-radius: 12px;
    color: #9ca3af;
    font-style: italic;
  }

  .placeholder-text {
    color: #9ca3af;
    font-size: .95rem;
    text-align: center;
    padding: 32px;
  }

  @media(max-width:640px) {
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
  }
  </style>
</head>

<body>

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
        <a href="index.php">Beranda</a>
        <a href="umkm.php">Kelola UMKM</a>
        <a href="produk.php">Kelola Produk</a>
        <a href="kategori_rasa.php">Kelola Rasa</a>
        <a href="bayar.php">Kelola Pembayaran</a>
        <a href="mitra.php" class="active">Kelola Mitra</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="container">
    <h1 class="page-title">🔗 Kelola Relasi UMKM–Mitra</h1>

    <?php if ($message): ?>
    <div class="message <?= $messageType ?>">
      <?= $messageType==='success' ? '✅' : ($messageType==='error' ? '❌' : '⚠️') ?>
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <!-- Pilih UMKM -->
    <div class="card">
      <label class="umkm-select-label">Pilih UMKM yang ingin dikelola relasinya:</label>
      <select class="umkm-select" onchange="if(this.value) window.location='relasi_umkm_mitra.php?id_umkm='+this.value">
        <option value="">— Pilih UMKM —</option>
        <?php foreach ($all_umkm as $u): ?>
        <option value="<?= $u['id_umkm'] ?>" <?= $selected_id==$u['id_umkm'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($u['nama_umkm']) ?> <?= $u['lokasi'] ? '— '.$u['lokasi'] : '' ?>
        </option>
        <?php endforeach; ?>
      </select>

      <?php if ($selected_umkm): ?>
      <div class="umkm-info">
        <div class="umkm-name">🏪 <?= htmlspecialchars($selected_umkm['nama_umkm']) ?></div>
        <div class="umkm-location">📍 <?= htmlspecialchars($selected_umkm['lokasi'] ?? '-') ?></div>
        <?php if (!empty($selected_umkm['nomor_kontak'])): ?>
        <div class="umkm-location">📞 <?= htmlspecialchars($selected_umkm['nomor_kontak']) ?></div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <?php if ($selected_umkm): ?>
    <!-- Card Mitra Terpilih -->
    <div class="card">
      <div class="section-title">🤝 Mitra Platform yang Sudah Dipilih</div>

      <div class="stats-info">
        <span>Total mitra aktif: <strong><?= count($mitraTerpilih) ?></strong></span>
        <span>UMKM bisa terdaftar di beberapa platform sekaligus</span>
      </div>

      <div class="selected-mitra-list">
        <?php if (empty($mitraTerpilih)): ?>
        <div class="empty-mitra">Belum ada mitra platform yang dipilih. Silakan pilih di bawah.</div>
        <?php else: ?>
        <?php foreach ($all_mitra as $m):
            if (!in_array($m['id_mitra'], $mitraTerpilih)) continue;
            $icon  = $iconMitra[$m['nama_mitra']]  ?? '📱';
            $color = $colorMitra[$m['nama_mitra']] ?? '#6b7280';
            $link  = $linkMitra[$m['id_mitra']]    ?? '';
        ?>
        <div class="mitra-badge" style="background:<?= $color ?>;">
          <?= $icon ?> <?= htmlspecialchars($m['nama_mitra']) ?>
          <?php if ($link): ?>
          <a href="<?= htmlspecialchars($link) ?>" target="_blank"
            style="color:#fff;text-decoration:none;margin-left:4px;">🔗</a>
          <?php endif; ?>
          <a href="?id_umkm=<?= $selected_id ?>&hapus_mitra=1&id_mitra=<?= $m['id_mitra'] ?>" class="remove"
            onclick="return confirm('Hapus <?= htmlspecialchars($m['nama_mitra']) ?> dari UMKM ini?')">✕</a>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Form Edit Mitra -->
    <div class="card">
      <form method="POST" action="">
        <input type="hidden" name="action" value="update_mitra">
        <input type="hidden" name="id_umkm" value="<?= $selected_id ?>">

        <div class="section-title">➕ Tambah / Ubah Mitra Platform</div>

        <div class="mitra-grid">
          <?php foreach ($all_mitra as $m):
            $checked      = in_array($m['id_mitra'], $mitraTerpilih);
            $icon         = $iconMitra[$m['nama_mitra']]  ?? '📱';
            $color        = $colorMitra[$m['nama_mitra']] ?? '#6b7280';
            $currentLink  = $linkMitra[$m['id_mitra']]    ?? '';
        ?>
          <div class="mitra-item <?= $checked ? 'selected' : '' ?>" id="item_<?= $m['id_mitra'] ?>"
            style="border-color:<?= $checked ? $color : 'transparent' ?>;">
            <div class="mitra-header">
              <input type="checkbox" name="mitra_ids[]" value="<?= $m['id_mitra'] ?>" id="cb_<?= $m['id_mitra'] ?>"
                <?= $checked ? 'checked' : '' ?> data-color="<?= $color ?>"
                onchange="toggleMitra(this, <?= $m['id_mitra'] ?>)">
              <span class="mitra-icon"><?= $icon ?></span>
              <label for="cb_<?= $m['id_mitra'] ?>" class="mitra-name" style="cursor:pointer;">
                <?= htmlspecialchars($m['nama_mitra']) ?>
              </label>
            </div>
            <div class="link-field" id="link_field_<?= $m['id_mitra'] ?>"
              style="display:<?= $checked ? 'flex' : 'none' ?>;">
              <label>🔗 Link:</label>
              <input type="text" name="links[<?= $m['id_mitra'] ?>]" placeholder="https://gofood.link/a/xxxx"
                value="<?= htmlspecialchars($currentLink) ?>">
            </div>
            <div class="link-hint" id="hint_<?= $m['id_mitra'] ?>" style="display:<?= $checked ? 'block' : 'none' ?>;">
              💡 Contoh: https://gofood.link/a/xxxx
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
          <a href="mitra.php" class="btn-back">← Kembali ke Kelola Mitra</a>
        </div>
      </form>
    </div>

    <?php else: ?>
    <div class="card">
      <div class="placeholder-text">👆 Pilih UMKM di atas untuk mengelola relasi mitra platformnya.</div>
    </div>
    <?php endif; ?>
  </div>

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
  function toggleMitra(cb, id) {
    const item = document.getElementById('item_' + id);
    const field = document.getElementById('link_field_' + id);
    const hint = document.getElementById('hint_' + id);
    if (cb.checked) {
      item.classList.add('selected');
      item.style.borderColor = cb.dataset.color || '#10b981';
      field.style.display = 'flex';
      hint.style.display = 'block';
    } else {
      item.classList.remove('selected');
      item.style.borderColor = 'transparent';
      field.style.display = 'none';
      hint.style.display = 'none';
    }
  }
  </script>
</body>

</html>