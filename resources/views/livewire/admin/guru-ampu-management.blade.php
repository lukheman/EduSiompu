<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Daftar Penugasan Guru" subtitle="Melihat detail guru, mata pelajaran yang diampu, beserta jadwal dan kelasnya.">
    </x-layout.page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-ui.toast variant="success">
            {{ session('success') }}
        </x-ui.toast>
    @endif

    {{-- Filters and Search --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-2 mb-md-0">
            <x-form.select wire:model.live="filter_tahun_ajaran" :options="$tahunAjarans->mapWithKeys(fn($ta) => [$ta->id_tahun_ajaran => $ta->nama_tahun . ' ' . ucfirst($ta->semester)])->toArray()" placeholder="Semua Tahun Ajaran" />
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <x-form.select wire:model.live="filter_guru" :options="$gurus->pluck('nama_guru', 'id_guru')->toArray()" placeholder="Semua Guru" />
        </div>
        <div class="col-md-6">
            <x-form.input wire:model.live.debounce.300ms="search" placeholder="Cari penugasan..." icon="fas fa-search" />
        </div>
    </div>

    {{-- Guru Ampu Table --}}
    <x-layout.modern-card :padding="false">
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Guru Pengampu</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guruAmpuList as $guruAmpu)
                        <tr wire:key="guru-ampu-{{ $guruAmpu->id_guru }}-{{ $guruAmpu->id_tahun_ajaran }}">
                            <td>
                                <x-ui.badge variant="primary">
                                    {{ $guruAmpu->tahunAjaran->nama_tahun ?? 'N/A' }} {{ ucfirst($guruAmpu->tahunAjaran->semester ?? '') }}
                                </x-ui.badge>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-ui.avatar :name="$guruAmpu->guru->nama_guru ?? 'N/A'" size="sm" class="me-2" />
                                    <div>
                                        <h6 class="mb-0 text-dark fw-bold">{{ $guruAmpu->guru->nama_guru ?? 'N/A' }}</h6>
                                        <small class="text-muted">{{ $guruAmpu->guru->nip ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-view wire:click="openViewModal({{ $guruAmpu->id_guru }}, {{ $guruAmpu->id_tahun_ajaran }})" tooltip="Lihat Detail Mata Pelajaran & Kelas" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-ui.empty-state icon="fas fa-user-tag" title="Belum ada penugasan guru" description="Data penugasan guru akan otomatis terbuat saat Anda membuat Jadwal Pelajaran." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($guruAmpuList->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $guruAmpuList->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    {{-- View Detail & Jadwal Modal --}}
    @if ($showViewModal && $viewingPenugasan && $viewingPenugasan->isNotEmpty())
        @php
            $guruInfo = $viewingPenugasan->first()->guru;
            $taInfo = $viewingPenugasan->first()->tahunAjaran;
        @endphp
        <div class="modal-backdrop-custom" wire:click.self="closeViewModal">
            <x-layout.modern-card class="modal-content-custom m-auto position-relative" style="max-width: 700px; margin-top: 5rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h5 class="mb-0 fw-bold">Detail Penugasan Guru</h5>
                    <button wire:click="closeViewModal" class="btn-close"></button>
                </div>

                <div class="mb-4 text-center">
                    <x-ui.avatar :name="$guruInfo->nama_guru" size="lg" class="mb-3 mx-auto" />
                    <h5 class="fw-bold text-dark mb-0">{{ $guruInfo->nama_guru }}</h5>
                    <p class="text-muted">{{ $guruInfo->nip }}</p>
                    <x-ui.badge variant="primary">
                        Tahun Ajaran: {{ $taInfo->nama_tahun ?? 'N/A' }} {{ ucfirst($taInfo->semester ?? '') }}
                    </x-ui.badge>
                </div>

                <h6 class="fw-bold mb-3">Daftar Mata Pelajaran & Kelas</h6>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Jadwal Mengajar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($viewingPenugasan as $penugasan)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-book-open text-primary me-2"></i>
                                            <span class="fw-medium text-dark">{{ $penugasan->mataPelajaran->nama_mapel }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <x-ui.badge variant="info">
                                            <i class="fas fa-chalkboard me-1"></i> {{ $penugasan->kelas->nama_kelas }}
                                        </x-ui.badge>
                                    </td>
                                    <td>
                                        @if($penugasan->jadwalPelajaran->isEmpty())
                                            <span class="text-muted fst-italic small">Belum Ada Jadwal</span>
                                        @else
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($penugasan->jadwalPelajaran as $jadwal)
                                                    <span class="badge bg-secondary text-start" style="width: fit-content;">
                                                        {{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <x-ui.button type="button" variant="primary" wire:click="closeViewModal">
                        Tutup
                    </x-ui.button>
                </div>
            </x-layout.modern-card>
        </div>
    @endif
</div>
