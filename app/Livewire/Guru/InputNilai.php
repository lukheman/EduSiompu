<?php

namespace App\Livewire\Guru;

use App\Models\GuruAmpu;
use App\Models\Kelas;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Input Nilai Raport')]
class InputNilai extends Component
{
    public $kelasOptions = [];

    public $selectedKelasId = '';

    public $mapelOptions = [];

    public $selectedAmpuId = '';

    public $siswas = [];

    public $nilaiData = [];

    public function mount()
    {
        $id_guru = Auth::guard('guru')->id();
        $this->kelasOptions = Kelas::where('id_guru', $id_guru)
            ->orderBy('nama_kelas')
            ->pluck('nama_kelas', 'id_kelas')
            ->toArray();
    }

    public function updatedSelectedKelasId()
    {
        $this->selectedAmpuId = '';
        $this->mapelOptions = [];
        $this->siswas = [];
        $this->nilaiData = [];

        if (! $this->selectedKelasId) {
            return;
        }

        if (! $this->kelasDiawalikan()) {
            return;
        }

        $this->mapelOptions = GuruAmpu::with(['mataPelajaran', 'tahunAjaran'])
            ->where('id_kelas', $this->selectedKelasId)
            ->whereHas('tahunAjaran', function ($q) {
                $q->where('status_aktif', true);
            })
            ->get()
            ->mapWithKeys(function ($ampu) {
                return [$ampu->id_guru_ampu => $ampu->mataPelajaran->nama_mapel.' ('.$ampu->tahunAjaran->nama_tahun.' '.ucfirst($ampu->tahunAjaran->semester).')'];
            })
            ->toArray();
    }

    public function updatedSelectedAmpuId()
    {
        $this->loadSiswas();
    }

    private function kelasDiawalikan(): bool
    {
        return Kelas::where('id_kelas', $this->selectedKelasId)
            ->where('id_guru', Auth::guard('guru')->id())
            ->exists();
    }

    public function loadSiswas()
    {
        if (! $this->selectedAmpuId) {
            $this->siswas = [];
            $this->nilaiData = [];

            return;
        }

        $ampu = GuruAmpu::find($this->selectedAmpuId);
        if (! $ampu || $ampu->id_kelas != $this->selectedKelasId || ! $this->kelasDiawalikan()) {
            return;
        }

        $this->siswas = Siswa::where('id_kelas', $ampu->id_kelas)->get();

        $this->nilaiData = [];
        foreach ($this->siswas as $siswa) {
            $raport = Raport::where('id_siswa', $siswa->id_siswa)
                ->where('id_tahun_ajaran', $ampu->id_tahun_ajaran)
                ->first();

            if ($raport) {
                $nilai = NilaiRaport::where('id_raport', $raport->id_raport)
                    ->where('id_mata_pelajaran', $ampu->id_mata_pelajaran)
                    ->first();

                $this->nilaiData[$siswa->id_siswa] = [
                    'pengetahuan' => $nilai->nilai_pengetahuan ?? '',
                    'keterampilan' => $nilai->nilai_keterampilan ?? '',
                ];
            } else {
                $this->nilaiData[$siswa->id_siswa] = [
                    'pengetahuan' => '',
                    'keterampilan' => '',
                ];
            }
        }
    }

    public function getPredikat($nilai)
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

    public function simpan()
    {
        if (! $this->selectedAmpuId) {
            return;
        }

        $ampu = GuruAmpu::find($this->selectedAmpuId);
        if (! $ampu || $ampu->id_kelas != $this->selectedKelasId || ! $this->kelasDiawalikan()) {
            return;
        }

        foreach ($this->siswas as $siswa) {
            $p = $this->nilaiData[$siswa->id_siswa]['pengetahuan'] ?? null;
            $k = $this->nilaiData[$siswa->id_siswa]['keterampilan'] ?? null;

            $p = ($p === '') ? null : $p;
            $k = ($k === '') ? null : $k;

            if ($p !== null || $k !== null) {
                $raport = Raport::firstOrCreate(
                    ['id_siswa' => $siswa->id_siswa, 'id_tahun_ajaran' => $ampu->id_tahun_ajaran],
                    ['id_kelas' => $ampu->id_kelas]
                );

                NilaiRaport::updateOrCreate(
                    ['id_raport' => $raport->id_raport, 'id_mata_pelajaran' => $ampu->id_mata_pelajaran],
                    [
                        'nilai_pengetahuan' => $p,
                        'predikat_pengetahuan' => $this->getPredikat($p),
                        'nilai_keterampilan' => $k,
                        'predikat_keterampilan' => $this->getPredikat($k),
                    ]
                );
            }
        }

        session()->flash('message', 'Nilai berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.guru.input-nilai');
    }
}
