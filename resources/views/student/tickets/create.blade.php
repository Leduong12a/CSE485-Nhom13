@extends('student.layouts.app')

@section('title', 'Báo sự cố mới')
@section('meta_description', 'Tạo phiếu yêu cầu hỗ trợ kỹ thuật mới')

@push('styles')
<style>
    .create-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 992px) {
        .create-grid { grid-template-columns: 1fr; }
    }

    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .form-card-header {
        background: linear-gradient(90deg, var(--tlu-dark), var(--tlu-primary));
        padding: 1.2rem 1.5rem;
        color: white;
    }

    .form-card-header h2 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    .form-card-header p {
        font-size: 0.8rem;
        opacity: 0.85;
        margin: 0.2rem 0 0;
    }

    .form-card-body { padding: 1.5rem; }

    .form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.4rem;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        font-size: 0.875rem;
        padding: 0.6rem 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--tlu-primary);
        box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: var(--tlu-danger);
    }

    /* Priority Radio Pills */
    .priority-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .priority-radio input[type="radio"] { display: none; }

    .priority-label {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 0.45rem 1rem;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.82rem;
        font-weight: 500;
        color: #374151;
        transition: all 0.15s;
        user-select: none;
    }

    .priority-label:hover { border-color: #94a3b8; background: #f8fafc; }

    .priority-radio input:checked + .priority-label.low    { border-color: #22c55e; background: #f0fdf4; color: #166534; }
    .priority-radio input:checked + .priority-label.medium { border-color: #f59e0b; background: #fffbeb; color: #92400e; }
    .priority-radio input:checked + .priority-label.high   { border-color: #ef4444; background: #fff1f2; color: #be123c; }

    /* Drop Zone Upload */
    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        background: #fafafa;
        position: relative;
    }

    .drop-zone.dragging {
        border-color: var(--tlu-primary);
        background: #eff6ff;
    }

    .drop-zone:hover {
        border-color: var(--tlu-primary);
        background: #f0f7ff;
    }

    .drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .drop-zone i { font-size: 2rem; color: #94a3b8; }
    .drop-zone p { margin: 0.5rem 0 0; font-size: 0.83rem; color: #64748b; }
    .drop-zone small { font-size: 0.72rem; color: #94a3b8; }

    /* Attachment Preview */
    .attachments-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 0.75rem;
    }

    .attachment-item {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 10px;
        overflow: hidden;
        border: 1.5px solid #e5e7eb;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .attachment-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .attachment-item .file-icon {
        font-size: 1.6rem;
        color: #64748b;
    }

    .attachment-item .file-name {
        font-size: 0.6rem;
        color: #64748b;
        text-align: center;
        padding: 0 4px;
        overflow: hidden;
        max-width: 100%;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .attachment-item .btn-remove {
        position: absolute;
        top: 3px;
        right: 3px;
        width: 18px;
        height: 18px;
        background: rgba(239,68,68,0.9);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 2;
    }

    /* Sidebar Info Card */
    .info-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        position: sticky;
        top: 80px;
    }

    .info-card-header {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        padding: 1rem 1.2rem;
        border-bottom: 1px solid #bae6fd;
    }

    .info-card-header h3 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0369a1;
        margin: 0;
    }

    .info-card-body { padding: 1.2rem; }

    .process-step {
        display: flex;
        gap: 10px;
        margin-bottom: 1rem;
        align-items: flex-start;
    }

    .step-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    .process-step p {
        font-size: 0.8rem;
        color: #374151;
        margin: 0;
        line-height: 1.5;
    }

    .process-step strong {
        color: #1e293b;
        display: block;
        font-size: 0.82rem;
        margin-bottom: 1px;
    }

    .sla-badge {
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        color: #92400e;
    }

    .btn-submit {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.7rem 1.5rem;
        font-size: 0.95rem;
        background: linear-gradient(90deg, var(--tlu-dark), var(--tlu-primary));
        border: none;
        box-shadow: 0 4px 14px rgba(13,110,253,0.3);
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(13,110,253,0.4);
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <h1><i class="bi bi-plus-circle me-2 text-primary" style="font-size:1.3rem;"></i>Báo sự cố mới</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('student.tickets.index') }}" class="text-decoration-none">Sự cố của tôi</a></li>
            <li class="breadcrumb-item active">Tạo mới</li>
        </ol>
    </nav>
</div>

<form method="POST" action="{{ route('student.tickets.store') }}" enctype="multipart/form-data" id="createForm">
    @csrf

    <div class="create-grid">

        {{-- ── CỘT TRÁI: FORM ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <h2><i class="bi bi-pencil-square me-2"></i>Thông tin sự cố</h2>
                <p>Mô tả càng chi tiết, kỹ thuật viên sẽ hỗ trợ bạn càng nhanh chóng.</p>
            </div>
            <div class="form-card-body">

                {{-- Tiêu đề --}}
                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề mô tả sự cố <span class="text-danger">*</span></label>
                    <input type="text" id="title" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        placeholder="VD: Máy chiếu phòng 301 A2 không nhận tín hiệu HDMI"
                        value="{{ old('title') }}" required maxlength="255">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Danh mục + Vị trí --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label for="category_id" class="form-label">Danh mục sự cố <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>— Chọn loại sự cố —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} (SLA {{ $cat->sla_hours }}h)
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <label for="location" class="form-label">Vị trí xảy ra sự cố</label>
                        <input type="text" id="location" name="location"
                            class="form-control @error('location') is-invalid @enderror"
                            placeholder="VD: Phòng 302 - Nhà C5"
                            value="{{ old('location') }}" maxlength="150">
                    </div>
                </div>

                {{-- Mô tả chi tiết --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả chi tiết sự cố <span class="text-danger">*</span></label>
                    <textarea id="description" name="description" rows="5"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Mô tả hiện tượng lỗi, thời điểm xảy ra, các bước đã thử...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mức độ ưu tiên --}}
                <div class="mb-4">
                    <label class="form-label">Mức độ ưu tiên <span class="text-danger">*</span></label>
                    <div class="priority-group">
                        <label class="priority-radio">
                            <input type="radio" name="priority" value="LOW" {{ old('priority', 'MEDIUM') === 'LOW' ? 'checked' : '' }}>
                            <span class="priority-label low"><i class="bi bi-arrow-down-circle-fill me-1"></i> Thấp</span>
                        </label>
                        <label class="priority-radio">
                            <input type="radio" name="priority" value="MEDIUM" {{ old('priority', 'MEDIUM') === 'MEDIUM' ? 'checked' : '' }}>
                            <span class="priority-label medium"><i class="bi bi-dash-circle-fill me-1"></i> Trung bình</span>
                        </label>
                        <label class="priority-radio">
                            <input type="radio" name="priority" value="HIGH" {{ old('priority', 'MEDIUM') === 'HIGH' ? 'checked' : '' }}>
                            <span class="priority-label high"><i class="bi bi-exclamation-circle-fill me-1"></i> Khẩn cấp</span>
                        </label>
                    </div>
                    @error('priority')
                        <div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Upload ảnh đính kèm --}}
                <div class="mb-4">
                    <label class="form-label">Ảnh / Tệp minh chứng <span style="font-weight:400; color:#94a3b8;">(Tối đa 5 tệp, mỗi tệp &lt; 5MB)</span></label>

                    {{-- Nút Chụp ảnh thông minh (Camera điện thoại / Live Webcam PC) --}}
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <button type="button" onclick="startCameraOrWebcam()" class="btn btn-outline-primary w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="font-size:0.85rem; min-height:48px;">
                                <i class="bi bi-camera-fill fs-5"></i> <span>Chụp ảnh từ Camera</span>
                            </button>
                            <input type="file" id="cameraCapture" accept="image/*" capture="environment" class="d-none" onchange="handleFileSelect(this)">
                        </div>
                        <div class="col-6">
                            <label for="attachments" class="btn btn-outline-secondary w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="font-size:0.85rem; cursor:pointer; min-height:48px;">
                                <i class="bi bi-images fs-5"></i> <span>Chọn từ Thư viện</span>
                            </label>
                        </div>
                    </div>

                    {{-- Dropzone Kéo Thả dành cho Máy tính / Tablet --}}
                    <div class="drop-zone" id="dropZone">
                        <input type="file" id="attachments" name="attachments[]"
                               accept=".jpg,.jpeg,.png,.pdf"
                               multiple
                               onchange="handleFileSelect(this)">
                        <i class="bi bi-cloud-arrow-up mb-2"></i>
                        <p><strong>Kéo &amp; thả ảnh vào đây</strong> hoặc bấm để chọn tệp</p>
                        <small>.jpg, .jpeg, .png, .pdf — Tối đa 5 tệp, mỗi tệp &lt; 5MB</small>
                    </div>

                    @error('attachments')
                        <div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>
                    @enderror
                    @error('attachments.*')
                        <div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>
                    @enderror

                    <div class="attachments-preview" id="attachmentsPreview"></div>
                </div>

                {{-- Buttons --}}
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary btn-submit flex-grow-1" id="btnSubmit" style="min-height:48px;">
                        <span id="btnText"><i class="bi bi-send-fill me-2"></i>Gửi yêu cầu hỗ trợ</span>
                        <span id="btnSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Đang gửi...
                        </span>
                    </button>
                    <a href="{{ route('student.tickets.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center px-4" style="border-radius:10px; min-height:48px;">
                        Hủy
                    </a>
                </div>

            </div>
        </div>

        {{-- ── CỘT PHẢI: SIDEBAR HƯỚNG DẪN ── --}}
        <div class="info-card">
            <div class="info-card-header">
                <h3><i class="bi bi-info-circle-fill me-2"></i>Quy trình xử lý sự cố</h3>
            </div>
            <div class="info-card-body">

                <div class="process-step">
                    <div class="step-icon" style="background:#eff6ff;"><i class="bi bi-send-fill text-primary"></i></div>
                    <p><strong>Bước 1: Gửi phiếu sự cố</strong>Mô tả chi tiết và đính kèm ảnh minh chứng giúp đẩy nhanh tốc độ xử lý.</p>
                </div>

                <div class="process-step">
                    <div class="step-icon" style="background:#f0fdf4;"><i class="bi bi-person-check-fill text-success"></i></div>
                    <p><strong>Bước 2: Tiếp nhận & phân công</strong>Quản lý tiếp nhận và phân công cho Kỹ thuật viên phù hợp.</p>
                </div>

                <div class="process-step">
                    <div class="step-icon" style="background:#fffbeb;"><i class="bi bi-tools text-warning"></i></div>
                    <p><strong>Bước 3: Xử lý sự cố</strong>KTV liên hệ và tiến hành khắc phục. Bạn có thể theo dõi và trao đổi trực tiếp qua ticket.</p>
                </div>

                <div class="process-step mb-3">
                    <div class="step-icon" style="background:#fdf4ff;"><i class="bi bi-star-fill text-info"></i></div>
                    <p><strong>Bước 4: Đánh giá chất lượng</strong>Sau khi hoàn thành, hãy đánh giá 5 sao để cải thiện dịch vụ.</p>
                </div>

                <hr style="border-color:#f1f5f9;">

                <div class="sla-badge">
                    <i class="bi bi-clock-history"></i>
                    <span>Thời gian xử lý theo cam kết SLA của từng loại sự cố.</span>
                </div>
            </div>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
    // ── Drag & Drop ──────────────────────────────────────────
    const dropZone = document.getElementById('dropZone');
    let selectedFiles = [];

    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('dragging');
    });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragging'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragging');
        addFiles(Array.from(e.dataTransfer.files));
    });

    function handleFileSelect(input) {
        addFiles(Array.from(input.files));
        input.value = '';
    }

    function addFiles(files) {
        files.forEach(file => {
            if (selectedFiles.length >= 5) return;
            if (!['image/jpeg','image/jpg','image/png','application/pdf'].includes(file.type)) return;
            if (file.size > 5 * 1024 * 1024) return;
            selectedFiles.push(file);
        });
        renderPreviews();
        syncFilesToInput();
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        renderPreviews();
        syncFilesToInput();
    }

    function renderPreviews() {
        const preview = document.getElementById('attachmentsPreview');
        preview.innerHTML = '';
        selectedFiles.forEach((file, i) => {
            const div = document.createElement('div');
            div.className = 'attachment-item';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                div.appendChild(img);
            } else {
                const icon = document.createElement('i');
                icon.className = 'bi bi-file-earmark-pdf-fill file-icon text-danger';
                const name = document.createElement('span');
                name.className = 'file-name';
                name.textContent = file.name;
                div.appendChild(icon);
                div.appendChild(name);
            }

            const btnRemove = document.createElement('button');
            btnRemove.type = 'button';
            btnRemove.className = 'btn-remove';
            btnRemove.innerHTML = '✕';
            btnRemove.onclick = () => removeFile(i);
            div.appendChild(btnRemove);

            preview.appendChild(div);
        });
    }

    function syncFilesToInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        document.getElementById('attachments').files = dt.files;
    }

    // ── Loading on submit ────────────────────────────────────
    document.getElementById('createForm').addEventListener('submit', () => {
        document.getElementById('btnText').classList.add('d-none');
        document.getElementById('btnSpinner').classList.remove('d-none');
        document.getElementById('btnSubmit').disabled = true;
    });

    // ── Smart Camera & Live Webcam Modal ──────────────────────────────
    let webcamStream = null;

    function startCameraOrWebcam() {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
            document.getElementById('cameraCapture').click();
            return;
        }

        // Mở Live Webcam xem trước trên PC/Laptop
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    webcamStream = stream;
                    const video = document.getElementById('webcamVideo');
                    video.srcObject = stream;
                    const modal = new bootstrap.Modal(document.getElementById('webcamModal'));
                    modal.show();
                })
                .catch(err => {
                    console.warn("Webcam not allowed, fallback to camera file picker:", err);
                    document.getElementById('cameraCapture').click();
                });
        } else {
            document.getElementById('cameraCapture').click();
        }
    }

    function stopWebcam() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
    }

    function takeWebcamSnapshot() {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(blob => {
            if (blob) {
                const capturedFile = new File([blob], `webcam_photo_${Date.now()}.jpg`, { type: 'image/jpeg' });
                addFiles([capturedFile]);
                stopWebcam();
                bootstrap.Modal.getInstance(document.getElementById('webcamModal')).hide();
            }
        }, 'image/jpeg', 0.92);
    }
</script>

{{-- ── WEBCAM LIVE MODAL (CHO PC LAPTOP) ── --}}
<div class="modal fade" id="webcamModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title h6 fw-bold mb-0">
                    <i class="bi bi-camera-video-fill me-2 text-primary"></i>Chụp ảnh trực tiếp từ Webcam Máy tính
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopWebcam()"></button>
            </div>
            <div class="modal-body p-3 bg-black text-center position-relative overflow-hidden" style="border-radius:0 0 16px 16px;">
                <video id="webcamVideo" autoplay playsinline style="width:100%; max-height:360px; border-radius:12px; object-fit:cover;"></video>
                <canvas id="webcamCanvas" class="d-none"></canvas>
                <div class="mt-3 d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" onclick="takeWebcamSnapshot()">
                        <i class="bi bi-camera-fill me-2"></i> Chụp ảnh ngay
                    </button>
                    <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2" data-bs-dismiss="modal" onclick="stopWebcam()">
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush
