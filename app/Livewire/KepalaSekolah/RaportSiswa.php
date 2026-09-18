<?php

namespace App\Livewire\KepalaSekolah;

use App\Models\Kelas;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Raport Siswa')]
class RaportSiswa extends Component
{
    public $kelasOptions = [];

    public $selectedKelasId = '';

    public $siswaOptions = [];

    public $selectedSiswaId = '';

    public $tahunAjarans = [];

    public $selectedTahunId = '';

    public $raport = null;

    public function mount()
    {
        $this->kelasOptions = Kelas::orderBy('nama_kelas')
            ->pluck('nama_kelas', 'id_kelas')
            ->toArray();
    }

    public function updatedSelectedKelasId()
    {
        $this->selectedSiswaId = '';
        $this->siswaOptions = [];
        $this->tahunAjarans = [];
        $this->selectedTahunId = '';
        $this->raport = null;

        if (! $this->selectedKelasId) {
            return;
        }

        $this->siswaOptions = Siswa::where('id_kelas', $this->selectedKelasId)
            ->orderBy('nama_siswa')
            ->pluck('nama_siswa', 'id_siswa')
            ->toArray();
    }

    public function updatedSelectedSiswaId()
    {
        $this->selectedTahunId = '';
        $this->raport = null;
        $this->loadTahunAjaran();
    }

    public function updatedSelectedTahunId()
    {
        $this->loadRaport();
    }

    private function loadTahunAjaran(): void
    {
        $this->tahunAjarans = [];

        if (! $this->selectedSiswaId) {
            return;
        }

        $raportTahunIds = Raport::where('id_siswa', $this->selectedSiswaId)->pluck('id_tahun_ajaran');
        $this->tahunAjarans = TahunAjaran::whereIn('id_tahun_ajaran', $raportTahunIds)->get();

        $aktif = TahunAjaran::where('status_aktif', true)->first();
        if ($aktif && $this->tahunAjarans->contains('id_tahun_ajaran', $aktif->id_tahun_ajaran)) {
            $this->selectedTahunId = $aktif->id_tahun_ajaran;
        } elseif (count($this->tahunAjarans) > 0) {
            $this->selectedTahunId = $this->tahunAjarans->first()->id_tahun_ajaran;
        }

        $this->loadRaport();
    }

    private function loadRaport(): void
    {
        if (! $this->selectedSiswaId || ! $this->selectedTahunId) {
            $this->raport = null;

            return;
        }

        $this->raport = Raport::with(['nilaiRaport.mataPelajaran', 'kelas', 'siswa'])
            ->where('id_siswa', $this->selectedSiswaId)
            ->where('id_tahun_ajaran', $this->selectedTahunId)
            ->first();
    }

    public function render()
    {
        return view('livewire.kepala-sekolah.raport-siswa');
    }
}
