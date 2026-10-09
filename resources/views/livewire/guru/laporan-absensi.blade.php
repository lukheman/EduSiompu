<div>
    <x-layout.page-header title="Laporan Absensi" subtitle="Pantau daftar hadir siswa di kelas yang Anda walikan dan cetak ke PDF">
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <div style="width: 220px;">
                    <x-form.select
                        wire:model.live="selectedKelasId"
                        id="kelas"
                        :options="$kelasOptions"
                        placeholder="-- Pilih Kelas Perwalian --"
                    />
                </div>
                <div style="width: 250px;">
                    <x-form.select
                        wire:model.live="selectedTahunId"
                        id="tahun"
                        :options="collect($tahunAjaranOptions)->mapWithKeys(fn($ta) => [$ta->id_tahun_ajaran => $ta->nama_tahun . ' ' . ucfirst($ta->semester)])->toArray()"
                        placeholder="-- Pilih Tahun Ajaran --"
                    />
                </div>
                <div style="width: 250px;">
                    <x-form.select
                        wire:model.live="selectedMapelId"
                        id="mapel"
                        :options="$mapelOptions"
                        placeholder="-- Pilih Mata Pelajaran --"
                    />
                </div>
            </div>
        </x-slot:actions>
    </x-layout.page-header>

    @if($selectedKelasId && count($siswas) > 0)
        <x-layout.table-card title="Daftar Hadir {{ $kelas ? '- ' . $kelas->nama_kelas : '' }}{{ $mapel ? ' - ' . $mapel : '' }}">
            @if($selectedMapelId && $mapel)
                <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center mb-3">
                    <x-ui.button variant="danger" icon="fas fa-file-pdf"
                        href="{{ route('guru.laporan-absensi.cetak-mapel', ['kelas' => $selectedKelasId, 'mapel' => $selectedMapelId, 'tahun' => $selectedTahunId]) }}"
                        target="_blank">
                        Cetak PDF {{ $mapel }}
                    </x-ui.button>
                    <span class="text-muted small ms-md-2">Keterangan: ✓ = Hadir, S = Sakit, I = Izin, A = Alpa, - = Belum diisi</span>
                </div>

                <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center" style="min-width: 2200px; font-size: 10px;">
                    <thead style="background: #d4af37;">
                        <tr>
                            <th rowspan="3" style="width: 50px;">NO</th><th rowspan="3" style="width: 220px;">NAMA</th><th rowspan="3">JK (L/P)</th>
                            <th colspan="20">PERTEMUAN KE</th><th colspan="18">PENILAIAN</th>
                            <th rowspan="3">PTS</th><th rowspan="3">PAS</th><th rowspan="3">NR</th><th rowspan="3">KET</th>
                        </tr>
                        <tr>@for($i = 1; $i <= 20; $i++) <th rowspan="2">{{ $i }}</th> @endfor <th colspan="4">AFEKTIF</th><th colspan="5">PSIKOMOTOR</th><th colspan="5">NILAI TUGAS</th><th colspan="4">UL. HARIAN</th></tr>
                        <tr>@for($i = 1; $i <= 3; $i++) <th>{{ $i }}</th> @endfor <th>RT</th>@for($i = 1; $i <= 4; $i++) <th>{{ $i }}</th> @endfor <th>RT</th>@for($i = 1; $i <= 4; $i++) <th>{{ $i }}</th> @endfor <th>RT</th>@for($i = 1; $i <= 3; $i++) <th>{{ $i }}</th> @endfor <th>RT</th></tr>
                    </thead>

                    @foreach($siswas as $index => $siswa)
                        @php $nilai = $mapelNilai[$siswa->id_siswa] ?? null; @endphp
                        <tr class="align-middle text-center" wire:key="absensi-{{ $siswa->id_siswa }}">
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start" style="font-weight: 500; white-space: normal; overflow-wrap: anywhere; word-break: break-word;">{{ $siswa->nama_siswa }}</td>
                            <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
                            @for($i = 0; $i < 20; $i++)
                                @php
                                    $tanggal = $pertemuans[$i] ?? null;
                                    $st = $tanggal ? ($kehadiran[$siswa->id_siswa][$tanggal->format('Y-m-d')] ?? null) : null;
                                @endphp
                                <td class="fw-bold">{{ $this->simbol($st) }}</td>
                            @endfor
                            @for($i = 1; $i <= 3; $i++) <td>{{ $nilai?->{'nilai_afektif_'.$i} ?? '-' }}</td> @endfor <td class="fw-bold">{{ $nilai?->rata_afektif ?? '-' }}</td>
                            @for($i = 1; $i <= 4; $i++) <td>{{ $nilai?->{'nilai_psikomotor_'.$i} ?? '-' }}</td> @endfor <td class="fw-bold">{{ $nilai?->rata_psikomotor ?? '-' }}</td>
                            @for($i = 1; $i <= 4; $i++) <td>{{ $nilai?->{'nilai_tugas_'.$i} ?? '-' }}</td> @endfor <td class="fw-bold">{{ $nilai?->rata_tugas ?? '-' }}</td>
                            @for($i = 1; $i <= 3; $i++) <td>{{ $nilai?->{'nilai_ulangan_harian_'.$i} ?? '-' }}</td> @endfor <td class="fw-bold">{{ $nilai?->rata_ulangan_harian ?? '-' }}</td>
                            <td>{{ $nilai?->rata_tugas ?? '-' }}</td><td>{{ $nilai?->nilai_ulangan_semester ?? '-' }}</td><td class="fw-bold">{{ $nilai?->nilai_raport ?? '-' }}</td><td>{{ $nilai?->predikat_raport ?? '-' }}</td>
                        </tr>
                    @endforeach
                </table>
                </div>
            @else
                <p class="text-muted small mb-0">Pilih mata pelajaran pada filter di atas untuk melihat daftar hadir dan mencetak laporannya.</p>
            @endif
        </x-layout.table-card>
    @elseif(count($kelasOptions) === 0)
        <x-layout.modern-card>
            <x-ui.empty-state
                icon="fas fa-user-shield"
                title="Bukan Wali Kelas"
                description="Anda belum ditugaskan sebagai wali kelas. Hubungi admin untuk penugasan wali kelas."
            />
        </x-layout.modern-card>
    @else
        <x-layout.modern-card>
            <x-ui.empty-state
                icon="fas fa-hand-pointer"
                title="Pilih Kelas & Tahun Ajaran"
                description="Silakan pilih kelas perwalian dan tahun ajaran di sudut kanan atas untuk melihat daftar hadir."
            />
        </x-layout.modern-card>
    @endif
</div>
