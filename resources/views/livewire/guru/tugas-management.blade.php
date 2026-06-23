<div>
    <x-layout.page-header title="Manajemen Tugas Pembelajaran" subtitle="Kelola tugas untuk setiap kelas dan berikan penilaian.">
    </x-layout.page-header>

    @if (session('success'))
        <x-ui.toast variant="success">
            {{ session('success') }}
        </x-ui.toast>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <label class="form-label fw-bold">Pilih Kelas / Jadwal</label>
            <x-form.select wire:model.live="selectedJadwalId" :options="$jadwals->mapWithKeys(fn($j) => [$j->id_jadwal_pelajaran => $j->guruAmpu->kelas->nama_kelas . ' - ' . $j->guruAmpu->mataPelajaran->nama_mapel . ' (' . $j->hari . ')'])->toArray()" placeholder="-- Pilih Jadwal --" />
        </div>
        <div class="col-md-8 d-flex align-items-end justify-content-md-end mt-3 mt-md-0">
            @if($selectedJadwalId)
                <x-ui.button variant="primary" wire:click="createTugas" icon="fas fa-plus">
                    Buat Tugas Baru
                </x-ui.button>
            @endif
        </div>
    </div>

    @if($selectedJadwalId)
        <div class="row g-4">
            @forelse($tugasList as $tugas)
                <div class="col-md-6 col-lg-4">
                    <x-layout.modern-card class="h-100 d-flex flex-column hover-elevate">
                        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                            <h5 class="fw-bold text-dark mb-0 text-truncate" title="{{ $tugas->judul }}">{{ $tugas->judul }}</h5>
                            <x-ui.badge variant="{{ \Carbon\Carbon::parse($tugas->tenggat_waktu)->isPast() ? 'danger' : 'success' }}" icon="{{ \Carbon\Carbon::parse($tugas->tenggat_waktu)->isPast() ? 'fas fa-clock' : 'fas fa-check-circle' }}">
                                {{ \Carbon\Carbon::parse($tugas->tenggat_waktu)->isPast() ? 'Berakhir' : 'Aktif' }}
                            </x-ui.badge>
                        </div>

                        <p class="text-muted small text-truncate-2 mb-3" style="min-height: 40px;">
                            {{ $tugas->deskripsi ?? 'Tidak ada deskripsi.' }}
                        </p>

                        <div class="mb-4">
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="far fa-calendar-alt text-primary me-2 w-15px"></i>
                                Tenggat: <strong class="ms-1 text-dark">{{ \Carbon\Carbon::parse($tugas->tenggat_waktu)->translatedFormat('d M Y, H:i') }}</strong>
                            </div>
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="fas fa-users text-info me-2 w-15px"></i>
                                Terkumpul: <strong class="ms-1 text-dark">{{ $tugas->pengumpulan_count }}</strong> Siswa
                            </div>
                            @if($tugas->file_lampiran)
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="fas fa-paperclip text-secondary me-2 w-15px"></i>
                                    <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank" class="text-decoration-none">Lihat Lampiran</a>
                                </div>
                            @endif
                        </div>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center gap-2">
                            <x-ui.button variant="primary" size="sm" class="flex-grow-1" wire:click="lihatPengumpulan({{ $tugas->id_tugas }})">
                                Cek & Nilai
                            </x-ui.button>
                            <x-ui.button variant="outline" size="sm" class="px-2" wire:click="editTugas({{ $tugas->id_tugas }})" title="Edit">
                                <i class="fas fa-edit text-warning"></i>
                            </x-ui.button>
                            <x-ui.button variant="outline" size="sm" class="px-2" wire:confirm="Yakin ingin menghapus tugas ini?" wire:click="deleteTugas({{ $tugas->id_tugas }})" title="Hapus">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </x-ui.button>
                        </div>
                    </x-layout.modern-card>
                </div>
            @empty
                <div class="col-12">
                    <x-ui.empty-state icon="fas fa-clipboard-list" title="Belum ada tugas" description="Anda belum membuat tugas untuk jadwal/kelas ini." />
                </div>
            @endforelse
        </div>
    @else
        <div class="p-5 text-center bg-white rounded-4 shadow-sm border">
            <i class="fas fa-chalkboard text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
            <h5 class="text-dark fw-bold">Pilih Jadwal Mengajar</h5>
            <p class="text-muted mb-0">Silakan pilih jadwal terlebih dahulu untuk melihat dan mengelola tugas.</p>
        </div>
    @endif

    {{-- Modal Form Tugas --}}
    @if($showModal)
        <div class="modal-backdrop-custom" wire:click.self="$set('showModal', false)">
            <x-layout.modern-card class="modal-content-custom m-auto" style="max-width: 600px; margin-top: 5rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h5 class="mb-0 fw-bold">{{ $isEditing ? 'Edit Tugas' : 'Buat Tugas Baru' }}</h5>
                    <button wire:click="$set('showModal', false)" class="btn-close"></button>
                </div>
                <form wire:submit="saveTugas">
                    <div class="mb-3">
                        <x-form.input label="Judul Tugas" wire:model="judul" required="true" placeholder="Misal: Latihan Soal BAB 1" :error="$errors->first('judul')" />
                    </div>
                    <div class="mb-3">
                        <x-form.textarea label="Deskripsi / Instruksi" wire:model="deskripsi" placeholder="Tuliskan instruksi tugas di sini..." rows="3" />
                    </div>
                    <div class="mb-3">
                        <x-form.input type="datetime-local" label="Tenggat Waktu (Deadline)" wire:model="tenggat_waktu" required="true" :error="$errors->first('tenggat_waktu')" />
                    </div>
                    <div class="mb-4">
                        <x-form.file-upload label="File Lampiran (Opsional)" wire:model="file_lampiran" hint="Maksimal 10MB. Jika ingin mengubah lampiran yang sudah ada, unggah file baru di sini." :error="$errors->first('file_lampiran')" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.rar" />
                    </div>
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <x-ui.button type="button" variant="outline" wire:click="$set('showModal', false)">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">Simpan Tugas</x-ui.button>
                    </div>
                </form>
            </x-layout.modern-card>
        </div>
    @endif

    {{-- Modal Pengumpulan Tugas --}}
    @if($showPengumpulanModal && $selectedTugas)
        <div class="modal-backdrop-custom" wire:click.self="$set('showPengumpulanModal', false)">
            <x-layout.modern-card class="modal-content-custom m-auto" style="max-width: 800px; margin-top: 3rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h5 class="mb-0 fw-bold">Penilaian Tugas: {{ $selectedTugas->judul }}</h5>
                    <button wire:click="$set('showPengumpulanModal', false)" class="btn-close"></button>
                </div>

                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-modern align-middle">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Siswa</th>
                                <th>File Tugas & Catatan</th>
                                <th>Waktu Pengumpulan</th>
                                <th style="width: 150px;">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selectedTugas->pengumpulan as $p)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <x-ui.avatar :name="$p->siswa->nama_siswa" size="sm" class="me-2" />
                                            <span class="fw-medium text-dark">{{ $p->siswa->nama_siswa }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column flex-sm-row gap-2">
                                            @if($p->file_tugas)
                                                <x-ui.button size="sm" variant="danger" href="{{ Storage::url($p->file_tugas) }}" target="_blank" icon="fas fa-download" >Unduh File</x-ui.button>
                                            @else
                                                <span class="text-muted small italic">Tidak ada file</span>
                                            @endif
                                            <x-ui.button size="sm" variant="info" wire:click="lihatDetail({{ $p->id_pengumpulan }})" icon="fas fa-eye" >Detail</x-ui.button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <span class="d-block {{ \Carbon\Carbon::parse($p->waktu_pengumpulan)->isAfter($selectedTugas->tenggat_waktu) ? 'text-danger fw-bold' : 'text-success' }}">
                                                {{ \Carbon\Carbon::parse($p->waktu_pengumpulan)->translatedFormat('d M, H:i') }}
                                            </span>
                                            @if(\Carbon\Carbon::parse($p->waktu_pengumpulan)->isAfter($selectedTugas->tenggat_waktu))
                                                <span class="badge bg-danger mt-1">Terlambat</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control text-center" wire:model="nilai.{{ $p->id_pengumpulan }}" placeholder="0-100" min="0" max="100">
                                            <button class="btn btn-primary" wire:click="saveNilai({{ $p->id_pengumpulan }})"><i class="fas fa-check"></i></button>
                                        </div>
                                        @if (session('success_nilai_' . $p->id_pengumpulan))
                                            <span class="text-success small mt-1 d-block"><i class="fas fa-check-circle me-1"></i>Disimpan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada siswa yang mengumpulkan tugas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end pt-3 mt-3 border-top">
                    <x-ui.button type="button" variant="primary" wire:click="$set('showPengumpulanModal', false)">Tutup</x-ui.button>
                </div>
            </x-layout.modern-card>
        </div>
    @endif

    {{-- Modal Detail Pengumpulan --}}
    @if($showDetailModal && $selectedPengumpulan)
        <div class="modal-backdrop-custom" style="z-index: 1060;" wire:click.self="$set('showDetailModal', false)">
            <x-layout.modern-card class="modal-content-custom m-auto" style="max-width: 500px; margin-top: 5rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h5 class="mb-0 fw-bold">Detail Pengumpulan</h5>
                    <button wire:click="$set('showDetailModal', false)" class="btn-close"></button>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Nama Siswa</label>
                    <p class="fw-bold mb-0 text-dark">{{ $selectedPengumpulan->siswa->nama_siswa }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Waktu Pengumpulan</label>
                    <p class="mb-0 text-dark">
                        {{ \Carbon\Carbon::parse($selectedPengumpulan->waktu_pengumpulan)->translatedFormat('d M Y, H:i') }}
                        @if(\Carbon\Carbon::parse($selectedPengumpulan->waktu_pengumpulan)->isAfter($selectedPengumpulan->tugas->tenggat_waktu))
                            <x-ui.badge variant="danger" class="ms-2">Terlambat</x-ui.badge>
                        @endif
                    </p>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small mb-1">Catatan dari Siswa</label>
                    <div class="p-3 bg-light rounded text-dark" style="min-height: 80px; font-style: italic;">
                        {{ $selectedPengumpulan->catatan ?: 'Tidak ada catatan yang diberikan.' }}
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    @if($selectedPengumpulan->file_tugas)
                        <x-ui.button variant="danger" href="{{ Storage::url($selectedPengumpulan->file_tugas) }}" target="_blank" icon="fas fa-download">Unduh File</x-ui.button>
                    @endif
                    <x-ui.button type="button" variant="primary" wire:click="$set('showDetailModal', false)">Tutup</x-ui.button>
                </div>
            </x-layout.modern-card>
        </div>
    @endif
</div>
