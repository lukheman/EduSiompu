<?php

namespace App\Livewire\Guru;

use App\Models\GuruAmpu;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Silabus Pembelajaran')]
class SilabusManagement extends Component
{
    public ?int $guruId = null;

    public ?int $tahunAjaranId = null;

    public function mount(): void
    {
        $this->guruId = Auth::guard('guru')->id();

        $activeTa = TahunAjaran::where('status_aktif', true)
            ->whereHas('guruAmpu', fn ($q) => $q->where('id_guru', $this->guruId))
            ->first();

        if ($activeTa) {
            $this->tahunAjaranId = $activeTa->id_tahun_ajaran;
        } else {
            $tahunAjaranId = TahunAjaran::whereHas('guruAmpu', fn ($q) => $q->where('id_guru', $this->guruId))
                ->orderByDesc('nama_tahun')
                ->value('id_tahun_ajaran');

            $this->tahunAjaranId = $tahunAjaranId ? (int) $tahunAjaranId : null;
        }
    }

    public function render()
    {
        $ampuList = GuruAmpu::with(['mataPelajaran', 'kelas', 'tahunAjaran', 'silabus.items'])
            ->where('id_guru', $this->guruId)
            ->when($this->tahunAjaranId, fn ($q) => $q->where('id_tahun_ajaran', $this->tahunAjaranId))
            ->get()
            ->sortBy(fn ($ampu) => $ampu->mataPelajaran?->nama_mapel)
            ->values();

        $ringkasan = [
            'draft' => 0,
            'diajukan' => 0,
            'revisi' => 0,
            'disetujui' => 0,
            'belum' => 0,
        ];

        foreach ($ampuList as $ampu) {
            $status = $ampu->silabus?->status->value ?? 'belum';
            $ringkasan[$status] = ($ringkasan[$status] ?? 0) + 1;
        }

        $tahunAjaranOptions = TahunAjaran::whereHas('guruAmpu', fn ($q) => $q->where('id_guru', $this->guruId))
            ->orderByDesc('nama_tahun')
            ->orderBy('semester')
            ->get();

        return view('livewire.guru.silabus-management', [
            'ampuList' => $ampuList,
            'ringkasan' => $ringkasan,
            'tahunAjaranOptions' => $tahunAjaranOptions,
        ]);
    }
}
