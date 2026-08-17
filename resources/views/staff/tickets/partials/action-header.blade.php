{{-- partial: action-header.blade.php --}}
{{-- Thanh nút hành động nhanh 1-Click chuyển trạng thái Ticket dành cho KTV --}}

<div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; border-radius:16px;">
    <div class="card-body p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-3">

        {{-- Trạng thái hiện tại --}}
        <div class="d-flex align-items-center gap-3">
            <div style="width:44px; height:44px; background:rgba(255,255,255,0.2); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
            <div>
                <div style="font-size:0.75rem; text-uppercase; opacity:0.85; font-weight:600; letter-spacing:0.05em;">Trạng thái ticket hiện tại</div>
                <div class="fw-bold" style="font-size:1.05rem;">
                    @switch($ticket->status)
                        @case('OPEN')        <i class="bi bi-record-circle-fill text-info me-1"></i> Mới gửi (Chờ xử lý)       @break
                        @case('IN_PROGRESS') <i class="bi bi-clock-history text-warning me-1"></i> Đang trong quá trình khắc phục @break
                        @case('RESOLVED')    <i class="bi bi-check-circle-fill text-white me-1"></i> Đã khắc phục thành công   @break
                        @case('CLOSED')      <i class="bi bi-lock-fill text-light me-1"></i> Đã đóng phiếu             @break
                        @case('REOPENED')    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Đã được sinh viên mở lại  @break
                    @endswitch
                </div>
            </div>
        </div>

        {{-- Cụm Nút Đổi Trạng Thái 1-Click --}}
        <div class="d-flex align-items-center gap-2">

            {{-- Nếu chưa có KTV phụ trách -> Nút Tự nhận việc --}}
            @if (! $ticket->current_assignee_id)
                <form method="POST" action="{{ route('staff.tickets.claim', $ticket->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-light fw-bold text-success rounded-3 px-3 shadow-sm" style="font-size:0.875rem;">
                        <i class="bi bi-hand-index-thumb me-1"></i> Tự nhận xử lý ngay
                    </button>
                </form>
            @endif

            {{-- Nếu đã nhận & status = OPEN hoặc REOPENED -> Nút Bắt đầu xử lý --}}
            @if ($ticket->current_assignee_id === Auth::id() && in_array($ticket->status, ['OPEN', 'REOPENED']))
                <form method="POST" action="{{ route('staff.tickets.status', $ticket->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="IN_PROGRESS">
                    <button type="submit" class="btn btn-warning fw-bold text-dark rounded-3 px-3 shadow-sm" style="font-size:0.875rem;">
                        <i class="bi bi-play-fill me-1"></i> Bắt đầu xử lý
                    </button>
                </form>
            @endif

            {{-- Nếu status = IN_PROGRESS -> Nút Đã khắc phục xong --}}
            @if ($ticket->current_assignee_id === Auth::id() && $ticket->status === 'IN_PROGRESS')
                <form method="POST" action="{{ route('staff.tickets.status', $ticket->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="RESOLVED">
                    <button type="submit" class="btn btn-light fw-bold text-success rounded-3 px-3 shadow-sm" style="font-size:0.875rem;">
                        <i class="bi bi-check-circle-fill me-1 text-success"></i> Đã khắc phục xong
                    </button>
                </form>
            @endif

            {{-- Nếu status = RESOLVED -> Nút Đóng ticket --}}
            @if ($ticket->current_assignee_id === Auth::id() && $ticket->status === 'RESOLVED')
                <form method="POST" action="{{ route('staff.tickets.status', $ticket->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="CLOSED">
                    <button type="submit" class="btn btn-outline-light fw-bold rounded-3 px-3" style="font-size:0.875rem;">
                        <i class="bi bi-lock-fill me-1"></i> Đóng ticket
                    </button>
                </form>
            @endif

        </div>

    </div>
</div>
