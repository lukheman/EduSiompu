<?php

namespace App\Livewire\Siswa;

use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

#[Title('Tugas Kelas')]
class TugasList extends Component
{
    use WithFileUploads;

    public $siswaId;
    public $activeTab = 'belum_selesai'; // belum_selesai, selesai

    public $showSubmitModal = false;
    public $selectedTugasId;
    public $file_tugas;
    public $catatan;

    public function mount()
    {
        $this->siswaId = Auth::guard('siswa')->user()->id_siswa;
    }

    public function openSubmitModal($tugasId)
    {
        $this->reset(['file_tugas', 'catatan']);
        $this->selectedTugasId = $tugasId;
        
        // Check if already submitted (if editing submission)
        $existing = PengumpulanTugas::where('id_tugas', $tugasId)
                                    ->where('id_siswa', $this->siswaId)
                                    ->first();
        if ($existing) {
            $this->catatan = $existing->catatan;
        }

        $this->showSubmitModal = true;
    }

    public function submitTugas()
    {
        $this->validate([
            'file_tugas' => 'required|file|max:10240', // 10MB max
            'catatan' => 'nullable|string',
        ]);

        $existing = PengumpulanTugas::where('id_tugas', $this->selectedTugasId)
                                    ->where('id_siswa', $this->siswaId)
                                    ->first();

        $path = $this->file_tugas->store('pengumpulan_tugas', 'public');

        if ($existing) {
            // Delete old file
            if ($existing->file_tugas) {
                Storage::disk('public')->delete($existing->file_tugas);
            }
            $existing->update([
                'file_tugas' => $path,
                'catatan' => $this->catatan,
                'waktu_pengumpulan' => Carbon::now(),
            ]);
            session()->flash('success', 'Tugas berhasil diperbarui.');
        } else {
            PengumpulanTugas::create([
                'id_tugas' => $this->selectedTugasId,
                'id_siswa' => $this->siswaId,
                'file_tugas' => $path,
                'catatan' => $this->catatan,
                'waktu_pengumpulan' => Carbon::now(),
            ]);
            session()->flash('success', 'Tugas berhasil dikumpulkan.');
        }

        $this->showSubmitModal = false;
    }

    public function render()
    {
        $siswa = Siswa::findOrFail($this->siswaId);
        $id_kelas = $siswa->id_kelas;

        // Find all tasks related to the student's class
        // by joining jadwal_pelajaran -> guru_ampu -> kelas
        $tugasQuery = Tugas::whereHas('jadwalPelajaran.guruAmpu', function($q) use ($id_kelas) {
            $q->where('id_kelas', $id_kelas)
              ->whereHas('tahunAjaran', function($q2) {
                  $q2->where('status_aktif', true);
              });
        })->with(['jadwalPelajaran.guruAmpu.mataPelajaran', 'jadwalPelajaran.guruAmpu.guru']);

        if ($this->activeTab == 'selesai') {
            $tugas = $tugasQuery->whereHas('pengumpulan', function($q) {
                $q->where('id_siswa', $this->siswaId);
            })->with(['pengumpulan' => function($q) {
                $q->where('id_siswa', $this->siswaId);
            }])->orderBy('tenggat_waktu', 'desc')->get();
        } else {
            $tugas = $tugasQuery->whereDoesntHave('pengumpulan', function($q) {
                $q->where('id_siswa', $this->siswaId);
            })->orderBy('tenggat_waktu', 'asc')->get();
        }

        return view('livewire.siswa.tugas-list', [
            'tugasList' => $tugas,
            'selectedTugas' => $this->selectedTugasId ? Tugas::with(['jadwalPelajaran.guruAmpu.mataPelajaran'])->find($this->selectedTugasId) : null
        ]);
    }
}
