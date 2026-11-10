<?php

namespace App\Livewire\Siswa;

use App\Models\JawabanKuis;
use App\Models\Kuis;
use App\Models\PengerjaanKuis;
use App\Models\SoalKuis;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kuis')]
class KuisList extends Component
{
    public ?int $siswaId = null;

    public string $activeTab = 'tersedia';

    public bool $showKuisModal = false;

    public ?int $selectedKuisId = null;

    public array $jawaban = [];

    public bool $showHasilModal = false;

    public ?int $selectedHasilPengerjaanId = null;

    public function mount(): void
    {
        $this->siswaId = Auth::guard('siswa')->id();
    }

    public function bukaKuis(int $id): void
    {
        $kuis = $this->kuisUntukSiswa($id);
        if (! $kuis) {
            abort(403, 'Kuis ini bukan untuk kelas Anda.');
        }

        if ($kuis->soal()->count() === 0) {
            session()->flash('error', 'Kuis ini belum memiliki soal.');

            return;
        }

        $pengerjaan = PengerjaanKuis::where('id_kuis', $kuis->id_kuis)
            ->where('id_siswa', $this->siswaId)
            ->first();

        if ($pengerjaan?->selesai()) {
            $this->showHasil($pengerjaan->id_pengerjaan);

            return;
        }

        if (! $pengerjaan) {
            if ($kuis->tenggat_waktu->isPast()) {
                session()->flash('error', 'Waktu pengerjaan kuis ini sudah berakhir.');

                return;
            }

            $pengerjaan = PengerjaanKuis::create([
                'id_kuis' => $kuis->id_kuis,
                'id_siswa' => $this->siswaId,
                'waktu_mulai' => now(),
                'jumlah_soal' => $kuis->soal()->count(),
            ]);
        }

        if ($this->waktuHabis($pengerjaan)) {
            $this->finalisasi($pengerjaan);
            $this->showHasil($pengerjaan->id_pengerjaan);

            return;
        }

        $this->selectedKuisId = $kuis->id_kuis;
        $this->jawaban = $pengerjaan->jawaban()
            ->whereNotNull('jawaban')
            ->pluck('jawaban', 'id_soal')
            ->all();
        $this->resetValidation();
        $this->showKuisModal = true;
    }

    public function updatedJawaban($value, $key): void
    {
        if (! $this->selectedKuisId) {
            return;
        }

        $pengerjaan = $this->pengerjaanAktif();
        if (! $pengerjaan || $pengerjaan->selesai()) {
            return;
        }

        if ($this->waktuHabis($pengerjaan)) {
            $this->finalisasi($pengerjaan);
            $this->showKuisModal = false;
            $this->selectedKuisId = null;
            $this->showHasil($pengerjaan->id_pengerjaan);

            return;
        }

        $soal = SoalKuis::where('id_kuis', $this->selectedKuisId)->find($key);
        if (! $soal || ! in_array($value, ['A', 'B', 'C', 'D'], true)) {
            return;
        }

        JawabanKuis::updateOrCreate(
            ['id_pengerjaan' => $pengerjaan->id_pengerjaan, 'id_soal' => $soal->id_soal],
            ['jawaban' => $value, 'benar' => $value === $soal->jawaban_benar]
        );
    }

    public function submitKuis(): void
    {
        $pengerjaan = $this->pengerjaanAktif();
        if (! $pengerjaan) {
            return;
        }

        if ($pengerjaan->selesai()) {
            $this->showKuisModal = false;
            $this->selectedKuisId = null;
            $this->showHasil($pengerjaan->id_pengerjaan);

            return;
        }

        if (! $this->waktuHabis($pengerjaan)) {
            $jumlahSoal = $pengerjaan->kuis->soal()->count();
            $jumlahDijawab = $pengerjaan->jawaban()->whereNotNull('jawaban')->count();

            if ($jumlahDijawab < $jumlahSoal) {
                $this->addError('jawaban', 'Masih ada '.($jumlahSoal - $jumlahDijawab).' soal yang belum dijawab.');

                return;
            }
        }

        $this->finalisasi($pengerjaan);
        $this->showKuisModal = false;
        $this->selectedKuisId = null;
        $this->showHasil($pengerjaan->id_pengerjaan);
    }

    public function showHasil(int $idPengerjaan): void
    {
        $pengerjaan = PengerjaanKuis::where('id_siswa', $this->siswaId)->find($idPengerjaan);
        if (! $pengerjaan) {
            abort(403, 'Hasil kuis ini bukan milik Anda.');
        }

        $this->selectedHasilPengerjaanId = $pengerjaan->id_pengerjaan;
        $this->showHasilModal = true;
    }

