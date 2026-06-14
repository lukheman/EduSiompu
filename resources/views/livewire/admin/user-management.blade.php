<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Admin" subtitle="Kelola semua admin dalam sistem">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Admin
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

    {{-- Users Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Semua Admin</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari admin..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        {{-- Users Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Email</th>
                        <th>Dibuat Pada</th>
                        <th>Status</th>
                        <th style="width: 120px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar">{{ $user->initials() }}</div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--text-primary);">{{ $user->name }}</div>
                                        <small class="text-muted">ID: {{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="color: var(--text-secondary);">{{ $user->email }}</td>
                            <td class="text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($user->email_verified_at)
                                    <x-ui.badge variant="success" icon="fas fa-check-circle">Terverifikasi</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning" icon="fas fa-clock">Tertunda</x-ui.badge>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-edit wire:click="openEditModal({{ $user->id_admin }})" tooltip="Edit admin" />
                                    <x-ui.btn-delete wire:click="confirmDelete({{ $user->id_admin }})" tooltip="Hapus admin" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data admin ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop>
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingUserId ? 'Edit Admin' : 'Tambah Admin Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            wire:model="name" placeholder="Masukkan nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span
                                style="color: var(--danger-color);">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            wire:model="email" placeholder="Enter email address">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Kata Sandi
                            @if (!$editingUserId)
                                <span style="color: var(--danger-color);">*</span>
                            @else
                                <small class="text-muted">(biarkan kosong jika tidak ingin diubah)</small>
                            @endif
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            wire:model="password"
                            placeholder="{{ $editingUserId ? 'Masukkan kata sandi baru' : 'Masukkan kata sandi' }}">
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
                            {{ $editingUserId ? 'Simpan Perubahan' : 'Tambah Admin' }}
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
        message="Apakah Anda yakin ingin menghapus profil admin ini? Tindakan ini tidak dapat dibatalkan."
        on-confirm="deleteUser"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Admin
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
