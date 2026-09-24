<?php

use App\Livewire\Guru\JadwalAbsensi;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\NilaiRaport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Livewire\Livewire;

function buatJadwalGuru(): array
{
    $tahunAjaran = TahunAjaran::factory()->create(['status_aktif' => true]);
    $guru = Guru::factory()->create();
    $kelas = Kelas::factory()->create();
    $ampu = GuruAmpu::factory()->create([
        'id_guru' => $guru->id_guru,
        'id_kelas' => $kelas->id_kelas,
        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
    ]);
    $jadwal = JadwalPelajaran::factory()->create(['id_guru_ampu' => $ampu->id_guru_ampu]);
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    return compact('guru', 'kelas', 'ampu', 'jadwal', 'siswa');
}

it('guru sees meeting matrix after selecting jadwal', function () {
    ['guru' => $guru, 'jadwal' => $jadwal, 'siswa' => $siswa] = buatJadwalGuru();

    Absensi::create([
        'id_jadwal_pelajaran' => $jadwal->id_jadwal_pelajaran,
        'id_siswa' => $siswa->id_siswa,
        'tanggal' => '2026-08-04',
        'status_kehadiran' => 'hadir',
    ]);

    Livewire::actingAs($guru, 'guru')
        ->test(JadwalAbsensi::class)
        ->call('selectJadwal', $jadwal->id_jadwal_pelajaran)
        ->assertSee($siswa->nama_siswa)
        ->assertSee('Pertemuan Ke-')
        ->assertSee('Nilai');
});

it('clicking a cell cycles status and auto-saves', function () {
    ['guru' => $guru, 'jadwal' => $jadwal, 'siswa' => $siswa] = buatJadwalGuru();

    $test = Livewire::actingAs($guru, 'guru')
        ->test(JadwalAbsensi::class)
        ->call('selectJadwal', $jadwal->id_jadwal_pelajaran)
        ->assertSee('20/20 pertemuan');

    $tanggal = $test->get('pertemuans')[0];

    $test->call('toggleKehadiran', $siswa->id_siswa, $tanggal);
    expect(Absensi::where('id_siswa', $siswa->id_siswa)->first()->status_kehadiran->value)->toBe('hadir');

    $test->call('toggleKehadiran', $siswa->id_siswa, $tanggal);
    expect(Absensi::where('id_siswa', $siswa->id_siswa)->first()->status_kehadiran->value)->toBe('sakit');

    foreach (['izin', 'alpa'] as $expected) {
        $test->call('toggleKehadiran', $siswa->id_siswa, $tanggal);
        expect(Absensi::where('id_siswa', $siswa->id_siswa)->first()->status_kehadiran->value)->toBe($expected);
    }

    $test->call('toggleKehadiran', $siswa->id_siswa, $tanggal);
    expect(Absensi::where('id_siswa', $siswa->id_siswa)->count())->toBe(0);
});

it('meetings auto-fill to 20 following the jadwal weekday', function () {
    ['guru' => $guru, 'jadwal' => $jadwal, 'siswa' => $siswa] = buatJadwalGuru();

    Absensi::create([
        'id_jadwal_pelajaran' => $jadwal->id_jadwal_pelajaran,
        'id_siswa' => $siswa->id_siswa,
        'tanggal' => '2026-08-04',
        'status_kehadiran' => 'hadir',
    ]);

    $test = Livewire::actingAs($guru, 'guru')
        ->test(JadwalAbsensi::class)
        ->call('selectJadwal', $jadwal->id_jadwal_pelajaran);

    $pertemuans = $test->get('pertemuans');

    expect($pertemuans)->toHaveCount(20)
        ->and($pertemuans[0])->toBe('2026-08-04');

    $mapHari = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0];
    foreach (array_slice($pertemuans, 1) as $tanggal) {
        expect(Carbon::parse($tanggal)->dayOfWeek)->toBe($mapHari[$jadwal->hari]);
    }
});

it('guru can edit nilai directly from absensi matrix', function () {
    ['guru' => $guru, 'jadwal' => $jadwal, 'ampu' => $ampu, 'siswa' => $siswa] = buatJadwalGuru();

    Livewire::actingAs($guru, 'guru')
        ->test(JadwalAbsensi::class)
        ->call('selectJadwal', $jadwal->id_jadwal_pelajaran)
        ->set("nilaiEdit.{$siswa->id_siswa}.tugas_1", 80)
        ->set("nilaiEdit.{$siswa->id_siswa}.tugas_2", 90)
        ->set("nilaiEdit.{$siswa->id_siswa}.ulangan_semester", 85)
        ->assertSet("nilaiEdit.{$siswa->id_siswa}.raport", 85);

    $nilai = NilaiRaport::whereHas('raport', fn ($q) => $q->where('id_siswa', $siswa->id_siswa))
        ->where('id_mata_pelajaran', $ampu->id_mata_pelajaran)
        ->first();

    expect($nilai)->not->toBeNull()
        ->and($nilai->nilai_tugas_1)->toEqual(80)
        ->and($nilai->nilai_tugas_2)->toEqual(90)
        ->and($nilai->rata_tugas)->toEqual(85)
        ->and($nilai->nilai_raport)->toEqual(85);
});

it('guru cannot open another teacher jadwal', function () {
    ['jadwal' => $jadwal, 'siswa' => $siswa] = buatJadwalGuru();
    $guruLain = Guru::factory()->create();

    Livewire::actingAs($guruLain, 'guru')
        ->test(JadwalAbsensi::class)
        ->call('selectJadwal', $jadwal->id_jadwal_pelajaran)
        ->assertDontSee($siswa->nama_siswa)
        ->call('toggleKehadiran', $siswa->id_siswa, '2026-08-04');

    expect(Absensi::count())->toBe(0);
});
