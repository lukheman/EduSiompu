<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Mata Pelajaran" subtitle="Kelola semua data mata pelajaran dalam sistem">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Mata Pelajaran
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-ui.toast variant="success">
            {{ session('success') }}
        </x-ui.toast>
    @endif

    @if (session('error'))
        <x-ui.toast variant="danger">
            {{ session('error') }}
        </x-ui.toast>
    @endif

    {{-- Mata Pelajaran Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Semua Mata Pelajaran</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari mata pelajaran..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        {{-- Mata Pelajaran Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th style="width: 120px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mapelList as $mapel)
                        <tr wire:key="mapel-{{ $mapel->id_mata_pelajaran }}">
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $mapel->kode_mapel }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $mapel->nama_mapel }}</div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-edit wire:click="openEditModal({{ $mapel->id_mata_pelajaran }})" tooltip="Edit Mata Pelajaran" />
                                    <x-ui.btn-delete wire:click="confirmDelete({{ $mapel->id_mata_pelajaran }})" tooltip="Hapus Mata Pelajaran" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-book mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data mata pelajaran ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($mapelList->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $mapelList->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop>
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingMapelId ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="mb-3">
                        <label for="kode_mapel" class="form-label">Kode Mata Pelajaran <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('kode_mapel') is-invalid @enderror" id="kode_mapel"
                            wire:model="kode_mapel" placeholder="Masukkan kode mata pelajaran (Contoh: M01)">
                        @error('kode_mapel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama_mapel" class="form-label">Nama Mata Pelajaran <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('nama_mapel') is-invalid @enderror" id="nama_mapel"
                            wire:model="nama_mapel" placeholder="Masukkan nama mata pelajaran">
                        @error('nama_mapel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <x-ui.button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-ui.button>
                        <x-ui.button type="submit" variant="primary">
                            {{ $editingMapelId ? 'Simpan Perubahan' : 'Tambah' }}
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
        message="Apakah Anda yakin ingin menghapus mata pelajaran ini? Tindakan ini tidak dapat dibatalkan."
        on-confirm="deleteMapel"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Mapel
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
