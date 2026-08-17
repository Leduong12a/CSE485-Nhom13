@extends('student.layouts.app')

@section('title', 'Sự cố của tôi')
@section('meta_description', 'Danh sách toàn bộ phiếu hỗ trợ kỹ thuật đã gửi của bạn')

@push('styles')
<style>
    .filter-card {
        border-radius: 14px;
        border: none;
        box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        background: white;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }

    .search-input {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 0.55rem 1rem 0.55rem 2.5rem;
        font-size: 0.875rem;
        transition: border-color 0.2s;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236c757d'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.75rem center;
        background-size: 0.9rem;
    }

    .search-input:focus {
        border-color: var(--tlu-primary);
        box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
        outline: none;
    }

    .status-pill {
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.35rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: #374151;
        background: white;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
    }

    .status-pill:hover { border-color: var(--tlu-primary); color: var(--tlu-primary); }
    .status-pill.active { background: var(--tlu-primary); border-color: var(--tlu-primary); color: white; }

    /* Desktop Table */
    .ticket-table {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 8px rgba(0,0,0,0.06);
    }

    .ticket-table table { margin: 0; }

    .ticket-table thead th {
        background: #f8fafc;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        border-bottom: 1px solid #f1f5f9;
        padding: 0.85rem 1rem;
        white-space: nowrap;
    }

    .ticket-table tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.875rem;
        color: #374151;
    }

    .ticket-table tbody tr:last-child td { border-bottom: none; }

    .ticket-table tbody tr {
        transition: background 0.12s;
        cursor: pointer;
    }

    .ticket-table tbody tr:hover { background: #f8fafc; }

    .ticket-id {
        font-size: 0.75rem;
        font-weight: 600;
        color: #94a3b8;
        font-family: 'Courier New', monospace;
    }

    .ticket-title {
        font-weight: 500;
        color: #1e293b;
        max-width: 280px;
    }

    .ticket-title a {
        text-decoration: none;
        color: inherit;
    }

    .ticket-title a:hover { color: var(--tlu-primary); }

    .ticket-title .category-tag {
        font-size: 0.72rem;
        color: #94a3b8;
        font-weight: 400;
        display: block;
        margin-top: 2px;
    }

    /* Mobile Card View */
    .ticket-card-mobile {
        background: white;
        border-radius: 14px;
        padding: 1rem 1.1rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        text-decoration: none;
        display: block;
        color: inherit;
        transition: box-shadow 0.15s, transform 0.12s;
        border-left: 4px solid transparent;
    }

    .ticket-card-mobile:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        transform: translateY(-1px);
        color: inherit;
    }

    .ticket-card-mobile.status-OPEN        { border-left-color: #0ea5e9; }
    .ticket-card-mobile.status-IN_PROGRESS { border-left-color: #f59e0b; }
    .ticket-card-mobile.status-RESOLVED    { border-left-color: #22c55e; }
    .ticket-card-mobile.status-CLOSED      { border-left-color: #94a3b8; }
    .ticket-card-mobile.status-REOPENED    { border-left-color: #ec4899; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 3.5rem;
        color: #cbd5e1;
        display: block;
        margin-bottom: 1rem;
    }

    .empty-state h5 {
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .empty-state p { color: #94a3b8; font-size: 0.875rem; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1><i class="bi bi-ticket-perforated me-2 text-primary" style="font-size:1.3rem;"></i>Sự cố của tôi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active">Sự cố của tôi</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('student.tickets.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="border-radius:10px; font-weight:600; padding: 0.55rem 1.1rem;">
        <i class="bi bi-plus-circle-fill"></i> Tạo ticket mới
    </a>
</div>

{{-- Filter Bar --}}
<div class="filter-card d-flex flex-wrap align-items-center gap-2">
    {{-- Search --}}
    <form method="GET" action="{{ route('student.tickets.index') }}" class="d-flex gap-2 flex-grow-1" id="filterForm">
        <input
            type="text"
            name="search"
            class="form-control search-input flex-grow-1"
            placeholder="Tìm theo tiêu đề hoặc mã ticket..."
            value="{{ request('search') }}"
            autocomplete="off"
        >
        <input type="hidden" name="status" id="statusInput" value="{{ request('status', 'ALL') }}">
    </form>

    {{-- Status Pills --}}
    <div class="d-flex gap-1 flex-wrap">
        @foreach([
            'ALL'         => ['label' => 'Tất cả',          'icon' => 'bi-list-ul'],
            'OPEN'        => ['label' => 'Mới gửi',          'icon' => 'bi-circle'],
            'IN_PROGRESS' => ['label' => 'Đang xử lý',       'icon' => 'bi-arrow-repeat'],
            'RESOLVED'    => ['label' => 'Đã khắc phục',     'icon' => 'bi-check-circle'],
            'CLOSED'      => ['label' => 'Đã đóng',          'icon' => 'bi-lock'],
        ] as $value => $item)
            <a href="#" class="status-pill {{ request('status', 'ALL') === $value ? 'active' : '' }}"
               onclick="filterByStatus('{{ $value }}')">
                <i class="{{ $item['icon'] }} me-1"></i>{{ $item['label'] }}
            </a>
        @endforeach
    </div>
</div>

{{-- ── DESKTOP TABLE ── --}}
<div class="ticket-table d-none d-md-block">
    @if ($tickets->isEmpty())
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h5>Chưa có sự cố nào</h5>
            <p>Bạn chưa gửi yêu cầu hỗ trợ kỹ thuật nào.<br>Hãy bấm "+ Tạo ticket mới" khi gặp sự cố nhé!</p>
            <a href="{{ route('student.tickets.create') }}" class="btn btn-primary mt-2" style="border-radius:9px;">
                <i class="bi bi-plus-circle-fill me-1"></i> Báo sự cố ngay
            </a>
        </div>
    @else
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Tiêu đề sự cố</th>
                    <th>Mức ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Ngày gửi</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                <tr onclick="window.location='{{ route('student.tickets.show', $ticket->id) }}'">
                    <td><span class="ticket-id">#{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                    <td class="ticket-title">
                        <a href="{{ route('student.tickets.show', $ticket->id) }}">{{ $ticket->title }}</a>
                        <span class="category-tag"><i class="bi bi-tag me-1"></i>{{ $ticket->category?->name ?? '—' }}</span>
                    </td>
                    <td>
                        <span class="badge badge-status badge-priority-{{ $ticket->priority }}">
                            @switch($ticket->priority)
                                @case('HIGH')   🔴 Cao      @break
                                @case('MEDIUM') 🟡 Trung bình @break
                                @case('LOW')    🟢 Thấp     @break
                            @endswitch
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-status badge-{{ $ticket->status }}">
                            @switch($ticket->status)
                                @case('OPEN')        🔵 Mới gửi       @break
                                @case('IN_PROGRESS') 🟡 Đang xử lý    @break
                                @case('RESOLVED')    🟢 Đã khắc phục  @break
                                @case('CLOSED')      ⚫ Đã đóng       @break
                                @case('REOPENED')    🔴 Mở lại        @break
                            @endswitch
                        </span>
                    </td>
                    <td style="color:#64748b; font-size:0.82rem;">
                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        <a href="{{ route('student.tickets.show', $ticket->id) }}"
                           class="btn btn-sm btn-outline-primary"
                           style="border-radius:7px; font-size:0.78rem;"
                           onclick="event.stopPropagation()">
                            <i class="bi bi-eye me-1"></i> Xem
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($tickets->hasPages())
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top" style="font-size:0.82rem; color:#64748b;">
                <span>Hiển thị {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} / {{ $tickets->total() }} kết quả</span>
                {{ $tickets->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

{{-- ── MOBILE CARD VIEW ── --}}
<div class="d-md-none">
    @if ($tickets->isEmpty())
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h5>Chưa có sự cố nào</h5>
            <p>Bấm "+ Báo sự cố" để tạo yêu cầu hỗ trợ đầu tiên!</p>
            <a href="{{ route('student.tickets.create') }}" class="btn btn-primary mt-2" style="border-radius:9px;">
                <i class="bi bi-plus-circle-fill me-1"></i> Báo sự cố ngay
            </a>
        </div>
    @else
        @foreach ($tickets as $ticket)
            <a href="{{ route('student.tickets.show', $ticket->id) }}"
               class="ticket-card-mobile status-{{ $ticket->status }}">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="ticket-id">#{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge badge-status badge-{{ $ticket->status }}">
                        @switch($ticket->status)
                            @case('OPEN')        🔵 Mới gửi       @break
                            @case('IN_PROGRESS') 🟡 Đang xử lý    @break
                            @case('RESOLVED')    🟢 Đã khắc phục  @break
                            @case('CLOSED')      ⚫ Đã đóng       @break
                            @case('REOPENED')    🔴 Mở lại        @break
                        @endswitch
                    </span>
                </div>
                <p class="mb-1 fw-500" style="font-size:0.9rem; color:#1e293b; line-height:1.4;">
                    {{ $ticket->title }}
                </p>
                <div class="d-flex gap-3 mt-1" style="font-size:0.75rem; color:#94a3b8;">
                    <span><i class="bi bi-tag me-1"></i>{{ $ticket->category?->name ?? '—' }}</span>
                    <span><i class="bi bi-clock me-1"></i>{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
            </a>
        @endforeach

        {{-- Pagination Mobile --}}
        @if ($tickets->hasPages())
            <div class="mt-2 d-flex justify-content-center">
                {{ $tickets->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

@endsection

@push('scripts')
<script>
function filterByStatus(status) {
    document.getElementById('statusInput').value = status;
    document.getElementById('filterForm').submit();
}
</script>
@endpush
