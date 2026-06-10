<?php
session_start();
require_once 'koneksi.php';

// Pesan notifikasi
$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

$id_umkm = isset($_GET['id_umkm']) ? (int)$_GET['id_umkm'] : 0;

// Jika tidak ada id_umkm, tampilkan daftar UMKM untuk dipilih
if ($id_umkm <= 0) {
    $queryUmkm = "SELECT id_umkm, nama_umkm FROM umkm ORDER BY nama_umkm";
    $resultUmkm = mysqli_query($koneksi, $queryUmkm);
    $showSelection = true;
} else {
    $showSelection = false;
    
    // Ambil data UMKM
    $queryUmkmDetail = "SELECT * FROM umkm WHERE id_umkm = $id_umkm";
    $resultUmkmDetail = mysqli_query($koneksi, $queryUmkmDetail);
    $umkm = mysqli_fetch_assoc($resultUmkmDetail);
    
    if (!$umkm) {
        $_SESSION['message'] = 'Data UMKM tidak ditemukan!';
        $_SESSION['message_type'] = 'error';
        header('Location: waktu_operasional.php');
        exit;
    }
    
    // Ambil data waktu operasional UMKM
    $queryWaktu = "SELECT * FROM waktu_operasional WHERE id_umkm = $id_umkm ORDER BY 
                   FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')";
    $resultWaktu = mysqli_query($koneksi, $queryWaktu);
    
    // Buat array untuk memudahkan akses
    $waktuData = [];
    while ($row = mysqli_fetch_assoc($resultWaktu)) {
        $waktuData[$row['hari']] = $row;
    }
    
    // Daftar hari dalam seminggu
    $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
}

