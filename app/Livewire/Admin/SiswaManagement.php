<?php

namespace App\Livewire\Admin;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\OrangTua;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Title('Manajemen Siswa')]
class SiswaManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Search and Filter
    #[Url(as: 'q')]
    public string $search = '';
    
    #[Url]
    public ?int $filter_kelas = null;

    // Form fields
    public string $nama_siswa = '';
    public string $nisn = '';
    public ?int $id_kelas = null;
    public ?int $id_orang_tua = null;
    public string $password = '';
    public string $password_confirmation = '';
    public $avatar;
    public ?string $currentAvatar = null;

    // State
    public ?int $editingSiswaId = null;
    public ?int $viewingSiswaId = null;
    public bool $showModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingSiswaId = null;

    protected function rules(): array
    {
        $rules = [
            'nama_siswa' => ['required', 'string', 'max:255'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'id_orang_tua' => ['nullable', 'exists:orang_tua,id_orang_tua'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];

        if ($this->editingSiswaId) {
            $rules['nisn'] = ['required', 'string', 'max:20', 'unique:siswa,nisn,' . $this->editingSiswaId . ',id_siswa'];
            // Tidak ada validasi password saat edit
        } else {
            $rules['nisn'] = ['required', 'string', 'max:20', 'unique:siswa,nisn'];
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterKelas(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        if ($this->filter_kelas) {
            $this->id_kelas = $this->filter_kelas;
        }
        $this->editingSiswaId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $siswaId): void
    {
        $siswa = Siswa::findOrFail($siswaId);
        $this->editingSiswaId = $siswaId;
        $this->nama_siswa = $siswa->nama_siswa;
        $this->nisn = $siswa->nisn;
        $this->id_kelas = $siswa->id_kelas;
        $this->id_orang_tua = $siswa->id_orang_tua;
        $this->password = '';
        $this->password_confirmation = '';
        $this->currentAvatar = $siswa->avatar;
        $this->showModal = true;
    }

    public function openViewModal(int $siswaId): void
    {
        $this->viewingSiswaId = $siswaId;
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingSiswaId = null;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingSiswaId) {
            $siswa = Siswa::findOrFail($this->editingSiswaId);
            $siswa->nama_siswa = $validated['nama_siswa'];
            $siswa->nisn = $validated['nisn'];
            $siswa->id_kelas = $validated['id_kelas'];
            $siswa->id_orang_tua = $validated['id_orang_tua'] ?? null;

            if ($this->avatar) {
                if ($siswa->avatar && Storage::disk('public')->exists($siswa->avatar)) {
                    Storage::disk('public')->delete($siswa->avatar);
                }
                $siswa->avatar = $this->avatar->store('avatars', 'public');
            }

            if (!empty($this->password)) {
                $siswa->password = Hash::make($this->password);
            }

            $siswa->save();
            session()->flash('success', 'Data siswa berhasil diperbarui.');
        } else {
            $avatarPath = null;
            if ($this->avatar) {
                $avatarPath = $this->avatar->store('avatars', 'public');
            }

            Siswa::create([
                'nama_siswa' => $validated['nama_siswa'],
                'nisn' => $validated['nisn'],
                'id_kelas' => $validated['id_kelas'],
                'id_orang_tua' => $validated['id_orang_tua'] ?? null,
                'password' => Hash::make($validated['password']),
                'avatar' => $avatarPath,
            ]);
            session()->flash('success', 'Siswa berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $siswaId): void
    {
        $this->deletingSiswaId = $siswaId;
        $this->showDeleteModal = true;
    }

    public function deleteSiswa(): void
    {
        if ($this->deletingSiswaId) {
            $siswa = Siswa::find($this->deletingSiswaId);
            if ($siswa) {
                if ($siswa->avatar && Storage::disk('public')->exists($siswa->avatar)) {
                    Storage::disk('public')->delete($siswa->avatar);
                }
                $siswa->delete();
                session()->flash('success', 'Data siswa berhasil dihapus.');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingSiswaId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingSiswaId = null;
    }

    protected function resetForm(): void
    {
        $this->nama_siswa = '';
        $this->nisn = '';
        $this->id_kelas = null;
        $this->id_orang_tua = null;
        $this->password = '';
        $this->password_confirmation = '';
        $this->avatar = null;
        $this->currentAvatar = null;
        $this->editingSiswaId = null;
    }

    public function render()
    {
        $siswas = Siswa::query()
            ->with(['kelas', 'orangTua'])
            ->when($this->filter_kelas, fn($q) => $q->where('id_kelas', $this->filter_kelas))
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('nama_siswa', 'like', '%' . $this->search . '%')
                      ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();
        $orangTuaList = OrangTua::orderBy('nama_orang_tua', 'asc')->get();

        $viewingSiswa = $this->viewingSiswaId 
            ? Siswa::with(['kelas', 'orangTua'])->find($this->viewingSiswaId) 
            : null;

        return view('livewire.admin.siswa-management', [
            'siswas' => $siswas,
            'kelasList' => $kelasList,
            'orangTuaList' => $orangTuaList,
            'viewingSiswa' => $viewingSiswa,
        ]);
    }
}
