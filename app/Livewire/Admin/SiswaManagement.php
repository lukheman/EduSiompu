<?php

namespace App\Livewire\Admin;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Siswa')]
class SiswaManagement extends Component
{
    use WithPagination;

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form fields
    public string $nama_siswa = '';
    public string $nisn = '';
    public ?int $id_kelas = null;
    public string $password = '';
    public string $password_confirmation = '';

    // State
    public ?int $editingSiswaId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingSiswaId = null;

    protected function rules(): array
    {
        $rules = [
            'nama_siswa' => ['required', 'string', 'max:255'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
        ];

        if ($this->editingSiswaId) {
            $rules['nisn'] = ['required', 'string', 'max:20', 'unique:siswa,nisn,' . $this->editingSiswaId . ',id_siswa'];
            if ($this->password) {
                $rules['password'] = ['confirmed', Password::defaults()];
            }
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

    public function openCreateModal(): void
    {
        $this->resetForm();
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
        $this->password = '';
        $this->password_confirmation = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingSiswaId) {
            $siswa = Siswa::findOrFail($this->editingSiswaId);
            $siswa->nama_siswa = $validated['nama_siswa'];
            $siswa->nisn = $validated['nisn'];
            $siswa->id_kelas = $validated['id_kelas'];

            if (!empty($this->password)) {
                $siswa->password = Hash::make($this->password);
            }

            $siswa->save();
            session()->flash('success', 'Data siswa berhasil diperbarui.');
        } else {
            Siswa::create([
                'nama_siswa' => $validated['nama_siswa'],
                'nisn' => $validated['nisn'],
                'id_kelas' => $validated['id_kelas'],
                'password' => Hash::make($validated['password']),
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
            Siswa::destroy($this->deletingSiswaId);
            session()->flash('success', 'Data siswa berhasil dihapus.');
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
        $this->password = '';
        $this->password_confirmation = '';
        $this->editingSiswaId = null;
    }

    public function render()
    {
        $siswas = Siswa::query()
            ->with('kelas')
            ->when($this->search, function ($query) {
                $query->where('nama_siswa', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();

        return view('livewire.admin.siswa-management', [
            'siswas' => $siswas,
            'kelasList' => $kelasList,
        ]);
    }
}
