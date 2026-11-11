<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

// Penghapusan hanya lewat POST agar tidak terpicu oleh tautan biasa.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    redirect('/');
}

$id = (int) ($_POST['id'] ?? 0);

if (!pin_valid()) {
    redirect('/kos/' . $id . '?pesan=pin');
}

$stmt = db()->prepare('DELETE FROM kos WHERE id = :id');
$stmt->execute([':id' => $id]);

redirect('/?pesan=dihapus');
