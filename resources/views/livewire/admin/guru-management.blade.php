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
        <x-ui.alert variant="success" title="Berhasil!" class="mb-4">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert variant="danger" title="Gagal!" class="mb-4">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    {{-- Guru Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Semua Guru</h5>
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
                        <th>NIP</th>
                        <th>Nama Guru</th>
                        <th>Didaftarkan Pada</th>
                        <th style="width: 120px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gurus as $guru)
                        <tr wire:key="guru-{{ $guru->id_guru }}">
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $guru->nip }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        $words = explode(' ', $guru->nama_guru);
                                        $initials = '';
                                        foreach ($words as $word) {
                                            $initials .= strtoupper(substr($word, 0, 1));
                                        }
                                        $initials = substr($initials, 0, 2);
                                    @endphp
                                    <div class="user-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--text-primary);">{{ $guru->nama_guru }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $guru->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="action-btn action-btn-edit" wire:click="openEditModal({{ $guru->id_guru }})"
                                        title="Edit Guru">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" wire:click="confirmDelete({{ $guru->id_guru }})"
                                        title="Hapus Guru">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
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
