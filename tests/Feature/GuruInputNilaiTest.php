<?php

use App\Livewire\Admin\KelasManagement;
use App\Livewire\Guru\InputNilai;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\Kelas;
use App\Models\NilaiRaport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Livewire\Livewire;

function buatKelasPerwalian(): array
{
    $tahunAjaran = TahunAjaran::factory()->create(['status_aktif' => true]);
    $wali = Guru::factory()->create();
    $kelas = Kelas::factory()->create(['id_guru' => $wali->id_guru]);
    $ampu = GuruAmpu::factory()->create([
        'id_kelas' => $kelas->id_kelas,
        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
    ]);
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    return compact('tahunAjaran', 'wali', 'kelas', 'ampu', 'siswa');
}

it('wali kelas can input nilai for students in their class', function () {
    ['wali' => $wali, 'kelas' => $kelas, 'ampu' => $ampu, 'siswa' => $siswa] = buatKelasPerwalian();

    Livewire::actingAs($wali, 'guru')
        ->test(InputNilai::class)
        ->assertSee($kelas->nama_kelas)
        ->set('selectedKelasId', $kelas->id_kelas)
        ->assertSee($ampu->mataPelajaran->nama_mapel)
        ->set('selectedAmpuId', $ampu->id_guru_ampu)
        ->assertSee($siswa->nama_siswa)
        ->set("nilaiData.{$siswa->id_siswa}.pengetahuan", 85)
        ->set("nilaiData.{$siswa->id_siswa}.keterampilan", 90)
        ->call('simpan')
        ->assertHasNoErrors();

    $nilai = NilaiRaport::whereHas('raport', fn ($q) => $q->where('id_siswa', $siswa->id_siswa))
        ->where('id_mata_pelajaran', $ampu->id_mata_pelajaran)
        ->first();

    expect($nilai)->not->toBeNull()
        ->and($nilai->nilai_pengetahuan)->toEqual(85)
        ->and($nilai->predikat_pengetahuan)->toBe('B')
        ->and($nilai->nilai_keterampilan)->toEqual(90)
        ->and($nilai->predikat_keterampilan)->toBe('A');
});

it('guru who is not wali cannot input nilai', function () {
    ['kelas' => $kelas, 'ampu' => $ampu] = buatKelasPerwalian();
    $guruLain = Guru::factory()->create();

    Livewire::actingAs($guruLain, 'guru')
        ->test(InputNilai::class)
        ->assertSee('Bukan Wali Kelas')
        ->assertDontSee($kelas->nama_kelas)
        ->set('selectedKelasId', $kelas->id_kelas)
        ->set('selectedAmpuId', $ampu->id_guru_ampu)
        ->call('simpan');

    expect(NilaiRaport::count())->toBe(0);
});

it('admin can assign wali kelas', function () {
    $admin = Admin::factory()->create();
    $guru = Guru::factory()->create();
    $kelas = Kelas::factory()->create(['id_guru' => null]);

    Livewire::actingAs($admin, 'admin')
        ->test(KelasManagement::class)
        ->call('openEditModal', $kelas->id_kelas)
        ->set('id_guru', $guru->id_guru)
        ->call('save')
        ->assertHasNoErrors();

    expect($kelas->fresh()->id_guru)->toBe($guru->id_guru);
});
