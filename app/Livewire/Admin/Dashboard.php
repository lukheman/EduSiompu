<?php

namespace App\Livewire\Admin;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\GuruAmpu;
use App\Models\Materi;
use App\Models\Pertemuan;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $stats = [];
        $recentData = [];
        $activeTa = TahunAjaran::where('status_aktif', true)->first();
        
        $role = 'admin';
        
        $stats = [
            'total_siswa' => Siswa::count(),
            'total_guru' => Guru::count(),
            'total_kelas' => Kelas::count(),
            'total_mapel' => MataPelajaran::count(),
        ];
        
        $recentData = [
            'active_ta' => $activeTa
        ];

        return view('livewire.admin.dashboard', compact('role', 'stats', 'recentData', 'activeTa'));
    }
}
