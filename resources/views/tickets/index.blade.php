@extends('layouts.app')

@section('title', 'Danh sách Sự cố của tôi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate-800 mb-1">Sự cố của tôi</h4>
        <p class="text-muted small mb-0">Theo dõi tiến độ xử lý các phiếu phản ánh hỗ trợ kỹ thuật.</p>
    </div>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-circle me-1"></i> Tạo ticket mới
    </a>
</div>

<!-- Filter Bar -->
<div class="card card-custom p-3 bg-white mb-4">
    <form method="GET" action="{{ route('tickets.index') }}" class="row g-3">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Tìm theo tiêu đề sự cố..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Mới gửi (OPEN)</option>
                <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>Đang xử lý (IN_PROGRESS)</option>
                <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>Đã xong (RESOLVED)</option>
                <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>Đã đóng (CLOSED)</option>
                <option value="REOPENED" {{ request('status') === 'REOPENED' ? 'selected' : '' }}>Mở lại (REOPENED)</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-100">Lọc dữ liệu</button>
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
        </div>
    </form>
</div>

<!-- Tickets Table -->
<div class="card card-custom bg-white">
    <div class="card-body p-0">
        @if($tickets->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted display-4"></i>
                <h5 class="fw-bold mt-2">Chưa có sự cố nào</h5>
                <p class="text-muted small">Không tìm thấy phiếu yêu cầu hỗ trợ phù hợp.</p>
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
                            <th>Ngày khởi tạo</th>
                            <th class="text-end pe-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td class="ps-3"><span class="fw-bold text-primary">#TK-{{ $ticket->id }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $ticket->title }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $ticket->location ?? 'Chưa ghi vị trí' }}</small>
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
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('H:i - d/m/Y') }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
