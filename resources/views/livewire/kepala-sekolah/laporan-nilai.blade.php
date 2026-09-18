<div>
    <x-layout.page-header title="Laporan Nilai" subtitle="Pantau dan cetak laporan hasil belajar seluruh kelas ke PDF">
        <x-slot:actions>
            <div class="d-flex gap-2">
                <div style="width: 220px;">
                    <x-form.select
                        wire:model.live="selectedKelasId"
                        id="kelas"
                        :options="$kelasOptions"
                        placeholder="-- Pilih Kelas --"
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
            </div>
        </x-slot:actions>
    </x-layout.page-header>

    @if($selectedKelasId && count($siswas) > 0)
        <x-layout.table-card title="Laporan per Mata Pelajaran {{ $kelas ? '- ' . $kelas->nama_kelas : '' }}">
            <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center mb-3">
                <div style="min-width: 280px;">
                    <x-form.select
                        wire:model.live="selectedMapelId"
                        id="mapel"
                        :options="$mapelOptions"
                        placeholder="-- Pilih Mata Pelajaran --"
                    />
                </div>
                @if($selectedMapelId && $mapel)
                    <x-ui.button variant="danger" icon="fas fa-file-pdf"
                        href="{{ route('kepala-sekolah.laporan-nilai.cetak-mapel', ['kelas' => $selectedKelasId, 'mapel' => $selectedMapelId, 'tahun' => $selectedTahunId]) }}"
                        target="_blank">
                        Cetak PDF {{ $mapel }}
                    </x-ui.button>
                @endif
            </div>

            @if($selectedMapelId && $mapel)
                <x-layout.table>
                    <x-slot:head>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">Afektif</th>
                            <th class="text-center">Psikomotor</th>
                            <th class="text-center">Tugas</th>
                            <th class="text-center">UH</th>
                            <th class="text-center">US</th>
                            <th class="text-center">Akhir</th>
                            <th class="text-center">Predikat</th>
                        </tr>
                    </x-slot:head>

                    @foreach($siswas as $index => $siswa)
                        @php $nilai = $mapelNilai[$siswa->id_siswa] ?? null; @endphp
                        <tr class="align-middle text-center" wire:key="mapel-{{ $siswa->id_siswa }}">
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start" style="font-weight: 500;">{{ $siswa->nama_siswa }}</td>
                            <td>{{ $nilai?->rata_afektif ?? '-' }}</td>
                            <td>{{ $nilai?->rata_psikomotor ?? '-' }}</td>
                            <td>{{ $nilai?->rata_tugas ?? '-' }}</td>
                            <td>{{ $nilai?->rata_ulangan_harian ?? '-' }}</td>
                            <td>{{ $nilai?->nilai_ulangan_semester ?? '-' }}</td>
                            <td class="fw-bold">{{ $nilai?->nilai_raport ?? '-' }}</td>
                            <td><x-ui.predikat-badge :nilai="$nilai?->predikat_raport" /></td>
                        </tr>
                    @endforeach
                </x-layout.table>
            @else
                <p class="text-muted small mb-0">Pilih mata pelajaran untuk melihat pratinjau nilai dan mencetak laporannya.</p>
            @endif
        </x-layout.table-card>
    @else
        <x-layout.modern-card>
            <x-ui.empty-state
                icon="fas fa-hand-pointer"
                title="Pilih Kelas & Tahun Ajaran"
                description="Silakan pilih kelas dan tahun ajaran di sudut kanan atas untuk melihat daftar siswa."
            />
        </x-layout.modern-card>
    @endif
</div>
