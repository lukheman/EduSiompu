<div>
    <x-layout.page-header title="Raport Digital Anak" subtitle="Lihat hasil belajar anak per tahun ajaran">
        <x-slot:actions>
            <div class="d-flex gap-2">
                <div style="width: 200px;">
                    <x-form.select 
                        wire:model.live="selectedAnakId" 
                        id="anak" 
                        :options="$anakList->pluck('nama_siswa', 'id_siswa')->toArray()" 
                    />
                </div>
                <div style="width: 200px;">
                    <x-form.select 
                        wire:model.live="selectedTahunId" 
                        id="tahun" 
                        :options="$tahunAjarans->mapWithKeys(function($ta) { return [$ta->id_tahun_ajaran => $ta->nama_tahun . ' ' . ucfirst($ta->semester)]; })->toArray()" 
                        placeholder="-- Tahun Ajaran --" 
                    />
                </div>
            </div>
        </x-slot:actions>
    </x-layout.page-header>

    @if($raport)
        <x-layout.modern-card class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td width="150" class="text-muted">Nama Anak</td><td>: <span style="color: var(--text-primary); font-weight: 600;">{{ $raport->siswa->nama_siswa }}</span></td></tr>
                        <tr><td class="text-muted">NISN</td><td>: <span style="color: var(--text-primary);">{{ $raport->siswa->nisn }}</span></td></tr>
                        <tr><td class="text-muted">Kelas</td><td>: <span style="color: var(--text-primary);">{{ $raport->kelas->nama_kelas }}</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td width="150" class="text-muted">Sakit</td><td>: <span style="color: var(--text-primary);">{{ $raport->sakit }} hari</span></td></tr>
                        <tr><td class="text-muted">Izin</td><td>: <span style="color: var(--text-primary);">{{ $raport->izin }} hari</span></td></tr>
                        <tr><td class="text-muted">Tanpa Keterangan</td><td>: <span style="color: var(--text-primary);">{{ $raport->alpa }} hari</span></td></tr>
                    </table>
                </div>
            </div>
        </x-layout.modern-card>

        <x-layout.table-card title="Rincian Nilai Mata Pelajaran">
            <x-layout.table>
                <x-slot:head>
                    <tr>
                        <th rowspan="2" class="align-middle text-center" style="width: 5%">No</th>
                        <th rowspan="2" class="align-middle text-start">Mata Pelajaran</th>
                        <th rowspan="2" class="align-middle text-center" style="width: 10%">KKM</th>
                        <th colspan="2" class="text-center" style="width: 25%">Pengetahuan</th>
                        <th colspan="2" class="text-center" style="width: 25%">Keterampilan</th>
                    </tr>
                    <tr>
                        <th class="text-center">Nilai</th>
                        <th class="text-center">Predikat</th>
                        <th class="text-center">Nilai</th>
                        <th class="text-center">Predikat</th>
                    </tr>
                </x-slot:head>

                @forelse($raport->nilaiRaport as $index => $nilai)
                    <tr class="text-center">
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start" style="font-weight: 500;">{{ $nilai->mataPelajaran->nama_mapel }}</td>
                        <td>{{ $nilai->mataPelajaran->kkm ?? 75 }}</td>
                        <td>{{ $nilai->nilai_pengetahuan ?? '-' }}</td>
                        <td>
                            @if($nilai->predikat_pengetahuan)
                                <x-ui.badge :variant="$nilai->predikat_pengetahuan === 'A' ? 'success' : ($nilai->predikat_pengetahuan === 'B' ? 'primary' : ($nilai->predikat_pengetahuan === 'C' ? 'warning' : 'danger'))">
                                    {{ $nilai->predikat_pengetahuan }}
                                </x-ui.badge>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $nilai->nilai_keterampilan ?? '-' }}</td>
                        <td>
                            @if($nilai->predikat_keterampilan)
                                <x-ui.badge :variant="$nilai->predikat_keterampilan === 'A' ? 'success' : ($nilai->predikat_keterampilan === 'B' ? 'primary' : ($nilai->predikat_keterampilan === 'C' ? 'warning' : 'danger'))">
                                    {{ $nilai->predikat_keterampilan }}
                                </x-ui.badge>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-ui.empty-state icon="fas fa-clipboard-list" title="Belum ada nilai" description="Nilai untuk semester ini belum diinput oleh guru." size="sm" />
                        </td>
                    </tr>
                @endforelse
            </x-layout.table>
        </x-layout.table-card>

        @if($raport->catatan)
            <x-layout.modern-card class="mt-4">
                <div class="d-flex align-items-start gap-3">
                    <div style="background: rgba(13, 148, 136, 0.1); padding: 1rem; border-radius: 12px; color: var(--primary-color);">
                        <i class="fas fa-comment-dots fa-2x"></i>
                    </div>
                    <div>
                        <h6 style="color: var(--text-primary); font-weight: 600;">Catatan Wali Kelas</h6>
                        <p class="mb-0 text-muted" style="font-style: italic;">"{{ $raport->catatan }}"</p>
                    </div>
                </div>
            </x-layout.modern-card>
        @endif
    @else
        <x-layout.modern-card>
            <x-ui.empty-state 
                icon="fas fa-file-invoice" 
                title="Raport Belum Tersedia" 
                description="Raport digital untuk tahun ajaran atau anak yang dipilih belum diterbitkan." 
            />
        </x-layout.modern-card>
    @endif
</div>
