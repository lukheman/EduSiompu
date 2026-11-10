<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Manajemen Siswa')]
class SiswaManagement extends Component
{
    use WithFileUploads, WithPagination;

    // Search and Filter
    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?int $filter_kelas = null;

    // Form fields
    public string $nama_siswa = '';

    public string $nisn = '';

    public string $nis = '';

    public string $nik = '';

    public string $tempat_lahir = '';

    public string $tanggal_lahir = '';

    public string $jenis_kelamin = '';

    public string $agama = '';

    public string $kewarganegaraan = '';

    public string $alamat = '';

    public string $rt = '';

    public string $rw = '';

    public string $dusun = '';

    public string $kelurahan = '';

    public string $kecamatan = '';

    public string $kode_pos = '';

    public string $jenis_tempat_tinggal = '';

    public string $transportasi_ke_sekolah = '';

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
            'nis' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nis')->ignore($this->editingSiswaId, 'id_siswa')],
            'nik' => ['nullable', 'digits:16', Rule::unique('siswa', 'nik')->ignore($this->editingSiswaId, 'id_siswa')],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'agama' => ['nullable', 'string', 'max:50'],
            'kewarganegaraan' => ['nullable', 'in:WNI,WNA'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'jenis_tempat_tinggal' => ['nullable', 'in:Bersama orang tua,Wali,Asrama,Kos,Panti asuhan'],
            'transportasi_ke_sekolah' => ['nullable', 'in:Jalan kaki,Kendaraan pribadi,Angkutan umum,Antar-jemput'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'id_orang_tua' => ['nullable', 'exists:orang_tua,id_orang_tua'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];

        if ($this->editingSiswaId) {
            $rules['nisn'] = ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')->ignore($this->editingSiswaId, 'id_siswa')];
            // Tidak ada validasi password saat edit
        } else {
            $rules['nisn'] = ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')];
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
        $this->nis = $siswa->nis ?? '';
        $this->nisn = $siswa->nisn;
        $this->nik = $siswa->nik ?? '';
        $this->tempat_lahir = $siswa->tempat_lahir ?? '';
        $this->tanggal_lahir = $siswa->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->jenis_kelamin = $siswa->jenis_kelamin ?? '';
        $this->agama = $siswa->agama ?? '';
        $this->kewarganegaraan = $siswa->kewarganegaraan ?? '';
        $this->alamat = $siswa->alamat ?? '';
        $this->rt = $siswa->rt ?? '';
        $this->rw = $siswa->rw ?? '';
        $this->dusun = $siswa->dusun ?? '';
        $this->kelurahan = $siswa->kelurahan ?? '';
        $this->kecamatan = $siswa->kecamatan ?? '';
        $this->kode_pos = $siswa->kode_pos ?? '';
        $this->jenis_tempat_tinggal = $siswa->jenis_tempat_tinggal ?? '';
        $this->transportasi_ke_sekolah = $siswa->transportasi_ke_sekolah ?? '';
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
            $siswa->nis = $validated['nis'] ?? null;
            $siswa->nisn = $validated['nisn'];
            $siswa->nik = $validated['nik'] ?? null;
            $siswa->tempat_lahir = $validated['tempat_lahir'] ?? null;
            $siswa->tanggal_lahir = $validated['tanggal_lahir'] ?? null;
            $siswa->jenis_kelamin = $validated['jenis_kelamin'] ?: null;
            $siswa->agama = $validated['agama'] ?? null;
            $siswa->kewarganegaraan = $validated['kewarganegaraan'] ?? null;
            $siswa->alamat = $validated['alamat'] ?? null;
            $siswa->rt = $validated['rt'] ?? null;
            $siswa->rw = $validated['rw'] ?? null;
            $siswa->dusun = $validated['dusun'] ?? null;
            $siswa->kelurahan = $validated['kelurahan'] ?? null;
            $siswa->kecamatan = $validated['kecamatan'] ?? null;
            $siswa->kode_pos = $validated['kode_pos'] ?? null;
            $siswa->jenis_tempat_tinggal = $validated['jenis_tempat_tinggal'] ?? null;
            $siswa->transportasi_ke_sekolah = $validated['transportasi_ke_sekolah'] ?? null;
            $siswa->id_kelas = $validated['id_kelas'];
            $siswa->id_orang_tua = $validated['id_orang_tua'] ?? null;

            if ($this->avatar) {
                if ($siswa->avatar && Storage::disk('public')->exists($siswa->avatar)) {
                    Storage::disk('public')->delete($siswa->avatar);
                }
                $siswa->avatar = $this->avatar->store('avatars', 'public');
            }

            if (! empty($this->password)) {
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
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'],
                'nik' => $validated['nik'] ?? null,
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?: null,
                'agama' => $validated['agama'] ?? null,
                'kewarganegaraan' => $validated['kewarganegaraan'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'dusun' => $validated['dusun'] ?? null,
                'kelurahan' => $validated['kelurahan'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kode_pos' => $validated['kode_pos'] ?? null,
                'jenis_tempat_tinggal' => $validated['jenis_tempat_tinggal'] ?? null,
                'transportasi_ke_sekolah' => $validated['transportasi_ke_sekolah'] ?? null,
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
        $this->nis = '';
        $this->nisn = '';
        $this->nik = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = '';
        $this->jenis_kelamin = '';
        $this->agama = '';
        $this->kewarganegaraan = '';
        $this->alamat = '';
        $this->rt = '';
        $this->rw = '';
        $this->dusun = '';
        $this->kelurahan = '';
        $this->kecamatan = '';
        $this->kode_pos = '';
        $this->jenis_tempat_tinggal = '';
        $this->transportasi_ke_sekolah = '';
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
            ->when($this->filter_kelas, fn ($q) => $q->where('id_kelas', $this->filter_kelas))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_siswa', 'like', '%'.$this->search.'%')
                        ->orWhere('nisn', 'like', '%'.$this->search.'%');
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
