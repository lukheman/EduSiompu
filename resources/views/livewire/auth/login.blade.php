<div class="login-container">
    <style>
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
            color: white;
            border: none;
        }
        .nav-pills .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        .nav-pills .nav-link:hover:not(.active) {
            background: rgba(13, 148, 136, 0.08);
            color: var(--primary-color);
            border: 1px solid rgba(13, 148, 136, 0.2);
        }
    </style>
    <div class="login-card">
        <!-- Brand Logo -->
        <div class="brand-logo">
            <div class="icon-wrapper">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>Selamat Datang</h1>
            <p>Silakan masuk ke portal SMAN 1 Siompu</p>
        </div>

        <!-- Role Tabs -->
        <ul class="nav nav-pills nav-justified mb-4" style="gap: 5px;">
            <li class="nav-item">
                <button class="nav-link {{ $role === 'siswa' ? 'active' : '' }}" wire:click="setRole('siswa')" type="button" style="border-radius: 8px;">
                    <i class="fas fa-user-graduate mb-1 d-block"></i> Siswa
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $role === 'guru' ? 'active' : '' }}" wire:click="setRole('guru')" type="button" style="border-radius: 8px;">
                    <i class="fas fa-chalkboard-teacher mb-1 d-block"></i> Guru
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $role === 'admin' ? 'active' : '' }}" wire:click="setRole('admin')" type="button" style="border-radius: 8px;">
                    <i class="fas fa-user-shield mb-1 d-block"></i> Admin
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $role === 'orang_tua' ? 'active' : '' }}" wire:click="setRole('orang_tua')" type="button" style="border-radius: 8px;">
                    <i class="fas fa-users mb-1 d-block"></i> Orang Tua
                </button>
            </li>
        </ul>

        <!-- Login Form -->
        <form wire:submit="submit" x-data @submit="$wire.identifier = $refs.identifier.value; $wire.password = $refs.password.value">
            <!-- Identifier Field -->
            <div class="form-floating position-relative">
                @php
                    $placeholder = $role === 'admin' ? 'Email Admin' : ($role === 'guru' ? 'NIP Guru' : ($role === 'orang_tua' ? 'NIK Orang Tua' : 'NISN Siswa'));
                    $icon = $role === 'admin' ? 'fas fa-envelope' : ($role === 'guru' ? 'fas fa-id-card' : ($role === 'orang_tua' ? 'fas fa-id-badge' : 'fas fa-user-graduate'));
                @endphp
                <i class="{{ $icon }} input-icon"></i>
                <input type="text" wire:model="identifier" x-ref="identifier" class="form-control @error('identifier') is-invalid @enderror"
                    id="identifier" placeholder="{{ $placeholder }}" autofocus>
                <label for="identifier">{{ $placeholder }}</label>
                @error('identifier')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-floating position-relative">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" wire:model="password" x-ref="password"
                    class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Kata Sandi">
                <label for="password">Kata Sandi</label>
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <script>
                function togglePassword() {
                    const input = document.getElementById('password');
                    const icon = document.getElementById('toggleIcon');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                }
            </script>

            <!-- Remember Me & Forgot Password -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" wire:model="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat Saya</label>
                </div>
                <a href="#" class="forgot-password">Lupa Sandi?</a>
            </div>

            <button type="submit" class="btn btn-login" wire:loading.attr="disabled">
                <span wire:loading.remove>Masuk <i class="fas fa-arrow-right ms-2"></i></span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin me-2"></i> Memproses...
                </span>
            </button>
        </form>

    </div>
</div>
