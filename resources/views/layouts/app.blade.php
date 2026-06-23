@props([
    'title' => 'EduSiompu',
    'brandName' => 'EduSiompu',
    'brandIcon' => 'fas fa-graduation-cap'
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-7.2.0-web/css/all.min.css')}}">

    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css')}}">

    @livewireStyles
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

        :root {
            --sidebar-width: 280px;
            --topbar-height: 70px;
            --primary-color: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #2dd4bf;
            --secondary-color: #8b5cf6;
            --success-color: #22c55e;
            --warning-color: #eab308;
            --danger-color: #f43f5e;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);

            --bs-primary: #0d9488;
            --bs-primary-rgb: 13, 148, 136;
            --bs-secondary: #8b5cf6;
            --bs-secondary-rgb: 139, 92, 246;
            --bs-success: #22c55e;
            --bs-success-rgb: 34, 197, 94;
            --bs-warning: #eab308;
            --bs-warning-rgb: 234, 179, 8;
            --bs-danger: #f43f5e;
            --bs-danger-rgb: 244, 63, 94;
            --bs-info: #0ea5e9;
            --bs-info-rgb: 14, 165, 233;

            /* Light theme (default) */
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --border-light: #f8fafc;
            --input-bg: #ffffff;
            --hover-bg: #f1f5f9;
        }

        [data-theme="dark"] {
            --bg-primary: #09090b;
            --bg-secondary: #18181b;
            --bg-tertiary: #27272a;
            --text-primary: #fafafa;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;
            --border-color: #27272a;
            --border-light: #3f3f46;
            --input-bg: #18181b;
            --hover-bg: #27272a;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.5rem 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand i {
            font-size: 1.8rem;
        }

        .sidebar-menu {
            padding: 1.5rem 0;
        }

        .menu-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            margin: 0.25rem 0.75rem;
            border-radius: 12px;
            font-weight: 500;
        }

        .sidebar-menu a:hover {
            background: var(--hover-bg);
            color: var(--primary-color);
        }

        .sidebar-menu a.active {
            background: var(--primary-color);
            color: white;
        }

        .sidebar-menu a i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            background: var(--bg-secondary);
            height: var(--topbar-height);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            transition: background-color 0.3s ease;
        }

        .topbar .form-control {
            background: var(--input-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .topbar .form-control::placeholder {
            color: var(--text-muted);
        }

        .topbar .input-group-text {
            background: var(--input-bg);
            border-color: var(--border-color);
        }

        .content-area {
            padding: 2rem;
        }

        .modern-card {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 1.75rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
        }

        .modern-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        [data-theme="dark"] .modern-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        .stat-card {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 1.75rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            border: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--accent-color);
        }

        .stat-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }

        [data-theme="dark"] .stat-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .preview-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-light);
        }

        .btn-modern {
            padding: 0.625rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary-modern {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-primary-modern:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .alert-modern {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: start;
            gap: 12px;
        }

        .progress-modern {
            height: 8px;
            border-radius: 50px;
            background: var(--border-light);
        }

        .progress-bar-modern {
            border-radius: 50px;
        }



        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block !important;
            }
        }

        .mobile-toggle {
            display: none;
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .table-modern thead th {
            border: none;
            background: transparent;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.75rem 1rem;
        }

        .table-modern tbody tr {
            background: var(--bg-secondary);
            box-shadow: var(--card-shadow);
            border-radius: 8px;
        }

        .table-modern tbody td {
            padding: 1rem;
            border: none;
            vertical-align: middle;
            color: var(--text-primary);
        }

        .table-modern tbody tr td:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .table-modern tbody tr td:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        /* Bootstrap Table Dark Mode Override */
        .table {
            --bs-table-bg: var(--bg-secondary);
            --bs-table-color: var(--text-primary);
            --bs-table-border-color: var(--border-color);
            --bs-table-striped-bg: var(--bg-tertiary);
            --bs-table-striped-color: var(--text-primary);
            --bs-table-hover-bg: var(--hover-bg);
            --bs-table-hover-color: var(--text-primary);
        }

        .table > :not(caption) > * > * {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            border-bottom-color: var(--border-color);
        }

        .table-modern tbody tr {
            background: var(--bg-secondary) !important;
        }

        .table-modern tbody tr:hover {
            background: var(--hover-bg) !important;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            background: var(--hover-bg);
            color: var(--primary-color);
        }

        .theme-toggle i {
            font-size: 1.25rem;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .topbar .user-name {
            color: var(--text-primary);
        }

        .topbar .user-role {
            color: var(--text-muted);
        }

        /* Modal Styles */
        .modal-backdrop-custom {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content-custom {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-color);
        }

        .modal-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title-custom {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .modal-close-btn:hover {
            color: var(--danger-color);
        }

        .form-label {
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            background: var(--input-bg);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Input Group Dark Mode */
        .input-group-text {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        .input-group .form-control {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .input-group .form-control:focus {
            background: var(--input-bg);
            border-color: var(--primary-color);
            color: var(--text-primary);
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Action buttons in table */
        .action-btn {
            background: transparent;
            border: none;
            padding: 0.5rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: var(--hover-bg);
        }

        .action-btn-edit {
            color: var(--primary-color);
        }

        .action-btn-delete {
            color: var(--danger-color);
        }

        .action-btn-view {
            color: var(--secondary-color);
        }

        /* Pagination */
        .pagination {
            --bs-pagination-bg: var(--bg-secondary);
            --bs-pagination-color: var(--text-primary);
            --bs-pagination-border-color: var(--border-color);
            --bs-pagination-hover-bg: var(--hover-bg);
            --bs-pagination-hover-color: var(--primary-color);
            --bs-pagination-focus-bg: var(--hover-bg);
            --bs-pagination-active-bg: var(--primary-color);
            --bs-pagination-active-border-color: var(--primary-color);
            --bs-pagination-disabled-bg: var(--bg-tertiary);
            --bs-pagination-disabled-color: var(--text-muted);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <x-layout.sidebar :brand-name="$brandName" :brand-icon="$brandIcon">
        <x-layout.sidebar-section title="Menu Utama">
            @if(Auth::guard('siswa')->check())
                <x-layout.sidebar-link href="{{ route('siswa.dashboard') }}" icon="fas fa-home" :active="request()->routeIs('siswa.dashboard')">Dashboard</x-layout.sidebar-link>
            @elseif(Auth::guard('guru')->check())
                <x-layout.sidebar-link href="{{ route('guru.dashboard') }}" icon="fas fa-home" :active="request()->routeIs('guru.dashboard')">Dashboard</x-layout.sidebar-link>
            @elseif(Auth::guard('orang_tua')->check())
                <x-layout.sidebar-link href="{{ route('orang-tua.dashboard') }}" icon="fas fa-home" :active="request()->routeIs('orang-tua.dashboard')">Dashboard</x-layout.sidebar-link>
            @else
                <x-layout.sidebar-link href="{{ route('admin.dashboard') }}" icon="fas fa-home" :active="request()->routeIs('admin.dashboard')">Dashboard</x-layout.sidebar-link>
            @endif
        </x-layout.sidebar-section>

        @if(Auth::guard('admin')->check())
            <x-layout.sidebar-section title="Data Induk">
                <x-layout.sidebar-link href="{{ route('admin.tahun-ajaran') }}" icon="fas fa-calendar-alt" :active="request()->routeIs('admin.tahun-ajaran')">Tahun Ajaran</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('admin.kelas') }}" icon="fas fa-chalkboard" :active="request()->routeIs('admin.kelas')">Kelas</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('admin.mata-pelajaran') }}" icon="fas fa-book" :active="request()->routeIs('admin.mata-pelajaran')">Mata Pelajaran</x-layout.sidebar-link>
            </x-layout.sidebar-section>

            <x-layout.sidebar-section title="Data Personalia">
                <x-layout.sidebar-link href="{{ route('admin.guru') }}" icon="fas fa-chalkboard-teacher" :active="request()->routeIs('admin.guru')">Data Guru</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('admin.siswa') }}" icon="fas fa-user-graduate" :active="request()->routeIs('admin.siswa')">Data Siswa</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('admin.orang-tua') }}" icon="fas fa-users" :active="request()->routeIs('admin.orang-tua')">Data Orang Tua</x-layout.sidebar-link>
            </x-layout.sidebar-section>

            <x-layout.sidebar-section title="Akademik">
                <x-layout.sidebar-link href="{{ route('admin.guru-ampu') }}" icon="fas fa-user-tag" :active="request()->routeIs('admin.guru-ampu')">Penugasan Guru</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('admin.jadwal-pelajaran') }}" icon="fas fa-clock" :active="request()->routeIs('admin.jadwal-pelajaran')">Jadwal Pelajaran</x-layout.sidebar-link>
            </x-layout.sidebar-section>
        @endif

        @if(Auth::guard('guru')->check())
            <x-layout.sidebar-section title="Akademik">
                <x-layout.sidebar-link href="{{ route('guru.materi') }}" icon="fas fa-file-alt" :active="request()->routeIs('guru.materi')">Materi Pembelajaran</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('guru.jadwal-absensi') }}" icon="fas fa-calendar-check" :active="request()->routeIs('guru.jadwal-absensi')">Jadwal & Absensi</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('guru.input-nilai') }}" icon="fas fa-edit" :active="request()->routeIs('guru.input-nilai')">Input Nilai Raport</x-layout.sidebar-link>
            </x-layout.sidebar-section>
        @endif

        @if(Auth::guard('siswa')->check())
            <x-layout.sidebar-section title="Akademik">
                <x-layout.sidebar-link href="{{ route('siswa.materi') }}" icon="fas fa-book-open" :active="request()->routeIs('siswa.materi')">Materi Belajar</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('siswa.absensi') }}" icon="fas fa-clipboard-user" :active="request()->routeIs('siswa.absensi')">Absensi Saya</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('siswa.raport') }}" icon="fas fa-award" :active="request()->routeIs('siswa.raport')">Raport Digital</x-layout.sidebar-link>
            </x-layout.sidebar-section>
        @endif

        @if(Auth::guard('orang_tua')->check())
            <x-layout.sidebar-section title="Akademik Anak">
                <x-layout.sidebar-link href="{{ route('orang-tua.absensi') }}" icon="fas fa-clipboard-user" :active="request()->routeIs('orang-tua.absensi')">Absensi Anak</x-layout.sidebar-link>
                <x-layout.sidebar-link href="{{ route('orang-tua.raport') }}" icon="fas fa-award" :active="request()->routeIs('orang-tua.raport')">Raport Digital Anak</x-layout.sidebar-link>
            </x-layout.sidebar-section>
        @endif

        <x-layout.sidebar-section title="Account">
            @if(Auth::guard('siswa')->check())
                <x-layout.sidebar-link href="{{ route('siswa.profile') }}" icon="fas fa-user-circle" :active="request()->routeIs('siswa.profile')">Profil Saya</x-layout.sidebar-link>
            @elseif(Auth::guard('guru')->check())
                <x-layout.sidebar-link href="{{ route('guru.profile') }}" icon="fas fa-user-circle" :active="request()->routeIs('guru.profile')">Profil Guru</x-layout.sidebar-link>
            @elseif(Auth::guard('orang_tua')->check())
                <x-layout.sidebar-link href="#" icon="fas fa-user-circle" :active="false">Profil Orang Tua</x-layout.sidebar-link>
            @else
                <x-layout.sidebar-link href="{{ route('admin.profile') }}" icon="fas fa-user-circle" :active="request()->routeIs('admin.profile')">Profil Admin</x-layout.sidebar-link>
            @endif
        </x-layout.sidebar-section>



    </x-layout.sidebar>

    <!-- Main Content -->
    <div class="main-content">
        @php
            $roleName = 'Guest';
            $user = null;
            $profileRoute = '#';
            if (Auth::guard('admin')->check()) {
                $roleName = 'Administrator';
                $user = Auth::guard('admin')->user();
                $profileRoute = route('admin.profile');
            }
            elseif (Auth::guard('guru')->check()) {
                $roleName = 'Guru';
                $user = Auth::guard('guru')->user();
                $profileRoute = route('guru.profile');
            }
            elseif (Auth::guard('siswa')->check()) {
                $roleName = 'Siswa';
                $user = Auth::guard('siswa')->user();
                $profileRoute = route('siswa.profile');
            }
            elseif (Auth::guard('orang_tua')->check()) {
                $roleName = 'Orang Tua';
                $user = Auth::guard('orang_tua')->user();
                $profileRoute = '#';
            }
        @endphp

        <!-- Top Bar -->
        <x-layout.topbar
            :user-name="$user?->nama ?? $user?->nama_guru ?? $user?->nama_siswa ?? $user?->nama_orang_tua ?? 'Guest'"
            :user-role="$roleName"
            :user-avatar="$user?->avatar"
            :profile-route="$profileRoute"
            :notification-count="0"
            :show-logout="true"
        />

        <!-- Content Area -->
        <div class="content-area">
            {{ $slot }}
        </div>
    </div>

    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Theme Toggle Functionality
        function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (prefersDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }

            updateThemeIcon();
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon) {
                themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
            }
        }

        // Initialize theme on page load
        initTheme();

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
    </script>
    @livewireScripts
</body>
</html>
