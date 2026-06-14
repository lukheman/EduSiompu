<div>
    <x-layout.page-header title="Absensi Saya" subtitle="Pantau riwayat kehadiran Anda di setiap mata pelajaran" />

    {{-- Filters --}}
    <div class="row mb-4">
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
    </div>

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
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengampu</th>
                        <th>Pertemuan Ke</th>
                        <th>Pokok Bahasan</th>
                        <th>Status</th>
                        <th style="width: 100px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensiList as $absensi)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($absensi->pertemuan->tanggal)->translatedFormat('d M Y') }}</td>
                            <td>
                                <div class="fw-semibold text-primary">{{ $absensi->pertemuan->guruAmpu->mataPelajaran->nama_mapel ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="text-muted">{{ $absensi->pertemuan->guruAmpu->guru->nama_guru ?? '-' }}</div>
                            </td>
                            <td>
                                <x-ui.badge variant="secondary" icon="fas fa-calendar-day">Ke-{{ $absensi->pertemuan->pertemuan_ke }}</x-ui.badge>
                            </td>
                            <td>
                                <div style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $absensi->pertemuan->pokok_bahasan }}">
                                    {{ $absensi->pertemuan->pokok_bahasan }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $variant = match($absensi->status_kehadiran) {
                                        'hadir' => 'success',
                                        'sakit' => 'warning',
                                        'izin' => 'info',
                                        'alpa' => 'danger',
                                        default => 'secondary'
                                    };
                                    $icon = match($absensi->status_kehadiran) {
                                        'hadir' => 'fas fa-check-circle',
                                        'sakit' => 'fas fa-briefcase-medical',
                                        'izin' => 'fas fa-envelope-open-text',
                                        'alpa' => 'fas fa-times-circle',
                                        default => 'fas fa-question-circle'
                                    };
                                @endphp
                                <x-ui.badge variant="{{ $variant }}" icon="{{ $icon }}">{{ ucfirst($absensi->status_kehadiran) }}</x-ui.badge>
                            </td>
                            <td>
                                <x-ui.btn-view wire:click="openViewModal({{ $absensi->id_absensi }})" tooltip="Lihat Detail" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-clipboard-list mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0">Belum ada data absensi untuk filter ini.</p>
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

    {{-- View Modal --}}
    @if ($showViewModal && $viewingAbsensi)
        <div class="modal-backdrop-custom" wire:click.self="closeViewModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 600px;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        Detail Absensi
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeViewModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 35%; background-color: var(--input-bg);">Tanggal</th>
                                <td>{{ \Carbon\Carbon::parse($viewingAbsensi->pertemuan->tanggal)->translatedFormat('l, d F Y') }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: var(--input-bg);">Pertemuan Ke</th>
                                <td>{{ $viewingAbsensi->pertemuan->pertemuan_ke }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: var(--input-bg);">Mata Pelajaran</th>
                                <td>{{ $viewingAbsensi->pertemuan->guruAmpu->mataPelajaran->nama_mapel ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: var(--input-bg);">Guru Pengampu</th>
                                <td>{{ $viewingAbsensi->pertemuan->guruAmpu->guru->nama_guru ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: var(--input-bg);">Pokok Bahasan</th>
                                <td>{{ $viewingAbsensi->pertemuan->pokok_bahasan }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: var(--input-bg);">Status Kehadiran</th>
                                <td>
                                    @php
                                        $variant = match($viewingAbsensi->status_kehadiran) {
                                            'hadir' => 'success',
                                            'sakit' => 'warning',
                                            'izin' => 'info',
                                            'alpa' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <x-ui.badge variant="{{ $variant }}">{{ ucfirst($viewingAbsensi->status_kehadiran) }}</x-ui.badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <x-ui.button type="button" variant="primary" wire:click="closeViewModal">
                        Tutup
                    </x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
