<?php

use App\Livewire\Guru\LaporanNilai;
use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\Kelas;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;

function buatRaportWali(): array
{
    $tahunAjaran = TahunAjaran::factory()->create(['status_aktif' => true]);
    $wali = Guru::factory()->create();
    $kelas = Kelas::factory()->create(['id_guru' => $wali->id_guru]);
    $ampu = GuruAmpu::factory()->create([
        'id_kelas' => $kelas->id_kelas,
        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
    ]);
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);
    $raport = Raport::create([
        'id_siswa' => $siswa->id_siswa,
        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
        'id_kelas' => $kelas->id_kelas,
    ]);
    NilaiRaport::create([
        'id_raport' => $raport->id_raport,
        'id_mata_pelajaran' => $ampu->id_mata_pelajaran,
        'nilai_afektif_1' => 80, 'nilai_afektif_2' => 80, 'nilai_afektif_3' => 80, 'predikat_afektif' => 'B',
        'nilai_raport' => 80, 'predikat_raport' => 'B',
    ]);

    return compact('tahunAjaran', 'wali', 'kelas', 'siswa', 'raport', 'ampu');
}

it('wali can open laporan page and download pdf', function () {
    ['wali' => $wali] = buatRaportWali();

    $this->actingAs($wali, 'guru')
        ->get(route('guru.laporan-nilai'))
        ->assertSuccessful()
        ->assertSeeLivewire(LaporanNilai::class);
});

it('wali can download per-mapel pdf', function () {
    ['wali' => $wali, 'kelas' => $kelas, 'siswa' => $siswa, 'tahunAjaran' => $tahunAjaran, 'ampu' => $ampu] = buatRaportWali();

    $params = ['kelas' => $kelas->id_kelas, 'mapel' => $ampu->id_mata_pelajaran, 'tahun' => $tahunAjaran->id_tahun_ajaran];

    $response = $this->actingAs($wali, 'guru')->get(route('guru.laporan-nilai.cetak-mapel', $params));

    $response->assertSuccessful();
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
    expect(substr($response->getContent(), 0, 5))->toBe('%PDF-');
});

it('non-wali guru cannot download per-mapel pdf', function () {
    ['kelas' => $kelas, 'tahunAjaran' => $tahunAjaran, 'ampu' => $ampu] = buatRaportWali();
    $guruLain = Guru::factory()->create();

    $this->actingAs($guruLain, 'guru')
        ->get(route('guru.laporan-nilai.cetak-mapel', ['kelas' => $kelas->id_kelas, 'mapel' => $ampu->id_mata_pelajaran, 'tahun' => $tahunAjaran->id_tahun_ajaran]))
        ->assertForbidden();
});

it('guest cannot access laporan pages', function () {
    ['tahunAjaran' => $tahunAjaran, 'kelas' => $kelas, 'ampu' => $ampu] = buatRaportWali();

    $this->get(route('guru.laporan-nilai'))->assertRedirect(route('login'));
    $this->get(route('guru.laporan-nilai.cetak-mapel', ['kelas' => $kelas->id_kelas, 'mapel' => $ampu->id_mata_pelajaran, 'tahun' => $tahunAjaran->id_tahun_ajaran]))
        ->assertRedirect(route('login'));
});
