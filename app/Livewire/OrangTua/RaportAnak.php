<?php

namespace App\Livewire\OrangTua;

use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Raport Anak')]
class RaportAnak extends Component
{
    public $anakList = [];
    public $selectedAnakId = '';
    public $tahunAjarans = [];
    public $selectedTahunId = '';
    public $raport = null;

    public function mount()
    {
        $id_orang_tua = Auth::guard('orang_tua')->id();
        $this->anakList = Siswa::where('id_orang_tua', $id_orang_tua)->get();
        
        if (count($this->anakList) > 0) {
            $this->selectedAnakId = $this->anakList->first()->id_siswa;
            $this->loadTahunAjaran();
        }
    }

    public function updatedSelectedAnakId()
    {
        $this->loadTahunAjaran();
    }

    public function updatedSelectedTahunId()
    {
        $this->loadRaport();
    }

    public function loadTahunAjaran()
    {
        if (!$this->selectedAnakId) return;

        $raportTahunIds = Raport::where('id_siswa', $this->selectedAnakId)->pluck('id_tahun_ajaran');
        $this->tahunAjarans = TahunAjaran::whereIn('id_tahun_ajaran', $raportTahunIds)->get();

        $aktif = TahunAjaran::where('status_aktif', true)->first();
        if ($aktif && $this->tahunAjarans->contains('id_tahun_ajaran', $aktif->id_tahun_ajaran)) {
            $this->selectedTahunId = $aktif->id_tahun_ajaran;
        } elseif (count($this->tahunAjarans) > 0) {
            $this->selectedTahunId = $this->tahunAjarans->first()->id_tahun_ajaran;
        } else {
            $this->selectedTahunId = '';
        }

        $this->loadRaport();
    }

    public function loadRaport()
    {
        if (!$this->selectedTahunId || !$this->selectedAnakId) {
            $this->raport = null;
            return;
        }

        $this->raport = Raport::with(['nilaiRaport.mataPelajaran', 'kelas', 'siswa'])
            ->where('id_siswa', $this->selectedAnakId)
            ->where('id_tahun_ajaran', $this->selectedTahunId)
            ->first();
    }

    public function render()
    {
        return view('livewire.orang-tua.raport-anak');
    }
}
