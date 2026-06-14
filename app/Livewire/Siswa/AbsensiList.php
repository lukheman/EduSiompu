<?php

namespace App\Livewire\Siswa;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\Absensi;
use App\Models\TahunAjaran;
use App\Models\GuruAmpu;
use Illuminate\Support\Facades\Auth;

#[Title('Absensi Saya')]
class AbsensiList extends Component
{
    use WithPagination;

    public ?int $id_tahun_ajaran = null;
    public ?int $id_mata_pelajaran = null;

    public bool $showViewModal = false;
    public $viewingAbsensi = null;

    public function mount()
    {
        $activeTahun = TahunAjaran::where('status_aktif', true)->first();
        if ($activeTahun) {
            $this->id_tahun_ajaran = $activeTahun->id_tahun_ajaran;
        }
    }

    public function updatedIdTahunAjaran()
    {
        $this->id_mata_pelajaran = null; // reset subject filter
        $this->resetPage();
    }

    public function updatedIdMataPelajaran()
    {
        $this->resetPage();
    }

    public function openViewModal(int $id)
    {
        $this->viewingAbsensi = Absensi::with(['pertemuan.guruAmpu.mataPelajaran', 'pertemuan.guruAmpu.guru', 'pertemuan.guruAmpu.kelas'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingAbsensi = null;
    }

    public function render()
    {
        $siswa = Auth::guard('siswa')->user();

        $query = Absensi::with(['pertemuan.guruAmpu.mataPelajaran', 'pertemuan.guruAmpu.guru', 'pertemuan.guruAmpu.tahunAjaran'])
            ->where('id_siswa', $siswa->id_siswa);

        if ($this->id_tahun_ajaran) {
            $query->whereHas('pertemuan.guruAmpu', function($q) {
                $q->where('id_tahun_ajaran', $this->id_tahun_ajaran);
            });
        }

        if ($this->id_mata_pelajaran) {
            $query->whereHas('pertemuan.guruAmpu', function($q) {
                $q->where('id_mata_pelajaran', $this->id_mata_pelajaran);
            });
        }

        // Calculate Summary before pagination
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

        // Join to order by pertemuan.tanggal
        $query->join('pertemuan', 'absensi.id_pertemuan', '=', 'pertemuan.id_pertemuan')
            ->select('absensi.*') // keep only absensi columns for the model
            ->orderBy('pertemuan.tanggal', 'desc');

        $absensiList = $query->paginate(15);

        // Fetch subjects that this student is enrolled in
        $mapelOptions = GuruAmpu::with('mataPelajaran')
            ->where('id_kelas', $siswa->id_kelas)
            ->when($this->id_tahun_ajaran, function($q) {
                $q->where('id_tahun_ajaran', $this->id_tahun_ajaran);
            })
            ->get()
            ->pluck('mataPelajaran')
            ->unique('id_mata_pelajaran');

        $tahunAjaranOptions = TahunAjaran::orderBy('nama_tahun', 'desc')->get();

        return view('livewire.siswa.absensi-list', [
            'absensiList' => $absensiList,
            'summary' => $summary,
            'mapelOptions' => $mapelOptions,
            'tahunAjaranOptions' => $tahunAjaranOptions,
        ]);
    }
}
