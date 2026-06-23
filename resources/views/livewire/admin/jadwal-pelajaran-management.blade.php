<div>
    <x-layout.page-header title="Manajemen Jadwal Pelajaran" subtitle="{{ $selectedKelasId ? 'Mengatur jadwal untuk kelas ' . $selectedKelasName : 'Pilih kelas untuk mengatur jadwal' }}">
        <x-slot:actions>
            @if($selectedKelasId)
                <x-ui.button wire:click="backToKelas" variant="outline" icon="fas fa-arrow-left">
                    Kembali ke Daftar Kelas
                </x-ui.button>
            @else
                <div style="width: 250px;">
                    <x-form.select wire:model.live="filter_tahun_ajaran" :options="$tahunAjarans->mapWithKeys(fn($ta) => [$ta->id_tahun_ajaran => $ta->nama_tahun . ' ' . ucfirst($ta->semester)])->toArray()" placeholder="-- Semua Tahun Ajaran --" />
                </div>
            @endif
        </x-slot:actions>
    </x-layout.page-header>

    @if (session('success'))
        <x-ui.toast variant="success">
            {{ session('success') }}
        </x-ui.toast>
    @endif

    @if(!$selectedKelasId)
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($kelasList as $kelas)
                <div class="col">
                    <x-layout.modern-card class="h-100 text-center py-4 hover-elevate cursor-pointer" wire:click="selectKelas({{ $kelas->id_kelas }})">
                        <div class="mb-3">
                            <div class="rounded-circle bg-light text-primary d-inline-flex align-items-center justify-content-center fs-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                        </div>
                        <h5 class="mb-1 text-dark fw-bold">{{ $kelas->nama_kelas }}</h5>
                        <p class="text-muted small mb-0">Klik untuk mengatur jadwal</p>
                    </x-layout.modern-card>
                </div>
            @empty
                <div class="col-12">
                    <x-layout.modern-card>
                        <x-ui.empty-state icon="fas fa-chalkboard" title="Belum ada data kelas" description="Tambahkan data kelas terlebih dahulu." />
                    </x-layout.modern-card>
                </div>
            @endforelse
        </div>
    @else
        <div class="row">
            @foreach($daftarHari as $hari)
                <div class="col-md-6 col-lg-4 mb-4">
                    <x-layout.modern-card class="h-100" :padding="false">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light bg-opacity-50">
                            <h6 class="mb-0 fw-bold text-dark"><i class="far fa-calendar-alt me-2 text-primary"></i>{{ $hari }}</h6>
                            <x-ui.button variant="outline-primary" size="sm" wire:click="openModal('{{ $hari }}')" title="Tambah Jadwal {{ $hari }}" icon="fas fa-plus"></x-ui.button>
                        </div>
                        <div class="p-0">
                            @php
                                $jadwalHariIni = $jadwalsByHari->get($hari, collect());
                            @endphp

                            @if($jadwalHariIni->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <p class="mb-0 small opacity-75">Tidak ada jadwal</p>
                                </div>
                            @else
                                <ul class="list-group list-group-flush rounded-bottom">
                                    @foreach($jadwalHariIni as $jadwal)
                                        <li class="list-group-item py-3 px-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="mb-2">
                                                        <x-ui.badge variant="primary" class="small">
                                                            <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                        </x-ui.badge>
                                                    </div>
                                                    <h6 class="mb-1 fw-bold text-dark">
                                                        <span class="text-primary me-1">[{{ $jadwal->guruAmpu->mataPelajaran->kode_mapel }}]</span>
                                                        {{ $jadwal->guruAmpu->mataPelajaran->nama_mapel }}
                                                    </h6>
                                                    <p class="mb-0 text-muted small"><i class="fas fa-user-tie me-1"></i> {{ $jadwal->guruAmpu->guru->nama_guru }}</p>
                                                </div>
                                                <div class="d-flex flex-column gap-1">
                                                    <x-ui.button variant="warning" size="sm" class="text-white" wire:click="edit({{ $jadwal->id_jadwal_pelajaran }})" title="Edit" icon="fas fa-edit"></x-ui.button>
                                                    <x-ui.button variant="danger" size="sm" class="text-white" wire:click="confirmDelete({{ $jadwal->id_jadwal_pelajaran }})" title="Hapus" icon="fas fa-trash-alt"></x-ui.button>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </x-layout.modern-card>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal-backdrop-custom">
            <x-layout.modern-card class="modal-content-custom m-auto position-relative" style="max-width: 600px; margin-top: 5rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h5 class="mb-0 fw-bold">{{ $editingId ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h5>
                    <button wire:click="closeModal" class="btn-close"></button>
                </div>
                <form wire:submit="save">

                    @if($errors->has('id_tahun_ajaran') || $errors->has('id_kelas') || $errors->has('hari'))
                        <div class="alert alert-danger mb-3 py-2 px-3 small">
                            Pastikan Anda telah memilih Tahun Ajaran dan Kelas sebelum menyimpan jadwal.
                            ({{ $errors->first('id_tahun_ajaran') ?? $errors->first('id_kelas') ?? $errors->first('hari') }})
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="position-relative" x-data="{ showDropdown: false }">
                                <label class="form-label fw-bold">Mata Pelajaran <span class="text-danger">*</span></label>
                                
                                <input type="hidden" wire:model.live="id_mata_pelajaran">
                                
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                                        <i class="fas fa-search" style="color: var(--text-muted);"></i>
                                    </span>
                                    <input type="text" class="form-control @error('id_mata_pelajaran') is-invalid @enderror" 
                                        placeholder="Ketik untuk mencari mapel..." 
                                        wire:model.live.debounce.300ms="searchMapelModal"
                                        x-on:focus="showDropdown = true"
                                        x-on:click.away="showDropdown = false"
                                        style="border-left: none;">
                                </div>
                                
                                @if(empty($id_mata_pelajaran))
                                    <div x-show="showDropdown" x-cloak class="position-absolute shadow-sm rounded" style="z-index: 1050; max-height: 220px; overflow-y: auto; top: 100%; left: 0; right: 0; background: var(--bg-primary); border: 1px solid var(--border-color); margin-top: 4px;">
                                        @php
                                            $filteredMapels = $searchMapelModal 
                                                ? $mapels->filter(fn($m) => str_contains(strtolower($m->nama_mapel), strtolower($searchMapelModal)) || str_contains(strtolower($m->kode_mapel), strtolower($searchMapelModal)))
                                                : $mapels;
                                        @endphp
                                        @forelse ($filteredMapels as $m)
                                            <div class="p-2 border-bottom" style="cursor: pointer; transition: background 0.2s; background: var(--bg-primary);" 
                                                 onmouseover="this.style.backgroundColor='var(--bg-tertiary)'" 
                                                 onmouseout="this.style.backgroundColor='var(--bg-primary)'"
                                                 wire:click="selectMapel('{{ $m->id_mata_pelajaran }}', '{{ addslashes('['.$m->kode_mapel.'] '.$m->nama_mapel) }}')"
                                                 x-on:click="showDropdown = false">
                                                <div class="fw-bold" style="color: var(--text-primary); font-size: 0.9rem;">{{ $m->nama_mapel }}</div>
                                                <small class="text-muted"><i class="fas fa-barcode me-1"></i>{{ $m->kode_mapel }}</small>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted small">
                                                Mata Pelajaran tidak ditemukan
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                                
                                @error('id_mata_pelajaran')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="position-relative" x-data="{ showDropdown: false }">
                                <label class="form-label fw-bold">Guru Pengampu <span class="text-danger">*</span></label>
                                
                                <input type="hidden" wire:model.live="id_guru">
                                
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                                        <i class="fas fa-search" style="color: var(--text-muted);"></i>
                                    </span>
                                    <input type="text" class="form-control @error('id_guru') is-invalid @enderror" 
                                        placeholder="Ketik untuk mencari guru..." 
                                        wire:model.live.debounce.300ms="searchGuruModal"
                                        x-on:focus="showDropdown = true"
                                        x-on:click.away="showDropdown = false"
                                        style="border-left: none;">
                                </div>
                                
                                @if(empty($id_guru))
                                    <div x-show="showDropdown" x-cloak class="position-absolute shadow-sm rounded" style="z-index: 1050; max-height: 220px; overflow-y: auto; top: 100%; left: 0; right: 0; background: var(--bg-primary); border: 1px solid var(--border-color); margin-top: 4px;">
                                        @php
                                            $filteredGurus = $searchGuruModal 
                                                ? $gurus->filter(fn($g) => str_contains(strtolower($g->nama_guru), strtolower($searchGuruModal)))
                                                : $gurus;
                                        @endphp
                                        @forelse ($filteredGurus as $g)
                                            <div class="p-2 border-bottom" style="cursor: pointer; transition: background 0.2s; background: var(--bg-primary);" 
                                                 onmouseover="this.style.backgroundColor='var(--bg-tertiary)'" 
                                                 onmouseout="this.style.backgroundColor='var(--bg-primary)'"
                                                 wire:click="selectGuru('{{ $g->id_guru }}', '{{ addslashes($g->nama_guru) }}')"
                                                 x-on:click="showDropdown = false">
                                                <div class="fw-bold" style="color: var(--text-primary); font-size: 0.9rem;">{{ $g->nama_guru }}</div>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-muted small">
                                                Guru tidak ditemukan
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                                
                                @error('id_guru')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 mb-4">
                            <x-form.input type="text" label="Jam Mulai" wire:model="jam_mulai" required="true" placeholder="08:00" hint="Format 24 Jam (HH:MM)" icon="far fa-clock" :error="$errors->first('jam_mulai')" />
                        </div>
                        <div class="col-md-6 mb-4">
                            <x-form.input type="text" label="Jam Selesai" wire:model="jam_selesai" required="true" placeholder="09:30" hint="Format 24 Jam (HH:MM)" icon="far fa-clock" :error="$errors->first('jam_selesai')" />
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2 pt-3 border-top">
                        <x-ui.button type="button" wire:click="closeModal" variant="outline">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">Simpan</x-ui.button>
                    </div>
                </form>
            </x-layout.modern-card>
        </div>
    @endif

    <!-- Delete Modal -->
    <x-ui.confirm-modal
        :show="$showDeleteModal"
        title="Hapus Jadwal"
        message="Apakah Anda yakin ingin menghapus jadwal ini?"
        confirmText="Ya, Hapus"
        cancelText="Batal"
        onConfirm="delete"
        onCancel="$set('showDeleteModal', false)"
    />
</div>
