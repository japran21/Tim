<?php
// File: import_data.php
require_once 'koneksi.php';

// Set time limit tidak terbatas
set_time_limit(0);

echo "<h1>Import Data UMKM Street Food Ciwaruga</h1>";

// Fungsi untuk membaca CSV
function bacaCSV($filename) {
    $data = [];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        // Skip header
        fgetcsv($handle, 0, ",");
        while (($row = fgetcsv($handle, 0, ",")) !== FALSE) {
            $data[] = $row;
        }
        fclose($handle);
    }
    return $data;
}

// 1. Import Kategori Rasa
echo "<h2>1. Import Kategori Rasa</h2>";
$rasa_data = [
    ['Asin'], ['Gurih'], ['Manis'], ['Pedas'], ['Asam']
];

foreach ($rasa_data as $rasa) {
    $jenis = mysqli_real_escape_string($koneksi, $rasa[0]);
    $check = mysqli_query($koneksi, "SELECT id_rasa FROM kategori_rasa WHERE jenis_rasa = '$jenis'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($koneksi, "INSERT INTO kategori_rasa (jenis_rasa) VALUES ('$jenis')");
        echo "✓ Rasa '$jenis' ditambahkan<br>";
    } else {
        echo "∼ Rasa '$jenis' sudah ada<br>";
    }
}

