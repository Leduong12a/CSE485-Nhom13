{{-- partial: stepper.blade.php --}}
{{-- Stepper trạng thái 4 bước --}}

@php
$steps = [
    ['key' => 'OPEN',        'label' => 'Mới gửi',      'icon' => 'bi-circle-fill',       'color' => '#0ea5e9'],
    ['key' => 'IN_PROGRESS', 'label' => 'Đang xử lý',   'icon' => 'bi-arrow-repeat',      'color' => '#f59e0b'],
    ['key' => 'RESOLVED',    'label' => 'Đã khắc phục', 'icon' => 'bi-check-circle-fill', 'color' => '#22c55e'],
    ['key' => 'CLOSED',      'label' => 'Đóng phiếu',   'icon' => 'bi-lock-fill',         'color' => '#64748b'],
];

$statusOrder = ['OPEN' => 0, 'IN_PROGRESS' => 1, 'RESOLVED' => 2, 'CLOSED' => 3, 'REOPENED' => 1];
$currentIdx  = $statusOrder[$ticket->status] ?? 0;
@endphp

<div class="detail-card mb-4">
    <div class="detail-card-header">
        <span><i class="bi bi-bar-chart-steps me-2 text-primary"></i>Tiến độ xử lý</span>
        <span class="badge badge-status badge-{{ $ticket->status }}">
            @switch($ticket->status)
                @case('OPEN')        <i class="bi bi-record-circle-fill text-info me-1"></i> Mới gửi       @break
                @case('IN_PROGRESS') <i class="bi bi-clock-history text-warning me-1"></i> Đang xử lý    @break
                @case('RESOLVED')    <i class="bi bi-check-circle-fill text-success me-1"></i> Đã khắc phục  @break
                @case('CLOSED')      <i class="bi bi-lock-fill text-secondary me-1"></i> Đã đóng       @break
                @case('REOPENED')    <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Mở lại        @break
            @endswitch
        </span>
    </div>
    <div class="detail-card-body">

        {{-- Reopened Banner --}}
        @if ($ticket->status === 'REOPENED')
            <div class="d-flex align-items-center gap-2 p-2 rounded-3 mb-3"
                 style="background:#fff0f3; border: 1px solid #fecdd3; font-size:0.82rem; color:#be123c;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Phiếu này đã được <strong>mở lại</strong>. Đang chờ kỹ thuật viên tiếp tục xử lý.</span>
            </div>
        @endif

        {{-- Stepper UI --}}
        <div class="d-flex align-items-center justify-content-between position-relative" style="padding: 0 8px;">

            {{-- Progress line behind steps --}}
            <div style="position:absolute; top:20px; left:36px; right:36px; height:3px; background:#f1f5f9; z-index:0;"></div>
            <div style="position:absolute; top:20px; left:36px; height:3px; background:linear-gradient(90deg,#0d6efd,#22c55e); z-index:0;
                        width: calc({{ $currentIdx }} * (100% - 72px) / 3);">
            </div>

            @foreach ($steps as $i => $step)
            @php
                $isDone   = $i < $currentIdx;
                $isCurrent = $i === $currentIdx;
            @endphp
            <div class="d-flex flex-column align-items-center" style="position:relative; z-index:1; flex:1;">
                <div style="
                    width: 40px; height: 40px;
                    border-radius: 50%;
                    display: flex; align-items: center; justify-content: center;
                    font-size: 1rem;
                    transition: all 0.3s;
                    background: {{ $isDone ? $step['color'] : ($isCurrent ? $step['color'] : '#f1f5f9') }};
                    color: {{ ($isDone || $isCurrent) ? 'white' : '#94a3b8' }};
                    box-shadow: {{ $isCurrent ? '0 0 0 4px ' . $step['color'] . '33' : 'none' }};
                    border: {{ $isCurrent ? '2.5px solid ' . $step['color'] : 'none' }};
                ">
                    @if ($isDone)
                        <i class="bi bi-check-lg fw-bold"></i>
                    @else
                        <i class="{{ $step['icon'] }}"></i>
                    @endif
                </div>
                <span style="
                    font-size: 0.72rem;
                    font-weight: {{ $isCurrent ? '700' : '500' }};
                    color: {{ $isCurrent ? $step['color'] : ($isDone ? '#374151' : '#94a3b8') }};
                    margin-top: 6px;
                    text-align: center;
                    line-height: 1.3;
                    max-width: 70px;
                ">{{ $step['label'] }}</span>
            </div>
            @endforeach

        </div>

        {{-- Timestamps --}}
        <div class="d-flex justify-content-between mt-3 px-1" style="font-size:0.7rem; color:#94a3b8;">
            <span>{{ $ticket->created_at->format('d/m H:i') }}</span>
            @if ($ticket->resolved_at)
                <span>Khắc phục: {{ $ticket->resolved_at->format('d/m H:i') }}</span>
            @endif
            @if ($ticket->closed_at)
                <span>Đóng: {{ $ticket->closed_at->format('d/m H:i') }}</span>
            @endif
        </div>

        {{-- SLA Indicator --}}
        @if ($ticket->sla_deadline && ! in_array($ticket->status, ['RESOLVED', 'CLOSED']))
            @php
                $isOverdue = now()->greaterThan($ticket->sla_deadline);
                $diffLabel = $isOverdue
                    ? 'Quá hạn ' . $ticket->sla_deadline->diffForHumans()
                    : 'Còn lại: ' . now()->diffForHumans($ticket->sla_deadline, true);
            @endphp
            <div class="mt-3 d-flex align-items-center gap-2 p-2 rounded-3"
                 style="background: {{ $isOverdue ? '#fff1f2' : '#f0fdf4' }}; border: 1px solid {{ $isOverdue ? '#fecdd3' : '#bbf7d0' }}; font-size:0.78rem; color: {{ $isOverdue ? '#be123c' : '#166534' }};">
                <i class="bi {{ $isOverdue ? 'bi-alarm-fill' : 'bi-clock-history' }}"></i>
                <span><strong>SLA:</strong> {{ $diffLabel }}</span>
                <span class="ms-auto" style="opacity:0.7;">Hạn: {{ $ticket->sla_deadline->format('d/m/Y H:i') }}</span>
            </div>
        @endif
    </div>
</div>
