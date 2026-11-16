<?php

use App\Support\MapelIcon;

it('memberi ikon berbeda untuk setiap mata pelajaran umum', function () {
    $mapel = [
        'Matematika',
        'Bahasa Indonesia',
        'Bahasa Inggris',
        'Fisika',
        'Kimia',
        'Biologi',
        'Sejarah',
        'Geografi',
        'Ekonomi',
        'PPKn',
        'Pendidikan Agama Islam',
        'Seni Budaya',
        'PJOK',
        'Informatika',
        'Prakarya',
    ];

    $ikon = array_map(fn (string $nama) => MapelIcon::untuk($nama)['icon'], $mapel);

    expect(array_unique($ikon))->toHaveCount(count($mapel));
});

it('memberi ikon yang konsisten meski kapitalisasi berbeda', function () {
    expect(MapelIcon::untuk('Matematika'))->toBe(MapelIcon::untuk('matematika'));
});

it('memakai ikon dan warna cadangan untuk mata pelajaran yang tidak dikenal', function () {
    $ikon = MapelIcon::untuk('Mata Pelajaran Khusus');

    expect($ikon['icon'])->toStartWith('fas fa-')
        ->and($ikon['warna'])->toStartWith('#')
        ->and($ikon)->toBe(MapelIcon::untuk('Mata Pelajaran Khusus'));
});
