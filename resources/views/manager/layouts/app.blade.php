<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'TLU Helpdesk - Workspace Quản trị viên & Trưởng bộ phận')">
    <title>@yield('title', 'Quản trị viên') — TLU Helpdesk Manager</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <style>
        * { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }

        :root {
            --mgr-primary:    #0d6efd;
            --mgr-primary-dark:#0a58ca;
            --mgr-dark:       #0f172a;
            --mgr-sidebar-bg: #1e293b;
            --mgr-bg:         #f8fafc;
            --mgr-sidebar-w:  270px;
        }

        body {
            background: var(--mgr-bg);
            min-height: 100vh;
            display: flex;
            color: #1e293b;
        }

        /* ─── SIDEBAR DESIGN (ROYAL MANAGER THEME) ───────────── */
        .mgr-sidebar {
            width: var(--mgr-sidebar-w);
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: white;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(0,0,0,0.12);
            transition: transform 0.3s ease;
        }

        .mgr-sidebar .brand-header {
            padding: 1.25rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.2);
        }

        .mgr-sidebar .brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #0d6efd 0%, #0284c7 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.35rem;
            box-shadow: 0 4px 14px rgba(13,110,253,0.4);
        }

        .mgr-sidebar .brand-title {
            font-weight: 800;
            font-size: 1.1rem;
            color: white;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .mgr-sidebar .role-badge {
            font-size: 0.68rem;
            background: linear-gradient(135deg, rgba(13,110,253,0.3) 0%, rgba(2,132,199,0.3) 100%);
            color: #60a5fa;
            padding: 3px 9px;
            border-radius: 6px;
            font-weight: 700;
            text-uppercase;
            letter-spacing: 0.05em;
            border: 1px solid rgba(96,165,250,0.2);
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
            color: #64748b;
            padding: 0.75rem 0.75rem 0.35rem;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.7rem 0.95rem;
            border-radius: 12px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-item i {
            font-size: 1.15rem;
            color: #64748b;
            transition: color 0.2s;
        }

        .sidebar-item:hover {
            background: rgba(255,255,255,0.07);
            color: #f8fafc;
            transform: translateX(3px);
        }

        .sidebar-item:hover i { color: #60a5fa; }

        .sidebar-item.active {
            background: linear-gradient(135deg, #0d6efd 0%, #0284c7 100%);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(13,110,253,0.35);
        }

        .sidebar-item.active i { color: white; }

        /* ─── MAIN LAYOUT WRAPPER ────────────────────────────── */
        .mgr-wrapper {
            margin-left: var(--mgr-sidebar-w);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* ─── TOPBAR DESIGN ──────────────────────────────────── */
        .mgr-topbar {
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

        .user-avatar-mgr {
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
        .mgr-content {
            padding: 1.75rem 1.75rem;
            flex-grow: 1;
        }

        /* ─── CARDS & BADGES ─────────────────────────────────── */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.05);
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

        @media (max-width: 991.98px) {
            .mgr-sidebar { transform: translateX(-100%); }
            .mgr-sidebar.show { transform: translateX(0); }
            .mgr-wrapper { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    <aside class="mgr-sidebar" id="mgrSidebar">
        <div class="brand-header">
            <div class="brand-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div>
                <div class="brand-title">TLU Helpdesk</div>
                <span class="role-badge">Trưởng bộ phận</span>
            </div>
        </div>

        <nav class="sidebar-menu">
            <div class="sidebar-heading">Tổng quan Hệ thống</div>
            <a href="{{ route('manager.dashboard') }}" class="sidebar-item {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Analytics Dashboard</span>
            </a>

            <div class="sidebar-heading mt-2">Điều hành Sự cố</div>
            <a href="{{ route('manager.tickets.index') }}" class="sidebar-item {{ request()->routeIs('manager.tickets.*') ? 'active' : '' }}">
                <i class="bi bi-ticket-detailed-fill"></i>
                <span>Quản lý Ticket toàn trường</span>
            </a>

            <div class="sidebar-heading mt-2">Cấu hình & Báo cáo</div>
            <a href="{{ route('manager.categories.index') }}" class="sidebar-item {{ request()->routeIs('manager.categories.*') ? 'active' : '' }}">
                <i class="bi bi-sliders"></i>
                <span>Cấu hình Danh mục & SLA</span>
            </a>
            <a href="{{ route('manager.reports.sla') }}" class="sidebar-item {{ request()->routeIs('manager.reports.*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-octagon-fill text-danger"></i>
                <span>Báo cáo Vi phạm SLA</span>
            </a>
            <a href="{{ route('manager.profile.index') }}" class="sidebar-item {{ request()->routeIs('manager.profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i>
                <span>Hồ sơ Quản trị viên</span>
            </a>
        </nav>

        <div class="p-3 border-top border-secondary border-opacity-10 text-center" style="font-size:0.75rem; color:#64748b;">
            TLU Manager Workspace v1.0
        </div>
    </aside>

    {{-- MAIN WRAPPER --}}
    <div class="mgr-wrapper">

        {{-- TOPBAR --}}
        <header class="mgr-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="topbar-toggle d-lg-none" onclick="document.getElementById('mgrSidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <span class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size:0.95rem;">
                    <i class="bi bi-building text-primary fs-5"></i> Trung tâm CNTT &amp; CSVC TLU
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('manager.reports.sla') }}" class="btn btn-sm btn-light border fw-bold text-danger d-none d-md-flex align-items-center gap-1 rounded-3" style="font-size:0.8rem;">
                    <i class="bi bi-alarm-fill"></i> Báo cáo SLA
                </a>

                {{-- Notification Bell Dropdown --}}
                @php
                    $mgrOverdueNotifs = \App\Models\Ticket::whereNotIn('status', ['RESOLVED', 'CLOSED'])
                        ->where('sla_deadline', '<', now())
                        ->latest()
                        ->take(5)
                        ->get();
                    $mgrUnread = $mgrOverdueNotifs->count();
                @endphp
                <div class="dropdown">
                    <button class="btn btn-light rounded-3 position-relative p-2 border" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo hệ thống Quản lý">
                        <i class="bi bi-bell-fill text-primary fs-6"></i>
                        @if ($mgrUnread > 0)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0 rounded-4 overflow-hidden" style="width: 330px; max-height: 420px;">
                        <div class="p-3 bg-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0" style="font-size:0.88rem;"><i class="bi bi-bell-fill me-2"></i>Thông báo Quản trị viên</h6>
                            <span class="badge bg-white text-primary rounded-pill" style="font-size:0.7rem;">{{ $mgrUnread }} Cảnh báo</span>
                        </div>
                        <div class="p-0 overflow-auto" style="max-height: 310px;">
                            @forelse ($mgrOverdueNotifs as $t)
                                <a href="{{ route('manager.tickets.show', $t->id) }}" class="dropdown-item p-3 border-bottom d-flex align-items-start gap-2 text-wrap" style="white-space: normal;">
                                    <div class="rounded-circle bg-danger-subtle text-danger p-2 flex-shrink-0" style="width:34px; height:34px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-alarm-fill fs-6"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size:0.83rem;">{{ Str::limit($t->title, 45) }}</div>
                                        <div class="text-muted" style="font-size:0.76rem;">Cảnh báo sự cố vượt quá thời hạn cam kết SLA.</div>
                                        <small class="text-danger fw-bold" style="font-size:0.68rem;">Hạn SLA: {{ $t->sla_deadline?->format('H:i d/m/Y') }}</small>
                                    </div>
                                </a>
                            @empty
                                <div class="p-4 text-center text-muted" style="font-size:0.8rem;">
                                    <i class="bi bi-check-circle fs-4 d-block mb-1 text-success"></i>
                                    Không có sự cố nào bị quá hạn SLA.
                                </div>
                            @endforelse
                        </div>
                        <div class="p-2 text-center bg-light border-top">
                            <a href="{{ route('manager.tickets.index') }}" class="text-decoration-none text-primary fw-bold" style="font-size:0.78rem;">Quản lý tất cả Ticket &rarr;</a>
                        </div>
                    </div>
                </div>

                {{-- User profile dropdown --}}
                <div class="dropdown user-dropdown">
                    <button class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar-mgr">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down ms-1 text-muted" style="font-size:0.65rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2" style="min-width: 210px;">
                        <li>
                            <div class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold" style="font-size:0.85rem; color:#1e293b;">{{ Auth::user()->name }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email }}</div>
                            </div>
                        </li>
                        <li>
                            <a href="{{ route('manager.profile.index') }}" class="dropdown-item py-2 rounded-2" style="font-size:0.85rem;">
                                <i class="bi bi-person me-2 text-primary"></i> Hồ sơ Quản trị viên
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
        <main class="mgr-content">
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
            &copy; {{ date('Y') }} Đại học Thủy Lợi — Phân hệ Quản trị &amp; Điều hành IT Helpdesk
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
