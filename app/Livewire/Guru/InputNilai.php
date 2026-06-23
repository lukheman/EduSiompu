<?php

namespace App\Livewire\Guru;

use App\Models\GuruAmpu;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Input Nilai Raport')]
class InputNilai extends Component
{
    public $guruAmpus = [];
    public $selectedAmpuId = '';
    public $siswas = [];
    public $nilaiData = [];

    public function mount()
    {
        $id_guru = Auth::guard('guru')->id();
        $this->guruAmpus = GuruAmpu::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
            ->where('id_guru', $id_guru)
            ->whereHas('tahunAjaran', function($q) {
                $q->where('status_aktif', true);
            })
            ->get();
    }

    public function updatedSelectedAmpuId()
    {
        $this->loadSiswas();
    }

    public function loadSiswas()
    {
        if (!$this->selectedAmpuId) {
            $this->siswas = [];
            $this->nilaiData = [];
            return;
        }

        $ampu = GuruAmpu::find($this->selectedAmpuId);
        if (!$ampu) return;

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
                    'keterampilan' => $nilai->nilai_keterampilan ?? ''
                ];
            } else {
                $this->nilaiData[$siswa->id_siswa] = [
                    'pengetahuan' => '',
                    'keterampilan' => ''
                ];
            }
        }
    }

    public function getPredikat($nilai)
    {
        if ($nilai === '' || $nilai === null) return null;
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        return 'D';
    }

    public function simpan()
    {
        if (!$this->selectedAmpuId) return;

        $ampu = GuruAmpu::find($this->selectedAmpuId);
        
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
