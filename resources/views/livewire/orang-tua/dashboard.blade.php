<div>
    <x-layout.page-header title="Selamat Datang, {{ $orangTua->nama_orang_tua }}" subtitle="Portal Pemantauan Akademik Anak">
    </x-layout.page-header>

    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="modern-card">
                <h5 class="fw-bold text-primary mb-4"><i class="fas fa-users me-2"></i>Daftar Anak Anda</h5>
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Nama Anak</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th class="text-end">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($anakList as $anak)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($anak->avatar)
                                            <img src="{{ Storage::url($anak->avatar) }}" alt="Avatar" class="me-3 rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="user-avatar bg-info-subtle text-info me-3 border border-info-subtle rounded-circle d-flex align-items-center justify-content-center fs-4" style="width: 45px; height: 45px;">
                                                {{ strtoupper(substr($anak->nama_siswa ?? '?', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="fw-bold text-primary">{{ $anak->nama_siswa }}</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><i class="fas fa-id-badge text-muted me-1"></i> {{ $anak->nisn }}</span></td>
                                <td><x-ui.badge variant="info" icon="fas fa-door-open">{{ $anak->kelas->nama_kelas ?? 'Belum ada kelas' }}</x-ui.badge></td>
                                <td class="text-end">
                                    <a href="{{ route('orang-tua.absensi', ['id_anak' => $anak->id_siswa]) }}" class="btn btn-sm btn-outline-primary" wire:navigate>
                                        <i class="fas fa-clipboard-user me-1"></i> Lihat Absensi
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-users-slash mb-3 text-info opacity-50" style="font-size: 3rem;"></i>
                                    <h5>Belum Ada Data Anak</h5>
                                    <p class="mb-0">Belum ada data anak yang terhubung dengan akun Anda. Hubungi administrator sekolah.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
