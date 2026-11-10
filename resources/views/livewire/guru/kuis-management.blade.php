<div>
    <x-layout.page-header title="Manajemen Kuis" subtitle="Buat kuis pilihan ganda, kelola soal, dan pantau hasil siswa." />

    @if (session('success'))
        <x-ui.toast variant="success">
            {{ session('success') }}
        </x-ui.toast>
    @endif

    <div class="row mb-4">
        <div class="col-md-5">
            <label class="form-label fw-bold">Pilih Kelas / Jadwal</label>
            <x-form.select wire:model.live="selectedJadwalId"
                :options="$jadwals->mapWithKeys(fn($j) => [$j->id_jadwal_pelajaran => $j->guruAmpu->kelas->nama_kelas . ' - ' . $j->guruAmpu->mataPelajaran->nama_mapel . ' (' . $j->hari . ')'])->toArray()"
                placeholder="-- Pilih Jadwal --" />
        </div>
        <div class="col-md-7 d-flex align-items-end justify-content-md-end mt-3 mt-md-0">
            @if($selectedJadwalId)
                <x-ui.button variant="primary" wire:click="createKuis" icon="fas fa-plus">
                    Buat Kuis Baru
                </x-ui.button>
            @endif
        </div>
    </div>

    @if($selectedJadwalId)
        <div class="row g-4">
            @forelse($kuisList as $kuis)
                <div class="col-md-6 col-lg-4" wire:key="kuis-{{ $kuis->id_kuis }}">
                    <x-layout.modern-card class="h-100 d-flex flex-column hover-elevate">
                        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                            <h5 class="fw-bold text-dark mb-0 text-truncate" title="{{ $kuis->judul }}">{{ $kuis->judul }}</h5>
                            <x-ui.badge variant="{{ $kuis->soal_count === 0 ? 'warning' : ($kuis->tenggat_waktu->isPast() ? 'danger' : 'success') }}"
                                icon="{{ $kuis->soal_count === 0 ? 'fas fa-exclamation-triangle' : ($kuis->tenggat_waktu->isPast() ? 'fas fa-clock' : 'fas fa-check-circle') }}">
                                {{ $kuis->soal_count === 0 ? 'Belum Ada Soal' : ($kuis->tenggat_waktu->isPast() ? 'Berakhir' : 'Aktif') }}
                            </x-ui.badge>
                        </div>

                        <p class="text-muted small text-truncate-2 mb-3" style="min-height: 40px;">
                            {{ $kuis->deskripsi ?: 'Tidak ada deskripsi.' }}
                        </p>

                        <div class="mb-4">
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="far fa-calendar-alt text-primary me-2"></i>
                                Tenggat: <strong class="ms-1 text-dark">{{ $kuis->tenggat_waktu->translatedFormat('d M Y, H:i') }}</strong>
                            </div>
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="fas fa-hourglass-half text-warning me-2"></i>
                                Durasi: <strong class="ms-1 text-dark">{{ $kuis->durasi_menit }} menit</strong>
                            </div>
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="fas fa-list-ol text-info me-2"></i>
                                Soal: <strong class="ms-1 text-dark">{{ $kuis->soal_count }}</strong>
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-users text-secondary me-2"></i>
                                Dikerjakan: <strong class="ms-1 text-dark">{{ $kuis->pengerjaan_count }}</strong> Siswa
                            </div>
                        </div>

                        <div class="mt-auto pt-3 border-top">
                            <div class="d-flex gap-2 mb-2">
                                <x-ui.button variant="primary" size="sm" class="flex-grow-1" wire:click="kelolaSoal({{ $kuis->id_kuis }})">
                                    Kelola Soal
                                </x-ui.button>
                                <x-ui.button variant="info" size="sm" class="flex-grow-1" wire:click="lihatHasil({{ $kuis->id_kuis }})">
                                    Hasil
                                </x-ui.button>
                            </div>
                            <div class="d-flex gap-2">
                                <x-ui.button variant="outline" size="sm" class="flex-grow-1" wire:click="editKuis({{ $kuis->id_kuis }})">
                                    <i class="fas fa-edit text-warning me-1"></i> Edit
                                </x-ui.button>
                                <x-ui.button variant="outline" size="sm" class="flex-grow-1"
                                    wire:confirm="Yakin ingin menghapus kuis ini beserta soal dan hasil pengerjaannya?"
                                    wire:click="deleteKuis({{ $kuis->id_kuis }})">
                                    <i class="fas fa-trash-alt text-danger me-1"></i> Hapus
                                </x-ui.button>
                            </div>
                        </div>
                    </x-layout.modern-card>
                </div>
            @empty
                <div class="col-12">
                    <x-ui.empty-state icon="fas fa-question-circle" title="Belum ada kuis"
                        description="Anda belum membuat kuis untuk jadwal/kelas ini." />
                </div>
            @endforelse
        </div>
    @else
        <div class="p-5 text-center bg-white rounded-4 shadow-sm border">
            <i class="fas fa-chalkboard text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
            <h5 class="text-dark fw-bold">Pilih Jadwal Mengajar</h5>
            <p class="text-muted mb-0">Silakan pilih jadwal terlebih dahulu untuk mengelola kuis.</p>
        </div>
    @endif

    {{-- Modal Form Kuis --}}
    @if($showModal)
        <div class="modal-backdrop-custom" wire:click.self="$set('showModal', false)">
            <x-layout.modern-card class="modal-content-custom m-auto" style="max-width: 600px; max-height: 90vh; overflow-y: auto; margin-top: 5rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h5 class="mb-0 fw-bold">{{ $isEditing ? 'Edit Kuis' : 'Buat Kuis Baru' }}</h5>
                    <button wire:click="$set('showModal', false)" class="btn-close"></button>
                </div>
                <form wire:submit="saveKuis">
                    <div class="mb-3">
                        <x-form.input label="Judul Kuis" wire:model="judul" required="true" placeholder="Misal: Kuis Bab 1 - Himpunan" :error="$errors->first('judul')" />
                    </div>
                    <div class="mb-3">
                        <x-form.textarea label="Deskripsi / Instruksi" wire:model="deskripsi" placeholder="Tuliskan petunjuk pengerjaan kuis..." rows="3" />
                    </div>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <x-form.input type="datetime-local" label="Tenggat Waktu" wire:model="tenggat_waktu" required="true" :error="$errors->first('tenggat_waktu')" />
                        </div>
                        <div class="col-md-5">
                            <x-form.input type="number" label="Durasi (Menit)" wire:model="durasi_menit" required="true" min="1" max="600" :error="$errors->first('durasi_menit')" />
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-3">
                        <x-ui.button type="button" variant="outline" wire:click="$set('showModal', false)">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">Simpan Kuis</x-ui.button>
                    </div>
                </form>
            </x-layout.modern-card>
        </div>
    @endif

    {{-- Modal Kelola Soal --}}
    @if($showSoalModal && $selectedKuis)
        <div class="modal-backdrop-custom" wire:click.self="$set('showSoalModal', false)">
            <x-layout.modern-card class="modal-content-custom m-auto" style="max-width: 900px; max-height: 90vh; overflow-y: auto; margin-top: 3rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div>
                        <h5 class="mb-0 fw-bold">Kelola Soal: {{ $selectedKuis->judul }}</h5>
                        <div class="small text-muted mt-1">{{ $selectedKuis->soal->count() }} soal &bull; {{ $selectedKuis->soal->sum('poin') }} poin total</div>
                    </div>
                    <button wire:click="$set('showSoalModal', false)" class="btn-close"></button>
                </div>

                @if (session('success_soal'))
                    <div class="alert alert-success py-2">{{ session('success_soal') }}</div>
                @endif

                <div class="bg-light rounded p-3 mb-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas {{ $soalId ? 'fa-edit text-warning' : 'fa-plus-circle text-primary' }} me-1"></i>
                        {{ $soalId ? 'Edit Soal' : 'Tambah Soal Baru' }}
                    </h6>
                    <form wire:submit="saveSoal">
                        <div class="mb-3">
                            <x-form.textarea label="Pertanyaan" wire:model="pertanyaan" placeholder="Tulis pertanyaan kuis..." rows="2" :error="$errors->first('pertanyaan')" />
                        </div>
                        <div class="row g-3">
                            @foreach(['A' => 'pilihan_a', 'B' => 'pilihan_b', 'C' => 'pilihan_c', 'D' => 'pilihan_d'] as $huruf => $field)
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Pilihan {{ $huruf }}</label>
                                    <input type="text" class="form-control @error($field) is-invalid @enderror" wire:model="{{ $field }}" placeholder="Isi pilihan {{ $huruf }}">
                                    @error($field) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endforeach
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Jawaban Benar</label>
                                <select class="form-select @error('jawaban_benar') is-invalid @enderror" wire:model="jawaban_benar">
                                    @foreach(['A', 'B', 'C', 'D'] as $opsi)
                                        <option value="{{ $opsi }}">Pilihan {{ $opsi }}</option>
                                    @endforeach
                                </select>
                                @error('jawaban_benar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Poin</label>
                                <input type="number" class="form-control @error('poin') is-invalid @enderror" wire:model="poin" min="1" max="100">
                                @error('poin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            @if($soalId)
                                <x-ui.button type="button" variant="outline" wire:click="resetSoalForm">Batal Edit</x-ui.button>
                            @endif
                            <x-ui.button type="submit" variant="primary" icon="fas fa-save">{{ $soalId ? 'Perbarui Soal' : 'Tambah Soal' }}</x-ui.button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive" style="max-height: 380px;">
                    <table class="table table-modern align-middle">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Soal & Pilihan</th>
                                <th style="width: 80px;" class="text-center">Kunci</th>
                                <th style="width: 70px;" class="text-center">Poin</th>
                                <th style="width: 110px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selectedKuis->soal as $index => $soal)
                                <tr wire:key="soal-{{ $soal->id_soal }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark mb-1">{{ $soal->pertanyaan }}</div>
                                        <div class="small text-muted">
                                            @foreach($soal->pilihan() as $huruf => $teks)
                                                <span class="{{ $soal->jawaban_benar === $huruf ? 'text-success fw-bold' : '' }}">{{ $huruf }}. {{ $teks }}</span>@if(!$loop->last) &nbsp;|&nbsp; @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center"><x-ui.badge variant="success">{{ $soal->jawaban_benar }}</x-ui.badge></td>
                                    <td class="text-center">{{ $soal->poin }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <x-ui.button variant="outline" size="sm" wire:click="editSoal({{ $soal->id_soal }})" title="Edit">
                                                <i class="fas fa-edit text-warning"></i>
                                            </x-ui.button>
                                            <x-ui.button variant="outline" size="sm" wire:confirm="Hapus soal ini?" wire:click="deleteSoal({{ $soal->id_soal }})" title="Hapus">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </x-ui.button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada soal. Tambahkan soal pertama di atas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end pt-3 mt-3 border-top">
                    <x-ui.button type="button" variant="primary" wire:click="$set('showSoalModal', false)">Tutup</x-ui.button>
                </div>
            </x-layout.modern-card>
        </div>
    @endif

    {{-- Modal Hasil Kuis --}}
    @if($showHasilModal && $hasilKuis)
        <div class="modal-backdrop-custom" style="z-index: 1060;" wire:click.self="$set('showHasilModal', false)">
            <x-layout.modern-card class="modal-content-custom m-auto" style="max-width: 800px; max-height: 90vh; overflow-y: auto; margin-top: 4rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div>
                        <h5 class="mb-0 fw-bold">Hasil Kuis: {{ $hasilKuis->judul }}</h5>
                        <div class="small text-muted mt-1">{{ $hasilKuis->pengerjaan->count() }} siswa telah mengerjakan</div>
                    </div>
                    <button wire:click="$set('showHasilModal', false)" class="btn-close"></button>
                </div>

                <div class="table-responsive" style="max-height: 420px;">
                    <table class="table table-modern align-middle">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Nama Siswa</th>
                                <th class="text-center">Benar / Soal</th>
                                <th class="text-center">Nilai</th>
                                <th>Waktu Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hasilKuis->pengerjaan as $pengerjaan)
                                <tr wire:key="hasil-{{ $pengerjaan->id_pengerjaan }}">
                                    <td class="fw-medium text-dark">{{ $pengerjaan->siswa?->nama_siswa ?? '-' }}</td>
                                    <td class="text-center">{{ $pengerjaan->jumlah_benar }} / {{ $pengerjaan->jumlah_soal }}</td>
                                    <td class="text-center">
                                        <x-ui.badge variant="{{ $pengerjaan->nilai >= 75 ? 'success' : ($pengerjaan->nilai >= 60 ? 'warning' : 'danger') }}" class="fs-6">
                                            {{ $pengerjaan->selesai() ? $pengerjaan->nilai : 'Belum Selesai' }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $pengerjaan->waktu_selesai?->translatedFormat('d M Y, H:i') ?? 'Sedang mengerjakan' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada siswa yang mengerjakan kuis ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end pt-3 mt-3 border-top">
                    <x-ui.button type="button" variant="primary" wire:click="$set('showHasilModal', false)">Tutup</x-ui.button>
                </div>
            </x-layout.modern-card>
        </div>
    @endif
</div>
