<?php
/** @var string $judul */
$judulHalaman = isset($judul) && $judul !== '' ? $judul . ' | ' . NAMA_SITUS : NAMA_SITUS . ' - Cari kos di Purwokerto';
$status = pesan_status();
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judulHalaman) ?></title>
    <meta name="description" content="KosanKu membantu mahasiswa mencari kos berdasarkan tipe, harga, dan fasilitas.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
</head>
<body>
<a class="lewati" href="#konten">Lewati ke konten</a>
<header class="bar">
    <div class="bungkus bar-isi">
        <a class="logo" href="/" aria-label="<?= e(NAMA_SITUS) ?> beranda">
            <span class="logo-tanda" aria-hidden="true">K</span>
            <span class="logo-teks"><?= e(NAMA_SITUS) ?></span>
        </a>
        <nav class="menu" aria-label="Menu utama">
            <a href="/">Daftar kos</a>
            <a class="tombol tombol-kecil" href="/tambah">+ Tambah kos</a>
        </nav>
    </div>
</header>
<main id="konten" class="bungkus">
<?php if ($status !== null): ?>
    <div class="notif notif-<?= e($status[0]) ?>" role="status"><?= e($status[1]) ?></div>
<?php endif; ?>
