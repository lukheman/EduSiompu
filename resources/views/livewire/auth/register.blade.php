<div class="login-container">
    <div class="login-card">
        <!-- Brand Logo -->
        <div class="brand-logo">
            <div class="icon-wrapper">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>Daftar Akun Baru</h1>
            <p>Bergabung ke portal SMAN 1 Siompu</p>
        </div>

        <!-- Register Form -->
        <form wire:submit="submit">
            <!-- Name Field -->
            <div class="form-floating position-relative">
                <i class="fas fa-user input-icon"></i>
                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" id="name"
                    placeholder="Nama Lengkap" autofocus>
                <label for="name">Nama Lengkap</label>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Field -->
            <div class="form-floating position-relative">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror"
                    id="email" placeholder="Alamat Email">
                <label for="email">Alamat Email</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-floating position-relative">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" wire:model="password"
                    class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Kata Sandi">
                <label for="password">Kata Sandi</label>
                <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                    <i class="fas fa-eye" id="toggleIcon1"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password Field -->
            <div class="form-floating position-relative">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" wire:model="password_confirmation" class="form-control"
                    id="password_confirmation" placeholder="Konfirmasi Sandi">
                <label for="password_confirmation">Konfirmasi Sandi</label>
                <button type="button" class="password-toggle"
                    onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                    <i class="fas fa-eye" id="toggleIcon2"></i>
                </button>
            </div>

            <!-- Terms and Conditions -->
            <div class="form-check mb-4">
                <input class="form-check-input @error('agree_terms') is-invalid @enderror" type="checkbox"
                    wire:model="agree_terms" id="agree_terms">
                <label class="form-check-label" for="agree_terms">
                    Saya setuju dengan <a href="#" class="forgot-password">Syarat & Ketentuan</a> dan <a href="#"
                        class="forgot-password">Kebijakan Privasi</a>
                </label>
                @error('agree_terms')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-login" wire:loading.attr="disabled">
                <span wire:loading.remove>Daftar <i class="fas fa-arrow-right ms-2"></i></span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin me-2"></i> Mendaftarkan...
                </span>
            </button>
        </form>

        <!-- Sign In Link -->
        <div class="signup-link mt-4">
            Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>