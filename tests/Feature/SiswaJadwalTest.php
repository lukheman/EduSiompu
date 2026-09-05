<?php

use App\Livewire\Siswa\JadwalList;
use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;
use Carbon\Carbon;
use Livewire\Livewire;

function tanggalUntukHari(string $hari): string
{
    $offset = ['Senin' => 0, 'Selasa' => 1, 'Rabu' => 2, 'Kamis' => 3, 'Jumat' => 4, 'Sabtu' => 5, 'Minggu' => 6][$hari];

    return Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays($offset)->format('Y-m-d');
}

it('guests cannot access siswa jadwal page', function () {
    $this->get(route('siswa.jadwal'))->assertRedirect(route('login'));
});

it('siswa can view jadwal pelajaran of their own class', function () {
    $siswa = Siswa::factory()->create();

    $guruAmpu = GuruAmpu::factory()->create(['id_kelas' => $siswa->id_kelas]);
    $jadwal = JadwalPelajaran::factory()->create(['id_guru_ampu' => $guruAmpu->id_guru_ampu]);

    $this->actingAs($siswa, 'siswa')
        ->get(route('siswa.jadwal'))
        ->assertSuccessful()
        ->assertSeeLivewire(JadwalList::class);

    Livewire::actingAs($siswa, 'siswa')
        ->test(JadwalList::class)
        ->set('tanggal', tanggalUntukHari($jadwal->hari))
        ->assertSuccessful()
        ->assertSee($guruAmpu->mataPelajaran->nama_mapel)
        ->assertSee($jadwal->hari);
});

it('jadwal follows the selected tanggal', function () {
    $siswa = Siswa::factory()->create();

    $guruAmpu = GuruAmpu::factory()->create(['id_kelas' => $siswa->id_kelas]);
    $jadwal = JadwalPelajaran::factory()->create([
        'id_guru_ampu' => $guruAmpu->id_guru_ampu,
        'hari' => 'Senin',
    ]);

    Livewire::actingAs($siswa, 'siswa')
        ->test(JadwalList::class)
        ->set('tanggal', tanggalUntukHari('Senin'))
        ->assertSee($guruAmpu->mataPelajaran->nama_mapel)
        ->set('tanggal', tanggalUntukHari('Selasa'))
        ->assertDontSee($guruAmpu->mataPelajaran->nama_mapel);
});

it('siswa does not see jadwal from other classes', function () {
    $siswa = Siswa::factory()->create();

    $otherAmpu = GuruAmpu::factory()->create();
    JadwalPelajaran::factory()->create(['id_guru_ampu' => $otherAmpu->id_guru_ampu]);

    Livewire::actingAs($siswa, 'siswa')
        ->test(JadwalList::class)
        ->assertSuccessful()
        ->assertDontSee($otherAmpu->mataPelajaran->nama_mapel);
});
