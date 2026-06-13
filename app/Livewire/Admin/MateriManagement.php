<?php

namespace App\Livewire\Admin;

use App\Models\Materi;
use App\Models\GuruAmpu;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Title('Manajemen Materi')]
class MateriManagement extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form fields
    public ?int $id_guru_ampu = null;
    public string $judul_materi = '';
    public $file_materi; // For upload

    // State
    public ?int $editingMateriId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingMateriId = null;

    protected function rules(): array
    {
        $rules = [
            'id_guru_ampu' => ['required', 'exists:guru_ampu,id_guru_ampu'],
            'judul_materi' => ['required', 'string', 'max:255'],
        ];

        if ($this->editingMateriId) {
            $rules['file_materi'] = ['nullable', 'file', 'max:20480']; // 20MB Max
        } else {
            $rules['file_materi'] = ['required', 'file', 'max:20480']; // 20MB Max
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
        $this->editingMateriId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $materiId): void
    {
        $materi = Materi::findOrFail($materiId);
        $this->editingMateriId = $materiId;
        $this->id_guru_ampu = $materi->id_guru_ampu;
        $this->judul_materi = $materi->judul_materi;
        // Do not bind file_materi to existing file to avoid confusion, user can upload a new one
        $this->file_materi = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
            'id_guru_ampu' => $validated['id_guru_ampu'],
            'judul_materi' => $validated['judul_materi'],
        ];

        if ($this->file_materi) {
            $path = $this->file_materi->store('materi_files', 'public');
            $data['file_path'] = $path;
            $data['jenis_file'] = $this->file_materi->getClientOriginalExtension();
        }

        if ($this->editingMateriId) {
            $materi = Materi::findOrFail($this->editingMateriId);

            // Delete old file if new file is uploaded
            if ($this->file_materi && $materi->file_path) {
                Storage::disk('public')->delete($materi->file_path);
            }

            $materi->update($data);
            session()->flash('success', 'Data materi berhasil diperbarui.');
        } else {
            Materi::create($data);
            session()->flash('success', 'Materi berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $materiId): void
    {
        $this->deletingMateriId = $materiId;
        $this->showDeleteModal = true;
    }

    public function deleteMateri(): void
    {
        if ($this->deletingMateriId) {
            $materi = Materi::find($this->deletingMateriId);
            if ($materi) {
                if ($materi->file_path) {
                    Storage::disk('public')->delete($materi->file_path);
                }
                $materi->delete();
                session()->flash('success', 'Data materi berhasil dihapus.');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingMateriId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingMateriId = null;
    }

    protected function resetForm(): void
    {
        $this->id_guru_ampu = null;
        $this->judul_materi = '';
        $this->file_materi = null;
        $this->editingMateriId = null;
    }

    public function render()
    {
        $materiList = Materi::query()
            ->with(['guruAmpu.guru', 'guruAmpu.mataPelajaran', 'guruAmpu.kelas'])
            ->when($this->search, function ($query) {
                $query->where('judul_materi', 'like', '%' . $this->search . '%')
                    ->orWhereHas('guruAmpu.mataPelajaran', function ($q) {
                        $q->where('nama_mapel', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('guruAmpu.kelas', function ($q) {
                        $q->where('nama_kelas', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $guruAmpuOptions = GuruAmpu::with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran'])->get();

        return view('livewire.admin.materi-management', [
            'materiList' => $materiList,
            'guruAmpuOptions' => $guruAmpuOptions,
        ]);
    }
}
