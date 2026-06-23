<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <x-layout.page-header title="Tugas Kelas" subtitle="Lihat daftar tugas yang belum dan sudah dikerjakan.">
    </x-layout.page-header>

    @if (session('success'))
        <x-ui.alert variant="success" class="mb-4">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <button class="nav-link {{ $activeTab == 'belum_selesai' ? 'active btn-primary-modern' : 'bg-white text-muted border' }} fw-bold px-4 py-2" wire:click="$set('activeTab', 'belum_selesai')">
                <i class="fas fa-tasks me-2"></i>Belum Selesai
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab == 'selesai' ? 'active btn-primary-modern' : 'bg-white text-muted border' }} fw-bold px-4 py-2" wire:click="$set('activeTab', 'selesai')">
                <i class="fas fa-check-circle me-2"></i>Sudah Selesai
            </button>
        </li>
    </ul>

    <div class="row g-4">
        @forelse($tugasList as $tugas)
            <div class="col-md-6 col-lg-4">
                <x-layout.modern-card class="h-100 d-flex flex-column hover-elevate">
                    <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">{{ $tugas->judul }}</h5>
                            <x-ui.badge variant="primary" class="opacity-75">{{ $tugas->jadwalPelajaran->guruAmpu->mataPelajaran->nama_mapel }}</x-ui.badge>
                        </div>
                    </div>
                    
                    <p class="text-muted small text-truncate-3 mb-3" style="min-height: 60px;">
                        {{ $tugas->deskripsi ?? 'Tidak ada instruksi tambahan.' }}
                    </p>
                    
                    <div class="mb-4 bg-light rounded p-3">
                        <div class="d-flex align-items-center text-muted small mb-2">
                            <i class="far fa-calendar-alt text-primary me-2 w-15px"></i>
                            Tenggat: <strong class="ms-1 {{ \Carbon\Carbon::parse($tugas->tenggat_waktu)->isPast() && $activeTab == 'belum_selesai' ? 'text-danger' : 'text-dark' }}">{{ \Carbon\Carbon::parse($tugas->tenggat_waktu)->translatedFormat('d M Y, H:i') }}</strong>
                        </div>
                        <div class="d-flex align-items-center text-muted small mb-2">
                            <i class="fas fa-user-tie text-info me-2 w-15px"></i>
                            Guru: <strong class="ms-1 text-dark">{{ $tugas->jadwalPelajaran->guruAmpu->guru->nama_guru }}</strong>
                        </div>
                        @if($tugas->file_lampiran)
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-paperclip text-secondary me-2 w-15px"></i>
                                <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank" class="text-decoration-none">Lihat Lampiran Materi</a>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-auto border-top pt-3">
                        @if($activeTab == 'selesai')
                            @php $pengumpulan = $tugas->pengumpulan->first(); @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-muted">Dikumpulkan: {{ \Carbon\Carbon::parse($pengumpulan->waktu_pengumpulan)->translatedFormat('d M Y') }}</span>
                                <x-ui.badge variant="{{ $pengumpulan->nilai !== null ? 'success' : 'warning' }}" class="fs-6">
                                    {{ $pengumpulan->nilai !== null ? 'Nilai: ' . $pengumpulan->nilai : 'Belum Dinilai' }}
                                </x-ui.badge>
                            </div>
                            <x-ui.button variant="outline" size="sm" class="w-100 mt-2" wire:click="openSubmitModal({{ $tugas->id_tugas }})">
                                Edit Pengumpulan
                            </x-ui.button>
                        @else
                            <x-ui.button variant="primary" size="sm" class="w-100" wire:click="openSubmitModal({{ $tugas->id_tugas }})">
                                Kumpulkan Tugas
                            </x-ui.button>
                        @endif
                    </div>
                </x-layout.modern-card>
            </div>
        @empty
            <div class="col-12">
                <x-ui.empty-state icon="{{ $activeTab == 'selesai' ? 'fas fa-check-circle' : 'fas fa-tasks' }}" 
                    title="{{ $activeTab == 'selesai' ? 'Belum ada tugas selesai' : 'Hore! Tidak ada tugas' }}" 
                    description="{{ $activeTab == 'selesai' ? 'Anda belum mengumpulkan tugas apa pun.' : 'Anda sudah menyelesaikan semua tugas. Waktunya bersantai!' }}" />
            </div>
        @endforelse
    </div>

    {{-- Modal Pengumpulan Tugas --}}
    @if($showSubmitModal && $selectedTugas)
        <div class="modal-backdrop-custom" wire:click.self="$set('showSubmitModal', false)">
            <x-layout.modern-card class="modal-content-custom m-auto" style="max-width: 600px; margin-top: 5rem !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h5 class="mb-0 fw-bold">Pengumpulan: {{ $selectedTugas->judul }}</h5>
                    <button wire:click="$set('showSubmitModal', false)" class="btn-close"></button>
                </div>
                <form wire:submit="submitTugas">
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="fw-bold text-dark mb-2">Instruksi:</h6>
                        <p class="small text-muted mb-0">{{ $selectedTugas->deskripsi ?? 'Tidak ada instruksi.' }}</p>
                    </div>

                    <div class="mb-3">
                        <x-form.file-upload label="File Tugas (PDF, DOCX, dll)" wire:model="file_tugas" required="true" hint="Maksimal 10MB." :error="$errors->first('file_tugas')" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.rar" />
                    </div>

                    <div class="mb-4">
                        <x-form.textarea label="Catatan (Opsional)" wire:model="catatan" placeholder="Tulis catatan untuk guru jika ada..." rows="3" />
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <x-ui.button type="button" variant="outline" wire:click="$set('showSubmitModal', false)">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-paper-plane">Kumpulkan</x-ui.button>
                    </div>
                </form>
            </x-layout.modern-card>
        </div>
    @endif
</div>