    private function finalisasi(PengerjaanKuis $pengerjaan): void
    {
        if ($pengerjaan->selesai()) {
            return;
        }

        $pengerjaan->loadMissing('kuis.soal');
        $soal = $pengerjaan->kuis->soal;
        $jawaban = $pengerjaan->jawaban()->get()->keyBy('id_soal');

        $totalPoin = $soal->sum('poin');
        $poinBenar = 0;
        $jumlahBenar = 0;

        foreach ($soal as $item) {
            $jawab = $jawaban->get($item->id_soal);
            if ($jawab && $jawab->jawaban === $item->jawaban_benar) {
                $poinBenar += $item->poin;
                $jumlahBenar++;
            }
        }

        $pengerjaan->update([
            'nilai' => $totalPoin > 0 ? (int) round(($poinBenar / $totalPoin) * 100) : 0,
            'jumlah_benar' => $jumlahBenar,
            'jumlah_soal' => $soal->count(),
            'waktu_selesai' => now(),
        ]);
    }

    private function pengerjaanAktif(): ?PengerjaanKuis
    {
        if (! $this->selectedKuisId) {
            return null;
        }

        return PengerjaanKuis::with('kuis')
            ->where('id_kuis', $this->selectedKuisId)
            ->where('id_siswa', $this->siswaId)
            ->first();
    }

    private function waktuHabis(PengerjaanKuis $pengerjaan): bool
    {
        return now()->greaterThan(
            $pengerjaan->waktu_mulai->copy()->addMinutes($pengerjaan->kuis->durasi_menit)
        );
    }

    private function kuisUntukSiswa(int $id): ?Kuis
    {
        $siswa = Auth::guard('siswa')->user();

        return Kuis::whereHas('jadwalPelajaran.guruAmpu', function ($q) use ($siswa) {
            $q->where('id_kelas', $siswa->id_kelas)
                ->whereHas('tahunAjaran', fn ($q2) => $q2->where('status_aktif', true));
        })->find($id);
    }

    public function render()
    {
        $siswa = Auth::guard('siswa')->user();

        $kuisQuery = Kuis::whereHas('jadwalPelajaran.guruAmpu', function ($q) use ($siswa) {
            $q->where('id_kelas', $siswa->id_kelas)
                ->whereHas('tahunAjaran', fn ($q2) => $q2->where('status_aktif', true));
        })
            ->with(['jadwalPelajaran.guruAmpu.mataPelajaran', 'jadwalPelajaran.guruAmpu.guru'])
            ->withCount('soal')
            ->with(['pengerjaan' => fn ($q) => $q->where('id_siswa', $this->siswaId)]);

        if ($this->activeTab === 'selesai') {
            $kuisList = $kuisQuery
                ->whereHas('pengerjaan', fn ($q) => $q->where('id_siswa', $this->siswaId)->whereNotNull('waktu_selesai'))
                ->orderByDesc('tenggat_waktu')
                ->get();
        } else {
            $kuisList = $kuisQuery
                ->whereDoesntHave('pengerjaan', fn ($q) => $q->where('id_siswa', $this->siswaId)->whereNotNull('waktu_selesai'))
                ->orderBy('tenggat_waktu')
                ->get();
        }

        $selectedKuis = $this->selectedKuisId
            ? Kuis::with(['soal', 'jadwalPelajaran.guruAmpu.mataPelajaran'])->find($this->selectedKuisId)
            : null;

        $pengerjaanAktif = $this->selectedKuisId
            ? PengerjaanKuis::where('id_kuis', $this->selectedKuisId)->where('id_siswa', $this->siswaId)->first()
            : null;

        $hasilPengerjaan = $this->selectedHasilPengerjaanId
            ? PengerjaanKuis::with(['kuis.soal', 'jawaban'])->where('id_siswa', $this->siswaId)->find($this->selectedHasilPengerjaanId)
            : null;

        $sisaDetik = $pengerjaanAktif
            ? max(0, (int) now()->diffInSeconds($pengerjaanAktif->waktu_mulai->copy()->addMinutes($selectedKuis?->durasi_menit ?? 0), false))
            : 0;

        return view('livewire.siswa.kuis-list', [
            'kuisList' => $kuisList,
            'selectedKuis' => $selectedKuis,
            'pengerjaanAktif' => $pengerjaanAktif,
            'hasilPengerjaan' => $hasilPengerjaan,
            'sisaDetik' => $sisaDetik,
        ]);
    }
}
