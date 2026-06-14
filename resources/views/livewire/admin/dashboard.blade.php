<div>
    {{-- Page Header --}}
    @php
        $userName = Auth::guard('admin')->user()->nama ?? 'Admin';
        $greeting = 'Selamat Datang, ' . $userName;
        $subtitle = $activeTa ? 'Tahun Ajaran Aktif: ' . $activeTa->nama_tahun . ' (Semester ' . ucfirst($activeTa->semester) . ')' : 'Belum ada Tahun Ajaran Aktif';
    @endphp

    <x-layout.page-header :title="$greeting" :subtitle="$subtitle" />

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--primary-color);">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Total Siswa</div>
                <h3 class="fw-bold text-primary mb-0">{{ number_format($stats['total_siswa'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--secondary-color);">
                <div class="stat-icon bg-secondary-subtle text-secondary">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Total Guru</div>
                <h3 class="fw-bold text-secondary mb-0">{{ number_format($stats['total_guru'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--info-color);">
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Total Kelas</div>
                <h3 class="fw-bold text-info mb-0">{{ number_format($stats['total_kelas'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--warning-color);">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fas fa-book"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Mata Pelajaran</div>
                <h3 class="fw-bold text-warning mb-0">{{ number_format($stats['total_mapel'] ?? 0) }}</h3>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <h4 class="preview-title"><i class="fas fa-info-circle text-primary me-2"></i>Status Sistem</h4>
        @if($activeTa)
            <div class="alert-modern bg-success-subtle border-0">
                <i class="fas fa-check-circle text-success fa-2x mt-1"></i>
                <div>
                    <h6 class="fw-bold text-success mb-1">Sistem Berjalan Normal</h6>
                    <p class="mb-0 text-success-emphasis small">Tahun Ajaran <strong>{{ $activeTa->nama_tahun }}</strong> (Semester {{ ucfirst($activeTa->semester) }}) saat ini sedang aktif. Semua aktivitas presensi dan KBM mengacu pada periode ini.</p>
                </div>
            </div>
        @else
            <div class="alert-modern bg-danger-subtle border-0">
                <i class="fas fa-exclamation-circle text-danger fa-2x mt-1"></i>
                <div>
                    <h6 class="fw-bold text-danger mb-1">Perhatian: Tidak Ada Tahun Ajaran Aktif!</h6>
                    <p class="mb-0 text-danger-emphasis small">Silakan menuju menu <strong>Tahun Ajaran</strong> untuk mengaktifkan salah satu periode agar sistem pembelajaran dan presensi dapat berjalan.</p>
                </div>
            </div>
        @endif
    </div>
</div>