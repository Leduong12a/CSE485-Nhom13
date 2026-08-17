{{-- partial: reopen-modal.blade.php --}}
{{-- Nút + Modal mở lại sự cố khi ticket đã RESOLVED / CLOSED --}}

{{-- Trigger Banner --}}
<div class="detail-card mt-0" style="border: 1.5px solid #fecdd3;">
    <div class="detail-card-body" style="padding: 1rem 1.25rem;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div style="font-size:0.875rem; font-weight:600; color:#be123c;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Sự cố chưa được khắc phục hoàn toàn?
                </div>
                <div style="font-size:0.78rem; color:#94a3b8; margin-top:3px;">
                    Nếu bạn vẫn gặp vấn đề, hãy mở lại phiếu này để được hỗ trợ thêm.
                </div>
            </div>
            <button type="button"
                    class="btn"
                    style="border-radius:9px; border:1.5px solid #dc3545; color:#dc3545; font-size:0.83rem; font-weight:600; padding:0.45rem 1rem; white-space:nowrap; transition:all 0.15s;"
                    data-bs-toggle="modal" data-bs-target="#reopenModal"
                    onmouseover="this.style.background='#dc3545'; this.style.color='white';"
                    onmouseout="this.style.background='transparent'; this.style.color='#dc3545';">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Mở lại sự cố
            </button>
        </div>
    </div>
</div>

{{-- Modal nhập lý do mở lại --}}
<div class="modal fade" id="reopenModal" tabindex="-1" aria-labelledby="reopenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">

            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold" id="reopenModalLabel" style="color:#1e293b;">
                        <i class="bi bi-arrow-counterclockwise text-danger me-2"></i>Mở lại phiếu sự cố
                    </h5>
                    <p style="font-size:0.8rem; color:#94a3b8; margin:4px 0 0;">
                        Ticket #{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }} — {{ Str::limit($ticket->title, 50) }}
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('student.tickets.reopen', $ticket->id) }}" id="reopenForm">
                @csrf
                <div class="modal-body px-4 py-3">

                    <div class="p-3 rounded-3 mb-3" style="background:#fff0f3; border:1px solid #fecdd3; font-size:0.8rem; color:#be123c;">
                        <i class="bi bi-info-circle me-1"></i>
                        Phiếu sẽ được chuyển trở lại trạng thái <strong>Mở lại</strong> và thông báo đến Kỹ thuật viên phụ trách.
                    </div>

                    <div>
                        <label for="reopenReason" class="form-label" style="font-size:0.85rem; font-weight:600; color:#374151;">
                            Lý do mở lại sự cố <span class="text-danger">*</span>
                        </label>
                        <textarea id="reopenReason" name="reason" rows="4"
                            class="form-control @error('reason') is-invalid @enderror"
                            placeholder="Mô tả cụ thể vấn đề vẫn còn tồn tại sau khi xử lý..."
                            required minlength="10" maxlength="500"
                            style="border-radius:10px; border:1.5px solid #e5e7eb; font-size:0.875rem; resize:none;"></textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div style="font-size:0.7rem; color:#94a3b8; margin-top:4px; text-align:right;">
                            <span id="reasonCount">0</span>/500 ký tự
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-grow-1"
                            style="border-radius:9px;" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger flex-grow-1"
                            style="border-radius:9px; font-weight:600;" id="btnReopen">
                        <span id="reopenText"><i class="bi bi-arrow-counterclockwise me-1"></i> Xác nhận mở lại</span>
                        <span id="reopenSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span>Đang xử lý...
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // Đếm ký tự lý do
    document.getElementById('reopenReason').addEventListener('input', function() {
        document.getElementById('reasonCount').textContent = this.value.length;
    });

    // Loading khi submit
    document.getElementById('reopenForm').addEventListener('submit', function() {
        document.getElementById('reopenText').classList.add('d-none');
        document.getElementById('reopenSpinner').classList.remove('d-none');
        document.getElementById('btnReopen').disabled = true;
    });
</script>
