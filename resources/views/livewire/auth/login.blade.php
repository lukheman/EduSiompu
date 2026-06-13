<div class="login-container">
    <div class="login-card">
        <!-- Brand Logo -->
        <div class="brand-logo">
            <div class="icon-wrapper">
                <i class="fas fa-layer-group"></i>
            </div>
            <h1>Welcome Back</h1>
            <p>Sign in to continue to EduSiompu</p>
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
        </ul>

        <!-- Login Form -->
        <form wire:submit="submit">
            <!-- Identifier Field -->
            <div class="form-floating position-relative">
                @php
                    $placeholder = $role === 'admin' ? 'Email Admin' : ($role === 'guru' ? 'NIP' : 'NISN');
                    $icon = $role === 'admin' ? 'fas fa-envelope' : ($role === 'guru' ? 'fas fa-chalkboard-teacher' : 'fas fa-user-graduate');
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
            <div class="form-floating position-relative">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" wire:model="password"
                    class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password">
                <label for="password">Password</label>
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
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn btn-login" wire:loading.attr="disabled">
                <span wire:loading.remove>Sign In <i class="fas fa-arrow-right"></i></span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin me-2"></i> Signing in...
                </span>
            </button>
        </form>

        <!-- Divider -->
        <div class="divider">
            <span>or continue with</span>
        </div>


        <!-- Sign Up Link -->
        <div class="signup-link">
            Don't have an account? <a href="{{ route('register') }}">Create Account</a>
        </div>
    </div>
</div>