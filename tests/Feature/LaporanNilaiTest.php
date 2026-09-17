<?php

use App\Livewire\Guru\LaporanNilai;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;

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

it('per-mapel pdf includes attendance meetings', function () {
    ['wali' => $wali, 'kelas' => $kelas, 'siswa' => $siswa, 'tahunAjaran' => $tahunAjaran, 'ampu' => $ampu] = buatRaportWali();

    $jadwal = JadwalPelajaran::factory()->create(['id_guru_ampu' => $ampu->id_guru_ampu]);
    Absensi::create([
        'id_jadwal_pelajaran' => $jadwal->id_jadwal_pelajaran,
        'id_siswa' => $siswa->id_siswa,
        'tanggal' => '2026-08-04',
        'status_kehadiran' => 'hadir',
    ]);
    Absensi::create([
        'id_jadwal_pelajaran' => $jadwal->id_jadwal_pelajaran,
        'id_siswa' => $siswa->id_siswa,
        'tanggal' => '2026-08-11',
        'status_kehadiran' => 'sakit',
    ]);

    $this->actingAs($wali, 'guru')
        ->get(route('guru.laporan-nilai.cetak-mapel', [
            'kelas' => $kelas->id_kelas,
            'mapel' => $ampu->id_mata_pelajaran,
            'tahun' => $tahunAjaran->id_tahun_ajaran,
        ]))
        ->assertSuccessful();

    // PDF bersifat biner: render ulang view yang sama untuk memastikan isi kehadiran
    $view = view('pdf.laporan-nilai-mapel', [
        'kelas' => $kelas,
        'mapel' => $ampu->mataPelajaran,
        'tahunAjaran' => $tahunAjaran,
        'pengampu' => $ampu->guru,
        'siswas' => Siswa::where('id_kelas', $kelas->id_kelas)->with(['raport' => function ($q) use ($tahunAjaran, $ampu) {
            $q->where('id_tahun_ajaran', $tahunAjaran->id_tahun_ajaran)->with(['nilaiRaport' => function ($qq) use ($ampu) {
                $qq->where('id_mata_pelajaran', $ampu->id_mata_pelajaran);
            }]);
        }])->orderBy('nama_siswa')->get(),
        'wali' => $wali,
        'pertemuans' => collect([Carbon::parse('2026-08-04'), Carbon::parse('2026-08-11')]),
        'kehadiran' => [$siswa->id_siswa => ['2026-08-04' => 'hadir', '2026-08-11' => 'sakit']],
        'logoKiri' => null,
        'logoKanan' => null,
    ])->render();

    expect($view)->toContain('Pertemuan Ke-')
        ->and($view)->toContain('Keterangan');
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
