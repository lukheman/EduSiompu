<div>
    <x-layout.page-header title="Profil Saya" subtitle="Informasi biodata diri Anda sebagai siswa" />

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="modern-card text-center h-100">
                <div class="d-flex flex-column align-items-center">
                    @if($siswa->avatar)
                        <img src="{{ Storage::url($siswa->avatar) }}" alt="Avatar" class="rounded-circle mb-3 border border-4 border-primary" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/default-avatar.svg') }}" alt="Avatar" class="rounded-circle mb-3 border border-4 border-primary" style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                    <h3 class="fw-bold text-primary mb-1">{{ $siswa->nama_siswa }}</h3>
                    <div class="text-muted mb-3"><i class="fas fa-id-card me-2"></i>NIS: {{ $siswa->nis ?? '-' }} | NISN: {{ $siswa->nisn }}</div>

                    <x-ui.badge variant="info" icon="fas fa-chalkboard">
                        Kelas {{ $siswa->kelas->nama_kelas ?? 'Belum Ditentukan' }}
                    </x-ui.badge>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="modern-card">
                <h5 class="fw-bold text-primary mb-4 border-bottom pb-3"><i class="fas fa-user-edit me-2"></i>Biodata Diri</h5>

                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-medium w-50">Nama Lengkap</th>
                                <td class="fw-semibold text-primary">{{ $siswa->nama_siswa }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Nomor Induk Siswa Nasional (NISN)</th>
                                <td class="fw-semibold">{{ $siswa->nisn }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Nomor Induk Siswa (NIS)</th>
                                <td class="fw-semibold">{{ $siswa->nis ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">NIK</th>
                                <td class="fw-semibold">{{ $siswa->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Tempat, Tanggal Lahir</th>
                                <td class="fw-semibold">{{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir?->format('d-m-Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Jenis Kelamin</th>
                                <td class="fw-semibold">{{ ['L' => 'Laki-laki', 'P' => 'Perempuan'][$siswa->jenis_kelamin] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Agama/Kepercayaan</th>
                                <td class="fw-semibold">{{ $siswa->agama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Kewarganegaraan</th>
                                <td class="fw-semibold">{{ $siswa->kewarganegaraan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Alamat Lengkap</th>
                                <td class="fw-semibold">{{ $siswa->alamat ?? '-' }}<br>{{ $siswa->rt || $siswa->rw ? 'RT/RW ' . ($siswa->rt ?? '-') . '/' . ($siswa->rw ?? '-') : '' }} {{ $siswa->dusun ?? '' }} {{ $siswa->kelurahan ?? '' }} {{ $siswa->kecamatan ?? '' }} {{ $siswa->kode_pos ?? '' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Jenis Tempat Tinggal</th>
                                <td class="fw-semibold">{{ $siswa->jenis_tempat_tinggal ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Transportasi ke Sekolah</th>
                                <td class="fw-semibold">{{ $siswa->transportasi_ke_sekolah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-medium">Kelas Saat Ini</th>
                                <td class="fw-semibold">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
