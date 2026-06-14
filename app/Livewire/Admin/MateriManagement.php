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

    // Filter
    public ?int $filter_guru_ampu = null;

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

    public function updatedFilterGuruAmpu(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingMateriId = null;
        
        // Auto select if filter is active
        if ($this->filter_guru_ampu) {
            $this->id_guru_ampu = $this->filter_guru_ampu;
        }

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
            session()->flash('success', 'Materi berhasil diunggah.');
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
        $query = Materi::query()->with(['guruAmpu.guru', 'guruAmpu.mataPelajaran', 'guruAmpu.kelas']);
        $guruQuery = GuruAmpu::with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran']);

        if (\Illuminate\Support\Facades\Auth::guard('guru')->check()) {
            $guruId = \Illuminate\Support\Facades\Auth::guard('guru')->id();
            $query->whereHas('guruAmpu', function($q) use ($guruId) {
                $q->where('id_guru', $guruId);
            });
            $guruQuery->where('id_guru', $guruId);
        }

        if ($this->filter_guru_ampu) {
            $query->where('id_guru_ampu', $this->filter_guru_ampu);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('judul_materi', 'like', '%' . $this->search . '%')
                  ->orWhereHas('guruAmpu.mataPelajaran', function ($q2) {
                      $q2->where('nama_mapel', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('guruAmpu.kelas', function ($q3) {
                      $q3->where('nama_kelas', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $materiList = $query->orderBy('created_at', 'desc')->paginate(10);
        $guruAmpuOptions = $guruQuery->get();

        return view('livewire.admin.materi-management', [
            'materiList' => $materiList,
            'guruAmpuOptions' => $guruAmpuOptions,
        ]);
    }
}
