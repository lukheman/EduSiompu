<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Guru" subtitle="Kelola semua data guru dalam sistem">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Guru
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

    {{-- Guru Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari guru..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        {{-- Guru Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th style="width: 120px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gurus as $guru)
                        <tr wire:key="guru-{{ $guru->id_guru }}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($guru->avatar)
                                        <img src="{{ Storage::url($guru->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        @php
                                            $words = explode(' ', $guru->nama_guru);
                                            $initials = '';
                                            foreach ($words as $word) {
                                                $initials .= strtoupper(substr($word, 0, 1));
                                            }
                                            $initials = substr($initials, 0, 2);
                                        @endphp
                                        <div class="user-avatar">{{ $initials }}</div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold" style="color: var(--text-primary);">{{ $guru->nama_guru }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $guru->nip }}</div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-view wire:click="openViewModal({{ $guru->id_guru }})" tooltip="Lihat Detail Guru" />
                                    <x-ui.btn-edit wire:click="openEditModal({{ $guru->id_guru }})" tooltip="Edit Guru" />
                                    <x-ui.btn-delete wire:click="confirmDelete({{ $guru->id_guru }})" tooltip="Hapus Guru" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-chalkboard-teacher mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data guru ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($gurus->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $gurus->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop>
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingGuruId ? 'Edit Guru' : 'Tambah Guru Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="mb-4 text-center">
                        <div class="position-relative d-inline-block mb-2">
                            @if ($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" class="rounded-circle border border-3 border-primary" style="width: 100px; height: 100px; object-fit: cover;">
                            @elseif ($currentAvatar)
                                <img src="{{ Storage::url($currentAvatar) }}" class="rounded-circle border border-3 border-primary" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="user-avatar mx-auto bg-light text-secondary border border-3 border-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                    <i class="fas fa-camera"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <label for="avatar" class="btn btn-sm btn-outline-primary rounded-pill cursor-pointer">
                                <i class="fas fa-upload me-1"></i> Pilih Foto Profil
                            </label>
                            <input type="file" class="d-none" id="avatar" wire:model="avatar" accept="image/*">
                        </div>
                        <div wire:loading wire:target="avatar" class="small text-muted mt-2">Mengunggah...</div>
                        @error('avatar') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nip" class="form-label">NIP <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip"
                            wire:model="nip" placeholder="Masukkan NIP">
                        @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama_guru" class="form-label">Nama Lengkap <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('nama_guru') is-invalid @enderror" id="nama_guru"
                            wire:model="nama_guru" placeholder="Masukkan nama lengkap guru">
                        @error('nama_guru')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Kata Sandi
                            @if (!$editingGuruId)
                                <span style="color: var(--danger-color);">*</span>
                            @else
                                <small class="text-muted">(biarkan kosong jika tidak ingin diubah)</small>
                            @endif
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            wire:model="password"
                            placeholder="{{ $editingGuruId ? 'Masukkan kata sandi baru' : 'Masukkan kata sandi' }}">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            wire:model="password_confirmation" placeholder="Konfirmasi kata sandi">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <x-ui.button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-ui.button>
                        <x-ui.button type="submit" variant="primary">
                            {{ $editingGuruId ? 'Simpan Perubahan' : 'Tambah' }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- View Detail Modal --}}
    @if ($showViewModal && $viewingGuru)
        <div class="modal-backdrop-custom" wire:click.self="closeViewModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 500px;">
                <div class="modal-header-custom border-bottom pb-3 mb-4">
                    <h5 class="modal-title-custom fw-bold">
                        <i class="fas fa-chalkboard-teacher text-info me-2"></i> Detail Profil Guru
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeViewModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="text-center mb-4">
                    @if($viewingGuru && $viewingGuru->avatar)
                        <img src="{{ Storage::url($viewingGuru->avatar) }}" alt="Avatar" class="mx-auto mb-3 shadow-sm rounded-circle d-flex align-items-center justify-content-center border border-4 border-info" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        @php
                            $words = explode(' ', $viewingGuru->nama_guru ?? '?');
                            $initials = '';
                            foreach ($words as $word) {
                                $initials .= strtoupper(substr($word, 0, 1));
                            }
                            $initials = substr($initials, 0, 2);
                        @endphp
                        <div class="user-avatar bg-info text-white mx-auto mb-3 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            {{ $initials }}
                        </div>
                    @endif
                    <h4 class="mb-1 text-primary fw-bold">{{ $viewingGuru->nama_guru ?? '-' }}</h4>
                    <x-ui.badge variant="secondary" icon="fas fa-id-card" class="mt-2">{{ $viewingGuru->nip ?? '-' }}</x-ui.badge>
                </div>

                <div class="modern-card bg-light border-0 p-3 mb-3">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-medium"><i class="fas fa-calendar-alt me-2 text-info"></i> Tanggal Terdaftar</th>
                                <td class="text-end">
                                    <span class="fw-semibold text-muted">{{ $viewingGuru->created_at ? $viewingGuru->created_at->format('d M Y') : '-' }}</span>
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
        message="Apakah Anda yakin ingin menghapus profil guru ini? Tindakan ini tidak dapat dibatalkan."
        on-confirm="deleteGuru"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Guru
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
