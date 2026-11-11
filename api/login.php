<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

$lanjut = tujuan_aman((string) ($_REQUEST['lanjut'] ?? '/'));

// Bila sudah masuk (atau mode terbuka), tidak perlu formulir.
if (sudah_masuk() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($lanjut);
}

$galat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (cek_kata_sandi((string) ($_POST['kata_sandi'] ?? ''))) {
        mulai_sesi();
        $pemisah = str_contains($lanjut, '?') ? '&' : '?';
        redirect($lanjut . $pemisah . 'pesan=masuk');
    }
    usleep(500000); // perlambat tebak-tebakan kata sandi
    http_response_code(401);
    $galat = 'Kata sandi tidak sesuai.';
}

$judul = 'Masuk admin';
require __DIR__ . '/../includes/header.php';
?>
<section class="judul-halaman">
    <h1>Masuk admin</h1>
    <p>Hanya admin yang dapat menambah, mengubah, dan menghapus data kos.</p>
</section>

<form class="form form-sempit" method="post" action="/masuk">
    <input type="hidden" name="lanjut" value="<?= e($lanjut) ?>">
    <div class="isian<?= $galat !== '' ? ' isian-galat' : '' ?>">
        <label for="kata_sandi">Kata sandi</label>
        <input type="password" id="kata_sandi" name="kata_sandi" autocomplete="current-password" required autofocus>
        <?php if ($galat !== ''): ?><p class="galat" role="alert"><?= e($galat) ?></p><?php endif; ?>
    </div>
    <div class="aksi">
        <button class="tombol" type="submit">Masuk</button>
        <a class="tombol tombol-garis" href="/">Batal</a>
    </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
