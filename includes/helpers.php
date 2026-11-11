<?php
declare(strict_types=1);

/** Escape teks sebelum dicetak ke HTML. */
function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

/** 850000 -> "Rp 850.000" */
function rupiah(int $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/** Label tipe kos memakai percabangan switch. */
function label_tipe(string $tipe): string
{
    switch ($tipe) {
        case 'putra':
            return 'Kos Putra';
        case 'putri':
            return 'Kos Putri';
        default:
            return 'Kos Campur';
    }
}

/** Pecah "Kasur,Lemari,WiFi" menjadi array bersih. */
function daftar_fasilitas(string $csv): array
{
    $hasil = [];
    foreach (explode(',', $csv) as $item) {
        $item = trim($item);
        if ($item !== '') {
            $hasil[] = $item;
        }
    }
    return $hasil;
}

/** Nomor 0812... -> 62812... untuk tautan WhatsApp. */
function nomor_wa(string $kontak): string
{
    $digit = preg_replace('/\D+/', '', $kontak) ?? '';
    if (str_starts_with($digit, '0')) {
        return '62' . substr($digit, 1);
    }
    return $digit;
}

function redirect(string $tujuan): never
{
    header('Location: ' . $tujuan, true, 303);
    exit;
}

/** Bangun query string dengan mempertahankan filter yang aktif. */
function url_daftar(array $filter, array $ubah = []): string
{
    $param = array_merge($filter, $ubah);
    $bersih = [];
    foreach ($param as $kunci => $nilai) {
        if ($nilai === '' || $nilai === null) {
            continue;
        }
        if ($kunci === 'hal' && (int) $nilai <= 1) {
            continue;
        }
        $bersih[$kunci] = $nilai;
    }
    return '/' . ($bersih ? '?' . http_build_query($bersih) : '');
}

/** Pesan singkat setelah aksi, dibawa lewat query string. */
function pesan_status(): ?array
{
    $pesan = [
        'dibuat'  => ['ok', 'Data kos berhasil ditambahkan.'],
        'diubah'  => ['ok', 'Data kos berhasil diperbarui.'],
        'dihapus' => ['ok', 'Data kos berhasil dihapus.'],
        'pin'     => ['err', 'Kode admin tidak sesuai.'],
    ];
    $kunci = $_GET['pesan'] ?? '';
    return $pesan[$kunci] ?? null;
}

/** Kode admin dipakai bila variabel ADMIN_PIN diisi di server. */
function butuh_pin(): bool
{
    return env('ADMIN_PIN') !== null;
}

function pin_valid(): bool
{
    $pin = env('ADMIN_PIN');
    if ($pin === null) {
        return true;
    }
    return hash_equals($pin, (string) ($_POST['pin'] ?? ''));
}

/**
 * Validasi isian formulir kos.
 * Mengembalikan [data_bersih, daftar_galat].
 */
function validasi_kos(array $masuk): array
{
    $data = [
        'nama'      => trim((string) ($masuk['nama'] ?? '')),
        'alamat'    => trim((string) ($masuk['alamat'] ?? '')),
        'kota'      => trim((string) ($masuk['kota'] ?? '')),
        'tipe'      => (string) ($masuk['tipe'] ?? ''),
        'harga'     => (string) ($masuk['harga'] ?? ''),
        'ukuran'    => trim((string) ($masuk['ukuran'] ?? '')),
        'fasilitas' => '',
        'deskripsi' => trim((string) ($masuk['deskripsi'] ?? '')),
        'kontak'    => trim((string) ($masuk['kontak'] ?? '')),
        'foto'      => (string) ($masuk['foto'] ?? ''),
        'tersedia'  => isset($masuk['tersedia']) ? 1 : 0,
    ];
    $galat = [];

    // Panjang teks: [min, maks, label]
    $aturanTeks = [
        'nama'   => [3, 100, 'Nama kos'],
        'alamat' => [5, 200, 'Alamat'],
        'kota'   => [2, 60, 'Kota'],
        'ukuran' => [3, 20, 'Ukuran kamar'],
    ];
    foreach ($aturanTeks as $kolom => [$min, $maks, $label]) {
        $panjang = mb_strlen($data[$kolom]);
        if ($panjang < $min) {
            $galat[$kolom] = "$label minimal $min karakter.";
        } elseif ($panjang > $maks) {
            $galat[$kolom] = "$label maksimal $maks karakter.";
        }
    }

    if (!array_key_exists($data['tipe'], TIPE_KOS)) {
        $galat['tipe'] = 'Pilih tipe kos yang tersedia.';
    }

    $hargaBersih = preg_replace('/\D+/', '', $data['harga']) ?? '';
    if ($hargaBersih === '') {
        $galat['harga'] = 'Harga wajib diisi dengan angka.';
        $data['harga'] = 0;
    } else {
        $harga = (int) $hargaBersih;
        $data['harga'] = $harga;
        if ($harga < 50000 || $harga > 20000000) {
            $galat['harga'] = 'Harga harus antara Rp 50.000 dan Rp 20.000.000.';
        }
    }

    $digitKontak = preg_replace('/\D+/', '', $data['kontak']) ?? '';
    if (strlen($digitKontak) < 9 || strlen($digitKontak) > 15) {
        $galat['kontak'] = 'Nomor kontak harus 9 sampai 15 digit.';
    }
    $data['kontak'] = $digitKontak;

    if (!array_key_exists($data['foto'], FOTO_TERSEDIA)) {
        $galat['foto'] = 'Pilih salah satu foto yang tersedia.';
    }

    if (mb_strlen($data['deskripsi']) > 1000) {
        $galat['deskripsi'] = 'Deskripsi maksimal 1000 karakter.';
    }

    // Fasilitas: dari kotak centang + isian tambahan
    $dipilih = [];
    foreach ((array) ($masuk['fasilitas'] ?? []) as $item) {
        $item = trim((string) $item);
        if ($item !== '' && !in_array($item, $dipilih, true)) {
            $dipilih[] = $item;
        }
    }
    foreach (daftar_fasilitas((string) ($masuk['fasilitas_lain'] ?? '')) as $item) {
        if (!in_array($item, $dipilih, true)) {
            $dipilih[] = $item;
        }
    }
    $data['fasilitas'] = implode(',', $dipilih);
    if (mb_strlen($data['fasilitas']) > 255) {
        $galat['fasilitas'] = 'Daftar fasilitas terlalu panjang.';
    }
    if (count($dipilih) === 0) {
        $galat['fasilitas'] = 'Pilih minimal satu fasilitas.';
    }

    return [$data, $galat];
}
