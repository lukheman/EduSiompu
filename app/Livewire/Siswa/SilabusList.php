<?php

namespace App\Livewire\Siswa;

use App\Models\GuruAmpu;
use App\Models\Silabus;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Silabus Pembelajaran')]
class SilabusList extends Component
{
    public ?int $id_mata_pelajaran = null;

    public function updatedIdMataPelajaran(): void
    {
        $this->resetValidation();
    }

    public function render()
    {
        $siswa = Auth::guard('siswa')->user();
        $activeTa = TahunAjaran::where('status_aktif', true)->first();

        $silabusList = Silabus::disetujui()
            ->with(['guruAmpu.mataPelajaran', 'guruAmpu.guru', 'guruAmpu.tahunAjaran', 'items'])
            ->whereHas('guruAmpu', function ($q) use ($siswa, $activeTa) {
                $q->where('id_kelas', $siswa->id_kelas);

                if ($activeTa) {
                    $q->where('id_tahun_ajaran', $activeTa->id_tahun_ajaran);
                }

                if ($this->id_mata_pelajaran) {
                    $q->where('id_mata_pelajaran', $this->id_mata_pelajaran);
                }
            })
            ->get()
            ->sortBy(fn ($silabus) => $silabus->guruAmpu?->mataPelajaran?->nama_mapel)
            ->values();

        $mapelOptions = GuruAmpu::with('mataPelajaran')
            ->where('id_kelas', $siswa->id_kelas)
            ->when($activeTa, fn ($q) => $q->where('id_tahun_ajaran', $activeTa->id_tahun_ajaran))
            ->get()
            ->pluck('mataPelajaran')
            ->filter()
            ->unique('id_mata_pelajaran');

        return view('livewire.siswa.silabus-list', [
            'silabusList' => $silabusList,
            'mapelOptions' => $mapelOptions,
            'activeTa' => $activeTa,
        ]);
    }
}
