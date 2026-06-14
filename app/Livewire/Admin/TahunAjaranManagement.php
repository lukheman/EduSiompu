<?php

namespace App\Livewire\Admin;

use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Tahun Ajaran')]
class TahunAjaranManagement extends Component
{
    use WithPagination;

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form
    public string $nama_tahun = '';
    public string $semester = 'ganjil';
    public bool $status_aktif = true;

    // State
    public ?int $editingId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'nama_tahun' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'in:ganjil,genap'],
            'status_aktif' => ['boolean'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $this->editingId = $id;
        $this->nama_tahun = $tahunAjaran->nama_tahun;
        $this->semester = $tahunAjaran->semester;
        $this->status_aktif = (bool) $tahunAjaran->status_aktif;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        // If status is active, we should probably set all others to inactive?
        // Let's assume that logic is needed since usually only one academic year is active.
        if ($validated['status_aktif']) {
            TahunAjaran::where('status_aktif', true)->update(['status_aktif' => false]);
        }

        if ($this->editingId) {
            $tahunAjaran = TahunAjaran::findOrFail($this->editingId);
            $tahunAjaran->update($validated);
            session()->flash('success', 'Tahun ajaran berhasil diperbarui.');
        } else {
            TahunAjaran::create($validated);
            session()->flash('success', 'Tahun ajaran berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function toggleStatus(int $id): void
    {
        // Set all other to inactive and this one to active
        TahunAjaran::where('status_aktif', true)->update(['status_aktif' => false]);
        
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update(['status_aktif' => true]);
        
        session()->flash('success', 'Tahun ajaran aktif berhasil diubah.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteData(): void
    {
        if ($this->deletingId) {
            $tahun = TahunAjaran::find($this->deletingId);
            if ($tahun) {
                // Check if it's the only active one
                $wasActive = $tahun->status_aktif;
                $tahun->delete();
                
                // If we deleted the active one, optionally activate the latest one
                if ($wasActive) {
                    $latest = TahunAjaran::latest()->first();
                    if ($latest) {
                        $latest->update(['status_aktif' => true]);
                    }
                }
                
                session()->flash('success', 'Tahun ajaran berhasil dihapus.');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    protected function resetForm(): void
    {
        $this->nama_tahun = '';
        $this->semester = 'ganjil';
        $this->status_aktif = true;
        $this->editingId = null;
    }

    public function render()
    {
        $tahunAjarans = TahunAjaran::query()
            ->when($this->search, function ($query) {
                $query->where('nama_tahun', 'like', '%' . $this->search . '%')
                    ->orWhere('semester', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.tahun-ajaran-management', [
            'tahunAjarans' => $tahunAjarans,
        ]);
    }
}
