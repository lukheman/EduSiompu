<div>
    <x-layout.page-header title="Materi Belajar" subtitle="Akses semua bahan ajar dan presentasi dari guru Anda" />

    {{-- Filters --}}
    <div class="modern-card mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-muted">Tahun Ajaran</label>
                <select class="form-select form-control" wire:model.live="id_tahun_ajaran">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjaranOptions as $ta)
                        <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->nama_tahun }} - {{ ucfirst($ta->semester) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted">Mata Pelajaran</label>
                <select class="form-select form-control" wire:model.live="id_mata_pelajaran">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach($mapelOptions as $mapel)
                        @if($mapel)
                            <option value="{{ $mapel->id_mata_pelajaran }}">{{ $mapel->nama_mapel }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted">Cari Materi</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                        <i class="fas fa-search" style="color: var(--text-muted);"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Ketik judul..." wire:model.live.debounce.300ms="search" style="border-left: none;">
                </div>
            </div>
        </div>
    </div>

    {{-- Materi Grid --}}
    <div class="row g-4">
        @forelse($materiList as $materi)
            @php
                $ext = strtolower($materi->jenis_file);
                $icon = 'fas fa-file';
                $color = 'var(--text-muted)';
                $bgColor = 'var(--bg-tertiary)';

                if (in_array($ext, ['pdf'])) {
                    $icon = 'fas fa-file-pdf';
                    $color = '#ef4444';
                    $bgColor = 'rgba(239, 68, 68, 0.1)';
                } elseif (in_array($ext, ['doc', 'docx'])) {
                    $icon = 'fas fa-file-word';
                    $color = '#3b82f6';
                    $bgColor = 'rgba(59, 130, 246, 0.1)';
                } elseif (in_array($ext, ['xls', 'xlsx'])) {
                    $icon = 'fas fa-file-excel';
                    $color = '#22c55e';
                    $bgColor = 'rgba(34, 197, 94, 0.1)';
                } elseif (in_array($ext, ['ppt', 'pptx'])) {
                    $icon = 'fas fa-file-powerpoint';
                    $color = '#f97316';
                    $bgColor = 'rgba(249, 115, 22, 0.1)';
                } elseif (in_array($ext, ['zip', 'rar'])) {
                    $icon = 'fas fa-file-archive';
                    $color = '#52525b';
                    $bgColor = 'rgba(82, 82, 91, 0.1)';
                } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $icon = 'fas fa-file-image';
                    $color = '#06b6d4';
                    $bgColor = 'rgba(6, 182, 212, 0.1)';
                }
            @endphp
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="modern-card h-100 d-flex flex-column p-0 overflow-hidden" style="transition: all 0.3s ease;">
                    <div class="p-4 flex-grow-1">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px; background-color: {{ $bgColor }}; color: {{ $color }};">
                                <i class="{{ $icon }} fa-2x"></i>
                            </div>
                            <x-ui.badge variant="secondary" class="text-uppercase" style="font-size: 0.7rem;">.{{ $ext }}</x-ui.badge>
                        </div>
                        <h5 class="fw-bold text-truncate mb-1" title="{{ $materi->judul_materi }}" style="color: var(--text-primary);">
                            {{ $materi->judul_materi }}
                        </h5>
                        <div class="text-primary fw-semibold mb-2" style="font-size: 0.85rem;">
                            {{ $materi->guruAmpu->mataPelajaran->nama_mapel ?? 'N/A' }}
                        </div>
                        <div class="text-muted d-flex align-items-center mb-1" style="font-size: 0.8rem;">
                            <i class="fas fa-chalkboard-teacher me-2" style="width: 14px;"></i>
                            {{ $materi->guruAmpu->guru->nama_guru ?? 'N/A' }}
                        </div>
                        <div class="text-muted d-flex align-items-center" style="font-size: 0.8rem;">
                            <i class="fas fa-clock me-2" style="width: 14px;"></i>
                            {{ $materi->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="bg-light p-3 border-top" style="background-color: var(--bg-tertiary) !important;">
                        <x-ui.button variant="danger" href="{{ \Illuminate\Support\Facades\Storage::url($materi->file_path) }}" target="_blank" class="w-100">
                            <i class="fas fa-external-link-alt me-2"></i> Download Materi
                        </x-ui.button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="modern-card text-center py-5 text-muted">
                    <i class="fas fa-folder-open mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                    <h4>Belum Ada Materi</h4>
                    <p class="mb-0">Tidak ada materi belajar yang ditemukan untuk filter ini.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($materiList->hasPages())
        <div class="mt-4">
            {{ $materiList->links() }}
        </div>
    @endif
</div>
