<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
$pdo = db();

$stmt = $pdo->prepare('SELECT * FROM kos WHERE id = :id');
$stmt->execute([':id' => $id]);
$kos = $stmt->fetch();

if (!$kos) {
    tidak_ditemukan();
}

// Kos lain di kota yang sama (maksimal 3)
$stmtLain = $pdo->prepare('SELECT id, nama, harga, foto, tipe FROM kos WHERE kota = :kota AND id <> :id ORDER BY ABS(harga - :harga) LIMIT 3');
$stmtLain->execute([':kota' => $kos['kota'], ':id' => $id, ':harga' => (int) $kos['harga']]);
$lainnya = $stmtLain->fetchAll();

$fasilitas = daftar_fasilitas($kos['fasilitas']);
$pesanWa   = rawurlencode('Halo, saya melihat ' . $kos['nama'] . ' di KosanKu. Apakah masih ada kamar kosong?');
$judul     = $kos['nama'];
$admin     = sudah_masuk();

require __DIR__ . '/../includes/header.php';
?>
<nav class="remah" aria-label="Jejak halaman"><a href="/">Daftar kos</a> <span aria-hidden="true">/</span> <span><?= e($kos['nama']) ?></span></nav>

<article class="detail">
    <div class="detail-foto">
        <img src="/assets/img/<?= e($kos['foto']) ?>" alt="Foto <?= e($kos['nama']) ?>" width="800" height="600">
    </div>

    <div class="detail-info">
        <div class="baris-lencana">
            <span class="lencana lencana-<?= e($kos['tipe']) ?>"><?= e(label_tipe($kos['tipe'])) ?></span>
            <?php if ((int) $kos['tersedia'] === 1): ?>
                <span class="lencana lencana-ada">Kamar tersedia</span>
            <?php else: ?>
                <span class="lencana lencana-penuh">Penuh</span>
            <?php endif; ?>
        </div>

        <h1><?= e($kos['nama']) ?></h1>
        <p class="alamat"><?= e($kos['alamat']) ?>, <?= e($kos['kota']) ?></p>
        <p class="harga harga-besar"><?= e(rupiah((int) $kos['harga'])) ?><span> / bulan</span></p>

        <dl class="spek">
            <div><dt>Ukuran kamar</dt><dd><?= e($kos['ukuran']) ?></dd></div>
            <div><dt>Tipe</dt><dd><?= e(label_tipe($kos['tipe'])) ?></dd></div>
            <div><dt>Diperbarui</dt><dd><?= e(date('d M Y', strtotime((string) $kos['diubah_pada']))) ?></dd></div>
        </dl>

        <?php if (trim((string) $kos['deskripsi']) !== ''): ?>
            <h2>Deskripsi</h2>
            <p class="deskripsi"><?= nl2br(e($kos['deskripsi'])) ?></p>
        <?php endif; ?>

        <h2>Fasilitas</h2>
        <ul class="chip-daftar chip-besar">
            <?php foreach ($fasilitas as $f): ?>
                <li><?= e($f) ?></li>
            <?php endforeach; ?>
        </ul>

        <div class="aksi">
            <a class="tombol" href="https://wa.me/<?= e(nomor_wa($kos['kontak'])) ?>?text=<?= $pesanWa ?>" target="_blank" rel="noopener">Hubungi via WhatsApp</a>
            <?php if ($admin): ?>
                <a class="tombol tombol-garis" href="/ubah/<?= (int) $kos['id'] ?>">Ubah</a>
                <button class="tombol tombol-bahaya" type="button" data-buka-hapus>Hapus</button>
            <?php endif; ?>
        </div>
    </div>
</article>

<?php if ($admin): ?>
<dialog id="dialog-hapus" class="dialog">
    <form method="post" action="/hapus">
        <h2>Hapus data kos?</h2>
        <p><strong><?= e($kos['nama']) ?></strong> akan dihapus permanen dan tidak bisa dikembalikan.</p>
        <input type="hidden" name="id" value="<?= (int) $kos['id'] ?>">
        <div class="aksi">
            <button class="tombol tombol-bahaya" type="submit">Ya, hapus</button>
            <button class="tombol tombol-garis" type="button" data-tutup-hapus>Batal</button>
        </div>
    </form>
</dialog>
<?php endif; ?>

<?php if (count($lainnya) > 0): ?>
    <section class="lainnya">
        <h2>Kos lain di <?= e($kos['kota']) ?></h2>
        <ul class="grid grid-kecil">
            <?php foreach ($lainnya as $k): ?>
                <li class="kartu">
                    <a class="kartu-foto" href="/kos/<?= (int) $k['id'] ?>">
                        <img src="/assets/img/<?= e($k['foto']) ?>" alt="Foto <?= e($k['nama']) ?>" width="800" height="600" loading="lazy">
                    </a>
                    <div class="kartu-isi">
                        <h3><a href="/kos/<?= (int) $k['id'] ?>"><?= e($k['nama']) ?></a></h3>
                        <p class="harga"><?= e(rupiah((int) $k['harga'])) ?><span> / bulan</span></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
