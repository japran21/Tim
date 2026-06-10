-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Jun 2026 pada 14.38
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_streetfood`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProdukByUMKM` (IN `p_id_umkm` INT)   BEGIN
    SELECT 
        p.id_produk,
        p.nama_produk,
        p.harga,
        p.kategori_produk,
        p.asal_daerah,
        u.nama_umkm
    FROM produk p
    JOIN umkm u ON p.id_umkm = u.id_umkm
    WHERE p.id_umkm = p_id_umkm
    ORDER BY p.nama_produk ASC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_rasa`
--

CREATE TABLE `kategori_rasa` (
  `id_rasa` int(11) NOT NULL,
  `jenis_rasa` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_rasa`
--

INSERT INTO `kategori_rasa` (`id_rasa`, `jenis_rasa`) VALUES
(1, 'Asin'),
(2, 'Gurih'),
(3, 'Manis'),
(4, 'Pedas'),
(5, 'Asam');

-- --------------------------------------------------------

--
-- Struktur dari tabel `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode` int(11) NOT NULL,
  `nama_metode` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id_metode`, `nama_metode`) VALUES
(1, 'Cash'),
(2, 'QRIS'),
(3, 'Dana'),
(4, 'OVO');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mitra_platform`
--

CREATE TABLE `mitra_platform` (
  `id_mitra` int(11) NOT NULL,
  `nama_mitra` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mitra_platform`
--

INSERT INTO `mitra_platform` (`id_mitra`, `nama_mitra`) VALUES
(1, 'GoFood'),
(2, 'GrabFood'),
(3, 'ShopeeFood');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `id_umkm` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kategori_produk` varchar(50) NOT NULL,
  `asal_daerah` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id_produk`, `id_umkm`, `nama_produk`, `harga`, `kategori_produk`, `asal_daerah`) VALUES
(1, 1, 'Cimol Bojot (Kecil)', 6000.00, 'Makanan', 'Garut, Jawa Barat'),
(2, 1, 'Cimol Bojot (Besar)', 12000.00, 'Makanan', 'Garut, Jawa Barat'),
(3, 1, 'Cimol Isi Beef (Kecil)', 10000.00, 'Makanan', 'Garut, Jawa Barat'),
(4, 1, 'Cimol Isi Beef (Besar)', 20000.00, 'Makanan', 'Garut, Jawa Barat'),
(5, 1, 'Cimol Isi Mozzarella (Kecil)', 10000.00, 'Makanan', 'Garut, Jawa Barat'),
(6, 1, 'Cimol Isi Mozzarella (Besar)', 20000.00, 'Makanan', 'Garut, Jawa Barat'),
(7, 1, 'Cimol Isi Ayam (Kecil)', 10000.00, 'Makanan', 'Garut, Jawa Barat'),
(8, 1, 'Cimol Isi Ayam (Besar)', 20000.00, 'Makanan', 'Garut, Jawa Barat'),
(9, 1, 'Cimol Bojot Mix Mozzarella', 16000.00, 'Makanan', 'Garut, Jawa Barat'),
(10, 1, 'Cimol Bojot Mix Ayam', 16000.00, 'Makanan', 'Garut, Jawa Barat'),
(11, 1, 'Cimol Bojot Mix Beef', 16000.00, 'Makanan', 'Garut, Jawa Barat'),
(12, 1, 'Cimol Ayam Mix Mozzarella', 20000.00, 'Makanan', 'Garut, Jawa Barat'),
(13, 1, 'Cimol Beef Mix Ayam', 20000.00, 'Makanan', 'Garut, Jawa Barat'),
(14, 1, 'Cimol Mozzarella Mix Beef', 20000.00, 'Makanan', 'Garut, Jawa Barat'),
(15, 2, 'Jus Alpukat', 12000.00, 'Minuman', 'NULL'),
(16, 2, 'Jus Mangga', 12000.00, 'Minuman', 'NULL'),
(17, 2, 'Jus Lemon', 12000.00, 'Minuman', 'NULL'),
(18, 2, 'Jus Apel', 12000.00, 'Minuman', 'NULL'),
(19, 2, 'Jus Buah Naga', 12000.00, 'Minuman', 'NULL'),
(20, 2, 'Jus Sunkist', 12000.00, 'Minuman', 'NULL'),
(21, 2, 'Jus Strawberry', 12000.00, 'Minuman', 'NULL'),
(22, 2, 'Jus Jeruk Yakult', 12000.00, 'Minuman', 'NULL'),
(23, 2, 'Jus Kiwi', 12000.00, 'Minuman', 'NULL'),
(24, 2, 'Jus Kweni', 12000.00, 'Minuman', 'NULL'),
(25, 2, 'Jus Jeruk', 8000.00, 'Minuman', 'NULL'),
(26, 2, 'Jus Jambu', 8000.00, 'Minuman', 'NULL'),
(27, 2, 'Jus Wortel', 8000.00, 'Minuman', 'NULL'),
(28, 2, 'Jus Pisang', 8000.00, 'Minuman', 'NULL'),
(29, 2, 'Jus Tomat', 8000.00, 'Minuman', 'NULL'),
(30, 2, 'Dancow', 8000.00, 'Topping', 'NULL'),
(31, 2, 'Jus Sirsak', 9000.00, 'Minuman', 'NULL'),
(32, 2, 'Capcin Oreo', 9000.00, 'Minuman', 'NULL'),
(33, 2, 'Jus Belimbing', 7000.00, 'Minuman', 'NULL'),
(34, 2, 'Jus Nanas', 7000.00, 'Minuman', 'NULL'),
(35, 2, 'Jus Pepaya', 7000.00, 'Minuman', 'NULL'),
(36, 2, 'Jus Melon', 7000.00, 'Minuman', 'NULL'),
(37, 2, 'Capcin', 7000.00, 'Minuman', 'NULL'),
(38, 2, 'Milo', 7000.00, 'Minuman', 'NULL'),
(39, 2, 'Jus Durian', 14000.00, 'Minuman', 'NULL'),
(40, 2, 'Pop Ice', 6000.00, 'Minuman', 'NULL'),
(41, 2, 'Jus Alpukat + Dancow', 18000.00, 'Minuman', 'NULL'),
(42, 2, 'Jus Buah Naga + Pisang', 18000.00, 'Minuman', 'NULL'),
(43, 2, 'Jus Strawberry + Pisang', 18000.00, 'Minuman', 'NULL'),
(44, 2, 'Jus Strawberry + Dancow', 18000.00, 'Minuman', 'NULL'),
(45, 2, 'Jus Pisang + Dancow', 14000.00, 'Minuman', 'NULL'),
(46, 2, 'Jus Pisang + Oreo', 11000.00, 'Minuman', 'NULL'),
(47, 2, 'Jus Tomat + Wortel', 11000.00, 'Minuman', 'NULL'),
(48, 2, 'Jus Alpukat + Oreo', 14000.00, 'Minuman', 'NULL'),
(49, 3, 'Lumpia Basah', 10000.00, 'Makanan', 'Bandung, Jawa Barat'),
(50, 4, 'Batagor 1 pcs', 1000.00, 'Makanan', 'Bandung, Jawa Barat'),
(51, 5, 'Jeyuk (Kecil)', 7000.00, 'Minuman', 'NULL'),
(52, 5, 'Jeyuk (Oyi)', 10000.00, 'Minuman', 'NULL'),
(53, 5, 'Jeyuk (Muyni)', 15000.00, 'Minuman', 'NULL'),
(54, 5, 'Sunkist (Oyi)', 13000.00, 'Minuman', 'NULL'),
(55, 5, 'Sunkist (Muyni)', 24000.00, 'Minuman', 'NULL'),
(56, 5, 'Lemon (Oyi)', 10000.00, 'Minuman', 'NULL'),
(57, 5, 'Yakult', 3000.00, 'Topping', 'NULL'),
(58, 5, 'Madu', 3000.00, 'Topping', 'NULL'),
(59, 5, 'Coco', 3000.00, 'Topping', 'NULL'),
(60, 5, 'Soda', 3000.00, 'Topping', 'NULL'),
(61, 6, 'Es Teh Jumbo', 4500.00, 'Minuman', 'NULL'),
(62, 6, 'Es Teh Kampul', 6000.00, 'Minuman', 'NULL'),
(63, 6, 'Es Sirup Frambos', 8000.00, 'Minuman', 'NULL'),
(64, 6, 'Es Sirup Melon', 8000.00, 'Minuman', 'NULL'),
(65, 6, 'Es Teh Green Tea', 8000.00, 'Minuman', 'NULL'),
(66, 6, 'Es Teh Thai Tea', 8000.00, 'Minuman', 'NULL'),
(67, 6, 'Es Teh Milk Tea', 8000.00, 'Minuman', 'NULL'),
(68, 6, 'Es Teh Coklat', 8000.00, 'Minuman', 'NULL'),
(69, 6, 'Es Teh Cappuccino', 8000.00, 'Minuman', 'NULL'),
(70, 6, 'Es Teh Red Velvet', 8000.00, 'Minuman', 'NULL'),
(71, 6, 'Es Teh Strawberry', 8000.00, 'Minuman', 'NULL'),
(72, 6, 'Es Teh Leci', 8000.00, 'Minuman', 'NULL'),
(73, 6, 'Es Teh Taro', 8000.00, 'Minuman', 'NULL'),
(74, 6, 'Es Teh Blue Velvet', 8000.00, 'Minuman', 'NULL'),
(75, 7, 'Mie Ayam Bakso', 10000.00, 'Makanan', 'Wonogiri, Jawa Tengah'),
(76, 7, 'Mie Ayam Pangsit', 15000.00, 'Makanan', 'Wonogiri, Jawa Tengah'),
(77, 8, 'Jasuke', 5000.00, 'Makanan', 'NULL'),
(78, 9, 'Otak-otak (4 pcs)', 5000.00, 'Makanan', 'NULL'),
(79, 9, 'Tempe Crispy (4 pcs)', 5000.00, 'Makanan', NULL),
(80, 9, 'Risol (4 Pcs)', 5000.00, 'Makanan', 'NULL'),
(81, 9, 'Comro (4 Pcs)', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(82, 9, 'Tahu Crispy (4 Pcs)', 5000.00, 'Makanan', 'NULL'),
(83, 10, 'Martabak Telur Mini (3 Kulit Lumpia)', 5000.00, 'Makanan', 'NULL'),
(84, 10, 'Martabak Telur Mini (4 Kulit Lumpia)', 7000.00, 'Makanan', 'NULL'),
(85, 10, 'Martabak Telur Mini (5 Kulit Lumpia)', 10000.00, 'Makanan', 'NULL'),
(86, 11, 'Kupat Tahu', 10000.00, 'Makanan', 'Bandung, Jawa Barat'),
(87, 11, 'Kupat Tahu Kuah Kari', 12000.00, 'Makanan', 'Bandung, Jawa Barat'),
(88, 11, 'Gado-gado', 15000.00, 'Makanan', 'Jakarta, DKI Jakarta '),
(89, 11, 'Gado-gado Tanpa Lontong', 15000.00, 'Makanan', 'Jakarta, DKI Jakarta'),
(90, 11, 'Nasi Lengko', 12000.00, 'Makanan', 'Cirebon, Jawa Barat'),
(91, 11, 'Nasi Kari Sapi', 14000.00, 'Makanan', 'Bandung, Jawa Barat'),
(92, 11, 'Nasi Gado-gado', 15000.00, 'Makanan', 'Jakarta, DKI Jakarta'),
(93, 11, 'Lontong Kari Sapi + Telur Setengah', 14000.00, 'Makanan', 'Bandung, Jawa Barat'),
(94, 11, 'Lontong Kari Sapi + Telur Setengah', 16000.00, 'Makanan', 'Bandung, Jawa Barat'),
(95, 11, 'Telur Dadar/Ceplok/Rebus', 4000.00, 'Makanan', NULL),
(96, 12, 'Surabi Gula Kinca (Kuah)', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(97, 12, 'Surabi Coklat', 4000.00, 'Makanan', 'Bandung, Jawa Barat'),
(98, 12, 'Surabi Coklat Susu', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(99, 12, 'Surabi Keju Susu', 8000.00, 'Makanan', 'Bandung, Jawa Barat'),
(100, 12, 'Surabi Keju Spesial (Extra Keju)', 11000.00, 'Makanan', 'Bandung, Jawa Barat'),
(101, 12, 'Surabi Coklat Keju Susu', 9000.00, 'Makanan', 'Bandung, Jawa Barat'),
(102, 12, 'Surabi Pisang Coklat', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(103, 12, 'Surabi Pisang Coklat Susu', 9000.00, 'Makanan', 'Bandung, Jawa Barat'),
(104, 12, 'Surabi Pisang Susu', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(105, 12, 'Surabi Pisang Coklat Keju Susu', 10000.00, 'Makanan', 'Bandung, Jawa Barat'),
(106, 12, 'Surabi Blueberry Susu', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(107, 12, 'Surabi Blueberry Coklat Susu', 6000.00, 'Makanan', 'Bandung, Jawa Barat'),
(108, 12, 'Surabi Blueberry Keju Susu', 9000.00, 'Makanan', 'Bandung, Jawa Barat'),
(109, 12, 'Surabi Kacang Susu', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(110, 12, 'Surabi Kacang Coklat', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(111, 12, 'Surabi Kacang Coklat Susu', 6000.00, 'Makanan', 'Bandung, Jawa Barat'),
(112, 12, 'Surabi Kacang Keju Susu', 9000.00, 'Makanan', 'Bandung, Jawa Barat'),
(113, 12, 'Surabi Kacang Coklat Keju Susu', 10000.00, 'Makanan', 'Bandung, Jawa Barat'),
(114, 12, 'Surabi Oreo Susu', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(115, 12, 'Surabi Oreo Keju Susu', 9000.00, 'Makanan', 'Bandung, Jawa Barat'),
(116, 12, 'Surabi Oreo Pisang Susu', 6000.00, 'Makanan', 'Bandung, Jawa Barat'),
(117, 12, 'Surabi Oreo Pisang Keju Susu', 10000.00, 'Makanan', 'Bandung, Jawa Barat'),
(118, 12, 'Surabi Oreo Coklat', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(119, 12, 'Surabi Oreo Coklat Susu', 6000.00, 'Makanan', 'Bandung, Jawa Barat'),
(120, 12, 'Surabi Oreo Coklat Keju Susu', 10000.00, 'Makanan', 'Bandung, Jawa Barat'),
(121, 12, 'Surabi Polos (Tanpa Kuah)', 3000.00, 'Makanan', 'Bandung, Jawa Barat'),
(122, 12, 'Surabi Oncom', 4000.00, 'Makanan', 'Bandung, Jawa Barat'),
(123, 12, 'Surabi Oncom Rawit', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(124, 12, 'Surabi Oncom Ayam', 6000.00, 'Makanan', 'Bandung, Jawa Barat'),
(125, 12, 'Surabi Oncom Sosis', 6000.00, 'Makanan', 'Bandung, Jawa Barat'),
(126, 12, 'Surabi Oncom Telur Mayo', 8000.00, 'Makanan', 'Bandung, Jawa Barat'),
(127, 12, 'Surabi Oncom Telur Mayo Rawit', 9000.00, 'Makanan', 'Bandung, Jawa Barat'),
(128, 12, 'Surabi Oncom Telur Ayam Mayo', 10000.00, 'Makanan', 'Bandung, Jawa Barat'),
(129, 12, 'Surabi Oncom Telur Sosis Mayo', 10000.00, 'Makanan', 'Bandung, Jawa Barat'),
(130, 12, 'Surabi Ayam', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(131, 12, 'Surabi Ayam Sosis', 7000.00, 'Makanan', 'Bandung, Jawa Barat'),
(132, 12, 'Surabi Sosis', 5000.00, 'Makanan', 'Bandung, Jawa Barat'),
(133, 12, 'Surabi Sosis Keju', 9000.00, 'Makanan', 'Bandung, Jawa Barat'),
(134, 12, 'Surabi Telur Mayo', 7000.00, 'Makanan', 'Bandung, Jawa Barat'),
(135, 12, 'Surabi Telur Keju Mayo', 11000.00, 'Makanan', 'Bandung, Jawa Barat'),
(136, 12, 'Surabi Telur Sosis Mayo', 9000.00, 'Makanan', 'Bandung, Jawa Barat'),
(137, 12, 'Surabi Ayam Telur Mayo', 9000.00, 'Makanan', 'Bandung, Jawa Barat'),
(138, 12, 'Surabi Oncom Telur Mayo Keju Dadu', 12000.00, 'Makanan', 'Bandung, Jawa Barat'),
(139, 12, 'Surabi Abon', 6000.00, 'Makanan', 'Bandung, Jawa Barat'),
(140, 12, 'Surabi Abon Oncom', 7000.00, 'Makanan', 'Bandung, Jawa Barat'),
(141, 12, 'Surabi Abon Telur Mayo', 10000.00, 'Makanan', 'Bandung, Jawa Barat'),
(142, 12, 'Surabi Abon Oncom Telur Mayo', 11000.00, 'Makanan', 'Bandung, Jawa Barat'),
(143, 12, 'Extra Keju', 4000.00, 'Topping', NULL),
(144, 12, 'Extra Rawit', 1000.00, 'Topping', NULL),
(145, 12, 'Extra Mayo & Sambal', 1000.00, 'Topping', NULL),
(146, 13, 'Ayam Katsu Original', 12000.00, 'Makanan', NULL),
(147, 13, 'Ayam Katsu Sambal Geprek', 12000.00, 'Makanan', NULL),
(148, 13, 'Ayam Katsu Lada Hitam', 12000.00, 'Makanan', NULL),
(149, 13, 'Ayam Katsu Saus Kari', 12000.00, 'Makanan', NULL),
(150, 13, 'Ayam Katsu Teriyaki', 15000.00, 'Makanan', NULL),
(151, 13, 'Ayam Katsu Saus Keju', 15000.00, 'Makanan', NULL),
(152, 13, 'Ayam Katsu BBQ', 15000.00, 'Makanan', NULL),
(153, 13, 'Ayam Katsu Mozzarella', 20000.00, 'Makanan', NULL),
(154, 13, 'Katsu Nasi Daun Jeruk', 15000.00, 'Makanan', NULL),
(155, 13, 'Katsu Nasi Goreng', 15000.00, 'Makanan', NULL),
(156, 13, 'Katsu Nasi Uduk', 15000.00, 'Makanan', NULL),
(157, 13, 'Katsu Nasi Nori', 15000.00, 'Makanan', NULL),
(158, 13, 'Nasi Ayam Geprek Sambal Merah', 11000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(159, 13, 'Nasi Ayam Geprek Sambal Ijo', 11000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(160, 13, 'Nasi Ayam Geprek Lada Hitam', 12000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(161, 13, 'Nasi Ayam Geprek Saus Kari', 12000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(162, 13, 'Nasi Ayam Geprek Saus Keju', 13000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(163, 13, 'Nasi Ayam Geprek BBQ', 13000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(164, 13, 'Nasi Ayam Geprek Mozzarella', 16000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(165, 13, 'Ayam Geprek Nasi Daun Jeruk', 13000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(166, 13, 'Ayam Geprek Nasi Uduk', 13000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(167, 13, 'Ayam Geprek Nasi Nori', 13000.00, 'Makanan', 'Yogyakarta, DI Yogyakarta'),
(168, 13, 'Nasi Krikil Original', 12000.00, 'Makanan', NULL),
(169, 13, 'Nasi Krikil Sambal Geprek', 12000.00, 'Makanan', NULL),
(170, 13, 'Nasi Krikil Lada Hitam', 12000.00, 'Makanan', NULL),
(171, 13, 'Nasi Krikil Saus Keju', 12000.00, 'Makanan', NULL),
(172, 13, 'Nasi Krikil Saus Kari', 12000.00, 'Makanan', NULL),
(173, 13, 'Sayap Pelakor', 12000.00, 'Makanan', NULL),
(174, 13, 'Ayam Krikil Nasi Daun Jeruk', 15000.00, 'Makanan', NULL),
(175, 13, 'Ayam Krikil Nasi Goreng', 17000.00, 'Makanan', NULL),
(176, 13, 'Ayam Krikil Nasi Uduk', 15000.00, 'Makanan', NULL),
(177, 13, 'Nasi Nori Ayam Krikil', 15000.00, 'Makanan', NULL),
(178, 13, 'Kentang Bumbu Keju', 10000.00, 'Makanan', NULL),
(179, 13, 'Kentang Bumbu BBQ', 10000.00, 'Makanan', NULL),
(180, 13, 'Kentang Bumbu Balado', 10000.00, 'Makanan', NULL),
(181, 13, 'Kentang Bumbu Sapi Lada Hitam', 10000.00, 'Makanan', NULL),
(182, 13, 'Kentang Bumbu Rumput Laut', 10000.00, 'Makanan', NULL),
(183, 13, 'Es Teh', 3000.00, 'Minuman', NULL),
(184, 13, 'Teh Hangat', 3000.00, 'Minuman', NULL),
(185, 13, 'Es Jeruk', 7000.00, 'Minuman', NULL),
(186, 13, 'Jeruk Hangat', 5000.00, 'Minuman', NULL),
(187, 14, 'Nasi Telur Dadar', 12500.00, 'Makanan', NULL),
(188, 14, 'Nasi Ayam Bakar', 17500.00, 'Makanan', 'Padang, Sumatera Barat'),
(189, 14, 'Nasi Ayam Gulai', 17500.00, 'Makanan', 'Padang, Sumatera Barat'),
(190, 14, 'Nasi Gulai Kikil', 22500.00, 'Makanan', 'Padang, Sumatera Barat'),
(191, 14, 'Nasi Gulai Cincang', 22500.00, 'Makanan', 'Padang, Sumatera Barat'),
(192, 14, 'Nasi Rendang', 22500.00, 'Makanan', 'Padang, Sumatera Barat'),
(193, 14, 'Nasi Ayam Balado', 17500.00, 'Makanan', 'Padang, Sumatera Barat'),
(194, 14, 'Nasi Ayam Goreng', 17500.00, 'Makanan', NULL),
(195, 14, 'Nasi Ikan Kembung Goreng', 15000.00, 'Makanan', NULL),
(196, 14, 'Nasi Telur Bulat Balado', 12500.00, 'Makanan', 'Padang, Sumatera Barat'),
(197, 14, 'Nasi Ikan Bawal Bakar', 15000.00, 'Makanan', NULL),
(198, 14, 'Nasi Ikan Nila', 15000.00, 'Makanan', NULL),
(199, 14, 'Nasi Gulai Kakap', 31500.00, 'Makanan', 'Padang, Sumatera Barat'),
(200, 14, 'Nasi Gulai Cumi', 22500.00, 'Makanan', 'Padang, Sumatera Barat'),
(201, 14, 'Nasi Gulai Limpa', 22500.00, 'Makanan', 'Padang, Sumatera Barat'),
(202, 14, 'Nasi Gulai Usus/Tambusu', 22500.00, 'Makanan', 'Padang, Sumatera Barat'),
(203, 14, 'Nasi Gulai Babat', 22500.00, 'Makanan', 'Padang, Sumatera Barat'),
(204, 14, 'Nasi Paru Goreng', 22500.00, 'Makanan', NULL),
(205, 14, 'Nasi Udang Balado', 15000.00, 'Makanan', 'Padang, Sumatera Barat'),
(206, 14, 'Nasi Ikan Lele Goreng', 15000.00, 'Makanan', NULL),
(207, 14, 'Nasi Ikan Kembung Bakar', 15000.00, 'Makanan', NULL),
(208, 14, 'Nasi Ikan Tongkol Balado', 15000.00, 'Makanan', 'Padang, Sumatera Barat'),
(209, 14, 'Gulai Cincang', 16250.00, 'Makanan', 'Padang, Sumatera Barat'),
(210, 14, 'Gulai Kikil', 16250.00, 'Makanan', 'Padang, Sumatera Barat'),
(211, 14, 'Rendang', 16250.00, 'Makanan', 'Padang, Sumatera Barat'),
(212, 14, 'Telur Bulat Balado', 6250.00, 'Makanan', 'Padang, Sumatera Barat'),
(213, 14, 'Gulai Ikan Kakap', 25000.00, 'Makanan', 'Padang, Sumatera Barat'),
(214, 14, 'Udang Balado', 11250.00, 'Makanan', 'Padang, Sumatera Barat'),
(215, 14, 'Ikan Tongkol Balado', 11250.00, 'Makanan', 'Padang, Sumatera Barat'),
(216, 14, 'Ikan Lele Goreng', 11250.00, 'Makanan', NULL),
(217, 14, 'Ikan Kembung Balado', 11250.00, 'Makanan', 'Padang, Sumatera Barat'),
(218, 14, 'Ikan Kembung Bakar', 11250.00, 'Makanan', NULL),
(219, 14, 'Ayam Balado', 12500.00, 'Makanan', 'Padang, Sumatera Barat'),
(220, 14, 'Ayam Goreng', 12500.00, 'Makanan', NULL),
(221, 14, 'Ayam Bakar', 12500.00, 'Makanan', 'Padang, Sumatera Barat'),
(222, 14, 'Ayam Gulai', 12500.00, 'Makanan', 'Padang, Sumatera Barat'),
(223, 14, 'Telur Dadar', 6250.00, 'Makanan', NULL),
(224, 14, 'Perkedel Kentang', 2500.00, 'Makanan', 'Padang, Sumatera Barat'),
(225, 14, 'Tempe Goreng', 1250.00, 'Makanan', NULL),
(226, 14, 'Tahu Goreng', 1250.00, 'Makanan', NULL),
(227, 14, 'Terong Balado', 2500.00, 'Makanan', 'Padang, Sumatera Barat'),
(228, 14, 'Gulai Cumi', 16250.00, 'Makanan', 'Padang, Sumatera Barat'),
(229, 14, 'Gulai Usus/Tamusu', 16250.00, 'Makanan', 'Padang, Sumatera Barat'),
(230, 14, 'Gulai Babat', 16250.00, 'Makanan', 'Padang, Sumatera Barat'),
(231, 14, 'Paru Goreng', 16250.00, 'Makanan', NULL),
(232, 14, 'Gulai Limpa', 16250.00, 'Makanan', 'Padang, Sumatera Barat'),
(233, 14, 'Sambal Ijo', 6250.00, 'Topping', 'Padang, Sumatera Barat'),
(234, 14, 'Sambal Merah', 6250.00, 'Topping', 'Padang, Sumatera Barat'),
(235, 15, 'Es Kelapa', 5000.00, 'Minuman', NULL),
(236, 16, 'Es Kelapa Muda', 6000.00, 'Minuman', NULL),
(237, 16, 'Es Kelapa Marjan', 8000.00, 'Minuman', NULL),
(238, 17, 'Dumpling Keju Goreng', 2000.00, 'Makanan', NULL),
(239, 17, 'Dumpling Ayam Goreng', 2000.00, 'Makanan', NULL),
(240, 17, 'Fish Roll Goreng', 2000.00, 'Makanan', NULL),
(241, 17, 'Fish Ball Goreng', 2000.00, 'Makanan', NULL),
(242, 17, 'Chikuwa Goreng', 2000.00, 'Makanan', NULL),
(243, 17, 'Crab Stick Goreng', 2000.00, 'Makanan', NULL),
(244, 17, 'Sosis Kecil Goreng', 2000.00, 'Makanan', NULL),
(245, 17, 'Sosis Jumbo Goreng', 5000.00, 'Makanan', NULL),
(246, 18, 'Es Pisang Ijo Original', 5000.00, 'Minuman', 'Makassar, Sulawesi Selatan'),
(247, 18, 'Es Pisang Ijo Medium', 7000.00, 'Minuman', 'Makassar, Sulawesi Selatan'),
(248, 18, 'Es Pisang Ijo Large', 8000.00, 'Minuman', 'Makassar, Sulawesi Selatan'),
(249, 18, 'Es Pisang Ijo Special', 10000.00, 'Minuman', 'Makassar, Sulawesi Selatan'),
(250, 19, 'Martabak Mini Keju', 2000.00, 'Makanan', 'Bangka, Kepulauan Bangka Belitung'),
(251, 19, 'Martabak Mini Coklat', 2000.00, 'Makanan', 'Bangka, Kepulauan Bangka Belitung'),
(252, 19, 'Martabak Mini Strawberry', 2000.00, 'Makanan', 'Bangka, Kepulauan Bangka Belitung'),
(253, 19, 'Martabak Mini Pisang', 2000.00, 'Makanan', 'Bangka, Kepulauan Bangka Belitung'),
(254, 19, 'Martabak Mini Kacang', 2000.00, 'Makanan', 'Bangka, Kepulauan Bangka Belitung'),
(255, 20, 'Soto Ayam dan Nasi (1 Porsi)', 12000.00, 'Makanan', 'Madura, Jawa Timur'),
(256, 20, 'Soto Ayam dan Nasi (Setengah Porsi)', 10000.00, 'Makanan', 'Madura, Jawa Timur'),
(257, 20, 'Soto Ayam (1 Porsi)', 9000.00, 'Makanan', 'Madura, Jawa Timur'),
(258, 20, 'Soto Ayam (Setengah Porsi)', 7000.00, 'Makanan', 'Madura, Jawa Timur'),
(259, 21, 'Bubur Polos (1 Porsi)', 8000.00, 'Makanan', 'Bandung, Jawa Barat'),
(260, 21, 'Bubur Ayam Biasa (1 Porsi)', 13000.00, 'Makanan', 'Bandung, Jawa Barat'),
(261, 21, 'Bubur Ayam Ati Ampela (1 Porsi)', 18000.00, 'Makanan', 'Bandung, Jawa Barat'),
(262, 21, 'Bubur Ayam Telur (1 Porsi)', 18000.00, 'Makanan', 'Bandung, Jawa Barat'),
(263, 21, 'Bubur Ayam Telur Ati Ampela (1 Porsi)', 23000.00, 'Makanan', 'Bandung, Jawa Barat'),
(264, 21, 'Bubur Ayam Biasa (Setengah Porsi)', 11000.00, 'Makanan', 'Bandung, Jawa Barat'),
(265, 21, 'Bubur Ayam Ati Ampela (Setengah Porsi)', 16000.00, 'Makanan', 'Bandung, Jawa Barat'),
(266, 21, 'Bubur Ayam Telur (Setengah Porsi)', 16000.00, 'Makanan', 'Bandung, Jawa Barat'),
(267, 21, 'Bubur Ayam Telur Ati Ampela (Setengah Porsi)', 22000.00, 'Makanan', 'Bandung, Jawa Barat');

--
-- Trigger `produk`
--
DELIMITER $$
CREATE TRIGGER `tgr_cek_harga_produk` BEFORE INSERT ON `produk` FOR EACH ROW BEGIN
    IF NEW.harga <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Gagal: Harga produk street food tidak boleh 0 atau minus!';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk_rasa`
--

CREATE TABLE `produk_rasa` (
  `id_produk_rasa` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_rasa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk_rasa`
--

INSERT INTO `produk_rasa` (`id_produk_rasa`, `id_produk`, `id_rasa`) VALUES
(1, 1, 1),
(2, 1, 2),
(4, 1, 4),
(5, 2, 1),
(6, 2, 2),
(8, 2, 4),
(9, 3, 1),
(10, 3, 2),
(12, 3, 4),
(13, 4, 1),
(14, 4, 2),
(16, 4, 4),
(17, 5, 1),
(18, 5, 2),
(20, 5, 4),
(21, 6, 1),
(22, 6, 2),
(24, 6, 4),
(25, 7, 1),
(26, 7, 2),
(28, 7, 4),
(29, 8, 1),
(30, 8, 2),
(32, 8, 4),
(33, 9, 1),
(34, 9, 2),
(36, 9, 4),
(37, 10, 1),
(38, 10, 2),
(40, 10, 4),
(41, 11, 1),
(42, 11, 2),
(44, 11, 4),
(45, 12, 1),
(46, 12, 2),
(48, 12, 4),
(49, 13, 1),
(50, 13, 2),
(52, 13, 4),
(53, 14, 1),
(54, 14, 2),
(56, 14, 4),
(57, 15, 3),
(58, 16, 3),
(59, 17, 5),
(60, 18, 3),
(61, 18, 5),
(62, 19, 3),
(63, 20, 3),
(64, 21, 3),
(65, 21, 5),
(66, 22, 3),
(67, 22, 5),
(68, 23, 3),
(69, 23, 5),
(70, 24, 3),
(71, 25, 5),
(72, 26, 3),
(73, 27, 3),
(74, 28, 3),
(75, 29, 3),
(76, 29, 5),
(77, 30, 3),
(78, 31, 3),
(79, 32, 3),
(80, 33, 3),
(81, 33, 5),
(82, 34, 5),
(83, 35, 3),
(84, 36, 3),
(85, 37, 3),
(86, 38, 3),
(87, 39, 3),
(88, 40, 3),
(89, 41, 3),
(90, 42, 3),
(91, 43, 3),
(92, 43, 5),
(93, 44, 3),
(94, 44, 5),
(95, 45, 3),
(96, 46, 3),
(97, 47, 3),
(98, 47, 5),
(99, 48, 3),
(100, 49, 1),
(101, 49, 2),
(102, 49, 3),
(103, 49, 4),
(104, 50, 1),
(105, 50, 2),
(106, 50, 3),
(107, 50, 4),
(108, 51, 3),
(109, 51, 5),
(110, 52, 3),
(111, 52, 5),
(112, 53, 3),
(113, 53, 5),
(114, 54, 3),
(115, 54, 5),
(116, 55, 3),
(117, 55, 5),
(118, 56, 5),
(119, 57, 3),
(120, 57, 5),
(121, 58, 3),
(122, 59, 3),
(123, 60, 3),
(124, 61, 3),
(125, 62, 3),
(126, 63, 3),
(127, 64, 3),
(128, 65, 3),
(129, 66, 3),
(130, 67, 3),
(131, 68, 3),
(132, 69, 3),
(133, 70, 3),
(134, 71, 3),
(135, 72, 3),
(136, 73, 3),
(137, 74, 3),
(138, 75, 1),
(139, 75, 2),
(140, 75, 4),
(141, 76, 1),
(142, 76, 2),
(143, 76, 4),
(144, 77, 3),
(145, 78, 1),
(146, 78, 2),
(148, 79, 1),
(149, 79, 2),
(151, 80, 1),
(152, 80, 2),
(155, 81, 1),
(156, 81, 2),
(157, 81, 4),
(158, 82, 1),
(159, 82, 2),
(161, 83, 1),
(162, 83, 2),
(163, 83, 4),
(164, 84, 1),
(165, 84, 2),
(166, 84, 4),
(167, 85, 1),
(168, 85, 2),
(169, 85, 4),
(170, 86, 1),
(171, 86, 2),
(172, 86, 3),
(173, 86, 4),
(174, 87, 1),
(175, 87, 2),
(176, 87, 3),
(177, 87, 4),
(178, 88, 1),
(179, 88, 2),
(180, 88, 3),
(181, 88, 4),
(182, 89, 1),
(183, 89, 2),
(184, 89, 3),
(185, 89, 4),
(186, 90, 2),
(187, 90, 3),
(188, 90, 4),
(189, 91, 1),
(190, 91, 2),
(191, 91, 4),
(192, 92, 1),
(193, 92, 2),
(194, 92, 3),
(195, 92, 4),
(196, 93, 1),
(197, 93, 2),
(198, 93, 4),
(199, 94, 1),
(200, 94, 2),
(201, 94, 4),
(202, 95, 1),
(203, 95, 2),
(204, 95, 4),
(205, 96, 3),
(206, 97, 3),
(207, 98, 3),
(208, 99, 3),
(209, 100, 3),
(210, 101, 3),
(211, 102, 3),
(212, 103, 3),
(213, 104, 3),
(214, 105, 3),
(215, 106, 3),
(216, 107, 3),
(217, 108, 3),
(218, 109, 3),
(219, 110, 3),
(220, 111, 3),
(221, 112, 3),
(222, 113, 3),
(223, 114, 3),
(224, 115, 3),
(225, 116, 3),
(226, 117, 3),
(227, 118, 3),
(228, 119, 3),
(229, 120, 3),
(230, 121, 3),
(231, 122, 1),
(232, 122, 2),
(233, 123, 1),
(234, 123, 2),
(235, 123, 4),
(236, 124, 1),
(237, 124, 2),
(238, 124, 4),
(239, 125, 1),
(240, 125, 2),
(241, 125, 4),
(242, 126, 1),
(243, 126, 2),
(244, 127, 1),
(245, 127, 2),
(246, 127, 4),
(247, 128, 1),
(248, 128, 2),
(249, 128, 4),
(250, 129, 1),
(251, 129, 2),
(252, 129, 4),
(253, 130, 1),
(254, 130, 2),
(255, 130, 4),
(256, 131, 1),
(257, 131, 2),
(258, 131, 4),
(259, 132, 1),
(260, 132, 2),
(261, 132, 4),
(262, 133, 1),
(263, 133, 2),
(264, 133, 4),
(265, 134, 1),
(266, 134, 2),
(267, 134, 4),
(268, 135, 1),
(269, 135, 2),
(270, 135, 4),
(271, 136, 1),
(272, 136, 2),
(273, 136, 4),
(274, 137, 1),
(275, 137, 2),
(276, 137, 4),
(277, 138, 1),
(278, 138, 2),
(279, 138, 4),
(280, 139, 1),
(281, 139, 2),
(282, 139, 4),
(283, 140, 1),
(284, 140, 2),
(285, 140, 4),
(286, 141, 1),
(287, 141, 2),
(288, 141, 4),
(289, 142, 1),
(290, 142, 2),
(291, 142, 4),
(292, 143, 1),
(293, 143, 2),
(294, 144, 4),
(295, 145, 1),
(296, 145, 2),
(297, 145, 4),
(298, 146, 1),
(299, 146, 2),
(300, 147, 1),
(301, 147, 2),
(302, 147, 4),
(303, 148, 1),
(304, 148, 2),
(305, 149, 1),
(306, 149, 2),
(307, 149, 4),
(308, 150, 1),
(309, 150, 2),
(310, 150, 3),
(311, 151, 1),
(312, 151, 2),
(313, 152, 1),
(314, 152, 2),
(315, 152, 3),
(316, 153, 1),
(317, 153, 2),
(318, 154, 1),
(319, 154, 2),
(320, 155, 1),
(321, 155, 2),
(322, 156, 1),
(323, 156, 2),
(324, 157, 1),
(325, 157, 2),
(326, 158, 1),
(327, 158, 2),
(328, 158, 4),
(329, 159, 1),
(330, 159, 2),
(331, 159, 4),
(332, 160, 1),
(333, 160, 2),
(334, 160, 4),
(335, 161, 1),
(336, 161, 2),
(337, 162, 1),
(338, 162, 2),
(339, 163, 1),
(340, 163, 2),
(341, 163, 3),
(342, 164, 1),
(343, 164, 2),
(344, 165, 1),
(345, 165, 2),
(346, 166, 1),
(347, 166, 2),
(348, 167, 1),
(349, 167, 2),
(350, 168, 1),
(351, 168, 2),
(352, 169, 1),
(353, 169, 2),
(354, 169, 4),
(355, 170, 1),
(356, 170, 2),
(357, 170, 4),
(358, 171, 1),
(359, 171, 2),
(360, 172, 1),
(361, 172, 2),
(362, 173, 1),
(363, 173, 2),
(364, 173, 4),
(365, 174, 1),
(366, 174, 2),
(367, 175, 1),
(368, 175, 2),
(369, 176, 1),
(370, 176, 2),
(371, 177, 1),
(372, 177, 2),
(373, 178, 1),
(374, 178, 2),
(375, 179, 1),
(376, 179, 2),
(377, 180, 1),
(378, 180, 2),
(379, 180, 4),
(380, 181, 1),
(381, 181, 2),
(382, 181, 4),
(383, 182, 1),
(384, 182, 2),
(385, 183, 3),
(386, 184, 3),
(387, 185, 3),
(388, 185, 5),
(389, 186, 3),
(390, 186, 5),
(391, 187, 1),
(392, 187, 2),
(393, 187, 4),
(394, 188, 1),
(395, 188, 2),
(396, 188, 4),
(397, 189, 1),
(398, 189, 2),
(399, 189, 4),
(400, 190, 1),
(401, 190, 2),
(402, 190, 4),
(403, 191, 1),
(404, 191, 2),
(405, 191, 4),
(406, 192, 1),
(407, 192, 2),
(408, 192, 4),
(409, 193, 1),
(410, 193, 2),
(411, 193, 4),
(412, 194, 1),
(413, 194, 2),
(414, 195, 1),
(415, 195, 2),
(416, 196, 1),
(417, 196, 2),
(418, 196, 4),
(419, 197, 1),
(420, 197, 2),
(421, 198, 1),
(422, 198, 2),
(423, 199, 1),
(424, 199, 2),
(425, 200, 1),
(426, 200, 2),
(427, 201, 1),
(428, 201, 2),
(429, 202, 1),
(430, 202, 2),
(431, 203, 1),
(432, 203, 2),
(433, 204, 1),
(434, 204, 2),
(435, 205, 1),
(436, 205, 2),
(437, 205, 4),
(438, 206, 1),
(439, 206, 2),
(440, 207, 1),
(441, 207, 2),
(442, 208, 1),
(443, 208, 2),
(444, 208, 4),
(445, 209, 1),
(446, 209, 2),
(447, 210, 1),
(448, 210, 2),
(449, 211, 1),
(450, 211, 2),
(451, 211, 3),
(452, 211, 4),
(453, 212, 1),
(454, 212, 2),
(455, 212, 4),
(456, 213, 1),
(457, 213, 2),
(458, 214, 1),
(459, 214, 2),
(460, 214, 4),
(461, 215, 1),
(462, 215, 2),
(463, 215, 4),
(464, 216, 1),
(465, 216, 2),
(466, 217, 1),
(467, 217, 2),
(468, 217, 4),
(469, 218, 1),
(470, 218, 2),
(471, 219, 1),
(472, 219, 2),
(473, 219, 4),
(474, 220, 1),
(475, 220, 2),
(476, 221, 1),
(477, 221, 2),
(478, 222, 1),
(479, 222, 2),
(480, 223, 1),
(481, 223, 2),
(482, 224, 1),
(483, 224, 2),
(484, 225, 1),
(485, 225, 2),
(486, 226, 1),
(487, 226, 2),
(488, 227, 1),
(489, 227, 2),
(490, 227, 4),
(491, 228, 1),
(492, 228, 2),
(493, 229, 1),
(494, 229, 2),
(495, 230, 1),
(496, 230, 2),
(497, 231, 1),
(498, 231, 2),
(499, 232, 1),
(500, 232, 2),
(501, 233, 1),
(502, 233, 2),
(503, 233, 4),
(504, 234, 1),
(505, 234, 2),
(506, 234, 4),
(507, 235, 3),
(508, 236, 3),
(509, 237, 3),
(510, 238, 1),
(511, 238, 2),
(512, 239, 1),
(513, 239, 2),
(514, 240, 1),
(515, 240, 2),
(516, 241, 1),
(517, 241, 2),
(518, 242, 1),
(519, 242, 2),
(520, 243, 1),
(521, 243, 2),
(522, 244, 1),
(523, 244, 2),
(524, 245, 1),
(525, 245, 2),
(526, 246, 3),
(527, 247, 3),
(528, 248, 3),
(529, 249, 3),
(530, 250, 2),
(531, 250, 3),
(532, 251, 3),
(533, 252, 3),
(534, 253, 3),
(535, 254, 3),
(536, 255, 1),
(537, 255, 2),
(538, 256, 1),
(539, 256, 2),
(540, 257, 1),
(541, 257, 2),
(542, 258, 1),
(543, 258, 2),
(544, 259, 1),
(545, 259, 2),
(546, 260, 1),
(547, 260, 2),
(548, 261, 1),
(549, 261, 2),
(550, 262, 1),
(551, 262, 2),
(552, 263, 1),
(553, 263, 2),
(554, 264, 1),
(555, 264, 2),
(556, 265, 1),
(557, 265, 2),
(558, 266, 1),
(559, 266, 2),
(560, 267, 1),
(561, 267, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `umkm`
--

CREATE TABLE `umkm` (
  `id_umkm` int(11) NOT NULL,
  `nama_umkm` varchar(100) NOT NULL,
  `lokasi` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nomor_kontak` varchar(20) DEFAULT NULL,
  `status_halal` enum('Halal Bersertifikat','Halal Belum Bersertifikat','Non-Halal') NOT NULL,
  `no_sertifikat` varchar(100) DEFAULT NULL,
  `lembaga_penerbit` varchar(100) DEFAULT NULL,
  `tanggal_terbit` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `umkm`
--

INSERT INTO `umkm` (`id_umkm`, `nama_umkm`, `lokasi`, `foto`, `nomor_kontak`, `status_halal`, `no_sertifikat`, `lembaga_penerbit`, `tanggal_terbit`) VALUES
(1, 'Cimol Bojot AA', 'Jl. Ciwaruga No.4, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780967503_Cimol_Bojot_AA.jpg', '6288802073662', 'Halal Bersertifikat', 'ID32110016944470224', 'Badan Penyelenggara Jaminan Produk Halal', '2026-02-24'),
(2, 'Dainisa Fruity Juice', 'Jl. Ciwaruga No.6, Ciwaruga, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973341_Dainisa_Fruity_Juice.jpg', '6281212918316', 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(3, 'Lumpia Basah', 'Jl. Waruga Jaya No. 37, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973363_Lumpia_Basah.jpg', '6281214314332', 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(4, 'Batagor Ikan Kirana', 'Jl. Ciwaruga No.5, Kec. Parongpong, Kabupaten Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973557_Batagor_Ikan_Kirana.jpg', '623875216734', 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(5, 'Mood Boostery-ku', 'Jl. Ciwaruga No. 41, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973580_Mood_Boostery_ku.jpg', '6281121212536', 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(6, 'Es Teh Kampoeng Solo', 'Jl. Ciwaruga No.6, Ciwaruga, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973604_Es_Teh_Kampoeng_Solo.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(7, 'Mie Ayam Baso Pangsit', 'Jl. Waruga Jaya No. 5, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973636_Mie_Ayam_Baso_Pangsit.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(8, 'Jasuke', 'Jl. Waruga Jaya No.5, Ciwaruga, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973648_Jasuke.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(9, 'Tahu Crispy & Otak-Otak Putra Mandiri', 'Jalan Waruga Jaya, Ciwaruga, Kec. Parongpong, Kota Bandung, Jawa Barat', 'FOTO_UMKM/1780973669_Tahu_Crispy___Otak_Otak_Putra_Mandiri.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(10, 'Martel (Martabak Telor)', 'Jl. Ciwaruga No. 37, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973694_Martel__Martabak_Telor_.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(11, 'Kupat Tahu & Gado-Gado Sutami', 'Jl. Waruga Jaya, Ciwaruga, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973734_Kupat_Tahu___Gado_Gado_Sutami.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(12, 'Surabi Bungsu Aneka Rasa', 'Jl. Ciwaruga No.2, Ciwaruga, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973751_Surabi_Bungsu_Aneka_Rasa.jpg', '628997969604', 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(13, 'Ayam Katsu Loka Hita', 'Jl. Ciwaruga No. 2, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973777_Ayam_Katsu_Loka_Hita.jpg', '628218494345', 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(14, 'R.M Padang Delapan Empat', 'Jl. Ciwaruga No. 7, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973799_R_M_Padang_Delapan_Empat.jpg', '6283180886858', 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(15, 'Es Kelapa', 'Jl. Ciwaruga No. 33, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973918_Es_Kelapa.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(16, 'Es Kelapa Febriyan', 'Jl. Ciwaruga No. 46, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973929_Es_Kelapa_Febriyan.jpg', '6281224863542', 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(17, 'Suki Goreng', 'Jl. Waruga Jaya No. 104, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973953_Suki_Goreng.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(18, 'Es Pisang Ijo MG Juned', 'Jl. Waruga Jaya No. 104, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780973965_Es_Pisang_Ijo_MG_Juned.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(19, 'Martabak Mini', 'Jl. Waruga Jaya No. 104, Kec. Parongpong, Kab. Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780974006_Martabak_Mini.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(20, 'Soto Madura Cak Ihwan', 'Jl. Ciwaruga No.2, Kec. Sukasari, Kabupaten Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780974018_Soto_Madura_Cak_Ihwan.jpg', NULL, 'Halal Belum Bersertifikat', NULL, NULL, NULL),
(21, 'Bubur Ayam Putra Pa Sunar', 'Jl. Ciwaruga No.2, Kec. Sukasari, Kabupaten Bandung Barat, Jawa Barat', 'FOTO_UMKM/1780974038_Bubur_Ayam_Putra_Pa_Sunar.jpg', '6283827224517', 'Halal Belum Bersertifikat', NULL, NULL, NULL);

--
-- Trigger `umkm`
--
DELIMITER $$
CREATE TRIGGER `Update_UMKM_Halal` BEFORE UPDATE ON `umkm` FOR EACH ROW BEGIN


    IF NEW.status_halal = 'Halal Bersertifikat' AND (NEW.no_sertifikat IS NULL OR NEW.no_sertifikat = '') THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Gagal Update! UMKM dengan status Halal Bersertifikat wajib memiliki Nomor Sertifikat.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `umkm_mitra`
--

CREATE TABLE `umkm_mitra` (
  `id_umkm_mitra` int(11) NOT NULL,
  `id_umkm` int(11) NOT NULL,
  `id_mitra` int(11) NOT NULL,
  `link_mitra` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `umkm_mitra`
--

INSERT INTO `umkm_mitra` (`id_umkm_mitra`, `id_umkm`, `id_mitra`, `link_mitra`) VALUES
(1, 1, 1, 'https://gofood.link/a/PpPkWYL'),
(2, 1, 2, 'https://r.grab.com/g/6-20260514_215308_1c953104f5554c75b482890a5331265b_MEXMPS-6-C7A3JF41NKVDJT'),
(3, 2, 1, 'https://gofood.link/a/QpegmCh'),
(4, 2, 2, 'https://r.grab.com/g/6-20260529_155218_adc8a8c9a7954d5eb30d4e05caf9db12_MEXMPS-6-C7MYVXX1V6B2NA'),
(5, 11, 1, 'https://gofood.link/u/NZ7DgZ'),
(6, 11, 2, 'https://r.grab.com/g/6-20260519_223149_adc8a8c9a7954d5eb30d4e05caf9db12_MEXMPS-6-CZMTLU2KRLJWG6'),
(7, 12, 1, 'https://gofood.link/a/yM9VHEQ'),
(8, 12, 2, 'https://r.grab.com/g/6-20260519_224202_adc8a8c9a7954d5eb30d4e05caf9db12_MEXMPS-IDGFSTI000029zu'),
(9, 13, 1, 'https://gofood.link/a/JC9Qg5h'),
(10, 13, 3, 'https://shopee.co.id/universal-link/now-food/shop/21369227?deep_and_deferred=1&shareChannel=copy_link'),
(11, 14, 1, 'https://gofood.link/a/S1QdC9u'),
(12, 14, 2, 'https://r.grab.com/g/6-20260519_225407_adc8a8c9a7954d5eb30d4e05caf9db12_MEXMPS-6-C7VGTJJBGRDKTJ');

-- --------------------------------------------------------

--
-- Struktur dari tabel `umkm_pembayaran`
--

CREATE TABLE `umkm_pembayaran` (
  `id_umkm_bayar` int(11) NOT NULL,
  `id_umkm` int(11) NOT NULL,
  `id_metode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `umkm_pembayaran`
--

INSERT INTO `umkm_pembayaran` (`id_umkm_bayar`, `id_umkm`, `id_metode`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 2),
(5, 3, 1),
(6, 4, 1),
(7, 4, 2),
(8, 5, 1),
(9, 5, 2),
(10, 6, 1),
(11, 6, 2),
(12, 7, 1),
(13, 8, 1),
(14, 9, 1),
(15, 10, 1),
(16, 11, 1),
(17, 11, 2),
(18, 12, 1),
(19, 12, 2),
(20, 13, 1),
(21, 13, 2),
(22, 14, 1),
(23, 14, 2),
(24, 15, 1),
(25, 16, 1),
(26, 16, 2),
(27, 17, 1),
(28, 18, 1),
(29, 18, 2),
(30, 19, 1),
(31, 20, 1),
(32, 20, 3),
(33, 21, 1),
(34, 21, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `waktu_operasional`
--

CREATE TABLE `waktu_operasional` (
  `id_waktu` int(11) NOT NULL,
  `id_umkm` int(11) NOT NULL,
  `hari` varchar(50) NOT NULL,
  `jam_buka` time DEFAULT NULL,
  `jam_tutup` time DEFAULT NULL,
  `keterangan` enum('Buka','Tutup') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `waktu_operasional`
--

INSERT INTO `waktu_operasional` (`id_waktu`, `id_umkm`, `hari`, `jam_buka`, `jam_tutup`, `keterangan`) VALUES
(1, 1, 'Senin', '12:00:00', '21:00:00', 'Buka'),
(2, 1, 'Selasa', '12:00:00', '21:00:00', 'Buka'),
(3, 1, 'Rabu', '12:00:00', '21:00:00', 'Buka'),
(4, 1, 'Kamis', '12:00:00', '21:00:00', 'Buka'),
(5, 1, 'Jumat', '12:00:00', '21:00:00', 'Buka'),
(6, 1, 'Sabtu', '12:00:00', '21:00:00', 'Buka'),
(7, 1, 'Minggu', '12:00:00', '21:00:00', 'Buka'),
(8, 2, 'Senin', '09:00:00', '21:00:00', 'Buka'),
(9, 2, 'Selasa', '09:00:00', '21:00:00', 'Buka'),
(10, 2, 'Rabu', '09:00:00', '21:00:00', 'Buka'),
(11, 2, 'Kamis', '09:00:00', '21:00:00', 'Buka'),
(12, 2, 'Jumat', '09:00:00', '21:00:00', 'Buka'),
(13, 2, 'Sabtu', '09:00:00', '21:00:00', 'Buka'),
(14, 2, 'Minggu', '09:00:00', '21:00:00', 'Buka'),
(15, 4, 'Senin', '09:00:00', '21:00:00', 'Buka'),
(16, 4, 'Selasa', '09:00:00', '21:00:00', 'Buka'),
(17, 4, 'Rabu', '09:00:00', '21:00:00', 'Buka'),
(18, 4, 'Kamis', '09:00:00', '21:00:00', 'Buka'),
(19, 4, 'Jumat', '09:00:00', '21:00:00', 'Buka'),
(20, 4, 'Sabtu', '09:00:00', '21:00:00', 'Buka'),
(21, 4, 'Minggu', '09:00:00', '21:00:00', 'Buka'),
(22, 5, 'Senin', '10:00:00', '18:00:00', 'Buka'),
(23, 5, 'Selasa', '10:00:00', '18:00:00', 'Buka'),
(24, 5, 'Rabu', '10:00:00', '18:00:00', 'Buka'),
(25, 5, 'Kamis', '10:00:00', '18:00:00', 'Buka'),
(26, 5, 'Jumat', '10:00:00', '18:00:00', 'Buka'),
(27, 5, 'Sabtu', '10:00:00', '18:00:00', 'Buka'),
(28, 5, 'Minggu', '10:00:00', '18:00:00', 'Buka'),
(29, 6, 'Senin', '10:00:00', '22:00:00', 'Buka'),
(30, 6, 'Selasa', '10:00:00', '22:00:00', 'Buka'),
(31, 6, 'Rabu', '10:00:00', '22:00:00', 'Buka'),
(32, 6, 'Kamis', '10:00:00', '22:00:00', 'Buka'),
(33, 6, 'Jumat', '10:00:00', '22:00:00', 'Buka'),
(34, 6, 'Sabtu', '10:00:00', '22:00:00', 'Buka'),
(35, 6, 'Minggu', '10:00:00', '22:00:00', 'Buka'),
(36, 7, 'Senin', '10:00:00', '20:00:00', 'Buka'),
(37, 7, 'Selasa', '10:00:00', '20:00:00', 'Buka'),
(38, 7, 'Rabu', '10:00:00', '20:00:00', 'Buka'),
(39, 7, 'Kamis', '10:00:00', '20:00:00', 'Buka'),
(40, 7, 'Jumat', '10:00:00', '20:00:00', 'Buka'),
(41, 7, 'Sabtu', '10:00:00', '20:00:00', 'Buka'),
(42, 7, 'Minggu', '10:00:00', '20:00:00', 'Buka'),
(43, 8, 'Senin', '08:00:00', '21:00:00', 'Buka'),
(44, 8, 'Selasa', '08:00:00', '21:00:00', 'Buka'),
(45, 8, 'Rabu', '08:00:00', '21:00:00', 'Buka'),
(46, 8, 'Kamis', '08:00:00', '21:00:00', 'Buka'),
(47, 8, 'Jumat', '08:00:00', '21:00:00', 'Buka'),
(48, 8, 'Sabtu', '08:00:00', '21:00:00', 'Buka'),
(49, 8, 'Minggu', '08:00:00', '21:00:00', 'Buka'),
(50, 9, 'Senin', '07:00:00', '20:00:00', 'Buka'),
(51, 9, 'Selasa', '07:00:00', '20:00:00', 'Buka'),
(52, 9, 'Rabu', '07:00:00', '20:00:00', 'Buka'),
(53, 9, 'Kamis', '07:00:00', '20:00:00', 'Buka'),
(54, 9, 'Jumat', '07:00:00', '20:00:00', 'Buka'),
(55, 9, 'Sabtu', '07:00:00', '20:00:00', 'Buka'),
(56, 9, 'Minggu', '07:00:00', '20:00:00', 'Buka'),
(57, 10, 'Senin', '12:00:00', '18:00:00', 'Buka'),
(58, 10, 'Selasa', '12:00:00', '18:00:00', 'Buka'),
(59, 10, 'Rabu', '12:00:00', '18:00:00', 'Buka'),
(60, 10, 'Kamis', '12:00:00', '18:00:00', 'Buka'),
(61, 10, 'Jumat', '12:00:00', '18:00:00', 'Buka'),
(62, 10, 'Sabtu', '12:00:00', '18:00:00', 'Buka'),
(63, 10, 'Minggu', '12:00:00', '18:00:00', 'Buka'),
(64, 11, 'Senin', '06:00:00', '13:00:00', 'Buka'),
(65, 11, 'Selasa', '06:00:00', '13:00:00', 'Buka'),
(66, 11, 'Rabu', '06:00:00', '13:00:00', 'Buka'),
(67, 11, 'Kamis', '06:00:00', '13:00:00', 'Buka'),
(68, 11, 'Jumat', '06:00:00', '13:00:00', 'Buka'),
(69, 11, 'Sabtu', '06:00:00', '13:00:00', 'Buka'),
(70, 11, 'Minggu', '06:00:00', '13:00:00', 'Buka'),
(71, 12, 'Senin', NULL, NULL, 'Tutup'),
(72, 12, 'Selasa', '06:00:00', '22:00:00', 'Buka'),
(73, 12, 'Rabu', '06:00:00', '22:00:00', 'Buka'),
(74, 12, 'Kamis', '06:00:00', '22:00:00', 'Buka'),
(75, 12, 'Jumat', '06:00:00', '22:00:00', 'Buka'),
(76, 12, 'Sabtu', '06:00:00', '22:00:00', 'Buka'),
(77, 12, 'Minggu', '06:00:00', '22:00:00', 'Buka'),
(78, 13, 'Senin', '10:00:00', '21:00:00', 'Buka'),
(79, 13, 'Selasa', '10:00:00', '21:00:00', 'Buka'),
(80, 13, 'Rabu', '10:00:00', '21:00:00', 'Buka'),
(81, 13, 'Kamis', '10:00:00', '21:00:00', 'Buka'),
(82, 13, 'Jumat', '10:00:00', '21:00:00', 'Buka'),
(83, 13, 'Sabtu', '10:00:00', '21:00:00', 'Buka'),
(84, 13, 'Minggu', '10:00:00', '21:00:00', 'Buka'),
(85, 14, 'Senin', '10:00:00', '22:00:00', 'Buka'),
(86, 14, 'Selasa', '10:00:00', '22:00:00', 'Buka'),
(87, 14, 'Rabu', '10:00:00', '22:00:00', 'Buka'),
(88, 14, 'Kamis', '10:00:00', '22:00:00', 'Buka'),
(89, 14, 'Jumat', '10:00:00', '22:00:00', 'Buka'),
(90, 14, 'Sabtu', '10:00:00', '22:00:00', 'Buka'),
(91, 14, 'Minggu', '10:00:00', '22:00:00', 'Buka'),
(92, 15, 'Senin', '07:00:00', '23:00:00', 'Buka'),
(93, 15, 'Selasa', '07:00:00', '23:00:00', 'Buka'),
(94, 15, 'Rabu', '07:00:00', '23:00:00', 'Buka'),
(95, 15, 'Kamis', '07:00:00', '23:00:00', 'Buka'),
(96, 15, 'Jumat', '07:00:00', '23:00:00', 'Buka'),
(97, 15, 'Sabtu', '07:00:00', '23:00:00', 'Buka'),
(98, 15, 'Minggu', '07:00:00', '23:00:00', 'Buka'),
(99, 16, 'Senin', '09:00:00', '21:00:00', 'Buka'),
(100, 16, 'Selasa', '09:00:00', '21:00:00', 'Buka'),
(101, 16, 'Rabu', '09:00:00', '21:00:00', 'Buka'),
(102, 16, 'Kamis', '09:00:00', '21:00:00', 'Buka'),
(103, 16, 'Jumat', '09:00:00', '21:00:00', 'Buka'),
(104, 16, 'Sabtu', '09:00:00', '21:00:00', 'Buka'),
(105, 16, 'Minggu', '13:00:00', '21:00:00', 'Buka'),
(106, 17, 'Senin', '08:00:00', '11:00:00', 'Buka'),
(107, 17, 'Selasa', '08:00:00', '11:00:00', 'Buka'),
(108, 17, 'Rabu', '08:00:00', '11:00:00', 'Buka'),
(109, 17, 'Kamis', '08:00:00', '11:00:00', 'Buka'),
(110, 17, 'Jumat', '08:00:00', '11:00:00', 'Buka'),
(111, 17, 'Sabtu', NULL, NULL, 'Tutup'),
(112, 17, 'Minggu', NULL, NULL, 'Tutup'),
(113, 18, 'Senin', '08:00:00', '12:30:00', 'Buka'),
(114, 18, 'Selasa', '08:00:00', '12:30:00', 'Buka'),
(115, 18, 'Rabu', '08:00:00', '12:30:00', 'Buka'),
(116, 18, 'Kamis', '08:00:00', '12:30:00', 'Buka'),
(117, 18, 'Jumat', '08:00:00', '12:30:00', 'Buka'),
(118, 18, 'Sabtu', NULL, NULL, 'Tutup'),
(119, 18, 'Minggu', NULL, NULL, 'Tutup'),
(120, 19, 'Senin', '07:00:00', '13:00:00', 'Buka'),
(121, 19, 'Selasa', '07:00:00', '13:00:00', 'Buka'),
(122, 19, 'Rabu', '07:00:00', '13:00:00', 'Buka'),
(123, 19, 'Kamis', '07:00:00', '13:00:00', 'Buka'),
(124, 19, 'Jumat', '07:00:00', '13:00:00', 'Buka'),
(125, 19, 'Sabtu', '07:00:00', '13:00:00', 'Buka'),
(126, 19, 'Minggu', '07:00:00', '13:00:00', 'Buka'),
(127, 20, 'Senin', '07:00:00', '18:00:00', 'Buka'),
(128, 20, 'Selasa', '07:00:00', '18:00:00', 'Buka'),
(129, 20, 'Rabu', '07:00:00', '18:00:00', 'Buka'),
(130, 20, 'Kamis', '07:00:00', '18:00:00', 'Buka'),
(131, 20, 'Jumat', '07:00:00', '18:00:00', 'Buka'),
(132, 20, 'Sabtu', '07:00:00', '18:00:00', 'Buka'),
(133, 20, 'Minggu', '07:00:00', '18:00:00', 'Buka'),
(134, 21, 'Senin', '06:30:00', '12:00:00', 'Buka'),
(135, 21, 'Selasa', '06:30:00', '12:00:00', 'Buka'),
(136, 21, 'Rabu', '06:30:00', '12:00:00', 'Buka'),
(137, 21, 'Kamis', '06:30:00', '12:00:00', 'Buka'),
(138, 21, 'Jumat', '06:30:00', '12:00:00', 'Buka'),
(139, 21, 'Sabtu', '06:30:00', '12:00:00', 'Buka'),
(140, 21, 'Minggu', '06:30:00', '12:00:00', 'Buka');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kategori_rasa`
--
ALTER TABLE `kategori_rasa`
  ADD PRIMARY KEY (`id_rasa`);

--
-- Indeks untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id_metode`);

--
-- Indeks untuk tabel `mitra_platform`
--
ALTER TABLE `mitra_platform`
  ADD PRIMARY KEY (`id_mitra`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_umkm` (`id_umkm`);

--
-- Indeks untuk tabel `produk_rasa`
--
ALTER TABLE `produk_rasa`
  ADD PRIMARY KEY (`id_produk_rasa`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_rasa` (`id_rasa`);

--
-- Indeks untuk tabel `umkm`
--
ALTER TABLE `umkm`
  ADD PRIMARY KEY (`id_umkm`);

--
-- Indeks untuk tabel `umkm_mitra`
--
ALTER TABLE `umkm_mitra`
  ADD PRIMARY KEY (`id_umkm_mitra`),
  ADD KEY `id_umkm` (`id_umkm`),
  ADD KEY `id_mitra` (`id_mitra`);

--
-- Indeks untuk tabel `umkm_pembayaran`
--
ALTER TABLE `umkm_pembayaran`
  ADD PRIMARY KEY (`id_umkm_bayar`),
  ADD KEY `id_umkm` (`id_umkm`),
  ADD KEY `id_metode` (`id_metode`);

--
-- Indeks untuk tabel `waktu_operasional`
--
ALTER TABLE `waktu_operasional`
  ADD PRIMARY KEY (`id_waktu`),
  ADD KEY `id_umkm` (`id_umkm`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kategori_rasa`
--
ALTER TABLE `kategori_rasa`
  MODIFY `id_rasa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `mitra_platform`
--
ALTER TABLE `mitra_platform`
  MODIFY `id_mitra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268;

--
-- AUTO_INCREMENT untuk tabel `produk_rasa`
--
ALTER TABLE `produk_rasa`
  MODIFY `id_produk_rasa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=562;

--
-- AUTO_INCREMENT untuk tabel `umkm`
--
ALTER TABLE `umkm`
  MODIFY `id_umkm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `umkm_mitra`
--
ALTER TABLE `umkm_mitra`
  MODIFY `id_umkm_mitra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `umkm_pembayaran`
--
ALTER TABLE `umkm_pembayaran`
  MODIFY `id_umkm_bayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `waktu_operasional`
--
ALTER TABLE `waktu_operasional`
  MODIFY `id_waktu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_umkm`) REFERENCES `umkm` (`id_umkm`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `produk_rasa`
--
ALTER TABLE `produk_rasa`
  ADD CONSTRAINT `produk_rasa_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produk_rasa_ibfk_2` FOREIGN KEY (`id_rasa`) REFERENCES `kategori_rasa` (`id_rasa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `umkm_mitra`
--
ALTER TABLE `umkm_mitra`
  ADD CONSTRAINT `umkm_mitra_ibfk_1` FOREIGN KEY (`id_umkm`) REFERENCES `umkm` (`id_umkm`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `umkm_mitra_ibfk_2` FOREIGN KEY (`id_mitra`) REFERENCES `mitra_platform` (`id_mitra`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `umkm_pembayaran`
--
ALTER TABLE `umkm_pembayaran`
  ADD CONSTRAINT `umkm_pembayaran_ibfk_1` FOREIGN KEY (`id_umkm`) REFERENCES `umkm` (`id_umkm`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `umkm_pembayaran_ibfk_2` FOREIGN KEY (`id_metode`) REFERENCES `metode_pembayaran` (`id_metode`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `waktu_operasional`
--
ALTER TABLE `waktu_operasional`
  ADD CONSTRAINT `waktu_operasional_ibfk_1` FOREIGN KEY (`id_umkm`) REFERENCES `umkm` (`id_umkm`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
