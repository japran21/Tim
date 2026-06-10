<?php
// get_produk.php
header('Content-Type: application/json; charset=utf-8');

// Jangan tampilkan error mentah ke end-user; simpan ke log saja
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once 'koneksi.php';

// ── Helper: bersihkan prefix FOTO_UMKM/ yang mungkin masih ada di DB lama ────
function sanitasiFoto(?string $foto): ?string {
    if ($foto === null || $foto === '') return null;
    // Hapus prefix jika masih tersimpan dengan folder lama
    return ltrim(str_replace('FOTO_UMKM/', '', $foto), '/');
}

$result = [];

if (!empty($_GET['rasa'])) {
    $rasa = mysqli_real_escape_string($koneksi, trim($_GET['rasa']));

    $queryRasa = "SELECT id_rasa FROM kategori_rasa WHERE jenis_rasa = '$rasa'";
    $resultRasa = mysqli_query($koneksi, $queryRasa);

    if ($rowRasa = mysqli_fetch_assoc($resultRasa)) {
        $id_rasa = (int) $rowRasa['id_rasa'];

        $sql = "
            SELECT
                p.id_produk,
                p.nama_produk,
                p.harga,
                p.kategori_produk,
                p.asal_daerah,
                u.nama_umkm,
                u.foto,
                GROUP_CONCAT(DISTINCT kr.jenis_rasa ORDER BY kr.jenis_rasa SEPARATOR ', ') AS daftar_rasa
            FROM produk p
            JOIN umkm u  ON p.id_umkm  = u.id_umkm
            JOIN produk_rasa pr  ON p.id_produk = pr.id_produk
            JOIN kategori_rasa kr ON pr.id_rasa  = kr.id_rasa
            WHERE pr.id_rasa = $id_rasa
            GROUP BY p.id_produk, u.nama_umkm, u.foto
            ORDER BY p.nama_produk ASC
            LIMIT 50
        ";

        $query = mysqli_query($koneksi, $sql);

        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $row['foto']  = sanitasiFoto($row['foto']);
                $row['harga'] = (float) $row['harga'];
                $result[]     = $row;
            }
        } else {
            error_log("get_produk.php [rasa] query error: " . mysqli_error($koneksi));
            echo json_encode(['error' => 'Terjadi kesalahan saat mengambil data.']);
            exit;
        }

    } else {
        echo json_encode(['error' => "Rasa '$rasa' tidak ditemukan"]);
        exit;
    }

} elseif (!empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, trim($_GET['keyword']));

    $sql = "
        SELECT
            p.id_produk,
            p.nama_produk,
            p.harga,
            p.kategori_produk,
            p.asal_daerah,
            u.nama_umkm,
            u.foto,
            GROUP_CONCAT(DISTINCT kr.jenis_rasa ORDER BY kr.jenis_rasa SEPARATOR ', ') AS daftar_rasa
        FROM produk p
        JOIN umkm u  ON p.id_umkm = u.id_umkm
        LEFT JOIN produk_rasa pr  ON p.id_produk = pr.id_produk
        LEFT JOIN kategori_rasa kr ON pr.id_rasa  = kr.id_rasa
        WHERE p.nama_produk LIKE '%$keyword%'
           OR u.nama_umkm   LIKE '%$keyword%'
        GROUP BY p.id_produk, u.nama_umkm, u.foto
        ORDER BY p.nama_produk ASC
        LIMIT 50
    ";

    $query = mysqli_query($koneksi, $sql);

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $row['foto']  = sanitasiFoto($row['foto']);
            $row['harga'] = (float) $row['harga'];
            $result[]     = $row;
        }
    } else {
        error_log("get_produk.php [keyword] query error: " . mysqli_error($koneksi));
        echo json_encode(['error' => 'Terjadi kesalahan saat mengambil data.']);
        exit;
    }

} elseif (!empty($_GET['kategori'])) {
    $kategori = mysqli_real_escape_string($koneksi, trim($_GET['kategori']));

    $sql = "
        SELECT
            p.id_produk,
            p.nama_produk,
            p.harga,
            p.kategori_produk,
            p.asal_daerah,
            u.nama_umkm,
            u.foto,
            GROUP_CONCAT(DISTINCT kr.jenis_rasa ORDER BY kr.jenis_rasa SEPARATOR ', ') AS daftar_rasa
        FROM produk p
        JOIN umkm u  ON p.id_umkm = u.id_umkm
        LEFT JOIN produk_rasa pr  ON p.id_produk = pr.id_produk
        LEFT JOIN kategori_rasa kr ON pr.id_rasa  = kr.id_rasa
        WHERE p.kategori_produk = '$kategori'
        GROUP BY p.id_produk, u.nama_umkm, u.foto
        ORDER BY p.nama_produk ASC
        LIMIT 50
    ";

    $query = mysqli_query($koneksi, $sql);

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $row['foto']  = sanitasiFoto($row['foto']);
            $row['harga'] = (float) $row['harga'];
            $result[]     = $row;
        }
    } else {
        error_log("get_produk.php [kategori] query error: " . mysqli_error($koneksi));
        echo json_encode(['error' => 'Terjadi kesalahan saat mengambil data.']);
        exit;
    }

} elseif (!empty($_GET['asal_daerah'])) {
    $asal = mysqli_real_escape_string($koneksi, trim($_GET['asal_daerah']));

    $sql = "
        SELECT
            p.id_produk,
            p.nama_produk,
            p.harga,
            p.kategori_produk,
            p.asal_daerah,
            u.nama_umkm,
            u.foto,
            GROUP_CONCAT(DISTINCT kr.jenis_rasa ORDER BY kr.jenis_rasa SEPARATOR ', ') AS daftar_rasa
        FROM produk p
        JOIN umkm u  ON p.id_umkm = u.id_umkm
        LEFT JOIN produk_rasa pr  ON p.id_produk = pr.id_produk
        LEFT JOIN kategori_rasa kr ON pr.id_rasa  = kr.id_rasa
        WHERE p.asal_daerah = '$asal'
        GROUP BY p.id_produk, u.nama_umkm, u.foto
        ORDER BY p.nama_produk ASC
        LIMIT 100
    ";

    $query = mysqli_query($koneksi, $sql);
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $row['foto']  = sanitasiFoto($row['foto']);
            $row['harga'] = (float) $row['harga'];
            $result[]     = $row;
        }
    } else {
        error_log("get_produk.php [asal_daerah] query error: " . mysqli_error($koneksi));
        echo json_encode(['error' => 'Terjadi kesalahan saat mengambil data.']);
        exit;
    }

} else {
    echo json_encode(['error' => 'Parameter tidak ditemukan. Gunakan ?rasa=, ?keyword=, ?kategori=, atau ?asal_daerah=']);
    exit;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
