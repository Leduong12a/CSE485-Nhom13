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
                                @if (str_starts_with($att->file_type ?? '', 'image/'))
                                    <img src="{{ asset('storage/' . $att->file_path) }}"
                                         alt="Ảnh minh chứng"
                                         style="width:80px; height:80px; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0; cursor:pointer;"
                                         onclick="window.open(this.src)">
                                @else
                                    <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
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
                                <span class="badge bg-light text-dark border me-1">{{ $log->old_status ?: 'MỚI' }}</span>
                                <i class="bi bi-arrow-right text-muted"></i>
                                <span class="badge badge-status badge-{{ $log->new_status }} ms-1">{{ $log->new_status }}</span>
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
                                <div class="d-flex flex-wrap gap-1 mt-1 {{ $isMe ? 'justify-content-end' : '' }}">
                                    @foreach ($c->attachments as $att)
                                        <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
                                           class="btn btn-sm btn-light border py-0 px-2 text-truncate" style="font-size:0.7rem; max-width:140px;">
                                            <i class="bi bi-paperclip me-1"></i>{{ basename($att->file_path) }}
                                        </a>
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
                <form method="POST" action="{{ route('staff.tickets.comments.store', $ticket->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex flex-column gap-2">
                        <textarea name="content" rows="2" class="form-control rounded-3" placeholder="Nhập tin nhắn hướng dẫn/trao đổi..." style="font-size:0.85rem; resize:none;" required></textarea>

                        <div class="d-flex justify-content-between align-items-center">
                            <label class="btn btn-sm btn-outline-secondary rounded-2 py-1 px-2" style="font-size:0.78rem; cursor:pointer;">
                                <i class="bi bi-paperclip"></i> Đính kèm
                                <input type="file" name="attachments[]" multiple class="d-none">
                            </label>
                            <button type="submit" class="btn btn-sm btn-success fw-bold rounded-2 px-3" style="font-size:0.83rem;">
                                <i class="bi bi-send-fill me-1"></i> Gửi tin nhắn
                            </button>
                        </div>
                    </div>
                </form>
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
</script>
@endpush
