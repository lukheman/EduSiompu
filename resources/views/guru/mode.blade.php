@php
    $styles = <<<'CSS'
<style>
    .mode-container {
        width: 100%;
        max-width: 900px;
        position: relative;
        z-index: 1;
    }

    .mode-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 28px;
        box-shadow: 0 25px 60px -20px rgba(2, 6, 23, 0.45);
        padding: 2.5rem;
        animation: slideUp 0.5s ease-out;
    }

    .mode-head {
        display: flex;
        align-items: center;
        gap: 1.1rem;
        margin-bottom: 1.75rem;
    }

    .mode-avatar {
        width: 68px;
        height: 68px;
        flex: 0 0 68px;
        padding: 3px;
        border-radius: 22px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        box-shadow: 0 10px 25px -10px rgba(13, 148, 136, 0.6);
    }

    .mode-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 19px;
        object-fit: cover;
        background: #fff;
        display: block;
    }

    .mode-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        background: rgba(13, 148, 136, 0.1);
        color: var(--primary-color);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .mode-head h1 {
        font-size: 1.55rem;
        font-weight: 700;
        margin: 0.45rem 0 0.15rem;
        color: var(--text-primary);
        line-height: 1.25;
    }

    .mode-head p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.92rem;
    }

    .mode-current {
        margin-left: auto;
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.7rem;
        border-radius: 10px;
        background: rgba(34, 197, 94, 0.1);
        color: #15803d;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    [data-theme="dark"] .mode-current {
        color: #4ade80;
    }

    .mode-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        padding: 0.85rem 1rem;
        margin-bottom: 1.25rem;
        border-radius: 14px;
        background: rgba(244, 63, 94, 0.08);
        border: 1px solid rgba(244, 63, 94, 0.25);
        color: var(--danger-color);
        font-size: 0.88rem;
    }

    .mode-fieldset {
        border: 0;
        padding: 0;
        margin: 0;
        min-width: 0;
    }

    .mode-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .mode-option {
        position: relative;
        display: block;
        margin: 0;
        cursor: pointer;
    }

    .mode-option.is-disabled {
        cursor: not-allowed;
    }

    .mode-input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        pointer-events: none;
    }

    .mode-option-body {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 1.35rem;
        border: 2px solid var(--border-color);
        border-radius: 20px;
        background: var(--bg-white);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .mode-option:not(.is-disabled):hover .mode-option-body {
        transform: translateY(-3px);
        border-color: var(--accent);
        box-shadow: 0 16px 30px -18px var(--accent);
    }

    .mode-input:checked + .mode-option-body {
        border-color: var(--accent);
        background: var(--soft);
        box-shadow: 0 16px 35px -20px var(--accent);
    }

    .mode-input:focus-visible + .mode-option-body {
        outline: 3px solid var(--accent);
        outline-offset: 3px;
    }

    .mode-check {
        position: absolute;
        top: 1.2rem;
        right: 1.2rem;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid var(--border-color);
        background: var(--bg-white);
        color: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        transition: all 0.2s ease;
    }

    .mode-input:checked + .mode-option-body .mode-check {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }

    .mode-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: var(--soft);
        color: var(--accent);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        margin-bottom: 1rem;
    }

    .mode-title {
        display: block;
        font-size: 1.08rem;
        font-weight: 700;
        color: var(--text-primary);
        padding-right: 1.75rem;
    }

    .mode-sub {
        display: block;
        margin-top: 0.2rem;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .mode-list {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        margin-top: 1rem;
        font-size: 0.86rem;
        color: var(--text-secondary);
    }

    .mode-list i {
        color: var(--accent);
        font-size: 0.7rem;
        margin-right: 0.4rem;
    }

    .mode-note {
        margin-top: auto;
        padding-top: 1rem;
        font-size: 0.78rem;
        color: var(--text-muted);
        line-height: 1.45;
    }

    .mode-option.is-disabled .mode-option-body {
        opacity: 0.7;
        border-style: dashed;
        background: var(--bg-light);
    }

    .mode-unavailable {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: auto;
        padding-top: 1rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--warning-color);
    }

    .mode-submit {
        width: 100%;
        margin-top: 1.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        padding: 0.95rem 1.25rem;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
        font-size: 1rem;
        font-weight: 700;
        transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        box-shadow: 0 16px 30px -12px rgba(13, 148, 136, 0.6);
    }

    .mode-submit:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 35px -12px rgba(13, 148, 136, 0.7);
    }

    .mode-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    .mode-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--border-color);
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .mode-footer-info {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .mode-footer-actions {
        display: inline-flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .mode-link,
    .mode-logout {
        border: none;
        background: none;
        padding: 0;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .mode-link {
        color: var(--primary-color);
    }

    .mode-link:hover {
        color: var(--primary-dark);
    }

    .mode-logout {
        color: var(--danger-color);
    }

    .mode-logout:hover {
        text-decoration: underline;
    }

    .mode-empty {
        padding: 1.25rem;
        border-radius: 16px;
        background: rgba(234, 179, 8, 0.08);
        border: 1px solid rgba(234, 179, 8, 0.3);
        color: #a16207;
        font-size: 0.9rem;
    }

    [data-theme="dark"] .mode-empty {
        color: #facc15;
    }

    @media (max-width: 767px) {
        .mode-card {
            padding: 1.75rem 1.35rem;
            border-radius: 22px;
        }

        .mode-head {
            flex-wrap: wrap;
        }

        .mode-current {
            margin-left: 0;
            order: 3;
        }

        .mode-grid {
            grid-template-columns: 1fr;
        }

        .mode-head h1 {
            font-size: 1.35rem;
        }
    }
</style>
CSS;
@endphp

@component('layouts.guest', ['type' => 'auth', 'title' => 'Pilih Mode Guru - EduSiompu', 'styles' => $styles])
    <div class="mode-container">
        <div class="mode-card">
            <div class="mode-head">
                <div class="mode-avatar">
                    <img src="{{ $guru->avatar_url }}" alt="Foto {{ $guru->nama_guru }}">
                </div>
                <div>
                    <span class="mode-eyebrow"><i class="fas fa-user-cog"></i> Pilih Mode Kerja</span>
                    <h1>Halo, {{ $guru->nama_guru }}</h1>
                    <p>Tentukan peran yang ingin Anda gunakan. Mode bisa diganti kapan saja.</p>
                </div>
                @if($currentMode)
                    <span class="mode-current">
                        <i class="fas fa-circle-check"></i>
                        Aktif: {{ $currentMode === 'wali_kelas' ? 'Wali Kelas' : 'Guru Mata Pelajaran' }}
                    </span>
                @endif
            </div>

            @if($errors->any())
                <div class="mode-alert" role="alert">
                    <i class="fas fa-circle-exclamation mt-1"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('guru.mode.select') }}" id="guruModeForm">
                @csrf

                <fieldset class="mode-fieldset">
                    <legend class="visually-hidden">Pilih mode guru</legend>

                    <div class="mode-grid">
                        {{-- Mode Guru Mata Pelajaran --}}
                        @if($hasMapel)
                            <label class="mode-option" style="--accent: #0d9488; --soft: rgba(13, 148, 136, 0.08);">
                                <input type="radio" name="mode" value="mata_pelajaran" class="mode-input"
                                    @checked(old('mode', $defaultMode) === 'mata_pelajaran')>
                                <span class="mode-option-body">
                                    <span class="mode-check"><i class="fas fa-check"></i></span>
                                    <span class="mode-icon"><i class="fas fa-book-open"></i></span>
                                    <span class="mode-title">Guru Mata Pelajaran</span>
                                    <span class="mode-sub">{{ $jumlahMapel }} penugasan mengajar</span>
                                    <span class="mode-list">
                                        <span><i class="fas fa-check"></i>Kelola materi, tugas &amp; kuis</span>
                                        <span><i class="fas fa-check"></i>Absensi jadwal mengajar</span>
                                        <span><i class="fas fa-check"></i>Input nilai rapor siswa</span>
                                    </span>
                                    <span class="mode-note">Tidak dapat mencetak rapor atau laporan absensi kelas.</span>
                                </span>
                            </label>
                        @else
                            <div class="mode-option is-disabled" style="--accent: #0d9488; --soft: rgba(13, 148, 136, 0.08);">
                                <div class="mode-option-body">
                                    <span class="mode-icon"><i class="fas fa-book-open"></i></span>
                                    <span class="mode-title">Guru Mata Pelajaran</span>
                                    <span class="mode-sub">Belum ada penugasan mengajar</span>
                                    <span class="mode-list">
                                        <span><i class="fas fa-check"></i>Kelola materi, tugas &amp; kuis</span>
                                        <span><i class="fas fa-check"></i>Absensi jadwal mengajar</span>
                                        <span><i class="fas fa-check"></i>Input nilai rapor siswa</span>
                                    </span>
                                    <span class="mode-unavailable"><i class="fas fa-lock"></i> Belum tersedia</span>
                                </div>
                            </div>
                        @endif

                        {{-- Mode Wali Kelas --}}
                        @if($hasWali)
                            <label class="mode-option" style="--accent: #8b5cf6; --soft: rgba(139, 92, 246, 0.08);">
                                <input type="radio" name="mode" value="wali_kelas" class="mode-input"
                                    @checked(old('mode', $defaultMode) === 'wali_kelas')>
                                <span class="mode-option-body">
                                    <span class="mode-check"><i class="fas fa-check"></i></span>
                                    <span class="mode-icon"><i class="fas fa-users"></i></span>
                                    <span class="mode-title">Wali Kelas</span>
                                    <span class="mode-sub">{{ $jumlahWali }} kelas binaan</span>
                                    <span class="mode-list">
                                        <span><i class="fas fa-check"></i>Pantau siswa kelas binaan</span>
                                        <span><i class="fas fa-check"></i>Cetak rapor digital</span>
                                        <span><i class="fas fa-check"></i>Cetak laporan absensi kelas</span>
                                    </span>
                                    <span class="mode-note">Fokus pada administrasi dan laporan kelas.</span>
                                </span>
                            </label>
                        @else
                            <div class="mode-option is-disabled" style="--accent: #8b5cf6; --soft: rgba(139, 92, 246, 0.08);">
                                <div class="mode-option-body">
                                    <span class="mode-icon"><i class="fas fa-users"></i></span>
                                    <span class="mode-title">Wali Kelas</span>
                                    <span class="mode-sub">Belum ditetapkan sebagai wali kelas</span>
                                    <span class="mode-list">
                                        <span><i class="fas fa-check"></i>Pantau siswa kelas binaan</span>
                                        <span><i class="fas fa-check"></i>Cetak rapor digital</span>
                                        <span><i class="fas fa-check"></i>Cetak laporan absensi kelas</span>
                                    </span>
                                    <span class="mode-unavailable"><i class="fas fa-lock"></i> Belum tersedia</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </fieldset>

                @if($hasMapel || $hasWali)
                    <button type="submit" class="mode-submit" id="guruModeSubmit">
                        <span id="guruModeSubmitLabel">Lanjutkan ke Dashboard</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                @else
                    <div class="mode-empty mt-4">
                        <i class="fas fa-circle-info me-2"></i>
                        Akun ini belum memiliki penugasan mengajar atau kelas wali. Hubungi admin untuk melengkapi data.
                    </div>
                @endif
            </form>

            <div class="mode-footer">
                <span class="mode-footer-info">
                    <i class="fas fa-circle-info"></i>
                    Mode dapat diganti melalui menu <strong>Ganti Mode Guru</strong>.
                </span>
                <div class="mode-footer-actions">
                    @if($currentMode)
                        <a href="{{ route('guru.dashboard') }}" class="mode-link">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="mode-logout">
                            <i class="fas fa-right-from-bracket me-1"></i>Bukan akun ini? Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('guruModeForm');
            if (!form) return;

            const inputs = form.querySelectorAll('.mode-input');
            const submit = document.getElementById('guruModeSubmit');
            const label = document.getElementById('guruModeSubmitLabel');
            const modeLabels = {
                mata_pelajaran: 'Guru Mata Pelajaran',
                wali_kelas: 'Wali Kelas',
            };

            function syncSubmit() {
                const selected = form.querySelector('.mode-input:checked');

                if (submit) {
                    submit.disabled = !selected;
                }

                if (label) {
                    label.textContent = selected
                        ? 'Masuk sebagai ' + modeLabels[selected.value]
                        : 'Pilih mode terlebih dahulu';
                }
            }

            inputs.forEach(function (input) {
                input.addEventListener('change', syncSubmit);
            });

            syncSubmit();
        });
    </script>
@endcomponent
