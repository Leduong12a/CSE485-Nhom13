@extends('manager.layouts.app')

@section('title', 'Báo cáo Vi phạm SLA')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-danger mb-0"><i class="bi bi-exclamation-octagon-fill me-2"></i>Báo cáo Vi phạm SLA</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Danh sách tất cả phiếu sự cố đã quá thời gian cam kết xử lý (SLA Overdue) chưa hoàn thành</p>
    </div>
    <div>
        <span class="badge bg-danger px-3 py-2 rounded-pill" style="font-size:0.85rem;">
            <i class="bi bi-alarm-fill me-1"></i> {{ $overdueTickets->total() }} Ticket Quá hạn
        </span>
    </div>
</div>

{{-- Banner Cảnh báo --}}
<div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center gap-3" style="background:#fff1f2; border-left: 5px solid #dc3545 !important;">
    <i class="bi bi-exclamation-triangle-fill fs-2 text-danger"></i>
    <div>
        <h6 class="fw-bold text-danger mb-1">Cảnh báo Trễ hạn xử lý sự cố!</h6>
        <div style="font-size:0.83rem; color:#be123c;">
            Các sự cố dưới đây đã vượt quá mốc thời gian cam kết SLA. Trưởng bộ phận vui lòng đôn đốc Kỹ thuật viên phụ trách hoặc tiến hành phân công lại cho KTV khác gấp.
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card border-danger border-opacity-25 overflow-hidden shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
            <thead class="table-light text-uppercase text-secondary" style="font-size:0.72rem;">
                <tr>
                    <th>Mã</th>
                    <th>Tiêu đề sự cố</th>
                    <th>Người báo</th>
                    <th>Danh mục / SLA</th>
                    <th>KTV Phụ trách</th>
                    <th>Hạn SLA ban đầu</th>
                    <th>Thời gian Quá hạn</th>
                    <th>Thao tác Khẩn cấp</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($overdueTickets as $t)
                @php
                    $overdueMinutes = now()->diffInMinutes($t->sla_deadline);
                    $overdueHours = floor($overdueMinutes / 60);
                    $remainMins = $overdueMinutes % 60;
                @endphp
                <tr class="table-danger">
                    <td class="font-monospace text-danger fw-bold">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="max-width:240px;">
                        <a href="{{ route('manager.tickets.show', $t->id) }}" class="fw-bold text-danger text-decoration-none">
                            {{ Str::limit($t->title, 45) }}
                        </a>
                        @if ($t->location)
                            <div class="text-muted" style="font-size:0.72rem;"><i class="bi bi-geo-alt me-1"></i>{{ $t->location }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-medium text-dark">{{ $t->requester->name }}</div>
                        <small class="text-muted" style="font-size:0.72rem;">{{ $t->requester->department?->name ?? 'Sinh viên' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-white text-dark border">{{ $t->category?->name ?? '—' }}</span>
                        <div class="text-muted mt-1" style="font-size:0.7rem;">SLA {{ $t->category?->sla_hours }}h</div>
                    </td>
                    <td>
                        @if ($t->currentAssignee)
                            <span class="text-dark fw-bold"><i class="bi bi-person-fill me-1 text-primary"></i>{{ $t->currentAssignee->name }}</span>
                        @else
                            <span class="text-danger font-italic fw-bold"><i class="bi bi-exclamation-circle me-1"></i>Chưa phân công</span>
                        @endif
                    </td>
                    <td style="font-size:0.8rem;" class="text-secondary">
                        {{ $t->sla_deadline ? $t->sla_deadline->format('d/m/Y H:i') : '—' }}
                    </td>
                    <td>
                        <span class="badge bg-danger text-white px-2 py-1" style="font-size:0.78rem;">
                            <i class="bi bi-clock-history me-1"></i>
                            Trễ {{ $overdueHours > 0 ? "{$overdueHours}h " : '' }}{{ $remainMins }}p
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-danger fw-bold rounded-2"
                                    onclick="openAssignModal({{ $t->id }}, '{{ addslashes($t->title) }}')">
                                <i class="bi bi-arrow-repeat me-1"></i> Đổi KTV khẩn cấp
                            </button>
                            <a href="{{ route('manager.tickets.show', $t->id) }}" class="btn btn-sm btn-outline-secondary rounded-2">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-success">
                        <i class="bi bi-check-circle-fill display-4 text-success d-block mb-2"></i>
                        <h5>Tuyệt vời! Không có sự cố nào bị trễ hạn SLA.</h5>
                        <p class="text-muted mb-0" style="font-size:0.85rem;">Tất cả các phiếu phản ánh sự cố đều được xử lý đúng cam kết.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($overdueTickets->hasPages())
        <div class="px-3 py-3 border-top d-flex justify-content-between align-items-center" style="font-size:0.82rem; color:#64748b;">
            <span>Hiển thị {{ $overdueTickets->firstItem() }}–{{ $overdueTickets->lastItem() }} / {{ $overdueTickets->total() }} kết quả</span>
            {{ $overdueTickets->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

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
