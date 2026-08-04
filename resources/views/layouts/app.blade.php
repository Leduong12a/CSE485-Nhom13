<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trang chủ') - TLU Helpdesk</title>
    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #1e293b;
        }
        .navbar-tlu {
            background-color: #004085;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .navbar-tlu .navbar-brand, .navbar-tlu .nav-link {
            color: #ffffff;
        }
        .navbar-tlu .nav-link:hover, .navbar-tlu .nav-link.active {
            color: #93c5fd;
        }
        .badge-notification {
            position: absolute;
            top: 4px;
            right: 4px;
            font-size: 0.65rem;
        }
        .card-custom {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.06);
            transition: all 0.2s;
        }
        .card-custom:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }
        .footer-tlu {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem 0;
            margin-top: 4rem;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-tlu sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}">
                <i class="bi bi-headset fs-3 text-warning"></i>
                <span>TLU Helpdesk</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('home') || request()->is('/') ? 'active fw-semibold' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('tickets/create*') ? 'active fw-semibold' : '' }}" href="{{ route('tickets.create') }}">
                            <i class="bi bi-plus-circle me-1"></i> Báo sự cố mới
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('tickets') ? 'active fw-semibold' : '' }}" href="{{ route('tickets.index') }}">
                            <i class="bi bi-ticket-perforated me-1"></i> Sự cố của tôi
                        </a>
                    </li>
                </ul>

                <!-- Notification Bell & User Dropdown -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Notification Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light border-0 position-relative p-2" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="badge rounded-pill bg-danger badge-notification">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 320px;">
                            <li><h6 class="dropdown-header fw-bold border-bottom pb-2">Thông báo mới</h6></li>
                            <li>
                                <a class="dropdown-item py-2 border-bottom" href="#">
                                    <div class="small fw-semibold text-primary">KTV đã tiếp nhận sự cố #TK-1024</div>
                                    <div class="text-muted extra-small">Máy chiếu phòng 302 C5 đang được xử lý.</div>
                                    <div class="text-muted text-end extra-small" style="font-size: 0.7rem;">10 phút trước</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 border-bottom" href="#">
                                    <div class="small fw-semibold text-success">Sự cố #TK-1020 đã hoàn thành</div>
                                    <div class="text-muted extra-small">Vui lòng làm khảo sát 5 sao đánh giá dịch vụ.</div>
                                    <div class="text-muted text-end extra-small" style="font-size: 0.7rem;">1 giờ trước</div>
                                </a>
                            </li>
                            <li><a class="dropdown-item text-center small text-primary py-2" href="#">Xem tất cả thông báo</a></li>
                        </ul>
                    </div>

                    <!-- User Profile Dropdown -->
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5 text-primary"></i>
                                <span class="fw-semibold small">{{ Auth::user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><div class="dropdown-header">
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <div class="text-muted small">{{ Auth::user()->email }}</div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill mt-1">
                                        {{ Auth::user()->role }}
                                    </span>
                                </div></li>
                                <li><hr class="dropdown-divider"></li>
                                @if(Auth::user()->role === 'STAFF')
                                    <li><a class="dropdown-item text-primary" href="{{ route('staff.workdesk') }}"><i class="bi bi-tools me-2"></i> IT Staff Workdesk</a></li>
                                @elseif(Auth::user()->role === 'MANAGER')
                                    <li><a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Manager Dashboard</a></li>
                                @endif
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Hồ sơ cá nhân</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light rounded-pill px-4 fw-semibold">Đăng nhập</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="container my-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show card-custom mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show card-custom mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-tlu text-muted">
        <div class="container text-center">
            <p class="mb-1">© 2026 Hệ thống Helpdesk CNTT & Cơ sở vật chất — Trường Đại học Thủy Lợi (TLU)</p>
            <small class="text-secondary">Phát triển bởi CSE485 - Nhóm 13</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
