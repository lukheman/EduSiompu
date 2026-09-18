<?php

use App\Livewire\KepalaSekolah\Dashboard;
use App\Livewire\KepalaSekolah\LaporanNilai;
use App\Livewire\KepalaSekolah\RaportSiswa;
use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\Kelas;
use App\Models\KepalaSekolah;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Livewire\Livewire;

it('kepala sekolah can login with email', function () {
    $kepsek = KepalaSekolah::factory()->create(['password' => bcrypt('password123')]);

    $this->post(route('login.submit'), [
        'role' => 'kepala_sekolah',
        'identifier' => $kepsek->email,
        'password' => 'password123',
    ])->assertRedirect(route('kepala-sekolah.dashboard'));

    $this->assertAuthenticatedAs($kepsek, 'kepala_sekolah');
});

it('kepala sekolah can open dashboard and laporan pages', function () {
    $kepsek = KepalaSekolah::factory()->create();
    $tahunAjaran = TahunAjaran::factory()->create(['status_aktif' => true]);
    $kelas = Kelas::factory()->create();
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
        'nilai_raport' => 80, 'predikat_raport' => 'B',
    ]);

    $this->actingAs($kepsek, 'kepala_sekolah')
        ->get(route('kepala-sekolah.dashboard'))
        ->assertSuccessful()
        ->assertSeeLivewire(Dashboard::class)
        ->assertSee($kelas->nama_kelas);

    Livewire::actingAs($kepsek, 'kepala_sekolah')
        ->test(LaporanNilai::class)
        ->set('selectedKelasId', $kelas->id_kelas)
        ->set('selectedTahunId', $tahunAjaran->id_tahun_ajaran)
        ->set('selectedMapelId', $ampu->id_mata_pelajaran)
        ->assertSee($siswa->nama_siswa);

    $response = $this->actingAs($kepsek, 'kepala_sekolah')
        ->get(route('kepala-sekolah.laporan-nilai.cetak-mapel', [
            'kelas' => $kelas->id_kelas,
            'mapel' => $ampu->id_mata_pelajaran,
            'tahun' => $tahunAjaran->id_tahun_ajaran,
        ]));

    $response->assertSuccessful();
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
    expect(substr($response->getContent(), 0, 5))->toBe('%PDF-');
});

it('kepala sekolah can view a student raport', function () {
    $kepsek = KepalaSekolah::factory()->create();
    $tahunAjaran = TahunAjaran::factory()->create(['status_aktif' => true]);
    $kelas = Kelas::factory()->create();
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
        'nilai_raport' => 85, 'predikat_raport' => 'B',
    ]);

    $this->actingAs($kepsek, 'kepala_sekolah')
        ->get(route('kepala-sekolah.raport'))
        ->assertSuccessful()
        ->assertSeeLivewire(RaportSiswa::class);

    Livewire::actingAs($kepsek, 'kepala_sekolah')
        ->test(RaportSiswa::class)
        ->set('selectedKelasId', $kelas->id_kelas)
        ->set('selectedSiswaId', $siswa->id_siswa)
        ->assertSee($siswa->nama_siswa)
        ->assertSee($ampu->mataPelajaran->nama_mapel)
        ->assertSee('85');
});

it('guest cannot access kepala sekolah pages', function () {
    $this->get(route('kepala-sekolah.dashboard'))->assertRedirect(route('login'));
    $this->get(route('kepala-sekolah.laporan-nilai'))->assertRedirect(route('login'));
    $this->get(route('kepala-sekolah.raport'))->assertRedirect(route('login'));
});

it('other roles cannot access kepala sekolah pages', function () {
    $guru = Guru::factory()->create();

    $this->actingAs($guru, 'guru')
        ->get(route('kepala-sekolah.dashboard'))
        ->assertRedirect(route('login'));
});
