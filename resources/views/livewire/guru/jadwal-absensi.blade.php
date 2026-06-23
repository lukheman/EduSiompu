

<div>
    <x-layout.page-header title="Jadwal & Absensi" subtitle="Pilih jadwal pelajaran dan catat kehadiran siswa berdasarkan tanggal">
    </x-layout.page-header>

    @if (session('success'))
        <x-ui.alert variant="success" title="Berhasil!" class="mb-4">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    <div class="row">
        {{-- Section: Pilih Jadwal --}}
        <div class="col-12 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <h5 class="fw-bold mb-2 mb-md-0"><i class="fas fa-calendar-alt text-primary me-2"></i> Jadwal Mengajar Anda</h5>
                <div style="min-width: 200px;">
                    <x-form.select wire:model.live="filterHari" :options="['Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu', 'Kamis' => 'Kamis', 'Jumat' => 'Jumat', 'Sabtu' => 'Sabtu']" placeholder="Semua Hari" />
                </div>
            </div>
            @if($jadwals->isEmpty())
                <x-layout.modern-card>
                    <x-ui.empty-state icon="fas fa-calendar-times" title="Belum Ada Jadwal" description="Anda belum ditugaskan pada jadwal pelajaran apapun saat ini." />
                </x-layout.modern-card>
            @else
                <div class="row g-3">
                    @foreach($jadwals as $jadwal)
                        <div class="col-md-6 col-lg-4">
                            <div class="modern-card p-3 h-100 border transition-all cursor-pointer {{ $selectedJadwalId == $jadwal->id_jadwal_pelajaran ? 'border-primary shadow-sm bg-primary bg-opacity-10' : 'hover-shadow' }}" 
                                 wire:click="selectJadwal({{ $jadwal->id_jadwal_pelajaran }})">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <x-ui.badge variant="{{ $selectedJadwalId == $jadwal->id_jadwal_pelajaran ? 'primary' : 'light text-dark' }}">
                                        {{ $jadwal->hari }}
                                    </x-ui.badge>
                                    <span class="small fw-bold text-muted">
                                        <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </span>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark">{{ $jadwal->guruAmpu->mataPelajaran->nama_mapel }}</h6>
                                <p class="mb-0 text-muted small"><i class="fas fa-chalkboard text-info me-1"></i> Kelas {{ $jadwal->guruAmpu->kelas->nama_kelas }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Section: Isi Absensi --}}
        @if($selectedJadwalId)
            <div class="col-12">
                <x-layout.modern-card>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fas fa-clipboard-list text-primary me-2"></i> Form Absensi Kelas</h5>
                            <p class="text-muted small mb-0">Pilih tanggal dan sesuaikan kehadiran siswa.</p>
                        </div>
                        <div class="mt-3 mt-md-0" style="min-width: 200px;">
                            <label class="form-label small fw-bold text-muted mb-1">Tanggal Absensi</label>
                            <x-form.input type="date" wire:model.live="selectedTanggal" />
                        </div>
                    </div>

                    @if($selectedTanggal)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th style="width: 300px;">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswaList as $index => $siswa)
                                        <tr wire:key="siswa-{{ $siswa->id_siswa }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $siswa->nama_siswa }}</div>
                                            </td>
                                            <td>{{ $siswa->nisn }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-3">
                                                    @foreach(\App\Enums\StatusKehadiran::cases() as $status)
                                                        <x-form.radio 
                                                            name="kehadiran_{{ $siswa->id_siswa }}" 
                                                            id="status_{{ $siswa->id_siswa }}_{{ $status->value }}"
                                                            value="{{ $status->value }}" 
                                                            label="{{ $status->getLabel() }}"
                                                            wire:model="kehadiran.{{ $siswa->id_siswa }}"
                                                        />
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <x-ui.empty-state icon="fas fa-users-slash" title="Tidak Ada Siswa" description="Belum ada data siswa di kelas ini." />
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($siswaList->isNotEmpty())
                            <div class="d-flex justify-content-end mt-4">
                                <x-ui.button variant="primary" icon="fas fa-save" wire:click="saveAbsensi">
                                    Simpan Absensi
                                </x-ui.button>
                            </div>
                        @endif
                    @else
                        <x-ui.empty-state icon="far fa-calendar" title="Pilih Tanggal" description="Silakan pilih tanggal absensi terlebih dahulu." />
                    @endif
                </x-layout.modern-card>
            </div>
        @endif
    </div>
</div>