<div class="login-container">
    <div class="login-card">
        <!-- Brand Logo -->
        <div class="brand-logo">
            <div class="icon-wrapper">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>Selamat Datang</h1>
            <p>Silakan masuk ke portal SMAN 1 Siompu</p>
        </div>

        <!-- Login Form -->
        <form wire:submit="submit">
            
            {{-- Role Selector --}}
            <div class="form-floating position-relative mb-3">
                <i class="fas fa-user-tag input-icon"></i>
                <select wire:model.live="role" class="form-select @error('role') is-invalid @enderror" id="role" style="padding-left: 2.5rem;">
                    <option value="">-- Pilih Role --</option>
                    @foreach($this->roleOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <label for="role" style="padding-left: 2.5rem;">Login Sebagai</label>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Identifier Field -->
            <div class="form-floating position-relative mb-3">
                @php
                    $placeholder = 'Email / NIP / NISN / NIK';
                    $icon = 'fas fa-user';
                    if ($role === 'admin') { $placeholder = 'Email Admin'; $icon = 'fas fa-envelope'; }
                    elseif ($role === 'guru') { $placeholder = 'NIP Guru'; $icon = 'fas fa-id-card'; }
                    elseif ($role === 'siswa') { $placeholder = 'NISN Siswa'; $icon = 'fas fa-user-graduate'; }
                    elseif ($role === 'orang_tua') { $placeholder = 'NIK Orang Tua'; $icon = 'fas fa-id-badge'; }
                @endphp
                <i class="{{ $icon }} input-icon"></i>
                <input type="text" wire:model="identifier" class="form-control @error('identifier') is-invalid @enderror"
                    id="identifier" placeholder="{{ $placeholder }}" autofocus>
                <label for="identifier">{{ $placeholder }}</label>
                @error('identifier')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-floating position-relative mb-3">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" wire:model="password"
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
                    if (input && input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else if (input) {
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
