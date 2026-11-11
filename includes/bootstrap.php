<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Tangani galat tak terduga (mis. database belum terhubung) dengan halaman yang ramah.
set_exception_handler(function (Throwable $ex): void {
    http_response_code(500);
    $judul = 'Terjadi kesalahan';
    $rincian = env('APP_DEBUG') === '1' ? $ex->getMessage() : '';
    require __DIR__ . '/header.php';
    echo '<section class="kosong"><h1>Halaman belum bisa dimuat</h1>';
    echo '<p>Koneksi ke database bermasalah atau terjadi galat pada server. Coba lagi beberapa saat.</p>';
    if ($rincian !== '') {
        echo '<pre class="galat-teknis">' . e($rincian) . '</pre>';
    }
    echo '<a class="tombol" href="/">Kembali ke beranda</a></section>';
    require __DIR__ . '/footer.php';
});

/** Tampilkan halaman 404 lalu hentikan eksekusi. */
function tidak_ditemukan(): never
{
    http_response_code(404);
    $judul = 'Tidak ditemukan';
    require __DIR__ . '/header.php';
    echo '<section class="kosong"><h1>Data kos tidak ditemukan</h1>';
    echo '<p>Kos yang kamu cari mungkin sudah dihapus atau alamatnya keliru.</p>';
    echo '<a class="tombol" href="/">Lihat semua kos</a></section>';
    require __DIR__ . '/footer.php';
    exit;
}
