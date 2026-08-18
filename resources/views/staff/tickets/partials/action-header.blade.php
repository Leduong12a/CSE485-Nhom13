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
        <div class="d-flex align-items-center gap-2 flex-wrap">

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

            {{-- Nút Trả về hàng chờ: chỉ hiện khi KTV đang phụ trách & ticket chưa hoàn thành --}}
            @if ($ticket->current_assignee_id === Auth::id() && ! in_array($ticket->status, ['RESOLVED', 'CLOSED']))
                <button type="button"
                        class="btn btn-outline-light fw-bold rounded-3 px-3"
                        style="font-size:0.875rem; border-color:rgba(255,255,255,0.5);"
                        data-bs-toggle="modal" data-bs-target="#releaseModal">
                    <i class="bi bi-box-arrow-left me-1"></i> Yêu cầu phân công lại
                </button>
            @endif

        </div>

    </div>
</div>

{{-- Modal Trả về hàng chờ --}}
@if ($ticket->current_assignee_id === Auth::id() && ! in_array($ticket->status, ['RESOLVED', 'CLOSED']))
<div class="modal fade" id="releaseModal" tabindex="-1" aria-labelledby="releaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="releaseModalLabel">
                    <i class="bi bi-box-arrow-left text-warning me-2"></i>Yêu cầu phân công lại
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('staff.tickets.release', $ticket->id) }}">
                @csrf
                <div class="modal-body pt-2">
                    <p class="text-muted" style="font-size:0.875rem;">
                        Ticket sẽ được trả về chưa phân công và <strong>Quản lý sẽ phân công lại</strong> cho KTV khác phù hợp hơn.
                        Vui lòng nhập lý do để Quản lý nắm được tình huống.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.875rem;">
                            Lý do trả lại <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason" rows="3" required minlength="10" maxlength="500"
                            class="form-control rounded-3"
                            placeholder="VD: Tôi đang bận xử lý sự cố khẩn cấp khác, hoặc ticket này không đúng chuyên môn của tôi..."
                            style="font-size:0.875rem; resize:none;"></textarea>
                        <div class="form-text">Tối thiểu 10 ký tự.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning fw-bold rounded-3 text-dark">
                        <i class="bi bi-box-arrow-left me-1"></i> Xác nhận yêu cầu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
