<div>
    <style>
        .nilai-input::-webkit-outer-spin-button,
        .nilai-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .nilai-input {
            -moz-appearance: textfield;
            appearance: textfield;
        }
    </style>
    <x-layout.page-header title="Jadwal & Absensi" subtitle="Pilih jadwal pelajaran, klik sel pertemuan untuk mengisi kehadiran">
    </x-layout.page-header>

    @if (session('success'))
        <x-ui.toast variant="success">
            {{ session('success') }}
        </x-ui.toast>
    @endif

    @if (session('error'))
        <x-ui.toast variant="danger">
            {{ session('error') }}
        </x-ui.toast>
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

        {{-- Section: Matriks Absensi --}}
        @if($selectedJadwalId)
            <div class="col-12">
                <x-layout.table-card title="Absensi Kelas (klik sel untuk mengubah status)">
                    <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center mb-3">
                        <div class="d-flex gap-2 align-items-center">
                            <x-form.input type="date" wire:model="tanggalBaru" />
                            <x-ui.button variant="outline" icon="fas fa-plus" wire:click="tambahPertemuan" wire:loading.attr="disabled">
                                Tambah Pertemuan
                            </x-ui.button>
                        </div>
                        <span class="text-muted small ms-md-auto">{{ count($pertemuans) }}/20 pertemuan</span>
                    </div>

                    @if(count($siswaList) > 0)
                        <x-layout.table>
                            <x-slot:head>
                                <tr>
                                    <th rowspan="3" class="align-middle" style="width: 40px;">No</th>
                                    <th rowspan="3" class="align-middle">Nama Siswa</th>
                                    <th rowspan="3" class="align-middle text-center">JK</th>
                                    @if(count($pertemuans) > 0)
                                        <th colspan="{{ count($pertemuans) }}" class="text-center">Pertemuan Ke-</th>
                                    @endif
                                    <th colspan="20" class="text-center">Nilai</th>
                                </tr>
                                <tr>
                                    @foreach($pertemuans as $index => $tanggal)
                                        <th rowspan="2" class="text-center" title="{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}">{{ $index + 1 }}</th>
                                    @endforeach
                                    <th colspan="4" class="text-center">Afektif</th>
                                    <th colspan="5" class="text-center">Psikomotor</th>
                                    <th colspan="5" class="text-center">Tugas</th>
                                    <th colspan="4" class="text-center">UH</th>
                                    <th rowspan="2" class="align-middle text-center">US</th>
                                    <th rowspan="2" class="align-middle text-center">Raport</th>
                                </tr>
                                <tr>
                                    @for($i = 1; $i <= 3; $i++)
                                        <th class="text-center">{{ $i }}</th>
                                    @endfor
                                    <th class="text-center">RT</th>
                                    @for($i = 1; $i <= 4; $i++)
                                        <th class="text-center">{{ $i }}</th>
                                    @endfor
                                    <th class="text-center">RT</th>
                                    @for($i = 1; $i <= 4; $i++)
                                        <th class="text-center">{{ $i }}</th>
                                    @endfor
                                    <th class="text-center">RT</th>
                                    @for($i = 1; $i <= 3; $i++)
                                        <th class="text-center">{{ $i }}</th>
                                    @endfor
                                    <th class="text-center">RT</th>
                                </tr>
                            </x-slot:head>

                            @php
                                $grupAspek = [
                                    ['afektif_1', 'afektif_2', 'afektif_3'],
                                    ['psikomotor_1', 'psikomotor_2', 'psikomotor_3', 'psikomotor_4'],
                                    ['tugas_1', 'tugas_2', 'tugas_3', 'tugas_4'],
                                    ['ulangan_harian_1', 'ulangan_harian_2', 'ulangan_harian_3'],
                                ];
                            @endphp
                            @foreach($siswaList as $index => $siswa)
                                <tr class="align-middle text-center" wire:key="siswa-{{ $siswa->id_siswa }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start" style="font-weight: 500; min-width: 150px;">{{ $siswa->nama_siswa }}</td>
                                    <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
                                    @foreach($pertemuans as $tanggal)
                                        @php $st = $kehadiran[$siswa->id_siswa][$tanggal] ?? null; @endphp
                                        <td wire:key="sel-{{ $siswa->id_siswa }}-{{ $tanggal }}"
                                            wire:click="toggleKehadiran({{ $siswa->id_siswa }}, '{{ $tanggal }}')"
                                            style="cursor: pointer; min-width: 44px;"
                                            title="Klik untuk mengubah: {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}">
                                            <x-ui.badge variant="{{ $st === 'hadir' ? 'success' : ($st === 'sakit' ? 'warning' : ($st === 'izin' ? 'info' : ($st === 'alpa' ? 'danger' : 'secondary'))) }}">
                                                {{ $this->simbol($st) }}
                                            </x-ui.badge>
                                        </td>
                                    @endforeach
                                    @foreach($grupAspek as $grup)
                                        @foreach($grup as $field)
                                            <td style="min-width: 110px; border: 1px solid var(--border-color);">
                                                <input type="number" min="0" max="100" class="form-control form-control-sm text-center nilai-input"
                                                    style="border: 1px solid var(--border-color);"
                                                    wire:model.live="nilaiEdit.{{ $siswa->id_siswa }}.{{ $field }}">
                                            </td>
                                        @endforeach
                                        <td class="text-center fw-bold" style="min-width: 80px; border: 1px solid var(--border-color); background: var(--bg-tertiary);">
                                            {{ $this->rataAspekForm($siswa->id_siswa, $grup) ?? '-' }}
                                        </td>
                                    @endforeach
                                    <td style="min-width: 110px; border: 1px solid var(--border-color);">
                                        <input type="number" min="0" max="100" class="form-control form-control-sm text-center nilai-input"
                                            style="border: 1px solid var(--border-color);"
                                            wire:model.live="nilaiEdit.{{ $siswa->id_siswa }}.ulangan_semester">
                                    </td>
                                    <td style="min-width: 110px; border: 1px solid var(--border-color);">
                                        <input type="number" min="0" max="100" class="form-control form-control-sm text-center nilai-input"
                                            style="border: 1px solid var(--border-color);"
                                            wire:model.live="nilaiEdit.{{ $siswa->id_siswa }}.raport">
                                    </td>
                                </tr>
                            @endforeach
                        </x-layout.table>
                        <p class="text-muted small mt-2 mb-0">Klik sel pertemuan untuk memutar status: - &rarr; ✓ Hadir &rarr; S Sakit &rarr; I Izin &rarr; A Alpa &rarr; - (tersimpan otomatis). Nilai per penilaian diketik langsung dan ikut tersimpan otomatis; kolom Raport terisi dari rata-rata namun dapat diubah manual.</p>
                    @else
                        <x-ui.empty-state icon="fas fa-users-slash" title="Tidak Ada Siswa" description="Belum ada data siswa di kelas ini." />
                    @endif
                </x-layout.table-card>
            </div>
        @endif
    </div>
</div>
