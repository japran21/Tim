<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID topping tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: kategori_topping.php');
    exit;
}

$id_topping = (int)$_GET['id'];

// Ambil data topping
$query = "SELECT * FROM kategori_topping WHERE id_topping = $id_topping";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['message'] = 'Data topping tidak ditemukan!';
    $_SESSION['message_type'] = 'error';
    header('Location: kategori_topping.php');
    exit;
}

$topping = mysqli_fetch_assoc($result);
$error = '';
$error_type = 'normal';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_topping = trim($_POST['nama_topping']);
    
    if (empty($nama_topping)) {
        $error = 'Nama topping tidak boleh kosong!';
    } else {
        // Cek apakah topping sudah ada (kecuali yang sedang diedit)
        $check = mysqli_query($koneksi, "SELECT id_topping FROM kategori_topping WHERE nama_topping = '$nama_topping' AND id_topping != $id_topping");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Topping "' . htmlspecialchars($nama_topping) . '" sudah terdaftar!';
        } else {
            // Update menggunakan transaksi
            mysqli_begin_transaction($koneksi);
            
            try {
                $query_update = "UPDATE kategori_topping SET nama_topping = '$nama_topping' WHERE id_topping = $id_topping";
                if (!mysqli_query($koneksi, $query_update)) {
                    throw new Exception('Gagal memperbarui data: ' . mysqli_error($koneksi));
                }
                
                mysqli_commit($koneksi);
                
                $_SESSION['message'] = 'Data topping berhasil diperbarui!';
                $_SESSION['message_type'] = 'success';
                header('Location: kategori_topping.php');
                exit;
                
            } catch (Exception $e) {
                mysqli_rollback($koneksi);
                $error = $e->getMessage();
                $error_type = 'rollback';
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
    <title>Edit Kategori Topping - UMKM Ciwaruga</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .form-container { max-width: 600px; margin: 40px auto; padding: 0 24px; }
        .form-card { background: white; border-radius: 24px; padding: 32px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); }
        .form-title { font-family: 'Playfair Display', serif; color: #1a1a2e; font-size: 1.8rem; margin-bottom: 8px; }
        .form-subtitle { color: #6b7280; margin-bottom: 32px; }
        .form-group { margin-bottom: 24px; }
        label { display: block; font-weight: 600; color: #374151; margin-bottom: 8px; }
        input[type="text"] { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: border-color 0.2s; font-family: inherit; }
        input[type="text"]:focus { outline: none; border-color: #2e6b4f; }
        .form-actions { display: flex; gap: 12px; margin-top: 32px; }
        .btn-submit { background: #2e6b4f; color: white; padding: 12px 28px; border: none; border-radius: 40px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-submit:hover { background: #1a2e1e; }
        .btn-cancel { background: #f3f4f6; color: #6b7280; padding: 12px 28px; border: none; border-radius: 40px; font-weight: 600; text-decoration: none; text-align: center; transition: background 0.2s; }
        .btn-cancel:hover { background: #e5e7eb; }
        .error-message { background: #fee2e2; color: #991b1b; padding: 12px 20px; border-radius: 12px; margin-bottom: 24px; }
        .info-card { background: #e0f2fe; padding: 12px 16px; border-radius: 12px; margin-top: 20px; }
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
                <a href="kategori_topping.php">Kelola Topping</a>
            </nav>
        </div>
    </header>

    <div class="form-container">
        <div class="form-card">
            <h1 class="form-title">✏️ Edit Kategori Topping</h1>
            <p class="form-subtitle">Ubah informasi jenis topping makanan</p>

            <?php if ($error && $error_type === 'rollback'): ?>
                <div style="background:#1a1a2e;color:#fff;border-radius:16px;padding:24px 28px;margin-bottom:24px;border-left:5px solid #ef4444;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                        <span style="font-size:1.3rem;">🔴</span>
                        <strong style="font-size:.95rem;letter-spacing:.05em;text-transform:uppercase;">GAGAL — PERUBAHAN DIBATALKAN (ROLLBACK)</strong>
                    </div>
                    <div style="font-size:.88rem;color:#fca5a5;margin-bottom:10px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                    <div style="font-size:.8rem;color:#9ca3af;">Tidak ada perubahan yang tersimpan. Silakan periksa kembali data dan coba lagi.</div>
                </div>
            <?php elseif ($error): ?>
                <div class="error-message">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama_topping">Nama Topping *</label>
                    <input type="text" id="nama_topping" name="nama_topping" value="<?= htmlspecialchars($topping['nama_topping']) ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                    <a href="kategori_topping.php" class="btn-cancel">Batal</a>
                </div>
            </form>
            
            <div class="info-card">
                ℹ️ <strong>Informasi:</strong> Perubahan nama topping akan langsung terlihat pada semua produk yang menggunakan topping ini karena menggunakan relasi antar tabel.
            </div>
        </div>
    </div>
</body>
</html>
