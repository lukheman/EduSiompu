<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Tahun Ajaran" subtitle="Kelola data tahun ajaran dan semester aktif">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Tahun Ajaran
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-ui.toast variant="success">
            {{ session('success') }}
        </x-ui.toast>
    @endif

    {{-- Data Table Card --}}
    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Daftar Tahun Ajaran</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari tahun ajaran..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th style="width: 150px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tahunAjarans as $tahun)
                        <tr wire:key="tahun-{{ $tahun->id_tahun_ajaran }}">
                            <td class="fw-semibold">{{ $tahun->nama_tahun }}</td>
                            <td>
                                @if($tahun->semester === 'ganjil')
                                    <x-ui.badge variant="info" icon="fas fa-sun">Ganjil</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning" icon="fas fa-snowflake">Genap</x-ui.badge>
                                @endif
                            </td>
                            <td>
                                @if($tahun->status_aktif)
                                    <x-ui.badge variant="success" icon="fas fa-check-circle">Aktif</x-ui.badge>
                                @else
                                    <x-ui.button variant="outline" size="sm" wire:click="toggleStatus({{ $tahun->id_tahun_ajaran }})" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                        Set Aktif
                                    </x-ui.button>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-edit wire:click="openEditModal({{ $tahun->id_tahun_ajaran }})" tooltip="Edit Tahun Ajaran" />
                                    <x-ui.btn-delete wire:click="confirmDelete({{ $tahun->id_tahun_ajaran }})" tooltip="Hapus Tahun Ajaran" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-alt mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data tahun ajaran ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tahunAjarans->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $tahunAjarans->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop>
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingId ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="mb-3">
                        <label for="nama_tahun" class="form-label">Tahun Ajaran <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('nama_tahun') is-invalid @enderror" id="nama_tahun"
                            wire:model="nama_tahun" placeholder="Contoh: 2026/2027">
                        @error('nama_tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester <span style="color: var(--danger-color);">*</span></label>
                        <select class="form-control form-select @error('semester') is-invalid @enderror" id="semester" wire:model="semester">
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                        @error('semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="status_aktif" wire:model="status_aktif">
                            <label class="form-check-label ms-2" for="status_aktif">Jadikan Tahun Ajaran Aktif</label>
                        </div>
                        <small class="text-muted d-block mt-1">Hanya satu tahun ajaran yang dapat aktif dalam satu waktu. Mengaktifkan ini akan menonaktifkan tahun ajaran lainnya.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <x-ui.button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-ui.button>
                        <x-ui.button type="submit" variant="primary">
                            {{ $editingId ? 'Simpan Perubahan' : 'Tambah Data' }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    <x-ui.confirm-modal
        :show="$showDeleteModal"
        title="Konfirmasi Hapus"
        message="Apakah Anda yakin ingin menghapus data tahun ajaran ini? Hal ini mungkin mempengaruhi data yang terhubung dengannya."
        on-confirm="deleteData"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Data
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
