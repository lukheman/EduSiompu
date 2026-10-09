<?php

namespace App\Livewire\Guru;

use App\Enums\StatusKehadiran;
use App\Models\Absensi;
use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Laporan Absensi')]
class LaporanAbsensi extends Component
{
    public $kelasOptions = [];

    public $selectedKelasId = '';

    public $tahunAjaranOptions = [];

    public $selectedTahunId = '';

    public $mapelOptions = [];

    public $selectedMapelId = '';

    public function mount()
    {
        $id_guru = Auth::guard('guru')->id();
        $this->kelasOptions = Kelas::where('id_guru', $id_guru)
            ->orderBy('nama_kelas')
            ->pluck('nama_kelas', 'id_kelas')
            ->toArray();

        $this->tahunAjaranOptions = TahunAjaran::orderBy('nama_tahun', 'desc')->get();

        $aktif = TahunAjaran::where('status_aktif', true)->first();
        if ($aktif) {
            $this->selectedTahunId = $aktif->id_tahun_ajaran;
        }
    }

    public function updatedSelectedKelasId()
    {
        $this->selectedMapelId = '';
        $this->loadMapelOptions();
    }

    public function updatedSelectedTahunId()
    {
        $this->selectedMapelId = '';
        $this->loadMapelOptions();
    }

    private function loadMapelOptions(): void
    {
        $this->mapelOptions = [];

        if (! $this->selectedKelasId || ! $this->selectedTahunId || ! $this->kelasDiawalikan()) {
            return;
        }

        $this->mapelOptions = GuruAmpu::with('mataPelajaran')
            ->where('id_kelas', $this->selectedKelasId)
            ->where('id_tahun_ajaran', $this->selectedTahunId)
            ->get()
            ->mapWithKeys(fn ($ampu) => [$ampu->id_mata_pelajaran => $ampu->mataPelajaran->nama_mapel])
            ->toArray();
    }

    public function render()
    {
        $siswas = collect();
        $kelas = null;
        $mapel = null;
        $pertemuans = collect();
        $kehadiran = [];
        $rekap = [];
        $mapelNilai = [];

        if ($this->selectedKelasId && $this->kelasDiawalikan()) {
            $kelas = Kelas::find($this->selectedKelasId);
            $siswas = Siswa::where('id_kelas', $this->selectedKelasId)
                ->orderBy('nama_siswa')
                ->get();

            foreach ($siswas as $siswa) {
                $rekap[$siswa->id_siswa] = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];
            }

            if ($this->selectedMapelId && $this->mapelValid()) {
                $mapel = $this->mapelOptions[$this->selectedMapelId] ?? null;

                $mapelNilai = Raport::where('id_kelas', $this->selectedKelasId)
                    ->where('id_tahun_ajaran', $this->selectedTahunId)
                    ->with(['nilaiRaport' => function ($q) {
                        $q->where('id_mata_pelajaran', $this->selectedMapelId);
                    }])
                    ->get()
                    ->mapWithKeys(fn ($raport) => [$raport->id_siswa => $raport->nilaiRaport->first()])
                    ->all();

                $ampu = GuruAmpu::where('id_kelas', $this->selectedKelasId)
                    ->where('id_tahun_ajaran', $this->selectedTahunId)
                    ->where('id_mata_pelajaran', $this->selectedMapelId)
                    ->first();

                if ($ampu) {
                    $jadwalIds = JadwalPelajaran::where('id_guru_ampu', $ampu->id_guru_ampu)
                        ->pluck('id_jadwal_pelajaran');

                    $pertemuans = Absensi::whereIn('id_jadwal_pelajaran', $jadwalIds)
                        ->select('tanggal')
                        ->distinct()
                        ->orderBy('tanggal')
                        ->limit(20)
                        ->pluck('tanggal');

                    $tanggalStrings = $pertemuans->map(fn ($t) => $t->format('Y-m-d'))->all();

                    $absensiList = Absensi::whereIn('id_jadwal_pelajaran', $jadwalIds)
                        ->whereIn('tanggal', $tanggalStrings)
                        ->get();

                    foreach ($absensiList as $absen) {
                        $status = $absen->status_kehadiran instanceof StatusKehadiran
                            ? $absen->status_kehadiran->value
                            : $absen->status_kehadiran;
                        $tgl = $absen->tanggal->format('Y-m-d');
                        $kehadiran[$absen->id_siswa][$tgl] = $status;
                        if (isset($rekap[$absen->id_siswa][$status])) {
                            $rekap[$absen->id_siswa][$status]++;
                        }
                    }
                }
            }
        }

        return view('livewire.guru.laporan-absensi', [
            'siswas' => $siswas,
            'kelas' => $kelas,
            'mapel' => $mapel,
            'pertemuans' => $pertemuans,
            'kehadiran' => $kehadiran,
            'rekap' => $rekap,
            'mapelNilai' => $mapelNilai,
        ]);
    }

    private function mapelValid(): bool
    {
        return GuruAmpu::where('id_kelas', $this->selectedKelasId)
            ->where('id_tahun_ajaran', $this->selectedTahunId)
            ->where('id_mata_pelajaran', $this->selectedMapelId)
            ->exists();
    }

    private function kelasDiawalikan(): bool
    {
        return Kelas::where('id_kelas', $this->selectedKelasId)
            ->where('id_guru', Auth::guard('guru')->id())
            ->exists();
    }

    public function simbol(?string $status): string
    {
        return match ($status) {
            'hadir' => '✓',
            'sakit' => 'S',
            'izin' => 'I',
            'alpa' => 'A',
            default => '-',
        };
    }
}
