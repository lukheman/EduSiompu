<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Pertemuan & Absensi" subtitle="Kelola pertemuan belajar dan absensi siswa">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Pertemuan
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-ui.alert variant="success" title="Berhasil!" class="mb-4">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert variant="danger" title="Gagal!" class="mb-4">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    {{-- Pertemuan Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Daftar Pertemuan</h5>
            <div class="d-flex gap-2" style="max-width: 500px; width: 100%;">
                <select class="form-control form-select" wire:model.live="filter_guru_ampu">
                    <option value="">Semua Kelas & Mapel</option>
                    @foreach($guruAmpuOptions as $ampu)
                        <option value="{{ $ampu->id_guru_ampu }}">
                            {{ $ampu->kelas->nama_kelas ?? '' }} - {{ $ampu->mataPelajaran->nama_mapel ?? '' }}
                        </option>
                    @endforeach
                </select>
                <div class="input-group">
                    <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                        <i class="fas fa-search" style="color: var(--text-muted);"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Cari..."
                        wire:model.live.debounce.300ms="search" style="border-left: none;">
                </div>
            </div>
        </div>

        {{-- Pertemuan Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Pertemuan Ke</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Tanggal</th>
                        <th>Pokok Bahasan</th>
                        <th style="width: 150px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pertemuanList as $pertemuan)
                        <tr wire:key="pertemuan-{{ $pertemuan->id_pertemuan }}">
                            <td>
                                <x-ui.badge variant="info" icon="fas fa-calendar-day">Ke-{{ $pertemuan->pertemuan_ke }}</x-ui.badge>
                            </td>
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">
                                    {{ $pertemuan->guruAmpu->kelas->nama_kelas ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">
                                    {{ $pertemuan->guruAmpu->mataPelajaran->nama_mapel ?? 'N/A' }}
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($pertemuan->tanggal)->format('d M Y') }}</td>
                            <td>
                                <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $pertemuan->pokok_bahasan }}">
                                    {{ $pertemuan->pokok_bahasan }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.button variant="warning" wire:click="openAbsensiModal({{ $pertemuan->id_pertemuan }})" tooltip="Kelola Absensi" icon="fas fa-calendar-check">

                                    Absensi
                                    </x-ui.button>
                                    <x-ui.btn-view wire:click="openViewModal({{ $pertemuan->id_pertemuan }})" tooltip="Lihat Detail" />
                                    <x-ui.btn-edit wire:click="openEditModal({{ $pertemuan->id_pertemuan }})" tooltip="Edit Pertemuan" />
                                    <x-ui.btn-delete wire:click="confirmDelete({{ $pertemuan->id_pertemuan }})" tooltip="Hapus Pertemuan" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-times mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data pertemuan ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($pertemuanList->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $pertemuanList->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal Pertemuan --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop>
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingPertemuanId ? 'Edit Pertemuan' : 'Tambah Pertemuan Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="mb-3">
                        <label for="id_guru_ampu" class="form-label">Pilih Kelas & Mapel <span style="color: var(--danger-color);">*</span></label>
                        <select class="form-control form-select @error('id_guru_ampu') is-invalid @enderror" id="id_guru_ampu" wire:model.live="id_guru_ampu">
                            <option value="">-- Pilih Penugasan --</option>
                            @foreach($guruAmpuOptions as $ampu)
                                <option value="{{ $ampu->id_guru_ampu }}">
                                    {{ $ampu->kelas->nama_kelas ?? '' }} - {{ $ampu->mataPelajaran->nama_mapel ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_guru_ampu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pertemuan_ke" class="form-label">Pertemuan Ke <span style="color: var(--danger-color);">*</span></label>
                            <input type="number" min="1" class="form-control @error('pertemuan_ke') is-invalid @enderror" id="pertemuan_ke"
                                wire:model="pertemuan_ke">
                            @error('pertemuan_ke')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span style="color: var(--danger-color);">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                                wire:model="tanggal">
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pokok_bahasan" class="form-label">Pokok Bahasan <span style="color: var(--danger-color);">*</span></label>
                        <textarea class="form-control @error('pokok_bahasan') is-invalid @enderror" id="pokok_bahasan"
                            wire:model="pokok_bahasan" rows="3" placeholder="Contoh: Pengenalan Aljabar"></textarea>
                        @error('pokok_bahasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <x-ui.button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-ui.button>
                        <x-ui.button type="submit" variant="primary">
                            {{ $editingPertemuanId ? 'Simpan Perubahan' : 'Tambah Pertemuan' }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Absensi Modal --}}
    @if ($showAbsensiModal)
        <div class="modal-backdrop-custom" wire:click.self="closeAbsensiModal" style="z-index: 1060;">
            <div class="modal-content-custom" wire:click.stop style="max-width: 800px; max-height: 90vh; display: flex; flex-direction: column;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        Kelola Absensi Siswa
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeAbsensiModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="flex-grow-1" style="overflow-y: auto; padding-right: 10px;">
                    <form wire:submit="saveAbsensi">
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th style="width: 250px;">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($absensiData as $id_siswa => $data)
                                        <tr wire:key="absensi-{{ $id_siswa }}">
                                            <td class="fw-semibold">{{ $data['nama'] }}</td>
                                            <td class="text-muted">{{ $data['nisn'] }}</td>
                                            <td>
                                                <select class="form-select form-control" wire:model="absensiData.{{ $id_siswa }}.status" style="width: auto;">
                                                    <option value="hadir">Hadir</option>
                                                    <option value="sakit">Sakit</option>
                                                    <option value="izin">Izin</option>
                                                    <option value="alpa">Alpa</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <div class="text-muted">
                                                    Tidak ada siswa di kelas ini.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <x-ui.button type="button" variant="outline" wire:click="closeAbsensiModal">
                        Batal
                    </x-ui.button>
                    @if(count($absensiData) > 0)
                        <x-ui.button type="button" variant="primary" wire:click="saveAbsensi">
                            Simpan Absensi
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- View Confirmation Modal --}}
    @if ($showViewModal && $viewingPertemuan)
        <div class="modal-backdrop-custom" wire:click.self="closeViewModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 600px;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        Detail Pertemuan
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeViewModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mb-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 35%; background-color: var(--input-bg);">Pertemuan Ke</th>
                                <td>{{ $viewingPertemuan->pertemuan_ke }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: var(--input-bg);">Kelas</th>
                                <td>{{ $viewingPertemuan->guruAmpu->kelas->nama_kelas ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: var(--input-bg);">Mata Pelajaran</th>
                                <td>{{ $viewingPertemuan->guruAmpu->mataPelajaran->nama_mapel ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: var(--input-bg);">Tanggal</th>
                                <td>{{ \Carbon\Carbon::parse($viewingPertemuan->tanggal)->translatedFormat('l, d F Y') }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: var(--input-bg);">Pokok Bahasan</th>
                                <td>{{ $viewingPertemuan->pokok_bahasan }}</td>
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

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-modal
        :show="$showDeleteModal"
        title="Konfirmasi Hapus"
        message="Apakah Anda yakin ingin menghapus pertemuan ini? Data absensi yang terkait juga akan dihapus."
        on-confirm="deletePertemuan"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Pertemuan
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
