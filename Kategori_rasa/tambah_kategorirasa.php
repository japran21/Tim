<?php
session_start();
require_once 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_rasa = trim($_POST['jenis_rasa']);
    
    if (empty($jenis_rasa)) {
        $error = 'Jenis rasa tidak boleh kosong!';
    } else {
        // Cek apakah rasa sudah ada
        $check = mysqli_query($koneksi, "SELECT id_rasa FROM kategori_rasa WHERE jenis_rasa = '$jenis_rasa'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Rasa "' . htmlspecialchars($jenis_rasa) . '" sudah terdaftar!';
        } else {
            $query = "INSERT INTO kategori_rasa (jenis_rasa) VALUES ('$jenis_rasa')";
            if (mysqli_query($koneksi, $query)) {
                $_SESSION['message'] = 'Kategori rasa "' . htmlspecialchars($jenis_rasa) . '" berhasil ditambahkan!';
                $_SESSION['message_type'] = 'success';
                header('Location: kategori_rasa.php');
                exit;
            } else {
                $error = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
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
    <title>Tambah Kategori Rasa - UMKM Ciwaruga</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
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

        .emoji-tip {
            background: #fef3c7;
            color: #92400e;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-top: 12px;
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
            </nav>
            <div class="nav-actions"></div>
        </div>
    </header>

    <div class="form-container">
        <div class="form-card">
            <h1 class="form-title">➕ Tambah Kategori Rasa</h1>
            <p class="form-subtitle">Silakan masukkan jenis rasa baru untuk makanan di Ciwaruga</p>

            <?php if ($error): ?>
                <div class="error-message">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="jenis_rasa">Jenis Rasa *</label>
                    <input type="text" id="jenis_rasa" name="jenis_rasa" placeholder="Contoh: Pedas, Manis, Gurih, Asin, Asam" required value="<?= isset($_POST['jenis_rasa']) ? htmlspecialchars($_POST['jenis_rasa']) : '' ?>">
                    <div class="emoji-tip">
                        💡 Tips: Rasa yang umum digunakan: 🧂 Asin, 🍗 Gurih, 🍯 Manis, 🌶️ Pedas, 🍋 Asam
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Simpan</button>
                    <a href="kategori_rasa.php" class="btn-cancel">Batal</a>
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
</body>
</html>