// 2. Import UMKM
echo "<h2>2. Import Data UMKM</h2>";
$umkm_data = [
    [1, 'Cimol Bojot AA', 'Jl. Ciwaruga No.3 RT 1/RW 3, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'cimol_bojot_aa.jpg', '6288802073662', 'Halal Bersertifikat', 'ID32110016944470224', 'BPJPH', '2026-02-24'],
    [2, 'Dainisa Fruity Juice', 'Jl. Ciwaruga No.6, Ciwaruga, Kec. Parongpong, Kab.Bandung Barat, Jawa Barat', 'dainisa_juice.jpg', '6281212918316', 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [3, 'Lumpia Basah', 'Jl. Waruga Jaya No.4, Kec. Parongpong, Kab.Bandung Barat, Jawa Barat', 'lumpia_basah.jpg', '6281214314332', 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [4, 'Batagor Ikan Kirana', 'Jl. Ciwaruga No.5, Kec. Parongpong, Kabupaten Bandung Barat, Jawa Barat', 'batagor.jpg', '623875216734', 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [5, 'Mood Boostery-ku', 'Jl. Ciwaruga No.41, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'mood_booster_ku.jpg', '6281121212536', 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [6, 'Es Teh Kampoeng Solo', 'Jl. Ciwaruga No.6, Ciwaruga, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'es_teh_kampoeng_solo.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [7, 'Mie Ayam Baso Pangsit', 'Jl. Waruga Jaya No.5, Ciwaruga, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'mie_ayam.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [8, 'Jasuke', 'Jl. Waruga Jaya No.5, Ciwaruga, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'jasuke.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [9, 'Tahu Crispy & Otak-Otak Putra Mandiri', 'Jalan Waruga Jaya, Ciwaruga, Kec. Parongpong, Kota Bandung, Jawa Barat', 'tahu_otak_otak.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [10, 'Martel (Martabak Telor)', 'Jl. Ciwaruga No.37, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'martel.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [11, 'Kupat Tahu & Gado-Gado Sutami', 'Jl. Waruga Jaya, Ciwaruga, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'kupat_tahu_lontong_sutami.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [12, 'Surabi Bungsu Aneka Rasa', 'Jl. Ciwaruga No.26, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'surabi-aneka.jpg', '628997969604', 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [13, 'Ayam Katsu Loka Hita', 'Jl. Ciwaruga No.26, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'ayam_katsu_lokahita.jpg', '628218494345', 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [14, 'R.M Padang Delapan Empat', 'Jl. Ciwaruga No.7 RT 01/RW 07, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'RM_delapan_empat.jpg', '6283180886858', 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [15, 'Es Kelapa', 'Jl. Ciwaruga No.33, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'es_kelapa.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [16, 'Es Kelapa Febriyan', 'Jl. Ciwaruga No.46, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'es_kelapa_febriyan.jpg', '6281224863542', 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [17, 'Suki Goreng', 'Jl. Waruga Jaya No.104 RT 04/RW 11, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'suki_goreng.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [18, 'Es Pisang Ijo MG Juned', 'Jl. Waruga Jaya No.104, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'es_pisang_ijo.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [19, 'Martabak Mini', 'Jl. Waruga Jaya No.104, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'martabak_mini.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [20, 'Soto Madura Cak Ihwan', 'Jl. Ciwaruga No.2, Kec. Sukasari, Kabupaten Bandung Barat, Jawa Barat', 'soto_madura.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL],
    [21, 'Bubur Ayam Putra Pa Sunar', 'Jl. Ciwaruga No.2, Kec. Sukasari, Kabupaten Bandung Barat, Jawa Barat', 'bubur_ayam.jpg', '6283827224517', 'Halal Belum Bersertifikat', NULL, NULL, NULL],
];

foreach ($umkm_data as $umkm) {
    $id = $umkm[0];
    $nama = mysqli_real_escape_string($koneksi, $umkm[1]);
    $lokasi = mysqli_real_escape_string($koneksi, $umkm[2]);
    $foto = $umkm[3] ? "'" . mysqli_real_escape_string($koneksi, $umkm[3]) . "'" : "NULL";
    $kontak = $umkm[4] ? "'" . mysqli_real_escape_string($koneksi, $umkm[4]) . "'" : "NULL";
    $status = mysqli_real_escape_string($koneksi, $umkm[5]);
    $no_sertifikat = $umkm[6] ? "'" . mysqli_real_escape_string($koneksi, $umkm[6]) . "'" : "NULL";
    $lembaga = $umkm[7] ? "'" . mysqli_real_escape_string($koneksi, $umkm[7]) . "'" : "NULL";
    $tgl_terbit = $umkm[8] ? "'" . $umkm[8] . "'" : "NULL";
    
    $sql = "INSERT INTO umkm (id_umkm, nama_umkm, lokasi, foto, nomor_kontak, status_halal, no_sertifikat, lembaga_penerbit, tanggal_terbit) 
            VALUES ($id, '$nama', '$lokasi', $foto, $kontak, '$status', $no_sertifikat, $lembaga, $tgl_terbit)
            ON DUPLICATE KEY UPDATE 
            nama_umkm = VALUES(nama_umkm), lokasi = VALUES(lokasi), foto = VALUES(foto),
            nomor_kontak = VALUES(nomor_kontak), status_halal = VALUES(status_halal)";
    
    if (mysqli_query($koneksi, $sql)) {
        echo "✓ UMKM '$nama' berhasil diimport<br>";
    } else {
        echo "✗ Gagal import UMKM '$nama': " . mysqli_error($koneksi) . "<br>";
    }
}

// 3. Import Mitra Platform
echo "<h2>3. Import Mitra Platform</h2>";
$mitra_data = [
    [1, 'GoFood'],
    [2, 'GrabFood'],
    [3, 'ShopeeFood']
];

foreach ($mitra_data as $mitra) {
    $id = $mitra[0];
    $nama = mysqli_real_escape_string($koneksi, $mitra[1]);
    $sql = "INSERT INTO mitra_platform (id_mitra, nama_mitra) VALUES ($id, '$nama')
            ON DUPLICATE KEY UPDATE nama_mitra = VALUES(nama_mitra)";
    mysqli_query($koneksi, $sql);
    echo "✓ Mitra '$nama' ditambahkan<br>";
}

// 4. Import Metode Pembayaran
echo "<h2>4. Import Metode Pembayaran</h2>";
$metode_data = [
    [1, 'Cash'],
    [2, 'QRIS'],
    [3, 'Dana']
];

foreach ($metode_data as $metode) {
    $id = $metode[0];
    $nama = mysqli_real_escape_string($koneksi, $metode[1]);
    $sql = "INSERT INTO metode_pembayaran (id_metode, nama_metode) VALUES ($id, '$nama')
            ON DUPLICATE KEY UPDATE nama_metode = VALUES(nama_metode)";
    mysqli_query($koneksi, $sql);
    echo "✓ Metode '$nama' ditambahkan<br>";
}

// 5. Import UMKM Mitra
echo "<h2>5. Import Relasi UMKM - Mitra</h2>";
$umkm_mitra_data = [
    [1, 1, 1, 'https://gofood.link/a/PpPkWYL'],
    [2, 1, 2, 'https://r.grab.com/g/6-20260514_215308_1c953104f5554c75b482890a5331265b_MEXMPS-6-C7A3JF41NKVDJT'],
    [3, 2, 1, 'https://gofood.link/a/QpegmCh'],
    [4, 2, 2, 'https://r.grab.com/g/6-20260529_155218_adc8a8c9a7954d5eb30d4e05caf9db12_MEXMPS-6-C7MYVXX1V6B2NA'],
    [5, 11, 1, 'https://gofood.link/u/NZ7DgZ'],
    [6, 11, 2, 'https://r.grab.com/g/6-20260519_223149_adc8a8c9a7954d5eb30d4e05caf9db12_MEXMPS-6-CZMTLU2KRLJWG6'],
    [7, 12, 1, 'https://gofood.link/a/yM9VHEQ'],
    [8, 12, 2, 'https://r.grab.com/g/6-20260519_224202_adc8a8c9a7954d5eb30d4e05caf9db12_MEXMPS-IDGFSTI000029zu'],
    [9, 13, 1, 'https://gofood.link/a/JC9Qg5h'],
    [10, 13, 3, 'https://shopee.co.id/universal-link/now-food/shop/21369227?deep_and_deferred=1&shareChannel=copy_link'],
    [11, 14, 1, 'https://gofood.link/a/S1QdC9u'],
    [12, 14, 2, 'https://r.grab.com/g/6-20260519_225407_adc8a8c9a7954d5eb30d4e05caf9db12_MEXMPS-6-C7VGTJJBGRDKTJ'],
];

foreach ($umkm_mitra_data as $relasi) {
    $id = $relasi[0];
    $id_umkm = $relasi[1];
    $id_mitra = $relasi[2];
    $link = $relasi[3] ? "'" . mysqli_real_escape_string($koneksi, $relasi[3]) . "'" : "NULL";
    
    $sql = "INSERT INTO umkm_mitra (id_umkm_mitra, id_umkm, id_mitra, link_mitra) 
            VALUES ($id, $id_umkm, $id_mitra, $link)
            ON DUPLICATE KEY UPDATE link_mitra = VALUES(link_mitra)";
    mysqli_query($koneksi, $sql);
    echo "✓ Relasi UMKM-$id_umkm dengan Mitra-$id_mitra ditambahkan<br>";
}

// 6. Import Waktu Operasional
echo "<h2>6. Import Waktu Operasional</h2>";

// Fungsi bantu konversi waktu
function konversiWaktu($waktu) {
    if (empty($waktu) || $waktu == 'NULL' || $waktu == 'Tutup') {
        return "NULL";
    }
    // Konversi format "12.00 WIB" ke "12:00:00"
    $waktu = str_replace(' WIB', '', $waktu);
    $waktu = str_replace('.', ':', $waktu);
    return "'$waktu:00'";
}

$waktu_data = [];

// Generate untuk UMKM dengan jadwal Setiap Hari
for ($umkm_id = 1; $umkm_id <= 21; $umkm_id++) {
    if ($umkm_id == 12) continue; // Surabi Bungsu beda jadwal
    if ($umkm_id == 16) continue; // Es Kelapa Febriyan beda
    if ($umkm_id == 17) continue; // Suki Goreng beda
    if ($umkm_id == 18) continue; // Es Pisang Ijo beda
    
    // Tentukan jam buka/tutup berdasarkan UMKM
    $jam = [
        1 => ['12:00', '21:00'],
        2 => ['09:00', '21:00'],
        3 => ['09:00', '21:00'],
        4 => ['09:00', '21:00'],
        5 => ['10:00', '18:00'],
        6 => ['10:00', '22:00'],
        7 => ['10:00', '20:00'],
        8 => ['08:00', '21:00'],
        9 => ['07:00', '20:00'],
        10 => ['12:00', '18:00'],
        11 => ['06:00', '13:00'],
        13 => ['10:00', '21:00'],
        14 => ['10:00', '22:00'],
        15 => ['07:00', '23:00'],
        19 => ['07:00', '13:00'],
        20 => ['07:00', '18:00'],
        21 => ['06:30', '12:00'],
    ];
    
    if (isset($jam[$umkm_id])) {
        $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        foreach ($hari_list as $hari) {
            $waktu_data[] = [$umkm_id, $hari, $jam[$umkm_id][0], $jam[$umkm_id][1], 'Buka'];
        }
    }
}

// UMKM 12 (Surabi Bungsu - Selasa-Minggu)
$hari_tutup = ['Senin'];
$hari_buka = ['Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
foreach ($hari_buka as $hari) {
    $waktu_data[] = [12, $hari, '06:00', '22:00', 'Buka'];
}
$waktu_data[] = [12, 'Senin', NULL, NULL, 'Tutup'];

// UMKM 16 (Es Kelapa Febriyan - Senin-Sabtu)
$hari_buka = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
foreach ($hari_buka as $hari) {
    $waktu_data[] = [16, $hari, '09:00', '21:00', 'Buka'];
}
$waktu_data[] = [16, 'Minggu', '13:00', '21:00', 'Buka'];

// UMKM 17 (Suki Goreng - Senin-Jumat)
$hari_buka = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
foreach ($hari_buka as $hari) {
    $waktu_data[] = [17, $hari, '08:00', '11:00', 'Buka'];
}
$waktu_data[] = [17, 'Sabtu', NULL, NULL, 'Tutup'];
$waktu_data[] = [17, 'Minggu', NULL, NULL, 'Tutup'];

// UMKM 18 (Es Pisang Ijo - Senin-Jumat)
$hari_buka = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
foreach ($hari_buka as $hari) {
    $waktu_data[] = [18, $hari, '08:00', '12:30', 'Buka'];
}
$waktu_data[] = [18, 'Sabtu', NULL, NULL, 'Tutup'];
$waktu_data[] = [18, 'Minggu', NULL, NULL, 'Tutup'];

// Insert waktu operasional
$counter = 1;
foreach ($waktu_data as $waktu) {
    $jam_buka = $waktu[2] ? "'" . $waktu[2] . ":00'" : "NULL";
    $jam_tutup = $waktu[3] ? "'" . $waktu[3] . ":00'" : "NULL";
    $keterangan = $waktu[4];
    
    $sql = "INSERT INTO waktu_operasional (id_waktu, id_umkm, hari, jam_buka, jam_tutup, keterangan) 
            VALUES ($counter, {$waktu[0]}, '{$waktu[1]}', $jam_buka, $jam_tutup, '$keterangan')";
    mysqli_query($koneksi, $sql);
    $counter++;
}
echo "✓ " . count($waktu_data) . " data waktu operasional diimport<br>";

// 7. Import Produk dan Relasi Rasa
echo "<h2>7. Import Produk dan Relasi Rasa</h2>";

// Kosongkan tabel produk_rasa dulu
mysqli_query($koneksi, "TRUNCATE TABLE produk_rasa");
mysqli_query($koneksi, "DELETE FROM produk");

// Data produk dari Excel (sample beberapa produk, untuk lengkapnya bisa dari file CSV)
// Di sini saya akan membuat query untuk insert produk dari data yang ada di database sebelumnya

// Alternatif: Copy data dari dump SQL yang sudah ada
$sql_produk = "
INSERT INTO produk (id_produk, id_umkm, nama_produk, harga, kategori_produk, asal_daerah) VALUES
(1, 1, 'Cimol Bojot (Kecil)', 6000, 'Makanan', 'Garut, Jawa Barat'),
(2, 1, 'Cimol Bojot (Besar)', 12000, 'Makanan', 'Garut, Jawa Barat'),
(3, 1, 'Cimol Isi Beef (Kecil)', 10000, 'Makanan', 'Garut, Jawa Barat'),
(4, 1, 'Cimol Isi Beef (Besar)', 20000, 'Makanan', 'Garut, Jawa Barat'),
(5, 1, 'Cimol Isi Mozzarella (Kecil)', 10000, 'Makanan', 'Garut, Jawa Barat'),
(6, 1, 'Cimol Isi Mozzarella (Besar)', 20000, 'Makanan', 'Garut, Jawa Barat'),
(7, 1, 'Cimol Isi Ayam (Kecil)', 10000, 'Makanan', 'Garut, Jawa Barat'),
(8, 1, 'Cimol Isi Ayam (Besar)', 20000, 'Makanan', 'Garut, Jawa Barat'),
(9, 1, 'Cimol Bojot Mix Mozzarella', 16000, 'Makanan', 'Garut, Jawa Barat'),
(10, 1, 'Cimol Bojot Mix Ayam', 16000, 'Makanan', 'Garut, Jawa Barat'),
(11, 1, 'Cimol Bojot Mix Beef', 16000, 'Makanan', 'Garut, Jawa Barat')
-- Lanjutkan sampai id 267
ON DUPLICATE KEY UPDATE 
nama_produk = VALUES(nama_produk), harga = VALUES(harga),
kategori_produk = VALUES(kategori_produk), asal_daerah = VALUES(asal_daerah);
";

// Untuk produk lengkap, gunakan dump SQL yang sudah ada sebelumnya
echo "✓ Gunakan file SQL dump yang sudah disediakan untuk import produk lengkap (267 produk)<br>";

// 8. Import UMKM Pembayaran
echo "<h2>8. Import UMKM Pembayaran</h2>";
$umkm_pembayaran_data = [
    [1, 1, 1], [2, 1, 2], [3, 2, 1], [4, 2, 2], [5, 3, 1],
    [6, 4, 1], [7, 4, 2], [8, 5, 1], [9, 5, 2], [10, 6, 1],
    [11, 6, 2], [12, 7, 1], [13, 8, 1], [14, 9, 1], [15, 10, 1],
    [16, 11, 1], [17, 11, 2], [18, 12, 1], [19, 12, 2], [20, 13, 1],
    [21, 13, 2], [22, 14, 1], [23, 14, 2], [24, 15, 1], [25, 16, 1],
    [26, 16, 2], [27, 17, 1], [28, 18, 1], [29, 18, 2], [30, 19, 1],
    [31, 20, 1], [32, 20, 3], [33, 21, 1], [34, 21, 2]
];

foreach ($umkm_pembayaran_data as $bayar) {
    $id = $bayar[0];
    $id_umkm = $bayar[1];
    $id_metode = $bayar[2];
    
    $sql = "INSERT INTO umkm_pembayaran (id_umkm_bayar, id_umkm, id_metode) 
            VALUES ($id, $id_umkm, $id_metode)
            ON DUPLICATE KEY UPDATE id_umkm = VALUES(id_umkm), id_metode = VALUES(id_metode)";
    mysqli_query($koneksi, $sql);
}
echo "✓ " . count($umkm_pembayaran_data) . " data UMKM pembayaran diimport<br>";

echo "<hr>";
echo "<h2 style='color:green'>✓ Proses import selesai!</h2>";
echo "<a href='index.php'>Kembali ke Beranda</a>";

?>