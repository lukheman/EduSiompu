<?php

namespace App\Support;

class MapelIcon
{
    /**
     * Pemetaan kata kunci mata pelajaran ke ikon dan warna.
     *
     * Urutan penting: kata kunci yang lebih spesifik harus berada di atas
     * kata kunci yang lebih umum (misal "bahasa indonesia" sebelum "bahasa").
     *
     * @var array<int, array{kata: array<int, string>, icon: string, warna: string}>
     */
    private const KAMUS = [
        ['kata' => ['matematika', 'math'], 'icon' => 'fas fa-calculator', 'warna' => '#6366f1'],
        ['kata' => ['bahasa indonesia', 'b. indonesia'], 'icon' => 'fas fa-book-open', 'warna' => '#0ea5e9'],
        ['kata' => ['bahasa inggris', 'b. inggris'], 'icon' => 'fas fa-language', 'warna' => '#3b82f6'],
        ['kata' => ['bahasa jepang'], 'icon' => 'fas fa-torii-gate', 'warna' => '#ef4444'],
        ['kata' => ['bahasa arab'], 'icon' => 'fas fa-book-quran', 'warna' => '#10b981'],
        ['kata' => ['bahasa jawa', 'bahasa sunda', 'bahasa daerah', 'muatan lokal', 'mulok'], 'icon' => 'fas fa-scroll', 'warna' => '#f59e0b'],
        ['kata' => ['fisika'], 'icon' => 'fas fa-atom', 'warna' => '#0ea5e9'],
        ['kata' => ['kimia'], 'icon' => 'fas fa-flask', 'warna' => '#14b8a6'],
        ['kata' => ['biologi'], 'icon' => 'fas fa-dna', 'warna' => '#22c55e'],
        ['kata' => ['ipa', 'ilmu pengetahuan alam'], 'icon' => 'fas fa-microscope', 'warna' => '#84cc16'],
        ['kata' => ['sejarah'], 'icon' => 'fas fa-landmark', 'warna' => '#b45309'],
        ['kata' => ['geografi'], 'icon' => 'fas fa-globe', 'warna' => '#06b6d4'],
        ['kata' => ['ekonomi'], 'icon' => 'fas fa-coins', 'warna' => '#eab308'],
        ['kata' => ['akuntansi'], 'icon' => 'fas fa-file-invoice-dollar', 'warna' => '#16a34a'],
        ['kata' => ['sosiologi'], 'icon' => 'fas fa-users', 'warna' => '#8b5cf6'],
        ['kata' => ['antropologi'], 'icon' => 'fas fa-people-group', 'warna' => '#c026d3'],
        ['kata' => ['ips', 'ilmu pengetahuan sosial'], 'icon' => 'fas fa-earth-asia', 'warna' => '#0d9488'],
        ['kata' => ['ppkn', 'pkn', 'pancasila', 'kewarganegaraan'], 'icon' => 'fas fa-scale-balanced', 'warna' => '#dc2626'],
        ['kata' => ['kristen'], 'icon' => 'fas fa-cross', 'warna' => '#7c3aed'],
        ['kata' => ['katolik'], 'icon' => 'fas fa-church', 'warna' => '#9333ea'],
        ['kata' => ['hindu'], 'icon' => 'fas fa-om', 'warna' => '#ea580c'],
        ['kata' => ['budha', 'buddha'], 'icon' => 'fas fa-dharmachakra', 'warna' => '#d97706'],
        ['kata' => ['agama', 'pai'], 'icon' => 'fas fa-mosque', 'warna' => '#059669'],
        ['kata' => ['musik'], 'icon' => 'fas fa-music', 'warna' => '#a855f7'],
        ['kata' => ['tari'], 'icon' => 'fas fa-shoe-prints', 'warna' => '#f43f5e'],
        ['kata' => ['teater', 'drama'], 'icon' => 'fas fa-masks-theater', 'warna' => '#be123c'],
        ['kata' => ['seni budaya', 'seni'], 'icon' => 'fas fa-palette', 'warna' => '#ec4899'],
        ['kata' => ['pjok', 'olahraga', 'jasmani', 'penjaskes'], 'icon' => 'fas fa-futbol', 'warna' => '#f97316'],
        ['kata' => ['informatika', 'tik', 'komputer'], 'icon' => 'fas fa-laptop-code', 'warna' => '#4f46e5'],
        ['kata' => ['prakarya', 'kerajinan'], 'icon' => 'fas fa-hammer', 'warna' => '#a16207'],
        ['kata' => ['bimbingan konseling', 'bimbingan dan konseling', 'bk'], 'icon' => 'fas fa-handshake', 'warna' => '#0891b2'],
        ['kata' => ['kewirausahaan'], 'icon' => 'fas fa-briefcase', 'warna' => '#65a30d'],
        ['kata' => ['kesehatan'], 'icon' => 'fas fa-heart-pulse', 'warna' => '#e11d48'],
        ['kata' => ['perpustakaan'], 'icon' => 'fas fa-book-bookmark', 'warna' => '#7c2d12'],
        ['kata' => ['bahasa'], 'icon' => 'fas fa-language', 'warna' => '#2563eb'],
    ];

    /**
     * Ikon cadangan untuk mata pelajaran yang tidak dikenali.
     *
     * @var array<int, array{icon: string, warna: string}>
     */
    private const CADANGAN = [
        ['icon' => 'fas fa-book', 'warna' => '#64748b'],
        ['icon' => 'fas fa-graduation-cap', 'warna' => '#6366f1'],
        ['icon' => 'fas fa-lightbulb', 'warna' => '#eab308'],
        ['icon' => 'fas fa-pen-ruler', 'warna' => '#0ea5e9'],
        ['icon' => 'fas fa-chalkboard-user', 'warna' => '#10b981'],
        ['icon' => 'fas fa-scroll', 'warna' => '#f59e0b'],
        ['icon' => 'fas fa-shapes', 'warna' => '#ec4899'],
        ['icon' => 'fas fa-cubes', 'warna' => '#8b5cf6'],
        ['icon' => 'fas fa-bookmark', 'warna' => '#ef4444'],
        ['icon' => 'fas fa-layer-group', 'warna' => '#14b8a6'],
    ];

    /**
     * Tentukan ikon dan warna untuk sebuah nama mata pelajaran.
     *
     * @return array{icon: string, warna: string}
     */
    public static function untuk(?string $nama): array
    {
        $nama = strtolower(trim((string) $nama));

        foreach (self::KAMUS as $entri) {
            foreach ($entri['kata'] as $kata) {
                if ($kata !== '' && str_contains($nama, $kata)) {
                    return [
                        'icon' => $entri['icon'],
                        'warna' => $entri['warna'],
                    ];
                }
            }
        }

        $index = abs((int) crc32($nama)) % count(self::CADANGAN);

        return self::CADANGAN[$index];
    }
}
