<?php

namespace App\Livewire\Siswa;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\Materi;
use App\Models\TahunAjaran;
use App\Models\GuruAmpu;
use Illuminate\Support\Facades\Auth;

#[Title('Materi Belajar')]
class MateriList extends Component
{
    use WithPagination;

    public ?int $id_tahun_ajaran = null;
    public ?int $id_mata_pelajaran = null;
    public string $search = '';

    public function mount()
    {
        $activeTahun = TahunAjaran::where('status_aktif', true)->first();
        if ($activeTahun) {
            $this->id_tahun_ajaran = $activeTahun->id_tahun_ajaran;
        }
    }

    public function updatedIdTahunAjaran()
    {
        $this->id_mata_pelajaran = null;
        $this->resetPage();
    }

    public function updatedIdMataPelajaran()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $siswa = Auth::guard('siswa')->user();

        // Base query for materi where the class matches student's class
        $query = Materi::with(['guruAmpu.mataPelajaran', 'guruAmpu.guru', 'guruAmpu.tahunAjaran'])
            ->whereHas('guruAmpu', function ($q) use ($siswa) {
                $q->where('id_kelas', $siswa->id_kelas);
                
                if ($this->id_tahun_ajaran) {
                    $q->where('id_tahun_ajaran', $this->id_tahun_ajaran);
                }
                if ($this->id_mata_pelajaran) {
                    $q->where('id_mata_pelajaran', $this->id_mata_pelajaran);
                }
            });

        if ($this->search) {
            $query->where('judul_materi', 'like', '%' . $this->search . '%');
        }

        $materiList = $query->orderBy('created_at', 'desc')->paginate(12);

        // Options for Filter
        $mapelOptions = GuruAmpu::with('mataPelajaran')
            ->where('id_kelas', $siswa->id_kelas)
            ->when($this->id_tahun_ajaran, function($q) {
                $q->where('id_tahun_ajaran', $this->id_tahun_ajaran);
            })
            ->get()
            ->pluck('mataPelajaran')
            ->unique('id_mata_pelajaran');

        $tahunAjaranOptions = TahunAjaran::orderBy('nama_tahun', 'desc')->get();

        return view('livewire.siswa.materi-list', [
            'materiList' => $materiList,
            'mapelOptions' => $mapelOptions,
            'tahunAjaranOptions' => $tahunAjaranOptions,
        ]);
    }
}
