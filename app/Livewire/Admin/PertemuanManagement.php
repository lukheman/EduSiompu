<?php

namespace App\Livewire\Admin;

use App\Models\Pertemuan;
use App\Models\GuruAmpu;
use App\Models\Siswa;
use App\Models\Absensi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Pertemuan & Absensi')]
class PertemuanManagement extends Component
{
    use WithPagination;

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Pertemuan Form
    public ?int $id_guru_ampu = null;
    public ?int $pertemuan_ke = null;
    public string $tanggal = '';
    public string $pokok_bahasan = '';

    // State
    public ?int $editingPertemuanId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingPertemuanId = null;

    // Absensi State
    public bool $showAbsensiModal = false;
    public ?int $activePertemuanId = null;
    public $absensiData = [];

    protected function rules(): array
    {
        return [
            'id_guru_ampu' => ['required', 'exists:guru_ampu,id_guru_ampu'],
            'pertemuan_ke' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'pokok_bahasan' => ['required', 'string', 'max:255'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // --- Pertemuan Methods ---

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->tanggal = date('Y-m-d');
        $this->editingPertemuanId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $pertemuan = Pertemuan::findOrFail($id);
        $this->editingPertemuanId = $id;
        $this->id_guru_ampu = $pertemuan->id_guru_ampu;
        $this->pertemuan_ke = $pertemuan->pertemuan_ke;
        $this->tanggal = $pertemuan->tanggal;
        $this->pokok_bahasan = $pertemuan->pokok_bahasan;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingPertemuanId) {
            $pertemuan = Pertemuan::findOrFail($this->editingPertemuanId);
            $pertemuan->update($validated);
            session()->flash('success', 'Data pertemuan berhasil diperbarui.');
        } else {
            Pertemuan::create($validated);
            session()->flash('success', 'Pertemuan berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingPertemuanId = $id;
        $this->showDeleteModal = true;
    }

    public function deletePertemuan(): void
    {
        if ($this->deletingPertemuanId) {
            Pertemuan::find($this->deletingPertemuanId)?->delete();
            session()->flash('success', 'Data pertemuan berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deletingPertemuanId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingPertemuanId = null;
    }

    protected function resetForm(): void
    {
        $this->id_guru_ampu = null;
        $this->pertemuan_ke = null;
        $this->tanggal = '';
        $this->pokok_bahasan = '';
        $this->editingPertemuanId = null;
    }

    // --- Absensi Methods ---

    public function openAbsensiModal(int $pertemuanId): void
    {
        $this->activePertemuanId = $pertemuanId;
        $pertemuan = Pertemuan::with('guruAmpu')->findOrFail($pertemuanId);
        
        $idKelas = $pertemuan->guruAmpu->id_kelas;
        $siswaList = Siswa::where('id_kelas', $idKelas)->orderBy('nama_siswa')->get();

        // Load existing absensi
        $existingAbsensi = Absensi::where('id_pertemuan', $pertemuanId)->get()->keyBy('id_siswa');

        $this->absensiData = [];
        foreach ($siswaList as $siswa) {
            $status = isset($existingAbsensi[$siswa->id_siswa]) 
                ? $existingAbsensi[$siswa->id_siswa]->status_kehadiran 
                : 'hadir'; // default

            $this->absensiData[$siswa->id_siswa] = [
                'nama' => $siswa->nama_siswa,
                'nisn' => $siswa->nisn,
                'status' => $status
            ];
        }

        $this->showAbsensiModal = true;
    }

    public function closeAbsensiModal(): void
    {
        $this->showAbsensiModal = false;
        $this->activePertemuanId = null;
        $this->absensiData = [];
    }

    public function saveAbsensi(): void
    {
        if (!$this->activePertemuanId) return;

        foreach ($this->absensiData as $id_siswa => $data) {
            Absensi::updateOrCreate(
                ['id_pertemuan' => $this->activePertemuanId, 'id_siswa' => $id_siswa],
                ['status_kehadiran' => $data['status']]
            );
        }

        session()->flash('success', 'Data absensi berhasil disimpan.');
        $this->closeAbsensiModal();
    }

    public function render()
    {
        $pertemuanList = Pertemuan::query()
            ->with(['guruAmpu.guru', 'guruAmpu.mataPelajaran', 'guruAmpu.kelas'])
            ->when($this->search, function ($query) {
                $query->where('pokok_bahasan', 'like', '%' . $this->search . '%')
                    ->orWhereHas('guruAmpu.mataPelajaran', function ($q) {
                        $q->where('nama_mapel', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('guruAmpu.kelas', function ($q) {
                        $q->where('nama_kelas', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        $guruAmpuOptions = GuruAmpu::with(['guru', 'mataPelajaran', 'kelas', 'tahunAjaran'])->get();

        return view('livewire.admin.pertemuan-management', [
            'pertemuanList' => $pertemuanList,
            'guruAmpuOptions' => $guruAmpuOptions,
        ]);
    }
}
