<?php

use App\Enums\StatusSilabus;
use App\Livewire\Guru\Dashboard;
use App\Livewire\Guru\SilabusEditor;
use App\Livewire\KepalaSekolah\SilabusApproval;
use App\Livewire\Siswa\SilabusList;
use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\Kelas;
use App\Models\KepalaSekolah;
use App\Models\MataPelajaran;
use App\Models\Silabus;
use App\Models\SilabusItem;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Livewire\Livewire;

function buatPenugasanSilabus(array $atribut = []): array
{
    $tahunAjaran = TahunAjaran::factory()->create([
        'status_aktif' => true,
        'semester' => 'ganjil',
    ]);
    $guru = Guru::factory()->create();
    $kelas = Kelas::factory()->create(['nama_kelas' => 'X IPA 1']);
    $mapel = MataPelajaran::factory()->create(['nama_mapel' => 'Matematika']);
    $ampu = GuruAmpu::factory()->create(array_merge([
        'id_guru' => $guru->id_guru,
        'id_kelas' => $kelas->id_kelas,
        'id_mata_pelajaran' => $mapel->id_mata_pelajaran,
        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
    ], $atribut));

    return compact('tahunAjaran', 'guru', 'kelas', 'mapel', 'ampu');
}

function buatSilabusDenganItem(GuruAmpu $ampu, string $status = 'draft'): Silabus
{
    $silabus = Silabus::factory()->create([
        'id_guru_ampu' => $ampu->id_guru_ampu,
        'status' => $status,
        'diajukan_at' => $status === 'draft' ? null : now(),
    ]);

    SilabusItem::factory()->create([
        'id_silabus' => $silabus->id_silabus,
        'urutan' => 1,
        'materi_pokok' => 'Himpunan',
        'deskripsi' => 'Konsep dasar himpunan.',
        'alokasi_jam' => 4,
    ]);

    return $silabus;
}

it('guru dapat menambah materi silabus pada penugasan miliknya', function () {
    ['guru' => $guru, 'ampu' => $ampu] = buatPenugasanSilabus();

    Livewire::actingAs($guru, 'guru')
        ->test(SilabusEditor::class, ['guruAmpu' => $ampu])
        ->call('tambahItem')
        ->assertSet('showItemModal', true)
        ->set('materi_pokok', 'Himpunan dan Operasinya')
        ->set('deskripsi', 'Membahas irisan, gabungan, dan komplemen.')
        ->set('alokasi_jam', 6)
        ->call('simpanItem')
        ->assertHasNoErrors()
        ->assertSet('showItemModal', false);

    $silabus = Silabus::where('id_guru_ampu', $ampu->id_guru_ampu)->sole();

    expect($silabus->status)->toBe(StatusSilabus::DRAFT)
        ->and($silabus->items()->count())->toBe(1)
        ->and($silabus->items()->first()->materi_pokok)->toBe('Himpunan dan Operasinya')
        ->and($silabus->items()->first()->alokasi_jam)->toBe(6);
});

it('guru tidak dapat mengubah item silabus yang sedang diajukan', function () {
    ['guru' => $guru, 'ampu' => $ampu] = buatPenugasanSilabus();
    $silabus = buatSilabusDenganItem($ampu, 'diajukan');

    Livewire::actingAs($guru, 'guru')
        ->test(SilabusEditor::class, ['guruAmpu' => $ampu])
        ->call('tambahItem')
        ->assertStatus(403);

    expect($silabus->items()->count())->toBe(1);
});

it('guru tidak dapat mengubah item silabus yang sudah disetujui', function () {
    ['guru' => $guru, 'ampu' => $ampu] = buatPenugasanSilabus();
    $silabus = buatSilabusDenganItem($ampu, 'disetujui');

    Livewire::actingAs($guru, 'guru')
        ->test(SilabusEditor::class, ['guruAmpu' => $ampu])
        ->call('hapusItem', $silabus->items()->first()->id_silabus_item)
        ->assertStatus(403);
});

