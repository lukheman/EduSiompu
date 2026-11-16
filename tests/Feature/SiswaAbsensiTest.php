<?php

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;

it('menampilkan ikon berbeda untuk setiap mata pelajaran pada kolom absensi', function () {
    $tahunAjaran = TahunAjaran::factory()->create(['status_aktif' => true]);
    $guru = Guru::factory()->create();
    $kelas = Kelas::factory()->create();
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    foreach (['Matematika', 'Fisika'] as $namaMapel) {
        $mapel = MataPelajaran::factory()->create(['nama_mapel' => $namaMapel]);
        $ampu = GuruAmpu::factory()->create([
            'id_guru' => $guru->id_guru,
            'id_kelas' => $kelas->id_kelas,
            'id_mata_pelajaran' => $mapel->id_mata_pelajaran,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        ]);
        $jadwal = JadwalPelajaran::factory()->create(['id_guru_ampu' => $ampu->id_guru_ampu]);

        Absensi::create([
            'id_jadwal_pelajaran' => $jadwal->id_jadwal_pelajaran,
            'id_siswa' => $siswa->id_siswa,
            'tanggal' => '2026-08-04',
            'status_kehadiran' => 'hadir',
        ]);
    }

    $this->actingAs($siswa, 'siswa')
        ->get(route('siswa.absensi'))
        ->assertSuccessful()
        ->assertSee('Matematika')
        ->assertSee('Fisika')
        ->assertSee('fa-calculator')
        ->assertSee('fa-atom');
});
