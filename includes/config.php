<?php
declare(strict_types=1);

// Konstanta aplikasi
const NAMA_SITUS = 'KosanKu';
const PER_HALAMAN = 6;

// Pilihan tipe kos: kunci = nilai di database, isi = label yang ditampilkan
const TIPE_KOS = [
    'putra'  => 'Putra',
    'putri'  => 'Putri',
    'campur' => 'Campur',
];

// Daftar foto contoh yang ada di public/assets/img
const FOTO_TERSEDIA = [
    'kos-01.svg' => 'Tampak depan - rumah dua lantai',
    'kos-02.svg' => 'Tampak depan - rumah satu lantai',
    'kos-03.svg' => 'Tampak depan - gedung tiga lantai',
    'kos-04.svg' => 'Tampak depan - paviliun taman',
    'kos-05.svg' => 'Kamar - kasur dan jendela',
    'kos-06.svg' => 'Kamar - meja belajar',
    'kos-07.svg' => 'Kamar - lemari dan karpet',
    'kos-08.svg' => 'Kamar - kasur dan rak',
];

// Daftar fasilitas yang disarankan pada formulir
const FASILITAS_UMUM = [
    'Kasur', 'Lemari', 'Meja belajar', 'AC', 'WiFi', 'Kamar mandi dalam',
    'Kamar mandi luar', 'Dapur bersama', 'Parkir motor', 'Parkir mobil',
    'Laundry', 'CCTV', 'Water heater',
];

/**
 * Ambil variabel lingkungan (Vercel / server lokal).
 */
function env(string $nama, ?string $bawaan = null): ?string
{
    $nilai = getenv($nama);
    if ($nilai === false || $nilai === '') {
        $nilai = $_ENV[$nama] ?? $_SERVER[$nama] ?? null;
    }
    if ($nilai === null || $nilai === '') {
        return $bawaan;
    }
    return (string) $nilai;
}
