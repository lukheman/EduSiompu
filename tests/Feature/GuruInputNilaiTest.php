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
        ->assertSee('Rata2')
        ->set("nilaiData.{$siswa->id_siswa}.afektif_1", 90)
        ->set("nilaiData.{$siswa->id_siswa}.afektif_2", 90)
        ->set("nilaiData.{$siswa->id_siswa}.afektif_3", 90)
        ->set("nilaiData.{$siswa->id_siswa}.psikomotor_1", 80)
        ->set("nilaiData.{$siswa->id_siswa}.psikomotor_2", 80)
        ->set("nilaiData.{$siswa->id_siswa}.psikomotor_3", 80)
        ->set("nilaiData.{$siswa->id_siswa}.psikomotor_4", 80)
        ->set("nilaiData.{$siswa->id_siswa}.tugas_1", 70)
        ->set("nilaiData.{$siswa->id_siswa}.tugas_2", 70)
        ->set("nilaiData.{$siswa->id_siswa}.tugas_3", 70)
        ->set("nilaiData.{$siswa->id_siswa}.tugas_4", 70)
        ->set("nilaiData.{$siswa->id_siswa}.ulangan_harian_1", 80)
        ->set("nilaiData.{$siswa->id_siswa}.ulangan_harian_2", 80)
        ->set("nilaiData.{$siswa->id_siswa}.ulangan_harian_3", 80)
        ->set("nilaiData.{$siswa->id_siswa}.ulangan_semester", 90)
        ->assertSet("nilaiData.{$siswa->id_siswa}.raport", 82)
        ->call('simpan')
        ->assertHasNoErrors();

    $nilai = NilaiRaport::whereHas('raport', fn ($q) => $q->where('id_siswa', $siswa->id_siswa))
        ->where('id_mata_pelajaran', $ampu->id_mata_pelajaran)
        ->first();

    expect($nilai)->not->toBeNull()
        ->and($nilai->nilai_afektif_1)->toEqual(90)
        ->and($nilai->nilai_afektif_3)->toEqual(90)
        ->and($nilai->predikat_afektif)->toBe('A')
        ->and($nilai->rata_afektif)->toEqual(90)
        ->and($nilai->nilai_psikomotor_4)->toEqual(80)
        ->and($nilai->predikat_psikomotor)->toBe('B')
        ->and($nilai->rata_psikomotor)->toEqual(80)
        ->and($nilai->rata_tugas)->toEqual(70)
        ->and($nilai->rata_ulangan_harian)->toEqual(80)
        ->and($nilai->nilai_ulangan_semester)->toEqual(90)
        ->and($nilai->nilai_raport)->toEqual(82)
        ->and($nilai->predikat_raport)->toBe('B');
});

it('wali can manually override nilai raport', function () {
    ['wali' => $wali, 'kelas' => $kelas, 'ampu' => $ampu, 'siswa' => $siswa] = buatKelasPerwalian();

    Livewire::actingAs($wali, 'guru')
        ->test(InputNilai::class)
        ->set('selectedKelasId', $kelas->id_kelas)
        ->set('selectedAmpuId', $ampu->id_guru_ampu)
        ->set("nilaiData.{$siswa->id_siswa}.tugas_1", 70)
        ->set("nilaiData.{$siswa->id_siswa}.tugas_2", 80)
        ->assertSet("nilaiData.{$siswa->id_siswa}.raport", 75)
        ->set("nilaiData.{$siswa->id_siswa}.raport", 95)
        ->call('simpan')
        ->assertHasNoErrors();

    $nilai = NilaiRaport::whereHas('raport', fn ($q) => $q->where('id_siswa', $siswa->id_siswa))
        ->where('id_mata_pelajaran', $ampu->id_mata_pelajaran)
        ->first();

    expect($nilai->nilai_raport)->toEqual(95)
        ->and($nilai->predikat_raport)->toBe('A');
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
