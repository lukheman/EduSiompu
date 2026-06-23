<?php

namespace App\Livewire\OrangTua;

use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pemantauan Kehadiran Anak')]
class AbsensiAnak extends Component
{
    use WithPagination;

    public $id_anak = null;

    protected $queryString = ['id_anak'];

    public function updatingIdAnak()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orangTua = Auth::guard('orang_tua')->user();
        $anakList = $orangTua->siswa;

        if (!$this->id_anak && $anakList->count() > 0) {
            $this->id_anak = $anakList->first()->id_siswa;
        }

        $siswa = $anakList->where('id_siswa', $this->id_anak)->first() ?? $anakList->first();

        $query = Absensi::with(['jadwalPelajaran.guruAmpu.mataPelajaran', 'jadwalPelajaran.guruAmpu.guru', 'jadwalPelajaran.guruAmpu.kelas'])
            ->where('id_siswa', $siswa->id_siswa);



        $summaryQuery = clone $query;
        $summaryData = $summaryQuery->selectRaw('status_kehadiran, count(*) as total')
            ->groupBy('status_kehadiran')
            ->pluck('total', 'status_kehadiran')
            ->toArray();

        $summary = [
            'hadir' => $summaryData['hadir'] ?? 0,
            'sakit' => $summaryData['sakit'] ?? 0,
            'izin' => $summaryData['izin'] ?? 0,
            'alpa' => $summaryData['alpa'] ?? 0,
        ];

        $query->orderBy('tanggal', 'desc');

        $absensiList = $query->paginate(15);

        return view('livewire.orang-tua.absensi-anak', [
            'anakList' => $anakList,
            'absensiList' => $absensiList,
            'summary' => $summary,
        ]);
    }
}
