<div>
    <x-layout.page-header title="Profil Saya" subtitle="Informasi biodata diri Anda sebagai siswa" />

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="modern-card text-center h-100">
                <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle mb-3 d-flex align-items-center justify-content-center bg-light text-primary border border-4 border-primary" 
                         style="width: 150px; height: 150px; font-size: 4rem;">
                        {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                    </div>
                    <h3 class="fw-bold text-primary mb-1">{{ $siswa->nama_siswa }}</h3>
                    <div class="text-muted mb-3"><i class="fas fa-id-card me-2"></i>NISN: {{ $siswa->nisn }}</div>
                    
                    <x-ui.badge variant="info" icon="fas fa-chalkboard">
                        Kelas {{ $siswa->kelas->nama_kelas ?? 'Belum Ditentukan' }}
                    </x-ui.badge>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="modern-card h-100">
                <h4 class="preview-title"><i class="fas fa-user-edit me-2"></i>Biodata Diri</h4>
                
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-medium" style="width: 35%;">Nama Lengkap</th>
                                <td class="fw-semibold text-primary">{{ $siswa->nama_siswa }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Nomor Induk Siswa Nasional (NISN)</th>
                                <td class="fw-semibold">{{ $siswa->nisn }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Kelas Saat Ini</th>
                                <td class="fw-semibold">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert-modern mt-4 bg-light">
                    <i class="fas fa-info-circle text-primary fa-2x"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Informasi Pembaruan Data</h6>
                        <p class="mb-0 text-muted small">
                            Halaman ini hanya bersifat "Baca Saja" <i>(Read-Only)</i>. Jika terdapat kesalahan pencatatan data (seperti Nama atau NISN), mohon segera menghubungi Administrator atau Wali Kelas Anda untuk melakukan perubahan data di sistem pusat.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
