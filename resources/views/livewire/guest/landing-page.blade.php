<div class="landing-wrapper">
    <!-- Hero Section -->
    <section class="hero d-flex align-items-center justify-content-center">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>

        <div class="container hero-container text-center text-white position-relative" style="z-index: 10;">
            <span class="hero-badge mb-3 d-inline-block">
                <i class="fas fa-graduation-cap me-2"></i> Sistem Informasi Akademik Terpadu
            </span>
            <h1 class="hero-title fw-bold display-3 mb-4">
                SMAN 1 Siompu
            </h1>
            <p class="hero-description lead mb-5 mx-auto" style="max-width: 700px;">
                Portal digital resmi SMAN 1 Siompu. Kemudahan akses informasi akademik, manajemen nilai, absensi, dan jadwal pembelajaran dalam satu aplikasi cerdas dan terintegrasi.
            </p>
            <div class="hero-actions d-flex justify-content-center gap-3">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg">
                    <i class="fas fa-sign-in-alt me-2"></i> Masuk Portal
                </a>
                <a href="#tentang" class="btn btn-outline-light btn-lg rounded-pill px-5">
                    <i class="fas fa-info-circle me-2"></i> Pelajari Sistem
                </a>
            </div>
        </div>
    </section>

    <!-- Glassmorphism Feature Section -->
    <section id="tentang" class="features-section py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3" style="color: var(--text-primary);">Mengapa Menggunakan EduSiompu?</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">Sistem kami dirancang khusus untuk mempermudah alur kerja sekolah, meningkatkan transparansi, dan mendekatkan komunikasi antara guru, siswa, dan admin.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card text-center p-4 h-100">
                        <div class="feature-icon bg-primary-subtle text-primary mb-3 mx-auto">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-primary);">Manajemen Pembelajaran</h4>
                        <p class="text-muted mb-0">Platform terpadu untuk pengaturan kelas, pembagian jadwal mengajar guru, dan distribusi mata pelajaran dengan efisien.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center p-4 h-100">
                        <div class="feature-icon bg-info-subtle text-info mb-3 mx-auto">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-primary);">Monitoring Siswa</h4>
                        <p class="text-muted mb-0">Pemantauan progres akademik, pembagian rombongan belajar, dan manajemen biodata siswa secara *real-time*.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center p-4 h-100">
                        <div class="feature-icon bg-success-subtle text-success mb-3 mx-auto">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-primary);">Aman & Transparan</h4>
                        <p class="text-muted mb-0">Didukung oleh keamanan data berlapis dan sistem hak akses bertingkat (RBAC) untuk melindungi informasi rahasia sekolah.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-slot:styles>
        <style>
            .landing-wrapper {
                margin-top: -100px; /* Pulls up to hide behind translucent navs if applicable */
                min-height: 100vh;
            }

            /* Hero Section */
            .hero {
                position: relative;
                min-height: 100vh;
                padding-top: 80px;
                overflow: hidden;
            }

            .hero-bg {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url('/images/school-bg.jpg');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                z-index: 1;
            }

            .hero-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 58, 138, 0.65) 100%);
                z-index: 2;
            }

            .hero-badge {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                padding: 0.6rem 1.5rem;
                border-radius: 50px;
                font-size: 0.95rem;
                font-weight: 500;
                letter-spacing: 0.5px;
            }

            .hero-title {
                text-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
                letter-spacing: -1px;
            }

            .hero-description {
                color: rgba(255, 255, 255, 0.95) !important;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            }

            .btn-outline-light:hover {
                color: #0f172a;
            }

            /* Features */
            .features-section {
                background: var(--bg-color);
                position: relative;
                z-index: 10;
            }

            .glass-card {
                background: var(--card-bg);
                border: 1px solid var(--border-color);
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .glass-card:hover {
                transform: translateY(-15px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                border-color: var(--primary-color);
            }

            [data-theme="dark"] .glass-card {
                background: rgba(30, 41, 59, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }

            [data-theme="dark"] .glass-card:hover {
                border-color: var(--primary-color);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            }

            .feature-icon {
                width: 70px;
                height: 70px;
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8rem;
                transition: transform 0.4s ease;
            }

            .glass-card:hover .feature-icon {
                transform: scale(1.15) rotate(5deg);
            }

            @media (max-width: 768px) {
                .hero-title {
                    font-size: 2.5rem;
                }
                .hero-actions {
                    flex-direction: column;
                }
            }
        </style>
    </x-slot:styles>
</div>
