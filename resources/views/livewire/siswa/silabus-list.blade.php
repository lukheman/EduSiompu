<div>
    <x-layout.page-header title="Silabus Pembelajaran"
        subtitle="Silabus yang sudah disetujui kepala sekolah untuk kelas dan semester Anda." />

    <style>
        .silabus-summary {
            cursor: pointer;
            list-style: none;
        }

        .silabus-summary::-webkit-details-marker {
            display: none;
        }

        .silabus-summary .toggle-icon {
            transition: transform 0.2s ease;
        }

        details[open] .silabus-summary .toggle-icon {
            transform: rotate(180deg);
        }
    </style>

    @if (! $activeTa)
        <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
            <i class="fas fa-circle-info mt-1"></i>
            <div>Belum ada tahun ajaran aktif. Silabus yang ditampilkan mencakup semua semester yang tersedia.</div>
        </div>
    @endif

    <x-layout.modern-card class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-6 col-lg-4">
                <label class="form-label fw-bold">Mata Pelajaran</label>
                <select class="form-select" wire:model.live="id_mata_pelajaran">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach ($mapelOptions as $mapel)
                        <option value="{{ $mapel->id_mata_pelajaran }}">{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-lg-8">
                <div class="text-muted small text-md-end">
                    <i class="fas fa-lock me-1"></i>
                    Silabus bersifat informasi dan tidak dapat diubah oleh siswa.
                </div>
            </div>
        </div>
    </x-layout.modern-card>

    <div class="d-flex flex-column gap-3">
        @forelse ($silabusList as $silabus)
            <div class="modern-card" wire:key="siswa-silabus-{{ $silabus->id_silabus }}">
                <details>
                    <summary class="silabus-summary">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px; background: rgba(13, 148, 136, 0.1); color: var(--primary-color);">
                                    <i class="fas fa-book-open fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1" style="color: var(--text-primary);">
                                        {{ $silabus->guruAmpu?->mataPelajaran?->nama_mapel ?? '-' }}
                                    </h5>
                                    <div class="text-muted small">
                                        <i class="fas fa-user-tie me-1"></i>{{ $silabus->guruAmpu?->guru?->nama_guru ?? '-' }}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-list-ol me-1"></i>{{ $silabus->items->count() }} materi
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <x-ui.badge variant="success" icon="fas fa-check-circle">Disetujui</x-ui.badge>
                                <i class="fas fa-chevron-down text-muted toggle-icon"></i>
                            </div>
                        </div>
                    </summary>

                    <div class="table-responsive mt-4 border-top pt-3">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 55px;">No</th>
                                    <th>Materi Pokok</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 100px;" class="text-center">Alokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($silabus->items as $item)
                                    <tr wire:key="siswa-item-{{ $item->id_silabus_item }}">
                                        <td>{{ $item->urutan }}</td>
                                        <td class="fw-semibold">{{ $item->materi_pokok }}</td>
                                        <td class="text-muted small">{{ $item->deskripsi ?: '-' }}</td>
                                        <td class="text-center">{{ $item->alokasi_jam ? $item->alokasi_jam . ' jam' : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada materi pada silabus ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </details>
            </div>
        @empty
            <x-ui.empty-state icon="fas fa-book-open-reader" title="Belum ada silabus"
                description="Belum ada silabus yang disetujui untuk kelas Anda pada semester ini." />
        @endforelse
    </div>
</div>
