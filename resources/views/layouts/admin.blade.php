<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản trị') - TLU Helpdesk</title>
    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
            min-height: 100vh;
        }
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #0f172a;
            color: #fff;
            transition: all 0.3s;
        }
        #sidebar .sidebar-header {
            padding: 1.5rem;
            background: #0284c7;
        }
        #sidebar ul.components {
            padding: 1rem 0;
        }
        #sidebar ul li a {
            padding: 0.85rem 1.5rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s;
        }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: #ffffff;
            background: #1e293b;
            border-left: 4px solid #38bdf8;
        }
        #content {
            width: 100%;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .admin-topbar {
            background: #ffffff;
            padding: 1rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .card-custom {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Collapsible Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill fs-3"></i>
                <div>
                    <h5 class="mb-0 fw-bold">TLU Workspace</h5>
                    <small class="text-white-50">{{ Auth::user()->role ?? 'STAFF' }}</small>
                </div>
            </div>

            <ul class="list-unstyled components">
                @if(optional(Auth::user())->role === 'MANAGER')
                    <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard Thống kê</a>
                    </li>
                @endif
                <li class="{{ request()->is('staff/workdesk*') ? 'active' : '' }}">
                    <a href="{{ route('staff.workdesk') }}"><i class="bi bi-kanban"></i> Workdesk Công việc</a>
                </li>
                <li class="{{ request()->is('tickets*') ? 'active' : '' }}">
                    <a href="{{ route('tickets.index') }}"><i class="bi bi-ticket-detailed"></i> Tất cả Ticket</a>
                </li>
                <li class="border-top my-2 pt-2"></li>
                <li>
                    <a href="{{ route('home') }}"><i class="bi bi-box-arrow-up-right"></i> Về Portal Người dùng</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <div class="admin-topbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0 fw-bold text-slate-800">@yield('page_title', 'Bảng điều khiển')</h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name ?? 'User' }} ({{ Auth::user()->role ?? 'STAFF' }})
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-box-arrow-right me-1"></i> Thoát</button>
                    </form>
                </div>
            </div>

            <div class="container-fluid p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show card-custom mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
