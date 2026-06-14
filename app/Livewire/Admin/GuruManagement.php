<?php

namespace App\Livewire\Admin;

use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Title('Manajemen Guru')]
class GuruManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form fields
    public string $nip = '';
    public string $nama_guru = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $avatar;
    public ?string $currentAvatar = null;

    // State
    public ?int $editingGuruId = null;
    public ?int $viewingGuruId = null;
    public bool $showModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingGuruId = null;

    protected function rules(): array
    {
        $rules = [
            'nama_guru' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];

        if ($this->editingGuruId) {
            $rules['nip'] = ['required', 'string', 'max:50', 'unique:guru,nip,' . $this->editingGuruId . ',id_guru'];
            if ($this->password) {
                $rules['password'] = ['confirmed', Password::defaults()];
            }
        } else {
            $rules['nip'] = ['required', 'string', 'max:50', 'unique:guru,nip'];
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
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
        $this->editingGuruId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $guruId): void
    {
        $guru = Guru::findOrFail($guruId);
        $this->editingGuruId = $guruId;
        $this->nip = $guru->nip;
        $this->nama_guru = $guru->nama_guru;
        $this->password = '';
        $this->password_confirmation = '';
        $this->currentAvatar = $guru->avatar;
        $this->showModal = true;
    }

    public function openViewModal(int $guruId): void
    {
        $this->viewingGuruId = $guruId;
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingGuruId = null;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingGuruId) {
            $guru = Guru::findOrFail($this->editingGuruId);
            $guru->nip = $validated['nip'];
            $guru->nama_guru = $validated['nama_guru'];

            if ($this->avatar) {
                if ($guru->avatar && Storage::disk('public')->exists($guru->avatar)) {
                    Storage::disk('public')->delete($guru->avatar);
                }
                $guru->avatar = $this->avatar->store('avatars', 'public');
            }

            if (!empty($this->password)) {
                $guru->password = Hash::make($this->password);
            }

            $guru->save();
            session()->flash('success', 'Data guru berhasil diperbarui.');
        } else {
            $avatarPath = null;
            if ($this->avatar) {
                $avatarPath = $this->avatar->store('avatars', 'public');
            }

            Guru::create([
                'nip' => $validated['nip'],
                'nama_guru' => $validated['nama_guru'],
                'password' => Hash::make($validated['password']),
                'avatar' => $avatarPath,
            ]);
            session()->flash('success', 'Guru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $guruId): void
    {
        $this->deletingGuruId = $guruId;
        $this->showDeleteModal = true;
    }

    public function deleteGuru(): void
    {
        if ($this->deletingGuruId) {
            $guru = Guru::find($this->deletingGuruId);
            if ($guru) {
                if ($guru->avatar && Storage::disk('public')->exists($guru->avatar)) {
                    Storage::disk('public')->delete($guru->avatar);
                }
                $guru->delete();
                session()->flash('success', 'Data guru berhasil dihapus.');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingGuruId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingGuruId = null;
    }

    protected function resetForm(): void
    {
        $this->nip = '';
        $this->nama_guru = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->avatar = null;
        $this->currentAvatar = null;
        $this->editingGuruId = null;
    }

    public function render()
    {
        $gurus = Guru::query()
            ->when($this->search, function ($query) {
                $query->where('nama_guru', 'like', '%' . $this->search . '%')
                      ->orWhere('nip', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $viewingGuru = $this->viewingGuruId ? Guru::find($this->viewingGuruId) : null;

        return view('livewire.admin.guru-management', [
            'gurus' => $gurus,
            'viewingGuru' => $viewingGuru,
        ]);
    }
}
