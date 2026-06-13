<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Materi" subtitle="Kelola senarai bahan pengajaran dan materi pembelajaran">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Muat Naik Materi
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

    {{-- Materi Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Semua Materi</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari materi, kelas, mapel..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        {{-- Materi Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Judul Materi</th>
                        <th>Penetapan Guru & Subjek</th>
                        <th>Format Fail</th>
                        <th>Dimuat Naik</th>
                        <th style="width: 120px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materiList as $materi)
                        <tr wire:key="materi-{{ $materi->id_materi }}">
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $materi->judul_materi }}</div>
                                @if($materi->file_path)
                                    <a href="{{ Storage::url($materi->file_path) }}" target="_blank" class="text-primary" style="font-size: 0.85rem; text-decoration: none;">
                                        <i class="fas fa-external-link-alt me-1"></i>Lihat Fail
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">
                                    {{ $materi->guruAmpu->mataPelajaran->nama_mapel ?? 'N/A' }}
                                </div>
                                <div class="text-muted" style="font-size: 0.85rem;">
                                    Kelas: {{ $materi->guruAmpu->kelas->nama_kelas ?? 'N/A' }} | Guru: {{ $materi->guruAmpu->guru->nama_guru ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <x-ui.badge variant="secondary" icon="fas fa-file">.{{ strtoupper($materi->jenis_file) }}</x-ui.badge>
                            </td>
                            <td class="text-muted">{{ $materi->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="action-btn action-btn-edit" wire:click="openEditModal({{ $materi->id_materi }})"
                                        title="Edit Materi">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" wire:click="confirmDelete({{ $materi->id_materi }})"
                                        title="Hapus Materi">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-file-alt mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data materi ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($materiList->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $materiList->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop>
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingMateriId ? 'Edit Materi' : 'Unggah Materi Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="mb-3">
                        <label for="id_guru_ampu" class="form-label">Penetapan (Guru - Mapel - Kelas) <span style="color: var(--danger-color);">*</span></label>
                        <select class="form-control form-select @error('id_guru_ampu') is-invalid @enderror" id="id_guru_ampu" wire:model="id_guru_ampu">
                            <option value="">-- Pilih Penetapan --</option>
                            @foreach($guruAmpuOptions as $ampu)
                                <option value="{{ $ampu->id_guru_ampu }}">
                                    {{ $ampu->mataPelajaran->nama_mapel ?? '' }} | {{ $ampu->kelas->nama_kelas ?? '' }} | {{ $ampu->guru->nama_guru ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_guru_ampu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="judul_materi" class="form-label">Judul Materi <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('judul_materi') is-invalid @enderror" id="judul_materi"
                            wire:model="judul_materi" placeholder="Contoh: Nota Bab 1">
                        @error('judul_materi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="file_materi" class="form-label">Fail Materi
                            @if (!$editingMateriId)
                                <span style="color: var(--danger-color);">*</span>
                            @else
                                <small class="text-muted">(Biarkan kosong untuk mengekalkan fail lama)</small>
                            @endif
                        </label>
                        <input type="file" class="form-control @error('file_materi') is-invalid @enderror" id="file_materi"
                            wire:model="file_materi">
                        <div wire:loading wire:target="file_materi" class="text-primary mt-1" style="font-size: 0.85rem;">
                            <i class="fas fa-spinner fa-spin me-1"></i>Sedang mengunggah...
                        </div>
                        @error('file_materi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <x-ui.button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-ui.button>
                        <x-ui.button type="submit" variant="primary">
                            <span wire:loading.remove wire:target="save">
                                {{ $editingMateriId ? 'Simpan Perubahan' : 'Unggah' }}
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin"></i> Menyimpan...
                            </span>
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
        message="Apakah Anda yakin ingin menghapus materi ini? File yang terkait juga akan dihapus secara permanen."
        on-confirm="deleteMateri"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Materi
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
