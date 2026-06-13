<?php

namespace App\Livewire\Admin;

use App\Models\GuruAmpu;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Guru Ampu')]
class GuruAmpuManagement extends Component
{
    use WithPagination;

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form fields
    public ?int $id_guru = null;
    public ?int $id_mata_pelajaran = null;
    public ?int $id_kelas = null;
    public ?int $id_tahun_ajaran = null;

    // State
    public ?int $editingGuruAmpuId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingGuruAmpuId = null;

    protected function rules(): array
    {
        return [
            'id_guru' => ['required', 'exists:guru,id_guru'],
            'id_mata_pelajaran' => ['required', 'exists:mata_pelajaran,id_mata_pelajaran'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'id_tahun_ajaran' => ['required', 'exists:tahun_ajaran,id_tahun_ajaran'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
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
            $this->addError('id_guru', 'Kombinasi Guru, Mata Pelajaran, Kelas dan Tahun Ajaran ini sudah wujud.');
            return;
        }

        if ($this->editingGuruAmpuId) {
            $guruAmpu = GuruAmpu::findOrFail($this->editingGuruAmpuId);
            $guruAmpu->update($validated);
            session()->flash('success', 'Data guru ampu berhasil diperbarui.');
        } else {
            GuruAmpu::create($validated);
            session()->flash('success', 'Data guru ampu berhasil ditambahkan.');
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
            session()->flash('success', 'Data guru ampu berhasil dihapus.');
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
            ->when($this->search, function ($query) {
                $query->whereHas('guru', function ($q) {
                    $q->where('nama_guru', 'like', '%' . $this->search . '%');
                })->orWhereHas('mataPelajaran', function ($q) {
                    $q->where('nama_mapel', 'like', '%' . $this->search . '%');
                })->orWhereHas('kelas', function ($q) {
                    $q->where('nama_kelas', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.guru-ampu-management', [
            'guruAmpuList' => $guruAmpuList,
            'gurus' => Guru::orderBy('nama_guru')->get(),
            'mapels' => MataPelajaran::orderBy('nama_mapel')->get(),
            'kelasList' => Kelas::orderBy('nama_kelas')->get(),
            'tahunAjarans' => TahunAjaran::orderBy('nama_tahun')->get(),
        ]);
    }
}
