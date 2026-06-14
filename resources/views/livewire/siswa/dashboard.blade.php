<div>
    {{-- Page Header --}}
    @php
        $userName = Auth::guard('siswa')->user()->nama_siswa ?? 'Siswa';
        $greeting = 'Selamat Datang, ' . $userName;
        $subtitle = $activeTa ? 'Tahun Ajaran Aktif: ' . $activeTa->nama_tahun . ' (Semester ' . ucfirst($activeTa->semester) . ')' : 'Belum ada Tahun Ajaran Aktif';
    @endphp

    <x-layout.page-header :title="$greeting" :subtitle="$subtitle" />

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--success-color);">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Total Hadir</div>
                <h3 class="fw-bold text-success mb-0">{{ $stats['hadir'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--warning-color);">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fas fa-briefcase-medical"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Sakit</div>
                <h3 class="fw-bold text-warning mb-0">{{ $stats['sakit'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--info-color);">
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Izin</div>
                <h3 class="fw-bold text-info mb-0">{{ $stats['izin'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--danger-color);">
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Alpa</div>
                <h3 class="fw-bold text-danger mb-0">{{ $stats['alpa'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h4 class="mb-0 fw-bold" style="color: var(--text-primary);"><i class="fas fa-book-reader text-primary me-2"></i>Materi Terbaru untuk Kelas Anda</h4>
            <a href="{{ route('siswa.materi') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
        </div>
        
        <div class="list-group list-group-flush border-0">
            @forelse($recentData as $materi)
                <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary rounded p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-file-pdf fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-primary mb-1">{{ $materi->judul_materi }}</h6>
                            <div class="text-muted small">
                                <i class="fas fa-book-open me-1"></i> {{ $materi->guruAmpu->mataPelajaran->nama_mapel ?? '-' }} 
                                <span class="mx-2">•</span> 
                                <i class="fas fa-user-tie me-1"></i> {{ $materi->guruAmpu->guru->nama_guru ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small mb-2"><i class="fas fa-clock me-1"></i> {{ $materi->created_at->diffForHumans() }}</div>
                        <a href="{{ Storage::url($materi->file_path) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 text-primary fw-medium">
                            <i class="fas fa-download me-1"></i> Unduh
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-inbox text-muted fs-2"></i>
                    </div>
                    <h6 class="fw-bold text-muted">Belum Ada Materi</h6>
                    <p class="text-muted small mb-0">Belum ada materi pembelajaran yang dibagikan untuk kelas Anda.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
