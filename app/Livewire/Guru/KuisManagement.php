<?php

namespace App\Livewire\Guru;

use App\Models\JadwalPelajaran;
use App\Models\Kuis;
use App\Models\SoalKuis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manajemen Kuis')]
class KuisManagement extends Component
{
    public ?int $guruId = null;

    public $jadwals;

    public $selectedJadwalId;

    // Form kuis
    public ?int $kuisId = null;

    public string $judul = '';

    public string $deskripsi = '';

    public string $tenggat_waktu = '';

    public int $durasi_menit = 30;

    public bool $showModal = false;

    public bool $isEditing = false;

    // Form soal
    public ?int $selectedKuisId = null;

    public ?int $soalId = null;

    public string $pertanyaan = '';

    public string $pilihan_a = '';

    public string $pilihan_b = '';

    public string $pilihan_c = '';

    public string $pilihan_d = '';

    public string $jawaban_benar = 'A';

    public int $poin = 1;

    public bool $showSoalModal = false;

    // Hasil
    public ?int $selectedHasilKuisId = null;

    public bool $showHasilModal = false;

    public function mount(): void
    {
        $this->guruId = Auth::guard('guru')->id();

        $this->jadwals = JadwalPelajaran::whereHas('guruAmpu', function ($q) {
            $q->where('id_guru', $this->guruId)
                ->whereHas('tahunAjaran', fn ($q2) => $q2->where('status_aktif', true));
        })->with(['guruAmpu.kelas', 'guruAmpu.mataPelajaran'])->get();

        if ($this->jadwals->isNotEmpty()) {
            $this->selectedJadwalId = $this->jadwals->first()->id_jadwal_pelajaran;
        }
    }

    public function updatedSelectedJadwalId(): void
    {
        $this->selectedKuisId = null;
        $this->selectedHasilKuisId = null;
    }

    public function createKuis(): void
    {
        $this->resetValidation();
        $this->reset(['kuisId', 'judul', 'deskripsi', 'tenggat_waktu']);
        $this->durasi_menit = 30;
        $this->tenggat_waktu = Carbon::now()->addDays(7)->format('Y-m-d\TH:i');
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function editKuis(int $id): void
    {
        $kuis = $this->kuisMilikGuru($id);
        if (! $kuis) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $this->resetValidation();
        $this->kuisId = $kuis->id_kuis;
        $this->judul = $kuis->judul;
        $this->deskripsi = $kuis->deskripsi ?? '';
        $this->tenggat_waktu = $kuis->tenggat_waktu->format('Y-m-d\TH:i');
        $this->durasi_menit = $kuis->durasi_menit;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function saveKuis(): void
    {
        $validated = $this->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tenggat_waktu' => ['required', 'date'],
            'durasi_menit' => ['required', 'integer', 'min:1', 'max:600'],
        ]);

        if (! $this->jadwalMilikGuru((int) $this->selectedJadwalId)) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $data = [
            'id_jadwal_pelajaran' => $this->selectedJadwalId,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'tenggat_waktu' => Carbon::parse($validated['tenggat_waktu']),
            'durasi_menit' => $validated['durasi_menit'],
        ];

        if ($this->isEditing) {
            $kuis = $this->kuisMilikGuru((int) $this->kuisId);
            if (! $kuis) {
                abort(403, 'Anda tidak memiliki akses ke kuis ini.');
            }

            $kuis->update($data);
            session()->flash('success', 'Kuis berhasil diperbarui.');
        } else {
            Kuis::create($data);
            session()->flash('success', 'Kuis berhasil dibuat. Tambahkan soal agar siswa dapat mengerjakannya.');
        }

        $this->showModal = false;
        $this->reset(['kuisId', 'judul', 'deskripsi', 'tenggat_waktu']);
        $this->durasi_menit = 30;
    }

    public function deleteKuis(int $id): void
    {
        $kuis = $this->kuisMilikGuru($id);
        if (! $kuis) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $kuis->delete();

        if ($this->selectedKuisId === $id) {
            $this->showSoalModal = false;
            $this->selectedKuisId = null;
        }
        if ($this->selectedHasilKuisId === $id) {
            $this->showHasilModal = false;
            $this->selectedHasilKuisId = null;
        }

        session()->flash('success', 'Kuis berhasil dihapus.');
    }

    public function kelolaSoal(int $id): void
    {
        $kuis = $this->kuisMilikGuru($id);
        if (! $kuis) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $this->selectedKuisId = $id;
        $this->resetSoalForm();
        $this->showSoalModal = true;
    }

