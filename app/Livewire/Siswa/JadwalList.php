<?php

namespace App\Livewire\Siswa;

use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Jadwal Pelajaran')]
class JadwalList extends Component
{
    public ?int $id_tahun_ajaran = null;

    public string $tanggal;

    public function mount()
    {
        $this->tanggal = date('Y-m-d');

        $activeTahun = TahunAjaran::where('status_aktif', true)->first();
        if ($activeTahun) {
            $this->id_tahun_ajaran = $activeTahun->id_tahun_ajaran;
        }
    }

    public function updatedIdTahunAjaran()
    {
        // filter applies on next render
    }

    public function updatedTanggal()
    {
        // filter applies on next render
    }

    public function render()
    {
        $siswa = Auth::guard('siswa')->user();

        $hari = $this->resolveHari($this->tanggal);

        $query = JadwalPelajaran::with(['guruAmpu.mataPelajaran', 'guruAmpu.guru', 'guruAmpu.kelas', 'guruAmpu.tahunAjaran'])
            ->whereHas('guruAmpu', function ($q) use ($siswa) {
                $q->where('id_kelas', $siswa->id_kelas);

                if ($this->id_tahun_ajaran) {
                    $q->where('id_tahun_ajaran', $this->id_tahun_ajaran);
                }
            });

        if ($hari) {
            $query->where('hari', $hari);
        }

        $jadwalList = $query
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 WHEN 'Minggu' THEN 7 ELSE 8 END")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        $tahunAjaranOptions = TahunAjaran::orderBy('nama_tahun', 'desc')->get();

        return view('livewire.siswa.jadwal-list', [
            'jadwalList' => $jadwalList,
            'tahunAjaranOptions' => $tahunAjaranOptions,
            'kelas' => $siswa->kelas,
            'hari' => $hari,
        ]);
    }

    private function resolveHari(?string $tanggal): ?string
    {
        if (! $tanggal) {
            return null;
        }

        try {
            $englishDay = Carbon::parse($tanggal)->format('l');
        } catch (\Exception) {
            return null;
        }

        return [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ][$englishDay] ?? null;
    }
}
