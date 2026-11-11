<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

$pdo = db();

// ---- 1. Baca filter dari query string -------------------------------
$q      = trim((string) ($_GET['q'] ?? ''));
$tipe   = (string) ($_GET['tipe'] ?? '');
$maks   = (int) ($_GET['maks'] ?? 0);
$urut   = (string) ($_GET['urut'] ?? 'terbaru');
$hal    = max(1, (int) ($_GET['hal'] ?? 1));

if (!array_key_exists($tipe, TIPE_KOS)) {
    $tipe = '';
}

// ---- 2. Susun WHERE secara kondisional ------------------------------
$kondisi = [];
$param   = [];

if ($q !== '') {
    $kondisi[] = '(nama LIKE :q1 OR alamat LIKE :q2 OR fasilitas LIKE :q3)';
    $param[':q1'] = $param[':q2'] = $param[':q3'] = '%' . $q . '%';
}
if ($tipe !== '') {
    $kondisi[] = 'tipe = :tipe';
    $param[':tipe'] = $tipe;
}
if ($maks > 0) {
    $kondisi[] = 'harga <= :maks';
    $param[':maks'] = $maks;
}
$where = $kondisi ? 'WHERE ' . implode(' AND ', $kondisi) : '';

switch ($urut) {
    case 'murah':
        $orderBy = 'harga ASC, id DESC';
        break;
    case 'mahal':
        $orderBy = 'harga DESC, id DESC';
        break;
    case 'nama':
        $orderBy = 'nama ASC';
        break;
    default:
        $urut = 'terbaru';
        $orderBy = 'id DESC';
}

// ---- 3. Hitung total dan ambil data halaman aktif -------------------
$stmtHitung = $pdo->prepare("SELECT COUNT(*) FROM kos $where");
$stmtHitung->execute($param);
$total = (int) $stmtHitung->fetchColumn();

$totalHal = max(1, (int) ceil($total / PER_HALAMAN));
if ($hal > $totalHal) {
    $hal = $totalHal;
}
$offset = ($hal - 1) * PER_HALAMAN;

