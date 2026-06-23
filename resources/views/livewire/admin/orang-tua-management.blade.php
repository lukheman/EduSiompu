<div>
    <x-layout.page-header title="Manajemen Orang Tua">
        <x-slot:actions>
            <x-ui.button wire:click="openModal" variant="primary" icon="fas fa-plus">
                Tambah Orang Tua
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    @if (session('success'))
        <x-ui.toast variant="success">
            {{ session('success') }}
        </x-ui.toast>
    @endif

    <x-layout.modern-card>
        <div class="row mb-4">
            <div class="col-md-4">
                <x-form.input wire:model.live="search" type="text" placeholder="Cari nama atau NIK..." icon="fas fa-search" />
            </div>
        </div>

        <x-layout.table>
            <x-slot:head>
                <tr>
                    <th>NIK</th>
                    <th>Nama Orang Tua</th>
                    <th>No HP</th>
                    <th>Jumlah Anak</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </x-slot:head>

            @forelse ($orangTuas as $ot)
                <tr class="align-middle">
                    <td>
                        <x-ui.badge variant="secondary" icon="fas fa-id-card">
                            {{ $ot->nik }}
                        </x-ui.badge>
                    </td>
                    <td style="font-weight: 500;">{{ $ot->nama_orang_tua }}</td>
                    <td>{{ $ot->no_hp ?? '-' }}</td>
                    <td>
                        <x-ui.badge variant="info">
                            {{ $ot->siswa_count }} Anak
                        </x-ui.badge>
                    </td>
                    <td class="text-end">
                        <x-ui.btn-edit wire:click="edit({{ $ot->id_orang_tua }})" />
                        <x-ui.btn-delete wire:click="confirmDelete({{ $ot->id_orang_tua }})" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-ui.empty-state icon="fas fa-users-slash" title="Belum ada data" description="Belum ada data orang tua yang ditambahkan." />
                    </td>
                </tr>
            @endforelse
        </x-layout.table>
        
        <div class="mt-4">
            {{ $orangTuas->links() }}
        </div>
    </x-layout.modern-card>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal-backdrop-custom">
            <div class="modal-content-custom">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">{{ $isEditing ? 'Edit Orang Tua' : 'Tambah Orang Tua' }}</h5>
                    <button wire:click="closeModal" class="modal-close-btn">&times;</button>
                </div>
                <form wire:submit="store">
                    <x-form.input label="NIK" wire:model="nik" placeholder="16 Digit NIK" required="true" />
                    <x-form.input label="Nama Orang Tua" wire:model="nama_orang_tua" placeholder="Masukkan nama lengkap" required="true" />
                    <x-form.input label="No HP" wire:model="no_hp" placeholder="Masukkan nomor HP" />
                    
                    <div class="mb-4">
                        <x-form.input 
                            type="password" 
                            label="Password" 
                            wire:model="password" 
                            placeholder="Masukkan password" 
                            :required="!$isEditing" 
                            hint="{{ $isEditing ? 'Kosongkan jika tidak ingin diubah' : '' }}" 
                        />
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <x-ui.button type="button" wire:click="closeModal" variant="outline">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">Simpan</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    <x-ui.confirm-modal 
        :show="$showDeleteModal" 
        title="Hapus Data" 
        message="Apakah Anda yakin ingin menghapus data orang tua ini? Data yang dihapus tidak dapat dikembalikan."
        confirmText="Ya, Hapus"
        cancelText="Batal"
        onConfirm="delete"
        onCancel="$set('showDeleteModal', false)"
    />
</div>
