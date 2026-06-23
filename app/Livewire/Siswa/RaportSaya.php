<?php

namespace App\Livewire\Siswa;

use App\Models\Raport;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Raport Saya')]
class RaportSaya extends Component
{
    public $tahunAjarans = [];
    public $selectedTahunId = '';
    public $raport = null;

    public function mount()
    {
        $id_siswa = Auth::guard('siswa')->id();
        $raportTahunIds = Raport::where('id_siswa', $id_siswa)->pluck('id_tahun_ajaran');
        $this->tahunAjarans = TahunAjaran::whereIn('id_tahun_ajaran', $raportTahunIds)->get();

        $aktif = TahunAjaran::where('status_aktif', true)->first();
        if ($aktif && $this->tahunAjarans->contains('id_tahun_ajaran', $aktif->id_tahun_ajaran)) {
            $this->selectedTahunId = $aktif->id_tahun_ajaran;
        } elseif (count($this->tahunAjarans) > 0) {
            $this->selectedTahunId = $this->tahunAjarans->first()->id_tahun_ajaran;
        }

        $this->loadRaport();
    }

    public function updatedSelectedTahunId()
    {
        $this->loadRaport();
    }

    public function loadRaport()
    {
        if (!$this->selectedTahunId) {
            $this->raport = null;
            return;
        }

        $id_siswa = Auth::guard('siswa')->id();
        $this->raport = Raport::with(['nilaiRaport.mataPelajaran', 'kelas'])
            ->where('id_siswa', $id_siswa)
            ->where('id_tahun_ajaran', $this->selectedTahunId)
            ->first();
    }

    public function render()
    {
        return view('livewire.siswa.raport-saya');
    }
}
