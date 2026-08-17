<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Phân hệ Cán bộ Kỹ thuật - TLU Helpdesk Staff')">
    <title>@yield('title', 'Kỹ thuật viên') — TLU Helpdesk Staff</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }

        :root {
            --stf-primary:    #10b981;
            --stf-dark:       #064e3b;
            --stf-sidebar-bg: #111827;
            --stf-sidebar-w:  255px;
            --stf-bg:         #f8fafc;
        }

        body {
            background: var(--stf-bg);
            min-height: 100vh;
            display: flex;
        }

        /* ─── SIDEBAR ────────────────────────────────────────── */
        .stf-sidebar {
            width: var(--stf-sidebar-w);
            background: var(--stf-sidebar-bg);
            color: #9ca3af;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: transform 0.25s ease;
        }

        .stf-sidebar .brand-header {
            padding: 1.25rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.2);
        }

        .stf-sidebar .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }

        .stf-sidebar .brand-title {
            font-weight: 700;
            font-size: 1.05rem;
            color: white;
            line-height: 1.2;
        }

        .stf-sidebar .role-badge {
            font-size: 0.68rem;
            background: rgba(16,185,129,0.2);
            color: #34d399;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .sidebar-menu {
            padding: 1rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex-grow: 1;
        }

        .sidebar-heading {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #4b5563;
            padding: 0.5rem 0.75rem 0.25rem;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.65rem 0.85rem;
            border-radius: 10px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
        }

        .sidebar-item i { font-size: 1.1rem; color: #9ca3af; transition: color 0.15s; }

        .sidebar-item:hover {
            background: rgba(255,255,255,0.06);
            color: white;
        }

        .sidebar-item:hover i { color: #34d399; }

        .sidebar-item.active {
            background: var(--stf-primary);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }

        .sidebar-item.active i { color: white; }

        /* ─── MAIN LAYOUT WRAPPER ────────────────────────────── */
        .stf-wrapper {
            margin-left: var(--stf-sidebar-w);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* ─── TOPBAR ─────────────────────────────────────────── */
        .stf-topbar {
            height: 64px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .topbar-toggle {
            border: none;
            background: none;
            font-size: 1.3rem;
            color: #64748b;
            cursor: pointer;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        .topbar-toggle:hover { background: #f1f5f9; }

        .user-dropdown .dropdown-toggle {
            border: none;
            background: #f8fafc;
            border-radius: 10px;
            padding: 0.4rem 0.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #1e293b;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }

        .user-dropdown .dropdown-toggle:hover { background: #f1f5f9; }

        .user-avatar-stf {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #10b981, #047857);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* ─── MAIN CONTENT CONTAINER ─────────────────────────── */
        .stf-content {
            padding: 1.75rem 1.5rem;
            flex-grow: 1;
        }

        /* ─── CARDS & BADGES ─────────────────────────────────── */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        }

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

        /* SLA Flashing Animation */
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(220,53,69,0.7); }
            70% { box-shadow: 0 0 0 8px rgba(220,53,69,0); }
            100% { box-shadow: 0 0 0 0 rgba(220,53,69,0); }
        }

        .sla-overdue-flash {
            animation: pulse-red 1.8s infinite;
        }

        @media (max-width: 992px) {
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
            <div class="sidebar-heading">Bàn làm việc KTV</div>
            <a href="{{ route('staff.workdesk.index') }}" class="sidebar-item {{ request()->routeIs('staff.workdesk.index') ? 'active' : '' }}">
                <i class="bi bi-list-task"></i>
                <span>Workdesk Dạng Bảng</span>
            </a>
            <a href="{{ route('staff.workdesk.kanban') }}" class="sidebar-item {{ request()->routeIs('staff.workdesk.kanban') ? 'active' : '' }}">
                <i class="bi bi-kanban-fill"></i>
                <span>Workdesk Dạng Kanban</span>
            </a>

            <div class="sidebar-heading mt-2">Cá nhân</div>
            <a href="{{ route('staff.profile.index') }}" class="sidebar-item {{ request()->routeIs('staff.profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i>
                <span>Hồ sơ &amp; Ca trực KTV</span>
            </a>
        </nav>

        <div class="p-3 border-top border-secondary border-opacity-10 text-center" style="font-size:0.75rem; color:#64748b;">
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
                <span class="fw-600 text-secondary" style="font-size:0.875rem;">
                    <i class="bi bi-headset me-1"></i> Bàn xử lý Kỹ thuật TLU
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                {{-- User profile dropdown --}}
                <div class="dropdown user-dropdown">
                    <button class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar-stf">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down ms-1" style="font-size:0.65rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <div class="fw-bold" style="font-size:0.85rem;">{{ Auth::user()->name }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email }}</div>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('staff.profile.index') }}">
                                <i class="bi bi-person-badge me-2 text-success"></i> Hồ sơ ca trực
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
        </header>

        {{-- MAIN CONTENT --}}
        <main class="stf-content">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
