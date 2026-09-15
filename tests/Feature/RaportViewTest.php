<?php

use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\Kelas;
use App\Models\NilaiRaport;
use App\Models\OrangTua;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;

function buatRaportLengkap(): array
{
    $tahunAjaran = TahunAjaran::factory()->create(['status_aktif' => true]);
    $wali = Guru::factory()->create();
    $kelas = Kelas::factory()->create(['id_guru' => $wali->id_guru]);
    $ampu = GuruAmpu::factory()->create([
        'id_kelas' => $kelas->id_kelas,
        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
    ]);
    $ortu = OrangTua::factory()->create();
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas, 'id_orang_tua' => $ortu->id_orang_tua]);
    $raport = Raport::create([
        'id_siswa' => $siswa->id_siswa,
        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        'id_kelas' => $kelas->id_kelas,
    ]);
    $nilai = NilaiRaport::create([
        'id_raport' => $raport->id_raport,
        'id_mata_pelajaran' => $ampu->id_mata_pelajaran,
        'nilai_afektif_1' => 80, 'nilai_afektif_2' => 80, 'nilai_afektif_3' => 80, 'predikat_afektif' => 'B',
        'nilai_psikomotor_1' => 80, 'nilai_psikomotor_2' => 80, 'nilai_psikomotor_3' => 80, 'nilai_psikomotor_4' => 80, 'predikat_psikomotor' => 'B',
        'nilai_tugas_1' => 80, 'nilai_tugas_2' => 80, 'nilai_tugas_3' => 80, 'nilai_tugas_4' => 80,
        'nilai_ulangan_harian_1' => 80, 'nilai_ulangan_harian_2' => 80, 'nilai_ulangan_harian_3' => 80,
        'nilai_ulangan_semester' => 80,
        'nilai_raport' => 80, 'predikat_raport' => 'B',
    ]);

    return compact('siswa', 'ortu', 'nilai');
}

it('siswa raport page shows extended nilai columns', function () {
    ['siswa' => $siswa, 'nilai' => $nilai] = buatRaportLengkap();

    $this->actingAs($siswa, 'siswa')
        ->get(route('siswa.raport'))
        ->assertSuccessful()
        ->assertSee('Afektif')
        ->assertSee('Psikomotor')
        ->assertSee('Nilai Raport')
        ->assertSee((string) $nilai->nilai_raport);
});

it('orang tua raport page shows extended nilai columns', function () {
    ['ortu' => $ortu, 'nilai' => $nilai] = buatRaportLengkap();

    $this->actingAs($ortu, 'orang_tua')
        ->get(route('orang-tua.raport'))
        ->assertSuccessful()
        ->assertSee('Afektif')
        ->assertSee('Psikomotor')
        ->assertSee('Nilai Raport')
        ->assertSee((string) $nilai->nilai_raport);
});
