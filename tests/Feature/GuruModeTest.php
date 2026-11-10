<?php

use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\Kelas;

it('guru login redirects to mode selection', function () {
    $guru = Guru::factory()->create(['password' => bcrypt('password123')]);

    $this->post(route('login.submit'), [
        'role' => 'guru',
        'identifier' => $guru->nip,
        'password' => 'password123',
    ])
        ->assertRedirect(route('guru.mode'));
});

it('guru can select mata pelajaran mode', function () {
    $guru = Guru::factory()->create();
    GuruAmpu::factory()->create(['id_guru' => $guru->id_guru]);

    $this->actingAs($guru, 'guru')
        ->post(route('guru.mode.select'), ['mode' => 'mata_pelajaran'])
        ->assertRedirect(route('guru.dashboard'));

    expect(session('guru_mode'))->toBe('mata_pelajaran');
});

it('guru can select wali kelas mode', function () {
    $guru = Guru::factory()->create();
    Kelas::factory()->create(['id_guru' => $guru->id_guru]);

    $this->actingAs($guru, 'guru')
        ->post(route('guru.mode.select'), ['mode' => 'wali_kelas'])
        ->assertRedirect(route('guru.dashboard'));

    expect(session('guru_mode'))->toBe('wali_kelas');
});

it('guru mata pelajaran cannot open wali class reports', function () {
    $guru = Guru::factory()->create();
    GuruAmpu::factory()->create(['id_guru' => $guru->id_guru]);

    $this->actingAs($guru, 'guru')
        ->withSession(['guru_mode' => 'mata_pelajaran'])
        ->get(route('guru.laporan-nilai'))
        ->assertForbidden();

    $this->actingAs($guru, 'guru')
        ->withSession(['guru_mode' => 'mata_pelajaran'])
        ->get(route('guru.laporan-absensi'))
        ->assertForbidden();
});

it('wali kelas can open both class report pages', function () {
    $guru = Guru::factory()->create();
    Kelas::factory()->create(['id_guru' => $guru->id_guru]);

    $this->actingAs($guru, 'guru')
        ->withSession(['guru_mode' => 'wali_kelas'])
        ->get(route('guru.laporan-nilai'))
        ->assertSuccessful();

    $this->actingAs($guru, 'guru')
        ->withSession(['guru_mode' => 'wali_kelas'])
        ->get(route('guru.laporan-absensi'))
        ->assertSuccessful();
});

it('guru without selected mode is sent to mode selection', function () {
    $guru = Guru::factory()->create();

    $this->actingAs($guru, 'guru')
        ->get(route('guru.dashboard'))
        ->assertRedirect(route('guru.mode'));
});
