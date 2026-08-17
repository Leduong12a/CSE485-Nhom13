@extends('staff.layouts.app')

@section('title', 'Hồ sơ Ca trực KTV')

@push('styles')
<style>
    .stf-profile-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .stf-header-bg {
        background: linear-gradient(135deg, var(--stf-dark) 0%, var(--stf-primary) 100%);
        height: 110px;
        position: relative;
    }

    .stf-avatar-container {
        padding: 0 1.5rem 1.5rem;
        position: relative;
    }

    .stf-avatar {
        width: 84px;
        height: 84px;
        background: white;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--stf-primary);
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
        color: var(--stf-primary);
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
        <h1 class="h3 fw-bold text-slate-800 mb-0">Hồ sơ &amp; Ca trực Kỹ thuật viên</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Thông tin ca trực cố định, SĐT liên hệ và thống kê công việc</p>
    </div>
</div>

{{-- Profile Card --}}
<div class="stf-profile-card">
    <div class="stf-header-bg"></div>

    <div class="stf-avatar-container">
        {{-- Avatar & Text Container --}}
        <div class="d-flex align-items-start gap-3 mb-3" style="margin-top: -42px;">
            <div class="stf-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="padding-top: 48px;">
                <h2 class="h4 fw-bold text-dark mb-1">{{ $user->name }}</h2>
                <p class="text-muted mb-0" style="font-size:0.85rem;">
                    <i class="bi bi-envelope me-1"></i>{{ $user->email }} ·
                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">Cán bộ Kỹ thuật</span>
                </p>
            </div>
        </div>

        {{-- Statistics Row --}}
        <div class="row g-3 pt-3 border-top">
            <div class="col-sm-4">
                <div class="stat-box">
                    <div class="num text-primary">{{ $totalAssigned }}</div>
                    <div class="lbl">Tổng Ticket được giao</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-box">
                    <div class="num text-warning">{{ $inProgress }}</div>
                    <div class="lbl">Ticket đang khắc phục</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-box">
                    <div class="num text-success">{{ $totalResolved }}</div>
                    <div class="lbl">Khắc phục thành công</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Cập nhật SĐT & Ca trực --}}
<div class="card">
    <div class="card-header bg-white font-bold py-3">
        <i class="bi bi-clock-history me-2 text-success"></i>Thông tin Ca trực &amp; SĐT Liên hệ
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('staff.profile.update') }}">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold style-085" style="font-size:0.85rem;">Số điện thoại trực ca <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone', $profile->phone) }}" placeholder="VD: 0987654321" required>
                    <small class="text-muted" style="font-size:0.75rem;">SĐT này dùng để hiển thị cho Trưởng bộ phận khi phân công khẩn cấp.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold style-085" style="font-size:0.85rem;">Ca trực cố định <span class="text-danger">*</span></label>
                    <select name="shift" class="form-select rounded-3">
                        <option value="Ca Sáng (07:00 - 11:30)" {{ old('shift', $profile->shift) === 'Ca Sáng (07:00 - 11:30)' ? 'selected' : '' }}>Ca Sáng (07:00 - 11:30)</option>
                        <option value="Ca Chiều (13:00 - 17:30)" {{ old('shift', $profile->shift) === 'Ca Chiều (13:00 - 17:30)' ? 'selected' : '' }}>Ca Chiều (13:00 - 17:30)</option>
                        <option value="Ca Tối / Hành chính" {{ old('shift', $profile->shift) === 'Ca Tối / Hành chính' ? 'selected' : '' }}>Ca Tối / Hành chính</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success rounded-3 fw-bold px-4" style="font-size:0.875rem;">
                <i class="bi bi-check-lg me-1"></i> Cập nhật thông tin ca trực
            </button>
        </form>
    </div>
</div>

@endsection
