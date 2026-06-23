<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Profil Guru" subtitle="Kelola informasi pribadi dan keamanan akun Anda" />

    <div class="row g-4">
        {{-- Profile Info Card --}}
        <div class="col-md-4">
            <div class="modern-card text-center">
                @php
                    $guru = Auth::guard('guru')->user();
                @endphp
                @if($guru && $guru->avatar)
                    <img src="{{ Storage::url($guru->avatar) }}" alt="Avatar" class="rounded-circle mb-3 border border-4 border-primary" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="user-avatar bg-primary-subtle text-primary mx-auto mb-3 border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; font-size: 4rem;">
                        {{ strtoupper(substr($nama_guru ?? 'G', 0, 1)) }}
                    </div>
                @endif
                <h4 class="fw-bold text-primary mb-1">{{ $nama_guru }}</h4>
                <div class="text-muted mb-3"><i class="fas fa-id-card me-2"></i>NIP: {{ $nip }}</div>

                <div class="alert alert-info bg-info-subtle border-0 text-start mt-4 mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Akun Guru</strong><br>
                    <small>Anda memiliki akses untuk mengelola KBM dan jadwal mengajar.</small>
                </div>
            </div>
        </div>

        {{-- Forms Column --}}
        <div class="col-md-8">
            {{-- Update Profile Form --}}
            <div class="modern-card mb-4">
                <h5 class="fw-bold text-primary mb-4 border-bottom pb-3"><i class="fas fa-user-edit me-2"></i>Informasi Dasar</h5>

                @if (session('success_profile'))
                    <x-ui.toast variant="success">
                        {{ session('success_profile') }}
                    </x-ui.toast>
    @endif

                <form wire:submit="updateProfile">
                    <div class="mb-3">
                        <label for="nama_guru" class="form-label text-muted fw-bold small">Nama Lengkap</label>
                        <input type="text" class="form-control @error('nama_guru') is-invalid @enderror" id="nama_guru" wire:model="nama_guru">
                        @error('nama_guru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="nip" class="form-label text-muted fw-bold small">Nomor Induk Pegawai (NIP)</label>
                        <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" wire:model="nip">
                        @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <x-ui.button type="submit" variant="primary">
                            <i class="fas fa-save me-1"></i> Simpan Profil
                        </x-ui.button>
                    </div>
                </form>
            </div>

            {{-- Update Password Form --}}
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold text-danger mb-0"><i class="fas fa-lock me-2"></i>Keamanan Akun</h5>
                    <x-ui.button type="button" variant="{{ $showPasswordSection ? 'danger' : 'outline' }}" size="sm"
                        wire:click="togglePasswordSection">
                        {{ $showPasswordSection ? 'Batal' : 'Ubah Password' }}
                    </x-ui.button>

                </div>

                @if (session('success_password'))
                    <x-ui.toast variant="success">
                        {{ session('success_password') }}
                    </x-ui.toast>
    @endif

                @if($showPasswordSection)
                    <form wire:submit="updatePassword">
                        <div class="mb-3">
                            <label for="current_password" class="form-label text-muted fw-bold small">Kata Sandi Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" wire:model="current_password">
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label text-muted fw-bold small">Kata Sandi Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model="password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label text-muted fw-bold small">Konfirmasi Sandi Baru</label>
                                <input type="password" class="form-control" id="password_confirmation" wire:model="password_confirmation">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <x-ui.button type="submit" variant="danger">
                                <i class="fas fa-key me-1"></i> Perbarui Kata Sandi
                            </x-ui.button>
                        </div>
                    </form>
                @else
                    <p class="text-muted mb-0">Klik tombol di atas jika Anda ingin mengganti kata sandi untuk akun ini. Jaga kerahasiaan kata sandi Anda untuk keamanan data nilai dan absensi siswa.</p>
                @endif
            </div>
        </div>
    </div>
</div>
