@extends('staff.layouts.app')

@section('title', 'Workdesk - Bàn làm việc KTV')

@push('styles')
<style>
    .kpi-mini-card {
        background: white;
        border-radius: 16px;
        padding: 1.2rem 1.35rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 8px rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .kpi-mini-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.07);
    }

    .kpi-mini-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .kpi-mini-val {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .kpi-mini-lbl {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 600;
        margin-top: 2px;
    }

    .workdesk-nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.85rem 1.25rem;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        border-radius: 0;
    }

    .workdesk-nav-tabs .nav-link:hover {
        color: var(--stf-primary);
        background: transparent;
    }

    .workdesk-nav-tabs .nav-link.active {
        color: var(--stf-primary);
        border-bottom-color: var(--stf-primary);
        background: transparent;
        font-weight: 700;
    }

    .table-workdesk tbody tr {
        transition: background 0.15s;
    }

    .table-workdesk tbody tr:hover {
        background-color: #f8fafc;
    }

    .staff-select-card:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1 !important;
    }

    .staff-select-card:has(input:checked) {
        background-color: #eff6ff !important;
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 1px #0d6efd;
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Bàn làm việc Kỹ thuật viên</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Quản lý danh sách sự cố được giao &amp; Hàng chờ tiếp nhận chung toàn bộ hệ thống</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.workdesk.kanban') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold" style="font-size:0.82rem;">
            <i class="bi bi-kanban-fill me-1"></i> Xem Thẻ Kanban
        </a>
    </div>
</div>

{{-- ── TOP 3 MINI KPI CARDS ── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="kpi-mini-card">
            <div class="kpi-mini-icon" style="background: linear-gradient(135deg, #0d6efd 0%, #0284c7 100%);">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div>
                <div class="kpi-mini-val">{{ number_format($assignedCount) }}</div>
                <div class="kpi-mini-lbl">Ticket được giao cho tôi</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="kpi-mini-card">
            <div class="kpi-mini-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="bi bi-inboxes-fill"></i>
            </div>
            <div>
                <div class="kpi-mini-val text-warning">{{ number_format($queueCount) }}</div>
                <div class="kpi-mini-lbl">Hàng chờ tiếp nhận chung</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="kpi-mini-card">
            <div class="kpi-mini-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="kpi-mini-val text-success">Active</div>
                <div class="kpi-mini-lbl">Trạng thái ca trực Sẵn sàng</div>
            </div>
        </div>
    </div>
</div>

{{-- Nav Tabs --}}
<div class="card overflow-hidden border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-header bg-white border-bottom-0 pb-0">
        <ul class="nav nav-tabs workdesk-nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'assigned' ? 'active' : '' }}" href="{{ route('staff.workdesk.index', ['tab' => 'assigned']) }}">
                    <i class="bi bi-person-check-fill me-1"></i> Ticket được giao cho tôi
                    <span class="badge bg-primary rounded-pill ms-1.5 px-2 py-0.5">{{ $assignedCount }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'queue' ? 'active' : '' }}" href="{{ route('staff.workdesk.index', ['tab' => 'queue']) }}">
                    <i class="bi bi-inboxes-fill me-1"></i> Hàng chờ chung
                    <span class="badge bg-warning text-dark rounded-pill ms-1.5 px-2 py-0.5">{{ $queueCount }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">

        {{-- ── TAB 1: TICKET ĐƯỢC GIAO CHO TÔI ── --}}
        @if ($activeTab === 'assigned')
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-workdesk" style="font-size:0.875rem;">
                    <thead class="table-light text-uppercase text-secondary" style="font-size:0.72rem;">
                        <tr>
                            <th>Tiêu đề sự cố</th>
                            <th>Người báo</th>
                            <th>Danh mục</th>
                            <th>Vị trí sự cố</th>
                            <th>Trạng thái</th>
                            <th>Đồng hồ đếm ngược SLA</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assignedTickets as $t)
                        @php
                            $isOverdue = $t->sla_deadline && now()->greaterThan($t->sla_deadline) && !in_array($t->status, ['RESOLVED','CLOSED']);
                            $diff = $t->sla_deadline ? now()->diffForHumans($t->sla_deadline, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW]) : null;

                            if ($isOverdue) {
                                $slaClass = 'sla-danger';
                                $slaText  = 'Quá hạn ' . Str::after($diff, 'sau ');
                            } elseif ($t->status === 'RESOLVED' || $t->status === 'CLOSED') {
                                $slaClass = 'sla-ok';
                                $slaText  = 'Đã hoàn thành';
                            } elseif ($t->sla_deadline) {
                                $slaClass = 'sla-warning';
                                $slaText  = 'Còn ' . Str::before($diff, ' nữa');
                            } else {
                                $slaClass = 'sla-ok';
                                $slaText  = '—';
                            }
                        @endphp
                        <tr>
                            <td style="max-width:240px;">
                                <a href="{{ route('staff.tickets.show', $t->id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ Str::limit($t->title, 45) }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $t->requester->name }}</div>
                                <small class="text-muted" style="font-size:0.72rem;">{{ $t->requester->department?->name ?? 'Sinh viên' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $t->category?->name ?? '—' }}</span></td>
                            <td class="text-danger fw-medium" style="font-size:0.8rem;">
                                <i class="bi bi-geo-alt me-1"></i>{{ $t->location ?: 'Không xác định' }}
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
                            <td>
                                <span class="sla-badge-bar {{ $slaClass }}">
                                    <i class="bi {{ $isOverdue ? 'bi-alarm-fill' : 'bi-clock' }}"></i>
                                    {{ $slaText }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('staff.tickets.show', $t->id) }}" class="btn btn-sm btn-success rounded-2 fw-bold" style="font-size:0.78rem;">
                                        <i class="bi bi-pencil-square me-1"></i> Xử lý ngay
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-2" style="font-size:0.78rem;" title="Chuyển giao cho KTV khác trong nhóm"
                                            onclick="openStaffAssignModal({{ $t->id }}, '{{ addslashes($t->title) }}')">
                                        <i class="bi bi-person-gear"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-check-circle display-4 text-success d-block mb-2"></i>
                                <h5>Hiện tại bạn không có ticket nào cần xử lý.</h5>
                                <p class="mb-0" style="font-size:0.85rem;">Hãy sang tab "Hàng chờ chung" để tự nhận thêm việc nhé!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($assignedTickets->hasPages())
                <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center" style="font-size:0.82rem; color:#64748b;">
                    <span>Hiển thị {{ $assignedTickets->firstItem() }}–{{ $assignedTickets->lastItem() }} / {{ $assignedTickets->total() }} kết quả</span>
                    {{ $assignedTickets->links('pagination::bootstrap-5') }}
                </div>
            @endif

        {{-- ── TAB 2: HÀNG CHỜ CHUNG ── --}}
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-workdesk" style="font-size:0.875rem;">
                    <thead class="table-light text-uppercase text-secondary" style="font-size:0.72rem;">
                        <tr>
                            <th>Tiêu đề sự cố</th>
                            <th>Người báo</th>
                            <th>Danh mục</th>
                            <th>Vị trí xảy ra sự cố</th>
                            <th>Mức ưu tiên</th>
                            <th>Hạn SLA</th>
                            <th>Thao tác Nhóm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groupQueueTickets as $t)
                        @php
                            $isOverdue = $t->sla_deadline && now()->greaterThan($t->sla_deadline);
                        @endphp
                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                            <td style="max-width:240px;">
                                <a href="{{ route('staff.tickets.show', $t->id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ Str::limit($t->title, 45) }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $t->requester->name }}</div>
                                <small class="text-muted" style="font-size:0.72rem;">{{ $t->requester->department?->name ?? 'Sinh viên' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $t->category?->name ?? '—' }}</span></td>
                            <td class="text-danger fw-medium" style="font-size:0.8rem;">
                                <i class="bi bi-geo-alt me-1"></i>{{ $t->location ?: 'Không xác định' }}
                            </td>
                            <td>
                                <span class="badge badge-status badge-priority-{{ $t->priority }}">
                                    @switch($t->priority)
                                        @case('HIGH')   <i class="bi bi-exclamation-circle-fill me-1"></i> Cao      @break
                                        @case('MEDIUM') <i class="bi bi-dash-circle-fill me-1"></i> Trung bình @break
                                        @case('LOW')    <i class="bi bi-arrow-down-circle-fill me-1"></i> Thấp     @break
                                    @endswitch
                                </span>
                            </td>
                            <td style="font-size:0.78rem;" class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                {{ $t->sla_deadline ? $t->sla_deadline->format('d/m H:i') : '—' }}
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <form method="POST" action="{{ route('staff.tickets.claim', $t->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary rounded-2 fw-bold" style="font-size:0.78rem;">
                                            <i class="bi bi-hand-index-thumb me-1"></i> Tự nhận
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-2 fw-bold" style="font-size:0.78rem;"
                                            onclick="openStaffAssignModal({{ $t->id }}, '{{ addslashes($t->title) }}')">
                                        <i class="bi bi-person-gear me-1"></i> Phân công
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-check-circle display-4 text-success d-block mb-2"></i>
                                <h5>Hàng chờ chung hiện tại không có sự cố nào.</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($groupQueueTickets->hasPages())
                <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center" style="font-size:0.82rem; color:#64748b;">
                    <span>Hiển thị {{ $groupQueueTickets->firstItem() }}–{{ $groupQueueTickets->lastItem() }} / {{ $groupQueueTickets->total() }} kết quả</span>
                    {{ $groupQueueTickets->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif

    </div>
</div>

{{-- Modal Phân công Nội bộ KTV --}}
<div class="modal fade" id="staffAssignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <form method="POST" id="staffAssignForm">
                @csrf
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title h6 fw-bold mb-0">
                        <i class="bi bi-person-gear me-2"></i>Phân công / Chuyển giao KTV
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-3" style="font-size:0.85rem;" id="assignModalTicketTitle"></p>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">
                            Chọn Kỹ thuật viên phụ trách <span class="text-danger">*</span>
                        </label>
                        <div class="staff-select-list d-flex flex-column gap-2" style="max-height: 280px; overflow-y: auto; padding-right: 4px;">
                            @foreach ($otherStaffs as $s)
                                <label class="staff-select-card border rounded-3 p-2.5 d-flex align-items-center justify-content-between" style="cursor:pointer; transition:all 0.15s;">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <input type="radio" name="staff_id" value="{{ $s->id }}" class="form-check-input mt-0 fs-5" required>
                                        <div style="width:38px; height:38px; border-radius:10px; background:linear-gradient(135deg, #0d6efd 0%, #0284c7 100%); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.9rem; flex-shrink:0;">
                                            {{ strtoupper(substr($s->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size:0.88rem; line-height:1.2;">{{ $s->name }}</div>
                                            <small class="text-muted" style="font-size:0.75rem;">{{ $s->staffProfile?->title ?: 'Kỹ thuật viên TLU' }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-2 px-2 py-1" style="font-size:0.72rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $s->staffProfile?->shift ?? 'Ca Trực' }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Ghi chú nội bộ</label>
                        <textarea name="note" class="form-control rounded-3" rows="2" placeholder="Ghi chú nguyên nhân chuyển giao..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">Xác nhận phân công</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openStaffAssignModal(ticketId, title) {
        document.getElementById('assignModalTicketTitle').innerText = 'Sự cố: #' + String(ticketId).padStart(4, '0') + ' - ' + title;
        document.getElementById('staffAssignForm').action = '/staff/tickets/' + ticketId + '/reassign';
        new bootstrap.Modal(document.getElementById('staffAssignModal')).show();
    }
</script>
@endpush
