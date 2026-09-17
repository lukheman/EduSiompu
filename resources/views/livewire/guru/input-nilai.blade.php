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
    <x-layout.page-header title="Input Nilai Raport" subtitle="Isi nilai pengetahuan dan keterampilan siswa berdasarkan mata pelajaran yang Anda ampu">
        <x-slot:actions>
            <div style="width: 350px;">
                <x-form.select
                    wire:model.live="selectedAmpuId"
                    id="ampu"
                    :options="$ampuOptions"
                    placeholder="-- Pilih Kelas & Mata Pelajaran --"
                />
            </div>
        </x-slot:actions>
    </x-layout.page-header>

    @if(session()->has('message'))
        <x-ui.toast variant="success">
            {{ session('message') }}
        </x-ui.toast>
    @endif

    @if($selectedAmpuId && count($siswas) > 0)
        <form wire:submit="simpan">
            <x-layout.table-card title="Daftar Siswa">
                <x-layout.table>
                    <x-slot:head>
                        <tr>
                            <th rowspan="2" class="align-middle">NISN</th>
                            <th rowspan="2" class="align-middle">Nama Siswa</th>
                            <th colspan="4" class="text-center" style="border-left: 1px solid var(--border-color);">Afektif</th>
                            <th colspan="5" class="text-center" style="border-left: 1px solid var(--border-color);">Psikomotor</th>
                            <th colspan="5" class="text-center" style="border-left: 1px solid var(--border-color);">Tugas</th>
                            <th colspan="4" class="text-center" style="border-left: 1px solid var(--border-color);">UH</th>
                            <th rowspan="2" class="align-middle text-center" style="border-left: 1px solid var(--border-color);">US</th>
                            <th rowspan="2" class="align-middle text-center" style="border-left: 1px solid var(--border-color);">Raport <i class="fas fa-magic text-muted" title="Terisi otomatis dari rata-rata, dapat diubah manual"></i></th>
                        </tr>
                        <tr>
                            @for($i = 1; $i <= 3; $i++)
                                <th class="text-center" style="border-left: 1px solid var(--border-color);">{{ $i }}</th>
                            @endfor
                            <th class="text-center" style="border-left: 1px solid var(--border-color);">Rata2</th>
                            @for($i = 1; $i <= 4; $i++)
                                <th class="text-center" style="border-left: 1px solid var(--border-color);">{{ $i }}</th>
                            @endfor
                            <th class="text-center" style="border-left: 1px solid var(--border-color);">Rata2</th>
                            @for($i = 1; $i <= 4; $i++)
                                <th class="text-center" style="border-left: 1px solid var(--border-color);">{{ $i }}</th>
                            @endfor
                            <th class="text-center" style="border-left: 1px solid var(--border-color);">Rata2</th>
                            @for($i = 1; $i <= 3; $i++)
                                <th class="text-center" style="border-left: 1px solid var(--border-color);">{{ $i }}</th>
                            @endfor
                            <th class="text-center" style="border-left: 1px solid var(--border-color);">Rata2</th>
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
                    @foreach($siswas as $siswa)
                        <tr class="align-middle">
                            <td>{{ $siswa->nisn }}</td>
                            <td style="font-weight: 500; min-width: 150px;">{{ $siswa->nama_siswa }}</td>
                            @foreach($grupAspek as $grup)
                                @foreach($grup as $field)
                                    <td style="min-width: 110px; border: 1px solid var(--border-color);">
                                        <input type="number" min="0" max="100" class="form-control form-control-sm text-center nilai-input"
                                            style="border: 1px solid var(--border-color);"
                                            wire:model.live="nilaiData.{{ $siswa->id_siswa }}.{{ $field }}">
                                    </td>
                                @endforeach
                                <td class="text-center fw-bold" style="min-width: 80px; border: 1px solid var(--border-color); background: var(--bg-tertiary);">
                                    {{ $this->rataAspekForm($siswa->id_siswa, $grup) ?? '-' }}
                                </td>
                            @endforeach
                            <td style="min-width: 110px; border: 1px solid var(--border-color);">
                                <input type="number" min="0" max="100" class="form-control form-control-sm text-center nilai-input"
                                    style="border: 1px solid var(--border-color);"
                                    wire:model.live="nilaiData.{{ $siswa->id_siswa }}.ulangan_semester">
                            </td>
                            <td style="min-width: 110px; border: 1px solid var(--border-color);">
                                <input type="number" min="0" max="100" class="form-control form-control-sm text-center nilai-input"
                                    style="border: 1px solid var(--border-color);"
                                    wire:model.live="nilaiData.{{ $siswa->id_siswa }}.raport">
                            </td>
                        </tr>
                    @endforeach
                </x-layout.table>

                <div class="mt-4 text-end px-3 pb-3">
                    <x-ui.button type="submit" variant="primary" icon="fas fa-save" wire:loading.attr="disabled">
                        Simpan Nilai
                    </x-ui.button>
                </div>
            </x-layout.table-card>
        </form>
    @elseif($selectedAmpuId)
        @if(! $this->ampuValid())
            <x-layout.modern-card>
                <x-ui.empty-state
                    icon="fas fa-lock"
                    title="Tidak Memiliki Akses"
                    description="Penugasan ini bukan mata pelajaran yang Anda ampu."
                />
            </x-layout.modern-card>
        @else
            <x-layout.modern-card>
                <x-ui.empty-state
                    icon="fas fa-users-slash"
                    title="Tidak ada siswa"
                    description="Tidak ada siswa yang terdaftar di kelas ini."
                />
            </x-layout.modern-card>
        @endif
    @elseif(count($ampuOptions) === 0)
        <x-layout.modern-card>
            <x-ui.empty-state
                icon="fas fa-book-open"
                title="Belum Ada Penugasan"
                description="Anda belum ditugaskan mengampu mata pelajaran. Hubungi admin untuk penugasan guru."
            />
        </x-layout.modern-card>
    @else
        <x-layout.modern-card>
            <x-ui.empty-state
                icon="fas fa-hand-pointer"
                title="Pilih Kelas & Mata Pelajaran"
                description="Silakan pilih kelas dan mata pelajaran yang Anda ampu di sudut kanan atas untuk mulai memasukkan nilai."
            />
        </x-layout.modern-card>
    @endif
</div>
