<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Manajemen Data Siswa" subtitle="Kelola seluruh akun dan informasi dasar siswa">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-user-plus" wire:click="openCreateModal">
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

    {{-- Users Table Card --}}
    <div class="modern-card">
        {{-- Advanced Filters UX --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Saring berdasarkan Kelas</label>
                <select class="form-select" wire:model.live="filter_kelas">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label text-muted small fw-bold">Pencarian Siswa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0 bg-transparent" placeholder="Ketik nama atau NISN siswa..."
                        wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Profil Siswa</th>
                        <th>NISN</th>
                        <th>Kelas / Rombel</th>
                        <th>Orang Tua</th>
                        <th style="width: 120px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswas as $siswa)
                        <tr wire:key="siswa-{{ $siswa->id_siswa }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($siswa->avatar)
                                        <img src="{{ Storage::url($siswa->avatar) }}" alt="Avatar" class="me-3 rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="user-avatar bg-info-subtle text-info me-3 border border-info-subtle rounded-circle d-flex align-items-center justify-content-center fs-4" style="width: 45px; height: 45px;">
                                            {{ strtoupper(substr($siswa->nama_siswa ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-primary">{{ $siswa->nama_siswa }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><i class="fas fa-id-badge text-muted me-1"></i> {{ $siswa->nisn }}</span>
                            </td>
                            <td>
                                <x-ui.badge variant="info" icon="fas fa-door-open">{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</x-ui.badge>
                            </td>
                            <td>
                                @if($siswa->orangTua)
                                    <span class="badge bg-light text-dark border"><i class="fas fa-users text-primary me-1"></i> {{ $siswa->orangTua->nama_orang_tua }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-view wire:click="openViewModal({{ $siswa->id_siswa }})" tooltip="Lihat Detail Siswa" />
                                    <x-ui.btn-edit wire:click="openEditModal({{ $siswa->id_siswa }})" tooltip="Edit Siswa" />
                                    <x-ui.btn-delete wire:click="confirmDelete({{ $siswa->id_siswa }})" tooltip="Hapus Siswa" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users-slash mb-3 text-info opacity-50" style="font-size: 3rem;"></i>
                                    <h5>Siswa Tidak Ditemukan</h5>
                                    <p class="mb-0">Tidak ada data siswa yang cocok dengan filter atau kata kunci pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($siswas->hasPages())
            <div class="mt-4">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 650px;">
                <div class="modal-header-custom border-bottom pb-3 mb-4">
                    <h5 class="modal-title-custom fw-bold">
                        <i class="fas {{ $editingSiswaId ? 'fa-user-edit text-warning' : 'fa-user-plus text-primary' }} me-2"></i>
                        {{ $editingSiswaId ? 'Edit Profil Siswa' : 'Registrasi Siswa Baru' }}
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

                    <div class="row g-3">
                        {{-- Nama Siswa --}}
                        <div class="col-md-12">
                            <label for="nama_siswa" class="form-label text-muted fw-bold small">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_siswa') is-invalid @enderror" id="nama_siswa"
                                wire:model="nama_siswa" placeholder="Cth: Ahmad Maulana">
                            @error('nama_siswa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NISN --}}
                        <div class="col-md-6">
                            <label for="nisn" class="form-label text-muted fw-bold small">Nomor Induk Siswa Nasional (NISN) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn"
                                wire:model="nisn" placeholder="10 Digit Angka NISN">
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kelas --}}
                        <div class="col-md-6">
                            <label for="id_kelas" class="form-label text-muted fw-bold small">Penempatan Kelas <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_kelas') is-invalid @enderror" id="id_kelas" wire:model="id_kelas">
                                <option value="">-- Pilih Kelas Saat Ini --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                            @error('id_kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Orang Tua --}}
                        <div class="col-md-12">
                            <label for="id_orang_tua" class="form-label text-muted fw-bold small">Pilih Orang Tua (Opsional)</label>
                            <select class="form-select @error('id_orang_tua') is-invalid @enderror" id="id_orang_tua" wire:model="id_orang_tua">
                                <option value="">-- Tidak Terhubung --</option>
                                @foreach($orangTuaList as $ot)
                                    <option value="{{ $ot->id_orang_tua }}">{{ $ot->nama_orang_tua }} - NIK: {{ $ot->nik }}</option>
                                @endforeach
                            </select>
                            @error('id_orang_tua')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password Section --}}
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2"><i class="fas fa-lock me-2"></i>Kredensial Login</h6>
                            @if ($editingSiswaId)
                                <div class="alert alert-warning border-0 bg-warning-subtle small py-2 mb-3">
                                    <i class="fas fa-info-circle me-1"></i> Biarkan kosong jika Anda <strong>tidak ingin mengubah</strong> kata sandi siswa ini.
                                </div>
                            @endif
                        </div>

                        <div class="col-md-{{ $editingSiswaId ? '12' : '6' }}">
                            <label for="password" class="form-label text-muted fw-bold small">Kata Sandi @if(!$editingSiswaId)<span class="text-danger">*</span>@endif</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                wire:model="password"
                                placeholder="{{ $editingSiswaId ? 'Kata sandi baru (opsional, tanpa validasi)' : 'Minimal 8 karakter' }}">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(!$editingSiswaId)
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label text-muted fw-bold small">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation"
                                wire:model="password_confirmation" placeholder="Ketik ulang kata sandi">
                        </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                        <x-ui.button type="button" variant="outline" wire:click="closeModal">
                            <i class="fas fa-times me-1"></i> Batal
                        </x-ui.button>
                        <x-ui.button type="submit" variant="primary">
                            <i class="fas fa-save me-1"></i> {{ $editingSiswaId ? 'Simpan Perubahan' : 'Daftarkan Siswa' }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- View Detail Modal --}}
    @if ($showViewModal && $viewingSiswa)
        <div class="modal-backdrop-custom" wire:click.self="closeViewModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 500px;">
                <div class="modal-header-custom border-bottom pb-3 mb-4">
                    <h5 class="modal-title-custom fw-bold">
                        <i class="fas fa-user-graduate text-info me-2"></i> Detail Profil Siswa
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeViewModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="text-center mb-4">
                    @if($viewingSiswa && $viewingSiswa->avatar)
                        <img src="{{ Storage::url($viewingSiswa->avatar) }}" alt="Avatar" class="mx-auto mb-3 shadow-sm rounded-circle d-flex align-items-center justify-content-center border border-4 border-info" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="user-avatar bg-info text-white mx-auto mb-3 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            {{ strtoupper(substr($viewingSiswa->nama_siswa ?? '?', 0, 1)) }}
                        </div>
                    @endif
                    <h4 class="mb-1 text-primary fw-bold">{{ $viewingSiswa->nama_siswa ?? '-' }}</h4>
                    <x-ui.badge variant="secondary" icon="fas fa-id-badge" class="mt-2">{{ $viewingSiswa->nisn ?? '-' }}</x-ui.badge>
                </div>

                <div class="modern-card bg-light border-0 p-3 mb-3">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-medium w-50"><i class="fas fa-door-open me-2 text-info"></i> Rombongan Belajar</th>
                                <td class="text-end">
                                    <span class="fw-bold">{{ $viewingSiswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium"><i class="fas fa-users me-2 text-primary"></i> Orang Tua</th>
                                <td class="text-end">
                                    <span class="fw-bold">{{ $viewingSiswa->orangTua->nama_orang_tua ?? 'Belum terhubung' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium"><i class="fas fa-calendar-alt me-2 text-info"></i> Tanggal Terdaftar</th>
                                <td class="text-end">
                                    <span class="fw-semibold text-muted">{{ $viewingSiswa->created_at ? $viewingSiswa->created_at->format('d M Y') : '-' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top gap-2">
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
        message="Apakah Anda yakin ingin menghapus akun siswa ini secara permanen? Semua riwayat kehadiran siswa ini akan ikut terpengaruh."
        on-confirm="deleteSiswa"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-user-minus"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Ya, Hapus Siswa
        </x-slot:confirmButton>
    </x-ui.confirm-modal>
</div>
