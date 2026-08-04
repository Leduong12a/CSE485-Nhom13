@extends('layouts.admin')

@section('title', 'Workdesk Kỹ thuật viên')
@section('page_title', 'IT Staff Workdesk — Bàn làm việc Kỹ thuật')

@section('content')
<!-- Header Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3 bg-white border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted extra-small text-uppercase font-weight-bold">Công việc tôi đang phụ trách</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ $assignedTickets->count() }}</h3>
                </div>
                <i class="bi bi-tools fs-1 text-primary-subtle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 bg-white border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted extra-small text-uppercase font-weight-bold">Phiếu mới mở chưa phân công</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $openTickets->count() }}</h3>
                </div>
                <i class="bi bi-inbox fs-1 text-warning-subtle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 bg-white border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted extra-small text-uppercase font-weight-bold">Cam kết SLA đúng hạn</span>
                    <h3 class="fw-bold mb-0 text-success">100%</h3>
                </div>
                <i class="bi bi-shield-check fs-1 text-success-subtle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabs View -->
<ul class="nav nav-tabs mb-3 border-bottom" id="workdeskTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-semibold" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned-pane">
            <i class="bi bi-person-workspace me-1"></i> Việc được giao cho tôi ({{ $assignedTickets->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold text-warning" id="open-tab" data-bs-toggle="tab" data-bs-target="#open-pane">
            <i class="bi bi-clipboard-pulse me-1"></i> Hàng chờ Phiếu mới ({{ $openTickets->count() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="workdeskTabContent">
    <!-- Tab 1: Công việc được giao -->
    <div class="tab-pane fade show active" id="assigned-pane">
        <div class="card card-custom bg-white">
            <div class="card-body p-0">
                @if($assignedTickets->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-smile text-muted display-4"></i>
                        <p class="text-muted mt-2">Hiện tại bạn không có ticket nào cần xử lý!</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Đồng hồ SLA</th>
                                    <th>Mã Ticket</th>
                                    <th>Tiêu đề & Người báo</th>
                                    <th>Danh mục</th>
                                    <th>Mức ưu tiên</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-3">Tác vụ 1-Click</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignedTickets as $ticket)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3">
                                                <i class="bi bi-clock-history me-1"></i>Còn 02h 15m
                                            </span>
                                        </td>
                                        <td><span class="fw-bold text-primary">#TK-{{ $ticket->id }}</span></td>
                                        <td>
                                            <div class="fw-semibold">{{ $ticket->title }}</div>
                                            <small class="text-muted"><i class="bi bi-person me-1"></i> {{ $ticket->requester_name }} — {{ $ticket->location }}</small>
                                        </td>
                                        <td><span class="badge bg-secondary-subtle text-secondary border">{{ $ticket->category_name }}</span></td>
                                        <td>
                                            @if($ticket->priority === 'HIGH')
                                                <span class="badge bg-danger">Cao</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Trung bình</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-arrow-repeat me-1"></i> Đang xử lý</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Xong</button>
                                                <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-outline-secondary"><i class="bi bi-eye"></i> Xem</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab 2: Hàng chờ phiếu mới -->
    <div class="tab-pane fade" id="open-pane">
        <div class="card card-custom bg-white">
            <div class="card-body p-0">
                @if($openTickets->isEmpty())
                    <div class="text-center py-5">
                        <p class="text-muted">Không có phiếu mới nào đang chờ.</p>
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
                                    <th>Thời gian gửi</th>
                                    <th class="text-end pe-3">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($openTickets as $ticket)
                                    <tr>
                                        <td class="ps-3"><span class="fw-bold text-primary">#TK-{{ $ticket->id }}</span></td>
                                        <td>{{ $ticket->title }}</td>
                                        <td><span class="badge bg-secondary-subtle text-secondary border">{{ $ticket->category_name }}</span></td>
                                        <td><span class="badge bg-info text-dark">{{ $ticket->priority }}</span></td>
                                        <td class="small text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->diffForHumans() }}</td>
                                        <td class="text-end pe-3">
                                            <button type="button" class="btn btn-primary btn-sm rounded-pill"><i class="bi bi-hand-index me-1"></i> Nhận xử lý</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
