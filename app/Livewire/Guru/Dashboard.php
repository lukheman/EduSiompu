<?php

namespace App\Livewire\Guru;

use App\Models\GuruAmpu;
use App\Models\Materi;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Guru')]
class Dashboard extends Component
{
    public function render()
    {
        $guru = Auth::guard('guru')->user();
        $activeTa = TahunAjaran::where('status_aktif', true)->first();
        
        $stats = [];
        $recentData = collect();

        if ($activeTa) {
            $guruAmpus = GuruAmpu::where('id_guru', $guru->id_guru)
                ->where('id_tahun_ajaran', $activeTa->id_tahun_ajaran)
                ->get();
                
            $guruAmpuIds = $guruAmpus->pluck('id_guru_ampu');

            $stats = [
                'total_kelas' => $guruAmpus->unique('id_kelas')->count(),
                'total_mapel' => $guruAmpus->unique('id_mata_pelajaran')->count(),
                'total_materi' => Materi::whereIn('id_guru_ampu', $guruAmpuIds)->count(),
                'total_jadwal' => \App\Models\JadwalPelajaran::whereIn('id_guru_ampu', $guruAmpuIds)->count(),
            ];

            $recentData = $guruAmpus->load(['kelas', 'mataPelajaran']); // Jadwal Mengajar
        }

        return view('livewire.guru.dashboard', compact('stats', 'recentData', 'activeTa'));
    }
}
