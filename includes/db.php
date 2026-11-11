<?php
declare(strict_types=1);

/**
 * Koneksi PDO ke MySQL. Pengaturan diambil dari variabel lingkungan:
 *   DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
 *   DB_SSL=1  -> aktifkan TLS (dibutuhkan oleh sebagian penyedia MySQL online)
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = env('DB_HOST', '127.0.0.1');
    $port = env('DB_PORT', '3306');
    $nama = env('DB_NAME', 'kosanku');
    $user = env('DB_USER', 'kosanku');
    $pass = env('DB_PASS', 'kosanku');

    $dsn = "mysql:host={$host};port={$port};dbname={$nama};charset=utf8mb4";
    $opsi = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    if (env('DB_SSL') === '1') {
        $kandidatCa = [
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/certs/ca-certificates.crt',
        ];
        $adaCa = false;
        foreach ($kandidatCa as $berkas) {
            if (is_file($berkas)) {
                $opsi[PDO::MYSQL_ATTR_SSL_CA] = $berkas;
                $adaCa = true;
                break;
            }
        }
        if (!$adaCa) {
            $opsi[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }
    }

    $pdo = new PDO($dsn, $user, $pass, $opsi);
    return $pdo;
}
