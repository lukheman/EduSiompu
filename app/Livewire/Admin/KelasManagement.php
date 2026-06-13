<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Kelas')]
class KelasManagement extends Component
{
    use WithPagination;

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form fields
    public string $nama_kelas = '';

    // State
    public ?int $editingKelasId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingKelasId = null;

    protected function rules(): array
    {
        $rules = [
            'nama_kelas' => ['required', 'string', 'max:255'],
        ];

        if ($this->editingKelasId) {
            $rules['nama_kelas'][] = 'unique:kelas,nama_kelas,' . $this->editingKelasId . ',id_kelas';
        } else {
            $rules['nama_kelas'][] = 'unique:kelas,nama_kelas';
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
        $this->editingKelasId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $kelasId): void
    {
        $kelas = Kelas::findOrFail($kelasId);
        $this->editingKelasId = $kelasId;
        $this->nama_kelas = $kelas->nama_kelas;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingKelasId) {
            $kelas = Kelas::findOrFail($this->editingKelasId);
            $kelas->nama_kelas = $validated['nama_kelas'];
            $kelas->save();
            
            session()->flash('success', 'Data kelas berhasil diperbarui.');
        } else {
            Kelas::create([
                'nama_kelas' => $validated['nama_kelas'],
            ]);
            
            session()->flash('success', 'Kelas berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $kelasId): void
    {
        $this->deletingKelasId = $kelasId;
        $this->showDeleteModal = true;
    }

    public function deleteKelas(): void
    {
        if ($this->deletingKelasId) {
            Kelas::destroy($this->deletingKelasId);
            session()->flash('success', 'Data kelas berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deletingKelasId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingKelasId = null;
    }

    protected function resetForm(): void
    {
        $this->nama_kelas = '';
        $this->editingKelasId = null;
    }

    public function render()
    {
        $kelasList = Kelas::query()
            ->when($this->search, function ($query) {
                $query->where('nama_kelas', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.kelas-management', [
            'kelasList' => $kelasList,
        ]);
    }
}
