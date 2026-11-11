// KosanKu - interaksi sisi klien (JavaScript murni, tanpa library)
(function () {
    'use strict';

    // Format angka rupiah untuk pratinjau harga di formulir
    function formatRupiah(angka) {
        return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }

    var inputHarga = document.querySelector('[data-harga]');
    var tampilHarga = document.querySelector('[data-harga-tampil]');
    if (inputHarga && tampilHarga) {
        var perbaruiHarga = function () {
            var digit = inputHarga.value.replace(/\D+/g, '');
            tampilHarga.textContent = digit ? formatRupiah(digit) + ' / bulan' : '';
        };
        inputHarga.addEventListener('input', perbaruiHarga);
        perbaruiHarga();
    }

    // Penghitung karakter deskripsi
    var area = document.querySelector('[data-hitung]');
    var hitung = document.querySelector('[data-hitung-tampil]');
    if (area && hitung) {
        var perbaruiHitung = function () {
            hitung.textContent = String(area.value.length);
        };
        area.addEventListener('input', perbaruiHitung);
        perbaruiHitung();
    }

    // Pratinjau foto saat pilihan berubah
    var pilihFoto = document.querySelector('[data-foto-pilih]');
    var gambar = document.querySelector('[data-foto-pratinjau]');
    if (pilihFoto && gambar) {
        pilihFoto.addEventListener('change', function () {
            gambar.src = '/assets/img/' + pilihFoto.value;
        });
    }

    // Validasi formulir di sisi klien (server tetap memvalidasi ulang)
    var form = document.querySelector('[data-form-kos]');
    if (form) {
        form.addEventListener('submit', function (ev) {
            var pesanAwal = form.querySelectorAll('.galat-js');
            pesanAwal.forEach(function (el) { el.remove(); });
            form.querySelectorAll('.isian-galat').forEach(function (el) {
                el.classList.remove('isian-galat');
            });

            var pertama = null;
            form.querySelectorAll('input[required], select[required]').forEach(function (el) {
                if (el.value.trim() === '') {
                    var wadah = el.closest('.isian');
                    wadah.classList.add('isian-galat');
                    var p = document.createElement('p');
                    p.className = 'galat galat-js';
                    p.setAttribute('role', 'alert');
                    p.textContent = 'Kolom ini wajib diisi.';
                    wadah.appendChild(p);
                    if (!pertama) { pertama = el; }
                }
            });

            if (pertama) {
                ev.preventDefault();
                pertama.focus();
            }
        });
    }

    // Dialog konfirmasi hapus
    var dialog = document.getElementById('dialog-hapus');
    var bukaHapus = document.querySelector('[data-buka-hapus]');
    var tutupHapus = document.querySelector('[data-tutup-hapus]');
    if (dialog && bukaHapus && typeof dialog.showModal === 'function') {
        bukaHapus.addEventListener('click', function () { dialog.showModal(); });
        if (tutupHapus) {
            tutupHapus.addEventListener('click', function () { dialog.close(); });
        }
        dialog.addEventListener('click', function (ev) {
            if (ev.target === dialog) { dialog.close(); }
        });
    }

    // Kirim filter otomatis saat dropdown pada pencarian diganti
    document.querySelectorAll('.cari select').forEach(function (el) {
        el.addEventListener('change', function () { el.form.submit(); });
    });
})();
