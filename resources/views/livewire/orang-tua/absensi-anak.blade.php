<div>
    <x-layout.page-header title="Kehadiran Anak" subtitle="Pantau absensi harian dan rekapitulasi kehadiran anak Anda di sekolah." />

    {{-- Filter Anak --}}
    @if($anakList->count() > 1)
    <div class="row mb-4">
        <div class="col-md-5">
            <label class="form-label text-muted fw-bold">Pilih Anak</label>
            <select class="form-select border-primary" wire:model.live="id_anak">
                @foreach($anakList as $anak)
                    <option value="{{ $anak->id_siswa }}">{{ $anak->nama_siswa }} - NISN: {{ $anak->nisn }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @endif

    @if($anakList->count() > 0)
    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="modern-card text-center p-3 border-top border-success border-4 h-100 d-flex flex-column justify-content-center">
                <h2 class="text-success fw-bold mb-1">{{ $summary['hadir'] }}</h2>
                <span class="text-muted fw-semibold">Hadir</span>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="modern-card text-center p-3 border-top border-warning border-4 h-100 d-flex flex-column justify-content-center">
                <h2 class="text-warning fw-bold mb-1">{{ $summary['sakit'] }}</h2>
                <span class="text-muted fw-semibold">Sakit</span>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="modern-card text-center p-3 border-top border-info border-4 h-100 d-flex flex-column justify-content-center">
                <h2 class="text-info fw-bold mb-1">{{ $summary['izin'] }}</h2>
                <span class="text-muted fw-semibold">Izin</span>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="modern-card text-center p-3 border-top border-danger border-4 h-100 d-flex flex-column justify-content-center">
                <h2 class="text-danger fw-bold mb-1">{{ $summary['alpa'] }}</h2>
                <span class="text-muted fw-semibold">Alpa</span>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="modern-card">
        <h5 class="fw-bold text-primary mb-3">Riwayat Absensi Terkini</h5>
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Jadwal (Hari & Waktu)</th>
                        <th class="text-center">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensiList as $absensi)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d M Y') }}</td>
                            <td>
                                <div class="fw-semibold text-primary">{{ $absensi->jadwalPelajaran->guruAmpu->mataPelajaran->nama_mapel ?? '-' }}</div>
                                <div class="small text-muted">{{ $absensi->jadwalPelajaran->guruAmpu->guru->nama_guru ?? '-' }}</div>
                            </td>
                            <td>
                                <x-ui.badge variant="secondary" icon="fas fa-clock">{{ $absensi->jadwalPelajaran->hari }}, {{ \Carbon\Carbon::parse($absensi->jadwalPelajaran->jam_mulai)->format('H:i') }}</x-ui.badge>
                            </td>
                            <td class="text-center">
                                <x-ui.badge variant="{{ $absensi->status_kehadiran->getColor() }}" icon="{{ $absensi->status_kehadiran->getIcon() }}">{{ $absensi->status_kehadiran->getLabel() }}</x-ui.badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-check mb-3 text-info opacity-50" style="font-size: 3rem;"></i>
                                <h5>Belum Ada Riwayat</h5>
                                <p class="mb-0">Anak Anda belum memiliki rekam absensi sejauh ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($absensiList->hasPages())
            <div class="mt-4">
                {{ $absensiList->links() }}
            </div>
        @endif
    </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i> Belum ada anak yang terhubung ke akun Anda.
        </div>
    @endif
</div>
