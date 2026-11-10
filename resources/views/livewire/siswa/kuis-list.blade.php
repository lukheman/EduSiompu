<div>
    <x-layout.page-header title="Kuis" subtitle="Kerjakan kuis pilihan ganda yang diberikan guru mata pelajaran Anda." />

    @if (session('success'))
        <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
    @endif

    @if (session('error'))
        <x-ui.toast variant="danger">{{ session('error') }}</x-ui.toast>
    @endif

    <x-ui.tabs variant="pills" class="mb-4">
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'tersedia' ? 'active' : '' }}" wire:click="$set('activeTab', 'tersedia')">
                <i class="fas fa-pen-to-square me-2"></i>Belum Dikerjakan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'selesai' ? 'active' : '' }}" wire:click="$set('activeTab', 'selesai')">
                <i class="fas fa-check-circle me-2"></i>Sudah Dikerjakan
            </button>
        </li>
    </x-ui.tabs>

    <div class="row g-4">
        @forelse($kuisList as $kuis)
            @php
                $pengerjaan = $kuis->pengerjaan->first();
                $sedangDikerjakan = $pengerjaan && ! $pengerjaan->selesai();
                $berakhir = $kuis->tenggat_waktu->isPast();
            @endphp
            <div class="col-md-6 col-lg-4" wire:key="kuis-{{ $kuis->id_kuis }}">
                <x-layout.modern-card class="h-100 d-flex flex-column hover-elevate">
                    <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">{{ $kuis->judul }}</h5>
                            <x-ui.badge variant="primary" class="opacity-75">{{ $kuis->jadwalPelajaran->guruAmpu->mataPelajaran->nama_mapel }}</x-ui.badge>
                        </div>
                        @if($sedangDikerjakan)
                            <x-ui.badge variant="warning" icon="fas fa-hourglass-half">Lanjutkan</x-ui.badge>
                        @elseif($berakhir)
                            <x-ui.badge variant="danger" icon="fas fa-clock">Berakhir</x-ui.badge>
                        @else
                            <x-ui.badge variant="success" icon="fas fa-check-circle">Aktif</x-ui.badge>
                        @endif
                    </div>

                    <p class="text-muted small text-truncate-3 mb-3" style="min-height: 60px;">
                        {{ $kuis->deskripsi ?: 'Tidak ada instruksi tambahan.' }}
                    </p>

                    <div class="mb-4 bg-light rounded p-3">
                        <div class="d-flex align-items-center text-muted small mb-2">
                            <i class="far fa-calendar-alt text-primary me-2"></i>
                            Tenggat: <strong class="ms-1 {{ $berakhir && ! $pengerjaan ? 'text-danger' : 'text-dark' }}">{{ $kuis->tenggat_waktu->translatedFormat('d M Y, H:i') }}</strong>
                        </div>
                        <div class="d-flex align-items-center text-muted small mb-2">
                            <i class="fas fa-hourglass-half text-warning me-2"></i>
                            Durasi: <strong class="ms-1 text-dark">{{ $kuis->durasi_menit }} menit</strong>
                        </div>
                        <div class="d-flex align-items-center text-muted small mb-2">
                            <i class="fas fa-list-ol text-info me-2"></i>
                            Jumlah soal: <strong class="ms-1 text-dark">{{ $kuis->soal_count }}</strong>
                        </div>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="fas fa-user-tie text-secondary me-2"></i>
                            Guru: <strong class="ms-1 text-dark">{{ $kuis->jadwalPelajaran->guruAmpu->guru->nama_guru ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="mt-auto border-top pt-3">
                        @if($activeTab === 'selesai' && $pengerjaan)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-muted">{{ $pengerjaan->waktu_selesai?->translatedFormat('d M Y, H:i') }}</span>
                                <x-ui.badge variant="{{ $pengerjaan->nilai >= 75 ? 'success' : ($pengerjaan->nilai >= 60 ? 'warning' : 'danger') }}" class="fs-6">
                                    Nilai: {{ $pengerjaan->nilai }}
                                </x-ui.badge>
                            </div>
                            <x-ui.button variant="info" size="sm" class="w-100" wire:click="showHasil({{ $pengerjaan->id_pengerjaan }})">
                                <i class="fas fa-eye me-1"></i> Lihat Hasil
                            </x-ui.button>
                        @elseif($sedangDikerjakan)
                            <x-ui.button variant="warning" size="sm" class="w-100" wire:click="bukaKuis({{ $kuis->id_kuis }})">
                                <i class="fas fa-play me-1"></i> Lanjutkan Kuis
                            </x-ui.button>
                        @elseif($kuis->soal_count === 0)
                            <x-ui.button variant="secondary" size="sm" class="w-100" disabled>
                                <i class="fas fa-ban me-1"></i> Belum Ada Soal
                            </x-ui.button>
                        @elseif($berakhir)
                            <x-ui.button variant="secondary" size="sm" class="w-100" disabled>
                                <i class="fas fa-lock me-1"></i> Waktu Berakhir
                            </x-ui.button>
                        @else
                            <x-ui.button variant="primary" size="sm" class="w-100" wire:click="bukaKuis({{ $kuis->id_kuis }})">
                                <i class="fas fa-play me-1"></i> Mulai Kuis
                            </x-ui.button>
                        @endif
                    </div>
                </x-layout.modern-card>
            </div>
        @empty
            <div class="col-12">
                <x-ui.empty-state icon="{{ $activeTab === 'selesai' ? 'fas fa-check-circle' : 'fas fa-question-circle' }}"
                    title="{{ $activeTab === 'selesai' ? 'Belum ada kuis selesai' : 'Tidak ada kuis' }}"
                    description="{{ $activeTab === 'selesai' ? 'Anda belum menyelesaikan kuis apa pun.' : 'Belum ada kuis yang tersedia untuk kelas Anda saat ini.' }}" />
            </div>
        @endforelse
    </div>

    {{-- Modal Pengerjaan Kuis --}}
    @if($showKuisModal && $selectedKuis && $pengerjaanAktif)
        <div class="modal-backdrop-custom" wire:click.self="$set('showKuisModal', false)">
            <div class="modal-content-custom m-auto" style="max-width: 820px; max-height: 92vh; overflow-y: auto; margin-top: 2rem !important;"
                x-data="{
                    sisa: {{ $sisaDetik }},
                    timer: null,
                    init() {
                        this.timer = setInterval(() => {
                            if (this.sisa > 0) {
                                this.sisa--;
                            } else {
                                clearInterval(this.timer);
                                $wire.submitKuis();
                            }
                        }, 1000);
                    },
                    destroy() {
                        clearInterval(this.timer);
                    },
                    format() {
                        const m = Math.floor(this.sisa / 60);
                        const s = this.sisa % 60;
                        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                    }
                }">
                <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="mb-1 fw-bold">{{ $selectedKuis->judul }}</h5>
                        <div class="small text-muted">{{ $selectedKuis->jadwalPelajaran->guruAmpu->mataPelajaran->nama_mapel }} &bull; {{ $selectedKuis->soal->count() }} soal</div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted mb-1">Sisa waktu</div>
                        <div class="badge bg-warning text-dark fs-5" x-text="format()">{{ gmdate('i:s', $sisaDetik) }}</div>
                    </div>
                </div>

                @if($selectedKuis->deskripsi)
                    <div class="alert alert-info py-2 small">{{ $selectedKuis->deskripsi }}</div>
                @endif

                @error('jawaban')
                    <div class="alert alert-danger py-2">{{ $message }}</div>
                @enderror

                <form wire:submit="submitKuis">
                    @foreach($selectedKuis->soal as $index => $soal)
                        <div class="border rounded p-3 mb-3 bg-white" wire:key="soal-{{ $soal->id_soal }}">
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="text-dark">Soal {{ $index + 1 }}</strong>
                                <span class="badge bg-light text-dark border">{{ $soal->poin }} poin</span>
                            </div>
                            <p class="mb-3">{{ $soal->pertanyaan }}</p>
                            @foreach($soal->pilihan() as $huruf => $teks)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio"
                                        id="jawaban-{{ $soal->id_soal }}-{{ $huruf }}"
                                        value="{{ $huruf }}"
                                        wire:model.live="jawaban.{{ $soal->id_soal }}">
                                    <label class="form-check-label" for="jawaban-{{ $soal->id_soal }}-{{ $huruf }}">
                                        <strong>{{ $huruf }}.</strong> {{ $teks }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <x-ui.button type="button" variant="outline" wire:click="$set('showKuisModal', false)">Tutup</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-paper-plane">Kumpulkan Jawaban</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Hasil Kuis --}}
    @if($showHasilModal && $hasilPengerjaan)
        <div class="modal-backdrop-custom" style="z-index: 1060;" wire:click.self="$set('showHasilModal', false)">
            <div class="modal-content-custom m-auto" style="max-width: 820px; max-height: 92vh; overflow-y: auto; margin-top: 2rem !important;">
                <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="mb-1 fw-bold">Hasil: {{ $hasilPengerjaan->kuis->judul }}</h5>
                        <div class="small text-muted">{{ $hasilPengerjaan->kuis->jadwalPelajaran?->guruAmpu?->mataPelajaran?->nama_mapel }}</div>
                    </div>
                    <button wire:click="$set('showHasilModal', false)" class="btn-close"></button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light text-center">
                            <div class="small text-muted mb-1">Nilai</div>
                            <div class="fs-2 fw-bold text-primary">{{ $hasilPengerjaan->nilai }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light text-center">
                            <div class="small text-muted mb-1">Jawaban Benar</div>
                            <div class="fs-2 fw-bold text-success">{{ $hasilPengerjaan->jumlah_benar }} / {{ $hasilPengerjaan->jumlah_soal }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light text-center">
                            <div class="small text-muted mb-1">Selesai</div>
                            <div class="fw-bold">{{ $hasilPengerjaan->waktu_selesai?->translatedFormat('d M Y, H:i') }}</div>
                        </div>
                    </div>
                </div>

                @foreach($hasilPengerjaan->kuis->soal as $index => $soal)
                    @php
                        $jawabanSiswa = $hasilPengerjaan->jawaban->firstWhere('id_soal', $soal->id_soal);
                        $jawabanHuruf = $jawabanSiswa?->jawaban;
                        $benar = $jawabanHuruf !== null && $jawabanHuruf === $soal->jawaban_benar;
                    @endphp
                    <div class="border rounded p-3 mb-3 {{ $benar ? 'border-success bg-success-subtle' : 'border-danger bg-danger-subtle' }}">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Soal {{ $index + 1 }}</strong>
                            <x-ui.badge variant="{{ $benar ? 'success' : 'danger' }}">
                                {{ $benar ? 'Benar' : 'Salah' }}
                            </x-ui.badge>
                        </div>
                        <p class="mb-2">{{ $soal->pertanyaan }}</p>
                        <div class="small">
                            <div>Jawaban Anda: <strong>{{ $jawabanHuruf ?? '-' }}.</strong> {{ $soal->pilihan()[$jawabanHuruf] ?? '-' }}</div>
                            @if(! $benar)
                                <div class="text-success mt-1">Jawaban benar: <strong>{{ $soal->jawaban_benar }}.</strong> {{ $soal->pilihan()[$soal->jawaban_benar] }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-end border-top pt-3">
                    <x-ui.button type="button" variant="primary" wire:click="$set('showHasilModal', false)">Tutup</x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
