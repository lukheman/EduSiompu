<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Penugasan Guru" subtitle="Tetapkan mata pelajaran dan kelas yang diajar oleh guru sesuai tahun ajaran">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Penugasan
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-ui.alert variant="success" title="Berhasil!" class="mb-4">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    {{-- Guru Ampu Table Card --}}
    <div class="modern-card">
        {{-- Advanced Filters UX --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Tahun Ajaran</label>
                <select class="form-select" wire:model.live="filter_tahun_ajaran">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->nama_tahun }} - {{ ucfirst($ta->semester) }} {{ $ta->status_aktif ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Saring berdasarkan Guru</label>
                <select class="form-select" wire:model.live="filter_guru">
                    <option value="">Semua Guru</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id_guru }}">{{ $guru->nama_guru }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label text-muted small fw-bold">Pencarian Mapel / Kelas</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Ketik mapel atau kelas..."
                        wire:model.live.debounce.300ms="search" style="background: var(--input-bg);">
                </div>
            </div>
        </div>

        {{-- Guru Ampu Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Guru Pengampu</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th style="width: 120px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guruAmpuList as $guruAmpu)
                        <tr wire:key="guru-ampu-{{ $guruAmpu->id_guru_ampu }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($guruAmpu->guru && $guruAmpu->guru->avatar)
                                        <img src="{{ Storage::url($guruAmpu->guru->avatar) }}" alt="Avatar" class="me-3 border border-primary-subtle rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="user-avatar bg-primary-subtle text-primary me-3 border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; font-size: 1.2rem;">
                                            {{ strtoupper(substr($guruAmpu->guru->nama_guru ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-primary">{{ $guruAmpu->guru->nama_guru ?? '-' }}</div>
                                        <div class="small text-muted"><i class="fas fa-id-card me-1"></i>{{ $guruAmpu->guru->nip ?? 'Tanpa NIP' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $guruAmpu->mataPelajaran->nama_mapel ?? '-' }}</div>
                                <x-ui.badge variant="secondary" class="mt-1" style="font-size: 0.7rem;">
                                    {{ $guruAmpu->mataPelajaran->kode_mapel ?? '-' }}
                                </x-ui.badge>
                            </td>
                            <td>
                                <x-ui.badge variant="info" icon="fas fa-chalkboard">{{ $guruAmpu->kelas->nama_kelas ?? '-' }}</x-ui.badge>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-view wire:click="openViewModal({{ $guruAmpu->id_guru_ampu }})" tooltip="Lihat Detail" />
                                    <x-ui.btn-edit wire:click="openEditModal({{ $guruAmpu->id_guru_ampu }})" tooltip="Edit Penugasan" />
                                    <x-ui.btn-delete wire:click="confirmDelete({{ $guruAmpu->id_guru_ampu }})" tooltip="Hapus Penugasan" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-clipboard-list mb-3 text-primary" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h5>Belum Ada Penugasan</h5>
                                    <p class="mb-0">Tidak ada data penugasan guru untuk kriteria pencarian ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($guruAmpuList->hasPages())
            <div class="mt-4">
                {{ $guruAmpuList->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop>
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        <i class="fas {{ $editingGuruAmpuId ? 'fa-edit' : 'fa-plus-circle' }} me-2 text-primary"></i>
                        {{ $editingGuruAmpuId ? 'Edit Penugasan Guru' : 'Tambah Penugasan Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    @if($errors->has('id_guru'))
                        <div class="alert alert-danger p-2 mb-3 small border-0 bg-danger-subtle text-danger" style="border-radius: 8px;">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first('id_guru') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="id_tahun_ajaran" class="form-label text-muted small fw-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_tahun_ajaran') is-invalid @enderror" id="id_tahun_ajaran" wire:model="id_tahun_ajaran">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->nama_tahun }} (Semester {{ $ta->semester }}) {{ $ta->status_aktif ? '✓' : '' }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">Penugasan berlaku untuk tahun ajaran ini.</div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="id_guru" class="form-label text-muted small fw-bold">Guru Pengajar <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_guru') is-invalid @enderror" id="id_guru" wire:model="id_guru">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id_guru }}">{{ $guru->nama_guru }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="id_mata_pelajaran" class="form-label text-muted small fw-bold">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_mata_pelajaran') is-invalid @enderror" id="id_mata_pelajaran" wire:model="id_mata_pelajaran">
                                <option value="">-- Mapel --</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id_mata_pelajaran }}">{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="id_kelas" class="form-label text-muted small fw-bold">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_kelas') is-invalid @enderror" id="id_kelas" wire:model="id_kelas">
                                <option value="">-- Kelas --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <x-ui.button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">
                            {{ $editingGuruAmpuId ? 'Simpan Perubahan' : 'Tetapkan Penugasan' }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- View Detail Modal --}}
    @if ($showViewModal && $viewingGuruAmpu)
        <div class="modal-backdrop-custom" wire:click.self="closeViewModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 600px;">
                <div class="modal-header-custom border-bottom pb-3 mb-4">
                    <h5 class="modal-title-custom">
                        <i class="fas fa-info-circle text-info me-2"></i> Detail Penugasan Guru
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeViewModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modern-card bg-light border-0 mb-4 p-3 d-flex align-items-center">
                    @if($viewingGuruAmpu->guru && $viewingGuruAmpu->guru->avatar)
                        <img src="{{ Storage::url($viewingGuruAmpu->guru->avatar) }}" alt="Avatar" class="me-3 rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                        <div class="user-avatar bg-primary text-white me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.8rem;">
                            {{ strtoupper(substr($viewingGuruAmpu->guru->nama_guru ?? '?', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="mb-1 text-primary fw-bold">{{ $viewingGuruAmpu->guru->nama_guru ?? '-' }}</h4>
                        <div class="text-muted"><i class="fas fa-id-card me-2"></i>NIP: {{ $viewingGuruAmpu->guru->nip ?? 'Tidak ada NIP' }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-medium" style="width: 35%;">Mata Pelajaran</th>
                                <td>
                                    <span class="fw-semibold">{{ $viewingGuruAmpu->mataPelajaran->nama_mapel ?? '-' }}</span><br>
                                    <small class="text-muted">{{ $viewingGuruAmpu->mataPelajaran->kode_mapel ?? '-' }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Kelas</th>
                                <td><x-ui.badge variant="info" icon="fas fa-chalkboard">{{ $viewingGuruAmpu->kelas->nama_kelas ?? '-' }}</x-ui.badge></td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Tahun Ajaran</th>
                                <td>
                                    <span class="fw-semibold">{{ $viewingGuruAmpu->tahunAjaran->nama_tahun ?? '-' }}</span>
                                    <x-ui.badge variant="{{ $viewingGuruAmpu->tahunAjaran->status_aktif ? 'success' : 'secondary' }}" class="ms-2">
                                        Semester {{ ucfirst($viewingGuruAmpu->tahunAjaran->semester ?? '') }}
                                    </x-ui.badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <x-ui.button type="button" variant="outline" wire:click="closeViewModal">
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
        message="Apakah Anda yakin ingin mencabut penugasan guru ini? Data absensi dan pertemuan yang terkait mungkin akan ikut terhapus atau bermasalah."
        on-confirm="deleteGuruAmpu"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Ya, Cabut Penugasan
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
