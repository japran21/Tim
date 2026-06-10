<<<<<<< HEAD
<?php
session_start();
require_once 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_umkm = trim($_POST['nama_umkm']);
    $lokasi = trim($_POST['lokasi']);
    $nomor_kontak = !empty($_POST['nomor_kontak']) ? trim($_POST['nomor_kontak']) : NULL;
    $status_halal = $_POST['status_halal'];
    $no_sertifikat = !empty($_POST['no_sertifikat']) ? trim($_POST['no_sertifikat']) : NULL;
    $lembaga_penerbit = !empty($_POST['lembaga_penerbit']) ? trim($_POST['lembaga_penerbit']) : NULL;
    $tanggal_terbit = !empty($_POST['tanggal_terbit']) ? $_POST['tanggal_terbit'] : NULL;
    
    // Upload foto
    $foto = NULL;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "FOTO_UMKM/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
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
        $foto_sql = $foto ? "'$foto'" : "NULL";
        $kontak_sql = $nomor_kontak ? "'$nomor_kontak'" : "NULL";
        $no_sertifikat_sql = $no_sertifikat ? "'$no_sertifikat'" : "NULL";
        $lembaga_sql = $lembaga_penerbit ? "'$lembaga_penerbit'" : "NULL";
        $tgl_sql = $tanggal_terbit ? "'$tanggal_terbit'" : "NULL";
        
        $query = "INSERT INTO umkm (nama_umkm, lokasi, foto, nomor_kontak, status_halal, no_sertifikat, lembaga_penerbit, tanggal_terbit) 
                  VALUES ('$nama_umkm', '$lokasi', $foto_sql, $kontak_sql, '$status_halal', $no_sertifikat_sql, $lembaga_sql, $tgl_sql)";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['message'] = 'UMKM "' . htmlspecialchars($nama_umkm) . '" berhasil ditambahkan!';
            $_SESSION['message_type'] = 'success';
            header('Location: umkm.php');
            exit;
        } else {
            $error = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
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
  <title>Tambah UMKM - Street Food Ciwaruga</title>
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
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="form-container">
    <div class="form-card">
      <h1 class="form-title">➕ Tambah UMKM Baru</h1>
      <p class="form-subtitle">Silakan masukkan data UMKM makanan/minuman di Ciwaruga</p>

      <?php if ($error): ?>
      <div class="error-message">
        ⚠️ <?= $error ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
          <label for="nama_umkm">Nama UMKM *</label>
          <input type="text" id="nama_umkm" name="nama_umkm" placeholder="Contoh: Cimol Bojot AA" required>
        </div>

        <div class="form-group">
          <label for="lokasi">Lokasi / Alamat *</label>
          <textarea id="lokasi" name="lokasi"
            placeholder="Jl. Ciwaruga No..., Kec. Parongpong, Kab. Bandung Barat, Jawa Barat" required></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="nomor_kontak">Nomor Kontak</label>
            <input type="text" id="nomor_kontak" name="nomor_kontak" placeholder="6281234567890">
          </div>

          <div class="form-group">
            <label for="foto">Foto UMKM</label>
            <input type="file" id="foto" name="foto" accept="image/*">
          </div>
        </div>

        <div class="form-group">
          <label for="status_halal">Status Halal *</label>
          <select id="status_halal" name="status_halal" required>
            <option value="Halal Bersertifikat">Halal Bersertifikat</option>
            <option value="Halal Belum Bersertifikat">Halal Belum Bersertifikat</option>
            <option value="Non-Halal">Non-Halal</option>
          </select>
        </div>

        <div class="form-row" id="sertifikat_fields">
          <div class="form-group">
            <label for="no_sertifikat">No Sertifikat Halal</label>
            <input type="text" id="no_sertifikat" name="no_sertifikat" placeholder="ID32110016944470224">
          </div>

          <div class="form-group">
            <label for="lembaga_penerbit">Lembaga Penerbit</label>
            <input type="text" id="lembaga_penerbit" name="lembaga_penerbit" placeholder="BPJPH / MUI">
          </div>

          <div class="form-group">
            <label for="tanggal_terbit">Tanggal Terbit</label>
            <input type="date" id="tanggal_terbit" name="tanggal_terbit">
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Simpan</button>
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

=======
<?php
session_start();
require_once 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_umkm = trim($_POST['nama_umkm']);
    $lokasi = trim($_POST['lokasi']);
    $nomor_kontak = !empty($_POST['nomor_kontak']) ? trim($_POST['nomor_kontak']) : NULL;
    $status_halal = $_POST['status_halal'];
    $no_sertifikat = !empty($_POST['no_sertifikat']) ? trim($_POST['no_sertifikat']) : NULL;
    $lembaga_penerbit = !empty($_POST['lembaga_penerbit']) ? trim($_POST['lembaga_penerbit']) : NULL;
    $tanggal_terbit = !empty($_POST['tanggal_terbit']) ? $_POST['tanggal_terbit'] : NULL;
    
    // Upload foto
    $foto = NULL;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "FOTO_UMKM/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
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
        $foto_sql = $foto ? "'$foto'" : "NULL";
        $kontak_sql = $nomor_kontak ? "'$nomor_kontak'" : "NULL";
        $no_sertifikat_sql = $no_sertifikat ? "'$no_sertifikat'" : "NULL";
        $lembaga_sql = $lembaga_penerbit ? "'$lembaga_penerbit'" : "NULL";
        $tgl_sql = $tanggal_terbit ? "'$tanggal_terbit'" : "NULL";
        
        $query = "INSERT INTO umkm (nama_umkm, lokasi, foto, nomor_kontak, status_halal, no_sertifikat, lembaga_penerbit, tanggal_terbit) 
                  VALUES ('$nama_umkm', '$lokasi', $foto_sql, $kontak_sql, '$status_halal', $no_sertifikat_sql, $lembaga_sql, $tgl_sql)";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['message'] = 'UMKM "' . htmlspecialchars($nama_umkm) . '" berhasil ditambahkan!';
            $_SESSION['message_type'] = 'success';
            header('Location: umkm.php');
            exit;
        } else {
            $error = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
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
  <title>Tambah UMKM - Street Food Ciwaruga</title>
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
      </nav>
      <div class="nav-actions"></div>
    </div>
  </header>

  <div class="form-container">
    <div class="form-card">
      <h1 class="form-title">➕ Tambah UMKM Baru</h1>
      <p class="form-subtitle">Silakan masukkan data UMKM makanan/minuman di Ciwaruga</p>

      <?php if ($error): ?>
      <div class="error-message">
        ⚠️ <?= $error ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
          <label for="nama_umkm">Nama UMKM *</label>
          <input type="text" id="nama_umkm" name="nama_umkm" placeholder="Contoh: Cimol Bojot AA" required>
        </div>

        <div class="form-group">
          <label for="lokasi">Lokasi / Alamat *</label>
          <textarea id="lokasi" name="lokasi"
            placeholder="Jl. Ciwaruga No..., Kec. Parongpong, Kab. Bandung Barat, Jawa Barat" required></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="nomor_kontak">Nomor Kontak</label>
            <input type="text" id="nomor_kontak" name="nomor_kontak" placeholder="6281234567890">
          </div>

          <div class="form-group">
            <label for="foto">Foto UMKM</label>
            <input type="file" id="foto" name="foto" accept="image/*">
          </div>
        </div>

        <div class="form-group">
          <label for="status_halal">Status Halal *</label>
          <select id="status_halal" name="status_halal" required>
            <option value="Halal Bersertifikat">Halal Bersertifikat</option>
            <option value="Halal Belum Bersertifikat">Halal Belum Bersertifikat</option>
            <option value="Non-Halal">Non-Halal</option>
          </select>
        </div>

        <div class="form-row" id="sertifikat_fields">
          <div class="form-group">
            <label for="no_sertifikat">No Sertifikat Halal</label>
            <input type="text" id="no_sertifikat" name="no_sertifikat" placeholder="ID32110016944470224">
          </div>

          <div class="form-group">
            <label for="lembaga_penerbit">Lembaga Penerbit</label>
            <input type="text" id="lembaga_penerbit" name="lembaga_penerbit" placeholder="BPJPH / MUI">
          </div>

          <div class="form-group">
            <label for="tanggal_terbit">Tanggal Terbit</label>
            <input type="date" id="tanggal_terbit" name="tanggal_terbit">
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Simpan</button>
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

>>>>>>> fcfb940 (update)
</html>