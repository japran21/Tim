-- ============================================================
-- Migration: Tambah tabel log_undo_pembayaran
-- Dijalankan SEKALI di database db_streetfood
-- ============================================================

CREATE TABLE IF NOT EXISTS `log_undo_pembayaran` (
    `id_log`         INT(11)      NOT NULL AUTO_INCREMENT COMMENT 'PK log',
    `id_umkm_bayar`  INT(11)      NOT NULL               COMMENT 'ID asli dari umkm_pembayaran sebelum dihapus',
    `id_umkm`        INT(11)      NOT NULL,
    `id_metode`      INT(11)      NOT NULL,
    `nama_umkm`      VARCHAR(255) NOT NULL,
    `nama_metode`    VARCHAR(100) NOT NULL,
    `di_undo_pada`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu rollback dilakukan',
    `keterangan`     VARCHAR(255) DEFAULT  'Rollback manual oleh admin',
    PRIMARY KEY (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='Audit trail untuk setiap rollback transaksi pembayaran UMKM';
