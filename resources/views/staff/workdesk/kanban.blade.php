@extends('staff.layouts.app')

@section('title', 'Workdesk Dạng Kanban Board')

@push('styles')
<style>
    .kanban-board {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.35rem;
        align-items: start;
    }

    @media (max-width: 992px) {
        .kanban-board { grid-template-columns: 1fr; }
    }

    .kanban-column {
        background: #f8fafc;
        border-radius: 18px;
        padding: 1.15rem;
        min-height: 520px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 8px rgba(0,0,0,0.03);
    }

    .kanban-column-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.15rem;
        padding-bottom: 0.65rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .kanban-column-header h3 {
        font-size: 0.98rem;
        font-weight: 800;
        margin: 0;
        color: #0f172a;
    }

    .kanban-card {
        background: white;
        border-radius: 16px;
        padding: 1.15rem;
        margin-bottom: 0.95rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s;
        border-left: 4px solid #cbd5e1;
        border-right: 1px solid #f1f5f9;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
    }

    .kanban-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .kanban-card.status-OPEN     { border-left-color: #0ea5e9; }
    .kanban-card.status-IN_PROGRESS { border-left-color: #f59e0b; }
    .kanban-card.status-RESOLVED { border-left-color: #10b981; }
    .kanban-card.status-REOPENED { border-left-color: #f43f5e; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Workdesk Dạng Thẻ Kanban</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Quản lý và chuyển đổi nhanh tiến độ xử lý sự cố qua 3 cột Kanban</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.workdesk.index') }}" class="btn btn-outline-primary rounded-pill px-3 fw-bold" style="font-size:0.85rem;">
            <i class="bi bi-table me-1"></i> Chuyển sang Dạng Bảng
        </a>
    </div>
</div>

<div class="kanban-board">

    {{-- ── CỘT 1: CẦN XỬ LÝ (OPEN / REOPENED) ── --}}
    <div class="kanban-column">
        <div class="kanban-column-header">
            <h3><i class="bi bi-record-circle-fill text-info me-2"></i>Cần xử lý / Tiếp nhận</h3>
            <span class="badge bg-primary rounded-pill px-3 py-1 fw-bold" style="font-size:0.8rem;">{{ $todoTickets->count() }}</span>
        </div>

        @forelse ($todoTickets as $t)
            <div class="kanban-card status-{{ $t->status }}" onclick="if (!event.target.closest('button, form, a')) window.location='{{ route('staff.tickets.show', $t->id) }}'">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-monospace text-muted fw-bold" style="font-size:0.78rem;">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge badge-status badge-priority-{{ $t->priority }}">
                        @switch($t->priority)
                            @case('HIGH')   <i class="bi bi-exclamation-circle-fill me-1"></i>Cao @break
                            @case('MEDIUM') <i class="bi bi-dash-circle-fill me-1"></i>Vừa @break
                            @case('LOW')    <i class="bi bi-arrow-down-circle-fill me-1"></i>Thấp @break
                        @endswitch
                    </span>
                </div>

                <h4 class="h6 fw-bold mb-1" style="font-size:0.92rem; line-height:1.4;">
                    <a href="{{ route('staff.tickets.show', $t->id) }}" class="text-dark text-decoration-none">{{ $t->title }}</a>
                </h4>

                <div class="text-muted mb-2.5" style="font-size:0.78rem;">
                    <i class="bi bi-person me-1"></i>{{ $t->requester->name }} · <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $t->location ?: '—' }}
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2.5 border-top">
                    <span class="badge bg-light text-dark border" style="font-size:0.72rem;">{{ $t->category?->name ?? '—' }}</span>

                    @if ($t->current_assignee_id === Auth::id())
                        <form method="POST" action="{{ route('staff.tickets.status', $t->id) }}">
                            @csrf
                            <input type="hidden" name="status" value="IN_PROGRESS">
                            <button type="submit" class="btn btn-sm btn-warning rounded-2 py-1 px-2.5 fw-bold" style="font-size:0.78rem;">
                                <i class="bi bi-play-fill me-1"></i>Bắt đầu <i class="bi bi-arrow-right"></i>
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('staff.tickets.claim', $t->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary rounded-2 py-1 px-2.5 fw-bold" style="font-size:0.78rem;">
                                <i class="bi bi-hand-index-thumb me-1"></i>Tự nhận
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted style-08">
                <i class="bi bi-check-circle display-5 text-success d-block mb-2"></i>
                <h6 class="fw-bold">Không có ticket cần xử lý</h6>
            </div>
        @endforelse
    </div>

    {{-- ── CỘT 2: ĐANG XỬ LÝ (IN_PROGRESS) ── --}}
    <div class="kanban-column" style="background:#fffbeb; border-color:#fef3c7;">
        <div class="kanban-column-header" style="border-bottom-color:#fde68a;">
            <h3 class="text-warning-emphasis"><i class="bi bi-clock-history text-warning me-2"></i>Đang khắc phục</h3>
            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold" style="font-size:0.8rem;">{{ $inProgressTickets->count() }}</span>
        </div>

        @forelse ($inProgressTickets as $t)
            <div class="kanban-card status-IN_PROGRESS" style="border-left-color:#f59e0b;" onclick="if (!event.target.closest('button, form, a')) window.location='{{ route('staff.tickets.show', $t->id) }}'">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-monospace text-muted fw-bold" style="font-size:0.78rem;">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</span>
                    @if ($t->sla_deadline && now()->greaterThan($t->sla_deadline))
                        <span class="badge bg-danger text-white" style="font-size:0.68rem;"><i class="bi bi-alarm-fill me-1"></i>Trễ SLA</span>
                    @endif
                </div>

                <h4 class="h6 fw-bold mb-1" style="font-size:0.92rem; line-height:1.4;">
                    <a href="{{ route('staff.tickets.show', $t->id) }}" class="text-dark text-decoration-none">{{ $t->title }}</a>
                </h4>

                <div class="text-muted mb-2.5" style="font-size:0.78rem;">
                    <i class="bi bi-person me-1"></i>{{ $t->requester->name }} · <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $t->location ?: '—' }}
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2.5 border-top">
                    <span class="badge bg-light text-dark border" style="font-size:0.72rem;">{{ $t->category?->name ?? '—' }}</span>

                    <form method="POST" action="{{ route('staff.tickets.status', $t->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="RESOLVED">
                        <button type="submit" class="btn btn-sm btn-success rounded-2 py-1 px-2.5 fw-bold" style="font-size:0.78rem;">
                            <i class="bi bi-check-circle-fill me-1"></i>Khắc phục xong <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted style-08">
                <i class="bi bi-check-circle display-5 text-success d-block mb-2"></i>
                <h6 class="fw-bold">Không có ticket đang xử lý</h6>
            </div>
        @endforelse
    </div>

    {{-- ── CỘT 3: ĐÃ KHẮC PHỤC (RESOLVED / CLOSED) ── --}}
    <div class="kanban-column" style="background:#f0fdf4; border-color:#dcfce7;">
        <div class="kanban-column-header" style="border-bottom-color:#bbf7d0;">
            <h3 class="text-success-emphasis"><i class="bi bi-check-circle-fill text-success me-2"></i>Đã khắc phục / Đóng</h3>
            <span class="badge bg-success rounded-pill px-3 py-1 fw-bold" style="font-size:0.8rem;">{{ $doneTickets->count() }}</span>
        </div>

        @forelse ($doneTickets as $t)
            <div class="kanban-card status-RESOLVED" style="border-left-color:#10b981;" onclick="if (!event.target.closest('button, form, a')) window.location='{{ route('staff.tickets.show', $t->id) }}'">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-monospace text-muted fw-bold" style="font-size:0.78rem;">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge badge-status badge-{{ $t->status }}">
                        @switch($t->status)
                            @case('RESOLVED') 🟢 Đã khắc phục @break
                            @case('CLOSED')   ⚫ Đã đóng @break
                            @default          {{ $t->status }}
                        @endswitch
                    </span>
                </div>

                <h4 class="h6 fw-bold mb-1" style="font-size:0.92rem; line-height:1.4;">
                    <a href="{{ route('staff.tickets.show', $t->id) }}" class="text-dark text-decoration-none">{{ $t->title }}</a>
                </h4>

                <div class="text-muted mb-2.5" style="font-size:0.78rem;">
                    <i class="bi bi-person me-1"></i>{{ $t->requester->name }} · {{ $t->resolved_at?->format('H:i d/m') }}
                </div>

                <div class="pt-2.5 border-top text-end">
                    <a href="{{ route('staff.tickets.show', $t->id) }}" class="btn btn-sm btn-outline-secondary rounded-2 py-1 px-2.5" style="font-size:0.78rem;">
                        <i class="bi bi-eye me-1"></i> Xem chi tiết
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted style-08">
                <i class="bi bi-clipboard-check display-5 text-success d-block mb-2"></i>
                <h6 class="fw-bold">Chưa có ticket nào hoàn thành</h6>
            </div>
        @endforelse
    </div>

</div>

@endsection
