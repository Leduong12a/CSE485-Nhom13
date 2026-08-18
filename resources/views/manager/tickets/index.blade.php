@extends('manager.layouts.app')

@section('title', 'Quản lý Ticket toàn trường')

@push('styles')
<style>
    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 1rem 1.25rem;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        margin-bottom: 1.25rem;
    }

    .search-input {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 0.55rem 1rem 0.55rem 2.5rem;
        font-size: 0.875rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236c757d'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.75rem center;
        background-size: 0.9rem;
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-slate-800 mb-0">Quản lý Ticket toàn trường</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Danh sách tất cả các phiếu phản ánh sự cố từ Sinh viên &amp; Giảng viên</p>
    </div>
</div>

{{-- Filter Card --}}
<div class="filter-card">
    <form method="GET" action="{{ route('manager.tickets.index') }}" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control search-input"
                   placeholder="Tìm kiếm theo Mã ticket, Tiêu đề..."
                   value="{{ request('search') }}">
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select rounded-3 text-secondary" style="font-size:0.875rem;" onchange="this.form.submit()">
                <option value="ALL" {{ request('status') === 'ALL' ? 'selected' : '' }}>— Tất cả Trạng thái —</option>
                <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>🔵 Mới gửi (OPEN)</option>
                <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>🟡 Đang xử lý (IN_PROGRESS)</option>
                <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>🟢 Đã khắc phục (RESOLVED)</option>
                <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>⚫ Đã đóng (CLOSED)</option>
                <option value="REOPENED" {{ request('status') === 'REOPENED' ? 'selected' : '' }}>🔴 Mở lại (REOPENED)</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="priority" class="form-select rounded-3 text-secondary" style="font-size:0.875rem;" onchange="this.form.submit()">
                <option value="ALL" {{ request('priority') === 'ALL' ? 'selected' : '' }}>— Mức ưu tiên —</option>
                <option value="HIGH" {{ request('priority') === 'HIGH' ? 'selected' : '' }}>🔴 Cao (HIGH)</option>
                <option value="MEDIUM" {{ request('priority') === 'MEDIUM' ? 'selected' : '' }}>🟡 Trung bình (MEDIUM)</option>
                <option value="LOW" {{ request('priority') === 'LOW' ? 'selected' : '' }}>🟢 Thấp (LOW)</option>
            </select>
        </div>

        <div class="col-md-3">
            <select name="category_id" class="form-select rounded-3 text-secondary" style="font-size:0.875rem;" onchange="this.form.submit()">
                <option value="">— Chọn Danh mục —</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-1 d-flex">
            <a href="{{ route('manager.tickets.index') }}" class="btn btn-outline-secondary w-100 rounded-3" title="Xóa bộ lọc">
                <i class="bi bi-x-circle"></i>
            </a>
        </div>
    </form>
</div>

{{-- Main Table --}}
<div class="card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
            <thead class="table-light text-uppercase text-secondary" style="font-size:0.72rem;">
                <tr>
                    <th>Tiêu đề sự cố</th>
                    <th>Người báo</th>
                    <th>Danh mục</th>
                    <th>Ưu tiên</th>
                    <th>KTV phụ trách</th>
                    <th>Trạng thái</th>
                    <th>Hạn SLA</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $t)
                @php
                    $isOverdue = $t->sla_deadline && now()->greaterThan($t->sla_deadline) && ! in_array($t->status, ['RESOLVED', 'CLOSED']);
                @endphp
                <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                    <td style="max-width:240px;">
                        <a href="{{ route('manager.tickets.show', $t->id) }}" class="fw-bold text-dark text-decoration-none">
                            {{ Str::limit($t->title, 45) }}
                        </a>
                        @if ($t->location)
                            <div class="text-muted" style="font-size:0.72rem;"><i class="bi bi-geo-alt me-1"></i>{{ $t->location }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-medium">{{ $t->requester->name }}</div>
                        <small class="text-muted" style="font-size:0.72rem;">{{ $t->requester->department?->name ?? 'Sinh viên' }}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $t->category?->name ?? '—' }}</span></td>
                    <td>
                        <span class="badge badge-status badge-priority-{{ $t->priority }}">
                            @switch($t->priority)
                                @case('HIGH')   <i class="bi bi-exclamation-circle-fill me-1"></i> Cao      @break
                                @case('MEDIUM') <i class="bi bi-dash-circle-fill me-1"></i> Trung bình @break
                                @case('LOW')    <i class="bi bi-arrow-down-circle-fill me-1"></i> Thấp     @break
                            @endswitch
                        </span>
                    </td>
                    <td>
                        @if ($t->currentAssignee)
                            <span class="text-success fw-medium"><i class="bi bi-person-check me-1"></i>{{ $t->currentAssignee->name }}</span>
                        @else
                            <button type="button" class="btn btn-sm btn-warning rounded-2 py-0 px-2" style="font-size:0.75rem;"
                                    onclick="openAssignModal({{ $t->id }}, '{{ addslashes($t->title) }}')">
                                <i class="bi bi-plus-lg"></i> Phân công
                            </button>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-status badge-{{ $t->status }}">
                            @switch($t->status)
                                @case('OPEN')        <i class="bi bi-record-circle-fill text-info me-1"></i> Mới gửi       @break
                                @case('IN_PROGRESS') <i class="bi bi-clock-history text-warning me-1"></i> Đang xử lý    @break
                                @case('RESOLVED')    <i class="bi bi-check-circle-fill text-success me-1"></i> Đã khắc phục  @break
                                @case('CLOSED')      <i class="bi bi-lock-fill text-secondary me-1"></i> Đã đóng       @break
                                @case('REOPENED')    <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Mở lại        @break
                            @endswitch
                        </span>
                    </td>
                    <td style="font-size:0.78rem;">
                        @if ($t->sla_deadline)
                            <span class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                {{ $t->sla_deadline->format('d/m H:i') }}
                                @if ($isOverdue) <i class="bi bi-exclamation-triangle-fill ms-1" title="Quá hạn!"></i> @endif
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border rounded-2" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size:0.85rem;">
                                <li>
                                    <a class="dropdown-item" href="{{ route('manager.tickets.show', $t->id) }}">
                                        <i class="bi bi-eye me-2 text-primary"></i> Xem chi tiết
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="openAssignModal({{ $t->id }}, '{{ addslashes($t->title) }}')">
                                        <i class="bi bi-person-plus me-2 text-success"></i> Phân công / Đổi KTV
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">Không tìm thấy ticket nào khớp với bộ lọc.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tickets->hasPages())
        <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center" style="font-size:0.82rem; color:#64748b;">
            <span>Hiển thị {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} / {{ $tickets->total() }} kết quả</span>
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Include Assign Modal --}}
@include('manager.tickets.partials.assign-modal')

@endsection

@push('scripts')
<script>
    function openAssignModal(ticketId, ticketTitle) {
        const form = document.getElementById('assignForm');
        form.action = `/manager/tickets/${ticketId}/assign`;
        document.getElementById('assignModalTicketTitle').textContent = `Ticket #${ticketId}: ${ticketTitle}`;
        new bootstrap.Modal(document.getElementById('assignModal')).show();
    }
</script>
@endpush
