<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Siswa" subtitle="Kelola semua data siswa">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Siswa
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-ui.alert variant="success" title="Sukses!" class="mb-4">
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
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Daftar Siswa</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari siswa..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        {{-- Users Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Terdaftar</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswas as $siswa)
                        <tr wire:key="siswa-{{ $siswa->id_siswa }}">
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $siswa->nama_siswa }}</div>
                                <small class="text-muted">ID: {{ $siswa->id_siswa }}</small>
                            </td>
                            <td style="color: var(--text-secondary);">{{ $siswa->nisn }}</td>
                            <td>
                                <x-ui.badge variant="info">{{ $siswa->kelas->nama_kelas ?? '-' }}</x-ui.badge>
                            </td>
                            <td class="text-muted">{{ $siswa->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="action-btn action-btn-edit" wire:click="openEditModal({{ $siswa->id_siswa }})"
                                        title="Edit Siswa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" wire:click="confirmDelete({{ $siswa->id_siswa }})"
                                        title="Hapus Siswa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-user-graduate mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data siswa ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($siswas->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop>
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingSiswaId ? 'Edit Siswa' : 'Tambah Siswa Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="mb-3">
                        <label for="nama_siswa" class="form-label">Nama Siswa <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('nama_siswa') is-invalid @enderror" id="nama_siswa"
                            wire:model="nama_siswa" placeholder="Masukkan nama siswa">
                        @error('nama_siswa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nisn" class="form-label">NISN <span
                                style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn"
                            wire:model="nisn" placeholder="Masukkan NISN">
                        @error('nisn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="id_kelas" class="form-label">Kelas <span style="color: var(--danger-color);">*</span></label>
                        <select class="form-select @error('id_kelas') is-invalid @enderror" id="id_kelas" wire:model="id_kelas">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('id_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Kata Sandi
                            @if (!$editingSiswaId)
                                <span style="color: var(--danger-color);">*</span>
                            @else
                                <small class="text-muted">(biarkan kosong jika tidak ingin diubah)</small>
                            @endif
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            wire:model="password"
                            placeholder="{{ $editingSiswaId ? 'Masukkan kata sandi baru' : 'Masukkan kata sandi' }}">
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
                            {{ $editingSiswaId ? 'Simpan Perubahan' : 'Tambah' }}
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
        message="Apakah Anda yakin ingin menghapus profil siswa ini? Tindakan ini tidak dapat dibatalkan."
        on-confirm="deleteSiswa"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Siswa
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
