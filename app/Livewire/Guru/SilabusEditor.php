<?php

namespace App\Livewire\Guru;

use App\Enums\StatusSilabus;
use App\Models\Guru;
use App\Models\GuruAmpu;
use App\Models\Silabus;
use App\Models\SilabusItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Susun Silabus')]
class SilabusEditor extends Component
{
    public ?int $guruAmpuId = null;

    public ?int $silabusId = null;

    public ?int $itemId = null;

    public string $materi_pokok = '';

    public string $deskripsi = '';

    public $alokasi_jam = '';

    public bool $showItemModal = false;

    public function mount(GuruAmpu $guruAmpu): void
    {
        $guruId = Auth::guard('guru')->id();
        abort_unless($guruAmpu->id_guru === $guruId, 403, 'Anda tidak memiliki akses ke penugasan ini.');

        $silabus = Silabus::firstOrCreate(
            ['id_guru_ampu' => $guruAmpu->id_guru_ampu],
            ['status' => StatusSilabus::DRAFT->value],
        );

        $this->guruAmpuId = $guruAmpu->id_guru_ampu;
        $this->silabusId = $silabus->id_silabus;
    }

    public function tambahItem(): void
    {
        $this->authorizeSilabus('update');
        $this->resetForm();
        $this->showItemModal = true;
    }

    public function editItem(int $id): void
    {
        $silabus = $this->authorizeSilabus('update');
        $item = $silabus->items()->findOrFail($id);

        $this->itemId = $item->id_silabus_item;
        $this->materi_pokok = $item->materi_pokok;
        $this->deskripsi = $item->deskripsi ?? '';
        $this->alokasi_jam = $item->alokasi_jam ?? '';
        $this->showItemModal = true;
        $this->resetValidation();
    }

    public function simpanItem(): void
    {
        $silabus = $this->authorizeSilabus('update');

        $validated = $this->validate([
            'materi_pokok' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'alokasi_jam' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $data = [
            'materi_pokok' => $validated['materi_pokok'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'alokasi_jam' => $validated['alokasi_jam'] ?: null,
        ];

        if ($this->itemId) {
            $item = $silabus->items()->findOrFail($this->itemId);
            $item->update($data);
            session()->flash('success_item', 'Materi silabus berhasil diperbarui.');
        } else {
            $data['urutan'] = ($silabus->items()->max('urutan') ?? 0) + 1;
            $silabus->items()->create($data);
            session()->flash('success_item', 'Materi silabus berhasil ditambahkan.');
        }

        $this->showItemModal = false;
        $this->resetForm();
    }

    public function hapusItem(int $id): void
    {
        $silabus = $this->authorizeSilabus('update');
        $item = $silabus->items()->findOrFail($id);
        $item->delete();

        $this->urutkanUlang($silabus);

        session()->flash('success_item', 'Materi silabus berhasil dihapus.');
    }

    public function ajukan(): void
    {
        $silabus = $this->authorizeSilabus('submit');

        if ($silabus->items()->count() === 0) {
            $this->addError('silabus', 'Tambahkan minimal satu materi sebelum mengajukan silabus.');

            return;
        }

        $silabus->update([
            'status' => StatusSilabus::DIAJUKAN->value,
            'catatan_terakhir' => null,
            'diajukan_at' => now(),
            'direview_at' => null,
            'id_kepala_sekolah' => null,
        ]);

        $silabus->catatRiwayat(StatusSilabus::DIAJUKAN, null, 'guru', $this->guruId());

        session()->flash('success', 'Silabus berhasil diajukan dan menunggu persetujuan kepala sekolah.');
    }

    public function render()
    {
        $silabus = Silabus::with([
            'guruAmpu.mataPelajaran',
            'guruAmpu.kelas',
            'guruAmpu.tahunAjaran',
            'guruAmpu.guru',
            'items',
            'riwayat',
            'reviewer',
        ])->findOrFail($this->silabusId);

        $canEdit = Gate::forUser($this->guru())->allows('update', $silabus);

        return view('livewire.guru.silabus-editor', compact('silabus', 'canEdit'));
    }

    private function authorizeSilabus(string $ability): Silabus
    {
        $silabus = Silabus::findOrFail($this->silabusId);

        abort_unless(
            Gate::forUser($this->guru())->allows($ability, $silabus),
            403,
            'Silabus ini tidak dapat diubah pada status saat ini.',
        );

        return $silabus;
    }

    private function guru(): Guru
    {
        return Auth::guard('guru')->user();
    }

    private function guruId(): int
    {
        return (int) Auth::guard('guru')->id();
    }

    private function urutkanUlang(Silabus $silabus): void
    {
        $silabus->items()->get()->each(function (SilabusItem $item, int $index) {
            $item->update(['urutan' => $index + 1]);
        });
    }

    private function resetForm(): void
    {
        $this->reset(['itemId', 'materi_pokok', 'deskripsi', 'alokasi_jam']);
        $this->resetValidation();
    }
}
