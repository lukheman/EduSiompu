<?php

namespace App\Livewire\KepalaSekolah;

use App\Enums\StatusSilabus;
use App\Models\KepalaSekolah;
use App\Models\Silabus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Persetujuan Silabus')]
class SilabusApproval extends Component
{
    public string $statusFilter = 'diajukan';

    public ?int $selectedSilabusId = null;

    public bool $showDetail = false;

    public string $catatan = '';

    public function bukaDetail(int $id): void
    {
        $silabus = Silabus::findOrFail($id);

        $this->selectedSilabusId = $silabus->id_silabus;
        $this->catatan = '';
        $this->resetValidation();
        $this->showDetail = true;
    }

    public function setujui(): void
    {
        $silabus = $this->authorizeSilabus('review');
        $validated = $this->validate([
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        $catatan = trim((string) ($validated['catatan'] ?? '')) ?: null;
        $kepala = $this->kepalaSekolah();

        $silabus->update([
            'status' => StatusSilabus::DISETUJUI->value,
            'catatan_terakhir' => $catatan,
            'direview_at' => now(),
            'id_kepala_sekolah' => $kepala->id_kepala_sekolah,
        ]);

        $silabus->catatRiwayat(StatusSilabus::DISETUJUI, $catatan, 'kepala_sekolah', $kepala->id_kepala_sekolah);

        $this->showDetail = false;
        $this->catatan = '';
        session()->flash('success', 'Silabus disetujui dan dikunci.');
    }

    public function mintaRevisi(): void
    {
        $silabus = $this->authorizeSilabus('review');
        $validated = $this->validate([
            'catatan' => ['required', 'string', 'max:2000'],
        ], [
            'catatan.required' => 'Tuliskan catatan revisi untuk guru.',
        ]);

        $catatan = trim($validated['catatan']);
        $kepala = $this->kepalaSekolah();

        $silabus->update([
            'status' => StatusSilabus::REVISI->value,
            'catatan_terakhir' => $catatan,
            'direview_at' => now(),
            'id_kepala_sekolah' => $kepala->id_kepala_sekolah,
        ]);

        $silabus->catatRiwayat(StatusSilabus::REVISI, $catatan, 'kepala_sekolah', $kepala->id_kepala_sekolah);

        $this->showDetail = false;
        $this->catatan = '';
        session()->flash('success', 'Silabus dikembalikan ke guru untuk direvisi.');
    }

    public function bukaKunci(): void
    {
        $silabus = $this->authorizeSilabus('unlock');
        $validated = $this->validate([
            'catatan' => ['required', 'string', 'max:2000'],
        ], [
            'catatan.required' => 'Tuliskan alasan membuka kunci silabus.',
        ]);

        $catatan = trim($validated['catatan']);
        $kepala = $this->kepalaSekolah();

        $silabus->update([
            'status' => StatusSilabus::REVISI->value,
            'catatan_terakhir' => $catatan,
            'direview_at' => now(),
            'id_kepala_sekolah' => $kepala->id_kepala_sekolah,
        ]);

        $silabus->catatRiwayat(StatusSilabus::REVISI, $catatan, 'kepala_sekolah', $kepala->id_kepala_sekolah);

        $this->showDetail = false;
        $this->catatan = '';
        session()->flash('success', 'Kunci silabus dibuka. Guru dapat memperbarui dan mengajukan ulang.');
    }

    public function render()
    {
        $query = Silabus::with([
            'guruAmpu.mataPelajaran',
            'guruAmpu.kelas',
            'guruAmpu.guru',
            'guruAmpu.tahunAjaran',
            'reviewer',
        ])->withCount('items');

        if ($this->statusFilter !== 'semua') {
            $query->where('status', $this->statusFilter);
        }

        $silabusList = $query->latest('updated_at')->get();

        $jumlah = Silabus::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $ringkasan = [
            'draft' => (int) ($jumlah['draft'] ?? 0),
            'diajukan' => (int) ($jumlah['diajukan'] ?? 0),
            'revisi' => (int) ($jumlah['revisi'] ?? 0),
            'disetujui' => (int) ($jumlah['disetujui'] ?? 0),
        ];

        $selectedSilabus = null;
        if ($this->selectedSilabusId) {
            $selectedSilabus = Silabus::with([
                'guruAmpu.mataPelajaran',
                'guruAmpu.kelas',
                'guruAmpu.guru',
                'guruAmpu.tahunAjaran',
                'items',
                'riwayat',
                'reviewer',
            ])->find($this->selectedSilabusId);
        }

        return view('livewire.kepala-sekolah.silabus-approval', [
            'silabusList' => $silabusList,
            'ringkasan' => $ringkasan,
            'selectedSilabus' => $selectedSilabus,
        ]);
    }

    private function authorizeSilabus(string $ability): Silabus
    {
        $silabus = Silabus::findOrFail($this->selectedSilabusId);

        abort_unless(
            Gate::forUser($this->kepalaSekolah())->allows($ability, $silabus),
            403,
            'Silabus ini tidak dapat diproses pada status saat ini.',
        );

        return $silabus;
    }

    private function kepalaSekolah(): KepalaSekolah
    {
        return Auth::guard('kepala_sekolah')->user();
    }
}
