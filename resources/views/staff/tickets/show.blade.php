@extends('staff.layouts.app')

@section('title', 'Xử lý Ticket #' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT))

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
        <a href="{{ route('staff.workdesk.index') }}" class="btn btn-outline-secondary rounded-3" style="font-size:0.85rem;">
            <i class="bi bi-arrow-left me-1"></i> Quay lại Workdesk
        </a>
    </div>
</div>

{{-- ── 1. QUICK ACTION HEADER 1-CLICK ── --}}
@include('staff.tickets.partials.action-header')

{{-- ── 2. MANAGER NOTE (NẾU CÓ) ── --}}
@include('staff.tickets.partials.manager-note')

<div class="row g-4">

    {{-- Cột Trái: Nội dung sự cố + Gallery ảnh + Chatstream --}}
    <div class="col-lg-7">

        {{-- Card Nội dung sự cố --}}
        <div class="card mb-4">
            <div class="card-header bg-white font-bold py-3">
                <i class="bi bi-file-text me-2 text-success"></i>Nội dung sự cố cần hỗ trợ
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3" style="font-size:0.875rem;">
                    <div class="col-sm-6">
                        <span class="text-muted">Danh mục:</span>
                        <strong class="ms-1 text-dark">{{ $ticket->category?->name ?? '—' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Vị trí xảy ra:</span>
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
                        <span class="text-muted">Hạn SLA:</span>
                        <strong class="ms-1 {{ $ticket->sla_deadline && now()->greaterThan($ticket->sla_deadline) && !in_array($ticket->status, ['RESOLVED','CLOSED']) ? 'text-danger fw-bold' : 'text-dark' }}">
                            {{ $ticket->sla_deadline ? $ticket->sla_deadline->format('d/m/Y H:i') : '—' }}
                        </strong>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3" style="font-size:0.875rem; white-space:pre-wrap; line-height:1.6;">
                    {{ $ticket->description }}
                </div>

                {{-- Ảnh minh chứng gốc --}}
                @if ($ticket->attachments->isNotEmpty())
                    <div>
                        <div class="fw-bold mb-2 text-secondary" style="font-size:0.78rem;">Ảnh / Tệp minh chứng gốc từ sinh viên:</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($ticket->attachments as $att)
                                @php
                                    $isImg = str_contains(strtolower($att->file_type ?? ''), 'image')
                                          || str_contains(strtolower($att->file_path), '.jpg')
                                          || str_contains(strtolower($att->file_path), '.png')
                                          || str_contains(strtolower($att->file_path), '.jpeg')
                                          || str_contains(strtolower($att->file_path), 'cloudinary');
                                @endphp

                                @if ($isImg)
                                    <div style="width:85px; height:85px; border-radius:12px; overflow:hidden; border:1.5px solid #cbd5e1; cursor:pointer; flex-shrink:0; background:#f1f5f9; box-shadow:0 2px 6px rgba(0,0,0,0.06);"
                                         onclick="openStaffLightbox(this.querySelector('img').src)">
                                        <img src="{{ $att->url }}" alt="Ảnh minh chứng" style="width:100%; height:100%; object-fit:cover;" loading="lazy">
                                    </div>
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

        {{-- Log Nhật ký chuyển trạng thái --}}
        <div class="card mb-4">
            <div class="card-header bg-white font-bold py-3">
                <i class="bi bi-clock-history me-2 text-success"></i>Nhật ký Trạng thái Xử lý
            </div>
            <div class="card-body">
                <div class="timeline" style="border-left: 2px solid #e2e8f0; margin-left: 10px; padding-left: 18px;">
                    @foreach ($ticket->statusLogs as $log)
                        <div class="mb-3 position-relative">
                            <div class="position-absolute" style="left:-25px; top:2px; width:12px; height:12px; border-radius:50%; background:#10b981;"></div>
                            <div style="font-size:0.85rem; color:#1e293b;">
                                Thay đổi trạng thái:
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
                                {{ $log->changedBy->name }} · {{ $log->created_at->format('H:i d/m/Y') }}
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- Cột Phải: Khung Chat 2 chiều với Sinh viên / Giảng viên --}}
    <div class="col-lg-5">
        <div class="card sticky-top" style="top:80px;">
            <div class="card-header bg-white font-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-chat-dots me-2 text-success"></i>Trao đổi với Người báo lỗi</span>
                <span class="badge bg-light text-dark border" style="font-size:0.75rem;">{{ $ticket->comments->count() }} tin nhắn</span>
            </div>

            {{-- Message Stream --}}
            <div id="chatStream" style="max-height:380px; overflow-y:auto; padding:1rem; display:flex; flex-direction:column; gap:10px; background:#f8fafc;">
                @forelse ($ticket->comments as $c)
                    @php $isMe = $c->user_id === Auth::id(); @endphp
                    <div class="d-flex gap-2 {{ $isMe ? 'flex-row-reverse' : '' }}" style="align-items:flex-end;">
                        <div style="width:30px; height:30px; border-radius:8px; background:{{ $isMe ? '#10b981' : '#0d6efd' }}; color:white; display:flex; align-items:center; justify-content:center; font-size:0.78rem; font-weight:700; flex-shrink:0;">
                            {{ strtoupper(substr($c->user->name, 0, 1)) }}
                        </div>
                        <div style="max-width:80%;">
                            <div style="background:{{ $isMe ? '#10b981' : 'white' }}; color:{{ $isMe ? 'white' : '#1e293b' }}; border-radius:{{ $isMe ? '12px 12px 4px 12px' : '12px 12px 12px 4px' }}; padding:0.55rem 0.85rem; font-size:0.84rem; line-height:1.5; box-shadow:0 1px 4px rgba(0,0,0,0.06); white-space:pre-wrap;">{{ $c->content }}</div>

                            {{-- Attachments trong chat --}}
                            @if ($c->attachments->isNotEmpty())
                                <div class="d-flex flex-wrap gap-1 mt-1.5 {{ $isMe ? 'justify-content-end' : '' }}">
                                    @foreach ($c->attachments as $att)
                                        @php
                                            $isImg = str_contains(strtolower($att->file_type ?? ''), 'image')
                                                  || str_contains(strtolower($att->file_path), '.jpg')
                                                  || str_contains(strtolower($att->file_path), '.png')
                                                  || str_contains(strtolower($att->file_path), '.jpeg')
                                                  || str_contains(strtolower($att->file_path), 'cloudinary');
                                        @endphp

                                        @if ($isImg)
                                            <div style="width:85px; height:85px; border-radius:12px; overflow:hidden; border:1.5px solid #cbd5e1; cursor:pointer; flex-shrink:0; background:#f1f5f9; box-shadow:0 2px 6px rgba(0,0,0,0.06); transition:transform 0.15s;"
                                                 class="chat-img-thumb"
                                                 onclick="openStaffLightbox(this.querySelector('img').src)">
                                                <img src="{{ $att->url }}" alt="Ảnh đính kèm" style="width:100%; height:100%; object-fit:cover;" loading="lazy">
                                            </div>
                                        @else
                                            <a href="{{ $att->url }}" target="_blank"
                                               class="btn btn-sm btn-light border py-1 px-2 text-truncate rounded-2" style="font-size:0.75rem; max-width:160px;">
                                                <i class="bi bi-paperclip me-1"></i>{{ basename($att->file_path) }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <div style="font-size:0.67rem; color:#94a3b8; margin-top:2px; {{ $isMe ? 'text-align:right;' : '' }}">
                                {{ $c->user->name }} · {{ $c->created_at->format('H:i d/m') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-chat-square-text fs-2 d-block mb-1"></i>
                        <p style="font-size:0.8rem; margin:0;">Chưa có tin nhắn trao đổi.<br>Nhập tin nhắn bên dưới để hướng dẫn người báo lỗi.</p>
                    </div>
                @endforelse
            </div>

            {{-- Input Chat --}}
            <div class="p-2 border-top bg-white">
                <form method="POST" action="{{ route('staff.tickets.comments.store', $ticket->id) }}" enctype="multipart/form-data" id="staffChatForm">
                    @csrf
                    <div class="d-flex flex-column gap-2">
                        <textarea name="content" id="staffChatContent" rows="2" class="form-control rounded-3" placeholder="Nhập tin nhắn hướng dẫn/trao đổi..." style="font-size:0.85rem; resize:none;" required></textarea>

                        {{-- Preview tệp đính kèm đã chọn --}}
                        <div id="staffChatPreview" class="d-flex flex-wrap gap-1 my-1"></div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex gap-1">
                                {{-- Nút Chụp ảnh thông minh --}}
                                <button type="button" onclick="startStaffWebcam()" class="btn btn-sm btn-outline-primary rounded-2 py-1 px-2 fw-bold" style="font-size:0.78rem;">
                                    <i class="bi bi-camera-fill me-1"></i> Chụp ảnh
                                </button>
                                <input type="file" id="staffCameraInput" name="attachments[]" accept="image/*" capture="environment" class="d-none" onchange="handleStaffFileSelect(this)">

                                {{-- Nút Đính kèm tệp từ máy --}}
                                <label for="staffFileInput" class="btn btn-sm btn-outline-secondary rounded-2 py-1 px-2 mb-0" style="font-size:0.78rem; cursor:pointer;">
                                    <i class="bi bi-paperclip me-1"></i> Đính kèm
                                    <input type="file" id="staffFileInput" name="attachments[]" accept=".jpg,.jpeg,.png,.pdf" multiple class="d-none" onchange="handleStaffFileSelect(this)">
                                </label>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary fw-bold rounded-2 px-3" style="font-size:0.83rem;">
                                <i class="bi bi-send-fill me-1"></i> Gửi tin nhắn
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- Live Webcam Modal cho KTV --}}
<div class="modal fade" id="staffWebcamModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title h6 fw-bold mb-0">
                    <i class="bi bi-camera-video-fill me-2 text-primary"></i>Chụp ảnh từ Webcam Máy tính
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopStaffWebcam()"></button>
            </div>
            <div class="modal-body p-3 bg-black text-center position-relative overflow-hidden" style="border-radius:0 0 16px 16px;">
                <video id="staffWebcamVideo" autoplay playsinline style="width:100%; max-height:360px; border-radius:12px; object-fit:cover;"></video>
                <canvas id="staffWebcamCanvas" class="d-none"></canvas>
                <div class="mt-3 d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" onclick="takeStaffWebcamSnapshot()">
                        <i class="bi bi-camera-fill me-2"></i> Chụp ngay
                    </button>
                    <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2" data-bs-dismiss="modal" onclick="stopStaffWebcam()">
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Lightbox Zoom Modal cho KTV --}}
<div class="modal fade" id="staffLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent">
            <div class="modal-body p-0 text-center position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                <img id="staffLightboxImg" src="" alt="Ảnh phóng to" class="img-fluid rounded-3 shadow-lg" style="max-height:85vh; object-fit:contain;">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Auto scroll bottom chat
    const cs = document.getElementById('chatStream');
    if (cs) cs.scrollTop = cs.scrollHeight;

    function openStaffLightbox(src) {
        if (!src) return;
        const img = document.getElementById('staffLightboxImg');
        if (img) img.src = src;
        const modalEl = document.getElementById('staffLightboxModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            window.open(src, '_blank');
        }
    }

    // File Preview Logic
    let staffSelectedFiles = [];

    function handleStaffFileSelect(input) {
        if (!input.files || input.files.length === 0) return;
        Array.from(input.files).forEach(f => {
            if (staffSelectedFiles.length < 5) staffSelectedFiles.push(f);
        });
        renderStaffPreviews();
        syncStaffFiles();
    }

    function removeStaffFile(index) {
        staffSelectedFiles.splice(index, 1);
        renderStaffPreviews();
        syncStaffFiles();
    }

    function renderStaffPreviews() {
        const preview = document.getElementById('staffChatPreview');
        preview.innerHTML = '';
        staffSelectedFiles.forEach((file, i) => {
            const badge = document.createElement('span');
            badge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle rounded-2 d-inline-flex align-items-center gap-1 p-1 px-2';
            badge.style.fontSize = '0.75rem';
            badge.innerHTML = `<i class="bi bi-paperclip"></i> ${file.name.substring(0, 15)}... <i class="bi bi-x-circle-fill ms-1 text-danger cursor-pointer" onclick="removeStaffFile(${i})"></i>`;
            preview.appendChild(badge);
        });
    }

    function syncStaffFiles() {
        const dt = new DataTransfer();
        staffSelectedFiles.forEach(f => dt.items.add(f));
        document.getElementById('staffFileInput').files = dt.files;
    }

    // Live Webcam logic
    let staffWebcamStream = null;

    function startStaffWebcam() {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
            document.getElementById('staffCameraInput').click();
            return;
        }

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    staffWebcamStream = stream;
                    const video = document.getElementById('staffWebcamVideo');
                    video.srcObject = stream;
                    const modal = new bootstrap.Modal(document.getElementById('staffWebcamModal'));
                    modal.show();
                })
                .catch(err => {
                    document.getElementById('staffCameraInput').click();
                });
        } else {
            document.getElementById('staffCameraInput').click();
        }
    }

    function stopStaffWebcam() {
        if (staffWebcamStream) {
            staffWebcamStream.getTracks().forEach(t => t.stop());
            staffWebcamStream = null;
        }
    }

    function takeStaffWebcamSnapshot() {
        const video = document.getElementById('staffWebcamVideo');
        const canvas = document.getElementById('staffWebcamCanvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(blob => {
            if (blob) {
                const capturedFile = new File([blob], `staff_snapshot_${Date.now()}.jpg`, { type: 'image/jpeg' });
                staffSelectedFiles.push(capturedFile);
                renderStaffPreviews();
                syncStaffFiles();
                stopStaffWebcam();
                bootstrap.Modal.getInstance(document.getElementById('staffWebcamModal')).hide();
            }
        }, 'image/jpeg', 0.92);
    }
</script>
@endpush
