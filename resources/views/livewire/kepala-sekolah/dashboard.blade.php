<div>
    @php
        $userName = Auth::guard('kepala_sekolah')->user()->nama_kepala_sekolah ?? 'Kepala Sekolah';
        $subtitle = $activeTa ? 'Tahun Ajaran Aktif: ' . $activeTa->nama_tahun . ' (Semester ' . ucfirst($activeTa->semester) . ')' : 'Belum ada Tahun Ajaran Aktif';
    @endphp

    <x-layout.page-header :title="'Selamat Datang, ' . $userName" :subtitle="$subtitle" />

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--primary-color);">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Total Guru</div>
                <h3 class="fw-bold text-primary mb-0">{{ $stats['total_guru'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--info-color);">
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Total Siswa</div>
                <h3 class="fw-bold text-info mb-0">{{ $stats['total_siswa'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--success-color);">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Total Kelas</div>
                <h3 class="fw-bold text-success mb-0">{{ $stats['total_kelas'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="--accent-color: var(--warning-color);">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fas fa-book"></i>
                </div>
                <div class="text-muted fw-medium mb-1">Mata Pelajaran</div>
                <h3 class="fw-bold text-warning mb-0">{{ $stats['total_mapel'] }}</h3>
            </div>
        </div>
    </div>

    <x-layout.table-card title="Ringkasan Kelas">
        <x-layout.table>
            <x-slot:head>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kelas</th>
                    <th>Wali Kelas</th>
                    <th class="text-center">Jumlah Siswa</th>
                </tr>
            </x-slot:head>

            @forelse($kelasList as $index => $kelas)
                <tr class="align-middle">
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: 500;">{{ $kelas->nama_kelas }}</td>
                    <td>{{ $kelas->waliKelas->nama_guru ?? '-' }}</td>
                    <td class="text-center">{{ $kelas->siswa_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <x-ui.empty-state icon="fas fa-chalkboard" title="Belum ada kelas" description="Data kelas belum tersedia." size="sm" />
                    </td>
                </tr>
            @endforelse
        </x-layout.table>
    </x-layout.table-card>
</div>
