@extends('staff.layouts.app')

@section('title', 'Workdesk Dạng Kanban Board')

@push('styles')
<style>
    .kanban-board {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        align-items: start;
    }

    @media (max-width: 992px) {
        .kanban-board { grid-template-columns: 1fr; }
    }

    .kanban-column {
        background: #f1f5f9;
        border-radius: 16px;
        padding: 1rem;
        min-height: 500px;
    }

    .kanban-column-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .kanban-column-header h3 {
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }

    .kanban-card {
        background: white;
        border-radius: 14px;
        padding: 1rem;
        margin-bottom: 0.85rem;
        box-shadow: 0 1px 6px rgba(0,0,0,0.06);
        transition: transform 0.15s, box-shadow 0.15s;
        border-left: 4px solid #cbd5e1;
    }

    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.09);
    }

    .kanban-card.status-OPEN     { border-left-color: #0ea5e9; }
    .kanban-card.status-IN_PROGRESS { border-left-color: #f59e0b; }
    .kanban-card.status-RESOLVED { border-left-color: #22c55e; }
    .kanban-card.status-REOPENED { border-left-color: #ec4899; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-slate-800 mb-0">Workdesk Dạng Thẻ Kanban</h1>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Quản lý và chuyển đổi nhanh tiến độ xử lý sự cố qua 3 cột Kanban</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.workdesk.index') }}" class="btn btn-outline-secondary rounded-3" style="font-size:0.85rem;">
            <i class="bi bi-list-task me-1"></i> Chuyển sang Dạng Bảng
        </a>
    </div>
</div>

<div class="kanban-board">

    {{-- ── CỘT 1: CẦN XỬ LÝ (OPEN / REOPENED) ── --}}
    <div class="kanban-column">
        <div class="kanban-column-header">
            <h3><i class="bi bi-record-circle-fill text-info me-2"></i>Cần xử lý / Tiếp nhận</h3>
            <span class="badge bg-primary rounded-pill px-2.5 py-1">{{ $todoTickets->count() }}</span>
        </div>

        @forelse ($todoTickets as $t)
            <div class="kanban-card status-{{ $t->status }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-monospace text-muted fw-bold" style="font-size:0.75rem;">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge badge-status badge-priority-{{ $t->priority }}">
                        @switch($t->priority)
                            @case('HIGH')   <i class="bi bi-exclamation-circle-fill me-1"></i>Cao @break
                            @case('MEDIUM') <i class="bi bi-dash-circle-fill me-1"></i>Vừa @break
                            @case('LOW')    <i class="bi bi-arrow-down-circle-fill me-1"></i>Thấp @break
                        @endswitch
                    </span>
                </div>

                <h4 class="h6 fw-bold mb-1" style="font-size:0.9rem;">
                    <a href="{{ route('staff.tickets.show', $t->id) }}" class="text-dark text-decoration-none">{{ $t->title }}</a>
                </h4>

                <div class="text-muted mb-2" style="font-size:0.75rem;">
                    <i class="bi bi-person me-1"></i>{{ $t->requester->name }} · <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $t->location ?: '—' }}
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <span class="badge bg-light text-dark border" style="font-size:0.7rem;">{{ $t->category?->name ?? '—' }}</span>

                    @if ($t->current_assignee_id === Auth::id())
                        <form method="POST" action="{{ route('staff.tickets.status', $t->id) }}">
                            @csrf
                            <input type="hidden" name="status" value="IN_PROGRESS">
                            <button type="submit" class="btn btn-sm btn-warning rounded-2 py-0 px-2 fw-bold" style="font-size:0.75rem;">
                                <i class="bi bi-play-fill me-1"></i>Bắt đầu <i class="bi bi-arrow-right"></i>
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('staff.tickets.claim', $t->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary rounded-2 py-0 px-2 fw-bold" style="font-size:0.75rem;">
                                <i class="bi bi-hand-index-thumb me-1"></i>Tự nhận
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted style-08">
                <i class="bi bi-inbox d-block fs-3 mb-1"></i>Không có ticket cần xử lý.
            </div>
        @endforelse
    </div>

    {{-- ── CỘT 2: ĐANG XỬ LÝ (IN_PROGRESS) ── --}}
    <div class="kanban-column" style="background:#fefce8;">
        <div class="kanban-column-header" style="border-bottom-color:#fef08a;">
            <h3 class="text-warning-emphasis"><i class="bi bi-clock-history text-warning me-2"></i>Đang khắc phục</h3>
            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">{{ $inProgressTickets->count() }}</span>
        </div>

        @forelse ($inProgressTickets as $t)
            <div class="kanban-card status-IN_PROGRESS" style="border-left-color:#f59e0b;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-monospace text-muted fw-bold" style="font-size:0.75rem;">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</span>
                    @if ($t->sla_deadline && now()->greaterThan($t->sla_deadline))
                        <span class="badge bg-danger text-white" style="font-size:0.68rem;"><i class="bi bi-alarm-fill me-1"></i>Trễ SLA</span>
                    @endif
                </div>

                <h4 class="h6 fw-bold mb-1" style="font-size:0.9rem;">
                    <a href="{{ route('staff.tickets.show', $t->id) }}" class="text-dark text-decoration-none">{{ $t->title }}</a>
                </h4>

                <div class="text-muted mb-2" style="font-size:0.75rem;">
                    <i class="bi bi-person me-1"></i>{{ $t->requester->name }} · <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $t->location ?: '—' }}
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <span class="badge bg-light text-dark border" style="font-size:0.7rem;">{{ $t->category?->name ?? '—' }}</span>

                    <form method="POST" action="{{ route('staff.tickets.status', $t->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="RESOLVED">
                        <button type="submit" class="btn btn-sm btn-success rounded-2 py-0 px-2 fw-bold" style="font-size:0.75rem;">
                            <i class="bi bi-check-circle-fill me-1"></i>Khắc phục xong <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted style-08">
                <i class="bi bi-check-circle d-block fs-3 mb-1"></i>Không có ticket đang xử lý.
            </div>
        @endforelse
    </div>

    {{-- ── CỘT 3: ĐÃ KHẮC PHỤC (RESOLVED / CLOSED) ── --}}
    <div class="kanban-column" style="background:#f0fdf4;">
        <div class="kanban-column-header" style="border-bottom-color:#bbf7d0;">
            <h3 class="text-success-emphasis"><i class="bi bi-check-circle-fill text-success me-2"></i>Đã khắc phục / Đóng</h3>
            <span class="badge bg-success rounded-pill px-2.5 py-1">{{ $doneTickets->count() }}</span>
        </div>

        @forelse ($doneTickets as $t)
            <div class="kanban-card status-RESOLVED" style="border-left-color:#22c55e;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-monospace text-muted fw-bold" style="font-size:0.75rem;">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge badge-status badge-{{ $t->status }}">{{ $t->status }}</span>
                </div>

                <h4 class="h6 fw-bold mb-1" style="font-size:0.9rem;">
                    <a href="{{ route('staff.tickets.show', $t->id) }}" class="text-dark text-decoration-none">{{ $t->title }}</a>
                </h4>

                <div class="text-muted mb-2" style="font-size:0.75rem;">
                    <i class="bi bi-person me-1"></i>{{ $t->requester->name }} · {{ $t->resolved_at?->format('H:i d/m') }}
                </div>

                <div class="pt-2 border-top text-end">
                    <a href="{{ route('staff.tickets.show', $t->id) }}" class="btn btn-sm btn-outline-secondary rounded-2 py-0 px-2" style="font-size:0.75rem;">
                        <i class="bi bi-eye me-1"></i> Xem chi tiết
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted style-08">
                <i class="bi bi-clipboard-check d-block fs-3 mb-1"></i>Chưa có ticket nào hoàn thành.
            </div>
        @endforelse
    </div>

</div>

@endsection
