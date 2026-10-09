<div>
    <x-layout.page-header title="Laporan Nilai" subtitle="Pantau dan cetak laporan hasil belajar seluruh kelas ke PDF">
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
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
        <x-layout.table-card title="Laporan per Siswa {{ $kelas ? '- ' . $kelas->nama_kelas : '' }}">
            <x-layout.table>
                <x-slot:head>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">JK</th>
                        <th class="text-center">Status Nilai</th>
                        <th class="text-center" style="width: 160px;">Laporan PDF</th>
                    </tr>
                </x-slot:head>

                @foreach($siswas as $index => $siswa)
                    <tr class="align-middle" wire:key="laporan-{{ $siswa->id_siswa }}">
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 500;">{{ $siswa->nama_siswa }}</td>
                        <td class="text-center">{{ $siswa->jenis_kelamin ?? '-' }}</td>
                        <td class="text-center">
                            @if($siswa->raport->isNotEmpty())
                                <x-ui.badge variant="success" icon="fas fa-check-circle">Sudah Dinilai</x-ui.badge>
                            @else
                                <x-ui.badge variant="secondary">Belum Ada Nilai</x-ui.badge>
                            @endif
                        </td>
                        <td class="text-center">
                            <x-ui.button variant="danger" size="sm" icon="fas fa-file-pdf"
                                href="{{ route('kepala-sekolah.laporan-nilai.cetak', ['siswa' => $siswa->id_siswa, 'tahun' => $selectedTahunId]) }}"
                                target="_blank">
                                Cetak PDF
                            </x-ui.button>
                        </td>
                    </tr>
                @endforeach
            </x-layout.table>
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
