@extends('manager.layouts.app')

@section('title', 'Chi tiết Ticket #' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT))

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-slate-800 mb-0">
            Ticket #{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}: {{ $ticket->title }}
        </h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">
            Người báo: <strong>{{ $ticket->requester->name }}</strong> ({{ $ticket->requester->department?->name ?? 'Sinh viên' }}) · {{ $ticket->created_at->format('d/m/Y H:i') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('manager.tickets.index') }}" class="btn btn-outline-secondary rounded-3" style="font-size:0.85rem;">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
        @if (!in_array($ticket->status, ['CLOSED', 'RESOLVED']))
            <button type="button" class="btn btn-primary rounded-3 fw-bold" style="font-size:0.85rem;"
                    onclick="openAssignModal({{ $ticket->id }}, '{{ addslashes($ticket->title) }}')">
                <i class="bi bi-person-plus-fill me-1"></i> Phân công / Đổi KTV
            </button>
        @endif
    </div>
</div>

<div class="row g-4">

    {{-- Cột Trái: Thông tin sự cố & Lịch sử log --}}
    <div class="col-lg-8">

        {{-- Card Thông tin sự cố --}}
        <div class="card mb-4">
            <div class="card-header bg-white font-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-text me-2 text-primary"></i>Nội dung phản ánh sự cố</span>
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
            <div class="card-body">
                <div class="row g-3 mb-3" style="font-size:0.875rem;">
                    <div class="col-sm-6">
                        <span class="text-muted">Danh mục:</span>
                        <strong class="ms-1 text-dark">{{ $ticket->category?->name ?? '—' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Vị trí sự cố:</span>
                        <strong class="ms-1 text-danger"><i class="bi bi-geo-alt me-1"></i>{{ $ticket->location ?: 'Không xác định' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Mức ưu tiên:</span>
                        <span class="badge badge-status badge-priority-{{ $ticket->priority }} ms-1">
                            @switch($ticket->priority)
                                @case('HIGH')   <i class="bi bi-exclamation-circle-fill me-1"></i> Cao      @break
                                @case('MEDIUM') <i class="bi bi-dash-circle-fill me-1"></i> Trung bình @break
                                @case('LOW')    <i class="bi bi-arrow-down-circle-fill me-1"></i> Thấp     @break
                            @endswitch
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Hạn xử lý SLA:</span>
                        <strong class="ms-1 {{ $ticket->sla_deadline && now()->greaterThan($ticket->sla_deadline) && !in_array($ticket->status, ['RESOLVED','CLOSED']) ? 'text-danger fw-bold' : 'text-dark' }}">
                            {{ $ticket->sla_deadline ? $ticket->sla_deadline->format('d/m/Y H:i') : '—' }}
                        </strong>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3" style="font-size:0.875rem; white-space:pre-wrap; line-height:1.6;">
                    {{ $ticket->description }}
                </div>

                {{-- Ảnh đính kèm --}}
                @if ($ticket->attachments->isNotEmpty())
                    <div class="mt-3">
                        <div class="fw-bold mb-2 text-secondary" style="font-size:0.78rem;">Ảnh / Tệp minh chứng gốc:</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($ticket->attachments as $att)
                                @if (str_starts_with($att->file_type ?? '', 'image/'))
                                    <img src="{{ $att->url }}"
                                         alt="Ảnh minh chứng"
                                         style="width:80px; height:80px; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0; cursor:pointer;"
                                         onclick="window.open(this.src)">
                                @else
                                    <a href="{{ $att->url }}" target="_blank"
                                       class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 rounded-2">
                                        <i class="bi bi-paperclip"></i> {{ basename($att->file_path) }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Lịch sử Phân công & Log đổi trạng thái --}}
        <div class="card mb-4">
            <div class="card-header bg-white font-bold py-3">
                <i class="bi bi-clock-history me-2 text-primary"></i>Lịch sử Phân công &amp; Nhật ký Trạng thái
            </div>
            <div class="card-body">
                <div class="timeline" style="border-left: 2px solid #e2e8f0; margin-left: 10px; padding-left: 18px;">

                    {{-- Assignments --}}
                    @foreach ($ticket->assignments as $asg)
                        <div class="mb-3 position-relative">
                            <div class="position-absolute" style="left:-25px; top:2px; width:12px; height:12px; border-radius:50%; background:#0d6efd;"></div>
                            <div class="fw-bold" style="font-size:0.85rem; color:#1e293b;">
                                Phân công cho: <span class="text-primary">{{ $asg->assignedToStaff->name }}</span>
                            </div>
                            <small class="text-muted" style="font-size:0.75rem;">
                                Phân công bởi {{ $asg->assignedByUser->name }} · {{ $asg->assigned_at->format('H:i d/m/Y') }}
                            </small>
                            @if ($asg->note)
                                <div class="mt-1 p-2 bg-light rounded-2 font-italic text-secondary" style="font-size:0.8rem;">
                                    "{{ $asg->note }}"
                                </div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Status Logs --}}
                    @foreach ($ticket->statusLogs as $log)
                        <div class="mb-3 position-relative">
                            <div class="position-absolute" style="left:-25px; top:2px; width:12px; height:12px; border-radius:50%; background:#10b981;"></div>
                            <div style="font-size:0.85rem; color:#1e293b;">
                                Đổi trạng thái:
                                <span class="badge bg-light text-dark border me-1">
                                    @switch($log->old_status)
                                        @case('OPEN')        Mới gửi @break
                                        @case('IN_PROGRESS') Đang xử lý @break
                                        @case('RESOLVED')    Đã khắc phục @break
                                        @case('CLOSED')      Đã đóng @break
                                        @case('REOPENED')    Mở lại @break
                                        @default             Khởi tạo ban đầu
                                    @endswitch
                                </span>
                                <i class="bi bi-arrow-right text-muted"></i>
                                <span class="badge badge-status badge-{{ $log->new_status }} ms-1">
                                    @switch($log->new_status)
                                        @case('OPEN')        <i class="bi bi-record-circle-fill text-info me-1"></i> Mới gửi (Chờ xử lý) @break
                                        @case('IN_PROGRESS') <i class="bi bi-clock-history text-warning me-1"></i> Đang xử lý @break
                                        @case('RESOLVED')    <i class="bi bi-check-circle-fill text-success me-1"></i> Đã khắc phục @break
                                        @case('CLOSED')      <i class="bi bi-lock-fill text-secondary me-1"></i> Đã đóng @break
                                        @case('REOPENED')    <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Mở lại @break
                                    @endswitch
                                </span>
                            </div>
                            <small class="text-muted" style="font-size:0.75rem;">
                                Thực hiện bởi {{ $log->changedBy->name }} · {{ $log->created_at->format('H:i d/m/Y') }}
                            </small>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

    </div>

    {{-- Cột Phải: Thông tin KTV phụ trách & Khảo sát 5 sao --}}
    <div class="col-lg-4">

        {{-- Card KTV Phụ trách --}}
        <div class="card mb-4">
            <div class="card-header bg-white font-bold py-3">
                <i class="bi bi-person-badge me-2 text-primary"></i>Kỹ thuật viên Phụ trách
            </div>
            <div class="card-body">
                @if ($ticket->currentAssignee)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="user-avatar-mgr" style="width:44px; height:44px; font-size:1.2rem;">
                            {{ strtoupper(substr($ticket->currentAssignee->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:0.95rem;">{{ $ticket->currentAssignee->name }}</div>
                            <div class="text-muted" style="font-size:0.8rem;">{{ $ticket->currentAssignee->email }}</div>
                        </div>
                    </div>

                    @if ($ticket->currentAssignee->staffProfile)
                        <div class="p-2 bg-light rounded-3 mb-3" style="font-size:0.8rem;">
                            <div><i class="bi bi-clock me-1 text-primary"></i>Ca trực: {{ $ticket->currentAssignee->staffProfile->shift }}</div>
                            <div><i class="bi bi-telephone me-1 text-success"></i>SĐT: {{ $ticket->currentAssignee->staffProfile->phone }}</div>
                        </div>
                    @endif

                    @if (!in_array($ticket->status, ['CLOSED', 'RESOLVED']))
                        <button type="button" class="btn btn-outline-primary w-100 rounded-3" style="font-size:0.85rem;"
                                onclick="openAssignModal({{ $ticket->id }}, '{{ addslashes($ticket->title) }}')">
                            <i class="bi bi-arrow-repeat me-1"></i> Đổi Kỹ thuật viên khác
                        </button>
                    @else
                        <div class="p-2 bg-light text-center rounded-3 text-muted" style="font-size:0.78rem;">
                            <i class="bi bi-lock-fill me-1 text-secondary"></i> Sự cố đã kết thúc (Không thể đổi KTV)
                        </div>
                    @endif
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-hourglass-split display-6 text-warning d-block mb-2"></i>
                        <p class="mb-2" style="font-size:0.85rem;">Chưa có Kỹ thuật viên phụ trách phiếu này.</p>
                        <button type="button" class="btn btn-primary rounded-3 fw-bold px-4" style="font-size:0.85rem;"
                                onclick="openAssignModal({{ $ticket->id }}, '{{ addslashes($ticket->title) }}')">
                            <i class="bi bi-plus-lg me-1"></i> Phân công ngay
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card Đánh giá 5 sao --}}
        @if ($ticket->satisfactionSurvey)
            <div class="card mb-4 border-warning" style="background:#fffbeb;">
                <div class="card-header bg-transparent font-bold py-3 text-warning border-warning">
                    <i class="bi bi-star-fill me-2"></i>Đánh giá từ Người báo lỗi
                </div>
                <div class="card-body">
                    <div class="display-6 fw-bold text-warning text-center mb-1">
                        {{ $ticket->satisfactionSurvey->rating_stars }} ⭐
                    </div>
                    <div class="text-center text-muted mb-2" style="font-size:0.8rem;">
                        {{ $ticket->satisfactionSurvey->created_at->format('d/m/Y H:i') }}
                    </div>
                    @if ($ticket->satisfactionSurvey->comment)
                        <div class="p-2 bg-white rounded-3 border text-dark" style="font-size:0.85rem; font-style:italic;">
                            "{{ $ticket->satisfactionSurvey->comment }}"
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>

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
