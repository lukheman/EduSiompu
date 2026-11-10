<?php

use App\Livewire\Guru\KuisManagement;
use App\Livewire\Siswa\KuisList;
use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\PengerjaanKuis;
use App\Models\Siswa;
use App\Models\SoalKuis;
use App\Models\TahunAjaran;
use Livewire\Livewire;

function buatJadwalKuis(): array
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

    return compact('tahunAjaran', 'guru', 'kelas', 'ampu', 'jadwal');
}

function buatKuisDenganSoal(JadwalPelajaran $jadwal, array $atribut = []): array
{
    $kuis = Kuis::factory()->create(array_merge([
        'id_jadwal_pelajaran' => $jadwal->id_jadwal_pelajaran,
        'tenggat_waktu' => now()->addDay(),
        'durasi_menit' => 30,
    ], $atribut));

    $soal1 = SoalKuis::factory()->create([
        'id_kuis' => $kuis->id_kuis,
        'jawaban_benar' => 'A',
        'poin' => 1,
    ]);
    $soal2 = SoalKuis::factory()->create([
        'id_kuis' => $kuis->id_kuis,
        'jawaban_benar' => 'B',
        'poin' => 1,
    ]);

    return compact('kuis', 'soal1', 'soal2');
}

it('guru dapat membuat kuis untuk jadwal yang diampu', function () {
    ['guru' => $guru, 'jadwal' => $jadwal] = buatJadwalKuis();

    Livewire::actingAs($guru, 'guru')
        ->test(KuisManagement::class)
        ->call('createKuis')
        ->set('judul', 'Kuis Himpunan')
        ->set('deskripsi', 'Kerjakan dengan teliti.')
        ->set('tenggat_waktu', now()->addDays(3)->format('Y-m-d\TH:i'))
        ->set('durasi_menit', 45)
        ->call('saveKuis')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $kuis = Kuis::where('judul', 'Kuis Himpunan')->first();

    expect($kuis)->not->toBeNull()
        ->and($kuis->id_jadwal_pelajaran)->toBe($jadwal->id_jadwal_pelajaran)
        ->and($kuis->durasi_menit)->toBe(45);
});

it('guru tidak dapat mengelola kuis milik guru lain', function () {
    ['jadwal' => $jadwal] = buatJadwalKuis();
    ['kuis' => $kuis] = buatKuisDenganSoal($jadwal);
    $guruLain = Guru::factory()->create();

    Livewire::actingAs($guruLain, 'guru')
        ->test(KuisManagement::class)
        ->call('deleteKuis', $kuis->id_kuis)
        ->assertStatus(403);

    expect(Kuis::find($kuis->id_kuis))->not->toBeNull();
});

it('siswa hanya melihat kuis dari kelasnya', function () {
    ['kelas' => $kelas, 'jadwal' => $jadwal] = buatJadwalKuis();
    ['kuis' => $kuis] = buatKuisDenganSoal($jadwal, ['judul' => 'Kuis Kelas Sendiri']);

    $jadwalLain = JadwalPelajaran::factory()->create();
    Kuis::factory()->create([
        'id_jadwal_pelajaran' => $jadwalLain->id_jadwal_pelajaran,
        'judul' => 'Kuis Kelas Lain',
    ]);

    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    Livewire::actingAs($siswa, 'siswa')
        ->test(KuisList::class)
        ->assertSee($kuis->judul)
        ->assertDontSee('Kuis Kelas Lain');
});

it('siswa dapat mengerjakan kuis dan mendapat nilai otomatis', function () {
    ['kelas' => $kelas, 'jadwal' => $jadwal] = buatJadwalKuis();
    ['kuis' => $kuis, 'soal1' => $soal1, 'soal2' => $soal2] = buatKuisDenganSoal($jadwal);
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    $test = Livewire::actingAs($siswa, 'siswa')
        ->test(KuisList::class)
        ->call('bukaKuis', $kuis->id_kuis)
        ->assertSet('showKuisModal', true)
        ->assertSet('jawaban', []);

    $pengerjaan = PengerjaanKuis::where('id_kuis', $kuis->id_kuis)->sole();

    $test->set('jawaban.'.$soal1->id_soal, 'A')
        ->set('jawaban.'.$soal2->id_soal, 'B')
        ->call('submitKuis')
        ->assertHasNoErrors()
        ->assertSet('showKuisModal', false)
        ->assertSet('showHasilModal', true);

    $pengerjaan->refresh();

    expect($pengerjaan->nilai)->toBe(100)
        ->and($pengerjaan->jumlah_benar)->toBe(2)
        ->and($pengerjaan->jumlah_soal)->toBe(2)
        ->and($pengerjaan->selesai())->toBeTrue();
});

