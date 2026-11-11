<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

wajib_masuk();

$id = (int) ($_GET['id'] ?? 0);
$pdo = db();

$stmt = $pdo->prepare('SELECT * FROM kos WHERE id = :id');
$stmt->execute([':id' => $id]);
$data = $stmt->fetch();

if (!$data) {
    tidak_ditemukan();
}

$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$data, $galat] = validasi_kos($_POST);
    $data['id'] = $id;

    if (count($galat) === 0) {
        $update = $pdo->prepare(
            'UPDATE kos SET nama = :nama, alamat = :alamat, kota = :kota, tipe = :tipe, harga = :harga,
                    ukuran = :ukuran, fasilitas = :fasilitas, deskripsi = :deskripsi, kontak = :kontak,
                    foto = :foto, tersedia = :tersedia
             WHERE id = :id'
        );
        $update->execute($data);
        redirect('/kos/' . $id . '?pesan=diubah');
    }
    http_response_code(422);
}

$judul  = 'Ubah ' . $data['nama'];
$aksi   = '/ubah/' . $id;
$tombol = 'Simpan perubahan';

require __DIR__ . '/../includes/header.php';
?>
<section class="judul-halaman">
    <h1>Ubah data kos</h1>
    <p>Perbarui informasi untuk <strong><?= e((string) $data['nama']) ?></strong>.</p>
</section>
<?php
require __DIR__ . '/../includes/form.php';
require __DIR__ . '/../includes/footer.php';
