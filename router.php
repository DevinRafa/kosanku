<?php
/**
 * Router untuk server bawaan PHP saat pengembangan lokal:
 *   php -S 127.0.0.1:8000 -t public router.php
 * Aturannya sama dengan "routes" pada vercel.json.
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

// Berkas statis di folder public dilayani langsung oleh server.
if ($path !== '/' && is_file(__DIR__ . '/public' . $path)) {
    return false;
}

if ($path === '/') {
    require __DIR__ . '/api/index.php';
} elseif (preg_match('#^/kos/(\d+)/?$#', $path, $m)) {
    $_GET['id'] = $m[1];
    require __DIR__ . '/api/detail.php';
} elseif (preg_match('#^/tambah/?$#', $path)) {
    require __DIR__ . '/api/create.php';
} elseif (preg_match('#^/ubah/(\d+)/?$#', $path, $m)) {
    $_GET['id'] = $m[1];
    require __DIR__ . '/api/edit.php';
} elseif (preg_match('#^/hapus/?$#', $path)) {
    require __DIR__ . '/api/delete.php';
} else {
    http_response_code(404);
    echo 'Halaman tidak ditemukan.';
}
