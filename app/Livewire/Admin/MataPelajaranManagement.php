<?php

namespace App\Livewire\Admin;

use App\Models\MataPelajaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Mata Pelajaran')]
class MataPelajaranManagement extends Component
{
    use WithPagination;

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form fields
    public string $kode_mapel = '';
    public string $nama_mapel = '';

    // State
    public ?int $editingMapelId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingMapelId = null;

    protected function rules(): array
    {
        $rules = [
            'nama_mapel' => ['required', 'string', 'max:255'],
        ];

        if ($this->editingMapelId) {
            $rules['kode_mapel'] = ['required', 'string', 'max:255', 'unique:mata_pelajaran,kode_mapel,' . $this->editingMapelId . ',id_mata_pelajaran'];
        } else {
            $rules['kode_mapel'] = ['required', 'string', 'max:255', 'unique:mata_pelajaran,kode_mapel'];
        }

        return $rules;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingMapelId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $mapelId): void
    {
        $mapel = MataPelajaran::findOrFail($mapelId);
        $this->editingMapelId = $mapelId;
        $this->kode_mapel = $mapel->kode_mapel;
        $this->nama_mapel = $mapel->nama_mapel;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingMapelId) {
            $mapel = MataPelajaran::findOrFail($this->editingMapelId);
            $mapel->kode_mapel = $validated['kode_mapel'];
            $mapel->nama_mapel = $validated['nama_mapel'];
            $mapel->save();
            
            session()->flash('success', 'Data mata pelajaran berhasil diperbarui.');
        } else {
            MataPelajaran::create([
                'kode_mapel' => $validated['kode_mapel'],
                'nama_mapel' => $validated['nama_mapel'],
            ]);
            
            session()->flash('success', 'Mata pelajaran berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $mapelId): void
    {
        $this->deletingMapelId = $mapelId;
        $this->showDeleteModal = true;
    }

    public function deleteMapel(): void
    {
        if ($this->deletingMapelId) {
            MataPelajaran::destroy($this->deletingMapelId);
            session()->flash('success', 'Data mata pelajaran berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deletingMapelId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingMapelId = null;
    }

    protected function resetForm(): void
    {
        $this->kode_mapel = '';
        $this->nama_mapel = '';
        $this->editingMapelId = null;
    }

    public function render()
    {
        $mapelList = MataPelajaran::query()
            ->when($this->search, function ($query) {
                $query->where('kode_mapel', 'like', '%' . $this->search . '%')
                      ->orWhere('nama_mapel', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.mata-pelajaran-management', [
            'mapelList' => $mapelList,
        ]);
    }
}
