# KosanKu

Situs pencarian kos untuk mahasiswa di sekitar kampus Purwokerto. Proyek akhir mata kuliah **Pemrograman Web I (IF21305)**, Informatika, Universitas Jenderal Soedirman.

Pengguna dapat mencari kos berdasarkan kata kunci, tipe (putra/putri/campur), dan batas harga, lalu menambah, mengubah, dan menghapus data kos.

- Nama: Devin Rafa Haryanto
- NIM: H1D025066

## Teknologi

| Lapisan | Pilihan |
|---|---|
| Tampilan | HTML, CSS, dan JavaScript murni (tanpa framework) |
| Sisi server | PHP 8 native (PDO) |
| Basis data | MySQL / MariaDB |
| Hosting | Vercel (runtime PHP komunitas `vercel-php`) |

## Pemetaan ke CPMK

| CPMK | Bukti di proyek |
|---|---|
| 01 Dasar pemrograman web dan teknologi | Arsitektur client-server: browser meminta halaman, PHP memproses, MySQL menyimpan data (lihat bagian Struktur) |
| 02 Dokumen HTML dan CSS | `includes/header.php`, `includes/footer.php`, `public/assets/css/style.css` |
| 03 Variasi layout HTML dan JavaScript | Layout grid/flex responsif di CSS; `public/assets/js/app.js` (validasi form, pratinjau foto, dialog hapus) |
| 04 Dasar PHP (variabel, sintaks) | Variabel, konstanta, fungsi di `includes/helpers.php` dan `includes/config.php` |
| 05 Perulangan dan seleksi kondisi PHP | `foreach`, `while`, `for` pada `api/index.php`; `if/elseif/else` dan `switch` pada `includes/helpers.php` dan `api/index.php` |
| 06 CRUD MySQL | Create `api/create.php`, Read `api/index.php` + `api/detail.php`, Update `api/edit.php`, Delete `api/delete.php`; skema di `database/schema.sql` |
| 07 PHP + MySQL di hosting gratis | Konfigurasi `vercel.json` dan panduan deploy di bawah |

## Struktur folder

```
api/              halaman PHP (satu berkas = satu halaman, termasuk login/logout)
includes/         konfigurasi, koneksi database, autentikasi, helper, header, footer, form
database/         schema.sql (tabel + data contoh)
public/assets/    css, js, dan foto contoh (SVG)
router.php        router untuk server PHP bawaan saat pengembangan lokal
vercel.json       pengaturan runtime PHP dan rute di Vercel
```

## Menjalankan di komputer sendiri

Prasyarat: PHP 8.1+ dengan ekstensi `pdo_mysql`, serta MySQL atau MariaDB.

```bash
# 1. Buat database dan isi data contoh
mysql -u root -p -e "CREATE DATABASE kosanku CHARACTER SET utf8mb4"
mysql -u root -p kosanku < database/schema.sql

# 2. Atur koneksi (salin lalu sesuaikan)
cp .env.example .env     # lalu ekspor variabelnya, atau set langsung di terminal
export DB_HOST=127.0.0.1 DB_NAME=kosanku DB_USER=root DB_PASS=

# 3. Jalankan server
php -S 127.0.0.1:8000 -t public router.php
```

Buka http://127.0.0.1:8000.

## Deploy ke Vercel

Vercel tidak menyediakan MySQL, jadi database harus berada di layanan MySQL online. Pilih penyedia yang menawarkan paket gratis (misalnya TiDB Cloud atau Aiven; periksa ketentuan terbaru masing-masing), buat database, lalu impor `database/schema.sql`.

1. Push repositori ini ke GitHub.
2. Di Vercel pilih **Add New > Project** dan impor repositori. Framework preset: **Other**.
3. Tambahkan **Environment Variables**:

   | Nama | Keterangan |
   |---|---|
   | `DB_HOST` | host MySQL dari penyedia |
   | `DB_PORT` | biasanya `3306` (sesuaikan dengan penyedia) |
   | `DB_NAME` | nama database |
   | `DB_USER` | pengguna database |
   | `DB_PASS` | kata sandi database |
   | `DB_SSL` | isi `1` bila penyedia mewajibkan koneksi TLS |
   | `ADMIN_PASSWORD` | kata sandi admin untuk menambah, mengubah, dan menghapus data |
   | `AUTH_SECRET` | string acak panjang untuk menandatangani cookie login |

4. Klik **Deploy**. Rute diatur oleh `vercel.json`.

## Catatan keamanan

- Semua query memakai prepared statement (PDO) dan semua keluaran di-escape dengan `htmlspecialchars`.
- Kredensial database tidak disimpan di repositori; diambil dari variabel lingkungan.
- Vercel bersifat serverless, jadi `$_SESSION` berbasis berkas tidak andal. Login admin memakai cookie bertanda tangan HMAC (`includes/auth.php`): server tidak menyimpan sesi apa pun, cukup memverifikasi tanda tangan dan masa berlaku (2 jam) di setiap permintaan. Cookie bersifat `HttpOnly`, `Secure` (di HTTPS), dan `SameSite=Lax`.
- Pengunjung umum hanya bisa melihat dan mencari data. Tambah, ubah, dan hapus memerlukan login di `/masuk`. Bila `ADMIN_PASSWORD` tidak diisi, aplikasi berjalan dalam mode terbuka (hanya untuk pengembangan lokal).
- Data kos pada `schema.sql` adalah data fiktif.