it('siswa tidak dapat memulai kuis yang sudah lewat tenggat', function () {
    ['kelas' => $kelas, 'jadwal' => $jadwal] = buatJadwalKuis();
    ['kuis' => $kuis] = buatKuisDenganSoal($jadwal, ['tenggat_waktu' => now()->subMinute()]);
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    Livewire::actingAs($siswa, 'siswa')
        ->test(KuisList::class)
        ->call('bukaKuis', $kuis->id_kuis)
        ->assertSet('showKuisModal', false);

    expect(PengerjaanKuis::count())->toBe(0);
});

it('membuka kuis dua kali melanjutkan pengerjaan yang sama', function () {
    ['kelas' => $kelas, 'jadwal' => $jadwal] = buatJadwalKuis();
    ['kuis' => $kuis, 'soal1' => $soal1] = buatKuisDenganSoal($jadwal);
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    $test = Livewire::actingAs($siswa, 'siswa')
        ->test(KuisList::class)
        ->call('bukaKuis', $kuis->id_kuis)
        ->set('jawaban.'.$soal1->id_soal, 'A')
        ->set('showKuisModal', false)
        ->call('bukaKuis', $kuis->id_kuis)
        ->assertSet('showKuisModal', true);

    expect(PengerjaanKuis::count())->toBe(1)
        ->and($test->get('jawaban')[$soal1->id_soal])->toBe('A');
});

it('siswa tidak dapat membuka kuis dari kelas lain', function () {
    ['jadwal' => $jadwal] = buatJadwalKuis();
    ['kuis' => $kuis] = buatKuisDenganSoal($jadwal);
    $siswa = Siswa::factory()->create(['id_kelas' => Kelas::factory()->create()->id_kelas]);

    Livewire::actingAs($siswa, 'siswa')
        ->test(KuisList::class)
        ->call('bukaKuis', $kuis->id_kuis)
        ->assertStatus(403);
});

it('siswa tidak bisa mengumpulkan jika masih ada soal kosong', function () {
    ['kelas' => $kelas, 'jadwal' => $jadwal] = buatJadwalKuis();
    ['kuis' => $kuis] = buatKuisDenganSoal($jadwal);
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    Livewire::actingAs($siswa, 'siswa')
        ->test(KuisList::class)
        ->call('bukaKuis', $kuis->id_kuis)
        ->call('submitKuis')
        ->assertHasErrors('jawaban')
        ->assertSet('showKuisModal', true);

    expect(PengerjaanKuis::sole()->selesai())->toBeFalse();
});

it('jawaban otomatis final saat waktu habis walau masih ada soal kosong', function () {
    ['kelas' => $kelas, 'jadwal' => $jadwal] = buatJadwalKuis();
    ['kuis' => $kuis, 'soal1' => $soal1] = buatKuisDenganSoal($jadwal);
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    $test = Livewire::actingAs($siswa, 'siswa')
        ->test(KuisList::class)
        ->call('bukaKuis', $kuis->id_kuis)
        ->set('jawaban.'.$soal1->id_soal, 'A');

    $pengerjaan = PengerjaanKuis::sole();
    $pengerjaan->update(['waktu_mulai' => now()->subMinutes(31)]);

    $test->call('submitKuis')
        ->assertSet('showKuisModal', false)
        ->assertSet('showHasilModal', true);

    $pengerjaan->refresh();

    expect($pengerjaan->selesai())->toBeTrue()
        ->and($pengerjaan->jumlah_benar)->toBe(1)
        ->and($pengerjaan->nilai)->toBe(50);
});
