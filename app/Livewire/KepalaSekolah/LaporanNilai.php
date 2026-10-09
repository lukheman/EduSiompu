<?php

namespace App\Livewire\KepalaSekolah;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Laporan Nilai')]
class LaporanNilai extends Component
{
    public $kelasOptions = [];

    public $selectedKelasId = '';

    public $tahunAjaranOptions = [];

    public $selectedTahunId = '';

    public function mount()
    {
        $this->kelasOptions = Kelas::orderBy('nama_kelas')
            ->pluck('nama_kelas', 'id_kelas')
            ->toArray();

        $this->tahunAjaranOptions = TahunAjaran::orderBy('nama_tahun', 'desc')->get();

        $aktif = TahunAjaran::where('status_aktif', true)->first();
        if ($aktif) {
            $this->selectedTahunId = $aktif->id_tahun_ajaran;
        }
    }

    public function render()
    {
        $siswas = collect();
        $kelas = null;

        if ($this->selectedKelasId) {
            $kelas = Kelas::find($this->selectedKelasId);
            $siswas = Siswa::where('id_kelas', $this->selectedKelasId)
                ->with(['raport' => function ($q) {
                    $q->where('id_tahun_ajaran', $this->selectedTahunId);
                }])
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('livewire.kepala-sekolah.laporan-nilai', [
            'siswas' => $siswas,
            'kelas' => $kelas,
        ]);
    }
}
