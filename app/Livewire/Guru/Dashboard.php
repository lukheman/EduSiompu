<?php

namespace App\Livewire\Guru;

use App\Models\GuruAmpu;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Siswa;
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
        $mode = session('guru_mode', 'mata_pelajaran');
        $guruAmpus = collect();
        $guruAmpuIds = collect();

        $kelasWali = Kelas::where('id_guru', $guru->id_guru)
            ->withCount('siswa')
            ->orderBy('nama_kelas')
            ->get();

        if ($activeTa) {
            $guruAmpus = GuruAmpu::where('id_guru', $guru->id_guru)
                ->where('id_tahun_ajaran', $activeTa->id_tahun_ajaran)
                ->get();
                
            $guruAmpuIds = $guruAmpus->pluck('id_guru_ampu');
        }

        if ($mode === 'wali_kelas') {
            $stats = [
                'total_kelas' => $kelasWali->count(),
                'total_siswa' => $kelasWali->sum('siswa_count'),
                'total_materi' => 0,
                'total_jadwal' => \App\Models\JadwalPelajaran::whereIn('id_guru_ampu', $guruAmpuIds)->count(),
            ];
            $recentData = $kelasWali;
        } else {
            $stats = [
                'total_kelas' => $guruAmpus->unique('id_kelas')->count(),
                'total_mapel' => $guruAmpus->unique('id_mata_pelajaran')->count(),
                'total_materi' => Materi::whereIn('id_guru_ampu', $guruAmpuIds)->count(),
                'total_jadwal' => \App\Models\JadwalPelajaran::whereIn('id_guru_ampu', $guruAmpuIds)->count(),
            ];

            $recentData = $guruAmpus->load(['kelas', 'mataPelajaran']);
        }

        return view('livewire.guru.dashboard', compact('stats', 'recentData', 'activeTa', 'mode'));
    }
}
