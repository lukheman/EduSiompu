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

    public $kehadiran = [];

    public $nilaiEdit = [];

    public $konteksAmpu = [];

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
        $this->kehadiran = [];
        $this->nilaiEdit = [];
        $this->konteksAmpu = [];
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

        $this->pertemuans = $this->lengkapiOtomatis($tercatat, $jadwal->hari);

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

    /**
     * Lengkapi daftar pertemuan hingga 20 secara otomatis dengan tanggal
     * mingguan mengikuti hari jadwal, dihitung setelah tanggal tercatat terakhir.
     */
    private function lengkapiOtomatis(array $tercatat, string $hari): array
    {
        $mapHari = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0];
        $target = $mapHari[$hari] ?? 1;

        $semua = array_values(array_unique($tercatat));
        $kandidat = count($semua) > 0
            ? Carbon::parse(max($semua))->addDay()
            : Carbon::today();

        while (count($semua) < self::MAX_PERTEMUAN) {
            while ($kandidat->dayOfWeek !== $target) {
                $kandidat->addDay();
            }
            $tgl = $kandidat->format('Y-m-d');
            if (! in_array($tgl, $semua)) {
                $semua[] = $tgl;
            }
            $kandidat->addDay();
        }

        sort($semua);

        return array_slice($semua, 0, self::MAX_PERTEMUAN);
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
        $this->konteksAmpu = [
            'id_kelas' => $ampu->id_kelas,
            'id_tahun_ajaran' => $ampu->id_tahun_ajaran,
            'id_mata_pelajaran' => $ampu->id_mata_pelajaran,
        ];

        $raports = Raport::where('id_kelas', $ampu->id_kelas)
            ->where('id_tahun_ajaran', $ampu->id_tahun_ajaran)
            ->with(['nilaiRaport' => function ($q) use ($ampu) {
                $q->where('id_mata_pelajaran', $ampu->id_mata_pelajaran);
            }])
            ->get()
            ->keyBy('id_siswa');

        $this->nilaiEdit = [];
        foreach ($this->siswaList as $siswa) {
            $raportSiswa = $raports->get($siswa->id_siswa);
            /** @var NilaiRaport|null $nilai */
            $nilai = $raportSiswa?->nilaiRaport->first();
            $row = [];
            foreach (NilaiRaport::ASPEK_SCORES as $koloms) {
                foreach ($koloms as $kolom) {
                    $row[substr($kolom, 6)] = $nilai?->{$kolom} ?? '';
                }
            }
            $row['ulangan_semester'] = $nilai?->nilai_ulangan_semester ?? '';
            $row['raport'] = $nilai?->nilai_raport ?? '';
            $this->nilaiEdit[$siswa->id_siswa] = $row;
        }
    }

    private function nilaiKosong(): array
    {
        $kosong = ['ulangan_semester' => '', 'raport' => ''];

        foreach (NilaiRaport::ASPEK_SCORES as $koloms) {
            foreach ($koloms as $kolom) {
                $kosong[substr($kolom, 6)] = '';
            }
        }

        return $kosong;
    }

    public function updatedNilaiEdit($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) !== 2) {
            return;
        }

        [$idSiswa, $field] = $parts;

        if ($field !== 'raport') {
            $this->hitungNilaiRaport($idSiswa);
        }

        $this->simpanNilai((int) $idSiswa);
    }

    private function hitungNilaiRaport($idSiswa): void
    {
        $data = $this->nilaiEdit[$idSiswa] ?? [];
        $rataAspek = [];

        foreach (NilaiRaport::ASPEK_SCORES as $koloms) {
            $scores = [];
            foreach ($koloms as $kolom) {
                $scores[] = $data[substr($kolom, 6)] ?? '';
            }
            $rata = NilaiRaport::rataAspek($scores);
            if ($rata !== null) {
                $rataAspek[] = $rata;
            }
        }

        $us = $data['ulangan_semester'] ?? '';
        if ($us !== '' && $us !== null && is_numeric($us)) {
            $rataAspek[] = (float) $us;
        }

        $this->nilaiEdit[$idSiswa]['raport'] = count($rataAspek) > 0 ? (int) round(array_sum($rataAspek) / count($rataAspek)) : '';
    }

    public function rataAspekForm($idSiswa, array $fields): ?int
    {
        $data = $this->nilaiEdit[$idSiswa] ?? [];
        $scores = [];
        foreach ($fields as $field) {
            $scores[] = $data[$field] ?? '';
        }

        return NilaiRaport::rataAspek($scores);
    }

    private function getPredikat($nilai)
    {
        if ($nilai === '' || $nilai === null) {
            return null;
        }
        if ($nilai >= 90) {
            return 'A';
        }
        if ($nilai >= 80) {
            return 'B';
        }
        if ($nilai >= 70) {
            return 'C';
        }

        return 'D';
    }

    public function simpanNilai(int $idSiswa): void
    {
        if (empty($this->konteksAmpu)) {
            return;
        }

        $data = array_merge($this->nilaiKosong(), $this->nilaiEdit[$idSiswa] ?? []);

        foreach ($data as $field => $nilai) {
            $data[$field] = ($nilai === '') ? null : $nilai;
        }
        $this->nilaiEdit[$idSiswa] = $data;

        if ($data['raport'] === null) {
            $this->hitungNilaiRaport($idSiswa);
            $otomatis = $this->nilaiEdit[$idSiswa]['raport'] ?? '';
            $data['raport'] = ($otomatis === '') ? null : $otomatis;
        }

        if (count(array_filter($data, fn ($v) => $v !== null)) === 0) {
            return;
        }

        $raport = Raport::firstOrCreate(
            ['id_siswa' => $idSiswa, 'id_tahun_ajaran' => $this->konteksAmpu['id_tahun_ajaran']],
            ['id_kelas' => $this->konteksAmpu['id_kelas']]
        );

        $payload = [];
        foreach (NilaiRaport::ASPEK_SCORES as $koloms) {
            foreach ($koloms as $kolom) {
                $payload[$kolom] = $data[substr($kolom, 6)];
            }
        }

        $payload['predikat_afektif'] = $this->getPredikat(NilaiRaport::rataAspek([$payload['nilai_afektif_1'], $payload['nilai_afektif_2'], $payload['nilai_afektif_3']]));
        $payload['predikat_psikomotor'] = $this->getPredikat(NilaiRaport::rataAspek([$payload['nilai_psikomotor_1'], $payload['nilai_psikomotor_2'], $payload['nilai_psikomotor_3'], $payload['nilai_psikomotor_4']]));
        $payload['nilai_ulangan_semester'] = $data['ulangan_semester'];
        $payload['nilai_raport'] = $data['raport'];
        $payload['predikat_raport'] = $this->getPredikat($data['raport']);

        NilaiRaport::updateOrCreate(
            ['id_raport' => $raport->id_raport, 'id_mata_pelajaran' => $this->konteksAmpu['id_mata_pelajaran']],
            $payload
        );
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
