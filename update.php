#!/usr/bin/env php
<?php

require __DIR__.'/helpers.php';

/**
 * Script Update Project Laravel (Versi Sederhana)
 *
 * 1. Mengambil pembaruan secara paksa dari repository git (Fetch & Hard Reset)
 * 2. Memperbarui dependensi (Composer)
 * 3. Reset dan migrasi database
 * 4. Bersihkan cache
 */

$dir = __DIR__;
$start = microtime(true);
chdir($dir);

out('');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('|'.str_pad('  SIMKA - SCRIPT UPDATE APLIKASI', 63).'|', 'bold', 'green');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('');
info('Memulai pembaruan sistem. Mohon tunggu...');
out('');

// ============================================================
//  Langkah 1 – Git Fetch & Reset (Force Pull)
// ============================================================
step(1, 'Mengunduh Pembaruan (Force Update)');

$branch = shell('git rev-parse --abbrev-ref HEAD');
if (empty($branch)) {
    $branch = 'main'; // Fallback
}

// Mengambil perubahan dari server origin
if (!run('git fetch origin')) {
    err('Gagal terhubung ke internet atau server pembaruan!');
    exit(1);
}

// Memaksa sistem lokal sama persis dengan server (menghapus perubahan lokal yang konflik)
if (!run("git reset --hard origin/{$branch}")) {
    err('Gagal menerapkan pembaruan sistem!');
    exit(1);
}
ok('Pembaruan sistem berhasil diterapkan!');

// ============================================================
//  Langkah 2 – Update Composer
// ============================================================
step(2, 'Memperbarui Modul & Dependensi');
if (!run('composer install --no-interaction --optimize-autoloader')) {
    err('Gagal memperbarui modul sistem!');
    exit(1);
}
ok('Modul sistem berhasil diperbarui!');

// ============================================================
//  Langkah 3 – Database
// ============================================================
step(3, 'Memperbarui & Mereset Database');
if (!run('php artisan migrate:fresh --seed --force')) {
    err('Gagal memperbarui database aplikasi!');
    exit(1);
}
ok('Database berhasil diperbarui dan dikembalikan ke awal!');

// ============================================================
//  Langkah 4 – Bersihkan Cache
// ============================================================
step(4, 'Membersihkan Cache');
run('php artisan optimize:clear');
ok('Cache sistem berhasil dibersihkan!');

// ============================================================
//  Selesai
// ============================================================
$duration = round(microtime(true) - $start, 2);

out('');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('|'.str_pad('  ✅  UPDATE BERHASIL! APLIKASI SIAP DIGUNAKAN', 63).'|', 'bold', 'green');
out('+'.str_repeat('-', 63).'+', 'bold', 'green');
out('');
info("Seluruh proses pembaruan selesai dalam {$duration} detik.");
out('');
out('  Untuk mulai menjalankan aplikasi, silakan jalankan:', 'yellow');
out('  php serve.php');
out('');

if (function_exists('showContact')) {
    showContact();
}
