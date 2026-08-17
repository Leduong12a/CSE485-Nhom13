{{-- partial: assign-modal.blade.php --}}
{{-- Modal Phân công Kỹ thuật viên phụ trách Ticket --}}

<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold" id="assignModalLabel" style="color:#1e293b;">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i>Phân công Kỹ thuật viên
                    </h5>
                    <p style="font-size:0.8rem; color:#94a3b8; margin:4px 0 0;" id="assignModalTicketTitle">
                        Phân công xử lý sự cố
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="" id="assignForm">
                @csrf
                <div class="modal-body px-4 py-3">

                    <div class="mb-3">
                        <label class="form-label fw-bold style-085" style="font-size:0.85rem; color:#374151;">
                            Chọn Kỹ thuật viên <span class="text-danger">*</span>
                        </label>

                        <div class="d-flex flex-column gap-2" style="max-height: 260px; overflow-y: auto;">
                            @foreach ($staffMembers as $staff)
                                @php
                                    $activeTicketCount = $staff->assignedTickets?->count() ?? 0;
                                @endphp
                                <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between cursor-pointer staff-radio-item"
                                       style="transition: all 0.15s; background:#f8fafc;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="radio" name="staff_id" value="{{ $staff->id }}" required style="transform: scale(1.1);">
                                        <div>
                                            <div class="fw-bold" style="font-size:0.875rem; color:#1e293b;">{{ $staff->name }}</div>
                                            <small class="text-muted" style="font-size:0.75rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $staff->staffProfile?->shift ?? 'Ca trực cố định' }}
                                                @if ($staff->staffProfile?->phone)
                                                    · <i class="bi bi-telephone me-1"></i>{{ $staff->staffProfile->phone }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <span class="badge {{ $activeTicketCount > 2 ? 'bg-warning text-dark' : 'bg-primary-subtle text-primary border' }}" style="font-size:0.72rem;">
                                        Đang giữ: {{ $activeTicketCount }} ticket
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Chỉ đạo chuyên môn --}}
                    <div class="mb-3">
                        <label for="assignNote" class="form-label fw-bold" style="font-size:0.85rem; color:#374151;">
                            Ghi chú / Chỉ đạo chuyên môn <span class="text-muted font-normal">(Tùy chọn)</span>
                        </label>
                        <textarea id="assignNote" name="note" rows="3"
                                  class="form-control"
                                  placeholder="VD: Kiểm tra lại dây cáp HDMI phòng 301 gấp..."
                                  style="border-radius:10px; border:1.5px solid #e5e7eb; font-size:0.85rem; resize:none;"></textarea>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-grow-1" style="border-radius:10px;" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary flex-grow-1 fw-bold" style="border-radius:10px;">
                        <i class="bi bi-check-lg me-1"></i> Phân công ngay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .staff-radio-item:hover { border-color: #0d6efd !important; background: #eff6ff !important; }
</style>
