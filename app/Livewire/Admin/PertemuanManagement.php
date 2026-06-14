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

    // Filter
    public ?int $filter_guru_ampu = null;

    // View State
    public bool $showViewModal = false;
    public $viewingPertemuan = null;

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

    public function updatedFilterGuruAmpu(): void
    {
        $this->resetPage();
    }

    public function updatedIdGuruAmpu($value): void
    {
        if ($value && !$this->editingPertemuanId) {
            $lastPertemuan = Pertemuan::where('id_guru_ampu', $value)->orderBy('pertemuan_ke', 'desc')->first();
            $this->pertemuan_ke = $lastPertemuan ? $lastPertemuan->pertemuan_ke + 1 : 1;
        }
    }

    // --- Pertemuan Methods ---

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->tanggal = date('Y-m-d');
        $this->editingPertemuanId = null;
        
        // Auto select if filter is active
        if ($this->filter_guru_ampu) {
            $this->id_guru_ampu = $this->filter_guru_ampu;
            $this->updatedIdGuruAmpu($this->filter_guru_ampu);
        }

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
            $this->closeModal();
        } else {
            $pertemuan = Pertemuan::create($validated);
            session()->flash('success', 'Pertemuan berhasil ditambahkan. Silakan isi absensi siswa.');
            $this->closeModal();
            $this->openAbsensiModal($pertemuan->id_pertemuan); // Auto open absensi
        }
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

    public function openViewModal(int $id): void
    {
        $this->viewingPertemuan = Pertemuan::with(['guruAmpu.guru', 'guruAmpu.mataPelajaran', 'guruAmpu.kelas'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingPertemuan = null;
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
        $query = Pertemuan::query()->with(['guruAmpu.guru', 'guruAmpu.mataPelajaran', 'guruAmpu.kelas']);
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
                $q->where('pokok_bahasan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('guruAmpu.mataPelajaran', function ($q2) {
                      $q2->where('nama_mapel', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('guruAmpu.kelas', function ($q3) {
                      $q3->where('nama_kelas', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $pertemuanList = $query->orderBy('tanggal', 'desc')->paginate(10);
        $guruAmpuOptions = $guruQuery->get();

        return view('livewire.admin.pertemuan-management', [
            'pertemuanList' => $pertemuanList,
            'guruAmpuOptions' => $guruAmpuOptions,
        ]);
    }
}
