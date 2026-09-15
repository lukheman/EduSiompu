<?php

use App\Http\Controllers\Admin\LogoutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Guru\LaporanNilaiController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\GuruAmpuManagement;
use App\Livewire\Admin\GuruManagement;
use App\Livewire\Admin\JadwalPelajaranManagement;
use App\Livewire\Admin\KelasManagement;
use App\Livewire\Admin\MataPelajaranManagement;
use App\Livewire\Admin\MateriManagement;
use App\Livewire\Admin\OrangTuaManagement;
use App\Livewire\Admin\Profile as AdminProfile;
use App\Livewire\Admin\SiswaManagement;
use App\Livewire\Admin\TahunAjaranManagement;
use App\Livewire\Auth\Register;
use App\Livewire\Guru\InputNilai;
use App\Livewire\Guru\JadwalAbsensi;
use App\Livewire\Guru\LaporanNilai;
use App\Livewire\Guru\Profile as GuruProfile;
use App\Livewire\Guru\TugasManagement;
use App\Livewire\OrangTua\AbsensiAnak;
use App\Livewire\OrangTua\RaportAnak;
use App\Livewire\Siswa\AbsensiList;
use App\Livewire\Siswa\JadwalList;
use App\Livewire\Siswa\MateriList;
use App\Livewire\Siswa\Profile as SiswaProfile;
use App\Livewire\Siswa\RaportSaya;
use App\Livewire\Siswa\TugasList;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::view('/', 'guest.landing')->name('home');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', Register::class)->name('register');
});

// Shared App Routes (Logout for all roles)
Route::middleware('auth:admin,guru,siswa,orang_tua,web')->group(function () {
    Route::post('/logout', [LogoutController::class, '__invoke'])->name('logout');
});

// Admin-only Routes
Route::prefix('admin')->middleware('auth:admin,web')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/tahun-ajaran', TahunAjaranManagement::class)->name('admin.tahun-ajaran');
    Route::get('/kelas', KelasManagement::class)->name('admin.kelas');
    Route::get('/mata-pelajaran', MataPelajaranManagement::class)->name('admin.mata-pelajaran');
    Route::get('/guru', GuruManagement::class)->name('admin.guru');
    Route::get('/siswa', SiswaManagement::class)->name('admin.siswa');
    Route::get('/orang-tua', OrangTuaManagement::class)->name('admin.orang-tua');
    Route::get('/guru-ampu', GuruAmpuManagement::class)->name('admin.guru-ampu');
    Route::get('/jadwal-pelajaran', JadwalPelajaranManagement::class)->name('admin.jadwal-pelajaran');

    Route::get('/profile', AdminProfile::class)->name('admin.profile');
});

// Guru-only Routes
Route::prefix('guru')->middleware('auth:guru,web')->group(function () {
    Route::get('/dashboard', App\Livewire\Guru\Dashboard::class)->name('guru.dashboard');
    Route::get('/materi', MateriManagement::class)->name('guru.materi');
    Route::get('/jadwal-absensi', JadwalAbsensi::class)->name('guru.jadwal-absensi');
    Route::get('/input-nilai', InputNilai::class)->name('guru.input-nilai');
    Route::get('/tugas', TugasManagement::class)->name('guru.tugas');
    Route::get('/laporan-nilai', LaporanNilai::class)->name('guru.laporan-nilai');
    Route::get('/laporan-nilai/cetak-mapel/{kelas}/{mapel}/{tahun}', [LaporanNilaiController::class, 'cetakMapel'])->name('guru.laporan-nilai.cetak-mapel');
    Route::get('/profil', GuruProfile::class)->name('guru.profile');
});

// Siswa-only Routes
Route::prefix('siswa')->middleware('auth:siswa,web')->group(function () {
    Route::get('/dashboard', App\Livewire\Siswa\Dashboard::class)->name('siswa.dashboard');
    Route::get('/jadwal-pelajaran', JadwalList::class)->name('siswa.jadwal');
    Route::get('/materi-belajar', MateriList::class)->name('siswa.materi');
    Route::get('/absensi-saya', AbsensiList::class)->name('siswa.absensi');
    Route::get('/raport-saya', RaportSaya::class)->name('siswa.raport');
    Route::get('/tugas', TugasList::class)->name('siswa.tugas');
    Route::get('/profil', SiswaProfile::class)->name('siswa.profile');
});

// Orang Tua-only Routes
Route::prefix('orang-tua')->middleware('auth:orang_tua,web')->group(function () {
    Route::get('/dashboard', App\Livewire\OrangTua\Dashboard::class)->name('orang-tua.dashboard');
    Route::get('/absensi-anak', AbsensiAnak::class)->name('orang-tua.absensi');
    Route::get('/raport-anak', RaportAnak::class)->name('orang-tua.raport');
});
