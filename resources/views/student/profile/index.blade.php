@extends('student.layouts.app')

@section('title', 'Hồ sơ cá nhân')
@section('meta_description', 'Quản lý thông tin hồ sơ sinh viên và cài đặt mật khẩu tài khoản TLU')

@push('styles')
<style>
    .profile-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .profile-header-bg {
        background: linear-gradient(135deg, var(--tlu-dark) 0%, var(--tlu-primary) 100%);
        height: 110px;
        position: relative;
    }

    .profile-avatar-wrapper {
        position: relative;
        margin-top: -50px;
        padding-left: 1.5rem;
        display: flex;
        align-items: flex-end;
        gap: 1.25rem;
        margin-bottom: 1rem;
    }

    .profile-avatar {
        width: 90px;
        height: 90px;
        background: white;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.4rem;
        font-weight: 700;
        color: var(--tlu-primary);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        border: 4px solid white;
    }

    .profile-user-title h2 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .profile-user-title p {
        font-size: 0.83rem;
        color: #64748b;
        margin: 2px 0 0;
    }

    .stat-badge-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        border: 1px solid #f1f5f9;
    }

    .stat-badge-box .num {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--tlu-primary);
        line-height: 1.1;
    }

    .stat-badge-box .lbl {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 4px;
    }

    .form-label {
        font-size: 0.83rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.4rem;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 0.6rem 0.9rem;
        font-size: 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--tlu-primary);
        box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <h1><i class="bi bi-person-circle me-2 text-primary" style="font-size:1.3rem;"></i>Hồ sơ cá nhân</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('student.tickets.index') }}" class="text-decoration-none">Sự cố của tôi</a></li>
            <li class="breadcrumb-item active">Hồ sơ cá nhân</li>
        </ol>
    </nav>
</div>

{{-- ── 1. PROFILE OVERVIEW CARD ── --}}
<div class="profile-card">
    <div class="profile-header-bg"></div>
    <div class="profile-avatar-wrapper">
        <div class="profile-avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="profile-user-title">
            <h2>{{ $user->name }}</h2>
            <p><i class="bi bi-envelope me-1"></i>{{ $user->email }} · <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $user->department?->name ?? 'Sinh viên TLU' }}</span></p>
        </div>
    </div>

    <div class="px-4 pb-4">
        <div class="row g-3 pt-2 border-top">
            <div class="col-sm-3 col-6">
                <div class="stat-badge-box">
                    <div class="num">{{ $studentCode }}</div>
                    <div class="lbl">Mã Sinh Viên (MSV)</div>
                </div>
            </div>
            <div class="col-sm-3 col-6">
                <div class="stat-badge-box">
                    <div class="num">{{ $totalSubmitted }}</div>
                    <div class="lbl">Ticket đã gửi</div>
                </div>
            </div>
            <div class="col-sm-3 col-6">
                <div class="stat-badge-box">
                    <div class="num text-success">{{ $totalResolved }}</div>
                    <div class="lbl">Đã khắc phục</div>
                </div>
            </div>
            <div class="col-sm-3 col-6">
                <div class="stat-badge-box">
                    <div class="num text-warning">{{ $totalSurveys }}</div>
                    <div class="lbl">Đánh giá 5 sao</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- ── 2. FORM CẬP NHẬT THÔNG TIN CÁ NHÂN & KHOA ── --}}
    <div class="col-lg-6">
        <div class="profile-card h-100 mb-0">
            <div class="p-3 border-bottom font-bold d-flex align-items-center gap-2" style="font-size:0.95rem; color:#1e293b;">
                <i class="bi bi-pencil-square text-primary"></i> Thông tin cá nhân &amp; Khoa
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('student.profile.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên đầy đủ <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email TLU (Cố định)</label>
                        <input type="email" id="email" class="form-control bg-light" value="{{ $user->email }}" disabled readonly>
                        <small class="text-muted" style="font-size:0.75rem;">Email trường dùng làm tài khoản định danh không thể thay đổi.</small>
                    </div>

                    <div class="mb-4">
                        <label for="department_id" class="form-label">Khoa / Đơn vị trực thuộc <span class="text-danger">*</span></label>
                        <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                            <option value="">— Chọn Khoa / Đơn vị —</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" style="font-size:0.75rem;">Hệ thống tự nhận diện từ Email SSO, bạn có thể điều chỉnh lại nếu cần.</small>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4" style="font-size:0.875rem;">
                        <i class="bi bi-check-lg me-1"></i> Cập nhật thông tin
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── 3. FORM ĐỔI / THIẾT LẬP MẬT KHẨU BẢO MẬT ── --}}
    <div class="col-lg-6">
        <div class="profile-card h-100 mb-0">
            <div class="p-3 border-bottom font-bold d-flex align-items-center gap-2" style="font-size:0.95rem; color:#1e293b;">
                <i class="bi bi-shield-lock text-danger"></i> Đổi / Thiết lập Mật khẩu
            </div>
            <div class="p-4">

                {{-- Thông báo giải thích SSO & Thiết lập mật khẩu --}}
                <div class="p-3 rounded-3 mb-4" style="background:#f0f9ff; border:1px solid #bae6fd; font-size:0.8rem; color:#0369a1;">
                    <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                    <strong>Quy trình đăng nhập &amp; Thiết lập Mật khẩu:</strong><br>
                    Ban đầu, bạn **chỉ có thể Đăng nhập bằng Microsoft Outlook SSO**.
                    <hr class="my-2" style="border-color:#bae6fd;">
                    Nếu bạn muốn dùng thêm phương thức **Email + Mật khẩu cục bộ** trên trang TLU Helpdesk, hãy tự thiết lập Mật khẩu mới bên dưới.
                </div>

                <form method="POST" action="{{ route('student.profile.password') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại <span class="text-muted font-normal">(Chỉ nhập nếu bạn đã từng đổi MK)</span></label>
                        <input type="password" id="current_password" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               placeholder="Bỏ trống nếu đây là lần đầu tạo mật khẩu">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Tối thiểu 8 ký tự" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-control"
                               placeholder="Nhập lại mật khẩu mới" required>
                    </div>

                    <button type="submit" class="btn btn-outline-primary rounded-3 fw-bold px-4" style="font-size:0.875rem;">
                        <i class="bi bi-key-fill me-1"></i> Thiết lập mật khẩu mới
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
