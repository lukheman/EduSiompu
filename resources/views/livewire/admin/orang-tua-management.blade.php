<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="preview-title mb-0">Manajemen Orang Tua</h2>
        <button wire:click="openModal" class="btn btn-modern btn-primary-modern">
            <i class="fas fa-plus me-2"></i>Tambah Orang Tua
        </button>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-modern alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle text-success fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="modern-card">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text border-end-0 bg-transparent">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input wire:model.live="search" type="text" class="form-control border-start-0" placeholder="Cari nama atau NIK...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama Orang Tua</th>
                        <th>No HP</th>
                        <th>Jumlah Anak</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orangTuas as $ot)
                        <tr>
                            <td><span class="badge-modern bg-light text-dark"><i class="fas fa-id-card text-primary me-1"></i>{{ $ot->nik }}</span></td>
                            <td>{{ $ot->nama_orang_tua }}</td>
                            <td>{{ $ot->no_hp ?? '-' }}</td>
                            <td>{{ $ot->siswa_count }}</td>
                            <td class="text-end">
                                <button wire:click="edit({{ $ot->id_orang_tua }})" class="action-btn action-btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $ot->id_orang_tua }})" class="action-btn action-btn-delete" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fs-2 mb-3 d-block text-muted opacity-50"></i>
                                Belum ada data orang tua
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orangTuas->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal-backdrop-custom">
            <div class="modal-content-custom">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">{{ $isEditing ? 'Edit Orang Tua' : 'Tambah Orang Tua' }}</h5>
                    <button wire:click="closeModal" class="modal-close-btn">&times;</button>
                </div>
                <form wire:submit="store">
                    <div class="mb-3">
                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                        <input type="text" wire:model="nik" class="form-control @error('nik') is-invalid @enderror" placeholder="16 Digit NIK">
                        @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Orang Tua <span class="text-danger">*</span></label>
                        <input type="text" wire:model="nama_orang_tua" class="form-control @error('nama_orang_tua') is-invalid @enderror" placeholder="Masukkan nama lengkap">
                        @error('nama_orang_tua') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" wire:model="no_hp" class="form-control @error('no_hp') is-invalid @enderror" placeholder="Masukkan nomor HP">
                        @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password {!! $isEditing ? '<small class="text-muted">(Kosongkan jika tidak ingin diubah)</small>' : '<span class="text-danger">*</span>' !!}</label>
                        <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" wire:click="closeModal" class="btn btn-modern btn-outline-secondary">Batal</button>
                        <button type="submit" class="btn btn-modern btn-primary-modern">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop-custom">
            <div class="modal-content-custom" style="max-width: 400px;">
                <div class="text-center mb-4">
                    <i class="fas fa-exclamation-triangle text-danger fs-1 mb-3"></i>
                    <h5 class="modal-title-custom">Hapus Data</h5>
                    <p class="text-muted mb-0">Apakah Anda yakin ingin menghapus data orang tua ini? Data yang dihapus tidak dapat dikembalikan.</p>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)" class="btn btn-modern btn-outline-secondary px-4">Batal</button>
                    <button wire:click="delete" class="btn btn-modern btn-danger px-4">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
