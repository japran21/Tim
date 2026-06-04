<?php
// test_db.php
require_once 'koneksi.php';

echo "<h2>Test Koneksi Database</h2>";

// Cek tabel kategori_rasa
$query = "SELECT * FROM kategori_rasa";
$result = mysqli_query($koneksi, $query);

if ($result) {
    echo "<h3>✅ Kategori Rasa:</h3>";
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>ID: {$row['id_rasa']} - {$row['jenis_rasa']}</li>";
    }
    echo "</ul>";
} else {
    echo "❌ Error: " . mysqli_error($koneksi);
}

// Cek tabel produk
$query2 = "SELECT COUNT(*) as total FROM produk";
$result2 = mysqli_query($koneksi, $query2);
$row2 = mysqli_fetch_assoc($result2);
echo "<h3>📦 Total Produk: " . $row2['total'] . "</h3>";

// Cek tabel produk_rasa
$query3 = "SELECT COUNT(*) as total FROM produk_rasa";
$result3 = mysqli_query($koneksi, $query3);
$row3 = mysqli_fetch_assoc($result3);
echo "<h3>🔗 Total Relasi Produk-Rasa: " . $row3['total'] . "</h3>";

// Cek sample produk dengan rasa
$query4 = "SELECT p.nama_produk, kr.jenis_rasa 
          FROM produk p 
          JOIN produk_rasa pr ON p.id_produk = pr.id_produk
          JOIN kategori_rasa kr ON pr.id_rasa = kr.id_rasa
          LIMIT 5";
$result4 = mysqli_query($koneksi, $query4);
echo "<h3>📋 Sample Produk dengan Rasa:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Produk</th><th>Rasa</th></tr>";
while ($row = mysqli_fetch_assoc($result4)) {
    echo "<tr><td>{$row['nama_produk']}</td><td>{$row['jenis_rasa']}</td></tr>";
}
echo "</table>";
?>