<div>
    <x-layout.page-header title="Input Nilai Raport" subtitle="Isi nilai pengetahuan dan keterampilan siswa di kelas yang Anda walikan">
        <x-slot:actions>
            <div class="d-flex gap-2">
                <div style="width: 220px;">
                    <x-form.select
                        wire:model.live="selectedKelasId"
                        id="kelas"
                        :options="$kelasOptions"
                        placeholder="-- Pilih Kelas Perwalian --"
                    />
                </div>
                <div style="width: 330px;">
                    <x-form.select
                        wire:model.live="selectedAmpuId"
                        id="ampu"
                        :options="$mapelOptions"
                        placeholder="-- Pilih Mata Pelajaran --"
                    />
                </div>
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
                            <th style="width: 15%">NISN</th>
                            <th style="width: 35%">Nama Siswa</th>
                            <th style="width: 25%" class="text-center">Nilai Pengetahuan</th>
                            <th style="width: 25%" class="text-center">Nilai Keterampilan</th>
                        </tr>
                    </x-slot:head>

                    @foreach($siswas as $siswa)
                        <tr class="align-middle">
                            <td>{{ $siswa->nisn }}</td>
                            <td style="font-weight: 500;">{{ $siswa->nama_siswa }}</td>
                            <td>
                                <input type="number" min="0" max="100" class="form-control" 
                                    wire:model="nilaiData.{{ $siswa->id_siswa }}.pengetahuan" placeholder="0-100">
                            </td>
                            <td>
                                <input type="number" min="0" max="100" class="form-control" 
                                    wire:model="nilaiData.{{ $siswa->id_siswa }}.keterampilan" placeholder="0-100">
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
        <x-layout.modern-card>
            <x-ui.empty-state
                icon="fas fa-users-slash"
                title="Tidak ada siswa"
                description="Tidak ada siswa yang terdaftar di kelas ini."
            />
        </x-layout.modern-card>
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
                title="Pilih Kelas & Mata Pelajaran"
                description="Silakan pilih kelas perwalian dan mata pelajaran di sudut kanan atas untuk mulai memasukkan nilai."
            />
        </x-layout.modern-card>
    @endif
</div>
