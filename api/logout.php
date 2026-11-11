<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    akhiri_sesi();
    redirect('/?pesan=keluar');
}

redirect('/');