$stmt = $pdo->prepare("SELECT * FROM kos $where ORDER BY $orderBy LIMIT :lim OFFSET :off");
foreach ($param as $kunci => $nilai) {
    $stmt->bindValue($kunci, $nilai, is_int($nilai) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':lim', PER_HALAMAN, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftar = [];
while ($baris = $stmt->fetch()) {      // perulangan while
    $daftar[] = $baris;
}

// ---- 4. Ringkasan untuk kartu statistik -----------------------------
$ringkas = $pdo->query('SELECT COUNT(*) AS jumlah, MIN(harga) AS termurah, AVG(harga) AS rata FROM kos')->fetch();
$perTipe = [];
foreach ($pdo->query('SELECT tipe, COUNT(*) AS n FROM kos GROUP BY tipe') as $r) {
    $perTipe[$r['tipe']] = (int) $r['n'];
}

$filter = ['q' => $q, 'tipe' => $tipe, 'maks' => $maks > 0 ? $maks : '', 'urut' => $urut === 'terbaru' ? '' : $urut];
$judul  = '';

require __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div>
        <p class="kicker">Cari kos mahasiswa</p>
        <h1>Temukan kos yang pas di Purwokerto</h1>
        <p class="lead">Bandingkan harga, tipe, dan fasilitas kos dekat kampus. Semua data tersimpan di database MySQL.</p>
    </div>
    <dl class="statistik">
        <div>
            <dt>Jumlah kos</dt>
            <dd><?= (int) $ringkas['jumlah'] ?></dd>
        </div>
        <div>
            <dt>Mulai dari</dt>
            <dd><?= $ringkas['termurah'] !== null ? e(rupiah((int) $ringkas['termurah'])) : '-' ?></dd>
        </div>
        <div>
            <dt>Rata-rata / bulan</dt>
            <dd><?= $ringkas['rata'] !== null ? e(rupiah((int) round((float) $ringkas['rata'], -3))) : '-' ?></dd>
        </div>
    </dl>
</section>

<form class="cari" method="get" action="/" role="search">
    <div class="kolom kolom-lebar">
        <label for="q">Kata kunci</label>
        <input type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="Nama, alamat, atau fasilitas">
    </div>
    <div class="kolom">
        <label for="tipe">Tipe</label>
        <select id="tipe" name="tipe">
            <option value="">Semua</option>
            <?php foreach (TIPE_KOS as $kode => $nama): ?>
                <option value="<?= e($kode) ?>" <?= $tipe === $kode ? 'selected' : '' ?>>
                    <?= e($nama) ?><?= isset($perTipe[$kode]) ? ' (' . $perTipe[$kode] . ')' : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="kolom">
        <label for="maks">Harga maks.</label>
        <select id="maks" name="maks">
            <option value="">Bebas</option>
            <?php for ($nilai = 500000; $nilai <= 1500000; $nilai += 250000): ?>
                <option value="<?= $nilai ?>" <?= $maks === $nilai ? 'selected' : '' ?>><?= e(rupiah($nilai)) ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="kolom">
        <label for="urut">Urutkan</label>
        <select id="urut" name="urut">
            <option value="terbaru" <?= $urut === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
            <option value="murah" <?= $urut === 'murah' ? 'selected' : '' ?>>Harga terendah</option>
            <option value="mahal" <?= $urut === 'mahal' ? 'selected' : '' ?>>Harga tertinggi</option>
            <option value="nama" <?= $urut === 'nama' ? 'selected' : '' ?>>Nama A-Z</option>
        </select>
    </div>
    <div class="kolom kolom-aksi">
        <button class="tombol" type="submit">Cari</button>
        <?php if ($q !== '' || $tipe !== '' || $maks > 0 || $urut !== 'terbaru'): ?>
            <a class="tautan-reset" href="/">Reset</a>
        <?php endif; ?>
    </div>
</form>

<p class="hasil-info" aria-live="polite">
    <?php if ($total === 0): ?>
        Tidak ada kos yang cocok.
    <?php else: ?>
        Menampilkan <strong><?= count($daftar) ?></strong> dari <strong><?= $total ?></strong> kos
        <?= $totalHal > 1 ? '(halaman ' . $hal . ' dari ' . $totalHal . ')' : '' ?>
    <?php endif; ?>
</p>

<?php if (count($daftar) === 0): ?>
    <section class="kosong">
        <h2>Belum ada hasil</h2>
        <p>Coba kurangi filter atau gunakan kata kunci yang lebih umum.</p>
        <a class="tombol" href="/tambah">Tambah data kos</a>
    </section>
<?php else: ?>
    <ul class="grid">
        <?php foreach ($daftar as $kos): ?>
            <li class="kartu">
                <a class="kartu-foto" href="/kos/<?= (int) $kos['id'] ?>">
                    <img src="/assets/img/<?= e($kos['foto']) ?>" alt="Foto <?= e($kos['nama']) ?>" width="800" height="600" loading="lazy">
                    <span class="lencana lencana-<?= e($kos['tipe']) ?>"><?= e(label_tipe($kos['tipe'])) ?></span>
                    <?php if ((int) $kos['tersedia'] === 0): ?>
                        <span class="lencana lencana-penuh">Penuh</span>
                    <?php endif; ?>
                </a>
                <div class="kartu-isi">
                    <h2><a href="/kos/<?= (int) $kos['id'] ?>"><?= e($kos['nama']) ?></a></h2>
                    <p class="alamat"><?= e($kos['alamat']) ?></p>
                    <ul class="chip-daftar">
                        <?php foreach (array_slice(daftar_fasilitas($kos['fasilitas']), 0, 4) as $f): ?>
                            <li><?= e($f) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="harga"><?= e(rupiah((int) $kos['harga'])) ?><span> / bulan</span></p>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($totalHal > 1): ?>
        <nav class="halaman" aria-label="Halaman">
            <?php if ($hal > 1): ?>
                <a href="<?= e(url_daftar($filter, ['hal' => $hal - 1])) ?>" rel="prev">Sebelumnya</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalHal; $i++): ?>
                <?php if ($i === $hal): ?>
                    <span class="aktif" aria-current="page"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= e(url_daftar($filter, ['hal' => $i])) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($hal < $totalHal): ?>
                <a href="<?= e(url_daftar($filter, ['hal' => $hal + 1])) ?>" rel="next">Berikutnya</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
