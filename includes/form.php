<?php
/**
 * Formulir kos yang dipakai bersama oleh halaman tambah dan ubah.
 *
 * @var array  $data    nilai isian saat ini
 * @var array  $galat   pesan galat per kolom
 * @var string $aksi    URL tujuan form
 * @var string $tombol  teks tombol kirim
 */
$fasilitasTerpilih = daftar_fasilitas((string) ($data['fasilitas'] ?? ''));
$fasilitasLain = [];
foreach ($fasilitasTerpilih as $f) {
    if (!in_array($f, FASILITAS_UMUM, true)) {
        $fasilitasLain[] = $f;
    }
}
$kolomTeks = static function (string $nama, string $label, string $tipe = 'text', string $petunjuk = '') use ($data, $galat): void {
    $nilai = (string) ($data[$nama] ?? '');
    $ada = isset($galat[$nama]);
    echo '<div class="isian' . ($ada ? ' isian-galat' : '') . '">';
    echo '<label for="' . e($nama) . '">' . e($label) . '</label>';
    echo '<input type="' . e($tipe) . '" id="' . e($nama) . '" name="' . e($nama) . '" value="' . e($nilai) . '"'
        . ($petunjuk !== '' ? ' placeholder="' . e($petunjuk) . '"' : '') . ' required>';
    if ($ada) {
        echo '<p class="galat" role="alert">' . e($galat[$nama]) . '</p>';
    }
    echo '</div>';
};
?>
<?php if (count($galat) > 0): ?>
    <div class="notif notif-err" role="alert">
        Ada <?= count($galat) ?> isian yang perlu diperbaiki. Periksa kolom bertanda merah.
    </div>
<?php endif; ?>

<form class="form" method="post" action="<?= e($aksi) ?>" novalidate data-form-kos>
    <div class="form-grid">
        <?php $kolomTeks('nama', 'Nama kos', 'text', 'Contoh: Kos Melati Asri'); ?>
        <?php $kolomTeks('kota', 'Kota', 'text', 'Purwokerto'); ?>
    </div>

    <?php $kolomTeks('alamat', 'Alamat lengkap', 'text', 'Nama jalan, nomor, patokan'); ?>

    <div class="form-grid form-grid-3">
        <div class="isian<?= isset($galat['tipe']) ? ' isian-galat' : '' ?>">
            <label for="tipe">Tipe kos</label>
            <select id="tipe" name="tipe" required>
                <option value="">Pilih tipe</option>
                <?php foreach (TIPE_KOS as $kode => $nama): ?>
                    <option value="<?= e($kode) ?>" <?= ($data['tipe'] ?? '') === $kode ? 'selected' : '' ?>><?= e($nama) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($galat['tipe'])): ?><p class="galat" role="alert"><?= e($galat['tipe']) ?></p><?php endif; ?>
        </div>

        <div class="isian<?= isset($galat['harga']) ? ' isian-galat' : '' ?>">
            <label for="harga">Harga per bulan (Rp)</label>
            <input type="text" inputmode="numeric" id="harga" name="harga" value="<?= e((string) (($data['harga'] ?? '') ?: '')) ?>" placeholder="850000" required data-harga>
            <p class="petunjuk" data-harga-tampil aria-live="polite"></p>
            <?php if (isset($galat['harga'])): ?><p class="galat" role="alert"><?= e($galat['harga']) ?></p><?php endif; ?>
        </div>

        <?php $kolomTeks('ukuran', 'Ukuran kamar', 'text', '3x4 m'); ?>
    </div>

    <fieldset class="isian<?= isset($galat['fasilitas']) ? ' isian-galat' : '' ?>">
        <legend>Fasilitas</legend>
        <div class="centang-grid">
            <?php foreach (FASILITAS_UMUM as $f): ?>
                <label class="centang">
                    <input type="checkbox" name="fasilitas[]" value="<?= e($f) ?>" <?= in_array($f, $fasilitasTerpilih, true) ? 'checked' : '' ?>>
                    <span><?= e($f) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <label for="fasilitas_lain" class="label-kecil">Fasilitas lain (pisahkan dengan koma)</label>
        <input type="text" id="fasilitas_lain" name="fasilitas_lain" value="<?= e(implode(', ', $fasilitasLain)) ?>" placeholder="Contoh: Kulkas, Dispenser">
        <?php if (isset($galat['fasilitas'])): ?><p class="galat" role="alert"><?= e($galat['fasilitas']) ?></p><?php endif; ?>
    </fieldset>

    <div class="isian<?= isset($galat['deskripsi']) ? ' isian-galat' : '' ?>">
        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" rows="4" maxlength="1000" data-hitung><?= e((string) ($data['deskripsi'] ?? '')) ?></textarea>
        <p class="petunjuk"><span data-hitung-tampil>0</span> / 1000 karakter</p>
        <?php if (isset($galat['deskripsi'])): ?><p class="galat" role="alert"><?= e($galat['deskripsi']) ?></p><?php endif; ?>
    </div>

    <div class="form-grid">
        <?php $kolomTeks('kontak', 'Nomor WhatsApp pemilik', 'tel', '081234567890'); ?>

        <div class="isian<?= isset($galat['foto']) ? ' isian-galat' : '' ?>">
            <label for="foto">Foto kos</label>
            <select id="foto" name="foto" required data-foto-pilih>
                <?php foreach (FOTO_TERSEDIA as $berkas => $keterangan): ?>
                    <option value="<?= e($berkas) ?>" <?= ($data['foto'] ?? 'kos-01.svg') === $berkas ? 'selected' : '' ?>><?= e($keterangan) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($galat['foto'])): ?><p class="galat" role="alert"><?= e($galat['foto']) ?></p><?php endif; ?>
        </div>
    </div>

    <div class="pratinjau">
        <img src="/assets/img/<?= e((string) ($data['foto'] ?? 'kos-01.svg')) ?>" alt="Pratinjau foto kos" width="800" height="600" data-foto-pratinjau>
    </div>

    <label class="centang centang-utama">
        <input type="checkbox" name="tersedia" value="1" <?= !empty($data['tersedia']) ? 'checked' : '' ?>>
        <span>Masih ada kamar kosong</span>
    </label>

    <?php if (butuh_pin()): ?>
        <div class="isian">
            <label for="pin">Kode admin</label>
            <input type="password" id="pin" name="pin" autocomplete="off" required>
            <p class="petunjuk">Diperlukan untuk menyimpan perubahan data.</p>
            <?php if (isset($galat['pin'])): ?><p class="galat" role="alert"><?= e($galat['pin']) ?></p><?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="aksi">
        <button class="tombol" type="submit"><?= e($tombol) ?></button>
        <a class="tombol tombol-garis" href="/">Batal</a>
    </div>
</form>
