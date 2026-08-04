@extends('layouts.admin')

@section('title', 'Manager Dashboard')
@section('page_title', 'Tổng quan Báo cáo & Thống kê Hệ thống (Manager)')

@section('content')
<!-- Top Stat Cards KPIs -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted extra-small text-uppercase font-weight-bold">Tổng Ticket tháng này</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ $totalTickets }}</h3>
                </div>
                <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                    <i class="bi bi-ticket-perforated fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted extra-small text-uppercase font-weight-bold">Tỷ lệ đúng hạn SLA</span>
                    <h3 class="fw-bold mb-0 text-success">96.5%</h3>
                </div>
                <div class="bg-success-subtle p-3 rounded-circle text-success">
                    <i class="bi bi-shield-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white border-start border-4 border-danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted extra-small text-uppercase font-weight-bold">Ticket Quá hạn SLA</span>
                    <h3 class="fw-bold mb-0 text-danger">{{ $overdueTickets }}</h3>
                </div>
                <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                    <i class="bi bi-exclamation-octagon fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted extra-small text-uppercase font-weight-bold">Đánh giá trung bình</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ number_format($avgRating, 1) }} ⭐</h3>
                </div>
                <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                    <i class="bi bi-star-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Stream Row -->
<div class="row g-4 mb-4">
    <!-- Chart Placeholder -->
    <div class="col-lg-8">
        <div class="card card-custom bg-white p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill me-2 text-primary"></i> Phân tích Sự cố theo Danh mục lỗi</h5>
            <div class="p-5 text-center bg-light rounded border border-dashed">
                <i class="bi bi-pie-chart display-3 text-muted"></i>
                <p class="text-muted mt-2 mb-0">Biểu đồ thống kê số lượng sự cố (Wi-Fi 45%, Máy chiếu 30%, ĐKMH 25%)</p>
            </div>
        </div>
    </div>

    <!-- Recent Assignments -->
    <div class="col-lg-4">
        <div class="card card-custom bg-white p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2 text-primary"></i> Lượt phân công mới nhất</h5>
            <div class="list-group list-group-flush">
                @if($recentAssignments->isEmpty())
                    <p class="text-muted small">Chưa có lượt phân công nào.</p>
                @else
                    @foreach($recentAssignments as $assign)
                        <div class="list-group-item px-0 py-2">
                            <div class="fw-semibold small text-primary">#TK-{{ $assign->ticket_id }}: {{ $assign->ticket_title }}</div>
                            <div class="extra-small text-muted">
                                Giao cho: <strong class="text-dark">{{ $assign->staff_name }}</strong>
                            </div>
                            <small class="text-muted extra-small">{{ \Carbon\Carbon::parse($assign->assigned_at)->diffForHumans() }}</small>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
