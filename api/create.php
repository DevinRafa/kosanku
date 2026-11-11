<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

wajib_masuk();

$data = [
    'nama' => '', 'alamat' => '', 'kota' => 'Purwokerto', 'tipe' => '', 'harga' => '',
    'ukuran' => '', 'fasilitas' => '', 'deskripsi' => '', 'kontak' => '',
    'foto' => 'kos-01.svg', 'tersedia' => 1,
];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$data, $galat] = validasi_kos($_POST);

    if (count($galat) === 0) {
        $stmt = db()->prepare(
            'INSERT INTO kos (nama, alamat, kota, tipe, harga, ukuran, fasilitas, deskripsi, kontak, foto, tersedia)
             VALUES (:nama, :alamat, :kota, :tipe, :harga, :ukuran, :fasilitas, :deskripsi, :kontak, :foto, :tersedia)'
        );
        $stmt->execute($data);
        redirect('/kos/' . db()->lastInsertId() . '?pesan=dibuat');
    }
    http_response_code(422);
}

$judul  = 'Tambah kos';
$aksi   = '/tambah';
$tombol = 'Simpan kos';

require __DIR__ . '/../includes/header.php';
?>
<section class="judul-halaman">
    <h1>Tambah data kos</h1>
    <p>Isi data kos dengan lengkap supaya mudah ditemukan.</p>
</section>
<?php
require __DIR__ . '/../includes/form.php';
require __DIR__ . '/../includes/footer.php';
