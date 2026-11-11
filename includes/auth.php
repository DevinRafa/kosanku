<?php
declare(strict_types=1);

/**
 * Autentikasi admin tanpa sesi di server (stateless).
 *
 * Vercel menjalankan PHP sebagai fungsi serverless: setiap permintaan bisa dilayani
 * oleh proses berbeda, sehingga $_SESSION berbasis berkas tidak dapat diandalkan.
 * Sebagai gantinya, setelah kata sandi benar server mengirim cookie berisi
 * "waktu-kedaluwarsa.tanda-tangan-HMAC". Pada tiap permintaan, tanda tangan dihitung
 * ulang dengan rahasia di environment variable, jadi tidak perlu menyimpan apa pun.
 *
 * Variabel lingkungan:
 *   ADMIN_PASSWORD  kata sandi admin (bila kosong, mode terbuka untuk pengembangan lokal)
 *   AUTH_SECRET     rahasia untuk menandatangani cookie (disarankan string acak panjang)
 */

const COOKIE_SESI = 'kosanku_sesi';
const SESI_DETIK  = 7200; // 2 jam

function auth_aktif(): bool
{
    return env('ADMIN_PASSWORD') !== null;
}

function auth_rahasia(): string
{
    return env('AUTH_SECRET') ?? hash('sha256', 'kosanku|' . (string) env('ADMIN_PASSWORD'));
}

function buat_token(int $kedaluwarsa): string
{
    $isi = (string) $kedaluwarsa;
    return $isi . '.' . hash_hmac('sha256', $isi, auth_rahasia());
}

function token_valid(string $token): bool
{
    $bagian = explode('.', $token, 2);
    if (count($bagian) !== 2 || !ctype_digit($bagian[0])) {
        return false;
    }
    if ((int) $bagian[0] < time()) {
        return false;
    }
    $harapan = hash_hmac('sha256', $bagian[0], auth_rahasia());
    return hash_equals($harapan, $bagian[1]);
}

function sudah_masuk(): bool
{
    if (!auth_aktif()) {
        return true;
    }
    return token_valid((string) ($_COOKIE[COOKIE_SESI] ?? ''));
}

function koneksi_aman(): bool
{
    return (($_SERVER['HTTPS'] ?? '') === 'on')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function kirim_cookie(string $nilai, int $kedaluwarsa): void
{
    setcookie(COOKIE_SESI, $nilai, [
        'expires'  => $kedaluwarsa,
        'path'     => '/',
        'secure'   => koneksi_aman(),
        'httponly' => true,
        'samesite' => 'Lax', // cookie tidak ikut pada POST lintas situs (perlindungan CSRF)
    ]);
}

function mulai_sesi(): void
{
    $kedaluwarsa = time() + SESI_DETIK;
    kirim_cookie(buat_token($kedaluwarsa), $kedaluwarsa);
}

function akhiri_sesi(): void
{
    kirim_cookie('', time() - 3600);
}

function cek_kata_sandi(string $masukan): bool
{
    $benar = env('ADMIN_PASSWORD');
    return $benar !== null && hash_equals($benar, $masukan);
}

/** Hanya terima tujuan redirect berupa path lokal. */
function tujuan_aman(string $tujuan): string
{
    if ($tujuan !== '' && $tujuan[0] === '/' && !str_starts_with($tujuan, '//') && !str_contains($tujuan, '\\')) {
        return $tujuan;
    }
    return '/';
}

/** Hentikan halaman yang butuh admin dan arahkan ke formulir masuk. */
function wajib_masuk(): void
{
    if (!sudah_masuk()) {
        redirect('/masuk?lanjut=' . rawurlencode((string) ($_SERVER['REQUEST_URI'] ?? '/')));
    }
}
