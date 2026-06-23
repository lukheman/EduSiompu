<?php

namespace App\Livewire\Siswa;

use App\Models\Absensi;
use App\Models\Materi;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Siswa')]
class Dashboard extends Component
{
    public function render()
    {
        $siswa = Auth::guard('siswa')->user()->load('kelas');
        $activeTa = TahunAjaran::where('status_aktif', true)->first();
        
        $stats = [];
        $recentData = collect();

        if ($activeTa) {
            // Absensi Summary
            $absensi = Absensi::where('id_siswa', $siswa->id_siswa)
                ->whereHas('jadwalPelajaran.guruAmpu', function($q) use ($activeTa) {
                    $q->where('id_tahun_ajaran', $activeTa->id_tahun_ajaran);
                })
                ->get();
                
            $stats = [
                'hadir' => $absensi->filter(fn($a) => $a->status_kehadiran?->value === 'hadir')->count(),
                'sakit' => $absensi->filter(fn($a) => $a->status_kehadiran?->value === 'sakit')->count(),
                'izin' => $absensi->filter(fn($a) => $a->status_kehadiran?->value === 'izin')->count(),
                'alpa' => $absensi->filter(fn($a) => $a->status_kehadiran?->value === 'alpa')->count(),
            ];

            // Recent Materi
            $recentData = Materi::with(['guruAmpu.mataPelajaran', 'guruAmpu.guru'])
                ->whereHas('guruAmpu', function($q) use ($siswa, $activeTa) {
                    $q->where('id_kelas', $siswa->id_kelas)
                      ->where('id_tahun_ajaran', $activeTa->id_tahun_ajaran);
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return view('livewire.siswa.dashboard', compact('stats', 'recentData', 'activeTa'));
    }
}