// Proses simpan data waktu operasional
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_waktu') {
    $id_umkm = (int)$_POST['id_umkm'];
    
    // Hapus semua data waktu operasional lama
    mysqli_query($koneksi, "DELETE FROM waktu_operasional WHERE id_umkm = $id_umkm");
    
    // Insert data baru
    $success = true;
    foreach ($daftarHari as $hari) {
        $keterangan = $_POST['keterangan_' . $hari];
        $jam_buka = !empty($_POST['jam_buka_' . $hari]) ? $_POST['jam_buka_' . $hari] : null;
        $jam_tutup = !empty($_POST['jam_tutup_' . $hari]) ? $_POST['jam_tutup_' . $hari] : null;
        
        $jam_buka_sql = $jam_buka ? "'$jam_buka'" : "NULL";
        $jam_tutup_sql = $jam_tutup ? "'$jam_tutup'" : "NULL";
        
        $query = "INSERT INTO waktu_operasional (id_umkm, hari, jam_buka, jam_tutup, keterangan) 
                  VALUES ($id_umkm, '$hari', $jam_buka_sql, $jam_tutup_sql, '$keterangan')";
        
        if (!mysqli_query($koneksi, $query)) {
            $success = false;
        }
    }
    
    if ($success) {
        $_SESSION['message'] = 'Waktu operasional UMKM "' . htmlspecialchars($umkm['nama_umkm']) . '" berhasil diupdate!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Gagal mengupdate waktu operasional: ' . mysqli_error($koneksi);
        $_SESSION['message_type'] = 'error';
    }
    
    header("Location: waktu_operasional.php?id_umkm=$id_umkm");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Waktu Operasional - Street Food Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 24px;
  }

  .card {
    background: white;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }

  .header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
  }

  .header-section h1 {
    font-family: 'Playfair Display', serif;
    color: #1a1a2e;
    font-size: 1.8rem;
  }

  .umkm-info {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 20px 24px;
    border-radius: 20px;
    margin-bottom: 28px;
    border: 1px solid #e2e8f0;
  }

  .umkm-name {
    font-size: 1.4rem;
    font-weight: bold;
    color: #1a1a2e;
    font-family: 'Playfair Display', serif;
  }

  .umkm-location {
    color: #6b7280;
    margin-top: 6px;
    font-size: 0.9rem;
  }

  .btn-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
  }

  .btn-primary {
    background: #2e6b4f;
    color: white;
    padding: 10px 24px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    cursor: pointer;
  }

  .btn-primary:hover {
    background: #1a2e1e;
    transform: translateY(-2px);
  }

  .btn-secondary {
    background: #f3f4f6;
    color: #6b7280;
    padding: 10px 24px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    cursor: pointer;
  }

  .btn-secondary:hover {
    background: #e5e7eb;
    color: #374151;
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

  /* Waktu Operasional Table */
  .waktu-table {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th {
    background: #f8fafc;
    padding: 14px 12px;
    text-align: left;
    font-weight: 600;
    color: #1a1a2e;
    border-bottom: 2px solid #e2e8f0;
  }

  td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
  }

  .hari-cell {
    font-weight: 600;
    background: #faf9f6;
  }

  .status-buka {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
  }

  .status-buka-buka {
    background: #d1fae5;
    color: #065f46;
  }

  .status-buka-tutup {
    background: #fee2e2;
    color: #991b1b;
  }

  input[type="time"],
  select {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-family: inherit;
    width: 100%;
  }

  select {
    cursor: pointer;
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
    margin-top: 24px;
  }

  .btn-save:hover {
    background: #1a2e1e;
    transform: scale(1.02);
  }

  /* Selection Grid */
  .umkm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-top: 24px;
  }

  .umkm-card {
    background: #f9fafb;
    border-radius: 16px;
    padding: 20px;
    transition: all 0.2s;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    display: block;
  }

  .umkm-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    border-color: #2e6b4f;
  }

  .umkm-card h3 {
    color: #1a1a2e;
    font-size: 1.1rem;
    margin-bottom: 8px;
  }

  .umkm-card p {
    color: #6b7280;
    font-size: 0.85rem;
  }

  .badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    background: #e0e7ff;
    color: #3730a3;
    margin-top: 8px;
  }

  .time-note {
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 4px;
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
        <a href="waktu_operasional.php">Waktu Operasional</a>
        <a href="produk.php">Kelola Produk</a>
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="container">
    <?php if ($showSelection): ?>
    <!-- Tampilan Pilih UMKM -->
    <div class="card">
      <div class="header-section">
        <h1>🕐 Atur Waktu Operasional UMKM</h1>
        <a href="umkm.php" class="btn-secondary">← Kembali ke UMKM</a>
      </div>

      <?php if ($message): ?>
      <div class="message <?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
      </div>
      <?php endif; ?>

      <p style="margin-bottom: 20px; color: #6b7280;">Pilih UMKM untuk mengatur jam operasional:</p>

      <div class="umkm-grid">
        <?php while ($umkm = mysqli_fetch_assoc($resultUmkm)): ?>
        <a href="waktu_operasional.php?id_umkm=<?= $umkm['id_umkm'] ?>" class="umkm-card">
          <h3>🏪 <?= htmlspecialchars($umkm['nama_umkm']) ?></h3>
          <p>Klik untuk atur waktu operasional</p>
          <span class="badge">Atur Jam Buka/Tutup</span>
        </a>
        <?php endwhile; ?>
      </div>
    </div>
    <?php else: ?>
    <!-- Tampilan Form Waktu Operasional -->
    <div class="card">
      <div class="header-section">
        <h1>🕐 Waktu Operasional</h1>
        <div class="btn-group">
          <a href="waktu_operasional.php" class="btn-secondary">← Ganti UMKM</a>
          <a href="umkm.php" class="btn-secondary">🏪 Daftar UMKM</a>
        </div>
      </div>

      <!-- Informasi UMKM -->
      <div class="umkm-info">
        <div class="umkm-name">🏪 <?= htmlspecialchars($umkm['nama_umkm']) ?></div>
        <div class="umkm-location">📍 <?= htmlspecialchars($umkm['lokasi']) ?></div>
        <?php if ($umkm['nomor_kontak']): ?>
        <div class="umkm-location">📞 <?= htmlspecialchars($umkm['nomor_kontak']) ?></div>
        <?php endif; ?>
      </div>

      <?php if ($message): ?>
      <div class="message <?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="hidden" name="action" value="save_waktu">
        <input type="hidden" name="id_umkm" value="<?= $id_umkm ?>">

        <div class="waktu-table">
          <table>
            <thead>
              <tr>
                <th style="width: 120px;">Hari</th>
                <th style="width: 100px;">Status</th>
                <th style="width: 140px;">Jam Buka</th>
                <th style="width: 140px;">Jam Tutup</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($daftarHari as $hari):
                                    $data = isset($waktuData[$hari]) ? $waktuData[$hari] : null;
                                    $keterangan = $data ? $data['keterangan'] : 'Buka';
                                    $jam_buka = $data && $data['jam_buka'] ? date('H:i', strtotime($data['jam_buka'])) : '';
                                    $jam_tutup = $data && $data['jam_tutup'] ? date('H:i', strtotime($data['jam_tutup'])) : '';
                                    $isTutup = $keterangan == 'Tutup';
                                ?>
              <tr>
                <td class="hari-cell"><strong><?= $hari ?></strong></td>
                <td>
                  <select name="keterangan_<?= $hari ?>" class="status-select" data-hari="<?= $hari ?>"
                    onchange="toggleJam(this, '<?= $hari ?>')">
                    <option value="Buka" <?= $keterangan == 'Buka' ? 'selected' : '' ?>>🟢 Buka</option>
                    <option value="Tutup" <?= $keterangan == 'Tutup' ? 'selected' : '' ?>>🔴 Tutup</option>
                  </select>
                </td>
                <td>
                  <input type="time" name="jam_buka_<?= $hari ?>" id="jam_buka_<?= $hari ?>" value="<?= $jam_buka ?>"
                    <?= $isTutup ? 'disabled' : '' ?>>
                </td>
                <td>
                  <input type="time" name="jam_tutup_<?= $hari ?>" id="jam_tutup_<?= $hari ?>" value="<?= $jam_tutup ?>"
                    <?= $isTutup ? 'disabled' : '' ?>>
                </td>
                <td>
                  <span class="time-note" id="note_<?= $hari ?>">
                    <?php if (!$isTutup && $jam_buka && $jam_tutup): ?>
                    ⏰ Buka dari <?= date('H:i', strtotime($jam_buka)) ?> WIB sampai
                    <?= date('H:i', strtotime($jam_tutup)) ?> WIB
                    <?php elseif (!$isTutup): ?>
                    ⏰ Belum diatur
                    <?php else: ?>
                    🚫 Libur/Tutup
                    <?php endif; ?>
                  </span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div style="text-align: center;">
          <button type="submit" class="btn-save">💾 Simpan Semua Perubahan</button>
        </div>
      </form>
    </div>
    <?php endif; ?>
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
  function toggleJam(select, hari) {
    const jamBuka = document.getElementById('jam_buka_' + hari);
    const jamTutup = document.getElementById('jam_tutup_' + hari);
    const note = document.getElementById('note_' + hari);

    if (select.value === 'Tutup') {
      jamBuka.disabled = true;
      jamTutup.disabled = true;
      jamBuka.value = '';
      jamTutup.value = '';
      note.innerHTML = '🚫 Libur/Tutup';
    } else {
      jamBuka.disabled = false;
      jamTutup.disabled = false;
      note.innerHTML = '⏰ Belum diatur';

      // Jika sudah ada nilai, update note
      if (jamBuka.value && jamTutup.value) {
        note.innerHTML = '⏰ Buka dari ' + jamBuka.value + ' WIB sampai ' + jamTutup.value + ' WIB';
      }
    }
  }

  function updateNote(hari) {
    const jamBuka = document.getElementById('jam_buka_' + hari);
    const jamTutup = document.getElementById('jam_tutup_' + hari);
    const note = document.getElementById('note_' + hari);
    const statusSelect = document.querySelector('.status-select[data-hari="' + hari + '"]');

    if (statusSelect && statusSelect.value === 'Buka' && jamBuka.value && jamTutup.value) {
      note.innerHTML = '⏰ Buka dari ' + jamBuka.value + ' WIB sampai ' + jamTutup.value + ' WIB';
    } else if (statusSelect && statusSelect.value === 'Buka') {
      note.innerHTML = '⏰ Belum diatur';
    }
  }

  // Event listener untuk update note saat jam berubah
  document.addEventListener('DOMContentLoaded', function() {
    const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    hariList.forEach(function(hari) {
      const jamBuka = document.getElementById('jam_buka_' + hari);
      const jamTutup = document.getElementById('jam_tutup_' + hari);
      if (jamBuka) {
        jamBuka.addEventListener('change', function() {
          updateNote(hari);
        });
      }
      if (jamTutup) {
        jamTutup.addEventListener('change', function() {
          updateNote(hari);
        });
      }
    });
  });
  </script>
</body>

</html>