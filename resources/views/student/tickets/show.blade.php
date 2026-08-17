@extends('student.layouts.app')

@section('title', 'Ticket #' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT))
@section('meta_description', 'Chi tiết phiếu hỗ trợ kỹ thuật: ' . $ticket->title)

@push('styles')
<style>
    .show-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 992px) {
        .show-grid { grid-template-columns: 1fr; }
    }

    .detail-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }

    .detail-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
    }

    .detail-card-body { padding: 1.25rem; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1>
            <span style="font-size:1rem; color:#94a3b8; font-weight:500; font-family:'Courier New',monospace;">
                #{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
            </span>
            <span style="font-size:1.2rem; margin-left: 8px;">{{ Str::limit($ticket->title, 60) }}</span>
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('student.tickets.index') }}" class="text-decoration-none">Sự cố của tôi</a></li>
                <li class="breadcrumb-item active">Chi tiết</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('student.tickets.index') }}" class="btn btn-outline-secondary" style="border-radius:9px; font-size:0.85rem;">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>
</div>

<div class="show-grid">

    {{-- ── CỘT TRÁI ── --}}
    <div>
        {{-- Stepper tiến độ --}}
        @include('student.tickets.partials.stepper')

        {{-- Thông tin sự cố + ảnh minh chứng --}}
        @include('student.tickets.partials.ticket-info')

        {{-- Card đánh giá 5 sao (hiện khi RESOLVED hoặc CLOSED và chưa survey) --}}
        @if (in_array($ticket->status, ['RESOLVED', 'CLOSED']) && ! $ticket->satisfactionSurvey)
            @include('student.tickets.partials.survey-card')
        @endif

        {{-- Nút mở lại sự cố (chỉ cho phép mở lại trong vòng 2 giờ kể từ khi đóng/khắc phục) --}}
        @if (in_array($ticket->status, ['RESOLVED', 'CLOSED']))
            @php
                $closedTime = $ticket->closed_at ?? $ticket->resolved_at ?? $ticket->updated_at;
                $canReopen  = $closedTime ? ($closedTime->diffInMinutes(now()) <= 120) : false;
            @endphp

            @if ($canReopen)
                @include('student.tickets.partials.reopen-modal')
            @endif
        @endif
    </div>

    {{-- ── CỘT PHẢI: KHUNG CHAT ── --}}
    <div>
        @include('student.tickets.partials.chat-stream')
    </div>

</div>

@endsection
