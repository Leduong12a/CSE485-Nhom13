<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Hệ thống Helpdesk CNTT & Cơ sở vật chất - Đại học Thủy Lợi')">
    <title>@yield('title', 'TLU Helpdesk') — TLU Helpdesk</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }

        :root {
            --tlu-primary:    #0d6efd;
            --tlu-dark:       #004085;
            --tlu-success:    #198754;
            --tlu-warning:    #ffc107;
            --tlu-danger:     #dc3545;
            --tlu-secondary:  #6c757d;
            --tlu-bg:         #f0f4f8;
            --tlu-sidebar-w:  260px;
        }

        body {
            background: var(--tlu-bg);
            min-height: 100vh;
        }

        /* ─── TOP NAVBAR ─────────────────────────────────────── */
        .tlu-navbar {
            background: linear-gradient(90deg, var(--tlu-dark) 0%, var(--tlu-primary) 100%);
            padding: 0 1.5rem;
            height: 60px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0,0,0,0.18);
        }

        .tlu-navbar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
            font-weight: 700;
            font-size: 1.05rem;
            white-space: nowrap;
        }

        .tlu-navbar .brand i {
            font-size: 1.4rem;
            opacity: 0.95;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-left: 2rem;
        }

        .nav-links a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 0.4rem 0.9rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a:hover, .nav-links a.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .nav-links a.btn-new-ticket {
            background: white;
            color: var(--tlu-primary);
            font-weight: 600;
            padding: 0.4rem 1rem;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .nav-links a.btn-new-ticket:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            background: #f0f8ff;
        }

        .nav-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Notification Bell */
        .btn-notif {
            position: relative;
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 10px;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-notif:hover { background: rgba(255,255,255,0.25); }

        .notif-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 8px;
            height: 8px;
            background: #ff4757;
            border-radius: 50%;
            border: 2px solid var(--tlu-primary);
        }

        /* User dropdown */
        .user-dropdown .dropdown-toggle {
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 10px;
            padding: 0.35rem 0.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }

        .user-dropdown .dropdown-toggle:hover { background: rgba(255,255,255,0.25); }

        .user-avatar {
            width: 30px;
            height: 30px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--tlu-primary);
            font-size: 0.9rem;
            font-weight: 700;
        }

        .user-info-text {
            line-height: 1.2;
            text-align: left;
        }

        .user-info-text .u-name {
            font-size: 0.82rem;
            font-weight: 600;
            display: block;
        }

        .user-info-text .u-dept {
            font-size: 0.7rem;
            opacity: 0.8;
            display: block;
        }

        .dropdown-menu {
            border: none;
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 0.5rem;
            min-width: 200px;
        }

        .dropdown-item {
            border-radius: 8px;
            font-size: 0.85rem;
            padding: 0.5rem 0.8rem;
        }

        /* ─── MAIN CONTENT ───────────────────────────────────── */
        .main-content {
            padding: 1.75rem 1.5rem;
            max-width: 1280px;
            margin: 0 auto;
        }

        /* ─── PAGE HEADER ────────────────────────────────────── */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .page-header .breadcrumb {
            font-size: 0.8rem;
            margin: 0;
        }

        /* ─── FLASH ALERTS ───────────────────────────────────── */
        .flash-success {
            background: linear-gradient(90deg, #d1fae5, #ecfdf5);
            border: none;
            border-left: 4px solid var(--tlu-success);
            border-radius: 10px;
            color: #065f46;
            font-size: 0.875rem;
        }

        .flash-error {
            background: linear-gradient(90deg, #fee2e2, #fef2f2);
            border: none;
            border-left: 4px solid var(--tlu-danger);
            border-radius: 10px;
            color: #7f1d1d;
            font-size: 0.875rem;
        }

        /* ─── CARDS ──────────────────────────────────────────── */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 16px 16px 0 0 !important;
            padding: 1rem 1.25rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #1e293b;
        }

        /* ─── BADGES ─────────────────────────────────────────── */
        .badge-status {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.3em 0.7em;
            border-radius: 6px;
        }

        .badge-OPEN       { background: #e0f2fe; color: #0369a1; }
        .badge-IN_PROGRESS{ background: #fef9c3; color: #92400e; }
        .badge-RESOLVED   { background: #dcfce7; color: #166534; }
        .badge-CLOSED     { background: #f3f4f6; color: #374151; }
        .badge-REOPENED   { background: #fce7f3; color: #9d174d; }

        .badge-priority-LOW    { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-priority-MEDIUM { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-priority-HIGH   { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }

        /* ─── TOOLTIP ─────────────────────────────────────────── */
        @media (max-width: 576px) {
            .nav-links .nav-label { display: none; }
            .main-content { padding: 1rem 0.75rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- TOP NAVIGATION BAR --}}
    <nav class="tlu-navbar">
        <a href="{{ route('student.tickets.index') }}" class="brand">
            <i class="bi bi-headset"></i>
            TLU Helpdesk
        </a>

        <div class="nav-links d-none d-md-flex">
            <a href="{{ route('student.tickets.index') }}" class="{{ request()->routeIs('student.tickets.index') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                <span class="nav-label">Sự cố của tôi</span>
            </a>
            <a href="{{ route('student.tickets.create') }}" class="btn-new-ticket">
                <i class="bi bi-plus-circle-fill"></i>
                <span class="nav-label">+ Báo sự cố</span>
            </a>
        </div>

        <div class="nav-right">
            {{-- Mobile: Nút tạo ticket --}}
            <a href="{{ route('student.tickets.create') }}" class="btn-notif d-md-none text-white" title="Báo sự cố mới">
                <i class="bi bi-plus-circle-fill"></i>
            </a>

            {{-- Notification Bell --}}
            <button class="btn-notif" title="Thông báo" id="notifToggle">
                <i class="bi bi-bell-fill"></i>
                <span class="notif-badge"></span>
            </button>

            {{-- User Profile Dropdown --}}
            <div class="dropdown user-dropdown">
                <button class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info-text d-none d-sm-block">
                        <span class="u-name">{{ Str::limit(Auth::user()->name, 18) }}</span>
                        <span class="u-dept">{{ Auth::user()->department?->name ?? 'Sinh viên TLU' }}</span>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size:0.65rem; opacity:0.7;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <div class="px-3 py-2 border-bottom mb-1">
                            <div class="fw-600" style="font-size:0.85rem; color:#1e293b;">{{ Auth::user()->name }}</div>
                            <div style="font-size:0.75rem; color:#6c757d;">{{ Auth::user()->email }}</div>
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('student.profile.index') }}">
                            <i class="bi bi-person-circle me-2 text-primary"></i> Hồ sơ cá nhân
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert flash-success d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert flash-error d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tự động ẩn thông báo Alert sau 4 giây (4000ms)
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                const alerts = document.querySelectorAll('.flash-success, .flash-error, .alert-dismissible');
                alerts.forEach(function (alertEl) {
                    alertEl.style.transition = 'opacity 0.5s ease';
                    alertEl.style.opacity = '0';
                    setTimeout(() => alertEl.remove(), 500);
                });
            }, 4000);
        });
    </script>
    @stack('scripts')
</body>
</html>
