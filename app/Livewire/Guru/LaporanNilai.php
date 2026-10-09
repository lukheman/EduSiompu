<?php

namespace App\Livewire\Guru;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
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
        $id_guru = Auth::guard('guru')->id();
        $this->kelasOptions = Kelas::where('id_guru', $id_guru)
            ->orderBy('nama_kelas')
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

        if ($this->selectedKelasId && $this->kelasDiawalikan()) {
            $kelas = Kelas::find($this->selectedKelasId);
            $siswas = Siswa::where('id_kelas', $this->selectedKelasId)
                ->with(['raport' => function ($q) {
                    $q->where('id_tahun_ajaran', $this->selectedTahunId);
                }])
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('livewire.guru.laporan-nilai', [
            'siswas' => $siswas,
            'kelas' => $kelas,
        ]);
    }

    private function kelasDiawalikan(): bool
    {
        return Kelas::where('id_kelas', $this->selectedKelasId)
            ->where('id_guru', Auth::guard('guru')->id())
            ->exists();
    }
}
