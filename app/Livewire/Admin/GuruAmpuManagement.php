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
    public ?int $editingGuruAmpuId = null;
    public ?int $viewingGuruAmpuId = null;
    public bool $showModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingGuruAmpuId = null;

    public function mount()
    {
        // Secara natural, admin ingin melihat tahun ajaran yang sedang berjalan
        $activeTa = TahunAjaran::where('status_aktif', true)->first();
        if ($activeTa && !$this->filter_tahun_ajaran) {
            $this->filter_tahun_ajaran = $activeTa->id_tahun_ajaran;
        }
    }

    protected function rules(): array
    {
        return [
            'id_guru' => ['required', 'exists:guru,id_guru'],
            'id_mata_pelajaran' => ['required', 'exists:mata_pelajaran,id_mata_pelajaran'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'id_tahun_ajaran' => ['required', 'exists:tahun_ajaran,id_tahun_ajaran'],
        ];
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterTahunAjaran(): void { $this->resetPage(); }
    public function updatedFilterGuru(): void { $this->resetPage(); }

    public function openCreateModal(): void
    {
        $this->resetForm();
        
        // Memudahkan admin: otomatis set form ke filter yang sedang dipilih
        $this->id_tahun_ajaran = $this->filter_tahun_ajaran;
        $this->id_guru = $this->filter_guru;

        $this->editingGuruAmpuId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $guruAmpuId): void
    {
        $guruAmpu = GuruAmpu::findOrFail($guruAmpuId);
        $this->editingGuruAmpuId = $guruAmpuId;
        $this->id_guru = $guruAmpu->id_guru;
        $this->id_mata_pelajaran = $guruAmpu->id_mata_pelajaran;
        $this->id_kelas = $guruAmpu->id_kelas;
        $this->id_tahun_ajaran = $guruAmpu->id_tahun_ajaran;
        $this->showModal = true;
    }

    public function openViewModal(int $guruAmpuId): void
    {
        $this->viewingGuruAmpuId = $guruAmpuId;
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingGuruAmpuId = null;
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Check for duplicates
        $query = GuruAmpu::where('id_guru', $this->id_guru)
            ->where('id_mata_pelajaran', $this->id_mata_pelajaran)
            ->where('id_kelas', $this->id_kelas)
            ->where('id_tahun_ajaran', $this->id_tahun_ajaran);

        if ($this->editingGuruAmpuId) {
            $query->where('id_guru_ampu', '!=', $this->editingGuruAmpuId);
        }

        if ($query->exists()) {
            $this->addError('id_guru', 'Kombinasi Guru, Mata Pelajaran, Kelas, dan Tahun Ajaran ini sudah ada.');
            return;
        }

        if ($this->editingGuruAmpuId) {
            GuruAmpu::findOrFail($this->editingGuruAmpuId)->update($validated);
            session()->flash('success', 'Data penugasan guru berhasil diperbarui.');
        } else {
            GuruAmpu::create($validated);
            session()->flash('success', 'Data penugasan guru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $guruAmpuId): void
    {
        $this->deletingGuruAmpuId = $guruAmpuId;
        $this->showDeleteModal = true;
    }

    public function deleteGuruAmpu(): void
    {
        if ($this->deletingGuruAmpuId) {
            GuruAmpu::destroy($this->deletingGuruAmpuId);
            session()->flash('success', 'Data penugasan berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deletingGuruAmpuId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingGuruAmpuId = null;
    }

    protected function resetForm(): void
    {
        $this->id_guru = null;
        $this->id_mata_pelajaran = null;
        $this->id_kelas = null;
        $this->id_tahun_ajaran = null;
        $this->editingGuruAmpuId = null;
    }

    public function render()
    {
        $guruAmpuList = GuruAmpu::query()
            ->with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran'])
            ->when($this->filter_tahun_ajaran, fn($q) => $q->where('id_tahun_ajaran', $this->filter_tahun_ajaran))
            ->when($this->filter_guru, fn($q) => $q->where('id_guru', $this->filter_guru))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('mataPelajaran', fn($sub) => $sub->where('nama_mapel', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('kelas', fn($sub) => $sub->where('nama_kelas', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('guru', fn($sub) => $sub->where('nama_guru', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $viewingGuruAmpu = $this->viewingGuruAmpuId 
            ? GuruAmpu::with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran'])->find($this->viewingGuruAmpuId) 
            : null;

        return view('livewire.admin.guru-ampu-management', [
            'guruAmpuList' => $guruAmpuList,
            'gurus' => Guru::orderBy('nama_guru')->get(),
            'mapels' => MataPelajaran::orderBy('nama_mapel')->get(),
            'kelasList' => Kelas::orderBy('nama_kelas')->get(),
            'tahunAjarans' => TahunAjaran::orderBy('nama_tahun', 'desc')->get(),
            'viewingGuruAmpu' => $viewingGuruAmpu,
        ]);
    }
}
