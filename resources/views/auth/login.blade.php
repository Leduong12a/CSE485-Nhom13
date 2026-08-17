<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng nhập vào Hệ thống Helpdesk CNTT & Cơ sở vật chất - Trường Đại học Thủy Lợi">
    <title>Đăng nhập — TLU Helpdesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #004085 0%, #0d6efd 50%, #0dcaf0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-wrapper {
            width: 100%;
            max-width: 460px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            background: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #0d6efd;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            flex-shrink: 0;
        }

        .brand-text h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            margin: 0;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.8);
            margin: 0;
            font-weight: 400;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 2.2rem 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .login-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.3rem;
        }

        .login-card .subtitle {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 1.8rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.4rem;
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            padding: 0.65rem 1rem;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.12);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: none;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f8f9fa;
            border: 1.5px solid #e5e7eb;
            border-right: none;
            color: #6c757d;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }

        .btn-login {
            background: linear-gradient(90deg, #0d6efd, #0a58ca);
            border: none;
            border-radius: 10px;
            padding: 0.7rem;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 14px rgba(13,110,253,0.35);
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(13,110,253,0.4);
            background: linear-gradient(90deg, #0a58ca, #0d6efd);
        }

        .btn-login:active { transform: translateY(0); }

        .divider {
            position: relative;
            text-align: center;
            margin: 1.4rem 0;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            position: relative;
            background: white;
            padding: 0 12px;
            font-size: 0.78rem;
            color: #9ca3af;
            font-weight: 500;
        }

        .btn-outlook {
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.65rem;
            font-weight: 500;
            font-size: 0.88rem;
            color: #374151;
            background: white;
            transition: background 0.15s, border-color 0.15s, box-shadow 0.15s;
        }

        .btn-outlook:hover {
            background: #f8f9fa;
            border-color: #0d6efd;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        }

        .ms-icon {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }

        .forgot-link {
            font-size: 0.8rem;
            color: #0d6efd;
            text-decoration: none;
        }

        .forgot-link:hover { text-decoration: underline; }

        .footer-note {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.7);
        }

        .alert-error {
            border-radius: 10px;
            border-left: 4px solid #dc3545;
            background: #fff5f5;
            color: #842029;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        {{-- Logo & Brand --}}
        <div class="brand-logo">
            <div class="brand-icon">
                <i class="bi bi-headset"></i>
            </div>
            <div class="brand-text">
                <h1>TLU Helpdesk</h1>
                <p>Hệ thống Hỗ trợ Kỹ thuật — Đại học Thủy Lợi</p>
            </div>
        </div>

        {{-- Login Card --}}
        <div class="login-card">
            <h2>Chào mừng trở lại 👋</h2>
            <p class="subtitle">Đăng nhập bằng tài khoản Email TLU của bạn</p>

            {{-- Thông báo lỗi --}}
            @if ($errors->any())
                <div class="alert-error mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Form Đăng nhập --}}
            <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email TLU</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="mssv@st.tlu.edu.vn hoặc gv@tlu.edu.vn"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                    </div>
                </div>

                {{-- Mật khẩu --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label mb-0">Mật khẩu</label>
                        <a href="#" class="forgot-link">Quên mật khẩu?</a>
                    </div>
                    <div class="input-group mt-1">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Nhập mật khẩu của bạn"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="btn btn-outline-secondary border-start-0 rounded-end-3" id="togglePassword" style="border: 1.5px solid #e5e7eb; border-left: none;">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Ghi nhớ đăng nhập --}}
                <div class="mb-4 d-flex align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember" style="font-size:0.85rem; color:#374151;">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                </div>

                {{-- Nút Đăng nhập --}}
                <button type="submit" id="btnLogin" class="btn btn-primary btn-login w-100">
                    <span id="btnText"><i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập</span>
                    <span id="btnSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Đang đăng nhập...
                    </span>
                </button>
            </form>

            <div class="divider"><span>hoặc tiếp tục với</span></div>

            {{-- Nút Outlook SSO --}}
            <a href="{{ route('auth.microsoft.redirect') }}"
               id="btnOutlook"
               class="btn btn-outlook w-100 d-flex align-items-center justify-content-center"
               style="text-decoration:none;">
                {{-- Microsoft Logo SVG --}}
                <svg class="ms-icon" viewBox="0 0 23 23" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#f3f3f3" d="M0 0h23v23H0z"/>
                    <path fill="#f35325" d="M1 1h10v10H1z"/>
                    <path fill="#81bc06" d="M12 1h10v10H12z"/>
                    <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                    <path fill="#ffba08" d="M12 12h10v10H12z"/>
                </svg>
                Đăng nhập bằng Tài khoản TLU (Microsoft)
            </a>
        </div>

        <div class="footer-note">
            © {{ date('Y') }} Trường Đại học Thủy Lợi — Bộ phận Hỗ trợ Kỹ thuật CNTT &amp; CSVC
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Toast SSO chưa cấu hình --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
        <div id="ssoToast" class="toast align-items-center text-white border-0"
             style="background:#374151; border-radius:12px;" role="alert" aria-live="assertive">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill" style="color:#60a5fa;"></i>
                    <span style="font-size:0.85rem;">Tính năng đăng nhập Microsoft SSO đang được cấu hình. Vui lòng dùng Email & Mật khẩu.</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <script>
        // Toggle hiện/ẩn mật khẩu
        document.getElementById('togglePassword').addEventListener('click', function () {
            const pwInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pwInput.type === 'password') {
                pwInput.type = 'text';
                eyeIcon.className = 'bi bi-eye-slash';
            } else {
                pwInput.type = 'password';
                eyeIcon.className = 'bi bi-eye';
            }
        });

        // Loading state khi submit
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('btnLogin');
            document.getElementById('btnText').classList.add('d-none');
            document.getElementById('btnSpinner').classList.remove('d-none');
            btn.disabled = true;
        });

        // Outlook SSO — Hiển thị toast thông báo chưa cấu hình
        document.getElementById('btnOutlook').addEventListener('click', function () {
            const toast = document.getElementById('ssoToast');
            new bootstrap.Toast(toast, { delay: 3500 }).show();
        });
    </script>
</body>
</html>
