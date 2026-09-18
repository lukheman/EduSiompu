<?php

namespace App\Livewire\KepalaSekolah;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Kepala Sekolah')]
class Dashboard extends Component
{
    public function render()
    {
        $activeTa = TahunAjaran::where('status_aktif', true)->first();

        $stats = [
            'total_guru' => Guru::count(),
            'total_siswa' => Siswa::count(),
            'total_kelas' => Kelas::count(),
            'total_mapel' => MataPelajaran::count(),
        ];

        $kelasList = Kelas::with(['waliKelas'])
            ->withCount('siswa')
            ->orderBy('nama_kelas')
            ->get();

        return view('livewire.kepala-sekolah.dashboard', compact('stats', 'kelasList', 'activeTa'));
    }
}
