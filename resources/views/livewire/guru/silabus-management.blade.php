<div>
    <x-layout.page-header title="Silabus Pembelajaran"
        subtitle="Susun daftar materi tiap mata pelajaran, lalu ajukan untuk disetujui kepala sekolah." />

    @if (session('success'))
        <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
    @endif

    <div class="row g-3 mb-4">
        @php
            $kartuStatus = [
                'belum' => ['label' => 'Belum Dibuat', 'icon' => 'fas fa-circle-plus', 'color' => '#94a3b8'],
                'draft' => ['label' => 'Draft', 'icon' => 'fas fa-pen', 'color' => '#64748b'],
                'diajukan' => ['label' => 'Diajukan', 'icon' => 'fas fa-paper-plane', 'color' => '#f59e0b'],
                'revisi' => ['label' => 'Revisi', 'icon' => 'fas fa-rotate-left', 'color' => '#ef4444'],
                'disetujui' => ['label' => 'Disetujui', 'icon' => 'fas fa-lock', 'color' => '#10b981'],
            ];
        @endphp

        @foreach ($kartuStatus as $key => $kartu)
            <div class="col-6 col-md-4 col-xl">
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

    <div class="modern-card mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-6 col-lg-5">
                <label class="form-label fw-bold">Tahun Ajaran / Semester</label>
                <select class="form-select" wire:model.live="tahunAjaranId">
                    @forelse($tahunAjaranOptions as $ta)
                        <option value="{{ $ta->id_tahun_ajaran }}">
                            {{ $ta->nama_tahun }} - Semester {{ ucfirst($ta->semester) }}{{ $ta->status_aktif ? ' (Aktif)' : '' }}
                        </option>
                    @empty
                        <option value="">Belum ada penugasan mengajar</option>
                    @endforelse
                </select>
            </div>
            <div class="col-md-6 col-lg-7">
                <div class="text-muted small text-md-end">
                    <i class="fas fa-circle-info me-1"></i>
                    Silabus disusun per mata pelajaran dan kelas. Satu silabus untuk satu semester.
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse ($ampuList as $ampu)
            @php
                $silabus = $ampu->silabus;
                $status = $silabus?->status;
            @endphp
            <div class="col-md-6 col-xl-4" wire:key="silabus-ampu-{{ $ampu->id_guru_ampu }}">
                <x-layout.modern-card class="h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="min-w-0">
                            <h5 class="fw-bold mb-1 text-truncate" title="{{ $ampu->mataPelajaran?->nama_mapel }}" style="color: var(--text-primary);">
                                {{ $ampu->mataPelajaran?->nama_mapel ?? '-' }}
                            </h5>
                            <div class="text-muted small">
                                <i class="fas fa-door-open me-1"></i>{{ $ampu->kelas?->nama_kelas ?? '-' }}
                                <span class="mx-1">•</span>
                                {{ $ampu->tahunAjaran?->nama_tahun ?? '-' }}
                                ({{ ucfirst($ampu->tahunAjaran?->semester ?? '-') }})
                            </div>
                        </div>
                        <x-ui.badge variant="{{ $status?->getColor() ?? 'secondary' }}"
                            icon="{{ $status?->getIcon() ?? 'fas fa-circle-plus' }}">
                            {{ $status?->getLabel() ?? 'Belum dibuat' }}
                        </x-ui.badge>
                    </div>

                    <div class="small text-muted mb-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-list-ol"></i>
                            <span>{{ $silabus?->items->count() ?? 0 }} materi</span>
                        </div>
                        @if($silabus?->diajukan_at)
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-paper-plane"></i>
                                <span>Diajukan {{ $silabus->diajukan_at->translatedFormat('d M Y') }}</span>
                            </div>
                        @endif
                        @if($silabus?->catatan_terakhir)
                            <div class="mt-2 p-2 rounded" style="background: rgba(239, 68, 68, 0.07); color: #b91c1c;">
                                <i class="fas fa-comment-dots me-1"></i>{{ Str::limit($silabus->catatan_terakhir, 100) }}
                            </div>
                        @endif
                    </div>

                    <x-ui.button href="{{ route('guru.silabus.editor', $ampu->id_guru_ampu) }}"
                        variant="{{ $silabus ? 'primary' : 'outline' }}"
                        icon="{{ $silabus ? 'fas fa-folder-open' : 'fas fa-plus' }}"
                        class="mt-auto w-100">
                        {{ $silabus ? 'Buka Silabus' : 'Susun Silabus' }}
                    </x-ui.button>
                </x-layout.modern-card>
            </div>
        @empty
            <div class="col-12">
                <x-ui.empty-state icon="fas fa-book-open-reader" title="Belum ada penugasan mengajar"
                    description="Silabus dapat disusun setelah admin menetapkan penugasan mengajar untuk Anda pada tahun ajaran ini." />
            </div>
        @endforelse
    </div>
</div>
