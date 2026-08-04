@extends('layouts.app')

@section('title', 'Trang chủ Portal')

@section('content')
<!-- Hero Welcome Banner -->
<div class="card card-custom bg-primary text-white p-4 mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #004085 0%, #0d6efd 100%);">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-2">Xin chào, {{ Auth::user()->name }}! 👋</h3>
            <p class="text-white-50 mb-3">Hệ thống TLU Helpdesk sẵn sàng hỗ trợ xử lý mọi sự cố về Mạng Wi-Fi, Máy chiếu, Thiết bị giảng đường và Đăng ký môn học.</p>
            <a href="{{ route('tickets.create') }}" class="btn btn-warning btn-lg rounded-pill font-weight-bold px-4 shadow-sm">
                <i class="bi bi-plus-circle-fill me-2"></i> Tạo Ticket Báo Sự cố Ngay
            </a>
        </div>
        <div class="col-lg-4 text-center d-none d-lg-block">
            <i class="bi bi-headset display-1 text-white-50"></i>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3 bg-white border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Tổng số sự cố đã gửi</span>
                    <h2 class="fw-bold mb-0 text-primary">{{ $myTicketsCount }}</h2>
                </div>
                <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                    <i class="bi bi-ticket-perforated fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 bg-white border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Đang được xử lý</span>
                    <h2 class="fw-bold mb-0 text-warning">{{ $inProgressCount }}</h2>
                </div>
                <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 bg-white border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Đã khắc phục xong</span>
                    <h2 class="fw-bold mb-0 text-success">{{ $resolvedCount }}</h2>
                </div>
                <div class="bg-success-subtle p-3 rounded-circle text-success">
                    <i class="bi bi-check-circle fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tickets List -->
<div class="card card-custom bg-white">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-slate-800"><i class="bi bi-list-stars me-2 text-primary"></i> Sự cố gần đây của bạn</h5>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">Xem tất cả</a>
    </div>
    <div class="card-body p-0">
        @if($recentTickets->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted display-4"></i>
                <p class="text-muted mt-2 mb-0">Bạn chưa gửi sự cố nào. Bấm nút bên dưới để gửi yêu cầu hỗ trợ!</p>
                <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm rounded-pill mt-3"><i class="bi bi-plus-circle me-1"></i> Báo sự cố</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Mã Ticket</th>
                            <th>Tiêu đề sự cố</th>
                            <th>Danh mục</th>
                            <th>Mức ưu tiên</th>
                            <th>Trạng thái</th>
                            <th>Ngày gửi</th>
                            <th class="text-end pe-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTickets as $ticket)
                            <tr>
                                <td class="ps-3"><span class="fw-bold text-primary">#TK-{{ $ticket->id }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $ticket->title }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $ticket->location ?? 'Không có vị trí' }}</small>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary border">{{ $ticket->category_name }}</span></td>
                                <td>
                                    @if($ticket->priority === 'HIGH')
                                        <span class="badge bg-danger">Cao</span>
                                    @elseif($ticket->priority === 'MEDIUM')
                                        <span class="badge bg-warning text-dark">Trung bình</span>
                                    @else
                                        <span class="badge bg-info text-dark">Thấp</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->status === 'OPEN')
                                        <span class="badge bg-secondary">Mới gửi</span>
                                    @elseif($ticket->status === 'IN_PROGRESS')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-arrow-repeat me-1"></i> Đang xử lý</span>
                                    @elseif($ticket->status === 'RESOLVED')
                                        <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Đã xong</span>
                                    @elseif($ticket->status === 'CLOSED')
                                        <span class="badge bg-dark">Đóng</span>
                                    @elseif($ticket->status === 'REOPENED')
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-diamond me-1"></i> Mở lại</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->diffForHumans() }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                        Chi tiết <i class="bi bi-chevron-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
