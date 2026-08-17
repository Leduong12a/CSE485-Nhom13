@extends('manager.layouts.app')

@section('title', 'Hồ sơ Quản trị viên')

@push('styles')
<style>
    .mng-profile-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .mng-header-bg {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        height: 110px;
        position: relative;
    }

    .mng-avatar-container {
        padding: 0 1.5rem 1.5rem;
        position: relative;
    }

    .mng-avatar {
        width: 84px;
        height: 84px;
        background: white;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: 700;
        color: #0f172a;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        border: 4px solid white;
        flex-shrink: 0;
    }

    .stat-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        border: 1px solid #f1f5f9;
    }

    .stat-box .num {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }

    .stat-box .lbl {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 4px;
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-slate-800 mb-0">Hồ sơ Quản trị viên</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Thông tin quản trị hệ thống, hiệu suất SLA &amp; Thiết lập mật khẩu</p>
    </div>
</div>

{{-- Profile Card --}}
<div class="mng-profile-card">
    <div class="mng-header-bg"></div>

    <div class="mng-avatar-container">
        {{-- Avatar & Text Container --}}
        <div class="d-flex align-items-start gap-3 mb-3" style="margin-top: -42px;">
            <div class="mng-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="padding-top: 48px;">
                <h2 class="h4 fw-bold text-dark mb-1">{{ $user->name }}</h2>
                <p class="text-muted mb-0" style="font-size:0.85rem;">
                    <i class="bi bi-envelope me-1"></i>{{ $user->email }} ·
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1">Trưởng bộ phận (Manager)</span>
                </p>
            </div>
        </div>

        {{-- Statistics Row --}}
        <div class="row g-3 pt-3 border-top">
            <div class="col-sm-4">
                <div class="stat-box">
                    <div class="num text-primary">{{ $totalSystemTickets }}</div>
                    <div class="lbl">Tổng Sự cố Toàn trường</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-box">
                    <div class="num text-success">{{ $slaRate }}%</div>
                    <div class="lbl">Tỷ lệ Hoàn thành Đúng SLA</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-box">
                    <div class="num text-dark">{{ $totalStaffCount }}</div>
                    <div class="lbl">Số KTV Quản lý</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Cập nhật Hồ sơ & Mật khẩu --}}
<div class="card">
    <div class="card-header bg-white font-bold py-3">
        <i class="bi bi-person-gear me-2 text-primary"></i>Cập nhật Thông tin &amp; Đổi Mật khẩu
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('manager.profile.update') }}">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">Họ và Tên Quản trị viên <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">Địa chỉ Email <span class="text-muted">(Cố định)</span></label>
                    <input type="email" class="form-control rounded-3 bg-light" value="{{ $user->email }}" readonly>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="fw-bold mb-3" style="font-size:0.95rem; color:#1e293b;"><i class="bi bi-shield-lock me-2 text-warning"></i>Đổi Mật khẩu Quản trị</h5>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" class="form-control rounded-3" placeholder="Nhập nếu muốn đổi mật khẩu">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">Mật khẩu mới</label>
                    <input type="password" name="new_password" class="form-control rounded-3" placeholder="Tối thiểu 6 ký tự">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">Xác nhận Mật khẩu mới</label>
                    <input type="password" name="new_password_confirmation" class="form-control rounded-3" placeholder="Nhập lại mật khẩu mới">
                </div>
            </div>

            <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4" style="font-size:0.875rem;">
                <i class="bi bi-check-lg me-1"></i> Lưu thay đổi Hồ sơ
            </button>
        </form>
    </div>
</div>

@endsection
