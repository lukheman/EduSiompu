<div>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <a href="{{ route('guru.silabus') }}" class="text-decoration-none small fw-semibold" style="color: var(--primary-color);">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke daftar silabus
            </a>
            <h1 class="mt-2 mb-1" style="color: var(--text-primary);">
                {{ $silabus->guruAmpu?->mataPelajaran?->nama_mapel ?? 'Silabus' }}
            </h1>
            <p class="text-muted mb-0">
                {{ $silabus->guruAmpu?->kelas?->nama_kelas ?? '-' }}
                <span class="mx-1">•</span>
                {{ $silabus->guruAmpu?->tahunAjaran?->nama_tahun ?? '-' }}
                (Semester {{ ucfirst($silabus->guruAmpu?->tahunAjaran?->semester ?? '-') }})
            </p>
        </div>
        <x-ui.badge variant="{{ $silabus->status->getColor() }}" icon="{{ $silabus->status->getIcon() }}" class="fs-6">
            {{ $silabus->status->getLabel() }}
        </x-ui.badge>
    </div>

    @if (session('success'))
        <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
    @endif

    @if (session('success_item'))
        <x-ui.toast variant="success">{{ session('success_item') }}</x-ui.toast>
    @endif

    @if ($silabus->catatan_terakhir)
        <div class="alert alert-{{ $silabus->status->value === 'revisi' ? 'danger' : 'info' }} d-flex align-items-start gap-2 mb-4">
            <i class="fas fa-comment-dots mt-1"></i>
            <div>
                <strong>Catatan dari kepala sekolah:</strong>
                <div>{{ $silabus->catatan_terakhir }}</div>
            </div>
        </div>
    @endif

    @if (! $canEdit)
        <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
            <i class="fas fa-lock mt-1"></i>
            <div>
                @if ($silabus->status->value === 'diajukan')
                    <strong>Silabus sedang menunggu persetujuan.</strong>
                    <div>Silabus terkunci sementara sampai kepala sekolah menyetujui atau meminta revisi.</div>
                @else
                    <strong>Silabus sudah disetujui dan terkunci.</strong>
                    <div>Hubungi kepala sekolah bila perlu membuka kunci untuk melakukan perubahan.</div>
                @endif
            </div>
        </div>
    @endif

    @error('silabus')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="row g-4">
        <div class="col-lg-8">
            <x-layout.modern-card class="h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Daftar Materi</h5>
                        <div class="text-muted small">{{ $silabus->items->count() }} materi tersusun</div>
                    </div>
                    @if ($canEdit)
                        <x-ui.button wire:click="tambahItem" variant="primary" size="sm" icon="fas fa-plus">
                            Tambah Materi
                        </x-ui.button>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th style="width: 55px;">No</th>
                                <th>Materi Pokok</th>
                                <th>Deskripsi</th>
                                <th style="width: 100px;" class="text-center">Alokasi</th>
                                @if ($canEdit)
                                    <th style="width: 110px;" class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($silabus->items as $item)
                                <tr wire:key="item-{{ $item->id_silabus_item }}">
                                    <td>{{ $item->urutan }}</td>
                                    <td class="fw-semibold" style="color: var(--text-primary);">{{ $item->materi_pokok }}</td>
                                    <td class="text-muted small">{{ $item->deskripsi ?: '-' }}</td>
                                    <td class="text-center">{{ $item->alokasi_jam ? $item->alokasi_jam . ' jam' : '-' }}</td>
                                    @if ($canEdit)
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <x-ui.button variant="outline" size="sm" wire:click="editItem({{ $item->id_silabus_item }})" title="Edit">
                                                    <i class="fas fa-edit text-warning"></i>
                                                </x-ui.button>
                                                <x-ui.button variant="outline" size="sm"
                                                    wire:confirm="Hapus materi ini dari silabus?"
                                                    wire:click="hapusItem({{ $item->id_silabus_item }})" title="Hapus">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </x-ui.button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $canEdit ? 5 : 4 }}" class="text-center py-5 text-muted">
                                        <i class="fas fa-list-check d-block fs-2 mb-2" style="opacity: 0.4;"></i>
                                        Belum ada materi silabus.
                                        @if ($canEdit)
                                            <div class="small mt-1">Tambahkan materi pokok pertama untuk memulai.</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($canEdit)
                    <div class="d-flex justify-content-end border-top pt-3 mt-3">
                        <x-ui.button wire:click="ajukan" variant="success" icon="fas fa-paper-plane"
                            wire:confirm="Ajukan silabus ini untuk disetujui? Setelah diajukan, silabus tidak dapat diubah sampai kepala sekolah memberi keputusan.">
                            Ajukan Silabus
                        </x-ui.button>
                    </div>
                @endif
            </x-layout.modern-card>
        </div>

        <div class="col-lg-4">
            <x-layout.modern-card class="mb-4">
                <h5 class="fw-bold mb-3" style="color: var(--text-primary);">Informasi</h5>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Guru</span>
                    <span class="fw-semibold small text-end">{{ $silabus->guruAmpu?->guru?->nama_guru ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Mata Pelajaran</span>
                    <span class="fw-semibold small text-end">{{ $silabus->guruAmpu?->mataPelajaran?->nama_mapel ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Kelas</span>
                    <span class="fw-semibold small text-end">{{ $silabus->guruAmpu?->kelas?->nama_kelas ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Semester</span>
                    <span class="fw-semibold small text-end">
                        {{ $silabus->guruAmpu?->tahunAjaran?->nama_tahun ?? '-' }}
                        / {{ ucfirst($silabus->guruAmpu?->tahunAjaran?->semester ?? '-') }}
                    </span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Diajukan</span>
                    <span class="fw-semibold small text-end">
                        {{ $silabus->diajukan_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted small">Direview</span>
                    <span class="fw-semibold small text-end">
                        {{ $silabus->direview_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                    </span>
                </div>
                @if ($silabus->reviewer)
                    <div class="d-flex justify-content-between py-2 border-top">
                        <span class="text-muted small">Reviewer</span>
                        <span class="fw-semibold small text-end">{{ $silabus->reviewer->nama_kepala_sekolah }}</span>
                    </div>
                @endif
            </x-layout.modern-card>

            <x-layout.modern-card>
                <h5 class="fw-bold mb-3" style="color: var(--text-primary);">Riwayat</h5>
                <div class="d-flex flex-column gap-3">
                    @forelse ($silabus->riwayat as $riwayat)
                        <div class="d-flex gap-2" wire:key="riwayat-{{ $riwayat->id_silabus_riwayat }}">
                            <div class="pt-1">
                                <x-ui.badge variant="{{ $riwayat->status->getColor() }}"
                                    icon="{{ $riwayat->status->getIcon() }}" />
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small" style="color: var(--text-primary);">
                                    {{ $riwayat->status->getLabel() }}
                                    <span class="text-muted fw-normal">
                                        oleh {{ $riwayat->oleh_role === 'kepala_sekolah' ? 'Kepala Sekolah' : 'Guru' }}
                                    </span>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    {{ $riwayat->created_at?->translatedFormat('d M Y, H:i') }}
                                </div>
                                @if ($riwayat->catatan)
                                    <div class="small mt-1 p-2 rounded" style="background: var(--bg-tertiary);">
                                        {{ $riwayat->catatan }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada riwayat pengajuan.</p>
                    @endforelse
                </div>
            </x-layout.modern-card>
        </div>
    </div>

    @if ($showItemModal)
        <div class="modal-backdrop-custom" wire:click.self="$set('showItemModal', false)">
            <x-layout.modern-card class="modal-content-custom m-auto"
                style="max-width: 620px; max-height: 90vh; overflow-y: auto; margin-top: 5rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h5 class="mb-0 fw-bold">{{ $itemId ? 'Edit Materi Silabus' : 'Tambah Materi Silabus' }}</h5>
                    <button type="button" wire:click="$set('showItemModal', false)" class="btn-close"></button>
                </div>

                <form wire:submit="simpanItem">
                    <x-form.input label="Materi Pokok" wire:model="materi_pokok" required="true"
                        placeholder="Misal: Himpunan dan operasinya" :error="$errors->first('materi_pokok')" />
                    <x-form.textarea label="Deskripsi" wire:model="deskripsi" rows="3"
                        placeholder="Ringkasan materi atau tujuan pembelajaran..." :error="$errors->first('deskripsi')" />
                    <x-form.input type="number" label="Alokasi Jam" wire:model="alokasi_jam" min="1" max="100"
                        placeholder="Opsional" hint="Perkiraan jumlah jam pertemuan untuk materi ini."
                        :error="$errors->first('alokasi_jam')" />

                    <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-3">
                        <x-ui.button type="button" variant="outline" wire:click="$set('showItemModal', false)">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">
                            {{ $itemId ? 'Perbarui Materi' : 'Tambah Materi' }}
                        </x-ui.button>
                    </div>
                </form>
            </x-layout.modern-card>
        </div>
    @endif
</div>
