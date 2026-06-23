<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\JadwalPelajaran;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Enums\StatusKehadiran;
use Illuminate\Support\Facades\Auth;

class JadwalAbsensi extends Component
{
    public $jadwals;
    public $selectedJadwalId = null;
    public $selectedTanggal = null;
    public $filterHari = null;
    public $siswaList = [];
    public $kehadiran = [];

    public function mount()
    {
        $this->selectedTanggal = date('Y-m-d');
        
        $daysMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        $todayEn = \Carbon\Carbon::now()->format('l');
        $this->filterHari = $daysMap[$todayEn] ?? 'Senin';

        $this->loadJadwals();
    }

    public function updatedFilterHari()
    {
        $this->selectedJadwalId = null;
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
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get();
    }

    public function selectJadwal($jadwalId)
    {
        $this->selectedJadwalId = $jadwalId;
        $this->loadAbsensi();
    }

    public function updatedSelectedTanggal()
    {
        if ($this->selectedJadwalId) {
            $this->loadAbsensi();
        }
    }

    public function loadAbsensi()
    {
        if (!$this->selectedJadwalId || !$this->selectedTanggal) {
            return;
        }

        $jadwal = JadwalPelajaran::with('guruAmpu.kelas')->find($this->selectedJadwalId);
        if (!$jadwal) return;

        $idKelas = $jadwal->guruAmpu->id_kelas;
        $this->siswaList = Siswa::where('id_kelas', $idKelas)
            ->orderBy('nama_siswa')
            ->get();

        $absensiRecords = Absensi::where('id_jadwal_pelajaran', $this->selectedJadwalId)
            ->where('tanggal', $this->selectedTanggal)
            ->get()
            ->keyBy('id_siswa');

        $this->kehadiran = [];
        foreach ($this->siswaList as $siswa) {
            if (isset($absensiRecords[$siswa->id_siswa])) {
                $this->kehadiran[$siswa->id_siswa] = $absensiRecords[$siswa->id_siswa]->status_kehadiran->value;
            } else {
                $this->kehadiran[$siswa->id_siswa] = StatusKehadiran::HADIR->value; // default
            }
        }
    }

    public function saveAbsensi()
    {
        if (!$this->selectedJadwalId || !$this->selectedTanggal) {
            return;
        }

        foreach ($this->kehadiran as $idSiswa => $status) {
            Absensi::updateOrCreate(
                [
                    'id_jadwal_pelajaran' => $this->selectedJadwalId,
                    'id_siswa' => $idSiswa,
                    'tanggal' => $this->selectedTanggal,
                ],
                [
                    'status_kehadiran' => $status
                ]
            );
        }

        session()->flash('success', 'Absensi berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.guru.jadwal-absensi');
    }
}
