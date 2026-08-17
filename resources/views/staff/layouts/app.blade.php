<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Phân hệ Cán bộ Kỹ thuật - TLU Helpdesk Staff')">
    <title>@yield('title', 'Kỹ thuật viên') — TLU Helpdesk Staff</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        h1, h2, h3, h4, .brand-title { font-family: 'Outfit', 'Inter', sans-serif; }

        :root {
            --stf-primary:       #0d6efd;
            --stf-primary-dark:  #0a58ca;
            --stf-sidebar-bg:    #0f172a;
            --stf-sidebar-w:     265px;
            --stf-bg:            #f8fafc;
        }

        body {
            background: var(--stf-bg);
            min-height: 100vh;
            display: flex;
            color: #1e293b;
        }

        /* ─── SIDEBAR DESIGN (ROYAL BLUE INDIGO THEME) ─────────── */
        .stf-sidebar {
            width: var(--stf-sidebar-w);
            background: linear-gradient(180deg, #0d6efd 0%, #0369a1 100%);
            color: white;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            box-shadow: 4px 0 24px rgba(13,110,253,0.22);
            transition: transform 0.3s ease;
        }

        .stf-sidebar .brand-header {
            padding: 1.35rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            background: rgba(0,0,0,0.12);
        }

        .stf-sidebar .brand-icon {
            width: 44px;
            height: 44px;
            background: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--stf-primary);
            font-size: 1.4rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }

        .stf-sidebar .brand-title {
            font-weight: 800;
            font-size: 1.15rem;
            color: white;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .stf-sidebar .role-badge {
            font-size: 0.68rem;
            background: rgba(255,255,255,0.22);
            color: #ffffff;
            padding: 3px 9px;
            border-radius: 6px;
            font-weight: 700;
            text-uppercase;
            letter-spacing: 0.06em;
            border: 1px solid rgba(255,255,255,0.35);
        }

        .sidebar-menu {
            padding: 1.25rem 0.85rem;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-grow: 1;
        }

        .sidebar-heading {
            font-size: 0.68rem;
            font-weight: 700;
            text-uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.65);
            padding: 0.75rem 0.75rem 0.35rem;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.7rem 0.95rem;
            border-radius: 12px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-item i {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.75);
            transition: color 0.2s;
        }

        .sidebar-item:hover {
            background: rgba(255,255,255,0.18);
            color: white;
            transform: translateX(4px);
        }

        .sidebar-item:hover i { color: white; }

        .sidebar-item.active {
            background: white;
            color: var(--stf-primary);
            font-weight: 700;
            box-shadow: 0 4px 18px rgba(0,0,0,0.14);
        }

        .sidebar-item.active i { color: var(--stf-primary); }

        /* ─── MAIN LAYOUT WRAPPER ────────────────────────────── */
        .stf-wrapper {
            margin-left: var(--stf-sidebar-w);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* ─── TOPBAR DESIGN ──────────────────────────────────── */
        .stf-topbar {
            height: 68px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        .topbar-toggle {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #475569;
            cursor: pointer;
            padding: 0;
        }

        .user-dropdown .dropdown-toggle {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.4rem 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.15s;
        }

        .user-dropdown .dropdown-toggle:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .user-avatar-stf {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #0d6efd 0%, #0284c7 100%);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 2px 8px rgba(13,110,253,0.25);
        }

        /* ─── MAIN CONTENT CONTAINER ─────────────────────────── */
        .stf-content {
            padding: 1.75rem 1.75rem;
            flex-grow: 1;
        }

        /* ─── CARDS & BADGES ─────────────────────────────────── */
        .card {
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.04);
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 600;
        }

        .badge-OPEN        { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-IN_PROGRESS { background: #fefce8; color: #a16207; border: 1px solid #fef08a; }
        .badge-RESOLVED    { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-CLOSED      { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
        .badge-REOPENED    { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        .badge-priority-HIGH   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-priority-MEDIUM { background: #fefce8; color: #a16207; border: 1px solid #fef08a; }
        .badge-priority-LOW    { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

        /* SLA countdown bar badge */
        .sla-badge-bar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 8px;
        }
        .sla-ok       { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .sla-warning  { background: #fefce8; color: #a16207; border: 1px solid #fef08a; }
        .sla-danger   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; animation: slaPulse 1.5s infinite; }

        @keyframes slaPulse {
            0%, 100% { opacity: 1; }
            50%      { opacity: 0.6; }
        }

        @media (max-width: 991.98px) {
            .stf-sidebar { transform: translateX(-100%); }
            .stf-sidebar.show { transform: translateX(0); }
            .stf-wrapper { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    <aside class="stf-sidebar" id="stfSidebar">
        <div class="brand-header">
            <div class="brand-icon">
                <i class="bi bi-tools"></i>
            </div>
            <div>
                <div class="brand-title">TLU Helpdesk</div>
                <span class="role-badge">Cán bộ Kỹ thuật</span>
            </div>
        </div>

        <nav class="sidebar-menu">
            <div class="sidebar-heading">Giao diện Bàn làm việc</div>
            <a href="{{ route('staff.workdesk.index') }}" class="sidebar-item {{ request()->routeIs('staff.workdesk.index') ? 'active' : '' }}">
                <i class="bi bi-table"></i>
                <span>Workdesk Dạng Bảng</span>
            </a>
            <a href="{{ route('staff.workdesk.kanban') }}" class="sidebar-item {{ request()->routeIs('staff.workdesk.kanban') ? 'active' : '' }}">
                <i class="bi bi-kanban-fill"></i>
                <span>Workdesk Thẻ Kanban</span>
            </a>

            <div class="sidebar-heading mt-2">Thiết lập Ca trực</div>
            <a href="{{ route('staff.profile.index') }}" class="sidebar-item {{ request()->routeIs('staff.profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i>
                <span>Hồ sơ &amp; Ca trực KTV</span>
            </a>
        </nav>

        <div class="p-3 border-top border-white border-opacity-10 text-center text-white-50" style="font-size:0.75rem;">
            TLU Staff Workspace v1.0
        </div>
    </aside>

    {{-- MAIN WRAPPER --}}
    <div class="stf-wrapper">

        {{-- TOPBAR --}}
        <header class="stf-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="topbar-toggle d-lg-none" onclick="document.getElementById('stfSidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <span class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size:0.95rem;">
                    <i class="bi bi-cpu text-primary fs-5"></i> Bàn làm việc Kỹ thuật viên TLU
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                {{-- Ca trực --}}
                @php $shift = Auth::user()->staffProfile?->shift ?? 'Ca Trực'; @endphp
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold d-none d-md-inline" style="font-size:0.78rem;">
                    <i class="bi bi-clock me-1"></i> {{ $shift }}
                </span>

                {{-- User profile dropdown --}}
                <div class="dropdown user-dropdown">
                    <button class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar-stf">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down ms-1 text-muted" style="font-size:0.65rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2" style="min-width:210px;">
                        <li>
                            <div class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold" style="font-size:0.85rem; color:#1e293b;">{{ Auth::user()->name }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email }}</div>
                            </div>
                        </li>
                        <li>
                            <a href="{{ route('staff.profile.index') }}" class="dropdown-item py-2 rounded-2" style="font-size:0.85rem;">
                                <i class="bi bi-person me-2 text-primary"></i> Hồ sơ Ca trực KTV
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2 rounded-2" style="font-size:0.85rem;">
                                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="stf-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer class="bg-white border-top py-3 text-center text-muted" style="font-size: 0.8rem;">
            &copy; {{ date('Y') }} Đại học Thủy Lợi — Phân hệ Kỹ thuật viên (Staff Workspace)
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tự động ẩn thông báo Alert sau 4 giây (4000ms)
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(function (alertEl) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                        const alert = bootstrap.Alert.getOrCreateInstance(alertEl);
                        if (alert) alert.close();
                    } else {
                        alertEl.style.transition = 'opacity 0.5s ease';
                        alertEl.style.opacity = '0';
                        setTimeout(() => alertEl.remove(), 500);
                    }
                });
            }, 4000);
        });
    </script>
    @stack('scripts')
</body>
</html>
