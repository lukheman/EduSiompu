<div>
    {{-- Page Header --}}
    @php
        $userName = Auth::guard('guru')->user()->nama_guru ?? 'Guru';
        $greeting = 'Selamat Datang, Bapak/Ibu ' . $userName;
        $subtitle = $activeTa ? 'Tahun Ajaran Aktif: ' . $activeTa->nama_tahun . ' (Semester ' . ucfirst($activeTa->semester) . ')' : 'Belum ada Tahun Ajaran Aktif';
    @endphp

    <x-layout.page-header :title="$greeting" :subtitle="$subtitle" />

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--primary-color);">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Kelas Diampu</div>
                <h3 class="fw-bold text-primary mb-0">{{ $stats['total_kelas'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--secondary-color);">
                <div class="stat-icon bg-secondary-subtle text-secondary">
                    <i class="fas fa-book"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Mata Pelajaran</div>
                <h3 class="fw-bold text-secondary mb-0">{{ $stats['total_mapel'] ?? 0 }}</h3>
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

    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold" style="color: var(--text-primary);"><i class="fas fa-chalkboard-teacher text-primary me-2"></i>Jadwal Mengajar Anda</h4>
        </div>
        <div class="row g-3">
            @forelse($recentData as $ampu)
                <div class="col-md-4">
                    <div class="border rounded p-3 d-flex flex-column h-100 bg-light">
                        <h5 class="fw-bold text-primary mb-1">{{ $ampu->mataPelajaran->nama_mapel ?? '-' }}</h5>
                        <div class="text-muted small mb-2">{{ $ampu->mataPelajaran->kode_mapel ?? '-' }}</div>
                        <div class="mt-auto pt-2 border-top">
                            <x-ui.badge variant="info" icon="fas fa-door-open">{{ $ampu->kelas->nama_kelas ?? '-' }}</x-ui.badge>
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
