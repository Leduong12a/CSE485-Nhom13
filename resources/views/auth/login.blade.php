@extends('layouts.guest')

@section('title', 'Đăng nhập Hệ thống')

@section('content')
<div class="auth-card card">
    <div class="auth-header">
        <div class="d-flex justify-content-center mb-2">
            <i class="bi bi-headset fs-1 text-warning"></i>
        </div>
        <h4 class="fw-bold mb-1">TLU Helpdesk</h4>
        <p class="mb-0 text-white-50 small">Cổng Hỗ trợ Kỹ thuật & Cơ sở Vật chất</p>
    </div>
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success small mb-3">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger small mb-3">
                <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold small text-secondary">Email TLU (@st.tlu.edu.vn hoặc @tlu.edu.vn)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Nhập email trường..." required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-semibold small text-secondary mb-0">Mật khẩu</label>
                    <a href="#" class="small text-decoration-none text-primary">Quên mật khẩu?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu..." required>
                </div>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label small text-secondary" for="remember">Ghi nhớ đăng nhập</label>
            </div>

            <button type="submit" class="btn btn-tlu w-100 mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i> Đăng nhập hệ thống
            </button>
        </form>

        <div class="border-top pt-3">
            <p class="small text-muted mb-2 fw-semibold text-center">Tài khoản thử nghiệm nhanh:</p>
            <div class="d-flex flex-column gap-1 extra-small">
                <div class="badge bg-light text-dark border text-start p-2">
                    <i class="bi bi-person-fill text-danger me-1"></i> <strong>Admin:</strong> <code>admin@tlu.edu.vn</code> | Pass: <code>password</code>
                </div>
                <div class="badge bg-light text-dark border text-start p-2">
                    <i class="bi bi-tools text-primary me-1"></i> <strong>KTV:</strong> <code>staff1@tlu.edu.vn</code> | Pass: <code>password</code>
                </div>
                <div class="badge bg-light text-dark border text-start p-2">
                    <i class="bi bi-mortarboard-fill text-success me-1"></i> <strong>Sinh viên:</strong> <code>student1@st.tlu.edu.vn</code> | Pass: <code>password</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
