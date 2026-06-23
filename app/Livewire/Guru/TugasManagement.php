<?php

namespace App\Livewire\Guru;

use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

#[Title('Manajemen Tugas')]
class TugasManagement extends Component
{
    use WithFileUploads;

    public $guruId;
    public $jadwals;
    public $selectedJadwalId;

    // Form attributes
    public $tugasId;
    public $judul;
    public $deskripsi;
    public $file_lampiran;
    public $tenggat_waktu;
    
    // View state
    public $showModal = false;
    public $isEditing = false;
    public $showPengumpulanModal = false;
    public $selectedTugasId;

    // Nilai attributes
    public $nilai = [];
    public $catatanGuru = [];

    // Detail Modal state
    public $showDetailModal = false;
    public $selectedPengumpulan = null;

    public function mount()
    {
        $this->guruId = Auth::guard('guru')->user()->id_guru;
        
        // Ambil jadwal pelajaran untuk guru ini, yang tahun ajarannya sedang aktif
        $this->jadwals = JadwalPelajaran::whereHas('guruAmpu', function($q) {
            $q->where('id_guru', $this->guruId)
              ->whereHas('tahunAjaran', function($q2) {
                  $q2->where('status_aktif', true);
              });
        })->with(['guruAmpu.kelas', 'guruAmpu.mataPelajaran'])->get();

        if ($this->jadwals->isNotEmpty()) {
            $this->selectedJadwalId = $this->jadwals->first()->id_jadwal_pelajaran;
        }
    }

    public function updatedSelectedJadwalId()
    {
        // Refresh tugas list automatically
    }

    public function createTugas()
    {
        $this->resetValidation();
        $this->reset(['tugasId', 'judul', 'deskripsi', 'file_lampiran', 'tenggat_waktu']);
        $this->tenggat_waktu = Carbon::now()->addDays(7)->format('Y-m-d\TH:i'); // default 1 minggu
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function editTugas($id)
    {
        $tugas = Tugas::findOrFail($id);
        $this->tugasId = $tugas->id_tugas;
        $this->judul = $tugas->judul;
        $this->deskripsi = $tugas->deskripsi;
        $this->tenggat_waktu = Carbon::parse($tugas->tenggat_waktu)->format('Y-m-d\TH:i');
        // We don't load the file itself to the component variable since it's a new upload
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function saveTugas()
    {
        $this->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tenggat_waktu' => 'required|date',
            'file_lampiran' => 'nullable|file|max:10240', // max 10MB
        ]);

        $data = [
            'id_jadwal_pelajaran' => $this->selectedJadwalId,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'tenggat_waktu' => Carbon::parse($this->tenggat_waktu),
        ];

        if ($this->file_lampiran) {
            $path = $this->file_lampiran->store('tugas_lampiran', 'public');
            $data['file_lampiran'] = $path;
        }

        if ($this->isEditing) {
            $tugas = Tugas::findOrFail($this->tugasId);
            // If new file uploaded and old file exists, delete old file
            if ($this->file_lampiran && $tugas->file_lampiran) {
                Storage::disk('public')->delete($tugas->file_lampiran);
            }
            $tugas->update($data);
            session()->flash('success', 'Tugas berhasil diperbarui.');
        } else {
            Tugas::create($data);
            session()->flash('success', 'Tugas berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function deleteTugas($id)
    {
        $tugas = Tugas::findOrFail($id);
        if ($tugas->file_lampiran) {
            Storage::disk('public')->delete($tugas->file_lampiran);
        }
        
        // Files submitted by students will be cascade deleted from DB, but we should also delete files if we want to be thorough.
        foreach($tugas->pengumpulan as $pengumpulan) {
            if ($pengumpulan->file_tugas) {
                Storage::disk('public')->delete($pengumpulan->file_tugas);
            }
        }

        $tugas->delete();
        session()->flash('success', 'Tugas berhasil dihapus.');
    }

    public function lihatPengumpulan($idTugas)
    {
        $this->selectedTugasId = $idTugas;
        $tugas = Tugas::with('pengumpulan.siswa')->findOrFail($idTugas);
        
        // Initialize model for grades
        foreach($tugas->pengumpulan as $p) {
            $this->nilai[$p->id_pengumpulan] = $p->nilai;
        }

        $this->showPengumpulanModal = true;
    }

    public function lihatDetail($idPengumpulan)
    {
        $this->selectedPengumpulan = PengumpulanTugas::with('siswa', 'tugas')->findOrFail($idPengumpulan);
        $this->showDetailModal = true;
    }

    public function saveNilai($idPengumpulan)
    {
        $pengumpulan = PengumpulanTugas::findOrFail($idPengumpulan);
        $pengumpulan->update([
            'nilai' => $this->nilai[$idPengumpulan] ?? null,
        ]);
        
        session()->flash('success_nilai_' . $idPengumpulan, 'Nilai disimpan.');
    }

    public function render()
    {
        $tugasList = collect();
        if ($this->selectedJadwalId) {
            $tugasList = Tugas::where('id_jadwal_pelajaran', $this->selectedJadwalId)
                ->orderBy('created_at', 'desc')
                ->withCount('pengumpulan')
                ->get();
        }

        $selectedTugas = $this->selectedTugasId ? Tugas::with(['pengumpulan.siswa'])->find($this->selectedTugasId) : null;

        return view('livewire.guru.tugas-management', [
            'tugasList' => $tugasList,
            'selectedTugas' => $selectedTugas
        ]);
    }
}
