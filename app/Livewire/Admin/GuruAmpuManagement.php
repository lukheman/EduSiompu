<?php

namespace App\Livewire\Admin;

use App\Models\GuruAmpu;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Penugasan Guru')]
class GuruAmpuManagement extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    
    #[Url]
    public ?int $filter_tahun_ajaran = null;
    
    #[Url]
    public ?int $filter_guru = null;

    // Form fields
    public ?int $id_guru = null;
    public ?int $id_mata_pelajaran = null;
    public ?int $id_kelas = null;
    public ?int $id_tahun_ajaran = null;

    // State
    public ?int $viewingGuruId = null;
    public ?int $viewingTahunAjaranId = null;
    public bool $showViewModal = false;

    public function mount()
    {
        // Secara natural, admin ingin melihat tahun ajaran yang sedang berjalan
        $activeTa = TahunAjaran::where('status_aktif', true)->first();
        if ($activeTa && !$this->filter_tahun_ajaran) {
            $this->filter_tahun_ajaran = $activeTa->id_tahun_ajaran;
        }
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterTahunAjaran(): void { $this->resetPage(); }
    public function updatedFilterGuru(): void { $this->resetPage(); }

    public function openViewModal(int $id_guru, int $id_tahun_ajaran): void
    {
        $this->viewingGuruId = $id_guru;
        $this->viewingTahunAjaranId = $id_tahun_ajaran;
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingGuruId = null;
        $this->viewingTahunAjaranId = null;
    }

    public function render()
    {
        // We group by id_guru and id_tahun_ajaran to hide subjects/classes from main table
        $guruAmpuList = GuruAmpu::query()
            ->select('id_guru', 'id_tahun_ajaran')
            ->with(['guru', 'tahunAjaran'])
            ->when($this->filter_tahun_ajaran, fn($q) => $q->where('id_tahun_ajaran', $this->filter_tahun_ajaran))
            ->when($this->filter_guru, fn($q) => $q->where('id_guru', $this->filter_guru))
            ->when($this->search, function ($query) {
                $query->whereHas('guru', fn($sub) => $sub->where('nama_guru', 'like', '%' . $this->search . '%'));
            })
            ->groupBy('id_guru', 'id_tahun_ajaran')
            ->paginate(12);

        $viewingPenugasan = null;
        if ($this->viewingGuruId && $this->viewingTahunAjaranId) {
            $viewingPenugasan = GuruAmpu::with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran', 'jadwalPelajaran' => function($q) {
                $orderHari = "FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')";
                $q->orderByRaw($orderHari)->orderBy('jam_mulai');
            }])
            ->where('id_guru', $this->viewingGuruId)
            ->where('id_tahun_ajaran', $this->viewingTahunAjaranId)
            ->get();
        }

        return view('livewire.admin.guru-ampu-management', [
            'guruAmpuList' => $guruAmpuList,
            'gurus' => Guru::orderBy('nama_guru')->get(),
            'tahunAjarans' => TahunAjaran::orderBy('nama_tahun', 'desc')->get(),
            'viewingPenugasan' => $viewingPenugasan,
        ]);
    }
}
