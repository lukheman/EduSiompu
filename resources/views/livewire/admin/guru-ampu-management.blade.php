<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Pengajaran (Guru Ampu)" subtitle="Tetapkan mata pelajaran dan kelas yang diajar oleh guru">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Penetapan
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

    {{-- Guru Ampu Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Daftar Penugasan</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari guru, kelas, mapel..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        {{-- Guru Ampu Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th style="width: 120px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guruAmpuList as $guruAmpu)
                        <tr wire:key="guru-ampu-{{ $guruAmpu->id_guru_ampu }}">
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $guruAmpu->guru->nama_guru ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $guruAmpu->mataPelajaran->nama_mapel ?? '-' }}</div>
                                <small class="text-muted">{{ $guruAmpu->mataPelajaran->kode_mapel ?? '-' }}</small>
                            </td>
                            <td>
                                <x-ui.badge variant="info" icon="fas fa-chalkboard">{{ $guruAmpu->kelas->nama_kelas ?? '-' }}</x-ui.badge>
                            </td>
                            <td>
                                <div class="text-muted">{{ $guruAmpu->tahunAjaran->nama_tahun ?? '-' }} (Sem {{ $guruAmpu->tahunAjaran->semester ?? '-' }})</div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-edit wire:click="openEditModal({{ $guruAmpu->id_guru_ampu }})" tooltip="Edit Penugasan" />
                                    <x-ui.btn-delete wire:click="confirmDelete({{ $guruAmpu->id_guru_ampu }})" tooltip="Hapus Penugasan" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-chalkboard-teacher mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data penugasan guru ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($guruAmpuList->hasPages())
            <div class="d-flex justify-content-end mt-4">
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
                        {{ $editingGuruAmpuId ? 'Edit Penugasan' : 'Tambah Penugasan Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    @if($errors->has('id_guru'))
                        <div class="alert alert-danger p-2 mb-3" style="font-size: 0.875rem;">
                            {{ $errors->first('id_guru') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="id_guru" class="form-label">Guru <span style="color: var(--danger-color);">*</span></label>
                        <select class="form-control form-select @error('id_guru') is-invalid @enderror" id="id_guru" wire:model="id_guru">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id_guru }}">{{ $guru->nama_guru }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_mata_pelajaran" class="form-label">Mata Pelajaran <span style="color: var(--danger-color);">*</span></label>
                        <select class="form-control form-select @error('id_mata_pelajaran') is-invalid @enderror" id="id_mata_pelajaran" wire:model="id_mata_pelajaran">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id_mata_pelajaran }}">{{ $mapel->kode_mapel }} - {{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_kelas" class="form-label">Kelas <span style="color: var(--danger-color);">*</span></label>
                        <select class="form-control form-select @error('id_kelas') is-invalid @enderror" id="id_kelas" wire:model="id_kelas">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_tahun_ajaran" class="form-label">Tahun Ajaran <span style="color: var(--danger-color);">*</span></label>
                        <select class="form-control form-select @error('id_tahun_ajaran') is-invalid @enderror" id="id_tahun_ajaran" wire:model="id_tahun_ajaran">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->nama_tahun }} (Semester {{ $ta->semester }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <x-ui.button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-ui.button>
                        <x-ui.button type="submit" variant="primary">
                            {{ $editingGuruAmpuId ? 'Simpan Perubahan' : 'Simpan' }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-modal
        :show="$showDeleteModal"
        title="Konfirmasi Hapus"
        message="Apakah Anda yakin ingin menghapus penugasan ini? Tindakan ini tidak dapat dibatalkan."
        on-confirm="deleteGuruAmpu"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Penugasan
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
