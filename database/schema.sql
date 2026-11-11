-- KosanKu : skema dan data contoh (MySQL / MariaDB)
-- Jalankan sekali pada database kosong:  mysql -u USER -p NAMA_DB < database/schema.sql

SET NAMES utf8mb4;

DROP TABLE IF EXISTS kos;

CREATE TABLE kos (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama        VARCHAR(100)  NOT NULL,
    alamat      VARCHAR(200)  NOT NULL,
    kota        VARCHAR(60)   NOT NULL,
    tipe        ENUM('putra','putri','campur') NOT NULL,
    harga       INT UNSIGNED  NOT NULL COMMENT 'harga sewa per bulan (rupiah)',
    ukuran      VARCHAR(20)   NOT NULL COMMENT 'contoh: 3x4 m',
    fasilitas   VARCHAR(255)  NOT NULL DEFAULT '' COMMENT 'dipisah koma',
    deskripsi   TEXT          NULL,
    kontak      VARCHAR(20)   NOT NULL,
    foto        VARCHAR(60)   NOT NULL DEFAULT 'kos-01.svg',
    tersedia    TINYINT(1)    NOT NULL DEFAULT 1,
    dibuat_pada DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    diubah_pada DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_tipe (tipe),
    KEY idx_harga (harga),
    KEY idx_kota (kota)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data contoh (fiktif) di sekitar kampus Purwokerto
INSERT INTO kos (nama, alamat, kota, tipe, harga, ukuran, fasilitas, deskripsi, kontak, foto, tersedia) VALUES
('Kos Melati Asri', 'Jl. Karangwangkal No. 12, dekat gerbang belakang kampus', 'Purwokerto', 'putri', 850000, '3x4 m',
 'Kasur,Lemari,Meja belajar,WiFi,Kamar mandi dalam,Dapur bersama',
 'Bangunan dua lantai dengan halaman depan yang teduh. Jalan kaki sekitar 5 menit ke kampus. Listrik sudah termasuk harga sewa.',
 '081200000001', 'kos-01.svg', 1),
('Kos Pak Darto', 'Jl. Dr. Soeparno Gg. 3 No. 7', 'Purwokerto', 'putra', 600000, '3x3 m',
 'Kasur,Lemari,WiFi,Parkir motor,Kamar mandi luar',
 'Kos sederhana dan tenang, pemilik tinggal di lokasi. Parkir motor luas dan aman.',
 '081200000002', 'kos-02.svg', 1),
('Griya Kenanga', 'Jl. Gerilya Barat No. 45', 'Purwokerto', 'campur', 1100000, '4x4 m',
 'Kasur,Lemari,Meja belajar,AC,WiFi,Kamar mandi dalam,Parkir mobil,CCTV',
 'Kamar luas dengan AC dan kamar mandi dalam. Akses jalan besar, dekat warung makan dan minimarket.',
 '081200000003', 'kos-03.svg', 1),
('Kos Seroja', 'Jl. Grendeng Indah Blok C No. 5', 'Purwokerto', 'putri', 950000, '3x4 m',
 'Kasur,Lemari,Meja belajar,WiFi,Kamar mandi dalam,Dapur bersama,Laundry',
 'Lingkungan khusus mahasiswi, ada jam malam pukul 22.00. Tersedia dapur bersama yang bersih.',
 '081200000004', 'kos-04.svg', 1),
('Kos Bumi Sari', 'Jl. Kranji Raya No. 21', 'Purwokerto', 'putra', 700000, '3x4 m',
 'Kasur,Lemari,Meja belajar,WiFi,Parkir motor',
 'Kamar rapi dengan ventilasi baik. Tersedia ruang tamu bersama untuk kerja kelompok.',
 '081200000005', 'kos-05.svg', 0),
('Paviliun Cempaka', 'Jl. Overste Isdiman No. 88', 'Purwokerto', 'campur', 1350000, '4x5 m',
 'Kasur,Lemari,Meja belajar,AC,WiFi,Kamar mandi dalam,Water heater,Parkir mobil,CCTV',
 'Paviliun terpisah dengan akses privat, cocok untuk yang mengerjakan tugas akhir dan butuh suasana tenang.',
 '081200000006', 'kos-06.svg', 1),
('Kos Ramah Hati', 'Jl. Berkoh Gg. Mawar No. 9', 'Purwokerto', 'putri', 500000, '3x3 m',
 'Kasur,Lemari,Kamar mandi luar,Dapur bersama',
 'Pilihan hemat untuk mahasiswi. Dekat angkutan umum dan pasar tradisional.',
 '081200000007', 'kos-07.svg', 1),
('Kos Harmoni', 'Jl. Karang Bawang No. 33', 'Purwokerto', 'putra', 800000, '3x4 m',
 'Kasur,Lemari,Meja belajar,WiFi,Kamar mandi dalam,Parkir motor,Laundry',
 'Kamar mandi dalam di setiap unit. Pengelola ramah dan responsif, tersedia layanan laundry kiloan.',
 '081200000008', 'kos-08.svg', 1);
