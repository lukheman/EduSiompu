<div>
    {{-- Page Header --}}
    @php
        $userName = Auth::guard('guru')->user()->nama_guru ?? 'Guru';
        $modeLabel = $mode === 'wali_kelas' ? 'Wali Kelas' : 'Guru Mata Pelajaran';
        $greeting = 'Selamat Datang, Bapak/Ibu ' . $userName;
        $subtitle = $activeTa ? 'Tahun Ajaran Aktif: ' . $activeTa->nama_tahun . ' (Semester ' . ucfirst($activeTa->semester) . ')' : 'Belum ada Tahun Ajaran Aktif';
    @endphp

    <x-layout.page-header :title="$greeting" :subtitle="$subtitle . ' • Mode: ' . $modeLabel" />

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--primary-color);">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="text-muted fw-medium mb-1">{{ $mode === 'wali_kelas' ? 'Kelas Wali' : 'Kelas Diampu' }}</div>
                <h3 class="fw-bold text-primary mb-0">{{ $stats['total_kelas'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--secondary-color);">
                <div class="stat-icon bg-secondary-subtle text-secondary">
                    <i class="fas fa-book"></i>
                </div>
                <div class="text-muted fw-medium mb-1">{{ $mode === 'wali_kelas' ? 'Jumlah Siswa' : 'Mata Pelajaran' }}</div>
                <h3 class="fw-bold text-secondary mb-0">{{ $mode === 'wali_kelas' ? ($stats['total_siswa'] ?? 0) : ($stats['total_mapel'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--info-color);">
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Total Jadwal</div>
                <h3 class="fw-bold text-info mb-0">{{ $stats['total_jadwal'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--warning-color);">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Materi Dibagikan</div>
                <h3 class="fw-bold text-warning mb-0">{{ $stats['total_materi'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    @if($mode !== 'wali_kelas')
        <div class="modern-card mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h4 class="mb-0 fw-bold" style="color: var(--text-primary);">
                        <i class="fas fa-book-open-reader text-primary me-2"></i>Status Silabus
                    </h4>
                    <div class="text-muted small mt-1">Pantau kelengkapan silabus tiap mata pelajaran yang Anda ampu.</div>
                </div>
                <a href="{{ route('guru.silabus') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    Kelola Silabus
                </a>
            </div>
            <div class="row g-3">
                @foreach([
                    'belum' => ['label' => 'Belum Dibuat', 'color' => '#94a3b8', 'icon' => 'fas fa-circle-plus'],
                    'draft' => ['label' => 'Draft', 'color' => '#64748b', 'icon' => 'fas fa-pen'],
                    'diajukan' => ['label' => 'Diajukan', 'color' => '#f59e0b', 'icon' => 'fas fa-paper-plane'],
                    'revisi' => ['label' => 'Revisi', 'color' => '#ef4444', 'icon' => 'fas fa-rotate-left'],
                    'disetujui' => ['label' => 'Disetujui', 'color' => '#10b981', 'icon' => 'fas fa-lock'],
                ] as $key => $kartu)
                    <div class="col-6 col-md-4 col-xl">
                        <div class="border rounded p-3 h-100 bg-light" style="border-left: 3px solid {{ $kartu['color'] }} !important;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small fw-semibold">{{ $kartu['label'] }}</div>
                                    <div class="fs-4 fw-bold" style="color: {{ $kartu['color'] }};">{{ $silabusStats[$key] ?? 0 }}</div>
                                </div>
                                <i class="{{ $kartu['icon'] }}" style="color: {{ $kartu['color'] }}; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold" style="color: var(--text-primary);"><i class="fas {{ $mode === 'wali_kelas' ? 'fa-users' : 'fa-chalkboard-teacher' }} text-primary me-2"></i>{{ $mode === 'wali_kelas' ? 'Kelas Perwalian Anda' : 'Jadwal Mengajar Anda' }}</h4>
        </div>
        <div class="row g-3">
            @forelse($recentData as $ampu)
                <div class="col-md-4">
                    <div class="border rounded p-3 d-flex flex-column h-100 bg-light">
                        @if($mode === 'wali_kelas')
                            <h5 class="fw-bold text-primary mb-1">{{ $ampu->nama_kelas ?? '-' }}</h5>
                            <div class="text-muted small mb-2">{{ $ampu->siswa_count }} siswa</div>
                        @else
                            <h5 class="fw-bold text-primary mb-1">{{ $ampu->mataPelajaran->nama_mapel ?? '-' }}</h5>
                            <div class="text-muted small mb-2">{{ $ampu->mataPelajaran->kode_mapel ?? '-' }}</div>
                        @endif
                        <div class="mt-auto pt-2 border-top">
                            <x-ui.badge variant="info" icon="fas fa-door-open">{{ $mode === 'wali_kelas' ? 'Wali Kelas' : ($ampu->kelas->nama_kelas ?? '-') }}</x-ui.badge>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted mb-0">Anda belum ditugaskan mengajar di kelas manapun pada semester ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
