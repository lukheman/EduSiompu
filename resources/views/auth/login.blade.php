@component('layouts.guest', ['type' => 'auth'])
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
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                
                {{-- Role Selector --}}
                <div class="form-floating position-relative mb-3">
                    <i class="fas fa-user-tag input-icon"></i>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" id="role" style="padding-left: 2.5rem;" onchange="updatePlaceholder()">
                        <option value="">-- Pilih Role --</option>
                        <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="orang_tua" {{ old('role') == 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                    </select>
                    <label for="role" style="padding-left: 2.5rem;">Login Sebagai</label>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Identifier Field -->
                <div class="form-floating position-relative mb-3">
                    <i id="identifierIcon" class="fas fa-user input-icon"></i>
                    <input type="text" name="identifier" value="{{ old('identifier') }}" class="form-control @error('identifier') is-invalid @enderror"
                        id="identifier" placeholder="Email / NIP / NISN / NIK" autofocus>
                    <label for="identifier" id="identifierLabel">Email / NIP / NISN / NIK</label>
                    @error('identifier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-floating position-relative mb-3">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password"
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

                    function updatePlaceholder() {
                        const role = document.getElementById('role').value;
                        const label = document.getElementById('identifierLabel');
                        const icon = document.getElementById('identifierIcon');
                        
                        let placeholder = 'Email / NIP / NISN / NIK';
                        let iconClass = 'fas fa-user';
                        
                        if (role === 'admin') { 
                            placeholder = 'Email Admin'; 
                            iconClass = 'fas fa-envelope'; 
                        } else if (role === 'guru') { 
                            placeholder = 'NIP Guru'; 
                            iconClass = 'fas fa-id-card'; 
                        } else if (role === 'siswa') { 
                            placeholder = 'NISN Siswa'; 
                            iconClass = 'fas fa-user-graduate'; 
                        } else if (role === 'orang_tua') { 
                            placeholder = 'NIK Orang Tua'; 
                            iconClass = 'fas fa-id-badge'; 
                        }
                        
                        label.textContent = placeholder;
                        document.getElementById('identifier').placeholder = placeholder;
                        icon.className = iconClass + ' input-icon';
                    }
                    
                    // Initialize placeholder on page load
                    document.addEventListener('DOMContentLoaded', updatePlaceholder);
                </script>

                <!-- Remember Me & Forgot Password (Removed) -->
                <div class="mb-4"></div>

                <button type="submit" class="btn btn-login">
                    <span>Masuk <i class="fas fa-arrow-right ms-2"></i></span>
                </button>
            </form>

        </div>
    </div>
@endcomponent
