<?php

namespace App\Livewire\Guru;

use App\Enums\StatusKehadiran;
use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JadwalAbsensi extends Component
{
    public $jadwals;

    public $selectedJadwalId = null;

    public $filterHari = null;

    public $siswaList = [];

    public $pertemuans = [];

    public $tanggalBaru = '';

    public $tanggalBaruPending = [];

    public $kehadiran = [];

    public $nilaiMap = [];

    public const MAX_PERTEMUAN = 20;

    public function mount()
    {
        $daysMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        $todayEn = Carbon::now()->format('l');
        $this->filterHari = $daysMap[$todayEn] ?? 'Senin';

        $this->loadJadwals();
    }

    public function updatedFilterHari()
    {
        $this->selectedJadwalId = null;
        $this->resetAbsensiState();
        $this->loadJadwals();
    }

    public function loadJadwals()
    {
        $guruId = Auth::guard('guru')->id();
        $query = JadwalPelajaran::whereHas('guruAmpu', function ($q) use ($guruId) {
            $q->where('id_guru', $guruId);
        });

        if ($this->filterHari) {
            $query->where('hari', $this->filterHari);
        }

        $this->jadwals = $query->with(['guruAmpu.kelas', 'guruAmpu.mataPelajaran'])
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 WHEN 'Minggu' THEN 7 ELSE 8 END")
            ->orderBy('jam_mulai')
            ->get();
    }

    public function selectJadwal($jadwalId)
    {
        $this->selectedJadwalId = $jadwalId;
        $this->resetAbsensiState();
        $this->loadAbsensi();
    }

    private function resetAbsensiState(): void
    {
        $this->siswaList = [];
        $this->pertemuans = [];
        $this->tanggalBaru = '';
        $this->tanggalBaruPending = [];
        $this->kehadiran = [];
        $this->nilaiMap = [];
    }

    private function selectedJadwal(): ?JadwalPelajaran
    {
        if (! $this->selectedJadwalId) {
            return null;
        }

        return JadwalPelajaran::with('guruAmpu')
            ->whereHas('guruAmpu', function ($q) {
                $q->where('id_guru', Auth::guard('guru')->id());
            })
            ->find($this->selectedJadwalId);
    }

    public function loadAbsensi()
    {
        $jadwal = $this->selectedJadwal();
        if (! $jadwal) {
            $this->resetAbsensiState();

            return;
        }

        $this->siswaList = Siswa::where('id_kelas', $jadwal->guruAmpu->id_kelas)
            ->orderBy('nama_siswa')
            ->get();

        $tercatat = Absensi::where('id_jadwal_pelajaran', $jadwal->id_jadwal_pelajaran)
            ->select('tanggal')
            ->distinct()
            ->orderBy('tanggal')
            ->pluck('tanggal')
            ->map(fn ($t) => $t->format('Y-m-d'))
            ->all();

        $this->pertemuans = array_values(array_unique(array_merge($tercatat, $this->tanggalBaruPending)));
        sort($this->pertemuans);
        $this->pertemuans = array_slice($this->pertemuans, 0, self::MAX_PERTEMUAN);

        $records = Absensi::where('id_jadwal_pelajaran', $jadwal->id_jadwal_pelajaran)
            ->whereIn('tanggal', $this->pertemuans)
            ->get();

        $this->kehadiran = [];
        foreach ($records as $record) {
            $status = $record->status_kehadiran instanceof StatusKehadiran
                ? $record->status_kehadiran->value
                : $record->status_kehadiran;
            $this->kehadiran[$record->id_siswa][$record->tanggal->format('Y-m-d')] = $status;
        }

        $this->loadNilai($jadwal);
    }

    public function tambahPertemuan()
    {
        $this->validate([
            'tanggalBaru' => ['required', 'date'],
        ]);

        if (count($this->pertemuans) >= self::MAX_PERTEMUAN) {
            session()->flash('error', 'Maksimal '.self::MAX_PERTEMUAN.' pertemuan.');

            return;
        }

        if (in_array($this->tanggalBaru, $this->pertemuans)) {
            session()->flash('error', 'Tanggal tersebut sudah ada di daftar pertemuan.');

            return;
        }

        $this->tanggalBaruPending[] = $this->tanggalBaru;
        $this->tanggalBaru = '';
        $this->loadAbsensi();
    }

    public function toggleKehadiran(int $idSiswa, string $tanggal)
    {
        $jadwal = $this->selectedJadwal();
        if (! $jadwal) {
            return;
        }

        if (! in_array($tanggal, $this->pertemuans)) {
            return;
        }

        $urutan = ['hadir', 'sakit', 'izin', 'alpa'];
        $saatIni = $this->kehadiran[$idSiswa][$tanggal] ?? null;

        if ($saatIni === null) {
            $berikutnya = 'hadir';
        } else {
            $index = array_search($saatIni, $urutan);
            $berikutnya = $index === false || $index === count($urutan) - 1 ? null : $urutan[$index + 1];
        }

        if ($berikutnya === null) {
            Absensi::where('id_jadwal_pelajaran', $jadwal->id_jadwal_pelajaran)
                ->where('id_siswa', $idSiswa)
                ->whereDate('tanggal', $tanggal)
                ->delete();
            unset($this->kehadiran[$idSiswa][$tanggal]);
        } else {
            $record = Absensi::where('id_jadwal_pelajaran', $jadwal->id_jadwal_pelajaran)
                ->where('id_siswa', $idSiswa)
                ->whereDate('tanggal', $tanggal)
                ->first() ?? new Absensi([
                    'id_jadwal_pelajaran' => $jadwal->id_jadwal_pelajaran,
                    'id_siswa' => $idSiswa,
                    'tanggal' => $tanggal,
                ]);
            $record->status_kehadiran = $berikutnya;
            $record->save();
            $this->kehadiran[$idSiswa][$tanggal] = $berikutnya;
        }
    }

    private function loadNilai(JadwalPelajaran $jadwal): void
    {
        $ampu = $jadwal->guruAmpu;
        $raports = Raport::where('id_kelas', $ampu->id_kelas)
            ->where('id_tahun_ajaran', $ampu->id_tahun_ajaran)
            ->with(['nilaiRaport' => function ($q) use ($ampu) {
                $q->where('id_mata_pelajaran', $ampu->id_mata_pelajaran);
            }])
            ->get()
            ->keyBy('id_siswa');

        $this->nilaiMap = [];
        foreach ($this->siswaList as $siswa) {
            $raportSiswa = $raports->get($siswa->id_siswa);
            /** @var NilaiRaport|null $nilai */
            $nilai = $raportSiswa?->nilaiRaport->first();
            $this->nilaiMap[$siswa->id_siswa] = $nilai ? [
                'afektif' => $nilai->rata_afektif,
                'psikomotor' => $nilai->rata_psikomotor,
                'tugas' => $nilai->rata_tugas,
                'uh' => $nilai->rata_ulangan_harian,
                'nts' => $nilai->rata_tugas,
                'nus' => $nilai->nilai_ulangan_semester,
                'nr' => $nilai->nilai_raport,
            ] : null;
        }
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

    public function render()
    {
        return view('livewire.guru.jadwal-absensi');
    }
}
