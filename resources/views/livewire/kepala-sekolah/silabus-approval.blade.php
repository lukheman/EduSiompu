<div>
    <x-layout.page-header title="Persetujuan Silabus"
        subtitle="Tinjau pengajuan silabus guru, beri persetujuan, atau minta revisi dengan catatan." />

    @if (session('success'))
        <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
    @endif

    <div class="row g-3 mb-4">
        @php
            $kartuStatus = [
                'draft' => ['label' => 'Draft', 'icon' => 'fas fa-pen', 'color' => '#64748b'],
                'diajukan' => ['label' => 'Menunggu Review', 'icon' => 'fas fa-paper-plane', 'color' => '#f59e0b'],
                'revisi' => ['label' => 'Revisi', 'icon' => 'fas fa-rotate-left', 'color' => '#ef4444'],
                'disetujui' => ['label' => 'Disetujui', 'icon' => 'fas fa-lock', 'color' => '#10b981'],
            ];
        @endphp

        @foreach ($kartuStatus as $key => $kartu)
            <div class="col-6 col-md-3">
                <div class="modern-card h-100 p-3" style="border-left: 4px solid {{ $kartu['color'] }};">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small fw-semibold">{{ $kartu['label'] }}</div>
                            <div class="fs-3 fw-bold" style="color: {{ $kartu['color'] }};">{{ $ringkasan[$key] ?? 0 }}</div>
                        </div>
                        <i class="{{ $kartu['icon'] }} fs-4" style="color: {{ $kartu['color'] }}; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <x-layout.modern-card class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5 col-lg-4">
                <label class="form-label fw-bold">Filter Status</label>
                <select class="form-select" wire:model.live="statusFilter">
                    <option value="semua">Semua Status</option>
                    <option value="diajukan">Menunggu Persetujuan</option>
                    <option value="revisi">Revisi</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
        </div>
    </x-layout.modern-card>

    <x-layout.modern-card padding="false">
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th>Mata Pelajaran / Kelas</th>
                        <th>Guru</th>
                        <th>Tahun / Semester</th>
                        <th class="text-center">Materi</th>
                        <th class="text-center">Status</th>
                        <th>Diperbarui</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($silabusList as $silabus)
                        <tr wire:key="approval-{{ $silabus->id_silabus }}">
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">
                                    {{ $silabus->guruAmpu?->mataPelajaran?->nama_mapel ?? '-' }}
                                </div>
                                <div class="text-muted small">{{ $silabus->guruAmpu?->kelas?->nama_kelas ?? '-' }}</div>
                            </td>
                            <td class="small">{{ $silabus->guruAmpu?->guru?->nama_guru ?? '-' }}</td>
                            <td class="small">
                                {{ $silabus->guruAmpu?->tahunAjaran?->nama_tahun ?? '-' }}
                                <div class="text-muted">{{ ucfirst($silabus->guruAmpu?->tahunAjaran?->semester ?? '-') }}</div>
                            </td>
                            <td class="text-center">
                                <x-ui.badge variant="info">{{ $silabus->items_count }}</x-ui.badge>
                            </td>
                            <td class="text-center">
                                <x-ui.badge variant="{{ $silabus->status->getColor() }}" icon="{{ $silabus->status->getIcon() }}">
                                    {{ $silabus->status->getLabel() }}
                                </x-ui.badge>
                            </td>
                            <td class="small text-muted">{{ $silabus->updated_at?->diffForHumans() }}</td>
                            <td class="text-center">
                                <x-ui.button variant="outline" size="sm" wire:click="bukaDetail({{ $silabus->id_silabus }})" title="Tinjau">
                                    <i class="fas fa-eye text-primary"></i>
                                </x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox d-block fs-2 mb-2" style="opacity: 0.4;"></i>
                                Tidak ada silabus dengan status ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-layout.modern-card>

    @if ($showDetail && $selectedSilabus)
        <div class="modal-backdrop-custom" wire:click.self="$set('showDetail', false)">
            <x-layout.modern-card class="modal-content-custom m-auto"
                style="max-width: 900px; max-height: 92vh; overflow-y: auto; margin-top: 3rem !important;">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="mb-1 fw-bold">
                            {{ $selectedSilabus->guruAmpu?->mataPelajaran?->nama_mapel ?? 'Silabus' }}
                        </h5>
                        <div class="small text-muted">
                            {{ $selectedSilabus->guruAmpu?->kelas?->nama_kelas ?? '-' }}
                            <span class="mx-1">•</span>
                            {{ $selectedSilabus->guruAmpu?->tahunAjaran?->nama_tahun ?? '-' }}
                            ({{ ucfirst($selectedSilabus->guruAmpu?->tahunAjaran?->semester ?? '-') }})
                            <span class="mx-1">•</span>
                            {{ $selectedSilabus->guruAmpu?->guru?->nama_guru ?? '-' }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <x-ui.badge variant="{{ $selectedSilabus->status->getColor() }}" icon="{{ $selectedSilabus->status->getIcon() }}">
                            {{ $selectedSilabus->status->getLabel() }}
                        </x-ui.badge>
                        <button type="button" wire:click="$set('showDetail', false)" class="btn-close"></button>
                    </div>
                </div>

                @if ($selectedSilabus->catatan_terakhir)
                    <div class="alert alert-info py-2 mb-3">
                        <strong>Catatan terakhir:</strong> {{ $selectedSilabus->catatan_terakhir }}
                    </div>
                @endif

                <h6 class="fw-bold mb-2">Daftar Materi</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th style="width: 55px;">No</th>
                                <th>Materi Pokok</th>
                                <th>Deskripsi</th>
                                <th style="width: 90px;" class="text-center">Alokasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($selectedSilabus->items as $item)
                                <tr wire:key="review-item-{{ $item->id_silabus_item }}">
                                    <td>{{ $item->urutan }}</td>
                                    <td class="fw-semibold">{{ $item->materi_pokok }}</td>
                                    <td class="text-muted small">{{ $item->deskripsi ?: '-' }}</td>
                                    <td class="text-center">{{ $item->alokasi_jam ? $item->alokasi_jam . ' jam' : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Silabus ini belum memiliki materi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold mb-2">Riwayat</h6>
                <div class="d-flex flex-column gap-2 mb-4">
                    @forelse ($selectedSilabus->riwayat as $riwayat)
                        <div class="d-flex justify-content-between align-items-start gap-2 p-2 rounded"
                            style="background: var(--bg-tertiary);" wire:key="review-riwayat-{{ $riwayat->id_silabus_riwayat }}">
                            <div>
                                <div class="small fw-semibold">
                                    {{ $riwayat->status->getLabel() }}
                                    <span class="text-muted fw-normal">
                                        oleh {{ $riwayat->oleh_role === 'kepala_sekolah' ? 'Kepala Sekolah' : 'Guru' }}
                                    </span>
                                </div>
                                @if ($riwayat->catatan)
                                    <div class="small text-muted mt-1">{{ $riwayat->catatan }}</div>
                                @endif
                            </div>
                            <div class="small text-muted text-end" style="white-space: nowrap;">
                                {{ $riwayat->created_at?->translatedFormat('d M Y, H:i') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada riwayat.</p>
                    @endforelse
                </div>

                @if ($selectedSilabus->status === \App\Enums\StatusSilabus::DIAJUKAN)
                    <div class="border-top pt-3">
                        <x-form.textarea label="Catatan untuk Guru" wire:model="catatan" rows="3"
                            placeholder="Wajib diisi bila meminta revisi. Opsional saat menyetujui."
                            :error="$errors->first('catatan')" />
                        <div class="d-flex flex-wrap justify-content-end gap-2">
                            <x-ui.button variant="danger" icon="fas fa-rotate-left" wire:click="mintaRevisi">
                                Minta Revisi
                            </x-ui.button>
                            <x-ui.button variant="success" icon="fas fa-check" wire:click="setujui">
                                Setujui Silabus
                            </x-ui.button>
                        </div>
                    </div>
                @elseif ($selectedSilabus->status === \App\Enums\StatusSilabus::DISETUJUI)
                    <div class="border-top pt-3">
                        <x-form.textarea label="Alasan Membuka Kunci" wire:model="catatan" rows="3" required="true"
                            placeholder="Jelaskan alasan silabus perlu diperbarui oleh guru."
                            :error="$errors->first('catatan')" />
                        <div class="d-flex justify-content-end">
                            <x-ui.button variant="warning" icon="fas fa-lock-open" wire:click="bukaKunci">
                                Buka Kunci Silabus
                            </x-ui.button>
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary mb-0">
                        @if ($selectedSilabus->status === \App\Enums\StatusSilabus::REVISI)
                            Silabus sedang diperbaiki oleh guru dan akan diajukan ulang.
                        @else
                            Silabus masih berupa draft dan belum diajukan oleh guru.
                        @endif
                    </div>
                @endif
            </x-layout.modern-card>
        </div>
    @endif
</div>
