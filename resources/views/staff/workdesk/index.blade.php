@extends('staff.layouts.app')

@section('title', 'Workdesk Dạng Bảng')

@push('styles')
<style>
    .workdesk-nav-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.75rem 1.25rem;
        background: transparent;
    }

    .workdesk-nav-tabs .nav-link:hover { color: #10b981; }

    .workdesk-nav-tabs .nav-link.active {
        color: #10b981;
        border-bottom-color: #10b981;
        background: transparent;
    }

    .sla-badge-bar {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .sla-green  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .sla-yellow { background: #fef9c3; color: #a16207; border: 1px solid #fef08a; }
    .sla-red    { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-slate-800 mb-0">Bàn làm việc Kỹ thuật (Workdesk)</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Danh sách công việc phân công &amp; Hàng chờ tiếp nhận xử lý sự cố</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.workdesk.kanban') }}" class="btn btn-outline-secondary rounded-3" style="font-size:0.85rem;">
            <i class="bi bi-kanban me-1"></i> Chuyển sang Kanban Board
        </a>
    </div>
</div>

{{-- Nav Tabs --}}
<div class="card overflow-hidden">
    <div class="card-header bg-white border-bottom-0 pb-0">
        <ul class="nav nav-tabs workdesk-nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'assigned' ? 'active' : '' }}" href="{{ route('staff.workdesk.index', ['tab' => 'assigned']) }}">
                    <i class="bi bi-person-check-fill me-1"></i> Ticket được giao cho tôi
                    <span class="badge bg-success ms-1">{{ $assignedCount }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'queue' ? 'active' : '' }}" href="{{ route('staff.workdesk.index', ['tab' => 'queue']) }}">
                    <i class="bi bi-inboxes-fill me-1"></i> Hàng chờ Nhóm Chuyên môn
                    <span class="badge bg-warning text-dark ms-1">{{ $queueCount }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">

        {{-- ── TAB 1: TICKET ĐƯỢC GIAO CHO TÔI ── --}}
        @if ($activeTab === 'assigned')
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                    <thead class="table-light text-uppercase text-secondary" style="font-size:0.72rem;">
                        <tr>
                            <th>Mã</th>
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
                            $now = now();
                            $deadline = $t->sla_deadline;
                            $isOverdue = $deadline && $now->greaterThan($deadline) && !in_array($t->status, ['RESOLVED', 'CLOSED']);

                            if ($deadline && !$isOverdue) {
                                $diffHours = $now->diffInHours($deadline);
                                $diffMins = $now->diffInMinutes($deadline) % 60;
                                $slaText = "Còn {$diffHours}h {$diffMins}p";
                                $slaClass = $diffHours < 2 ? 'sla-yellow' : 'sla-green';
                            } elseif ($isOverdue) {
                                $diffMins = $now->diffInMinutes($deadline);
                                $diffHours = floor($diffMins / 60);
                                $slaText = "Quá hạn {$diffHours}h " . ($diffMins % 60) . "p";
                                $slaClass = 'sla-red sla-overdue-flash';
                            } else {
                                $slaText = '—';
                                $slaClass = 'sla-green';
                            }
                        @endphp
                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                            <td class="font-monospace text-muted fw-bold">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td style="max-width:240px;">
                                <a href="{{ route('staff.tickets.show', $t->id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ Str::limit($t->title, 45) }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $t->requester->name }}</div>
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
                                <p class="mb-0" style="font-size:0.85rem;">Hãy sang tab "Hàng chờ Nhóm Chuyên môn" để tự nhận thêm việc nhé!</p>
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

        {{-- ── TAB 2: HÀNG CHỜ NHÓM CHUYÊN MÔN ── --}}
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                    <thead class="table-light text-uppercase text-secondary" style="font-size:0.72rem;">
                        <tr>
                            <th>Mã</th>
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
                            <td class="font-monospace text-muted fw-bold">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td style="max-width:240px;">
                                <a href="{{ route('staff.tickets.show', $t->id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ Str::limit($t->title, 45) }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $t->requester->name }}</div>
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
                                        <i class="bi bi-people-fill me-1"></i> Giao KTV
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 text-muted d-block mb-2"></i>
                                <h5>Hàng chờ nhóm hiện tại không có sự cố nào mới.</h5>
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

{{-- MODAL PHÂN CÔNG NỘI BỘ NHÓM KTV --}}
<div class="modal fade" id="staffAssignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold" style="color:#1e293b;"><i class="bi bi-people-fill text-success me-2"></i>Phân công / Chuyển giao KTV trong Nhóm</h5>
                    <p style="font-size:0.8rem; color:#94a3b8; margin:4px 0 0;" id="staffAssignModalTicketTitle">Chọn KTV tiếp nhận công việc</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="staffAssignForm">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Chọn Kỹ thuật viên tiếp nhận <span class="text-danger">*</span></label>
                        <div class="d-flex flex-column gap-2" style="max-height: 240px; overflow-y: auto;">
                            @foreach ($staffMembers as $stf)
                                @php $activeCnt = $stf->assignedTickets?->count() ?? 0; @endphp
                                <label class="p-2.5 border rounded-3 d-flex align-items-center justify-content-between cursor-pointer" style="background:#f8fafc; font-size:0.85rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="radio" name="staff_id" value="{{ $stf->id }}" required style="transform:scale(1.1);">
                                        <div>
                                            <div class="fw-bold" style="color:#1e293b;">{{ $stf->name }}</div>
                                            <small class="text-muted" style="font-size:0.75rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $stf->staffProfile?->shift ?? 'Ca trực' }} · SĐT: {{ $stf->staffProfile?->phone ?? '—' }}
                                            </small>
                                        </div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border" style="font-size:0.72rem;">Đang giữ: {{ $activeCnt }} ticket</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">Ghi chú bàn giao công việc <span class="text-muted font-normal">(Tùy chọn)</span></label>
                        <textarea name="note" rows="2" class="form-control rounded-3" style="font-size:0.85rem; resize:none;" placeholder="Dặn dò KTV đồng nghiệp về lưu ý phòng học/thiết bị..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-grow-1 rounded-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success flex-grow-1 fw-bold rounded-3">Xác nhận phân công</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openStaffAssignModal(ticketId, ticketTitle) {
        const form = document.getElementById('staffAssignForm');
        form.action = `/staff/tickets/${ticketId}/reassign`;
        document.getElementById('staffAssignModalTicketTitle').textContent = `Ticket #${ticketId}: ${ticketTitle}`;
        new bootstrap.Modal(document.getElementById('staffAssignModal')).show();
    }
</script>
@endpush