it('pengajuan silabus membutuhkan minimal satu materi', function () {
    ['guru' => $guru, 'ampu' => $ampu] = buatPenugasanSilabus();
    $silabus = Silabus::factory()->create([
        'id_guru_ampu' => $ampu->id_guru_ampu,
        'status' => 'draft',
    ]);

    Livewire::actingAs($guru, 'guru')
        ->test(SilabusEditor::class, ['guruAmpu' => $ampu])
        ->call('ajukan')
        ->assertHasErrors('silabus');

    expect($silabus->fresh()->status)->toBe(StatusSilabus::DRAFT);
});

it('guru dapat mengajukan silabus dan riwayat tercatat', function () {
    ['guru' => $guru, 'ampu' => $ampu] = buatPenugasanSilabus();
    $silabus = buatSilabusDenganItem($ampu);

    Livewire::actingAs($guru, 'guru')
        ->test(SilabusEditor::class, ['guruAmpu' => $ampu])
        ->call('ajukan')
        ->assertHasNoErrors();

    $silabus->refresh();

    expect($silabus->status)->toBe(StatusSilabus::DIAJUKAN)
        ->and($silabus->diajukan_at)->not->toBeNull()
        ->and($silabus->riwayat()->count())->toBe(1)
        ->and($silabus->riwayat()->first()->status)->toBe(StatusSilabus::DIAJUKAN);
});

it('kepala sekolah dapat menyetujui silabus yang diajukan', function () {
    ['ampu' => $ampu] = buatPenugasanSilabus();
    $silabus = buatSilabusDenganItem($ampu, 'diajukan');
    $kepala = KepalaSekolah::factory()->create();

    Livewire::actingAs($kepala, 'kepala_sekolah')
        ->test(SilabusApproval::class)
        ->call('bukaDetail', $silabus->id_silabus)
        ->assertSet('showDetail', true)
        ->set('catatan', 'Silabus sudah sesuai.')
        ->call('setujui')
        ->assertHasNoErrors()
        ->assertSet('showDetail', false);

    $silabus->refresh();

    expect($silabus->status)->toBe(StatusSilabus::DISETUJUI)
        ->and($silabus->id_kepala_sekolah)->toBe($kepala->id_kepala_sekolah)
        ->and($silabus->direview_at)->not->toBeNull()
        ->and($silabus->catatan_terakhir)->toBe('Silabus sudah sesuai.')
        ->and($silabus->riwayat()->latest('id_silabus_riwayat')->first()->status)->toBe(StatusSilabus::DISETUJUI);
});

it('kepala sekolah wajib menulis catatan saat meminta revisi', function () {
    ['ampu' => $ampu] = buatPenugasanSilabus();
    $silabus = buatSilabusDenganItem($ampu, 'diajukan');
    $kepala = KepalaSekolah::factory()->create();

    Livewire::actingAs($kepala, 'kepala_sekolah')
        ->test(SilabusApproval::class)
        ->call('bukaDetail', $silabus->id_silabus)
        ->call('mintaRevisi')
        ->assertHasErrors('catatan');

    expect($silabus->fresh()->status)->toBe(StatusSilabus::DIAJUKAN);

    Livewire::actingAs($kepala, 'kepala_sekolah')
        ->test(SilabusApproval::class)
        ->call('bukaDetail', $silabus->id_silabus)
        ->set('catatan', 'Tambahkan alokasi jam untuk setiap materi.')
        ->call('mintaRevisi')
        ->assertHasNoErrors();

    $silabus->refresh();

    expect($silabus->status)->toBe(StatusSilabus::REVISI)
        ->and($silabus->catatan_terakhir)->toBe('Tambahkan alokasi jam untuk setiap materi.');
});