    public function editSoal(int $id): void
    {
        $kuis = $this->kuisMilikGuru((int) $this->selectedKuisId);
        if (! $kuis) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $soal = SoalKuis::where('id_kuis', $kuis->id_kuis)->findOrFail($id);

        $this->soalId = $soal->id_soal;
        $this->pertanyaan = $soal->pertanyaan;
        $this->pilihan_a = $soal->pilihan_a;
        $this->pilihan_b = $soal->pilihan_b;
        $this->pilihan_c = $soal->pilihan_c;
        $this->pilihan_d = $soal->pilihan_d;
        $this->jawaban_benar = $soal->jawaban_benar;
        $this->poin = $soal->poin;
    }

    public function saveSoal(): void
    {
        $kuis = $this->kuisMilikGuru((int) $this->selectedKuisId);
        if (! $kuis) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $validated = $this->validate([
            'pertanyaan' => ['required', 'string'],
            'pilihan_a' => ['required', 'string', 'max:255'],
            'pilihan_b' => ['required', 'string', 'max:255'],
            'pilihan_c' => ['required', 'string', 'max:255'],
            'pilihan_d' => ['required', 'string', 'max:255'],
            'jawaban_benar' => ['required', 'in:A,B,C,D'],
            'poin' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $data = [
            'id_kuis' => $kuis->id_kuis,
            'pertanyaan' => $validated['pertanyaan'],
            'pilihan_a' => $validated['pilihan_a'],
            'pilihan_b' => $validated['pilihan_b'],
            'pilihan_c' => $validated['pilihan_c'],
            'pilihan_d' => $validated['pilihan_d'],
            'jawaban_benar' => $validated['jawaban_benar'],
            'poin' => $validated['poin'],
        ];

        if ($this->soalId) {
            $soal = SoalKuis::where('id_kuis', $kuis->id_kuis)->findOrFail($this->soalId);
            $soal->update($data);
            session()->flash('success_soal', 'Soal berhasil diperbarui.');
        } else {
            SoalKuis::create($data);
            session()->flash('success_soal', 'Soal berhasil ditambahkan.');
        }

        $this->resetSoalForm();
    }

    public function deleteSoal(int $id): void
    {
        $kuis = $this->kuisMilikGuru((int) $this->selectedKuisId);
        if (! $kuis) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        SoalKuis::where('id_kuis', $kuis->id_kuis)->findOrFail($id)->delete();
        session()->flash('success_soal', 'Soal berhasil dihapus.');
    }

    public function lihatHasil(int $id): void
    {
        $kuis = $this->kuisMilikGuru($id);
        if (! $kuis) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $this->selectedHasilKuisId = $id;
        $this->showHasilModal = true;
    }

    public function resetSoalForm(): void
    {
        $this->reset(['soalId', 'pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d']);
        $this->jawaban_benar = 'A';
        $this->poin = 1;
        $this->resetValidation();
    }

    private function jadwalMilikGuru(int $id): ?JadwalPelajaran
    {
        return JadwalPelajaran::whereHas('guruAmpu', function ($q) {
            $q->where('id_guru', $this->guruId);
        })->find($id);
    }

    private function kuisMilikGuru(int $id): ?Kuis
    {
        return Kuis::whereHas('jadwalPelajaran.guruAmpu', function ($q) {
            $q->where('id_guru', $this->guruId);
        })->find($id);
    }

    public function render()
    {
        $kuisList = collect();
        if ($this->selectedJadwalId && $this->jadwalMilikGuru((int) $this->selectedJadwalId)) {
            $kuisList = Kuis::where('id_jadwal_pelajaran', $this->selectedJadwalId)
                ->withCount(['soal', 'pengerjaan'])
                ->orderByDesc('created_at')
                ->get();
        }

        $selectedKuis = null;
        if ($this->selectedKuisId && $this->kuisMilikGuru((int) $this->selectedKuisId)) {
            $selectedKuis = Kuis::with('soal')->find($this->selectedKuisId);
        }

        $hasilKuis = null;
        if ($this->selectedHasilKuisId && $this->kuisMilikGuru((int) $this->selectedHasilKuisId)) {
            $hasilKuis = Kuis::with(['pengerjaan' => fn ($q) => $q->with('siswa')->orderByDesc('nilai')])
                ->find($this->selectedHasilKuisId);
        }

        return view('livewire.guru.kuis-management', [
            'kuisList' => $kuisList,
            'selectedKuis' => $selectedKuis,
            'hasilKuis' => $hasilKuis,
        ]);
    }
}
