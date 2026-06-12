<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID UMKM tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: umkm.php');
    exit;
}

$id_umkm = (int)$_GET['id'];

// Ambil data UMKM
$query = "SELECT * FROM umkm WHERE id_umkm = $id_umkm";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['message'] = 'Data UMKM tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: umkm.php');
    exit;
}

$umkm = mysqli_fetch_assoc($result);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_umkm = trim($_POST['nama_umkm']);
    $lokasi = trim($_POST['lokasi']);
    $nomor_kontak = !empty($_POST['nomor_kontak']) ? trim($_POST['nomor_kontak']) : NULL;
    $status_halal = $_POST['status_halal'];
    
    // Field halal hanya jika status Halal Bersertifikat
    if ($status_halal === 'Halal Bersertifikat') {
        $no_sertifikat = !empty($_POST['no_sertifikat']) ? trim($_POST['no_sertifikat']) : NULL;
        $lembaga_penerbit = !empty($_POST['lembaga_penerbit']) ? trim($_POST['lembaga_penerbit']) : NULL;
        $tanggal_terbit = !empty($_POST['tanggal_terbit']) ? $_POST['tanggal_terbit'] : NULL;
    } else {
        $no_sertifikat = NULL;
        $lembaga_penerbit = NULL;
        $tanggal_terbit = NULL;
    }
    
    // Upload foto baru jika ada
    $foto = $umkm['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "FOTO_UMKM/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Hapus foto lama jika ada
        if ($umkm['foto'] && file_exists($umkm['foto'])) {
            unlink($umkm['foto']);
        }
        
        $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nama_umkm) . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto = $target_file;
        }
    }
    
    $errors = [];
    if (empty($nama_umkm)) $errors[] = 'Nama UMKM tidak boleh kosong';
    if (empty($lokasi)) $errors[] = 'Lokasi tidak boleh kosong';
    
    if (empty($errors)) {
        // Simpan data lama untuk keperluan rollback file foto
        $foto_lama = $umkm['foto'];
        $foto_baru_diupload = ($foto !== $foto_lama); // true jika ada foto baru

        // ── TRANSACTION: BEGIN ──────────────────────────────────────────────
        try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $koneksi->begin_transaction();

            $stmt = $koneksi->prepare("UPDATE umkm SET
                nama_umkm       = :nama_umkm,
                lokasi          = :lokasi,
                foto            = :foto,
                nomor_kontak    = :nomor_kontak,
                status_halal    = :status_halal,
                no_sertifikat   = :no_sertifikat,
                lembaga_penerbit= :lembaga_penerbit,
                tanggal_terbit  = :tanggal_terbit
                WHERE id_umkm   = :id_umkm");

            $stmt->bind_param([
                ':nama_umkm'        => $nama_umkm,
                ':lokasi'           => $lokasi,
                ':foto'             => $foto ?: null,
                ':nomor_kontak'     => $nomor_kontak,
                ':status_halal'     => $status_halal,
                ':no_sertifikat'    => $no_sertifikat,
                ':lembaga_penerbit' => $lembaga_penerbit,
                ':tanggal_terbit'   => $tanggal_terbit,
                ':id_umkm'          => $id_umkm,
            ]);
            $stmt->execute();

            // Pastikan tepat 1 baris yang berubah
            if ($stmt->rowCount() < 0) {
                throw new Exception('Tidak ada baris yang diperbarui di database.');
            }

            // ── COMMIT ──────────────────────────────────────────────────────
$koneksi->commit();
            $_SESSION['message'] = 'Data UMKM "' . htmlspecialchars($nama_umkm) . '" berhasil diperbarui!';
            $_SESSION['message_type'] = 'success';
            header('Location: umkm.php');
            exit;

        } catch (Exception $e) {
            // ── ROLLBACK ────────────────────────────────────────────────────
           $koneksi->rollback();

            // Jika foto baru sudah terlanjur diupload, hapus agar tidak jadi sampah
            if ($foto_baru_diupload && $foto && file_exists($foto)) {
                unlink($foto);
            }

            // Kembalikan referensi foto ke foto lama
            $foto = $foto_lama;

            error_log('edit_umkm rollback: ' . $e->getMessage());
            $error = 'Gagal memperbarui data. Perubahan telah dibatalkan (rollback). '
                   . 'Silakan coba lagi atau hubungi administrator.';
        }
        // ── END TRANSACTION ─────────────────────────────────────────────────

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
  <title>Edit UMKM - Street Food Ciwaruga</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <style>
  .form-container {
    max-width: 800px;
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
  input[type="file"],
  input[type="date"],
  select,
  textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: border-color 0.2s;
    font-family: inherit;
  }

  textarea {
    resize: vertical;
    min-height: 80px;
  }

  input:focus,
  select:focus,
  textarea:focus {
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
    font-size: 0.85rem;
  }

  .foto-preview-container {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 8px;
  }

  .foto-preview {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
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

  <div class="form-container">
    <div class="form-card">
      <h1 class="form-title">Edit Data UMKM</h1>
      <p class="form-subtitle">Ubah informasi data UMKM makanan/minuman di Ciwaruga</p>

      <?php if ($error): ?>
      <div class="error-message">
        ⚠️ <?= $error ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
          <label for="nama_umkm">Nama UMKM *</label>
          <input type="text" id="nama_umkm" name="nama_umkm" value="<?= htmlspecialchars($umkm['nama_umkm']) ?>"
            required>
        </div>

        <div class="form-group">
          <label for="lokasi">Lokasi / Alamat *</label>
          <textarea id="lokasi" name="lokasi" required><?= htmlspecialchars($umkm['lokasi']) ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="nomor_kontak">Nomor Kontak</label>
            <input type="text" id="nomor_kontak" name="nomor_kontak"
              value="<?= htmlspecialchars($umkm['nomor_kontak'] ?? '') ?>" placeholder="6281234567890">
          </div>

          <div class="form-group">
            <label for="foto">Foto UMKM</label>
            <input type="file" id="foto" name="foto" accept="image/*">
            <?php if ($umkm['foto'] && file_exists($umkm['foto'])): ?>
            <div class="foto-preview-container">
              <img src="<?= htmlspecialchars($umkm['foto']) ?>" class="foto-preview" alt="Foto">
              <span style="font-size: 0.8rem; color: #6b7280;">Foto saat ini</span>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group">
          <label for="status_halal">Status Halal *</label>
          <select id="status_halal" name="status_halal" required>
            <option value="Halal Bersertifikat"
              <?= $umkm['status_halal'] === 'Halal Bersertifikat' ? 'selected' : '' ?>>Halal Bersertifikat</option>
            <option value="Halal Belum Bersertifikat"
              <?= $umkm['status_halal'] === 'Halal Belum Bersertifikat' ? 'selected' : '' ?>>Halal Belum Bersertifikat
            </option>
            <option value="Non-Halal" <?= $umkm['status_halal'] === 'Non-Halal' ? 'selected' : '' ?>>Non-Halal</option>
          </select>
        </div>

        <div class="form-row" id="sertifikat_fields">
          <div class="form-group">
            <label for="no_sertifikat">No Sertifikat Halal</label>
            <input type="text" id="no_sertifikat" name="no_sertifikat"
              value="<?= htmlspecialchars($umkm['no_sertifikat'] ?? '') ?>" placeholder="ID32110016944470224">
          </div>

          <div class="form-group">
            <label for="lembaga_penerbit">Lembaga Penerbit</label>
            <input type="text" id="lembaga_penerbit" name="lembaga_penerbit"
              value="<?= htmlspecialchars($umkm['lembaga_penerbit'] ?? '') ?>" placeholder="BPJPH / MUI">
          </div>

          <div class="form-group">
            <label for="tanggal_terbit">Tanggal Terbit</label>
            <input type="date" id="tanggal_terbit" name="tanggal_terbit" value="<?= $umkm['tanggal_terbit'] ?>">
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Simpan Perubahan</button>
          <a href="umkm.php" class="btn-cancel">Batal</a>
        </div>
      </form>

      <div class="info-card">
        💡 <strong>Tips:</strong>
        <ul style="margin-top: 8px; margin-left: 20px;">
          <li>Nomor kontak gunakan format internasional (contoh: 6281234567890)</li>
          <li>Foto akan diupload ke folder FOTO_UMKM/</li>
          <li>Field sertifikat hanya diperlukan jika status "Halal Bersertifikat"</li>
        </ul>
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
  // Tampilkan/sembunyikan field sertifikat berdasarkan status halal
  const statusSelect = document.getElementById('status_halal');
  const sertifikatFields = document.getElementById('sertifikat_fields');

  function toggleSertifikatFields() {
    if (statusSelect.value === 'Halal Bersertifikat') {
      sertifikatFields.style.display = 'grid';
    } else {
      sertifikatFields.style.display = 'none';
    }
  }

  statusSelect.addEventListener('change', toggleSertifikatFields);
  toggleSertifikatFields();
  </script>
</body>

</html>