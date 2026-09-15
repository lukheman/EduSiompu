<?php

use App\Livewire\Admin\TahunAjaranManagement;
use App\Models\Admin;
use App\Models\TahunAjaran;
use Livewire\Livewire;

it('admin can create tahun ajaran with periode dates', function () {
    $admin = Admin::factory()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(TahunAjaranManagement::class)
        ->set('nama_tahun', '2027/2028')
        ->set('semester', 'ganjil')
        ->set('tanggal_mulai', '2027-07-01')
        ->set('tanggal_akhir', '2027-12-31')
        ->set('status_aktif', false)
        ->call('save')
        ->assertHasNoErrors();

    $tahun = TahunAjaran::where('nama_tahun', '2027/2028')->first();

    expect($tahun)->not->toBeNull()
        ->and($tahun->tanggal_mulai->format('Y-m-d'))->toBe('2027-07-01')
        ->and($tahun->tanggal_akhir->format('Y-m-d'))->toBe('2027-12-31');
});

it('tanggal akhir cannot be before tanggal mulai', function () {
    $admin = Admin::factory()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(TahunAjaranManagement::class)
        ->set('nama_tahun', '2027/2028')
        ->set('semester', 'ganjil')
        ->set('tanggal_mulai', '2027-12-31')
        ->set('tanggal_akhir', '2027-07-01')
        ->set('status_aktif', false)
        ->call('save')
        ->assertHasErrors(['tanggal_akhir']);

    expect(TahunAjaran::where('nama_tahun', '2027/2028')->exists())->toBeFalse();
});