it('guru dapat memperbaiki silabus revisi lalu mengajukan ulang', function () {
    ['guru' => $guru, 'ampu' => $ampu] = buatPenugasanSilabus();
    $silabus = buatSilabusDenganItem($ampu, 'revisi');

    Livewire::actingAs($guru, 'guru')
        ->test(SilabusEditor::class, ['guruAmpu' => $ampu])
        ->call('tambahItem')
        ->set('materi_pokok', 'Relasi dan Fungsi')
        ->call('simpanItem')
        ->assertHasNoErrors()
        ->call('ajukan')
        ->assertHasNoErrors();

    $silabus->refresh();

    expect($silabus->status)->toBe(StatusSilabus::DIAJUKAN)
        ->and($silabus->items()->count())->toBe(2)
        ->and($silabus->catatan_terakhir)->toBeNull();
});

it('kepala sekolah dapat membuka kunci silabus yang disetujui', function () {
    ['guru' => $guru, 'ampu' => $ampu] = buatPenugasanSilabus();
    $silabus = buatSilabusDenganItem($ampu, 'disetujui');
    $kepala = KepalaSekolah::factory()->create();

    Livewire::actingAs($kepala, 'kepala_sekolah')
        ->test(SilabusApproval::class)
        ->call('bukaDetail', $silabus->id_silabus)
        ->set('catatan', 'Ada penyesuaian kurikulum.')
        ->call('bukaKunci')
        ->assertHasNoErrors();

    expect($silabus->fresh()->status)->toBe(StatusSilabus::REVISI);

    Livewire::actingAs($guru, 'guru')
        ->test(SilabusEditor::class, ['guruAmpu' => $ampu])
        ->call('tambahItem')
        ->assertSet('showItemModal', true);
});

it('guru tidak dapat membuka silabus milik guru lain', function () {
    ['ampu' => $ampu] = buatPenugasanSilabus();
    $guruLain = Guru::factory()->create();
    GuruAmpu::factory()->create(['id_guru' => $guruLain->id_guru]);

    $this->actingAs($guruLain, 'guru')
        ->withSession(['guru_mode' => 'mata_pelajaran'])
        ->get(route('guru.silabus.editor', $ampu->id_guru_ampu))
        ->assertForbidden();
});

it('siswa hanya melihat silabus disetujui dari kelasnya', function () {
    ['kelas' => $kelas, 'ampu' => $ampu] = buatPenugasanSilabus();

    buatSilabusDenganItem($ampu, 'disetujui');

    $ampuDraft = GuruAmpu::factory()->create([
        'id_kelas' => $kelas->id_kelas,
        'id_tahun_ajaran' => $ampu->id_tahun_ajaran,
        'id_mata_pelajaran' => MataPelajaran::factory()->create(['nama_mapel' => 'Fisika'])->id_mata_pelajaran,
    ]);
    $silabusDraft = buatSilabusDenganItem($ampuDraft, 'draft');
    $silabusDraft->items()->update(['materi_pokok' => 'Materi Draft Rahasia']);

    $kelasLain = Kelas::factory()->create();
    $ampuLain = GuruAmpu::factory()->create([
        'id_kelas' => $kelasLain->id_kelas,
        'id_tahun_ajaran' => $ampu->id_tahun_ajaran,
        'id_mata_pelajaran' => MataPelajaran::factory()->create(['nama_mapel' => 'Kimia'])->id_mata_pelajaran,
    ]);
    $silabusKelasLain = buatSilabusDenganItem($ampuLain, 'disetujui');
    $silabusKelasLain->items()->update(['materi_pokok' => 'Materi Kelas Lain']);

    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    Livewire::actingAs($siswa, 'siswa')
        ->test(SilabusList::class)
        ->assertSee('Matematika')
        ->assertSee('Himpunan')
        ->assertSee('Disetujui')
        ->assertDontSee('Materi Draft Rahasia')
        ->assertDontSee('Materi Kelas Lain');
});

