@extends('layouts.app')

@section('title', 'Chi tiết Ticket #' . $ticket->id)

@section('content')
<div class="mb-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-arrow-left"></i></a>
        <h4 class="fw-bold mb-0">Ticket #TK-{{ $ticket->id }}: {{ $ticket->title }}</h4>
    </div>
    <div>
        @if($ticket->status === 'OPEN')
            <span class="badge bg-secondary fs-6">Mới gửi</span>
        @elseif($ticket->status === 'IN_PROGRESS')
            <span class="badge bg-warning text-dark fs-6"><i class="bi bi-arrow-repeat me-1"></i> Đang xử lý</span>
        @elseif($ticket->status === 'RESOLVED')
            <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i> Đã xong</span>
        @elseif($ticket->status === 'CLOSED')
            <span class="badge bg-dark fs-6">Đóng phiếu</span>
        @elseif($ticket->status === 'REOPENED')
            <span class="badge bg-danger fs-6"><i class="bi bi-exclamation-diamond me-1"></i> Mở lại sự cố</span>
        @endif
    </div>
</div>

<!-- Status Timeline Stepper -->
<div class="card card-custom p-4 bg-white mb-4">
    <h6 class="fw-bold text-muted mb-3 text-uppercase extra-small">Tiến độ xử lý sự cố</h6>
    <div class="d-flex justify-content-between position-relative">
        <div class="text-center position-relative z-1">
            <div class="btn btn-circle btn-primary rounded-circle mb-2" style="width: 40px; height: 40px; line-height: 26px;">1</div>
            <div class="small fw-semibold">Mới gửi</div>
            <small class="text-muted extra-small">{{ \Carbon\Carbon::parse($ticket->created_at)->format('H:i d/m') }}</small>
        </div>
        <div class="text-center position-relative z-1">
            <div class="btn btn-circle {{ in_array($ticket->status, ['IN_PROGRESS', 'RESOLVED', 'CLOSED']) ? 'btn-warning text-dark' : 'btn-light border' }} rounded-circle mb-2" style="width: 40px; height: 40px; line-height: 26px;">2</div>
            <div class="small fw-semibold">Đang xử lý</div>
            <small class="text-muted extra-small">{{ $ticket->assignee_name ? 'KTV: ' . $ticket->assignee_name : 'Chờ giao KTV' }}</small>
        </div>
        <div class="text-center position-relative z-1">
            <div class="btn btn-circle {{ in_array($ticket->status, ['RESOLVED', 'CLOSED']) ? 'btn-success' : 'btn-light border' }} rounded-circle mb-2" style="width: 40px; height: 40px; line-height: 26px;">3</div>
            <div class="small fw-semibold">Đã khắc phục</div>
            <small class="text-muted extra-small">{{ $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at)->format('H:i d/m') : '--:--' }}</small>
        </div>
        <div class="text-center position-relative z-1">
            <div class="btn btn-circle {{ $ticket->status === 'CLOSED' ? 'btn-dark' : 'btn-light border' }} rounded-circle mb-2" style="width: 40px; height: 40px; line-height: 26px;">4</div>
            <div class="small fw-semibold">Đóng phiếu</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Nội dung Ticket & Chat (Left 8 Cols) -->
    <div class="col-lg-8">
        <!-- Main Description -->
        <div class="card card-custom p-4 bg-white mb-4">
            <h5 class="fw-bold border-bottom pb-2 mb-3">Mô tả sự cố chi tiết</h5>
            <p class="text-secondary" style="white-space: pre-line;">{{ $ticket->description }}</p>

            <div class="row g-3 mt-2 pt-3 border-top bg-light rounded p-2">
                <div class="col-sm-6">
                    <small class="text-muted d-block">Danh mục loại lỗi:</small>
                    <span class="fw-semibold text-primary"><i class="bi bi-tag me-1"></i> {{ $ticket->category_name }}</span>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Vị trí xảy ra sự cố:</small>
                    <span class="fw-semibold text-dark"><i class="bi bi-geo-alt me-1"></i> {{ $ticket->location ?? 'Không ghi nhận' }}</span>
                </div>
            </div>
        </div>

        <!-- Section Survey / Reopen if RESOLVED -->
        @if($ticket->status === 'RESOLVED')
            <div class="card card-custom p-4 bg-success-subtle border border-success mb-4">
                <h5 class="fw-bold text-success mb-2"><i class="bi bi-star-fill me-2 text-warning"></i> Khảo sát Đánh giá Chất lượng Dịch vụ</h5>
                <p class="small text-muted mb-3">Sự cố của bạn đã được Kỹ thuật viên khắc phục xong. Vui lòng chấm điểm và để lại nhận xét đóng góp!</p>

                @if($survey)
                    <div class="alert alert-white bg-white border p-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-bold">Đánh giá của bạn:</span>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= $survey->rating_stars ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                @endfor
                            </div>
                            <span class="badge bg-primary ms-2">{{ $survey->rating_stars }} / 5 sao</span>
                        </div>
                        @if($survey->comment)
                            <div class="small text-muted italic">"{{ $survey->comment }}"</div>
                        @endif
                    </div>
                @else
                    <form class="bg-white p-3 rounded border">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Chấm điểm sao (1 - 5 sao):</label>
                            <div class="d-flex gap-3">
                                @for($star = 5; $star >= 1; $star--)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating_stars" id="star{{ $star }}" value="{{ $star }}" {{ $star == 5 ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-warning" for="star{{ $star }}">{{ $star }} ⭐</label>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm" placeholder="Viết nhận xét đóng góp ý kiến...">
                        </div>
                        <button type="button" class="btn btn-success btn-sm rounded-pill px-4">Gửi đánh giá</button>
                    </form>
                @endif
            </div>
        @endif

        <!-- Trao đổi hai chiều Stream Chat -->
        <div class="card card-custom bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-slate-800"><i class="bi bi-chat-dots me-2 text-primary"></i> Trao đổi tin nhắn với Kỹ thuật viên</h5>
            </div>
            <div class="card-body p-4" style="max-height: 450px; overflow-y: auto;">
                @if($comments->isEmpty())
                    <p class="text-muted text-center py-4 small">Chưa có trao đổi nào. Hãy viết bình luận bên dưới nếu cần nhắn thêm cho KTV!</p>
                @else
                    @foreach($comments as $comment)
                        <div class="mb-3 d-flex {{ $comment->user_role === 'REQUESTER' ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="p-3 rounded-3 {{ $comment->user_role === 'REQUESTER' ? 'bg-primary text-white' : 'bg-light border text-dark' }}" style="max-width: 80%;">
                                <div class="d-flex justify-content-between align-items-center gap-3 mb-1 extra-small opacity-75">
                                    <span class="fw-bold">{{ $comment->user_name }} ({{ $comment->user_role }})</span>
                                    <span>{{ \Carbon\Carbon::parse($comment->created_at)->format('H:i d/m') }}</span>
                                </div>
                                <div class="small">{{ $comment->content }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="card-footer bg-light p-3">
                <form class="d-flex gap-2">
                    <input type="text" class="form-control" placeholder="Nhập câu hỏi hoặc nội dung bổ sung tin nhắn...">
                    <button type="button" class="btn btn-primary rounded-pill px-4"><i class="bi bi-send-fill"></i></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Thông tin bổ trợ (Right 4 Cols) -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 bg-white mb-4">
            <h6 class="fw-bold text-muted mb-3 text-uppercase extra-small border-bottom pb-2">Thông tin ticket</h6>
            
            <div class="mb-3">
                <small class="text-muted d-block">Người gửi phản ánh:</small>
                <div class="fw-semibold"><i class="bi bi-person me-1 text-primary"></i> {{ $ticket->requester_name }}</div>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Kỹ thuật viên phụ trách:</small>
                @if($ticket->assignee_name)
                    <div class="fw-semibold text-success"><i class="bi bi-tools me-1"></i> {{ $ticket->assignee_name }}</div>
                @else
                    <div class="text-muted small italic">Chưa được phân công KTV</div>
                @endif
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Thời hạn cam kết SLA:</small>
                <div class="fw-semibold text-danger">
                    <i class="bi bi-clock-history me-1"></i> 
                    {{ $ticket->sla_deadline ? \Carbon\Carbon::parse($ticket->sla_deadline)->format('H:i - d/m/Y') : 'Tính theo giờ tạo + SLA' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
