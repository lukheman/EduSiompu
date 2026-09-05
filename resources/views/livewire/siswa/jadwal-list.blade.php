<div>
    <x-layout.page-header title="Jadwal Pelajaran" subtitle="Lihat jadwal pelajaran kelas {{ $kelas->nama_kelas ?? '' }} setiap hari" />

    {{-- Filters --}}
    <div class="modern-card mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-muted">Tahun Ajaran</label>
                <select class="form-select form-control" wire:model.live="id_tahun_ajaran">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjaranOptions as $ta)
                        <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->nama_tahun }} - {{ ucfirst($ta->semester) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted">Tanggal</label>
                <input type="date" class="form-control" wire:model.live="tanggal">
                @if($hari)
                    <div class="small text-muted mt-1">Menampilkan jadwal hari <strong class="text-primary">{{ $hari }}</strong></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Jadwal grouped by day --}}
    @forelse($jadwalList as $hari => $jadwals)
        <div class="d-flex align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="fas fa-calendar-day text-primary me-2"></i> {{ $hari }}</h5>
            <x-ui.badge variant="primary" class="ms-2">{{ $jadwals->count() }} pelajaran</x-ui.badge>
        </div>
        <div class="row g-3 mb-4">
            @foreach($jadwals as $jadwal)
                <div class="col-md-6 col-lg-4" wire:key="jadwal-{{ $jadwal->id_jadwal_pelajaran }}">
                    <div class="modern-card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <x-ui.badge variant="light text-dark">{{ $jadwal->hari }}</x-ui.badge>
                            <span class="small fw-bold text-muted">
                                <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </span>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">{{ $jadwal->guruAmpu->mataPelajaran->nama_mapel ?? 'N/A' }}</h6>
                        <p class="mb-0 text-muted small"><i class="fas fa-chalkboard-teacher text-info me-1"></i> {{ $jadwal->guruAmpu->guru->nama_guru ?? 'N/A' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <x-layout.modern-card>
            <x-ui.empty-state icon="fas fa-calendar-times" title="Belum Ada Jadwal" description="Tidak ada jadwal pelajaran untuk kelas Anda pada tanggal ini." />
        </x-layout.modern-card>
    @endforelse
</div>