it('siswa tidak melihat silabus draft atau kelas lain pada halaman silabus', function () {
    ['kelas' => $kelas, 'ampu' => $ampu] = buatPenugasanSilabus();

    $ampuDraft = GuruAmpu::factory()->create([
        'id_kelas' => $kelas->id_kelas,
        'id_tahun_ajaran' => $ampu->id_tahun_ajaran,
        'id_mata_pelajaran' => MataPelajaran::factory()->create(['nama_mapel' => 'Fisika'])->id_mata_pelajaran,
    ]);
    $silabusDraft = buatSilabusDenganItem($ampuDraft, 'draft');
    $silabusDraft->items()->update(['materi_pokok' => 'Materi Draft Rahasia']);

    $kelasLain = Kelas::factory()->create();
    $ampulain = GuruAmpu::factory()->create([
        'id_kelas' => $kelasLain->id_kelas,
        'id_tahun_ajaran' => $ampu->id_tahun_ajaran,
        'id_mata_pelajaran' => MataPelajaran::factory()->create(['nama_mapel' => 'Kimia'])->id_mata_pelajaran,
    ]);
    $silabusKelasLain = buatSilabusDenganItem($ampulain, 'disetujui');
    $silabusKelasLain->items()->update(['materi_pokok' => 'Materi Kelas Lain']);

    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    Livewire::actingAs($siswa, 'siswa')
        ->test(SilabusList::class)
        ->assertDontSee('Materi Draft Rahasia')
        ->assertDontSee('Materi Kelas Lain');
});

it('guru dapat membuka halaman daftar silabus', function () {
    ['guru' => $guru, 'ampu' => $ampu] = buatPenugasanSilabus();
    buatSilabusDenganItem($ampu);

    $this->actingAs($guru, 'guru')
        ->withSession(['guru_mode' => 'mata_pelajaran'])
        ->get(route('guru.silabus'))
        ->assertOk()
        ->assertSee('Matematika')
        ->assertSee('Buka Silabus');
});

it('kepala sekolah dapat membuka halaman persetujuan silabus', function () {
    ['ampu' => $ampu] = buatPenugasanSilabus();
    buatSilabusDenganItem($ampu, 'diajukan');
    $kepala = KepalaSekolah::factory()->create();

    $this->actingAs($kepala, 'kepala_sekolah')
        ->get(route('kepala-sekolah.silabus'))
        ->assertOk()
        ->assertSee('Persetujuan Silabus')
        ->assertSee('Matematika');
});

it('siswa dapat membuka halaman silabus', function () {
    ['kelas' => $kelas, 'ampu' => $ampu] = buatPenugasanSilabus();
    buatSilabusDenganItem($ampu, 'disetujui');
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    $this->actingAs($siswa, 'siswa')
        ->get(route('siswa.silabus'))
        ->assertOk()
        ->assertSee('Matematika');
});

it('dashboard guru menampilkan ringkasan status silabus', function () {
    ['guru' => $guru, 'ampu' => $ampu] = buatPenugasanSilabus();
    buatSilabusDenganItem($ampu, 'disetujui');

    $this->withSession(['guru_mode' => 'mata_pelajaran']);

    Livewire::actingAs($guru, 'guru')
        ->test(Dashboard::class)
        ->assertSee('Status Silabus')
        ->assertSee('Disetujui');
});

it('dashboard siswa menampilkan silabus yang disetujui', function () {
    ['kelas' => $kelas, 'ampu' => $ampu] = buatPenugasanSilabus();
    buatSilabusDenganItem($ampu, 'disetujui');
    $siswa = Siswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

    Livewire::actingAs($siswa, 'siswa')
        ->test(App\Livewire\Siswa\Dashboard::class)
        ->assertSee('Silabus Kelas Anda')
        ->assertSee('Matematika');
});

it('tamu tidak dapat membuka halaman silabus', function () {
    $this->get(route('guru.silabus'))->assertRedirect(route('login'));
    $this->get(route('siswa.silabus'))->assertRedirect(route('login'));
    $this->get(route('kepala-sekolah.silabus'))->assertRedirect(route('login'));
});
